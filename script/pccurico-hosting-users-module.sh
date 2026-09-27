#!/usr/bin/env bash
set -euo pipefail

PROJECT="/var/www/pccurico-hosting-panel"
SCRIPT_DIR="$PROJECT/script"
STAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP="/root/pccurico-hosting-users-$STAMP"

echo "=============================================="
echo " PCCURICO HOSTING PANEL - USUARIOS"
echo "=============================================="

mkdir -p "$BACKUP"

echo "[1/8] Backup..."

cp -a "$PROJECT/routes/web.php" "$BACKUP/web.php"
cp -a "$PROJECT/app/Views/layouts/app.php" "$BACKUP/app.php"

[ -f "$PROJECT/app/Models/User.php" ] &&
    cp -a "$PROJECT/app/Models/User.php" "$BACKUP/User.php" || true

[ -f "$PROJECT/app/Controllers/UsersController.php" ] &&
    cp -a "$PROJECT/app/Controllers/UsersController.php" "$BACKUP/UsersController.php" || true

echo "[OK] Backup: $BACKUP"

echo "[2/8] Verificando base de datos..."

DBPASS="$(grep '^DB_PASSWORD=' "$PROJECT/.env" | cut -d= -f2-)"

mysql \
    --host=127.0.0.1 \
    --user=pccurico_hosting \
    --password="$DBPASS" \
    --database=pccurico_hosting \
    -e "SELECT COUNT(*) AS permissions FROM permissions;
        SELECT COUNT(*) AS role_permissions FROM role_permissions;"

echo "[OK] Base de datos."

echo "[3/8] Creando modelo User..."

mkdir -p "$PROJECT/app/Models"

cat > "$PROJECT/app/Models/User.php" <<'PHP'
<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Models;

use Pccurico\HostingPanel\Core\Database;
use PDO;

