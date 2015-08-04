MPD Cover
===
Music Player Daemon (MPD) is a moltimedia player that runs on a - possibly headless - system and can be controlled via a large variety of web interfaces, applications and smartphone apps. Even though it supports many fields of the ID3 tags, its protocol lacks an option to send cover images to the clients.

Smartphone apps that are derivates of dmix (such as MPDroid) solve this problem by fetching cover art from a separate HTTP server.
See here for more information: https://github.com/abarisain/dmix/wiki/Album-Art-on-your-LAN

This is a simple PHP script that reads artwork from MP3 files and serves them on a webserver just as dmix/MPDroid expect them.


External Libraries
-----
MPD Cover uses the PHP library getID3 to fetch the cover image from the file.
If you clone the GIT repository, execute the following commands to automatically get a current version o the library

    # git submodule init
    # git submodule update

If you use a packaged version, get the library from https://github.com/JamesHeinrich/getID3 and place it in the getID3 subfolder.

Server configuration
-----
In MPD Cover there is not much to configure.
Rename ``config.php.tpl`` to ``config.php`` and set the path to your music library. This must be the same as you used to configure your MPD.

I use nginx, but Apache, lighttpd or any other webserver should do as long as it supports PHP and is able to rewrite URLs.
You need a rewrite rule that calls ``covers.php?coverfile=???`` with the path the browser accesses.

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
* Check Load local album covers

If you followed my advice from above - That's it.