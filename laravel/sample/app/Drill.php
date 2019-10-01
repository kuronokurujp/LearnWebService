<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Drill extends Model
{
    // 変更したいカラムリスト
    protected $fillable = [
        'title',
        'category_name',
        'problem0',
        'problem1',
        'problem2',
        'problem3',
        'problem4',
        'problem5',
        'problem6',
        'problem7',
        'problem8',
        'problem9',
    ];

    // カラムにロックを掛ける
    // protected $quarded = [];

    // ドリルデータに関連のあるユーザーを取得
    public function user() {
        return $this->belongsTo('App\User');
    }
}
