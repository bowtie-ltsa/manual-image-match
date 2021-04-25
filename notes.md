folder-pair-F1-F2.json:
- defines a folder pair, has left folder, right folder, and filename, and an array of image pair allocations (shuffled)
- image pair allocation: left image, right image, vidlist

folder-pairs.json:
- shuffled list of folder pairs (e.g. F1-F2, or F3-F4)
- folder1id, folder2id, vidlist

current-round.txt:
- contains the number of the current round (first round = round 1?)
- when a volunteer shows up, volunteer is allocated the first folder pair from folder-pairs list where count(vidlist) <= $(current-round.txt)*
- when volunteer completes a folder pair, volunteer is allocated another folder pair where count(vidlist) <= $(current-round.txt)*
- * if there are no unallocated folder-pairs left for the current round, then the volunteer is allocated an unfinished folder** and helps to finish it. Specifically, an unfinished folder where count(vidlist) is minimized.
- ** if there are no unfinished folders, then current round is finished; current-round.txt is incremented and allocation logic starts over.

$vid-folder-pairs-allocated.json:
- a list of folder pairs allocated to $vid
- folder1id, folder2id

folder-results-F1-F2-$vid.json:
- has the results

FolderPairAllocation[vid, folder1id, folder2id]
FolderPair[folder1id, folder2id, imagePairAllocations array, imagePairResults array?] ?
ImagePairAllocation[left, right, vidlist] ?
ImagePairResult[left, right, vid, result] ?

class ImagePairResult { ??
    image1, image2, vid, result
}
