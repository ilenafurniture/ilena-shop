<?php
namespace App\Services;

/** Roll back on every early return/exception unless the complete operation commits. */
final class AtomicOperation
{
    private bool $active = false;
    private bool $locked = false;
    public function __construct(
        private $db,
        private string $lockName = 'ilena-documents',
        bool $beginTransaction = true
    )
    {
        if ($db->DBDriver==='MySQLi') {
            $row=$db->query('SELECT GET_LOCK(?, 10) AS acquired',[$lockName])->getRowArray();
            if ((int)($row['acquired']??0)!==1) throw new \RuntimeException('Operasi lain sedang diproses. Coba kembali.');
            $this->locked=true;
        }
        if ($beginTransaction) {
            $this->beginTransaction();
        }
    }

    /** Begin the database transaction after lock-protected non-DB work is complete. */
    public function beginTransaction(): void
    {
        if ($this->active) {
            throw new \LogicException('Transaksi operasi sudah dimulai.');
        }
        $this->db->transBegin();
        $this->active = true;
    }
    public function commit(): void
    {
        if (!$this->db->transStatus()) throw new \RuntimeException('Transaksi gagal disimpan; perubahan dibatalkan.');
        if (!$this->db->transCommit()) throw new \RuntimeException('Commit transaksi gagal.');
        $this->active=false; $this->release();
    }

    public function rollback(bool $releaseLock = true): void
    {
        if ($this->active) {
            $this->db->transRollback();
            $this->active = false;
        }
        if ($releaseLock) {
            $this->release();
        }
    }

    /** Release the per-operation lock after non-DB compensation has completed. */
    public function releaseLock(): void
    {
        $this->release();
    }
    private function release(): void
    {
        if ($this->locked) { $this->db->query('SELECT RELEASE_LOCK(?)',[$this->lockName]); $this->locked=false; }
    }
    public function __destruct()
    {
        try { if ($this->active) $this->db->transRollback(); $this->release(); }
        catch (\Throwable $e) { log_message('error','Atomic rollback failed: {error}',['error'=>$e->getMessage()]); }
    }
}
