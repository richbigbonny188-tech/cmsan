/* --------------------------------------------------------------
 gulp_general_build.js 2021-07-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp General Build Task
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const gulpsync = require('gulp-sync')(gulp);
	
	const task = gulpsync.sync([
		'jsengine:build',
		'admin:build',
		'themes:build',
		'gxmodules:build',
		'general:copyflagicons'
	], 'general:build');
	
	task.__description = 'Will execute the build tasks from all the registered domains (equals to gulp command).';
	
	return task;
};
