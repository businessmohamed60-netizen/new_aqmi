<?php
namespace App\Services;

use App\Helpers\Database;
use App\Models\Assessment;
use App\Models\ConsolidatedReport;
use App\Models\EvaluationModel;
use App\Models\ScoreLevel;

/**
 * Moteur de consolidation intelligent des rapports AQMI.
 *
 * Calcule un score consolidé à partir de plusieurs évaluations
 * terminées, en proposant différentes méthodes de pondération :
 *   - weighted_average : moyenne pondérée par modèle
 *   - domain_fusion    : fusion des scores par domaine commun
 *   - best_of          : meilleur score par domaine
 *   - latest_only      : dernière évaluation par modèle
 *   - maturity_gap     : indice d'écart de maturité
 *   - consensus        : score médian (robuste aux outliers)
 *
 * Chaque méthode produit :
 *   - un score global consolidé (0-100)
 *   - un niveau de maturité
 *   - un découpage par domaine (domain_breakdown)
 *   - des méta-données (tendances, écarts, contributions)
 */
class ConsolidationService
{
    private ScoringService $scoring;

    /** Méthodes dont le score dépend réellement des poids d'évaluation. */
    public const WEIGHT_AWARE_METHODS = ['weighted_average', 'latest_only', 'domain_fusion', 'maturity_gap'];

    public static function usesAssessmentWeights(string $method): bool
    {
        return in_array($method, self::WEIGHT_AWARE_METHODS, true);
    }

    /**
     * Poids d'une évaluation : 1.0 par défaut, jamais négatif
     * (règle unique pour toutes les méthodes ; 0 = évaluation exclue).
     */
    private function weightFor(array $weights, $aid): float
    {
        $w = (float)($weights[$aid] ?? 1.0);
        return $w < 0 ? 0.0 : $w;
    }

    public function __construct()
    {
        $this->scoring = new ScoringService();
    }

    /**
     * Retourne toutes les méthodes de consolidation disponibles.
     */
    public function getAvailableMethods(): array
    {
        return Database::fetchAll(
            "SELECT * FROM consolidation_methods WHERE is_active = 1 ORDER BY sort_order"
        );
    }

    /**
     * Récupère les analyses détaillées (par domaine) pour une liste
     * d'évaluations. Retourne un tableau indexé par assessment_id.
     */
    public function getAssessmentAnalyses(array $assessmentIds): array
    {
        $analyses = [];
        foreach ($assessmentIds as $aid) {
            $aid = (int)$aid;
            try {
                $analysis = $this->scoring->analyzeAssessment($aid);
                $assessment = Assessment::find($aid);
                $analyses[$aid] = [
                    'assessment'    => $assessment,
                    'global_score'  => $analysis['global_score'],
                    'maturity_level'=> $analysis['maturity_level'] ?? null,
                    'domain_scores' => $analysis['domain_scores'],
                    'model_id'      => $assessment['model_id'] ?? null,
                ];
            } catch (\Throwable $e) {
                $assessment = Assessment::find($aid);
                $analyses[$aid] = [
                    'assessment'    => $assessment,
                    'global_score'  => (float)($assessment['total_score'] ?? 0),
                    'maturity_level'=> null,
                    'domain_scores' => [],
                    'model_id'      => $assessment['model_id'] ?? null,
                ];
            }
        }
        return $analyses;
    }

    /**
     * Calcule le score consolidé selon la méthode choisie.
     *
     * @param array $assessmentIds  IDs des évaluations à consolider
     * @param string $method        Code de la méthode
     * @param array $weights        Poids par assessment_id [id => weight]
     * @return array {
     *   global_score: float,
     *   maturity_level: ?array,
     *   domain_breakdown: array,
     *   items_detail: array,
     *   method: string,
     *   params: array
     * }
     */
    public function calculate(array $assessmentIds, string $method = 'weighted_average', array $weights = []): array
    {
        $analyses = $this->getAssessmentAnalyses($assessmentIds);
        if (empty($analyses)) {
            return $this->emptyResult($method);
        }

        switch ($method) {
            case 'domain_fusion':
                return $this->calcDomainFusion($analyses, $weights);
            case 'best_of':
                return $this->calcBestOf($analyses, $weights);
            case 'latest_only':
                return $this->calcLatestOnly($analyses, $weights);
            case 'maturity_gap':
                return $this->calcMaturityGap($analyses, $weights);
            case 'consensus':
                return $this->calcConsensus($analyses, $weights);
            case 'weighted_average':
            default:
                return $this->calcWeightedAverage($analyses, $weights);
        }
    }

