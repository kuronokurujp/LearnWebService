<?php
    // ログを出す
    error_reporting(E_ALL);
    // ログを取る
    ini_set('log_errors','on');
    // ログファイルの出力先を決める
    ini_set('error_log', 'php.log');

    // デバッグ
    // サーバーにデプロイしてリリースした時はtrueからfalseにする
    $debug_flag = true;

    // デバッグログ関数
    function debug($str) {
        global $debug_flag;
        if (!empty($debug_flag)) {
            error_log('デバッグ:'.$str);
        }
    }

    // セッション準備・セッション有効期限を延ばす
    // セッションファイルの置き場を変更する
    // (var/tmp以下に置くと30日は削除されない)
    session_save_path("var/tmp/");
    // ガーベージコレクションが削除するセッションの有効期限を設定
    // (30日以上経っているものに対してだけ100分の1の確率で削除)
    ini_set('session.gc_maxlifetime', 60*60*24*30);
    // プラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
    ini_set('session.cookie_lifetime', 60*60*24*30);
    // セッションを使う
    session_start();
    // 現在のセッションIDを新しく生成したものと置き換える
    // (なりすましのセキュリティ対策, 同じセッションIDを使い続けるといずれ情報を把握されるから)
    session_regenerate_id();

    // 画面表示処理開始ログを吐き出し関数
    function debugLogStart() {
        debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
        debug('セッションID:'.session_id());
        debug('セッション変数の中身:'.print_r($_SESSION, true));
        debug('現在日時タイムスタンプ:'.time());
        // セッションにタイムスタンプ情報があれば表示
        if (!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])) {
            debug('ログイン期限日時タイムスタンプ:'.($_SESSION['login_date'] + $_SESSION['login_limit']));
        }
    }

    // 定数
    // エラーメッセージを定数化
    define('MSG01', '入力必須です');
    define('MSG02', 'Emailの形式で入力してください');
    define('MSG03', 'パスワード(再入力)が合っていません');
    define('MSG04', '半角英数字のみご利用いただけます');
    define('MSG05', '6文字以上で入力してください');
    define('MSG06', '256文字以内で入力してください');
    define('MSG07', 'Emailが重複しています');
    define('MSG08', '時間をおいて再度登録をお願い致します');
    define('MSG09', 'メールアドレスまたはパスワードが違います');
    define('MSG10', '電話番号の形式が違います');
    define('MSG11', '郵便番号の形式が違います');
    define('MSG12', '登録したパスワードと同じではありません');
    define('MSG13', '古いのと新しいパスワードが同じです');
    define('MSG14', '文字で入力してください');
    define('MSG15', '正しくありません');
    define('MSG16', '有効期限が切れています');
    define('MSG17', '半角数字のみご利用できます');
    define('SUC01', 'パスワードを変更しました');
    define('SUC02', 'プロフィールを変更しました');
    define('SUC03', 'メールを送信しました');
    define('SUC04', '登録しました');
    define('SUC05', '購入しました！相手と連絡を取りましょう');

    // selectboxのチェック
    function validSelect($inStr, &$outErrMsg) {
        // 数字でない場合はエラー
        if (!preg_match("/^[0-9]+$/", $inStr)) {
            $outErrMsg = MSG15;
            return false;
        }

        $outErrMsg = '';

        return true;
    }

    // 固定長チェック
    function validLength($inStr, &$inLimitLen, &$outErrMsg, $inLen=8) {
        $inLimitLen = $inLen;
        if (mb_strlen($inStr) !== $inLen) {
            $outErrMsg = MSG14;
            return false;
        }

        $outErrMsg = '';

        return true;
    }

    // パスワードのバリデーション関数
    function validPass($inStr, &$outputErrorMsg) {
        $outputErrorMsg = '';

        if (!validHalf($inStr, $outputErrorMsg)) {
            return false;
        }

        if (!validMaxLen($inStr, $outputErrorMsg)) {
            return false;
        }

        if (!validMinLen($inStr, $outputErrorMsg)) {
            return false;
        }

        return true;
    }

    // 未入力のバリデーション関数
    function validRequired($inStr, &$outErrMsg) {
        // 数の文字列があるのでemptyとすると０の文字列を空と判定される。
        // なので空文字列なら失敗にする
        if ($inStr === '') {
            $outErrMsg = MSG01;
            return false;
        }

        $outErrMsg = '';

        return true;
    }

    // emailの未入力のバリデーション関数
    function validEmail($inStr, &$outErrMsg) {
        if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $inStr)) {
            $outErrMsg = MSG02;
            return false;
        }

        $outErrMsg = '';

        return true;
    }

    // emailの重複チェック
    function validEmailDup($inEmail, &$outErrMsg) {
        try {
            $dbh = dbConnect();
            $sql = 'SELECT * FROM users WHERE email = :email AND delete_flag = 0';
            $data = array(':email' => $inEmail);

            // クエリ実行
            $resultPost = false;
            $stm = queryPost($dbh, $sql, $data, $resultPost);
            if ($stm) {
                debug('クエリ成功');

                $result = $stm->fetch(PDO::FETCH_ASSOC);
                if (!empty($result)) {
                    // 重複している
                    $outErrMsg = MSG07;
                    return null;
                }
            }
            else {
                debug('クエリ失敗');
                return null;
            }
        } catch(Exception $e) {
            dbErrorLog($e);
            $outErrMsg = MSG08;
            return null;
        }

        return null;
    }

    // 同値のバリデーション関数
    function validMatch($inStr, $inStr2, &$outErrMsg) {
        if ($inStr !== $inStr2) {
            $outErrMsg = MSG15;
            return false;
        }

        $outErrMsg = '';

        return true;
    }

    // 最小文字数のバリデーション関数
    function validMinLen($inStr, &$outErrMsg, $inMin = 6) {
        if (mb_strlen($inStr) < $inMin) {
            $outErrMsg = MSG05;
            return false;
        }

        $outErrMsg = MSG06;

        return true;
    }

    // 最大文字数のバリデーション関数
    function validMaxLen($inStr, &$outErrMsg, $inMax = 255) {
        if (mb_strlen($inStr) > $inMax) {
            $outErrMsg = MSG06;
            return false;
        }

        return true;
    }

    // 電話番号形式のバリデーション関数
    function validTel($inStr, &$outErrMsg) {
        if (!preg_match("/0\d{1,4}\d{1,4}\d{4}/", $inStr)) {
            $outErrMsg = MSG10;
            return false;
        }

        $outErrMsg = '';

        return true;
    }

    // 郵便番号形式のバリデーション関数
    function validZip($inStr, &$outErrMsg) {
        if (!preg_match("/^\d{7}$/", $inStr)) {
            $outErrMsg = MSG11;
            return false;
        }

        $outErrMsg = '';
        return true;
    }

    // 半角のバリデーション関数
    function validHalf($inStr, &$outErrMsg) {
        if (!preg_match("/^[a-zA-Z0-9]+$/", $inStr)) {
            $outErrMsg = MSG05;
            return false;
        }

        $outErrMsg = '';
        return true;
    }

    // 半角数字のバリデーション関数
    function validNumber($inStr, &$outErrMsg) {
        if (!preg_match("/^[0-9]+$/", $inStr)) {
            $outErrMsg = MSG17;
            return false;
        }

        $outErrMsg = '';

        return true;
    }

    // ユーザー情報をDBから取得する
    function getUser($u_id) {
        debug('ユーザー情報を取得します。');

        try {
            // DBに接続
            $dbh = dbConnect();

            // SQL文作成
            $sql = 'SELECT * FROM `users` WHERE `id` = :u_id AND `delete_flag` = 0';

            // SQL文に流すデータ作成
            $data = array(':u_id' => $u_id);

            // クエリ実行
            $queryPostResult = false;
            $stmt = queryPost($dbh, $sql, $data, $queryPostResult);

            if ($stmt) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            else {
                debug('クエリに失敗しました');
                return false;
            }
        }
        catch (Exception $e) {
            dbErrorLog($e);
        }

        // クエリの結果のデータを返却
        // return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // プロダクト取得
    function getProduct($u_id, $p_id) {
        debug('商品情報を取得');
        debug('ユーザーID:'.$u_id);
        debug('プロダクトID:'.$p_id);

        // 例外処理
        try {
            $dbh = dbConnect();

            $sql = 'SELECT * FROM product WHERE user_id = :u_id AND id = :p_id AND delete_flag = 0';
            $data = array(':u_id' => $u_id, ':p_id' => $p_id);
            $result_flag = false;
            $stmt = queryPost($dbn, $sql, $data, $result_flag);
            if ($stmt) {
                debug('クエリ成功');
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            else {
                debug('クエリ失敗');
                return false;
            }
        }
        catch (Exception $e) {
            dbErrorLog($e);
        }
    }

    // プロダクトをリスト取得
    function getProductList($inCurrentMinNum, $category, $sort, $inSpan = 20) {
        debug('商品情報を取得');
        // 例外処理
        try {
            $dbh = dbConnect();
            $sql = 'SELECT id FROM product';
            // カテゴリーとリストソートする
            if (!empty($category)) $sql .= ' WHERE category_id = '.$category;
            if (!empty($sort)) {
                switch ($sort) {
                    case 1:
                        $sql .= ' ORDER BY price ASC';
                        break;
                    case 2:
                        $sql .= ' ORDER BY price DESC';
                        break;
                }
            }

            $data = array();
            $result_flag = false;
            $stmt = queryPost($dbh, $sql, $data, $result_flag);
            $rst['total'] = $stmt->rowCount();
            $rst['total_page'] = ceil($rst['total'] / $inSpan);
            if (!$stmt) {
                return false;
            }

            // todo 本来ならSQLインジェクション対策をするべき
            // しかしLIMIT構文は数値を入れないと動かないので今の仕組みでは動かない
            $sql = 'SELECT * FROM product';
            // カテゴリーとリストソートする
            if (!empty($category)) $sql .= ' WHERE category_id = '.$category;
            if (!empty($sort)) {
                switch ($sort) {
                    case 1:
                        $sql .= ' ORDER BY price ASC';
                        break;
                    case 2:
                        $sql .= ' ORDER BY price DESC';
                        break;
                }
            }

            $sql .= ' LIMIT '.$inSpan.' OFFSET '.$inCurrentMinNum;
            $data = array();
            $stmt = queryPost($dbh, $sql, $data, $result_flag);
            if ($stmt) {
                // クエリ結果のデータを全レコードを格納
                $rst['data'] = $stmt->fetchAll();
                return $rst;
            }
            else {
                return false;
            }
        }
        catch (Exception $e) {
            error_log('エラー発生:'. $e->getMessage());
        }
    }

    // 指定したproduct_idの商品情報を取得
    function getProductOne($p_id) {
        debug('商品情報を取得');
        debug('商品ID:'.$p_id);

        // 例外処理
        try {
            $dbh = dbConnect();
            $sql = 'SELECT p.id, p.name, p.comment, p.price, p.pic1, p.pic2, p.pic3, p.user_id, p.create_date, p.update_date, c.name AS category 
                    FROM product AS p LEFT JOIN category AS c ON p.category_id = c.id WHERE p.id = :p_id AND p.delete_flag = 0 AND c.delete_flag = 0';
            $data = array(':p_id' => $p_id);
            $result_flag = false;
            $stmt = queryPost($dbh, $sql, $data, $result_flag);

            if ($stmt) {
                // 成功
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            else {
                return false;
            }
        }
        catch (Exception $e) {
            error_log('エラー発生:'.$e->getMessage());
        }
    }

    // DB処理のエラーログ
    function dbErrorLog($e) {
        // 文字コードがshift-jisの場合utf8に変更してログ出力
        error_log('エラー発生:'.mb_convert_encoding($e->getMessage(), "UTF-8", "Shift-JIS")); 
    }

    // DB接続
    function dbConnect() {
        // DBへの接続準備
        $dsn = 'mysql:dbname=freemarket;host=localhost;charset=utf8';
        $user = 'root';
        $password = 'root';
        $options = array(
            // SQL実行失敗時の例外をスロー
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // デフォルトフェッチモードを連想配列フォーマットに
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // 一度結果セットをすべて取得して、サーバー負荷を軽減
            // SELECTで得た結果からromCountメソッドを使えるように
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true, 
        );

        // PDOオブジェクト生成(DB接続)
        $dbh = new PDO($dsn, $user, $password, $options);

        return $dbh;
    }

    // DBにSQLを投げる
    function queryPost($dbh, $sql, $data, &$outResultPost) {
        debug('SQL:'.$sql);
        debug('流し込みデータ:'.print_r($data, true));

        // SQL文(クエリー作成)
        $stmt = $dbh->prepare($sql);
        // todo LIMIT構文を利用できるようにためにbindParam対応が必要になる
        // ブレースホルダーに値を設定、SQL文を実行
        if ($stmt->execute($data)) {
            debug('クエリ成功');
            $outResultPost = true;
        }
        else {
            debug('クエリ失敗');
            debug('失敗したSQL:'.print_r($stmt, true));
            $outResultPost = false;
            return 0;
        }

        return $stmt;
    }

    // 画像ファイル更新
    function uploadImg($inFile, &$outErrMsg) {
        debug('画像更新開始');
        debug('画像ファイルデータ：'.$inFile);

        $outErrMsg = '';
        // 数字が設定しているかをチェック
        if (isset($inFile['error']) && is_int($inFile['error'])) {
            // 画像データが正常なデータかチェック
            try {
                switch ($inFile['error']) {
                    // 画像データが正常
                    case UPLOAD_ERR_OK: {
                        break;
                    }
                    // ファイル未選択
                    case UPLOAD_ERR_NO_FILE: {
                        throw new RuntimeException('ファイル選択されていません');
                    }
                    // php.ini定義にあるデータ最大サイズを超えている
                    case UPLOAD_ERR_INI_SIZE:
                    // フォーム定義の最大サイズを超えている
                    case UPLOAD_ERR_FORM_SIZE:
                        throw new RuntimeException('ファイルサイズが大きすぎる');
                        break;
                    // その他のエラー
                    default: {
                        throw new RuntimeException('その他のエラーが発生しました');
                    }
                }

                // $inFile['mime']の値は操作できてしまうらしい
                // なので面倒だが自前でMIMEタイプをチェック
                // @を付けると引数値の問題でエラーになっても処理が進む！
                $type = @exif_imagetype($inFile['tmp_name']);
                debug('アップロードする画像タイプ : '.$type);
                // 第三引数にtrueを設定すると厳しくチェックする
                if (!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG], true)) {
                    throw new RuntimeException("画像形式が未対応です");
                }

                // 正常だったので更新する
                // ファイルデータからハッシュを取得してそれをファイル名にして保存する
                // これをする理由はファイル名の重複するとアップロードが上書きするのでそれを防ぐため
                $path = 'uploads/'.sha1_file($inFile['tmp_name']).image_type_to_extension($type);

                // ファイルを指定したパスへ移動
                if (!move_uploaded_file($inFile['tmp_name'], $path)) {
                    throw new RuntimeException("ファイル保存時にエラーが発生しました");
                }

                // 移動したファイルは権限を変更する
                chmod($path, 0644);

                debug('ファイルは正常にアップロード');
                debug('ファイルパス:'.$path);

                return $path;
            }
            catch (RuntimeException $e) {
                debug($e->getMessage());
                $outErrMsg = $e->getMessage();
            }
        }
    }

    // サニタイズ
    function sanitize($inStr) {
        // HTMLエンティティ化
        // クロスサイトスクリプティング対応
        // フォームで入力テキストがHTMLタグだとHTML解釈されてページされる
        // 入力してテキストにサイト誘導など悪意のあるのがあると問題になるので
        // 入力したフォームテキストはタグとして解釈されないようにする
        return htmlspecialchars($inStr, ENT_QUOTES);
    }

    // フォーム入力保持
    // プロフィール画面へ入力した情報を取得するために作成
    // なぜ作成したか、プロフィール更新に失敗した場合DBで取得した情報をフォームに表示すると
    // せっかく入力した情報が消えてしまう。
    // 再入力の手間を省けるためにあらかじめ入力した情報を保持しておく
    function getFormData($inStr, $inGETFlag, $inFormErrorFlag) {
        if ($inGETFlag) {
            $method = $_GET;
        }
        else {
            $method = $_POST;
        }
        global $dbFormData;

        if (!empty($dbFormData)) {
            // フォームにエラーがあった
            if(!empty($dbFormData[$inStr])) {
                // フォームに入力があればそれを採用
                // emptyではなくissetを採用している理由としては
                // 数値の０がフォームに入力することがある
                // 電話番号とか郵便番号、年齢などが
                // 数値の０が設定されている場合もデータ存在すると判定しないといけないので、
                // issetを利用する
                // emptyだと0がないと判定されるので今回は使えない
                if (isset($method[$inStr])) {
                    return sanitize($method[$inStr]);
                }
                // フォームに入力がなければDBを採用
                else {
                    return sanitize($dbFormData[$inStr]);
                }
            }
            else {
                // フォームに入力があるが、DBのデータと異なる場合はフォームを採用
                if (isset($method[$inStr]) && $method[$inStr] !== $dbFormData[$inStr]) {
                    return sanitize($method[$inStr]);
                }
                // フォームの入力ないのでそもそも変更がない
                else {
                    return sanitize($dbFormData[$inStr]);
                }
            }
        }
        elseif (isset($method[$inStr])) {
            return sanitize($method[$inStr]);
        }
    }

    // メール送信
    function sendMail($from, $to, $subject, $comment) {

        // フォームがすべて入力さているか
        if (!empty($to) && !empty($subject) && !empty($comment)) {
            // 文字化けしないおきまり設定
            mb_language("Japanese");
            mb_internal_encoding("UTF-8");

            // メール送信(送信結果はbool型で返ってくる)
            $result = mb_send_mail($to, $subject, $comment, "From: ".$from);

            // 送信結果を判定
            if ($result) {
                debug('メールが送信されました。');
            }
            else {
                debug('メールの送信に失敗しました。');
            }
        }
    }

    // エラーメッセージ取得
    function getErrorMessage($inErrorMessageArray, $inKey) {
        if (!empty($inErrorMessageArray[$inKey])) {
            return $inErrorMessageArray[$inKey];
        }
    }

    // セッションのキー情報を一度のみ取得
    function getSessionFlash($inKeyName) {
        if (!empty($_SESSION[$inKeyName])) {
            $data = $_SESSION[$inKeyName];
            $_SESSION[$inKeyName] = '';

            return $data;
        }
    }

    // 認証キーの作成
    function makeRandKey($inLength = 8) {
        static $chars = 'adcdefjhijklmnopqrstuvwxyzADCDEFJHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $inLength; ++$i) {
            $str .= $chars[mt_rand(0, mb_strlen($chars) - 1)];
        }

        return $str;
    }

    // 商品のカテゴリーをDBから取得
    function getCategory() {
        debug('カテゴリーデータを取得');

        try {
            $dbh = dbConnect();

            $sql = 'SELECT * FROM `category`';
            $data = array();
            $result_flag = false;

            $stmt = queryPost($dbh, $sql, $data, $result_flag);
            // 該当するカテゴリー名一覧
            if ($stmt) {
                debug('クエリ成功');
                return $stmt->fetchAll();
            }
            else {
                debug('クエリ失敗');
                return false;
            }
        }
        catch (Exception $e) {
            error_log('エラーログ：'.$e->getMessage());
        }
    }

    // ページング機能
    function pagination($currentPageNum, $totalPageNum, $link='', $pageColNum = 5) {
        debug('ページングのリンクテキスト : '.$link);

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

        echo '<div class="pagination">';
          echo '<ul class="pagination-list">';
          $andLink = (!empty($link)) ? '&'.$link : '';

            if ($currentPageNum != 1) {
              echo '<li class="list-item"><a href="?p=1'.$andLink.'">&lt;</a></li>';
            }

            for ($i = $minPageNum; $i <= $maxPageNum; $i++) { 
                echo '<li class="list-item ';
                if ($currentPageNum == $i) { echo 'active'; }
                echo '"><a href="?p='.$i.$andLink.'">'.$i.'</a></li>';
            }

            if ($currentPageNum != $maxPageNum && $maxPageNum > 1) {
              echo '<li class="list-item"><a href="?p='.$maxPageNum.$andLink.'">&gt;</a></li>';
            }
          echo '</ul>';
        echo '</div>';
    }

    // 画像表示用
    function showImg($inPath) {
        if (empty($inPath)) {
            return 'mock/img/sample-img.png';
        }
        else {
            return $inPath;
        }
    }

    // GETパラメータ付与
    function appendGetParam($inArrDelKey) {
        if (!empty($_GET)) {
            $str = '?';
            foreach ($_GET as $key => $value) {
                if (!in_array($key, $inArrDelKey, true)) {
                    $str .= $key.'='.$value.'&';
                }
            }
            $str = mb_substr($str, 0, -1, "UTF-8");
            return $str;
        }
    }

    // Msg取得
    function getMsgsAndBord($id) {
        debug('msg情報を取得');
        debug('掲示板ID:'.$id);

        // 例外処理
        try {
            $dbh = dbConnect();
            $sql = 'SELECT m.id AS m_id, product_id, bord_id, send_date, to_user, from_user, sale_user, buy_user, msg, b.create_date FROM message AS m RIGHT JOIN bord AS b ON b.id = m.bord_id WHERE b.id = :id AND b.delete_flag = 0 ORDER BY send_date ASC';
            $data = array(':id' => $id);
            $result_flag = false;
            $stmt = queryPost($dbh, $sql, $data, $result_flag);

            if($stmt) {
                return $stmt->fetchAll();
            }
            else {
                return false;
            }
        }
        catch (Exception $e) {
            error_log('エラー発生:'.$e->getMessage());
        }
    }

?>