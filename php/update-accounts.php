<?php
    declare(strict_types=1);
    require_once "first-things.php";

    include "get-volunteer.php";
    //sleep(6);
    if (!Account::IsAdmin()) {
        die("(forbidden)");
    }

    $updateResultMsg = "";
    define("AUTHORITY", "authority");
    define("ACCOUNTSTEXT", "accountsText");
    define("SUBMIT", "submit");
    if (getPostedString(SUBMIT) !== null) {
        include "update-accounts-post.php";
    }

    $numRows = count(Account::$accounts) + 3;
    $accountsText = file_get_contents(Account::$filepath);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require "_Head1.html" ?>
    <meta name="description" 
          content="modify list of volunteers" 
          />
    <meta name="author" content="Bowtie" />
    <title>Accounts | Capture Match</title>
    <?php require "_Head2.html" ?>
</head>
<body>
    <?php require "_TopNav.php" ?>
    <div class="container">

        <div class="row">
            <div class="col-sm-6">
                <div class="row" style="margin-bottom: 36px;">
                    <div class="col-xs-12 new-tegomin-xl text-success">
                        <span class="text">
                            Volunteers
                        </span>
                    </div>
                </div>
                <form class="form-horizontal" action="update-accounts.php?vid=<?=$vid?>" method="POST"
                    onsubmit="if (prompt('are you sure? type \'yes\'') != 'yes') { return false; }">
                    <div class="row form-group">
                        <div class="col-xs-12">
                            <div class="form-inline">
                                <label class="text-success"><?=$updateResultMsg?></label>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-xs-12">
                            <label for="<?=ACCOUNTSTEXT?>">List:</label>
                            <textarea class="form-control" name="<?=ACCOUNTSTEXT?>" rows="<?=$numRows?>"
                                ><?=$accountsText?></textarea>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-xs-12">
                            <div class="form-inline">
                                <label for="<?=AUTHORITY?>">Authority:</label>
                                <input class="form-control" name="<?=AUTHORITY?>" type="password" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-xs-12">
                            <div class="form-inline">
                                <button class="btn btn-warning" name="submit" type="submit">Replace List</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-sm-6">
                <div class="row" style="margin-bottom: 36px;">
                    <div class="col-xs-12 new-tegomin-xl text-success">
                        Readme
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                    <img class="img-responsive center-block" src="/images/caution.svg" alt="caution sign" />
                    <div>
                        Notes:
                        <ol style="margin-top: 0px;">
                            <li>
                                <!-- <span class="text-danger" style="font-weight: bold;">CAUTION:</span>  -->
                                Don't leave out <span class="text-danger" style="font-weight: bold;">role</span>.
                                Every line must have two fields: name,role.
                            </li>
                            <li>
                                Removing a volunteer does not remove any of their data. You can add them back later, no problem.
                            </li>
                            <li>
                                Renaming a volunteer is the same as removing and adding.
                                The volunteer will start recording decisions under their new name.
                            </li>
                            <li>Don't remove yourself. 😉</li>
                        </ol>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require "_zFooter-Nav.php" ?>
    </div> <!-- /container -->
    <?php require "_zFooter-zBootstrap.html" ?>
</body>
</html>
