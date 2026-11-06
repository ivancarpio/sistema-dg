@extends('layouts.app')

@section('content')

<style>
    .table-custom-header th {
        background-color: #f1f5f9 !important; font-size: 0.8rem; font-weight: 800; color: #64748b;
        text-transform: uppercase; letter-spacing: 0.5px; padding: 15px 10px; border-bottom: none !important;
    }
    .table-custom-header th:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
    .table-custom-header th:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
    .table-custom tbody tr { border-bottom: 1px solid #f0f2f5; transition: background-color 0.2s; }
    .table-custom tbody tr:hover { background-color: #f8f9fa; }
    .table-custom td { vertical-align: middle; padding: 12px 10px; }
    .card { border: none !important; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important; border-radius: .5rem !important; }
    
    /* Venta anulada se ve opaca */
    .fila-anulada { opacity: 0.6; background-color: #f8f9fa; }
    .table-custom-header a { transition: color 0.2s ease-in-out; }
    .table-custom-header a:hover { color: #0dcaf0 !important; }
</style>

<!-- Alertas -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm"><i class="bi bi-check-circle me-2"></i>{{ session('success') }} <button class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }} <button class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<!-- Encabezado con Icono Celeste -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div class="bg-info bg-opacity-10 p-2 rounded me-3 text-info">
            <i class="bi bi-receipt-cutoff fs-3"></i>
        </div>
        <div>
            <h3 class="fw-bold m-0 text-dark">Historial de Ventas</h3>
            <small class="text-muted">Administra, filtra y anula las notas de venta</small>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-0">
        <h6 class="fw-bold m-0 mb-3"><i class="bi bi-funnel"></i> Filtros de Búsqueda Avanzada</h6>
        
        <!-- PANEL DE FILTROS -->
        <form action="/ventas" method="GET" class="row g-2 align-items-center">
            <!-- Buscador -->
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" class="form-control border-start-0 bg-light" placeholder="Nº o Cliente..." value="{{ request('buscar') }}">
                </div>
            </div>
            <!-- Fecha -->
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-calendar-event"></i></span>
                    <input type="date" name="fecha" class="form-control border-start-0 bg-light" value="{{ request('fecha') }}">
                </div>
            </div>
            <!-- Cajero -->
            <div class="col-md-2">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person-badge"></i></span>
                    <select name="cajero" class="form-select border-start-0 bg-light">
                        <option value="">Cajeros</option>
                        <option value="1" {{ request('cajero') == '1' ? 'selected' : '' }}>Iván</option>
                    </select>
                </div>
            </div>
            
            <!-- Switches (Físicas y Anuladas) -->
            <div class="col-md-2 d-flex justify-content-center gap-3">
                <!-- Switch Solo Físicas -->
                <div class="form-check form-switch pt-2" title="Solo notas físicas">
                    <input class="form-check-input" type="checkbox" role="switch" id="notasFisicas" name="notas_fisicas" value="1" {{ request('notas_fisicas') ? 'checked' : '' }}>
                    <label class="form-check-label small fw-bold text-muted" for="notasFisicas">Físicas</label>
                </div>
                <!-- Switch Solo Anuladas -->
                <div class="form-check form-switch pt-2" title="Solo ventas anuladas">
                    <!-- Si este checkbox está marcado, enviará estado=Anulada -->
                    <input class="form-check-input" type="checkbox" role="switch" id="soloAnuladas" name="estado" value="Anulada" {{ request('estado') == 'Anulada' ? 'checked' : '' }}>
                    <label class="form-check-label small fw-bold text-danger" for="soloAnuladas">Anuladas</label>
                </div>
            </div>

            <!-- Botones -->
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-info text-white w-100 fw-bold shadow-sm">Filtrar</button>
                <a href="/ventas" class="btn btn-light border text-muted" title="Limpiar filtros"><i class="bi bi-arrow-clockwise"></i></a>
            </div>

            <!-- Mantener orden al filtrar -->
            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'fecha') }}">
            <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom align-middle m-0">
                <thead class="table-custom-header">
                    <tr>
                        <th class="ps-4">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'numero_nota', 'sort_order' => (request('sort_by') == 'numero_nota' && request('sort_order') == 'asc') ? 'desc' : 'asc']) }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1">
                                Nº de Nota 
                                @if(request('sort_by') == 'numero_nota') <i class="bi bi-sort-alpha-{{ request('sort_order') == 'asc' ? 'down' : 'up-alt' }} text-info fs-6"></i> @else <i class="bi bi-arrow-down-up opacity-25"></i> @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'fecha', 'sort_order' => (request('sort_by', 'fecha') == 'fecha' && request('sort_order', 'desc') == 'desc') ? 'asc' : 'desc']) }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1">
                                Fecha y Hora
                                @if(request('sort_by', 'fecha') == 'fecha') <i class="bi bi-sort-numeric-{{ request('sort_order', 'desc') == 'desc' ? 'down-alt' : 'up' }} text-info fs-6"></i> @else <i class="bi bi-arrow-down-up opacity-25"></i> @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'cliente', 'sort_order' => (request('sort_by') == 'cliente' && request('sort_order') == 'asc') ? 'desc' : 'asc']) }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1">
                                Cliente
                                @if(request('sort_by') == 'cliente') <i class="bi bi-sort-alpha-{{ request('sort_order') == 'asc' ? 'down' : 'up-alt' }} text-info fs-6"></i> @else <i class="bi bi-arrow-down-up opacity-25"></i> @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'total', 'sort_order' => (request('sort_by') == 'total' && request('sort_order') == 'asc') ? 'desc' : 'asc']) }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1">
                                Total
                                @if(request('sort_by') == 'total') <i class="bi bi-sort-numeric-{{ request('sort_order') == 'asc' ? 'down' : 'up-alt' }} text-info fs-6"></i> @else <i class="bi bi-arrow-down-up opacity-25"></i> @endif
                            </a>
                        </th>
                        <th>Cajero</th>
                        <th>Estado</th>
                        <th class="text-center pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $venta)
                    <tr class="{{ $venta->estado == 'Anulada' ? 'fila-anulada' : '' }}">
                        <td class="ps-4 fw-bold">{{ $venta->numero_nota ?? 'SYS-'.str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <div class="fw-bold">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</div>
                            <small class="text-muted"><i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($venta->fecha)->format('H:i') }}</small>
                        </td>
                        <td><span class="badge bg-light text-dark border"><i class="bi bi-person me-1"></i> {{ $venta->cliente }}</span></td>
                        <td class="fw-bold text-success">Bs. {{ number_format($venta->total, 2) }}</td>
                        <td><small class="text-muted fw-bold"><i class="bi bi-person-circle"></i> {{ optional($venta->usuario)->name ?? 'Cajero' }}</small></td>
                        
                        <!-- Columna Estado -->
                        <td>
                            @if($venta->estado == 'Anulada')
                                <span class="badge bg-secondary">Anulada</span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Completada</span>
                            @endif
                        </td>

                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-2">
                                <!-- Botón Ver PDF -->
                                <a href="/ventas/{{ $venta->id }}/pdf" target="_blank" class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-3">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                </a>

                                <!-- Botón Anular (Solo aparece si no está anulada) -->
                                @if($venta->estado !== 'Anulada')
                                <form action="/ventas/{{ $venta->id }}/anular" method="POST" onsubmit="return confirm('¿ESTÁS SEGURO?\n\nAl anular esta venta, el monto no contará para la caja y todos los productos regresarán automáticamente al stock del inventario.');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning fw-bold rounded-pill px-3" title="Anular y devolver stock">
                                        <i class="bi bi-x-octagon-fill"></i> Anular
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-receipt-cutoff fs-1 d-block mb-2"></i>
                            No hay registros de transacciones con estos filtros.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection