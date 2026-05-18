# Traccar for IP-Symcon

[![IP-Symcon Version](https://img.shields.io/badge/IP--Symcon-8.1+-blue.svg)](https://www.symcon.de)
[![License: EUPL-1.2](https://img.shields.io/badge/License-EUPL--1.2-blue.svg)](LICENSE)

An IP-Symcon module library for integrating [Traccar](https://www.traccar.org/) GPS tracking server via its REST API.

**[Deutsche Version](README.de.md)**

---

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Module Overview](#module-overview)
  - [Traccar Splitter](#traccar-splitter)
  - [Traccar Configurator](#traccar-configurator)
  - [Traccar Device](#traccar-device)
- [Configuration](#configuration)
- [Variables](#variables)
- [PHP Functions](#php-functions)
- [License](#license)

---

## Features

- **Full Traccar API Integration**: Connect to any Traccar server (self-hosted or cloud)
- **API Token Authentication**: Secure authentication using Traccar API tokens
- **Device Discovery**: Automatic discovery of all devices from your Traccar server
- **Real-time Position Data**: Track latitude, longitude, altitude, speed, course, and more
- **Device Status**: Monitor online/offline status of tracked devices
- **Extended Attributes**: Battery level, motion detection, ignition status, odometer, engine hours, and more
- **Address Geocoding**: Display reverse-geocoded addresses from Traccar
- **Configurable Variables**: Choose which data points to display per device
- **German Localization**: Full German translation for UI and variables

---

## Requirements

- IP-Symcon 8.1 or higher
- Traccar Server (self-hosted or [Traccar subscription](https://www.traccar.org/product/tracking-server/))
- Network access to your Traccar server

---

## Installation

### Via Module Store (Recommended)

1. Open IP-Symcon Console
2. Navigate to **Modules** > **Module Store**
3. Search for "Traccar"
4. Click **Install**

### Manual Installation via Git

1. Open IP-Symcon Console
2. Navigate to **Modules** > **Modules**
3. Click **Add** (Plus icon)
4. Select **Add Module from URL**
5. Enter: `https://github.com/mwlf01/IPSymcon-Traccar.git`
6. Click **OK**

### Manual Installation (File Copy)

1. Clone or download this repository
2. Copy the folder to your IP-Symcon modules directory:
   - Windows: `C:\ProgramData\Symcon\modules\`
   - Linux: `/var/lib/symcon/modules/`
   - Docker: Check your volume mapping
3. Reload modules in IP-Symcon Console

---

## Module Overview

This library contains three modules that work together:

### Traccar Splitter

The **Traccar Splitter** module handles the connection to your Traccar server. It manages authentication via API token and provides API access to all child modules.

**Features:**
- Server connection configuration (host, port, HTTPS, TLS certificate verification)
- API Token authentication with session cookie, including automatic session refresh on expiration
- Configurable update interval (default: 30 seconds)
- Connection testing

### Traccar Configurator

The **Traccar Configurator** module automatically discovers all devices from your Traccar server and allows you to create device instances with a single click.

**Features:**
- Lists all devices from Traccar
- Shows device name, unique ID, status, category, model, and last update
- One-click device instance creation
- Detects already configured devices

### Traccar Device

The **Traccar Device** module represents a single tracked device and displays its position and status data.

**Features:**
- Real-time position tracking (latitude, longitude, altitude)
- Speed and course information
- Reverse-geocoded address display
- Battery level and voltage monitoring
- Motion and ignition detection
- Odometer, trip distance, and total distance
- Engine hours tracking
- GPS quality (satellites, HDOP, accuracy)
- Configurable variable visibility

---

## Configuration

### Step 1: Create Traccar Splitter Instance

1. Navigate to **Objects** > **Add Object** > **Instance**
2. Search for "Traccar Splitter"
3. Click **OK**
4. Configure:
   - **Traccar Server Host**: Your Traccar server hostname or IP (e.g., `demo.traccar.org`)
   - **Port**: API port (default: 443 for HTTPS)
   - **Use HTTPS**: Enable for secure connections (recommended)
   - **Verify TLS Certificate**: Validate the server's TLS certificate (recommended; disable only for self-signed setups)
   - **API Token**: Generate in Traccar under Settings → Account → Token
   - **Update Interval**: How often to poll for updates (default: 30 seconds)
5. Click **Test Connection** to verify
6. Click **Apply**

### Step 2: Create Traccar Configurator Instance

1. Navigate to **Objects** > **Add Object** > **Instance**
2. Search for "Traccar Configurator"
3. Click **OK**
4. The configurator will automatically connect to the Traccar Splitter instance
5. A list of all devices will be displayed

### Step 3: Add Device Instances

1. In the Traccar Configurator, find the device you want to add
2. Click the **+** button next to the device
3. The device instance will be created automatically with all settings

### Step 4: Configure Device Variables (Optional)

1. Open the created Traccar Device instance
2. In **Displayed Variables**, enable/disable the data points you want to see
3. Click **Apply**

---

## Variables

Each Traccar Device instance can create the following variables:

| Variable | Type | Description |
|----------|------|-------------|
| **Status** | String | Online/Offline status |
| **Last Update** | Integer | Unix timestamp of last position update |
| **Latitude** | Float | GPS latitude in degrees |
| **Longitude** | Float | GPS longitude in degrees |
| **Altitude** | Float | Altitude in meters |
| **Address** | String | Reverse-geocoded address |
| **Speed** | Float | Current speed in km/h |
| **Course** | Float | Heading/bearing in degrees |
| **Geofence** | String | Names of geofences the device is in |
| **Geofence IDs** | String | IDs of geofences the device is in |
| **Accuracy** | Float | GPS accuracy in meters |
| **Position Valid** | Boolean | Whether GPS fix is valid |
| **Satellites** | Integer | Number of GPS satellites |
| **HDOP** | Float | Horizontal dilution of precision |
| **Protocol** | String | Tracking protocol (e.g., osmand, teltonika) |
| **Device Time** | Integer | Timestamp from the device |
| **Server Time** | Integer | Timestamp from the server |
| **Battery** | Integer | Battery level in percent (for phones) |
| **Battery Voltage** | Float | Battery voltage in V (for GPS trackers) |
| **Charging** | Boolean | Whether device is charging |
| **External Power** | Float | External power voltage in V |
| **Motion** | Boolean | Whether the device is moving |
| **Ignition** | Boolean | Vehicle ignition status |
| **Alarm** | String | Alarm status |
| **Total Distance** | Float | Total distance traveled in km |
| **Odometer** | Float | Device odometer in km |
| **Trip Distance** | Float | Current trip distance in km |
| **Engine Hours** | Float | Engine running hours |
| **Fuel Level** | Float | Fuel level in percent |
| **Signal Strength** | Integer | RSSI signal strength in dBm |
| **Activity** | String | Activity state (still, walking, etc.) |
| **Category** | String | Device category |
| **Model** | String | Device model |
| **Phone** | String | Associated phone number |
| **Contact** | String | Contact information |
| **Disabled** | Boolean | Whether the device is disabled in Traccar |

---

## PHP Functions

### Traccar Splitter

#### TestConnection
Test the connection to the Traccar server.

```php
bool TRACCAR_TestConnection(int $InstanceID);
```

#### GetDevices
Retrieve all devices from the Traccar server.

```php
array TRACCAR_GetDevices(int $InstanceID);
```

#### GetPositions
Retrieve current positions for all devices.

```php
array TRACCAR_GetPositions(int $InstanceID);
```

#### GetDevicePosition
Get the position for a specific device.

```php
array TRACCAR_GetDevicePosition(int $InstanceID, int $DeviceID);
```

#### GetGeofences
Retrieve all geofences from the server.

```php
array TRACCAR_GetGeofences(int $InstanceID);
```

#### GetServerInfo
Get Traccar server information.

```php
array TRACCAR_GetServerInfo(int $InstanceID);
```

#### UpdateDevices
Trigger an update for all device instances.

```php
void TRACCAR_UpdateDevices(int $InstanceID);
```

#### RefreshSession
Forces creation of a new session (useful if the cookie became invalid outside of the regular polling cycle). The splitter also refreshes the session automatically when a request returns HTTP 401.

```php
bool TRACCAR_RefreshSession(int $InstanceID);
```

### Traccar Device

#### RequestUpdate
Request an immediate position update for this device.

```php
void TRACCARDEV_RequestUpdate(int $InstanceID);
```

#### GetPosition
Get the current position data as an array.

```php
array TRACCARDEV_GetPosition(int $InstanceID);
```

**Example:**
```php
$position = TRACCARDEV_GetPosition(12345);
echo "Latitude: " . $position['latitude'];
echo "Longitude: " . $position['longitude'];
echo "Speed: " . $position['speed'] . " km/h";
```

#### GetRawAttributes
Get all raw position attributes as array.

```php
array TRACCARDEV_GetRawAttributes(int $InstanceID);
```

---

## Troubleshooting

### Connection Failed
- Verify the server hostname and port are correct
- Check if HTTPS is required for your server
- Ensure your API token is valid and not expired
- If the server uses a self-signed certificate, disable **Verify TLS Certificate**
- Test the API directly: `https://your-server/api/server`

### Devices Not Showing
- Ensure the Traccar Splitter instance is connected (green status)
- Check if your user has permission to view devices in Traccar
- Click "Refresh Device List" in the configurator

### Position Not Updating
- Check the update interval in Traccar Splitter settings
- Verify the device is sending data to Traccar
- Check the "Last Update" variable for the last known position time

---

## Changelog

### Version 1.2.0
- **License**: relicensed from MIT to EUPL v. 1.2. The new licence is in the `LICENSE` file; an SPDX header has been added to every module source file.
- **Authentication**: kept the session-cookie model for full backwards compatibility. The splitter now automatically re-establishes the session when a request returns HTTP 401, and multiple `Set-Cookie` headers are merged correctly. The token is no longer written to the debug log.
- **Device status tracking**: device instances now subscribe to the splitter's status changes via `MessageSink` (`IM_CHANGESTATUS` and `IPS_KERNELSTARTED`). This fixes the false *No Traccar Splitter instance connected* error on Symcon 9.0+, where `HasActiveParent()` inside `ApplyChanges` is not reliable because the parent connection is established asynchronously.
- **Security**: API token, session cookies and full API response bodies are no longer written to the debug log; only HTTP code and payload size.
- **Security**: new **Verify TLS Certificate** option (default on) replaces the previously hard-coded disabled certificate verification.
- **Receive filter precision**: the device-side receive-data filter is now anchored so that device ID `1` no longer matches IDs like `10`, `100`, `11`, etc.
- **Self-healing status**: a successful API request after a previous failure now restores the `Active` status without manual intervention.
- **No-parent detection**: device instances now report status `No parent` correctly, driven by the splitter's actual status rather than a one-shot check during `ApplyChanges`.
- **Geofence cache**: geofence list is cached for 5 minutes per splitter, reducing API load.
- **RequestUpdate**: the device's `Update Now` now delegates to the splitter, ensuring consistent data (including geofence names) and removing a duplicate update path.
- **Configurator**: form opens immediately when the splitter is inactive, with a hint instead of blocking on a 30-second timeout.
- **HTTP timeouts**: reduced default request timeout from 30 s to 15 s and added a 5 s connect timeout.
- **Battery voltage**: only written when both `battery` and `batteryLevel` attributes are present (the convention for tracker hardware), replacing the previous `< 50` heuristic.
- **Fuel level**: only reads `fuelLevel` (percent); the ambiguous `fuel` attribute (often litres) is ignored to avoid wrong units.
- **Alarm**: cleared when no alarm is reported, instead of keeping the last value.
- **Status normalization**: `Status` variable always stored lowercase; duplicate presentation options removed.

### Version 1.1.1
- Fixed warning when no parent instance is connected during RequestUpdate

### Version 1.1.0
- Added separate Geofence Names and Geofence IDs variables
- Added Device Time and Server Time variables
- Added Contact and Disabled variables
- Improved variable ordering into logical groups
- Improved German translations
- Code cleanup and optimizations

### Version 1.0.0
- Initial release
- Traccar Splitter module for server connection with session-based authentication
- Traccar Configurator for device discovery
- Traccar Device for position tracking with extensive attribute support
- API Token authentication
- Full German localization

---

## Support

For issues, feature requests, or contributions, please visit:
- [GitHub Repository](https://github.com/mwlf01/IPSymcon-Traccar)
- [GitHub Issues](https://github.com/mwlf01/IPSymcon-Traccar/issues)
- [Symcon Community](https://community.symcon.de/) – User: **mwlf**
- [Traccar Documentation](https://www.traccar.org/documentation/)

---

## License

This project is licensed under the **European Union Public Licence (EUPL) v. 1.2** — see the [LICENSE](LICENSE) file for the full text.

The EUPL is a copyleft licence: derivative works that are distributed must also be released under the EUPL or a compatible licence (e.g. GPL, AGPL, MPL, LGPL — see the appendix of the EUPL for the full compatibility list). Earlier releases up to version 1.1.1 remain available under the previous MIT licence.

The EUPL is published in 24 official language versions, all legally equivalent. Other language versions are available on the [official EU page](https://interoperable-europe.ec.europa.eu/collection/eupl/eupl-text-eupl-12).

---

## Author

**mwlf01**

- GitHub: [@mwlf01](https://github.com/mwlf01)
- Symcon Community: [mwlf](https://community.symcon.de/)

---

## Disclaimer

This project is an unofficial third-party integration and is not affiliated with, endorsed by, or connected to [Traccar](https://www.traccar.org/) or its developers. 

**Traccar** is a trademark of Anton Tananaev. This module uses the publicly available Traccar REST API and does not include any Traccar source code.

Traccar is licensed under the [Apache License 2.0](https://github.com/traccar/traccar/blob/master/LICENSE.txt).
