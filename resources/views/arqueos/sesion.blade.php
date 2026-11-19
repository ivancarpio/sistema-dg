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

{{-- Alertas --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ENCABEZADO --}}
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="d-flex align-items-center">
        <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3 text-primary">
            <i class="bi bi-safe2-fill fs-2"></i>
        </div>
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:0.8rem;">
                    <li class="breadcrumb-item"><a href="/arqueos" class="text-muted text-decoration-none">Arqueos</a></li>
                    <li class="breadcrumb-item active">Turno de Caja en Vivo</li>
                </ol>
            </nav>
            <h3 class="fw-bold m-0 text-dark">Control de Caja por Sesiones</h3>
            <small class="text-muted">Gestión de aperturas, cobros en tiempo real, egresos y cierre ciego con declaración física</small>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="/arqueos" class="btn btn-outline-secondary fw-bold rounded-pill px-3 shadow-sm">
            <i class="bi bi-clock-history me-1"></i> Historial de Arqueos
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
                    Bs. {{ number_format($sesion->efectivo_esperado, 2) }}
                </div>
                <div class="small text-white-50 mt-1">
                    Inicial (Bs. {{ number_format($sesion->monto_inicial, 2) }}) + Cobros Efectivo (Bs. {{ number_format($sesion->total_efectivo_sistema, 2) }}) - Egresos (Bs. {{ number_format($sesion->total_egresos, 2) }})
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
                    Bs. {{ number_format($sesion->monto_inicial, 2) }}
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
                    + Bs. {{ number_format($sesion->total_efectivo_sistema, 2) }}
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
                    + Bs. {{ number_format($sesion->total_qr_sistema, 2) }}
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
                    - Bs. {{ number_format($sesion->total_egresos, 2) }}
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
                                    <td class="text-end pe-3 fw-bold text-success">+ Bs. {{ number_format($pago->monto, 2) }}</td>
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
                                        @elseif($mov->tipo === 'egreso_devolucion')
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle fw-bold">Devolución a Cliente</span>
                                        @else
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle fw-bold">Ingreso Extra</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $mov->metodo_pago }}</span></td>
                                    <td class="small">{{ $mov->motivo }}</td>
                                    <td class="small text-muted">{{ optional($mov->usuario)->name ?? 'Usuario' }}</td>
                                    <td class="text-end pe-3 fw-bold {{ in_array($mov->tipo, ['egreso_gasto', 'egreso_devolucion']) ? 'text-danger' : 'text-success' }}">
                                        {{ in_array($mov->tipo, ['egreso_gasto', 'egreso_devolucion']) ? '- Bs. ' : '+ Bs. ' }}{{ number_format($mov->monto, 2) }}
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
                        <label class="form-label fw-bold text-dark small">Monto Inicial en Gaveta (Bs.) *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold text-muted">Bs.</span>
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

