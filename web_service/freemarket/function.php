<?php

    // ログを出す
    error_reporting(E_ALL);
    // 画面にエラーを表示させるか
    ini_set("display_errors", "On");
    // ログを取る
    ini_set('log_errors','on');
    // ログファイルの出力先を決める
    ini_set('error_log', 'php.log');

    // デバッグ
    // サーバーにデプロイしてリリースした時はtrueからfalseにする
    $debug_flag = true;
    // デバッグログ関数
    function debug($str) {
        global $debug_flag;
        if (!empty($debug_flag)) {
            error_log('デバッグ:'.$str);
        }
    }

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

    // 画面表示処理開始ログを吐き出し関数
    function debugLogStart() {
        debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
        debug('セッションID:'.session_id());
        debug('セッション変数の中身:'.print_r($_SESSION, true));
        debug('現在日時タイムスタンプ:'.time());
        // セッションにタイムスタンプ情報があれば表示
        if (!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])) {
            debug('ログイン期限日時タイムスタンプ:'.($_SESSION['login_date'] + $_SESSION['login_limit']));
        }
    }

    // 定数
    // エラーメッセージを定数化
    define('MSG01', '入力必須です');
    define('MSG02', 'Emailの形式で入力してください');
    define('MSG03', 'パスワード(再入力)が合っていません');
    define('MSG04', '半角英数字のみご利用いただけます');
    define('MSG05', '6文字以上で入力してください');
    define('MSG06', '256文字以内で入力してください');
    define('MSG07', 'Emailが重複しています');
    define('MSG08', '時間をおいて再度登録をお願い致します');
    define('MSG09', 'メールアドレスまたはパスワードが違います');

    // 未入力のバリデーション関数
    function validRequired($inStr) {
        if (empty($inStr)) {
            return false;
        }

        return true;
    }

    // emailの未入力のバリデーション関数
    function validEmail($inStr) {
        if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $inStr)) {
            return false;
        }

        return true;
    }

    // emailの重複チェック
    function validEmailDup($inEmail) {

        try {
            $dbh = dbConnect();
            $sql = 'SELECT * FROM users WHERE email = :email';
            $data = array(':email' => $inEmail);

            // クエリ実行
            $resultPost = false;
            $stm = queryPost($dbh, $sql, $data, $resultPost);
            $result = $stm->fetch(PDO::FETCH_ASSOC);
            if (!empty($result)) {
                // 重複している
                return MSG07;
            }
        } catch(Exception $e) {
            error_log('エラー発生:', $e->getMessage());
            return MSG08;
        }

        return null;
    }

    // 同値のバリデーション関数
    function validMatch($inStr, $inStr2) {
        if ($inStr !== $inStr2) {
            return false;
        }

        return true;
    }

    // 最小文字数のバリデーション関数
    function validMinLen($inStr, $inMin = 6) {
        if (mb_strlen($inStr) < $inMin) {
            return false;
        }

        return true;
    }

    // 最大文字数のバリデーション関数
    function validMaxLen($inStr, $inMax = 255) {
        if (mb_strlen($inStr) > $inMax) {
            return false;
        }

        return true;
    }

    // 半角のバリデーション関数
    function validHalf($inStr) {
        if (!preg_match("/^[a-zA-Z0-9]+$/", $inStr)) {
            return false;
        }

        return true;
    }

    // DB接続
    function dbConnect() {
        // DBへの接続準備
        $dsn = 'mysql:dbname=freemarket;host-localhost;charset-utf8';
        $user = 'root';
        $password = 'root';
        $options = array(
            // SQL実行失敗時の例外をスロー
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // デフォルトフェッチモードを連想配列フォーマットに
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // 一度結果セットをすべて取得して、サーバー負荷を軽減
            // SELECTで得た結果からromCountメソッドを使えるように
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true, 
        );

        // PDOオブジェクト生成(DB接続)
        $dbh = new PDO($dsn, $user, $password, $options);

        return $dbh;
    }

    // DBにSQLを投げる
    function queryPost($dbh, $sql, $data, &$outResultPost) {
        // SQL文(クエリー作成)
        $stmt = $dbh->prepare($sql);
        // ブレースホルダーに値を設定、SQL文を実行
        if ($stmt->execute($data)) {
            $outResultPost = true;
        }
        else {
            $outResultPost = false;
        }

        return $stmt;
    }
?>