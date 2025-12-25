/* --------------------------------------------------------------
 gulp_admin_vendor.js 2018-10-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Admin Vendor Task
 *
 * This task will fetch the vendor files for admin section.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const vendor = require('../vendor');
	const ftp = require('../ftp');
	
	function bundleCompatibility() {
		const packages = [
			'jquery-migrate',
			'jquery-ui-dist',
			'pace-js',
			'srcdoc-polyfill',
			'mustache'
		];
		
		const overrides = {
			'mustache': {
				'main': 'mustache.min.js'
			},
			'jquery-ui-dist': {
				'main': [
					'jquery-ui.js',
					'jquery-ui.css'
				]
			}
		};
		
		const scripts = vendor('js', packages, overrides);
		
		gulp.src(scripts)
			.pipe($.sourcemaps.init())
			.pipe($.concat('compatibility-vendor.js'))
			.pipe($.sourcemaps.write())
			.pipe(gulp.dest('../admin/html/assets/javascript'))
			.pipe(ftp.upload('../admin/html/assets/javascript'))
			.pipe($.terser())
			.pipe($.rename({suffix: '.min'}))
			.pipe(gulp.dest('../admin/html/assets/javascript'))
			.pipe(ftp.upload('../admin/html/assets/javascript'));
		
		const styles = vendor('css', packages, overrides);
		
		gulp.src(styles)
			.pipe($.sourcemaps.init())
			.pipe($.concat('compatibility-vendor.css'))
			.pipe($.sourcemaps.write())
			.pipe(gulp.dest('../admin/html/assets/styles'))
			.pipe(ftp.upload('../admin/html/assets/styles'))
			.pipe($.cleanCss())
			.pipe($.rename({suffix: '.min'}))
			.pipe(gulp.dest('../admin/html/assets/styles'))
			.pipe(ftp.upload('../admin/html/assets/styles'));
	}
	
	function bundleAdmin() {
		const packages = [
			'bootstrap',
			'pace-js'
		];
		
		const overrides = {
			'bootstrap': {
				'main': ['dist/js/bootstrap.js']
			}
		};
		
		const scripts = vendor('js', packages, overrides);
		
		gulp.src(scripts)
			.pipe($.sourcemaps.init())
			.pipe($.concat('admin-vendor.js'))
			.pipe($.sourcemaps.write())
			.pipe(gulp.dest('../admin/html/assets/javascript'))
			.pipe(ftp.upload('../admin/html/assets/javascript'))
			.pipe($.terser())
			.pipe($.rename({suffix: '.min'}))
			.pipe(gulp.dest('../admin/html/assets/javascript'))
			.pipe(ftp.upload('../admin/html/assets/javascript'));
	}
	
	function task() {
		// Bundle explicit vendor libraries for compatibility mode.
		bundleCompatibility();
		
		// Bundle explicit vendor libraries for new pages. 
		bundleAdmin();
		
		// Bootstrap and FontAwesome fonts.
		gulp.src('node_modules/@fortawesome/fontawesome-free/webfonts/**')
			.pipe(gulp.dest('../admin/html/assets/fonts/fontawesome-free'));

		return gulp.src('node_modules/bootstrap-sass/assets/fonts/bootstrap/**')
			.pipe(gulp.dest('../admin/html/assets/fonts/bootstrap'));
	}
	
	task.__description = 'Will build vendor.js and vendor.css files in the admin assets directory.';
	
	return task;
};
