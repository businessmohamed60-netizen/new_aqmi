<?php
/**
 * Fiche Prospect (administrateur)
 * - Données du prospect (lead) croisées avec le profil utilisateur
 * - Score global + scores détaillés par domaine
 * - Sections sélectionnables, prêtes à imprimer (A4)
 *
 * Variables attendues du contrôleur : $lead, $assessment, $customFields, $documents
 * Fournies par AdminController::leadDetail :
 *   $analysis       -> ScoringService::analyzeAssessment() (domain_scores, global_score, maturity_level, benchmark)
 *   $profileUser    -> ligne de la table users liée à l'évaluation (assessments.user_id)
 */
$lead         = $lead ?? [];
$assessment   = $assessment ?? null;
$customFields = $customFields ?? [];
$documents    = $documents ?? [];
$title = 'Fiche Prospect : ' . e((string)($lead['firstname'] ?? '')) . ' ' . e((string)($lead['lastname'] ?? ''));
ob_start();

/* ───────────── Profil utilisateur lié ───────────── */
$profileUser = $profileUser ?? null;
if ($profileUser === null) {
    $uid = $lead['user_id'] ?? ($assessment['user_id'] ?? null);
    if ($uid) {
        try { $profileUser = \App\Models\User::find($uid); } catch (\Throwable $ex) { $profileUser = null; }
    }
}
$profileUser = is_array($profileUser) ? $profileUser : [];

/* ───────────── Scores ───────────── */
$analysis     = $analysis ?? [];
$domainScores = $domainScores ?? ($analysis['domain_scores'] ?? []);
$domainScores = is_array($domainScores) ? array_values($domainScores) : [];
$benchmark    = $benchmark ?? ($analysis['benchmark'] ?? ['global_avg' => 0, 'domain_avgs' => []]);
$bmDomains    = is_array($benchmark['domain_avgs'] ?? null) ? $benchmark['domain_avgs'] : [];

// Source unique : $analysis['global_score'] (comme results.php), repli sur le score enregistré
$globalScore = null;
if (isset($analysis['global_score'])) {
    $globalScore = (float)$analysis['global_score'];
} elseif ($assessment && isset($assessment['total_score']) && $assessment['total_score'] !== '') {
    $globalScore = (float)$assessment['total_score'];
}
if ($globalScore !== null) { $globalScore = max(0, min(100, $globalScore)); }

$levelNameFromData = '';
$levelColorFromData = null;
if (!empty($analysis['maturity_level']) && is_array($analysis['maturity_level'])) {
    $ml = $analysis['maturity_level'];
    $levelNameFromData = (string)($ml['name_fr'] ?? $ml['name'] ?? '');
    $levelColorFromData = $ml['color'] ?? null;
} elseif (!empty($assessment['maturity_level'])) {
    $ml = $assessment['maturity_level'];
    $levelNameFromData = is_array($ml) ? (string)($ml['name_fr'] ?? $ml['name'] ?? '') : (string)$ml;
}

// Palette et libellés alignés sur le tableau de bord (seuils 31 / 51 / 71 / 86)
$tone = function ($s) {
    if ($s === null) return '#7d8794';
    if ($s >= 86) return '#d97706';
    if ($s >= 71) return '#059669';
    if ($s >= 51) return '#1a56db';
    if ($s >= 31) return '#fd7e14';
    return '#6c757d';
};
$levelLabel = function ($s) {
    $k = $s >= 86 ? 'excellence' : ($s >= 71 ? 'performing' : ($s >= 51 ? 'structured' : ($s >= 31 ? 'developing' : 'beginner')));
    $t = (string)__('maturity.' . $k);
    return ($t === '' || $t === 'maturity.' . $k) ? ucfirst($k) : $t;
};
$globalColor = $tone($globalScore);
if (is_string($levelColorFromData) && preg_match('/^#[0-9a-fA-F]{6}$/', $levelColorFromData)) {
    $globalColor = $levelColorFromData; // même couleur que la page résultats
}
$globalLevel = $levelNameFromData !== '' ? $levelNameFromData : ($globalScore !== null ? $levelLabel($globalScore) : '');
$ringCirc    = 2 * M_PI * 52;
$ringOffset  = $ringCirc * (1 - ($globalScore ?? 0) / 100);

/* ───────────── Libellés des valeurs codées (alignés sur lead.php / profile.php) ───────────── */
$FL = [
    'company_size' => ['1-10'=>'1-10 employés','11-50'=>'11-50 employés','51-200'=>'51-200 employés','201-500'=>'201-500 employés','501-1000'=>'501-1000 employés','1000+'=>'Plus de 1000 employés'],
    'activity_category' => ['fabricant'=>'Fabricant de pièces','sous-traitant'=>'Sous-traitant','distributeur'=>'Distributeur','importateur'=>'Importateur','equipementier'=>'Équipementier','prestation'=>'Prestation de services'],
    'oem_tier' => ['tier1'=>'Tier 1 — Équipementier direct','tier2'=>'Tier 2 — Sous-traitant direct','tier3'=>'Tier 3 — Fournisseur de matière','aftermarket'=>'Aftermarket / Rechange','na'=>'Non applicable'],
    'product_category' => ['moteur'=>'Pièces moteur','transmission'=>'Transmission / Boîte','chassis'=>'Châssis / Suspension','carrosserie'=>'Carrosserie','interieur'=>'Intérieur / Habitacle','electric'=>'Composants électriques','plastique'=>'Pièces plastiques','metal_decoupe'=>'Métal découpé / embouti','usinage'=>'Usinage','fonderie'=>'Fonderie','caoutchouc'=>'Caoutchouc / Joints','verre'=>'Verre','autre'=>'Autre'],
    'annual_revenue' => ['<2M'=>'Moins de 2 M€','2-10M'=>'2 à 10 M€','10-50M'=>'10 à 50 M€','50-200M'=>'50 à 200 M€','>200M'=>'Plus de 200 M€'],
    'production_type' => ['unitaire'=>'Unitaire','petite_serie'=>'Petite série','grande_serie'=>'Grande série','masse'=>'Production de masse','continue'=>'Continue','projet'=>'Par projet','mass'=>'Production en série','batch'=>'Production par lots','unit'=>'Production unitaire','mixed'=>'Mixte'],
    'traceability_system' => ['aucun'=>'Aucun','manuel'=>'Manuel / papier','excel'=>'Excel / tableur','mes'=>'MES dédié','erp'=>'Intégré ERP','rfid'=>'RFID / IoT'],
    'logistics_system' => ['kanban'=>'Kanban','mrp'=>'MRP','lean'=>'Lean / Juste-à-temps','wms'=>'WMS dédié','mixte'=>'Mixte','aucun'=>'Non structuré'],
];

