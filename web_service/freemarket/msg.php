<?php
  // 共通変数・関数ファイル読み込み
  require('function.php');

  debug('-------------------------------');
  debug('連絡掲示板ページ');
  debug('-------------------------------');
  debugLogStart();

  // 画面処理
  $partnerUserId = '';
  $partnerUserInfo = '';
  $myUserInfo = '';
  $productInfo = '';
  $dealUserIds = array();
  $transactionBeginDate = '';
  $err_msg = array();

  // GETパラメータを取得
  $m_id = (!empty($_GET['m_id'])) ? $_GET['m_id'] : '';
  // DBから掲示板とメッセージデータを取得
  $viewData = getMsgsAndBord($m_id);
  debug('取得したDBメッセージデータ：'.print_r($viewData, true));
  // 不正チェック
  if (empty($viewData)) {
    error_log('エラー発生:指定したページに不正な値が入った');
    header('Location:mypage.php');
  }

  // 商品情報を取得
  $productInfo = getProductOne($viewData[0]['product_id']);
  debug('取得したDB商品情報データ:'.print_r($productInfo, true));
  // 商品情報が入っているかチェック
  if (empty($productInfo)) {
    error_log('エラー発生:指定したページに不正な値が入った');
    header('Location:mypage.php');
  }

  // viewDataから相手のユーザーIDを取り出す
  if (!empty($viewData[0]['sale_user'])) {
    $dealUserIds[] = $viewData[0]['sale_user'];
  }

  if (!empty($viewData[0]['buy_user'])) {
    $dealUserIds[] = $viewData[0]['buy_user'];
  }

  // 取引開始日を取得
  $transactionBeginDate = $viewData[0]['create_date'];

  if (($key = array_search($_SESSION['user_id'], $dealUserIds)) !== false) {
    // 自身のIDは外して相手のIDのみ残す
    unset($dealUserIds[$key]);
  }

  // viewData連想配列の中にm_id=''のがあれば削除する
  if (empty($viewData[0]['m_id']))
  {
    unset($viewData[0]);
  }

  // 相手のIDを取得
  $partnerUserId = array_shift($dealUserIds);
  debug('取得した相手のユーザーID:'.$partnerUserId);
  // DBから取引相手のユーザー情報を取得
  if (isset($partnerUserId)) {
    $partnerUserInfo = getUser($partnerUserId);
  }
  debug('取得した相手のユーザーデータ:'.print_r($partnerUserInfo, true));

  // 相手のユーザーIDがあるか
  if (empty($partnerUserInfo)) {
    error_log('エラー発生:指定したページに不正な値が入った');
    header('Location:mypage.php');
  }

  // DBから自分のユーザー情報を取得
  $myUserInfo = getUser($_SESSION['user_id']);
  debug('取得したユーザーデータ:'.print_r($myUserInfo, true));
  if (empty($myUserInfo)) {
    error_log('エラー発生:指定したページに不正な値が入った');
    header('Location:mypage.php');
  }

  // post送信されていた場合
  debug('POSTデータ:'.print_r($_POST, true));
  if (!empty($_POST)) {
    debug('POST送信があります。');

    // ログイン認証
    require('auth.php');

    // バリデーションチェック
    $msg = (isset($_POST['msg'])) ? $_POST['msg'] : '';
    $valide_err_msg = '';
    if (!validMaxLen($msg, $valide_err_msg, 500)) {
      $err_msg['msg'] = $valide_err_msg;
    }

    if (!validRequired($msg, $valide_err_msg)) {
      $err_msg['msg'] = $valide_err_msg;
    }

    debug('err_msg:'.print_r($err_msg, true));
    if (empty($err_msg)) {
      debug('バリデーションOKです');

      // DBとの通信があるので例外処理を付ける
      try {
        $dbh = dbConnect();
        $sql = 'INSERT INTO message(bord_id, send_date, to_user, from_user, msg, create_date) VALUES (:b_id, :send_date, :to_user, :from_user, :msg, :date)';
        $data = array(
          'b_id' => $m_id,
          ':send_date' => date('Y-m-d H:i:s'),
          ':to_user' => $partnerUserId,
          ':from_user' => $_SESSION['user_id'],
          ':msg' => $msg,
          ':date' => date('Y-m-d H:i:s'),
        );

        $result_flag = false;
        $stmt = queryPost($dbh, $sql, $data, $result_flag);
        if ($stmt) {
          // POSTをクリアする
          $_POST = array();
          debug('連絡掲示板へ移動');
          // 自身に遷移
          header('Location: '.$SERVER['PHP_SELF'].'?m_id='.$m_id);
        }
      }
      catch (Exception $e) {
        error_log('エラー発生:'.$e->getMessage());
        $err_msg['common'] = MSG08;
      }
    }
  }

  debug('連絡掲示板処理終了');
?>

<?php
  $siteTitle = '連絡掲示板 ';
  require('head.php');
