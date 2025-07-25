<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->integer('imported_id')->index();
            $table->integer('position_id')->nullable()->index();
            $table->integer('country_id')->index();
            $table->string('name')->index();
            $table->string('normalized_name')->nullable()->index();
            $table->string('common_name');
            $table->string('display_name');
            $table->enum('gender', ['male', 'female'])->default('male');
            $table->integer('height')->nullable();
            $table->integer('weight')->nullable();
            $table->timestamp('date_of_birth')->nullable()->index();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
