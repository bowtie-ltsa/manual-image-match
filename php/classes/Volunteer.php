<?php 
    declare(strict_types=1);
    require_once "functions/cast.php";

    class Volunteer {
        public $vid; // string, volunteer id, the name/loginid of the volunteer
        public $questions; //an array of ImagePairAllocationId values
        public $filename; // the name of the file where the volunteer object is stored
        public $foo;

        // public function __construct(string $vid, string $filename) {
        //     $this->filename = $filename;
        //     $this->vid = $vid;
        //     $this->questions = array();
        //     $f1 = new Folder("f1", "path1");
        //     $f2 = new Folder("f2", "path1");
        //     $f3 = new Folder("f3", "path1");
        //     $this->foo = array($f1,$f2,$f3);
        // }

        public function Save() {
            $json = json_encode($this);
            file_put_contents(DATA_DIR . $this->filename, $json);
        }
        
        public static function Load(string $vid): Volunteer { 
            $filename = "$vid.txt";
            $json = @file_get_contents(DATA_DIR . $filename);
            if (!$json) {
                return $v;
            }
            $o = json_decode($json);
            writeln("object is");
            pre_dump($o);

            $v = cast('Volunteer', $o);
            writeln("v is");
            pre_dump($v);
            return $v;

            // $filename = "$vid.txt";
            // $v = new Volunteer($vid, $filename);

            // $json = @file_get_contents(DATA_DIR . $filename);
            // if (!$json) {
            //     return $v;
            // }

            // $v->questions = json_decode($json);
            // return $v;
        }
    }
?>