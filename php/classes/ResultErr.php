<?php
    declare(strict_types=1);

    class ResultErr {
        public $result; // some object
        public $err; // Exception

        public function Result(): array {
            return array($this->result, $this->err);
        }
    }
?>