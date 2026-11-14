@extends('layouts.app')

@section('content')

<style>
    /* Estilos del Punto de Venta */
    .pos-card {
        border: none !important;
        border-radius: 14px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important;
        background: #ffffff;
    }
    
    .table-custom-header th {
        background-color: #f8fafc !important;
        font-size: 0.8rem;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 14px 12px;
        border-bottom: 2px solid #e2e8f0 !important;
    }

    .table-custom tbody tr { 
        border-bottom: 1px solid #f1f5f9; 
        transition: all 0.15s ease-in-out; 
    }
    .table-custom tbody tr:hover { 
        background-color: #f8fafc; 
    }
    .table-custom td { 
        vertical-align: middle; 
        padding: 13px 12px; 
    }

    /* Buscador estilizado */
    .search-btn-left {
        background: linear-gradient(135deg, #0284c7, #0ea5e9);
        color: white;
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;
        border: none;
        padding: 0 18px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .search-input {
        border-top-right-radius: 10px;
        border-bottom-right-radius: 10px;
        border: 1px solid #cbd5e1;
        background-color: #f8fafc;
        font-size: 0.95rem;
    }
    .search-input:focus {
        background-color: #ffffff;
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
    }

    /* Inputs ultra legibles para Talonario y Cliente */
    .input-pos-destacado {
        font-size: 1.05rem !important;
        font-weight: 600 !important;
        color: #0f172a !important;
        height: 44px !important;
        border: 2px solid #cbd5e1 !important;
        border-radius: 10px !important;
        background-color: #ffffff !important;
        padding-left: 14px !important;
        transition: all 0.2s;
    }
    .input-pos-destacado:focus {
        border-color: #0284c7 !important;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15) !important;
        background-color: #ffffff !important;
    }
    .label-pos-destacado {
        font-size: 0.92rem !important;
        font-weight: 800 !important;
        color: #1e293b !important;
        margin-bottom: 5px !important;
        display: flex;
        align-items: center;
    }

    /* Botones de método de pago */
    .btn-metodo-pago {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        transition: all 0.15s ease-in-out;
        cursor: pointer;
        padding: 10px 6px;
        background: #ffffff;
    }
    .btn-check:checked + label.metodo-efectivo {
        background-color: #ecfdf5 !important;
        border-color: #10b981 !important;
        color: #047857 !important;
        box-shadow: 0 3px 10px rgba(16, 185, 129, 0.2) !important;
    }
    .btn-check:checked + label.metodo-qr {
        background-color: #f0f9ff !important;
        border-color: #0284c7 !important;
        color: #0369a1 !important;
        box-shadow: 0 3px 10px rgba(2, 132, 199, 0.2) !important;
    }
    .btn-check:checked + label.metodo-credito {
        background-color: #fffbeb !important;
        border-color: #f59e0b !important;
        color: #b45309 !important;
        box-shadow: 0 3px 10px rgba(245, 158, 11, 0.2) !important;
    }

    /* Selector de Tarifa de Precios (Ocasional vs Regular / Ferretería) */
    .btn-tarifa {
        border-radius: 8px !important;
        border: 1px solid transparent;
        color: #64748b;
        background: transparent;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-align: center;
        font-size: 0.82rem;
    }
    .btn-tarifa:hover {
        background: #ffffff;
        color: #0f172a;
    }
    .btn-check:checked + label#lblTarifaOcasional {
        background: #ffffff !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08) !important;
    }
    .btn-check:checked + label#lblTarifaRegular {
        background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%) !important;
        border-color: #0284c7 !important;
        color: #0369a1 !important;
        box-shadow: 0 3px 10px rgba(2, 132, 199, 0.25) !important;
    }

    /* Resaltado de precios según tarifa */
    .box-precio-pos {
        transition: all 0.2s ease;
    }
    .tarifa-modo-regular .linea-precio-cliente {
        font-size: 0.94rem !important;
        font-weight: 800 !important;
    }
    .tarifa-modo-regular .linea-precio-cliente .val-precio-cliente {
        font-size: 0.98rem !important;
        color: #0284c7 !important;
    }
    .tarifa-modo-regular .linea-precio-venta {
        opacity: 0.6;
    }

    /* Badge producto */
    .badge-code {
        font-family: monospace;
        font-weight: 700;
        letter-spacing: 0.5px;
        padding: 5px 9px;
        border-radius: 6px;
        font-size: 0.82rem;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
    }

    /* Total destacado */
    .box-total-pos {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 2px dashed #86efac;
        border-radius: 12px;
        padding: 12px 18px;
    }

    /* Botón cobrar imponente */
    .btn-cobrar-pos {
        background: linear-gradient(135deg, #059669, #10b981);
        color: white;
        font-size: 1.15rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        border: none;
        border-radius: 12px;
        padding: 14px;
        box-shadow: 0 5px 18px rgba(16, 185, 129, 0.35);
        transition: all 0.2s;
    }
    .btn-cobrar-pos:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 7px 22px rgba(16, 185, 129, 0.45);
        color: white;
    }
    .btn-cobrar-pos:disabled {
        background: #cbd5e1;
        box-shadow: none;
        cursor: not-allowed;
    }

    /* Paginación SaaS moderna para POS */
    .pos-pagination {
        display: flex;
        align-items: center;
        gap: 4px;
        margin: 0;
        list-style: none;
        padding-left: 0;
    }
    .pos-pagination .page-item .page-link {
        border: 1px solid #e2e8f0;
        border-radius: 8px !important;
        color: #475569;
        font-size: 0.82rem;
        font-weight: 600;
        padding: 5px 10px;
        background: #ffffff;
        text-decoration: none;
        transition: all 0.15s ease-in-out;
    }
    .pos-pagination .page-item .page-link:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    .pos-pagination .page-item.active .page-link {
        background: #0284c7 !important;
        border-color: #0284c7 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(2, 132, 199, 0.35);
    }
    .pos-pagination .page-item.disabled .page-link {
        background: #f8fafc;
        border-color: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
    }

    /* Input de cantidad editable en el carrito */
    .input-cantidad-item {
        transition: all 0.15s ease-in-out;
        border-color: #cbd5e1;
    }
    .input-cantidad-item:focus {
        border-color: #0284c7 !important;
        box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.2) !important;
        background-color: #ffffff !important;
    }
    .input-cantidad-item::-webkit-outer-spin-button,
    .input-cantidad-item::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .input-cantidad-item {
        -moz-appearance: textfield;
    }
</style>

