<?php

class LockPgLockTable
{
    private $dsn = null;
    private $conn = null;

    /**
     * @param $dsn string Postgresql connection string
     * @param $table string The name of the table to lock on (should exists prior usage)
     * @throws Exception
     */
    public function __construct($dsn, $table)
    {
        $this->dsn = $dsn;
        $this->table = $table;
        $this->conn = pg_connect($this->dsn);
        if ($this->conn === false) {
            throw new Exception(sprintf("Error connecting to Postgresql with DSN '%s': %s", $this->dsn, pg_last_error()));
        }
        $ret = pg_prepare($this->conn, 'lock', sprintf('LOCK TABLE "%s"', pg_escape_string($this->table)));
        if ($ret === false) {
            throw new Exception(sprintf("Error preparing lock command: %s", pg_last_error($this->conn)));
        }
        $ret = pg_query($this->conn, 'BEGIN');
        if ($ret === false) {
            $err = pg_last_error($this->conn);
            pg_close($this->conn);
            throw new Exception(sprintf("Error beginning transaction: %s", $err));
        }
    }

    public function __destruct()
    {
        pg_close($this->conn);
    }

    /**
     * Acquire lock
     * @throws Exception
     */
    public function lock()
    {
        $ret = pg_execute($this->conn, 'lock', array());
        if ($ret === false) {
            throw new Exception(sprintf("Could not get lock on table '%s': %s", $this->table, pg_last_error($this->conn)));
        }
    }

    /**
     * Release lock
     * @throws Exception
     */
    public function unlock()
    {
        $ret = pg_query('ROLLBACK');
        if ($ret === false) {
            throw new Exception(sprintf("Could not release lock: %s", pg_last_error($this->conn)));
        }
    }
}
