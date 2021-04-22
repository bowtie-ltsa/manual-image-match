<?php 
    declare(strict_types=1);
    require_once "first-things.php";

    class ImagePair {
        private $q;
        public $image1 = "(not set)";
        public $image2 = "(not set)";

        public function __construct(int $q, string $image1, string $image2) { 
            $this->q = $q;
            $this->image1 = $image1;
            $this->image2 = $image2;
        }

        public function q(): int { return $this->q; }
    }
?>