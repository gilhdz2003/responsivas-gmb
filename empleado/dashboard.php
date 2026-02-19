<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Auth.php';

Auth::require();

// Verificar que el usuario sea empleado
if (Auth::hasRole('admin')) {
    header('Location: /admin/dashboard.php');
    exit;
}

$db = Database::getInstance();
$user = Auth::user();

// Obtener empleado asociado
$empleado = $db->fetchOne(
    "SELECT * FROM empleados WHERE usuario_id = :uid",
    ['uid' => $user['id']]
);

// Responsivas pendientes
$pendientes = $db->fetchAll("
    SELECT r.*, eq.tipo, eq.marca, eq.modelo, eq.numero_serie, s.nombre as sucursal_nombre
    FROM responsivas r
    JOIN equipos eq ON r.equipo_id = eq.id
    JOIN sucursales s ON r.sucursal_id = s.id
    WHERE r.empleado_id = :eid AND r.estatus = 'pendiente'
    ORDER BY r.created_at DESC
", ['eid' => $empleado['id']]);

// Responsivas firmadas
$firmadas = $db->fetchAll("
    SELECT r.*, eq.tipo, eq.marca, eq.modelo, s.nombre as sucursal_nombre
    FROM responsivas r
    JOIN equipos eq ON r.equipo_id = eq.id
    JOIN sucursales s ON r.sucursal_id = s.id
    WHERE r.empleado_id = :eid AND r.estatus = 'firmada'
    ORDER BY r.fecha_firma DESC
    LIMIT 10
", ['eid' => $empleado['id']]);

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Mi Panel</h1>
        <p class="text-gray-600 mt-2">Bienvenido, <?= htmlspecialchars($user['nombre']) ?></p>
    </div>

    <!-- Alert: Pendientes -->
    <?php if (!empty($pendientes)): ?>
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-8">
        <div class="flex items-center">
            <div class="bg-yellow-100 p-3 rounded-full mr-4">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-yellow-800">Tienes <?= count($pendientes) ?> responsiva(s) pendiente(s) de firma</h3>
                <p class="text-yellow-700 mt-1">Por favor firma los documentos asignados</p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Pendientes</p>
                    <p class="text-3xl font-bold text-gray-800"><?= count($pendientes) ?></p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Firmadas</p>
                    <p class="text-3xl font-bold text-gray-800"><?= count($firmadas) ?></p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Mi Sucursal</p>
                    <p class="text-xl font-bold text-gray-800"><?= htmlspecialchars($user['sucursal_nombre'] ?? 'N/A') ?></p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsivas Pendientes -->
    <?php if (!empty($pendientes)): ?>
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Pendientes de Firma</h2>
        <div class="space-y-4">
            <?php foreach ($pendientes as $r): ?>
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                <?= htmlspecialchars($r['tipo']) ?>
                            </span>
                            <span class="text-gray-600 font-medium">
                                <?= htmlspecialchars($r['marca'] . ' ' . $r['modelo']) ?>
                            </span>
                        </div>
                        <p class="text-gray-500 text-sm mt-2">
                            Serie: <?= htmlspecialchars($r['numero_serie']) ?> |
                            Sucursal: <?= htmlspecialchars($r['sucursal_nombre']) ?>
                        </p>
                        <p class="text-gray-400 text-sm mt-1">
                            Creada: <?= date('d/m/Y', strtotime($r['created_at'])) ?>
                        </p>
                    </div>
                    <a href="/empleado/firmar.php?id=<?= $r['id'] ?>"
                       class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
                        Firmar Ahora
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8 text-center">
        <svg class="h-16 w-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h3 class="text-lg font-semibold text-gray-800">¡Todo al día!</h3>
        <p class="text-gray-500 mt-2">No tienes responsivas pendientes de firma</p>
    </div>
    <?php endif; ?>

    <!-- Historial -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Mi Historial</h2>

        <?php if (empty($firmadas)): ?>
        <p class="text-gray-500 text-center py-8">No hay responsivas firmadas aún</p>
        <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($firmadas as $r): ?>
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div>
                    <span class="text-gray-800 font-medium"><?= htmlspecialchars($r['tipo']) ?></span>
                    <span class="text-gray-500 text-sm ml-2"><?= htmlspecialchars($r['marca']) ?></span>
                </div>
                <div class="text-right">
                    <p class="text-green-600 font-medium">Firmada</p>
                    <p class="text-gray-400 text-sm"><?= date('d/m/Y H:i', strtotime($r['fecha_firma'])) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();

$titulo = 'Mi Panel - GMB Responsivas';
require_once __DIR__ . '/../views/layout.php';
