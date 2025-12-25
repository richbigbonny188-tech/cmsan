/* --------------------------------------------------------------
 gulp_admin_clean.js 2016-09-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp Admin Clean Task
 *
 * This task will remove the admin javascript and styles directories.
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
			'../admin/html/assets/fonts/bootstrap',
			'../admin/html/assets/fonts/font-awesome',
			'../admin/html/assets/javascript/engine',
			'../admin/html/assets/javascript/legacy',
			'../admin/html/assets/javascript/modules/hermes',
			'../admin/html/assets/javascript/vendor*',
			'../admin/html/assets/styles/legacy',
			'../admin/html/assets/styles/admin*',
			'../admin/html/assets/styles/compatibility*',
			'../admin/html/assets/styles/vendor*'
		], {force: true});
		async();
	}
	
	task.__description = 'Will remove all the auto-generated files from the /admin/html/assets directory.';
	
	return task;
};
