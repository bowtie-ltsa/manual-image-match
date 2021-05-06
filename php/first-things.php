<?php 
    declare(strict_types=1);
    error_reporting(E_ALL);

    spl_autoload_register(function($className) {
        try {
            $filename = "classes/${className}.php";
            if (!file_exists($filename)) { throw new Exception("file not found: $filename"); }
            require_once $filename;
        }
        catch (Exception $ex) {
            // echo preTrace($ex);
            throw $ex;
        }
    });

    require_once "classes/exceptions.php";
    require_once "functions/stackTrace.php";
    require_once "functions/cast.php";

    define("IMAGE_DATA_DIR", 'image-data/');
    define("CONFIG_DIR", "config/");

    function define_ENV_CONSTANTS() {
        $envName = explode(":", $_SERVER['HTTP_HOST'])[0];

        $datadirFile = sprintf("%sdatadir-%s.txt", CONFIG_DIR, $envName);
        $datadir = file_get_contents($datadirFile);
        define("DATA_DIR", $datadir);

        $backupdirFile = sprintf("%sbackupdir-%s.txt", CONFIG_DIR, $envName);
        $backupdir = file_get_contents($backupdirFile);
        define("BACKUP_DIR", $backupdir);

        $backupurlFile = sprintf("%sbackup-url-%s.txt", CONFIG_DIR, $envName);
        $backupurl = file_get_contents($backupurlFile);
        define("BACKUP_URL", $backupurl);
    }
    define_ENV_CONSTANTS();

    //Log::Init(LogLevel::Debug);
    Log::Init(LogLevel::Entry);
    //Log::Init(LogLevel::Event);

    define("LOCKTIME", 3000);
    define("PIPE", "|");
    define("JSON_FMT", JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);

    function debug(...$args) {
        foreach($args as $arg) { echo $arg . " "; }
        echo "<br />\n";
    }

    function writeln(...$args) {
        foreach($args as $arg) { echo $arg . " "; }
        echo "<br />\n";
    }

    function pre_dump($args) {
        echo "<pre>";
        var_dump($args);
        echo "</pre>";
    }

    function getIntParam(string $pname): ?int { return isset($_GET[$pname]) ? intval($_GET[$pname]) : null; }
    function getStringParam(string $pname): ?string { return isset($_GET[$pname]) ? $_GET[$pname] : null; }
    function qsParam(string $pname, $pvalue): string { return $pvalue !== null ? "&$pname=$pvalue" : "";}

    function getPostedInt(string $pname): ?int { return isset($_POST[$pname]) ? intval($_POST[$pname]) : null; }
    function getPostedString(string $pname): ?string { return isset($_POST[$pname]) ? $_POST[$pname] : null; }

    function readCsv(string $filename): array {
        $rows   = array_map('str_getcsv', file($filename)); // consider fgetcsv() loop instead to process newlines in values, and to save memory
        $header = array_shift($rows);
        $countColumns = count($header);
        $csv    = array();
        $i = 0;
        foreach($rows as $row) {
            $i++;
            if (count($row) != $countColumns) { throw new Exception("panic: invalid row $i reading accounts"); }
            $csv[] = array_combine($header, $row);
        }
        return $csv;
    }

    function readAccounts(string $filename): array {
        $accounts = array();
        $list = readCsv($filename);
        foreach($list as $item) {
            $name = $item['name'];
            $role = @$item['role'];
            $acct = new Account($name, $role);
            $accounts[strtolower($name)] = $acct;
        }
        return $accounts;
    }
?>