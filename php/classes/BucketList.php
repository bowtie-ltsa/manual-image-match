<?php 
    declare(strict_types=1);

    // A BucketList is the list of opportunities left to the volunteer before they are finished with the study.
    // This list starts out the same as TheImagePairList and shrinks over time; the volunteer knocks things off the list.
    // Important: items are not removed from the BucketList until and unless the volunteer reports a decision for that opportunity.
    class BucketList extends OppList {
        public const FILENAME_SUFFIX = "-bucket-list.psv";
        public const FILEPATH_FMT = DATA_DIR . "%s" . self::FILENAME_SUFFIX;

        public static function ForVolunteer(string $vid): BucketList {
            $filepath = sprintf(self::FILEPATH_FMT, $vid);
            $mustInitialize = !file_exists($filepath);
            $bktList = new BucketList();
            parent::ForFile($filepath, $bktList);
            $bktList->vid = $vid;
            if ($mustInitialize) {
                $bktList->initialize();
            }
            return $bktList;
        }

        // instance variables and methods

        private $vid; // string, the id of the volunteer associated with this BucketList

        // called only when the volunteer's bucket list is first created.
        // this method creates a shuffled copy of TheImagePairList.
        private function initialize(): void {
            $this->lines = TheImagePairList::It()->GetAll(); // a copy of the image pair array
            shuffle($this->lines);
            $this->save();
        }

        public function IsEmpty(): bool {
            return parent::IsEmpty($this->filepath);
        }

        // Returns an Opportunity from the volunteer's BucketList. May consider $ipid when picking one. 
        // Adds that Opportunity to the BucketBoard. Does *not* remove it from the BucketList.
        // It is an error to call this method except when all other attempts to find an opportunity for the 
        // volunteer have failed; this is the last ditch solution to just get the volunteer "any" valid opportunity.
        // In particular, the BucketBoard is assumed to be empty (already checked) when this method is called.
        // Returns null if the volunteer's BucketList is empty. (In which case, the volunteer is finished with the study.)
        public function GetNewOpportunity(?string $ipid): ?Opportunity {
            $count = $this->Count();
            if ($count == 0) return null;
            $opp = $this->OpportunityAt($count - 1);
            BucketBoard::ForVolunteer($this->vid)->Add($opp);
            return $opp;
        }
    }
?>