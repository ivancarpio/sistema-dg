@extends('layouts.app')

@section('content')

<style>
    .table-custom-header th {
        background-color: #f1f5f9 !important;
        font-size: 0.75rem; font-weight: 800; color: #64748b;
        text-transform: uppercase; letter-spacing: 0.5px;
        padding: 13px 10px; border-bottom: none !important;
    }
    .table-custom-header th:first-child  { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
    .table-custom-header th:last-child   { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
    .table-custom tbody tr { border-bottom: 1px solid #f0f2f5; transition: background-color 0.15s; }
    .table-custom td { vertical-align: middle; padding: 11px 10px; }
    .card { border: none !important; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important; border-radius: .5rem !important; }

    .badge-efectivo { background-color: #dcfce7; color: #16a34a; font-weight: 700; border-radius: 20px; padding: 4px 12px; }
    .badge-qr       { background-color: #dbeafe; color: #2563eb; font-weight: 700; border-radius: 20px; padding: 4px 12px; }
    .badge-verificado { background-color: #fefce8; color: #ca8a04; font-weight: 700; border-radius: 20px; padding: 3px 10px; font-size: 0.75rem; }

    /* Tarjetas de totales */
    .total-card { border-radius: 14px; border: none; padding: 20px 24px; display: flex; align-items: center; gap: 16px; }
    .total-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
    .total-label { font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
    .total-value { font-size: 1.5rem; font-weight: 800; }

    /* Desglose de productos por venta */
    .detalle-productos { background: #f8faff; border-left: 3px solid #0ea5e9; border-radius: 0 8px 8px 0; }
    .detalle-prod-row { display: flex; align-items: center; gap: 10px; padding: 7px 0; border-bottom: 1px dashed #e2e8f0; font-size: 0.84rem; }
    .detalle-prod-row:last-child { border-bottom: none; }

    /* Botón expandir */
    .btn-expandir { background: none; border: none; cursor: pointer; color: #94a3b8; transition: all 0.2s; padding: 0 4px; }
    .btn-expandir:hover, .btn-expandir.abierto { color: #0ea5e9; }

    /* Resumen de productos consolidado */
    .consolidado-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 6px; transition: background 0.15s; }
    .consolidado-item:hover { background: #f8faff; }

    @media print {
        .no-print { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #cbd5e1 !important; margin-bottom: 12px !important; }
        .total-card { border: 1px solid #cbd5e1 !important; padding: 10px 14px !important; }
        .fila-detalle { display: table-row !important; }
        .detalle-productos { background: #f8fafc !important; border-left: 2px solid #0284c7 !important; padding: 8px !important; }
        body { font-size: 11pt !important; color: #000 !important; }
    }
</style>

{{-- ENCABEZADO EXCLUSIVO PARA IMPRESIÓN (OCULTO EN PANTALLA) --}}
<div class="d-none d-print-block mb-3 border-bottom pb-2">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold m-0 text-dark">DISTRIBUIDORA GUADALUPE</h4>
            <div class="small text-muted">Comprobante Oficial de Arqueo y Cierre de Caja</div>
        </div>
        <div class="text-end">
            <h5 class="fw-bold text-dark m-0">ARQUEO #{{ $arqueo->id }}</h5>
            <small class="text-muted">Fecha: {{ $arqueo->created_at->format('d/m/Y H:i') }}</small>
        </div>
    </div>
</div>

{{-- Alertas --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm no-print">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ===== ENCABEZADO DE PANTALLA ===== --}}
<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <div class="d-flex align-items-center">
        <div class="bg-primary bg-opacity-10 p-2 rounded me-3 text-primary">
            <i class="bi bi-safe2-fill fs-3"></i>
        </div>
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:0.8rem;">
                    <li class="breadcrumb-item"><a href="/arqueos" class="text-muted text-decoration-none">Arqueos de caja</a></li>
                    <li class="breadcrumb-item active">Arqueo #{{ $arqueo->id }}</li>
                </ol>
            </nav>
            <h3 class="fw-bold m-0 text-dark">Arqueo de Caja <span class="text-primary">#{{ $arqueo->id }}</span></h3>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="/arqueos/{{ $arqueo->id }}/excel" class="btn btn-success fw-bold shadow-sm">
            <i class="bi bi-file-earmark-excel-fill me-1"></i> Exportar a Excel
        </a>
        <button onclick="window.print()" class="btn btn-primary fw-bold shadow-sm">
            <i class="bi bi-printer-fill me-1"></i> Imprimir Arqueo
        </button>
        <a href="/arqueos" class="btn btn-outline-secondary fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>

{{-- ===== META-DATOS DEL ARQUEO ===== --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-2 rounded">
                        <i class="bi bi-person-circle text-primary fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:0.75rem; font-weight:700; text-transform:uppercase;">Creado por</div>
                        <div class="fw-bold text-dark">{{ optional($arqueo->usuario)->name ?? 'Usuario' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-info bg-opacity-10 p-2 rounded">
                        <i class="bi bi-calendar-check text-info fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:0.75rem; font-weight:700; text-transform:uppercase;">Fecha del arqueo</div>
                        <div class="fw-bold text-dark">
                            {{ $arqueo->created_at->format('d/m/Y') }}
                            <small class="text-muted">{{ $arqueo->created_at->format('H:i') }} hs</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 p-2 rounded">
                        <i class="bi bi-receipt text-success fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:0.75rem; font-weight:700; text-transform:uppercase;">Ventas incluidas</div>
                        <div class="fw-bold text-dark">{{ $arqueo->cantidad_ventas }} transacciones</div>
                    </div>
                </div>
            </div>
            @if($arqueo->observaciones)
            <div class="col-12">
                <div class="bg-light rounded p-3">
                    <div class="text-muted small fw-bold mb-1"><i class="bi bi-chat-text me-1"></i>Observaciones</div>
                    <div>{{ $arqueo->observaciones }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ===== TARJETAS DE TOTALES ===== --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="total-card card" style="background: linear-gradient(135deg, #f0fdf4, #dcfce7);">
            <div class="total-icon" style="background: rgba(22,163,74,0.15);">
                <i class="bi bi-cash-stack text-success"></i>
            </div>
            <div>
                <div class="total-label">Efectivo cobrado</div>
                <div class="total-value text-success">Bs. {{ number_format($arqueo->total_efectivo, 2) }}</div>
                <small class="text-muted">{{ $arqueo->ventas->where('metodo_pago', 'Efectivo')->count() }} ventas · contar los billetes</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="total-card card" style="background: linear-gradient(135deg, #eff6ff, #dbeafe);">
            <div class="total-icon" style="background: rgba(37,99,235,0.1);">
                <i class="bi bi-qr-code-scan text-primary"></i>
            </div>
            <div>
                <div class="total-label">QR / Transferencia</div>
                <div class="total-value text-primary">Bs. {{ number_format($arqueo->total_qr, 2) }}</div>
                <small class="text-muted">{{ $arqueo->ventas->where('metodo_pago', 'QR')->count() }} ventas · verificar en banco</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="total-card card" style="background: linear-gradient(135deg, #1e40af, #0ea5e9);">
            <div class="total-icon" style="background: rgba(255,255,255,0.2);">
                <i class="bi bi-wallet2 text-white"></i>
            </div>
            <div>
                <div class="total-label text-white opacity-75">Total General</div>
                <div class="total-value text-white">Bs. {{ number_format($arqueo->total_general, 2) }}</div>
                <small class="text-white opacity-75">{{ $arqueo->cantidad_ventas }} ventas totales</small>
            </div>
        </div>
    </div>
</div>

{{-- ===== TABS: Ventas + Consolidado de Productos ===== --}}
<ul class="nav nav-tabs border-0 mb-0 no-print" id="arqueoTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active fw-bold" id="tab-ventas" data-bs-toggle="tab" data-bs-target="#panel-ventas" type="button">
            <i class="bi bi-receipt me-2"></i>Ventas del Arqueo
            <span class="badge bg-primary ms-1">{{ $arqueo->cantidad_ventas }}</span>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-bold" id="tab-productos" data-bs-toggle="tab" data-bs-target="#panel-productos" type="button">
            <i class="bi bi-box-seam me-2"></i>Productos Vendidos
            @php
                $totalArticulos = $arqueo->ventas->flatMap->detalles->sum('cantidad');
                $totalProductosDistintos = $arqueo->ventas->flatMap->detalles->groupBy('id_producto')->count();
            @endphp
            <span class="badge bg-secondary ms-1">{{ $totalProductosDistintos }}</span>
        </button>
    </li>
</ul>

<div class="tab-content">

    {{-- ===== TAB 1: TABLA DE VENTAS CON DESGLOSE ===== --}}
    <div class="tab-pane fade show active" id="panel-ventas">
        <div class="card" style="border-top-left-radius: 0 !important;">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center no-print">
                <h6 class="fw-bold m-0">
                    <i class="bi bi-list-check me-2 text-primary"></i>Ventas incluidas — clic en <i class="bi bi-chevron-right"></i> para ver productos
                </h6>
                <div class="d-flex gap-2">
                    <button onclick="expandirTodas()" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-chevron-expand me-1"></i>Expandir todas
                    </button>
                    <button onclick="contraerTodas()" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-chevron-contract me-1"></i>Contraer
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
                                <th>Cliente</th>
                                <th>Monto</th>
                                <th>Método</th>
                                <th>Estado</th>
                                <th>Cajero</th>
                                <th class="pe-4">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($arqueo->ventas as $venta)

                            {{-- FILA PRINCIPAL --}}
                            <tr>
                                <td class="ps-3">
                                    @if($venta->detalles->count() > 0)
                                    <button class="btn-expandir" onclick="toggleDetalle({{ $venta->id }}, this)" title="Ver productos">
                                        <i class="bi bi-chevron-right fs-5"></i>
                                    </button>
                                    @endif
                                </td>
                                <td class="fw-bold">
                                    {{ $venta->numero_nota ?? 'SYS-'.str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-person me-1"></i>{{ $venta->cliente ?? 'Sin nombre' }}
                                    </span>
                                </td>
                                <td class="fw-bold text-success">Bs. {{ number_format($venta->total, 2) }}</td>
                                <td>
                                    @if(strtolower($venta->metodo_pago) == 'efectivo')
                                        <span class="badge-efectivo"><i class="bi bi-cash me-1"></i>Efectivo</span>
                                    @elseif(strtolower($venta->metodo_pago) == 'qr')
                                        <span class="badge-qr"><i class="bi bi-qr-code me-1"></i>QR</span>
                                    @else
                                        <span class="badge bg-warning text-dark border"><i class="bi bi-clock-history me-1"></i>Por Pagar</span>
                                        @if(($venta->saldo_pendiente ?? 0) > 0)
                                            <small class="text-danger fw-bold d-block" style="font-size:0.75rem;">Debe: Bs. {{ number_format($venta->saldo_pendiente, 2) }}</small>
                                        @endif
                                    @endif
                                </td>
                                <td><span class="badge-verificado"><i class="bi bi-patch-check-fill me-1"></i>Verificada</span></td>
                                <td><small class="text-muted fw-bold"><i class="bi bi-person-circle me-1"></i>{{ optional($venta->usuario)->name ?? 'Cajero' }}</small></td>
                                <td class="pe-4"><small class="text-muted">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</small></td>
                            </tr>

                            {{-- FILA EXPANDIBLE: DESGLOSE DE PRODUCTOS --}}
                            @if($venta->detalles->count() > 0)
                            <tr class="fila-detalle" id="detalle-{{ $venta->id }}" style="display:none;">
                                <td colspan="8" class="pb-3 pt-0 px-4">
                                    <div class="detalle-productos p-3 ms-3">
                                        <div class="text-muted fw-bold mb-2" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:1px;">
                                            <i class="bi bi-box-seam me-1 text-info"></i>
                                            Productos vendidos en esta nota — {{ $venta->detalles->count() }} ítem(s)
                                        </div>
                                        {{-- Encabezado --}}
                                        <div class="detalle-prod-row" style="background:#eff6ff; border-radius:6px; padding: 6px 10px !important; font-weight:800; font-size:0.72rem; color:#1e40af; text-transform:uppercase; letter-spacing:0.5px; border:none !important;">
                                            <span style="flex:3;">Producto</span>
                                            <span style="flex:1; text-align:center;">Cantidad</span>
                                            <span style="flex:1.5; text-align:right;">Precio Unit.</span>
                                            <span style="flex:1.5; text-align:right;">Subtotal</span>
                                        </div>
                                        @foreach($venta->detalles as $detalle)
                                        <div class="detalle-prod-row">
                                            <span style="flex:3; font-weight:600; color:#1e293b;">
                                                <i class="bi bi-tools me-2 text-muted" style="font-size:0.8rem;"></i>
                                                {{ optional($detalle->producto)->nombre ?? 'Producto eliminado' }}
                                            </span>
                                            <span style="flex:1; text-align:center;">
                                                <span class="badge bg-light text-dark border">
                                                    {{ number_format($detalle->cantidad, 0) }}
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
                                        <div style="display:flex; justify-content:flex-end; margin-top:8px; padding-top:8px; border-top:2px solid #bfdbfe;">
                                            <span style="font-weight:800; color:#1e40af; font-size:0.88rem;">
                                                Total nota: Bs. {{ number_format($venta->total, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endif

                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Sin ventas registradas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        {{-- Fila totales --}}
                        <tfoot>
                            <tr style="background: #f8fafc; border-top: 2px solid #e2e8f0;">
                                <td colspan="3" class="ps-4 fw-bold text-uppercase text-muted" style="font-size:0.78rem;">Totales</td>
                                <td class="fw-bold text-dark fs-6">Bs. {{ number_format($arqueo->total_general, 2) }}</td>
                                <td colspan="4" class="pe-4">
                                    <span class="text-success fw-bold me-3">
                                        <i class="bi bi-cash me-1"></i>Bs. {{ number_format($arqueo->total_efectivo, 2) }}
                                    </span>
                                    <span class="text-primary fw-bold">
                                        <i class="bi bi-qr-code me-1"></i>Bs. {{ number_format($arqueo->total_qr, 2) }}
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TAB 2: CONSOLIDADO DE PRODUCTOS ===== --}}
    <div class="tab-pane fade" id="panel-productos">
        <div class="card" style="border-top-left-radius: 0 !important;">
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 text-center" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                            <div class="text-muted small fw-bold text-uppercase mb-1">Artículos totales</div>
                            <div class="fw-bold text-success" style="font-size:1.8rem;">{{ number_format($totalArticulos, 0) }}</div>
                            <div class="text-muted small">unidades despachadas</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 text-center" style="background:#eff6ff; border:1px solid #bfdbfe;">
                            <div class="text-muted small fw-bold text-uppercase mb-1">Productos distintos</div>
                            <div class="fw-bold text-primary" style="font-size:1.8rem;">{{ $totalProductosDistintos }}</div>
                            <div class="text-muted small">referencias diferentes</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 text-center" style="background:#faf5ff; border:1px solid #ddd6fe;">
                            <div class="text-muted small fw-bold text-uppercase mb-1">Promedio por venta</div>
                            @php $promedio = $arqueo->cantidad_ventas > 0 ? $totalArticulos / $arqueo->cantidad_ventas : 0; @endphp
                            <div class="fw-bold text-purple" style="font-size:1.8rem; color:#7c3aed;">{{ number_format($promedio, 1) }}</div>
                            <div class="text-muted small">artículos por nota</div>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-dark mb-3">
                    <i class="bi bi-bar-chart-steps me-2 text-primary"></i>
                    Consolidado de productos vendidos en este arqueo
                </h6>

                {{-- Agrupar todos los detalles de todas las ventas del arqueo --}}
                @php
                    $consolidado = $arqueo->ventas
                        ->flatMap->detalles
                        ->groupBy('id_producto')
                        ->map(function($detalles) {
                            return [
                                'nombre'   => optional($detalles->first()->producto)->nombre ?? 'Producto eliminado',
                                'cantidad' => $detalles->sum('cantidad'),
                                'subtotal' => $detalles->sum('subtotal'),
                                'precio'   => $detalles->first()->precio_unitario,
                                'ventas'   => $detalles->count(),
                            ];
                        })
                        ->sortByDesc('subtotal');

                    $maxSubtotal = $consolidado->max('subtotal') ?: 1;
                @endphp

                @forelse($consolidado as $producto)
                <div class="consolidado-item">
                    <div style="flex:3; display:flex; align-items:center; gap:12px;">
                        <div style="width:38px; height:38px; background:#eff6ff; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="bi bi-tools text-primary" style="font-size:1rem;"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size:0.9rem;">{{ $producto['nombre'] }}</div>
                            <div class="text-muted" style="font-size:0.75rem;">
                                Bs. {{ number_format($producto['precio'], 2) }} c/u
                                · {{ $producto['ventas'] }} venta(s)
                            </div>
                            {{-- Barra de proporción --}}
                            <div style="height:4px; background:#e2e8f0; border-radius:4px; margin-top:5px; width:160px;">
                                <div style="height:4px; background:#0ea5e9; border-radius:4px; width:{{ ($producto['subtotal'] / $maxSubtotal) * 100 }}%;"></div>
                            </div>
                        </div>
                    </div>
                    <div style="flex:1; text-align:center;">
                        <span class="badge bg-light text-dark border fw-bold px-3 py-2" style="font-size:0.88rem;">
                            {{ number_format($producto['cantidad'], 0) }} u.
                        </span>
                    </div>
                    <div style="flex:1.5; text-align:right; font-weight:800; color:#16a34a; font-size:1rem;">
                        Bs. {{ number_format($producto['subtotal'], 2) }}
                    </div>
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-box-seam fs-1 d-block mb-2 opacity-25"></i>
                    <p class="fw-bold mb-1">No hay detalle de productos disponible</p>
                    <small>Los detalles de productos de este arqueo no están registrados.</small>
                </div>
                @endforelse

                {{-- Total consolidado --}}
                @if($consolidado->count() > 0)
                <div class="mt-3 p-3 rounded-3 d-flex justify-content-between align-items-center"
                     style="background: linear-gradient(135deg, #1e40af, #0ea5e9);">
                    <span class="text-white fw-bold">
                        <i class="bi bi-calculator me-2"></i>
                        Total consolidado — {{ $totalProductosDistintos }} producto(s) distintos
                    </span>
                    <span class="text-white fw-bold fs-5">
                        Bs. {{ number_format($arqueo->total_general, 2) }}
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>{{-- fin tab-content --}}

{{-- SECCIÓN DE FIRMAS EXCLUSIVA PARA IMPRESIÓN --}}
<div class="d-none d-print-block mt-5 pt-4">
    <div class="row text-center">
        <div class="col-6">
            <div style="border-top: 1px solid #334155; width: 75%; margin: 0 auto; padding-top: 6px;">
                <div class="fw-bold text-dark">{{ optional($arqueo->usuario)->name ?? 'Cajero Responsable' }}</div>
                <small class="text-muted">Firma y Aclaración (Cajero)</small>
            </div>
        </div>
        <div class="col-6">
            <div style="border-top: 1px solid #334155; width: 75%; margin: 0 auto; padding-top: 6px;">
                <div class="fw-bold text-dark">Administración / Supervisor</div>
                <small class="text-muted">Firma y Sello Recibido Conforme</small>
            </div>
        </div>
    </div>
</div>

<script>
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
function expandirTodas() {
    document.querySelectorAll('.fila-detalle').forEach(f => f.style.display = '');
    document.querySelectorAll('.btn-expandir i').forEach(i => i.className = 'bi bi-chevron-down fs-5');
    document.querySelectorAll('.btn-expandir').forEach(b => b.classList.add('abierto'));
}
function contraerTodas() {
    document.querySelectorAll('.fila-detalle').forEach(f => f.style.display = 'none');
    document.querySelectorAll('.btn-expandir i').forEach(i => i.className = 'bi bi-chevron-right fs-5');
    document.querySelectorAll('.btn-expandir').forEach(b => b.classList.remove('abierto'));
}
</script>

@endsection
