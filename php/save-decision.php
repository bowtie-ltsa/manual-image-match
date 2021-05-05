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
        ?>
            <div class="helvetica">
                hmm, something unexpected happenned and we made a note of it.
                That's about all we can say.
                <br />
                <br />
                Please <a href="show-image-pair.php?vid=<?=$vid?>">continue matching</a> if you can! 😎 We'll figure it out.
                <br />
                <br />
                The error has been logged and will be investigated.
                If you have time, please <a href="mailto:<?=file_get_contents(CONFIG_DIR . 'contact-email.txt')?>">drop a line</a>
                to let us know you're interested in helping us figure out what went wrong. If not, no worries.
                <br />
                <br />
                The important thing is to 
                <a href="show-image-pair.php?vid=<?=$vid?>">keep on matching</a> if you can!
                <br />
                <br />
            </div>
        <?
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
