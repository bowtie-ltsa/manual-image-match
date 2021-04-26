<?php 
    declare(strict_types=1);

    class Folder {
        public $fid; // string in the format "Fn", zero based
        public $relativePath; // relative to DATA_DIR
        public $images; // an array of imageId values

        public static function New($fid, $relativePath): Folder {
            $f = new Folder();
            $f->fid = $fid;
            $f->relativePath = $relativePath;
            $f->images = array();
            return $f;
        }

        public function FullPath(): string {
            return DATA_DIR . $this->relativePath;
        }
    }
?>