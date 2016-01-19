MPDart
===
Music Player Daemon (MPD, http://www.musicpd.org/) is a multimedia player that runs on a - possibly headless - system and can be controlled via a large variety of web interfaces, applications and smartphone apps. Even though it supports many fields of the ID3 tags, its protocol lacks an option to send album cover art to the clients.

Smartphone apps that are derivates of dmix (such as MPDroid) solve this problem by fetching cover art from a separate HTTP server.
See here for more information: https://github.com/abarisain/dmix/wiki/Album-Art-on-your-LAN

This is a simple PHP script that reads artwork from MP3 files and serves them on a webserver just as dmix/MPDroid expect them.

Getting the software
-----
Either clone from GIT:

    # git clone https://github.com/tjaehnel/MPDart.git
    
Or get the current HEAD source package from https://github.com/tjaehnel/MPDart/archive/master.zip

External Libraries
-----
MPDart uses the PHP library getID3 to fetch the cover image from the file.
If you clone the GIT repository, execute the following commands to automatically get a current version of the library

    # git submodule init
    # git submodule update

If you use a packaged version, get the library from https://github.com/JamesHeinrich/getID3 and place it in the getID3 subfolder.

Server configuration
-----
In MPD Cover there is not much to configure.
Rename ``config.php.tpl`` to ``config.php`` and set the path to your music library. This must be the same as you use in your MPD configuration.

As webserver I use nginx, but Apache, lighttpd or any other should do as long as it supports PHP and is able to rewrite URLs.
You need a rewrite rule that calls ``covers.php?coverfile=???`` where ??? is the path the browser accesses.

This means ``/myartist/myalbum/cover.jpg`` must be rewritten to ``/covers.php?coverfile=/myartist/myalbum/cover.jpg``

The following nginx configuration file does the trick

    server {
       listen   80;
       server_name my.server;
       charset utf-8;
       
       root /webroot/of/mpdcover;
       index index.php;
       
       location / {
          rewrite ^/(.*)$ /covers.php?coverfile=$1 last;
       }
       
       # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
       #
       location ~ \.php$ {
   	      fastcgi_split_path_info ^(.+\.php)(/.+)$;
   	      # With php5-fpm:
   	      fastcgi_pass unix:/var/run/php5-fpm.sock;
   	      fastcgi_index index.php;
   	      include fastcgi_params;
       }         
    }

I recommend running the Webserver on the same machine as MPD and put mpdcover in the webroot.
That allows you to configure dmix/MPDroid by simply checking one box.

dmix/MPDroid configuration
----
* Start App
* Navigate to Settings->Album Cover Settings
* Check *Load local album covers*

If you followed my advice from above - That's it.

Notice
----
Only use this software in closed, secured environments. It is subject to <whatever>-injection, since there is virtually no input validation at all.

Generally, use this software at your own risk, it comes with no warranty at all.

License
----
MPD Cover is released under the terms of the Lesser Beerware License.

"THE LESSER BEER-WARE LICENSE" (Revision 1):
<tjaehnel@gmail.com> wrote this file. As long as you retain this notice you
can do whatever you want with this stuff. If we meet some day, and you think
this stuff is worth it, you could buy me a beer in return.
But since I do not drink beer at all, don't waste your money.

Reference Beerware License: http://people.freebsd.org/~phk/

