@extends('layouts.app')

@section('content')

<style>
    /* Estilos del Catálogo de Reportes estilo SaaS */
    .report-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        transition: all 0.2s ease-in-out;
        position: relative;
        overflow: hidden;
        text-decoration: none;
        display: block;
        height: 100%;
    }
    .report-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        border-color: #cbd5e1;
    }

    /* Bordes de color a la izquierda según categoría */
    .report-card.border-blue {
        border-left: 6px solid #1e40af;
    }
    .report-card.border-cyan {
        border-left: 6px solid #0284c7;
    }
    .report-card.border-green {
        border-left: 6px solid #16a34a;
    }
    .report-card.border-teal {
        border-left: 6px solid #0d9488;
    }
    .report-card.border-amber {
        border-left: 6px solid #d97706;
    }

    /* Badges de categoría */
    .cat-badge {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        border-radius: 20px;
        padding: 5px 14px;
        display: inline-block;
    }
    .badge-blue {
        background-color: #1e3a8a;
        color: #ffffff;
    }
    .badge-cyan {
        background-color: #0284c7;
        color: #ffffff;
    }
    .badge-green {
        background-color: #16a34a;
        color: #ffffff;
    }
    .badge-teal {
        background-color: #0d9488;
        color: #ffffff;
    }
    .badge-amber {
        background-color: #d97706;
        color: #ffffff;
    }

    /* Títulos de sección */
    .section-title {
        font-size: 0.85rem;
        font-weight: 800;
        color: #1e293b;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background-color: #e2e8f0;
    }

    .report-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        margin-top: 14px;
        margin-bottom: 6px;
    }
    .report-desc {
        font-size: 0.84rem;
        color: #64748b;
        margin-bottom: 0;
        line-height: 1.4;
    }

    .report-footer {
        margin-top: 16px;
        padding-top: 12px;
        border-top: 1px dashed #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.78rem;
        font-weight: 700;
        color: #0ea5e9;
    }
</style>

{{-- ===== ENCABEZADO PRINCIPAL ===== --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div class="bg-primary bg-opacity-10 p-2 rounded me-3 text-primary">
            <i class="bi bi-file-earmark-bar-graph-fill fs-3"></i>
        </div>
        <div>
            <h3 class="fw-bold m-0 text-dark">REPORTES</h3>
            <small class="text-muted">Centro de control, análisis de ventas y cierre de inventario físico</small>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- SECCIÓN 1: TRANSACCIONES Y OPERACIONES (VENTAS Y CAJA)        --}}
{{-- ============================================================ --}}
<div class="mb-5">
    <div class="section-title">
        <i class="bi bi-cash-stack text-primary"></i> TRANSACCIONES Y OPERACIONES
    </div>
    <div class="row g-4">
        
        {{-- Tarjeta 1: Reporte de Ventas --}}
        <div class="col-md-6 col-lg-6">
            <a href="/reportes/ventas" class="report-card border-blue p-4">
                <span class="cat-badge badge-blue">Transacciones y Operaciones</span>
                <div class="report-title">Reporte de Ventas y Métodos de Pago</div>
                <p class="report-desc">
                    Cuadre de dinero en <strong>Efectivo</strong> y transferencias <strong>QR</strong> por día, semana o mes. Incluye desglose detallado de productos por nota y exportación directa a Excel.
                </p>
                <div class="report-footer">
                    <span><i class="bi bi-calendar3 me-1"></i> {{ $ventasMes }} ventas este mes · Bs. {{ number_format($totalRecaudadoMes, 2) }}</span>
                    <span>Abrir Reporte <i class="bi bi-arrow-right"></i></span>
                </div>
            </a>
        </div>

        {{-- Tarjeta 2: Historial de Arqueos de Caja --}}
        <div class="col-md-6 col-lg-6">
            <a href="/arqueos" class="report-card border-cyan p-4">
                <span class="cat-badge badge-cyan">Transacciones y Operaciones</span>
                <div class="report-title">Historial de Arqueos de Caja</div>
                <p class="report-desc">
                    Módulo de cuadre y sellado de turno. Verifica qué pagos físicos y QR ya fueron contemplados y sellados con el visto bueno del cajero.
                </p>
                <div class="report-footer text-primary">
                    <span><i class="bi bi-safe2 me-1"></i> Control de Billetes y Bancos</span>
                    <span>Abrir Módulo <i class="bi bi-arrow-right"></i></span>
                </div>
            </a>
        </div>

    </div>
