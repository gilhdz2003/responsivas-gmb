<?php
/**
 * GMB Responsivas - Landing / Login
 *
 * Punto de entrada principal de la aplicación
 */

session_start();

// Redirigir según autenticación
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: empleado/dashboard.php');
    }
    exit;
}

require_once __DIR__ . '/views/login.php';
