<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rsvp_email_logs', function (Blueprint $table) {
            $table->string('type')->default('invite')->after('sent_to_email');
            $table->index(['site_id', 'type', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::table('rsvp_email_logs', function (Blueprint $table) {
            $table->dropIndex(['site_id', 'type', 'sent_at']);
            $table->dropColumn('type');
        });
    }
};
