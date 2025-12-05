<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
    use HasFactory;

    // 必要ならテーブル名を指定
    protected $table = 'item_types';

    // 必要ならカラムを指定
    protected $fillable = ['id', 'name', 'search_key_romaji', 'search_key_hiragana', 'search_key_katakana'];
}