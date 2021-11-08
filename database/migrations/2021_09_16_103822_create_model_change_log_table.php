<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// phpcs:ignore
final class CreateModelChangeLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(config('change-logger.table'), function (Blueprint $table) {
            $table->id();
            $table->string('model')->index();
            $table->unsignedBigInteger('foreign_id');
            $table->json('original');
            $table->json('changes');
            $table->timestamps();

            $table->index(['model', 'foreign_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('change-logger.table'));
    }
}
