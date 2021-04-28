<?php
    declare(strict_types=1);
    require_once "first-things.php";

    // get $vid, and optional parameters $did (decision id) and $ipid (imagepair id)
    include "get-volunteer.php";
    $did = getIntParam("did");
    $ipid = getStringParam("ipid");

    list($opp, $err) = TheCoordinator::GetOpportunity($vid, $did, $ipid)->Result();
    if ($err instanceof BusyException) {
        header("Location: please-wait.php?vid=$vid&q=$ipa->q()");
        exit();
    }
    if ($err instanceof HaltException) {
        exit();
    }
    if ($err instanceof VidFinishedException) {
        header("Location: vid-finished.php?$vid=$vid&did=$did&ipid=$ipid");
        exit();
    }
    if ($err != null) {
        writeln("halting on unexpected error:");
        echo preTrace($err);
        exit();
    }
    if ($did != $opp->$somethingsomething || $ipid != $opp->$somethingelse) { // decision requested did not exist, so redirect
        // redirect so the page is properly bookmarkable
        header("Location: show-question.php?vid=$vid&did=something&ipid=somethingelse");
        exit();
    }

?>
<div>
ready to display this opportunity:
</div>
<?pre_dump($opp)?>

hello <?=$vid?>.<br>
<a href="/">home</a>
