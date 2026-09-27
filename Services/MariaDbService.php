<?php

namespace Pccurico\HostingPanel\Services;

class MariaDbService
{
    private string $dbHost;
    private string $dbName;
    private string $dbPort;
    private bool $enabled;

    public function __construct()
    {
        $this->dbHost = getenv('DB_HOST') ?: 'localhost';
        $this->dbName = getenv('DB_NAME') ?: 'pccurico_db';
        $this->dbPort = getenv('DB_PORT') ?: '3306';
        $this->enabled = getenv('DB_ENABLED') === 'true';
    }

    public function getConnectionString(): string
    {
        return "mysql://${this->dbHost}:${this->dbPort}/${this->dbName}";
    }

    public function isConnected(): bool
    {
        return $this->enabled && !empty($this->dbHost);
    }

    public function status(): array
    {
        return [
            'service' => 'maria-db',
            'host' => $this->dbHost,
            'name' => $this->dbName,
            'port' => $this->dbPort,
            'enabled' => $this->enabled,
            'connected' => $this->isConnected(),
        ];
    }
}
