<?php
    namespace Charcter;

    // 偽ドラクエのロジック
    // ログ取るか
    ini_set('log_errors', 'on');
    // ログ出力ファイルを指定
    ini_set('error_log', 'php.log');
    session_start();

    // マクロ定義
    define("MONSTER", 'monster');
    define("HUMAN", 'human');
    define("HISTORY", 'history');
    define("KNOCK_DOWNCOUNT_KEY", 'knockDownCount');

    // 性別クラス
    class Sex {
        const MAN = 0;
        const WOMAN = 1;
        const OKAMA = 2;
    }

    // 生物クラス
    abstract class Creature {
        protected $name = '';
        protected $hp = 0;
        protected $attackMin = 0;
        protected $attackMax = 0;

        public function __construct($inName, $inHP, $inAttackMin, $inAttackMax) {
            $this->name = $inName;
            $this->hp = filter_var($inHP, FILTER_VALIDATE_INT);
            $this->attackMin = filter_var($inAttackMin, FILTER_VALIDATE_INT);
            $this->attackMax = filter_var($inAttackMax, FILTER_VALIDATE_INT);
        }

        // 叫び！
        abstract public function sayCry();

        // HP設定
        public function setHP($inHp) {
            $this->inHp = filter_var($inHp, FILTER_VALIDATE_INT);
        }

        // 名取得
        public function getName() {
            return $this->name;
        }

        // HP取得
        public function getHP() {
            return $this->hp;
        }

        // 攻撃
        public function attack($inTargetObject) {
            $attackPoint = mt_rand($this->attackMin, $this->attackMax);

            if (!mt_rand(0, 9)) {
                $attackPoint *= 2;
                $attackPoint = (int)($attackPoint);
                History::set($this->getName(). "のクリティカル攻撃");
            }

            $inTargetObject->damage($attackPoint);
            History::set($inTargetObject->getName(). "は".$attackPoint.'ポイントのダメージを受けた!');
        }

        // ダメージを受けた
        public function damage($inDamageValue) {
            $this->hp -= $inDamageValue;
        }

        // 死亡しているか
        public function isDead() {
            return $this->hp <= 0;
        } 
    }

    // 人クラス
    class Human extends Creature {
        const HP_MAX = 500;

        protected $sex = Sex::MAN;

        public function __construct($inName, $inHP, $inSex, $inAttackMin, $inAttackMax) {
            parent::__construct($inName, $inHP, $inAttackMin, $inAttackMax);
            $this->sex = $inSex;
        }

        // 叫び
        public function sayCry() {
            $message = "";
            switch ($this->sex) {
                case Sex::MAN: {
                    $message = "いって～！！！！";
                    break;
                }
                case Sex::WOMAN: {
                    $message = "いたいわ";
                    break;
                }
                case Sex::OKAMA: {
                    $message = "もっと！！！！";
                    break;
                }
            }

            return $message;
        }
    }

    // 敵基本クラス
    class Monster extends Creature {
        protected $img = null;

        // インスタンス生成時に呼ばれる
        public function __construct($inName, $inHP, $inImg, $inAttack) {
            parent::__construct($inName, $inHP, $inAttack, $inAttack);

            $this->img = $inImg;
        }

        // 画像取得
        public function getImg() {
            if (empty($this->img)) {
                return "img/monster01.png";
            }

            return $this->img;
        }

        // 叫び
        public function sayCry() {
            return "ぐは！";
        }
    }

    // 魔法モンスター
    class MagicMonster extends Monster {
        private $magicAttack;

        // インスタンス生成時に呼ばれる
        public function __construct($inName, $inHP, $inImg, $inAttack, $inMagicAttack) {
            parent::__construct($inName, $inHP, $inImg, $inAttack, $inAttack);

            $this->magicAttack = $inMagicAttack;
        }

        public function getMagicAttack() {
            return $this->magicAttack;
        }

        // 魔法攻撃
        // 基本クラスのメソッド「attack」をオーバーライド
        public function attack($inTargetObject) {
            if (!mt_rand(0, 4)) {
                $attackPoint = $this->magicAttack;

                History::set($this->getName().'の魔法攻撃!');
                $inTargetObject->damage($attackPoint);
                History::set($inTargetObject->getName(). "は".$attackPoint.'ポイントの魔法ダメージを受けた!');
            }
            else {
                parent::attack($inTargetObject);
            }
        }
    }

    // 履歴クラスの設計図
    interface HistoryInterface {
        public static function set($inStr);
        public static function clear();
    }

    // 履歴クラス
    class History implements HistoryInterface {
        // 履歴のテキスト設定
        public static function set($inStr) {
            if (empty($_SESSION)) {
                $_SESSION[HISTORY] = '';
            }

            $_SESSION[HISTORY] .= $inStr."<br>";
        }

        // 履歴テキストを取得
        public static function get() {
            if (empty($_SESSION)) {
                return '';
            }

            return $_SESSION[HISTORY];
        }

        // 履歴をクリア
        public static function clear() {
            $_SESSION[HISTORY] = '';
        }
    }

    $monsters = array();
    $human = new \Charcter\Human('勇者見習い', Human::HP_MAX, Sex::OKAMA, 50, 100);
    $monsters[] = new \Charcter\Monster('フランケン', 100, "img/monster01.png", mt_rand(20, 40));
    $monsters[] = new \Charcter\MagicMonster('フランケンNEO', 100, "img/monster02.png", mt_rand(20, 40), mt_rand(50, 100));
    $monsters[] = new \Charcter\Monster('ヴァンパイア', 100, "img/monster03.png", mt_rand(20, 40));
    $monsters[] = new \Charcter\MagicMonster('ヴァンパイアNEO', 100, "img/monster04.png", mt_rand(20, 40), mt_rand(50, 100));
    $monsters[] = new \Charcter\Monster('スケルトン', 100, "img/monster05.png", mt_rand(20, 40));
    $monsters[] = new \Charcter\Monster('マッハハンド', 100, "img/monster06.png", mt_rand(20, 40));
    $monsters[] = new \Charcter\MagicMonster('マッハハンドNEO', 100, "img/monster07.png", mt_rand(20, 40), mt_rand(50, 100));
    $monsters[] = new \Charcter\Monster('マッハハンドSUPER', 100, "img/monster08.png", mt_rand(20, 40));

    // 勇者作成
    function createHuman() {
        global $human;
        $_SESSION[HUMAN] = $human;
    }

    // 出現するモンスターを作成
    function createMonster() {
        global $monsters;
        $_SESSION[MONSTER] = $monsters[mt_rand(0, count($monsters) - 1)];
        History::set($_SESSION[MONSTER]->getName().'現れた!');
    }

    function init() {
        gameReset();

        History::set('初期化します!');
        $_SESSION[KNOCK_DOWNCOUNT_KEY] = 0;
        createHuman();
        createMonster();
    }

    function gameOver() {
        gameReset();
    }

    function gameReset()
    {
        History::clear();
        $_SESSION = array();
    }

    // 入力があった場合
    if (!empty($_POST)) {
        $startFlag = (!empty($_POST['start'])) ? true : false;
        $attackFlag = (!empty($_POST['attack'])) ? true : false;
        $escapeFlag = (!empty($_POST['escape'])) ? true : false;
        error_log('post!');

        if ($startFlag) {
            History::set("ゲーム開始！");
            init();
        }
        else if ($attackFlag) {
            History::set('攻撃した!');

            $currentHuman = $_SESSION[HUMAN];
            $currentMonster = $_SESSION[MONSTER];
            // 勇者はモンスターへ攻撃
            History::set($currentHuman->getName()."の攻撃");
            $currentHuman->attack($currentMonster);
            History::set($currentMonster->sayCry());

            // モンスターからの攻撃を勇者は受ける
            History::set($currentMonster->getName()."の攻撃");
            $currentMonster->attack($currentHuman);
            History::set($currentHuman->sayCry());

            // プレイヤーのHPが0以下になったらゲームオーバー
            if ($_SESSION[HUMAN]->isDead()) {
                gameOver();
            }
            else {
                // 敵のHPが0以下になったら、別のモンスターを出現
                if ($_SESSION[MONSTER]->isDead()) {
                    History::set($_SESSION[MONSTER]->getName().'を倒した!');
                    createMonster();
                    $_SESSION[KNOCK_DOWNCOUNT_KEY] = $_SESSION[KNOCK_DOWNCOUNT_KEY] + 1;
                }
            }
        }
        else if ($escapeFlag) {
            History::set('逃げた!');
            createMonster();
        }
        $_POST = array();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>にせド〇クエ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css">
</head>

<body>
    <h1 id="title_text">ゲーム「にせド〇クエ」</h1>
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
        <h2><?php echo $_SESSION[MONSTER]->getName().'が現れた！！'; ?></h2>
        <div style="height: 150px;">
            <img id="img_monster" src="<?php echo $_SESSION[MONSTER]->getImg(); ?>">
        </div>
        <p style="font-size:14px; text-align:center;">モンスターのHP: <?php echo $_SESSION[MONSTER]->getHP(); ?></p>
        <p>倒したモンスター数: <?php echo $_SESSION[KNOCK_DOWNCOUNT_KEY]; ?></p>
        <p>勇者の残りHP: <?php echo $_SESSION[HUMAN]->getHP(); ?></p>
        <form method="post">
            <input type="submit" name="attack" value="➡攻撃する">
            <input type="submit" name="escape" value="➡逃げる">
            <input type="submit" name="start" value="➡ゲームリスタート">
        </form>
        <div class='history'>
            <p><?php echo HISTORY::get(); ?></p>
        </div>
    <?php } ?>
    </div>
</body>
</html>