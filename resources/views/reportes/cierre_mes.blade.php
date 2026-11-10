@extends('layouts.app')

@section('content')

<style>
    /* Estilos del Módulo de Cierre de Mes (Auditoría y Cuadre de Stock) */
    .card { border: none !important; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important; border-radius: .75rem !important; }

    /* Cabecera ejecutiva y profesional estilo SaaS */
    .tabla-cierre thead th {
        background-color: #1e293b !important;
        color: #f8fafc !important;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 13px 14px;
        border: none !important;
        border-bottom: 2px solid #334155 !important;
    }
    .tabla-cierre thead th:first-child { border-top-left-radius: 8px; }
    .tabla-cierre thead th:last-child { border-top-right-radius: 8px; }

    /* Fila de datos */
    .tabla-cierre tbody tr {
        transition: background-color 0.15s;
        border-bottom: 1px solid #e2e8f0;
    }
    .tabla-cierre tbody tr:hover {
        background-color: #f8fafc !important;
    }
    .tabla-cierre td {
        vertical-align: middle;
        padding: 10px 14px;
        font-size: 0.88rem;
    }

    /* Fila verificada (cuadrada) */
    .fila-cuadrada {
        background-color: rgba(240, 253, 244, 0.65) !important;
    }

    /* Botón / Check interactivo "OK" */
    .btn-check-ok {
        border-radius: 8px;
        padding: 6px 14px;
        font-weight: 700;
        font-size: 0.82rem;
        letter-spacing: 0.5px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        user-select: none;
    }
    /* Estado OK activado (verde sobrio ejecutivo) */
    .btn-check-ok.is-ok {
        background-color: #16a34a;
        color: #ffffff;
        border-color: #15803d;
        box-shadow: 0 2px 4px rgba(22, 163, 74, 0.2);
    }
    .btn-check-ok.is-ok:hover {
        background-color: #15803d;
        transform: translateY(-1px);
    }
    /* Estado pendiente (gris neutro) */
    .btn-check-ok.is-pending {
        background-color: #f1f5f9;
        color: #64748b;
        border-color: #cbd5e1;
    }
    .btn-check-ok.is-pending:hover {
        background-color: #e2e8f0;
        color: #334155;
        border-color: #94a3b8;
    }
    /* Estado deshabilitado si el mes está cerrado */
    .btn-check-ok.is-disabled {
        cursor: not-allowed;
        opacity: 0.85;
    }

    /* Tarjetas de Estadísticas */
    .metric-cierre {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px 20px;
        border: 1px solid #e2e8f0;
    }
    .metric-cierre .label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
    }
    .metric-cierre .value {
        font-size: 1.65rem;
        font-weight: 800;
        color: #0f172a;
        margin-top: 4px;
        line-height: 1;
    }

    /* Barra de progreso de verificación */
    .progress-bar-cierre {
        height: 10px;
        border-radius: 20px;
        background-color: #e2e8f0;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #0284c7, #16a34a);
        transition: width 0.3s ease;
    }

    /* Estilos de tabla de historial */
    .tabla-historial th {
        background-color: #f8fafc !important;
        font-size: 0.75rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
    }
    .tabla-historial td {
        vertical-align: middle;
        padding: 12px 14px;
        font-size: 0.85rem;
    }
</style>

