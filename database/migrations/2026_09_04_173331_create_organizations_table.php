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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->foreignId('denomination_id')
                ->constrained('denominations')
                ->restrictOnDelete();

            $table->foreignId('location_id')
                ->constrained('locations')
                ->restrictOnDelete();

            $table->text('description')->nullable();

            $table->string('address');

            $table->string('website')->nullable();

            $table->string('telephone')->nullable();

            $table->string('email')->nullable();

            $table->string('head')->nullable();

            $table->string('photo')->nullable();

            $table->timestamps();

            $table->index([
                'denomination_id',
                'location_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
