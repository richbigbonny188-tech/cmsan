/* --------------------------------------------------------------
 gulp_theme_jshint.js 2018-11-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp Theme JSHint Task
 *
 * Perform JSHint checks in the admin js files.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const environment = require('../environment');
	const theme = environment.getArgument('theme') || 'Malibu';
	
	function task() {
		const config = {
			jquery: true,
			browser: true,
			camelcase: false,
			eqeqeq: true,
			indent: 4,
			latedef: true,
			maxlen: 120,
			newcap: true,
			quotmark: 'single',
			strict: true,
			undef: true,
			unused: false,
			predef: [
				'jse',
				'gambio',
				'Mustache',
				'Responsive',
				'Events',
				'Interact',
				'Modal',
				'ppp',
				'initPPP',
				'alert',
				'console'
			],
			eqnull: true,
			laxbreak: true,
			laxcomma: true
		};
		
		return gulp.src('../themes/' + theme + '/javascripts/**/*.js')
			.pipe($.jshint(config))
			.pipe($.jshint.reporter('jshint-stylish'));
	}
	
	task.__description = 'Will perform a JSHint check in the JavaScript files.';
	
	return task;
};
