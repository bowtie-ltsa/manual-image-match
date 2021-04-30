<?php 
    declare(strict_types=1);
    error_reporting(E_ALL);

    define("RQ_VID", "custom_data_vid");
    $_REQUEST[RQ_VID] = '';
    define("RQ_INDENT", "custom_data_indent");
    $_REQUEST[RQ_INDENT] = 0;
    define("RQ_REQID", "custom_data_request_id");
    $_REQUEST[RQ_REQID] = hash('crc32b', $_SERVER['REMOTE_ADDR'] . $_SERVER['REQUEST_TIME_FLOAT'] . $_SERVER['REMOTE_PORT']);

    spl_autoload_register(function($className) {
        try {
            $filename = "classes/${className}.php";
            if (!file_exists($filename)) { throw new Exception("file not found: $filename"); }
            require_once $filename;
        }
        catch (Exception $ex) {
            echo preTrace($ex);
            throw $ex;
        }
    });

    require_once "classes/exceptions.php";
    require_once "functions/stackTrace.php";
    require_once "functions/cast.php";
    require_once "functions/info.php";
    
    define("IMAGE_DATA_DIR", 'image-data/');
    define("CONFIG_DIR", "config/");

    function define_DATA_DIR() {
        $datadirFile = sprintf("%sdatadir-%s.txt", CONFIG_DIR, explode(":", $_SERVER['HTTP_HOST'])[0]);
        $datadir = file_get_contents($datadirFile);
        define("DATA_DIR", $datadir);
    }
    define_DATA_DIR();

    define("ACCOUNTS_FILENAME", CONFIG_DIR . "accounts.csv");
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

    function readCsv(string $filename): array {
        $rows   = array_map('str_getcsv', file($filename)); // consider fgetcsv() loop instead to process newlines in values, and to save memory
        $header = array_shift($rows);
        $csv    = array();
        foreach($rows as $row) {
            $csv[] = array_combine($header, $row);
        }
        return $csv;
    }

    function readAccounts(string $filename): array {
        $accounts = array();
        $list = readCsv($filename);
        foreach($list as $item) {
            $name = $item['name'];
            $acct = new Account($name);
            $accounts[$name] = $acct;
        }
        return $accounts;
    }
?>