<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Auth.php';

Auth::requireRole('admin');

$tipo = $_GET['tipo'] ?? '';
$query = $_GET['q'] ?? '';

if (!$tipo || strlen($query) < 2) { echo json_encode([]); exit; }

$db = Database::getInstance();
$equipos = $db->fetchAll("
    SELECT e.id, e.tipo, e.marca, e.modelo, e.numero_serie, s.nombre as sucursal_nombre
    FROM equipos e
    LEFT JOIN sucursales s ON e.sucursal_id = s.id
    WHERE e.estado = 'disponible' AND e.tipo = :tipo AND (e.marca LIKE :q OR e.modelo LIKE :q OR e.numero_serie LIKE :q)
    ORDER BY e.marca, e.modelo LIMIT 50
", ['tipo' => $tipo, 'q' => "%$query%"]);

echo json_encode($equipos);
