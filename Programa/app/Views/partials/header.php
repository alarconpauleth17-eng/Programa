<?php
$user = $_SESSION['user'] ?? null;
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($pageTitle ?? 'Cafetería Dinámica') ?></title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/style.css" />
</head>
<body>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="<?= BASE_PATH ?>/">
            <span class="brand-icon">☕</span>
            <span>Delicias del Barrio</span>
        </a>

        <nav class="site-nav">
            <a href="<?= BASE_PATH ?>/">Inicio</a>
            <?php if ($user && ($user['role'] ?? '') === 'admin'): ?>
                <a href="<?= BASE_PATH ?>/admin">Administración</a>
            <?php endif; ?>
        </nav>

        <div class="auth-actions">
            <?php if ($user): ?>
                <span class="user-pill">Hola, <?= htmlspecialchars($user['name']) ?></span>
                <?php if (($user['role'] ?? '') === 'admin'): ?>
                    <a class="btn btn-secondary" href="<?= BASE_PATH ?>/admin">Dashboard</a>
                <?php endif; ?>
                <a class="btn btn-secondary" href="<?= BASE_PATH ?>/logout">Cerrar sesión</a>
            <?php else: ?>
                <a class="btn btn-secondary" href="<?= BASE_PATH ?>/login">Entrar</a>
                <a class="btn btn-primary" href="<?= BASE_PATH ?>/register">Registrarse</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php if ($flash): ?>
<div class="flash-message"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<main class="site-main">
