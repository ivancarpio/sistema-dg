@extends('layouts.app')

@section('content')

<style>
    /* ---- Estilos propios del módulo Arqueo (misma línea visual que el sistema) ---- */
    .table-custom-header th {
        background-color: #f1f5f9 !important;
        font-size: 0.75rem; font-weight: 800; color: #64748b;
        text-transform: uppercase; letter-spacing: 0.5px;
        padding: 13px 10px; border-bottom: none !important;
    }
    .table-custom-header th:first-child  { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
    .table-custom-header th:last-child   { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
    .table-custom tbody tr { border-bottom: 1px solid #f0f2f5; transition: background-color 0.2s; }
    .table-custom tbody tr:hover { background-color: #f8f9fa; }
    .table-custom td { vertical-align: middle; padding: 12px 10px; }
    .card { border: none !important; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important; border-radius: .5rem !important; }

    /* Stat cards del panel de control */
    .stat-card { border-radius: 12px; border: none; transition: transform 0.15s; }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-label { font-size: 0.72rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
    .stat-value { font-size: 1.75rem; font-weight: 800; color: #1e293b; }
    .stat-value.text-money { color: #0ea5e9; }

    /* Panel de control header */
    .panel-header {
        background: linear-gradient(135deg, #1e40af 0%, #0ea5e9 100%);
        border-radius: 10px 10px 0 0;
        padding: 12px 20px;
    }

    /* Avatar de usuario */
    .user-avatar {
        width: 32px; height: 32px;
        border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 0.75rem; font-weight: 800; color: #fff;
    }
</style>

{{-- Alertas de sesión --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ===== ENCABEZADO ===== --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div class="bg-primary bg-opacity-10 p-2 rounded me-3 text-primary">
            <i class="bi bi-safe2-fill fs-3"></i>
        </div>
        <div>
            <h3 class="fw-bold m-0 text-dark">Arqueo de Caja</h3>
            <small class="text-muted">Cuadre de efectivo y pagos QR de la ferretería</small>
        </div>
    </div>
    <a href="/arqueos/crear" class="btn btn-primary fw-bold shadow-sm px-4">
        <i class="bi bi-plus-circle-fill me-2"></i>Crear nuevo arqueo
    </a>
</div>

{{-- ===== PANEL DE CONTROL ===== --}}
<div class="card mb-4">
    <div class="panel-header d-flex justify-content-between align-items-center">
        <span class="fw-bold text-white fs-6"><i class="bi bi-bar-chart-fill me-2"></i>Panel de Control</span>
        <i class="bi bi-chevron-up text-white opacity-75"></i>
    </div>
    <div class="card-body py-3">
        <div class="row g-3">
            {{-- Arqueos este mes --}}
            <div class="col-6 col-md-3">
                <div class="stat-card card bg-white p-3 h-100">
                    <div class="stat-label mb-1"><i class="bi bi-calendar3 me-1"></i>Arqueos este mes</div>
                    <div class="stat-value">{{ $arqueosEsteMes }}</div>
                </div>
            </div>
            {{-- Total arqueado este mes --}}
            <div class="col-6 col-md-3">
                <div class="stat-card card bg-white p-3 h-100">
                    <div class="stat-label mb-1"><i class="bi bi-cash-stack me-1"></i>Total arqueado este mes</div>
                    <div class="stat-value text-money">Bs. {{ number_format($totalArqueadoMes, 2) }}</div>
                </div>
            </div>
            {{-- Ventas sin arquear --}}
            <div class="col-6 col-md-3">
                <div class="stat-card card bg-white p-3 h-100">
                    <div class="stat-label mb-1"><i class="bi bi-hourglass-split me-1"></i>Ventas sin arquear</div>
                    <div class="stat-value {{ $ventasSinArquear > 0 ? 'text-warning' : '' }}">{{ $ventasSinArquear }}</div>
                </div>
            </div>
            {{-- Monto sin arquear --}}
            <div class="col-6 col-md-3">
                <div class="stat-card card bg-white p-3 h-100">
                    <div class="stat-label mb-1"><i class="bi bi-exclamation-circle me-1"></i>Monto sin arquear</div>
                    <div class="stat-value {{ $montoSinArquear > 0 ? 'text-danger' : '' }}">
                        Bs. {{ number_format($montoSinArquear, 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== FILTROS ===== --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form action="/arqueos" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="buscar" class="form-control border-start-0 bg-light"
                        placeholder="Buscar por ID o Cajero..." value="{{ request('buscar') }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0">
                        <i class="bi bi-calendar-event"></i>
                    </span>
                    <input type="date" name="fecha" class="form-control border-start-0 bg-light"
                        value="{{ request('fecha') }}">
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary text-white w-100 fw-bold">
                    <i class="bi bi-funnel me-1"></i>Filtrar
                </button>
                <a href="/arqueos" class="btn btn-light border text-muted" title="Limpiar filtros">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ===== TABLA DE ARQUEOS ===== --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom align-middle m-0">
                <thead class="table-custom-header">
                    <tr>
                        <th class="ps-4">#</th>
                        <th><i class="bi bi-clock me-1"></i>Fecha y Hora</th>
                        <th><i class="bi bi-person me-1"></i>Creado por</th>
                        <th class="text-center"><i class="bi bi-receipt me-1"></i>Ventas</th>
                        <th><i class="bi bi-cash me-1"></i>Efectivo</th>
                        <th><i class="bi bi-qr-code me-1"></i>QR</th>
                        <th class="fw-bold">Total General</th>
                        <th class="text-center pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($arqueos as $arqueo)
                    <tr>
                        {{-- ID --}}
                        <td class="ps-4">
                            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold fs-6">
                                #{{ $arqueo->id }}
                            </span>
                        </td>
                        {{-- Fecha --}}
                        <td>
                            <div class="fw-bold">{{ $arqueo->created_at->format('d/m/Y') }}</div>
                            <small class="text-muted">
                                <i class="bi bi-clock"></i> {{ $arqueo->created_at->format('H:i') }}
                            </small>
                        </td>
                        {{-- Cajero --}}
                        <td>
                            @php
                                $colors = ['#0ea5e9','#8b5cf6','#10b981','#f59e0b','#ef4444'];
                                $color = $colors[$arqueo->id_usuario % count($colors)];
                                $iniciales = strtoupper(substr(optional($arqueo->usuario)->name ?? 'U', 0, 2));
                            @endphp
                            <div class="d-flex align-items-center gap-2">
                                <span class="user-avatar" style="background-color: {{ $color }};">
                                    {{ $iniciales }}
                                </span>
                                <span class="fw-bold text-dark" style="font-size: 0.88rem;">
                                    {{ optional($arqueo->usuario)->name ?? 'Usuario' }}
                                </span>
                            </div>
                        </td>
                        {{-- Cant. ventas --}}
                        <td class="text-center">
                            <span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold px-3 py-2">
                                {{ $arqueo->cantidad_ventas }}
                            </span>
                        </td>
                        {{-- Efectivo --}}
                        <td>
                            <span class="text-success fw-bold">
                                Bs. {{ number_format($arqueo->total_efectivo, 2) }}
                            </span>
                        </td>
                        {{-- QR --}}
                        <td>
                            <span class="text-primary fw-bold">
                                Bs. {{ number_format($arqueo->total_qr, 2) }}
                            </span>
                        </td>
                        {{-- Total --}}
                        <td>
                            <span class="fw-bold text-dark fs-6">
                                Bs. {{ number_format($arqueo->total_general, 2) }}
                            </span>
                        </td>
                        {{-- Acciones --}}
                        <td class="text-center pe-4">
                            <a href="/arqueos/{{ $arqueo->id }}"
                                class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold"
                                title="Ver detalle del arqueo">
                                <i class="bi bi-eye-fill me-1"></i>Ver
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-safe2 fs-1 d-block mb-2 opacity-25"></i>
                            <p class="mb-1 fw-bold">No hay arqueos registrados</p>
                            <small>Crea el primer arqueo con el botón superior.</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($arqueos->hasPages())
        <div class="card-footer bg-white border-top-0 py-3">
            {{ $arqueos->links() }}
        </div>
        @endif
    </div>
</div>

@endsection
