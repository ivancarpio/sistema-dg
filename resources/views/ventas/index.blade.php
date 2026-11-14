@extends('layouts.app')

@section('content')

<style>
    /* Estilos generales del módulo de Ventas */
    .card { border: none !important; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important; border-radius: .75rem !important; }
    
    .table-custom-header th {
        background-color: #f1f5f9 !important; font-size: 0.78rem; font-weight: 800; color: #64748b;
        text-transform: uppercase; letter-spacing: 0.5px; padding: 14px 10px; border-bottom: none !important;
    }
    .table-custom-header th:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
    .table-custom-header th:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
    .table-custom tbody tr { border-bottom: 1px solid #f0f2f5; transition: background-color 0.15s; }
    .table-custom tbody tr:hover { background-color: #f8fafc; }
    .table-custom td { vertical-align: middle; padding: 12px 10px; }

    /* Fila anulada destacada en gris claro */
    .fila-anulada { background-color: #fcfcfc !important; }
    .fila-anulada td { color: #64748b; }

    /* Tarjetas de estadísticas */
    .stat-card-venta {
        background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 16px 20px; transition: transform 0.15s;
    }
    .stat-card-venta:hover { transform: translateY(-2px); }
    .stat-val-venta { font-size: 1.5rem; font-weight: 800; line-height: 1.1; }
    .stat-lbl-venta { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; }

    /* Pestañas de navegación */
    .nav-pills-ventas .nav-link {
        color: #64748b; font-weight: 700; border-radius: 10px; padding: 10px 20px;
        border: 1px solid #e2e8f0; background: #ffffff; margin-right: 8px; transition: all 0.2s;
    }
    .nav-pills-ventas .nav-link:hover { color: #0284c7; background: #f0f9ff; }
    .nav-pills-ventas .nav-link.active {
        color: #ffffff; background: linear-gradient(135deg, #0284c7, #0ea5e9);
        border-color: #0284c7; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
    }

    /* Opciones rápidas de motivo en modal */
    .btn-motivo-rapido {
        font-size: 0.78rem; font-weight: 600; padding: 4px 10px; border-radius: 20px;
        border: 1px dashed #cbd5e1; background: #f8fafc; color: #475569; transition: all 0.15s;
    }
    .btn-motivo-rapido:hover {
        background: #eff6ff; border-color: #3b82f6; color: #1d4ed8;
    }
</style>

<!-- ALERTAS -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- ENCABEZADO PRINCIPAL -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div class="d-flex align-items-center">
        <div class="bg-info bg-opacity-10 p-2 rounded-3 me-3 text-info">
            <i class="bi bi-receipt-cutoff fs-2"></i>
        </div>
        <div>
            <h3 class="fw-bold m-0 text-dark">Historial de Ventas</h3>
            <small class="text-muted">Supervisa notas emitidas, audita anulaciones y analiza clientes recurrentes</small>
        </div>
    </div>
    <div>
        <a href="/pos" class="btn btn-primary fw-bold px-4 py-2 shadow-sm rounded-3">
            <i class="bi bi-cart-plus me-1"></i> Ir al Punto de Venta
        </a>
    </div>
</div>

<!-- TARJETAS DE MÉTRICAS RÁPIDAS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card-venta h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="stat-lbl-venta">Facturado Válido</span>
                <i class="bi bi-cash-stack text-success fs-4"></i>
            </div>
            <div class="stat-val-venta text-success">Bs. {{ number_format($totalFacturado, 2) }}</div>
            <small class="text-muted">sin contar anuladas</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card-venta h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="stat-lbl-venta">Notas Completadas</span>
                <i class="bi bi-check2-circle text-primary fs-4"></i>
            </div>
            <div class="stat-val-venta text-primary">{{ $totalCompletadas }}</div>
            <small class="text-muted">ventas cobradas con éxito</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card-venta h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="stat-lbl-venta">Talonarios Físicos</span>
                <i class="bi bi-journal-text text-info fs-4"></i>
            </div>
            <div class="stat-val-venta text-info">{{ $totalFisicas }}</div>
            <small class="text-muted">emitidas fuera de sistema</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card-venta h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="stat-lbl-venta">Notas Anuladas</span>
                <i class="bi bi-x-octagon text-danger fs-4"></i>
            </div>
            <div class="stat-val-venta text-danger">{{ $totalAnuladas }}</div>
            <small class="text-muted">con registro y motivo de baja</small>
        </div>
    </div>
</div>

<!-- NAVEGACIÓN ENTRE PESTAÑAS (HISTORIAL VS CLIENTES RECURRENTES) -->
<ul class="nav nav-pills nav-pills-ventas mb-3" id="ventasTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active d-flex align-items-center" id="tab-notas-btn" data-bs-toggle="pill" data-bs-target="#tab-notas" type="button" role="tab">
            <i class="bi bi-receipt me-2"></i> Historial de Notas de Venta
            <span class="badge bg-white text-dark ms-2 rounded-pill px-2">{{ $ventas->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link d-flex align-items-center" id="tab-clientes-btn" data-bs-toggle="pill" data-bs-target="#tab-clientes" type="button" role="tab">
            <i class="bi bi-people-fill me-2"></i> Clientes y Frecuencia de Compra
            <span class="badge bg-light text-secondary ms-2 rounded-pill px-2">{{ $compradores->count() }}</span>
        </button>
    </li>
</ul>

<div class="tab-content" id="ventasTabsContent">

    <!-- ============================================================ -->
    <!-- PESTAÑA 1: HISTORIAL DE NOTAS DE VENTA                        -->
    <!-- ============================================================ -->
    <div class="tab-pane fade show active" id="tab-notas" role="tabpanel">
        
        <!-- PANEL DE FILTROS LIMPIO E INTUITIVO -->
        <div class="card p-3 mb-4">
            <form action="/ventas" method="GET" class="row g-2 align-items-center">
                
                <!-- Buscador rápido -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Buscar por Nota o Cliente</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="buscar" class="form-control border-start-0 bg-light" placeholder="Ej. Juan, SYS-00001..." value="{{ request('buscar') }}">
                    </div>
                </div>

                <!-- Fecha específica -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Fecha</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-calendar-event"></i></span>
                        <input type="date" name="fecha" class="form-control border-start-0 bg-light" value="{{ request('fecha') }}">
                    </div>
                </div>

                <!-- Cajero responsable -->
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Cajero</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person-badge"></i></span>
                        <select name="cajero" class="form-select border-start-0 bg-light">
                            <option value="">Todos los cajeros</option>
                            @foreach($cajeros as $caj)
                                <option value="{{ $caj->id }}" {{ request('cajero') == $caj->id ? 'selected' : '' }}>
                                    {{ $caj->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Selector de Tipo / Estado (Sin interruptores confusos) -->
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Tipo / Estado de Nota</label>
                    <select name="filtro_tipo" class="form-select bg-light">
                        <option value="">Todas las notas</option>
                        <option value="completadas" {{ request('filtro_tipo') == 'completadas' ? 'selected' : '' }}>
                            🟢 Completadas (Válidas)
                        </option>
                        <option value="fisicas" {{ request('filtro_tipo') == 'fisicas' ? 'selected' : '' }}>
                            📝 Talonario Físico (Manual)
                        </option>
                        <option value="sistema" {{ request('filtro_tipo') == 'sistema' ? 'selected' : '' }}>
                            💻 Solo Sistema (SYS)
                        </option>
                        <option value="anuladas" {{ request('filtro_tipo') == 'anuladas' ? 'selected' : '' }}>
                            🔴 Solo Anuladas
                        </option>
                    </select>
                </div>

                <!-- Botones Filtrar y Limpiar -->
                <div class="col-md-2 d-flex align-items-end gap-1 pt-4">
                    <button type="submit" class="btn btn-primary text-white w-100 fw-bold shadow-sm" style="height: 38px;">
                        <i class="bi bi-funnel-fill me-1"></i> Filtrar
                    </button>
                    <a href="/ventas" class="btn btn-light border text-muted" title="Limpiar todos los filtros" style="height: 38px;">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>

                <!-- Mantener orden al filtrar -->
                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'fecha') }}">
                <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">
            </form>
        </div>

        <!-- TABLA DE VENTAS -->
        <div class="card overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table table-custom align-middle m-0" id="tablaVentas">
                    <thead class="table-custom-header">
                        <tr>
                            <th class="ps-4" style="width: 14%;">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'numero_nota', 'sort_order' => (request('sort_by') == 'numero_nota' && request('sort_order') == 'asc') ? 'desc' : 'asc']) }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1">
                                    Nº de Nota 
                                    @if(request('sort_by') == 'numero_nota') <i class="bi bi-sort-alpha-{{ request('sort_order') == 'asc' ? 'down' : 'up-alt' }} text-primary fs-6"></i> @else <i class="bi bi-arrow-down-up opacity-25"></i> @endif
                                </a>
                            </th>
                            <th style="width: 14%;">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'fecha', 'sort_order' => (request('sort_by', 'fecha') == 'fecha' && request('sort_order', 'desc') == 'desc') ? 'asc' : 'desc']) }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1">
                                    Fecha y Hora
                                    @if(request('sort_by', 'fecha') == 'fecha') <i class="bi bi-sort-numeric-{{ request('sort_order', 'desc') == 'desc' ? 'down-alt' : 'up' }} text-primary fs-6"></i> @else <i class="bi bi-arrow-down-up opacity-25"></i> @endif
                                </a>
                            </th>
                            <th style="width: 20%;">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'cliente', 'sort_order' => (request('sort_by') == 'cliente' && request('sort_order') == 'asc') ? 'desc' : 'asc']) }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1">
                                    Cliente
                                    @if(request('sort_by') == 'cliente') <i class="bi bi-sort-alpha-{{ request('sort_order') == 'asc' ? 'down' : 'up-alt' }} text-primary fs-6"></i> @else <i class="bi bi-arrow-down-up opacity-25"></i> @endif
                                </a>
                            </th>
                            <th style="width: 12%;">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'total', 'sort_order' => (request('sort_by') == 'total' && request('sort_order') == 'asc') ? 'desc' : 'asc']) }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1">
                                    Total
                                    @if(request('sort_by') == 'total') <i class="bi bi-sort-numeric-{{ request('sort_order') == 'asc' ? 'down' : 'up-alt' }} text-primary fs-6"></i> @else <i class="bi bi-arrow-down-up opacity-25"></i> @endif
                                </a>
                            </th>
                            <th style="width: 12%;">Cajero</th>
                            <th style="width: 16%;">Estado y Auditoría</th>
                            <th class="text-center pe-4" style="width: 12%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                        @php
                            $esAnulada = ($venta->estado === 'Anulada');
                            $esFisica  = !str_starts_with($venta->numero_nota ?? '', 'SYS-');
                        @endphp
                        <tr class="{{ $esAnulada ? 'fila-anulada' : '' }}">
                            
                            {{-- Nº de Nota --}}
                            <td class="ps-4">
                                <div class="fw-bold {{ $esAnulada ? 'text-decoration-line-through text-muted' : 'text-dark' }}">
                                    {{ $venta->numero_nota ?? 'SYS-'.str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}
                                </div>
                                <small class="text-muted">
                                    @if($esFisica)
                                        <span class="badge bg-light text-secondary border" style="font-size:0.68rem;"><i class="bi bi-journal me-1"></i>Talonario</span>
                                    @else
                                        <span class="badge bg-light text-muted border" style="font-size:0.68rem;">Sistema</span>
                                    @endif
                                </small>
                            </td>

                            {{-- Fecha y Hora --}}
                            <td>
                                <div class="fw-bold {{ $esAnulada ? 'text-muted' : 'text-dark' }}">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</div>
                                <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($venta->fecha)->format('H:i') }}</small>
                            </td>

                            {{-- Cliente --}}
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle p-1 me-2 text-secondary d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold {{ $esAnulada ? 'text-muted' : 'text-dark' }}">
                                            {{ $venta->cliente }}
                                        </span>
                                        @if(($venta->tipo_tarifa ?? '') === 'Regular' || $venta->id_cliente || isset($clientesRegulares[$venta->cliente]))
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1" style="font-size: 0.65rem;">Regular</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Total --}}
                            <td class="fw-bold {{ $esAnulada ? 'text-muted text-decoration-line-through' : 'text-success' }}" style="font-size: 0.95rem;">
                                Bs. {{ number_format($venta->total, 2) }}
                            </td>

                            {{-- Cajero --}}
                            <td>
                                <div class="small fw-semibold text-dark">
                                    <i class="bi bi-person-circle me-1 text-secondary"></i>
                                    {{ optional($venta->usuario)->name ?? 'Cajero' }}
                                </div>
                            </td>
                            
                            {{-- Estado y Detalle de Anulación --}}
                            <td>
                                @if($esAnulada)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-bold">
                                        <i class="bi bi-x-circle-fill me-1"></i> Anulada
                                    </span>
                                    @if($venta->motivo_anulacion)
                                        <div class="mt-1 small text-danger fw-semibold" style="font-size: 0.74rem;">
                                            <i class="bi bi-chat-left-text me-1"></i>{{ $venta->motivo_anulacion }}
                                        </div>
                                    @endif
                                    @if($venta->usuarioAnulacion || $venta->fecha_anulacion)
                                        <div class="small text-muted" style="font-size: 0.70rem;">
                                            <i class="bi bi-shield-check me-1"></i>Por: {{ optional($venta->usuarioAnulacion)->name ?? 'Cajero' }}
                                            @if($venta->fecha_anulacion)
                                                ({{ \Carbon\Carbon::parse($venta->fecha_anulacion)->format('d/m H:i') }})
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">
                                        <i class="bi bi-check-circle-fill me-1"></i> Completada
                                    </span>
                                    <div class="small text-muted" style="font-size: 0.72rem;">
                                        Método: {{ $venta->metodo_pago }}
                                    </div>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-1">
                                    <!-- Botón Cobrar Saldo Pendiente -->
                                    @if(!$esAnulada && floatval($venta->saldo_pendiente ?? 0) > 0)
                                    <button type="button" 
                                            class="btn btn-sm btn-success fw-bold rounded-pill px-3 shadow-sm btn-abrir-modal-cobro" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalCobrarNotaVenta"
                                            data-id="{{ $venta->id }}"
                                            data-nota="{{ $venta->numero_nota ?? ('SYS-'.str_pad($venta->id, 6, '0', STR_PAD_LEFT)) }}"
                                            data-cliente="{{ $venta->cliente }}"
                                            data-saldo="{{ number_format($venta->saldo_pendiente, 2, '.', '') }}"
                                            data-total="{{ number_format($venta->total, 2) }}"
                                            data-pagado="{{ number_format($venta->monto_pagado ?? 0, 2) }}"
                                            title="Cobrar o abonar saldo pendiente">
                                        <i class="bi bi-cash-coin me-1"></i> Cobrar
                                    </button>
                                    @endif

                                    <!-- Botón Ver PDF -->
                                    <a href="/ventas/{{ $venta->id }}/pdf" target="_blank" class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-3" title="Descargar / Imprimir Nota en PDF">
                                        <i class="bi bi-file-earmark-pdf"></i> PDF
                                    </a>

                                    <!-- Botón Anular con Modal -->
                                    @if(!$esAnulada && auth()->check() && auth()->user()->tienePermiso('anular_ventas'))
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-warning fw-bold rounded-pill px-3 btn-abrir-modal-anular" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalAnularVenta"
                                            data-id="{{ $venta->id }}"
                                            data-nota="{{ $venta->numero_nota ?? ('SYS-'.str_pad($venta->id, 6, '0', STR_PAD_LEFT)) }}"
                                            data-cliente="{{ $venta->cliente }}"
                                            data-total="{{ number_format($venta->total, 2) }}"
                                            title="Anular venta y registrar motivo">
                                        <i class="bi bi-x-octagon-fill"></i> Anular
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-receipt-cutoff fs-1 d-block mb-2 opacity-50"></i>
                                <h6>No se encontraron ventas con los filtros seleccionados.</h6>
                                <p class="small text-muted mb-0">Modifica los filtros o <a href="/ventas">limpia la búsqueda</a>.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- PESTAÑA 2: COMPRADORES FRECUENTES (OCASIONALES Y REGULARES)  -->
    <!-- ============================================================ -->
    <div class="tab-pane fade" id="tab-clientes" role="tabpanel">
        
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4">
            <i class="bi bi-info-circle-fill fs-3 me-3 text-info"></i>
            <div>
                <strong class="d-block text-dark">Detección de Compradores Frecuentes</strong>
                <span class="small text-muted">
                    Esta vista agrupa todas las notas de venta por el nombre del cliente. Te permite identificar qué clientes de mostrador compran habitualmente para registrarlos como <strong>Clientes Regulares</strong> con sus datos completos.
                </span>
            </div>
        </div>

        <div class="card overflow-hidden mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <div class="input-group" style="max-width: 320px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="buscadorCompradores" class="form-control border-start-0 bg-light" placeholder="Filtrar cliente en la lista...">
                </div>
                <div class="small text-muted fw-bold">
                    Mostrando <strong id="contadorCompradores">{{ $compradores->count() }}</strong> cliente(s) registrados en ventas
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-custom align-middle m-0" id="tablaCompradores">
                    <thead class="table-custom-header">
                        <tr>
                            <th class="ps-4" style="width: 25%;">Cliente</th>
                            <th class="text-center" style="width: 15%;">Tipo de Cuenta</th>
                            <th class="text-center" style="width: 15%;">Compras Realizadas</th>
                            <th class="text-end" style="width: 15%;">Total Gastado</th>
                            <th style="width: 15%;">Última Compra</th>
                            <th class="text-center pe-4" style="width: 15%;">Acción Recomendada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($compradores as $comp)
                        @php
                            $idRegular = $comp->id_cliente_asociado ?? ($clientesRegulares[$comp->cliente] ?? null);
                            $esRegular = !empty($idRegular);
                        @endphp
                        <tr class="fila-comprador">
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle p-2 me-2 {{ $esRegular ? 'bg-primary bg-opacity-10 text-primary' : 'bg-light text-secondary' }} d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                        <i class="bi {{ $esRegular ? 'bi-person-check-fill' : 'bi-person' }}"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark nombre-comprador">{{ $comp->cliente }}</div>
                                        <small class="text-muted">Nombre registrado en ventas</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($esRegular)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-bold">
                                        <i class="bi bi-patch-check-fill me-1"></i> Cliente Regular
                                    </span>
                                @else
                                    <span class="badge bg-light text-secondary border px-2 py-1 fw-bold">
                                        <i class="bi bi-shop me-1"></i> Mostrador / Ocasional
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-3 py-1 fw-bold fs-6">
                                    {{ $comp->total_compras }}
                                </span>
                            </td>
                            <td class="text-end fw-bold text-success fs-6">
                                Bs. {{ number_format($comp->total_gastado, 2) }}
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($comp->ultima_compra)->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($comp->ultima_compra)->format('H:i') }}</small>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-1">
                                    @if(!$esRegular)
                                        <a href="/clientes?nuevo_nombre={{ urlencode($comp->cliente) }}" class="btn btn-sm btn-outline-primary fw-bold rounded-pill px-3" title="Registrar como cliente regular en el sistema">
                                            <i class="bi bi-person-plus-fill me-1"></i> + Regular
                                        </a>
                                    @else
                                        <a href="/clientes/{{ $idRegular }}" class="btn btn-sm btn-light border fw-bold rounded-pill px-3 text-primary" title="Ver cuenta corriente e historial">
                                            <i class="bi bi-eye-fill me-1"></i> Ver Cuenta
                                        </a>
                                    @endif
                                    <a href="/ventas?buscar={{ urlencode($comp->cliente) }}" class="btn btn-sm btn-light border text-muted rounded-pill px-2" title="Ver notas de este cliente">
                                        <i class="bi bi-receipt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-2 opacity-50"></i>
                                <h6>No hay compras registradas aún con nombres de clientes.</h6>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- ============================================================ -->
