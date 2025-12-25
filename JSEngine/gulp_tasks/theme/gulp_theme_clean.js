/* --------------------------------------------------------------
 gulp_theme_clean.js 2018-11-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp Theme Clean Task
 *
 * This task will remove the admin assets directory entirely.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const del = require('del');
	const environment = require('../environment');
	const theme = environment.getArgument('theme') || 'Malibu';
	
	function task(async) {
		del.sync([
			`../themes/${theme}/fonts/bootstrap`,
			`../themes/${theme}/fonts/font-awesome`,
			`../themes/${theme}/javascripts/system/controllers`,
			`../themes/${theme}/javascripts/system/libs`,
			`../themes/${theme}/javascripts/system/widgets`,
			`../themes/${theme}/javascripts/system/vendor.js`,
			`../themes/${theme}/javascripts/system/vendor.min.js`,
			`../themes/${theme}/javascripts/initialize_theme.js`,
			`../themes/${theme}/javascripts/initialize_theme.min.js`,
			`../themes/${theme}/javascripts/theme_helpers.js`,
			`../themes/${theme}/javascripts/theme_helpers.min.js`,
			`../themes/${theme}/styles/system/bootstrap`,
			`../themes/${theme}/styles/system/fontawesome-free`,
			`../themes/${theme}/styles/vendor.css`,
			`../themes/${theme}/styles/vendor.min.cs`
		], {force: true});
		async();
	}
	
	task.__description = 'Will remove all the auto-generated assets from the theme directory.';
	
	return task;
};
