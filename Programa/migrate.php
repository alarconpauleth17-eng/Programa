<?php
/**
 * Script de migración para la base de datos.
 * Ejecutar una sola vez desde el navegador:
 *   http://localhost/Programa/Programa/migrate.php
 *
 * Crea las tablas e inserta datos iniciales (seeds).
 */

require __DIR__ . '/app/Database.php';

use App\Database;

echo "<pre>";
echo "=== Migración de Base de Datos ===\n\n";

try {
    $pdo = Database::connection();
    echo "[OK] Conexión a MySQL establecida.\n";

    // ─── Tabla: users ───
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(50) NOT NULL DEFAULT 'user',
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "[OK] Tabla 'users' creada.\n";

    // ─── Tabla: products ───
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        category VARCHAR(100) NOT NULL,
        whatsapp VARCHAR(20) NOT NULL,
        icon VARCHAR(50) NOT NULL,
        status VARCHAR(50) NOT NULL DEFAULT 'activo',
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "[OK] Tabla 'products' creada.\n";

    // ─── Tabla: orders ───
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(255) NOT NULL,
        customer_phone VARCHAR(20) NOT NULL,
        product_id INT,
        product_name VARCHAR(255) NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        status VARCHAR(50) NOT NULL DEFAULT 'pendiente',
        notes TEXT,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "[OK] Tabla 'orders' creada.\n";

    // ─── Seed: Admin ───
    $adminEmail = 'admin@cafeteria.local';
    $check = $pdo->prepare('SELECT id FROM users WHERE email = :email');
    $check->execute([':email' => $adminEmail]);

    if (!$check->fetch()) {
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)');
        $stmt->execute([
            ':name'     => 'Administrador',
            ':email'    => $adminEmail,
            ':password' => password_hash('admin123', PASSWORD_BCRYPT),
            ':role'     => 'admin',
        ]);
        echo "[OK] Usuario admin creado (admin@cafeteria.local / admin123).\n";
    } else {
        echo "[--] Usuario admin ya existe, se omitió.\n";
    }

    // ─── Seed: Productos ───
    $seedProducts = [
        [
            'name'        => 'Café Especial',
            'description' => 'Café aromático con espuma y un toque de canela.',
            'price'       => 4500,
            'category'    => 'Bebidas',
            'whatsapp'    => '573001234567',
            'icon'        => '☕',
        ],
        [
            'name'        => 'Croissant Dulce',
            'description' => 'Croissant fresco con azúcar y mantequilla.',
            'price'       => 3200,
            'category'    => 'Panadería',
            'whatsapp'    => '573001234567',
            'icon'        => '🥐',
        ],
        [
            'name'        => 'Combo Snack',
            'description' => 'Paquete de snacks con bebida y postre.',
            'price'       => 7800,
            'category'    => 'Snacks',
            'whatsapp'    => '573001234567',
            'icon'        => '🍪',
        ],
    ];

    foreach ($seedProducts as $product) {
        $check = $pdo->prepare('SELECT id FROM products WHERE name = :name');
        $check->execute([':name' => $product['name']]);
        if ($check->fetch()) {
            echo "[--] Producto '{$product['name']}' ya existe, se omitió.\n";
            continue;
        }

        $stmt = $pdo->prepare('INSERT INTO products (name, description, price, category, whatsapp, icon, status) VALUES (:name, :description, :price, :category, :whatsapp, :icon, :status)');
        $stmt->execute([
            ':name'        => $product['name'],
            ':description' => $product['description'],
            ':price'       => $product['price'],
            ':category'    => $product['category'],
            ':whatsapp'    => $product['whatsapp'],
            ':icon'        => $product['icon'],
            ':status'      => 'activo',
        ]);
        echo "[OK] Producto '{$product['name']}' insertado.\n";
    }

    // ─── Seed: Pedidos de ejemplo ───
    $cafeId = $pdo->query("SELECT id FROM products WHERE name = 'Café Especial'")->fetchColumn();

    if ($cafeId) {
        $seedOrders = [
            [
                'customer_name'  => 'María López',
                'customer_phone' => '573001112233',
                'product_id'     => $cafeId,
                'product_name'   => 'Café Especial',
                'quantity'       => 2,
                'status'         => 'pendiente',
                'notes'          => 'Entrega en la mañana',
            ],
            [
                'customer_name'  => 'Carlos Ruiz',
                'customer_phone' => '573009998877',
                'product_id'     => $cafeId,
                'product_name'   => 'Café Especial',
                'quantity'       => 1,
                'status'         => 'confirmado',
                'notes'          => 'Recojo en tienda',
            ],
        ];

        foreach ($seedOrders as $order) {
            $checkOrder = $pdo->prepare('SELECT id FROM orders WHERE customer_name = :customer_name AND product_name = :product_name LIMIT 1');
            $checkOrder->execute([
                ':customer_name' => $order['customer_name'],
                ':product_name'  => $order['product_name'],
            ]);
            if ($checkOrder->fetch()) {
                echo "[--] Pedido de '{$order['customer_name']}' ya existe, se omitió.\n";
                continue;
            }

            $orderStmt = $pdo->prepare('INSERT INTO orders (customer_name, customer_phone, product_id, product_name, quantity, status, notes) VALUES (:customer_name, :customer_phone, :product_id, :product_name, :quantity, :status, :notes)');
            $orderStmt->execute([
                ':customer_name'  => $order['customer_name'],
                ':customer_phone' => $order['customer_phone'],
                ':product_id'     => $order['product_id'],
                ':product_name'   => $order['product_name'],
                ':quantity'       => $order['quantity'],
                ':status'         => $order['status'],
                ':notes'          => $order['notes'],
            ]);
            echo "[OK] Pedido de '{$order['customer_name']}' insertado.\n";
        }
    }

    echo "\n=== Migración completada con éxito ===\n";
} catch (PDOException $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}

echo "</pre>";
