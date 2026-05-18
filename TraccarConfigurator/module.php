<?php

/*
 * Traccar Configurator for IP-Symcon
 *
 * SPDX-License-Identifier: EUPL-1.2
 * Copyright (c) 2026 mwlf01
 *
 * Licensed under the EUPL, Version 1.2. See the LICENSE file for the full text.
 */

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
        $parentReady = $this->IsParentActive();
        $devices = $parentReady ? $this->GetDevicesFromTraccar() : [];
        $values = $this->GetConfigurationValues($devices);

        $elements = [
            [
                'type' => 'Label',
                'caption' => 'This configurator lists all devices from your Traccar server.'
            ],
            [
                'type' => 'Label',
                'caption' => 'Click the "+" button to create a device instance.'
            ]
        ];

        if (!$parentReady) {
            $elements[] = [
                'type' => 'Label',
                'caption' => 'The connected Traccar Splitter is not active. Check its configuration and connection status.'
            ];
        }

        return json_encode([
            'elements' => $elements,
            'actions' => [
                [
                    'type' => 'Configurator',
                    'name' => 'DeviceList',
                    'caption' => 'Traccar Devices',
                    'rowCount' => 15,
                    'add' => false,
                    'delete' => true,
                    'columns' => [
                        ['caption' => 'Name', 'name' => 'name', 'width' => '300px'],
                        ['caption' => 'Unique ID', 'name' => 'uniqueId', 'width' => 'auto'],
                        ['caption' => 'Status', 'name' => 'status', 'width' => '150px'],
                        ['caption' => 'Category', 'name' => 'category', 'width' => '150px'],
                        ['caption' => 'Model', 'name' => 'model', 'width' => '150px'],
                        ['caption' => 'Last Update', 'name' => 'lastUpdate', 'width' => '180px']
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

    private function IsParentActive(): bool
    {
        $parentId = IPS_GetInstance($this->InstanceID)['ConnectionID'];
        if ($parentId === 0) {
            return false;
        }
        return IPS_GetInstance($parentId)['InstanceStatus'] === 102;
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

        $decoded = json_decode((string)$response, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function GetConfigurationValues(array $devices): array
    {
        $values = [];
        $existingInstances = $this->GetExistingInstances();
        $matchedInstanceIds = [];

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
                $matchedInstanceIds[] = $instanceId;
            }

            $values[] = $value;
        }

        foreach ($existingInstances as $deviceId => $instanceId) {
            if (in_array($instanceId, $matchedInstanceIds, true)) {
                continue;
            }

            $deviceName = $this->ReadInstanceProperty($instanceId, 'DeviceName');
            $uniqueId = $this->ReadInstanceProperty($instanceId, 'UniqueID');

            $values[] = [
                'name' => $deviceName !== '' ? $deviceName : $this->Translate('Unknown'),
                'uniqueId' => $uniqueId,
                'status' => $this->Translate('Not found in Traccar'),
                'category' => '',
                'model' => '',
                'lastUpdate' => '',
                'instanceID' => $instanceId
            ];
        }

        return $values;
    }

    private function GetExistingInstances(): array
    {
        $instances = [];
        $moduleGUID = '{24B39122-FE4C-99E7-586E-CB0DEE1508AC}';
        $mySplitterId = IPS_GetInstance($this->InstanceID)['ConnectionID'];

        if ($mySplitterId === 0) {
            return $instances;
        }

        foreach (IPS_GetInstanceListByModuleID($moduleGUID) as $instanceId) {
            $instanceSplitterId = IPS_GetInstance($instanceId)['ConnectionID'];
            if ($instanceSplitterId !== $mySplitterId) {
                continue;
            }

            $deviceId = (int)$this->ReadInstanceProperty($instanceId, 'DeviceID');
            if ($deviceId > 0) {
                $instances[$deviceId] = $instanceId;
            }
        }

        return $instances;
    }

    private function ReadInstanceProperty(int $instanceId, string $property)
    {
        if (!IPS_InstanceExists($instanceId)) {
            return '';
        }
        return @IPS_GetProperty($instanceId, $property);
    }

    private function TranslateStatus(string $status): string
    {
        switch (strtolower($status)) {
            case 'online':
                return $this->Translate('Online');
            case 'offline':
                return $this->Translate('Offline');
            default:
                return $this->Translate('Unknown');
        }
    }
}
