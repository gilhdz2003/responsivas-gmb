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
$id = $input['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID requerido']);
    exit;
}

try {
    $equipo = $db->fetchOne("SELECT * FROM equipos WHERE id = :id", ['id' => $id]);
    if (!$equipo) {
        echo json_encode(['success' => false, 'message' => 'Equipo no encontrado']);
        exit;
    }

    $responsivasActivas = $db->fetchOne("SELECT COUNT(*) as count FROM responsivas WHERE equipo_id = :eq AND estatus = 'firmada'", ['eq' => $id]);
    if ($responsivasActivas['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'No se puede eliminar: tiene responsivas activas']);
        exit;
    }

    $db->update('equipos', ['estado' => 'dado_de_baja', 'asignado_a' => null], 'id = :id', ['id' => $id]);

    echo json_encode(['success' => true, 'message' => 'Equipo eliminado correctamente']);
} catch (Exception $e) {
    error_log("Error eliminando equipo: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al eliminar']);
}
