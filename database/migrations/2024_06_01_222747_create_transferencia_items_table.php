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
        Schema::create('transferencia_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger("pessoa_destino");
            $table->foreign("pessoa_destino")->references("id")->on("pessoas");

            $table->unsignedInteger("transferencia_id");
            $table->foreign("transferencia_id")->references("id")->on("transferencias");

            $table->double("vl_transferencia", 8, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('transferencia_items', function (Blueprint $table) {
            $table->dropForeign(['pessoa_destino']);
            $table->dropColumn('pessoa_destino');

            $table->dropForeign(['transferencia_id']);
            $table->dropColumn('transferencia_id');
        });

        Schema::dropIfExists('transferencia_items');
    }
};
