<?php
/** Domain Scores — premium table with color-coded badges.
 * @var array $config
 * @var string $title
 */
$domains = $config['domains'] ?? [];
$title   = $config['title']   ?? ($title ?: 'Scores par domaine');
$showBar = $config['show_progress_bar'] ?? true;
$altRows = $config['alternating_rows'] ?? true;
?>
<div class="rs-block-domain-scores py-2">
    <h5 class="rs-block-title"><?= e($title) ?></h5>
    <table class="table table-sm" style="border-radius:8px;overflow:hidden;">
        <thead>
            <tr>
                <th style="font-weight:700;color:#fff;">Domaine</th>
                <th class="text-center" style="font-weight:700;color:#fff;">Score</th>
                <th class="text-center" style="font-weight:700;color:#fff;">Max</th>
                <th style="font-weight:700;color:#fff;">Niveau</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($domains as $d):
                $score = (int)($d['score'] ?? 0);
                $max   = (int)($d['max']   ?? 100);
                $pct   = $max > 0 ? round(($score / $max) * 100) : 0;
                $level = $pct >= 80 ? 'A' : ($pct >= 60 ? 'B' : ($pct >= 40 ? 'C' : 'D'));
                $levelColor = $pct >= 80 ? '#2EC4B6' : ($pct >= 60 ? '#1F6FEB' : ($pct >= 40 ? '#9d8fd1' : '#E5484D'));
            ?>
                <tr style="border-color:#f0e8d8;<?= $altRows ? 'background: transparent;' : '' ?>">
                    <td style="font-weight:600;color:#0f2845;font-size:0.82rem;"><?= e($d['label'] ?? '') ?></td>
                    <td class="text-center" style="font-weight:700;color:#0f2845;font-size:0.82rem;"><?= $score ?></td>
                    <td class="text-center" style="color:#8298b5;font-size:0.82rem;"><?= $max ?></td>
                    <td><span class="badge" style="background:<?= $levelColor ?>;color:#fff;font-weight:700;font-size:0.68rem;padding:4px 10px;border-radius:10px;"><?= $level ?></span></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($domains)): ?>
                <tr><td colspan="4" class="text-muted text-center" style="padding: 16px;">Aucun domaine</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
