<?php

function validarCodigoTrazabilidad(string $codigo)
{
    $codigo = trim($codigo);
    $partes = explode('.', $codigo);

    if (count($partes) !== 4) {
        return false;
    }

    return $partes;
}

function convertirFechaSubasta(string $valor): string
{
    $valor = trim($valor);

    if (strlen($valor) === 5) {
        $dia = '0' . substr($valor, 0, 1);
        $mes = substr($valor, 1, 2);
        $anio = '20' . substr($valor, 3, 2);
    } else {
        $dia = substr($valor, 0, 2);
        $mes = substr($valor, 2, 2);
        $anio = '20' . substr($valor, 4, 2);
    }

    return sprintf('%s/%s/%s', $dia, $mes, $anio);
}

function obtenerDatosEtiqueta(PDO $conn, string $codigo)
{
    $partes = validarCodigoTrazabilidad($codigo);
    if ($partes === false) {
        return false;
    }

    $stmt = $conn->prepare(
        "SELECT
            v.sVTA_IdSubasta,
            v.sVTA_IdCliente,
            v.sVTA_NVenta,
            v.sVTA_Idenvase,
            v.sVTA_BultosVta,
            g.GEN_NombreGenero
        FROM sb_ventas v
        INNER JOIN generos g
            ON g.GEN_IdGenero=v.sVTA_IdGenero
        WHERE
            v.sVTA_IdSubasta=?
            AND v.sVTA_IdCliente=?
            AND v.sVTA_NVenta=?
        LIMIT 1"
    );

    $stmt->execute([
        $partes[1],
        $partes[2],
        $partes[3]
    ]);

    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$fila) {
        return false;
    }

    return [
        'codigo' => $fila['sVTA_IdCliente'],
        'cliente' => $fila['GEN_NombreGenero'],
        'producto' => $fila['GEN_NombreGenero'],
        'lote' => sprintf(
            'L.%s.%s.%s',
            $fila['sVTA_IdSubasta'],
            $fila['sVTA_IdCliente'],
            $fila['sVTA_NVenta']
        ),
        'fecha' => convertirFechaSubasta($fila['sVTA_IdSubasta']),
        'copias' => calcularCopias(
            $fila['sVTA_Idenvase'], 
            $fila['sVTA_BultosVta'], 
            $fila['sVTA_IdCliente']
        ),
    ];
}

function calcularCopias(int $envase, int $bultos, int $cliente): int
{
    $config = [
        1 => 48,
        2 => 48,
        3 => 150,
        11 => 45,
        12 => 35,
        13 => 70,
        15 => 150,
        16 => 35,
        19 => 48,
        21 => 80,
        31 => 80,
    ];

    if ($cliente === 91300) {
        $config = [
            1 => 30,
            2 => 30,
            3 => 80,
            11 => 25,
            12 => 20,
            13 => 40,
            15 => 80,
            16 => 20,
            19 => 30,
            21 => 40,
            31 => 40,
        ];
    }

    if (!isset($config[$envase])) {
        return 1;
    }

    return max(1, (int) ceil($bultos / $config[$envase]));
}
