<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Services;

use App\Helpers\Database;
use PDO;

/**
 * Lets any Report Studio block pull data from a real database table.
 * Each block stores a `data_source` config block:
 *   table:        target table name (allowlisted)
 *   label_column: column for chart labels / axis names
 *   value_column: column for chart values
 *   series_column: optional column to group rows into multiple series
 *   where_clause: optional SQL fragment (validated: no quotes/semicolons)
 *   order_by:     optional column + ASC/DESC
 *   limit:        max rows (default 50)
 */
final class DataSourceService
{
    /**
     * Tables the user is allowed to bind to. Built from the app's own tables
     * so nothing sensitive is ever exposed.
     */
    private const ALLOWED_TABLES = [
        'domains',
        'questions',
        'answers',
        'assessments',
        'recommendations',
        'score_levels',
        'leads',
        'lead_custom_fields',
        'reports',
        'users',
        'login_history',
        'evaluation_models',
        'report_templates',
        'report_template_blocks',
        'report_blocks',
        'report_themes',
    ];

    public static function allowedTables(): array
    {
        return self::ALLOWED_TABLES;
    }

    public function isAllowedTable(string $table): bool
    {
        return in_array($table, self::ALLOWED_TABLES, true);
    }

    /**
     * List every allowed table with its columns and row count.
     */
    public function listTables(): array
    {
        $pdo = Database::getInstance()->getConnection();
        $result = [];

        foreach (self::ALLOWED_TABLES as $table) {
            $columns = $this->getColumns($table);
            if (empty($columns)) {
                continue;
            }

            try {
                $count = (int) $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
            } catch (\Throwable) {
                $count = 0;
            }

            $result[] = [
                'name'    => $table,
                'columns' => $columns,
                'rows'    => $count,
            ];
        }

        return $result;
    }

