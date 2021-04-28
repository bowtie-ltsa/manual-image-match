<?php 
    declare(strict_types=1);

    // A BucketList is the list of opportunities left to the volunteer before they are finished with the study.
    // This list starts out the same as TheImagePairList and shrinks over time; the volunteer knocks things off the list.
    // Important: items are not removed from the BucketList until and unless the volunteer reports a decision for that opportunity.
    class BucketList {
        public const FILENAME_SUFFIX = "-bucket-list.psv";
        public const FILEPATH_FMT = DATA_DIR . "%s" . self::FILENAME;

        // get an individual item in the list by position (zero-based)
        public static function DecisionAt(int $pos): Opportunity {
            throw new Exception("implement With Index")
        }

        public static function ForVolunteer(string $vid): BucketList {
            throw new Exception("not implemented");
        }
        public $entireList; 

        // returns any item from the BucketList. may consider $ipid.
        public static function GetNewOpportunity(string $vid, ?string $ipid): Opportunity {
            throw new Exception("not implemented");
        }
    }
?>