    // ─────────────────────────────────────────────
    //  Méthodes de calcul
    // ─────────────────────────────────────────────

    /**
     * Moyenne pondérée : chaque évaluation contribue proportionnellement
     * à son poids. C'est la méthode par défaut.
     */
    private function calcWeightedAverage(array $analyses, array $weights): array
    {
        $totalWeight = 0;
        $weightedSum = 0;
        $itemsDetail = [];

        foreach ($analyses as $aid => $data) {
            $w = $this->weightFor($weights, $aid);
            $score = (float)$data['global_score'];
            $totalWeight += $w;
            $weightedSum += $score * $w;

            $itemsDetail[] = $this->buildItemDetail($aid, $data, $w, $score);
        }

        $globalScore = $totalWeight > 0 ? round($weightedSum / $totalWeight, 2) : 0;
        $domainBreakdown = $this->buildDomainBreakdown($analyses, $weights);

        return $this->assembleResult($globalScore, $domainBreakdown, $itemsDetail, 'weighted_average', $weights);
    }

    /**
     * Fusion par domaine : les domaines communs à plusieurs modèles sont
     * moyennés (pondérés), puis le score global est la moyenne pondérée
     * des domaines fusionnés.
     */
    private function calcDomainFusion(array $analyses, array $weights): array
    {
        $domainAgg = [];

        foreach ($analyses as $aid => $data) {
            $w = $this->weightFor($weights, $aid);
            foreach ($data['domain_scores'] as $ds) {
                if ((int)($ds['question_count'] ?? 1) < 1) continue; // domaine non répondu : ignoré (comme ScoringService)
                $did = (int)$ds['domain_id'];
                if (!isset($domainAgg[$did])) {
                    $domainAgg[$did] = [
                        'domain_id'   => $did,
                        'domain_label'=> $ds['domain_label'] ?? $ds['domain_name_fr'] ?? $ds['domain_name'],
                        'icon'        => $ds['icon'] ?? null,
                        'weight'      => (float)($ds['weight'] ?? 1),
                        'scores'      => [],
                        'weights'     => [],
                    ];
                }
                $domainAgg[$did]['scores'][] = (float)$ds['percent_score'];
                $domainAgg[$did]['weights'][] = $w;
            }
        }

        $domainBreakdown = [];
        $totalDomainWeight = 0;
        $weightedDomainSum = 0;
        $itemsDetail = [];

        foreach ($analyses as $aid => $data) {
            $w = $this->weightFor($weights, $aid);
            $itemsDetail[] = $this->buildItemDetail($aid, $data, $w, (float)$data['global_score']);
        }

        foreach ($domainAgg as $did => &$agg) {
            $wSum = array_sum($agg['weights']);
            $wScored = 0;
            foreach ($agg['scores'] as $i => $s) {
                $wScored += $s * $agg['weights'][$i];
            }
            $fused = $wSum > 0 ? round($wScored / $wSum, 2) : 0;
            $agg['fused_score'] = $fused;
            $agg['contribution'] = count($agg['scores']);

            $domainWeight = (float)($agg['weight'] ?? 1);
            $totalDomainWeight += $domainWeight;
            $weightedDomainSum += $fused * $domainWeight;

            $domainBreakdown[] = [
                'domain_id'    => $did,
                'domain_label' => $agg['domain_label'],
                'icon'         => $agg['icon'],
                'fused_score'  => $fused,
                'contributions'=> $agg['contribution'],
                'weight'       => $domainWeight,
            ];
        }
        unset($agg);

        $globalScore = $totalDomainWeight > 0 ? round($weightedDomainSum / $totalDomainWeight, 2) : 0;

        return $this->assembleResult($globalScore, $domainBreakdown, $itemsDetail, 'domain_fusion', $weights);
    }

