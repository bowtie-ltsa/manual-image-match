<?php 
    declare(strict_types=1);


    function hi(string $s): string { $i = $_REQUEST[RQ_INDENT]; $_REQUEST[RQ_INDENT] = $i + 1; return str_repeat(" ", $i) . "->" . $s; }
    function bye(string $s): string { $i = $_REQUEST[RQ_INDENT] - 1; $_REQUEST[RQ_INDENT] = $i; return str_repeat(" ", $i) . "<-". $s;}



    function info(string $note = "", string $data = ""): void {
        $vid = $_REQUEST[RQ_VID];
        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        //pre_dump($bt);
        //$bt = $bt[1];
        $callinfo = @$bt[1]['class'] . @$bt[1]['type']  . @$bt[1]['function'] . ':'. @$bt[0]['line'];

        $entry = (new DateTime("now", new DateTimeZone('America/Los_Angeles')))->format("Y-m-d H:i:s.v")
            . PIPE . $_REQUEST[RQ_REQID]
            . PIPE . $vid
            . PIPE . @$_SERVER['HTTP_CLIENT_ID'] // not trusted
            . PIPE . @$_SERVER['HTTP_X_FORWARDED_FOR'] // not trusted
            . PIPE . $_SERVER['REMOTE_ADDR'] // trusted but often a proxy (not that useful)
            . PIPE . $note
            . PIPE . $data
            . PIPE . $callinfo
            . PHP_EOL;

        file_put_contents(DATA_DIR . "log.psv", $entry, FILE_APPEND);

        if (isset($vid) && strlen($vid) > 0) {
            file_put_contents(DATA_DIR . "$vid-log.psv", $entry, FILE_APPEND);
        }
    }

    function nopipe($data): string {
        return $str_replace(PIPE, "`", $data);
    }

?>