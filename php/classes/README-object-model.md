Folder 
- folderId (string in the format "Fn")
- relativePath
- images (an array of imageIds)
- FullPath()

FolderManager
- folders (array of Folder objects, indexed by folderId)
- Load()
- Save()
- AddFolder()

FolderPair
- folderPairId (string in the format "Fn-Fm" with n < m)
- f1 (id of folder 1 "Fn")
- f2 (id of folder 2 "Fm")

FolderPairManager
- folderPairs (array of FolderPair objects, indexed by folderPairId)
- Load()
- Save()
- AddFolderPair()

Image
- imageId (string in the format "FnCx")
- folderId
- name

ImageManager
- images (array of Image objects, indexed by imageId)
- Load()
- Save()
- AddImage()

ImagePair
- imagePairId (string in the format "FnCx-FmCy" with n != m)
- i1 (id of image 1 "FnCx")
- i2 (id of image 2 "FmCy")
- answers (an array of AnswerIds) (the # answers for almost all image pairs is <= k, where `k` is the current round; the # answers for a few image pairs _can_ end up being greater than `k`, due to having volunteers "swarm" on the last few image pairs toward the end of each round, to ensure the round finishes.)
- allocations (an array of ImagePairAllocationIds) (the # answers is always <= # allocations)
  - ImagePairs are allocated to volunteers as needed but not before

ImagePairManager
- imagePairs (array of ImagePair objects, indexed by imagePairId)
- Load()
- Save()
- AddImagePair()
- AllocateImagePair(Volunteer v)
  - ImagePairs are allocated to volunteers as needed. An image pair allocated must be answered `k` times before the volunteer can get another image pair allocated.
  - We will never allocate an image pair to a volunteer more than once; if the volunteer has had all image pairs allocated, then we should exit with `VolunteerFinished` signal.
  - If there is at least one image pair with # allocations < `k`, then we will allocate one of them. We will prefer an image pair from among these that provides continuity for the volunteer (one image from new pair matches an image from the previous pair), unless the volunteer has already been allocated his or her "fair share" of image pairs containing that image.
  - If there are no image pairs with # allocations < `k`, then we are in the "swarming" phase at the end of the round:
    - We consider those images with # answers < `k`. There can be no more than `v-1` of these (where `v` is the # volunteers). If there are none, then round `k` is over; this method should either call NewRound() directly or return a signal so caller can do that; either way this logic then starts from the top....
    - We are willing to allocate multiple volunteers to one of these image pairs because the goal of round `k` is to get all image pairs answered `k` times.
    - ImagePairs with fewer allocations are prioritized over those with more allocations. (That is, we spread the swarming effect evenly.)
    - Among those with the least # allocations, ImagePairs with fewer answers are prioritized over those with more answers.

ImagePairAllocation
- ImagePairAllocationId (zero-based)
- imagePairId (id of ImagePair)
- vid (volunteer id)
- isMatch (0 for no, 1 for yes, null for not yet answered)

AllocationManager
- allocations (an array of ImagePairAllocation objects, indexed by allocationId)

RoundManager
- k (the current round, starting at round 1; if k is zero then initialize the project via first call to NextRound()) 
- ImagePairs (an array of imagePairIds, in a random order, indexed by position for that image pair in the current round)
- Load()
- Save()
- NextRound() starts a new round; when starting the first round, the project is initialized (image folders scanned, image pairs created, etc)
  - The goal of round `k` is to get every image pair answered `k` times.
  - Given `v` volunteers, when there are only `v` image pairs left in the round, then each volunteer will be assigned one of these last image pairs.
  - When one of those image pairs is answered, the round enters a "swarming" phase, where there are few image pairs left than volunteers; in this phase, more than one volunteer is assigned to an image pair. We do this to reach the goal of the round as soon as possible. We continue to "swarm" the remaining images until all images have been answered at least `k` times. (The swarming admits the possibility that some of these images end up being answered more than `k` times, but that is preferred over the alternative.)

Volunteer
- id (string)
- filename (string) (relative to DATA_DIR)
- questions (an array of ImagePairAllocationIds)
- Save()
- ::Load(string $vid): Volunteer
