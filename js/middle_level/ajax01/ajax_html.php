<?php
    if (!empty($_POST)) {
        echo "<h1>名前は{$_POST['name']}で、年齢は{$_POST['age']}です。</h1>";
    }