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
// Generar ZPL
//------------------------------------------

$zpl = generarZPL(
    $datos["codigo"],
    $datos["cliente"],
    $datos["lote"],
    $datos["fecha"]
);

//------------------------------------------
// Añadir número de copias
//------------------------------------------

$zpl = str_replace(
    "^XA",
    "^XA^PQ" . intval($datos["copias"]),
    $zpl
);

$resultado = enviarZPL($zpl);
if (!$resultado['ok']) {
    echo json_encode($resultado);
    exit;
}

//------------------------------------------
// Respuesta
//------------------------------------------

echo json_encode([
    "ok" => true,
    "copias" => $datos["copias"]
]);