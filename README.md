# phpEasyVCS
phpEasyVCS is a simple version control system (VCS) and WebDAV server running with PHP

Original project webpage: http://phpeasyvcs.sourceforge.net/

---

*The current fork states v1.3. I have incountered a few bugs with v2.0, it's why I decided to start a fork from there, and not from the latest version available. Of course, on the long run, I would like to continue to improve and develop around this, and continue to update the project, started in .*


## Introduction

phpEasyVCS is a simple version control system (VCS) and WebDAV server with minimal hosting requirements:

* PHP 5.2+

No database is needed.

Files can be viewed and uploaded with a browser or by WebDAV.

## Installation

Copy phpEasyVCS to a directory on your server, e.g. `easyvcs`.

Browse to `http(s)://your.server/easyvcs-dir/index.php` (replace `your.server` and `easyvcs-dir` with your server and installation directory) and fill out the settings form.

You can also create additional users on the Users tab.

Depending on your server settings, the authorization header might not reach PHP. In case of Apache you can add the following rewrite rule to your `.htaccess`:

```RewriteEngine on
RewriteBase /
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
```

You can also create additional repositories by 
* creating a new directory in the data folder (on the same level as the default folder) 
* editing the user(s) to give them access to the additional repository - you have to reenter the password for this

## Usage

Independent of the access type the user has to identify himself with his user name and password. If the user has access to multiple repositories, he must prefix the user name with the repository name and a backslash, e.g. "specialrepository\max", otherwise it is not sure, into which repository he will be logged in.

### Web Browser

Browse to `http(s)://your.server/easyvcs-dir/index.php` (replace `your.server` and `easyvcs-dir` with your server and installation directory).

You can browse the VCS, create tags to browse the VCS at a specific date, view versions, revert a file to a version, etc.

The web access will work

* with Javascript switched off (no syntax highlighting, page reload on many actions),
* with Javascript activated,
* with Javascript and Java plugin activated and the Java applet loaded you can upload whole directories and get progress bars during upload.

To enable Java and experience the least warnings possible:

* Download and install Java from java.com,
* in your favorite browser (which should support the Java plugin), go to the plugins page (e.g. in Firefox Tools/Add-ons/Plugins) and make sure that the Java plugin will either Always Activate or Ask to Activate,
* open the Java Plugin Control Panel (Windows: in Start/Programs, Linux Gnome: search for "java" in your Gnome menu) and set Security/Security Level to medium and add your phpEasyVCS web site URL to the website exceptions.
Using the navigation item Profile the user can change his password and switch the repository, if he has access to multiple repositories.

### WebDAV

Use `http(s)://your.server/easyvcs-dir/webdav.php` for WebDAV access. The actual syntax may vary depending on your operating system and WebDAV program.

The root level of the WebDAV drive shows at least the directory current, which represents the currently saved files. You will also see the tags created in the web interface, which represent read-only views of your VCS at a specific time. Additionally you can view the VCS at a specific point in time by manually specifying a date and time in the format `YYYY-MM-DD` or `YYYY-MM-DDTHH:MM`, e.g. `http://your.server/easyvcs-dir/webdav.php/2011-01-01` or `http://your.server/easyvcs-dir/webdav.php/2011-01-01T16:00` (this might not work with your WebDAV client).

#### Linux

Enter the following URL in Nautilus or Caja: dav://your.server/easyvcs-dir/webdav.php

If this does not work, connect explicitely by use of its menu File/Connect to Server and set Server to your.server, type to WebDAV (HTTP) or Secure WebDAV (HTTPS), path to easyvcs-dir/webdav.php and enter your user name and password.

You can also connect using Gnome Commander: set type to WebDAV, server to your.server and remote directory to /easyvcs-dir/webdav.php.

Or install davfs2 and mount the WebDAV, e.g.:

```sudo apt-get install davfs2
sudo mkdir /media/easyvcs
sudo mount -t davfs http://your.server/easyvcs-dir/webdav.php /media/easyvcs
```
You probably need to add options like `-o rw,user,uid=myusername` to be able to write, too.

#### Windows XP

Preparation:

Download and install KB907306 for web folders
To use basic authentication, set the `DWORD` registry entry `UseBasicAuth` in `HKEY_LOCAL_MACHINE\SYSTEM\CurrentControlSet\Services\WebClient\Parameters` to `1` and restart Windows.
Goto explorer - Tools - Map Network Drive - Connect to a Web site and enter `http://your.server/easyvcs-dir/webdav.php` as URL

Or goto explorer - Tools - Map Network Drive and directly add `http://your.server/easyvcs-dir/webdav.php` as folder (this only seems to work if your phpEasyVCS installation requires no authentication)

#### Windows Vista/Windows 7

Preparation:

