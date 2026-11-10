<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Distribuidora Guadalupe - Sistema DG</title>
    
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            background-color: #f1f5f9;
            background-image: 
                radial-gradient(at 0% 0%, rgba(22, 163, 74, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(14, 165, 233, 0.12) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(241, 245, 249, 1) 0px, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            padding: 20px 10px;
        }

        .login-card {
            max-width: 960px;
            width: 100%;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(226, 232, 240, 0.8);
            overflow: hidden;
            display: flex;
            min-height: 540px;
            position: relative;
        }

        /* Lado Izquierdo: Formulario */
        .login-form-section {
            padding: 3.5rem 3rem;
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        /* Lado Derecho: Branding Oficial con Logo */
        .login-brand-section {
            width: 50%;
            background: linear-gradient(145deg, #0284c7 0%, #0369a1 45%, #0f172a 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            padding: 3rem 2.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        /* Destellos decorativos de fondo */
        .brand-glow-1 {
            position: absolute;
            top: -60px;
            right: -60px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(56, 189, 248, 0.25);
            filter: blur(40px);
            pointer-events: none;
        }
        .brand-glow-2 {
            position: absolute;
            bottom: -60px;
            left: -60px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(22, 163, 74, 0.2);
            filter: blur(40px);
            pointer-events: none;
        }

        /* Contenedor del Logo */
        .logo-hero-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 18px 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.22), 0 0 0 1px rgba(255, 255, 255, 0.9);
            margin-bottom: 22px;
            max-width: 280px;
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }
        .logo-hero-card:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 24px 45px rgba(0, 0, 0, 0.28);
        }
        .logo-hero-img {
            max-width: 210px;
            max-height: 110px;
            width: auto;
            height: auto;
            object-fit: contain;
            display: block;
        }

        /* Pastillas de características (Punto de venta, stock, reportes) */
        .feature-box {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 12px;
            padding: 10px 14px;
            text-align: center;
            transition: background 0.2s;
            flex: 1;
        }
        .feature-box:hover {
            background: rgba(255, 255, 255, 0.18);
        }

        /* Inputs estilizados */
        .form-floating > .form-control {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            background-color: #f8fafc;
            padding-left: 2.85rem;
            height: calc(3.5rem + 2px);
            font-size: 0.95rem;
            color: #0f172a;
            transition: all 0.2s ease;
        }
        
        .form-floating > .form-control:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 0.25rem rgba(2, 132, 199, 0.15);
            background-color: #ffffff;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.2rem;
            z-index: 4;
            pointer-events: none;
            transition: color 0.2s;
        }
        .form-floating:focus-within .input-icon {
            color: #0284c7;
        }

        .toggle-password-btn {
            position: absolute;
            top: 50%;
            right: 0.85rem;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 1.15rem;
            cursor: pointer;
            z-index: 5;
            padding: 4px 6px;
            border-radius: 6px;
            transition: color 0.2s;
        }
        .toggle-password-btn:hover {
            color: #0f172a;
        }

        /* Botón de Iniciar Sesión */
        .btn-login {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 13px;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.4px;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.2);
            transition: all 0.25s ease;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(2, 132, 199, 0.3);
        }

        /* Insignia de estado */
        .badge-system {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            font-size: 0.73rem;
            font-weight: 700;
            border-radius: 20px;
            padding: 5px 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Responsivo para móviles */
        @media (max-width: 820px) {
            .login-card { 
                flex-direction: column; 
                max-width: 480px; 
            }
            .login-form-section { 
                width: 100%; 
                padding: 2.5rem 2rem; 
            }
            .login-brand-section { 
                width: 100%; 
                padding: 2.5rem 1.5rem; 
                order: -1; 
            }
            .logo-hero-card {
                padding: 14px 20px;
                margin-bottom: 14px;
            }
            .logo-hero-img {
                max-width: 170px;
                max-height: 80px;
            }
        }
    </style>
</head>
<body>

    <div class="container d-flex justify-content-center align-items-center">
        <div class="login-card">
            
            <!-- ============================================== -->
            <!-- SECCIÓN DEL FORMULARIO (Lado Izquierdo)        -->
            <!-- ============================================== -->
            <div class="login-form-section">
                
                {{-- Logo visible en pantallas móviles pequeñas --}}
                <div class="d-md-none text-center mb-3">
                    <img src="{{ asset('img/logo.png') }}?v={{ \App\Models\Configuracion::get('logo_version', 1) }}" alt="Distribuidora Guadalupe" style="max-height: 60px; object-fit: contain;">
                </div>

                <div class="mb-4">
                    <div class="mb-2">
                        <span class="badge-system">
                            <span class="rounded-circle" style="width: 7px; height: 7px; background-color: #16a34a; display: inline-block;"></span>
                            Acceso Autorizado al Sistema
                        </span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.5px;">¡Bienvenido de nuevo!</h3>
                    <p class="text-muted small m-0">Ingresa tus credenciales para acceder al sistema comercial</p>
                </div>

                {{-- Alertas de advertencia / inactividad / sesión --}}
                @if(session('warning'))
                    <div class="alert alert-warning py-2.5 px-3 small fw-bold d-flex align-items-center gap-2 mb-3 border-warning border-opacity-25" style="border-radius: 12px; background-color: #fffbeb; color: #92400e;">
                        <i class="bi bi-clock-history fs-5 text-warning flex-shrink-0"></i>
                        <div>{{ session('warning') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger py-2.5 px-3 small fw-bold d-flex align-items-center gap-2 mb-3" style="border-radius: 12px;">
                        <i class="bi bi-shield-x fs-5 text-danger flex-shrink-0"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info py-2.5 px-3 small fw-bold d-flex align-items-center gap-2 mb-3" style="border-radius: 12px;">
                        <i class="bi bi-info-circle fs-5 text-primary flex-shrink-0"></i>
                        <div>{{ session('info') }}</div>
                    </div>
                @endif

                {{-- Alertas de error de validación --}}
                @if(isset($errors) && $errors->any())
                    <div class="alert alert-danger py-2 px-3 small fw-bold d-flex align-items-center gap-2 mb-3" style="border-radius: 10px;">
                        <i class="bi bi-exclamation-triangle-fill fs-5 text-danger flex-shrink-0"></i>
                        <div>{{ $errors->first() }}</div>
                    </div>
                @endif

                <form action="/login" method="POST">
                    @csrf
                    
                    {{-- Campo Usuario / Correo --}}
                    <div class="form-floating mb-3 position-relative">
                        <i class="bi bi-person-fill input-icon"></i>
                        <input type="text" name="email" class="form-control" id="floatingEmail"
                               placeholder="user.123 o usuario@correo.com" value="{{ old('email') }}" required autofocus autocomplete="username">
                        <label for="floatingEmail" class="ps-5 text-muted">Usuario o Correo de Acceso</label>
                    </div>

                    {{-- Campo Contraseña con botón Ver/Ocultar --}}
                    <div class="form-floating mb-4 position-relative">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input type="password" name="password" class="form-control pe-5" id="floatingPassword"
                               placeholder="Contraseña" required>
                        <label for="floatingPassword" class="ps-5 text-muted">Contraseña</label>
                        <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility()" title="Mostrar/ocultar contraseña">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>

                    {{-- Botón Enviar --}}
                    <button type="submit" class="btn btn-login w-100 d-flex align-items-center justify-content-center gap-2">
                        <span>Iniciar Sesión</span> 
                        <i class="bi bi-arrow-right-circle-fill fs-5"></i>
                    </button>
                </form>

                {{-- Pie sutil del formulario --}}
                <div class="mt-4 pt-3 text-center border-top border-light-subtle">
                    <small class="text-muted" style="font-size: 0.75rem;">
                        Distribuidora Guadalupe &copy; {{ date('Y') }} &bull; Gestión Segura
                    </small>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- SECCIÓN DECORATIVA Y BRANDING (Lado Derecho)   -->
            <!-- ============================================== -->
            <div class="login-brand-section">
                <div class="brand-glow-1"></div>
                <div class="brand-glow-2"></div>

                {{-- Tarjeta Blanca con el Logo Oficial --}}
                <div class="logo-hero-card">
                    <img src="{{ asset('img/logo.png') }}?v={{ \App\Models\Configuracion::get('logo_version', 1) }}" alt="Distribuidora Guadalupe" class="logo-hero-img">
                </div>

                <h4 class="fw-bold text-white mb-1" style="letter-spacing: 0.5px;">Sistema Comercial DG</h4>
                <p class="text-white text-opacity-75 small mb-4" style="max-width: 320px; font-size: 0.85rem;">
                    Control integral de inventario, punto de venta rápido y auditoría de stock mensual.
                </p>
                
                {{-- Badges de Módulos Clave --}}
                <div class="w-100 mt-2" style="max-width: 340px;">
                    <div class="d-flex gap-2">
                        <div class="feature-box">
                            <i class="bi bi-cart-check-fill d-block fs-4 text-info mb-1"></i>
                            <span class="small fw-bold d-block text-white" style="font-size: 0.72rem;">Punto de Venta</span>
                        </div>
                        <div class="feature-box">
                            <i class="bi bi-box-seam-fill d-block fs-4 text-warning mb-1"></i>
                            <span class="small fw-bold d-block text-white" style="font-size: 0.72rem;">Stock en Vivo</span>
                        </div>
                        <div class="feature-box">
                            <i class="bi bi-shield-check d-block fs-4 text-success mb-1"></i>
                            <span class="small fw-bold d-block text-white" style="font-size: 0.72rem;">Auditoría</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Script para Ver / Ocultar Contraseña --}}
    <script>
    function togglePasswordVisibility() {
        const input = document.getElementById('floatingPassword');
        const icon  = document.getElementById('togglePasswordIcon');
        if (!input || !icon) return;

        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'bi bi-eye';
        }
    }
    </script>

</body>
</html>
