<?php
// 画像アップロード
    // ファイルが送らて来ている場合
    if (!empty($file)) {
        // バリデーションが必要
        // 1.ファイルが画像ファイルかどうかの判定
        // 2.ファイルサイズの上限チェック
        
        // サーバーに画像を保存
        $upload_path = 'images/'.$file['name'];
        // 一時フォルダにある画像をサーバー側で用意したアップロードフォルダに移動
        $rst = move_uploaded_file($file['tmp_name'], $upload_path);
        // アップロード結果によって表示するメッセージを変数に代入
        if ($rst) {
            $msg = '画像アップしました。アップした画像ファイル名：'.$file['name'];
            // 表示用の画像パスを渡す
            $img_path = $upload_path;
        }
        else {
            $msg = '画像はアップできませんでした。エラー内容：'.$file['error'];
        }
    }
    else {
        $msg = '画像を選択してください';
    }
?>