<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Auth.php';

Auth::requireRole('admin');

$db = Database::getInstance();
$sucursales = $db->fetchAll("SELECT * FROM sucursales WHERE activo = 1");

ob_start();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="wizardStep()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Nueva Responsiva</h1>
        <p class="text-gray-600 mt-2">Asignar equipo a empleado</p>
    </div>

    <!-- Progress -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white text-sm font-medium" :class="step >= 1 ? 'bg-blue-600' : 'bg-gray-300'">1</div>
                <span class="ml-2 text-sm font-medium" :class="step >= 1 ? 'text-blue-600' : 'text-gray-500'">Empleado</span>
            </div>
            <div class="flex-1 h-1 mx-4 bg-gray-200"><div class="h-full bg-blue-600 transition-all" :style="`width: ${step * 33}%`"></div></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-8 h-8 rounded-full text-white text-sm font-medium" :class="step >= 2 ? 'bg-blue-600' : 'bg-gray-300'">2</div>
                <span class="ml-2 text-sm font-medium" :class="step >= 2 ? 'text-blue-600' : 'text-gray-500'">Equipo</span>
            </div>
            <div class="flex-1 h-1 mx-4 bg-gray-200"><div class="h-full bg-blue-600 transition-all" :style="`width: ${Math.max(0, (step - 1) * 33)}%`"></div></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-8 h-8 rounded-full text-white text-sm font-medium" :class="step >= 3 ? 'bg-blue-600' : 'bg-gray-300'">3</div>
                <span class="ml-2 text-sm font-medium" :class="step >= 3 ? 'text-blue-600' : 'text-gray-500'">Confirmar</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-8">
        <!-- Step 1: Seleccionar Empleado -->
        <div x-show="step === 1">
            <h2 class="text-xl font-semibold mb-4">Seleccionar Empleado</h2>
            <div class="mb-4">
                <input type="text" x-model="empleadoQuery" @input="buscarEmpleados()" placeholder="Buscar por nombre o número de empleado..." class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div x-show="empleados.length > 0" class="border border-gray-200 rounded-lg max-h-64 overflow-y-auto">
                <template x-for="emp in empleados" :key="emp.id">
                <div @click="seleccionarEmpleado(emp)" class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-0">
                    <div class="font-medium" x-text="emp.nombre"></div>
                    <div class="text-sm text-gray-500">No. <span x-text="emp.numero_empleado"></span> - <span x-text="emp.sucursal_nombre"></span></div>
                </div>
                </template>
            </div>
            <div class="mt-6 flex justify-end">
                <button @click="step = 2" :disabled="!empleadoSeleccionado" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed">Siguiente</button>
            </div>
        </div>

        <!-- Step 2: Seleccionar Equipo -->
        <div x-show="step === 2">
            <h2 class="text-xl font-semibold mb-4">Seleccionar Tipo de Equipo</h2>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <button @click="tipoEquipo = 'computadora'" :class="tipoEquipo === 'computadora' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'" class="border-2 rounded-lg p-6 text-center">
                    <svg class="h-12 w-12 mx-auto mb-2" :class="tipoEquipo === 'computadora' ? 'text-blue-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span class="font-medium">Computadora</span>
                </button>
                <button @click="tipoEquipo = 'celular'" :class="tipoEquipo === 'celular' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'" class="border-2 rounded-lg p-6 text-center">
                    <svg class="h-12 w-12 mx-auto mb-2" :class="tipoEquipo === 'celular' ? 'text-blue-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span class="font-medium">Celular</span>
                </button>
            </div>

            <div x-show="tipoEquipo">
                <h3 class="font-medium mb-2">Buscar Equipo Disponible</h3>
                <input type="text" x-model="equipoQuery" @input="buscarEquipos()" placeholder="Marca, modelo o número de serie..." class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-4">
                <div x-show="equipos.length > 0" class="border border-gray-200 rounded-lg max-h-64 overflow-y-auto">
                    <template x-for="eq in equipos" :key="eq.id">
                    <div @click="seleccionarEquipo(eq)" class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-0">
                        <div class="font-medium"><span x-text="eq.marca"></span> <span x-text="eq.modelo"></span></div>
                        <div class="text-sm text-gray-500">Serie: <span x-text="eq.numero_serie"></span> - <span x-text="eq.sucursal_nombre"></span></div>
                    </div>
                    </template>
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <button @click="step = 1" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Anterior</button>
                <button @click="step = 3" :disabled="!equipoSeleccionado" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-300">Siguiente</button>
            </div>
        </div>

        <!-- Step 3: Confirmar -->
        <div x-show="step === 3">
            <h2 class="text-xl font-semibold mb-4">Confirmar Responsiva</h2>
            <div class="space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-medium text-gray-700">Empleado</h3>
                    <p class="text-lg" x-text="empleadoSeleccionado?.nombre"></p>
                    <p class="text-sm text-gray-500">No. <span x-text="empleadoSeleccionado?.numero_empleado"></span></p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-medium text-gray-700">Equipo</h3>
                    <p class="text-lg capitalize" x-text="tipoEquipo"></p>
                    <p><span x-text="equipoSeleccionado?.marca"></span> <span x-text="equipoSeleccionado?.modelo"></span></p>
                    <p class="text-sm text-gray-500">Serie: <span x-text="equipoSeleccionado?.numero_serie"></span></p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-medium text-gray-700">Sucursal</h3>
                    <p x-text="empleadoSeleccionado?.sucursal_nombre"></p>
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <button @click="step = 2" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Anterior</button>
                <button @click="crearResponsiva" :disabled="loading" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-300">
                    <span x-show="loading">Creando...</span>
                    <span x-show="!loading">Crear Responsiva</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function wizardStep() {
    return {
        step: 1,
        empleadoQuery: '',
        equipoQuery: '',
        tipoEquipo: '',
        empleadoSeleccionado: null,
        equipoSeleccionado: null,
        empleados: [],
        equipos: [],
        loading: false,

        async buscarEmpleados() {
            if (this.empleadoQuery.length < 2) { this.empleados = []; return; }
            const res = await fetch(`/api/buscar-empleados.php?q=${encodeURIComponent(this.empleadoQuery)}`);
            this.empleados = await res.json();
        },

        seleccionarEmpleado(emp) {
            this.empleadoSeleccionado = emp;
        },

        async buscarEquipos() {
            if (!this.tipoEquipo || this.equipoQuery.length < 2) { this.equipos = []; return; }
            const res = await fetch(`/api/buscar-equipos.php?tipo=${this.tipoEquipo}&q=${encodeURIComponent(this.equipoQuery)}`);
            this.equipos = await res.json();
        },

        seleccionarEquipo(eq) {
            this.equipoSeleccionado = eq;
        },

        async crearResponsiva() {
            this.loading = true;
            const res = await fetch('/api/responsiva-crear.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    empleado_id: this.empleadoSeleccionado.id,
                    equipo_id: this.equipoSeleccionado.id,
                    sucursal_id: this.empleadoSeleccionado.sucursal_id,
                    tipo_equipo: this.tipoEquipo
                })
            });
            const data = await res.json();
            if (data.success) {
                alert('Responsiva creada correctamente');
                window.location.href = '/admin/dashboard.php';
            } else {
                alert(data.message);
                this.loading = false;
            }
        }
    }
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../views/layout.php';
