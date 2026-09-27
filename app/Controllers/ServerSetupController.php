<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Controllers;

use Pccurico\HostingPanel\Core\Router;
use Pccurico\HostingPanel\Core\View;

class ServerSetupController
{
    private Router $router;
    private View $view;

    public function __construct(Router $router, View $view)
    {
        $this->router = $router;
        $this->view = $view;
    }

    public function index(): void
    {
        $this->view->render('server/setup/process', [
            'title' => 'Configuración del Panel',
            'subtitle' => 'Completar la configuración del panel',
        ]);
    }

    public function step1(): void
    {
        $this->view->render('server/setup/step1', [
            'title' => 'Paso 1: Información del Servidor',
            'subtitle' => 'Ingrese los datos básicos del servidor',
        ]);
    }

    public function step2(): void
    {
        $this->view->render('server/setup/step2', [
            'title' => 'Paso 2: Configuración de Red',
            'subtitle' => 'Configure la red y DNS básico',
        ]);
    }

    public function step3(): void
    {
        $this->view->render('server/setup/step3', [
            'title' => 'Paso 3: Seguridad Básica',
            'subtitle' => 'Active medidas de seguridad iniciales',
        ]);
    }

    public function step4(): void
    {
        $this->view->render('server/setup/step4', [
            'title' => 'Paso 4: Configuración de Usuario',
            'subtitle' => 'Cree el primer usuario administrador',
        ]);
    }

    public function step5(): void
    {
        $this->view->render('server/setup/step5', [
            'title' => 'Paso 5: Configuración de Correo',
            'subtitle' => 'Configure el servidor de correo',
        ]);
    }

    public function step6(): void
    {
        $this->view->render('server/setup/step6', [
            'title' => 'Paso 6: Configuración de SSL',
            'subtitle' => 'Instale y configure certificado SSL',
        ]);
    }

    public function step7(): void
    {
        $this->view->render('server/setup/step7', [
            'title' => 'Paso 7: Configuración de Backup',
            'subtitle' => 'Active y configure backups automáticos',
        ]);
    }

    public function step8(): void
    {
        $this->view->render('server/setup/step8', [
            'title' => 'Paso 8: Configuración de Acceso SSH',
            'subtitle' => 'Configure acceso SSH y firewall',
        ]);
    }

    public function step9(): void
    {
        $this->view->render('server/setup/step9', [
            'title' => 'Paso 9: Configuración de Herramientas',
            'subtitle' => 'Active herramientas de mantenimiento',
        ]);
    }

    public function process(): void
    {
        // Process all setup steps
        $this->view->render('server/setup/process', [
            'title' => 'Configuración Completada',
            'subtitle' => 'El panel ha sido configurado exitosamente',
            'status' => 'success',
        ]);
    }
}