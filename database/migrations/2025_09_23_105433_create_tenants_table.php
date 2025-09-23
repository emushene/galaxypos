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
        Schema::connection('main')->create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('subdomain')->unique();
            $table->string('db_database');
            $table->string('db_host')->nullable();
            $table->string('db_port')->nullable();
            $table->string('db_username')->nullable();
            $table->string('db_password')->nullable();
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
        Schema::connection('main')->dropIfExists('tenants');
    }
};