{{-- Alertas de éxito o error --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5 align-middle"></i>
        <strong>¡Éxito!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="bi bi-info-circle-fill me-2 fs-5 align-middle"></i>
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5 align-middle"></i>
        <strong>Error:</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ===== NAVEGACIÓN Y TÍTULO ===== --}}
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="d-flex align-items-center">
        <div class="bg-primary bg-opacity-10 p-2 rounded me-3 text-primary">
            <i class="bi bi-calendar2-check-fill fs-3"></i>
        </div>
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:0.8rem;">
                    <li class="breadcrumb-item"><a href="/reportes" class="text-muted text-decoration-none">Reportes</a></li>
                    <li class="breadcrumb-item active">Cierre de Mes</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2">
                <h3 class="fw-bold m-0 text-dark">Auditoría y Cierre de Mes</h3>
                @if($cierreActual && $cierreActual->isCerrado())
                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle fw-bold px-2 py-1">
                        <i class="bi bi-shield-check me-1"></i> Mes Cerrado
                    </span>
                @else
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle fw-bold px-2 py-1">
                        <i class="bi bi-clock me-1"></i> En Cuadre
                    </span>
                @endif
            </div>
            <small class="text-muted">Período auditado: {{ \Carbon\Carbon::parse($mesSeleccionado . '-01')->translatedFormat('F Y') }} ({{ $inicioMes->format('d/m/Y') }} al {{ $finMes->format('d/m/Y') }})</small>
        </div>
    </div>
    
    <div class="d-flex flex-wrap gap-2">
        <a href="/reportes" class="btn btn-outline-secondary fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Volver a Reportes
        </a>
        
        {{-- Botón Exportar Excel --}}
        <a href="/reportes/cierre-mes/excel?mes={{ $mesSeleccionado }}" id="btnExportarExcel"
           class="btn fw-bold shadow-sm px-3 text-white"
           style="background-color: #16a34a; border-radius: 8px;">
            <i class="bi bi-file-earmark-excel-fill me-1"></i> Exportar a Excel
        </a>

        {{-- Botón Cerrar Mes (Rojo destacado) o Badge de Mes Cerrado --}}
        @if($cierreActual && $cierreActual->isCerrado())
            @if(Auth::user() && Auth::user()->rol === 'admin')
                <button type="button" class="btn btn-outline-danger fw-bold shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#modalReabrirMes">
                    <i class="bi bi-unlock-fill me-1"></i> Reabrir Mes (Admin)
                </button>
            @endif
        @else
            <button type="button" class="btn btn-danger fw-bold shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#modalConfirmarCierreMes">
                <i class="bi bi-lock-fill me-1"></i> Cerrar Mes Oficial
            </button>
        @endif
    </div>
</div>

{{-- ===== BANNER INFORMATIVO SI EL MES YA ESTÁ CERRADO ===== --}}
@if($cierreActual && $cierreActual->isCerrado())
<div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #0f172a, #1e293b); color: #ffffff; border-radius: 12px;">
    <div class="card-body p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center">
            <div class="bg-success bg-opacity-20 text-success p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                <i class="bi bi-shield-lock-fill fs-3"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-success text-white fw-bold px-2 py-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        AUDITORÍA CONGELADA & ARCHIVADA
                    </span>
                    <span class="text-white-50 small">•</span>
                    <span class="text-white-50 small">Cerrado el {{ \Carbon\Carbon::parse($cierreActual->fecha_cierre)->format('d/m/Y H:i') }}</span>
                </div>
                <h5 class="fw-bold mb-1 text-white">
                    El mes de {{ \Carbon\Carbon::parse($mesSeleccionado . '-01')->translatedFormat('F Y') }} fue cerrado oficialmente
                </h5>
                <div class="small text-white-50">
                    Responsable del Cierre: <strong class="text-white">{{ optional($cierreActual->usuario)->name ?? 'Administrador' }}</strong>
                    &nbsp;|&nbsp; 
                    Resultado: <strong class="text-success">{{ $cierreActual->verificados_count }} de {{ $cierreActual->total_productos }} productos verificados ({{ $cierreActual->porcentaje }}%)</strong>
                </div>
                @if($cierreActual->notas)
                    <div class="small mt-2 p-2 rounded bg-white bg-opacity-10 text-white border border-light border-opacity-10">
                        <i class="bi bi-chat-quote me-1 text-info"></i> <strong>Observación:</strong> {{ $cierreActual->notas }}
                    </div>
                @endif
            </div>
        </div>
        <div class="text-end">
            <span class="d-inline-block p-2 px-3 rounded-3 bg-white bg-opacity-10 text-white-50 small">
                <i class="bi bi-info-circle me-1 text-info"></i> Las cifras reflejan la instantánea exacta registrada al momento del cierre.
            </span>
        </div>
    </div>
</div>
@endif

