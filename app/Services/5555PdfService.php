<?php
namespace App\Services;

use App\Models\Assessment;
use App\Models\Lead;

class PdfService
{
    private ScoringService $scoringService;
    private RecommendationService $recommendationService;

    public function __construct()
    {
        $this->scoringService = new ScoringService();
        $this->recommendationService = new RecommendationService();
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

    private function buildHtml(array $assessment, array $analysis, array $recommendations, ?array $lead): string
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

    <!-- Industrial Summary Stats -->
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

    <!-- Machines / Equipment Table -->
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

    <!-- Production Lines -->
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

    <!-- Infrastructure -->
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

    <!-- Assessment Notes -->
    <div style="margin-top:20px;padding:14px 18px;background:#fffbeb;border-radius:10px;border-left:4px solid #d97706;">
        <div style="font-size:10pt;font-weight:700;color:#92400e;margin-bottom:4px;">Note d\'Évaluation du Parc Industriel</div>
        <div style="font-size:9pt;color:#78350f;">
            L\'évaluation du parc industriel est basée sur les données déclarées et l\'analyse sectorielle.
            ' . ($industrialData['avg_efficiency'] >= 80 ? 'Le parc industriel présente un bon niveau de performance avec des équipements modernes et bien maintenus.' : ($industrialData['avg_efficiency'] >= 60 ? 'Le parc industriel est opérationnel mais nécessite des investissements ciblés pour améliorer la productivité.' : 'Le parc industriel nécessite une attention particulière avec un plan de modernisation et de maintenance à mettre en œuvre.')) . '
        </div>
    </div>
</div>

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
                <tr><td style="text-align:center;font-weight:bold;color:#059669;">80-100%</td><td style="font-weight:bold;">Optimisé</td><td>Processus maîtrisés, amélioration continue active</td></tr>
                <tr><td style="text-align:center;font-weight:bold;color:#1a56db;">60-79%</td><td style="font-weight:bold;">Avancé</td><td>Processus structurés et mesurés, résultats conformes</td></tr>
                <tr><td style="text-align:center;font-weight:bold;color:#d97706;">40-59%</td><td style="font-weight:bold;">En Développement</td><td>Processus définis mais application irrégulière</td></tr>
                <tr><td style="text-align:center;font-weight:bold;color:#dc3545;">0-39%</td><td style="font-weight:bold;">Initial</td><td>Processus non structurés, actions correctives urgentes</td></tr>
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