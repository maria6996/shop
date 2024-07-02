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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('parent_id')->nullable()->unsigned();
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('set null');
            $table->text('meta')->nullable();
            $table->text('keywords')->nullable();
            $table->integer('media_id')->nullable();
            $table->foreign('media_id')->references('id')->on('media');
            $table->text('description');
            $table->integer('category_type_id')->unsigned();
            $table->foreign('category_type_id')->references('id')->on('category_types');
            $table->tinyInteger('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
