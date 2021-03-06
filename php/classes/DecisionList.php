<?php 
    declare(strict_types=1);

    // A DecisionList is the list of decisions made by a specific volunteer.
    class DecisionList extends OppList {
        public const FILENAME_SUFFIX = "-decision-list.psv";
        public const FILEPATH_FMT = DATA_DIR . "%s" . self::FILENAME_SUFFIX;

        public static function ForVolunteer(string $vid): DecisionList {
            $filepath = sprintf(self::FILEPATH_FMT, $vid);
            $decList = new DecisionList();
            parent::ForFile($filepath, $decList);
            $decList->vid = $vid;
            return $decList;
        }

        // instance variables and methods

        private $vid; // string, the id of the volunteer associated with this DecisionList

        public function DecisionAt(?int $pos): ?Opportunity {
            return $this->OpportunityAt($pos);
        }

        public function UpdateDecision(?int $did, ?string $ipid, int $decision): void {
            Log::In();
            Log::Mention(__METHOD__);
            try {
                $opp = $this->OpportunityAt($did);
                if ($opp == null) {
                    throw Log::PanicException("panic: decision $did was not found in the volunteer's Decision List", "ipid=$ipid, decision=$decision");
                }
                if ($ipid != null && $opp->ipid != $ipid) {
                    throw Log::PanicException("panic: validation failed: decision $did has ipid '$opp->ipid' not '$ipid'.");
                }
                if (intval($opp->decision) === $decision) {
                    Log::Mention("Decision is not actually changing.");
                    return;
                }

                $maxid = $this->Count() - 1;
                $diff = $maxid - $did;
                Log::Event("Decision Changed", "did=$did ($diff from the end) ipid=$ipid from=" . $opp->decision . " to=$decision");
                $opp->decision = $decision;
                $this->Update($opp);
                $this->save();
            } 
            finally {
                Log::Out();
            }
        }

        
    }
?>