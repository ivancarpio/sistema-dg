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

    /* Inputs ultra legibles y estilizados para Talonario y Cliente */
    .input-pos-destacado {
        font-size: 0.96rem !important;
        font-weight: 600 !important;
        color: #0f172a !important;
        height: 40px !important;
        border: 2px solid #cbd5e1 !important;
        border-radius: 9px !important;
        background-color: #ffffff !important;
        padding-left: 12px !important;
        transition: all 0.2s;
    }
    .input-pos-destacado:focus {
        border-color: #0284c7 !important;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15) !important;
        background-color: #ffffff !important;
    }
    .label-pos-destacado {
        font-size: 0.88rem !important;
        font-weight: 800 !important;
        color: #1e293b !important;
        margin-bottom: 4px !important;
        display: flex;
        align-items: center;
    }
    .item-sugerencia-cliente {
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    .item-sugerencia-cliente:hover {
        background-color: #f1f5f9 !important;
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

    /* Animación de giro para botón Actualizar Catálogo */
    /* Segmented pill selector para unidades duales en POS */
    .grupo-unidades-pos {
        border: 1px solid #cbd5e1 !important;
        background-color: #f1f5f9;
        display: inline-flex;
        padding: 2px !important;
    }
    .btn-toggle-unidad {
        transition: all 0.18s ease-in-out;
        border: none !important;
        box-shadow: none !important;
        color: #64748b;
        cursor: pointer;
        background: transparent;
    }
    .btn-toggle-unidad:hover:not(.active) {
        background-color: #e2e8f0;
        color: #0f172a;
    }
    .btn-toggle-unidad.active {
        background-color: #0284c7 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(2, 132, 199, 0.35) !important;
    }

    @keyframes spinPosAnim {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .spin-anim {
        display: inline-block;
        animation: spinPosAnim 0.75s linear infinite;
    }

    /* ===== RESPONSIVE MÓVIL — Punto de Venta (POS) ===== */
    @media (max-width: 767.98px) {
        /* Encabezado */
        .header-pos {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 10px !important;
        }
        .header-pos-title h3 {
            font-size: 1.1rem !important;
        }
        .header-pos-title small {
            font-size: 0.72rem !important;
        }
        .header-pos-icon {
            width: 36px !important;
            height: 36px !important;
            padding: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 8px !important;
        }
        .header-pos-icon i {
            font-size: 1.25rem !important;
        }
        .header-pos-badges {
            width: 100% !important;
            flex-wrap: wrap !important;
            gap: 6px !important;
        }
        .header-pos-badges .badge {
            font-size: 0.72rem !important;
            padding: 4px 8px !important;
        }

        /* Buscador y selectores de catálogo más compactos */
        .search-input-pos-group,
        .search-input-pos-group .input-group,
        #filtroCategoria,
        #btnActualizarCatalogoPos {
            height: 36px !important;
            font-size: 0.82rem !important;
        }
        .search-btn-left {
            padding: 0 10px !important;
            font-size: 0.82rem !important;
        }
        #btnActualizarCatalogoPos {
            font-size: 0.76rem !important;
            padding: 4px 8px !important;
        }

        /* Tabla de productos compacta para scroll cómodo */
        .table-custom-header th {
            padding: 7px 6px !important;
            font-size: 0.65rem !important;
            letter-spacing: 0.3px !important;
        }
        .table-custom td {
            padding: 6px 6px !important;
            font-size: 0.78rem !important;
        }
        .pos-prod-nombre {
            font-size: 0.78rem !important;
            line-height: 1.25 !important;
            font-weight: 700 !important;
            margin-bottom: 2px !important;
        }
        .badge-code {
            font-size: 0.68rem !important;
            padding: 1.5px 5px !important;
            border-radius: 4px !important;
        }
        .val-precio-venta {
            font-size: 0.82rem !important;
        }
        .val-precio-cliente {
            font-size: 0.78rem !important;
        }
        .grupo-unidades-pos {
            padding: 1px !important;
        }
        .btn-toggle-unidad {
            font-size: 0.65rem !important;
            padding: 1px 4px !important;
        }
        .btn-agregar-producto {
            font-size: 0.72rem !important;
            padding: 3px 8px !important;
        }
        .btn-agregar-producto .lbl-btn-agregar {
            font-size: 0.72rem !important;
        }
        td.ps-4 {
            padding-left: 6px !important;
        }
        td.pe-4, th.pe-4 {
            padding-right: 6px !important;
        }
        th.ps-4 {
            padding-left: 6px !important;
        }

        /* Carrito / Cobro en móvil */
        .box-total-pos {
            padding: 8px 12px !important;
        }
        .box-total-pos h2 {
            font-size: 1.35rem !important;
        }
        .btn-cobrar-pos {
            font-size: 0.95rem !important;
            padding: 10px !important;
        }
        .btn-tarifa {
            font-size: 0.74rem !important;
            padding: 4px 6px !important;
        }
        .btn-metodo-pago {
            padding: 6px 4px !important;
            font-size: 0.76rem !important;
        }
    }
</style>

@if(isset($ventaRehacer) && $ventaRehacer)
    <div class="alert alert-warning border-0 shadow-sm d-flex flex-wrap align-items-center justify-content-between mb-4 p-3 rounded-4" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-left: 5px solid #f59e0b !important;">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; font-size: 1.3rem;">
                <i class="bi bi-arrow-repeat"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-dark">
                    Rehaciendo Venta: <span class="text-primary">{{ $ventaRehacer->numero_nota ?? ('SYS-'.str_pad($ventaRehacer->id, 6, '0', STR_PAD_LEFT)) }}</span>
                    @if($ventaRehacer->estado === 'Anulada')
                        <span class="badge bg-danger ms-2">Anulada</span>
                    @endif
                </h6>
                <small class="text-secondary">
                    Cliente: <strong>{{ $ventaRehacer->cliente }}</strong> (Tarifa {{ $ventaRehacer->tipo_tarifa ?? 'Ocasional' }}) • Se cargaron automáticamente los <strong>{{ count($carritoRehacer) }} productos</strong> con sus precios cobrados.
                </small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
            <span class="badge bg-white text-dark border px-2.5 py-1.5 shadow-sm fw-bold">
                Total Original: <span class="text-success">Bs. {{ number_format($ventaRehacer->total, 2) }}</span>
            </span>
            <a href="/pos" class="btn btn-sm btn-outline-secondary fw-semibold bg-white rounded-pill px-3 shadow-sm" title="Descartar y empezar una nueva venta vacía">
                <i class="bi bi-x-circle me-1"></i> Descartar
            </a>
        </div>
    </div>
@endif

<!-- ENCABEZADO PRINCIPAL (ESTILO UNIFICADO DEL SISTEMA) -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 header-pos">
    <div class="d-flex align-items-center header-pos-title">
        <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3 text-primary header-pos-icon">
            <i class="bi bi-cart4 fs-2"></i>
        </div>
        <div>
            <h3 class="fw-bold m-0 text-dark">Punto de Venta (POS)</h3>
            <small class="text-muted">Caja registradora rápida • Selección de productos y cobro</small>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 header-pos-badges">
        @if(isset($arqueoActivo) && $arqueoActivo)
            <a href="/arqueos/sesion" class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-2 rounded-3 shadow-sm d-flex align-items-center text-decoration-none" style="font-size: 0.85rem;" title="Ver turno de caja activo en vivo">
                <i class="bi bi-record-circle-fill text-success me-1.5"></i> Caja Abierta · Turno #{{ $arqueoActivo->id }}
                <span class="ms-1.5 text-dark fw-bold">(Gaveta: Bs. {{ number_format($arqueoActivo->efectivo_esperado, 2) }})</span>
            </a>
        @else
            <a href="/arqueos/sesion" class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle px-3 py-2 rounded-3 shadow-sm d-flex align-items-center text-decoration-none" style="font-size: 0.85rem;" title="Abrir turno de caja">
                <i class="bi bi-exclamation-circle-fill text-warning me-1.5"></i> Sin Turno Abierto
                <span class="ms-1.5 text-primary fw-bold">[ Abrir Caja ]</span>
            </a>
        @endif
        <span class="badge bg-white text-dark border px-3 py-2 rounded-3 shadow-sm d-flex align-items-center" style="font-size: 0.85rem;">
            <i class="bi bi-person-badge-fill text-primary me-2 fs-6"></i> Cajero:&nbsp;<strong class="text-primary">{{ optional(auth()->user())->name ?? 'Usuario' }}</strong>
        </span>
    </div>
</div>

<div class="row g-4">
    <!-- COLUMNA IZQUIERDA: Catálogo de Productos Disponibles -->
    <div class="col-lg-7 col-xl-8">
        <div class="pos-card h-100 d-flex flex-column">
            <!-- Barra superior con buscador, categorías y botón Actualizar -->
            <div class="p-3 border-bottom bg-light bg-opacity-50">
                <div class="row g-2 align-items-center">
                    <div class="col-md-6 col-lg-6">
                        <div class="input-group search-input-pos-group" style="height: 44px;">
                            <span class="search-btn-left"><i class="bi bi-search"></i></span>
                            <input type="text" id="buscadorProductos" class="form-control search-input" placeholder="Buscar por producto, código o marca..." autofocus>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <select id="filtroCategoria" class="form-select border-1" style="height: 44px; font-size: 0.95rem;">
                            <option value="">Todas las categorías</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <button type="button" id="btnActualizarCatalogoPos" class="btn btn-outline-primary w-100 fw-bold d-flex align-items-center justify-content-center gap-1 shadow-sm" style="height: 44px; font-size: 0.88rem; border-radius: 10px;" title="Actualizar precios y existencias en tiempo real sin perder el carrito">
                            <i class="bi bi-arrow-clockwise fs-6" id="iconActualizarCatalogo"></i> <span>Actualizar</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Banner / Notificación de Catálogo Actualizado -->
            <div id="toastActualizarPos" class="alert alert-success alert-dismissible fade show border-0 rounded-0 py-2 px-3 m-0 align-items-center justify-content-between" style="display: none; background: #ecfdf5; border-bottom: 1px solid #a7f3d0 !important; font-size: 0.83rem;">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill text-success me-2 fs-6"></i>
                    <span id="txtToastActualizarPos" class="text-success fw-semibold">Catálogo actualizado con éxito.</span>
                </div>
                <button type="button" class="btn-close py-2" onclick="document.getElementById('toastActualizarPos').style.display='none'"></button>
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
                            $unidadBase = optional($producto->unidad)->nombre ?? 'Pieza';
                            $pClienteBase = ($producto->precio_cliente !== null && $producto->precio_cliente > 0) ? floatval($producto->precio_cliente) : floatval($producto->precio_venta);
                            $tienePClienteBase = ($producto->precio_cliente !== null && $producto->precio_cliente > 0);

                            $tieneSec = $producto->tieneUnidadSecundaria();
                            $unidadSec = $tieneSec ? (optional($producto->unidadSecundaria)->nombre ?? 'Paquete') : '';
                            $factorSec = $tieneSec ? floatval($producto->factor_conversion ?? 1) : 1;
                            $pVentaSec = $tieneSec ? $producto->getPrecioSecundarioPara('Ocasional') : 0;
                            $pClienteSec = $tieneSec ? $producto->getPrecioSecundarioPara('Regular') : 0;
                            $tienePClienteSec = $tieneSec && (($producto->precio_cliente_secundario !== null && $producto->precio_cliente_secundario > 0) || ($producto->precio_venta_secundario !== null && $producto->precio_venta_secundario > 0));
                        @endphp
                        <tr class="fila-producto" 
                            data-id="{{ $producto->id }}" 
                            data-codigo="{{ $producto->codigo }}"
                            data-nombre="{{ $producto->nombre }}"
                            data-marca="{{ optional($producto->marca)->nombre ?? '' }}"
                            data-categoria-nombre="{{ optional($producto->categoria)->nombre ?? '' }}"
                            data-stock="{{ $producto->stock }}"
                            data-categoria="{{ $producto->id_categoria }}"
                            data-unidad-base="{{ $unidadBase }}"
                            data-precio-venta-base="{{ $producto->precio_venta }}"
                            data-precio-cliente-base="{{ $pClienteBase }}"
                            data-tiene-precio-cliente-base="{{ $tienePClienteBase ? '1' : '0' }}"
                            data-tiene-secundaria="{{ $tieneSec ? '1' : '0' }}"
                            data-unidad-secundaria="{{ $unidadSec }}"
                            data-factor-secundaria="{{ $factorSec }}"
                            data-precio-venta-secundario="{{ $pVentaSec }}"
                            data-precio-cliente-secundario="{{ $pClienteSec }}"
                            data-tiene-precio-cliente-sec="{{ $tienePClienteSec ? '1' : '0' }}"
                            data-unidad-activa="base"
                            data-precio-venta="{{ $producto->precio_venta }}"
                            data-precio-cliente="{{ $pClienteBase }}"
                            data-tiene-precio-cliente="{{ $tienePClienteBase ? '1' : '0' }}"
                            data-precio="{{ $producto->precio_venta }}">
                            
                            <td class="ps-4">
                                <span class="badge-code">{{ $producto->codigo }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark pos-prod-nombre" style="font-size: 0.98rem;">{{ $producto->nombre }}</div>
                                <div class="text-muted small d-flex flex-wrap align-items-center gap-1.5 mt-0.5">
                                    <span class="me-1"><i class="bi bi-tag me-1"></i>{{ optional($producto->marca)->nombre ?? 'Genérico' }}</span>
                                    @if($producto->categoria)
                                        <span class="badge bg-light text-muted border">{{ $producto->categoria->nombre }}</span>
                                    @endif
                                </div>
                                @if($tieneSec)
                                    <!-- Selector interactivo de unidad dual -->
                                    <div class="mt-1.5 d-flex align-items-center gap-1.5 selector-unidades-row">
                                        <span class="text-muted" style="font-size: 0.70rem; font-weight: 600;">Vender por:</span>
                                        <div class="btn-group btn-group-sm rounded-pill p-0.5 grupo-unidades-pos" role="group">
                                            <button type="button" class="btn btn-xs py-0.5 px-2 rounded-pill fw-bold btn-toggle-unidad active" data-tipo="base" style="font-size: 0.72rem;">
                                                <i class="bi bi-tag me-0.5"></i>{{ $unidadBase }}
                                            </button>
                                            <button type="button" class="btn btn-xs py-0.5 px-2 rounded-pill fw-bold btn-toggle-unidad" data-tipo="secundaria" style="font-size: 0.72rem;">
                                                <i class="bi bi-boxes me-0.5"></i>{{ $unidadSec }} (x{{ number_format($factorSec, 0) }})
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-0.5">
                                        <span class="badge bg-light text-secondary border py-0 px-1.5" style="font-size: 0.68rem;">
                                            <i class="bi bi-rulers me-1"></i>{{ $unidadBase }}
                                        </span>
                                    </div>
                                @endif
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
                                <div class="text-muted small mt-0.5 txt-unidad-stock" style="font-size: 0.68rem;">
                                    {{ $unidadBase }}
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="box-precio-pos">
                                    <!-- Tarifa Venta Ocasional -->
                                    <div class="linea-precio-venta d-flex align-items-center justify-content-end gap-1">
                                        <span class="fw-bold text-dark val-precio-venta" style="font-size: 0.94rem;">
                                            {{ $monedaSimbolo }} {{ number_format($producto->precio_venta, 2) }}
                                        </span>
                                        <span class="badge bg-light text-secondary border py-0 px-1" style="font-size: 0.64rem;">Venta</span>
                                    </div>
                                    <!-- Tarifa Cliente Regular / Ferretería -->
                                    <div class="linea-precio-cliente box-pcliente-wrap d-flex align-items-center justify-content-end gap-1 mt-0.5" style="color: #0284c7; {{ $tienePClienteBase ? '' : 'display:none !important;' }}">
                                        <i class="bi bi-tag-fill" style="font-size: 0.65rem;"></i>
                                        <span class="fw-bold val-precio-cliente" style="font-size: 0.88rem;">
                                            {{ $monedaSimbolo }} {{ number_format($pClienteBase, 2) }}
                                        </span>
                                        <span class="badge py-0 px-1" style="font-size: 0.64rem; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;">Cliente</span>
                                    </div>
                                    <div class="linea-precio-cliente-mismo text-muted mt-0.5" style="font-size: 0.68rem; {{ $tienePClienteBase ? 'display:none !important;' : '' }}" title="Sin precio especial: aplica el mismo precio de venta">
                                        <span>Cliente: <em>Mismo</em></span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center pe-4">
                                <button type="button" class="btn btn-sm btn-primary fw-bold px-3 py-1.5 rounded-pill btn-agregar-producto shadow-sm">
                                    <i class="bi bi-plus-lg me-1"></i> <span class="lbl-btn-agregar">Agregar</span>
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
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-receipt-cutoff fs-5 text-primary"></i>
                    <h6 class="fw-bold m-0 text-dark">Detalle de Cobro</h6>
                    <span id="badgeAutoGuardado" class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-0.5" style="display: none; font-size: 0.68rem;" title="Tu carrito está guardado automáticamente en cola">
                        <i class="bi bi-cloud-check-fill me-1"></i>En Cola
                    </span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger border-0 py-0 px-2" id="btnVaciarCarrito" title="Vaciar carrito" style="display: none; font-size: 0.78rem;">
                    <i class="bi bi-trash3 me-1"></i> Vaciar
                </button>
            </div>
            
            <!-- FORMULARIO DE COBRO -->
            <form id="formVenta" action="/pos/cobrar" method="POST" class="d-flex flex-column flex-grow-1 m-0">
                @csrf

                <!-- Banner de Carrito en Cola Restaurado (Estilo PedidosYa / Delivery) -->
                <div id="bannerCarritoEnCola" class="p-2.5 px-3 border-bottom" style="display: none; background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 28px; height: 28px; font-size: 0.85rem;">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div style="min-width: 0;">
                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.80rem;">
                                    Carrito en cola recuperado
                                    <span class="badge bg-primary text-white ms-1" id="badgeNumItemsCola" style="font-size: 0.68rem;">0 productos</span>
                                </div>
                                <div class="text-muted small" style="font-size: 0.70rem;" id="txtDetalleCarritoCola">
                                    Restauramos tu carrito pendiente para que no pierdas tu avance.
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-1 flex-shrink-0 ms-2">
                            <button type="button" class="btn btn-sm btn-outline-danger py-0.5 px-2 rounded-pill fw-semibold" id="btnDescartarCarritoCola" style="font-size: 0.70rem;" title="Descartar carrito en cola">
                                <i class="bi bi-x-circle me-1"></i>Descartar
                            </button>
                            <button type="button" class="btn btn-sm btn-primary py-0.5 px-2.5 rounded-pill fw-bold" id="btnContinuarCarritoCola" style="font-size: 0.70rem;" title="Continuar venta">
                                <i class="bi bi-check2 me-1"></i>Continuar
                            </button>
                        </div>
                    </div>
                </div>
                
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
                            <span id="txtSubtotal">{{ $monedaSimbolo }} 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fs-5 fw-bold text-dark">TOTAL A PAGAR:</span>
                            <span class="fs-3 fw-bold text-success" id="txtTotal">{{ $monedaSimbolo }} 0.00</span>
                        </div>
                        <!-- Conversión Multimoneda en Tiempo Real (Ragnar Capital) -->
                        <div class="d-flex justify-content-between align-items-center pt-2 mt-2 border-top border-success-subtle" style="font-size: 0.85rem;">
                            <span class="text-secondary fw-bold d-flex align-items-center gap-1">
                                <span id="badgeFlagEquiv">{{ ($paisOperacion ?? 'BO') === 'PE' ? '🇧🇴 Equivalente Bolivia:' : '🇵🇪 Equivalente Perú:' }}</span>
                            </span>
                            <span class="fw-extrabold text-primary fs-6" id="txtTotalEquivalente">
                                {{ ($paisOperacion ?? 'BO') === 'PE' ? 'Bs. 0.00' : 'S/ 0.00' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-muted mt-1" style="font-size: 0.68rem;">
                            <span><i class="bi bi-arrow-repeat text-success me-0.5"></i> Cotización Ragnar</span>
                            <span>Tasa Venta: 1 Sol = <strong>Bs. {{ number_format($tipoCambio->tasa_venta ?? 3.6, 2) }}</strong></span>
                        </div>
                    </div>
                    
                    <!-- Inputs ocultos para enviar los productos -->
                    <input type="hidden" name="total_venta" id="inputTotalVenta" value="0">
                    <input type="hidden" name="venta_origen_id" id="inputVentaOrigenId" value="{{ isset($ventaRehacer) && $ventaRehacer ? $ventaRehacer->id : '' }}">
                    <input type="hidden" name="resolucion_diferencia" id="inputResolucionDiferencia" value="">
                    <input type="hidden" name="metodo_diferencia" id="inputMetodoDiferencia" value="">
                    <input type="hidden" name="referencia_diferencia" id="inputReferenciaDiferencia" value="">
                    <div id="inputsOcultosCarrito"></div>

                    <!-- CAMPO 1: Nº DE TALONARIO / NOTA FÍSICA -->
                    <div class="mb-2.5 text-start">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="label-pos-destacado m-0 d-flex align-items-center" for="inputNumeroNota">
                                <i class="bi bi-receipt text-primary me-1.5 fs-6"></i>
                                <span>Nº de Talonario</span>
                                <span class="badge bg-light text-secondary border ms-1.5 fw-normal py-0 px-1.5" style="font-size: 0.68rem;">Opcional</span>
                            </label>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle font-monospace py-0 px-1.5" style="font-size: 0.70rem;">
                                Auto: SYS-XXXXXX
                            </span>
                        </div>
                        <input type="text" 
                               name="numero_nota" 
                               id="inputNumeroNota"
                               class="form-control input-pos-destacado" 
                               placeholder="Ej. 00154 (vacío = correlativo automático)"
                               style="height: 40px !important; font-size: 0.95rem !important;">
                    </div>

                    <!-- CAMPO 2: SELECTOR DE TARIFA (OCASIONAL VS REGULAR / FERRETERÍA) -->
                    <div class="mb-2.5 text-start">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="label-pos-destacado m-0 d-flex align-items-center gap-1.5">
                                <i class="bi bi-tags-fill text-primary fs-6"></i>
                                <span>Lista de Precios</span>
                            </label>
                            <span id="badgeTarifaActiva" class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle py-0.5 px-2" style="font-size: 0.70rem;">
                                Tarifa Venta Ocasional
                            </span>
                        </div>
                        <div class="btn-group w-100 p-1 rounded-3 bg-light border shadow-sm" role="group">
                            <input type="radio" class="btn-check" name="tipo_tarifa" id="tarifa_ocasional" value="Ocasional" checked autocomplete="off">
                            <label class="btn btn-tarifa py-1.5 w-50 fw-bold" for="tarifa_ocasional" id="lblTarifaOcasional">
                                <i class="bi bi-person me-1"></i> Cliente Ocasional
                                <small class="d-block text-muted fw-normal" style="font-size: 0.68rem;">Precio Venta Normal</small>
                            </label>

                            <input type="radio" class="btn-check" name="tipo_tarifa" id="tarifa_regular" value="Regular" autocomplete="off">
                            <label class="btn btn-tarifa py-1.5 w-50 fw-bold" for="tarifa_regular" id="lblTarifaRegular">
                                <i class="bi bi-building-check me-1"></i> Cliente Regular
                                <small class="d-block text-muted fw-normal" style="font-size: 0.68rem;">Precio Ferretería / Mayorista</small>
                            </label>
                        </div>
                    </div>

                    <!-- CAMPO 3: NOMBRE DEL CLIENTE (CON BUSCADOR Y AUTOCOMPLETADO INTELIGENTE) -->
                    <div class="mb-2.5 text-start position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="d-flex align-items-center gap-1.5">
                                <label class="label-pos-destacado m-0">
                                    <i class="bi bi-person-fill text-primary me-1 fs-6"></i>
                                    Nombre del Cliente *
                                </label>
                                <button type="button" class="btn btn-xs py-0 px-2 rounded-pill border fw-bold text-primary bg-primary bg-opacity-10" id="btnChipClienteVarios" style="font-size: 0.70rem;" title="Rellenar rápidamente 'Cliente Varios'">
                                    <i class="bi bi-magic me-1"></i>Varios
                                </button>
                            </div>
                            @if(count($clientes) > 0)
                            <div class="dropdown position-relative">
                                <button type="button" class="btn btn-sm btn-outline-primary fw-bold shadow-sm d-inline-flex align-items-center gap-1 px-2.5 py-0.5" 
                                        id="btnDropdownClientesRegulares"
                                        aria-expanded="false" style="font-size: 0.76rem; border-radius: 8px;"
                                        title="Buscar y seleccionar un cliente regular registrado">
                                    <i class="bi bi-building-check"></i> 
                                    <span>Ferreterías</span> 
                                    <i class="bi bi-chevron-down ms-0.5" style="font-size:0.65rem;"></i>
                                </button>
                                <div class="dropdown-menu shadow-lg p-2 rounded-3 border" 
                                     id="menuDropdownClientesRegulares"
                                     style="position: absolute !important; right: 0 !important; left: auto !important; top: 100% !important; min-width: 290px; max-width: 330px; width: 310px; max-height: 280px; overflow-y: auto; z-index: 1075;">
                                    <div class="px-2 py-1 mb-2 border-bottom">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                                            <input type="text" id="buscadorRapidoClientesDrop" class="form-control border-start-0" placeholder="Buscar ferretería o cliente...">
                                        </div>
                                    </div>
                                    <div id="listaDropClientes">
                                        @foreach($clientes as $cli)
                                            <a href="javascript:void(0)" class="dropdown-item p-2 rounded-2 item-cliente-drop mb-1"
                                               onclick="seleccionarClienteRegular({{ $cli->id }}, '{{ addslashes($cli->nombre) }}', '{{ $cli->telefono ?? '' }}')">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="fw-bold text-dark" style="font-size: 0.86rem;">{{ $cli->nombre }}</div>
                                                    @if(($cli->saldo_pendiente ?? 0) > 0)
                                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle" style="font-size: 0.66rem;">
                                                            Debe Bs. {{ number_format($cli->saldo_pendiente, 2) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="small text-muted" style="font-size: 0.72rem;">
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
                                <span class="input-group-text bg-white border-end-0 text-muted px-2.5" style="height: 40px;"><i class="bi bi-person fs-5"></i></span>
                                <input type="text" 
                                       name="cliente" 
                                       id="inputCliente" 
                                       class="form-control input-pos-destacado border-start-0" 
                                       value="Cliente Varios"
                                       placeholder="Nombre del cliente o ferretería..." 
                                       autocomplete="off"
                                       required
                                       style="height: 40px !important; font-size: 0.95rem !important;">
                            </div>
                            <!-- Lista flotante de autocompletado en tiempo real -->
                            <div id="sugerenciasClientesPos" class="list-group shadow-lg position-absolute w-100 rounded-3 border" 
                                 style="display: none; top: 100%; left: 0; z-index: 1080; max-height: 220px; overflow-y: auto; background: #ffffff;">
                            </div>
                        </div>

                        <!-- Aviso de coincidencia / Corrección de mayúsculas y acentos -->
                        <div id="contenedorAvisoCliente" class="mt-1" style="display: none;"></div>

                        <!-- Chip cuando un cliente regular está seleccionado -->
                        <div id="badgeClienteSeleccionado" class="mt-1.5 p-1.5 px-2.5 rounded-3 bg-primary bg-opacity-10 border border-primary-subtle d-flex justify-content-between align-items-center" style="display: none !important;">
                            <div class="d-flex align-items-center gap-1.5">
                                <i class="bi bi-patch-check-fill text-primary fs-6"></i>
                                <div>
                                    <span class="fw-bold text-primary small" id="txtNombreClienteRegular">—</span>
                                    <small class="text-muted ms-1" id="txtDetalleClienteRegular" style="font-size:0.72rem;">· Cliente Regular</small>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-link text-danger p-0 text-decoration-none fw-bold" onclick="desvincularClienteRegular()" title="Desvincular cliente regular">
                                <i class="bi bi-x-circle-fill fs-6"></i>
                            </button>
                        </div>
                    </div>

                    <!-- CAMPO 4: MÉTODO DE PAGO (EFECTIVO / QR / POR PAGAR) -->
                    <div class="mb-2.5 text-start">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="label-pos-destacado m-0">
                                <i class="bi bi-credit-card-2-front-fill text-primary me-1.5 fs-6"></i>
                                Método de Pago *
                            </label>
                            <button type="button" onclick="abrirModalQRManual()" class="btn btn-sm btn-outline-primary py-0 px-2 rounded-pill fw-semibold border-1 shadow-none" style="font-size: 0.72rem;" title="Ver código QR de pago">
                                <i class="bi bi-qr-code me-1"></i>Ver QR
                            </button>
                        </div>
                        <div class="row g-2">
                            <!-- Efectivo -->
                            <div class="col-4 position-relative">
                                <input type="radio" class="btn-check" name="metodo_pago" id="pago_efectivo" value="Efectivo" checked>
                                <label class="btn btn-metodo-pago metodo-efectivo w-100 d-flex flex-column align-items-center py-2" for="pago_efectivo">
                                    <i class="bi bi-cash-coin fs-5 mb-0.5"></i>
                                    <span style="font-size: 0.82rem; font-weight: 700;">Efectivo</span>
                                </label>
                            </div>

                            <!-- QR -->
                            <div class="col-4 position-relative">
                                <input type="radio" class="btn-check" name="metodo_pago" id="pago_qr" value="QR">
                                <label class="btn btn-metodo-pago metodo-qr w-100 d-flex flex-column align-items-center py-2" for="pago_qr">
                                    <i class="bi bi-qr-code-scan fs-5 mb-0.5"></i>
                                    <span style="font-size: 0.82rem; font-weight: 700;">QR Simple</span>
                                </label>
                            </div>

                            <!-- Por Pagar (Crédito) -->
                            <div class="col-4 position-relative">
                                <input type="radio" class="btn-check" name="metodo_pago" id="pago_por_pagar" value="Por Pagar">
                                <label class="btn btn-metodo-pago metodo-credito w-100 d-flex flex-column align-items-center py-2" for="pago_por_pagar">
                                    <i class="bi bi-clock-history fs-5 mb-0.5"></i>
                                    <span style="font-size: 0.82rem; font-weight: 700;">Por Pagar</span>
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
                <div class="d-flex justify-content-center mb-2">
                    <img src="{{ asset('img/qr_banco.jpeg') }}" 
                         alt="QR de Pago" 
                         class="img-fluid rounded-3 shadow-sm border p-2 bg-white mx-auto d-block" 
                         style="max-width: 240px; margin: 0 auto;"
                         onerror="this.src='https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=SISTEMA_DG_PAGO'">
                </div>
                <p class="text-muted small mb-0 fw-bold text-uppercase" style="font-size: 0.75rem;">Monto a transferir:</p>
                <h3 class="fw-bold text-success mb-1" id="totalModalQR">{{ $monedaSimbolo }} 0.00</h3>
                <div class="mb-2" id="boxEquivModalQR">
                    <span class="badge bg-light text-primary border border-primary-subtle px-2.5 py-1" style="font-size: 0.80rem;" id="equivModalQR">
                        <i class="bi bi-arrow-repeat me-1"></i> Equivalente
                    </span>
                </div>
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

<!-- ============================================== -->
<!-- MODAL DE RESOLUCIÓN DE DIFERENCIAS (REHACER)  -->
<!-- ============================================== -->
<div class="modal fade" id="modalDiferenciaRehacer" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-warning bg-opacity-25 border-0 py-3 px-4">
                <h6 class="modal-title fw-bold text-dark m-0 d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-repeat text-warning fs-5"></i>
                    <span id="tituloModalDiferencia">Ajuste por Venta Rehecha</span>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-start">
                <div class="p-3 rounded-3 mb-3 border bg-light">
                    <div class="d-flex justify-content-between mb-1 text-muted small">
                        <span>Total Pagado Originalmente:</span>
                        <strong class="text-dark" id="txtOrigModalDif">Bs. 0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1 text-muted small">
                        <span>Nuevo Total Corregido:</span>
                        <strong class="text-dark" id="txtNuevoModalDif">Bs. 0.00</strong>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold" id="lblResultadoDif">Diferencia:</span>
                        <span class="fs-4 fw-bold" id="txtMontoResultadoDif">Bs. 0.00</span>
                    </div>
                </div>

                <!-- PANEL CASO C: El total BAJÓ (A favor del cliente) -->
                <div id="panelCasoBaja" style="display: none;">
                    <p class="small text-muted mb-2 fw-semibold">
                        <i class="bi bi-info-circle text-primary me-1"></i>
                        Como el cliente pagó de más en la primera venta, selecciona cómo resolver la diferencia a su favor:
                    </p>

                    <div class="list-group gap-2 mb-3">
                        <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 rounded-3 border p-3 cursor-pointer">
                            <input class="form-check-input flex-shrink-0 fs-5 mt-0" type="radio" name="opt_resolucion" value="saldo_favor" checked>
                            <div>
                                <strong class="d-block text-dark fw-bold">
                                    <i class="bi bi-wallet2 text-success me-1"></i> Acreditar como Saldo a Favor del Cliente
                                </strong>
                                <small class="text-muted" style="font-size:0.75rem;">
                                    El monto queda como crédito a favor del cliente para descontar en futuras compras. No sale dinero físico de la caja de hoy.
                                </small>
                            </div>
                        </label>
                        <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 rounded-3 border p-3 cursor-pointer">
                            <input class="form-check-input flex-shrink-0 fs-5 mt-0" type="radio" name="opt_resolucion" value="devolucion_efectivo">
                            <div>
                                <strong class="d-block text-dark fw-bold">
                                    <i class="bi bi-cash-stack text-danger me-1"></i> Devolución en Efectivo de Caja
                                </strong>
                                <small class="text-muted" style="font-size:0.75rem;">
                                    Entregar el dinero físico en efectivo al cliente. Se registrará un egreso de caja en el turno de hoy para que tu gaveta cuadre exacta.
                                </small>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- PANEL CASO B: El total SUBIÓ (El cliente paga la diferencia) -->
                <div id="panelCasoSube" style="display: none;">
                    <p class="small text-muted mb-2 fw-semibold">
                        <i class="bi bi-info-circle text-primary me-1"></i>
                        El nuevo pedido tiene mayor valor. ¿Cómo abonará el cliente los <span id="txtDiferenciaPagarSpan" class="fw-bold text-success">Bs. 0.00</span> restantes?
                    </p>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="opt_metodo_dif" id="opt_dif_efectivo" value="Efectivo" checked>
                            <label class="btn btn-outline-success w-100 py-3 fw-bold rounded-3" for="opt_dif_efectivo">
                                <i class="bi bi-cash-coin fs-4 d-block mb-1"></i>
                                Efectivo
                            </label>
                        </div>
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="opt_metodo_dif" id="opt_dif_qr" value="QR">
                            <label class="btn btn-outline-primary w-100 py-3 fw-bold rounded-3" for="opt_dif_qr">
                                <i class="bi bi-qr-code-scan fs-4 d-block mb-1"></i>
                                QR Simple
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3 px-4 justify-content-between">
                <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary px-4 fw-bold rounded-pill shadow-sm" id="btnConfirmarResolucionDiferencia">
                    <i class="bi bi-check2-circle me-1"></i> Confirmar y Finalizar Venta
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JAVASCRIPT DEL CARRITO Y COBRO INTELIGENTE -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let carrito = [];
    const POS_STORAGE_KEY = 'dg_pos_carrito_activo_v1';
    const VENTA_COMPLETADA_EXITOSA = @if(session('success_venta_id')) true @else false @endif;
    const VENTA_REHACER = @json($ventaRehacer ?? null);
    const CARRITO_REHACER = @json($carritoRehacer ?? []);

    // Configuración Multimoneda y Tipo de Cambio Ragnar Capital
    const monedaSimbolo = '{{ $monedaSimbolo ?? "Bs." }}';
    const paisOperacion = '{{ $paisOperacion ?? "BO" }}';
    const tasaVentaPos = {{ (float)($tipoCambio->tasa_venta ?? 3.6) }};
    const txtTotalEquivalente = document.getElementById('txtTotalEquivalente');

    function calcularEquivalente(total) {
        if (!txtTotalEquivalente) return;
        if (total <= 0) {
            txtTotalEquivalente.textContent = paisOperacion === 'PE' ? 'Bs. 0.00' : 'S/ 0.00';
            return;
        }
        if (paisOperacion === 'PE') {
            const bs = total * tasaVentaPos;
            txtTotalEquivalente.textContent = `Bs. ${bs.toFixed(2)}`;
        } else {
            const sol = tasaVentaPos > 0 ? (total / tasaVentaPos) : 0;
            txtTotalEquivalente.textContent = `S/ ${sol.toFixed(2)}`;
        }
    }

    // Elementos DOM
    const listaCarrito = document.getElementById('listaCarrito');
    const mensajeVacio = document.getElementById('mensajeCarritoVacio');
    const txtSubtotal = document.getElementById('txtSubtotal');
    const txtTotal = document.getElementById('txtTotal');
    const inputTotalVenta = document.getElementById('inputTotalVenta');
    const inputCliente = document.getElementById('inputCliente');
    const inputIdCliente = document.getElementById('inputIdCliente');
    const inputNumeroNota = document.getElementById('inputNumeroNota');
    const inputsOcultos = document.getElementById('inputsOcultosCarrito');
    const btnCobrar = document.getElementById('btnCobrar');
    const btnVaciar = document.getElementById('btnVaciarCarrito');
    const buscador = document.getElementById('buscadorProductos');
    const filtroCat = document.getElementById('filtroCategoria');
    const tbodyProductos = document.querySelector('#tablaProductos tbody');
    let filasProductos = document.querySelectorAll('.fila-producto');
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

    // 1. Filtrado inteligente multi-palabra (Buscador + Categoría) con recálculo de paginación
    function filtrarProductos() {
        const texto = buscador ? buscador.value.toLowerCase().trim() : '';
        const cat = filtroCat ? filtroCat.value : '';
        const palabras = texto.split(/\s+/).filter(Boolean);

        productosFiltrados = Array.from(filasProductos).filter(fila => {
            const nombre = (fila.dataset.nombre || '').toLowerCase();
            const codigo = (fila.dataset.codigo || '').toLowerCase();
            const marca = (fila.dataset.marca || '').toLowerCase();
            const catNom = (fila.dataset.categoriaNombre || '').toLowerCase();
            const categoriaFila = fila.dataset.categoria || '';
            const textoCompleto = `${codigo} ${nombre} ${marca} ${catNom}`;

            // Coincidencia inteligente: todas las palabras ingresadas deben estar presentes
            const coincideTexto = palabras.length === 0 || palabras.every(p => textoCompleto.includes(p));
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
            const equivModal = document.getElementById('equivModalQR');
            if (equivModal && txtTotalEquivalente) {
                equivModal.innerHTML = `<i class="bi bi-arrow-repeat me-1"></i> ${txtTotalEquivalente.textContent} (Tasa: ${tasaVentaPos.toFixed(2)})`;
            }
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
            localStorage.removeItem(POS_STORAGE_KEY);
            formVenta.submit();
        });
    }

    let resolucionDiferenciaConfirmada = false;

    if (formVenta) {
        formVenta.addEventListener('submit', function(e) {
            if (carrito.length === 0) {
                e.preventDefault();
                alert('El carrito está vacío. Agrega al menos un producto antes de cobrar.');
                return;
            }

            // Si es venta rehecha y no se ha confirmado la resolución de la diferencia
            if (VENTA_REHACER && !resolucionDiferenciaConfirmada) {
                const origPagado = parseFloat(VENTA_REHACER.monto_pagado || 0);
                const nuevoTotal = parseFloat(inputTotalVenta.value) || 0;
                const diff = Math.round((nuevoTotal - origPagado) * 100) / 100;

                // Solo si la venta original YA había sido pagada y hay diferencia real
                if (origPagado > 0 && Math.abs(diff) > 0.01) {
                    e.preventDefault();
                    abrirModalResolucionDiferencia(origPagado, nuevoTotal, diff);
                    return;
                }
            }

            // Al cobrar se limpia el almacenamiento para que la próxima venta inicie limpia
            localStorage.removeItem(POS_STORAGE_KEY);
        });
    }

    function abrirModalResolucionDiferencia(origPagado, nuevoTotal, diff) {
        const modalEl = document.getElementById('modalDiferenciaRehacer');
        if (!modalEl || typeof bootstrap === 'undefined') return;

        document.getElementById('txtOrigModalDif').textContent = `Bs. ${origPagado.toFixed(2)}`;
        document.getElementById('txtNuevoModalDif').textContent = `Bs. ${nuevoTotal.toFixed(2)}`;
        
        const panelBaja = document.getElementById('panelCasoBaja');
        const panelSube = document.getElementById('panelCasoSube');
        const lblRes = document.getElementById('lblResultadoDif');
        const txtRes = document.getElementById('txtMontoResultadoDif');
        const titulo = document.getElementById('tituloModalDiferencia');

        if (diff < 0) {
            // El total BAJÓ: dinero a favor del cliente
            const sobrante = Math.abs(diff);
            titulo.innerHTML = '<i class="bi bi-wallet2 text-success fs-5"></i> Diferencia a Favor del Cliente';
            lblRes.textContent = 'Monto a favor del cliente:';
            lblRes.className = 'fw-bold text-success';
            txtRes.textContent = `Bs. ${sobrante.toFixed(2)}`;
            txtRes.className = 'fs-4 fw-bold text-success';

            panelBaja.style.display = 'block';
            panelSube.style.display = 'none';
        } else {
            // El total SUBIÓ: cliente debe pagar la diferencia
            titulo.innerHTML = '<i class="bi bi-cash-coin text-danger fs-5"></i> Diferencia Adicional por Cobrar';
            lblRes.textContent = 'Monto adicional a pagar:';
            lblRes.className = 'fw-bold text-danger';
            txtRes.textContent = `+Bs. ${diff.toFixed(2)}`;
            txtRes.className = 'fs-4 fw-bold text-danger';

            document.getElementById('txtDiferenciaPagarSpan').textContent = `Bs. ${diff.toFixed(2)}`;
            panelBaja.style.display = 'none';
            panelSube.style.display = 'block';
        }

        const m = bootstrap.Modal.getOrCreateInstance(modalEl);
        m.show();
    }

    const btnConfirmarDif = document.getElementById('btnConfirmarResolucionDiferencia');
    if (btnConfirmarDif) {
        btnConfirmarDif.addEventListener('click', function() {
            const nuevoTotal = parseFloat(inputTotalVenta.value) || 0;
            const origPagado = parseFloat(VENTA_REHACER.monto_pagado || 0);
            const diff = Math.round((nuevoTotal - origPagado) * 100) / 100;

            if (diff < 0) {
                // Caso baja: leer opción elegida
                const opt = document.querySelector('input[name="opt_resolucion"]:checked');
                document.getElementById('inputResolucionDiferencia').value = opt ? opt.value : 'saldo_favor';
            } else if (diff > 0) {
                // Caso sube: leer método de pago de la diferencia
                const opt = document.querySelector('input[name="opt_metodo_dif"]:checked');
                document.getElementById('inputMetodoDiferencia').value = opt ? opt.value : 'Efectivo';
            }

            resolucionDiferenciaConfirmada = true;
            localStorage.removeItem(POS_STORAGE_KEY);
            formVenta.submit();
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

        // Lógica de nombre del cliente según tarifa
        if (nuevaTarifa === 'Ocasional') {
            // Al pasar a Ocasional, si estaba vacío o tenía cliente regular, rellenar "Cliente Varios"
            if (inputCliente && (inputCliente.value.trim() === '' || (inputIdCliente && inputIdCliente.value !== ''))) {
                inputCliente.value = 'Cliente Varios';
                if (inputIdCliente) inputIdCliente.value = '';
                if (badgeCliente) badgeCliente.style.setProperty('display', 'none', 'important');
                const aviso = document.getElementById('contenedorAvisoCliente');
                if (aviso) aviso.style.display = 'none';
            }
        } else if (nuevaTarifa === 'Regular') {
            // Al pasar a Regular, si tenía "Cliente Varios", limpiar para elegir cliente regular
            if (inputCliente && inputCliente.value.trim().toLowerCase() === 'cliente varios') {
                inputCliente.value = '';
                const aviso = document.getElementById('contenedorAvisoCliente');
                if (aviso) aviso.style.display = 'none';
                inputCliente.focus();
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

        // Sincronizar precios en la tabla según la tarifa activa y la unidad activa en cada fila
        document.querySelectorAll('.fila-producto').forEach(fila => {
            actualizarFilaSegunUnidad(fila);
        });

        validarBotonCobrar();
        guardarCarritoEnStorage();
    };

    radiosTarifa.forEach(r => {
        r.addEventListener('change', function() {
            cambiarTarifa(this.value, true);
        });
    });

    // Función para refrescar precios y etiquetas de una fila según la unidad activa (base o secundaria)
    function actualizarFilaSegunUnidad(fila) {
        if (!fila) return;
        const tipo = fila.dataset.unidadActiva || 'base';
        const esSec = (tipo === 'secundaria');

        const pVentaBase = parseFloat(fila.dataset.precioVentaBase) || 0;
        const pClienteBase = parseFloat(fila.dataset.precioClienteBase) || pVentaBase;
        const tienePCBase = (fila.dataset.tienePrecioClienteBase === '1');

        const pVentaSec = parseFloat(fila.dataset.precioVentaSecundario) || 0;
        const pClienteSec = parseFloat(fila.dataset.precioClienteSecundario) || pVentaSec;
        const tienePCSec = (fila.dataset.tienePrecioClienteSec === '1');

        const pVenta = esSec ? pVentaSec : pVentaBase;
        const pCliente = esSec ? pClienteSec : pClienteBase;
        const tienePC = esSec ? tienePCSec : tienePCBase;

        fila.dataset.precioVenta = pVenta;
        fila.dataset.precioCliente = pCliente;
        fila.dataset.tienePrecioCliente = tienePC ? '1' : '0';
        fila.dataset.precio = (tarifaActiva === 'Regular') ? pCliente : pVenta;

        // Actualizar valores visuales de precios en la fila
        const elPVenta = fila.querySelector('.val-precio-venta');
        if (elPVenta) elPVenta.textContent = `${monedaSimbolo} ${pVenta.toFixed(2)}`;

        const elPCliente = fila.querySelector('.val-precio-cliente');
        if (elPCliente) elPCliente.textContent = `${monedaSimbolo} ${pCliente.toFixed(2)}`;

        const wrapPC = fila.querySelector('.box-pcliente-wrap');
        const wrapMismo = fila.querySelector('.linea-precio-cliente-mismo');
        if (wrapPC && wrapMismo) {
            wrapPC.style.setProperty('display', tienePC ? 'flex' : 'none', 'important');
            wrapMismo.style.setProperty('display', tienePC ? 'none' : 'block', 'important');
        }

        const unidadNom = esSec ? (fila.dataset.unidadSecundaria || 'Paquete') : (fila.dataset.unidadBase || 'Pieza');
        const lblStock = fila.querySelector('.txt-unidad-stock');
        if (lblStock) {
            if (esSec) {
                const factor = parseFloat(fila.dataset.factorSecundaria) || 1;
                const stkBase = parseInt(fila.dataset.stock) || 0;
                const equiv = Math.floor(stkBase / factor);
                lblStock.innerHTML = `<span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-1.5 py-0.5">${unidadNom} (disp: ~${equiv})</span>`;
            } else {
                lblStock.textContent = unidadNom;
            }
        }

        const lblBtn = fila.querySelector('.lbl-btn-agregar');
        if (lblBtn) {
            lblBtn.textContent = esSec ? `+ ${unidadNom}` : 'Agregar';
        }
    }

    // 3. Agregar productos al carrito y selector de unidad dual (Delegado en tbody para soportar actualizaciones dinámicas AJAX sin recargar)
    if (tbodyProductos) {
        tbodyProductos.addEventListener('click', function(e) {
            // A) Click en botón selector de unidad (Base vs Secundaria)
            const btnToggle = e.target.closest('.btn-toggle-unidad');
            if (btnToggle) {
                const fila = btnToggle.closest('tr');
                if (!fila) return;
                const tipo = btnToggle.dataset.tipo; // 'base' o 'secundaria'
                fila.dataset.unidadActiva = tipo;

                fila.querySelectorAll('.btn-toggle-unidad').forEach(b => {
                    b.classList.remove('active');
                });
                btnToggle.classList.add('active');

                actualizarFilaSegunUnidad(fila);
                return;
            }

            // B) Click en botón "+ Agregar"
            const boton = e.target.closest('.btn-agregar-producto');
            if (!boton) return;

            const fila = boton.closest('tr');
            if (!fila) return;

            const id = fila.dataset.id;
            const codigo = fila.dataset.codigo;
            const nombre = fila.dataset.nombre;
            const stockBase = parseInt(fila.dataset.stock) || 0;

            const tipoUnidad = fila.dataset.unidadActiva || 'base';
            const esSec = (tipoUnidad === 'secundaria');

            const unidadMedida = esSec
                ? (fila.dataset.unidadSecundaria || 'Paquete')
                : (fila.dataset.unidadBase || 'Pieza');

            const factorConversion = esSec
                ? (parseFloat(fila.dataset.factorSecundaria) || 1)
                : 1;

            const precioVenta = esSec
                ? (parseFloat(fila.dataset.precioVentaSecundario) || 0)
                : (parseFloat(fila.dataset.precioVentaBase) || 0);

            const precioCliente = esSec
                ? (parseFloat(fila.dataset.precioClienteSecundario) || precioVenta)
                : (parseFloat(fila.dataset.precioClienteBase) || precioVenta);

            const tienePrecioCliente = esSec
                ? (fila.dataset.tienePrecioClienteSec === '1')
                : (fila.dataset.tienePrecioClienteBase === '1');

            // Stock máximo vendible en esta unidad específica
            const stockMaximo = Math.floor(stockBase / factorConversion);

            if (stockMaximo < 1) {
                alert(`¡Stock insuficiente! El stock actual (${stockBase} u.) no alcanza para completar 1 ${unidadMedida} de ${factorConversion} unidades.`);
                return;
            }

            const esCliente = (tarifaActiva === 'Regular');
            const precioAUsar = esCliente ? precioCliente : precioVenta;

            // Clave única para permitir vender piezas y paquetes del mismo producto en la misma nota
            const itemKey = `${id}_${tipoUnidad}`;
            const existente = carrito.find(item => item.key === itemKey);

            if (existente) {
                // Verificar que no sobrepase el stock base físico total
                const unidadesOcupadasPorOtros = carrito
                    .filter(item => item.id === id && item.key !== itemKey)
                    .reduce((sum, it) => sum + (it.cantidad * (it.factorConversion || 1)), 0);
                
                const unidadesDisponibles = stockBase - unidadesOcupadasPorOtros;
                const maxPermitido = Math.floor(unidadesDisponibles / factorConversion);

                if (existente.cantidad < maxPermitido) {
                    existente.cantidad++;
                } else {
                    alert(`¡Atención! Stock máximo alcanzado (${maxPermitido} ${unidadMedida}).`);
                }
            } else {
                carrito.push({
                    key: itemKey,
                    id: id,
                    codigo: codigo,
                    nombre: nombre,
                    tipoUnidad: tipoUnidad,
                    unidadMedida: unidadMedida,
                    factorConversion: factorConversion,
                    tieneSecundaria: fila.dataset.tieneSecundaria === '1',
                    unidadBase: fila.dataset.unidadBase || 'Pieza',
                    unidadSecundaria: fila.dataset.unidadSecundaria || 'Paquete',
                    factorSecundaria: parseFloat(fila.dataset.factorSecundaria) || 1,
                    precioVentaBase: parseFloat(fila.dataset.precioVentaBase) || 0,
                    precioClienteBase: parseFloat(fila.dataset.precioClienteBase) || 0,
                    tienePrecioClienteBase: fila.dataset.tienePrecioClienteBase === '1',
                    precioVentaSecundario: parseFloat(fila.dataset.precioVentaSecundario) || 0,
                    precioClienteSecundario: parseFloat(fila.dataset.precioClienteSecundario) || 0,
                    tienePrecioClienteSec: fila.dataset.tienePrecioClienteSec === '1',
                    stockBase: stockBase,
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
            boton.classList.add('btn-success');
            setTimeout(() => boton.classList.remove('btn-success'), 300);
        });
    }

    
    // ==========================================
    // CATÁLOGO UNIFICADO DE CLIENTES (REGULARES + HISTORIAL DE VENTAS)
    // ==========================================
    const badgeCliente = document.getElementById("badgeClienteSeleccionado");
    const txtNomReg = document.getElementById("txtNombreClienteRegular");
    const txtDetReg = document.getElementById("txtDetalleClienteRegular");
    const btnDropCli = document.getElementById("btnDropdownClientesRegulares");
    const menuDropCli = document.getElementById("menuDropdownClientesRegulares");
    const cajaSugerencias = document.getElementById("sugerenciasClientesPos");
    const chipClienteVarios = document.getElementById("btnChipClienteVarios");
    const contenedorAvisoCliente = document.getElementById("contenedorAvisoCliente");

    @php
        $clientesRegularesJs = $clientes->map(function($c) {
            return [
                'id' => $c->id,
                'nombre' => trim($c->nombre),
                'telefono' => $c->telefono,
                'ci_nit' => $c->ci_nit,
                'saldo_pendiente' => floatval($c->saldo_pendiente ?? 0),
                'tipo' => 'regular',
                'total_compras' => 0,
            ];
        })->values();

        $clientesHistorialJs = (isset($clientesHistorial) ? $clientesHistorial : collect())->map(function($h) {
            return [
                'nombre' => trim($h->cliente),
                'total_compras' => intval($h->total_compras),
                'ultima_compra' => $h->ultima_compra ? date('d/m/Y', strtotime($h->ultima_compra)) : '',
                'tipo' => 'historial',
            ];
        })->values();
    @endphp

    const clientesRegularesCatalogo = @json($clientesRegularesJs);
    const clientesHistorialCatalogo = @json($clientesHistorialJs);

    function normalizarTexto(txt) {
        return (txt || '').toLowerCase()
            .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
            .replace(/\s+/g, ' ')
            .trim();
    }

    // Función rápida para asignar "Cliente Varios"
    window.asignarClienteVarios = function() {
        if (inputCliente) {
            inputCliente.value = "Cliente Varios";
            if (inputIdCliente) inputIdCliente.value = "";
            if (badgeCliente) badgeCliente.style.setProperty('display', 'none', 'important');
            if (cajaSugerencias) cajaSugerencias.style.display = "none";
            if (contenedorAvisoCliente) contenedorAvisoCliente.style.display = "none";
            cambiarTarifa('Ocasional', true);
            validarBotonCobrar();
            guardarCarritoEnStorage();
        }
    };

    if (chipClienteVarios) {
        chipClienteVarios.addEventListener('click', function(e) {
            e.preventDefault();
            asignarClienteVarios();
        });
    }

    // Seleccionar cliente regular con cuenta / ferretería
    window.seleccionarClienteRegular = function(id, nombre, tel) {
        if (inputCliente) inputCliente.value = nombre;
        if (inputIdCliente) inputIdCliente.value = id;
        if (txtNomReg) txtNomReg.textContent = nombre;
        if (txtDetReg) txtDetReg.textContent = tel ? `Cliente Regular · Tel: ${tel}` : 'Cliente Regular Registrado';
        if (badgeCliente) badgeCliente.style.setProperty('display', 'flex', 'important');
        if (contenedorAvisoCliente) contenedorAvisoCliente.style.display = "none";
        
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
        guardarCarritoEnStorage();
    };

    // Seleccionar cliente existente desde el historial de ventas previas
    window.seleccionarClienteHistorial = function(nombreExacto, totalCompras) {
        if (inputCliente) {
            inputCliente.value = nombreExacto;
            if (inputIdCliente) inputIdCliente.value = "";
            if (badgeCliente) badgeCliente.style.setProperty('display', 'none', 'important');
            if (cajaSugerencias) cajaSugerencias.style.display = "none";

            if (contenedorAvisoCliente) {
                contenedorAvisoCliente.innerHTML = `
                    <div class="d-flex align-items-center justify-content-between p-1.5 px-2.5 rounded-2 bg-light border border-success-subtle" style="font-size: 0.74rem;">
                        <span class="text-success fw-semibold">
                            <i class="bi bi-patch-check-fill text-success me-1"></i>Cliente en historial (${totalCompras} compra(s) previas)
                        </span>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle py-0 px-1" style="font-size:0.65rem;">Verificado</span>
                    </div>
                `;
                contenedorAvisoCliente.style.display = "block";
            }

            validarBotonCobrar();
            guardarCarritoEnStorage();
        }
    };

    // Aplicar corrección de mayúsculas y acentos
    window.aplicarNombreCorrecto = function(nombreCorrecto) {
        if (inputCliente) {
            inputCliente.value = nombreCorrecto;
            if (cajaSugerencias) cajaSugerencias.style.display = "none";
            if (contenedorAvisoCliente) {
                contenedorAvisoCliente.innerHTML = `
                    <div class="d-flex align-items-center p-1.5 px-2.5 rounded-2 bg-light border border-success-subtle text-success fw-semibold" style="font-size: 0.74rem;">
                        <i class="bi bi-check-circle-fill me-1"></i>Nombre corregido a: <strong>${nombreCorrecto}</strong>
                    </div>
                `;
                contenedorAvisoCliente.style.display = "block";
                setTimeout(() => {
                    if (contenedorAvisoCliente) contenedorAvisoCliente.style.display = "none";
                }, 3500);
            }
            validarBotonCobrar();
            guardarCarritoEnStorage();
        }
    };

    window.desvincularClienteRegular = function() {
        if (inputIdCliente) inputIdCliente.value = "";
        if (badgeCliente) badgeCliente.style.setProperty('display', 'none', 'important');
        if (inputCliente) {
            inputCliente.value = "Cliente Varios";
            inputCliente.focus();
        }
        if (cajaSugerencias) cajaSugerencias.style.display = "none";
        if (contenedorAvisoCliente) contenedorAvisoCliente.style.display = "none";

        // Vuelve a tarifa de venta ocasional
        cambiarTarifa('Ocasional', true);

        validarBotonCobrar();
        guardarCarritoEnStorage();
    };

    // Control manual para el botón "Clientes Regulares / Ferreterías"
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

    // Autocompletado dinámico inteligente mientras el usuario escribe en el input de cliente
    if (inputCliente) {
        inputCliente.addEventListener("input", function() {
            const rawVal = this.value;
            const queryNorm = normalizarTexto(rawVal);
            const aviso = document.getElementById("contenedorAvisoCliente");

            // Desvincular si cambió el texto respecto al cliente regular seleccionado
            if (inputIdCliente && inputIdCliente.value !== "") {
                if (txtNomReg && queryNorm !== normalizarTexto(txtNomReg.textContent)) {
                    inputIdCliente.value = "";
                    if (badgeCliente) badgeCliente.style.setProperty('display', 'none', 'important');
                }
            }

            // Mostrar sugerencias coincidentes (de clientes regulares y del historial de compras)
            if (cajaSugerencias) {
                if (queryNorm.length >= 1 && (!inputIdCliente || inputIdCliente.value === "")) {
                    const matchesReg = clientesRegularesCatalogo.filter(c => 
                        normalizarTexto(c.nombre).includes(queryNorm) || 
                        (c.telefono && c.telefono.includes(queryNorm)) ||
                        (c.ci_nit && normalizarTexto(c.ci_nit).includes(queryNorm))
                    );

                    const regNombresSet = new Set(clientesRegularesCatalogo.map(r => normalizarTexto(r.nombre)));
                    const matchesHist = clientesHistorialCatalogo.filter(h => 
                        !regNombresSet.has(normalizarTexto(h.nombre)) &&
                        normalizarTexto(h.nombre).includes(queryNorm)
                    );

                    const totalMatches = matchesReg.length + matchesHist.length;

                    if (totalMatches > 0) {
                        let html = '';

                        matchesReg.slice(0, 4).forEach(c => {
                            const badgeDeuda = c.saldo_pendiente > 0 
                                ? `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle ms-1" style="font-size:0.66rem;">Debe Bs. ${c.saldo_pendiente.toFixed(2)}</span>` 
                                : '';
                            const meta = c.telefono ? `<small class="text-muted ms-1.5"><i class="bi bi-telephone text-success"></i> ${c.telefono}</small>` : '';
                            html += `
                                <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3 border-0 border-bottom"
                                        onclick="seleccionarClienteRegular(${c.id}, '${c.nombre.replace(/'/g, "\\'")}', '${c.telefono || ''}')">
                                    <div>
                                        <strong class="text-dark small d-block">${c.nombre}</strong>
                                        <small class="text-muted" style="font-size: 0.72rem;">Cliente Regular ${meta}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle" style="font-size: 0.65rem;">Regular</span>
                                        ${badgeDeuda}
                                    </div>
                                </button>
                            `;
                        });

                        matchesHist.slice(0, 4).forEach(h => {
                            html += `
                                <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3 border-0 border-bottom"
                                        onclick="seleccionarClienteHistorial('${h.nombre.replace(/'/g, "\\'")}', ${h.total_compras})">
                                    <div>
                                        <strong class="text-dark small d-block">${h.nombre}</strong>
                                        <small class="text-muted" style="font-size: 0.72rem;">
                                            <i class="bi bi-clock-history text-secondary me-1"></i>${h.total_compras} venta(s) registradas anteriormente
                                        </small>
                                    </div>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle" style="font-size: 0.65rem;">Historial</span>
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

            // Detección de coincidencia para mayúsculas / ortografía en vivo
            if (aviso) {
                if (queryNorm.length >= 2) {
                    const exactoReg = clientesRegularesCatalogo.find(c => normalizarTexto(c.nombre) === queryNorm);
                    const exactoHist = clientesHistorialCatalogo.find(h => normalizarTexto(h.nombre) === queryNorm);
                    const match = exactoReg || exactoHist;

                    if (match && rawVal !== match.nombre) {
                        aviso.innerHTML = `
                            <div class="d-flex align-items-center justify-content-between p-1.5 px-2.5 rounded-2 bg-light border border-info-subtle" style="font-size: 0.74rem;">
                                <span><i class="bi bi-info-circle text-info me-1"></i>Registrado como: <strong>${match.nombre}</strong></span>
                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 fw-bold" onclick="aplicarNombreCorrecto('${match.nombre.replace(/'/g, "\\'")}')" style="font-size:0.70rem;">
                                    Corregir a "${match.nombre}"
                                </button>
                            </div>
                        `;
                        aviso.style.display = "block";
                    } else if (match && rawVal === match.nombre) {
                        const compras = match.total_compras ? ` · ${match.total_compras} compras previas` : '';
                        aviso.innerHTML = `
                            <div class="d-flex align-items-center p-1 px-2.5 rounded-2 text-muted" style="font-size: 0.72rem;">
                                <i class="bi bi-check-circle-fill text-success me-1"></i>Cliente en sistema${compras}
                            </div>
                        `;
                        aviso.style.display = "block";
                    } else {
                        aviso.style.display = "none";
                    }
                } else {
                    aviso.style.display = "none";
                }
            }

            validarBotonCobrar();
            guardarCarritoEnStorage();
        });

        // Al salir del input (blur): si escribió en minúsculas un nombre existente (ej. "cliente varios"), autocorregir a mayúsculas
        inputCliente.addEventListener("blur", function() {
            setTimeout(() => {
                const rawVal = this.value;
                const queryNorm = normalizarTexto(rawVal);
                if (queryNorm.length >= 2) {
                    const exactoReg = clientesRegularesCatalogo.find(c => normalizarTexto(c.nombre) === queryNorm);
                    const exactoHist = clientesHistorialCatalogo.find(h => normalizarTexto(h.nombre) === queryNorm);
                    const match = exactoReg || exactoHist;

                    if (match && rawVal !== match.nombre) {
                        this.value = match.nombre;
                        const aviso = document.getElementById("contenedorAvisoCliente");
                        if (aviso) aviso.style.display = "none";
                        validarBotonCobrar();
                        guardarCarritoEnStorage();
                    }
                }
            }, 250);
        });
    }

    // Buscador dentro del Dropdown de Clientes Regulares (Multi-palabra inteligente)
    const buscadorDrop = document.getElementById("buscadorRapidoClientesDrop");
    if (buscadorDrop) {
        buscadorDrop.addEventListener("input", function() {
            const q = this.value.toLowerCase().trim();
            const palabras = q.split(/\s+/).filter(Boolean);
            document.querySelectorAll("#listaDropClientes .item-cliente-drop").forEach(item => {
                const txt = item.textContent.toLowerCase();
                const match = palabras.length === 0 || palabras.every(p => txt.includes(p));
                item.style.display = match ? "" : "none";
            });
        });
    }

    // ==========================================
    // GESTIÓN DE ALMACENAMIENTO (CARRITO EN COLA / AUTO-GUARDADO ESTILO PEDIDOSYA)
    // ==========================================
    function guardarCarritoEnStorage() {
        if (carrito.length > 0) {
            const payload = {
                carrito: carrito,
                tarifaActiva: tarifaActiva,
                cliente: inputCliente ? inputCliente.value : '',
                idCliente: inputIdCliente ? inputIdCliente.value : '',
                nombreClienteRegular: txtNomReg ? txtNomReg.textContent : '',
                detalleClienteRegular: txtDetReg ? txtDetReg.textContent : '',
                numeroNota: inputNumeroNota ? inputNumeroNota.value : '',
                timestamp: Date.now()
            };
            try {
                localStorage.setItem(POS_STORAGE_KEY, JSON.stringify(payload));
            } catch (e) {
                console.warn('No se pudo guardar carrito en localStorage:', e);
            }
            const badgeAuto = document.getElementById('badgeAutoGuardado');
            if (badgeAuto) badgeAuto.style.display = 'inline-flex';
        } else {
            try {
                localStorage.removeItem(POS_STORAGE_KEY);
            } catch (e) {}
            const badgeAuto = document.getElementById('badgeAutoGuardado');
            if (badgeAuto) badgeAuto.style.display = 'none';
            const bannerCola = document.getElementById('bannerCarritoEnCola');
            if (bannerCola) bannerCola.style.display = 'none';
        }
    }

    function restaurarCarritoDesdeStorage() {
        // 1. Si se solicitó rehacer una venta específica, se restaura tal como fue cobrada
        if (VENTA_REHACER && Array.isArray(CARRITO_REHACER) && CARRITO_REHACER.length > 0) {
            carrito = CARRITO_REHACER;
            if (VENTA_REHACER.tipo_tarifa) {
                cambiarTarifa(VENTA_REHACER.tipo_tarifa, false);
            }
            if (inputCliente) {
                inputCliente.value = VENTA_REHACER.cliente || 'Cliente Varios';
            }
            if (VENTA_REHACER.id_cliente && inputIdCliente) {
                inputIdCliente.value = VENTA_REHACER.id_cliente;
                if (badgeCliente) {
                    if (txtNomReg) txtNomReg.textContent = VENTA_REHACER.cliente;
                    if (txtDetReg) txtDetReg.textContent = 'Cliente Regular Registrado';
                    badgeCliente.style.setProperty('display', 'flex', 'important');
                }
            }
            if (VENTA_REHACER.metodo_pago) {
                const radioPago = document.querySelector(`input[name="metodo_pago"][value="${VENTA_REHACER.metodo_pago}"]`);
                if (radioPago) {
                    radioPago.checked = true;
                    if (panelPorPagar) {
                        panelPorPagar.style.display = (VENTA_REHACER.metodo_pago === 'Por Pagar') ? 'block' : 'none';
                    }
                }
            }
            renderizarCarrito();
            guardarCarritoEnStorage();
            return;
        }

        if (VENTA_COMPLETADA_EXITOSA) {
            try {
                localStorage.removeItem(POS_STORAGE_KEY);
            } catch (e) {}
            if (inputCliente && (!inputCliente.value || inputCliente.value.trim() === '')) {
                inputCliente.value = 'Cliente Varios';
            }
            return;
        }

        try {
            const guardadoRaw = localStorage.getItem(POS_STORAGE_KEY);
            if (!guardadoRaw) {
                if (inputCliente && (!inputCliente.value || inputCliente.value.trim() === '')) {
                    inputCliente.value = 'Cliente Varios';
                }
                return;
            }

            const guardado = JSON.parse(guardadoRaw);
            if (guardado && Array.isArray(guardado.carrito) && guardado.carrito.length > 0) {
                carrito = guardado.carrito;

                if (guardado.tarifaActiva) {
                    cambiarTarifa(guardado.tarifaActiva, false);
                }

                if (inputCliente) {
                    inputCliente.value = guardado.cliente || 'Cliente Varios';
                }

                if (guardado.idCliente && inputIdCliente) {
                    inputIdCliente.value = guardado.idCliente;
                    if (badgeCliente) {
                        if (txtNomReg) txtNomReg.textContent = guardado.nombreClienteRegular || guardado.cliente;
                        if (txtDetReg) txtDetReg.textContent = guardado.detalleClienteRegular || 'Cliente Regular Registrado';
                        badgeCliente.style.setProperty('display', 'flex', 'important');
                    }
                }

                if (guardado.numeroNota && inputNumeroNota) {
                    inputNumeroNota.value = guardado.numeroNota;
                }

                renderizarCarrito();

                // Mostrar banner visual de "Carrito en cola recuperado"
                const bannerCola = document.getElementById('bannerCarritoEnCola');
                const badgeNum = document.getElementById('badgeNumItemsCola');
                const txtDet = document.getElementById('txtDetalleCarritoCola');
                if (bannerCola) {
                    if (badgeNum) badgeNum.textContent = `${carrito.length} ${carrito.length === 1 ? 'producto' : 'productos'}`;
                    if (txtDet) txtDet.textContent = `Restauramos tus ${carrito.length} productos pendientes de tu sesión anterior.`;
                    bannerCola.style.display = 'block';
                }
            }
        } catch (e) {
            console.error('Error al restaurar carrito en cola desde almacenamiento:', e);
        }
    }

    // Botones del banner "Carrito en Cola"
    const btnContinuarCola = document.getElementById('btnContinuarCarritoCola');
    if (btnContinuarCola) {
        btnContinuarCola.addEventListener('click', function() {
            const bannerCola = document.getElementById('bannerCarritoEnCola');
            if (bannerCola) bannerCola.style.display = 'none';
        });
    }

    const btnDescartarCola = document.getElementById('btnDescartarCarritoCola');
    if (btnDescartarCola) {
        btnDescartarCola.addEventListener('click', function() {
            if (confirm('¿Deseas descartar este carrito en cola y empezar uno nuevo?')) {
                carrito = [];
                try {
                    localStorage.removeItem(POS_STORAGE_KEY);
                } catch (e) {}
                renderizarCarrito();
                const bannerCola = document.getElementById('bannerCarritoEnCola');
                if (bannerCola) bannerCola.style.display = 'none';
            }
        });
    }

    // 4. Vaciar carrito
    if (btnVaciar) {
        btnVaciar.addEventListener('click', function() {
            if (carrito.length > 0 && confirm('¿Deseas vaciar todo el carrito?')) {
                carrito = [];
                try {
                    localStorage.removeItem(POS_STORAGE_KEY);
                } catch (e) {}
                renderizarCarrito();
                const bannerCola = document.getElementById('bannerCarritoEnCola');
                if (bannerCola) bannerCola.style.display = 'none';
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
        inputCliente.addEventListener('input', function() {
            validarBotonCobrar();
            guardarCarritoEnStorage();
        });
    }

    if (inputNumeroNota) {
        inputNumeroNota.addEventListener('input', guardarCarritoEnStorage);
    }

    // 6. Dibujar el carrito en pantalla
    function renderizarCarrito() {
        listaCarrito.innerHTML = '';
        inputsOcultos.innerHTML = '';
        let total = 0;

        if (carrito.length === 0) {
            mensajeVacio.style.display = 'block';
            if (btnVaciar) btnVaciar.style.display = 'none';
            txtSubtotal.textContent = `${monedaSimbolo} 0.00`;
            txtTotal.textContent = `${monedaSimbolo} 0.00`;
            calcularEquivalente(0);
            inputTotalVenta.value = 0;
            actualizarSaldoPendiente();
            validarBotonCobrar();
            guardarCarritoEnStorage();
            return;
        }

        mensajeVacio.style.display = 'none';
        if (btnVaciar) btnVaciar.style.display = 'inline-block';

        carrito.forEach((item, index) => {
            // Asegurar compatibilidad de campos para unidades duales
            if (!item.key) item.key = `${item.id}_${item.tipoUnidad || 'base'}`;
            if (!item.unidadMedida) item.unidadMedida = 'Pieza';
            if (!item.factorConversion) item.factorConversion = 1;

            const subtotalItem = item.precio * item.cantidad;
            total += subtotalItem;

            const esCliente = item.esTarifaCliente;
            const badgeTarifaItem = esCliente
                ? `<span class="badge" style="font-size: 0.65rem; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; cursor: pointer;" title="Tarifa Cliente Regular aplicada. Haz clic para alternar a Precio Venta"><i class="bi bi-tag-fill me-0.5"></i>P. Cliente</span>`
                : `<span class="badge bg-light text-secondary border" style="font-size: 0.65rem; cursor: pointer;" title="Tarifa Venta Ocasional aplicada. Haz clic para alternar a Precio Cliente">P. Venta</span>`;

            const badgeUnidad = item.tieneSecundaria
                ? `<span class="badge ${item.tipoUnidad === 'secundaria' ? 'bg-primary text-white' : 'bg-light text-dark border'} fw-bold ms-1" 
                         style="font-size: 0.70rem; cursor: pointer; transition: all 0.2s;" 
                         onclick="toggleUnidadItemCarrito(${index})" 
                         title="Haz clic para alternar entre ${item.unidadBase || 'Pieza'} y ${item.unidadSecundaria || 'Paquete'}">
                        ${item.tipoUnidad === 'secundaria' 
                            ? `<i class="bi bi-boxes me-0.5"></i>${item.unidadMedida} (x${Math.round(item.factorConversion)})` 
                            : `<i class="bi bi-tag me-0.5"></i>${item.unidadMedida}`}
                        <i class="bi bi-arrow-left-right opacity-75 ms-1" style="font-size: 0.65rem;"></i>
                   </span>`
                : (item.tipoUnidad === 'secundaria'
                    ? `<span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle fw-bold ms-1" style="font-size: 0.70rem;">
                            <i class="bi bi-boxes me-0.5"></i>${item.unidadMedida} (x${Math.round(item.factorConversion)})
                       </span>`
                    : `<span class="badge bg-light text-secondary border fw-normal ms-1" style="font-size: 0.70rem;">
                            ${item.unidadMedida}
                       </span>`);

            const li = document.createElement('li');
            li.className = 'list-group-item bg-white rounded-3 shadow-sm mb-2 p-2.5 border-0';
            li.innerHTML = `
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="me-2" style="flex: 1 1 auto; min-width: 0;">
                        <span class="badge bg-light text-secondary border font-monospace me-1" style="font-size: 0.72rem; padding: 2px 6px;">${item.codigo}</span>
                        <span class="fw-bold text-dark d-inline" style="font-size: 0.88rem; line-height: 1.35; word-break: break-word;">${item.nombre}</span>
                        ${badgeUnidad}
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
                            <small class="text-muted" style="font-size: 0.75rem;">U: ${monedaSimbolo} ${item.precio.toFixed(2)}</small>
                        </div>
                        <span class="text-success fw-bold" style="font-size: 0.92rem;">${monedaSimbolo} ${subtotalItem.toFixed(2)}</span>
                    </div>
                </div>
            `;
            listaCarrito.appendChild(li);

            // Inputs ocultos para enviar al backend (con unidad y factor para descuento exacto de stock)
            inputsOcultos.innerHTML += `
                <input type="hidden" name="productos[${index}][id]" value="${item.id}">
                <input type="hidden" name="productos[${index}][cantidad]" value="${item.cantidad}">
                <input type="hidden" name="productos[${index}][precio]" value="${item.precio}">
                <input type="hidden" name="productos[${index}][unidad_medida]" value="${item.unidadMedida || ''}">
                <input type="hidden" name="productos[${index}][factor_conversion]" value="${item.factorConversion || 1}">
            `;
        });

        txtSubtotal.textContent = `${monedaSimbolo} ${total.toFixed(2)}`;
        txtTotal.textContent = `${monedaSimbolo} ${total.toFixed(2)}`;
        calcularEquivalente(total);
        inputTotalVenta.value = total;

        actualizarSaldoPendiente();
        validarBotonCobrar();
        asignarEventosCarrito();
        guardarCarritoEnStorage();
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

    // Alternar individualmente la unidad (Pieza <-> Paquete) de un ítem en el carrito
    window.toggleUnidadItemCarrito = function(index) {
        const item = carrito[index];
        if (!item || !item.tieneSecundaria) return;

        const nuevoTipo = (item.tipoUnidad === 'secundaria') ? 'base' : 'secundaria';
        const esSec = (nuevoTipo === 'secundaria');

        item.tipoUnidad = nuevoTipo;
        item.key = `${item.id}_${nuevoTipo}`;
        item.unidadMedida = esSec ? (item.unidadSecundaria || 'Paquete') : (item.unidadBase || 'Pieza');
        item.factorConversion = esSec ? (item.factorSecundaria || 1) : 1;
        item.precioVenta = esSec ? (item.precioVentaSecundario || 0) : (item.precioVentaBase || 0);
        item.precioCliente = esSec ? (item.precioClienteSecundario || item.precioVenta) : (item.precioClienteBase || item.precioVenta);
        item.tienePrecioCliente = esSec ? item.tienePrecioClienteSec : item.tienePrecioClienteBase;

        item.precio = item.esTarifaCliente ? item.precioCliente : item.precioVenta;
        const stockBase = item.stockBase || 0;
        item.stockMaximo = Math.max(1, Math.floor(stockBase / item.factorConversion));
        if (item.cantidad > item.stockMaximo) {
            item.cantidad = item.stockMaximo;
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
                    alert(`¡Atención! Stock máximo disponible: ${stockMax} ${carrito[idx].unidadMedida || 'unidad(es)'}.`);
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
                    alert(`Stock máximo disponible: ${carrito[idx].stockMaximo} ${carrito[idx].unidadMedida || 'unidad(es)'}`);
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

    // ==========================================
    // ACTUALIZAR CATÁLOGO VÍA AJAX (SIN RECARGAR PÁGINA NI PERDER CARRITO)
    // ==========================================
    const btnActualizarPos = document.getElementById('btnActualizarCatalogoPos');
    const iconActualizarPos = document.getElementById('iconActualizarCatalogo');
    const toastActualizarPos = document.getElementById('toastActualizarPos');
    const txtToastActualizarPos = document.getElementById('txtToastActualizarPos');

    function actualizarTablaProductosDOM(productos) {
        if (!tbodyProductos) return;

        if (!productos || productos.length === 0) {
            tbodyProductos.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-box-seam fs-1 d-block mb-2 text-muted"></i>
                        <h6>No hay productos con stock disponible en el sistema.</h6>
                        <p class="small text-muted mb-0">Agrega stock desde el módulo de Inventario o Productos.</p>
                    </td>
                </tr>
            `;
            filasProductos = document.querySelectorAll('.fila-producto');
            filtrarProductos();
            return;
        }

        let html = '';
        productos.forEach(p => {
            const unidadBase = (p.unidad && p.unidad.nombre) ? p.unidad.nombre : 'Pieza';
            const pVentaBase = parseFloat(p.precio_venta) || 0;
            const tienePCBase = (p.precio_cliente !== null && parseFloat(p.precio_cliente) > 0);
            const pClienteBase = tienePCBase ? parseFloat(p.precio_cliente) : pVentaBase;

            const pVentaSecRaw = (p.precio_venta_secundario !== null && p.precio_venta_secundario !== undefined) ? parseFloat(p.precio_venta_secundario) : 0;
            const pClienteSecRaw = (p.precio_cliente_secundario !== null && p.precio_cliente_secundario !== undefined) ? parseFloat(p.precio_cliente_secundario) : 0;
            const factorSec = (p.factor_conversion && parseFloat(p.factor_conversion) > 0) ? parseFloat(p.factor_conversion) : 1;
            const tieneSec = !!(p.id_unidad_secundaria && (pVentaSecRaw > 0 || pClienteSecRaw > 0 || factorSec > 1));
            const unidadSec = (tieneSec && p.unidad_secundaria && p.unidad_secundaria.nombre) ? p.unidad_secundaria.nombre : 'Paquete';
            const pVentaSec = pVentaSecRaw > 0 ? pVentaSecRaw : pClienteSecRaw;
            const pClienteSec = pClienteSecRaw > 0 ? pClienteSecRaw : pVentaSec;
            const tienePCSec = (pClienteSecRaw > 0);

            const stk = parseInt(p.stock) || 0;
            const minStk = parseInt(p.stock_minimo) || 5;
            const umbralBajo = Math.max(minStk * 2, minStk + 5);

            let stockBadge = '';
            if (stk <= 0) {
                stockBadge = `<span class="badge bg-danger text-white border px-2 py-1 fs-6" title="Agotado">0</span>`;
            } else if (stk <= minStk) {
                stockBadge = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-6 fw-bold" title="Stock crítico (Mín: ${minStk})">${stk}</span>`;
            } else if (stk <= umbralBajo) {
                stockBadge = `<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fs-6 fw-bold" title="Por agotarse (Mín: ${minStk})">${stk}</span>`;
            } else {
                stockBadge = `<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-6 fw-bold" title="Stock óptimo">${stk}</span>`;
            }

            const marcaNom = (p.marca && p.marca.nombre) ? p.marca.nombre : 'Genérico';
            const catBadge = (p.categoria && p.categoria.nombre)
                ? `<span class="badge bg-light text-muted border">${p.categoria.nombre}</span>`
                : '';

            const selectorUnidadesHtml = tieneSec
                ? `<div class="mt-1.5 d-flex align-items-center gap-1.5 selector-unidades-row">
                        <span class="text-muted" style="font-size: 0.70rem; font-weight: 600;">Vender por:</span>
                        <div class="btn-group btn-group-sm rounded-pill p-0.5 grupo-unidades-pos" role="group">
                            <button type="button" class="btn btn-xs py-0.5 px-2 rounded-pill fw-bold btn-toggle-unidad active" data-tipo="base" style="font-size: 0.72rem;">
                                <i class="bi bi-tag me-0.5"></i>${unidadBase}
                            </button>
                            <button type="button" class="btn btn-xs py-0.5 px-2 rounded-pill fw-bold btn-toggle-unidad" data-tipo="secundaria" style="font-size: 0.72rem;">
                                <i class="bi bi-boxes me-0.5"></i>${unidadSec} (x${Math.round(factorSec)})
                            </button>
                        </div>
                    </div>`
                : `<div class="mt-0.5">
                        <span class="badge bg-light text-secondary border py-0 px-1.5" style="font-size: 0.68rem;">
                            <i class="bi bi-rulers me-1"></i>${unidadBase}
                        </span>
                    </div>`;

            html += `
                <tr class="fila-producto" 
                    data-id="${p.id}" 
                    data-codigo="${p.codigo || ''}" 
                    data-nombre="${p.nombre || ''}" 
                    data-marca="${marcaNom}"
                    data-categoria-nombre="${(p.categoria && p.categoria.nombre) ? p.categoria.nombre : ''}"
                    data-stock="${stk}" 
                    data-categoria="${p.id_categoria || ''}"
                    data-unidad-base="${unidadBase}"
                    data-precio-venta-base="${pVentaBase}"
                    data-precio-cliente-base="${pClienteBase}"
                    data-tiene-precio-cliente-base="${tienePCBase ? '1' : '0'}"
                    data-tiene-secundaria="${tieneSec ? '1' : '0'}"
                    data-unidad-secundaria="${unidadSec}"
                    data-factor-secundaria="${factorSec}"
                    data-precio-venta-secundario="${pVentaSec}"
                    data-precio-cliente-secundario="${pClienteSec}"
                    data-tiene-precio-cliente-sec="${tienePCSec ? '1' : '0'}"
                    data-unidad-activa="base"
                    data-precio-venta="${pVentaBase}" 
                    data-precio-cliente="${pClienteBase}" 
                    data-tiene-precio-cliente="${tienePCBase ? '1' : '0'}" 
                    data-precio="${pVentaBase}">
                    <td class="ps-4">
                        <span class="badge-code">${p.codigo || 'S/C'}</span>
                    </td>
                    <td>
                        <div class="fw-bold text-dark" style="font-size: 0.98rem;">${p.nombre || ''}</div>
                        <div class="text-muted small d-flex flex-wrap align-items-center gap-1.5 mt-0.5">
                            <span class="me-1"><i class="bi bi-tag me-1"></i>${marcaNom}</span>
                            ${catBadge}
                        </div>
                        ${selectorUnidadesHtml}
                    </td>
                    <td class="text-center">
                        ${stockBadge}
                        <div class="text-muted small mt-0.5 txt-unidad-stock" style="font-size: 0.68rem;">
                            ${unidadBase}
                        </div>
                    </td>
                    <td class="text-end">
                        <div class="box-precio-pos">
                            <div class="linea-precio-venta d-flex align-items-center justify-content-end gap-1">
                                <span class="fw-bold text-dark val-precio-venta" style="font-size: 0.94rem;">
                                    Bs. ${pVentaBase.toFixed(2)}
                                </span>
                                <span class="badge bg-light text-secondary border py-0 px-1" style="font-size: 0.64rem;">Venta</span>
                            </div>
                            <div class="linea-precio-cliente box-pcliente-wrap d-flex align-items-center justify-content-end gap-1 mt-0.5" style="color: #0284c7; ${tienePCBase ? '' : 'display:none !important;'}">
                                <i class="bi bi-tag-fill" style="font-size: 0.65rem;"></i>
                                <span class="fw-bold val-precio-cliente" style="font-size: 0.88rem;">Bs. ${pClienteBase.toFixed(2)}</span>
                                <span class="badge py-0 px-1" style="font-size: 0.64rem; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;">Cliente</span>
                            </div>
                            <div class="linea-precio-cliente-mismo text-muted mt-0.5" style="font-size: 0.68rem; ${tienePCBase ? 'display:none !important;' : ''}" title="Sin precio especial: aplica el mismo precio de venta">
                                <span>Cliente: <em>Mismo</em></span>
                            </div>
                        </div>
                    </td>
                    <td class="text-center pe-4">
                        <button type="button" class="btn btn-sm btn-primary fw-bold px-3 py-1.5 rounded-pill btn-agregar-producto shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i> <span class="lbl-btn-agregar">Agregar</span>
                        </button>
                    </td>
                </tr>
            `;
        });

        tbodyProductos.innerHTML = html;
        filasProductos = document.querySelectorAll('.fila-producto');
        filtrarProductos();
    }

    if (btnActualizarPos) {
        btnActualizarPos.addEventListener('click', async function() {
            try {
                btnActualizarPos.disabled = true;
                if (iconActualizarPos) iconActualizarPos.classList.add('spin-anim');

                const res = await fetch('/pos/productos-ajax');
                if (!res.ok) throw new Error('Error al conectar con el servidor.');
                const data = await res.json();

                if (data && data.success && Array.isArray(data.productos)) {
                    actualizarTablaProductosDOM(data.productos);

                    // Sincronización inteligente de precios con productos ya existentes en el carrito
                    let preciosSincronizados = 0;
                    carrito.forEach(cartItem => {
                        const pDb = data.productos.find(p => String(p.id) === String(cartItem.id));
                        if (pDb) {
                            const esSec = (cartItem.tipoUnidad === 'secundaria');
                            const factor = esSec ? (parseFloat(pDb.factor_conversion) || 1) : 1;
                            const nuevoPV = esSec ? (parseFloat(pDb.precio_venta_secundario) || 0) : (parseFloat(pDb.precio_venta) || 0);
                            const tienePC = esSec
                                ? (pDb.precio_cliente_secundario !== null && parseFloat(pDb.precio_cliente_secundario) > 0)
                                : (pDb.precio_cliente !== null && parseFloat(pDb.precio_cliente) > 0);
                            const nuevoPC = tienePC
                                ? (esSec ? parseFloat(pDb.precio_cliente_secundario) : parseFloat(pDb.precio_cliente))
                                : nuevoPV;

                            const nuevoStockBase = parseInt(pDb.stock) || 0;
                            cartItem.stockMaximo = Math.floor(nuevoStockBase / factor);

                            if (cartItem.precioVenta !== nuevoPV || cartItem.precioCliente !== nuevoPC) {
                                cartItem.precioVenta = nuevoPV;
                                cartItem.precioCliente = nuevoPC;
                                cartItem.tienePrecioCliente = tienePC;
                                cartItem.precio = cartItem.esTarifaCliente ? nuevoPC : nuevoPV;
                                preciosSincronizados++;
                            }
                        }
                    });

                    if (preciosSincronizados > 0) {
                        renderizarCarrito();
                    }

                    // Mostrar notificación de actualización
                    if (toastActualizarPos && txtToastActualizarPos) {
                        if (preciosSincronizados > 0) {
                            txtToastActualizarPos.innerHTML = `<strong>¡Catálogo y carrito sincronizados!</strong> Se actualizaron los precios de ${preciosSincronizados} producto(s) en tu carrito sin perder tu avance.`;
                        } else {
                            txtToastActualizarPos.innerHTML = `<strong>¡Catálogo actualizado!</strong> Se cargaron los precios y existencias más recientes (${data.total} productos con stock). Tu carrito se mantuvo intacto.`;
                        }
                        toastActualizarPos.style.display = 'flex';
                        setTimeout(() => {
                            if (toastActualizarPos) toastActualizarPos.style.display = 'none';
                        }, 6000);
                    }
                }
            } catch (err) {
                console.error('Error al actualizar catálogo POS:', err);
                alert('No se pudo actualizar el catálogo: ' + err.message);
            } finally {
                btnActualizarPos.disabled = false;
                if (iconActualizarPos) iconActualizarPos.classList.remove('spin-anim');
            }
        });
    }

    // Inicializar catálogo y restaurar carrito si estaba guardado en cola
    if (inputCliente && (!inputCliente.value || inputCliente.value.trim() === '')) {
        inputCliente.value = 'Cliente Varios';
    }
    restaurarCarritoDesdeStorage();
    filtrarProductos();
    validarBotonCobrar();
});
</script>


@endsection
