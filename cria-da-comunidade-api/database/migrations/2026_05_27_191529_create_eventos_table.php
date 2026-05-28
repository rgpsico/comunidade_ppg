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
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizador_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('comunidade_id')->nullable()->constrained('comunidades')->nullOnDelete();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('categoria'); // baile, pagode, esporte, cultura, festa, workshop
            $table->datetime('data_hora');
            $table->string('local')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->boolean('gratuito')->default(true);
            $table->decimal('preco', 8, 2)->nullable();
            $table->integer('idade_minima')->default(0);
            $table->string('duracao')->nullable();
            $table->integer('confirmados')->default(0);
            $table->integer('interessados')->default(0);
            $table->string('cor1', 7)->default('#FF5E1A');
            $table->string('cor2', 7)->default('#FFD23F');
            $table->boolean('destaque')->default(false);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
