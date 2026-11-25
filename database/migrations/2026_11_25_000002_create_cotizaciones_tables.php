<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero_cotizacion', 30)->unique();
            $table->dateTime('fecha');
            $table->date('valida_hasta')->nullable();
            $table->string('tipo_cotizacion', 30)->default('cliente'); // 'cliente' | 'tienda_externa'

            // Destinatario (Cliente registrado o tienda externa / ocasional)
            $table->foreignId('id_cliente')->nullable()->constrained('clientes')->nullOnDelete();
            $table->string('destinatario_nombre', 255);
            $table->string('destinatario_empresa', 255)->nullable();
            $table->string('destinatario_nit', 50)->nullable();
            $table->string('destinatario_telefono', 50)->nullable();
            $table->string('destinatario_direccion', 255)->nullable();

            // Moneda y conversión Ragnar Capital
            $table->string('moneda', 10)->default('BOB'); // 'BOB' | 'PEN'
            $table->string('simbolo_moneda', 10)->default('Bs.');
            $table->decimal('tasa_cambio_aplicada', 10, 4)->nullable();
            $table->boolean('mostrar_equivalente')->default(true);

            // Montos
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->string('condiciones', 255)->nullable();
            $table->text('observaciones')->nullable();
            $table->string('estado', 30)->default('pendiente'); // pendiente, enviada, aprobada, rechazada, vencida
            $table->foreignId('id_usuario')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['fecha', 'estado']);
            $table->index('tipo_cotizacion');
        });

        Schema::create('detalle_cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_cotizacion')->constrained('cotizaciones')->cascadeOnDelete();
            $table->integer('id_producto')->nullable(); // Coincide con productos.id (int signed)
            $table->foreign('id_producto')->references('id')->on('productos')->nullOnDelete();
            $table->string('codigo_producto', 50)->nullable();
            $table->string('nombre_producto', 255);
            $table->string('unidad_medida', 50)->default('Pieza');
            $table->boolean('es_externo')->default(false);
            $table->decimal('cantidad', 12, 2);
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        // Otorgar permiso 'cotizaciones' automáticamente a usuarios no-admin que ya operan POS o Clientes
        try {
            $usuarios = DB::table('users')->where('rol', '!=', 'admin')->get();
            foreach ($usuarios as $u) {
                $permisos = is_string($u->permisos) ? json_decode($u->permisos, true) : (is_array($u->permisos) ? $u->permisos : []);
                if (is_array($permisos) && (in_array('pos', $permisos, true) || in_array('clientes', $permisos, true))) {
                    if (!in_array('cotizaciones', $permisos, true)) {
                        $permisos[] = 'cotizaciones';
                        DB::table('users')->where('id', $u->id)->update([
                            'permisos' => json_encode(array_values($permisos)),
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // No bloquear migración si la tabla users difiere
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_cotizaciones');
        Schema::dropIfExists('cotizaciones');
    }
};
