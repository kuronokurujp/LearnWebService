<?php

// セッション制御
class Session {
    public function __construct()  {
        // セッション準備・セッション有効期限を延ばす
        // セッションファイルの置き場を変更する
        // (var/tmp以下に置くと30日は削除されない)
        session_save_path("var/tmp/");
        // ガーベージコレクションが削除するセッションの有効期限を設定
        // (30日以上経っているものに対してだけ100分の1の確率で削除)
        ini_set('session.gc_maxlifetime', 60*60*24*30);
        // プラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
        ini_set('session.cookie_lifetime', 60*60*24*30);
        // セッションを使う
        session_start();
        // 現在のセッションIDを新しく生成したものと置き換える
        // (なりすましのセキュリティ対策, 同じセッションIDを使い続けるといずれ情報を把握されるから)
        session_regenerate_id();
    }
}

?>