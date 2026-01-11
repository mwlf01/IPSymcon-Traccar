<?php
declare(strict_types=1);

class TraccarSplitter extends IPSModule
{
    private const STATUS_ACTIVE = 102;
    private const STATUS_NO_CONNECTION = 201;
    private const STATUS_INVALID_TOKEN = 202;
    private const STATUS_CONFIGURATION_ERROR = 203;

    public function Create()
    {
        parent::Create();

        $this->RegisterPropertyString('Host', '');
        $this->RegisterPropertyInteger('Port', 443);
        $this->RegisterPropertyBoolean('UseHTTPS', true);
        $this->RegisterPropertyString('Token', '');
        $this->RegisterPropertyInteger('UpdateInterval', 30);

        $this->RegisterAttributeString('SessionCookie', '');

        $this->RegisterTimer('UpdateTimer', 0, 'TRACCAR_UpdateDevices($_IPS[\'TARGET\']);');
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();

        $host = $this->ReadPropertyString('Host');
        $token = $this->ReadPropertyString('Token');

        if (empty($host)) {
            $this->SetStatus(self::STATUS_CONFIGURATION_ERROR);
            $this->SetTimerInterval('UpdateTimer', 0);
            return;
        }

        if (empty($token)) {
            $this->SetStatus(self::STATUS_CONFIGURATION_ERROR);
            $this->SetTimerInterval('UpdateTimer', 0);
            return;
        }

        if ($this->CreateSession()) {
            $this->SetStatus(self::STATUS_ACTIVE);
            $interval = $this->ReadPropertyInteger('UpdateInterval');
            $this->SetTimerInterval('UpdateTimer', $interval * 1000);
        } else {
            $this->SetTimerInterval('UpdateTimer', 0);
        }
    }

    private function CreateSession(): bool
    {
        $host = $this->ReadPropertyString('Host');
        $port = $this->ReadPropertyInteger('Port');
        $useHTTPS = $this->ReadPropertyBoolean('UseHTTPS');
        $token = $this->ReadPropertyString('Token');

        $protocol = $useHTTPS ? 'https' : 'http';
        if (($useHTTPS && $port === 443) || (!$useHTTPS && $port === 80)) {
            $url = "{$protocol}://{$host}/api/session?token=" . urlencode($token);
        } else {
            $url = "{$protocol}://{$host}:{$port}/api/session?token=" . urlencode($token);
        }

        $this->SendDebug('CreateSession', "URL: {$url}", 0);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            $this->SendDebug('CreateSession Error', $error, 0);
            $this->SetStatus(self::STATUS_NO_CONNECTION);
            return false;
        }

        $headers = substr($response, 0, $headerSize);
        $this->SendDebug('CreateSession Response', "HTTP {$httpCode}", 0);

        if ($httpCode === 401) {
            $this->SetStatus(self::STATUS_INVALID_TOKEN);
            return false;
        }

        if ($httpCode >= 400) {
            $this->SendDebug('CreateSession Error', "HTTP Error {$httpCode}", 0);
            $this->SetStatus(self::STATUS_NO_CONNECTION);
            return false;
        }

        preg_match('/Set-Cookie:\s*([^;\r\n]+)/i', $headers, $matches);
        if (isset($matches[1])) {
            $this->WriteAttributeString('SessionCookie', $matches[1]);
            $this->SendDebug('CreateSession', "Cookie: {$matches[1]}", 0);
            return true;
        }

