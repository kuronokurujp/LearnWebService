// gulpのテストコード
var gulp = require('gulp');
var minifycss = require('gulp-minify-css'); 
var sass = require('gulp-sass');
var changed = require('gulp-changed');
var imagemin = require('gulp-imagemin');

gulp.task('test', function() {
    return gulp.src('src/css/*.css').pipe(minifycss()).pipe(gulp.dest('dist/css/'));
});

gulp.task('sass', function() {
    return gulp.src('src/sass/*.scss').pipe(sass()).pipe(gulp.dest('dist/css'))
})


var paths = {
    srcDir: 'src',
    distDir: 'dist'
};

// jpg/gif/pngをサイズ圧縮する
gulp.task('imagemin', function() {

    var srcGlob = paths.srcDir + '/**/*.+(png|jpeg|jpg|gif)';
    var distGlob = paths.distDir;

    return gulp.src(srcGlob).pipe(changed(distGlob)).pipe(imagemin([
        imagemin.gifsicle({interlaced: true}),
        imagemin.jpegtran({progressive: true}),
        imagemin.optipng({optimaizationLevel: 3})
    ])).pipe(gulp.dest(distGlob));
});

// 監視タスク
// 監視ディレクトリに変化が起きた時に指定した名のタスクを実行する
gulp.task('watch', function() {
    // 配列を使う時は順番の実行ならseries/並列による同時実行ならparallelを利用
    return gulp.watch(paths.srcDir + '/**/*', gulp.series(['imagemin']));
});

// defaultタスクは最後に記述するのがいい
// 指定したタスクがdefaultタスク以降に記述している場合、その状態でdefault実行するとエラーになる！
gulp.task('default', gulp.series(['imagemin', 'sass']), function() {});
