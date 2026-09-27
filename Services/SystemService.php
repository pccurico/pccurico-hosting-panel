<?php

namespace Pccurico\HostingPanel\Services;

class SystemService
{
    private string $hostname;
    private int $uptimeSeconds;

    public function __construct()
    {
        $this->hostname = getenv('HOSTNAME') ?: 'localhost';
        $this->uptimeSeconds = 0;
    }

    public function getHostname(): string
    {
        return $this->hostname;
    }

    public function getUptimeSeconds(): int
    {
        return $this->uptimeSeconds;
    }

    public function status(): array
    {
        return [
            'service' => 'system',
            'hostname' => $this->hostname,
            'uptime_seconds' => $this->uptimeSeconds,
            'timestamp' => date('Y-m-d H:i') . ' UTC',
        ];
    }
}
