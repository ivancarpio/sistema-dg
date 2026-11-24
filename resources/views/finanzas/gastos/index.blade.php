@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    <!-- ============================================================ -->
    <!-- ENCABEZADO PRINCIPAL                                         -->
    <!-- ============================================================ -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="p-3 bg-danger bg-opacity-10 text-danger rounded-4 border border-danger border-opacity-20 shadow-sm">
                <i class="bi bi-receipt-cutoff fs-2"></i>
            </div>
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Gastos Operativos</h1>
                <p class="text-muted small mb-0">Control centralizado de egresos del negocio sin distorsionar ventas</p>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 w-100 w-md-auto justify-content-start justify-content-md-end">
            <a href="{{ route('finanzas.gastos.excel', request()->query()) }}" class="btn btn-outline-success d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 shadow-sm fw-semibold">
                <i class="bi bi-file-earmark-excel"></i>
                <span>Exportar Excel</span>
            </a>
            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalNuevoGasto">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Nuevo Gasto</span>
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
    <!-- CARDS RESUMEN / KPIS FINANCIEROS                              -->
    <!-- ============================================================ -->
    <div class="row g-3 mb-4">
        <!-- Total Gastos Filtrados -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Total Gastos</span>
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1 small">{{ $conteoGastos }} reg.</span>
                </div>
                <h3 class="fw-bold text-danger mb-1 fs-4 fs-md-3">Bs. {{ number_format($totalGastos, 2) }}</h3>
                <span class="text-muted small">Egresos operativos del período</span>
            </div>
        </div>

        <!-- Desde Gaveta (Turno de Caja) -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Desde Gaveta</span>
                    <i class="bi bi-cash-stack text-warning fs-5"></i>
                </div>
                <h3 class="fw-bold text-dark mb-1 fs-4 fs-md-3">Bs. {{ number_format($gastosGaveta, 2) }}</h3>
                <span class="text-muted small">Descontados de turnos diarios</span>
            </div>
        </div>

        <!-- Desde Caja Chica -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Desde Caja Chica</span>
                    <i class="bi bi-piggy-bank text-success fs-5"></i>
                </div>
                <h3 class="fw-bold text-dark mb-1 fs-4 fs-md-3">Bs. {{ number_format($gastosCajaChica, 2) }}</h3>
                <div class="d-flex align-items-center gap-1 small text-muted">
                    <span>Fondo actual:</span>
                    <strong class="{{ $saldoCajaChica <= 50 ? 'text-danger' : 'text-success' }}">Bs. {{ number_format($saldoCajaChica, 2) }}</strong>
                </div>
            </div>
        </div>

        <!-- Banco / QR / Gerencia -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Banco / Otros</span>
                    <i class="bi bi-bank text-info fs-5"></i>
                </div>
                <h3 class="fw-bold text-dark mb-1 fs-4 fs-md-3">Bs. {{ number_format($gastosBancoQr, 2) }}</h3>
                <span class="text-muted small">Cuentas bancarias o aportes</span>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- BARRA DE FILTROS                                             -->
    <!-- ============================================================ -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('finanzas.gastos.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Fecha Desde</label>
                    <input type="date" name="fecha_desde" class="form-control form-control-sm rounded-3" value="{{ request('fecha_desde', $fechaDesde->format('Y-m-d')) }}">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Fecha Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control form-control-sm rounded-3" value="{{ request('fecha_hasta', $fechaHasta->format('Y-m-d')) }}">
                </div>
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Categoría</label>
                    <select name="categoria_id" class="form-select form-select-sm rounded-3">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Origen Fondos</label>
                    <select name="origen_pago" class="form-select form-select-sm rounded-3">
                        <option value="">Todos los orígenes</option>
                        <option value="gaveta" {{ request('origen_pago') == 'gaveta' ? 'selected' : '' }}>Gaveta (Turno)</option>
                        <option value="caja_chica" {{ request('origen_pago') == 'caja_chica' ? 'selected' : '' }}>Caja Chica</option>
                        <option value="banco_qr" {{ request('origen_pago') == 'banco_qr' ? 'selected' : '' }}>Banco / QR</option>
                        <option value="gerencia" {{ request('origen_pago') == 'gerencia' ? 'selected' : '' }}>Aporte Gerencia</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 w-100 fw-semibold d-flex align-items-center justify-content-center gap-1">
                        <i class="bi bi-funnel-fill"></i>
                        <span>Filtrar</span>
                    </button>
                    <a href="{{ route('finanzas.gastos.index') }}" class="btn btn-outline-secondary btn-sm rounded-3" title="Limpiar filtros">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TABLA DE GASTOS REGISTRADOS                                  -->
    <!-- ============================================================ -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white border-0 py-3 px-3 px-md-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark fs-6 fs-md-5">
                <i class="bi bi-list-ul me-2 text-primary"></i>Listado de Gastos
            </h5>
            <span class="badge bg-light text-muted border px-2 py-1 small">
                {{ $gastos->total() }} registros encontrados
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3 ps-md-4 py-3">Fecha</th>
                        <th class="py-3">Categoría</th>
                        <th class="py-3">Descripción</th>
                        <th class="py-3">Origen Pago</th>
                        <th class="py-3">Comprobante</th>
                        <th class="py-3 text-end">Monto</th>
                        <th class="py-3">Usuario</th>
                        <th class="pe-3 pe-md-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($gastos as $g)
                        <tr>
                            <td class="ps-3 ps-md-4 text-dark fw-medium small">
                                <div>{{ $g->fecha ? $g->fecha->format('d/m/Y') : '-' }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ $g->fecha ? $g->fecha->format('H:i') : '' }}</div>
                            </td>
                            <td>
                                @if($g->categoria)
                                    <span class="badge rounded-pill px-2 py-1 small d-inline-flex align-items-center gap-1" style="background-color: {{ $g->categoria->color }}15; color: {{ $g->categoria->color }}; border: 1px solid {{ $g->categoria->color }}40;">
                                        <i class="bi {{ $g->categoria->icono }}"></i>
                                        <span>{{ $g->categoria->nombre }}</span>
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Sin categoría</span>
                                @endif
                            </td>
                            <td style="max-width: 280px; white-space: normal;">
                                <div class="fw-semibold text-dark">{{ $g->descripcion }}</div>
                                @if($g->observacion)
                                    <div class="text-muted small text-truncate" title="{{ $g->observacion }}">{{ $g->observacion }}</div>
                                @endif
                            </td>
                            <td>
                                @if($g->origen_pago === 'gaveta')
                                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 px-2 py-1 rounded-pill small">
                                        <i class="bi bi-cash-stack text-warning me-1"></i>Gaveta #{{ $g->turno_caja_id ?? 'Turno' }}
                                    </span>
                                @elseif($g->origen_pago === 'caja_chica')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill small">
                                        <i class="bi bi-piggy-bank me-1"></i>Caja Chica
                                    </span>
                                @elseif($g->origen_pago === 'banco_qr')
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1 rounded-pill small">
                                        <i class="bi bi-qr-code-scan me-1"></i>Banco / QR
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1 rounded-pill small">
                                        <i class="bi bi-briefcase me-1"></i>Gerencia
                                    </span>
                                @endif
                            </td>
                            <td class="small text-muted">
                                @if($g->nro_comprobante)
                                    <span class="font-monospace fw-medium text-dark"><i class="bi bi-receipt me-1"></i>{{ $g->nro_comprobante }}</span>
                                @else
                                    <span class="text-muted fst-italic">Sin doc.</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-danger fs-6">
                                -Bs. {{ number_format($g->monto, 2) }}
                            </td>
                            <td class="small text-muted">
                                {{ $g->usuario ? $g->usuario->name : 'Sistema' }}
                            </td>
                            <td class="pe-3 pe-md-4 text-center">
                                <form action="{{ route('finanzas.gastos.destroy', $g->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este gasto de Bs. {{ number_format($g->monto, 2) }}? Si provino de gaveta abierta o caja chica, se revertirá el saldo correspondiente.')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 p-1 px-2" title="Eliminar / Revertir Gasto">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                <span class="fw-semibold">No se encontraron gastos en este período con los filtros aplicados.</span>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-primary rounded-3" data-bs-toggle="modal" data-bs-target="#modalNuevoGasto">
                                        <i class="bi bi-plus-circle me-1"></i>Registrar el primer gasto
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($gastos->hasPages())
            <div class="card-footer bg-white border-0 py-3 px-3 px-md-4">
                {{ $gastos->links() }}
            </div>
        @endif
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL: REGISTRAR NUEVO GASTO                                 -->
<!-- ============================================================ -->
<div class="modal fade" id="modalNuevoGasto" tabindex="-1" aria-labelledby="modalNuevoGastoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-danger bg-opacity-10 text-danger rounded-3">
                        <i class="bi bi-receipt-cutoff fs-5"></i>
                    </div>
                    <h5 class="modal-title fw-bold" id="modalNuevoGastoLabel">Registrar Gasto Operativo</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('finanzas.gastos.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Fecha del Gasto -->
                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-semibold text-dark mb-1">Fecha <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="fecha" class="form-control rounded-3" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                        </div>

                        <!-- Monto -->
                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-semibold text-dark mb-1">Monto (Bs.) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 fw-bold">Bs.</span>
                                <input type="number" step="0.01" min="0.01" name="monto" class="form-control rounded-end-3" placeholder="0.00" required>
                            </div>
                        </div>

                        <!-- Categoría de Gasto -->
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-dark mb-1">Categoría Contable <span class="text-danger">*</span></label>
                            <select name="categoria_gasto_id" class="form-select rounded-3" required>
                                <option value="" disabled selected>Selecciona una categoría...</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}">
                                        {{ $cat->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Descripción / Concepto -->
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-dark mb-1">Descripción / Concepto <span class="text-danger">*</span></label>
                            <input type="text" name="descripcion" class="form-control rounded-3" placeholder="Ej: Compra de combustible camioneta placa 4567-XYZ" required maxlength="255">
                        </div>

                        <!-- Origen del Pago -->
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-dark mb-1">Origen del Dinero <span class="text-danger">*</span></label>
                            <select name="origen_pago" id="origenPagoSelect" class="form-select rounded-3" required onchange="toggleOrigenHelp(this.value)">
                                <option value="gaveta" {{ $turnoActivo ? 'selected' : '' }}>
                                    Gaveta (Turno en Vivo) {{ $turnoActivo ? '— Turno #' . $turnoActivo->id . ' Abierto' : '(Sin turno abierto)' }}
                                </option>
                                <option value="caja_chica">
                                    Caja Chica (Fondo disponible: Bs. {{ number_format($saldoCajaChica, 2) }})
                                </option>
                                <option value="banco_qr">
                                    Banco / Transferencia / QR (No afecta físico)
                                </option>
                                <option value="gerencia">
                                    Aporte Directo de Gerencia (Fondos propios)
                                </option>
                            </select>

                            <div id="helpGaveta" class="form-text small text-warning {{ $turnoActivo ? '' : 'd-none' }}">
                                <i class="bi bi-info-circle me-1"></i>Se registrará un egreso en la gaveta activa #{{ $turnoActivo?->id }} y se descontará del saldo esperado.
                            </div>
                            <div id="helpCajaChica" class="form-text small text-success d-none">
                                <i class="bi bi-piggy-bank me-1"></i>Se descontará directamente del saldo de Caja Chica (Saldo actual: Bs. {{ number_format($saldoCajaChica, 2) }}).
                            </div>
                        </div>

                        <!-- N° Comprobante / Factura -->
                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-semibold text-dark mb-1">N° Comprobante / Recibo</label>
                            <input type="text" name="nro_comprobante" class="form-control rounded-3" placeholder="Ej: FAC-10294 o Recibo 45">
                        </div>

                        <!-- Observaciones Adicionales -->
                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-semibold text-dark mb-1">Observación Adicional</label>
                            <input type="text" name="observacion" class="form-control rounded-3" placeholder="Detalles u observaciones">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold">
                        <i class="bi bi-check-lg me-1"></i>Guardar Gasto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleOrigenHelp(origen) {
    const helpGaveta = document.getElementById('helpGaveta');
    const helpCajaChica = document.getElementById('helpCajaChica');
    if (helpGaveta) helpGaveta.classList.toggle('d-none', origen !== 'gaveta');
    if (helpCajaChica) helpCajaChica.classList.toggle('d-none', origen !== 'caja_chica');
}
</script>
@endsection
