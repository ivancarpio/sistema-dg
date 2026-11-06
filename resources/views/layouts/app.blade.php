<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema DG</title>
    
    <!-- LIBRERÍAS DE DISEÑO (CDN) PARA QUE NUNCA FALLE -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Tu compilador local (por si tienes estilos extra) -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <style>
        body { 
            background-color: #f8fafc; 
            overflow: hidden; /* Evita el doble scroll molesto */
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        
        /* Estilos del Sidebar oscuro SaaS */
        .sidebar-custom {
            background-color: #1e293b; 
            width: 250px;
            min-width: 250px;
            transition: all 0.3s;
            z-index: 1000;
        }
        
        /* Contenedor principal a la derecha */
        .main-content {
            height: 100vh;
            overflow-y: auto;
            background-color: #f1f5f9;
        }
        
        /* Efectos de los enlaces del menú */
        .nav-link {
            color: rgba(255, 255, 255, 0.6);
            transition: all 0.2s;
            border-radius: 8px;
            margin: 2px 15px;
            padding: 10px 15px;
            font-size: 0.9rem;
        }
        .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.05);
        }
        .nav-link.active {
            background-color: rgba(13, 202, 240, 0.15);
            color: #0dcaf0 !important;
            font-weight: bold;
            border-left: 4px solid #0dcaf0;
        }
        
        /* Quita los puntos feos de las listas por si acaso */
        ul { list-style: none; padding-left: 0; }
    </style>
</head>
<body class="d-flex vh-100">

    <!-- ========================================== -->
    <!-- BARRA LATERAL (SIDEBAR)                    -->
    <!-- ========================================== -->
    <div class="sidebar-custom d-flex flex-column shadow-lg">
        
        <!-- Logo superior -->
        <div class="p-4 d-flex align-items-center mb-2">
            <div class="bg-info bg-opacity-25 p-2 rounded text-info me-3">
                <i class="bi bi-box-seam-fill fs-4"></i>
            </div>
            <h5 class="m-0 fw-bold text-white tracking-wide">Sistema DG</h5>
        </div>

        <!-- Enlaces de navegación -->
        <ul class="nav flex-column mb-auto w-100">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('inicio') ? 'active' : '' }}" href="/inicio">
                    <i class="bi bi-grid-1x2 me-3 fs-5 align-middle"></i> Inicio
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('pos') ? 'active' : '' }}" href="/pos">
                    <i class="bi bi-cart me-3 fs-5 align-middle"></i> Punto de Venta
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('productos') ? 'active' : '' }}" href="/productos">
                    <i class="bi bi-box me-3 fs-5 align-middle"></i> Productos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('ventas') ? 'active' : '' }}" href="/ventas">
                    <i class="bi bi-currency-dollar me-3 fs-5 align-middle"></i> Ventas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('arqueos*') ? 'active' : '' }}" href="/arqueos">
                    <i class="bi bi-safe2-fill me-3 fs-5 align-middle"></i> Arqueo de Caja
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('reportes*') ? 'active' : '' }}" href="/reportes">
                    <i class="bi bi-graph-up-arrow me-3 fs-5 align-middle"></i> Reportes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('inventario') ? 'active' : '' }}" href="/inventario">
                    <i class="bi bi-diagram-3 me-3 fs-5 align-middle"></i> Inventario
                </a>
            </li>
            
            <!-- BLOQUE PROTEGIDO: SOLO ADMINISTRADORES -->
            @if(auth()->check() && auth()->user()->rol === 'admin')
            <li class="nav-item mt-4 mb-2">
                <span class="text-white-50 px-4 fw-bold" style="font-size: 0.65rem; letter-spacing: 1.5px;">ADMINISTRACIÓN</span>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('usuarios') ? 'active' : '' }}" href="/usuarios">
                    <i class="bi bi-people me-3 fs-5 align-middle"></i> Usuarios
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('configuracion') ? 'active' : '' }}" href="/configuracion">
                    <i class="bi bi-gear me-3 fs-5 align-middle"></i> Configuración
                </a>
            </li>
            @endif
        </ul>

        <!-- Sección de Usuario y Cerrar Sesión -->
        <div class="mt-auto p-4 border-top border-light border-opacity-10" style="background-color: rgba(0,0,0,0.1);">
            <div class="d-flex align-items-center mb-3 text-white">
                <i class="bi bi-person-circle fs-2 me-3 text-info"></i>
                <div style="line-height: 1.2;">
                    <div class="fw-bold" style="font-size: 0.9rem;">{{ optional(auth()->user())->name ?? 'Invitado' }}</div>
                    <div class="text-info fw-bold" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px;">
                        {{ optional(auth()->user())->rol ?? 'Sin rol' }}
                    </div>
                </div>
            </div>
            
            <form action="/logout" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-danger bg-opacity-10 text-danger w-100 fw-bold border-0 text-start px-3 py-2" style="border-radius: 8px;">
                    <i class="bi bi-box-arrow-right me-2 fs-5 align-middle"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
    <!-- ========================================== -->
    <!-- FIN BARRA LATERAL                          -->
    <!-- ========================================== -->


    <!-- ========================================== -->
    <!-- CONTENIDO PRINCIPAL                        -->
    <!-- ========================================== -->
    <div class="main-content flex-grow-1 p-5">
        @yield('content')
    </div>

    <!-- Scripts de Bootstrap para que funcionen los Modales y Botones -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>