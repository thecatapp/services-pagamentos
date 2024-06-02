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
        Schema::create('transferencias', function (Blueprint $table) {
            $table->id();

            $table->double("vl_total", 8, 2);

            $table->unsignedInteger("pessoa_origem");
            $table->foreign("pessoa_origem")->references("id")->on("pessoas");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transferencias', function (Blueprint $table) {
            $table->dropForeign(['pessoa_origem']);
            $table->dropColumn('pessoa_origem');
        });

        Schema::dropIfExists('transferencias');
    }
};
