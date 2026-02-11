<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuarios')->constrained('usuarios')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('id_categorias')->nullable()->constrained('categorias')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('nome', 45)->nullable();
            $table->unsignedInteger('tempo_preparo_minutos')->nullable();
            $table->unsignedInteger('porcoes')->nullable();
            $table->text('modo_preparo');
            $table->text('ingredientes')->nullable();
            $table->dateTime('criado_em');
            $table->dateTime('alterado_em');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receitas');
    }
};
