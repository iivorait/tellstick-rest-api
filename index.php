<?php
/*
Simple RESTful API for controlling Telldus Tellstick.
Tellstick software (tdtool, TelldusCenter) is required on the server (https://developer.telldus.com/wiki/TellStickInstallationUbuntu).

To allow tdtool to access the Tellstick service:
in /lib/systemd/system/apache2.service set "PrivateTmp = true" to "PrivateTmp = false"
sudo systemctl daemon-reload
sudo service apache2 restart
(from https://marcussjogren.wordpress.com / https://forum.telldus.com/viewtopic.php?t=15277)

OpenHab 3 HTTP Binding configuration
Thing:
    Base URL: http://server_host/index.php
    Command Method: POST
Channel:
    Command URL Extension: ?device_id=XXX (GET http://server_host/index.php for ID listing)
    On Value: ON
    Off Value: OFF
    Read/Write Mode: Write Only
*/

$device_id = filter_input(INPUT_GET, 'device_id', FILTER_SANITIZE_NUMBER_INT);
$action = strtolower(file_get_contents('php://input')); 

if(($action != "on" && $action != "off") or empty($device_id)) {
   echo "Usage: POST host_address/?device_id=ID_FROM_LIST_BELOW and [ON|OFF] as payload";
   echo "<pre>";
   echo shell_exec("tdtool --list-devices");
   echo "</pre>";
}
else if($action == "on") {
   echo shell_exec("tdtool --on $device_id");
}
else if($action == "off") {
   echo shell_exec("tdtool --off $device_id");
}
