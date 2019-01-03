<?php

// 共通関数読み込み
require('function.php');

// ログ開始！
debug('---------------------------------');
debug('ログインページ');
debug('---------------------------------');
debugLogStart();

// ログイン認証
require('auth.php');

$err_msg = array();

// ログイン画面処理
if (!empty($_POST)) {
    debug('POST送信があります。');

    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false;

    // バリデーションチェック
    if (!validRequired($email)) {
        $err_msg['email'] = MSG01;
    }
    if (!validRequired($pass)) {
        $err_msg['pass'] = MSG01;
    }

    // emailのフォーマットチェック
    if (!validEmail($email)) {
        $err_msg['email'] = MSG02;
    }

    // パスワードの半角英数字チェック
    if (!validHalf($pass)) {
        $err_msg['pass'] = MSG04;
    }

    // パスワードの最小文字数チェック
    if (!validMinLen($pass)) {
        $err_msg['pass'] = MSG05;
    }

    // パスワードの最大文字数チェック
    if (!validMaxLen($pass)) {
        $err_msg['pass'] = MSG06;
    }

    if (empty($err_msg)) {
        debug('バリデーションOK');

        // 例外処理
        try {

            // DB接続
            $dbh = dbConnect();
            // SQL文作成
            $sql = 'SELECT password,id FROM users WHERE email=:email';
            $data = array(':email'=>$email);
            // クエリ実行
            $result_query_post = false;
            $stmt = queryPost($dbh, $sql, $data, $result_query_post);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            debug('クエリ結果の中身:'.print_r($result,true));

            if (!empty($result) && password_verify($pass, array_shift($result))) {
                debug('パスワードがマッチしました');

                // ログイン有効期限(デフォルトを1時間に)
                $sesLimit = 60 * 60;
                // 最終ログイン日時を現在日時に
                $_SESSION['login_date'] = time();

                // ログイン保持にはチェックがある
                if ($pass_save) {
                    debug('ログイン保持にチェックがあります');
                    $_SESSION['login_limit'] = $sesLimit * 24 * 30;
                }
                else {
                    debug('ログイン保持にチェックがない');
                    // 次回からログイン保持しないので、すぐにセッション切れになるように時間設定する
                    $_SESSION['login_limit'] = $sesLimit;
                }

                // ユーザーIDを格納
                $_SESSION['user_id'] = $result['id'];

                debug('セッション変数の中身:'.print_r($_SESSION, true));
                debug('マイページへ遷移します');

                header('Location:mypage.php');
            }
            else {
                debug('パスワードがミスマッチです');
                $err_msg['common'] = MSG09;
            }
        }
        catch (Exception $e) {
            error_log('エラー発生:'. $e->getMessage());
            $err_msg['common'] = MSG08;
        }
    }
}
debug('画面表示処理終了------------------------');
?>
<?php


?>
<?php
    $siteTitle = 'ログイン';
    require('head.php')
?>

<body  class="page-login page-1colum">
<!-- メニュー -->
<?php
    require('header.php');
?>

<!-- メインコンテンツ -->
<div id="contents" class="site-width">
    <!-- Main -->
    <section id="main">
        <div>
            <form action="" method="post" class="form">
                <h2 class="title">ログイン</h2>
                <div class="area-msg">
                    <?php
                        if (!empty($err_msg['common'])) {
                            echo $err_msg['common'];
                        }
                    ?>
                </div>

                <label class="<?php if (!empty($err_msg['email'])) echo 'err'; ?>">
                メールアドレス
                <input type="text" name="email" value="<?php if (!empty($_POST['email'])) echo $_POST['email']; ?>">
                </label>
                <div class="area-msg">
                    <?php
                        if (!empty($err_msg['email'])) {
                            echo $err_msg['email'];
                        }
                    ?>
                </div>

                <label class="<?php if (!empty($err_msg['pass'])) echo 'err'; ?>">
                パスワード 
                <input type="password" name="pass" value="<?php if (!empty($_POST['pass'])) echo $_POST['pass']; ?>">
                </label>
                <div class="area-msg">
                    <?php
                        if (!empty($err_msg['pass'])) {
                            echo $err_msg['pass'];
                        }
                    ?>
                </div>

                <label>
                    <input type="checkbox" name="pass_save">次回ログインを省略する
                </label>
                <div class="btn-container">
                    <input type="submit" class="btn btn-mid" value="ログイン">
                </div>
                パスワードを忘れた方は<a href="/mock/passRemindSend.html">コチラ</a>
            </form>
        </div>
    </section>
</div>

<?php
    require('footer.php')
?>
