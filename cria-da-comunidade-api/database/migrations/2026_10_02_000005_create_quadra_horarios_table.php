<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quadra_horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quadra_time_id')->constrained('quadra_times')->cascadeOnDelete();
            // 0 = domingo ... 6 = sábado
            $table->unsignedTinyInteger('dia_semana');
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->timestamps();

            $table->index(['dia_semana', 'hora_inicio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quadra_horarios');
    }
};
