# tellstick-rest-api
Simple RESTful wrapper for tdtool. For use with OpenHab / Home Assistant / etc.

## Usage

Tellstick software (tdtool, TelldusCenter) is required on the server and devices configured (https://developer.telldus.com/wiki/TellStickInstallationUbuntu).

To allow tdtool to access the Tellstick service:

```
in /lib/systemd/system/apache2.service set "PrivateTmp = true" to "PrivateTmp = false"
sudo systemctl daemon-reload
sudo service apache2 restart
```

(from https://marcussjogren.wordpress.com / https://forum.telldus.com/viewtopic.php?t=15277)

## OpenHab 3 HTTP Binding configuration

### Switches

```
Thing:
    Base URL: http://server_host/index.php
    Command Method: POST
Channel:
    Command URL Extension: ?device_id=XXX (GET http://server_host/index.php for ID listing)
    On Value: ON
    Off Value: OFF
    Read/Write Mode: Write Only
```

### Sensors

```
Thing:
    Base URL: http://server_host/sensors.php
    State Method: GET
Channel:
    State URL Extension: ?device_id=XXX (GET http://server_host/sensors.php for ID listing)
    Read/Write Mode: Read Only
    State Transformation: JSONPATH:$.temperature (or some other field from the sensor)
```

If you get the "Transformation service JSONPATH for pattern XXX not found!" error, the official JSONPath Transformation addon needs to be installed
