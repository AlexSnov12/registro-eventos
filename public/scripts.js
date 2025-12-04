document.addEventListener('DOMContentLoaded', function() {
    const API_BASE = window.location.origin + '/api/crud.php';
    
    // Referencias a elementos
    const registroForm = document.getElementById('registroForm');
    const buscarForm = document.getElementById('buscarForm');
    const refrescarBtn = document.getElementById('refrescarLista');
    const resultadoRegistro = document.getElementById('resultadoRegistro');
    const resultadoBusqueda = document.getElementById('resultadoBusqueda');
    const listaRegistros = document.getElementById('listaRegistros');

    // Registrar nuevo
    registroForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch(API_BASE, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(formData).toString()
            });
            
            const data = await response.json();
            
            if (data.success) {
                mostrarResultado(resultadoRegistro, 'success', 
                    `✅ Registro exitoso!<br>Tu folio: <strong>${data.folio}</strong>`);
                registroForm.reset();
                cargarRegistros();
            } else {
                mostrarResultado(resultadoRegistro, 'error', `❌ ${data.message}`);
            }
        } catch (error) {
            mostrarResultado(resultadoRegistro, 'error', '❌ Error de conexión');
        }
    });

    // Buscar registro
    buscarForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        try {
            const response = await fetch(API_BASE, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'read',
                    folio: formData.get('folio')
                }).toString()
            });
            
            const data = await response.json();
            
            if (data.success) {
                const reg = data.data;
                mostrarResultado(resultadoBusqueda, 'success',
                    `✅ <strong>Registro encontrado:</strong><br>
                     Folio: ${reg.folio}<br>
                     Nombre: ${reg.nombre}<br>
                     Teléfono: ${reg.telefono}<br>
                     Fecha: ${reg.fecha_registro}`);
            } else {
                mostrarResultado(resultadoBusqueda, 'error', `❌ ${data.message}`);
            }
        } catch (error) {
            mostrarResultado(resultadoBusqueda, 'error', '❌ Error de conexión');
        }
    });

    // Refrescar lista
    refrescarBtn.addEventListener('click', cargarRegistros);

    // Cargar registros al inicio
    cargarRegistros();

    // Funciones auxiliares
    function mostrarResultado(elemento, tipo, mensaje) {
        elemento.innerHTML = mensaje;
        elemento.className = 'alert ' + tipo;
        
        setTimeout(() => {
            elemento.style.display = 'none';
        }, 5000);
    }

    async function cargarRegistros() {
        listaRegistros.innerHTML = '<p class="loading"><i class="fas fa-spinner fa-spin"></i> Cargando...</p>';
        
        try {
            const response = await fetch(API_BASE, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=list'
            });
            
            const data = await response.json();
            
            if (data.success) {
                if (data.data.length === 0) {
                    listaRegistros.innerHTML = '<p class="loading">No hay registros todavía.</p>';
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
                        <div class="fecha">${registro.fecha_registro}</div>
                    </div>
                    `;
                });
                
                listaRegistros.innerHTML = html;
            } else {
                listaRegistros.innerHTML = '<p class="loading">❌ Error al cargar</p>';
            }
        } catch (error) {
            listaRegistros.innerHTML = '<p class="loading">❌ Error de conexión</p>';
        }
    }
});