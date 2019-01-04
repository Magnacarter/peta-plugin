"use strict";

var gulp         = require( 'gulp' ),
    maps         = require( 'gulp-sourcemaps' ),
    autoprefixer = require( 'gulp-autoprefixer' ),
    concat       = require( 'gulp-concat' ),
    uglify       = require( 'gulp-uglify' ),
    rename       = require( 'gulp-rename' ),
    sass         = require( 'gulp-sass' ),
    cleanCSS     = require( 'gulp-clean-css' ),
    del          = require( 'del' ),
    browserSync  = require( 'browser-sync' );

var path = {
  SCSS_PUB        : '../pub/scss/main.scss',
  SCSS_ADMIN      : '../admin/scss/main.scss',
  CSS_DIR         : '../assets/css',
  JS_DIR          : '../assets/js',
  JS_FILES        : [ '../assets/js/admin-script.js', '../jquery.rateyo.min.js', '../select2.min.js' ],
  CSS_FILES       : [ '../assets/css/select2.min.css', '../assets/css/unsemantic-grid-responsive.css', '../assets/css/admin.css' ]
};

/**
 * Admin scripts
 */
gulp.task( 'concatAdminScripts', function() {
  return gulp.src( path.JS_FILES )
    .pipe( maps.init() )
    .pipe( concat( 'admin-app.js' ) )
    .pipe( maps.write( './' ) )
    .pipe( gulp.dest( path.JS_DIR ) );
});

/**
 * Public scripts
 */
gulp.task( 'concatScripts', function() {
  return gulp.src( path.JS_FILES )
    .pipe( maps.init() )
    .pipe( concat( 'app.js' ) )
    .pipe( maps.write( './' ) )
    .pipe( gulp.dest( path.JS_DIR ) );
});

/**
 * Admin sass
 */
gulp.task( 'compileAdminSass', function() {
  return gulp.src( path.SCSS_ADMIN )
    .pipe( maps.init() )
    .pipe( sass().on( 'error', sass.logError ) )
    .pipe( autoprefixer( { browsers: ["> 0%"] } ) )
    .pipe( rename( 'admin.css' ) )
    .pipe( maps.write() )
    .pipe( gulp.dest( path.CSS_DIR ) )
});

/**
 * Admin css
 */
gulp.task( 'concatAdminCSS', [ 'compileAdminSass' ], function() {
  return gulp.src( path.CSS_FILES )
    .pipe( cleanCSS( { compatibility: 'ie8' } ) )
    .pipe( concat( 'admin-styles.css' ) )
    .pipe( gulp.dest( path.CSS_DIR ) );
});

/**
 * Public sass
 */
gulp.task( 'compileSass', function() {
  return gulp.src( path.SCSS_PUB )
    .pipe( maps.init() )
    .pipe( sass().on( 'error', sass.logError ) )
    .pipe( autoprefixer( { browsers: ["> 0%"] } ) )
    .pipe( rename( 'pub.css' ) )
    .pipe( maps.write() )
    .pipe( gulp.dest( path.CSS_DIR ) )
});

/**
 * Public css
 */
gulp.task( 'concatCSS', [ 'compileSass' ], function() {
  return gulp.src( path.CSS_FILES )
    .pipe( cleanCSS( { compatibility: 'ie8' } ) )
    .pipe( concat( 'pub-styles.css' ) )
    .pipe( gulp.dest( path.CSS_DIR ) );
});

gulp.task( 'cssWatch', [ 'concatCSS' ], browserSync.reload );
gulp.task( 'jsWatch', [ 'concatScripts' ], browserSync.reload );
gulp.task( 'cssAdminWatch', [ 'concatAdminCSS' ], browserSync.reload );
gulp.task( 'jsAdminWatch', [ 'concatAdminScripts' ], browserSync.reload );

gulp.task( 'watchFiles', function() {
  browserSync({
    proxy: {
      target: 'http://plugindev.test/'
    }
  });
  gulp.watch([ '../pub/scss/**/*.scss' ], [ 'cssWatch' ]);
  gulp.watch( '../assets/js/pub-script.js', [ 'jsWatch' ]);
  gulp.watch([ '../admin/scss/**/*.scss' ], [ 'cssAdminWatch' ]);
  gulp.watch( '../assets/js/admin-script.js', [ 'jsAdminWatch' ]);
});

gulp.task( 'serve', [ 'watchFiles' ] );

gulp.task( 'default', [ 'serve' ], function() {
  console.log( 'Welcome to the Gulp task runner!' );
});
