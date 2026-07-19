<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dramas', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('poster_url')->nullable();
            $table->string('banner_url')->nullable();
            $table->string('trailer_url')->nullable();
            $table->decimal('rating', 3, 1)->default(0);
            $table->integer('year')->nullable();
            $table->string('duration')->nullable();
            $table->integer('episode_count')->default(0);
            $table->integer('latest_episode')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_trending')->default(false);
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dramas');
    }
};