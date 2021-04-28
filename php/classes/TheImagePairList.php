<?php 
    declare(strict_types=1);

    // the one and only list of image pairs for the study.
    class TheImagePairList {
        public const FILENAME = "all-image-pairs.psv";
        public const FILEPATH = DATADIR . self::FILENAME;

        // get an individual item in the list by position
        public static function FromId(int $i): Opportunity {

        }

        // get an individual item by id
        public static function FromId(string $id): Opportunity {

        }

        public static function entireList(int $i): array {
            if (self::$list == null) {
                self::$list = array();
                // read file line by line, convert each line to Opportunity object
            }
            return $_entireList;
        }
        private static $_entireList; // an simple array of Opportunity objects; we may be fancier later (split into multiple files, etc)

        public static function CreateIfNecessary() {
            if (file_exists(FILEPATH)) { return; }
            $mu = new Mutex(self::FILENAME);
            if (!$mu->lock()) {
                return array(null, new BusyException());
            }
            try {
                self::create();
            }
            finally {
                $mu->unlock();
            }
        }

        // creates a list of all pairs of images; we do not compare any image to another image from the same folder
        private static function create() {
            writeln("Generating image pairs:");
            $allDirs = glob(IMAGE_DATA_DIR . "*", GLOB_MARK+GLOB_ONLYDIR);
            $dirCount = count($allDirs);
            if ($dirCount == 0) { die("panic: no image folders found in image-data-dir"); }
            $imageCount = 0;
            $pairCount = 0;
            $allPairs = array();
            $header = "ipdi|path1|path2|vidlist";
            for ($i = 0; $i < $dirCount; $i++) {
                $leftImages = glob($allDirs[$i]."*");
                $leftImageCount = count($leftImages);
                if ($leftImageCount == 0 ) { die("panic: folder $allDirs[$i] contains no images"); }
                $imageCount += $leftImageCount;

                for ($j = $i+1; $j < $dirCount; $j++) {
                    $rightImages = glob($allDirs[$j]."*");
                    $rightImageCount = count($rightImages);
                    if ($rightImageCount == 0) { die("panic: folder $allDirs[$j] contains no images"); }

                    $I = $i+1; $J = $j+1;
                    writeln("F$I-F$J: ${leftImageCount}x$rightImageCount $allDirs[$i] --- $allDirs[$j]:");
                    $folderPair = new FolderPair(
                        new ImageFolder($i, $allDirs[$i], $leftImageCount),
                        new ImageFolder($j, $allDirs[$j], $rightImageCount)
                    );
                    for ($x = 0; $x < $leftImageCount; $x++) {
                        for ($y = 0; $y < $rightImageCount; $y++) {
                            $X = $x+1; $Y = $y+1;
                            writeln("--- F${I}C$X-F${J}C$Y: $leftImages[$x] --- $rightImages[$y]");
                            $pairCount++;
                            $allPairs[] = sprintf("F${I}C$X-F${J}C$Y|$leftImages[$x]|$rightImages[$y]|");
                        }
                    }
                    $folderPair->writeFile();
                }
            }
            writeln("total number of images: $imageCount");
            writeln("total number of image pairs: $pairCount");
            unset($allDirs); unset($leftImages); unset($rightImages);
            array_unshift($allPairs, $header);
            file_put_contents(ALLPAIRS_LIST_FILENAME, implode(PHP_EOL, $allPairs));
        }
    }
?>