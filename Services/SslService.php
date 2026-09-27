<?php

namespace Pccurico\HostingPanel\Services;

class SslService
{
    private string $certPath;
    private string $keyPath;
    private bool $enabled;

    public function __construct()
    {
        $this->certPath = getenv('SSL_CERT_PATH') ?: '/etc/ssl/certs/ssl-cert-snakeoil.pem';
        $this->keyPath = getenv('SSL_KEY_PATH') ?: '/etc/ssl/private/ssl-cert-snakeoil.key';
        $this->enabled = getenv('SSL_ENABLED') === 'true';
    }

    public function getCertificatePath(): string
    {
        return $this->certPath;
    }

    public function isValid(): bool
    {
        return $this->enabled && file_exists($this->certPath) && file_exists($this->keyPath);
    }

    public function status(): array
    {
        return [
            'service' => 'ssl',
            'certificate' => $this->certPath,
            'key' => $this->keyPath,
            'enabled' => $this->enabled,
            'valid' => $this->isValid(),
        ];
    }
}
