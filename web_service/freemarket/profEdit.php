<?php
    // 共通関数を呼ぶ
    require('function.php');

    // ログ開始！
    debug('---------------------------------');
    debug('プロフィール編集ページ');
    debug('---------------------------------');
    debugLogStart();

    require('auth.php');

    $dbFormData = getUser($_SESSION['user_id']);

    debug('取得したユーザー情報:'.print_r($dbFormData, true));

    $err_msg = array();

    // POSTを取得
    if (!empty($_POST)) {

      debug('post送信があります。');
      debug('post情報:'.print_r($_POST, true));

      $username = $_POST['username'];
      $tel = $_POST['tel'];
      // バリデーションチェックのため空文字なら０を代入
      $zip = !empty($_POST['zip']) ? $_POST['zip'] : 0;
      $addr = $_POST['addr'];
      $age = $_POST['age'];
      $email = $_POST['email'];

      $valid_err_msg = '';

      // DB情報と入力情報とが異なる場合はバリデーションチェックをする
      if ($dbFormData['username'] !== $username) { 
        // 名前の最大文字数チェック
        if (!validMaxLen($username, $valid_err_msg)) {
          $err_msg['username'] = $valid_err_msg;
        }
      }

      if ($dbFormData['tel'] !== $tel) {
        // 電話番号の形式チェック
        if (!validTel($tel, $valid_err_msg)) {
          $err_msg['tel'] = $valid_err_msg;
        }
      }

      // 郵便番号はint型なのでキャストして文字列から整数型に変換
      if ((int)$dbFormData['zip'] !== $zip) {
        if (!validZip($zip, $valid_err_msg)) {
          $err_msg['zip'] = $valid_err_msg;
        }
      }

      if ($dbFormData['addr'] !== $addr) {
        // 住所チェック
        if (!validMaxLen($addr, $valid_err_msg)) {
          $err_msg['addr'] = $valid_err_msg;
        }
      }

      if ($dbFormData['age'] !== $age) {
        // 年齢チェック
        if (!validMaxLen($age, $valid_err_msg)) {
          $err_msg['age'] = $valid_err_msg;
        }

        if (!validNumber($age, $valid_err_msg)) {
          $err_msg['age'] = $valid_err_msg;
        }
      }

      if ($dbFormData['email'] !== $email) {
        if (!validMaxLen($email, $valid_err_msg)) {
          $err_msg['email'] = $valid_err_msg;
        }

        if (empty($err_msg['email'])) {
          if (validEmailDup($email, $valid_err_msg)) {
            $err_msg['email'] = $valid_err_msg;
          }
        }

        if (!validEmail($email, $valid_err_msg)) {
          $err_msg['email'] = $valid_err_msg;
        }

        if (!validRequired($email, $valid_err_msg)) {
          $err_msg['email'] = $valid_err_msg;
        }
      }

      if (empty($err_msg)) {
        debug('バリデーションチェックOK!');

        // DB接続して情報を更新
        try {
          $db = dbConnect();

          $sql = 'UPDATE `users` SET `username`=:u_name, `tel`=:tel, `zip`=:zip, `addr`=:addr, `age`=:age, `email`=:email WHERE `id`=:u_id';
          $data = array(':u_name'=>$username, ':tel'=>$tel, ':zip'=>$zip, ':addr'=>$addr, ':age'=>$age, ':email'=>$email, ':u_id'=>$dbFormData['id']);

          $resultFlag = false;
          $stmt = queryPost($db, $sql, $data, $resultFlag);

          if ($stmt) {
            debug('クエリ成功！');
            debug('マイページへ遷移します。');

            header('Location:mypage.php');
          }
          else {
            debug('クエリ失敗!!!');
            $err_msg['common'] = MSG09;
          }
        }
        catch (Exception $e) {
          error_log($e->getMessage());
          $err_msg['common'] = MSG08;
        }
      }
      else {
        debug('バリデーションエラー:'.print_r($err_msg, true));
      }
    }
    debug('画面表示処理終了');
?>

<?php
  $siteTitle = 'プロフィール編集 ';
  require('head.php');
?>

<body class="page-profEdit page-2colum page-logined">

<!-- メニュー -->
<?php
  require('header.php');
?>

<!-- メインコンテンツ -->
<div id="contents" class="site-width">
  <h1 class="page-title">プロフィール編集</h1>
  <!-- Main -->
  <section id="main" >
    <div class="form-container">
      <form action="" method="post" class="form">
        <div class="area-msg">
          <?php
              if (!empty($err_msg['common'])) {
                  echo $err_msg['common'];
              }
          ?>
        </div>

        <label class="<?php if(!empty($err_msg['username'])) echo 'err'; ?>">
          名前
          <input type="text" name="username" value="<?php echo getFormData('username', !empty($err_msg['username'])); ?>">
        </label>
        <div class="area-msg">
          <?php
              if (!empty($err_msg['username'])) {
                  echo $err_msg['username'];
              }
          ?>
        </div>

        <label class="<?php if(!empty($err_msg['tel'])) echo 'err'; ?>">
          TEL<span style='font-size:12px;margin-left:5px;'>※ハイフン無しでご入力ください</span>
          <input type="text" name="tel" value="<?php echo getFormData('tel', !empty($err_msg['tel'])); ?>">
        </label>
        <div class="area-msg">
          <?php
              if (!empty($err_msg['tel'])) {
                  echo $err_msg['tel'];
              }
          ?>
        </div>

        <label class="<?php if(!empty($err_msg['zip'])) echo 'err'; ?>">
          郵便番号<span style='font-size:12px;margin-left:5px;'>※ハイフン無しでご入力ください</span>
          <input type="text" name="zip" value="<?php
            if(!empty(getFormData('zip', !empty($err_msg['zip'])))) {
              echo getFormData('zip', !empty($err_msg['zip']));
            }
          ?>">
        </label>
        <div class="area-msg">
          <?php
              if (!empty($err_msg['zip'])) {
                  echo $err_msg['zip'];
              }
          ?>
        </div>

        <label class="<?php if(!empty($err_msg['addr'])) echo 'err'; ?>">
          住所
          <input type="text" name="addr" value="<?php echo getFormData('addr', !empty($err_msg['addr'])); ?>">
        </label>
        <div class="area-msg">
          <?php
              if (!empty($err_msg['addr'])) {
                  echo $err_msg['addr'];
              }
          ?>
        </div>

        <label style="text-align:left;" class="<?php if(!empty($err_msg['age'])) echo 'err'; ?>">
          年齢
          <input type="number" name="age" value="<?php echo getFormData('age', !empty($err_msg['age'])); ?>">
        </label>
        <div class="area-msg">
          <?php
              if (!empty($err_msg['age'])) {
                  echo $err_msg['age'];
              }
          ?>
        </div>

        <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
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
