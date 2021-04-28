<?php
    declare(strict_types=1);

    // represents a pair of image folders; this class is used to generate TheImagePairList, 
    // which is created once for the study and then never changes.
    class FolderPair {
        public $filename;
        public $left;
        public $right;

        public function __construct(ImageFolder $left, ImageFolder $right) {
            if ($left->id >= $right->id) { die("panic: invalid FolderPair F{$left->num()}F{$right->num()}"); }
            $this->left = $left;
            $this->right = $right;
            $this->filename = sprintf("folder-pair-F%d-F%d.txt", $left->num(), $right->num());
        }

        public function writeFile() {
            file_put_contents(DATA_DIR . $this->filename, json_encode($this, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
        }
    }

?>