    /**
     * Meilleure performance : retient le score le plus élevé par domaine.
     * Les poids d'évaluation ne sont PAS utilisés (seuls les poids de domaine comptent).
     */
    private function calcBestOf(array $analyses, array $weights): array
    {
        $domainBest = [];
        $itemsDetail = [];

        foreach ($analyses as $aid => $data) {
            $w = $this->weightFor($weights, $aid);
            $itemsDetail[] = $this->buildItemDetail($aid, $data, $w, (float)$data['global_score']);

            foreach ($data['domain_scores'] as $ds) {
                if ((int)($ds['question_count'] ?? 1) < 1) continue; // domaine non répondu : ignoré (comme ScoringService)
                $did = (int)$ds['domain_id'];
                $score = (float)$ds['percent_score'];
                if (!isset($domainBest[$did]) || $score > $domainBest[$did]['best_score']) {
                    $domainBest[$did] = [
                        'domain_id'   => $did,
                        'domain_label'=> $ds['domain_label'] ?? $ds['domain_name_fr'] ?? $ds['domain_name'],
                        'icon'        => $ds['icon'] ?? null,
                        'best_score'  => $score,
                        'weight'      => (float)($ds['weight'] ?? 1),
                        'from_assessment' => $aid,
                    ];
                }
            }
        }

        $domainBreakdown = [];
        $totalW = 0;
        $weightedSum = 0;
        foreach ($domainBest as $did => $best) {
            $domainBreakdown[] = [
                'domain_id'    => $did,
                'domain_label' => $best['domain_label'],
                'icon'         => $best['icon'],
                'fused_score'  => $best['best_score'],
                'contributions'=> 1,
                'weight'       => $best['weight'],
            ];
            $totalW += $best['weight'];
            $weightedSum += $best['best_score'] * $best['weight'];
        }

        $globalScore = $totalW > 0 ? round($weightedSum / $totalW, 2) : 0;

        return $this->assembleResult($globalScore, $domainBreakdown, $itemsDetail, 'best_of', $weights);
    }

    /**
     * Dernière évaluation : ne considère que l'évaluation la plus récente
     * par modèle d'évaluation.
     */
    private function calcLatestOnly(array $analyses, array $weights): array
    {
        $latestPerModel = [];

        foreach ($analyses as $aid => $data) {
            $modelId = (int)($data['model_id'] ?? 0);
            $completedAt = $data['assessment']['completed_at'] ?? $data['assessment']['created_at'] ?? '';
            if (!isset($latestPerModel[$modelId]) || strtotime($completedAt) > strtotime($latestPerModel[$modelId]['completed_at'])) {
                $latestPerModel[$modelId] = [
                    'aid'         => $aid,
                    'completed_at'=> $completedAt,
                    'data'        => $data,
                ];
            }
        }

        $filteredAnalyses = [];
        foreach ($latestPerModel as $m) {
            $filteredAnalyses[$m['aid']] = $m['data'];
        }

        $result = $this->calcWeightedAverage($filteredAnalyses, $weights);
        $result['method'] = 'latest_only';
        return $result;
    }

    /**
     * Indice d'écart de maturité : calcule un score composite qui récompense
     * la cohérence entre modèles et pénalise les écarts importants.
     *
     * Score = moyenne pondérée - (pénalité basée sur l'écart-type des scores)
     */
    private function calcMaturityGap(array $analyses, array $weights): array
    {
        $scores = [];
        $totalWeight = 0;
        $weightedSum = 0;
        $itemsDetail = [];

        foreach ($analyses as $aid => $data) {
            $w = $this->weightFor($weights, $aid);
            $score = (float)$data['global_score'];
            $scores[] = $score;
            $totalWeight += $w;
            $weightedSum += $score * $w;
            $itemsDetail[] = $this->buildItemDetail($aid, $data, $w, $score);
        }

        $weightedAvg = $totalWeight > 0 ? $weightedSum / $totalWeight : 0;

        // Écart-type (non pondéré — mesure de dispersion)
        $n = count($scores);
        $variance = 0;
        if ($n > 1) {
            $mean = array_sum($scores) / $n;
            foreach ($scores as $s) {
                $variance += ($s - $mean) ** 2;
            }
            $variance /= $n;
        }
        $stdDev = sqrt($variance);

        // Pénalité : 50% de l'écart-type (ajustable)
        $penalty = round($stdDev * 0.5, 2);
        $globalScore = max(0, round($weightedAvg - $penalty, 2));

        $domainBreakdown = $this->buildDomainBreakdown($analyses, $weights);

        $result = $this->assembleResult($globalScore, $domainBreakdown, $itemsDetail, 'maturity_gap', $weights);
        $result['meta'] = [
            'weighted_average' => round($weightedAvg, 2),
            'std_dev'         => round($stdDev, 2),
            'penalty'         => $penalty,
            'consistency'     => $stdDev > 0 ? round(max(0, 100 - ($stdDev * 2)), 1) : 100,
        ];

        return $result;
    }

