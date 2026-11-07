<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = [
        'payee_id',
        'amount',
        'status',
        'bank_account_id',
        'payment_method',
        'paystack_reference',
        'requested_at',
        'processed_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function payee()
    {
        return $this->belongsTo(User::class, 'payee_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
