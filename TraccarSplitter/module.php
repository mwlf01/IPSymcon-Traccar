<?php

/*
 * Traccar Splitter for IP-Symcon
 *
 * SPDX-License-Identifier: EUPL-1.2
 * Copyright (c) 2026 mwlf01
 *
 * Licensed under the EUPL, Version 1.2. See the LICENSE file for the full text.
 */

declare(strict_types=1);

class TraccarSplitter extends IPSModule
{
    private const STATUS_ACTIVE = 102;
    private const STATUS_NO_CONNECTION = 201;
    private const STATUS_INVALID_TOKEN = 202;
    private const STATUS_CONFIGURATION_ERROR = 203;

    private const HTTP_TIMEOUT = 15;
    private const CONNECT_TIMEOUT = 5;
    private const GEOFENCE_CACHE_TTL = 300;

    public function Create()
    {
        parent::Create();

        $this->RegisterPropertyString('Host', '');
        $this->RegisterPropertyInteger('Port', 443);
        $this->RegisterPropertyBoolean('UseHTTPS', true);
        $this->RegisterPropertyBoolean('VerifySSL', true);
        $this->RegisterPropertyString('Token', '');
        $this->RegisterPropertyInteger('UpdateInterval', 30);

        $this->RegisterAttributeString('SessionCookie', '');
        $this->RegisterAttributeString('GeofenceCache', '{}');
        $this->RegisterAttributeInteger('GeofenceCacheTime', 0);

        $this->RegisterTimer('UpdateTimer', 0, 'TRACCAR_UpdateDevices($_IPS[\'TARGET\']);');
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();

        $this->SetTimerInterval('UpdateTimer', 0);

        $host = $this->ReadPropertyString('Host');
        $token = $this->ReadPropertyString('Token');

        if (empty($host) || empty($token)) {
            $this->SetStatus(self::STATUS_CONFIGURATION_ERROR);
            return;
        }

        if ($this->CreateSession()) {
            $this->SetStatus(self::STATUS_ACTIVE);
            $interval = $this->ReadPropertyInteger('UpdateInterval');
            $this->SetTimerInterval('UpdateTimer', $interval * 1000);
        }
    }

    public function GetConfigurationForm(): string
    {
        return json_encode([
            'elements' => [
                [
                    'type' => 'ExpansionPanel',
                    'caption' => 'Server Settings',
                    'expanded' => true,
                    'items' => [
                        ['type' => 'ValidationTextBox', 'name' => 'Host', 'caption' => 'Traccar Server Host', 'width' => '400px'],
                        ['type' => 'NumberSpinner', 'name' => 'Port', 'caption' => 'Port', 'minimum' => 1, 'maximum' => 65535],
                        ['type' => 'CheckBox', 'name' => 'UseHTTPS', 'caption' => 'Use HTTPS'],
                        ['type' => 'CheckBox', 'name' => 'VerifySSL', 'caption' => 'Verify TLS Certificate']
                    ]
                ],
                [
                    'type' => 'ExpansionPanel',
                    'caption' => 'Authentication',
                    'expanded' => true,
                    'items' => [
                        ['type' => 'PasswordTextBox', 'name' => 'Token', 'caption' => 'API Token', 'width' => '400px'],
                        ['type' => 'Label', 'caption' => 'Generate an API token in Traccar: Settings → Account → Token']
                    ]
                ],
                [
                    'type' => 'ExpansionPanel',
                    'caption' => 'Update Settings',
                    'items' => [
                        ['type' => 'NumberSpinner', 'name' => 'UpdateInterval', 'caption' => 'Update Interval', 'minimum' => 5, 'maximum' => 3600, 'suffix' => ' s']
                    ]
                ]
            ],
            'actions' => [
                [
                    'type' => 'Button',
                    'caption' => 'Test Connection',
                    'onClick' => 'if (TRACCAR_TestConnection($id)) { echo "Connection successful!"; } else { echo "Connection failed!"; }'
                ],
                [
                    'type' => 'Button',
                    'caption' => 'Refresh Session',
                    'onClick' => 'if (TRACCAR_RefreshSession($id)) { echo "Session refreshed!"; } else { echo "Session refresh failed!"; }'
                ],
                [
                    'type' => 'Button',
                    'caption' => 'Update All Devices',
                    'onClick' => 'TRACCAR_UpdateDevices($id);'
                ]
            ],
            'status' => [
                ['code' => self::STATUS_ACTIVE, 'icon' => 'active', 'caption' => 'Connected to Traccar server'],
                ['code' => self::STATUS_NO_CONNECTION, 'icon' => 'error', 'caption' => 'Cannot connect to Traccar server'],
                ['code' => self::STATUS_INVALID_TOKEN, 'icon' => 'error', 'caption' => 'Invalid API token'],
                ['code' => self::STATUS_CONFIGURATION_ERROR, 'icon' => 'error', 'caption' => 'Configuration incomplete']
            ]
        ]);
    }

