<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('premises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('floor_id')->constrained()->cascadeOnDelete();
            $table->string('apartment_number');
            $table->string('type', 32)->default('apartment')->index();
            $table->unsignedTinyInteger('rooms')->default(1);
            $table->decimal('area_total', 10, 2)->nullable();
            $table->decimal('area_living', 10, 2)->nullable();
            $table->decimal('area_kitchen', 10, 2)->nullable();
            $table->string('status', 32)->default('available')->index();
            $table->decimal('price_base', 14, 2)->nullable();
            $table->decimal('price_discount', 14, 2)->nullable();
            $table->decimal('price_per_m2', 12, 2)->nullable();
            $table->unsignedSmallInteger('floor_number')->nullable();
            $table->string('layout_image')->nullable();
            $table->json('gallery')->nullable();
            $table->json('extras')->nullable();
            $table->timestamps();

            $table->index(['floor_id']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('premises');
    }
};
