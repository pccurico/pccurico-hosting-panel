<?php

namespace Pccurico\HostingPanel\Services;

class PhpService
{
    private string $engine;
    private bool $enabled;

    public function __construct()
    {
        $this->engine = getenv('PHP_ENGINE') ?: 'cli';
        $this->enabled = getenv('PHP_ENABLED') === 'true';
    }

    public function getEngine(): string
    {
        return $this->engine;
    }

    public function isRunning(): bool
    {
        return $this->enabled && file_exists($this->engine . '_status');
    }

    public function status(): array
    {
        return [
            'service' => 'php',
            'engine' => $this->engine,
            'enabled' => $this->enabled,
            'status' => $this->isRunning() ? 'running' : 'stopped',
        ];
    }
}
