<?php 
    declare(strict_types=1);

    class Account {
        public $name = "(not set)";
        
        public function __construct(string $name) { 
            $this->name = $name;
        }
    }
?>