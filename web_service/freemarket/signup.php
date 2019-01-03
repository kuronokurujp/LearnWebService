<?php

require('function.php');

// エラーメッセージの配列
$err_msg = array();

// dbアクセス結果用
$dbRst = false;

// post送信しているか
if (!empty($_POST)) {
    // ユーザー情報を設定
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_retype'];

    // バリデーションチェック
    if (!validRequired($email)) {
        $err_msg['email'] = MSG01;
    }
    if (!validRequired($pass)) {
        $err_msg['pass'] = MSG01;
    }
    if (!validRequired($pass_re)) {
        $err_msg['pass_retype'] = MSG01;
    }

    // バリデーションチェックで問題ないか
    if (empty($err_msg)) {
        // emailのフォーマットチェック
        if (!validEmail($email)) {
            $err_msg['email'] = MSG02;
        }

        $msg = validEmailDup($email);
        if (!empty($msg)) {
            $err_msg['email'] = $msg;
        }

        // パスワードとパスワード再入力が合っているか
        if (!validMatch($pass, $pass_re)) {
            $err_msg['pass'] = MSG03;
        }

        // バリデーションチェックで問題ないか
        if (empty($err_msg)) {

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

            // バリデーションチェックで問題ないか
            if (empty($err_msg)) {
                $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
                try {
                    //code...
                    // DBへの接続準備
                    $dbh = dbConnect();

                    // SQLを投げる
                    $resultQueryPost = false;
                    $stm = queryPost(
                    $dbh, 
                    'INSERT INTO users (email, password, login_time, create_date) 
                    VALUES (:email, :pass, :login_time, :create_date)', 
                    array(':email' => $email, ':pass' => $pass_hash, 
                          ':login_time' => date('Y-m-d H:i:s'),
                          ':create_date' => date('Y-m-d H:i:s')),
                    $resultQueryPost);

                    // SQL実行結果が成功なら
                    header("Location:mock/mypage.html");

                } catch (\Throwable $th) {
                    //throw $th;
                    error_log('エラー発生:', $th->getMessage());
                    $err_msg['common'] = MSG08; 
                }
            }
        }
    }
}
?>

<?php
    $siteTitle = "ユーザ登録";
    require('head.php');
?>

<body class="page-signup page-1colum"> 

<!-- メニュー -->
<?php
    require('header.php');
?>

<!-- メインコンテンツ -->
<div id="contents" class="site-width">
    <!-- Main -->
    <section id="main">
        <div class="form-container">
            <form action="" method="post" class="form">
                <h2 class="title">ユーザー登録</h2>
                <div class="area-msg">
                    <?php
                        if (!empty($err_msg['common'])) {
                            echo $err_msg['common'];
                        }
                    ?>
                </div>
                <label class="<?php if (!empty($err_msg['email'])) echo 'err'; ?>">
                    Email
                    <input type="text" name="email" value=
                    <?php
                        if (!empty($_POST['email'])) {
                            echo $_POST['email'];
                        }
                    ?>>
                </label>
                <div class="area-msg">
                    <?php
                        if (!empty($err_msg['email'])) {
                            echo $err_msg['email'];
                        }
                        ?>
                </div>

                <label class="<?php if (!empty($err_msg['pass'])) echo 'err'; ?>">
                    パスワード <span style="font-size:12px">※英数字6文字以上</span>
                    <input type="password" name="pass" value=
                    <?php
                        if (!empty($_POST['pass'])) {
                            echo $_POST['pass'];
                        }
                    ?>>
                </label>
                <div class="area-msg">
                    <?php
                        if (!empty($err_msg['pass'])) {
                            echo $err_msg['pass'];
                        }
                        ?>
                </div>

                <label class="<?php if (!empty($err_msg['pass_retype'])) echo 'err'; ?>">
                    パスワード（再入力）
                    <input type="password" name="pass_retype" value=
                    <?php
                        if (!empty($_POST['pass_retype'])) {
                            echo $_POST['pass_retype'];
                        }
                    ?>>
                </label>
                <div class="area-msg">
                    <?php
                        if (!empty($err_msg['pass_retype'])) {
                            echo $err_msg['pass_retype'];
                        }
                    ?>
                </div>

                <div class="btn-container">
                    <input type="submit" class="btn btn-mid" value="登録する">
                </div>
            </form>
        </div>
    </section>
</div>

<!-- footer -->
<?php
    require('footer.php');
?>