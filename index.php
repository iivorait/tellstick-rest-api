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
    You may wish to tweak the "Refresh Time" setting to be a smaller value if you are using motion sensors
Channel:
    State URL Extension: ?device_id=XXX (GET http://server_host/index.php for ID listing, required only if you want to get device status like motion alerts)
    Command URL Extension: ?device_id=XXX (GET http://server_host/index.php for ID listing, required only if you want to send commands to on/off switches)
    On Value: ON
    Off Value: OFF
*/

$device_id = filter_input(INPUT_GET, 'device_id', FILTER_SANITIZE_NUMBER_INT);
$action = strtolower(file_get_contents('php://input')); 

if(empty($action) && !empty($device_id)) { //fetch single device status
        $devices_raw = shell_exec("tdtool --list-devices");
        $devices = explode("\n", $devices_raw);
        foreach($devices as $device) {
                if(strpos($device, "\tid=" . $device_id . "\t") !== false) {
                        echo explode("lastsentcommand=", $device)[1];
                        die();
                }
        }
        http_response_code(404);
        echo "device id " . $device_id . " not found";
}
else if(($action != "on" && $action != "off") or empty($device_id)) { //list devices (default action)
        echo "<p>Usage:</p>";
        echo "<p>To get device status: GET host_address/?device_id=ID_FROM_LIST_BELOW returns [ON|OFF] as response</p>";
        echo "<p>To control device: POST host_address/?device_id=ID_FROM_LIST_BELOW and [ON|OFF] as payload</p>";
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

