<?php

use App\Enums\PropertyStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('properties', static function (Blueprint $table) {
            $table->id();
            $table->uuid()->nullable()->unique();
            $table->foreignId('user_id');

            $table->text('description')->nullable();
            $table->string('type')->nullable();
            $table->string('rooms_number')->nullable();
            $table->string('country')->nullable();
            $table->string('location')->nullable();
            $table->text('address_original')->nullable();
            $table->text('address_ru')->nullable();
            $table->text('address_en')->nullable();
            $table->string('status')->default(PropertyStatus::CREATED);
            $table->boolean('visibility')->default(false);
            $table->jsonb('data')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
