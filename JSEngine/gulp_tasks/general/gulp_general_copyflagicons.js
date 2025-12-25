/* --------------------------------------------------------------
   gulp_general_copyflagicons.js 2018-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

'use strict';

module.exports = function(gulp, $) {
	function task() {
		gulp.src('node_modules/flag-icon-css/flags/*/*.svg')
			.pipe(gulp.dest('../images/flags'));
		gulp.src('node_modules/flag-icon-css/flags/1x1/gb.svg')
			.pipe($.rename('en.svg'))
			.pipe(gulp.dest('../images/flags/1x1'));
		gulp.src('node_modules/flag-icon-css/flags/4x3/gb.svg')
			.pipe($.rename('en.svg'))
			.pipe(gulp.dest('../images/flags/4x3'));
	}

	task.__description = 'Copy required files from flag-icon-css.';
	
	return task;
}

