<?php

namespace Pccurico\HostingPanel\Services;

class ServerSetupService
{
    private string $hostname;
    private int $port;
    private bool $active;

    public function __construct()
    {
        // Obtener información del servidor real desde los servicios existentes
        $hostname = getenv('SERVER_HOSTNAME');
        if ($hostname === '' || $hostname === 'localhost') {
            $hostname = 'pccurico-hosting-panel';
        }

        $port = (int)parse_int(getenv('SERVER_PORT')) ?? 8080;
        $active = getenv('SERVER_ACTIVE') === 'true' ? true : false;

        $this->hostname = $hostname;
        $this->port = $port;
        $this->active = $active;
    }

    public function getStatus(): array
    {
        return [
            'hostname' => $this->hostname,
            'port' => $this->port,
            'active' => $this->active,
            'timestamp' => date('Y-m-d H:i') . ' UTC',
        ];
    }

    public function enable(): bool
    {
        if (!$this->active) {
            // Lógica para habilitar el servidor
            return true;
        }
        return false;
    }

    public function disable(): bool
    {
        if ($this->active) {
            // Lógica para deshabilitar el servidor
            return true;
        }
        return false;
    }
}
