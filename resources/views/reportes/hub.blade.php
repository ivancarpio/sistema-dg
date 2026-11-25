@extends('layouts.app')

@section('content')

<style>
/* ===== HUB DE REPORTES — Solo 2 reportes, diseño limpio ===== */
.report-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: all 0.2s ease;
    text-decoration: none;
    display: block;
    height: 100%;
    position: relative;
    overflow: hidden;
}
.report-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.09);
    border-color: #cbd5e1;
    text-decoration: none;
}

/* Borde izquierdo de color por tipo */
.report-card.tipo-ventas    { border-left: 6px solid #1e40af; }
.report-card.tipo-finanzas  { border-left: 6px solid #7c3aed; }
.report-card.tipo-cierre    { border-left: 6px solid #16a34a; }
.report-card.tipo-productos { border-left: 6px solid #0284c7; }

/* Badge de categoría */
.cat-badge {
    font-size: 0.72rem; font-weight: 700;
    letter-spacing: 0.5px; border-radius: 20px;
    padding: 5px 14px; display: inline-block;
}
.badge-ventas    { background-color: #1e3a8a; color: #fff; }
.badge-finanzas  { background-color: #6d28d9; color: #fff; }
.badge-cierre    { background-color: #16a34a; color: #fff; }
.badge-productos { background-color: #0284c7; color: #fff; }

/* Icono grande decorativo */
.report-icon {
    width: 60px; height: 60px; border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.7rem; flex-shrink: 0;
}
.icon-ventas    { background: #eff6ff; color: #1e40af; }
.icon-finanzas  { background: #f5f3ff; color: #7c3aed; }
.icon-cierre    { background: #f0fdf4; color: #16a34a; }
.icon-productos { background: #f0f9ff; color: #0284c7; }

.report-title { font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 14px 0 6px; }
.report-desc  { font-size: 0.85rem; color: #64748b; line-height: 1.5; margin: 0; }

.report-footer {
    margin-top: 20px; padding-top: 14px;
    border-top: 1px dashed #e2e8f0;
    display: flex; align-items: center;
    justify-content: space-between;
    font-size: 0.8rem; font-weight: 700;
}
.report-footer-ventas    { color: #1e40af; }
.report-footer-finanzas  { color: #7c3aed; }
.report-footer-cierre    { color: #16a34a; }
.report-footer-productos { color: #0284c7; }

/* Stats rápidas arriba */
.stat-pill {
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 10px; padding: 10px 18px;
    font-size: 0.82rem; font-weight: 600; color: #475569;
    display: flex; align-items: center; gap: 8px;
}
.stat-pill span { font-weight: 800; color: #0f172a; font-size: 1rem; }
</style>

{{-- ===== ENCABEZADO ===== --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="bg-primary bg-opacity-10 p-2 rounded text-primary">
            <i class="bi bi-file-earmark-bar-graph-fill fs-3"></i>
        </div>
        <div>
            <h3 class="fw-bold m-0 text-dark">Reportes</h3>
            <small class="text-muted">Selecciona el tipo de reporte que necesitás generar</small>
        </div>
    </div>
</div>

{{-- ===== STATS RÁPIDAS ===== --}}
<div class="d-flex flex-wrap gap-3 mb-5">
    <div class="stat-pill">
        <i class="bi bi-calendar3 text-primary"></i>
        Ventas este mes: <span>{{ $ventasMes }}</span>
    </div>
    <div class="stat-pill">
        <i class="bi bi-cash-stack text-success"></i>
        Recaudado este mes: <span>{{ $monedaSimbolo }} {{ number_format($totalRecaudadoMes, 2) }}</span>
    </div>
    <div class="stat-pill">
        <i class="bi bi-box-seam text-warning"></i>
        Productos en catálogo: <span>{{ $totalProductos }}</span>
    </div>
    @if($porcentajeCierre > 0)
    <div class="stat-pill">
        <i class="bi bi-check2-circle text-success"></i>
        Cierre del mes: <span>{{ $porcentajeCierre }}% verificado</span>
    </div>
    @endif
</div>

{{-- ===== LAS CUATRO TARJETAS DE REPORTE ===== --}}
<div class="row g-4">

    {{-- ========================================== --}}
    {{-- TARJETA 1: REPORTE DE VENTAS              --}}
    {{-- ========================================== --}}
    <div class="col-lg-6 col-md-6">
        <a href="/reportes/ventas" class="report-card tipo-ventas p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-start gap-3">
                    <div class="report-icon icon-ventas">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div class="flex-grow-1">
                        <span class="cat-badge badge-ventas">Ventas</span>
                        <div class="report-title">Reporte de Ventas</div>
                    </div>
                </div>
                <p class="report-desc mt-3">
                    Visualizá y exportá a Excel todas las ventas por rango de fechas.
                    Separadas por <strong>Efectivo</strong> y <strong>QR</strong>, con el desglose
                    detallado por nota y cliente.
                </p>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <div style="background:#eff6ff; border-radius:8px; padding:6px 12px; font-size:0.75rem; font-weight:700; color:#1e40af;">
                        <i class="bi bi-calendar-day me-1"></i> {{ $ventasMes }} ventas en el mes
                    </div>
                    <div style="background:#f0fdf4; border-radius:8px; padding:6px 12px; font-size:0.75rem; font-weight:700; color:#16a34a;">
                        <i class="bi bi-cash me-1"></i> {{ $monedaSimbolo }} {{ number_format($totalRecaudadoMes, 2) }}
                    </div>
                </div>
            </div>
            <div class="report-footer report-footer-ventas">
                <span><i class="bi bi-file-earmark-excel-fill me-1"></i> Excel disponible</span>
                <span class="fw-bold">Abrir <i class="bi bi-arrow-right ms-1"></i></span>
            </div>
        </a>
    </div>

    {{-- ========================================== --}}
    {{-- TARJETA 2: REPORTE DE CAJA Y FINANZAS     --}}
    {{-- ========================================== --}}
    <div class="col-lg-6 col-md-6">
        <a href="/reportes/finanzas" class="report-card tipo-finanzas p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-start gap-3">
                    <div class="report-icon icon-finanzas">
                        <i class="bi bi-pie-chart-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <span class="cat-badge badge-finanzas">Caja y Finanzas</span>
                        <div class="report-title">Reporte de Caja y Finanzas</div>
                    </div>
                </div>
                <p class="report-desc mt-3">
                    Estado de resultados real, <strong>gráfico de torta</strong> con el top de gastos por categoría,
                    y control de cuánto efectivo se saca de la gaveta para <strong>depósitos al banco</strong>.
                </p>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <div style="background:#fef2f2; border-radius:8px; padding:6px 12px; font-size:0.75rem; font-weight:700; color:#dc2626;">
                        <i class="bi bi-receipt me-1"></i> Gastos mes: {{ $monedaSimbolo }} {{ number_format($gastosMes ?? 0, 2) }}
                    </div>
                    <div style="background:#f5f3ff; border-radius:8px; padding:6px 12px; font-size:0.75rem; font-weight:700; color:#7c3aed;">
                        <i class="bi bi-bank me-1"></i> Retiros banco: {{ $monedaSimbolo }} {{ number_format($retirosBancoMes ?? 0, 2) }}
                    </div>
                </div>
            </div>
            <div class="report-footer report-footer-finanzas">
                <span><i class="bi bi-file-earmark-excel-fill me-1"></i> Gráficos + Excel</span>
                <span class="fw-bold">Ver Análisis <i class="bi bi-arrow-right ms-1"></i></span>
            </div>
        </a>
    </div>

    {{-- ========================================== --}}
    {{-- TARJETA 3: REPORTE GENERAL DE PRODUCTOS   --}}
    {{-- ========================================== --}}
    <div class="col-lg-6 col-md-6">
        <a href="/reportes/productos" class="report-card tipo-productos p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-start gap-3">
                    <div class="report-icon icon-productos">
                        <i class="bi bi-boxes"></i>
                    </div>
                    <div class="flex-grow-1">
                        <span class="cat-badge badge-productos">Catálogo</span>
                        <div class="report-title">Productos e Inventario</div>
                    </div>
                </div>
                <p class="report-desc mt-3">
                    Auditoría completa del catálogo: códigos, marcas, unidades, existencias físicas,
                    precios de <strong>Venta</strong>, <strong>Costo</strong> y <strong>Cliente Regular</strong>,
                    con exportación completa a Excel.
                </p>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <div style="background:#f0f9ff; border-radius:8px; padding:6px 12px; font-size:0.75rem; font-weight:700; color:#0284c7;">
                        <i class="bi bi-box-seam me-1"></i> {{ $totalProductos }} productos
                    </div>
                    <div style="background:#f8fafc; border-radius:8px; padding:6px 12px; font-size:0.75rem; font-weight:700; color:#475569;">
                        <i class="bi bi-tag me-1"></i> Todos los precios
                    </div>
                </div>
            </div>
            <div class="report-footer report-footer-productos">
                <span><i class="bi bi-file-earmark-excel-fill me-1"></i> Exportar a Excel</span>
                <span class="fw-bold">Ver Catálogo <i class="bi bi-arrow-right ms-1"></i></span>
            </div>
        </a>
    </div>

    {{-- ========================================== --}}
    {{-- TARJETA 4: REPORTE DE CIERRE DE MES       --}}
    {{-- ========================================== --}}
    <div class="col-lg-6 col-md-6">
        <a href="/reportes/cierre-mes" class="report-card tipo-cierre p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-start gap-3">
                    <div class="report-icon icon-cierre">
                        <i class="bi bi-calendar2-check"></i>
                    </div>
                    <div class="flex-grow-1">
                        <span class="cat-badge badge-cierre">Auditoría</span>
                        <div class="report-title">Cierre de Mes — Cuadre</div>
                    </div>
                </div>
                <p class="report-desc mt-3">
                    Compara <strong>stock inicial</strong> contra <strong>ventas del mes</strong>
                    para auditar el <strong>total en tienda</strong> físico. Verificación casilla por casilla y Excel formal.
                </p>
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-1" style="font-size:0.75rem; font-weight:700; color:#16a34a;">
                        <span><i class="bi bi-check2-circle me-1"></i> {{ $checksMes }} de {{ $totalProductos }} listos</span>
                        <span>{{ $porcentajeCierre }}%</span>
                    </div>
                    <div style="height:6px; background:#bbf7d0; border-radius:10px; overflow:hidden;">
                        <div style="height:6px; background:linear-gradient(90deg,#16a34a,#4ade80); border-radius:10px; width:{{ $porcentajeCierre }}%;"></div>
                    </div>
                </div>
            </div>
            <div class="report-footer report-footer-cierre">
                <span><i class="bi bi-file-earmark-excel-fill me-1"></i> Formato ferretero</span>
                <span class="fw-bold">{{ $porcentajeCierre == 100 ? '✅ Completo' : 'Iniciar' }} <i class="bi bi-arrow-right ms-1"></i></span>
            </div>
        </a>
    </div>

</div>

@endsection
