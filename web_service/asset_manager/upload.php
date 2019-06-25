<?php
    require('./php/common.php');

    $common = new Common();
    $common->LogStart('画像更新');
    $common->LogEnd();
?>

<!-- ヘッド部 -->
<?php require('./php/head.php'); ?>

    <body>
        <!--ヘッダー部-->
        <?php
            require('./php/header.php');
        ?>

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
