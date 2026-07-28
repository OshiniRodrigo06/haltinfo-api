<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->string('bus_number')->unique();
            $table->enum('type', ['Luxury', 'Semi-Luxury', 'Normal']);
            $table->string('operator_name');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('total_seats')->default(50);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('buses');
    }
};