{{-- HISTORIAL DE TURNOS CERRADOS --}}
@if($sesionesCerradas->count() > 0)
    <div class="card card-session">
        <div class="card-header bg-white border-bottom p-3">
            <h6 class="fw-bold text-dark m-0 d-flex align-items-center">
                <i class="bi bi-archive-fill text-muted me-2"></i> Últimos Turnos de Caja Cerrados (Inmutables)
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead class="table-light">
                        <tr style="font-size:0.75rem; text-transform:uppercase;">
                            <th class="ps-3">Turno</th>
                            <th>Cajero</th>
                            <th>Apertura</th>
                            <th>Cierre</th>
                            <th class="text-end">Inicial</th>
                            <th class="text-end">Efectivo Sistema</th>
                            <th class="text-end">QR Sistema</th>
                            <th class="text-end">Egresos</th>
                            <th class="text-end">Declarado Físico</th>
                            <th class="text-end pe-3">Diferencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sesionesCerradas as $cierre)
                        <tr>
                            <td class="ps-3 fw-bold text-primary">#{{ $cierre->id }}</td>
                            <td class="small fw-semibold">{{ optional($cierre->usuario)->name ?? 'Cajero' }}</td>
                            <td class="small text-muted">{{ $cierre->fecha_apertura->format('d/m H:i') }}</td>
                            <td class="small text-muted">{{ $cierre->fecha_cierre ? $cierre->fecha_cierre->format('d/m H:i') : '—' }}</td>
                            <td class="text-end small">Bs. {{ number_format($cierre->monto_inicial, 2) }}</td>
                            <td class="text-end small text-success fw-bold">Bs. {{ number_format($cierre->total_efectivo_sistema, 2) }}</td>
                            <td class="text-end small text-primary fw-bold">Bs. {{ number_format($cierre->total_qr_sistema, 2) }}</td>
                            <td class="text-end small text-danger">Bs. {{ number_format($cierre->total_egresos, 2) }}</td>
                            <td class="text-end small fw-bold">Bs. {{ number_format($cierre->monto_fisico_declarado ?? 0, 2) }}</td>
                            <td class="text-end pe-3">
                                @php $dif = floatval($cierre->diferencia ?? 0); @endphp
                                @if($dif == 0)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle fw-bold">Exacto (Bs. 0.00)</span>
                                @elseif($dif > 0)
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle fw-bold">+ Bs. {{ number_format($dif, 2) }}</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle fw-bold">- Bs. {{ number_format(abs($dif), 2) }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

{{-- ============================================== --}}
{{-- MODALES                                       --}}
{{-- ============================================== --}}

{{-- Modal Registrar Gasto / Retiro --}}
<div class="modal fade" id="modalGasto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-danger text-white border-0 py-3">
                <h6 class="modal-title fw-bold m-0"><i class="bi bi-dash-circle me-1"></i> Registrar Salida / Gasto Operativo</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="/arqueos/movimiento" method="POST">
                @csrf
                <input type="hidden" name="tipo" value="egreso_gasto">
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Monto de la salida (Bs.) *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold text-muted">Bs.</span>
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
                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark small">Motivo / Concepto del gasto *</label>
                        <textarea name="motivo" class="form-control" rows="2" placeholder="Ej. Pago de flete, compra de cinta de embalaje, viáticos..." required></textarea>
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
                        <label class="form-label fw-bold text-dark small">Monto que ingresa (Bs.) *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold text-muted">Bs.</span>
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
                            <strong class="text-dark">Bs. {{ number_format($sesion->efectivo_esperado, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-muted small">
                            <span>QR / Banco según Sistema:</span>
                            <strong class="text-primary">Bs. {{ number_format($sesion->total_qr_sistema, 2) }}</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Efectivo Físico Contado en Gaveta (Bs.) *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold text-muted">Bs.</span>
                            <input type="number" step="0.5" min="0" name="monto_fisico_declarado" id="inputMontoDeclaradoCierre" class="form-control form-control-lg fw-bold text-primary" placeholder="0.00" required autofocus>
                        </div>
                        <small class="text-muted" style="font-size:0.75rem;">Cuenta tus billetes y monedas físicos en la gaveta y escribe el total exacto.</small>
                    </div>

                    <div id="cajaDiferenciaCierre" class="p-2.5 rounded-3 mb-3 border" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="fw-bold" id="lblDifCierre">Diferencia:</span>
                            <span class="fs-5 fw-bold" id="txtDifCierre">Bs. 0.00</span>
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
document.addEventListener('DOMContentLoaded', function() {
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
                txtDif.textContent = 'Bs. 0.00';
                txtDif.className = 'fs-5 fw-bold text-success';
            } else if (diff > 0) {
                boxDif.className = 'p-2.5 rounded-3 mb-3 border border-primary bg-primary bg-opacity-10';
                lblDif.textContent = 'Sobrante en caja:';
                lblDif.className = 'fw-bold text-primary';
                txtDif.textContent = `+ Bs. ${diff.toFixed(2)}`;
                txtDif.className = 'fs-5 fw-bold text-primary';
            } else {
                boxDif.className = 'p-2.5 rounded-3 mb-3 border border-danger bg-danger bg-opacity-10';
                lblDif.textContent = 'Faltante en caja:';
                lblDif.className = 'fw-bold text-danger';
                txtDif.textContent = `- Bs. ${Math.abs(diff).toFixed(2)}`;
                txtDif.className = 'fs-5 fw-bold text-danger';
            }
        });
    }
});
</script>
@endif

@endsection
