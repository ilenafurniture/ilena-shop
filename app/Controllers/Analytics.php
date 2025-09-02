<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\M_Tracking;
use Dompdf\Dompdf;

class Analytics extends BaseController
{
    private function parseOpts(): array
    {
        // default: bulan ini
        $start = $this->request->getGet('start') ?: date('Y-m-01');
        $end   = $this->request->getGet('end')   ?: date('Y-m-t');

        $minDur = (int)($this->request->getGet('min_duration') ?? 5);
        $lowAvg = (int)($this->request->getGet('exclude_low_avg_duration') ?? 0);
        $maxDay = (int)($this->request->getGet('exclude_high_hits_per_day') ?? 0);

        // custom exclude IPs via query param: ?exclude_ips=1.2.3.4,5.6.7.8
        $excludeIps = [];
        $ipsParam = trim((string)$this->request->getGet('exclude_ips'));
        if ($ipsParam !== '') {
            $excludeIps = array_values(array_filter(array_map('trim', explode(',', $ipsParam))));
        }

        return [
            'start' => $start,
            'end'   => $end,
            'min_duration' => max(0, $minDur),
            'exclude_low_avg_duration' => $lowAvg,  // 0 = off
            'exclude_high_hits_per_day' => $maxDay,  // 0 = off
            'exclude_ips' => $excludeIps, // empty => pakai blacklist table
        ];
    }

    public function index()
    {
        $m = new M_Tracking();

        $opt = $this->parseOpts();
        $summaryNow  = $m->getSummary($opt);
        $topNow      = $m->getTopPaths($opt, 10);
        $dailyNow    = $m->getDailySeries($opt);

        $startPrev = date('Y-m-d', strtotime($opt['start'].' -1 month'));
        $endPrev   = date('Y-m-d', strtotime($opt['end'].' -1 month'));
        $optPrev          = $opt;
        $optPrev['start'] = $startPrev;
        $optPrev['end']   = $endPrev;

        $summaryPrev = $m->getSummary($optPrev);
        $topPrev     = $m->getTopPaths($optPrev, 10);
        $dailyPrev   = $m->getDailySeries($optPrev);

        // blacklist lengkap (ip + alasan) biar tabel admin enak
        $db = \Config\Database::connect();
        $blacklistFull = $db->table('tracking_ip_blacklist')
                            ->select('ip, alasan')
                            ->orderBy('ip', 'ASC')
                            ->get()->getResultArray();

        return view('admin/tracing', [
            'title'       => 'Insights Analytics',
            'navbar'      => session()->get('menu') ?? [],
            'metaTitle'   => 'Insights | ILENA',
            'metaDesc'    => 'Dashboard analytics pengunjung manusia dengan filter anti-bot.',
            'metaKeyword' => 'analytics, ilena, tracking, human traffic',

            'hideMegaNav' => true, // ⬅️ ini yang bikin mega-menu disembunyikan di navbar

            'opt'         => $opt,
            'optPrev'     => $optPrev,
            'summaryNow'  => $summaryNow,
            'summaryPrev' => $summaryPrev,
            'topNow'      => $topNow,
            'topPrev'     => $topPrev,
            'dailyNow'    => $dailyNow,
            'dailyPrev'   => $dailyPrev,
            'blacklist'   => $blacklistFull,
        ]);
    }

    /** Export CSV dari rangkuman & top paths & daily */
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
        foreach ($summary as $k=>$v) fputcsv($fh, [$k,$v]);

        fputcsv($fh, []);
        fputcsv($fh, ['Top Paths (max 100)']);
        fputcsv($fh, ['path','jumlah']);
        foreach ($top as $r) fputcsv($fh, [$r['path'] ?: '/', $r['jumlah']]);

        fputcsv($fh, []);
        fputcsv($fh, ['Daily Series']);
        fputcsv($fh, ['tanggal','hits']);
        foreach ($daily as $r) fputcsv($fh, [$r['tanggal'], $r['hits']]);

        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="'.$filename.'"')
            ->setBody($csv);
    }

    /** Export PDF (simple) */
    public function exportPdf()
    {
        $m = new M_Tracking();
        $opt = $this->parseOpts();

        $summary = $m->getSummary($opt);
        $top     = $m->getTopPaths($opt, 20);
        $daily   = $m->getDailySeries($opt);

        $html = view('analytics/pdf', [
            'opt'=>$opt, 'summary'=>$summary, 'top'=>$top, 'daily'=>$daily
        ]);

        $dompdf = new Dompdf(['chroot'=>WRITEPATH]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="analytics_'.$opt['start'].'_to_'.$opt['end'].'.pdf"')
            ->setBody($dompdf->output());
    }

    /** Tambah IP ke blacklist (opsional, via form POST) */
    public function addBlacklist()
    {
        $ip = trim((string)$this->request->getPost('ip'));
        $alasan = trim((string)$this->request->getPost('alasan'));

        if ($ip === '') return redirect()->back()->with('err','IP wajib diisi');

        $db = \Config\Database::connect();
        try {
            $db->table('tracking_ip_blacklist')->insert(['ip'=>$ip, 'alasan'=>$alasan ?: null]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('err','Gagal menambah: '.$e->getMessage());
        }
        return redirect()->back()->with('ok','IP ditambahkan ke blacklist');
    }

    public function delBlacklist($ip)
    {
        $db = \Config\Database::connect();
        $db->table('tracking_ip_blacklist')->where('ip',$ip)->delete();
        return redirect()->back()->with('ok','IP dihapus dari blacklist');
    }
}