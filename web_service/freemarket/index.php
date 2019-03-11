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
  debug('カレントページ '.$currentPageNum);
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
  $dbProductData = getProductList($currentMinNum);
  /*
  // DBからカテゴリーデータ取得
  $dbCategoryData = getCategory();
  debug('現在のページ:'.$currentPageNum);
*/
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
            <select name="category">
              <option value="1">パソコン</option>
              <option value="2">スマホ</option>
            </select>
          </div>
          <h1 class="title">表示順</h1>
          <div class="selectbox">
            <span class="icn_select"></span>
            <select name="sort">
              <option value="1">金額が安い順</option>
              <option value="2">金額が高い順</option>
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
            <span class="num"><?php echo $currentMinNum + 1?></span> - <span class="num"><?php echo $currentMinNum + $listSpan; ?></span>件 / <span class="num"><?php echo sanitize($dbProductData['total']); ?></span>件中
          </div>
        </div>
        <div class="panel-list">
          <?php
            foreach ($dbProductData['data'] as $key => $value):
          ?>
          <a href="productDetail.php?p_id=<?php echo $value['id']?>" class="panel">
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

        <div class="pagination">
          <ul class="pagination-list">
            <?php
              $pageColNum = 5;
              $totalPageNum = $dbProductData['total_page'];
              if ($currentPageNum == $totalPageNum && $totalPageNum >= $pageColNum) {
                $minPageNum = $currentPageNum - 4;
                $maxPageNum = $currentPageNum;
              }
              elseif($currentPageNum == ($totalPageNum - 1) && $totalPageNum >= $pageColNum) {
                $minPageNum = $currentPageNum - 3;
                $maxPageNum = $currentPageNum + 1;
              }
              elseif ($currentPageNum == 2 && $totalPageNum >= $pageColNum) {
                $minPageNum = $currentPageNum - 1;
                $maxPageNum = $currentPageNum + 3;
              }
              elseif($currentPageNum == 1 && $totalPageNum >= $pageColNum) {
                $minPageNum = $currentPageNum;
                $maxPageNum = 5;
              }
              elseif ($totalPageNum < $pageColNum) {
                $minPageNum = 1; 
                $maxPageNum = $totalPageNum;
              }
              else {
                $minPageNum = $currentPageNum - 2;
                $maxPageNum = $currentPageNum + 2;
              }
            ?>
            <?php
              if ($currentPageNum != 1):
            ?>
              <li class="list-item"><a href="?p=1">&lt;</a></li>
            <?php
              endif;
            ?>
            <?php
              for ($i = $minPageNum; $i <= $maxPageNum; $i++):
            ?>
              <li class="list-item <?php if ($currentPageNum == $i) echo 'active'; ?>"><a href="?p=<?php echo $i; ?>"><?php  echo $i; ?></a></li>
            <?php
              endfor;
            ?>
            <?php
              if ($currentPageNum != $maxPageNum):
            ?>
              <li class="list-item"><a href="?p=<?php echo $maxPageNum;?>">&gt;</a></li>
            <?php
              endif;
            ?>
          </ul>
        </div>
        
      </section>

    </div>

    <!-- footer -->
    <?php
      require('footer.php');
    ?>
