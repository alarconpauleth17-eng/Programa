<?php

namespace App;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    // Configuración de conexión MySQL (XAMPP)
    private static string $host = 'localhost';
    private static string $port = '3306';
    private static string $dbName = 'programa_cafeteria';
    private static string $user = 'root';
    private static string $password = '';
    private static string $charset = 'utf8mb4';

    /**
     * Retorna la conexión PDO a MySQL.
     * Crea la base de datos si no existe.
     */
    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            // Primero conectar sin BD para crearla si no existe
            $dsn = "mysql:host=" . self::$host . ";port=" . self::$port . ";charset=" . self::$charset;
            try {
                $tmp = new PDO($dsn, self::$user, self::$password);
                $tmp->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $tmp->exec("CREATE DATABASE IF NOT EXISTS `" . self::$dbName . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $tmp = null;
            } catch (PDOException $e) {
                die("Error al conectar con MySQL: " . $e->getMessage());
            }

            // Conectar a la base de datos
            $dsnDb = "mysql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$dbName . ";charset=" . self::$charset;
            try {
                self::$pdo = new PDO($dsnDb, self::$user, self::$password);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                // Verificar si las tablas existen, si no, crearlas
                self::ensureTablesExist();
            } catch (PDOException $e) {
                die("Error al conectar con la base de datos: " . $e->getMessage());
            }
        }

        return self::$pdo;
    }

    private static function ensureTablesExist(): void
    {
        try {
            $result = self::$pdo->query("SHOW TABLES LIKE 'users'");
            if ($result->rowCount() == 0) {
                // Si no existe la tabla users, asumimos que faltan todas y ejecutamos migración básica
                self::runBasicMigration();
            }
        } catch (PDOException $e) {
            // Error al verificar, intentar migrar de todos modos
            self::runBasicMigration();
        }
    }

    private static function runBasicMigration(): void
    {
        // SQL para crear tablas (reutilizado de migrate.php)
        $queries = [
            "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(50) NOT NULL DEFAULT 'user',
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE IF NOT EXISTS products (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE IF NOT EXISTS orders (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ];

        foreach ($queries as $query) {
            self::$pdo->exec($query);
        }

        // Insertar admin por defecto si no existe
        $adminEmail = 'admin@cafeteria.local';
        $check = self::$pdo->prepare('SELECT id FROM users WHERE email = :email');
        $check->execute([':email' => $adminEmail]);
        if (!$check->fetch()) {
            $stmt = self::$pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)');
            $stmt->execute([
                ':name'     => 'Administrador',
                ':email'    => $adminEmail,
                ':password' => password_hash('admin123', PASSWORD_BCRYPT),
                ':role'     => 'admin',
            ]);
        }
    }
}
