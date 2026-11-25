@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    <!-- ============================================================ -->
    <!-- ENCABEZADO PRINCIPAL                                         -->
    <!-- ============================================================ -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="p-3 bg-success bg-opacity-10 text-success rounded-4 border border-success border-opacity-20 shadow-sm">
                <i class="bi bi-piggy-bank-fill fs-2"></i>
            </div>
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Caja Chica (Fondo Fijo)</h1>
                <p class="text-muted small mb-0">Gestión rotatoria de fondos menores para compras urgentes e imprevistos</p>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 w-100 w-md-auto justify-content-start justify-content-md-end">
            <a href="{{ route('finanzas.cajachica.excel', request()->query()) }}" class="btn btn-outline-success d-inline-flex align-items-center gap-1 px-3 py-2 rounded-3 shadow-sm fw-semibold">
                <i class="bi bi-file-earmark-excel"></i>
                <span class="d-none d-sm-inline">Exportar</span>
            </a>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1 px-3 py-2 rounded-3 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalAjuste">
                <i class="bi bi-sliders"></i>
                <span>Ajuste</span>
            </button>
            <button type="button" class="btn btn-danger d-inline-flex align-items-center gap-1 px-3 py-2 rounded-3 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalGastoCajaChica">
                <i class="bi bi-dash-circle-fill"></i>
                <span>Gasto Rápido</span>
            </button>
            <button type="button" class="btn btn-success d-inline-flex align-items-center gap-1 px-3 py-2 rounded-3 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalReposicion">
                <i class="bi bi-plus-circle-fill"></i>
                <span>+ Reponer Fondo</span>
            </button>
        </div>
    </div>

    <!-- Mensajes Flash de alerta -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
            <div class="flex-grow-1">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
            <div class="flex-grow-1">{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- ============================================================ -->
    <!-- KPIS DE CAJA CHICA                                           -->
    <!-- ============================================================ -->
    <div class="row g-3 mb-4">
        <!-- Saldo Disponible Actual -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Saldo Disponible</span>
                    <span class="badge {{ $saldoActual <= 50 ? 'bg-danger' : 'bg-success' }} bg-opacity-10 {{ $saldoActual <= 50 ? 'text-danger' : 'text-success' }} rounded-pill px-2 py-1 small">
                        {{ $saldoActual <= 50 ? 'Fondo Bajo' : 'Activo' }}
                    </span>
                </div>
                <h3 class="fw-bold {{ $saldoActual <= 50 ? 'text-danger' : 'text-success' }} mb-1 fs-4 fs-md-3">
                    {{ $monedaSimbolo }} {{ number_format($saldoActual, 2) }}
                </h3>
                <span class="text-muted small">Efectivo físico disponible en caja chica</span>
            </div>
        </div>

        <!-- Reposiciones del Mes -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Reposiciones Mes</span>
                    <i class="bi bi-arrow-up-circle-fill text-success fs-5"></i>
                </div>
                <h3 class="fw-bold text-dark mb-1 fs-4 fs-md-3">+{{ $monedaSimbolo }} {{ number_format($reposicionesMes, 2) }}</h3>
                <span class="text-muted small">Fondos inyectados este mes</span>
            </div>
        </div>

        <!-- Gastos Pagados del Mes -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Gastos Pagados Mes</span>
                    <i class="bi bi-arrow-down-circle-fill text-danger fs-5"></i>
                </div>
                <h3 class="fw-bold text-danger mb-1 fs-4 fs-md-3">-{{ $monedaSimbolo }} {{ number_format($gastosMes, 2) }}</h3>
                <span class="text-muted small">Egresos operativos menores</span>
            </div>
        </div>

        <!-- Reversiones y Ajustes -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Reversiones / Ajustes</span>
                    <i class="bi bi-shield-check text-primary fs-5"></i>
                </div>
                <h3 class="fw-bold text-dark mb-1 fs-4 fs-md-3">{{ $ajustesCount }} reg.</h3>
                <span class="text-muted small">Auditorías y correcciones</span>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- BARRA DE FILTROS                                             -->
    <!-- ============================================================ -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('finanzas.cajachica.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Fecha Desde</label>
                    <input type="date" name="fecha_desde" class="form-control form-control-sm rounded-3" value="{{ request('fecha_desde', $fechaDesde->format('Y-m-d')) }}">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Fecha Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control form-control-sm rounded-3" value="{{ request('fecha_hasta', $fechaHasta->format('Y-m-d')) }}">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Tipo de Movimiento</label>
                    <select name="tipo" class="form-select form-select-sm rounded-3">
                        <option value="">Todos los tipos</option>
                        <option value="apertura_o_reposicion" {{ request('tipo') == 'apertura_o_reposicion' ? 'selected' : '' }}>Reposición (+)</option>
                        <option value="gasto" {{ request('tipo') == 'gasto' ? 'selected' : '' }}>Gasto Operativo (-)</option>
                        <option value="reversion_gasto" {{ request('tipo') == 'reversion_gasto' ? 'selected' : '' }}>Reversión de Gasto (+)</option>
                        <option value="ajuste_ingreso" {{ request('tipo') == 'ajuste_ingreso' ? 'selected' : '' }}>Ajuste Ingreso (+)</option>
                        <option value="ajuste_egreso" {{ request('tipo') == 'ajuste_egreso' ? 'selected' : '' }}>Ajuste Egreso (-)</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 w-100 fw-semibold d-flex align-items-center justify-content-center gap-1">
                        <i class="bi bi-funnel-fill"></i>
                        <span>Filtrar</span>
                    </button>
                    <a href="{{ route('finanzas.cajachica.index') }}" class="btn btn-outline-secondary btn-sm rounded-3" title="Limpiar filtros">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TABLA DE MOVIMIENTOS Y AUDITORÍA                             -->
    <!-- ============================================================ -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white border-0 py-3 px-3 px-md-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark fs-6 fs-md-5">
                <i class="bi bi-clock-history me-2 text-success"></i>Kardex de Movimientos de Caja Chica
            </h5>
            <span class="badge bg-light text-muted border px-2 py-1 small">
                {{ $movimientos->total() }} movimientos
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3 ps-md-4 py-3">Fecha y Hora</th>
                        <th class="py-3">Tipo</th>
                        <th class="py-3">Concepto / Justificación</th>
                        <th class="py-3 text-end">Monto</th>
                        <th class="py-3 text-end">Saldo Resultante</th>
                        <th class="py-3">Comprobante</th>
                        <th class="py-3">Origen / Turno</th>
                        <th class="pe-3 pe-md-4 py-3">Usuario</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($movimientos as $m)
                        <tr>
                            <td class="ps-3 ps-md-4 text-dark fw-medium small">
                                <div>{{ $m->fecha ? $m->fecha->format('d/m/Y') : '-' }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ $m->fecha ? $m->fecha->format('H:i') : '' }}</div>
                            </td>
                            <td>
                                @if($m->tipo === 'apertura_o_reposicion')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill small">
                                        <i class="bi bi-plus-lg me-1"></i>Reposición
                                    </span>
                                @elseif($m->tipo === 'gasto')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill small">
                                        <i class="bi bi-dash-lg me-1"></i>Gasto
                                    </span>
                                @elseif($m->tipo === 'reversion_gasto')
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 rounded-pill small">
                                        <i class="bi bi-arrow-repeat me-1"></i>Reversión
                                    </span>
                                @elseif($m->tipo === 'ajuste_ingreso')
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1 rounded-pill small">
                                        <i class="bi bi-sliders me-1"></i>Ajuste (+)
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1 rounded-pill small">
                                        <i class="bi bi-sliders me-1"></i>Ajuste (-)
                                    </span>
                                @endif
                            </td>
                            <td style="max-width: 320px; white-space: normal;">
                                <div class="fw-semibold text-dark">{{ $m->motivo }}</div>
                                @if($m->gasto && $m->gasto->categoria)
                                    <span class="badge rounded-pill small mt-1" style="background-color: {{ $m->gasto->categoria->color }}15; color: {{ $m->gasto->categoria->color }}; font-size: 0.7rem;">
                                        <i class="bi {{ $m->gasto->categoria->icono }} me-1"></i>{{ $m->gasto->categoria->nombre }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-end fw-bold fs-6">
                                @if(in_array($m->tipo, ['apertura_o_reposicion', 'reversion_gasto', 'ajuste_ingreso']))
                                    <span class="text-success">+{{ $monedaSimbolo }} {{ number_format($m->monto, 2) }}</span>
                                @else
                                    <span class="text-danger">-{{ $monedaSimbolo }} {{ number_format($monto = $m->monto, 2) }}</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-dark fs-6 bg-light bg-opacity-25">
                                {{ $monedaSimbolo }} {{ number_format($m->saldo_resultante, 2) }}
                            </td>
                            <td class="small text-muted">
                                @if($m->comprobante)
                                    <span class="font-monospace text-dark"><i class="bi bi-receipt me-1"></i>{{ $m->comprobante }}</span>
                                @else
                                    <span class="text-muted fst-italic">Sin doc.</span>
                                @endif
                            </td>
                            <td class="small text-muted">
                                @if($m->turno_caja_id)
                                    <a href="/arqueos/sesion" class="text-decoration-none badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25">
                                        Turno Gaveta #{{ $m->turno_caja_id }}
                                    </a>
                                @else
                                    <span class="text-muted">Caja Chica</span>
                                @endif
                            </td>
                            <td class="pe-3 pe-md-4 small text-muted">
                                {{ $m->usuario ? $m->usuario->name : 'Sistema' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                <span class="fw-semibold">No se encontraron movimientos registrados en Caja Chica para este período.</span>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-success rounded-3" data-bs-toggle="modal" data-bs-target="#modalReposicion">
                                        <i class="bi bi-plus-circle me-1"></i>Hacer Apertura o Reposición
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movimientos->hasPages())
            <div class="card-footer bg-white border-0 py-3 px-3 px-md-4">
                {{ $movimientos->links() }}
            </div>
        @endif
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL: REPOSICIÓN DE FONDOS (APERTURA / FONDEO)             -->
<!-- ============================================================ -->
<div class="modal fade" id="modalReposicion" tabindex="-1" aria-labelledby="modalReposicionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-success bg-opacity-10 text-success rounded-3">
                        <i class="bi bi-plus-circle-fill fs-5"></i>
                    </div>
                    <h5 class="modal-title fw-bold" id="modalReposicionLabel">Reponer Saldo a Caja Chica</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('finanzas.cajachica.reposicion') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-dark mb-1">Monto a Inyectar ({{ $monedaSimbolo }}) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 fw-bold">{{ $monedaSimbolo }}</span>
                                <input type="number" step="0.5" min="0.5" name="monto" class="form-control rounded-end-3" placeholder="0.00" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold text-dark mb-1">Origen de los Fondos <span class="text-danger">*</span></label>
                            <select name="origen_fondos" class="form-select rounded-3" required id="origenFondosSelect" onchange="toggleReposicionHelp(this.value)">
                                <option value="gaveta" {{ $turnoActivo ? 'selected' : '' }}>
                                    Retirar de la Gaveta de Ventas {{ $turnoActivo ? '— Turno #' . $turnoActivo->id . ' Abierto' : '(Requiere turno abierto)' }}
                                </option>
                                <option value="externo" {{ !$turnoActivo ? 'selected' : '' }}>
                                    Aporte Externo / Depósito de Gerencia (No toca gaveta)
                                </option>
                            </select>
                            <div id="helpGavetaReposicion" class="form-text small text-warning {{ $turnoActivo ? '' : 'd-none' }}">
                                <i class="bi bi-info-circle me-1"></i>Se registrará un retiro de efectivo en la gaveta activa #{{ $turnoActivo?->id }}. NO es gasto operativo y no altera la utilidad de ventas.
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold text-dark mb-1">Concepto o Justificación</label>
                            <input type="text" name="motivo" class="form-control rounded-3" placeholder="Ej: Reposición quincenal de fondo fijo o inicio de semana">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold text-dark mb-1">N° Comprobante / Recibo Interno</label>
                            <input type="text" name="comprobante" class="form-control rounded-3" placeholder="Ej: REC-FONDO-01">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success rounded-3 px-4 fw-semibold">
                        <i class="bi bi-check-lg me-1"></i>Inyectar Saldo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL: GASTO RÁPIDO DESDE CAJA CHICA                         -->
<!-- ============================================================ -->
<div class="modal fade" id="modalGastoCajaChica" tabindex="-1" aria-labelledby="modalGastoCajaChicaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-danger bg-opacity-10 text-danger rounded-3">
                        <i class="bi bi-dash-circle-fill fs-5"></i>
                    </div>
                    <h5 class="modal-title fw-bold" id="modalGastoCajaChicaLabel">Pagar Gasto con Caja Chica</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('finanzas.cajachica.gasto') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-light border small text-muted d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-wallet2 text-success fs-5"></i>
                        <div>Saldo disponible en Caja Chica: <strong class="text-success fs-6">{{ $monedaSimbolo }} {{ number_format($saldoActual, 2) }}</strong></div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-semibold text-dark mb-1">Monto a Pagar ({{ $monedaSimbolo }}) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 fw-bold">{{ $monedaSimbolo }}</span>
                                <input type="number" step="0.01" min="0.01" max="{{ $saldoActual }}" name="monto" class="form-control rounded-end-3" placeholder="0.00" required>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-semibold text-dark mb-1">Categoría <span class="text-danger">*</span></label>
                            <select name="categoria_gasto_id" class="form-select rounded-3" required>
                                <option value="" disabled selected>Elegir categoría...</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold text-dark mb-1">Descripción del Egrego <span class="text-danger">*</span></label>
                            <input type="text" name="descripcion" class="form-control rounded-3" placeholder="Ej: Compra de cinta de embalaje y bolsas plásticas" required maxlength="255">
                        </div>

                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-semibold text-dark mb-1">N° Factura / Recibo</label>
                            <input type="text" name="nro_comprobante" class="form-control rounded-3" placeholder="Ej: F-1234 o Recibo 99">
                        </div>

                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-semibold text-dark mb-1">Observaciones</label>
                            <input type="text" name="observacion" class="form-control rounded-3" placeholder="Detalles u observaciones">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger rounded-3 px-4 fw-semibold">
                        <i class="bi bi-check-lg me-1"></i>Pagar Gasto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL: AJUSTE MANUAL DE AUDITORÍA (INGRESO O EGRESO)         -->
<!-- ============================================================ -->
<div class="modal fade" id="modalAjuste" tabindex="-1" aria-labelledby="modalAjusteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-secondary bg-opacity-10 text-secondary rounded-3">
                        <i class="bi bi-sliders fs-5"></i>
                    </div>
                    <h5 class="modal-title fw-bold" id="modalAjusteLabel">Ajuste de Auditoría en Caja Chica</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('finanzas.cajachica.ajuste') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Utiliza este formulario para corregir discrepancias físicas detectadas en el conteo de Caja Chica. Quedará registrado en la auditoría con tu usuario.
                    </p>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-dark mb-1">Dirección del Ajuste <span class="text-danger">*</span></label>
                            <select name="tipo" class="form-select rounded-3" required>
                                <option value="ajuste_ingreso">Ajuste de Ingreso (+) — Aumenta el saldo por sobrante o corrección</option>
                                <option value="ajuste_egreso">Ajuste de Egreso (-) — Reduce el saldo por faltante o merma</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-semibold text-dark mb-1">Monto del Ajuste ({{ $monedaSimbolo }}) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 fw-bold">{{ $monedaSimbolo }}</span>
                                <input type="number" step="0.01" min="0.01" name="monto" class="form-control rounded-end-3" placeholder="0.00" required>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-semibold text-dark mb-1">N° Acta / Comprobante</label>
                            <input type="text" name="comprobante" class="form-control rounded-3" placeholder="Ej: AUD-01">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold text-dark mb-1">Justificación Obligatoria <span class="text-danger">*</span></label>
                            <textarea name="motivo" rows="2" class="form-control rounded-3" placeholder="Explica detalladamente el motivo de auditoría para este ajuste..." required minlength="5"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold">
                        <i class="bi bi-check-lg me-1"></i>Aplicar Ajuste
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleReposicionHelp(origen) {
    const helpGaveta = document.getElementById('helpGavetaReposicion');
    if (helpGaveta) {
        helpGaveta.classList.toggle('d-none', origen !== 'gaveta');
    }
}
</script>
@endsection
