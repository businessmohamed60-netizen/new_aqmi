<?php
namespace App\Services;

use App\Models\Assessment;
use App\Models\Lead;
use App\Models\Report;
use App\Models\ScoreLevel;

class PdfService
{
    private ScoringService $scoringService;
    private RecommendationService $recommendationService;

    public function __construct()
    {
        $this->scoringService = new ScoringService();
        $this->recommendationService = new RecommendationService();
    }

    /**
     * Extrait le contenu intérieur d'une balise (<style>...</style> ou
     * <body>...</body>) d'un document HTML complet, pour pouvoir le
     * refusionner dans un document englobant unique sans dupliquer
     * <!DOCTYPE>/<html>/<head> et sans casser l'encodage UTF-8.
     */
    private function extractHtmlPart(string $html, string $tag): string
    {
        if (preg_match('/<' . $tag . '[^>]*>(.*)<\/' . $tag . '>/is', $html, $matches)) {
            return $matches[1];
        }
        return '';
    }

    public function generate(int $assessmentId): string
    {
        $assessment = Assessment::find($assessmentId);
        if (!$assessment) throw new \RuntimeException('Assessment not found');

        $analysis = $this->scoringService->analyzeAssessment($assessmentId);
        $recommendations = $this->recommendationService->generate($assessmentId);
        $lead = Lead::findByAssessment($assessmentId);

        $html = $this->buildHtml($assessment, $analysis, $recommendations, $lead);

        $fileName = 'rapport_AQMI_' . $assessmentId . '_' . date('Ymd_His');
        $reportsDir = BASE_PATH . '/storage/reports';
        if (!is_dir($reportsDir)) {
            mkdir($reportsDir, 0775, true);
        }

        if (class_exists('Dompdf\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4');
            $dompdf->render();
            $pdfFile = $fileName . '.pdf';
            file_put_contents($reportsDir . '/' . $pdfFile, $dompdf->output());
            return $pdfFile;
        }

        $htmlFile = $fileName . '.html';
        file_put_contents($reportsDir . '/' . $htmlFile, $html);
        return $htmlFile;
    }

    /**
     * Génère le PDF officiel "Rapport AQMI Certifié" pour une demande
     * de certification déjà approuvée par un administrateur.
     * Réutilise buildHtml() (identique au contenu du rapport existant)
     * et l'entoure d'une page de couverture + une page de certification
     * (observations, plan d'action, signature, QR code de vérification).
     */
    public function generateCertificate(int $reportId): string
    {
        $report = Report::find($reportId);
        if (!$report) throw new \RuntimeException('Report not found');

        $assessment = Assessment::find($report['assessment_id']);
        if (!$assessment) throw new \RuntimeException('Assessment not found');

        $analysis = $this->scoringService->analyzeAssessment($assessment['id']);
        $recommendations = $this->recommendationService->generate($assessment['id']);
        $lead = Lead::findByAssessment($assessment['id']);

        $reportNumber = $report['report_number'] ?: Report::assignReportNumber($reportId);
        $qrDataUri = $this->generateQrCode($reportId, $reportNumber);

        $coverHtml = $this->buildCoverPageHtml($assessment, $analysis, $lead, $report, $reportNumber);
        $rawBodyHtml = $this->buildHtml($assessment, $analysis, $recommendations, $lead, false);
        $certPageHtml = $this->buildCertificationPageHtml($report, $reportNumber, $qrDataUri);

        // buildHtml() renvoie un DOCUMENT COMPLET (<!DOCTYPE>, <html>, <head><style>,
        // <body>). On ne peut pas le coller tel quel entre deux fragments — ça
        // produirait un <html> imbriqué au milieu du document, et le cover/cert
        // se retrouveraient hors de tout <body>, ce qui casse l'encodage UTF-8
        // (c'était le bug des accents illisibles dans le PDF précédent).
        // On extrait donc son <style> et son <body>, et on fusionne le tout dans
        // un unique document bien formé avec un seul <meta charset="UTF-8">.
        $bodyStyle = $this->extractHtmlPart($rawBodyHtml, 'style');
        $bodyInner = $this->extractHtmlPart($rawBodyHtml, 'body');

        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8">'
            . '<style>' . $bodyStyle . '</style>'
            . '</head><body>'
            . $coverHtml
            . '<div style="page-break-before:always;"></div>'
            . $bodyInner
            . '<div style="page-break-before:always;"></div>'
            . $certPageHtml
            . '</body></html>';

        $fileName = 'certificat_AQMI_' . $reportNumber . '_' . date('Ymd_His');
        $reportsDir = BASE_PATH . '/storage/reports';
        if (!is_dir($reportsDir)) {
            mkdir($reportsDir, 0775, true);
        }

        if (class_exists('Dompdf\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4');
            $dompdf->render();
            $pdfFile = $fileName . '.pdf';
            file_put_contents($reportsDir . '/' . $pdfFile, $dompdf->output());
            return $pdfFile;
        }

        $htmlFile = $fileName . '.html';
        file_put_contents($reportsDir . '/' . $htmlFile, $html);
        return $htmlFile;
    }

