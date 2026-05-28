<?php include dirname(__DIR__) . '/partials/header.php'; ?>

<section class="auth-section">
    <div class="container auth-shell">
        <div class="auth-card">
            <p class="eyebrow">Acceso</p>
            <h1>Iniciar sesión</h1>
            <p class="auth-copy">Ingresa tus credenciales para administrar tus pedidos o revisar el catálogo.</p>

            <form method="POST" action="/login" class="auth-form">
                <label>
                    Correo electrónico
                    <input type="email" name="email" placeholder="tu@correo.com" required />
                </label>

                <label>
                    Contraseña
                    <input type="password" name="password" placeholder="••••••••" required />
                </label>

                <button type="submit" class="btn btn-primary full">Ingresar</button>
            </form>

            <p class="auth-note">¿No tienes cuenta? <a href="/register">Regístrate aquí</a></p>
        </div>
    </div>
</section>

<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
