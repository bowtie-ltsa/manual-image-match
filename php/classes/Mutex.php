<?php 
    declare(strict_types=1);
    require_once "first-things.php";

    // provide a simple mutex based on file lock, apparently the best we can do in hosted web server environment. flock is available on all php platforms.
    class Mutex {
        public $name;
        private $h;
        
        public function __construct(string $name) { 
            $this->name = DATA_DIR . $name . ".lock";
            $this->h = null;
        }

        public function Lock(int $timeoutMilliseconds = LOCKTIME): bool {
            if ($this->h) { throw new Exception("already locked"); }
            $this->h = fopen($this->name, 'w');
            if (!$this->h) return false;

            if ($timeoutMilliseconds <= 0) { $timeoutMilliseconds = 1; }

            while ($timeoutMilliseconds > 0) {
                if (flock($this->h, LOCK_EX)) {
                    // global $vid;
                    // if ($vid == 'slowpoke') { sleep(7); } // useful for testing
                    return true;
                }
                sleep(1);
                $timeoutMilliseconds -= 1000; // this is not accurate but, whatever, close enough for now
            }
            return false;
        }

        public function Unlock() {
            if (!$this->h) { throw new Exception("not locked"); }
            // do not unlink (delete) the file! because thread #2 is waiting up there and has already
            // opened it. thread #1 deletes it while #2 is waiting, then it will be gone *while* #2
            // executes critical section code(!). Kind of a shame to leave the lock file hanging out
            // there (clutter) but the alternative is a disaster. This is discussed at 
            // https://stackoverflow.com/q/32904928 (see comment!) and a real alternative is offered 
            // at https://stackoverflow.com/a/33105860. Though we could just use a do-not-delete flag
            // file instead (created by threads 2, 3, 4...)... probably....
            // unlink($this->name); <-- bad idea, don't do this. leave the file in place.
            flock($this->h, LOCK_UN);
            fclose($this->h);
            $this->h = null;
        }
    }

    // instead of using flock() consider using @fopen(... 'xb')
    // cf https://github.com/cubiclesoft/efss/blob/fd03275a752e53696419327dea1ac853023ab0b9/support/web_mutex.php
    // $fp = @fopen($this->name . ".lock", "xb");
    // if ($fp === false && $maxlock !== false && $maxlock > 0)
    // {
    //     $ts = @filemtime($this->name . ".lock");
    //     if ($ts !== false && time() - $ts > $maxlock)
    //     {
    //         $fp = @fopen($this->name . ".stale", "xb");
    //         if ($fp !== false)
    //         {
    //             @unlink($this->name . ".lock");
    //             fclose($fp);
    //             $fp = @fopen($this->name . ".lock", "xb");
    //             @unlink($this->name . ".stale");
    //         }
    //     }
    // }

?>