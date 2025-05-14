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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title_en');
            $table->string('title_heb');
            $table->text('body_en');
            $table->text('body_heb');
            $table->string('link')->nullable()->unique();
            $table->string('icon')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();

            // self-join constraint
            $table->foreign('parent_id')->references('id')->on('posts')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
