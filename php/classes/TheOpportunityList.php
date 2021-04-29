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
    class TheOpportunityList extends OppList {
        public const FILENAME = "the-opportunity-list.psv";
        public const FILEPATH = DATA_DIR . self::FILENAME;

        // return the one and only OpportunityList
        public static function It(): TheOpportunityList {
            $tol = new TheOpportunityList();
            OppList::ForFile(self::FILEPATH, $tol);
            return $tol;
        }

        // create or recreate TheOpportunityList. This is how we start the first round or a new round. We also clear any BucketBoards,
        // because we want returning volunteers to get an opportunity from the new round, if possible.
        // todo: At the start of round `k`, we could take the effort to include just those image pairs that have fewer than `k` decisions, 
        // rather than the simple approach of merely copying and shuffling TheImagePairList.
        public function StartNewRound() {
            $this->lines = TheImagePairList::It()->GetAll(); // a copy of the image pair array
            shuffle($this->lines);
            $this->save();
            
            BucketBoard::ClearAll();
        }

        // Returns a new opportunity for the given volunteer "from the hat", one that is valid for the volunteer (it's on their bucket list, 
        // i.e. they have *not* already made a decision on it). Returns null if there are no opportunities for the volunteer.
        // This method may or may not use the $ipid to provide continuity for the volunteer.
        // It removes the opportunity from TheOpportunityList. This ensures that no other volunteers will get the same opportunity for this 
        // round, unless swarming is needed, near the end of the round, to finish the round.
        // Adds it to TheOpportunityBoard (with vidlist length of 1).
        // Does *not* remove it from the volunteer's BucketList!
        // Note: it is an error to call this function if the volunteer already has something on TheOpportunityBoard -or- their BucketBoard!
        public function GetNewOpportunity(string $vid, ?string $ipid): ?Opportunity {
            for ($i = $this->Count()-1; $i >= 0; $i--) {
                $opp = $this->OpportunityAt($i);
                if (BucketList::ForVolunteer($vid)->Contains($opp)) {
                    $opp->vidList[] = $vid;
                    $this->RemoveAt($i);
                    TheOpportunityBoard::It()->Add($opp);
                    return $opp;
                }
            }
            return null;
        }
    }
?>