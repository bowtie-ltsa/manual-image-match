<?php
    declare(strict_types=1);
    require_once "first-things.php";

    include "get-volunteer.php";

    $files = glob(sprintf(DataZipper::FILEPATH_FMT, '*'));
    $numFiles = count($files);
    if ($files === false || $numFiles === 0) {
        Log::Concern("no results found");
        header("Location: index.php?problem=no-results-found!");
        exit();
    }
    rsort($files); // sorting by name also sorts by time, from youngest to oldest, due to file naming convention
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require "_Head1.html" ?>
    <meta name="description" 
          content="List zips containing snapshots of results" 
          />
    <meta name="author" content="Bowtie" />
    <title>Results | Capture Match</title>
    <?php require "_Head2.html" ?>
</head>
<body>
    <?php require "_TopNav.php" ?>
    <div class="container">

        <div class="row">
            <div class="col-sm-6">
                <div class="row" style="margin-bottom: 36px;">
                    <div class="col-xs-12 new-tegomin-xl text-success">
                        <span class="">
                            Results
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <span class="">
                            <? include "get-results-list.php" ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="row" style="margin-bottom: 36px;">
                    <div class="col-xs-12 new-tegomin-xl text-success">
                        Readme
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <? include "get-results-blurb.php" ?>
                    </div>
                </div>
            </div>
        </div>
        <?php require "_zFooter-Nav.php" ?>
    </div> <!-- /container -->
    <?php require "_zFooter-zBootstrap.html" ?>
</body>
</html>
