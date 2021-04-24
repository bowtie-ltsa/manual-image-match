<?php 
    declare(strict_types=1);
    error_reporting(E_ALL);
    
    define("IMAGE_DATA_DIR", 'image-data/');
    define("CONFIG_DIR", "config/");

    define("ACCOUNTS_FILENAME", CONFIG_DIR . "accounts.csv");
    define("IMAGEPAIR_REJECT_PATTERN_FILENAME", CONFIG_DIR . "image-pair-reject-pattern.txt");

    define("LOCKTIME", 3000);

    function define_DATADIR() {
        $datadirFile = sprintf("%sdatadir-%s.txt", CONFIG_DIR, explode(":", $_SERVER['HTTP_HOST'])[0]);
        $datadir = file_get_contents($datadirFile);
        define("DATADIR", $datadir);
    }
    define_DATADIR();
    define("ALLPAIRS_FILENAME", CONFIG_DIR . 'all-pairs-allocations.txt');

    function debug(...$args) {
        foreach($args as $arg) { echo $arg . " "; }
        echo "<br />\n";
    }

    function pre_dump($args) {
        echo "<pre>";
        var_dump($args);
        echo "</pre>";
    }

    function readCsv(string $filename): array {
        $rows   = array_map('str_getcsv', file($filename)); // consider fgetcsv() loop instead to process newlines in values, and to save memory
        $header = array_shift($rows);
        $csv    = array();
        foreach($rows as $row) {
            $csv[] = array_combine($header, $row);
        }
        return $csv;
    }

    require_once "classes/account.php";
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