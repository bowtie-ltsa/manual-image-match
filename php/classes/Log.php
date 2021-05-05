<?php 
    declare(strict_types=1);

    // trying to keep logging simple. foolish? might as well be static while we're at it.
    class Log {
        public const LOG_PFX = "log";
        public const LOG_EXT = ".psv";
        public const LOG_PATH = DATA_DIR . self::LOG_PFX . self::LOG_EXT;

        public const MAXFILESIZE = 10*1024*1024;

        public const ROLL_TO_FMT = "-to-%s";
        public const ROLL_TO_PATH_FMT = DATA_DIR . self::LOG_PFX . self::ROLL_TO_FMT . self::LOG_EXT;

        public const VID_LOG_PFX_FMT = "%s-log";
        public const VID_LOG_PATH_FMT = DATA_DIR . self::VID_LOG_PFX_FMT . self::LOG_EXT;
        public const VID_ROLL_TO_PATH_FMT = DATA_DIR . self::VID_LOG_PFX_FMT . self::ROLL_TO_FMT . self::LOG_EXT;

        private static $logLevel = LogLevel::Entry;
        private static $reqid = "";
        private static $vid = "";
        private static $currentPrefix = "";
        private static $prefixUnit = ".";
        private static $untrustedClientIp = "";
        private static $untrustedForwardedForIp = "";
        private static $remoteIp = "";
        private static $machineid = "";

        
        public static function Init(int $logLevel = LogLevel::Entry): void {
            self::$logLevel = $logLevel;
            self::$reqid = hash('crc32b', $_SERVER['REMOTE_ADDR'] . $_SERVER['REQUEST_TIME_FLOAT'] . $_SERVER['REMOTE_PORT']);
            self::$untrustedClientIp = @$_SERVER['HTTP_CLIENT_ID']; // not trusted
            self::$untrustedForwardedForIp = @$_SERVER['HTTP_X_FORWARDED_FOR']; // not trusted
            self::$remoteIp = $_SERVER['REMOTE_ADDR']; // trusted but often a proxy's ip (so not that useful)
            self::$machineid = hash('crc32b', self::$remoteIp . self::$untrustedForwardedForIp . self::$untrustedClientIp);
        }

        public static function SetVid(string $vid): void { 
            self::$vid = $vid;
        }

        public static function SetPrefixUnit(string $prefixUnit): void {
            self::$prefixUnit = $prefixUnit;
        }

        public static function In(): void {
            self::$currentPrefix .= self::$prefixUnit;
        }

        public static function Out(): void {
            $i = strlen(self::$currentPrefix) - strlen(self::$prefixUnit);
            self::$currentPrefix = substr(self::$currentPrefix, 0, $i < 0 ? 0 : $i);
        }

        public static function NoPipe(string $s): string {
            return str_replace("|", "", $s);
        }

        public static function NoQuote(string $s): string {
            return str_replace('"', "'", $s);
        }

        public static function Quote(string $s): string {
            return '"'+ self::NoQuote($s) +'"';
        }

        public static function Brace(...$data): string {
            return "{ " . str_replace("=;", "=", implode(";", $data)) . " }";
        }

        public static function Debug(string $text, ...$data): void { 
            self::writelog(LogLevel::Debug, $text, $data);
        }

        public static function Mention(string $text, ...$data): void { 
            self::writelog(LogLevel::Mention, $text, $data);
        }

        public static function Note(string $text, ...$data): void { 
            self::writelog(LogLevel::Note, $text, $data);
        }

        public static function Entry(string $text, ...$data): void { 
            self::writelog(LogLevel::Entry, $text, $data);
        }

        public static function Event(string $text, ...$data): void { 
            self::writelog(LogLevel::Event, $text, $data);
        }

        public static function Concern(string $text, ...$data): void { 
            self::writelog(LogLevel::Concern, $text, $data);
        }

        public static function Warning(string $text, ...$data): void { 
            self::writelog(LogLevel::Warning, $text, $data);
        }

        public static function Panic(string $text, ...$data): void { 
            self::writelog(LogLevel::Panic, $text, $data);
        }

        public static function PanicAndDie(string $text, ...$data): void { 
            self::writelog(LogLevel::Panic, $text, $data);
            die($text . ($data ? ": " : "") . implode(";", $data));
        }

        public static function PanicException(string $text, ...$data): Exception { 
            self::writelog(LogLevel::Panic, $text, $data);
            return new Exception($text . ": " . implode(PIPE, $data));
        }

        public static function Write(int $logLevel, string $text, ...$data): void {
            self::writelog($logLevel, $text, $data); 
        }

        private static function writelog(int $logLevel, string $text, array $data): void {
            if (self::$logLevel > $logLevel) { return; }

            $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
            //pre_dump($bt);
            $callinfo = 
                (@$bt[2]['function']
                    ? @$bt[2]['class'] . @$bt[2]['type']  . @$bt[2]['function'] 
                    : basename($bt[1]['file']))
                . ':' . @$bt[1]['line']
            ;

            $now = (new DateTime("now", new DateTimeZone('America/Los_Angeles')))->format("Y-m-d--H-i-s.v");

            $entry = $now
                . PIPE . self::$reqid
                . PIPE . self::$vid
                . PIPE . self::$machineid
                // . PIPE . self::$untrustedClientIp
                // . PIPE . self::$untrustedForwardedForIp
                // . PIPE . self::$remoteIp
                . PIPE . LogLevel::Name($logLevel)
                . PIPE . $text
                . PIPE . implode(";", $data)
                . PIPE . $callinfo
                . PHP_EOL
            ;

            clearstatcache(true, self::LOG_PATH);
            if (@filesize(self::LOG_PATH) > self::MAXFILESIZE) { 
                rename(self::LOG_PATH, sprintf(self::ROLL_TO_PATH_FMT, $now)); 
            }

            file_put_contents(self::LOG_PATH, $entry, FILE_APPEND|LOCK_EX);

            global $vid;
            if (isset($vid) && strlen($vid) > 0) {
                $vidLogname = sprintf(self::VID_LOG_PATH_FMT, $vid);

                clearstatcache(true, $vidLogname);
                if (@filesize($vidLogname) > self::MAXFILESIZE) {
                    rename($vidLogname, sprintf(self::VID_ROLL_TO_PATH_FMT, $vid, $now));
                }

                file_put_contents($vidLogname, $entry, FILE_APPEND|LOCK_EX);
            }
        }

    }

?>