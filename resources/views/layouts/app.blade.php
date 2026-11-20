<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ \App\Models\Configuracion::get('nombre_empresa', 'Distribuidora Guadalupe') }} - Sistema DG</title>
    <!-- Favicon Oficial con Cache Buster -->
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}?v={{ \App\Models\Configuracion::get('logo_version', 1) }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/logo.png') }}?v={{ \App\Models\Configuracion::get('logo_version', 1) }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}?v={{ \App\Models\Configuracion::get('logo_version', 1) }}">
    
    <!-- FUENTES EJECUTIVAS SAAS (PLUS JAKARTA SANS) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- LIBRERÍAS DE DISEÑO (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Compilador Vite local -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --sidebar-bg: #0b1a2d;
            --sidebar-surface: #0f233d;
            --sidebar-footer-bg: #071220;
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 82px;
            --sidebar-mobile-width: 285px;
            --mobile-nav-height: 60px;
            --color-primary: #0284c7;
            --color-primary-hover: #0369a1;
            --color-primary-light: #38bdf8;
            --color-primary-subtle: #e0f2fe;
            --bg-body: #f4f7fb;
            --card-radius: 20px;
        }

        html {
            font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: var(--sidebar-bg);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: var(--bg-body);
            font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #0f172a;
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
                background-color: var(--bg-body);
            }

            .navbar-mobile {
                display: none !important;
            }

            .sidebar-backdrop {
                display: none !important;
            }

            .sidebar-custom {
                background: linear-gradient(180deg, #0b1a2d 0%, #071322 100%);
                width: var(--sidebar-width);
                min-width: var(--sidebar-width);
                height: 100vh;
                max-height: 100vh;
                display: flex;
                flex-direction: column;
                overflow: hidden;
                z-index: 1040;
                box-shadow: 6px 0 30px rgba(7, 19, 34, 0.22);
                flex-shrink: 0;
                position: relative;
                transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), min-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            /* MODO COLAPSADO (DESKTOP) */
            body.sidebar-collapsed .sidebar-custom {
                width: var(--sidebar-collapsed-width);
                min-width: var(--sidebar-collapsed-width);
            }
            body.sidebar-collapsed .sidebar-brand-text,
            body.sidebar-collapsed .nav-text-container,
            body.sidebar-collapsed .sidebar-section-title,
            body.sidebar-collapsed .user-info-text,
            body.sidebar-collapsed .btn-logout-text,
            body.sidebar-collapsed .active-indicator-dot {
                display: none !important;
            }
            body.sidebar-collapsed .sidebar-custom .nav-link {
                justify-content: center;
                padding: 10px 8px;
                margin: 4px 8px;
            }
            body.sidebar-collapsed .nav-icon-box {
                margin-right: 0 !important;
            }
            body.sidebar-collapsed .sidebar-header {
                justify-content: center !important;
                padding: 14px 10px !important;
            }
            body.sidebar-collapsed .sidebar-footer {
                padding: 12px 10px !important;
                text-align: center;
            }
            body.sidebar-collapsed .btn-logout-sidebar {
                padding: 8px !important;
                justify-content: center;
            }
            body.sidebar-collapsed .user-profile-box {
                justify-content: center !important;
                margin-bottom: 8px !important;
            }

            .main-content {
                flex: 1 1 auto;
                height: 100vh;
                overflow-y: auto;
                overflow-x: hidden !important;
                background-color: var(--bg-body);
                padding: 1.6rem 2.2rem;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
                background-color: var(--bg-body);
            }

            .btn-sidebar-toggle-desktop {
                display: none !important;
            }

            /* Barra superior móvil */
            .navbar-mobile {
                display: flex !important;
                align-items: center;
                justify-content: space-between;
                height: var(--mobile-nav-height);
                padding: 0 16px;
                background-color: #0b1a2d;
                color: #ffffff;
                position: sticky;
                top: 0;
                z-index: 1030;
                box-shadow: 0 2px 14px rgba(7, 19, 34, 0.25);
                flex-shrink: 0;
            }

            /* Sidebar en modo Drawer Retráctil / Offcanvas */
            .sidebar-custom {
                background: linear-gradient(180deg, #0b1a2d 0%, #071322 100%);
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
                box-shadow: 16px 0 45px rgba(0, 0, 0, 0.55) !important;
            }

            /* Fondo oscuro translúcido */
            .sidebar-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(7, 18, 32, 0.72);
                backdrop-filter: blur(4px);
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
                background-color: var(--bg-body);
                padding: 1rem 0.9rem;
                flex: 1 1 auto;
            }
        }

        /* ============================================================ */
        /* 3. ESTILOS INTERNOS DEL SIDEBAR                              */
        /* ============================================================ */
        .sidebar-header {
            flex-shrink: 0;
            background: transparent;
        }

        .btn-sidebar-toggle-desktop {
            width: 30px;
            height: 30px;
            border-radius: 9px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.75);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-sidebar-toggle-desktop:hover {
            background: rgba(56, 189, 248, 0.2);
            color: #ffffff;
            border-color: rgba(56, 189, 248, 0.4);
            transform: scale(1.05);
        }

        /* Contenedor scrolleable del menú */
        .sidebar-menu-scroll {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.15) transparent;
        }
        .sidebar-menu-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-menu-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
        }

        /* Footer del Sidebar (Fijo y Siempre Visible en la Base) */
        .sidebar-footer {
            flex-shrink: 0;
            background-color: #071220;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            position: sticky;
            bottom: 0;
            z-index: 10;
        }

        /* Enlaces del Menú (Inspirados en Figma SaaS) */
        .sidebar-custom .nav-link {
            color: rgba(255, 255, 255, 0.72);
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 14px;
            margin: 3px 12px;
            padding: 8px 12px;
            font-size: 0.88rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            text-decoration: none;
            position: relative;
        }
        .sidebar-custom .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.07);
            transform: translateX(2px);
        }
        .sidebar-custom .nav-link.active {
            background-color: #e0f2fe !important;
            color: #0369a1 !important;
            font-weight: 700;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.22);
        }
        .sidebar-custom .nav-link.active .nav-icon-box {
            background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%);
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(2, 132, 199, 0.4);
        }
        .sidebar-custom .nav-link.active .nav-title {
            color: #0369a1 !important;
        }
        .sidebar-custom .nav-link.active .nav-subtitle {
            color: #0284c7 !important;
            opacity: 0.9;
        }

        .nav-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            background: rgba(255, 255, 255, 0.06);
            color: #38bdf8;
            transition: all 0.22s ease;
            flex-shrink: 0;
            margin-right: 12px;
        }
        .sidebar-custom .nav-link:hover .nav-icon-box {
            background: rgba(56, 189, 248, 0.18);
            color: #ffffff;
        }

        /* Estilos universales nítidos y legibles para Pestañas (Tabs y Pills) en páginas de contenido */
        .main-content .nav-tabs .nav-link,
        .main-content .nav-pills .nav-link,
        .tab-content-container .nav-tabs .nav-link,
        .tab-content-container .nav-pills .nav-link {
            color: #475569 !important;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .main-content .nav-tabs .nav-link:hover,
        .main-content .nav-pills .nav-link:hover,
        .tab-content-container .nav-tabs .nav-link:hover,
        .tab-content-container .nav-pills .nav-link:hover {
            color: #0284c7 !important;
            background-color: #f0f9ff;
            border-color: #93c5fd;
        }
        .main-content .nav-tabs .nav-link.active,
        .main-content .nav-pills .nav-link.active,
        .tab-content-container .nav-tabs .nav-link.active,
        .tab-content-container .nav-pills .nav-link.active {
            color: #0369a1 !important;
            background-color: #e0f2fe !important;
            border-color: #38bdf8 !important;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.15);
        }

        .nav-title {
            font-size: 0.88rem;
            font-weight: 700;
            line-height: 1.2;
            color: #f1f5f9;
        }
        .nav-subtitle {
            font-size: 0.68rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.45);
            line-height: 1.1;
            margin-top: 2px;
        }

        .active-indicator-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: #0284c7;
            margin-left: auto;
            flex-shrink: 0;
            box-shadow: 0 0 6px rgba(2, 132, 199, 0.6);
        }

        /* Botón Cerrar Sesión Sutil, Elegante y Sobrio */
        .btn-logout-sidebar {
            background-color: rgba(255, 255, 255, 0.05);
            color: #94a3b8;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
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
            background-color: rgba(239, 68, 68, 0.12);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.25);
            transform: translateY(-1px);
        }

        .btn-nav-toggle {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 9px;
            padding: 5px 10px;
            color: #ffffff;
            transition: all 0.2s;
        }
        .btn-nav-toggle:hover {
            background: rgba(255, 255, 255, 0.18);
            color: #ffffff;
        }

        /* Botones globales en Azul Tonal */
        .btn-primary {
            background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%) !important;
            border: none !important;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.28) !important;
            font-weight: 700;
            border-radius: 10px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #0369a1 0%, #0284c7 100%) !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(2, 132, 199, 0.38) !important;
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
        /* CONTENEDOR GLOBAL DE TOASTS / ALERTAS FLOTANTES (ESQUINA SUPERIOR DERECHA) */
        #contenedorAlertasDinamicas {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 105000;
            min-width: 340px;
            max-width: 470px;
            pointer-events: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        #contenedorAlertasDinamicas .alert {
            pointer-events: auto;
            box-shadow: 0 12px 30px -4px rgba(15, 23, 42, 0.16), 0 0 0 1px rgba(0, 0, 0, 0.05) !important;
            border-radius: 14px !important;
            margin-bottom: 0 !important;
            padding: 12px 14px !important;
            position: relative;
            animation: toastNotificationIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Botón de cierre en cuadro visible y sin superposición */
        #contenedorAlertasDinamicas .btn-close-toast {
            position: static !important;
            flex-shrink: 0;
            width: 28px;
            height: 28px;
            padding: 0;
            margin: 0 0 0 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(15, 23, 42, 0.14);
            border-radius: 8px;
            color: #334155;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            line-height: 1;
        }

        #contenedorAlertasDinamicas .btn-close-toast:hover {
            background-color: rgba(15, 23, 42, 0.18);
            color: #0f172a;
            transform: scale(1.08);
        }

        /* Sobrescritura si Bootstrap inyecta .btn-close */
        #contenedorAlertasDinamicas .btn-close {
            position: static !important;
            flex-shrink: 0;
            width: 28px !important;
            height: 28px !important;
            padding: 0 !important;
            margin: 0 0 0 12px !important;
            background-color: rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(15, 23, 42, 0.14) !important;
            border-radius: 8px !important;
            opacity: 0.8;
            background-size: 10px 10px;
            background-position: center;
            transition: all 0.2s ease;
        }

        #contenedorAlertasDinamicas .btn-close:hover {
            opacity: 1;
            background-color: rgba(15, 23, 42, 0.18);
            transform: scale(1.08);
        }

        @keyframes toastNotificationIn {
            from {
                opacity: 0;
                transform: translateY(-10px) translateX(20px) scale(0.96);
            }
            to {
                opacity: 1;
                transform: translateY(0) translateX(0) scale(1);
            }
        }

        @media (max-width: 576px) {
            #contenedorAlertasDinamicas {
                top: 14px;
                right: 14px;
                left: 14px;
                min-width: auto;
                max-width: none;
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
            <div class="bg-white rounded-2 p-1 d-flex align-items-center justify-content-center shadow-sm" style="width: 34px; height: 34px;">
                <img src="{{ asset('img/logo.png') }}?v={{ \App\Models\Configuracion::get('logo_version', 1) }}" alt="Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
            </div>
            <span class="fw-bold text-white small" style="letter-spacing: 0.5px;">Sistema DG</span>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge px-2 py-1 rounded-pill" style="font-size: 0.70rem; background: rgba(56, 189, 248, 0.18); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3);">
                {{ optional(auth()->user())->rol === 'admin' ? 'Admin' : 'Cajero' }}
            </span>
        </div>
    </header>

    <!-- Overlay oscuro para cerrar el menú en móviles -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- ========================================== -->
    <!-- BARRA LATERAL (SIDEBAR INSPIRADO EN FIGMA) -->
    <!-- ========================================== -->
    <aside class="sidebar-custom shadow-lg" id="sidebarMenu">
        
        <!-- 1. Header Superior del Menú con Logo y Toggle -->
        <div class="sidebar-header p-3 px-3.5 d-flex align-items-center justify-content-between border-bottom border-light border-opacity-10">
            <div class="d-flex align-items-center overflow-hidden">
                <div class="bg-white rounded-3 me-3 d-flex align-items-center justify-content-center p-1.5 shadow-sm flex-shrink-0" style="width: 42px; height: 42px; overflow: hidden; border: 2px solid rgba(56, 189, 248, 0.35);">
                    <img src="{{ asset('img/logo.png') }}?v={{ \App\Models\Configuracion::get('logo_version', 1) }}" alt="Distribuidora Guadalupe" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                </div>
                <div class="sidebar-brand-text text-truncate">
                    <h5 class="m-0 fw-extrabold text-white tracking-wide" style="letter-spacing: 0.5px; font-size: 0.98rem; font-weight: 800;">Sistema DG</h5>
                    <small class="text-white-50" style="font-size: 0.70rem; font-weight: 500;">Distribuidora Guadalupe</small>
                </div>
            </div>

            <!-- Botón Minimizar en Desktop -->
            <button type="button" class="btn-sidebar-toggle-desktop d-none d-lg-flex" id="btnSidebarCollapse" title="Alternar menú compacto">
                <i class="bi bi-chevron-left" id="iconSidebarCollapse"></i>
            </button>

            <!-- Botón de Cerrar en Móviles -->
            <button type="button" class="btn btn-sm btn-link text-white-50 p-1 d-lg-none text-decoration-none" id="btnSidebarClose" aria-label="Cerrar Menú">
                <i class="bi bi-x-lg fs-5"></i>
            </button>
        </div>

        <!-- 2. Menú de navegación con SCROLL INDEPENDIENTE -->
        <div class="sidebar-menu-scroll py-2.5">
            <ul class="nav flex-column w-100">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('inicio') || request()->is('/') ? 'active' : '' }}" href="/inicio" title="Inicio">
                        <div class="nav-icon-box">
                            <i class="bi bi-grid-1x2-fill"></i>
                        </div>
                        <div class="nav-text-container">
                            <div class="nav-title">Inicio</div>
                            <div class="nav-subtitle">Métricas del sistema</div>
                        </div>
                        @if(request()->is('inicio') || request()->is('/'))
                            <span class="active-indicator-dot"></span>
                        @endif
                    </a>
                </li>

                @if(auth()->check() && auth()->user()->tienePermiso('pos'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('pos') ? 'active' : '' }}" href="/pos" title="Punto de Venta">
                        <div class="nav-icon-box">
                            <i class="bi bi-cart-check-fill"></i>
                        </div>
                        <div class="nav-text-container">
                            <div class="nav-title">Punto de Venta</div>
                            <div class="nav-subtitle">Caja rápida y notas</div>
                        </div>
                        @if(request()->is('pos'))
                            <span class="active-indicator-dot"></span>
                        @endif
                    </a>
                </li>
                @endif

                @if(auth()->check() && auth()->user()->tienePermiso('productos'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('productos*') ? 'active' : '' }}" href="/productos" title="Productos">
                        <div class="nav-icon-box">
                            <i class="bi bi-box-seam-fill"></i>
                        </div>
                        <div class="nav-text-container">
                            <div class="nav-title">Productos</div>
                            <div class="nav-subtitle">Catálogo y precios</div>
                        </div>
                        @if(request()->is('productos*'))
                            <span class="active-indicator-dot"></span>
                        @endif
                    </a>
                </li>
                @endif

                @if(auth()->check() && auth()->user()->tienePermiso('ventas'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('ventas*') ? 'active' : '' }}" href="/ventas" title="Ventas">
                        <div class="nav-icon-box">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <div class="nav-text-container">
                            <div class="nav-title">Ventas</div>
                            <div class="nav-subtitle">Historial y cobros</div>
                        </div>
                        @if(request()->is('ventas*'))
                            <span class="active-indicator-dot"></span>
                        @endif
                    </a>
                </li>
                @endif

                @if(auth()->check() && auth()->user()->tienePermiso('clientes'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('clientes*') ? 'active' : '' }}" href="/clientes" title="Clientes">
                        <div class="nav-icon-box">
                            <i class="bi bi-person-lines-fill"></i>
                        </div>
                        <div class="nav-text-container">
                            <div class="nav-title">Clientes</div>
                            <div class="nav-subtitle">Cartera y créditos</div>
                        </div>
                        @if(request()->is('clientes*'))
                            <span class="active-indicator-dot"></span>
                        @endif
                    </a>
                </li>
                @endif

                @if(auth()->check() && auth()->user()->tienePermiso('arqueos'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('arqueos*') ? 'active' : '' }}" href="/arqueos" title="Arqueo de Caja">
                        <div class="nav-icon-box">
                            <i class="bi bi-safe2-fill"></i>
                        </div>
                        <div class="nav-text-container">
                            <div class="nav-title">Arqueo de Caja</div>
                            <div class="nav-subtitle">Cierres y turnos</div>
                        </div>
                        @if(request()->is('arqueos*'))
                            <span class="active-indicator-dot"></span>
                        @endif
                    </a>
                </li>
                @endif

                @if(auth()->check() && auth()->user()->tienePermiso('reportes'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('reportes*') ? 'active' : '' }}" href="/reportes" title="Reportes">
                        <div class="nav-icon-box">
                            <i class="bi bi-bar-chart-fill"></i>
                        </div>
                        <div class="nav-text-container">
                            <div class="nav-title">Reportes</div>
                            <div class="nav-subtitle">Analítica avanzada</div>
                        </div>
                        @if(request()->is('reportes*'))
                            <span class="active-indicator-dot"></span>
                        @endif
                    </a>
                </li>
                @endif

                @if(auth()->check() && auth()->user()->tienePermiso('inventario'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('inventario*') ? 'active' : '' }}" href="/inventario" title="Inventario">
                        <div class="nav-icon-box">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                        <div class="nav-text-container">
                            <div class="nav-title">Inventario</div>
                            <div class="nav-subtitle">Kardex y ajustes</div>
                        </div>
                        @if(request()->is('inventario*'))
                            <span class="active-indicator-dot"></span>
                        @endif
                    </a>
                </li>
                @endif
                
                <!-- BLOQUE PROTEGIDO: ADMINISTRACIÓN -->
                @if(auth()->check() && (auth()->user()->tienePermiso('usuarios') || auth()->user()->tienePermiso('configuracion')))
                <li class="nav-item mt-3 mb-2 sidebar-section-title">
                    <span class="text-white-50 px-4 fw-bold" style="font-size: 0.68rem; letter-spacing: 1.5px;">ADMINISTRACIÓN</span>
                </li>

                @if(auth()->user()->tienePermiso('usuarios'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('usuarios*') ? 'active' : '' }}" href="/usuarios" title="Usuarios">
                        <div class="nav-icon-box">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="nav-text-container">
                            <div class="nav-title">Usuarios</div>
                            <div class="nav-subtitle">Roles y permisos</div>
                        </div>
                        @if(request()->is('usuarios*'))
                            <span class="active-indicator-dot"></span>
                        @endif
                    </a>
                </li>
                @endif

                @if(auth()->user()->tienePermiso('configuracion'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('configuracion*') ? 'active' : '' }}" href="/configuracion" title="Configuración">
                        <div class="nav-icon-box">
                            <i class="bi bi-gear-fill"></i>
                        </div>
                        <div class="nav-text-container">
                            <div class="nav-title">Configuración</div>
                            <div class="nav-subtitle">Ajustes y empresa</div>
                        </div>
                        @if(request()->is('configuracion*'))
                            <span class="active-indicator-dot"></span>
                        @endif
                    </a>
                </li>
                @endif
                @endif
            </ul>
        </div>

        <!-- 3. Sección de Usuario y Cerrar Sesión (FIJA EN LA BASE) -->
        <div class="sidebar-footer p-3">
            <div class="user-profile-box d-flex align-items-center mb-2.5 text-white">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-2.5 flex-shrink-0" 
                     style="width: 38px; height: 38px; background: linear-gradient(135deg, #0284c7, #38bdf8); color: #ffffff; font-weight: 800; font-size: 0.85rem; box-shadow: 0 2px 10px rgba(2, 132, 199, 0.4);">
                    {{ strtoupper(substr(optional(auth()->user())->name ?? 'U', 0, 2)) }}
                </div>
                <div class="user-info-text flex-grow-1 overflow-hidden" style="line-height: 1.25;">
                    <div class="fw-bold text-truncate text-white" style="font-size: 0.86rem;" title="{{ optional(auth()->user())->name ?? 'Invitado' }}">
                        {{ optional(auth()->user())->name ?? 'Invitado' }}
                    </div>
                    <span class="badge px-2 py-0.5 rounded-pill fw-semibold" style="font-size: 0.65rem; background: rgba(56, 189, 248, 0.16); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3);">
                        <i class="bi {{ (optional(auth()->user())->rol === 'admin') ? 'bi-shield-lock-fill' : 'bi-person-badge' }} me-1"></i>
                        {{ (optional(auth()->user())->rol === 'admin') ? 'Administrador' : 'Cajero' }}
                    </span>
                </div>
            </div>
            
            <form action="/logout" method="POST" class="m-0" id="formLogoutSidebar">
                @csrf
                <button type="submit" class="btn-logout-sidebar" title="Finalizar sesión segura">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="btn-logout-text ms-2">Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>
    <!-- ========================================== -->
    <!-- FIN BARRA LATERAL                          -->
    <!-- ========================================== -->


    {{-- CONTENEDOR GLOBAL DE NOTIFICACIONES FLOTANTES (Esquina superior derecha - Estilo Toast) --}}
    <div id="contenedorAlertasDinamicas" aria-live="polite" aria-atomic="true">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0" role="alert">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center flex-grow-1 me-2 overflow-hidden">
                        <i class="bi bi-shield-x text-danger fs-4 me-2.5 flex-shrink-0"></i>
                        <div class="flex-grow-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;"><strong>{{ session('error') }}</strong></div>
                    </div>
                    <button type="button" class="btn-close-toast" data-bs-dismiss="alert" aria-label="Cerrar" title="Cerrar"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show border-0" role="alert">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center flex-grow-1 me-2 overflow-hidden">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-4 me-2.5 flex-shrink-0"></i>
                        <div class="flex-grow-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;"><strong>{{ session('warning') }}</strong></div>
                    </div>
                    <button type="button" class="btn-close-toast" data-bs-dismiss="alert" aria-label="Cerrar" title="Cerrar"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0" role="alert">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center flex-grow-1 me-2 overflow-hidden">
                        <i class="bi bi-check-circle-fill text-success fs-4 me-2.5 flex-shrink-0"></i>
                        <div class="flex-grow-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;">
                            <strong>{{ session('success') }}</strong>
                            @if(session('success_venta_id'))
                                <div class="mt-2">
                                    <a href="/ventas/{{ session('success_venta_id') }}/pdf" target="_blank" class="btn btn-sm btn-success fw-bold text-white shadow-sm rounded-pill px-3 py-1" style="font-size: 0.8rem;">
                                        <i class="bi bi-printer-fill me-1"></i> Imprimir Nota #{{ session('success_venta_id') }}
                                    </a>
                                </div>
                            @endif
                            @if(session('anulada_id'))
                                <div class="mt-2">
                                    <a href="/pos?rehacer_venta={{ session('anulada_id') }}" class="btn btn-sm btn-primary text-white fw-bold shadow-sm rounded-pill px-3 py-1" style="font-size: 0.8rem;">
                                        <i class="bi bi-arrow-repeat me-1"></i> Rehacer esta Venta en Caja Ahora
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <button type="button" class="btn-close-toast" data-bs-dismiss="alert" aria-label="Cerrar" title="Cerrar"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show border-0" role="alert">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center flex-grow-1 me-2 overflow-hidden">
                        <i class="bi bi-info-circle-fill text-info fs-4 me-2.5 flex-shrink-0"></i>
                        <div class="flex-grow-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;"><strong>{{ session('info') }}</strong></div>
                    </div>
                    <button type="button" class="btn-close-toast" data-bs-dismiss="alert" aria-label="Cerrar" title="Cerrar"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0" role="alert">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center flex-grow-1 me-2 overflow-hidden">
                        <i class="bi bi-exclamation-octagon-fill text-danger fs-4 me-2.5 flex-shrink-0"></i>
                        <div class="flex-grow-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;"><strong>{{ $errors->first() }}</strong></div>
                    </div>
                    <button type="button" class="btn-close-toast" data-bs-dismiss="alert" aria-label="Cerrar" title="Cerrar"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- CONTENIDO PRINCIPAL                        -->
    <!-- ========================================== -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- ========================================== -->
    <!-- WIDGET FLOTANTE DE INACTIVIDAD EMPRESARIAL -->
    <!-- ========================================== -->
    <div id="inactivityWarningToast" class="card shadow-lg border-warning position-fixed bottom-0 end-0 m-3 p-3 bg-white" style="z-index: 2100; max-width: 360px; display: none; border-radius: 16px; border-width: 2px;">
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
    <!-- SCRIPTS RESPONSIVE, COLLAPSE & SEGURIDAD   -->
    <!-- ========================================== -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // ---------------------------------------------------------------------
        // 1. ALTERNADOR DEL SIDEBAR EN ESCRITORIO (MODO COMPACTO FIGMA)
        // ---------------------------------------------------------------------
        const btnDesktopToggle = document.getElementById("btnSidebarCollapse");
        const iconCollapse = document.getElementById("iconSidebarCollapse");

        function aplicarEstadoSidebarDesktop(colapsado) {
            if (colapsado) {
                document.body.classList.add("sidebar-collapsed");
                if (iconCollapse) {
                    iconCollapse.classList.remove("bi-chevron-left");
                    iconCollapse.classList.add("bi-chevron-right");
                }
            } else {
                document.body.classList.remove("sidebar-collapsed");
                if (iconCollapse) {
                    iconCollapse.classList.remove("bi-chevron-right");
                    iconCollapse.classList.add("bi-chevron-left");
                }
            }
        }

        // Cargar preferencia guardada en localStorage
        const estadoGuardado = localStorage.getItem("sistema_dg_sidebar_collapsed") === "true";
        if (window.innerWidth >= 992 && estadoGuardado) {
            aplicarEstadoSidebarDesktop(true);
        }

        if (btnDesktopToggle) {
            btnDesktopToggle.addEventListener("click", function() {
                const ahoraColapsado = !document.body.classList.contains("sidebar-collapsed");
                aplicarEstadoSidebarDesktop(ahoraColapsado);
                localStorage.setItem("sistema_dg_sidebar_collapsed", ahoraColapsado);
            });
        }

        // ---------------------------------------------------------------------
        // 2. CONTROL RESPONSIVE DEL SIDEBAR (Móviles / Tablets)
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
        // 3. TEMPORIZADOR DE INACTIVIDAD EMPRESARIAL (15 MINUTOS)
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
        // 4. AUTO-CIERRE SUAVE DE NOTIFICACIONES A LOS 5 SEGUNDOS
        // ---------------------------------------------------------------------
        window.configurarAutoCierreAlerta = function(alertEl, duracionMs = 5000) {
            if (!alertEl || alertEl.dataset.autocierreIniciado) return;
            alertEl.dataset.autocierreIniciado = "true";

            alertEl.style.transition = "opacity 0.5s ease, transform 0.5s ease, max-height 0.5s ease, margin 0.5s ease, padding 0.5s ease";
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
            progressInner.style.transition = `width ${duracionMs / 1000}s linear`;
            progressBar.appendChild(progressInner);
            alertEl.appendChild(progressBar);

            setTimeout(function() {
                progressInner.style.width = "0%";
            }, 50);

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
                }, 500);
            }

            let timer = setTimeout(cerrarAlerta, duracionMs);

            alertEl.addEventListener("mouseenter", function() {
                clearTimeout(timer);
                progressInner.style.transition = "none";
            });

            alertEl.addEventListener("mouseleave", function() {
                timer = setTimeout(cerrarAlerta, 2000);
            });
        };

        const toastAlerts = document.querySelectorAll("#contenedorAlertasDinamicas .alert");
        toastAlerts.forEach(function(alertEl) {
            window.configurarAutoCierreAlerta(alertEl, 5000);
        });

        // Función global para mostrar alertas dinámicas desde cualquier script
        window.mostrarAlertaFlotante = function(mensaje, tipo = 'success', duracionMs = 5000) {
            let contenedor = document.getElementById('contenedorAlertasDinamicas');
            if (!contenedor) {
                contenedor = document.createElement('div');
                contenedor.id = 'contenedorAlertasDinamicas';
                document.body.appendChild(contenedor);
            }

            const alertDiv = document.createElement('div');
            const alertClass = tipo === 'danger' ? 'alert-danger' : (tipo === 'warning' ? 'alert-warning' : (tipo === 'info' || tipo === 'loading' ? 'alert-info' : 'alert-success'));
            
            let iconoHtml = '';
            if (tipo === 'loading') {
                iconoHtml = `<div class="spinner-border spinner-border-sm text-primary me-2.5 flex-shrink-0" style="width: 1.2rem; height: 1.2rem;" role="status"></div>`;
            } else {
                const iconClass = tipo === 'danger' ? 'bi-exclamation-octagon-fill text-danger' : (tipo === 'warning' ? 'bi-exclamation-triangle-fill text-warning' : (tipo === 'info' ? 'bi-info-circle-fill text-info' : 'bi-check-circle-fill text-success'));
                iconoHtml = `<i class="bi ${iconClass} fs-4 me-2.5 flex-shrink-0"></i>`;
            }
            
            alertDiv.className = `alert ${alertClass} alert-dismissible fade show border-0 toast-alerta-item`;
            alertDiv.setAttribute('role', 'alert');
            alertDiv.innerHTML = `
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center flex-grow-1 me-2 overflow-hidden">
                        ${iconoHtml}
                        <div class="flex-grow-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;">${mensaje.startsWith('<') ? mensaje : '<strong>' + mensaje + '</strong>'}</div>
                    </div>
                    <button type="button" class="btn-close-toast" data-bs-dismiss="alert" aria-label="Cerrar" title="Cerrar"><i class="bi bi-x-lg"></i></button>
                </div>
            `;
            contenedor.appendChild(alertDiv);
            window.configurarAutoCierreAlerta(alertDiv, duracionMs);
            return alertDiv;
        };
    });
    </script>

</body>
</html>