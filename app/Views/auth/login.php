<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCCURICO Hosting Panel</title>
    <link rel="stylesheet" href="/assets/css/panel.css">
</head>
<body class="login-page">

<div class="login-card">
    <div class="brand">
        <div class="brand-mark">P</div>
        <div>
            <strong>PCCURICO</strong>
            <span>Hosting Panel</span>
        </div>
    </div>

    <h1>Iniciar sesión</h1>
    <p class="muted">Administración del servidor</p>

    <?php if (!empty($error)): ?>
        <div class="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="post" action="/login">
        <input
            type="hidden"
            name="_csrf"
            value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"
        >

        <label>Correo electrónico</label>
        <input
            type="email"
            name="email"
            autocomplete="username"
            required
        >

        <label>Contraseña</label>
        <input
            type="password"
            name="password"
            autocomplete="current-password"
            required
        >

        <button type="submit">Ingresar</button>
    </form>
</div>

</body>
</html>
