@extends('layouts.app')

@section('title', $cotizacion ? 'Editar Cotización ' . $cotizacion->numero_cotizacion : 'Nueva Cotización')

@section('content')
@php
    $esEdicion = $cotizacion !== null;
    $monedaInicial = old('moneda', $cotizacion->moneda ?? ($monedaPrincipal ?? 'BOB'));
    $simboloInicial = $monedaInicial === 'PEN' ? 'S/' : 'Bs.';
    $tipoInicial = old('tipo_cotizacion', $cotizacion->tipo_cotizacion ?? 'cliente');
    $tasaDefault = floatval($tipoCambio->tasa_venta ?? 3.60);
    $tasaInicial = old('tasa_cambio_aplicada', $cotizacion->tasa_cambio_aplicada ?? $tasaDefault);
    $mostrarEquivInicial = old('mostrar_equivalente', $esEdicion ? ($cotizacion->mostrar_equivalente ? '1' : '0') : '1');
    $validaHastaInicial = old('valida_hasta', ($cotizacion && $cotizacion->valida_hasta) ? $cotizacion->valida_hasta->format('Y-m-d') : now()->addDays(7)->format('Y-m-d'));
    $descuentoInicial = old('descuento', $cotizacion->descuento ?? 0);

    $itemsParaJs = old('items', $itemsIniciales ?? []);
    if (!is_array($itemsParaJs)) {
        $itemsParaJs = [];
    }
    $itemsParaJs = array_values(array_map(function ($it, $idx) {
        return [
            'key'             => $it['key'] ?? ('item_' . $idx . '_' . uniqid()),
            'id_producto'     => !empty($it['id_producto']) ? intval($it['id_producto']) : null,
            'codigo'          => $it['codigo'] ?? '',
            'nombre'          => $it['nombre'] ?? '',
            'unidad'          => $it['unidad'] ?? 'Pieza',
            'es_externo'      => !empty($it['es_externo']),
            'cantidad'        => floatval($it['cantidad'] ?? 1),
            'precio_unitario' => floatval($it['precio_unitario'] ?? 0),
        ];
    }, $itemsParaJs, array_keys($itemsParaJs)));

    $clientesJson = $clientes->map(function ($c) {
        return [
            'id'        => $c->id,
            'nombre'    => $c->nombre,
            'telefono'  => $c->telefono ?? '',
            'ci_nit'    => $c->ci_nit ?? '',
            'direccion' => $c->direccion ?? '',
        ];
    })->values();
@endphp

