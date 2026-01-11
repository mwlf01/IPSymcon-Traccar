<?php
declare(strict_types=1);

class TraccarConfigurator extends IPSModule
{
    public function Create()
    {
        parent::Create();

        $this->ConnectParent('{B02E003C-7A70-C422-C417-0E41DC1CE86D}');
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();
    }

    public function GetConfigurationForm(): string
    {
        $devices = $this->GetDevicesFromTraccar();
        $values = $this->GetConfigurationValues($devices);

        return json_encode([
            'elements' => [
                [
                    'type' => 'Label',
                    'caption' => 'This configurator lists all devices from your Traccar server.'
                ],
                [
                    'type' => 'Label',
                    'caption' => 'Click the "+" button to create a device instance.'
                ]
            ],
            'actions' => [
                [
                    'type' => 'Configurator',
                    'name' => 'DeviceList',
                    'caption' => 'Traccar Devices',
                    'rowCount' => 15,
                    'add' => false,
                    'delete' => true,
                    'columns' => [
                        [
                            'caption' => 'Name',
                            'name' => 'name',
                            'width' => '200px'
                        ],
                        [
                            'caption' => 'Unique ID',
                            'name' => 'uniqueId',
                            'width' => '150px'
                        ],
                        [
                            'caption' => 'Status',
                            'name' => 'status',
                            'width' => '100px'
                        ],
                        [
                            'caption' => 'Category',
                            'name' => 'category',
                            'width' => '100px'
                        ],
                        [
                            'caption' => 'Model',
                            'name' => 'model',
                            'width' => '100px'
                        ],
                        [
                            'caption' => 'Last Update',
                            'name' => 'lastUpdate',
                            'width' => '180px'
                        ]
                    ],
                    'values' => $values
                ],
                [
                    'type' => 'Button',
                    'caption' => 'Refresh Device List',
                    'onClick' => 'IPS_RequestAction($id, "RefreshDeviceList", "");'
                ]
            ]
        ]);
    }

    public function RequestAction($Ident, $Value)
    {
        switch ($Ident) {
            case 'RefreshDeviceList':
                $this->ReloadForm();
                break;
        }
    }

    private function GetDevicesFromTraccar(): array
    {
        $data = [
            'DataID' => '{D6BB3A8B-2C57-050E-0D98-7853B4E18BAE}',
            'Buffer' => json_encode([
                'Method' => 'GET',
                'Endpoint' => '/api/devices',
                'Body' => null
            ])
        ];
        $response = $this->SendDataToParent(json_encode($data));

        if ($response === false || $response === '') {
            return [];
        }

        $data = json_decode($response, true);
        if (!is_array($data)) {
            return [];
        }

        return $data;
    }

    private function GetConfigurationValues(array $devices): array
    {
        $values = [];
        $existingInstances = $this->GetExistingInstances();

        foreach ($devices as $device) {
            $deviceId = $device['id'] ?? 0;
            $name = $device['name'] ?? 'Unknown';
            $uniqueId = $device['uniqueId'] ?? '';
            $status = $device['status'] ?? 'unknown';
            $category = $device['category'] ?? '';
            $model = $device['model'] ?? '';
            $lastUpdate = $device['lastUpdate'] ?? '';

            if (!empty($lastUpdate)) {
                $timestamp = strtotime($lastUpdate);
                if ($timestamp !== false) {
                    $lastUpdate = date('Y-m-d H:i:s', $timestamp);
                }
            }

            $instanceId = $existingInstances[$deviceId] ?? 0;

            $value = [
                'name' => $name,
                'uniqueId' => $uniqueId,
                'status' => $this->TranslateStatus($status),
                'category' => $category,
                'model' => $model,
                'lastUpdate' => $lastUpdate,
                'create' => [
                    'moduleID' => '{24B39122-FE4C-99E7-586E-CB0DEE1508AC}',
                    'configuration' => [
                        'DeviceID' => $deviceId,
                        'DeviceName' => $name,
                        'UniqueID' => $uniqueId
                    ]
                ]
            ];

            if ($instanceId > 0) {
                $value['instanceID'] = $instanceId;
            }

            $values[] = $value;
        }

        return $values;
    }

    private function GetExistingInstances(): array
    {
        $instances = [];
        $moduleGUID = '{24B39122-FE4C-99E7-586E-CB0DEE1508AC}';

        foreach (IPS_GetInstanceListByModuleID($moduleGUID) as $instanceId) {
            $deviceId = IPS_GetProperty($instanceId, 'DeviceID');
            if ($deviceId > 0) {
                $instances[$deviceId] = $instanceId;
            }
        }

        return $instances;
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