    /**
     * Génère un QR code pointant vers la page publique de vérification
     * /verify/{report_number}, l'enregistre sur disque (qr_code_path) et
     * retourne une data-URI base64 prête à être intégrée dans le PDF.
     * Retourne null si endroid/qr-code n'est pas encore installé
     * (composer update requis) — le PDF se génère quand même, sans QR.
     */
    private function generateQrCode(int $reportId, string $reportNumber): ?string
    {
        if (!class_exists('Endroid\QrCode\Builder\Builder')) {
            return null;
        }

        $appUrl = rtrim($_ENV['APP_URL'] ?? '', '/');
        if ($appUrl === '') {
            $appUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
                . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
        }
        $verifyUrl = $appUrl . '/verify/' . $reportNumber;

        try {
            $result = \Endroid\QrCode\Builder\Builder::create()
                ->writer(new \Endroid\QrCode\Writer\PngWriter())
                ->data($verifyUrl)
                ->size(240)
                ->margin(8)
                ->build();

            $qrDir = BASE_PATH . '/storage/qrcodes';
            if (!is_dir($qrDir)) {
                mkdir($qrDir, 0775, true);
            }
            $qrRelativePath = 'qrcodes/' . $reportNumber . '.png';
            $result->saveToFile(BASE_PATH . '/storage/' . $qrRelativePath);

            Report::setQrCodePath($reportId, $qrRelativePath);

            return 'data:image/png;base64,' . base64_encode($result->getString());
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Page de couverture du certificat officiel.
     */
    private function buildCoverPageHtml(array $assessment, array $analysis, ?array $lead, array $report, string $reportNumber): string
    {
        $companyName = $lead['company'] ?? 'Entreprise';
        $sector = $lead['sector'] ?? '—';
        $country = $lead['country'] ?? '—';
        $globalScore = $analysis['global_score'];
        $level = $analysis['maturity_level'];
        $levelName = $report['aqmi_level_assigned'] ?: ($level['name_fr'] ?? $level['name'] ?? 'Non défini');
        $levelColor = $level['color'] ?? '#7367f0';
        $certDate = $report['certified_at'] ? date('d/m/Y', strtotime($report['certified_at'])) : date('d/m/Y');
        $domainScores = $analysis['domain_scores'];

        $navy = '#0b1f4d';
        $gold = '#b8860b';

        $gaugeSvg = $this->buildGaugeSvg($globalScore, $levelColor);
        $radarSvg = $this->buildRadarSvg($domainScores, $levelColor);
        $legendHtml = $this->buildLevelLegendHtml();
        $sealSvg = $this->buildSealSvg($levelColor);

        $domainRows = '';
        foreach ($domainScores as $d) {
            $pct = round($d['percent_score']);
            $c = $pct >= 70 ? '#16a34a' : ($pct >= 50 ? '#d97706' : '#dc2626');
            $domainRows .= '
                <tr>
                    <td style="padding:6px 0;font-size:9pt;color:#374151;width:52%;border-bottom:1px solid #f1f2f6;">' . htmlspecialchars($d['domain_name_fr'] ?: $d['domain_name']) . '</td>
                    <td style="padding:6px 0;width:38%;border-bottom:1px solid #f1f2f6;">
                        <div style="background:#eef0f4;border-radius:5px;height:7px;width:100%;">
                            <div style="background:' . $c . ';border-radius:5px;height:7px;width:' . $pct . '%;"></div>
                        </div>
                    </td>
                    <td style="padding:6px 0 6px 10px;font-size:9pt;font-weight:bold;color:' . $c . ';text-align:right;width:10%;border-bottom:1px solid #f1f2f6;">' . $pct . '%</td>
                </tr>';
        }

        $strengths = '';
        foreach (($analysis['strengths'] ?? []) as $s) {
            $strengths .= '<div style="font-size:8.5pt;color:#374151;margin-bottom:7px;padding-left:14px;position:relative;">
                <span style="position:absolute;left:0;color:#16a34a;font-weight:bold;">•</span>' . htmlspecialchars($s['domain_name_fr'] ?: $s['domain_name']) . '
            </div>';
        }
        $weaknesses = '';
        foreach (($analysis['weaknesses'] ?? []) as $w) {
            $weaknesses .= '<div style="font-size:8.5pt;color:#374151;margin-bottom:7px;padding-left:14px;position:relative;">
                <span style="position:absolute;left:0;color:#d97706;font-weight:bold;">•</span>' . htmlspecialchars($w['domain_name_fr'] ?: $w['domain_name']) . '
            </div>';
        }

        return '
        <div style="font-family:sans-serif;">
            <!-- Bandeau de marque -->
            <table style="width:100%;background:linear-gradient(135deg,' . $navy . ' 0%,#173b8c 100%);border-collapse:collapse;">
                <tr>
                    <td style="padding:22px 40px;width:55%;">
                        <div style="font-size:22pt;font-weight:800;color:#ffffff;letter-spacing:1.5px;">AQMI</div>
                        <div style="font-size:7pt;color:#c7d2fe;letter-spacing:2.5px;margin-top:2px;">AUTOMOTIVE QUALITY MATURITY INDEX</div>
                    </td>
                    <td style="padding:22px 40px;width:45%;text-align:right;">
                        <div style="font-size:12pt;font-weight:700;color:#ffffff;">NOVAQYS</div>
                        <div style="font-size:6.5pt;color:#c7d2fe;">Plateforme intégrée de performance industrielle &amp; qualité</div>
                    </td>
                </tr>
            </table>
            <div style="height:3px;background:linear-gradient(90deg,' . $gold . ',' . $levelColor . ');"></div>

            <div style="padding:26px 40px 0;">
                <table style="width:100%;">
                    <tr>
                        <td style="vertical-align:top;width:72%;">
                            <div style="font-size:8pt;color:#9ca3af;letter-spacing:2px;">CERTIFICAT N° ' . htmlspecialchars($reportNumber) . '</div>
                            <div style="font-size:24pt;font-weight:800;color:' . $navy . ';margin-top:2px;">Rapport de Certification AQMI</div>
                            <div style="font-size:10.5pt;color:#4b5563;margin-top:2px;">Évaluation officielle de maturité qualité industrielle</div>
                        </td>
                        <td style="vertical-align:top;width:28%;text-align:center;">
                            ' . $sealSvg . '
                        </td>
                    </tr>
                </table>

                <table style="width:100%;margin-top:22px;">
                    <tr>
                        <td style="width:48%;vertical-align:top;background:#f8f9fc;border:1px solid #e9ebf2;border-radius:10px;padding:16px 18px;">
                            <div style="font-size:7.5pt;color:' . $navy . ';font-weight:700;letter-spacing:1.5px;margin-bottom:10px;">DOSSIER ENTREPRISE</div>
                            <table style="font-size:9pt;color:#374151;width:100%;">
                                <tr><td style="color:#9ca3af;padding:4px 0;">Entreprise</td><td style="font-weight:bold;text-align:right;">' . htmlspecialchars($companyName) . '</td></tr>
                                <tr><td style="color:#9ca3af;padding:4px 0;">Secteur</td><td style="font-weight:bold;text-align:right;">' . htmlspecialchars($sector) . '</td></tr>
                                <tr><td style="color:#9ca3af;padding:4px 0;">Pays</td><td style="font-weight:bold;text-align:right;">' . htmlspecialchars($country) . '</td></tr>
                                <tr><td style="color:#9ca3af;padding:4px 0;">Date de certification</td><td style="font-weight:bold;text-align:right;">' . $certDate . '</td></tr>
                            </table>
                        </td>
                        <td style="width:4%;"></td>
                        <td style="width:48%;vertical-align:top;text-align:center;border:1px solid #e9ebf2;border-radius:10px;padding:14px 10px;">
                            <div style="font-size:7.5pt;color:' . $navy . ';font-weight:700;letter-spacing:1.5px;margin-bottom:6px;">SCORE AQMI GLOBAL</div>
                            ' . $gaugeSvg . '
                            <div style="display:inline-block;background:' . $levelColor . '18;border:1px solid ' . $levelColor . ';border-radius:999px;padding:4px 16px;margin-top:-4px;">
                                <span style="font-size:10pt;font-weight:800;color:' . $levelColor . ';">Niveau ' . htmlspecialchars($levelName) . '</span>
                            </div>
                        </td>
                    </tr>
                </table>

                <table style="width:100%;margin-top:20px;">
                    <tr>
                        <td style="width:40%;vertical-align:top;">
                            <div style="font-size:7.5pt;color:' . $navy . ';font-weight:700;letter-spacing:1.5px;margin-bottom:10px;">RÉSULTATS PAR DOMAINE</div>
                            <table style="width:100%;">' . $domainRows . '</table>
                        </td>
                        <td style="width:2%;"></td>
                        <td style="width:58%;vertical-align:top;text-align:center;">
                            <div style="font-size:7.5pt;color:' . $navy . ';font-weight:700;letter-spacing:1.5px;margin-bottom:2px;">PROFIL DE MATURITÉ</div>
                            ' . $radarSvg . '
                        </td>
                    </tr>
                </table>

                ' . $legendHtml . '

                <table style="width:100%;margin-top:8px;">
                    <tr>
                        <td style="width:32%;vertical-align:top;background:#ffffff;border:1px solid #e9ebf2;border-top:3px solid #16a34a;border-radius:8px;padding:12px 14px;">
                            <div style="font-size:7.5pt;font-weight:700;color:#16a34a;letter-spacing:1px;margin-bottom:8px;">POINTS FORTS</div>
                            ' . ($strengths ?: '<div style="font-size:8.5pt;color:#9ca3af;">—</div>') . '
                        </td>
                        <td style="width:2%;"></td>
                        <td style="width:32%;vertical-align:top;background:#ffffff;border:1px solid #e9ebf2;border-top:3px solid #d97706;border-radius:8px;padding:12px 14px;">
                            <div style="font-size:7.5pt;font-weight:700;color:#d97706;letter-spacing:1px;margin-bottom:8px;">AXES D\'AMÉLIORATION</div>
                            ' . ($weaknesses ?: '<div style="font-size:8.5pt;color:#9ca3af;">—</div>') . '
                        </td>
                        <td style="width:2%;"></td>
                        <td style="width:32%;vertical-align:top;background:#ffffff;border:1px solid #e9ebf2;border-top:3px solid ' . $navy . ';border-radius:8px;padding:12px 14px;">
                            <div style="font-size:7.5pt;font-weight:700;color:' . $navy . ';letter-spacing:1px;margin-bottom:8px;">RECOMMANDATION AQMI</div>
                            <div style="font-size:8.5pt;color:#374151;line-height:1.5;">L\'entreprise est recommandée pour le <strong>niveau ' . htmlspecialchars($levelName) . '</strong> et peut prétendre à des opportunités de développement au sein de la chaîne d\'approvisionnement.</div>
                        </td>
                    </tr>
                </table>

                <div style="margin-top:24px;padding-top:10px;border-top:1px solid #e9ebf2;text-align:center;font-size:6.5pt;color:#9ca3af;">
                    Document confidentiel généré le ' . date('d/m/Y') . ' — AQMI by NOVAQYS © ' . date('Y') . '
                </div>
            </div>
        </div>';
    }

    /**
     * Anneau de score SVG (stroke-dasharray) avec graduations discrètes,
     * compatible dompdf — évite conic-gradient/canvas non supportés.
     */
    private function buildGaugeSvg(float $percent, string $color): string
    {
        $radius = 60;
        $circumference = 2 * M_PI * $radius;
        $pct = max(0, min(100, $percent));
        $offset = $circumference * (1 - $pct / 100);

        $ticks = '';
        for ($t = 0; $t < 100; $t += 10) {
            $angle = (2 * M_PI * $t / 100) - (M_PI / 2);
            $x1 = 75 + 52 * cos($angle); $y1 = 75 + 52 * sin($angle);
            $x2 = 75 + 58 * cos($angle); $y2 = 75 + 58 * sin($angle);
            $ticks .= '<line x1="' . round($x1, 1) . '" y1="' . round($y1, 1) . '" x2="' . round($x2, 1) . '" y2="' . round($y2, 1) . '" stroke="#e5e7eb" stroke-width="1.5"/>';
        }

        return '
        <svg width="150" height="150" viewBox="0 0 150 150">
            ' . $ticks . '
            <circle cx="75" cy="75" r="' . $radius . '" fill="none" stroke="#eef0f4" stroke-width="12"/>
            <circle cx="75" cy="75" r="' . $radius . '" fill="none" stroke="' . $color . '" stroke-width="12"
                stroke-dasharray="' . round($circumference, 2) . '" stroke-dashoffset="' . round($offset, 2) . '"
                stroke-linecap="round" transform="rotate(-90 75 75)"/>
            <text x="75" y="72" text-anchor="middle" font-size="27" font-weight="bold" fill="' . $color . '">' . round($pct) . '%</text>
            <text x="75" y="90" text-anchor="middle" font-size="7" fill="#9ca3af" letter-spacing="1">MATURITÉ</text>
        </svg>';
    }

    /**
     * Radar/spider chart en SVG calculé en PHP (trigonométrie) — pas de
     * JS/canvas possible dans dompdf, donc les polygones sont dessinés
     * directement en coordonnées.
     */
    private function buildRadarSvg(array $domainScores, string $color = '#1a56db'): string
    {
        $n = count($domainScores);
        if ($n < 3) return '<div style="font-size:8pt;color:#9ca3af;">Pas assez de domaines pour un radar.</div>';

        $size = 230;
        $center = $size / 2;
        $maxR = 88;

        $gridPolygons = '';
        foreach ([25, 50, 75, 100] as $lvl) {
            $r = $maxR * ($lvl / 100);
            $pts = [];
            for ($i = 0; $i < $n; $i++) {
                $angle = (2 * M_PI * $i / $n) - (M_PI / 2);
                $pts[] = round($center + $r * cos($angle), 1) . ',' . round($center + $r * sin($angle), 1);
            }
            $gridPolygons .= '<polygon points="' . implode(' ', $pts) . '" fill="none" stroke="#eef0f4" stroke-width="0.75"/>';
        }

        $axisLines = '';
        $dataPts = [];
        $dots = '';
        for ($i = 0; $i < $n; $i++) {
            $angle = (2 * M_PI * $i / $n) - (M_PI / 2);
            $axisLines .= '<line x1="' . $center . '" y1="' . $center . '" x2="' . round($center + $maxR * cos($angle), 1) . '" y2="' . round($center + $maxR * sin($angle), 1) . '" stroke="#eef0f4" stroke-width="0.75"/>';

            $pct = max(0, min(100, $domainScores[$i]['percent_score']));
            $r = $maxR * ($pct / 100);
            $px = round($center + $r * cos($angle), 1);
            $py = round($center + $r * sin($angle), 1);
            $dataPts[] = $px . ',' . $py;
            $dots .= '<circle cx="' . $px . '" cy="' . $py . '" r="2.5" fill="' . $color . '"/>';
        }

        return '
        <svg width="' . $size . '" height="' . $size . '" viewBox="0 0 ' . $size . ' ' . $size . '">
            ' . $gridPolygons . $axisLines . '
            <polygon points="' . implode(' ', $dataPts) . '" fill="' . $color . '22" stroke="' . $color . '" stroke-width="2"/>
            ' . $dots . '
        </svg>';
    }

    /**
     * Légende des niveaux AQMI sous forme de puces (chips), tirée
     * dynamiquement de la table score_levels.
     */
    private function buildLevelLegendHtml(): string
    {
        $levels = ScoreLevel::all();
        if (empty($levels)) return '';

        $items = '';
        foreach ($levels as $lvl) {
            $items .= '<span style="display:inline-block;margin:0 10px 4px 0;padding:3px 10px;background:#f8f9fc;border:1px solid #e9ebf2;border-radius:999px;font-size:7pt;color:#374151;">
                <span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:' . $lvl['color'] . ';margin-right:4px;"></span>
                ' . htmlspecialchars($lvl['name_fr'] ?: $lvl['name']) . ' (' . round($lvl['min_percent']) . '–' . round($lvl['max_percent']) . '%)
            </span>';
        }
        return '<div style="text-align:center;margin:6px 0 4px;">' . $items . '</div>';
    }

    /**
     * Sceau circulaire "certifié" en SVG pur (double anneau + coche),
     * pour remplacer un logo/badge image qu\'on n\'a pas en assets.
     */
    private function buildSealSvg(string $color): string
    {
        return '
        <svg width="105" height="105" viewBox="0 0 105 105">
            <circle cx="52.5" cy="52.5" r="50" fill="none" stroke="' . $color . '" stroke-width="2"/>
            <circle cx="52.5" cy="52.5" r="43" fill="none" stroke="' . $color . '" stroke-width="1" stroke-dasharray="2,3"/>
            <circle cx="52.5" cy="52.5" r="36" fill="' . $color . '0d"/>
            <path d="M 38 53 L 47 62 L 68 40" fill="none" stroke="' . $color . '" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
            <text x="52.5" y="80" text-anchor="middle" font-size="6.5" font-weight="bold" fill="' . $color . '" letter-spacing="1.5">CERTIFIÉ AQMI</text>
        </svg>';
    }

    /**
     * Dernière page : observations, plan d'action, déclaration officielle,
     * signature électronique de l'administrateur et QR code de vérification.
     */
    private function buildCertificationPageHtml(array $report, string $reportNumber, ?string $qrDataUri): string
    {
        $navy = '#0b1f4d';
        $gold = '#b8860b';

        $qrImg = $qrDataUri
            ? '<img src="' . $qrDataUri . '" style="width:110px;height:110px;" />'
            : '<div style="width:110px;height:110px;border:1px dashed #d1d5db;border-radius:8px;text-align:center;padding-top:46px;font-size:7.5pt;color:#9ca3af;">QR indisponible</div>';

        $certDate = $report['certified_at'] ? date('d/m/Y', strtotime($report['certified_at'])) : date('d/m/Y');

        return '
        <div style="font-family:sans-serif;">
            <table style="width:100%;background:linear-gradient(135deg,' . $navy . ' 0%,#173b8c 100%);border-collapse:collapse;">
                <tr><td style="padding:18px 40px;">
                    <div style="font-size:14pt;font-weight:800;color:#ffffff;letter-spacing:1px;">Déclaration Officielle de Certification</div>
                </td></tr>
            </table>
            <div style="height:3px;background:' . $gold . ';"></div>

            <div style="padding:28px 40px;">
                <div style="font-size:9pt;color:' . $navy . ';font-weight:700;letter-spacing:1.5px;margin-bottom:8px;">OBSERVATIONS DE L\'ADMINISTRATEUR</div>
                <p style="font-size:9.5pt;color:#374151;line-height:1.7;padding-bottom:14px;border-bottom:1px solid #eef0f4;">' . nl2br(htmlspecialchars($report['observations'] ?: 'Aucune observation particulière.')) . '</p>

                <div style="font-size:9pt;color:' . $navy . ';font-weight:700;letter-spacing:1.5px;margin:20px 0 8px;">PLAN D\'ACTION RECOMMANDÉ</div>
                <p style="font-size:9.5pt;color:#374151;line-height:1.7;">' . nl2br(htmlspecialchars($report['action_plan'] ?: 'Aucun plan d\'action spécifique.')) . '</p>

                <table style="width:100%;margin-top:40px;border-top:2px solid ' . $navy . ';padding-top:22px;">
                    <tr>
                        <td style="width:62%;vertical-align:top;">
                            <p style="font-size:8.5pt;color:#6b7280;line-height:1.7;">
                                Ce certificat atteste que l\'entreprise mentionnée a fait l\'objet d\'une évaluation
                                de maturité qualité selon la méthodologie officielle <strong style="color:' . $navy . ';">AQMI by NOVAQYS</strong>.
                                Le QR code ci-contre permet d\'en vérifier l\'authenticité en ligne à tout moment.
                            </p>
                            <div style="margin-top:26px;">
                                <div style="font-size:7.5pt;color:#9ca3af;letter-spacing:1px;">SIGNATURE ÉLECTRONIQUE</div>
                                <div style="font-size:13pt;font-weight:bold;font-style:italic;color:' . $navy . ';border-bottom:1.5px solid ' . $navy . ';display:inline-block;padding:6px 24px 6px 0;margin-top:8px;">' . htmlspecialchars($report['admin_signature'] ?? '') . '</div>
                                <div style="font-size:7.5pt;color:#9ca3af;margin-top:4px;">Certifié le ' . $certDate . '</div>
                            </div>
                        </td>
                        <td style="width:38%;text-align:center;vertical-align:top;">
                            <div style="display:inline-block;padding:10px;border:1px solid #e9ebf2;border-radius:10px;">
                                ' . $qrImg . '
                            </div>
                            <div style="font-size:7.5pt;color:#9ca3af;margin-top:8px;">Réf. ' . htmlspecialchars($reportNumber) . '</div>
                        </td>
                    </tr>
                </table>

                <div style="margin-top:30px;text-align:center;padding:14px;background:#f8f9fc;border-radius:8px;">
                    <span style="font-size:9pt;font-weight:800;color:' . $navy . ';letter-spacing:1px;">RAPPORT AQMI CERTIFIÉ</span>
                </div>
            </div>
        </div>';
    }

