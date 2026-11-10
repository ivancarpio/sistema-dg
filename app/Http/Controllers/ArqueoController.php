<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Arqueo;
use App\Models\Venta;
use App\Models\User;
use App\Exports\ArqueoExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ArqueoController extends Controller
{
    // ============================================================
    // 1. LISTADO DE ARQUEOS (Pantalla principal)
    // ============================================================
    public function index(Request $request)
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes    = Carbon::now()->endOfMonth();

        $arqueosEsteMes = Arqueo::whereBetween('created_at', [$inicioMes, $finMes])->count();
        $totalArqueadoMes = Arqueo::whereBetween('created_at', [$inicioMes, $finMes])->sum('total_general');

        // Ventas no arqueadas y no anuladas
        $ventasSinArquear = Venta::whereNull('arqueo_id')
            ->where('estado', '!=', 'Anulada')
            ->count();

        // El monto sin arquear es el dinero real cobrado (monto_pagado)
        $montoSinArquear = Venta::whereNull('arqueo_id')
            ->where('estado', '!=', 'Anulada')
            ->sum(DB::raw('COALESCE(monto_pagado, total)'));

        $query = Arqueo::with('usuario')->latest();

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('id', 'LIKE', "%{$buscar}%")
                  ->orWhereHas('usuario', function ($qu) use ($buscar) {
                      $qu->where('name', 'LIKE', "%{$buscar}%");
                  });
            });
        }

        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        }

        $arqueos = $query->paginate(20)->withQueryString();
        $usuarios = User::all();

        return view('arqueos.index', compact(
            'arqueos',
            'arqueosEsteMes',
            'totalArqueadoMes',
            'ventasSinArquear',
            'montoSinArquear',
            'usuarios'
        ));
    }

    // ============================================================
    // 2. FORMULARIO DE CREACIÓN (ver ventas pendientes)
    // ============================================================
    public function create(Request $request)
    {
        $query = Venta::with(['usuario', 'detalles.producto'])
            ->whereNull('arqueo_id')
            ->where('estado', '!=', 'Anulada');

        $periodo = $request->input('periodo', 'hoy');
        switch ($periodo) {
            case 'semana':
                $query->whereBetween('fecha', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'mes':
                $query->whereBetween('fecha', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                break;
            case 'hoy':
            default:
                $query->whereDate('fecha', Carbon::today());
                break;
        }

        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        if ($request->filled('cajero')) {
            $query->where('id_usuario', $request->cajero);
        }

        $ventasPendientes = $query->orderBy('fecha', 'desc')->get();

        // Calcular dinero real cobrado por canal
        $totalEfectivo = 0;
        $totalQr = 0;

        foreach ($ventasPendientes as $v) {
            $montoCobrado = $v->monto_pagado !== null ? floatval($v->monto_pagado) : floatval($v->total);
            
            if ($v->metodo_pago === 'Efectivo') {
                $totalEfectivo += $montoCobrado;
            } elseif ($v->metodo_pago === 'QR') {
                $totalQr += $montoCobrado;
            } elseif ($v->metodo_pago === 'Por Pagar') {
                if ($v->metodo_adelanto === 'QR') {
                    $totalQr += $montoCobrado;
                } else {
                    $totalEfectivo += $montoCobrado;
                }
            }
        }

        $resumen = [
            'efectivo' => $totalEfectivo,
            'qr'       => $totalQr,
            'total'    => $totalEfectivo + $totalQr,
            'cantidad' => $ventasPendientes->count(),
        ];

        $usuarios = User::all();

        return view('arqueos.create', compact('ventasPendientes', 'resumen', 'usuarios', 'periodo'));
    }

    // ============================================================
    // 3. GUARDAR EL ARQUEO
    // ============================================================
    public function store(Request $request)
    {
        $request->validate([
            'ventas_seleccionadas'   => 'required|array|min:1',
            'ventas_seleccionadas.*' => 'integer|exists:ventas,id',
        ], [
            'ventas_seleccionadas.required' => 'Debes seleccionar al menos una venta para crear el arqueo.',
            'ventas_seleccionadas.min'      => 'Selecciona al menos 1 venta.',
        ]);

        DB::beginTransaction();
        try {
            $ids = $request->ventas_seleccionadas;

            $ventas = Venta::whereIn('id', $ids)
                ->whereNull('arqueo_id')
                ->where('estado', '!=', 'Anulada')
                ->get();

            if ($ventas->isEmpty()) {
                return back()->with('error', 'Las ventas seleccionadas no son válidas o ya fueron arqueadas.');
            }

            $totalEfectivo = 0;
            $totalQr = 0;

            foreach ($ventas as $v) {
                $montoCobrado = $v->monto_pagado !== null ? floatval($v->monto_pagado) : floatval($v->total);
                
                if ($v->metodo_pago === 'Efectivo') {
                    $totalEfectivo += $montoCobrado;
                } elseif ($v->metodo_pago === 'QR') {
                    $totalQr += $montoCobrado;
                } elseif ($v->metodo_pago === 'Por Pagar') {
                    if ($v->metodo_adelanto === 'QR') {
                        $totalQr += $montoCobrado;
                    } else {
                        $totalEfectivo += $montoCobrado;
                    }
                }
            }

            $totalGeneral = $totalEfectivo + $totalQr;

            $arqueo = Arqueo::create([
                'id_usuario'     => Auth::id() ?? 1,
                'total_efectivo' => $totalEfectivo,
                'total_qr'       => $totalQr,
                'total_general'  => $totalGeneral,
                'cantidad_ventas'=> $ventas->count(),
                'observaciones'  => $request->observaciones,
            ]);

            foreach ($ventas as $venta) {
                $venta->arqueo_id = $arqueo->id;
                $venta->save();

                DB::table('arqueo_venta')->insert([
                    'arqueo_id' => $arqueo->id,
                    'venta_id'  => $venta->id,
                ]);
            }

            DB::commit();
            return redirect('/arqueos/' . $arqueo->id)
                ->with('success', '¡Arqueo de Caja #' . $arqueo->id . ' creado correctamente!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear el arqueo: ' . $e->getMessage());
        }
    }

    // ============================================================
    // 4. DETALLE DE UN ARQUEO
    // ============================================================
    public function show($id)
    {
        $arqueo = Arqueo::with(['usuario', 'ventas.usuario', 'ventas.detalles.producto'])->findOrFail($id);
        return view('arqueos.show', compact('arqueo'));
    }

    // ============================================================
    // 5. EXPORTAR ARQUEO A EXCEL
    // ============================================================
    public function exportarExcel($id)
    {
        $arqueo = Arqueo::with(['usuario', 'ventas.usuario', 'ventas.detalles.producto'])->findOrFail($id);
        $nombreArchivo = 'Arqueo_Caja_#' . $arqueo->id . '_' . $arqueo->created_at->format('Ymd_His') . '.xlsx';
        return Excel::download(new ArqueoExport($arqueo), $nombreArchivo);
    }
}
