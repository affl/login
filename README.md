# Sistema de Usuarios con Roles, Login Seguro y Gestión de Estado

Este proyecto es un sistema base escrito en **PHP 8 + PDO + Bootstrap 5** que incluye:

- Inicio de sesión con verificación de contraseña y PHP Sessions  
- Función *Mantener sesión iniciada* con token seguro  
- Gestión de usuarios con roles (`admin`, `user`, `dummy`, `coordinator`)  
- Activación y desactivación de usuarios (estatus `active` / `inactive`)  
- Protección de rutas según rol  
- Registro de nuevos usuarios (rol `dummy` por defecto)  
- Buscador de usuarios (PHP)  
- Modales de confirmación para baja/reactivación  
- Interfaz moderna con logo y vistas limpias en Bootstrap  

---

## 🚀 Características principales

### 🔐 Autenticación segura
- `password_hash()` y `password_verify()`
- Tokens SHA-256 para recordar sesión
- Prevención de acceso de usuarios inactivos
- Protección centralizada con `authRequired()` y `requireRole()`

### 👥 Gestión completa de usuarios (solo admin)
- Listado con buscador
- Edición de datos
- Activación/desactivación con confirmación por modal
- Prevención de auto-eliminación del admin
- Rol por usuario

### ✨ Interfaz moderna
- Navbar con logo  
- Login estilizado con fondo profesional  
- Vistas limpias con Bootstrap 5  

---

## 📁 Estructura del proyecto

