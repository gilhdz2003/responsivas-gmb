<?php
/**
 * GMB Responsivas - Authentication Class
 */

class Auth {
    private static $user = null;

    /**
     * Iniciar sesión
     */
    public static function login($email, $password) {
        $db = Database::getInstance();

        $user = $db->fetchOne(
            "SELECT u.*, e.numero_empleado, s.nombre as sucursal_nombre
            FROM usuarios u
            LEFT JOIN empleados e ON u.id = e.usuario_id
            LEFT JOIN sucursales s ON u.sucursal_id = s.id
            WHERE u.email = :email AND u.activo = 1",
            ['email' => $email]
        );

        if ($user && password_verify($password, $user['password_hash'])) {
            self::setUser($user);
            self::updateLastAccess($user['id']);
            return true;
        }

        return false;
    }

    /**
     * Cerrar sesión
     */
    public static function logout() {
        session_start();
        session_destroy();
        self::$user = null;
    }

    /**
     * Verificar si hay sesión activa
     */
    public static function check() {
        if (self::$user === null) {
            session_start();
            self::$user = $_SESSION['user'] ?? null;
        }
        return self::$user !== null;
    }

    /**
     * Obtener usuario actual
     */
    public static function user() {
        if (!self::check()) {
            return null;
        }
        return self::$user;
    }

    /**
     * Verificar rol
     */
    public static function hasRole($role) {
        $user = self::user();
        return $user && $user['rol'] === $role;
    }

    /**
     * Requiere autenticación (redirige si no está logueado)
     */
    public static function require() {
        if (!self::check()) {
            header('Location: /index.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
    }

    /**
     * Requiere rol específico
     */
    public static function requireRole($role) {
        self::require();
        if (!self::hasRole($role)) {
            http_response_code(403);
            die('Acceso no autorizado');
        }
    }

    /**
     * Guardar usuario en sesión
     */
    private static function setUser($user) {
        session_start();
        $_SESSION['user'] = $user;
        self::$user = $user;
    }

    /**
     * Actualizar último acceso
     */
    private static function updateLastAccess($userId) {
        $db = Database::getInstance();
        $db->query(
            "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = :id",
            ['id' => $userId]
        );
    }

    /**
     * Generar código de verificación para responsivas
     */
    public static function generateVerificationCode() {
        return bin2hex(random_bytes(16));
    }
}
