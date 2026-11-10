@extends('layouts.app')

@section('content')
<!-- Chart.js para visualización de analíticas -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* Estilos Ejecutivos del Dashboard */
    .dashboard-header-badge {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
        font-size: 0.73rem;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    /* Tarjetas de Métricas (KPIs) Premium */
    .card-kpi-pro {
        border-radius: 16px !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05) !important;
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s ease;
        position: relative;
        overflow: hidden;
    }
    .card-kpi-pro:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.1) !important;
    }
    .card-kpi-pro .kpi-label {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        margin-bottom: 6px;
    }
    .card-kpi-pro .kpi-amount {
        font-size: 1.65rem;
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 6px;
    }
    .card-kpi-pro .kpi-meta {
        font-size: 0.74rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .kpi-icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    /* Tarjetas de Gráficos */
    .card-chart-pro {
        border-radius: 16px !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05) !important;
        background: #ffffff;
    }
    .card-chart-pro .card-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 16px 20px;
    }

    /* Botones de Filtro Activos */
    .btn-pill-filter {
        font-size: 0.78rem;
        font-weight: 700;
        border-radius: 8px;
        padding: 4px 12px;
        color: #64748b;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
    }
    .btn-pill-filter:hover {
        background-color: #e2e8f0;
        color: #0f172a;
    }
    .btn-pill-filter.active {
        background-color: #0284c7 !important;
        border-color: #0284c7 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);
    }

    /* Selector de Período y Año Personalizado */
    .btn-periodo-pro {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 8px 16px;
        font-weight: 700;
        font-size: 0.88rem;
        color: #0f172a;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s ease;
    }
    .btn-periodo-pro:hover {
        background: #f8fafc;
        border-color: #0284c7;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.15);
    }
    .periodo-icon-box {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: rgba(2, 132, 199, 0.1);
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    /* Menú del Selector de Período */
    .dropdown-periodo-menu {
        width: 320px;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.15);
        padding: 16px;
        background: #ffffff;
        z-index: 1060;
    }
    .btn-mes-grid {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 8px;
        padding: 8px 4px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #334155;
        transition: all 0.15s ease;
        width: 100%;
    }
    .btn-mes-grid:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .btn-mes-grid.active {
        background: #0284c7 !important;
        border-color: #0284c7 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(2, 132, 199, 0.35);
    }

    /* Estilos del Embudo (Funnel) de Clientes */
    .funnel-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        width: 100%;
        padding: 0;
    }
    .funnel-tier {
        border-radius: 7px;
        transition: filter 0.15s ease, box-shadow 0.15s ease;
        position: relative;
        cursor: default;
        overflow: hidden;
    }
    .funnel-tier:hover {
        filter: brightness(1.08);
        box-shadow: 0 4px 12px rgba(3, 74, 117, 0.2) !important;
    }
    .funnel-rank-badge {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        font-weight: 800;
        font-size: 0.68rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
</style>

{{-- ======================================================== --}}
{{-- 1. ENCABEZADO EJECUTIVO & SELECTOR DE PERÍODO Y AÑO      --}}
{{-- ======================================================== --}}
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge dashboard-header-badge rounded-pill px-2.5 py-1">
                <i class="bi bi-graph-up-arrow me-1"></i> CONTROL COMERCIAL
            </span>
            @if($esMesActual)
                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle fw-bold px-2 py-0.5" style="font-size: 0.72rem;">
                    &bull; Mes en Curso
                </span>
            @else
                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle fw-bold px-2 py-0.5" style="font-size: 0.72rem;">
                    Historial Archivado
                </span>
            @endif
        </div>
        <h3 class="fw-bold text-dark m-0 tracking-tight" style="letter-spacing: -0.5px;">
            Dashboard de Ventas
        </h3>
        <small class="text-muted">
            Métricas de rendimiento en tiempo real &bull; Período activo: <strong class="text-dark">{{ ucfirst($nombreMesActual) }} {{ $anio }}</strong>
        </small>
    </div>

    <!-- SELECTOR DE PERÍODO (AÑO Y MES) MODERNO Y ELEGANTE -->
    <div class="d-flex align-items-center flex-wrap gap-2">
        <div class="dropdown position-relative" id="dropdownPeriodoContainer">
            <button type="button" class="btn-periodo-pro" id="btnDropdownPeriodo" 
                    data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                <div class="periodo-icon-box">
                    <i class="bi bi-calendar3"></i>
                </div>
                <div class="text-start pe-2">
                    <div style="font-size: 0.68rem; text-transform: uppercase; color: #64748b; font-weight: 800; letter-spacing: 0.5px;">
                        Período Seleccionado
                    </div>
                    <div style="font-size: 0.95rem; font-weight: 800; color: #0f172a;" id="txtLabelPeriodoActivo">
                        {{ ucfirst($nombreMesActual) }} {{ $anio }}
                    </div>
                </div>
                <i class="bi bi-chevron-down text-muted" style="font-size: 0.78rem;"></i>
            </button>

            <!-- Menú Desplegable con Selector de Año y Cuadrícula de 12 Meses -->
            <div class="dropdown-menu dropdown-menu-end dropdown-periodo-menu" id="menuPeriodoPicker">
                <!-- Selector de Año -->
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <button type="button" class="btn btn-sm btn-light border rounded-circle p-0 d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;" onclick="sumarAnio(-1)" title="Año anterior">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <div class="d-flex align-items-center gap-1.5">
                        <span class="text-muted small fw-bold">Año:</span>
                        <select id="selectAnioPicker" class="form-select form-select-sm fw-bold border-0 bg-light text-center" 
                                style="width: 105px; font-size: 0.95rem; cursor: pointer;" onchange="alCambiarAnioEnSelect(this.value)">
                            @php
                                $anioMax = max(Carbon\Carbon::now()->year + 1, intval($anio));
                            @endphp
                            @for($y = $anioMax; $y >= 2024; $y--)
                                <option value="{{ $y }}" {{ $anio == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <button type="button" class="btn btn-sm btn-light border rounded-circle p-0 d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;" onclick="sumarAnio(1)" title="Año siguiente">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                <!-- Cuadrícula de los 12 Meses (100% en Español) -->
                <div class="row g-2 mb-3 text-center">
                    @php
                        $mesesAbrev = [
                            '01' => 'Ene', '02' => 'Feb', '03' => 'Mar', '04' => 'Abr',
                            '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago',
                            '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dic',
                        ];
                        $mesNum = $fechaMes->format('m');
                    @endphp
                    @foreach($mesesAbrev as $codigoMes => $nomAbrev)
                    <div class="col-4">
                        <button type="button" 
                                class="btn-mes-grid {{ ($mesNum == $codigoMes && $anio == $fechaMes->format('Y')) ? 'active' : '' }}" 
                                onclick="seleccionarMesDirecto('{{ $codigoMes }}')">
                            {{ $nomAbrev }}
                        </button>
                    </div>
                    @endforeach
                </div>

                <!-- Accesos Rápidos Inferiores -->
                <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                    <a href="/inicio?mes={{ $mesAnterior }}" class="btn btn-sm btn-link text-decoration-none text-muted small p-0 fw-semibold">
                        &larr; Mes Anterior
                    </a>
                    <a href="/inicio" class="btn btn-sm btn-primary fw-bold px-2.5 py-1 rounded-3 shadow-sm" style="font-size: 0.78rem;">
                        <i class="bi bi-clock-history me-1"></i> Mes Actual
                    </a>
                </div>
            </div>
        </div>

        @if(!$esMesActual)
            <a href="/inicio" class="btn btn-sm btn-primary fw-bold shadow-sm px-3 py-2" style="border-radius: 10px;">
                <i class="bi bi-calendar2-check me-1"></i> Ir a Mes Actual
            </a>
        @endif
    </div>
</div>

{{-- ======================================================== --}}
{{-- 2. FILA DE KPIs (TARJETAS MODERNAS CON DEGRADADOS SUAVES)  --}}
{{-- ======================================================== --}}
<div class="row g-3 mb-4">
    <!-- KPI 1: Ventas del Mes -->
    <div class="col-6 col-xl-3">
        <div class="card card-kpi-pro h-100" style="background: linear-gradient(145deg, #ffffff 60%, #fff1f2 100%);">
            <div class="card-body p-3 p-xl-3.5 d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-label" style="color: #e11d48;">VENTAS DEL MES</div>
                    <div class="kpi-amount" style="color: #e11d48;">Bs. {{ number_format($ventasMes, 2) }}</div>
                    <div class="kpi-meta">
                        <span>{{ $conteoVentasMes }} venta(s) en {{ $nombreMesActual }}</span>
                        @if($variacionVentas != 0)
                            <span class="{{ $variacionVentas > 0 ? 'text-success' : 'text-danger' }} fw-bold ms-1">
                                ({{ $variacionVentas > 0 ? '+' : '' }}{{ $variacionVentas }}%)
                            </span>
                        @endif
                    </div>
                </div>
                <div class="kpi-icon-circle" style="background: rgba(225, 29, 72, 0.1); color: #e11d48;">
                    <i class="bi bi-cart3"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- KPI 2: Margen Estimado -->
    <div class="col-6 col-xl-3">
        <div class="card card-kpi-pro h-100" style="background: linear-gradient(145deg, #ffffff 60%, #f0fdf4 100%);">
            <div class="card-body p-3 p-xl-3.5 d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-label" style="color: #16a34a;">MARGEN ESTIMADO $</div>
                    <div class="kpi-amount" style="color: #16a34a;">Bs. {{ number_format($margenEstimado, 2) }}</div>
                    <div class="kpi-meta">
                        <span>Rentabilidad estimada del período</span>
                    </div>
                </div>
                <div class="kpi-icon-circle" style="background: rgba(22, 163, 74, 0.1); color: #16a34a;">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI 3: Catálogo / Ventas de Hoy o Promedio Diario -->
    <div class="col-6 col-xl-3">
        <div class="card card-kpi-pro h-100" style="background: linear-gradient(145deg, #ffffff 60%, #f0f9ff 100%);">
            <div class="card-body p-3 p-xl-3.5 d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-label" style="color: #0284c7;">
                        {{ $esMesActual ? 'VENTAS DE HOY' : 'PROMEDIO DIARIO' }}
                    </div>
                    <div class="kpi-amount" style="color: #0284c7;">
                        Bs. {{ number_format($esMesActual ? $ventasHoy : $promedioDiario, 2) }}
                    </div>
                    <div class="kpi-meta">
                        <i class="bi bi-box-seam me-1 text-primary"></i> Catálogo: <strong>{{ $totalProductos }}</strong> productos
                    </div>
                </div>
                <div class="kpi-icon-circle" style="background: rgba(2, 132, 199, 0.1); color: #0284c7;">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI 4: Cuentas por Cobrar (Créditos Pendientes) -->
    <div class="col-6 col-xl-3">
        <div class="card card-kpi-pro h-100" style="background: linear-gradient(145deg, #ffffff 60%, #fffbeb 100%);">
            <div class="card-body p-3 p-xl-3.5 d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-label" style="color: #d97706;">POR COBRAR (CRÉDITO)</div>
                    <div class="kpi-amount" style="color: #d97706;">Bs. {{ number_format($totalPorCobrar, 2) }}</div>
                    <div class="kpi-meta">
                        <span>{{ $conteoPorCobrar }} nota(s) con saldo pendiente</span>
                    </div>
                </div>
                <div class="kpi-icon-circle" style="background: rgba(217, 119, 6, 0.1); color: #d97706;">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================== --}}
{{-- 3. FILA DE GRÁFICOS: VENTAS DEL AÑO & TOP PRODUCTOS (PASTEL) --}}
{{-- ======================================================== --}}
<div class="row g-4 mb-4">
    <!-- Gráfico 1: Ventas Mensuales del Año (Barras Rojas con Datos Reales de BD) -->
    <div class="col-lg-7">
        <div class="card card-chart-pro h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold m-0 text-dark">
                        <i class="bi bi-bar-chart-fill me-1 text-danger"></i> Ventas Mensuales (Bs.) &bull; Año {{ $anio }}
                    </h6>
                    <small class="text-muted">Ingresos reales acumulados en cada mes del sistema</small>
                </div>
                <span class="badge bg-light text-secondary border px-2.5 py-1 font-monospace" style="font-size: 0.72rem;">
                    Año {{ $anio }}
                </span>
            </div>
            <div class="card-body p-3 pt-2">
                <div style="height: 270px; position: relative;">
                    <canvas id="chartMensual"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico 2: Top Ventas por Producto (Gráfico de Pastel / Doughnut Top 5 / Top 10) -->
    <div class="col-lg-5">
        <div class="card card-chart-pro h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold m-0 text-dark">
                        <i class="bi bi-pie-chart-fill me-1 text-primary"></i> Top Productos Más Vendidos
                    </h6>
                    <small class="text-muted">Distribución de unidades vendidas</small>
                </div>
                <!-- Selector Top 5 / Top 10 -->
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-pill-filter active" id="btnTop5" onclick="cambiarTop(5, this)">
                        Top 5
                    </button>
                    <button type="button" class="btn btn-pill-filter" id="btnTop10" onclick="cambiarTop(10, this)">
                        Top 10
                    </button>
                </div>
            </div>
            <div class="card-body p-3 pt-2 d-flex flex-column justify-content-center">
                @if(count($top5Labels) > 0)
                <div style="height: 270px; position: relative;">
                    <canvas id="chartProductos"></canvas>
                </div>
                @else
                <div class="text-center py-5 my-auto text-muted">
                    <i class="bi bi-pie-chart fs-1 d-block mb-2 text-secondary opacity-50"></i>
                    <p class="mb-0 fw-semibold text-dark">Sin ventas registradas en el período</p>
                    <small>La gráfica de productos se generará al registrar ventas</small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ======================================================== --}}
{{-- 4. FILA: FLUJO DIARIO + TOP 5 CLIENTES REGULARES (EMBUDO) --}}
{{-- ======================================================== --}}
<div class="row g-4 mb-4">
    <!-- Gráfico 3: Flujo de Ventas Diarias con Filtros Reales -->
    <div class="col-lg-7">
        <div class="card card-chart-pro h-100">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h6 class="fw-bold m-0 text-dark" id="tituloFlujoDiario">
                        <i class="bi bi-activity me-1 text-success"></i> Flujo de Ventas Diarias (Últimos 15 días)
                    </h6>
                    <small class="text-muted">Comportamiento diario de las notas registradas</small>
                </div>
                <!-- Filtros Interactivos de Rango de Días -->
                <div class="d-flex gap-1 flex-wrap">
                    <button type="button" class="btn btn-pill-filter" onclick="cambiarRangoDiario('7', this, 'Últimos 7 días')">
                        7 días
                    </button>
                    <button type="button" class="btn btn-pill-filter active" onclick="cambiarRangoDiario('15', this, 'Últimos 15 días')">
                        15 días
                    </button>
                    <button type="button" class="btn btn-pill-filter" onclick="cambiarRangoDiario('30', this, 'Últimos 30 días')">
                        30 días
                    </button>
                    <button type="button" class="btn btn-pill-filter" onclick="cambiarRangoDiario('mes', this, 'Mes de {{ ucfirst($nombreMesActual) }}')">
                        Mes ({{ substr($nombreMesActual, 0, 3) }})
                    </button>
                </div>
            </div>
            <div class="card-body p-3 pt-2 d-flex flex-column justify-content-between">
                <div class="w-100 flex-grow-1 mb-2" style="position: relative; min-height: 335px;">
                    <canvas id="chartDiario"></canvas>
                </div>
                <div class="pt-2 border-top d-flex justify-content-between align-items-center text-muted small" style="font-size: 0.76rem;">
                    <span><i class="bi bi-calendar2-check me-1 text-success"></i> Rango activo: <strong class="text-dark fw-semibold" id="labelResumenPeriodo">Últimos 15 días</strong></span>
                    <span><i class="bi bi-graph-up me-1 text-primary"></i> Tendencia de notas registradas</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Métrica 4: Top 10 Clientes Más Regulares (Forma de Embudo / Funnel en Azul Degradado) -->
    <div class="col-lg-5">
        <div class="card card-chart-pro h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold m-0 text-dark">
                        <i class="bi bi-funnel-fill me-1 text-primary"></i> Top 10 Clientes Regulares
                    </h6>
                    <small class="text-muted">Embudo de compras acumuladas en {{ ucfirst($nombreMesActual) }}</small>
                </div>
                <a href="/clientes" class="btn btn-sm btn-link text-decoration-none fw-bold p-0 text-primary small">
                    Ver Todos &rarr;
                </a>
            </div>
            <div class="card-body p-3 pt-2 d-flex flex-column justify-content-between">
                <div class="funnel-container my-0">
                    @php
                        // Paleta Degradada Suave y Armónica: Tonalidad Azul y Celeste (sin tonos negros ni oscuros)
                        $gradientesAzul = [
                            1  => ['fondo' => 'linear-gradient(135deg, #2563eb 0%, #2a7aee 100%)', 'texto' => '#ffffff', 'badge' => 'rgba(255,255,255,0.28)', 'pill' => 'rgba(255,255,255,0.20)', 'border' => 'rgba(255,255,255,0.35)'],
                            2  => ['fondo' => 'linear-gradient(135deg, #2a7aee 0%, #2e90f2 100%)', 'texto' => '#ffffff', 'badge' => 'rgba(255,255,255,0.28)', 'pill' => 'rgba(255,255,255,0.20)', 'border' => 'rgba(255,255,255,0.35)'],
                            3  => ['fondo' => 'linear-gradient(135deg, #2e90f2 0%, #33a6f5 100%)', 'texto' => '#ffffff', 'badge' => 'rgba(255,255,255,0.28)', 'pill' => 'rgba(255,255,255,0.20)', 'border' => 'rgba(255,255,255,0.35)'],
                            4  => ['fondo' => 'linear-gradient(135deg, #33a6f5 0%, #38bdf8 100%)', 'texto' => '#ffffff', 'badge' => 'rgba(255,255,255,0.28)', 'pill' => 'rgba(255,255,255,0.20)', 'border' => 'rgba(255,255,255,0.35)'],
                            5  => ['fondo' => 'linear-gradient(135deg, #38bdf8 0%, #5ac8f9 100%)', 'texto' => '#034a75', 'badge' => 'rgba(3,74,117,0.14)', 'pill' => 'rgba(3,74,117,0.09)', 'border' => 'rgba(3,74,117,0.20)'],
                            6  => ['fondo' => 'linear-gradient(135deg, #5ac8f9 0%, #7cd3fa 100%)', 'texto' => '#034a75', 'badge' => 'rgba(3,74,117,0.14)', 'pill' => 'rgba(3,74,117,0.09)', 'border' => 'rgba(3,74,117,0.20)'],
                            7  => ['fondo' => 'linear-gradient(135deg, #7cd3fa 0%, #9edefc 100%)', 'texto' => '#034a75', 'badge' => 'rgba(3,74,117,0.14)', 'pill' => 'rgba(3,74,117,0.09)', 'border' => 'rgba(3,74,117,0.20)'],
                            8  => ['fondo' => 'linear-gradient(135deg, #9edefc 0%, #c0e9fd 100%)', 'texto' => '#034a75', 'badge' => 'rgba(3,74,117,0.14)', 'pill' => 'rgba(3,74,117,0.09)', 'border' => 'rgba(3,74,117,0.20)'],
                            9  => ['fondo' => 'linear-gradient(135deg, #c0e9fd 0%, #e2f4fe 100%)', 'texto' => '#034a75', 'badge' => 'rgba(3,74,117,0.14)', 'pill' => 'rgba(3,74,117,0.09)', 'border' => 'rgba(3,74,117,0.20)'],
                            10 => ['fondo' => 'linear-gradient(135deg, #e2f4fe 0%, #f0faff 100%)', 'texto' => '#034a75', 'badge' => 'rgba(3,74,117,0.14)', 'pill' => 'rgba(3,74,117,0.09)', 'border' => 'rgba(3,74,117,0.20)'],
                        ];
                        $anchosFunnel = [
                            1 => 100, 2 => 95, 3 => 90, 4 => 85, 5 => 80,
                            6 => 75,  7 => 70, 8 => 65, 9 => 60, 10 => 55
                        ];
                    @endphp

                    @for($rank = 1; $rank <= 10; $rank++)
                    @php
                        $cl = $topClientes->get($rank - 1);
                        $tieneCliente = !is_null($cl);
                        $anchoNivel = $anchosFunnel[$rank] ?? 50;
                        $estilo = $gradientesAzul[$rank];
                    @endphp
                    <div class="funnel-tier shadow-sm" 
                         style="width: {{ $anchoNivel }}%; background: {{ $estilo['fondo'] }}; {{ !$tieneCliente ? 'border: 1px dashed ' . ($rank <= 4 ? 'rgba(255,255,255,0.6)' : 'rgba(3,74,117,0.3)') . ';' : '' }}">
                        <div class="d-flex align-items-center justify-content-between px-3 py-1" style="color: {{ $estilo['texto'] }}; min-height: 26px;">
                            <div class="d-flex align-items-center me-2 text-truncate">
                                <span class="funnel-rank-badge me-2" style="background: {{ $estilo['badge'] }}; color: {{ $estilo['texto'] }};">
                                    #{{ $rank }}
                                </span>
                                <span class="fw-bold text-truncate" style="font-size: 0.81rem;" title="{{ $tieneCliente ? $cl->nombre_cliente : 'Posición #'.$rank.' disponible' }}">
                                    {{ $tieneCliente ? $cl->nombre_cliente : 'Posición #'.$rank.' disponible' }}
                                </span>
                                <span class="ms-2 px-2 py-0.5 rounded-pill fw-semibold flex-shrink-0" 
                                      style="font-size: 0.65rem; background: {{ $estilo['pill'] }}; color: {{ $estilo['texto'] }}; border: 1px solid {{ $estilo['border'] }};">
                                    {{ $tieneCliente ? $cl->total_compras . ' venta(s)' : 'Sin ventas' }}
                                </span>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <span class="fw-bold" style="font-size: 0.83rem; color: {{ $estilo['texto'] }};">
                                    Bs. {{ $tieneCliente ? number_format($cl->total_monto, 2) : '0.00' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>

                <div class="pt-2 border-top d-flex justify-content-between align-items-center text-muted small" style="font-size: 0.76rem;">
                    <span><i class="bi bi-cash-coin me-1 text-primary"></i> Total acumulado Top 10:</span>
                    <strong class="text-dark">Bs. {{ number_format($totalMontoTopClientes, 2) }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================== --}}
{{-- 5. FILA: PANEL OPERATIVO (STOCK CRÍTICO & ÚLTIMAS VENTAS)  --}}
{{-- ======================================================== --}}
<div class="row g-4 mb-4">
    <!-- Columna 1: Alertas de Stock Crítico / Bajo -->
    <div class="col-lg-6">
        <div class="card card-chart-pro h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold m-0 text-dark">
                        <i class="bi bi-exclamation-triangle-fill text-warning me-1.5"></i> Alertas de Stock Crítico
                        <span class="badge bg-warning bg-opacity-15 text-warning-emphasis border border-warning-subtle rounded-pill ms-1 px-2 py-0.5" style="font-size: 0.72rem;">
                            {{ $conteoBajoStock }} productos
                        </span>
                    </h6>
                    <small class="text-muted">Artículos con existencia igual o por debajo del stock mínimo</small>
                </div>
                <a href="/inventario" class="btn btn-sm btn-link text-decoration-none fw-bold p-0 text-primary small">
                    Gestionar Inventario &rarr;
                </a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($productosBajoStock as $prod)
                    <div class="list-group-item px-3 py-2.5 border-bottom d-flex justify-content-between align-items-center">
                        <div class="me-3 text-truncate">
                            <div class="fw-bold text-dark small text-truncate">{{ $prod->nombre }}</div>
                            <small class="text-muted font-monospace" style="font-size: 0.72rem;">
                                Cód: {{ $prod->codigo }} &bull; Mínimo requerido: <strong>{{ $prod->stock_minimo }}</strong>
                            </small>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <span class="badge {{ $prod->stock <= 0 ? 'bg-danger' : 'bg-warning text-dark' }} px-2.5 py-1 fw-bold">
                                {{ $prod->stock }} disponible(s)
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-check-circle-fill fs-2 text-success d-block mb-1"></i>
                        <span class="small fw-bold">Inventario al día: todos los productos cuentan con stock suficiente</span>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Columna 2: Registro de Últimas Ventas en Tiempo Real -->
    <div class="col-lg-6">
        <div class="card card-chart-pro h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold m-0 text-dark">
                        <i class="bi bi-receipt-cutoff text-primary me-1.5"></i> Últimas Ventas en Tiempo Real
                    </h6>
                    <small class="text-muted">Comprobantes y cobros registrados recientemente en caja</small>
                </div>
                <a href="/ventas" class="btn btn-sm btn-link text-decoration-none fw-bold p-0 text-primary small">
                    Ver Registro de Ventas &rarr;
                </a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($ultimasVentas as $v)
                    <div class="list-group-item px-3 py-2.5 border-bottom d-flex justify-content-between align-items-center">
                        <div class="me-2 text-truncate">
                            <div class="fw-bold text-dark small">
                                <span class="badge bg-light text-secondary border font-monospace me-1">{{ $v->numero_nota }}</span>
                                <span class="text-truncate">{{ $v->cliente }}</span>
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">
                                <i class="bi bi-clock me-0.5"></i> {{ \Carbon\Carbon::parse($v->fecha)->format('d/m/Y H:i') }} &bull; 
                                <span class="badge bg-info bg-opacity-10 text-primary px-1.5 py-0.5">{{ $v->metodo_pago }}</span>
                            </small>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div class="fw-extrabold text-success small">Bs. {{ number_format($v->total, 2) }}</div>
                            <a href="/ventas/{{ $v->id }}/pdf" target="_blank" class="text-decoration-none text-muted small" style="font-size: 0.72rem;">
                                <i class="bi bi-file-pdf text-danger me-0.5"></i> Nota PDF
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-cart-x fs-2 d-block mb-1 text-secondary opacity-50"></i>
                        <span class="small">No hay transacciones registradas</span>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================== --}}
{{-- 5. SCRIPTS: CHART.JS & CONTROL DEL SELECTOR DE PERÍODO   --}}
{{-- ======================================================== --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Configuración tipográfica ejecutiva
    Chart.defaults.font.family = "'Segoe UI', system-ui, -apple-system, sans-serif";
    Chart.defaults.color = '#64748b';
    const gridOptions = { color: '#f1f5f9', drawBorder: false };

    // ============================================================
    // 1. VENTAS MENSUALES DEL AÑO (DATOS REALES - PLANO SIN VENTAS)
    // ============================================================
    const ctxMensual = document.getElementById('chartMensual');
    if (ctxMensual) {
        new Chart(ctxMensual, {
            type: 'bar',
            data: {
                labels: @json($mesesLabels),
                datasets: [{
                    label: 'Ventas Bs.',
                    data: @json($ventasPorMesAnio), // Reales desde MySQL
                    backgroundColor: '#e11d48',
                    borderRadius: 4,
                    maxBarThickness: 32
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' Ventas: Bs. ' + context.parsed.y.toLocaleString('es-BO', { minimumFractionDigits: 2 });
                            }
                        }
                    }
                },
                scales: { 
                    x: { grid: { display: false } }, 
                    y: { 
                        grid: gridOptions,
                        ticks: {
                            callback: function(value) {
                                return 'Bs. ' + value;
                            }
                        }
                    } 
                }
            }
        });
    }

    // ============================================================
    // 2. TOP VENTAS POR PRODUCTO (GRÁFICA DE PASTEL / DOUGHNUT)
    // ============================================================
    const paletaColoresPastel = [
        '#0284c7', // Sky Blue
        '#10b981', // Emerald
        '#f59e0b', // Amber
        '#6366f1', // Indigo
        '#ec4899', // Pink
        '#8b5cf6', // Violet
        '#14b8a6', // Teal
        '#f97316', // Orange
        '#06b6d4', // Cyan
        '#e11d48'  // Rose
    ];

    const dataTop5 = {
        labels: @json($top5Labels),
        data: @json($top5Cant),
        colors: paletaColoresPastel.slice(0, 5)
    };
    const dataTop10 = {
        labels: @json($top10Labels),
        data: @json($top10Cant),
        colors: paletaColoresPastel.slice(0, 10)
    };

    let chartProductosInstance = null;
    const ctxProductos = document.getElementById('chartProductos');
    if (ctxProductos) {
        chartProductosInstance = new Chart(ctxProductos, {
            type: 'doughnut',
            data: {
                labels: dataTop5.labels,
                datasets: [{
                    data: dataTop5.data,
                    backgroundColor: dataTop5.colors,
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '56%',
                plugins: {
                    legend: {
                        position: 'right',
                        align: 'center',
                        labels: {
                            boxWidth: 12,
                            boxHeight: 12,
                            padding: 8,
                            font: {
                                size: 11,
                                family: "'Segoe UI', system-ui, sans-serif"
                            },
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    return data.labels.map((label, i) => {
                                        const valor = data.datasets[0].data[i];
                                        const pct = total > 0 ? Math.round((valor / total) * 100) : 0;
                                        const nombreCorto = label.length > 18 ? label.substring(0, 16) + '...' : label;
                                        return {
                                            text: `${nombreCorto} (${valor} u. - ${pct}%)`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: isNaN(data.datasets[0].data[i]) || chart.getDatasetMeta(0).data[i].hidden,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const valor = context.parsed || 0;
                                const ds = context.dataset;
                                const total = ds.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? ((valor / total) * 100).toFixed(1) : 0;
                                return ` ${label}: ${valor} u. (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    window.cambiarTop = function(cantidad, btn) {
        if (!chartProductosInstance) return;
        
        btn.parentElement.querySelectorAll('.btn-pill-filter').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        if (cantidad === 5) {
            chartProductosInstance.data.labels = dataTop5.labels;
            chartProductosInstance.data.datasets[0].data = dataTop5.data;
            chartProductosInstance.data.datasets[0].backgroundColor = dataTop5.colors;
        } else {
            chartProductosInstance.data.labels = dataTop10.labels;
            chartProductosInstance.data.datasets[0].data = dataTop10.data;
            chartProductosInstance.data.datasets[0].backgroundColor = dataTop10.colors;
        }
        chartProductosInstance.update();
    };

    // ============================================================
    // 3. FLUJO DE VENTAS DIARIAS (FILTROS 7, 15, 30 DÍAS Y MES)
    // ============================================================
    const datosRangosDiarios = @json($rangosDiarios);
    let chartDiarioInstance = null;
    const ctxDiario = document.getElementById('chartDiario');

    if (ctxDiario) {
        const datosIniciales = datosRangosDiarios['15'] || { labels: [], valores: [] };

        chartDiarioInstance = new Chart(ctxDiario, {
            type: 'line',
            data: {
                labels: datosIniciales.labels,
                datasets: [{
                    label: 'Ventas Bs.',
                    data: datosIniciales.valores,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.12)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointBackgroundColor: '#10b981'
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' Total: Bs. ' + context.parsed.y.toLocaleString('es-BO', { minimumFractionDigits: 2 });
                            }
                        }
                    }
                },
                scales: { 
                    x: { grid: { display: false } }, 
                    y: { 
                        grid: gridOptions,
                        ticks: {
                            callback: function(value) {
                                return 'Bs. ' + value;
                            }
                        }
                    } 
                }
            }
        });
    }

    window.cambiarRangoDiario = function(rango, btn, tituloTexto) {
        if (!chartDiarioInstance) return;

        btn.parentElement.querySelectorAll('.btn-pill-filter').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const seleccionado = datosRangosDiarios[rango];
        if (seleccionado) {
            chartDiarioInstance.data.labels = seleccionado.labels;
            chartDiarioInstance.data.datasets[0].data = seleccionado.valores;
            chartDiarioInstance.update();
        }

        const tituloEl = document.getElementById('tituloFlujoDiario');
        if (tituloEl && tituloTexto) {
            tituloEl.innerHTML = '<i class="bi bi-activity me-1 text-success"></i> Flujo de Ventas Diarias (' + tituloTexto + ')';
        }

        const labelPeriodo = document.getElementById('labelResumenPeriodo');
        if (labelPeriodo && tituloTexto) {
            labelPeriodo.textContent = tituloTexto;
        }
    };
});

// ============================================================
// FUNCIONES DEL SELECTOR DE PERÍODO Y AÑO PERSONALIZADO
// ============================================================
let anioActualPicker = {{ $anio }};

function sumarAnio(delta) {
    anioActualPicker += delta;
    const select = document.getElementById('selectAnioPicker');
    if (select) {
        // Verificar si existe la opción, si no, crearla
        let opt = select.querySelector(`option[value="${anioActualPicker}"]`);
        if (!opt) {
            opt = document.createElement('option');
            opt.value = anioActualPicker;
            opt.textContent = anioActualPicker;
            if (delta > 0) {
                select.insertBefore(opt, select.firstChild);
            } else {
                select.appendChild(opt);
            }
        }
        select.value = anioActualPicker;
    }
}

function alCambiarAnioEnSelect(nuevoAnio) {
    anioActualPicker = parseInt(nuevoAnio);
}

function seleccionarMesDirecto(mesDosDigitos) {
    const mesFinal = `${anioActualPicker}-${mesDosDigitos}`;
    window.location.href = `/inicio?mes=${mesFinal}`;
}
</script>
@endsection
