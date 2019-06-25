<?php

/*
    DB制御オブジェクトクラス
*/
class DB {
    private $dbh = null;

    // DBにアクセス
    public function connect($in_db_name, $in_host, $in_user, $in_password) {
        // DBへの接続準備
        $dsn = 'mysql:dbname='.$in_db_name.';host='.$in_host.';charset=utf8';
        $user = $in_user;
        $password = $in_password;
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
        $this->$dbh = new PDO($dsn, $user, $password, $options);
    }

    // SQLを投げる
    public function queryPost($in_sql, $in_data) {

        // SQL文作成
        $stmt = $this->$dbh->prepare($in_sql);

        // BindParamによる構文生成

        // SQL結果を返す
        return $stmt;
    }

}

?>