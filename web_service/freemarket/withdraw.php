<?php
    require('function.php');

    // ログ開始！
    debug('---------------------------------');
    debug('退会ページ');
    debug('---------------------------------');
    debugLogStart();

    // ログイン認証
    require('auth.php');

    $err_msg = array();

    // 画面の処理
    if (!empty($_POST)) {
        debug("post送信があります。");

        try {
            // DB接続
            $dbh = dbConnect();

            // SQL文作成
            // テーブルデータを削除するのではなく、削除フラグを立てて削除する
            // 論理削除にしている
            $sql1 = 'UPDATE users SET delete_flag = 1 WHERE id = :us_id';
            $sql2 = 'UPDATE product SET delete_flag = 1 WHERE user_id = :us_id';
            $sql3 = 'UPDATE `like` SET delete_flag = 1 WHERE user_id = :us_id';

            // データ流し込み
            $userID = $_SESSION['user_id'];
            $data = array(':us_id' => $userID);

            // クエリ実行
            $queryPostResult = false;
            $stmt1 = queryPost($dbh, $sql1, $data, $queryPostResult);
            $stmt2 = queryPost($dbh, $sql2, $data, $queryPostResult);
            $stmt3 = queryPost($dbh, $sql3, $data, $queryPostResult);

            // クエリ成功後にトップページに遷移
            if ($stmt1) {
                session_destroy();
                debug('セッション変数の中身:'.print_r($_SESSION, true));
                debug('トップページへ遷移します。');
                header('Location:mock/index.html');

            }
            else {
                debug('クエリに失敗しました。');
                $err_msg['common'] = MSG08;
            }
        }
        catch (Exception $e) {
            error_log('エラー発生:'. $e->getMessage());
            $err_msg['common'] = MSG08;
        }
    }
    debug('画面処理終了');
?>

<?php
    $siteTitle = '退会';
    require('head.php')
?>

<body  class="page-windraw page-1colum">

<style>
    .from .btn {
        float: none;
    }
    .from {
        text-align: center;
    }
</style>

<!-- メニュー -->
<?php
    require('header.php');
?>

<!-- メインコンテンツ -->
<div id="contents" class="site-width">
    <!-- Main -->
    <section id="main">
        <div>
            <form action="" method="post" class="form">
                <h2 class="title">退会</h2>
                <div class="area-msg">
                    <?php
                        if (!empty($err_msg['common'])) {
                            echo $err_msg['common'];
                        }
                    ?>
                </div>

                <div class="btn-container">
                    <input type="submit" class="btn" value="退会する" name='submit'>
                </div>
            </form>
            <a href="/mypage.php">マイページに戻る</a>
        </div>
    </section>
</div>

<!-- フッター -->
<?php
    require('footer.php')
?>
