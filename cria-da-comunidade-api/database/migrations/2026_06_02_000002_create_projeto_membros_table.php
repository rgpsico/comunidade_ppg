<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projeto_membros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projeto_id')->constrained()->cascadeOnDelete();
            $table->string('nome', 100);
            $table->string('cargo', 80)->default('');     // "Professor", "Coordenador"
            $table->text('bio')->nullable();
            $table->string('foto', 255)->nullable();      // storage path
            $table->string('cor', 7)->default('#FF5E1A'); // hex color for avatar gradient
            $table->unsignedTinyInteger('ordem')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projeto_membros');
    }
};
