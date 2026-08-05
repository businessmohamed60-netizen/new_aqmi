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
        $user = $assessment['user_id'] ? \App\Models\User::find($assessment['user_id']) : null;

        if ($this->hasStudioTemplate()) {
            $html = $this->renderWithStudioTemplate($assessment, $analysis, $recommendations, $lead, $user);
        } else {
            $html = $this->buildHtml($assessment, $analysis, $recommendations, $lead, true, $user);
        }

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
        $user = $assessment['user_id'] ? \App\Models\User::find($assessment['user_id']) : null;

        $reportNumber = $report['report_number'] ?: Report::assignReportNumber($reportId);
        $qrDataUri = $this->generateQrCode($reportId, $reportNumber);

        $coverHtml = $this->buildCoverPageHtml($assessment, $analysis, $lead, $report, $reportNumber);
        $rawBodyHtml = $this->buildHtml($assessment, $analysis, $recommendations, $lead, false, $user);
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
            $c = $pct >= 70 ? '#16a34a' : ($pct >= 50 ? '#d97706' : '#E5484D');
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
            <table style="width:100%;background:' . $navy . ';border-collapse:collapse;">
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
            <div style="height:3px;background:' . $gold . ';"></div>

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
    private function buildRadarSvg(array $domainScores, string $color = '#1F6FEB'): string
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
            <table style="width:100%;background:' . $navy . ';border-collapse:collapse;">
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
                            <p style="font-size:8.5pt;color:#486581;line-height:1.7;">
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

    /**
     * Check whether any published Report Studio template exists.
     */
    private function hasStudioTemplate(): bool
    {
        if (!class_exists(\App\Modules\ReportStudio\Models\ReportTemplate::class)) {
            return false;
        }
        try {
            return \App\Modules\ReportStudio\Models\ReportTemplate::publishedCount() > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Render a report using a Report Studio template (blocks + theme).
     * Falls back gracefully if the module or tables are missing.
     */
    private function renderWithStudioTemplate(array $assessment, array $analysis, array $recommendations, ?array $lead, ?array $user = null): string
    {
        $previewService = new \App\Modules\ReportStudio\Services\PreviewService();
        $renderer = new \App\Modules\ReportStudio\Services\TemplateRenderer();

        $templates = \App\Modules\ReportStudio\Models\ReportTemplate::all();
        $template = null;
        foreach ($templates as $t) {
            if ($t->status === 'published') {
                $template = $t;
                break;
            }
        }
        if (!$template) {
            $template = $templates[0] ?? null;
        }
        if (!$template) {
            return $this->buildHtml($assessment, $analysis, $recommendations, $lead, true, $user);
        }

        $data = $previewService->loadForPreview((int) $template->id);
        if (!$data) {
            return $this->buildHtml($assessment, $analysis, $recommendations, $lead, true, $user);
        }

        $reportNumber = $data['reportNumber'] ?? 'AQMI-RPT-' . str_pad((string) $assessment['id'], 3, '0', STR_PAD_LEFT);
        $blocksHtml = $renderer->renderAll($data['blocks'], $data['template'], $reportNumber, 'pdf');

        $globalScore = $analysis['global_score'];
        $level = $analysis['maturity_level'];
        $levelName = $level['name_fr'] ?? $level['name'] ?? 'Non défini';
        $levelColor = $level['color'] ?? '#102A43';

        $domainLabels = [];
        $domainValues = [];
        foreach ($analysis['domain_scores'] as $ds) {
            $domainLabels[] = $ds['domain_name_fr'] ?: $ds['domain_name'];
            $domainValues[] = $ds['percent_score'];
        }

        $radarSvg = $this->buildRadarSvg($analysis['domain_scores'], $levelColor);
        $gaugeSvg = $this->buildGaugeSvg($globalScore, $levelColor);

        $recoHtml = '';
        foreach ($recommendations as $rec) {
            $recoHtml .= '<li style="font-size:10pt;margin-bottom:6px;">' . htmlspecialchars($rec['text'] ?? '') . '</li>';
        }

        $companyName = $lead['company'] ?? 'Entreprise';
        $sector = $lead['sector'] ?? '—';
        $country = $lead['country'] ?? '—';

        $themeCss = $data['themeCss'] ?? '';
        $themeStyle = $data['themeStyle'] ?? '';

        $blocksHtml = str_replace(
            ['{{GLOBAL_SCORE}}', '{{LEVEL_NAME}}', '{{LEVEL_COLOR}}', '{{RADAR_SVG}}', '{{GAUGE_SVG}}', '{{RECOMMENDATIONS}}', '{{COMPANY_NAME}}', '{{SECTOR}}', '{{COUNTRY}}', '{{REPORT_NUMBER}}', '{{DATE}}'],
            [(string) $globalScore, $levelName, $levelColor, $radarSvg, $gaugeSvg, $recoHtml, htmlspecialchars($companyName), htmlspecialchars($sector), htmlspecialchars($country), htmlspecialchars($reportNumber), date('d/m/Y')],
            $blocksHtml
        );

        return '<!DOCTYPE html><html><head><meta charset="UTF-8">
<style>
' . $themeCss . '
body { font-family: var(--rs-font, "DejaVu Sans", sans-serif); color: var(--rs-body, #102A43); margin:0; padding:0; }
.rs-report { max-width: 210mm; margin: 0 auto; padding: 20mm 15mm; background: var(--rs-background, #fff); }
.rs-report-block { margin-bottom: 16px; page-break-inside: avoid; }
.rs-block-title { font-size: 12pt; font-weight: 700; color: var(--rs-heading, #102A43); margin-bottom: 8px; }
.rs-score-ring { width: 120px; height: 120px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; border: 8px solid var(--rs-primary, #102A43); }
.rs-score-value { font-size: 28pt; font-weight: 800; color: var(--rs-primary, #102A43); }
.rs-stamp { display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; border: 3px solid var(--rs-primary, #102A43); color: var(--rs-primary, #102A43); font-weight: 800; }
.rs-sig-line { border-top: 1px solid #999; margin-bottom: 6px; }
</style>
</head><body>
<div class="rs-report" style="' . $themeStyle . '">
' . $blocksHtml . '
</div>
</body></html>';
    }

    private function buildHtml(array $assessment, array $analysis, array $recommendations, ?array $lead, bool $includeIndustrialPark = true, ?array $user = null): string
    {
        $companyName = $lead['company'] ?? 'Entreprise';
        $leadName = ($lead['firstname'] ?? '') . ' ' . ($lead['lastname'] ?? '');
        $leadFullName = ($lead['firstname'] ?? '') . ' ' . ($lead['lastname'] ?? '');
        if (empty(trim($leadFullName)) && $user) {
            $leadFullName = ($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? '');
            $leadName = trim($leadFullName);
        }
        $globalScore = $analysis['global_score'];
        $level = $analysis['maturity_level'];
        $levelName = $level['name_fr'] ?? $level['name'] ?? 'Non défini';
        $levelColor = $level['color'] ?? '#6c757d';
        $date = date('d/m/Y');
        $ref = 'AQMI-' . $assessment['id'] . '-' . date('Ymd');
        $sector = $lead['sector'] ?? 'Non spécifié';
        $country = $lead['country'] ?? 'Non spécifié';
        $jobTitle = $lead['job_title'] ?? 'Non spécifié';
        $phone = $lead['phone'] ?? 'Non spécifié';
        $email = $lead['email'] ?? ($user['email'] ?? 'Non spécifié');
        $domainCount = count($analysis['domain_scores']);

        $navy = '#0b1f4d';
        $gold = '#b8860b';

        // ---- Domain score bars ----
        $domainBars = '';
        foreach ($analysis['domain_scores'] as $ds) {
            $pct = round($ds['percent_score']);
            $barColor = $pct >= 70 ? '#059669' : ($pct >= 50 ? '#d97706' : '#dc3545');
            $domainBars .= '<tr>
                <td style="padding:8px 10px;border-bottom:1px solid #e5e7eb;font-size:9.5pt;">' . htmlspecialchars($ds['domain_name_fr'] ?: $ds['domain_name']) . '</td>
                <td style="padding:8px 10px;border-bottom:1px solid #e5e7eb;width:150px;">
                    <table style="margin:0;border:none;"><tr><td style="padding:0;border:none;background:#f3f4f6;border-radius:4px;height:16px;width:' . $pct . '%;">
                        <div style="background:' . $barColor . ';height:16px;border-radius:4px;width:100%;"></div>
                    </td></tr></table>
                </td>
                <td style="padding:8px 10px;border-bottom:1px solid #e5e7eb;text-align:center;font-weight:bold;font-size:10pt;color:' . $barColor . ';">' . $pct . '%</td>
                <td style="padding:8px 10px;border-bottom:1px solid #e5e7eb;text-align:center;font-size:9pt;color:#6b7280;">' . round($ds['avg_score'], 1) . '/5</td>
            </tr>';
        }

        // ---- Level legend from DB ----
        $levelLegendRows = '';
        foreach (ScoreLevel::all() as $lvl) {
            $levelLegendRows .= '<tr>
                <td style="text-align:center;font-weight:bold;color:' . $lvl['color'] . ';padding:6px 10px;border-bottom:1px solid #e5e7eb;">' . round($lvl['min_percent']) . '-' . round($lvl['max_percent']) . '%</td>
                <td style="font-weight:bold;padding:6px 10px;border-bottom:1px solid #e5e7eb;color:' . $lvl['color'] . ';">' . htmlspecialchars($lvl['name_fr'] ?: $lvl['name']) . '</td>
                <td style="padding:6px 10px;border-bottom:1px solid #e5e7eb;font-size:8.5pt;color:#6b7280;">' . $this->getMaturityLevelDescription($lvl['name_fr'] ?: $lvl['name']) . '</td>
            </tr>';
        }

        // ---- Strengths & Weaknesses ----
        $strengthsHtml = '';
        foreach ($analysis['strengths'] as $i => $s) {
            $strengthsHtml .= '<tr>
                <td style="padding:5px 8px;border-bottom:1px solid #e5e7eb;width:24px;text-align:center;color:#059669;font-weight:bold;font-size:8pt;">' . ($i+1) . '</td>
                <td style="padding:5px 8px;border-bottom:1px solid #e5e7eb;font-size:8.5pt;">' . htmlspecialchars($s['domain_name_fr'] ?: $s['domain_name']) . '</td>
                <td style="padding:5px 8px;border-bottom:1px solid #e5e7eb;text-align:right;font-weight:bold;color:#059669;font-size:8.5pt;">' . round($s['percent_score']) . '%</td>
            </tr>';
        }
        $weaknessesHtml = '';
        foreach ($analysis['weaknesses'] as $i => $w) {
            $weaknessesHtml .= '<tr>
                <td style="padding:5px 8px;border-bottom:1px solid #e5e7eb;width:24px;text-align:center;color:#dc3545;font-weight:bold;font-size:8pt;">' . ($i+1) . '</td>
                <td style="padding:5px 8px;border-bottom:1px solid #e5e7eb;font-size:8.5pt;">' . htmlspecialchars($w['domain_name_fr'] ?: $w['domain_name']) . '</td>
                <td style="padding:5px 8px;border-bottom:1px solid #e5e7eb;text-align:right;font-weight:bold;color:#dc3545;font-size:8.5pt;">' . round($w['percent_score']) . '%</td>
            </tr>';
        }

        // ---- Recommendations ----
        $recHtml = '';
        foreach ($recommendations as $i => $rec) {
            $pClass = $rec['priority'];
            $pColor = $pClass === 'critical' ? '#dc3545' : ($pClass === 'high' ? '#d97706' : ($pClass === 'medium' ? '#1F6FEB' : '#6b7280'));
            $pBg   = $pClass === 'critical' ? '#fef2f2' : ($pClass === 'high' ? '#fffbeb' : ($pClass === 'medium' ? '#eff6ff' : '#f9fafb'));
            $pBadge = $pClass === 'critical' ? 'Critique' : ($pClass === 'high' ? 'Haute' : ($pClass === 'medium' ? 'Moyenne' : 'Basse'));
            $recHtml .= '<tr>
                <td style="padding:8px 10px;border-bottom:1px solid #e5e7eb;text-align:center;width:28px;font-weight:bold;color:' . $pColor . ';font-size:9pt;">' . ($i+1) . '</td>
                <td style="padding:8px 10px;border-bottom:1px solid #e5e7eb;font-size:9pt;color:#374151;">' . htmlspecialchars($rec['text'] ?? '') . '</td>
                <td style="padding:8px 10px;border-bottom:1px solid #e5e7eb;text-align:center;width:80px;">
                    <span style="background:' . $pBg . ';color:' . $pColor . ';padding:3px 8px;border-radius:4px;font-size:7.5pt;font-weight:bold;">' . $pBadge . '</span>
                </td>
            </tr>';
        }

        // ---- Industrial Park data ----
        $industrialData = $this->getIndustrialParkData($sector, $companyName);
        $machineRows = '';
        foreach ($industrialData['machines'] as $m) {
            $statusColor = $m['status'] === 'Excellent' ? '#059669' : ($m['status'] === 'Bon' ? '#1F6FEB' : ($m['status'] === 'Moyen' ? '#d97706' : '#dc3545'));
            $machineRows .= '<tr>
                <td style="padding:6px 8px;border:1px solid #e5e7eb;font-size:8.5pt;">' . htmlspecialchars($m['name']) . '</td>
                <td style="padding:6px 8px;border:1px solid #e5e7eb;font-size:8.5pt;">' . htmlspecialchars($m['type']) . '</td>
                <td style="padding:6px 8px;border:1px solid #e5e7eb;font-size:8.5pt;text-align:center;">' . htmlspecialchars($m['capacity']) . '</td>
                <td style="padding:6px 8px;border:1px solid #e5e7eb;font-size:8.5pt;text-align:center;">' . $m['quantity'] . '</td>
                <td style="padding:6px 8px;border:1px solid #e5e7eb;font-size:8.5pt;text-align:center;color:' . $statusColor . ';font-weight:bold;">' . $m['status'] . '</td>
                <td style="padding:6px 8px;border:1px solid #e5e7eb;font-size:8.5pt;text-align:center;">' . $m['year'] . '</td>
            </tr>';
        }

        $prodLineRows = '';
        foreach ($industrialData['production_lines'] as $pl) {
            $capColor = $pl['efficiency'] >= 85 ? '#059669' : ($pl['efficiency'] >= 70 ? '#1F6FEB' : ($pl['efficiency'] >= 50 ? '#d97706' : '#dc3545'));
            $prodLineRows .= '<tr>
                <td style="padding:6px 8px;border:1px solid #e5e7eb;font-size:8.5pt;">' . htmlspecialchars($pl['name']) . '</td>
                <td style="padding:6px 8px;border:1px solid #e5e7eb;font-size:8.5pt;text-align:center;">' . htmlspecialchars($pl['products']) . '</td>
                <td style="padding:6px 8px;border:1px solid #e5e7eb;font-size:8.5pt;text-align:center;">' . htmlspecialchars($pl['daily_capacity']) . '</td>
                <td style="padding:6px 8px;border:1px solid #e5e7eb;font-size:8.5pt;text-align:center;color:' . $capColor . ';font-weight:bold;">' . $pl['efficiency'] . '%</td>
            </tr>';
        }

        $infraRows = '';
        foreach ($industrialData['infrastructure'] as $inf) {
            $infraRows .= '<tr>
                <td style="padding:6px 8px;border:1px solid #e5e7eb;font-size:8.5pt;">' . htmlspecialchars($inf['item']) . '</td>
                <td style="padding:6px 8px;border:1px solid #e5e7eb;font-size:8.5pt;">' . htmlspecialchars($inf['description']) . '</td>
                <td style="padding:6px 8px;border:1px solid #e5e7eb;font-size:8.5pt;text-align:center;">' . htmlspecialchars($inf['status']) . '</td>
            </tr>';
        }

        $levelDesc = $this->getMaturityLevelDescription($levelName);

        $gaugeSvg = $this->buildGaugeSvg($globalScore, $levelColor);
        $radarSvg = $this->buildRadarSvg($analysis['domain_scores'], $levelColor);
        $legendHtml = $this->buildLevelLegendHtml();

        // ---- Industrial park section ----
        $industrialParkSection = '';
        if ($includeIndustrialPark) {
            $statBoxes = '';
            $stats = [
                [count($industrialData['machines']), 'Machines et Équipements'],
                [count($industrialData['production_lines']), 'Lignes de Production'],
                [$industrialData['total_capacity'], 'Capacité Journalière'],
                [$industrialData['avg_efficiency'] . '%', 'Rendement Opérationnel'],
                [$industrialData['total_quantity'], 'Total Unités Installées'],
            ];
            foreach ($stats as $s) {
                $statBoxes .= '<td style="width:20%;text-align:center;padding:12px 6px;background:#f8f9fc;border:1px solid #e9ebf2;border-radius:8px;">
                    <div style="font-size:16pt;font-weight:800;color:' . $navy . ';">' . $s[0] . '</div>
                    <div style="font-size:7pt;color:#6b7280;margin-top:2px;">' . $s[1] . '</div>
                </td>';
            }

            $industrialParkSection = '
<div class="page page-break">
    <table style="width:100%;border-collapse:collapse;margin-bottom:18px;border-bottom:3px solid ' . $navy . ';">
        <tr>
            <td style="width:32px;height:32px;background:' . $navy . ';color:#fff;text-align:center;font-size:11pt;font-weight:700;border-radius:50%;vertical-align:middle;">3</td>
            <td style="padding-left:10px;vertical-align:middle;">
                <div style="font-size:15pt;font-weight:700;color:' . $navy . ';">Évaluation du Parc Industriel</div>
                <div style="font-size:8pt;color:#9ca3af;">Inventaire des équipements, capacités et état des installations</div>
            </td>
        </tr>
    </table>

    <p style="font-size:9pt;color:#4b5563;line-height:1.6;margin:8px 0 14px;">
        Diagnostic détaillé du parc machines et équipements industriels de <strong>' . htmlspecialchars($companyName) . '</strong>.
        Cette section présente l\'inventaire des actifs de production, leur état opérationnel et les capacités installées.
    </p>

    <table style="width:100%;border-collapse:collapse;margin-bottom:16px;"><tr>' . $statBoxes . '</tr></table>

    <div style="font-size:11pt;font-weight:700;color:' . $navy . ';margin:16px 0 8px;padding-bottom:5px;border-bottom:1px solid #e5e7eb;">Inventaire des Machines et Équipements</div>
    <table style="width:100%;border-collapse:collapse;">
        <thead><tr>
            <th style="width:22%;background:' . $navy . ';color:#fff;padding:6px 8px;font-size:8pt;text-align:left;">Machine / Équipement</th>
            <th style="width:18%;background:' . $navy . ';color:#fff;padding:6px 8px;font-size:8pt;text-align:left;">Type / Marque</th>
            <th style="width:16%;background:' . $navy . ';color:#fff;padding:6px 8px;font-size:8pt;text-align:left;">Capacité</th>
            <th style="width:10%;background:' . $navy . ';color:#fff;padding:6px 8px;font-size:8pt;text-align:center;">Qté</th>
            <th style="width:16%;background:' . $navy . ';color:#fff;padding:6px 8px;font-size:8pt;text-align:center;">État</th>
            <th style="width:10%;background:' . $navy . ';color:#fff;padding:6px 8px;font-size:8pt;text-align:center;">Année</th>
        </tr></thead>
        <tbody>' . $machineRows . '</tbody>
    </table>

    <div style="font-size:11pt;font-weight:700;color:' . $navy . ';margin:18px 0 8px;padding-bottom:5px;border-bottom:1px solid #e5e7eb;">Lignes de Production</div>
    <table style="width:100%;border-collapse:collapse;">
        <thead><tr>
            <th style="width:28%;background:' . $navy . ';color:#fff;padding:6px 8px;font-size:8pt;text-align:left;">Ligne / Atelier</th>
            <th style="width:22%;background:' . $navy . ';color:#fff;padding:6px 8px;font-size:8pt;text-align:left;">Produits</th>
            <th style="width:25%;background:' . $navy . ';color:#fff;padding:6px 8px;font-size:8pt;text-align:left;">Capacité Journalière</th>
            <th style="width:15%;background:' . $navy . ';color:#fff;padding:6px 8px;font-size:8pt;text-align:center;">Rendement</th>
        </tr></thead>
        <tbody>' . $prodLineRows . '</tbody>
    </table>

    <div style="font-size:11pt;font-weight:700;color:' . $navy . ';margin:18px 0 8px;padding-bottom:5px;border-bottom:1px solid #e5e7eb;">Infrastructure et Installations</div>
    <table style="width:100%;border-collapse:collapse;">
        <thead><tr>
            <th style="width:25%;background:' . $navy . ';color:#fff;padding:6px 8px;font-size:8pt;text-align:left;">Installation</th>
            <th style="width:50%;background:' . $navy . ';color:#fff;padding:6px 8px;font-size:8pt;text-align:left;">Description</th>
            <th style="width:15%;background:' . $navy . ';color:#fff;padding:6px 8px;font-size:8pt;text-align:center;">État</th>
        </tr></thead>
        <tbody>' . $infraRows . '</tbody>
    </table>

    <div style="margin-top:16px;padding:12px 16px;background:#fffbeb;border-radius:8px;border-left:4px solid #d97706;">
        <div style="font-size:9pt;font-weight:700;color:#92400e;margin-bottom:3px;">Note d\'Évaluation du Parc Industriel</div>
        <div style="font-size:8.5pt;color:#78350f;line-height:1.5;">
            L\'évaluation du parc industriel est basée sur les données déclarées et l\'analyse sectorielle.
            ' . ($industrialData['avg_efficiency'] >= 80 ? 'Le parc industriel présente un bon niveau de performance avec des équipements modernes et bien maintenus.' : ($industrialData['avg_efficiency'] >= 60 ? 'Le parc industriel est opérationnel mais nécessite des investissements ciblés pour améliorer la productivité.' : 'Le parc industriel nécessite une attention particulière avec un plan de modernisation et de maintenance à mettre en œuvre.')) . '
        </div>
    </div>
</div>
';
        }

        $criticalCount = count(array_filter($recommendations, fn($r) => $r['priority'] === 'critical'));
        $highCount = count(array_filter($recommendations, fn($r) => $r['priority'] === 'high'));
        $mediumCount = count(array_filter($recommendations, fn($r) => $r['priority'] === 'medium'));
        $lowCount = count(array_filter($recommendations, fn($r) => $r['priority'] === 'low'));

        return '<!DOCTYPE html>
<html><head><meta charset="utf-8">
<style>
@page { margin: 0; }
body { font-family: "DejaVu Sans", sans-serif; font-size: 10pt; color: #1f2937; line-height: 1.5; margin: 0; padding: 0; }
.page { padding: 45px 40px 35px; }
.page-break { page-break-before: always; }
table { border-collapse: collapse; }
</style>
</head><body>

<!-- ===== COVER PAGE ===== -->
<div style="width:100%;background:' . $navy . ';padding:50px 40px 40px;text-align:center;">
    <div style="font-size:48pt;font-weight:900;color:#ffffff;letter-spacing:6px;">AQMI</div>
    <div style="font-size:10pt;color:#c7d2fe;letter-spacing:3px;margin-top:4px;">AUTOMOTIVE QUALITY MATURITY INDEX</div>
    <div style="width:60px;height:3px;background:' . $gold . ';margin:25px auto;"></div>
    <div style="font-size:22pt;color:#ffffff;font-weight:700;line-height:1.3;">Rapport d\'Évaluation<br>de Maturité Qualité</div>
    <div style="font-size:11pt;color:#c7d2fe;margin-top:10px;">Assessment &amp; Diagnostic de Performance Industrielle</div>
</div>
<div style="height:3px;background:' . $gold . ';"></div>

<div style="padding:30px 40px;text-align:center;">
    <table style="width:100%;max-width:420px;margin:0 auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:12px;padding:30px;">
        <tr><td style="text-align:center;padding:0 20px;">
            <div style="font-size:17pt;font-weight:700;color:' . $navy . ';">' . htmlspecialchars($companyName) . '</div>
            <div style="font-size:10pt;color:#6b7280;margin-top:4px;">' . htmlspecialchars($leadFullName) . '</div>
            <div style="font-size:8.5pt;color:#9ca3af;margin-top:8px;">' . htmlspecialchars($sector) . ' &bull; ' . htmlspecialchars($country) . '</div>
            <div style="margin-top:16px;display:inline-block;background:' . $levelColor . ';color:#ffffff;padding:10px 36px;border-radius:6px;font-size:15pt;font-weight:700;">' . $globalScore . '%</div>
            <div style="margin-top:10px;font-size:9pt;color:#6b7280;">Niveau : <strong style="color:' . $levelColor . ';">' . htmlspecialchars($levelName) . '</strong></div>
            <div style="margin-top:12px;font-size:7.5pt;color:#9ca3af;border-top:1px solid #e5e7eb;padding-top:10px;">Réf: ' . $ref . ' &nbsp;|&nbsp; Date: ' . $date . '</div>
        </td></tr>
    </table>
    <div style="margin-top:20px;font-size:7.5pt;color:#9ca3af;">AQMI &copy; ' . date('Y') . ' — Document confidentiel</div>
</div>

<!-- ===== PAGE 1: FICHE D\'IDENTITÉ ===== -->
<div class="page page-break">
    <table style="width:100%;border-collapse:collapse;margin-bottom:18px;border-bottom:3px solid ' . $navy . ';">
        <tr>
            <td style="width:32px;height:32px;background:' . $navy . ';color:#fff;text-align:center;font-size:11pt;font-weight:700;border-radius:50%;vertical-align:middle;">1</td>
            <td style="padding-left:10px;vertical-align:middle;">
                <div style="font-size:15pt;font-weight:700;color:' . $navy . ';">Fiche d\'Identité du Candidat</div>
                <div style="font-size:8pt;color:#9ca3af;">Informations personnelles et professionnelles</div>
            </td>
        </tr>
    </table>

    <table style="width:100%;background:#f8f9fc;border:1px solid #e9ebf2;border-radius:8px;margin-bottom:16px;">
        <tr><td style="padding:18px 20px;">
            <table style="width:100%;">
                <tr><td style="width:140px;font-weight:600;color:#6b7280;padding:5px 0;font-size:9.5pt;">Nom &amp; Prénom</td><td style="font-weight:bold;padding:5px 0;font-size:9.5pt;">' . htmlspecialchars($leadFullName) . '</td></tr>
                <tr><td style="font-weight:600;color:#6b7280;padding:5px 0;font-size:9.5pt;">Entreprise / Société</td><td style="font-weight:bold;padding:5px 0;font-size:9.5pt;">' . htmlspecialchars($companyName) . '</td></tr>
                <tr><td style="font-weight:600;color:#6b7280;padding:5px 0;font-size:9.5pt;">Secteur d\'Activité</td><td style="padding:5px 0;font-size:9.5pt;">' . htmlspecialchars($sector) . '</td></tr>
                <tr><td style="font-weight:600;color:#6b7280;padding:5px 0;font-size:9.5pt;">Fonction / Poste</td><td style="padding:5px 0;font-size:9.5pt;">' . htmlspecialchars($jobTitle) . '</td></tr>
                <tr><td style="font-weight:600;color:#6b7280;padding:5px 0;font-size:9.5pt;">Email</td><td style="padding:5px 0;font-size:9.5pt;">' . htmlspecialchars($email) . '</td></tr>
                <tr><td style="font-weight:600;color:#6b7280;padding:5px 0;font-size:9.5pt;">Téléphone</td><td style="padding:5px 0;font-size:9.5pt;">' . htmlspecialchars($phone) . '</td></tr>
                <tr><td style="font-weight:600;color:#6b7280;padding:5px 0;font-size:9.5pt;">Pays</td><td style="padding:5px 0;font-size:9.5pt;">' . htmlspecialchars($country) . '</td></tr>
                <tr><td style="font-weight:600;color:#6b7280;padding:5px 0;font-size:9.5pt;">Date d\'Évaluation</td><td style="padding:5px 0;font-size:9.5pt;">' . $date . '</td></tr>
                <tr><td style="font-weight:600;color:#6b7280;padding:5px 0;font-size:9.5pt;">Référence Rapport</td><td style="padding:5px 0;font-size:9.5pt;">' . $ref . '</td></tr>
            </table>
        </td></tr>
    </table>

    <div style="font-size:11pt;font-weight:700;color:' . $navy . ';margin:16px 0 8px;padding-bottom:5px;border-bottom:1px solid #e5e7eb;">Informations Complémentaires</div>
    <table style="width:100%;background:#f8f9fc;border:1px solid #e9ebf2;border-radius:8px;">
        <tr><td style="padding:18px 20px;">
            <table style="width:100%;">
                <tr><td style="width:140px;font-weight:600;color:#6b7280;padding:5px 0;font-size:9.5pt;">Domaine d\'Expertise</td><td style="padding:5px 0;font-size:9.5pt;">Qualité, Management Industriel, Conformité Automobile</td></tr>
                <tr><td style="font-weight:600;color:#6b7280;padding:5px 0;font-size:9.5pt;">Type d\'Évaluation</td><td style="padding:5px 0;font-size:9.5pt;">Auto-évaluation de Maturité Qualité</td></tr>
                <tr><td style="font-weight:600;color:#6b7280;padding:5px 0;font-size:9.5pt;">Périmètre</td><td style="padding:5px 0;font-size:9.5pt;">' . $domainCount . ' domaines de performance</td></tr>
                <tr><td style="font-weight:600;color:#6b7280;padding:5px 0;font-size:9.5pt;">Score Global</td><td style="font-weight:bold;padding:5px 0;font-size:9.5pt;">' . $globalScore . '%</td></tr>
                <tr><td style="font-weight:600;color:#6b7280;padding:5px 0;font-size:9.5pt;">Niveau de Maturité</td><td style="font-weight:bold;color:' . $levelColor . ';padding:5px 0;font-size:9.5pt;">' . htmlspecialchars($levelName) . '</td></tr>
            </table>
        </td></tr>
    </table>
</div>

<!-- ===== PAGE 2: RÉSUMÉ EXÉCUTIF ===== -->
<div class="page page-break">
    <table style="width:100%;border-collapse:collapse;margin-bottom:18px;border-bottom:3px solid ' . $navy . ';">
        <tr>
            <td style="width:32px;height:32px;background:' . $navy . ';color:#fff;text-align:center;font-size:11pt;font-weight:700;border-radius:50%;vertical-align:middle;">2</td>
            <td style="padding-left:10px;vertical-align:middle;">
                <div style="font-size:15pt;font-weight:700;color:' . $navy . ';">Résumé Exécutif</div>
                <div style="font-size:8pt;color:#9ca3af;">Synthèse de l\'évaluation de maturité qualité</div>
            </td>
        </tr>
    </table>

    <p style="font-size:9pt;color:#4b5563;line-height:1.6;margin:8px 0 14px;">
        Ce rapport présente les résultats de l\'évaluation de maturité qualité réalisée pour
        <strong>' . htmlspecialchars($companyName) . '</strong>. L\'analyse couvre <strong>' . $domainCount . ' domaines</strong>
        de performance et fournit une vision claire des forces, axes d\'amélioration et priorités stratégiques.
    </p>

    <table style="width:100%;background:' . $levelColor . ';border-radius:12px;margin:10px 0 18px;">
        <tr><td style="text-align:center;padding:25px 30px;">
            <div style="font-size:42pt;font-weight:900;color:#ffffff;line-height:1;">' . $globalScore . '%</div>
            <div style="font-size:11pt;color:#ffffff;margin-top:4px;">Score Global de Maturité Qualité</div>
            <div style="margin-top:10px;display:inline-block;background:rgba(255,255,255,0.25);padding:5px 20px;border-radius:6px;font-size:9pt;font-weight:600;color:#ffffff;">Niveau : ' . htmlspecialchars($levelName) . '</div>
        </td></tr>
    </table>

    <p style="text-align:center;font-size:9.5pt;color:#6b7280;line-height:1.6;margin:8px 0 14px;">' . $levelDesc . '</p>

    <table style="width:100%;margin-top:10px;">
        <tr>
            <td style="width:50%;vertical-align:top;padding-right:8px;">
                <div style="font-size:11pt;font-weight:700;color:#059669;margin-bottom:6px;padding-bottom:4px;border-bottom:1px solid #e5e7eb;">Points Forts</div>
                <table style="width:100%;">
                    <thead><tr>
                        <th style="background:#059669;color:#fff;padding:5px 6px;font-size:7.5pt;text-align:center;width:24px;">#</th>
                        <th style="background:#059669;color:#fff;padding:5px 6px;font-size:7.5pt;text-align:left;">Domaine</th>
                        <th style="background:#059669;color:#fff;padding:5px 6px;font-size:7.5pt;text-align:right;">Score</th>
                    </tr></thead>
                    <tbody>' . $strengthsHtml . '</tbody>
                </table>
            </td>
            <td style="width:50%;vertical-align:top;padding-left:8px;">
                <div style="font-size:11pt;font-weight:700;color:#dc3545;margin-bottom:6px;padding-bottom:4px;border-bottom:1px solid #e5e7eb;">Points à Améliorer</div>
                <table style="width:100%;">
                    <thead><tr>
                        <th style="background:#dc3545;color:#fff;padding:5px 6px;font-size:7.5pt;text-align:center;width:24px;">#</th>
                        <th style="background:#dc3545;color:#fff;padding:5px 6px;font-size:7.5pt;text-align:left;">Domaine</th>
                        <th style="background:#dc3545;color:#fff;padding:5px 6px;font-size:7.5pt;text-align:right;">Score</th>
                    </tr></thead>
                    <tbody>' . $weaknessesHtml . '</tbody>
                </table>
            </td>
        </tr>
    </table>

    <div style="margin-top:18px;padding:14px 18px;background:#eff6ff;border-radius:8px;border-left:4px solid ' . $navy . ';">
        <div style="font-size:9.5pt;font-weight:700;color:' . $navy . ';margin-bottom:3px;">Interprétation du Score</div>
        <div style="font-size:8.5pt;color:#4b5563;line-height:1.5;">
            ' . htmlspecialchars($companyName) . ' se situe au niveau <strong>"' . htmlspecialchars($levelName) . '"</strong> avec un score global de <strong>' . $globalScore . '%</strong>.
            ' . ($globalScore >= 70 ? 'L\'entreprise démontre une maîtrise solide de ses processus qualité avec des pratiques bien établies.' : ($globalScore >= 50 ? 'Des processus qualité sont en place mais des améliorations significatives sont nécessaires pour atteindre les standards d\'excellence.' : 'L\'entreprise est dans une phase de construction de son système qualité. Un plan d\'action structuré est fortement recommandé.')) . '
        </div>
    </div>
</div>

' . $industrialParkSection . '

<!-- ===== PAGE 4: ANALYSE PAR DOMAINE ===== -->
<div class="page page-break">
    <table style="width:100%;border-collapse:collapse;margin-bottom:18px;border-bottom:3px solid ' . $navy . ';">
        <tr>
            <td style="width:32px;height:32px;background:' . $navy . ';color:#fff;text-align:center;font-size:11pt;font-weight:700;border-radius:50%;vertical-align:middle;">4</td>
            <td style="padding-left:10px;vertical-align:middle;">
                <div style="font-size:15pt;font-weight:700;color:' . $navy . ';">Analyse Détaillée par Domaine</div>
                <div style="font-size:8pt;color:#9ca3af;">Répartition des scores par domaine d\'évaluation</div>
            </td>
        </tr>
    </table>

    <table style="width:100%;margin:10px 0;">
        <tr>
            <td style="width:48%;vertical-align:top;text-align:center;">
                <div style="font-size:8pt;font-weight:700;color:' . $navy . ';letter-spacing:1px;margin-bottom:4px;">SCORE GLOBAL</div>
                ' . $gaugeSvg . '
            </td>
            <td style="width:4%;"></td>
            <td style="width:48%;vertical-align:top;text-align:center;">
                <div style="font-size:8pt;font-weight:700;color:' . $navy . ';letter-spacing:1px;margin-bottom:4px;">PROFIL DE MATURITÉ</div>
                ' . $radarSvg . '
            </td>
        </tr>
    </table>

    ' . $legendHtml . '

    <table style="width:100%;margin-top:16px;">
        <thead><tr>
            <th style="width:28%;background:' . $navy . ';color:#fff;padding:7px 10px;font-size:8.5pt;text-align:left;">Domaine d\'Évaluation</th>
            <th style="width:30%;background:' . $navy . ';color:#fff;padding:7px 10px;font-size:8.5pt;text-align:left;">Niveau de Maîtrise</th>
            <th style="width:15%;background:' . $navy . ';color:#fff;padding:7px 10px;font-size:8.5pt;text-align:center;">Score %</th>
            <th style="width:12%;background:' . $navy . ';color:#fff;padding:7px 10px;font-size:8.5pt;text-align:center;">/5</th>
        </tr></thead>
        <tbody>' . $domainBars . '</tbody>
    </table>

    <div style="font-size:11pt;font-weight:700;color:' . $navy . ';margin:20px 0 8px;padding-bottom:5px;border-bottom:1px solid #e5e7eb;">Légende des Niveaux de Maturité</div>
    <table style="width:100%;">
        <thead><tr>
            <th style="width:18%;background:#6b7280;color:#fff;padding:6px 8px;font-size:8pt;text-align:center;">Score</th>
            <th style="width:30%;background:#6b7280;color:#fff;padding:6px 8px;font-size:8pt;text-align:left;">Niveau</th>
            <th style="background:#6b7280;color:#fff;padding:6px 8px;font-size:8pt;text-align:left;">Interprétation</th>
        </tr></thead>
        <tbody>' . $levelLegendRows . '</tbody>
    </table>
</div>

<!-- ===== PAGE 5: PLAN D\'AMÉLIORATION ===== -->
<div class="page page-break">
    <table style="width:100%;border-collapse:collapse;margin-bottom:18px;border-bottom:3px solid ' . $navy . ';">
        <tr>
            <td style="width:32px;height:32px;background:' . $navy . ';color:#fff;text-align:center;font-size:11pt;font-weight:700;border-radius:50%;vertical-align:middle;">5</td>
            <td style="padding-left:10px;vertical-align:middle;">
                <div style="font-size:15pt;font-weight:700;color:' . $navy . ';">Plan d\'Amélioration</div>
                <div style="font-size:8pt;color:#9ca3af;">Recommandations et actions prioritaires</div>
            </td>
        </tr>
    </table>

    <p style="font-size:9pt;color:#4b5563;line-height:1.6;margin:8px 0 14px;">
        Sur la base des résultats de l\'évaluation, les recommandations suivantes sont proposées
        pour guider <strong>' . htmlspecialchars($companyName) . '</strong> dans l\'amélioration de sa maturité qualité.
        Les actions sont classées par ordre de priorité.
    </p>

    ' . (!empty($recommendations) ? '
    <table style="width:100%;">
        <thead><tr>
            <th style="width:28px;background:' . $navy . ';color:#fff;padding:7px 8px;font-size:8pt;text-align:center;">#</th>
            <th style="background:' . $navy . ';color:#fff;padding:7px 8px;font-size:8pt;text-align:left;">Recommandation</th>
            <th style="width:80px;background:' . $navy . ';color:#fff;padding:7px 8px;font-size:8pt;text-align:center;">Priorité</th>
        </tr></thead>
        <tbody>' . $recHtml . '</tbody>
    </table>' : '<p style="text-align:center;color:#6b7280;padding:25px;background:#f9fafb;border-radius:8px;font-size:9pt;">Aucune recommandation spécifique générée automatiquement. Continuez sur votre lancée !</p>') . '

    <div style="font-size:11pt;font-weight:700;color:' . $navy . ';margin:20px 0 8px;padding-bottom:5px;border-bottom:1px solid #e5e7eb;">Synthèse du Plan d\'Action</div>
    <table style="width:100%;">
        <thead><tr>
            <th style="background:#059669;color:#fff;padding:7px 10px;font-size:8pt;text-align:left;">Priorité</th>
            <th style="background:#059669;color:#fff;padding:7px 10px;font-size:8pt;text-align:center;">Nombre d\'Actions</th>
            <th style="background:#059669;color:#fff;padding:7px 10px;font-size:8pt;text-align:left;">Délai Recommandé</th>
        </tr></thead>
        <tbody>
            <tr><td style="padding:6px 10px;border-bottom:1px solid #e5e7eb;font-size:8.5pt;"><span style="color:#dc3545;font-weight:bold;">●</span> Critique</td><td style="padding:6px 10px;border-bottom:1px solid #e5e7eb;text-align:center;font-size:8.5pt;">' . $criticalCount . '</td><td style="padding:6px 10px;border-bottom:1px solid #e5e7eb;font-size:8.5pt;">Immédiat (0-3 mois)</td></tr>
            <tr><td style="padding:6px 10px;border-bottom:1px solid #e5e7eb;font-size:8.5pt;"><span style="color:#d97706;font-weight:bold;">●</span> Haute</td><td style="padding:6px 10px;border-bottom:1px solid #e5e7eb;text-align:center;font-size:8.5pt;">' . $highCount . '</td><td style="padding:6px 10px;border-bottom:1px solid #e5e7eb;font-size:8.5pt;">Court terme (3-6 mois)</td></tr>
            <tr><td style="padding:6px 10px;border-bottom:1px solid #e5e7eb;font-size:8.5pt;"><span style="color:#1F6FEB;font-weight:bold;">●</span> Moyenne</td><td style="padding:6px 10px;border-bottom:1px solid #e5e7eb;text-align:center;font-size:8.5pt;">' . $mediumCount . '</td><td style="padding:6px 10px;border-bottom:1px solid #e5e7eb;font-size:8.5pt;">Moyen terme (6-12 mois)</td></tr>
            <tr><td style="padding:6px 10px;border-bottom:1px solid #e5e7eb;font-size:8.5pt;"><span style="color:#6b7280;font-weight:bold;">●</span> Basse</td><td style="padding:6px 10px;border-bottom:1px solid #e5e7eb;text-align:center;font-size:8.5pt;">' . $lowCount . '</td><td style="padding:6px 10px;border-bottom:1px solid #e5e7eb;font-size:8.5pt;">Long terme (12+ mois)</td></tr>
            <tr style="background:#f9fafb;font-weight:bold;"><td style="padding:6px 10px;font-size:8.5pt;">Total</td><td style="padding:6px 10px;text-align:center;font-size:8.5pt;">' . count($recommendations) . '</td><td style="padding:6px 10px;font-size:8.5pt;">—</td></tr>
        </tbody>
    </table>
</div>

<!-- ===== PAGE 6: CONCLUSION ===== -->
<div class="page page-break">
    <table style="width:100%;border-collapse:collapse;margin-bottom:18px;border-bottom:3px solid ' . $navy . ';">
        <tr>
            <td style="width:32px;height:32px;background:' . $navy . ';color:#fff;text-align:center;font-size:11pt;font-weight:700;border-radius:50%;vertical-align:middle;">6</td>
            <td style="padding-left:10px;vertical-align:middle;">
                <div style="font-size:15pt;font-weight:700;color:' . $navy . ';">Conclusion</div>
                <div style="font-size:8pt;color:#9ca3af;">Synthèse finale et recommandations stratégiques</div>
            </td>
        </tr>
    </table>

    <table style="width:100%;margin:16px 0 24px;"><tr><td style="text-align:center;">
        <table style="display:inline-block;background:' . $levelColor . ';border-radius:12px;"><tr><td style="text-align:center;padding:18px 36px;">
            <div style="font-size:32pt;font-weight:900;color:#ffffff;">' . $globalScore . '%</div>
            <div style="font-size:10pt;color:#ffffff;">Score Global de Maturité</div>
            <div style="margin-top:6px;display:inline-block;background:rgba(255,255,255,0.25);padding:4px 14px;border-radius:6px;font-size:9pt;font-weight:600;color:#ffffff;">Niveau : ' . htmlspecialchars($levelName) . '</div>
        </td></tr></table>
    </td></tr></table>

    <table style="width:100%;background:#f8f9fc;border:1px solid #e9ebf2;border-radius:8px;margin-bottom:16px;">
        <tr><td style="padding:18px 20px;">
            <table style="width:100%;">
                <tr><td style="width:140px;font-weight:600;color:#6b7280;padding:5px 0;font-size:9.5pt;">Entreprise</td><td style="font-weight:bold;padding:5px 0;font-size:9.5pt;">' . htmlspecialchars($companyName) . '</td></tr>
                <tr><td style="font-weight:600;color:#6b7280;padding:5px 0;font-size:9.5pt;">Évalué par</td><td style="padding:5px 0;font-size:9.5pt;">' . htmlspecialchars($leadFullName) . '</td></tr>
                <tr><td style="font-weight:600;color:#6b7280;padding:5px 0;font-size:9.5pt;">Secteur</td><td style="padding:5px 0;font-size:9.5pt;">' . htmlspecialchars($sector) . '</td></tr>
                <tr><td style="font-weight:600;color:#6b7280;padding:5px 0;font-size:9.5pt;">Score Global</td><td style="padding:5px 0;font-size:9.5pt;"><strong>' . $globalScore . '%</strong> — Niveau <span style="color:' . $levelColor . ';font-weight:bold;">"' . htmlspecialchars($levelName) . '"</span></td></tr>
                <tr><td style="font-weight:600;color:#6b7280;padding:5px 0;font-size:9.5pt;">Domaines évalués</td><td style="padding:5px 0;font-size:9.5pt;">' . $domainCount . '</td></tr>
                <tr><td style="font-weight:600;color:#6b7280;padding:5px 0;font-size:9.5pt;">Date du rapport</td><td style="padding:5px 0;font-size:9.5pt;">' . $date . '</td></tr>
            </table>
        </td></tr>
    </table>

    <div style="padding:14px 18px;background:#f0fdf4;border-radius:8px;border-left:4px solid #059669;margin-bottom:16px;">
        <div style="font-size:9.5pt;font-weight:700;color:#065f46;margin-bottom:4px;">Prochaines Étapes Recommandées</div>
        <table style="font-size:8.5pt;color:#065f46;line-height:1.8;">
            <tr><td style="padding:2px 8px 2px 0;font-weight:bold;">1.</td><td>Analyser en détail les axes d\'amélioration identifiés dans ce rapport</td></tr>
            <tr><td style="padding:2px 8px 2px 0;font-weight:bold;">2.</td><td>Élaborer un plan d\'action structuré avec des jalons mesurables</td></tr>
            <tr><td style="padding:2px 8px 2px 0;font-weight:bold;">3.</td><td>Engager les équipes dans une démarche d\'amélioration continue</td></tr>
            <tr><td style="padding:2px 8px 2px 0;font-weight:bold;">4.</td><td>Réaliser une nouvelle évaluation dans 6 à 12 mois pour mesurer les progrès</td></tr>
            <tr><td style="padding:2px 8px 2px 0;font-weight:bold;">5.</td><td>Utiliser le référentiel AQMI comme guide de progression vers l\'excellence</td></tr>
        </table>
    </div>

    <p style="text-align:center;margin-top:20px;font-size:9pt;color:#6b7280;line-height:1.6;">
        <strong>AQMI — Automotive Quality Maturity Index</strong><br>
        Référentiel d\'évaluation de la maturité qualité dans l\'industrie automobile<br>
        Document confidentiel généré le ' . $date . '
    </p>

    <div style="text-align:center;color:#9ca3af;font-size:7.5pt;margin-top:24px;padding-top:12px;border-top:1px solid #e5e7eb;">
        AQMI &copy; ' . date('Y') . ' — Tous droits réservés — Ce document est confidentiel et destiné à ' . htmlspecialchars($companyName) . '
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
            'Beginner' => 'L\'entreprise est dans une phase de démarrage de sa démarche qualité. Les processus ne sont pas encore structurés et des actions fondamentales sont nécessaires pour établir un système de management de la qualité.',
            'Débutant' => 'L\'entreprise est dans une phase de démarrage de sa démarche qualité. Les processus ne sont pas encore structurés et des actions fondamentales sont nécessaires pour établir un système de management de la qualité.',
            'Developing' => 'Les processus qualité sont en cours de construction. Des bases sont posées mais la systématisation et la standardisation restent à renforcer pour atteindre un niveau de maturité satisfaisant.',
            'En Développement' => 'Les processus qualité sont en cours de construction. Des bases sont posées mais la systématisation et la standardisation restent à renforcer pour atteindre un niveau de maturité satisfaisant.',
            'Structured' => 'L\'entreprise dispose de processus qualité définis et documentés. Les pratiques sont appliquées de manière cohérente et les premiers indicateurs de performance sont suivis.',
            'Structuré' => 'L\'entreprise dispose de processus qualité définis et documentés. Les pratiques sont appliquées de manière cohérente et les premiers indicateurs de performance sont suivis.',
            'Performing' => 'Les processus qualité sont maîtrisés et mesurés. L\'entreprise démontre une capacité à prévenir les non-conformités et à optimiser ses performances de manière continue.',
            'Performant' => 'Les processus qualité sont maîtrisés et mesurés. L\'entreprise démontre une capacité à prévenir les non-conformités et à optimiser ses performances de manière continue.',
            'Excellence' => 'L\'entreprise a atteint un niveau d\'excellence opérationnelle. Les processus sont intégrés, optimisés et font l\'objet d\'une amélioration continue structurée. Les résultats sont durablement au rendez-vous.',
        ];

        foreach ($descriptions as $key => $desc) {
            if (stripos($levelName, $key) !== false) return $desc;
        }

        return 'Niveau de maturité ' . $levelName . '. Des efforts d\'amélioration continue sont recommandés pour progresser vers le niveau supérieur.';
    }
}