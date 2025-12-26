/* --------------------------------------------------------------
 gulp_themes_build.js 2021-07-28
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp Themes Build Task
 *
 * This task will build the theme javascript and scripts files for the themes Malibu and Honeygrid.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const gulpsync = require('gulp-sync')(gulp);
	
	gulp.task('Malibu:clean', $.shell.task('gulp theme:clean --theme Malibu', {quiet: true}));
	gulp.task('Malibu:vendor', $.shell.task('gulp theme:vendor --theme Malibu', {quiet: true}));
	gulp.task('Malibu:scripts', $.shell.task('gulp theme:scripts --theme Malibu', {quiet: true}));
	gulp.task('Honeygrid:clean', $.shell.task('gulp theme:clean --theme Honeygrid', {quiet: true}));
	gulp.task('Honeygrid:vendor', $.shell.task('gulp theme:vendor --theme Honeygrid', {quiet: true}));
	gulp.task('Honeygrid:scripts', $.shell.task('gulp theme:scripts --theme Honeygrid', {quiet: true}));
    
    const task = gulpsync.sync([
        ['Malibu:clean', 'Honeygrid:clean'],
        ['Malibu:vendor', 'Malibu:scripts', 'Honeygrid:vendor', 'Honeygrid:scripts']
    ], 'themes:build');

	task.__description = 'Will build all the assets of the themes Malibu and Honeygrid.';
	
	return task;
};
