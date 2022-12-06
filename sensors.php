<?php
/*
Simple RESTful API for reading sensor values from Telldus Tellstick.
Tellstick software (tdtool, TelldusCenter) is required on the server (https://developer.telldus.com/wiki/TellStickInstallationUbuntu).
The sensors need to be configured to TelldusCenter before the values can be read via the API.

To allow tdtool to access the Tellstick service:
in /lib/systemd/system/apache2.service set "PrivateTmp = true" to "PrivateTmp = false"
sudo systemctl daemon-reload
sudo service apache2 restart
(from https://marcussjogren.wordpress.com / https://forum.telldus.com/viewtopic.php?t=15277)

OpenHab 3 HTTP Binding configuration
Thing:
    Base URL: http://server_host/sensors.php (alternatively leave the sensors.php out and insert it to channel's State URL Extension)
    State Method: GET
Channel:
    State URL Extension: ?device_id=XXX (GET http://server_host/sensors.php for ID listing)
    Read/Write Mode: Read Only
    State Transformation: JSONPATH:$.temperature (or some other field from the sensor)
     (if you get the "Transformation service JSONPATH for pattern XXX not found!" error, the official JSONPath Transformation addon needs to be installed)
*/

$device_id = filter_input(INPUT_GET, 'device_id', FILTER_SANITIZE_NUMBER_INT);
$action = strtolower(file_get_contents('php://input'));

if(empty($device_id)) {
   echo "Usage: GET host_address/sensors.php?device_id=ID_FROM_LIST_BELOW";
   echo "<pre>";
   echo shell_exec("tdtool --list-sensors");
   echo "</pre>";
}
else {
   header('Content-Type: application/json');
   $sensors_raw = shell_exec("tdtool --list-sensors");
   $sensors = explode("\n", $sensors_raw);
   foreach($sensors as $sensor) {
      if(strpos($sensor, "\tid=" . $device_id . "\t") !== false) {
         $result = str_replace("=", '":"', $sensor);
         $result = str_replace("\t", '", "', $result);
         echo '{"' . $result . '"}';
         return;
      }
   }
   http_response_code(404);
   echo '{"error": "sensor id ' . $device_id . ' not found"}';
}
