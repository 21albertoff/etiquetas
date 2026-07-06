<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Sistema de Impresión de Etiquetas</title>

<link rel="stylesheet" href="css/estilos.css">
<link rel="icon" href="assets/favicon.png" type="image/x-icon">

</head>

<body>

<div class="contenedor">

    <header>

        <h1>SISTEMA DE IMPRESIÓN DE ETIQUETAS</h1>

        <div id="hora"></div>

    </header>

    <main>

        <section class="izquierda">

            <label>Código de trazabilidad</label>

            <input
                id="codigo"
                type="text"
                autocomplete="off"
                autofocus
            >

            <div class="datos">

                <div class="fila">
                    <span>Cliente</span>
                    <strong id="cliente"></strong>
                </div>

                <div class="fila">
                    <span>Producto</span>
                    <strong id="producto"></strong>
                </div>

                <div class="fila">
                    <span>Lote</span>
                    <strong id="lote"></strong>
                </div>

                <div class="fila">
                    <span>Fecha</span>
                    <strong id="fecha"></strong>
                </div>

                <div class="fila">
                    <span>Copias</span>
                    <strong id="copias"></strong>
                </div>

            </div>

            <div id="estado" class="estado ok">

                🟢 Esperando lectura...

            </div>

        </section>

        <aside class="derecha">

            <h2>Últimas impresiones</h2>

            <div id="historial"></div>

        </aside>

    </main>

</div>

<script src="js/app.js"></script>

</body>

</html>