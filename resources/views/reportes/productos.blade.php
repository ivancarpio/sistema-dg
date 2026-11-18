@extends('layouts.app')

@section('content')

<style>
/* ===== ESTILOS REPORTE DE PRODUCTOS E INVENTARIO ===== */
.card { border: none !important; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important; border-radius: .75rem !important; }

/* Tarjetas métricas superiores */
.metric-card-prod {
    border-radius: 14px !important;
    padding: 18px 20px;
    border: none !important;
    transition: transform 0.15s;
    background: #ffffff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
}
.metric-card-prod:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.08) !important; }
.metric-icon-box {
    width: 48px; height: 48px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; flex-shrink: 0;
}
.metric-label-prod { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; }
.metric-value-prod { font-size: 1.6rem; font-weight: 800; line-height: 1.1; margin-top: 4px; color: #0f172a; }
.metric-sub-prod   { font-size: 0.75rem; margin-top: 4px; color: #94a3b8; }

/* Tabla */
.table-custom-header th {
    background-color: #f8fafc !important;
    font-size: 0.72rem; font-weight: 800; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.5px;
    padding: 12px 10px; border-bottom: 1px solid #e2e8f0 !important;
}
.table-custom-header th.sortable { cursor: pointer; user-select: none; }
.table-custom-header th.sortable:hover { background-color: #f1f5f9 !important; color: #0f172a; }
.table-custom tbody tr { border-bottom: 1px solid #f1f5f9; transition: background-color 0.15s; }
.table-custom tbody tr:hover { background-color: #f8faff; }
.table-custom td { vertical-align: middle; padding: 11px 10px; font-size: 0.88rem; }

.code-pill {
    font-family: monospace; font-size: 0.78rem; font-weight: 700;
    background: #f1f5f9; color: #334155; padding: 3px 8px; border-radius: 6px;
    border: 1px solid #e2e8f0;
}
</style>

{{-- ENCABEZADO PRINCIPAL --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div class="d-flex align-items-center gap-3">
        <a href="/reportes" class="btn btn-light border p-2 rounded-3 text-secondary" title="Volver a Reportes">
            <i class="bi bi-arrow-left fs-5"></i>
        </a>
        <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary">
            <i class="bi bi-boxes fs-3"></i>
        </div>
        <div>
            <h3 class="fw-bold m-0 text-dark">Reporte General de Productos y Stock Físico</h3>
            <small class="text-muted">Inventario total, desglose de precios (Venta, Costo, Cliente), marcas y unidades</small>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reportes.productos.excel', request()->query()) }}" class="btn btn-success fw-bold px-4 py-2 shadow-sm rounded-3 d-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-excel-fill fs-5"></i>
            <span>Exportar a Excel</span>
        </a>
    </div>
</div>

{{-- TARJETAS DE MÉTRICAS RÁPIDAS --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="metric-card-prod d-flex align-items-center gap-3 h-100">
            <div class="metric-icon-box" style="background: rgba(2, 132, 199, 0.12); color: #0284c7;">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                <div class="metric-label-prod">Total Catálogo</div>
                <div class="metric-value-prod">{{ number_format($totalProductos, 0) }}</div>
                <div class="metric-sub-prod">artículos registrados</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="metric-card-prod d-flex align-items-center gap-3 h-100">
            <div class="metric-icon-box" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                <i class="bi bi-building-check"></i>
            </div>
            <div>
                <div class="metric-label-prod">Stock en Tienda</div>
                <div class="metric-value-prod text-success">{{ number_format($totalStock, 0) }}</div>
                <div class="metric-sub-prod">unidades físicas reales</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="metric-card-prod d-flex align-items-center gap-3 h-100">
            <div class="metric-icon-box" style="background: rgba(99, 102, 241, 0.12); color: #6366f1;">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div>
                <div class="metric-label-prod">Valor en Tienda</div>
                <div class="metric-value-prod" style="color: #4f46e5;">Bs. {{ number_format($totalValor, 2) }}</div>
                <div class="metric-sub-prod">según precio venta</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="metric-card-prod d-flex align-items-center gap-3 h-100">
            <div class="metric-icon-box" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div>
                <div class="metric-label-prod">Stock Crítico</div>
                <div class="metric-value-prod text-danger">{{ $totalCriticos }}</div>
                <div class="metric-sub-prod">{{ $totalAgotados }} agotados (0 stock)</div>
            </div>
        </div>
    </div>
</div>

{{-- PANEL DE FILTROS --}}
<div class="card p-3 mb-4 shadow-sm bg-white border-0">
    <form action="/reportes/productos" method="GET" class="row g-2 align-items-end">
        <!-- Buscador -->
        <div class="col-md-3">
            <label class="form-label small fw-bold text-muted mb-1">Buscar Producto o Código</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="buscar" id="buscarInput" class="form-control border-start-0 bg-light" 
                       placeholder="Ej. Codo, Cable, Tubo..." value="{{ request('buscar') }}">
            </div>
        </div>

        <!-- Categoría -->
        <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-1">Categoría</label>
            <select name="categoria" class="form-select bg-light">
                <option value="">Todas las categorías</option>
                @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}" {{ request('categoria') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Marca -->
        <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-1">Marca</label>
            <select name="marca" class="form-select bg-light">
                <option value="">Todas las marcas</option>
                @foreach($marcas as $m)
                    <option value="{{ $m->id }}" {{ request('marca') == $m->id ? 'selected' : '' }}>
                        {{ $m->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Estado Stock -->
        <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-1">Estado de Stock</label>
            <select name="estado_stock" class="form-select bg-light">
                <option value="">Todos los stocks</option>
                <option value="con_stock" {{ request('estado_stock') == 'con_stock' ? 'selected' : '' }}>🟢 Con Stock (>0)</option>
                <option value="optimo" {{ request('estado_stock') == 'optimo' ? 'selected' : '' }}>🟢 Stock Óptimo</option>
                <option value="alerta" {{ request('estado_stock') == 'alerta' ? 'selected' : '' }}>🟡 Por Agotarse</option>
                <option value="critico" {{ request('estado_stock') == 'critico' ? 'selected' : '' }}>🔴 Críticos (≤ Mín)</option>
                <option value="agotado" {{ request('estado_stock') == 'agotado' ? 'selected' : '' }}>⚫ Agotados (0)</option>
            </select>
        </div>

        <!-- Botones de Acción -->
        <div class="col-md-3 d-flex gap-1">
            <button type="submit" class="btn btn-primary text-white flex-grow-1 fw-bold shadow-sm d-flex align-items-center justify-content-center" style="height: 38px;">
                <i class="bi bi-funnel-fill me-1"></i> Filtrar
            </button>
            <a href="/reportes/productos" class="btn btn-light border text-muted d-flex align-items-center justify-content-center px-3" title="Limpiar filtros" style="height: 38px;">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>
</div>

{{-- TABLA DE RESULTADOS --}}
<div class="card overflow-hidden shadow-sm border-0 mb-4 bg-white">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
        <div>
            <h6 class="fw-bold m-0 text-dark">
                <i class="bi bi-list-check me-2 text-primary"></i>Detalle de Artículos en Inventario
                <span class="badge bg-primary ms-1" id="badgeTotal">{{ $productos->count() }}</span>
            </h6>
            <small class="text-muted">Ordena por cualquier columna haciendo clic en su título</small>
        </div>
        <div class="w-25">
            <input type="text" id="filtroRapido" class="form-control form-control-sm bg-light" placeholder="Filtro rápido en pantalla...">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom align-middle m-0" id="tablaReporteProductos">
            <thead class="table-custom-header">
                <tr>
                    <th class="ps-3 text-center sortable" style="width: 4%;" data-col="0">N°</th>
                    <th class="text-center sortable" style="width: 10%;" data-col="1">
                        CÓDIGO <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i>
                    </th>
                    <th class="text-start sortable" style="width: 23%;" data-col="2">
                        PRODUCTO <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i>
                    </th>
                    <th class="text-center sortable" style="width: 10%;" data-col="3">
                        CATEGORÍA <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i>
                    </th>
                    <th class="text-center sortable" style="width: 9%;" data-col="4">
                        MARCA <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i>
                    </th>
                    <th class="text-center sortable" style="width: 7%;" data-col="5">
                        UNIDAD <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i>
                    </th>
                    <th class="text-end sortable" style="width: 7%;" data-col="6">
                        COSTO <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i>
                    </th>
                    <th class="text-end sortable" style="width: 9%;" data-col="7">
                        P. VENTA <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i>
                    </th>
                    <th class="text-end sortable" style="width: 9%;" data-col="8">
                        P. CLIENTE <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i>
                    </th>
                    <th class="text-center sortable" style="width: 8%;" data-col="9">
                        STOCK <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i>
                    </th>
                    <th class="text-end sortable pe-3" style="width: 10%;" data-col="10">
                        VALOR TOTAL <i class="icono-orden bi bi-arrow-down-up ms-1" style="font-size: 0.8em; opacity: 0.5;"></i>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $idx => $p)
                @php
                    $stk = (int)($p->stock ?? 0);
                    $pv  = (float)($p->precio_venta ?? 0);
                    $pc  = $p->precio_cliente !== null ? (float)$p->precio_cliente : null;
                    $costo = (float)($p->costo ?? 0);
                    $val = $stk * $pv;

                    $min = is_numeric($p->stock_minimo) && $p->stock_minimo > 0 ? (int)$p->stock_minimo : 5;
                    if ($stk <= 0) {
                        $badgeClass = 'bg-danger text-white';
                        $lblEstado = 'Agotado';
                    } elseif ($stk <= $min) {
                        $badgeClass = 'bg-danger bg-opacity-10 text-danger border border-danger-subtle';
                        $lblEstado = 'Crítico';
                    } elseif ($stk <= max($min * 2, $min + 5)) {
                        $badgeClass = 'bg-warning bg-opacity-10 text-warning-emphasis border border-warning-subtle';
                        $lblEstado = 'Por Agotarse';
                    } else {
                        $badgeClass = 'bg-success bg-opacity-10 text-success border border-success-subtle';
                        $lblEstado = 'Óptimo';
                    }
                @endphp
                <tr class="fila-reporte">
                    <td class="ps-3 text-center text-muted" data-sort-value="{{ $idx + 1 }}">{{ $idx + 1 }}</td>
                    <td class="text-center" data-sort-value="{{ $p->codigo }}">
                        <span class="code-pill">{{ $p->codigo }}</span>
                    </td>
                    <td class="text-start" data-sort-value="{{ strtolower($p->nombre) }}">
                        <div class="fw-bold text-dark">{{ $p->nombre }}</div>
                        @if($p->tieneUnidadSecundaria())
                            <span class="badge bg-light text-secondary border px-1.5 py-0.5 mt-1" style="font-size: 0.68rem;">
                                <i class="bi bi-layers-half me-1 text-primary"></i>Dual: {{ optional($p->unidad)->nombre ?? 'Unid' }} + {{ optional($p->unidadSecundaria)->nombre }}
                            </span>
                        @endif
                    </td>
                    <td class="text-center" data-sort-value="{{ strtolower(optional($p->categoria)->nombre ?? '') }}">
                        @if($p->categoria)
                            <span class="badge bg-light text-secondary border">{{ $p->categoria->nombre }}</span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="text-center" data-sort-value="{{ strtolower(optional($p->marca)->nombre ?? '') }}">
                        @if($p->marca)
                            <span class="badge bg-light text-dark border px-2 py-1 fw-bold">{{ $p->marca->nombre }}</span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="text-center" data-sort-value="{{ strtolower(optional($p->unidad)->nombre ?? '') }}">
                        <span class="small fw-semibold text-dark">{{ optional($p->unidad)->nombre ?? 'Pieza' }}</span>
                        @if($p->tieneUnidadSecundaria())
                            <div class="mt-1">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size: 0.70rem; font-weight: 600;" title="Segunda unidad de venta / empaque">
                                    <i class="bi bi-box-seam me-1"></i>{{ optional($p->unidadSecundaria)->nombre }}
                                    @if($p->factor_conversion > 1)
                                        (x{{ number_format($p->factor_conversion, 0) }})
                                    @endif
                                </span>
                            </div>
                        @endif
                    </td>
                    <td class="text-end" data-sort-value="{{ $costo }}">
                        <span class="text-muted small">Bs. {{ number_format($costo, 2) }}</span>
                    </td>
                    <td class="text-end" data-sort-value="{{ $pv }}">
                        <div>
                            <span class="fw-bold text-dark">Bs. {{ number_format($pv, 2) }}</span>
                            @if($p->tieneUnidadSecundaria())
                                <small class="text-muted d-block" style="font-size: 0.68rem;">/ {{ optional($p->unidad)->nombre ?? 'Unid' }}</small>
                            @endif
                        </div>
                        @if($p->tieneUnidadSecundaria() && $p->precio_venta_secundario > 0)
                            <div class="mt-1 pt-1 border-top border-light">
                                <span class="fw-bold text-success" style="font-size: 0.85rem;">Bs. {{ number_format($p->precio_venta_secundario, 2) }}</span>
                                <small class="text-muted d-block" style="font-size: 0.68rem;">/ {{ optional($p->unidadSecundaria)->nombre }}</small>
                            </div>
                        @endif
                    </td>
                    <td class="text-end" data-sort-value="{{ $pc ?? $pv }}">
                        @if($pc !== null && $pc > 0)
                            <div>
                                <span class="fw-bold text-primary">Bs. {{ number_format($pc, 2) }}</span>
                                <small class="d-block text-muted" style="font-size: 0.65rem;">/ {{ optional($p->unidad)->nombre ?? 'Unid' }}</small>
                            </div>
                        @else
                            <span class="text-muted small">—</span>
                        @endif

                        @if($p->tieneUnidadSecundaria() && $p->precio_cliente_secundario > 0)
                            <div class="mt-1 pt-1 border-top border-light">
                                <span class="fw-bold text-primary" style="font-size: 0.85rem;">Bs. {{ number_format($p->precio_cliente_secundario, 2) }}</span>
                                <small class="d-block text-muted" style="font-size: 0.65rem;">/ {{ optional($p->unidadSecundaria)->nombre }}</small>
                            </div>
                        @endif
                    </td>
                    <td class="text-center" data-sort-value="{{ $stk }}">
                        <span class="badge {{ $badgeClass }} px-2 py-1 fw-bold">
                            {{ $stk }} UND.
                        </span>
                    </td>
                    <td class="text-end pe-3" data-sort-value="{{ $val }}">
                        <span class="fw-bold" style="color: #0284c7;">Bs. {{ number_format($val, 2) }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center py-5 text-muted">
                        <i class="bi bi-box-seam fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        No se encontraron productos con los filtros seleccionados.
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($productos->count() > 0)
            <tfoot class="bg-light fw-bold border-top">
                <tr>
                    <td colspan="6" class="ps-3 text-end text-dark">TOTALES GENERALES:</td>
                    <td class="text-end text-muted small">Bs. {{ number_format($totalCosto, 2) }}</td>
                    <td class="text-end">—</td>
                    <td class="text-end">—</td>
                    <td class="text-center text-success fs-6 fw-bold">{{ number_format($totalStock, 0) }} UND.</td>
                    <td class="text-end pe-3 fs-6 fw-bold text-primary">Bs. {{ number_format($totalValor, 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

{{-- SCRIPT DE FILTRADO Y ORDENAMIENTO INSTANTÁNEO --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabla = document.getElementById('tablaReporteProductos');
    const tbody = tabla ? tabla.querySelector('tbody') : null;
    const filtroInput = document.getElementById('filtroRapido');
    const badgeTotal = document.getElementById('badgeTotal');

    if (!tabla || !tbody) return;

    // Cache filas en memoria para ordenamiento y búsqueda ultra rápida (0 lag)
    const domFilas = Array.from(tbody.querySelectorAll('tr.fila-reporte'));
    const cacheFilas = domFilas.map(tr => {
        const celdas = Array.from(tr.children);
        const sortValues = celdas.map(td => {
            const raw = td.getAttribute('data-sort-value') ?? '';
            const n = Number(raw);
            const isNum = raw !== '' && !isNaN(n);
            return isNum ? n : raw.toLowerCase();
        });

        return {
            tr: tr,
            texto: (tr.innerText || tr.textContent).toLowerCase(),
            sortValues: sortValues
        };
    });

    // Filtro rápido en pantalla
    let timer = null;
    if (filtroInput) {
        filtroInput.addEventListener('input', function() {
            clearTimeout(timer);
            timer = setTimeout(() => {
                const q = filtroInput.value.toLowerCase().trim();
                const palabras = q.split(/\s+/).filter(Boolean);
                let visibles = 0;
                for (let i = 0; i < cacheFilas.length; i++) {
                    const row = cacheFilas[i];
                    if (palabras.length === 0 || palabras.every(p => row.texto.includes(p))) {
                        row.tr.style.display = '';
                        visibles++;
                    } else {
                        row.tr.style.display = 'none';
                    }
                }
                if (badgeTotal) badgeTotal.textContent = visibles;
            }, 60);
        });
    }

    // Ordenamiento por clic en encabezados
    const headers = tabla.querySelectorAll('th.sortable');
    let colActual = -1;
    let ordenAsc = true;

    headers.forEach((header, index) => {
        header.addEventListener('click', () => {
            ordenAsc = (colActual === index) ? !ordenAsc : true;
            colActual = index;

            headers.forEach(h => {
                const icon = h.querySelector('.icono-orden');
                if (icon) {
                    icon.className = 'icono-orden bi bi-arrow-down-up ms-1';
                    icon.style.opacity = '0.5';
                }
            });

            const iconActual = header.querySelector('.icono-orden');
            if (iconActual) {
                iconActual.className = `icono-orden bi ${ordenAsc ? 'bi-caret-down-fill text-primary' : 'bi-caret-up-fill text-primary'} ms-1`;
                iconActual.style.opacity = '1';
            }

            cacheFilas.sort((a, b) => {
                const valA = a.sortValues[index];
                const valB = b.sortValues[index];

                if (typeof valA === 'number' && typeof valB === 'number') {
                    return ordenAsc ? valA - valB : valB - valA;
                }
                if (valA < valB) return ordenAsc ? -1 : 1;
                if (valA > valB) return ordenAsc ? 1 : -1;
                return 0;
            });

            const fragment = document.createDocumentFragment();
            for (let i = 0; i < cacheFilas.length; i++) {
                fragment.appendChild(cacheFilas[i].tr);
            }
            tbody.appendChild(fragment);
        });
    });
});
</script>

@endsection
