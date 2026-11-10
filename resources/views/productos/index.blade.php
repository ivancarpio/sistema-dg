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
        padding: 20px;
        background: #ffffff;
        box-shadow: 0 4px 18px rgba(0,0,0,0.04);
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .stat-card-prod:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 22px rgba(0,0,0,0.07);
    }
    .stat-icon-prod {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .stat-val-prod {
        font-size: 1.6rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }
    .stat-lbl-prod {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #64748b;
        margin-bottom: 4px;
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

<!-- ALERTAS DE ÉXITO O ERROR (Se auto-cierran en 7s) -->
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
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3 text-primary">
            <i class="bi bi-box-seam-fill fs-2"></i>
        </div>
        <div>
            <h3 class="fw-bold m-0 text-dark">Gestión de Productos</h3>
            <small class="text-muted">Catálogo maestro • Códigos estandarizados, precios y existencias</small>
        </div>
    </div>
    
    <div class="d-flex gap-2">
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
<div class="row g-3 mb-4">
    <!-- Total Productos -->
    <div class="col-6 col-md-3">
        <div class="stat-card-prod">
            <div class="stat-icon-prod" style="background: rgba(2, 132, 199, 0.12); color: #0284c7;">
                <i class="bi bi-boxes"></i>
            </div>
            <div>
                <div class="stat-lbl-prod">Total Catálogo</div>
                <div class="stat-val-prod">{{ $totalProductos }}</div>
                <small class="text-muted">artículos registrados</small>
            </div>
        </div>
    </div>

    <!-- Stock Total en Tienda -->
    <div class="col-6 col-md-3">
        <div class="stat-card-prod">
            <div class="stat-icon-prod" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                <i class="bi bi-building-check"></i>
            </div>
            <div>
                <div class="stat-lbl-prod">Stock en Tienda</div>
                <div class="stat-val-prod text-success">{{ number_format($totalStockUnidades, 0) }}</div>
                <small class="text-muted">unidades físicas</small>
            </div>
        </div>
    </div>

    <!-- Stock Crítico / Alerta -->
    <div class="col-6 col-md-3">
        <div class="stat-card-prod">
            <div class="stat-icon-prod" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div>
                <div class="stat-lbl-prod">Stock Crítico (≤ Mín)</div>
                <div class="stat-val-prod {{ $productosCriticos > 0 ? 'text-danger' : 'text-secondary' }}">
                    {{ $productosCriticos }}
                </div>
                <small class="text-muted">
                    @if($productosAlerta > 0)
                        <span class="text-warning-emphasis fw-bold">+{{ $productosAlerta }} por agotarse</span>
                    @else
                        artículos bajo mínimo
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
            <div>
                <div class="stat-lbl-prod">Valor en Tienda</div>
                <div class="stat-val-prod" style="color: #4f46e5; font-size: 1.4rem;">
                    Bs. {{ number_format($valorInventario, 2) }}
                </div>
                <small class="text-muted">precio de venta</small>
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
                    <th class="ps-4 sortable text-start" style="width: 30%;" data-col="0">
                        PRODUCTO <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i>
                    </th>
                    <th class="sortable text-center" style="width: 14%;" data-col="1">
                        CÓDIGO <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i>
                    </th>
                    <th class="sortable text-center" style="width: 14%;" data-col="2">
                        MARCA <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i>
                    </th>
                    <th class="sortable text-end" style="width: 13%;" data-col="3">
                        PRECIO VENTA <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i>
                    </th>
                    <th class="sortable text-end" style="width: 11%;" data-col="4">
                        COSTO <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i>
                    </th>
                    <th class="sortable text-center" style="width: 10%;" data-col="5">
                        STOCK <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i>
                    </th>
                    <th class="text-center pe-4" style="width: 8%;">ACCIONES</th>
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
                    data-categoria="{{ $producto->id_categoria }}"
                    data-estado-stock="{{ $estadoStock }}">
                    
                    <!-- Nombre del Producto -->
                    <td class="ps-4 text-start" data-sort-value="{{ strtolower($producto->nombre) }}">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-3">
                                <i class="bi bi-box fs-5"></i>
                            </div>
                            <div>
                                <div class="prod-nombre text-uppercase">{{ $producto->nombre }}</div>
                                <div class="text-muted small">
                                    @if($producto->categoria)
                                        <span class="badge bg-light text-secondary border me-1">{{ $producto->categoria->nombre }}</span>
                                    @endif
                                    @if($producto->unidad)
                                        <span><i class="bi bi-rulers me-1"></i>{{ $producto->unidad->nombre }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>

                    <!-- Código Estandarizado -->
                    <td class="text-center" data-sort-value="{{ $producto->codigo }}">
                        <span class="prod-codigo">{{ $producto->codigo }}</span>
                    </td>
                    
                    <!-- Marca -->
                    <td class="text-center" data-sort-value="{{ strtolower(optional($producto->marca)->nombre ?? '') }}">
                        @if($producto->marca)
                            <span class="badge bg-light text-dark border px-2 py-1 fw-bold">
                                {{ $producto->marca->nombre }}
                            </span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    
                    <!-- Precio de Venta -->
                    <td class="text-end" data-sort-value="{{ $producto->precio_venta ?? 0 }}">
                        <div class="prod-precio">
                            Bs. {{ number_format($producto->precio_venta ?? 0, 2) }}
                        </div>
                    </td>

                    <!-- Costo -->
                    <td class="text-end" data-sort-value="{{ $producto->costo ?? 0 }}">
                        <div class="prod-costo">
                            Bs. {{ number_format($producto->costo ?? 0, 2) }}
                        </div>
                    </td>

                    <!-- Stock con semáforo inteligente claro -->
                    <td class="text-center" data-sort-value="{{ $stockActual }}">
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
                                    class="btn-action-icon btn-action-edit" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEditar{{ $producto->id }}"
                                    title="Editar producto">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button type="button" 
                                    class="btn-action-icon btn-action-delete" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEliminar{{ $producto->id }}"
                                    title="Eliminar producto">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
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
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-bold">Precio de Venta (Bs.) *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">Bs.</span>
                                <input type="number" name="precio_venta" step="0.01" class="form-control fw-bold text-success fs-5" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-bold">Costo Unitario (Bs.) *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">Bs.</span>
                                <input type="number" name="costo" step="0.01" class="form-control fw-bold fs-5" placeholder="0.00" value="0.00" required>
                            </div>
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
                            <label class="form-label text-muted small fw-bold">Unidad de Medida</label>
                            <select name="id_unidad" class="form-select">
                                <option value="">Seleccionar...</option>
                                @foreach($unidades as $unidad)
                                    <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                                @endforeach
                            </select>
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

    <!-- MODALES EDITAR Y ELIMINAR (Por cada producto) -->
    @foreach($productos as $producto)
        <!-- MODAL EDITAR -->
        <div class="modal fade" id="modalEditar{{ $producto->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <form action="/productos/{{ $producto->id }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                    @csrf @method('PUT')
                    <div class="modal-header bg-light border-0 py-3">
                        <h5 class="fw-bold m-0 text-dark"><i class="bi bi-pencil-square me-2 text-primary"></i>Editar Producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold">Nombre del Producto *</label>
                            <input type="text" name="nombre" id="editNombre{{ $producto->id }}" class="form-control form-control-lg fw-bold" value="{{ $producto->nombre }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold">Código *</label>
                            <div class="input-group">
                                <input type="text" name="codigo" id="editCodigo{{ $producto->id }}" class="form-control fw-bold fs-6 font-monospace" value="{{ $producto->codigo }}" required>
                                <button class="btn btn-outline-primary fw-bold btn-generar-codigo" type="button" 
                                        data-nombre-id="editNombre{{ $producto->id }}" 
                                        data-codigo-id="editCodigo{{ $producto->id }}" 
                                        data-producto-id="{{ $producto->id }}" 
                                        title="Regenerar código estándar">
                                    <i class="bi bi-magic me-1"></i> Generar
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="2">{{ $producto->descripcion }}</textarea>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-bold">Precio de Venta (Bs.) *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">Bs.</span>
                                    <input type="number" name="precio_venta" step="0.01" class="form-control fw-bold text-success fs-5" value="{{ $producto->precio_venta }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-bold">Costo (Bs.) *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">Bs.</span>
                                    <input type="number" name="costo" step="0.01" class="form-control fw-bold fs-5" value="{{ $producto->costo }}" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold">Categoría</label>
                                <select name="id_categoria" class="form-select">
                                    <option value="">Seleccionar...</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" {{ $producto->id_categoria == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold">Marca</label>
                                <select name="id_marca" class="form-select">
                                    <option value="">Seleccionar...</option>
                                    @foreach($marcas as $marca)
                                        <option value="{{ $marca->id }}" {{ $producto->id_marca == $marca->id ? 'selected' : '' }}>{{ $marca->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold">Unidad de Medida</label>
                                <select name="id_unidad" class="form-select">
                                    <option value="">Seleccionar...</option>
                                    @foreach($unidades as $unidad)
                                        <option value="{{ $unidad->id }}" {{ $producto->id_unidad == $unidad->id ? 'selected' : '' }}>{{ $unidad->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-bold">Stock Mínimo (Alerta)</label>
                            <input type="number" name="stock_minimo" class="form-control fw-bold fs-5" value="{{ $producto->stock_minimo }}">
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-3 bg-light">
                        <button type="button" class="btn btn-light border px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary fw-bold px-5">Actualizar Producto</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL ELIMINAR -->
        <div class="modal fade" id="modalEliminar{{ $producto->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-body p-4 text-center">
                        <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle d-inline-flex mb-3">
                            <i class="bi bi-trash3 fs-1"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Eliminar Producto</h5>
                        <p class="text-muted small mb-4">¿Estás seguro de eliminar el producto <strong>{{ $producto->nombre }}</strong> ({{ $producto->codigo }})?</p>
                        <form action="/productos/{{ $producto->id }}" method="POST">
                            @csrf @method('DELETE')
                            <div class="d-flex justify-content-between gap-2">
                                <button type="button" class="btn btn-light border w-50 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger w-50 fw-bold">Sí, Eliminar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endauth

<!-- ============================================== -->
<!-- JAVASCRIPT DEL CATÁLOGO DE PRODUCTOS           -->
<!-- ============================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Filtrado dinámico combinado (Búsqueda + Categoría + Estado Stock)
    const buscador = document.getElementById('buscadorProductos');
    const filtroCat = document.getElementById('filtroCategoria');
    const filtroStock = document.getElementById('filtroStock');
    const filas = document.querySelectorAll('.fila-producto');
    const contador = document.getElementById('contadorVisibles');

    function filtrarTabla() {
        const texto = buscador ? buscador.value.toLowerCase().trim() : '';
        const cat = filtroCat ? filtroCat.value : '';
        const stockState = filtroStock ? filtroStock.value : '';
        let visibles = 0;

        filas.forEach(fila => {
            const contenido = fila.textContent.toLowerCase();
            const coincideTexto = (texto === '' || contenido.includes(texto));

            const idCat = fila.getAttribute('data-categoria') || '';
            const coincideCat = (cat === '' || idCat === cat);

            const estado = fila.getAttribute('data-estado-stock') || '';
            let coincideStock = true;
            if (stockState === 'critico') {
                coincideStock = (estado === 'critico' || estado === 'sin_stock');
            } else if (stockState !== '') {
                coincideStock = (estado === stockState);
            }

            if (coincideTexto && coincideCat && coincideStock) {
                fila.style.display = '';
                visibles++;
            } else {
                fila.style.display = 'none';
            }
        });

        if (contador) contador.textContent = visibles;
    }

    if (buscador) buscador.addEventListener('input', filtrarTabla);
    if (filtroCat) filtroCat.addEventListener('change', filtrarTabla);
    if (filtroStock) filtroStock.addEventListener('change', filtrarTabla);

    // 2. Ordenamiento de Columnas
    const tabla = document.getElementById('tablaProductos');
    if (tabla) {
        const headers = tabla.querySelectorAll('th.sortable');
        let colActual = -1;
        let ordenAsc = true;

        headers.forEach((header, index) => {
            header.addEventListener('click', () => {
                ordenAsc = (colActual === index) ? !ordenAsc : true;
                colActual = index;

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

                const tbody = tabla.querySelector('tbody');
                const filasArray = Array.from(tbody.querySelectorAll('tr.fila-producto'));

                filasArray.sort((a, b) => {
                    let valA = a.children[index].getAttribute('data-sort-value') ?? '';
                    let valB = b.children[index].getAttribute('data-sort-value') ?? '';

                    if (!isNaN(valA) && !isNaN(valB) && valA !== '' && valB !== '') {
                        valA = parseFloat(valA);
                        valB = parseFloat(valB);
                    } else {
                        valA = valA.toString().toLowerCase();
                        valB = valB.toString().toLowerCase();
                    }

                    if (valA < valB) return ordenAsc ? -1 : 1;
                    if (valA > valB) return ordenAsc ? 1 : -1;
                    return 0;
                });

                filasArray.forEach(fila => tbody.appendChild(fila));
            });
        });
    }

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
