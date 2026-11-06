@extends('layouts.app')

@section('content')

<style>
    /* Estilos del Módulo de Cierre de Mes (Cuadre de Stock) */
    .card { border: none !important; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important; border-radius: .75rem !important; }

    /* Barra de Encabezado Institucional estilo Excel */
    .banner-institucional {
        background: linear-gradient(135deg, #a9d08e, #86efac);
        border: 2px solid #65a30d;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 20px;
    }

    /* Cabeceras de la tabla con los colores exactos de la ferretería */
    .th-codigo {
        background-color: #92d050 !important;
        color: #000000 !important;
        font-weight: 800 !important;
        text-align: center;
        letter-spacing: 0.5px;
        border: 1px solid #70a83b !important;
    }
    .th-producto {
        background-color: #ffc000 !important;
        color: #000000 !important;
        font-weight: 800 !important;
        text-align: left;
        letter-spacing: 0.5px;
        border: 1px solid #d99b00 !important;
    }
    .th-cantidad {
        background-color: #ef4444 !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        text-align: center;
        letter-spacing: 0.5px;
        border: 1px solid #dc2626 !important;
    }
    .th-venta {
        background-color: #00b0f0 !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        text-align: center;
        letter-spacing: 0.5px;
        border: 1px solid #0091c7 !important;
    }
    .th-total-tienda {
        background-color: #ed7d31 !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        text-align: center;
        letter-spacing: 0.5px;
        border: 1px solid #c7611e !important;
    }
    .th-check {
        background-color: #00b0f0 !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        text-align: center;
        letter-spacing: 0.5px;
        border: 1px solid #0091c7 !important;
    }

    /* Fila de datos */
    .tabla-cierre tbody tr {
        transition: background-color 0.15s;
        border-bottom: 1px solid #e2e8f0;
    }
    .tabla-cierre tbody tr:hover {
        background-color: #f0fdf4 !important;
    }
    .tabla-cierre td {
        vertical-align: middle;
        padding: 9px 12px;
        font-size: 0.88rem;
    }

    /* Fila verificada (cuadrada) */
    .fila-cuadrada {
        background-color: rgba(220, 252, 231, 0.45);
    }

    /* Botón / Check interactivo "OK" */
    .btn-check-ok {
        border-radius: 8px;
        padding: 6px 16px;
        font-weight: 800;
        font-size: 0.82rem;
        letter-spacing: 1px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid transparent;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        user-select: none;
    }
    /* Estado OK activado (celeste/azul como el excel) */
    .btn-check-ok.is-ok {
        background-color: #00b0f0;
        color: #ffffff;
        border-color: #0284c7;
        box-shadow: 0 2px 6px rgba(0, 176, 240, 0.35);
    }
    .btn-check-ok.is-ok:hover {
        background-color: #0284c7;
        transform: scale(1.05);
    }
    /* Estado pendiente (gris/apagado) */
    .btn-check-ok.is-pending {
        background-color: #f1f5f9;
        color: #94a3b8;
        border-color: #cbd5e1;
    }
    .btn-check-ok.is-pending:hover {
        background-color: #e2e8f0;
        color: #475569;
        border-color: #94a3b8;
    }

    /* Tarjetas de Estadísticas */
    .metric-cierre {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px 20px;
        border: 1px solid #e2e8f0;
    }
    .metric-cierre .label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
    }
    .metric-cierre .value {
        font-size: 1.65rem;
        font-weight: 800;
        color: #0f172a;
        margin-top: 4px;
        line-height: 1;
    }

    /* Barra de progreso de verificación */
    .progress-bar-cierre {
        height: 10px;
        border-radius: 20px;
        background-color: #e2e8f0;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #00b0f0, #16a34a);
        transition: width 0.3s ease;
    }
</style>

