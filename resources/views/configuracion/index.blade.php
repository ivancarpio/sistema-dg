@extends('layouts.app')

@section('content')
<style>
    .nav-tabs-custom {
        border-bottom: 2px solid #e2e8f0;
    }
    .nav-tabs-custom .nav-link {
        border: none;
        color: #64748b;
        font-weight: 700;
        padding: 12px 20px;
        border-bottom: 3px solid transparent;
        transition: all 0.2s ease;
    }
    .nav-tabs-custom .nav-link:hover {
        color: #0284c7;
        border-color: #cbd5e1;
    }
    .nav-tabs-custom .nav-link.active {
        color: #0284c7;
        border-bottom: 3px solid #0284c7;
        background-color: transparent;
    }
    .table-config th {
        background-color: #f8fafc !important;
        font-size: 0.8rem;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 12px;
        border-bottom: 1px solid #e2e8f0 !important;
    }
    .table-config td {
        vertical-align: middle;
        padding: 12px;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-config tr:hover {
        background-color: #f8fafc;
    }
    .badge-count {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 5px 10px;
        border-radius: 20px;
    }
    .card-panel-form {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
    }
    /* Switch visual estilizado */
    .form-switch-lg .form-check-input {
        width: 3.2rem;
        height: 1.7rem;
        cursor: pointer;
    }
    .form-switch-lg .form-check-input:checked {
        background-color: #10b981;
        border-color: #10b981;
    }
    /* Estilos limpios y sólidos para modales (evita transparencia/glitch) */
    .modal-content-clean {
        background-color: #ffffff !important;
        border: none !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1) !important;
        border-radius: 16px !important;
        overflow: hidden;
    }
    .fila-prod-masiva {
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    .fila-prod-masiva:hover {
        background-color: #f0f9ff !important;
    }
    /* Scrollbar visible, profesional y fluido para no cortar texto */
    .tabla-scroll-masivo {
        height: 380px !important;
        max-height: 380px !important;
        overflow-y: scroll !important;
        border: 1px solid #cbd5e1;
        border-radius: 8px 8px 0 0;
        background-color: #ffffff;
    }
    .tabla-scroll-masivo::-webkit-scrollbar {
        width: 12px;
    }
    .tabla-scroll-masivo::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .tabla-scroll-masivo::-webkit-scrollbar-thumb {
        background-color: #0284c7;
        border-radius: 6px;
        border: 2px solid #f1f5f9;
    }
    .tabla-scroll-masivo::-webkit-scrollbar-thumb:hover {
        background-color: #0369a1;
    }
    .table-masiva td {
        padding: 10px 12px !important;
        vertical-align: middle !important;
        white-space: normal !important;
    }
    .table-masiva th {
        padding: 10px 12px !important;
        background-color: #f8fafc !important;
        z-index: 5;
    }
</style>

<!-- ENCABEZADO -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3 text-primary">
            <i class="bi bi-gear-fill fs-3"></i>
        </div>
        <div>
            <h3 class="fw-bold m-0 text-dark">Configuración del Sistema</h3>
            <small class="text-muted">Administra catálogos de productos, métodos de pago y datos comerciales</small>
        </div>
    </div>
</div>

<!-- ALERTAS -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-success border-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-4 text-success me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-danger border-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-4 text-danger me-2"></i>
            <div>{{ $errors->first() }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- CONTENEDOR PRINCIPAL CON PESTAÑAS -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    
    <!-- PESTAÑAS DE NAVEGACIÓN -->
    <div class="card-header bg-white pt-3 pb-0 px-4 border-0">
        <ul class="nav nav-tabs nav-tabs-custom" id="configTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="categorias-tab" data-bs-toggle="tab" data-bs-target="#tab-categorias" type="button" role="tab">
                    <i class="bi bi-folder-fill me-2 text-primary"></i> Categorías ({{ count($categorias) }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="marcas-tab" data-bs-toggle="tab" data-bs-target="#tab-marcas" type="button" role="tab">
                    <i class="bi bi-tags-fill me-2 text-info"></i> Marcas ({{ count($marcas) }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="unidades-tab" data-bs-toggle="tab" data-bs-target="#tab-unidades" type="button" role="tab">
                    <i class="bi bi-rulers me-2 text-warning"></i> Unidades ({{ count($unidades) }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pagos-tab" data-bs-toggle="tab" data-bs-target="#tab-pagos" type="button" role="tab">
                    <i class="bi bi-qr-code-scan me-2 text-success"></i> Pagos y QR
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="empresa-tab" data-bs-toggle="tab" data-bs-target="#tab-empresa" type="button" role="tab">
                    <i class="bi bi-building-gear me-2 text-secondary"></i> Datos de Empresa
                </button>
            </li>
        </ul>
    </div>

    <!-- CONTENIDO DE LAS PESTAÑAS -->
    <div class="card-body bg-light p-4">
        <div class="tab-content" id="configTabsContent">
            
            <!-- ======================================================== -->
            <!-- PESTAÑA 1: CATEGORÍAS (CREAR, EDITAR, ELIMINAR, MASIVA) -->
            <!-- ======================================================== -->
            <div class="tab-pane fade show active" id="tab-categorias" role="tabpanel">
                
                <!-- Barra superior de Categorías con botón Masivo -->
                <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm border">
                    <div>
                        <h6 class="fw-bold m-0 text-dark"><i class="bi bi-diagram-3-fill text-primary me-2"></i> Organización por Categorías</h6>
                        <small class="text-muted">Crea grupos y asigna productos de forma individual o masiva</small>
                    </div>
                    <button type="button" class="btn btn-primary fw-bold shadow-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalAsignacionMasiva">
                        <i class="bi bi-grid-3x3-gap-fill me-1"></i> Administración Masiva de Categorías
                    </button>
                </div>

                <div class="row g-4">
                    <!-- Formulario Nueva Categoría -->
                    <div class="col-lg-4">
                        <div class="card card-panel-form shadow-sm">
                            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                                <h6 class="fw-bold m-0 text-dark"><i class="bi bi-plus-circle-fill text-primary me-2"></i> Nueva Categoría</h6>
                                <small class="text-muted">Crea grupos para organizar los productos</small>
                            </div>
                            <div class="card-body p-4">
                                <form action="/configuracion/categorias" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label text-muted small fw-bold">Nombre de la Categoría *</label>
                                        <input type="text" name="nombre" class="form-control text-uppercase fw-bold" placeholder="Ej. PLOMERÍA, ELECTRICIDAD" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small fw-bold">Descripción (Opcional)</label>
                                        <textarea name="descripcion" class="form-control" rows="2" placeholder="Detalle o alcance de la categoría..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm py-2">
                                        <i class="bi bi-save me-1"></i> Guardar Categoría
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Categorías -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-config m-0">
                                        <thead>
                                            <tr>
                                                <th class="ps-4">ID</th>
                                                <th>Categoría</th>
                                                <th>Descripción</th>
                                                <th class="text-center">Productos</th>
                                                <th class="text-end pe-4">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($categorias as $cat)
                                            <tr>
                                                <td class="ps-4 text-muted small fw-bold">#{{ $cat->id }}</td>
                                                <td>
                                                    <span class="fw-bold text-dark fs-6">{{ $cat->nombre }}</span>
                                                </td>
                                                <td class="text-muted small">
                                                    {{ $cat->descripcion ?: '—' }}
                                                </td>
                                                <td class="text-center">
                                                    @if($cat->productos_count > 0)
                                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 badge-count">
                                                            <i class="bi bi-box-seam me-1"></i> {{ $cat->productos_count }} prod.
                                                        </span>
                                                    @else
                                                        <span class="badge bg-light text-muted border badge-count">
                                                            0 prod.
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-end pe-4">
                                                    <!-- Botón Asignar rápido a esta categoría -->
                                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 me-1" 
                                                            onclick="abrirAsignacionParaCategoria({{ $cat->id }})" title="Asignar productos a esta categoría">
                                                        <i class="bi bi-plus-lg"></i> Asignar
                                                    </button>

                                                    <!-- Editar -->
                                                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2 me-1" 
                                                            data-bs-toggle="modal" data-bs-target="#modalEditarCat{{ $cat->id }}" title="Editar nombre">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </button>
                                                    
                                                    <!-- Eliminar -->
                                                    @if($cat->productos_count == 0)
                                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2" 
                                                                data-bs-toggle="modal" data-bs-target="#modalEliminarCat{{ $cat->id }}" title="Eliminar categoría">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2 disabled" 
                                                                title="No se puede eliminar: tiene {{ $cat->productos_count }} producto(s) asignado(s)">
                                                            <i class="bi bi-lock-fill"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">No hay categorías registradas.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- PESTAÑA 2: MARCAS (CREAR, EDITAR Y ELIMINAR)             -->
            <!-- ======================================================== -->
            <div class="tab-pane fade" id="tab-marcas" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card card-panel-form shadow-sm">
                            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                                <h6 class="fw-bold m-0 text-dark"><i class="bi bi-tag-fill text-info me-2"></i> Agregar Nueva Marca</h6>
                                <small class="text-muted">Marcas de fabricantes y proveedores</small>
                            </div>
                            <div class="card-body p-4">
                                <form action="/configuracion/marcas" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label text-muted small fw-bold">Nombre de la Marca *</label>
                                        <input type="text" name="nombre" class="form-control text-uppercase fw-bold" required placeholder="Ej. TRAMONTINA, CONDUBOL">
                                    </div>
                                    <button type="submit" class="btn btn-info text-white w-100 fw-bold shadow-sm py-2">
                                        <i class="bi bi-save me-1"></i> Guardar Marca
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-config m-0">
                                        <thead>
                                            <tr>
                                                <th class="ps-4">ID</th>
                                                <th>Marca</th>
                                                <th class="text-center">Productos Asociados</th>
                                                <th class="text-end pe-4">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($marcas as $marca)
                                            <tr>
                                                <td class="ps-4 text-muted small fw-bold">#{{ $marca->id }}</td>
                                                <td><span class="fw-bold text-dark fs-6">{{ $marca->nombre }}</span></td>
                                                <td class="text-center">
                                                    @if($marca->productos_count > 0)
                                                        <span class="badge bg-info bg-opacity-10 text-dark border border-info border-opacity-25 badge-count">
                                                            <i class="bi bi-box-seam me-1"></i> {{ $marca->productos_count }} prod.
                                                        </span>
                                                    @else
                                                        <span class="badge bg-light text-muted border badge-count">0 prod.</span>
                                                    @endif
                                                </td>
                                                <td class="text-end pe-4">
                                                    <button class="btn btn-sm btn-outline-warning rounded-pill px-2 me-1" data-bs-toggle="modal" data-bs-target="#editarMarca{{ $marca->id }}" title="Editar Marca">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </button>

                                                    @if($marca->productos_count == 0)
                                                        <button class="btn btn-sm btn-outline-danger rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#eliminarMarca{{ $marca->id }}" title="Eliminar Marca">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-2 disabled" title="No se puede eliminar: tiene {{ $marca->productos_count }} producto(s) asignado(s)">
                                                            <i class="bi bi-lock-fill"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- PESTAÑA 3: UNIDADES (CREAR, EDITAR Y ELIMINAR)           -->
            <!-- ======================================================== -->
            <div class="tab-pane fade" id="tab-unidades" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card card-panel-form shadow-sm">
                            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                                <h6 class="fw-bold m-0 text-dark"><i class="bi bi-rulers text-warning me-2"></i> Agregar Unidad de Medida</h6>
                                <small class="text-muted">Unidades para control de stock</small>
                            </div>
                            <div class="card-body p-4">
                                <form action="/configuracion/unidades" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label text-muted small fw-bold">Nombre de la Unidad *</label>
                                        <input type="text" name="nombre" class="form-control fw-bold" required placeholder="Ej. Metro, Pieza, Paquete, Kilo">
                                    </div>
                                    <button type="submit" class="btn btn-warning text-dark w-100 fw-bold shadow-sm py-2">
                                        <i class="bi bi-save me-1"></i> Guardar Unidad
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-config m-0">
                                        <thead>
                                            <tr>
                                                <th class="ps-4">ID</th>
                                                <th>Unidad</th>
                                                <th class="text-center">Productos Asociados</th>
                                                <th class="text-end pe-4">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($unidades as $unidad)
                                            <tr>
                                                <td class="ps-4 text-muted small fw-bold">#{{ $unidad->id }}</td>
                                                <td><span class="fw-bold text-dark fs-6">{{ $unidad->nombre }}</span></td>
                                                <td class="text-center">
                                                    @if($unidad->productos_count > 0)
                                                        <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 badge-count">
                                                            <i class="bi bi-box-seam me-1"></i> {{ $unidad->productos_count }} prod.
                                                        </span>
                                                    @else
                                                        <span class="badge bg-light text-muted border badge-count">0 prod.</span>
                                                    @endif
                                                </td>
                                                <td class="text-end pe-4">
                                                    <button class="btn btn-sm btn-outline-warning rounded-pill px-2 me-1" data-bs-toggle="modal" data-bs-target="#editarUnidad{{ $unidad->id }}" title="Editar Unidad">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </button>

                                                    @if($unidad->productos_count == 0)
                                                        <button class="btn btn-sm btn-outline-danger rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#eliminarUnidad{{ $unidad->id }}" title="Eliminar Unidad">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-2 disabled" title="No se puede eliminar: tiene {{ $unidad->productos_count }} producto(s) asignado(s)">
                                                            <i class="bi bi-lock-fill"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- PESTAÑA 4: PAGOS / QR (SWITCH ACTIVAR/DESACTIVAR + IMAGEN) -->
            <!-- ======================================================== -->
            <div class="tab-pane fade" id="tab-pagos" role="tabpanel">
                <div class="row g-4 justify-content-center">
                    
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4 bg-white">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3 text-success">
                                        <i class="bi bi-toggle2-on fs-3"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold m-0 text-dark">Modal de Cobro QR en Punto de Venta</h5>
                                        <small class="text-muted">Controla si debe desplegarse la ventana flotante con el QR</small>
                                    </div>
                                </div>

                                <div class="p-3 bg-light rounded-3 mb-4 border">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="me-3">
                                            <div class="fw-bold text-dark fs-6" id="labelEstadoQr">
                                                @if(($configuraciones['mostrar_modal_qr'] ?? '1') == '1')
                                                    <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> Modal QR Activado</span>
                                                @else
                                                    <span class="text-secondary"><i class="bi bi-slash-circle me-1"></i> Modal QR Desactivado</span>
                                                @endif
                                            </div>
                                            <small class="text-muted d-block mt-1">
                                                @if(($configuraciones['mostrar_modal_qr'] ?? '1') == '1')
                                                    Al elegir <strong>QR</strong> en el POS, se abrirá la ventana emergente con el código y el total para que el cliente lo escanee en pantalla.
                                                @else
                                                    Al elegir <strong>QR</strong> en el POS, se seleccionará de inmediato sin abrir la ventana emergente (ideal si ya cuentas con el QR impreso en el mostrador).
                                                @endif
                                            </small>
                                        </div>
                                        
                                        <form action="/configuracion/qr-toggle" method="POST" id="formToggleQr" class="m-0">
                                            @csrf
                                            <div class="form-check form-switch form-switch-lg m-0">
                                                <input class="form-check-input" type="checkbox" role="switch" id="switchModalQr" 
                                                       {{ ($configuraciones['mostrar_modal_qr'] ?? '1') == '1' ? 'checked' : '' }}
                                                       onchange="document.getElementById('formToggleQr').submit();">
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="alert alert-info border-0 shadow-sm d-flex align-items-start m-0">
                                    <i class="bi bi-lightbulb-fill text-info fs-5 me-2 mt-1"></i>
                                    <div class="small">
                                        <strong>Consejo para el cajero:</strong> Puedes desactivar esta ventana si tus clientes escanean un QR físico ubicado en el mostrador de la ferretería, acelerando la atención en horas pico.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 text-center">
                            <div class="card-body p-4 bg-white">
                                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-qr-code text-primary me-2"></i> Código QR Actual para Transferencias</h6>
                                
                                <div class="d-inline-block p-2 bg-white rounded-4 shadow-sm border mb-3">
                                    <img src="{{ asset('img/qr_banco.jpeg') }}?v={{ time() }}" 
                                         alt="QR de Pago" 
                                         class="img-fluid rounded-3" 
                                         style="max-height: 200px; max-width: 200px; object-fit: contain;">
                                </div>
                                
                                <form action="/configuracion/qr" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3 text-start">
                                        <label class="form-label text-muted small fw-bold">Actualizar Imagen del QR</label>
                                        <input class="form-control" type="file" name="qr_image" accept=".jpg,.jpeg,.png" required>
                                        <small class="text-muted">Archivos permitidos: JPG, PNG (máx. 4MB).</small>
                                    </div>
                                    <button class="btn btn-success fw-bold w-100 shadow-sm">
                                        <i class="bi bi-cloud-arrow-up-fill me-2"></i> Subir Nuevo QR
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ======================================================== -->
            <!-- PESTAÑA 5: DATOS DE EMPRESA Y AJUSTES GENERALES         -->
            <!-- ======================================================== -->
            <div class="tab-pane fade" id="tab-empresa" role="tabpanel">
                <div class="row g-4 justify-content-center">
                    <!-- Vista previa del Logo Corporativo y Carga de Archivo -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                            <div class="card-body p-4 text-center d-flex flex-column justify-content-between">
                                <div>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-1 fw-bold mb-3">
                                        <i class="bi bi-patch-check-fill me-1"></i> Logo Oficial Activo
                                    </span>
                                    <div class="p-3 bg-light rounded-3 mb-3 d-flex align-items-center justify-content-center border" style="min-height: 150px; background-color: #f8fafc !important;">
                                        <img src="{{ asset('img/logo.png') }}?v={{ \App\Models\Configuracion::get('logo_version', 1) }}" 
                                             id="previewLogoImg"
                                             alt="Distribuidora Guadalupe" 
                                             class="img-fluid" 
                                             style="max-height: 125px; object-fit: contain;">
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">{{ $configuraciones['nombre_empresa'] ?? 'Distribuidora Guadalupe' }}</h6>
                                    <p class="text-muted small m-0 mb-3" style="font-size: 0.8rem;">
                                        Este logo se aplica automáticamente en todas las <strong>Notas de Venta en PDF</strong>, pantalla de <strong>Login</strong>, <strong>Barra Lateral</strong> y en todo el sistema.
                                    </p>

                                    <!-- Formulario para subir nuevo logo -->
                                    <form action="/configuracion/logo" method="POST" enctype="multipart/form-data" class="text-start p-3 rounded-3 border bg-light-subtle">
                                        @csrf
                                        <label class="form-label text-dark small fw-bold mb-1 d-flex align-items-center justify-content-between">
                                            <span><i class="bi bi-image text-primary me-1"></i> Subir Nuevo Logo</span>
                                            <span class="text-muted fw-normal" style="font-size: 0.7rem;">PNG, JPG, WEBP</span>
                                        </label>
                                        <input type="file" name="logo" class="form-control form-control-sm mb-2" accept="image/png,image/jpeg,image/jpg,image/webp" required onchange="previewSelectedLogo(this)">
                                        <div id="logoFileInfo" class="small text-muted mb-2 d-none" style="font-size: 0.72rem;">
                                            <i class="bi bi-check-circle-fill text-success me-1"></i> Archivo seleccionado listo para guardar
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold shadow-sm py-2">
                                            <i class="bi bi-cloud-arrow-up-fill me-1"></i> Actualizar Logo Oficial
                                        </button>
                                    </form>
                                </div>
                                <div class="mt-3 pt-3 border-top text-start small text-muted">
                                    <i class="bi bi-info-circle text-primary me-1"></i>
                                    Al subir una nueva imagen, el sistema genera automáticamente las versiones optimizadas para pantalla e impresión.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de Datos Comerciales -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                                <h5 class="fw-bold text-dark m-0"><i class="bi bi-building-check text-primary me-2"></i> Información Comercial y Notas de Venta</h5>
                                <small class="text-muted">Datos que se muestran en los comprobantes PDF y reportes del sistema</small>
                            </div>
                            <div class="card-body p-4 bg-white">
                                <form action="/configuracion/ajustes" method="POST">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label class="form-label text-muted small fw-bold">Nombre Comercial de la Empresa / Ferretería *</label>
                                            <input type="text" name="nombre_empresa" class="form-control fw-bold" 
                                                   value="{{ $configuraciones['nombre_empresa'] ?? 'Distribuidora Guadalupe' }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label text-muted small fw-bold">NIT / C.I.</label>
                                            <input type="text" name="nit_empresa" class="form-control" 
                                                   value="{{ $configuraciones['nit_empresa'] ?? '' }}" placeholder="Ej. 123456789">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label text-muted small fw-bold">Teléfono o Celular de Contacto</label>
                                            <input type="text" name="telefono_empresa" class="form-control" 
                                                   value="{{ $configuraciones['telefono_empresa'] ?? '' }}" placeholder="Ej. 70000000 / 4-4444444">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted small fw-bold">Alerta Stock Mínimo por Defecto</label>
                                            <div class="input-group">
                                                <input type="number" name="stock_minimo_defecto" class="form-control" 
                                                       value="{{ $configuraciones['stock_minimo_defecto'] ?? '5' }}" min="1">
                                                <span class="input-group-text bg-light text-muted">unidades</span>
                                            </div>
                                            <small class="text-muted">Valor sugerido al crear nuevos productos</small>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label text-muted small fw-bold">Dirección Comercial</label>
                                            <input type="text" name="direccion_empresa" class="form-control" 
                                                   value="{{ $configuraciones['direccion_empresa'] ?? '' }}" placeholder="Ej. Av. Principal #123, Zona Central">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label text-muted small fw-bold">Pie de Ticket / Mensaje de Agradecimiento</label>
                                            <textarea name="pie_ticket" class="form-control" rows="2" 
                                                      placeholder="Ej. ¡Gracias por su compra! Revise su mercadería antes de retirarse.">{{ $configuraciones['pie_ticket'] ?? '¡Gracias por su preferencia!' }}</textarea>
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-3 border-top text-end">
                                        <button type="submit" class="btn btn-primary fw-bold px-4 py-2 shadow-sm">
                                            <i class="bi bi-save me-1"></i> Guardar Ajustes del Negocio
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODALES: FUERA DE LAS TABLAS PARA EVITAR CONFLICTOS HTML -->
<!-- ======================================================== -->

<!-- 1. MODAL: ADMINISTRACIÓN MASIVA DE CATEGORÍAS -->
<div class="modal fade" id="modalAsignacionMasiva" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="/configuracion/categorias/asignar-masivo" method="POST" class="modal-content modal-content-clean">
            @csrf
            <div class="modal-header bg-primary text-white border-0 py-3 px-4">
                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255, 255, 255, 0.2);">
                        <i class="bi bi-grid-3x3-gap-fill fs-4 text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold m-0 text-white">Administración Masiva de Categorías</h5>
                        <small class="text-white-50">Asigna productos a una categoría o desvincúlalos ("Sin Categoría") en un solo paso</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4 bg-white">
                
                <!-- PASO 1: CATEGORÍA DESTINO O ACCIÓN -->
                <div class="mb-4 p-3 bg-light rounded-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-bold text-dark m-0">
                            <i class="bi bi-folder-fill text-primary me-1"></i> Paso 1: Selecciona la Categoría de Destino
                        </label>
                        <span class="badge bg-white text-secondary border px-2 py-1">Opcional si vas a Quitar</span>
                    </div>
                    <select name="id_categoria" id="selectCategoriaDestino" class="form-select form-select-lg fw-bold">
                        <option value="">-- Elige una categoría de la lista --</option>
                        <option value="sin_categoria" class="text-danger fw-bold">❌ Quitar categoría (Dejar como "Sin Categoría")</option>
                        <optgroup label="── Categorías Disponibles ──">
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nombre }} (Actualmente {{ $cat->productos_count }} prod.)</option>
                            @endforeach
                        </optgroup>
                    </select>
                    <small class="text-muted d-block mt-2">
                        <i class="bi bi-info-circle me-1"></i> Para asignar, selecciona la categoría arriba. Si deseas quitar la categoría a los productos, puedes usar directamente el botón rojo inferior.
                    </small>
                </div>

                <!-- PASO 2: FILTROS Y SELECCIÓN DE PRODUCTOS -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label fw-bold text-dark m-0">
                        <i class="bi bi-check2-square text-primary me-1"></i> Paso 2: Marca los Productos
                    </label>
                    <span id="badgeConteoSeleccionados" class="badge bg-primary px-3 py-2 rounded-pill fs-7 fw-bold">
                        0 seleccionados
                    </span>
                </div>

                <!-- Buscador y filtro en vivo dentro del modal -->
                <div class="row g-2 mb-3">
                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="buscadorMasivoProd" class="form-control" placeholder="Filtrar por nombre o código...">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <select id="filtroCategoriaActualMasivo" class="form-select">
                            <option value="todos">Todos los productos ({{ count($productos) }})</option>
                            <option value="sin_cat">Solo sin categoría ({{ $productos->whereNull('id_categoria')->count() }})</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}">De: {{ $cat->nombre }} ({{ $cat->productos_count }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Botones rápidos de selección -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary" id="btnMarcarVisibles">
                            <i class="bi bi-check-all"></i> Marcar visibles
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnDesmarcarTodosMasivo">
                            <i class="bi bi-x"></i> Desmarcar todos
                        </button>
                    </div>
                    <small class="text-muted fw-bold" id="txtTotalVisiblesMasivo">Mostrando {{ count($productos) }} producto(s)</small>
                </div>

                <!-- Tabla de productos con scroll garantizado, visible y sin cortes -->
                <div class="tabla-scroll-masivo">
                    <table class="table table-hover table-masiva m-0">
                        <thead class="sticky-top">
                            <tr>
                                <th style="width: 45px;" class="text-center">
                                    <i class="bi bi-check2"></i>
                                </th>
                                <th>CÓDIGO</th>
                                <th>PRODUCTO</th>
                                <th>CATEGORÍA ACTUAL</th>
                                <th class="text-center">STOCK</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyProductosMasivo">
                            @forelse($productos as $prod)
                            <tr class="fila-prod-masiva" 
                                data-id="{{ $prod->id }}" 
                                data-cat="{{ $prod->id_categoria ?? '0' }}" 
                                data-texto="{{ strtolower($prod->codigo . ' ' . $prod->nombre) }}"
                                onclick="toggleCheckFila(event, 'checkProd{{ $prod->id }}')">
                                <td class="text-center">
                                    <input type="checkbox" name="productos_ids[]" value="{{ $prod->id }}" id="checkProd{{ $prod->id }}" class="form-check-input check-prod-item" style="cursor: pointer; width: 1.15rem; height: 1.15rem;">
                                </td>
                                <td class="small fw-bold text-secondary">{{ $prod->codigo }}</td>
                                <td class="fw-bold text-dark">{{ $prod->nombre }}</td>
                                <td>
                                    @if($prod->categoria)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">{{ $prod->categoria->nombre }}</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-25 text-dark"><i class="bi bi-question-circle"></i> Sin Categoría</span>
                                    @endif
                                </td>
                                <td class="text-center small fw-bold">{{ $prod->stock }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No hay productos registrados en el inventario.</td>
                            </tr>
                            @endforelse
                            <!-- Fila de espacio para asegurar que el último producto nunca se corte -->
                            <tr style="height: 25px; border: none; background: transparent;">
                                <td colspan="5" style="border: none; padding: 10px;" class="text-center text-muted small fst-italic">
                                    Fin de la lista de productos
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Barra informativa inferior con guía de scroll -->
                <div class="bg-light p-2 px-3 border border-top-0 rounded-bottom d-flex justify-content-between align-items-center small text-muted">
                    <div>
                        <i class="bi bi-mouse-fill text-primary me-1"></i> 
                        <strong>Desplaza la barra lateral azul</strong> hacia abajo para ver y marcar todos los productos.
                    </div>
                    <div class="badge bg-secondary text-white px-2 py-1">
                        <i class="bi bi-arrows-vertical me-1"></i> Desplazamiento activo
                    </div>
                </div>

            </div>

            <div class="modal-footer border-0 bg-light p-3 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal">Cancelar</button>
                </div>
                <div class="d-flex gap-2">
                    <!-- Botón Quitar Categoría -->
                    <button type="submit" name="accion" value="quitar" class="btn btn-outline-danger fw-bold px-3 py-2" id="btnQuitarMasivo" onclick="return validarAccionMasiva('quitar');">
                        <i class="bi bi-folder-x me-1"></i> Quitar de Categoría ("Sin Categoría")
                    </button>

                    <!-- Botón Asignar Categoría -->
                    <button type="submit" name="accion" value="asignar" class="btn btn-primary fw-bold px-4 py-2 shadow-sm" id="btnGuardarMasivo" onclick="return validarAccionMasiva('asignar');">
                        <i class="bi bi-folder-check me-1"></i> Asignar a Categoría
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 2. MODALES DE CATEGORÍAS (EDITAR Y ELIMINAR) -->
@foreach($categorias as $cat)
    <!-- Modal Editar Categoría -->
    <div class="modal fade" id="modalEditarCat{{ $cat->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="/configuracion/categorias/{{ $cat->id }}" method="POST" class="modal-content modal-content-clean">
                @csrf @method('PUT')
                <div class="modal-header bg-warning bg-opacity-25 border-0 py-3 px-4">
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 36px; height: 36px; border-radius: 8px; background: rgba(245, 158, 11, 0.2); color: #b45309;">
                            <i class="bi bi-pencil-square fs-5"></i>
                        </div>
                        <h6 class="modal-title fw-bold text-dark m-0">Editar Categoría</h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nombre de la Categoría *</label>
                        <input type="text" name="nombre" class="form-control text-uppercase fw-bold" value="{{ $cat->nombre }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2">{{ $cat->descripcion }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light p-3 px-4">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Eliminar Categoría -->
    <div class="modal fade" id="modalEliminarCat{{ $cat->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content modal-content-clean">
                <div class="modal-body p-4 text-center bg-white">
                    <i class="bi bi-exclamation-circle text-danger mb-3" style="font-size: 2.8rem;"></i>
                    <h6 class="fw-bold">¿Eliminar Categoría?</h6>
                    <p class="text-muted small mb-4">Se eliminará la categoría <strong>"{{ $cat->nombre }}"</strong>. Esta acción no se puede deshacer.</p>
                    <form action="/configuracion/categorias/{{ $cat->id }}" method="POST">
                        @csrf @method('DELETE')
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-light border w-50" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger w-50 fw-bold">Eliminar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- 3. MODALES DE MARCAS (EDITAR Y ELIMINAR) -->
@foreach($marcas as $marca)
    <!-- Modal Editar Marca -->
    <div class="modal fade" id="editarMarca{{ $marca->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content modal-content-clean">
                <form action="/configuracion/marcas/{{ $marca->id }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header bg-warning bg-opacity-25 border-0 py-3 px-4">
                        <h6 class="modal-title fw-bold text-dark m-0"><i class="bi bi-pencil-fill me-1"></i> Editar Marca</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 bg-white">
                        <label class="form-label text-muted small fw-bold">Nombre de la Marca *</label>
                        <input type="text" name="nombre" class="form-control text-uppercase fw-bold" value="{{ $marca->nombre }}" required>
                    </div>
                    <div class="modal-footer border-0 bg-light p-3">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning fw-bold px-3">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Marca -->
    <div class="modal fade" id="eliminarMarca{{ $marca->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content modal-content-clean">
                <div class="modal-body p-4 text-center bg-white">
                    <i class="bi bi-exclamation-circle text-danger mb-3" style="font-size: 2.5rem;"></i>
                    <h6 class="fw-bold">¿Eliminar Marca?</h6>
                    <p class="text-muted small mb-4">Se eliminará la marca <strong>"{{ $marca->nombre }}"</strong>.</p>
                    <form action="/configuracion/marcas/{{ $marca->id }}" method="POST">
                        @csrf @method('DELETE')
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-light border w-50" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger w-50 fw-bold">Eliminar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- 4. MODALES DE UNIDADES (EDITAR Y ELIMINAR) -->
@foreach($unidades as $unidad)
    <!-- Modal Editar Unidad -->
    <div class="modal fade" id="editarUnidad{{ $unidad->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content modal-content-clean">
                <form action="/configuracion/unidades/{{ $unidad->id }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header bg-warning bg-opacity-25 border-0 py-3 px-4">
                        <h6 class="modal-title fw-bold text-dark m-0"><i class="bi bi-pencil-fill me-1"></i> Editar Unidad</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 bg-white">
                        <label class="form-label text-muted small fw-bold">Nombre de la Unidad *</label>
                        <input type="text" name="nombre" class="form-control fw-bold" value="{{ $unidad->nombre }}" required>
                    </div>
                    <div class="modal-footer border-0 bg-light p-3">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning fw-bold px-3">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Unidad -->
    <div class="modal fade" id="eliminarUnidad{{ $unidad->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content modal-content-clean">
                <div class="modal-body p-4 text-center bg-white">
                    <i class="bi bi-exclamation-circle text-danger mb-3" style="font-size: 2.5rem;"></i>
                    <h6 class="fw-bold">¿Eliminar Unidad?</h6>
                    <p class="text-muted small mb-4">Se eliminará la unidad <strong>"{{ $unidad->nombre }}"</strong>.</p>
                    <form action="/configuracion/unidades/{{ $unidad->id }}" method="POST">
                        @csrf @method('DELETE')
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-light border w-50" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger w-50 fw-bold">Eliminar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

<script>
// Función para marcar checkbox al hacer clic en la fila
function toggleCheckFila(event, checkId) {
    if (event.target.tagName.toLowerCase() === 'input') return;
    const check = document.getElementById(checkId);
    if (check) {
        check.checked = !check.checked;
        actualizarConteoMasivo();
    }
}

// Abrir modal masivo con categoría pre-seleccionada
function abrirAsignacionParaCategoria(idCat) {
    const select = document.getElementById('selectCategoriaDestino');
    if (select) select.value = idCat;
    const modalEl = document.getElementById('modalAsignacionMasiva');
    if (modalEl) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
}

function actualizarConteoMasivo() {
    const checks = document.querySelectorAll('.check-prod-item:checked');
    const badge = document.getElementById('badgeConteoSeleccionados');
    if (badge) {
        badge.textContent = `${checks.length} seleccionado(s)`;
    }
}

// Validación antes de enviar formulario masivo
function validarAccionMasiva(tipo) {
    const seleccionados = document.querySelectorAll('.check-prod-item:checked');
    if (seleccionados.length === 0) {
        alert('Por favor selecciona al menos un producto de la lista marcando su casilla.');
        return false;
    }

    if (tipo === 'asignar') {
        const selectCat = document.getElementById('selectCategoriaDestino');
        if (!selectCat || !selectCat.value) {
            alert('Por favor selecciona la Categoría de Destino en el Paso 1.');
            if (selectCat) selectCat.focus();
            return false;
        }
    }

    if (tipo === 'quitar') {
        return confirm(`¿Estás seguro de quitar la categoría a los ${seleccionados.length} producto(s) seleccionados? Quedarán desvinculados como "Sin Categoría".`);
    }

    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    // 1. Recordar pestaña activa
    const activeTabKey = 'configuracion_active_tab';
    const savedTab = localStorage.getItem(activeTabKey);
    if (savedTab) {
        const tabEl = document.querySelector(`button[data-bs-target="${savedTab}"]`);
        if (tabEl) {
            const tabInstance = bootstrap.Tab.getOrCreateInstance(tabEl);
            tabInstance.show();
        }
    }

    const tabButtons = document.querySelectorAll('#configTabs button[data-bs-toggle="tab"]');
    tabButtons.forEach(btn => {
        btn.addEventListener('shown.bs.tab', function(e) {
            const target = e.target.getAttribute('data-bs-target');
            localStorage.setItem(activeTabKey, target);
        });
    });

    // 2. Lógica de Filtrado y Selección Masiva
    const buscadorMasivo = document.getElementById('buscadorMasivoProd');
    const filtroCatMasivo = document.getElementById('filtroCategoriaActualMasivo');
    const filasMasivas = document.querySelectorAll('.fila-prod-masiva');
    const txtTotalVisibles = document.getElementById('txtTotalVisiblesMasivo');
    const checksItems = document.querySelectorAll('.check-prod-item');

    function filtrarProductosMasivo() {
        const query = buscadorMasivo ? buscadorMasivo.value.toLowerCase().trim() : '';
        const catFiltro = filtroCatMasivo ? filtroCatMasivo.value : 'todos';
        let visibles = 0;

        filasMasivas.forEach(fila => {
            const texto = fila.getAttribute('data-texto') || '';
            const catId = fila.getAttribute('data-cat') || '0';

            const matchTexto = (query === '' || texto.includes(query));
            let matchCat = true;
            if (catFiltro === 'sin_cat') {
                matchCat = (catId === '0' || catId === '');
            } else if (catFiltro !== 'todos') {
                matchCat = (catId === catFiltro);
            }

            if (matchTexto && matchCat) {
                fila.style.display = '';
                visibles++;
            } else {
                fila.style.display = 'none';
            }
        });

        if (txtTotalVisibles) {
            txtTotalVisibles.textContent = `Mostrando ${visibles} producto(s)`;
        }
    }

    if (buscadorMasivo) buscadorMasivo.addEventListener('input', filtrarProductosMasivo);
    if (filtroCatMasivo) filtroCatMasivo.addEventListener('change', filtrarProductosMasivo);

    // Botón Marcar Visibles
    const btnMarcarVisibles = document.getElementById('btnMarcarVisibles');
    if (btnMarcarVisibles) {
        btnMarcarVisibles.addEventListener('click', function() {
            filasMasivas.forEach(fila => {
                if (fila.style.display !== 'none') {
                    const chk = fila.querySelector('.check-prod-item');
                    if (chk) chk.checked = true;
                }
            });
            actualizarConteoMasivo();
        });
    }

    // Botón Desmarcar Todos
    const btnDesmarcar = document.getElementById('btnDesmarcarTodosMasivo');
    if (btnDesmarcar) {
        btnDesmarcar.addEventListener('click', function() {
            checksItems.forEach(chk => chk.checked = false);
            actualizarConteoMasivo();
        });
    }

    // Event listener en checks individuales
    checksItems.forEach(chk => {
        chk.addEventListener('change', actualizarConteoMasivo);
    });
});

function previewSelectedLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('previewLogoImg');
            if (preview) {
                preview.src = e.target.result;
            }
            const info = document.getElementById('logoFileInfo');
            if (info) {
                info.classList.remove('d-none');
                info.innerHTML = `<i class="bi bi-check-circle-fill text-success me-1"></i> Seleccionado: <strong>${input.files[0].name}</strong> (${(input.files[0].size / 1024).toFixed(1)} KB)`;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
