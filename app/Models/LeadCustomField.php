<?php
namespace App\Models;

use App\Helpers\Database;

class LeadCustomField
{
    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM lead_custom_fields WHERE id = ?", [$id]);
    }

    public static function all(): array
    {
        return Database::fetchAll("SELECT * FROM lead_custom_fields ORDER BY sort_order");
    }

    public static function allActive(): array
    {
        return Database::fetchAll("SELECT * FROM lead_custom_fields WHERE is_active = 1 ORDER BY sort_order");
    }

    public static function getBySection(string $section): array
    {
        return Database::fetchAll("SELECT * FROM lead_custom_fields WHERE is_active = 1 AND section = ? ORDER BY sort_order", [$section]);
    }

    public static function getSections(): array
    {
        return Database::fetchAll("SELECT DISTINCT section FROM lead_custom_fields WHERE is_active = 1 ORDER BY section");
    }

    public static function create(array $data): int
    {
        return Database::insert(
            "INSERT INTO lead_custom_fields (label, label_fr, label_ar, field_type, options, placeholder, placeholder_fr, is_required, sort_order, section, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['label'] ?? '', $data['label_fr'] ?? '', $data['label_ar'] ?? '',
                $data['field_type'] ?? 'text', $data['options'] ?? null,
                $data['placeholder'] ?? '', $data['placeholder_fr'] ?? '',
                $data['is_required'] ?? 0, $data['sort_order'] ?? 0,
                $data['section'] ?? 'general', $data['is_active'] ?? 1
            ]
        );
    }

    public static function update(int $id, array $data): int
    {
        $sets = []; $params = [];
        foreach (['label', 'label_fr', 'label_ar', 'field_type', 'options', 'placeholder', 'placeholder_fr', 'is_required', 'sort_order', 'section', 'is_active'] as $key) {
            if (isset($data[$key])) { $sets[] = "{$key} = ?"; $params[] = $data[$key]; }
        }
        if (empty($sets)) return 0;
        $params[] = $id;
        return Database::execute("UPDATE lead_custom_fields SET " . implode(', ', $sets) . " WHERE id = ?", $params);
    }

    public static function delete(int $id): int
    {
        return Database::execute("DELETE FROM lead_custom_fields WHERE id = ?", [$id]);
    }

    public static function getValues(int $leadId): array
    {
        return Database::fetchAll(
            "SELECT lcf.*, lfv.value, lfv.id as value_id FROM lead_custom_fields lcf LEFT JOIN lead_field_values lfv ON lcf.id = lfv.field_id AND lfv.lead_id = ? WHERE lcf.is_active = 1 ORDER BY lcf.sort_order",
            [$leadId]
        );
    }

    public static function saveValue(int $leadId, int $fieldId, ?string $value): void
    {
        $existing = Database::fetch("SELECT id FROM lead_field_values WHERE lead_id = ? AND field_id = ?", [$leadId, $fieldId]);
        if ($existing) {
            Database::execute("UPDATE lead_field_values SET value = ? WHERE id = ?", [$value, $existing['id']]);
        } else {
            Database::insert("INSERT INTO lead_field_values (lead_id, field_id, value) VALUES (?, ?, ?)", [$leadId, $fieldId, $value]);
        }
    }

    public static function saveValues(int $leadId, array $values): void
    {
        foreach ($values as $fieldId => $value) {
            if (is_array($value)) $value = json_encode($value);
            self::saveValue($leadId, (int)$fieldId, $value);
        }
    }

    public static function count(): int
    {
        return (int)Database::fetch("SELECT COUNT(*) as count FROM lead_custom_fields")['count'];
    }
}