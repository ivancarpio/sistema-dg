@extends('layouts.app')

@section('content')

<style>
    .card-session { border: none !important; border-radius: 1rem !important; box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important; }
    .metric-box { border-radius: 0.75rem; padding: 1.25rem; transition: transform 0.15s ease; }
    .metric-box:hover { transform: translateY(-2px); }
    .badge-session-open { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; font-weight: 700; border-radius: 20px; padding: 6px 16px; }
    .badge-session-closed { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; font-weight: 700; border-radius: 20px; padding: 6px 16px; }
    .banner-gaveta {
        background: linear-gradient(135deg, #1e40af 0%, #0ea5e9 100%);
        color: white;
        border-radius: 1rem;
        padding: 1.5rem 2rem;
    }
    #turnosTab .nav-link {
        color: #475569 !important;
        background-color: #f8fafc !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 10px !important;
        padding: 8px 18px !important;
        margin-right: 6px !important;
        font-weight: 700 !important;
        transition: all 0.2s ease !important;
    }
    #turnosTab .nav-link:hover {
        color: #0284c7 !important;
        background-color: #f0f9ff !important;
        border-color: #93c5fd !important;
    }
    #turnosTab .nav-link.active {
        color: #0369a1 !important;
        background-color: #e0f2fe !important;
        border-color: #38bdf8 !important;
        box-shadow: 0 2px 8px rgba(2, 132, 199, 0.18) !important;
    }
</style>

{{-- ENCABEZADO --}}
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="d-flex align-items-center">
        <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3 text-primary">
            <i class="bi bi-cash-stack fs-2"></i>
        </div>
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:0.8rem;">
                    <li class="breadcrumb-item text-muted">Caja y Finanzas</li>
                    <li class="breadcrumb-item active">Gaveta de Efectivo</li>
                </ol>
            </nav>
            <h3 class="fw-bold m-0 text-dark">Gaveta de Efectivo</h3>
            <small class="text-muted">Apertura de turno, cobros en vivo, egresos, cierre físico e historial de turnos cerrados</small>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="#historialTurnosCerrados" class="btn btn-outline-secondary fw-bold rounded-pill px-3 shadow-sm">
            <i class="bi bi-clock-history me-1"></i> Ver Turnos Cerrados
        </a>
        <a href="/pos" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm">
            <i class="bi bi-cart4 me-1"></i> Ir al POS
        </a>
    </div>
</div>

