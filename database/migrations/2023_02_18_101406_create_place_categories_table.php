<?php

use App\Enums\CategoryStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('place_categories', static function (Blueprint $table) {
            $table->id();
            $table->uuid()->nullable()->unique();
            $table->string('slug')->nullable()->unique();

            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default(CategoryStatus::ACTIVE);
            $table->boolean('visibility')->default(false);
            $table->nestedSet();
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
    public function down()
    {
        Schema::dropIfExists('place_categories');
    }
};
