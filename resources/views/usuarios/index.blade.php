@extends('layouts.app')

@section('content')
<style>
    .table-usuarios-header th {
        background-color: #f1f5f9 !important;
        font-size: 0.78rem;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 16px 12px;
        border-bottom: 2px solid #e2e8f0 !important;
    }
    .table-usuarios-header th:first-child { border-top-left-radius: 10px; }
    .table-usuarios-header th:last-child { border-top-right-radius: 10px; }
    .table-usuarios tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.2s ease;
    }
    .table-usuarios tbody tr:hover {
        background-color: #f8fafc;
    }
    .table-usuarios td {
        vertical-align: middle;
        padding: 16px 12px;
    }
    .avatar-user {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.05rem;
    }
    .search-input-group {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }
    .permiso-card {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        background-color: #ffffff;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .permiso-card:hover {
        background-color: #f8fafc;
        border-color: #cbd5e1;
    }
    .permiso-card input:checked ~ label {
        font-weight: 700;
    }
    .permiso-chip {
        font-size: 0.72rem;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin: 2px;
    }
    /* Asegurar fondo blanco y sin transparencia en todos los modales */
    .modal-content-clean {
        background-color: #ffffff !important;
        border: none !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1) !important;
        border-radius: 16px !important;
        overflow: hidden;
    }
</style>

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

<!-- ENCABEZADO -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3 text-primary">
            <i class="bi bi-people-fill fs-3"></i>
        </div>
        <div>
            <h3 class="fw-bold m-0 text-dark">Gestión de Personal y Accesos</h3>
            <small class="text-muted">Administra los usuarios del sistema, sus datos personales y permisos detallados</small>
        </div>
    </div>
    <button class="btn btn-primary text-white fw-bold shadow-sm px-4 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
        <i class="bi bi-person-plus-fill me-1"></i> Registrar Nuevo Personal
    </button>
</div>

