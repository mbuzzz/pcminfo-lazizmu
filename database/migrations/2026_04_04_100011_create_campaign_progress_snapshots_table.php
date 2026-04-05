<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_progress_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')
                ->constrained('campaigns')
                ->cascadeOnDelete();
            $table->string('source_type', 50);
            $table->unsignedBigInteger('source_id');
            $table->integer('delta_amount')->default(0);
            $table->integer('delta_unit')->default(0);
            $table->unsignedBigInteger('before_amount')->default(0);
            $table->unsignedBigInteger('after_amount')->default(0);
            $table->unsignedInteger('before_unit')->default(0);
            $table->unsignedInteger('after_unit')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'created_at']);
            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_progress_snapshots');
    }
};
