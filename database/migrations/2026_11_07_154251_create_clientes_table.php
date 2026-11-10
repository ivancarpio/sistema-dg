<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('clientes')) {
            Schema::create('clientes', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 255);
                $table->string('telefono', 50)->nullable();
                $table->string('ci_nit', 50)->nullable();
                $table->string('direccion', 255)->nullable();
                $table->string('email', 100)->nullable();
                $table->text('notas')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('ventas', function (Blueprint $table) {
            if (!Schema::hasColumn('ventas', 'id_cliente')) {
                $table->unsignedBigInteger('id_cliente')->nullable()->after('id_usuario');
                $table->foreign('id_cliente')->references('id')->on('clientes')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (Schema::hasColumn('ventas', 'id_cliente')) {
                $table->dropForeign(['id_cliente']);
                $table->dropColumn('id_cliente');
            }
        });

        Schema::dropIfExists('clientes');
    }
};
