<?php 
    declare(strict_types=1);

    class IpaResult extends ResultErr {
        public function __construct(?ImagePairAllocation $ipa, ?Exception $err) {
            $this->result = $ipa;
            $this->err = $err;
        }
    }
?>