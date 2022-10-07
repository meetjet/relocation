<?php

use App\Enums\FaqStatus;
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
        Schema::create('faqs', static function (Blueprint $table) {
            $table->id();
            $table->uuid()->nullable()->unique();

            $table->text('original')->nullable();
            $table->string('title')->nullable();
            $table->text('question')->nullable();
            $table->text('answer')->nullable();
            $table->string('status')->default(FaqStatus::CREATED);
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
        Schema::dropIfExists('faqs');
    }
};
