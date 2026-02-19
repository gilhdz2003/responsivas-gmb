<?php
/**
 * GMB Responsivas - Database Configuration
 *
 * Variables de entorno para conexión a base de datos
 * Copiar este archivo como db_credentials.php y llenar con valores reales
 */

return [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'port' => getenv('DB_PORT') ?: '3306',
    'database' => getenv('DB_NAME') ?: 'grupomb_responsivas',
    'username' => getenv('DB_USER') ?: '',
    'password' => getenv('DB_PASS') ?: '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
];
