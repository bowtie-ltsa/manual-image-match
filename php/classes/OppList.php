<?php 
    declare(strict_types=1);

    // for at least, we keep the static classes static, but try to keep the code DRY.
    // eventually we may take the time to create a generic, nonstatic OpportunityList....
    class OppList {
        public const HEADERS = "ipid|path1|path2|vidList|decision";

        public static function HeadersArray(): array { 
            if (self::$headersArray == null) {
                self::$headersArray = explode(PIPE, self::HEADERS);
            }
            return self::$headersArray;
        }
        private static $headersArray = null;
        
        public static function Array(string $line): array { 
            return array_combine(self::HeadersArray(), explode(PIPE, $line));
        }

        public static function Line(Opportunity $opp): string { 
            $line = null;
            foreach(OppList::HeadersArray() as $header) {
                $value = $opp->{$header};
                if ($line == null) { $line = $value; }
                elseif ($header != "vidList") { $line = $line . PIPE . $value; }
                else { $line = $line . PIPE . implode(",", $value); }
            }
            return $line;
        }

        public static function Opportunity(string $line): Opportunity {
            $opportunity = new Opportunity();
            foreach(self::Array($line) as $key => $value) {
                if ($key != "vidList") { $opportunity->{$key} = $value; }
                elseif ($value != "" && $value != null) { $opportunity->vidList = explode(",", $value); }
                else { $opportunity->vidList = array(); }
            }
            return $opportunity;
        }

        // $lists is a map[$vid]OppList objects that are in memory
        // so we don't have to load them multiple times for the same request
        protected static $lists = array(); 
        public function foo() { return self::$lists; }

        public static function ForFile(string $filepath, OppList &$subtype) {
            Log::In();
            Log::Mention(__METHOD__);
            $list = @self::$lists[$filepath];
            $src = "";
            if ($list == null) {
                $list = self::Load($filepath, $subtype);
                self::$lists[$filepath] = $list;
                $src = "load";
            } else { 
                $subtype = $list;
                $src = "cache";
            }
            Log::Mention("OppList Loaded", Log::Brace("filename", basename($filepath), "src", $src, "count", count($list->lines)));
            Log::Out();
        }

        public static function Load(string $filepath, OppList &$subtype): OppList {
            if (!file_exists($filepath)) {
                $emptyList = self::HEADERS . PHP_EOL;
                file_put_contents($filepath, $emptyList);
                Log::Mention("OppList File Created", basename($filepath));
            }
            $subtype->filepath = $filepath;

            // for now at least we read the entire list into memory; in future we can be more memory-efficent
            $lines = file($filepath, FILE_IGNORE_NEW_LINES);
            $header = array_shift($lines);
            $subtype->lines = $lines;

            return $subtype;
        }

        // instance variables and methods

        protected $filepath;
        protected $lines; // a simple array of strings

        public function IsEmpty(): bool {
            // simple, for now at least, since we keep the entire list in memory
            return count($this->lines) == 0;
        }

        public function Count(): int {
            // simple, for now at least, since we keep the entire list in memory
            return count($this->lines);
        }

        // get an individual item in the list by position (zero-based)
        public function OpportunityAt(?int $pos): ?Opportunity {
            if ($pos === null) { return null; }
            $line = @$this->lines[$pos];
            if ($line == null) { return null; }
            $opp = OppList::Opportunity($line);
            $opp->index = $pos;
            return $opp;
        }

        public function Update(Opportunity $opp): void {
            $i = $opp->index;
            if ($i === null) {
                throw Log::PanicException("panic: index must not be null");
            }
            if ($i < 0 or $i > count($this->lines)) {
                $count = count($this->lines);
                throw Log::PanicException("panic: unexpected index $i with count=$count");
            }
            $this->lines[$i] = self::Line($opp);
        }

        public function Contains(Opportunity $opp): bool {
            // keeping it simple for now - just scan. In future we could do something like maintain an index (on disk and maybe in memory).
            $ipid = $opp->ipid . PIPE;
            $n = strlen($ipid);
            foreach($this->lines as $line) {
                if (strncmp($line, $ipid, $n) === 0) {
                    return true;
                }
            }
            return false;
        }

        public function RemoveAt(int $i): void {
            $count = count($this->lines);
            if ($i < 0 || $i >= $count) {
                throw Log::PanicException("panic: unexpected value $i when count(lines)=$count.");
            }
            unset($this->lines[$i]);
            if ($i < $count-1) {
                $this->lines = array_values($this->lines);
            }
            $this->save();
        }

        protected function save(): void {
            file_put_contents($this->filepath, OppList::HEADERS . PHP_EOL . implode(PHP_EOL, $this->lines));
        }

        public function Add(Opportunity $opp): void {
            if ($this->Contains($opp)) {
                throw Log::PanicException("panic: opportunity is already part of $this->filepath: $opp->ipid");
            }
            $this->lines[] = self::Line($opp);
            $this->save();
        }

    }
?>