<?php

namespace App\Commands;

use App\Services\CacheRefreshWorker;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CacheWarm extends BaseCommand
{
    protected $group = 'Ilena Performance';
    protected $name = 'cache:warm';
    protected $description = 'Warm shared public navigation, sitemap, and homepage slider caches.';
    protected $usage = 'cache:warm';

    public function run(array $params)
    {
        try {
            helper(['url', 'ilena_store']);
            $count = (new CacheRefreshWorker())->warmAll();
            CLI::write('Warmed ' . $count . ' shared public caches.');
            return EXIT_SUCCESS;
        } catch (\Throwable $error) {
            CLI::error('Cache warming failed: ' . get_class($error));
            log_message('error', 'Cache warming failed: {type}', ['type' => get_class($error)]);
            return EXIT_ERROR;
        }
    }
}
