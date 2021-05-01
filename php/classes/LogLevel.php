<?php 
    declare(strict_types=1);

    class LogLevel extends BasicEnum {
        public const Debug = 0;
        public const Mention = 1;
        public const Note = 2;
        public const Entry = 3;
        public const Event = 4;
        public const Concern = 5;
        public const Warning = 6;
        public const Panic = 7;
    }

?>