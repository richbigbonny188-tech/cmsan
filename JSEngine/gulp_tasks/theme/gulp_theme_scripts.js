/* --------------------------------------------------------------
 gulp_theme_scripts.js 2018-11-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

const es2015 = require('babel-preset-es2015');

/**
 * Gulp Theme Scripts Task
 *
 * This task will concatenate and minify the theme JS Engine modules. The final files will be
 * placed in the themes/{Theme}/javascripts/source directory.
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
	const environment = require('../environment');
	const theme = environment.getArgument('theme') || 'Malibu';
	
	function isDir(file) {
		return fs.lstatSync(file.path).isDirectory();
	}
	
	function isHtml(file) {
		return (path.extname(file.path) === '.html');
	}
	
	function task() {
		return gulp.src(`../themes/${theme}/javascripts/source/**`)
			.pipe($.changed(`../themes/${theme}/javascripts/system`))
			.pipe($.sourcemaps.init())
			.pipe($.babel({presets: [es2015]}))
			.pipe($.sourcemaps.write())
			.pipe(gulp.dest(`../themes/${theme}/javascripts/system`))
			.pipe(ftp.upload(`../themes/${theme}/javascripts/system`))
			.pipe($.ignore.exclude(isDir))
			.pipe($.ignore.exclude(isHtml))
			.pipe($.terser().on('error', $.util.log))
			.pipe($.rename({suffix: '.min'}))
			.pipe(gulp.dest(`../themes/${theme}/javascripts/system`))
			.pipe(ftp.upload(`../themes/${theme}/javascripts/system`));
	}
	
	task.__description = 'Will only build the theme JavaScript files.';
	
	return task;
};
