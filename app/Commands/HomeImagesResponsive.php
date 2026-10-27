<?php

namespace App\Commands;

use App\Services\HomeAssetManager;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class HomeImagesResponsive extends BaseCommand
{
    protected $group = 'Media';
    protected $name = 'home-images:responsive';
    protected $description = 'Buat varian WebP responsif untuk aset Home R2 yang sudah dimigrasikan.';

    public function run(array $params)
    {
        $result = (new HomeAssetManager())->backfillResponsiveVariants();

        foreach ($result['updated'] as $filename => $label) {
            CLI::write('Selesai: ' . $filename . ' — ' . $label, 'green');
        }
        foreach ($result['skipped'] as $filename => $reason) {
            CLI::write('Lewati: ' . $filename . ' — ' . $reason, 'yellow');
        }
        foreach ($result['failed'] as $filename => $reason) {
            CLI::error('Gagal: ' . $filename . ' — ' . $reason);
        }

        $failed = count($result['failed']);
        CLI::write(sprintf(
            'Varian responsif selesai: %d dibuat, %d dilewati, %d gagal.',
            count($result['updated']),
            count($result['skipped']),
            $failed
        ));

        return $failed === 0 ? EXIT_SUCCESS : EXIT_ERROR;
    }
}
