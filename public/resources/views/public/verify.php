<?php
declare(strict_types=1);
/**
 * Public certificate verification page.
 * Accessible via QR code on the official PDF (/c/{token}) or legacy /verify/{report_number}.
 * Shows ONLY non-sensitive verification info — never detailed results or contact data.
 *
 * @var array|null  $report
 * @var string      $verifyToken
 * @var string      $effectiveStatus   active|revoked|expired|not_found
 * @var bool        $isLegacy
 */
$title = 'AQMI Certificate Verification';

$statusConfig = [
    'active'    => ['label' => 'Valid certificate',    'short' => 'Valid',   'icon' => 'fa-circle-check',    'color' => '#34D399', 'bg' => 'rgba(52,211,153,0.14)'],
    'revoked'   => ['label' => 'Certificate revoked',  'short' => 'Revoked', 'icon' => 'fa-circle-xmark',    'color' => '#F87171', 'bg' => 'rgba(248,113,113,0.14)'],
    'expired'   => ['label' => 'Certificate expired',  'short' => 'Expired', 'icon' => 'fa-clock',           'color' => '#FBBF24', 'bg' => 'rgba(251,191,36,0.14)'],
    'not_found' => ['label' => 'Certificate not found', 'short' => 'Unknown', 'icon' => 'fa-circle-question', 'color' => '#94A3B8', 'bg' => 'rgba(148,163,184,0.14)'],
];
$sc = $statusConfig[$effectiveStatus] ?? $statusConfig['not_found'];

$messages = [
    'active'    => 'This AQMI certificate has been verified and is currently valid.',
    'revoked'   => 'This certificate was revoked by the issuing authority and is no longer valid.',
    'expired'   => 'This certificate has passed its validity period and is no longer valid.',
    'not_found' => 'No certificate matches this verification code.',
];
$message = $messages[$effectiveStatus] ?? $messages['not_found'];

$showInfo = $report !== null && $effectiveStatus !== 'not_found';

// English date formatting, independent of the app-wide formatDate() helper.
$fmtDate = static function ($value): string {
    if (empty($value)) {
        return '—';
    }
    try {
        return (new DateTimeImmutable((string) $value))->format('j M Y');
    } catch (Throwable $e) {
        return '—';
    }
};

// Validity progress (only meaningful for active certificates).
$progress = null;
if ($effectiveStatus === 'active' && $showInfo) {
    $start = strtotime((string) ($report['issued_at'] ?? $report['certified_at'] ?? ''));
    $end   = strtotime((string) ($report['expires_at'] ?? ''));
    if ($start && $end && $end > $start) {
        $progress = (int) max(0, min(100, round((time() - $start) / ($end - $start) * 100)));
    }
}

$certNumber = (string) ($report['report_number'] ?? $verifyToken);

