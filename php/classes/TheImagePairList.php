<?php 
    declare(strict_types=1);

    // the one and only list of image pairs for the study. Each image pair is represented as an Opportunity not assigned and undecided.
    class TheImagePairList extends OppList {
        public const FILENAME = "the-image-pair-list.psv";
        public const FILEPATH = DATA_DIR . self::FILENAME;

        // return the one and only ImagePairList
        public static function It(): TheImagePairList {
            $ipList = new TheImagePairList();
            self::ForFile(self::FILEPATH, $ipList);
            return $ipList;
        }

        public function GetAll(): array {
            // for now at least, this is simple, because we keep the entire list in memory
            return $this->lines;
        }

        // creates a list of all pairs of images; we do not compare any image to another image from the same folder
        public function CreateOnce() {
            writeln("Generating image pairs:");
            $allDirs = glob(IMAGE_DATA_DIR . "*", GLOB_MARK+GLOB_ONLYDIR);
            $dirCount = count($allDirs);
            if ($dirCount == 0) { die("panic: no image folders found in image-data-dir"); }
            $imageCount = 0;
            $pairCount = 0;
            $allPairs = array();
            $header = OppList::HEADERS;
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
                            $allPairs[] = sprintf("F${I}C$X-F${J}C$Y|$leftImages[$x]|$rightImages[$y]||");
                        }
                    }
                    $folderPair->writeFile();
                }
            }
            writeln("total number of images: $imageCount");
            writeln("total number of image pairs: $pairCount");
            unset($allDirs); unset($leftImages); unset($rightImages);
            $this->lines = $allPairs;
            array_unshift($allPairs, $header);
            file_put_contents(self::FILEPATH, implode(PHP_EOL, $allPairs));
        }

    }
?>