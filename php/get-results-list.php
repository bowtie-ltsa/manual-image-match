<?
    foreach ($files as $file) {
        $basename = basename($file);
        $href = BACKUP_URL . $basename;
        ?>
            <div>
                <a href="<?=$href?>"><?=$basename?></a>
            </div>
            <!-- <div><a href="<?=BACKUP_URL . $basename?>"><?=$basename?></a></div -->
        <?
    }
?>
