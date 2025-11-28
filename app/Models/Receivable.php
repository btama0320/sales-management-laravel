<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ReceivableDetail; // ← ここがポイント！

class Receivable extends Model
{
    use HasFactory;

    protected $fillable = [
        'slip_date', 'slip_no', 'shipper_code', 'shipper_name',
        'customer_code', 'customer_name', 'department', 'honorific',
        'billing_code', 'billing_name', 'item_code_header', 'item_name_header',
        'carrier_code', 'carrier_name', 'summary', 'sales_date', 'is_draft'
    ];

    public function details()
    {
        return $this->hasMany(ReceivableDetail::class);
    }
}