<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable()->unique();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('public_slug')->unique();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('account_id')->nullable()->after('id')->constrained('accounts')->nullOnDelete();
            $table->string('role')->default('owner')->after('password');
        });

        Schema::table('parties', function (Blueprint $table) {
            $table->foreignId('site_id')->nullable()->after('id')->constrained('sites')->cascadeOnDelete();
            $table->dropUnique('parties_code_unique');
            $table->unique(['site_id', 'code']);
            $table->index('site_id');
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->foreignId('site_id')->nullable()->after('id')->constrained('sites')->cascadeOnDelete();
            $table->index('site_id');
        });

        Schema::table('rsvps', function (Blueprint $table) {
            $table->foreignId('site_id')->nullable()->after('id')->constrained('sites')->cascadeOnDelete();
            $table->index('site_id');
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->foreignId('site_id')->nullable()->after('id')->constrained('sites')->cascadeOnDelete();
            $table->dropUnique('site_settings_key_unique');
            $table->unique(['site_id', 'key']);
            $table->index('site_id');
        });

        $now = now();
        $defaultAccountId = DB::table('accounts')->insertGetId([
            'name' => 'Default Account',
            'slug' => 'default-account',
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $defaultSiteId = DB::table('sites')->insertGetId([
            'account_id' => $defaultAccountId,
            'title' => 'Default Wedding Site',
            'public_slug' => 'default-'.Str::lower(Str::random(8)),
            'is_published' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('users')->whereNull('account_id')->update([
            'account_id' => $defaultAccountId,
            'role' => 'owner',
            'updated_at' => $now,
        ]);

        DB::table('parties')->whereNull('site_id')->update([
            'site_id' => $defaultSiteId,
            'updated_at' => $now,
        ]);

        DB::table('guests')->whereNull('site_id')->update([
            'site_id' => $defaultSiteId,
            'updated_at' => $now,
        ]);

        DB::table('rsvps')->whereNull('site_id')->update([
            'site_id' => $defaultSiteId,
            'updated_at' => $now,
        ]);

        DB::table('site_settings')->whereNull('site_id')->update([
            'site_id' => $defaultSiteId,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropUnique('site_settings_site_id_key_unique');
            $table->unique('key');
            $table->dropIndex(['site_id']);
            $table->dropConstrainedForeignId('site_id');
        });

        Schema::table('rsvps', function (Blueprint $table) {
            $table->dropIndex(['site_id']);
            $table->dropConstrainedForeignId('site_id');
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->dropIndex(['site_id']);
            $table->dropConstrainedForeignId('site_id');
        });

        Schema::table('parties', function (Blueprint $table) {
            $table->dropUnique('parties_site_id_code_unique');
            $table->unique('code');
            $table->dropIndex(['site_id']);
            $table->dropConstrainedForeignId('site_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('account_id');
            $table->dropColumn('role');
        });

        Schema::dropIfExists('sites');
        Schema::dropIfExists('accounts');
    }
};
