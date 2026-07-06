<?php

require_once __DIR__ . '/config.php';

function enviarZPL(string $zpl, string $host = PRINTER_IP, int $port = PRINTER_PORT, int $timeout = 5): array
{
    if (trim($zpl) === '') {
        return [
            'ok' => false,
            'mensaje' => 'ZPL vacío. No se envió nada a la impresora.'
        ];
    }

    $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
    if (!$socket) {
        return [
            'ok' => false,
            'mensaje' => "No se puede conectar con la impresora: $errstr"
        ];
    }

    fwrite($socket, $zpl);
    fflush($socket);
    fclose($socket);

    return ['ok' => true];
}
