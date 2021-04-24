<?php
    declare(strict_types=1);

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

        // todo: public static function readFromFile(string $filename)
    }

    class ImageFolder {
        public $id;
        public $path;
        public $imageCount;

        public function __construct(int $id, string $path, int $imageCount) {
            $this->id = $id;
            $this->path = $path;
            $this->imageCount = $imageCount;
        }

        public function num(): int { return $this->id + 1; }
    }

?>