let productos = [];
let indiceActual = 0;
let valorTotal = 0;

// 1. FUNCIÓN PARA PEDIR LA CAJA A TU PHP
async function iniciarCaja(categoriaId) {
    const pantallaInicio = document.getElementById('pantalla-inicio');
    const pantallaJuego = document.getElementById('pantalla-juego'); // Referencia
    const mensajeCarga = document.getElementById('mensaje-carga');
    const sobreIcono = document.getElementById('sobre-icono');

    // Efecto visual de carga
    sobreIcono.classList.add('oculto');
    mensajeCarga.classList.remove('oculto');

    try {
        const respuesta = await fetch(`api/obtener_caja.php?cat=${categoriaId}`);
        const datos = await respuesta.json();

        if (datos.exito) {
            productos = datos.productos;
            valorTotal = datos.valor_real;
            indiceActual = 0;
            
            cargarCarta(0);
            
            // --- CAMBIO DE PANTALLA ---
            pantallaInicio.classList.remove('activa'); // Quitamos la vieja
            
            pantallaJuego.classList.remove('oculto'); // <--- CORRECCIÓN IMPORTANTE: Quitamos el candado oculto
            pantallaJuego.classList.add('activa');    // Activamos la nueva
            
        } else {
            alert("Error: No hay productos suficientes.");
            location.reload();
        }

    } catch (error) {
        console.error(error);
        alert("Error de conexión.");
        location.reload();
    }
}

// 2. CARGAR CARTA
function cargarCarta(indice) {
    const carta = document.getElementById('carta-activa');
    const btnSiguiente = document.getElementById('btn-siguiente');
    
    carta.classList.remove('volteada'); 
    btnSiguiente.classList.add('oculto');
    
    document.getElementById('contador-actual').innerText = indice + 1;

    setTimeout(() => {
        const p = productos[indice];
        document.getElementById('img-producto').src = p.imagen_url;
        document.getElementById('nombre-producto').innerText = p.nombre;
        document.getElementById('precio-producto').innerText = p.precio + "€";
    }, 200);
}

// 3. GIRAR CARTA
function voltearCarta() {
    const carta = document.getElementById('carta-activa');
    if (!carta.classList.contains('volteada')) {
        carta.classList.add('volteada');
        setTimeout(() => {
            document.getElementById('btn-siguiente').classList.remove('oculto');
        }, 600);
    }
}

// 4. SIGUIENTE
function siguienteCarta() {
    indiceActual++;
    if (indiceActual < productos.length) {
        cargarCarta(indiceActual);
    } else {
        mostrarResumen();
    }
}

// 5. RESUMEN FINAL
function mostrarResumen() {
    const pantallaJuego = document.getElementById('pantalla-juego');
    const pantallaResumen = document.getElementById('pantalla-resumen');

    pantallaJuego.classList.remove('activa');
    pantallaJuego.classList.add('oculto'); // Opcional: la ocultamos explícitamente

    pantallaResumen.classList.remove('oculto'); // <--- CORRECCIÓN IMPORTANTE
    pantallaResumen.classList.add('activa');
    
    document.getElementById('valor-total').innerText = valorTotal.toFixed(2) + "€";

    const grid = document.getElementById('grid-productos');
    grid.innerHTML = ""; 

    productos.forEach(p => {
        const img = document.createElement('img');
        img.src = p.imagen_url;
        img.title = p.nombre;
        grid.appendChild(img);
    });
}