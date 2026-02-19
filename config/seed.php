<?php
/**
 * GMB Responsivas - Seed Data
 *
 * Ejecutar: php config/seed.php
 */

require_once __DIR__ . '/database.php';

echo "=== GMB Responsivas - Seed Data ===\n\n";

$db = Database::getInstance();

// Limpiar datos (opcional - comentar si no se desea)
echo "1. Limpiando datos existentes...\n";
$db->query("DELETE FROM equipo_historial");
$db->query("DELETE FROM notificaciones");
$db->query("DELETE FROM responsivas");
$db->query("UPDATE equipos SET asignado_a = NULL, estado = 'disponible'");
$db->query("DELETE FROM empleados");
$db->query("DELETE FROM usuarios WHERE rol = 'empleado'");
$db->query("DELETE FROM usuarios WHERE rol = 'admin' AND email = 'admin@grupomb.com'");
echo "   Datos limpiados.\n\n";

// Crear Admin
echo "2. Creando usuario admin...\n";
$adminPassword = password_hash('Admin123456', PASSWORD_DEFAULT);
$db->query("INSERT IGNORE INTO usuarios (email, password_hash, nombre, rol) VALUES ('admin@grupomb.com', :pass, 'Administrador', 'admin')", ['pass' => $adminPassword]);
echo "   Admin creado: admin@grupomb.com / Admin123456\n\n";

// Crear Empleados
echo "3. Creando empleados de prueba...\n";
$empleados = [
    ['numero' => 'EMP001', 'nombre' => 'Juan Pérez López', 'puesto' => 'Ajustador', 'depto' => 'Siniestros', 'sucursal' => 1, 'email' => 'juan.perez@grupomb.com'],
    ['numero' => 'EMP002', 'nombre' => 'María González García', 'puesto' => 'Analista', 'depto' => 'Finanzas', 'sucursal' => 2, 'email' => 'maria.gonzalez@grupomb.com'],
    ['numero' => 'EMP003', 'nombre' => 'Carlos López Martínez', 'puesto' => 'Supervisor', 'depto' => 'Operaciones', 'sucursal' => 3, 'email' => 'carlos.lopez@grupomb.com']
];

foreach ($empleados as $emp) {
    $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10);
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $usuarioId = $db->insert('usuarios', [
        'email' => $emp['email'],
        'password_hash' => $passwordHash,
        'nombre' => $emp['nombre'],
        'rol' => 'empleado',
        'sucursal_id' => $emp['sucursal']
    ]);

    $db->insert('empleados', [
        'usuario_id' => $usuarioId,
        'numero_empleado' => $emp['numero'],
        'nombre' => $emp['nombre'],
        'puesto' => $emp['puesto'],
        'departamento' => $emp['depto'],
        'sucursal_id' => $emp['sucursal'],
        'activo' => 1
    ]);

    echo "   - {$emp['nombre']} ({$emp['numero']}) - Pass: {$password}\n";
}
echo "\n";

// Crear Equipos
echo "4. Creando equipos de prueba...\n";
$equiposComputo = [
    ['marca' => 'Dell', 'modelo' => 'Latitude 5420', 'serie' => 'DELL-5420-001', 'sucursal' => 1],
    ['marca' => 'HP', 'modelo' => 'ProBook 450', 'serie' => 'HP-450-002', 'sucursal' => 2],
    ['marca' => 'Lenovo', 'modelo' => 'ThinkPad E15', 'serie' => 'LENO-E15-003', 'sucursal' => 3],
    ['marca' => 'Dell', 'modelo' => 'Optiplex 7090', 'serie' => 'DELL-7090-004', 'sucursal' => 1],
    ['marca' => 'HP', 'modelo' => 'EliteDesk 800', 'serie' => 'HP-800-005', 'sucursal' => 2]
];

$equiposCelular = [
    ['marca' => 'Samsung', 'modelo' => 'Galaxy S22', 'serie' => 'SAM-S22-001', 'sucursal' => 1],
    ['marca' => 'Motorola', 'modelo' => 'Edge 30', 'serie' => 'MOT-E30-002', 'sucursal' => 2],
    ['marca' => 'Xiaomi', 'modelo' => 'Redmi Note 12', 'serie' => 'XIA-RN12-003', 'sucursal' => 3],
    ['marca' => 'Huawei', 'modelo' => 'P50 Lite', 'serie' => 'HUA-P50-004', 'sucursal' => 1],
    ['marca' => 'Samsung', 'modelo' => 'Galaxy A54', 'serie' => 'SAM-A54-005', 'sucursal' => 2]
];

foreach ($equiposComputo as $eq) {
    $db->insert('equipos', ['tipo' => 'computadora', 'marca' => $eq['marca'], 'modelo' => $eq['modelo'], 'numero_serie' => $eq['serie'], 'sucursal_id' => $eq['sucursal'], 'estado' => 'disponible']);
    echo "   - Computadora: {$eq['marca']} {$eq['modelo']} ({$eq['serie']})\n";
}

foreach ($equiposCelular as $eq) {
    $db->insert('equipos', ['tipo' => 'celular', 'marca' => $eq['marca'], 'modelo' => $eq['modelo'], 'numero_serie' => $eq['serie'], 'sucursal_id' => $eq['sucursal'], 'estado' => 'disponible']);
    echo "   - Celular: {$eq['marca']} {$eq['modelo']} ({$eq['serie']})\n";
}
echo "\n";

echo "=== Seed completado exitosamente ===\n";
echo "\nCredenciales de prueba:\n";
echo "Admin: admin@grupomb.com / Admin123456\n";
echo "Empleados: ver passwords arriba\n";
