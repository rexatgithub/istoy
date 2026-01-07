<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();
            $table->integer('service')->nullable();
            $table->string('link');
            $table->integer('quantity');
            $table->string('status')->default('pending');
            $table->integer('start_count')->default(0);
            $table->integer('remains')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

