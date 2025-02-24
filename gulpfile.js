const gulp = require('gulp')
const babel = require('gulp-babel');
const less = require('gulp-less');
const rename = require('gulp-rename');
const uglify = require('gulp-uglify');
const plumber = require('gulp-plumber');
const concat = require('gulp-concat');
const concatCss = require('gulp-concat-css');
const minifyCSS = require('gulp-minify-css')

const _libs_script = "/lib/*.js"
const _libs_style = "/lib/*.css"
const _dist = "/dist"

gulp.task('lib_styles', function () {
    return gulp.src(_libs_style)
        .pipe(concatCss("libs.min.css", {
            // inlineImports:false,
            rebaseUrls: false
        }))
        .pipe(minifyCSS({
            format: 'keep-breaks',
            semicolonAfterLastProperty: true,
            afterComment: true
        }))
        .pipe(gulp.dest(_dist + '/style'))
})

gulp.task('lib_scripts', function () {
    return gulp.src(_libs_script)
        .pipe(concat("libs.min.js"))
        .pipe(uglify({
            output: {
                comments: true,
            }
        }))
        .pipe(gulp.dest(_dist + '/js'))
})

gulp.task('build', gulp.series(
    'lib_style',
    'lib_script',
))