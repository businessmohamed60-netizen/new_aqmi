<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Services;

use App\Modules\ReportStudio\Models\ReportTheme;

/**
 * Compiles a ReportTheme into CSS custom properties usable by both
 * the live preview canvas and the host PDF engine.
 */
final class ThemeCompiler
{
    /**
     * Return an associative [var => value] map for a theme.
     */
    public function compile(ReportTheme $theme): array
    {
        $vars = $theme->toCssVars();
        $vars['--rs-heading'] = $vars['--rs-heading'] ?? ($theme->primary_color ?? '#102A43');
        $vars['--rs-body']    = $vars['--rs-body']    ?? ($theme->body_color ?? '#102A43');
        return $vars;
    }

    /**
     * Render the variables as a CSS `:root { ... }` string.
     */
    public function toCss(ReportTheme $theme): string
    {
        $lines = [];
        foreach ($this->compile($theme) as $var => $value) {
            $lines[] = sprintf('%s: %s;', $var, $value);
        }
        return ":root {\n  " . implode("\n  ", $lines) . "\n}";
    }

    /**
     * Inline `style="..."` string for the preview container.
     */
    public function toInlineStyle(ReportTheme $theme): string
    {
        $parts = [];
        foreach ($this->compile($theme) as $var => $value) {
            $parts[] = sprintf('%s: %s', $var, $value);
        }
        return implode('; ', $parts);
    }
}
