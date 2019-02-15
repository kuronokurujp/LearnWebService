<?php
    // 共通関数読み込み
    require('function.php');

    // ログ開始！
    debug('---------------------------------');
    debug('商品出品登録ページ');
    debug('---------------------------------');
    debugLogStart();

    // ログイン認証
    require('auth.php');

    $err_msg = array();

    // 画面処理

    // 画面表示データ取得
    // GETデータから商品IDを取得
    $p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';

    // DBから商品データ取得
    $dbFormData = (!empty($p_id)) ? getProduct($_SESSION['id'], $p_id) : '';

    // 新規編集データかあるいは編集データかを判断
    $edit_flag = (empty($dbFormData)) ? false : true;

    // DBからカテゴリーデータ取得
    $dbCategoryData = getCategory();
    // データのデバッグ表示
    debug('商品ID:'.$p_id);
    debug('商品データ:'.print_r($dbFormData, true));
    debug('カテゴリー一覧:'.print_r($dbCategoryData, true));

    // GETから取得商品IDが改ざんされているかチェック
    if (!empty($p_id) && empty($dbFormData)) {
      debug('GETパラメータの商品IDが違うので、マイページに戻ります');
      header('Location:mypage.php');
    }

    if (!empty($_POST)) {
      debug('POST送信があります。');
      debug('POST情報：'.print_r($_POST, true));
      debug('FILE情報：'.print_r($_FILES, true));

      // 変数にユーザー情報を代入
      $name = $_POST['name'];
      $category = $_POST['category_id'];
      $price = (!empty($_POST['price'])) ? $_POST['price'] : 0;
      $comment = $_POST['comment'];

      $uploadImg_err_msg = '';

      $pic1 = '';
      if (!empty($_FILES['pic1']['name'])) {
        $pic1 = uploadImg($_FILES['pic1'], $uploadImg_err_msg);
        if (!empty($uploadImg_err_msg)) {
          $err_msg['pic1'] = $uploadImg_err_msg;
        }
      } 
      $pic1 = (empty($pic1) && !empty($dbFormData['pic1'])) ? $dbFormData['pic1'] : $pic1;

      $pic2 = '';
      if (!empty($_FILES['pic2']['name'])) {
        $pic2 = uploadImg($_FILES['pic2'], $uploadImg_err_msg);
        if (!empty($uploadImg_err_msg)) {
          $err_msg['pic2'] = $uploadImg_err_msg;
        }
      }
      $pic2 = (empty($pic1) && !empty($dbFormData['pic2'])) ? $dbFormData['pic2'] : $pic2;

      $pic3 = '';
      if (!empty($_FILES['pic3']['name'])) {
        $pic3 = uploadImg($_FILES['pic3'], $uploadImg_err_msg);
        if (!empty($uploadImg_err_msg)) {
          $err_msg['pic3'] = $uploadImg_err_msg;
        }
      }
      $pic3 = (empty($pic1) && !empty($dbFormData['pic3'])) ? $dbFormData['pic3'] : $pic3;

      $valid_err_msg = '';

      // 更新の場合はDBに情報と入力情報とのチェックをする
      if (empty($dbFormData)) {
        // 新規追加

        // 未入力チェック
        if (!validRequired($name, $valid_err_msg)) {
          $err_msg['name'] = $valid_err_msg;
        }

        if (!validMaxLen($name, $valid_err_msg)) {
          $err_msg['name'] = $valid_err_msg;
        }

        if (!validSelect($category, $valid_err_msg)) {
          $err_msg['category'] = $valid_err_msg;
        }

        if (!validMaxLen($comment, $valid_err_msg)) {
          $err_msg['comment'] = $valid_err_msg;
        }

        if (!validRequired($price, $valid_err_msg)) {
          $err_msg['price'] = $valid_err_msg;
        }

        if (!validNumber($price, $valid_err_msg)) {
          $err_msg['price'] = $valid_err_msg;
        }
      }
      else {
        if ($dbFormData['name'] !== $name) {
          if (!validRequired($name, $valid_err_msg)) {
            $err_msg['name'] = $valid_err_msg;
          }

          if (!validMaxLen($name, $valid_err_msg)) {
            $err_msg['name'] = $valid_err_msg;
          }
        }

        if ($dbFormData['category_id'] !== $category) {
          if (!validSelect($category, $valid_err_msg)) {
            $err_msg['category_id'] = $valid_err_msg;
          }
        }

        if ($dbFormData['comment'] !== $comment) {
          if (!validMaxLen($comment, $valid_err_msg, 500)) {
            $err_msg['comment'] = $valid_err_msg;
          }
        }

        if ($dbFormData['price'] !== $price) {
          if (!validRequired($price, $valid_err_msg)) {
            $err_msg['price'] = $valid_err_msg;
          }

          if (!validNumber($price, $valid_err_msg)) {
            $err_msg['price'] = $valid_err_msg;
          }
        }
      }

      if (empty($err_msg)) {
        debug('バリデーションOKです');

        try {
          // DBへ接続
          $dbh = dbConnect();
          // SQL文作成
          // 編集画面の場合はUPDAE文、新規登録ならINSERT文を生成
          if ($edit_flag) {
            debug('DB更新');

            $sql = 'UPDATE product SET name = :name, category_id = :category, price = :price, comment = :comment, pic1 = :pic1, pic2 = :pic2, pic3 = :pic3 WHERE user_id = :u_id AND id = :p_id';
            $data = array(':name' => $name, ':category' => $category, ':price' => $price, ':comment' => $comment, ':pic1' => $pic1, ':pic2' => $pic2, ':pic3' => $pic3, ':u_id' => $_SESSION['user_id'], ':p_id' => $p_id);
          }
          else {
            debug('DB新規登録');

            $sql = 'INSERT INTO `product`(`name`, `category_id`, `price`, `comment`, `pic1`, `pic2`, `pic3`, `user_id`, `create_date` ) VALUES (:name, :category, :price, :comment, :pic1, :pic2, :pic3, :u_id, :date)';
            $data = array(':name' => $name, ':category' => $category, ':price' => $price, ':comment' => $comment, ':pic1' => $pic1, ':pic2' => $pic2, ':pic3' => $pic3, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
          }

          // クエリ実行
          $queryResultFlag = false;
          $stmt = queryPost($dbh, $sql, $data, $queryResultFlag);

          // クエリ成功の場合
          if ($stmt) {
            $_SESSION['msg_success'] = SUC04;
            debug('マイページへ遷移します。');
            header("Location:mypage.php");
          }
        }
        catch (Exception $e) {
          error_log('エラー発生:'. $e->getMessage());
          $err_msg['common'] = MSG08;
        }
      }
    }
?>

<?php
  $siteTitle = '商品出品登録';
  require('head.php');
?>

<body class="page-profEdit page-2colum page-logined">

<!-- メニュー -->
<?php
  require('header.php');
?>

<!-- メインコンテンツ -->
<div id="contents" class="site-width">
  <h1 class="page-title"><?php echo ($edit_flag) ? '商品を編集する' : '商品を出品する'; ?></h1>
  <!-- Main -->
  <section id="main" >
    <div class="form-container">
      <form action="" method='post' class="form" enctype="multipart/form-data">
        <div class="area-msg">
            <?php
                if (!empty($err_msg['common'])) {
                    echo $err_msg['common'];
                }
            ?>
        </div>

        <label class="<?php if (!empty($err_msg['name'])) echo 'err'; ?>">
          商品名<span class="label-require">必須</span>
          <input type="text" name="name">
        </label>
        <div class="area-msg">
            <?php
                if (!empty($err_msg['name'])) {
                    echo $err_msg['name'];
                }
            ?>
        </div>

        <label class="<?php if (!empty($err_msg['category_id'])) echo 'err'; ?>">
          カテゴリ<span class="label-require">必須</span>
          <select name="category_id" id="">
            <option value="0" 
            <?php 
              $error_flag = false;
              if (getFormData('category_id', $error_flag) == 0) {
                echo 'selected';
              };
            ?>>
            選択してください
            </option>
            <?php
              foreach ($dbCategoryData as $key => $value) {
            ?>
              <option value= "<?php echo $value['id']; ?>" 
              <?php
                $error_flag = false;
                if (getFormData('category_id', $error_flag) == $value['id']) {
                  echo 'selected';
                };
              ?>>
                <?php echo $value['name']; ?>
              </option>
            <?php
              }
            ?>
          </select>
        </label>
        <div class="area-msg">
            <?php
                if (!empty($err_msg['category_id'])) {
                    echo $err_msg['category_id'];
                }
            ?>
        </div>

        <label class="<?php if (!empty($err_msg['comment'])) echo 'err'; ?>">
          詳細
          <textarea name="comment" id="js-count" cols="30" rows="10" style="height:150px;"><?php $error_flag = false; echo getFormData('comment', $error_flag); ?></textarea>
        </label>
        <p class="counter-text"><span id="js-count-view">0</span>/500文字</p>
        <div class="area-msg">
            <?php
                if (!empty($err_msg['comment'])) {
                    echo $err_msg['comment'];
                }
            ?>
        </div>

        <label style="text-align:left;" class="<?php if (!empty($err_msg['price'])) echo 'err'; ?>">
          金額<span class="label-require">必須</span>
          <div class="form-group">
            <input type="text" name="price" style="width:150px" placeholder="50,000"
            value="<?php
              $error_flag = false;
              echo (!empty(getFormData('price', $error_flag))) ? getFormData('price', $error_flag) : 0;
            ?>"><span class="option">円</span>
          </div>
        </label>
        <div class="area-msg">
            <?php
                if (!empty($err_msg['price'])) {
                    echo $err_msg['price'];
                }
            ?>
        </div>

<!-- 画像のアップロード 記述開始-->
        <div style="overflow: hidden;">
          <div class="imgDrop-container">
            画像１
            <label class="area-drop" <?php if(!empty($err_msg['pic1'])) echo 'err'; ?>>
              <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
              <input type="file" name="pic1" class="input-file">
              <img src="<?php $error_flag = false; echo getFormData('pic1', $error_flag); ?>" alt="" class="prev-img" style="<?php $error_flag = false; if (empty(getFormData('pic1', $error_flag))) { echo 'display:none'; } ?>">
                ドラッグ＆ドロップ
            </label>
            <div class="area-msg">
                <?php
                    if (!empty($err_msg['pic1'])) {
                        echo $err_msg['pic1'];
                    }
                ?>
            </div>
          </div>

          <div class="imgDrop-container">
            画像２
            <label class="area-drop" <?php if(!empty($err_msg['pic2'])) echo 'err'; ?>>
              <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
              <input type="file" name="pic2" class="input-file">
              <img src=" <?php $error_flag = false; echo getFormData('pic2', $error_flag); ?>" alt="" class="prev-img" style="<?php $error_flag = false; if (empty(getFormData('pic2', $error_flag))) { echo 'display:none'; } ?>">
                ドラッグ＆ドロップ
            </label>
            <div class="area-msg">
              <?php
                if (!empty($err_msg['pic2'])) {
                  echo $err_msg['pic2'];
                }
              ?>
            </div>
          </div>

          <div class="imgDrop-container">
            画像３
            <label class="area-drop" <?php if(!empty($err_msg['pic3'])) echo 'err'; ?>>
              <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
              <input type="file" name="pic3" class="input-file">
              <img src="<?php $error_lag = false; echo getFormData('pic3', $error_flag); ?>" alt="" class="prev-img" style="<?php $error_flag = false; if (empty(getFormData('pic3', $error_flag))) {echo 'display:none';} ?>">
                ドラッグ＆ドロップ
            </label>
            <div class="area-msg">
                <?php
                    if (!empty($err_msg['pic3'])) {
                        echo $err_msg['pic3'];
                    }
                ?>
            </div>
          </div>
        </div>
<!-- 画像のアップロード 記述終了-->

        <div class="btn-container">
          <input type="submit" class="btn btn-mid" value="<?php echo ($edit_flag) ? '更新する' : '出品する'; ?>">
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
