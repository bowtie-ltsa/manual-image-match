<?php 
    declare(strict_types=1);

    // A BucketBoard is a "board" that can only have up to one opportunity on it. It is the opportunity that the volunteer
    // has been given from their BucketList, in the special circumstance where they cannot be given an opportuntiy from the
    // OpportunityList, nor from the OpportunityBoard, because they've already provided a decision for all opportunties on
    // those lists. And yet their BucketList is not empty. (They still have decisions they could add to their DecisionList.)
    // So we give them one from their BucketList, and use the BucketBoard to keep track of it. This will add a decision beyond
    // the goal of the current round, but this is better than telling the volunteer to wait for the round to end (which might
    // be a while). The volunteer is essentially helping to get a head start on a future round.
    class BucketBoard {
        public const FILENAME_SUFFIX = "-bucket-list.psv";
        public const FILEPATH_FMT = DATA_DIR . "%s" . self::FILENAME;

        // get an individual item in the list by position (zero-based)
        public static function DecisionAt(int $pos): Opportunity {
            throw new Exception("implement With Index")
        }

        public static function ForVolunteer(string $vid): BucketBoard {
            throw new Exception("not implemented");
        }
        public $entireList; 

        // returns the (one and only) opportunity from the volunteer's BucketBoard -- if any.
        // returns null if none.
        public static function GetExistingOpportunity(string $vid): Opportunity {
            throw new Exception("not implemented");
        }

    }
?>