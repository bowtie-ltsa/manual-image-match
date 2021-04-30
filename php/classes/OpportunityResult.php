<?php 
    declare(strict_types=1);

    class OpportunityResult extends ResultErr {
        public function __construct(?Opportunity $opp, ?Exception $err) {
            $this->result = $opp;
            $this->err = $err;
        }
    }
?>