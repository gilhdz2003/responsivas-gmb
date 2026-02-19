# GMB Responsivas App

> Sistema digital de firma de responsivas para Grupo MB

---

## 📋 Descripción

Sistema web para digitalizar el proceso de firma de responsivas de equipos (computadoras, celulares) en Grupo MB, eliminando el uso de hojas impresas.

## 🎯 MVP Alcance (Fase 1)

- ✅ Responsivas de equipos (PC, celular)
- ✅ Firma digital simple
- ✅ Panel RH/IT para generar y ver responsivas
- ✅ Panel empleado para ver y firmar
- ✅ PDF generado automáticamente
- ✅ Notificaciones por email

## 🛠️ Stack Técnico

- **Frontend**: HTML + Tailwind CSS + Alpine.js
- **Backend**: PHP 8.x
- **Database**: MySQL/MariaDB
- **Firma digital**: `signature_pad`
- **PDF**: `TCPDF` o `DomPDF`
- **Hosting**: Hostinger Business

## 📂 Estructura del Proyecto

```
/public_html/
├── index.php              # Landing/Login
├── auth/                  # Autenticación
├── admin/                 # Panel RH/IT
├── empleado/              # Panel empleado
├── api/                   # Endpoints AJAX
├── assets/                # CSS, JS, imágenes
├── config/                # DB config, constants
└── uploads/               # Firmas guardadas
```

## 🚀 Deployment

- **Hosting**: Hostinger Business
- **Subdominio**: `responsivas.grupomb.com`
- **Método**: TBD (GitHub Direct / FTP / CI-CD)

## 📝 Fases Posteriores

- **Fase 2**: Formatos RH (vacaciones, permisos)
- **Fase 3**: Políticas corporativas
- **Fase 4**: Flujos de aprobación jerárquica
- **Fase 5**: Integraciones

---

**Inicio**: Febrero 2026
**Estado**: Inicialización
