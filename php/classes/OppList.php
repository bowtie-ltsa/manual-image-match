<?php 
    declare(strict_types=1);

    // for at least, we keep the static classes static, but try to keep the code DRY.
    // eventually we may take the time to create a generic, nonstatic OpportunityList....
    class OppList {

        public static function IsEmpty($filepath): bool {
            if (!file_exists($filepath)) {
                return true;
            }

            clearstatcache();
            if (filesize($filepath) == 0) {
                return true;
            }

            return false;
        }

    }
?>