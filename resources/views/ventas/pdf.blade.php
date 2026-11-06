<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nota de Venta</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; color: #333; }
        .header { width: 100%; border-bottom: 2px solid #198754; padding-bottom: 15px; margin-bottom: 20px; }
        .logo-container { width: 50%; vertical-align: top; }
        .company-info { width: 50%; text-align: right; vertical-align: top; }
        .invoice-title { font-size: 28px; font-weight: bold; color: #198754; text-transform: uppercase; margin-bottom: 5px; }
        .details-table { width: 100%; margin-bottom: 25px; }
        .details-table td { padding: 5px 0; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .items-table th { background-color: #f8f9fa; font-weight: bold; color: #333; }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .total-row td { font-weight: bold; font-size: 16px; background-color: #f8f9fa; }
        .footer { text-align: center; margin-top: 50px; font-size: 12px; color: #777; border-top: 1px solid #ddd; padding-top: 15px; }
    </style>
</head>
<body>

    <!-- ENCABEZADO -->
    <table class="header">
        <tr>
            <td class="logo-container">
                <!-- AQUÍ VA TU LOGO -->
                <h2 style="margin: 0; color: #555;">[ TU LOGO AQUÍ ]</h2>
                
                <!-- Cuando tengas tu logo, borra el <h2> de arriba y descomenta esta línea: -->
                <!-- <img src="{{ public_path('img/logo.png') }}" style="max-width: 200px;"> -->
                
                <p style="margin: 5px 0 0 0; font-size: 12px; color: #777;">
                    Santa Cruz, Bolivia<br>
                    Teléfono: +591 XXXXXXXX
                </p>
            </td>
            <td class="company-info">
                <div class="invoice-title">Nota de Venta</div>
                <div style="font-size: 16px; margin-bottom: 5px;"><strong>Nº: </strong> {{ $venta->numero_nota }}</div>
                <div><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>

    <!-- DATOS DEL CLIENTE -->
    <table class="details-table">
        <tr>
            <td><strong>Cliente:</strong> {{ $venta->cliente }}</td>
        </tr>
        <tr>
            <td><strong>Método de Pago:</strong> {{ strtoupper($venta->metodo_pago) }}</td>
        </tr>
    </table>

    <!-- DETALLE DE PRODUCTOS -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 10%;">Cant.</th>
                <th style="width: 50%;">Descripción del Producto</th>
                <th class="text-right" style="width: 20%;">P. Unit.</th>
                <th class="text-right" style="width: 20%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $detalle)
            <tr>
                <td class="text-center">{{ $detalle->cantidad }}</td>
                <td>{{ optional($detalle->producto)->nombre ?? 'Producto Eliminado' }}</td>
                <td class="text-right">Bs. {{ number_format($detalle->precio_unitario, 2) }}</td>
                <td class="text-right">Bs. {{ number_format($detalle->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right">TOTAL A PAGAR:</td>
                <td class="text-right" style="color: #198754;">Bs. {{ number_format($venta->total, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        ¡Gracias por su preferencia!<br>
        Esta nota de venta no es válida como factura fiscal.
    </div>

</body>
</html>