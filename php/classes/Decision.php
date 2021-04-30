<?php 
    declare(strict_types=1);

    // A Decision represents a volunteer's opinion of a specific image pair: same or different? (match or no match?)
    class Decision extends Opportunity {
        public function IsValid(): bool { return $this->decision != null && is_int($this->decision); }
    }
?>