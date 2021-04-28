<?php 
    declare(strict_types=1);

    // A DecisionList is the list of decisions made by a specific volunteer.
    class DecisionList {
        public const FILENAME_SUFFIX = "-decision-list.psv";
        public const FILEPATH_FMT = DATA_DIR . "%s" . self::FILENAME_SUFFIX;

        public static function VidFilepath(string $vid): string {
            return sprintf(self::FILEPATH_FMT, $vid);
        }

        // $lists is a map[$vid]DecisionList objects that are in memory
        // so we don't have to load them multiple times for the same request
        private static $lists = array(); 

        public static function ForVolunteer(string $vid): DecisionList {
            $list = @self::$lists[$vid];
            if ($list == null) {
                $list = new DecisionList($vid);
                self::$lists[$vid] = $list;
            }
            return $list;
        }

        // get an individual item in the list by position (zero-based)
        public static function DecisionAt(?int $pos): ?Decision {
            if ($pos == null) { return null; }
            $line = @$this->lines[$pos];
            if ($line == null) { return null; }
            return Opportunity::FromLine($line);
        }

        public function __construct(string $vid) {
            $filepath = self::VidFilepath($vid);
            if (!file_exists($filepath)) {
                $emptyList = OppList::HEADERS . PHP_EOL;
                file_put_contents($filepath, $emptyList);
            }
            $this->filepath = $filepath;

            // for now at least we read the entire list into memory; in future we can be more memory-efficent
            $lines = file($filepath);
            $header = array_shift($lines);
            $this->lines = $lines;
        }

        private $filepath;
        private $lines; // a simple array of strings
    }
?>