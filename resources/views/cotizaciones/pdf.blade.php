<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización {{ $cotizacion->numero_cotizacion }}</title>
    <style>
        @page {
            margin: 26px 32px;
        }
        body {
            font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #1e293b;
            line-height: 1.4;
            background: #ffffff;
        }

        /* Encabezado */
        .header-table {
            width: 100%;
            border-bottom: 2.5px solid #0284c7;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .logo-col {
            width: 56%;
            vertical-align: middle;
        }
        .company-info-col {
            width: 44%;
            text-align: right;
            vertical-align: middle;
        }
        .logo-img {
            max-height: 76px;
            max-width: 220px;
            display: block;
            margin-bottom: 6px;
        }
        .company-name {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 2px;
        }
        .company-meta {
            font-size: 10.5px;
            color: #64748b;
            line-height: 1.35;
        }

        .doc-title {
            font-size: 22px;
            font-weight: 800;
            color: #0284c7;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin: 0 0 3px 0;
        }
        .doc-number {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .doc-date {
            font-size: 10.5px;
            color: #475569;
            line-height: 1.4;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-top: 4px;
        }
        .badge-cliente { background-color: #e0f2fe; color: #0369a1; }
        .badge-tienda { background-color: #ede9fe; color: #6d28d9; }
        .badge-estado { background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }

        /* Tarjeta Destinatario */
        .info-card {
            width: 100%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 16px;
        }
        .info-card-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-card-table td {
            padding: 2.5px 4px;
            font-size: 11px;
            vertical-align: top;
        }
        .info-label {
            color: #64748b;
            font-weight: 700;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.35px;
            display: block;
        }
        .info-value {
            color: #0f172a;
            font-weight: 600;
        }

        /* Tabla de Ítems */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .items-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.45px;
            padding: 7px 8px;
            border: 1px solid #0f172a;
        }
        .items-table td {
            border: 1px solid #e2e8f0;
            padding: 7px 8px;
            font-size: 11px;
            vertical-align: middle;
        }
        .items-table tbody tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .text-left { text-align: left !important; }

        .tag-externo {
            display: inline-block;
            font-size: 8.5px;
            font-weight: 700;
            padding: 1px 5px;
            border-radius: 3px;
            background: #fef3c7;
            color: #92400e;
            margin-left: 4px;
            text-transform: uppercase;
        }

        /* Resumen y Totales */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .summary-notes-col {
            width: 56%;
            vertical-align: top;
            padding-right: 16px;
        }
        .summary-totals-col {
            width: 44%;
            vertical-align: top;
        }
        .totals-box {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }
        .totals-box td {
            padding: 6px 10px;
            font-size: 11.5px;
            border-bottom: 1px solid #e2e8f0;
        }
        .totals-box tr.total-final td {
            background-color: #f0f9ff;
            border-top: 2px solid #0284c7;
            border-bottom: none;
            font-size: 14px;
            font-weight: 800;
            color: #0369a1;
            padding: 9px 10px;
        }

        .equiv-box {
            margin-top: 8px;
            padding: 7px 10px;
            background-color: #f8fafc;
            border: 1px solid #bae6fd;
            border-left: 3.5px solid #0284c7;
            border-radius: 4px;
            font-size: 10.5px;
            color: #0c4a6e;
        }

        .note-box {
            margin-bottom: 8px;
            padding: 8px 10px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            font-size: 10.5px;
            color: #334155;
        }
        .note-title {
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            color: #475569;
            margin-bottom: 3px;
        }

        /* Pie */
        .footer {
            margin-top: 26px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }
        .footer-message {
            font-weight: 700;
            color: #0f172a;
            font-size: 11.5px;
            margin-bottom: 3px;
        }
        .footer-disclaimer {
            font-size: 9.5px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    @php
        $sim = $cotizacion->simbolo_moneda ?: ($cotizacion->moneda === 'PEN' ? 'S/' : 'Bs.');
        $equiv = $cotizacion->mostrar_equivalente ? $cotizacion->convertirAOtraMoneda() : null;
    @endphp

    <!-- ENCABEZADO -->
    <table class="header-table">
        <tr>
            <td class="logo-col">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" class="logo-img" alt="{{ $empresa['nombre'] ?? 'Logo' }}">
                @elseif(file_exists(public_path('img/logo_white.png')))
                    <img src="{{ public_path('img/logo_white.png') }}" class="logo-img" alt="{{ $empresa['nombre'] ?? 'Logo' }}">
                @endif
                <div class="company-name">{{ $empresa['nombre'] ?? 'Distribuidora Guadalupe' }}</div>
                <div class="company-meta">
                    @if(!empty($empresa['nit']))
                        <div>NIT / RUC: {{ $empresa['nit'] }}</div>
                    @endif
                    @if(!empty($empresa['direccion']))
                        <div>{{ $empresa['direccion'] }}</div>
                    @endif
                    @if(!empty($empresa['telefono']))
                        <div>Tel / WhatsApp: {{ $empresa['telefono'] }}</div>
                    @endif
                </div>
            </td>
            <td class="company-info-col">
                <div class="doc-title">Cotización</div>
                <div class="doc-number">Nº {{ $cotizacion->numero_cotizacion }}</div>
                <div class="doc-date">
                    <strong>Emisión:</strong> {{ $cotizacion->fecha ? $cotizacion->fecha->format('d/m/Y H:i') : now()->format('d/m/Y') }}<br>
                    <strong>Válida hasta:</strong> {{ $cotizacion->valida_hasta ? $cotizacion->valida_hasta->format('d/m/Y') : 'Sin vencimiento' }}<br>
                    <strong>Moneda:</strong> {{ $cotizacion->moneda === 'PEN' ? 'Soles (S/)' : 'Bolivianos (Bs.)' }}
                </div>
                <div>
                    <span class="badge {{ $cotizacion->tipo_cotizacion === 'tienda_externa' ? 'badge-tienda' : 'badge-cliente' }}">
                        {{ $cotizacion->tipo_cotizacion === 'tienda_externa' ? 'Tienda Externa' : 'Para Cliente' }}
                    </span>
                    <span class="badge badge-estado">{{ strtoupper($cotizacion->estado) }}</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- DATOS DEL DESTINATARIO -->
    <div class="info-card">
        <table class="info-card-table">
            <tr>
                <td style="width: 38%;">
                    <span class="info-label">{{ $cotizacion->tipo_cotizacion === 'tienda_externa' ? 'Contacto / Responsable' : 'Cliente / Destinatario' }}</span>
                    <span class="info-value">{{ $cotizacion->destinatario_nombre }}</span>
                </td>
                <td style="width: 34%;">
                    <span class="info-label">{{ $cotizacion->tipo_cotizacion === 'tienda_externa' ? 'Tienda / Razón Social' : 'Empresa / Referencia' }}</span>
                    <span class="info-value">{{ $cotizacion->destinatario_empresa ?: '—' }}</span>
                </td>
                <td style="width: 28%;">
                    <span class="info-label">NIT / RUC / CI</span>
                    <span class="info-value">{{ $cotizacion->destinatario_nit ?: '—' }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding-top: 6px;">
                    <span class="info-label">Teléfono / WhatsApp</span>
                    <span class="info-value">{{ $cotizacion->destinatario_telefono ?: '—' }}</span>
                </td>
                <td style="padding-top: 6px;">
                    <span class="info-label">Dirección / Entrega</span>
                    <span class="info-value">{{ $cotizacion->destinatario_direccion ?: '—' }}</span>
                </td>
                <td style="padding-top: 6px;">
                    <span class="info-label">Cotizado por</span>
                    <span class="info-value">{{ optional($cotizacion->usuario)->name ?? 'Administración' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- DETALLE DE PRODUCTOS -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">#</th>
                <th class="text-left" style="width: 14%;">Código</th>
                <th class="text-left" style="width: 40%;">Descripción del Producto / Ítem</th>
                <th class="text-center" style="width: 10%;">Unidad</th>
                <th class="text-center" style="width: 9%;">Cant.</th>
                <th class="text-right" style="width: 11%;">P. Unit.</th>
                <th class="text-right" style="width: 11%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cotizacion->detalles as $idx => $det)
                @php
                    $cantFmt = fmod((float) $det->cantidad, 1) == 0
                        ? number_format($det->cantidad, 0)
                        : number_format($det->cantidad, 2);
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-left" style="color:#475569;">{{ $det->codigo_producto ?: '—' }}</td>
                    <td class="text-left">
                        <strong style="color:#0f172a;">{{ $det->nombre_producto }}</strong>
                        @if($det->es_externo)
                            <span class="tag-externo">Externo</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $det->unidad_medida }}</td>
                    <td class="text-center"><strong>{{ $cantFmt }}</strong></td>
                    <td class="text-right">{{ $sim }} {{ number_format($det->precio_unitario, 2) }}</td>
                    <td class="text-right"><strong>{{ $sim }} {{ number_format($det->subtotal, 2) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- CONDICIONES Y TOTALES -->
    <table class="summary-table">
        <tr>
            <td class="summary-notes-col">
                @if(!empty($cotizacion->condiciones))
                    <div class="note-box">
                        <div class="note-title">Condiciones Comerciales y Validez</div>
                        <div style="white-space: pre-line;">{{ $cotizacion->condiciones }}</div>
                    </div>
                @endif
                @if(!empty($cotizacion->observaciones))
                    <div class="note-box">
                        <div class="note-title">Observaciones</div>
                        <div style="white-space: pre-line;">{{ $cotizacion->observaciones }}</div>
                    </div>
                @endif
            </td>
            <td class="summary-totals-col">
                <table class="totals-box">
                    <tr>
                        <td style="color:#475569; font-weight:600;">Subtotal</td>
                        <td class="text-right" style="font-weight:700;">{{ $sim }} {{ number_format($cotizacion->subtotal, 2) }}</td>
                    </tr>
                    @if((float) $cotizacion->descuento > 0)
                        <tr>
                            <td style="color:#dc2626; font-weight:600;">Descuento aplicado</td>
                            <td class="text-right" style="color:#dc2626; font-weight:700;">- {{ $sim }} {{ number_format($cotizacion->descuento, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="total-final">
                        <td>TOTAL COTIZADO</td>
                        <td class="text-right">{{ $sim }} {{ number_format($cotizacion->total, 2) }}</td>
                    </tr>
                </table>

                @if($equiv !== null)
                    <div class="equiv-box">
                        <strong>Equivalente ref. en {{ $cotizacion->moneda === 'PEN' ? 'Bolivianos' : 'Soles' }}:</strong>
                        <span style="float:right; font-weight:800;">{{ $cotizacion->simboloOtraMoneda() }} {{ number_format($equiv, 2) }}</span>
                        <div style="clear:both; font-size:9.5px; color:#0369a1; margin-top:2px;">
                            Tipo de cambio referencial aplicado: 1 S/ = Bs. {{ number_format($cotizacion->tasa_cambio_aplicada, 2) }}
                        </div>
                    </div>
                @endif
            </td>
        </tr>
    </table>

    <!-- PIE DE PÁGINA -->
    <div class="footer">
        <div class="footer-message">{{ $empresa['pie'] ?? '¡Gracias por su preferencia!' }}</div>
        <div class="footer-disclaimer">
            Precios sujetos a disponibilidad de stock y vigencia hasta la fecha indicada. Documento informativo de cotización — no implica salida de inventario ni comprobante tributario.
        </div>
    </div>
</body>
</html>
