<?php
    declare(strict_types=1);
    require_once "functions/file-functions.php";

    class ImagePairManager {

        // getImagePair() returns the requested image pair $q, if possible, or an alternate pair if possible, or null if there are problems.
        public function getImagePair(string $vid, ?int $q): ImagePairResult {
            $mu = new Mutex("$vid-pairs");
            if (!$mu->lock()) {
                return new ImagePairResult(null, new BusyException());
            }
            try {
                $vidPairs = fileOrEmpty("$vid-pairs.csv"); // contains a list of allocated image pairs and their results: folder1id, folder2id, 
                $maxq = count($vidPairs) - 1;
                if ($q <= 0 || $q > $maxq) { $q = $maxq; }
                if ($q > 0) {
                    debug("q=$q exists - implement");
                    return new ImagePairResult(null, new HaltException()); //temp
                }

                $result = $this->allocateImagePair($vid);
                return $result;
            }
            finally {
                $mu->unlock();
            }
        }

        // allocates an image pair to a volunteer; it returns (null,exception) if the volunteer has already matched 
        // all image pairs. It is a programming bug to call this function when the volunteer already has an allocated image pair and
        // has not provided an answer for it. (That is, a volunteer is only ever allocated at most one unanswered image pair, and 
        // there are only ever, at most, len(accounts.csv) unanswered image pairs.)
        private function allocateImagePair(string $vid): ImagePairResult {
            $mu = new Mutex("all-pairs-allocations");
            if (!$mu->lock()) {
                return array(null, new BusyException());
            }
            try {
                $allPairs = fileOrEmpty(ALLPAIRS_ALLOC_FILENAME);
                if (count($allPairs) == 0) {
                    $this->createAllPairs();
                    return new ImagePairResult(null, new HaltException());
                }
                $allPairsCount = count($allPairs);
                return new ImagePairResult(null, new Exception("got this far, keep going"));

                $vidResults = fileOrEmpty(vidResultsFilename($vid));
                if (count($vidResults) >= $allPairsCount) { 
                    // we cannot allocate an image pair to the volunteer
                    // volunteer has finished (i.e. volunteer has already provided an answer for every image pair)
                    return array(null, new VidFinishedException());
                }

                $currentRound = fileInt(CURRENT_ROUND_FILENAME);
                $vidNext = fileInt(vidNextFilename($vid), 1);
                for ($n = $vidNext; $n < $allPairsCount; $n++) {
                    $imagePairLine = &$allPairs[$n];
                    debug("considering line $n: $imagePairLine.");
                    break;
                }
                debug("got to here");
                return array(null, new HaltException());

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
        private function createAllPairs() {
            if (file_exists(ALLPAIRS_ALLOC_FILENAME)) {
                die("panic: allpairs already exists");
            }

            // create list of all pairs of images; do not compare any image to another image from the same folder
            writeln("Generating image pairs:");
            $allDirs = glob(IMAGE_DATA_DIR . "*", GLOB_MARK+GLOB_ONLYDIR);
            $dirCount = count($allDirs);
            if ($dirCount == 0) { die("panic: no image folders found in image-data-dir"); }
            $imageCount = 0;
            $pairCount = 0;
            $allPairs = array();
            $header = "pairid|folder1id|folder2id|image1id|image2id|image1|image2|vidlist";
            for ($i = 0; $i < $dirCount; $i++) {
                $leftImages = glob($allDirs[$i]."*");
                $leftImageCount = count($leftImages);
                if ($leftImageCount == 0 ) { die("panic: folder $allDirs[$i] contains no images"); }
                $imageCount += $leftImageCount;

                for ($j = $i+1; $j < $dirCount; $j++) {
                    $rightImages = glob($allDirs[$j]."*");
                    $rightImageCount = count($rightImages);
                    if ($rightImageCount == 0) { die("panic: folder $allDirs[$j] contains no images"); }

                    $I = $i+1; $J = $j+1;
                    writeln("F$I-F$J: ${leftImageCount}x$rightImageCount $allDirs[$i] --- $allDirs[$j]:");
                    $folderPair = new FolderPairInfo(
                        new ImageFolder($i, $allDirs[$i], $leftImageCount),
                        new ImageFolder($j, $allDirs[$j], $rightImageCount)
                    );
                    for ($x = 0; $x < $leftImageCount; $x++) {
                        for ($y = 0; $y < $rightImageCount; $y++) {
                            $X = $x+1; $Y = $y+1;
                            writeln("--- F${I}C$X-F${J}C$Y: $leftImages[$x] --- $rightImages[$y]");
                            $pairCount++;
                            $allPairs[] = sprintf("$pairCount|F${I}|F${J}|F${I}C$X|F${J}C$Y|$leftImages[$x]|$rightImages[$y]|");
                            //$folderPair->addImagePair(new FolderImagePair(....))
                        }
                    }
                    $folderPair->writeFile();
                }
            }
            writeln("total number of images: $imageCount");
            writeln("total number of image pairs: $pairCount");
            unset($allDirs); unset($leftImages); unset($rightImages);
            array_unshift($allPairs, $header);
            file_put_contents(ALLPAIRS_LIST_FILENAME, implode(PHP_EOL, $allPairs));
            array_splice($allPairs, 0, 1);

            // prepare for the first round: set round#
            file_put_contents(CURRENT_ROUND_FILENAME, "0");

            // prepare for the first round: shuffle the list
            shuffle($allPairs);
            writeln("");
            writeln("shuffled list of image pairs for the first round:");
            for ($n = 0; $n < $pairCount; $n++) {
                writeln($allPairs[$n]);
            }
            array_unshift($allPairs, $header);
            file_put_contents(ALLPAIRS_ALLOC_FILENAME, implode(PHP_EOL, $allPairs));
            
            writeln("");
            writeln("initialization complete: all image pairs have been listed, and the list shuffled for the first round. No pairs have been allocated.");
            writeln("press F5 to continue.");
            return array(null, new HaltException());
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