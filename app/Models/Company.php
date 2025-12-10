<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    // 必要ならfillableを指定
    protected $fillable = [
        'code',
        'name',
        'address',
        'tel',
        'is_default',
    ];
}