<?php
    declare(strict_types=1);

    // represents a pair of image folders; this class is used to generate TheImagePairList, 
    // which is created once for the study and then never changes.
    class FolderPair {
        public $folderPairName;
        public $left;
        public $right;

        public function __construct(ImageFolder $left, ImageFolder $right) {
            if ($left->id >= $right->id) { Log::PanicAndDie("panic: invalid FolderPair F{$left->num()}F{$right->num()}"); }
            $this->left = $left;
            $this->right = $right;
            $this->folderPairName = sprintf("folder-pair-F%d-F%d", $left->num(), $right->num());
        }

        public function String(): string { 
            return $this->folderPairName . PIPE . $this->left->String() . PIPE . $this->right->String(); 
        }
    }

?>