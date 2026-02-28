<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('premise_price_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('premise_id')->constrained()->cascadeOnDelete();
            $table->decimal('old_price', 14, 2)->nullable();
            $table->decimal('new_price', 14, 2);
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->index(['premise_id', 'changed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('premise_price_history');
    }
};
