//============================================
// VARIABLES
//============================================

let historial = [];
let imprimiendo = false;

//============================================
// INICIO
//============================================

window.onload = () => {

    iniciarReloj();

    cambiarEstado("🟢 Esperando lectura...", "ok");

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

        document.getElementById("cliente").innerHTML = datos.cliente;
        document.getElementById("producto").innerHTML = datos.producto;
        document.getElementById("lote").innerHTML = datos.lote;
        document.getElementById("fecha").innerHTML = datos.fecha;
        document.getElementById("copias").innerHTML = datos.copias;

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

        setTimeout(limpiar,1000);

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

function agregarHistorial(codigo,copias){

    historial.unshift({

        hora:new Date().toLocaleTimeString(),

        codigo:codigo,

        copias:copias

    });

    if(historial.length>10){

        historial.pop();

    }

    let html="";

    historial.forEach(item=>{

        html += `
        <div class="item">

            <strong>${item.hora}</strong><br>

            Código: ${item.codigo}<br>

            Copias: ${item.copias}

        </div>
        `;

    });

    document.getElementById("historial").innerHTML = html;

}

//============================================
// EVENTOS
//============================================

document.getElementById("codigo").addEventListener("keydown",function(e){

    if(e.key==="Enter"){

        buscarCodigo();

    }

});