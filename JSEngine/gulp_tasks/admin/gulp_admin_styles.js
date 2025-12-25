/* --------------------------------------------------------------
 gulp_admin_styles.js 2016-09-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp Admin Styles Task
 *
 * This task will handle the compilation of the gx-admin.scss file.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const fs = require('fs');
	const path = require('path');
	const ftp = require('../ftp');
	const notifier = require('node-notifier');
	
	function isDir(file) {
		return fs.lstatSync(file.path).isDirectory();
	}
	
	function isHtml(file) {
		return (path.extname(file.path) === '.html');
	}
	
	function task() {
		gulp.src('../admin/styles/**/*.scss')
			.pipe($.changed('../admin/styles/*.scss'))
			.pipe($.sourcemaps.init())
			.pipe($.sass({
				css: '../admin/html/assets/styles',
				sass: '../admin/styles',
				style: 'expanded',
				sourcemap: true,
				logging: true
			}))
			.pipe($.sourcemaps.write())
			.pipe(gulp.dest('../admin/html/assets/styles'))
			.pipe(ftp.upload('../admin/html/assets/styles'))
			.pipe($.cleanCss())
			.pipe($.rename({suffix: '.min'}))
			.pipe(gulp.dest('../admin/html/assets/styles'))
			.pipe(ftp.upload('../admin/html/assets/styles'));
		
		return gulp.src('../admin/styles/legacy/**')
			.pipe($.changed('../admin/html/assets/styles/legacy'))
			.pipe(gulp.dest('../admin/html/assets/styles/legacy'))
			.pipe(ftp.upload('../admin/html/assets/styles/legacy'))
			.pipe($.ignore.exclude(isDir))
			.pipe($.ignore.exclude(isHtml))
			.pipe($.cleanCss())
			.pipe($.rename({suffix: '.min'}))
			.pipe(gulp.dest('../admin/html/assets/styles/legacy'))
			.pipe(ftp.upload('../admin/html/assets/styles/legacy'));
	}
	
	task.__description = 'Will only build the admin style files.';
	
	return task;
};
