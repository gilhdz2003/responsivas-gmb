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

$empleadoId = $input['empleado_id'] ?? null;
$equipoId = $input['equipo_id'] ?? null;
$sucursalId = $input['sucursal_id'] ?? null;
$tipoEquipo = $input['tipo_equipo'] ?? null;

if (!$empleadoId || !$equipoId || !$sucursalId || !$tipoEquipo) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

try {
    $db->getConnection()->beginTransaction();

    $empleado = $db->fetchOne("SELECT * FROM empleados WHERE id = :id AND activo = 1", ['id' => $empleadoId]);
    if (!$empleadoId) { throw new Exception('Empleado no encontrado'); }

    $equipo = $db->fetchOne("SELECT * FROM equipos WHERE id = :id AND estado = 'disponible'", ['id' => $equipoId]);
    if (!$equipo) { throw new Exception('Equipo no disponible'); }

    if ($equipo['tipo'] !== $tipoEquipo) { throw new Exception('El tipo de equipo no coincide'); }

    $codigoVerificacion = bin2hex(random_bytes(16));
    while ($db->fetchOne("SELECT id FROM responsivas WHERE codigo_verificacion = :cv", ['cv' => $codigoVerificacion])) {
        $codigoVerificacion = bin2hex(random_bytes(16));
    }

    $responsivaId = $db->insert('responsivas', [
        'empleado_id' => $empleadoId,
        'equipo_id' => $equipoId,
        'sucursal_id' => $sucursalId,
        'tipo_equipo' => $tipoEquipo,
        'fecha_emision' => date('Y-m-d'),
        'estatus' => 'pendiente',
        'codigo_verificacion' => $codigoVerificacion
    ]);

    $db->update('equipos', ['estado' => 'asignado', 'asignado_a' => $empleadoId], 'id = :id', ['id' => $equipoId]);

    $db->insert('equipo_historial', [
        'equipo_id' => $equipoId,
        'accion' => 'asignado',
        'empleado_id' => $empleadoId,
        'responsable_id' => Auth::user()['id']
    ]);

    if ($empleado['usuario_id']) {
        $db->insert('notificaciones', [
            'usuario_id' => $empleado['usuario_id'],
            'tipo' => 'responsiva_pendiente',
            'titulo' => 'Nueva responsiva pendiente',
            'mensaje' => "Tienes una nueva responsiva de {$tipoEquipo} por firmar."
        ]);
    }

    $db->getConnection()->commit();

    echo json_encode(['success' => true, 'message' => 'Responsiva creada correctamente', 'responsiva_id' => $responsivaId]);

} catch (Exception $e) {
    $db->getConnection()->rollBack();
    error_log("Error creando responsiva: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
