<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bus_status_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained()->onDelete('cascade');
            $table->foreignId('route_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['Departed', 'En Route', 'Arriving Soon', 'Seats Full', 'At Depot']);
            $table->string('current_stop');
            $table->integer('seats_available');
            $table->integer('eta_minutes')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('gps_accuracy')->nullable();
            $table->enum('direction', ['forward', 'reverse']);
            $table->timestamp('update_time');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bus_status_updates');
    }
};