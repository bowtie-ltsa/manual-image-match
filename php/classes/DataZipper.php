<?php 
    declare(strict_types=1);

    class DataZipper {
        public const FILENAME_FMT = "capture-match-results--%s.zip";
        public const FILEPATH_FMT = BACKUP_DIR . self::FILENAME_FMT;

        public const BACKUP_INTERVAL_SECONDS = 5*self::MINUTES;
        public const BACKUP_TRACKER_FILENAME = "last-results-time.txt";
        public const BACKUP_TRACKER_FILEPATH = DATA_DIR . self::BACKUP_TRACKER_FILENAME;

        // it is important that this file does not match the glob pattern for '*' . DecisionList::FILENAME_SUFFIX!
        public const ALL_DECISIONS_FILE = "!AllDecisions.psv";

        private static $globPattern = "*.psv"; // specifically we want to skip the *.lock files which are zero length and maybe trouble
        private static $globFlags = 0;
        private static $zipOptions = null;

        // should be called within a lock (from the coordinator)so we don't create another one here
        public static function BackupAtInterval(): void {
            clearstatcache(true, self::BACKUP_TRACKER_FILEPATH);
            $lastTime = @filemtime(self::BACKUP_TRACKER_FILEPATH);
            if (time() - $lastTime > self::BACKUP_INTERVAL_SECONDS) {
                self::Backup();
                touch(self::BACKUP_TRACKER_FILEPATH);
            }
            //self::PruneBackups(); // just for debugging, prune every time
        }

        public static function Backup(): void {
            Log::Entry("Starting Backup");
            if (self::$zipOptions === null) {
                self::$zipOptions = array('remove_all_path' => true, 'add_path' => basename(DATA_DIR) . '/');
            }

            try {
                self::CombineDecisionLists();
                $now = (new DateTime("now", new DateTimeZone('America/Los_Angeles')))->format("Y-m-d--H-i-s.v");
                $finalFilepath = sprintf(self::FILEPATH_FMT, $now);
                $tempFilepath = $finalFilepath . ".creating";
                $zip = new ZipArchive();
                if ($zip->open($tempFilepath, ZipArchive::CREATE) === true) {
                    $zip->addGlob(DATA_DIR . self::$globPattern, self::$globFlags, self::$zipOptions);
                    $zip->close();
                    rename($tempFilepath, $finalFilepath);
                    Log::Entry("Finished Backup");
                }
                self::PruneBackups(); // normally, only prune after a backup
            }
            catch(Exception $ex) {
                Log::Concern("Failed to create a backup", $ex->getMessage());
            }
        }

        public static function CombineDecisionLists(): void {
            $filenames = glob(DATA_DIR . '*' . DecisionList::FILENAME_SUFFIX);
            $allFile = fopen(DATA_DIR . self::ALL_DECISIONS_FILE, "w");
            if ($allFile === false) {
                Log::Concern("Unable to open AllDecisions file for writing");
                return;
            }
            try {
                Log::Entry("Start combining decision files", "count=" . count($filenames));
                $isFirstFile = true;
                foreach ($filenames as $filename) {
                    $file = fopen($filename, "r");
                    if ($file === false) {
                        Log::Concern("Skipping file $filename");
                    }
                    try {
                        if (!$isFirstFile) { while(($char = fread($file, 1)) !== false && $char != "\n"); } // skip header
                        $bytes = stream_copy_to_stream($file, $allFile);
                        if ($bytes === false) {
                            Log::Concern("stream copy for '$filename' reports failure without more info");
                        }
                        fwrite($allFile, PHP_EOL);
                    }
                    catch (Exception $ex) { Log::Warning("Skipping file $filename due to Exception: " . $ex->getMessage()); }
                    finally { fclose($file); }
                    $isFirstFile = false;
                }
            }
            catch (Exception $ex) {
                Log::Warning("Exception: " . $ex->getMessage());
            }
            finally {
                fclose($allFile);
            }
            Log::Entry("Done combining decision files");
        }

        public static function PruneBackups(): void {
            try {
                $mu = new Mutex("PruneBackups");
                if (!$mu->Lock(5)) {
                    Log::Entry("Someone else is pruning");
                }
                Log::Entry("Start Pruning Backups");
                self::PruneBackupsEx();
                Log::Entry("Done Pruning Backups");
            }
            catch(Exception $ex) {
                Log::Entry("Exception Pruning Backups", $ex->getMessage());
            }
            finally {
                $mu->Unlock();
            }
        }

        private const SECONDS = 1;
        private const MINUTES = 60*self::SECONDS;
        private const HOURS = 60*self::MINUTES;
        private const DAYS = 24*self::HOURS;

        // "interval" is a misnomer here; this var will be an array of what are more 
        // correctly called the demarcations of the intervals...
        // each demarcation must be greater than the previous one.
        private static $intervals = null;

        private static function setIntervals(array $intervals): void {
            $count = count($intervals);
            for ($i = 1; $i < $count; $i++) {
                if ($intervals[$i-1] >= $intervals[$i]) {
                    throw Log::PanicException(
                        "panic: invalid backup intervals configuration", 
                        "intervals[i-1] >= intervals[i]",
                        "intervals[$i-1] >= intervals[$i]",
                        $intervals[$i-1] . ' >= ' . $intervals[$i]
                    );
                }
            }
            self::$intervals = $intervals;
        }

        private static function initIntervals(): void {
            $a = array();
            for ($s = 5*self::MINUTES; $s < 2*self::HOURS; $s += 5*self::MINUTES) $a[] = $s;
            for ($s = 2*self::HOURS; $s < 6*self::HOURS; $s += 15*self::MINUTES) $a[] = $s;
            for ($s = 6*self::HOURS; $s < 48*self::HOURS; $s += 1*self::HOURS) $a[] = $s;
            for ($s = 2*self::DAYS; $s < 7*self::DAYS; $s += 4*self::HOURS) $a[] = $s;
            for ($s = 7*self::DAYS; $s < 14*self::DAYS; $s += 8*self::HOURS) $a[] = $s;
            for ($s = 14*self::DAYS; $s < 30*self::DAYS; $s += 12*self::HOURS) $a[] = $s;
            for ($s = 30*self::DAYS; $s < 60*self::DAYS; $s += 1*self::DAYS) $a[] = $s;
            for ($s = 60*self::DAYS; $s < 120*self::DAYS; $s += 7*self::DAYS) $a[] = $s;
            for ($s = 120*self::DAYS; $s < 366*self::DAYS; $s += 30*self::DAYS) $a[] = $s;

            self::setIntervals($a);

            // self::$intervals = array(
            //     10*self::MINUTES, 20*self::MINUTES, 30*self::MINUTES,
            //     1*self::HOURS, 2*self::HOURS, 2.5*self::HOURS
            // );
        }

        // keep all files younger than the first interval (age younger than $intervals[0])
        // keep one file older than the last interval (age older than $intervals[max]) -- the youngest file
        // keep two files between all other intervals (the youngest and the oldest)
        public static function PruneBackupsEx(): void {
            if (self::$intervals == null) { 
                self::initIntervals(); 
            }
            $numIntervals = count(self::$intervals);
            
            $files = glob(sprintf(self::FILEPATH_FMT, '*'));
            $numFiles = count($files);
            if ($files === false || $numFiles === 0) {
                Log::Concern("no backup files found");
                return;
            }
            rsort($files); // sorting by name also sorts by time, from youngest to oldest, due to file naming convention
            Log::Entry("Found $numFiles files.", $files[0], $files[$numFiles-1]);

            $now = time();
            $i = 0;
            $firstDemarc = self::$intervals[$i];
            $high = $firstDemarc;
            $f = 0;
            $prev = null; // a prev file is a file marked for deletion (if we find another after it, in the same interval)
            $age = 0;
            $prevAge = null;
            for($f = 0; $f < $numFiles; $f++) {
                $file = $files[$f];
                clearstatcache(true, $file);
                $age = $now - filemtime($file);

                if ($i == 0 && $age < $high) {
                    Log::Entry("Keeping file with age <= ${high}s", basename($file), $age);
                }
                
                if ($age > $high && $i+1 < $numIntervals) {
                    if ($prev != null) { Log::Entry("Keeping last file with age <= ${high}s", basename($prev), $prevAge); }
                    while ($age > $high && $i+1 < $numIntervals) {
                        $i++;
                        $high = self::$intervals[$i];
                    }
                    // keep $file, the youngest in it's interval
                    Log::Entry("Keeping first file with age <= ${high}s", basename($file), $age);
                    $prev = null;
                    $prevAge = null;
                    continue;
                }

                if ($prev != null) {
                    // delete the previous file, which is from the same interval as $file; $prev is neither the youngest nor oldest
                    Log::Entry("Removing middle file with age <= ${high}s", basename($prev), $prevAge);
                    unlink($prev);
                }

                if ($age >= $firstDemarc && $f > 0) { // file is not younger than the first interval
                    // remember $file in case we find one that is older from the same interval
                    $prev = $file;
                    $prevAge = $age;
                }
            }
            if ($prev != null && $age > $high && $i+1 == $numIntervals) {
                // delete the last (oldest) file that is older than the last interval
                Log::Entry("Removing last file with age > ${high}s", basename($prev), $prevAge);
                unlink($prev);
            } else if ($prev != null) {
                Log::Entry("Keeping Last file with age <= ${high}s", basename($prev), $prevAge);
            }
        }
    }
?>