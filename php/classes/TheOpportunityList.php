<?php 
    declare(strict_types=1);

    // aka "The Hat", this is the list of opportunities that have not been given out yet in the current round.
    // At the start of a round, this list is a shuffled copy of TheImagePairList, and items (opportunities) are 
    // removed from this, and added to the OpportunityBoard, as they are handed out to volunteers in need of an
    // opportunity.  A volunteer can get an opportunity from this list only if they don't have one on the Oppportunity
    // Board, or their individual BucketBoard. The goal each round is to have every opportunity on this list turned into 
    // a decision. The goal of round `k` (e.g. round 1) is to have a total of `k` decisions for every image pair. We will
    // find it necessary (or at least simple) to allow a few image pairs to end up with more than `k` decisions, as a 
    // trade-off to ensure we complete the round as soon as possible. At the start of round `k`, we could take the effort
    // to include just those image pairs that have fewer than `k` decisions, rather than the simple approach of merely
    // copying and shuffling TheImagePairList. Important: when items are removed from TheOpportunityList, they are *not* 
    // removed from the volunteer's BucketList; they are not removed from the volunteer's BucketList until and unless the 
    // volunteer reports a decision for that opportunity. In fact, due to swarming, the volunteer might lose their opportunity
    // to make a decision, for that opportunity (until/unless it gets reassigned to them in a future round).
    class TheOpportunityList {
        public const FILENAME = "the-opportunity-list.psv";
        public const FILEPATH = DATA_DIR . self::FILENAME;

        public static function IsEmpty(): bool { 
            return OppList::IsEmpty(self::FILEPATH); 
        }

        // create or recreate TheOpportunityList. This is how we start the first round or a new round.
        public static function Create() {
            $allPairs = TheImagePairList::GetAll();
            $headers = array_shift($allPairs);
            shuffle($allPairs);
            array_unshift($allPairs, $headers);
            file_put_contents(self::FILEPATH, implode(PHP_EOL, $allPairs));
        }

        // returns a new opportunity for the given volunteer from the hat, 
        // one that is valid for the volunteer (on their bucket list / they have already made a decision on it)
        // returns null if there are no opportunities for the volunteer
        // may or may not use the $ipid to provide continuity for the volunteer
        // removes the opportunity from TheOpportunityList
        // adds it to TheOpportunityBoard (with vidlist length of 1)
        // does *not* remove it from the volunteer's BucketList!
        // it is an error to call this function if the volunteer already has something on TheOpportunityBoard -or- their BucketBoard
        public static function GetNewOpportunity(string $vid, ?string $ipid): Opportunity {
            throw new Exception("not implemented");
        }
    }
?>