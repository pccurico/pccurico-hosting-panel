<?php

namespace Pccurico\HostingPanel\Services;

class ApacheService
{
    private string $configPath;
    private bool $enabled;

    public function __construct()
    {
        $this->configPath = getenv('APACHE_CONFIG_PATH') ?: '/etc/apache2/apache2.conf';
        $this->enabled = getenv('APACHE_ENABLED') === 'true';
    }

    public function getConfigPath(): string
    {
        return $this->configPath;
    }

    public function isRunning(): bool
    {
        return $this->enabled && file_exists($this->configPath);
    }

    public function restart(): bool
    {
        if (!$this->isRunning()) {
            return false;
        }
        // Lógica para reiniciar Apache
        return true;
    }

    public function status(): array
    {
        return [
            'service' => 'apache',
            'enabled' => $this->enabled,
            'running' => $this->isRunning(),
            'config_path' => $this->configPath,
        ];
    }
}
