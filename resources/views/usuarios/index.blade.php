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
    .icon-action { font-size: 1.1rem; transition: transform 0.2s; cursor: pointer; }
    .icon-action:hover { transform: scale(1.15); }
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
        <div class="bg-primary bg-opacity-10 p-2 rounded me-3 text-primary">
            <i class="bi bi-people fs-3"></i>
        </div>
        <div>
            <h3 class="fw-bold m-0 text-dark">Gestión de Usuarios</h3>
            <small class="text-muted">Administra los accesos y roles del sistema</small>
        </div>
    </div>
    <button class="btn btn-primary text-white fw-bold shadow-sm px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
        <i class="bi bi-person-plus-fill me-1"></i> Nuevo Usuario
    </button>
</div>

<!-- BARRA DE FILTROS BLANCA -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <div class="input-group" style="height: 42px;">
                    <button class="search-btn-left"><i class="bi bi-search"></i></button>
                    <input type="text" id="buscadorUsuarios" class="form-control search-input" placeholder="Buscar por nombre, correo o rol...">
                </div>
            </div>
            <div class="col-md-6 text-end">
                <span class="fw-bold text-dark">Total:</span> <span class="text-muted">{{ count($usuarios) }} usuario(s)</span>
            </div>
        </div>
    </div>
</div>

<!-- TABLA DE USUARIOS -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-custom m-0">
                <thead class="table-custom-header">
                    <tr>
                        <th class="ps-4">NOMBRE</th>
                        <th>CORREO ELECTRÓNICO</th>
                        <th class="text-center">ROL</th>
                        <th class="text-center pe-4">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $user)
                    <tr class="fila-usuario">
                        <td class="ps-4">
                            <div class="d-flex align-items-center fw-bold text-dark">
                                <i class="bi bi-person-circle text-secondary me-2 fs-4"></i>
                                {{ $user->name }}
                            </div>
                        </td>
                        <td class="text-muted small">{{ $user->email }}</td>
                        <td class="text-center">
                            @if($user->rol == 'admin')
                                <span class="badge bg-primary rounded-pill px-3 py-2">Administrador</span>
                            @else
                                <span class="badge bg-secondary rounded-pill px-3 py-2">Cajero</span>
                            @endif
                        </td>
                        <td class="text-center pe-4">
                            <button type="button" class="btn btn-link p-0 m-0 text-info icon-action me-3 text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $user->id }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button type="button" class="btn btn-link p-0 m-0 text-danger icon-action text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalEliminar{{ $user->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= MODALES ================= -->

<!-- MODAL NUEVO USUARIO -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="/usuarios" method="POST" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-body p-4">
                <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-person-plus text-primary me-2"></i> Crear Nuevo Usuario</h5>
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Nombre Completo *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Correo (Usuario de acceso) *</label>
                    <input type="email" name="email" class="form-control" placeholder="ejemplo@ferreteria.com" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Contraseña *</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold">Rol del Sistema *</label>
                    <select name="rol" class="form-select" required>
                        <option value="cajero">Cajero (Solo Ventas e Inventario)</option>
                        <option value="admin">Administrador (Acceso Total)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-light">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary text-white fw-bold px-4">Guardar Usuario</button>
            </div>
        </form>
    </div>
</div>

<!-- MODALES EDITAR Y ELIMINAR -->
@foreach($usuarios as $user)
    <!-- Editar -->
    <div class="modal fade" id="modalEditar{{ $user->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="/usuarios/{{ $user->id }}" method="POST" class="modal-content border-0 shadow">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-pencil-square text-info me-2"></i> Editar Usuario</h5>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nombre Completo *</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Correo Electrónico *</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nueva Contraseña <span class="fw-normal">(Dejar en blanco si no desea cambiarla)</span></label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">Rol del Sistema *</label>
                        <select name="rol" class="form-select" required>
                            <option value="cajero" {{ $user->rol == 'cajero' ? 'selected' : '' }}>Cajero</option>
                            <option value="admin" {{ $user->rol == 'admin' ? 'selected' : '' }}>Administrador</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info text-white fw-bold px-4">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Eliminar -->
    <div class="modal fade" id="modalEliminar{{ $user->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow">
                <div class="modal-body p-4 text-center">
                    <i class="bi bi-trash text-danger mb-3" style="font-size: 2.5rem;"></i>
                    <h6 class="fw-bold">Eliminar Usuario</h6>
                    <p class="text-muted small mb-4">¿Revocar acceso a <strong>{{ $user->name }}</strong>?</p>
                    <form action="/usuarios/{{ $user->id }}" method="POST">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const buscador = document.getElementById('buscadorUsuarios');
    const filas = document.querySelectorAll('.fila-usuario');
    
    buscador.addEventListener('keyup', function() {
        const texto = this.value.toLowerCase();
        filas.forEach(fila => {
            const contenido = fila.textContent.toLowerCase();
            fila.style.display = contenido.includes(texto) ? '' : 'none';
        });
    });
});
</script>
@endsection