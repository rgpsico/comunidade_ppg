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
        Schema::create('feed_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comunidade_id')->constrained()->cascadeOnDelete();
            $table->string('autor');
            $table->string('legenda')->nullable();
            $table->string('imagem_url')->nullable();
            $table->string('cor1')->default('#FF5E1A');
            $table->string('cor2')->default('#FFD23F');
            $table->enum('tamanho', ['normal', 'tall', 'wide'])->default('normal');
            $table->boolean('publicado')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feed_posts');
    }
};
