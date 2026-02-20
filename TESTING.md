# GMB Responsivas - Plan de Testing Completo

> **Fecha de creación**: 19 de Febrero, 2026
> **Estado del proyecto**: MVP Desplegado en Hostinger
> **URL**: [Dominio temporal de Hostinger]

---

## 📋 Índice

1. [Preparación del Entorno](#preparación-del-entorno)
2. [Testing de Autenticación](#testing-de-autenticación)
3. [Testing Dashboard Admin](#testing-dashboard-admin)
4. [Testing Dashboard Empleado](#testing-dashboard-empleado)
5. [Testing Gestión de Equipos](#testing-gestión-de-equipos)
6. [Testing Gestión de Empleados](#testing-gestión-de-empleados)
7. [Testing Creación de Responsivas](#testing-creación-de-responsivas)
8. [Testing Firma Digital](#testing-firma-digital)
9. [Testing PDF y Notificaciones](#testing-pdf-y-notificaciones)
10. [Checklist Final](#checklist-final)

---

## 🔧 Preparación del Entorno

### Paso 1: Ejecutar Seed de Datos

**Objetivo**: Crear datos de prueba para testing

**Acción**:
1. Abre el navegador en: `http://tu-dominio-temporal/config/seed.php`
2. Verifica que veas el mensaje: "=== GMB Responsivas - Seed Data ==="
3. Anota las credenciales generadas

**Resultado esperado**:
- Usuario admin creado
- 3 empleados de prueba creados
- 10 equipos de prueba creados (5 computo + 5 celular)
- 3 responsivas pendientes creadas

**Credenciales admin**:
- Email: `admin@grupomb.com`
- Password: `Admin123456`

---

## 🔐 Testing de Autenticación

### Test 1: Login como Admin

**Pasos**:
1. Ve a: `http://tu-dominio-temporal/`
2. Ingresa email: `admin@grupomb.com`
3. Ingresa password: `Admin123456`
4. Clic en "Iniciar Sesión"

**Resultado esperado**:
- ✅ Redirección a `/admin/dashboard.php`
- ✅ Ver nombre de usuario en navbar
- ✅ Ver estadísticas en dashboard

**Si falla**:
- Verifica que el usuario admin exista en BD
- Verifica que el password esté hasheado correctamente
- Revisa logs de error PHP

---

### Test 2: Login como Empleado

**Pasos**:
1. Cierra sesión (si está logueado)
2. Ve a: `http://tu-dominio-temporal/`
3. Ingresa email de empleado (del seed)
4. Ingresa password generado
5. Clic en "Iniciar Sesión"

**Resultado esperado**:
- ✅ Redirección a `/empleado/dashboard.php`
- ✅ Ver nombre de empleado
- ✅ Ver responsivas pendientes (si hay)

---

### Test 3: Cierre de Sesión

**Pasos**:
1. Estar logueado
2. Clic en menú de usuario (arriba derecha)
3. Clic en "Cerrar Sesión"

**Resultado esperado**:
- ✅ Redirección a página de login
- ✅ Sesión destruida (no puede acceder a dashboards directamente)

---

### Test 4: Acceso No Autorizado

**Pasos**:
1. Estar logueado como empleado
2. Intentar acceder directamente: `http://tu-dominio-temporal/admin/dashboard.php`

**Resultado esperado**:
- ✅ Error 403 o redirección a login
- ✅ Mensaje de "Acceso no autorizado"

---

## 📊 Testing Dashboard Admin

### Test 5: Visualización de Estadísticas

**Pasos**:
1. Login como admin
2. Observar las tarjetas de estadísticas

**Resultado esperado**:
- ✅ Ver: "Pendientes de Firma" con número
- ✅ Ver: "Firmadas Hoy" con número
- ✅ Ver: "Total Firmadas" con número
- ✅ Ver: "Equipos Asignados" con número

---

### Test 6: Acciones Rápidas

**Pasos**:
1. En dashboard admin
2. Ver botones de acciones rápidas

**Resultado esperado**:
- ✅ Botón "Nueva Responsiva" visible
- ✅ Botón "Gestionar Equipos" visible
- ✅ Botón "Empleados" visible
- ✅ Los botones redirigen a las páginas correctas

---

### Test 7: Actividad Reciente

**Pasos**:
1. En dashboard admin
2. Ver tabla de actividad reciente

**Resultado esperado**:
- ✅ Ver columnas: Empleado, Equipo, Sucursal, Estado, Fecha
- ✅ Ver datos de responsivas recientes
- ✅ Estados con colores correctos (verde=firmada, amarillo=pendiente)

---

## 👤 Testing Dashboard Empleado

### Test 8: Alerta de Responsivas Pendientes

**Pasos**:
1. Login como empleado con responsivas pendientes
2. Ver alerta amarilla en dashboard

**Resultado esperado**:
- ✅ Alerta visible con número de pendientes
- ✅ Mensaje claro de acción
- ✅ Icono de advertencia

---

### Test 9: Lista de Responsivas Pendientes

**Pasos**:
1. En dashboard empleado
2. Ver sección "Pendientes de Firma"

**Resultado esperado**:
- ✅ Ver tarjetas con: Tipo, Marca/Modelo, Serie, Sucursal
- ✅ Botón "Firmar Ahora" en cada tarjeta
- ✅ Información del equipo visible

---

### Test 10: Lista de Responsivas Firmadas

**Pasos**:
1. En dashboard empleado
2. Ver sección "Mi Historial"

**Resultado esperado**:
- ✅ Ver responsivas firmadas
- ✅ Fecha y hora de firma visible
- ✅ Estado "Firmada" en verde

---

## 🖥️ Testing Gestión de Equipos

### Test 11: Listado de Equipos

**Pasos**:
1. Login como admin
2. Ir a: Equipos (desde dashboard o URL directa)
3. Ver lista de equipos

**Resultado esperado**:
- ✅ Ver tabla con columnas: Equipo, Marca/Modelo, Serie, Sucursal, Asignado a, Estado, Acciones
- ✅ Ver iconos correctos (computadora vs celular)
- ✅ Ver estados con colores

---

### Test 12: Filtros de Equipos

**Pasos**:
1. En página de equipos
2. Filtrar por Tipo: "Computadora"
3. Filtrar por Sucursal: "MB"
4. Filtrar por Estado: "Disponible"

**Resultado esperado**:
- ✅ Resultados filtrados correctamente
- ✅ Contador de resultados actualizado
- ✅ Sin errores de JavaScript

---

### Test 13: Crear Nuevo Equipo

**Pasos**:
1. Clic en "Nuevo Equipo"
2. Llenar formulario:
   - Tipo: Computadora
   - Marca: Dell
   - Modelo: Latitude 5420
   - Número de serie: TEST-001
   - Descripción: Equipo de prueba
3. Clic en "Guardar Equipo"

**Resultado esperado**:
- ✅ Mensaje de éxito
- ✅ Redirección a listado de equipos
- ✅ Nuevo equipo visible en la lista

---

### Test 14: Editar Equipo

**Pasos**:
1. En listado de equipos
2. Clic en "Editar" de un equipo
3. Modificar marca o modelo
4. Guardar cambios

**Resultado esperado**:
- ✅ Cambios guardados correctamente
- ✅ Equipo actualizado en listado

---

### Test 15: Ver Detalles de Equipo

**Pasos**:
1. En listado de equipos
2. Clic en "Ver" de un equipo

**Resultado esperado**:
- ✅ Ver detalles completos del equipo
- ✅ Ver historial de asignaciones (si hay)
- ✅ Ver responsivas asociadas (si hay)

---

### Test 16: Eliminar Equipo

**Pasos**:
1. En listado de equipos
2. Clic en "Eliminar" de un equipo de prueba
3. Confirmar eliminación

**Resultado esperado**:
- ✅ Mensaje de confirmación
- ✅ Equipo removido de la lista
- ✅ Estado cambiado a "dado de_baja" en BD

---

## 👥 Testing Gestión de Empleados

### Test 17: Listado de Empleados

**Pasos**:
1. Login como admin
2. Ir a: Empleados
3. Ver lista de empleados

**Resultado esperado**:
- ✅ Ver tabla con: Nombre, No. Empleado, Puesto, Departamento, Sucursal, Estado, Acciones
- ✅ Ver datos del seed

---

### Test 18: Crear Nuevo Empleado

**Pasos**:
1. Clic en "Nuevo Empleado"
2. Llenar formulario:
   - Número de empleado: TEST999
   - Nombre: Empleado Prueba
   - Puesto: Tester
   - Departamento: QA
   - Sucursal: MB
3. Clic en "Crear Empleado"

**Resultado esperado**:
- ✅ Modal con credenciales temporales
- ✅ Email temporal mostrado
- ✅ Password temporal mostrado
- ✅ Empleado creado en BD

---

### Test 19: Editar Empleado

**Pasos**:
1. En listado de empleados
2. Clic en "Editar"
3. Modificar puesto
4. Guardar

**Resultado esperado**:
- ✅ Cambios guardados
- ✅ Listado actualizado

---

### Test 20: Reset Password de Empleado

**Pasos**:
1. En listado de empleados
2. Clic en "Reset Password"
3. Confirmar acción

**Resultado esperado**:
- ✅ Nuevo password generado
- ✅ Modal con nuevas credenciales
- ✅ Password actualizado en BD

---

## 📝 Testing Creación de Responsivas

### Test 21: Wizard - Paso 1 (Seleccionar Empleado)

**Pasos**:
1. Login como admin
2. Clic en "Nueva Responsiva"
3. En paso 1, buscar empleado: "Juan"
4. Seleccionar empleado de resultados
5. Clic en "Siguiente"

**Resultado esperado**:
- ✅ Búsqueda AJAX funciona
- ✅ Resultados aparecen al escribir
- ✅ Empleado seleccionado resaltado
- ✅ Avanza al paso 2

---

### Test 22: Wizard - Paso 2 (Seleccionar Equipo)

**Pasos**:
1. Seleccionar tipo: "Computadora"
2. Buscar equipo: "Dell"
3. Seleccionar equipo de resultados
4. Clic en "Siguiente"

**Resultado esperado**:
- ✅ Botones de tipo funcionan
- ✅ Búsqueda de equipos funciona
- ✅ Solo equipos disponibles aparecen
- ✅ Avanza al paso 3

---

### Test 23: Wizard - Paso 3 (Confirmar)

**Pasos**:
1. Ver resumen de responsiva
2. Verificar datos de empleado
3. Verificar datos de equipo
4. Clic en "Crear Responsiva"

**Resultado esperado**:
- ✅ Resumen completo visible
- ✅ Datos correctos mostrados
- ✅ Responsiva creada en BD
- ✅ Redirección a dashboard
- ✅ Notificación de éxito

---

### Test 24: Validaciones del Wizard

**Pasos**:
1. Intentar avanzar sin seleccionar empleado
2. Intentar avanzar sin seleccionar equipo
3. Intentar crear responsiva con datos inválidos

**Resultado esperado**:
- ✅ Botones deshabilitados si no hay selección
- ✅ Validaciones funcionan
- ✅ Mensajes de error claros

---

## ✍️ Testing Firma Digital

### Test 25: Acceder a Página de Firma

**Pasos**:
1. Login como empleado con responsiva pendiente
2. Clic en "Firmar Ahora" en una responsiva
3. Ver página de firma

**Resultado esperado**:
- ✅ Documento de responsiva visible
- ✅ Datos del empleado correctos
- ✅ Datos del equipo correctos
- ✅ Canvas de firma visible

---

### Test 26: Dibujar Firma (Mouse)

**Pasos**:
1. En página de firma
2. Dibujar firma con mouse
3. Ver que la firma aparece

**Resultado esperado**:
- ✅ Canvas responde al mouse
- ✅ Firma visible en canvas
- ✅ Sin errores de JavaScript

---

### Test 27: Dibujar Firma (Touch)

**Pasos**:
1. En página de firma (en dispositivo móvil o tablet)
2. Dibujar firma con dedo
3. Ver que la firma aparece

**Resultado esperado**:
- ✅ Canvas responde al touch
- ✅ Firma visible en canvas
- ✅ Sin errores de JavaScript

---

### Test 28: Borrar Firma

**Pasos**:
1. Dibujar firma
2. Clic en "Borrar Firma"

**Resultado esperado**:
- ✅ Canvas se limpia
- ✅ Firma eliminada

---

### Test 29: Firmar Responsiva

**Pasos**:
1. Dibujar firma
2. Clic en "Firmar y Confirmar"
3. Esperar respuesta del servidor

**Resultado esperado**:
- ✅ Petición AJAX enviada
- ✅ Respuesta de éxito recibida
- ✅ Redirección a dashboard empleado
- ✅ Responsiva ahora en "Historial"
- ✅ Responsiva con estado "Firmada"

---

### Test 30: Validación de Firma Vacía

**Pasos**:
1. En página de firma
2. No dibujar nada
3. Clic en "Firmar y Confirmar"

**Resultado esperado**:
- ✅ Error: "Por favor firma antes de continuar"
- ✅ No se envía formulario

---

## 📄 Testing PDF y Notificaciones

> **NOTA**: Estos tests requieren que las dependencias de Composer estén instaladas (`composer install` en servidor)

### Test 31: Generación de PDF (Si Composer instalado)

**Pasos**:
1. Firmar una responsiva
2. Verificar que se genera PDF

**Resultado esperado**:
- ✅ PDF creado en `uploads/responsivas/`
- ✅ PDF tiene nombre: `responsiva_{id}.pdf`
- ✅ PDF incluye firma digital
- ✅ PDF incluye código de verificación

---

### Test 32: Descarga de PDF

**Pasos**:
1. En historial de empleado
2. Clic en "Descargar PDF" de una responsiva firmada

**Resultado esperado**:
- ✅ PDF se descarga
- ✅ PDF abre correctamente
- ✅ Contenido visible y formateado

---

### Test 33: Notificación por Email (Si configurado)

**Pasos**:
1. Crear nueva responsiva como admin
2. Verificar que empleado recibe email

**Resultado esperado**:
- ✅ Email enviado a empleado
- ✅ Email tiene link a firma
- ✅ Email tiene datos del equipo

---

### Test 34: Email a Admins (Si configurado)

**Pasos**:
1. Empleado firma responsiva
2. Verificar que admins reciben email

**Resultado esperado**:
- ✅ Email enviado a admins
- ✅ PDF adjunto al email
- ✅ Email tiene datos de empleado y equipo

---

## ✅ Checklist Final

### Deploy Completo

- [ ] Dominio temporal funciona
- [ ] Login como admin funciona
- [ ] Login como empleado funciona
- [ ] Dashboard admin carga correctamente
- [ ] Dashboard empleado carga correctamente
- [ ] Creación de equipos funciona
- [ ] Creación de empleados funciona
- [ ] Creación de responsivas funciona
- [ ] Firma digital funciona (mouse)
- [ ] Firma digital funciona (touch)
- [ ] Responsiva se marca como firmada
- [ ] PDF se genera (si Composer instalado)
- [ ] Emails se envían (si configurado)

### Seguridad

- [ ] Archivo `db_credentials.php` no es accesible vía URL
- [ ] No se puede acceder a dashboards sin login
- [ ] Empleados no pueden acceder a rutas de admin
- [ ] Passwords están hasheados en BD

### Bugs Conocidos

- [ ] PDF no se genera si Composer no está instalado
- [ ] Emails no se envían si SMTP no está configurado
- [ ] SSL/HTTPS no configurado para `responsivas.grupomb.mx`

---

## 📝 Notas de Testing

**Fecha**: ___/___/______
**Tester**: _______________
**Ambiente**: Producción / Desarrollo

**Bugs encontrados**:
1. ___________________________
2. ___________________________
3. ___________________________

**Mejoras sugeridas**:
1. ___________________________
2. ___________________________
3. ___________________________

---

**Estado final del testing**: [ ] PASÓ / [ ] REQUIERE AJUSTES

**Firma**: ____________________    **Fecha**: ___/___/______
