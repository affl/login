
---

## 🔐 Autenticación

El sistema utiliza:

- Sesiones para manejo estándar
- Token seguro generado con `random_bytes()`
- Hash SHA-256 almacenado en base de datos
- Cookie HttpOnly para evitar ataques XSS

---

## 🛠 Requisitos

- PHP 8+
- MySQL / MariaDB
- Servidor local (MAMP, XAMPP, Laragon, etc.)

---

## ⚙️ Configuración

Modificar `config/database.php` con tus credenciales:

```php
$dsn  = 'mysql:host=localhost;dbname=my_base;charset=utf8mb4';
$user = 'my_user';
$pass = 'my_password';