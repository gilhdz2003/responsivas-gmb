<?php
/**
 * Cerrar sesión
 */

require_once __DIR__ . '/../utils/Auth.php';

Auth::logout();
header('Location: /index.php');
exit;
