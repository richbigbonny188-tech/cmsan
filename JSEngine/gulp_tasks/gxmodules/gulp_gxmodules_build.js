/* --------------------------------------------------------------
 gulp_gxmodules_build.js 2017-03-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp GXModules Build Task
 *
 * This task will build the GXModules javascript and style files.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const gulpsync = require('gulp-sync')(gulp);
	
	const task = gulpsync.sync(['gxmodules:clean', 'gxmodules:scripts', 'gxmodules:styles'], 'gxmodules:build');
	
	task.__description = 'Will build all the assets of the GXModules domain.';
	
	return task;
};
