<?php

declare(strict_types=1);

$users = $users ?? [];
$roles = $roles ?? [];
$csrf = $csrf ?? '';
?>

<div class="page-header">
    <div>
        <h1>Usuarios</h1>
        <p>Administración de usuarios y accesos del panel.</p>
    </div>
</div>

<?php if (isset($_GET['saved'])): ?>
    <div class="alert alert-success">
        Operación realizada correctamente.
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger">
        No fue posible completar la operación.
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2>Crear usuario</h2>
    </div>

    <div class="card-body">
        <form method="post" action="/users/save">

            <input
                type="hidden"
                name="_csrf"
                value="<?= htmlspecialchars(
                    $csrf,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >

            <div class="form-grid">

                <div>
                    <label>Nombre</label>

                    <input
                        type="text"
                        name="name"
                        maxlength="120"
                        required
                    >
                </div>

                <div>
                    <label>Email</label>

                    <input
                        type="email"
                        name="email"
                        maxlength="190"
                        required
                    >
                </div>

                <div>
                    <label>Contraseña</label>

                    <input
                        type="password"
                        name="password"
                        minlength="8"
                        required
                    >
                </div>

                <div>
                    <label>Rol</label>

                    <select
                        name="role_id"
                        required
                    >
                        <option value="">
                            Seleccionar...
                        </option>

                        <?php foreach ($roles as $role): ?>

                            <option
                                value="<?= (int) $role['id'] ?>"
                            >
                                <?= htmlspecialchars(
                                    (string) $role['name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>
                </div>

            </div>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Crear usuario
            </button>

        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Usuarios registrados</h2>
    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="hosting-table">

                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Creado</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($users as $user): ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars(
                                (string) $user['name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                (string) $user['email'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                (string) (
                                    $user['roles']
                                    ?: 'Sin rol'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>

                            <?php if (
                                (int) $user['active'] === 1
                            ): ?>

                                <span class="status-badge status-online">
                                    Activo
                                </span>

                            <?php else: ?>

                                <span class="status-badge status-offline">
                                    Inactivo
                                </span>

                            <?php endif; ?>

                        </td>

                        <td>
                            <?= htmlspecialchars(
                                (string) $user['created_at'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>

                            <form
                                method="post"
                                action="/users/toggle"
                            >

                                <input
                                    type="hidden"
                                    name="_csrf"
                                    value="<?= htmlspecialchars(
                                        $csrf,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                >

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int) $user['id'] ?>"
                                >

                                <button
                                    type="submit"
                                    class="btn btn-secondary"
                                >
                                    <?= (
                                        (int) $user['active'] === 1
                                    )
                                        ? 'Desactivar'
                                        : 'Activar' ?>
                                </button>

                            </form>

                        </td>

                    </tr>

                <?php endforeach; ?>

                <?php if (!$users): ?>

                    <tr>
                        <td colspan="6">
                            No hay usuarios registrados.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>
</div>
