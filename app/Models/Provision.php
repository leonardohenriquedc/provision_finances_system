<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provision extends Model
{
    protected $table = 'provisions';

    protected $fillable = [
        'description',
        'user_id',
        'base_amount',
        'interest_rate',
        'interest_type',
        'interest_period',
        'installments',
        'competence_date',
        'first_due_date'
    ];

    public function provisionInstallments()
    {
        return $this->hasMany(ProvisionInstallment::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
