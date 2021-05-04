<?php 
    require_once "first-things.php";
    include "get-volunteer.php";
    $lastDecision = DecisionList::ForVolunteer($vid)->Count() - 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require "_Head1.html" ?>
    <meta name="description" 
          content="Explain that the volunteer has finished." 
          />
    <meta name="author" content="Bowtie" />
    <title>Finished! | Capture Match</title>
    <?php require "_Head2.html" ?>
</head>
<body>
    <?php require "_TopNav.php" ?>
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-xs-12 text-center new-tegomin-xl text-success">
                        You are done!
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <h3 class="text-green">
                            Wow - You finished! Amazing!
                        </h3>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <span class="">
                            <div class="helvetica-md" style="color: black;">
                                You've managed to compare **all** the image pairs we have!
                                Thank&nbsp;you!!!
                            </div>
                        </span>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <div class="">
                            You can <a href="show-image-pair.php?vid=<?=$vid?>&did=<?=$lastDecision?>"
                            >review your decisions</a> if you want.
                        </div>
                    </div>
                </div>
                <br />
                <!-- 
                <form class="form-horizontal" action="index.php">
                    <div class="row form-group form-inline">
                        <div class="col-xs-12 text-center">
                            <button class="btn btn-green" type="submit">Return to home page</button>
                        </div>
                    </div>
                </form>
                -->
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <span class="">
                            If you have any questions or concerns, please 
                            <a href="mailto:<?=file_get_contents(CONFIG_DIR . 'contact-email.txt')?>">contact us</a>.
                        </span>
                    </div>
                </div>
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