<!-- BARRA DE BÚSQUEDA Y RESUMEN -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3 px-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <div class="input-group search-input-group" style="height: 44px;">
                    <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-search"></i></span>
                    <input type="text" id="buscadorUsuarios" class="form-control border-0 bg-transparent" placeholder="Buscar por nombre, CI, celular, correo o rol...">
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="d-inline-flex gap-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill">
                        <i class="bi bi-shield-check me-1"></i> {{ $usuarios->where('rol', 'admin')->count() }} Admin(s)
                    </span>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 rounded-pill">
                        <i class="bi bi-person-badge me-1"></i> {{ $usuarios->where('rol', 'cajero')->count() }} Cajero(s)
                    </span>
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold">
                        Total: {{ count($usuarios) }} usuario(s)
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TABLA DE USUARIOS PROFESIONAL -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-usuarios m-0">
                <thead class="table-usuarios-header">
                    <tr>
                        <th class="ps-4">PERSONAL / USUARIO</th>
                        <th>IDENTIFICACIÓN Y CONTACTO</th>
                        <th>FECHAS CLAVE</th>
                        <th class="text-center">ROL</th>
                        <th>PERMISOS DEL SISTEMA</th>
                        <th class="text-end pe-4">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $user)
                    @php
                        $userPermisos = is_array($user->permisos) ? $user->permisos : (json_decode($user->permisos ?? '[]', true) ?? []);
                        $totalModulos = count($catalogoPermisos);
                        $conteoPermisos = ($user->rol === 'admin') ? $totalModulos : count($userPermisos);
                    @endphp
                    <tr class="fila-usuario">
                        <!-- Personal / Usuario -->
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-user me-3 {{ $user->rol === 'admin' ? 'bg-primary text-white' : 'bg-info bg-opacity-15 text-primary' }}">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark fs-6">{{ $user->name }}</div>
                                    <div class="text-muted small"><i class="bi bi-person-badge me-1"></i> <span class="fw-semibold text-secondary">{{ $user->email }}</span></div>
                                </div>
                            </div>
                        </td>

                        <!-- Identificación y Contacto -->
                        <td>
                            <div>
                                <span class="fw-bold text-dark"><i class="bi bi-card-text text-muted me-1"></i> CI:</span> 
                                <span class="text-secondary">{{ $user->ci ?: 'No registrado' }}</span>
                            </div>
                            <div class="small mt-1">
                                <span class="fw-bold text-dark"><i class="bi bi-telephone text-muted me-1"></i> Cel:</span> 
                                @if($user->celular)
                                    <a href="https://wa.me/591{{ preg_replace('/[^0-9]/', '', $user->celular) }}" target="_blank" class="text-decoration-none text-success fw-bold">
                                        <i class="bi bi-whatsapp"></i> {{ $user->celular }}
                                    </a>
                                @else
                                    <span class="text-muted">No registrado</span>
                                @endif
                            </div>
                        </td>

                        <!-- Fechas Clave -->
                        <td>
                            <div class="small">
                                <span class="text-muted">Ingreso:</span> 
                                <strong class="text-dark">{{ $user->fecha_ingreso ? \Carbon\Carbon::parse($user->fecha_ingreso)->format('d/m/Y') : ($user->created_at ? $user->created_at->format('d/m/Y') : '—') }}</strong>
                            </div>
                            <div class="small mt-1">
                                <span class="text-muted">Nacimiento:</span> 
                                <span class="text-secondary">{{ $user->fecha_nacimiento ? \Carbon\Carbon::parse($user->fecha_nacimiento)->format('d/m/Y') : 'No registrado' }}</span>
                            </div>
                        </td>

                        <!-- Rol -->
                        <td class="text-center">
                            @if($user->rol === 'admin')
                                <span class="badge bg-primary text-white rounded-pill px-3 py-2 fw-bold">
                                    <i class="bi bi-shield-fill-check me-1"></i> Administrador
                                </span>
                            @else
                                <span class="badge bg-secondary text-white rounded-pill px-3 py-2 fw-bold">
                                    <i class="bi bi-person-badge-fill me-1"></i> Cajero
                                </span>
                            @endif
                        </td>

                        <!-- Permisos Asignados -->
                        <td>
                            <div class="d-flex align-items-center mb-1">
                                <span class="badge {{ $conteoPermisos == $totalModulos ? 'bg-success' : 'bg-primary' }} bg-opacity-15 text-dark border px-2 py-1 rounded-pill me-2">
                                    <i class="bi bi-key-fill text-primary me-1"></i> {{ $conteoPermisos }} de {{ $totalModulos }} módulos
                                </span>
                                @if($user->rol === 'admin')
                                    <small class="text-success fw-bold"><i class="bi bi-check2-all"></i> Acceso Total</small>
                                @endif
                            </div>

                            <div class="d-flex flex-wrap" style="max-width: 380px;">
                                @if($user->rol === 'admin')
                                    <span class="permiso-chip bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                                        <i class="bi bi-asterisk"></i> Todos los accesos autorizados
                                    </span>
                                @else
                                    @forelse($userPermisos as $pSlug)
                                        @if(isset($catalogoPermisos[$pSlug]))
                                            <span class="permiso-chip bg-light text-dark border">
                                                <i class="bi {{ $catalogoPermisos[$pSlug]['icono'] }} text-primary"></i> 
                                                {{ $catalogoPermisos[$pSlug]['nombre'] }}
                                            </span>
                                        @endif
                                    @empty
                                        <span class="text-danger small fst-italic"><i class="bi bi-exclamation-triangle"></i> Sin permisos asignados</span>
                                    @endforelse
                                @endif
                            </div>
                        </td>

                        <!-- Acciones -->
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3 me-1 fw-bold" 
                                    data-bs-toggle="modal" data-bs-target="#modalEditarUsuario{{ $user->id }}" title="Editar datos y permisos">
                                <i class="bi bi-pencil-square"></i> Editar
                            </button>

                            @if(auth()->id() != $user->id)
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2" 
                                        data-bs-toggle="modal" data-bs-target="#modalEliminarUsuario{{ $user->id }}" title="Eliminar usuario">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @else
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2 disabled" title="Tu sesión actual">
                                    <i class="bi bi-person-check-fill"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 text-muted d-block mb-2"></i>
                            No hay usuarios registrados en el sistema.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODALES DE USUARIOS (COLOCADOS FUERA DE LA TABLA)         -->
