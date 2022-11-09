<?php

use App\Enums\ListingItemStatus;
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
        Schema::create('listing_items', static function (Blueprint $table) {
            $table->id();
            $table->uuid()->nullable()->unique();
            $table->foreignId('user_id');
            $table->foreignId('category_id')->nullable()
                ->constrained('listing_categories')
                ->nullOnDelete();

            $table->string('country')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default(ListingItemStatus::CREATED);
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
        Schema::dropIfExists('listing_items');
    }
};
