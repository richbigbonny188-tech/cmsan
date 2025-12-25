/* --------------------------------------------------------------
 gulp_theme_vendor.js 2018-11-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Theme Vendor Task
 *
 * This task will fetch the vendor files for Theme.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const vendor = require('../vendor');
	const ftp = require('../ftp');
	const environment = require('../environment');
	const theme = environment.getArgument('theme') || 'Malibu';
	
	function bundleTheme() {
		const packages = [
			'honeygrid-modernizr',
			'html5shiv',
			'magnific-popup',
			'mustache',
			'respond.js',
			'swiper',
			'bootstrap-sass'
		];
		const overrides = {
			'swiper': {
				'main': [
					'dist/js/swiper.js',
					'dist/css/swiper.css'
				]
			},
			'mustache': {
				'main': 'mustache.min.js'
			},
			'respond.js': {
				'main': 'dest/respond.min.js'
			},
			'magnific-popup': {
				'main': [
					'dist/jquery.magnific-popup.js',
					'dist/magnific-popup.css'
				]
			},
			'bootstrap-sass': {
				'main': [
					'assets/javascripts/bootstrap.js',
				]
			}
		};
		
		const scripts = vendor('js', packages, overrides);
		
		gulp.src(scripts)
			.pipe($.sourcemaps.init())
			.pipe($.concat('vendor.js'))
			.pipe($.sourcemaps.write())
			.pipe(gulp.dest(`../themes/${theme}/javascripts/system`))
			.pipe(ftp.upload(`../themes/${theme}/javascripts/system`))
			.pipe($.terser())
			.pipe($.rename({suffix: '.min'}))
			.pipe(gulp.dest(`../themes/${theme}/javascripts/system`))
			.pipe(ftp.upload(`../themes/${theme}/javascripts/system`));
		
		const styles = vendor('css', packages, overrides);
		
		gulp.src(styles)
			.pipe($.sourcemaps.init())
			.pipe($.concat('vendor.css'))
			.pipe($.sourcemaps.write())
			.pipe(gulp.dest(`../themes/${theme}/styles/system`))
			.pipe(ftp.upload(`../themes/${theme}/styles/system`))
			.pipe($.cleanCss())
			.pipe($.rename({suffix: '.min'}))
			.pipe(gulp.dest(`../themes/${theme}/styles/system`))
			.pipe(ftp.upload(`../themes/${theme}/styles/system`));
	}
	
	function task() {
		// Bundle explicit vendor dependencies.
		bundleTheme();
		
		// Copy Bootstrap and Font Awesome files. 
		gulp.src('node_modules/bootstrap-sass/assets/stylesheets/**')
			.pipe(gulp.dest(`../themes/${theme}/styles/system/bootstrap`))
			.pipe(ftp.upload(`../themes/${theme}/styles/system/bootstrap`));
		
		gulp.src('node_modules/bootstrap-sass/assets/fonts/**')
			.pipe(gulp.dest(`../themes/${theme}/fonts`))
			.pipe(ftp.upload(`../themes/${theme}/fonts/bootstrap`));
		
		// FontAwesome 5
		gulp.src('node_modules/@fortawesome/fontawesome-free/webfonts/**')
			.pipe(gulp.dest(`../themes/${theme}/fonts/fontawesome-free`))
			.pipe(ftp.upload(`../themes/${theme}/fonts/fontawesome-free`));
		
		gulp.src('node_modules/@fortawesome/fontawesome-free/scss/**')
			.pipe(gulp.dest(`../themes/${theme}/styles/system/fontawesome-free`))
			.pipe(ftp.upload(`../themes/${theme}/styles/system/fontawesome-free`));
	}
	
	task.__description = 'Will build vendor.js and vendor.css files in the theme assets directory.';
	
	return task;
};
