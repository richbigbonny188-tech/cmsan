/* --------------------------------------------------------------
 gulp_gxmodules_clean.js 2021-07-24
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp GXModules Clean Task
 *
 * This task will remove all the "build" directories of GXModules.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const del = require('del');
	
	function task(async) {
		del.sync([
			'../GXModules/*/*/Build',
			'!../GXModules/Gambio/StyleEdit/Build'
		], {force: true});
		async();
	}
	
	task.__description = 'Will remove all the auto-generated "build" directory from GXModules.';
	
	return task;
};