ob_start();
?>
<main class="page">
  <article class="card" style="--accent:<?= $sc['color'] ?>;--accent-bg:<?= $sc['bg'] ?>;" aria-labelledby="verify-title">

    <header class="hero">
      <div class="seal" aria-hidden="true">
        <i class="fas <?= $sc['icon'] ?>"></i>
      </div>
      <h1 id="verify-title"><?= e($sc['label']) ?></h1>
      <p class="hero-text"><?= e($message) ?></p>
    </header>

    <?php if ($showInfo): ?>
    <section class="body">

      <?php if ($effectiveStatus === 'revoked'): ?>
        <div class="notice" role="alert">
          <i class="fas fa-ban"></i>
          <div>
            <strong>This certificate has been revoked</strong>
            <?php if (!empty($report['revoked_reason'])): ?>
              <span>Reason: <?= e($report['revoked_reason']) ?></span>
            <?php endif; ?>
            <?php if (!empty($report['revoked_at'])): ?>
              <span>Revoked on <?= e($fmtDate($report['revoked_at'])) ?></span>
            <?php endif; ?>
          </div>
        </div>
      <?php elseif ($effectiveStatus === 'expired'): ?>
        <div class="notice" role="alert">
          <i class="fas fa-clock"></i>
          <div>
            <strong>This certificate has expired</strong>
            <?php if (!empty($report['expires_at'])): ?>
              <span>Expired on <?= e($fmtDate($report['expires_at'])) ?></span>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>

      <div class="cert-id">
        <div>
          <span class="label">Certificate number</span>
          <span class="cert-number" id="cert-number"><?= e($certNumber) ?></span>
        </div>
        <button type="button" class="copy-btn" id="copy-btn" aria-label="Copy certificate number">
          <i class="far fa-copy"></i> <span>Copy</span>
        </button>
      </div>

      <dl class="grid">
        <div class="item wide">
          <dt>Organization</dt>
          <dd class="strong"><?= e($report['company'] ?? 'N/A') ?></dd>
        </div>

        <?php if (!empty($report['sector'])): ?>
        <div class="item">
          <dt>Sector</dt>
          <dd><?= e($report['sector']) ?></dd>
        </div>
        <?php endif; ?>

        <div class="item">
          <dt>Country</dt>
          <dd><?= e($report['country'] ?? '-') ?></dd>
        </div>

        <?php if (!empty($report['aqmi_level_assigned'])): ?>
        <div class="item">
          <dt>AQMI level</dt>
          <dd><span class="pill"><?= e($report['aqmi_level_assigned']) ?></span></dd>
        </div>
        <?php endif; ?>

        <div class="item">
          <dt>Status</dt>
          <dd><span class="status-dot"></span><span class="status-text"><?= e($sc['short']) ?></span></dd>
        </div>

        <div class="item">
          <dt>Issued on</dt>
          <dd><?= e($fmtDate($report['issued_at'] ?? $report['certified_at'] ?? null)) ?></dd>
        </div>

        <div class="item">
          <dt>Expires on</dt>
          <dd><?= e($fmtDate($report['expires_at'] ?? null)) ?></dd>
        </div>
      </dl>

      <?php if ($progress !== null): ?>
      <div class="validity" aria-label="Validity period elapsed">
        <div class="bar"><span style="width:<?= $progress ?>%"></span></div>
        <div class="bar-legend">
          <span>Issued</span>
          <span><?= $progress ?>% of validity period elapsed</span>
          <span>Expires</span>
        </div>
      </div>
      <?php endif; ?>
    </section>

    <?php else: ?>
    <section class="body empty">
      <span class="label">Code searched</span>
      <code class="code"><?= e($verifyToken) ?></code>
      <p class="hint">Check the code for typos, or scan the QR code printed on the official certificate.</p>
      <?php if ($isLegacy): ?>
      <p class="hint">This looks like a legacy link. If you have the original QR code, scan it to open the up-to-date verification page.</p>
      <?php endif; ?>
    </section>
    <?php endif; ?>

    <aside class="security">
      <i class="fas fa-shield-halved"></i>
      <p>This check is public and reveals no confidential data. Detailed results are only available to authorized users after signing in.</p>
    </aside>

    <footer class="footer">
      <a href="/"><i class="fas fa-arrow-left"></i> Back to home</a>
      <span class="brand">AQMI by NOVAQYS</span>
    </footer>
  </article>
