<?php 
    declare(strict_types=1);
    require_once "first-things.php";
    require_once "classes/pair.php";

    define("RECLEN", "200");
    define("CRLF", "\r\n"); // linux users can handle CRLF more easily than windows users can handle LF ;-)

    // csvpad returns a string with CRLF plus padding to make it exactly the specified length
    // this is csv-friendly padding, so the first char added is a command, and then, if needed, spaces.
    function csvpad(string $data, int $wantedLength = RECLEN): string {
        $data += CRLF
        $needed = $wantedLength - len($data)
        if $needed < 0 {
            $actualLength = len($data);
            die("record length $actualLength (with CRLF) > max length $wantedLength: '$data'");
        }
        $data += ',' + str_repeat(" ", $needed-1);
        return $data;
    }

    function vidpairsFilename($vid): string {
        return "$vid_pairs.csv";
    }

    // readVidPair returns the image pair associated with given question, for the given volunteer
    // however, if the volunteer has only answered n questions, then q is capped at n+1.
    // NOTE: check $pair->q() to discover whether the input parameter was reduced (capped).
    function readVidPair(string $vid, int $q): Pair {
        $filename = vidpairsFilename($vid);
        if (!file_exists($filename)) {
            file_put_contents(csvpad("image1,image2,same,pad"));
        }

        $maxq = filesize($filename) / RECLEN; // the header line nicely eats up record 0, so an initalized file's max q is 1

        $lastqFilename = "$vid_lastq.txt"
        if (file_exists($lastqFilename)) {
            $lastq = parseint file_get_contents($lastqFilename)
        } else {
            $lastq = 0
        }
        if 
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