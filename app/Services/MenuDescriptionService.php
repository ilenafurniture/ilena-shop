<?php

namespace App\Services;

use App\Config\MenuDescriptionDefaults;

/** Stores menu-only copy separately from product detail descriptions. */
final class MenuDescriptionService
{
    public const SETTING_KEY = 'menu_short_descriptions';

    public static function key(string $value): string
    {
        return trim((string) preg_replace('/[^a-z0-9]+/i', '-', strtolower(trim($value))), '-');
    }

    /** One settings lookup per navbar cache rebuild; public navbar output is cached separately. */
    public function entries(): array
    {
        $db = db_connect();
        if (!$db->tableExists('app_settings')) return MenuDescriptionDefaults::entries();

        try {
            $row = $db->table('app_settings')->select('setting_value')
                ->where('setting_key', self::SETTING_KEY)->get()->getRowArray();
            if (!$row) return MenuDescriptionDefaults::entries();
            $decoded = json_decode((string) ($row['setting_value'] ?? ''), true);
            $entries = is_array($decoded) ? ($decoded['entries'] ?? null) : null;
            return is_array($entries) ? $this->sanitizeEntries($entries) : [];
        } catch (\Throwable $e) {
            log_message('error', 'Menu descriptions could not be loaded: {type}', ['type' => get_class($e)]);
            return [];
        }
    }

    public function save(array $entries): bool
    {
        $db = db_connect();
        if (!$db->tableExists('app_settings')) return false;

        $payload = json_encode(['entries' => $this->sanitizeEntries($entries)], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (!is_string($payload)) return false;

        $table = $db->table('app_settings');
        $existing = $table->select('setting_key')->where('setting_key', self::SETTING_KEY)->get()->getRowArray();
        $record = ['setting_value' => $payload, 'updated_at' => date('Y-m-d H:i:s')];
        return $existing
            ? (bool) $db->table('app_settings')->where('setting_key', self::SETTING_KEY)->update($record)
            : (bool) $db->table('app_settings')->insert(['setting_key' => self::SETTING_KEY] + $record);
    }

    private function sanitizeEntries(array $entries): array
    {
        $clean = [];
        foreach ($entries as $key => $copy) {
            if (!is_string($key) || !preg_match('/^[a-z0-9-]{1,100}\|[a-z0-9-]{1,100}$/', $key) || !is_array($copy)) continue;
            $id = $this->plainText($copy['id'] ?? '');
            $en = $this->plainText($copy['en'] ?? '');
            if ($id !== '' || $en !== '') $clean[$key] = ['id' => $id, 'en' => $en];
        }
        return $clean;
    }

    private function plainText($value): string
    {
        if (!is_string($value) && !is_numeric($value)) return '';
        $text = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim((string) preg_replace('/\s+/u', ' ', $text));
        if (function_exists('mb_substr')) return mb_substr($text, 0, 320, 'UTF-8');
        return substr($text, 0, 320);
    }
}
