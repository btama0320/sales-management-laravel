<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    protected $fillable = [
        'code',
        'name',
        'phone_number',
        'address',
        'search_key_romaji',
        'search_key_hiragana',
        'search_key_katakana',
    ];
}
