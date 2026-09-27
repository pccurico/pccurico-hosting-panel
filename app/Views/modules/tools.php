<?php

declare(strict_types=1);

$module = 'tools';
$title = 'Herramientas';
$subtitle = 'Utilidades administrativas del servidor';
$icon = '🛠';
$section = 'Sistema';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | PCCURICO Hosting Panel</title>
    <link rel="stylesheet" href="/assets/css/panel.css">
    <link rel="stylesheet" href="/assets/css/pccurico-shell.css">
    <link rel="stylesheet" href="/assets/css/pccurico-modules.css">
</head>

<body class="pcc-panel-body">

<div class="pcc-shell">

    <aside class="pcc-sidebar">

        <div class="pcc-brand">
            <div class="pcc-brand-mark">P</div>
            <div>
                <strong>PCCURICO</strong>
                <span>Hosting Panel</span>
            </div>
        </div>

        <nav class="pcc-nav">

            <div class="pcc-nav-label">Principal</div>

            <a href="/dashboard" class="pcc-nav-link">
                <span>▦</span>
                <span>Dashboard</span>
            </a>

            <div class="pcc-nav-label">Hosting</div>

            <a href="/sites" class="pcc-nav-link">
                <span>⌂</span>
                <span>Sitios</span>
            </a>

            <a href="/domains" class="pcc-nav-link <?= $module === 'domains' ? 'active' : '' ?>">
                <span>🌐</span>
                <span>Dominios</span>
            </a>

            <a href="/dns" class="pcc-nav-link <?= $module === 'dns' ? 'active' : '' ?>">
                <span>🔗</span>
                <span>DNS</span>
            </a>

            <a href="/databases" class="pcc-nav-link <?= $module === 'databases' ? 'active' : '' ?>">
                <span>💾</span>
                <span>Bases de datos</span>
            </a>

            <a href="/mail" class="pcc-nav-link <?= $module === 'mail' ? 'active' : '' ?>">
                <span>✉</span>
                <span>Correo</span>
            </a>

            <div class="pcc-nav-label">Servidor</div>

            <a href="/apache" class="pcc-nav-link <?= $module === 'apache' ? 'active' : '' ?>">
                <span>🖥</span>
                <span>Apache</span>
            </a>

            <a href="/php" class="pcc-nav-link <?= $module === 'php' ? 'active' : '' ?>">
                <span>🐘</span>
                <span>PHP</span>
            </a>

            <a href="/mysql" class="pcc-nav-link <?= $module === 'mysql' ? 'active' : '' ?>">
                <span>🗄</span>
                <span>MySQL</span>
            </a>

            <a href="/logs" class="pcc-nav-link <?= $module === 'logs' ? 'active' : '' ?>">
                <span>▤</span>
                <span>Logs</span>
            </a>

            <div class="pcc-nav-label">Seguridad</div>

            <a href="/ssl" class="pcc-nav-link <?= $module === 'ssl' ? 'active' : '' ?>">
                <span>🔒</span>
                <span>SSL</span>
            </a>

            <a href="/backups" class="pcc-nav-link <?= $module === 'backups' ? 'active' : '' ?>">
                <span>↻</span>
                <span>Backups</span>
            </a>

            <a href="/audit" class="pcc-nav-link <?= $module === 'audit' ? 'active' : '' ?>">
                <span>◉</span>
                <span>Auditoría</span>
            </a>

            <div class="pcc-nav-label">Usuarios</div>

            <a href="/users" class="pcc-nav-link">
                <span>♙</span>
                <span>Usuarios</span>
            </a>

            <a href="/roles" class="pcc-nav-link <?= $module === 'roles' ? 'active' : '' ?>">
                <span>♟</span>
                <span>Roles</span>
            </a>

            <a href="/permissions" class="pcc-nav-link <?= $module === 'permissions' ? 'active' : '' ?>">
                <span>✓</span>
                <span>Permisos</span>
            </a>

            <div class="pcc-nav-label">Sistema</div>

            <a href="/settings" class="pcc-nav-link <?= $module === 'settings' ? 'active' : '' ?>">
                <span>⚙</span>
                <span>Configuración</span>
            </a>

            <a href="/tools" class="pcc-nav-link <?= $module === 'tools' ? 'active' : '' ?>">
                <span>🛠</span>
                <span>Herramientas</span>
            </a>

            <a href="/logout" class="pcc-nav-link pcc-nav-danger">
                <span>⇥</span>
                <span>Cerrar sesión</span>
            </a>

        </nav>

    </aside>

    <main class="pcc-main">

        <header class="pcc-topbar">

            <div>
                <div class="pcc-breadcrumb">
                    PCCURICO Hosting Panel / <?= htmlspecialchars($section) ?>
                </div>

                <h1><?= htmlspecialchars($title) ?></h1>

                <p><?= htmlspecialchars($subtitle) ?></p>
            </div>

            <div class="pcc-user-box">
                <span class="pcc-status-dot"></span>
                <span>Administrador</span>
            </div>

        </header>

        <section class="pcc-content">

            <div class="pcc-page-title">
                <div class="pcc-page-icon"><?= $icon ?></div>

                <div>
                    <h2><?= htmlspecialchars($title) ?></h2>
                    <p><?= htmlspecialchars($subtitle) ?></p>
                </div>
            </div>

            <div class="pcc-module-grid">

                <div class="pcc-module-card pcc-module-main">

                    <div class="pcc-module-card-header">
                        <div>
                            <span class="pcc-card-kicker">MÓDULO</span>
                            <h3><?= htmlspecialchars($title) ?></h3>
                        </div>

                        <span class="pcc-module-icon"><?= $icon ?></span>
                    </div>

                    <p class="pcc-module-description">
                        Este módulo forma parte del sistema de administración
                        centralizada de PCCURICO Hosting.
                    </p>

                    <div class="pcc-module-state">
                        <span class="pcc-status-dot"></span>
                        Interfaz preparada
                    </div>

                </div>

                <div class="pcc-module-card">

                    <span class="pcc-card-kicker">ESTADO</span>

                    <div class="pcc-stat-value">
                        Preparado
                    </div>

                    <p>
                        La interfaz está instalada y lista para conectar
                        las operaciones reales del servidor.
                    </p>

                </div>

                <div class="pcc-module-card">

                    <span class="pcc-card-kicker">SEGURIDAD</span>

                    <div class="pcc-stat-value">
                        Sesión requerida
                    </div>

                    <p>
                        El acceso a este módulo requiere autenticación
                        dentro del panel.
                    </p>

                </div>

            </div>

            <div class="pcc-panel-section">

                <div class="pcc-section-header">
                    <div>
                        <span class="pcc-card-kicker">ADMINISTRACIÓN</span>
                        <h3>Herramientas de <?= htmlspecialchars($title) ?></h3>
                    </div>
                </div>

                <div class="pcc-action-grid">

                    <button type="button" class="pcc-action-card">
                        <span>＋</span>
                        <strong>Crear</strong>
                        <small>Preparar nuevo recurso</small>
                    </button>

                    <button type="button" class="pcc-action-card">
                        <span>≡</span>
                        <strong>Administrar</strong>
                        <small>Gestionar recursos existentes</small>
                    </button>

                    <button type="button" class="pcc-action-card">
                        <span>↻</span>
                        <strong>Actualizar</strong>
                        <small>Consultar estado actual</small>
                    </button>

                    <button type="button" class="pcc-action-card">
                        <span>⚙</span>
                        <strong>Configuración</strong>
                        <small>Opciones del módulo</small>
                    </button>

                </div>

            </div>

            <div class="pcc-notice">

                <div class="pcc-notice-icon">i</div>

                <div>
                    <strong>Interfaz instalada</strong>

                    <p>
                        Las operaciones destructivas y los cambios sobre
                        servidor/base de datos se habilitarán en la etapa
                        de implementación funcional.
                    </p>
                </div>

            </div>

        </section>

    </main>

</div>

</body>
</html>
