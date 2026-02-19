<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Auth.php';

Auth::requireRole('admin');

$db = Database::getInstance();

// Obtener filtros
$tipoFilter = $_GET['tipo'] ?? '';
$sucursalFilter = $_GET['sucursal'] ?? '';
$estadoFilter = $_GET['estado'] ?? '';

// Build query
$where = ['1=1'];
$params = [];

if ($tipoFilter) {
    $where[] = 'e.tipo = :tipo';
    $params['tipo'] = $tipoFilter;
}
if ($sucursalFilter) {
    $where[] = 'e.sucursal_id = :sucursal';
    $params['sucursal'] = $sucursalFilter;
}
if ($estadoFilter) {
    $where[] = 'e.estado = :estado';
    $params['estado'] = $estadoFilter;
}

$whereClause = implode(' AND ', $where);

// Obtener equipos
$equipos = $db->fetchAll("
    SELECT e.*, s.nombre as sucursal_nombre, emp.nombre as asignado_a_nombre
    FROM equipos e
    LEFT JOIN sucursales s ON e.sucursal_id = s.id
    LEFT JOIN empleados emp ON e.asignado_a = emp.id
    WHERE $whereClause
    ORDER BY e.created_at DESC
", $params);

// Stats
$stats = [
    'total' => $db->fetchOne("SELECT COUNT(*) as count FROM equipos")['count'],
    'disponibles' => $db->fetchOne("SELECT COUNT(*) as count FROM equipos WHERE estado = 'disponible'")['count'],
    'asignados' => $db->fetchOne("SELECT COUNT(*) as count FROM equipos WHERE estado = 'asignado'")['count'],
    'reparacion' => $db->fetchOne("SELECT COUNT(*) as count FROM equipos WHERE estado = 'en_reparacion'")['count'],
];

$sucursales = $db->fetchAll("SELECT * FROM sucursales WHERE activo = 1 ORDER BY nombre");

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Gestión de Equipos</h1>
            <p class="text-gray-600 mt-2">Inventario de computadoras y celulares</p>
        </div>
        <a href="/admin/equipo-crear.php" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nuevo Equipo
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Equipos</p>
                    <p class="text-3xl font-bold text-gray-800"><?= $stats['total'] ?></p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Disponibles</p>
                    <p class="text-3xl font-bold text-gray-800"><?= $stats['disponibles'] ?></p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Asignados</p>
                    <p class="text-3xl font-bold text-gray-800"><?= $stats['asignados'] ?></p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">En Reparación</p>
                    <p class="text-3xl font-bold text-gray-800"><?= $stats['reparacion'] ?></p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                <select name="tipo" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Todos</option>
                    <option value="computadora" <?= $tipoFilter == 'computadora' ? 'selected' : '' ?>>Computadora</option>
                    <option value="celular" <?= $tipoFilter == 'celular' ? 'selected' : '' ?>>Celular</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sucursal</label>
                <select name="sucursal" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Todas</option>
                    <?php foreach ($sucursales as $sucursal): ?>
                    <option value="<?= $sucursal['id'] ?>" <?= $sucursalFilter == $sucursal['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sucursal['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select name="estado" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Todos</option>
                    <option value="disponible" <?= $estadoFilter == 'disponible' ? 'selected' : '' ?>>Disponible</option>
                    <option value="asignado" <?= $estadoFilter == 'asignado' ? 'selected' : '' ?>>Asignado</option>
                    <option value="en_reparacion" <?= $estadoFilter == 'en_reparacion' ? 'selected' : '' ?>>En Reparación</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Equipment Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <?php if (empty($equipos)): ?>
        <div class="text-center py-12">
            <p class="text-gray-500">No hay equipos que coincidan con los filtros</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Marca/Modelo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serie</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sucursal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asignado a</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($equipos as $eq): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <?php if ($eq['tipo'] == 'computadora'): ?>
                                <svg class="h-6 w-6 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <?php else: ?>
                                <svg class="h-6 w-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <?php endif; ?>
                                <span class="text-sm font-medium text-gray-900 capitalize"><?= htmlspecialchars($eq['tipo']) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900"><?= htmlspecialchars($eq['marca']) ?></div>
                            <div class="text-sm text-gray-500"><?= htmlspecialchars($eq['modelo']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= htmlspecialchars($eq['numero_serie']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= htmlspecialchars($eq['sucursal_nombre'] ?? '-') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= htmlspecialchars($eq['asignado_a_nombre'] ?? '-') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                            $estadoColors = [
                                'disponible' => 'bg-green-100 text-green-800',
                                'asignado' => 'bg-yellow-100 text-yellow-800',
                                'en_reparacion' => 'bg-red-100 text-red-800',
                                'dado_de_baja' => 'bg-gray-100 text-gray-800'
                            ];
                            $estadoLabels = [
                                'disponible' => 'Disponible',
                                'asignado' => 'Asignado',
                                'en_reparacion' => 'En Reparación',
                                'dado_de_baja' => 'Dado de Baja'
                            ];
                            ?>
                            <span class="px-2 py-1 rounded-full text-xs font-medium <?= $estadoColors[$eq['estado']] ?? 'bg-gray-100 text-gray-800' ?>">
                                <?= $estadoLabels[$eq['estado']] ?? $eq['estado'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <a href="/admin/equipo-ver.php?id=<?= $eq['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                            <a href="/admin/equipo-editar.php?id=<?= $eq['id'] ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                            <button onclick="eliminarEquipo(<?= $eq['id'] ?>)" class="text-red-600 hover:text-red-900">Eliminar</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function eliminarEquipo(id) {
    if (confirm('¿Estás seguro de eliminar este equipo?')) {
        fetch('/api/equipo-eliminar.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.message);
            }
        });
    }
}
</script>

<?php
$content = ob_get_clean();
$titulo = 'Gestión de Equipos - GMB Responsivas';
require_once __DIR__ . '/../views/layout.php';
