/* --------------------------------------------------------------
 gulp_general_jshint.js 2021-05-04
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp General JSHint Task
 *
 * This task will execute all the JSHint tasks.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const gulpsync = require('gulp-sync')(gulp);
	
	const task = gulpsync.sync(['jsengine:jshint', 'admin:jshint'],
		'general:jshint');
	
	task.__description = 'Will execute all the JSHint tasks from every domain.';
	
	return task;
}; 