    /**
     * Score de consensus : utilise la médiane des scores pour réduire
     * l'influence des valeurs extrêmes. Les poids d'évaluation ne sont PAS utilisés.
     */
    private function calcConsensus(array $analyses, array $weights): array
    {
        $scores = [];
        $itemsDetail = [];

        foreach ($analyses as $aid => $data) {
            $w = $this->weightFor($weights, $aid);
            $scores[] = (float)$data['global_score'];
            $itemsDetail[] = $this->buildItemDetail($aid, $data, $w, (float)$data['global_score']);
        }

        sort($scores);
        $n = count($scores);
        if ($n % 2 === 0) {
            $median = ($scores[$n / 2 - 1] + $scores[$n / 2]) / 2;
        } else {
            $median = $scores[(int)floor($n / 2)];
        }
        $globalScore = round($median, 2);

        $domainBreakdown = $this->buildDomainBreakdown($analyses, $weights);

        return $this->assembleResult($globalScore, $domainBreakdown, $itemsDetail, 'consensus', $weights);
    }

    // ─────────────────────────────────────────────
    //  Helpers
    // ─────────────────────────────────────────────

    private function buildItemDetail(int $aid, array $data, float $weight, float $score): array
    {
        $assessment = $data['assessment'];
        $model = $assessment['model_id'] ? EvaluationModel::find((int)$assessment['model_id']) : null;
        $modelName = $model ? ($model['name_fr'] ?: $model['name']) : 'Modèle';

        return [
            'assessment_id'  => $aid,
            'model_id'       => $assessment['model_id'] ?? null,
            'model_name'     => $modelName,
            'score'          => $score,
            'weight'         => $weight,
            'maturity_level' => $data['maturity_level']['name'] ?? null,
            'completed_at'   => $assessment['completed_at'] ?? null,
            'domain_scores'  => $data['domain_scores'],
        ];
    }

    private function buildDomainBreakdown(array $analyses, array $weights): array
    {
        $domainAgg = [];
        foreach ($analyses as $aid => $data) {
            $w = $this->weightFor($weights, $aid);
            foreach ($data['domain_scores'] as $ds) {
                if ((int)($ds['question_count'] ?? 1) < 1) continue; // domaine non répondu : ignoré (comme ScoringService)
                $did = (int)$ds['domain_id'];
                if (!isset($domainAgg[$did])) {
                    $domainAgg[$did] = [
                        'domain_id'   => $did,
                        'domain_label'=> $ds['domain_label'] ?? $ds['domain_name_fr'] ?? $ds['domain_name'],
                        'icon'        => $ds['icon'] ?? null,
                        'weight'      => (float)($ds['weight'] ?? 1),
                        'scores'      => [],
                        'weights'     => [],
                    ];
                }
                $domainAgg[$did]['scores'][] = (float)$ds['percent_score'];
                $domainAgg[$did]['weights'][] = $w;
            }
        }

        $breakdown = [];
        foreach ($domainAgg as $did => $agg) {
            $wSum = array_sum($agg['weights']);
            $wScored = 0;
            foreach ($agg['scores'] as $i => $s) {
                $wScored += $s * $agg['weights'][$i];
            }
            $fused = $wSum > 0 ? round($wScored / $wSum, 2) : 0;
            $breakdown[] = [
                'domain_id'    => $did,
                'domain_label' => $agg['domain_label'],
                'icon'         => $agg['icon'],
                'fused_score'  => $fused,
                'contributions'=> count($agg['scores']),
                'weight'       => $agg['weight'],
            ];
        }
        return $breakdown;
    }

    private function assembleResult(float $globalScore, array $domainBreakdown, array $itemsDetail, string $method, array $weights): array
    {
        $level = ScoreLevel::findByScore($globalScore);
        return [
            'global_score'    => $globalScore,
            'maturity_level'  => $level,
            'domain_breakdown'=> $domainBreakdown,
            'items_detail'    => $itemsDetail,
            'method'          => $method,
            'params'          => ['weights' => $weights, 'weights_applied' => self::usesAssessmentWeights($method)],
        ];
    }

    private function emptyResult(string $method): array
    {
        return [
            'global_score'    => 0,
            'maturity_level'  => null,
            'domain_breakdown'=> [],
            'items_detail'    => [],
            'method'          => $method,
            'params'          => ['weights' => []],
        ];
    }

    // ─────────────────────────────────────────────
    //  Atelier de pondération : recalcul d'un rapport existant
    // ─────────────────────────────────────────────

    /** Statuts pour lesquels le score d'un rapport peut encore être recalculé. */
    public const EDITABLE_STATUSES = ['draft', 'certification_requested', 'under_review', 'approved'];

