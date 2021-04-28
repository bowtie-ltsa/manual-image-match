<?php 
    declare(strict_types=1);

    // represents the person who would hand out image pairs intelligently, if only a person could live inside a web server.
    class TheCoordinator {
        public static function GetOpportunity(string $vid, ?int $did, ?string $ipid): OpportunityResult {
            TheImagePairList::CreateIfNecessary();
            return new OpportunityResult(null, new Exception("keep implementing"));
        }
    }
?>