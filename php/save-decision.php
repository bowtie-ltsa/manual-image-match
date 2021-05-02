<?php
    declare(strict_types=1);
    require_once "first-things.php";

    // get $vid, and optional parameters $did (decision id) and $ipid (imagepair id)
    include "get-volunteer.php";
    $ipid = getStringParam("ipid");
    if ($ipid == null) {
        Log::PanicAndDie("panic: the form submission is invalid");
    }
    
    $decision = getPostedInt("decision");
    if ($decision === null) {
        Log::PanicAndDie("panic: the form submission is not valid");
    }

    $didRaw = getPostedString("did");
    $did = getPostedInt("did");
    if ($did !== null && ($did < 0 || !preg_match('/^[0-9]+$/', $didRaw))) {
        Log::PanicAndDie("panic: the form submission isn't valid");
    }
    
    $err = TheCoordinator::SaveDecision($vid, $did, $ipid, $decision);

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

    if ($did !== null) {
        // they changed their mind while reviewing past decisions;
        // don't navigate away from their place in history;
        // show them the same page again (now updated and "save" button will disappear)
        header("Location: show-image-pair.php?vid=$vid&did=$did&ipid=$ipid");
        exit();
    }

    // they just saved a new decision; take them to a page that will
    // get their next opportunity to make a new decision.
    header("Location: show-image-pair.php?vid=$vid");
   
?>
