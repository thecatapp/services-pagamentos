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
        Schema::create('contatos', function (Blueprint $table) {
            $table->id();

            $table->string("email");

            $table->unsignedInteger("pessoa_id");

            $table->foreign("pessoa_id")->references("id")->on("pessoas");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('contatos', function (Blueprint $table) {

            $table->dropForeign(['pessoa_id']);
            $table->dropColumn('pessoa_id');
        });

        Schema::dropIfExists('contatos');    }
};
