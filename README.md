# manual-image-match
Manual Image Match is simple software to facilitate having volunteers match photos by hand.

The idea is to provide simple software that could be used by most any researcher, and to keep things very simple for volunteers.

The software will support a research who has 
1. Several sets of pictures (in separate folders); these are "source folders".
2. A single "target" folder, containing pictures that might match pictures in the "source folders".
3. A set of volunteers, each of which can be assigned to consider the pictures in one or more of the source folders. 

Each volunteer will be asked, for each picture in an assigned source folder, whether that picture is a match for each picture from the target folder. 

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
