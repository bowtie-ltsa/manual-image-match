<?php 
    declare(strict_types=1);
    require_once "functions/cast.php";

    class Volunteer {
        public $vid; // string, volunteer id, the name/loginid of the volunteer
        public $filename; // the name of the file where the volunteer object is stored
        public $questions; //an array of ImagePairAllocationId values; array is zero-based but q parameter is 1-based

        // returns an ImagePairAllocation object for the requested question # `q`, if possible.
        // q is 1-based, so `q-1` is the index we want.
        // if q refers to a past question (already answered), then we just use that without any thought (easy).
        // if q refers to the volunteer's last question (aka current question), or q is null or beyond the array, then we 
        // want to provide a question that the volunteer has not yet answered, and which has been answered fewer than `k` 
        // times (where `k` is the current round, starting with round 1). This is normally just the current question, but 
        // it might not be: the array could be empty (there is no current question), or the volunteer's current question 
        // might have already been answered `k` times (by other volunteers). (The latter situation can only happen if multiple 
        // volunteers are "swarming" on an image pair, near the end of a round; in this situation, since the volunteer is 
        // _asking_ us for their current question, we may change their current question to different one. This is the only 
        // situation where we will change their current question; normally they must answer the question once the image pair
        // has been allocated.)
        public function GetImagePairAllocation(?int $q): IpaResult {
            $count = count($this->questions);
            if ($q != null && $q > 0 and $q < $count) {
                $ipaId = $this->questions[$q-1];
                $result = IpaManager::IPA($ipaId);
                return $result;
            }

            if ($q === $count) {
                // todo: check if question has already been answered `k` times
                return new IpaResult(null, new Exception("todo: check if question has already been answered `k` times."));
            }
            
            // todo: request new allocation (possibly causing a new round to start)
            return new IpaResult(null, new Exception("todo: request new allocation, possibly causing a new round to start"));
        }

        public function Save() {
            $json = json_encode($this, JSON_FMT);
            file_put_contents(DATA_DIR . $this->filename, $json);
        }
        
        public static function Load(string $vid): Volunteer { 
            $filename = "$vid.txt";
            $json = @file_get_contents(DATA_DIR . $filename);
            if (!$json) { return Volunteer::New($vid, $filename); }
            $v = Volunteer::FromJson($json);
            return $v;
        }

        public static function FromJson(string $json): Volunteer {
            $o = json_decode($json);
            $v = Volunteer::Cast($o);
            return $v;
        }

        public static function Cast(stdClass $o): Volunteer {
            $v = cast($o, new Volunteer());
            return $v;
        }

        public static function New(string $vid, string $filename): Volunteer {
            $v = new Volunteer();
            $v->vid = $vid;
            $v->filename = $filename;
            $v->questions = array();
            return $v;
        }

  }
?>