<?php
/** Domain Scores Table — sortable table of domain scores.
 * @var array $config
 * @var string $title
 */
$domains = $config['domains'] ?? [];
$title   = $config['title']   ?? ($title ?: 'Scores par domaine');
?>
<div class="rs-block-domain-scores py-2">
    <h5 class="rs-block-title"><?= e($title) ?></h5>
    <table class="table table-sm" style="border-radius:8px;overflow:hidden;">
        <thead style="background:#EEF2F7;">
            <tr>
                <th style="font-weight:700;color:#0D1B3E;">Domaine</th>
                <th class="text-center" style="font-weight:700;color:#0D1B3E;">Score</th>
                <th class="text-center" style="font-weight:700;color:#0D1B3E;">Max</th>
                <th style="font-weight:700;color:#0D1B3E;">Niveau</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($domains as $d):
                $score = (int)($d['score'] ?? 0);
                $max   = (int)($d['max']   ?? 100);
                $pct   = $max > 0 ? round(($score / $max) * 100) : 0;
                $level = $pct >= 80 ? 'A' : ($pct >= 60 ? 'B' : ($pct >= 40 ? 'C' : 'D'));
                $levelColor = $pct >= 80 ? '#2EC4B6' : ($pct >= 60 ? '#1F6FEB' : ($pct >= 40 ? '#C9A227' : '#E5484D'));
            ?>
                <tr style="border-color:#EEF2F7;">
                    <td style="font-weight:600;color:#102A43;"><?= e($d['label'] ?? '') ?></td>
                    <td class="text-center" style="font-weight:700;color:#0D1B3E;"><?= $score ?></td>
                    <td class="text-center text-muted"><?= $max ?></td>
                    <td><span class="badge" style="background:<?= $levelColor ?>;color:#fff;font-weight:700;"><?= $level ?></span></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($domains)): ?>
                <tr><td colspan="4" class="text-muted text-center">Aucun domaine</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
