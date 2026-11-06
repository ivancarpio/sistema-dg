@extends('layouts.app')

@section('content')
<!-- Importamos Chart.js para los gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold m-0"><i class="bi bi-speedometer2 text-primary me-2"></i> Dashboard de Ventas</h3>
        <small class="text-muted">Resumen general y métricas de rendimiento (KPIs)</small>
    </div>
</div>

<!-- FILA 1: KPIs (Tarjetas Superiores) -->
<div class="row mb-4">
    <!-- Dato Real -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-danger border-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-danger fw-bold mb-1">VENTAS DEL MES</h6>
                    <h3 class="fw-bold m-0">Bs. {{ number_format($ventasMes, 2) }}</h3>
                </div>
                <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger">
                    <i class="bi bi-cart3 fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Dato Simulado (Margen $) -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-success border-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-success fw-bold mb-1">MARGEN ESTIMADO $</h6>
                    <h3 class="fw-bold m-0">Bs. {{ number_format($ventasMes * 0.35, 2) }}</h3>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                    <i class="bi bi-cash-stack fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Dato Real y Simulado -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-primary border-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-primary fw-bold mb-1">CATÁLOGO / VENTAS HOY</h6>
                    <h3 class="fw-bold m-0">{{ $totalProductos }} <small class="text-muted fs-6">Prod.</small> / Bs. {{ number_format($ventasHoy, 2) }}</h3>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                    <i class="bi bi-graph-up-arrow fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FILA 2: Gráficos Principales -->
<div class="row mb-4">
    <!-- Gráfico 1: Ventas Mensuales (Barras Verticales) -->
    <div class="col-md-5 mb-4 mb-md-0">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold m-0">Ventas Mensuales (Bs.)</h6>
            </div>
            <div class="card-body">
                <canvas id="chartMensual" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Gráfico 2: Ventas por Producto (Barras Horizontales) -->
    <div class="col-md-4 mb-4 mb-md-0">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold m-0">Top Ventas por Producto</h6>
            </div>
            <div class="card-body">
                <canvas id="chartProductos" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Gráfico 3: Medios de Pago (Dona) -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold m-0">Medios de Pago</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartPagos" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- FILA 3: Gráficos Inferiores -->
<div class="row mb-4">
    <!-- Gráfico 4: Ventas Diarias (Área) -->
    <div class="col-md-8 mb-4 mb-md-0">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold m-0">Flujo de Ventas Diarias (Últimos 15 días)</h6>
            </div>
            <div class="card-body">
                <canvas id="chartDiario" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Gráfico 5: Canales de Venta (Pastel) -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold m-0">Canales de Venta</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartCanales" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- CONFIGURACIÓN DE LOS GRÁFICOS (CHART.JS) 📊  -->
<!-- ============================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Configuraciones comunes para que se vea limpio como en Excel
    Chart.defaults.font.family = "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
    Chart.defaults.color = '#858796';
    const gridOptions = { color: '#f8f9fa', drawBorder: false };

    // 1. Ventas Mensuales (Barras Rojas)
    new Chart(document.getElementById('chartMensual'), {
        type: 'bar',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            datasets: [{
                label: 'Ventas Bs.',
                data: [5000, 15000, 20000, 15000, 11000, 30000, 23000, 4000, 10000, 4000, 2000, 3000], // Datos simulados
                backgroundColor: '#ff4d4d', // Rojo coral como en la imagen
                borderRadius: 2
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { grid: { display: false } }, y: { grid: gridOptions } }
        }
    });

    // 2. Ventas por Producto (Barras Horizontales Verdes)
    new Chart(document.getElementById('chartProductos'), {
        type: 'bar',
        data: {
            labels: ['Producto 1', 'Producto 2', 'Producto 3', 'Producto 4', 'Producto 5'],
            datasets: [{
                label: 'Ingresos Bs.',
                data: [6000, 40200, 52400, 35000, 4200],
                backgroundColor: '#4ecdc4', // Verde agua
                borderRadius: 2
            }]
        },
        options: {
            indexAxis: 'y', // Esto voltea el gráfico a horizontal
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { grid: gridOptions }, y: { grid: { display: false } } }
        }
    });

    // 3. Medios de Pago (Gráfico de Dona)
    new Chart(document.getElementById('chartPagos'), {
        type: 'doughnut',
        data: {
            labels: ['Débito', 'Mercado Pago', 'Tarjeta', 'Transferencia', 'Efectivo'],
            datasets: [{
                data: [6, 24, 57, 4, 8],
                backgroundColor: ['#4ecdc4', '#ffe66d', '#1a535c', '#ff6b6b', '#f7fff7'],
                borderWidth: 2
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutout: '70%', // Qué tan delgada es la dona
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 15 } } }
        }
    });

    // 4. Ventas Diarias (Gráfico de Área Verde Claro)
    new Chart(document.getElementById('chartDiario'), {
        type: 'line',
        data: {
            labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15'],
            datasets: [{
                label: 'Ventas Diarias',
                data: [55000, 20000, 5000, 2000, 2000, 2000, 8000, 15000, 22000, 31000, 15000, 2000, 2000, 2000, 3000],
                borderColor: '#4ecdc4',
                backgroundColor: 'rgba(78, 205, 196, 0.2)', // Verde transparente para el relleno
                fill: true,
                tension: 0.3, // Hace que la línea sea curva y suave
                pointRadius: 0 // Oculta los puntitos
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { grid: { display: false } }, y: { grid: gridOptions } }
        }
    });

    // 5. Canales de Venta (Pastel)
    new Chart(document.getElementById('chartCanales'), {
        type: 'pie',
        data: {
            labels: ['Tienda Física', 'Online / WhatsApp'],
            datasets: [{
                data: [74, 26],
                backgroundColor: ['#4ecdc4', '#ff6b6b'],
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
});
</script>
@endsection