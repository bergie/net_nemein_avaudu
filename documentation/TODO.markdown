Avaudu TODO
===========

Avaudu is a desktop client for [Qaiku](http://www.qaiku.com) and [Twitter](http://twitter.com) microblogging clients.

## UI

* Keep track of what posts user has seen and identify visually + update the unread counter in dock (track if user is idle)
* Growl new messages (or at least radar entries)
* Distinquish new messages and replies visually
* Quick reply mode (sets hidden `in_reply_to` field)
* Infinite page scrolling to reveal history
* Pretty-print message timestamps
* Proper ICNS application icon

## Features

* Radar view (mentions)
* Per-thread view (show all replies, enable replying)
* Per-user view with profile
* Picture messages (needs Qaiku API for it)
* Search (needs signalling support to initiate searches on the background)

## Back-end

* Fix Midgard [timestamp setting issues](http://trac.midgard-project.org/ticket/1176)
* Fix Midgard [64bit crashes](http://trac.midgard-project.org/ticket/1177)
* Move all files under the .app
* Refactor synchronization to happen as a background process (PHP-CLI or Python)
* Cache user avatars as Midgard attachments for offline support