<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CodigoProductoService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class ImportarProductosExcel extends Command
{
    protected $signature = 'importar:productos-excel
                            {archivo : Ruta al archivo Excel}
                            {--categoria=1 : ID de categoría a asignar}
                            {--unidad=4 : ID de unidad a asignar (4=Pieza)}
                            {--marca=2 : ID de marca a asignar (2=KRONA)}
                            {--precio=0 : Precio de venta por defecto}
                            {--costo=0 : Costo por defecto}
                            {--stock-minimo=5 : Stock mínimo por defecto}
                            {--actualizar : Si el producto ya existe (por nombre), actualiza su stock}';

    protected $description = 'Importa productos desde un Excel con columnas: CODIGO, PRODUCTO, CANTIDAD (stock actual = TOTAL EN TIENDA)';

    public function handle(): int
    {
        $archivo = $this->argument('archivo');

        if (!file_exists($archivo)) {
            $this->error("El archivo no existe: $archivo");
            return self::FAILURE;
        }

        $this->info("Leyendo archivo: $archivo");

        try {
            $spreadsheet = IOFactory::load($archivo);
        } catch (\Exception $e) {
            $this->error("Error al leer el Excel: " . $e->getMessage());
            return self::FAILURE;
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Buscar la fila de cabecera (que tenga "CODIGO" y "PRODUCTO")
        $headerRow = null;
        $headerIndex = 0;
        foreach ($rows as $i => $row) {
            foreach ($row as $cell) {
                if (is_string($cell) && strtoupper(trim($cell)) === 'CODIGO') {
                    $headerRow = $row;
                    $headerIndex = $i;
                    break 2;
                }
            }
        }

        if ($headerRow === null) {
            $this->error("No se encontró la fila de cabecera con 'CODIGO'.");
            return self::FAILURE;
        }

        // Mapear columnas por nombre
        $colMap = [];
        foreach ($headerRow as $j => $cell) {
            $key = strtoupper(trim((string)$cell));
            if ($key !== '') {
                $colMap[$key] = $j;
            }
        }

        $this->line("Columnas encontradas: " . implode(', ', array_keys($colMap)));

        // Verificar columnas necesarias
        $required = ['CODIGO', 'PRODUCTO'];
        foreach ($required as $col) {
            if (!isset($colMap[$col])) {
                $this->error("Columna requerida no encontrada: $col");
                return self::FAILURE;
            }
        }

        // Columna de stock: preferir "TOTAL EN TIENDA", luego "CANTIDAD"
        $stockCol = null;
        if (isset($colMap['TOTAL EN TIENDA'])) {
            $stockCol = $colMap['TOTAL EN TIENDA'];
            $this->info("Usando columna 'TOTAL EN TIENDA' como stock actual.");
        } elseif (isset($colMap['CANTIDAD'])) {
            $stockCol = $colMap['CANTIDAD'];
            $this->info("Usando columna 'CANTIDAD' como stock actual.");
        }

        $categoriaId   = (int) $this->option('categoria');
        $unidadId      = (int) $this->option('unidad');
        $marcaId       = (int) $this->option('marca');
        $precio        = (float) $this->option('precio');
        $costo         = (float) $this->option('costo');
        $stockMinimo   = (int) $this->option('stock-minimo');
        $actualizar    = $this->option('actualizar');

        $importados  = 0;
        $actualizados = 0;
        $omitidos    = 0;
        $errores     = [];

        // Datos de los productos del Excel (desde la fila siguiente al header)
        $productRows = array_slice($rows, $headerIndex + 1);

        $this->newLine();
        $this->info("Procesando " . count($productRows) . " filas...");
        $this->newLine();

        $headers = ['#Excel', 'Nombre', 'Stock', 'Acción'];
        $tableRows = [];

        DB::beginTransaction();
        try {
            foreach ($productRows as $row) {
                $codigoExcel = trim((string)($row[$colMap['CODIGO']] ?? ''));
                $nombre      = trim((string)($row[$colMap['PRODUCTO']] ?? ''));

                // Saltar filas vacías
                if ($nombre === '' || $nombre === 'null') {
                    continue;
                }

                // Generamos un código estandarizado basado en el nombre del producto
                $codigoInterno = CodigoProductoService::generarCodigo($nombre);

                $stock = 0;
                if ($stockCol !== null) {
                    $val = $row[$stockCol] ?? null;
                    $stock = (int)(is_numeric($val) ? $val : 0);
                }

                // Verificar si ya existe por código interno o nombre
                $existente = DB::table('productos')
                    ->where('codigo', $codigoInterno)
                    ->orWhere('nombre', $nombre)
                    ->first();

                if ($existente) {
                    if ($actualizar) {
                        DB::table('productos')
                            ->where('id', $existente->id)
                            ->update(['stock' => $stock]);

                        // Registrar movimiento de ajuste
                        DB::table('movimientos')->insert([
                            'id_usuario'  => 1,
                            'id_producto' => $existente->id,
                            'tipo'        => 'Agregar',
                            'cantidad'    => $stock,
                            'motivo'      => 'Importación Excel KRONA - stock inicial',
                            'comentario'  => "Stock actualizado desde Excel. Anterior: {$existente->stock} → Nuevo: {$stock}",
                            'fecha'       => now(),
                        ]);

                        $tableRows[] = [$codigoExcel, $nombre, $stock, '✔ Actualizado'];
                        $actualizados++;
                    } else {
                        $tableRows[] = [$codigoExcel, $nombre, $stock, '– Omitido (ya existe)'];
                        $omitidos++;
                    }
                    continue;
                }

                // Insertar nuevo producto
                $productoId = DB::table('productos')->insertGetId([
                    'id_categoria'  => $categoriaId,
                    'id_unidad'     => $unidadId,
                    'id_marca'      => $marcaId,
                    'codigo'        => $codigoInterno,
                    'nombre'        => $nombre,
                    'precio_venta'  => $precio,
                    'costo'         => $costo,
                    'stock'         => $stock,
                    'stock_minimo'  => $stockMinimo,
                    'descripcion'   => "Importado desde Excel KRONA INV OCT 2026. Código original: $codigoExcel",
                ]);

                // Registrar movimiento inicial si tiene stock
                if ($stock > 0) {
                    DB::table('movimientos')->insert([
                        'id_usuario'  => 1,
                        'id_producto' => $productoId,
                        'tipo'        => 'Agregar',
                        'cantidad'    => $stock,
                        'motivo'      => 'Stock inicial - Importación Excel KRONA',
                        'comentario'  => "Stock cargado desde Excel de inventario KRONA OCT 2026",
                        'fecha'       => now(),
                    ]);
                }

                $tableRows[] = [$codigoExcel, $nombre, $stock, '✔ Importado'];
                $importados++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error durante la importación: " . $e->getMessage());
            return self::FAILURE;
        }

        $this->table($headers, $tableRows);
        $this->newLine();

        $this->info("════════════════════════════════");
        $this->info("  RESUMEN DE IMPORTACIÓN");
        $this->info("════════════════════════════════");
        $this->line("  ✔ Importados nuevos : <fg=green>$importados</>");
        $this->line("  ✔ Actualizados      : <fg=cyan>$actualizados</>");
        $this->line("  – Omitidos          : <fg=yellow>$omitidos</>");
        $this->info("════════════════════════════════");

        return self::SUCCESS;
    }
}
