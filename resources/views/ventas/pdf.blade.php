<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nota de Venta - {{ $venta->numero_nota }}</title>
    <style>
        @page {
            margin: 28px 32px;
        }
        body {
            font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
            font-size: 13px;
            color: #1e293b;
            line-height: 1.4;
            background: #ffffff;
        }

        /* Encabezado */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #16a34a;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .logo-col {
            width: 55%;
            vertical-align: middle;
        }
        .company-info-col {
            width: 45%;
            text-align: right;
            vertical-align: middle;
        }
        .logo-img {
            max-height: 80px;
            max-width: 220px;
            display: block;
            margin-bottom: 6px;
        }
        .company-meta {
            font-size: 11px;
            color: #64748b;
            line-height: 1.35;
        }

        .doc-title {
            font-size: 24px;
            font-weight: 800;
            color: #16a34a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 4px 0;
        }
        .doc-number {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 3px;
        }
        .doc-date {
            font-size: 11px;
            color: #64748b;
        }

        /* Cuadro de Información de la Venta / Cliente */
        .info-card {
            width: 100%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 18px;
        }
        .info-card-table {
            width: 100%;
        }
        .info-card-table td {
            padding: 3px 0;
            font-size: 12px;
            vertical-align: top;
        }
        .info-label {
            color: #64748b;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .info-value {
            color: #0f172a;
            font-weight: 600;
        }

        /* Tabla de Productos */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .items-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            border: 1px solid #1e293b;
        }
        .items-table td {
            border: 1px solid #e2e8f0;
            padding: 8px 10px;
            font-size: 12px;
            vertical-align: middle;
        }
        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .text-left { text-align: left !important; }

        /* Fila de Totales */
        .total-row td {
            background-color: #f1f5f9 !important;
            font-weight: 800;
            font-size: 14px;
            border-top: 2px solid #cbd5e1;
            padding: 10px;
        }
        .total-highlight {
            color: #16a34a;
            font-size: 16px;
        }

        /* Desglose de crédito si aplica */
        .credito-box {
            margin-top: 8px;
            padding: 8px 12px;
            background: #fffbeb;
            border: 1px solid #fef3c7;
            border-radius: 4px;
            font-size: 11px;
            color: #92400e;
        }

        /* Pie de página */
        .footer {
            margin-top: 30px;
            padding-top: 14px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }
        .footer-message {
            font-weight: 700;
            color: #0f172a;
            font-size: 12px;
            margin-bottom: 3px;
        }
        .footer-disclaimer {
            font-size: 10px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    <!-- ======================================================== -->
    <!-- ENCABEZADO INSTITUCIONAL                                 -->
    <!-- ======================================================== -->
    <table class="header-table">
        <tr>
            <!-- LADO IZQUIERDO: LOGO Y DATOS DE LA EMPRESA -->
            <td class="logo-col">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" class="logo-img" alt="{{ $empresa['nombre'] ?? 'Logo' }}">
                @elseif(file_exists(public_path('img/logo_white.png')))
                    <img src="{{ public_path('img/logo_white.png') }}" class="logo-img" alt="{{ $empresa['nombre'] ?? 'Logo' }}">
                @else
                    <h2 style="margin: 0 0 4px 0; color: #16a34a; font-weight: 800;">
                        {{ $empresa['nombre'] ?? 'DISTRIBUIDORA GUADALUPE' }}
                    </h2>
                @endif

                <div class="company-meta">
                    @if(!empty($empresa['direccion']))
                        <div>{{ $empresa['direccion'] }}</div>
                    @endif
                    @if(!empty($empresa['telefono']))
                        <div>Tel / Cel: {{ $empresa['telefono'] }}</div>
                    @endif
                </div>
            </td>

            <!-- LADO DERECHO: TÍTULO Y NÚMERO DE NOTA -->
            <td class="company-info-col">
                <div class="doc-title">Nota de Venta</div>
                <div class="doc-number">Nº: {{ $venta->numero_nota }}</div>
                <div class="doc-date">
                    <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}
                </div>
                @if($venta->usuario)
                    <div class="doc-date">
                        <strong>Cajero:</strong> {{ $venta->usuario->name }}
                    </div>
                @endif
            </td>
        </tr>
    </table>

    <!-- ======================================================== -->
    <!-- DATOS DEL CLIENTE Y DETALLES DE COBRO                     -->
    <!-- ======================================================== -->
    <div class="info-card">
        <table class="info-card-table">
            <tr>
                <td style="width: 60%;">
                    <span class="info-label">Cliente:</span><br>
                    <span class="info-value" style="font-size: 14px;">{{ $venta->cliente }}</span>
                    @if(($venta->tipo_tarifa ?? '') === 'Regular' || $venta->id_cliente)
                        <span style="font-size: 10px; color: #0284c7; background: #e0f2fe; padding: 1px 6px; border-radius: 4px; font-weight: 700; margin-left: 4px;">Cliente Regular</span>
                    @endif
                </td>
                <td style="width: 40%; text-align: right;">
                    <span class="info-label">Método de Pago:</span><br>
                    <span class="info-value">
                        @if($venta->metodo_pago === 'QR')
                            QR SIMPLE / TRANSFERENCIA
                        @elseif($venta->metodo_pago === 'Por Pagar')
                            VENTA A CRÉDITO (PENDIENTE)
                        @else
                            EFECTIVO
                        @endif
                    </span>
                </td>
            </tr>
        </table>

        {{-- Desglose adicional si fue venta a crédito con saldo pendiente --}}
        @if($venta->metodo_pago === 'Por Pagar' && floatval($venta->saldo_pendiente ?? 0) > 0)
        <div class="credito-box">
            <strong>Control de Crédito:</strong> 
            Cobrado/Adelanto: Bs. {{ number_format($venta->monto_pagado ?? 0, 2) }} 
            &nbsp;|&nbsp; 
            <strong>Saldo Pendiente: Bs. {{ number_format($venta->saldo_pendiente, 2) }}</strong>
        </div>
        @endif
    </div>

    <!-- ======================================================== -->
    <!-- DETALLE DE PRODUCTOS                                     -->
    <!-- ======================================================== -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 10%;">Cant.</th>
                <th class="text-left" style="width: 54%;">Descripción del Producto</th>
                <th class="text-right" style="width: 18%;">P. Unitario</th>
                <th class="text-right" style="width: 18%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $detalle)
            <tr>
                <td class="text-center font-weight-bold">
                    {{ number_format($detalle->cantidad, 0) }}
                </td>
                <td class="text-left">
                    @if(optional($detalle->producto)->codigo)
                        <span style="font-size: 10px; color: #64748b; font-weight: bold;">[{{ $detalle->producto->codigo }}]</span>
                    @endif
                    <strong style="color: #0f172a;">{{ optional($detalle->producto)->nombre ?? 'Producto Eliminado' }}</strong>
                </td>
                <td class="text-right">
                    Bs. {{ number_format($detalle->precio_unitario, 2) }}
                </td>
                <td class="text-right" style="font-weight: 600;">
                    Bs. {{ number_format($detalle->subtotal, 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right">
                    TOTAL A PAGAR:
                </td>
                <td class="text-right total-highlight">
                    Bs. {{ number_format($venta->total, 2) }}
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- ======================================================== -->
    <!-- PIE DE PÁGINA INSTITUCIONAL                              -->
    <!-- ======================================================== -->
    <div class="footer">
        <div class="footer-message">
            {{ !empty($empresa['pie']) ? $empresa['pie'] : '¡Gracias por su preferencia!' }}
        </div>
        <div class="footer-disclaimer">
            Esta nota de venta es un comprobante de entrega y control interno. No es válida como factura fiscal.
        </div>
    </div>

</body>
</html>
