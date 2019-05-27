<?php

// ↓の2行は定型文としよう
// E_STRICTレベル以外のエラーを報告する
error_reporting(E_ALL);
// ログを取る
ini_set('log_errors','on');
// ログファイルの出力先を決める
ini_set('error_log', 'php.log');

error_log(print_r($_POST, true));

// POST送信値を取得
if (!empty($_POST)) {
    if (!empty($_POST["email"])) {
        $email = $_POST['email'];

        // todo DBに接続
        $dsn = 'mysql:dbname=ajax_email;host=localhost;charset=utf8';
        $user = 'root';
        $password = 'root';
        $option = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
        );
        $dbn = new PDO($dsn, $user, $password, $option);

        // todo DBからemailが存在するかチェック
        $stmt = $dbn->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(array(':email' => $email));

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!empty($result)) {
            // todo 入力したemailがDBに存在した
            echo json_encode(array('errorFlg' => false, 'msg' => 'メールが見つかった'));
        }
        else {
            echo json_encode(array('errorFlg' => true, 'msg' => 'メールが見つからない'));
        }
    }
}

?>