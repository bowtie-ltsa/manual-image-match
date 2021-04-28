<?php 
    declare(strict_types=1);

    // When a volunteer is given an Opportunity, it is removed from the OpportunityList and added to the OpportunityBoard.
    // Near the end of a round, several volunteers can "swarm" on the last remaining opportunities; that is, it is possible
    // for more than one volunteer to be given the same opportunity in a given round.
    // The opportunity stays on the opportunity board until someone makes a decision; it is removed as soon as the first volunteer
    // makes a decision. This means that (just in swarming mode) a returning volunteer get a new opportunity (might lose their 
    // opportunity for a particular imagepair, at least for a while). If the opportunity is not being swarmed, then there is only
    // the one volunteer assigned to it, and it will stay on the board until it gets decided by that volunteer.
    class TheOpportunityBoard {
        public const FILENAME = "the-opportunity-board.psv";
        public const FILEPATH = DATA_DIR . self::FILENAME;

        public static function IsEmpty(): bool {
            throw new Exception("not implemented")
        }

        // returns the (one and only) opportunity from TheOpportunityBoard assigned to the volunteer -- if any.
        // returns null if none.
        public static function GetExistingOpportunity(string $vid): Opportunity {
            throw new Exception("not implemented");
        }

        // returns an opportunity from TheOpportunityBoard that the volunteer,
        // one that is valid for the volunteer (on their bucket list / they have already made a decision on it).
        // this is an opportunity for swarming (increasing vidlist length)
        // it is an error to call this function if the volunteer already has something on TheOpportunityBoard -or- their BucketBoard
        // from among those that are valid for the volunteer, chooses from among those with least number of volunteers already.
        // it may choose randomly or it may consider $ipid.
        // adds +1 to the vidlist length. 
        // does *not* remove it from the volunteer's BucketList!
        public static function GetNewOpportunity(string $vid, ?string $ipid): Opportunity {
            throw new Exception("not implemented");
        }
    }
?>