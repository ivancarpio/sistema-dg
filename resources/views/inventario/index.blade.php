@extends('layouts.app')

@section('content')

<style>
/* ===== INVENTARIO — Diseño coherente con el sistema ===== */
.card { border: none !important; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important; border-radius: .75rem !important; }

/* Cabeceras de tabla principal */
.table-custom-header th {
    background-color: #f1f5f9 !important;
    font-size: 0.72rem; font-weight: 800; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.5px;
    padding: 13px 10px; border-bottom: none !important;
    cursor: pointer; user-select: none; transition: background-color 0.15s;
}
.table-custom-header th:hover { background-color: #e2e8f0 !important; }
.table-custom-header th:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
.table-custom-header th:last-child  { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }

.table-custom tbody tr { border-bottom: 1px solid #f0f2f5; transition: background-color 0.15s; }
.table-custom tbody tr:hover { background-color: #f8faff; }
.table-custom td { vertical-align: middle; padding: 12px 10px; }

/* Stat cards superiores */
.stat-inv {
    border-radius: 12px; padding: 16px 20px;
    border: 1px solid #e2e8f0; background: #fff;
    transition: transform 0.15s;
}
.stat-inv:hover { transform: translateY(-2px); }
.stat-inv .stat-num { font-size: 1.6rem; font-weight: 800; line-height: 1; }
.stat-inv .stat-lbl { font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 4px; }

/* Badge tipo de movimiento */
.badge-entrada {
    background: #dcfce7; color: #16a34a; font-weight: 700;
    border-radius: 20px; padding: 5px 14px; font-size: 0.78rem;
    display: inline-flex; align-items: center; gap: 5px;
    border: 1px solid #bbf7d0;
}
.badge-salida {
    background: #fee2e2; color: #dc2626; font-weight: 700;
    border-radius: 20px; padding: 5px 14px; font-size: 0.78rem;
    display: inline-flex; align-items: center; gap: 5px;
    border: 1px solid #fecaca;
}
.badge-devolucion {
    background: #ede9fe; color: #6d28d9; font-weight: 700;
    border-radius: 20px; padding: 5px 14px; font-size: 0.78rem;
    display: inline-flex; align-items: center; gap: 5px;
    border: 1px solid #ddd6fe;
}

/* Modal rediseñado con estilo Administración Masiva */
.modal-header-custom {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    padding: 18px 24px;
    border-radius: 14px 14px 0 0;
}
.modal-content-clean {
    background-color: #ffffff !important;
    border: none !important;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1) !important;
    border-radius: 16px !important;
    overflow: hidden;
}
.form-label-custom { font-size: 0.78rem; font-weight: 800; text-transform: uppercase;
    letter-spacing: 0.5px; color: #475569; margin-bottom: 6px; }

/* Botones visuales de Tipo de Movimiento */
.tipo-btn { border: 2px solid #e2e8f0; border-radius: 10px; padding: 10px 8px;
    cursor: pointer; transition: all 0.2s; text-align: center; background: #f8fafc; }
.tipo-btn.selected-entrada { border-color: #16a34a; background: #f0fdf4; box-shadow: 0 2px 6px rgba(22, 163, 74, 0.15); }
.tipo-btn.selected-salida  { border-color: #dc2626; background: #fff1f2; box-shadow: 0 2px 6px rgba(220, 38, 38, 0.15); }
.tipo-btn .tipo-icon { font-size: 1.35rem; margin-bottom: 2px; }
.tipo-btn .tipo-label { font-size: 0.82rem; font-weight: 700; }

/* Filas interactivas en tabla del modal */
.fila-prod-ajuste {
    cursor: pointer;
    transition: background-color 0.15s ease;
}
.fila-prod-ajuste:hover {
    background-color: #f0f9ff !important;
}
.fila-prod-ajuste.fila-seleccionada {
    background-color: #f0fdf4 !important;
}

/* Scrollbar azul idéntico a Administración Masiva de Categorías */
.tabla-scroll-masivo {
    height: 320px !important;
    max-height: 320px !important;
    overflow-y: scroll !important;
    border: 1px solid #cbd5e1;
    border-radius: 8px 8px 0 0;
    background-color: #ffffff;
    -webkit-overflow-scrolling: touch;
}
.tabla-scroll-masivo::-webkit-scrollbar {
    width: 10px;
}
.tabla-scroll-masivo::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}
.tabla-scroll-masivo::-webkit-scrollbar-thumb {
    background-color: #0284c7;
    border-radius: 6px;
    border: 2px solid #f1f5f9;
}
.tabla-scroll-masivo::-webkit-scrollbar-thumb:hover {
    background-color: #0369a1;
}
.table-masiva td {
    padding: 8px 10px !important;
    vertical-align: middle !important;
}
.table-masiva th {
    padding: 9px 10px !important;
    background-color: #f8fafc !important;
    z-index: 5;
    font-size: 0.74rem;
    font-weight: 800;
    color: #64748b;
    letter-spacing: 0.5px;
}

/* Chips de motivo rápido */
.chip-motivo {
    cursor: pointer;
    transition: all 0.15s;
    border-radius: 6px;
    font-size: 0.76rem;
}
.chip-motivo:hover {
    background-color: #e0f2fe !important;
    color: #0369a1 !important;
    border-color: #bae6fd !important;
}

/* Campo requerido error */
.campo-requerido.error { border-color: #ef4444 !important; box-shadow: 0 0 0 3px rgba(239,68,68,0.1) !important; }
.msg-error { color: #dc2626; font-size: 0.78rem; font-weight: 600; margin-top: 4px; }

/* Responsive Móvil para Modal Nuevo Ajuste y Tabla Principal de Inventario */
@media (max-width: 767.98px) {
    /* Tabla Principal de Movimientos compacta para móvil */
    .table-custom-header th {
        padding: 8px 6px !important;
        font-size: 0.65rem !important;
        letter-spacing: 0.3px !important;
    }
    .table-custom td {
        padding: 6px 6px !important;
        font-size: 0.78rem !important;
    }
    .table-custom td .fw-bold.text-dark {
        font-size: 0.80rem !important;
        line-height: 1.25 !important;
        font-weight: 700 !important;
    }
    .table-custom td .fw-semibold.text-dark {
        font-size: 0.78rem !important;
        line-height: 1.2 !important;
    }
    .table-custom td small {
        font-size: 0.68rem !important;
    }
    .badge-entrada, .badge-salida, .badge-devolucion {
        padding: 2px 7px !important;
        font-size: 0.70rem !important;
        border-radius: 12px !important;
    }
    .btn-filtro-tipo {
        padding: 4px 7px !important;
        font-size: 0.72rem !important;
    }
    .card-header.py-3 {
        padding: 10px 12px !important;
    }
    .card-header h6 {
        font-size: 0.88rem !important;
    }
    .card-header small {
        font-size: 0.68rem !important;
    }

    /* Stat cards superiores en móvil */
    .stat-inv {
        padding: 10px 12px !important;
        border-radius: 10px !important;
    }
    .stat-inv .stat-num {
        font-size: 1.15rem !important;
    }
    .stat-inv .stat-lbl {
        font-size: 0.65rem !important;
        margin-bottom: 2px !important;
    }

    /* Barra de filtros de fecha y buscador */
    .card.p-3.mb-4 {
        padding: 10px 12px !important;
        margin-bottom: 12px !important;
    }
    .card.p-3.mb-4 .input-group-text,
    .card.p-3.mb-4 .form-control,
    .card.p-3.mb-4 .btn {
        height: 36px !important;
        font-size: 0.82rem !important;
    }

    #modalNuevoAjuste .modal-dialog {
        margin: 0.5rem auto !important;
        max-width: 96% !important;
    }
    #modalNuevoAjuste .modal-header-custom {
        padding: 12px 14px !important;
    }
    #modalNuevoAjuste .modal-header-custom h5 {
        font-size: 1rem !important;
    }
    #modalNuevoAjuste .modal-body {
        padding: 12px !important;
    }
    #modalNuevoAjuste .paso-1-card {
        padding: 10px 12px !important;
        margin-bottom: 10px !important;
    }
    #modalNuevoAjuste .tipo-btn {
        padding: 8px 4px !important;
    }
    #modalNuevoAjuste .tipo-btn .tipo-icon {
        font-size: 1.15rem !important;
        margin-bottom: 0 !important;
    }
    #modalNuevoAjuste .tipo-btn .tipo-label {
        font-size: 0.74rem !important;
    }

    /* Tabla con scroll exactamente igual a Administración Masiva de Categorías */
    .tabla-scroll-masivo {
        height: 320px !important;
        max-height: 320px !important;
    }
    .table-masiva th {
        padding: 6px 6px !important;
        font-size: 0.72rem !important;
        letter-spacing: 0.2px !important;
    }
    .table-masiva td {
        padding: 7px 6px !important;
        font-size: 0.80rem !important;
        line-height: 1.25 !important;
    }
    .table-masiva .prod-code {
        font-size: 0.72rem !important;
        white-space: nowrap !important;
    }
    .table-masiva .prod-name {
        font-size: 0.82rem !important;
        line-height: 1.25 !important;
        font-weight: 700 !important;
    }
    .table-masiva .prod-empaque {
        font-size: 0.65rem !important;
        line-height: 1.2 !important;
    }
    .table-masiva .marca-badge {
        font-size: 0.68rem !important;
        padding: 2px 5px !important;
        white-space: normal !important;
        display: inline-block !important;
        max-width: 90px !important;
        line-height: 1.2 !important;
    }
    .table-masiva .badge-stock {
        font-size: 0.70rem !important;
        padding: 2px 5px !important;
    }
    .table-masiva .input-cant-fila {
        width: 60px !important;
        height: 28px !important;
        font-size: 0.82rem !important;
        padding: 2px 4px !important;
    }
    .table-masiva .form-check-input {
        width: 1.1rem !important;
        height: 1.1rem !important;
    }

    #modalNuevoAjuste .modal-footer {
        padding: 10px 14px !important;
    }
    #modalNuevoAjuste .modal-footer .btn {
        font-size: 0.86rem !important;
        padding: 8px 14px !important;
    }
}

