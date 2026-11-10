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
        html, body { 
            height: 100vh;
            max-height: 100vh;
            overflow: hidden !important;
            margin: 0;
            padding: 0;
            background-color: #1e293b; /* Color base oscuro idéntico al sidebar para evitar cualquier marco blanco */
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        
        /* Estilos del Sidebar oscuro SaaS moderno con scroll independiente */
        .sidebar-custom {
            background-color: #1e293b; 
            width: 250px;
            min-width: 250px;
            height: 100vh;
            max-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            z-index: 1000;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.12);
            flex-shrink: 0;
            position: relative;
        }
        
        .sidebar-header {
            flex-shrink: 0;
            background-color: #1e293b;
        }

        /* Contenedor scrolleable del menú de navegación */
        .sidebar-menu-scroll {
            flex: 1 1 0%;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
        }
        .sidebar-menu-scroll::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-menu-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }
        
        .sidebar-footer {
            flex-shrink: 0;
            background-color: #0f172a;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Contenedor principal a la derecha */
        .main-content {
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden !important;
            background-color: #f1f5f9;
        }
        
        /* Efectos de los enlaces del menú - ESPACIOSOS Y CÓMODOS */
        .nav-link {
            color: rgba(255, 255, 255, 0.75);
            transition: all 0.2s ease-in-out;
            border-radius: 10px;
            margin: 2px 14px;
            padding: 9px 16px;
            font-size: 0.92rem;
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.08);
            transform: translateX(2px);
        }
        .nav-link.active {
            background-color: rgba(13, 202, 240, 0.18);
            color: #38bdf8 !important;
            font-weight: 700;
            border-left: 4px solid #38bdf8;
        }

        /* Botón Cerrar Sesión Sutil, Elegante y Sobrio */
        .btn-logout-sidebar {
            background-color: rgba(255, 255, 255, 0.05);
            color: #94a3b8;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            padding: 8px 12px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.22s ease-in-out;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-logout-sidebar:hover {
            background-color: rgba(239, 68, 68, 0.12);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.28);
            transform: translateY(-1px);
        }
        
        /* Quita los puntos feos de las listas por si acaso */
        ul { list-style: none; padding-left: 0; }

        /* Reglas de impresión limpias para todo el sistema */
        @media print {
            .sidebar-custom, 
            .no-print, 
            .btn, 
            button, 
            nav, 
            header, 
            .alert, 
            .pagination, 
            .btn-group { 
                display: none !important; 
            }
            html, body {
                display: block !important;
                height: auto !important;
                max-height: none !important;
                overflow: visible !important;
                position: static !important;
                background: #ffffff !important;
                color: #000000 !important;
                width: 100% !important;
            }
            .main-content {
                height: auto !important;
                max-height: none !important;
                overflow: visible !important;
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }
        }
    </style>
