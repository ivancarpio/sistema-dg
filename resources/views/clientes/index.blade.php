@extends('layouts.app')

@section('content')

<style>
    .cliente-card {
        border: none !important;
        border-radius: 14px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important;
        background: #ffffff;
    }

    /* Stats Cards */
    .stat-card-cli {
        border-radius: 14px;
        border: none;
        padding: 20px;
        background: #ffffff;
        box-shadow: 0 4px 18px rgba(0,0,0,0.04);
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .stat-card-cli:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 22px rgba(0,0,0,0.07);
    }
    .stat-icon-cli {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .stat-val-cli {
        font-size: 1.6rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }
    .stat-lbl-cli {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #64748b;
        margin-bottom: 4px;
    }

    /* Tabla */
    .table-custom-header th {
        background-color: #f8fafc !important;
        font-size: 0.82rem;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 16px 14px;
        border-bottom: 2px solid #e2e8f0 !important;
    }
    .table-custom tbody tr { 
        border-bottom: 1px solid #f1f5f9; 
        transition: background-color 0.15s; 
    }
    .table-custom tbody tr:hover { 
        background-color: #f8fafc; 
    }
    .table-custom td { 
        vertical-align: middle; 
        padding: 15px 14px; 
    }

    /* Avatar iniciales */
    .avatar-cli {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.1rem;
        color: #0284c7;
        background: #e0f2fe;
        flex-shrink: 0;
    }

    /* Botones de acción circulares */
    .btn-action-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.2s;
        font-size: 1.1rem;
        text-decoration: none;
    }
    .btn-action-view {
        background: #f1f5f9;
        color: #334155;
    }
    .btn-action-view:hover {
        background: #334155;
        color: #ffffff;
        transform: scale(1.08);
    }
    .btn-action-edit {
        background: #e0f2fe;
        color: #0284c7;
    }
    .btn-action-edit:hover {
        background: #0284c7;
        color: #ffffff;
        transform: scale(1.08);
    }
    .btn-action-delete {
        background: #fee2e2;
        color: #dc2626;
    }
    .btn-action-delete:hover {
        background: #dc2626;
        color: #ffffff;
        transform: scale(1.08);
    }

    /* Buscador */
    .search-btn-left {
        background: linear-gradient(135deg, #0284c7, #0ea5e9);
        color: white;
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;
        border: none;
        padding: 0 18px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .search-input {
        border-top-right-radius: 10px;
        border-bottom-right-radius: 10px;
        border: 1px solid #cbd5e1;
        background-color: #f8fafc;
        font-size: 0.95rem;
    }
    .search-input:focus {
        background-color: #ffffff;
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
    }
</style>

<!-- ALERTAS -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-4 text-success me-2"></i>
            <div><strong>{{ session('success') }}</strong></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-4 text-danger me-2"></i>
            <div><strong>{{ $errors->first() }}</strong></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- ENCABEZADO -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3 text-primary">
            <i class="bi bi-person-lines-fill fs-2"></i>
        </div>
        <div>
            <h3 class="fw-bold m-0 text-dark">Clientes Frecuentes</h3>
            <small class="text-muted">Registro de clientes regulares • Datos de contacto y control de deudas</small>
        </div>
    </div>
    
    <button class="btn btn-primary fw-bold shadow-sm px-4 py-2" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
        <i class="bi bi-person-plus-fill me-1"></i> + Nuevo Cliente
    </button>
</div>

<!-- STATS CARDS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card-cli">
            <div class="stat-icon-cli" style="background: rgba(2, 132, 199, 0.12); color: #0284c7;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="stat-lbl-cli">Total Clientes</div>
                <div class="stat-val-cli">{{ $totalClientes }}</div>
                <small class="text-muted">registrados</small>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card-cli">
            <div class="stat-icon-cli" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <div class="stat-lbl-cli">Con Saldo Pendiente</div>
                <div class="stat-val-cli {{ $clientesConDeuda > 0 ? 'text-warning' : 'text-secondary' }}">
                    {{ $clientesConDeuda }}
                </div>
                <small class="text-muted">por cobrar</small>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card-cli">
            <div class="stat-icon-cli" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                <i class="bi bi-wallet2"></i>
            </div>
            <div>
                <div class="stat-lbl-cli">Total por Cobrar</div>
                <div class="stat-val-cli {{ $totalDeudaPorCobrar > 0 ? 'text-danger' : 'text-secondary' }}" style="font-size: 1.4rem;">
                    Bs. {{ number_format($totalDeudaPorCobrar, 2) }}
                </div>
                <small class="text-muted">en créditos</small>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card-cli">
            <div class="stat-icon-cli" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                <i class="bi bi-cart-check-fill"></i>
            </div>
            <div>
                <div class="stat-lbl-cli">Total Facturado</div>
                <div class="stat-val-cli text-success" style="font-size: 1.4rem;">
                    Bs. {{ number_format($totalCompradoHistorico, 2) }}
                </div>
                <small class="text-muted">compras acumuladas</small>
            </div>
        </div>
    </div>
</div>

<!-- BARRA DE BÚSQUEDA Y FILTROS -->
<div class="cliente-card p-3 mb-4">
    <form action="/clientes" method="GET" class="row g-3 align-items-center">
        <div class="col-md-6">
            <div class="input-group" style="height: 44px;">
                <span class="search-btn-left"><i class="bi bi-search"></i></span>
                <input type="text" name="buscar" class="form-control search-input" placeholder="Buscar por nombre, teléfono o CI/NIT..." value="{{ request('buscar') }}">
            </div>
        </div>
        <div class="col-md-4">
            <select name="estado" class="form-select" style="height: 44px; font-size: 0.95rem;" onchange="this.form.submit()">
                <option value="">Todos los estados de cuenta</option>
                <option value="deuda" {{ request('estado') == 'deuda' ? 'selected' : '' }}>⏳ Con saldo pendiente (Deudores)</option>
                <option value="al_dia" {{ request('estado') == 'al_dia' ? 'selected' : '' }}>✔ Al día (Sin deudas)</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100 fw-bold" style="height: 44px; border-radius: 10px;">
                Filtrar
            </button>
            <a href="/clientes" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="height: 44px; width: 44px; border-radius: 10px;" title="Limpiar filtros">
                <i class="bi bi-arrow-clockwise fs-5"></i>
            </a>
        </div>
    </form>
</div>

<!-- TABLA DE CLIENTES -->
<div class="cliente-card overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-custom align-middle m-0" id="tablaClientes">
            <thead class="table-custom-header">
                <tr>
                    <th class="ps-4" style="width: 32%;">CLIENTE</th>
                    <th style="width: 20%;">CONTACTO</th>
                    <th style="width: 15%;">CI / NIT</th>
                    <th class="text-end" style="width: 13%;">TOTAL COMPRAS</th>
                    <th class="text-center" style="width: 10%;">ESTADO</th>
                    <th class="text-center pe-4" style="width: 10%;">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientes as $cli)
                @php
                    $deuda = $cli->saldo_pendiente_total;
                    $tieneDeuda = $deuda > 0;
                    $inicial = strtoupper(substr($cli->nombre, 0, 1));
                @endphp
                <tr>
                    <!-- Nombre y datos -->
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar-cli me-3">
                                {{ $inicial }}
                            </div>
                            <div>
                                <a href="/clientes/{{ $cli->id }}" class="fw-bold text-dark text-decoration-none d-block" style="font-size: 1rem;">
                                    {{ $cli->nombre }}
                                </a>
                                @if($cli->direccion)
                                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $cli->direccion }}</small>
                                @endif
                            </div>
                        </div>
                    </td>

                    <!-- Contacto / WhatsApp -->
                    <td>
                        @if($cli->telefono)
                            @php
                                $limpioTel = preg_replace('/[^0-9]/', '', $cli->telefono);
                            @endphp
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-dark fs-6">{{ $cli->telefono }}</span>
                                <a href="https://wa.me/591{{ $limpioTel }}" target="_blank" class="btn btn-sm btn-outline-success py-0 px-2 rounded-pill" title="Escribir por WhatsApp">
                                    <i class="bi bi-whatsapp"></i> Chat
                                </a>
                            </div>
                        @else
                            <span class="text-muted small">Sin teléfono</span>
                        @endif
                        @if($cli->email)
                            <div class="small text-muted"><i class="bi bi-envelope me-1"></i>{{ $cli->email }}</div>
                        @endif
                    </td>

                    <!-- CI / NIT -->
                    <td>
                        @if($cli->ci_nit)
                            <span class="badge bg-light text-dark border px-2 py-1 fw-bold fs-6">
                                {{ $cli->ci_nit }}
                            </span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>

                    <!-- Total Compras -->
                    <td class="text-end">
                        <div class="fw-bold text-dark fs-6">
                            Bs. {{ number_format($cli->total_comprado, 2) }}
                        </div>
                        <small class="text-muted">{{ $cli->cantidad_compras }} venta(s)</small>
                    </td>

                    <!-- Estado de Cuenta -->
                    <td class="text-center">
                        @if($tieneDeuda)
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 fw-bold" style="font-size: 0.82rem;">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>Debe: Bs. {{ number_format($deuda, 2) }}
                            </span>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fw-bold" style="font-size: 0.82rem;">
                                <i class="bi bi-check-circle-fill me-1"></i>Al Día
                            </span>
                        @endif
                    </td>

                    <!-- Acciones -->
                    <td class="text-center pe-4">
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            @if($tieneDeuda)
                            <button type="button" class="btn btn-sm btn-success fw-bold px-2 py-1 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAbonarCliente{{ $cli->id }}" title="Cobrar saldo pendiente de Bs. {{ number_format($deuda, 2) }}">
                                <i class="bi bi-cash-stack me-1"></i> Cobrar
                            </button>
                            @endif

                            <a href="/clientes/{{ $cli->id }}" class="btn-action-icon btn-action-view" title="Ver Historial y Estado de Cuenta">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button type="button" class="btn-action-icon btn-action-edit" data-bs-toggle="modal" data-bs-target="#modalEditarCliente{{ $cli->id }}" title="Editar datos">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            @if($tieneDeuda)
                            <button type="button" class="btn-action-icon text-muted border-0 bg-transparent p-0" style="cursor: not-allowed; opacity: 0.35;" title="Bloqueado: No se puede eliminar porque tiene deuda de Bs. {{ number_format($deuda, 2) }}">
                                <i class="bi bi-lock-fill text-danger fs-5"></i>
                            </button>
                            @else
                            <button type="button" class="btn-action-icon btn-action-delete" data-bs-toggle="modal" data-bs-target="#modalEliminarCliente{{ $cli->id }}" title="Eliminar cliente">
                                <i class="bi bi-trash3"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-people fs-1 d-block mb-2 text-muted"></i>
                        <h5>No se encontraron clientes registrados.</h5>
                        <p class="small text-muted">Presiona "+ Nuevo Cliente" para comenzar a registrar clientes frecuentes.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3 bg-light border-top text-muted small d-flex justify-content-between align-items-center">
        <span>Mostrando <strong>{{ count($clientes) }}</strong> clientes registrados</span>
        <span><i class="bi bi-info-circle me-1"></i> Puedes seleccionarlos al instante en el Punto de Venta (POS)</span>
    </div>
