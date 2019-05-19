<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Ajext Test!</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <form method="post" action="" class="formArea js-formArea">
        <div id="ajaxArea" class="js-ajexArea">
            <p>ここだけ入れ替わります</p>
        </div>

        <p><?php echo date('Y/M/d H:i:s'); ?></p>
        名前
        <input type="text" name="name" class="inputText js-get-val-name" autocomplete="off">
        年齢
        <input type="number" name="age" class="inputText inputText--s js-get-val-age">

        <input type="submit" value="送信" class="btn">
    </form>

    <!-- JQuery -->
    <!-- 読み込むjsファイルが正しいかをハッシュ値を指定して比較 情報サイト「http://3jigen.net/2017/01/post-239/#Integrity」-->
    <script 
        src="https://code.jquery.com/jquery-3.2.1.min.js"
        integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
        crossorigin="anonymous"
    ></script>
    <script src="app.js"></script>
</body>
</html>