@if($sesion)
    {{-- ============================================================ --}}
    {{-- ESTADO 1: TURNO DE CAJA ABIERTO EN VIVO                      --}}
    {{-- ============================================================ --}}

    {{-- Banner de Caja Abierta y Gaveta --}}
    <div class="banner-gaveta mb-4 shadow">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-white text-success fw-bold px-3 py-1.5 rounded-pill shadow-sm">
                        <i class="bi bi-record-circle-fill text-success me-1"></i> TURNO ABIERTO #{{ $sesion->id }}
                    </span>
                    <span class="text-white-50 small">
                        Iniciado por <strong>{{ optional($sesion->usuario)->name ?? 'Cajero' }}</strong> el {{ $sesion->fecha_apertura->format('d/m/Y H:i') }}
                    </span>
                </div>
                <div class="small text-white-50 text-uppercase fw-bold letter-spacing-1">Efectivo Físico Esperado en Gaveta</div>
                <div class="display-5 fw-bold text-white">
                    {{ $monedaSimbolo }} {{ number_format($sesion->efectivo_esperado, 2) }}
                </div>
                <div class="small text-white-50 mt-1">
                    Inicial ({{ $monedaSimbolo }} {{ number_format($sesion->monto_inicial, 2) }}) + Cobros Efectivo ({{ $monedaSimbolo }} {{ number_format($sesion->total_efectivo_sistema, 2) }}) - Egresos Efectivo ({{ $monedaSimbolo }} {{ number_format($sesion->egresos_efectivo, 2) }})
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-light fw-bold text-danger px-3 py-2.5 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalGasto">
                    <i class="bi bi-dash-circle me-1"></i> Registrar Gasto / Retiro
                </button>
                <button type="button" class="btn btn-light fw-bold text-primary px-3 py-2.5 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalIngresoExtra">
                    <i class="bi bi-plus-circle me-1"></i> Ingreso Extra
                </button>
                <button type="button" class="btn btn-warning fw-bold text-dark px-4 py-2.5 rounded-pill shadow" data-bs-toggle="modal" data-bs-target="#modalCierreCaja">
                    <i class="bi bi-lock-fill me-1"></i> Cerrar Caja
                </button>
            </div>
        </div>
    </div>

    {{-- Métricas Desglosadas --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card card-session h-100 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-1 text-muted small fw-bold">
                    <span>MONTO INICIAL</span>
                    <i class="bi bi-cash-stack text-secondary fs-5"></i>
                </div>
                <div class="fs-4 fw-bold text-dark">
                    {{ $monedaSimbolo }} {{ number_format($sesion->monto_inicial, 2) }}
                </div>
                <small class="text-muted" style="font-size:0.75rem;">Fondo de apertura</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card card-session h-100 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-1 text-muted small fw-bold">
                    <span>EFECTIVO COBRADO</span>
                    <i class="bi bi-cash-coin text-success fs-5"></i>
                </div>
                <div class="fs-4 fw-bold text-success">
                    + {{ $monedaSimbolo }} {{ number_format($sesion->total_efectivo_sistema, 2) }}
                </div>
                <small class="text-muted" style="font-size:0.75rem;">Ventas y abonos directos</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card card-session h-100 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-1 text-muted small fw-bold">
                    <span>QR SIMPLE / BANCO</span>
                    <i class="bi bi-qr-code text-primary fs-5"></i>
                </div>
                <div class="fs-4 fw-bold text-primary">
                    + {{ $monedaSimbolo }} {{ number_format($sesion->total_qr_sistema, 2) }}
                </div>
                <small class="text-muted" style="font-size:0.75rem;">Verificable en extracto</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card card-session h-100 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-1 text-muted small fw-bold">
                    <span>EGRESOS / DEVOLUCIONES</span>
                    <i class="bi bi-arrow-down-right-circle text-danger fs-5"></i>
                </div>
                <div class="fs-4 fw-bold text-danger">
                    - {{ $monedaSimbolo }} {{ number_format($sesion->total_egresos, 2) }}
                </div>
                <small class="text-muted" style="font-size:0.75rem;">Salidas reales de caja</small>
            </div>
        </div>
    </div>

    {{-- Pestañas de Movimientos y Pagos de este Turno --}}
    <div class="card card-session mb-4">
        <div class="card-header bg-white border-bottom p-3">
            <ul class="nav nav-pills card-header-pills" id="turnosTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-bold" id="pagos-tab" data-bs-toggle="tab" data-bs-target="#pagos-content" type="button">
                        <i class="bi bi-receipt me-1"></i> Pagos y Cobros del Turno ({{ $sesion->pagos->count() }})
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold" id="egresos-tab" data-bs-toggle="tab" data-bs-target="#egresos-content" type="button">
                        <i class="bi bi-arrow-left-right me-1"></i> Egresos y Gastos ({{ $sesion->movimientos->count() }})
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="tab-content" id="turnosTabContent">
                {{-- TAB 1: PAGOS --}}
                <div class="tab-pane fade show active" id="pagos-content" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead class="table-light">
                                <tr style="font-size:0.75rem; text-transform:uppercase;">
                                    <th class="ps-3">Hora</th>
                                    <th>Nº Nota</th>
                                    <th>Cliente</th>
                                    <th>Método</th>
                                    <th>Concepto / Observación</th>
                                    <th class="text-end pe-3">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sesion->pagos as $pago)
                                <tr>
                                    <td class="ps-3 text-muted small">{{ $pago->fecha ? \Carbon\Carbon::parse($pago->fecha)->format('H:i') : $pago->created_at->format('H:i') }}</td>
                                    <td class="fw-bold">{{ optional($pago->venta)->numero_nota ?? ('Nota #' . $pago->id_venta) }}</td>
                                    <td>{{ optional($pago->cliente)->nombre ?? (optional($pago->venta)->cliente ?? 'Cliente Ocasional') }}</td>
                                    <td>
                                        @if(strtolower($pago->metodo_pago) === 'qr')
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle fw-bold">📱 QR</span>
                                        @elseif(strtolower($pago->metodo_pago) === 'efectivo')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle fw-bold">💵 Efectivo</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border fw-bold">{{ $pago->metodo_pago }}</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $pago->observacion ?: 'Cobro de venta' }}</td>
                                    <td class="text-end pe-3 fw-bold text-success">+ {{ $monedaSimbolo }} {{ number_format($pago->monto, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-3 d-block text-secondary opacity-50 mb-1"></i>
                                        Aún no se han registrado cobros en este turno.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 2: EGRESOS Y MOVIMIENTOS --}}
                <div class="tab-pane fade" id="egresos-content" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead class="table-light">
                                <tr style="font-size:0.75rem; text-transform:uppercase;">
                                    <th class="ps-3">Hora</th>
                                    <th>Tipo</th>
                                    <th>Método</th>
                                    <th>Motivo / Razón</th>
                                    <th>Autorizado Por</th>
                                    <th class="text-end pe-3">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sesion->movimientos as $mov)
                                <tr>
                                    <td class="ps-3 text-muted small">{{ \Carbon\Carbon::parse($mov->fecha)->format('H:i') }}</td>
                                    <td>
                                        @if($mov->tipo === 'egreso_gasto')
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle fw-bold">Gasto Operativo</span>
                                            @if($mov->gasto && $mov->gasto->categoria)
                                                <span class="badge rounded-pill small ms-1" style="background-color: {{ $mov->gasto->categoria->color }}15; color: {{ $mov->gasto->categoria->color }}; font-size: 0.7rem;">
                                                    <i class="bi {{ $mov->gasto->categoria->icono }}"></i> {{ $mov->gasto->categoria->nombre }}
                                                </span>
                                            @endif
                                        @elseif($mov->tipo === 'egreso_retiro')
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle fw-bold">Retiro / Depósito</span>
                                        @elseif($mov->tipo === 'egreso_devolucion')
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle fw-bold">Devolución a Cliente</span>
                                        @else
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle fw-bold">Ingreso Extra</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $mov->metodo_pago }}</span></td>
                                    <td class="small">{{ $mov->motivo }}</td>
                                    <td class="small text-muted">{{ optional($mov->usuario)->name ?? 'Usuario' }}</td>
                                    <td class="text-end pe-3 fw-bold {{ in_array($mov->tipo, ['egreso_gasto', 'egreso_retiro', 'egreso_devolucion']) ? 'text-danger' : 'text-success' }}">
                                        {{ in_array($mov->tipo, ['egreso_gasto', 'egreso_retiro', 'egreso_devolucion']) ? '- ' . $monedaSimbolo . ' ' : '+ ' . $monedaSimbolo . ' ' }}{{ number_format($mov->monto, 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-3 d-block text-secondary opacity-50 mb-1"></i>
                                        No hay salidas ni movimientos especiales registrados en este turno.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@else
    {{-- ============================================================ --}}
    {{-- ESTADO 2: NO HAY TURNO ABIERTO (FORMULARIO DE APERTURA)       --}}
    {{-- ============================================================ --}}
    <div class="row justify-content-center mb-5">
        <div class="col-md-6 col-lg-5">
            <div class="card card-session p-4 text-center">
                <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 64px; height: 64px; font-size: 1.8rem;">
                    <i class="bi bi-safe2"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">No hay turno de caja abierto</h4>
                <p class="text-muted small mb-4">Abre un nuevo turno indicando el monto de cambio que tienes en gaveta para empezar a cobrar con arqueo controlado.</p>

                <form action="/arqueos/abrir" method="POST" class="text-start">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Monto Inicial en Gaveta ({{ $monedaSimbolo }}) *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold text-muted">{{ $monedaSimbolo }}</span>
                            <input type="number" step="0.5" min="0" name="monto_inicial" class="form-control form-control-lg fw-bold text-primary" value="0.00" placeholder="0.00" required autofocus>
                        </div>
                        <small class="text-muted" style="font-size:0.75rem;">Dinero físico en billetes y monedas con el que inicias el turno.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small">Observaciones iniciales (opcional)</label>
                        <input type="text" name="observaciones" class="form-control" placeholder="Ej. Turno Mañana / Cajero 1">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow">
                        <i class="bi bi-unlock-fill me-2"></i> Abrir Turno de Caja Ahora
                    </button>
                </form>
            </div>
        </div>
    </div>
@endif

{{-- HISTORIAL DE TURNOS CERRADOS (CON FILTROS Y DETALLE) --}}
<div id="historialTurnosCerrados" class="card card-session mt-4">
    <div class="card-header bg-white border-bottom p-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h6 class="fw-bold text-dark m-0 d-flex align-items-center">
                    <i class="bi bi-archive-fill text-primary me-2"></i> Historial de Turnos Cerrados
                </h6>
                <small class="text-muted">Auditoría de cierres de gaveta: cajero que cerró, efectivo contado, diferencias y fechas</small>
            </div>
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3 py-1.5 rounded-pill fw-bold">
                {{ $resumenHistorial['cantidad'] }} {{ $resumenHistorial['cantidad'] === 1 ? 'turno cerrado' : 'turnos cerrados' }}
            </span>
        </div>

        {{-- FILTROS DEL HISTORIAL --}}
        <form action="/arqueos/sesion#historialTurnosCerrados" method="GET" class="row g-2 align-items-end bg-light p-2.5 rounded-3 border">
            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-muted mb-1">Fecha Desde</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="form-control form-control-sm">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-muted mb-1">Fecha Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="form-control form-control-sm">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-bold text-muted mb-1">Cajero que Cerró</label>
                <select name="cajero" class="form-select form-select-sm">
                    <option value="">Todos los cajeros</option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id }}" {{ (string) request('cajero') === (string) $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-bold text-muted mb-1">Resultado del Cuadre</label>
                <select name="cuadre" class="form-select form-select-sm">
                    <option value="">Todos (Exactos y Diferencias)</option>
                    <option value="exacto" {{ request('cuadre') === 'exacto' ? 'selected' : '' }}>Exacto ({{ $monedaSimbolo }} 0.00)</option>
                    <option value="sobrante" {{ request('cuadre') === 'sobrante' ? 'selected' : '' }}>Con Sobrante (+)</option>
                    <option value="faltante" {{ request('cuadre') === 'faltante' ? 'selected' : '' }}>Con Faltante (-)</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm fw-bold flex-grow-1 rounded-3">
                    <i class="bi bi-funnel-fill me-1"></i> Filtrar
                </button>
                @if($hayFiltrosHistorial)
                    <a href="/arqueos/sesion#historialTurnosCerrados" class="btn btn-outline-secondary btn-sm rounded-3" title="Limpiar filtros">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- RESUMEN DE TURNOS FILTRADOS --}}
        @if($resumenHistorial['cantidad'] > 0)
            <div class="row g-2 mt-2">
                <div class="col-6 col-md-3">
                    <div class="p-2 rounded-3 bg-success bg-opacity-10 border border-success-subtle">
                        <div class="text-muted" style="font-size:0.68rem; font-weight:700; text-transform:uppercase;">Cobros Efectivo (Período)</div>
                        <div class="fw-bold text-success">{{ $monedaSimbolo }} {{ number_format($resumenHistorial['efectivo_sistema'], 2) }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2 rounded-3 bg-primary bg-opacity-10 border border-primary-subtle">
                        <div class="text-muted" style="font-size:0.68rem; font-weight:700; text-transform:uppercase;">Cobros QR (Período)</div>
                        <div class="fw-bold text-primary">{{ $monedaSimbolo }} {{ number_format($resumenHistorial['qr_sistema'], 2) }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2 rounded-3 bg-light border">
                        <div class="text-muted" style="font-size:0.68rem; font-weight:700; text-transform:uppercase;">Total Efectivo Contado</div>
                        <div class="fw-bold text-dark">{{ $monedaSimbolo }} {{ number_format($resumenHistorial['declarado_fisico'], 2) }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    @php $netoDif = $resumenHistorial['neto_diferencia']; @endphp
                    <div class="p-2 rounded-3 {{ $netoDif == 0 ? 'bg-success bg-opacity-10 border-success-subtle' : ($netoDif > 0 ? 'bg-primary bg-opacity-10 border-primary-subtle' : 'bg-danger bg-opacity-10 border-danger-subtle') }} border">
                        <div class="text-muted" style="font-size:0.68rem; font-weight:700; text-transform:uppercase;">Balance Diferencias</div>
                        <div class="fw-bold {{ $netoDif == 0 ? 'text-success' : ($netoDif > 0 ? 'text-primary' : 'text-danger') }}">
                            @if($netoDif == 0)
                                Exacto ({{ $monedaSimbolo }} 0.00)
                            @elseif($netoDif > 0)
                                + {{ $monedaSimbolo }} {{ number_format($netoDif, 2) }} (Sobrante)
                            @else
                                - {{ $monedaSimbolo }} {{ number_format(abs($netoDif), 2) }} (Faltante)
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead class="table-light">
                    <tr style="font-size:0.74rem; text-transform:uppercase;">
                        <th class="ps-3">Turno</th>
                        <th>Cajero que Cerró</th>
                        <th>Fecha y Hora Apertura</th>
                        <th>Fecha y Hora Cierre</th>
                        <th class="text-end">Inicial</th>
                        <th class="text-end">Cobros Efec.</th>
                        <th class="text-end">Cobros QR</th>
                        <th class="text-end">Egresos</th>
                        <th class="text-end">Esperado Gaveta</th>
                        <th class="text-end">Contado Físico</th>
                        <th class="text-end">Diferencia</th>
                        <th class="text-center pe-3">Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sesionesCerradas as $cierre)
                    @php
                        $cajeroApertura = optional($cierre->usuario)->name ?? 'Cajero';
                        $cajeroCierre = optional($cierre->usuarioCierre)->name ?? $cajeroApertura;
                        $esperadoCierre = ($cierre->monto_fisico_declarado !== null && $cierre->diferencia !== null)
                            ? round(floatval($cierre->monto_fisico_declarado) - floatval($cierre->diferencia), 2)
                            : $cierre->efectivo_esperado;
                        $dif = floatval($cierre->diferencia ?? 0);
                    @endphp
                    <tr>
                        <td class="ps-3 fw-bold text-primary">#{{ $cierre->id }}</td>
                        <td>
                            <div class="small fw-bold text-dark">{{ $cajeroCierre }}</div>
                            @if($cajeroCierre !== $cajeroApertura)
                                <div class="text-muted" style="font-size:0.7rem;">Abrió: {{ $cajeroApertura }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $cierre->fecha_apertura->format('d/m/Y') }}</div>
                            <div class="text-muted" style="font-size:0.72rem;">{{ $cierre->fecha_apertura->format('H:i') }}</div>
                        </td>
                        <td>
                            @if($cierre->fecha_cierre)
                                <div class="small fw-semibold text-dark">{{ $cierre->fecha_cierre->format('d/m/Y') }}</div>
                                <div class="text-muted" style="font-size:0.72rem;">{{ $cierre->fecha_cierre->format('H:i') }}</div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end small">{{ $monedaSimbolo }} {{ number_format($cierre->monto_inicial, 2) }}</td>
                        <td class="text-end small text-success fw-bold">{{ $monedaSimbolo }} {{ number_format($cierre->total_efectivo_sistema, 2) }}</td>
                        <td class="text-end small text-primary fw-bold">{{ $monedaSimbolo }} {{ number_format($cierre->total_qr_sistema, 2) }}</td>
                        <td class="text-end small text-danger">{{ $monedaSimbolo }} {{ number_format($cierre->total_egresos, 2) }}</td>
                        <td class="text-end small text-muted fw-semibold">{{ $monedaSimbolo }} {{ number_format($esperadoCierre, 2) }}</td>
                        <td class="text-end small fw-bold text-dark">{{ $monedaSimbolo }} {{ number_format($cierre->monto_fisico_declarado ?? 0, 2) }}</td>
                        <td class="text-end">
                            @if($dif == 0)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle fw-bold">Exacto ({{ $monedaSimbolo }} 0.00)</span>
                            @elseif($dif > 0)
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle fw-bold">Sobrante +{{ $monedaSimbolo }} {{ number_format($dif, 2) }}</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle fw-bold">Faltante -{{ $monedaSimbolo }} {{ number_format(abs($dif), 2) }}</span>
                            @endif
                        </td>
                        <td class="text-center pe-3">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 fw-semibold" onclick="verDetalleTurnoCerrado({{ $cierre->id }})" title="Ver cobros, gastos y notas de este cierre">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block opacity-50 mb-2"></i>
                            @if($hayFiltrosHistorial)
                                <div class="fw-bold">No se encontraron turnos cerrados con esos filtros.</div>
                                <a href="/arqueos/sesion#historialTurnosCerrados" class="btn btn-sm btn-outline-primary rounded-pill mt-2">
                                    Limpiar filtros
                                </a>
                            @else
                                <div class="fw-bold">Aún no hay turnos cerrados registrados.</div>
                                <small>Cuando cierres un turno en la gaveta, aparecerá aquí automáticamente con su arqueo completo.</small>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($sesionesCerradas->hasPages())
        <div class="card-footer bg-white border-top py-3 px-3">
            {{ $sesionesCerradas->fragment('historialTurnosCerrados')->links() }}
        </div>
    @endif
</div>

{{-- ============================================== --}}
{{-- MODALES                                       --}}
{{-- ============================================== --}}

{{-- Modal Registrar Gasto / Retiro --}}
<div class="modal fade" id="modalGasto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-danger text-white border-0 py-3">
                <h6 class="modal-title fw-bold m-0"><i class="bi bi-dash-circle me-1"></i> Registrar Salida de Dinero (Gaveta)</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="/arqueos/movimiento" method="POST">
                @csrf
                <div class="modal-body p-4 text-start">
                    <!-- Selector de Tipo de Egreso -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Tipo de Salida *</label>
                        <div class="d-flex gap-2">
                            <div class="flex-fill">
                                <input type="radio" class="btn-check" name="tipo" id="tipoGastoOperativo" value="egreso_gasto" checked onchange="toggleTipoSalida(this.value)">
                                <label class="btn btn-outline-danger w-100 py-2 small fw-bold d-flex flex-column align-items-center gap-1 rounded-3" for="tipoGastoOperativo">
                                    <i class="bi bi-receipt-cutoff fs-5"></i>
                                    <span>Gasto Operativo</span>
                                </label>
                            </div>
                            <div class="flex-fill">
                                <input type="radio" class="btn-check" name="tipo" id="tipoRetiro" value="egreso_retiro" onchange="toggleTipoSalida(this.value)">
                                <label class="btn btn-outline-secondary w-100 py-2 small fw-bold d-flex flex-column align-items-center gap-1 rounded-3" for="tipoRetiro">
                                    <i class="bi bi-bank fs-5"></i>
                                    <span>Retiro / Depósito</span>
                                </label>
                            </div>
                        </div>
                        <div id="helpGastoText" class="form-text small text-danger mt-2">
                            <i class="bi bi-info-circle me-1"></i>Se registrará como gasto de la empresa y descontará de la Utilidad Neta Real.
                        </div>
                        <div id="helpRetiroText" class="form-text small text-muted mt-2 d-none">
                            <i class="bi bi-shield-check me-1"></i>Retiro de efectivo (traslado a banco, socio o caja chica). NO afecta la Utilidad Neta.
                        </div>
                    </div>

                    <!-- Categoría Contable (Obligatoria para Gasto Operativo) -->
                    <div class="mb-3" id="campoCategoriaGasto">
                        <label class="form-label fw-bold text-dark small">Categoría del Gasto *</label>
                        <select name="categoria_gasto_id" id="categoriaGastoSelect" class="form-select fw-semibold" required>
                            <option value="" disabled selected>Selecciona una categoría...</option>
                            @foreach($categorias ?? [] as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Monto de la salida ({{ $monedaSimbolo }}) *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold text-muted">{{ $monedaSimbolo }}</span>
                            <input type="number" step="0.5" min="0.5" name="monto" class="form-control form-control-lg fw-bold text-danger" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Método de salida *</label>
                        <select name="metodo_pago" class="form-select fw-semibold">
                            <option value="Efectivo" selected>Efectivo de gaveta</option>
                            <option value="QR">QR / Banco</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Motivo / Concepto *</label>
                        <textarea name="motivo" class="form-control" rows="2" placeholder="Ej. Pago de flete, compra de cinta, retiro para depósito en banco..." required></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark small">N° Comprobante / Recibo (opcional)</label>
                        <input type="text" name="nro_comprobante" class="form-control form-control-sm" placeholder="Ej: REC-102">
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-bold px-4 rounded-pill">Registrar Salida</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Ingreso Extra --}}
<div class="modal fade" id="modalIngresoExtra" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h6 class="modal-title fw-bold m-0"><i class="bi bi-plus-circle me-1"></i> Registrar Ingreso Extra en Caja</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="/arqueos/movimiento" method="POST">
                @csrf
                <input type="hidden" name="tipo" value="ingreso_extra">
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Monto que ingresa ({{ $monedaSimbolo }}) *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold text-muted">{{ $monedaSimbolo }}</span>
                            <input type="number" step="0.5" min="0.5" name="monto" class="form-control form-control-lg fw-bold text-primary" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Método de ingreso *</label>
                        <select name="metodo_pago" class="form-select fw-semibold">
                            <option value="Efectivo" selected>Efectivo a gaveta</option>
                            <option value="QR">QR / Banco</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark small">Motivo / Origen del dinero *</label>
                        <textarea name="motivo" class="form-control" rows="2" placeholder="Ej. Aporte de cambio adicional, cobro misceláneo..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 rounded-pill">Registrar Ingreso</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Cerrar Turno de Caja (Declaración Física Ciega o Auditada) --}}