{{-- ===== NAVEGACIÓN Y TÍTULO ===== --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div class="bg-success bg-opacity-10 p-2 rounded me-3 text-success">
            <i class="bi bi-calendar2-check-fill fs-3"></i>
        </div>
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:0.8rem;">
                    <li class="breadcrumb-item"><a href="/reportes" class="text-muted text-decoration-none">Reportes</a></li>
                    <li class="breadcrumb-item active">Cierre de Mes</li>
                </ol>
            </nav>
            <h3 class="fw-bold m-0 text-dark">Reporte de Cierre de Mes (Cuadre de Stock)</h3>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="/reportes" class="btn btn-outline-secondary fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Volver a Reportes
        </a>
        {{-- Botón Exportar Excel --}}
        <a href="/reportes/cierre-mes/excel?mes={{ $mesSeleccionado }}" id="btnExportarExcel"
           class="btn fw-bold shadow-sm px-4 text-white"
           style="background-color: #16a34a; border-radius: 8px;">
            <i class="bi bi-file-earmark-excel-fill me-2"></i> Exportar a Excel
        </a>
    </div>
</div>

{{-- ===== BANNER INSTITUCIONAL DEL EXCEL ===== --}}
<div class="banner-institucional">
    <div class="d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold text-dark m-0 tracking-wide" style="letter-spacing: 1px;">
                <i class="bi bi-building me-2"></i> KRONA DISTRIBUIDORA GUADALUPE {{ \Carbon\Carbon::parse($mesSeleccionado . '-01')->format('Y') }}
            </h5>
            <small class="text-dark opacity-75 fw-semibold">
                Período auditado: {{ \Carbon\Carbon::parse($mesSeleccionado . '-01')->translatedFormat('F Y') }}
                ({{ $inicioMes->format('d/m/Y') }} al {{ $finMes->format('d/m/Y') }})
            </small>
        </div>
        <div class="d-flex gap-4 mt-2 mt-md-0 text-dark" style="font-size: 0.84rem;">
            <div><strong>GERENTE:</strong> LUIS ANTENOR CALDERON RODRIGUEZ</div>
            <div><strong>ENCARGADO:</strong> AJAEL</div>
            <div><strong>ENCARGADA INV.:</strong> CIELO MADELEN CALDERON POMA</div>
        </div>
    </div>
</div>

{{-- ===== TARJETAS DE MÉTRICAS Y PROGRESO ===== --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="metric-cierre h-100">
            <div class="label"><i class="bi bi-box-seam me-1 text-primary"></i> Total Productos</div>
            <div class="value">{{ $totalProductos }}</div>
            <small class="text-muted">en catálogo de ferretería</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="metric-cierre h-100">
            <div class="label"><i class="bi bi-cart-check me-1 text-info"></i> Ventas del Mes</div>
            <div class="value text-info">{{ number_format($totalVendidos, 0) }}</div>
            <small class="text-muted">unidades vendidas</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="metric-cierre h-100">
            <div class="label"><i class="bi bi-check2-circle me-1 text-success"></i> Cuadrados (OK)</div>
            <div class="value text-success" id="statVerificados">{{ $verificadosCount }}</div>
            <small class="text-muted"><span id="statPorcentaje">{{ $porcentajeVerificado }}%</span> verificado</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="metric-cierre h-100">
            <div class="label"><i class="bi bi-hourglass-split me-1 text-danger"></i> Pendientes de Revisar</div>
            <div class="value text-danger" id="statPendientes">{{ $pendientesCount }}</div>
            <small class="text-muted">por confirmar stock</small>
        </div>
    </div>
</div>

{{-- BARRA DE PROGRESO DE VERIFICACIÓN --}}
<div class="card p-3 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="fw-bold text-dark small">
            <i class="bi bi-speedometer2 me-1 text-primary"></i> Progreso de Cuadre Físico en Tienda:
            <span id="labelProgresoTexto" class="text-primary">{{ $verificadosCount }} de {{ $totalProductos }} productos ({{ $porcentajeVerificado }}%)</span>
        </span>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-primary fw-bold" onclick="marcarTodos('marcar')">
                <i class="bi bi-check-all me-1"></i> Marcar todos como OK
            </button>
            <button class="btn btn-sm btn-outline-secondary fw-bold" onclick="marcarTodos('desmarcar')">
                <i class="bi bi-x-circle me-1"></i> Desmarcar todos
            </button>
        </div>
    </div>
    <div class="progress-bar-cierre">
        <div class="progress-bar-fill" id="barraProgreso" style="width: {{ $porcentajeVerificado }}%;"></div>
    </div>
</div>

{{-- ===== FILTROS Y BUSCADOR ===== --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-center">
            {{-- Selector de Mes --}}
            <div class="col-md-3">
                <label class="form-label small text-muted fw-bold m-0 mb-1">
                    <i class="bi bi-calendar-month me-1"></i> Mes a Cuadrar
                </label>
                <input type="month" id="selectorMes" class="form-control bg-light"
                       value="{{ $mesSeleccionado }}"
                       onchange="cambiarMes(this.value)">
            </div>

            {{-- Buscador en tiempo real --}}
            <div class="col-md-5">
                <label class="form-label small text-muted fw-bold m-0 mb-1">
                    <i class="bi bi-search me-1"></i> Buscar Producto
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="buscadorProductos" class="form-control border-start-0 bg-light"
                           placeholder="Escribe código o nombre del producto (ej: Codo, Tubo, Cable)...">
                </div>
            </div>

            {{-- Filtro por estado --}}
            <div class="col-md-4">
                <label class="form-label small text-muted fw-bold m-0 mb-1">
                    <i class="bi bi-funnel me-1"></i> Filtrar por Estado
                </label>
                <div class="btn-group w-100" role="group">
                    <button type="button" class="btn btn-sm btn-outline-secondary active" onclick="filtrarEstado('todos', this)">Todos ({{ $totalProductos }})</button>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="filtrarEstado('ok', this)">Solo OK</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="filtrarEstado('pendiente', this)">Pendientes</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== TABLA PRINCIPAL DE CUADRE (FORMATO EXCEL) ===== --}}
<div class="card">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-bold m-0 text-dark">
                <i class="bi bi-table me-2 text-primary"></i> Cuadre de Cantidades Físicas
            </h6>
            <small class="text-muted">
                Fórmula de cuadre: <strong>CANTIDAD</strong> (stock inicial) - <strong>VENTA</strong> (del mes) = <strong>TOTAL EN TIENDA</strong> (stock actual)
            </small>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 fw-bold" id="badgeMostrados">
            Mostrando {{ $totalProductos }} productos
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table tabla-cierre align-middle m-0" id="tablaCierre">
                <thead>
                    <tr>
                        <th class="th-codigo" style="width: 120px;">CODIGO</th>
                        <th class="th-producto">PRODUCTO</th>
                        <th class="th-cantidad" style="width: 130px;" title="Stock anterior o inicial del mes">
                            CANTIDAD
                        </th>
                        <th class="th-venta" style="width: 120px;" title="Ventas realizadas durante este mes">
                            VENTA
                        </th>
                        <th class="th-total-tienda" style="width: 150px;" title="Stock físico actual en tienda">
                            TOTAL EN TIENDA
                        </th>
                        <th class="th-check" style="width: 120px;" title="Clic para marcar como verificado">
                            CHECK
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr class="fila-producto {{ $item['verificado'] ? 'fila-cuadrada' : '' }}"
                        id="fila-{{ $item['id'] }}"
                        data-id="{{ $item['id'] }}"
                        data-codigo="{{ strtolower($item['codigo']) }}"
                        data-nombre="{{ strtolower($item['nombre']) }}"
                        data-estado="{{ $item['verificado'] ? 'ok' : 'pendiente' }}">
                        
                        {{-- CÓDIGO --}}
                        <td class="text-center fw-bold text-dark">
                            <span class="badge bg-light text-dark border px-2 py-1">
                                {{ $item['codigo'] }}
                            </span>
                        </td>

                        {{-- PRODUCTO --}}
                        <td class="fw-semibold text-dark">
                            {{ $item['nombre'] }}
                        </td>

                        {{-- CANTIDAD (Stock anterior / inicial) --}}
                        <td class="text-center fw-bold fs-6" style="color: #b91c1c;">
                            {{ $item['cantidad_anterior'] }}
                        </td>

                        {{-- VENTA (mes) --}}
                        <td class="text-center fw-bold fs-6 text-primary">
                            @if($item['venta'] > 0)
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3 py-1">
                                    {{ $item['venta'] }}
                                </span>
                            @else
                                <span class="text-muted opacity-50">—</span>
                            @endif
                        </td>

                        {{-- TOTAL EN TIENDA (stock actual) --}}
                        <td class="text-center fw-bold fs-6" style="color: #c2410c;">
                            {{ $item['total_tienda'] }}
                        </td>

                        {{-- CHECK / OK INTERACTIVO --}}
                        <td class="text-center">
                            <button type="button"
                                    class="btn-check-ok {{ $item['verificado'] ? 'is-ok' : 'is-pending' }}"
                                    id="btn-check-{{ $item['id'] }}"
                                    onclick="toggleCheck({{ $item['id'] }})"
                                    title="Haz clic para marcar o desmarcar">
                                <i class="bi {{ $item['verificado'] ? 'bi-check-lg' : 'bi-circle' }}"></i>
                                <span class="label-ok">{{ $item['verificado'] ? 'OK' : 'Revisar' }}</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                            <p class="fw-bold mb-1">No hay productos registrados en el inventario</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                {{-- Fila de Totales --}}
                <tfoot>
                    <tr style="background: #f8fafc; border-top: 2px solid #cbd5e1; font-weight: 800;">
                        <td colspan="2" class="ps-4 text-dark text-uppercase" style="font-size:0.85rem;">
                            Totales Consolidados del Mes
                        </td>
                        <td class="text-center fs-6" style="color: #b91c1c;">
                            {{ number_format($totalStockAnterior, 0) }}
                        </td>
                        <td class="text-center fs-6 text-primary">
                            {{ number_format($totalVendidos, 0) }}
                        </td>
                        <td class="text-center fs-6" style="color: #c2410c;">
                            {{ number_format($totalStockTienda, 0) }}
                        </td>
                        <td class="text-center text-success" id="pieVerificados">
                            {{ $verificadosCount }} OK
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{{-- Token CSRF para peticiones AJAX --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- ===== JAVASCRIPT: INTERACTIVIDAD Y GUARDADO EN VIVO ===== --}}
<script>
const MES_ACTUAL = "{{ $mesSeleccionado }}";

// 1. Alternar Check interactivo (AJAX instantáneo)
function toggleCheck(idProducto) {
    const btn = document.getElementById('btn-check-' + idProducto);
    const fila = document.getElementById('fila-' + idProducto);
    const icono = btn.querySelector('i');
    const label = btn.querySelector('.label-ok');

    // Feedback optimista inmediato en la interfaz
    const eraOk = btn.classList.contains('is-ok');
    if (eraOk) {
        btn.classList.remove('is-ok');
        btn.classList.add('is-pending');
        icono.className = 'bi bi-circle';
        label.textContent = 'Revisar';
        fila.classList.remove('fila-cuadrada');
        fila.dataset.estado = 'pendiente';
    } else {
        btn.classList.remove('is-pending');
        btn.classList.add('is-ok');
        icono.className = 'bi bi-check-lg';
        label.textContent = 'OK';
        fila.classList.add('fila-cuadrada');
        fila.dataset.estado = 'ok';
    }

    // Petición AJAX en segundo plano
    fetch('/reportes/cierre-mes/toggle-check', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            id_producto: idProducto,
            mes: MES_ACTUAL
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            actualizarEstadisticas(data.totalChecks, data.pendientes, data.porcentaje);
        }
    })
    .catch(err => {
        console.error('Error al guardar el check:', err);
    });
}

// 2. Marcar todos o desmarcar todos
function marcarTodos(accion) {
    const confirmMsg = accion === 'marcar'
        ? '¿Estás seguro de marcar TODOS los productos como OK?'
        : '¿Estás seguro de desmarcar TODOS los productos?';

    if (!confirm(confirmMsg)) return;

    fetch('/reportes/cierre-mes/check-all', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            mes: MES_ACTUAL,
            accion: accion
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const esMarcar = (accion === 'marcar');
            document.querySelectorAll('.fila-producto').forEach(fila => {
                const btn = fila.querySelector('.btn-check-ok');
                const icono = btn.querySelector('i');
                const label = btn.querySelector('.label-ok');

                if (esMarcar) {
                    btn.classList.remove('is-pending');
                    btn.classList.add('is-ok');
                    icono.className = 'bi bi-check-lg';
                    label.textContent = 'OK';
                    fila.classList.add('fila-cuadrada');
                    fila.dataset.estado = 'ok';
                } else {
                    btn.classList.remove('is-ok');
                    btn.classList.add('is-pending');
                    icono.className = 'bi bi-circle';
                    label.textContent = 'Revisar';
                    fila.classList.remove('fila-cuadrada');
                    fila.dataset.estado = 'pendiente';
                }
            });
            actualizarEstadisticas(data.totalChecks, data.pendientes, data.porcentaje);
        }
    })
    .catch(err => console.error('Error:', err));
}

