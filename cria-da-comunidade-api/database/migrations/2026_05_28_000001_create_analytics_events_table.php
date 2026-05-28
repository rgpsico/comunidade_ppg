<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);               // 'page_view' | 'click'
            $table->string('screen', 100)->nullable(); // 'inicio', 'profissionais', 'proDetail'…
            $table->string('entity_type', 50)->nullable(); // 'profissional' | 'evento' | 'projeto' | 'vaga'
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('entity_name', 200)->nullable();
            $table->string('session_id', 64)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_hash', 64)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['type', 'created_at']);
            $table->index(['screen', 'created_at']);
            $table->index(['entity_type', 'entity_id', 'created_at']);
            $table->index(['session_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};
