<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Sistema DG</title>
    
    <!-- Bootstrap 5 & Icons (CDN para garantizar que siempre cargue el diseño) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            background-color: #f4f7f6;
            background-image: radial-gradient(circle at 10% 20%, rgb(239, 246, 249) 0%, rgb(206, 239, 253) 90%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .login-container {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            display: flex;
            min-height: 500px;
        }

        /* Lado Izquierdo: Formulario */
        .login-form-section {
            padding: 3rem;
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Lado Derecho: Decorativo */
        .login-image-section {
            width: 50%;
            background: linear-gradient(135deg, #0dcaf0 0%, #0077b6 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 3rem;
            text-align: center;
        }

        /* Estilos de inputs flotantes (Moderno) */
        .form-floating > .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            background-color: #f8fafc;
            padding-left: 3rem; /* Espacio para el ícono */
        }
        
        .form-floating > .form-control:focus {
            border-color: #0dcaf0;
            box-shadow: 0 0 0 0.25rem rgba(13, 202, 240, 0.15);
            background-color: #ffffff;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 1.2rem;
            z-index: 4;
        }

        .btn-login {
            background-color: #1e293b;
            color: white;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background-color: #0f172a;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(15, 23, 42, 0.2);
        }

        /* Responsivo para celulares */
        @media (max-width: 768px) {
            .login-container { flex-direction: column; width: 90%; margin: 20px auto; }
            .login-form-section { width: 100%; padding: 2rem; }
            .login-image-section { display: none; /* Ocultamos la imagen en celular */ }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="login-container shadow-lg">
            
            <!-- SECCIÓN DEL FORMULARIO -->
            <div class="login-form-section">
                <div class="mb-4">
                    <h3 class="fw-bold text-dark mb-1">¡Bienvenido de nuevo!</h3>
                    <p class="text-muted small">Ingresa tus credenciales para acceder al sistema</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger py-2 small fw-bold" style="border-radius: 10px;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $errors->first() }}
                    </div>
                @endif

                <form action="/login" method="POST">
                    @csrf
                    
                    <div class="form-floating mb-3 position-relative">
                        <i class="bi bi-envelope-at input-icon"></i>
                        <input type="email" name="email" class="form-control" id="floatingEmail" placeholder="correo@ejemplo.com" required autofocus>
                        <label for="floatingEmail" class="ps-5 text-muted">Usuario</label>
                    </div>

                    <div class="form-floating mb-4 position-relative">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Contraseña" required>
                        <label for="floatingPassword" class="ps-5 text-muted">Contraseña</label>
                    </div>

                    <button type="submit" class="btn btn-login w-100 d-flex align-items-center justify-content-center">
                        <span class="me-2">Iniciar Sesión</span> 
                        <i class="bi bi-arrow-right-circle"></i>
                    </button>
                </form>
            </div>

            <!-- SECCIÓN DECORATIVA (Lado Derecho) -->
            <div class="login-image-section">
                <i class="bi bi-box-seam-fill mb-3" style="font-size: 4rem; text-shadow: 0 5px 15px rgba(0,0,0,0.2);"></i>
                <h2 class="fw-bold mb-2">Sistema DG</h2>
                <p class="opacity-75 small">Gestión Inteligente de Inventario y Punto de Venta</p>
                
                <div class="mt-5 p-3 rounded" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <div class="text-center">
                            <i class="bi bi-graph-up-arrow d-block fs-4"></i>
                            <small style="font-size: 0.7rem;">Ventas</small>
                        </div>
                        <div class="text-center">
                            <i class="bi bi-boxes d-block fs-4"></i>
                            <small style="font-size: 0.7rem;">Stock</small>
                        </div>
                        <div class="text-center">
                            <i class="bi bi-file-earmark-pdf d-block fs-4"></i>
                            <small style="font-size: 0.7rem;">Reportes</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>