<style>
    .cot-header-card {
        background: linear-gradient(135deg, #0f172a 0%, #0369a1 100%);
        border-radius: 14px;
        color: #fff;
    }
    .catalog-scroll {
        max-height: 560px;
        overflow-y: auto;
    }
    .catalog-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .catalog-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .fila-catalogo-prod {
        transition: background-color 0.15s ease;
    }
    .fila-catalogo-prod:hover {
        background-color: #f0f9ff !important;
    }
    .tipo-pill-btn {
        cursor: pointer;
        border: 2px solid #cbd5e1;
        border-radius: 10px;
        padding: 10px 14px;
        transition: all 0.15s ease;
        background: #fff;
    }
    .tipo-pill-btn.active-cliente {
        border-color: #0284c7;
        background: #f0f9ff;
        color: #0369a1;
        box-shadow: 0 2px 8px rgba(2, 132, 199, 0.15);
    }
    .tipo-pill-btn.active-tienda {
        border-color: #7c3aed;
        background: #f5f3ff;
        color: #5b21b6;
        box-shadow: 0 2px 8px rgba(124, 58, 237, 0.15);
    }
    .cot-table-items th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #475569;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }
    .cot-table-items td {
        vertical-align: middle;
    }
    .total-panel {
        background: linear-gradient(135deg, #f8fafc 0%, #f0f9ff 100%);
        border: 2px solid #bae6fd;
        border-radius: 14px;
    }
    .btn-cantidad-step {
        width: 28px;
        height: 28px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        border-radius: 6px;
    }
</style>

<div class="container-fluid py-3">
    <!-- ENCABEZADO -->
    <div class="cot-header-card p-3 px-4 mb-3 shadow-sm d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-info text-dark fw-bold">{{ $esEdicion ? 'EDICIÓN' : 'NUEVA COTIZACIÓN' }}</span>
                <span class="badge bg-white bg-opacity-25 text-white fw-semibold">{{ $numeroSugerido }}</span>
            </div>
            <h4 class="fw-bold mb-0">
                <i class="bi bi-file-earmark-text-fill me-2"></i>
                {{ $esEdicion ? 'Editar Cotización ' . $cotizacion->numero_cotizacion : 'Nueva Cotización y Proforma' }}
            </h4>
            <div class="small text-white-50 mt-1">
                Agrega productos de tu catálogo o conceptos libres, cotiza en Soles o Bolivianos y genera de inmediato el PDF o envío por WhatsApp.
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('cotizaciones.index') }}" class="btn btn-outline-light btn-sm fw-semibold px-3">
                <i class="bi bi-arrow-left me-1"></i> Volver al Historial
            </a>
            @if($esEdicion)
                <a href="{{ route('cotizaciones.pdf', $cotizacion->id) }}" target="_blank" class="btn btn-light btn-sm fw-bold text-danger">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Descargar PDF
                </a>
            @endif
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
            <strong><i class="bi bi-exclamation-octagon-fill me-1"></i> Por favor revisa los datos:</strong>
            <ul class="mb-0 mt-1 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 align-items-start">
        <!-- COLUMNA IZQUIERDA: CATÁLOGO DE PRODUCTOS (RENDERIZADO INMEDIATO) -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-box-seam-fill text-primary me-2"></i>Catálogo de Productos
                            <span class="badge bg-secondary-subtle text-secondary border ms-1" id="badgeTotalCatalogo">{{ count($productos) }}</span>
                        </h6>
                        <button type="button" class="btn btn-outline-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalItemExterno">
                            <i class="bi bi-plus-lg me-1"></i> + Ítem Libre / Externo
                        </button>
                    </div>

                    <!-- Buscador y Categoría -->
                    <div class="row g-2 mb-2">
                        <div class="col-7">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="buscarCatalogo" class="form-control border-start-0" placeholder="Buscar producto, código o marca..." autofocus>
                            </div>
                        </div>
                        <div class="col-5">
                            <select id="filtroCategoriaCatalogo" class="form-select form-select-sm">
                                <option value="">Todas las categorías</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Tarifa por defecto al agregar -->
                    <div class="d-flex align-items-center justify-content-between bg-light rounded-3 px-2 py-1 border">
                        <span class="small fw-semibold text-muted"><i class="bi bi-tag-fill me-1"></i>Tarifa:</span>
                        <div class="btn-group btn-group-sm" role="group">
                            <input type="radio" class="btn-check" name="tarifa_catalogo" id="tarifaVenta" value="venta" checked>
                            <label class="btn btn-outline-primary fw-semibold py-0 px-2" for="tarifaVenta">P. Venta</label>

                            <input type="radio" class="btn-check" name="tarifa_catalogo" id="tarifaCliente" value="cliente">
                            <label class="btn btn-outline-success fw-semibold py-0 px-2" for="tarifaCliente">Ferretería / Cliente</label>
                        </div>
                    </div>

                    <div id="avisoConversionCatalogo" class="alert alert-info py-1 px-2 mt-2 mb-0 small {{ ($monedaPrincipal ?? 'BOB') === $monedaInicial ? 'd-none' : '' }}">
                        <i class="bi bi-arrow-left-right me-1"></i>
                        Precios del catálogo se muestran convertidos a <strong id="monedaCotDestinoLbl">{{ $monedaInicial === 'PEN' ? 'Soles (S/)' : 'Bolivianos (Bs.)' }}</strong> según tasa.
                    </div>
                </div>

                <!-- Tabla de productos del catálogo (visible al 100% desde la primera carga) -->
                <div class="card-body p-0 catalog-scroll" id="contenedorCatalogo">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tablaCatalogoProductos">
                            <thead class="table-light sticky-top small" style="z-index: 2;">
                                <tr>
                                    <th class="ps-3" style="width: 18%;">CÓDIGO</th>
                                    <th>PRODUCTO</th>
                                    <th class="text-center" style="width: 14%;">STOCK</th>
                                    <th class="text-end" style="width: 22%;">PRECIO</th>
                                    <th class="text-center pe-3" style="width: 18%;">ACCIÓN</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyCatalogo">
                                @forelse($productos as $p)
                                @php
                                    $unidadBase = optional($p->unidad)->nombre ?? 'Pieza';
                                    $pClienteBase = ($p->precio_cliente !== null && $p->precio_cliente > 0) ? floatval($p->precio_cliente) : floatval($p->precio_venta);
                                    $pVentaBase = floatval($p->precio_venta);

                                    $tieneSec = $p->tieneUnidadSecundaria();
                                    $unidadSec = $tieneSec ? (optional($p->unidadSecundaria)->nombre ?? 'Paquete') : '';
                                    $pVentaSec = $tieneSec ? $p->getPrecioSecundarioPara('Ocasional') : 0;
                                    $pClienteSec = $tieneSec ? $p->getPrecioSecundarioPara('Regular') : 0;
                                @endphp
                                <tr class="fila-catalogo-prod"
                                    data-id="{{ $p->id }}"
                                    data-codigo="{{ strtolower($p->codigo ?? '') }}"
                                    data-nombre="{{ strtolower($p->nombre) }}"
                                    data-marca="{{ strtolower(optional($p->marca)->nombre ?? '') }}"
                                    data-categoria="{{ $p->id_categoria }}"
                                    data-stock="{{ $p->stock }}"
                                    data-nombre-real="{{ $p->nombre }}"
                                    data-codigo-real="{{ $p->codigo ?? '' }}"
                                    data-unidad-base="{{ $unidadBase }}"
                                    data-pventa-base="{{ $pVentaBase }}"
                                    data-pcliente-base="{{ $pClienteBase }}"
                                    data-tiene-sec="{{ $tieneSec ? '1' : '0' }}"
                                    data-unidad-sec="{{ $unidadSec }}"
                                    data-pventa-sec="{{ $pVentaSec }}"
                                    data-pcliente-sec="{{ $pClienteSec }}">
                                    <td class="ps-3">
                                        <span class="badge bg-light text-dark border small" style="font-size: 10px;">{{ $p->codigo ?: '—' }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 190px;" title="{{ $p->nombre }}">{{ $p->nombre }}</div>
                                        <div class="text-muted" style="font-size: 11px;">
                                            {{ optional($p->marca)->nombre ?? '' }} • {{ $unidadBase }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($p->stock > 0)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $p->stock }}</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">0</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold text-primary precio-visible-catalogo" data-pventa="{{ $pVentaBase }}" data-pcliente="{{ $pClienteBase }}">
                                            <span class="lbl-simbolo-moneda">{{ $simboloInicial }}</span> <span class="val-num-catalogo">{{ number_format($pVentaBase, 2) }}</span>
                                        </div>
                                        <small class="text-muted tarifa-lbl-catalogo" style="font-size: 10px;">P. Venta</small>
                                    </td>
                                    <td class="text-center pe-3">
                                        <button type="button" class="btn btn-sm btn-primary fw-bold px-2 py-1" onclick="agregarProductoDesdeFila(this, false)">
                                            <i class="bi bi-plus-lg"></i> Agregar
                                        </button>
                                        @if($tieneSec && $unidadSec)
                                            <button type="button" class="btn btn-sm btn-outline-secondary fw-semibold mt-1 py-0 px-1 d-block w-100" style="font-size: 10px;" onclick="agregarProductoDesdeFila(this, true)" title="Agregar por {{ $unidadSec }}">
                                                + {{ $unidadSec }}
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No hay productos registrados en el inventario.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div id="noResultadosCatalogo" class="text-center py-5 text-muted d-none">
                        <i class="bi bi-search fs-3 opacity-50"></i>
                        <p class="small mt-2 mb-0">No se encontraron productos con ese criterio de búsqueda.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: FORMULARIO Y DETALLE DE LA COTIZACIÓN -->
        <div class="col-lg-7">
            <form id="formCotizacion" method="POST" action="{{ $esEdicion ? route('cotizaciones.update', $cotizacion->id) : route('cotizaciones.store') }}">
                @csrf
                @if($esEdicion)
                    @method('PUT')
                @endif
                <input type="hidden" name="accion_post" id="accionPostInput" value="guardar">
                <input type="hidden" name="id_cliente" id="idClienteInput" value="{{ old('id_cliente', $cotizacion->id_cliente ?? '') }}">

                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body p-3 p-md-4">
                        <!-- SECCIÓN 1: TIPO DE COTIZACIÓN -->
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <h6 class="fw-bold text-dark mb-0">
                                <i class="bi bi-person-badge-fill text-primary me-2"></i>1. Destinatario de la Cotización
                            </h6>
                            <div class="d-flex align-items-center gap-2">
                                <label class="small text-muted fw-semibold mb-0">N° Cotización:</label>
                                <input type="text" name="numero_cotizacion" class="form-control form-control-sm fw-bold text-primary text-center"
                                       style="width: 140px;" value="{{ old('numero_cotizacion', $numeroSugerido) }}"
                                       {{ $esEdicion ? 'readonly' : '' }}>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="tipo-pill-btn {{ $tipoInicial === 'cliente' ? 'active-cliente' : '' }}" id="pillTipoCliente" onclick="seleccionarTipoCotizacion('cliente')">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-person-fill fs-4 text-primary"></i>
                                        <div>
                                            <div class="fw-bold small">Para un Cliente</div>
                                            <div class="text-muted" style="font-size: 11px;">Cliente final, obra o frecuente</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="tipo-pill-btn {{ $tipoInicial === 'tienda_externa' ? 'active-tienda' : '' }}" id="pillTipoTienda" onclick="seleccionarTipoCotizacion('tienda_externa')">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-shop-window fs-4" style="color: #7c3aed;"></i>
                                        <div>
                                            <div class="fw-bold small">Tienda Externa / Ferretería</div>
                                            <div class="text-muted" style="font-size: 11px;">Cotización comercial o proveedor</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="tipo_cotizacion" id="tipoCotizacionInput" value="{{ $tipoInicial }}">
                        </div>

                        <!-- Selector rápido de clientes / ferreterías registrados -->
                        <div class="bg-light border rounded-3 p-2.5 mb-3">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold text-muted mb-1">
                                        <i class="bi bi-people-fill text-primary me-1"></i>Autocompletar con Cliente / Ferretería Registrada:
                                    </label>
                                    <select id="selectorClienteRegistrado" class="form-select form-select-sm">
                                        <option value="">-- Seleccionar cliente para autocompletar WhatsApp y datos --</option>
                                        @foreach($clientes as $cli)
                                            <option value="{{ $cli->id }}" {{ old('id_cliente', $cotizacion->id_cliente ?? '') == $cli->id ? 'selected' : '' }}>
                                                {{ $cli->nombre }} {{ $cli->telefono ? '— Tel: ' . $cli->telefono : '' }} {{ $cli->ci_nit ? '(NIT: ' . $cli->ci_nit . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted mb-1">Válida Hasta</label>
                                    <input type="date" name="valida_hasta" class="form-control form-control-sm" value="{{ $validaHastaInicial }}">
                                </div>
                            </div>
                        </div>

                        <!-- Campos del destinatario -->
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold mb-1" id="lblDestinatarioNombre">
                                    {{ $tipoInicial === 'tienda_externa' ? 'Contacto / Encargado de Tienda *' : 'Nombre del Cliente *' }}
                                </label>
                                <input type="text" name="destinatario_nombre" id="destinatarioNombre" class="form-control form-control-sm"
                                       placeholder="Ej. Juan Pérez" value="{{ old('destinatario_nombre', $cotizacion->destinatario_nombre ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold mb-1" id="lblDestinatarioEmpresa">
                                    {{ $tipoInicial === 'tienda_externa' ? 'Nombre de la Tienda Externa / Ferretería' : 'Empresa / Proyecto / Ferretería (Opcional)' }}
                                </label>
                                <input type="text" name="destinatario_empresa" id="destinatarioEmpresa" class="form-control form-control-sm"
                                       placeholder="Ej. Ferretería Central S.R.L." value="{{ old('destinatario_empresa', $cotizacion->destinatario_empresa ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold mb-1">
                                    <i class="bi bi-whatsapp text-success me-1"></i>WhatsApp / Teléfono
                                </label>
                                <input type="text" name="destinatario_telefono" id="destinatarioTelefono" class="form-control form-control-sm"
                                       placeholder="Ej. 987654321 ó 78003571" value="{{ old('destinatario_telefono', $cotizacion->destinatario_telefono ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold mb-1">NIT / RUC / CI</label>
                                <input type="text" name="destinatario_nit" id="destinatarioNit" class="form-control form-control-sm"
                                       placeholder="Documento o RUC" value="{{ old('destinatario_nit', $cotizacion->destinatario_nit ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold mb-1">Dirección / Ciudad</label>
                                <input type="text" name="destinatario_direccion" id="destinatarioDireccion" class="form-control form-control-sm"
                                       placeholder="Dirección de entrega" value="{{ old('destinatario_direccion', $cotizacion->destinatario_direccion ?? '') }}">
                            </div>
                        </div>

                        <hr class="my-3">

                        <!-- SECCIÓN 2: MONEDA Y CONVERSIÓN -->
                        <div class="row g-2 align-items-center bg-light border rounded-3 p-2">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold mb-1 text-dark">
                                    <i class="bi bi-currency-exchange text-primary me-1"></i>Moneda de la Cotización
                                </label>
                                <select name="moneda" id="selectMonedaCotizacion" class="form-select form-select-sm fw-bold">
                                    <option value="BOB" {{ $monedaInicial === 'BOB' ? 'selected' : '' }}>🇧🇴 Bolivianos (Bs.)</option>
                                    <option value="PEN" {{ $monedaInicial === 'PEN' ? 'selected' : '' }}>🇵🇪 Soles Peruanos (S/)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold mb-1 text-dark">
                                    Tasa Ref. Ragnar (1 S/ = Bs.)
                                </label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Bs.</span>
                                    <input type="number" step="0.0001" min="0.01" name="tasa_cambio_aplicada" id="inputTasaCambio"
                                           class="form-control fw-bold" value="{{ number_format(floatval($tasaInicial), 2, '.', '') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch mt-3 pt-1">
                                    <input type="hidden" name="mostrar_equivalente" value="0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="mostrar_equivalente" id="chkMostrarEquivalente" value="1"
                                           {{ $mostrarEquivInicial == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold" for="chkMostrarEquivalente">
                                        Mostrar equivalente en PDF y WA
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 3: TABLA DE PRODUCTOS COTIZADOS -->
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-header bg-white border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-list-check text-primary me-2"></i>2. Productos e Ítems Cotizados
                            <span class="badge bg-primary rounded-pill ms-1" id="badgeCantidadItems">0</span>
                        </h6>
                        <button type="button" class="btn btn-sm btn-link text-danger text-decoration-none fw-semibold p-0" onclick="vaciarItemsCotizacion()">
                            <i class="bi bi-trash3 me-1"></i>Limpiar lista
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover cot-table-items mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 36%;">Producto / Descripción</th>
                                        <th class="text-center" style="width: 14%;">Unidad</th>
                                        <th class="text-center" style="width: 17%;">Cant.</th>
                                        <th class="text-end" style="width: 16%;">P. Unit (<span class="lbl-simbolo-moneda">{{ $simboloInicial }}</span>)</th>
                                        <th class="text-end" style="width: 13%;">Subtotal</th>
                                        <th class="text-center" style="width: 4%;"></th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyItemsCotizacion">
                                    <!-- Renderizado interactivo -->
                                </tbody>
                            </table>
                        </div>
                        <div id="emptyStateItems" class="text-center py-5 text-muted">
                            <i class="bi bi-cart-plus fs-1 text-secondary opacity-50"></i>
                            <p class="fw-semibold mb-1 mt-2">Aún no agregaste productos a esta cotización</p>
                            <small class="text-secondary">
                                Haz clic en <strong>+ Agregar</strong> en los productos de la izquierda o añade un <strong>+ Ítem Libre / Externo</strong>.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 4: CONDICIONES, TOTALES Y ACCIÓN DE GUARDADO ÚNICA -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold mb-1">Condiciones Comerciales y Validez</label>
                                <textarea name="condiciones" id="inputCondiciones" rows="3" class="form-control form-control-sm mb-2"
                                          placeholder="Ej. Precios válidos por 7 días. Sujeto a disponibilidad de stock.">{{ old('condiciones', $cotizacion->condiciones ?? 'Precios vigentes hasta la fecha indicada. Sujeto a disponibilidad de stock.') }}</textarea>
                                <label class="form-label small fw-bold mb-1">Observaciones para el Cliente o Internas</label>
                                <textarea name="observaciones" rows="2" class="form-control form-control-sm"
                                          placeholder="Notas adicionales, tiempo de entrega, datos bancarios...">{{ old('observaciones', $cotizacion->observaciones ?? '') }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <div class="total-panel p-3 h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="small fw-semibold text-muted">Subtotal</span>
                                            <span class="fw-bold text-dark fs-6" id="lblSubtotalCot">{{ $simboloInicial }} 0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="small fw-semibold text-danger mb-0" for="inputDescuentoCot">Descuento (-)</label>
                                            <div class="input-group input-group-sm" style="width: 140px;">
                                                <span class="input-group-text lbl-simbolo-moneda">{{ $simboloInicial }}</span>
                                                <input type="number" step="0.01" min="0" name="descuento" id="inputDescuentoCot"
                                                       class="form-control text-end fw-bold text-danger" value="{{ $descuentoInicial }}">
                                            </div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-extrabold text-primary fs-6">TOTAL COTIZADO</span>
                                            <span class="fw-extrabold text-primary fs-4" id="lblTotalCot">{{ $simboloInicial }} 0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-1 small text-secondary" id="boxEquivalenteLive">
                                            <span>Equivalente ref.:</span>
                                            <strong id="lblEquivalenteCot" class="text-dark"></strong>
                                        </div>
                                    </div>

                                    <!-- BOTÓN DE GUARDADO PRINCIPAL CLARO Y DIRECTO -->
                                    <div class="mt-3">
                                        <button type="button" onclick="guardarCotizacionDirecta()" class="btn btn-primary btn-lg w-100 fw-bold py-3 shadow-sm d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-check2-circle fs-4"></i>
                                            <span class="fs-5">{{ $esEdicion ? 'Actualizar Cotización' : 'Guardar Cotización' }}</span>
                                        </button>
                                        <div class="text-center text-muted small mt-2">
                                            <i class="bi bi-info-circle me-1"></i> Al guardar se abrirá el panel para <strong>descargar PDF</strong>, <strong>imprimir</strong> o <strong>enviar por WhatsApp</strong>.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: AGREGAR ÍTEM EXTERNO O PERSONALIZADO -->
<div class="modal fade" id="modalItemExterno" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-primary text-white rounded-top-4">
                <h6 class="modal-title fw-bold mb-0">
                    <i class="bi bi-plus-square-dotted me-2"></i>Agregar Producto Externo o Concepto Libre
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="small text-muted mb-3">
                    Útil para cotizar productos de terceros, servicios, fletes o artículos que no están en tu inventario.
                </p>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Descripción del Producto o Concepto *</label>
                    <input type="text" id="extNombre" class="form-control" placeholder="Ej. Taladro Percutor 800W / Servicio de flete">
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Código (Opcional)</label>
                        <input type="text" id="extCodigo" class="form-control" placeholder="Ej. EXT-01">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Unidad</label>
                        <input type="text" id="extUnidad" class="form-control" value="Pieza" placeholder="Pieza, Caja, Und...">
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Cantidad *</label>
                        <input type="number" step="0.01" min="0.01" id="extCantidad" class="form-control" value="1">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Precio Unitario (<span class="lbl-simbolo-moneda">{{ $simboloInicial }}</span>) *</label>
                        <input type="number" step="0.01" min="0" id="extPrecio" class="form-control" value="0.00">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-light fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary fw-bold px-4" onclick="agregarItemExterno()">
                    <i class="bi bi-plus-circle me-1"></i> Agregar a Cotización
                </button>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS DE COMPORTAMIENTO DIRECTO EN LA VISTA -->
<script>
    const CLIENTES_LISTA = @json($clientesJson);
    const MONEDA_SISTEMA_BASE = @json($monedaPrincipal ?? 'BOB');
    let itemsCotizacion = @json($itemsParaJs);
    let monedaAnterior = document.getElementById('selectMonedaCotizacion') ? document.getElementById('selectMonedaCotizacion').value : 'BOB';

    function escHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function simboloActual() {
        const el = document.getElementById('selectMonedaCotizacion');
        return (el && el.value === 'PEN') ? 'S/' : 'Bs.';
    }

    function simboloOpuesto() {
        const el = document.getElementById('selectMonedaCotizacion');
        return (el && el.value === 'PEN') ? 'Bs.' : 'S/';
    }

    function tasaCambioActual() {
        const val = parseFloat(document.getElementById('inputTasaCambio').value);
        return (!isNaN(val) && val > 0) ? val : 3.60;
    }

    function tarifaCatalogoActiva() {
        const rCli = document.getElementById('tarifaCliente');
        return (rCli && rCli.checked) ? 'cliente' : 'venta';
    }

    function convertirDesdeSistemaACotizacion(montoBase) {
        const monCot = document.getElementById('selectMonedaCotizacion').value;
        const tasa = tasaCambioActual();
        let res = parseFloat(montoBase) || 0;
        if (MONEDA_SISTEMA_BASE !== monCot) {
            if (MONEDA_SISTEMA_BASE === 'PEN' && monCot === 'BOB') {
                res = res * tasa;
            } else if (MONEDA_SISTEMA_BASE === 'BOB' && monCot === 'PEN') {
                res = res / tasa;
            }
        }
        return Math.round(res * 100) / 100;
    }

    function filtrarCatalogo() {
        const q = (document.getElementById('buscarCatalogo').value || '').toLowerCase().trim();
        const catId = document.getElementById('filtroCategoriaCatalogo').value;
        const filas = document.querySelectorAll('.fila-catalogo-prod');
        let visibles = 0;

        filas.forEach(f => {
            const fCat = f.getAttribute('data-categoria');
            if (catId && String(fCat) !== String(catId)) {
                f.style.display = 'none';
                return;
            }
            if (q) {
                const cod = f.getAttribute('data-codigo') || '';
                const nom = f.getAttribute('data-nombre') || '';
                const mar = f.getAttribute('data-marca') || '';
                if (!cod.includes(q) && !nom.includes(q) && !mar.includes(q)) {
                    f.style.display = 'none';
                    return;
                }
            }
            f.style.display = '';
            visibles++;
        });

        const noRes = document.getElementById('noResultadosCatalogo');
        if (noRes) {
            noRes.classList.toggle('d-none', visibles > 0);
        }
        const badge = document.getElementById('badgeTotalCatalogo');
        if (badge) {
            badge.textContent = visibles;
        }
    }

    function actualizarPreciosCatalogo() {
        const tarifa = tarifaCatalogoActiva();
        const sim = simboloActual();
        const monCot = document.getElementById('selectMonedaCotizacion').value;

        const aviso = document.getElementById('avisoConversionCatalogo');
        if (aviso) {
            if (MONEDA_SISTEMA_BASE !== monCot) {
                document.getElementById('monedaCotDestinoLbl').textContent = monCot === 'PEN' ? 'Soles (S/)' : 'Bolivianos (Bs.)';
                aviso.classList.remove('d-none');
            } else {
                aviso.classList.add('d-none');
            }
        }

        document.querySelectorAll('.lbl-simbolo-moneda').forEach(el => el.textContent = sim);

        document.querySelectorAll('.fila-catalogo-prod').forEach(f => {
            const pVenta = parseFloat(f.getAttribute('data-pventa-base')) || 0;
            const pCli = parseFloat(f.getAttribute('data-pcliente-base')) || 0;
            const montoBase = tarifa === 'cliente' ? pCli : pVenta;
            const convertido = convertirDesdeSistemaACotizacion(montoBase);

            const valNumEl = f.querySelector('.val-num-catalogo');
            if (valNumEl) {
                valNumEl.textContent = convertido.toFixed(2);
            }
            const tarifaLbl = f.querySelector('.tarifa-lbl-catalogo');
            if (tarifaLbl) {
                tarifaLbl.textContent = tarifa === 'cliente' ? 'P. Cliente' : 'P. Venta';
            }
        });
    }

    function agregarProductoDesdeFila(btn, usarSecundaria) {
        const fila = btn.closest('.fila-catalogo-prod');
        if (!fila) return;

        const prodId = parseInt(fila.getAttribute('data-id'));
        const nombre = fila.getAttribute('data-nombre-real');
        const codigo = fila.getAttribute('data-codigo-real') || '';
        const tarifa = tarifaCatalogoActiva();

        let unidad = fila.getAttribute('data-unidad-base');
        let precioBase = tarifa === 'cliente'
            ? parseFloat(fila.getAttribute('data-pcliente-base'))
            : parseFloat(fila.getAttribute('data-pventa-base'));

        if (usarSecundaria) {
            unidad = fila.getAttribute('data-unidad-sec') || unidad;
            precioBase = tarifa === 'cliente'
                ? parseFloat(fila.getAttribute('data-pcliente-sec'))
                : parseFloat(fila.getAttribute('data-pventa-sec'));
        }

        const precioFinal = convertirDesdeSistemaACotizacion(precioBase);

        const existente = itemsCotizacion.find(it =>
            it.id_producto === prodId && !it.es_externo && it.unidad === unidad
        );

        if (existente) {
            existente.cantidad = Math.round((parseFloat(existente.cantidad) + 1) * 100) / 100;
        } else {
            itemsCotizacion.push({
                key: 'prod_' + prodId + '_' + Date.now() + '_' + Math.floor(Math.random() * 1000),
                id_producto: prodId,
                codigo: codigo,
                nombre: nombre,
                unidad: unidad || 'Pieza',
                es_externo: false,
                cantidad: 1,
                precio_unitario: precioFinal,
            });
        }

        renderizarItems();

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: `Agregado: ${nombre}`,
                showConfirmButton: false,
                timer: 1200
            });
        }
    }

    function agregarItemExterno() {
        const nombre = document.getElementById('extNombre').value.trim();
        const codigo = document.getElementById('extCodigo').value.trim();
        const unidad = document.getElementById('extUnidad').value.trim() || 'Pieza';
        const cantidad = parseFloat(document.getElementById('extCantidad').value) || 0;
        const precio = parseFloat(document.getElementById('extPrecio').value) || 0;

        if (!nombre) {
            Swal.fire({ icon: 'warning', title: 'Falta descripción', text: 'Ingresa el nombre del producto o concepto.' });
            return;
        }
        if (cantidad <= 0) {
            Swal.fire({ icon: 'warning', title: 'Cantidad inválida', text: 'La cantidad debe ser mayor a 0.' });
            return;
        }

        itemsCotizacion.push({
            key: 'ext_' + Date.now() + '_' + Math.floor(Math.random() * 1000),
            id_producto: null,
            codigo: codigo,
            nombre: nombre,
            unidad: unidad,
            es_externo: true,
            cantidad: cantidad,
            precio_unitario: precio,
        });

        document.getElementById('extNombre').value = '';
        document.getElementById('extCodigo').value = '';
        document.getElementById('extUnidad').value = 'Pieza';
        document.getElementById('extCantidad').value = '1';
        document.getElementById('extPrecio').value = '0.00';

        const modalEl = document.getElementById('modalItemExterno');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

        renderizarItems();
    }

    function actualizarCampoItem(index, campo, valor) {
        if (!itemsCotizacion[index]) return;
        if (campo === 'cantidad' || campo === 'precio_unitario') {
            const num = parseFloat(valor);
            itemsCotizacion[index][campo] = isNaN(num) ? 0 : Math.max(0, num);
            recalcularFilaYTotales(index);
        } else {
            itemsCotizacion[index][campo] = valor;
        }
    }

    function pasoCantidad(index, delta) {
        if (!itemsCotizacion[index]) return;
        const actual = parseFloat(itemsCotizacion[index].cantidad) || 0;
        const nuevo = Math.max(1, actual + delta);
        itemsCotizacion[index].cantidad = Math.round(nuevo * 100) / 100;
        renderizarItems();
    }

    function recalcularFilaYTotales(index) {
        const item = itemsCotizacion[index];
        if (item) {
            const sub = (parseFloat(item.cantidad) || 0) * (parseFloat(item.precio_unitario) || 0);
            const cel = document.getElementById(`sub_item_${index}`);
            if (cel) cel.textContent = `${simboloActual()} ${sub.toFixed(2)}`;
        }
        recalcularTotales();
    }

    function eliminarItem(index) {
        itemsCotizacion.splice(index, 1);
        renderizarItems();
    }

    function vaciarItemsCotizacion() {
        if (itemsCotizacion.length === 0) return;
        itemsCotizacion = [];
        renderizarItems();
    }

    function renderizarItems() {
        const tbody = document.getElementById('tbodyItemsCotizacion');
        const empty = document.getElementById('emptyStateItems');
        const badge = document.getElementById('badgeCantidadItems');
        const sim = simboloActual();

        document.querySelectorAll('.lbl-simbolo-moneda').forEach(el => el.textContent = sim);
        badge.textContent = itemsCotizacion.length;

        if (itemsCotizacion.length === 0) {
            tbody.innerHTML = '';
            empty.classList.remove('d-none');
            recalcularTotales();
            return;
        }

        empty.classList.add('d-none');
        let html = '';
        itemsCotizacion.forEach((it, idx) => {
            const cant = parseFloat(it.cantidad) || 0;
            const pu = parseFloat(it.precio_unitario) || 0;
            const sub = cant * pu;

            html += `
                <tr>
                    <td>
                        <input type="hidden" name="items[${idx}][id_producto]" value="${it.id_producto ?? ''}">
                        <input type="hidden" name="items[${idx}][codigo]" value="${escHtml(it.codigo)}">
                        <input type="hidden" name="items[${idx}][es_externo]" value="${it.es_externo ? 1 : 0}">
                        <div class="d-flex align-items-center gap-1 mb-1">
                            ${it.codigo ? `<span class="badge bg-light text-dark border" style="font-size:10px;">${escHtml(it.codigo)}</span>` : ''}
                            ${it.es_externo ? `<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size:10px;">Externo</span>` : ''}
                        </div>
                        <input type="text" name="items[${idx}][nombre]" class="form-control form-control-sm fw-semibold"
                               value="${escHtml(it.nombre)}" onchange="actualizarCampoItem(${idx}, 'nombre', this.value)" required>
                    </td>
                    <td>
                        <input type="text" name="items[${idx}][unidad]" class="form-control form-control-sm text-center"
                               value="${escHtml(it.unidad)}" onchange="actualizarCampoItem(${idx}, 'unidad', this.value)">
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <button type="button" class="btn btn-outline-secondary btn-cantidad-step" onclick="pasoCantidad(${idx}, -1)">-</button>
                            <input type="number" step="0.01" min="0.01" name="items[${idx}][cantidad]"
                                   class="form-control form-control-sm text-center fw-bold"
                                   value="${cant}" oninput="actualizarCampoItem(${idx}, 'cantidad', this.value)" required>
                            <button type="button" class="btn btn-outline-secondary btn-cantidad-step" onclick="pasoCantidad(${idx}, 1)">+</button>
                        </div>
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0" name="items[${idx}][precio_unitario]"
                               class="form-control form-control-sm text-end fw-bold"
                               value="${pu.toFixed(2)}" oninput="actualizarCampoItem(${idx}, 'precio_unitario', this.value)" required>
                    </td>
                    <td class="text-end fw-bold text-dark" id="sub_item_${idx}">${sim} ${sub.toFixed(2)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="eliminarItem(${idx})" title="Quitar">
                            <i class="bi bi-x-circle-fill fs-6"></i>
                        </button>
                    </td>
                </tr>`;
        });

        tbody.innerHTML = html;
        recalcularTotales();
    }

    function recalcularTotales() {
        const sim = simboloActual();
        const mon = document.getElementById('selectMonedaCotizacion').value;
        const tasa = tasaCambioActual();
        let subtotal = 0;

        itemsCotizacion.forEach(it => {
            subtotal += Math.round(((parseFloat(it.cantidad) || 0) * (parseFloat(it.precio_unitario) || 0)) * 100) / 100;
        });

        let descuento = parseFloat(document.getElementById('inputDescuentoCot').value) || 0;
        if (descuento < 0) descuento = 0;
        if (descuento > subtotal) descuento = subtotal;
        const total = Math.max(0, Math.round((subtotal - descuento) * 100) / 100);

        document.getElementById('lblSubtotalCot').textContent = `${sim} ${subtotal.toFixed(2)}`;
        document.getElementById('lblTotalCot').textContent = `${sim} ${total.toFixed(2)}`;

        const equivBox = document.getElementById('boxEquivalenteLive');
        const equivLbl = document.getElementById('lblEquivalenteCot');
        if (document.getElementById('chkMostrarEquivalente').checked && tasa > 0) {
            const equiv = mon === 'PEN' ? (total * tasa) : (total / tasa);
            equivLbl.textContent = `${simboloOpuesto()} ${equiv.toFixed(2)} (1 S/ = Bs. ${tasa.toFixed(2)})`;
            equivBox.classList.remove('d-none');
        } else {
            equivBox.classList.add('d-none');
        }
    }

    function seleccionarTipoCotizacion(tipo) {
        document.getElementById('tipoCotizacionInput').value = tipo;
        const pillC = document.getElementById('pillTipoCliente');
        const pillT = document.getElementById('pillTipoTienda');
        const lblNom = document.getElementById('lblDestinatarioNombre');
        const lblEmp = document.getElementById('lblDestinatarioEmpresa');

        pillC.classList.remove('active-cliente');
        pillT.classList.remove('active-tienda');

        if (tipo === 'tienda_externa') {
            pillT.classList.add('active-tienda');
            lblNom.textContent = 'Contacto / Encargado de Tienda *';
            lblEmp.textContent = 'Nombre de la Tienda Externa / Ferretería';
        } else {
            pillC.classList.add('active-cliente');
            lblNom.textContent = 'Nombre del Cliente *';
            lblEmp.textContent = 'Empresa / Proyecto / Ferretería (Opcional)';
        }
    }

    function guardarCotizacionDirecta() {
        const nombre = document.getElementById('destinatarioNombre').value.trim();
        if (!nombre) {
            Swal.fire({
                icon: 'warning',
                title: 'Falta Destinatario',
                text: 'Ingresa el nombre del cliente o contacto de la tienda.'
            });
            document.getElementById('destinatarioNombre').focus();
            return;
        }
        if (itemsCotizacion.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Cotización Vacía',
                text: 'Agrega al menos un producto del catálogo o un ítem libre antes de guardar.'
            });
            return;
        }

        document.getElementById('accionPostInput').value = 'guardar';
        document.getElementById('formCotizacion').submit();
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function () {
        const buscador = document.getElementById('buscarCatalogo');
        if (buscador) buscador.addEventListener('input', filtrarCatalogo);

        const filtroCat = document.getElementById('filtroCategoriaCatalogo');
        if (filtroCat) filtroCat.addEventListener('change', filtrarCatalogo);

        const rVenta = document.getElementById('tarifaVenta');
        if (rVenta) rVenta.addEventListener('change', actualizarPreciosCatalogo);

        const rCli = document.getElementById('tarifaCliente');
        if (rCli) rCli.addEventListener('change', actualizarPreciosCatalogo);

        const descInput = document.getElementById('inputDescuentoCot');
        if (descInput) descInput.addEventListener('input', recalcularTotales);

        const tasaInput = document.getElementById('inputTasaCambio');
        if (tasaInput) tasaInput.addEventListener('input', function () {
            actualizarPreciosCatalogo();
            recalcularTotales();
        });

        const chkEquiv = document.getElementById('chkMostrarEquivalente');
        if (chkEquiv) chkEquiv.addEventListener('change', recalcularTotales);

        const selCli = document.getElementById('selectorClienteRegistrado');
        if (selCli) {
            selCli.addEventListener('change', function () {
                const idSel = parseInt(this.value);
                document.getElementById('idClienteInput').value = idSel || '';
                if (!idSel) return;
                const cli = CLIENTES_LISTA.find(c => c.id === idSel);
                if (!cli) return;
                document.getElementById('destinatarioNombre').value = cli.nombre || '';
                document.getElementById('destinatarioTelefono').value = cli.telefono || '';
                document.getElementById('destinatarioNit').value = cli.ci_nit || '';
                document.getElementById('destinatarioDireccion').value = cli.direccion || '';
            });
        }

        const selMon = document.getElementById('selectMonedaCotizacion');
        if (selMon) {
            selMon.addEventListener('change', function () {
                const monNueva = this.value;
                const monOrig = monedaAnterior;
                monedaAnterior = monNueva;

                actualizarPreciosCatalogo();

                if (itemsCotizacion.length > 0 && monOrig !== monNueva) {
                    const tasa = tasaCambioActual();
                    Swal.fire({
                        title: `Moneda cambiada a ${monNueva === 'PEN' ? 'Soles (S/)' : 'Bolivianos (Bs.)'}`,
                        text: `¿Deseas convertir automáticamente los precios de los ${itemsCotizacion.length} ítem(s) usando la tasa 1 S/ = Bs. ${tasa.toFixed(2)}?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, convertir',
                        cancelButtonText: 'No, mantener números'
                    }).then(res => {
                        if (res.isConfirmed) {
                            const factor = (monOrig === 'PEN' && monNueva === 'BOB') ? tasa : (1 / tasa);
                            itemsCotizacion = itemsCotizacion.map(it => ({
                                ...it,
                                precio_unitario: Math.round((parseFloat(it.precio_unitario) || 0) * factor * 100) / 100
                            }));
                        }
                        renderizarItems();
                    });
                } else {
                    renderizarItems();
                }
            });
        }

        actualizarPreciosCatalogo();
        renderizarItems();
    });
</script>
@endsection
