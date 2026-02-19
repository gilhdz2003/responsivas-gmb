<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Auth.php';

Auth::require();

// Verificar que sea empleado
if (Auth::hasRole('admin')) {
    header('Location: /admin/dashboard.php');
    exit;
}

$db = Database::getInstance();
$user = Auth::user();

// Obtener empleado
$empleado = $db->fetchOne(
    "SELECT * FROM empleados WHERE usuario_id = :uid",
    ['uid' => $user['id']]
);

// Obtener responsiva
$responsivaId = $_GET['id'] ?? null;

if (!$responsivaId) {
    header('Location: /empleado/dashboard.php');
    exit;
}

$responsiva = $db->fetchOne("
    SELECT r.*, eq.tipo, eq.marca, eq.modelo, eq.numero_serie,
           s.nombre as sucursal_nombre, s.direccion as sucursal_direccion,
           s.clave as sucursal_clave
    FROM responsivas r
    JOIN equipos eq ON r.equipo_id = eq.id
    JOIN sucursales s ON r.sucursal_id = s.id
    WHERE r.id = :rid AND r.empleado_id = :eid AND r.estatus = 'pendiente'
", ['rid' => $responsivaId, 'eid' => $empleado['id']]);

if (!$responsiva) {
    header('Location: /empleado/dashboard.php?error=' . urlencode('Responsiva no encontrada o ya firmada'));
    exit;
}

ob_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firmar Responsiva - GMB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <style>
        .signature-pad {
            touch-action: none;
        }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header (no print) -->
    <header class="gmb-gradient text-white py-4 no-print">
        <div class="max-w-4xl mx-auto px-4">
            <a href="/empleado/dashboard.php" class="flex items-center text-white/80 hover:text-white">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver a mi panel
            </a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8">
        <!-- Document Preview -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8" id="responsiva-document">
            <!-- Header Document -->
            <div class="text-center mb-8 pb-6 border-b-2 border-gray-200">
                <h1 class="text-3xl font-bold text-blue-800 mb-2">CARTA RESPONSIVA</h1>
                <p class="text-gray-600">DE EQUIPO DE CÓMPUTO / CELULAR</p>
                <p class="text-sm text-gray-500 mt-2">
                    <?= strtoupper(htmlspecialchars($responsiva['sucursal_nombre'])) ?> |
                    <?= date('d/m/Y', strtotime($responsiva['fecha_emision'])) ?>
                </p>
            </div>

            <!-- Content -->
            <div class="prose max-w-none text-gray-700 space-y-4">
                <p>
                    En la ciudad de <strong><?= htmlspecialchars($responsiva['sucursal_nombre']) ?></strong>,
                    siendo el día <strong><?= date('d', strtotime($responsiva['fecha_emision'])) ?></strong>
                    del mes de <strong><?= strftime('%B', strtotime($responsiva['fecha_emision'])) ?></strong>
                    del año <strong><?= date('Y', strtotime($responsiva['fecha_emision'])) ?></strong>,
                    por la presente yo:
                </p>

                <div class="bg-gray-50 p-4 rounded-lg my-6">
                    <p class="text-xl font-semibold text-gray-800">
                        <?= htmlspecialchars($empleado['nombre']) ?>
                    </p>
                    <p class="text-gray-600">
                        Puesto: <?= htmlspecialchars($empleado['puesto'] ?? 'N/A') ?> |
                        Departamento: <?= htmlspecialchars($empleado['departamento'] ?? 'N/A') ?>
                    </p>
                    <p class="text-gray-500 text-sm mt-2">
                        No. Empleado: <?= htmlspecialchars($empleado['numero_empleado'] ?? 'N/A') ?>
                    </p>
                </div>

                <p>
                    Me comprometo a hacer buen uso del equipo que se me ha asignado en
                    <strong>Grupo MB</strong>, el cual se describe a continuación:
                </p>

                <!-- Equipment Details -->
                <div class="bg-blue-50 border border-blue-200 p-6 rounded-lg my-6">
                    <h3 class="font-bold text-blue-800 mb-4">DETALLES DEL EQUIPO</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-500 text-sm">Tipo de Equipo</p>
                            <p class="font-semibold text-gray-800 uppercase">
                                <?= htmlspecialchars($responsiva['tipo']) ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Marca</p>
                            <p class="font-semibold text-gray-800">
                                <?= htmlspecialchars($responsiva['marca']) ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Modelo</p>
                            <p class="font-semibold text-gray-800">
                                <?= htmlspecialchars($responsiva['modelo']) ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Número de Serie</p>
                            <p class="font-semibold text-gray-800">
                                <?= htmlspecialchars($responsiva['numero_serie']) ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Terms -->
                <div class="space-y-3 text-sm">
                    <h3 class="font-bold text-gray-800">CONDICIONES Y RESPONSABILIDADES:</h3>
                    <ol class="list-decimal pl-5 space-y-2">
                        <li>Me comprometo a hacer buen uso del equipo, siguiendo las políticas de la empresa.</li>
                        <li>El equipo es para uso exclusivo de actividades laborales de Grupo MB.</li>
                        <li>Soy responsable del cuidado y mantenimiento adecuado del equipo.</li>
                        <li>En caso de robo, pérdida o daño, debo reportarlo inmediatamente al área de TI.</li>
                        <li>Al terminar mi relación laboral, debo devolver el equipo en buen estado.</li>
                        <li>No debo instalar software no autorizado por el área de TI.</li>
                        <li>Debo mantener actualizado el antivirus y sistemas de seguridad.</li>
                        <li>No debo compartir mis credenciales de acceso con nadie.</li>
                    </ol>
                </div>

                <!-- Verification Code -->
                <div class="mt-8 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500">
                        Código de verificación:
                        <code class="bg-gray-100 px-2 py-1 rounded text-xs">
                            <?= htmlspecialchars($responsiva['codigo_verificacion']) ?>
                        </code>
                    </p>
                </div>
            </div>

            <!-- Signature Area -->
            <div class="mt-12 pt-8 border-t-2 border-gray-300">
                <div class="grid grid-cols-2 gap-8">
                    <!-- Signature -->
                    <div class="text-center">
                        <p class="text-gray-600 mb-2">Firma del Empleado</p>
                        <div id="signature-display" class="border-b-2 border-gray-400 h-20"></div>
                    </div>
                    <!-- Admin Signature -->
                    <div class="text-center">
                        <p class="text-gray-600 mb-2">Firma del Responsable</p>
                        <div class="border-b-2 border-gray-400 h-20"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Signature Pad (no print) -->
        <div class="bg-white rounded-xl shadow-lg p-8 no-print">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Firmar Responsiva</h2>
            <p class="text-gray-600 mb-6">Por favor dibuja tu firma en el recuadro de abajo:</p>

            <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-4 mb-6">
                <canvas id="signature-pad" class="signature-pad w-full bg-white rounded cursor-crosshair" height="200"></canvas>
            </div>

            <div class="flex space-x-4">
                <button onclick="clearSignature()" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Borrar Firma
                </button>
                <button onclick="submitSignature()" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Firmar y Confirmar
                </button>
            </div>

            <div id="error-message" class="hidden mt-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg"></div>
        </div>
    </main>

    <script>
        const canvas = document.getElementById('signature-pad');
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)'
        });

        // Resize canvas
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
        }
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);

        function clearSignature() {
            signaturePad.clear();
        }

        function submitSignature() {
            if (signaturePad.isEmpty()) {
                showError('Por favor firma antes de continuar');
                return;
            }

            const signatureData = signaturePad.toDataURL();

            fetch('/api/firmar.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    responsiva_id: <?= $responsiva['id'] ?>,
                    firma: signatureData
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '/empleado/dashboard.php?success=' + encodeURIComponent('Responsiva firmada correctamente');
                } else {
                    showError(data.message || 'Error al firmar. Intenta de nuevo.');
                }
            })
            .catch(error => {
                showError('Error de conexión. Intenta de nuevo.');
                console.error('Error:', error);
            });
        }

        function showError(message) {
            const errorDiv = document.getElementById('error-message');
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
        }
    </script>
</body>
</html>
