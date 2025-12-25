/* --------------------------------------------------------------
 gulp_admin_scripts.js 2016-09-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

const es2015 = require('babel-preset-es2015');

/**
 * Gulp Admin Scripts Task
 *
 * This task will concatenate and minify the admin js files. The final files will be
 * placed in the admin/html/assets/javascript directory.
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
	
	function isDir(file) {
		return fs.lstatSync(file.path).isDirectory();
	}
	
	function isHtml(file) {
		return (path.extname(file.path) === '.html');
	}
	
	function compile(src, dest) {
		gulp.src(src + '/**')
			.pipe($.changed(dest))
			.pipe($.sourcemaps.init())
			.pipe($.babel({presets: [es2015]}))
			.pipe($.sourcemaps.write())
			.pipe(gulp.dest(dest))
			.pipe(ftp.upload(dest))
			.pipe($.ignore.exclude(isDir))
			.pipe($.ignore.exclude(isHtml))
			.pipe($.terser().on('error', $.util.log))
			.pipe($.rename({suffix: '.min'}))
			.pipe(gulp.dest(dest))
			.pipe(ftp.upload(dest));
	}
	
	function task() {
		const src = '../admin/javascript';
		const dest = '../admin/html/assets/javascript';
		const directories = [
			'engine',
			'modules'
		];
		
		directories.forEach(directory => {
			const subdirectories = fs.readdirSync(src + '/' + directory).filter(file => {
				return fs.statSync(path.join(src + '/' + directory, file)).isDirectory();
			});
			
			subdirectories.forEach(subdirectory => {
				compile(src + '/' + directory + '/' + subdirectory, dest + '/' + directory + '/' + subdirectory);
			});
		});
		
		// Compile legacy files ...
		return gulp.src('../admin/javascript/legacy/**')
			.pipe($.changed('../admin/html/assets/javascript/legacy'))
			.pipe(gulp.dest('../admin/html/assets/javascript/legacy'))
			.pipe(ftp.upload('../admin/html/assets/javascript/legacy'))
			.pipe($.ignore.exclude(isDir))
			.pipe($.ignore.exclude(isHtml))
			.pipe($.terser().on('error', $.util.log))
			.pipe($.rename({suffix: '.min'}))
			.pipe(gulp.dest('../admin/html/assets/javascript/legacy'))
			.pipe(ftp.upload('../admin/html/assets/javascript/legacy'));
	}
	
	task.__description = 'Will only build the admin javascript files.';
	
	return task;
};
