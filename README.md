#### RotU-Stats for Call of Duty 4 Reign of the Undead - Revolution Zombie Mod

This is a PHP written code to MaM/B3 rotustats plugin.
You can find more information at:
    * http://rotu-revolution.com
    * http://bigbrotherbot.net
    * http://gsmanager.de

###### Needs:
    * Working webserver with PHP engine.
    * Call of Duty 4 server at the same machine
    * MySQL/MariaDB
    * B3/ManuAdminMod with rotustats plugin

###### Installation:
* Copy the rotustats folder in your weberserver root directory.
* Edit this line with your database setting and user/password:
// DB connection
$db = new PDO('mysql:host=localhost;dbname=rotu;charset=utf8', 'root', '');

Then Access your page at:
http://yourdomain.com/rotustats

Enjoy!


