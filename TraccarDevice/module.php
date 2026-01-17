<?php
declare(strict_types=1);

class TraccarDevice extends IPSModule
{
    private const STATUS_ACTIVE = 102;
    private const STATUS_INACTIVE = 104;
    private const STATUS_NO_PARENT = 201;

    public function Create()
    {
        parent::Create();

        $this->ConnectParent('{B02E003C-7A70-C422-C417-0E41DC1CE86D}');

        $this->RegisterPropertyInteger('DeviceID', 0);
        $this->RegisterPropertyString('DeviceName', '');
        $this->RegisterPropertyString('UniqueID', '');

        $this->RegisterPropertyBoolean('ShowStatus', true);
        $this->RegisterPropertyBoolean('ShowLatitude', true);
        $this->RegisterPropertyBoolean('ShowLongitude', true);
        $this->RegisterPropertyBoolean('ShowAltitude', true);
        $this->RegisterPropertyBoolean('ShowSpeed', true);
        $this->RegisterPropertyBoolean('ShowCourse', true);
        $this->RegisterPropertyBoolean('ShowAddress', true);
        $this->RegisterPropertyBoolean('ShowAccuracy', true);
        $this->RegisterPropertyBoolean('ShowLastUpdate', true);
        $this->RegisterPropertyBoolean('ShowValid', false);
        $this->RegisterPropertyBoolean('ShowProtocol', false);
        $this->RegisterPropertyBoolean('ShowSatellites', false);
        $this->RegisterPropertyBoolean('ShowHDOP', false);
        $this->RegisterPropertyBoolean('ShowBattery', true);
        $this->RegisterPropertyBoolean('ShowCharge', false);
        $this->RegisterPropertyBoolean('ShowPower', false);
        $this->RegisterPropertyBoolean('ShowMotion', true);
        $this->RegisterPropertyBoolean('ShowIgnition', false);
        $this->RegisterPropertyBoolean('ShowAlarm', false);
        $this->RegisterPropertyBoolean('ShowTotalDistance', true);
        $this->RegisterPropertyBoolean('ShowOdometer', false);
        $this->RegisterPropertyBoolean('ShowDistance', false);
        $this->RegisterPropertyBoolean('ShowHours', false);
        $this->RegisterPropertyBoolean('ShowBatteryVoltage', false);
        $this->RegisterPropertyBoolean('ShowActivity', false);
        $this->RegisterPropertyBoolean('ShowCategory', false);
        $this->RegisterPropertyBoolean('ShowModel', false);
        $this->RegisterPropertyBoolean('ShowPhone', false);
        $this->RegisterPropertyBoolean('ShowRSSI', false);
        $this->RegisterPropertyBoolean('ShowFuel', false);
        $this->RegisterPropertyBoolean('ShowGeofence', false);
        $this->RegisterPropertyBoolean('ShowGeofenceIds', false);
        $this->RegisterPropertyBoolean('ShowDeviceTime', false);
        $this->RegisterPropertyBoolean('ShowServerTime', false);
        $this->RegisterPropertyBoolean('ShowDisabled', false);
        $this->RegisterPropertyBoolean('ShowContact', false);

        $this->RegisterAttributeInteger('LastPositionID', 0);
        $this->RegisterAttributeString('RawAttributes', '{}');
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();

        $deviceId = $this->ReadPropertyInteger('DeviceID');

        if ($deviceId === 0) {
            $this->SetStatus(self::STATUS_INACTIVE);
            return;
        }

        $pos = 1;
        // Status & Time
        $this->MaintainVariable('Status', $this->Translate('Status'), VARIABLETYPE_STRING, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'OPTIONS' => json_encode([['Value' => 'online', 'Caption' => $this->Translate('Online'), 'IconActive' => false, 'IconValue' => '', 'ColorActive' => true, 'ColorValue' => 0x00FF00], ['Value' => 'Online', 'Caption' => $this->Translate('Online'), 'IconActive' => false, 'IconValue' => '', 'ColorActive' => true, 'ColorValue' => 0x00FF00], ['Value' => 'offline', 'Caption' => $this->Translate('Offline'), 'IconActive' => false, 'IconValue' => '', 'ColorActive' => true, 'ColorValue' => 0xFF0000], ['Value' => 'Offline', 'Caption' => $this->Translate('Offline'), 'IconActive' => false, 'IconValue' => '', 'ColorActive' => true, 'ColorValue' => 0xFF0000], ['Value' => 'unknown', 'Caption' => $this->Translate('Unknown'), 'IconActive' => false, 'IconValue' => '', 'ColorActive' => false, 'ColorValue' => -1], ['Value' => 'Unknown', 'Caption' => $this->Translate('Unknown'), 'IconActive' => false, 'IconValue' => '', 'ColorActive' => false, 'ColorValue' => -1]])], $pos++, $this->ReadPropertyBoolean('ShowStatus'));
        $this->MaintainVariable('LastUpdate', $this->Translate('Last Update'), VARIABLETYPE_INTEGER, ['PRESENTATION' => VARIABLE_PRESENTATION_DATE_TIME], $pos++, $this->ReadPropertyBoolean('ShowLastUpdate'));
        // Position
        $this->MaintainVariable('Latitude', $this->Translate('Latitude'), VARIABLETYPE_FLOAT, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'DIGITS' => 8, 'SUFFIX' => '°'], $pos++, $this->ReadPropertyBoolean('ShowLatitude'));
        $this->MaintainVariable('Longitude', $this->Translate('Longitude'), VARIABLETYPE_FLOAT, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'DIGITS' => 8, 'SUFFIX' => '°'], $pos++, $this->ReadPropertyBoolean('ShowLongitude'));
        $this->MaintainVariable('Altitude', $this->Translate('Altitude'), VARIABLETYPE_FLOAT, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'DIGITS' => 1, 'SUFFIX' => ' m'], $pos++, $this->ReadPropertyBoolean('ShowAltitude'));
        $this->MaintainVariable('Address', $this->Translate('Address'), VARIABLETYPE_STRING, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION], $pos++, $this->ReadPropertyBoolean('ShowAddress'));
        // Movement
        $this->MaintainVariable('Speed', $this->Translate('Speed'), VARIABLETYPE_FLOAT, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'DIGITS' => 1, 'SUFFIX' => ' km/h'], $pos++, $this->ReadPropertyBoolean('ShowSpeed'));
        $this->MaintainVariable('Course', $this->Translate('Course'), VARIABLETYPE_FLOAT, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'DIGITS' => 1, 'SUFFIX' => '°'], $pos++, $this->ReadPropertyBoolean('ShowCourse'));
        // Geofence
        $this->MaintainVariable('Geofence', $this->Translate('Geofence'), VARIABLETYPE_STRING, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION], $pos++, $this->ReadPropertyBoolean('ShowGeofence'));
        $this->MaintainVariable('GeofenceIds', $this->Translate('Geofence IDs'), VARIABLETYPE_STRING, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION], $pos++, $this->ReadPropertyBoolean('ShowGeofenceIds'));
        // GPS Quality
        $this->MaintainVariable('Accuracy', $this->Translate('Accuracy'), VARIABLETYPE_FLOAT, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'DIGITS' => 1, 'SUFFIX' => ' m'], $pos++, $this->ReadPropertyBoolean('ShowAccuracy'));
        $this->MaintainVariable('Valid', $this->Translate('Position Valid'), VARIABLETYPE_BOOLEAN, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'OPTIONS' => json_encode([['Value' => false, 'Caption' => $this->Translate('Invalid'), 'IconActive' => false, 'IconValue' => '', 'ColorActive' => false, 'ColorValue' => -1], ['Value' => true, 'Caption' => $this->Translate('Valid'), 'IconActive' => false, 'IconValue' => '', 'ColorActive' => false, 'ColorValue' => -1]])], $pos++, $this->ReadPropertyBoolean('ShowValid'));
        $this->MaintainVariable('Satellites', $this->Translate('Satellites'), VARIABLETYPE_INTEGER, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION], $pos++, $this->ReadPropertyBoolean('ShowSatellites'));
        $this->MaintainVariable('HDOP', $this->Translate('HDOP'), VARIABLETYPE_FLOAT, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'DIGITS' => 1], $pos++, $this->ReadPropertyBoolean('ShowHDOP'));
        $this->MaintainVariable('Protocol', $this->Translate('Protocol'), VARIABLETYPE_STRING, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION], $pos++, $this->ReadPropertyBoolean('ShowProtocol'));
        // Time Details
        $this->MaintainVariable('DeviceTime', $this->Translate('Device Time'), VARIABLETYPE_INTEGER, ['PRESENTATION' => VARIABLE_PRESENTATION_DATE_TIME], $pos++, $this->ReadPropertyBoolean('ShowDeviceTime'));
        $this->MaintainVariable('ServerTime', $this->Translate('Server Time'), VARIABLETYPE_INTEGER, ['PRESENTATION' => VARIABLE_PRESENTATION_DATE_TIME], $pos++, $this->ReadPropertyBoolean('ShowServerTime'));
        // Power
        $this->MaintainVariable('Battery', $this->Translate('Battery'), VARIABLETYPE_INTEGER, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'SUFFIX' => ' %'], $pos++, $this->ReadPropertyBoolean('ShowBattery'));
        $this->MaintainVariable('BatteryVoltage', $this->Translate('Battery Voltage'), VARIABLETYPE_FLOAT, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'DIGITS' => 2, 'SUFFIX' => ' V'], $pos++, $this->ReadPropertyBoolean('ShowBatteryVoltage'));
        $this->MaintainVariable('Charge', $this->Translate('Charging'), VARIABLETYPE_BOOLEAN, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'OPTIONS' => json_encode([['Value' => false, 'Caption' => $this->Translate('No'), 'IconActive' => false, 'IconValue' => '', 'ColorActive' => false, 'ColorValue' => -1], ['Value' => true, 'Caption' => $this->Translate('Yes'), 'IconActive' => false, 'IconValue' => '', 'ColorActive' => false, 'ColorValue' => -1]])], $pos++, $this->ReadPropertyBoolean('ShowCharge'));
        $this->MaintainVariable('Power', $this->Translate('External Power'), VARIABLETYPE_FLOAT, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'DIGITS' => 2, 'SUFFIX' => ' V'], $pos++, $this->ReadPropertyBoolean('ShowPower'));
        // Vehicle State
        $this->MaintainVariable('Motion', $this->Translate('Motion'), VARIABLETYPE_BOOLEAN, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'OPTIONS' => json_encode([['Value' => false, 'Caption' => $this->Translate('No'), 'IconActive' => false, 'IconValue' => '', 'ColorActive' => false, 'ColorValue' => -1], ['Value' => true, 'Caption' => $this->Translate('Yes'), 'IconActive' => false, 'IconValue' => '', 'ColorActive' => false, 'ColorValue' => -1]])], $pos++, $this->ReadPropertyBoolean('ShowMotion'));
        $this->MaintainVariable('Ignition', $this->Translate('Ignition'), VARIABLETYPE_BOOLEAN, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'OPTIONS' => json_encode([['Value' => false, 'Caption' => $this->Translate('Off'), 'IconActive' => false, 'IconValue' => '', 'ColorActive' => false, 'ColorValue' => -1], ['Value' => true, 'Caption' => $this->Translate('On'), 'IconActive' => false, 'IconValue' => '', 'ColorActive' => false, 'ColorValue' => -1]])], $pos++, $this->ReadPropertyBoolean('ShowIgnition'));
        $this->MaintainVariable('Alarm', $this->Translate('Alarm'), VARIABLETYPE_STRING, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION], $pos++, $this->ReadPropertyBoolean('ShowAlarm'));
        // Distance & Usage
        $this->MaintainVariable('TotalDistance', $this->Translate('Total Distance'), VARIABLETYPE_FLOAT, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'DIGITS' => 2, 'SUFFIX' => ' km'], $pos++, $this->ReadPropertyBoolean('ShowTotalDistance'));
        $this->MaintainVariable('Odometer', $this->Translate('Odometer'), VARIABLETYPE_FLOAT, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'DIGITS' => 2, 'SUFFIX' => ' km'], $pos++, $this->ReadPropertyBoolean('ShowOdometer'));
        $this->MaintainVariable('Distance', $this->Translate('Trip Distance'), VARIABLETYPE_FLOAT, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'DIGITS' => 2, 'SUFFIX' => ' km'], $pos++, $this->ReadPropertyBoolean('ShowDistance'));
        $this->MaintainVariable('Hours', $this->Translate('Engine Hours'), VARIABLETYPE_FLOAT, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'DIGITS' => 1, 'SUFFIX' => ' h'], $pos++, $this->ReadPropertyBoolean('ShowHours'));
        // Other Sensors
        $this->MaintainVariable('Fuel', $this->Translate('Fuel Level'), VARIABLETYPE_FLOAT, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'DIGITS' => 1, 'SUFFIX' => ' %'], $pos++, $this->ReadPropertyBoolean('ShowFuel'));
        $this->MaintainVariable('RSSI', $this->Translate('Signal Strength'), VARIABLETYPE_INTEGER, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'SUFFIX' => ' dBm'], $pos++, $this->ReadPropertyBoolean('ShowRSSI'));
        $this->MaintainVariable('Activity', $this->Translate('Activity'), VARIABLETYPE_STRING, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION], $pos++, $this->ReadPropertyBoolean('ShowActivity'));
        // Device Properties
        $this->MaintainVariable('Category', $this->Translate('Category'), VARIABLETYPE_STRING, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION], $pos++, $this->ReadPropertyBoolean('ShowCategory'));
        $this->MaintainVariable('Model', $this->Translate('Model'), VARIABLETYPE_STRING, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION], $pos++, $this->ReadPropertyBoolean('ShowModel'));
        $this->MaintainVariable('Phone', $this->Translate('Phone'), VARIABLETYPE_STRING, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION], $pos++, $this->ReadPropertyBoolean('ShowPhone'));
        $this->MaintainVariable('Contact', $this->Translate('Contact'), VARIABLETYPE_STRING, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION], $pos++, $this->ReadPropertyBoolean('ShowContact'));
        $this->MaintainVariable('Disabled', $this->Translate('Disabled'), VARIABLETYPE_BOOLEAN, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION, 'OPTIONS' => json_encode([['Value' => false, 'Caption' => $this->Translate('No'), 'IconActive' => false, 'IconValue' => '', 'ColorActive' => false, 'ColorValue' => -1], ['Value' => true, 'Caption' => $this->Translate('Yes'), 'IconActive' => false, 'IconValue' => '', 'ColorActive' => true, 'ColorValue' => 0xFF0000]])], $pos++, $this->ReadPropertyBoolean('ShowDisabled'));

        $this->SetReceiveDataFilter('.*"deviceId":' . $deviceId . '.*');

        $this->SetStatus(self::STATUS_ACTIVE);

        $this->RequestUpdate();
    }

    public function GetConfigurationForm(): string
    {
        return json_encode([
            'elements' => [
                [
                    'type' => 'ExpansionPanel',
                    'caption' => 'Device Information',
                    'expanded' => true,
                    'items' => [
                        [
                            'type' => 'NumberSpinner',
                            'name' => 'DeviceID',
                            'caption' => 'Traccar Device ID',
                            'enabled' => false
                        ],
                        [
                            'type' => 'ValidationTextBox',
                            'name' => 'DeviceName',
                            'caption' => 'Device Name',
                            'enabled' => false
                        ],
                        [
                            'type' => 'ValidationTextBox',
                            'name' => 'UniqueID',
                            'caption' => 'Unique ID',
                            'enabled' => false
                        ]
                    ]
                ],
                [
                    'type' => 'ExpansionPanel',
                    'caption' => 'Position Data',
                    'expanded' => true,
                    'items' => [
                        ['type' => 'CheckBox', 'name' => 'ShowStatus', 'caption' => 'Connection Status'],
                        ['type' => 'CheckBox', 'name' => 'ShowLastUpdate', 'caption' => 'Last Update'],
                        ['type' => 'CheckBox', 'name' => 'ShowLatitude', 'caption' => 'Latitude'],
                        ['type' => 'CheckBox', 'name' => 'ShowLongitude', 'caption' => 'Longitude'],
                        ['type' => 'CheckBox', 'name' => 'ShowAltitude', 'caption' => 'Altitude'],
                        ['type' => 'CheckBox', 'name' => 'ShowAddress', 'caption' => 'Address'],
                        ['type' => 'CheckBox', 'name' => 'ShowSpeed', 'caption' => 'Speed'],
                        ['type' => 'CheckBox', 'name' => 'ShowCourse', 'caption' => 'Heading'],
                        ['type' => 'CheckBox', 'name' => 'ShowGeofence', 'caption' => 'Geofence Names'],
                        ['type' => 'CheckBox', 'name' => 'ShowGeofenceIds', 'caption' => 'Geofence IDs'],
                        ['type' => 'CheckBox', 'name' => 'ShowAccuracy', 'caption' => 'GPS Accuracy'],
                        ['type' => 'CheckBox', 'name' => 'ShowValid', 'caption' => 'Position Valid'],
                        ['type' => 'CheckBox', 'name' => 'ShowSatellites', 'caption' => 'Satellites'],
                        ['type' => 'CheckBox', 'name' => 'ShowHDOP', 'caption' => 'HDOP'],
                        ['type' => 'CheckBox', 'name' => 'ShowProtocol', 'caption' => 'Protocol'],
                        ['type' => 'CheckBox', 'name' => 'ShowDeviceTime', 'caption' => 'Device Time'],
                        ['type' => 'CheckBox', 'name' => 'ShowServerTime', 'caption' => 'Server Time']
                    ]
                ],
                [
                    'type' => 'ExpansionPanel',
                    'caption' => 'Sensor Data',
                    'items' => [
                        ['type' => 'CheckBox', 'name' => 'ShowBattery', 'caption' => 'Battery Level'],
                        ['type' => 'CheckBox', 'name' => 'ShowBatteryVoltage', 'caption' => 'Battery Voltage'],
                        ['type' => 'CheckBox', 'name' => 'ShowCharge', 'caption' => 'Charging'],
                        ['type' => 'CheckBox', 'name' => 'ShowPower', 'caption' => 'External Power'],
                        ['type' => 'CheckBox', 'name' => 'ShowMotion', 'caption' => 'Motion'],
                        ['type' => 'CheckBox', 'name' => 'ShowIgnition', 'caption' => 'Ignition'],
                        ['type' => 'CheckBox', 'name' => 'ShowAlarm', 'caption' => 'Alarm'],
                        ['type' => 'CheckBox', 'name' => 'ShowTotalDistance', 'caption' => 'Total Distance'],
                        ['type' => 'CheckBox', 'name' => 'ShowOdometer', 'caption' => 'Odometer'],
                        ['type' => 'CheckBox', 'name' => 'ShowDistance', 'caption' => 'Trip Distance'],
                        ['type' => 'CheckBox', 'name' => 'ShowHours', 'caption' => 'Engine Hours'],
                        ['type' => 'CheckBox', 'name' => 'ShowFuel', 'caption' => 'Fuel Level'],
                        ['type' => 'CheckBox', 'name' => 'ShowRSSI', 'caption' => 'Signal Strength'],
                        ['type' => 'CheckBox', 'name' => 'ShowActivity', 'caption' => 'Activity']
                    ]
                ],
                [
                    'type' => 'ExpansionPanel',
                    'caption' => 'Device Properties',
                    'items' => [
                        ['type' => 'CheckBox', 'name' => 'ShowCategory', 'caption' => 'Category'],
                        ['type' => 'CheckBox', 'name' => 'ShowModel', 'caption' => 'Model'],
                        ['type' => 'CheckBox', 'name' => 'ShowPhone', 'caption' => 'Phone Number'],
                        ['type' => 'CheckBox', 'name' => 'ShowContact', 'caption' => 'Contact'],
                        ['type' => 'CheckBox', 'name' => 'ShowDisabled', 'caption' => 'Device Disabled']
                    ]
                ]
            ],
            'actions' => [
                [
                    'type' => 'Button',
                    'caption' => 'Update Now',
                    'onClick' => 'TRACCARDEV_RequestUpdate($id);'
                ]
            ],
            'status' => [
                [
                    'code' => self::STATUS_ACTIVE,
                    'icon' => 'active',
                    'caption' => 'Connected and receiving data'
                ],
                [
                    'code' => self::STATUS_INACTIVE,
                    'icon' => 'inactive',
                    'caption' => 'No Traccar device configured'
                ],
                [
                    'code' => self::STATUS_NO_PARENT,
                    'icon' => 'error',
                    'caption' => 'No Traccar Splitter instance connected'
                ]
            ]
        ]);
    }

    public function ReceiveData($JSONString)
    {
        $data = json_decode($JSONString, true);
        if (!is_array($data)) {
            return;
        }

        $device = $data['device'] ?? [];
        $position = $data['position'] ?? [];
        $geofenceMap = $data['geofenceMap'] ?? [];

        $myDeviceId = $this->ReadPropertyInteger('DeviceID');
        $deviceId = $data['deviceId'] ?? ($device['id'] ?? ($position['deviceId'] ?? 0));

        $this->SendDebug('ReceiveData', "Received deviceId: {$deviceId}, myDeviceId: {$myDeviceId}", 0);

        if ($deviceId !== $myDeviceId) {
            return;
        }

        $this->UpdateDeviceData($device, $position, $geofenceMap);
    }

    public function RequestUpdate(): void
    {
        $deviceId = $this->ReadPropertyInteger('DeviceID');
        if ($deviceId === 0) {
            return;
        }

        $data = [
            'DataID' => '{D6BB3A8B-2C57-050E-0D98-7853B4E18BAE}',
            'Buffer' => json_encode([
                'Method' => 'GET',
                'Endpoint' => '/api/devices?id=' . $deviceId,
                'Body' => null
            ])
        ];
        $response = $this->SendDataToParent(json_encode($data));

        if ($response !== false && $response !== '') {
            $devices = json_decode($response, true);
            if (is_array($devices) && count($devices) > 0) {
                $device = $devices[0];

                $posData = [
                    'DataID' => '{D6BB3A8B-2C57-050E-0D98-7853B4E18BAE}',
                    'Buffer' => json_encode([
                        'Method' => 'GET',
                        'Endpoint' => '/api/positions?deviceId=' . $deviceId,
                        'Body' => null
                    ])
                ];
                $positionResponse = $this->SendDataToParent(json_encode($posData));

                $position = [];
                if ($positionResponse !== false && $positionResponse !== '') {
                    $positions = json_decode($positionResponse, true);
                    if (is_array($positions) && count($positions) > 0) {
                        $position = $positions[0];
                    }
                }

                $this->UpdateDeviceData($device, $position);
            }
        }
    }

    public function GetPosition(): array
    {
        $result = [
            'latitude' => 0.0,
            'longitude' => 0.0,
            'altitude' => 0.0,
            'speed' => 0.0,
            'course' => 0.0,
            'address' => '',
            'lastUpdate' => 0
        ];

        if ($this->ReadPropertyBoolean('ShowLatitude')) {
            $result['latitude'] = $this->GetValue('Latitude');
        }
        if ($this->ReadPropertyBoolean('ShowLongitude')) {
            $result['longitude'] = $this->GetValue('Longitude');
        }
        if ($this->ReadPropertyBoolean('ShowAltitude')) {
            $result['altitude'] = $this->GetValue('Altitude');
        }
        if ($this->ReadPropertyBoolean('ShowSpeed')) {
            $result['speed'] = $this->GetValue('Speed');
        }
        if ($this->ReadPropertyBoolean('ShowCourse')) {
            $result['course'] = $this->GetValue('Course');
        }
        if ($this->ReadPropertyBoolean('ShowAddress')) {
            $result['address'] = $this->GetValue('Address');
        }
        if ($this->ReadPropertyBoolean('ShowLastUpdate')) {
            $result['lastUpdate'] = $this->GetValue('LastUpdate');
        }

        return $result;
    }

    private function UpdateDeviceData(array $device, array $position, array $geofenceMap = []): void
    {
        $this->SendDebug('UpdateDeviceData', 'Device: ' . json_encode($device), 0);
        $this->SendDebug('UpdateDeviceData', 'Position: ' . json_encode($position), 0);

        if ($this->ReadPropertyBoolean('ShowStatus') && isset($device['status'])) {
            $this->SetValue('Status', $device['status']);
        }

        if (!empty($position)) {
            if ($this->ReadPropertyBoolean('ShowLatitude') && isset($position['latitude'])) {
                $this->SetValue('Latitude', (float)$position['latitude']);
            }

            if ($this->ReadPropertyBoolean('ShowLongitude') && isset($position['longitude'])) {
                $this->SetValue('Longitude', (float)$position['longitude']);
            }

            if ($this->ReadPropertyBoolean('ShowAltitude') && isset($position['altitude'])) {
                $this->SetValue('Altitude', (float)$position['altitude']);
            }

            if ($this->ReadPropertyBoolean('ShowSpeed') && isset($position['speed'])) {
                $speedKmh = (float)$position['speed'] * 1.852;
                $this->SetValue('Speed', round($speedKmh, 1));
            }

            if ($this->ReadPropertyBoolean('ShowCourse') && isset($position['course'])) {
                $this->SetValue('Course', (float)$position['course']);
            }

            if ($this->ReadPropertyBoolean('ShowAddress') && isset($position['address'])) {
                $this->SetValue('Address', (string)$position['address']);
            }

            if ($this->ReadPropertyBoolean('ShowAccuracy') && isset($position['accuracy'])) {
                $this->SetValue('Accuracy', (float)$position['accuracy']);
            }

            if ($this->ReadPropertyBoolean('ShowLastUpdate')) {
                $fixTime = $position['fixTime'] ?? $position['deviceTime'] ?? null;
                if ($fixTime !== null) {
                    $timestamp = strtotime($fixTime);
                    if ($timestamp !== false) {
                        $this->SetValue('LastUpdate', $timestamp);
                    }
                }
            }

            if ($this->ReadPropertyBoolean('ShowValid') && isset($position['valid'])) {
                $this->SetValue('Valid', (bool)$position['valid']);
            }

            if ($this->ReadPropertyBoolean('ShowProtocol') && isset($position['protocol'])) {
                $this->SetValue('Protocol', (string)$position['protocol']);
            }

            if (isset($position['geofenceIds'])) {
                $geofenceIds = $position['geofenceIds'];
                $hasGeofences = is_array($geofenceIds) && count($geofenceIds) > 0;

                if ($this->ReadPropertyBoolean('ShowGeofence')) {
                    if ($hasGeofences) {
                        $geofenceNames = [];
                        foreach ($geofenceIds as $geoId) {
                            if (isset($geofenceMap[$geoId]) && $geofenceMap[$geoId] !== '') {
                                $geofenceNames[] = $geofenceMap[$geoId];
                            } else {
                                $geofenceNames[] = (string)$geoId;
                            }
                        }
                        $this->SetValue('Geofence', implode(', ', $geofenceNames));
                    } else {
                        $this->SetValue('Geofence', '');
                    }
                }

                if ($this->ReadPropertyBoolean('ShowGeofenceIds')) {
                    if ($hasGeofences) {
                        $this->SetValue('GeofenceIds', implode(', ', $geofenceIds));
                    } else {
                        $this->SetValue('GeofenceIds', '');
                    }
                }
            }

            if ($this->ReadPropertyBoolean('ShowDeviceTime') && isset($position['deviceTime'])) {
                $timestamp = strtotime($position['deviceTime']);
                if ($timestamp !== false) {
                    $this->SetValue('DeviceTime', $timestamp);
                }
            }

            if ($this->ReadPropertyBoolean('ShowServerTime') && isset($position['serverTime'])) {
                $timestamp = strtotime($position['serverTime']);
                if ($timestamp !== false) {
                    $this->SetValue('ServerTime', $timestamp);
                }
            }

            $attributes = $position['attributes'] ?? [];
            $this->WriteAttributeString('RawAttributes', json_encode($attributes));

            if ($this->ReadPropertyBoolean('ShowSatellites')) {
                $sats = $attributes['sat'] ?? ($attributes['satellites'] ?? null);
                if ($sats !== null) {
                    $this->SetValue('Satellites', (int)$sats);
                }
            }

            if ($this->ReadPropertyBoolean('ShowHDOP')) {
                $hdop = $attributes['hdop'] ?? null;
                if ($hdop !== null) {
                    $this->SetValue('HDOP', (float)$hdop);
                }
            }

            if ($this->ReadPropertyBoolean('ShowBattery')) {
                $battery = $attributes['batteryLevel'] ?? null;
                if ($battery !== null && $battery >= 0 && $battery <= 100) {
                    $this->SetValue('Battery', (int)$battery);
                }
            }

            if ($this->ReadPropertyBoolean('ShowCharge') && isset($attributes['charge'])) {
                $this->SetValue('Charge', (bool)$attributes['charge']);
            }

            if ($this->ReadPropertyBoolean('ShowPower')) {
                $power = $attributes['power'] ?? null;
                if ($power !== null) {
                    $this->SetValue('Power', (float)$power);
                }
            }

            if ($this->ReadPropertyBoolean('ShowMotion') && isset($attributes['motion'])) {
                $this->SetValue('Motion', (bool)$attributes['motion']);
            }

            if ($this->ReadPropertyBoolean('ShowIgnition') && isset($attributes['ignition'])) {
                $this->SetValue('Ignition', (bool)$attributes['ignition']);
            }

            if ($this->ReadPropertyBoolean('ShowAlarm') && isset($attributes['alarm'])) {
                $this->SetValue('Alarm', (string)$attributes['alarm']);
            }

            if ($this->ReadPropertyBoolean('ShowTotalDistance') && isset($attributes['totalDistance'])) {
                $distanceKm = (float)$attributes['totalDistance'] / 1000;
                $this->SetValue('TotalDistance', round($distanceKm, 2));
            }

            if ($this->ReadPropertyBoolean('ShowOdometer') && isset($attributes['odometer'])) {
                $odometerKm = (float)$attributes['odometer'] / 1000;
                $this->SetValue('Odometer', round($odometerKm, 2));
            }

            if ($this->ReadPropertyBoolean('ShowDistance') && isset($attributes['distance'])) {
                $distanceKm = (float)$attributes['distance'] / 1000;
                $this->SetValue('Distance', round($distanceKm, 2));
            }

            if ($this->ReadPropertyBoolean('ShowHours') && isset($attributes['hours'])) {
                $hours = (float)$attributes['hours'] / 3600000;
                $this->SetValue('Hours', round($hours, 1));
            }

            if ($this->ReadPropertyBoolean('ShowBatteryVoltage')) {
                $batteryV = $attributes['battery'] ?? null;
                if ($batteryV !== null && is_numeric($batteryV) && $batteryV < 50) {
                    $this->SetValue('BatteryVoltage', round((float)$batteryV, 2));
                }
            }

            if ($this->ReadPropertyBoolean('ShowActivity') && isset($attributes['activity'])) {
                $this->SetValue('Activity', (string)$attributes['activity']);
            }

            if ($this->ReadPropertyBoolean('ShowRSSI')) {
                $rssi = $attributes['rssi'] ?? null;
                if ($rssi !== null) {
                    $this->SetValue('RSSI', (int)$rssi);
                }
            }

            if ($this->ReadPropertyBoolean('ShowFuel')) {
                $fuel = $attributes['fuel'] ?? ($attributes['fuelLevel'] ?? null);
                if ($fuel !== null) {
                    $this->SetValue('Fuel', (float)$fuel);
                }
            }

            if (isset($position['id'])) {
                $this->WriteAttributeInteger('LastPositionID', (int)$position['id']);
            }
        }

        if ($this->ReadPropertyBoolean('ShowCategory') && isset($device['category'])) {
            $this->SetValue('Category', (string)$device['category']);
        }

        if ($this->ReadPropertyBoolean('ShowModel') && isset($device['model'])) {
            $this->SetValue('Model', (string)$device['model']);
        }

        if ($this->ReadPropertyBoolean('ShowPhone') && isset($device['phone'])) {
            $this->SetValue('Phone', (string)$device['phone']);
        }

        if ($this->ReadPropertyBoolean('ShowContact') && isset($device['contact'])) {
            $this->SetValue('Contact', (string)$device['contact']);
        }

        if ($this->ReadPropertyBoolean('ShowDisabled') && isset($device['disabled'])) {
            $this->SetValue('Disabled', (bool)$device['disabled']);
        }
    }

    public function GetRawAttributes(): array
    {
        $raw = $this->ReadAttributeString('RawAttributes');
        return json_decode($raw, true) ?: [];
    }
}
