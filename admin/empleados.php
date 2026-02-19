<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Auth.php';

Auth::requireRole('admin');

$db = Database::getInstance();
$empleados = $db->fetchAll("
    SELECT e.*, s.nombre as sucursal_nombre, u.email,
           (SELECT COUNT(*) FROM responsivas r WHERE r.empleado_id = e.id AND r.estatus = 'firmada') as total_responsivas
    FROM empleados e
    LEFT JOIN sucursales s ON e.sucursal_id = s.id
    LEFT JOIN usuarios u ON e.usuario_id = u.id
    ORDER BY e.nombre
");

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Empleados</h1>
            <p class="text-gray-600 mt-2">Gestionar empleados del sistema</p>
        </div>
        <a href="/admin/empleado-crear.php" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Nuevo Empleado</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <p class="text-gray-500 text-sm">Total Empleados</p>
            <p class="text-3xl font-bold text-gray-800"><?= count($empleados) ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
            <p class="text-gray-500 text-sm">Activos</p>
            <p class="text-3xl font-bold text-gray-800"><?= count(array_filter($empleados, fn($e) => $e['activo'])) ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
            <p class="text-gray-500 text-sm">Con Responsivas</p>
            <p class="text-3xl font-bold text-gray-800"><?= count(array_filter($empleados, fn($e) => $e['total_responsivas'] > 0)) ?></p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <?php if (empty($empleados)): ?>
        <div class="text-center py-12">
            <p class="text-gray-500">No hay empleados. Crea el primero.</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empleado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Puesto/Depto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sucursal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Responsivas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($empleados as $e): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-800 font-medium mr-3">
                                    <?= substr(htmlspecialchars($e['nombre']), 0, 2) ?>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($e['nombre']) ?></div>
                                    <div class="text-sm text-gray-500">#<?= htmlspecialchars($e['numero_empleado']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900"><?= htmlspecialchars($e['puesto'] ?? '-') ?></div>
                            <div class="text-sm text-gray-500"><?= htmlspecialchars($e['departamento'] ?? '-') ?></div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500"><?= htmlspecialchars($e['sucursal_nombre'] ?? '-') ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><?= $e['total_responsivas'] ?> activas</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium <?= $e['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= $e['activo'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <a href="/admin/empleado-editar.php?id=<?= $e['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-4">Editar</a>
                        </td>
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
require_once __DIR__ . '/../views/layout.php';
