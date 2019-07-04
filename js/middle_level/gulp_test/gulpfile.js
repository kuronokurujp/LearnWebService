// gulpのテストコード
var gulp = require('gulp');
var minifycss = require('gulp-minify-css'); 
var sass = require('gulp-sass');

gulp.task('test', function() {
    return gulp.src('src/css/*.css').pipe(minifycss()).pipe(gulp.dest('dist/css/'));
});

gulp.task('sass', function() {
    return gulp.src('src/sass/*.scss').pipe(sass()).pipe(gulp.dest('dist/css'))
})

gulp.task('default', gulp.series(['test', 'sass']), function() {});