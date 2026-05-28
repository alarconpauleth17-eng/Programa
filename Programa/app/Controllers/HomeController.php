<?php

namespace App\Controllers;

use App\Controller;
use App\Database;

class HomeController extends Controller
{
    public function index(): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT * FROM products WHERE status = "activo" ORDER BY created_at DESC');
        $products = $stmt->fetchAll();

        foreach ($products as &$product) {
            $product['formatted_price'] = number_format($product['price'], 0, ',', '.');
            $product['whatsapp_url'] = 'https://wa.me/' . $product['whatsapp'] . '?text=' . urlencode('Hola, quiero comprar ' . $product['name'] . ' por $' . $product['formatted_price']);
        }

        $this->view('home', [
            'products' => $products,
            'pageTitle' => 'Inicio',
        ]);
    }
}
