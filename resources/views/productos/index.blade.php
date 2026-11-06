@extends('layouts.app')

@section('content')
<style>
    .table-custom-header th {
        background-color: #f1f5f9 !important;
        font-size: 0.8rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 20px 10px;
        border-bottom: none !important;
    }
    .table-custom-header th:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
    .table-custom-header th:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }

    .table-custom tbody tr { border-bottom: 1px solid #f0f2f5; transition: background-color 0.2s; }
    .table-custom tbody tr:hover { background-color: #f8f9fa; }
    .table-custom td { vertical-align: middle; padding: 15px 10px; }
    
    .search-btn-left { background-color: #0dcaf0; color: white; border-top-left-radius: 8px; border-bottom-left-radius: 8px; border: none; padding: 0 20px; }
    .search-input { border-top-right-radius: 8px; border-bottom-right-radius: 8px; border: 1px solid #e2e8f0; background-color: #f8fafc; }
    
    .btn-dark-custom { background-color: #1e293b; color: white; border-radius: 6px; }
    .btn-dark-custom:hover { background-color: #0f172a; color: white; }
    
    .icon-action { font-size: 1.1rem; transition: transform 0.2s; cursor: pointer; }
    .icon-action:hover { transform: scale(1.15); }

    th.sortable { cursor: pointer; user-select: none; transition: color 0.2s; }
    th.sortable:hover { color: #0dcaf0 !important; }
</style>

<!-- ALERTAS -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- ENCABEZADO -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div class="bg-info bg-opacity-10 p-2 rounded me-3 text-info">
            <i class="bi bi-box-seam fs-3"></i>
        </div>
        <div>
            <h3 class="fw-bold m-0 text-dark">Gestión de Productos</h3>
            <small class="text-muted">Administra tu catálogo de productos</small>
        </div>
    </div>
    
    <!-- SOLO ADMIN: BOTON NUEVO PRODUCTO -->
    @if(auth()->check() && auth()->user()->rol === 'admin')
    <button class="btn btn-info text-white fw-bold shadow-sm px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalNuevoProducto">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Producto
    </button>
    @endif
</div>

<!-- BARRA DE FILTROS BLANCA -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-4">
        <div class="mb-3">
            <span class="fw-bold text-dark">Total:</span> <span class="text-muted">{{ count($productos) }} producto(s)</span>
        </div>
        
        <div class="row g-3 align-items-center">
            <div class="col-md-5">
                <div class="input-group" style="height: 42px;">
                    <button class="search-btn-left"><i class="bi bi-search"></i></button>
                    <input type="text" id="buscadorProductos" class="form-control search-input" placeholder="Buscar productos...">
                </div>
            </div>
            <div class="col-md-3">
                <select id="filtroCategoria" class="form-select text-muted bg-light border-0" style="height: 42px;">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex justify-content-end gap-2">
                <a href="/productos" class="btn btn-dark-custom d-flex align-items-center px-4"><i class="bi bi-arrow-repeat me-2"></i> Actualizar</a>
                
                <!-- SOLO ADMIN: BOTON NUEVA CATEGORÍA -->
                @if(auth()->check() && auth()->user()->rol === 'admin')
                <button class="btn btn-dark-custom d-flex align-items-center px-4" data-bs-toggle="modal" data-bs-target="#modalNuevaCategoria">
                    <i class="bi bi-plus-lg me-2"></i> Nueva Categoría
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- TABLA DE PRODUCTOS -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-custom m-0" id="tablaProductos">
                <thead class="table-custom-header">
                    <tr>
                        <th class="ps-4 sortable text-start" style="width: 25%;" data-col="0">PRODUCTO <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i></th>
                        <th class="sortable text-center" style="width: 12%;" data-col="1">CÓDIGO <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i></th>
                        <th class="sortable text-center" style="width: 13%;" data-col="2">MARCA <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i></th>
                        <th class="sortable text-center" style="width: 15%;" data-col="3">PRECIO VENTA <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i></th>
                        <th class="sortable text-center" style="width: 10%;" data-col="4">COSTO <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i></th>
                        <th class="sortable text-center" style="width: 15%;" data-col="5">STOCK <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i></th>
                        
                        <!-- SOLO ADMIN: COLUMNA DE ACCIONES -->
                        @if(auth()->check() && auth()->user()->rol === 'admin')
                        <th class="text-center pe-4" style="width: 10%;">ACCIONES</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $producto)
                    <tr class="fila-producto" data-categoria="{{ $producto->id_categoria }}">
                        <td class="ps-4 text-start" data-sort-value="{{ strtolower($producto->nombre) }}">
                            <div class="d-flex align-items-center fw-bold text-dark text-uppercase" style="font-size: 0.85rem;">
                                <i class="bi bi-box-fill text-info me-2 fs-5"></i>
                                {{ $producto->nombre }}
                            </div>
                        </td>
                        <td class="text-muted small text-center" data-sort-value="{{ $producto->codigo }}">{{ $producto->codigo }}</td>
                        
                        <td class="text-muted small text-center fw-bold" data-sort-value="{{ strtolower(optional($producto->marca)->nombre ?? '') }}">
                            {{ optional($producto->marca)->nombre ?? '---' }}
                        </td>
                        
                        <td class="fw-bold text-success small text-center" data-sort-value="{{ $producto->precio_venta ?? 0 }}">
                            Bs. {{ number_format($producto->precio_venta ?? 0, 2) }}
                        </td>
                        <td class="text-muted small text-center" data-sort-value="{{ $producto->costo ?? 0 }}">
                            Bs. {{ number_format($producto->costo ?? 0, 2) }}
                        </td>
                        <td class="text-center" data-sort-value="{{ $producto->stock ?? 0 }}">
                            @php
                                $stockActual =$producto->stock ?? 0;
                                $stockMinimo =$producto->stock_minimo ?? 0;
                                if ($stockActual <= $stockMinimo) {$colorBarra = 'bg-danger'; }
                                elseif ($stockActual <= ($stockMinimo == 0 ? 15 : $stockMinimo * 2)) {$colorBarra = 'bg-warning'; }
                                else { $colorBarra = 'bg-success'; }$metaVisual = $stockMinimo == 0 ? 50 :$stockMinimo * 3;
                                $porcentaje =$stockActual > 0 ? min(100, ($stockActual / $metaVisual) * 100) : 0;
                            @endphp
                            <div class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $stockActual }} UND.</div>
                            <div class="text-muted mb-1" style="font-size: 0.7rem;">Min: {{ $stockMinimo }}</div>
                            <div class="progress mx-auto" style="height: 4px; width: 60px; background-color: #e2e8f0; border-radius: 4px;">
                                <div class="progress-bar {{ $colorBarra }}" role="progressbar" style="width: {{ $porcentaje }}%;"></div>
                            </div>
                        </td>
                        
                        <!-- SOLO ADMIN: BOTONES DE EDITAR Y ELIMINAR -->
                        @if(auth()->check() && auth()->user()->rol === 'admin')
                        <td class="text-center pe-4">
                            <button type="button" class="btn btn-link p-0 m-0 text-info icon-action me-3 border-0 shadow-none text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $producto->id }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button type="button" class="btn btn-link p-0 m-0 text-danger icon-action border-0 shadow-none text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalEliminar{{ $producto->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No hay productos registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3 text-muted small bg-light rounded p-2 text-center">
            Mostrando {{ count($productos) }} producto(s)
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- ZONA DE MODALES (PROTEGIDA SOLO PARA ADMIN)    -->
<!-- ============================================== -->
@if(auth()->check() && auth()->user()->rol === 'admin')

    <!-- MODAL: NUEVA CATEGORÍA -->
    <div class="modal fade" id="modalNuevaCategoria" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <form action="/categorias" method="POST" class="modal-content border-0 shadow-lg rounded-3">
                @csrf
                <div class="modal-body p-4">
                    <h6 class="fw-bold mb-4 text-dark text-center">Nueva Categoría</h6>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nombre *</label>
                        <!-- Agregamos id="inputNombreProducto" aquí -->
                        <input type="text" name="nombre" id="inputNombreProducto" class="form-control" placeholder="Ej. BROCHAS DURACOLOR #6" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">Descripción (opcional)</label>
                        <textarea name="descripcion" class="form-control" rows="2" placeholder="Descripción de la categoría..."></textarea>
                    </div>
                    <div class="d-flex justify-content-between gap-2">
                        <button type="button" class="btn btn-light border w-50 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-info text-white w-50 fw-bold">Crear Categoría</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: NUEVO PRODUCTO -->
    <div class="modal fade" id="modalNuevoProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <form action="/productos" method="POST" class="modal-content border-0 shadow">
                @csrf
                <div class="modal-body p-5">
                    <h5 class="fw-bold mb-4 text-dark">Nuevo Producto</h5>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nombre *</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Código *</label>
                        <div class="input-group">
                            <!-- Es MUY importante que tenga el id="inputCodigoProducto" -->
                            <input type="text" name="codigo" id="inputCodigoProducto" class="form-control border-info-subtle" placeholder="Ej. PRD-12345" required>
                            
                            <!-- Aquí está el botón que faltaba, con el id="btnGenerarCodigo" -->
                            <button class="btn btn-info text-white fw-bold shadow-sm" type="button" id="btnGenerarCodigo" title="Generar código automático">
                                <i class="bi bi-magic me-1"></i> Generar
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Precio de Venta (Bs.) *</label>
                            <input type="number" name="precio_venta" step="0.01" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Costo (Bs.) *</label>
                            <input type="number" name="costo" step="0.01" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Unidad de Medida</label>
                            <select name="id_unidad" class="form-select">
                                <option value="">Seleccionar...</option>
                                @foreach($unidades as $unidad)
                                    <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Marca</label>
                            <select name="id_marca" class="form-select">
                                <option value="">Seleccionar...</option>
                                @foreach($marcas as $marca)
                                    <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Categoría</label>
                            <select name="id_categoria" class="form-select">
                                <option value="">Seleccionar...</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Stock Inicial *</label>
                            <input type="number" name="stock_inicial" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Stock Mínimo</label>
                            <input type="number" name="stock_minimo" class="form-control" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info text-white fw-bold px-5">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODALES EDITAR Y ELIMINAR -->
    @foreach($productos as $producto)
        <div class="modal fade" id="modalEditar{{ $producto->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <form action="/productos/{{ $producto->id }}" method="POST" class="modal-content border-0 shadow">
                    @csrf @method('PUT')
                    <div class="modal-body p-5">
                        <h5 class="fw-bold mb-4 text-dark">Editar Producto</h5>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Nombre *</label>
                            <input type="text" name="nombre" class="form-control" value="{{ $producto->nombre }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Código *</label>
                            <div class="input-group">
                                <!-- El campo de texto (le agregamos un ID para que el JS lo encuentre) -->
                                <input type="text" name="codigo" id="inputCodigoProducto" class="form-control" placeholder="Ej. CAB-7H6" required>
                                <!-- El nuevo botón celeste -->
                                <button class="btn btn-outline-info fw-bold" type="button" id="btnGenerarCodigo" title="Generar código aleatorio">
                                    <i class="bi bi-magic me-1"></i> Generar
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="2">{{ $producto->descripcion }}</textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Precio de Venta (Bs.) *</label>
                                <input type="number" name="precio_venta" step="0.01" class="form-control" value="{{ $producto->precio_venta }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Costo (Bs.) *</label>
                                <input type="number" name="costo" step="0.01" class="form-control" value="{{ $producto->costo }}" required>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold">Unidad de Medida</label>
                                <select name="id_unidad" class="form-select">
                                    <option value="">Seleccionar...</option>
                                    @foreach($unidades as $unidad)
                                        <option value="{{ $unidad->id }}" {{ $producto->id_unidad == $unidad->id ? 'selected' : '' }}>{{ $unidad->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold">Marca</label>
                                <select name="id_marca" class="form-select">
                                    <option value="">Seleccionar...</option>
                                    @foreach($marcas as $marca)
                                        <option value="{{ $marca->id }}" {{ $producto->id_marca == $marca->id ? 'selected' : '' }}>{{ $marca->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold">Categoría</label>
                                <select name="id_categoria" class="form-select">
                                    <option value="">Seleccionar...</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" {{ $producto->id_categoria == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Stock Mínimo</label>
                            <input type="number" name="stock_minimo" class="form-control" value="{{ $producto->stock_minimo }}">
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 bg-light">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-info text-white fw-bold px-5">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="modalEliminar{{ $producto->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow">
                    <div class="modal-body p-4 text-center">
                        <i class="bi bi-trash text-danger mb-3" style="font-size: 2.5rem;"></i>
                        <h6 class="fw-bold">Eliminar Producto</h6>
                        <p class="text-muted small mb-4">¿Eliminar <strong>{{ $producto->nombre }}</strong>?</p>
                        <form action="/productos/{{ $producto->id }}" method="POST">
                            @csrf @method('DELETE')
                            <div class="d-flex justify-content-between gap-2">
                                <button type="button" class="btn btn-light border w-50" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger w-50">Eliminar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endif
<!-- FIN ZONA DE MODALES PROTEGIDA -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ==========================================
    // 1. LÓGICA DE BÚSQUEDA Y FILTRADO COMBINADO
    // ==========================================
    const buscador = document.getElementById('buscadorProductos');
    const filtroCat = document.getElementById('filtroCategoria');
    const filas = document.querySelectorAll('.fila-producto');

    function filtrarTabla() {
        const textoBusqueda = buscador ? buscador.value.toLowerCase() : '';
        const categoriaSeleccionada = filtroCat ? filtroCat.value : '';

        filas.forEach(fila => {
            const contenidoFila = fila.textContent.toLowerCase();
            const coincideTexto = contenidoFila.includes(textoBusqueda);
            
            const idCategoriaFila = fila.getAttribute('data-categoria');
            const coincideCategoria = (categoriaSeleccionada === '') || (idCategoriaFila === categoriaSeleccionada);

            if (coincideTexto && coincideCategoria) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    }

    if (buscador) buscador.addEventListener('keyup', filtrarTabla);
    if (filtroCat) filtroCat.addEventListener('change', filtrarTabla);


    // ==========================================
    // 2. LÓGICA DE ORDENAMIENTO DE COLUMNAS
    // ==========================================
    const tabla = document.getElementById('tablaProductos');
    if(tabla) {
        const headers = tabla.querySelectorAll('th.sortable');
        let columnaActual = -1;
        let ordenAscendente = true;

        headers.forEach((header, index) => {
            header.addEventListener('click', () => {
                ordenAscendente = (columnaActual === index) ? !ordenAscendente : true;
                columnaActual = index;

                headers.forEach(h => {
                    let icon = h.querySelector('.icono-orden');
                    if(icon) {
                        icon.className = 'icono-orden bi bi-arrow-down-up ms-1';
                        icon.style.opacity = '0.5';
                    }
                });
                
                const iconActual = header.querySelector('.icono-orden');
                if(iconActual) {
                    iconActual.className = `icono-orden bi ${ordenAscendente ? 'bi-caret-down-fill' : 'bi-caret-up-fill'} text-info ms-1`;
                    iconActual.style.opacity = '1';
                }

                const tbody = tabla.querySelector('tbody');
                const filasArray = Array.from(tbody.querySelectorAll('tr.fila-producto'));

                filasArray.sort((a, b) => {
                    let valA = a.children[index].getAttribute('data-sort-value');
                    let valB = b.children[index].getAttribute('data-sort-value');

                    if (!isNaN(valA) && !isNaN(valB) && valA !== '' && valB !== '') {
                        valA = parseFloat(valA);
                        valB = parseFloat(valB);
                    } else {
                        valA = valA.toString().toLowerCase();
                        valB = valB.toString().toLowerCase();
                    }

                    if (valA < valB) return ordenAscendente ? -1 : 1;
                    if (valA > valB) return ordenAscendente ? 1 : -1;
                    return 0;
                });

                filasArray.forEach(fila => tbody.appendChild(fila));
            });
        });
    }


    // ==========================================
    // 3. GENERADOR DE CÓDIGO INTELIGENTE (MODO BLINDADO)
    // ==========================================
    // Escuchamos clics en cualquier botón de generar (por si hay varios modales)
    document.querySelectorAll('#btnGenerarCodigo').forEach(btnGenerar => {
        btnGenerar.addEventListener('click', function() {
            // Buscamos el contenedor modal o formulario más cercano a ESTE botón específico
            const modalOForm = this.closest('.modal') || this.closest('form') || document;
            
            // Encontramos los inputs exactos de esta ventana
            const inputNombre = modalOForm.querySelector('input[name="nombre"]') || document.getElementById('inputNombreProducto');
            const inputCodigo = modalOForm.querySelector('input[name="codigo"]') || document.getElementById('inputCodigoProducto');

            let nombre = inputNombre ? inputNombre.value.trim().toUpperCase() : '';
            
            if (nombre === '') {
                alert('Por favor, ingresa primero el Nombre del producto para generar su código automático.');
                if (inputNombre) inputNombre.focus();
                return;
            }

            // Lógica de abreviación inteligente
            let palabras = nombre.split(' ');
            let prefijo = palabras[0].substring(0, 3);
            let sufijo = '';

            if (palabras.length > 1) {
                let letrasSegunda = palabras[1].replace(/[^A-Z]/g, '').substring(0, 3);
                let numeros = nombre.replace(/[^0-9]/g, '').substring(0, 4); 
                sufijo = letrasSegunda + numeros;
            }
            
            if (sufijo === '') {
                sufijo = Math.floor(100 + Math.random() * 900).toString();
            }

            let codigoFinal = prefijo + '-' + sufijo;
            
            if (inputCodigo) {
                inputCodigo.value = codigoFinal;
                inputCodigo.classList.add('bg-info', 'bg-opacity-10');
                setTimeout(() => inputCodigo.classList.remove('bg-info', 'bg-opacity-10'), 300);
            }
        });
    });

});
</script>
@endsection