<?php

// Carga básica de variables desde un archivo .env en la raíz del proyecto
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
	$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	foreach ($lines as $line) {
		$line = trim($line);
		if ($line === '' || strpos($line, '#') === 0) continue;
		if (strpos($line, '=') === false) continue;
		list($name, $value) = explode('=', $line, 2);
		$name = trim($name);
		$value = trim($value);
		if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
			(substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
			$value = substr($value, 1, -1);
		}
		putenv("$name=$value");
		$_ENV[$name] = $value;
	}
}

// Parámetros de configuración con valores por defecto
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('DB_NAME') ?: 'etiquetas');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('PRINTER_NAME', getenv('PRINTER_NAME') ?: 'LISLR-ZEBRA');
define('PRINTER_IP', getenv('PRINTER_IP') ?: '192.168.1.226');
define('PRINTER_PORT', getenv('PRINTER_PORT') ?: 9100);