Go to Settings in your phpEasyVCS instance and make sure that authentication method is Digest.
Or, if you really want to use Basic authentication, follow the steps in KB841215: Set the `DWORD` registry entry `BasicAuthLevel` in `HKEY_LOCAL_MACHINE\SYSTEM\CurrentControlSet\Services\WebClient\Parameters` to `2` and restart Windows.
Goto explorer - Map Network Drive - Connect to a Web site and enter `http://your.server/easyvcs-dir/webdav.php` as URL.

#### Alternatives for Windows XP/Windows Vista/Windows 7

TotalCommander has a WebDAV plugin.

BitKinex - All-in-one FTP/SFTP/HTTP/WebDAV Client (Freeware)

When setting up the connection you need to specify first the server and then set `easyvcs-dir/webdav.php` as default directory.
NetDrive (free for home use): You can assign a drive letter to the WebDAV drive and use it like a local drive.

## Contributing

1. Fork it!
2. Create your feature branch: `git checkout -b my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin my-new-feature`
5. Submit a pull request :D

## History

Initiated in 2011.

#### 2.0
- synchronization page to merge and synchronize a remote directory with a local directory
  - uses Mergely to merge local/remote files
  - uses CodeMirror, syntax highlighting included for c, c++, c#, java, groovy, html, css, less, sass, scss, javascript, php, python, properties files, sql, xml
- REST interface improved (stores user name, comment for DELETE and PUT as parameter)

#### 1.3
- better WebDAV compliance (tested with litmus test suite - http://www.webdav.org/neon/litmus/), now only the following incompatibilities remain:
  - overwriting (PUT, MKCOL, MOVE, COPY) collections (folders) with non-collections and vice versa is conceptually not possible
  - setting properties (PROPPATCH) is not supported and will always return 403 (Forbidden)
- because of the better WebDAV compliance the Teamdrive client (http://www.teamdrive.com) now works with phpEasyVCS
- basic authentication bug corrected

#### 1.2
- better compatibility with Windows (strptime function)
- upload in Chrome corrected
- added setting for site title
- added profile page for users to allow them to change password and timezone and switch repositories
- (Ticket #10: correct basic authentication in trunk)
- (Ticket #12: compatibility with PHP 5.1)

#### 1.1
- Ticket #6: users.php corrected to start with <?php instead of <?
- Ticket #7: added csv file type
- avoid PHP warning messages (e.g. Ticket #8)
- dialogs will now open on top in newer versions of Chrome, too
- simple file inputs for upload will only be hidden after applet is successfully loaded

#### 1.0
- new user management
- support for read only access
- multiple improvements for the web interface:
  - download multiple files and directories with the Java applet
  - upload zips extracting them on the server without Java applet
  - download multiple files and directories as zip without Java applet
  - delete, copy and move multiple files on the server
  - pure CSS collapsible directory tree for copy/move
  - easily undelete files and directories
- other small fixes and enhancements

#### 0.9.10
- corrects an problem with Windows' directory separator (backslash)
  (if you have files with backslashes on your non-Windows server, you can delete them with cleanup.php)

#### 0.9.9
- action menu instead of (rather random) links
- upload up to 5 files at once
- upload multiple files and even directories with a Java applet

#### 0.9.8
- allow copy/move of any file/directory version
- (re)create directory on add file, revert file and add directory, if necessary

#### 0.9.7
- corrected difference view

#### 0.9.6
- corrected difference view
- show MD5 in file name tool tips 
- support open ranges with GET request (Gnome Commander)
- small GUI corrections

#### 0.9.5:
- difference view
- workaround for some clients' three step approach on upload (delete, create empty file, upload):
  deletions and empty/one-byte files are removed within a short time automatically
- save date format in settings
- improved GUI

#### 0.9.4:
- use correct directory ('default') for data, when no or external authorization
- show quick view when browsing tags
- quick view for more file types: xml
- improved GUI
- show saved user name (instead of 'admin') in settings
- optionally show deleted files in web GUI

#### 0.9.3:
- phpEasyVCS now handles the authorization itself - no need to configure Apache
- GUI for settings - automatically called for setup
- improved UI 

#### 0.9.2:
- improved handling of temporary files (e.g. created by MS Office) 


## Credits

Amazing original work by Martin Vlcek.

## License

Work available under GPLv3 license.

phpEasyVCS uses some classes from the PEAR project, which are licensed 
under the PHP license, see license_PEAR.txt

## Problem Solving

The following is a list of problems with various WebDAV clients:

* With Nautilus you get an error message when moving/renaming (this is a Nautilus bug). But the move is executed correctly and you will see it after a refresh (F5).
* Windows XP: opening a file from a WebDAV drive, which is NOT mapped to a drive letter, will only work, if the opening program is WebDAV aware, like Microsoft Word.
* Windows 7: Although connecting to a WebDAV drive without authentication by http works perfectly, connecting to the same WebDAV drive with basic authentication set up only displays grayed out entries, clicking on a file or folder will do nothing.

## Comparison with other VCSes

phpEasyVCS is the only VCS which

* is implemented in pure PHP,
* stores the data in the file system, and
* offers WebDAV access.

As to my knowledge there is no other VCS even offering two of these features.
