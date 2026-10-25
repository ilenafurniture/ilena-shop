<?php
namespace App\Services;

/** Call inside a transaction. Sequence rows serialize numbering across orders. */
final class DocumentSequence
{
    public static function next($db, string $key, int $legacyMaximum = 0): int
    {
        $table=$db->protectIdentifiers($db->prefixTable('document_sequences'));
        if ($db->DBDriver === 'MySQLi') {
            $db->query("INSERT IGNORE INTO $table (sequence_key, counter) VALUES (?, 0)",[$key]);
        } elseif ($db->DBDriver === 'SQLite3') {
            $db->query("INSERT OR IGNORE INTO $table (sequence_key, counter) VALUES (?, 0)",[$key]);
        } else { throw new \RuntimeException('Driver sequence dokumen belum didukung.'); }
        $sql=$db->table('document_sequences')->where('sequence_key',$key)->getCompiledSelect();
        $row=$db->query($sql.($db->DBDriver==='MySQLi'?' FOR UPDATE':''))->getRowArray();
        $next=max((int)$row['counter'],$legacyMaximum)+1;
        if (!$db->table('document_sequences')->where('sequence_key',$key)->update(['counter'=>$next])) throw new \RuntimeException('Nomor dokumen gagal disimpan.');
        return $next;
    }
}
