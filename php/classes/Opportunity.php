<?php 
    declare(strict_types=1);

    // aka Decision Opportunity or just "DecOp" for short.
    // Represents the opportunity to make a decision on a given imagePair, not the decision itself.
    // An opportunity becomes a decision when the $decision property is set.
    // We might have public class Decision extends Opportunity and do some casting and checking, perhaps.
    class Opportunity {

        public static function FromLine(string $line) { return OppList::Opportunity($line); }

        public function ToLine(): string { return OppList::Line($opp); }

        // the id of the image pair, in the format FnCx-FmCy, with n<m
        public $ipid;

        public $path1;
        public $path2;

        // the volunteers who have the opportunity. a simple array of $vid values (strings).
        // - length is always zero for an opportuntiy in TheImagePairList.
        // - length is always zero for an opportunity in TheOpportunityList.
        // - length is always 1 or more for an opportunity on TheOpportunityBoard.
        // - length is always exactly 1 for an opportunity on a volunteer's OpportunityBoard.
        public $vidList;

        // the decision made for this opportunity. 1=same 0=different null=no decision
        // - always null except for when in a DecisionList!!! (Save and Load should check/enforce this...)
        public $decision;

        public function IsValid(): bool { return $this->decision == null; }

        public function String(): string { return "{ index='" . @$this->index . "', ipid='$this->ipid', vidList='" . implode(",", $this->vidList) . "', decision='$this->decision' }"; }
    }
?>