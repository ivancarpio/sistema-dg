@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

<style>
    /* Estilo para que los títulos parezcan botones al pasar el mouse */
    th.sortable { cursor: pointer; user-select: none; transition: background-color 0.2s; }
    th.sortable:hover { background-color: #e9ecef; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold m-0">Gestión de Inventario</h3>
        <small class="text-muted">Controla y ajusta el stock de tus productos</small>
    </div>
    <button class="btn btn-info text-white fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoAjuste">
        <i class="bi bi-plus-lg"></i> Nuevo Ajuste
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-3">
    <!-- CABECERA CON EL BUSCADOR -->
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold m-0"><i class="bi bi-graph-down-arrow text-primary me-2"></i> Historial de Ajustes</h5>
        <div class="input-group" style="width: 300px;">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" id="buscadorInventario" class="form-control border-start-0 bg-light" placeholder="Buscar fecha, producto, usuario...">
        </div>
    </div>

    <!-- TABLA DE DATOS -->
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0" id="tablaInventario">
                <thead class="table-light">
                    <tr>
                        <!-- Agregamos la clase 'sortable' y un icono a cada cabecera -->
                        <th class="ps-4 sortable" data-col="0">FECHA <i class="icono-orden bi bi-arrow-down-up text-muted ms-1" style="font-size: 0.8em;"></i></th>
                        <th class="sortable" data-col="1">PRODUCTO <i class="icono-orden bi bi-arrow-down-up text-muted ms-1" style="font-size: 0.8em;"></i></th>
                        <th class="sortable" data-col="2">TIPO <i class="icono-orden bi bi-arrow-down-up text-muted ms-1" style="font-size: 0.8em;"></i></th>
                        <th class="sortable" data-col="3">CANTIDAD <i class="icono-orden bi bi-arrow-down-up text-muted ms-1" style="font-size: 0.8em;"></i></th>
                        <th class="sortable" data-col="4">MOTIVO <i class="icono-orden bi bi-arrow-down-up text-muted ms-1" style="font-size: 0.8em;"></i></th>
                        <th class="sortable" data-col="5">USUARIO <i class="icono-orden bi bi-arrow-down-up text-muted ms-1" style="font-size: 0.8em;"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimientos as $mov)
                    <!-- Clase 'fila-movimiento' para que el buscador la identifique -->
                    <tr class="fila-movimiento">
                        
                        <!-- Usamos data-sort-value para que JS ordene por el valor exacto oculto, no por el HTML visual -->
                        <td class="ps-4 text-muted small" data-sort-value="{{ $mov->fecha ?? '0000-00-00' }}">
                            {{ $mov->fecha ?? 'N/A' }}
                        </td>
                        
                        <td data-sort-value="{{ optional($mov->producto)->nombre ?? '' }}">
                            <div class="fw-bold">{{ optional($mov->producto)->nombre ?? 'Producto no encontrado' }}</div>
                            <small class="text-muted">Código: {{ optional($mov->producto)->codigo ?? 'N/A' }}</small>
                        </td>
                        
                        <td data-sort-value="{{ strtolower($mov->tipo ?? '') }}">
                            @if(strtolower($mov->tipo ?? '') == 'agregar' || strtolower($mov->tipo ?? '') == 'entrada')
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Agregar</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Restar</span>
                            @endif
                        </td>
                        
                        <td class="fw-bold" data-sort-value="{{ $mov->cantidad ?? 0 }}">
                            {{ $mov->cantidad ?? 0 }}
                        </td>
                        
                        <td class="text-muted" data-sort-value="{{ $mov->motivo ?? '' }}">
                            {{ $mov->motivo ?? 'Sin motivo registrado' }}
                        </td>
                        
                        <td data-sort-value="{{ optional($mov->usuario)->name ?? 'Administrador' }}">
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-person-circle me-1 text-secondary"></i>
                                {{ optional($mov->usuario)->name ?? 'Administrador' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr id="filaVacia">
                        <td colspan="6" class="text-center py-5 text-muted">No hay movimientos de inventario registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Nuevo Ajuste (Se mantiene igual) -->
<div class="modal fade" id="modalNuevoAjuste" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-sliders me-2"></i> Registrar Nuevo Ajuste</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="/inventario" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Seleccionar Producto *</label>
                        <select name="id_producto" id="selectProductoModal" required>
                            <option value="" selected disabled>Elije un producto...</option>
                            @foreach($productos as $p)
                                <option value="{{ $p->id }}">{{ $p->codigo }} - {{ $p->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tipo de Ajuste *</label>
                            <select name="tipo" class="form-select" required>
                                <option value="ENTRADA">Agregar (Entrada)</option>
                                <option value="SALIDA">Restar (Salida)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Cantidad *</label>
                            <input type="number" name="cantidad" class="form-control" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Motivo</label>
                        <textarea name="motivo" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- MOTOR DE BÚSQUEDA Y ORDENAMIENTO EN VIVO ⚙️    -->
<!-- ============================================== -->

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- NUEVO: INICIALIZAR EL BUSCADOR DEL MODAL ---
    new TomSelect("#selectProductoModal", {
        create: false,
        placeholder: "Escribe para buscar un producto...",
        sortField: {
            field: "text",
            direction: "asc"
        }
    });

    // --- LÓGICA DEL BUSCADOR DE LA TABLA (Lo que ya tenías) ---
    const buscador = document.getElementById('buscadorInventario');
    const filas = document.querySelectorAll('.fila-movimiento');
    
    buscador.addEventListener('keyup', function() {
        const texto = this.value.toLowerCase();
        filas.forEach(fila => {
            const contenido = fila.textContent.toLowerCase();
            fila.style.display = contenido.includes(texto) ? '' : 'none';
        });
    });

    // --- LÓGICA DE ORDENAMIENTO (Lo que ya tenías) ---
    const tabla = document.getElementById('tablaInventario');
    const headers = tabla.querySelectorAll('th.sortable');
    let columnaActual = -1;
    let ordenAscendente = true;

    headers.forEach((header, index) => {
        header.addEventListener('click', () => {
            ordenAscendente = (columnaActual === index) ? !ordenAscendente : true;
            columnaActual = index;

            headers.forEach(h => {
                h.querySelector('.icono-orden').className = 'icono-orden bi bi-arrow-down-up text-muted ms-1';
            });
            
            const iconoSeleccionado = ordenAscendente ? 'bi-caret-down-fill text-primary' : 'bi-caret-up-fill text-primary';
            header.querySelector('.icono-orden').className = `icono-orden bi ${iconoSeleccionado} ms-1`;

            const tbody = tabla.querySelector('tbody');
            const filasArray = Array.from(tbody.querySelectorAll('tr.fila-movimiento'));

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
});
</script>
@endsection