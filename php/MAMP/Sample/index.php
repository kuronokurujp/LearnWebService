<?php
    // ↓の2行は定型文としよう
    // E_STRICTレベル以外のエラーを報告する
    error_reporting(E_ALL);
    // 画面にエラーを表示させるか
    ini_set("display_errors", "On");

    // 1. post送信されていた場合
    if (!empty($_POST)) {
        define('MSG01', '入力必須です');
        define('MSG02', 'Emailの形式で入力してください');
        define('MSG03', 'パスワード「再入力」が合っていません');
        define('MSG04', '半角英数字のみご利用いただけます');
        define('MSG05', '6文字以上で入力してください');

        $err_flg = false;
        $err_msg = array();

        // 2. フォームが入力されていない場合
        if (empty($_POST['email'])) {
            $err_msg['email'] = MSG01;
        }

        if (empty($_POST['pass'])) {
            $err_msg['pass'] = MSG01;
        }

        if (empty($_POST['pass_retype'])) {
            $err_msg['pass_retype'] = MSG01;
        }

        // エラーメッセージがない
        // 入力形式が正しいかチェック
        if (empty($err_msg)) {
            $email = htmlspecialchars($_POST['email'], ENT_QUOTES);
            $pass = htmlspecialchars($_POST['pass'], ENT_QUOTES);
            $pass_re = htmlspecialchars($_POST['pass_retype'], ENT_QUOTES);

            // 3. emailの形式でない場合
            if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
                $err_msg['email'] = MSG02;
            }

            // 4. パスワードとパスワード再入力が合っていない場合
            if ($pass !== $pass_re) {
                $err_msg['pass'] = MSG03;
            }

            if (empty($err_flg)) {
                // 5. パスワードとパスワード再入力が半角英数字でない場合
                if (!preg_match("/^[a-zA-Z0-9]+$/", $pass)) {
                    $err_msg['pass'] = MSG04;
                }
                elseif (mb_strlen($pass) < 6) {
                // 6. パスワードとパスワード再入力が6文字以上でない場合
                    $err_msg['pass'] = MSG05;
                }

                if (empty($err_msg)) {
                    // DBにデータ送信

                    // DB接続
                    $dsn = 'mysql:dbname=php_sample01;host=localhost;charset=utf8';
                    $user = 'root';
                    $password = 'root';
                    // このオプション設定は定型文
                    $option = array(
                        // SQL実行失敗時の例外スロー
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        // デフォルトフェッチモードを連想配列形式に設定
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        // SELECTで得た結果にしてもrowCountメソッドを使えるようにする
                        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                    );

                    // PDOオブジェクト生成(DBへ接続)
                    $dbn = new PDO($dsn, $user, $password, $option);

                    // SQL文(クエリー作成)
                    $stmt = $dbn->prepare('INSERT INTO users (email, pass, login_time) VALUES (:email, :pass, :login_time)');

                    // Insert命令の穴抜けの入力枠に値を設定してSQL文を実行
                    $stmt->execute(array(':email' => $email, ':pass' => $pass, ':login_time' => date('Y/m/d H:i:s')));

                    header("Location:mypage.php");
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>ホームページのタイトル</title>
        <style>
            body{
                margin: 0 auto;
                padding: 150px;
                width: 25%;
                background: #fbfbfa;
            }
            h1 { color: #545454; font-size; 20px;}
            form {
                overflow: hidden;
            }
            input[type="text"]{
                color: #545454;
                height: 60px;
                width: 100%;
                padding: 5px 10px;
                font-size: 16px;
                display: block;
                margin-bottom: 10px;
                box-sizing: border-box;
            }
            input[type="password"]{
                color: #545454;
                height: 60px;
                width: 100%;
                padding: 5px 10px;
                font-size: 16px;
                display: block;
                margin-bottom: 10px;
                box-sizing: border-box;
            }
            input[type="submit"]{
                border: none;
                padding: 15px 30px;
                margin-bottom: 15px;
                background: #3d3938;
                color: white;
                float: right;
            }
            input[type="submit"]:hover{
                background: #111;
                cursor: pointer;
            }
            a {
                color: #545454;
                display: block;
            }
            a:hover {
                text-decoration: none;
            }
            .err_msg {
                color: #ff4a4b;
            }
        </style>
    </head>
    <body>
        <h1>ユーザー登録</h1>
        <!-- 自分自身へpost送信する action=""となっていると自分ということになるaction="mypage.php"とするとmypage.phpにpost送信-->
        <form action="" method="post">
            <span class="err_msg"><?php if (!empty($err_msg['email'])) echo $err_msg['email']; ?></span>
            <input type="text" name="email" placeholder="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">

            <span class="err_msg"><?php if (!empty($err_msg['pass'])) echo $err_msg['pass']; ?></span>
            <input type="password" name="pass" placeholder="パスワード" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">

            <span class="err_msg"><?php if (!empty($err_msg['pass_retype'])) echo $err_msg['pass_retype']; ?></span>
            <input type="password" name="pass_retype" placeholder="パスワード再入力" value="<?php if(!empty($_POST['pass_retype'])) echo $_POST['pass_retype']; ?>">
            <input type="submit" value="送信">
        </form>
        <a href="mypage.php">マイページ</a>
    </body>
</html>