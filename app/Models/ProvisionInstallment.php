<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProvisionInstallment extends Model
{
    protected $table = 'provisions_installments';
    protected $fillable = [
        'provision_id',
        'installment_number',
        'amount',
        'due_date',
        'status'
    ];

    public function provision()
    {
        return $this->belongsTo(Provision::class);
    }
}