    /** Catalogue de secours si la table consolidation_methods n'existe pas encore. */
    private const FALLBACK_METHODS = [
        ['code' => 'weighted_average', 'name_fr' => 'Moyenne pondérée',        'description_fr' => 'Chaque évaluation contribue proportionnellement à son poids.', 'icon' => 'fa-scale-balanced',      'sort_order' => 1],
        ['code' => 'domain_fusion',    'name_fr' => 'Fusion par domaine',      'description_fr' => 'Les domaines communs à plusieurs modèles sont moyennés puis regroupés (poids d\'évaluation puis poids de domaine).', 'icon' => 'fa-object-group', 'sort_order' => 2],
        ['code' => 'best_of',          'name_fr' => 'Meilleure performance',   'description_fr' => 'Retient le score le plus élevé par domaine (les poids d\'évaluation ne sont pas utilisés).', 'icon' => 'fa-trophy', 'sort_order' => 3],
        ['code' => 'latest_only',      'name_fr' => 'Dernière évaluation',     'description_fr' => 'Seule l\'évaluation la plus récente de chaque modèle est prise en compte.', 'icon' => 'fa-clock-rotate-left', 'sort_order' => 4],
        ['code' => 'maturity_gap',     'name_fr' => 'Indice d\'écart de maturité', 'description_fr' => 'Moyenne pondérée moins une pénalité liée à la dispersion entre modèles.', 'icon' => 'fa-chart-line', 'sort_order' => 5],
        ['code' => 'consensus',        'name_fr' => 'Score de consensus',      'description_fr' => 'Médiane des scores (les poids d\'évaluation ne sont pas utilisés).', 'icon' => 'fa-handshake', 'sort_order' => 6],
    ];

    /** Méthodes disponibles (base de données, ou catalogue de secours). */
    public function getMethodsCatalog(): array
    {
        try {
            $rows = $this->getAvailableMethods();
            if (!empty($rows)) {
                return $rows;
            }
        } catch (\Throwable $e) {
            // table absente : on retombe sur le catalogue interne
        }
        return self::FALLBACK_METHODS;
    }

    /** Valide un code de méthode (liste blanche issue du catalogue). */
    public function sanitizeMethod(string $method): string
    {
        $codes = array_column($this->getMethodsCatalog(), 'code');
        if (!in_array($method, $codes, true)) {
            throw new \InvalidArgumentException('Méthode de calcul inconnue.');
        }
        return $method;
    }

    /** Détecte la colonne qui relie consolidated_report_items au rapport. */
    private function itemsForeignKey(): string
    {
        static $col = null;
        if ($col !== null) {
            return $col;
        }
        $fields = array_column(Database::fetchAll("SHOW COLUMNS FROM consolidated_report_items"), 'Field');
        foreach (['consolidated_report_id', 'consolidated_id', 'report_id'] as $candidate) {
            if (in_array($candidate, $fields, true)) {
                return $col = $candidate;
            }
        }
        throw new \RuntimeException('Colonne de liaison introuvable dans consolidated_report_items.');
    }

    /** Évaluations rattachées à un rapport : [assessment_id => poids enregistré]. */
    public function getReportItemRows(int $reportId): array
    {
        $fk = $this->itemsForeignKey();
        $rows = Database::fetchAll(
            "SELECT assessment_id, weight FROM consolidated_report_items WHERE {$fk} = " . (int)$reportId
        );
        $out = [];
        foreach ($rows as $r) {
            $out[(int)$r['assessment_id']] = isset($r['weight']) ? (float)$r['weight'] : 1.0;
        }
        return $out;
    }

    private function fetchReportRow(int $reportId): ?array
    {
        $rows = Database::fetchAll(
            "SELECT id, status, calculation_method, consolidated_score FROM consolidated_reports WHERE id = "
            . (int)$reportId . " LIMIT 1"
        );
        return $rows[0] ?? null;
    }