</head>
<body class="d-flex vh-100">

    <!-- ========================================== -->
    <!-- BARRA LATERAL (SIDEBAR)                    -->
    <!-- ========================================== -->
    <div class="sidebar-custom shadow-lg">
        
        <!-- 1. Header Fijo Superior -->
        <div class="sidebar-header p-3 px-4 d-flex align-items-center border-bottom border-light border-opacity-10">
            <div class="bg-white rounded-3 me-3 d-flex align-items-center justify-content-center p-1 shadow-sm flex-shrink-0" style="width: 42px; height: 42px; overflow: hidden;">
                <img src="{{ asset('img/logo.png') }}?v={{ \App\Models\Configuracion::get('logo_version', 1) }}" alt="Distribuidora Guadalupe" style="max-width: 100%; max-height: 100%; object-fit: contain;">
            </div>
            <div>
                <h5 class="m-0 fw-bold text-white tracking-wide" style="letter-spacing: 0.5px; font-size: 0.98rem;">Sistema DG</h5>
                <small class="text-white-50" style="font-size: 0.72rem;">Distribuidora Guadalupe</small>
            </div>
        </div>

        <!-- 2. Menú de navegación con SCROLL INDEPENDIENTE (Espacioso y Cómodo) -->
        <div class="sidebar-menu-scroll py-2">
            <ul class="nav flex-column w-100">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('inicio') || request()->is('/') ? 'active' : '' }}" href="/inicio">
                        <i class="bi bi-grid-1x2 me-3 fs-5 align-middle"></i> Inicio
                    </a>
                </li>
                @if(auth()->check() && auth()->user()->tienePermiso('pos'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('pos') ? 'active' : '' }}" href="/pos">
                        <i class="bi bi-cart me-3 fs-5 align-middle"></i> Punto de Venta
                    </a>
                </li>
                @endif
                @if(auth()->check() && auth()->user()->tienePermiso('productos'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('productos*') ? 'active' : '' }}" href="/productos">
                        <i class="bi bi-box me-3 fs-5 align-middle"></i> Productos
                    </a>
                </li>
                @endif
                @if(auth()->check() && auth()->user()->tienePermiso('ventas'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('ventas*') ? 'active' : '' }}" href="/ventas">
                        <i class="bi bi-receipt me-3 fs-5 align-middle"></i> Ventas
                    </a>
                </li>
                @endif
                @if(auth()->check() && auth()->user()->tienePermiso('clientes'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('clientes*') ? 'active' : '' }}" href="/clientes">
                        <i class="bi bi-person-lines-fill me-3 fs-5 align-middle"></i> Clientes
                    </a>
                </li>
                @endif
                @if(auth()->check() && auth()->user()->tienePermiso('arqueos'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('arqueos*') ? 'active' : '' }}" href="/arqueos">
                        <i class="bi bi-safe2-fill me-3 fs-5 align-middle"></i> Arqueo de Caja
                    </a>
                </li>
                @endif
                @if(auth()->check() && auth()->user()->tienePermiso('reportes'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('reportes*') ? 'active' : '' }}" href="/reportes">
                        <i class="bi bi-graph-up-arrow me-3 fs-5 align-middle"></i> Reportes
                    </a>
                </li>
                @endif
                @if(auth()->check() && auth()->user()->tienePermiso('inventario'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('inventario*') ? 'active' : '' }}" href="/inventario">
                        <i class="bi bi-diagram-3 me-3 fs-5 align-middle"></i> Inventario
                    </a>
                </li>
                @endif
                
                <!-- BLOQUE PROTEGIDO: ADMINISTRACIÓN -->
                @if(auth()->check() && (auth()->user()->tienePermiso('usuarios') || auth()->user()->tienePermiso('configuracion')))
                <li class="nav-item mt-3 mb-2">
                    <span class="text-white-50 px-4 fw-bold" style="font-size: 0.68rem; letter-spacing: 1.5px;">ADMINISTRACIÓN</span>
                </li>
                @if(auth()->user()->tienePermiso('usuarios'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('usuarios*') ? 'active' : '' }}" href="/usuarios">
                        <i class="bi bi-people me-3 fs-5 align-middle"></i> Usuarios
                    </a>
                </li>
                @endif
                @if(auth()->user()->tienePermiso('configuracion'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('configuracion*') ? 'active' : '' }}" href="/configuracion">
                        <i class="bi bi-gear me-3 fs-5 align-middle"></i> Configuración
                    </a>
                </li>
                @endif
                @endif
            </ul>
        </div>

        <!-- 3. Sección de Usuario y Cerrar Sesión (FIJA EN LA BASE, SIEMPRE VISIBLE) -->
        <div class="sidebar-footer p-3">
            <div class="d-flex align-items-center mb-3 text-white">
                <div class="rounded-circle bg-info bg-opacity-20 d-flex align-items-center justify-content-center me-2.5 flex-shrink-0" style="width: 38px; height: 38px; border: 2px solid rgba(14, 165, 233, 0.45);">
                    <i class="bi bi-person-fill fs-5 text-info"></i>
                </div>
                <div style="line-height: 1.3; overflow: hidden;">
                    <div class="fw-bold text-truncate text-white" style="font-size: 0.88rem;" title="{{ optional(auth()->user())->name ?? 'Invitado' }}">
                        {{ optional(auth()->user())->name ?? 'Invitado' }}
                    </div>
                    <span class="badge {{ (optional(auth()->user())->rol === 'admin') ? 'bg-primary text-white' : 'bg-secondary text-white' }}" style="font-size: 0.65rem; letter-spacing: 0.5px; padding: 2px 8px; font-weight: 600;">
                        <i class="bi {{ (optional(auth()->user())->rol === 'admin') ? 'bi-shield-lock-fill' : 'bi-person-badge' }} me-1"></i>
                        {{ (optional(auth()->user())->rol === 'admin') ? 'Administrador' : 'Cajero' }}
                    </span>
                </div>
            </div>
            
            <form action="/logout" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn-logout-sidebar" title="Finalizar sesión segura">
                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
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
    <div class="main-content flex-grow-1 p-3 p-lg-4">
        @yield('content')
    </div>

    <!-- Scripts de Bootstrap para que funcionen los Modales y Botones -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script Global: Auto-cierre de Notificaciones a los 7 segundos -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const alerts = document.querySelectorAll(".alert");
        alerts.forEach(function(alertEl) {
            // Estilos para desvanecimiento suave
            alertEl.style.transition = "opacity 0.6s ease, transform 0.6s ease, max-height 0.6s ease, margin 0.6s ease, padding 0.6s ease";
            alertEl.style.position = "relative";
            alertEl.style.overflow = "hidden";
            alertEl.style.boxShadow = "0 4px 12px rgba(0,0,0,0.08)";

            // Barra de progreso inferior de 7 segundos
            const progressBar = document.createElement("div");
            progressBar.style.position = "absolute";
            progressBar.style.bottom = "0";
            progressBar.style.left = "0";
            progressBar.style.height = "3px";
            progressBar.style.width = "100%";
            progressBar.style.backgroundColor = "rgba(0, 0, 0, 0.08)";
            progressBar.style.borderRadius = "0 0 6px 6px";

            const progressInner = document.createElement("div");
            progressInner.style.height = "100%";
            progressInner.style.width = "100%";
            progressInner.style.backgroundColor = "rgba(0, 0, 0, 0.25)";
            progressInner.style.transition = "width 7s linear";
            progressBar.appendChild(progressInner);
            alertEl.appendChild(progressBar);

            // Iniciar barra
            setTimeout(function() {
                progressInner.style.width = "0%";
            }, 60);

            // Función para cerrar
            function cerrarAlerta() {
                alertEl.style.opacity = "0";
                alertEl.style.transform = "translateY(-8px)";
                setTimeout(function() {
                    try {
                        const bsAlert = bootstrap.Alert.getOrCreateInstance(alertEl);
                        bsAlert.close();
                    } catch(e) {
                        alertEl.remove();
                    }
                }, 600);
            }

            // Temporizador de 7 segundos
            let timer = setTimeout(cerrarAlerta, 7000);

            // Pausar si el usuario pasa el cursor por encima
            alertEl.addEventListener("mouseenter", function() {
                clearTimeout(timer);
                progressInner.style.transition = "none";
            });

            // Reanudar cuando retira el cursor
            alertEl.addEventListener("mouseleave", function() {
                timer = setTimeout(cerrarAlerta, 2500);
            });
        });
    });

    // Prevenir cualquier desplazamiento involuntario del marco raíz de la ventana
    window.addEventListener('scroll', function() {
        if (window.scrollY !== 0 || window.scrollX !== 0) {
            window.scrollTo(0, 0);
        }
    });
    </script>

</body>
</html>