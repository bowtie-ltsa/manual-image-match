<?php
    declare(strict_types=1);
    require_once "first-things.php";

    // get $vid, and optional parameters $did (decision id) and $ipid (imagepair id)
    include "get-volunteer.php";
    $did = getIntParam("did");
    $ipid = getStringParam("ipid");

    list($opp, $err) = TheCoordinator::GetOpportunity($vid, $did, $ipid)->Result();
    if ($err instanceof BusyException) {
        include "please-wait.php";
        exit();
    }
    if ($err instanceof HaltException) {
        exit();
    }
    if ($err instanceof VidFinishedException) {
        header("Location: vid-finished.php?vid=$vid");
        exit();
    }
    if ($err != null) {
        include 'message-for-unexpected-error.php';
        exit();
    }    

    $question = file_get_contents(CONFIG_DIR . "question.txt");
    $sameClass = $opp->decision === '1' ? "active" : "";
    //$sameChecked = $opp->decision === '1' ? 'checked="true"' : "";
    $sameChecked = "";
    $diffClass = $opp->decision === '0' ? "active" : "";
    //$diffChecked = $opp->decision === '0' ? 'checked="true"' : "";
    $diffChecked = "";
    $decisionCount = DecisionList::ForVolunteer($vid)->Count();

    if ($decisionCount == 0) {
        $greeting = "👈 click one of these butons 😎"; // 🙂 or maybe 😎 would be good?
        $shortGreeting = "☝️ click up there ☝️ 😎"; // we're stuck below so let's point up!
    } else {
        $badgeChars = array("👍", "⭐", "✨", "&#x1F929;");
        $badges = array();
        $x = $decisionCount;
        $lastChar = "";
        while ($x > 0) {
            $char = array_shift($badgeChars);
            $lastChar = $char;
            $badges[] = str_repeat($char, $x % 10);
            $x = intdiv($x, 10);
        }
        $bestBadge = $lastChar;
        $badges = implode("", array_reverse($badges));
        $plural = $decisionCount != 1 ? "s" : "";
        $decisionsMade = "$decisionCount decision$plural made!";
        $greeting = "$badges $decisionsMade";
        $shortGreeting = "$badges $decisionsMade"; // ignore $bestBadge for now
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require "_Head1.html" ?>
    <meta name="description" 
          content="Decide whether two images match." 
          />
    <meta name="author" content="Bowtie" />
    <title>Image Pair <?=$opp->ipid?> | Capture Match</title>
    <?php require "_Head2.html" ?>
</head>
<body>
    <?php require "_TopNav.php" ?>
    <div class="container-fluid">
        <div class="row">
            <div class="hidden-xs col-sm-12 helvetica-big text-successNO">
                <?=$question?>
            </div>
            <!-- no need to display the question here; the _TopNav.php took care of it -->
            <!--
            <div class="visible-xs col-sm-12 helvetica-md text-successNO">
                <?=$question?>
            </div>
            -->
        </div>

        <div class="row overflowy">
            <div class="col-xs-6">
                <div class="row">
                    <div class="col-xs-12">
                        <img class="img-responsive center-block" src="/<?=IMAGE_DATA_DIR?><?=$opp->path1?>" alt="/<?=$opp->path1?>" />
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
                        <img class="img-responsive center-block" src="/<?=IMAGE_DATA_DIR?><?=$opp->path2?>" alt="/<?=$opp->path2?>" />
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
                    <!-- form -->
                    <form class="form-horizontal" action="save-decision.php?vid=<?=$vid?>&ipid=<?=$opp->ipid?>" method="POST"
                        onsubmit="$('#saveButton').prop('disabled', true).html('<i>please wait...</i>').addClass('btn-warning disabled');">
                        <!-- disabling the button is important but requires always-pointer-hack (arrow pointer) on the button to avoid left-over "not allowed" cursor after page loads -->
                        <? if ($did !== null) { ?> <input type="hidden" name="did" value="<?=$did?>" /> <? } ?>
                        <div class="form-group form-inline" style="margin: 5px 7px 5px 7px;">
                            <div class="form-row">
                                <div class="col-sm-12">
                                    <div class="navbar-left">
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-info <?=$sameClass?>" <?=$sameChecked?> onclick="setDirty();">
                                              <input type="radio" name="decision" value="1" id="decisionSame" autocomplete="off"> Same
                                            </label>
                                            <label class="btn btn-info <?=$diffClass?>" <?=$diffChecked?> onclick="setDirty();">
                                              <input type="radio" name="decision" value="0" id="decisionDiff" autocomplete="off"> Different
                                            </label>
                                        </div>
                                        <button id="saveButton" class="btn btn-primary always-pointer-hack hidden" type="submit" onclick="setSaving();">Save</button>
                                        <span class="hidden-xs" style="color:white;"><?=$greeting?></span>
                                        <span class="visible-xs" style="color:white; font-size: smaller;"><?=$shortGreeting?></span>
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
                                    </div>

                                    <?
                                        $vcrBackwardClass = $did === 0 || $decisionCount === 0 ? "disabled" : "";
                                        $vcrForwardClass = $did === null ? "disabled" : "";
                                    ?>
                                    <div class="navbar-right">
                                        <button class="btn btn-primary vcr <?=$vcrBackwardClass?>" <?=$vcrBackwardClass?> type="button" onclick="vcr('min');">&lt;&lt;</button>
                                        <button class="btn btn-primary vcr <?=$vcrBackwardClass?>" <?=$vcrBackwardClass?> type="button" onclick="vcr(-10);">-10</button>
                                        <button class="btn btn-primary vcr <?=$vcrBackwardClass?>" <?=$vcrBackwardClass?> type="button" onclick="vcr(-1);">Prev</button>
                                        <div class="btn btn-info not-a-button-hack vcr new-tegomin" style="width: 4em;">
                                            <? if ($did !== null) { echo "#" . ($did+1); } else { echo "(new)"; } ?> <!-- "#" . ($decisionCount + 1); -->
                                        </div>
                                        <button class="btn btn-primary vcr <?=$vcrForwardClass?>"  <?=$vcrForwardClass?>  type="button" onclick="vcr(1);">Next</button>
                                        <button class="btn btn-primary vcr <?=$vcrForwardClass?>"  <?=$vcrForwardClass?>  type="button" onclick="vcr(10);">+10</button>
                                        <button class="btn btn-primary vcr <?=$vcrForwardClass?>"  <?=$vcrForwardClass?>  type="button" onclick="vcr('max');">&gt;&gt;</button>
                                    </div>
                                    <script>
                                        function vcr(offset) {
                                            var url = 'show-image-pair.php?vid=<?=$vid?>';

                                            var did = <?= $did ?? $decisionCount ?>; // did -- decision id
                                            switch (offset) {
                                                case 'min': did = 0; break;
                                                case 'max': did = <?=$decisionCount?>; break;
                                                default: did = did + offset; break;
                                            }
                                            var didParam = null;
                                            if (did < 0) {
                                                didParam = '&did=0'; // go to first decision
                                            } else if (did >= <?=$decisionCount?>) {
                                                didParam = ''; // go to the end (to image pair waiting for a decision)
                                            } else {
                                                didParam = '&did=' + did; // go to this decision #
                                            }
                                            window.location.href = url + didParam;
                                        }
                                    </script>
                                </div>
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
