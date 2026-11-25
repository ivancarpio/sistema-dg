@extends('layouts.app')

@section('title', $cotizacion ? 'Editar Cotización ' . $cotizacion->numero_cotizacion : 'Nueva Cotización')

@section('content')
@php
    $esEdicion = $cotizacion !== null;
    $monedaInicial = old('moneda', $cotizacion->moneda ?? ($monedaPrincipal ?? 'BOB'));
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

    $productosJson = $productos->map(function ($p) {
        return [
            'id'                        => $p->id,
            'codigo'                    => $p->codigo ?? '',
            'nombre'                    => $p->nombre,
            'marca'                     => optional($p->marca)->nombre ?? '',
            'categoria_id'              => $p->id_categoria,
            'categoria'                 => optional($p->categoria)->nombre ?? '',
            'stock'                     => intval($p->stock),
            'unidad'                    => optional($p->unidad)->nombre ?? 'Pieza',
            'precio_venta'              => floatval($p->getPrecioPara('Ocasional')),
            'precio_cliente'            => floatval($p->getPrecioPara('Regular')),
            'tiene_secundaria'          => $p->tieneUnidadSecundaria(),
            'unidad_secundaria'         => optional($p->unidadSecundaria)->nombre ?? '',
            'factor_conversion'         => floatval($p->factor_conversion ?? 1),
            'precio_venta_secundario'   => floatval($p->getPrecioSecundarioPara('Ocasional')),
            'precio_cliente_secundario' => floatval($p->getPrecioSecundarioPara('Regular')),
        ];
    })->values();

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
        max-height: 615px;
        overflow-y: auto;
    }
    .catalog-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .catalog-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .prod-catalog-card {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        transition: all 0.15s ease;
        background: #ffffff;
    }
    .prod-catalog-card:hover {
        border-color: #0284c7;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.08);
    }
    .tipo-pill-btn {
        cursor: pointer;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        padding: 10px 14px;
        transition: all 0.15s ease;
        background: #fff;
    }
    .tipo-pill-btn.active-cliente {
        border-color: #0284c7;
        background: #f0f9ff;
        color: #0369a1;
        box-shadow: 0 2px 8px rgba(2, 132, 199, 0.12);
    }
    .tipo-pill-btn.active-tienda {
        border-color: #7c3aed;
        background: #f5f3ff;
        color: #5b21b6;
        box-shadow: 0 2px 8px rgba(124, 58, 237, 0.12);
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
        border: 1.5px solid #bae6fd;
        border-radius: 12px;
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
                {{ $esEdicion ? 'Editar Cotización ' . $cotizacion->numero_cotizacion : 'Generador de Cotizaciones y Proformas' }}
            </h4>
            <div class="small text-white-50 mt-1">
                Cotiza productos del catálogo o ítems externos para clientes y tiendas externas con envío directo por PDF, Impresión y WhatsApp.
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('cotizaciones.index') }}" class="btn btn-outline-light btn-sm fw-semibold px-3">
                <i class="bi bi-arrow-left me-1"></i> Volver al Historial
            </a>
            @if($esEdicion)
                <a href="{{ route('cotizaciones.pdf', $cotizacion->id) }}" target="_blank" class="btn btn-light btn-sm fw-bold text-primary">
                    <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i> Ver PDF
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
            <strong><i class="bi bi-exclamation-octagon-fill me-1"></i> Revisa los datos de la cotización:</strong>
            <ul class="mb-0 mt-1 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 align-items-start">
        <!-- COLUMNA IZQUIERDA: CATÁLOGO DE PRODUCTOS -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-box-seam-fill text-primary me-2"></i>Catálogo de Productos
                        </h6>
                        <button type="button" class="btn btn-outline-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalItemExterno">
                            <i class="bi bi-plus-lg me-1"></i> Ítem Externo / Libre
                        </button>
                    </div>

                    <!-- Buscador y Categoría -->
                    <div class="row g-2 mb-2">
                        <div class="col-7">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="buscarCatalogo" class="form-control border-start-0" placeholder="Buscar producto, código o marca...">
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
                        <span class="small fw-semibold text-muted"><i class="bi bi-tag-fill me-1"></i>Tarifa al agregar:</span>
                        <div class="btn-group btn-group-sm" role="group">
                            <input type="radio" class="btn-check" name="tarifa_catalogo" id="tarifaVenta" value="venta" checked>
                            <label class="btn btn-outline-primary fw-semibold py-0 px-2" for="tarifaVenta">P. Venta</label>

                            <input type="radio" class="btn-check" name="tarifa_catalogo" id="tarifaCliente" value="cliente">
                            <label class="btn btn-outline-success fw-semibold py-0 px-2" for="tarifaCliente">Ferretería / Cliente</label>
                        </div>
                    </div>

                    <div id="avisoConversionCatalogo" class="alert alert-info py-1 px-2 mt-2 mb-0 small d-none">
                        <i class="bi bi-arrow-left-right me-1"></i>
                        Catálogo base en <strong>{{ $monedaSimbolo }}</strong>: al agregar, los precios se convertirán automáticamente a <strong id="monedaCotDestinoLbl"></strong>.
                    </div>
                </div>

                <div class="card-body p-2 bg-light catalog-scroll" id="contenedorCatalogo">
                    <!-- Renderizado dinámico con JS para respuesta instantánea -->
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
                                <i class="bi bi-person-badge-fill text-primary me-2"></i>Tipo y Datos del Destinatario
                            </h6>
                            <div class="d-flex align-items-center gap-2">
                                <label class="small text-muted fw-semibold mb-0">N°:</label>
                                <input type="text" name="numero_cotizacion" class="form-control form-control-sm fw-bold text-primary text-center"
                                       style="width: 135px;" value="{{ old('numero_cotizacion', $numeroSugerido) }}"
                                       {{ $esEdicion ? 'readonly' : '' }}>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="tipo-pill-btn {{ $tipoInicial === 'cliente' ? 'active-cliente' : '' }}" id="pillTipoCliente" onclick="seleccionarTipoCotizacion('cliente')">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-person-fill fs-5"></i>
                                        <div>
                                            <div class="fw-bold small">Cotizar a Cliente</div>
                                            <div class="text-muted" style="font-size: 11px;">Cliente final, obra o frecuente</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="tipo-pill-btn {{ $tipoInicial === 'tienda_externa' ? 'active-tienda' : '' }}" id="pillTipoTienda" onclick="seleccionarTipoCotizacion('tienda_externa')">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-shop-window fs-5"></i>
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
                        <div class="bg-light border rounded-3 p-2 mb-3">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold text-muted mb-1">
                                        <i class="bi bi-people-fill text-primary me-1"></i>Cargar Cliente o Ferretería Registrada (Opcional)
                                    </label>
                                    <select id="selectorClienteRegistrado" class="form-select form-select-sm">
                                        <option value="">-- Seleccionar para autocompletar datos y WhatsApp --</option>
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

                        <!-- SECCIÓN 2: MONEDA Y TASA DE CAMBIO -->
                        <div class="row g-2 align-items-center bg-light border rounded-3 p-2">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold mb-1 text-dark">
                                    <i class="bi bi-currency-exchange text-primary me-1"></i>Moneda de Cotización
                                </label>
                                <select name="moneda" id="selectMonedaCotizacion" class="form-select form-select-sm fw-bold">
                                    <option value="BOB" {{ $monedaInicial === 'BOB' ? 'selected' : '' }}>🇧🇴 Bolivianos (Bs.)</option>
                                    <option value="PEN" {{ $monedaInicial === 'PEN' ? 'selected' : '' }}>🇵🇪 Soles Peruanos (S/)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold mb-1 text-dark">
                                    Tasa Ref. (1 S/ = Bs.)
                                </label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Bs.</span>
                                    <input type="number" step="0.0001" min="0.01" name="tasa_cambio_aplicada" id="inputTasaCambio"
                                           class="form-control fw-bold" value="{{ number_format(floatval($tasaInicial), 2, '.', '') }}">
                                    <button type="button" class="btn btn-outline-secondary" onclick="convertirTodosLosPrecios()" title="Recalcular precios unitarios según tasa">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch mt-3 pt-1">
                                    <input type="hidden" name="mostrar_equivalente" value="0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="mostrar_equivalente" id="chkMostrarEquivalente" value="1"
                                           {{ $mostrarEquivInicial == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold" for="chkMostrarEquivalente">
                                        Mostrar equivalente en PDF / WA
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
                            <i class="bi bi-list-check text-primary me-2"></i>Detalle de Productos e Ítems
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
                                        <th style="width: 36%;">Producto / Ítem</th>
                                        <th class="text-center" style="width: 14%;">Unidad</th>
                                        <th class="text-center" style="width: 15%;">Cant.</th>
                                        <th class="text-end" style="width: 18%;">P. Unit (<span class="lbl-simbolo-moneda">{{ $monedaInicial === 'PEN' ? 'S/' : 'Bs.' }}</span>)</th>
                                        <th class="text-end" style="width: 13%;">Subtotal</th>
                                        <th class="text-center" style="width: 4%;"></th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyItemsCotizacion">
                                    <!-- Renderizado dinámico JS -->
                                </tbody>
                            </table>
                        </div>
                        <div id="emptyStateItems" class="text-center py-5 text-muted">
                            <i class="bi bi-cart-plus fs-1 text-secondary opacity-50"></i>
                            <p class="fw-semibold mb-1 mt-2">Aún no agregaste productos a esta cotización</p>
                            <small>Selecciona productos del catálogo izquierdo o agrega un <strong>Ítem Externo / Libre</strong>.</small>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 4: CONDICIONES, TOTALES Y ACCIONES -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold mb-1">Condiciones Comerciales y Validez</label>
                                <textarea name="condiciones" id="inputCondiciones" rows="3" class="form-control form-control-sm mb-2"
                                          placeholder="Ej. Pago al contado o transferencia. Entrega inmediata según disponibilidad de stock.">{{ old('condiciones', $cotizacion->condiciones ?? 'Precios vigentes hasta la fecha indicada. Sujeto a disponibilidad de stock.') }}</textarea>
                                <label class="form-label small fw-bold mb-1">Observaciones Internas o para el Cliente</label>
                                <textarea name="observaciones" rows="2" class="form-control form-control-sm"
                                          placeholder="Notas adicionales, tiempo de entrega, contacto...">{{ old('observaciones', $cotizacion->observaciones ?? '') }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <div class="total-panel p-3 h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="small fw-semibold text-muted">Subtotal</span>
                                            <span class="fw-bold text-dark fs-6" id="lblSubtotalCot">{{ $monedaInicial === 'PEN' ? 'S/' : 'Bs.' }} 0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="small fw-semibold text-danger mb-0" for="inputDescuentoCot">Descuento (-)</label>
                                            <div class="input-group input-group-sm" style="width: 140px;">
                                                <span class="input-group-text lbl-simbolo-moneda">{{ $monedaInicial === 'PEN' ? 'S/' : 'Bs.' }}</span>
                                                <input type="number" step="0.01" min="0" name="descuento" id="inputDescuentoCot"
                                                       class="form-control text-end fw-bold text-danger" value="{{ $descuentoInicial }}">
                                            </div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-extrabold text-primary fs-6">TOTAL COTIZADO</span>
                                            <span class="fw-extrabold text-primary fs-4" id="lblTotalCot">{{ $monedaInicial === 'PEN' ? 'S/' : 'Bs.' }} 0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-1 small text-secondary" id="boxEquivalenteLive">
                                            <span>Equivalente ref.:</span>
                                            <strong id="lblEquivalenteCot" class="text-dark"></strong>
                                        </div>
                                    </div>

                                    <!-- Botonera de Acciones -->
                                    <div class="d-grid gap-2 mt-3">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <button type="button" onclick="enviarCotizacion('guardar')" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">
                                                    <i class="bi bi-check-circle-fill me-1"></i> Guardar
                                                </button>
                                            </div>
                                            <div class="col-6">
                                                <button type="button" onclick="enviarCotizacion('whatsapp')" class="btn btn-success w-100 fw-bold py-2 shadow-sm">
                                                    <i class="bi bi-whatsapp me-1"></i> Guardar y WhatsApp
                                                </button>
                                            </div>
                                            <div class="col-6">
                                                <button type="button" onclick="enviarCotizacion('pdf')" class="btn btn-outline-danger w-100 fw-bold">
                                                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Guardar y PDF
                                                </button>
                                            </div>
                                            <div class="col-6">
                                                <button type="button" onclick="enviarCotizacion('imprimir')" class="btn btn-outline-dark w-100 fw-bold">
                                                    <i class="bi bi-printer-fill me-1"></i> Guardar e Imprimir
                                                </button>
                                            </div>
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
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-plus-square-dotted me-2"></i>Agregar Producto Externo o Concepto Libre
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="small text-muted mb-3">
                    Útil para cotizar productos de terceros, tiendas externas, fletes o artículos que aún no están en tu inventario.
                </p>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Descripción del Producto o Servicio *</label>
                    <input type="text" id="extNombre" class="form-control" placeholder="Ej. Taladro Percutor 800W / Servicio de envío">
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
                        <label class="form-label small fw-bold">Precio Unitario (<span class="lbl-simbolo-moneda">{{ $monedaInicial === 'PEN' ? 'S/' : 'Bs.' }}</span>) *</label>
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
@endsection

@section('scripts')
<script>
    const PRODUCTOS_CATALOGO = @json($productosJson);
    const CLIENTES_LISTA = @json($clientesJson);
    const MONEDA_SISTEMA_BASE = @json($monedaPrincipal ?? 'BOB');
    let itemsCotizacion = @json($itemsParaJs);
    let monedaAnterior = document.getElementById('selectMonedaCotizacion').value;

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
        return document.getElementById('selectMonedaCotizacion').value === 'PEN' ? 'S/' : 'Bs.';
    }

    function simboloOpuesto() {
        return document.getElementById('selectMonedaCotizacion').value === 'PEN' ? 'Bs.' : 'S/';
    }

    function tasaCambioActual() {
        const val = parseFloat(document.getElementById('inputTasaCambio').value);
        return (!isNaN(val) && val > 0) ? val : 3.60;
    }

    function tarifaCatalogoActiva() {
        return document.getElementById('tarifaCliente').checked ? 'cliente' : 'venta';
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

    function renderizarCatalogo() {
        const q = (document.getElementById('buscarCatalogo').value || '').toLowerCase().trim();
        const catId = document.getElementById('filtroCategoriaCatalogo').value;
        const tarifa = tarifaCatalogoActiva();
        const sim = simboloActual();
        const monCot = document.getElementById('selectMonedaCotizacion').value;

        const aviso = document.getElementById('avisoConversionCatalogo');
        if (MONEDA_SISTEMA_BASE !== monCot) {
            document.getElementById('monedaCotDestinoLbl').textContent = monCot === 'PEN' ? 'Soles (S/)' : 'Bolivianos (Bs.)';
            aviso.classList.remove('d-none');
        } else {
            aviso.classList.add('d-none');
        }

        const filtrados = PRODUCTOS_CATALOGO.filter(p => {
            if (catId && String(p.categoria_id) !== String(catId)) return false;
            if (!q) return true;
            const hay = `${p.codigo} ${p.nombre} ${p.marca} ${p.categoria}`.toLowerCase();
            return hay.includes(q);
        });

        const cont = document.getElementById('contenedorCatalogo');
        if (filtrados.length === 0) {
            cont.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-search fs-3 opacity-50"></i>
                    <p class="small mt-2 mb-0">No se encontraron productos con ese criterio.</p>
                </div>`;
            return;
        }

        const limite = filtrados.slice(0, 80);
        let html = '';
        limite.forEach(p => {
            const baseRaw = tarifa === 'cliente' ? p.precio_cliente : p.precio_venta;
            const precioBase = convertirDesdeSistemaACotizacion(baseRaw);
            const stockBadge = p.stock > 0
                ? `<span class="badge bg-success-subtle text-success border border-success-subtle">Stock: ${p.stock}</span>`
                : `<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Sin stock</span>`;

            let btnSec = '';
            if (p.tiene_secundaria && p.unidad_secundaria) {
                const secRaw = tarifa === 'cliente' ? p.precio_cliente_secundario : p.precio_venta_secundario;
                const precioSec = convertirDesdeSistemaACotizacion(secRaw);
                btnSec = `
                    <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold" style="font-size:11px;"
                            onclick="agregarProductoCatalogo(${p.id}, true)">
                        + ${escHtml(p.unidad_secundaria)} (${sim} ${precioSec.toFixed(2)})
                    </button>`;
            }

            html += `
                <div class="prod-catalog-card p-2 mb-2">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div style="min-width:0; flex:1;">
                            <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                                ${p.codigo ? `<span class="badge bg-light text-dark border" style="font-size:10px;">${escHtml(p.codigo)}</span>` : ''}
                                ${stockBadge}
                                ${p.marca ? `<span class="small text-muted" style="font-size:11px;">• ${escHtml(p.marca)}</span>` : ''}
                            </div>
                            <div class="fw-bold text-dark small text-truncate" title="${escHtml(p.nombre)}">${escHtml(p.nombre)}</div>
                            <div class="text-muted" style="font-size:11px;">
                                ${escHtml(p.unidad)} • P. Venta: ${sim} ${convertirDesdeSistemaACotizacion(p.precio_venta).toFixed(2)} | Cliente: ${sim} ${convertirDesdeSistemaACotizacion(p.precio_cliente).toFixed(2)}
                            </div>
                        </div>
                        <div class="text-end flex-shrink-0 d-flex flex-column gap-1 align-items-end">
                            <button type="button" class="btn btn-primary btn-sm fw-bold px-2 py-1" onclick="agregarProductoCatalogo(${p.id}, false)">
                                <i class="bi bi-plus-lg"></i> ${sim} ${precioBase.toFixed(2)}
                            </button>
                            ${btnSec}
                        </div>
                    </div>
                </div>`;
        });

        if (filtrados.length > 80) {
            html += `<div class="text-center text-muted small py-2">Mostrando los primeros 80 de ${filtrados.length} resultados. Usa el buscador para filtrar mejor.</div>`;
        }
        cont.innerHTML = html;
    }

    function agregarProductoCatalogo(prodId, usarSecundaria) {
        const prod = PRODUCTOS_CATALOGO.find(p => p.id === prodId);
        if (!prod) return;

        const tarifa = tarifaCatalogoActiva();
        const unidad = usarSecundaria ? (prod.unidad_secundaria || prod.unidad) : prod.unidad;
        const precioSistema = usarSecundaria
            ? (tarifa === 'cliente' ? prod.precio_cliente_secundario : prod.precio_venta_secundario)
            : (tarifa === 'cliente' ? prod.precio_cliente : prod.precio_venta);
        const precioCot = convertirDesdeSistemaACotizacion(precioSistema);

        const existente = itemsCotizacion.find(it =>
            it.id_producto === prod.id && !it.es_externo && it.unidad === unidad
        );

        if (existente) {
            existente.cantidad = Math.round((parseFloat(existente.cantidad) + 1) * 100) / 100;
        } else {
            itemsCotizacion.push({
                key: 'prod_' + prod.id + '_' + Date.now() + '_' + Math.floor(Math.random() * 1000),
                id_producto: prod.id,
                codigo: prod.codigo || '',
                nombre: prod.nombre,
                unidad: unidad || 'Pieza',
                es_externo: false,
                cantidad: 1,
                precio_unitario: precioCot,
            });
        }
        renderizarItems();
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
            const sub = (parseFloat(it.cantidad) || 0) * (parseFloat(it.precio_unitario) || 0);
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
                        <input type="number" step="0.01" min="0.01" name="items[${idx}][cantidad]"
                               class="form-control form-control-sm text-center fw-bold"
                               value="${it.cantidad}" oninput="actualizarCampoItem(${idx}, 'cantidad', this.value)" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0" name="items[${idx}][precio_unitario]"
                               class="form-control form-control-sm text-end fw-bold"
                               value="${Number(it.precio_unitario).toFixed(2)}" oninput="actualizarCampoItem(${idx}, 'precio_unitario', this.value)" required>
                    </td>
                    <td class="text-end fw-bold text-dark" id="sub_item_${idx}">${sim} ${sub.toFixed(2)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="eliminarItem(${idx})" title="Quitar ítem">
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

    function convertirTodosLosPrecios(monOrigenForzada) {
        if (itemsCotizacion.length === 0) {
            renderizarCatalogo();
            recalcularTotales();
            return;
        }
        const monNueva = document.getElementById('selectMonedaCotizacion').value;
        const tasa = tasaCambioActual();
        const factor = (monOrigenForzada === 'PEN' && monNueva === 'BOB')
            ? tasa
            : ((monOrigenForzada === 'BOB' && monNueva === 'PEN') ? (1 / tasa) : null);

        if (factor === null) {
            // Botón manual de conversión usando tasa
            Swal.fire({
                title: 'Recalcular precios del detalle',
                text: `¿Deseas convertir los precios actuales según la tasa (1 S/ = Bs. ${tasa.toFixed(2)})?`,
                icon: 'question',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: 'De S/ a Bs. (× ' + tasa.toFixed(2) + ')',
                denyButtonText: 'De Bs. a S/ (÷ ' + tasa.toFixed(2) + ')',
                cancelButtonText: 'Cancelar'
            }).then(res => {
                if (res.isConfirmed) {
                    aplicarFactorPrecios(tasa);
                } else if (res.isDenied) {
                    aplicarFactorPrecios(1 / tasa);
                }
            });
            return;
        }

        aplicarFactorPrecios(factor);
    }

    function aplicarFactorPrecios(factor) {
        itemsCotizacion = itemsCotizacion.map(it => ({
            ...it,
            precio_unitario: Math.round((parseFloat(it.precio_unitario) || 0) * factor * 100) / 100
        }));
        const descInput = document.getElementById('inputDescuentoCot');
        const desc = parseFloat(descInput.value) || 0;
        if (desc > 0) {
            descInput.value = (Math.round(desc * factor * 100) / 100).toFixed(2);
        }
        renderizarItems();
    }

    function enviarCotizacion(accion) {
        const nombre = document.getElementById('destinatarioNombre').value.trim();
        if (!nombre) {
            Swal.fire({ icon: 'warning', title: 'Falta destinatario', text: 'Ingresa el nombre del cliente o contacto de la tienda.' });
            document.getElementById('destinatarioNombre').focus();
            return;
        }
        if (itemsCotizacion.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Cotización vacía', text: 'Agrega al menos un producto o ítem antes de guardar.' });
            return;
        }
        if (accion === 'whatsapp' && !document.getElementById('destinatarioTelefono').value.trim()) {
            Swal.fire({
                icon: 'info',
                title: 'Sin número de WhatsApp',
                text: 'Se guardará la cotización y podrás ingresar el número de WhatsApp en la ventana de envío.',
                confirmButtonText: 'Continuar'
            }).then(() => {
                document.getElementById('accionPostInput').value = accion;
                document.getElementById('formCotizacion').submit();
            });
            return;
        }

        document.getElementById('accionPostInput').value = accion;
        document.getElementById('formCotizacion').submit();
    }

    // Listeners iniciales
    document.getElementById('buscarCatalogo').addEventListener('input', renderizarCatalogo);
    document.getElementById('filtroCategoriaCatalogo').addEventListener('change', renderizarCatalogo);
    document.getElementById('tarifaVenta').addEventListener('change', renderizarCatalogo);
    document.getElementById('tarifaCliente').addEventListener('change', renderizarCatalogo);
    document.getElementById('inputDescuentoCot').addEventListener('input', recalcularTotales);
    document.getElementById('inputTasaCambio').addEventListener('input', function () {
        renderizarCatalogo();
        recalcularTotales();
    });
    document.getElementById('chkMostrarEquivalente').addEventListener('change', recalcularTotales);

    document.getElementById('selectorClienteRegistrado').addEventListener('change', function () {
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

    document.getElementById('selectMonedaCotizacion').addEventListener('change', function () {
        const monNueva = this.value;
        const monOrig = monedaAnterior;
        monedaAnterior = monNueva;

        renderizarCatalogo();

        if (itemsCotizacion.length > 0 && monOrig !== monNueva) {
            const tasa = tasaCambioActual();
            Swal.fire({
                title: `Moneda cambiada a ${monNueva === 'PEN' ? 'Soles (S/)' : 'Bolivianos (Bs.)'}`,
                text: `¿Deseas convertir automáticamente los precios de los ${itemsCotizacion.length} ítem(s) agregados usando la tasa 1 S/ = Bs. ${tasa.toFixed(2)}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, convertir precios',
                cancelButtonText: 'No, mantener montos'
            }).then(res => {
                if (res.isConfirmed) {
                    convertirTodosLosPrecios(monOrig);
                } else {
                    renderizarItems();
                }
            });
        } else {
            renderizarItems();
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        renderizarCatalogo();
        renderizarItems();
    });
</script>
@endsection
