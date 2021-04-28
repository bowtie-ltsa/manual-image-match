<?php 
    declare(strict_types=1);

    // for at least, we keep the static classes static, but try to keep the code DRY.
    // eventually we may take the time to create a generic, nonstatic OpportunityList....
    class OppList {
        public const HEADERS = "ipid|path1|path2|vidlist|decision";

        public static function HeadersArray(): array { return explode(PIPE, self::HEADERS); }
        public static function Array(string $line): array { return explode(PIPE, $line); }

        // $lists is a map[$vid]OppList objects that are in memory
        // so we don't have to load them multiple times for the same request
        protected static $lists = array(); 
        public function foo() { return self::$lists; }

        public static function ForFile(string $filepath, OppList &$subtype) {
            $list = @self::$lists[$filepath];
            if ($list == null) {
                $list = self::Load($filepath, $subtype);
                self::$lists[$filepath] = $list;
            } else { 
                $subtype = $list; 
            }
        }

        public static function Load(string $filepath, OppList &$subtype): OppList {
            if (!file_exists($filepath)) {
                $emptyList = self::HEADERS . PHP_EOL;
                file_put_contents($filepath, $emptyList);
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
            // simple, for now at least, since we keep it in memory
            return count($this->lines) == 0;
        }

        // get an individual item in the list by position (zero-based)
        public function OpportunityAt(?int $pos): ?Opportunity {
            if ($pos == null) { return null; }
            $line = @$this->lines[$pos];
            if ($line == null) { return null; }
            return Opportunity::FromLine($line);
        }

    }
?>