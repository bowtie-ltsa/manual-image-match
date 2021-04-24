<?php
    declare(strict_types=1);
    require_once "first-things.php";
    require_once "get-volunteer.php";


    require_once "classes/imagePairManager.php";
    $pm = new ImagePairManager();
    $q = isset($_GET["q"]) ? intval($_GET["q"]) : null; // if not present; we will take the user to an unanswered image pair
    $ok = true;
    list($pair, $err) = $pm->getImagePair($vid, $q);
    if ($err instanceof BusyException) {
        header("Location: please-wait.php?vid=$vid&q=$pair->q()");
        exit();
    }
    if ($err != null) {
        header("Location: no-more-questions.php?vid=$vid&q=$pair->q()");
        exit();
    }
    if ($pair->q() != $q) { // actual pair does not match requested pair
        // redirect so the page is properly bookmarkable
        $q = $pair->q();
        header("Location: show-question.php?vid=$vid&q=$q");
        exit();
    }
?>
<pre>
now we know what pair of photos to show:
<?print_r($pair)?>
and q() is <?= $pair->q() ?>.
</pre>

hello <?=$vid?>.<br>
<a href="/">home</a>
