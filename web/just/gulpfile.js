'use strict';

var gulp = require('gulp'),
		watch = require('gulp-watch'),
		prefixer = require('gulp-autoprefixer'),
		less = require('gulp-less'),
		cssmin = require('gulp-cssmin'),
		rename = require('gulp-rename'),
		pug = require('gulp-pug'),
		imagemin = require('gulp-imagemin'),
		pngquant = require('imagemin-pngquant'),
		rimraf = require('rimraf'),
		concat = require('gulp-concat'),
		uglify = require('gulp-uglify'),
		browserSync = require('browser-sync').create(),
		extend = require('extend'),
		// Formating 'code' blocks
		pugCodeFilter = function( block ) {
	    return block
	        .replace( /&/g, '&amp;'  )
	        .replace( /</g, '&lt;'   )
	        .replace( />/g, '&gt;'   )
	        .replace( /"/g, '&quot;' )
	        .replace( /#/g, '&#35;'  )
	        .replace( /\\/g, '\\\\'  )
	        .replace( /\n/g, '\n'   );
		}

var path = {
	dist: {
		html: 'dist/',
		js: 'dist/js/',
		css: 'dist/css/',
		img: 'dist/img/',
		libs: 'dist/libs/',
		other: 'dist/'
	},
	src: {
		pug: 'src/pug/*.pug',
		js: 'src/js/**/*.js',
		less: 'src/less/',
		img: 'src/img/**/*.*',
		libs: 'src/libs/',
		other: 'src/*.*'
	},
	watch: {
		pug: 'src/pug/**/*.*',
		js: 'src/js/**/*.js',
		less: 'src/less/**/*.*',
		img: 'src/img/**/*.*',
		libs: 'src/libs/**/*.*',
		other: 'src/*.*'
	}
};

gulp.task('pug:build', function () {
	gulp.src(path.src.pug)
		.pipe(
			pug({
						pretty: true,
						filters: { code: pugCodeFilter },
						locals: { analytics: false }
					})
		)
		.pipe(gulp.dest(path.dist.html));
});

gulp.task('js:build', function () {
	gulp.src(path.src.js)
		.pipe(gulp.dest(path.dist.js));
});

gulp.task('less:build', function () {
	gulp.src(path.src.less+'*.less')
		.pipe(less())
		.pipe(prefixer('last 2 versions'))
		.pipe(gulp.dest(path.dist.css))
		.pipe(cssmin())
		.pipe(rename({suffix: '.min'}))
		.pipe(gulp.dest(path.dist.css));
});

gulp.task('libs:build', function() {
	// transfer all libs to dist
	gulp.src(path.src.libs + '**/*.*')
		.pipe(gulp.dest(path.dist.libs))

	// concat and min common libs js
	gulp.src([
			path.src.libs + 'jquery/jquery.js',
			path.src.libs + 'bootstrap/js/bootstrap.js',
			path.src.libs + 'jquery.scrollbar/jquery.scrollbar.js',
			path.src.libs + 'ion-rangeSlider/js/ion.rangeSlider.js',
			path.src.libs + 'moment/moment.js',
			path.src.libs + 'select2/js/select2.full.js',
			path.src.libs + 'popup/popup.js',
			path.src.libs + 'switchery/index.js',
			path.src.libs + 'highlight.js/highlight.pack.js',
			path.src.libs + 'vkbeautify/vkbeautify.js',
			path.src.libs + 'bootstrap-tabdrop/js/ab.tabdrop.js',
			path.src.libs + 'panel-tools/panel-tools.js'
		])
		.pipe(concat('libs.common.js'))
		.pipe(gulp.dest(path.dist.js))
		.pipe(uglify())
		.pipe(rename({suffix: '.min'}))
		.pipe(gulp.dest(path.dist.js))

	// concat and min common libs css
	gulp.src([
			path.src.libs + 'jquery.scrollbar/jquery.scrollbar.css',
			path.src.libs + 'awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css',
			path.src.libs + 'select2/css/select2.css',
			path.src.libs + 'highlight.js/styles/atom-one-light.css',
			path.src.libs + 'ion-rangeSlider/css/ion.rangeSlider.css',
			path.src.libs + 'ion-rangeSlider/css/ion.rangeSlider.skinFlat.css',
			path.src.libs + 'switchery/index.css',
			path.src.libs + 'bootstrap-tabdrop/css/tabdrop.css'
		])
		.pipe(concat('libs.common.css'))
		.pipe(gulp.dest(path.dist.css))
		.pipe(cssmin())
		.pipe(rename({suffix: '.min'}))
		.pipe(gulp.dest(path.dist.css));
});

gulp.task('image:build', function () {
	gulp.src(path.src.img)
		.pipe(imagemin({
			progressive: true,
			svgoPlugins: [{removeViewBox: false}],
			use: [pngquant()],
			interlaced: true
		}))
		.pipe(gulp.dest(path.dist.img));
});

gulp.task('other:build', function() {
	gulp.src(path.src.other)
		.pipe(gulp.dest(path.dist.other));
});


gulp.task('clean', function (cb) {
	rimraf('./dist', cb);
});

gulp.task('build', [
	'js:build',
	'less:build',
	'libs:build',
	'image:build',
	'pug:build',
	'other:build'
]);

gulp.task('watch', function(){
	watch([path.watch.pug], function(event, cb) {
		gulp.start('pug:build');
	});
	watch([path.watch.less], function(event, cb) {
		gulp.start('less:build');
	});
	watch([path.watch.js], function(event, cb) {
		gulp.start('js:build');
	});
	watch([path.watch.libs], function(event, cb) {
		gulp.start('libs:build');
	});
	watch([path.watch.img], function(event, cb) {
		gulp.start('image:build');
	});
});

gulp.task('serve', ['build'], function() {
	browserSync.init({
		server: {
			baseDir: "dist/"
		}
	});
});

gulp.task('default', ['build', 'watch']);
