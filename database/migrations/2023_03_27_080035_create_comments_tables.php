<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $this->nullableMorphs($table, 'commentator', 'commentator_comments');
            $table->morphs('commentable');
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');
            $table->longText('original_text');
            $table->longText('text');
            $table->json('extra')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $this->nullableMorphs($table, 'commentator', 'commentator_reactions');
            $table->foreignId('comment_id')->references('id')->on('comments')->cascadeOnDelete();
            $table->string('reaction');
            $table->timestamps();
        });

        Schema::create('comment_notification_subscriptions', static function(Blueprint $table) {
            $table->id();
            $table->morphs('commentable', 'cn_subscriptions_commentable');
            $table->morphs('subscriber', 'cn_subscriptions_subscriber');
            $table->string('type');

            $table->timestamps();
        });
    }

    protected function nullableMorphs(Blueprint $table, string $name, string $indexName): void
    {
        $table->string("{$name}_type")->nullable();
        $table->unsignedBigInteger("{$name}_id")->nullable();
        $table->index(["{$name}_type", "{$name}_id"], $indexName);
    }
};
