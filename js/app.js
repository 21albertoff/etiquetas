//============================================
// VARIABLES
//============================================

let historial = JSON.parse(localStorage.getItem("historialEtiquetas")) || [];
let imprimiendo = false;

//============================================
// INICIO
//============================================

window.onload = () => {

    iniciarReloj();

    cambiarEstado("🟢 Esperando lectura...", "ok");

    mostrarHistorial();

    document.getElementById("codigo").focus();

};

//============================================
// RELOJ
//============================================

function iniciarReloj(){

    actualizarHora();

    setInterval(actualizarHora,1000);

}

function actualizarHora(){

    const ahora = new Date();

    document.getElementById("hora").innerHTML =
        ahora.toLocaleTimeString('es-ES');

}

//============================================
// ESTADO
//============================================

function cambiarEstado(texto,tipo){

    const estado = document.getElementById("estado");

    estado.innerHTML = texto;

    estado.className = "estado";

    estado.classList.add(tipo);

}

//============================================
// BUSCAR
//============================================

async function buscarCodigo(){

    if(imprimiendo) return;

    const codigo = document.getElementById("codigo").value.trim();

    if(codigo=="") return;

    cambiarEstado("🟡 Buscando...","info");

    try{

        const respuesta = await fetch("api/buscar.php",{

            method:"POST",

            headers:{
                "Content-Type":"application/x-www-form-urlencoded"
            },

            body:"codigo="+encodeURIComponent(codigo)

        });

        const texto = await respuesta.text();
        const datos = JSON.parse(texto);

        if(!datos.ok){

            limpiar();

            cambiarEstado("🔴 Código no encontrado","error");

            return;

        }

        const fila = datos.datos[0];

        document.getElementById("cliente").textContent = fila.cliente ?? "";
        document.getElementById("producto").textContent = fila.producto ?? "";
        document.getElementById("lote").textContent = fila.lote ?? "";
        document.getElementById("fecha").textContent = fila.fecha ?? "";
        document.getElementById("copias").textContent = fila.copias ?? "";

        cambiarEstado("🟢 Datos encontrados","ok");

        setTimeout(imprimirEtiqueta,300);

    }

    catch(error){

        console.error(error);

        cambiarEstado("🔴 Error consultando la base de datos","error");

    }

}

//============================================
// IMPRIMIR
//============================================

async function imprimirEtiqueta(){

    if(imprimiendo) return;

    imprimiendo = true;

    document.getElementById("codigo").disabled = true;

    const codigo = document.getElementById("codigo").value.trim();

    cambiarEstado("🟡 Imprimiendo...","info");

    try{

        const respuesta = await fetch("api/imprimir.php",{

            method:"POST",

            headers:{
                "Content-Type":"application/x-www-form-urlencoded"
            },

            body:"codigo="+encodeURIComponent(codigo)

        });

        const texto = await respuesta.text();
        const datos = JSON.parse(texto);

        if(!datos.ok){

            cambiarEstado("🔴 "+datos.mensaje,"error");

            imprimiendo = false;

            document.getElementById("codigo").disabled = false;

            return;

        }

        agregarHistorial(codigo,datos.copias);

        cambiarEstado("🟢 Etiqueta impresa correctamente","ok");

        setTimeout(limpiar,3000);

    }

    catch(error){

        console.error(error);

        cambiarEstado("🔴 Error al imprimir","error");

    }

    imprimiendo = false;

    document.getElementById("codigo").disabled = false;

}

//============================================
// LIMPIAR
//============================================

function limpiar(){

    document.getElementById("codigo").value = "";

    document.getElementById("cliente").innerHTML = "";
    document.getElementById("producto").innerHTML = "";
    document.getElementById("lote").innerHTML = "";
    document.getElementById("fecha").innerHTML = "";
    document.getElementById("copias").innerHTML = "";

    cambiarEstado("🟢 Esperando lectura...","ok");

    document.getElementById("codigo").focus();

}

//============================================
// HISTORIAL
//============================================

function agregarHistorial(codigo, copias){

    historial.unshift({

        hora: new Date().toLocaleTimeString('es-ES'),

        codigo: codigo,

        copias: copias

    });

    // Mantener únicamente las últimas 10
    if(historial.length > 10){

        historial = historial.slice(0, 10);

    }

    // Guardar en el navegador
    localStorage.setItem(
        "historialEtiquetas",
        JSON.stringify(historial)
    );

    mostrarHistorial();

}

function mostrarHistorial(){

    let html = "";

    historial.forEach((item, index) => {

        html += `
        <div class="item">

            <div class="historial-info">
                <strong>${item.hora}</strong><br>
                Código: ${item.codigo}<br>
                Copias: ${item.copias}
            </div>

            <button 
                class="btn-reimprimir"
                onclick="reimprimirHistorial(${index})"
                title="Reimprimir etiqueta">
                🖨️
            </button>

        </div>
        `;

    });

    document.getElementById("historial").innerHTML = html;

}

async function reimprimirHistorial(index){

    if(imprimiendo) return;

    const item = historial[index];

    if(!item) return;

    const codigo = item.codigo;

    imprimiendo = true;

    document.getElementById("codigo").disabled = true;

    cambiarEstado("🟡 Reimprimiendo...", "info");

    try{

        const respuesta = await fetch("api/imprimir.php", {

            method: "POST",

            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },

            body: "codigo=" + encodeURIComponent(codigo)

        });

        const texto = await respuesta.text();
        const datos = JSON.parse(texto);

        if(!datos.ok){

            cambiarEstado(
                "🔴 " + datos.mensaje,
                "error"
            );

            imprimiendo = false;
            document.getElementById("codigo").disabled = false;

            return;
        }

        cambiarEstado(
            "🟢 Etiqueta reimpresa correctamente",
            "ok"
        );

        setTimeout(() => {

            limpiar();

            imprimiendo = false;
            document.getElementById("codigo").disabled = false;

        }, 3000);

    }
    catch(error){

        console.error(error);

        cambiarEstado(
            "🔴 Error al reimprimir",
            "error"
        );

        imprimiendo = false;
        document.getElementById("codigo").disabled = false;

    }

}

//============================================
// EVENTOS
//============================================

document.getElementById("codigo").addEventListener("keydown",function(e){

    if(e.key==="Enter"){

        buscarCodigo();

    }

});