<!-- ======================================================== -->
@foreach($usuarios as $user)
    @php
        $userPermisosModal = is_array($user->permisos) ? $user->permisos : (json_decode($user->permisos ?? '[]', true) ?? []);
    @endphp

    <!-- MODAL EDITAR USUARIO -->
    <div class="modal fade" id="modalEditarUsuario{{ $user->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="/usuarios/{{ $user->id }}" method="POST" class="modal-content modal-content-clean">
                @csrf @method('PUT')
                <div class="modal-header bg-info bg-opacity-15 border-0 py-3 px-4">
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 40px; height: 40px; border-radius: 10px; background: rgba(14, 165, 233, 0.2); color: #0284c7;">
                            <i class="bi bi-pencil-fill fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark">Editar Personal y Permisos</h5>
                            <small class="text-muted">Modifica información y accesos de <strong>{{ $user->name }}</strong></small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4 bg-white">
                    <!-- SECCIÓN 1: DATOS PERSONALES -->
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-person-vcard text-primary fs-5 me-2"></i>
                        <h6 class="fw-bold m-0 text-dark">1. Datos Personales y Credenciales</h6>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Nombre Completo *</label>
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold">Carnet de Identidad (CI)</label>
                            <input type="text" name="ci" class="form-control" value="{{ $user->ci }}" placeholder="Ej. 8765432 LP">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold">Número de Celular</label>
                            <input type="text" name="celular" class="form-control" value="{{ $user->celular }}" placeholder="Ej. 76543210">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Usuario de Acceso *</label>
                            <input type="text" name="email" class="form-control" value="{{ $user->email }}" placeholder="Ej. user.123 o usuario@correo.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Nueva Contraseña <span class="fw-normal text-muted">(En blanco para conservar)</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" class="form-control" 
                                   value="{{ $user->fecha_nacimiento ? \Carbon\Carbon::parse($user->fecha_nacimiento)->format('Y-m-d') : '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Fecha de Ingreso</label>
                            <input type="date" name="fecha_ingreso" class="form-control" 
                                   value="{{ $user->fecha_ingreso ? \Carbon\Carbon::parse($user->fecha_ingreso)->format('Y-m-d') : ($user->created_at ? $user->created_at->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Rol en el Sistema *</label>
                            <select name="rol" class="form-select select-rol-modal fw-bold" data-target="editar-permisos-{{ $user->id }}" required>
                                <option value="cajero" {{ $user->rol == 'cajero' ? 'selected' : '' }}>Cajero</option>
                                <option value="admin" {{ $user->rol == 'admin' ? 'selected' : '' }}>Administrador (Total)</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- SECCIÓN 2: GESTIÓN DE PERMISOS -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-shield-lock-fill text-primary fs-5 me-2"></i>
                            <div>
                                <h6 class="fw-bold m-0 text-dark">2. Permisos y Accesos a Módulos</h6>
                                <small class="text-muted">Activa o desactiva con checks a qué partes del sistema podrá ingresar este colaborador</small>
                            </div>
                        </div>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-marcar-todos" data-container="editar-permisos-{{ $user->id }}">
                                <i class="bi bi-check-all"></i> Marcar Todos
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-desmarcar-todos" data-container="editar-permisos-{{ $user->id }}">
                                <i class="bi bi-x"></i> Ninguno
                            </button>
                        </div>
                    </div>

                    <div class="row g-2" id="editar-permisos-{{ $user->id }}">
                        @foreach($catalogoPermisos as $slug => $permiso)
                        @php
                            $tiene = ($user->rol === 'admin') || in_array($slug, $userPermisosModal);
                        @endphp
                        <div class="col-md-6">
                            <label class="permiso-card d-flex align-items-center w-100 m-0">
                                <input class="form-check-input me-3 check-permiso" type="checkbox" name="permisos[]" value="{{ $slug }}" {{ $tiene ? 'checked' : '' }} style="width: 1.25rem; height: 1.25rem;">
                                <div class="d-flex align-items-center">
                                    <div class="p-2 rounded bg-light text-{{ $permiso['color'] }} me-2">
                                        <i class="bi {{ $permiso['icono'] }} fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small" style="line-height: 1.2;">{{ $permiso['nombre'] }}</div>
                                        <small class="text-muted" style="font-size: 0.72rem;">{{ $permiso['descripcion'] }}</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @endforeach
                    </div>

                </div>

                <div class="modal-footer border-0 bg-light p-3 px-4">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info text-white fw-bold px-4 shadow-sm">
                        <i class="bi bi-check-circle me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL ELIMINAR USUARIO -->
    <div class="modal fade" id="modalEliminarUsuario{{ $user->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content modal-content-clean">
                <div class="modal-body p-4 text-center">
                    <i class="bi bi-person-x-fill text-danger mb-3" style="font-size: 3rem;"></i>
                    <h6 class="fw-bold">¿Eliminar Usuario?</h6>
                    <p class="text-muted small mb-4">Se revocará el acceso permanentemente a <strong>{{ $user->name }}</strong> (Usuario: <code>{{ $user->email }}</code>).</p>
                    <form action="/usuarios/{{ $user->id }}" method="POST">
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

