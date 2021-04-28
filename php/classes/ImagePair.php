<?php 
    declare(strict_types=1);

    // An ImagePair represents a special kind of blank Opportunity; a template from which to create Opportunities.
    class ImagePair extends Opportunity {
        public function IsValid(): bool { return $this->vidlist == null && $this->decision == null; }
    }
?>