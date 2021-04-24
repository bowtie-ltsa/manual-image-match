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

        public function lock(int $timeoutMilliseconds = LOCKTIME): bool {
            if ($this->h) { throw new Exception("already locked"); }
            $this->h = fopen($this->name, 'w');
            if (!$this->h) return false;

            if ($timeoutMilliseconds <= 0) { $timeoutMilliseconds = 1; }

            while ($timeoutMilliseconds > 0) {
                if (flock($this->h, LOCK_EX)) {
                    return true;
                }
                sleep(1);
                $timeoutMilliseconds -= 1000; // this is not accurate but, whatever, close enough for now
            }
            return false;
        }

        public function unlock() {
            if (!$this->h) { throw new Exception("not locked"); }
            unlink($this->name);
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