<?php

use App\Enums\EventPointStatus;
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
        Schema::create('event_points', static function (Blueprint $table) {
            $table->id();
            $table->uuid()->nullable()->unique();
            $table->string('slug')->nullable()->unique();

            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->text('address')->nullable();
            $table->string('status')->default(EventPointStatus::ACTIVE);
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
        Schema::dropIfExists('event_points');
    }
};
