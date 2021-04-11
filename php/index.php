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
    <?php require "_TopNav.html" ?>
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
                        <h3>
                            Allowing volunteers to<br />
                            identify recaptures by hand
                        </h3>
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
        <?php require "_zFooter-Nav.html" ?>
    </div> <!-- /container -->
    <?php require "_zFooter-zBootstrap.html" ?>
</body>
</html>
