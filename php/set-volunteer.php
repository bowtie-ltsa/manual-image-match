<?php
    declare(strict_types=1);
    require_once "first-things.php";

    define("DAYS", '60*60*24');
    $vid = $_GET['vid'];
    setcookie("vid", $vid, time()+90*DAYS);

    $accounts = readAccounts(CONFIG.ACCOUNTS);
    ?>
        <pre>
            so far so good: 
            - we've set a cookie to remember vid
            - we've read the list of accounts into an associate array
            - next up: validate the vid, and redirect to the show-question page!

the accounts list:
<?print_r($accounts)?>
        </pre>
        hello <?=$vid?>.<br>
        <a href="/">home</a>
    <?


    //header('Location: show-question.php')
    exit();
?>