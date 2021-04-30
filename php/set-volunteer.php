<?php
    declare(strict_types=1);
    require_once "first-things.php";
    require_once "functions/createCookie.php";

    include "get-volunteer.php";

    const DAYS = 60*60*24;
    createcookie("vid", $vid, 90*DAYS);

    header("Location: show-image-pair.php?vid=$vid");
    exit();
?>