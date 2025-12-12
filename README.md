# 🚀 Sistema de Usuarios con Roles, Login Seguro y Gestión de Estado

**Versión inicial desarrollada por: *Favián FL***

Este proyecto es un sistema base escrito en **PHP 8 + PDO + Bootstrap
5** diseñado como punto de partida para aplicaciones web que requieren
autenticación segura, control de roles y administración de usuarios.

Es completamente adaptable: cada persona puede modificarlo, extenderlo o
integrarlo en proyectos más grandes.

------------------------------------------------------------------------

# 🔥 Funcionalidades principales

## 🔐 Autenticación segura

-   Manejo de contraseñas con `password_hash()` + `password_verify()`
-   Sesiones seguras en PHP
-   Función **Recordar sesión** mediante token SHA-256
-   Verificación automática de usuarios inactivos
-   Protección centralizada:
    -   `authRequired()`
    -   `requireRole()`

## 👥 Gestión de usuarios (solo rol `admin`)

-   Crear, editar y eliminar usuarios
-   Activación/desactivación (`active` / `inactive`)
-   Buscador sencillo por nombre o correo
-   Prevención de auto-desactivación del administrador
-   Roles disponibles:
    -   `admin`
    -   `user`
    -   `dummy`
    -   `coordinator`

## ✨ Interfaz moderna

-   Navbar con logo
-   Login estilizado con fondo profesional
-   Layouts en **Bootstrap 5**
-   Modales de confirmación para acciones críticas
-   Vistas limpias y organizadas

------------------------------------------------------------------------

# 📁 Estructura del proyecto

    /config
        db.php
    /public
        index.php
        assets/
            css/
            js/
            img/
    /src
        controllers/
        helpers/
        middleware/
        auth/
    /views
        auth/
        users/
        layouts/
    database/
        schema.sql
        seeds.sql
    README.md
    LICENSE

------------------------------------------------------------------------

# ⚙️ Instalación y configuración

## 1️⃣ Clonar el repositorio

``` bash
git clone https://gitlab.com/tu-usuario/tu-repo.git
cd tu-repo
```

## 2️⃣ Crear la base de datos

``` sql
CREATE DATABASE demoPHP CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE demoPHP;
```

## 3️⃣ Importar estructura

``` bash
mysql -u root -p demoPHP < database/schema.sql
```

## 4️⃣ (Opcional) Importar datos de ejemplo

``` bash
mysql -u root -p demoPHP < database/seeds.sql
```

## 5️⃣ Configurar la conexión en `/config/db.php`

``` php
return [
    'host' => 'localhost',
    'dbname' => 'demoPHP',
    'user' => 'root',
    'password' => '',
];
```

------------------------------------------------------------------------

# 🧪 Usuarios de prueba (si cargas seeds.sql)

  Email               Rol     Estatus   Contraseña
  ------------------- ------- --------- ------------
  admin@example.com   admin   active    admin123
  user1@example.com   user    active    user123

------------------------------------------------------------------------

# 🔒 Requisitos

-   **PHP 8 o superior**
-   Extensión **PDO MySQL**
-   Servidor local (MAMP, WAMP, XAMPP, etc.)
-   MySQL 8+
-   Navegador moderno

------------------------------------------------------------------------

# 📜 Licencia

Este proyecto está bajo la licencia **MIT**.\
Puedes usarlo, modificarlo y adaptarlo libremente, siempre manteniendo
referencia a la licencia original.

------------------------------------------------------------------------

# 👨‍💻 Autor

**Anastacio Favián Flores Lira**\
Desarrollador y docente en Tecnologías de la Información.\ UDG
Este proyecto sirve como base para prácticas, demostraciones y futuras
extensiones.

------------------------------------------------------------------------

# 🙌 Contribuciones

Cualquier persona puede: - abrir issues, - proponer mejoras, - enviar
pull requests.

Este repositorio está pensado para aprender, mejorar y experimentar con
autenticación en PHP.

------------------------------------------------------------------------

# ✨ Notas finales

Este sistema fue creado como una versión inicial que otros
desarrolladores pueden adaptar.\
Funciona como **boilerplate** para proyectos web más complejos con
módulos adicionales como dashboards, reportes, gestión extendida, etc.
