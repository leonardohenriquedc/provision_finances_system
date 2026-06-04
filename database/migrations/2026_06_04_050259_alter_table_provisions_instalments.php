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
        Schema::table('provisions_installments', function (Blueprint $table) {
            $table->enum('status', [
                'PAID',
                'LATE',
                'OPEN',
                'LATE_PAYMENT'
            ])
            ->default("OPEN")
            ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    
    public function down(): void
    {
        DB::table('provision_installment')
            ->where('status', 'LATE_PAYMENT')
            ->update([
                'status' => 'LATE'
            ]);

        Schema::table('provision_installment', function (Blueprint $table) {
            $table->enum('status', [
                'PAID',
                'LATE',
                'OPEN'
            ])
            ->default('OPEN')
            ->change();
        });
    }
};
