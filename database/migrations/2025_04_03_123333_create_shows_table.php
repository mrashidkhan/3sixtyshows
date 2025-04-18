<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shows', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('category_id')->constrained('show_categories');
            $table->foreignId('venue_id')->constrained('venues');
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->string('featured_image');
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('available_tickets')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['upcoming', 'ongoing', 'past', 'cancelled'])->default('upcoming');
            $table->json('performers')->nullable();
            $table->json('additional_info')->nullable();
            $table->string('duration')->nullable();
            $table->string('age_restriction')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shows');
    }
};
