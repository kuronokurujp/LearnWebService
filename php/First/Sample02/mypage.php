<?php
    // ↓の2行は定型文としよう
    // E_STRICTレベル以外のエラーを報告する
    error_reporting(E_ALL);
    // 画面にエラーを表示させるか
    ini_set("display_errors", "On");

    session_start();

    // ログイン情報がないならログインページに戻る
    if (empty($_SESSION['login'])) {
        header("Location:login.php");
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
            h1 { color: #545454; font-size: 20px;}
            a {
                color: #545454;
                display: block;
            }
            a:hover{
                text-decoration: none;
            }
        </style>
    </head>
    <body>
            <!-- ログイン情報はcookieに保存されるので、デバッグする時はcookieを消すのが必要になる -!>
            <?php if (!empty($_SESSION['login'])) { ?>
                <h1>マイページ</h1>
                <section>
                    <p>
                        あなたのemailは info@webukatu.com です。
                        あなたのpassは password です。
                    </p>
                    <a href="login.php">ログイン画面へ</a>
                </section>

            <?php }else { ?>

                <p>ログインしていないと見れません。</p>

            <?php } ?>
    </body>
</html>