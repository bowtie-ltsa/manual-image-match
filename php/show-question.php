<?php
    declare(strict_types=1);
    require_once "first-things.php";
    require_once "get-volunteer.php";


    $q = isset($_GET["q"]) ? intval($_GET["q"]) : null; // if not present; we will take the user to an unanswered image pair
    $ipm = new ImagePairManager();
    list($ipa, $err) = $ipm->getImagePair($vid, $q)->Result();
    if ($err instanceof BusyException) {
        header("Location: please-wait.php?vid=$vid&q=$ipa->q()");
        exit();
    }
    if ($err instanceof HaltException) {
        exit();
    }
    if ($err instanceof VidFinishedException) {
        header("Location: no-more-questions.php?vid=$vid&q=$ipa->q()");
        exit();
    }
    if ($err != null) {
        writeln("halting on unexpected error:");
        writeln($err);
        exit();
    }
    if ($ipa->$q != $q) { // actual pair does not match requested pair
        // redirect so the page is properly bookmarkable
        $q = $ipa->$q;
        header("Location: show-question.php?vid=$vid&q=$q");
        exit();
    }
?>
<pre>
now we know what pair of photos to show:
<?print_r($ipa)?>
and q() is <?= $ipa->q() ?>.
</pre>

hello <?=$vid?>.<br>
<a href="/">home</a>