</div>

{{-- ============================================================ --}}
{{-- SECCIÓN 2: PROPIEDADES E INVENTARIO (CIERRE DE MES Y STOCK)   --}}
{{-- ============================================================ --}}
<div class="mb-5">
    <div class="section-title">
        <i class="bi bi-box-seam text-success"></i> PROPIEDADES E INVENTARIO
    </div>
    <div class="row g-4">

        {{-- Tarjeta 3: Reporte de Cierre de Mes (El que pidió con el Excel y checks) --}}
        <div class="col-md-6 col-lg-6">
            <a href="/reportes/cierre-mes" class="report-card border-green p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="cat-badge badge-green">Propiedades e Inventario</span>
                    @if($porcentajeCierre == 100)
                        <span class="badge bg-success-subtle text-success fw-bold px-2 py-1" style="font-size:0.75rem;">
                            <i class="bi bi-check-all"></i> Cuadre 100%
                        </span>
                    @elseif($checksMes > 0)
                        <span class="badge bg-warning-subtle text-warning fw-bold px-2 py-1" style="font-size:0.75rem;">
                            {{ $porcentajeCierre }}% Verificado
                        </span>
                    @endif
                </div>
                <div class="report-title">Reporte de Cierre de Mes (Cuadre de Stock)</div>
                <p class="report-desc">
                    Control físico de mercadería: <strong>CANTIDAD</strong> (stock anterior) menos <strong>VENTA</strong> (del mes) igual a <strong>TOTAL EN TIENDA</strong>. Vista interactiva con casillas de verificación <strong>OK</strong> y exportación a Excel idéntico al formato ferretero.
                </p>
                <div class="report-footer text-success">
                    <span><i class="bi bi-check2-circle me-1"></i> {{ $checksMes }} de {{ $totalProductos }} productos verificados</span>
                    <span>Iniciar Cierre <i class="bi bi-arrow-right"></i></span>
                </div>
            </a>
        </div>

        {{-- Tarjeta 4: Kardex / Movimientos de Almacén --}}
        <div class="col-md-6 col-lg-6">
            <a href="/inventario" class="report-card border-teal p-4">
                <span class="cat-badge badge-teal">Propiedades e Inventario</span>
                <div class="report-title">Kardex y Movimientos de Inventario</div>
                <p class="report-desc">
                    Bitácora detallada de entradas, salidas por ventas, devoluciones por anulación y ajustes manuales con motivo, fecha y usuario responsable.
                </p>
                <div class="report-footer text-teal" style="color: #0d9488;">
                    <span><i class="bi bi-arrow-left-right me-1"></i> Auditoría de entradas y salidas</span>
                    <span>Ver Inventario <i class="bi bi-arrow-right"></i></span>
                </div>
            </a>
        </div>

    </div>
</div>

{{-- ============================================================ --}}
{{-- SECCIÓN 3: ADMINISTRACIÓN Y USUARIOS                          --}}
{{-- ============================================================ --}}
<div class="mb-4">
    <div class="section-title">
        <i class="bi bi-people text-warning"></i> ADMINISTRACIÓN
    </div>
    <div class="row g-4">

        {{-- Tarjeta 5: Usuarios y Permisos --}}
        <div class="col-md-6 col-lg-6">
            <a href="/usuarios" class="report-card border-amber p-4">
                <span class="cat-badge badge-amber">Administración</span>
                <div class="report-title">Reporte de Usuarios y Cajeros</div>
                <p class="report-desc">
                    Listado del personal autorizado para ventas y administración, roles en el sistema y control de acceso por cajero.
                </p>
                <div class="report-footer text-warning" style="color: #d97706 !important;">
                    <span><i class="bi bi-shield-lock me-1"></i> Personal y privilegios</span>
                    <span>Ver Usuarios <i class="bi bi-arrow-right"></i></span>
                </div>
            </a>
        </div>

    </div>
</div>

@endsection
