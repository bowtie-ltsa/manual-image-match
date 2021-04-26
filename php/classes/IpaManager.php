<?php 
    declare(strict_types=1);

    class IpaNotFoundException extends Exception {}
    class AllocationsNotLoadedException extends Exception {}

    // Image Pair Allocation Manager
    class IpaManager {
        const FILENAME = "ipa-manager.txt";
        public static $allocations = null; // array of ImagePairAllocation objects, indexed by ipaId (simple numeric index)

        public static function AddNewImagePairAllocation(ImagePairAllocation $ipa): Exception {
            if (IpaManager::$allocations == null) {
                return new AllocationsNotLoadedException();
            }
            $allocations[] = $ipa;
            return null;
        }

        public static function Save(): Exception {
            if (IpaManager::$allocations == null) {
                return new AllocationsNotLoadedException();
            }
            $mu = new Mutex("ipaManager");
            if (!$mu->Lock()) {
                return new BusyException();
            }
            try {
                file_put_contents(DATA_DIR . IpaManager::FILENAME, json_encode(IpaManager::$allocations, JSON_FMT))
            }
            finally {
                $mu->Unlock();
            }
        }

        // returns the requested ImagePairAllocation object
        public static function IPA(int $ipaId): IpaResult {
            if ($allocations == null) {
                $err = IpaManager::LoadAllocations();
                if ($err != nil) { return new IpaResult(null, $err); }
            }
            $ipa = IpaManager::$allocations[$ipaId];
            if (!$ipa) {
                return new IpaResult($null, new IpaNotFoundException());
            }
            return new IpaResult($ipa, null);
        }

        public static function LoadAllocations(): Exception {
            $mu = new Mutex("ipaManager");
            if (!$mu->Lock()) {
                return new BusyException();
            }
            try {
                $filename = IpaManager::FILENAME;
                $json = @file_get_contents(DATA_DIR . $filename);
                if (!$json) {
                    IpaManager::$allocations = array();
                    return null;
                }
                $o = json_decode($json);
                IpaManager::$allocations = casteach($o, new ImagePairAllocation());
                return null;
            }
            finally {
                $mu->Unlock();
            }
        }

    }
?>