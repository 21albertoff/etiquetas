<?php

include("../includes/conexion.php");
include("../includes/funciones.php");

header("Content-Type: application/json");

$codigo = trim($_POST["codigo"] ?? "");

$datos = obtenerDatosEtiqueta($conn, $codigo);

if (!$datos) {

    echo json_encode([
        "ok" => false
    ]);

    exit;
}

echo json_encode([
    "ok" => true,
    "datos" => $datos
]);