    /**
     * Données de l'atelier de pondération pour la page de détail.
     */
    public function getWorkbenchData(int $reportId): array
    {
        $report = $this->fetchReportRow($reportId);
        if (!$report) {
            throw new \InvalidArgumentException('Rapport introuvable.');
        }

        $stored = $this->getReportItemRows($reportId);
        $analyses = $this->getAssessmentAnalyses(array_keys($stored));

        $rows = [];
        foreach ($stored as $aid => $weight) {
            $data = $analyses[$aid] ?? null;
            if (!$data) {
                continue;
            }
            $assessment = $data['assessment'] ?? [];
            $model = !empty($assessment['model_id']) ? EvaluationModel::find((int)$assessment['model_id']) : null;
            $rows[] = [
                'assessment_id' => $aid,
                'model_name'    => $model ? (($model['name_fr'] ?? '') ?: ($model['name'] ?? 'Modèle')) : 'Modèle',
                'score'         => (float)$data['global_score'],
                'weight'        => $weight,
                'completed_at'  => $assessment['completed_at'] ?? $assessment['created_at'] ?? null,
            ];
        }

        return [
            'method'   => (string)($report['calculation_method'] ?: 'weighted_average'),
            'methods'  => $this->getMethodsCatalog(),
            'rows'     => $rows,
            'editable' => in_array((string)$report['status'], self::EDITABLE_STATUSES, true),
        ];
    }

    /**
     * Recalcule un rapport existant avec une méthode et des poids donnés.
     * $save = false : simple aperçu (rien n'est écrit en base).
     *
     * @param array $weights [assessment_id => poids] (valeurs 0 à 100, 2 décimales)
     */
    public function recalculateReport(int $reportId, string $method, array $weights, bool $save): array
    {
        $report = $this->fetchReportRow($reportId);
        if (!$report) {
            throw new \InvalidArgumentException('Rapport introuvable.');
        }
        if (!in_array((string)$report['status'], self::EDITABLE_STATUSES, true)) {
            throw new \InvalidArgumentException('Ce rapport ne peut plus être recalculé (statut : ' . $report['status'] . ').');
        }

        $method = $this->sanitizeMethod($method);
        $ids = array_keys($this->getReportItemRows($reportId));
        if (empty($ids)) {
            throw new \InvalidArgumentException("Aucune évaluation n'est rattachée à ce rapport.");
        }

        // Seuls les ids réellement rattachés au rapport sont acceptés
        $clean = [];
        foreach ($ids as $aid) {
            if (array_key_exists($aid, $weights)) {
                if (!is_numeric($weights[$aid])) {
                    throw new \InvalidArgumentException('Poids invalide.');
                }
                $clean[$aid] = max(0.0, min(100.0, round((float)$weights[$aid], 2)));
            } else {
                $clean[$aid] = 1.0;
            }
        }
        if (self::usesAssessmentWeights($method) && array_sum($clean) <= 0) {
            throw new \InvalidArgumentException('Au moins un poids doit être supérieur à 0.');
        }

        $result = $this->calculate($ids, $method, $clean);
        if ($save) {
            $this->persist($reportId, $result, $clean);
        }
        return $result;
    }

    /** Version allégée d'un résultat, destinée à l'aperçu JSON. */
    public function summarizeResult(array $result): array
    {
        $level = $result['maturity_level'] ?? null;
        $domains = [];
        foreach (($result['domain_breakdown'] ?? []) as $d) {
            $domains[] = [
                'domain_label'  => (string)($d['domain_label'] ?? ''),
                'fused_score'   => (float)($d['fused_score'] ?? 0),
                'contributions' => (int)($d['contributions'] ?? 1),
            ];
        }
        return [
            'global_score'    => (float)$result['global_score'],
            'level_label'     => $level ? (($level['name_fr'] ?? '') ?: ($level['name'] ?? null)) : null,
            'method'          => (string)$result['method'],
            'weights_applied' => self::usesAssessmentWeights((string)$result['method']),
            'domain_breakdown'=> $domains,
            'meta'            => $result['meta'] ?? null,
        ];
    }

    /**
     * Persiste les résultats de consolidation sur le rapport et ses items.
     */
    public function persist(int $consolidatedId, array $result, array $assessmentWeights): void
    {
        $level = $result['maturity_level'];
        $levelName = $level ? ($level['name_fr'] ?: $level['name']) : null;

        ConsolidatedReport::updateScore($consolidatedId, $result['global_score'], $levelName);
        ConsolidatedReport::updateCalculationMeta($consolidatedId, [
            'calculation_method'  => $result['method'],
            'consolidation_params'=> json_encode(['weights' => $assessmentWeights]),
            'domain_breakdown_json'=> json_encode($result['domain_breakdown']),
        ]);

        foreach ($result['items_detail'] as $item) {
            ConsolidatedReport::updateItemWeight(
                $consolidatedId,
                (int)$item['assessment_id'],
                (float)$item['weight'],
                json_encode($item['domain_scores'] ?? [])
            );
        }
    }
}
