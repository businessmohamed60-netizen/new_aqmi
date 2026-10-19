<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Traits;

/**
 * Casts JSON columns to/from arrays on models backed by the AQMI
 * Database (PDO) helper.
 */
trait HasJsonFields
{
    /**
     * Decode JSON columns to arrays after a fetch.
     */
    public function hydrateJson(array $attributes): array
    {
        foreach ($this->jsonFields as $field) {
            if (isset($attributes[$field]) && is_string($attributes[$field])) {
                $decoded = json_decode($attributes[$field], true);
                $attributes[$field] = is_array($decoded) ? $decoded : [];
            } elseif (!isset($attributes[$field])) {
                $attributes[$field] = [];
            }
        }
        return $attributes;
    }

    /**
     * Encode array fields to JSON before an insert/update.
     */
    public function packJson(array $attributes): array
    {
        foreach ($this->jsonFields as $field) {
            if (array_key_exists($field, $attributes) && is_array($attributes[$field])) {
                $attributes[$field] = json_encode($attributes[$field], JSON_UNESCAPED_UNICODE);
            }
        }
        return $attributes;
    }
}
