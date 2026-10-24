<?php

namespace App\Commands;

use App\Services\Auth\DisposableEmailDomainList;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SyncDisposableEmailDomains extends BaseCommand
{
    protected $group = 'Customers';
    protected $name = 'customers:sync-disposable-email-domains';
    protected $description = 'Perbarui daftar lokal domain email sementara.';

    public function run(array $params)
    {
        try {
            $count = (new DisposableEmailDomainList())->refresh();
            CLI::write('Daftar domain email sementara diperbarui: ' . $count . ' domain.', 'green');
            return EXIT_SUCCESS;
        } catch (\Throwable $error) {
            log_message('error', 'Disposable email domain sync failed: {type}', ['type' => get_class($error)]);
            CLI::error('Daftar domain tidak diperbarui; salinan lokal sebelumnya tetap digunakan.');
            return EXIT_ERROR;
        }
    }
}