// 3. Actualizar contadores y barra de progreso
function actualizarEstadisticas(verificados, pendientes, porcentaje) {
    document.getElementById('statVerificados').textContent = verificados;
    document.getElementById('statPendientes').textContent  = pendientes;
    document.getElementById('statPorcentaje').textContent  = porcentaje + '%';
    document.getElementById('barraProgreso').style.width   = porcentaje + '%';
    document.getElementById('pieVerificados').textContent  = verificados + ' OK';

    const total = verificados + pendientes;
    document.getElementById('labelProgresoTexto').textContent = `${verificados} de ${total} productos (${porcentaje}%)`;
}

// 4. Cambiar de mes
function cambiarMes(nuevoMes) {
    window.location.href = '/reportes/cierre-mes?mes=' + nuevoMes;
}

// 5. Buscador en tiempo real
document.getElementById('buscadorProductos').addEventListener('input', function() {
    filtrarTabla();
});

// 6. Filtro por estado (todos, ok, pendientes)
let estadoFiltroActivo = 'todos';
function filtrarEstado(estado, btn) {
    estadoFiltroActivo = estado;
    btn.parentElement.querySelectorAll('.btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    filtrarTabla();
}

function filtrarTabla() {
    const q = document.getElementById('buscadorProductos').value.toLowerCase().trim();
    let visibles = 0;

    document.querySelectorAll('.fila-producto').forEach(fila => {
        const codigo = fila.dataset.codigo || '';
        const nombre = fila.dataset.nombre || '';
        const estado = fila.dataset.estado || '';

        const coincideTexto = !q || codigo.includes(q) || nombre.includes(q);
        const coincideEstado = (estadoFiltroActivo === 'todos') || (estado === estadoFiltroActivo);

        if (coincideTexto && coincideEstado) {
            fila.style.display = '';
            visibles++;
        } else {
            fila.style.display = 'none';
        }
    });

    document.getElementById('badgeMostrados').textContent = `Mostrando ${visibles} productos`;
}
</script>

@endsection
