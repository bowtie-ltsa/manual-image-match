<?php
    declare(strict_types=1);
    require_once "first-things.php";
    require_once "createCookie.php";

    include "get-volunteer.php";

    const DAYS = 60*60*24;
    createcookie("vid", $vid, 90*DAYS);

    header("Location: show-question.php?vid=$vid");
    exit();
?>