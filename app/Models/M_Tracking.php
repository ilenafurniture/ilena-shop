<?php
namespace App\Models;

use CodeIgniter\Model;

class M_Tracking extends Model
{
    protected $table         = 'tracking';
    protected $allowedFields = ['waktu','ip','path','durasi'];
    public $useTimestamps    = false;

    protected $defaultHumanMin = 5; // detik

    /** Ambil daftar IP blacklist */
    public function getBlacklist(): array
    {
        $db = \Config\Database::connect();
        return $db->table('tracking_ip_blacklist')->select('ip')->get()->getResultArray();
    }

    /** Build builder dengan semua filter */
    private function baseFilteredBuilder(array $opt)
    {
        $db = $this->db;
        $builder = $db->table($this->table);

        // Range waktu
        $start = $opt['start'] ?? null;
        $end   = $opt['end']   ?? null;
        if ($start) $builder->where('waktu >=', $start.' 00:00:00');
        if ($end)   $builder->where('waktu <',  date('Y-m-d H:i:s', strtotime($end.' +1 day')));

        // Filter manusia (durasi minimal)
        $minDur = isset($opt['min_duration']) ? (int)$opt['min_duration'] : $this->defaultHumanMin;
        if ($minDur > 0) $builder->where('durasi >=', $minDur);

        // Blacklist IP
        $blacklist = $opt['exclude_ips'] ?? array_map(fn($r)=>$r['ip'], $this->getBlacklist());
        if (!empty($blacklist)) $builder->whereNotIn('ip', $blacklist);

        // Anti-bot tambahan (opsional):
        // exclude IP dengan rata-rata durasi < X detik
        if (!empty($opt['exclude_low_avg_duration']) && (int)$opt['exclude_low_avg_duration'] > 0) {
            // ambil ip2 yang avg durasi-nya < threshold dalam range ini, lalu exclude
            $sub = $db->table($this->table)
                ->select('ip, AVG(durasi) AS avgd')
                ->groupStart()
                    ->where('durasi IS NOT NULL')
                ->groupEnd();

            if ($start) $sub->where('waktu >=', $start.' 00:00:00');
            if ($end)   $sub->where('waktu <',  date('Y-m-d H:i:s', strtotime($end.' +1 day')));

            $rows = $sub->groupBy('ip')->having('AVG(durasi) <', (int)$opt['exclude_low_avg_duration'])->get()->getResultArray();
            $lowAvgIps = array_column($rows, 'ip');
            if (!empty($lowAvgIps)) $builder->whereNotIn('ip', $lowAvgIps);
        }

        // exclude IP dengan hit per hari >= N
        if (!empty($opt['exclude_high_hits_per_day']) && (int)$opt['exclude_high_hits_per_day'] > 0) {
            $threshold = (int)$opt['exclude_high_hits_per_day'];
            $sub2 = $db->table($this->table)
                ->select('ip, DATE(waktu) as d, COUNT(*) as c');

            if ($start) $sub2->where('waktu >=', $start.' 00:00:00');
            if ($end)   $sub2->where('waktu <',  date('Y-m-d H:i:s', strtotime($end.' +1 day')));

            $rows2 = $sub2->groupBy('ip, DATE(waktu)')->having('COUNT(*) >=', $threshold)->get()->getResultArray();
            $badIps = array_unique(array_column($rows2, 'ip'));
            if (!empty($badIps)) $builder->whereNotIn('ip', $badIps);
        }

        return $builder;
    }

    /** Ringkasan KPI */
    public function getSummary(array $opt): array
    {
        $builder = $this->baseFilteredBuilder($opt);
        $row = $builder
            ->select('COUNT(*) AS total_tracking, COUNT(DISTINCT ip) AS total_ip_unik, COUNT(DISTINCT path) AS total_path_unik, COALESCE(SUM(durasi),0) AS total_durasi')
            ->get()->getRowArray() ?? [];

        return [
            'total_tracking'  => (int)($row['total_tracking'] ?? 0),
            'total_ip_unik'   => (int)($row['total_ip_unik'] ?? 0),
            'total_path_unik' => (int)($row['total_path_unik'] ?? 0),
            'total_durasi'    => (int)($row['total_durasi'] ?? 0),
        ];
    }

    /** Top N path */
    public function getTopPaths(array $opt, int $limit = 10): array
    {
        $builder = $this->baseFilteredBuilder($opt);
        return $builder
            ->select('path, COUNT(*) AS jumlah')
            ->groupBy('path')
            ->orderBy('jumlah', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    /** Seri harian */
    public function getDailySeries(array $opt): array
    {
        $db = $this->db;
        // rolling builder untuk membaca group by date
        $builder = $this->baseFilteredBuilder($opt);
        $rows = $builder
            ->select('DATE(waktu) AS tgl, COUNT(*) AS hits')
            ->groupBy('DATE(waktu)')
            ->orderBy('tgl','ASC')
            ->get()->getResultArray();

        // normalisasi tanggal kosong
        $start = $opt['start'] ?? date('Y-m-01');
        $end   = $opt['end']   ?? date('Y-m-t');
        $startTs = strtotime($start);
        $endTs   = strtotime($end);

        // jika end < start, swap
        if ($endTs < $startTs) { $tmp = $startTs; $startTs = $endTs; $endTs = $tmp; }

        $map = [];
        foreach ($rows as $r) $map[$r['tgl']] = (int)$r['hits'];

        $out = [];
        $cursor = $startTs;
        while ($cursor <= $endTs) {
            $d = date('Y-m-d', $cursor);
            $out[] = ['tanggal' => $d, 'hits' => ($map[$d] ?? 0)];
            $cursor = strtotime('+1 day', $cursor);
        }
        return $out;
    }

    public function countOnlineNow(int $minutes = 5): int
{
    $since = date('Y-m-d H:i:s', time() - $minutes * 60);
    $row = $this->db->table($this->table)
        ->select('COUNT(DISTINCT ip) AS c')
        ->where('waktu >=', $since)
        ->get()->getRowArray();
    return (int)($row['c'] ?? 0);
}

}