/* ===== RESPONSIVE MÓVIL — Inventario ===== */
@media (max-width: 767.98px) {
    /* Encabezado: título y botón a la par en la misma fila sin cortar descripción */
    .header-inventario {
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 10px !important;
        margin-bottom: 1rem !important;
    }
    .header-inv-left {
        min-width: 0 !important;
        flex: 1 1 auto !important;
    }
    .header-inv-left h3 {
        font-size: 1.05rem !important;
        line-height: 1.2 !important;
        margin-bottom: 2px !important;
    }
    .header-inv-left small {
        font-size: 0.70rem !important;
        line-height: 1.25 !important;
        display: block !important;
        white-space: normal !important;
        word-break: normal !important;
        color: #64748b !important;
    }
    .header-inv-icon {
        width: 36px !important;
        height: 36px !important;
        padding: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 8px !important;
        flex-shrink: 0 !important;
    }
    .header-inv-icon i {
        font-size: 1.25rem !important;
    }
    .header-btn-ajuste {
        flex-shrink: 0 !important;
        white-space: nowrap !important;
        font-size: 0.78rem !important;
        padding: 7px 12px !important;
        border-radius: 8px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    /* Stat cards inventario */
    .stat-inv {
        padding: 10px 12px !important;
        border-radius: 10px !important;
    }
    .stat-inv .stat-num {
        font-size: 1.2rem !important;
    }
    .stat-inv .stat-lbl {
        font-size: 0.62rem !important;
    }
    .stat-inv small {
        font-size: 0.68rem !important;
    }

    /* Tabla del historial */
    .table-custom-header th {
        padding: 8px 6px !important;
        font-size: 0.65rem !important;
    }
    .table-custom td {
        padding: 8px 6px !important;
        font-size: 0.8rem !important;
    }

    /* Encabezado de la tarjeta de historial */
    .card-header h6 { font-size: 0.9rem !important; }
    .card-header small { font-size: 0.7rem !important; }

    /* Botones de filtro de tipo adaptados al 100% de ancho sin espacios vacíos */
    .wrapper-botones-tipo {
        width: 100% !important;
        margin-top: 4px !important;
    }
    #grupoBotonesTipo {
        width: 100% !important;
        display: flex !important;
    }
    #grupoBotonesTipo .btn {
        flex: 1 1 25% !important;
        font-size: 0.74rem !important;
        padding: 6px 2px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        white-space: nowrap !important;
    }
    #grupoBotonesTipo .btn i {
        margin-right: 2px !important;
        font-size: 0.72rem !important;
    }
}
</style>

{{-- ===== ENCABEZADO ===== --}}
<div class="d-flex justify-content-between align-items-center gap-2 mb-4 header-inventario">
    <div class="d-flex align-items-center gap-2 header-inv-left">
        <div class="bg-info bg-opacity-10 p-2 rounded text-info header-inv-icon">
            <i class="bi bi-diagram-3-fill fs-3"></i>
        </div>
        <div>
            <h3 class="fw-bold m-0 text-dark">Control de Inventario</h3>
            <small class="text-muted">Historial de entradas, salidas y ajustes de stock</small>
        </div>
    </div>
    <button class="btn btn-primary fw-bold shadow-sm px-3 py-2 header-btn-ajuste" data-bs-toggle="modal" data-bs-target="#modalNuevoAjuste">
        <i class="bi bi-plus-circle-fill me-1"></i>Nuevo Ajuste
    </button>
</div>

{{-- ===== TARJETAS DE ESTADÍSTICAS ===== --}}
@php
    $totalMovimientos = $movimientos->count();
    $totalEntradas    = $movimientos->filter(function($m) {
        $t = strtolower(trim($m->tipo ?? ''));
        return in_array($t, ['entrada', 'agregar']) && !str_contains(strtolower($m->motivo ?? ''), 'devolución') && !str_contains(strtolower($m->motivo ?? ''), 'anulación');
    })->count();
    $totalDevoluciones = $movimientos->filter(function($m) {
        $t = strtolower(trim($m->tipo ?? ''));
        return in_array($t, ['devolución', 'devolucion']) || str_contains(strtolower($m->motivo ?? ''), 'devolución') || str_contains(strtolower($m->motivo ?? ''), 'anulación');
    })->count();
    $totalSalidas     = $movimientos->filter(function($m) {
        $t = strtolower(trim($m->tipo ?? ''));
        return $t === 'salida' && !str_contains(strtolower($m->motivo ?? ''), 'devolución') && !str_contains(strtolower($m->motivo ?? ''), 'anulación');
    })->count();
