<?php 
    declare(strict_types=1);

    // represents the person who would hand out image pairs intelligently, if only a person could live inside a web server.
    // for simplicity of code, we use a mutex to make the coordinator do just one thing at a time. Won't scale to millions
    // or even thousands of volunteers.
    class TheCoordinator {
        public static function GetOpportunity(string $vid, ?int $did, ?string $ipid): OpportunityResult {
            try {
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
            }
        }

        private static function getOpportunityEx(string $vid, ?int $did, ?string $ipid): OpportunityResult {
            TheImagePairList::CreateIfNecessary();

            if (TheOpportunityList::IsEmpty() && TheOpportunityBoard::IsEmpty()) {
                TheOpportunityList::Create();
            }

            // if a decision was requested, and exists, return that
            $decision = DecisionList::ForVolunteer($vid)->DecisionAt($did);
            if ($decision != null) { 
                return new OpportunityResult($decision, null);
            }

            // if the volunteer has nothing on his or her bucket list, then we won't be able to provide a new opportunity for the volunteer.
            if (BucketList::ForVolunteer($vid)->IsEmpty()) {
                return new OpportunityResult(null, new VidFinishedException());
            }

            // if the volunteer already has an opportunity on the opportunity board, return that
            $opportunity = TheOpportunityBoard::GetExistingOpportunity($vid);
            if ($opportunity != null) {
                return new OpportunityResult($opportunity, null);
            }

            // if the volunteer already has an opportunity on his or her bucket board, return that
            $opportunity = BucketBoard::GetExistingOpportunity($vid);
            if ($opportunity != null) {
                return new OpportunityResult($opportunity, null);
            }

            // pull an opportunity for the volunteer out of the hat, if possible.
            // the hat (the opportunity list) may be empty; or it might reference only image pairs aleady decided by the volunteer.
            $opportunity = TheOpportunityList::GetNewOpportunity($vid, $ipid);
            if ($opportunity != null) {
                return new OpportunityResult($opportunity, null);
            }

            // if the opportunity board has an opportunity that the volunteer can join in on (swarming), return that.
            // (volunteer cannot swarm on an opportunity if he or she has already made a decision for that image pair.)
            $opportunity = TheOpportunityBoard::GetNewOpportunity($vid, $ipid);
            if ($opportunity != null) {
                return new OpportunityResult($opportunity, null);
            }

            // if anything at all is on the volunteer's bucket list, return one
            $opportunity = BucketList::ForVolunteer($vid)->GetNewOpportunity($ipid);
            if ($opportunity != null) {
                return new OpportunityResult($opportunity, null);
            }

            // at this point we know the volunteer's bucket list is empty
            // the volunteer has finished the study
            return new OpportunityResult(null, new VidFinishedException());
        }
    }
?>