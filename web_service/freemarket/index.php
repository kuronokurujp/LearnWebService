<?php
  // 共通変数・関数ファイルを読み込み
  require('function.php');

  debug('--------------------------------------------------------');
  debug('トップページ');
  debug('--------------------------------------------------------');
  debugLogStart();

  // 画面処理

  // 画面表示用データ取得
  // カレントページの番号をGETパラメータから取得
  $currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;
  // カテゴリー
  $category = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';
  // ソート順
  $sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '';

  // ページングでページが切り替わった時のカテゴリーとソート設定を引継ぎ
  {
    $cateogry_link = (!empty($category)) ? $category : '0';
    $sort_link = (!empty($sort)) ? $sort: '0';
    $link = 'c_id='.$cateogry_link.'&'.'sort='.$sort_link;
  }

  // カレントページの番号が不正かチェック
  if (!is_int((int)$currentPageNum)) {
    error_log('エラー発生:指定ページに不正な値が入った');
    // エラーの場合はトップページへ
    header('Location:index.php');
  }
  // 表示最大件数
  $listSpan = 20;
  // 現在の表示レコード先頭を算出
  $currentMinNum = (($currentPageNum - 1) * $listSpan);
  // DBから商品データを取得
  $dbProductData = getProductList($currentMinNum, $category, $sort);
  // DBからカテゴリー一覧を取得
  $dbCategoryData = getCategory();

  debug('画面表示処理終了----------------------');
?>

<?php
  $siteTitle = 'HOME';
  require('head.php');
?>
  <body class="page-home page-2colum">

    <!-- メニュー -->
    <?php
      require('header.php');
    ?>

    <!-- メインコンテンツ -->
    <div id="contents" class="site-width">

      <!-- サイドバー -->
      <section id="sidebar">
        <form>
          <h1 class="title">カテゴリー</h1>
          <div class="selectbox">
            <span class="icn_select"></span>
            <select name="c_id" id="">
              <option value="0" <?php if (getFormData('c_id', true, false) == 0) { echo 'selected'; } ?> >選択してください</option>
              <?php 
                foreach ($dbCategoryData as $key => $value) {
              ?>
                  <option value="<?php echo $value['id'] ?>" <?php if (getFormData('c_id', true, false) == $value['id']) { echo 'selected'; }?>>
                    <?php echo $value['name']; ?>
                  </option>
              <?php
                }
              ?>
            </select>
          </div>
          <h1 class="title">表示順</h1>
          <div class="selectbox">
            <span class="icn_select"></span>
            <select name="sort">
              <option value="0" <?php if (getFormData('sort', true, false) == 0) { echo 'selected'; } ?>>選択してください</option>
              <option value="1" <?php if (getFormData('sort', true, false) == 1) { echo 'selected'; } ?>>金額が安い順</option>
              <option value="2" <?php if (getFormData('sort', true, false) == 2) { echo 'selected'; } ?>>金額が高い順</option>
            </select>
          </div>
          <input type="submit" value="検索">
        </form>

      </section>

      <!-- Main -->
      <section id="main" >
        <div class="search-title">
          <div class="search-left">
            <span class="total-num"><?php echo sanitize($dbProductData['total']); ?></span>件の商品が見つかりました
          </div>
          <div class="search-right">
            <span class="num"><?php echo (!empty($dbProductData['data'])) ? $currentMinNum + 1 : 0;?></span> - <span class="num"><?php echo $currentMinNum + count($dbProductData['data']); ?></span>件 / <span class="num"><?php echo sanitize($dbProductData['total']); ?></span>件中
          </div>
        </div>
        <div class="panel-list">
          <?php
            foreach ($dbProductData['data'] as $key => $value):
          ?>
          <a href="productDetail.php<?php echo (!empty(appendGetParam(array()))) ? appendGetParam(array()).'&p_id='.$value['id'] : '?p_id='.$value['id']; ?>" class="panel">
            <div class="panel-head">
              <img src="<?php echo sanitize($value['pic1']); ?>" alt="<?php echo sanitize($value['name']); ?>">
            </div>
            <div class="panel-body">
              <p class="panel-title"><?php echo sanitize($value['name']); ?><span class="price"><?php echo sanitize(number_format($value['price'])); ?></span></p>
            </div>
          </a>
          <?php
            endforeach;
          ?>
        </div>

        <?php pagination($currentPageNum, $dbProductData['total_page'], $link) ?>
      </section>

    </div>

    <!-- footer -->
    <?php
      require('footer.php');
    ?>
