<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

Auth::requireRole('admin');

$db = Database::getInstance();
$input = json_decode(file_get_contents('php://input'), true);

$tipo = $input['tipo'] ?? '';
$marca = $input['marca'] ?? '';
$modelo = $input['modelo'] ?? '';
$numeroSerie = $input['numero_serie'] ?? '';
$sucursalId = $input['sucursal_id'] ?? null;
$asignadoA = $input['asignado_a'] ?? null;
$estado = $input['estado'] ?? 'disponible';
$descripcion = $input['descripcion'] ?? '';

if (!$tipo || !$marca || !$modelo || !$numeroSerie) {
    echo json_encode(['success' => false, 'message' => 'Tipo, marca, modelo y número de serie son requeridos']);
    exit;
}

if (!in_array($tipo, ['computadora', 'celular'])) {
    echo json_encode(['success' => false, 'message' => 'Tipo de equipo inválido']);
    exit;
}

try {
    $existe = $db->fetchOne("SELECT id FROM equipos WHERE numero_serie = :ns", ['ns' => $numeroSerie]);
    if ($existe) {
        echo json_encode(['success' => false, 'message' => 'El número de serie ya existe']);
        exit;
    }

    $db->insert('equipos', [
        'tipo' => $tipo,
        'marca' => $marca,
        'modelo' => $modelo,
        'numero_serie' => $numeroSerie,
        'descripcion' => $descripcion,
        'sucursal_id' => $sucursalId,
        'asignado_a' => $asignadoA,
        'estado' => $asignadoA ? 'asignado' : $estado
    ]);

    echo json_encode(['success' => true, 'message' => 'Equipo creado correctamente']);
} catch (Exception $e) {
    error_log("Error creando equipo: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al crear equipo']);
}
