<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('arqueos_caja') && !Schema::hasColumn('arqueos_caja', 'id_usuario_cierre')) {
            Schema::table('arqueos_caja', function (Blueprint $table) {
                $table->unsignedBigInteger('id_usuario_cierre')->nullable()->after('id_usuario');
                $table->foreign('id_usuario_cierre')->references('id')->on('users')->onDelete('set null');
            });

            DB::table('arqueos_caja')
                ->where('estado', 'cerrado')
                ->whereNull('id_usuario_cierre')
                ->update(['id_usuario_cierre' => DB::raw('id_usuario')]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('arqueos_caja') && Schema::hasColumn('arqueos_caja', 'id_usuario_cierre')) {
            Schema::table('arqueos_caja', function (Blueprint $table) {
                $table->dropForeign(['id_usuario_cierre']);
                $table->dropColumn('id_usuario_cierre');
            });
        }
    }
};
