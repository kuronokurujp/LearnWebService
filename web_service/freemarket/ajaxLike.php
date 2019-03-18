<?php
    // 共通変数・関数ファイル読み込み
    require('function.php');
    debug('------------------------');
    debug('Ajax');
    debug('------------------------');
    debugLogStart();

    // Ajax処理(クライアントとサーバーとの非同期通信)
    // post useridそしてログインしているなら実行
    if (isset($_POST['productId']) && isset($_SESSION['user_id']) && isLogin()) {
        debug('POST送信があります');
        $p_id = $_POST['productId'];
        debug('商品ID:'.$p_id);
        try {
            $dbh = dbConnect();
            $sql = 'SELECT * FROM `like` WHERE product_id = :p_id AND user_id = :u_id';
            $data = array(':p_id' => $p_id, ':u_id' => $_SESSION['user_id']);
            $result_flag = false;
            $stmt = queryPost($dbh, $sql, $data, $result_flag);

            $result_count = $stmt->rowCount();
            debug($result_count);
            // レコードが存在している場合は
            // レコード削除
            // お気に入りから削除されたので
            if (!empty($result_count)) {
                $sql = 'DELETE FROM `like` WHERE product_id = :p_id AND user_id = :u_id';
                $data = array(':p_id' => $p_id, ':u_id' => $_SESSION['user_id']);
            }
            // レコード追加
            // お気に入りに追加されたので
            // レコードが存在しない場合も追加 
            else {
                $sql = 'INSERT INTO `like` (product_id, user_id, create_date) VALUES (:p_id, :u_id, :date)';
                $data = array(':p_id' => $p_id, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
            }
            $stmt = queryPost($dbh, $sql, $data, $result_flag);
        }
        catch (Exception $e) {
            error_log('エラー発生:'.$e->getMessage());
        }
    }
    debug('Ajax処理終了');
?>