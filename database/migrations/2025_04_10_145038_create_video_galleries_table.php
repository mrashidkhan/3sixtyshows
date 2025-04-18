// Migration for VideoGalleries table
// create_video_galleries_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('video_galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('show_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('video_url');
            $table->string('thumbnail')->nullable();
            $table->text('description')->nullable();
            $table->string('video_type')->default('youtube'); // youtube, vimeo, etc.
            $table->integer('display_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('video_galleries');
    }
};
