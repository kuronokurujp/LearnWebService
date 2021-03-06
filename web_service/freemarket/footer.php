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
        if (msg.replace(/^[\s　]+[\s　]+$/g, "").length) {
            $jsShowMsg.slideToggle('slow');
            setTimeout(() => {
                $jsShowMsg.slideToggle('slow');
            }, 5000);
        }

        // 画像ライブプレビュー
        var $dropArea = $('.area-drop');
        var $fileInput = $('.input-file');
        $dropArea.on('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            $(this).css('border', '3px #ccc dashed');
        });

        $dropArea.on('dragleave', function(e) {
            e.stopPropagation();
            e.preventDefault();
            $(this).css('border', 'none');
        });

        $fileInput.on('change', function(e) {
            $dropArea.css('border', 'none');
            // files配列に画像ファイルが入っている
            var file = this.files[0], 
                $img = $(this).siblings('.prev-img'), // jQueryのsiblingsメソッドで兄弟のimgを取得
                fileReader = new FileReader(); // ファイルを読み込むFileReaderオブジェクト

            // 画像の読み込みが完了したらイベントを受け取り、imgのsrcにデータをセットする
            fileReader.onload = function (event) {
                $img.attr('src', event.target.result).show();
            };

            // 画像読み込み
            fileReader.readAsDataURL(file);
        });

        // テキストエリアカウント
        var $countUp = $('#js-count'),
            $countView = $('#js-count-view');
        $countUp.on('keyup', function(e) {
            $countView.html($(this).val().length);
        });

        // 画像切り替え
        var $switchImgSubs = $('.js-switch-img-sub'),
            $switchImgMain = $('#js-switch-img-main');
        $switchImgSubs.on('click', function(e) {
            $switchImgMain.attr('src', $(this).attr('src'));
        });

        // お気に入りに登録・削除
        var $like,
            likeProductId;
        $like = $('.js-click-like') || null;
        likeProductId = $like.data('productid') || null;
        // 数値0をtrueするためにはundefined/nullで判定 0 == falseと見なしているから
        if (likeProductId !== undefined && likeProductId !== null) {
            $like.on('click', function() {
                var $this = $(this);
                $.ajax({
                    type: "POST",
                    url:'ajaxLike.php',
                    data: { productId : likeProductId}
                }).done(function ( data ) {
                    // note 公開するページなのでユーザーから内容が見れてしまう！
                    console.log('Ajax success');
                    $this.toggleClass('active');
                }).fail(function(msg) {
                    console.log('Ajax error');
                });
            });
        }
    });


</script>
</body>
</html>