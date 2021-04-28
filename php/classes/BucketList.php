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
            if ($mustInitialize) {
                $bktList->initialize();
            }
            return $bktList;
        }

        // instance variables and methods

        // called only when the volunteer's bucket list is first created.
        // this method creates a shuffled copy of TheImagePairList.
        private function initialize(): void {
            $this->lines = TheImagePairList::It()->GetAll(); // a copy of the image pair array
            shuffle($this->lines);
            file_put_contents($this->filepath, OppList::HEADERS . PHP_EOL . implode(PHP_EOL, $this->lines));
        }

        public function IsEmpty(): bool {
            return parent::IsEmpty($this->filepath);
        }

        // returns any item from the BucketList. may consider $ipid.
        public function GetNewOpportunity(string $vid, ?string $ipid): Opportunity {
            throw new Exception("not implemented");
        }
    }
?>