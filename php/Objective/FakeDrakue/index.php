<?php
    // 偽ドラクエのロジック
    // ログ取るか
    ini_set('log_errors', 'on');
    // ログ出力ファイルを指定
    ini_set('error_log', 'php.log');
    session_start();

    // マクロ定義
    define("MY_HP", 500);
    define("MONSTAR", 'monster');
    define("HISTORY", 'history');
    define("MY_HP_KEY", 'my_hp');
    define("KNOCK_DOWNCOUNT_KEY", 'knockDownCount');

    // 敵クラス
    class Monster {
        public $name = '';
        public $hp = 0;
        public $img = null;
        public $attack = 0;

        // インスタンス生成時に呼ばれる
        public function __construct($inName, $inHP, $inImg, $inAttack) {
            $this->name = $inName;
            $this->hp = $inHP;
            $this->img = $inImg;
            $this->attack = $inAttack;
        }

        // 死亡しているか
        public function isDead() {
            return $this->hp <= 0;
        } 

        // プレイヤーに攻撃
        public function attack() {
            $_SESSION[MY_HP_KEY] -= $this->attack;
            $_SESSION[HISTORY] .= $this->attack.'ポイントのダメージを受けた!<br>';
        }

        // ダメージを受けた
        public function damage($inDamageValue) {
            $this->hp -= $inDamageValue;
            $_SESSION[HISTORY] .= $attackPoint.'ポイントのダメージを与えた!<br>';
        }
    }

    $monsters = array();

    addMonster('フランケン', 100, "img/monster01.png", mt_rand(20, 40));
    addMonster('フランケンNEO', 100, "img/monster02.png", mt_rand(20, 40));
    addMonster('ヴァンパイア', 100, "img/monster03.png", mt_rand(20, 40));
    addMonster('ヴァンパイアNEO', 100, "img/monster04.png", mt_rand(20, 40));
    addMonster('スケルトン', 100, "img/monster05.png", mt_rand(20, 40));
    addMonster('マッハハンド', 100, "img/monster06.png", mt_rand(20, 40));
    addMonster('マッハハンドNEO', 100, "img/monster07.png", mt_rand(20, 40));
    addMonster('マッハハンドSUPER', 100, "img/monster08.png", mt_rand(20, 40));

    function addMonster($in_name, $in_hp, $in_img, $in_attack) {
        global $monsters;

        $monsters[] = new Monster(
            $in_name,
            $in_hp,
            $in_img,
            $in_attack 
        );
    }

    function createMonstar() {
        global $monsters;
        $_SESSION[MONSTAR] = $monsters[mt_rand(0, count($monsters) - 1)];
        $_SESSION[HISTORY] .= $_SESSION[MONSTAR]->name.'現れた!<br>';
    }

    function init() {
        $_SESSION[HISTORY] .= '初期化します!<br>';
        $_SESSION[KNOCK_DOWNCOUNT_KEY] = 0;
        $_SESSION[MY_HP_KEY] = MY_HP;
        createMonstar();
    }

    function gameOver() {
        $_SESSION = array();
    }

    // 入力があった場合
    if (!empty($_POST)) {
        $startFlag = (!empty($_POST['start'])) ? true : false;
        $attackFlag = (!empty($_POST['attack'])) ? true : false;
        $escapeFlag = (!empty($_POST['escape'])) ? true : false;
        error_log('post!');

        if ($startFlag) {
            $_SESSION[HISTORY] .= "ゲーム開始！<br>";
            init();
        }
        else if ($attackFlag) {
            $_SESSION[HISTORY] .= '攻撃した!<br>';

            // ランダムでモンスターに攻撃を与える
            $attackPoint = mt_rand(50, 100);
            $_SESSION[MONSTAR]->damage($attackPoint);

            // モンスターからの攻撃を受ける
            $_SESSION[MONSTAR]->attack();

            // プレイヤーのHPが0以下になったらゲームオーバー
            if ($_SESSION[MY_HP_KEY] <= 0) {
                gameOver();
            }
            else {
                // 敵のHPが0以下になったら、別のモンスターを出現
                if ($_SESSION[MONSTAR]->isDead()) {
                    $_SESSION[HISTORY] .= $_SESSION[MONSTAR]->name.'を倒した!<br>';
                    createMonstar();
                    $_SESSION[KNOCK_DOWNCOUNT_KEY] = $_SESSION[KNOCK_DOWNCOUNT_KEY] + 1;
                }
            }
        }
        else if ($escapeFlag) {
            $_SESSION[HISTORY] .= '逃げた!<br>';
            createMonstar();
        }
        $_POST = array();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>偽ドラクエ!!</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css">
</head>

<body>
    <h1 id="title_text">ゲーム「にせドラクエ」</h1>
    <div id="screen">
    <!-- ゲーム開始画面 -->
    <?php if (empty($_SESSION)) { ?>
        <h2 id="game_start_text">GAME START ?</h2>
        <form method="post">
            <input type="submit" name="start" value="➡ゲームスタート">
        </form>
    <?php
        }
        else {
    ?>
    <!-- インゲーム画面 -->
        <h2><?php echo $_SESSION[MONSTAR]->name.'が現れた！！'; ?></h2>
        <div style="height: 150px;">
            <img id="img_monster" src="<?php echo $_SESSION[MONSTAR]->img; ?>">
        </div>
        <p style="font-size:14px; text-align:center;">モンスターのHP: <?php echo $_SESSION[MONSTAR]->hp; ?></p>
        <p>倒したモンスター数: <?php echo $_SESSION[KNOCK_DOWNCOUNT_KEY]; ?></p>
        <p>勇者の残りHP: <?php echo $_SESSION[MY_HP_KEY]; ?></p>
        <form method="post">
            <input type="submit" name="attack" value="➡攻撃する">
            <input type="submit" name="escape" value="➡逃げる">
            <input type="submit" name="start" value="➡ゲームリスタート">
        </form>
        <div class='history'>
            <p><?php echo (!empty($_SESSION[HISTORY])) ? $_SESSION[HISTORY] : ''; ?></p>
        </div>
    <?php } ?>
    </div>
</body>
</html>