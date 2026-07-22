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
        Schema::create('drink_items', function (Blueprint $table) {
            $table->id();
            $table->string('list_key', 64)->default('default');
            $table->string('name');
            $table->string('name_key');
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->unique(['list_key', 'name_key']);
            $table->index('list_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drink_items');
    }
};
