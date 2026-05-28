<?php include __DIR__ . '/partials/header.php'; ?>

<section class="hero-section">
    <div class="container hero-grid">
        <div class="hero-copy">
            <p class="eyebrow">Negocio familiar • Pedidos fáciles</p>
            <h1>Descubre los productos del día y compra directo por WhatsApp.</h1>
            <p class="lead">Nuestro catálogo se actualiza dinámicamente y convierte cada selección en un contacto rápido con el vendedor para cerrar tu pedido.</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="#catalogo">Ver catálogo</a>
                <a class="btn btn-secondary" href="/register">Crear cuenta</a>
            </div>
            <ul class="hero-points">
                <li>✓ Gestión de productos</li>
                <li>✓ Login y registro de usuarios</li>
                <li>✓ Compra con redirección a WhatsApp</li>
            </ul>
        </div>

        <div class="hero-card">
            <div class="hero-card-top">Panel del negocio</div>
            <div class="hero-stats">
                <div class="stat-box">
                    <strong><?= count($products) ?></strong>
                    <span>Productos activos</span>
                </div>
                <div class="stat-box">
                    <strong>24/7</strong>
                    <span>Contactos rápidos</span>
                </div>
            </div>
            <div class="hero-highlight">
                <span class="highlight-icon">💬</span>
                <div>
                    <h2>Compra con un clic</h2>
                    <p>Selecciona un producto y serás enviado directamente a WhatsApp del vendedor.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="catalogo" class="content-section">
    <div class="container section-header">
        <div>
            <p class="eyebrow">Catálogo visual</p>
            <h2>Productos destacados</h2>
        </div>
        <p class="section-copy">Cada producto incluye precio, categoría y enlace directo a WhatsApp para que tu pedido llegue rápido.</p>
    </div>

    <div class="container product-grid">
        <?php foreach ($products as $product): ?>
            <article class="product-card">
                <div class="product-emoji" aria-hidden="true"><?= htmlspecialchars($product['icon']) ?></div>
                <div class="product-meta">
                    <span class="category-tag"><?= htmlspecialchars($product['category']) ?></span>
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p><?= htmlspecialchars($product['description']) ?></p>
                </div>
                <div class="product-footer">
                    <div class="price-block">
                        <span class="price-label">Precio</span>
                        <strong class="price-value">$<?= htmlspecialchars($product['formatted_price']) ?></strong>
                    </div>
                    <a class="btn btn-primary buy-link" href="<?= htmlspecialchars($product['whatsapp_url']) ?>" target="_blank" rel="noopener noreferrer">Comprar por WhatsApp</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
