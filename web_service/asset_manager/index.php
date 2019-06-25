<?php
    require('./php/common.php');

    $common = new Common();
    $common->LogStart('トップページ');
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
        <div id="contens_site">
            <section id="contens_body">
                <!-- ここに大量の絵を張り付け -->
                <div id="panel_list">
                    <section class="panel">
                        <div>
                            <img width="300" height="300" src="./img/thumbnail.png" />
                            <p class="panel_title">
                                タイトル: yyyy<br>
                                登録日: 2019年3月12日
                            </p>
                        </div>
                    </section>

                    <section class="panel">
                        <div>
                            <img src="./img/thumbnail.png" />
                            <p class="panel_title">
                                タイトル: yyyy<br>
                                登録日: 2019年3月13日
                            </p>
                        </div>
                    </section>

                    <section class="panel">
                        <div>
                            <img src="./img/thumbnail.png" />
                            <p class="panel_title">
                                タイトル: yyyy<br>
                                登録日: 2019年3月14日
                            </p>
                        </div>
                    </section>

                    <section class="panel">
                        <div>
                            <img src="./img/thumbnail.png" />
                            <p class="panel_title">
                                タイトル: yyyy<br>
                                登録日: 2019年3月15日
                            </p>
                        </div>
                    </section>

                    <section class="panel">
                        <div>
                            <img src="./img/thumbnail.png" />
                            <p class="panel_title">
                                タイトル: yyyy<br>
                                登録日: 2019年3月16日
                            </p>
                        </div>
                    </section>

                    <section class="panel">
                        <div>
                            <img src="./img/thumbnail.png" />
                            <p class="panel_title">
                                タイトル: yyyy<br>
                                登録日: 2019年3月17日
                            </p>
                        </div>
                    </section>

                </div>

                <div id="pageing">
                    <span class="pages">page 1 of 100</span>
                    <a href="index.php" class="prev_btn">Prev</a>
                    <a href="index.php">1</a>
                    <a href="index.php">2</a>
                    <a href="index.php">3</a>
                    <a href="index.php">4</a>
                    <span>...</span>
                    <a href="index.php" class="next_btn">Next</a>
                </div>

            </section>

            <!-- サイドメニュー -->
            <section id="side_menu_body">
                <div class="sign_in_form_body">
                    <h3>ログイン</h3>
                    <!-- ユーザー名の入力とメールアドレス入力-->
                    <!-- 登録ページに遷移するリンクボタンも用意する-->
                    <form>
                        <div class="sign_in_form_item">
                            <label for="email"></label>
                            <input type="email" name="email" required="required" placeholder="Email Address">
                        </div>

                        <div class="sign_in_form_item">
                            <label for="password"></label>
                            <input type="password" name="password" required="required" placeholder="Password">
                        </div>

                        <div class="sign_in_button_panel">
                            <input type="submit" class="button" title="sign_in" value="ログイン">
                        </div>
                    </form>
                    <div class="sign_in_form_footer">
                        <a href="createAccount.php">アカウント作成</a>
                    </div>
                </div>
            </section>
        </div>

    </body>

<!-- フッター -->
<?php require('./php/footer.php'); ?>