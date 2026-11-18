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
<<<<<<< HEAD
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
            AND v.sVTA_NVenta=?"
    );
=======
    v.sVTA_IdSubasta,
    v.sVTA_IdCliente,
    v.sVTA_NVenta,
    v.sVTA_Idenvase,
    SUM(v.sVTA_BultosVta) AS TotalBultosVta,
    g.GEN_NombreGenero,
    ae.AEN_idAgriPrincipal AS codigoAgricultor,
    ae.AEN_campa,
    ael.AEL_muestreo


FROM sb_ventas v

INNER JOIN generos g
    ON g.GEN_IdGenero = v.sVTA_IdGenero

INNER JOIN albentrada_lineas ael
    ON ael.AEL_idlinea = v.sVTA_IdLineaEntrada

INNER JOIN albentrada ae
    ON ae.AEN_idalbaran = ael.AEL_idalbaran

WHERE
    v.sVTA_IdSubasta = ?
    AND v.sVTA_IdCliente = ?
    AND v.sVTA_NVenta = ?

GROUP BY
    v.sVTA_IdSubasta,
    v.sVTA_IdCliente,
    v.sVTA_NVenta,
    v.sVTA_Idenvase,
    g.GEN_NombreGenero,
    ae.AEN_idAgriPrincipal,
    ae.AEN_campa,
    ael.AEL_muestreo

ORDER BY
    v.sVTA_Idenvase,
    g.GEN_NombreGenero;
    ");

    
>>>>>>> 614fd4b2820060f6489f192dafefbc61c6c47045

    $stmt->execute([
        $partes[1],
        $partes[2],
        $partes[3]
    ]);

    $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
<<<<<<< HEAD
=======

>>>>>>> 614fd4b2820060f6489f192dafefbc61c6c47045
    if (!$filas) {
        return false;
    }

<<<<<<< HEAD
    $fila = $filas[0];

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
        'copias' => calcularCopiasVenta($filas, (int) $fila['sVTA_IdCliente']),
    ];
=======
    $resultado = [];

    foreach ($filas as $fila) {

        $bultos = (int) $fila['TotalBultosVta'];
        $envase = (int) $fila['sVTA_Idenvase'];
        $cliente = (int) $fila['sVTA_IdCliente'];

        $resultado[] = [
            'codigo' => $cliente,

            'cliente' => $cliente,

            'codigoAgricultor' => $fila['codigoAgricultor'],

            'producto' => $fila['GEN_NombreGenero'],

            'genero' => $fila['GEN_NombreGenero'],

            'envase' => $envase,

            'bultos' => $bultos,

            'lote' => sprintf(
                'L.%s.%s.%s',
                $fila['sVTA_IdSubasta'],
                $fila['sVTA_IdCliente'],
                $fila['sVTA_NVenta']
            ),

            'codigoPartida' => sprintf(
                'P.%s.%s',
                $fila['AEN_campa'],
                $fila['AEL_muestreo']
            ),

            'fecha' => convertirFechaSubasta(
                $fila['sVTA_IdSubasta']
            ),

            'copias' => calcularCopias(
                $envase,
                $bultos,
                $cliente
            ),
        ];
    }

    return $resultado;
>>>>>>> 614fd4b2820060f6489f192dafefbc61c6c47045
}

function calcularCopiasVenta(array $filas, int $cliente): int
{
    if ($cliente === 91300) {
        return calcularCopias(
            (int) $filas[0]['sVTA_Idenvase'],
            (int) $filas[0]['sVTA_BultosVta'],
            $cliente
        );
    }

    $bultosPorEnvase = [];

    foreach ($filas as $fila) {
        $envase = (int) $fila['sVTA_Idenvase'];
        $bultosPorEnvase[$envase] = ($bultosPorEnvase[$envase] ?? 0)
            + (int) $fila['sVTA_BultosVta'];
    }

    $copias = 0;
    foreach ($bultosPorEnvase as $envase => $bultos) {
        $copias += calcularCopias((int) $envase, $bultos, $cliente);
    }

    return max(1, $copias);
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
