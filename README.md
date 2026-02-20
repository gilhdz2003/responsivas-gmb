# GMB Responsivas App

> Sistema digital de firma de responsivas para Grupo MB

---

## 📋 Descripción

Sistema web para digitalizar el proceso de firma de responsivas de equipos (computadoras, celulares) en Grupo MB, eliminando el uso de hojas impresas.

---

## 🎯 MVP Alcance (Fase 1) - COMPLETADO ✅

- ✅ Responsivas de equipos (PC, celular)
- ✅ Firma digital con canvas (touch/mouse)
- ✅ Panel RH/IT para crear responsivas
- ✅ Panel empleado para ver y firmar
- ✅ Autenticación de usuarios
- ✅ Dashboard con estadísticas
- ✅ Gestión de equipos (CRUD completo)
- ✅ Gestión de empleados (CRUD + auto-usuario)
- ✅ Wizard de creación de responsivas
- ✅ PDF generado automáticamente
- ✅ Notificaciones por email
- ✅ Sistema de seed para datos de prueba

---

## 🛠️ Stack Técnico

| Componente | Tecnología |
|------------|------------|
| Frontend | HTML + Tailwind CSS + Alpine.js |
| Backend | PHP 8.x (Native, sin framework) |
| Database | MySQL/MariaDB |
| Firma digital | `signature_pad` (JS) |
| PDF | TCPDF |
| Email | PHPMailer |
| Hosting | Hostinger Business |

---

## 📂 Estructura del Proyecto

```
/public_html/
├── TESTING.md                # ✅ Plan de testing completo
├── index.php                # Entry point / Login
├── auth/                    # Autenticación
│   ├── iniciar-sesion.php
│   └── cerrar-sesion.php
├── admin/                   # Panel RH/IT
│   ├── dashboard.php        # Dashboard con estadísticas
│   ├── crear-responsiva.php # Wizard 4 pasos
│   ├── equipos.php          # Gestión de equipos
│   ├── equipo-crear.php     # Formulario crear equipo
│   ├── empleados.php        # Gestión de empleados
│   └── empleado-crear.php   # Formulario crear empleado
├── empleado/                # Panel empleado
│   ├── dashboard.php        # Dashboard con pendientes/historial
│   └── firmar.php           # Página de firma digital
├── api/                     # Endpoints AJAX
│   ├── firmar.php           # Procesar firma + PDF + email
│   ├── equipo-guardar.php   # CRUD equipos
│   ├── equipo-eliminar.php  # Soft delete
│   ├── buscar-empleados.php # Autocomplete
│   ├── buscar-equipos.php   # Autocomplete
│   ├── empleado-guardar.php # CRUD empleados
│   └── responsiva-crear.php # Crear responsiva
├── utils/                   # Helpers
│   ├── Auth.php             # Autenticación
│   ├── PDFGenerator.php     # Generación PDF TCPDF
│   └── Notifier.php         # Notificaciones PHPMailer
├── config/                  # Configuración
│   ├── database.php         # Clase Database
│   ├── db_credentials.php   # Credenciales BD
│   ├── schema.sql           # Esquema BD (8 tablas)
│   └── seed.php             # Datos de prueba
├── views/                   # Vistas
│   ├── layout.php           # Layout base con navbar
│   └── login.php            # Login
├── assets/                  # CSS, JS, imágenes
├── uploads/                 # PDFs generados
├── templates/               # PDFs de referencia (6 docs)
└── composer.json            # Dependencias
```

---

## 🚀 Estado Actual

**Última actualización**: 19 de Febrero, 2026

### Milestones Alcanzados

| Milestone | Fecha | Descripción |
|-----------|-------|-------------|
| MVP Core | 19 Feb | Implementación completa de funcionalidad |
| Deploy Hostinger | 19 Feb | ✅ Desplegado en producción |
| Testing Plan | 19 Feb | ✅ Plan de 34 casos de prueba creado |

### Estado del Sistema

- **Backend**: ✅ Completado
- **Frontend**: ✅ Completado
- **Base de Datos**: ✅ Configurada
- **Deploy**: ✅ Producción (Hostinger)
- **Testing**: 🔄 En progreso

---

## 🔐 Credenciales de Acceso

### Producción (Hostinger)

- **URL**: Dominio temporal de Hostinger
- **Admin**: `admin@grupomb.com` / `Admin123456`

### Datos de Prueba (Seed)

Ejecutar: `http://tu-dominio/config/seed.php`

- 1 Admin
- 3 Empleados (con contraseñas generadas)
- 10 Equipos (5 computo + 5 celular)
- 3 Responsivas pendientes

---

## 📊 Plan de Testing

El plan de testing completo está disponible en [TESTING.md](TESTING.md)

**Resumen de casos de prueba**:
- 34 casos documentados
- 7 módulos probados
- Checklist final de validación

---

## 📝 Fases Posteriores

| Fase | Descripción | Prioridad |
|------|-------------|-----------|
| Fase 1 | ✅ MVP Responsivas | Completado |
| Fase 2 | Formatos RH (vacaciones, permisos) | Media |
| Fase 3 | Políticas corporativas | Baja |
| Fase 4 | Flujos de aprobación jerárquica | Media |
| Fase 5 | Integraciones | Baja |

---

## 🔧 Configuración Pendiente

### Composer Dependencies

Para habilitar PDF y Email, ejecutar en servidor:
```bash
cd public_html
composer install
```

Dependencias:
- `tecnickcom/tcpdf: ^6.6` - Generación de PDFs
- `phpmailer/phpmailer: ^6.8` - Envío de emails

### SSL/HTTPS

Configurar certificado SSL para `responsivas.grupomb.mx`:
- Usar Let's Encrypt gratuito desde Hostinger
- O esperar configuración DNS

---

## 🔐 Seguridad

- ✅ Passwords con `password_hash()` (bcrypt)
- ✅ Prepared statements (PDO)
- ✅ Sesiones PHP seguras
- ✅ Verificación de roles (admin/empleado)
- ✅ Protección de archivos sensibles (.htaccess)
- ⏳ CSRF tokens (pendiente)
- ⏳ Rate limiting (pendiente)

---

## 📄 Documentación

- **Testing**: [TESTING.md](TESTING.md)
- **Schema BD**: `config/schema.sql`
- **Seed Data**: `config/seed.php`
- **Plantillas PDF**: `templates/` (6 documentos de referencia)

---

## 📄 Licencia

Uso exclusivo para Grupo MB.

---

**Inicio**: Febrero 2026
**Estado**: ✅ MVP Completado y Deployado
**Repositorio**: [github.com/gilhdz2003/responsivas-gmb](https://github.com/gilhdz2003/responsivas-gmb)
