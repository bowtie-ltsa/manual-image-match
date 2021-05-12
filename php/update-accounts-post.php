<?php
    declare(strict_types=1);

    if (getPostedString(AUTHORITY) != file_get_contents(CONFIG_DIR . "accounts-update-authority.txt")) {
        die("not authorized");
    }

    $text = trim(getPostedString(ACCOUNTSTEXT));
    if ($text == null || $text == "") {
        die("panic: invalid text");
    }
    $lines = explode("\r\n", $text); // browser post data does not depend on operating system, always CRLF
    if ($lines[0] != "name,role") {
        die("panic: column headers are missing: line 0 is \"$lines[0]\"");
    }
    $i = 0;
    foreach ($lines as $line) {
        $i++;
        $fields = explode(",", $line);
        if (count($fields) != 2) {
            die("panic: line $i (\"$line\"): found " . count($fields) . " field(s), expecting 2 fields");
        }
        for ($j = 0; $j < 2; $j++) {
            if (preg_match('/^[A-Za-z0-9-]+$/', $fields[$j]) != 1) {
                die("panic: line $i ($line), field $j (\"$fields[$j]\"): invalid characters: allowed characters are A-Z, a-z, 0-9 and dash ('-').");
            }
        }
    }

    $now = (new DateTime("now", new DateTimeZone('America/Los_Angeles')))->format("Y-m-d--H-i-s.v");
    $backupName = Account::$filepath . ".backup--$now--.csv";
    $originalText = file_get_contents(Account::$filepath);
    $result = file_put_contents($backupName, $originalText);
    if ($result === false || $result == 0) {
        die("panic: failed to create backup file");
    }

    file_put_contents(Account::$filepath, $text);
    file_put_contents(Account::$filepath . ".save--$now--.csv", $text);
    $updateResultMsg = "List Updated $now";
?>