@endphp
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-inv h-100">
            <div class="stat-lbl"><i class="bi bi-clock-history me-1 text-primary"></i>Total Movimientos</div>
            <div class="stat-num text-dark">{{ $totalMovimientos }}</div>
            <small class="text-muted">{{ $subtituloPeriodo }}</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-inv h-100">
            <div class="stat-lbl"><i class="bi bi-arrow-up-circle me-1 text-success"></i>Entradas</div>
            <div class="stat-num text-success">{{ $totalEntradas }}</div>
            <small class="text-muted">ingresos de mercadería</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-inv h-100">
            <div class="stat-lbl"><i class="bi bi-arrow-down-circle me-1 text-danger"></i>Salidas</div>
            <div class="stat-num text-danger">{{ $totalSalidas }}</div>
            <small class="text-muted">ventas y retiros</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-inv h-100">
            <div class="stat-lbl" style="color: #7c3aed;"><i class="bi bi-arrow-counterclockwise me-1"></i>Devoluciones</div>
            <div class="stat-num" style="color: #7c3aed;">{{ $totalDevoluciones }}</div>
            <small class="text-muted">anulaciones de venta</small>
        </div>
    </div>
</div>

{{-- ===== FILTROS Y BÚSQUEDA AVANZADA ===== --}}
<div class="card p-3 mb-4 shadow-sm border-0 bg-white">
    <form action="/inventario" method="GET" class="row g-2 align-items-end" id="formFiltroInventario">
        <input type="hidden" name="tipo" id="inputHiddenTipo" value="{{ request('tipo') }}">

        <!-- Única barra de búsqueda inteligente -->
        <div class="col-12 col-md-5 col-xl-4">
            <label class="form-label small fw-bold text-muted mb-1">Buscar Producto, Motivo o Marca</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" name="buscar" id="inputBuscarInventario" class="form-control border-start-0 bg-light" 
                       placeholder="Buscar por código, producto, marca, motivo..." value="{{ request('buscar') }}" autocomplete="off">
            </div>
        </div>

        <!-- Rango de Fechas: Desde -->
        <div class="col-6 col-md-2 col-xl-2">
            <label class="form-label small fw-bold text-muted mb-1">
                <i class="bi bi-calendar-event me-1"></i>Fecha Desde
            </label>
            <input type="date" name="fecha_desde" id="inputFechaDesde" class="form-control bg-light" 
                   value="{{ $verTodo ? '' : ($fechaDesde ?? now()->toDateString()) }}">
        </div>

        <!-- Rango de Fechas: Hasta -->
        <div class="col-6 col-md-2 col-xl-2">
            <label class="form-label small fw-bold text-muted mb-1">
                <i class="bi bi-calendar-check me-1"></i>Fecha Hasta
            </label>
            <input type="date" name="fecha_hasta" id="inputFechaHasta" class="form-control bg-light" 
                   value="{{ $verTodo ? '' : ($fechaHasta ?? now()->toDateString()) }}">
        </div>

        <!-- Botones de Acción y Presets Rápidos -->
        <div class="col-12 col-md-5 col-xl-4">
            <div class="d-flex align-items-center gap-1.5 w-100 flex-nowrap" style="height: 38px;">
                <button type="submit" class="btn btn-primary text-white fw-bold shadow-sm d-flex align-items-center justify-content-center px-3" style="height: 38px; white-space: nowrap; flex-shrink: 0;">
                    <i class="bi bi-funnel-fill me-1"></i> Filtrar
                </button>
                <div class="btn-group flex-grow-1 shadow-sm" role="group" style="height: 38px;">
                    <a href="/inventario" class="btn btn-sm btn-outline-success fw-semibold d-flex align-items-center justify-content-center px-2 {{ (!$verTodo && ($fechaDesde ?? '') === now()->toDateString() && ($fechaHasta ?? '') === now()->toDateString()) ? 'active fw-bold' : '' }}" title="Ver movimientos de Hoy" style="white-space: nowrap; font-size: 0.82rem;">
                        Hoy
                    </a>
                    <a href="/inventario?fecha_desde={{ now()->startOfWeek()->toDateString() }}&fecha_hasta={{ now()->toDateString() }}" class="btn btn-sm btn-outline-info fw-semibold d-flex align-items-center justify-content-center px-2" title="Ver movimientos de esta semana" style="white-space: nowrap; font-size: 0.82rem;">
                        Semana
                    </a>
                    <a href="/inventario?ver=todos" class="btn btn-sm btn-outline-secondary fw-semibold d-flex align-items-center justify-content-center px-2 {{ $verTodo ? 'active fw-bold' : '' }}" title="Ver todo el historial acumulado" style="white-space: nowrap; font-size: 0.82rem;">
                        Histórico
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- ===== TARJETA PRINCIPAL CON TABLA ===== --}}
<div class="card">

    {{-- Cabecera con selector exclusivo de Tipo de Movimiento por botones --}}
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h6 class="fw-bold m-0 text-dark">
                    <i class="bi bi-list-ul me-2 text-primary"></i>Historial de Movimientos
                    <span class="badge bg-primary ms-1" id="badgeTotal">{{ $totalMovimientos }}</span>
                </h6>
                <small class="text-muted">Mostrando: <strong class="text-dark">{{ $subtituloPeriodo }}</strong> • Cada día nuevo inicia en 0</small>
            </div>
            <div class="wrapper-botones-tipo">
                {{-- Selector exclusivo por tipo de movimiento mediante botones --}}
                @php
                    $tipoActual = strtolower(request('tipo', 'todos'));
                @endphp
                <div class="btn-group shadow-xs w-100" role="group" id="grupoBotonesTipo">
                    <button type="button" class="btn btn-sm btn-filtro-tipo {{ ($tipoActual === 'todos' || empty($tipoActual)) ? 'btn-secondary text-white fw-bold active' : 'btn-outline-secondary' }}"
                        onclick="filtrarTipo('todos', this)">
                        Todos
                    </button>
                    <button type="button" class="btn btn-sm btn-filtro-tipo {{ $tipoActual === 'entrada' ? 'btn-success text-white fw-bold active' : 'btn-outline-success' }}"
                        onclick="filtrarTipo('entrada', this)">
                        <i class="bi bi-arrow-up"></i> Entradas
                    </button>
                    <button type="button" class="btn btn-sm btn-filtro-tipo {{ $tipoActual === 'salida' ? 'btn-danger text-white fw-bold active' : 'btn-outline-danger' }}"
                        onclick="filtrarTipo('salida', this)">
                        <i class="bi bi-arrow-down"></i> Salidas
                    </button>
                    <button type="button" class="btn btn-sm btn-filtro-tipo {{ $tipoActual === 'devolucion' ? 'text-white fw-bold active' : '' }}"
                        style="{{ $tipoActual === 'devolucion' ? 'background-color: #7c3aed; border-color: #7c3aed;' : 'color: #6d28d9; border: 1px solid #c4b5fd;' }}"
                        onclick="filtrarTipo('devolucion', this)">
                        <i class="bi bi-arrow-counterclockwise"></i> Devoluc.
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla Principal --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom align-middle m-0" id="tablaInventario">
                <thead class="table-custom-header">
                    <tr>
                        <th class="ps-4" data-col="0" style="width: 13%;">
                            Fecha y Hora <i class="icono-orden bi bi-arrow-down-up text-muted ms-1" style="font-size:0.8em;"></i>
                        </th>
                        <th data-col="1" style="width: 19%;">
                            Producto <i class="icono-orden bi bi-arrow-down-up text-muted ms-1" style="font-size:0.8em;"></i>
                        </th>
                        <th data-col="2" style="width: 11%;">
                            Tipo <i class="icono-orden bi bi-arrow-down-up text-muted ms-1" style="font-size:0.8em;"></i>
                        </th>
                        <th class="text-center" data-col="3" style="width: 10%;">
                            Stock Antes <i class="icono-orden bi bi-arrow-down-up text-muted ms-1" style="font-size:0.8em;"></i>
                        </th>
                        <th class="text-center" data-col="4" style="width: 9%;">
                            Movimiento <i class="icono-orden bi bi-arrow-down-up text-muted ms-1" style="font-size:0.8em;"></i>
                        </th>
                        <th class="text-center" data-col="5" style="width: 11%;">
                            Stock Saldo <i class="icono-orden bi bi-arrow-down-up text-muted ms-1" style="font-size:0.8em;"></i>
                        </th>
                        <th data-col="6" style="width: 15%;">
                            Motivo <i class="icono-orden bi bi-arrow-down-up text-muted ms-1" style="font-size:0.8em;"></i>
                        </th>
                        <th class="pe-4" data-col="7" style="width: 12%;">
                            Usuario <i class="icono-orden bi bi-arrow-down-up text-muted ms-1" style="font-size:0.8em;"></i>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimientos as $mov)
                    @php
                        $t = strtolower(trim($mov->tipo ?? ''));
                        $esDevolucion = in_array($t, ['devolución', 'devolucion']) || str_contains(strtolower($mov->motivo ?? ''), 'devolución') || str_contains(strtolower($mov->motivo ?? ''), 'anulación');
                        $esEntrada    = !$esDevolucion && in_array($t, ['entrada', 'agregar']);
                        $tipoKey      = $esDevolucion ? 'devolucion' : ($esEntrada ? 'entrada' : 'salida');

                        $stockAntes = $mov->stock_anterior;
                        $stockSaldo = $mov->stock_nuevo;
                    @endphp
                    <tr class="fila-movimiento" data-tipo="{{ $tipoKey }}">
                        {{-- Fecha --}}
                        <td class="ps-4" data-sort-value="{{ $mov->fecha ?? '0000-00-00' }}">
                            <div class="fw-semibold text-dark" style="font-size:0.88rem;">
                                {{ \Carbon\Carbon::parse($mov->fecha)->format('d/m/Y') }}
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-clock"></i>
                                {{ \Carbon\Carbon::parse($mov->fecha)->format('H:i') }}
                            </small>
                        </td>
                        {{-- Producto --}}
                        <td data-sort-value="{{ optional($mov->producto)->nombre ?? '' }}">
                            <div class="fw-bold text-dark">
                                {{ optional($mov->producto)->nombre ?? 'Producto eliminado' }}
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-upc me-1"></i>
                                {{ optional($mov->producto)->codigo ?? 'N/A' }}
                                @if(optional($mov->producto)->marca)
                                    <span class="badge ms-1" style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; font-size:0.70rem;">
                                        {{ optional($mov->producto)->marca->nombre }}
                                    </span>
                                @endif
                            </small>
                        </td>
                        {{-- Tipo --}}
                        <td data-sort-value="{{ $tipoKey }}">
                            @if($esDevolucion)
                                <span class="badge-devolucion">
                                    <i class="bi bi-arrow-counterclockwise"></i> Devolución
                                </span>
                            @elseif($esEntrada)
                                <span class="badge-entrada">
                                    <i class="bi bi-arrow-up-circle-fill"></i> Entrada
                                </span>
                            @else
                                <span class="badge-salida">
                                    <i class="bi bi-arrow-down-circle-fill"></i> Salida
                                </span>
                            @endif
                        </td>
                        {{-- Stock Antes --}}
                        <td class="text-center" data-sort-value="{{ $stockAntes ?? 0 }}">
                            @if($stockAntes !== null)
                                <span class="fw-bold text-secondary" style="font-size: 0.92rem;">
                                    {{ $stockAntes }} <small class="text-muted" style="font-size: 0.72rem;">UND.</small>
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        {{-- Cantidad --}}
                        <td class="text-center fw-bold fs-6" data-sort-value="{{ $mov->cantidad ?? 0 }}"
                            style="color: {{ $esDevolucion ? '#7c3aed' : ($esEntrada ? '#16a34a' : '#dc2626') }};">
                            {{ $esDevolucion ? '+' : ($esEntrada ? '+' : '-') }}{{ $mov->cantidad ?? 0 }}
                        </td>
                        {{-- Stock Saldo Resultante --}}
                        <td class="text-center" data-sort-value="{{ $stockSaldo ?? 0 }}">
                            @if($stockSaldo !== null)
                                <span class="badge px-3 py-1 fs-6 fw-bold" style="background-color: #f1f5f9; color: #0f172a; border: 1px solid #cbd5e1;">
                                    {{ $stockSaldo }} <small style="font-size: 0.72rem; opacity: 0.8;">UND.</small>
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        {{-- Motivo --}}
                        <td data-sort-value="{{ $mov->motivo ?? '' }}">
                            @if($mov->motivo)
                                <span style="font-size:0.86rem; color:#334155; font-weight: 500;">
                                    {{ $mov->motivo }}
                                </span>
                            @else
                                <span class="text-muted small">Sin motivo</span>
                            @endif
                        </td>
                        {{-- Usuario --}}
                        <td class="pe-4" data-sort-value="{{ optional($mov->usuario)->name ?? '' }}">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:28px; height:28px; background:#eff6ff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:800; color:#1e40af; flex-shrink:0;">
                                    {{ strtoupper(substr(optional($mov->usuario)->name ?? 'U', 0, 2)) }}
                                </div>
                                <span class="fw-semibold text-dark text-truncate" style="font-size:0.84rem; max-width: 90px;">
                                    {{ optional($mov->usuario)->name ?? 'Sistema' }}
                                </span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="filaVacia">
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar2-check fs-1 d-block mb-2 text-primary opacity-50"></i>
                            <h6 class="fw-bold mb-1 text-dark">No hay movimientos registrados para este período</h6>
                            <small class="text-muted d-block mb-3">
                                @php
                                    $esFiltroHoy = (!$verTodo && ($fechaDesde ?? ($fechaFiltro ?? null)) === now()->toDateString() && ($fechaHasta ?? now()->toDateString()) === now()->toDateString());
                                @endphp
                                @if($esFiltroHoy)
                                    El día de hoy aún no tiene ingresos ni salidas de mercadería registradas.
                                @else
                                    No se encontraron movimientos para los filtros o fecha seleccionada.
                                @endif
                            </small>
                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoAjuste">
                                    <i class="bi bi-plus-circle me-1"></i> Registrar Ajuste
                                </button>
                                @if(!$verTodo)
                                    <a href="/inventario?ver=todos" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-clock-history me-1"></i> Ver Historial Completo
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mensaje de "sin resultados" para el buscador --}}
        <div id="sinResultados" class="text-center py-5 text-muted" style="display:none;">
            <i class="bi bi-search fs-1 d-block mb-2 opacity-25"></i>
            <p class="fw-bold mb-0">Sin resultados para tu búsqueda</p>
        </div>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL — REGISTRAR AJUSTE DE INVENTARIO (UNA SOLA VISTA, SIN PESTAÑAS)     --}}
{{-- Diseñado fielmente al selector de Administración Masiva de Categorías     --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="modalNuevoAjuste" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 900px;">
        <form action="/inventario" method="POST" id="formNuevoAjuste" class="modal-content modal-content-clean border-0 shadow-lg rounded-4 overflow-hidden" novalidate>
            @csrf

            {{-- Header del modal --}}
            <div class="modal-header border-0 py-3 px-4" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px);">
                        <i class="bi bi-sliders2 fs-4 text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold m-0 text-white" style="font-size: 1.15rem; letter-spacing: -0.2px;">Registrar Ajuste de Inventario</h5>
                        <small class="text-white-50" style="font-size: 0.8rem;">Entrada o salida manual de stock para uno o múltiples productos</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-3 p-md-4 bg-white">

                {{-- PASO 1: TIPO DE MOVIMIENTO Y CANTIDAD GENERAL --}}
                <div class="card border border-primary border-opacity-20 rounded-4 mb-3 p-3 shadow-sm paso-1-card" style="background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);">
                    <div class="row g-3 align-items-center">
                        <!-- Tipo de Movimiento (Botones Entrada / Salida) -->
                        <div class="col-12 col-md-7">
                            <label class="form-label fw-bold text-dark mb-1.5 d-flex align-items-center gap-1.5" style="font-size: 0.88rem;">
                                <span class="badge bg-primary text-white rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 0.74rem;">1</span>
                                <span>Tipo de Movimiento *</span>
                            </label>
                            <input type="hidden" name="tipo" id="tipoAjusteHidden" value="ENTRADA">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="tipo-btn selected-entrada" id="btnEntradaModal" onclick="seleccionarTipoAjuste('ENTRADA')">
                                        <div class="tipo-icon text-success"><i class="bi bi-arrow-up-circle-fill"></i></div>
                                        <div class="tipo-label text-success">🟢 Entrada (Sumar)</div>
                                        <small class="text-muted d-none d-sm-block">Ingreso de mercadería</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="tipo-btn" id="btnSalidaModal" onclick="seleccionarTipoAjuste('SALIDA')">
                                        <div class="tipo-icon text-danger"><i class="bi bi-arrow-down-circle-fill"></i></div>
                                        <div class="tipo-label text-danger">🔴 Salida (Restar)</div>
                                        <small class="text-muted d-none d-sm-block">Merma o retiro físico</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cantidad Rápida por Defecto -->
                        <div class="col-12 col-md-5">
                            <label class="form-label fw-bold text-dark mb-1.5 d-flex align-items-center justify-content-between" style="font-size: 0.88rem;">
                                <span>Cantidad a Aplicar</span>
                                <span class="badge bg-light text-secondary border px-1.5 py-0.5 rounded-pill" style="font-size: 0.70rem;">Editable por fila</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted"><i class="bi bi-123"></i></span>
                                <input type="number" id="inputCantidadGeneralAjuste" name="cantidad_general" class="form-control fw-bold fs-5 text-center" min="0" value="0" placeholder="0">
                                <button type="button" class="btn btn-outline-primary fw-bold btn-sm" id="btnAplicarCantidadGeneral" title="Rellenar esta cantidad en todos los productos seleccionados">
                                    <i class="bi bi-arrow-repeat me-1"></i>Aplicar
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.74rem;">Opcional: Si ingresas un número aquí y pulsas "Aplicar", se asignará a los seleccionados.</small>
                        </div>
                    </div>
                </div>

                {{-- PASO 2: SELECCIÓN DE PRODUCTOS CON BUSCADOR Y MARCAS CLARAS --}}
                <div class="mb-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                        <label class="form-label fw-bold text-dark m-0 d-flex align-items-center gap-1.5">
                            <span class="badge bg-primary text-white rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 0.74rem;">2</span>
                            <span style="font-size: 0.92rem;">Selecciona los Productos a Ajustar</span>
                        </label>
                        <span id="badgeConteoSeleccionadosAjuste" class="badge bg-primary px-2.5 py-1 rounded-pill fw-bold shadow-sm" style="font-size: 0.82rem;">
                            0 seleccionados
                        </span>
                    </div>

                    <!-- Buscador en vivo multi-palabra y filtro de marca -->
                    <div class="row g-2 mb-2">
                        <div class="col-12 col-md-8">
                            <div class="input-group shadow-none" style="min-height: 38px;">
                                <span class="input-group-text bg-white border-end-0 text-muted px-2.5" style="border-radius: 8px 0 0 8px; border-color: #cbd5e1;"><i class="bi bi-search"></i></span>
                                <input type="text" id="buscadorModalProd" class="form-control border-start-0" placeholder="Buscar por código, producto o marca (ej. codo tigre)..." style="border-radius: 0 8px 8px 0; border-color: #cbd5e1; font-size: 0.88rem;">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <select id="filtroMarcaModal" class="form-select shadow-none" style="min-height: 38px; border-radius: 8px; border-color: #cbd5e1; font-size: 0.86rem;">
                                <option value="todas">Todas las marcas</option>
                                @php
                                    $marcasUnicas = $productos->pluck('marca.nombre')->filter()->unique()->sort();
                                @endphp
                                @foreach($marcasUnicas as $mNom)
                                    <option value="{{ strtolower($mNom) }}">{{ $mNom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Acciones rápidas de selección masiva -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-1">
                        <div class="d-flex gap-1.5">
                            <button type="button" class="btn btn-sm btn-outline-primary fw-bold px-2.5 py-1 rounded-2 d-inline-flex align-items-center gap-1 shadow-none" id="btnMarcarVisiblesAjuste" style="font-size: 0.78rem;">
                                <i class="bi bi-check-all fs-6"></i> <span>Marcar visibles</span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary fw-semibold px-2.5 py-1 rounded-2 d-inline-flex align-items-center gap-1 shadow-none" id="btnDesmarcarTodosAjuste" style="font-size: 0.78rem;">
                                <i class="bi bi-x-circle"></i> <span>Desmarcar todos</span>
                            </button>
                        </div>
                        <div class="text-muted small fw-semibold text-end" id="txtTotalVisiblesModal" style="font-size: 0.78rem;">
                            Mostrando <strong class="text-dark">{{ count($productos) }}</strong> producto(s)
                        </div>
                    </div>

                    <!-- Tabla con scroll vertical (estilo exacto de Administración Masiva de Categorías) -->
                    <div class="tabla-scroll-masivo">
                        <table class="table table-hover table-masiva m-0">
                            <thead class="sticky-top">
                                <tr>
                                    <th style="width: 35px;" class="text-center">
                                        <i class="bi bi-check2"></i>
                                    </th>
                                    <th style="width: 85px;">CÓDIGO</th>
                                    <th>PRODUCTO</th>
                                    <th style="width: 105px;">MARCA</th>
                                    <th style="width: 60px;" class="text-center">STOCK</th>
                                    <th style="width: 85px;" class="text-center">CANTIDAD</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyModalProductos">
                                @forelse($productos as $prod)
                                @php
                                    $nomMarca = optional($prod->marca)->nombre ?? 'Genérico';
                                    $nomUnidad = optional($prod->unidad)->nombre ?? 'u.';
                                @endphp
                                <tr class="fila-prod-ajuste" 
                                    data-id="{{ $prod->id }}" 
                                    data-marca="{{ strtolower($nomMarca) }}" 
                                    data-texto="{{ strtolower($prod->codigo . ' ' . $prod->nombre . ' ' . $nomMarca) }}"
                                    onclick="toggleCheckProdAjuste(event, 'checkAjuste{{ $prod->id }}')">
                                    <td class="text-center">
                                        <input type="checkbox" name="productos_ids[]" value="{{ $prod->id }}" id="checkAjuste{{ $prod->id }}" class="form-check-input check-prod-ajuste" style="cursor: pointer; width: 1.15rem; height: 1.15rem;">
                                    </td>
                                    <td class="small fw-bold text-secondary prod-code">{{ $prod->codigo }}</td>
                                    <td class="fw-bold text-dark prod-name">
                                        {{ $prod->nombre }}
                                        @if($prod->tieneUnidadSecundaria())
                                            <small class="text-muted fw-normal d-block prod-empaque" style="font-size: 0.70rem;">
                                                Empaque: {{ optional($prod->unidadSecundaria)->nombre }} (x{{ round($prod->factor_conversion) }} {{ $nomUnidad }})
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge marca-badge" style="background:#f1f5f9; color:#334155; border:1px solid #cbd5e1; font-size:0.75rem; font-weight:600;">
                                            {{ $nomMarca }}
                                        </span>
                                    </td>
                                    <td class="text-center small fw-bold">
                                        <span class="badge bg-light text-dark border badge-stock">{{ $prod->stock }}</span>
                                    </td>
                                    <td class="text-center" onclick="event.stopPropagation();">
                                        <input type="number" 
                                               name="cantidades[{{ $prod->id }}]" 
                                               id="inputCantRow{{ $prod->id }}"
                                               class="form-control form-control-sm text-center fw-bold input-cant-fila" 
                                               min="0" 
                                               value="0" 
                                               placeholder="0"
                                               style="width: 85px; margin: 0 auto; border-color: #cbd5e1;"
                                               disabled>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No hay productos registrados en el inventario.</td>
                                </tr>
                                @endforelse
                                <tr style="height: 25px; border: none; background: transparent;">
                                    <td colspan="6" style="border: none; padding: 10px;" class="text-center text-muted small fst-italic">
                                        Fin de la lista de productos
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Barra informativa inferior -->
                    <div class="bg-light p-2 px-3 border border-top-0 rounded-bottom d-flex justify-content-between align-items-center small text-muted" style="font-size: 0.75rem;">
                        <div>
                            <i class="bi bi-mouse-fill text-primary me-1"></i>
                            Haz clic en cualquier fila para seleccionarla. Ingresa la cantidad en la columna derecha.
                        </div>
                        <div id="txtResumenFisicoAjuste" class="fw-bold text-primary">
                            0 productos seleccionados
                        </div>
                    </div>
                </div>

                {{-- PASO 3: MOTIVO DEL AJUSTE (OBLIGATORIO) --}}
                <div class="mb-2">
                    <label class="form-label-custom d-flex justify-content-between align-items-center mb-1">
                        <span>
                            <span class="badge bg-primary text-white rounded-circle p-1 d-inline-flex align-items-center justify-content-center me-1" style="width: 20px; height: 20px; font-size: 0.74rem;">3</span>
                            <i class="bi bi-chat-text me-1"></i>Motivo del Ajuste *
                            <span class="text-danger ms-1">Obligatorio</span>
                        </span>
                        <small class="text-muted fw-normal">Elige un motivo rápido o escribe uno</small>
                    </label>

                    <!-- Chips de Motivo Rápido -->
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <button type="button" class="btn btn-sm btn-light border text-secondary fw-semibold py-0.5 px-2 chip-motivo" onclick="setMotivoRapido('Llegada de mercadería / Fábrica')">
                            🚚 Llegada de Mercadería / Fábrica
                        </button>
                        <button type="button" class="btn btn-sm btn-light border text-secondary fw-semibold py-0.5 px-2 chip-motivo" onclick="setMotivoRapido('Conteo físico / Regularización de inventario')">
                            📋 Conteo Físico / Regularización
                        </button>
                        <button type="button" class="btn btn-sm btn-light border text-secondary fw-semibold py-0.5 px-2 chip-motivo" onclick="setMotivoRapido('Devolución de cliente')">
                            ↩️ Devolución de Cliente
                        </button>
                        <button type="button" class="btn btn-sm btn-light border text-secondary fw-semibold py-0.5 px-2 chip-motivo" onclick="setMotivoRapido('Merma / Producto dañado o defectuoso')">
                            ⚠️ Merma / Daño
                        </button>
                    </div>

                    <textarea name="motivo" id="inputMotivoModal" rows="2"
                        class="form-control campo-requerido"
                        placeholder="Describe brevemente el motivo del ajuste (ej. Llegada de mercadería de fábrica, Devolución, Conteo físico...)"
                        style="border-radius:10px; resize:none; font-size: 0.88rem;"></textarea>
                    <div class="msg-error" id="errorMotivoModal" style="display: none;">
                        <i class="bi bi-exclamation-circle me-1"></i>El motivo es obligatorio para registrar el ajuste.
                    </div>
                </div>

            </div>

            {{-- Footer del modal --}}
            <div class="modal-footer bg-light border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-light border fw-bold" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancelar
                </button>
                <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm" id="btnGuardarAjusteModal">
                    <i class="bi bi-check-circle-fill me-1.5"></i>Guardar Ajuste (<span id="txtBtnGuardarCount">0</span>)
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Scripts --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. BUSCADOR EN TIEMPO REAL SOBRE LA TABLA PRINCIPAL
    // ============================================================
    const buscador = document.getElementById('buscadorInventario');
    if (buscador) {
        buscador.addEventListener('input', function() {
            filtrarTabla();
        });
    }

    // ============================================================
    // 2. ORDENAMIENTO DE COLUMNAS DE LA TABLA PRINCIPAL
    // ============================================================
    const tabla = document.getElementById('tablaInventario');
    if (tabla) {
        const headers = tabla.querySelectorAll('th[data-col]');
        let columnaActual = -1;
        let ordenAscendente = true;

        headers.forEach((header) => {
            header.addEventListener('click', () => {
                const index = parseInt(header.getAttribute('data-col'));
                ordenAscendente = (columnaActual === index) ? !ordenAscendente : true;
                columnaActual = index;

                headers.forEach(h => {
                    const icon = h.querySelector('.icono-orden');
                    if (icon) icon.className = 'icono-orden bi bi-arrow-down-up text-muted ms-1';
                });
                const iconoClass = ordenAscendente ? 'bi-caret-down-fill text-primary' : 'bi-caret-up-fill text-primary';
                const iconAct = header.querySelector('.icono-orden');
                if (iconAct) iconAct.className = `icono-orden bi ${iconoClass} ms-1`;

                const tbody = tabla.querySelector('tbody');
                const filas = Array.from(tbody.querySelectorAll('tr.fila-movimiento'));

                filas.sort((a, b) => {
                    let valA = a.children[index]?.getAttribute('data-sort-value') ?? '';
                    let valB = b.children[index]?.getAttribute('data-sort-value') ?? '';
                    if (!isNaN(valA) && !isNaN(valB) && valA !== '' && valB !== '') {
                        valA = parseFloat(valA); valB = parseFloat(valB);
                    } else {
                        valA = valA.toString().toLowerCase(); valB = valB.toString().toLowerCase();
                    }
                    if (valA < valB) return ordenAscendente ? -1 : 1;
                    if (valA > valB) return ordenAscendente ? 1 : -1;
                    return 0;
                });

                filas.forEach(f => tbody.appendChild(f));
            });
        });
    }

    // ============================================================
    // 3. LÓGICA DEL MODAL DE AJUSTE (ESTILO ADMINISTRACIÓN MASIVA)
    // ============================================================
    const buscadorModal = document.getElementById('buscadorModalProd');
    const filtroMarcaModal = document.getElementById('filtroMarcaModal');

    if (buscadorModal) buscadorModal.addEventListener('input', filtrarProductosModalAjuste);
    if (filtroMarcaModal) filtroMarcaModal.addEventListener('change', filtrarProductosModalAjuste);

    // Función ultrarrápida para actualizar contadores sin escanear el DOM
    function actualizarConteoModalAjusteDirecto(count) {
        const badge = document.getElementById('badgeConteoSeleccionadosAjuste');
        const txtBtn = document.getElementById('txtBtnGuardarCount');
        const txtResumen = document.getElementById('txtResumenFisicoAjuste');

        const texto = `${count} seleccionado${count === 1 ? '' : 's'}`;
        if (badge) badge.textContent = texto;
        if (txtBtn) txtBtn.textContent = `${count} producto${count === 1 ? '' : 's'}`;
        if (txtResumen) txtResumen.textContent = `${count} producto${count === 1 ? '' : 's'} seleccionado${count === 1 ? '' : 's'}`;
    }

    // Botón Marcar Visibles en el Modal (Optimizado: Acceso directo a celdas sin cuelgues)
    const btnMarcar = document.getElementById('btnMarcarVisiblesAjuste');
    if (btnMarcar) {
        btnMarcar.addEventListener('click', function() {
            const tbody = document.getElementById('tbodyModalProductos');
            if (!tbody) return;

            const cantGeneral = document.getElementById('inputCantidadGeneralAjuste');
            const valorGeneral = (cantGeneral && parseInt(cantGeneral.value) > 0) ? parseInt(cantGeneral.value) : 0;
            
            const rows = tbody.rows;
            let totalChecked = 0;

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                if (row.style.display === 'none') {
                    const chkH = row.cells[0]?.firstElementChild;
                    if (chkH && chkH.checked) totalChecked++;
                    continue;
                }

                row.classList.add('fila-seleccionada');
                const chk = row.cells[0]?.firstElementChild;
                if (chk) chk.checked = true;

                const inputCant = row.cells[5]?.firstElementChild;
                if (inputCant) {
                    inputCant.disabled = false;
                    if (valorGeneral > 0) {
                        inputCant.value = valorGeneral;
                    } else if (!inputCant.value || parseInt(inputCant.value) <= 0) {
                        inputCant.value = 0;
                    }
                }
                totalChecked++;
            }

            actualizarConteoModalAjusteDirecto(totalChecked);
        });
    }

    // Botón Desmarcar Todos en el Modal (Instantáneo: sólo itera filas seleccionadas)
    const btnDesmarcar = document.getElementById('btnDesmarcarTodosAjuste');
    if (btnDesmarcar) {
        btnDesmarcar.addEventListener('click', function() {
            const tbody = document.getElementById('tbodyModalProductos');
            if (!tbody) return;

            const seleccionados = tbody.querySelectorAll('.fila-seleccionada');
            for (let i = 0; i < seleccionados.length; i++) {
                const row = seleccionados[i];
                row.classList.remove('fila-seleccionada');
                const chk = row.cells[0]?.firstElementChild;
                if (chk) chk.checked = false;
                const inputCant = row.cells[5]?.firstElementChild;
                if (inputCant) {
                    inputCant.disabled = true;
                    inputCant.classList.remove('is-invalid', 'border-danger');
                }
            }

            actualizarConteoModalAjusteDirecto(0);
        });
    }

    // Botón Aplicar Cantidad General a los Seleccionados
    const btnAplicarCant = document.getElementById('btnAplicarCantidadGeneral');
    if (btnAplicarCant) {
        btnAplicarCant.addEventListener('click', function() {
            const cantGeneral = document.getElementById('inputCantidadGeneralAjuste');
            const val = cantGeneral ? parseInt(cantGeneral.value) : 0;
            if (isNaN(val) || val <= 0) {
                alert('Ingresa una cantidad mayor a 0 en la casilla superior para aplicar.');
                if (cantGeneral) {
                    cantGeneral.focus();
                    cantGeneral.select();
                }
                return;
            }
            let modificados = 0;
            document.querySelectorAll('.check-prod-ajuste:checked').forEach(chk => {
                const inputCant = document.getElementById('inputCantRow' + chk.value);
                if (inputCant) {
                    inputCant.value = val;
                    inputCant.classList.remove('is-invalid', 'border-danger');
                    modificados++;
                }
            });
            if (modificados === 0) {
                alert('Marca primero uno o varios productos en la tabla para aplicarles esta cantidad.');
            }
        });
    }

    // Listeners directos en checkboxes
    document.querySelectorAll('.check-prod-ajuste').forEach(chk => {
        chk.addEventListener('change', function() {
            actualizarEstadoFilaAjuste('checkAjuste' + this.value);
        });
    });

    // Validación del formulario de ajuste al guardar
    const formAjuste = document.getElementById('formNuevoAjuste');
    if (formAjuste) {
        formAjuste.addEventListener('submit', function(e) {
            const checks = document.querySelectorAll('.check-prod-ajuste:checked');
            if (checks.length === 0) {
                alert('Por favor selecciona al menos un producto de la tabla para registrar el ajuste.');
                e.preventDefault();
                return;
            }

            // Validar que ningún producto seleccionado quede con cantidad 0 o vacía
            let tieneCeros = false;
            let primerInvalido = null;

            checks.forEach(chk => {
                const inputCant = document.getElementById('inputCantRow' + chk.value);
                const val = inputCant ? parseInt(inputCant.value) : 0;
                if (!val || val <= 0) {
                    tieneCeros = true;
                    if (inputCant) {
                        inputCant.classList.add('is-invalid', 'border-danger');
                        if (!primerInvalido) primerInvalido = inputCant;
                    }
                } else {
                    if (inputCant) {
                        inputCant.classList.remove('is-invalid', 'border-danger');
                    }
                }
            });

            if (tieneCeros) {
                e.preventDefault();
                alert('¡Atención! La cantidad para cada producto seleccionado debe ser mayor a 0 (no puede quedar en 0).\n\nPor favor ingresa la cantidad correcta en la fila correspondiente.');
                if (primerInvalido) {
                    primerInvalido.focus();
                    primerInvalido.select();
                    primerInvalido.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            const motivo = document.getElementById('inputMotivoModal');
            const errMotivo = document.getElementById('errorMotivoModal');
            if (!motivo || !motivo.value.trim() || motivo.value.trim().length < 3) {
                if (motivo) motivo.classList.add('error');
                if (errMotivo) errMotivo.style.display = 'block';
                e.preventDefault();
                alert('El motivo del ajuste es obligatorio (mínimo 3 caracteres).');
                if (motivo) motivo.focus();
                return;
            } else {
                if (motivo) motivo.classList.remove('error');
                if (errMotivo) errMotivo.style.display = 'none';
            }
        });
    }

});

// Helper de Tipo en el Modal (Entrada vs Salida)
function seleccionarTipoAjuste(tipo) {
    const inputHidden = document.getElementById('tipoAjusteHidden');
    if (inputHidden) inputHidden.value = tipo;

    const btnE = document.getElementById('btnEntradaModal');
    const btnS = document.getElementById('btnSalidaModal');

    if (tipo === 'ENTRADA') {
        if (btnE) btnE.className = 'tipo-btn selected-entrada';
        if (btnS) btnS.className = 'tipo-btn';
    } else {
        if (btnE) btnE.className = 'tipo-btn';
        if (btnS) btnS.className = 'tipo-btn selected-salida';
    }
}

// Alternar selección de fila en la tabla del modal
function toggleCheckProdAjuste(event, checkId) {
    if (event.target.tagName === 'INPUT' && (event.target.type === 'checkbox' || event.target.type === 'number')) {
        actualizarEstadoFilaAjuste(checkId);
        return;
    }
    const chk = document.getElementById(checkId);
    if (chk) {
        chk.checked = !chk.checked;
        actualizarEstadoFilaAjuste(checkId);
    }
}

function actualizarEstadoFilaAjuste(checkId) {
    const chk = document.getElementById(checkId);
    if (!chk) return;

    const idProd = chk.value;
    const fila = chk.closest('tr');
    const inputCant = document.getElementById('inputCantRow' + idProd);
    const cantGeneral = document.getElementById('inputCantidadGeneralAjuste');
    const valorGeneral = (cantGeneral && parseInt(cantGeneral.value) > 0) ? parseInt(cantGeneral.value) : 0;

    if (chk.checked) {
        if (fila) fila.classList.add('fila-seleccionada');
        if (inputCant) {
            inputCant.disabled = false;
            if (valorGeneral > 0 && (!inputCant.value || parseInt(inputCant.value) <= 0)) {
                inputCant.value = valorGeneral;
            } else if (!inputCant.value) {
                inputCant.value = 0;
            }
            setTimeout(() => {
                inputCant.focus();
                inputCant.select();
            }, 50);
        }
    } else {
        if (fila) fila.classList.remove('fila-seleccionada');
        if (inputCant) {
            inputCant.disabled = true;
            inputCant.classList.remove('is-invalid', 'border-danger');
        }
    }

    actualizarConteoModalAjuste();
}

function actualizarConteoModalAjuste() {
    const checks = document.querySelectorAll('.check-prod-ajuste:checked');
    const count = checks.length;

    const badge = document.getElementById('badgeConteoSeleccionadosAjuste');
    const txtBtn = document.getElementById('txtBtnGuardarCount');
    const txtResumen = document.getElementById('txtResumenFisicoAjuste');

    if (badge) badge.textContent = `${count} seleccionado${count === 1 ? '' : 's'}`;
    if (txtBtn) txtBtn.textContent = `${count} producto${count === 1 ? '' : 's'}`;
    if (txtResumen) txtResumen.textContent = `${count} producto${count === 1 ? '' : 's'} seleccionado${count === 1 ? '' : 's'}`;
}

// Filtro en vivo en la tabla del modal (Multi-palabra y Marca)
function filtrarProductosModalAjuste() {
    const buscador = document.getElementById('buscadorModalProd');
    const filtroMarca = document.getElementById('filtroMarcaModal');
    const query = (buscador ? buscador.value : '').toLowerCase().trim();
    const marcaFiltro = (filtroMarca ? filtroMarca.value : 'todas').toLowerCase();
    const palabras = query.split(/\s+/).filter(Boolean);

    let visibles = 0;
    const filas = document.querySelectorAll('.fila-prod-ajuste');

    filas.forEach(fila => {
        const texto = (fila.getAttribute('data-texto') || '').toLowerCase();
        const marcaFila = (fila.getAttribute('data-marca') || '').toLowerCase();

        const matchTexto = palabras.length === 0 || palabras.every(p => texto.includes(p));
        const matchMarca = (marcaFiltro === 'todas') || (marcaFila === marcaFiltro);

        if (matchTexto && matchMarca) {
            fila.style.display = '';
            visibles++;
        } else {
            fila.style.display = 'none';
        }
    });

    const txtVisibles = document.getElementById('txtTotalVisiblesModal');
    if (txtVisibles) {
        txtVisibles.innerHTML = `Mostrando <strong class="text-dark">${visibles}</strong> producto(s)`;
    }
}

// Rellenar motivo con chip rápido
function setMotivoRapido(texto) {
    const input = document.getElementById('inputMotivoModal');
    const err = document.getElementById('errorMotivoModal');
    if (input) {
        input.value = texto;
        input.classList.remove('error');
    }
    if (err) err.style.display = 'none';
}

// Filtro exclusivo por tipo de movimiento mediante botones
let tipoFiltroActivo = '{{ strtolower(request('tipo', 'todos')) ?: 'todos' }}';

function filtrarTipo(tipo, btn) {
    tipoFiltroActivo = tipo;
    
    // Resetear clases de todos los botones de tipo
    document.querySelectorAll('.btn-filtro-tipo').forEach(b => {
        b.classList.remove('active', 'text-white', 'fw-bold', 'btn-secondary', 'btn-success', 'btn-danger');
        const txt = b.textContent.trim().toLowerCase();
        if (txt.includes('todos')) b.className = 'btn btn-sm btn-filtro-tipo btn-outline-secondary';
        else if (txt.includes('entradas')) b.className = 'btn btn-sm btn-filtro-tipo btn-outline-success';
        else if (txt.includes('salidas')) b.className = 'btn btn-sm btn-filtro-tipo btn-outline-danger';
        else if (txt.includes('devoluc')) {
            b.className = 'btn btn-sm btn-filtro-tipo';
            b.style.color = '#6d28d9';
            b.style.borderColor = '#c4b5fd';
            b.style.backgroundColor = '';
        }
    });

    // Activar el botón seleccionado
    if (btn) {
        btn.classList.add('active', 'text-white', 'fw-bold');
        if (tipo === 'todos') btn.classList.add('btn-secondary');
        else if (tipo === 'entrada') btn.classList.add('btn-success');
        else if (tipo === 'salida') btn.classList.add('btn-danger');
        else if (tipo === 'devolucion') {
            btn.style.backgroundColor = '#7c3aed';
            btn.style.borderColor = '#7c3aed';
            btn.style.color = '#ffffff';
        }
    }

    // Guardar en el input hidden para conservar el filtro si se recarga o envía el formulario
    const hiddenTipo = document.getElementById('inputHiddenTipo');
    if (hiddenTipo) hiddenTipo.value = (tipo === 'todos') ? '' : tipo;

    filtrarTabla();
}

// Filtro instantáneo usando el buscador único superior
function filtrarTabla() {
    const inputBuscar = document.getElementById('inputBuscarInventario');
    const q = (inputBuscar?.value ?? '').toLowerCase().trim();
    const palabras = q.split(/\s+/).filter(Boolean);
    let visibles = 0;

    const filas = document.querySelectorAll('.fila-movimiento');
    for (let i = 0; i < filas.length; i++) {
        const fila = filas[i];
        const texto    = fila.textContent.toLowerCase();
        const tipoFila = (fila.dataset.tipo ?? '').toLowerCase();
        const okTexto  = palabras.length === 0 || palabras.every(p => texto.includes(p));
        const okTipo   = tipoFiltroActivo === 'todos' || tipoFila === tipoFiltroActivo;

        if (okTexto && okTipo) {
            fila.style.display = '';
            visibles++;
        } else {
            fila.style.display = 'none';
        }
    }

    const sinRes = document.getElementById('sinResultados');
    if (sinRes) sinRes.style.display = visibles === 0 ? '' : 'none';
    const bTotal = document.getElementById('badgeTotal');
    if (bTotal) bTotal.textContent = visibles;
}

// Conectar buscador único superior para filtrado dinámico en pantalla
document.addEventListener('DOMContentLoaded', function() {
    const inputBuscar = document.getElementById('inputBuscarInventario');
    if (inputBuscar) {
        inputBuscar.addEventListener('input', filtrarTabla);
    }
});
</script>

@endsection