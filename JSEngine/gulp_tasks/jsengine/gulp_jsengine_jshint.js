/* --------------------------------------------------------------
 gulp_general_jshint.js 2016-09-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp JS Engine JSHint Task
 *
 * Execute the JSHint directly on the src/JSEngine files.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	function task() {
		const config = {
			jquery: true,
			browser: true,
			esversion: 6,
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
				'CKEDITOR',
				'require',
				'console',
				'alert'
			],
			eqnull: true,
			laxbreak: true,
			laxcomma: true
		};
		
		return gulp.src([
			'../JSEngine/**/*.js',
			'!src/JSEngine/build/**'
		])
			.pipe($.jshint(config))
			.pipe($.jshint.reporter('jshint-stylish'));
	}
	
	task.__description = 'Will perform a JSHint check in the javascript files.';
	
	return task;
};
