<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_staff')->default(false)->after('role');
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->string('access_status')->default('active')->after('status');
            $table->text('internal_notes')->nullable()->after('access_status');
        });

        Schema::create('staff_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->string('action');
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        Schema::table('stripe_webhook_events', function (Blueprint $table) {
            $table->foreignId('account_id')->nullable()->after('type')->constrained('accounts')->nullOnDelete();
            $table->index(['account_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('stripe_webhook_events', function (Blueprint $table) {
            $table->dropIndex(['account_id', 'created_at']);
            $table->dropConstrainedForeignId('account_id');
        });

        Schema::dropIfExists('staff_audit_logs');

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn(['access_status', 'internal_notes']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_staff');
        });
    }
};
