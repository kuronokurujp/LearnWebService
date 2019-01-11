<footer id="footer">
    Copyright<a href="">提供者</a>. All Rights Reserved
</footer>

<script src="js/vendor/jquery-2.2.2.min.js"></script>
<script>
    $(function() {
        var $ftr = $('#footer');
        if (window.innerHeight > $ftr.offset().top + $ftr.outerHeight()) {
            $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) + 'px;' });
        }

        // メッセージ表示
        var $jsShowMsg = $('#js-show-msg');
        var msg = $jsShowMsg.text();
        // 文字列の先頭と末尾の空白を削除するのであれば「trim」メソッドを使用するべき
        var showMsg = msg.trim();
        if (showMsg.length > 0) {
            $jsShowMsg.slideToggle('slow');
            setTimeout(() => {
                $jsShowMsg.slideToggle('slow');
            }, 5000);
        }
    });
</script>

</body>
</html>