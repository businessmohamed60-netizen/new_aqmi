<?php
namespace App\Helpers;

class ViewHelper
{
    public static function renderLayout(string $layout, array $data = []): void
    {
        extract($data);
        require BASE_PATH . "/resources/views/layouts/{$layout}.php";
    }

    public static function include(string $view, array $data = []): void
    {
        extract($data);
        require BASE_PATH . "/resources/views/{$view}.php";
    }

    public static function pagination(int $currentPage, int $totalPages, string $urlPattern): string
    {
        if ($totalPages <= 1) return '';

        $html = '<nav><ul class="pagination justify-content-center">';
        $prevDisabled = $currentPage <= 1 ? 'disabled' : '';
        $prevUrl = $currentPage > 1 ? str_replace('{page}', $currentPage - 1, $urlPattern) : '#';
        $html .= "<li class='page-item {$prevDisabled}'><a class='page-link' href='{$prevUrl}'>&laquo;</a></li>";

        for ($i = 1; $i <= $totalPages; $i++) {
            $active = $i === $currentPage ? 'active' : '';
            $pageUrl = str_replace('{page}', $i, $urlPattern);
            $html .= "<li class='page-item {$active}'><a class='page-link' href='{$pageUrl}'>{$i}</a></li>";
        }

        $nextDisabled = $currentPage >= $totalPages ? 'disabled' : '';
        $nextUrl = $currentPage < $totalPages ? str_replace('{page}', $currentPage + 1, $urlPattern) : '#';
        $html .= "<li class='page-item {$nextDisabled}'><a class='page-link' href='{$nextUrl}'>&raquo;</a></li>";
        $html .= '</ul></nav>';
        return $html;
    }
}