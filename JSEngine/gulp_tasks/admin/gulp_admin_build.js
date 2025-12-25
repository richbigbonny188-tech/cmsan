/* --------------------------------------------------------------
 gulp_admin_build.js 2016-09-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp Admin Build Task
 *
 * This task will build the admin javascript and scripts files.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const gulpsync = require('gulp-sync')(gulp);
	
	const task = gulpsync.sync(['admin:clean', 'admin:scripts', 'admin:styles', 'admin:vendor'], 'admin:build');
	
	task.__description = 'Will build all the assets of the admin domain.';
	
	return task;
};
