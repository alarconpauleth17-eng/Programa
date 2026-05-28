<?php include dirname(__DIR__) . '/partials/header.php'; ?>

<section class="dashboard-section">
    <div class="container dashboard-header">
        <div>
            <p class="eyebrow">Pedidos</p>
            <h1>Gestión completa de pedidos</h1>
            <p class="section-copy">Revisa los pedidos del negocio, actualiza el estado y prioriza la atención al cliente.</p>
        </div>
    </div>

    <div class="container admin-full-panel">
        <section class="admin-panel">
            <div class="panel-heading">
                <div>
                    <p class="eyebrow">Historial</p>
                    <h2>Pedidos registrados</h2>
                </div>
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
                            <th>Fecha</th>
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
                                <td><?= htmlspecialchars($order['created_at']) ?></td>
                                <td>
                                    <form method="POST" action="<?= BASE_PATH ?>/admin/orders" class="inline-status-form">
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
