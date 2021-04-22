<?php
    declare(strict_types=1);
    require_once "first-things.php";

    require_once "classes/imagePair.php";
    require_once "classes/mutex.php";
    require_once "fileOrEmpty.php";
    class ImagePairManager {

        // getImagePair() returns the requested image pair $q, if possible, or an alternate pair if possible, or null if there are problems.
        public function getImagePair(string $vid, ?int $q): array { // ImagePair, Exception
            $mu = new Mutex("$vid-pairs");
            if (!$mu->lock()) {
                return array(null, new BusyException());
            }
            try {
                $vidPairs = fileOrEmpty("$vid-pairs.csv");
                $maxq = count($vidPairs) - 1;
                if ($q <= 0 || $q > $maxq) { $q = $maxq; }
                if ($q > 0) {
                    debug("q=$q exists - implement");
                    exit();
                }

                $pair = $this->allocatePair($vid);
                if ($pair == null) {
                    return array(null, new BusyException());
                }
                return array($pair, null);
            }
            finally {
                $mu->unlock();
            }
        }

        // AllocatePair() allocates an image pair to a volunteer; it returns null if the volunteer has already matched all image pairs,
        // or if the volunteer has not yet recorded an answer for their current allocated image pair. (That is, a volunteer is only ever
        // allocated at most one unanswered image pair, and there are only ever, at most, len(accounts.txt) unanswered image pairs.)
        private function allocatePair(string $vid): ImagePair {
            $mu = new Mutex("all-pairs");
            if (!$mu->lock()) {
                return null;
            }
            try {
                $allPairs = fileOrEmpty("all-pairs.txt");
                if (count($allPairs)==0) {
                    $allPairs = $this->createAllPairs();
                }
                die("implement allocatePair next step");
                // if the file "$vid-next.txt" does not exist, write "0".
                // if len($vid-results.txt) >= len(all-pairs.txt), return null: volunteer has matched every pair and has no more pairs to match.
                // starting at $($vid-next.txt), find the first image pair that has been allocated $(current-pass.txt) times.
                // if none, invoke prepareNextPass() and find the first image pair that has been allocated $(current-pass.txt) times.
                // if none (shouldn't happen!) return null.
                // update $vid-next.txt to $selectedIndex+1
                // return image pair.
                return new ImagePair(7,"foo","bar");
            }
            finally {
                $mu->unlock();
            }
        }

        // createAllPairs() creates a random list of all pairs of images, unallocated to any volunteers.
        private function createAllPairs(): array {
            die("implement create all pairs");
            // if the file "all-pairs.txt" exists then panic.
            // create all-pairs array in memory, and shuffle it.
            // write array to "all-pairs.txt".
            // write "0" to "current-pass.txt".
            // at this point all image pairs are listed in a random order and none have been allocated.
        }

    // prepareNextPass() shuffles the file "all-pairs.txt" and increments "current-pass.txt".
    // This method is invoked when a volunteer needs a new image pair to be allocated to him or her, but all
    // image pairs have already been allocated $(current-pass.txt) times. We must now prepare the _next_ pass because, 
    // for pass n (starting at zero), we will only allocate images that have been allocated n times.
    // 
    // Note: There will generally be a few image pairs that have been allocated $(current-pass.txt) times, but not yet 
    // _answered_ $(current-pass.txt) times:
    // - There can be at most $v of these, where $v is the number of unique volunteers that have started doing image matches.
    // - These _open_ image pairs are top priority for the next pass (since answering them will in reality complete the previous 
    //   pass), so they will be moved to the front of the file.
    // - There would typically be $v-1 of them; only after one or more volunteers actually answers *all* image pairs would
    //   there be fewer than $v-1 of them. 
    // - There is always at least 1 of them.
    // --
    // if file all-pairs.txt does not exist panic
    // read "all-pairs.txt", place into map[pair]allocations
    // read last line of every $vid-pairs.txt; most (but definitely not all) will be for an open image pair
    // foreach openpair, remove from map, add to start of newpass; shuffle newpass; write contents to "all-pairs.txt"
    // shuffle remaining map; append contents to "all-pairs.txt"
    }
?>