<?php

declare(strict_types=1);

$title = $title ?? 'Permissions';
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
                    <h3>Gestión de permisos</h3>
                </div>

                <span class="pcc-module-icon">
                    <?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') ?>
                </span>

            </div>

            <div class="pcc-module-card-body">

                <div class="pcc-permissions-form">
                    <h2>Crear nuevo permiso</h2>
                    <form method="post" action="/permissions/create">
                        <div class="form-group">
                            <label>Nombre del permiso</label>
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
                        <div class="form-group">
                            <label>Rol asignado</label>
                            <select name="role_id" required>
                                <option value="">Seleccione un rol</option>
                                <?php foreach ($data['roles'] as $role): ?>
                                    <option value="<?= (int) $role['id'] ?>">
                                        <?= htmlspecialchars(
                                            (string) $role['name'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Crear permiso
                        </button>
                    </form>
                </div>

                <div class="pcc-permissions-list">
                    <h2>Permisos existentes</h2>

                    <?php if (empty($data['permissions'])): ?>

                        <p class="pcc-empty-state">
                            No hay permisos registrados.
                        </p>

                    <?php else: ?>

                        <table class="hosting-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Rol</th>
                                    <th>Usuarios</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>

                            <?php foreach ($data['permissions'] as $permission): ?>

                                <tr>
                                    <td>
                                        <?= htmlspecialchars(
                                            (string) $permission['name'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            (string) ($permission['description'] ?? ''),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <span class="badge badge-pill badge-secondary">
                                            <?= htmlspecialchars(
                                                (string) ($permission['role_name'] ?? '-'),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= (int) ($permission['users_count'] ?? 0) ?>
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