<!-- ALERTAS DE ÉXITO O ERROR -->
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3 py-2" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-2 text-danger"></i>
            <div><strong>Atención:</strong> {{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('success_venta_id'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm d-flex justify-content-between align-items-center mb-3 py-2" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-4 text-success me-2"></i>
            <div>
                <h6 class="fw-bold mb-0 text-success">{{ session('success') ?? '¡Venta registrada con éxito!' }}</h6>
                <small class="text-muted">Nota de Venta ID: #{{ session('success_venta_id') }}</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="/ventas/{{ session('success_venta_id') }}/pdf" target="_blank" class="btn btn-sm btn-success fw-bold shadow-sm px-3">
                <i class="bi bi-printer-fill me-1"></i> Imprimir Nota
            </a>
            <button type="button" class="btn-close position-relative top-0 end-0" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif

<!-- ENCABEZADO PRINCIPAL (ESTILO UNIFICADO DEL SISTEMA) -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="d-flex align-items-center">
        <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3 text-primary">
            <i class="bi bi-cart4 fs-2"></i>
        </div>
        <div>
            <h3 class="fw-bold m-0 text-dark">Punto de Venta (POS)</h3>
            <small class="text-muted">Caja registradora rápida • Selección de productos y cobro</small>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-white text-dark border px-3 py-2 rounded-3 shadow-sm d-flex align-items-center" style="font-size: 0.85rem;">
            <i class="bi bi-person-badge-fill text-primary me-2 fs-6"></i> Cajero:&nbsp;<strong class="text-primary">{{ optional(auth()->user())->name ?? 'Usuario' }}</strong>
        </span>
    </div>
</div>

<div class="row g-4">
    <!-- COLUMNA IZQUIERDA: Catálogo de Productos Disponibles -->
    <div class="col-lg-7 col-xl-8">
        <div class="pos-card h-100 d-flex flex-column">
            <!-- Barra superior con buscador y categorías -->
            <div class="p-3 border-bottom bg-light bg-opacity-50">
                <div class="row g-2 align-items-center">
                    <div class="col-md-7">
                        <div class="input-group" style="height: 44px;">
                            <span class="search-btn-left"><i class="bi bi-search"></i></span>
                            <input type="text" id="buscadorProductos" class="form-control search-input" placeholder="Buscar por producto, código o marca..." autofocus>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <select id="filtroCategoria" class="form-select border-1" style="height: 44px; font-size: 0.95rem;">
                            <option value="">Todas las categorías</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabla de productos -->
            <div class="table-responsive flex-grow-1" style="min-height: 420px;">
                <table class="table table-custom align-middle m-0" id="tablaProductos">
                    <thead class="table-custom-header">
                        <tr>
                            <th class="ps-4" style="width: 17%;">CÓDIGO</th>
                            <th style="width: 41%;">PRODUCTO</th>
                            <th class="text-center" style="width: 13%;">STOCK</th>
                            <th class="text-end" style="width: 17%;">PRECIOS</th>
                            <th class="text-center pe-4" style="width: 12%;">ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productos as $producto)
                        @php
                            $pCliente = ($producto->precio_cliente !== null && $producto->precio_cliente > 0) ? floatval($producto->precio_cliente) : floatval($producto->precio_venta);
                            $tienePCliente = ($producto->precio_cliente !== null && $producto->precio_cliente > 0);
                        @endphp
                        <tr class="fila-producto" 
                            data-id="{{ $producto->id }}" 
                            data-codigo="{{ $producto->codigo }}"
                            data-nombre="{{ $producto->nombre }}"
                            data-precio-venta="{{ $producto->precio_venta }}"
                            data-precio-cliente="{{ $pCliente }}"
                            data-tiene-precio-cliente="{{ $tienePCliente ? '1' : '0' }}"
                            data-precio="{{ $producto->precio_venta }}"
                            data-stock="{{ $producto->stock }}"
                            data-categoria="{{ $producto->id_categoria }}">
                            
                            <td class="ps-4">
                                <span class="badge-code">{{ $producto->codigo }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark" style="font-size: 0.98rem;">{{ $producto->nombre }}</div>
                                <div class="text-muted small">
                                    <span class="me-2"><i class="bi bi-tag me-1"></i>{{ optional($producto->marca)->nombre ?? 'Genérico' }}</span>
                                    @if($producto->categoria)
                                        <span class="badge bg-light text-muted border">{{ $producto->categoria->nombre }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                @php
                                    $stk = $producto->stock ?? 0;
                                    $minStk = is_numeric($producto->stock_minimo) ? intval($producto->stock_minimo) : 5;
                                    $umbralBajo = max($minStk * 2, $minStk + 5);
                                @endphp
                                @if($stk <= 0)
                                    <span class="badge bg-danger text-white border px-2 py-1 fs-6" title="Agotado">
                                        0
                                    </span>
                                @elseif($stk <= $minStk)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-6 fw-bold" title="Stock crítico (Mín: {{ $minStk }})">
                                        {{ $stk }}
                                    </span>
                                @elseif($stk <= $umbralBajo)
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fs-6 fw-bold" title="Por agotarse (Mín: {{ $minStk }})">
                                        {{ $stk }}
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-6 fw-bold" title="Stock óptimo">
                                        {{ $stk }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="box-precio-pos">
                                    <!-- Tarifa Venta Ocasional -->
                                    <div class="linea-precio-venta d-flex align-items-center justify-content-end gap-1">
                                        <span class="fw-bold text-dark val-precio-venta" style="font-size: 0.94rem;">
                                            Bs. {{ number_format($producto->precio_venta, 2) }}
                                        </span>
                                        <span class="badge bg-light text-secondary border py-0 px-1" style="font-size: 0.64rem;">Venta</span>
                                    </div>
                                    <!-- Tarifa Cliente Regular / Ferretería -->
                                    @if($tienePCliente)
                                        <div class="linea-precio-cliente d-flex align-items-center justify-content-end gap-1 mt-0.5" style="color: #0284c7;">
                                            <i class="bi bi-tag-fill" style="font-size: 0.65rem;"></i>
                                            <span class="fw-bold val-precio-cliente" style="font-size: 0.88rem;">
                                                Bs. {{ number_format($pCliente, 2) }}
                                            </span>
                                            <span class="badge py-0 px-1" style="font-size: 0.64rem; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;">Cliente</span>
                                        </div>
                                    @else
                                        <div class="linea-precio-cliente text-muted mt-0.5" style="font-size: 0.68rem;" title="Sin precio especial: aplica el mismo precio de venta">
                                            <span>Cliente: <em>Mismo</em></span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center pe-4">
                                <button type="button" class="btn btn-sm btn-primary fw-bold px-3 py-1.5 rounded-pill btn-agregar-producto shadow-sm">
                                    <i class="bi bi-plus-lg me-1"></i> Agregar
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-box-seam fs-1 d-block mb-2 text-muted"></i>
                                <h6>No hay productos con stock disponible en el sistema.</h6>
                                <p class="small text-muted mb-0">Agrega stock desde el módulo de Inventario o Productos.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer con paginación integrada y contador -->
            <div class="p-2.5 px-3 bg-light border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="small text-muted d-flex align-items-center flex-wrap gap-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1 font-monospace" style="font-size: 0.78rem;">
                        Pág. <span id="txtPaginaActual">1</span> de <span id="txtTotalPaginas">1</span>
                    </span>
                    <span id="txtInfoPaginacion" class="fw-semibold text-secondary">Cargando catálogo...</span>
                </div>
                <nav aria-label="Paginación de productos">
                    <ul class="pos-pagination" id="paginadorProductos">
                        <!-- JS inserta los botones aquí -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- COLUMNA DERECHA: Resumen de Venta / Carrito y Cobro -->
    <div class="col-lg-5 col-xl-4">
        <div class="pos-card h-100 d-flex flex-column border-top border-primary border-4">
            
            <div class="p-2.5 px-3 border-bottom d-flex justify-content-between align-items-center bg-white">
                <div class="d-flex align-items-center">
                    <i class="bi bi-receipt-cutoff fs-5 text-primary me-2"></i>
                    <h6 class="fw-bold m-0 text-dark">Detalle de Cobro</h6>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger border-0 py-0 px-2" id="btnVaciarCarrito" title="Vaciar carrito" style="display: none; font-size: 0.78rem;">
                    <i class="bi bi-trash3 me-1"></i> Vaciar
                </button>
            </div>
            
            <!-- FORMULARIO DE COBRO -->
            <form id="formVenta" action="/pos/cobrar" method="POST" class="d-flex flex-column flex-grow-1 m-0">
                @csrf
                
                <!-- Área de productos en el carrito -->
                <div class="flex-grow-1 p-2 bg-light" style="min-height: 220px; max-height: 300px; overflow-y: auto;" id="contenedorCarrito">
                    <div class="text-center text-muted py-4" id="mensajeCarritoVacio">
                        <i class="bi bi-cart-x fs-2 text-secondary opacity-50 d-block mb-1"></i>
                        <span class="fw-bold text-secondary small">El carrito está vacío</span>
                        <small class="d-block text-muted" style="font-size: 0.75rem;">Haz clic en los productos para agregarlos</small>
                    </div>
                    
                    <ul class="list-group list-group-flush" id="listaCarrito">
                        <!-- JS inserta los ítems aquí -->
                    </ul>
                </div>

                <!-- Totales y Datos de la Venta -->
                <div class="p-3.5 bg-white border-top">
                    
                    <!-- Caja de Total a Pagar -->
                    <div class="box-total-pos mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1 text-muted small fw-bold">
                            <span>SUBTOTAL:</span>
                            <span id="txtSubtotal">Bs. 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fs-5 fw-bold text-dark">TOTAL A PAGAR:</span>
                            <span class="fs-3 fw-bold text-success" id="txtTotal">Bs. 0.00</span>
                        </div>
                    </div>
                    
                    <!-- Inputs ocultos para enviar los productos -->
                    <input type="hidden" name="total_venta" id="inputTotalVenta" value="0">
                    <div id="inputsOcultosCarrito"></div>

                    <!-- CAMPO 1: Nº DE TALONARIO / NOTA FÍSICA -->
                    <div class="mb-3 text-start">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="label-pos-destacado m-0 d-flex align-items-center" for="inputNumeroNota">
                                <i class="bi bi-receipt text-primary me-2 fs-5"></i>
                                <span>Nº de Talonario</span>
                                <span class="badge bg-light text-secondary border ms-2 fw-normal" style="font-size: 0.7rem; padding: 2px 6px;">Opcional</span>
                            </label>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle font-monospace" style="font-size: 0.72rem; padding: 2px 7px;">
                                Auto: SYS-XXXXXX
                            </span>
                        </div>
                        <input type="text" 
                               name="numero_nota" 
                               id="inputNumeroNota"
                               class="form-control input-pos-destacado" 
                               placeholder="Ej. 00154 (dejar vacío para número automático)">
                        <small class="text-muted d-block mt-1" style="font-size: 0.74rem;">
                            <i class="bi bi-info-circle me-1"></i>Si emites talonario manual escribe el número aquí; si está vacío se genera automáticamente.
                        </small>
                    </div>

                    <!-- CAMPO 2: SELECTOR DE TARIFA (OCASIONAL VS REGULAR / FERRETERÍA) -->
                    <div class="mb-3 text-start">
                        <div class="d-flex justify-content-between align-items-center mb-1.5">
                            <label class="label-pos-destacado m-0 d-flex align-items-center gap-1.5">
                                <i class="bi bi-tags-fill text-primary fs-5"></i>
                                <span>Lista de Precios</span>
                            </label>
                            <span id="badgeTarifaActiva" class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle" style="font-size: 0.72rem;">
                                Tarifa Venta Ocasional
                            </span>
                        </div>
                        <div class="btn-group w-100 p-1 rounded-3 bg-light border shadow-sm" role="group">
                            <input type="radio" class="btn-check" name="tipo_tarifa" id="tarifa_ocasional" value="Ocasional" checked autocomplete="off">
                            <label class="btn btn-tarifa py-2 w-50 fw-bold" for="tarifa_ocasional" id="lblTarifaOcasional">
                                <i class="bi bi-person me-1"></i> Cliente Ocasional
                                <small class="d-block text-muted fw-normal" style="font-size: 0.70rem;">Precio Venta Normal</small>
                            </label>

                            <input type="radio" class="btn-check" name="tipo_tarifa" id="tarifa_regular" value="Regular" autocomplete="off">
                            <label class="btn btn-tarifa py-2 w-50 fw-bold" for="tarifa_regular" id="lblTarifaRegular">
                                <i class="bi bi-building-check me-1"></i> Cliente Regular
                                <small class="d-block text-muted fw-normal" style="font-size: 0.70rem;">Precio Ferretería / Mayorista</small>
                            </label>
                        </div>
                    </div>

                    <!-- CAMPO 3: NOMBRE DEL CLIENTE (CON BUSCADOR DE REGULARES) -->
                    <div class="mb-3 text-start position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="label-pos-destacado m-0">
                                <i class="bi bi-person-fill text-primary me-2 fs-5"></i>
                                Nombre del Cliente *
                            </label>
                            @if(count($clientes) > 0)
                            <div class="dropdown position-relative">
                                <button type="button" class="btn btn-primary fw-bold shadow-sm d-inline-flex align-items-center gap-1.5 px-3 py-1.5" 
                                        id="btnDropdownClientesRegulares"
                                        aria-expanded="false" style="font-size: 0.82rem; border-radius: 8px;"
                                        title="Buscar y seleccionar un cliente regular registrado">
                                    <i class="bi bi-person-check-fill fs-6"></i> 
                                    <span>Clientes Regulares</span> 
                                    <i class="bi bi-chevron-down ms-1" style="font-size:0.7rem;"></i>
                                </button>
                                <div class="dropdown-menu shadow-lg p-2 rounded-3 border" 
                                     id="menuDropdownClientesRegulares"
                                     style="position: absolute !important; right: 0 !important; left: auto !important; top: 100% !important; min-width: 290px; max-width: 330px; width: 310px; max-height: 290px; overflow-y: auto; z-index: 1060;">
                                    <div class="px-2 py-1 mb-2 border-bottom">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                                            <input type="text" id="buscadorRapidoClientesDrop" class="form-control border-start-0" placeholder="Buscar por nombre, teléfono o NIT...">
                                        </div>
                                    </div>
                                    <div id="listaDropClientes">
                                        @foreach($clientes as $cli)
                                            <a href="javascript:void(0)" class="dropdown-item p-2 rounded-2 item-cliente-drop mb-1"
                                               onclick="seleccionarClienteRegular({{ $cli->id }}, '{{ addslashes($cli->nombre) }}', '{{ $cli->telefono ?? '' }}')">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="fw-bold text-dark" style="font-size: 0.88rem;">{{ $cli->nombre }}</div>
                                                    @if(($cli->saldo_pendiente ?? 0) > 0)
                                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle" style="font-size: 0.68rem;">
                                                            Debe Bs. {{ number_format($cli->saldo_pendiente, 2) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="small text-muted" style="font-size: 0.75rem;">
                                                    @if($cli->telefono)
                                                        <i class="bi bi-telephone text-success me-1"></i>{{ $cli->telefono }}
                                                    @endif
                                                    @if($cli->ci_nit)
                                                        <span class="ms-1">· NIT/CI: {{ $cli->ci_nit }}</span>
                                                    @endif
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <input type="hidden" name="id_cliente" id="inputIdCliente" value="">
                        
                        <!-- Input de Cliente con Sugerencias Flotantes -->
                        <div class="position-relative">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted px-3" style="height: 44px;"><i class="bi bi-person fs-5"></i></span>
                                <input type="text" 
                                       name="cliente" 
                                       id="inputCliente" 
                                       class="form-control input-pos-destacado border-start-0" 
                                       placeholder="Ingresa nombre del cliente..." 
                                       autocomplete="off"
                                       required>
                            </div>
                            <!-- Lista flotante de autocompletado en tiempo real -->
                            <div id="sugerenciasClientesPos" class="list-group shadow-lg position-absolute w-100 rounded-3 border" 
                                 style="display: none; top: 100%; left: 0; z-index: 1070; max-height: 200px; overflow-y: auto; background: #ffffff;">
                            </div>
                        </div>

                        <!-- Chip cuando un cliente regular está seleccionado -->
                        <div id="badgeClienteSeleccionado" class="mt-2 p-2 rounded-3 bg-primary bg-opacity-10 border border-primary-subtle d-flex justify-content-between align-items-center" style="display: none !important;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-patch-check-fill text-primary fs-5"></i>
                                <div>
                                    <span class="fw-bold text-primary small" id="txtNombreClienteRegular">—</span>
                                    <small class="text-muted ms-1" id="txtDetalleClienteRegular">· Cliente Regular</small>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-link text-danger p-0 text-decoration-none fw-bold" onclick="desvincularClienteRegular()" title="Desvincular">
                                <i class="bi bi-x-circle-fill fs-5"></i>
                            </button>
                        </div>
                    </div>

                    <!-- CAMPO 3: MÉTODO DE PAGO (EFECTIVO / QR / POR PAGAR) -->
                    <div class="mb-3 text-start">
                        <div class="d-flex justify-content-between align-items-center mb-1.5">
                            <label class="label-pos-destacado m-0">
                                <i class="bi bi-credit-card-2-front-fill text-primary me-2 fs-5"></i>
                                Método de Pago *
                            </label>
                            <button type="button" onclick="abrirModalQRManual()" class="btn btn-sm btn-outline-primary py-0 px-2.5 rounded-pill fw-semibold border-1 shadow-none" style="font-size: 0.75rem;" title="Ver código QR de pago">
                                <i class="bi bi-qr-code me-1"></i>Ver QR
                            </button>
                        </div>
                        <div class="row g-2">
                            <!-- Efectivo -->
                            <div class="col-4 position-relative">
                                <input type="radio" class="btn-check" name="metodo_pago" id="pago_efectivo" value="Efectivo" checked>
                                <label class="btn btn-metodo-pago metodo-efectivo w-100 d-flex flex-column align-items-center" for="pago_efectivo">
                                    <i class="bi bi-cash-coin fs-4 mb-1"></i>
                                    <span style="font-size: 0.85rem; font-weight: 700;">Efectivo</span>
                                </label>
                            </div>

                            <!-- QR -->
                            <div class="col-4 position-relative">
                                <input type="radio" class="btn-check" name="metodo_pago" id="pago_qr" value="QR">
                                <label class="btn btn-metodo-pago metodo-qr w-100 d-flex flex-column align-items-center" for="pago_qr">
                                    <i class="bi bi-qr-code-scan fs-4 mb-1"></i>
                                    <span style="font-size: 0.85rem; font-weight: 700;">QR Simple</span>
                                </label>
                            </div>

                            <!-- Por Pagar (Crédito) -->
                            <div class="col-4 position-relative">
                                <input type="radio" class="btn-check" name="metodo_pago" id="pago_por_pagar" value="Por Pagar">
                                <label class="btn btn-metodo-pago metodo-credito w-100 d-flex flex-column align-items-center" for="pago_por_pagar">
                                    <i class="bi bi-clock-history fs-4 mb-1"></i>
                                    <span style="font-size: 0.85rem; font-weight: 700;">Por Pagar</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- RECUADRO ESPECIAL: DETALLES DE "POR PAGAR" -->
                    <div id="panelPorPagar" class="p-3 rounded-3 mb-3 border border-warning bg-warning bg-opacity-10 text-start" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-1.5">
                            <span class="small fw-bold text-dark">
                                <i class="bi bi-clock-history text-warning me-1"></i> Venta a Crédito / Por Pagar
                            </span>
                            <span class="badge bg-warning text-dark border border-warning-subtle fw-bold" style="font-size: 0.72rem;">Adelanto Opcional</span>
                        </div>
                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-7">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white fw-bold text-muted">Adelanto Bs.</span>
                                    <input type="number" step="0.5" min="0" name="monto_adelanto" id="inputMontoAdelanto" class="form-control form-control-sm fw-bold text-primary" value="0.00" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-5">
                                <select name="metodo_adelanto" id="selectMetodoAdelanto" class="form-select form-select-sm fw-bold">
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="QR">QR</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-1.5 border-top border-warning-subtle">
                            <span class="text-secondary small fw-bold">SALDO PENDIENTE:</span>
                            <span class="fw-bold text-danger fs-5" id="txtSaldoPendiente">Bs. 0.00</span>
                        </div>
                    </div>

                    <!-- BOTÓN COBRAR VENTA -->
                    <button type="submit" class="btn btn-cobrar-pos w-100" id="btnCobrar" disabled>
                        <i class="bi bi-check2-circle me-2"></i> COBRAR VENTA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- POPUP MODAL QR                                -->
<!-- ============================================== -->
<div class="modal fade" id="modalQR" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white border-0 py-2.5 px-3">
                <h6 class="modal-title fw-bold m-0"><i class="bi bi-qr-code-scan me-2"></i> Cobro por Código QR</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-3">
                <img src="{{ asset('img/qr_banco.jpeg') }}" 
                     alt="QR de Pago" 
                     class="img-fluid rounded-3 mb-2 shadow-sm border p-2 bg-white" 
                     style="max-width: 220px;"
                     onerror="this.src='https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=SISTEMA_DG_PAGO'">
                <p class="text-muted small mb-0 fw-bold text-uppercase" style="font-size: 0.75rem;">Monto a transferir:</p>
                <h3 class="fw-bold text-success mb-2" id="totalModalQR">Bs. 0.00</h3>
                <div class="bg-light rounded-3 p-2 text-start small border">
                    <div><strong>Beneficiario:</strong> Distribuidora Guadalupe</div>
                    <div class="text-muted" style="font-size: 0.75rem;">Transferencia QR Simple Inmediata</div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 justify-content-center py-2 px-3">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success fw-bold px-4 rounded-pill shadow-sm" id="btnConfirmarPagoQR">
                    <i class="bi bi-check-lg me-1"></i> Confirmar Pago y Cobrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JAVASCRIPT DEL CARRITO Y COBRO INTELIGENTE -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let carrito = [];

    // Elementos DOM
    const listaCarrito = document.getElementById('listaCarrito');
    const mensajeVacio = document.getElementById('mensajeCarritoVacio');
    const txtSubtotal = document.getElementById('txtSubtotal');
    const txtTotal = document.getElementById('txtTotal');
    const inputTotalVenta = document.getElementById('inputTotalVenta');
    const inputCliente = document.getElementById('inputCliente');
    const inputsOcultos = document.getElementById('inputsOcultosCarrito');
    const btnCobrar = document.getElementById('btnCobrar');
    const btnVaciar = document.getElementById('btnVaciarCarrito');
    const buscador = document.getElementById('buscadorProductos');
    const filtroCat = document.getElementById('filtroCategoria');
    const filasProductos = document.querySelectorAll('.fila-producto');
    const contadorVisibles = document.getElementById('contadorProductosVisibles');
    
    // Elementos Por Pagar
    const radiosPago = document.querySelectorAll('input[name="metodo_pago"]');
    const panelPorPagar = document.getElementById('panelPorPagar');
    const inputMontoAdelanto = document.getElementById('inputMontoAdelanto');
    const txtSaldoPendiente = document.getElementById('txtSaldoPendiente');

    // Modal QR
    const modalQREl = document.getElementById('modalQR');
    const modalQR = modalQREl ? new bootstrap.Modal(modalQREl) : null;
    const totalModalQR = document.getElementById('totalModalQR');
    const MOSTRAR_MODAL_QR = {{ \App\Models\Configuracion::get('mostrar_modal_qr', '1') == '1' ? 'true' : 'false' }};

    // ==========================================
    // PAGINACIÓN Y FILTRADO INTELIGENTE
    // ==========================================
    const ITEMS_POR_PAGINA = 10;
    let paginaActual = 1;
    let productosFiltrados = Array.from(filasProductos);

    function actualizarPaginacion() {
        const totalItems = productosFiltrados.length;
        const totalPaginas = Math.ceil(totalItems / ITEMS_POR_PAGINA) || 1;

        if (paginaActual > totalPaginas) paginaActual = totalPaginas;
        if (paginaActual < 1) paginaActual = 1;

        const inicio = (paginaActual - 1) * ITEMS_POR_PAGINA;
        const fin = inicio + ITEMS_POR_PAGINA;

        // Ocultar todas las filas
        filasProductos.forEach(f => f.style.display = 'none');

        // Mostrar solo las de la página actual
        productosFiltrados.slice(inicio, fin).forEach(f => {
            f.style.display = '';
        });

        // Actualizar badges e info
        const txtPagAct = document.getElementById('txtPaginaActual');
        const txtTotPag = document.getElementById('txtTotalPaginas');
        if (txtPagAct) txtPagAct.textContent = paginaActual;
        if (txtTotPag) txtTotPag.textContent = totalPaginas;

        const txtInfo = document.getElementById('txtInfoPaginacion');
        if (txtInfo) {
            if (totalItems > 0) {
                txtInfo.innerHTML = `Mostrando <strong>${inicio + 1} - ${Math.min(fin, totalItems)}</strong> de <strong>${totalItems}</strong> productos`;
            } else {
                txtInfo.innerHTML = `No se encontraron productos coincidentes`;
            }
        }

        renderizarBotonesPaginador(totalPaginas);
    }

    function renderizarBotonesPaginador(totalPaginas) {
        const contenedor = document.getElementById('paginadorProductos');
        if (!contenedor) return;

        if (totalPaginas <= 1) {
            contenedor.innerHTML = '';
            return;
        }

        let html = '';

        // Botón Anterior
        html += `<li class="page-item ${paginaActual === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="cambiarPagina(${paginaActual - 1})" title="Página Anterior">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                 </li>`;

        // Rango de páginas (máximo 5 visibles)
        let inicioP = Math.max(1, paginaActual - 2);
        let finP = Math.min(totalPaginas, inicioP + 4);
        if (finP - inicioP < 4) {
            inicioP = Math.max(1, finP - 4);
        }

        if (inicioP > 1) {
            html += `<li class="page-item">
                        <a class="page-link" href="javascript:void(0)" onclick="cambiarPagina(1)">1</a>
                     </li>`;
            if (inicioP > 2) {
                html += `<li class="page-item disabled"><span class="page-link border-0 bg-transparent text-muted px-1">...</span></li>`;
            }
        }

        for (let p = inicioP; p <= finP; p++) {
            html += `<li class="page-item ${p === paginaActual ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0)" onclick="cambiarPagina(${p})">${p}</a>
                     </li>`;
        }

        if (finP < totalPaginas) {
            if (finP < totalPaginas - 1) {
                html += `<li class="page-item disabled"><span class="page-link border-0 bg-transparent text-muted px-1">...</span></li>`;
            }
            html += `<li class="page-item">
                        <a class="page-link" href="javascript:void(0)" onclick="cambiarPagina(${totalPaginas})">${totalPaginas}</a>
                     </li>`;
        }

        // Botón Siguiente
        html += `<li class="page-item ${paginaActual === totalPaginas ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="cambiarPagina(${paginaActual + 1})" title="Página Siguiente">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                 </li>`;

        contenedor.innerHTML = html;
    }

    window.cambiarPagina = function(p) {
        paginaActual = p;
        actualizarPaginacion();
    };

    // 1. Filtrado combinado (Buscador + Categoría) con recálculo de paginación
    function filtrarProductos() {
        const texto = buscador ? buscador.value.toLowerCase().trim() : '';
        const cat = filtroCat ? filtroCat.value : '';

        productosFiltrados = Array.from(filasProductos).filter(fila => {
            const nombre = (fila.dataset.nombre || '').toLowerCase();
            const codigo = (fila.dataset.codigo || '').toLowerCase();
            const categoriaFila = fila.dataset.categoria || '';

            const coincideTexto = (nombre.includes(texto) || codigo.includes(texto));
            const coincideCat = (cat === '' || categoriaFila === cat);

            return coincideTexto && coincideCat;
        });

        paginaActual = 1;
        actualizarPaginacion();
    }

    if (buscador) buscador.addEventListener('input', filtrarProductos);
    if (filtroCat) filtroCat.addEventListener('change', filtrarProductos);

    // Inicializar primera página al cargar
    filtrarProductos();

    // 2. Control de Métodos de Pago (Efectivo / QR / Por Pagar)
    radiosPago.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'Por Pagar') {
                panelPorPagar.style.display = 'block';
                actualizarSaldoPendiente();
            } else {
                panelPorPagar.style.display = 'none';
            }

            // Si se selecciona QR y el switch en configuración está activo
            if (this.value === 'QR') {
                if (MOSTRAR_MODAL_QR) {
                    abrirModalQRManual();
                }
            }

            validarBotonCobrar();
        });
    });

    // Abrir Modal QR también si se hace clic en el botón/label de QR
    const labelQR = document.querySelector('label[for="pago_qr"]');
    if (labelQR) {
        labelQR.addEventListener('click', function() {
            if (MOSTRAR_MODAL_QR) {
                setTimeout(abrirModalQRManual, 50);
            }
        });
    }

    // Abrir Modal QR manualmente a demanda
    window.abrirModalQRManual = function() {
        if (totalModalQR && txtTotal) {
            totalModalQR.textContent = txtTotal.textContent;
        }
        const modalEl = document.getElementById('modalQR');
        if (modalEl && typeof bootstrap !== 'undefined') {
            const m = bootstrap.Modal.getOrCreateInstance(modalEl);
            m.show();
        }
    };

    // Flujo para botón de confirmación dentro del Modal QR
    const formVenta = document.getElementById('formVenta');
    const btnConfirmarPagoQR = document.getElementById('btnConfirmarPagoQR');
    if (btnConfirmarPagoQR && formVenta) {
        btnConfirmarPagoQR.addEventListener('click', function() {
            const modalEl = document.getElementById('modalQR');
            if (modalEl && typeof bootstrap !== 'undefined') {
                const m = bootstrap.Modal.getOrCreateInstance(modalEl);
                m.hide();
            }
            formVenta.submit();
        });
    }

    if (formVenta) {
        formVenta.addEventListener('submit', function(e) {
            if (carrito.length === 0) {
                e.preventDefault();
                alert('El carrito está vacío. Agrega al menos un producto antes de cobrar.');
                return;
            }
        });
    }

    function actualizarSaldoPendiente() {
        const total = parseFloat(inputTotalVenta.value) || 0;
        let adelanto = parseFloat(inputMontoAdelanto.value) || 0;
        if (adelanto < 0) adelanto = 0;
        if (adelanto > total) {
            adelanto = total;
            inputMontoAdelanto.value = total.toFixed(2);
        }
        const saldo = Math.max(0, total - adelanto);
        if (txtSaldoPendiente) {
            txtSaldoPendiente.textContent = `Bs. ${saldo.toFixed(2)}`;
        }
    }

    if (inputMontoAdelanto) {
        inputMontoAdelanto.addEventListener('input', actualizarSaldoPendiente);
    }

    // Control de Tarifa (Ocasional vs Regular / Ferretería)
    let tarifaActiva = 'Ocasional';
    const radiosTarifa = document.querySelectorAll('input[name="tipo_tarifa"]');
    const badgeTarifa = document.getElementById('badgeTarifaActiva');
    const tablaProductosCont = document.getElementById('tablaProductos');

    window.cambiarTarifa = function(nuevaTarifa, forzarRecalculo = true) {
        tarifaActiva = nuevaTarifa;
        const radioTarget = document.getElementById(nuevaTarifa === 'Regular' ? 'tarifa_regular' : 'tarifa_ocasional');
        if (radioTarget) radioTarget.checked = true;

        if (badgeTarifa) {
            if (nuevaTarifa === 'Regular') {
                badgeTarifa.className = 'badge bg-primary bg-opacity-10 text-primary border border-primary-subtle';
                badgeTarifa.innerHTML = '<i class="bi bi-tag-fill me-1"></i>Tarifa Cliente Regular Activa';
            } else {
                badgeTarifa.className = 'badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle';
                badgeTarifa.innerHTML = 'Tarifa Venta Ocasional';
            }
        }

        // Resaltado visual en tabla de productos
        if (tablaProductosCont) {
            if (nuevaTarifa === 'Regular') {
                tablaProductosCont.classList.add('tarifa-modo-regular');
            } else {
                tablaProductosCont.classList.remove('tarifa-modo-regular');
            }
        }

        // Recalcular carrito si ya tiene productos
        if (forzarRecalculo && carrito.length > 0) {
            carrito.forEach(item => {
                if (nuevaTarifa === 'Regular') {
                    item.precio = item.precioCliente;
                    item.esTarifaCliente = true;
                } else {
                    item.precio = item.precioVenta;
                    item.esTarifaCliente = false;
                }
            });
            renderizarCarrito();
        }
    };

    radiosTarifa.forEach(r => {
        r.addEventListener('change', function() {
            cambiarTarifa(this.value, true);
        });
    });

    // 3. Agregar productos al carrito
    document.querySelectorAll('.btn-agregar-producto').forEach(boton => {
        boton.addEventListener('click', function() {
            const fila = this.closest('tr');
            const id = fila.dataset.id;
            const codigo = fila.dataset.codigo;
            const nombre = fila.dataset.nombre;
            const precioVenta = parseFloat(fila.dataset.precioVenta) || 0;
            const precioCliente = parseFloat(fila.dataset.precioCliente) || precioVenta;
            const tienePrecioCliente = fila.dataset.tienePrecioCliente === '1';
            const stockMaximo = parseInt(fila.dataset.stock) || 0;

            const esCliente = (tarifaActiva === 'Regular');
            const precioAUsar = esCliente ? precioCliente : precioVenta;

            const existente = carrito.find(item => item.id === id);

            if (existente) {
                if (existente.cantidad < stockMaximo) {
                    existente.cantidad++;
                } else {
                    alert(`¡Atención! Stock máximo alcanzado (${stockMaximo} unidades).`);
                }
            } else {
                carrito.push({
                    id: id,
                    codigo: codigo,
                    nombre: nombre,
                    precioVenta: precioVenta,
                    precioCliente: precioCliente,
                    tienePrecioCliente: tienePrecioCliente,
                    precio: precioAUsar,
                    esTarifaCliente: esCliente,
                    cantidad: 1,
                    stockMaximo: stockMaximo
                });
            }

            renderizarCarrito();

            // Efecto visual de pulsación
            this.classList.add('btn-success');
            setTimeout(() => this.classList.remove('btn-success'), 300);
        });
    });

    
    // Lógica para Clientes Regulares y Búsqueda Rápida
    const inputIdCliente = document.getElementById("inputIdCliente");
    const badgeCliente = document.getElementById("badgeClienteSeleccionado");
    const txtNomReg = document.getElementById("txtNombreClienteRegular");
    const txtDetReg = document.getElementById("txtDetalleClienteRegular");
    const btnDropCli = document.getElementById("btnDropdownClientesRegulares");
    const menuDropCli = document.getElementById("menuDropdownClientesRegulares");
    const cajaSugerencias = document.getElementById("sugerenciasClientesPos");

    // Catálogo en memoria para búsqueda instantánea
    @php
        $clientesListaJs = $clientes->map(function($c) {
            return [
                'id' => $c->id,
                'nombre' => $c->nombre,
                'telefono' => $c->telefono,
                'ci_nit' => $c->ci_nit,
                'saldo_pendiente' => floatval($c->saldo_pendiente ?? 0),
            ];
        })->values();
    @endphp
    const clientesCatalogo = @json($clientesListaJs);

    window.seleccionarClienteRegular = function(id, nombre, tel) {
        if (inputCliente) inputCliente.value = nombre;
        if (inputIdCliente) inputIdCliente.value = id;
        if (txtNomReg) txtNomReg.textContent = nombre;
        if (txtDetReg) txtDetReg.textContent = tel ? `Cliente Regular · Tel: ${tel}` : 'Cliente Regular Registrado';
        if (badgeCliente) badgeCliente.style.setProperty('display', 'flex', 'important');
        
        // Al seleccionar un cliente regular registrado, se activa automáticamente su tarifa
        cambiarTarifa('Regular', true);

        // Cerrar dropdown y caja de sugerencias
        if (menuDropCli) menuDropCli.classList.remove("show");
        if (btnDropCli) {
            btnDropCli.classList.remove("show");
            btnDropCli.setAttribute("aria-expanded", "false");
        }
        if (cajaSugerencias) cajaSugerencias.style.display = "none";
        
        validarBotonCobrar();
    };

    window.desvincularClienteRegular = function() {
        if (inputIdCliente) inputIdCliente.value = "";
        if (badgeCliente) badgeCliente.style.setProperty('display', 'none', 'important');
        if (inputCliente) {
            inputCliente.value = "";
            inputCliente.focus();
        }
        if (cajaSugerencias) cajaSugerencias.style.display = "none";

        // Vuelve a tarifa de venta ocasional
        cambiarTarifa('Ocasional', true);

        validarBotonCobrar();
    };

    // Control manual e infalible para el botón "Clientes Regulares"
    if (btnDropCli && menuDropCli) {
        btnDropCli.addEventListener("click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            const isOpen = menuDropCli.classList.contains("show");
            if (isOpen) {
                menuDropCli.classList.remove("show");
                btnDropCli.classList.remove("show");
                btnDropCli.setAttribute("aria-expanded", "false");
            } else {
                menuDropCli.classList.add("show");
                btnDropCli.classList.add("show");
                btnDropCli.setAttribute("aria-expanded", "true");
                setTimeout(() => {
                    const searchInput = document.getElementById("buscadorRapidoClientesDrop");
                    if (searchInput) searchInput.focus();
                }, 80);
            }
        });

        // Cerrar al hacer clic fuera
        document.addEventListener("click", function(e) {
            if (!btnDropCli.contains(e.target) && !menuDropCli.contains(e.target)) {
                menuDropCli.classList.remove("show");
                btnDropCli.classList.remove("show");
                btnDropCli.setAttribute("aria-expanded", "false");
            }
            if (cajaSugerencias && !cajaSugerencias.contains(e.target) && (!inputCliente || !inputCliente.contains(e.target))) {
                cajaSugerencias.style.display = "none";
            }
        });
    }

    // Autocompletado dinámico mientras el usuario escribe en el input
    if (inputCliente) {
        inputCliente.addEventListener("input", function() {
            const query = this.value.toLowerCase().trim();

            // Desvincular si cambió el texto respecto al cliente regular seleccionado
            if (inputIdCliente && inputIdCliente.value !== "") {
                if (txtNomReg && query !== txtNomReg.textContent.trim().toLowerCase()) {
                    inputIdCliente.value = "";
                    if (badgeCliente) badgeCliente.style.setProperty('display', 'none', 'important');
                }
            }

            // Mostrar sugerencias coincidentes
            if (cajaSugerencias) {
                if (query.length >= 1 && (!inputIdCliente || inputIdCliente.value === "")) {
                    const coincidencias = clientesCatalogo.filter(c => 
                        c.nombre.toLowerCase().includes(query) || 
                        (c.telefono && c.telefono.includes(query)) ||
                        (c.ci_nit && c.ci_nit.toLowerCase().includes(query))
                    );

                    if (coincidencias.length > 0) {
                        let html = '';
                        coincidencias.slice(0, 5).forEach(c => {
                            const badgeDeuda = c.saldo_pendiente > 0 
                                ? `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle ms-1" style="font-size:0.68rem;">Debe Bs. ${c.saldo_pendiente.toFixed(2)}</span>` 
                                : '';
                            const meta = c.telefono ? `<small class="text-muted ms-2"><i class="bi bi-telephone text-success"></i> ${c.telefono}</small>` : '';
                            html += `
                                <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3"
                                        onclick="seleccionarClienteRegular(${c.id}, '${c.nombre.replace(/'/g, "\\'")}', '${c.telefono || ''}')">
                                    <div>
                                        <strong class="text-dark small">${c.nombre}</strong> ${meta}
                                    </div>
                                    <div>${badgeDeuda}</div>
                                </button>
                            `;
                        });
                        cajaSugerencias.innerHTML = html;
                        cajaSugerencias.style.display = "block";
                    } else {
                        cajaSugerencias.style.display = "none";
                    }
                } else {
                    cajaSugerencias.style.display = "none";
                }
            }

            validarBotonCobrar();
        });
    }

    // Buscador dentro del Dropdown de Clientes Regulares
    const buscadorDrop = document.getElementById("buscadorRapidoClientesDrop");
    if (buscadorDrop) {
        buscadorDrop.addEventListener("input", function() {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll("#listaDropClientes .item-cliente-drop").forEach(item => {
                const txt = item.textContent.toLowerCase();
                item.style.display = (q === "" || txt.includes(q)) ? "" : "none";
            });
        });
    }

    // 4. Vaciar carrito
    if (btnVaciar) {
        btnVaciar.addEventListener('click', function() {
            if (carrito.length > 0 && confirm('¿Deseas vaciar todo el carrito?')) {
                carrito = [];
                renderizarCarrito();
            }
        });
    }

    // 5. Validar botón de cobrar
    function validarBotonCobrar() {
        const hayProductos = carrito.length > 0;
        const hayCliente = inputCliente && inputCliente.value.trim() !== '';

        if (hayProductos && hayCliente) {
            btnCobrar.disabled = false;
            const total = parseFloat(inputTotalVenta.value) || 0;
            btnCobrar.innerHTML = `<i class="bi bi-check2-circle me-2"></i> COBRAR (Bs. ${total.toFixed(2)})`;
        } else {
            btnCobrar.disabled = true;
            btnCobrar.innerHTML = `<i class="bi bi-check2-circle me-2"></i> COBRAR VENTA`;
        }
    }

    if (inputCliente) {
        inputCliente.addEventListener('input', validarBotonCobrar);
    }

    // 6. Dibujar el carrito en pantalla
    function renderizarCarrito() {
        listaCarrito.innerHTML = '';
        inputsOcultos.innerHTML = '';
        let total = 0;

        if (carrito.length === 0) {
            mensajeVacio.style.display = 'block';
            if (btnVaciar) btnVaciar.style.display = 'none';
            txtSubtotal.textContent = 'Bs. 0.00';
            txtTotal.textContent = 'Bs. 0.00';
            inputTotalVenta.value = 0;
            actualizarSaldoPendiente();
            validarBotonCobrar();
            return;
        }

        mensajeVacio.style.display = 'none';
        if (btnVaciar) btnVaciar.style.display = 'inline-block';

        carrito.forEach((item, index) => {
            const subtotalItem = item.precio * item.cantidad;
            total += subtotalItem;

            const esCliente = item.esTarifaCliente;
            const badgeTarifaItem = esCliente
                ? `<span class="badge" style="font-size: 0.65rem; background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; cursor: pointer;" title="Tarifa Cliente Regular aplicada. Haz clic para alternar a Precio Venta"><i class="bi bi-tag-fill me-0.5"></i>P. Cliente</span>`
                : `<span class="badge bg-light text-secondary border" style="font-size: 0.65rem; cursor: pointer;" title="Tarifa Venta Ocasional aplicada. Haz clic para alternar a Precio Cliente">P. Venta</span>`;

            const li = document.createElement('li');
            li.className = 'list-group-item bg-white rounded-3 shadow-sm mb-2 p-2.5 border-0';
            li.innerHTML = `
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="me-2" style="flex: 1 1 auto; min-width: 0;">
                        <span class="badge bg-light text-secondary border font-monospace me-1" style="font-size: 0.72rem; padding: 2px 6px;">${item.codigo}</span>
                        <span class="fw-bold text-dark d-inline" style="font-size: 0.88rem; line-height: 1.35; word-break: break-word;">${item.nombre}</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-eliminar-item text-decoration-none ms-1 flex-shrink-0" data-index="${index}" title="Quitar producto">
                        <i class="bi bi-trash3 fs-5"></i>
                    </button>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-1.5 border-top border-light">
                    <div class="input-group input-group-sm" style="width: 112px; height: 28px;">
                        <button class="btn btn-outline-secondary btn-restar fw-bold px-2 py-0 d-flex align-items-center justify-content-center" type="button" data-index="${index}" title="Restar 1">−</button>
                        <input type="number" 
                               class="form-control text-center input-cantidad-item fw-bold py-0 px-1 bg-white" 
                               style="font-size: 0.85rem;" 
                               data-index="${index}" 
                               value="${item.cantidad}" 
                               min="1" 
                               max="${item.stockMaximo}" 
                               step="1"
                               title="Ingresa la cantidad directamente o usa +/-">
                        <button class="btn btn-outline-secondary btn-sumar fw-bold px-2 py-0 d-flex align-items-center justify-content-center" type="button" data-index="${index}" title="Sumar 1">+</button>
                    </div>
                    <div class="text-end">
                        <div class="d-flex align-items-center justify-content-end gap-1 mb-0.5">
                            <span onclick="togglePrecioItem(${index})" style="cursor: pointer;">
                                ${badgeTarifaItem}
                            </span>
                            <small class="text-muted" style="font-size: 0.75rem;">U: Bs. ${item.precio.toFixed(2)}</small>
                        </div>
                        <span class="text-success fw-bold" style="font-size: 0.92rem;">Bs. ${subtotalItem.toFixed(2)}</span>
                    </div>
                </div>
            `;
            listaCarrito.appendChild(li);

            // Inputs ocultos para enviar al backend
            inputsOcultos.innerHTML += `
                <input type="hidden" name="productos[${index}][id]" value="${item.id}">
                <input type="hidden" name="productos[${index}][cantidad]" value="${item.cantidad}">
                <input type="hidden" name="productos[${index}][precio]" value="${item.precio}">
            `;
        });

        txtSubtotal.textContent = `Bs. ${total.toFixed(2)}`;
        txtTotal.textContent = `Bs. ${total.toFixed(2)}`;
        inputTotalVenta.value = total;

        actualizarSaldoPendiente();
        validarBotonCobrar();
        asignarEventosCarrito();
    }

    // Alternar individualmente el precio de un ítem en el carrito
    window.togglePrecioItem = function(index) {
        const item = carrito[index];
        if (!item) return;
        if (item.esTarifaCliente) {
            item.precio = item.precioVenta;
            item.esTarifaCliente = false;
        } else {
            item.precio = item.precioCliente;
            item.esTarifaCliente = true;
        }
        renderizarCarrito();
    };

    function asignarEventosCarrito() {
        // Entrada numérica directa
        document.querySelectorAll('.input-cantidad-item').forEach(input => {
            // Selecciona todo el texto al recibir foco para escribir inmediatamente sin borrar a mano
            input.addEventListener('focus', function() {
                this.select();
            });

            // Confirmar y aplicar cambio al presionar Enter
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.blur();
                }
            });

            // Procesar cantidad ingresada al cambiar o perder foco
            input.addEventListener('change', function() {
                const idx = parseInt(this.dataset.index);
                if (isNaN(idx) || !carrito[idx]) return;

                let nuevaCant = parseInt(this.value, 10);
                const stockMax = carrito[idx].stockMaximo || 999999;

                if (isNaN(nuevaCant) || nuevaCant < 1) {
                    nuevaCant = 1;
                }

                if (nuevaCant > stockMax) {
                    alert(`¡Atención! Stock máximo disponible: ${stockMax} unidad(es).`);
                    nuevaCant = stockMax;
                }

                carrito[idx].cantidad = nuevaCant;
                renderizarCarrito();
            });
        });

        // Sumar
        document.querySelectorAll('.btn-sumar').forEach(btn => {
            btn.addEventListener('click', function() {
                const idx = parseInt(this.dataset.index);
                if (carrito[idx].cantidad < carrito[idx].stockMaximo) {
                    carrito[idx].cantidad++;
                    renderizarCarrito();
                } else {
                    alert(`Stock máximo disponible: ${carrito[idx].stockMaximo}`);
                }
            });
        });

        // Restar
        document.querySelectorAll('.btn-restar').forEach(btn => {
            btn.addEventListener('click', function() {
                const idx = parseInt(this.dataset.index);
                if (carrito[idx].cantidad > 1) {
                    carrito[idx].cantidad--;
                    renderizarCarrito();
                }
            });
        });

        // Eliminar
        document.querySelectorAll('.btn-eliminar-item').forEach(btn => {
            btn.addEventListener('click', function() {
                const idx = parseInt(this.dataset.index);
                carrito.splice(idx, 1);
                renderizarCarrito();
            });
        });
    }
});
</script>


@endsection
