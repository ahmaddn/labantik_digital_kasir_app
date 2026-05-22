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
        'cash_category_id',
        'type',
        'amount',
        'description',
        'reference'
    ];

    public function cashCategory()
    {
        return $this->belongsTo(CashCategory::class, 'cash_category_id');
    }
}
