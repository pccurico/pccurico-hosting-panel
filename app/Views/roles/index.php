<?php

declare(strict_types=1);

$title = $title ?? 'Roles';
?>

<div class="pcc-page-wrapper">

    <div class="page-header">
        <div>
            <h1>
                <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p>
                <?= htmlspecialchars(
                    $subtitle ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>
        </div>
    </div>

    <div class="pcc-module-grid">

        <div class="pcc-module-card pcc-module-main">

            <div class="pcc-module-card-header">

                <div>
                    <span class="pcc-card-kicker">
                        ADMINISTRACIÓN
                    </span>
                    <h3>Gestión de roles</h3>
                </div>

                <span class="pcc-module-icon">
                    <?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') ?>
                </span>

            </div>

            <div class="pcc-module-card-body">

                <div class="pcc-roles-form">
                    <h2>Crear nuevo rol</h2>
                    <form method="post" action="/roles/create">
                        <div class="form-group">
                            <label>Nombre del rol</label>
                            <input
                                type="text"
                                name="name"
                                maxlength="100"
                                required
                            >
                        </div>
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea
                                name="description"
                                rows="3"
                                maxlength="255"
                            ></textarea>
                        </div>
                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Crear rol
                        </button>
                    </form>
                </div>

                <div class="pcc-roles-list">
                    <h2>Roles existentes</h2>

                    <?php if (empty($data['roles'])): ?>

                        <p class="pcc-empty-state">
                            No hay roles registrados.
                        </p>

                    <?php else: ?>

                        <table class="hosting-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Permisos</th>
                                    <th>Usuarios</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>

                            <?php foreach ($data['roles'] as $role): ?>

                                <tr>
                                    <td>
                                        <?= htmlspecialchars(
                                            (string) $role['name'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            (string) ($role['description'] ?? ''),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <span class="badge badge-pill badge-info">
                                            Ver permisos
                                        </span>
                                    </td>

                                    <td>
                                        <?= (int) ($role['users_count'] ?? 0) ?>
                                    </td>

                                    <td>
                                        <span class="status-badge status-online">
                                            Activo
                                        </span>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                            </tbody>
                        </table>

                    <?php endif; ?>
                </div>

            </div>

        </div>

    </div>

</div>