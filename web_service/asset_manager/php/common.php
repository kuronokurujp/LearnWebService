<?php

/*
    共通クラス
*/
class Common {
    private $debug_flag = false;
    private $log_title = '';

    public function __construct() {
        // デバッグの時はtrue
        $this->debug_flag = true;

        // エラーを出力する
        error_reporting(E_ALL);
        // エラーをログにして出力
        ini_set('log_errors', 'on');
        // ログファイルの出力先を決める
        ini_set('error_log', './php/php.log');
    }

    // デバッグログ出力
    public function DebugPrint($in_str) {
        if ($this->debug_flag == false) {
            return;
        }

        error_log('デバッグ: '.$in_str);
    }

    // ログ開始
    public function LogStart($in_title) {
        if ($this->debug_flag == false) {
            return;
        }

        $this->log_title = $in_title;

        $this->DebugPrint('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>'. $this->log_title. '画面処理開始');
        $this->DebugPrint('セッションID:'.session_id());
        if (!empty($_SESSION)) {
            $this->DebugPrint('セッション変数の中身:'.print_r($_SESSION, true));
        }

        $this->DebugPrint('現在日時タイムスタンプ:'.time());
        // セッションにタイムスタンプ情報があれば表示
        if (!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])) {
            $this->DebugPrint('ログイン期限日時タイムスタンプ:'.($_SESSION['login_date'] + $_SESSION['login_limit']));
        }
    }

    // ログ終了
    public function LogEnd() {
        $this->DebugPrint('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>'. $this->log_title. '画面処理終了');
    }
}

?>