<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Receivable; // ← ここがポイント！

class ReceivableDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'receivable_id', 'row_no', 'item_code', 'item_name',
        'package', 'unit', 'grade', 'class',
        'quantity', 'unit_price', 'amount', 'remarks'
    ];

    public function receivable()
    {
        return $this->belongsTo(Receivable::class);
    }
}