<?php

namespace Pccurico\HostingPanel\Services;

class CloudflareService
{
    private string $apiKey;
    private string $domain;
    private bool $enabled;

    public function __construct()
    {
        $this->apiKey = getenv('CLOUDFLARE_API_KEY') ?: '';
        $this->domain = getenv('CLOUDFLARE_DOMAIN') ?: 'example.com';
        $this->enabled = getenv('CLOUDFLARE_ENABLED') === 'true';
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function isReady(): bool
    {
        return $this->enabled && !empty($this->apiKey);
    }

    public function status(): array
    {
        return [
            'service' => 'cloudflare',
            'domain' => $this->domain,
            'api_key_valid' => !empty($this->apiKey),
            'enabled' => $this->enabled,
        ];
    }
}
