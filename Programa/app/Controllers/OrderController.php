<?php

namespace App\Controllers;

use App\Controller;
use App\Database;

class OrderController extends Controller
{
    public function index(): void
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            $_SESSION['flash'] = 'Necesitas permisos de administrador';
            $this->redirect('/login');
        }

        $pdo = Database::connection();
        $orders = $pdo->query('SELECT o.id, o.customer_name, o.customer_phone, o.product_name, o.quantity, o.status, o.notes, o.created_at FROM orders o ORDER BY o.created_at DESC')->fetchAll();

        $this->view('admin/orders', [
            'pageTitle' => 'Pedidos',
            'orders' => $orders,
        ]);
    }

    public function updateStatus(): void
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            $_SESSION['flash'] = 'Necesitas permisos de administrador';
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/orders');
        }

        $orderId = (int) ($_POST['order_id'] ?? 0);
        $status = trim($_POST['status'] ?? 'pendiente');

        if ($orderId <= 0 || !in_array($status, ['pendiente', 'confirmado', 'listo', 'cancelado'], true)) {
            $_SESSION['flash'] = 'Estado del pedido inválido';
            $this->redirect('/admin/orders');
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE orders SET status = :status WHERE id = :id');
        $stmt->execute([
            ':status' => $status,
            ':id' => $orderId,
        ]);

        $_SESSION['flash'] = 'Pedido actualizado correctamente';
        $this->redirect('/admin/orders');
    }
}
