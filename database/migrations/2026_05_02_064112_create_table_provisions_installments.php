<?php

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
        Schema::create('provisions_installments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('provision_id')
                ->constrained('provisions')
                ->cascadeOnDelete();
            $table->integer('installment_number');
            // valor calculado da parcela
            $table->decimal('amount', 12, 2);
            $table->date('due_date');
            $table->enum('status', ['OPEN', 'PAID', 'LATE'])
                  ->default('OPEN');
            $table->index('due_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provisions_installments');
    }
};
