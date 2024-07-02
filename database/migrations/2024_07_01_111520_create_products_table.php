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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('en_name');
            $table->string('slug');
            $table->string('serialnumber')->nullable();
            $table->foreign('media_id')->references('id')->on('medias')->onDelete('set null');
            $table->integer('view_count')->default(0);
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
            $table->integer('brand_id')->useCurrent('id')->on('brands')->onDelete('set null');
            $table->text('short_description')->nullable();
            $table->longText('long_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
