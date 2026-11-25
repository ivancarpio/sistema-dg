<table>
    <thead>
        <tr>
            <th colspan="16" style="font-weight: bold; font-size: 14pt; color: #0f172a;">
                DISTRIBUIDORA GUADALUPE — REPORTE GENERAL DE PRODUCTOS E INVENTARIO
            </th>
        </tr>
        <tr>
            <th colspan="16" style="font-size: 9pt; color: #64748b;">
                Generado el: {{ $fecha }} | Filtros aplicados: {{ $filtros['descripcion'] ?? 'Todo el catálogo' }}
            </th>
        </tr>
        <tr>
            <th colspan="16"></th>
        </tr>
        <tr style="background-color: #1e293b; color: #ffffff; font-weight: bold; text-align: center;">
            <th style="border: 1px solid #94a3b8; text-align: center;">N°</th>
            <th style="border: 1px solid #94a3b8; text-align: center;">CÓDIGO</th>
            <th style="border: 1px solid #94a3b8; text-align: left;">PRODUCTO</th>
            <th style="border: 1px solid #94a3b8; text-align: center;">CATEGORÍA</th>
            <th style="border: 1px solid #94a3b8; text-align: center;">MARCA</th>
            <th style="border: 1px solid #94a3b8; text-align: center;">UNIDAD BASE</th>
            <th style="border: 1px solid #94a3b8; text-align: right;">COSTO ({{ $monedaSimbolo }})</th>
            <th style="border: 1px solid #94a3b8; text-align: right;">PRECIO VENTA BASE ({{ $monedaSimbolo }})</th>
            <th style="border: 1px solid #94a3b8; text-align: right;">PRECIO CLIENTE BASE ({{ $monedaSimbolo }})</th>
            <th style="border: 1px solid #94a3b8; text-align: center;">2DA UNIDAD / EMPAQUE</th>
            <th style="border: 1px solid #94a3b8; text-align: center;">FACTOR CONVERSIÓN</th>
            <th style="border: 1px solid #94a3b8; text-align: right;">P. VENTA EMPAQUE ({{ $monedaSimbolo }})</th>
            <th style="border: 1px solid #94a3b8; text-align: right;">P. CLIENTE EMPAQUE ({{ $monedaSimbolo }})</th>
            <th style="border: 1px solid #94a3b8; text-align: center;">STOCK EN TIENDA</th>
            <th style="border: 1px solid #94a3b8; text-align: right;">VALOR EN TIENDA ({{ $monedaSimbolo }})</th>
            <th style="border: 1px solid #94a3b8; text-align: center;">ESTADO STOCK</th>
        </tr>
    </thead>
    <tbody>
        @php
            $sumStock = 0;
            $sumValor = 0;
            $sumCosto = 0;
        @endphp
        @foreach($productos as $index => $p)
        @php
            $stk = (int)($p->stock ?? 0);
            $pv  = (float)($p->precio_venta ?? 0);
            $pc  = $p->precio_cliente !== null ? (float)$p->precio_cliente : null;
            $costo = (float)($p->costo ?? 0);
            $val = $stk * $pv;
            $valCosto = $stk * $costo;

            $sumStock += $stk;
            $sumValor += $val;
            $sumCosto += $valCosto;

            $min = is_numeric($p->stock_minimo) && $p->stock_minimo > 0 ? (int)$p->stock_minimo : 5;
            if ($stk <= 0) {
                $estado = 'Agotado';
            } elseif ($stk <= $min) {
                $estado = 'Crítico (≤ Mínimo)';
            } elseif ($stk <= max($min * 2, $min + 5)) {
                $estado = 'Por Agotarse';
            } else {
                $estado = 'Óptimo';
            }

            $tieneSec = $p->tieneUnidadSecundaria();
        @endphp
        <tr>
            <td style="border: 1px solid #e2e8f0; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #e2e8f0; text-align: center; font-family: monospace;">{{ $p->codigo }}</td>
            <td style="border: 1px solid #e2e8f0; text-align: left; font-weight: 600;">{{ $p->nombre }}</td>
            <td style="border: 1px solid #e2e8f0; text-align: center;">{{ optional($p->categoria)->nombre ?? '—' }}</td>
            <td style="border: 1px solid #e2e8f0; text-align: center;">{{ optional($p->marca)->nombre ?? '—' }}</td>
            <td style="border: 1px solid #e2e8f0; text-align: center;">{{ optional($p->unidad)->nombre ?? 'Pieza' }}</td>
            <td style="border: 1px solid #e2e8f0; text-align: right;">{{ number_format($costo, 2, '.', '') }}</td>
            <td style="border: 1px solid #e2e8f0; text-align: right; font-weight: 600;">{{ number_format($pv, 2, '.', '') }}</td>
            <td style="border: 1px solid #e2e8f0; text-align: right;">{{ $pc !== null ? number_format($pc, 2, '.', '') : '—' }}</td>
            <td style="border: 1px solid #e2e8f0; text-align: center;">{{ $tieneSec ? (optional($p->unidadSecundaria)->nombre ?? '—') : '—' }}</td>
            <td style="border: 1px solid #e2e8f0; text-align: center;">{{ ($tieneSec && $p->factor_conversion > 0) ? number_format($p->factor_conversion, 0) : '—' }}</td>
            <td style="border: 1px solid #e2e8f0; text-align: right;">{{ ($tieneSec && $p->precio_venta_secundario > 0) ? number_format($p->precio_venta_secundario, 2, '.', '') : '—' }}</td>
            <td style="border: 1px solid #e2e8f0; text-align: right;">{{ ($tieneSec && $p->precio_cliente_secundario > 0) ? number_format($p->precio_cliente_secundario, 2, '.', '') : '—' }}</td>
            <td style="border: 1px solid #e2e8f0; text-align: center; font-weight: bold;">{{ $stk }}</td>
            <td style="border: 1px solid #e2e8f0; text-align: right; font-weight: bold;">{{ number_format($val, 2, '.', '') }}</td>
            <td style="border: 1px solid #e2e8f0; text-align: center;">{{ $estado }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="16"></th>
        </tr>
        <tr style="background-color: #f1f5f9; font-weight: bold;">
            <td colspan="6" style="border: 1px solid #cbd5e1; text-align: right; font-weight: bold;">TOTALES GENERALES ({{ count($productos) }} PRODUCTOS):</td>
            <td style="border: 1px solid #cbd5e1; text-align: right; font-weight: bold;">{{ $monedaSimbolo }} {{ number_format($sumCosto, 2) }}</td>
            <td style="border: 1px solid #cbd5e1; text-align: right;">—</td>
            <td style="border: 1px solid #cbd5e1; text-align: right;">—</td>
            <td style="border: 1px solid #cbd5e1; text-align: center;">—</td>
            <td style="border: 1px solid #cbd5e1; text-align: center;">—</td>
            <td style="border: 1px solid #cbd5e1; text-align: right;">—</td>
            <td style="border: 1px solid #cbd5e1; text-align: right;">—</td>
            <td style="border: 1px solid #cbd5e1; text-align: center; font-weight: bold; font-size: 11pt;">{{ number_format($sumStock, 0) }}</td>
            <td style="border: 1px solid #cbd5e1; text-align: right; font-weight: bold; font-size: 11pt; color: #0284c7;">{{ $monedaSimbolo }} {{ number_format($sumValor, 2) }}</td>
            <td style="border: 1px solid #cbd5e1; text-align: center;">Auditoría Completa</td>
        </tr>
    </tfoot>
</table>
