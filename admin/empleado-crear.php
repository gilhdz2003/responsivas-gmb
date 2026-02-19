<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Auth.php';

Auth::requireRole('admin');

$db = Database::getInstance();
$sucursales = $db->fetchAll("SELECT * FROM sucursales WHERE activo = 1");

ob_start();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="empleadoForm()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Nuevo Empleado</h1>
        <p class="text-gray-600 mt-2">Dar de alta un empleado en el sistema</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-8">
        <form id="formEmpleado">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Número de Empleado *</label>
                    <input type="text" name="numero_empleado" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="EJ: 12345">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo *</label>
                    <input type="text" name="nombre" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="EJ: Juan Pérez López">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Puesto</label>
                    <input type="text" name="puesto" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="EJ: Ajustador">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Departamento</label>
                    <input type="text" name="departamento" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="EJ: Siniestros">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Ingreso</label>
                    <input type="date" name="fecha_ingreso" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>

            <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                <h3 class="text-sm font-medium text-blue-800 mb-2">Información de Acceso</h3>
                <p class="text-sm text-blue-700">Se creará automáticamente un usuario con email temporal: <code>empleado{numero}@temp.grupomb.com</code> y una contraseña generada.</p>
            </div>

            <div class="mt-8 flex justify-end space-x-4">
                <a href="/admin/empleados.php" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cancelar</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Crear Empleado</button>
            </div>
        </form>
    </div>

    <!-- Modal Credenciales -->
    <div x-show="showCredentials" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4">
            <div class="text-center mb-6">
                <div class="mx-auto h-12 w-12 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Empleado Creado</h3>
                <p class="text-sm text-gray-500 mt-2">Credenciales de acceso (muestra esto una sola vez)</p>
            </div>
            <div class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-500 uppercase">Email</p>
                    <p class="text-lg font-mono font-medium text-gray-900 mt-1" x-text="credentials.email"></p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-500 uppercase">Contraseña</p>
                    <p class="text-lg font-mono font-medium text-gray-900 mt-1" x-text="credentials.password"></p>
                </div>
            </div>
            <div class="mt-6 flex space-x-4">
                <button @click="window.location.href = '/admin/empleados.php'" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Ir a Lista</button>
                <button @click="showCredentials = false; window.location.href = '/admin/empleado-crear.php'" class="flex-1 px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50">Crear Otro</button>
            </div>
        </div>
    </div>
</div>

<script>
function empleadoForm() {
    return {
        loading: false,
        showCredentials: false,
        credentials: { email: '', password: '' },

        async submitForm() {
            const form = document.getElementById('formEmpleado');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            const response = await fetch('/api/empleado-guardar.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();

            if (result.success) {
                if (result.data.password_temporal) {
                    this.credentials = { email: result.data.email_temporal, password: result.data.password_temporal };
                    this.showCredentials = true;
                } else {
                    window.location.href = '/admin/empleados.php';
                }
            } else {
                alert(result.message);
            }
        }
    }
}
document.getElementById('formEmpleado').addEventListener('submit', (e) => { e.preventDefault(); Alpine.evaluate(e.target, 'submitForm()'); });
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../views/layout.php';