<!-- MODAL DE CONFIRMACIÓN Y LOG DE ANULACIÓN DE VENTA            -->
<!-- ============================================================ -->
<div class="modal fade" id="modalAnularVenta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Anular Nota de Venta
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formAnularVenta" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    
                    <!-- Resumen de la nota a cancelar -->
                    <div class="p-3 rounded-3 bg-danger bg-opacity-10 border border-danger-subtle mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted fw-bold">Nº DE NOTA:</span>
                            <span class="fw-bold text-danger fs-6" id="anularNotaNumero">—</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted fw-bold">CLIENTE:</span>
                            <span class="fw-bold text-dark" id="anularNotaCliente">—</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted fw-bold">TOTAL VENTA:</span>
                            <span class="fw-bold text-success fs-5">Bs. <span id="anularNotaTotal">0.00</span></span>
                        </div>
                    </div>

                    <div class="alert alert-warning border-0 small py-2 mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Al anular, el monto se descontará de la caja, todos los artículos vendidos <strong>volverán automáticamente al stock</strong> y se guardará tu nombre en la bitácora de auditoría.
                    </div>

                    <!-- Motivo obligatorio -->
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold text-dark small text-uppercase">
                            Motivo de la Anulación *
                        </label>

                        <!-- Botones rápidos de motivo -->
                        <div class="d-flex flex-wrap gap-1 mb-2">
                            <button type="button" class="btn-motivo-rapido" onclick="seleccionarMotivoRapido('Error de digitación o monto')">Error de tipeo</button>
                            <button type="button" class="btn-motivo-rapido" onclick="seleccionarMotivoRapido('Cliente canceló la compra')">Cliente canceló</button>
                            <button type="button" class="btn-motivo-rapido" onclick="seleccionarMotivoRapido('Devolución de mercadería defectuosa')">Mercadería devuelta</button>
                            <button type="button" class="btn-motivo-rapido" onclick="seleccionarMotivoRapido('Cliente no contaba con el dinero completo')">Falta de dinero</button>
                            <button type="button" class="btn-motivo-rapido" onclick="seleccionarMotivoRapido('Cambio de método de pago o talonario')">Cambio de forma de pago</button>
                        </div>

                        <textarea name="motivo" 
                                  id="inputMotivoAnulacion" 
                                  rows="3" 
                                  class="form-control" 
                                  placeholder="Escribe detalladamente por qué se anula esta venta..." 
                                  required 
                                  minlength="3"></textarea>
                        <div class="form-text small">Mínimo 3 caracteres. Este motivo quedará registrado en el historial y en el Kardex de inventario.</div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary fw-bold px-3" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger fw-bold px-4 shadow-sm" id="btnConfirmarAnulacion">
                        <i class="bi bi-x-octagon-fill me-1"></i> Confirmar Anulación y Devolver Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL DE COBRO DE SALDO PENDIENTE                            -->
