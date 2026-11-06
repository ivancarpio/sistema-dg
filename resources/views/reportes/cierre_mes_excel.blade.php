<table>
    {{-- FILA 1: Encabezado General --}}
    <tr>
        <th colspan="6" style="background-color: #A9D08E; font-size: 14pt; font-weight: bold; text-align: center; border: 1px solid #000000; height: 35px;">
            {{ $empresa ?? 'KRONA DISTRIBUIDORA GUADALUPE' }} {{ $anio ?? date('Y') }}
        </th>
    </tr>

    {{-- FILA 2: Gerente --}}
    <tr>
        <td style="font-weight: bold; text-align: center; border: 1px solid #000000; height: 22px;">GERENTE</td>
        <td colspan="3" style="font-weight: bold; border: 1px solid #000000;">{{ $gerente ?? 'LUIS ANTENOR CALDERON RODRIGUEZ' }}</td>
        <td colspan="2" style="border: 1px solid #000000;"></td>
    </tr>

    {{-- FILA 3: Encargado --}}
    <tr>
        <td style="font-weight: bold; text-align: center; border: 1px solid #000000; height: 22px;">ENCARGADO</td>
        <td colspan="3" style="font-weight: bold; border: 1px solid #000000;">{{ $encargado ?? 'AJAEL' }}</td>
        <td colspan="2" style="border: 1px solid #000000;"></td>
    </tr>

    {{-- FILA 4: Encargada Inv. --}}
    <tr>
        <td style="font-weight: bold; text-align: center; border: 1px solid #000000; height: 22px;">ENCARGADA INV.</td>
        <td colspan="3" style="font-weight: bold; border: 1px solid #000000;">{{ $encargada_inv ?? 'CIELO MADELEN CALDERON POMA' }}</td>
        <td colspan="2" style="border: 1px solid #000000;"></td>
    </tr>

    {{-- FILA 5: Cabeceras de Columnas --}}
    <tr>
        <th style="background-color: #92D050; font-weight: bold; text-align: center; border: 1px solid #000000; height: 25px;">
            CODIGO
        </th>
        <th style="background-color: #FFA500; font-weight: bold; text-align: center; border: 1px solid #000000;">
            PRODUCTO
        </th>
        <th style="background-color: #FF0000; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #000000;">
            CANTIDAD
        </th>
        <th style="background-color: #00B0F0; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #000000;">
            VENTA
        </th>
        <th style="background-color: #ED7D31; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #000000;">
            TOTAL EN TIENDA
        </th>
        <th style="background-color: #00B0F0; font-weight: bold; text-align: center; border: 1px solid #000000;">
            CHECK
        </th>
    </tr>

    {{-- FILAS DE DATOS --}}
    @foreach($items as $index => $item)
    <tr>
        <td style="background-color: #00B0F0; text-align: center; border: 1px solid #000000;">
            {{ $item['codigo'] }}
        </td>
        <td style="background-color: #00B0F0; text-align: left; border: 1px solid #000000;">
            {{ $item['nombre'] }}
        </td>
        <td style="background-color: #00B0F0; text-align: center; font-weight: bold; border: 1px solid #000000;">
            {{ $item['cantidad_anterior'] }}
        </td>
        <td style="background-color: #00B0F0; text-align: center; border: 1px solid #000000;">
            {{ $item['venta'] > 0 ? $item['venta'] : '' }}
        </td>
        <td style="background-color: #00B0F0; text-align: center; font-weight: bold; border: 1px solid #000000;">
            {{ $item['total_tienda'] }}
        </td>
        <td style="background-color: #00B0F0; text-align: center; font-weight: bold; border: 1px solid #000000;">
            {{ $item['verificado'] ? 'OK' : '' }}
        </td>
    </tr>
    @endforeach
</table>
