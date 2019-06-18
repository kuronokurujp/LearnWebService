<!-- ヘッド部 -->
<?php require('./php/head.php'); ?>

    <body>
        <!--ヘッダー部-->
        <div id="header_body">
            <h2>アセットマネージャー</h2>
            <ul>
                <li><a href="upload.html">アップロード</a></li>
                <li><a href="top.html" class="active">ホーム</a></li>
            </ul>
        </div>

        <!-- コンテンツ部 -->
        <div id="upload_panel">
            <!-- 複合データ形式で送信 -->
            <form action="" method="POST" enctype="multipart/form-data">
                <!-- アップロードする画像表示 -->
                <img src="./img/thumbnail.png" alt="" class="prev_img">
                <!-- 画像パス入力 -->
                <input class="" type="file" value="入力画像">
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                <input class="button_upload_push" type="submit" name="upload_img" value="アップロード">
            </form>
        </div>
    </body>

<!-- フッター -->
<?php require('./php/footer.php'); ?>
