<?php
    declare(strict_types=1);
    require_once "first-things.php";

    include "get-volunteer.php";

    $files = glob(sprintf(DataZipper::FILEPATH_FMT, '*'));
    $numFiles = count($files);
    if ($files === false || $numFiles === 0) {
        debug("glob=" . sprintf(DataZipper::FILEPATH_FMT, '*'));
        debug("numFiles=$numFiles");
        pre_dump($files);
        die("what");
        Log::Concern("no results found");
        header("Location: index.php?problem=no-results-found!");
        exit();
    }
    rsort($files); // sorting by name also sorts by time, from youngest to oldest, due to file naming convention
?>
<div class="helvetica-md">
    Select the version you want. Most recent results are at the top.<br />
    You may want to focus on the "!AllDecisions.psv" file in the archive.<br />
    <br />
    You can easily import into google sheets: 
    <ol style="margin-top: 0px;">
        <li>First rename the file from *.psv to *.txt.</li>
        <li>Then use "File | Import".</li>
        <li>Specify the pipe character ("|") as the delimeter.</li>
    </ol>
    You can import *.psv files into Excel easily enough:
    <ul style="margin-top: 0px;">
        <li>"Data | GetData | File: Text/CSV" might be the fastest approach.</li>
        <li>"File | Open | All Files | (select *.psv file) | (follow the wizard)" is another approach.</li>
    </ul>
</div>
<?
    foreach ($files as $file) {
        $basename = basename($file);
        ?>
            <div class="helvetica-md">
                <a href="<?=BACKUP_URL . $basename?>"><?=$basename?></a><br />
            </div
        <?
    }
?>