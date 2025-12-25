/* --------------------------------------------------------------
 gulp_theme_watch.js 2018-11-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

const enrivonment = require('../environment');
const theme = enrivonment.getArgument('theme') || 'Malibu';

/**
 * Gulp Theme Watch Task
 *
 * This task will place a watcher upon the theme scripts and styles and it will execute the
 * required operations whenever a file is changed.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	function task() {
		gulp.watch([
				`../themes/${theme}/javascripts/**/*.js`
			],
			['theme:scripts']);
		
		gulp.watch([
				`../themes/${theme}/styles/**/*.scss`,
				`../themes/${theme}/styles/**/*.css`,
				`!src/themes/${theme}/styles/styles/system/.css`,
				`../public/theme/styles/**/*.scss`,
				`../public/theme/styles/**/*.css`,
				`!src/public/theme/styles/system/main.css`
			],
			['theme:refresh_cache']);
		
		gulp.watch([
				`../themes/${theme}/html/**/*.html`,
				`../public/theme/html/**/*.html`
			],
			['theme:templates']);
	}
	
	task.__description = 'Will start the file watchers for the theme files (prefer the "dev" task for development).';
	
	return task;
};
