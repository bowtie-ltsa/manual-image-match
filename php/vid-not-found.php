<?php 
    require_once "first-things.php";
    $vid = $_GET['vid'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require "_Head1.html" ?>
    <meta name="description" 
          content="Explain that volunteer id was not found." 
          />
    <meta name="author" content="Bowtie" />
    <title>Not Found: Volunteer Id | Capture Match</title>
    <?php require "_Head2.html" ?>
</head>
<body>
    <?php require "_TopNav.php" ?>
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-xs-12 text-center new-tegomin-xl text-warning">
                        Id Not Found
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <h3 class="text-warning">
                            <? if ($vid !== '') { ?>
                                The volunteer id '<?=$vid?>' was not found.
                            <? } else { ?>
                                You must enter a volunteer id.
                            <? } ?>
                        </h3>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <span class="">
                            <h3>
                                Please check for typos, or 
                                <a href="mailto:<?=file_get_contents(CONFIG_DIR . 'contact-email.txt')?>">contact us</a>.
                            </h3>
                            <h3>
                                Thank you!
                            </h3>
                        </span>
                    </div>
                </div>
                <br />
                <form class="form-horizontal" action="index.php">
                    <div class="row form-group form-inline">
                        <div class="col-xs-12 text-center">
                            <button class="btn btn-warning" type="submit">Try again</button>
                        </div>
                    </div>
                </form>
                <!--
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <span class="">
                            If you have any questions or concerns, please 
                            <a href="mailto:<?=file_get_contents(CONFIG_DIR . 'contact-email.txt')?>">contact us</a>.
                        </span>
                    </div>
                </div>
                -->
            </div>
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-xs-12">
                        <!-- img size is "responsive" (fluid) for xs only, now that we are using 10 columns for sm+, above. -->
                        <!--<img class="img-responsive" src="images/LakeMelakwa.jpg" />-->
                        <img class="img-responsive center-block" src="/images/bowtie-not-found.png" alt="bowtie not found" />
                    </div>
                </div>
            </div>
        </div>
        <?php require "_zFooter-Nav.php" ?>
    </div> <!-- /container -->
    <?php require "_zFooter-zBootstrap.html" ?>
</body>
</html>
