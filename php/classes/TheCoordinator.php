<?php 
    declare(strict_types=1);

    // represents the person who would hand out image pairs intelligently, if only a person could live inside a web server.
    // for simplicity of code, we use a mutex to make the coordinator do just one thing at a time. Won't scale to millions
    // or even thousands of volunteers.
    class TheCoordinator {

        public static function GetOpportunity(string $vid, ?int $did, ?string $ipid): OpportunityResult {
            try {
                Log::In();
                Log::Mention(__METHOD__, "did=$did, ipid=$ipid");
                $mu = new Mutex("TheCoordinator");
                if (!$mu->Lock()) {
                    return new OpportunityResult(null, new BusyException());
                }
                return self::getOpportunityEx($vid, $did, $ipid);
            }
            catch(Exception $ex) {
                return new OpportunityResult(null, $ex);
            }
            finally {
                $mu->Unlock();
                Log::Mention("Leaving " . __METHOD__);
                Log::Out();
            }
        }

        private static function getOpportunityEx(string $vid, ?int $did, ?string $ipid): OpportunityResult {
            if (TheImagePairList::It()->IsEmpty()) {
                TheImagePairList::It()->CreateOnce();
            }

            DataZipper::BackupAtInterval();

            if (TheOpportunityList::It()->IsEmpty() && TheOpportunityBoard::It()->IsEmpty()) {
                TheOpportunityList::It()->StartNewRound();
            }

            // if a decision was requested, and exists, return that (and ignore $ipid)
            $decision = DecisionList::ForVolunteer($vid)->DecisionAt($did);
            if ($decision != null) {
                $decision->did = $decision->index;
                Log::Event("Review Decision from the Decision List", $decision->String());
                return new OpportunityResult($decision, null);
            }

            // if the volunteer has nothing on his or her bucket list, then we won't be able to provide a new opportunity for the volunteer.
            if (BucketList::ForVolunteer($vid)->IsEmpty()) {
                return new OpportunityResult(null, new VidFinishedException());
            }

            // if the volunteer already has an opportunity on the opportunity board, return that
            $opportunity = TheOpportunityBoard::It()->GetExistingOpportunity($vid);
            if ($opportunity != null) {
                return new OpportunityResult($opportunity, null);
            }

            // if the volunteer already has an opportunity on his or her bucket board, return that
            $opportunity = BucketBoard::ForVolunteer($vid)->GetExistingOpportunity();
            if ($opportunity != null) {
                return new OpportunityResult($opportunity, null);
            }

            // pull an opportunity for the volunteer out of the hat, if possible.
            // the hat (the opportunity list) may be empty; or it might reference only image pairs aleady decided by the volunteer.
            $opportunity = TheOpportunityList::It()->GetNewOpportunity($vid, $ipid);
            if ($opportunity != null) {
                return new OpportunityResult($opportunity, null);
            }

            // if the opportunity board has an opportunity that the volunteer can join in on (swarming), return that.
            // (volunteer cannot swarm on an opportunity if he or she has already made a decision for that image pair.)
            $opportunity = TheOpportunityBoard::It()->GetNewOpportunity($vid, $ipid);
            if ($opportunity != null) {
                return new OpportunityResult($opportunity, null);
            }

            // if anything at all is on the volunteer's bucket list, return it
            $opportunity = BucketList::ForVolunteer($vid)->GetNewOpportunity($ipid);
            if ($opportunity != null) {
                return new OpportunityResult($opportunity, null);
            }

            // At this point we know the volunteer's bucket list is empty. The volunteer has finished the study. 
            // Really, we shouldn't get to this line, because this condition has already been checked, at the top of this method.
            Log::Concern("We should not get to this point.");
            return new OpportunityResult(null, new VidFinishedException());
        }

        // updates an existing decision ($did is not null), or saves a new decision ($did is null).
        public static function SaveDecision(string $vid, ?int $did, string $ipid, int $decision): ?Exception {
            try {
                Log::In();
                Log::Mention(__METHOD__, "did=$did, ipid=$ipid, decision=$decision");
                $mu = new Mutex("TheCoordinator");
                if (!$mu->Lock()) {
                    return new BusyException();
                }
                self::SaveDecisionEx($vid, $did, $ipid, $decision);
                return null;
            }
            catch(Exception $ex) {
                return $ex;
            }
            finally {
                $mu->Unlock();
                Log::Mention("Leaving " . __METHOD__);
                Log::Out();
            }
        }

        // updates an existing decision ($did is not null), or saves a new decision ($did is null).
        public static function SaveDecisionEx(string $vid, ?int $did, string $ipid, int $decision): void {
            if ($did !== null) {
                DecisionList::ForVolunteer($vid)->UpdateDecision($did, $ipid, $decision);
                return;
            }
            
            $opp = BucketList::ForVolunteer($vid)->FindOpportunityByIpId($ipid);
            if ($opp == null) {
                // maybe user has multiple browsers going and submitted on both (maybe going from cell to desk)? let's check past decisions ...
                $did = DecisionList::ForVolunteer($vid)->FindIndexByIpId($ipid);
                if ($did !== null) {
                    Log::Entry("...found $ipid in the volunteer's Decision List", "did=$did ipid=$ipid this time decision=$decision");
                    DecisionList::ForVolunteer($vid)->UpdateDecision($did, $ipid, $decision);
                    $did = null; // reset $did to null so that navigation will be what the user expects - nav to a new image pair
                    return;
                }
                throw Log::PanicException("panic: opportunity for '$ipid' was not found in the volunteer's Bucket List", "decision=$decision");
            }
            $opp->decision = $decision;
            $opp->vidList = array($vid);
            DecisionList::ForVolunteer($vid)->Add($opp);
            TheOpportunityBoard::It()->RemoveByIpId($ipid);
            BucketList::ForVolunteer($vid)->RemoveAt($opp->index);
            $count = DecisionList::ForVolunteer($vid)->Count();
            Log::Event("Decision Made!", "ipid=$opp->ipid decision=$opp->decision #=$count");
            return;        
        }

    }
?>