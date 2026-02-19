# GMB Responsivas App

> Sistema digital de firma de responsivas para Grupo MB

---

## 📋 Descripción

Sistema web para digitalizar el proceso de firma de responsivas de equipos (computadoras, celulares) en Grupo MB, eliminando el uso de hojas impresas.

## 🎯 MVP Alcance (Fase 1)

- ✅ Responsivas de equipos (PC, celular)
- ✅ Firma digital con canvas (touch/mouse)
- ✅ Panel RH/IT para crear responsivas
- ✅ Panel empleado para ver y firmar
- ✅ Autenticación de usuarios
- ✅ Dashboard con estadísticas
- ✅ PDF generado automáticamente (pendiente implementación)
- ✅ Notificaciones (pendiente implementación email)

## 🛠️ Stack Técnico

| Componente | Tecnología |
|------------|------------|
| Frontend | HTML + Tailwind CSS + Alpine.js |
| Backend | PHP 8.x (Native, sin framework) |
| Database | MySQL/MariaDB |
| Firma digital | `signature_pad` (JS) |
| PDF | `TCPDF` o `DomPDF` |
| Hosting | Hostinger Business |

## 📂 Estructura del Proyecto

```
/public_html/
├── index.php                 # Entry point / Login
├── auth/                     # Autenticación
│   ├── iniciar-sesion.php
│   └── cerrar-sesion.php
├── admin/                    # Panel RH/IT
│   └── dashboard.php
├── empleado/                 # Panel empleado
│   ├── dashboard.php
│   └── firmar.php            # Vista de firma
├── api/                      # Endpoints AJAX
│   └── firmar.php            # Procesar firma
├── assets/                   # CSS, JS, imágenes
│   ├── css/
│   ├── js/
│   └── images/
├── config/                   # Configuración
│   ├── database.php          # Clase Database
│   ├── db_credentials.php    # Credenciales (no versionado)
│   └── schema.sql            # Esquema de BD
├── utils/                    # Helpers
│   └── Auth.php              # Clase de autenticación
├── views/                    # Vistas
│   └── layout.php            # Layout base
├── models/                   # Modelos de datos
├── templates/                # PDFs de referencia
└── uploads/                  # PDFs generados
```

## 🚀 Instalación

### 1. Requisitos

- PHP 8.x
- MySQL/MariaDB
- Hostinger Business plan (o similar)

### 2. Base de Datos

```bash
# En Hostinger, crear base de datos desde MySQL Database Wizard

# Importar schema
mysql -u usuario -p nombre_db < config/schema.sql
```

### 3. Configuración

```bash
# Copiar environment
cp .env.example .env

# Editar config/db_credentials.php con tus credenciales
```

### 4. Deploy a Hostinger

```bash
# Opción 1: FTP/SFTP
# Subir contenido de /public_html a public_html/

# Opción 2: Git (si Hostinger lo soporta)
git clone https://github.com/gilhdz2003/responsivas-gmb .
```

### 5. Crear Usuarios Iniciales

```sql
-- Admin (RH/IT)
INSERT INTO usuarios (email, password_hash, nombre, rol, sucursal_id)
VALUES ('admin@grupomb.com', '$2y$10$...', 'Admin RH', 'admin', 1);

-- Empleado prueba
INSERT INTO usuarios (email, password_hash, nombre, rol, sucursal_id)
VALUES ('empleado@grupomb.com', '$2y$10$...', 'Juan Pérez', 'empleado', 1);
```

## 📝 Fases Posteriores

| Fase | Descripción | Status |
|------|-------------|--------|
| Fase 1 | ✅ MVP Responsivas | Completado |
| Fase 2 | Formatos RH (vacaciones, permisos) | Pendiente |
| Fase 3 | Políticas corporativas | Pendiente |
| Fase 4 | Flujos de aprobación jerárquica | Pendiente |
| Fase 5 | Integraciones | Pendiente |

## 🔐 Seguridad

- ✅ Passwords con `password_hash()` (bcrypt)
- ✅ Prepared statements (PDO)
- ✅ Sesiones PHP seguras
- ✅ Verificación de roles
- ⏳ CSRF tokens (pendiente)
- ⏳ Rate limiting (pendiente)

## 📄 Licencia

Uso exclusivo para Grupo MB.

---

**Inicio**: Febrero 2026
**Estado**: MVP en desarrollo
**Repositorio**: [github.com/gilhdz2003/responsivas-gmb](https://github.com/gilhdz2003/responsivas-gmb)
