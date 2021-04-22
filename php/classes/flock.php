<?php 
    declare(strict_types=1);
    require_once "first-things.php";

    // provide a simple mutex based on file lock, apparently the best we can do in hosted web server environment
    class Flock {
        public string $name = "(not set)";
        private $h;
        
        public function __construct(string $name) { 
            $this->name = DATADIR . $name;
            $this->h = null;
        }

        public function lock(int $timeoutMilliseconds): bool {
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
            flock($this->h, LOCK_UN);
            fclose($this->h);
            $this->h = null;
        }
    }
?>