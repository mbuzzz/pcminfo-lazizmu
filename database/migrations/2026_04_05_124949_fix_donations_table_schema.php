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
        Schema::table('donations', function (Blueprint $table) {
            // Renames to match the Donation model and current codebase
            if (Schema::hasColumn('donations', 'confirmed_at')) {
                $table->renameColumn('confirmed_at', 'verified_at');
            }
            if (Schema::hasColumn('donations', 'confirmed_by')) {
                $table->renameColumn('confirmed_by', 'verified_by');
            }
            if (Schema::hasColumn('donations', 'donor_name')) {
                $table->renameColumn('donor_name', 'payer_name');
            }
            if (Schema::hasColumn('donations', 'donor_email')) {
                $table->renameColumn('donor_email', 'payer_email');
            }
            if (Schema::hasColumn('donations', 'donor_phone')) {
                $table->renameColumn('donor_phone', 'payer_phone');
            }
            if (Schema::hasColumn('donations', 'unit_quantity')) {
                $table->renameColumn('unit_quantity', 'quantity');
            }
            
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('donations', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('status')->index();
            }
            if (!Schema::hasColumn('donations', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('verified_at')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            if (Schema::hasColumn('donations', 'verified_at')) {
                $table->renameColumn('verified_at', 'confirmed_at');
            }
            if (Schema::hasColumn('donations', 'verified_by')) {
                $table->renameColumn('verified_by', 'confirmed_by');
            }
            if (Schema::hasColumn('donations', 'payer_name')) {
                $table->renameColumn('payer_name', 'donor_name');
            }
            if (Schema::hasColumn('donations', 'payer_email')) {
                $table->renameColumn('payer_email', 'donor_email');
            }
            if (Schema::hasColumn('donations', 'payer_phone')) {
                $table->renameColumn('payer_phone', 'donor_phone');
            }
            if (Schema::hasColumn('donations', 'quantity')) {
                $table->renameColumn('quantity', 'unit_quantity');
            }
            
            $table->dropColumn(['submitted_at', 'rejected_at']);
        });
    }
};