        $this->SendDebug('CreateSession Error', 'No session cookie received', 0);
        $this->SetStatus(self::STATUS_NO_CONNECTION);
        return false;
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
                        [
                            'type' => 'ValidationTextBox',
                            'name' => 'Host',
                            'caption' => 'Traccar Server Host',
                            'width' => '400px'
                        ],
                        [
                            'type' => 'NumberSpinner',
                            'name' => 'Port',
                            'caption' => 'Port',
                            'minimum' => 1,
                            'maximum' => 65535
                        ],
                        [
                            'type' => 'CheckBox',
                            'name' => 'UseHTTPS',
                            'caption' => 'Use HTTPS'
                        ]
                    ]
                ],
                [
                    'type' => 'ExpansionPanel',
                    'caption' => 'Authentication',
                    'expanded' => true,
                    'items' => [
                        [
                            'type' => 'PasswordTextBox',
                            'name' => 'Token',
                            'caption' => 'API Token',
                            'width' => '400px'
                        ],
                        [
                            'type' => 'Label',
                            'caption' => 'Generate an API token in Traccar: Settings → Account → Token'
                        ]
                    ]
                ],
                [
                    'type' => 'ExpansionPanel',
                    'caption' => 'Update Settings',
                    'items' => [
                        [
                            'type' => 'NumberSpinner',
                            'name' => 'UpdateInterval',
                            'caption' => 'Update Interval',
                            'minimum' => 5,
                            'maximum' => 3600,
                            'suffix' => ' s'
                        ]
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
                [
                    'code' => self::STATUS_ACTIVE,
                    'icon' => 'active',
                    'caption' => 'Connected to Traccar server'
                ],
                [
                    'code' => self::STATUS_NO_CONNECTION,
                    'icon' => 'error',
                    'caption' => 'Cannot connect to Traccar server'
                ],
                [
                    'code' => self::STATUS_INVALID_TOKEN,
                    'icon' => 'error',
                    'caption' => 'Invalid API token'
                ],
                [
                    'code' => self::STATUS_CONFIGURATION_ERROR,
                    'icon' => 'error',
                    'caption' => 'Configuration incomplete'
                ]
            ]
        ]);
    }

    public function TestConnection(): bool
    {
        if (!$this->CreateSession()) {
            return false;
        }
        $response = $this->APIRequest('GET', '/api/server');
        if ($response === false) {
            return false;
        }
        return true;
    }

    public function RefreshSession(): bool
    {
        return $this->CreateSession();
    }

    public function GetDevices(): array
    {
        $response = $this->APIRequest('GET', '/api/devices');
        if ($response === false) {
            return [];
        }
        return $response;
    }

    public function GetPositions(): array
    {
        $response = $this->APIRequest('GET', '/api/positions');
        if ($response === false) {
            return [];
        }
        return $response;
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
        if ($response === false) {
            return [];
        }
        return $response;
    }

    public function GetServerInfo(): array
    {
        $response = $this->APIRequest('GET', '/api/server');
        if ($response === false) {
            return [];
        }
        return $response;
    }

    public function UpdateDevices(): void
    {
        $this->SendDebug('UpdateDevices', 'Updating all device instances...', 0);

        $devices = $this->GetDevices();
        $positions = $this->GetPositions();

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
            $this->ProcessDeviceUpdate($device, $position);
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

        if (!isset($buffer['Method']) || !isset($buffer['Endpoint'])) {
            return json_encode(['error' => 'Invalid request']);
        }

        $method = $buffer['Method'];
        $endpoint = $buffer['Endpoint'];
        $body = $buffer['Body'] ?? null;

        $response = $this->APIRequest($method, $endpoint, $body);
        return json_encode($response);
    }

    private function ProcessDeviceUpdate(array $device, array $position): void
    {
        $deviceId = $device['id'] ?? ($position['deviceId'] ?? 0);

        $this->SendDebug('ProcessDeviceUpdate', "Sending update for deviceId: {$deviceId}", 0);

        $this->SendDataToChildren(json_encode([
            'DataID' => '{595D0659-EEE7-C3A6-7F61-2F145327A6AE}',
            'deviceId' => $deviceId,
            'device' => $device,
            'position' => $position
        ]));
    }

    private function APIRequest(string $method, string $endpoint, ?array $body = null)
    {
        $host = $this->ReadPropertyString('Host');
        $port = $this->ReadPropertyInteger('Port');
        $useHTTPS = $this->ReadPropertyBoolean('UseHTTPS');
        $sessionCookie = $this->ReadAttributeString('SessionCookie');

        $protocol = $useHTTPS ? 'https' : 'http';
        
        if (($useHTTPS && $port === 443) || (!$useHTTPS && $port === 80)) {
            $url = "{$protocol}://{$host}{$endpoint}";
        } else {
            $url = "{$protocol}://{$host}:{$port}{$endpoint}";
        }

        $this->SendDebug('APIRequest', "Method: {$method}, URL: {$url}", 0);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if (!empty($sessionCookie)) {
            curl_setopt($ch, CURLOPT_COOKIE, $sessionCookie);
        }

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

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

        if ($error) {
            $this->SendDebug('APIRequest Error', $error, 0);
            $this->SetStatus(self::STATUS_NO_CONNECTION);
            return false;
        }

        $this->SendDebug('APIRequest Response', "HTTP {$httpCode}: {$response}", 0);

        if ($httpCode === 401) {
            $this->SetStatus(self::STATUS_INVALID_TOKEN);
            return false;
        }

        if ($httpCode >= 400) {
            $this->SendDebug('APIRequest Error', "HTTP Error {$httpCode}", 0);
            return false;
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $response;
        }

        return $decoded;
    }
}
