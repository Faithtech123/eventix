<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('ticket_type_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('event_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('holder_name');
            $table->timestamp('checked_in_at')->nullable();
            $table->foreignId('checked_in_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
