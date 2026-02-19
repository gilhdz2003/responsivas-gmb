<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Auth.php';

Auth::requireRole('admin');

$query = $_GET['q'] ?? '';
if (strlen($query) < 2) { echo json_encode([]); exit; }

$db = Database::getInstance();
$empleados = $db->fetchAll("
    SELECT e.id, e.nombre, e.numero_empleado, e.puesto, s.nombre as sucursal_nombre, s.id as sucursal_id
    FROM empleados e
    LEFT JOIN sucursales s ON e.sucursal_id = s.id
    WHERE e.activo = 1 AND (e.nombre LIKE :q OR e.numero_empleado LIKE :q)
    ORDER BY e.nombre LIMIT 50
", ['q' => "%$query%"]);

echo json_encode($empleados);
