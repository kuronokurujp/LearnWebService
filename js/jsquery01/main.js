// 1.テキストエリアに入力があった場合
// 2.テキストエリアの文字数を取得します
// 3.文字数をカウンターに表示させる

window.addEventListener("DOMContentLoaded",
    function() {
        // count-textのid名の要素取得
        //var node = document.getElementById("count-text");
        var node = document.querySelector("#count-text");

        // キーボードのkeyを押して離す時に呼ばれるイベント
        node.addEventListener("keyup", 
        function() {

            // テキストエリアの文字数を取得
            // イベントで呼ばれた関数内のthisはイベントを呼んだオブジェクトとなる
            var count = this.value.length;

            // 文字列をカウント数のオブジェクトに設定
            var counterNode = document.querySelector(".show-count-text");
            // オブジェクトのテキストを書き換える
            counterNode.innerText = count;

        }, false);

    }, false);