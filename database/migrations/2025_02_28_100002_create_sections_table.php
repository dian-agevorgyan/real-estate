<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('number')->nullable();
            $table->unsignedSmallInteger('floors_count_in_section')->default(1);
            $table->timestamps();

            $table->index(['building_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
