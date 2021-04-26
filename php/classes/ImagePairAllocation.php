<?php 
    declare(strict_types=1);

    class ImagePairAllocation {
        public $ipaId; // int, zero based, image pair allocation id
        public $imagePairId; // string, id of image pair being allocated
        public $vid; // string, volunteer receiving the allocation
        public $q; // int, position in the volunteer's 'questions' array
        public $isMatch; // ?int, 1 for yes, 0 for no, null for not-yet-answered ('open' vs 'answered')
    }
?>