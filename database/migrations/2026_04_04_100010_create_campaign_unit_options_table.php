<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_unit_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')
                ->constrained('campaigns')
                ->cascadeOnDelete();
            $table->string('code', 50);
            $table->string('label');
            $table->unsignedInteger('unit_value')->default(1);
            $table->unsignedBigInteger('amount')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['campaign_id', 'code']);
            $table->index(['campaign_id', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_unit_options');
    }
};
