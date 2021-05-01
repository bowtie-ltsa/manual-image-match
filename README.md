# manual-image-match
The "Manual Image Match" project aims to provide a simple website to facilitate the process of having a small group of `volunteers` "match" a small set of `images`. (Tens of volunteers and hundreds of images. Nothing big.) The inspiration for building this simple web app was a `capture-recapture` study (aka "mark and capture") in which long-toed salamanders were not marked, but could be identified visually by spot patterns. Recapture identification through automated means was not feasible / error rates not acceptable.

The input to the project is a set of `image folders`; each image folder is thought to represent the captures (images) for a single day.

The goal of the project is to make `decisions` about every relevant `pair of images`. We eliminate `image pairs` from the same folder (from the same day), preferring to assume that recaptures do not take place within a single day (single folder). (Changing this rule would be a very minor code change. Using the rule reduces the volunteers' workload by about 10%.)

The `decision` to make for each image pair is binary: do the two images "match" or not. For a capture-recapture study, this question is more precisely phrased as "Is the same animal pictured in each of the two images?". Tweaking this project to make it a fuzzy decision, or a multi-valued decision, rather than a binary one, would be a small code change.

We stipulate that no volunteer can record multiple decisions for a given image pair. We allow them to go back and change their mind, but we record only a single decision for a given image pair from a given volunteer. Based on this requirement, we can say that the volunteer has `finished` the project if they manage to make a decision for all image pairs in the project. Likewise, the project itself is `finished` if all volunteers are `finished`.

We assume that the number of image pairs is large compared to the number of volunteers. (e.g. 100 images would yield roughly ~4500-5000 image pairs [depending on the number of captures in a folder]. For ~10 volunteers, that would be ~460-500 decisions per volunteer.) So there may be a concern about getting the project finished. This has implications for how an intelligent human coordinator would handle volunteers asking for `opportunities` to make decisions:
- The initial, basic goal of the project is to get a decision from at least one volunteer for every image pair in the project. We call this `round 1`.
- In general, the goal of round `k` is to have at least `k` decisions made for every image pair in the project.
- To achieve this goal, we are careful about handing out `opportunties` (opportunities to make decisions on image pairs) to volunteers; we prefer to hand out `new` opportunties, and careful to avoid giving the same opportunty to different volunteers, except near the end of the round.
- Toward the end of round `k`, when there are only a few image pairs left to be `decided`, we will end up with more volunteers than we have image pairs left in the round. That is okay. We choose to have those volunteers `swarm` the remaining image pairs (that is, we choose to have multiple volunteers given the same opportunity), because this helps ensure that the round will finish as quickly as possible (or at all).
- A consequence of `swarming` is that it is possible that a few image pairs will get some "extra" decisions (i.e., when round `k` finishes, all image pairs will have `k` decisions, and a few might have more than `k` decisions.) How many is "a few"? At the end of round 1, no more than `v/2` image pairs will have "extra" decisions. Fewer if there happen to be few volunteers active at the end of the round, or their timing is good. For later rounds, it gets harder to calculate and we haven't bothered since we assume that having "extra" decisions are not really a concern. For `k` far less than `N` (the number of image pairs), we would guess an upper bound of `k*(v/2)`, although this number can be reduced by omitting image pairs from the next round that have already met the goal of that round. Note that it is possible but perhaps unlikely that the very last image pair in a round could get many decisions at the last minute (up to `v` of them).
- We also feel that an intelligent coordinator would never tell a willing volunteer to wait for the next round; in other words: it is better to have the volunteer swarm, or even start the next round if necessary, than to turn them away and risk having them never come back.
  
# Volunter options
We anticipate navigation options that allow the volunteer to:
- Request an image pair decision opportunity.
- Review a previous decision, and possibly change their decision.
- Navigate forward and backward in their personal decision history (previous, next, start, end, -10, +10).

# Volunteer restrictions
- Volunteer cannot generally get a new decision opportunity until they make a decision on the opportunity they were previously given.
- In other words, we make them stick to whatever random image pair they drew out of the hat. They can't skip hard ones by hitting "refresh".
- An exception occurs when multiple volunteers are "swarming" on the same image pair near the end of a round; in this case, if any volunteer makes a decision, then a "page refresh" (or leaving the website and returning later) _will_ draw a new opportunity for the other volunteers.
- A volunteer cannot record multiple decisions for the same image pair. (But they _can_ review and change their decision.)

# Definitions / concepts for The Coordinator's Procedure (below)
- **Image Pair**: The subject of the project. The thing for which a `decision` is wanted.
- **Opportunity**: An opportunity is a chance to make a `decision` on an `image pair`. It is not the decision itself.
- **Decision**: A `volunteer's` opinion on whether the two images in a given `image pair` match, or not.
- **Volunteer**: A person who is makes `decisions`.
- **The Image Pair List**: The one and only list of image pairs for the study. This list never changes, and is generated before `round 1` begins.
- **Round**: The goal of round `k` (`k` > 0) is to have at least `k` decisions made for each `image pair`.
- **The Opportunity List**: The list of available `opportunities` for the current round. There is only one `opportunity list` at any given moment in time. A round ends when the `opportunity list` is empty, **and** the `opportunity board` is empty. A new `opportuntity list` is created when a round begins. It is created by copying and shuffling `The Image Pair List`.
- **The Opportunity Board**: When a volunteer is given an `Opportunity`, it is removed from `The Opportunity List` (so it is not given to other volunteers), and added to `The Opportunity Board` (so it can be tracked). 
  - Near the end of a round (i.e. when `the opportunity list` is empty), several volunteers can "swarm" on the last remaining opportunities (i.e. several volunteers can get assigned to the same opportunity from `the opportunity board`).
  - The opportunity stays on `the opportunity board` until someone makes a decision; it is removed as soon as any volunteer assigned to that opportunity makes a decision for that opportunity's image pair. Usually, except when swarming near the end of a round, there is only one volunteer assigned, and it remains until that one volunteer has made a decision.
  - When the last item is removed from TheOpportunityBoard, and TheOpportunityList is empty, a new round is about to begin.
- **Decision List**: The list of `decisions` made by a given volunteer. Each volunteer has their own `decision list`. This list starts out empty grows over the course of the project.
- **Bucket List**: The list of opportunities (image pairs) not yet decided by a given volunteer. This list starts out as a shuffled copied of `the image pair list`, and shrinks over the course of the project. When a volunteer has done everything on their `bucket list`, they are done with the project; they cannot make any more decisions. (But they can still review past decisions.)
- **Bucket Board**: A BucketBoard is a "board" that can only have up to one opportunity on it. Each volunteer has their own `bucket board`.
  - The one opportunity on the `bucket board` (if any) is an opportunity that the volunteer has been given from their `BucketList`,
  - in the special circumstance where they cannot be given an opportunity from `The Opportunity List`, nor from `The Opportunity Board`,
  - because they've already provided a decision for all the opportunties left on those lists,
  - and yet their `Bucket List` is not empty (i.e., they still have decisions they could add to their `Decision List`).
  - So we give them an opportunity from their `Bucket List`, and use their `Bucket Board` to keep track of it. 
  - This will add a decision for that opportunity's image pair beyond the goal of the current round, but that's better than telling the volunteer to wait for the round to end (which might be a while).
  - The volunteer is essentially helping to get a head start on a future round.


# The Coordinator's procedure (using the Definitions above)
The following procedure is used by the website software whenever a volunteer shows up, to provide the volunteer with an `opportunity` (including the "opportunity" to view and possibly change a past `decision`).

1. If `The Image Pair List` is empty, create it.

2. If `The Opportunity List` is empty, and `The Opportunity Board` is empty, start a new round:
   1. Make a copy of `The Image Pair List`
   2. Shuffle it.
   3. Save it as `The Opportunity List`.
   4. Clear (empty) any and all `bucket boards` for all volunteers.

3. If the volunteer requested to view a past `decision`, provide that decision (and stop here).
   - We ignore requests for non-existent decisions, as might occur through URL hacking.

4. If the volunteer's `bucket list` is empty, redirect the volunteer to a "you are finished" page.

5. If the volunteer has an opportunity on `The Opportunity Board`, provide that opportunity (and stop here).

6. If the volunteer has an opportunity on his or her `bucket board`, provide that opportunity (and stop here).

7. Pull a valid opportunity more or less at random from `The Opportunity List`, if possible.
   - It has to be valid for the volunteer (on the volunteer's `bucket list`).
   - `The Opportunity List` might be empty.
   - It might not be entirely random: we might provide some continuity for the volunteer, by choosing a new opportunity that holds one of the images constant, until and unless the volunteer has already had more than their "fair share" of image pairs containing that image.... Or it could just be random.
   - If a valid opportunity is found: 
     1. Remove it from `The Opportunity List`;
     2. Add it to the `The Opportunity Board`;
     3. Provide that opportunity (and stop here).

8. Check to see if there is a `swarming` opportunity:
   - Consider opportunities on `The Opportunity Board`.
   - The opportunity must be on the volunteer's `bucket list` (valid for the volunteer).
   - Among those that are valid for the volunteer, consider only those with the least number of volunteers already assigned. (We want to spread the volunteers out over more image pairs, when swarming is happening, e.g. 2 volunteers each, on each of 4 remaining image pairs, not 5 volunteers focused on the same image pair, with 1 each on the remaining 3 image pairs.)
   - Among those, pick one at random or optionally try provide continuity for the volunteer.
   - If a valid swarming opportunity is present on `The Opportunity Board`:
   1. Add the volunteer to that opportunity;
   2. Provide that opportunity (and stop here).

9. If there is anything at all on the volunteer's `bucket list` (there should be, cf step #4 above):
   1. Add that opportunity to the volunteer's `bucket board` (which is empty, cf step #6 above).
   2. Provide that opportunity (and stop here).

10. There is no step 10. It should not be possible to reach this point. :)

# software / ux design decisions
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
- pictures hosted on the website to keep things simple. Code could be changed to query a github project etc.
- config files will be used for any customization in presentation (e.g. project name, image matching prompt, etc.)

# expected workflow for volunteers
1. user starts with url for host (e.g. http://capture-match.hoza.us)
2. user gets prompted to enter volunteer name (no password, just name, to keep it simple)
   - list of volunteers is read from text file, e.g. accounts.csv.
   - accounts.csv file would list one volunteer per line.
3. user is taken to their first opportunity to make a decision. (i.e. two images are shown, with a prompt to decide.)
   - prompt is read from text file e.g. prompt.txt, e.g. "Are these two pictures of the same salamander?"
4. user answers "same" or "different" and is then taken to next opportunity.
5. user can click nav buttons to go back and then forward.
6. When a user is given an opportunity, it is generally "sticky" (except when several volunteers are swarming on the same image pair, near the end of a round). "Sticky" means that they must make a decision before being given a new opportunity. It does not change even if they hit "refresh", edit the URL, leave the site to take a break, etc.

# ux
- each decision is recorded as soon as possible (user clicks "same" or "different" and then clicks a button to save that decision.)
- all pages are bookmark-able / emailable
- as the user clicks next, next, next, it would be very helpful to the volunteer (and therefore to the project) if one of the images remained constant for as long as possible/reasonable. (For example, if there are ~100 image pairs that contain "image 1", and we want to spread them evenly across ~5 volunteers, then ideally the ~20 image pairs for a given volunteer would be shown consecutively. Of course this is not possible for the all image pairs for all volunteers.)
- the _order_ of image pairs should be random (different for differnt volunteers), to avoid making any bias from fatigue systemic; it seems like this concern would be higher for any sequence of image pairs that hold one image constant.
- however, the volunteer must be able to revisit past decisions in the order they were made; they must be able to decide, for example, to "go back ~15 decisions (image pairs)" to review one that they remember. In other words, opportunities to make decisions (about image pairs) should be _handed out_ in random order (pulled out of hat), but decisions must be remembered and shown in historical order. (The volunteer's `decision list` facilitates this.)

# local host using docker
- https://chocolatey.org/install
- `choco install docker-desktop`
- note: use the docker User Interface to configure docker to allow folder access to the php directory!
- `docker image pull php:7.2-apache`
- `docker run --rm -d -p 8123:80 --name manual-image-match -v C:\github\bowtie-ltsa\manual-image-match\php:/var/www/html php:7.2-apache`
- browse to http://localhost:8123/

