<?php 
    declare(strict_types=1);

    class Pair {
        private int $q
        public string $image1 = "(not set)";
        public string $image2 = "(not set)";

        public function __construct(string $image1, string $image2) { 
            $this->image1 = $image1;
            $this->image2 = $image2;
        }

        public function q(): int { return $q }
    }
?>