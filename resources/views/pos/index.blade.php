@extends('layouts.app')

@section('content')

<style>
    .table-custom-header th {
        background-color: #f1f5f9 !important;
        font-size: 0.8rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 15px 10px;
        border-bottom: none !important;
    }
    .table-custom-header th:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
    .table-custom-header th:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }

    .table-custom tbody tr { border-bottom: 1px solid #f0f2f5; transition: background-color 0.2s; }
    .table-custom tbody tr:hover { background-color: #f8f9fa; }
    .table-custom td { vertical-align: middle; padding: 12px 10px; }
    
    .card { border: none !important; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important; border-radius: .5rem !important; }
    
    /* ESTILOS CELESTES DEL BUSCADOR */
    .search-btn-left { background-color: #0dcaf0; color: white; border-top-left-radius: 8px; border-bottom-left-radius: 8px; border: none; padding: 0 20px; }
    .search-input { border-top-right-radius: 8px; border-bottom-right-radius: 8px; border: 1px solid #e2e8f0; background-color: #f8fafc; }
</style>

<!-- ALERTAS DE ÉXITO O ERROR (NUEVO) -->
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('success_venta_id'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm d-flex justify-content-between align-items-center" role="alert">
        <div>
            <i class="bi bi-check-circle-fill me-2"></i> ¡Venta cobrada con éxito! (ID: {{ session('success_venta_id') }})
        </div>
        <!-- BOTÓN MÁGICO PARA EL PDF -->
        <a href="/ventas/{{ session('success_venta_id') }}/pdf" target="_blank" class="btn btn-sm btn-light fw-bold text-success shadow-sm">
            <i class="bi bi-printer me-1"></i> Imprimir Nota
        </a>
        <button type="button" class="btn-close position-relative top-0 end-0 ms-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<!-- FIN DE ALERTAS -->


<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div class="bg-info bg-opacity-10 p-2 rounded me-3 text-info">
            <i class="bi bi-cart3 fs-3"></i>
        </div>
        <div>
            <h3 class="fw-bold m-0 text-dark">Punto de Venta (POS)</h3>
            <small class="text-muted">Selecciona los productos para armar la venta</small>
        </div>
    </div>
</div>


<div class="row">
    <!-- COLUMNA IZQUIERDA: Lista de Productos -->
    <div class="col-md-7 col-lg-8 mb-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold m-0">Productos Disponibles</h6>
                <div class="input-group" style="width: 280px; height: 42px;">
                    <button class="search-btn-left" type="button"><i class="bi bi-search"></i></button>
                    <input type="text" id="buscadorProductos" class="form-control search-input" placeholder="Buscar producto...">
                </div>
            </div>
            <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                <table class="table table-custom align-middle m-0" id="tablaProductos">
                    <thead class="table-custom-header sticky-top">
                        <tr>
                            <th class="ps-4">Código</th>
                            <th>Producto</th>
                            <th>Stock</th>
                            <th>Precio</th>
                            <th class="text-center pe-4">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productos as $producto)
                        <!-- Añadimos atributos 'data-' para que JS pueda leer los datos del producto -->
                        <tr class="fila-producto" 
                            data-id="{{ $producto->id }}" 
                            data-codigo="{{ $producto->codigo }}"
                            data-nombre="{{ $producto->nombre }}"
                            data-precio="{{ $producto->precio_venta }}"
                            data-stock="{{ $producto->stock }}">
                            
                            <td class="ps-4"><span class="badge bg-light text-dark border">{{ $producto->codigo }}</span></td>
                            <td>
                                <div class="fw-bold">{{ $producto->nombre }}</div>
                                <small class="text-muted">{{ optional($producto->marca)->nombre ?? 'Genérico' }}</small>
                            </td>
                            <td><span class="badge bg-success-subtle text-success border border-success-subtle">{{ $producto->stock }}</span></td>
                            <td class="fw-bold text-primary">Bs. {{ number_format($producto->precio_venta, 2) }}</td>
                            <td class="text-center pe-4">
                                <!-- El botón ahora tiene la clase 'btn-agregar-producto' para que JS lo identifique -->
                                <button type="button" class="btn btn-sm btn-outline-primary fw-bold px-3 rounded-pill btn-agregar-producto">
                                    <i class="bi bi-plus-lg"></i> Agregar
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-box-seam fs-1 d-block mb-2"></i>
                                No hay productos con stock disponible para la venta.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- COLUMNA DERECHA: El Carrito / Resumen de Venta -->
    <div class="col-md-5 col-lg-4 mb-4">
        <div class="card border-0 shadow rounded-3 h-100 border-top border-primary border-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold m-0">Resumen de Venta</h5>
            </div>
            
            <!-- FORMULARIO PARA ENVIAR AL SERVIDOR -->
            <form id="formVenta" action="/pos/cobrar" method="POST" class="d-flex flex-column h-100 m-0">
                @csrf
                <div class="card-body d-flex flex-column p-0">
                    
                    <!-- Área del Carrito (JS insertará los ítems aquí) -->
                    <div class="flex-grow-1 p-3 bg-light" style="min-height: 250px; max-height: 350px; overflow-y: auto;" id="contenedorCarrito">
                        <div class="text-center text-muted mt-5" id="mensajeCarritoVacio">
                            <i class="bi bi-cart-x fs-1"></i>
                            <p class="mt-2">El carrito está vacío</p>
                        </div>
                        
                        <!-- Lista oculta donde se irán creando los items del carrito -->
                        <ul class="list-group list-group-flush" id="listaCarrito">
                            <!-- JS agregará los <li> aquí -->
                        </ul>
                    </div>

                    <!-- Totales y Botón de Cobrar -->
                    <div class="p-4 bg-white border-top">
                        <div class="d-flex justify-content-between mb-2 text-muted">
                            <span>Subtotal:</span>
                            <span id="txtSubtotal">Bs. 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4 mt-2">
                            <span class="fs-4 fw-bold">TOTAL:</span>
                            <span class="fs-4 fw-bold text-success" id="txtTotal">Bs. 0.00</span>
                        </div>
                        
                        <!-- Inputs ocultos para enviar la info del carrito al controlador -->
                        <input type="hidden" name="total_venta" id="inputTotalVenta" value="0">
                        <div id="inputsOcultosCarrito"></div>

                        <div class="mb-3 text-start">
                            <label class="form-label text-muted">Nº de Nota Física (Opcional)</label>
                            <input type="text" name="numero_nota" class="form-control" placeholder="Ej. 00154 (Vacío = automático)">
                            <small class="text-muted" style="font-size: 0.75rem;">Si es de talonario manual, escríbala aquí.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Nombre del Cliente *</label>
                            <input type="text" name="cliente" id="inputCliente" class="form-control" placeholder="Ej. Juan Pérez" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Método de Pago *</label>
                            <div class="d-flex gap-2">
                                <!-- Opción Efectivo (Seleccionado por defecto) -->
                                <input type="radio" class="btn-check" name="metodo_pago" id="pago_efectivo" value="Efectivo" checked>
                                <label class="btn btn-outline-success w-100 d-flex flex-column align-items-center py-2 shadow-sm" for="pago_efectivo">
                                    <i class="bi bi-cash-coin fs-4 mb-1"></i>
                                    <span style="font-size: 0.85rem; font-weight: bold;">Efectivo</span>
                                </label>

                                <!-- Opción QR -->
                                <input type="radio" class="btn-check" name="metodo_pago" id="pago_qr" value="QR">
                                <label class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-2 shadow-sm" for="pago_qr">
                                    <i class="bi bi-qr-code-scan fs-4 mb-1"></i>
                                    <span style="font-size: 0.85rem; font-weight: bold;">QR</span>
                                </label>

                                <!-- Opción Transferencia -->
                                <input type="radio" class="btn-check" name="metodo_pago" id="pago_transferencia" value="Transferencia">
                                <label class="btn btn-outline-info w-100 d-flex flex-column align-items-center py-2 shadow-sm" for="pago_transferencia">
                                    <i class="bi bi-bank fs-4 mb-1"></i>
                                    <span style="font-size: 0.85rem; font-weight: bold;">Transf.</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-3 fw-bold fs-5 shadow-sm rounded-3" id="btnCobrar" disabled>
                            <i class="bi bi-cash-coin me-2"></i> COBRAR VENTA
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- EL MOTOR JAVASCRIPT DEL CARRITO ⚙️           -->
<!-- ============================================== -->
<script>
    document.addEventListener('DOMContentLoaded', function() {


        // Lógica para el Pop-up del código QR
        const radiosPago = document.querySelectorAll('input[name="metodo_pago"]');
        // Inicializamos el modal usando Bootstrap en JS
        const modalQR = new bootstrap.Modal(document.getElementById('modalQR'));
        const totalModalQR = document.getElementById('totalModalQR');

        radiosPago.forEach(radio => {
            radio.addEventListener('change', function() {
                // Si el cajero selecciona QR
                if (this.value === 'QR') {
                    // Copiamos el total actual del carrito al modal para que el cliente lo vea
                    const totalActual = document.getElementById('txtTotal').textContent;
                    totalModalQR.textContent = totalActual;
                    
                    // Mostramos el pop-up
                    modalQR.show();
                }
            });
        });

        // Variables globales
        let carrito = []; // Aquí guardaremos los productos en memoria
        
        // Elementos del DOM (la pantalla)
        const botonesAgregar = document.querySelectorAll('.btn-agregar-producto');
        const listaCarrito = document.getElementById('listaCarrito');
        const mensajeVacio = document.getElementById('mensajeCarritoVacio');
        const txtSubtotal = document.getElementById('txtSubtotal');
        const txtTotal = document.getElementById('txtTotal');
        const inputTotalVenta = document.getElementById('inputTotalVenta');
        const inputCliente = document.getElementById('inputCliente');
        const inputsOcultos = document.getElementById('inputsOcultosCarrito');
        const btnCobrar = document.getElementById('btnCobrar');
        const buscador = document.getElementById('buscadorProductos');
        const filasProductos = document.querySelectorAll('.fila-producto');

        // 1. Buscador en tiempo real (Bono extra para tu UX)
        buscador.addEventListener('keyup', function() {
            const texto = this.value.toLowerCase();
            filasProductos.forEach(fila => {
                const nombre = fila.dataset.nombre.toLowerCase();
                const codigo = fila.dataset.codigo.toLowerCase();
                if (nombre.includes(texto) || codigo.includes(texto)) {
                    fila.style.display = ''; // Mostrar
                } else {
                    fila.style.display = 'none'; // Ocultar
                }
            });
        });

        // 2. Evento: Cuando hacen clic en "Agregar"
        botonesAgregar.forEach(boton => {
            boton.addEventListener('click', function() {
                // Leer datos de la fila
                const fila = this.closest('tr');
                const id = fila.dataset.id;
                const nombre = fila.dataset.nombre;
                const precio = parseFloat(fila.dataset.precio);
                const stockMaximo = parseInt(fila.dataset.stock);

                // Revisar si ya está en el carrito
                const productoExistente = carrito.find(item => item.id === id);

                if (productoExistente) {
                    // Si ya existe, sumarle 1 (solo si hay stock)
                    if (productoExistente.cantidad < stockMaximo) {
                        productoExistente.cantidad++;
                    } else {
                        alert(`¡Ups! Solo hay ${stockMaximo} unidades disponibles en stock.`);
                    }
                } else {
                    // Si no existe, agregarlo al arreglo
                    carrito.push({
                        id: id,
                        nombre: nombre,
                        precio: precio,
                        cantidad: 1,
                        stockMaximo: stockMaximo
                    });
                }
                
                // Actualizar la pantalla
                renderizarCarrito();
            });
        });

        function validarBotonCobrar() {
            const hayProductos = carrito.length > 0;
            const hayCliente = inputCliente.value.trim() !== '';

            // El botón se habilita SOLO si hay productos en el carrito Y escribieron un nombre
            if (hayProductos && hayCliente) {
                btnCobrar.disabled = false;
            } else {
                btnCobrar.disabled = true;
            }
        }

        // Escuchar cada vez que el cajero teclee una letra en el nombre del cliente
        inputCliente.addEventListener('input', validarBotonCobrar);

        // 3. Función Principal: Dibujar el carrito y calcular totales
        function renderizarCarrito() {
            // Limpiar listas
            listaCarrito.innerHTML = '';
            inputsOcultos.innerHTML = '';
            let total = 0;

            if (carrito.length === 0) {
                // Si el carrito está vacío, mostrar el mensaje y bloquear el botón
                mensajeVacio.style.display = 'block';
                validarBotonCobrar();
                txtSubtotal.textContent = 'Bs. 0.00';
                txtTotal.textContent = 'Bs. 0.00';
                inputTotalVenta.value = 0;
                return;
            }

            // Si hay productos, ocultar el mensaje y habilitar el botón
            mensajeVacio.style.display = 'none';
            validarBotonCobrar();

            // Recorrer el arreglo del carrito
            carrito.forEach((item, index) => {
                const subtotalItem = item.precio * item.cantidad;
                total += subtotalItem;

                // A. Crear el diseño visual del producto en la lista derecha
                const li = document.createElement('li');
                li.className = 'list-group-item bg-transparent px-0 border-bottom';
                li.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-truncate" style="max-width: 180px;">${item.nombre}</span>
                        <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-eliminar" data-index="${index}">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="input-group input-group-sm" style="width: 100px;">
                            <button class="btn btn-outline-secondary btn-restar" type="button" data-index="${index}">-</button>
                            <input type="text" class="form-control text-center bg-white" value="${item.cantidad}" readonly>
                            <button class="btn btn-outline-secondary btn-sumar" type="button" data-index="${index}">+</button>
                        </div>
                        <span class="text-primary fw-bold">Bs. ${subtotalItem.toFixed(2)}</span>
                    </div>
                `;
                listaCarrito.appendChild(li);

                // B. Crear inputs ocultos para que PHP reciba los datos al presionar "Cobrar"
                inputsOcultos.innerHTML += `
                    <input type="hidden" name="productos[${index}][id]" value="${item.id}">
                    <input type="hidden" name="productos[${index}][cantidad]" value="${item.cantidad}">
                    <input type="hidden" name="productos[${index}][precio]" value="${item.precio}">
                `;
            });

            // Actualizar Totales en pantalla
            txtSubtotal.textContent = `Bs. ${total.toFixed(2)}`;
            txtTotal.textContent = `Bs. ${total.toFixed(2)}`;
            inputTotalVenta.value = total;

            // 4. Asignar eventos a los nuevos botones (+, -, eliminar)
            asignarEventosCarrito();
        }

        function asignarEventosCarrito() {
            // Botones SUMAR
            document.querySelectorAll('.btn-sumar').forEach(btn => {
                btn.addEventListener('click', function() {
                    const idx = this.dataset.index;
                    if (carrito[idx].cantidad < carrito[idx].stockMaximo) {
                        carrito[idx].cantidad++;
                        renderizarCarrito();
                    } else {
                        alert('No hay más stock disponible.');
                    }
                });
            });

            // Botones RESTAR
            document.querySelectorAll('.btn-restar').forEach(btn => {
                btn.addEventListener('click', function() {
                    const idx = this.dataset.index;
                    if (carrito[idx].cantidad > 1) {
                        carrito[idx].cantidad--;
                        renderizarCarrito();
                    }
                });
            });

            // Botones ELIMINAR (la X roja)
            document.querySelectorAll('.btn-eliminar').forEach(btn => {
                btn.addEventListener('click', function() {
                    const idx = this.dataset.index;
                    carrito.splice(idx, 1); // Quitar del arreglo
                    renderizarCarrito();
                });
            });
        }
    });
</script>

<!-- ============================================== -->
<!-- MODAL PARA MOSTRAR QR DE PAGO 📱               -->
<!-- ============================================== -->
<div class="modal fade" id="modalQR" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white pb-2 border-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-qr-code-scan me-2"></i> Escanear para Pagar</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                
                <!-- AQUÍ VA LA IMAGEN REAL DE TU QR -->
                <!-- Cuando tengas tu imagen, ponla en la carpeta public/img/ y cambia la ruta del src -->
                <img src="{{ asset('img/qr_banco.jpeg') }}" alt="QR de Pago" class="img-fluid rounded mb-3 shadow-sm border p-2" style="max-width: 350px;">
                <p class="text-muted small mb-1">Monto a transferir:</p>
                <h2 class="fw-bold text-success mb-2" id="totalModalQR">Bs. 0.00</h2>
                
                <div class="bg-light rounded p-2 mt-3">
                    <p class="text-muted small mb-0"><strong>QR Simple</strong></p>
                    <p class="text-muted small mb-0">A nombre de: <strong>Distribuidora DG</strong></p>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 justify-content-center py-2">
                <button type="button" class="btn btn-outline-secondary btn-sm fw-bold px-4" data-bs-dismiss="modal">Ya realizó el pago</button>
            </div>
        </div>
    </div>
</div>

@endsection