<!-- ======================================================== -->
<!-- MODAL REGISTRAR NUEVO USUARIO                            -->
<!-- ======================================================== -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="/usuarios" method="POST" class="modal-content modal-content-clean">
            @csrf
            <div class="modal-header bg-primary bg-opacity-10 border-0 py-3 px-4">
                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 40px; height: 40px; border-radius: 10px; background: rgba(2, 132, 199, 0.2); color: #0284c7;">
                        <i class="bi bi-person-plus-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark">Registrar Nuevo Personal</h5>
                        <small class="text-muted">Ingresa los datos personales y selecciona los módulos permitidos</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4 bg-white">
                <!-- SECCIÓN 1: DATOS PERSONALES -->
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-person-vcard text-primary fs-5 me-2"></i>
                    <h6 class="fw-bold m-0 text-dark">1. Datos Personales y Credenciales</h6>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">Nombre Completo *</label>
                        <input type="text" name="name" class="form-control" placeholder="Ej. Juan Pérez López" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-bold">Carnet de Identidad (CI)</label>
                        <input type="text" name="ci" class="form-control" placeholder="Ej. 6543210 LP">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-bold">Número de Celular</label>
                        <input type="text" name="celular" class="form-control" placeholder="Ej. 70000000">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">Usuario de Acceso *</label>
                        <input type="text" name="email" class="form-control" placeholder="Ej. user.123 o usuario@correo.com" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">Contraseña *</label>
                        <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold">Fecha de Ingreso</label>
                        <input type="date" name="fecha_ingreso" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold">Rol en el Sistema *</label>
                        <select name="rol" class="form-select select-rol-modal fw-bold" data-target="nuevo-permisos-container" required>
                            <option value="cajero" selected>Cajero</option>
                            <option value="admin">Administrador (Total)</option>
                        </select>
                    </div>
                </div>

                <hr class="my-4">

                <!-- SECCIÓN 2: GESTIÓN DE PERMISOS -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-shield-lock-fill text-primary fs-5 me-2"></i>
                        <div>
                            <h6 class="fw-bold m-0 text-dark">2. Permisos y Accesos a Módulos</h6>
                            <small class="text-muted">Marca qué módulos podrá operar este colaborador</small>
                        </div>
                    </div>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-marcar-todos" data-container="nuevo-permisos-container">
                            <i class="bi bi-check-all"></i> Marcar Todos
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-desmarcar-todos" data-container="nuevo-permisos-container">
                            <i class="bi bi-x"></i> Ninguno
                        </button>
                    </div>
                </div>

                <div class="row g-2" id="nuevo-permisos-container">
                    @foreach($catalogoPermisos as $slug => $permiso)
                    @php
                        $defaultCheck = in_array($slug, ['pos', 'ventas', 'clientes']);
                    @endphp
                    <div class="col-md-6">
                        <label class="permiso-card d-flex align-items-center w-100 m-0">
                            <input class="form-check-input me-3 check-permiso" type="checkbox" name="permisos[]" value="{{ $slug }}" {{ $defaultCheck ? 'checked' : '' }} style="width: 1.25rem; height: 1.25rem;">
                            <div class="d-flex align-items-center">
                                <div class="p-2 rounded bg-light text-{{ $permiso['color'] }} me-2">
                                    <i class="bi {{ $permiso['icono'] }} fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark small" style="line-height: 1.2;">{{ $permiso['nombre'] }}</div>
                                    <small class="text-muted" style="font-size: 0.72rem;">{{ $permiso['descripcion'] }}</small>
                                </div>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>

            </div>

            <div class="modal-footer border-0 bg-light p-3 px-4">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">
                    <i class="bi bi-save me-1"></i> Guardar Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Buscador ágil en vivo
    const buscador = document.getElementById('buscadorUsuarios');
    const filas = document.querySelectorAll('.fila-usuario');

    if (buscador) {
        buscador.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            filas.forEach(fila => {
                const text = fila.textContent.toLowerCase();
                fila.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }

    // 2. Control de Marcar / Desmarcar Todos
    document.querySelectorAll('.btn-marcar-todos').forEach(btn => {
        btn.addEventListener('click', function() {
            const containerId = this.getAttribute('data-container');
            const checks = document.querySelectorAll(`#${containerId} .check-permiso`);
            checks.forEach(c => c.checked = true);
        });
    });

    document.querySelectorAll('.btn-desmarcar-todos').forEach(btn => {
        btn.addEventListener('click', function() {
            const containerId = this.getAttribute('data-container');
            const checks = document.querySelectorAll(`#${containerId} .check-permiso`);
            checks.forEach(c => c.checked = false);
        });
    });

    // 3. Si selecciona Administrador, marcar todos los permisos automáticamente
    document.querySelectorAll('.select-rol-modal').forEach(select => {
        select.addEventListener('change', function() {
            const containerId = this.getAttribute('data-target');
            if (this.value === 'admin') {
                const checks = document.querySelectorAll(`#${containerId} .check-permiso`);
                checks.forEach(c => c.checked = true);
            }
        });
    });
});
</script>
@endsection
