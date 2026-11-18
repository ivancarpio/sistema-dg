@extends('layouts.app')

@section('content')

<style>
    /* Estilos del Módulo de Productos */
    .prod-card {
        border: none !important;
        border-radius: 14px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important;
        background: #ffffff;
    }

    /* Tarjetas de estadísticas superiores */
    .stat-card-prod {
        border-radius: 14px;
        border: none;
        padding: 16px 18px;
        background: #ffffff;
        box-shadow: 0 4px 18px rgba(0,0,0,0.04);
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
        height: 100%;
    }
    .stat-card-prod:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 22px rgba(0,0,0,0.07);
    }
    .stat-content-prod {
        min-width: 0;
        flex: 1;
        overflow: hidden;
    }
    .stat-icon-prod {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .stat-val-prod {
        font-size: 1.45rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.15;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .stat-val-valor {
        font-size: 1.25rem;
    }
    .stat-currency {
        font-size: 0.8em;
        font-weight: 700;
        color: #6366f1;
        margin-right: 2px;
    }
    .stat-lbl-prod {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Encabezado Principal Responsive */
    .header-productos {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        margin-bottom: 1.5rem;
    }
    .header-prod-left {
        display: flex;
        align-items: center;
        min-width: 0;
    }
    .header-prod-actions {
        display: flex;
        gap: 10px;
        margin-left: auto;
        flex-shrink: 0;
    }

    /* Responsive específico para móviles */
    @media (max-width: 767.98px) {
        .header-productos {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }
        .header-prod-left {
            width: 100%;
        }
        .header-prod-actions {
            width: 100%;
            margin-left: 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .header-prod-actions .btn {
            width: 100%;
            justify-content: center;
            padding: 8px 10px !important;
            font-size: 0.88rem;
        }
        .stat-card-prod {
            padding: 12px 10px;
            gap: 10px;
            border-radius: 12px;
        }
        .stat-icon-prod {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            font-size: 1.15rem;
        }
        .stat-val-prod {
            font-size: 1.15rem;
        }
        .stat-val-valor {
            font-size: 0.95rem;
            letter-spacing: -0.3px;
        }
        .stat-lbl-prod {
            font-size: 0.68rem;
            letter-spacing: 0.2px;
            margin-bottom: 2px;
        }
    }

    /* Tabla */
    .table-custom-header th {
        background-color: #f8fafc !important;
        font-size: 0.82rem;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 16px 14px;
        border-bottom: 2px solid #e2e8f0 !important;
    }
    .table-custom tbody tr { 
        border-bottom: 1px solid #f1f5f9; 
        transition: background-color 0.15s; 
    }
    .table-custom tbody tr:hover { 
        background-color: #f8fafc; 
    }
    .table-custom td { 
        vertical-align: middle; 
        padding: 15px 14px; 
    }

    /* Tipografía nítida y visible */
    .prod-nombre {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.2px;
    }
    .prod-codigo {
        font-family: monospace;
        font-size: 0.9rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #1e293b;
        border: 1px solid #e2e8f0;
        display: inline-block;
    }
    .prod-precio {
        font-size: 1.05rem;
        font-weight: 800;
        color: #059669;
    }
    .prod-precio-cliente {
        font-size: 1.02rem;
        font-weight: 800;
        color: #0284c7;
    }
    .badge-precio-cliente {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
        font-size: 0.68rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 9999px;
    }
    .prod-costo {
        font-size: 0.92rem;
        font-weight: 600;
        color: #64748b;
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

    /* Botones de acción circulares */
    .btn-action-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.2s;
        font-size: 1.1rem;
        text-decoration: none;
    }
    .btn-action-edit {
        background: #e0f2fe;
        color: #0284c7;
    }
    .btn-action-edit:hover {
        background: #0284c7;
        color: #ffffff;
        transform: scale(1.08);
    }
    .btn-action-delete {
        background: #fee2e2;
        color: #dc2626;
    }
    .btn-action-delete:hover {
        background: #dc2626;
        color: #ffffff;
        transform: scale(1.08);
    }

    /* Ordenamiento */
    th.sortable { cursor: pointer; user-select: none; transition: color 0.2s; }
    th.sortable:hover { color: #0284c7 !important; }
</style>

<!-- ALERTAS DE ÉXITO O ERROR (Se auto-cierran en 5s) -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-4 text-success me-2"></i>
            <div><strong>{{ session('success') }}</strong></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-4 text-danger me-2"></i>
            <div><strong>{{ $errors->first() }}</strong></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- ENCABEZADO PRINCIPAL -->
<div class="header-productos">
    <div class="header-prod-left">
        <div class="bg-primary bg-opacity-10 p-2 p-md-3 rounded-3 me-3 text-primary flex-shrink-0">
            <i class="bi bi-box-seam-fill fs-2"></i>
        </div>
        <div>
            <h3 class="fw-bold m-0 text-dark">Gestión de Productos</h3>
            <small class="text-muted d-block">Catálogo maestro • Códigos estandarizados, precios y existencias</small>
        </div>
    </div>
    
    <div class="header-prod-actions">
        @auth
        <button class="btn btn-primary fw-bold shadow-sm px-4 py-2" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#modalNuevoProducto">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Producto
        </button>
        <button class="btn btn-outline-dark fw-bold px-3 py-2" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#modalNuevaCategoria">
            <i class="bi bi-folder-plus me-1"></i> Nueva Categoría
        </button>
        @endauth
    </div>
</div>

<!-- ============================================== -->
<!-- STATS CARDS: MÉTRICAS DEL CATÁLOGO             -->
<!-- ============================================== -->
<div class="row g-2 g-md-3 mb-4">
    <!-- Total Productos -->
    <div class="col-6 col-md-3">
        <div class="stat-card-prod">
            <div class="stat-icon-prod" style="background: rgba(2, 132, 199, 0.12); color: #0284c7;">
                <i class="bi bi-boxes"></i>
            </div>
            <div class="stat-content-prod">
                <div class="stat-lbl-prod">Total Catálogo</div>
                <div class="stat-val-prod">{{ $totalProductos }}</div>
                <small class="text-muted d-block text-truncate">artículos registrados</small>
            </div>
        </div>
    </div>

    <!-- Stock Total en Tienda -->
    <div class="col-6 col-md-3">
        <div class="stat-card-prod">
            <div class="stat-icon-prod" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                <i class="bi bi-building-check"></i>
            </div>
            <div class="stat-content-prod">
                <div class="stat-lbl-prod">Stock en Tienda</div>
                <div class="stat-val-prod text-success">{{ number_format($totalStockUnidades, 0) }}</div>
                <small class="text-muted d-block text-truncate">unidades físicas</small>
            </div>
        </div>
    </div>

    <!-- Stock Crítico / Alerta -->
    <div class="col-6 col-md-3">
        <div class="stat-card-prod">
            <div class="stat-icon-prod" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="stat-content-prod">
                <div class="stat-lbl-prod">Stock Crítico</div>
                <div class="stat-val-prod {{ $productosCriticos > 0 ? 'text-danger' : 'text-secondary' }}">
                    {{ $productosCriticos }}
                </div>
                <small class="text-muted d-block text-truncate">
                    @if($productosAlerta > 0)
                        <span class="text-warning-emphasis fw-bold">+{{ $productosAlerta }} por agotarse</span>
                    @else
                        artículos bajo mín.
                    @endif
                </small>
            </div>
        </div>
    </div>

    <!-- Valorización Estimada -->
    <div class="col-6 col-md-3">
        <div class="stat-card-prod">
            <div class="stat-icon-prod" style="background: rgba(99, 102, 241, 0.12); color: #6366f1;">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="stat-content-prod">
                <div class="stat-lbl-prod">Valor en Tienda</div>
                <div class="stat-val-prod stat-val-valor" style="color: #4f46e5;">
                    <span class="stat-currency">Bs.</span>{{ number_format($valorInventario, 2) }}
                </div>
                <small class="text-muted d-block text-truncate">precio de venta</small>
            </div>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- BARRA DE BÚSQUEDA Y FILTROS                    -->
<!-- ============================================== -->
<div class="prod-card p-3 mb-4">
    <div class="row g-3 align-items-center">
        <!-- Buscador -->
        <div class="col-md-5">
            <div class="input-group" style="height: 44px;">
                <span class="search-btn-left"><i class="bi bi-search"></i></span>
                <input type="text" id="buscadorProductos" class="form-control search-input" placeholder="Buscar por producto, código, marca...">
            </div>
        </div>

        <!-- Filtro por Categoría -->
        <div class="col-md-3">
            <select id="filtroCategoria" class="form-select" style="height: 44px; font-size: 0.95rem;">
                <option value="">Todas las categorías</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filtro por Estado de Stock -->
        <div class="col-md-2">
            <select id="filtroStock" class="form-select" style="height: 44px; font-size: 0.90rem;">
                <option value="">Todos los stocks</option>
                <option value="critico">🔴 Críticos (≤ Mínimo)</option>
                <option value="alerta">🟡 Por Agotarse</option>
                <option value="optimo">🟢 Stock Óptimo</option>
                <option value="sin_stock">⚫ Agotados (0)</option>
            </select>
        </div>

        <!-- Botón Actualizar -->
        <div class="col-md-2 d-flex justify-content-end">
            <a href="/productos" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center fw-bold" style="height: 44px; border-radius: 10px;">
                <i class="bi bi-arrow-repeat me-2 fs-5"></i> Actualizar
            </a>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- TABLA PRINCIPAL DE PRODUCTOS                   -->
<!-- ============================================== -->
<div class="prod-card overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-custom align-middle m-0" id="tablaProductos">
            <thead class="table-custom-header">
                <tr>
                    <th class="ps-4 sortable text-start" style="width: 25%;" data-col="0">
                        PRODUCTO <i class="icono-orden bi bi-caret-down-fill text-primary ms-1" style="font-size: 0.8em; opacity: 1;" title="Ordenado alfabéticamente (A - Z)"></i>
                    </th>
                    <th class="sortable text-center" style="width: 12%;" data-col="1">
                        CÓDIGO <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i>
                    </th>
                    <th class="sortable text-center" style="width: 11%;" data-col="2">
                        MARCA <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i>
                    </th>
                    <th class="sortable text-end" style="width: 12%;" data-col="3">
                        PRECIO VENTA <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i>
                    </th>
                    <th class="sortable text-end" style="width: 13%;" data-col="4">
                        PRECIO CLIENTE <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i>
                    </th>
                    <th class="sortable text-end" style="width: 10%;" data-col="5">
                        COSTO <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i>
                    </th>
                    <th class="sortable text-center" style="width: 10%;" data-col="6">
                        STOCK <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i>
                    </th>
                    <th class="text-center pe-4" style="width: 7%;">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $producto)
                @php
                    $stockActual = $producto->stock ?? 0;
                    $stockMinimo = is_numeric($producto->stock_minimo) && $producto->stock_minimo > 0 ? intval($producto->stock_minimo) : 5;
                    $umbralAlerta = max($stockMinimo * 2, $stockMinimo + 5);

                    if ($stockActual <= 0) {
                        $estadoStock = 'sin_stock';
                    } elseif ($stockActual <= $stockMinimo) {
                        $estadoStock = 'critico';
                    } elseif ($stockActual <= $umbralAlerta) {
                        $estadoStock = 'alerta';
                    } else {
                        $estadoStock = 'optimo';
                    }
                @endphp
                <tr class="fila-producto" 
                    id="fila-producto-{{ $producto->id }}"
                    data-id="{{ $producto->id }}"
                    data-categoria="{{ $producto->id_categoria }}"
                    data-estado-stock="{{ $estadoStock }}">
                    
                    <!-- Nombre del Producto -->
                    <td class="ps-4 text-start col-nombre" data-sort-value="{{ strtolower($producto->nombre) }}">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-3">
                                <i class="bi bi-box fs-5"></i>
                            </div>
                            <div>
                                <div class="prod-nombre text-uppercase">{{ $producto->nombre }}</div>
                                <div class="text-muted small sub-info-prod">
                                    @if($producto->categoria)
                                        <span class="badge bg-light text-secondary border me-1">{{ $producto->categoria->nombre }}</span>
                                    @endif
                                    @if($producto->unidad)
                                        <span><i class="bi bi-rulers me-1"></i>{{ $producto->unidad->nombre }}</span>
                                    @endif
                                    @if($producto->unidadSecundaria)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle ms-1" title="Venta dual: {{ $producto->unidadSecundaria->nombre }} con factor {{ number_format($producto->factor_conversion ?? 1, 0) }} {{ $producto->unidad->nombre ?? 'u.' }}">
                                            <i class="bi bi-boxes me-1"></i>{{ $producto->unidadSecundaria->nombre }} (x{{ number_format($producto->factor_conversion ?? 1, 0) }})
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>

                    <!-- Código Estandarizado -->
                    <td class="text-center col-codigo" data-sort-value="{{ $producto->codigo }}">
                        <span class="prod-codigo">{{ $producto->codigo }}</span>
                    </td>
                    
                    <!-- Marca -->
                    <td class="text-center col-marca" data-sort-value="{{ strtolower(optional($producto->marca)->nombre ?? '') }}">
                        @if($producto->marca)
                            <span class="badge bg-light text-dark border px-2 py-1 fw-bold">
                                {{ $producto->marca->nombre }}
                            </span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    
                    <!-- Precio de Venta (Ocasional) -->
                    <td class="text-end col-precio-venta" data-sort-value="{{ $producto->precio_venta ?? 0 }}">
                        <div class="prod-precio">
                            Bs. {{ number_format($producto->precio_venta ?? 0, 2) }}
                        </div>
                        <small class="text-muted d-block" style="font-size: 0.70rem;">Ocasional ({{ $producto->unidad->nombre ?? 'Base' }})</small>
                        @if($producto->precio_venta_secundario)
                            <div class="mt-1 pt-1 border-top border-light-subtle" title="Precio por {{ $producto->unidadSecundaria->nombre ?? 'Empaque' }}">
                                <span class="fw-bold text-primary" style="font-size: 0.82rem;">Bs. {{ number_format($producto->precio_venta_secundario, 2) }}</span>
                                <small class="text-muted d-block" style="font-size: 0.65rem;">/ {{ $producto->unidadSecundaria->nombre ?? 'Empaque' }}</small>
                            </div>
                        @endif
                    </td>

                    <!-- Precio Cliente (Regular / Ferretería) -->
                    <td class="text-end col-precio-cliente" data-sort-value="{{ $producto->precio_cliente ?? ($producto->precio_venta ?? 0) }}">
                        @if($producto->precio_cliente !== null && $producto->precio_cliente > 0)
                            <div class="prod-precio-cliente">
                                Bs. {{ number_format($producto->precio_cliente, 2) }}
                            </div>
                            <span class="badge-precio-cliente d-inline-flex align-items-center gap-1">
                                <i class="bi bi-tag-fill" style="font-size: 0.62rem;"></i> Regular
                            </span>
                        @else
                            <div class="text-muted small fw-semibold" title="Sin precio especial: aplica precio venta">
                                <span>—</span>
                                <small class="d-block text-muted" style="font-size: 0.68rem;">P. Venta</small>
                            </div>
                        @endif
                        @if($producto->precio_cliente_secundario)
                            <div class="mt-1 pt-1 border-top border-light-subtle" title="Precio cliente por {{ $producto->unidadSecundaria->nombre ?? 'Empaque' }}">
                                <span class="fw-bold" style="font-size: 0.82rem; color: #0284c7;">Bs. {{ number_format($producto->precio_cliente_secundario, 2) }}</span>
                                <small class="text-muted d-block" style="font-size: 0.65rem;">/ {{ $producto->unidadSecundaria->nombre ?? 'Empaque' }} (Reg)</small>
                            </div>
                        @endif
                    </td>

                    <!-- Costo -->
                    <td class="text-end col-costo" data-sort-value="{{ $producto->costo ?? 0 }}">
                        <div class="prod-costo">
                            Bs. {{ number_format($producto->costo ?? 0, 2) }}
                        </div>
                    </td>

                    <!-- Stock con semáforo inteligente claro -->
                    <td class="text-center col-stock" data-sort-value="{{ $stockActual }}">
                        @if($stockActual <= 0)
                            <span class="badge bg-danger text-white border border-danger px-3 py-2 fs-6 fw-bold">
                                0 UND.
                            </span>
                            <div class="text-danger small fw-bold mt-1" style="font-size: 0.72rem;">Agotado</div>
                        @elseif($stockActual <= $stockMinimo)
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 fs-6 fw-bold">
                                {{ $stockActual }} UND.
                            </span>
                            <div class="text-danger small fw-bold mt-1" style="font-size: 0.72rem;">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>¡Crítico! Mín: {{ $stockMinimo }}
                            </div>
                        @elseif($stockActual <= $umbralAlerta)
                            <span class="badge bg-warning-subtle text-dark border border-warning px-3 py-2 fs-6 fw-bold" style="background-color: #fef3c7 !important; color: #92400e !important;">
                                {{ $stockActual }} UND.
                            </span>
                            <div class="small fw-bold mt-1" style="font-size: 0.72rem; color: #b45309;">
                                <i class="bi bi-clock-history me-1"></i>Por agotarse (Mín: {{ $stockMinimo }})
                            </div>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6 fw-bold">
                                {{ $stockActual }} UND.
                            </span>
                            <div class="text-success small fw-semibold mt-1" style="font-size: 0.72rem;">
                                Óptimo
                            </div>
                        @endif
                    </td>
                    
                    <!-- Acciones: Editar y Eliminar -->
                    <td class="text-center pe-4">
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" 
                                    class="btn-action-icon btn-action-edit btn-abrir-editar-prod" 
                                    data-id="{{ $producto->id }}"
                                    data-nombre="{{ $producto->nombre }}"
                                    data-codigo="{{ $producto->codigo }}"
                                    data-descripcion="{{ $producto->descripcion }}"
                                    data-precio-venta="{{ $producto->precio_venta }}"
                                    data-precio-cliente="{{ $producto->precio_cliente }}"
                                    data-costo="{{ $producto->costo }}"
                                    data-id-categoria="{{ $producto->id_categoria }}"
                                    data-id-marca="{{ $producto->id_marca }}"
                                    data-id-unidad="{{ $producto->id_unidad }}"
                                    data-id-unidad-sec="{{ $producto->id_unidad_secundaria }}"
                                    data-factor-conversion="{{ $producto->factor_conversion ? number_format($producto->factor_conversion, 0, '', '') : '' }}"
                                    data-precio-venta-sec="{{ $producto->precio_venta_secundario }}"
                                    data-precio-cliente-sec="{{ $producto->precio_cliente_secundario }}"
                                    data-stock-minimo="{{ $producto->stock_minimo }}"
                                    title="Editar producto">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button type="button" 
                                    class="btn-action-icon btn-action-delete btn-abrir-eliminar-prod" 
                                    data-id="{{ $producto->id }}"
                                    data-nombre="{{ $producto->nombre }}"
                                    title="Eliminar producto">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-box-seam fs-1 d-block mb-2 text-muted"></i>
                        <h5>No hay productos registrados en el catálogo.</h5>
                        <p class="small text-muted">Presiona "+ Nuevo Producto" para registrar el primero.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pie de tabla con contador -->
    <div class="p-3 bg-light border-top text-muted small d-flex justify-content-between align-items-center">
        <span>Mostrando <strong id="contadorVisibles">{{ count($productos) }}</strong> de <strong>{{ count($productos) }}</strong> productos</span>
        <span><i class="bi bi-info-circle me-1"></i> Haz clic en los títulos de columna para ordenar</span>
    </div>
</div>

<!-- ============================================== -->
<!-- ZONA DE MODALES                                -->
<!-- ============================================== -->
@auth

    <!-- MODAL: NUEVA CATEGORÍA -->
    <div class="modal fade" id="modalNuevaCategoria" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <form action="/categorias" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-body p-4">
                    <h5 class="fw-bold mb-3 text-dark text-center">Nueva Categoría</h5>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nombre de Categoría *</label>
                        <input type="text" name="nombre" id="inputNombreCategoria" class="form-control fw-bold" placeholder="Ej. ACCESORIOS PVC" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">Descripción (opcional)</label>
                        <textarea name="descripcion" class="form-control" rows="2" placeholder="Detalles de la categoría..."></textarea>
                    </div>
                    <div class="d-flex justify-content-between gap-2">
                        <button type="button" class="btn btn-light border w-50 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary w-50 fw-bold">Crear</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: NUEVO PRODUCTO (Con código estándar automático) -->
    <div class="modal fade" id="modalNuevoProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <form action="/productos" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="fw-bold m-0 text-dark"><i class="bi bi-box-seam me-2 text-primary"></i>Registrar Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">Nombre del Producto *</label>
                        <input type="text" name="nombre" id="nuevoNombreProducto" class="form-control form-control-lg fw-bold" placeholder="Ej. CODO ROSCA 1 KRONA" required>
                        <small class="text-muted">Escribe el nombre completo (incluyendo medidas o especificaciones).</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">
                            Código del Producto * 
                            <span class="badge bg-primary-subtle text-primary ms-1 fw-bold">Estandarizado</span>
                        </label>
                        <div class="input-group">
                            <input type="text" name="codigo" id="nuevoCodigoProducto" class="form-control fw-bold fs-6 font-monospace" placeholder="Ej. COD-R-1" required>
                            <button class="btn btn-primary fw-bold px-3 btn-generar-codigo" type="button" data-nombre-id="nuevoNombreProducto" data-codigo-id="nuevoCodigoProducto" title="Generar código estándar automáticamente">
                                <i class="bi bi-magic me-1"></i> Generar Código
                            </button>
                        </div>
                        <small class="text-muted">Se autogenera según el nombre ferretero estándar al hacer clic en Generar.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Descripción (Opcional)</label>
                        <textarea name="descripcion" class="form-control" rows="2" placeholder="Especificaciones adicionales..."></textarea>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label text-dark fw-bold">Precio Venta (Bs.) *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-success">Bs.</span>
                                <input type="number" name="precio_venta" step="0.01" class="form-control fw-bold text-success fs-5" placeholder="0.00" required>
                            </div>
                            <small class="text-muted" style="font-size: 0.74rem;">Clientes ocasionales.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-dark fw-bold">
                                Precio Cliente (Bs.)
                                <span class="badge bg-light text-secondary border fw-normal" style="font-size: 0.70rem;">Opcional</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold" style="color: #0284c7;">Bs.</span>
                                <input type="number" name="precio_cliente" step="0.01" class="form-control fw-bold fs-5" style="color: #0284c7;" placeholder="0.00">
                            </div>
                            <small class="text-muted" style="font-size: 0.74rem;">Ferreterías / regulares.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-dark fw-bold">Costo Unitario (Bs.) *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">Bs.</span>
                                <input type="number" name="costo" step="0.01" class="form-control fw-bold fs-5" placeholder="0.00" value="0.00" required>
                            </div>
                            <small class="text-muted" style="font-size: 0.74rem;">Costo de adquisición.</small>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Categoría</label>
                            <select name="id_categoria" class="form-select">
                                <option value="">Seleccionar...</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Marca</label>
                            <select name="id_marca" class="form-select">
                                <option value="">Seleccionar...</option>
                                @foreach($marcas as $marca)
                                    <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Unidad de Medida (Principal) *</label>
                            <select name="id_unidad" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                @foreach($unidades as $unidad)
                                    <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- SECCIÓN: UNIDAD SECUNDARIA / EMPAQUE / VENTA POR MAYOR (OPCIONAL) -->
                    <div class="card border border-primary-subtle bg-light bg-opacity-50 rounded-3 mb-3 p-3 shadow-none">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-2 me-2.5">
                                    <i class="bi bi-boxes fs-5"></i>
                                </div>
                                <div>
                                    <span class="fw-bold text-dark" style="font-size: 0.92rem;">Segunda Unidad de Medida / Venta por Mayor</span>
                                    <span class="badge bg-light text-secondary border ms-1" style="font-size: 0.68rem;">Opcional</span>
                                    <div class="text-muted" style="font-size: 0.75rem;">Para productos con 2 formas de venta (ej. Pieza y Caja, Pieza y Paquete, etc.)</div>
                                </div>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" id="checkHabilitarUnidadSecundaria" onchange="toggleSeccionUnidadSecundaria(this, 'cajaUnidadSecundariaNuevo')" style="cursor: pointer; width: 2.2rem; height: 1.2rem;">
                            </div>
                        </div>

                        <div id="cajaUnidadSecundariaNuevo" style="display: none;" class="pt-3 mt-2 border-top">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-dark fw-bold small">Unidad Secundaria (Empaque / Paquete / Caja)</label>
                                    <select name="id_unidad_secundaria" class="form-select">
                                        <option value="">Seleccionar unidad (Caja, Paquete, Rollo...)</option>
                                        @foreach($unidades as $unidad)
                                            <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted" style="font-size: 0.72rem;">Ej. Paquete, Caja, Rollo.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-dark fw-bold small">Contenido (Unidades Base por Empaque)</label>
                                    <div class="input-group">
                                        <input type="number" name="factor_conversion" step="1" min="1" class="form-control fw-bold" placeholder="Ej. 50">
                                        <span class="input-group-text bg-white small text-muted">unidades</span>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.72rem;">¿Cuántas piezas/unidades base contiene 1 empaque?</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-dark fw-bold small">Precio Venta por Empaque (Bs.)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light fw-bold text-success">Bs.</span>
                                        <input type="number" name="precio_venta_secundario" step="0.01" min="0" class="form-control fw-bold text-success fs-5" placeholder="0.00">
                                    </div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Precio de venta al público para clientes ocasionales.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-dark fw-bold small">Precio Cliente por Empaque (Bs.)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light fw-bold" style="color: #0284c7;">Bs.</span>
                                        <input type="number" name="precio_cliente_secundario" step="0.01" min="0" class="form-control fw-bold fs-5" style="color: #0284c7;" placeholder="0.00">
                                    </div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Precio preferencial para ferreterías / clientes regulares.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-bold">Stock Inicial *</label>
                            <input type="number" name="stock_inicial" class="form-control fw-bold fs-5" value="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-bold">Stock Mínimo (Alerta)</label>
                            <input type="number" name="stock_minimo" class="form-control fw-bold fs-5" value="5">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-light border px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-5">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDITAR DINÁMICO (ÚNICO Y ULTRA-LIVIANO) -->
    <div class="modal fade" id="modalEditarProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <form id="formEditarProducto" action="" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf @method('PUT')
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="fw-bold m-0 text-dark"><i class="bi bi-pencil-square me-2 text-primary"></i>Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">Nombre del Producto *</label>
                        <input type="text" name="nombre" id="editNombre" class="form-control form-control-lg fw-bold" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">Código *</label>
                        <div class="input-group">
                            <input type="text" name="codigo" id="editCodigo" class="form-control fw-bold fs-6 font-monospace" required>
                            <button class="btn btn-outline-primary fw-bold btn-generar-codigo" type="button" 
                                    data-nombre-id="editNombre" 
                                    data-codigo-id="editCodigo" 
                                    id="btnRegenerarCodigoEdit"
                                    title="Regenerar código estándar">
                                <i class="bi bi-magic me-1"></i> Generar
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Descripción</label>
                        <textarea name="descripcion" id="editDescripcion" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label text-dark fw-bold">Precio Venta (Bs.) *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-success">Bs.</span>
                                <input type="number" name="precio_venta" id="editPrecioVenta" step="0.01" class="form-control fw-bold text-success fs-5" required>
                            </div>
                            <small class="text-muted" style="font-size: 0.74rem;">Clientes ocasionales.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-dark fw-bold">
                                Precio Cliente (Bs.)
                                <span class="badge bg-light text-secondary border fw-normal" style="font-size: 0.70rem;">Opcional</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold" style="color: #0284c7;">Bs.</span>
                                <input type="number" name="precio_cliente" id="editPrecioCliente" step="0.01" class="form-control fw-bold fs-5" style="color: #0284c7;">
                            </div>
                            <small class="text-muted" style="font-size: 0.74rem;">Ferreterías / regulares.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-dark fw-bold">Costo (Bs.) *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">Bs.</span>
                                <input type="number" name="costo" id="editCosto" step="0.01" class="form-control fw-bold fs-5" required>
                            </div>
                            <small class="text-muted" style="font-size: 0.74rem;">Costo de adquisición.</small>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Categoría</label>
                            <select name="id_categoria" id="editCategoria" class="form-select">
                                <option value="">Seleccionar...</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Marca</label>
                            <select name="id_marca" id="editMarca" class="form-select">
                                <option value="">Seleccionar...</option>
                                @foreach($marcas as $marca)
                                    <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Unidad de Medida (Principal)</label>
                            <select name="id_unidad" id="editUnidad" class="form-select">
                                <option value="">Seleccionar...</option>
                                @foreach($unidades as $unidad)
                                    <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- SECCIÓN: UNIDAD SECUNDARIA / EMPAQUE / VENTA POR MAYOR (OPCIONAL) -->
                    <div class="card border border-primary-subtle bg-light bg-opacity-50 rounded-3 mb-3 p-3 shadow-none">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-2 me-2.5">
                                    <i class="bi bi-boxes fs-5"></i>
                                </div>
                                <div>
                                    <span class="fw-bold text-dark" style="font-size: 0.92rem;">Segunda Unidad de Medida / Venta por Mayor</span>
                                    <span class="badge bg-light text-secondary border ms-1" style="font-size: 0.68rem;">Opcional</span>
                                    <div class="text-muted" style="font-size: 0.75rem;">Para productos con 2 formas de venta (ej. Pieza y Caja, Pieza y Paquete, etc.)</div>
                                </div>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" id="checkHabilitarSecEdit" onchange="toggleSeccionUnidadSecundaria(this, 'cajaUnidadSecundariaEdit')" style="cursor: pointer; width: 2.2rem; height: 1.2rem;">
                            </div>
                        </div>

                        <div id="cajaUnidadSecundariaEdit" style="display: none;" class="pt-3 mt-2 border-top">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-dark fw-bold small">Unidad Secundaria (Empaque / Paquete / Caja)</label>
                                    <select name="id_unidad_secundaria" id="editUnidadSec" class="form-select">
                                        <option value="">Seleccionar unidad (Caja, Paquete, Rollo...)</option>
                                        @foreach($unidades as $unidad)
                                            <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted" style="font-size: 0.72rem;">Ej. Paquete, Caja, Rollo.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-dark fw-bold small">Contenido (Unidades Base por Empaque)</label>
                                    <div class="input-group">
                                        <input type="number" name="factor_conversion" id="editFactorConversion" step="1" min="1" class="form-control fw-bold" placeholder="Ej. 50">
                                        <span class="input-group-text bg-white small text-muted">unidades</span>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.72rem;">¿Cuántas piezas/unidades base contiene 1 empaque?</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-dark fw-bold small">Precio Venta por Empaque (Bs.)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light fw-bold text-success">Bs.</span>
                                        <input type="number" name="precio_venta_secundario" id="editPrecioVentaSec" step="0.01" min="0" class="form-control fw-bold text-success fs-5" placeholder="0.00">
                                    </div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Precio de venta al público para clientes ocasionales.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-dark fw-bold small">Precio Cliente por Empaque (Bs.)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light fw-bold" style="color: #0284c7;">Bs.</span>
                                        <input type="number" name="precio_cliente_secundario" id="editPrecioClienteSec" step="0.01" min="0" class="form-control fw-bold fs-5" style="color: #0284c7;" placeholder="0.00">
                                    </div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Precio preferencial para ferreterías / clientes regulares.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label text-dark fw-bold">Stock Mínimo (Alerta)</label>
                        <input type="number" name="stock_minimo" id="editStockMinimo" class="form-control fw-bold fs-5">
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-light border px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-5">Actualizar Producto</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL ELIMINAR DINÁMICO (ÚNICO Y ULTRA-LIVIANO) -->
    <div class="modal fade" id="modalEliminarProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-body p-4 text-center">
                    <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle d-inline-flex mb-3">
                        <i class="bi bi-trash3 fs-1"></i>
                    </div>
                    <h5 class="fw-bold mb-2">¿Eliminar Producto?</h5>
                    <p class="text-muted small mb-4">Esta acción no se puede deshacer. Se eliminará <strong id="nombreProductoEliminar">este producto</strong> del catálogo.</p>
                    <form id="formEliminarProducto" action="" method="POST" class="d-flex gap-2">
                        @csrf @method('DELETE')
                        <button type="button" class="btn btn-light border w-50 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger w-50 fw-bold">Sí, Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
@endauth

<!-- ============================================== -->
<!-- JAVASCRIPT DEL CATÁLOGO DE PRODUCTOS (ULTRA-RÁPIDO) -->
<!-- ============================================== -->
<script>
window.toggleSeccionUnidadSecundaria = function(checkbox, targetId) {
    const contenedor = document.getElementById(targetId);
    if (!contenedor) return;
    if (checkbox.checked) {
        contenedor.style.display = 'block';
    } else {
        contenedor.style.display = 'none';
        // Limpiar campos si se apaga el switch
        contenedor.querySelectorAll('input, select').forEach(el => {
            el.value = '';
        });
    }
};

document.addEventListener('DOMContentLoaded', function() {
    // Delegación de eventos para Abrir Modal Editar y Eliminar Dinámico
    const modalEditarEl = document.getElementById('modalEditarProducto');
    const modalEditar = modalEditarEl ? new bootstrap.Modal(modalEditarEl) : null;
    const formEditar = document.getElementById('formEditarProducto');

    const modalEliminarEl = document.getElementById('modalEliminarProducto');
    const modalEliminar = modalEliminarEl ? new bootstrap.Modal(modalEliminarEl) : null;
    const formEliminar = document.getElementById('formEliminarProducto');
    const txtEliminarNombre = document.getElementById('nombreProductoEliminar');

    document.addEventListener('click', function(e) {
        // Clic en Editar
        const btnEdit = e.target.closest('.btn-abrir-editar-prod');
        if (btnEdit && formEditar) {
            const id = btnEdit.dataset.id;
            formEditar.action = '/productos/' + id;

            document.getElementById('editNombre').value = btnEdit.dataset.nombre || '';
            document.getElementById('editCodigo').value = btnEdit.dataset.codigo || '';
            document.getElementById('editDescripcion').value = btnEdit.dataset.descripcion || '';
            document.getElementById('editPrecioVenta').value = btnEdit.dataset.precioVenta || '';
            document.getElementById('editPrecioCliente').value = btnEdit.dataset.precioCliente || '';
            document.getElementById('editCosto').value = btnEdit.dataset.costo || '0.00';
            document.getElementById('editCategoria').value = btnEdit.dataset.idCategoria || '';
            document.getElementById('editMarca').value = btnEdit.dataset.idMarca || '';
            document.getElementById('editUnidad').value = btnEdit.dataset.idUnidad || '';
            document.getElementById('editStockMinimo').value = btnEdit.dataset.stockMinimo || '5';

            const btnRegen = document.getElementById('btnRegenerarCodigoEdit');
            if (btnRegen) {
                btnRegen.dataset.productoId = id;
            }

            // Datos de unidad secundaria
            const idSec = btnEdit.dataset.idUnidadSec;
            const factorConv = parseFloat(btnEdit.dataset.factorConversion) || 1;
            const pVentaSec = btnEdit.dataset.precioVentaSec;
            const pClienteSec = btnEdit.dataset.precioClienteSec;
            const tieneSec = !!(idSec && idSec !== '' && idSec !== 'null' && (
                (pVentaSec && parseFloat(pVentaSec) > 0) ||
                (pClienteSec && parseFloat(pClienteSec) > 0) ||
                (factorConv > 1)
            ));

            const checkSec = document.getElementById('checkHabilitarSecEdit');
            const cajaSec = document.getElementById('cajaUnidadSecundariaEdit');

            if (checkSec && cajaSec) {
                checkSec.checked = tieneSec;
                cajaSec.style.display = tieneSec ? 'block' : 'none';
                document.getElementById('editUnidadSec').value = idSec || '';
                document.getElementById('editFactorConversion').value = btnEdit.dataset.factorConversion || '1';
                document.getElementById('editPrecioVentaSec').value = (pVentaSec && parseFloat(pVentaSec) > 0) ? pVentaSec : '';
                document.getElementById('editPrecioClienteSec').value = (pClienteSec && parseFloat(pClienteSec) > 0) ? pClienteSec : '';
            }

            if (modalEditar) modalEditar.show();
            return;
        }

        // Clic en Eliminar
        const btnDelete = e.target.closest('.btn-abrir-eliminar-prod');
        if (btnDelete && formEliminar) {
            const id = btnDelete.dataset.id;
            const nombre = btnDelete.dataset.nombre || 'este producto';
            formEliminar.action = '/productos/' + id;
            if (txtEliminarNombre) txtEliminarNombre.textContent = `"${nombre}"`;
            if (modalEliminar) modalEliminar.show();
            return;
        }
    });

    const tabla = document.getElementById('tablaProductos');
    const tbody = tabla ? tabla.querySelector('tbody') : null;
    const buscador = document.getElementById('buscadorProductos');
    const filtroCat = document.getElementById('filtroCategoria');
    const filtroStock = document.getElementById('filtroStock');
    const contador = document.getElementById('contadorVisibles');

    if (!tabla || !tbody) return;

    // 1. Pre-procesar filas en memoria UNA SOLA VEZ para que la búsqueda y ordenación sean instantáneas (0 lag)
    const domFilas = Array.from(tbody.querySelectorAll('tr.fila-producto'));
    const cacheFilas = domFilas.map(tr => {
        const celdas = Array.from(tr.children);
        const sortValues = celdas.map(td => {
            const raw = td.getAttribute('data-sort-value') ?? '';
            const n = Number(raw);
            const isNum = raw !== '' && !isNaN(n);
            return isNum ? n : raw.toLowerCase();
        });

        return {
            tr: tr,
            texto: (tr.innerText || tr.textContent).toLowerCase(),
            categoria: tr.getAttribute('data-categoria') || '',
            estadoStock: tr.getAttribute('data-estado-stock') || '',
            sortValues: sortValues
        };
    });

    function escapeHtml(str) {
        if (!str) return '';
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    function formatMoneda(val) {
        const num = parseFloat(val) || 0;
        return 'Bs. ' + num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatEntero(val) {
        const num = Math.round(parseFloat(val) || 1);
        return num.toLocaleString('en-US');
    }

    // Actualización visual y en memoria en tiempo real (0 recarga de página)
    function actualizarFilaEnTabla(p) {
        const tr = document.getElementById('fila-producto-' + p.id);
        if (!tr) return;

        // 1. Atributos de fila
        tr.setAttribute('data-categoria', p.id_categoria || '');

        // 2. Columna Nombre (Col 0)
        const tdNombre = tr.querySelector('.col-nombre');
        if (tdNombre) {
            tdNombre.setAttribute('data-sort-value', (p.nombre || '').toLowerCase());
            const elNombre = tdNombre.querySelector('.prod-nombre');
            if (elNombre) elNombre.textContent = p.nombre;

            const subInfo = tdNombre.querySelector('.sub-info-prod');
            if (subInfo) {
                let htmlBadges = '';
                if (p.categoria_nombre) {
                    htmlBadges += `<span class="badge bg-light text-secondary border me-1">${escapeHtml(p.categoria_nombre)}</span>`;
                }
                if (p.unidad_nombre) {
                    htmlBadges += `<span><i class="bi bi-rulers me-1"></i>${escapeHtml(p.unidad_nombre)}</span>`;
                }
                if (p.id_unidad_secundaria && p.unidad_secundaria_nombre) {
                    const factor = formatEntero(p.factor_conversion);
                    htmlBadges += `
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle ms-1" title="Venta dual: ${escapeHtml(p.unidad_secundaria_nombre)} con factor ${factor} ${escapeHtml(p.unidad_nombre || 'u.')}">
                            <i class="bi bi-boxes me-1"></i>${escapeHtml(p.unidad_secundaria_nombre)} (x${factor})
                        </span>`;
                }
                subInfo.innerHTML = htmlBadges;
            }
        }

        // 3. Columna Código (Col 1)
        const tdCodigo = tr.querySelector('.col-codigo');
        if (tdCodigo) {
            tdCodigo.setAttribute('data-sort-value', p.codigo || '');
            const elCod = tdCodigo.querySelector('.prod-codigo');
            if (elCod) elCod.textContent = p.codigo || '';
        }

        // 4. Columna Marca (Col 2)
        const tdMarca = tr.querySelector('.col-marca');
        if (tdMarca) {
            tdMarca.setAttribute('data-sort-value', (p.marca_nombre || '').toLowerCase());
            if (p.marca_nombre) {
                tdMarca.innerHTML = `<span class="badge bg-light text-dark border px-2 py-1 fw-bold">${escapeHtml(p.marca_nombre)}</span>`;
            } else {
                tdMarca.innerHTML = `<span class="text-muted small">—</span>`;
            }
        }

        // 5. Columna Precio Venta (Col 3)
        const tdPrecioVenta = tr.querySelector('.col-precio-venta');
        if (tdPrecioVenta) {
            tdPrecioVenta.setAttribute('data-sort-value', p.precio_venta || 0);
            let htmlPV = `
                <div class="prod-precio">${formatMoneda(p.precio_venta)}</div>
                <small class="text-muted d-block" style="font-size: 0.70rem;">Ocasional (${escapeHtml(p.unidad_nombre || 'Base')})</small>
            `;
            if (p.precio_venta_secundario && p.precio_venta_secundario > 0) {
                htmlPV += `
                    <div class="mt-1 pt-1 border-top border-light-subtle" title="Precio por ${escapeHtml(p.unidad_secundaria_nombre || 'Empaque')}">
                        <span class="fw-bold text-primary" style="font-size: 0.82rem;">${formatMoneda(p.precio_venta_secundario)}</span>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">/ ${escapeHtml(p.unidad_secundaria_nombre || 'Empaque')}</small>
                    </div>
                `;
            }
            tdPrecioVenta.innerHTML = htmlPV;
        }

        // 6. Columna Precio Cliente (Col 4)
        const tdPrecioCliente = tr.querySelector('.col-precio-cliente');
        if (tdPrecioCliente) {
            const valSort = (p.precio_cliente !== null && p.precio_cliente > 0) ? p.precio_cliente : p.precio_venta;
            tdPrecioCliente.setAttribute('data-sort-value', valSort);
            let htmlPC = '';
            if (p.precio_cliente !== null && p.precio_cliente > 0) {
                htmlPC += `
                    <div class="prod-precio-cliente">${formatMoneda(p.precio_cliente)}</div>
                    <span class="badge-precio-cliente d-inline-flex align-items-center gap-1">
                        <i class="bi bi-tag-fill" style="font-size: 0.62rem;"></i> Regular
                    </span>
                `;
            } else {
                htmlPC += `
                    <div class="text-muted small fw-semibold" title="Sin precio especial: aplica precio venta">
                        <span>—</span>
                        <small class="d-block text-muted" style="font-size: 0.68rem;">P. Venta</small>
                    </div>
                `;
            }
            if (p.precio_cliente_secundario && p.precio_cliente_secundario > 0) {
                htmlPC += `
                    <div class="mt-1 pt-1 border-top border-light-subtle" title="Precio cliente por ${escapeHtml(p.unidad_secundaria_nombre || 'Empaque')}">
                        <span class="fw-bold" style="font-size: 0.82rem; color: #0284c7;">${formatMoneda(p.precio_cliente_secundario)}</span>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">/ ${escapeHtml(p.unidad_secundaria_nombre || 'Empaque')} (Reg)</small>
                    </div>
                `;
            }
            tdPrecioCliente.innerHTML = htmlPC;
        }

        // 7. Columna Costo (Col 5)
        const tdCosto = tr.querySelector('.col-costo');
        if (tdCosto) {
            tdCosto.setAttribute('data-sort-value', p.costo || 0);
            tdCosto.innerHTML = `<div class="prod-costo">${formatMoneda(p.costo)}</div>`;
        }

        // 8. Actualizar atributos en botón Editar
        const btnEdit = tr.querySelector('.btn-abrir-editar-prod');
        if (btnEdit) {
            btnEdit.dataset.nombre = p.nombre || '';
            btnEdit.dataset.codigo = p.codigo || '';
            btnEdit.dataset.descripcion = p.descripcion || '';
            btnEdit.dataset.precioVenta = p.precio_venta || '';
            btnEdit.dataset.precioCliente = (p.precio_cliente !== null) ? p.precio_cliente : '';
            btnEdit.dataset.costo = p.costo || '0.00';
            btnEdit.dataset.idCategoria = p.id_categoria || '';
            btnEdit.dataset.idMarca = p.id_marca || '';
            btnEdit.dataset.idUnidad = p.id_unidad || '';
            btnEdit.dataset.idUnidadSec = p.id_unidad_secundaria || '';
            btnEdit.dataset.factorConversion = p.factor_conversion ? formatEntero(p.factor_conversion) : '1';
            btnEdit.dataset.precioVentaSec = (p.precio_venta_secundario !== null) ? p.precio_venta_secundario : '';
            btnEdit.dataset.precioClienteSec = (p.precio_cliente_secundario !== null) ? p.precio_cliente_secundario : '';
            btnEdit.dataset.stockMinimo = p.stock_minimo || '5';
        }

        // Botón Eliminar
        const btnDel = tr.querySelector('.btn-abrir-eliminar-prod');
        if (btnDel) {
            btnDel.dataset.nombre = p.nombre || '';
        }

        // 9. Actualizar caché en memoria para que búsqueda y orden sigan funcionando al 100%
        const idxCache = cacheFilas.findIndex(item => item.tr === tr);
        if (idxCache !== -1) {
            cacheFilas[idxCache].texto = (tr.innerText || tr.textContent).toLowerCase();
            cacheFilas[idxCache].categoria = String(p.id_categoria || '');
            const celdas = Array.from(tr.children);
            cacheFilas[idxCache].sortValues = celdas.map(td => {
                const raw = td.getAttribute('data-sort-value') ?? '';
                const n = Number(raw);
                const isNum = raw !== '' && !isNaN(n);
                return isNum ? n : raw.toLowerCase();
            });
        }

        // Destello visual sutil de confirmación
        tr.style.transition = 'background-color 0.4s ease';
        tr.style.backgroundColor = '#ecfdf5';
        setTimeout(() => {
            tr.style.backgroundColor = '';
        }, 1200);
    }

    // Interceptar guardado de edición vía AJAX (0 recargas, ultra-rápido)
    if (formEditar) {
        formEditar.addEventListener('submit', async function(e) {
            e.preventDefault();

            const btnSubmit = formEditar.querySelector('button[type="submit"]');
            const originalText = btnSubmit ? btnSubmit.innerHTML : 'Actualizar Producto';
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Guardando...';
            }

            try {
                const formData = new FormData(formEditar);
                const response = await fetch(formEditar.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    if (modalEditar) modalEditar.hide();
                    if (data.producto) {
                        actualizarFilaEnTabla(data.producto);
                    }
                    if (typeof window.mostrarAlertaFlotante === 'function') {
                        window.mostrarAlertaFlotante(data.message || '¡Producto actualizado correctamente!', 'success', 5000);
                    }
                } else {
                    let errMsg = data.message || 'Error al actualizar el producto.';
                    if (data.errors) {
                        const firstKey = Object.keys(data.errors)[0];
                        if (firstKey && data.errors[firstKey].length > 0) {
                            errMsg = data.errors[firstKey][0];
                        }
                    }
                    if (typeof window.mostrarAlertaFlotante === 'function') {
                        window.mostrarAlertaFlotante(errMsg, 'danger', 5000);
                    } else {
                        alert(errMsg);
                    }
                }
            } catch (err) {
                console.error('Error al actualizar vía AJAX:', err);
                if (typeof window.mostrarAlertaFlotante === 'function') {
                    window.mostrarAlertaFlotante('Ocurrió un error inesperado al actualizar el producto.', 'danger', 5000);
                } else {
                    alert('Ocurrió un error inesperado al actualizar.');
                }
            } finally {
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = originalText;
                }
            }
        });
    }

    // Interceptar eliminación de producto vía AJAX
    if (formEliminar) {
        formEliminar.addEventListener('submit', async function(e) {
            e.preventDefault();

            const btnSubmit = formEliminar.querySelector('button[type="submit"]');
            const originalText = btnSubmit ? btnSubmit.innerHTML : 'Sí, Eliminar';
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Eliminando...';
            }

            try {
                const formData = new FormData(formEliminar);
                const response = await fetch(formEliminar.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    if (modalEliminar) modalEliminar.hide();

                    const tr = document.getElementById('fila-producto-' + data.id);
                    if (tr) {
                        tr.style.transition = 'all 0.35s ease';
                        tr.style.opacity = '0';
                        tr.style.transform = 'scale(0.96)';
                        setTimeout(() => {
                            tr.remove();
                            const idx = cacheFilas.findIndex(item => item.tr === tr);
                            if (idx !== -1) cacheFilas.splice(idx, 1);
                            filtrarTabla();
                        }, 350);
                    }

                    if (typeof window.mostrarAlertaFlotante === 'function') {
                        window.mostrarAlertaFlotante(data.message || '¡Producto eliminado con éxito!', 'success', 5000);
                    }
                } else {
                    let errMsg = data.message || 'No se pudo eliminar el producto.';
                    if (modalEliminar) modalEliminar.hide();
                    if (typeof window.mostrarAlertaFlotante === 'function') {
                        window.mostrarAlertaFlotante(errMsg, 'danger', 5000);
                    } else {
                        alert(errMsg);
                    }
                }
            } catch (err) {
                console.error('Error al eliminar vía AJAX:', err);
                if (modalEliminar) modalEliminar.hide();
                if (typeof window.mostrarAlertaFlotante === 'function') {
                    window.mostrarAlertaFlotante('Ocurrió un error al eliminar el producto.', 'danger', 5000);
                }
            } finally {
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = originalText;
                }
            }
        });
    }

    // 2. Filtrado dinámico optimizado en memoria (Multi-palabra inteligente)
    let debounceTimer = null;
    function filtrarTabla() {
        const texto = buscador ? buscador.value.toLowerCase().trim() : '';
        const cat = filtroCat ? filtroCat.value : '';
        const stockState = filtroStock ? filtroStock.value : '';
        const palabras = texto.split(/\s+/).filter(Boolean);
        let visibles = 0;

        for (let i = 0; i < cacheFilas.length; i++) {
            const item = cacheFilas[i];
            const coincideTexto = palabras.length === 0 || palabras.every(p => item.texto.includes(p));
            const coincideCat = (cat === '' || item.categoria === cat);

            let coincideStock = true;
            if (stockState === 'critico') {
                coincideStock = (item.estadoStock === 'critico' || item.estadoStock === 'sin_stock');
            } else if (stockState !== '') {
                coincideStock = (item.estadoStock === stockState);
            }

            if (coincideTexto && coincideCat && coincideStock) {
                if (item.tr.style.display !== '') item.tr.style.display = '';
                visibles++;
            } else {
                if (item.tr.style.display !== 'none') item.tr.style.display = 'none';
            }
        }

        if (contador) contador.textContent = visibles;
    }

    if (buscador) {
        buscador.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(filtrarTabla, 80);
        });
    }
    if (filtroCat) filtroCat.addEventListener('change', filtrarTabla);
    if (filtroStock) filtroStock.addEventListener('change', filtrarTabla);

    // 3. Ordenamiento Ultrarrápido (<10ms) usando caché en memoria y DocumentFragment (0 bloqueos)
    const headers = tabla.querySelectorAll('th.sortable');
    let colActual = 0; // Inicia ordenado alfabéticamente por Nombre de Producto (Col 0)
    let ordenAsc = true;

    headers.forEach((header, index) => {
        header.style.cursor = 'pointer';
        header.title = 'Haz clic para ordenar de forma instantánea';

        header.addEventListener('click', () => {
            ordenAsc = (colActual === index) ? !ordenAsc : true;
            colActual = index;

            // Actualizar iconos visuales de cabecera
            headers.forEach(h => {
                const icon = h.querySelector('.icono-orden');
                if (icon) {
                    icon.className = 'icono-orden bi bi-arrow-down-up ms-1';
                    icon.style.opacity = '0.5';
                }
            });

            const iconActual = header.querySelector('.icono-orden');
            if (iconActual) {
                iconActual.className = `icono-orden bi ${ordenAsc ? 'bi-caret-down-fill text-primary' : 'bi-caret-up-fill text-primary'} ms-1`;
                iconActual.style.opacity = '1';
            }

            // Ordenamiento ultrarrápido en memoria pura
            cacheFilas.sort((a, b) => {
                const valA = a.sortValues[index];
                const valB = b.sortValues[index];

                if (typeof valA === 'number' && typeof valB === 'number') {
                    return ordenAsc ? valA - valB : valB - valA;
                }
                if (valA < valB) return ordenAsc ? -1 : 1;
                if (valA > valB) return ordenAsc ? 1 : -1;
                return 0;
            });

            // Reinserción masiva en 1 solo reflow usando DocumentFragment
            const fragment = document.createDocumentFragment();
            for (let i = 0; i < cacheFilas.length; i++) {
                fragment.appendChild(cacheFilas[i].tr);
            }
            tbody.appendChild(fragment);
        });
    });

    // 3. Generador de Códigos Estándar Ferretero
    const prefijosFerreteria = {
        'UNION PATENTE': 'UNP', 'TAPON HEMBRA': 'TPH', 'TAPON MACHO': 'TPM',
        'RED BUJE': 'RDB', 'REDUCCION BUJE': 'RDB', 'RED COPA': 'RDC', 'REDUCCION COPA': 'RDC',
        'CAÑERIA PVC': 'CAN-PVC', 'CANERIA PVC': 'CAN-PVC', 'CAÑERIA': 'CAN', 'CANERIA': 'CAN',
        'CABLE ALAMBRE': 'CAB-A', 'CABLE FLEXIBLE': 'CAB-F', 'CABLE CORDON COPLAS BIPOLAR': 'CAB-CCB',
        'CABLE CORDON COPLAS': 'CAB-CC', 'CABLE 7 HILOS': 'CAB-7H', 'CABLE': 'CAB',
        'CARPA': 'CRP', 'CHAPA PERFIL ESTRECHO INOX': 'CHP-PE-INOX', 'CHAPA PERFIL ESTRECHO': 'CHP-PE',
        'CHAPA CLASSIC': 'CHP-CL', 'CHAPA': 'CHP', 'CLAVO': 'CLV', 'CODO ROSCA': 'COD-R', 'CODO': 'COD',
        'TEE ROSCA': 'TEE-R', 'TEE': 'TEE', 'UNION': 'UNI', 'NIPLE': 'NIP', 'CUPLA': 'CUP',
        'BRIDA': 'BRI', 'TAPON': 'TAP', 'VALVULA': 'VAL', 'LLAVE': 'LLV', 'TUBO': 'TUB',
        'PEGAMENTO': 'PEG', 'CINTA': 'CNT', 'ALAMBRE': 'ALM', 'TORNILLO': 'TOR', 'PERNO': 'PER',
        'TUERCA': 'TUE', 'ARANDELA': 'ARA'
    };

    const palabrasIgnorar = ['KRONA', 'ACTARI', 'PLAMAT', 'EXT', 'ESPEJO', 'OXIDADO', 'OXID', 'DE', 'PARA', 'NEGRO', 'NEG', 'CON', 'SIN'];

    function generarCodigoLocal(nombre) {
        let n = (nombre || '').trim().toUpperCase();
        n = n.replace(/[ÁÀÄ]/g, 'A').replace(/[ÉÈË]/g, 'E').replace(/[ÍÌÏ]/g, 'I').replace(/[ÓÒÖ]/g, 'O').replace(/[ÚÙÜ]/g, 'U').replace(/Ñ/g, 'N');
        
        let prefijo = null;
        let resto = n;

        for (const [frase, pre] of Object.entries(prefijosFerreteria)) {
            if (n.startsWith(frase)) {
                prefijo = pre;
                resto = n.substring(frase.length).trim();
                break;
            }
        }

        if (!prefijo) {
            const palabras = n.split(/\s+/);
            const primera = palabras[0] || 'PRD';
            const sinVocales = primera.charAt(0) + primera.substring(1).replace(/[AEIOU]/g, '');
            prefijo = sinVocales.length >= 3 ? sinVocales.substring(0, 3) : primera.padEnd(3, 'X').substring(0, 3);
            resto = n.substring(primera.length).trim();
        }

        resto = resto.replace(/(\d+)\s+(\d+\/\d+)/g, '$1$2');
        const tokens = resto.split(/\s+/);
        const medidas = [];

        tokens.forEach(t => {
            t = t.replace(/[()#]/g, '').trim();
            if (!t || palabrasIgnorar.includes(t)) return;
            if (/[0-9]/.test(t) || ['INOX', 'NEG'].includes(t)) {
                medidas.push(t);
            }
        });

        let sufijo = medidas.join('-').replace(/\s+/g, '');
        let codigo = sufijo ? (prefijo + '-' + sufijo) : prefijo;
        return codigo.replace(/-+/g, '-').replace(/-$/, '');
    }

    async function aplicarCodigo(inputNombre, inputCodigo, productoId = null) {
        const nombre = inputNombre ? inputNombre.value.trim() : '';
        if (!nombre) {
            alert('Ingresa primero el Nombre del producto para generar su código estándar.');
            if (inputNombre) inputNombre.focus();
            return;
        }

        const codigoLocal = generarCodigoLocal(nombre);
        if (inputCodigo) inputCodigo.value = codigoLocal;

        try {
            let url = '/productos/generar-codigo?nombre=' + encodeURIComponent(nombre);
            if (productoId) url += '&id=' + encodeURIComponent(productoId);
            const res = await fetch(url);
            if (res.ok) {
                const data = await res.json();
                if (data.codigo && inputCodigo) {
                    inputCodigo.value = data.codigo;
                }
            }
        } catch (e) {
            console.log('Usando código estándar local:', codigoLocal);
        }

        if (inputCodigo) {
            inputCodigo.classList.add('bg-primary', 'bg-opacity-10');
            setTimeout(() => inputCodigo.classList.remove('bg-primary', 'bg-opacity-10'), 400);
        }
    }

    document.querySelectorAll('.btn-generar-codigo').forEach(btn => {
        btn.addEventListener('click', function() {
            const nombreId = this.getAttribute('data-nombre-id');
            const codigoId = this.getAttribute('data-codigo-id');
            const productoId = this.getAttribute('data-producto-id');
            
            const inNombre = document.getElementById(nombreId);
            const inCodigo = document.getElementById(codigoId);
            aplicarCodigo(inNombre, inCodigo, productoId);
        });
    });

    const inNuevoNom = document.getElementById('nuevoNombreProducto');
    const inNuevoCod = document.getElementById('nuevoCodigoProducto');
    if (inNuevoNom && inNuevoCod) {
        inNuevoNom.addEventListener('blur', function() {
            if (this.value.trim() !== '' && inNuevoCod.value.trim() === '') {
                aplicarCodigo(inNuevoNom, inNuevoCod);
            }
        });
    }
});
</script>

@endsection
