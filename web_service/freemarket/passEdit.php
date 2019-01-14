<?php
    // 共通関数を呼ぶ
    require('function.php');

    // ログ開始！
    debug('---------------------------------');
    debug('パスワード変更ページ');
    debug('---------------------------------');
    debugLogStart();

    require('auth.php');

    $dbFormData = getUser($_SESSION['user_id']);

    debug('取得したユーザー情報:'.print_r($dbFormData, true));

    $err_msg = array();

    if (!empty($_POST)) {
      debug('POST送信がありました。');
      debug('POST情報:'.print_r($_POST, true));

      $pass_old = $_POST['pass_old'];
      $pass_new = $_POST['pass_new'];
      $pass_new_re = $_POST['pass_new_re'];

      // 未入力チェック
      if (!validRequired($pass_old)) {
        $err_msg['pass_old'] = MSG01;
      }

      if (!validRequired($pass_new)) {
        $err_msg['pass_new'] = MSG01;
      }

      if (!validRequired($pass_new_re)) {
        $err_msg['pass_new_re'] = MSG01;
      }

      if (empty($err_msg)) {
        debug('未入力チェックOK');

        // パスワードの内容チェック
        $output_err_msg = '';
        if (!validPass($pass_old, $output_err_msg)) {
          $err_msg['pass_old'] = $output_err_msg;
        }

        if (!validPass($pass_new, $output_err_msg)) {
          $err_msg['pass_new'] = $output_err_msg;
        }

        // 古いパスワードがDBで登録しているパスワードと一致するか
        if (!password_verify($pass_old, $dbFormData['password'])) {
          $err_msg ['pass_old'] = MSG12;
        }

        // 古いパスワードと新しいパスワードが同じでないか
        if ($pass_old === $pass_new) {
          $err_msg['pass_new'] = MSG13;
        }

        // 新しいパスワードと再入力した新しいパスワードが一致するか 
        if (!validMatch($pass_new, $pass_new_re)) {
          $err_msg['pass_new_re'] = MSG03;
        }

        if (empty($err_msg)) {
          debug('バリデーションチェックOK');

          try {
            $dbh = dbConnect();

            // SQL文作成
            $sql = 'UPDATE `users` SET `password` = :pass WHERE `id` = :id';
            // SQLのプレースホルダーを作成
            $data = array(':pass' => password_hash($pass_new, PASSWORD_DEFAULT), ':id' => $_SESSION['user_id']);

            $resultPost = false;
            $stmt = queryPost($dbh, $sql, $data, $resultPost);

            if ($stmt) {
              debug('クエリ成功');
              // 変更結果のメッセージをJSで出す
              $_SESSION['msg_success'] = SUC01;

              // メール送信
              $username = ($dbFormData['username']) ? $dbFormData['username'] : '名無し';
              $to = $dbFormData['email'];
              $form = 'info@gmail.com';
              $subject = 'パスワード変更通知';
              // メール本文をヒアドキュメント形式で作成する
              // メリット:送信したメール本文とフォーマットが一致する
              $common = <<<EOT
{$username} さん
パスワードが変更されました。

///////////////////////
XXXXカスタマーサポートセンター
///////////////////////
EOT;
              sendMail($form, $to, $subject, $common);

              header('Location:mypage.php');
            }
            else {
              debug('クエリに失敗');
              $err_msg['common'] = MSG08;
            }
          }
          catch (Exception $e) {
            error_log('エラー:'.$e->getMessage());
            $err_msg['common'] = MSG08;
          }
        }
      }
    }

    debug('画面処理終了');

?>

<?php
  $siteTitle = 'パスワード変更';
  require('head.php');
?>

<body class="page-passEdit page-2colum page-logined">
<style>
  .form {
    margin-top: 50px;
  }
</style>

<!-- メニュー -->
<?php
  require('header.php');
?>

<!-- メインコンテンツ -->
<div id="contents" class="site-width">
  <h1 class="page-title">パスワード変更</h1>
  <!-- Main -->
  <section id="main" >
    <div class="form-container">
      <form action="" method="post" class="form">
        <div class="area-msg">
          <?php
            echo getErrorMessage($err_msg, 'common');
          ?>
        </div>

        <label class="<?php if (!empty($err_msg['pass_old'])) echo 'err'; ?>">
          古いパスワード
          <input type="password" name="pass_old" value="<?php echo getFormData('pass_old',!empty($err_msg['pass_old'])); ?>">
        </label>
        <div class="area-msg">
          <?php
            echo getErrorMessage($err_msg, 'pass_old');
          ?>
        </div>

        <label class="<?php if (!empty($err_msg['pass_new'])) echo 'err'; ?>">
          新しいパスワード
          <input type="password" name="pass_new" value="<?php echo getFormData('pass_new', !empty($err_msg['pass_new'])); ?>">
        </label>
        <div class="area-msg">
          <?php
            echo getErrorMessage($err_msg, 'pass_new');
          ?>
        </div>

        <label class="<?php if (!empty($err_msg['pass_new_re'])) echo 'err'; ?>">
          新しいパスワード（再入力）
          <input type="password" name="pass_new_re" value="<?php echo getFormData('pass_new_re', !empty($err_msg['pass_new_re'])); ?>">
        </label>
        <div class="area-msg">
          <?php
            echo getErrorMessage($err_msg, 'pass_new_re');
          ?>
        </div>

        <div class="btn-container">
          <input type="submit" class="btn btn-mid" value="変更する">
        </div>
      </form>
    </div>
  </section>
  
  <!-- サイドバー -->
  <?php
    require('sidebar_mypage.php');
  ?>
</div>

<!-- footer -->
<?php
  require('footer.php');
?>