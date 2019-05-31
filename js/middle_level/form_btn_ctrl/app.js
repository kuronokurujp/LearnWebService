// 起動時に実行する
$(function(){

    // キーを押して上げた時にイベント呼ぶ
    $(".js-formInput").on("keyup", function() {
        // 入力した文字列の長さをチェックして文字が入力しているばボタンを押せるようにする
        var value = $(this).val();
        if (value) {
            $(".js-formSubmit").prop("disabled", false);
        }
        else {
            $(".js-formSubmit").prop("disabled", true);
        }
    });
});