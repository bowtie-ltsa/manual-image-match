<?php 
    declare(strict_types=1);

    // A DecisionList is the list of decisions made by a specific volunteer.
    class DecisionList {
        public const FILENAME_SUFFIX = "-decision-list.psv";
        public const FILEPATH_FMT = DATA_DIR . "%s" . self::FILENAME;

        // get an individual item in the list by position (zero-based)
        public static function DecisionAt(int $pos): Decision {
            throw new Exception("implement With Index")
        }

        public static function ForVolunteer(string $vid): DecisionList {
            throw new Exception("not implemented");
        }
        public $entireList; 

    }
?>