</div>

<!-- MODAL: NUEVO CLIENTE -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="/clientes" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-light border-0 py-3">
                <h5 class="fw-bold m-0 text-dark"><i class="bi bi-person-plus-fill me-2 text-primary"></i>Registrar Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label text-dark fw-bold">Nombre Completo / Razón Social *</label>
                    <input type="text" name="nombre" class="form-control form-control-lg fw-bold" placeholder="Ej. Juan Carlos Mendoza" value="{{ request('nuevo_nombre') }}" required>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-dark fw-bold">Teléfono / Celular (WhatsApp)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-whatsapp text-success"></i></span>
                            <input type="text" name="telefono" class="form-control fw-bold" placeholder="Ej. 75123456">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-dark fw-bold">CI / NIT</label>
                        <input type="text" name="ci_nit" class="form-control fw-bold" placeholder="Ej. 8452104 LP">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Dirección / Ubicación</label>
                    <input type="text" name="direccion" class="form-control" placeholder="Ej. Av. Blanco Galindo Km 5">
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Correo Electrónico (Opcional)</label>
                    <input type="email" name="email" class="form-control" placeholder="cliente@correo.com">
                </div>
                <div class="mb-0">
                    <label class="form-label text-muted small fw-bold">Notas o Referencia</label>
                    <textarea name="notas" class="form-control" rows="2" placeholder="Observaciones adicionales, límites de crédito..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-light">
                <button type="button" class="btn btn-light border px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary fw-bold px-5">Guardar Cliente</button>
            </div>
        </form>
    </div>
