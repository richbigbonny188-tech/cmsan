/* --------------------------------------------------------------
 gulp_admin_watch.js 2016-09-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp Admin Watch Task
 *
 * This task will place a watcher upon the admin scripts and styles and it will execute the
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
				'../admin/javascript/**/*.js'
			],
			['admin:scripts']);
		
		gulp.watch([
				'../admin/styles/**/*.scss',
				'../admin/styles/**/*.css'
			],
			['admin:styles']);
		
		gulp.watch([
				'../admin/html/content/**/*.html'
			],
			['admin:templates']);
	}
	
	task.__description = 'Will start the file watchers for the admin files (prefer the "dev" task for development).';
	
	return task;
};
