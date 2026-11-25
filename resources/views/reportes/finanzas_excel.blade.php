<table>
    <thead>
        <tr>
            <th colspan="6">REPORTE DE CAJA Y FINANZAS - DISTRIBUIDORA GUADALUPE</th>
        </tr>
        <tr>
            <th colspan="6">Período: {{ $periodoLabel }}</th>
        </tr>
    </thead>
    <tbody>
        <tr><td colspan="6"></td></tr>
        <tr>
            <th colspan="6" style="background-color:#ede9fe; font-weight:bold;">1. RESUMEN DE INGRESOS, GASTOS Y SALIDAS DE GAVETA</th>
        </tr>
        <tr>
            <td>Dinero Cobrado ({{ $conteoVentas }} ventas)</td>
            <td>{{ number_format($ingresosCobrados, 2, '.', '') }}</td>
            <td>Cobrado en Efectivo</td>
            <td>{{ number_format($ingresosEfectivo, 2, '.', '') }}</td>
            <td>Cobrado por QR</td>
            <td>{{ number_format($ingresosQr, 2, '.', '') }}</td>
        </tr>
        <tr>
            <td>Gastos Operativos ({{ $conteoGastos }} reg.)</td>
            <td>{{ number_format($totalGastos, 2, '.', '') }}</td>
            <td>Flujo Neto (Cobrado - Gastos)</td>
            <td>{{ number_format($flujoNetoOperativo, 2, '.', '') }}</td>
            <td>Retiros de Gaveta a Banco ({{ $conteoRetirosBanco }})</td>
            <td>{{ number_format($totalRetirosBanco, 2, '.', '') }}</td>
        </tr>
        <tr>
            <td>Gastos desde Gaveta</td>
            <td>{{ number_format($gastosDesdeGaveta, 2, '.', '') }}</td>
            <td>Gastos desde Caja Chica</td>
            <td>{{ number_format($gastosDesdeCajaChica, 2, '.', '') }}</td>
            <td>Gastos desde Banco / QR / Gerencia</td>
            <td>{{ number_format($gastosDesdeBancoQr + $gastosDesdeGerencia, 2, '.', '') }}</td>
        </tr>

        <tr><td colspan="6"></td></tr>
        <tr>
            <th colspan="6" style="background-color:#fee2e2; font-weight:bold;">2. TOP CATEGORÍAS DE GASTOS OPERATIVOS</th>
        </tr>
        <tr>
            <th>Categoría</th>
            <th>Registros</th>
            <th>Total Gastado ({{ $monedaSimbolo }})</th>
            <th>% del Total</th>
            <th colspan="2"></th>
        </tr>
        @foreach($gastosPorCategoria as $cat)
            <tr>
                <td>{{ $cat->nombre }}</td>
                <td>{{ $cat->total_cantidad }}</td>
                <td>{{ number_format($cat->total_monto, 2, '.', '') }}</td>
                <td>{{ $totalGastos > 0 ? round(($cat->total_monto / $totalGastos) * 100, 1) : 0 }}%</td>
                <td colspan="2"></td>
            </tr>
        @endforeach

        <tr><td colspan="6"></td></tr>
        <tr>
            <th colspan="6" style="background-color:#e0f2fe; font-weight:bold;">3. DETALLE DE RETIROS DE GAVETA PARA BANCO / GERENCIA</th>
        </tr>
        <tr>
            <th>Fecha y Hora</th>
            <th>Turno Gaveta</th>
            <th>Método</th>
            <th>Motivo / Destino</th>
            <th>Cajero</th>
            <th>Monto ({{ $monedaSimbolo }})</th>
        </tr>
        @forelse($retirosBanco as $ret)
            <tr>
                <td>{{ $ret->fecha ? $ret->fecha->format('d/m/Y H:i') : '' }}</td>
                <td>Turno #{{ $ret->arqueo_caja_id }}</td>
                <td>{{ $ret->metodo_pago }}</td>
                <td>{{ $ret->motivo }}</td>
                <td>{{ optional($ret->usuario)->name ?? 'Usuario' }}</td>
                <td>{{ number_format($ret->monto, 2, '.', '') }}</td>
            </tr>
        @empty
            <tr><td colspan="6">No hubo retiros ni depósitos a banco en este período.</td></tr>
        @endforelse

        <tr><td colspan="6"></td></tr>
        <tr>
            <th colspan="6" style="background-color:#f1f5f9; font-weight:bold;">4. LISTADO DE GASTOS OPERATIVOS DEL PERÍODO</th>
        </tr>
        <tr>
            <th>Fecha y Hora</th>
            <th>Categoría</th>
            <th>Descripción</th>
            <th>Origen Fondos</th>
            <th>Usuario</th>
            <th>Monto ({{ $monedaSimbolo }})</th>
        </tr>
        @forelse($gastos as $g)
            <tr>
                <td>{{ $g->fecha ? $g->fecha->format('d/m/Y H:i') : '' }}</td>
                <td>{{ optional($g->categoria)->nombre ?? 'Sin categoría' }}</td>
                <td>{{ $g->descripcion }}</td>
                <td>{{ $g->origen_pago }}</td>
                <td>{{ optional($g->usuario)->name ?? 'Sistema' }}</td>
                <td>{{ number_format($g->monto, 2, '.', '') }}</td>
            </tr>
        @empty
            <tr><td colspan="6">No hay gastos operativos registrados en este período.</td></tr>
        @endforelse
    </tbody>
</table>
