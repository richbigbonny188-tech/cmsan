/* --------------------------------------------------------------
 gulp_general_post_configure.js 2016-11-04
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp General Post-Configure Task
 *
 * This task will execute all the post-configuration tasks.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const fs = require('fs');
	
	function task() {
		try {
			// Check if the configure.php file exists.
			fs.lstatSync('../includes/configure.php');
		} catch (e) {
			// Create initial configuration files. 
			gulp.src('../includes/configure.release.php')
				.pipe($.chmod(777))
				.pipe($.rename('configure.php'))
				.pipe(gulp.dest('../includes'))
				.pipe(gulp.dest('../admin/includes'))
				.pipe($.rename('configure.org.php'))
				.pipe(gulp.dest('../includes'))
				.pipe(gulp.dest('../admin/includes'));
		}
	}
	
	task.__description = 'Perform post-configuration tasks.';
	
	return task;
}; 