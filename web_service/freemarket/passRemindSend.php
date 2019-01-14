<?php
    // 共通関数を呼ぶ
    require('function.php');

    // ログ開始！
    debug('---------------------------------');
    debug('パスワード再発行の認証メール送信ページ');
    debug('---------------------------------');
    debugLogStart();

    // ログイン認証は不要、なぜならログインしていない人が使う前提の画面だから

    $err_msg = array();

    // フォーム入力があるかチェック
    if (!empty($_POST)) {
      debug('POST送信があります');
      debug('POST情報:'. print_r($_POST, true));

      $email = $_POST['email'];

      if (!validRequired($email)) {
        $err_msg['email'] = MSG01;
      }

      if (empty($err_msg)) {
        debug('未入力チェックOK');

        if (!validEmail($email)) {
          $err_msg['email'] = MSG02;
        }

        if (!validMaxLen($email))  {
          $err_msg['email'] = MSG6;
        }

        if (empty($err_msg)) {
          debug('バリデーションチェックOK');

          // DB接続して認証パスワードのメール送信
          try {
            $dbh = dbConnect();

            $sql = 'SELECT COUNT(*) FROM `users` WHERE `email` = :email AND `delete_flag` = 0';
            $data = array(':email' => $email); 

            // クエリ送信
            $result_flag = false;
            $stmt = queryPost($dbh, $sql, $data, $result_flag);

            // クエリ結果の値を取得
            $result = $stmt->Fetch(PDO::FETCH_ASSOC);

            if ($stmt && array_shift($result)) {
              debug('フォームに入力したEmailがDBに保存されている');

              $_SESSION['msg_success'] = SUC03; 
              // 認証キーの作成
              $auth_key = makeRandKey();

              // メールを送信
              $from = 'info@gmail.com';
              $to = $email;
              $subject = 'パスワード再発行認証';

              $comment = <<<EOT
本メールアドレス宛にパスワード再発行のご依頼がありました。
下記のURLにて認証キーをご入力頂くとパスワードが再発行されます。

パスワード再発行認証キー入力ページ：http://localhost:80/passRemindRecieve.php
認証キー：{$auth_key}
※認証キーの有効期限は30分となります。

認証キーを再発行されたい場合は下記ページより再度再発行をお願い致します。
http://localhost:80/passRemindSend.php

/////////////////////////////
カスタマセンター
URL    https/
E-mail info@gmail.com
/////////////////////////////
EOT;
              sendMail($from, $to, $subject, $comment);

              // 認証に必要な情報をセッションに保存
              $_SESSION['auth_key'] = $auth_key;
              $_SESSION['auth_email'] = $email;
              // 認証キーを発行してからの有効時間の設定
              $_SESSION['auth_key_limit'] = time() + (60 *30);
              debug('セッションの中身：'.print_r($_SESSION, true));

              // 認証ページへ移動
              header('Location:passRemindRecieve.php');
            }
            else {
              // DBに未登録のemail
              debug('クエリに失敗したかDBにemailが登録していないかのどちらか');
              $err_msg['common'] = MSG08;
            }
          }
          catch (Exception $e) {
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG08;
          }
        }
      }
    }
?>

<?php
  $siteTitle = 'パスワード再発行メール送信';
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
        <div class="area-msg">
          <?php
            echo getErrorMessage($err_msg, 'common');
          ?>
        </div>

        <p>ご指定のメールアドレス宛にパスワード再発行用のURLと認証キーをお送り致します。</p>

        <label class="<?php if (!empty($err_msg['email'])) echo 'err'; ?>">
          Email
          <input type="text" name="email" value="<?php echo getFormData('email', !empty($err_msg['email'])); ?>">
        </label>
        <div class="area-msg">
          <?php
            if (!empty($err_msg['email'])) {
              echo $err_msg['email'];
            }
          ?>
        </div>

        <div class="btn-container">
          <input type="submit" class="btn btn-mid" value="送信する">
        </div>
      </form>
    </div>
    <a href="mypage.php">&lt; マイページに戻る</a>
  </section>

</div>

<!-- footer -->
<?php
  require('footer.php');
?>
