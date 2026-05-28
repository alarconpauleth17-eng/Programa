<?php

namespace App\Controllers;

use App\Controller;
use App\Database;

class DashboardController extends Controller
{
    public function index(): void
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            $_SESSION['flash'] = 'Necesitas iniciar sesión como administrador';
            $this->redirect('/login');
        }

        $pdo = Database::connection();
        $userCount = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $productCount = $pdo->query('SELECT COUNT(*) FROM products WHERE status = "activo"')->fetchColumn();
        $pendingOrders = $pdo->query('SELECT COUNT(*) FROM orders WHERE status = "pendiente"')->fetchColumn();
        $products = $pdo->query('SELECT * FROM products ORDER BY created_at DESC')->fetchAll();
        $orders = $pdo->query('SELECT o.id, o.customer_name, o.customer_phone, o.product_name, o.quantity, o.status, o.notes, o.created_at FROM orders o ORDER BY o.created_at DESC LIMIT 8')->fetchAll();

        $this->view('admin/dashboard', [
            'pageTitle' => 'Panel administrativo',
            'userCount' => $userCount,
            'productCount' => $productCount,
            'pendingOrders' => $pendingOrders,
            'products' => $products,
            'orders' => $orders,
        ]);
    }
}
