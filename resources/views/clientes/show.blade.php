@extends('layouts.app')

@section('content')

<style>
    .cliente-card {
        border: none !important;
        border-radius: 14px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important;
        background: #ffffff;
    }
    .badge-efectivo { background: #dcfce7; color: #16a34a; font-weight: 700; border-radius: 20px; padding: 4px 12px; font-size: 0.78rem; }
    .badge-qr       { background: #dbeafe; color: #2563eb; font-weight: 700; border-radius: 20px; padding: 4px 12px; font-size: 0.78rem; }
    .badge-por-pagar{ background: #fef3c7; color: #b45309; font-weight: 700; border-radius: 20px; padding: 4px 12px; font-size: 0.78rem; border: 1px solid #fde68a; }

    .avatar-large {
        width: 70px;
        height: 70px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 2rem;
        color: #0284c7;
        background: #e0f2fe;
        flex-shrink: 0;
    }

    .modal-content-clean {
        background-color: #ffffff !important;
        border: none !important;
        border-radius: 20px !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        overflow: hidden;
    }
    .payment-option-btn {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 14px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: #fff;
    }
    .payment-option-btn:hover {
        border-color: #0284c7;
        background: #f0f9ff;
    }
    .payment-option-btn.active {
        border-color: #0284c7;
        background: #e0f2fe;
        color: #0369a1;
        font-weight: 700;
    }
</style>

<!-- ALERTAS -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 rounded-3 shadow-sm" role="alert">
    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
    <div class="fw-semibold">{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error') || (isset($errors) && $errors->any()))
<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4 rounded-3 shadow-sm" role="alert">
    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
    <div class="fw-semibold">
        {{ session('error') ?? ($errors->first() ?? 'Ha ocurrido un error.') }}
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- ENCABEZADO -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
        <a href="/clientes" class="btn btn-outline-secondary d-flex align-items-center" style="border-radius: 10px;">
            <i class="bi bi-arrow-left me-1"></i> Volver a Clientes
        </a>
        <div>
            <h3 class="fw-bold m-0 text-dark">Ficha del Cliente</h3>
            <small class="text-muted">Detalle de compras, deudas y control de pagos</small>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        @if($cliente->saldo_pendiente_total > 0)
        <button type="button" class="btn btn-success fw-bold px-3 py-2 shadow-sm" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#modalAbonarGeneral">
            <i class="bi bi-cash-stack me-1"></i> Cobrar Deuda (Bs. {{ number_format($cliente->saldo_pendiente_total, 2) }})
        </button>
        @endif

        @if($cliente->telefono)
            @php $limpioTel = preg_replace('/[^0-9]/', '', $cliente->telefono); @endphp
            <a href="https://wa.me/591{{ $limpioTel }}" target="_blank" class="btn btn-outline-success fw-bold px-3 py-2" style="border-radius: 10px;">
                <i class="bi bi-whatsapp me-1"></i> WhatsApp
            </a>
        @endif
    </div>
</div>

<!-- TARJETA PRINCIPAL DEL CLIENTE -->
<div class="cliente-card p-4 mb-4">
    <div class="row g-4 align-items-center">
        <div class="col-lg-6 d-flex align-items-start">
            <div class="avatar-large me-3">
                {{ strtoupper(substr($cliente->nombre, 0, 1)) }}
            </div>
            <div>
                <h4 class="fw-bold text-dark m-0">{{ $cliente->nombre }}</h4>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    @if($cliente->ci_nit)
                        <span class="badge bg-light text-dark border"><i class="bi bi-card-heading me-1"></i>CI/NIT: {{ $cliente->ci_nit }}</span>
                    @endif
                    @if($cliente->telefono)
                        <span class="badge bg-light text-dark border"><i class="bi bi-telephone me-1"></i>{{ $cliente->telefono }}</span>
                    @endif
                    @if($cliente->email)
                        <span class="badge bg-light text-dark border"><i class="bi bi-envelope me-1"></i>{{ $cliente->email }}</span>
                    @endif
                </div>
                @if($cliente->direccion)
                    <div class="text-muted small mt-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $cliente->direccion }}</div>
                @endif
                @if($cliente->notas)
                    <div class="text-muted small mt-1 fst-italic"><i class="bi bi-sticky me-1"></i>Nota: {{ $cliente->notas }}</div>
                @endif
                <div class="text-muted small mt-2">
                    <i class="bi bi-calendar-check me-1"></i>Cliente registrado el: <strong>{{ $cliente->created_at ? $cliente->created_at->format('d/m/Y') : '—' }}</strong>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="p-3 rounded-3 bg-light text-center border h-100 d-flex flex-column justify-content-center">
                        <div class="text-muted small fw-bold text-uppercase">Total Compras</div>
                        <div class="fs-4 fw-bold text-dark mt-1">Bs. {{ number_format($cliente->total_comprado, 2) }}</div>
                        <small class="text-muted">{{ $cliente->cantidad_compras }} venta(s) registradas</small>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-3 rounded-3 text-center border h-100 d-flex flex-column justify-content-between {{ $cliente->saldo_pendiente_total > 0 ? 'bg-danger bg-opacity-10 border-danger' : 'bg-success bg-opacity-10 border-success' }}">
                        <div>
                            <div class="small fw-bold text-uppercase {{ $cliente->saldo_pendiente_total > 0 ? 'text-danger' : 'text-success' }}">
                                {{ $cliente->saldo_pendiente_total > 0 ? 'Saldo Pendiente por Cobrar' : 'Estado de Cuenta' }}
                            </div>
                            <div class="fs-4 fw-bold mt-1 {{ $cliente->saldo_pendiente_total > 0 ? 'text-danger' : 'text-success' }}">
                                {{ $cliente->saldo_pendiente_total > 0 ? 'Bs. ' . number_format($cliente->saldo_pendiente_total, 2) : 'Al Día (Bs. 0.00)' }}
                            </div>
                            <small class="text-muted d-block mt-1">
                                {{ $cliente->saldo_pendiente_total > 0 ? 'Tiene cuentas por pagar pendientes' : 'Sin deudas pendientes en el sistema' }}
                            </small>
                        </div>
                        @if($cliente->saldo_pendiente_total > 0)
                        <button type="button" class="btn btn-sm btn-success fw-bold mt-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAbonarGeneral">
                            <i class="bi bi-cash-stack me-1"></i> Cobrar Ahora
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- NAVEGACIÓN DE PESTAÑAS: VENTAS VS HISTORIAL DE PAGOS -->
<ul class="nav nav-pills mb-3 gap-2" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold px-4 py-2" id="pills-ventas-tab" data-bs-toggle="pill" data-bs-target="#pills-ventas" type="button" role="tab" style="border-radius: 10px;">
            <i class="bi bi-receipt-cutoff me-1"></i> Notas de Venta ({{ $cliente->ventas->count() }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold px-4 py-2" id="pills-pagos-tab" data-bs-toggle="pill" data-bs-target="#pills-pagos" type="button" role="tab" style="border-radius: 10px;">
            <i class="bi bi-cash-coin me-1"></i> Historial de Cobros y Abonos ({{ $cliente->pagos->count() }})
        </button>
    </li>
</ul>

<div class="tab-content" id="pills-tabContent">
    <!-- PESTAÑA 1: HISTORIAL DE COMPRAS -->
    <div class="tab-pane fade show active" id="pills-ventas" role="tabpanel">
        <div class="cliente-card overflow-hidden">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
                <h5 class="fw-bold m-0 text-dark"><i class="bi bi-cart3 me-2 text-primary"></i>Notas de Venta de {{ $cliente->nombre }}</h5>
                <span class="badge bg-light text-muted border">Total: {{ $cliente->ventas->count() }} notas</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Nº NOTA</th>
                            <th>FECHA</th>
                            <th>MÉTODO REGISTRO</th>
                            <th>ESTADO</th>
                            <th class="text-end">TOTAL VENTA</th>
                            <th class="text-end">COBRADO</th>
                            <th class="text-end">SALDO PENDIENTE</th>
                            <th class="text-center pe-4">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cliente->ventas as $v)
                        @php
                            $pendienteNota = floatval($v->saldo_pendiente ?? 0);
                            $esPendiente = $pendienteNota > 0 && $v->estado !== 'Anulada';
                        @endphp
                        <tr>
                            <td class="ps-4 fw-bold">
                                {{ $v->numero_nota ?? 'SYS-'.$v->id }}
                                @if($v->tipo_comprobante === 'Fisico')
                                    <span class="badge bg-secondary-subtle text-secondary small ms-1">Física</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($v->fecha)->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($v->metodo_pago === 'Efectivo')
                                    <span class="badge-efectivo"><i class="bi bi-cash me-1"></i>Efectivo</span>
                                @elseif($v->metodo_pago === 'QR')
                                    <span class="badge-qr"><i class="bi bi-qr-code me-1"></i>QR</span>
                                @else
                                    <span class="badge-por-pagar"><i class="bi bi-clock-history me-1"></i>Por Pagar</span>
                                @endif
                            </td>
                            <td>
                                @if($v->estado === 'Completada')
                                    <span class="badge bg-success-subtle text-success">Completada</span>
                                @elseif($v->estado === 'Pendiente')
                                    <span class="badge bg-warning-subtle text-warning">Pendiente</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">{{ $v->estado }}</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-dark">Bs. {{ number_format($v->total, 2) }}</td>
                            <td class="text-end fw-bold text-success">Bs. {{ number_format($v->monto_pagado ?? $v->total, 2) }}</td>
                            <td class="text-end fw-bold {{ $esPendiente ? 'text-danger' : 'text-muted' }}">
                                {{ $pendienteNota > 0 ? 'Bs. ' . number_format($pendienteNota, 2) : 'Bs. 0.00' }}
                            </td>
                            <td class="text-center pe-4">
                                @if($esPendiente)
                                <button type="button" class="btn btn-sm btn-success fw-bold px-3 py-1 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCobrarNota{{ $v->id }}">
                                    <i class="bi bi-cash-coin me-1"></i> Cobrar
                                </button>
                                @elseif($v->estado === 'Anulada')
                                    <span class="text-muted small fst-italic">Anulada</span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-circle me-1"></i>Pagada</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                No hay compras registradas para este cliente aún.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PESTAÑA 2: HISTORIAL DE ABONOS Y COBROS -->
    <div class="tab-pane fade" id="pills-pagos" role="tabpanel">
        <div class="cliente-card overflow-hidden">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
                <h5 class="fw-bold m-0 text-dark"><i class="bi bi-journal-check me-2 text-success"></i>Trazabilidad de Pagos Recibidos</h5>
                <span class="badge bg-success fs-6">{{ $cliente->pagos->count() }} cobro(s)</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">FECHA Y HORA</th>
                            <th>Nº NOTA</th>
                            <th class="text-end">MONTO COBRADO</th>
                            <th>MÉTODO DE PAGO</th>
                            <th>CAJERO RESPONSABLE</th>
                            <th class="pe-4">DETALLE / OBSERVACIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cliente->pagos as $p)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ \Carbon\Carbon::parse($p->fecha)->format('d/m/Y H:i') }}</td>
                            <td class="fw-bold">
                                @if($p->venta)
                                    {{ $p->venta->numero_nota ?? 'SYS-'.$p->venta->id }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-success fs-6">Bs. {{ number_format($p->monto, 2) }}</td>
                            <td>
                                @if($p->metodo_pago === 'Efectivo')
                                    <span class="badge-efectivo"><i class="bi bi-cash me-1"></i>Efectivo</span>
                                @else
                                    <span class="badge-qr"><i class="bi bi-qr-code me-1"></i>QR Simple</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-person-fill me-1"></i>{{ $p->usuario->name ?? 'Usuario' }}
                                </span>
                            </td>
                            <td class="pe-4 text-muted small">
                                {{ $p->observacion ?? 'Cobro de saldo pendiente' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-clock-history fs-2 d-block mb-2 text-muted"></i>
                                No se han registrado abonos o cobros posteriores para este cliente aún.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODALES FUERA DE LAS TABLAS                -->
<!-- ========================================== -->

<!-- MODAL: COBRAR DEUDA GENERAL DEL CLIENTE -->
@if($cliente->saldo_pendiente_total > 0)
<div class="modal fade" id="modalAbonarGeneral" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="/clientes/{{ $cliente->id }}/abono" method="POST" class="modal-content modal-content-clean">
            @csrf
            <div class="modal-header border-0 bg-success text-white py-3">
                <h5 class="fw-bold m-0"><i class="bi bi-cash-stack me-2"></i>Registrar Cobro a {{ $cliente->nombre }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 rounded-3 bg-danger bg-opacity-10 border border-danger-subtle mb-3 text-center">
                    <div class="small fw-bold text-danger text-uppercase">Deuda Total Pendiente</div>
                    <div class="fs-2 fw-bold text-danger">Bs. {{ number_format($cliente->saldo_pendiente_total, 2) }}</div>
                    <small class="text-muted">El pago se abonará automáticamente liquidando las notas pendientes más antiguas.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">MONTO A COBRAR (BS.) *</label>
                    <div class="input-group">
                        <span class="input-group-text fw-bold bg-light">Bs.</span>
                        <input type="number" step="0.5" min="0.5" max="{{ $cliente->saldo_pendiente_total }}" name="monto_abono" class="form-control form-control-lg fw-bold text-primary" value="{{ $cliente->saldo_pendiente_total }}" required>
                        <button type="button" class="btn btn-outline-secondary" onclick="this.form.monto_abono.value='{{ $cliente->saldo_pendiente_total }}'">
                            Total
                        </button>
                    </div>
                    <div class="form-text">Puedes cobrar el total completo o registrar un abono parcial.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">MÉTODO DE PAGO DEL COBRO *</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="payment-option-btn w-100 d-block active" id="lblGenEfectivo">
                                <input type="radio" name="metodo_pago" value="Efectivo" checked class="d-none" onchange="actualizarMetodoGen(this)">
                                <i class="bi bi-cash-stack fs-4 d-block text-success"></i>
                                <span class="d-block fw-bold mt-1">Efectivo</span>
                            </label>
                        </div>
                        <div class="col-6">
                            <label class="payment-option-btn w-100 d-block" id="lblGenQR">
                                <input type="radio" name="metodo_pago" value="QR" class="d-none" onchange="actualizarMetodoGen(this)">
                                <i class="bi bi-qr-code fs-4 d-block text-primary"></i>
                                <span class="d-block fw-bold mt-1">QR Simple</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label fw-bold small text-muted">OBSERVACIÓN O NOTA (OPCIONAL)</label>
                    <input type="text" name="observacion" class="form-control" placeholder="Ej: Pago realizado en tienda / Comprobante transf.">
                </div>
            </div>
            <div class="modal-footer border-0 bg-light p-3">
                <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success fw-bold px-4">
                    <i class="bi bi-check2-circle me-1"></i> Confirmar Cobro
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    function actualizarMetodoGen(radio) {
        document.getElementById('lblGenEfectivo').classList.toggle('active', radio.value === 'Efectivo');
        document.getElementById('lblGenQR').classList.toggle('active', radio.value === 'QR');
    }
</script>
@endif

<!-- MODALES INDIVIDUALES: COBRAR POR NOTA DE VENTA -->
@foreach($cliente->ventas as $v)
@if(floatval($v->saldo_pendiente ?? 0) > 0 && $v->estado !== 'Anulada')
<div class="modal fade" id="modalCobrarNota{{ $v->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="/ventas/{{ $v->id }}/abono" method="POST" class="modal-content modal-content-clean">
            @csrf
            <div class="modal-header border-0 bg-success text-white py-3">
                <h5 class="fw-bold m-0"><i class="bi bi-cash-coin me-2"></i>Cobrar Nota {{ $v->numero_nota ?? 'SYS-'.$v->id }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between p-3 rounded-3 bg-light border mb-3">
                    <div>
                        <div class="small text-muted">Total de la Nota</div>
                        <div class="fw-bold text-dark">Bs. {{ number_format($v->total, 2) }}</div>
                    </div>
                    <div>
                        <div class="small text-muted">Ya Pagado</div>
                        <div class="fw-bold text-success">Bs. {{ number_format($v->monto_pagado ?? 0, 2) }}</div>
                    </div>
                    <div class="text-end">
                        <div class="small text-muted">Saldo Pendiente</div>
                        <div class="fw-bold text-danger fs-5">Bs. {{ number_format($v->saldo_pendiente, 2) }}</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">MONTO A COBRAR (BS.) *</label>
                    <div class="input-group">
                        <span class="input-group-text fw-bold bg-light">Bs.</span>
                        <input type="number" step="0.5" min="0.5" max="{{ $v->saldo_pendiente }}" name="monto_abono" class="form-control form-control-lg fw-bold text-primary" value="{{ $v->saldo_pendiente }}" required>
                        <button type="button" class="btn btn-outline-secondary" onclick="this.form.monto_abono.value='{{ $v->saldo_pendiente }}'">
                            Total
                        </button>
                    </div>
                    <div class="form-text">Si cobra el saldo completo, la nota se marcará como Completada.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">MÉTODO DE PAGO DEL COBRO *</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="payment-option-btn w-100 d-block active" id="lblNotaEf{{ $v->id }}">
                                <input type="radio" name="metodo_pago" value="Efectivo" checked class="d-none" onchange="document.getElementById('lblNotaEf{{ $v->id }}').classList.toggle('active', this.value==='Efectivo'); document.getElementById('lblNotaQr{{ $v->id }}').classList.toggle('active', this.value==='QR');">
                                <i class="bi bi-cash-stack fs-4 d-block text-success"></i>
                                <span class="d-block fw-bold mt-1">Efectivo</span>
                            </label>
                        </div>
                        <div class="col-6">
                            <label class="payment-option-btn w-100 d-block" id="lblNotaQr{{ $v->id }}">
                                <input type="radio" name="metodo_pago" value="QR" class="d-none" onchange="document.getElementById('lblNotaEf{{ $v->id }}').classList.toggle('active', this.value==='Efectivo'); document.getElementById('lblNotaQr{{ $v->id }}').classList.toggle('active', this.value==='QR');">
                                <i class="bi bi-qr-code fs-4 d-block text-primary"></i>
                                <span class="d-block fw-bold mt-1">QR Simple</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label fw-bold small text-muted">OBSERVACIÓN O NOTA (OPCIONAL)</label>
                    <input type="text" name="observacion" class="form-control" placeholder="Ej: Cancelación saldo nota en tienda">
                </div>
            </div>
            <div class="modal-footer border-0 bg-light p-3">
                <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success fw-bold px-4">
                    <i class="bi bi-check2-circle me-1"></i> Registrar Cobro
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endforeach

@endsection
