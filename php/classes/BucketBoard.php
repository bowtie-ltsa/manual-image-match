<?php 
    declare(strict_types=1);

    // A BucketBoard is a "board" that can only have up to one opportunity on it. It is the opportunity that the volunteer
    // has been given from their BucketList, in the special circumstance where they cannot be given an opportuntiy from the
    // OpportunityList, nor from the OpportunityBoard, because they've already provided a decision for all opportunties on
    // those lists. And yet their BucketList is not empty. (They still have decisions they could add to their DecisionList.)
    // So we give them one from their BucketList, and use the BucketBoard to keep track of it. This will add a decision beyond
    // the goal of the current round, but this is better than telling the volunteer to wait for the round to end (which might
    // be a while). The volunteer is essentially helping to get a head start on a future round.
    class BucketBoard extends OppList {
        public const FILENAME_SUFFIX = "-bucket-board.psv";
        public const FILEPATH_FMT = DATA_DIR . "%s" . self::FILENAME_SUFFIX;

        // called at the start of a new round.
        public static function ClearAll() {
            foreach(glob(DATA_DIR . "*" . self::FILENAME_SUFFIX) as $bbFile) {
                unlink($bbFile);
                unset(self::$lists[$bbFile]);
            }
        }

        public static function ForVolunteer(string $vid): BucketBoard {
            Log::In();
            Log::Mention(__METHOD__);
            $filepath = sprintf(self::FILEPATH_FMT, $vid);
            $bktBoard = new BucketBoard();
            parent::ForFile($filepath, $bktBoard);
            $bktBoard->vid = $vid;
            Log::Note("BucketBoard Loaded", $bktBoard->Count());
            Log::Out();
            return $bktBoard;
        }

        // instance variables and methods

        private $vid; // string, the id of the volunteer associated with this BucketBoard

        // returns the (one and only) opportunity from the volunteer's BucketBoard -- if any.
        // returns null if none.
        public function GetExistingOpportunity(): ?Opportunity {
            Log::In();
            Log::Mention(__METHOD__);
            if (count($this->lines) == 0) { 
                Log::Mention("No Existing Opportunity from the vid's Bucket Board");
                Log::Out();
                    return null; 
            }
            $opp = Opportunity::FromLine($this->lines[0]);
            Log::Event("Use Existing Opportunity from the vid's Bucket Board", $opp->String());
            Log::Out();
            return $opp;
        }

    }
?>