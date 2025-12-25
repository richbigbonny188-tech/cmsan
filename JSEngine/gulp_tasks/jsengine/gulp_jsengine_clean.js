/* --------------------------------------------------------------
 gulp_general_clean.js 2016-09-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp JS Engine Clean Task
 *
 * This task will remove the src/JSEngine/build directory and its files.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const del = require('del');
	
	function task(async) {
		del.sync(['../JSEngine/build'], {force: true});
		async();
	}
	
	task.__description = 'Will remove all the auto-generated assets from the JSEngine/build directory.';
	
	return task;
};
