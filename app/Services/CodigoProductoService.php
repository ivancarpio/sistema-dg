<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class CodigoProductoService
{
    /**
     * Mapeo de prefijos compuestos comunes en ferretería
     */
    private static array $prefijosCompuestos = [
        'UNION PATENTE'               => 'UNP',
        'TAPON HEMBRA'                => 'TPH',
        'TAPON MACHO'                 => 'TPM',
        'RED BUJE'                    => 'RDB',
        'REDUCCION BUJE'              => 'RDB',
        'RED COPA'                    => 'RDC',
        'REDUCCION COPA'              => 'RDC',
        'CAÑERIA PVC'                 => 'CAN-PVC',
        'CANERIA PVC'                 => 'CAN-PVC',
        'CAÑERIA'                     => 'CAN',
        'CANERIA'                     => 'CAN',
        'CABLE ALAMBRE'               => 'CAB-A',
        'CABLE FLEXIBLE'              => 'CAB-F',
        'CABLE CORDON COPLAS BIPOLAR' => 'CAB-CCB',
        'CABLE CORDON COPLAS'         => 'CAB-CC',
        'CABLE 7 HILOS'               => 'CAB-7H',
        'CABLE'                       => 'CAB',
        'CARPA'                       => 'CRP',
        'CHAPA PERFIL ESTRECHO INOX'  => 'CHP-PE-INOX',
        'CHAPA PERFIL ESTRECHO'       => 'CHP-PE',
        'CHAPA CLASSIC'               => 'CHP-CL',
        'CHAPA'                       => 'CHP',
        'CLAVO'                       => 'CLV',
        'CODO ROSCA'                  => 'COD-R',
        'CODO'                        => 'COD',
        'TEE ROSCA'                   => 'TEE-R',
        'TEE'                         => 'TEE',
        'UNION'                       => 'UNI',
        'NIPLE'                       => 'NIP',
        'CUPLA'                       => 'CUP',
        'BRIDA'                       => 'BRI',
        'TAPON'                       => 'TAP',
        'VALVULA'                     => 'VAL',
        'LLAVE'                       => 'LLV',
        'TUBO'                        => 'TUB',
        'PEGAMENTO'                   => 'PEG',
        'CINTA'                       => 'CNT',
        'ALAMBRE'                     => 'ALM',
        'TORNILLO'                    => 'TOR',
        'PERNO'                       => 'PER',
        'TUERCA'                      => 'TUE',
        'ARANDELA'                    => 'ARA',
    ];

    /**
     * Palabras secundarias/marcas/adjetivos a ignorar en el código
     */
    private static array $palabrasIgnorar = [
        'KRONA', 'ACTARI', 'PLAMAT', 'EXT', 'ESPEJO', 'OXIDADO', 'OXID',
        'DE', 'PARA', 'NEGRO', 'NEG', 'CON', 'SIN', 'COLOR', 'TIPO'
    ];

    /**
     * Genera un código estándar a partir del nombre del producto
     */
    public static function generarCodigo(string $nombre, ?int $ignorarProductoId = null): string
    {
        $n = strtoupper(trim($nombre));
        if ($n === '') {
            return 'PRD-001';
        }

        // Normalizar caracteres especiales
        $n = str_replace(['Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'], ['A', 'E', 'I', 'O', 'U', 'N'], $n);

        // 1. Detectar prefijo
        $prefijo = null;
        $resto = $n;

        foreach (self::$prefijosCompuestos as $frase => $pre) {
            if (strpos($n, $frase) === 0) {
                $prefijo = $pre;
                $resto = trim(substr($n, strlen($frase)));
                break;
            }
        }

        // Si no está en el diccionario, abreviar la primera palabra
        if (!$prefijo) {
            $palabras = preg_split('/\s+/', $n);
            $primera = $palabras[0] ?? 'PRD';
            $primeraLetra = substr($primera, 0, 1);
            $sinVocales = $primeraLetra . preg_replace('/[AEIOU]/', '', substr($primera, 1));
            if (strlen($sinVocales) >= 3) {
                $prefijo = substr($sinVocales, 0, 3);
            } else {
                $prefijo = str_pad(substr($primera, 0, 3), 3, 'X');
            }
            $resto = trim(substr($n, strlen($primera)));
        }

        // 2. Extraer medidas/dimensiones/números del resto
        // Unir fracciones como "1 1/2" a "11/2" antes de tokenizar
        $resto = preg_replace('/(\d+)\s+(\d+\/\d+)/', '$1$2', $resto);

        $tokens = preg_split('/\s+/', $resto);
        $medidas = [];

        foreach ($tokens as $t) {
            $t = trim($t, "()# \t\n\r\0\x0B");
            if ($t === '') continue;
            if (in_array($t, self::$palabrasIgnorar)) continue;

            // Conservar tokens con dígitos o especificaciones como INOX, NEG, 2X2, 11/2A1
            if (preg_match('/[0-9]/', $t) || in_array($t, ['INOX', 'NEG'])) {
                $medidas[] = $t;
            }
        }

        // Si no se encontraron números ni medidas, tomar letras iniciales de las siguientes palabras
        if (empty($medidas) && trim($resto) !== '') {
            $subPalabras = array_filter(preg_split('/\s+/', $resto));
            $sufijoLetras = '';
            foreach ($subPalabras as $sp) {
                if (!in_array($sp, self::$palabrasIgnorar)) {
                    $sufijoLetras .= substr($sp, 0, 2);
                }
            }
            if ($sufijoLetras !== '') {
                $medidas[] = substr($sufijoLetras, 0, 4);
            }
        }

        $sufijo = implode('-', $medidas);
        $sufijo = str_replace(' ', '', $sufijo);

        // Construir código base
        if ($sufijo !== '') {
            // Si el prefijo ya termina en guion (ej. CAB-A, COD-R) y el sufijo empieza sin guion
            $codigoBase = $prefijo . '-' . $sufijo;
        } else {
            $codigoBase = $prefijo;
        }

        // Limpieza de guiones dobles
        $codigoBase = preg_replace('/-+/', '-', $codigoBase);
        $codigoBase = rtrim($codigoBase, '-');

        // 3. Garantizar unicidad contra la base de datos
        $codigoFinal = $codigoBase;
        $contador = 1;

        while (self::existeCodigo($codigoFinal, $ignorarProductoId)) {
            $contador++;
            $codigoFinal = $codigoBase . '-' . $contador;
        }

        return $codigoFinal;
    }

    private static function existeCodigo(string $codigo, ?int $ignorarProductoId = null): bool
    {
        $query = DB::table('productos')->where('codigo', $codigo);
        if ($ignorarProductoId !== null) {
            $query->where('id', '!=', $ignorarProductoId);
        }
        return $query->exists();
    }
}
