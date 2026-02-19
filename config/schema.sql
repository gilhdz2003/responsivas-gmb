-- GMB Responsivas - Database Schema
-- MySQL/MariaDB

-- =============================================
-- TABLA: Sucursales
-- =============================================
CREATE TABLE IF NOT EXISTS sucursales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    clave VARCHAR(20) NOT NULL UNIQUE, -- 'MB', 'BBC_CDMX', 'BBC_MTY'
    direccion TEXT,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- TABLA: Usuarios (RH/IT + Empleados)
-- =============================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    rol ENUM('admin', 'empleado') NOT NULL DEFAULT 'empleado',
    sucursal_id INT,
    activo TINYINT(1) DEFAULT 1,
    ultimo_acceso TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sucursal_id) REFERENCES sucursales(id)
);

-- =============================================
-- TABLA: Empleados
-- =============================================
CREATE TABLE IF NOT EXISTS empleados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNIQUE,
    numero_empleado VARCHAR(50) UNIQUE,
    nombre VARCHAR(150) NOT NULL,
    puesto VARCHAR(150),
    departamento VARCHAR(100),
    sucursal_id INT,
    fecha_ingreso DATE,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (sucursal_id) REFERENCES sucursales(id)
);

-- =============================================
-- TABLA: Equipos
-- =============================================
CREATE TABLE IF NOT EXISTS equipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('computadora', 'celular') NOT NULL,
    marca VARCHAR(100),
    modelo VARCHAR(150),
    numero_serie VARCHAR(150) UNIQUE,
    descripcion TEXT,
    sucursal_id INT,
    asignado_a INT NULL, -- empleado_id
    estado ENUM('disponible', 'asignado', 'en_reparacion', 'dado_de_baja') DEFAULT 'disponible',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (asignado_a) REFERENCES empleados(id),
    FOREIGN KEY (sucursal_id) REFERENCES sucursales(id)
);

-- =============================================
-- TABLA: Responsivas
-- =============================================
CREATE TABLE IF NOT EXISTS responsivas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empleado_id INT NOT NULL,
    equipo_id INT NOT NULL,
    sucursal_id INT NOT NULL,
    tipo_equipo ENUM('computadora', 'celular') NOT NULL,
    fecha_emision DATE NOT NULL,
    fecha_firma TIMESTAMP NULL,
    firma_digital TEXT, -- Base64 de la imagen de firma
    ip_firma VARCHAR(45), -- IPv4 o IPv6
    user_agent_firma TEXT,
    estatus ENUM('pendiente', 'firmada', 'cancelada') DEFAULT 'pendiente',
    pdf_ruta VARCHAR(255), -- Ruta del PDF generado
    codigo_verificacion VARCHAR(50) UNIQUE, -- Para verificar autenticidad
    cancelada_por INT NULL,
    razon_cancelacion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (empleado_id) REFERENCES empleados(id),
    FOREIGN KEY (equipo_id) REFERENCES equipos(id),
    FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
    FOREIGN KEY (cancelada_por) REFERENCES usuarios(id)
);

-- =============================================
-- TABLA: Historial de Equipos (Audit log)
-- =============================================
CREATE TABLE IF NOT EXISTS equipo_historial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    accion ENUM('asignado', 'devuelto', 'reparacion', 'baja') NOT NULL,
    empleado_id INT NULL,
    responsable_id INT NOT NULL, -- usuario que realizó la acción
    observaciones TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipo_id) REFERENCES equipos(id),
    FOREIGN KEY (empleado_id) REFERENCES empleados(id),
    FOREIGN KEY (responsable_id) REFERENCES usuarios(id)
);

-- =============================================
-- TABLA: Notificaciones
-- =============================================
CREATE TABLE IF NOT EXISTS notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo ENUM('responsiva_pendiente', 'responsiva_firmada', 'equipo_asignado') NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    mensaje TEXT NOT NULL,
    leida TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- =============================================
-- ÍNDICES para optimización
-- =============================================
CREATE INDEX idx_responsivas_empleado ON responsivas(empleado_id);
CREATE INDEX idx_responsivas_equipo ON responsivas(equipo_id);
CREATE INDEX idx_responsivas_estatus ON responsivas(estatus);
CREATE INDEX idx_equipos_estado ON equipos(estado);
CREATE INDEX idx_equipos_sucursal ON equipos(sucursal_id);
CREATE INDEX idx_notificaciones_usuario ON notificaciones(usuario_id);
CREATE INDEX idx_notificaciones_leida ON notificaciones(leida);

-- =============================================
-- DATOS INICIALES: Sucursales
-- =============================================
INSERT INTO sucursales (nombre, clave, direccion) VALUES
('Grupo MB Matriz', 'MB', 'Matriz'),
('BBC Ciudad de México', 'BBC_CDMX', 'CDMX'),
('BBC Monterrey', 'BBC_MTY', 'Monterrey');
