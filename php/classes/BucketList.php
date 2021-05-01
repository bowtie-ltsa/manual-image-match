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
        // this method creates a copy of TheImagePairList, which should be in sorted order.
        private function initialize(): void {
            $this->lines = TheImagePairList::It()->GetAll(); // a copy of the image pair array
            $this->save(); // expected to be in sorted order!
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
            Log::In();
            Log::Mention(__METHOD__);
            $count = $this->Count();
            if ($count == 0) {
                Log::Mention("No New Opportunity from the vid's Bucket List");
                Log::Out();
                return null;
            }
            $opp = $this->OpportunityAt(mt_rand(0, $count - 1));
            BucketBoard::ForVolunteer($this->vid)->Add($opp);
            Log::Event("A New Opportunity from the vid's Bucket List", $opp->String());
            Log::Out();
            return $opp;
        }

        // returns the Opportunity identified by ipid from the volunteer's bucket list,
        // or null if it cannot be found. The bucket list must be in sorted order!
        public function FindOpportunityByIpId(string $ipid): ?Opportunity {
            $originalid = $ipid;
            $ipid .= PIPE;
            $idlen = strlen($ipid);
            if ($idlen == 0) {
                Log::Concern("ipid is the empty string");
                return null;
            }

            $arr = $this->lines;
            $low = 0;
            $high = count($arr) - 1;
            if ($high < $low) {
                Log::Concern("bucket list is empty");
                return null;
            }

            while ($low <= $high) {
                $mid = intval(($low+$high)/2);
                $compareResult = strncmp($arr[$mid], $ipid, $idlen);
                if ($compareResult == 0) {
                    $opp = Opportunity::FromLine($arr[$mid]);
                    $opp->index = $mid;
                    Log::Note("Found $originalid at position $mid", $opp->String());
                    return $opp;
                } else if ($compareResult < 0) {
                    $low = $mid + 1;
                } else {
                    $high = $mid - 1;
                }
            }

            Log::Concern("could not find $originalid in the bucket list");
            return null;
        }
    }
?>