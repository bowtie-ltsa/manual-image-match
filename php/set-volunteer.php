<?php
    declare(strict_types=1);
    require_once "first-things.php";
    require_once "get-volunteer.php";

    define("DAYS", '60*60*24');
    setcookie("vid", $vid, time()+90*DAYS);

    header("Location: show-question.php?vid=$vid");
    exit();
?>