<!-- ============================================================ -->
<div class="modal fade" id="modalCobrarNotaVenta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            
            <div class="modal-header bg-success text-white py-3 px-4 border-0">
                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255, 255, 255, 0.2);">
                        <i class="bi bi-cash-stack fs-4 text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold m-0 text-white">Registrar Cobro de Saldo Pendiente</h5>
                        <small class="text-white-50">Liquidación o abono a nota de venta a crédito</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formCobrarNotaVenta" method="POST" action="">
                @csrf
                <div class="modal-body p-4 bg-white">
                    
                    <!-- Resumen de la nota -->
                    <div class="p-3 rounded-3 bg-light border mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted fw-bold">Nº DE NOTA:</span>
                            <span class="fw-bold text-dark fs-6" id="cobroNotaNumero">—</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted fw-bold">CLIENTE:</span>
                            <span class="fw-bold text-primary" id="cobroNotaCliente">—</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted fw-bold">TOTAL VENTA:</span>
                            <span class="fw-bold text-dark">Bs. <span id="cobroNotaTotal">0.00</span></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="small text-danger fw-bold text-uppercase">Saldo Pendiente:</span>
                            <span class="fw-bold text-danger fs-4">Bs. <span id="cobroNotaSaldo">0.00</span></span>
                        </div>
                    </div>

                    <!-- Monto a cobrar -->
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold text-dark small text-uppercase">
                            Monto a Cobrar / Pagar (Bs.) *
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light text-success fw-bold">Bs.</span>
                            <input type="number" step="0.10" min="0.50" name="monto_abono" id="inputMontoCobroNota" class="form-control fw-bold text-success fs-4" required>
                        </div>
                        <div class="form-text small">Por defecto cobra el saldo total adeudado. Puedes modificarlo si el cliente realiza un pago parcial.</div>
                    </div>

                    <!-- Método de cobro -->
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold text-dark small text-uppercase">
                            Forma de Cobro *
                        </label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="metodo_pago" id="cobroMetodoEf" value="Efectivo" checked>
                                <label class="btn btn-outline-success w-100 fw-bold py-2 d-flex align-items-center justify-content-center" for="cobroMetodoEf">
                                    <i class="bi bi-cash me-2 fs-5"></i> Efectivo
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="metodo_pago" id="cobroMetodoQr" value="QR">
                                <label class="btn btn-outline-primary w-100 fw-bold py-2 d-flex align-items-center justify-content-center" for="cobroMetodoQr">
                                    <i class="bi bi-qr-code me-2 fs-5"></i> QR Simple
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Observación -->
                    <div class="mb-0 text-start">
                        <label class="form-label text-muted small fw-bold">Observación (Opcional)</label>
                        <input type="text" name="observacion" class="form-control" placeholder="Ej. Pago en caja, liquidación de saldo...">
                    </div>
                </div>

                <div class="modal-footer bg-light py-3 px-4 d-flex justify-content-between border-0">
                    <button type="button" class="btn btn-outline-secondary fw-bold px-3" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-success fw-bold px-4 shadow-sm" id="btnConfirmarCobroNota">
                        <i class="bi bi-check-circle-fill me-1"></i> Confirmar Cobro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Preparar Modal de Anulación
    const modalAnular = document.getElementById('modalAnularVenta');
    const formAnular = document.getElementById('formAnularVenta');
    const txtNumero = document.getElementById('anularNotaNumero');
    const txtCliente = document.getElementById('anularNotaCliente');
    const txtTotal = document.getElementById('anularNotaTotal');
    const inputMotivo = document.getElementById('inputMotivoAnulacion');

    document.querySelectorAll('.btn-abrir-modal-anular').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nota = this.getAttribute('data-nota');
            const cli = this.getAttribute('data-cliente');
            const tot = this.getAttribute('data-total');

            formAnular.action = `/ventas/${id}/anular`;
            txtNumero.textContent = nota;
            txtCliente.textContent = cli;
            txtTotal.textContent = tot;
            inputMotivo.value = '';
            setTimeout(() => inputMotivo.focus(), 400);
        });
    });

    // 2. Preparar Modal de Cobro de Saldo
    const formCobro = document.getElementById('formCobrarNotaVenta');
    const txtCobroNumero = document.getElementById('cobroNotaNumero');
    const txtCobroCliente = document.getElementById('cobroNotaCliente');
    const txtCobroTotal = document.getElementById('cobroNotaTotal');
    const txtCobroSaldo = document.getElementById('cobroNotaSaldo');
    const inputMontoCobro = document.getElementById('inputMontoCobroNota');

    document.querySelectorAll('.btn-abrir-modal-cobro').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nota = this.getAttribute('data-nota');
            const cli = this.getAttribute('data-cliente');
            const saldo = this.getAttribute('data-saldo');
            const tot = this.getAttribute('data-total');

            formCobro.action = `/ventas/${id}/abono`;
            txtCobroNumero.textContent = nota;
            txtCobroCliente.textContent = cli;
            txtCobroTotal.textContent = tot;
            txtCobroSaldo.textContent = saldo;
            inputMontoCobro.value = saldo;
            inputMontoCobro.max = saldo;
            setTimeout(() => inputMontoCobro.focus(), 400);
        });
    });

    // 3. Buscador en tiempo real de compradores frecuentes
    const buscadorComp = document.getElementById('buscadorCompradores');
    if (buscadorComp) {
        buscadorComp.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const filas = document.querySelectorAll('#tablaCompradores .fila-comprador');
            let visibles = 0;

            filas.forEach(f => {
                const nombre = f.querySelector('.nombre-comprador')?.textContent.toLowerCase() || '';
                if (query === '' || nombre.includes(query)) {
                    f.style.display = '';
                    visibles++;
                } else {
                    f.style.display = 'none';
                }
            });

            const contador = document.getElementById('contadorCompradores');
            if (contador) contador.textContent = visibles;
        });
    }
});

function seleccionarMotivoRapido(texto) {
    const textarea = document.getElementById('inputMotivoAnulacion');
    if (textarea) {
        textarea.value = texto;
        textarea.focus();
    }
}
</script>

@endsection
