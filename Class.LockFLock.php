<?php

class LockFLock
{
    private $lock = null;
    private $lockfile = null;

    /**
     * @param $lockfile The file to lock on (will be created if it does not exists)
     */
    public function __construct($lockfile)
    {
        $this->lockfile = $lockfile;
        $this->lock = fopen($this->lockfile, 'a');
        if ($this->lock === false) {
            throw new Exception(sprintf("Error opening lockfile '%s'.", $this->lockfile));
        }
    }

    /**
     * Acquire lock
     * @throws Exception
     */
    public function lock()
    {
        $ret = flock($this->lock, LOCK_EX);
        if ($ret === false) {
            throw new Exception(sprintf("Could not get lock on lockfile '%s'.", $this->lockfile));
        }
    }

    /**
     * Release lock
     * @throws Exception
     */
    public function unlock()
    {
        $ret = flock($this->lock, LOCK_UN);
        if ($ret === false) {
            throw new Exception(sprintf("Could not release lock on lockfile '%s'.", $this->lockfile));
        }
    }
}
