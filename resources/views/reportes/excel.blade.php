{{-- Vista usada por el Export de Excel (no se muestra en el browser) --}}
<table>
    {{-- FILA 1: Título --}}
    <tr>
        <td colspan="9"><strong>Sistema DG — Ferretería | Reporte de Ventas</strong></td>
    </tr>
    {{-- FILA 2: Info del reporte --}}
    <tr>
        <td colspan="9">
            Generado el: {{ now()->format('d/m/Y H:i') }}
            @if(!empty($filtros['periodo_label']))
                | Período: {{ $filtros['periodo_label'] }}
            @endif
            @if(!empty($filtros['metodo_pago']))
                | Método de pago: {{ $filtros['metodo_pago'] }}
            @endif
        </td>
    </tr>
    {{-- FILA 3: Encabezados --}}
    <tr>
        <td><strong>Nº Nota</strong></td>
        <td><strong>Fecha</strong></td>
        <td><strong>Hora</strong></td>
        <td><strong>Cliente</strong></td>
        <td><strong>Producto(s)</strong></td>
        <td><strong>Cantidad</strong></td>
        <td><strong>Precio Unit.</strong></td>
        <td><strong>Método de Pago</strong></td>
        <td><strong>Total Venta</strong></td>
        <td><strong>Cajero</strong></td>
        <td><strong>Estado</strong></td>
    </tr>
    {{-- DATOS: 1 fila por producto vendido (con la info de la venta repetida) --}}
    @foreach($ventas as $venta)
        @if($venta->detalles->count() > 0)
            @foreach($venta->detalles as $i => $detalle)
            <tr>
                @if($i === 0)
                    <td>{{ $venta->numero_nota ?? 'SYS-'.str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('H:i') }}</td>
                    <td>{{ $venta->cliente ?? 'Cliente Ocasional' }}</td>
                @else
                    <td></td><td></td><td></td><td></td>
                @endif
                <td>{{ optional($detalle->producto)->nombre ?? 'Producto eliminado' }}</td>
                <td>{{ number_format($detalle->cantidad, 0) }}</td>
                <td>{{ number_format($detalle->precio_unitario, 2) }}</td>
                @if($i === 0)
                    <td>{{ $venta->metodo_pago }}</td>
                    <td>{{ number_format($venta->total, 2) }}</td>
                    <td>{{ optional($venta->usuario)->name ?? 'Cajero' }}</td>
                    <td>{{ $venta->estado }}</td>
                @else
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                @endif
            </tr>
            @endforeach
        @else
        {{-- Venta sin detalles guardados --}}
        <tr>
            <td>{{ $venta->numero_nota ?? 'SYS-'.str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</td>
            <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('H:i') }}</td>
            <td>{{ $venta->cliente ?? 'Cliente Ocasional' }}</td>
            <td>—</td>
            <td>—</td>
            <td>—</td>
            <td>{{ $venta->metodo_pago }}</td>
            <td>{{ number_format($venta->total, 2) }}</td>
            <td>{{ optional($venta->usuario)->name ?? 'Cajero' }}</td>
            <td>{{ $venta->estado }}</td>
        </tr>
        @endif
    @endforeach
    {{-- FILA DE TOTALES --}}
    @php
        $totEfectivo = is_array($totales) ? ($totales['efectivo'] ?? 0) : 0;
        $totQr       = is_array($totales) ? ($totales['qr'] ?? 0) : 0;
        $totPendiente= is_array($totales) ? ($totales['por_pagar'] ?? 0) : 0;
        $totGeneral  = is_array($totales) ? ($totales['general'] ?? 0) : 0;
    @endphp
    <tr>
        <td colspan="7"><strong>TOTALES</strong></td>
        <td>
            <strong>
                Efectivo: Bs. {{ number_format($totEfectivo, 2) }} | QR: Bs. {{ number_format($totQr, 2) }}
                @if($totPendiente > 0)
                    | Por Cobrar: Bs. {{ number_format($totPendiente, 2) }}
                @endif
            </strong>
        </td>
        <td><strong>Bs. {{ number_format($totGeneral, 2) }}</strong></td>
        <td colspan="2"></td>
    </tr>
</table>
