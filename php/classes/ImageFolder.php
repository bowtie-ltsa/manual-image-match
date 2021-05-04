<?php
    declare(strict_types=1);

    // represents a folder that contains images for the study.
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

        public function String(): string {
            return $this->id . PIPE . $this->path . PIPE . $this->imageCount;
        }
    }

?>