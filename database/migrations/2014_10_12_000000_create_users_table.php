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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('type');
            $table->string('email')->unique();
            $table->string('country_code');
            $table->string('phone');
            $table->string('image')->default('');
            $table->text('gender')->default('');
            $table->string('location')->default('');
            $table->string('lat')->default('');
            $table->string('lng')->default('');
            $table->string('about')->default('');
            $table->string('dob')->default('');
            $table->string('password');
            $table->integer('verify')->default(0);
            $table->string('otp')->default('');
            $table->string('otp_time')->default('');
            $table->string('customer_id')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
