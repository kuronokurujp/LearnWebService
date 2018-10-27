<?php

    // メール送信プログラム

    // フォームがすべて入力さているか
    if (!empty($to) && !empty($subject) && !empty($comment)) {
        // 文字化けしないおきまり設定
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");

        // メール送信準備
        $from = 'info@webukatu.com';

        // メール送信(送信結果はbool型で返ってくる)
        $result = mb_send_mail($to, $subject, $comment, "From: ".$from);

        // 送信結果を判定
        if ($result) {
            unset($_POST);
            $msg = 'メールが送信されました。';
        }
        else {
            $msg = 'メールの送信に失敗しました。';
        }
    }
    else {
        $msg = 'すべて入力必須です。';
    }
?>