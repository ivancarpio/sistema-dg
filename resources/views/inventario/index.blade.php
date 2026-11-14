@extends('layouts.app')

@section('content')

{{-- TomSelect para buscador del modal --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

<style>
/* ===== INVENTARIO — Diseño coherente con el sistema ===== */
.card { border: none !important; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important; border-radius: .75rem !important; }

/* Cabeceras de tabla */
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

/* Modal rediseñado */
.modal-header-custom {
    background: linear-gradient(135deg, #1e40af, #0ea5e9);
    padding: 20px 24px; border-radius: 12px 12px 0 0;
}
.modal-content { border: none !important; border-radius: 14px !important; overflow: hidden; }
.form-label-custom { font-size: 0.78rem; font-weight: 800; text-transform: uppercase;
    letter-spacing: 0.5px; color: #475569; margin-bottom: 6px; }

/* Tipo de ajuste: botones visuales en lugar de select */
.tipo-btn { border: 2px solid #e2e8f0; border-radius: 10px; padding: 12px 10px;
    cursor: pointer; transition: all 0.2s; text-align: center; background: #f8fafc; }
.tipo-btn.selected-entrada { border-color: #16a34a; background: #f0fdf4; }
.tipo-btn.selected-salida  { border-color: #dc2626; background: #fff1f2; }
.tipo-btn .tipo-icon { font-size: 1.5rem; margin-bottom: 4px; }
.tipo-btn .tipo-label { font-size: 0.8rem; font-weight: 700; }

/* Campo motivo con asterisco en rojo cuando está vacío */
.campo-requerido.error { border-color: #ef4444 !important; box-shadow: 0 0 0 3px rgba(239,68,68,0.1) !important; }
.msg-error { color: #dc2626; font-size: 0.78rem; font-weight: 600; margin-top: 4px; display: none; }
</style>

{{-- ===== ALERTAS ===== --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ===== ENCABEZADO ===== --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="bg-info bg-opacity-10 p-2 rounded text-info">
            <i class="bi bi-diagram-3-fill fs-3"></i>
        </div>
        <div>
            <h3 class="fw-bold m-0 text-dark">Control de Inventario</h3>
            <small class="text-muted">Historial de entradas, salidas y ajustes de stock</small>
        </div>
    </div>
    <button class="btn btn-primary fw-bold shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#modalNuevoAjuste">
        <i class="bi bi-plus-circle-fill me-2"></i>Nuevo Ajuste
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

{{-- ===== FILTROS Y BÚSQUEDA AVANZADA (ESTILO VENTAS/REPORTES) ===== --}}
<div class="card p-3 mb-4 shadow-sm border-0 bg-white">
    <form action="/inventario" method="GET" class="row g-2 align-items-end">
        <!-- Buscador rápido -->
        <div class="col-md-4">
            <label class="form-label small fw-bold text-muted mb-1">Buscar Producto o Motivo</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" name="buscar" class="form-control border-start-0 bg-light" 
                       placeholder="Ej. Tubo, Alambre, Ajuste..." value="{{ request('buscar') }}">
            </div>
        </div>

        <!-- Fecha específica (por defecto Hoy) -->
        <div class="col-md-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label small fw-bold text-muted mb-0">Fecha de Movimientos</label>
                @if(!$verTodo && $fechaFiltro === now()->toDateString())
                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle" style="font-size: 0.65rem;">Hoy</span>
                @elseif($verTodo)
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle" style="font-size: 0.65rem;">Histórico</span>
                @endif
            </div>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-calendar-event"></i></span>
                <input type="date" name="fecha" id="inputFiltroFecha" class="form-control border-start-0 bg-light" 
                       value="{{ $verTodo ? '' : ($fechaFiltro ?? now()->toDateString()) }}">
            </div>
        </div>

        <!-- Tipo de Movimiento -->
        <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-1">Tipo de Movimiento</label>
            <select name="tipo" class="form-select bg-light">
                <option value="">Todos los tipos</option>
                <option value="Entrada" {{ request('tipo') == 'Entrada' ? 'selected' : '' }}>🟢 Entradas</option>
                <option value="Salida" {{ request('tipo') == 'Salida' ? 'selected' : '' }}>🔴 Salidas</option>
                <option value="Devolucion" {{ request('tipo') == 'Devolucion' ? 'selected' : '' }}>🟣 Devoluciones</option>
            </select>
        </div>

        <!-- Botones de Acción -->
        <div class="col-md-3 d-flex gap-1">
            <button type="submit" class="btn btn-primary text-white flex-grow-1 fw-bold shadow-sm d-flex align-items-center justify-content-center" style="height: 38px;">
                <i class="bi bi-funnel-fill me-1"></i> Filtrar
            </button>
            <a href="/inventario" class="btn btn-outline-success fw-semibold d-flex align-items-center justify-content-center px-2" title="Ver movimientos de Hoy" style="height: 38px; white-space: nowrap; font-size: 0.82rem;">
                <i class="bi bi-calendar-check me-1"></i> Hoy
            </a>
            <a href="/inventario?ver=todos" class="btn btn-outline-secondary fw-semibold d-flex align-items-center justify-content-center px-2" title="Ver todo el historial acumulado" style="height: 38px; white-space: nowrap; font-size: 0.82rem;">
                Histórico
            </a>
        </div>
    </form>
</div>

{{-- ===== TARJETA PRINCIPAL CON TABLA ===== --}}
<div class="card">

    {{-- Cabecera con buscador y filtros --}}
    <div class="card-header bg-white border-0 py-3">
        <div class="row g-2 align-items-center">
            <div class="col-md-5">
                <h6 class="fw-bold m-0 text-dark">
                    <i class="bi bi-list-ul me-2 text-primary"></i>Historial de Movimientos
                    <span class="badge bg-primary ms-1" id="badgeTotal">{{ $totalMovimientos }}</span>
                </h6>
                <small class="text-muted">Mostrando: <strong class="text-dark">{{ $subtituloPeriodo }}</strong> • Cada día nuevo inicia en 0</small>
            </div>
            <div class="col-md-4">
                {{-- Buscador en tiempo real sobre la tabla --}}
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="buscadorInventario"
                        class="form-control border-start-0 bg-light"
                        placeholder="Filtro rápido en pantalla...">
                </div>
            </div>
            <div class="col-md-3">
                {{-- Filtro rápido por tipo --}}
                <div class="btn-group w-100" role="group">
                    <button type="button" class="btn btn-sm btn-outline-secondary active"
                        onclick="filtrarTipo('todos', this)">
                        Todos
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success"
                        onclick="filtrarTipo('entrada', this)">
                        <i class="bi bi-arrow-up"></i> Entradas
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger"
                        onclick="filtrarTipo('salida', this)">
                        <i class="bi bi-arrow-down"></i> Salidas
                    </button>
                    <button type="button" class="btn btn-sm"
                        style="color: #6d28d9; border: 1px solid #c4b5fd;"
                        onclick="filtrarTipo('devolucion', this)">
                        <i class="bi bi-arrow-counterclockwise"></i> Devoluc.
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
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
                                @if(!$verTodo && $fechaFiltro === now()->toDateString())
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

{{-- ============================================================ --}}
{{-- MODAL — NUEVO AJUSTE (Rediseñado + Validación de Motivo)      --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalNuevoAjuste" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
        <div class="modal-content">

            {{-- Header del modal --}}
            <div class="modal-header-custom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div style="background:rgba(255,255,255,0.15); padding:10px; border-radius:10px;">
                        <i class="bi bi-sliders2 text-white fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-white m-0">Registrar Nuevo Ajuste</h5>
                        <small class="text-white opacity-75">Entrada o salida manual de stock</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="/inventario" method="POST" id="formNuevoAjuste" novalidate>
                @csrf
                <div class="modal-body p-4">

                    {{-- 1. Producto --}}
                    <div class="mb-4">
                        <label class="form-label-custom">
                            <i class="bi bi-box-seam me-1"></i>Seleccionar Producto *
                        </label>
                        <select name="id_producto" id="selectProductoModal" required>
                            <option value="" disabled selected>Escribe para buscar un producto...</option>
                            @foreach($productos as $p)
                                <option value="{{ $p->id }}">{{ $p->codigo }} — {{ $p->nombre }}</option>
                            @endforeach
                        </select>
                        <div class="msg-error" id="errorProducto">
                            <i class="bi bi-exclamation-circle me-1"></i>Seleccioná un producto.
                        </div>
                    </div>

                    {{-- 2. Tipo de ajuste (botones visuales) --}}
                    <div class="mb-4">
                        <label class="form-label-custom">
                            <i class="bi bi-arrow-left-right me-1"></i>Tipo de Ajuste *
                        </label>
                        <input type="hidden" name="tipo" id="tipoAjusteHidden" value="ENTRADA">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="tipo-btn selected-entrada" id="btnEntrada" onclick="seleccionarTipo('ENTRADA')">
                                    <div class="tipo-icon text-success"><i class="bi bi-arrow-up-circle-fill"></i></div>
                                    <div class="tipo-label text-success">Agregar (Entrada)</div>
                                    <small class="text-muted">Suma stock</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="tipo-btn" id="btnSalida" onclick="seleccionarTipo('SALIDA')">
                                    <div class="tipo-icon text-danger"><i class="bi bi-arrow-down-circle-fill"></i></div>
                                    <div class="tipo-label text-danger">Restar (Salida)</div>
                                    <small class="text-muted">Resta stock</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Cantidad --}}
                    <div class="mb-4">
                        <label class="form-label-custom">
                            <i class="bi bi-123 me-1"></i>Cantidad *
                        </label>
                        <input type="number" name="cantidad" id="inputCantidad"
                            class="form-control form-control-lg campo-requerido"
                            min="1" placeholder="Ej: 10" required
                            style="border-radius:10px;">
                        <div class="msg-error" id="errorCantidad">
                            <i class="bi bi-exclamation-circle me-1"></i>Ingresá una cantidad mayor a 0.
                        </div>
                    </div>

                    {{-- 4. Motivo — OBLIGATORIO --}}
                    <div class="mb-1">
                        <label class="form-label-custom">
                            <i class="bi bi-chat-text me-1"></i>Motivo del Ajuste *
                            <span class="text-danger ms-1">Obligatorio</span>
                        </label>
                        <textarea name="motivo" id="inputMotivo" rows="3"
                            class="form-control campo-requerido"
                            placeholder="Ej: Llegó mercadería de Santa Cruz, Devolución de cliente, Ajuste de inventario mensual..."
                            style="border-radius:10px; resize:none;"></textarea>
                        <div class="msg-error" id="errorMotivo">
                            <i class="bi bi-exclamation-circle me-1"></i>El motivo es obligatorio para registrar el ajuste.
                        </div>
                        <small class="text-muted mt-1 d-block">
                            <i class="bi bi-info-circle me-1"></i>
                            Describí brevemente por qué se hace este ajuste. Es importante para la auditoría.
                        </small>
                    </div>

                </div>

                {{-- Footer del modal --}}
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-light border fw-bold" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary fw-bold px-4" id="btnGuardarAjuste">
                        <i class="bi bi-check-circle-fill me-2"></i>Guardar Ajuste
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // =========================================
    // 1. TomSelect para el selector de producto
    // =========================================
    new TomSelect("#selectProductoModal", {
        create: false,
        placeholder: "Escribe para buscar un producto...",
        sortField: { field: "text", direction: "asc" }
    });

    // =========================================
    // 2. VALIDACIÓN DEL FORMULARIO (motivo obligatorio)
    // =========================================
    document.getElementById('formNuevoAjuste').addEventListener('submit', function(e) {
        let valido = true;

        // Validar Cantidad
        const cantidad = document.getElementById('inputCantidad');
        const errCantidad = document.getElementById('errorCantidad');
        if (!cantidad.value || parseInt(cantidad.value) < 1) {
            cantidad.classList.add('error');
            errCantidad.style.display = 'block';
            valido = false;
        } else {
            cantidad.classList.remove('error');
            errCantidad.style.display = 'none';
        }

        // Validar Motivo — OBLIGATORIO
        const motivo = document.getElementById('inputMotivo');
        const errMotivo = document.getElementById('errorMotivo');
        if (!motivo.value.trim() || motivo.value.trim().length < 3) {
            motivo.classList.add('error');
            errMotivo.style.display = 'block';
            valido = false;
        } else {
            motivo.classList.remove('error');
            errMotivo.style.display = 'none';
        }

        if (!valido) {
            e.preventDefault();
            // Hacer scroll al primer error
            document.querySelector('.campo-requerido.error')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    // Limpiar errores al escribir
    document.getElementById('inputMotivo').addEventListener('input', function() {
        if (this.value.trim().length >= 3) {
            this.classList.remove('error');
            document.getElementById('errorMotivo').style.display = 'none';
        }
    });
    document.getElementById('inputCantidad').addEventListener('input', function() {
        if (this.value && parseInt(this.value) >= 1) {
            this.classList.remove('error');
            document.getElementById('errorCantidad').style.display = 'none';
        }
    });

    // =========================================
    // 3. BUSCADOR EN TIEMPO REAL
    // =========================================
    const buscador = document.getElementById('buscadorInventario');
    buscador.addEventListener('input', function() {
        filtrarTabla();
    });

    // =========================================
    // 4. ORDENAMIENTO DE COLUMNAS
    // =========================================
    const tabla = document.getElementById('tablaInventario');
    const headers = tabla.querySelectorAll('th[data-col]');
    let columnaActual = -1;
    let ordenAscendente = true;

    headers.forEach((header) => {
        header.addEventListener('click', () => {
            const index = parseInt(header.getAttribute('data-col'));
            ordenAscendente = (columnaActual === index) ? !ordenAscendente : true;
            columnaActual = index;

            headers.forEach(h => {
                h.querySelector('.icono-orden').className = 'icono-orden bi bi-arrow-down-up text-muted ms-1';
            });
            const iconoClass = ordenAscendente ? 'bi-caret-down-fill text-primary' : 'bi-caret-up-fill text-primary';
            header.querySelector('.icono-orden').className = `icono-orden bi ${iconoClass} ms-1`;

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

});

// =========================================
// 5. SELECTOR DE TIPO VISUAL (Entrada/Salida)
// =========================================
function seleccionarTipo(tipo) {
    document.getElementById('tipoAjusteHidden').value = tipo;

    const btnE = document.getElementById('btnEntrada');
    const btnS = document.getElementById('btnSalida');

    if (tipo === 'ENTRADA') {
        btnE.className = 'tipo-btn selected-entrada';
        btnS.className = 'tipo-btn';
    } else {
        btnE.className = 'tipo-btn';
        btnS.className = 'tipo-btn selected-salida';
    }
}

// =========================================
// 6. FILTRO POR TIPO EN LA TABLA
// =========================================
let tipoFiltroActivo = 'todos';

function filtrarTipo(tipo, btn) {
    tipoFiltroActivo = tipo;
    btn.closest('.btn-group').querySelectorAll('.btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    filtrarTabla();
}

function filtrarTabla() {
    const q = (document.getElementById('buscadorInventario')?.value ?? '').toLowerCase().trim();
    let visibles = 0;

    document.querySelectorAll('.fila-movimiento').forEach(fila => {
        const texto       = fila.textContent.toLowerCase();
        const tipoFila    = fila.dataset.tipo ?? '';
        const okTexto     = !q || texto.includes(q);
        const okTipo      = tipoFiltroActivo === 'todos' || tipoFila === tipoFiltroActivo;

        if (okTexto && okTipo) {
            fila.style.display = '';
            visibles++;
        } else {
            fila.style.display = 'none';
        }
    });

    const sinRes = document.getElementById('sinResultados');
    sinRes.style.display = visibles === 0 ? '' : 'none';
    document.getElementById('badgeTotal').textContent = visibles;
}
</script>

@endsection
