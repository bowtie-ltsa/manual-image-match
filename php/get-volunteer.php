<?php
    declare(strict_types=1);
    require_once "first-things.php";

    $vid = $_GET['vid'];
    if (!isset($vid) || $vid == '') {
        header('Location: index.php?problem=id-not-present');
        exit();
    }

    $accounts = readAccounts(ACCOUNTS_FILENAME);
    $account = $accounts[$vid];
    if (isset($account) === false) {
        header('Location: index.php?problem=id-not-found');
        exit();
    }

    Log::SetVid($vid);
?>