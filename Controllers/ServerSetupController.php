<?php

namespace Pccurico\HostingPanel\Controllers;

use Pccurico\HostingPanel\Services\ApacheService;
use Pccurico\HostingPanel\Services\CloudflareService;
use Pccurico\HostingPanel\Services\DnsService;
use Pccurico\HostingPanel\Services\MariaDbService;
use Pccurico\HostingPanel\Services\PhpService;
use Pccurico\HostingPanel\Services\ServerSetupService;
use Pccurico\HostingPanel\Services\SslService;
use Pccurico\HostingPanel\Services\SystemService;

class ServerSetupController extends UnifiedViewController
{
    private SystemService $systemService;
    private ApacheService $apacheService;
    private PhpService $phpService;
    private MariaDbService $mariaDbService;
    private SslService $sslService;
    private DnsService $dnsService;
    private CloudflareService $cloudflareService;
    private ServerSetupService $serverSetupService;

    public function __construct()
    {
        $this->systemService = new SystemService();
        $this->apacheService = new ApacheService();
        $this->phpService = new PhpService();
        $this->mariaDbService = new MariaDbService();
        $this->sslService = new SslService();
        $this->dnsService = new DnsService();
        $this->cloudflareService = new CloudflareService();
        $this->serverSetupService = new ServerSetupService();
    }

    public function index(): void
    {
        // Dashboard del servidor - estado general
        $data = [
            'system' => $this->systemService->status(),
            'apache' => $this->apacheService->status(),
            'php' => $this->phpService->status(),
            'maria_db' => $this->mariaDbService->status(),
            'ssl' => $this->sslService->status(),
            'dns' => $this->dnsService->status(),
            'cloudflare' => $this->cloudflareService->status(),
            'server' => $this->serverSetupService->getStatus(),
        ];
        
        $this->renderPage('server/index', 'Estado del Servidor', 'Panel de Control de Infraestructura', '/server', $data);
    }

    public function setup(): void
    {
        // Wizard de configuración del servidor - paso actual basado en parámetro GET
        $step = $_GET['step'] ?? 1;
        $step = max(1, min(9, (int)$step)); // Asegurar que esté entre 1 y 9
        
        $data = [
            'currentStep' => $step,
            'totalSteps' => 9,
            'system' => $this->systemService->status(),
            'apache' => $this->apacheService->status(),
            'php' => $this->phpService->status(),
            'maria_db' => $this->mariaDbService->status(),
            'ssl' => $this->sslService->status(),
            'dns' => $this->dnsService->status(),
            'cloudflare' => $this->cloudflareService->status(),
        ];
        
        // Determinar qué vista del wizard cargar según el paso
        $view = "server/setup/step{$step}";
        
        $this->renderPage($view, "Configuración del Servidor", "Paso {$step} de 9", '/server/setup', $data);
    }

    public function setupProcess(): void
    {
        // Manejar el procesamiento de cada paso del wizard (POST)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /server/setup');
            exit;
        }
        
        $step = $_POST['step'] ?? 1;
        $step = max(1, min(9, (int)$step));
        
        // Procesar según el paso
        switch ($step) {
            case 1: // Diagnóstico
                // Ya se hace en el constructor mediante los servicios
                break;
            case 2: // Sistema
                // Configuración del sistema (hostname, etc.)
                if (!empty($_POST['hostname'])) {
                    // En una implementación real, esto actualizaría el hostname del sistema
                }
                break;
            case 3: // Apache
                if (!empty($_POST['apache_action'])) {
                    $action = $_POST['apache_action'];
                    if ($action === 'install' || $action === 'start' || $action === 'restart') {
                        $this->apacheService->restart();
                    }
                }
                break;
            case 4: // PHP
                if (!empty($_POST['php_version'])) {
                    # Configurar versión de PHP
                }
                break;
            case 5: // MariaDB
                if (!empty($_POST['db_action'])) {
                    # Configurar base de datos
                }
                break;
            case 6: // SSL
                if (!empty($_POST['ssl_action'])) {
                    # Configurar SSL
                }
                break;
            case 7: // DNS
                if (!empty($_POST['dns_action'])) {
                    # Configurar DNS
                }
                break;
            case 8: // Cloudflare
                if (!empty($_POST['cloudflare_action'])) {
                    # Configurar Cloudflare
                }
                break;
            case 9: // Finalización
                # Guardar configuración y completar setup
                break;
        }
        
        # Redirigir al siguiente paso o al dashboard
        $nextStep = $step < 9 ? $step + 1 : null;
        if ($nextStep) {
            header("Location: /server/setup?step={$nextStep}");
        } else {
            header('Location: /server');
        }
        exit;
    }
}