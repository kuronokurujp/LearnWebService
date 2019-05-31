$(function(){
    $(".show-btn").on("click", function(){
        // modalを表示
        $cover = $(".cover");

        // 中央に表示
        var width = $(".modal").width();
        // 変数名にwindowを指定するとwindowのインスタンス名と衝突してバグる
        var windowWidth = $(window).width();

        var offset = (windowWidth / 2 - width / 2); 
        console.log(offset);
        $(".modal").attr('style', 'margin-left: ' + offset + 'px');
        $(".modal").show();
        $cover.show();
    });

    $(".show-close-btn").on("click", function(){
        // modalを非表示
        $modal  = $(".modal");
        $cover = $(".cover");
        $modal.hide();
        $cover.hide();
    });
});