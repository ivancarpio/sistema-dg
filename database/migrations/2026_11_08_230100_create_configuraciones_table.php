<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->text('valor')->nullable();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        // Insertar valores predeterminados
        \Illuminate\Support\Facades\DB::table('configuraciones')->insert([
            [
                'clave' => 'mostrar_modal_qr',
                'valor' => '1',
                'descripcion' => 'Determina si se muestra el popup con imagen QR al seleccionar método QR en POS (1 = Sí, 0 = No)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'nombre_empresa',
                'valor' => 'Distribuidora Guadalupe',
                'descripcion' => 'Nombre comercial de la empresa para recibos y reportes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'nit_empresa',
                'valor' => '',
                'descripcion' => 'NIT o Identificación Tributaria',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'telefono_empresa',
                'valor' => '',
                'descripcion' => 'Teléfono o Celular de contacto',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'direccion_empresa',
                'valor' => '',
                'descripcion' => 'Dirección física del establecimiento',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'pie_ticket',
                'valor' => '¡Gracias por su preferencia!',
                'descripcion' => 'Mensaje al pie de las notas de venta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'stock_minimo_defecto',
                'valor' => '5',
                'descripcion' => 'Alerta de stock mínimo predeterminada para nuevos productos',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuraciones');
    }
};
