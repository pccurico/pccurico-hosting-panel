<?php

namespace Pccurico\HostingPanel\Services;

class DnsService
{
    private string $zone;
    private string[] $records;
    private bool $enabled;

    public function __construct()
    {
        $this->zone = getenv('DNS_ZONE') ?: 'example.com';
        $this->records = [];
        $this->enabled = getenv('DNS_ENABLED') === 'true';
    }

    public function addRecord(string $name, string $value): void
    {
        $this->records[] = [$name, $value];
    }

    public function getRecords(): array
    {
        return $this->records;
    }

    public function status(): array
    {
        return [
            'service' => 'dns',
            'zone' => $this->zone,
            'records_count' => count($this->records),
            'enabled' => $this->enabled,
        ];
    }
}
