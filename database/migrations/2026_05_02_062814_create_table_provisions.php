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
        Schema::create('provisions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('description');
            $table->decimal('base_amount', 12, 2);
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->enum('interest_type', ['SIMPLE', 'COMPOUND'])->nullable();
            $table->enum('interest_period', ['DAY', 'MONTH', 'YEAR'])->nullable();
            $table->integer('installments')->default(1);
            $table->date('competence_date');
            $table->date('first_due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provisions');
    }
};
