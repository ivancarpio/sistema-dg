@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.card-fin { border: none !important; box-shadow: 0 .125rem .35rem rgba(15, 23, 42, 0.06) !important; border-radius: 1rem !important; }
.periodo-btn { border-radius: 8px !important; font-size: 0.82rem; font-weight: 600; border: 1px solid #e2e8f0; }
.periodo-btn.activo { background: #4c1d95 !important; color: #fff !important; border-color: #4c1d95 !important; }

.fin-kpi-card {
    border-radius: 16px !important;
    padding: 18px 20px;
    border: 1px solid transparent;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    height: 100%;
}
.fin-kpi-card:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(15, 23, 42, 0.08) !important; }
.fin-kpi-icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; flex-shrink: 0;
}
.fin-kpi-label { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.6px; }
.fin-kpi-value { font-size: 1.55rem; font-weight: 800; line-height: 1.15; margin-top: 4px; }
.fin-kpi-sub   { font-size: 0.74rem; margin-top: 6px; }

.kpi-ingresos { background: linear-gradient(135deg, #eff6ff, #dbeafe); border-color: #bfdbfe; }
.kpi-ingresos .fin-kpi-icon { background: rgba(37, 99, 235, 0.15); color: #2563eb; }
.kpi-ingresos .fin-kpi-value, .kpi-ingresos .fin-kpi-label { color: #1e40af; }

.kpi-gastos { background: linear-gradient(135deg, #fef2f2, #fee2e2); border-color: #fecaca; }
.kpi-gastos .fin-kpi-icon { background: rgba(220, 38, 38, 0.14); color: #dc2626; }
.kpi-gastos .fin-kpi-value, .kpi-gastos .fin-kpi-label { color: #991b1b; }

.kpi-neto { background: linear-gradient(135deg, #f0fdf4, #dcfce7); border-color: #bbf7d0; }
.kpi-neto .fin-kpi-icon { background: rgba(22, 163, 74, 0.15); color: #16a34a; }
.kpi-neto .fin-kpi-value, .kpi-neto .fin-kpi-label { color: #166534; }

.kpi-banco { background: linear-gradient(135deg, #f5f3ff, #ede9fe); border-color: #ddd6fe; }
.kpi-banco .fin-kpi-icon { background: rgba(109, 40, 217, 0.15); color: #6d28d9; }
.kpi-banco .fin-kpi-value, .kpi-banco .fin-kpi-label { color: #5b21b6; }

.chart-torta-wrapper {
    position: relative;
    height: 250px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.rank-badge {
    width: 24px; height: 24px; border-radius: 7px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 0.72rem; font-weight: 800; color: #fff; flex-shrink: 0;
}

@media (max-width: 767.98px) {
    .fin-kpi-value { font-size: 1.2rem; }
    .chart-torta-wrapper { height: 220px; }
}
</style>

{{-- ===== ENCABEZADO ===== --}}
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div class="d-flex align-items-center gap-2.5">
        <div class="p-2.5 rounded-3 flex-shrink-0" style="background: #f5f3ff; color: #6d28d9;">
            <i class="bi bi-pie-chart-fill fs-3"></i>
        </div>
        <div>
            <h4 class="fw-bold m-0 text-dark">Reporte de Caja y Finanzas</h4>
            <small class="text-muted">
                <i class="bi bi-clock me-1"></i>{{ $periodoLabel }} &bull; Ingresos reales, análisis de gastos y retiros de gaveta a banco
            </small>
        </div>
    </div>
    <div class="d-flex flex-wrap gap-2 no-print">
        <a href="/reportes" class="btn btn-outline-secondary btn-sm fw-bold rounded-3 px-3">
            <i class="bi bi-arrow-left me-1"></i> Volver a Reportes
        </a>
        <button type="button" onclick="window.print()" class="btn btn-outline-dark btn-sm fw-bold rounded-3 px-3">
            <i class="bi bi-printer me-1"></i> Imprimir
        </button>
        <a href="/reportes/finanzas/excel?{{ http_build_query(request()->all()) }}"
           class="btn btn-sm fw-bold shadow-sm rounded-3 px-3"
           style="background: #16a34a; color: #fff;">
            <i class="bi bi-file-earmark-excel-fill me-1"></i> Exportar a Excel
        </a>
    </div>
</div>

{{-- ===== FILTRO DE PERÍODO ===== --}}
<div class="card card-fin bg-white mb-4 no-print">
    <div class="card-body py-3">
        <form action="/reportes/finanzas" method="GET" id="formFiltrosFinanzas">
            <input type="hidden" name="periodo" id="inputPeriodoFinanzas" value="{{ $periodo }}">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-lg-auto">
                    <label class="text-muted fw-bold d-block mb-1" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:1px;">
                        <i class="bi bi-calendar3 me-1"></i>Período de Análisis
                    </label>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" onclick="seleccionarPeriodoFin('hoy')" class="btn btn-sm periodo-btn {{ $periodo === 'hoy' ? 'activo' : 'btn-light' }}">
                            <i class="bi bi-sun me-1"></i>Hoy
                        </button>
                        <button type="button" onclick="seleccionarPeriodoFin('semana')" class="btn btn-sm periodo-btn {{ $periodo === 'semana' ? 'activo' : 'btn-light' }}">
                            <i class="bi bi-calendar-week me-1"></i>Esta Semana
                        </button>
                        <button type="button" onclick="seleccionarPeriodoFin('mes')" class="btn btn-sm periodo-btn {{ $periodo === 'mes' ? 'activo' : 'btn-light' }}">
                            <i class="bi bi-calendar-month me-1"></i>Este Mes
                        </button>
                        <button type="button" onclick="activarPersonalizadoFin()" class="btn btn-sm periodo-btn {{ $periodo === 'personalizado' ? 'activo' : 'btn-light' }}" id="btnPersFin">
                            <i class="bi bi-sliders me-1"></i>Personalizado
                        </button>
                    </div>
                </div>

                <div class="col-12 col-lg {{ $periodo === 'personalizado' ? '' : 'd-none' }}" id="rangoPersFin">
                    <div class="row g-2 align-items-end">
                        <div class="col-6 col-md-4">
                            <label class="form-label small text-muted fw-bold mb-1">Desde</label>
                            <input type="date" name="fecha_desde" class="form-control form-control-sm bg-light" value="{{ request('fecha_desde', $fechaDesde->format('Y-m-d')) }}">
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label small text-muted fw-bold mb-1">Hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control form-control-sm bg-light" value="{{ request('fecha_hasta', $fechaHasta->format('Y-m-d')) }}">
                        </div>
                        <div class="col-12 col-md-4">
                            <button type="submit" class="btn btn-primary btn-sm fw-bold w-100 rounded-3">
                                <i class="bi bi-funnel-fill me-1"></i> Aplicar Rango
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ===== 1. RESUMEN FINANCIERO REAL DEL PERÍODO (SIN DATOS REPETIDOS NI COSTOS FICTICIOS) ===== --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="fin-kpi-card kpi-ingresos shadow-sm d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div>
                    <div class="fin-kpi-label">1. Dinero Cobrado</div>
                    <div class="fin-kpi-value">Bs. {{ number_format($ingresosCobrados, 2) }}</div>
                </div>
                <div class="fin-kpi-icon"><i class="bi bi-wallet2"></i></div>
            </div>
            <div class="fin-kpi-sub text-muted">
                Efec: <strong>Bs. {{ number_format($ingresosEfectivo, 2) }}</strong> &bull;
                QR: <strong>Bs. {{ number_format($ingresosQr, 2) }}</strong>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="fin-kpi-card kpi-gastos shadow-sm d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div>
                    <div class="fin-kpi-label">2. (–) Gastos Operativos</div>
                    <div class="fin-kpi-value">Bs. {{ number_format($totalGastos, 2) }}</div>
                </div>
                <div class="fin-kpi-icon"><i class="bi bi-receipt-cutoff"></i></div>
            </div>
            <div class="fin-kpi-sub text-muted">
                <strong>{{ $conteoGastos }}</strong> gasto(s) &bull; Gaveta: <strong>Bs. {{ number_format($gastosDesdeGaveta, 2) }}</strong>
                @if($gastosOtrasFuentes > 0)
                    | Otros: <strong>Bs. {{ number_format($gastosOtrasFuentes, 2) }}</strong>
                @endif
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="fin-kpi-card kpi-neto shadow-sm d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div>
                    <div class="fin-kpi-label">3. (=) Flujo Neto (Cobros – Gastos)</div>
                    <div class="fin-kpi-value">Bs. {{ number_format($flujoNetoOperativo, 2) }}</div>
                </div>
                <div class="fin-kpi-icon"><i class="bi bi-graph-up-arrow"></i></div>
            </div>
            <div class="fin-kpi-sub text-muted">
                <strong>{{ $pctLibreGastos }}%</strong> restante antes de reposición de mercadería
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="fin-kpi-card kpi-banco shadow-sm d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div>
                    <div class="fin-kpi-label">4. Retiros de Gaveta a Banco</div>
                    <div class="fin-kpi-value">Bs. {{ number_format($totalRetirosBanco, 2) }}</div>
                </div>
                <div class="fin-kpi-icon"><i class="bi bi-bank2"></i></div>
            </div>
            <div class="fin-kpi-sub text-muted">
                <strong>{{ $conteoRetirosBanco }}</strong> retiro(s)
                @if($conteoRetirosBanco > 0)
                    &bull; Promedio: <strong>Bs. {{ number_format($promedioRetiroBanco, 2) }}</strong>
                @else
                    &bull; Plata sacada de gaveta al banco
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ===== 2. ANÁLISIS VISUAL COMPLEMENTARIO (SIN REPETIR LA MISMA INFO) ===== --}}
<div class="row g-4 mb-4">
    {{-- IZQUIERDA: GRÁFICO DE TORTA UNIFICADO CON SU RANKING DE CATEGORÍAS --}}
    <div class="col-lg-6">
        <div class="card card-fin bg-white h-100">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h6 class="fw-bold m-0 text-dark d-flex align-items-center">
                        <i class="bi bi-pie-chart-fill text-danger me-2"></i> Top de Gastos por Categoría (Torta)
                    </h6>
                    <small class="text-muted">En qué rubros se está gastando el dinero en {{ $periodoLabel }}</small>
                </div>
                @if($topCategoriaGasto)
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill px-3 py-1 fw-bold">
                        #1: {{ $topCategoriaGasto->nombre }}
                    </span>
                @endif
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                @if($gastosPorCategoria->count() > 0)
                    <div class="chart-torta-wrapper mb-3">
                        <canvas id="chartGastosTorta"></canvas>
                    </div>

                    <div class="d-flex flex-column gap-2 pt-3 border-top">
                        @foreach($gastosPorCategoria as $idx => $cat)
                            @php
                                $pct = $totalGastos > 0 ? round(($cat->total_monto / $totalGastos) * 100, 1) : 0;
                                $color = $cat->chart_color ?? '#2563eb';
                            @endphp
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light border">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="rank-badge" style="background-color: {{ $color }};">#{{ $idx + 1 }}</span>
                                    <i class="bi {{ $cat->icono }}" style="color: {{ $color }};"></i>
                                    <span class="fw-bold text-dark small">{{ $cat->nombre }}</span>
                                    <span class="badge bg-white text-muted border fw-semibold" style="font-size: 0.68rem;">
                                        {{ $cat->total_cantidad }} reg.
                                    </span>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold text-danger small">Bs. {{ number_format($cat->total_monto, 2) }}</span>
                                    <span class="badge rounded-pill ms-1 fw-bold" style="background-color: {{ $color }}18; color: {{ $color }}; font-size: 0.72rem;">
                                        {{ $pct }}%
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 my-auto text-muted">
                        <i class="bi bi-pie-chart fs-1 opacity-25 d-block mb-2"></i>
                        <div class="fw-bold text-dark">Sin gastos operativos en este período</div>
                        <small>Cuando registres gastos, aquí aparecerá el gráfico de torta automático.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- DERECHA: SALIDAS REALES POR CANAL + TOP 5 GASTOS PUNTUALES MÁS ALTOS --}}
    <div class="col-lg-6">
        <div class="card card-fin bg-white h-100">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h6 class="fw-bold m-0 text-dark d-flex align-items-center">
                        <i class="bi bi-arrow-left-right text-primary me-2"></i> Origen de Fondos y Gastos Más Altos
                    </h6>
                    <small class="text-muted">De dónde salió el dinero y cuáles fueron los desembolsos individuales mayores</small>
                </div>
                <a href="/finanzas/gastos" class="btn btn-outline-primary btn-sm rounded-pill fw-semibold px-3 py-1 no-print">
                    <i class="bi bi-plus-circle me-1"></i> Ir a Gastos
                </a>
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                {{-- Bloque A: De dónde salió el dinero --}}
                <div class="mb-4">
                    <div class="text-muted fw-bold text-uppercase mb-2" style="font-size:0.7rem; letter-spacing:0.6px;">
                        Flujo de Salidas por Origen del Dinero
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-2.5 rounded-3 border bg-light h-100">
                                <div class="small text-muted fw-semibold"><i class="bi bi-cash-stack text-warning me-1"></i> Gastos de Gaveta</div>
                                <div class="fw-bold text-dark fs-6 mt-0.5">Bs. {{ number_format($gastosDesdeGaveta, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2.5 rounded-3 border bg-light h-100">
                                <div class="small text-muted fw-semibold"><i class="bi bi-bank text-primary me-1"></i> Retiros a Banco</div>
                                <div class="fw-bold text-primary fs-6 mt-0.5">Bs. {{ number_format($totalRetirosBanco, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2.5 rounded-3 border bg-light h-100">
                                <div class="small text-muted fw-semibold"><i class="bi bi-qr-code text-info me-1"></i> Pagos Banco / QR</div>
                                <div class="fw-bold text-dark fs-6 mt-0.5">Bs. {{ number_format($gastosDesdeBancoQr, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2.5 rounded-3 border bg-light h-100">
                                <div class="small text-muted fw-semibold"><i class="bi bi-piggy-bank text-success me-1"></i> Caja Chica (Saldo: Bs. {{ number_format($saldoCajaChicaActual, 2) }})</div>
                                <div class="fw-bold text-dark fs-6 mt-0.5">Bs. {{ number_format($gastosDesdeCajaChica, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    @if($totalTrasladosCajaChica > 0)
                        <div class="mt-2 small text-muted">
                            <i class="bi bi-info-circle text-primary me-1"></i>
                            Además se trasladaron <strong>Bs. {{ number_format($totalTrasladosCajaChica, 2) }}</strong> desde la gaveta para reponer Caja Chica.
                        </div>
                    @endif
                </div>

                {{-- Bloque B: Top 5 Gastos Individuales más grandes --}}
                <div class="pt-3 border-top">
                    <div class="text-muted fw-bold text-uppercase mb-2" style="font-size:0.7rem; letter-spacing:0.6px;">
                        <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Top Desembolsos Puntuales Más Altos
                    </div>
                    @forelse($topGastosIndividuales as $idx => $gTop)
                        <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="me-2 overflow-hidden">
                                <div class="fw-bold text-dark small text-truncate">
                                    {{ $idx + 1 }}. {{ $gTop->descripcion }}
                                </div>
                                <div class="text-muted" style="font-size: 0.72rem;">
                                    {{ $gTop->fecha ? $gTop->fecha->format('d/m/Y H:i') : '—' }}
                                    &bull; {{ optional($gTop->categoria)->nombre ?? 'Sin categoría' }}
                                    &bull; {{ $gTop->origen_pago === 'gaveta' ? 'Gaveta #' . $gTop->turno_caja_id : ($gTop->origen_pago === 'caja_chica' ? 'Caja Chica' : 'Banco/QR') }}
                                </div>
                            </div>
                            <span class="fw-bold text-danger small flex-shrink-0">Bs. {{ number_format($gTop->monto, 2) }}</span>
                        </div>
                    @empty
                        <div class="text-center py-3 text-muted small">
                            No hay gastos individuales registrados en este período.
                        </div>
                    @endforelse
                </div>

                @if($mayorRetiroBanco)
                    <div class="mt-3 pt-2 border-top d-flex flex-wrap justify-content-between align-items-center gap-2 small">
                        <span class="text-muted">
                            <i class="bi bi-bank text-primary me-1"></i>
                            Mayor depósito/retiro al banco: <strong class="text-dark">{{ $mayorRetiroBanco->motivo }}</strong>
                        </span>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle fw-bold px-2.5 py-1">
                            Bs. {{ number_format($mayorRetiroBanco->monto, 2) }} ({{ $mayorRetiroBanco->fecha ? $mayorRetiroBanco->fecha->format('d/m/Y H:i') : '—' }})
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ===== 3. TABLAS DE AUDITORÍA (RETIROS A BANCO, GASTOS Y TURNOS) ===== --}}
<div class="card card-fin bg-white overflow-hidden">
    <div class="card-header bg-white border-bottom p-3">
        <ul class="nav nav-pills gap-2" id="finanzasReportTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold rounded-3" id="tab-retiros-btn" data-bs-toggle="pill" data-bs-target="#tab-retiros" type="button" role="tab">
                    <i class="bi bi-bank me-1"></i> Retiros / Depósitos a Banco ({{ $conteoRetirosBanco }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold rounded-3" id="tab-gastos-btn" data-bs-toggle="pill" data-bs-target="#tab-gastos" type="button" role="tab">
                    <i class="bi bi-receipt-cutoff me-1"></i> Detalle de Gastos ({{ $conteoGastos }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold rounded-3" id="tab-turnos-btn" data-bs-toggle="pill" data-bs-target="#tab-turnos" type="button" role="tab">
                    <i class="bi bi-cash-stack me-1"></i> Turnos de Gaveta ({{ $turnosPeriodo->count() }})
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content">
        {{-- TAB 1: RETIROS / DEPÓSITOS A BANCO --}}
        <div class="tab-pane fade show active" id="tab-retiros" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead class="table-light">
                        <tr style="font-size:0.74rem; text-transform:uppercase;">
                            <th class="ps-4">Fecha y Hora</th>
                            <th>Turno Gaveta</th>
                            <th>Método</th>
                            <th>Motivo / Destino del Retiro</th>
                            <th>Registrado Por</th>
                            <th class="text-end pe-4">Monto Retirado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($retirosBanco as $ret)
                        <tr>
                            <td class="ps-4 small text-muted">{{ $ret->fecha ? $ret->fecha->format('d/m/Y H:i') : '—' }}</td>
                            <td>
                                <a href="/arqueos/sesion" class="badge bg-light text-dark border text-decoration-none fw-bold">
                                    Turno #{{ $ret->arqueo_caja_id }}
                                </a>
                            </td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle">{{ $ret->metodo_pago }}</span></td>
                            <td class="fw-semibold text-dark small">{{ $ret->motivo }}</td>
                            <td class="small text-muted">{{ optional($ret->usuario)->name ?? 'Usuario' }}</td>
                            <td class="text-end pe-4 fw-bold text-primary">Bs. {{ number_format($ret->monto, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-bank fs-2 opacity-25 d-block mb-2"></i>
                                <div class="fw-bold text-dark">No se registraron retiros de gaveta para banco en este período.</div>
                                <small>Cuando saques dinero de la gaveta usando "Registrar Gasto / Retiro &rarr; Retiro / Depósito", se contabilizará aquí.</small>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($retirosBanco->count() > 0)
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="ps-4 fw-bold text-end text-dark">TOTAL RETIRADO DE GAVETA PARA BANCO / GERENCIA:</td>
                            <td class="text-end pe-4 fw-bold text-primary fs-6">Bs. {{ number_format($totalRetirosBanco, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- TAB 2: DETALLE DE GASTOS --}}
        <div class="tab-pane fade" id="tab-gastos" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead class="table-light">
                        <tr style="font-size:0.74rem; text-transform:uppercase;">
                            <th class="ps-4">Fecha y Hora</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th>Origen Fondos</th>
                            <th>Comprobante</th>
                            <th>Usuario</th>
                            <th class="text-end pe-4">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gastos as $g)
                        <tr>
                            <td class="ps-4 small text-muted">{{ $g->fecha ? $g->fecha->format('d/m/Y H:i') : '—' }}</td>
                            <td>
                                @if($g->categoria)
                                    <span class="badge rounded-pill px-2.5 py-1 fw-semibold" style="background-color: {{ $g->categoria->color }}18; color: {{ $g->categoria->color }};">
                                        <i class="bi {{ $g->categoria->icono }} me-1"></i>{{ $g->categoria->nombre }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Sin categoría</span>
                                @endif
                            </td>
                            <td class="small fw-semibold text-dark">{{ $g->descripcion }}</td>
                            <td class="small">
                                @if($g->origen_pago === 'gaveta')
                                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning-subtle">Gaveta #{{ $g->turno_caja_id }}</span>
                                @elseif($g->origen_pago === 'caja_chica')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle">Caja Chica</span>
                                @elseif($g->origen_pago === 'banco_qr')
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle">Banco / QR</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border">Gerencia</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $g->nro_comprobante ?: 'Sin doc.' }}</td>
                            <td class="small text-muted">{{ optional($g->usuario)->name ?? 'Sistema' }}</td>
                            <td class="text-end pe-4 fw-bold text-danger">- Bs. {{ number_format($g->monto, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No hay gastos operativos registrados en este período.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TAB 3: TURNOS DE GAVETA DEL PERÍODO --}}
        <div class="tab-pane fade" id="tab-turnos" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead class="table-light">
                        <tr style="font-size:0.74rem; text-transform:uppercase;">
                            <th class="ps-4">Turno</th>
                            <th>Estado</th>
                            <th>Cajero</th>
                            <th>Apertura</th>
                            <th>Cierre</th>
                            <th class="text-end">Efectivo Sist.</th>
                            <th class="text-end">QR Sist.</th>
                            <th class="text-end">Egresos</th>
                            <th class="text-end">Contado Físico</th>
                            <th class="text-end pe-4">Diferencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($turnosPeriodo as $t)
                        <tr>
                            <td class="ps-4 fw-bold text-primary">#{{ $t->id }}</td>
                            <td>
                                @if($t->estado === 'abierto')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle">Abierto</span>
                                @else
                                    <span class="badge bg-light text-secondary border">Cerrado</span>
                                @endif
                            </td>
                            <td class="small fw-semibold">{{ optional($t->usuarioCierre)->name ?? (optional($t->usuario)->name ?? 'Cajero') }}</td>
                            <td class="small text-muted">{{ $t->fecha_apertura ? $t->fecha_apertura->format('d/m/Y H:i') : '—' }}</td>
                            <td class="small text-muted">{{ $t->fecha_cierre ? $t->fecha_cierre->format('d/m/Y H:i') : 'En curso' }}</td>
                            <td class="text-end small text-success fw-bold">Bs. {{ number_format($t->total_efectivo_sistema, 2) }}</td>
                            <td class="text-end small text-primary fw-bold">Bs. {{ number_format($t->total_qr_sistema, 2) }}</td>
                            <td class="text-end small text-danger">Bs. {{ number_format($t->total_egresos, 2) }}</td>
                            <td class="text-end small fw-bold">{{ $t->monto_fisico_declarado !== null ? 'Bs. ' . number_format($t->monto_fisico_declarado, 2) : '—' }}</td>
                            <td class="text-end pe-4">
                                @if($t->estado === 'cerrado')
                                    @php $d = floatval($t->diferencia ?? 0); @endphp
                                    @if($d == 0)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle">Exacto</span>
                                    @elseif($d > 0)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle">+ Bs. {{ number_format($d, 2) }}</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle">- Bs. {{ number_format(abs($d), 2) }}</span>
                                    @endif
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">No hubo turnos de gaveta abiertos en este período.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function seleccionarPeriodoFin(p) {
    document.getElementById('inputPeriodoFinanzas').value = p;
    document.getElementById('formFiltrosFinanzas').submit();
}

function activarPersonalizadoFin() {
    document.getElementById('inputPeriodoFinanzas').value = 'personalizado';
    const box = document.getElementById('rangoPersFin');
    if (box) box.classList.remove('d-none');
    document.querySelectorAll('.periodo-btn').forEach(b => b.classList.remove('activo'));
    const btnP = document.getElementById('btnPersFin');
    if (btnP) btnP.classList.add('activo');
}

document.addEventListener('DOMContentLoaded', function() {
    const canvasTorta = document.getElementById('chartGastosTorta');
    if (canvasTorta) {
        const labels = @json($chartLabels);
        const montos = @json($chartMontos);
        const colores = @json($chartColores);
        const total = montos.reduce((acc, v) => acc + Number(v || 0), 0);

        new Chart(canvasTorta.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: montos,
                    backgroundColor: colores,
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '56%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 14,
                            font: { size: 12, weight: '600' }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                const val = Number(ctx.raw || 0);
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : '0.0';
                                return ` ${ctx.label}: Bs. ${val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>

@endsection
