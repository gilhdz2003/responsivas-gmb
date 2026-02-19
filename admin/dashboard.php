<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Auth.php';

Auth::requireRole('admin');

$db = Database::getInstance();

// Estadísticas
$stats = [
    'pendientes' => $db->fetchOne("SELECT COUNT(*) as count FROM responsivas WHERE estatus = 'pendiente'")['count'],
    'firmadas_hoy' => $db->fetchOne("SELECT COUNT(*) as count FROM responsivas WHERE DATE(fecha_firma) = CURDATE()")['count'],
    'total_firmadas' => $db->fetchOne("SELECT COUNT(*) as count FROM responsivas WHERE estatus = 'firmada'")['count'],
    'equipos_asignados' => $db->fetchOne("SELECT COUNT(*) as count FROM equipos WHERE estado = 'asignado'")['count'],
];

// Responsivas recientes
$recientes = $db->fetchAll("
    SELECT r.*, e.nombre as empleado_nombre, eq.tipo as equipo_tipo, eq.marca, eq.modelo, s.nombre as sucursal
    FROM responsivas r
    JOIN empleados e ON r.empleado_id = e.id
    JOIN equipos eq ON r.equipo_id = eq.id
    JOIN sucursales s ON r.sucursal_id = s.id
    ORDER BY r.created_at DESC
    LIMIT 10
");

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Panel de Administración</h1>
        <p class="text-gray-600 mt-2">Gestión de responsivas y equipos</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Pendientes de Firma</p>
                    <p class="text-3xl font-bold text-gray-800"><?= $stats['pendientes'] ?></p>
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
                    <p class="text-gray-500 text-sm">Firmadas Hoy</p>
                    <p class="text-3xl font-bold text-gray-800"><?= $stats['firmadas_hoy'] ?></p>
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
                    <p class="text-gray-500 text-sm">Total Firmadas</p>
                    <p class="text-3xl font-bold text-gray-800"><?= $stats['total_firmadas'] ?></p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Equipos Asignados</p>
                    <p class="text-3xl font-bold text-gray-800"><?= $stats['equipos_asignados'] ?></p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Acciones Rápidas</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="/admin/crear-responsiva.php" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                <svg class="h-6 w-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span class="font-medium text-blue-800">Nueva Responsiva</span>
            </a>

            <a href="/admin/equipos.php" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                <svg class="h-6 w-6 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span class="font-medium text-purple-800">Gestionar Equipos</span>
            </a>

            <a href="/admin/empleados.php" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                <svg class="h-6 w-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="font-medium text-green-800">Empleados</span>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Actividad Reciente</h2>

        <?php if (empty($recientes)): ?>
        <p class="text-gray-500 text-center py-8">No hay actividad reciente</p>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 text-gray-600 font-medium">Empleado</th>
                        <th class="text-left py-3 px-4 text-gray-600 font-medium">Equipo</th>
                        <th class="text-left py-3 px-4 text-gray-600 font-medium">Sucursal</th>
                        <th class="text-left py-3 px-4 text-gray-600 font-medium">Estado</th>
                        <th class="text-left py-3 px-4 text-gray-600 font-medium">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recientes as $r): ?>
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-3 px-4"><?= htmlspecialchars($r['empleado_nombre']) ?></td>
                        <td class="py-3 px-4">
                            <span class="text-gray-800"><?= htmlspecialchars($r['equipo_tipo']) ?></span>
                            <span class="text-gray-500 text-sm ml-1"><?= htmlspecialchars($r['marca'] . ' ' . $r['modelo']) ?></span>
                        </td>
                        <td class="py-3 px-4 text-gray-600"><?= htmlspecialchars($r['sucursal']) ?></td>
                        <td class="py-3 px-4">
                            <?php if ($r['estatus'] == 'firmada'): ?>
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">Firmada</span>
                            <?php else: ?>
                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">Pendiente</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-gray-600"><?= date('d/m/Y', strtotime($r['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();

$titulo = 'Panel Admin - GMB Responsivas';
require_once __DIR__ . '/../views/layout.php';
