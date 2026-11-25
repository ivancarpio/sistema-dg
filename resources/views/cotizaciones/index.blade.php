@extends('layouts.app')

@section('title', 'Cotizaciones')

@section('content')
<style>
    .cot-kpi-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #ffffff;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .cot-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
    }
    .cot-kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .estado-select-badge {
        font-weight: 700;
        font-size: 12px;
        border-radius: 20px;
        padding: 3px 10px;
        cursor: pointer;
    }
    .wa-preview-box {
        font-family: 'Segoe UI', system-ui, sans-serif;
        font-size: 13px;
        line-height: 1.45;
        background-color: #ecfdf5;
        border: 1px solid #a7f3d0;
        border-radius: 10px;
    }
</style>

<div class="container-fluid py-3">
    <!-- HEADER -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold px-2 py-1">
                    <i class="bi bi-file-earmark-text-fill me-1"></i> Gestión Comercial
                </span>
                <span class="badge bg-light text-secondary border fw-semibold px-2 py-1">
                    <i class="bi bi-currency-exchange me-1"></i> Ref.: 1 S/ = Bs. {{ number_format(floatval($tipoCambio->tasa_venta ?? 3.60), 2) }}
                </span>
            </div>
            <h3 class="fw-bold text-dark mb-0">Cotizaciones y Proformas</h3>
            <p class="text-muted small mb-0">
                Crea cotizaciones para clientes o tiendas externas, descárgalas en PDF con el logo de tu empresa, imprímelas o envíalas por WhatsApp.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('cotizaciones.create') }}" class="btn btn-primary fw-bold shadow-sm px-4 py-2 rounded-3">
                <i class="bi bi-plus-circle-fill me-2"></i> Nueva Cotización
            </a>
        </div>
    </div>

    <!-- ALERTAS -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex flex-wrap align-items-center justify-content-between gap-2" role="alert">
            <div>
                <i class="bi bi-check-circle-fill me-2"></i> <strong>{{ session('success') }}</strong>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if(session('abrir_pdf_id'))
                    <a href="{{ route('cotizaciones.pdf', session('abrir_pdf_id')) }}" target="_blank" class="btn btn-sm btn-danger fw-bold">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Abrir PDF Generado
                    </a>
                @endif
                @if(session('abrir_print_id'))
                    <a href="{{ route('cotizaciones.imprimir', session('abrir_print_id')) }}?autoprint=1" target="_blank" class="btn btn-sm btn-dark fw-bold">
                        <i class="bi bi-printer-fill me-1"></i> Abrir Vista de Impresión
                    </a>
                @endif
                <button type="button" class="btn-close position-static ms-2" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- KPIS -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="cot-kpi-card p-3 shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Emitidas en el Mes</div>
                        <div class="fs-3 fw-extrabold text-dark mt-1">{{ number_format($totalMes) }}</div>
                        <div class="small text-secondary">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</div>
                    </div>
                    <div class="cot-kpi-icon bg-primary-subtle text-primary">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="cot-kpi-card p-3 shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Pendientes</div>
                        <div class="fs-3 fw-extrabold text-warning mt-1">{{ number_format($pendientesCount) }}</div>
                        <div class="small text-secondary">Por enviar o confirmar</div>
                    </div>
                    <div class="cot-kpi-icon bg-warning-subtle text-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="cot-kpi-card p-3 shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Enviadas / Aprobadas</div>
                        <div class="fs-3 fw-extrabold text-success mt-1">{{ number_format($enviadasAprobadasCount) }}</div>
                        <div class="small text-secondary">En el mes actual</div>
                    </div>
                    <div class="cot-kpi-icon bg-success-subtle text-success">
                        <i class="bi bi-check2-all"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="cot-kpi-card p-3 shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Monto Cotizado (Mes)</div>
                        <div class="fs-3 fw-extrabold text-info-emphasis mt-1">{{ $monedaSimbolo }} {{ number_format($montoCotizadoMes, 2) }}</div>
                        <div class="small text-secondary">Excluye rechazadas</div>
                    </div>
                    <div class="cot-kpi-icon bg-info-subtle text-info-emphasis">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTROS -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('cotizaciones.index') }}" class="row g-2 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label small fw-bold text-muted mb-1">Buscar</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="buscar" class="form-control" value="{{ request('buscar') }}"
                               placeholder="N° cotización, cliente, tienda, NIT o teléfono...">
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <label class="form-label small fw-bold text-muted mb-1">Tipo</label>
                    <select name="tipo" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="cliente" {{ request('tipo') === 'cliente' ? 'selected' : '' }}>Clientes</option>
                        <option value="tienda_externa" {{ request('tipo') === 'tienda_externa' ? 'selected' : '' }}>Tiendas Externas</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <label class="form-label small fw-bold text-muted mb-1">Estado</label>
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="enviada" {{ request('estado') === 'enviada' ? 'selected' : '' }}>Enviada</option>
                        <option value="aprobada" {{ request('estado') === 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                        <option value="rechazada" {{ request('estado') === 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                        <option value="vencida" {{ request('estado') === 'vencida' ? 'selected' : '' }}>Vencida</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <label class="form-label small fw-bold text-muted mb-1">Desde / Hasta</label>
                    <div class="input-group input-group-sm">
                        <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}" title="Desde">
                        <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}" title="Hasta">
                    </div>
                </div>
                <div class="col-lg-2 col-md-8 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm fw-bold flex-grow-1">
                        <i class="bi bi-funnel-fill me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('cotizaciones.index') }}" class="btn btn-outline-secondary btn-sm" title="Limpiar filtros">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- LISTADO DE COTIZACIONES -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-3">N° / Fecha</th>
                        <th>Tipo</th>
                        <th>Destinatario / Tienda</th>
                        <th class="text-center">Ítems</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cotizaciones as $c)
                        @php
                            $sim = $c->simbolo_moneda ?: ($c->moneda === 'PEN' ? 'S/' : 'Bs.');
                            $equiv = $c->mostrar_equivalente ? $c->convertirAOtraMoneda() : null;
                            $vencidaPorFecha = $c->valida_hasta && $c->valida_hasta->isPast() && in_array($c->estado, ['pendiente', 'enviada']);
                            $estadoColors = [
                                'pendiente' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                                'enviada'   => 'bg-info-subtle text-info-emphasis border-info-subtle',
                                'aprobada'  => 'bg-success-subtle text-success border-success-subtle',
                                'rechazada' => 'bg-danger-subtle text-danger border-danger-subtle',
                                'vencida'   => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                            ];
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-primary">{{ $c->numero_cotizacion }}</div>
                                <div class="small text-muted">
                                    {{ $c->fecha ? $c->fecha->format('d/m/Y H:i') : '' }}
                                </div>
                                @if($c->valida_hasta)
                                    <div style="font-size: 11px;" class="{{ $vencidaPorFecha ? 'text-danger fw-bold' : 'text-secondary' }}">
                                        Válida: {{ $c->valida_hasta->format('d/m/Y') }}
                                        @if($vencidaPorFecha) • Vencida @endif
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($c->tipo_cotizacion === 'tienda_externa')
                                    <span class="badge rounded-pill px-2 py-1" style="background-color:#f5f3ff; color:#6d28d9; border:1px solid #ddd6fe;">
                                        <i class="bi bi-shop-window me-1"></i>Tienda Externa
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1">
                                        <i class="bi bi-person-fill me-1"></i>Cliente
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $c->destinatario_nombre }}</div>
                                @if($c->destinatario_empresa)
                                    <div class="small text-secondary fw-semibold">
                                        <i class="bi bi-building me-1"></i>{{ $c->destinatario_empresa }}
                                    </div>
                                @endif
                                <div class="small text-muted d-flex flex-wrap gap-2 mt-1" style="font-size: 11.5px;">
                                    @if($c->destinatario_telefono)
                                        <a href="javascript:void(0)" onclick="abrirModalWhatsApp({{ $c->id }})" class="text-success text-decoration-none fw-semibold">
                                            <i class="bi bi-whatsapp"></i> {{ $c->destinatario_telefono }}
                                        </a>
                                    @endif
                                    @if($c->destinatario_nit)
                                        <span>NIT/RUC: {{ $c->destinatario_nit }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border rounded-pill px-2 py-1 fw-bold">
                                    {{ $c->detalles->count() }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="fw-extrabold text-dark fs-6">{{ $sim }} {{ number_format($c->total, 2) }}</div>
                                @if($equiv !== null)
                                    <div class="small text-muted" style="font-size: 11px;">
                                        ≈ {{ $c->simboloOtraMoneda() }} {{ number_format($equiv, 2) }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <select class="form-select form-select-sm d-inline-block w-auto estado-select-badge border {{ $estadoColors[$c->estado] ?? 'bg-light' }}"
                                        onchange="cambiarEstadoCotizacion({{ $c->id }}, this)" data-prev="{{ $c->estado }}">
                                    <option value="pendiente" {{ $c->estado === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="enviada" {{ $c->estado === 'enviada' ? 'selected' : '' }}>Enviada</option>
                                    <option value="aprobada" {{ $c->estado === 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                                    <option value="rechazada" {{ $c->estado === 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                                    <option value="vencida" {{ $c->estado === 'vencida' ? 'selected' : '' }}>Vencida</option>
                                </select>
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary" onclick="verDetalleCotizacion({{ $c->id }})" title="Ver Detalle">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-success" onclick="abrirModalWhatsApp({{ $c->id }})" title="Enviar por WhatsApp">
                                        <i class="bi bi-whatsapp"></i>
                                    </button>
                                    <a href="{{ route('cotizaciones.pdf', $c->id) }}" target="_blank" class="btn btn-outline-danger" title="Descargar PDF">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </a>
                                    <a href="{{ route('cotizaciones.imprimir', $c->id) }}?autoprint=1" target="_blank" class="btn btn-outline-dark" title="Imprimir">
                                        <i class="bi bi-printer-fill"></i>
                                    </a>
                                    <a href="{{ route('cotizaciones.edit', $c->id) }}" class="btn btn-outline-secondary" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" onclick="confirmarEliminarCotizacion({{ $c->id }}, '{{ $c->numero_cotizacion }}')" title="Eliminar">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                                <form id="formEliminarCot_{{ $c->id }}" method="POST" action="{{ route('cotizaciones.destroy', $c->id) }}" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark-text fs-1 opacity-50"></i>
                                <p class="fw-bold mt-2 mb-1">No hay cotizaciones registradas</p>
                                <p class="small mb-3">Crea tu primera cotización para un cliente o una tienda externa.</p>
                                <a href="{{ route('cotizaciones.create') }}" class="btn btn-primary btn-sm fw-bold">
                                    <i class="bi bi-plus-circle me-1"></i> Nueva Cotización
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($cotizaciones->hasPages())
            <div class="card-footer bg-white border-top py-3 px-3">
                {{ $cotizaciones->links() }}
            </div>
        @endif
    </div>
</div>

<!-- MODAL: VER DETALLE DE COTIZACIÓN -->
<div class="modal fade" id="modalDetalleCotizacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-dark text-white rounded-top-4">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="detModalTitulo">Cotización</h5>
                    <small class="text-white-50" id="detModalSubtitulo"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="detModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light rounded-bottom-4 d-flex flex-wrap justify-content-between gap-2">
                <div class="d-flex gap-2">
                    <a href="#" id="detBtnEditar" class="btn btn-outline-secondary btn-sm fw-bold">
                        <i class="bi bi-pencil-fill me-1"></i> Editar
                    </a>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" id="detBtnWa" class="btn btn-success btn-sm fw-bold">
                        <i class="bi bi-whatsapp me-1"></i> WhatsApp
                    </button>
                    <a href="#" target="_blank" id="detBtnImprimir" class="btn btn-dark btn-sm fw-bold">
                        <i class="bi bi-printer-fill me-1"></i> Imprimir
                    </a>
                    <a href="#" target="_blank" id="detBtnPdf" class="btn btn-danger btn-sm fw-bold">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Descargar PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: ENVIAR COTIZACIÓN POR WHATSAPP -->
<div class="modal fade" id="modalWhatsAppCotizacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-success text-white rounded-top-4">
                <div>
                    <h6 class="modal-title fw-bold mb-0">
                        <i class="bi bi-whatsapp me-2"></i>Enviar Cotización por WhatsApp
                    </h6>
                    <small class="text-white-50" id="waSubtituloModal"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="waCotId">
                <input type="hidden" id="waCotEstadoActual">
                <input type="hidden" id="waCotPdfUrl">

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Número de WhatsApp (con código de país)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold text-success">+</span>
                        <input type="text" id="waTelefonoInput" class="form-control fw-bold" placeholder="Ej. 51987654321 ó 59178003571">
                        <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold" onclick="aplicarPrefijoWa('51')" title="Agregar código de Perú +51">🇵🇪 +51</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold" onclick="aplicarPrefijoWa('591')" title="Agregar código de Bolivia +591">🇧🇴 +591</button>
                    </div>
                    <div class="form-text small">
                        Si el número no tiene código de país, haz clic en <strong>🇵🇪 +51</strong> o <strong>🇧🇴 +591</strong>.
                    </div>
                </div>

                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label small fw-bold text-dark mb-0">Mensaje de Cotización</label>
                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-semibold" onclick="copiarMensajeWa()">
                            <i class="bi bi-clipboard me-1"></i>Copiar texto
                        </button>
                    </div>
                    <textarea id="waMensajeTextarea" rows="8" class="form-control wa-preview-box p-2"></textarea>
                </div>

                <div class="alert alert-light border small text-muted py-2 mb-0">
                    <i class="bi bi-paperclip text-danger me-1"></i>
                    ¿Deseas adjuntar también el archivo PDF? Descárgalo con el botón inferior y arrástralo al chat de WhatsApp.
                </div>
            </div>
            <div class="modal-footer border-0 bg-light rounded-bottom-4 d-flex flex-wrap justify-content-between gap-2">
                <a href="#" target="_blank" id="waBtnDescargarPdf" class="btn btn-outline-danger btn-sm fw-bold">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Descargar PDF
                </a>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light btn-sm fw-semibold" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success btn-sm fw-bold px-3" onclick="lanzarWhatsApp()">
                        <i class="bi bi-whatsapp me-1"></i> Abrir WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const CSRF_TOKEN = '{{ csrf_token() }}';

    function escHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function verDetalleCotizacion(id) {
        const modal = new bootstrap.Modal(document.getElementById('modalDetalleCotizacion'));
        document.getElementById('detModalTitulo').textContent = 'Cargando cotización...';
        document.getElementById('detModalSubtitulo').textContent = '';
        document.getElementById('detModalBody').innerHTML = `
            <div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>`;
        modal.show();

        fetch(`/cotizaciones/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(d => {
            if (!d.success) throw new Error('Error al cargar');

            const sim = d.simbolo_moneda || 'Bs.';
            document.getElementById('detModalTitulo').textContent = `Cotización ${d.numero_cotizacion}`;
            document.getElementById('detModalSubtitulo').textContent = `Fecha: ${d.fecha} • Válida hasta: ${d.valida_hasta} • ${d.tipo_label}`;

            document.getElementById('detBtnEditar').href = `/cotizaciones/${d.id}/editar`;
            document.getElementById('detBtnPdf').href = d.pdf_url;
            document.getElementById('detBtnImprimir').href = `${d.imprimir_url}?autoprint=1`;
            document.getElementById('detBtnWa').onclick = function () {
                const inst = bootstrap.Modal.getInstance(document.getElementById('modalDetalleCotizacion'));
                if (inst) inst.hide();
                abrirModalWhatsApp(d.id);
            };

            let rows = '';
            (d.detalles || []).forEach((it, idx) => {
                rows += `
                    <tr>
                        <td class="text-center text-muted">${idx + 1}</td>
                        <td>
                            <div class="fw-semibold text-dark">${escHtml(it.nombre)}</div>
                            <div class="small text-muted">
                                ${it.codigo && it.codigo !== '—' ? `Cód: ${escHtml(it.codigo)}` : ''}
                                ${it.es_externo ? `<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle ms-1">Externo</span>` : ''}
                            </div>
                        </td>
                        <td class="text-center">${escHtml(it.unidad)}</td>
                        <td class="text-center fw-bold">${Number(it.cantidad).toFixed(Number.isInteger(Number(it.cantidad)) ? 0 : 2)}</td>
                        <td class="text-end">${sim} ${Number(it.precio_unitario).toFixed(2)}</td>
                        <td class="text-end fw-bold">${sim} ${Number(it.subtotal).toFixed(2)}</td>
                    </tr>`;
            });

            let equivHtml = '';
            if (d.mostrar_equivalente && d.equivalente_monto !== null) {
                equivHtml = `
                    <div class="alert alert-info py-2 px-3 mt-2 mb-0 small d-flex justify-content-between align-items-center">
                        <span>Equivalente ref. (1 S/ = Bs. ${Number(d.tasa_cambio_aplicada).toFixed(2)}):</span>
                        <strong class="fs-6">${escHtml(d.equivalente_simbolo)} ${Number(d.equivalente_monto).toFixed(2)}</strong>
                    </div>`;
            }

            document.getElementById('detModalBody').innerHTML = `
                <div class="row g-2 bg-light border rounded-3 p-3 mb-3">
                    <div class="col-md-6">
                        <div class="small text-muted fw-bold text-uppercase" style="font-size:10.5px;">Destinatario (${escHtml(d.tipo_label)})</div>
                        <div class="fw-bold text-dark">${escHtml(d.destinatario_nombre)}</div>
                        ${d.destinatario_empresa ? `<div class="small text-secondary fw-semibold">${escHtml(d.destinatario_empresa)}</div>` : ''}
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted fw-bold text-uppercase" style="font-size:10.5px;">Contacto / Fiscal</div>
                        <div class="small"><strong>Tel / WA:</strong> ${escHtml(d.destinatario_telefono || '—')}</div>
                        <div class="small"><strong>NIT / RUC:</strong> ${escHtml(d.destinatario_nit || '—')} • <strong>Dir:</strong> ${escHtml(d.destinatario_direccion || '—')}</div>
                    </div>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-dark small">
                            <tr>
                                <th class="text-center">#</th>
                                <th>Producto / Descripción</th>
                                <th class="text-center">Unidad</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-end">P. Unit</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>

                <div class="row g-3 align-items-start">
                    <div class="col-md-7">
                        ${d.condiciones ? `<div class="p-2 bg-light border rounded mb-2 small"><strong>Condiciones:</strong><br>${escHtml(d.condiciones)}</div>` : ''}
                        ${d.observaciones ? `<div class="p-2 bg-light border rounded small"><strong>Observaciones:</strong><br>${escHtml(d.observaciones)}</div>` : ''}
                    </div>
                    <div class="col-md-5">
                        <div class="border rounded-3 p-3 bg-light">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Subtotal:</span>
                                <strong>${sim} ${Number(d.subtotal).toFixed(2)}</strong>
                            </div>
                            ${Number(d.descuento) > 0 ? `
                                <div class="d-flex justify-content-between small text-danger mb-1">
                                    <span>Descuento:</span>
                                    <strong>- ${sim} ${Number(d.descuento).toFixed(2)}</strong>
                                </div>` : ''}
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary">TOTAL:</span>
                                <span class="fw-extrabold fs-5 text-primary">${sim} ${Number(d.total).toFixed(2)}</span>
                            </div>
                        </div>
                        ${equivHtml}
                    </div>
                </div>`;
        })
        .catch(() => {
            document.getElementById('detModalBody').innerHTML = `
                <div class="alert alert-danger mb-0">No se pudo cargar el detalle de la cotización.</div>`;
        });
    }

    function abrirModalWhatsApp(id) {
        const modalEl = document.getElementById('modalWhatsAppCotizacion');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

        document.getElementById('waSubtituloModal').textContent = 'Cargando datos...';
        document.getElementById('waTelefonoInput').value = '';
        document.getElementById('waMensajeTextarea').value = 'Generando mensaje de cotización...';
        modal.show();

        fetch(`/cotizaciones/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(d => {
            document.getElementById('waCotId').value = d.id;
            document.getElementById('waCotEstadoActual').value = d.estado;
            document.getElementById('waCotPdfUrl').value = d.pdf_url;
            document.getElementById('waSubtituloModal').textContent = `${d.numero_cotizacion} — ${d.destinatario_nombre}`;
            document.getElementById('waTelefonoInput').value = d.telefono_wa || '';
            document.getElementById('waMensajeTextarea').value = d.mensaje_wa || '';
            document.getElementById('waBtnDescargarPdf').href = d.pdf_url;
        })
        .catch(() => {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cargar la cotización para WhatsApp.' });
        });
    }

    function aplicarPrefijoWa(codigo) {
        const input = document.getElementById('waTelefonoInput');
        let num = (input.value || '').replace(/[^0-9]/g, '');
        if (!num) {
            input.value = codigo;
            input.focus();
            return;
        }
        if (num.startsWith('51') || num.startsWith('591')) {
            num = num.replace(/^(51|591)/, '');
        }
        input.value = codigo + num;
    }

    function copiarMensajeWa() {
        const txt = document.getElementById('waMensajeTextarea').value;
        navigator.clipboard.writeText(txt).then(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Mensaje copiado al portapapeles',
                showConfirmButton: false,
                timer: 2000
            });
        });
    }

    function lanzarWhatsApp() {
        const id = document.getElementById('waCotId').value;
        const estadoActual = document.getElementById('waCotEstadoActual').value;
        const tel = (document.getElementById('waTelefonoInput').value || '').replace(/[^0-9]/g, '');
        const msg = document.getElementById('waMensajeTextarea').value;

        if (!tel || tel.length < 8) {
            Swal.fire({
                icon: 'warning',
                title: 'Número requerido',
                text: 'Ingresa un número de WhatsApp válido con su código de país (Ej. 51987654321 o 59178003571).'
            });
            document.getElementById('waTelefonoInput').focus();
            return;
        }

        const url = `https://wa.me/${tel}?text=${encodeURIComponent(msg)}`;
        window.open(url, '_blank');

        // Marcar automáticamente como 'enviada' si aún está 'pendiente'
        if (id && estadoActual === 'pendiente') {
            fetch(`/cotizaciones/${id}/estado`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({ estado: 'enviada' })
            }).catch(() => {});
        }
    }

    function cambiarEstadoCotizacion(id, selectEl) {
        const nuevoEstado = selectEl.value;
        const previo = selectEl.getAttribute('data-prev');

        fetch(`/cotizaciones/${id}/estado`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({ estado: nuevoEstado })
        })
        .then(r => r.json())
        .then(d => {
            if (!d.success) throw new Error('Error');
            selectEl.setAttribute('data-prev', nuevoEstado);
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: `Estado actualizado a ${nuevoEstado}`,
                showConfirmButton: false,
                timer: 1800
            });
            setTimeout(() => window.location.reload(), 650);
        })
        .catch(() => {
            selectEl.value = previo;
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cambiar el estado.' });
        });
    }

    function confirmarEliminarCotizacion(id, numero) {
        Swal.fire({
            title: `¿Eliminar cotización ${numero}?`,
            text: 'Esta acción eliminará la cotización de forma permanente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(res => {
            if (res.isConfirmed) {
                document.getElementById(`formEliminarCot_${id}`).submit();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        @if(session('abrir_wa_id'))
            abrirModalWhatsApp({{ intval(session('abrir_wa_id')) }});
        @elseif(session('abrir_detalle_id'))
            verDetalleCotizacion({{ intval(session('abrir_detalle_id')) }});
        @elseif(session('abrir_pdf_id'))
            window.open("{{ route('cotizaciones.pdf', intval(session('abrir_pdf_id'))) }}", "_blank");
        @elseif(session('abrir_print_id'))
            window.open("{{ route('cotizaciones.imprimir', intval(session('abrir_print_id'))) }}?autoprint=1", "_blank");
        @endif
    });
</script>
@endsection
