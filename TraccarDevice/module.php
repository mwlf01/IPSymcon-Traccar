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

        $this->RegisterProfiles();

        $pos = 1;
        $this->MaintainVariable('Status', $this->Translate('Status'), VARIABLETYPE_STRING, '', $pos++, $this->ReadPropertyBoolean('ShowStatus'));
        $this->MaintainVariable('Latitude', $this->Translate('Latitude'), VARIABLETYPE_FLOAT, 'TRACCAR.Coordinate', $pos++, $this->ReadPropertyBoolean('ShowLatitude'));
        $this->MaintainVariable('Longitude', $this->Translate('Longitude'), VARIABLETYPE_FLOAT, 'TRACCAR.Coordinate', $pos++, $this->ReadPropertyBoolean('ShowLongitude'));
        $this->MaintainVariable('Altitude', $this->Translate('Altitude'), VARIABLETYPE_FLOAT, 'TRACCAR.Altitude', $pos++, $this->ReadPropertyBoolean('ShowAltitude'));
        $this->MaintainVariable('Speed', $this->Translate('Speed'), VARIABLETYPE_FLOAT, 'TRACCAR.Speed', $pos++, $this->ReadPropertyBoolean('ShowSpeed'));
        $this->MaintainVariable('Course', $this->Translate('Course'), VARIABLETYPE_FLOAT, 'TRACCAR.Course', $pos++, $this->ReadPropertyBoolean('ShowCourse'));
        $this->MaintainVariable('Address', $this->Translate('Address'), VARIABLETYPE_STRING, '', $pos++, $this->ReadPropertyBoolean('ShowAddress'));
        $this->MaintainVariable('Accuracy', $this->Translate('Accuracy'), VARIABLETYPE_FLOAT, 'TRACCAR.Accuracy', $pos++, $this->ReadPropertyBoolean('ShowAccuracy'));
        $this->MaintainVariable('LastUpdate', $this->Translate('Last Update'), VARIABLETYPE_INTEGER, '~UnixTimestamp', $pos++, $this->ReadPropertyBoolean('ShowLastUpdate'));
        $this->MaintainVariable('Valid', $this->Translate('GPS Valid'), VARIABLETYPE_BOOLEAN, '', $pos++, $this->ReadPropertyBoolean('ShowValid'));
        $this->MaintainVariable('Protocol', $this->Translate('Protocol'), VARIABLETYPE_STRING, '', $pos++, $this->ReadPropertyBoolean('ShowProtocol'));
        $this->MaintainVariable('Satellites', $this->Translate('Satellites'), VARIABLETYPE_INTEGER, '', $pos++, $this->ReadPropertyBoolean('ShowSatellites'));
        $this->MaintainVariable('HDOP', $this->Translate('HDOP'), VARIABLETYPE_FLOAT, 'TRACCAR.HDOP', $pos++, $this->ReadPropertyBoolean('ShowHDOP'));
        $this->MaintainVariable('Battery', $this->Translate('Battery'), VARIABLETYPE_INTEGER, '~Battery.100', $pos++, $this->ReadPropertyBoolean('ShowBattery'));
        $this->MaintainVariable('Charge', $this->Translate('Charging'), VARIABLETYPE_BOOLEAN, '', $pos++, $this->ReadPropertyBoolean('ShowCharge'));
        $this->MaintainVariable('Power', $this->Translate('External Power'), VARIABLETYPE_FLOAT, 'TRACCAR.Voltage', $pos++, $this->ReadPropertyBoolean('ShowPower'));
        $this->MaintainVariable('Motion', $this->Translate('Motion'), VARIABLETYPE_BOOLEAN, '~Motion', $pos++, $this->ReadPropertyBoolean('ShowMotion'));
        $this->MaintainVariable('Ignition', $this->Translate('Ignition'), VARIABLETYPE_BOOLEAN, '~Switch', $pos++, $this->ReadPropertyBoolean('ShowIgnition'));
        $this->MaintainVariable('Alarm', $this->Translate('Alarm'), VARIABLETYPE_STRING, '', $pos++, $this->ReadPropertyBoolean('ShowAlarm'));
        $this->MaintainVariable('TotalDistance', $this->Translate('Total Distance'), VARIABLETYPE_FLOAT, 'TRACCAR.Distance', $pos++, $this->ReadPropertyBoolean('ShowTotalDistance'));
        $this->MaintainVariable('Odometer', $this->Translate('Odometer'), VARIABLETYPE_FLOAT, 'TRACCAR.Distance', $pos++, $this->ReadPropertyBoolean('ShowOdometer'));
        $this->MaintainVariable('Distance', $this->Translate('Trip Distance'), VARIABLETYPE_FLOAT, 'TRACCAR.Distance', $pos++, $this->ReadPropertyBoolean('ShowDistance'));
        $this->MaintainVariable('Hours', $this->Translate('Engine Hours'), VARIABLETYPE_FLOAT, 'TRACCAR.Hours', $pos++, $this->ReadPropertyBoolean('ShowHours'));
        $this->MaintainVariable('BatteryVoltage', $this->Translate('Battery Voltage'), VARIABLETYPE_FLOAT, 'TRACCAR.Voltage', $pos++, $this->ReadPropertyBoolean('ShowBatteryVoltage'));
        $this->MaintainVariable('Activity', $this->Translate('Activity'), VARIABLETYPE_STRING, '', $pos++, $this->ReadPropertyBoolean('ShowActivity'));
        $this->MaintainVariable('Category', $this->Translate('Category'), VARIABLETYPE_STRING, '', $pos++, $this->ReadPropertyBoolean('ShowCategory'));
        $this->MaintainVariable('Model', $this->Translate('Model'), VARIABLETYPE_STRING, '', $pos++, $this->ReadPropertyBoolean('ShowModel'));
        $this->MaintainVariable('Phone', $this->Translate('Phone'), VARIABLETYPE_STRING, '', $pos++, $this->ReadPropertyBoolean('ShowPhone'));
        $this->MaintainVariable('RSSI', $this->Translate('Signal Strength'), VARIABLETYPE_INTEGER, 'TRACCAR.RSSI', $pos++, $this->ReadPropertyBoolean('ShowRSSI'));
        $this->MaintainVariable('Fuel', $this->Translate('Fuel Level'), VARIABLETYPE_FLOAT, 'TRACCAR.Percent', $pos++, $this->ReadPropertyBoolean('ShowFuel'));

        $this->SetReceiveDataFilter('.*"deviceId":' . $deviceId . '.*');

        $this->SetStatus(self::STATUS_ACTIVE);

        $this->RequestUpdate();
    }

    private function RegisterProfiles(): void
    {
        if (!IPS_VariableProfileExists('TRACCAR.Coordinate')) {
            IPS_CreateVariableProfile('TRACCAR.Coordinate', VARIABLETYPE_FLOAT);
            IPS_SetVariableProfileDigits('TRACCAR.Coordinate', 6);
            IPS_SetVariableProfileText('TRACCAR.Coordinate', '', '°');
        }

        if (!IPS_VariableProfileExists('TRACCAR.Altitude')) {
            IPS_CreateVariableProfile('TRACCAR.Altitude', VARIABLETYPE_FLOAT);
            IPS_SetVariableProfileDigits('TRACCAR.Altitude', 1);
            IPS_SetVariableProfileText('TRACCAR.Altitude', '', ' m');
        }

        if (!IPS_VariableProfileExists('TRACCAR.Speed')) {
            IPS_CreateVariableProfile('TRACCAR.Speed', VARIABLETYPE_FLOAT);
            IPS_SetVariableProfileDigits('TRACCAR.Speed', 1);
            IPS_SetVariableProfileText('TRACCAR.Speed', '', ' km/h');
        }

        if (!IPS_VariableProfileExists('TRACCAR.Course')) {
            IPS_CreateVariableProfile('TRACCAR.Course', VARIABLETYPE_FLOAT);
            IPS_SetVariableProfileDigits('TRACCAR.Course', 1);
            IPS_SetVariableProfileText('TRACCAR.Course', '', '°');
            IPS_SetVariableProfileValues('TRACCAR.Course', 0, 360, 0);
        }

        if (!IPS_VariableProfileExists('TRACCAR.Accuracy')) {
            IPS_CreateVariableProfile('TRACCAR.Accuracy', VARIABLETYPE_FLOAT);
            IPS_SetVariableProfileDigits('TRACCAR.Accuracy', 1);
            IPS_SetVariableProfileText('TRACCAR.Accuracy', '', ' m');
        }

        if (!IPS_VariableProfileExists('TRACCAR.Distance')) {
            IPS_CreateVariableProfile('TRACCAR.Distance', VARIABLETYPE_FLOAT);
            IPS_SetVariableProfileDigits('TRACCAR.Distance', 2);
            IPS_SetVariableProfileText('TRACCAR.Distance', '', ' km');
        }

        if (!IPS_VariableProfileExists('TRACCAR.HDOP')) {
            IPS_CreateVariableProfile('TRACCAR.HDOP', VARIABLETYPE_FLOAT);
            IPS_SetVariableProfileDigits('TRACCAR.HDOP', 1);
        }

        if (!IPS_VariableProfileExists('TRACCAR.Voltage')) {
            IPS_CreateVariableProfile('TRACCAR.Voltage', VARIABLETYPE_FLOAT);
            IPS_SetVariableProfileDigits('TRACCAR.Voltage', 2);
            IPS_SetVariableProfileText('TRACCAR.Voltage', '', ' V');
        }

        if (!IPS_VariableProfileExists('TRACCAR.Hours')) {
            IPS_CreateVariableProfile('TRACCAR.Hours', VARIABLETYPE_FLOAT);
            IPS_SetVariableProfileDigits('TRACCAR.Hours', 1);
            IPS_SetVariableProfileText('TRACCAR.Hours', '', ' h');
        }

        if (!IPS_VariableProfileExists('TRACCAR.RSSI')) {
            IPS_CreateVariableProfile('TRACCAR.RSSI', VARIABLETYPE_INTEGER);
            IPS_SetVariableProfileText('TRACCAR.RSSI', '', ' dBm');
            IPS_SetVariableProfileValues('TRACCAR.RSSI', -120, 0, 1);
        }

        if (!IPS_VariableProfileExists('TRACCAR.Percent')) {
            IPS_CreateVariableProfile('TRACCAR.Percent', VARIABLETYPE_FLOAT);
            IPS_SetVariableProfileDigits('TRACCAR.Percent', 1);
            IPS_SetVariableProfileText('TRACCAR.Percent', '', ' %');
            IPS_SetVariableProfileValues('TRACCAR.Percent', 0, 100, 0);
        }
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
                        ['type' => 'CheckBox', 'name' => 'ShowStatus', 'caption' => 'Status'],
                        ['type' => 'CheckBox', 'name' => 'ShowLatitude', 'caption' => 'Latitude'],
                        ['type' => 'CheckBox', 'name' => 'ShowLongitude', 'caption' => 'Longitude'],
                        ['type' => 'CheckBox', 'name' => 'ShowAltitude', 'caption' => 'Altitude'],
                        ['type' => 'CheckBox', 'name' => 'ShowSpeed', 'caption' => 'Speed'],
                        ['type' => 'CheckBox', 'name' => 'ShowCourse', 'caption' => 'Course/Heading'],
                        ['type' => 'CheckBox', 'name' => 'ShowAddress', 'caption' => 'Address'],
                        ['type' => 'CheckBox', 'name' => 'ShowAccuracy', 'caption' => 'GPS Accuracy'],
                        ['type' => 'CheckBox', 'name' => 'ShowLastUpdate', 'caption' => 'Last Update Time'],
                        ['type' => 'CheckBox', 'name' => 'ShowValid', 'caption' => 'GPS Valid'],
                        ['type' => 'CheckBox', 'name' => 'ShowProtocol', 'caption' => 'Protocol'],
                        ['type' => 'CheckBox', 'name' => 'ShowSatellites', 'caption' => 'Satellites'],
                        ['type' => 'CheckBox', 'name' => 'ShowHDOP', 'caption' => 'HDOP']
                    ]
                ],
                [
                    'type' => 'ExpansionPanel',
                    'caption' => 'Device Attributes',
                    'items' => [
                        ['type' => 'CheckBox', 'name' => 'ShowBattery', 'caption' => 'Battery Level (%)'],
                        ['type' => 'CheckBox', 'name' => 'ShowBatteryVoltage', 'caption' => 'Battery Voltage (V)'],
                        ['type' => 'CheckBox', 'name' => 'ShowCharge', 'caption' => 'Charging Status'],
                        ['type' => 'CheckBox', 'name' => 'ShowPower', 'caption' => 'External Power (V)'],
                        ['type' => 'CheckBox', 'name' => 'ShowMotion', 'caption' => 'Motion'],
                        ['type' => 'CheckBox', 'name' => 'ShowIgnition', 'caption' => 'Ignition'],
                        ['type' => 'CheckBox', 'name' => 'ShowAlarm', 'caption' => 'Alarm'],
                        ['type' => 'CheckBox', 'name' => 'ShowActivity', 'caption' => 'Activity'],
                        ['type' => 'CheckBox', 'name' => 'ShowTotalDistance', 'caption' => 'Total Distance'],
                        ['type' => 'CheckBox', 'name' => 'ShowOdometer', 'caption' => 'Odometer'],
                        ['type' => 'CheckBox', 'name' => 'ShowDistance', 'caption' => 'Trip Distance'],
                        ['type' => 'CheckBox', 'name' => 'ShowHours', 'caption' => 'Engine Hours'],
                        ['type' => 'CheckBox', 'name' => 'ShowRSSI', 'caption' => 'Signal Strength (RSSI)'],
                        ['type' => 'CheckBox', 'name' => 'ShowFuel', 'caption' => 'Fuel Level']
                    ]
                ],
                [
                    'type' => 'ExpansionPanel',
                    'caption' => 'Device Info',
                    'items' => [
                        ['type' => 'CheckBox', 'name' => 'ShowCategory', 'caption' => 'Category'],
                        ['type' => 'CheckBox', 'name' => 'ShowModel', 'caption' => 'Model'],
                        ['type' => 'CheckBox', 'name' => 'ShowPhone', 'caption' => 'Phone']
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
                    'caption' => 'Device is active'
                ],
                [
                    'code' => self::STATUS_INACTIVE,
                    'icon' => 'inactive',
                    'caption' => 'No device configured'
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

        $myDeviceId = $this->ReadPropertyInteger('DeviceID');
        $deviceId = $data['deviceId'] ?? ($device['id'] ?? ($position['deviceId'] ?? 0));

        $this->SendDebug('ReceiveData', "Received deviceId: {$deviceId}, myDeviceId: {$myDeviceId}", 0);

        if ($deviceId !== $myDeviceId) {
            return;
        }

        $this->UpdateDeviceData($device, $position);
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

    private function UpdateDeviceData(array $device, array $position): void
    {
        $this->SendDebug('UpdateDeviceData', 'Device: ' . json_encode($device), 0);
        $this->SendDebug('UpdateDeviceData', 'Position: ' . json_encode($position), 0);

        if ($this->ReadPropertyBoolean('ShowStatus') && isset($device['status'])) {
            $status = $this->TranslateStatus($device['status']);
            $this->SetValue('Status', $status);
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
    }

    public function GetRawAttributes(): array
    {
        $raw = $this->ReadAttributeString('RawAttributes');
        return json_decode($raw, true) ?: [];
    }

    private function TranslateStatus(string $status): string
    {
        switch ($status) {
            case 'online':
                return $this->Translate('Online');
            case 'offline':
                return $this->Translate('Offline');
            default:
                return $this->Translate('Unknown');
        }
    }
}
