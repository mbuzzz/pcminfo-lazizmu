<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('email');
            $table->timestamp('last_login_at')->nullable()->after('email_verified_at');
            $table->boolean('is_active')->default(true)->after('password');
            $table->softDeletes();

            $table->index(['is_active', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'deleted_at']);
            $table->dropSoftDeletes();
            $table->dropColumn(['avatar', 'last_login_at', 'is_active']);
        });
    }
};
