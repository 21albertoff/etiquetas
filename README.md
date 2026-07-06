# Sistema de Impresión de Etiquetas

Este proyecto es una interfaz web sencilla para buscar datos de trazabilidad y enviar órdenes de impresión a una impresora Zebra.

## Estructura del proyecto

- `index.php` - Página principal de la aplicación.
- `api/buscar.php` - Busca los datos de la etiqueta según el código.
- `api/imprimir.php` - Genera y envía la impresión a la impresora Zebra.
- `api/config.php` - Devuelve la impresora configurada.
- `assets/` - Recursos estáticos adicionales del proyecto.
- `css/estilos.css` - Estilos visuales de la interfaz.
- `includes/config.php` - Lee las variables del archivo `.env` y define constantes.
- `includes/conexion.php` - Crea la conexión PDO a la base de datos.
- `includes/funciones.php` - Funciones de negocio para buscar datos y calcular copias.
- `includes/zpl.php` - Genera el comando ZPL para la etiqueta.
- `js/app.js` - Lógica del frontend y gestión de la interfaz.
- `logs/` - Carpeta para almacenar registros si se utiliza en el futuro.
- `plantilla/` - Plantillas o archivos base relacionados con etiquetas.

## Uso

1. Copia `.env.example` a `.env`.
2. Ajusta las variables de configuración en `.env`.
3. Accede al proyecto desde tu servidor local (por ejemplo, `http://localhost/etiquetas`).

## Notas

- El archivo `.env` contiene credenciales y no debe subirse al repositorio.
- Se usa `includes/config.php` para leer variables del entorno y simplificar la configuración.

- Variables de impresora: además de `PRINTER_NAME` puedes definir `PRINTER_IP` y `PRINTER_PORT` en el `.env` para indicar la dirección y puerto de la impresora Zebra.
