<?php
    define("DAYS", '60*60*24');
    $vid = $_GET['vid'];
    setcookie("vid", $vid, time()+90*DAYS);
    ?>
        volunteer id recieved as "<?=$vid?>".
        <br />
        that's all there is right now. go <a href="/">home</a>
        <?php
?>