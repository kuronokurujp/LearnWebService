<?php

// ログイン認証・自動ログアウト機能
// ログインしている場合
if (!empty($_SESSION['login_date'])) {
    debug('ログイン済みユーザーです。');

    // 現在日時が最終ログイン日時+有効期限を超えていた場合
    if ($_SESSION['login_date'] + $_SESSION['login_limit'] < time()) {
        debug('ログイン有効期限オーバーです。');

        session_destroy();
        // ログインページへ
        header('Location:login.php');
    }
    // ログイン有効期限以内
    else {
        debug('ログイン有効期限以内です。');
        // 最終ログイン日時を現在日時に変更
        // ページの閲覧から指定時間まで閲覧してない時にセッション切れを実現するため
        $_SESSION['login_date'] = time();
        // 現在実行しているphpファイルをカレントディレクトリパスを含めて取得する
        // basenameメソッドでファイル名のみ取得してファイル名から判別している
        // mypage.phpでもauth.phpを呼び出しているので、mypage.php -> mypage.phpと無限にここの処理が呼び出されるので
        // これを防ぐために実行しているphpファイルが何かを調べてページ遷移条件を決めている 
        if (basename($_SERVER['PHP_SELF']) === 'login.php') {
            debug('マイページへ遷移します。');
            header('Location:mypage.php');
        }
    }
}
// ログインしていない場合
else {
    if (basename($_SERVER['PHP_SELF']) !== 'login.php') {
        debug('未ログインユーザーです。');
        header('Location:login.php');
    }
}
?>