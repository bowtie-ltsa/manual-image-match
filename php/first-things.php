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
            echo preTrace($ex);
            throw $ex;
        }
    });

    require_once "classes/exceptions.php";
    require_once "functions/stackTrace.php";
    require_once "functions/cast.php";
    
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

    function getIntParam($pname): ?int { return isset($_GET[$pname]) ? intval($_GET[$pname]) : null; }
    function getStringParam($pname): ?string { return isset($_GET[$pname]) ? $_GET[$pname] : null; }

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
            // $folders = explode(",", $item['foldersCSV']);
            $acct = new Account($name);
            $accounts[$name] = $acct;
        }
        return $accounts;
    }
?>