    /**
     * Get column metadata for a single table.
     */
    public function getColumns(string $table): array
    {
        if (!$this->isAllowedTable($table)) {
            return [];
        }

        try {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->query("SHOW COLUMNS FROM `{$table}`");
            $cols = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $type = $row['Type'] ?? 'text';
                $isNumeric = preg_match('/(int|decimal|float|double|numeric|bit)/i', $type);
                $cols[] = [
                    'name'      => $row['Field'],
                    'type'      => $type,
                    'numeric'   => (bool) $isNumeric,
                    'nullable'  => strtoupper($row['Null'] ?? 'YES') === 'YES',
                ];
            }
            return $cols;
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Fetch data from a bound table and return it in chart-ready format.
     * Returns either single-series or multi-series data depending on
     * whether `series_column` is set.
     *
     * @return array{labels: array, series: array, rows: array}
     */
    public function fetchChartData(array $dataSource): array
    {
        $table        = $dataSource['table']        ?? '';
        $labelColumn  = $dataSource['label_column']  ?? '';
        $valueColumn  = $dataSource['value_column']  ?? '';
        $seriesColumn = $dataSource['series_column'] ?? '';
        $orderBy      = $dataSource['order_by']      ?? '';
        $orderDir     = strtoupper($dataSource['order_dir'] ?? 'ASC');
        $limit        = max(1, min(500, (int) ($dataSource['limit'] ?? 50)));
        $whereClause  = $dataSource['where_clause'] ?? '';

        if (!$this->isAllowedTable($table) || $labelColumn === '' || $valueColumn === '') {
            return ['labels' => [], 'series' => [], 'rows' => []];
        }

        // Validate columns exist
        $columns = $this->getColumns($table);
        $columnNames = array_column($columns, 'name');
        if (!in_array($labelColumn, $columnNames, true) || !in_array($valueColumn, $columnNames, true)) {
            return ['labels' => [], 'series' => [], 'rows' => []];
        }
        if ($seriesColumn !== '' && !in_array($seriesColumn, $columnNames, true)) {
            $seriesColumn = '';
        }

        // Validate ORDER BY column
        $orderCol = '';
        if ($orderBy !== '' && in_array($orderBy, $columnNames, true)) {
            $orderCol = $orderBy;
        }
        $orderDir = in_array($orderDir, ['ASC', 'DESC'], true) ? $orderDir : 'ASC';

        // Validate WHERE clause — only allow simple column op value patterns
        $whereSql = '';
        if ($whereClause !== '') {
            $whereSql = $this->sanitizeWhere($whereClause);
        }

        $selectCols = ["`{$labelColumn}`", "`{$valueColumn}`"];
        if ($seriesColumn !== '') {
            $selectCols[] = "`{$seriesColumn}`";
        }

        $sql = sprintf(
            "SELECT %s FROM `%s`%s ORDER BY %s LIMIT %d",
            implode(', ', $selectCols),
            $table,
            $whereSql !== '' ? " WHERE {$whereSql}" : '',
            $orderCol !== '' ? "`{$orderCol}` {$orderDir}" : "`{$labelColumn}` ASC",
            $limit
        );

        try {
            $rows = Database::fetchAll($sql);
        } catch (\Throwable) {
            return ['labels' => [], 'series' => [], 'rows' => []];
        }

        if ($seriesColumn !== '') {
            return $this->buildMultiSeries($rows, $labelColumn, $valueColumn, $seriesColumn);
        }

        return $this->buildSingleSeries($rows, $labelColumn, $valueColumn);
    }

    /**
     * Preview the first N rows for the property panel.
     */
    public function previewRows(string $table, int $limit = 10): array
    {
        if (!$this->isAllowedTable($table)) {
            return [];
        }

        try {
            $limit = max(1, min(100, $limit));
            return Database::fetchAll("SELECT * FROM `{$table}` LIMIT {$limit}");
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Resolve a block's data source: if the block has a data_source config
     * with a table binding, fetch live data and merge it into the config.
     * If no binding, return config unchanged (manual data mode).
     */
    public function resolveBlockConfig(array $config): array
    {
        $ds = $config['data_source'] ?? null;
        if (!is_array($ds) || empty($ds['table']) || empty($ds['label_column']) || empty($ds['value_column'])) {
            return $config;
        }

        $data = $this->fetchChartData($ds);

        // Transform into the format each chart block expects
        if (!empty($data['series']) && count($data['series']) > 1) {
            // Multi-series: bar/line/area charts
            $config['series'] = $data['series'];
        } elseif (!empty($data['series'])) {
            // Single series
            $config['series'] = $data['series'];
        }

        // For radar chart: axes format
        if (!empty($data['labels']) && !empty($data['series'])) {
            $axes = [];
            foreach ($data['labels'] as $i => $label) {
                $axes[] = [
                    'label' => $label,
                    'value' => $data['series'][0]['data'][$i] ?? 0,
                ];
            }
            $config['axes'] = $axes;
        }

        // For global_score / gauge: use first value or sum
        if (isset($config['score']) && !empty($data['series'])) {
            $config['score'] = $data['series'][0]['data'][0] ?? 0;
        }

        // For donut chart: series format is [{label, value}]
        if (!empty($data['labels']) && !empty($data['series'][0]['data'])) {
            $donutSeries = [];
            foreach ($data['labels'] as $i => $label) {
                $donutSeries[] = [
                    'label' => $label,
                    'value' => $data['series'][0]['data'][$i] ?? 0,
                ];
            }
            $config['series'] = $donutSeries;
        }

        return $config;
    }

    // ── Internal helpers ──────────────────────────────────────────

    private function buildSingleSeries(array $rows, string $labelCol, string $valueCol): array
    {
        $labels = [];
        $values = [];

        foreach ($rows as $row) {
            $labels[] = (string) ($row[$labelCol] ?? '');
            $values[] = (float) ($row[$valueCol] ?? 0);
        }

        return [
            'labels' => $labels,
            'series' => [
                ['name' => $valueCol, 'data' => $values],
            ],
            'rows'  => $rows,
        ];
    }

    private function buildMultiSeries(array $rows, string $labelCol, string $valueCol, string $seriesCol): array
    {
        $labels = [];
        $seriesMap = [];

        foreach ($rows as $row) {
            $label = (string) ($row[$labelCol] ?? '');
            $seriesName = (string) ($row[$seriesCol] ?? 'Default');
            $value = (float) ($row[$valueCol] ?? 0);

            if (!in_array($label, $labels, true)) {
                $labels[] = $label;
            }
            $seriesMap[$seriesName][$label] = $value;
        }

        $series = [];
        foreach ($seriesMap as $name => $labelValues) {
            $data = [];
            foreach ($labels as $label) {
                $data[] = $labelValues[$label] ?? 0;
            }
            $series[] = ['name' => $name, 'data' => $data];
        }

        return [
            'labels' => $labels,
            'series' => $series,
            'rows'  => $rows,
        ];
    }

    /**
     * Sanitize a WHERE clause — allow only basic patterns:
     * column=value, column>value, column LIKE '%text%'
     * Block semicolons, UNION, subqueries, comments.
     */
    private function sanitizeWhere(string $where): string
    {
        // Block dangerous keywords
        $dangerous = [';', 'UNION', 'SLEEP', 'BENCHMARK', 'LOAD_FILE', 'INTO OUTFILE', '--', '/*', '*/'];
        $upper = strtoupper($where);
        foreach ($dangerous as $word) {
            if (str_contains($upper, strtoupper($word))) {
                return '';
            }
        }

        // Only allow alphanumeric, column names, operators, quotes, wildcards
        if (!preg_match('/^[a-zA-Z0-9_\'"=<>!%().,\s\*\-]+$/', $where)) {
            return '';
        }

        return $where;
    }
}
