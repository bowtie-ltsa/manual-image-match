<?php 
    require_once "first-things.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require "_Head1.html" ?>
    <meta name="description" 
          content="Allows volunteers to manually identify images that match." 
          />
    <meta name="author" content="Bowtie" />
    <title>Capture Match</title>
    <?php require "_Head2.html" ?>
</head>
<body>
    <?php require "_TopNav.php" ?>
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-xs-12 text-center lobster-xl text-success">
                        Capture Match
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <h3 class="text-success">Manual Image Matching</h3>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <span class="">
                            <?=file_get_contents(CONFIG_DIR . "welcome.html")?>
                        </span>
                    </div>
                </div>
                <br />
                <form class="form-horizontal" action="set-volunteer.php">
                    <div class="row form-group form-inline">
                        <div class="col-xs-12 text-center">
                            <label for="vid">Volunteer Id:</label>
                            <input class="form-control" type="text" name="vid" id="vid" autocomplete="off" 
                                    autofocus onfocus="this.select();"
                                    value="<?=$_COOKIE['vid']?>" 
                                    />
                            <button class="btn btn-primary" type="submit">Let's match!</button>
                        </div>
                    </div>
                </form>
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
                        <img class="img-responsive center-block" src="/images/bowtie.png" alt="bowtie the long-toed salamander" />
                    </div>
                </div>
            </div>
        </div>
        <?php require "_zFooter-Nav.php" ?>
    </div> <!-- /container -->
    <?php require "_zFooter-zBootstrap.html" ?>
</body>
</html>
