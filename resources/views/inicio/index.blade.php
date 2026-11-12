@extends('layouts.app')

@section('content')
<!-- Chart.js para visualización de analíticas -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* ============================================================ */
    /* ESTILOS DE DISEÑO SAAS / EMS DASHBOARD                       */
    /* ============================================================ */
    
    /* Animación de entrada suave para tarjetas */
    @keyframes emsFadeSlideUp {
        from {
            opacity: 0;
            transform: translateY(12px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .ems-animate-in {
        animation: emsFadeSlideUp 0.35s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    /* Badge con Punto Pulsante de Monitoreo en Vivo */
    .badge-ems-pulse {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 5px 12px;
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
        border-radius: 9999px;
        font-size: 0.74rem;
        font-weight: 700;
        letter-spacing: 0.3px;
    }
    .ems-pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #0284c7;
        position: relative;
    }
    .ems-pulse-dot::after {
        content: '';
        position: absolute;
        top: -3px;
        left: -3px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background-color: rgba(2, 132, 199, 0.4);
        animation: emsPulseAnim 1.8s infinite ease-out;
    }
    @keyframes emsPulseAnim {
        0% { transform: scale(0.6); opacity: 1; }
        100% { transform: scale(1.6); opacity: 0; }
    }

    /* Badge de estado del período */
    .badge-status-subtle {
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 0.72rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
    }
    .status-active {
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .status-archived {
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    /* Barra / Botón Selector de Período (Estilo Filtro Toolbar Figma) */
    .btn-periodo-toolbar {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 14px;
        padding: 7px 14px;
        color: #0f172a;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    .btn-periodo-toolbar:hover {
        background: #f8fafc;
        border-color: #0284c7;
        box-shadow: 0 4px 14px rgba(2, 132, 199, 0.15);
        transform: translateY(-1px);
    }
    .periodo-toolbar-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: #e0f2fe;
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .periodo-toolbar-sub {
        font-size: 0.66rem;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 800;
        letter-spacing: 0.6px;
        line-height: 1.1;
    }
    .periodo-toolbar-title {
        font-size: 0.92rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }

    /* Contenedor y Menú del Selector de Período */
    #dropdownPeriodoContainer {
        position: relative;
        z-index: 1080;
    }
    .dropdown-periodo-menu {
        width: 320px;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.22);
        padding: 16px;
        background: #ffffff;
        z-index: 1090 !important;
    }
    .btn-mes-grid {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 9px;
        padding: 8px 4px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #334155;
        transition: all 0.18s ease;
        width: 100%;
    }
    .btn-mes-grid:hover {
        background: #e0f2fe;
        color: #0284c7;
        border-color: #bae6fd;
    }
    .btn-mes-grid.active {
        background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%) !important;
        border-color: #0284c7 !important;
        color: #ffffff !important;
        box-shadow: 0 3px 10px rgba(2, 132, 199, 0.35);
    }

    /* ============================================================ */
    /* 4 TARJETAS KPI (ESTILO EMS HIGH CONTRAST / LUXURY OCEANIC)   */
    /* ============================================================ */
    .card-kpi-ems {
        background: linear-gradient(145deg, #0d233a 0%, #081626 100%);
        border: 1px solid rgba(56, 189, 248, 0.22);
        border-radius: 20px;
        box-shadow: 0 8px 24px -4px rgba(7, 20, 36, 0.25);
        color: #ffffff;
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s ease, border-color 0.25s ease;
        position: relative;
        overflow: hidden;
    }
    .card-kpi-ems::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(56, 189, 248, 0.45), transparent);
    }
    .card-kpi-ems:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 36px -4px rgba(14, 165, 233, 0.3);
        border-color: rgba(56, 189, 248, 0.45);
    }
    .kpi-ems-code {
        color: #38bdf8;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
    }
    .kpi-ems-badge {
        background: rgba(56, 189, 248, 0.12);
        color: #7dd3fc;
        border: 1px solid rgba(56, 189, 248, 0.25);
        border-radius: 20px;
        font-size: 0.70rem;
        font-weight: 700;
        padding: 2px 9px;
        display: inline-flex;
        align-items: center;
    }
    .kpi-ems-amount {
        font-size: 1.82rem;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.5px;
        line-height: 1.1;
    }
    .kpi-ems-footer {
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        padding-top: 10px;
        margin-top: 4px;
    }
    .kpi-ems-sub {
        color: #94a3b8;
        font-size: 0.74rem;
        font-weight: 500;
    }
    .kpi-ems-trend {
        color: #e2e8f0;
        font-size: 0.74rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }
    .text-cyan-accent {
        color: #38bdf8 !important;
    }
    .text-danger-accent {
        color: #f87171 !important;
    }

    /* ============================================================ */
    /* TARJETAS BLANCAS DE CONTENEDOR / GRÁFICOS                    */
    /* ============================================================ */
    .card-chart-ems {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s ease;
    }
    .card-chart-ems:hover {
        box-shadow: 0 10px 28px -4px rgba(15, 23, 42, 0.08);
    }
    .card-chart-ems .card-header {
        background: transparent;
        border-bottom: 1px solid #f1f5f9;
        padding: 16px 20px;
    }

    /* Botones de Filtro Activos (Pills) */
    .btn-pill-filter {
        font-size: 0.76rem;
        font-weight: 700;
        border-radius: 8px;
        padding: 4px 11px;
        color: #64748b;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        transition: all 0.18s ease;
    }
    .btn-pill-filter:hover {
        background-color: #e0f2fe;
        color: #0284c7;
        border-color: #bae6fd;
    }
    .btn-pill-filter.active {
        background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%) !important;
        border-color: #0284c7 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(2, 132, 199, 0.35);
    }

    /* Header icon box */
    .header-icon-box {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: #e0f2fe;
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        flex-shrink: 0;
    }

    /* ============================================================ */
    /* EMBUDO DE CLIENTES REGULARES                                 */
    /* ============================================================ */
    .funnel-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        width: 100%;
        padding: 0;
    }
    .funnel-tier {
        border-radius: 9px;
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s ease, filter 0.2s ease;
        position: relative;
        cursor: pointer;
        overflow: hidden;
    }
    .funnel-tier:hover {
        transform: translateY(-1px) scale(1.015);
        filter: brightness(1.06);
        box-shadow: 0 6px 18px rgba(2, 132, 199, 0.28) !important;
        z-index: 5;
    }
    .funnel-tier:active {
        transform: scale(0.99);
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
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 ems-animate-in" style="position: relative; z-index: 1050;">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1.5">
            <span class="badge-ems-pulse">
                <span class="ems-pulse-dot"></span> Monitoreo en Tiempo Real
            </span>
            @if($esMesActual)
                <span class="badge-status-subtle status-active">
                    <i class="bi bi-calendar2-check me-1"></i> Mes en Curso
                </span>
            @else
                <span class="badge-status-subtle status-archived">
                    <i class="bi bi-archive me-1"></i> Historial Archivado
                </span>
            @endif
        </div>
        <h2 class="fw-extrabold text-dark m-0 tracking-tight" style="font-size: 1.7rem; font-weight: 800; letter-spacing: -0.5px;">
            Hola, {{ optional(auth()->user())->name ? explode(' ', auth()->user()->name)[0] : 'Usuario' }}! 👋
        </h2>
        <p class="text-muted m-0 small" style="font-weight: 500;">
            ¿Qué deseas gestionar hoy? &bull; Resumen ejecutivo comercial &bull; Período: <strong class="text-dark">{{ ucfirst($nombreMesActual) }} {{ $anio }}</strong>
        </p>
    </div>

    <!-- TOOLBAR SELECTOR DE PERÍODO (ESTILO FIGMA) -->
    <div class="d-flex align-items-center flex-wrap gap-2">
        <div class="dropdown position-relative" id="dropdownPeriodoContainer">
            <button type="button" class="btn-periodo-toolbar" id="btnDropdownPeriodo" 
                    data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                <div class="periodo-toolbar-icon">
                    <i class="bi bi-calendar3"></i>
                </div>
                <div class="text-start pe-2">
                    <div class="periodo-toolbar-sub">Período Seleccionado</div>
                    <div class="periodo-toolbar-title" id="txtLabelPeriodoActivo">
                        {{ ucfirst($nombreMesActual) }} {{ $anio }}
                    </div>
                </div>
                <i class="bi bi-chevron-down text-muted" style="font-size: 0.78rem;"></i>
            </button>

            <!-- Menú Desplegable con Selector de Año y Cuadrícula de 12 Meses -->
            <div class="dropdown-menu dropdown-menu-end dropdown-periodo-menu shadow-lg" id="menuPeriodoPicker">
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
            <a href="/inicio" class="btn btn-primary fw-bold px-3 py-2 d-flex align-items-center gap-1.5" style="border-radius: 12px; font-size: 0.85rem;">
                <i class="bi bi-calendar2-check"></i> Ir a Mes Actual
            </a>
        @endif
    </div>
</div>

{{-- ======================================================== --}}
{{-- 2. 4 TARJETAS KPI (ESTILO EMS HIGH CONTRAST LUXURY)       --}}
{{-- ======================================================== --}}
<div class="row g-3 mb-4 ems-animate-in">
    <!-- KPI 1: Ventas del Mes -->
    <div class="col-6 col-xl-3">
        <div class="card card-kpi-ems h-100">
            <div class="card-body p-3.5 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="kpi-ems-code">VENTAS</span>
                    <span class="kpi-ems-badge">
                        <i class="bi bi-cart3 me-1"></i> {{ $esMesActual ? 'Mes Activo' : 'Cerrado' }}
                    </span>
                </div>
                <div class="my-2">
                    <div class="kpi-ems-amount">Bs. {{ number_format($ventasMes, 2) }}</div>
                </div>
                <div class="d-flex justify-content-between align-items-center kpi-ems-footer">
                    <span class="kpi-ems-sub">Ventas del período</span>
                    <span class="kpi-ems-trend">
                        {{ $conteoVentasMes }} venta(s)
                        @if($variacionVentas != 0)
                            <span class="{{ $variacionVentas > 0 ? 'text-cyan-accent' : 'text-danger-accent' }}">
                                ({{ $variacionVentas > 0 ? '+' : '' }}{{ $variacionVentas }}%)
                            </span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- KPI 2: Margen Estimado -->
    <div class="col-6 col-xl-3">
        <div class="card card-kpi-ems h-100">
            <div class="card-body p-3.5 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="kpi-ems-code">RENTABILIDAD</span>
                    <span class="kpi-ems-badge">
                        <i class="bi bi-cash-stack me-1"></i> Estimado
                    </span>
                </div>
                <div class="my-2">
                    <div class="kpi-ems-amount">Bs. {{ number_format($margenEstimado, 2) }}</div>
                </div>
                <div class="d-flex justify-content-between align-items-center kpi-ems-footer">
                    <span class="kpi-ems-sub">Margen comercial</span>
                    <span class="kpi-ems-trend">Rentabilidad neta</span>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI 3: Catálogo / Ventas de Hoy o Promedio Diario -->
    <div class="col-6 col-xl-3">
        <div class="card card-kpi-ems h-100">
            <div class="card-body p-3.5 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="kpi-ems-code">OPERACIONES</span>
                    <span class="kpi-ems-badge">
                        <i class="bi bi-activity me-1"></i> En Vivo
                    </span>
                </div>
                <div class="my-2">
                    <div class="kpi-ems-amount">Bs. {{ number_format($esMesActual ? $ventasHoy : $promedioDiario, 2) }}</div>
                </div>
                <div class="d-flex justify-content-between align-items-center kpi-ems-footer">
                    <span class="kpi-ems-sub">{{ $esMesActual ? 'Ventas de hoy' : 'Promedio diario' }}</span>
                    <span class="kpi-ems-trend">{{ $totalProductos }} productos</span>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI 4: Cuentas por Cobrar (Créditos Pendientes) -->
    <div class="col-6 col-xl-3">
        <div class="card card-kpi-ems h-100">
            <div class="card-body p-3.5 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="kpi-ems-code">POR COBRAR</span>
                    <span class="kpi-ems-badge">
                        <i class="bi bi-wallet2 me-1"></i> Crédito
                    </span>
                </div>
                <div class="my-2">
                    <div class="kpi-ems-amount">Bs. {{ number_format($totalPorCobrar, 2) }}</div>
                </div>
                <div class="d-flex justify-content-between align-items-center kpi-ems-footer">
                    <span class="kpi-ems-sub">Saldo por cobrar</span>
                    <span class="kpi-ems-trend">{{ $conteoPorCobrar }} nota(s)</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================== --}}
{{-- 3. FILA DE GRÁFICOS: VENTAS DEL AÑO & TOP PRODUCTOS (PASTEL) --}}
{{-- ======================================================== --}}
<div class="row g-4 mb-4 ems-animate-in">
    <!-- Gráfico 1: Ventas Mensuales del Año (Barras en Azul Tonal con Datos Reales) -->
    <div class="col-lg-7">
        <div class="card card-chart-ems h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="header-icon-box">
                        <i class="bi bi-bar-chart-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold m-0 text-dark" style="font-size: 0.95rem;">
                            Ventas Mensuales (Bs.) &bull; Año {{ $anio }}
                        </h6>
                        <small class="text-muted">Ingresos reales acumulados en cada mes del sistema</small>
                    </div>
                </div>
                <span class="badge px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.72rem; background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;">
                    Gestión {{ $anio }}
                </span>
            </div>
            <div class="card-body p-3.5 pt-2">
                <div style="height: 275px; position: relative;">
                    <canvas id="chartMensual"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico 2: Top Ventas por Producto (PASTEL / DOUGHNUT CON ARCOÍRIS PRESERVADO) -->
    <div class="col-lg-5">
        <div class="card card-chart-ems h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="header-icon-box">
                        <i class="bi bi-pie-chart-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold m-0 text-dark" style="font-size: 0.95rem;">
                            Top Productos Más Vendidos
                        </h6>
                        <small class="text-muted">Distribución de unidades vendidas</small>
                    </div>
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
            <div class="card-body p-3.5 pt-2 d-flex flex-column justify-content-center">
                @if(count($top5Labels) > 0)
                <div style="height: 275px; position: relative;">
                    <canvas id="chartProductos"></canvas>
                </div>
                @else
                <div class="text-center py-5 my-auto text-muted">
                    <div class="mx-auto mb-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #f8fafc; border: 1px dashed #cbd5e1;">
                        <i class="bi bi-pie-chart fs-4 text-secondary opacity-50"></i>
                    </div>
                    <p class="mb-0 fw-bold text-dark small">Sin ventas registradas en el período</p>
                    <small class="text-muted" style="font-size: 0.75rem;">La gráfica se generará automáticamente al registrar ventas</small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ======================================================== --}}
{{-- 4. FILA: FLUJO DIARIO + TOP 10 CLIENTES REGULARES (EMBUDO) --}}
{{-- ======================================================== --}}
<div class="row g-4 mb-4 ems-animate-in">
    <!-- Gráfico 3: Flujo de Ventas Diarias con Filtros Interactivos -->
    <div class="col-lg-7">
        <div class="card card-chart-ems h-100">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="header-icon-box">
                        <i class="bi bi-activity"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold m-0 text-dark" id="tituloFlujoDiario" style="font-size: 0.95rem;">
                            Flujo de Ventas Diarias (Últimos 15 días)
                        </h6>
                        <small class="text-muted">Comportamiento diario de las notas registradas</small>
                    </div>
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
            <div class="card-body p-3.5 pt-2 d-flex flex-column justify-content-between">
                <div class="w-100 flex-grow-1 mb-2" style="position: relative; min-height: 335px;">
                    <canvas id="chartDiario"></canvas>
                </div>
                <div class="pt-2 border-top d-flex justify-content-between align-items-center text-muted small" style="font-size: 0.76rem;">
                    <span><i class="bi bi-calendar2-check me-1 text-primary"></i> Rango activo: <strong class="text-dark fw-bold" id="labelResumenPeriodo">Últimos 15 días</strong></span>
                    <span><i class="bi bi-graph-up me-1 text-primary"></i> Tendencia de recaudación diaria</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Métrica 4: Top 10 Clientes Más Regulares (Forma de Embudo / Funnel en Azul Degradado) -->
    <div class="col-lg-5">
        <div class="card card-chart-ems h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="header-icon-box">
                        <i class="bi bi-funnel-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold m-0 text-dark" style="font-size: 0.95rem;">
                            Top 10 Clientes Regulares
                        </h6>
                        <small class="text-muted">Embudo de compras acumuladas en {{ ucfirst($nombreMesActual) }}</small>
                    </div>
                </div>
                <a href="/clientes" class="btn btn-sm btn-link text-decoration-none fw-bold p-0 text-primary small">
                    Ver Todos &rarr;
                </a>
            </div>
            <div class="card-body p-3.5 pt-2 d-flex flex-column justify-content-between">
                <div class="funnel-container my-0">
                    @php
                        // Paleta Degradada Suave y Armónica: Tonalidad Azul y Celeste (Aprobada por el Usuario)
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
                            1 => 100, 2 => 97, 3 => 94, 4 => 91, 5 => 88,
                            6 => 85,  7 => 82, 8 => 79, 9 => 76, 10 => 73
                        ];
                    @endphp

                    @for($rank = 1; $rank <= 10; $rank++)
                    @php
                        $cl = $topClientes->get($rank - 1);
                        $tieneCliente = !is_null($cl);
                        $anchoNivel = $anchosFunnel[$rank] ?? 73;
                        $estilo = $gradientesAzul[$rank];
                        $nombreClienteRaw = $tieneCliente ? $cl->nombre_cliente : '';
                        $nombreMostrar = $tieneCliente ? $cl->nombre_cliente : 'Posición #'.$rank.' disponible';
                        $tooltipHtml = $tieneCliente 
                            ? "<strong>🏆 Top #{$rank}: " . e($cl->nombre_cliente) . "</strong><br><small>{$cl->total_compras} compra(s) &bull; Total: Bs. " . number_format($cl->total_monto, 2) . "</small><br><span style='font-size:0.7rem; color:#bae6fd;'><i class='bi bi-cursor-fill me-1'></i>Haz clic para ver ficha y detalles</span>"
                            : "<strong>Posición #{$rank} Disponible</strong><br><small>Aún no registra compras en este mes</small>";
                    @endphp
                    <div class="funnel-tier shadow-sm" 
                         style="width: {{ $anchoNivel }}%; background: {{ $estilo['fondo'] }}; {{ !$tieneCliente ? 'border: 1px dashed ' . ($rank <= 4 ? 'rgba(255,255,255,0.6)' : 'rgba(3,74,117,0.3)') . ';' : '' }}"
                         data-bs-toggle="tooltip"
                         data-bs-html="true"
                         data-bs-placement="top"
                         title="{{ $tooltipHtml }}"
                         onclick="abrirModalTopCliente({{ $rank }}, '{{ addslashes($nombreClienteRaw) }}', '{{ $tieneCliente ? $cl->total_compras : 0 }}', '{{ $tieneCliente ? number_format($cl->total_monto, 2) : '0.00' }}', {{ $tieneCliente ? 'true' : 'false' }})">
                        <div class="d-flex align-items-center justify-content-between px-3 py-1" style="color: {{ $estilo['texto'] }}; min-height: 27px;">
                            <div class="d-flex align-items-center me-2 text-truncate" style="flex: 1 1 auto; min-width: 0;">
                                <span class="funnel-rank-badge me-2" style="background: {{ $estilo['badge'] }}; color: {{ $estilo['texto'] }};">
                                    #{{ $rank }}
                                </span>
                                <span class="fw-bold text-truncate" style="font-size: 0.81rem;" title="{{ $nombreMostrar }}">
                                    {{ $nombreMostrar }}
                                </span>
                                @if($tieneCliente)
                                    <span class="ms-2 px-2 py-0.5 rounded-pill fw-semibold flex-shrink-0 d-none d-sm-inline-block" 
                                          style="font-size: 0.65rem; background: {{ $estilo['pill'] }}; color: {{ $estilo['texto'] }}; border: 1px solid {{ $estilo['border'] }};">
                                        {{ $cl->total_compras }} venta(s)
                                    </span>
                                @endif
                            </div>
                            <div class="text-end flex-shrink-0 ms-1">
                                <span class="fw-bold" style="font-size: 0.83rem; color: {{ $estilo['texto'] }};">
                                    Bs. {{ $tieneCliente ? number_format($cl->total_monto, 2) : '0.00' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>

                <div class="pt-2 border-top d-flex flex-wrap justify-content-between align-items-center text-muted small" style="font-size: 0.74rem;">
                    <span><i class="bi bi-cursor-fill me-1 text-primary"></i>Haz clic en cualquier puesto para ver detalles</span>
                    <strong class="text-dark">Total: Bs. {{ number_format($totalMontoTopClientes, 2) }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================== --}}
{{-- 5. FILA: PANEL OPERATIVO (STOCK CRÍTICO & ÚLTIMAS VENTAS)  --}}
{{-- ======================================================== --}}
<div class="row g-4 mb-4 ems-animate-in">
    <!-- Columna 1: Alertas de Stock Crítico / Bajo -->
    <div class="col-lg-6">
        <div class="card card-chart-ems h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="header-icon-box" style="background: #fef3c7; color: #d97706;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold m-0 text-dark" style="font-size: 0.95rem;">
                            Alertas de Stock Crítico
                            <span class="badge rounded-pill ms-1 px-2 py-0.5 fw-bold" style="font-size: 0.72rem; background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
                                {{ $conteoBajoStock }} productos
                            </span>
                        </h6>
                        <small class="text-muted">Artículos con existencia igual o por debajo del stock mínimo</small>
                    </div>
                </div>
                <a href="/inventario" class="btn btn-sm btn-link text-decoration-none fw-bold p-0 text-primary small">
                    Inventario &rarr;
                </a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($productosBajoStock as $prod)
                    <div class="list-group-item px-3.5 py-2.5 border-bottom d-flex justify-content-between align-items-center" style="transition: background-color 0.15s ease;">
                        <div class="me-3 text-truncate">
                            <div class="fw-bold text-dark small text-truncate">{{ $prod->nombre }}</div>
                            <small class="text-muted font-monospace" style="font-size: 0.72rem;">
                                Cód: {{ $prod->codigo }} &bull; Mínimo: <strong>{{ $prod->stock_minimo }}</strong>
                            </small>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <span class="badge px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.72rem; background: {{ $prod->stock <= 0 ? '#fee2e2' : '#fef3c7' }}; color: {{ $prod->stock <= 0 ? '#b91c1c' : '#b45309' }}; border: 1px solid {{ $prod->stock <= 0 ? '#fecaca' : '#fde68a' }};">
                                {{ $prod->stock }} disp.
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-check-circle-fill fs-2 text-primary d-block mb-1 opacity-75"></i>
                        <span class="small fw-bold">Inventario al día: todos los productos cuentan con stock suficiente</span>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Columna 2: Registro de Últimas Ventas en Tiempo Real -->
    <div class="col-lg-6">
        <div class="card card-chart-ems h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="header-icon-box">
                        <i class="bi bi-receipt-cutoff"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold m-0 text-dark" style="font-size: 0.95rem;">
                            Últimas Ventas en Tiempo Real
                        </h6>
                        <small class="text-muted">Comprobantes y cobros registrados recientemente en caja</small>
                    </div>
                </div>
                <a href="/ventas" class="btn btn-sm btn-link text-decoration-none fw-bold p-0 text-primary small">
                    Ver Ventas &rarr;
                </a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($ultimasVentas as $v)
                    <div class="list-group-item px-3.5 py-2.5 border-bottom d-flex justify-content-between align-items-center" style="transition: background-color 0.15s ease;">
                        <div class="me-2 text-truncate">
                            <div class="fw-bold text-dark small">
                                <span class="badge bg-light text-secondary border font-monospace me-1">{{ $v->numero_nota }}</span>
                                <span class="text-truncate">{{ $v->cliente }}</span>
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">
                                <i class="bi bi-clock me-0.5"></i> {{ \Carbon\Carbon::parse($v->fecha)->format('d/m/Y H:i') }} &bull; 
                                <span class="badge px-1.5 py-0.5 rounded-pill" style="background: #e0f2fe; color: #0284c7;">{{ $v->metodo_pago }}</span>
                            </small>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div class="fw-bold text-dark small" style="font-size: 0.88rem;">Bs. {{ number_format($v->total, 2) }}</div>
                            <a href="/ventas/{{ $v->id }}/pdf" target="_blank" class="text-decoration-none text-muted small" style="font-size: 0.72rem;">
                                <i class="bi bi-file-pdf text-danger me-0.5"></i> PDF
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
{{-- 6. MODAL INTERACTIVO: DETALLE DE CLIENTE TOP (EMBUDO)    --}}
{{-- ======================================================== --}}
<div class="modal fade" id="modalDetalleClienteTop" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 pb-0 pt-3.5 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #0b1a2d 0%, #0f2847 100%); color: #ffffff;">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(56, 189, 248, 0.2); color: #38bdf8;">
                        <i class="bi bi-trophy-fill fs-5"></i>
                    </div>
                    <div>
                        <span class="badge rounded-pill px-2.5 py-0.5 fw-bold" id="topModalRankBadge" style="font-size: 0.72rem; background: rgba(56, 189, 248, 0.18); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.35);">
                            Top #1 del Mes
                        </span>
                        <h6 class="m-0 fw-bold text-white small">Cliente Regular Destacado</h6>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4 pt-3" style="background: #ffffff;">
                <!-- Nombre completo grande y sin truncar -->
                <div class="p-3 rounded-3 mb-3 border" style="background: #f8fafc; border-color: #e2e8f0 !important;">
                    <div class="text-muted small fw-bold mb-1" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="bi bi-person-badge me-1 text-primary"></i> Nombre del Cliente
                    </div>
                    <h5 class="fw-extrabold text-dark m-0" id="topModalClienteNombre" style="word-break: break-word; line-height: 1.3;">
                        -
                    </h5>
                </div>

                <!-- Tarjetas de Métricas del Cliente -->
                <div class="row g-2 mb-3" id="topModalMetricasRow">
                    <div class="col-6">
                        <div class="p-3 rounded-3 h-100 border text-center" style="background: #f0f9ff; border-color: #bae6fd !important;">
                            <div class="text-muted small fw-bold" style="font-size: 0.70rem; text-transform: uppercase;">Total Comprado</div>
                            <div class="fw-extrabold text-primary fs-5 mt-1" id="topModalTotalMonto">Bs. 0.00</div>
                            <small class="text-muted" style="font-size: 0.68rem;">Acumulado del mes</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3 h-100 border text-center" style="background: #f0fdf4; border-color: #bbf7d0 !important;">
                            <div class="text-muted small fw-bold" style="font-size: 0.70rem; text-transform: uppercase;">Compras / Notas</div>
                            <div class="fw-extrabold text-success fs-5 mt-1" id="topModalTotalCompras">0</div>
                            <small class="text-muted" style="font-size: 0.68rem;">Transacciones</small>
                        </div>
                    </div>
                </div>

                <div id="topModalMensajeVacio" class="alert alert-info py-2 px-3 small mb-3" style="display: none; border-radius: 10px;">
                    <i class="bi bi-info-circle-fill me-1"></i> Esta posición aún no tiene compras registradas en <strong>{{ ucfirst($nombreMesActual) }} {{ $anio }}</strong>.
                </div>

                <div class="d-flex gap-2">
                    <a href="#" id="topModalBtnVerCliente" class="btn btn-primary fw-bold flex-grow-1" style="border-radius: 10px;">
                        <i class="bi bi-person-lines-fill me-1"></i> Ver Ficha en Clientes
                    </a>
                    <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal" style="border-radius: 10px;">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================== --}}
{{-- 7. SCRIPTS: CHART.JS & CONTROL DEL SELECTOR DE PERÍODO   --}}
{{-- ======================================================== --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Configuración tipográfica ejecutiva
    Chart.defaults.font.family = "'Plus Jakarta Sans', 'Segoe UI', system-ui, -apple-system, sans-serif";
    Chart.defaults.color = '#64748b';
    const gridOptions = { color: '#f1f5f9', drawBorder: false };

    // ============================================================
    // 1. VENTAS MENSUALES DEL AÑO (BARRAS EN AZUL TONAL)
    // ============================================================
    const ctxMensual = document.getElementById('chartMensual');
    if (ctxMensual) {
        const ctx = ctxMensual.getContext('2d');
        const gradienteBarras = ctx.createLinearGradient(0, 0, 0, 270);
        gradienteBarras.addColorStop(0, '#0284c7');
        gradienteBarras.addColorStop(1, '#38bdf8');

        new Chart(ctxMensual, {
            type: 'bar',
            data: {
                labels: @json($mesesLabels),
                datasets: [{
                    label: 'Ventas Bs.',
                    data: @json($ventasPorMesAnio), // Reales desde MySQL
                    backgroundColor: gradienteBarras,
                    hoverBackgroundColor: '#0369a1',
                    borderRadius: 6,
                    maxBarThickness: 34
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#ffffff',
                        bodyColor: '#38bdf8',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return ' Ventas: Bs. ' + context.parsed.y.toLocaleString('es-BO', { minimumFractionDigits: 2 });
                            }
                        }
                    }
                },
                scales: { 
                    x: { 
                        grid: { display: false },
                        ticks: { font: { weight: '600' } }
                    }, 
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
    // 2. TOP PRODUCTOS (ARCOÍRIS PRESERVADO SEGÚN LO SOLICITADO)
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
                cutout: '60%',
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
                                weight: '600',
                                family: "'Plus Jakarta Sans', system-ui, sans-serif"
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
                        backgroundColor: '#0f172a',
                        titleColor: '#ffffff',
                        bodyColor: '#38bdf8',
                        padding: 10,
                        cornerRadius: 8,
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
    // 3. FLUJO DE VENTAS DIARIAS (SPLINE EN AZUL TONAL FIGMA)
    // ============================================================
    const datosRangosDiarios = @json($rangosDiarios);
    let chartDiarioInstance = null;
    const ctxDiario = document.getElementById('chartDiario');

    if (ctxDiario) {
        const datosIniciales = datosRangosDiarios['15'] || { labels: [], valores: [] };
        const ctxD = ctxDiario.getContext('2d');
        const gradienteLinea = ctxD.createLinearGradient(0, 0, 0, 320);
        gradienteLinea.addColorStop(0, 'rgba(14, 165, 233, 0.22)');
        gradienteLinea.addColorStop(1, 'rgba(14, 165, 233, 0.00)');

        chartDiarioInstance = new Chart(ctxDiario, {
            type: 'line',
            data: {
                labels: datosIniciales.labels,
                datasets: [{
                    label: 'Ventas Bs.',
                    data: datosIniciales.valores,
                    borderColor: '#0ea5e9',
                    backgroundColor: gradienteLinea,
                    fill: true,
                    tension: 0.38,
                    borderWidth: 2.8,
                    pointRadius: 3.5,
                    pointBackgroundColor: '#0284c7',
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#38bdf8'
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#ffffff',
                        bodyColor: '#38bdf8',
                        padding: 10,
                        cornerRadius: 8,
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
            tituloEl.textContent = 'Flujo de Ventas Diarias (' + tituloTexto + ')';
        }

        const labelPeriodo = document.getElementById('labelResumenPeriodo');
        if (labelPeriodo && tituloTexto) {
            labelPeriodo.textContent = tituloTexto;
        }
    };

    // Inicializar tooltips interactivos para el embudo de clientes
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// ============================================================
// MODAL DETALLE DE CLIENTE TOP EN EMBUDO
// ============================================================
window.abrirModalTopCliente = function(rank, nombre, compras, monto, tieneCliente) {
    const modalEl = document.getElementById('modalDetalleClienteTop');
    if (!modalEl) return;

    const rankBadge = document.getElementById('topModalRankBadge');
    const nombreEl = document.getElementById('topModalClienteNombre');
    const montoEl = document.getElementById('topModalTotalMonto');
    const comprasEl = document.getElementById('topModalTotalCompras');
    const metricasRow = document.getElementById('topModalMetricasRow');
    const mensajeVacio = document.getElementById('topModalMensajeVacio');
    const btnVerCliente = document.getElementById('topModalBtnVerCliente');

    if (rankBadge) rankBadge.textContent = 'Top #' + rank + ' del Mes';

    if (tieneCliente && nombre) {
        if (nombreEl) nombreEl.textContent = nombre;
        if (montoEl) montoEl.textContent = 'Bs. ' + monto;
        if (comprasEl) comprasEl.textContent = compras + ' nota(s)';
        if (metricasRow) metricasRow.style.display = '';
        if (mensajeVacio) mensajeVacio.style.display = 'none';
        if (btnVerCliente) {
            btnVerCliente.style.display = '';
            btnVerCliente.href = '/clientes?buscar=' + encodeURIComponent(nombre);
        }
    } else {
        if (nombreEl) nombreEl.textContent = 'Posición #' + rank + ' disponible';
        if (metricasRow) metricasRow.style.display = 'none';
        if (mensajeVacio) mensajeVacio.style.display = '';
        if (btnVerCliente) btnVerCliente.style.display = 'none';
    }

    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
};

// ============================================================
// FUNCIONES DEL SELECTOR DE PERÍODO Y AÑO PERSONALIZADO
// ============================================================
let anioActualPicker = {{ $anio }};

function sumarAnio(delta) {
    anioActualPicker += delta;
    const select = document.getElementById('selectAnioPicker');
    if (select) {
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