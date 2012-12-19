foobar2000 Remote Music Player
Copyright 2007, Robert M Rogers


Description
-----------
A web-based PHP5/MySQL application that plays music and handles playlists
remotely using foobar2000


Requirements  (built and tested on listed versions)
------------
Windows 2000, XP, Vista

foobar2000 0.9.3+
http://www.foobar2000.org/

AMIP 2.61+ for foobar2000
http://amip.tools-for.net/wiki/amip/download

AMIP Configurator (Optional but helpful)
http://amip.tools-for.net/wiki/amip/download (bottom of page)

PHP5
http://php.net

MySQL5
http://mysql.com


Instructions
------------
Once you install foobar2000, the AMIP plugin and the AMIP configurator:

- Open AMIP configurator (run as admin in Vista)

- Nav to Other Integrations -> File/E-Mail

- Enter a filename of "amip.txt" in the File field, located wherever is most
  convenient  (ie "c:\foobar2000\amip.txt")

- Click the "Enabled" checkbox

- Click the "Update every second" checkbox

- Click "OK"

- Go to the location of your foobar2000 installation's component folder,
  ie c:\foobar2000\components

- Open the file plugin.ini

- Locate the following four variables and replace each with the following:

CFG_SPLAY="playing^lb%1^lb%2^lb%3^lb%4^lb%sl^lb%sle^lb%prc^lb%vol"
CFG_SSTOP="stopped^lb%1^lb%2^lb%3^lb%4^lb%sl^lb%sle^lb%prc^lb%vol"
CFG_SPAUSE="paused^lb%1^lb%2^lb%3^lb%4^lb%sl^lb%sle^lb%prc^lb%vol"
CFG_SEXIT="offline"

(They should be located around lines 5, 29, 75, 114 respectively)

- Open controller.php and edit the top few public variables as needed.

- Point the docroot of Apache to the base root of the app

- Start web server, load page and pray


Notes
-----
* If you need to shut down foobar2000, you will most likely have to do so
  through the Task Manager. If you're using Vista, you must "Show processes
  from all users" within task manager.

* For some reason I've yet to figure out, you may have to hit play/stop through
  the web interface a few times whenever foobar2000 hasn't been loaded yet.