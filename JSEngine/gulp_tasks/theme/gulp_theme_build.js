/* --------------------------------------------------------------
 gulp_theme_build.js 2018-11-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp Theme Build Task
 *
 * This task will build the theme javascript and scripts files.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const gulpsync = require('gulp-sync')(gulp);
	
	const task = gulpsync.sync([
		'theme:clean', 'theme:vendor', 'theme:scripts'
	], 'theme:build');
	
	task.__description = 'Will build all the assets of the selected theme.';
	
	return task;
};
