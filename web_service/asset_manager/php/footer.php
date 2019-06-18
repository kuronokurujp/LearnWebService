    <footer id="footer_body">
        <p>Copyright © TKO. All Rights Reserved.</p>
    </footer>

    <script src="./js/vendor/jquery-2.2.2.min.js"></script>
    <script>
        // フッターの配置更新
        function updateFooterPosition(footer_id_name) {
            var $footer_body = $(footer_id_name);
            var footer_under_position = $footer_body.offset().top + $footer_body.outerHeight();

            // フッター要素位置が画面下以外の位置にいるなら画面下位置に来るようにする
            if (window.innerHeight > footer_under_position) {
                $footer_body.attr({'style': 'position:fixed; top:' + (window.innerHeight - $footer_body.outerHeight()) + 'px;'});
            }
        }

        $(function() {
            updateFooterPosition('#footer_body');
        });

    </script>
</html>