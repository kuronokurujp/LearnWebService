<?php
    // 共通関数の読み込み
    require('function.php');

    // ログ開始！
    debug('---------------------------------');
    debug('パスワード再発行の認証キー入力ページ');
    debug('---------------------------------');
    debugLogStart();

    // SESSIONに認証キーがあるかどうか調べる
    if (empty($_SESSION['auth_key'])) {
      // 認証キーがなければ認証発行ページに遷移
      header('Location:passRemindSend.php');
    }

    $err_msg = array();

    if (!empty($_POST)) {
      debug('POSTデータがある');
      debug('POST情報：'.print_r($_POST, true));

      // フォームに入力した認証キーを取得
      $auth_key = $_POST['token'];

      $valid_err_msg = '';

      // 未入力チェック
      if (!validRequired($auth_key, $valid_err_msg)) {
        $err_msg['token'] = $valid_err_msg;
      }

      if (empty($err_msg)) {
        debug('未入力チェックOK');

        $limitLen = 0;
        if(!validLength($auth_key, $limitLen, $valid_err_msg)) {
          $err_msg['token'] = $limitLen . MSG14;
        }

        if (!validHalf($auth_key, $valid_err_msg)) {
          $err_msg['token'] = $valid_err_msg;
        }

        if (empty($err_msg)) {
          debug('バリデーションOK!');

          if ($auth_key !== $_SESSION['auth_key']) {
            $err_msg['token'] = MSG15;
          }

          if (time() > $_SESSION['auth_key_limit']) {
            $err_msg['token'] = MSG16;
          }

          if (empty($err_msg)) {
            debug('認証OK');
          }

          // パスワード生成
          $pass = makeRandKey();

          try {
            // DBを接続して新しいパスワードにする
            $dbt = dbConnect();

            $sql = 'UPDATE `users` SET `password` = :pass WHERE `email` = :email AND `delete_flag` = 0' ;
            $data = array(':pass' => password_hash($pass, PASSWORD_DEFAULT), ':email' => $_SESSION['auth_email']);

            $result_flag = false;
            $stmt = queryPost($dbt, $sql, $data, $result_flag);

            if ($stmt) {
              debug('クエリ成功');

              $from = 'info@gmail.com';
              $to = $_SESSION['auth_email'];
              $subject = '【パスワード再発行完了】';
              $comment = <<<EOT
本メールアドレス宛にパスワードの再発行を致しました。
下記のURLに再発行パスワードをご入力頂き、ログインしてください。
http://localhost:80/login.php
再発行パスワード{$pass}
※ログイン後にパスワードの変更をお願い致します。

カスタムサポートセンター
URL    info.php
E-MAIL info@gmail.com
EOT;
              sendMail($from, $to, $subject, $comment);

              session_unset();
              $_SESSION['msg_success'] = SUC01;
              debug('セッション変数の中身：'.print_r($_SESSION, true));

              header('Location:login.php');
            }
            else {
              debug('DBに接続失敗かSQL発行失敗している');
              $err_msg['common'] = MSG08;
            }
          }
          catch (Exception $e) {
            error_log('エラー発生'.$e->getMessage());
            $err_msg['common'] = MSG08;
          }

        }
      }
    }

    debug('画面処理終了');
?>

<?php
  $siteTitle = 'パスワード再発行';
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
  <section id="main" >

    <div class="form-container">

      <form action="" method="post" class="form">
        <p>ご指定のメールアドレスお送りした【パスワード再発行認証メール】内にある「認証キー」をご入力ください。</p>
        <div class="area-msg">
          <?php
            echo getErrorMessage($err_msg, 'common');
          ?>
        </div>
        <label class="<?php if (!empty($err_msg['token'])) echo 'err'; ?>">
          認証キー
          <input type="text" name="token" valie="<?php echo getFormData('token', false, !empty($err_msg['token'])); ?>">
        </label>
        <div class="area-msg">
          <?php
            if (!empty($err_msg['token'])) {
              echo $err_msg['token'];
            }
          ?>
        </div>
        <div class="btn-container">
          <input type="submit" class="btn btn-mid" value="再発行する">
        </div>
      </form>
    </div>
    <a href="passRemindSend.php">&lt; パスワード再発行メールを再度送信する</a>
  </section>

</div>

<!-- footer -->
<?php
  require('footer.php');
?>