<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videosin_galleries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('video_gallery_id');
            $table->string('image')->nullable(); // Path to the video thumbnail/image
            $table->text('description')->nullable(); // Description of the video
            $table->integer('display_order')->default(0); // Order in which the video should be displayed
            $table->boolean('is_active')->default(true); // Status of the video
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('video_gallery_id')->references('id')->on('video_galleries')->onDelete('cascade');

            // Index for better performance
            $table->index(['video_gallery_id', 'display_order']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videosin_galleries');
    }
};
