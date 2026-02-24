<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rsvps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->unsignedTinyInteger('attending_count')->default(0);
            $table->json('meal_choices')->nullable();
            $table->text('dietary_restrictions')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rsvps');
    }
};
