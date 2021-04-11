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

# active website design decisions
- use text files only to keep everything simple (don't use a database)
- pictures hosted on and fetch from a separate github project
  - github project to consist of many folders of pictures
  - one folder is the "target" folder; it would contain many pictures
- website code will work for any capture matching project with that structure
- config files will be used for any customization in presentation (e.g. project name, image matching prompt, etc.)
