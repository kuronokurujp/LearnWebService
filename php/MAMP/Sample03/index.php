<?php
    // E_STRICTレベル以外のエラーを報告
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');

    // post送信している
    if (!empty($_POST)) {
        // 変数にユーザー情報を代入
        $to = $_POST['email'];
        $subject = $_POST['subject'];
        $comment = $_POST['comment'];

        // メッセージ表示用の変数を用意
        $msg = '';

        // メール送信プログラムであるphpファイルを読み込む
        include('mail.php');
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'>
        <title>ホームページのタイトル</title>
        <style>
            body{
                margin: 0 auto;
                padding: 150px;
                width: 25%;
                background: #fbfbfa;
            }
            h1{ color: #545454; font-size: 20px;}
            form{
                overfloat: hidden;
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
            textarea{
                color: #545454;
                height: 200px;
                width: 100%;
                padding: 5px 10px;
                font-size: 16px;
                display: block;
                margin-bottom: 10px;
                box-sizing: border-box;
                border-color: #ddd;
            }
            input[type="submit"]:hover{
                background: #111;
                cursor: pointer;
            }
        </style>
    </head>

    <body>
            <p><?php if (!empty($msg)) echo $msg; ?></p>
            <h1>お問い合わせ</h1>
            <form method="post">
                <input type="text" name="email" placeholder="email" value="<?php if (!empty($_POST['email'])) echo $_POST['email']; ?>">
                <input type="text" name="subject" placeholder="件名" value="<?php if (!empty($_POST['subject'])) echo $_POST['subject']; ?>">
                <textarea name="comment" placeholder="内容" value="<?php if (!empty($_POST['comment'])) echo $_POST['comment']; ?>"></textarea>
                <input type="submit" value="送信">
            </form>
    </body>
</html>