<?php
    declare(strict_types=1);

    class FolderPairManager {
        public $allFolderPairs;

        public function __construct() {
            // read all folder pairs from disk
        }

        public function currentFolderPairForVid(string $vid): FolderPair {
            // find list of folder pairs allocated to vid
            // find last folder pair allocated
            // if none allocate a new one by calling nextFolderPairForVid($vid)
        }

        public function nextFolderPairForVid(string $vid): FolderPair {
            // loop over folder pairs
            // - ignore any folder pair that is either already allocated to vid in current round (presumably finished) or actually finished (simplify to ignore finshed folder pairs?)
            // - track minCount and firstFolderPairWithMinCount for # allocations in current round
            // - exit early if minCount of zero is found (zero implies unstarted, better than just unfinished)
            // take firstFolderPairWIthMinCOunt if any
            // otherwise loop over folder pairs looking for unfinished folder pairs
            // otherwise return FOlderPairNotAvailable (for this vid)
        }
    }
?>