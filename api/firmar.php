<?php
/**
 * API: Firmar Responsiva
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Auth.php';

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

Auth::require();

// Verificar que sea empleado
if (Auth::hasRole('admin')) {
    echo json_encode(['success' => false, 'message' => 'Los administradores no firman responsivas']);
    exit;
}

$db = Database::getInstance();
$user = Auth::user();

// Obtener datos
$input = json_decode(file_get_contents('php://input'), true);
$responsivaId = $input['responsiva_id'] ?? null;
$firmaData = $input['firma'] ?? null;

if (!$responsivaId || !$firmaData) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

// Obtener empleado
$empleado = $db->fetchOne(
    "SELECT id FROM empleados WHERE usuario_id = :uid",
    ['uid' => $user['id']]
);

if (!$empleado) {
    echo json_encode(['success' => false, 'message' => 'Empleado no encontrado']);
    exit;
}

// Verificar que la responsiva pertenezca al empleado y esté pendiente
$responsiva = $db->fetchOne(
    "SELECT * FROM responsivas WHERE id = :rid AND empleado_id = :eid AND estatus = 'pendiente'",
    ['rid' => $responsivaId, 'eid' => $empleado['id']]
);

if (!$responsiva) {
    echo json_encode(['success' => false, 'message' => 'Responsiva no encontrada o ya firmada']);
    exit;
}

try {
    // Actualizar responsiva
    $db->query(
        "UPDATE responsivas SET
            estatus = 'firmada',
            firma_digital = :firma,
            fecha_firma = NOW(),
            ip_firma = :ip,
            user_agent_firma = :ua
        WHERE id = :rid",
        [
            'firma' => $firmaData,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'ua' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'rid' => $responsivaId
        ]
    );

    // Actualizar estado del equipo
    $db->query(
        "UPDATE equipos SET estado = 'asignado', asignado_a = :eid WHERE id = :eqid",
        ['eid' => $empleado['id'], 'eqid' => $responsiva['equipo_id']]
    );

    // Crear notificación para admins
    $admins = $db->fetchAll("SELECT id FROM usuarios WHERE rol = 'admin' AND activo = 1");
    foreach ($admins as $admin) {
        $db->insert('notificaciones', [
            'usuario_id' => $admin['id'],
            'tipo' => 'responsiva_firmada',
            'titulo' => 'Nueva responsiva firmada',
            'mensaje' => "El empleado {$user['nombre']} ha firmado su responsiva de equipo."
        ]);
    }

    // Generar PDF
    try {
        require_once __DIR__ . '/../utils/PDFGenerator.php';
        $pdfGen = new PDFGenerator();
        $pdfGen->generateResponsivaPDF($responsivaId);
    } catch (Exception $pdfEx) {
        error_log("Error generando PDF: " . $pdfEx->getMessage());
        // No fallar el proceso si el PDF falla
    }

    // Enviar notificación por email
    try {
        require_once __DIR__ . '/../utils/Notifier.php';
        $notifier = new Notifier();
        $notifier->notifyResponsivaFirmada($responsivaId);
    } catch (Exception $emailEx) {
        error_log("Error enviando email: " . $emailEx->getMessage());
        // No fallar el proceso si el email falla
    }

    echo json_encode([
        'success' => true,
        'message' => 'Responsiva firmada correctamente',
        'responsiva_id' => $responsivaId
    ]);

} catch (Exception $e) {
    error_log("Error firmando responsiva: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al procesar la firma']);
}
