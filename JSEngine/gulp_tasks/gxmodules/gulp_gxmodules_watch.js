/* --------------------------------------------------------------
 gulp_gxmodules_watch.js 2017-03-28
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp GXModules Watch Task
 *
 * This task will place a watcher upon the GXModules scripts and styles and it will execute the
 * required operations whenever a file is changed.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	function task() {
		gulp.watch([
				'../GXModules/**/*.js',
				'!src/GXModules/**/*.min.js',
				'!src/GXModules/*/*/Build/**',
				'!src/GXModules/**/Themes/**',
				'!src/GXModules/**/Templates/**'
			],
			['gxmodules:scripts']);
		
		gulp.watch([
				'../GXModules/**/*.css',
				'!src/GXModules/**/*.min.css',
				'!src/GXModules/*/*/Build/**',
				'!src/GXModules/**/Themes/**',
				'!src/GXModules/**/Templates/**'
			],
			['gxmodules:styles']);
		
		gulp.watch([
				'../GXModules/**/*.html'
			],
			['gxmodules:templates']);
	}
	
	task.__description =
		'Will start the file watchers for the GXModules files (prefer the "dev" task for development).';
	
	return task;
};
