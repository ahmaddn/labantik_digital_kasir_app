<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'cash_type',
        'name',
        'type',
        'amount',
        'description',
        'reference'
    ];
}
