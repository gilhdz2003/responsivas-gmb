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

$empleadoId = $input['id'] ?? null;
$numeroEmpleado = $input['numero_empleado'] ?? null;
$nombre = $input['nombre'] ?? null;
$puesto = $input['puesto'] ?? null;
$departamento = $input['departamento'] ?? null;
$sucursalId = $input['sucursal_id'] ?? null;
$fechaIngreso = $input['fecha_ingreso'] ?? null;

if (!$numeroEmpleado || !$nombre) {
    echo json_encode(['success' => false, 'message' => 'Número de empleado y nombre son requeridos']);
    exit;
}

try {
    $db->getConnection()->beginTransaction();

    if ($empleadoId) {
        // Actualizar
        $existe = $db->fetchOne("SELECT id FROM empleados WHERE numero_empleado = :num AND id != :id", ['num' => $numeroEmpleado, 'id' => $empleadoId]);
        if ($existe) { throw new Exception('El número de empleado ya existe'); }

        $db->update('empleados', ['numero_empleado' => $numeroEmpleado, 'nombre' => $nombre, 'puesto' => $puesto, 'departamento' => $departamento, 'sucursal_id' => $sucursalId ?: null, 'fecha_ingreso' => $fechaIngreso ?: null], 'id = :id', ['id' => $empleadoId]);

        $empleado = $db->fetchOne("SELECT usuario_id FROM empleados WHERE id = :id", ['id' => $empleadoId]);
        if ($empleado['usuario_id']) {
            $db->update('usuarios', ['nombre' => $nombre, 'sucursal_id' => $sucursalId ?: null], 'id = :id', ['id' => $empleado['usuario_id']]);
        }

        $message = 'Empleado actualizado correctamente';
        $responseData = ['empleado_id' => $empleadoId];
    } else {
        // Crear
        $existe = $db->fetchOne("SELECT id FROM empleados WHERE numero_empleado = :num", ['num' => $numeroEmpleado]);
        if ($existe) { throw new Exception('El número de empleado ya existe'); }

        $passwordTemp = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 12);
        $passwordHash = password_hash($passwordTemp, PASSWORD_DEFAULT);
        $emailTemp = "empleado{$numeroEmpleado}@temp.grupomb.com";

        $usuarioId = $db->insert('usuarios', ['email' => $emailTemp, 'password_hash' => $passwordHash, 'nombre' => $nombre, 'rol' => 'empleado', 'sucursal_id' => $sucursalId ?: null, 'activo' => 1]);

        $empleadoId = $db->insert('empleados', ['usuario_id' => $usuarioId, 'numero_empleado' => $numeroEmpleado, 'nombre' => $nombre, 'puesto' => $puesto, 'departamento' => $departamento, 'sucursal_id' => $sucursalId ?: null, 'fecha_ingreso' => $fechaIngreso ?: null, 'activo' => 1]);

        $message = 'Empleado creado correctamente';
        $responseData = ['empleado_id' => $empleadoId, 'password_temporal' => $passwordTemp, 'email_temporal' => $emailTemp];
    }

    $db->getConnection()->commit();

    echo json_encode(['success' => true, 'message' => $message, 'data' => $responseData]);

} catch (Exception $e) {
    $db->getConnection()->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
