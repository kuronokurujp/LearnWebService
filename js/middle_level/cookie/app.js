// $はjqueryがないと使えないので注意
$(function() {
    // cookieを書き込む時は「'プロパティ名=' + encodeURIComponent(データ)」にする
    // encodeURIComponent関数を挟む事で特別な意味を持つ値が入っても問題なく書き込める
    document.cookie = 'name=' + encodeURIComponent('kuronokurujp');
    document.cookie = 'love=' + encodeURIComponent('moeny');

    var $user_name = $('.js-cookie_user_name');
    $user_name.html(getCookie('name'));

    var update_count = Number(getCookie('count'));
    if (update_count === '') {
        update_count = 1;
    }
    else {
        update_count += 1;
    }
    document.cookie = 'count=' + encodeURIComponent(update_count);

    var $update_count = $('.js-cookie_value');
    $update_count.html(update_count);

    // Cookie内のkeyからデータを取得
    function getCookie(key) {
        var cookie = document.cookie;
        var dataArray = cookie.split(';');

        for (var i = 0; i < dataArray.length; ++i) {
            var value = dataArray[i];

            value = value.replace(/^\s+|\s+$/g, '');
            var index = value.indexOf('=');

            if (value.substring(0, index) === key) {
                value= value.slice(index + 1);
                value = decodeURIComponent(value);
                return value;
            }
        }

        return '';
    }

    // ボタンクリックイベント
    $('.js-cart_button').on('click', function(e) {
        var cart = $.cookie('cart');
        var shouhin = $(this).text();
        if (cart) {
            $.cookie('cart', cart + ',' + shouhin, { expries: 7, path: '/' });
        }
        else {
            $.cookie('cart', shouhin, { expries: 7, path: '/' });
        }

        location.reload();
    });

    $('.js-cart-data').html($.cookie('cart'));
});