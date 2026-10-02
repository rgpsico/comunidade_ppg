<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quadra_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quadra_id')->constrained('quadras')->cascadeOnDelete();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->text('regras')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['quadra_id', 'ativo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quadra_times');
    }
};