</main>
<?php $content = ob_get_clean(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title><?= e($title) ?></title>
  <?php require BASE_PATH . '/resources/views/partials/favicon.blade.php'; ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #070B14;
      --surface: #111827;
      --surface-2: #0D1422;
      --line: rgba(148,163,184,0.14);
      --text: #F1F5F9;
      --muted: #94A3B8;
      --faint: #64748B;
      --brand: #38BDF8;
    }
    *, *::before, *::after { box-sizing: border-box; }
    html, body { margin: 0; }
    body {
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
      color: var(--text);
      background:
        radial-gradient(900px 420px at 50% -10%, rgba(56,189,248,0.10), transparent 70%),
        linear-gradient(rgba(148,163,184,0.04) 1px, transparent 1px) 0 0 / 100% 36px,
        linear-gradient(90deg, rgba(148,163,184,0.04) 1px, transparent 1px) 0 0 / 36px 100%,
        var(--bg);
      min-height: 100vh;
      -webkit-font-smoothing: antialiased;
    }
    .page {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
    }
    .card {
      width: 100%;
      max-width: 580px;
      background: var(--surface);
      border: 1px solid var(--line);
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 30px 80px rgba(0,0,0,0.55);
    }

    /* Hero */
    .hero {
      text-align: center;
      padding: 2.5rem 2rem 2rem;
      background:
        radial-gradient(420px 200px at 50% 0%, var(--accent-bg), transparent 75%),
        var(--surface);
      border-bottom: 1px solid var(--line);
    }
    .seal {
      width: 84px; height: 84px;
      margin: 0 auto 1.25rem;
      border-radius: 50%;
      display: grid; place-items: center;
      background: var(--accent-bg);
      box-shadow: 0 0 0 8px rgba(255,255,255,0.02), 0 0 0 1px var(--accent) inset;
      animation: seal-in .6s cubic-bezier(.2,.9,.3,1.2) both;
    }
    .seal i { font-size: 2.3rem; color: var(--accent); }
    .hero h1 {
      margin: 0 0 .5rem;
      font-size: 1.75rem;
      font-weight: 800;
      letter-spacing: -0.02em;
    }
    .hero-text {
      margin: 0 auto;
      max-width: 36ch;
      font-size: .95rem;
      line-height: 1.55;
      color: var(--muted);
    }

    /* Body */
    .body { padding: 1.75rem 2rem 1.25rem; }
    .label { display: block; font-size: .78rem; color: var(--faint); margin-bottom: .3rem; }

    .notice {
      display: flex; gap: .8rem; align-items: flex-start;
      background: var(--accent-bg);
      border: 1px solid var(--accent);
      border-radius: 14px;
      padding: .9rem 1rem;
      margin-bottom: 1.25rem;
      font-size: .85rem;
      color: var(--muted);
    }
    .notice > i { color: var(--accent); margin-top: .15rem; }
    .notice strong { display: block; color: var(--accent); font-weight: 700; margin-bottom: .15rem; }
    .notice span { display: block; }

    .cert-id {
      display: flex; align-items: center; justify-content: space-between; gap: 1rem;
      background: var(--surface-2);
      border: 1px solid var(--line);
      border-radius: 14px;
      padding: .9rem 1rem;
      margin-bottom: 1.25rem;
    }
    .cert-number {
      font-family: 'JetBrains Mono', ui-monospace, monospace;
      font-size: 1.05rem; font-weight: 700;
      word-break: break-all;
    }
    .copy-btn {
      flex-shrink: 0;
      display: inline-flex; align-items: center; gap: .4rem;
      font: inherit; font-size: .8rem; font-weight: 600;
      color: var(--brand);
      background: rgba(56,189,248,0.1);
      border: 1px solid rgba(56,189,248,0.25);
      border-radius: 10px;
      padding: .5rem .8rem;
      cursor: pointer;
      transition: background .15s;
    }
    .copy-btn:hover { background: rgba(56,189,248,0.18); }

    .grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0;
      margin: 0;
      border: 1px solid var(--line);
      border-radius: 14px;
      overflow: hidden;
    }
    .item {
      padding: .9rem 1rem;
      border-bottom: 1px solid var(--line);
      border-right: 1px solid var(--line);
    }
    .item:nth-child(even) { border-right: 0; }
    .item.wide { grid-column: 1 / -1; border-right: 0; }
    .item:last-child, .item:nth-last-child(2):nth-child(odd) { border-bottom: 0; }
    .item dt { font-size: .75rem; color: var(--faint); margin-bottom: .25rem; }
    .item dd { margin: 0; font-size: .92rem; font-weight: 500; }
    .item dd.strong { font-size: 1.1rem; font-weight: 700; }

    .pill {
      display: inline-block;
      padding: .2rem .65rem;
      border-radius: 999px;
      font-size: .82rem; font-weight: 700;
      color: var(--brand);
      background: rgba(56,189,248,0.12);
    }
    .status-dot {
      display: inline-block; width: 8px; height: 8px; border-radius: 50%;
      background: var(--accent); margin-right: .45rem;
      box-shadow: 0 0 0 3px var(--accent-bg);
    }
    .status-text { color: var(--accent); font-weight: 700; }

    .validity { margin-top: 1.25rem; }
    .bar { height: 6px; border-radius: 999px; background: rgba(148,163,184,0.15); overflow: hidden; }
    .bar span { display: block; height: 100%; border-radius: inherit; background: linear-gradient(90deg, var(--accent), var(--brand)); }
    .bar-legend { display: flex; justify-content: space-between; gap: .5rem; margin-top: .5rem; font-size: .72rem; color: var(--faint); }

    /* Empty state */
    .empty { text-align: center; }
    .code {
      display: inline-block; margin: .25rem 0 1rem;
      padding: .45rem .8rem;
      background: var(--surface-2);
      border: 1px solid var(--line);
      border-radius: 10px;
      font-family: 'JetBrains Mono', ui-monospace, monospace;
      font-size: .9rem; color: var(--muted);
      word-break: break-all;
    }
    .hint { margin: .4rem auto 0; max-width: 42ch; font-size: .85rem; line-height: 1.55; color: var(--faint); }

    /* Security + footer */
    .security {
      display: flex; gap: .75rem; align-items: flex-start;
      margin: 0 2rem 1.5rem; padding: .9rem 1rem;
      border-radius: 12px;
      background: rgba(56,189,248,0.05);
      color: var(--faint);
    }
    .security i { color: var(--brand); margin-top: .15rem; }
    .security p { margin: 0; font-size: .76rem; line-height: 1.55; }

    .footer {
      display: flex; align-items: center; justify-content: space-between; gap: 1rem;
      padding: 1rem 2rem 1.25rem;
      background: var(--surface-2);
      border-top: 1px solid var(--line);
      font-size: .8rem;
    }
    .footer a { color: var(--brand); font-weight: 600; text-decoration: none; }
    .footer a:hover { text-decoration: underline; }
    .brand { color: var(--faint); }

    a:focus-visible, button:focus-visible { outline: 2px solid var(--brand); outline-offset: 2px; }

    @keyframes seal-in { from { transform: scale(.6); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    @media (prefers-reduced-motion: reduce) { .seal { animation: none; } }

    @media (max-width: 480px) {
      .hero { padding: 2rem 1.25rem 1.5rem; }
      .hero h1 { font-size: 1.5rem; }
      .body { padding: 1.25rem 1.25rem 1rem; }
      .security { margin: 0 1.25rem 1.25rem; }
      .footer { padding: 1rem 1.25rem; flex-direction: column; gap: .5rem; }
      .grid { grid-template-columns: 1fr; }
      .item { border-right: 0 !important; }
      .item:nth-last-child(2):nth-child(odd) { border-bottom: 1px solid var(--line); }
    }
  </style>
</head>
<body>
  <?= $content ?>
  <script>
    (function () {
      var btn = document.getElementById('copy-btn');
      var num = document.getElementById('cert-number');
      if (!btn || !num || !navigator.clipboard) { if (btn) btn.style.display = 'none'; return; }
      btn.addEventListener('click', function () {
        navigator.clipboard.writeText(num.textContent.trim()).then(function () {
          var label = btn.querySelector('span');
          label.textContent = 'Copied';
          setTimeout(function () { label.textContent = 'Copy'; }, 1800);
        });
      });
    })();
  </script>
</body>
</html>
