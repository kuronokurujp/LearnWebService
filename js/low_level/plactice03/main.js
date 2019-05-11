$(function(){

    $(".format-number").change(function(){

        // フォームの値を取得
        var format_before = $(this).val();

        // ハイフンが入力されていた場合にまずハイフンを削除する
        format_before = format_before.replace(/-/g, '');

        // 全角英数字を半角に置換
        var format_after = format_before.replace(/[Ａ-Ｚａ-ｚ０-９]/g, function(s) {
            return String.fromCharCode(s.charCodeAt(0)-0xFEE0)
        });

        // 入力された文字数が11文字だった場合
        if (format_after.length == 11)
        {
            // ハイフンを挿入して要素内を書き換える
            $(this).val(format_after.substr(0, 3)+'-'+format_after.substr(3, 4)+'-'+format_after.substr(7,4));
        }
        else
        {
            // 何もせずに要素内を書き換える
            $(this).val(format_after);
        }
    });
});