    private function buildHtml(array $assessment, array $analysis, array $recommendations, ?array $lead, bool $includeIndustrialPark = true): string
    {
        $companyName = $lead['company'] ?? 'Entreprise';
        $leadName = ($lead['firstname'] ?? '') . ' ' . ($lead['lastname'] ?? '');
        $leadFullName = ($lead['firstname'] ?? '') . ' ' . ($lead['lastname'] ?? '');
        $globalScore = $analysis['global_score'];
        $level = $analysis['maturity_level'];
        $levelName = $level['name_fr'] ?? $level['name'] ?? 'Non défini';
        $levelColor = $level['color'] ?? '#6c757d';
        $levelIcon = $level['icon'] ?? 'fa-chart-bar';
        $date = date('d/m/Y');
        $ref = 'AQMI-' . $assessment['id'] . '-' . date('Ymd');
        $sector = $lead['sector'] ?? 'Non spécifié';
        $country = $lead['country'] ?? 'Non spécifié';
        $jobTitle = $lead['job_title'] ?? 'Non spécifié';
        $phone = $lead['phone'] ?? 'Non spécifié';
        $email = $lead['email'] ?? 'Non spécifié';
        $domainCount = count($analysis['domain_scores']);

        // ---- Generate domain score bars ----
        $domainBars = '';
        foreach ($analysis['domain_scores'] as $ds) {
            $pct = $ds['percent_score'];
            $barColor = $pct >= 70 ? '#059669' : ($pct >= 50 ? '#d97706' : '#dc3545');
            $domainBars .= '<tr>
                <td style="padding:10px 12px;border-bottom:1px solid #e5e7eb;font-size:10pt;">' . ($ds['domain_name_fr'] ?: $ds['domain_name']) . '</td>
                <td style="padding:10px 12px;border-bottom:1px solid #e5e7eb;width:160px;">
                    <div style="background:#f3f4f6;border-radius:6px;height:18px;overflow:hidden;">
                        <div style="height:100%;width:' . $pct . '%;background:' . $barColor . ';border-radius:6px;"></div>
                    </div>
                </td>
                <td style="padding:10px 12px;border-bottom:1px solid #e5e7eb;text-align:center;font-weight:bold;font-size:11pt;color:' . $barColor . ';">' . $pct . '%</td>
                <td style="padding:10px 12px;border-bottom:1px solid #e5e7eb;text-align:center;">' . $ds['avg_score'] . '/5</td>
            </tr>';
        }

        // ---- Légende des niveaux : construite depuis la vraie table
        // score_levels (avant, ce tableau était codé en dur et contredisait
        // la légende réelle affichée sur la page de couverture) ----
        $levelLegendRows = '';
        foreach (ScoreLevel::all() as $lvl) {
            $levelLegendRows .= '<tr>
                <td style="text-align:center;font-weight:bold;color:' . $lvl['color'] . ';">' . round($lvl['min_percent']) . '-' . round($lvl['max_percent']) . '%</td>
                <td style="font-weight:bold;" colspan="2">' . htmlspecialchars($lvl['name_fr'] ?: $lvl['name']) . '</td>
            </tr>';
        }

        // ---- Strengths & Weaknesses ----
        $strengthsHtml = '';
        foreach ($analysis['strengths'] as $i => $s) {
            $strengthsHtml .= '<tr>
                <td style="padding:6px 10px;border-bottom:1px solid #e5e7eb;width:30px;text-align:center;color:#059669;font-weight:bold;">' . ($i+1) . '</td>
                <td style="padding:6px 10px;border-bottom:1px solid #e5e7eb;">' . ($s['domain_name_fr'] ?: $s['domain_name']) . '</td>
                <td style="padding:6px 10px;border-bottom:1px solid #e5e7eb;text-align:right;font-weight:bold;color:#059669;">' . $s['percent_score'] . '%</td>
            </tr>';
        }
        $weaknessesHtml = '';
        foreach ($analysis['weaknesses'] as $i => $w) {
            $weaknessesHtml .= '<tr>
                <td style="padding:6px 10px;border-bottom:1px solid #e5e7eb;width:30px;text-align:center;color:#dc3545;font-weight:bold;">' . ($i+1) . '</td>
                <td style="padding:6px 10px;border-bottom:1px solid #e5e7eb;">' . ($w['domain_name_fr'] ?: $w['domain_name']) . '</td>
                <td style="padding:6px 10px;border-bottom:1px solid #e5e7eb;text-align:right;font-weight:bold;color:#dc3545;">' . $w['percent_score'] . '%</td>
            </tr>';
        }

        // ---- Recommendations ----
        $recHtml = '';
        foreach ($recommendations as $i => $rec) {
            $pClass = $rec['priority'];
            $pColor = $pClass === 'critical' ? '#dc3545' : ($pClass === 'high' ? '#d97706' : ($pClass === 'medium' ? '#1a56db' : '#6b7280'));
            $pBg   = $pClass === 'critical' ? '#fef2f2' : ($pClass === 'high' ? '#fffbeb' : ($pClass === 'medium' ? '#eff6ff' : '#f9fafb'));
            $pBadge = $pClass === 'critical' ? 'Critique' : ($pClass === 'high' ? 'Haute' : ($pClass === 'medium' ? 'Moyenne' : 'Basse'));
            $recHtml .= '<tr>
                <td style="padding:10px 12px;border-bottom:1px solid #e5e7eb;text-align:center;width:30px;font-weight:bold;color:' . $pColor . ';">' . ($i+1) . '</td>
                <td style="padding:10px 12px;border-bottom:1px solid #e5e7eb;font-size:9.5pt;">' . $rec['text'] . '</td>
                <td style="padding:10px 12px;border-bottom:1px solid #e5e7eb;text-align:center;width:90px;">
                    <span style="background:' . $pBg . ';color:' . $pColor . ';padding:4px 10px;border-radius:10px;font-size:8pt;font-weight:bold;">' . $pBadge . '</span>
                </td>
            </tr>';
        }

        // ---- Industrial Park Evaluation Data ----
        $industrialData = $this->getIndustrialParkData($sector, $companyName);
        $machineRows = '';
        foreach ($industrialData['machines'] as $m) {
            $statusColor = $m['status'] === 'Excellent' ? '#059669' : ($m['status'] === 'Bon' ? '#1a56db' : ($m['status'] === 'Moyen' ? '#d97706' : '#dc3545'));
            $machineRows .= '<tr>
                <td style="padding:7px 10px;border:1px solid #e5e7eb;font-size:9pt;">' . $m['name'] . '</td>
                <td style="padding:7px 10px;border:1px solid #e5e7eb;font-size:9pt;">' . $m['type'] . '</td>
                <td style="padding:7px 10px;border:1px solid #e5e7eb;font-size:9pt;text-align:center;">' . $m['capacity'] . '</td>
                <td style="padding:7px 10px;border:1px solid #e5e7eb;font-size:9pt;text-align:center;">' . $m['quantity'] . '</td>
                <td style="padding:7px 10px;border:1px solid #e5e7eb;font-size:9pt;text-align:center;color:' . $statusColor . ';font-weight:bold;">' . $m['status'] . '</td>
                <td style="padding:7px 10px;border:1px solid #e5e7eb;font-size:9pt;text-align:center;">' . $m['year'] . '</td>
            </tr>';
        }

        $prodLineRows = '';
        foreach ($industrialData['production_lines'] as $pl) {
            $capColor = $pl['efficiency'] >= 85 ? '#059669' : ($pl['efficiency'] >= 70 ? '#1a56db' : ($pl['efficiency'] >= 50 ? '#d97706' : '#dc3545'));
            $prodLineRows .= '<tr>
                <td style="padding:7px 10px;border:1px solid #e5e7eb;font-size:9pt;">' . $pl['name'] . '</td>
                <td style="padding:7px 10px;border:1px solid #e5e7eb;font-size:9pt;text-align:center;">' . $pl['products'] . '</td>
                <td style="padding:7px 10px;border:1px solid #e5e7eb;font-size:9pt;text-align:center;">' . $pl['daily_capacity'] . '</td>
                <td style="padding:7px 10px;border:1px solid #e5e7eb;font-size:9pt;text-align:center;color:' . $capColor . ';font-weight:bold;">' . $pl['efficiency'] . '%</td>
            </tr>';
        }

        $infraRows = '';
        foreach ($industrialData['infrastructure'] as $inf) {
            $infraRows .= '<tr>
                <td style="padding:7px 10px;border:1px solid #e5e7eb;font-size:9pt;">' . $inf['item'] . '</td>
                <td style="padding:7px 10px;border:1px solid #e5e7eb;font-size:9pt;">' . $inf['description'] . '</td>
                <td style="padding:7px 10px;border:1px solid #e5e7eb;font-size:9pt;text-align:center;">' . $inf['status'] . '</td>
            </tr>';
        }

        $levelDesc = $this->getMaturityLevelDescription($levelName);

        // ---- Section "Parc Industriel" : contient des données déclaratives
        // génériques par secteur, pas les vraies réponses de l'utilisateur.
        // Exclue du certificat officiel (voir generateCertificate()) pour ne
        // pas faire figurer d'informations non vérifiées dans un document
        // certifié ; conservée par défaut dans le résumé gratuit existant.
        $industrialParkSection = '';
        if ($includeIndustrialPark) {
            $industrialParkSection = '
<!-- ============================================================ -->
<!-- PAGE 3: ÉVALUATION DU PARC INDUSTRIEL -->
<!-- ============================================================ -->
<div class="page page-break">
    <div class="section-header">
        <div class="num">3</div>
        <div>
            <h2>Évaluation du Parc Industriel</h2>
            <div class="sub">Inventaire des équipements, capacités et état des installations</div>
        </div>
    </div>

    <p class="desc-text">
        Diagnostic détaillé du parc machines et équipements industriels de <strong>' . $companyName . '</strong>.
        Cette section présente l\'inventaire des actifs de production, leur état opérationnel et les capacités installées.
    </p>

    <div class="industrial-summary">
        <div class="stat-box">
            <div class="num">' . count($industrialData['machines']) . '</div>
            <div class="lbl">Machines<br>et Équipements</div>
        </div>
        <div class="stat-box">
            <div class="num">' . count($industrialData['production_lines']) . '</div>
            <div class="lbl">Lignes de<br>Production</div>
        </div>
        <div class="stat-box">
            <div class="num">' . $industrialData['total_capacity'] . '</div>
            <div class="lbl">Capacité<br>Journalière</div>
        </div>
        <div class="stat-box">
            <div class="num">' . $industrialData['avg_efficiency'] . '%</div>
            <div class="lbl">Rendement<br>Opérationnel</div>
        </div>
        <div class="stat-box">
            <div class="num">' . $industrialData['total_quantity'] . '</div>
            <div class="lbl">Total Unités<br>Installées</div>
        </div>
    </div>

    <div class="section-title-sm">Inventaire des Machines et Équipements</div>
    <table class="striped">
        <thead>
            <tr>
                <th style="width:22%;">Machine / Équipement</th>
                <th style="width:18%;">Type / Marque</th>
                <th style="width:16%;">Capacité</th>
                <th style="width:10%;">Qté</th>
                <th style="width:16%;">État</th>
                <th style="width:10%;">Année</th>
            </tr>
        </thead>
        <tbody>' . $machineRows . '</tbody>
    </table>

    <div class="section-title-sm" style="margin-top:25px;">Lignes de Production</div>
    <table class="striped">
        <thead>
            <tr>
                <th style="width:28%;">Ligne / Atelier</th>
                <th style="width:22%;">Produits</th>
                <th style="width:25%;">Capacité Journalière</th>
                <th style="width:15%;">Rendement</th>
            </tr>
        </thead>
        <tbody>' . $prodLineRows . '</tbody>
    </table>

    <div class="section-title-sm" style="margin-top:25px;">Infrastructure et Installations</div>
    <table class="striped">
        <thead>
            <tr>
                <th style="width:25%;">Installation</th>
                <th style="width:50%;">Description</th>
                <th style="width:15%;">État</th>
            </tr>
        </thead>
        <tbody>' . $infraRows . '</tbody>
    </table>

    <div style="margin-top:20px;padding:14px 18px;background:#fffbeb;border-radius:10px;border-left:4px solid #d97706;">
        <div style="font-size:10pt;font-weight:700;color:#92400e;margin-bottom:4px;">Note d\'Évaluation du Parc Industriel</div>
        <div style="font-size:9pt;color:#78350f;">
            L\'évaluation du parc industriel est basée sur les données déclarées et l\'analyse sectorielle.
            ' . ($industrialData['avg_efficiency'] >= 80 ? 'Le parc industriel présente un bon niveau de performance avec des équipements modernes et bien maintenus.' : ($industrialData['avg_efficiency'] >= 60 ? 'Le parc industriel est opérationnel mais nécessite des investissements ciblés pour améliorer la productivité.' : 'Le parc industriel nécessite une attention particulière avec un plan de modernisation et de maintenance à mettre en œuvre.')) . '
        </div>
    </div>
</div>
';
        }

        // ==============================================
        // BUILD THE COMPLETE HTML
        // ==============================================
        return '<!DOCTYPE html>
<html><head><meta charset="utf-8">
<style>
    @page { margin: 0; }
    body {
        font-family: "DejaVu Sans", sans-serif;
        font-size: 10pt;
        color: #1f2937;
        line-height: 1.5;
        margin: 0;
        padding: 0;
    }

    /* ===== COVER PAGE ===== */
    .cover-page {
        width: 100%;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .cover-bg {
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 55%;
        background: linear-gradient(135deg, #0f1a3e 0%, #1a56db 50%, #2563eb 100%);
    }
    .cover-bg-accent {
        position: absolute;
        top: 0; right: 0;
        width: 300px; height: 300px;
        background: rgba(255,255,255,0.04);
        border-radius: 0 0 0 100%;
    }
    .cover-content {
        position: relative;
        padding: 60px 50px;
        text-align: center;
    }
    .cover-logo {
        font-size: 52pt;
        font-weight: 900;
        color: #ffffff;
        letter-spacing: 6px;
        margin-top: 60px;
    }
    .cover-logo-sub {
        font-size: 13pt;
        color: rgba(255,255,255,0.8);
        letter-spacing: 3px;
        font-weight: 300;
        margin-top: 8px;
    }
    .cover-divider {
        width: 80px;
        height: 4px;
        background: #3b82f6;
        margin: 35px auto;
        border-radius: 2px;
    }
    .cover-title {
        font-size: 26pt;
        color: #ffffff;
        font-weight: 700;
        margin-top: 30px;
        line-height: 1.3;
    }
    .cover-subtitle {
        font-size: 13pt;
        color: rgba(255,255,255,0.75);
        margin-top: 12px;
        font-weight: 300;
    }
    .cover-info-box {
        background: #ffffff;
        border-radius: 16px;
        padding: 40px 30px;
        margin: 40px auto 0;
        max-width: 450px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    }
    .cover-info-box .company {
        font-size: 18pt;
        font-weight: 700;
        color: #0f1a3e;
        margin-bottom: 4px;
    }
    .cover-info-box .candidate {
        font-size: 11pt;
        color: #6b7280;
        margin-bottom: 12px;
    }
    .cover-info-box .meta {
        font-size: 9pt;
        color: #9ca3af;
    }
    .cover-info-box .meta span {
        display: inline-block;
        margin: 0 8px;
    }
    .cover-badge {
        display: inline-block;
        background: ' . $levelColor . ';
        color: #ffffff;
        padding: 12px 40px;
        border-radius: 50px;
        margin-top: 20px;
        font-size: 16pt;
        font-weight: 700;
        letter-spacing: 1px;
    }
    .cover-footer {
        position: absolute;
        bottom: 30px;
        left: 0; right: 0;
        text-align: center;
        color: #9ca3af;
        font-size: 8pt;
    }

    /* ===== GENERAL ===== */
    .page {
        padding: 50px 50px 40px;
    }
    .page-break { page-break-before: always; }

    .section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 25px;
        padding-bottom: 12px;
        border-bottom: 3px solid #1a56db;
    }
    .section-header .num {
        width: 36px; height: 36px;
        background: #1a56db;
        color: #fff;
        border-radius: 50%;
        text-align: center;
        line-height: 36px;
        font-size: 12pt;
        font-weight: 700;
        flex-shrink: 0;
    }
    .section-header h2 {
        font-size: 16pt;
        color: #0f1a3e;
        margin: 0;
        font-weight: 700;
    }
    .section-header .sub {
        font-size: 9pt;
        color: #9ca3af;
        font-weight: 400;
    }

    .section-title-sm {
        font-size: 12pt;
        color: #0f1a3e;
        font-weight: 700;
        margin: 20px 0 10px;
        padding-bottom: 6px;
        border-bottom: 1px solid #e5e7eb;
    }

    /* ===== TABLES ===== */
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 10px 0 20px;
    }
    table th {
        background: #0f1a3e;
        color: #ffffff;
        padding: 10px 12px;
        font-size: 9pt;
        font-weight: 600;
        text-align: left;
    }
    table td {
        padding: 8px 12px;
        font-size: 9.5pt;
    }
    table.striped tbody tr:nth-child(even) {
        background: #f9fafb;
    }

    /* ===== INFO CARDS ===== */
    .info-card {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 18px;
    }
    .info-card table td {
        padding: 6px 12px;
        border: none;
        font-size: 9.5pt;
    }
    .info-card table td:first-child {
        font-weight: 600;
        color: #6b7280;
        width: 140px;
    }

    /* ===== SCORE BOX ===== */
    .score-card {
        background: linear-gradient(135deg, ' . $levelColor . ' 0%, ' . $this->adjustBrightness($levelColor, -20) . ' 100%);
        color: #ffffff;
        border-radius: 16px;
        padding: 30px;
        text-align: center;
        margin: 15px 0 25px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    .score-card .score-value {
        font-size: 52pt;
        font-weight: 900;
        line-height: 1;
    }
    .score-card .score-label {
        font-size: 12pt;
        opacity: 0.9;
        margin-top: 6px;
    }
    .score-card .score-level {
        display: inline-block;
        background: rgba(255,255,255,0.2);
        padding: 6px 24px;
        border-radius: 20px;
        margin-top: 12px;
        font-size: 10pt;
        font-weight: 600;
    }

    /* ===== BADGES ===== */
    .badge-green  { color:#059669; background:#ecfdf5; padding:3px 10px; border-radius:8px; font-size:8pt; font-weight:700; }
    .badge-red    { color:#dc3545; background:#fef2f2; padding:3px 10px; border-radius:8px; font-size:8pt; font-weight:700; }

    /* ===== INDUSTRIAL SECTION ===== */
    .industrial-summary {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
    }
    .industrial-summary .stat-box {
        flex: 1;
        text-align: center;
        padding: 15px 8px;
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
    }
    .industrial-summary .stat-box .num {
        font-size: 18pt;
        font-weight: 800;
        color: #0f1a3e;
    }
    .industrial-summary .stat-box .lbl {
        font-size: 7.5pt;
        color: #6b7280;
        margin-top: 3px;
    }

    /* Bar indicator for status */
    .status-bar {
        display: inline-block;
        width: 12px; height: 12px;
        border-radius: 50%;
        margin-right: 5px;
        vertical-align: middle;
    }

    .footer-note {
        text-align: center;
        color: #9ca3af;
        font-size: 7.5pt;
        margin-top: 40px;
        padding-top: 15px;
        border-top: 1px solid #e5e7eb;
    }
    .page-num {
        text-align: right;
        color: #d1d5db;
        font-size: 8pt;
    }

    .desc-text {
        font-size: 9.5pt;
        color: #4b5563;
        margin: 10px 0 15px;
        line-height: 1.7;
    }
</style>
</head><body>

<!-- ============================================================ -->
<!-- COVER PAGE -->
<!-- ============================================================ -->
<div class="cover-page">
    <div class="cover-bg">
        <div class="cover-bg-accent"></div>
    </div>
    <div class="cover-content">
        <div class="cover-logo">AQMI</div>
        <div class="cover-logo-sub">AUTOMOTIVE QUALITY MATURITY INDEX</div>
        <div class="cover-divider"></div>
        <div class="cover-title">Rapport d\'Évaluation<br>de Maturité Qualité</div>
        <div class="cover-subtitle">Assessment &amp; Diagnostic de Performance Industrielle</div>

        <div class="cover-info-box">
            <div class="company">' . $companyName . '</div>
            <div class="candidate">' . $leadFullName . '</div>
            <div class="meta">
                <span>' . $sector . '</span> &bull;
                <span>' . $country . '</span>
            </div>
            <div class="cover-badge">' . $globalScore . '%</div>
            <div style="margin-top:12px;font-size:9pt;color:#6b7280;">
                Niveau : <strong>' . $levelName . '</strong>
            </div>
            <div style="margin-top:10px;font-size:8pt;color:#9ca3af;border-top:1px solid #e5e7eb;padding-top:10px;">
                Réf: ' . $ref . ' &nbsp;|&nbsp; Date: ' . $date . '
            </div>
        </div>
    </div>
    <div class="cover-footer">AQMI &copy; ' . date('Y') . ' &mdash; Document confidentiel</div>
</div>

<!-- ============================================================ -->
<!-- PAGE 1: FICHE D\'IDENTITÉ DU CANDIDAT -->
<!-- ============================================================ -->
<div class="page page-break">
    <div class="section-header">
        <div class="num">1</div>
        <div>
            <h2>Fiche d\'Identité du Candidat</h2>
            <div class="sub">Informations personnelles et professionnelles</div>
        </div>
    </div>

    <div class="info-card">
        <table>
            <tr><td>Nom &amp; Prénom</td><td><strong>' . $leadFullName . '</strong></td></tr>
            <tr><td>Entreprise / Société</td><td><strong>' . $companyName . '</strong></td></tr>
            <tr><td>Secteur d\'Activité</td><td>' . $sector . '</td></tr>
            <tr><td>Fonction / Poste</td><td>' . $jobTitle . '</td></tr>
            <tr><td>Email</td><td>' . $email . '</td></tr>
            <tr><td>Téléphone</td><td>' . $phone . '</td></tr>
            <tr><td>Pays</td><td>' . $country . '</td></tr>
            <tr><td>Date d\'Évaluation</td><td>' . $date . '</td></tr>
            <tr><td>Référence Rapport</td><td>' . $ref . '</td></tr>
        </table>
    </div>

    <div style="margin-top:20px;">
        <div class="section-title-sm">Informations Complémentaires</div>
        <div class="info-card">
            <table>
                <tr><td>Domaine d\'Expertise</td><td>Qualité, Management Industriel, Conformité Automobile</td></tr>
                <tr><td>Type d\'Évaluation</td><td>Auto-évaluation de Maturité Qualité</td></tr>
                <tr><td>Périmètre d\'Évaluation</td><td>' . $domainCount . ' domaines de performance</td></tr>
                <tr><td>Score Global Obtenu</td><td><strong>' . $globalScore . '%</strong></td></tr>
                <tr><td>Niveau de Maturité</td><td><span style="color:' . $levelColor . ';font-weight:bold;">' . $levelName . '</span></td></tr>
            </table>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- PAGE 2: RÉSUMÉ EXÉCUTIF -->
<!-- ============================================================ -->
<div class="page page-break">
    <div class="section-header">
        <div class="num">2</div>
        <div>
            <h2>Résumé Exécutif</h2>
            <div class="sub">Synthèse de l\'évaluation de maturité qualité</div>
        </div>
    </div>

    <p class="desc-text">
        Ce rapport présente les résultats de l\'évaluation de maturité qualité réalisée pour
        <strong>' . $companyName . '</strong>. L\'analyse couvre <strong>' . $domainCount . ' domaines</strong>
        de performance et fournit une vision claire des forces, axes d\'amélioration et priorités stratégiques.
    </p>

    <div class="score-card">
        <div class="score-value">' . $globalScore . '%</div>
        <div class="score-label">Score Global de Maturité Qualité</div>
        <div class="score-level">
            Niveau : ' . $levelName . '
        </div>
    </div>

    <p class="desc-text" style="text-align:center;font-size:10pt;color:#6b7280;">
        ' . $levelDesc . '
    </p>

    <table style="margin-top:15px;">
        <tr>
            <td style="width:50%;vertical-align:top;padding-right:15px;">
                <div class="section-title-sm" style="color:#059669;">Points Forts</div>
                <table>
                    <thead>
                        <tr><th style="background:#059669;">#</th><th style="background:#059669;">Domaine</th><th style="background:#059669;text-align:right;">Score</th></tr>
                    </thead>
                    <tbody>' . $strengthsHtml . '</tbody>
                </table>
            </td>
            <td style="width:50%;vertical-align:top;padding-left:15px;">
                <div class="section-title-sm" style="color:#dc3545;">Points à Améliorer</div>
                <table>
                    <thead>
                        <tr><th style="background:#dc3545;">#</th><th style="background:#dc3545;">Domaine</th><th style="background:#dc3545;text-align:right;">Score</th></tr>
                    </thead>
                    <tbody>' . $weaknessesHtml . '</tbody>
                </table>
            </td>
        </tr>
    </table>

    <div style="margin-top:20px;padding:16px 20px;background:#eff6ff;border-radius:10px;border-left:4px solid #1a56db;">
        <div style="font-size:10pt;font-weight:700;color:#0f1a3e;margin-bottom:4px;">Interprétation du Score</div>
        <div style="font-size:9pt;color:#4b5563;">
            ' . $companyName . ' se situe au niveau <strong>"' . $levelName . '"</strong> avec un score global de <strong>' . $globalScore . '%</strong>.
            ' . ($globalScore >= 70 ? 'L\'entreprise démontre une maîtrise solide de ses processus qualité avec des pratiques bien établies.' : ($globalScore >= 50 ? 'Des processus qualité sont en place mais des améliorations significatives sont nécessaires pour atteindre les standards d\'excellence.' : 'L\'entreprise est dans une phase de construction de son système qualité. Un plan d\'action structuré est fortement recommandé.')) . '
        </div>
    </div>
</div>

' . $industrialParkSection . '

<!-- ============================================================ -->
<!-- PAGE 4: ANALYSE DÉTAILLÉE PAR DOMAINE -->
<!-- ============================================================ -->
<div class="page page-break">
    <div class="section-header">
        <div class="num">4</div>
        <div>
            <h2>Analyse Détaillée par Domaine</h2>
            <div class="sub">Répartition des scores par domaine d\'évaluation</div>
        </div>
    </div>

    <p class="desc-text">
        Le tableau ci-dessous présente le détail des scores obtenus pour chacun des
        <strong>' . $domainCount . ' domaines</strong> évalués. La barre de progression
        indique visuellement le niveau de maîtrise atteint.
    </p>

    <table>
        <thead>
            <tr>
                <th style="width:28%;">Domaine d\'Évaluation</th>
                <th style="width:30%;">Niveau de Maîtrise</th>
                <th style="width:15%;text-align:center;">Score %</th>
                <th style="width:12%;text-align:center;">/5</th>
            </tr>
        </thead>
        <tbody>' . $domainBars . '</tbody>
    </table>

    <!-- Level Interpretation Legend -->
    <div style="margin-top:25px;">
        <div class="section-title-sm">Légende des Niveaux de Maturité</div>
        <table>
            <thead>
                <tr>
                    <th style="width:20%;background:#6b7280;">Score</th>
                    <th style="width:35%;background:#6b7280;">Niveau</th>
                    <th style="background:#6b7280;">Interprétation</th>
                </tr>
            </thead>
            <tbody>
                ' . $levelLegendRows . '
            </tbody>
        </table>
    </div>
</div>

<!-- ============================================================ -->
<!-- PAGE 5: PLAN D\'AMÉLIORATION -->
<!-- ============================================================ -->
<div class="page page-break">
    <div class="section-header">
        <div class="num">5</div>
        <div>
            <h2>Plan d\'Amélioration</h2>
            <div class="sub">Recommandations et actions prioritaires</div>
        </div>
    </div>

    <p class="desc-text">
        Sur la base des résultats de l\'évaluation, les recommandations suivantes sont proposées
        pour guider <strong>' . $companyName . '</strong> dans l\'amélioration de sa maturité qualité.
        Les actions sont classées par ordre de priorité.
    </p>

    ' . (!empty($recommendations) ? '
    <table class="striped">
        <thead>
            <tr>
                <th style="width:40px;text-align:center;">#</th>
                <th>Recommandation</th>
                <th style="width:90px;text-align:center;">Priorité</th>
            </tr>
        </thead>
        <tbody>' . $recHtml . '</tbody>
    </table>' : '<p style="text-align:center;color:#6b7280;padding:30px;background:#f9fafb;border-radius:10px;">Aucune recommandation spécifique générée automatiquement. Continuez sur votre lancée !</p>') . '

    <!-- Priority action plan table -->
    <div style="margin-top:25px;">
        <div class="section-title-sm">Synthèse du Plan d\'Action</div>
        <table>
            <thead>
                <tr>
                    <th style="background:#059669;">Priorité</th>
                    <th style="background:#059669;">Nombre d\'Actions</th>
                    <th style="background:#059669;">Délai Recommandé</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span style="color:#dc3545;font-weight:bold;">&#9679; Critique</span></td>
                    <td style="text-align:center;">' . count(array_filter($recommendations, fn($r) => $r['priority'] === 'critical')) . '</td>
                    <td>Immédiat (0-3 mois)</td>
                </tr>
                <tr>
                    <td><span style="color:#d97706;font-weight:bold;">&#9679; Haute</span></td>
                    <td style="text-align:center;">' . count(array_filter($recommendations, fn($r) => $r['priority'] === 'high')) . '</td>
                    <td>Court terme (3-6 mois)</td>
                </tr>
                <tr>
                    <td><span style="color:#1a56db;font-weight:bold;">&#9679; Moyenne</span></td>
                    <td style="text-align:center;">' . count(array_filter($recommendations, fn($r) => $r['priority'] === 'medium')) . '</td>
                    <td>Moyen terme (6-12 mois)</td>
                </tr>
                <tr>
                    <td><span style="color:#6b7280;font-weight:bold;">&#9679; Basse</span></td>
                    <td style="text-align:center;">' . count(array_filter($recommendations, fn($r) => $r['priority'] === 'low')) . '</td>
                    <td>Long terme (12+ mois)</td>
                </tr>
                <tr style="background:#f9fafb;font-weight:bold;">
                    <td>Total</td>
                    <td style="text-align:center;">' . count($recommendations) . '</td>
                    <td>-</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ============================================================ -->
<!-- PAGE 6: CONCLUSION -->
<!-- ============================================================ -->
<div class="page page-break">
    <div class="section-header">
        <div class="num">6</div>
        <div>
            <h2>Conclusion</h2>
            <div class="sub">Synthèse finale et recommandations stratégiques</div>
        </div>
    </div>

    <div style="text-align:center;margin:20px 0 30px;">
        <div style="display:inline-block;background:' . $levelColor . ';color:#fff;padding:20px 40px;border-radius:16px;">
            <div style="font-size:36pt;font-weight:900;">' . $globalScore . '%</div>
            <div style="font-size:11pt;opacity:0.9;">Score Global de Maturité</div>
            <div style="font-size:10pt;margin-top:6px;background:rgba(255,255,255,0.2);padding:4px 16px;border-radius:12px;display:inline-block;">
                Niveau : ' . $levelName . '
            </div>
        </div>
    </div>

    <div class="info-card">
        <table>
            <tr><td>Entreprise</td><td><strong>' . $companyName . '</strong></td></tr>
            <tr><td>Évalué par</td><td>' . $leadFullName . '</td></tr>
            <tr><td>Secteur</td><td>' . $sector . '</td></tr>
            <tr><td>Score Global</td><td><strong>' . $globalScore . '%</strong> — Niveau <span style="color:' . $levelColor . ';font-weight:bold;">"' . $levelName . '"</span></td></tr>
            <tr><td>Domaines évalués</td><td>' . $domainCount . '</td></tr>
            <tr><td>Date du rapport</td><td>' . $date . '</td></tr>
        </table>
    </div>

    <div style="margin-top:20px;padding:18px 22px;background:#f0fdf4;border-radius:10px;border-left:4px solid #059669;">
        <div style="font-size:10pt;font-weight:700;color:#065f46;margin-bottom:6px;">Prochaines Étapes Recommandées</div>
        <ol style="margin:0;padding-left:20px;font-size:9pt;color:#065f46;line-height:2;">
            <li>Analyser en détail les axes d\'amélioration identifiés dans ce rapport</li>
            <li>Élaborer un plan d\'action structuré avec des jalons mesurables</li>
            <li>Engager les équipes dans une démarche d\'amélioration continue</li>
            <li>Réaliser une nouvelle évaluation dans 6 à 12 mois pour mesurer les progrès</li>
            <li>Utiliser le référentiel AQMI comme guide de progression vers l\'excellence</li>
        </ol>
    </div>

    <p style="text-align:center;margin-top:30px;font-size:10pt;color:#6b7280;line-height:1.8;">
        <strong>AQMI — Automotive Quality Maturity Index</strong><br>
        Référentiel d\'évaluation de la maturité qualité dans l\'industrie automobile<br>
        Document confidentiel généré le ' . $date . '
    </p>

    <div class="footer-note">
        AQMI &copy; ' . date('Y') . ' — Tous droits réservés — Ce document est confidentiel et destiné à ' . $companyName . '
    </div>
</div>

</body></html>';
    }

    /**
     * Generate structured industrial park evaluation data based on sector
     */
    public function getIndustrialParkData(string $sector, string $companyName): array
    {
        // Base datasets per sector
        $sectorMachines = [
            'Automobile' => [
                ['name' => 'Centre d\'usinage CNC 5 axes', 'type' => 'DMG MORI DMU 80', 'capacity' => '600 pièces/jour', 'quantity' => 4, 'status' => 'Excellent', 'year' => '2022'],
                ['name' => 'Presse hydraulique', 'type' => 'SCHULER 800T', 'capacity' => '1200 pièces/jour', 'quantity' => 6, 'status' => 'Bon', 'year' => '2020'],
                ['name' => 'Robot de soudage', 'type' => 'FANUC Arc Mate', 'capacity' => '900 pièces/jour', 'quantity' => 8, 'status' => 'Excellent', 'year' => '2023'],
                ['name' => 'Ligne de peinture', 'type' => 'DÜRR EcoBell', 'capacity' => '500 pièces/jour', 'quantity' => 2, 'status' => 'Bon', 'year' => '2021'],
                ['name' => 'Machine de mesure 3D', 'type' => 'ZEISS ACCURA', 'capacity' => '200 mesures/jour', 'quantity' => 3, 'status' => 'Excellent', 'year' => '2023'],
                ['name' => 'Convoyeur automatisé', 'type' => 'SIEMENS S7-1500', 'capacity' => '1500 pièces/jour', 'quantity' => 5, 'status' => 'Bon', 'year' => '2021'],
                ['name' => 'Tour CN', 'type' => 'HAAS ST-30', 'capacity' => '400 pièces/jour', 'quantity' => 3, 'status' => 'Moyen', 'year' => '2019'],
                ['name' => 'Poste de contrôle qualité', 'type' => 'COGNEX In-Sight', 'capacity' => '100% production', 'quantity' => 4, 'status' => 'Bon', 'year' => '2022'],
            ],
            'Aéronautique' => [
                ['name' => 'Centre d\'usinage 5 axes', 'type' => 'MIKRON HSM 600', 'capacity' => '200 pièces/jour', 'quantity' => 5, 'status' => 'Excellent', 'year' => '2022'],
                ['name' => 'Fraiseuse grande vitesse', 'type' => 'STARAG LT', 'capacity' => '150 pièces/jour', 'quantity' => 3, 'status' => 'Excellent', 'year' => '2023'],
                ['name' => 'Machine d\'assemblage automatique', 'type' => 'BROETJE', 'capacity' => '80 pièces/jour', 'quantity' => 4, 'status' => 'Bon', 'year' => '2021'],
                ['name' => 'Scanner 3D industriel', 'type' => 'GOM ATOS', 'capacity' => '50 scans/jour', 'quantity' => 2, 'status' => 'Excellent', 'year' => '2023'],
                ['name' => 'Presse plieuse', 'type' => 'TRUMPF TruBend', 'capacity' => '300 pièces/jour', 'quantity' => 2, 'status' => 'Bon', 'year' => '2020'],
                ['name' => 'Four de traitement thermique', 'type' => 'NABERTHERM', 'capacity' => '100 cycles/sem', 'quantity' => 2, 'status' => 'Moyen', 'year' => '2018'],
            ],
            'Pharmaceutique' => [
                ['name' => 'Mélangeur industriel', 'type' => 'GEA Pharma', 'capacity' => '500 kg/heure', 'quantity' => 3, 'status' => 'Excellent', 'year' => '2023'],
                ['name' => 'Tabletteuse rotative', 'type' => 'FETTE 3200', 'capacity' => '400000 comp./h', 'quantity' => 4, 'status' => 'Bon', 'year' => '2021'],
                ['name' => 'Chaîne de conditionnement', 'type' => 'IMA BLITZ', 'capacity' => '300 boîtes/min', 'quantity' => 5, 'status' => 'Excellent', 'year' => '2022'],
                ['name' => 'Autoclave stérilisation', 'type' => 'FEDEGARI', 'capacity' => '200 cycles/jour', 'quantity' => 3, 'status' => 'Bon', 'year' => '2020'],
                ['name' => 'HPLC systèmes analyse', 'type' => 'AGILENT 1260', 'capacity' => '100 échantillons/j', 'quantity' => 4, 'status' => 'Excellent', 'year' => '2023'],
            ],
            'Mécanique' => [
                ['name' => 'Tour parallèle', 'type' => 'CAZENEUVE HBC', 'capacity' => '300 pièces/jour', 'quantity' => 5, 'status' => 'Moyen', 'year' => '2018'],
                ['name' => 'Fraiseuse universelle', 'type' => 'HURON KX', 'capacity' => '250 pièces/jour', 'quantity' => 4, 'status' => 'Bon', 'year' => '2020'],
                ['name' => 'Rectifieuse cylindrique', 'type' => 'STUDER S33', 'capacity' => '180 pièces/jour', 'quantity' => 3, 'status' => 'Bon', 'year' => '2021'],
                ['name' => 'Centre d\'usinage vertical', 'type' => 'HAAS VF-6', 'capacity' => '400 pièces/jour', 'quantity' => 3, 'status' => 'Excellent', 'year' => '2022'],
                ['name' => 'Poste de soudure TIG', 'type' => 'FRONIUS TPS', 'capacity' => '200 pièces/jour', 'quantity' => 6, 'status' => 'Bon', 'year' => '2021'],
                ['name' => 'Presse mécanique', 'type' => 'BALTIMORE', 'capacity' => '600 pièces/jour', 'quantity' => 2, 'status' => 'Moyen', 'year' => '2017'],
            ],
            'Électronique' => [
                ['name' => 'Pick & Place SMD', 'type' => 'ASM SIPLACE X', 'capacity' => '80000 composants/h', 'quantity' => 4, 'status' => 'Excellent', 'year' => '2023'],
                ['name' => 'Four de refusion', 'type' => 'HELLER 1800', 'capacity' => '600 cartes/h', 'quantity' => 3, 'status' => 'Bon', 'year' => '2022'],
                ['name' => 'Machine de soudure sélective', 'type' => 'ERSA Versaflow', 'capacity' => '400 cartes/h', 'quantity' => 2, 'status' => 'Excellent', 'year' => '2023'],
                ['name' => 'Testeur AOI', 'type' => 'OMRON VT-S730', 'capacity' => '800 cartes/h', 'quantity' => 4, 'status' => 'Bon', 'year' => '2022'],
                ['name' => 'Carte de test fonctionnel', 'type' => 'TERADYNE', 'capacity' => '300 cartes/h', 'quantity' => 3, 'status' => 'Excellent', 'year' => '2023'],
            ],
        ];

        $sectorLines = [
            'Automobile' => [
                ['name' => 'Ligne d\'emboutissage', 'products' => 'Panneaux carrosserie', 'daily_capacity' => '2000 pièces', 'efficiency' => 92],
                ['name' => 'Ligne de soudure carrosserie', 'products' => 'Caisses en blanc', 'daily_capacity' => '800 unités', 'efficiency' => 88],
                ['name' => 'Ligne d\'assemblage final', 'products' => 'Véhicules complets', 'daily_capacity' => '600 unités', 'efficiency' => 85],
                ['name' => 'Ligne de peinture', 'products' => 'Carrosseries peintes', 'daily_capacity' => '750 unités', 'efficiency' => 90],
            ],
            'Aéronautique' => [
                ['name' => 'Ligne d\'usinage structural', 'products' => 'Pièces structurelles', 'daily_capacity' => '120 pièces', 'efficiency' => 92],
                ['name' => 'Chaîne d\'assemblage aile', 'products' => 'Sections d\'aile', 'daily_capacity' => '8 unités', 'efficiency' => 85],
                ['name' => 'Atelier composites', 'products' => 'Pièces carbone', 'daily_capacity' => '50 pièces', 'efficiency' => 80],
            ],
            'Pharmaceutique' => [
                ['name' => 'Ligne de production solides', 'products' => 'Comprimés/gélules', 'daily_capacity' => '2 millions unités', 'efficiency' => 95],
                ['name' => 'Ligne de production liquides', 'products' => 'Sirops/injectables', 'daily_capacity' => '50000 flacons', 'efficiency' => 88],
                ['name' => 'Chaîne de conditionnement', 'products' => 'Produits finis', 'daily_capacity' => '100000 boîtes', 'efficiency' => 90],
            ],
            'Mécanique' => [
                ['name' => 'Atelier d\'usinage', 'products' => 'Pièces mécaniques', 'daily_capacity' => '800 pièces', 'efficiency' => 78],
                ['name' => 'Ligne d\'assemblage', 'products' => 'Sous-ensembles', 'daily_capacity' => '300 unités', 'efficiency' => 75],
                ['name' => 'Ligne de traitement surface', 'products' => 'Pièces traitées', 'daily_capacity' => '600 pièces', 'efficiency' => 82],
            ],
            'Électronique' => [
                ['name' => 'Ligne CMS', 'products' => 'Cartes électroniques', 'daily_capacity' => '5000 cartes', 'efficiency' => 94],
                ['name' => 'Ligne d\'assemblage final', 'products' => 'Produits finis', 'daily_capacity' => '3000 unités', 'efficiency' => 90],
                ['name' => 'Ligne de test', 'products' => 'Cartes testées', 'daily_capacity' => '4500 cartes', 'efficiency' => 96],
            ],
        ];

        $infrastructure = [
            ['item' => 'Surface couverte (production)', 'description' => 'Ateliers et zones de production', 'status' => 'Conforme'],
            ['item' => 'Système de gestion énergétique', 'description' => 'Monitoring et optimisation énergétique', 'status' => 'Opérationnel'],
            ['item' => 'Système de traitement des effluents', 'description' => 'Station d\'épuration industrielle', 'status' => 'Conforme'],
            ['item' => 'Réseau logistique interne', 'description' => 'Système de manutention et stockage', 'status' => 'Opérationnel'],
            ['item' => 'Salle blanche / environnement contrôlé', 'description' => 'Zones à atmosphère contrôlée', 'status' => 'Conforme'],
            ['item' => 'Système incendie et sécurité', 'description' => 'Détection et protection incendie', 'status' => 'Certifié'],
            ['item' => 'Infrastructure réseau IT/OT', 'description' => 'Réseau industriel et informatique', 'status' => 'Opérationnel'],
            ['item' => 'Laboratoire de contrôle qualité', 'description' => 'Métrologie et essais', 'status' => 'Équipé'],
        ];

        // Defaults for unknown sectors
        $defaultMachines = [
            ['name' => 'Unité de production principale', 'type' => 'Polyvalent', 'capacity' => '500 unités/jour', 'quantity' => 3, 'status' => 'Bon', 'year' => '2021'],
            ['name' => 'Équipement de contrôle qualité', 'type' => 'Automated QC', 'capacity' => '100% production', 'quantity' => 2, 'status' => 'Bon', 'year' => '2022'],
            ['name' => 'Chaîne d\'assemblage', 'type' => 'Semi-automatique', 'capacity' => '400 pièces/jour', 'quantity' => 2, 'status' => 'Moyen', 'year' => '2020'],
            ['name' => 'Machine de conditionnement', 'type' => 'Automatique', 'capacity' => '300 unités/jour', 'quantity' => 2, 'status' => 'Bon', 'year' => '2021'],
        ];
        $defaultLines = [
            ['name' => 'Ligne de production principale', 'products' => 'Produits finis', 'daily_capacity' => '500 unités', 'efficiency' => 80],
            ['name' => 'Ligne de conditionnement', 'products' => 'Emballage', 'daily_capacity' => '600 unités', 'efficiency' => 75],
        ];

        // Normalize sector key
        $sectorKey = $sector;
        $machines = $sectorMachines[$sectorKey] ?? $defaultMachines;
        $lines = $sectorLines[$sectorKey] ?? $defaultLines;

        $totalQty = array_sum(array_column($machines, 'quantity'));
        $avgEff = count($lines) > 0 ? round(array_sum(array_column($lines, 'efficiency')) / count($lines)) : 75;
        $totalCap = count($lines) > 0 ? $lines[0]['daily_capacity'] : 'N/A';

        return [
            'machines' => $machines,
            'production_lines' => $lines,
            'infrastructure' => $infrastructure,
            'total_quantity' => $totalQty,
            'avg_efficiency' => $avgEff,
            'total_capacity' => $totalCap,
        ];
    }

    /**
     * Get a description for the maturity level
     */
    public function getMaturityLevelDescription(string $levelName): string
    {
        $descriptions = [
            'Initial' => 'L\'entreprise est dans une phase de démarrage de sa démarche qualité. Les processus ne sont pas encore structurés et des actions fondamentales sont nécessaires pour établir un système de management de la qualité.',
            'En Développement' => 'Les processus qualité sont en cours de construction. Des bases sont posées mais la systématisation et la standardisation restent à renforcer pour atteindre un niveau de maturité satisfaisant.',
            'Structuré' => 'L\'entreprise dispose de processus qualité définis et documentés. Les pratiques sont appliquées de manière cohérente et les premiers indicateurs de performance sont suivis.',
            'Avancé' => 'Les processus qualité sont maîtrisés et mesurés. L\'entreprise démontre une capacité à prévenir les non-conformités et à optimiser ses performances de manière continue.',
            'Optimisé' => 'L\'entreprise a atteint un niveau d\'excellence opérationnelle. Les processus sont intégrés, optimisés et font l\'objet d\'une amélioration continue structurée. Les résultats sont durablement au rendez-vous.',
            'Excellence' => 'Niveau d\'excellence maximale. L\'entreprise est un benchmark dans son secteur. L\'innovation et l\'amélioration continue sont ancrées dans la culture d\'entreprise.',
        ];

        foreach ($descriptions as $key => $desc) {
            if (stripos($levelName, $key) !== false) return $desc;
        }

        return 'Niveau de maturité ' . $levelName . '. Des efforts d\'amélioration continue sont recommandés pour progresser vers le niveau supérieur.';
    }

    /**
     * Adjust hex color brightness
     */
    private function adjustBrightness(string $hex, int $steps): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) !== 6) return $hex;
        $r = max(0, min(255, hexdec(substr($hex, 0, 2)) + $steps));
        $g = max(0, min(255, hexdec(substr($hex, 2, 2)) + $steps));
        $b = max(0, min(255, hexdec(substr($hex, 4, 2)) + $steps));
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}