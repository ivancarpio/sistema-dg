<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Sistema DG - Distribuidora Guadalupe</title>
    
    <!-- LIBRERÍAS DE DISEÑO (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Tu compilador local -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <style>
        :root {
            --sidebar-bg: #1e293b;
            --sidebar-footer-bg: #0f172a;
            --sidebar-width: 250px;
            --sidebar-mobile-width: 280px;
            --mobile-nav-height: 58px;
        }

        html {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #1e293b;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #1e293b;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        /* ============================================================ */
        /* 1. LAYOUT DE ESCRITORIO (>= 992px)                           */
        /* ============================================================ */
        @media (min-width: 992px) {
            body {
                display: flex;
                height: 100vh;
                max-height: 100vh;
                overflow: hidden !important;
            }

            .navbar-mobile {
                display: none !important;
            }

            .sidebar-backdrop {
                display: none !important;
            }

            .sidebar-custom {
                background-color: var(--sidebar-bg);
                width: var(--sidebar-width);
                min-width: var(--sidebar-width);
                height: 100vh;
                max-height: 100vh;
                display: flex;
                flex-direction: column;
                overflow: hidden;
                z-index: 1040;
                box-shadow: 4px 0 24px rgba(0, 0, 0, 0.12);
                flex-shrink: 0;
                position: relative;
            }

            .main-content {
                flex: 1 1 auto;
                height: 100vh;
                overflow-y: auto;
                overflow-x: hidden !important;
                background-color: #f1f5f9;
                padding: 1.5rem 2rem;
            }
        }

        /* ============================================================ */
        /* 2. LAYOUT MÓVIL Y TABLET (< 992px)                          */
        /* ============================================================ */
        @media (max-width: 991.98px) {
            html, body {
                height: auto !important;
                min-height: 100vh;
                min-height: 100dvh;
                overflow-y: auto !important;
                overflow-x: hidden !important;
                display: flex !important;
                flex-direction: column !important;
                background-color: #f1f5f9;
            }

            /* Barra superior móvil */
            .navbar-mobile {
                display: flex !important;
                align-items: center;
                justify-content: space-between;
                height: var(--mobile-nav-height);
                padding: 0 14px;
                background-color: var(--sidebar-bg);
                color: #ffffff;
                position: sticky;
                top: 0;
                z-index: 1030;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
                flex-shrink: 0;
            }

            /* Sidebar en modo Drawer Retráctil / Offcanvas */
            .sidebar-custom {
                background-color: var(--sidebar-bg);
                position: fixed !important;
                top: 0;
                left: 0;
                bottom: 0;
                width: var(--sidebar-mobile-width) !important;
                min-width: var(--sidebar-mobile-width) !important;
                max-width: 85vw;
                height: 100vh !important;
                height: 100dvh !important;
                z-index: 1060 !important;
                display: flex;
                flex-direction: column;
                transform: translateX(-100%);
                transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: none;
            }

            .sidebar-custom.show {
                transform: translateX(0) !important;
                box-shadow: 12px 0 35px rgba(0, 0, 0, 0.5) !important;
            }

            /* Fondo oscuro translúcido */
            .sidebar-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(15, 23, 42, 0.7);
                backdrop-filter: blur(2px);
                z-index: 1050;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.28s ease;
            }

            .sidebar-backdrop.show {
                opacity: 1;
                pointer-events: auto;
            }

            .main-content {
                width: 100% !important;
                max-width: 100vw;
                min-height: calc(100vh - var(--mobile-nav-height));
                min-height: calc(100dvh - var(--mobile-nav-height));
                height: auto !important;
                overflow-y: visible !important;
                overflow-x: hidden !important;
                background-color: #f1f5f9;
                padding: 1rem 0.85rem;
                flex: 1 1 auto;
            }
        }

        /* ============================================================ */
        /* 3. ESTILOS INTERNOS DEL SIDEBAR                              */
        /* ============================================================ */
        .sidebar-header {
            flex-shrink: 0;
            background-color: var(--sidebar-bg);
        }

        /* Contenedor scrolleable del menú */
        .sidebar-menu-scroll {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
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

        /* Footer del Sidebar (Fijo y Siempre Visible en la Base) */
        .sidebar-footer {
            flex-shrink: 0;
            background-color: var(--sidebar-footer-bg);
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            position: sticky;
            bottom: 0;
            z-index: 10;
        }

        /* Enlaces del Menú */
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
            background-color: rgba(255, 255, 255, 0.06);
            color: #94a3b8;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 9px;
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            padding: 8px 12px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-logout-sidebar:hover {
            background-color: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.3);
            transform: translateY(-1px);
        }

        .btn-nav-toggle {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            padding: 4px 10px;
            color: #ffffff;
            transition: all 0.2s;
        }
        .btn-nav-toggle:hover {
            background: rgba(255, 255, 255, 0.16);
            color: #ffffff;
        }

        /* Listas limpias */
        ul { list-style: none; padding-left: 0; }

        /* Reglas de impresión */
        @media print {
            .sidebar-custom, 
            .navbar-mobile,
            .sidebar-backdrop,
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
<body>

    <!-- ========================================== -->
    <!-- BARRA SUPERIOR MÓVIL (NAVBAR MOBILE)       -->
    <!-- ========================================== -->
    <header class="navbar-mobile d-flex d-lg-none">
        <button type="button" class="btn btn-nav-toggle" id="btnSidebarToggle" aria-label="Abrir menú de navegación">
            <i class="bi bi-list fs-3"></i>
        </button>

        <div class="d-flex align-items-center gap-2">
            <div class="bg-white rounded-2 p-1 d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;">
                <img src="{{ asset('img/logo.png') }}?v={{ \App\Models\Configuracion::get('logo_version', 1) }}" alt="Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
            </div>
            <span class="fw-bold text-white small" style="letter-spacing: 0.5px;">Sistema DG</span>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge {{ optional(auth()->user())->rol === 'admin' ? 'bg-primary' : 'bg-secondary' }} px-2 py-1" style="font-size: 0.68rem;">
                {{ optional(auth()->user())->rol === 'admin' ? 'Admin' : 'Cajero' }}
            </span>
        </div>
    </header>

    <!-- Overlay oscuro para cerrar el menú en móviles -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- ========================================== -->
    <!-- BARRA LATERAL (SIDEBAR)                    -->
    <!-- ========================================== -->
    <aside class="sidebar-custom shadow-lg" id="sidebarMenu">
        
        <!-- 1. Header Superior del Menú -->
        <div class="sidebar-header p-3 px-4 d-flex align-items-center justify-content-between border-bottom border-light border-opacity-10">
            <div class="d-flex align-items-center">
                <div class="bg-white rounded-3 me-3 d-flex align-items-center justify-content-center p-1 shadow-sm flex-shrink-0" style="width: 40px; height: 40px; overflow: hidden;">
                    <img src="{{ asset('img/logo.png') }}?v={{ \App\Models\Configuracion::get('logo_version', 1) }}" alt="Distribuidora Guadalupe" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                </div>
                <div>
                    <h5 class="m-0 fw-bold text-white tracking-wide" style="letter-spacing: 0.5px; font-size: 0.96rem;">Sistema DG</h5>
                    <small class="text-white-50" style="font-size: 0.70rem;">Distribuidora Guadalupe</small>
                </div>
            </div>

            <!-- Botón de Cerrar en Móviles -->
            <button type="button" class="btn btn-sm btn-link text-white-50 p-1 d-lg-none text-decoration-none" id="btnSidebarClose" aria-label="Cerrar Menú">
                <i class="bi bi-x-lg fs-5"></i>
            </button>
        </div>

        <!-- 2. Menú de navegación con SCROLL INDEPENDIENTE -->
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
            <div class="d-flex align-items-center mb-2.5 text-white">
                <div class="rounded-circle bg-info bg-opacity-20 d-flex align-items-center justify-content-center me-2.5 flex-shrink-0" style="width: 36px; height: 36px; border: 2px solid rgba(14, 165, 233, 0.45);">
                    <i class="bi bi-person-fill fs-5 text-info"></i>
                </div>
                <div style="line-height: 1.25; overflow: hidden;" class="flex-grow-1 me-1">
                    <div class="fw-bold text-truncate text-white" style="font-size: 0.85rem;" title="{{ optional(auth()->user())->name ?? 'Invitado' }}">
                        {{ optional(auth()->user())->name ?? 'Invitado' }}
                    </div>
                    <span class="badge {{ (optional(auth()->user())->rol === 'admin') ? 'bg-primary text-white' : 'bg-secondary text-white' }}" style="font-size: 0.65rem; letter-spacing: 0.4px; padding: 2px 7px; font-weight: 600;">
                        <i class="bi {{ (optional(auth()->user())->rol === 'admin') ? 'bi-shield-lock-fill' : 'bi-person-badge' }} me-1"></i>
                        {{ (optional(auth()->user())->rol === 'admin') ? 'Administrador' : 'Cajero' }}
                    </span>
                </div>
            </div>
            
            <form action="/logout" method="POST" class="m-0" id="formLogoutSidebar">
                @csrf
                <button type="submit" class="btn-logout-sidebar" title="Finalizar sesión segura">
                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </aside>
    <!-- ========================================== -->
    <!-- FIN BARRA LATERAL                          -->
    <!-- ========================================== -->


    <!-- ========================================== -->
    <!-- CONTENIDO PRINCIPAL                        -->
    <!-- ========================================== -->
    <main class="main-content">
        {{-- Alertas Globales de Sesión y Seguridad --}}
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3 py-2.5 px-3 d-flex align-items-center gap-2 border-danger border-opacity-25" role="alert" style="border-radius: 12px;">
                <i class="bi bi-shield-x fs-4 text-danger flex-shrink-0"></i>
                <div class="flex-grow-1 fw-semibold small">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show shadow-sm mb-3 py-2.5 px-3 d-flex align-items-center gap-2 border-warning border-opacity-25" role="alert" style="border-radius: 12px; background-color: #fffbeb; color: #92400e;">
                <i class="bi bi-exclamation-triangle-fill fs-4 text-warning flex-shrink-0"></i>
                <div class="flex-grow-1 fw-semibold small">{{ session('warning') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3 py-2.5 px-3 d-flex align-items-center gap-2 border-success border-opacity-25" role="alert" style="border-radius: 12px;">
                <i class="bi bi-check-circle-fill fs-4 text-success flex-shrink-0"></i>
                <div class="flex-grow-1 fw-semibold small">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show shadow-sm mb-3 py-2.5 px-3 d-flex align-items-center gap-2 border-info border-opacity-25" role="alert" style="border-radius: 12px;">
                <i class="bi bi-info-circle-fill fs-4 text-info flex-shrink-0"></i>
                <div class="flex-grow-1 fw-semibold small">{{ session('info') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- ========================================== -->
    <!-- WIDGET FLOTANTE DE INACTIVIDAD EMPRESARIAL -->
    <!-- ========================================== -->
    <div id="inactivityWarningToast" class="card shadow-lg border-warning position-fixed bottom-0 end-0 m-3 p-3 bg-white" style="z-index: 2100; max-width: 360px; display: none; border-radius: 14px; border-width: 2px;">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-warning bg-opacity-20 text-warning-emphasis p-2 rounded-circle flex-shrink-0">
                <i class="bi bi-hourglass-split fs-3 text-warning"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.9rem;">Sesión a punto de expirar</h6>
                <p class="text-muted small mb-2" style="font-size: 0.78rem;">
                    Por inactividad, tu sesión se cerrará en <strong class="text-danger" id="inactivityCountdownSec">60</strong> segundos.
                </p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-primary fw-bold px-3 py-1" onclick="resetearInactividadUsuario()">
                        <i class="bi bi-arrow-repeat me-1"></i> Seguir conectado
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-1" onclick="cerrarSesionInmediata()">
                        Cerrar ahora
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts de Bootstrap para Modales y Componentes -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ========================================== -->
    <!-- SCRIPTS RESPONSIVE & SEGURIDAD             -->
    <!-- ========================================== -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // ---------------------------------------------------------------------
        // 1. CONTROL RESPONSIVE DEL SIDEBAR (Móviles / Tablets)
        // ---------------------------------------------------------------------
        const sidebar = document.getElementById("sidebarMenu");
        const backdrop = document.getElementById("sidebarBackdrop");
        const btnToggle = document.getElementById("btnSidebarToggle");
        const btnClose = document.getElementById("btnSidebarClose");

        function abrirSidebar() {
            if (sidebar) sidebar.classList.add("show");
            if (backdrop) backdrop.classList.add("show");
            document.body.style.overflow = "hidden";
        }

        function cerrarSidebar() {
            if (sidebar) sidebar.classList.remove("show");
            if (backdrop) backdrop.classList.remove("show");
            document.body.style.overflow = "";
        }

        if (btnToggle) {
            btnToggle.addEventListener("click", function(e) {
                e.stopPropagation();
                if (sidebar && sidebar.classList.contains("show")) {
                    cerrarSidebar();
                } else {
                    abrirSidebar();
                }
            });
        }

        if (btnClose) {
            btnClose.addEventListener("click", cerrarSidebar);
        }

        if (backdrop) {
            backdrop.addEventListener("click", cerrarSidebar);
        }

        // Cerrar el menú al hacer clic en cualquier enlace dentro del sidebar en pantallas móviles
        const navLinks = document.querySelectorAll(".sidebar-custom .nav-link");
        navLinks.forEach(function(link) {
            link.addEventListener("click", function() {
                if (window.innerWidth < 992) {
                    cerrarSidebar();
                }
            });
        });

        // Cerrar con tecla Escape
        document.addEventListener("keydown", function(e) {
            if (e.key === "Escape" && sidebar && sidebar.classList.contains("show")) {
                cerrarSidebar();
            }
        });

        // ---------------------------------------------------------------------
        // 2. TEMPORIZADOR DE INACTIVIDAD EMPRESARIAL (15 MINUTOS)
        // ---------------------------------------------------------------------
        const TIEMPO_LIMITE_SEGUNDOS = 900; // 15 minutos de inactividad
        const TIEMPO_AVISO_SEGUNDOS   = 60;  // Aviso cuando queden 60s
        let segundosInactivo = 0;
        let toastAvisoVisible = false;
        const toastElement = document.getElementById("inactivityWarningToast");
        const countdownElement = document.getElementById("inactivityCountdownSec");

        function reiniciarContadorActividad() {
            segundosInactivo = 0;
            if (toastAvisoVisible) {
                if (toastElement) toastElement.style.display = "none";
                toastAvisoVisible = false;
            }
        }

        // Detectar interacción humana en pantalla
        const eventosUsuario = ['mousemove', 'mousedown', 'keydown', 'touchstart', 'scroll'];
        let throttleTimer = null;
        eventosUsuario.forEach(function(evento) {
            window.addEventListener(evento, function() {
                if (!throttleTimer) {
                    reiniciarContadorActividad();
                    throttleTimer = setTimeout(function() {
                        throttleTimer = null;
                    }, 1000);
                }
            }, { passive: true });
        });

        // Intervalo de verificación cada segundo
        setInterval(function() {
            segundosInactivo++;

            const segundosRestantes = TIEMPO_LIMITE_SEGUNDOS - segundosInactivo;

            if (segundosRestantes <= TIEMPO_AVISO_SEGUNDOS && segundosRestantes > 0) {
                if (!toastAvisoVisible) {
                    if (toastElement) toastElement.style.display = "block";
                    toastAvisoVisible = true;
                }
                if (countdownElement) {
                    countdownElement.textContent = segundosRestantes;
                }
            }

            // Expiración total: cerrar sesión automáticamente
            if (segundosRestantes <= 0) {
                cerrarSesionInmediata();
            }
        }, 1000);

        window.resetearInactividadUsuario = function() {
            reiniciarContadorActividad();
            // Ping seguro al servidor para extender sesión
            fetch('/ping-session', { credentials: 'same-origin' })
                .catch(function(err) { console.log('Ping status error:', err); });
        };

        window.cerrarSesionInmediata = function() {
            const formLogout = document.getElementById("formLogoutSidebar");
            if (formLogout) {
                formLogout.submit();
            } else {
                window.location.href = "/login";
            }
        };

        // ---------------------------------------------------------------------
        // 3. AUTO-CIERRE SUAVE DE NOTIFICACIONES A LOS 7 SEGUNDOS
        // ---------------------------------------------------------------------
        const alerts = document.querySelectorAll(".alert");
        alerts.forEach(function(alertEl) {
            alertEl.style.transition = "opacity 0.6s ease, transform 0.6s ease, max-height 0.6s ease, margin 0.6s ease, padding 0.6s ease";
            alertEl.style.position = "relative";
            alertEl.style.overflow = "hidden";

            // Barra de progreso inferior
            const progressBar = document.createElement("div");
            progressBar.style.position = "absolute";
            progressBar.style.bottom = "0";
            progressBar.style.left = "0";
            progressBar.style.height = "3px";
            progressBar.style.width = "100%";
            progressBar.style.backgroundColor = "rgba(0, 0, 0, 0.08)";

            const progressInner = document.createElement("div");
            progressInner.style.height = "100%";
            progressInner.style.width = "100%";
            progressInner.style.backgroundColor = "rgba(0, 0, 0, 0.25)";
            progressInner.style.transition = "width 7s linear";
            progressBar.appendChild(progressInner);
            alertEl.appendChild(progressBar);

            setTimeout(function() {
                progressInner.style.width = "0%";
            }, 60);

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

            let timer = setTimeout(cerrarAlerta, 7000);

            alertEl.addEventListener("mouseenter", function() {
                clearTimeout(timer);
                progressInner.style.transition = "none";
            });

            alertEl.addEventListener("mouseleave", function() {
                timer = setTimeout(cerrarAlerta, 2500);
            });
        });
    });
    </script>

</body>
</html>
