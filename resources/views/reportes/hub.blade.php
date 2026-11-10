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
.report-card.tipo-ventas   { border-left: 6px solid #1e40af; }
.report-card.tipo-cierre   { border-left: 6px solid #16a34a; }

/* Badge de categoría */
.cat-badge {
    font-size: 0.72rem; font-weight: 700;
    letter-spacing: 0.5px; border-radius: 20px;
    padding: 5px 14px; display: inline-block;
}
.badge-ventas  { background-color: #1e3a8a; color: #fff; }
.badge-cierre  { background-color: #16a34a; color: #fff; }

/* Icono grande decorativo */
.report-icon {
    width: 60px; height: 60px; border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.7rem; flex-shrink: 0;
}
.icon-ventas  { background: #eff6ff; color: #1e40af; }
.icon-cierre  { background: #f0fdf4; color: #16a34a; }

.report-title { font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 14px 0 6px; }
.report-desc  { font-size: 0.85rem; color: #64748b; line-height: 1.5; margin: 0; }

.report-footer {
    margin-top: 20px; padding-top: 14px;
    border-top: 1px dashed #e2e8f0;
    display: flex; align-items: center;
    justify-content: space-between;
    font-size: 0.8rem; font-weight: 700;
}
.report-footer-ventas { color: #1e40af; }
.report-footer-cierre { color: #16a34a; }

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
        Recaudado este mes: <span>Bs. {{ number_format($totalRecaudadoMes, 2) }}</span>
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

{{-- ===== LAS DOS TARJETAS DE REPORTE ===== --}}
<div class="row g-4">

    {{-- ========================================== --}}
    {{-- TARJETA 1: REPORTE DE VENTAS              --}}
    {{-- ========================================== --}}
    <div class="col-md-6">
        <a href="/reportes/ventas" class="report-card tipo-ventas p-4 p-lg-5">
            <div class="d-flex align-items-start gap-4">
                <div class="report-icon icon-ventas">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div class="flex-grow-1">
                    <span class="cat-badge badge-ventas">Ventas</span>
                    <div class="report-title">Reporte de Ventas</div>
                    <p class="report-desc">
                        Visualizá y exportá a Excel todas las ventas del día, semana o mes.
                        Separadas por <strong>Efectivo</strong> y <strong>QR</strong>, con el desglose
                        detallado de cada producto vendido por nota.
                    </p>

                    {{-- Mini métricas dentro de la tarjeta --}}
                    <div class="d-flex flex-wrap gap-3 mt-3">
                        <div style="background:#eff6ff; border-radius:8px; padding:8px 14px; font-size:0.8rem; font-weight:700; color:#1e40af;">
                            <i class="bi bi-calendar-day me-1"></i>
                            {{ $ventasMes }} ventas este mes
                        </div>
                        <div style="background:#f0fdf4; border-radius:8px; padding:8px 14px; font-size:0.8rem; font-weight:700; color:#16a34a;">
                            <i class="bi bi-cash me-1"></i>
                            Bs. {{ number_format($totalRecaudadoMes, 2) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="report-footer report-footer-ventas">
                <span>
                    <i class="bi bi-file-earmark-excel-fill me-1"></i>
                    Incluye exportación a Excel con todos los filtros
                </span>
                <span class="fw-bold">
                    Abrir Reporte <i class="bi bi-arrow-right ms-1"></i>
                </span>
            </div>
        </a>
    </div>

    {{-- ========================================== --}}
    {{-- TARJETA 2: REPORTE DE CIERRE DE MES       --}}
    {{-- ========================================== --}}
    <div class="col-md-6">
        <a href="/reportes/cierre-mes" class="report-card tipo-cierre p-4 p-lg-5">
            <div class="d-flex align-items-start gap-4">
                <div class="report-icon icon-cierre">
                    <i class="bi bi-calendar2-check"></i>
                </div>
                <div class="flex-grow-1">
                    <span class="cat-badge badge-cierre">Inventario</span>
                    <div class="report-title">Cierre de Mes — Cuadre de Stock</div>
                    <p class="report-desc">
                        Cuadre físico de la mercadería: compara el <strong>stock inicial</strong>
                        del mes contra las <strong>ventas realizadas</strong> para obtener el
                        <strong>total que debe haber en tienda</strong>. Marcá cada producto
                        como <strong>OK</strong> y exportá el Excel al formato de la ferretería.
                    </p>

                    {{-- Mini barra de progreso del cierre --}}
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1" style="font-size:0.78rem; font-weight:700; color:#16a34a;">
                            <span><i class="bi bi-check2-circle me-1"></i> {{ $checksMes }} de {{ $totalProductos }} productos verificados</span>
                            <span>{{ $porcentajeCierre }}%</span>
                        </div>
                        <div style="height:6px; background:#bbf7d0; border-radius:10px; overflow:hidden;">
                            <div style="height:6px; background:linear-gradient(90deg,#16a34a,#4ade80); border-radius:10px; width:{{ $porcentajeCierre }}%; transition:width 0.3s;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="report-footer report-footer-cierre">
                <span>
                    <i class="bi bi-file-earmark-excel-fill me-1"></i>
                    Exporta al formato de tu ferretería
                </span>
                <span class="fw-bold">
                    {{ $porcentajeCierre == 100 ? '✅ Cuadre completo' : 'Iniciar Cierre' }}
                    <i class="bi bi-arrow-right ms-1"></i>
                </span>
            </div>
        </a>
    </div>

</div>

@endsection
