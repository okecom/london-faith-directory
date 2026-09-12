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
       Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('group_id')
                ->constrained('groups')
                ->cascadeOnDelete();

            $table->foreignId('event_type_id')
                ->constrained('event_types')
                ->restrictOnDelete();

            $table->foreignId('location_id')
                ->constrained('locations')
                ->restrictOnDelete();

            $table->string('name');

            $table->text('description')
                ->nullable();

            $table->dateTime('start_datetime');

            $table->dateTime('end_datetime')
                ->nullable();

            $table->string('venue_name')
                ->nullable();

            $table->string('address')
                ->nullable();

            $table->string('contact_name')
                ->nullable();

            $table->string('telephone')
                ->nullable();

            $table->string('email')
                ->nullable();

            $table->string('website')
                ->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'group_id',
                'start_datetime',
            ]);

            $table->index([
                'event_type_id',
                'location_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
