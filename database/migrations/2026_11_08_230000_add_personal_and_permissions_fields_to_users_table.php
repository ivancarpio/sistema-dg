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
        Schema::table('users', function (Blueprint $table) {
            $table->string('ci', 30)->nullable()->after('rol');
            $table->string('celular', 30)->nullable()->after('ci');
            $table->date('fecha_nacimiento')->nullable()->after('celular');
            $table->date('fecha_ingreso')->nullable()->after('fecha_nacimiento');
            $table->text('permisos')->nullable()->after('fecha_ingreso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ci', 'celular', 'fecha_nacimiento', 'fecha_ingreso', 'permisos']);
        });
    }
};