    public function TestConnection(): bool
    {
        if (!$this->CreateSession()) {
            return false;
        }
        return is_array($this->APIRequest('GET', '/api/server'));
    }

    public function RefreshSession(): bool
    {
        return $this->CreateSession();
    }

    public function GetDevices(): array
    {
        $response = $this->APIRequest('GET', '/api/devices');
        return is_array($response) ? $response : [];
    }

    public function GetPositions(): array
    {
        $response = $this->APIRequest('GET', '/api/positions');
        return is_array($response) ? $response : [];
    }

    public function GetDevicePosition(int $deviceId): array
    {
        $positions = $this->GetPositions();
        foreach ($positions as $position) {
            if (isset($position['deviceId']) && $position['deviceId'] === $deviceId) {
                return $position;
            }
        }
        return [];
    }

    public function GetGeofences(): array
    {
        $response = $this->APIRequest('GET', '/api/geofences');
        return is_array($response) ? $response : [];
    }

    public function GetServerInfo(): array
    {
        $response = $this->APIRequest('GET', '/api/server');
        return is_array($response) ? $response : [];
    }

    public function UpdateDevices(): void
    {
        $this->SendDebug('UpdateDevices', 'Updating all device instances', 0);

        $devices = $this->GetDevices();
        $positions = $this->GetPositions();
        $geofenceMap = $this->GetGeofenceMap();

        $positionMap = [];
        foreach ($positions as $position) {
            if (isset($position['deviceId'])) {
                $positionMap[$position['deviceId']] = $position;
            }
        }

        foreach ($devices as $device) {
            $deviceId = $device['id'] ?? 0;
            if ($deviceId === 0) {
                continue;
            }

            $position = $positionMap[$deviceId] ?? [];
            $this->ProcessDeviceUpdate($device, $position, $geofenceMap);
        }
    }

    public function ForwardData($JSONString): string
    {
        $data = json_decode($JSONString, true);

        if (isset($data['Buffer'])) {
            $buffer = json_decode($data['Buffer'], true);
        } else {
            $buffer = $data;
        }

        if (!is_array($buffer) || !isset($buffer['Method'], $buffer['Endpoint'])) {
            return '';
        }

        $response = $this->APIRequest($buffer['Method'], $buffer['Endpoint'], $buffer['Body'] ?? null);
        if ($response === false) {
            return '';
        }

        return json_encode($response);
    }

    private function GetGeofenceMap(): array
    {
        $cacheTime = $this->ReadAttributeInteger('GeofenceCacheTime');
        $now = time();

        if ($cacheTime > 0 && ($now - $cacheTime) < self::GEOFENCE_CACHE_TTL) {
            $cached = json_decode($this->ReadAttributeString('GeofenceCache'), true);
            if (is_array($cached)) {
                return $cached;
            }
        }

        $geofences = $this->GetGeofences();
        $map = [];
        foreach ($geofences as $geofence) {
            if (isset($geofence['id'])) {
                $map[$geofence['id']] = $geofence['name'] ?? '';
            }
        }

        $this->WriteAttributeString('GeofenceCache', json_encode($map));
        $this->WriteAttributeInteger('GeofenceCacheTime', $now);

        return $map;
    }

    private function ProcessDeviceUpdate(array $device, array $position, array $geofenceMap = []): void
    {
        $deviceId = $device['id'] ?? ($position['deviceId'] ?? 0);

        $this->SendDataToChildren(json_encode([
            'DataID' => '{595D0659-EEE7-C3A6-7F61-2F145327A6AE}',
            'deviceId' => $deviceId,
            'device' => $device,
            'position' => $position,
            'geofenceMap' => $geofenceMap
        ]));
    }

