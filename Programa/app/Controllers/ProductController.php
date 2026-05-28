<?php

namespace App\Controllers;

use App\Controller;
use App\Database;

class ProductController extends Controller
{
    public function manage(): void
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            $_SESSION['flash'] = 'Necesitas permisos de administrador';
            $this->redirect('/login');
        }

        $pdo = Database::connection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = (float) ($_POST['price'] ?? 0);
            $category = trim($_POST['category'] ?? '');
            $whatsapp = preg_replace('/[^0-9]/', '', $_POST['whatsapp'] ?? '');
            $icon = trim($_POST['icon'] ?? '🛍️');

            if ($name === '' || $description === '' || $price <= 0 || $category === '' || $whatsapp === '') {
                $_SESSION['flash'] = 'Todos los campos del producto son obligatorios';
                $this->redirect('/admin');
            }

            $stmt = $pdo->prepare('INSERT INTO products (name, description, price, category, whatsapp, icon, status) VALUES (:name, :description, :price, :category, :whatsapp, :icon, :status)');
            $stmt->execute([
                ':name' => $name,
                ':description' => $description,
                ':price' => $price,
                ':category' => $category,
                ':whatsapp' => $whatsapp,
                ':icon' => $icon,
                ':status' => 'activo',
            ]);

            $_SESSION['flash'] = 'Producto creado correctamente';
            $this->redirect('/admin');
        }

        $this->redirect('/admin');
    }

    public function import(): void
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            $_SESSION['flash'] = 'Necesitas permisos de administrador';
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin');
        }

        if (!isset($_FILES['catalog_file']) || $_FILES['catalog_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash'] = 'No se recibió el archivo del catálogo';
            $this->redirect('/admin');
        }

        $handle = fopen($_FILES['catalog_file']['tmp_name'], 'r');
        if (!$handle) {
            $_SESSION['flash'] = 'No fue posible leer el archivo';
            $this->redirect('/admin');
        }

        $header = fgetcsv($handle);
        if (!$header || !in_array('name', $header, true) || !in_array('description', $header, true) || !in_array('price', $header, true)) {
            fclose($handle);
            $_SESSION['flash'] = 'Formato de CSV inválido. Usa las columnas name, description, price, category, whatsapp, icon, status';
            $this->redirect('/admin');
        }

        $indexMap = array_flip($header);
        $pdo = Database::connection();
        $imported = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === 0 || array_filter($row) === []) {
                continue;
            }

            $name = trim($row[$indexMap['name']] ?? '');
            $description = trim($row[$indexMap['description']] ?? '');
            $price = (float) ($row[$indexMap['price']] ?? 0);
            $category = trim($row[$indexMap['category']] ?? 'General');
            $whatsapp = preg_replace('/[^0-9]/', '', $row[$indexMap['whatsapp']] ?? '');
            $icon = trim($row[$indexMap['icon']] ?? '🛍️');
            $status = trim($row[$indexMap['status']] ?? 'activo');

            if ($name === '' || $description === '' || $price <= 0) {
                $skipped++;
                continue;
            }

            $check = $pdo->prepare('SELECT id FROM products WHERE name = :name');
            $check->execute([':name' => $name]);
            if ($check->fetch()) {
                $skipped++;
                continue;
            }

            $stmt = $pdo->prepare('INSERT INTO products (name, description, price, category, whatsapp, icon, status) VALUES (:name, :description, :price, :category, :whatsapp, :icon, :status)');
            $stmt->execute([
                ':name' => $name,
                ':description' => $description,
                ':price' => $price,
                ':category' => $category,
                ':whatsapp' => $whatsapp,
                ':icon' => $icon,
                ':status' => $status,
            ]);
            $imported++;
        }

        fclose($handle);

        $_SESSION['flash'] = 'Importación finalizada: ' . $imported . ' productos agregados, ' . $skipped . ' omitidos.';
        $this->redirect('/admin');
    }
}
