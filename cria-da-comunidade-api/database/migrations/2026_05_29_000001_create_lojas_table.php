<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lojas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('comunidade_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nome', 150);
            $table->text('descricao')->nullable();
            $table->string('categoria', 80);
            $table->string('logo')->nullable();       // path no disco public
            $table->string('capa')->nullable();       // path no disco public
            $table->string('whatsapp', 20)->nullable();
            $table->string('endereco', 200)->nullable();
            $table->string('cor1', 7)->default('#FF5E1A');
            $table->string('cor2', 7)->default('#FFD23F');
            $table->boolean('verificado')->default(false);
            $table->boolean('ativo')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lojas');
    }
};
