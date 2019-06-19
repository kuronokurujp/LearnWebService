var currentItemNum = 1;
var $slideContainer = $('.slider__container');
var slideItemNum = $('.slider__item').length;
var slideItemWidth = $('.slider__item').innerWidth();
var slideContainerWidth = slideItemWidth * slideItemNum;
// 0.5s期間アニメする
var DURATION = 500;

$slideContainer.attr('style', 'width:' + slideContainerWidth + 'px');

$('.js-slide-prev').on('click', function() {
    if (1 < currentItemNum) {
        $slideContainer.animate({left: '+=' + slideItemWidth + 'px'}, DURATION);
        --currentItemNum;
    }
});

$('.js-slide-next').on('click', function() {
    if (currentItemNum < slideItemNum) {
        $slideContainer.animate({left: '-=' + slideItemWidth + 'px'}, DURATION);
        ++currentItemNum;
    }
});

