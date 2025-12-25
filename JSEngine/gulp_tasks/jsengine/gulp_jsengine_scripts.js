/* --------------------------------------------------------------
 gulp_general_scripts.js 2016-09-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp JS Engine Scripts Task
 *
 * This task will handle the src/JSEngine files concatenation and minification.
 * The final files will be placed in the src/JSEngine/build directory.
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
	const bundle = require('../browserify')(gulp, $, false, '../JSEngine/core/main.js', '../JSEngine/build/jse.js');
	
	function isDir(file) {
		return fs.lstatSync(file.path).isDirectory();
	}
	
	function isHtml(file) {
		return (path.extname(file.path) === '.html');
	}
	
	function task() {
		bundle();
		
		gulp.src('../JSEngine/widgets/**')
			.pipe($.changed('../JSEngine/build/widgets'))
			.pipe($.sourcemaps.init())
			.pipe($.babel({presets: ['es2015']}))
			.pipe($.sourcemaps.write())
			.pipe(gulp.dest('../JSEngine/build/widgets'))
			.pipe(ftp.upload('../JSEngine/build/widgets'))
			.pipe($.ignore.exclude(isDir))
			.pipe($.ignore.exclude(isHtml))
			.pipe($.terser().on('error', $.util.log))
			.pipe($.rename({suffix: '.min'}))
			.pipe(gulp.dest('../JSEngine/build/widgets'))
			.pipe(ftp.upload('../JSEngine/build/widgets'));
		
		gulp.src('../JSEngine/extensions/**')
			.pipe($.changed('../JSEngine/build/extensions'))
			.pipe($.sourcemaps.init())
			.pipe($.babel({presets: ['es2015']}))
			.pipe($.sourcemaps.write())
			.pipe(gulp.dest('../JSEngine/build/extensions'))
			.pipe(ftp.upload('../JSEngine/build/extensions'))
			.pipe($.ignore.exclude(isDir))
			.pipe($.ignore.exclude(isHtml))
			.pipe($.terser().on('error', $.util.log))
			.pipe($.rename({suffix: '.min'}))
			.pipe(gulp.dest('../JSEngine/build/extensions'))
			.pipe(ftp.upload('../JSEngine/build/extensions'));
		
		return gulp.src('../JSEngine/libs/**')
			.pipe($.changed('../JSEngine/build/libs'))
			.pipe($.sourcemaps.init())
			.pipe($.babel({presets: ['es2015']}))
			.pipe($.sourcemaps.write())
			.pipe(gulp.dest('../JSEngine/build/libs'))
			.pipe(ftp.upload('../JSEngine/build/libs'))
			.pipe($.ignore.exclude(isDir))
			.pipe($.ignore.exclude(isHtml))
			.pipe($.terser().on('error', $.util.log))
			.pipe($.rename({suffix: '.min'}))
			.pipe(gulp.dest('../JSEngine/build/libs'))
			.pipe(ftp.upload('../JSEngine/build/libs'));
	}
	
	task.__description = 'Will only build the JSEngine script files.';
	
	return task;
};
