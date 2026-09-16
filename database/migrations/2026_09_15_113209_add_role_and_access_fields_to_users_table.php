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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')
                ->default('registered_user')
                ->after('password');

            $table->foreignId('organization_id')
                ->nullable()
                ->after('role')
                ->constrained('organizations')
                ->nullOnDelete();

            $table->boolean('is_active')
                ->default(true)
                ->after('organization_id');

            $table->boolean('must_change_password')
                ->default(false)
                ->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);

            $table->dropColumn([
                'role',
                'organization_id',
                'is_active',
                'must_change_password',
            ]);
        });
    }
};
