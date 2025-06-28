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
        Schema::create('player_position', function (Blueprint $table) {
            $table->id();
            $table->integer('imported_id')->index();
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->string('developer_name')->nullable();
            $table->string('model_type')->nullable();
            $table->string('stat_group')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_position');
    }
};