{{-- ===== TARJETAS DE MÉTRICAS Y PROGRESO ===== --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="metric-cierre h-100">
            <div class="label"><i class="bi bi-box-seam me-1 text-primary"></i> Total Productos</div>
            <div class="value">{{ $totalProductos }}</div>
            <small class="text-muted">en catálogo de ferretería</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="metric-cierre h-100">
            <div class="label"><i class="bi bi-cart-check me-1 text-info"></i> Ventas del Mes</div>
            <div class="value text-primary">{{ number_format($totalVendidos, 0) }}</div>
            <small class="text-muted">unidades vendidas en total</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="metric-cierre h-100">
            <div class="label"><i class="bi bi-check2-circle me-1 text-success"></i> Cuadrados (OK)</div>
            <div class="value text-success" id="statVerificados">{{ $verificadosCount }}</div>
            <small class="text-muted"><span id="statPorcentaje">{{ $porcentajeVerificado }}%</span> verificado físicamente</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="metric-cierre h-100">
            <div class="label"><i class="bi bi-hourglass-split me-1 text-secondary"></i> Pendientes de Revisar</div>
            <div class="value text-secondary" id="statPendientes">{{ $pendientesCount }}</div>
            <small class="text-muted">por confirmar stock</small>
        </div>
    </div>
</div>

{{-- BARRA DE PROGRESO DE VERIFICACIÓN --}}
<div class="card p-3 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
        <span class="fw-bold text-dark small">
            <i class="bi bi-speedometer2 me-1 text-primary"></i> Progreso de Cuadre Físico en Tienda:
            <span id="labelProgresoTexto" class="text-primary">{{ $verificadosCount }} de {{ $totalProductos }} productos ({{ $porcentajeVerificado }}%)</span>
        </span>
        @if(!$cierreActual || !$cierreActual->isCerrado())
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-primary fw-bold" onclick="marcarTodos('marcar')">
                <i class="bi bi-check-all me-1"></i> Marcar todos como OK
            </button>
            <button class="btn btn-sm btn-outline-secondary fw-bold" onclick="marcarTodos('desmarcar')">
                <i class="bi bi-x-circle me-1"></i> Desmarcar todos
            </button>
        </div>
        @else
        <span class="badge bg-light text-muted border px-2 py-1 small">
            <i class="bi bi-lock-fill me-1"></i> Acciones masivas bloqueadas (Mes Cerrado)
        </span>
        @endif
    </div>
    <div class="progress-bar-cierre">
        <div class="progress-bar-fill" id="barraProgreso" style="width: {{ $porcentajeVerificado }}%;"></div>
    </div>
</div>

{{-- ===== FILTROS Y BUSCADOR ===== --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-center">
            {{-- Selector de Mes --}}
            <div class="col-md-3">
                <label class="form-label small text-muted fw-bold m-0 mb-1">
                    <i class="bi bi-calendar-month me-1"></i> Mes a Auditar
                </label>
                <input type="month" id="selectorMes" class="form-control bg-light"
                       value="{{ $mesSeleccionado }}"
                       onchange="cambiarMes(this.value)">
            </div>

            {{-- Buscador en tiempo real --}}
            <div class="col-md-5">
                <label class="form-label small text-muted fw-bold m-0 mb-1">
                    <i class="bi bi-search me-1"></i> Buscar Producto
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="buscadorProductos" class="form-control border-start-0 bg-light"
                           placeholder="Escribe código o nombre del producto (ej: Codo, Tubo, Cable)...">
                </div>
            </div>

            {{-- Filtro por estado --}}
            <div class="col-md-4">
                <label class="form-label small text-muted fw-bold m-0 mb-1">
                    <i class="bi bi-funnel me-1"></i> Filtrar por Estado
                </label>
                <div class="btn-group w-100" role="group">
                    <button type="button" class="btn btn-sm btn-outline-secondary active" onclick="filtrarEstado('todos', this)">Todos ({{ $totalProductos }})</button>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="filtrarEstado('ok', this)">Solo OK</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="filtrarEstado('pendiente', this)">Pendientes</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== TABLA PRINCIPAL DE CUADRE (COLORES PROFESIONALES) ===== --}}
<div class="card mb-4">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-bold m-0 text-dark">
                <i class="bi bi-table me-2 text-primary"></i> Cuadre de Cantidades Físicas
            </h6>
            <small class="text-muted">
                Fórmula de cuadre: <strong>CANTIDAD ANTERIOR</strong> (stock inicial) - <strong>VENTAS</strong> (del período) = <strong>TOTAL EN TIENDA</strong> (stock actual)
            </small>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 fw-bold" id="badgeMostrados">
            Mostrando {{ $totalProductos }} productos
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table tabla-cierre align-middle m-0" id="tablaCierre">
                <thead>
                    <tr>
                        <th style="width: 130px; text-align: center;">CÓDIGO</th>
                        <th>PRODUCTO</th>
                        <th style="width: 140px; text-align: center;" title="Stock anterior o inicial del período">
                            CANTIDAD ANTERIOR
                        </th>
                        <th style="width: 130px; text-align: center;" title="Ventas realizadas durante este mes">
                            VENTAS MES
                        </th>
                        <th style="width: 160px; text-align: center;" title="Stock físico actual registrado en tienda">
                            TOTAL EN TIENDA
                        </th>
                        <th style="width: 130px; text-align: center;" title="Verificación física en estantería">
                            ESTADO CHECK
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr class="fila-producto {{ $item['verificado'] ? 'fila-cuadrada' : '' }}"
                        id="fila-{{ $item['id'] }}"
                        data-id="{{ $item['id'] }}"
                        data-codigo="{{ strtolower($item['codigo']) }}"
                        data-nombre="{{ strtolower($item['nombre']) }}"
                        data-estado="{{ $item['verificado'] ? 'ok' : 'pendiente' }}">
                        
                        {{-- CÓDIGO --}}
                        <td class="text-center">
                            <span class="badge fw-bold" style="background:#f1f5f9; color:#334155; border: 1px solid #cbd5e1; font-size: 0.82rem;">
                                {{ $item['codigo'] }}
                            </span>
                        </td>

                        {{-- PRODUCTO --}}
                        <td class="fw-semibold text-dark">
                            {{ $item['nombre'] }}
                        </td>

                        {{-- CANTIDAD (Stock anterior / inicial) --}}
                        <td class="text-center fw-bold fs-6 text-secondary">
                            {{ $item['cantidad_anterior'] }}
                        </td>

                        {{-- VENTA (mes) --}}
                        <td class="text-center fw-bold fs-6">
                            @if($item['venta'] > 0)
                                <span class="badge px-3 py-1" style="background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;">
                                    {{ $item['venta'] }}
                                </span>
                            @else
                                <span class="text-muted opacity-50">—</span>
                            @endif
                        </td>

                        {{-- TOTAL EN TIENDA (stock actual) --}}
                        <td class="text-center fw-bold fs-6 text-dark">
                            {{ $item['total_tienda'] }}
                        </td>

                        {{-- CHECK / OK INTERACTIVO --}}
                        <td class="text-center">
                            @if($cierreActual && $cierreActual->isCerrado())
                                <button type="button"
                                        class="btn-check-ok {{ $item['verificado'] ? 'is-ok' : 'is-pending' }} is-disabled"
                                        title="Mes cerrado oficialmente. No se pueden modificar los checks.">
                                    <i class="bi {{ $item['verificado'] ? 'bi-check-lg' : 'bi-dash-circle' }}"></i>
                                    <span class="label-ok">{{ $item['verificado'] ? 'OK' : 'Pendiente' }}</span>
                                </button>
                            @else
                                <button type="button"
                                        class="btn-check-ok {{ $item['verificado'] ? 'is-ok' : 'is-pending' }}"
                                        id="btn-check-{{ $item['id'] }}"
                                        onclick="toggleCheck({{ $item['id'] }})"
                                        title="Haz clic para marcar o desmarcar la verificación física">
                                    <i class="bi {{ $item['verificado'] ? 'bi-check-lg' : 'bi-circle' }}"></i>
                                    <span class="label-ok">{{ $item['verificado'] ? 'OK' : 'Revisar' }}</span>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                            <p class="fw-bold mb-1">No hay productos registrados en el inventario</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                {{-- Fila de Totales --}}
                <tfoot>
                    <tr style="background: #f8fafc; border-top: 2px solid #cbd5e1; font-weight: 800;">
                        <td colspan="2" class="ps-4 text-dark text-uppercase" style="font-size:0.85rem;">
                            Totales Consolidados del Período
                        </td>
                        <td class="text-center fs-6 text-secondary">
                            {{ number_format($totalStockAnterior, 0) }}
                        </td>
                        <td class="text-center fs-6 text-primary">
                            {{ number_format($totalVendidos, 0) }}
                        </td>
                        <td class="text-center fs-6 text-dark">
                            {{ number_format($totalStockTienda, 0) }}
                        </td>
                        <td class="text-center text-success" id="pieVerificados">
                            {{ $verificadosCount }} OK
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{{-- ===== SECCIÓN HISTORIAL DE CIERRES DE MES (PERSISTENCIA Y AUDITORÍA) ===== --}}
<div class="card mt-4 border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h6 class="fw-bold m-0 text-dark">
                <i class="bi bi-clock-history me-2 text-primary"></i> Historial de Cierres de Mes Oficiales
            </h6>
            <small class="text-muted">Registro auditado e inmutable de meses cerrados</small>
        </div>
        <span class="badge bg-secondary bg-opacity-10 text-secondary border px-3 py-1 fw-bold">
            {{ $historialCierres->count() }} mes(es) cerrado(s)
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table tabla-historial align-middle m-0">
                <thead>
                    <tr>
                        <th>PERÍODO</th>
                        <th>FECHA DE CIERRE</th>
                        <th>RESPONSABLE</th>
                        <th class="text-center">PRODUCTOS CUADRADOS</th>
                        <th class="text-center">VENTAS MES</th>
                        <th class="text-center">STOCK EN TIENDA</th>
                        <th class="text-center">ESTADO</th>
                        <th class="text-end">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historialCierres as $h)
                    @php
                        $fechaMes = \Carbon\Carbon::parse($h->mes . '-01');
                        $esMesSeleccionado = ($h->mes === $mesSeleccionado);
                    @endphp
                    <tr class="{{ $esMesSeleccionado ? 'bg-light bg-opacity-50' : '' }}">
                        <td>
                            <strong class="text-dark">{{ $fechaMes->translatedFormat('F Y') }}</strong>
                            @if($esMesSeleccionado)
                                <span class="badge bg-primary bg-opacity-10 text-primary ms-1" style="font-size:0.7rem;">Viendo ahora</span>
                            @endif
                        </td>
                        <td class="text-muted">
                            <i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::parse($h->fecha_cierre)->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                    {{ strtoupper(substr(optional($h->usuario)->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="fw-semibold text-dark">{{ optional($h->usuario)->name ?? 'Administración' }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $h->porcentaje >= 100 ? 'bg-success' : 'bg-primary' }} text-white px-2 py-1">
                                {{ $h->verificados_count }} / {{ $h->total_productos }} ({{ $h->porcentaje }}%)
                            </span>
                        </td>
                        <td class="text-center fw-bold text-primary">
                            {{ number_format($h->total_ventas, 0) }} u.
                        </td>
                        <td class="text-center fw-bold text-dark">
                            {{ number_format($h->total_stock, 0) }} u.
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1">
                                <i class="bi bi-shield-check me-1"></i> {{ $h->estado ?? 'Cerrado' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="/reportes/cierre-mes?mes={{ $h->mes }}" class="btn btn-sm btn-outline-primary fw-bold" title="Ver auditoría de este mes">
                                    <i class="bi bi-eye me-1"></i> Ver Cuadre
                                </a>
                                <a href="/reportes/cierre-mes/excel?mes={{ $h->mes }}" class="btn btn-sm btn-outline-success fw-bold" title="Descargar Excel de este cierre">
                                    <i class="bi bi-file-earmark-excel"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-journal-x fs-2 d-block mb-2 opacity-50"></i>
                            Aún no se ha realizado ningún Cierre de Mes oficial en el sistema.<br>
                            <small class="text-muted">Usa el botón <strong>"Cerrar Mes Oficial"</strong> superior para archivar este período.</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ===== MODAL DE CONFIRMACIÓN: CERRAR MES OFICIAL ===== --}}
<div class="modal fade" id="modalConfirmarCierreMes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <form action="/reportes/cierre-mes/cerrar" method="POST">
                @csrf
                <input type="hidden" name="mes" value="{{ $mesSeleccionado }}">

                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                            <i class="bi bi-lock-fill fs-3"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark m-0">Confirmar Cierre de Mes</h5>
                            <small class="text-muted">Período: {{ \Carbon\Carbon::parse($mesSeleccionado . '-01')->translatedFormat('F Y') }}</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 d-flex align-items-start gap-2 mb-3" style="border-radius: 10px;">
                        <i class="bi bi-exclamation-triangle-fill fs-5 mt-1"></i>
                        <div class="small">
                            <strong>Atención:</strong> Esta acción capturará una instantánea permanente e inmutable del estado del inventario, ventas y cuadre físico de este mes.
                        </div>
                    </div>

                    {{-- Resumen de datos a congelar --}}
                    <div class="p-3 rounded-3 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Total Productos Auditados:</span>
                            <strong class="text-dark">{{ $totalProductos }} items</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Productos con Check OK:</span>
                            <strong class="text-success">{{ $verificadosCount }} ({{ $porcentajeVerificado }}%)</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Pendientes de verificar:</span>
                            <strong class="text-secondary">{{ $pendientesCount }} items</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Ventas consolidadas del mes:</span>
                            <strong class="text-primary">{{ number_format($totalVendidos, 0) }} unidades</strong>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Stock actual en tienda:</span>
                            <strong class="text-dark">{{ number_format($totalStockTienda, 0) }} unidades</strong>
                        </div>
                    </div>

                    {{-- Notas u Observaciones opcionales --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">
                            <i class="bi bi-pencil-square me-1"></i> Observaciones o Notas del Cierre (Opcional):
                        </label>
                        <textarea name="notas" class="form-control" rows="3"
                                  placeholder="Ej: Cuadre conforme realizado junto al encargado de almacén. Faltante de 2 unidades en revisión..."></textarea>
                    </div>

                    <p class="small text-muted m-0 text-center">
                        El cierre quedará registrado a nombre de: <strong>{{ Auth::user()->name ?? 'Usuario Actual' }}</strong>
                    </p>
                </div>

                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary fw-bold px-3" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger fw-bold px-4 shadow-sm">
                        <i class="bi bi-shield-lock-fill me-1"></i> Sí, Cerrar Mes Oficialmente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== MODAL PARA REABRIR MES (SOLO ADMINISTRADOR) ===== --}}
@if($cierreActual && $cierreActual->isCerrado() && Auth::user() && Auth::user()->rol === 'admin')
<div class="modal fade" id="modalReabrirMes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <form action="/reportes/cierre-mes/reabrir" method="POST">
                @csrf
                <input type="hidden" name="mes" value="{{ $mesSeleccionado }}">

                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                            <i class="bi bi-unlock-fill fs-3"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark m-0">Reabrir Mes para Edición</h5>
                            <small class="text-muted">Período: {{ \Carbon\Carbon::parse($mesSeleccionado . '-01')->translatedFormat('F Y') }}</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <p class="small text-muted">
                        ¿Estás seguro de que deseas reabrir este mes? Al reabrirlo, se desbloquearán las opciones para marcar o desmarcar checks y realizar un nuevo cuadre de stock.
                    </p>
                    <div class="alert alert-info border-0 small m-0" style="border-radius: 8px;">
                        <i class="bi bi-info-circle me-1"></i> Una vez que termines de ajustar, podrás volver a pulsar <strong>"Cerrar Mes Oficial"</strong> para archivar el nuevo resultado.
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary fw-bold px-3" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning fw-bold px-4 shadow-sm text-dark">
                        <i class="bi bi-unlock-fill me-1"></i> Sí, Reabrir Mes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Token CSRF para peticiones AJAX --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- ===== JAVASCRIPT: INTERACTIVIDAD Y GUARDADO EN VIVO ===== --}}
<script>
const MES_ACTUAL = "{{ $mesSeleccionado }}";
const ES_MES_CERRADO = {{ ($cierreActual && $cierreActual->isCerrado()) ? 'true' : 'false' }};

// 1. Alternar Check interactivo (AJAX instantáneo)
function toggleCheck(idProducto) {
    if (ES_MES_CERRADO) {
        alert('Este mes ya ha sido cerrado oficialmente y sus cifras están archivadas.\nSi necesitas realizar correcciones, un administrador debe reabrir el mes.');
        return;
    }

    const btn = document.getElementById('btn-check-' + idProducto);
    const fila = document.getElementById('fila-' + idProducto);
    if (!btn || !fila) return;

    const icono = btn.querySelector('i');
    const label = btn.querySelector('.label-ok');

    // Feedback optimista inmediato en la interfaz
    const eraOk = btn.classList.contains('is-ok');
    if (eraOk) {
        btn.classList.remove('is-ok');
        btn.classList.add('is-pending');
        if (icono) icono.className = 'bi bi-circle';
        if (label) label.textContent = 'Revisar';
        fila.classList.remove('fila-cuadrada');
        fila.dataset.estado = 'pendiente';
    } else {
        btn.classList.remove('is-pending');
        btn.classList.add('is-ok');
        if (icono) icono.className = 'bi bi-check-lg';
        if (label) label.textContent = 'OK';
        fila.classList.add('fila-cuadrada');
        fila.dataset.estado = 'ok';
    }

    // Petición AJAX en segundo plano
    fetch('/reportes/cierre-mes/toggle-check', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            id_producto: idProducto,
            mes: MES_ACTUAL
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            actualizarEstadisticas(data.totalChecks, data.pendientes, data.porcentaje);
        }
    })
    .catch(err => {
        console.error('Error al guardar el check:', err);
    });
}

