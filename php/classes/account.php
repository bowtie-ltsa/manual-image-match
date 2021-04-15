<?php 
    declare(strict_types=1);

    class Account {
        public $name = "(not set)";
        public $folders = array();

        public function __construct(string $name, array $folders) { 
            $this->name = $name;
            $this->folders = $folders;
        }
    }
?>