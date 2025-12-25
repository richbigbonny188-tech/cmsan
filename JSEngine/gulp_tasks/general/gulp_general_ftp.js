/* --------------------------------------------------------------
 gulp_general_ftp.js 2021-05-04
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp General FTP Task
 *
 * This task will execute all the FTP tasks.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const gulpsync = require('gulp-sync')(gulp);
	
	const task = gulpsync.sync(['jsengine:ftp', 'admin:ftp'], 'general:ftp');
	
	task.__description = 'Will execute all the FTP tasks which will actually upload all the asset files.';
	
	return task;
}; 