final class User
{
    public static function all(): array
    {
        $stmt = Database::connection()->query("
            SELECT
                u.id,
                u.name,
                u.email,
                u.active,
                u.created_at,
                GROUP_CONCAT(
                    r.name
                    ORDER BY r.name
                    SEPARATOR ', '
                ) AS roles
            FROM users u
            LEFT JOIN user_roles ur
                ON ur.user_id = u.id
            LEFT JOIN roles r
                ON r.id = ur.role_id
            GROUP BY
                u.id,
                u.name,
                u.email,
                u.active,
                u.created_at
            ORDER BY u.id DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare("
            SELECT
                id,
                name,
                email,
                active,
                created_at,
                updated_at
            FROM users
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([$id]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public static function create(
        string $name,
        string $email,
        string $password,
        int $roleId
    ): int {
        $pdo = Database::connection();

        $stmt = $pdo->prepare("
            INSERT INTO users
                (name, email, password_hash, active)
            VALUES
                (?, ?, ?, 1)
        ");

        $stmt->execute([
            $name,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
        ]);

        $id = (int) $pdo->lastInsertId();

        self::setRole($id, $roleId);

        return $id;
    }

    public static function setActive(
        int $id,
        bool $active
    ): void {
        $stmt = Database::connection()->prepare("
            UPDATE users
            SET active = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $active ? 1 : 0,
            $id,
        ]);
    }

    public static function setRole(
        int $userId,
        int $roleId
    ): void {
        $pdo = Database::connection();

        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("
                DELETE FROM user_roles
                WHERE user_id = ?
            ");

            $stmt->execute([$userId]);

            $stmt = $pdo->prepare("
                INSERT INTO user_roles
                    (user_id, role_id)
                VALUES
                    (?, ?)
            ");

            $stmt->execute([
                $userId,
                $roleId,
            ]);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function roles(): array
    {
        return Database::connection()
            ->query("
                SELECT
                    id,
                    name
                FROM roles
                ORDER BY name
            ")
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}
PHP

echo "[OK] Modelo User."

echo "[4/8] Creando controlador..."

mkdir -p "$PROJECT/app/Controllers"

cat > "$PROJECT/app/Controllers/UsersController.php" <<'PHP'
<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Controllers;

use Pccurico\HostingPanel\Core\Database;
use Pccurico\HostingPanel\Middleware\Csrf;
use Pccurico\HostingPanel\Models\User;

final class UsersController
{
    public function index(): void
    {
        $this->requireAdmin();

        $this->render('users/index', [
            'users' => User::all(),
            'roles' => User::roles(),
            'csrf' => Csrf::token(),
        ]);
    }

    public function save(): void
    {
        $this->requireAdmin();

        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            exit('Token CSRF inválido.');
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');
        $roleId = (int) ($_POST['role_id'] ?? 0);

        if (
            $name === '' ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            $roleId <= 0 ||
            strlen($password) < 8
        ) {
            $this->redirect('/users?error=datos');
        }

        try {
            $id = User::create(
                $name,
                $email,
                $password,
                $roleId
            );

            $this->audit(
                'users.create',
                'user',
                $id
            );
        } catch (\Throwable $e) {
            error_log((string) $e);
            $this->redirect('/users?error=save');
        }

        $this->redirect('/users?saved=1');
    }

    public function toggle(): void
    {
        $this->requireAdmin();

        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            exit('Token CSRF inválido.');
        }

        $id = (int) ($_POST['id'] ?? 0);

        $target = User::find($id);
        $current = User::find(
            (int) ($_SESSION['user_id'] ?? 0)
        );

        if (!$target || !$current) {
            $this->redirect('/users?error=user');
        }

        if (
            strtolower((string) $target['email']) ===
            strtolower((string) $current['email'])
        ) {
            $this->redirect('/users?error=self');
        }

        User::setActive(
            $id,
            (int) $target['active'] !== 1
        );

        $this->audit(
            'users.toggle',
            'user',
            $id
        );

        $this->redirect('/users?saved=1');
    }

    private function requireAdmin(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $stmt = Database::connection()->prepare("
            SELECT r.name
            FROM user_roles ur
            INNER JOIN roles r
                ON r.id = ur.role_id
            WHERE ur.user_id = ?
        ");

        $stmt->execute([
            (int) $_SESSION['user_id']
        ]);

        $roles = $stmt->fetchAll(
            \PDO::FETCH_COLUMN
        );

        if (
            !in_array(
                'SuperAdministrador',
                $roles,
                true
            ) &&
            !in_array(
                'Administrador',
                $roles,
                true
            )
        ) {
            http_response_code(403);
            exit('Acceso denegado.');
        }
    }

    private function audit(
        string $action,
        string $targetType,
        int $targetId
    ): void {
        try {
            $stmt = Database::connection()->prepare("
                INSERT INTO audit_logs
                (
                    user_id,
                    action,
                    target_type,
                    target_id,
                    ip_address,
                    user_agent
                )
                VALUES
                (?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                (int) ($_SESSION['user_id'] ?? 0),
                $action,
                $targetType,
                (string) $targetId,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null,
            ]);
        } catch (\Throwable $e) {
            error_log((string) $e);
        }
    }

    private function render(
        string $view,
        array $data = []
    ): void {
        extract($data, EXTR_SKIP);

        $file =
            dirname(__DIR__) .
            '/Views/' .
            $view .
            '.php';

        if (!is_file($file)) {
            http_response_code(500);
            exit('Vista no encontrada.');
        }

        require $file;
    }

    private function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }
}
PHP

echo "[OK] Controller."

echo "[5/8] Creando vista..."

mkdir -p "$PROJECT/app/Views/users"

cat > "$PROJECT/app/Views/users/index.php" <<'PHP'
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
PHP

echo "[OK] Vista."

echo "[6/8] Registrando rutas..."

python3 - "$PROJECT/routes/web.php" <<'PY'
from pathlib import Path
import sys

path = Path(sys.argv[1])
text = path.read_text()

use_line = (
    "use Pccurico\\HostingPanel\\Controllers\\UsersController;"
)

if use_line not in text:
    text = text.replace(
        "use Pccurico\\HostingPanel\\Controllers\\SitesController;",
        "use Pccurico\\HostingPanel\\Controllers\\SitesController;\n"
        + use_line
    )

if "$usersController = new UsersController();" not in text:
    text = text.replace(
        "$sitesController = new SitesController();",
        "$sitesController = new SitesController();\n"
        "$usersController = new UsersController();"
    )

if "$router->get('/users'" not in text:
    text = text.replace(
        "return $router;",
        """
$router->get(
    '/users',
    [$usersController, 'index']
);

$router->post(
    '/users/save',
    [$usersController, 'save']
);

$router->post(
    '/users/toggle',
    [$usersController, 'toggle']
);

return $router;
"""
    )

path.write_text(text)
PY

echo "[OK] Rutas."

echo "[7/8] Validando..."

php -l "$PROJECT/app/Models/User.php"
php -l "$PROJECT/app/Controllers/UsersController.php"
php -l "$PROJECT/app/Views/users/index.php"
php -l "$PROJECT/routes/web.php"

echo "[8/8] Permisos..."

chown www-data:www-data \
    "$PROJECT/app/Models/User.php" \
    "$PROJECT/app/Controllers/UsersController.php"

chown -R www-data:www-data \
    "$PROJECT/app/Views/users"

chmod 640 \
    "$PROJECT/app/Models/User.php" \
    "$PROJECT/app/Controllers/UsersController.php"

echo
echo "=============================================="
echo " MÓDULO USUARIOS INSTALADO"
echo "=============================================="
echo
echo "URL:"
echo "http://hosting.local/users"
echo
echo "Backup:"
echo "$BACKUP"
echo
echo "No se modificaron Sitios, Apache, DNS ni Cloudflare."
