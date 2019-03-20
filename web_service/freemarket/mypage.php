<?php
    // 共通関数読み込み
    require('function.php');

    // ログ開始！
    debug('---------------------------------');
    debug('マイページ');
    debug('---------------------------------');
    debugLogStart();

    // ログイン認証
    require('auth.php');

    $u_id = $_SESSION['user_id'];
    // 自分が登録した商品データリスト取得
    $myProductData = getMyProductData($u_id);
    // 連絡掲示板のリスト取得
    $myBordData = getMyMsgsAndBordData($u_id);
    // お気に入りリスト取得
    $myLikeData = getMyLikeData($u_id);

//    debug('マイページの登録商品データ:'. print_r($myProductData, true));
//    debug('マイページの連絡掲示板データ:'. print_r($myBordData, true));
//    debug('マイページのお気に入りデータ:'. print_r($myLikeData, true));

    debug('画面表示処理終了------------------------');
?>

<?php
    $siteTitle = "マイページ";
    require('head.php');
?>

<body class="page-mypage page-2colum page-logined">
  <style>
    #main {
      border: none !important;
    }
  </style>

<!-- メニュー -->
<?php
    require('header.php');
?>

    <!-- 処理のメッセージを通知-->
    <p id="js-show-msg" style="display:none;" class="msg-slide">
        <?php echo getSessionFlash('msg_success'); ?>
    </p>

    <!-- メインコンテンツ -->
    <div id="contents" class="site-width">
        <h1 class="page-title">MYPAGE</h1>

        <!-- Main -->
        <section id="main" >
            <section class="list panel-list">
              <h2 class="title">
              登録商品一覧
              </h2>
              <?php
                if (!empty($myProductData)):
                  foreach ($myProductData as $key => $value):
              ?>
              <a href="registProduct.php<?php echo (!empty(appendGetParam(false))) ? appendGetParam(false).'&p_id='.$value['id'] : '?p_id='.$value['id']; ?>" class="panel">
                <div class="panel-head">
                  <img src="<?php echo showImg(sanitize($value['pic1'])); ?> " alt="<?php echo sanitize($value['name']); ?>">
                </div>
                <div class="panel-body">
                  <p class="panel-title"><?php echo sanitize($value['name']); ?><span class="price">¥<?php echo sanitize($value['price']); ?></span></p>
                </div>
              </a>
              <?php
                  endforeach;
                endif;
              ?>
            </section>
            <style>
              .list{
                margin-bottom: 30px;
              }
          </style>
          <section class="list list-table">
            <h2 class="title">
              連絡掲示板一覧
            </h2>
            <table class="table">
              <thead>
                <tr>
                  <th>最新送信日時</th>
                  <th>取引相手</th>
                  <th>メッセージ</th>
                </tr>
              </thead>

              <tbody>
                <?php
                  if (!empty($myBordData)):
                    foreach ($myBordData as $key => $value):
                      if (!empty($value['msg'])) {
                        $msg = array_shit($value['msg']);
                ?>
                      <tr>
                          <td><?php echo sanitize(date('Y.m.d H:i:s', strtotime($msg['send_date'])));?></td>
                          <td>XX XX</td>
                          <td><a href="msg.php?m_id=<?php echo sanitize($value['id']); ?>"><?php echo mb_substr(sanitize($msg['msg'], 0, 48)); ?>...</a></td>
                      </tr>
                <?php
                      }
                      else {
                ?>
                      <tr>
                        <td>---</td>
                        <td>XX XX</td>
                        <td><a href="msg.php?m_id=<?php echo sanitize($value['id']); ?>">まだメッセージはありません</a></td>
                      </tr>
                <?php
                      }
                ?>
                <?php
                    endforeach;
                  endif;
                ?>
              </tbody>
            </table>
          </section>

          <section class="list panel-list">
            <h2 class="title">
              お気に入り一覧
            </h2>
            <?php
              if (!empty($myLikeData)):
                foreach ($variable as $key => $value):
            ?>
                  <a href="productDetail.php<?php echo (!empty(appendGetParam(false))) ? appendGetParam(false).'&p_id='.$value['id'] : '?p_id='.$value['id']; ?>" class="panel">
                    <div class="panel-head">
                      <img src="<?php showImg(sanitize($value['pic1'])); ?>" alt="<?php echo sanitize($value['name']); ?>">
                    </div>
                    <div class="panel-body">
                      <p class="panel-title"><?php echo sanitize($value['name']); ?><span class="price">¥<?php echo sanitize($vvalue['price']); ?></span></p>
                    </div>
                  </a>
            <?php
                endforeach;
              endif;
            ?>
          </section>
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
