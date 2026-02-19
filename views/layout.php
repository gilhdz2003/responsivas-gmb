<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'GMB Responsivas' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <style>
        :root {
            --gmb-blue: #1e40af;
            --gmb-light: #3b82f6;
        }
        .gmb-gradient {
            background: linear-gradient(135deg, var(--gmb-blue) 0%, var(--gmb-light) 100%);
        }
        .signature-pad {
            touch-action: none;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php
    // Define base URL for assets
    $baseURL = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    ?>

    <?php if (Auth::check()): ?>
    <!-- Navbar -->
    <nav class="gmb-gradient text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="<?= $baseURL ?>/" class="flex items-center space-x-2">
                        <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-bold text-xl">GMB Responsivas</span>
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    <?php if (Auth::hasRole('admin')): ?>
                    <a href="/admin/dashboard.php" class="hover:bg-white/10 px-3 py-2 rounded-md transition">
                        Panel Admin
                    </a>
                    <?php else: ?>
                    <a href="/empleado/dashboard.php" class="hover:bg-white/10 px-3 py-2 rounded-md transition">
                        Mi Panel
                    </a>
                    <?php endif; ?>

                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-md transition">
                            <span><?= htmlspecialchars(Auth::user()['nombre']) ?></span>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 text-gray-700">
                            <a href="/auth/cerrar-sesion.php" class="block px-4 py-2 hover:bg-gray-100 transition">
                                Cerrar Sesión
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-gray-400 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>&copy; <?= date('Y') ?> Grupo MB. Sistema de Responsivas Digitales.</p>
        </div>
    </footer>
</body>
</html>