</div>

<!-- MODALES EDITAR Y ELIMINAR (Por cada cliente) -->
@foreach($clientes as $cli)
    <!-- MODAL EDITAR -->
    <div class="modal fade" id="modalEditarCliente{{ $cli->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="/clientes/{{ $cli->id }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf @method('PUT')
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="fw-bold m-0 text-dark"><i class="bi bi-pencil-square me-2 text-primary"></i>Editar Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">Nombre Completo *</label>
                        <input type="text" name="nombre" class="form-control form-control-lg fw-bold" value="{{ $cli->nombre }}" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-bold">Teléfono / WhatsApp</label>
                            <input type="text" name="telefono" class="form-control fw-bold" value="{{ $cli->telefono }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-bold">CI / NIT</label>
                            <input type="text" name="ci_nit" class="form-control fw-bold" value="{{ $cli->ci_nit }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Dirección</label>
                        <input type="text" name="direccion" class="form-control" value="{{ $cli->direccion }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $cli->email }}">
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted small fw-bold">Notas</label>
                        <textarea name="notas" class="form-control" rows="2">{{ $cli->notas }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-light border px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-5">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL ABONAR / COBRAR DEUDA (SI TIENE SALDO PENDIENTE) -->
    @if($cli->saldo_pendiente_total > 0)
    <div class="modal fade" id="modalAbonarCliente{{ $cli->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="/clientes/{{ $cli->id }}/abono" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                @csrf
                <div class="modal-header bg-success text-white border-0 py-3 px-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-20 p-2 rounded-circle me-3">
                            <i class="bi bi-cash-stack fs-4 text-white"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold m-0 text-white">Registrar Cobro / Abono de Deuda</h5>
                            <small class="text-white-50">Cliente: <strong>{{ $cli->nombre }}</strong></small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4 bg-white">
                    <!-- Resumen de Deuda -->
                    <div class="p-3 bg-light rounded-3 border mb-3 text-center">
                        <span class="text-muted small fw-bold text-uppercase d-block mb-1">Deuda Total Pendiente</span>
                        <h2 class="fw-bold text-danger m-0">Bs. {{ number_format($cli->saldo_pendiente_total, 2) }}</h2>
                    </div>

                    <!-- Si tiene varias notas pendientes -->
                    @php
                        $ventasConDeuda = $cli->ventas->where('saldo_pendiente', '>', 0);
                    @endphp
                    @if($ventasConDeuda->count() > 1)
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold small">Aplicar Cobro A:</label>
                        <select name="id_venta" class="form-select form-select-sm fw-bold">
                            <option value="todas">Todas las notas (Deuda más antigua primero)</option>
                            @foreach($ventasConDeuda as $vd)
                                <option value="{{ $vd->id }}">Nota {{ $vd->numero_nota ?? ('SYS-'.$vd->id) }} (Debe: Bs. {{ number_format($vd->saldo_pendiente, 2) }})</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Monto a Cobrar -->
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">Monto que paga el cliente (Bs.) *</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light fw-bold text-success">Bs.</span>
                            <input type="number" step="0.10" min="0.50" max="{{ $cli->saldo_pendiente_total }}" name="monto_abono" class="form-control fw-bold text-success fs-4" value="{{ number_format($cli->saldo_pendiente_total, 2, '.', '') }}" required>
                        </div>
                        <small class="text-muted">Por defecto se liquida el saldo total. Puedes ingresar un monto menor si es abono parcial.</small>
                    </div>

                    <!-- Método de Pago del Cobro -->
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">Método de Cobro *</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="metodo_pago" id="metodoCobroEf_{{ $cli->id }}" value="Efectivo" checked>
                                <label class="btn btn-outline-success w-100 fw-bold py-2 d-flex align-items-center justify-content-center" for="metodoCobroEf_{{ $cli->id }}">
                                    <i class="bi bi-cash me-2 fs-5"></i> Efectivo
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="metodo_pago" id="metodoCobroQr_{{ $cli->id }}" value="QR">
                                <label class="btn btn-outline-primary w-100 fw-bold py-2 d-flex align-items-center justify-content-center" for="metodoCobroQr_{{ $cli->id }}">
                                    <i class="bi bi-qr-code me-2 fs-5"></i> QR Simple
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Observación -->
                    <div class="mb-0">
                        <label class="form-label text-muted small fw-bold">Observación (Opcional)</label>
                        <input type="text" name="observacion" class="form-control" placeholder="Ej. Pago en caja, liquidación de saldo...">
                    </div>
                </div>

                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-light border px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-bold px-4 shadow-sm">
                        <i class="bi bi-check-circle-fill me-1"></i> Confirmar Cobro
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL ELIMINAR (SOLO SI NO TIENE DEUDA) -->
    @if($cli->saldo_pendiente_total == 0)
    <div class="modal fade" id="modalEliminarCliente{{ $cli->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-body p-4 text-center">
                    <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle d-inline-flex mb-3">
                        <i class="bi bi-trash3 fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Eliminar Cliente</h5>
                    <p class="text-muted small mb-4">¿Eliminar a <strong>{{ $cli->nombre }}</strong> del registro de clientes?</p>
                    <form action="/clientes/{{ $cli->id }}" method="POST">
                        @csrf @method('DELETE')
                        <div class="d-flex justify-content-between gap-2">
                            <button type="button" class="btn btn-light border w-50 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger w-50 fw-bold">Eliminar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach

@if(request('nuevo_nombre'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    const el = document.getElementById('modalNuevoCliente');
    if (el) {
        const modal = new bootstrap.Modal(el);
        modal.show();
    }
});
</script>
@endif

@endsection
