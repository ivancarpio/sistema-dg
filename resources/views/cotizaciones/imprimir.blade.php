<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Cotización {{ $cotizacion->numero_cotizacion }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #e2e8f0;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 0;
        }
        .print-toolbar {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #fff;
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.25);
        }
        .paper-sheet {
            max-width: 850px;
            margin: 28px auto 40px auto;
            background: #ffffff;
            padding: 36px 42px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
        }
        .doc-header {
            border-bottom: 3px solid #0284c7;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .logo-empresa {
            max-height: 82px;
            max-width: 240px;
            object-fit: contain;
        }
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 20px;
        }
        .info-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #64748b;
            font-weight: 700;
            display: block;
        }
        .info-val {
            font-size: 13.5px;
            font-weight: 600;
            color: #0f172a;
        }
        .table-cotizacion th {
            background-color: #0f172a !important;
            color: #ffffff !important;
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-color: #0f172a !important;
        }
        .table-cotizacion td {
            font-size: 13px;
            vertical-align: middle;
        }
        .total-highlight-box {
            background-color: #f0f9ff;
            border: 1.5px solid #0284c7;
            border-radius: 8px;
            overflow: hidden;
        }
        @media print {
            @page {
                size: letter portrait;
                margin: 12mm 14mm;
            }
            body {
                background: #ffffff !important;
            }
            .no-print {
                display: none !important;
            }
            .paper-sheet {
                margin: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body>
    @php
        $sim = $cotizacion->simbolo_moneda ?: ($cotizacion->moneda === 'PEN' ? 'S/' : 'Bs.');
        $equiv = $cotizacion->mostrar_equivalente ? $cotizacion->convertirAOtraMoneda() : null;
        $logoVer = \App\Models\Configuracion::get('logo_version', '1');
        $mensajeWa = $cotizacion->construirMensajeWhatsApp($empresa);
        $telWa = $cotizacion->telefonoWhatsApp();
    @endphp

    <!-- BARRA DE ACCIONES NO IMPRIMIBLE -->
    <div class="print-toolbar no-print">
        <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center gap-2" style="max-width: 900px;">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('cotizaciones.index') }}" class="btn btn-outline-light btn-sm fw-semibold">
                    <i class="bi bi-arrow-left me-1"></i> Volver a Cotizaciones
                </a>
                <span class="text-white-50 small ms-2 d-none d-sm-inline">
                    Vista previa de impresión • {{ $cotizacion->numero_cotizacion }}
                </span>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if($telWa)
                    <a href="https://wa.me/{{ $telWa }}?text={{ urlencode($mensajeWa) }}" target="_blank" class="btn btn-success btn-sm fw-semibold">
                        <i class="bi bi-whatsapp me-1"></i> Enviar por WhatsApp
                    </a>
                @endif
                <a href="{{ route('cotizaciones.pdf', $cotizacion->id) }}" target="_blank" class="btn btn-info btn-sm text-white fw-semibold">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Descargar PDF
                </a>
                <button type="button" onclick="window.print()" class="btn btn-primary btn-sm fw-bold px-3">
                    <i class="bi bi-printer-fill me-1"></i> Imprimir
                </button>
            </div>
        </div>
    </div>

    <!-- HOJA IMPRIMIBLE -->
    <div class="paper-sheet">
        <!-- Encabezado -->
        <div class="row align-items-center doc-header">
            <div class="col-7">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" class="logo-empresa mb-2" alt="{{ $empresa['nombre'] ?? 'Logo' }}">
                @elseif(file_exists(public_path('img/logo_white.png')))
                    <img src="{{ asset('img/logo_white.png') }}?v={{ $logoVer }}" class="logo-empresa mb-2" alt="{{ $empresa['nombre'] ?? 'Logo' }}">
                @endif
                <div class="fw-bold fs-5 text-dark">{{ $empresa['nombre'] ?? 'Distribuidora Guadalupe' }}</div>
                <div class="small text-muted">
                    @if(!empty($empresa['nit'])) <div>NIT / RUC: {{ $empresa['nit'] }}</div> @endif
                    @if(!empty($empresa['direccion'])) <div>{{ $empresa['direccion'] }}</div> @endif
                    @if(!empty($empresa['telefono'])) <div>Tel / WhatsApp: {{ $empresa['telefono'] }}</div> @endif
                </div>
            </div>
            <div class="col-5 text-end">
                <div class="text-uppercase fw-extrabold fs-3" style="color:#0284c7; letter-spacing: 0.6px;">Cotización</div>
                <div class="fs-5 fw-bold text-dark mb-1">Nº {{ $cotizacion->numero_cotizacion }}</div>
                <div class="small text-secondary">
                    <div><strong>Emisión:</strong> {{ $cotizacion->fecha ? $cotizacion->fecha->format('d/m/Y H:i') : now()->format('d/m/Y') }}</div>
                    <div><strong>Válida hasta:</strong> {{ $cotizacion->valida_hasta ? $cotizacion->valida_hasta->format('d/m/Y') : 'Sin vencimiento' }}</div>
                    <div><strong>Moneda:</strong> {{ $cotizacion->moneda === 'PEN' ? 'Soles (S/)' : 'Bolivianos (Bs.)' }}</div>
                </div>
                <div class="mt-2">
                    <span class="badge {{ $cotizacion->tipo_cotizacion === 'tienda_externa' ? 'bg-purple text-white' : 'bg-info-subtle text-info-emphasis border border-info-subtle' }}"
                          style="{{ $cotizacion->tipo_cotizacion === 'tienda_externa' ? 'background-color:#7c3aed;' : '' }}">
                        {{ $cotizacion->tipo_cotizacion === 'tienda_externa' ? 'Tienda Externa' : 'Para Cliente' }}
                    </span>
                    <span class="badge bg-light text-dark border ms-1">{{ strtoupper($cotizacion->estado) }}</span>
                </div>
            </div>
        </div>

        <!-- Datos Destinatario -->
        <div class="info-card">
            <div class="row g-3">
                <div class="col-md-5 col-6">
                    <span class="info-label">{{ $cotizacion->tipo_cotizacion === 'tienda_externa' ? 'Contacto / Responsable' : 'Cliente / Destinatario' }}</span>
                    <span class="info-val">{{ $cotizacion->destinatario_nombre }}</span>
                </div>
                <div class="col-md-4 col-6">
                    <span class="info-label">{{ $cotizacion->tipo_cotizacion === 'tienda_externa' ? 'Tienda / Razón Social' : 'Empresa / Referencia' }}</span>
                    <span class="info-val">{{ $cotizacion->destinatario_empresa ?: '—' }}</span>
                </div>
                <div class="col-md-3 col-6">
                    <span class="info-label">NIT / RUC / CI</span>
                    <span class="info-val">{{ $cotizacion->destinatario_nit ?: '—' }}</span>
                </div>
                <div class="col-md-5 col-6">
                    <span class="info-label">Teléfono / WhatsApp</span>
                    <span class="info-val">{{ $cotizacion->destinatario_telefono ?: '—' }}</span>
                </div>
                <div class="col-md-4 col-6">
                    <span class="info-label">Dirección / Entrega</span>
                    <span class="info-val">{{ $cotizacion->destinatario_direccion ?: '—' }}</span>
                </div>
                <div class="col-md-3 col-6">
                    <span class="info-label">Cotizado por</span>
                    <span class="info-val">{{ optional($cotizacion->usuario)->name ?? 'Administración' }}</span>
                </div>
            </div>
        </div>

        <!-- Tabla Ítems -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-striped table-cotizacion mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">#</th>
                        <th style="width: 14%;">Código</th>
                        <th>Descripción del Producto / Ítem</th>
                        <th class="text-center" style="width: 11%;">Unidad</th>
                        <th class="text-center" style="width: 9%;">Cant.</th>
                        <th class="text-end" style="width: 12%;">P. Unit.</th>
                        <th class="text-end" style="width: 13%;">Subtotal</th>
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
                            <td class="text-secondary">{{ $det->codigo_producto ?: '—' }}</td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $det->nombre_producto }}</span>
                                @if($det->es_externo)
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle ms-1" style="font-size:10px;">Externo</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $det->unidad_medida }}</td>
                            <td class="text-center fw-bold">{{ $cantFmt }}</td>
                            <td class="text-end">{{ $sim }} {{ number_format($det->precio_unitario, 2) }}</td>
                            <td class="text-end fw-bold">{{ $sim }} {{ number_format($det->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Notas y Totales -->
        <div class="row g-4">
            <div class="col-md-7">
                @if(!empty($cotizacion->condiciones))
                    <div class="p-3 bg-light border rounded mb-3">
                        <div class="info-label mb-1">Condiciones Comerciales y Validez</div>
                        <div class="small text-dark" style="white-space: pre-line;">{{ $cotizacion->condiciones }}</div>
                    </div>
                @endif
                @if(!empty($cotizacion->observaciones))
                    <div class="p-3 bg-light border rounded">
                        <div class="info-label mb-1">Observaciones</div>
                        <div class="small text-dark" style="white-space: pre-line;">{{ $cotizacion->observaciones }}</div>
                    </div>
                @endif
            </div>
            <div class="col-md-5">
                <div class="total-highlight-box">
                    <table class="table table-sm mb-0">
                        <tbody>
                            <tr>
                                <td class="ps-3 py-2 text-muted fw-semibold">Subtotal</td>
                                <td class="pe-3 py-2 text-end fw-bold">{{ $sim }} {{ number_format($cotizacion->subtotal, 2) }}</td>
                            </tr>
                            @if((float) $cotizacion->descuento > 0)
                                <tr>
                                    <td class="ps-3 py-2 text-danger fw-semibold">Descuento aplicado</td>
                                    <td class="pe-3 py-2 text-end text-danger fw-bold">- {{ $sim }} {{ number_format($cotizacion->descuento, 2) }}</td>
                                </tr>
                            @endif
                            <tr style="background-color:#e0f2fe;">
                                <td class="ps-3 py-3 fw-extrabold fs-6" style="color:#0369a1;">TOTAL COTIZADO</td>
                                <td class="pe-3 py-3 text-end fw-extrabold fs-5" style="color:#0369a1;">{{ $sim }} {{ number_format($cotizacion->total, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if($equiv !== null)
                    <div class="mt-2 p-2 px-3 rounded border" style="background-color:#f8fafc; border-color:#bae6fd !important; border-left:4px solid #0284c7 !important;">
                        <div class="d-flex justify-content-between align-items-center small fw-bold text-dark">
                            <span>Equivalente ref. ({{ $cotizacion->moneda === 'PEN' ? 'Bolivianos' : 'Soles' }}):</span>
                            <span style="color:#0369a1;">{{ $cotizacion->simboloOtraMoneda() }} {{ number_format($equiv, 2) }}</span>
                        </div>
                        <div class="text-muted" style="font-size:11px;">
                            Tasa referencial: 1 S/ = Bs. {{ number_format($cotizacion->tasa_cambio_aplicada, 2) }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pie -->
        <div class="mt-5 pt-3 border-top text-center">
            <div class="fw-bold text-dark mb-1">{{ $empresa['pie'] ?? '¡Gracias por su preferencia!' }}</div>
            <div class="text-muted" style="font-size:11px;">
                Precios sujetos a disponibilidad de stock y vigencia hasta la fecha indicada. Documento informativo de cotización.
            </div>
        </div>
    </div>

    <script>
        if (new URLSearchParams(window.location.search).get('autoprint') === '1') {
            window.addEventListener('load', function () {
                setTimeout(function () { window.print(); }, 350);
            });
        }
    </script>
</body>
</html>
