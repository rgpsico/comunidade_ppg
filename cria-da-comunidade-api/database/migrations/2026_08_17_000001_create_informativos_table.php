<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('informativos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comunidade_id')->constrained()->cascadeOnDelete();
            $table->string('titulo');
            $table->string('fonte')->nullable();
            $table->date('data_ocorrencia')->nullable();
            $table->text('corpo');
            $table->boolean('urgente')->default(false);
            $table->boolean('publicado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informativos');
    }
};
