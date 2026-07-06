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
        Schema::create('domain_nameserver', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('domain_id');
            $table->unsignedBigInteger('nameserver_id');
            $table->timestamps();

            $table->unique(['domain_id', 'nameserver_id']);
            $table->index(['nameserver_id', 'domain_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_nameserver');
    }
};
