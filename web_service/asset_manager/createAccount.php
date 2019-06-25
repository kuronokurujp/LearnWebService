<?php
    require('./php/common.php');

    $common = new Common();
    $common->LogStart('アカウント作成');
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
        <div id="account_input_menu">
            <span>アカウント作成</span>
            <form method="POST">
                <dl class="form">
                    <!-- 名前 -->
                    <dt>名前</dt>
                    <dd>
                        <input  type="text" name="name" title="名前">
                    </dd>

                    <!-- パスワード -->
                    <dt>パスワード</dt>
                    <dd>
                        <input type="password" name="password" title="パスワード">
                    </dd>

                    <!-- ニックネーム -->
                    <dt>ニックネーム</dt>
                    <dd>
                        <input type="text" name="nickname" title="ニックネーム">
                    </dd>

                    <!-- メールアドレス -->
                    <dt>メールアドレス</dt>
                    <dd>
                        <input type="text" name="email" title="メールアドレス">
                    </dd>
                </dl>

                <!-- 作成ボタン -->
                <div class="button_item">
                    <input class = "button_create_push" type="submit" name="create_btn" value="作成">
                </div>

            </form>
        </div>
    </body>

<!-- フッター -->
<?php require('./php/footer.php'); ?>