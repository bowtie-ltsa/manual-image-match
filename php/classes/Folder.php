<?php 
    declare(strict_types=1);

    class Folder {
        public $fId; // string in the format "Fn")
        public $relativePath; // relative to DATA_DIR
        public $images; // an array of imageId values

        public function __construct($fid, $relativePath) {
            $this->fid = $fid;
            $this->relativePath = $relativePath;
            $this->images = array();
        }

        public function FullPath(): string {
            return DATA_DIR . $this->relativePath;
        }
    }
?>