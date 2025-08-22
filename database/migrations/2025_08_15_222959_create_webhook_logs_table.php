<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('webhook_id')->unique()->index();
            $table->string('status', 50)->index(); // received, processed, error, signature_failed, etc.
            $table->string('event_type', 100)->nullable()->index();
            $table->longText('payload'); // Full webhook payload
            $table->text('headers')->nullable(); // Request headers as JSON
            $table->text('additional_data')->nullable(); // Processing results, errors, etc.
            $table->string('resource_id')->nullable()->index(); // PayPal order ID, capture ID, etc.
            $table->unsignedBigInteger('booking_id')->nullable()->index();
            $table->timestamp('processed_at');
            $table->integer('processing_time_ms')->nullable(); // Processing time in milliseconds
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('set null');

            // Indexes for performance
            $table->index(['status', 'processed_at']);
            $table->index(['event_type', 'processed_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webhook_logs');
    }
};
