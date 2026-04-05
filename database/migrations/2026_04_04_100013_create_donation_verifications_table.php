<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')
                ->constrained('donations')
                ->cascadeOnDelete();
            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('status', 30)->index();
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['donation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_verifications');
    }
};
