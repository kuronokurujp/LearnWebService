$(function() {
    $('.js-keyup-valid-email').on('keyup', function (e) {
        // ajaxのコールバック内でイベントのthisを参照したいので変数に保存
        var $that = $(this);

        // ajax実行
        $.ajax({
            type: 'post',
            url: 'ajax_email.php',
            dataType: 'json',
            data: {
                email: $(this).val()
            }
        }).then(function (data) {
            console.log(data);

            if (data) {
                console.log(data);

                $msg_email_field = $('.js-set-msg-email');

                // フォームにメッセージをセット
                if (data.errorFlg) {
                    $msg_email_field.addClass('.is-error');
                    if ($msg_email_field.hasClass('.is-success'))
                    {
                        $msg_email_field.removeClass('.is-success');
                    }

                    $that.addClass('.is-error');
                    if ($that.hasClass('.is-success'))
                    {
                        $that.removeClass('.is-success');
                    }
                }
                else {
                    if ($msg_email_field.hasClass('.is-error'))
                    {
                        $msg_email_field.removeClass('.is-error');
                    }

                    $msg_email_field.addClass('.is-success');

                    if ($that.hasClass('.is-error'))
                    {
                        $that.removeClass('.is-error');
                    }
                    $that.addClass('.is-success');
                }

                // 表示するテキスト設定
                $msg_email_field.text(data.msg);
            }
        });
    });
});