@if($sesion)
<div class="modal fade" id="modalCierreCaja" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h6 class="modal-title fw-bold m-0"><i class="bi bi-lock-fill text-warning me-2"></i> Cerrar Turno de Caja #{{ $sesion->id }}</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="/arqueos/cerrar" method="POST">
                @csrf
                <div class="modal-body p-4 text-start">
                    <div class="p-3 rounded-3 mb-3 border bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-1 text-muted small">
                            <span>Efectivo según Sistema:</span>
                            <strong class="text-dark">{{ $monedaSimbolo }} {{ number_format($sesion->efectivo_esperado, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-muted small">
                            <span>QR / Banco según Sistema:</span>
                            <strong class="text-primary">{{ $monedaSimbolo }} {{ number_format($sesion->total_qr_sistema, 2) }}</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Efectivo Físico Contado en Gaveta ({{ $monedaSimbolo }}) *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold text-muted">{{ $monedaSimbolo }}</span>
                            <input type="number" step="0.5" min="0" name="monto_fisico_declarado" id="inputMontoDeclaradoCierre" class="form-control form-control-lg fw-bold text-primary" placeholder="0.00" required autofocus>
                        </div>
                        <small class="text-muted" style="font-size:0.75rem;">Cuenta tus billetes y monedas físicos en la gaveta y escribe el total exacto.</small>
                    </div>

                    <div id="cajaDiferenciaCierre" class="p-2.5 rounded-3 mb-3 border" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="fw-bold" id="lblDifCierre">Diferencia:</span>
                            <span class="fs-5 fw-bold" id="txtDifCierre">{{ $monedaSimbolo }} 0.00</span>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark small">Observaciones del Cierre (opcional)</label>
                        <textarea name="observaciones" class="form-control" rows="2" placeholder="Notas sobre el cierre, faltantes o justificaciones..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-bold text-dark px-4 rounded-pill shadow">
                        <i class="bi bi-lock-fill me-1"></i> Confirmar Cierre de Caja
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleTipoSalida(tipo) {
    const campoCat = document.getElementById('campoCategoriaGasto');
    const selectCat = document.getElementById('categoriaGastoSelect');
    const helpGasto = document.getElementById('helpGastoText');
    const helpRetiro = document.getElementById('helpRetiroText');

    if (tipo === 'egreso_gasto') {
        if (campoCat) campoCat.classList.remove('d-none');
        if (selectCat) selectCat.setAttribute('required', 'required');
        if (helpGasto) helpGasto.classList.remove('d-none');
        if (helpRetiro) helpRetiro.classList.add('d-none');
    } else {
        if (campoCat) campoCat.classList.add('d-none');
        if (selectCat) {
            selectCat.removeAttribute('required');
            selectCat.value = '';
        }
        if (helpGasto) helpGasto.classList.add('d-none');
        if (helpRetiro) helpRetiro.classList.remove('d-none');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const monedaSimboloSesion = '{{ $monedaSimbolo ?? "Bs." }}';
    const esperado = {{ $sesion->efectivo_esperado }};
    const inputDecl = document.getElementById('inputMontoDeclaradoCierre');
    const boxDif = document.getElementById('cajaDiferenciaCierre');
    const lblDif = document.getElementById('lblDifCierre');
    const txtDif = document.getElementById('txtDifCierre');

    if (inputDecl) {
        inputDecl.addEventListener('input', function() {
            const decl = parseFloat(this.value) || 0;
            const diff = Math.round((decl - esperado) * 100) / 100;
            boxDif.style.display = 'block';

            if (diff === 0) {
                boxDif.className = 'p-2.5 rounded-3 mb-3 border border-success bg-success bg-opacity-10';
                lblDif.textContent = '¡Caja Cuadrada Exacta!';
                lblDif.className = 'fw-bold text-success';
                txtDif.textContent = `${monedaSimboloSesion} 0.00`;
                txtDif.className = 'fs-5 fw-bold text-success';
            } else if (diff > 0) {
                boxDif.className = 'p-2.5 rounded-3 mb-3 border border-primary bg-primary bg-opacity-10';
                lblDif.textContent = 'Sobrante en caja:';
                lblDif.className = 'fw-bold text-primary';
                txtDif.textContent = `+ ${monedaSimboloSesion} ${diff.toFixed(2)}`;
                txtDif.className = 'fs-5 fw-bold text-primary';
            } else {
                boxDif.className = 'p-2.5 rounded-3 mb-3 border border-danger bg-danger bg-opacity-10';
                lblDif.textContent = 'Faltante en caja:';
                lblDif.className = 'fw-bold text-danger';
                txtDif.textContent = `- ${monedaSimboloSesion} ${Math.abs(diff).toFixed(2)}`;
                txtDif.className = 'fs-5 fw-bold text-danger';
            }
        });
    }
});
</script>
@endif

{{-- MODAL: DETALLE DE TURNO CERRADO --}}
<div class="modal fade" id="modalDetalleTurnoCerrado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <div>
                    <h6 class="modal-title fw-bold m-0 d-flex align-items-center">
                        <i class="bi bi-safe2-fill text-info me-2"></i>
                        <span id="detTurnoTitulo">Detalle de Turno Cerrado</span>
                    </h6>
                    <small class="text-white-50" id="detTurnoSubtitulo">Cargando información...</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4 text-start">
                <div id="detTurnoLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="text-muted small mt-2">Cargando auditoría del turno...</div>
                </div>

                <div id="detTurnoContenido" class="d-none">
                    {{-- Info de responsables y fechas --}}
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <div class="p-2.5 rounded-3 bg-light border h-100">
                                <div class="text-muted" style="font-size:0.7rem; font-weight:700; text-transform:uppercase;">Apertura del Turno</div>
                                <div class="fw-bold text-dark small" id="detTurnoApertura">—</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2.5 rounded-3 bg-light border h-100">
                                <div class="text-muted" style="font-size:0.7rem; font-weight:700; text-transform:uppercase;">Cierre del Turno</div>
                                <div class="fw-bold text-dark small" id="detTurnoCierre">—</div>
                            </div>
                        </div>
                    </div>

                    {{-- Observaciones --}}
                    <div id="detTurnoObsBox" class="alert alert-light border small py-2 px-3 mb-3 d-none">
                        <i class="bi bi-chat-left-text text-primary me-1"></i>
                        <strong>Observaciones:</strong> <span id="detTurnoObs"></span>
                    </div>

                    {{-- Resumen Numérico --}}
                    <div class="row g-2 mb-4">
                        <div class="col-6 col-lg-3">
                            <div class="p-3 rounded-3 bg-light border h-100">
                                <div class="text-muted" style="font-size:0.68rem; font-weight:700; text-transform:uppercase;">Fondo Inicial</div>
                                <div class="fs-5 fw-bold text-dark" id="detInicial">{{ $monedaSimbolo }} 0.00</div>
                                <div class="text-muted" style="font-size:0.7rem;" id="detDesgloseMovs">Efec: {{ $monedaSimbolo }} 0.00 | QR: {{ $monedaSimbolo }} 0.00</div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="p-3 rounded-3 bg-primary bg-opacity-10 border border-primary-subtle h-100">
                                <div class="text-muted" style="font-size:0.68rem; font-weight:700; text-transform:uppercase;">Esperado en Gaveta</div>
                                <div class="fs-5 fw-bold text-primary" id="detEsperado">{{ $monedaSimbolo }} 0.00</div>
                                <div class="text-danger" style="font-size:0.7rem;" id="detEgresosEfec">Egresos efec: - {{ $monedaSimbolo }} 0.00</div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="p-3 rounded-3 bg-success bg-opacity-10 border border-success-subtle h-100">
                                <div class="text-muted" style="font-size:0.68rem; font-weight:700; text-transform:uppercase;">Contado Físico</div>
                                <div class="fs-5 fw-bold text-success" id="detContado">{{ $monedaSimbolo }} 0.00</div>
                                <div class="text-muted" style="font-size:0.7rem;" id="detQrInfo">QR Banco: {{ $monedaSimbolo }} 0.00</div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div id="detDiferenciaCard" class="p-3 rounded-3 border h-100">
                                <div class="text-muted" style="font-size:0.68rem; font-weight:700; text-transform:uppercase;">Diferencia (Cuadre)</div>
                                <div class="fs-5 fw-bold" id="detDiferencia">{{ $monedaSimbolo }} 0.00</div>
                                <div style="font-size:0.7rem;" id="detDiferenciaEstado">—</div>
                            </div>
                        </div>
                    </div>

                    {{-- Pestañas de Cobros y Egresos --}}
                    <ul class="nav nav-pills mb-3" id="detTurnoTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold btn-sm" id="det-pagos-tab" data-bs-toggle="pill" data-bs-target="#det-pagos-pane" type="button" role="tab">
                                <i class="bi bi-receipt me-1"></i> Cobros (<span id="detCountPagos">0</span>)
                            </button>
                        </li>
                        <li class="nav-item ms-2" role="presentation">
                            <button class="nav-link fw-bold btn-sm" id="det-movs-tab" data-bs-toggle="pill" data-bs-target="#det-movs-pane" type="button" role="tab">
                                <i class="bi bi-arrow-left-right me-1"></i> Egresos e Ingresos Extra (<span id="detCountMovs">0</span>)
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="det-pagos-pane" role="tabpanel">
                            <div class="table-responsive border rounded-3" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-sm table-hover align-middle m-0">
                                    <thead class="table-light sticky-top">
                                        <tr style="font-size:0.72rem; text-transform:uppercase;">
                                            <th class="ps-3">Fecha / Hora</th>
                                            <th>N° Nota</th>
                                            <th>Cliente</th>
                                            <th>Método</th>
                                            <th>Observación</th>
                                            <th class="text-end pe-3">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detPagosBody"></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="det-movs-pane" role="tabpanel">
                            <div class="table-responsive border rounded-3" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-sm table-hover align-middle m-0">
                                    <thead class="table-light sticky-top">
                                        <tr style="font-size:0.72rem; text-transform:uppercase;">
                                            <th class="ps-3">Fecha / Hora</th>
                                            <th>Tipo</th>
                                            <th>Método</th>
                                            <th>Motivo</th>
                                            <th>Usuario</th>
                                            <th class="text-end pe-3">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detMovsBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-2.5">
                <button type="button" class="btn btn-secondary fw-bold rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
const simboloMonedaModal = '{{ $monedaSimbolo ?? "Bs." }}';
function formatBs(val) {
    const num = Number(val) || 0;
    return simboloMonedaModal + ' ' + num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function createCell(text, className = '') {
    const td = document.createElement('td');
    if (className) td.className = className;
    td.textContent = text;
    return td;
}

function verDetalleTurnoCerrado(id) {
    const modalEl = document.getElementById('modalDetalleTurnoCerrado');
    const loadingEl = document.getElementById('detTurnoLoading');
    const contentEl = document.getElementById('detTurnoContenido');

    if (loadingEl) loadingEl.classList.remove('d-none');
    if (contentEl) contentEl.classList.add('d-none');
    document.getElementById('detTurnoTitulo').textContent = 'Detalle de Turno #' + id;
    document.getElementById('detTurnoSubtitulo').textContent = 'Cargando...';

    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();

    fetch('/arqueos/sesion/' + encodeURIComponent(id) + '/detalle', {
        headers: { 'Accept': 'application/json' }
    })
    .then(function(res) {
        if (!res.ok) throw new Error('Error al cargar el turno');
        return res.json();
    })
    .then(function(data) {
        document.getElementById('detTurnoTitulo').textContent = 'Auditoría de Turno #' + data.id;
        document.getElementById('detTurnoSubtitulo').textContent = 'Cerrado por ' + data.usuario_cierre + ' el ' + data.fecha_cierre;
        document.getElementById('detTurnoApertura').textContent = data.usuario_apertura + ' · ' + data.fecha_apertura;
        document.getElementById('detTurnoCierre').textContent = data.usuario_cierre + ' · ' + data.fecha_cierre;

        const obsBox = document.getElementById('detTurnoObsBox');
        const obsSpan = document.getElementById('detTurnoObs');
        if (data.observaciones) {
            obsSpan.textContent = data.observaciones;
            obsBox.classList.remove('d-none');
        } else {
            obsBox.classList.add('d-none');
        }

        document.getElementById('detInicial').textContent = formatBs(data.monto_inicial);
        document.getElementById('detDesgloseMovs').textContent = '+ Ventas Efec: ' + formatBs(data.total_efectivo_sistema);
        document.getElementById('detEsperado').textContent = formatBs(data.efectivo_esperado);
        document.getElementById('detEgresosEfec').textContent = '- Salidas Efec: ' + formatBs(data.egresos_efectivo);
        document.getElementById('detContado').textContent = formatBs(data.monto_fisico_declarado);
        document.getElementById('detQrInfo').textContent = 'QR / Banco: ' + formatBs(data.total_qr_sistema);

        const dif = Number(data.diferencia || 0);
        const difCard = document.getElementById('detDiferenciaCard');
        const difVal = document.getElementById('detDiferencia');
        const difEst = document.getElementById('detDiferenciaEstado');

        if (dif === 0) {
            difCard.className = 'p-3 rounded-3 border border-success-subtle bg-success bg-opacity-10 h-100';
            difVal.className = 'fs-5 fw-bold text-success';
            difVal.textContent = formatBs(0);
            difEst.className = 'fw-bold text-success';
            difEst.textContent = 'Cuadre exacto';
        } else if (dif > 0) {
            difCard.className = 'p-3 rounded-3 border border-primary-subtle bg-primary bg-opacity-10 h-100';
            difVal.className = 'fs-5 fw-bold text-primary';
            difVal.textContent = '+ ' + formatBs(dif);
            difEst.className = 'fw-bold text-primary';
            difEst.textContent = 'Sobrante en gaveta';
        } else {
            difCard.className = 'p-3 rounded-3 border border-danger-subtle bg-danger bg-opacity-10 h-100';
            difVal.className = 'fs-5 fw-bold text-danger';
            difVal.textContent = '- ' + formatBs(Math.abs(dif));
            difEst.className = 'fw-bold text-danger';
            difEst.textContent = 'Faltante en gaveta';
        }

        // Poblar tabla de pagos
        document.getElementById('detCountPagos').textContent = data.pagos.length;
        const pagosBody = document.getElementById('detPagosBody');
        pagosBody.innerHTML = '';
        if (data.pagos.length === 0) {
            const tr = document.createElement('tr');
            const td = createCell('No hubo cobros registrados en este turno.', 'text-center py-4 text-muted small');
            td.colSpan = 6;
            tr.appendChild(td);
            pagosBody.appendChild(tr);
        } else {
            data.pagos.forEach(function(p) {
                const tr = document.createElement('tr');
                tr.appendChild(createCell(p.hora, 'ps-3 small text-muted'));
                tr.appendChild(createCell(p.nota, 'small fw-bold'));
                tr.appendChild(createCell(p.cliente, 'small'));
                const tdMet = document.createElement('td');
                const badge = document.createElement('span');
                const isQr = (p.metodo || '').toLowerCase() === 'qr';
                badge.className = isQr
                    ? 'badge bg-primary bg-opacity-10 text-primary border border-primary-subtle'
                    : 'badge bg-success bg-opacity-10 text-success border border-success-subtle';
                badge.textContent = p.metodo;
                tdMet.appendChild(badge);
                tr.appendChild(tdMet);
                tr.appendChild(createCell(p.observacion, 'small text-muted'));
                tr.appendChild(createCell('+ ' + formatBs(p.monto), 'text-end pe-3 small fw-bold text-success'));
                pagosBody.appendChild(tr);
            });
        }

        // Poblar tabla de movimientos
        document.getElementById('detCountMovs').textContent = data.movimientos.length;
        const movsBody = document.getElementById('detMovsBody');
        movsBody.innerHTML = '';
        if (data.movimientos.length === 0) {
            const tr = document.createElement('tr');
            const td = createCell('No hubo gastos, retiros ni ingresos extras en este turno.', 'text-center py-4 text-muted small');
            td.colSpan = 6;
            tr.appendChild(td);
            movsBody.appendChild(tr);
        } else {
            data.movimientos.forEach(function(m) {
                const tr = document.createElement('tr');
                tr.appendChild(createCell(m.hora, 'ps-3 small text-muted'));
                const tdTipo = document.createElement('td');
                const bTipo = document.createElement('span');
                bTipo.className = m.es_egreso
                    ? 'badge bg-danger bg-opacity-10 text-danger border border-danger-subtle'
                    : 'badge bg-info bg-opacity-10 text-info border border-info-subtle';
                bTipo.textContent = m.tipo_label + (m.categoria ? ' (' + m.categoria + ')' : '');
                tdTipo.appendChild(bTipo);
                tr.appendChild(tdTipo);
                tr.appendChild(createCell(m.metodo, 'small'));
                tr.appendChild(createCell(m.motivo, 'small'));
                tr.appendChild(createCell(m.usuario, 'small text-muted'));
                tr.appendChild(createCell(
                    (m.es_egreso ? '- ' : '+ ') + formatBs(m.monto),
                    'text-end pe-3 small fw-bold ' + (m.es_egreso ? 'text-danger' : 'text-success')
                ));
                movsBody.appendChild(tr);
            });
        }

        loadingEl.classList.add('d-none');
        contentEl.classList.remove('d-none');
    })
    .catch(function(err) {
        document.getElementById('detTurnoSubtitulo').textContent = 'No se pudo cargar el detalle';
        loadingEl.classList.add('d-none');
        if (window.mostrarAlertaFlotante) {
            window.mostrarAlertaFlotante('Error al cargar el detalle del turno cerrado.', 'danger');
        }
    });
}

@if(!empty($hayFiltrosHistorial) || request()->has('page'))
document.addEventListener('DOMContentLoaded', function() {
    const histEl = document.getElementById('historialTurnosCerrados');
    if (histEl) {
        histEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
});
@endif
</script>

@endsection
