<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable()->unique()->after('status');
            $table->string('stripe_subscription_id')->nullable()->after('stripe_customer_id');
            $table->string('stripe_price_id')->nullable()->after('stripe_subscription_id');
            $table->string('subscription_status')->nullable()->after('stripe_price_id');
            $table->timestamp('subscription_current_period_end')->nullable()->after('subscription_status');
            $table->boolean('subscription_cancel_at_period_end')->default(false)->after('subscription_current_period_end');
            $table->timestamp('subscription_canceled_at')->nullable()->after('subscription_cancel_at_period_end');
            $table->timestamp('subscription_ends_at')->nullable()->after('subscription_canceled_at');
        });

        Schema::create('stripe_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_event_id')->unique();
            $table->string('type');
            $table->json('payload');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stripe_webhook_events');

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_customer_id',
                'stripe_subscription_id',
                'stripe_price_id',
                'subscription_status',
                'subscription_current_period_end',
                'subscription_cancel_at_period_end',
                'subscription_canceled_at',
                'subscription_ends_at',
            ]);
        });
    }
};
