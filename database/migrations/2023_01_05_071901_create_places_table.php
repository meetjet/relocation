<?php

use App\Enums\PlaceStatus;
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
        Schema::table('events', static function (Blueprint $table) {
            $table->dropColumn('point_slug');
        });

        Schema::drop('event_points');

        Schema::create('places', static function (Blueprint $table) {
            $table->id();
            $table->uuid()->nullable()->unique();
            $table->string('slug')->nullable()->unique();

            $table->string('country')->nullable();
            $table->string('location')->nullable();
            $table->string('type')->nullable();
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->text('address_ru')->nullable();
            $table->text('address_en')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->string('status')->default(PlaceStatus::ACTIVE);
            $table->boolean('visibility')->default(false);
            $table->jsonb('data')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('events', static function (Blueprint $table) {
            $table->string('place_slug')->nullable();
            $table->foreign('place_slug')
                ->references('slug')
                ->on('places')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('places');
    }
};