?>
  <body class="page-msg page-1colum">
    <style>
      /* 連絡掲示板 */
      .msg-info{
        background: #f6f5f4;
        padding: 15px;
        overflow: hidden;
        margin-bottom: 15px;
      }
      .msg-info .avatar{
        width: 80px;
        height: 80px;
        border-radius: 40px;
      }
      .msg-info .avatar-img{
        text-align: center;
        width: 100px;
        float: left;
      }
      .msg-info .avatar-info{
        float: left;
        padding-left: 15px;
        width: 500px;
      }
      .msg-info .product-info{
        float: left;
        padding-left: 15px;
        width: 315px;
      }
      .msg-info .product-info .left,
      .msg-info .product-info .right{
        float: left;
      }
      .msg-info .product-info .right{
        padding-left: 15px;
      }
      .msg-info .product-info .price{
        display: inline-block;
      }
      .area-bord{
        height: 500px;
        overflow-y: scroll;
        background: #f6f5f4;
        padding: 15px;
      }
      .area-send-msg{
        background: #f6f5f4;
        padding: 15px;
        overflow: hidden;
      }
      .area-send-msg textarea{
        width:100%;
        background: white;
        height: 100px;
        padding: 15px;
      }
      .area-send-msg .btn-send{
        width: 150px;
        float: right;
        margin-top: 0;
      }
      .area-bord .msg-cnt{
        width: 80%;
        overflow: hidden;
        margin-bottom: 30px;
      }
      .area-bord .msg-cnt .avatar{
        width: 5.2%;
        overflow: hidden;
        float: left;
      }
      .area-bord .msg-cnt .avatar img{
        width: 40px;
        height: 40px;
        border-radius: 20px;
        float: left;
      }
      .area-bord .msg-cnt .msg-inrTxt{
        width: 85%;
        float: left;
        border-radius: 5px;
        padding: 10px;
        margin: 0 0 0 25px;
        position: relative;
      }
      .area-bord .msg-cnt.msg-left .msg-inrTxt{
        background: #f6e2df;
      }
      .area-bord .msg-cnt.msg-left .msg-inrTxt > .triangle{
        position: absolute;
        left: -20px;
        width: 0;
        height: 0;
        border-top: 10px solid transparent;
        border-right: 15px solid #f6e2df;
        border-left: 10px solid transparent;
        border-bottom: 10px solid transparent;
      }
      .area-bord .msg-cnt.msg-right{
        float: right;
      }
      .area-bord .msg-cnt.msg-right .msg-inrTxt{
        background: #d2eaf0;
        margin: 0 25px 0 0;
      }
      .area-bord .msg-cnt.msg-right .msg-inrTxt > .triangle{
        position: absolute;
        right: -20px;
        width: 0;
        height: 0;
        border-top: 10px solid transparent;
        border-left: 15px solid #d2eaf0;
        border-right: 10px solid transparent;
        border-bottom: 10px solid transparent;
      }
      .area-bord .msg-cnt.msg-right .msg-inrTxt{
        float: right;
      }
      .area-bord .msg-cnt.msg-right .avatar{
        float: right;
      }
    </style>

    <!-- メニュー -->
    <?php
      require('header.php');
    ?>

    <p id="js-show-msg" style="display:none;" class="msg-slide">
      <?php echo getSessionFlash('msg_success'); ?>
    </p>

    <!-- メインコンテンツ -->
    <div id="contents" class="site-width">
      <!-- Main -->
      <section id="main" >
        <div class="msg-info">
          <div class="avatar-img">
            <img src="<?php echo showImg(sanitize($partnerUserInfo['pic'])); ?>" alt="" class="avatar"><br>
          </div>
          <div class="avatar-info">
            <?php echo sanitize($partnerUserInfo['username']).' '.sanitize($partnerUserInfo['age']).'歳'; ?><br>
            〒<?php echo wordwrap($partnerUserInfo['zip'], 4, "-", true)?><br>
            <?php echo sanitize($partnerUserInfo['addr']);?><br>
            TEL：<?php echo sanitize($partnerUserInfo['tel']);?>
          </div>
          <div class="product-info">
            <div class="left">
              取引商品<br>
              <img src="<?php echo sanitize($productInfo['pic1']);?>" alt="" height="70px" width="auto" >
            </div>
            <div class="right">
              <?php echo sanitize($productInfo['name']); ?><br>
              取引金額：<span class="price">¥<?php echo sanitize($productInfo['price']); ?></span><br>
              取引開始日：<?php echo date('Y-m-d', strtotime(sanitize($transactionBeginDate)));?>
            </div>
          </div>
        </div>
        <div class="area-bord" id="js-scroll-bottom">
          <?php
            if (!empty($viewData)) {
                foreach ($viewData as $key => $value) {
                  if (!empty($value['from_user']) && $value['from_user'] == $partnerUserId) {
          ?>
                    <div class="msg-cnt msg-left">
                      <div class="avatar">
                        <img src="<?php echo sanitize(showImg($partnerUserInfo['pic']));?>" alt="" class="avatar">
                      </div>
                      <p class="msg-inrTxt">
                        <span class="triangle"></span>
                        <?php echo sanitize($value['msg']); ?>
                      </p>
                    </div>
          <?php
                  }
                  else {
          ?>
                    <div class="msg-cnt msg-right">
                      <div class="avatar">
                        <img src="<?php echo sanitize(showImg($myUserInfo['pic'])); ?>" alt="" class="avatar">
                      </div>
                      <p class="msg-inrTxt">
                        <span class="triangle"></span>
                        <?php echo sanitize($value['msg']); ?>
                      </p>
                    </div>
          <?php
                  }
                }
              }
              else {
          ?>
              <p style="text-align:center;line-height:20;">メッセージ投稿はまだありません</p>
          <?php
              }
          ?>
        </div>
        <div class="area-send-msg">
          <form action="" method="post">
            <textarea name="msg" id="" cols="30" rows="3"></textarea>
            <input type="submit" value="送信" class="btn btn-send">
          </form>
        </div>
      </section>
      
      <script src="js/vendor/jquery-2.2.2.min.js"></script>
      
      <script>
        $(function(){
          $('#js-scroll-bottom').animate({scrollTop: $('#js-scroll-bottom')[0].scrollHeight}, 'fast');
        });
      </script>

    </div>

    <!-- footer -->
    <?php
      require('footer.php');
    ?>
