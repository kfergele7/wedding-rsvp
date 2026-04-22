<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('parties', function (Blueprint $table) {
            $table->string('guest_type', 20)->default('day')->after('display_name');
        });

        DB::table('parties')
            ->whereNull('guest_type')
            ->update(['guest_type' => 'day']);
    }

    public function down(): void
    {
        Schema::table('parties', function (Blueprint $table) {
            $table->dropColumn('guest_type');
        });
    }
};
