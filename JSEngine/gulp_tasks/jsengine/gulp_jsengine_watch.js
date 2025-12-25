/* --------------------------------------------------------------
 gulp_general_watch.js 2016-09-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp JS Engine Watch Task
 *
 * This tasks checks for changes in the src/JSEngine directory and performs the required actions.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	function task() {
		
		gulp.watch([
			'./constructors/**/*.js',
			'./core/**/*.js',
			'./extensions/**/*.js',
			'./libs/**/*.js',
			'./widgets/**/*.js',
		], ['jsengine:scripts'])
		
		// gulp.watch([
		// 		'./**/*.js',
		// 		'!./build/**',
		// 		'!./gulp_tasks/**',
		// 	],
		// 	['jsengine:scripts']);
	}
	
	task.__description = 'Will start the file watchers for the JSEngine files (prefer the "dev" task for development).';
	
	return task;
};
