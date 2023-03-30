<?php

use App\Enums\AdvertisementStatus;
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
        Schema::create('advertisements', static function (Blueprint $table) {
            $table->id();
            $table->uuid()->nullable()->unique();
            $table->foreignId('user_id');

            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('country')->nullable();
            $table->string('status')->default(AdvertisementStatus::CREATED);
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
        Schema::dropIfExists('advertisements');
    }
};
