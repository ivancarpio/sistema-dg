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

    /* ===== RESPONSIVE MÓVIL — Clientes ===== */
    @media (max-width: 767.98px) {
        .header-clientes {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
        }
        .header-cli-left h3 {
            font-size: 1.1rem !important;
        }
        .header-cli-left small {
            font-size: 0.72rem !important;
        }
        .header-cli-icon {
            width: 36px !important;
            height: 36px !important;
            padding: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 8px !important;
            margin-right: 8px !important;
        }
        .header-cli-icon i {
            font-size: 1.25rem !important;
        }
        .header-cli-actions {
            width: 100% !important;
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 8px !important;
        }
        .header-cli-actions .btn {
            width: 100% !important;
            padding: 8px 6px !important;
            font-size: 0.78rem !important;
            border-radius: 10px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center !important;
            white-space: nowrap !important;
        }

        /* Stat cards en vertical para que nunca se corten los montos ni títulos */
        .stat-card-cli {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 4px !important;
            padding: 10px 10px !important;
            min-height: 94px !important;
            border-radius: 12px !important;
        }
        .stat-icon-cli {
            width: 28px !important;
            height: 28px !important;
            font-size: 0.85rem !important;
            border-radius: 7px !important;
            margin-bottom: 2px !important;
        }
        .stat-lbl-cli {
            font-size: 0.64rem !important;
            font-weight: 800 !important;
            letter-spacing: 0.2px !important;
            line-height: 1.2 !important;
            margin-bottom: 1px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            width: 100% !important;
        }
        .stat-val-cli {
            font-size: 1.05rem !important;
            font-weight: 800 !important;
            letter-spacing: -0.3px !important;
            line-height: 1.15 !important;
            width: 100% !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        .stat-card-cli small {
            font-size: 0.65rem !important;
            margin-top: 1px !important;
            display: block !important;
        }

        /* Buscador de clientes */
        .cliente-card.p-3.mb-4 {
            padding: 10px 12px !important;
        }
        .cliente-card.p-3.mb-4 .input-group,
        .cliente-card.p-3.mb-4 select,
        .cliente-card.p-3.mb-4 .btn {
            height: 36px !important;
            font-size: 0.82rem !important;
        }
        .search-btn-left {
            padding: 0 10px !important;
        }

        /* Tabla de clientes compacta */
        .table-custom-header th {
            padding: 7px 6px !important;
            font-size: 0.65rem !important;
            letter-spacing: 0.3px !important;
        }
        .table-custom td {
            padding: 6px 6px !important;
            font-size: 0.78rem !important;
        }
        .avatar-cli {
            width: 26px !important;
            height: 26px !important;
            font-size: 0.75rem !important;
            border-radius: 6px !important;
            margin-right: 6px !important;
        }
        .table-custom td a.fw-bold {
            font-size: 0.80rem !important;
            line-height: 1.25 !important;
        }
        .table-custom td small {
            font-size: 0.68rem !important;
        }
        .table-custom td .badge {
            font-size: 0.70rem !important;
            padding: 2px 6px !important;
        }
        .table-custom td .fs-6 {
            font-size: 0.80rem !important;
        }
        .btn-action-icon {
            width: 28px !important;
            height: 28px !important;
            font-size: 0.78rem !important;
        }
    }
</style>

<!-- ENCABEZADO -->
<div class="d-flex justify-content-between align-items-center mb-4 header-clientes">
    <div class="d-flex align-items-center header-cli-left">
        <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3 text-primary header-cli-icon">
            <i class="bi bi-person-lines-fill fs-2"></i>
        </div>
        <div>
            <h3 class="fw-bold m-0 text-dark">Clientes Frecuentes</h3>
            <small class="text-muted">Registro de clientes regulares • Datos de contacto y control de deudas</small>
        </div>
    </div>
    
    <div class="d-flex gap-2 header-cli-actions">
        <button class="btn btn-outline-primary fw-bold shadow-sm px-3 py-2" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#modalLogGlobalCobros">
            <i class="bi bi-clock-history me-1"></i> <span>Auditoría<span class="d-none d-sm-inline"> / Log</span></span>
        </button>
        <button class="btn btn-primary fw-bold shadow-sm px-4 py-2" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
            <i class="bi bi-person-plus-fill me-1"></i> <span>+ Nuevo Cliente</span>
        </button>
    </div>
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
                    {{ $monedaSimbolo }} {{ number_format($totalDeudaPorCobrar, 2) }}
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
                    {{ $monedaSimbolo }} {{ number_format($totalCompradoHistorico, 2) }}
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
                                $prefijoWa = ($paisOperacion ?? 'BO') === 'PE' ? '51' : '591';
                                $waTel = strlen($limpioTel) > 9 ? $limpioTel : $prefijoWa . $limpioTel;
                            @endphp
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-dark fs-6">{{ $cli->telefono }}</span>
                                <a href="https://wa.me/{{ $waTel }}" target="_blank" class="btn btn-sm btn-outline-success py-0 px-2 rounded-pill" title="Escribir por WhatsApp">
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
                            {{ $monedaSimbolo }} {{ number_format($cli->total_comprado, 2) }}
                        </div>
                        <small class="text-muted">{{ $cli->cantidad_compras }} venta(s)</small>
                    </td>

                    <!-- Estado de Cuenta -->
                    <td class="text-center">
                        @if($tieneDeuda)
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 fw-bold" style="font-size: 0.82rem;">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>Debe: {{ $monedaSimbolo }} {{ number_format($deuda, 2) }}
                            </span>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fw-bold" style="font-size: 0.82rem;">
                                <i class="bi bi-check-circle-fill me-1"></i>Al Día
                            </span>
                        @endif

                        @php
                            $ultimoCobroCli = $cli->pagos->first();
                        @endphp

                        @if($ultimoCobroCli)
                            <div class="mt-1 d-flex flex-column align-items-center">
                                <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1 px-2 py-1 shadow-xs" style="font-size: 0.72rem; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalLogCobros{{ $cli->id }}" title="Click para ver el detalle del cobro registrado por {{ $ultimoCobroCli->usuario->name ?? 'Usuario' }}">
                                    <i class="bi bi-person-check-fill text-success"></i>
                                    <span>Cobró: <strong class="text-primary">{{ Str::words($ultimoCobroCli->usuario->name ?? 'Sistema', 2, '') }}</strong></span>
                                </span>
                                <span class="text-muted mt-0.5" style="font-size: 0.68rem;">
                                    {{ \Carbon\Carbon::parse($ultimoCobroCli->fecha)->format('d/m/Y H:i') }} &bull; <strong class="text-success">{{ $monedaSimbolo }} {{ number_format($ultimoCobroCli->monto, 2) }}</strong>
                                </span>
                            </div>
                        @endif

                        @if(($cli->saldo_favor ?? 0) > 0)
                            <div class="mt-1">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1 fw-bold" style="font-size: 0.74rem;">
                                    <i class="bi bi-wallet2 me-1"></i>A Favor: {{ $monedaSimbolo }} {{ number_format($cli->saldo_favor, 2) }}
                                </span>
                            </div>
                        @endif
                    </td>

                    <!-- Acciones -->
                    <td class="text-center pe-4">
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            @if($tieneDeuda)
                            <button type="button" class="btn btn-sm btn-success fw-bold px-2 py-1 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAbonarCliente{{ $cli->id }}" title="Cobrar saldo pendiente de {{ $monedaSimbolo }} {{ number_format($deuda, 2) }}">
                                <i class="bi bi-cash-stack me-1"></i> Cobrar
                            </button>
                            @endif

                            <button type="button" class="btn-action-icon" style="color: #0284c7; background: #e0f2fe; border: 1px solid #bae6fd;" data-bs-toggle="modal" data-bs-target="#modalLogCobros{{ $cli->id }}" title="Ver Log y Auditoría de Cobros de {{ $cli->nombre }} ({{ $cli->pagos->count() }} pagos)">
                                <i class="bi bi-clock-history"></i>
                            </button>

                            <a href="/clientes/{{ $cli->id }}" class="btn-action-icon btn-action-view" title="Ver Ficha y Estado de Cuenta">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button type="button" class="btn-action-icon btn-action-edit" data-bs-toggle="modal" data-bs-target="#modalEditarCliente{{ $cli->id }}" title="Editar datos">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            @if($tieneDeuda)
                            <button type="button" class="btn-action-icon text-muted border-0 bg-transparent p-0" style="cursor: not-allowed; opacity: 0.35;" title="Bloqueado: No se puede eliminar porque tiene deuda de {{ $monedaSimbolo }} {{ number_format($deuda, 2) }}">
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
                        <h2 class="fw-bold text-danger m-0">{{ $monedaSimbolo }} {{ number_format($cli->saldo_pendiente_total, 2) }}</h2>
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
                                <option value="{{ $vd->id }}">Nota {{ $vd->numero_nota ?? ('SYS-'.$vd->id) }} (Debe: {{ $monedaSimbolo }} {{ number_format($vd->saldo_pendiente, 2) }})</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Monto a Cobrar -->
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">Monto que paga el cliente ({{ $monedaSimbolo }}) *</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light fw-bold text-success">{{ $monedaSimbolo }}</span>
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

    <!-- MODAL LOG / AUDITORÍA DE COBROS DEL CLIENTE -->
    <div class="modal fade" id="modalLogCobros{{ $cli->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-light border-bottom p-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-circle">
                            <i class="bi bi-clock-history fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold m-0 text-dark">Log de Cobros — {{ $cli->nombre }}</h5>
                            <small class="text-muted">Registro de auditoría de cobros y abonos realizados a este cliente</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-2 mb-3">
                        <div class="col-6 col-md-4">
                            <div class="p-2.5 rounded-3 bg-light border text-center">
                                <small class="text-muted d-block" style="font-size:0.74rem;">Total Cobrado en Abonos</small>
                                <span class="fw-bold text-success fs-5">{{ $monedaSimbolo }} {{ number_format($cli->pagos->sum('monto'), 2) }}</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="p-2.5 rounded-3 bg-light border text-center">
                                <small class="text-muted d-block" style="font-size:0.74rem;">Cobros Registrados</small>
                                <span class="fw-bold text-dark fs-5">{{ $cli->pagos->count() }} pago(s)</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="p-2.5 rounded-3 bg-light border text-center">
                                <small class="text-muted d-block" style="font-size:0.74rem;">Estado Actual</small>
                                <span class="fw-bold {{ $cli->saldo_pendiente_total > 0 ? 'text-danger' : 'text-success' }} fs-5">
                                    {{ $cli->saldo_pendiente_total > 0 ? 'Debe: ' . $monedaSimbolo . ' ' . number_format($cli->saldo_pendiente_total, 2) : '✔ Al Día (' . $monedaSimbolo . ' 0.00)' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive rounded-3 border">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.86rem;">
                            <thead class="bg-light text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">
                                <tr>
                                    <th class="ps-3 py-2.5">Fecha y Hora</th>
                                    <th>Nº Nota</th>
                                    <th class="text-end">Monto Cobrado</th>
                                    <th>Método</th>
                                    <th>Cajero Responsable</th>
                                    <th class="pe-3">Observación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cli->pagos as $pago)
                                <tr>
                                    <td class="ps-3 fw-semibold text-dark" style="white-space: nowrap;">
                                        <i class="bi bi-calendar-event me-1 text-muted"></i>{{ \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border fw-bold">
                                            {{ $pago->venta->numero_nota ?? ('SYS-' . ($pago->id_venta ?? 'N/A')) }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-success fs-6" style="white-space: nowrap;">
                                        {{ $monedaSimbolo }} {{ number_format($pago->monto, 2) }}
                                    </td>
                                    <td>
                                        @if($pago->metodo_pago === 'QR')
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1 fw-bold"><i class="bi bi-qr-code me-1"></i>QR</span>
                                        @else
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1 fw-bold"><i class="bi bi-cash me-1"></i>Efectivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1.5">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:24px; height:24px; font-size:0.7rem; flex-shrink: 0;">
                                                {{ strtoupper(substr($pago->usuario->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <span class="fw-bold text-dark d-block" style="line-height: 1.1;">{{ $pago->usuario->name ?? 'Sistema' }}</span>
                                                <small class="text-muted" style="font-size:0.70rem;">{{ $pago->usuario->rol ?? 'Cajero' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="pe-3 text-muted small">
                                        {{ $pago->observacion ?: 'Cobro de saldo pendiente' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-info-circle fs-3 d-block mb-1 text-secondary opacity-50"></i>
                                        No se han registrado cobros o abonos posteriores para este cliente aún.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0 p-3">
                    <a href="/clientes/{{ $cli->id }}" class="btn btn-outline-primary btn-sm fw-bold">
                        <i class="bi bi-eye me-1"></i> Ver Ficha Completa del Cliente
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm fw-bold px-3" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- MODAL AUDITORÍA / LOG GLOBAL DE COBROS -->
<div class="modal fade" id="modalLogGlobalCobros" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom p-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
                        <i class="bi bi-journal-check fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold m-0 text-dark">Auditoría / Log Global de Cobros</h5>
                        <small class="text-muted">Historial completo de pagos y cobranzas con filtros en tiempo real</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4 bg-light bg-opacity-50">
                {{-- KPI Cards del Modal --}}
                <div class="row g-2 mb-3">
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm p-3 rounded-3 bg-white h-100">
                            <div class="text-muted small fw-bold text-uppercase" style="font-size:0.70rem;">Total Cobrado</div>
                            <div class="fw-bold fs-5 text-dark" id="logCobrosTotal">{{ $monedaSimbolo }} {{ number_format($ultimosCobrosGlobales->sum('monto'), 2) }}</div>
                            <small class="text-muted" id="logCobrosCantidad">{{ $ultimosCobrosGlobales->count() }} cobros</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm p-3 rounded-3 bg-white h-100 border-start border-success border-4">
                            <div class="text-success small fw-bold text-uppercase" style="font-size:0.70rem;"><i class="bi bi-cash me-1"></i>En Efectivo</div>
                            <div class="fw-bold fs-5 text-success" id="logCobrosEfectivo">{{ $monedaSimbolo }} {{ number_format($ultimosCobrosGlobales->where('metodo_pago', 'Efectivo')->sum('monto'), 2) }}</div>
                            <small class="text-muted" id="logCobrosCantEfectivo">{{ $ultimosCobrosGlobales->where('metodo_pago', 'Efectivo')->count() }} pagos</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm p-3 rounded-3 bg-white h-100 border-start border-primary border-4">
                            <div class="text-primary small fw-bold text-uppercase" style="font-size:0.70rem;"><i class="bi bi-qr-code me-1"></i>Por QR</div>
                            <div class="fw-bold fs-5 text-primary" id="logCobrosQr">{{ $monedaSimbolo }} {{ number_format($ultimosCobrosGlobales->where('metodo_pago', 'QR')->sum('monto'), 2) }}</div>
                            <small class="text-muted" id="logCobrosCantQr">{{ $ultimosCobrosGlobales->where('metodo_pago', 'QR')->count() }} pagos</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm p-3 rounded-3 bg-white h-100 d-flex justify-content-center">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-muted small fw-bold text-uppercase" style="font-size:0.70rem;">Filtrando por</div>
                                    <div class="fw-bold text-dark small" id="logCobrosFiltroActivo" style="line-height:1.2;">Todos los registros</div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarFiltrosLogCobros()" title="Restablecer filtros">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Barra de Filtros interactiva --}}
                <div class="card border-0 shadow-sm p-3 rounded-3 bg-white mb-3">
                    <div class="row g-2 align-items-center">
                        {{-- Períodos rápidos --}}
                        <div class="col-12 col-lg-auto d-flex gap-1 flex-wrap">
                            <button type="button" class="btn btn-sm btn-periodo-log active btn-primary fw-bold" data-periodo="todos" onclick="setPeriodoLog('todos', this)">Todos</button>
                            <button type="button" class="btn btn-sm btn-periodo-log btn-light border fw-semibold" data-periodo="hoy" onclick="setPeriodoLog('hoy', this)">Hoy</button>
                            <button type="button" class="btn btn-sm btn-periodo-log btn-light border fw-semibold" data-periodo="semana" onclick="setPeriodoLog('semana', this)">Esta Semana</button>
                            <button type="button" class="btn btn-sm btn-periodo-log btn-light border fw-semibold" data-periodo="mes" onclick="setPeriodoLog('mes', this)">Este Mes</button>
                        </div>

                        {{-- Selector Método --}}
                        <div class="col-6 col-md-3 col-lg-2">
                            <select id="logFiltroMetodo" class="form-select form-select-sm bg-light" onchange="filtrarTablaCobros()">
                                <option value="">Todos los métodos</option>
                                <option value="efectivo">💵 Solo Efectivo</option>
                                <option value="qr">📱 Solo QR</option>
                            </select>
                        </div>

                        {{-- Selector Tipo de Cobro (Abono de Crédito vs Venta Contado POS) --}}
                        <div class="col-6 col-md-3 col-lg-2">
                            <select id="logFiltroTipo" class="form-select form-select-sm bg-light" onchange="filtrarTablaCobros()">
                                <option value="">Todos los orígenes</option>
                                <option value="credito">📑 Abonos Crédito (Deudas)</option>
                                <option value="contado">🛒 Ventas Contado POS</option>
                            </select>
                        </div>

                        {{-- Rango de fechas manual --}}
                        <div class="col-6 col-md-4 col-lg-2.5 d-flex gap-1 align-items-center">
                            <input type="date" id="logFechaDesde" class="form-control form-control-sm bg-light" placeholder="Desde" onchange="aplicarFechaManualLog()">
                            <span class="text-muted small">-</span>
                            <input type="date" id="logFechaHasta" class="form-control form-control-sm bg-light" placeholder="Hasta" onchange="aplicarFechaManualLog()">
                        </div>

                        {{-- Buscador --}}
                        <div class="col-12 col-md-5 col-lg-auto flex-grow-1">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="logBuscador" class="form-control border-start-0 bg-light" placeholder="Buscar cliente, nota o cajero..." onkeyup="filtrarTablaCobros()">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabla de Cobros --}}
                <div class="table-responsive rounded-3 border bg-white shadow-sm" style="max-height: 440px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0" id="tablaLogCobros" style="font-size: 0.86rem;">
                        <thead class="bg-light text-secondary text-uppercase fw-bold sticky-top" style="font-size: 0.74rem; z-index: 2;">
                            <tr>
                                <th class="ps-3 py-2.5">Fecha y Hora</th>
                                <th>Cliente</th>
                                <th>Nº Nota</th>
                                <th class="text-end">Monto Cobrado</th>
                                <th class="text-center">Método</th>
                                <th>Cajero</th>
                                <th class="pe-3">Observación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimosCobrosGlobales as $cg)
                            @php
                                $esCredito = ($cg->venta && $cg->venta->metodo_pago === 'Por Pagar');
                            @endphp
                            <tr class="fila-cobro"
                                data-fecha="{{ \Carbon\Carbon::parse($cg->fecha)->format('Y-m-d') }}"
                                data-metodo="{{ strtolower($cg->metodo_pago) }}"
                                data-tipo="{{ $esCredito ? 'credito' : 'contado' }}"
                                data-monto="{{ floatval($cg->monto) }}"
                                data-texto="{{ strtolower(($cg->cliente->nombre ?? '') . ' ' . ($cg->venta->numero_nota ?? ('SYS-'.$cg->id_venta)) . ' ' . ($cg->usuario->name ?? '') . ' ' . ($cg->observacion ?? '')) }}">
                                <td class="ps-3 fw-semibold text-dark" style="white-space: nowrap;">
                                    <i class="bi bi-calendar2-check me-1 text-primary"></i>
                                    {{ \Carbon\Carbon::parse($cg->fecha)->format('d/m/Y H:i') }}
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $cg->cliente->nombre ?? 'Cliente Varios' }}</div>
                                    @if(optional($cg->cliente)->telefono)
                                        <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $cg->cliente->telefono }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border fw-bold">
                                        {{ $cg->venta->numero_nota ?? ('SYS-' . ($cg->id_venta ?? 'N/A')) }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-success fs-6" style="white-space: nowrap;">
                                    {{ $monedaSimbolo }} {{ number_format($cg->monto, 2) }}
                                </td>
                                <td class="text-center">
                                    @if(strtolower($cg->metodo_pago) === 'qr')
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1 fw-bold"><i class="bi bi-qr-code me-1"></i>QR</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1 fw-bold"><i class="bi bi-cash me-1"></i>Efectivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:24px; height:24px; font-size:0.70rem; flex-shrink: 0;">
                                            {{ strtoupper(substr($cg->usuario->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block" style="line-height: 1.1; font-size:0.80rem;">{{ $cg->usuario->name ?? 'Sistema' }}</span>
                                            <small class="text-muted" style="font-size: 0.68rem;">{{ $cg->usuario->rol ?? 'Cajero' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="pe-3 small">
                                    @if($esCredito)
                                        <span class="badge bg-warning bg-opacity-25 text-dark border border-warning-subtle py-1 px-1.5 me-1" style="font-size:0.70rem;">
                                            <i class="bi bi-clock-history me-1"></i>Abono Crédito
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-25 text-dark border border-secondary-subtle py-1 px-1.5 me-1" style="font-size:0.70rem;">
                                            <i class="bi bi-cart-check me-1"></i>Venta Contado POS
                                        </span>
                                    @endif
                                    <span class="text-muted">{{ $cg->observacion ?: 'Cobro de saldo pendiente' }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr id="filaSinCobrosInicial">
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-journal-x fs-1 d-block mb-2 opacity-25"></i>
                                    <p class="fw-bold mb-1">No se han registrado cobros todavía</p>
                                    <small>Los cobros y abonos realizados se listarán aquí en tiempo real.</small>
                                </td>
                            </tr>
                            @endforelse
                            <tr id="filaSinResultadosFiltro" style="display:none;">
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-funnel fs-1 d-block mb-2 opacity-25"></i>
                                    <p class="fw-bold mb-1">No se encontraron cobros para el filtro seleccionado</p>
                                    <small>Prueba ajustando el rango de fechas, el método o el buscador.</small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer bg-light border-top p-2.5 d-flex justify-content-between">
                <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Los cobros quedan registrados con fecha exacta y cajero responsable para auditoría interna.</small>
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
let periodoLogSeleccionado = 'todos';

function setPeriodoLog(periodo, btn) {
    periodoLogSeleccionado = periodo;
    document.querySelectorAll('.btn-periodo-log').forEach(b => {
        b.classList.remove('active', 'btn-primary');
        b.classList.add('btn-light');
    });
    btn.classList.add('active', 'btn-primary');
    btn.classList.remove('btn-light');
    
    document.getElementById('logFechaDesde').value = '';
    document.getElementById('logFechaHasta').value = '';

    filtrarTablaCobros();
}

function aplicarFechaManualLog() {
    periodoLogSeleccionado = 'manual';
    document.querySelectorAll('.btn-periodo-log').forEach(b => {
        b.classList.remove('active', 'btn-primary');
        b.classList.add('btn-light');
    });
    filtrarTablaCobros();
}

function limpiarFiltrosLogCobros() {
    periodoLogSeleccionado = 'todos';
    document.querySelectorAll('.btn-periodo-log').forEach((b, i) => {
        if (i === 0) {
            b.classList.add('active', 'btn-primary');
            b.classList.remove('btn-light');
        } else {
            b.classList.remove('active', 'btn-primary');
            b.classList.add('btn-light');
        }
    });
    document.getElementById('logFiltroMetodo').value = '';
    document.getElementById('logFiltroTipo').value = '';
    document.getElementById('logFechaDesde').value = '';
    document.getElementById('logFechaHasta').value = '';
    document.getElementById('logBuscador').value = '';
    filtrarTablaCobros();
}

function filtrarTablaCobros() {
    const metodo = document.getElementById('logFiltroMetodo').value.toLowerCase();
    const tipo = document.getElementById('logFiltroTipo') ? document.getElementById('logFiltroTipo').value.toLowerCase() : '';
    const buscar = document.getElementById('logBuscador').value.toLowerCase().trim();
    const fDesde = document.getElementById('logFechaDesde').value;
    const fHasta = document.getElementById('logFechaHasta').value;

    const filas = document.querySelectorAll('.fila-cobro');
    const hoy = new Date();
    
    let fechaInicioPeriodo = null;
    let fechaFinPeriodo = null;
    
    if (periodoLogSeleccionado === 'hoy') {
        const y = hoy.getFullYear();
        const m = String(hoy.getMonth() + 1).padStart(2, '0');
        const d = String(hoy.getDate()).padStart(2, '0');
        fechaInicioPeriodo = `${y}-${m}-${d}`;
        fechaFinPeriodo = fechaInicioPeriodo;
    } else if (periodoLogSeleccionado === 'semana') {
        const diaSemana = hoy.getDay() || 7;
        const lunes = new Date(hoy);
        lunes.setDate(hoy.getDate() - diaSemana + 1);
        const y = lunes.getFullYear();
        const m = String(lunes.getMonth() + 1).padStart(2, '0');
        const d = String(lunes.getDate()).padStart(2, '0');
        fechaInicioPeriodo = `${y}-${m}-${d}`;
        
        const yH = hoy.getFullYear();
        const mH = String(hoy.getMonth() + 1).padStart(2, '0');
        const dH = String(hoy.getDate()).padStart(2, '0');
        fechaFinPeriodo = `${yH}-${mH}-${dH}`;
    } else if (periodoLogSeleccionado === 'mes') {
        const año = hoy.getFullYear();
        const mes = String(hoy.getMonth() + 1).padStart(2, '0');
        fechaInicioPeriodo = `${año}-${mes}-01`;
        const ultimoDia = new Date(año, hoy.getMonth() + 1, 0).getDate();
        fechaFinPeriodo = `${año}-${mes}-${ultimoDia}`;
    } else if (periodoLogSeleccionado === 'manual') {
        fechaInicioPeriodo = fDesde || null;
        fechaFinPeriodo = fHasta || null;
    }

    let sumaTotal = 0;
    let sumaEf = 0;
    let sumaQr = 0;
    let vis = 0;
    let visEf = 0;
    let visQr = 0;

    filas.forEach(fila => {
        const filaFecha = fila.getAttribute('data-fecha');
        const filaMetodo = fila.getAttribute('data-metodo');
        const filaTipo = fila.getAttribute('data-tipo');
        const filaMonto = parseFloat(fila.getAttribute('data-monto')) || 0;
        const filaTexto = fila.getAttribute('data-texto') || '';

        let coincideFecha = true;
        if (fechaInicioPeriodo && filaFecha < fechaInicioPeriodo) coincideFecha = false;
        if (fechaFinPeriodo && filaFecha > fechaFinPeriodo) coincideFecha = false;

        let coincideMetodo = true;
        if (metodo && filaMetodo !== metodo) coincideMetodo = false;

        let coincideTipo = true;
        if (tipo && filaTipo !== tipo) coincideTipo = false;

        let coincideTexto = true;
        if (buscar && !filaTexto.includes(buscar)) coincideTexto = false;

        if (coincideFecha && coincideMetodo && coincideTipo && coincideTexto) {
            fila.style.display = '';
            sumaTotal += filaMonto;
            vis++;
            if (filaMetodo === 'qr') {
                sumaQr += filaMonto;
                visQr++;
            } else {
                sumaEf += filaMonto;
                visEf++;
            }
        } else {
            fila.style.display = 'none';
        }
    });

    const simMonCli = '{{ $monedaSimbolo ?? "Bs." }} ';
    document.getElementById('logCobrosTotal').textContent = simMonCli + sumaTotal.toLocaleString('es-BO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('logCobrosEfectivo').textContent = simMonCli + sumaEf.toLocaleString('es-BO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('logCobrosQr').textContent = simMonCli + sumaQr.toLocaleString('es-BO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('logCobrosCantidad').textContent = vis + ' cobros';
    document.getElementById('logCobrosCantEfectivo').textContent = visEf + ' pagos';
    document.getElementById('logCobrosCantQr').textContent = visQr + ' pagos';

    let textoFiltro = 'Todos los registros';
    if (periodoLogSeleccionado === 'hoy') textoFiltro = 'Cobros de Hoy';
    else if (periodoLogSeleccionado === 'semana') textoFiltro = 'Cobros de Esta Semana';
    else if (periodoLogSeleccionado === 'mes') textoFiltro = 'Cobros de Este Mes';
    else if (periodoLogSeleccionado === 'manual' && (fDesde || fHasta)) textoFiltro = 'Rango personalizado';
    if (tipo) textoFiltro += ' • ' + (tipo === 'credito' ? 'Abonos Crédito' : 'Ventas Contado POS');
    if (metodo) textoFiltro += ' (' + (metodo === 'qr' ? 'Solo QR' : 'Solo Efectivo') + ')';
    if (buscar) textoFiltro += ' • "' + buscar + '"';
    document.getElementById('logCobrosFiltroActivo').textContent = textoFiltro;

    const filaSinResultados = document.getElementById('filaSinResultadosFiltro');
    if (filaSinResultados) {
        filaSinResultados.style.display = (vis === 0 && filas.length > 0) ? '' : 'none';
    }
}
</script>

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
