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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();

            $table->increments('id');
            $table->string("first_name",50)->nullable();
            $table->string("last_name",50)->nullable();
            $table->string('email',60)->unique();
            $table->string('mobile',11)->unique();
            $table->string('code_melli',10)->unique();
            $table->string('password');
            $table->boolean("status")->default(0);
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
