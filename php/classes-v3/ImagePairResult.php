<?php 
    declare(strict_types=1);

    class ImagePairResult extends ResultErr {
        public function __construct(?ImagePair $ip, ?Exception $err) {
            $this->result = $ip;
            $this->err = $err;
        }
    }
?>