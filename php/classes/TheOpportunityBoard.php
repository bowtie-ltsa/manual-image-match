<?php 
    declare(strict_types=1);

    // When a volunteer is given an Opportunity, it is removed from the OpportunityList and added to the OpportunityBoard.
    // Near the end of a round, several volunteers can "swarm" on the last remaining opportunities; that is, it is possible
    // for more than one volunteer to be given the same opportunity in a given round.
    // The opportunity stays on the opportunity board until someone makes a decision; it is removed as soon as the first volunteer
    // makes a decision. This means that (just in swarming mode) a returning volunteer can get a new opportunity (might lose their 
    // previous opportunity for an imagepair, at least for a while). If the opportunity is not being swarmed, then there is only
    // the one volunteer assigned to it, and it will stay on the board until it gets decided by that volunteer.
    // Note: when the last item is removed from TheOpportunityBoard, and TheOpportunityList is empty, a new round is about to begin.
    class TheOpportunityBoard extends OppList {
        public const FILENAME = "the-opportunity-board.psv";
        public const FILEPATH = DATA_DIR . self::FILENAME;

        // return the one and only OpportunityBoard
        public static function It(): TheOpportunityBoard {
            $obList = new TheOpportunityBoard();
            self::ForFile(self::FILEPATH, $obList);
            return $obList;
        }

        // returns the (one and only) opportunity from TheOpportunityBoard assigned to the volunteer -- if any.
        // returns null if none.
        public function GetExistingOpportunity(string $vid): ?Opportunity {
            Log::In();
            Log::Mention(__METHOD__);
            // a small list, no more than one per volunteer; just loop
            foreach($this->lines as $line) {
                $opp = Opportunity::FromLine($line);
                if (in_array($vid, $opp->vidList)) {
                    Log::Event("Use Existing Opportunity from The Opportunity Board", $opp->String());
                    Log::Out();
                    return $opp;
                }
            }
            Log::Mention("No Existing Opportunity from The Opportunity Board");
            Log::Out();
            return null;
        }

        // returns an opportunity from TheOpportunityBoard for the named the volunteer, one that is valid for the volunteer 
        // (it's on their bucket list, i.e. they have *not* already made a decision on it). This is an opportunity for swarming 
        // (increasing vidlist length).
        //
        // Note: It is an error to call this function if the volunteer already has something on TheOpportunityBoard -or- their BucketBoard.
        //
        // From among those that are valid for the volunteer, chooses from among those with least number of volunteers already.
        // It may choose randomly or it may consider $ipid.
        // Adds +1 to the vidlist length. 
        // Does *not* remove it from the volunteer's BucketList!
        public function GetNewOpportunity(string $vid, ?string $ipid): ?Opportunity {
            Log::In();
            Log::Mention(__METHOD__);
            $minVidCount = 99999;
            $bestChoices = array();

            // find best choices (valid for vid, and no other opportunities have fewer vids currently in the vidlist)
            for ($i = $this->Count()-1; $i >= 0; $i--) {
                $opp = $this->OpportunityAt($i);
                if (BucketList::ForVolunteer($vid)->Contains($opp)) {
                    $vidCount = count($opp->vidList);
                    if ($vidCount < $minVidCount) {
                        $minVidCount = $vidCount;
                        $bestChoices = array($opp);
                    } else if ($vidCount == $minVidCount) {
                        $bestChoices[] = $opp;
                    }
                }
            }

            if (count($bestChoices) == 0) {
                Log::Mention("No New Opportunity from The Opportunity Board");
                Log::Out();
                return null;
            }

            $opp = $bestChoices[0];

            $opp->vidList[] = $vid;
            $this->Update($opp);
            $this->save();

            Log::Event("A New Opportunity from The Opportunity Board", $opp->String());
            Log::Out();
            return $opp;
        }
    }
?>