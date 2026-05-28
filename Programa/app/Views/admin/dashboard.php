<?php include dirname(__DIR__) . '/partials/header.php'; ?>

<section class="dashboard-section">
    <div class="container dashboard-header">
        <div>
            <p class="eyebrow">Administración</p>
            <h1>Panel del dueño</h1>
            <p class="section-copy">Gestiona el menú, carga catálogos y revisa pedidos para mantener el negocio operativo.</p>
        </div>
    </div>

    <div class="container dashboard-stats">
        <article class="stat-card">
            <span class="stat-title">Usuarios registrados</span>
            <strong class="stat-value"><?= (int) $userCount ?></strong>
        </article>
        <article class="stat-card">
            <span class="stat-title">Productos activos</span>
            <strong class="stat-value"><?= (int) $productCount ?></strong>
        </article>
        <article class="stat-card">
            <span class="stat-title">Pedidos pendientes</span>
            <strong class="stat-value"><?= (int) $pendingOrders ?></strong>
        </article>
    </div>

    <div class="container dashboard-grid">
        <section class="admin-panel">
            <div class="panel-heading">
                <div>
                    <p class="eyebrow">Agregar producto</p>
                    <h2>Nuevo menú</h2>
                </div>
            </div>

            <form method="POST" action="/admin/products" class="admin-form">
                <label>
                    Nombre del producto
                    <input type="text" name="name" placeholder="Café con leche" required />
                </label>

                <label>
                    Descripción
                    <textarea name="description" rows="4" placeholder="Describe el producto" required></textarea>
                </label>

                <div class="form-row">
                    <label>
                        Precio
                        <input type="number" step="0.01" min="0" name="price" placeholder="4500" required />
                    </label>

                    <label>
                        Categoría
                        <input type="text" name="category" placeholder="Bebidas" required />
                    </label>
                </div>

                <div class="form-row">
                    <label>
                        Número de WhatsApp (sin +)
                        <input type="text" name="whatsapp" placeholder="573001234567" required />
                    </label>

                    <label>
                        Icono
                        <input type="text" name="icon" placeholder="☕" value="☕" required />
                    </label>
                </div>

                <button type="submit" class="btn btn-primary full">Publicar producto</button>
            </form>
        </section>

        <section class="admin-panel">
            <div class="panel-heading">
                <div>
                    <p class="eyebrow">Catálogo</p>
                    <h2>Productos del negocio</h2>
                </div>
            </div>

            <div class="product-list">
                <?php foreach ($products as $product): ?>
                    <article class="mini-product">
                        <div class="mini-product-icon"><?= htmlspecialchars($product['icon']) ?></div>
                        <div class="mini-product-copy">
                            <h3><?= htmlspecialchars($product['name']) ?></h3>
                            <p><?= htmlspecialchars($product['category']) ?> • $<?= number_format((float) $product['price'], 0, ',', '.') ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <div class="container admin-full-panel">
        <section class="admin-panel">
            <div class="panel-heading">
                <div>
                    <p class="eyebrow">Carga masiva</p>
                    <h2>Subir catálogo o menú</h2>
                </div>
            </div>

            <form method="POST" action="/admin/import" enctype="multipart/form-data" class="admin-form">
                <label>
                    Archivo CSV
                    <input type="file" name="catalog_file" accept=".csv,text/csv" required />
                </label>
                <p class="form-help">Formato esperado: <strong>name, description, price, category, whatsapp, icon, status</strong>. Ideal para subir menús completos en una sola carga.</p>
                <button type="submit" class="btn btn-primary full">Importar catálogo</button>
            </form>
        </section>
    </div>

    <div class="container admin-full-panel">
        <section class="admin-panel">
            <div class="panel-heading">
                <div>
                    <p class="eyebrow">Pedidos</p>
                    <h2>Administrar pedidos</h2>
                </div>
                <a class="btn btn-secondary" href="/admin/orders">Ver todos</a>
            </div>

            <div class="order-table-wrap">
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= (int) $order['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($order['customer_name']) ?></strong><br />
                                    <span class="order-phone"><?= htmlspecialchars($order['customer_phone']) ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($order['product_name']) ?></strong><br />
                                    <span class="order-note"><?= htmlspecialchars($order['notes'] ?? 'Sin observaciones') ?></span>
                                </td>
                                <td><?= (int) $order['quantity'] ?></td>
                                <td><span class="status-badge status-<?= htmlspecialchars($order['status']) ?>"><?= htmlspecialchars($order['status']) ?></span></td>
                                <td>
                                    <form method="POST" action="/admin/orders" class="inline-status-form">
                                        <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>" />
                                        <select name="status" class="order-status-select">
                                            <option value="pendiente" <?= $order['status'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                            <option value="confirmado" <?= $order['status'] === 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
                                            <option value="listo" <?= $order['status'] === 'listo' ? 'selected' : '' ?>>Listo</option>
                                            <option value="cancelado" <?= $order['status'] === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                        </select>
                                        <button type="submit" class="btn btn-secondary small">Actualizar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</section>

<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
