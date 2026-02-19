<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Auth.php';

$error = $_GET['error'] ?? null;
$redirect = $_GET['redirect'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GMB Responsivas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen gmb-gradient flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-full mb-4">
                <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">GMB Responsivas</h1>
            <p class="text-gray-500 mt-2">Sistema de Firma Digital</p>
        </div>

        <!-- Error Message -->
        <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="/auth/iniciar-sesion.php" class="space-y-6">
            <?php if ($redirect): ?>
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
            <?php endif; ?>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico</label>
                <input type="email" name="email" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                       placeholder="correo@grupomb.com">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                       placeholder="••••••••">
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-200">
                Iniciar Sesión
            </button>
        </form>

        <!-- Footer -->
        <div class="mt-8 text-center text-sm text-gray-500">
            <p>Grupo MB &copy; <?= date('Y') ?></p>
        </div>
    </div>
</body>
</html>
