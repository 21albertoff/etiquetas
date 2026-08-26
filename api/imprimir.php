<?php

include("../includes/conexion.php");
include("../includes/funciones.php");
include("../includes/zpl.php");
include("../includes/printer.php");

header("Content-Type: application/json");

$codigo = trim($_POST["codigo"] ?? "");

if ($codigo === "") {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Código vacío"
    ]);
    exit;
}

$datos = obtenerDatosEtiqueta($conn, $codigo);

if (!$datos) {

    echo json_encode([
        "ok" => false,
        "mensaje" => "Código no encontrado"
    ]);

    exit;
}

//------------------------------------------
// Generar ZPL de todas las etiquetas
//------------------------------------------

$zplTotal = "";
$totalCopias = 0;

foreach ($datos as $fila) {

    $zpl = generarZPL(
        $fila["codigo"],
        $fila["cliente"],
        $fila["producto"],
        $fila["lote"],
        $fila["fecha"]
    );

    //------------------------------------------
    // Número de copias
    //------------------------------------------

    $zpl = str_replace(
        "^XA",
        "^XA^PQ" . intval($fila["copias"]),
        $zpl
    );

    $zplTotal .= $zpl;

    $totalCopias += intval($fila["copias"]);
}

//------------------------------------------
// Enviar todo a la impresora
//------------------------------------------

$resultado = enviarZPL($zplTotal);

if (!$resultado['ok']) {
    echo json_encode($resultado);
    exit;
}

//------------------------------------------
// Respuesta
//------------------------------------------

echo json_encode([
    "ok" => true,
    "copias" => $totalCopias
]);