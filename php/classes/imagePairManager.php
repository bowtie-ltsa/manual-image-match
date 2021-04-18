<?php
    // class ImagePairManager

    // definitions and rules:
    // 1. An image pair is _allocated_ when it is assigned to a volunteer, to answer whether the two images match.
    // 2. An image pair is _answered_ by a particular volunteer when that volunteer has indicated whether the two images match or not.
    // 3. An image pair is _open_ if it is an image pair that has been allocated to a volunteer but not yet answered by that volunteer.
    // 4. A volunteer can have at most one _open_ image pair.
    // 5. A volunteer will never be asked to match an image pair more than once (but they can go "back" to change their mind). 
    //    The volunteer is finished when he or she has _answered_ all image pairs (once).
    // 6. The study is finished when all volunteers have finished: each image pair will have been _answered_ v times, where v is the
    //    number of volunteers.
    // 7. We don't expect the study to finish, so we allocate image pairs in a way that helps ensure that all image pairs will be 
    //    _answered_ once before any image pair is _answered_ twice (and in general all image pairs will be answered n times (n>0) 
    //    before any image pair is answered n+1 times). The actual guarantee is slightly weaker but still very strong: 
    //    - If there are any image pairs answered twice, then there are at most v/2 image pairs answered zero times
    //    (where v is the number of volunteers). More generally:
    //    - If there are any image pairs answered n times (n>1), then there are at most v/2 image pairs answered n-2 times.
    //    We choose not to make the stronger guarantee because we choose to have a simpler allocation method, one without deadlines;
    //    this admits the possibility, during the transition to n+1 allocations, that two volunteers will answer increase the answer
    //    count for the same image twice (from n-1 to n+1 answers), before either volunteer increases the image count for a different 
    //    image (leaving that image at n-1 answers for a period of time).
        
    // AllocatePair() allocates an image pair to a volunteer; it returns null if the volunteer has already matched all image pairs,
    // --
    // or if the volunteer has not yet recorded an answer for their current allocated image pair. (That is, a volunteer is only ever
    // allocated at most one unanswered image pair, and there are only ever, at most, len(accounts.txt) unanswered image pairs.)
    // Prevent parallel execution.
    // if the file "all-pairs.txt" does not exist, invoke preparePassZero().
    // if the file "$vid-next.txt" does not exist, write "0".
    // if len($vid-results.txt) >= len(all-pairs.txt), return null: volunteer has matched every pair and has no more pairs to match.
    // starting at $($vid-next.txt), find the first image pair that has been allocated $(current-pass.txt) times.
    // if none, invoke prepareNextPass() and find the first image pair that has been allocated $(current-pass.txt) times.
    // if none (shouldn't happen!) return null.
    // update $vid-next.txt to $selectedIndex+1
    // return image pair.

    // preparePass0() creates a random list of all pairs of images, unallocated to any volunteers.
    // --
    // if the file "all-pairs.txt" exists then panic.
    // create all-pairs array in memory, and shuffle it.
    // write array to "all-pairs.txt".
    // write "0" to "current-pass.txt".
    // at this point all image pairs are listed in a random order and none have been allocated.

    // prepareNextPass() shuffles the file "all-pairs.txt" and increments "current-pass.txt".
    // This method is invoked when a volunteer needs a new image pair to be allocated to him or her, but all
    // image pairs have already been allocated $(current-pass.txt) times. We must now prepare the _next_ pass because, 
    // for pass n (starting at zero), we will only allocate images that have been allocated n times.
    // 
    // Note: There will generally be a few image pairs that have been allocated $(current-pass.txt) times, but not yet 
    // _answered_ $(current-pass.txt) times:
    // - There can be at most $v of these, where $v is the number of unique volunteers that have started doing image matches.
    // - These _open_ image pairs are top priority for the next pass (since answering them will in reality complete the previous 
    //   pass), so they will be moved to the front of the file.
    // - There would typically be $v-1 of them; only after one or more volunteers actually answers *all* image pairs would
    //   there be fewer than $v-1 of them. 
    // - There is always at least 1 of them.
    // --
    // if file all-pairs.txt does not exist panic
    // read "all-pairs.txt", place into map[pair]allocations
    // read last line of every $vid-pairs.txt; most (but definitely not all) will be for an open image pair
    // foreach openpair, remove from map, add to start of newpass; shuffle newpass; write contents to "all-pairs.txt"
    // shuffle remaining map; append contents to "all-pairs.txt"
?>