// 2. Marcar todos o desmarcar todos
function marcarTodos(accion) {
    if (ES_MES_CERRADO) {
        alert('Este mes ya ha sido cerrado oficialmente y sus cifras están archivadas.');
        return;
    }

    const confirmMsg = accion === 'marcar'
        ? '¿Estás seguro de marcar TODOS los productos como OK?'
        : '¿Estás seguro de desmarcar TODOS los productos?';

    if (!confirm(confirmMsg)) return;

    fetch('/reportes/cierre-mes/check-all', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            mes: MES_ACTUAL,
            accion: accion
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const esMarcar = (accion === 'marcar');
            document.querySelectorAll('.fila-producto').forEach(fila => {
                const btn = fila.querySelector('.btn-check-ok');
                if (!btn) return;
                const icono = btn.querySelector('i');
                const label = btn.querySelector('.label-ok');

                if (esMarcar) {
                    btn.classList.remove('is-pending');
                    btn.classList.add('is-ok');
                    if (icono) icono.className = 'bi bi-check-lg';
                    if (label) label.textContent = 'OK';
                    fila.classList.add('fila-cuadrada');
                    fila.dataset.estado = 'ok';
                } else {
                    btn.classList.remove('is-ok');
                    btn.classList.add('is-pending');
                    if (icono) icono.className = 'bi bi-circle';
                    if (label) label.textContent = 'Revisar';
                    fila.classList.remove('fila-cuadrada');
                    fila.dataset.estado = 'pendiente';
                }
            });
            actualizarEstadisticas(data.totalChecks, data.pendientes, data.porcentaje);
        }
    })
    .catch(err => console.error('Error:', err));
}

