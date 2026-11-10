<table>
    {{-- TITULO --}}
    <tr>
        <th colspan="7" style="font-size: 14pt; font-weight: bold; text-align: center; color: #1E40AF;">
            DISTRIBUIDORA GUADALUPE — COMPROBANTE DE ARQUEO DE CAJA #{{ $arqueo->id }}
        </th>
    </tr>
    <tr>
        <td colspan="7" style="text-align: center; color: #64748B;">
            Fecha de Arqueo: {{ $arqueo->created_at->format('d/m/Y H:i') }} | Cajero Responsable: {{ optional($arqueo->usuario)->name ?? 'Cajero' }} | Total Ventas: {{ $arqueo->cantidad_ventas }}
        </td>
    </tr>
    <tr><td colspan="7"></td></tr>

    {{-- RESUMEN DE FONDOS --}}
    <tr style="background-color: #1E40AF; color: #FFFFFF; font-weight: bold;">
        <th colspan="2" style="background-color: #1E40AF; color: #FFFFFF; font-weight: bold; text-align: center;">CONCEPTO</th>
        <th colspan="2" style="background-color: #1E40AF; color: #FFFFFF; font-weight: bold; text-align: center;">MONTO RECAUDADO</th>
        <th colspan="3" style="background-color: #1E40AF; color: #FFFFFF; font-weight: bold; text-align: center;">DETALLES</th>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold;">Efectivo en Caja</td>
        <td colspan="2" style="font-weight: bold; color: #16A34A;">Bs. {{ number_format($arqueo->total_efectivo, 2) }}</td>
        <td colspan="3" style="color: #64748B;">Billetes y monedas verificados en gaveta</td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold;">Transferencias QR Simple</td>
        <td colspan="2" style="font-weight: bold; color: #0284C7;">Bs. {{ number_format($arqueo->total_qr, 2) }}</td>
        <td colspan="3" style="color: #64748B;">Comprobantes bancarios verificados</td>
    </tr>
    <tr style="background-color: #F1F5F9;">
        <td colspan="2" style="font-weight: bold; font-size: 11pt;">TOTAL GENERAL ARQUEADO</td>
        <td colspan="2" style="font-weight: bold; font-size: 11pt; color: #0F172A;">Bs. {{ number_format($arqueo->total_general, 2) }}</td>
        <td colspan="3" style="font-weight: bold;">Total cuadre del turno</td>
    </tr>
    @if($arqueo->observaciones)
    <tr>
        <td colspan="2"><strong>Observaciones:</strong></td>
        <td colspan="5">{{ $arqueo->observaciones }}</td>
    </tr>
    @endif
    <tr><td colspan="7"></td></tr>

    {{-- DETALLE DE TRANSACCIONES --}}
    <tr style="background-color: #334155; color: #FFFFFF; font-weight: bold;">
        <th style="background-color: #334155; color: #FFFFFF; font-weight: bold;">Nº NOTA</th>
        <th style="background-color: #334155; color: #FFFFFF; font-weight: bold;">FECHA / HORA</th>
        <th style="background-color: #334155; color: #FFFFFF; font-weight: bold;">CLIENTE</th>
        <th style="background-color: #334155; color: #FFFFFF; font-weight: bold;">PRODUCTO(S)</th>
        <th style="background-color: #334155; color: #FFFFFF; font-weight: bold;">CANTIDAD</th>
        <th style="background-color: #334155; color: #FFFFFF; font-weight: bold;">MÉTODO</th>
        <th style="background-color: #334155; color: #FFFFFF; font-weight: bold; text-align: right;">TOTAL VENTA</th>
    </tr>

    @foreach($arqueo->ventas as $venta)
        @if($venta->detalles->count() > 0)
            @foreach($venta->detalles as $idx => $det)
            <tr>
                @if($idx === 0)
                    <td>{{ $venta->numero_nota ?? 'SYS-'.str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</td>
                    <td>{{ $venta->cliente ?? 'Cliente Ocasional' }}</td>
                @else
                    <td></td><td></td><td></td>
                @endif
                <td>{{ optional($det->producto)->nombre ?? 'Producto eliminado' }}</td>
                <td style="text-align: center;">{{ number_format($det->cantidad, 0) }}</td>
                @if($idx === 0)
                    <td>{{ $venta->metodo_pago }}</td>
                    <td style="text-align: right; font-weight: bold;">Bs. {{ number_format($venta->total, 2) }}</td>
                @else
                    <td></td><td></td>
                @endif
            </tr>
            @endforeach
        @else
        <tr>
            <td>{{ $venta->numero_nota ?? 'SYS-'.str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</td>
            <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</td>
            <td>{{ $venta->cliente ?? 'Cliente Ocasional' }}</td>
            <td>—</td>
            <td style="text-align: center;">—</td>
            <td>{{ $venta->metodo_pago }}</td>
            <td style="text-align: right; font-weight: bold;">Bs. {{ number_format($venta->total, 2) }}</td>
        </tr>
        @endif
    @endforeach

    {{-- FILA TOTALES --}}
    <tr style="background-color: #E2E8F0; font-weight: bold;">
        <td colspan="5"><strong>TOTALES DEL ARQUEO</strong></td>
        <td><strong>{{ $arqueo->cantidad_ventas }} ventas</strong></td>
        <td style="text-align: right;"><strong>Bs. {{ number_format($arqueo->total_general, 2) }}</strong></td>
    </tr>
</table>
