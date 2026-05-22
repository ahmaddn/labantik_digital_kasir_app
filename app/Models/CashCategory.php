<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashCategory extends Model
{
    protected $fillable = ['name'];

    public function cashTransactions()
    {
        return $this->hasMany(CashTransaction::class);
    }
}
