<?php

namespace App\Services;

use App\Models\PortfolioProjectModel;

/** One cached, ordered public read for the portfolio; edits invalidate it. */
final class PortfolioService
{
    public const CACHE_KEY = 'ilena_public_portfolio_v1';
    public const REFRESH_NAME = 'portfolio';
    public const PAGE_SETTING_KEY = 'portfolio_page_enabled';

    public function pageData(bool $forceRefresh = false): array
    {
        return (new CoordinatedPublicCache())->remember(
            self::CACHE_KEY,
            self::REFRESH_NAME,
            300,
            1800,
            static function (): array {
                $db = db_connect();
                $enabled = false;
                if ($db->tableExists('app_settings')) {
                    $setting = $db->table('app_settings')->select('setting_value')
                        ->where('setting_key', self::PAGE_SETTING_KEY)->get()->getRowArray();
                    $enabled = (string) ($setting['setting_value'] ?? '0') === '1';
                }
                $projects = [];
                if ($db->tableExists('portfolio_projects')) {
                    $projects = (new PortfolioProjectModel())
                    ->where('active', 1)
                    ->orderBy('featured', 'DESC')
                    ->orderBy('sort_order', 'ASC')
                    ->orderBy('id', 'DESC')
                    ->findAll(100);
                }
                return ['enabled' => $enabled, 'projects' => $projects];
            },
            $forceRefresh
        );
    }

    public function projects(bool $forceRefresh = false): array
    {
        return $this->pageData($forceRefresh)['projects'] ?? [];
    }

    public function setPageEnabled(bool $enabled): bool
    {
        $db = db_connect();
        if (!$db->tableExists('app_settings')) return false;
        $table = $db->table('app_settings');
        $exists = $table->select('setting_key')->where('setting_key', self::PAGE_SETTING_KEY)->get()->getRowArray();
        $now = date('Y-m-d H:i:s');
        $record = ['setting_value' => $enabled ? '1' : '0', 'updated_at' => $now];
        $saved = $exists
            ? (bool) $db->table('app_settings')->where('setting_key', self::PAGE_SETTING_KEY)->update($record)
            : (bool) $db->table('app_settings')->insert(['setting_key' => self::PAGE_SETTING_KEY] + $record);
        if ($saved) $this->invalidate();
        return $saved;
    }

    public function invalidate(): void
    {
        (new CoordinatedPublicCache())->invalidate(self::CACHE_KEY, self::REFRESH_NAME);
    }
}
