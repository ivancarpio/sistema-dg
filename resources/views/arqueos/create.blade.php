@extends('layouts.app')

@section('content')

<style>
    /* ---- Estilos específicos para el formulario de creación de arqueo ---- */
    .table-custom-header th {
        background-color: #f1f5f9 !important;
        font-size: 0.75rem; font-weight: 800; color: #64748b;
        text-transform: uppercase; letter-spacing: 0.5px;
        padding: 13px 10px; border-bottom: none !important;
    }
    .table-custom-header th:first-child  { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
    .table-custom-header th:last-child   { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
    .table-custom tbody tr { border-bottom: 1px solid #f0f2f5; transition: background-color 0.15s; cursor: pointer; }
    .table-custom tbody tr:hover { background-color: #eff6ff; }
    .table-custom td { vertical-align: middle; padding: 11px 10px; }
    .card { border: none !important; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important; border-radius: .5rem !important; }

    /* Fila seleccionada */
    .fila-seleccionada { background-color: #dbeafe !important; }
    .fila-seleccionada td { color: #1e40af; }

    /* Panel derecho - Resumen en tiempo real */
    .resumen-card {
        position: sticky;
        top: 20px;
    }
    .resumen-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 0; border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }
    .resumen-row:last-child { border-bottom: none; }
    .resumen-label { color: #64748b; font-weight: 600; }
    .resumen-value { font-weight: 800; }

    /* Badge de método de pago */
    .badge-efectivo { background-color: #dcfce7; color: #16a34a; font-weight: 700; border-radius: 20px; padding: 4px 12px; }
    .badge-qr       { background-color: #dbeafe; color: #2563eb; font-weight: 700; border-radius: 20px; padding: 4px 12px; }

    /* Tabs de período */
    .periodo-tabs .btn { border-radius: 20px; font-size: 0.82rem; font-weight: 600; }
    .periodo-tabs .btn.active { background: #0ea5e9; color: white; border-color: #0ea5e9; }

    /* Conteo de selección */
    .seleccion-info {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        padding: 12px 20px;
        font-size: 0.88rem;
    }
</style>

{{-- Alertas --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Errores de validación --}}
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Por favor corrige los errores:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
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
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:0.8rem;">
                    <li class="breadcrumb-item"><a href="/arqueos" class="text-muted text-decoration-none">Arqueos de caja</a></li>
                    <li class="breadcrumb-item active">Crear nuevo</li>
                </ol>
            </nav>
            <h3 class="fw-bold m-0 text-dark">Nuevo Arqueo de Caja</h3>
        </div>
    </div>
    <a href="/arqueos" class="btn btn-outline-secondary fw-bold">
        <i class="bi bi-arrow-left me-2"></i>Volver
    </a>
</div>

<form action="/arqueos" method="POST" id="formArqueo">
    @csrf

    <div class="row g-4">

        {{-- ============================================================ --}}
        {{-- COLUMNA IZQUIERDA: lista de ventas + filtros                  --}}
        {{-- ============================================================ --}}
        <div class="col-lg-8">

            {{-- Buscador y filtros de período --}}
            <div class="card mb-3">
                <div class="card-body py-3">
                    <div class="row g-2 align-items-center">
                        {{-- Buscador --}}
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" id="buscadorVentas"
                                    class="form-control border-start-0 bg-light"
                                    placeholder="Buscar por cliente, Nº nota...">
                            </div>
                        </div>
                        {{-- Tabs de período --}}
                        <div class="col-md-4">
                            <div class="periodo-tabs d-flex gap-1">
                                <a href="?periodo=hoy"
                                    class="btn btn-sm btn-outline-secondary {{ $periodo == 'hoy' ? 'active' : '' }}">
                                    Hoy
                                </a>
                                <a href="?periodo=semana"
                                    class="btn btn-sm btn-outline-secondary {{ $periodo == 'semana' ? 'active' : '' }}">
                                    Esta semana
                                </a>
                                <a href="?periodo=mes"
                                    class="btn btn-sm btn-outline-secondary {{ $periodo == 'mes' ? 'active' : '' }}">
                                    Este mes
                                </a>
                            </div>
                        </div>
                        {{-- Filtro método de pago --}}
                        <div class="col-md-3">
                            <select class="form-select bg-light"
                                onchange="window.location='?periodo={{ $periodo }}&metodo_pago='+this.value">
                                <option value="">Todo</option>
                                <option value="Efectivo" {{ request('metodo_pago') == 'Efectivo' ? 'selected' : '' }}>
                                    💵 Efectivo
                                </option>
                                <option value="QR" {{ request('metodo_pago') == 'QR' ? 'selected' : '' }}>
                                    📱 QR
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info de selección --}}
            <div class="seleccion-info mb-3 d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-info-circle-fill text-primary me-2"></i>
                    Has seleccionado <strong id="countSeleccionados">0</strong> ventas.
                    <a href="#" id="btnSeleccionarTodos" class="text-primary fw-bold ms-2">
                        Seleccionar todas ({{ $ventasPendientes->count() }})
                    </a>
                    <span class="text-muted mx-1">·</span>
                    <a href="#" id="btnDeseleccionarTodos" class="text-muted">Deseleccionar todas</a>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-2">
                    {{ $ventasPendientes->count() }} ventas disponibles
                </span>
            </div>

            {{-- Tabla de ventas pendientes --}}
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom align-middle m-0" id="tablaVentas">
                            <thead class="table-custom-header">
                                <tr>
                                    <th class="ps-3" style="width:40px;">
                                        <input type="checkbox" id="checkTodos" class="form-check-input">
                                    </th>
                                    <th>#</th>
                                    <th>Nº Nota</th>
                                    <th>Cliente</th>
                                    <th>Monto</th>
                                    <th>Método</th>
                                    <th>Cajero</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ventasPendientes as $venta)
                                <tr class="fila-venta"
                                    data-id="{{ $venta->id }}"
                                    data-total="{{ $venta->total }}"
                                    data-metodo="{{ $venta->metodo_pago }}"
                                    data-cliente="{{ strtolower($venta->cliente) }}"
                                    data-nota="{{ strtolower($venta->numero_nota ?? '') }}">

                                    <td class="ps-3">
                                        <input type="checkbox"
                                            name="ventas_seleccionadas[]"
                                            value="{{ $venta->id }}"
                                            class="form-check-input check-venta">
                                    </td>
                                    <td class="text-muted" style="font-size:0.8rem;">{{ $venta->id }}</td>
                                    <td class="fw-bold">
                                        {{ $venta->numero_nota ?? 'SYS-'.str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-person me-1"></i>{{ $venta->cliente ?? 'Sin nombre' }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-success">
                                        Bs. {{ number_format($venta->total, 2) }}
                                    </td>
                                    <td>
                                        @if(strtolower($venta->metodo_pago) == 'efectivo')
                                            <span class="badge-efectivo">
                                                <i class="bi bi-cash me-1"></i>Efectivo
                                            </span>
                                        @else
                                            <span class="badge-qr">
                                                <i class="bi bi-qr-code me-1"></i>QR
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted fw-bold">
                                            <i class="bi bi-person-circle me-1"></i>
                                            {{ optional($venta->usuario)->name ?? 'Cajero' }}
                                        </small>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}
                                        </small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-check2-all fs-1 d-block mb-2 text-success opacity-50"></i>
                                        <p class="fw-bold mb-1">¡Todo arqueado!</p>
                                        <small>No hay ventas pendientes de arqueo en este período.</small>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- COLUMNA DERECHA: resumen + botón confirmar                    --}}
        {{-- ============================================================ --}}
        <div class="col-lg-4">
            <div class="resumen-card">

                {{-- Tarjeta de resumen en tiempo real --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white py-3 border-0 rounded-top">
                        <h6 class="fw-bold m-0">
                            <i class="bi bi-calculator me-2"></i>Resumen del Arqueo
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="resumen-row">
                            <span class="resumen-label">
                                <i class="bi bi-cash-stack me-2 text-success"></i>Efectivo
                            </span>
                            <span class="resumen-value text-success" id="resumenEfectivo">
                                Bs. 0,00
                            </span>
                        </div>
                        <div class="resumen-row">
                            <span class="resumen-label">
                                <i class="bi bi-qr-code me-2 text-primary"></i>QR
                            </span>
                            <span class="resumen-value text-primary" id="resumenQr">
                                Bs. 0,00
                            </span>
                        </div>
                        <div class="resumen-row" style="border-top: 2px solid #e2e8f0; margin-top: 5px; padding-top: 15px;">
                            <span class="resumen-label fw-bold text-dark">
                                <i class="bi bi-wallet2 me-2"></i>Total General
                            </span>
                            <span class="resumen-value text-dark fs-5" id="resumenTotal">
                                Bs. 0,00
                            </span>
                        </div>
                        <div class="resumen-row">
                            <span class="resumen-label">Nº de ventas</span>
                            <span class="resumen-value" id="resumenCantidad">0</span>
                        </div>
                    </div>
                </div>

                {{-- Observaciones --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <label class="form-label fw-bold text-muted small text-uppercase">
                            <i class="bi bi-chat-text me-1"></i>Observaciones (opcional)
                        </label>
                        <textarea name="observaciones" class="form-control bg-light" rows="3"
                            placeholder="Ej: Turno mañana, notas del cajero...">{{ old('observaciones') }}</textarea>
                    </div>
                </div>

                {{-- Botón de acción --}}
                <button type="submit" id="btnCrearArqueo"
                    class="btn btn-primary w-100 py-3 fw-bold shadow fs-5" disabled>
                    <i class="bi bi-safe2-fill me-2"></i>
                    Crear Arqueo de Caja
                </button>
                <p class="text-muted text-center mt-2" style="font-size: 0.78rem;">
                    <i class="bi bi-lock me-1"></i>
                    Las ventas incluidas quedarán marcadas como <strong>Verificadas</strong>
                </p>

            </div>
        </div>
    </div>
</form>

{{-- ===== JAVASCRIPT: lógica de selección y resumen en tiempo real ===== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const filas       = document.querySelectorAll('.fila-venta');
    const checkTodos  = document.getElementById('checkTodos');
    const btnSelTodos = document.getElementById('btnSeleccionarTodos');
    const btnDeselTodos = document.getElementById('btnDeseleccionarTodos');
    const countSpan   = document.getElementById('countSeleccionados');
    const btnCrear    = document.getElementById('btnCrearArqueo');
    const buscador    = document.getElementById('buscadorVentas');

    // ---- Formato de moneda ----
    function formatMonto(n) {
        return 'Bs. ' + n.toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // ---- Actualizar resumen lateral ----
    function actualizarResumen() {
        let efectivo = 0, qr = 0, cantidad = 0;

        document.querySelectorAll('.check-venta:checked').forEach(cb => {
            const fila = cb.closest('tr');
            const total  = parseFloat(fila.dataset.total) || 0;
            const metodo = (fila.dataset.metodo || '').toLowerCase();
            if (metodo === 'efectivo') efectivo += total;
            else                       qr       += total;
            cantidad++;
        });

        document.getElementById('resumenEfectivo').textContent = formatMonto(efectivo);
        document.getElementById('resumenQr').textContent       = formatMonto(qr);
        document.getElementById('resumenTotal').textContent    = formatMonto(efectivo + qr);
        document.getElementById('resumenCantidad').textContent = cantidad;
        countSpan.textContent = cantidad;

        // Habilitar botón si hay al menos 1 seleccionada
        btnCrear.disabled = (cantidad === 0);
    }

    // ---- Click en fila: toggle check ----
    filas.forEach(fila => {
        fila.addEventListener('click', function(e) {
            if (e.target.type === 'checkbox') return; // ya lo maneja el check
            const cb = fila.querySelector('.check-venta');
            cb.checked = !cb.checked;
            fila.classList.toggle('fila-seleccionada', cb.checked);
            syncCheckTodos();
            actualizarResumen();
        });
    });

    // ---- Click en checkbox directamente ----
    document.querySelectorAll('.check-venta').forEach(cb => {
        cb.addEventListener('change', function() {
            const fila = cb.closest('tr');
            fila.classList.toggle('fila-seleccionada', cb.checked);
            syncCheckTodos();
            actualizarResumen();
        });
    });

    // ---- Check "todos" del encabezado ----
    checkTodos.addEventListener('change', function() {
        const visibles = document.querySelectorAll('.fila-venta:not([style*="display: none"]) .check-venta');
        visibles.forEach(cb => {
            cb.checked = checkTodos.checked;
            cb.closest('tr').classList.toggle('fila-seleccionada', checkTodos.checked);
        });
        actualizarResumen();
    });

    // ---- Sincronizar check del encabezado ----
    function syncCheckTodos() {
        const todos    = document.querySelectorAll('.check-venta').length;
        const marcados = document.querySelectorAll('.check-venta:checked').length;
        checkTodos.checked       = (marcados === todos && todos > 0);
        checkTodos.indeterminate = (marcados > 0 && marcados < todos);
    }

    // ---- Botones Seleccionar / Deseleccionar todos ----
    btnSelTodos.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.check-venta').forEach(cb => {
            cb.checked = true;
            cb.closest('tr').classList.add('fila-seleccionada');
        });
        checkTodos.checked = true;
        actualizarResumen();
    });

    btnDeselTodos.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.check-venta').forEach(cb => {
            cb.checked = false;
            cb.closest('tr').classList.remove('fila-seleccionada');
        });
        checkTodos.checked = false;
        actualizarResumen();
    });

    // ---- Buscador en tiempo real ----
    buscador.addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        document.querySelectorAll('.fila-venta').forEach(fila => {
            const cliente = fila.dataset.cliente || '';
            const nota    = fila.dataset.nota    || '';
            const visible = !q || cliente.includes(q) || nota.includes(q);
            fila.style.display = visible ? '' : 'none';
        });
    });

    // ---- Inicializar resumen ----
    actualizarResumen();
});
</script>

@endsection
