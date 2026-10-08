<?php

/*
|--------------------------------------------------------------------------
| Zebra ZT410 - 203 dpi
| Etiqueta física: 105 x 147 mm
| Contenido rotado 90º
|--------------------------------------------------------------------------
*/

function mm($mm)
{
    return round($mm * 8);
}

/*
|--------------------------------------------------------------------------
| Divide un texto en 2 líneas sin cortar palabras
|--------------------------------------------------------------------------
*/

function dividirTexto($texto, $maxCaracteres = 42)
{
    $texto = trim(preg_replace('/\s+/', ' ', $texto));

    if (strlen($texto) <= $maxCaracteres) {
        return [$texto];
    }

    $palabras = explode(" ", $texto);

    $linea1 = "";
    $linea2 = "";

    foreach ($palabras as $palabra) {

        if (strlen($linea1 . " " . $palabra) <= $maxCaracteres) {

            $linea1 = trim($linea1 . " " . $palabra);

        } else {

            $linea2 = trim($linea2 . " " . $palabra);

        }

    }

    return [$linea1, $linea2];
}

/*
|--------------------------------------------------------------------------
| Escribe texto rotado 90º
|--------------------------------------------------------------------------
*/

function escribirTexto(&$zpl, $x, $y, $alto, $ancho, $texto)
{
    $zpl .= "^FO" . mm($x) . "," . mm($y);
    $zpl .= "^A0R," . mm($alto) . "," . mm($ancho);
    $zpl .= "^FD" . strtoupper($texto) . "^FS";
}

/*
|--------------------------------------------------------------------------
| Centrar texto horizontalmente
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Generar ZPL
|--------------------------------------------------------------------------
*/

function generarZPL($codigo, $cliente, $producto, $lote, $fecha, $codigoAgricultor = null, $codigoPartida = null)
{

    /*
    =====================================================
    CONFIGURACIÓN
    =====================================================
    */

    $anchoEtiqueta = mm(105);
    $altoEtiqueta  = mm(147);

    $velocidad = 4;
    $oscuridad = 15;

    /*
    =====================================================
    POSICIONES
    (EN MILÍMETROS)
    =====================================================
    */

    // Código
    $codigoX = 40;
    $codigoY = 8;
    $codigoTam = 59;

    // Cliente
    $clienteX = 40;
    $clienteY = 8;
    $clienteTam = 8;

    // Lote
    $loteX = 4;
    $loteY = 6;
    $loteTam = 6;

    // Código agricultor
    $agricultorX = 10;
    $agricultorY = 6;
    $agricultorTam = 6;

    // Código partida
    $partidaX = 16;
    $partidaY = 6;
    $partidaTam = 6;

    // Fecha
    $fechaX = 4;
    $fechaY = 100;
    $fechaTam = 6;

    /*
    =====================================================
    CREAR ETIQUETA
    =====================================================
    */

    $zpl = "^XA";

    $zpl .= "^PW" . $anchoEtiqueta;
    $zpl .= "^LL" . $altoEtiqueta;

    $zpl .= "^CI28";

    $zpl .= "^PR" . $velocidad;
    $zpl .= "^MD" . $oscuridad;

    /*
    =====================================================
    CÓDIGO CENTRADO
    =====================================================
    */

    escribirTexto(
        $zpl,
        $codigoX,
        $codigoY,
        $codigoTam,
        $codigoTam,
        $codigo
    );

    /*
    =====================================================
    CLIENTE
    =====================================================
    */
    $lineas = dividirTexto($producto, 32);

    escribirTexto(
        $zpl,
        $clienteX,
        $clienteY,
        $clienteTam,
        $clienteTam,
        $lineas[0]
    );

    if (isset($lineas[1])) {

        escribirTexto(
            $zpl,
            $clienteX - 10,
            $clienteY,
            $clienteTam,
            $clienteTam,
            $lineas[1]
        );

    }

    /*
    =====================================================
    LOTE
    =====================================================
    */

    escribirTexto(
        $zpl,
        $loteX,
        $loteY,
        $loteTam,
        $loteTam,
        "LOTE: " . $lote
    );

    /*
    =====================================================
    CÓDIGO AGRICULTOR
    =====================================================
    */

    if ((int)$cliente === 91300 && $codigoAgricultor !== null) {

        escribirTexto(
            $zpl,
            $agricultorX,
            $agricultorY,
            $agricultorTam,
            $agricultorTam,
            "C.A.: " . $codigoAgricultor
        );

    }

    /* 
    ===================================================== 
    CÓDIGO PARTIDA
    ===================================================== 
    */

    if ((int)$cliente === 91300 && $codigoPartida !== null) {

        escribirTexto(
            $zpl,
            $partidaX,
            $partidaY,
            $partidaTam,
            $partidaTam,
            $codigoPartida
        );

    }

    /*
    =====================================================
    FECHA
    =====================================================
    */

    escribirTexto(
        $zpl,
        $fechaX,
        $fechaY,
        $fechaTam,
        $fechaTam,
        "FECHA: " . $fecha
    );

    $zpl .= "^XZ";

    return $zpl;

}