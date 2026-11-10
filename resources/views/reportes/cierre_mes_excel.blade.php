<table>
    {{-- FILA 1: Encabezado General --}}
    <tr>
        <th colspan="6" style="background-color: #1E293B; color: #FFFFFF; font-size: 14pt; font-weight: bold; text-align: center; height: 35px;">
            DISTRIBUIDORA GUADALUPE — REPORTE DE CIERRE DE MES (CUADRE DE STOCK)
        </th>
    </tr>

    {{-- FILA 2: Info del Período --}}
    <tr>
        <td colspan="6" style="text-align: center; color: #64748B; font-size: 10pt; height: 22px;">
            Período: {{ $periodo_label ?? '' }} | Generado el: {{ now()->format('d/m/Y H:i') }} | Estado: Auditoría de Inventario Físico
        </td>
    </tr>
    <tr><td colspan="6"></td></tr>

    {{-- FILA 4: Cabeceras de Columnas Profesionales --}}
    <tr>
        <th style="background-color: #1E293B; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #334155; height: 26px;">
            CÓDIGO
        </th>
        <th style="background-color: #1E293B; color: #FFFFFF; font-weight: bold; text-align: left; border: 1px solid #334155;">
            PRODUCTO
        </th>
        <th style="background-color: #334155; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #334155;">
            STOCK INICIAL
        </th>
        <th style="background-color: #334155; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #334155;">
            VENTAS MES
        </th>
        <th style="background-color: #0F172A; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #334155;">
            TOTAL EN TIENDA
        </th>
        <th style="background-color: #0284C7; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #334155;">
            ESTADO / CHECK
        </th>
    </tr>

    {{-- FILAS DE DATOS --}}
    @php
        $totInicial = 0;
        $totVentas = 0;
        $totTienda = 0;
    @endphp
    @foreach($items as $index => $item)
    @php
        $totInicial += (int)($item['cantidad_anterior'] ?? 0);
        $totVentas  += (int)($item['ventas'] ?? 0);
        $totTienda  += (int)($item['total_tienda'] ?? 0);
        $bg = ($index % 2 === 0) ? '#FFFFFF' : '#F8FAFC';
    @endphp
    <tr style="background-color: {{ $bg }};">
        <td style="text-align: center; border: 1px solid #E2E8F0; font-family: monospace;">
            {{ $item['codigo'] }}
        </td>
        <td style="text-align: left; border: 1px solid #E2E8F0; font-weight: 500;">
            {{ $item['nombre'] }}
        </td>
        <td style="text-align: center; border: 1px solid #E2E8F0;">
            {{ number_format($item['cantidad_anterior'] ?? 0, 0) }}
        </td>
        <td style="text-align: center; border: 1px solid #E2E8F0;">
            {{ ($item['ventas'] ?? 0) > 0 ? number_format($item['ventas'], 0) : '—' }}
        </td>
        <td style="text-align: center; border: 1px solid #E2E8F0; font-weight: bold;">
            {{ number_format($item['total_tienda'] ?? 0, 0) }}
        </td>
        <td style="text-align: center; border: 1px solid #E2E8F0; font-weight: bold; color: {{ ($item['verificado'] ?? false) ? '#0284C7' : '#94A3B8' }};">
            {{ ($item['verificado'] ?? false) ? 'OK (Verificado)' : 'Pendiente' }}
        </td>
    </tr>
    @endforeach

    {{-- FILA DE TOTALES --}}
    <tr style="background-color: #E2E8F0; font-weight: bold; height: 26px;">
        <td colspan="2" style="text-align: right; border: 1px solid #CBD5E1;">
            <strong>TOTALES ({{ count($items) }} PRODUCTOS):</strong>
        </td>
        <td style="text-align: center; border: 1px solid #CBD5E1;">
            <strong>{{ number_format($totInicial, 0) }}</strong>
        </td>
        <td style="text-align: center; border: 1px solid #CBD5E1;">
            <strong>{{ number_format($totVentas, 0) }}</strong>
        </td>
        <td style="text-align: center; border: 1px solid #CBD5E1;">
            <strong>{{ number_format($totTienda, 0) }}</strong>
        </td>
        <td style="text-align: center; border: 1px solid #CBD5E1;"></td>
    </tr>
</table>
