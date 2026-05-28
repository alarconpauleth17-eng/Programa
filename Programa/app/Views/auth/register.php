<?php include dirname(__DIR__) . '/partials/header.php'; ?>

<section class="auth-section">
    <div class="container auth-shell">
        <div class="auth-card">
            <p class="eyebrow">Registro</p>
            <h1>Crear cuenta</h1>
            <p class="auth-copy">Regístrate para acceder al catálogo, guardar tu preferencia y comprar con WhatsApp.</p>

            <form method="POST" action="/register" class="auth-form">
                <label>
                    Nombre completo
                    <input type="text" name="name" placeholder="Juan Pérez" required />
                </label>

                <label>
                    Correo electrónico
                    <input type="email" name="email" placeholder="tu@correo.com" required />
                </label>

                <label>
                    Contraseña
                    <input type="password" name="password" placeholder="••••••••" required />
                </label>

                <label>
                    Confirmar contraseña
                    <input type="password" name="confirm_password" placeholder="••••••••" required />
                </label>

                <button type="submit" class="btn btn-primary full">Crear cuenta</button>
            </form>

            <p class="auth-note">¿Ya tienes cuenta? <a href="/login">Inicia sesión</a></p>
        </div>
    </div>
</section>

<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
