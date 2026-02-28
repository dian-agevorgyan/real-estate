<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('floors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('number');
            $table->unsignedSmallInteger('apartments_count')->default(0);
            $table->string('plan_image')->nullable();
            $table->timestamps();

            $table->index(['section_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('floors');
    }
};
