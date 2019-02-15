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


        $valid_err_msg = '';
        // バリデーションチェック
        if (!validRequired($email, $valid_err_msg)) {
            $err_msg['email'] = $valid_err_msg;
        }
        if (!validRequired($pass, $valid_err_msg)) {
            $err_msg['pass'] = $valid_err_msg;
        }

        // emailのフォーマットチェック
        if (!validEmail($email, $valid_err_msg)) {
            $err_msg['email'] = $valid_err_msg;
        }

        // パスワードの半角英数字チェック
        if (!validHalf($pass, $valid_err_msg)) {
            $err_msg['pass'] = $valid_err_msg;
        }

        // パスワードの最小文字数チェック
        if (!validMinLen($pass, $valid_err_msg)) {
            $err_msg['pass'] = $valid_err_msg;
        }

        // パスワードの最大文字数チェック
        if (!validMaxLen($pass, $valid_err_msg)) {
            $err_msg['pass'] = $valid_err_msg;
        }

        if (empty($err_msg)) {
            debug('バリデーションOK');

            // 例外処理
            try {
                // DB接続
                $dbh = dbConnect();
                // SQL文作成
                $sql = 'SELECT password,id FROM users WHERE email=:email AND delete_flag = 0';
                $data = array(':email'=>$email);
                // クエリ実行
                $result_query_post = false;
                $stmt = queryPost($dbh, $sql, $data, $result_query_post);
                if ($stmt) {
                    debug('クエリ成功');

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
                else {
                    debug('クエリ失敗');
                    $err_msg['common'] = MSG08;
                }
            }
            catch (Exception $e) {
                dbErrorLog($e);
                $err_msg['common'] = MSG08;
            }
        }
    }
    debug('画面表示処理終了------------------------');
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

<!-- 処理成功時にメッセージを出す-->
<p id="js-show-msg" style="display:none;" class="msg-slide">
  <?php echo getSessionFlash('msg_success'); ?>
</p>

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
                パスワードを忘れた方は<a href="passRemindSend.php">コチラ</a>
            </form>
        </div>
    </section>
</div>

<?php
    require('footer.php')
?>
