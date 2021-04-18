<?php
    declare(strict_types=1);
    require_once "first-things.php";
    require_once "get-volunteer.php";

    $q = $_GET["q"]
    if (!isset($q) || !is_numeric($q)) {
        header('Location: show-question.php?vid=$vid&q=1');
        exit();
    }

    require_once "read-vid-pair.php"
    $pair = readVidPair($vid, $q)
    if $pair->q() != $q {
        header("Location: show-question.php?vid=$vid&q=$pair->q()");
        exit();
    }
?>
<pre>
now we know what pair of photos to show:
<?print_r($pair)?>
</pre>

hello <?=$vid?>.<br>
<a href="/">home</a>
