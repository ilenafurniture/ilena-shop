<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\M_Tracking;
use App\Services\AnalyticsCsvSanitizer;
use App\Services\AnalyticsOptions;
use Dompdf\Dompdf;

class Analytics extends BaseController
{
    /**
     * Ambil & normalisasi opsi filter dari query string.
     * Default range = bulan ini.
     */
    private function parseOpts(bool $live = false): array
    {
        $defaultEnd = $live ? date('Y-m-d') : date('Y-m-t');
        return AnalyticsOptions::normalize($this->request->getGet() ?? [], $defaultEnd);
    }

    /**
     * Halaman utama tracing (admin).
     * NOTE: view diganti ke 'admin/tracing'
     */
    public function index()
    {
        $m = new M_Tracking();

        $opt = $this->parseOpts();
        $summaryNow  = $m->getSummary($opt);
        $topNow      = $m->getTopPaths($opt, 10);
        $dailyNow    = $m->getDailySeries($opt);

        $startPrev = date('Y-m-d', strtotime($opt['start'].' -1 month'));
        $endPrev   = date('Y-m-d', strtotime($opt['end'].' -1 month'));
        $optPrev   = $opt; 
        $optPrev['start'] = $startPrev; 
        $optPrev['end']   = $endPrev;

        $summaryPrev = $m->getSummary($optPrev);
        $topPrev     = $m->getTopPaths($optPrev, 10);
        $dailyPrev   = $m->getDailySeries($optPrev);

        // 👉 Tambahkan ini untuk hitung pengunjung online 5 menit terakhir
        $onlineNow   = $m->countOnlineNow(5);

        return view('admin/tracing', [
            'title'       => 'Insights Analytics',
            'navbar'      => session()->get('menu') ?? [],
            'metaTitle'   => 'Insights | ILENA',
            'metaDesc'    => 'Dashboard analytics pengunjung manusia dengan filter anti-bot.',
            'metaKeyword' => 'analytics, ilena, tracking, human traffic',

            'opt'         => $opt,
            'optPrev'     => $optPrev,
            'summaryNow'  => $summaryNow,
            'summaryPrev' => $summaryPrev,
            'topNow'      => $topNow,
            'topPrev'     => $topPrev,
            'dailyNow'    => $dailyNow,
            'dailyPrev'   => $dailyPrev,
            'blacklist'   => $m->getBlacklist(),
            // kirim ke view
            'onlineNow'   => $onlineNow,
        ]);
    }


    /**
     * ✅ Endpoint JSON realtime untuk auto-refresh front-end.
     * End date fix ke HARI INI, dengan header no-cache.
     */
    public function live()
    {
        $m = new M_Tracking();

        $opt = $this->parseOpts(true);

        $summaryNow = $m->getSummary($opt);
        $topNow     = $m->getTopPaths($opt, 10);
        $dailyNow   = $m->getDailySeries($opt);

        $startPrev  = date('Y-m-d', strtotime($opt['start'].' -1 month'));
        $endPrev    = date('Y-m-d', strtotime($opt['end'].' -1 month'));
        $optPrev    = $opt; $optPrev['start'] = $startPrev; $optPrev['end'] = $endPrev;

        $summaryPrev = $m->getSummary($optPrev);
        $topPrev     = $m->getTopPaths($optPrev, 10);
        $dailyPrev   = $m->getDailySeries($optPrev);

        // (opsional) jika kamu punya fitur “onlineNow”
        $onlineNow = $m->countOnlineNow(5);

        return $this->response->setJSON([
            'server_time'  => date('Y-m-d H:i:s'),
            'opt'          => $opt,
            'summaryNow'   => $summaryNow,
            'summaryPrev'  => $summaryPrev,
            'topNow'       => $topNow,
            'topPrev'      => $topPrev,
            'dailyNow'     => $dailyNow,
            'dailyPrev'    => $dailyPrev,
            'onlineNow'  => $onlineNow ?? null,
        ]);
    }


    /**
     * Export CSV dari summary + top paths + daily series.
     */
    public function exportCsv()
    {
        $m = new M_Tracking();
        $opt = $this->parseOpts();

        $summary = $m->getSummary($opt);
        $top     = $m->getTopPaths($opt, 100);
        $daily   = $m->getDailySeries($opt);

        $filename = 'analytics_'.$opt['start'].'_to_'.$opt['end'].'.csv';
        $fh = fopen('php://temp', 'w+');

        fputcsv($fh, ['Summary']);
        foreach ($summary as $k => $v) {
            fputcsv($fh, [$k, $v]);
        }

        fputcsv($fh, []);
        fputcsv($fh, ['Top Paths (max 100)']);
        fputcsv($fh, ['path', 'jumlah']);
        foreach ($top as $r) {
            fputcsv($fh, [AnalyticsCsvSanitizer::text((string) ($r['path'] ?: '/')), $r['jumlah']]);
        }

        fputcsv($fh, []);
        fputcsv($fh, ['Daily Series']);
        fputcsv($fh, ['tanggal', 'hits']);
        foreach ($daily as $r) {
            fputcsv($fh, [$r['tanggal'], $r['hits']]);
        }

        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="'.$filename.'"')
            ->setBody($csv);
    }

    /**
     * Export PDF sederhana (layout ada di view 'analytics/pdf').
     */
    public function exportPdf()
    {
        $m = new M_Tracking();
        $opt = $this->parseOpts();

        $summary = $m->getSummary($opt);
        $top     = $m->getTopPaths($opt, 20);
        $daily   = $m->getDailySeries($opt);

        $html = view('analytics/pdf', [
            'opt'     => $opt,
            'summary' => $summary,
            'top'     => $top,
            'daily'   => $daily,
        ]);

        $dompdf = new Dompdf(['chroot' => WRITEPATH]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader(
                'Content-Disposition',
                'attachment; filename="analytics_'.$opt['start'].'_to_'.$opt['end'].'.pdf"'
            )
            ->setBody($dompdf->output());
    }

    /**
     * Tambah IP ke blacklist (via form POST).
     */
    public function addBlacklist()
    {
        $ipInput = $this->request->getPost('ip');
        $reasonInput = $this->request->getPost('alasan');
        $ip     = is_string($ipInput) ? trim($ipInput) : '';
        $alasan = is_string($reasonInput) ? trim($reasonInput) : '';

        if ($ip === '') {
            return redirect()->back()->with('err', 'IP wajib diisi');
        }
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return redirect()->back()->with('err', 'Format IP tidak valid');
        }

        $db = \Config\Database::connect();
        try {
            $db->table('tracking_ip_blacklist')->insert([
                'ip'     => $ip,
                'alasan' => $alasan === '' ? null : mb_substr($alasan, 0, 180),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Analytics blacklist insert failed: {message}', ['message' => $e->getMessage()]);
            return redirect()->back()->with('err', 'Gagal menambah IP ke blacklist.');
        }

        return redirect()->back()->with('ok', 'IP ditambahkan ke blacklist');
    }

    /**
     * Hapus IP dari blacklist.
     */
    public function delBlacklist($ip)
    {
        $ip = is_string($ip) ? trim($ip) : '';
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return redirect()->back()->with('err', 'Format IP tidak valid');
        }

        $db = \Config\Database::connect();
        $db->table('tracking_ip_blacklist')->where('ip', $ip)->delete();
        return redirect()->back()->with('ok', 'IP dihapus dari blacklist');
    }
}
