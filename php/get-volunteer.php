<?php
    declare(strict_types=1);
    require_once "first-things.php";

    $vid = @$_GET['vid'];
    if (!isset($vid) || $vid == '') {
        header("Location: vid-not-found.php?vid=$vid");
        exit();
    }

    $accounts = Account::ReadAccounts();
    $account = @$accounts[strtolower(trim($vid))];
    if (isset($account) === false) {
        header("Location: vid-not-found.php?vid=$vid");
        exit();
    }
    Account::Set($account);
    $vid = $account->name; // always use the defined case
    Log::SetVid($vid);
?>