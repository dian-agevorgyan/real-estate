<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complex_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('number')->nullable();
            $table->unsignedSmallInteger('floors_count')->default(1);
            $table->unsignedSmallInteger('built_year')->nullable();
            $table->timestamps();

            $table->index(['complex_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
