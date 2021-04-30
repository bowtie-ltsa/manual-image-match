<?php 
    declare(strict_types=1);

    // A DecisionList is the list of decisions made by a specific volunteer.
    class DecisionList extends OppList {
        public const FILENAME_SUFFIX = "-decision-list.psv";
        public const FILEPATH_FMT = DATA_DIR . "%s" . self::FILENAME_SUFFIX;

        public static function ForVolunteer(string $vid): DecisionList {
            $filepath = sprintf(self::FILEPATH_FMT, $vid);
            $decList = new DecisionList();
            parent::ForFile($filepath, $decList);
            $decList->vid = $vid;
            return $decList;
        }

        // instance variables and methods

        private $vid; // string, the id of the volunteer associated with this DecisionList

        public function DecisionAt(?int $pos): ?Decision {
            return $this->OpportunityAt($pos);
        }
        
    }
?>