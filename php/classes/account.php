<?php 
    declare(strict_types=1);

    class Account {
        public string $name = "(not set)";
        
        public function __construct(string $name) { 
            $this->name = $name;
        }
    }
?>