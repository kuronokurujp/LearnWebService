<?php

/*
    バリデーションチェッククラス
*/
class Validation {
    private $err_msg = '';

    // たくさんあるチェック関数

    // チェック時点でのエラーテキスト
    public function getErrorMsg() {
        return $err_msg;
    }
}

?>