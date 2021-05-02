<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <div class="visible-xs pull-left navhack" style="margin:8px; color: white;"><?=@$question?></div>
            <button type="button" class="navbar-toggle btn btn-info" data-toggle="collapse" data-target="#navbar">
                Menu
                <!--<span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>-->
            </button>
            <!-- <a class="navbar-brand" href="/">
                <img src="/images/bowtie50px.png" />
            </a> -->
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li><a href="/">Home</a></li>
            </ul>
            <ul class="nav navbar-nav">
                <li>
                    <a href="mailto:<?=file_get_contents(CONFIG_DIR . 'contact-email.txt')?>">Contact Us</a>
                </li>
            </ul>
            <? if (isset($vid)) { ?>
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="#">Thank you, <?=$vid?>!</a>
                    </li>
                </ul>
            <? } ?>
        </div>
    </div>
</div>
