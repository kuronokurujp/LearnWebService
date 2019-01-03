<?php

    // 共通関数を呼ぶ
    require('function.php');

    // ログ開始！
    debug('---------------------------------');
    debug('ログアウトページ');
    debug('---------------------------------');
    debugLogStart();

    // セッションを削除
    debug('ログアウトをします。');
    session_destroy();

    // ログインページに
    debug('ログインページに遷移します。');
    header('Location:login.php');
?>