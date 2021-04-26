<?php
    declare(strict_types=1);

    // if a folder pair is allocated to a vid in a given round, then we will avoid allocating it to additional vids in the same round
    // however if all folder pairs have been allocated to round k, but some of those folder pairs are not yet finished,
    // then we will allocate additional vids to those folder pairs; these vids are said to be swarming the folder pair to get it done.
    class FolderPair {

        // primary vid + swarm vids?
        // or vidlist + latestRound?
        // or vidlists - array, indexed by round <-- this one

        public function __construct() {
            // ?
        }

        // needs to work for normal case (volunteer is working through a folder pair on his own)
        // as well as swarming case (two or more volunteers are trying to finish a folder pair)
        public function nextImagePairForVid($vid): ImagePair {
            // get list of image pairs for this folder pair, including allocations _and_ answers
            // loop through image pairs 
            // - if image pair has been answered k times during round k, skip it
            // - if image pair has been allocated < k times, claim it (allocate it to vid and return it)
            // - for image pair that has been allocated k or more times (implies swarm mode for this folder pair), and has not already been allocated to $vid, track minAllocationCount and FirstImagePairWithMinAllocation
            // take FirstImagePairWithMinAllocation if any; however, flag it as being swarmed. html+js should watch for changes and tell volunteer something like "image is being viewed by n other volunteers" and eventually "image has been analyzed by another volunteer; skip to next image? [ok/cancel]".
            // if there is no "FirstImagePairWIthMinAllocation" this implies "not swarm mode" or "swarming complete for this folder pair";
            // in either case, "none" means all image pairs in this folder pair have been answered k times: return ImageNotAvailable
        }
    }
?>