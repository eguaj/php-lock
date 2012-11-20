<?php

switch ($argv[1]) {
    case 'LockFLock':
        _lockflock();
        break;
    case 'LockPgLockTable':
        _lockpglocktable();
        break;
    default:
        printf("Usage: %s <'LockFLock'|'LockPgLockTable'>\n", basename(__FILE__));
}

function _lockflock()
{
    include_once('Class.LockFLock.php');
    $lock = new LockFLock('foo.lock');
    print "Waiting for lock... ";
    $lock->lock();
    print "Got lock!\n";
    work();
    $lock->unlock();
}

function _lockpglocktable()
{
    include_once('Class.LockPgLockTable.php');
    $lock = new LockPgLockTable('service=lock', 'lock_table');
    print "Waiting for lock... ";
    $lock->lock();
    print "Got lock!\n";
    work();
    $lock->unlock();
}

function work()
{
    print "Processing";
    for ($i = 10; $i > 0; $i--) {
        sleep(1);
        print ".";
    }
    print " Done.\n";
}
