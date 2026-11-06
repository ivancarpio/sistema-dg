@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold m-0"><i class="bi bi-gear text-primary me-2"></i> Configuración del Sistema</h3>
        <small class="text-muted">Administra catálogos y ajustes generales</small>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-3">
    <!-- PESTAÑAS DE NAVEGACIÓN -->
    <div class="card-header bg-white pt-3 pb-0 border-0">
        <ul class="nav nav-tabs border-bottom-0" id="configTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold text-dark" id="marcas-tab" data-bs-toggle="tab" data-bs-target="#marcas" type="button" role="tab"><i class="bi bi-tags me-1"></i> Marcas</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-dark" id="unidades-tab" data-bs-toggle="tab" data-bs-target="#unidades" type="button" role="tab"><i class="bi bi-rulers me-1"></i> Unidades</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-dark" id="pagos-tab" data-bs-toggle="tab" data-bs-target="#pagos" type="button" role="tab"><i class="bi bi-qr-code-scan me-1"></i> Pagos / QR</button>
            </li>
        </ul>
    </div>

    <!-- CONTENIDO DE LAS PESTAÑAS -->
    <div class="card-body bg-light p-4">
        <div class="tab-content" id="configTabsContent">
            
            <!-- PESTAÑA: MARCAS -->
            <div class="tab-pane fade show active" id="marcas" role="tabpanel">
                <div class="row">
                    <div class="col-md-5 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white"><h6 class="m-0 fw-bold">Agregar Nueva Marca</h6></div>
                            <div class="card-body">
                                <form action="/configuracion/marcas" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Nombre de la Marca</label>
                                        <input type="text" name="nombre" class="form-control text-uppercase" required placeholder="Ej. TRAMONTINA">
                                    </div>
                                    <button class="btn btn-primary w-100 fw-bold">Guardar Marca</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-0">
                                <table class="table table-hover m-0">
                                    <thead class="table-light"><tr><th class="ps-3">ID</th><th>Marca</th><th class="text-end pe-3">Editar</th></tr></thead>
                                    <tbody>
                                        @foreach($marcas as $marca)
                                        <tr>
                                            <td class="ps-3 text-muted">{{ $marca->id }}</td>
                                            <td class="fw-bold">{{ $marca->nombre }}</td>
                                            <td class="text-end pe-3">
                                                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editarMarca{{ $marca->id }}"><i class="bi bi-pencil"></i></button>
                                            </td>
                                        </tr>
                                        <!-- Modal Editar Marca -->
                                        <div class="modal fade" id="editarMarca{{ $marca->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                                <div class="modal-content">
                                                    <form action="/configuracion/marcas/{{ $marca->id }}" method="POST">
                                                        @csrf @method('PUT')
                                                        <div class="modal-header bg-warning text-dark"><h6 class="modal-title fw-bold">Editar Marca</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                                        <div class="modal-body">
                                                            <input type="text" name="nombre" class="form-control text-uppercase" value="{{ $marca->nombre }}" required>
                                                        </div>
                                                        <div class="modal-footer"><button type="submit" class="btn btn-warning w-100 fw-bold">Actualizar</button></div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PESTAÑA: UNIDADES -->
            <div class="tab-pane fade" id="unidades" role="tabpanel">
                <div class="row">
                    <div class="col-md-5 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white"><h6 class="m-0 fw-bold">Agregar Unidad de Medida</h6></div>
                            <div class="card-body">
                                <form action="/configuracion/unidades" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Nombre de la Unidad</label>
                                        <input type="text" name="nombre" class="form-control" required placeholder="Ej. Kilogramo">
                                    </div>
                                    <button class="btn btn-primary w-100 fw-bold">Guardar Unidad</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-0">
                                <table class="table table-hover m-0">
                                    <thead class="table-light"><tr><th class="ps-3">ID</th><th>Unidad</th><th class="text-end pe-3">Editar</th></tr></thead>
                                    <tbody>
                                        @foreach($unidades as $unidad)
                                        <tr>
                                            <td class="ps-3 text-muted">{{ $unidad->id }}</td>
                                            <td class="fw-bold">{{ $unidad->nombre }}</td>
                                            <td class="text-end pe-3">
                                                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editarUnidad{{ $unidad->id }}"><i class="bi bi-pencil"></i></button>
                                            </td>
                                        </tr>
                                        <!-- Modal Editar Unidad -->
                                        <div class="modal fade" id="editarUnidad{{ $unidad->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                                <div class="modal-content">
                                                    <form action="/configuracion/unidades/{{ $unidad->id }}" method="POST">
                                                        @csrf @method('PUT')
                                                        <div class="modal-header bg-warning text-dark"><h6 class="modal-title fw-bold">Editar Unidad</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                                        <div class="modal-body">
                                                            <input type="text" name="nombre" class="form-control" value="{{ $unidad->nombre }}" required>
                                                        </div>
                                                        <div class="modal-footer"><button type="submit" class="btn btn-warning w-100 fw-bold">Actualizar</button></div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PESTAÑA: PAGOS / QR -->
            <div class="tab-pane fade" id="pagos" role="tabpanel">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm text-center">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3">QR Actual de Transferencias</h5>
                                <!-- Mostramos el QR actual agregando el tiempo para evitar la caché del navegador -->
                                <img src="{{ asset('img/qr_banco.jpeg') }}?v={{ time() }}" alt="QR de Pago" class="img-fluid border rounded shadow-sm mb-4" style="max-height: 250px;">
                                
                                <form action="/configuracion/qr" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3 text-start">
                                        <label class="form-label fw-bold">Subir Nuevo Código QR (Imagen)</label>
                                        <input class="form-control" type="file" name="qr_image" accept=".jpg,.jpeg,.png" required>
                                        <small class="text-muted">Formatos: JPG, PNG. Se actualizará automáticamente en el Punto de Venta.</small>
                                    </div>
                                    <button class="btn btn-success fw-bold w-100"><i class="bi bi-upload me-2"></i> Actualizar QR</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection