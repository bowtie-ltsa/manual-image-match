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
?>
<div>
ready to display this opportunity:
</div>
<?pre_dump($opp)?>

hello <?=$vid?>.<br>
<a href="/">home</a>
