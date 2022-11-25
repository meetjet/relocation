<?php

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
            $table->renameColumn('city', 'location');
            $table->timestamp('published_at')->nullable();
            $table->decimal('price', 64, 2)->nullable();
            $table->string('currency')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('point_slug')->nullable();
            $table->text('address')->nullable();

            $table->foreignId('category_id')->nullable()
                ->constrained('event_categories')
                ->nullOnDelete();

            $table->date('start_date')->nullable();
            $table->time('start_time')->nullable();
            $table->date('finish_date')->nullable();
            $table->time('finish_time')->nullable();

            $table->foreign('point_slug')
                ->references('slug')
                ->on('event_points')
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
        //
    }
};
