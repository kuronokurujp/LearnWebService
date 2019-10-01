<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// C#の所usingの事名前空間の事
use App\Drill;

class DrillsController extends Controller
{
    // ログインしたユーザーが作成した問題練習一覧表示
    public function mypage() {
        $drills = Auth::user()->drills()->get();
        return view('drills.mypage', compact('drills'));
    }

    // 問題データ更新
    public function update(Request $request, $id) {
        if (!ctype_digit($id)) {
            return redirect('/drills/new')->with('flash_message', __('Invalide Missmatch Drill data.'));
        }

        $drill = Auth::user()->drills()->find($id);
        if ($drill === null) {
            return redirect('/drills/new')->with('flash_message', __('Invalide Missmatch Drill data.'));
        }

        Auth::user()->drills()->save($drill->fill($request->all()));

        return redirect('/drills')->with('flash_message', __('Sucessed Drill Data Update.'));

    }

    // 問題編集
    // パラメータ全部文字列型になる！
    public function edit($id) {
        if (!ctype_digit($id)) {
            return redirect('/drills/new')->with('flash_message', __('Invalide edit data.'));
        }

        // ログインしたユーザーの練習問題のみ取得
        // ない場合はnull
        $drill = Auth::user()->drills()->find($id);
        if ($drill === null) {
            return redirect('/drills/new')->with('flash_message', __('Invalide edit data.'));
        }

        // todo 空かどうかチェックするべき
        return view('drills.edit', ['drill' => $drill]);
    }

    // 問題削除
    public function destory($id) {
        if (!ctype_digit($id)) {
            return redirect('/drills')->with('flash_message', __('Miss Delete Drill Data Update.'));
        }

        $drill = Auth::user()->drills()->find($id);
        if ($drill === null) {
            return redirect('/drills')->with('flash_message', __('Miss Delete Drill Data Update.'));
        }

        $drill->delete();

        return redirect('/drills')->with('flash_message', __('Delete Drill Data Update.'));
    }

    // 問題一覧表示
    public function index() {
        $drills = Drill::all();
        // 特定ユーザーIdの問題を取得出来る
        // ↓
        // $drills = Drill::find(1);
        return view('drills.index', ['drills' => $drills]);
    }

    // 練習問題のView取得
    public function new () {
        return view('drills.new');
    }

    // データ作成
    public function create(Request $request) {
        // 値が正しいかチェックする
        $request->validate([
            'title' =>         'required|string|max:255',
            'category_name' => 'required|string|max:255',
            'problem0' =>      'required|string|max:255',
            'problem1' => 'nullable|string|max:255',
            'problem2' => 'nullable|string|max:255',
            'problem3' => 'nullable|string|max:255',
            'problem4' => 'nullable|string|max:255',
            'problem5' => 'nullable|string|max:255',
            'problem6' => 'nullable|string|max:255',
            'problem7' => 'nullable|string|max:255',
            'problem8' => 'nullable|string|max:255',
            'problem9' => 'nullable|string|max:255',
        ]);

        // モデル作成、DBに登録する値を設定
        $drill = new Drill;

        // fillで一気にぶっこむ
        // 全部更新になるので、モデルの$fillable配列で更新対象カラムのみになるので
        // 更新したくないのは更新しないので一括書き込むがいける
        Auth::user()->drills()->save($drill->fill($request->all()));

        // リダイレクトする
        // sessionにメッセージを追加する事が出来る
        // with以降の処理がそう
        // 一度切りのみsession
        return redirect('/drills/new')->with('flash_message', __('Registered.'));
    }

    public function show($id) {
        if (!ctype_digit($id)) {
            return redirect('/drills/new')->with('flash_message', __('Invalide Missmatch Drill data.'));
        }

        $drill = Drill::find($id);

        return view('drills.show', ['drill' => $drill]);
    }
}
