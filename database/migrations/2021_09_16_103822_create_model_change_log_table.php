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
        Schema::create('model_change_log', function (Blueprint $table) {
            $table->id();
            $table->string('model');
            $table->json('original');
            $table->json('changes');
            $table->timestamps();
        });
    }
}
