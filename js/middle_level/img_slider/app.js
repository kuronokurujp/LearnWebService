// クロージャーを使用した書き方
var slider = (function() {

    var currentItemNum = 1;
    var $slideContainer = $('.slider__container');
    var slideItemNum = $('.slider__item').length;
    var slideItemWidth = $('.slider__item').innerWidth();
    var slideContainerWidth = slideItemWidth * slideItemNum;
    // 0.5s期間アニメする
    var DURATION = 500;

    return {

        // 左にスライドする
        prevSlide: function() {
            if (1 < currentItemNum) {
                // 画像の要素横幅分ずらす
                $slideContainer.animate({left: '+=' + slideItemWidth + 'px'}, DURATION);
                --currentItemNum;
            }
        },

        // 右にスライドする
        nextSlide: function() {
            if (currentItemNum < slideItemNum) {
                // 画像の要素横幅分ずらす
                $slideContainer.animate({left: '-=' + slideItemWidth + 'px'}, DURATION);
                ++currentItemNum;
            }
        },

        init: function() {
            // 要素を横並びにする
            $slideContainer.attr('style', 'width:' + slideContainerWidth + 'px');
            var that = this;

            $('.js-slide-prev').on('click', function() {
                that.prevSlide();
            });

            $('.js-slide-next').on('click', function() {
                that.nextSlide();
            });
        }
    };
})();

slider.init();