// 3. Actualizar contadores y barra de progreso
function actualizarEstadisticas(verificados, pendientes, porcentaje) {
    const elVerificados = document.getElementById('statVerificados');
    const elPendientes  = document.getElementById('statPendientes');
    const elPorcentaje  = document.getElementById('statPorcentaje');
    const elBarra       = document.getElementById('barraProgreso');
    const elPie         = document.getElementById('pieVerificados');
    const elLabel       = document.getElementById('labelProgresoTexto');

    if (elVerificados) elVerificados.textContent = verificados;
    if (elPendientes)  elPendientes.textContent  = pendientes;
    if (elPorcentaje)  elPorcentaje.textContent  = porcentaje + '%';
    if (elBarra)       elBarra.style.width       = porcentaje + '%';
    if (elPie)         elPie.textContent         = verificados + ' OK';

    const total = verificados + pendientes;
    if (elLabel) elLabel.textContent = `${verificados} de ${total} productos (${porcentaje}%)`;
}

// 4. Cambiar de mes
function cambiarMes(nuevoMes) {
    window.location.href = '/reportes/cierre-mes?mes=' + nuevoMes;
}

// 5. Buscador en tiempo real
const inputBuscador = document.getElementById('buscadorProductos');
if (inputBuscador) {
    inputBuscador.addEventListener('input', function() {
        filtrarTabla();
    });
}

// 6. Filtro por estado (todos, ok, pendientes)
let estadoFiltroActivo = 'todos';
function filtrarEstado(estado, btn) {
    estadoFiltroActivo = estado;
    btn.parentElement.querySelectorAll('.btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    filtrarTabla();
}

function filtrarTabla() {
    const q = (document.getElementById('buscadorProductos')?.value || '').toLowerCase().trim();
    let visibles = 0;

    document.querySelectorAll('.fila-producto').forEach(fila => {
        const codigo = fila.dataset.codigo || '';
        const nombre = fila.dataset.nombre || '';
        const estado = fila.dataset.estado || '';

        const coincideTexto = !q || codigo.includes(q) || nombre.includes(q);
        const coincideEstado = (estadoFiltroActivo === 'todos') || (estado === estadoFiltroActivo);

        if (coincideTexto && coincideEstado) {
            fila.style.display = '';
            visibles++;
        } else {
            fila.style.display = 'none';
        }
    });

    const badge = document.getElementById('badgeMostrados');
    if (badge) {
        badge.textContent = `Mostrando ${visibles} productos`;
    }
}
</script>

@endsection