<?php 
    declare(strict_types=1);

    function fileOrEmpty($filename): array {
        $result = @file(DATADIR . $filename, FILE_IGNORE_NEW_LINES);
        if ($result) { return $result; }
        else { return array(); }
    }

?>