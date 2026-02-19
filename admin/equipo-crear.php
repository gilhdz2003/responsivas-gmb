<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Auth.php';

Auth::requireRole('admin');

$db = Database::getInstance();
$sucursales = $db->fetchAll("SELECT * FROM sucursales WHERE activo = 1 ORDER BY nombre");
$empleados = $db->fetchAll("SELECT * FROM empleados WHERE activo = 1 ORDER BY nombre");

ob_start();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Nuevo Equipo</h1>
        <p class="text-gray-600 mt-2">Registrar equipo en el inventario</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-8">
        <form id="formEquipo" x-data="equipoForm()">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Equipo *</label>
                    <select name="tipo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Seleccionar...</option>
                        <option value="computadora">Computadora</option>
                        <option value="celular">Celular</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Marca *</label>
                    <input type="text" name="marca" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Dell, HP, Samsung...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Modelo *</label>
                    <input type="text" name="modelo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Latitude 5420, Galaxy S21...">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Número de Serie *</label>
                    <input type="text" name="numero_serie" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="SN único del equipo">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea name="descripcion" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Características adicionales..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sucursal</label>
                    <select name="sucursal_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Seleccionar...</option>
                        <?php foreach ($sucursales as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Asignar a</label>
                    <select name="asignado_a" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">No asignado</option>
                        <?php foreach ($empleados as $e): ?>
                        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select name="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="disponible">Disponible</option>
                        <option value="asignado">Asignado</option>
                        <option value="en_reparacion">En Reparación</option>
                    </select>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-4">
                <a href="/admin/equipos.php" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cancelar</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Guardar Equipo</button>
            </div>
        </form>
    </div>
</div>

<script>
function equipoForm() {
    return {
        async submitForm() {
            const form = document.getElementById('formEquipo');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            data.sucursal_id = data.sucursal_id || null;
            data.asignado_a = data.asignado_a || null;

            const response = await fetch('/api/equipo-guardar.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                alert('Equipo creado correctamente');
                window.location.href = '/admin/equipos.php';
            } else {
                alert(result.message);
            }
        }
    }
}
document.getElementById('formEquipo').addEventListener('submit', (e) => { e.preventDefault(); Alpine.evaluate(e.target, 'submitForm()'); });
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../views/layout.php';
