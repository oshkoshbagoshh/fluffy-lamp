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
        Schema::create('seo_results', function (Blueprint $table) {
            $table->id();
            $table->string('url')->unique();
            $table->float('aria_landmarks');
            $table->float('img_alt_tags');
            $table->float('color_contrast');
            $table->float('semantic_html');
            $table->float('broken_links');
            $table->float('meta_tags');
            $table->float('lazy_loading_images');
            $table->float('favicon');
            $table->float('mobile_friendly');
            $table->float('input_types');
            $table->float('total_score');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_results');
    }
};
