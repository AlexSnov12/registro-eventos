document.addEventListener('DOMContentLoaded', function() {
    // URL de la API - usar ruta relativa
    const API_BASE = window.location.origin + '/crud.php';
    console.log('API URL:', API_BASE); // Para debugging
    
    // Referencias a elementos
    const registroForm = document.getElementById('registroForm');
    const buscarForm = document.getElementById('buscarForm');
    const refrescarBtn = document.getElementById('refrescarLista');
    const resultadoRegistro = document.getElementById('resultadoRegistro');
    const resultadoBusqueda = document.getElementById('resultadoBusqueda');
    const listaRegistros = document.getElementById('listaRegistros');

    // Función para mostrar mensajes
    function mostrarMensaje(elemento, tipo, mensaje) {
        elemento.innerHTML = mensaje;
        elemento.className = 'alert ' + tipo;
        elemento.style.display = 'block';
        
        // Ocultar después de 5 segundos
        setTimeout(() => {
            elemento.style.display = 'none';
        }, 5000);
    }

    // Registrar nuevo
    registroForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const nombre = document.getElementById('nombre').value.trim();
        const telefono = document.getElementById('telefono').value.trim();
        
        if (!nombre || !telefono) {
            mostrarMensaje(resultadoRegistro, 'error', '❌ Nombre y teléfono son requeridos');
            return;
        }
        
        if (!/^\d{10}$/.test(telefono)) {
            mostrarMensaje(resultadoRegistro, 'error', '❌ El teléfono debe tener 10 dígitos');
            return;
        }
        
        try {
            const response = await fetch(API_BASE, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=create&nombre=${encodeURIComponent(nombre)}&telefono=${telefono}`
            });
            
            const data = await response.json();
            console.log('Create response:', data); // Para debugging
            
            if (data.success) {
                mostrarMensaje(resultadoRegistro, 'success', 
                    `✅ Registro exitoso!<br><strong>Tu folio: ${data.folio}</strong><br>Guárdalo para gestionar tu registro.`);
                registroForm.reset();
                cargarRegistros();
            } else {
                mostrarMensaje(resultadoRegistro, 'error', `❌ Error: ${data.message}`);
            }
        } catch (error) {
            console.error('Fetch error:', error);
            mostrarMensaje(resultadoRegistro, 'error', '❌ Error de conexión con el servidor');
        }
    });

    // Buscar registro
    buscarForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const folio = document.getElementById('folioBuscar').value.trim();
        
        if (!folio) {
            mostrarMensaje(resultadoBusqueda, 'error', '❌ Ingresa un folio');
            return;
        }
        
        try {
            const response = await fetch(API_BASE, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=read&folio=${encodeURIComponent(folio)}`
            });
            
            const data = await response.json();
            console.log('Read response:', data);
            
            if (data.success) {
                const reg = data.data;
                mostrarMensaje(resultadoBusqueda, 'success',
                    `✅ <strong>Registro encontrado:</strong><br>
                     <strong>Folio:</strong> ${reg.folio}<br>
                     <strong>Nombre:</strong> ${reg.nombre}<br>
                     <strong>Teléfono:</strong> ${reg.telefono}<br>
                     <strong>Fecha:</strong> ${reg.fecha_registro}`);
            } else {
                mostrarMensaje(resultadoBusqueda, 'error', `❌ ${data.message}`);
            }
        } catch (error) {
            console.error('Fetch error:', error);
            mostrarMensaje(resultadoBusqueda, 'error', '❌ Error de conexión');
        }
    });

    // Refrescar lista
    refrescarBtn.addEventListener('click', cargarRegistros);

    // Cargar registros
    async function cargarRegistros() {
        listaRegistros.innerHTML = '<p class="loading"><i class="fas fa-spinner fa-spin"></i> Cargando registros...</p>';
        
        try {
            const response = await fetch(API_BASE, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=list'
            });
            
            const data = await response.json();
            console.log('List response:', data);
            
            if (data.success) {
                if (data.data.length === 0) {
                    listaRegistros.innerHTML = '<p class="loading">No hay registros todavía. ¡Sé el primero!</p>';
                    return;
                }
                
                let html = '';
                data.data.forEach(registro => {
                    html += `
                    <div class="registro-item">
                        <div>
                            <div class="folio">${registro.folio}</div>
                            <div class="nombre">${registro.nombre}</div>
                            <div class="telefono">📱 ${registro.telefono}</div>
                        </div>
                        <div class="fecha">${new Date(registro.fecha_registro).toLocaleDateString()}</div>
                    </div>
                    `;
                });
                
                listaRegistros.innerHTML = html;
            } else {
                listaRegistros.innerHTML = '<p class="loading">❌ Error al cargar registros</p>';
            }
        } catch (error) {
            console.error('Fetch error:', error);
            listaRegistros.innerHTML = '<p class="loading">❌ Error de conexión</p>';
        }
    }

    // Cargar registros al inicio
    cargarRegistros();
});