    private function CreateSession(): bool
    {
        $host = $this->ReadPropertyString('Host');
        $token = $this->ReadPropertyString('Token');
        $verifySSL = $this->ReadPropertyBoolean('VerifySSL');

        if (empty($host) || empty($token)) {
            return false;
        }

        // Traccar accepts the token-based session login only as a GET request
        // with the token as a query parameter; a POST body is rejected (HTTP 400).
        $url = $this->BuildURL('/api/session') . '?token=' . urlencode($token);
        $this->SendDebug('CreateSession', 'GET /api/session', 0);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::HTTP_TIMEOUT);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIMEOUT);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verifySSL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $verifySSL ? 2 : 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error !== '') {
            $this->SendDebug('CreateSession', "cURL error: {$error}", 0);
            $this->SetStatus(self::STATUS_NO_CONNECTION);
            return false;
        }

        $this->SendDebug('CreateSession', "HTTP {$httpCode}", 0);

        if ($httpCode === 401 || $httpCode === 403) {
            $this->SetStatus(self::STATUS_INVALID_TOKEN);
            return false;
        }

        if ($httpCode >= 400) {
            $this->SetStatus(self::STATUS_NO_CONNECTION);
            return false;
        }

        $headers = substr((string)$response, 0, $headerSize);
        $cookies = [];
        if (preg_match_all('/Set-Cookie:\s*([^;\r\n]+)/i', $headers, $matches)) {
            $cookies = $matches[1];
        }

        if (empty($cookies)) {
            $this->SendDebug('CreateSession', 'No session cookie received', 0);
            $this->SetStatus(self::STATUS_NO_CONNECTION);
            return false;
        }

        $this->WriteAttributeString('SessionCookie', implode('; ', $cookies));
        return true;
    }

    private function BuildURL(string $endpoint): string
    {
        $host = $this->ReadPropertyString('Host');
        $port = $this->ReadPropertyInteger('Port');
        $useHTTPS = $this->ReadPropertyBoolean('UseHTTPS');

        $protocol = $useHTTPS ? 'https' : 'http';
        $defaultPort = $useHTTPS ? 443 : 80;

        if ($port === $defaultPort) {
            return "{$protocol}://{$host}{$endpoint}";
        }
        return "{$protocol}://{$host}:{$port}{$endpoint}";
    }

    private function APIRequest(string $method, string $endpoint, ?array $body = null, bool $allowRetry = true)
    {
        $verifySSL = $this->ReadPropertyBoolean('VerifySSL');
        $sessionCookie = $this->ReadAttributeString('SessionCookie');

        if (empty($sessionCookie)) {
            if (!$allowRetry || !$this->CreateSession()) {
                return false;
            }
            $sessionCookie = $this->ReadAttributeString('SessionCookie');
        }

        $url = $this->BuildURL($endpoint);
        $this->SendDebug('APIRequest', "{$method} {$endpoint}", 0);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::HTTP_TIMEOUT);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIMEOUT);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verifySSL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $verifySSL ? 2 : 0);
        curl_setopt($ch, CURLOPT_COOKIE, $sessionCookie);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json'
        ]);

        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                if ($body !== null) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
                }
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                if ($body !== null) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
                }
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error !== '') {
            $this->SendDebug('APIRequest', "cURL error: {$error}", 0);
            $this->SetStatus(self::STATUS_NO_CONNECTION);
            return false;
        }

        $this->SendDebug('APIRequest', "HTTP {$httpCode} (" . strlen((string)$response) . ' bytes)', 0);

        if ($httpCode === 401 && $allowRetry) {
            $this->SendDebug('APIRequest', 'Session expired, refreshing', 0);
            $this->WriteAttributeString('SessionCookie', '');
            if ($this->CreateSession()) {
                return $this->APIRequest($method, $endpoint, $body, false);
            }
            return false;
        }

        if ($httpCode === 401 || $httpCode === 403) {
            $this->SetStatus(self::STATUS_INVALID_TOKEN);
            return false;
        }

        if ($httpCode >= 400) {
            $this->SetStatus(self::STATUS_NO_CONNECTION);
            return false;
        }

        if ($this->GetStatus() !== self::STATUS_ACTIVE) {
            $this->SetStatus(self::STATUS_ACTIVE);
        }

        $decoded = json_decode((string)$response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $response;
        }

        return $decoded;
    }
}
