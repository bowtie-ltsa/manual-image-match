<?php
    declare(strict_types=1);
    require_once "first-things.php";

    $vid = @$_GET['vid'];
    if (!isset($vid) || $vid == '') {
        header("Location: vid-not-found.php?vid=$vid");
        exit();
    }

    $filename = sprintf("%saccounts-%s.csv", CONFIG_DIR, explode(":", $_SERVER['HTTP_HOST'])[0]);
    $accounts = readAccounts($filename);
    $account = @$accounts[strtolower(trim($vid))];
    if (isset($account) === false) {
        header("Location: vid-not-found.php?vid=$vid");
        exit();
    }
    Account::Set($account);
    $vid = $account->name; // always use the defined case
    Log::SetVid($vid);
?>