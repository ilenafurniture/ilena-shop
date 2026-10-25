<?php

namespace App\Commands;

use App\Services\HomeAssetManager;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class MigrateHomeImages extends BaseCommand
{
    protected $group = 'Media';
    protected $name = 'home-images:migrate';
    protected $description = 'Migrasikan foto Home lama ke WebP statis/CDN tanpa request PHP saat pengunjung membuka gambar.';

    public function run(array $params)
    {
        $manager = new HomeAssetManager();
        $records = $manager->records();
        $force = in_array('--force', $params, true);
        $failed = 0;
        $done = 0;

        foreach ($manager->catalog() as $filename => $label) {
            if (!$force && isset($records[$filename])) {
                CLI::write('Lewati (sudah dikelola): ' . $filename, 'yellow');
                continue;
            }

            try {
                $manager->importLegacy($filename);
                CLI::write('Selesai: ' . $filename . ' — ' . $label, 'green');
                $done++;
            } catch (\Throwable $error) {
                CLI::error('Gagal: ' . $filename . ' — ' . $error->getMessage());
                $failed++;
            }
        }

        CLI::write("Migrasi selesai: {$done} berhasil, {$failed} gagal.");
        return $failed === 0 ? EXIT_SUCCESS : EXIT_ERROR;
    }
}
