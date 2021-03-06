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
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            //$table->foreignId('host_id')->constrained('users')
               // ->onDelete('cascade');// deleted the party when the host user is deleted
            $table->string('partyName')->unique();
            $table->string('password');
            $table->boolean('hideHostLibrary');
            $table->boolean('isLocked');
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
        Schema::dropIfExists('parties');
    }
};
