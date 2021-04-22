# manual-image-match
Manual Image Match is simple software to facilitate having volunteers match photos by hand.

The idea is to provide simple software that could be used by most any researcher, and to keep things very simple for volunteers.

The software will support a researcher who has a set of photos, some of which may "match" by some definition. We assume the number of image pairs (`n^2` for `n` images) will be large relative to the number of volunteers `v` and the number of hours available for each volunteer. That is, we adopt a strategy that increases the chances that all possible image pairs will be viewed at least once, by more or less pulling image pairs out of a hat for volunteers, until we run out of image pairs.

# Definitions

1. An image pair is `allocated` when it is assigned to a volunteer (to answer whether the two images match). That image pair will not be allocated to another volunteer until all unallocated image pairs have been first allocated. Image pairs are allocated randomly.
2. An image pair is `answered` by a particular volunteer when that volunteer has indicated whether the two images match, or not. The volunteer can navigate "bacK" to a previous image pair to change his or her answer, but a volunteer cannot "unanswer". Navigating back and forth to previously answered image pairs always retains the original order for that volunteer.
3. An image pair is `open` if it is an image pair that has been allocated to a volunteer but not yet answered by that volunteer.
4. A volunteer can have at most one `open` (unanswered) image pair.
5. A volunteer will never be asked to match an image pair more than once. The volunteer is `finished` when he or she has `answered` all image pairs exactly once. At that point, the volunteer can only review and modify previous answers, if desired. Alternatively, a volunteer might be limited to a specific number of image pairs, to prevent a small number of volunteers from doing most of the matches.
6. The study is `finished` when all volunteers have finished: each image pair will have been `answered` exactly v times, where `v` is the number of volunteers. We don't actually expect the study to even come close to `finishing`, which is why we have the guarantees below.
7. The study progresses in `rounds`. Each round consists of allocating all image pairs exactly once (taking the image pairs out of the hat), allocating them to volunteers as they need them. `Round k` is complete when all image pairs have been allocated exactly `k` times.  And, since volunteers cannot get a new allocation until they answer their current allocation, there can be at most `v-1` image pairs `open` at the start of a new round.  The study's goal may be simply to get to the end of `round 1` and then close the open image pairs. (That is, the study's goal may be simply to have every image pair matched exactly once by one or more volunteers.)

# Guarantee

We allocate image pairs in a way that ensures all image pairs are `allocated` once before any image pairs are `allocated` twice. (And so on.) Once an image pair is allocated to a volunteer, the volunteer must answer it before he or she can get a new allocation. However, we cannot guarantee that the volunteer will ever `answer` it. 

We can only guarantee, if we have `v` volunteers (v>0), that all image pairs will be answered once before more than `v/2` image pairs are answered two or more times (and all image pairs answered twice before more than `v/2` image pairs are answered three or more times, and so on), as long as `n^2` is larger than `v` (where `n` is the number of images).

  For example: for `n=100` and `v=15`, all `10,000` image pairs will be answered once before more than `7` image pairs are answered two or more times.

To handle volunteers that drop out, we prioritize their `open` image pairs at the start of the next `round` of allocations. That is, once all image pairs have been allocated [taken out of the hat] `k` times (k>0), and we start to allocate image pairs `k+1` times, we continually prioritize image pairs that seem to be abandoned -- those that have been answered fewer than `k` times). Because we prioritize "finishing `round k` (k>0) as soon as possible" (i.e. because we prioritize getting at least `k` answers for all image pairs after we start `round k+1`), we purposefully over-allocate volunteers to those "abandoned" image pairs, and _may_ end up "overshooting" the mark (that is, we may end up with _more_ than `k+1` answers for some of those previously-abandoned image pairs) -- but it is not likely and of small consequence.

# design decisions
- images cannot be compared easily on a phone, so don't worry about mobile/small screens.
- volunteers would have trouble installing software, so make it a web application.
- torn between a "static website" (git pages delivers html+javascript) vs. "active website" (server-side logic to display pages and process submitted data)
  - static website pros and cons:
    - (list discussed - write-up tbd)
  - active website pros and cons:
    - (list discussed - write-up tbd)
- decision: active website
  - active website scripting should be as simple as possible, so researcher can find hosting services most anywhere and possibly tinker with it.
  - php seems to offer a very simple approach

# active website - design decisions
- use text files only to keep everything simple (don't use a database)
- pictures hosted on and fetch from a separate github project
  - github project to consist of many folders of pictures
  - one folder is the "target" folder; it would contain many pictures
- website code will work for any capture matching project with that structure
- config files will be used for any customization in presentation (e.g. project name, image matching prompt, etc.)

# expected workflow for volunteers
1. user starts with url for host (e.g. http://capture-match.hoza.us)
2. user get prompted to enter volunteer name (no password, just name, to keep it simple)
   - list of volunteers is read from text file, e.g. accounts.txt
   - accounts.txt file would list one volunteer per line in format "name:folder-list-csv"
3. user is taken to first question (two images shown, with prompt)
   - prompt is read from text file e.g. prompt.txt, e.g. "Are these two pictures of the same salamander?"
4. user answers "same" or "different" and is then taken to next question
5. user can click nav buttons to go back and then forward

# ux
- all answers are recorded immediately (when user clicks to leave page - "next" or "back")
- all pages are bookmark-able / emailable
- as the user clicks next, next, next, the source picture remains the same, while the target picture changes, until all target pictures have been shown to the user.
- the _order_ of target pictures is different for each volunteer and for each source picture.
- however, the order does not change over time.


# local host using docker
- choco install docker-desktop
- configure docker to allow folder access to the php directory
- pull image
  ```
    docker image pull php:7.2-apache
  ```
- start container
  ```cmd
    docker run --rm -d -p 8123:80 --name manual-image-match -v C:\github\bowtie-ltsa\manual-image-match\php:/var/www/html php:7.2-apache
  ```
- browse to http://localhost:8123/

