<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rsvp_email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->foreignId('party_id')->constrained('parties')->cascadeOnDelete();
            $table->foreignId('sent_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('sent_to_email');
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->index(['site_id', 'party_id', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rsvp_email_logs');
    }
};