/* ───────────── Définition des champs : [clé, libellé, liste de libellés|null, suffixe, pleine largeur] ───────────── */
$FD = [
    'contact' => [
        ['firstname', 'Prénom', null, '', false],
        ['lastname', 'Nom', null, '', false],
        ['job_title', 'Fonction / Poste', null, '', false],
        ['email', 'Email', null, '', false],
        ['phone', 'Téléphone', null, '', false],
    ],
    'company' => [
        ['company', 'Entreprise', null, '', false],
        ['company_size', "Taille de l'entreprise", 'company_size', '', false],
        ['sector', "Secteur d'activité", null, '', false],
        ['activity_category', "Catégorie d'activité", 'activity_category', '', false],
        ['oem_tier', 'Niveau OEM', 'oem_tier', '', false],
        ['product_category', 'Catégorie de produits', 'product_category', '', false],
        ['country', 'Pays', null, '', false],
        ['founded_year', 'Année de création', null, '', false],
        ['website', 'Site web', null, '', false],
        ['annual_revenue', 'CA annuel', 'annual_revenue', '', false],
        ['export_percentage', "Part à l'export", null, '%', false],
        ['main_clients', 'Principaux clients / OEM', null, '', false],
        ['production_sites', 'Sites de production', null, '', false],
        ['workforce_production', 'Effectif production', null, '', false],
        ['workforce_engineering', 'Effectif ingénierie / R&D', null, '', false],
    ],
    'production' => [
        ['production_type', 'Type de production', 'production_type', '', false],
        ['production_capacity', 'Capacité de production', null, '', false],
        ['machine_count', 'Nombre de machines', null, '', false],
        ['machine_types', 'Types de machines', null, '', false],
        ['main_materials', 'Matières principales', null, '', false],
        ['process_technologies', 'Technologies de procédé', null, '', false],
    ],
    'quality' => [
        ['certifications', 'Certifications détenues', null, '', true],
        ['ppm_target', 'Objectif PPM', null, '', false],
        ['otd_rate', 'Taux OTD', null, '%', false],
        ['fta_rate', 'Taux FTA', null, '%', false],
        ['scrap_rate', 'Taux de rebut', null, '%', false],
    ],
    'systems' => [
        ['traceability_system', 'Système de traçabilité', 'traceability_system', '', false],
        ['logistics_system', 'Système logistique', 'logistics_system', '', false],
        ['rd_budget_percent', 'Budget R&D (% du CA)', null, '%', false],
        ['current_erp', 'ERP actuel', null, '', false],
    ],
];

/* ───────────── Fusion prospect ↔ profil ───────────── */
$isEmpty = function ($v) { return $v === null || trim((string)$v) === ''; };
$get = function ($key) use ($lead, $profileUser, $isEmpty) {
    $lv = $lead[$key] ?? null;
    $pv = $profileUser[$key] ?? null;
    $src = 'lead'; $val = $lv; $diff = null;
    if ($isEmpty($lv) && !$isEmpty($pv)) { $val = $pv; $src = 'profile'; }
    elseif (!$isEmpty($lv) && !$isEmpty($pv) && trim((string)$lv) !== trim((string)$pv)) { $diff = $pv; }
    return [$val, $src, $diff];
};
$text = function ($key, $val, $mapKey = null, $suffix = '') use ($FL) {
    $t = $mapKey ? ($FL[$mapKey][(string)$val] ?? (string)$val) : (string)$val;
    return $t . ($suffix !== '' ? ' ' . $suffix : '');
};
$renderField = function ($def) use ($get, $text, $isEmpty) {
    list($key, $label, $mapKey, $suffix, $full) = $def;
    list($val, $src, $diff) = $get($key);
    if ($isEmpty($val)) {
        $display = '<span class="f-empty">Non renseigné</span>';
    } else {
        $display = e($text($key, $val, $mapKey, $suffix));
    }
    $tags = '';
    if ($src === 'profile') {
        $tags .= ' <span class="f-tag f-tag-profile" title="Valeur issue du profil utilisateur">profil</span>';
    }
    if ($diff !== null) {
        $tags .= ' <span class="f-tag f-tag-diff" title="Valeur du profil : ' . e($text($key, $diff, $mapKey, $suffix)) . '">≠ profil</span>';
    }
    return '<div class="f-item' . ($full ? ' f-full' : '') . '"><div class="f-label">' . e($label) . '</div><div class="f-value">' . $display . $tags . '</div></div>';
};

// Écarts entre la fiche prospect et le profil utilisateur
$gaps = [];
if (!empty($profileUser)) {
    foreach ($FD as $defs) {
        foreach ($defs as $def) {
            list($val, $src, $diff) = $get($def[0]);
            if ($diff !== null) {
                $gaps[] = [$def[1], $text($def[0], $val, $def[2], $def[3]), $text($def[0], $diff, $def[2], $def[3])];
            }
        }
    }
}

/* ───────────── Champs personnalisés ───────────── */
$groupedFields = [];
foreach ($customFields as $cf) {
    $groupedFields[$cf['section'] ?? 'General'][] = $cf;
}
$sectionLabels = [
    'General' => 'Général', 'Company' => 'Entreprise', 'Industrial' => 'Industriel',
    'Certifications' => 'Certifications', 'Contact' => 'Contact', 'Engagement' => 'Engagement',
];

/* ───────────── Divers ───────────── */
$initials = strtoupper(substr((string)($lead['firstname'] ?? ''), 0, 1)) . strtoupper(substr((string)($lead['lastname'] ?? ''), 0, 1));
$fullName = trim((string)($lead['firstname'] ?? '') . ' ' . (string)($lead['lastname'] ?? ''));
$ref      = 'PRS-' . str_pad((string)($lead['id'] ?? 0), 4, '0', STR_PAD_LEFT);
$adminName = '';
try {
    $adm = \App\Helpers\Auth::user();
    $adminName = trim((string)($adm['firstname'] ?? '') . ' ' . (string)($adm['lastname'] ?? ''));
} catch (\Throwable $ex) { $adminName = ''; }
$hasConsent = array_key_exists('consent_contact', $lead) || array_key_exists('consent_share_industry', $lead);
$yesNo = function ($v) {
    if ($v === null || $v === '') return '<span class="f-empty">Non renseigné</span>';
    return in_array(strtolower((string)$v), ['1', 'yes', 'oui', 'true', 'on'], true) ? 'Oui' : 'Non';
};
$fmtDate = function ($d, $withTime = false) {
    if (empty($d)) return '—';
    $t = strtotime((string)$d);
    return $t ? date($withTime ? 'd/m/Y H:i' : 'd/m/Y', $t) : '—';
};

