<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('intro_posts', function (Blueprint $table) {
            $table->id();
            $table->json('intro_title');
            $table->json('intro_post');
            $table->json('left_intro_title');
            $table->json('left_intro_post');
            $table->json('right_intro_title');
            $table->json('right_intro_post');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intro_posts');
    }
};
