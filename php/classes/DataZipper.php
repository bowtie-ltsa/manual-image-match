<?php 
    declare(strict_types=1);

    class DataZipper {
        public const FILENAME_FMT = "capture-match-backup--%s.zip";
        public const FILEPATH_FMT = BACKUP_DIR . self::FILENAME_FMT;

        public const BACKUP_INTERVAL_SECONDS = 10;
        public const BACKUP_TRACKER_FILENAME = "last-backup-time.txt";
        public const BACKUP_TRACKER_FILEPATH = DATADIR . BACKUP_TRACKER_FILENAME;

        public static function BackupAtInterval(): void {
            
        }

        public static function Backup(): void {
            try {
                $now = (new DateTime("now", new DateTimeZone('America/Los_Angeles')))->format("Y-m-d--H-i-s.v");
                $filepath = sprintf(self::FILEPATH_FMT, $now);
                $zip = new ZipArchive();
                $res = $zip->open($filepath, ZipArchive::CREATE);
                if ($res === true) {
                    $zip->addFromString("test.txt", "this is a test");
                    $zip->close();
                }
            }
            catch(Exception $ex) {
                Log::Concern("Failed to create a backup", $ex->getMessage());
            }
        }
    }
?>