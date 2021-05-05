<div class="helvetica">
    hmm, something unexpected happenned and we made a note of it.
    That's about all we can say.
    <br />
    <br />
    Please <a href="show-image-pair.php?vid=<?=$vid?>">continue matching</a> if you can! 😎 We'll figure it out.
    <br />
    <br />
    The error has been logged and will be investigated.
    If you have time, please <a href="mailto:<?=file_get_contents(CONFIG_DIR . 'contact-email.txt')?>">drop a line</a>
    to let us know you're interested in helping us figure out what went wrong. If not, no worries.
    <br />
    <br />
    The important thing is to 
    <a href="show-image-pair.php?vid=<?=$vid?>">keep on matching</a> if you can!
    <br />
    <br />
</div>
<?
    echo preTrace($err);
?>
