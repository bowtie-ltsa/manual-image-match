<?php 
    declare(strict_types=1);

    function fileOrEmpty(string $filename): array {
        $result = @file($filename, FILE_IGNORE_NEW_LINES);
        if ($result) { return $result; }
        return array();
    }

    function fileInt(string $filename, int $min = 0): int {
        $result = @file_get_contents($filename);
        if ($result && $result >= $min) return $result + 0;
        return $min;
    }

    function vidResultsFilename(string $vid): string {
        return "$vid-results.psv";
    }

    function vidNextFilename(string $vid): string {
        return "$vid-next-allocation-id.txt";
    }

?>