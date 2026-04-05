<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_units', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->string('name');
            $table->string('slug');
            $table->string('acronym', 30)->nullable();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('chairperson')->nullable();
            $table->string('secretary')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('address')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['type', 'slug']);
            $table->index(['type', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_units');
    }
};
