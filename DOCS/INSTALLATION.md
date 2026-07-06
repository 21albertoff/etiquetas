# Documentación técnica de instalación

## Requisitos

- PHP 7.4+ con PDO y extensión `pdo_mysql`.
- Servidor web compatible (Apache, Nginx, XAMPP, WAMP).
- Acceso a la base de datos MySQL/MariaDB.

## Instalación

1. Clona o copia el proyecto en el directorio de tu servidor web.
2. Asegúrate de que la carpeta del proyecto tenga permisos de lectura para el servidor web.
3. Si usas XAMPP, coloca el proyecto en `C:\xampp\htdocs\etiquetas`.

## Configuración del entorno

1. Copia el archivo de ejemplo:

```bash
copy .env.example .env
```

2. Abre `.env` y define tus valores:

- `DB_HOST` - host de la base de datos.
- `DB_NAME` - nombre de la base de datos.
- `DB_USER` - usuario de la base de datos.
- `DB_PASS` - contraseña del usuario.
- `PRINTER_NAME` - nombre de la impresora Zebra.
 - `PRINTER_IP` - IP de la impresora Zebra (por ejemplo `192.168.1.226`).
 - `PRINTER_PORT` - Puerto de la impresora (por ejemplo `9100`).

3. Guarda el archivo `.env`.

## Estructura de configuración

- `includes/config.php` carga el `.env` y define constantes usadas en todo el proyecto.
- `includes/conexion.php` usa esas constantes para construir la conexión PDO.

## Seguridad

- No subas el archivo `.env` a control de versiones.
- En producción, coloca la configuración sensible fuera de la carpeta pública si es posible.

## Arranque

1. Abre `index.php` desde el navegador en tu servidor local.
2. Usa la interfaz para ingresar el código de trazabilidad.
3. Comprueba que la aplicación puede conectar con la base de datos y mostrar datos.

## Solución de problemas

- Si falla la conexión, revisa las credenciales en `.env`.
- Si la aplicación no encuentra `.env`, usa el archivo `.env.example` como referencia.
- Revisa los permisos del servidor web en los archivos del proyecto.