// Sections sélectionnables : clé => [libellé, icône, disponible ?]
$sections = [
    'score'      => ['Score global', 'fa-gauge-high', true],
    'domains'    => ['Scores détaillés par domaine', 'fa-chart-bar', true],
    'contact'    => ['Identité & contact', 'fa-address-card', true],
    'account'    => ['Profil utilisateur', 'fa-user-shield', !empty($profileUser)],
    'company'    => ['Entreprise', 'fa-building', true],
    'production' => ['Production & équipement', 'fa-industry', true],
    'quality'    => ['Qualité & performance', 'fa-clipboard-check', true],
    'systems'    => ['Systèmes & ingénierie', 'fa-laptop-code', true],
    'custom'     => ['Champs personnalisés', 'fa-sliders', !empty($groupedFields)],
    'consent'    => ['Consentements', 'fa-shield-halved', $hasConsent],
    'documents'  => ['Documents joints', 'fa-paperclip', !empty($documents)],
    'notes'      => ['Notes internes', 'fa-note-sticky', !$isEmpty($lead['notes'] ?? '')],
];
?>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

  .fiche-page {
    --f-ink: #0f172a; --f-muted: #64748b; --f-line: #e2e8f0; --f-soft: #f8fafc;
    --f-accent: #1F6FEB; --f-accent-soft: rgba(31,111,235,.09);
    font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; color: var(--f-ink);
  }
  .fiche-page *, .fiche-page *::before, .fiche-page *::after { box-sizing: border-box; }

  /* ── Barre d'outils (écran uniquement) ── */
  .fiche-toolbar { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem; }
  .fiche-toolbar h4 { font-weight: 800; font-size: 1.4rem; margin: 0; letter-spacing: -0.02em; color: var(--auto-text-primary, var(--f-ink)); }
  .fiche-toolbar-actions { display: flex; gap: 0.6rem; flex-wrap: wrap; }
  .f-btn {
    display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.15rem; border-radius: 0.7rem;
    font: 700 0.78rem 'Plus Jakarta Sans', sans-serif; text-decoration: none; cursor: pointer;
    border: 1px solid var(--f-line); background: #fff; color: #334155;
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, filter .18s ease;
  }
  .f-btn:hover { transform: translateY(-2px); border-color: var(--f-accent); color: var(--f-accent); box-shadow: 0 8px 18px -10px rgba(31,111,235,.5); }
  .f-btn:focus-visible { outline: none; box-shadow: 0 0 0 4px var(--f-accent-soft), 0 0 0 1px var(--f-accent); }
  .f-btn-primary { color: #fff; border-color: transparent; background: linear-gradient(135deg, #1F6FEB, #3b82f6); box-shadow: 0 8px 20px -8px rgba(31,111,235,.7); }
  .f-btn-primary:hover { color: #fff; filter: brightness(1.07); box-shadow: 0 12px 26px -8px rgba(31,111,235,.75); }

  /* ── Sélecteur de contenu ── */
  .fiche-picker { background: #fff; border: 1px solid var(--f-line); border-radius: 1rem; margin-bottom: 1.25rem; box-shadow: 0 4px 16px rgba(80,64,42,.06); }
  .fiche-picker > summary { list-style: none; cursor: pointer; display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.9rem 1.15rem; font-weight: 700; font-size: 0.85rem; }
  .fiche-picker > summary::-webkit-details-marker { display: none; }
  .fiche-picker > summary .f-count { font-weight: 600; font-size: 0.72rem; color: var(--f-muted); background: var(--f-soft); border: 1px solid var(--f-line); border-radius: 999px; padding: 0.2rem 0.7rem; }
  .fiche-picker-body { padding: 0 1.15rem 1.1rem; border-top: 1px solid var(--f-line); }
  .fiche-presets { display: flex; gap: 0.5rem; flex-wrap: wrap; margin: 0.9rem 0; }
  .f-chip-btn { font: 600 0.72rem 'Plus Jakarta Sans', sans-serif; padding: 0.4rem 0.85rem; border-radius: 999px; border: 1px dashed #94a3b8; background: transparent; color: #475569; cursor: pointer; }
  .f-chip-btn:hover { border-color: var(--f-accent); color: var(--f-accent); }
  .fiche-picker-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 0.5rem; }
  .f-check { display: flex; align-items: center; gap: 0.6rem; padding: 0.55rem 0.8rem; border: 1.5px solid var(--f-line); border-radius: 0.75rem; cursor: pointer; font-size: 0.78rem; font-weight: 600; background: #fff; transition: border-color .15s ease, background .15s ease; user-select: none; }
  .f-check:hover { border-color: var(--f-accent); }
  .f-check input { accent-color: var(--f-accent); width: 1rem; height: 1rem; margin: 0; }
  .f-check i { color: var(--f-accent); width: 1rem; text-align: center; }
  .f-check.is-on { border-color: var(--f-accent); background: var(--f-accent-soft); }
  .f-check.is-disabled { opacity: .45; cursor: not-allowed; }
  .fiche-picker-sub { margin-top: 1rem; }
  .fiche-picker-sub h6 { font-size: 0.7rem; text-transform: uppercase; letter-spacing: .06em; color: var(--f-muted); margin: 0 0 0.5rem; font-weight: 700; }

  /* ── La fiche (aperçu fidèle de l'impression) ── */
  .fiche-sheet { background: #fff; border: 1px solid var(--f-line); border-radius: 1.1rem; padding: 1.75rem; box-shadow: 0 10px 30px -12px rgba(15,23,42,.18); max-width: 1020px; margin: 0 auto 1.5rem; }
  .f-hidden { display: none !important; }

  .f-head { display: flex; align-items: center; gap: 1.1rem; padding-bottom: 1.1rem; border-bottom: 3px solid var(--f-accent); margin-bottom: 1.1rem; }
  .f-avatar { flex: 0 0 auto; width: 64px; height: 64px; border-radius: 16px; display: grid; place-items: center; color: #fff; font-weight: 800; font-size: 1.4rem; background: linear-gradient(135deg, #1F6FEB, #4f46e5); }
  .f-head-main { flex: 1 1 auto; min-width: 0; }
  .f-head-main h1 { margin: 0; font-size: 1.45rem; font-weight: 800; letter-spacing: -0.02em; line-height: 1.2; color: var(--f-ink); }
  .f-head-sub { margin: 0.2rem 0 0.5rem; font-size: 0.82rem; color: var(--f-muted); }
  .f-badges { display: flex; flex-wrap: wrap; gap: 0.35rem; }
  .f-badge { padding: 0.22rem 0.65rem; border-radius: 999px; font-size: 0.68rem; font-weight: 700; background: var(--f-accent-soft); color: var(--f-accent); }
  .f-head-meta { flex: 0 0 auto; text-align: right; font-size: 0.7rem; color: var(--f-muted); line-height: 1.6; }
  .f-head-meta strong { display: block; font-size: 0.95rem; color: var(--f-ink); letter-spacing: .04em; }
  .f-brand { font-size: 0.62rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; color: var(--f-accent); }

  .f-top { display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem; }
  .f-card { background: #fff; border: 1px solid var(--f-line); border-radius: 0.9rem; padding: 1.1rem 1.2rem; }
  .f-card-title { display: flex; align-items: center; gap: 0.5rem; margin: 0 0 0.85rem; padding-bottom: 0.6rem; border-bottom: 1px solid var(--f-line); font-size: 0.8rem; font-weight: 800; color: var(--f-ink); text-transform: uppercase; letter-spacing: .04em; }
  .f-card-title i { color: var(--f-accent); font-size: 0.85rem; }

  .f-score { flex: 0 0 230px; text-align: center; }
  .f-ring { position: relative; width: 132px; height: 132px; margin: 0.2rem auto 0.6rem; }
  .f-ring svg { width: 100%; height: 100%; transform: rotate(-90deg); display: block; }
  .f-ring .bg { fill: none; stroke: #e8edf4; stroke-width: 10; }
  .f-ring .fg { fill: none; stroke-width: 10; stroke-linecap: round; }
  .f-ring-num { position: absolute; inset: 0; display: grid; place-items: center; font-size: 1.9rem; font-weight: 800; letter-spacing: -0.03em; }
  .f-score-label { font-size: 0.66rem; text-transform: uppercase; letter-spacing: .08em; color: var(--f-muted); font-weight: 700; }
  .f-level { display: inline-block; margin-top: 0.55rem; padding: 0.3rem 0.85rem; border-radius: 999px; font-size: 0.74rem; font-weight: 800; }
  .f-assess-meta { margin-top: 0.8rem; padding-top: 0.7rem; border-top: 1px dashed var(--f-line); font-size: 0.7rem; color: var(--f-muted); line-height: 1.7; text-align: left; }
  .f-assess-meta b { color: var(--f-ink); font-weight: 700; }
  .f-noscore { font-size: 0.8rem; color: var(--f-muted); font-style: italic; padding: 1rem 0; }

  .f-domains { flex: 1 1 380px; min-width: 0; }
  .f-dom { padding: 0.5rem 0; border-bottom: 1px solid #f1f5f9; }
  .f-dom:last-child { border-bottom: none; }
  .f-dom-head { display: flex; justify-content: space-between; align-items: baseline; gap: 0.75rem; margin-bottom: 0.3rem; font-size: 0.8rem; }
  .f-dom-name { font-weight: 600; min-width: 0; }
  .f-dom-pct { font-weight: 800; font-variant-numeric: tabular-nums; white-space: nowrap; }
  .f-dom-pct small { font-weight: 600; font-size: 0.64rem; color: var(--f-muted); margin-left: 0.35rem; }
  .f-bar { position: relative; height: 9px; border-radius: 999px; background: #e8edf4; overflow: visible; }
  .f-bar-fill { height: 100%; border-radius: 999px; }
  .f-bar-bm { position: absolute; top: -3px; width: 2px; height: 15px; background: #0f172a; opacity: .55; border-radius: 1px; }
  .f-legend { margin-top: 0.6rem; font-size: 0.64rem; color: var(--f-muted); display: flex; align-items: center; gap: 0.4rem; }
  .f-legend i { display: inline-block; width: 2px; height: 11px; background: #0f172a; opacity: .55; }

  .f-body { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; grid-auto-flow: dense; }
  .f-body .f-wide { grid-column: 1 / -1; }
  .f-grid { display: grid; grid-template-columns: 1fr; gap: 0.7rem 1rem; }
  .f-wide .f-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .f-item { min-width: 0; }
  .f-item.f-full { grid-column: 1 / -1; }
  .f-label { font-size: 0.62rem; text-transform: uppercase; letter-spacing: .06em; color: var(--f-muted); font-weight: 700; margin-bottom: 0.15rem; }
  .f-value { font-size: 0.84rem; font-weight: 600; color: var(--f-ink); overflow-wrap: anywhere; }
  .f-empty { color: #94a3b8; font-weight: 400; font-style: italic; }
  .f-tag { display: inline-block; margin-left: 0.3rem; padding: 0.05rem 0.45rem; border-radius: 999px; font-size: 0.58rem; font-weight: 700; vertical-align: middle; }
  .f-tag-profile { background: rgba(5,150,105,.12); color: #047857; }
  .f-tag-diff { background: rgba(253,126,20,.14); color: #c2570c; }

  .f-gaps { margin-top: 0.9rem; padding: 0.7rem 0.85rem; border-radius: 0.7rem; background: rgba(253,126,20,.08); border: 1px solid rgba(253,126,20,.25); font-size: 0.74rem; }
  .f-gaps b { display: block; margin-bottom: 0.35rem; color: #c2570c; }
  .f-gaps ul { margin: 0; padding-left: 1.1rem; }
  .f-gaps li { margin: 0.15rem 0; }

  .f-doc { display: flex; justify-content: space-between; align-items: center; gap: 0.75rem; padding: 0.5rem 0; border-bottom: 1px solid #f1f5f9; font-size: 0.8rem; }
  .f-doc:last-child { border-bottom: none; }
  .f-doc small { color: var(--f-muted); }
  .f-notes { font-size: 0.84rem; line-height: 1.6; white-space: normal; }

  .f-foot { margin-top: 1.25rem; padding-top: 0.75rem; border-top: 1px solid var(--f-line); display: flex; justify-content: space-between; gap: 1rem; flex-wrap: wrap; font-size: 0.64rem; color: var(--f-muted); }

  /* ── Modification (écran uniquement) ── */
  .fiche-edit { max-width: 1020px; margin: 0 auto 2rem; background: #fff; border: 1px solid var(--f-line); border-radius: 1.1rem; padding: 1.5rem; box-shadow: 0 4px 16px rgba(80,64,42,.06); }
  .fiche-edit h5 { margin: 0 0 1rem; font-size: 0.9rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem; }
  .fiche-edit h5 i { color: var(--f-accent); }
  .fiche-form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
  .fiche-form-grid .full { grid-column: 1 / -1; }
  .fiche-form-group label { display: block; font-size: 0.72rem; font-weight: 600; color: #475569; margin-bottom: 0.35rem; }
  .fiche-form-group label .req { color: #dc2626; }
  .fiche-form-group input, .fiche-form-group select, .fiche-form-group textarea {
    width: 100%; padding: 0.55rem 0.85rem; font: 500 0.8rem 'Plus Jakarta Sans', sans-serif;
    border-radius: 8px; border: 1px solid var(--vx-input-border, #cbd5e1); background: var(--vx-input-bg, #fff); color: var(--f-ink);
  }
  .fiche-form-group input:focus, .fiche-form-group select:focus, .fiche-form-group textarea:focus { border-color: var(--f-accent); outline: none; box-shadow: 0 0 0 3px var(--f-accent-soft); }
  .fiche-form-actions { display: flex; gap: 0.75rem; margin-top: 1.25rem; padding-top: 1.1rem; border-top: 1px solid var(--f-line); }
  .fiche-section-divider { border: none; border-top: 1px solid var(--f-line); margin: 1.25rem 0; }

  @media (max-width: 900px) {
    .f-body { grid-template-columns: 1fr; }
    .f-wide .f-grid { grid-template-columns: 1fr; }
    .f-head { flex-wrap: wrap; }
    .f-head-meta { text-align: left; }
    .f-score { flex: 1 1 100%; }
    .fiche-form-grid { grid-template-columns: 1fr; }
  }

  /* ══════════ IMPRESSION (A4) ══════════ */
  @page { size: A4; margin: 12mm 12mm 14mm; }
  @media print {
    html, body { background: #fff !important; height: auto !important; overflow: visible !important; }
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    .fiche-ph { display: none !important; }
    .fiche-pa { margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: none !important; min-height: 0 !important; height: auto !important;
                position: static !important; display: block !important; overflow: visible !important; transform: none !important;
                background: #fff !important; border: 0 !important; box-shadow: none !important; }
    .fiche-toolbar, .fiche-picker, .fiche-edit, .f-tag, .no-print { display: none !important; }
    .fiche-sheet { box-shadow: none !important; border: 0 !important; border-radius: 0 !important; padding: 0 !important; max-width: none !important; margin: 0 !important; }
    .f-head, .f-card, .f-dom, .f-doc, .f-gaps { break-inside: avoid; page-break-inside: avoid; }
    .f-card { box-shadow: none !important; }
    .f-head { border-bottom-width: 2px; }
    .f-value, .f-label { orphans: 2; widows: 2; }
    a { color: inherit !important; text-decoration: none !important; }
    .f-foot { margin-top: 6mm; }
  }
</style>

<div class="fiche-page" id="fichePage">

  <!-- Barre d'outils -->
  <div class="fiche-toolbar">
    <h4><i class="fas fa-id-card me-2" style="color:var(--f-accent);"></i>Fiche Prospect</h4>
    <div class="fiche-toolbar-actions">
      <a href="/admin/leads" class="f-btn"><i class="fas fa-arrow-left"></i> Retour à la liste</a>
      <button type="button" class="f-btn f-btn-primary" id="ficheBtnPrint"><i class="fas fa-print"></i> Imprimer la fiche</button>
    </div>
  </div>

  <!-- Sélecteur : que faut-il afficher / imprimer ? -->
  <details class="fiche-picker" id="fichePicker" open>
    <summary>
      <span><i class="fas fa-list-check" style="color:var(--f-accent);margin-right:.5rem;"></i>Contenu de la fiche</span>
      <span class="f-count" id="ficheSelCount"></span>
    </summary>
    <div class="fiche-picker-body">
      <div class="fiche-presets">
        <button type="button" class="f-chip-btn" data-preset="all"><i class="fas fa-check-double"></i> Tout afficher</button>
        <button type="button" class="f-chip-btn" data-preset="summary"><i class="fas fa-bolt"></i> Synthèse (contact, entreprise, scores)</button>
        <button type="button" class="f-chip-btn" data-preset="scores"><i class="fas fa-chart-pie"></i> Scores uniquement</button>
        <button type="button" class="f-chip-btn" data-preset="none"><i class="fas fa-xmark"></i> Tout masquer</button>
      </div>
      <div class="fiche-picker-grid">
        <?php foreach ($sections as $key => $meta): ?>
          <label class="f-check<?= $meta[2] ? '' : ' is-disabled' ?>" title="<?= $meta[2] ? '' : 'Aucune donnée disponible' ?>">
            <input type="checkbox" data-sec-toggle="<?= e($key) ?>" <?= $meta[2] ? 'checked' : 'disabled' ?>>
            <i class="fas <?= e($meta[1]) ?>"></i> <?= e($meta[0]) ?>
          </label>
        <?php endforeach; ?>
      </div>
      <?php if (!empty($domainScores)): ?>
        <div class="fiche-picker-sub">
          <h6>Domaines d'évaluation à afficher</h6>
          <div class="fiche-picker-grid">
            <?php foreach ($domainScores as $i => $ds):
              $dLabel = (string)($ds['domain_label'] ?? $ds['domain_name_fr'] ?? $ds['domain_name'] ?? ('Domaine ' . ($i + 1)));
              $dId = (string)($ds['domain_id'] ?? $i);
            ?>
              <label class="f-check">
                <input type="checkbox" data-domain-toggle="<?= e($dId) ?>" checked>
                <?= e($dLabel) ?> <span style="margin-left:auto;font-weight:800;color:<?= $tone((float)($ds['percent_score'] ?? 0)) ?>;"><?= round((float)($ds['percent_score'] ?? 0)) ?>%</span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </details>

  <!-- ═════════════ LA FICHE ═════════════ -->
  <article class="fiche-sheet" id="ficheSheet">

    <header class="f-head">
      <div class="f-avatar"><?= e($initials ?: '?') ?></div>
      <div class="f-head-main">
        <div class="f-brand">AQMI · Fiche prospect</div>
        <h1><?= e($fullName !== '' ? $fullName : 'Prospect') ?></h1>
        <p class="f-head-sub">
          <?= e((string)($lead['job_title'] ?? '') ?: 'Fonction non renseignée') ?>
          <?php if (!$isEmpty($lead['company'] ?? '')): ?> · <strong><?= e((string)$lead['company']) ?></strong><?php endif; ?>
        </p>
        <div class="f-badges">
          <?php if (!$isEmpty($lead['sector'] ?? '')): ?><span class="f-badge"><?= e((string)$lead['sector']) ?></span><?php endif; ?>
          <?php if (!$isEmpty($lead['country'] ?? '')): ?><span class="f-badge"><?= e((string)$lead['country']) ?></span><?php endif; ?>
          <?php if (!$isEmpty($lead['oem_tier'] ?? '')): ?><span class="f-badge"><?= e($text('oem_tier', $lead['oem_tier'], 'oem_tier')) ?></span><?php endif; ?>
        </div>
      </div>
      <div class="f-head-meta">
        <strong><?= e($ref) ?></strong>
        Inscrit le <?= e($fmtDate($lead['created_at'] ?? null)) ?><br>
        Édité le <?= date('d/m/Y') ?>
      </div>
    </header>

    <!-- Scores -->
    <div class="f-top">
      <section class="f-card f-score" data-sec="score">
        <h3 class="f-card-title"><i class="fas fa-gauge-high"></i> Score global</h3>
        <?php if ($globalScore !== null): ?>
          <div class="f-ring">
            <svg viewBox="0 0 120 120" aria-hidden="true">
              <circle class="bg" cx="60" cy="60" r="52"/>
              <circle class="fg" cx="60" cy="60" r="52" stroke="<?= $globalColor ?>" stroke-dasharray="<?= round($ringCirc, 2) ?>" stroke-dashoffset="<?= round($ringOffset, 2) ?>"/>
            </svg>
            <div class="f-ring-num" style="color:<?= $globalColor ?>;"><?= round($globalScore, 1) ?>%</div>
          </div>
          <div class="f-score-label">Score AQMI</div>
          <?php if ($globalLevel !== ''): ?>
            <div class="f-level" style="background:<?= $globalColor ?>1f;color:<?= $globalColor ?>;"><?= e($globalLevel) ?></div>
          <?php endif; ?>
          <div class="f-assess-meta">
            <?php if (!empty($assessment['model_name'] ?? '')): ?><div>Modèle : <b><?= e((string)$assessment['model_name']) ?></b></div><?php endif; ?>
            <?php if (!empty($assessment['id'] ?? '')): ?><div>Évaluation : <b>#<?= e((string)$assessment['id']) ?></b></div><?php endif; ?>
            <?php $aDate = $assessment['completed_at'] ?? ($assessment['created_at'] ?? null); if (!empty($aDate)): ?><div>Date : <b><?= e($fmtDate($aDate)) ?></b></div><?php endif; ?>
          </div>
        <?php else: ?>
          <div class="f-noscore">Aucune évaluation terminée pour ce prospect.</div>
        <?php endif; ?>
      </section>

      <section class="f-card f-domains" data-sec="domains">
        <h3 class="f-card-title"><i class="fas fa-chart-bar"></i> Scores détaillés par domaine</h3>
        <?php if (!empty($domainScores)): ?>
          <?php foreach ($domainScores as $i => $ds):
            $pct = max(0, min(100, (float)($ds['percent_score'] ?? 0)));
            $dLabel = (string)($ds['domain_label'] ?? $ds['domain_name_fr'] ?? $ds['domain_name'] ?? ('Domaine ' . ($i + 1)));
            $dId = (string)($ds['domain_id'] ?? $i);
            $dColor = $tone($pct);
            $bm = (float)($bmDomains[$ds['domain_id'] ?? null] ?? 0);
          ?>
            <div class="f-dom" data-domain="<?= e($dId) ?>">
              <div class="f-dom-head">
                <span class="f-dom-name"><?= e($dLabel) ?></span>
                <span class="f-dom-pct" style="color:<?= $dColor ?>;"><?= round($pct) ?>%<small><?= e($levelLabel($pct)) ?></small></span>
              </div>
              <div class="f-bar">
                <div class="f-bar-fill" style="width:<?= round($pct, 1) ?>%;background:<?= $dColor ?>;"></div>
                <?php if ($bm > 0): ?><span class="f-bar-bm" style="left:calc(<?= round(min(100, $bm), 1) ?>% - 1px);" title="Moyenne du marché : <?= round($bm) ?>%"></span><?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
          <?php if (!empty($bmDomains)): ?><div class="f-legend"><i></i> Moyenne du marché</div><?php endif; ?>
        <?php else: ?>
          <div class="f-noscore">Le détail par domaine n'est pas disponible.</div>
          <div class="f-noscore no-print" style="font-style:normal;font-size:.7rem;">
            Aucune donnée d'évaluation calculée pour ce prospect (évaluation absente ou non terminée).
          </div>
        <?php endif; ?>
      </section>
    </div>

    <!-- Données -->
    <div class="f-body">

      <section class="f-card" data-sec="contact">
        <h3 class="f-card-title"><i class="fas fa-address-card"></i> Identité &amp; contact</h3>
        <div class="f-grid">
          <?php foreach ($FD['contact'] as $def) { echo $renderField($def); } ?>
          <div class="f-item"><div class="f-label">Inscrit le</div><div class="f-value"><?= e($fmtDate($lead['created_at'] ?? null)) ?></div></div>
        </div>
      </section>

      <?php if (!empty($profileUser)): ?>
      <section class="f-card" data-sec="account">
        <h3 class="f-card-title"><i class="fas fa-user-shield"></i> Profil utilisateur</h3>
        <div class="f-grid">
          <?php if (isset($profileUser['id'])): ?>
            <div class="f-item"><div class="f-label">Identifiant compte</div><div class="f-value">#<?= e((string)$profileUser['id']) ?></div></div>
          <?php endif; ?>
          <div class="f-item"><div class="f-label">Email du compte</div><div class="f-value"><?= e((string)($profileUser['email'] ?? '—')) ?></div></div>
          <div class="f-item"><div class="f-label">Membre depuis</div><div class="f-value"><?= e($fmtDate($profileUser['created_at'] ?? null)) ?></div></div>
          <div class="f-item"><div class="f-label">Dernière connexion</div><div class="f-value"><?= e($fmtDate($profileUser['last_login_at'] ?? null, true)) ?></div></div>
          <?php if (array_key_exists('is_active', $profileUser) || array_key_exists('status', $profileUser)):
            $st = $profileUser['is_active'] ?? $profileUser['status'];
            $active = in_array(strtolower((string)$st), ['1', 'active', 'actif', 'true'], true);
          ?>
            <div class="f-item"><div class="f-label">Statut du compte</div><div class="f-value" style="color:<?= $active ? '#047857' : '#b91c1c' ?>;"><?= $active ? 'Actif' : 'Inactif' ?></div></div>
          <?php endif; ?>
        </div>
        <?php if (!empty($gaps)): ?>
          <div class="f-gaps">
            <b><i class="fas fa-triangle-exclamation"></i> Écarts entre la fiche prospect et le profil (<?= count($gaps) ?>)</b>
            <ul>
              <?php foreach ($gaps as $g): ?>
                <li><?= e($g[0]) ?> — prospect : <strong><?= e($g[1]) ?></strong> · profil : <strong><?= e($g[2]) ?></strong></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
      </section>
      <?php endif; ?>

      <section class="f-card f-wide" data-sec="company">
        <h3 class="f-card-title"><i class="fas fa-building"></i> Entreprise</h3>
        <div class="f-grid">
          <?php foreach ($FD['company'] as $def) { echo $renderField($def); } ?>
        </div>
      </section>

      <section class="f-card" data-sec="production">
        <h3 class="f-card-title"><i class="fas fa-industry"></i> Production &amp; équipement</h3>
        <div class="f-grid">
          <?php foreach ($FD['production'] as $def) { echo $renderField($def); } ?>
        </div>
      </section>

      <section class="f-card" data-sec="quality">
        <h3 class="f-card-title"><i class="fas fa-clipboard-check"></i> Qualité &amp; performance</h3>
        <div class="f-grid">
          <?php foreach ($FD['quality'] as $def) { echo $renderField($def); } ?>
        </div>
      </section>

      <section class="f-card f-wide" data-sec="systems">
        <h3 class="f-card-title"><i class="fas fa-laptop-code"></i> Systèmes &amp; ingénierie</h3>
        <div class="f-grid">
          <?php foreach ($FD['systems'] as $def) { echo $renderField($def); } ?>
        </div>
      </section>

      <?php if (!empty($groupedFields)): ?>
      <section class="f-card f-wide" data-sec="custom">
        <h3 class="f-card-title"><i class="fas fa-sliders"></i> Champs personnalisés</h3>
        <?php foreach ($groupedFields as $section => $fields): ?>
          <div style="margin-bottom:.9rem;">
            <div class="f-label" style="color:var(--f-accent);font-size:.68rem;margin-bottom:.5rem;"><?= e($sectionLabels[ucfirst((string)$section)] ?? (string)$section) ?></div>
            <div class="f-grid">
              <?php foreach ($fields as $cf):
                $val = $cf['value'] ?? '';
                $display = '';
                if ($cf['field_type'] === 'multiselect' && $val) {
                    $decoded = json_decode($val, true);
                    $display = e(is_array($decoded) ? implode(', ', $decoded) : (string)$val);
                } elseif ($cf['field_type'] === 'file' && $val) {
                    $display = '<a href="/storage/leads/' . e((string)$lead['id']) . '/' . e((string)$val) . '" target="_blank" style="color:var(--f-accent);font-weight:600;text-decoration:none;"><i class="fas fa-download"></i> Voir le fichier</a>';
                } else {
                    $display = e((string)$val);
                }
              ?>
                <div class="f-item">
                  <div class="f-label"><?= e((string)($cf['label_fr'] ?: $cf['label'])) ?></div>
                  <div class="f-value"><?= $display !== '' ? $display : '<span class="f-empty">Non renseigné</span>' ?></div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </section>
      <?php endif; ?>

      <?php if ($hasConsent): ?>
      <section class="f-card" data-sec="consent">
        <h3 class="f-card-title"><i class="fas fa-shield-halved"></i> Consentements</h3>
        <div class="f-grid">
          <div class="f-item"><div class="f-label">Être contacté au sujet du rapport</div><div class="f-value"><?= $yesNo($lead['consent_contact'] ?? null) ?></div></div>
          <div class="f-item"><div class="f-label">Partage du score avec des industriels</div><div class="f-value"><?= $yesNo($lead['consent_share_industry'] ?? null) ?></div></div>
        </div>
      </section>
      <?php endif; ?>

      <?php if (!empty($documents)): ?>
      <section class="f-card f-wide" data-sec="documents">
        <h3 class="f-card-title"><i class="fas fa-paperclip"></i> Documents joints</h3>
        <?php foreach ($documents as $doc): ?>
          <div class="f-doc">
            <div>
              <strong><?= e((string)$doc['original_name']) ?></strong>
              <small> — <?= round(((float)$doc['file_size']) / 1024) ?> Ko</small>
            </div>
            <a href="/storage/leads/<?= e((string)$lead['id']) ?>/<?= e((string)$doc['filename']) ?>" class="f-btn no-print" download><i class="fas fa-download"></i> Télécharger</a>
          </div>
        <?php endforeach; ?>
      </section>
      <?php endif; ?>

      <?php if (!$isEmpty($lead['notes'] ?? '')): ?>
      <section class="f-card f-wide" data-sec="notes">
        <h3 class="f-card-title"><i class="fas fa-note-sticky"></i> Notes internes</h3>
        <div class="f-notes"><?= nl2br(e((string)$lead['notes'])) ?></div>
      </section>
      <?php endif; ?>

    </div>

    <footer class="f-foot">
      <span>Document confidentiel — AQMI · <?= e($ref) ?></span>
      <span>Imprimé le <?= date('d/m/Y à H:i') ?><?= $adminName !== '' ? ' par ' . e($adminName) : '' ?></span>
    </footer>
  </article>

  <!-- ═════════════ Modification (non imprimée) ═════════════ -->
  <div class="fiche-edit">
    <h5><i class="fas fa-edit"></i> Modifier les informations</h5>
    <form method="POST" action="/admin/leads/save-fields/<?= e((string)$lead['id']) ?>" enctype="multipart/form-data" id="leadEditForm" novalidate>
      <?= csrf_field() ?>

      <div class="fiche-form-grid">
        <div class="fiche-form-group">
          <label>Taille de l'entreprise <span class="req">*</span></label>
          <select name="company_size" required>
            <option value="">Sélectionnez...</option>
            <?php foreach ($FL['company_size'] as $v => $l): ?>
              <option value="<?= e((string)$v) ?>" <?= ((string)($lead['company_size'] ?? '') === (string)$v) ? 'selected' : '' ?>><?= e($l) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="fiche-form-group">
          <label>Site web <span class="req">*</span></label>
          <input type="url" name="website" placeholder="https://www.example.com" value="<?= e((string)($lead['website'] ?? '')) ?>" required>
        </div>
        <div class="fiche-form-group">
          <label>Année de création <span class="req">*</span></label>
          <input type="number" name="founded_year" placeholder="Ex: 2010" min="1900" max="2030" value="<?= e((string)($lead['founded_year'] ?? '')) ?>" required>
        </div>
        <div class="fiche-form-group">
          <label>Type de production <span class="req">*</span></label>
          <select name="production_type" required>
            <option value="">Sélectionnez...</option>
            <?php
              $curPt = (string)($lead['production_type'] ?? '');
              foreach ($FL['production_type'] as $v => $l): ?>
              <option value="<?= e((string)$v) ?>" <?= ($curPt === (string)$v) ? 'selected' : '' ?>><?= e($l) ?></option>
            <?php endforeach; ?>
            <?php if ($curPt !== '' && !isset($FL['production_type'][$curPt])): ?>
              <option value="<?= e($curPt) ?>" selected><?= e($curPt) ?></option>
            <?php endif; ?>
          </select>
        </div>
        <div class="fiche-form-group full">
          <label>Certifications <span class="req">*</span></label>
          <input type="text" name="certifications" placeholder="Ex: ISO 9001, IATF 16949, ISO 14001" value="<?= e((string)($lead['certifications'] ?? '')) ?>" required>
        </div>
        <div class="fiche-form-group full">
          <label>Notes</label>
          <textarea name="notes" rows="3" placeholder="Notes internes sur ce prospect..."><?= e((string)($lead['notes'] ?? '')) ?></textarea>
        </div>
      </div>

      <?php if (!empty($customFields)): ?>
      <hr class="fiche-section-divider">
      <h5 style="font-size:.8rem;"><i class="fas fa-sliders-h"></i> Champs personnalisés</h5>
      <div class="fiche-form-grid">
        <?php foreach ($customFields as $cf): ?>
          <?php
          $val = $cf['value'] ?? '';
          $fieldName = 'custom_fields[' . $cf['field_id'] . ']';
          $fieldId = 'cf_' . $cf['field_id'];
          $placeholder = $cf['placeholder_fr'] ?: $cf['placeholder'] ?: '';
          $required = $cf['is_required'] ? 'required' : '';
          ?>
          <div class="fiche-form-group">
            <label for="<?= $fieldId ?>">
              <?= e((string)($cf['label_fr'] ?: $cf['label'])) ?>
              <?php if ($cf['is_required']): ?><span class="req">*</span><?php endif; ?>
            </label>
            <?php if ($cf['field_type'] === 'text'): ?>
              <input type="text" name="<?= $fieldName ?>" id="<?= $fieldId ?>" placeholder="<?= e($placeholder) ?>" value="<?= e((string)$val) ?>" <?= $required ?>>
            <?php elseif ($cf['field_type'] === 'textarea'): ?>
              <textarea name="<?= $fieldName ?>" id="<?= $fieldId ?>" rows="2" placeholder="<?= e($placeholder) ?>" <?= $required ?>><?= e((string)$val) ?></textarea>
            <?php elseif ($cf['field_type'] === 'number'): ?>
              <input type="number" name="<?= $fieldName ?>" id="<?= $fieldId ?>" placeholder="<?= e($placeholder) ?>" value="<?= e((string)$val) ?>" <?= $required ?>>
            <?php elseif ($cf['field_type'] === 'phone'): ?>
              <input type="tel" name="<?= $fieldName ?>" id="<?= $fieldId ?>" placeholder="<?= e($placeholder) ?>" value="<?= e((string)$val) ?>" <?= $required ?>>
            <?php elseif ($cf['field_type'] === 'date'): ?>
              <input type="date" name="<?= $fieldName ?>" id="<?= $fieldId ?>" value="<?= e((string)$val) ?>" <?= $required ?>>
            <?php elseif ($cf['field_type'] === 'select'): ?>
              <select name="<?= $fieldName ?>" id="<?= $fieldId ?>" <?= $required ?>>
                <option value=""><?= e($placeholder ?: 'Sélectionnez...') ?></option>
                <?php $opts = json_decode($cf['options'] ?? '[]', true) ?: []; ?>
                <?php foreach ($opts as $opt): ?>
                  <option value="<?= e((string)$opt['value']) ?>" <?= $val === $opt['value'] ? 'selected' : '' ?>><?= e((string)$opt['label']) ?></option>
                <?php endforeach; ?>
              </select>
            <?php elseif ($cf['field_type'] === 'multiselect'): ?>
              <?php $selectedVals = json_decode($val, true) ?: []; ?>
              <div style="max-height:120px;overflow-y:auto;border:1px solid var(--vx-input-border,#cbd5e1);border-radius:8px;padding:0.5rem;">
                <?php $opts = json_decode($cf['options'] ?? '[]', true) ?: []; ?>
                <?php foreach ($opts as $i => $opt): ?>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="<?= $fieldName ?>[]" value="<?= e((string)$opt['value']) ?>" id="<?= $fieldId . '_' . $i ?>"
                      <?= in_array($opt['value'], $selectedVals) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="<?= $fieldId . '_' . $i ?>" style="font-size:0.8rem;"><?= e((string)$opt['label']) ?></label>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php elseif ($cf['field_type'] === 'file'): ?>
              <input type="file" name="documents[]" <?= $cf['is_required'] && !$val ? 'required' : '' ?>>
              <input type="hidden" name="doc_field_id" value="<?= e((string)$cf['field_id']) ?>">
              <?php if ($val): ?>
                <small style="color:var(--f-muted);font-size:0.7rem;">Fichier actuel : <?= e((string)$val) ?></small>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div class="fiche-form-actions">
        <button type="submit" class="f-btn f-btn-primary"><i class="fas fa-floppy-disk"></i> Enregistrer</button>
        <a href="/admin/leads" class="f-btn"><i class="fas fa-arrow-left"></i> Annuler</a>
      </div>
    </form>
  </div>
</div>

<script>
(function () {
  var root = document.getElementById('fichePage');
  if (!root) return;

  /* ── 1) Mise en page d'impression : masque tout ce qui entoure la fiche, quel que soit le layout ── */
  (function prepareForPrint() {
    var node = root;
    while (node && node !== document.body && node.parentElement) {
      var parent = node.parentElement;
      for (var i = 0; i < parent.children.length; i++) {
        var c = parent.children[i];
        if (c === node) continue;
        var tag = c.tagName;
        if (tag === 'SCRIPT' || tag === 'STYLE' || tag === 'LINK' || tag === 'TEMPLATE') continue;
        c.classList.add('fiche-ph');
      }
      parent.classList.add('fiche-pa');
      node = parent;
    }
  })();

  /* ── 2) Sélection des sections / domaines ── */
  var KEY = 'aqmiFicheSelection:v1';
  var secInputs = [].slice.call(root.querySelectorAll('[data-sec-toggle]'));
  var domInputs = [].slice.call(root.querySelectorAll('[data-domain-toggle]'));
  var counter = document.getElementById('ficheSelCount');

  function readSaved() {
    try { return JSON.parse(localStorage.getItem(KEY) || 'null'); } catch (e) { return null; }
  }
  function writeSaved() {
    var s = { sections: {}, domains: {} };
    secInputs.forEach(function (i) { if (!i.disabled) s.sections[i.getAttribute('data-sec-toggle')] = i.checked; });
    domInputs.forEach(function (i) { s.domains[i.getAttribute('data-domain-toggle')] = i.checked; });
    try { localStorage.setItem(KEY, JSON.stringify(s)); } catch (e) {}
  }
  function apply() {
    var on = 0, total = 0;
    secInputs.forEach(function (i) {
      var key = i.getAttribute('data-sec-toggle');
      var show = !i.disabled && i.checked;
      if (!i.disabled) { total++; if (i.checked) on++; }
      [].forEach.call(root.querySelectorAll('[data-sec="' + key + '"]'), function (el) { el.classList.toggle('f-hidden', !show); });
      i.parentNode.classList.toggle('is-on', show);
    });
    domInputs.forEach(function (i) {
      var id = i.getAttribute('data-domain-toggle');
      [].forEach.call(root.querySelectorAll('[data-domain="' + id.replace(/"/g, '\\"') + '"]'), function (el) { el.classList.toggle('f-hidden', !i.checked); });
      i.parentNode.classList.toggle('is-on', i.checked);
    });
    if (counter) counter.textContent = on + ' / ' + total + ' sections';
  }
  function setSections(keys) {
    secInputs.forEach(function (i) {
      if (i.disabled) return;
      i.checked = keys === 'all' ? true : (keys.indexOf(i.getAttribute('data-sec-toggle')) !== -1);
    });
    domInputs.forEach(function (i) { i.checked = true; });
    apply(); writeSaved();
  }

  var saved = readSaved();
  if (saved) {
    secInputs.forEach(function (i) {
      var k = i.getAttribute('data-sec-toggle');
      if (!i.disabled && saved.sections && typeof saved.sections[k] === 'boolean') i.checked = saved.sections[k];
    });
    domInputs.forEach(function (i) {
      var k = i.getAttribute('data-domain-toggle');
      if (saved.domains && typeof saved.domains[k] === 'boolean') i.checked = saved.domains[k];
    });
  }
  secInputs.concat(domInputs).forEach(function (i) {
    i.addEventListener('change', function () { apply(); writeSaved(); });
  });
  [].forEach.call(root.querySelectorAll('[data-preset]'), function (b) {
    b.addEventListener('click', function () {
      var p = b.getAttribute('data-preset');
      if (p === 'all') setSections('all');
      else if (p === 'none') setSections([]);
      else if (p === 'scores') setSections(['score', 'domains']);
      else setSections(['score', 'domains', 'contact', 'company']);
    });
  });
  apply();

  /* ── 3) Impression (titre du PDF = nom du prospect) ── */
  var oldTitle = document.title;
  var printTitle = 'Fiche-Prospect-<?= e(preg_replace('/[^A-Za-z0-9_-]+/', '-', $fullName !== '' ? $fullName : 'Prospect')) ?>-<?= e($ref) ?>';
  window.addEventListener('beforeprint', function () { document.title = printTitle; });
  window.addEventListener('afterprint', function () { document.title = oldTitle; });
  var btn = document.getElementById('ficheBtnPrint');
  if (btn) btn.addEventListener('click', function () { window.print(); });

  /* ── 4) Validation du formulaire de modification ── */
  var form = document.getElementById('leadEditForm');
  if (form) {
    form.addEventListener('submit', function (e) {
      var valid = true;
      [].forEach.call(form.querySelectorAll('[required]'), function (field) {
        field.style.borderColor = '';
        if (!String(field.value || '').trim() || (field.type === 'checkbox' && !field.checked)) {
          field.style.borderColor = '#dc2626';
          valid = false;
        }
      });
      if (!valid) {
        e.preventDefault();
        var firstInvalid = form.querySelector('[required]:invalid') || form.querySelector('[required]');
        if (firstInvalid) { firstInvalid.focus(); firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
      }
    });
  }
})();
</script>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>
