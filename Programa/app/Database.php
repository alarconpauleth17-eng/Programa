<?php

namespace App;

use PDO;
use PDOException;

class Database
{
    public static function connection(): PDO
    {
        static $pdo = null;

        if ($pdo === null) {
            $dbPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app.sqlite';
            $dir = dirname($dbPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            $pdo = new PDO('sqlite:' . $dbPath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }

        return $pdo;
    }

    public static function initialize(): void
    {
        $pdo = self::connection();

        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'user',
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            description TEXT NOT NULL,
            price REAL NOT NULL DEFAULT 0,
            category TEXT NOT NULL,
            whatsapp TEXT NOT NULL,
            icon TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT 'activo',
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            customer_name TEXT NOT NULL,
            customer_phone TEXT NOT NULL,
            product_id INTEGER,
            product_name TEXT NOT NULL,
            quantity INTEGER NOT NULL DEFAULT 1,
            status TEXT NOT NULL DEFAULT 'pendiente',
            notes TEXT,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )");

        $adminEmail = 'admin@cafeteria.local';
        $adminPassword = password_hash('admin123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT OR IGNORE INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)');
        $stmt->execute([
            ':name' => 'Administrador',
            ':email' => $adminEmail,
            ':password' => $adminPassword,
            ':role' => 'admin',
        ]);

        $seedProducts = [
            [
                'name' => 'Café Especial',
                'description' => 'Café aromático con espuma y un toque de canela.',
                'price' => 4500,
                'category' => 'Bebidas',
                'whatsapp' => '573001234567',
                'icon' => '☕',
            ],
            [
                'name' => 'Croissant Dulce',
                'description' => 'Croissant fresco con azúcar y mantequilla.',
                'price' => 3200,
                'category' => 'Panadería',
                'whatsapp' => '573001234567',
                'icon' => '🥐',
            ],
            [
                'name' => 'Combo Snack',
                'description' => 'Paquete de snacks con bebida y postre.',
                'price' => 7800,
                'category' => 'Snacks',
                'whatsapp' => '573001234567',
                'icon' => '🍪',
            ],
        ];

        foreach ($seedProducts as $product) {
            $check = $pdo->prepare('SELECT id FROM products WHERE name = :name');
            $check->execute([':name' => $product['name']]);
            if ($check->fetch()) {
                continue;
            }

            $stmt = $pdo->prepare('INSERT INTO products (name, description, price, category, whatsapp, icon, status) VALUES (:name, :description, :price, :category, :whatsapp, :icon, :status)');
            $stmt->execute([
                ':name' => $product['name'],
                ':description' => $product['description'],
                ':price' => $product['price'],
                ':category' => $product['category'],
                ':whatsapp' => $product['whatsapp'],
                ':icon' => $product['icon'],
                ':status' => 'activo',
            ]);
        }

        $sampleProductId = $pdo->query('SELECT id FROM products WHERE name = "Café Especial"')->fetchColumn();
        if ($sampleProductId) {
            $seedOrders = [
                [
                    'customer_name' => 'María López',
                    'customer_phone' => '573001112233',
                    'product_id' => $sampleProductId,
                    'product_name' => 'Café Especial',
                    'quantity' => 2,
                    'status' => 'pendiente',
                    'notes' => 'Entrega en la mañana',
                ],
                [
                    'customer_name' => 'Carlos Ruiz',
                    'customer_phone' => '573009998877',
                    'product_id' => $sampleProductId,
                    'product_name' => 'Café Especial',
                    'quantity' => 1,
                    'status' => 'confirmado',
                    'notes' => 'Recojo en tienda',
                ],
            ];

            foreach ($seedOrders as $order) {
                $checkOrder = $pdo->prepare('SELECT id FROM orders WHERE customer_name = :customer_name AND product_name = :product_name LIMIT 1');
                $checkOrder->execute([
                    ':customer_name' => $order['customer_name'],
                    ':product_name' => $order['product_name'],
                ]);
                if ($checkOrder->fetch()) {
                    continue;
                }

                $orderStmt = $pdo->prepare('INSERT INTO orders (customer_name, customer_phone, product_id, product_name, quantity, status, notes) VALUES (:customer_name, :customer_phone, :product_id, :product_name, :quantity, :status, :notes)');
                $orderStmt->execute([
                    ':customer_name' => $order['customer_name'],
                    ':customer_phone' => $order['customer_phone'],
                    ':product_id' => $order['product_id'],
                    ':product_name' => $order['product_name'],
                    ':quantity' => $order['quantity'],
                    ':status' => $order['status'],
                    ':notes' => $order['notes'],
                ]);
            }
        }
    }
}
