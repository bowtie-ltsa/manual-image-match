<?php
    declare(strict_types=1);
    require_once "first-things.php";

    // get $vid, and optional parameters $did (decision id) and $ipid (imagepair id)
    include "get-volunteer.php";
    $did = getIntParam("did");
    $ipid = getStringParam("ipid");

    list($opp, $err) = TheCoordinator::GetOpportunity($vid, $did, $ipid)->Result();
    if ($err instanceof BusyException) {
        header("Location: please-wait.php?vid=$vid". qsParam("did", $did) . qsParam("ipid", $ipid));
        exit();
    }
    if ($err instanceof HaltException) {
        exit();
    }
    if ($err instanceof VidFinishedException) {
        header("Location: vid-finished.php?$vid=$vid");
        exit();
    }
    if ($err != null) {
        writeln("halting on unexpected error:");
        echo preTrace($err);
        exit();
    }    

    $question = file_get_contents(CONFIG_DIR . "question.txt");
    $sameClass = $opp->decision === 1 ? "Active" : "";
    $diffClass = $opp->decision === 0 ? "Active" : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require "_Head1.html" ?>
    <meta name="description" 
          content="Decide whether two images match." 
          />
    <meta name="author" content="Bowtie" />
    <title>Image Pair <?=$opp->ipid?></title>
    <?php require "_Head2.html" ?>
</head>
<body>
    <?php require "_TopNav.html" ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 helvetica-big text-successNO">
                <?=$question?>
            </div>
        </div>

        <div class="row overflowy">
            <div class="col-xs-6">
                <div class="row">
                    <div class="col-xs-12">
                        <img class="img-responsive center-block" src="/<?=$opp->path1?>" alt="/<?=$opp->path1?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <?=$opp->path1?>
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="row">
                    <div class="col-xs-12">
                        <img class="img-responsive center-block" src="/<?=$opp->path2?>" alt="/<?=$opp->path2?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <?=$opp->path2?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?pre_dump($opp)?>
            </div>
        </div>
        <!--?php require "_zFooter-Nav.php" -->
    </div> <!-- /container -->
    <footer class="page-footer navbar-fixed-bottom navbar-inverse">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <form class="form-horizontal" action="save-decision.php" method="POST">
                        <div class="form-group form-inline" style="margin: 5px 7px 5px 7px;">
                            <div class="form-row">
                                <div class="col-sm-5">
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                      <label class="btn btn-info <?=$sameClass?>" onclick="setDirty();">
                                        <input type="radio" name="options" id="option1" autocomplete="off" checked> Same
                                      </label>
                                      <label class="btn btn-info <?=$diffClass?>" onclick="setDirty();">
                                        <input type="radio" name="options" id="option3" autocomplete="off"> Different
                                      </label>
                                    </div>
                                    <script>
                                        var isDirty = false;
                                        var isSaving = false;
                                        function setDirty() { 
                                            isDirty = true;
                                            $('#saveButton').removeClass('hidden');
                                        }
                                        function setSaving() {
                                            isSaving = true;
                                        }
                                        window.onbeforeunload = function() {
                                            if (isDirty && !isSaving) { 
                                                // this text may not be shown, but instead trigger the browser's own version of this prompt
                                                return 'Your decision has not been saved. Are you sure you want to leave the page?'; 
                                            }
                                        };
                                    </script>

                                    <button id="saveButton" class="btn btn-primary hidden" type="submit" onclick="setSaving();">Save</button>
                                </div>
                                <div class="col-sm-7"><div class="navbar-right">
                                    <button class="btn btn-primary" type="submit">&lt;&lt;</button>
                                    <button class="btn btn-primary" type="submit">-10</button>
                                    <button class="btn btn-primary" type="submit">Prev</button>
                                    <button class="btn btn-primary" type="submit">Next</button>
                                    <button class="btn btn-primary" type="submit">+10</button>
                                    <button class="btn btn-primary" type="submit">&gt;&gt;</button>
                                </div></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </footer>
    <?php require "_zFooter-zBootstrap.html" ?>
</body>
</html>
