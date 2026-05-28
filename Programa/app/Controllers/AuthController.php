<?php

namespace App\Controllers;

use App\Controller;
use App\Database;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view('auth/login', [
            'pageTitle' => 'Iniciar sesión',
        ]);
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['flash'] = 'Completa todos los campos';
            $this->redirect('/login');
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['flash'] = 'Credenciales inválidas';
            $this->redirect('/login');
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];

        $_SESSION['flash'] = 'Bienvenido de nuevo';

        if ($user['role'] === 'admin') {
            $this->redirect('/admin');
        }

        $this->redirect('/');
    }

    public function showRegister(): void
    {
        $this->view('auth/register', [
            'pageTitle' => 'Registrarse',
        ]);
    }

    public function register(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($name === '' || $email === '' || $password === '' || $confirm === '') {
            $_SESSION['flash'] = 'Completa todos los campos';
            $this->redirect('/register');
        }

        if ($password !== $confirm) {
            $_SESSION['flash'] = 'Las contraseñas no coinciden';
            $this->redirect('/register');
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)');
        try {
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => password_hash($password, PASSWORD_BCRYPT),
                ':role' => 'user',
            ]);
        } catch (\PDOException $e) {
            $_SESSION['flash'] = 'El correo ya está registrado';
            $this->redirect('/register');
        }

        $user = $pdo->prepare('SELECT * FROM users WHERE email = :email');
        $user->execute([':email' => $email]);
        $account = $user->fetch();

        $_SESSION['user'] = [
            'id' => $account['id'],
            'name' => $account['name'],
            'email' => $account['email'],
            'role' => $account['role'],
        ];

        $_SESSION['flash'] = 'Cuenta creada correctamente';
        $this->redirect('/');
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
        $_SESSION['flash'] = 'Sesión cerrada';
        $this->redirect('/');
    }
}
