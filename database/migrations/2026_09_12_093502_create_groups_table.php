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
      Schema::create('groups', function (Blueprint $table) {
        $table->id();

        $table->foreignId('organization_id')
            ->constrained('organizations')
            ->cascadeOnDelete();

        $table->string('name');

        $table->text('description')
            ->nullable();

        $table->string('contact_name')
            ->nullable();

        $table->string('telephone')
            ->nullable();

        $table->string('email')
            ->nullable();

        $table->boolean('is_head_office')
            ->default(false);

        $table->timestamps();
        $table->softDeletes();

        $table->unique([
            'organization_id',
            'name',
        ]);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
