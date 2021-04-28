<?php 
    declare(strict_types=1);

    // aka Decision Opportunity or just "DecOp" for short.
    // Represents the opportunity to make a decision on a given imagePair, not the decision itself.
    class Opportunity {
        // the id of the image pair, in the format FnCx-FmCy, with n<m
        public $ipid;

        public $path1;
        public $path2;

        // the volunteers who have the opportunity. a simple array of strings.
        // - length is always zero for an opportuntiy in TheImagePairList.
        // - length is always zero for an opportunity in TheOpportunityList.
        // - length is always 1 or more for an opportunity on TheOpportunityBoard.
        // - length is always exactly 1 for an opportunity on a volunteer's OpportunityBoard.
        public $vidList;

        // just add public $decision (1=same 0=different, null=no decision) and poof you have a Decision
        // "An opportunity becomes a decision when the $decision property is set"
        // class Decision extends Opportunity { ... } and maybe put in a check on save() functions to insist $decision is or is not null...
    }
?>