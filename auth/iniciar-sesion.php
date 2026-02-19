<?php
/**
 * Procesar login
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $redirect = $_POST['redirect'] ?? null;

    if (Auth::login($email, $password)) {
        // Redirigir según rol o a la página original
        if ($redirect) {
            header('Location: ' . $redirect);
        } elseif (Auth::hasRole('admin')) {
            header('Location: /admin/dashboard.php');
        } else {
            header('Location: /empleado/dashboard.php');
        }
        exit;
    } else {
        header('Location: /index.php?error=' . urlencode('Credenciales inválidas'));
        exit;
    }
}
