@extends('layouts.app')

@section('content')

<style>
/* ===== ESTILOS MÓDULO REPORTES ===== */
.card { border: none !important; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important; border-radius: .75rem !important; }

/* Tarjetas métricas superiores */
.metric-card {
    border-radius: 14px !important;
    padding: 20px 22px;
    border: none !important;
    transition: transform 0.15s;
    position: relative;
    overflow: hidden;
}
.metric-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.12) !important; }
.metric-card .metric-icon {
    width: 52px; height: 52px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; flex-shrink: 0;
}
.metric-card .metric-label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; }
.metric-card .metric-value { font-size: 1.7rem; font-weight: 800; line-height: 1.1; margin-top: 6px; }
.metric-card .metric-sub   { font-size: 0.75rem; margin-top: 4px; opacity: 0.75; }

/* Tarjeta Efectivo */
.metric-efectivo { background: linear-gradient(135deg, #f0fdf4, #dcfce7); }
.metric-efectivo .metric-icon { background: rgba(22,163,74,0.15); color: #16a34a; }
.metric-efectivo .metric-value { color: #16a34a; }
.metric-efectivo .metric-label,.metric-efectivo .metric-sub { color: #166534; }

/* Tarjeta QR */
.metric-qr { background: linear-gradient(135deg, #eff6ff, #dbeafe); }
.metric-qr .metric-icon { background: rgba(37,99,235,0.12); color: #2563eb; }
.metric-qr .metric-value { color: #2563eb; }
.metric-qr .metric-label,.metric-qr .metric-sub { color: #1e40af; }

/* Tarjeta Total */
.metric-total { background: linear-gradient(135deg, #1e40af, #0ea5e9); }
.metric-total .metric-icon { background: rgba(255,255,255,0.2); color: #fff; }
.metric-total .metric-value,.metric-total .metric-label,.metric-total .metric-sub { color: #fff; }

/* Tarjeta Productos */
.metric-productos { background: linear-gradient(135deg, #faf5ff, #ede9fe); }
.metric-productos .metric-icon { background: rgba(124,58,237,0.12); color: #7c3aed; }
.metric-productos .metric-value { color: #7c3aed; }
.metric-productos .metric-label,.metric-productos .metric-sub { color: #5b21b6; }

/* Tabla */
.table-custom-header th {
    background-color: #f1f5f9 !important;
    font-size: 0.72rem; font-weight: 800; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.5px;
    padding: 13px 10px; border-bottom: none !important;
}
.table-custom-header th:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
.table-custom-header th:last-child  { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
.table-custom tbody tr { border-bottom: 1px solid #f0f2f5; transition: background-color 0.15s; }
.table-custom tbody tr:hover { background-color: #f8faff; }
.table-custom td { vertical-align: middle; padding: 12px 10px; }

/* Badges de método de pago */
.badge-efectivo { background: #dcfce7; color: #16a34a; font-weight: 700; border-radius: 20px; padding: 4px 12px; font-size: 0.78rem; }
.badge-qr       { background: #dbeafe; color: #2563eb; font-weight: 700; border-radius: 20px; padding: 4px 12px; font-size: 0.78rem; }

/* Tabs de período */
.periodo-btn { border-radius: 8px !important; font-size: 0.82rem; font-weight: 600; border: 1px solid #e2e8f0; }
.periodo-btn.activo { background: #1e40af !important; color: #fff !important; border-color: #1e40af !important; }

/* Desglose de productos (accordion) */
.detalle-productos { background: #f8faff; border-left: 3px solid #0ea5e9; border-radius: 0 8px 8px 0; }
.detalle-prod-row { display: flex; align-items: center; gap: 10px; padding: 6px 0; border-bottom: 1px dashed #e2e8f0; font-size: 0.84rem; }
.detalle-prod-row:last-child { border-bottom: none; }

/* Botón expandir */
.btn-expandir { background: none; border: none; cursor: pointer; color: #94a3b8; font-size: 0.8rem; transition: all 0.2s; }
.btn-expandir:hover { color: #0ea5e9; }
.btn-expandir.abierto { color: #0ea5e9; }

/* Mini barra de método de pago */
.mini-bar { height: 4px; border-radius: 4px; margin-top: 6px; }
</style>

{{-- Alertas --}}
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show shadow-sm">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ===== ENCABEZADO ===== --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="bg-primary bg-opacity-10 p-2 rounded text-primary">
            <i class="bi bi-graph-up-arrow fs-3"></i>
        </div>
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:0.8rem;">
                    <li class="breadcrumb-item"><a href="/reportes" class="text-muted text-decoration-none">Reportes</a></li>
                    <li class="breadcrumb-item active">Reporte de Ventas</li>
                </ol>
            </nav>
            <h3 class="fw-bold m-0 text-dark">Reportes de Ventas</h3>
            <small class="text-muted">
                <i class="bi bi-clock me-1"></i>{{ $periodoLabel }}
            </small>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="/reportes" class="btn btn-outline-secondary fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Volver a Reportes
        </a>
        {{-- Botón Exportar Excel --}}
        <a href="/reportes/ventas/excel?{{ http_build_query(request()->all()) }}"
           class="btn fw-bold shadow-sm px-4 py-2"
           style="background: #16a34a; color: #fff; border-radius: 8px;">
            <i class="bi bi-file-earmark-excel-fill me-2"></i>Exportar a Excel
        </a>
    </div>
</div>

{{-- ===== FILTROS ===== --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form action="/reportes/ventas" method="GET" id="formFiltros">
            <div class="row g-2 align-items-end">

                {{-- Período rápido --}}
                <div class="col-12 mb-1">
                    <label class="text-muted fw-bold" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:1px;">
                        <i class="bi bi-calendar3 me-1"></i>Período
                    </label>
                    <div class="d-flex gap-2 flex-wrap mt-1">
                        <button type="submit" name="periodo" value="hoy"
                            class="btn btn-sm periodo-btn {{ $periodo == 'hoy' ? 'activo' : 'btn-light' }}">
                            <i class="bi bi-sun me-1"></i>Hoy
                        </button>
                        <button type="submit" name="periodo" value="semana"
                            class="btn btn-sm periodo-btn {{ $periodo == 'semana' ? 'activo' : 'btn-light' }}">
                            <i class="bi bi-calendar-week me-1"></i>Esta Semana
                        </button>
                        <button type="submit" name="periodo" value="mes"
                            class="btn btn-sm periodo-btn {{ $periodo == 'mes' ? 'activo' : 'btn-light' }}">
                            <i class="bi bi-calendar-month me-1"></i>Este Mes
                        </button>
                        <button type="submit" name="periodo" value="personalizado"
                            class="btn btn-sm periodo-btn {{ $periodo == 'personalizado' ? 'activo' : 'btn-light' }}"
                            id="btnPersonalizado">
                            <i class="bi bi-sliders me-1"></i>Personalizado
                        </button>
                    </div>
                </div>

                {{-- Rango personalizado (oculto por defecto) --}}
                <div class="col-12" id="rangoPersonalizado" style="{{ $periodo == 'personalizado' ? '' : 'display:none;' }}">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label small text-muted fw-bold">Desde</label>
                            <input type="date" name="fecha_desde" class="form-control bg-light"
                                value="{{ request('fecha_desde', $fechaDesde->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted fw-bold">Hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control bg-light"
                                value="{{ request('fecha_hasta', $fechaHasta->format('Y-m-d')) }}">
                        </div>
                    </div>
                </div>

                {{-- Buscador --}}
                <div class="col-md-3">
                    <label class="form-label small text-muted fw-bold">Buscar</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="buscar" class="form-control border-start-0 bg-light"
                            placeholder="Cliente o Nº nota..." value="{{ request('buscar') }}">
                    </div>
                </div>

                {{-- Método de pago --}}
                <div class="col-md-2">
                    <label class="form-label small text-muted fw-bold">Método de Pago</label>
                    <select name="metodo_pago" class="form-select bg-light">
                        <option value="">Todos</option>
                        <option value="Efectivo" {{ request('metodo_pago') == 'Efectivo' ? 'selected' : '' }}>💵 Efectivo</option>
                        <option value="QR"       {{ request('metodo_pago') == 'QR'       ? 'selected' : '' }}>📱 QR</option>
                    </select>
                </div>

                {{-- Cajero --}}
                <div class="col-md-2">
                    <label class="form-label small text-muted fw-bold">Cajero</label>
                    <select name="cajero" class="form-select bg-light">
                        <option value="">Todos</option>
                        @foreach($usuarios as $u)
                        <option value="{{ $u->id }}" {{ request('cajero') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Mantener período al filtrar --}}
                <input type="hidden" name="periodo" value="{{ $periodo }}">

                {{-- Botones --}}
                <div class="col-md-2 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary fw-bold w-100">
                        <i class="bi bi-funnel me-1"></i>Filtrar
                    </button>
                    <a href="/reportes/ventas" class="btn btn-light border" title="Limpiar">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ===== TARJETAS DE MÉTRICAS ===== --}}
<div class="row g-3 mb-4">
    {{-- Efectivo --}}
    <div class="col-6 col-md-3">
        <div class="metric-card metric-efectivo card h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="metric-icon"><i class="bi bi-cash-stack"></i></div>
                <div>
                    <div class="metric-label">Efectivo</div>
                    <div class="metric-value">Bs. {{ number_format($totales['efectivo'], 2) }}</div>
                    <div class="metric-sub">
                        {{ $ventas->where('metodo_pago', 'Efectivo')->count() }} ventas
                    </div>
                </div>
            </div>
            @php
                $pctEfectivo = $totales['general'] > 0 ? ($totales['efectivo'] / $totales['general']) * 100 : 0;
            @endphp
            <div class="mini-bar mt-3" style="background:#bbf7d0;">
                <div class="mini-bar" style="width:{{ $pctEfectivo }}%; background:#16a34a; margin:0;"></div>
            </div>
        </div>
    </div>
    {{-- QR --}}
    <div class="col-6 col-md-3">
        <div class="metric-card metric-qr card h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="metric-icon"><i class="bi bi-qr-code-scan"></i></div>
                <div>
                    <div class="metric-label">QR / Transferencia</div>
                    <div class="metric-value">Bs. {{ number_format($totales['qr'], 2) }}</div>
                    <div class="metric-sub">
                        {{ $ventas->where('metodo_pago', 'QR')->count() }} ventas
                    </div>
                </div>
            </div>
            @php $pctQr = 100 - $pctEfectivo; @endphp
            <div class="mini-bar mt-3" style="background:#bfdbfe;">
                <div class="mini-bar" style="width:{{ $pctQr }}%; background:#2563eb; margin:0;"></div>
            </div>
        </div>
    </div>
    {{-- Total --}}
    <div class="col-6 col-md-3">
        <div class="metric-card metric-total card h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="metric-icon"><i class="bi bi-wallet2"></i></div>
                <div>
                    <div class="metric-label">Total Recaudado</div>
                    <div class="metric-value">Bs. {{ number_format($totales['general'], 2) }}</div>
                    <div class="metric-sub">{{ $totales['cantidad'] }} ventas en total</div>
                </div>
            </div>
        </div>
    </div>
    {{-- Productos --}}
    <div class="col-6 col-md-3">
        <div class="metric-card metric-productos card h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="metric-icon"><i class="bi bi-box-seam"></i></div>
                <div>
                    <div class="metric-label">Artículos Vendidos</div>
                    <div class="metric-value">{{ number_format($totales['productos'], 0) }}</div>
                    <div class="metric-sub">unidades despachadas</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== TABLA PRINCIPAL DE VENTAS CON DESGLOSE ===== --}}
<div class="card">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold m-0">
            <i class="bi bi-table me-2 text-primary"></i>
            Detalle de Ventas
            <span class="badge bg-primary ms-2">{{ $totales['cantidad'] }}</span>
        </h6>
        <div class="d-flex gap-2">
            <button onclick="expandirTodas()" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-chevron-expand me-1"></i>Expandir todas
            </button>
            <button onclick="contraerTodas()" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-chevron-contract me-1"></i>Contraer todas
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom align-middle m-0">
                <thead class="table-custom-header">
                    <tr>
                        <th class="ps-3" style="width:36px;"></th>
                        <th>Nº Nota</th>
                        <th>Fecha y Hora</th>
                        <th>Cliente</th>
                        <th>Método</th>
                        <th>Cajero</th>
                        <th>Productos</th>
                        <th class="text-end pe-4">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $venta)
                    {{-- FILA PRINCIPAL DE LA VENTA --}}
                    <tr class="fila-venta" data-id="{{ $venta->id }}">
                        {{-- Botón expandir --}}
                        <td class="ps-3">
                            @if($venta->detalles->count() > 0)
                            <button class="btn-expandir" onclick="toggleDetalle({{ $venta->id }}, this)"
                                title="Ver productos vendidos">
                                <i class="bi bi-chevron-right fs-5"></i>
                            </button>
                            @endif
                        </td>
                        <td class="fw-bold" style="font-size:0.9rem;">
                            {{ $venta->numero_nota ?? 'SYS-'.str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">
                                {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-clock"></i>
                                {{ \Carbon\Carbon::parse($venta->fecha)->format('H:i') }}
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-person me-1"></i>{{ $venta->cliente ?? 'Cliente Ocasional' }}
                            </span>
                        </td>
                        <td>
                            @if(strtolower($venta->metodo_pago) == 'efectivo')
                                <span class="badge-efectivo">
                                    <i class="bi bi-cash me-1"></i>Efectivo
                                </span>
                            @else
                                <span class="badge-qr">
                                    <i class="bi bi-qr-code me-1"></i>QR
                                </span>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted fw-semibold">
                                <i class="bi bi-person-badge me-1"></i>
                                {{ optional($venta->usuario)->name ?? 'Cajero' }}
                            </small>
                        </td>
                        <td>
                            @if($venta->detalles->count() > 0)
                            <span class="badge bg-secondary bg-opacity-10 text-secondary fw-semibold px-2">
                                {{ $venta->detalles->count() }} ítem(s)
                            </span>
                            @else
                            <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-end pe-4 fw-bold text-success" style="font-size:1rem;">
                            Bs. {{ number_format($venta->total, 2) }}
                        </td>
                    </tr>

                    {{-- FILA DE DESGLOSE DE PRODUCTOS (oculta inicialmente) --}}
                    @if($venta->detalles->count() > 0)
                    <tr class="fila-detalle" id="detalle-{{ $venta->id }}" style="display:none;">
                        <td colspan="8" class="pb-3 pt-0 px-4">
                            <div class="detalle-productos p-3 ms-3">
                                <div class="text-muted fw-bold mb-2" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:1px;">
                                    <i class="bi bi-box-seam me-1 text-info"></i>
                                    Productos vendidos en esta nota
                                </div>
                                {{-- Encabezado mini tabla --}}
                                <div class="detalle-prod-row" style="background:#eff6ff; border-radius:6px; padding: 6px 10px !important; font-weight:800; font-size:0.72rem; color:#1e40af; text-transform:uppercase; letter-spacing:0.5px; border:none !important;">
                                    <span style="flex:3;">Producto</span>
                                    <span style="flex:1; text-align:center;">Cantidad</span>
                                    <span style="flex:1.5; text-align:right;">Precio Unit.</span>
                                    <span style="flex:1.5; text-align:right;">Subtotal</span>
                                </div>
                                {{-- Filas de productos --}}
                                @foreach($venta->detalles as $detalle)
                                <div class="detalle-prod-row">
                                    <span style="flex:3; font-weight:600; color:#1e293b;">
                                        <i class="bi bi-tools me-2 text-muted" style="font-size:0.8rem;"></i>
                                        {{ optional($detalle->producto)->nombre ?? 'Producto eliminado' }}
                                    </span>
                                    <span style="flex:1; text-align:center;">
                                        <span class="badge bg-light text-dark border">
                                            {{ number_format($detalle->cantidad, 0) }}
                                            {{ optional(optional($detalle->producto)->unidad)->nombre ?? 'u.' }}
                                        </span>
                                    </span>
                                    <span style="flex:1.5; text-align:right; color:#64748b;">
                                        Bs. {{ number_format($detalle->precio_unitario, 2) }}
                                    </span>
                                    <span style="flex:1.5; text-align:right; font-weight:700; color:#16a34a;">
                                        Bs. {{ number_format($detalle->subtotal, 2) }}
                                    </span>
                                </div>
                                @endforeach
                                {{-- Total de la nota en el desglose --}}
                                <div style="display:flex; justify-content:flex-end; margin-top:8px; padding-top:8px; border-top:2px solid #bfdbfe;">
                                    <span style="font-weight:800; color:#1e40af; font-size:0.9rem;">
                                        Total nota:&nbsp; Bs. {{ number_format($venta->total, 2) }}
                                        &nbsp;·&nbsp;
                                        @if(strtolower($venta->metodo_pago) == 'efectivo')
                                            <span class="badge-efectivo"><i class="bi bi-cash me-1"></i>Efectivo</span>
                                        @else
                                            <span class="badge-qr"><i class="bi bi-qr-code me-1"></i>QR</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif

                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-graph-up fs-1 d-block mb-2 opacity-25"></i>
                            <p class="fw-bold mb-1">No hay ventas en este período</p>
                            <small>Ajusta los filtros o selecciona otro período.</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>

                {{-- Pie con totales --}}
                @if($ventas->count() > 0)
                <tfoot>
                    <tr style="background: linear-gradient(135deg, #f8faff, #eff6ff); border-top: 2px solid #bfdbfe;">
                        <td colspan="4" class="ps-4 py-3">
                            <span class="fw-bold text-dark">Totales del período</span>
                            <small class="text-muted ms-2">{{ $periodoLabel }}</small>
                        </td>
                        <td colspan="2">
                            <span class="badge-efectivo me-2">
                                <i class="bi bi-cash me-1"></i>Bs. {{ number_format($totales['efectivo'], 2) }}
                            </span>
                            <span class="badge-qr">
                                <i class="bi bi-qr-code me-1"></i>Bs. {{ number_format($totales['qr'], 2) }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted small">
                                <i class="bi bi-box-seam me-1"></i>
                                {{ number_format($totales['productos'], 0) }} artículos
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <span class="fw-bold text-dark fs-5">
                                Bs. {{ number_format($totales['general'], 2) }}
                            </span>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

{{-- ===== JAVASCRIPT ===== --}}
<script>
// Toggle expandir/contraer una fila de detalle
function toggleDetalle(id, btn) {
    const fila = document.getElementById('detalle-' + id);
    const icon = btn.querySelector('i');
    if (fila.style.display === 'none') {
        fila.style.display = '';
        icon.className = 'bi bi-chevron-down fs-5';
        btn.classList.add('abierto');
    } else {
        fila.style.display = 'none';
        icon.className = 'bi bi-chevron-right fs-5';
        btn.classList.remove('abierto');
    }
}

// Expandir todas las filas
function expandirTodas() {
    document.querySelectorAll('.fila-detalle').forEach(f => f.style.display = '');
    document.querySelectorAll('.btn-expandir i').forEach(i => i.className = 'bi bi-chevron-down fs-5');
    document.querySelectorAll('.btn-expandir').forEach(b => b.classList.add('abierto'));
}

// Contraer todas
function contraerTodas() {
    document.querySelectorAll('.fila-detalle').forEach(f => f.style.display = 'none');
    document.querySelectorAll('.btn-expandir i').forEach(i => i.className = 'bi bi-chevron-right fs-5');
    document.querySelectorAll('.btn-expandir').forEach(b => b.classList.remove('abierto'));
}

// Mostrar/ocultar rango personalizado
document.getElementById('btnPersonalizado').addEventListener('click', function() {
    document.getElementById('rangoPersonalizado').style.display = '';
});
</script>

@endsection
