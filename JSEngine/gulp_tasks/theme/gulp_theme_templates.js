/* --------------------------------------------------------------
 gulp_theme_themes.js 2018-11-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp Theme Themes Task
 *
 * This task will clear the cache/smarty directory and upload any changed theme files. It is
 * used in cooperation with the watch task to remove the theme cache whenever there are changes
 * in the "/themes/{Theme}/html" directories.
 *
 * It will also remove the FTP files if there is an active FTP connection.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const fs = require('vinyl-fs');
	const del = require('del');
	const environment = require('../environment');
	const theme = environment.getArgument('theme') || 'Malibu';
	const ftp = require('../ftp');
	
	function task() {
		try {
			// Remove the template cache locally. 
			del.sync('./src/cache/smarty/*.php');
			$.util.log('Refresh Template Cache: Removed template cache locally.');
			
			if (global.ftpConn !== undefined) {
				// Upload the html files to the server.
				gulp.src([
					`../themes/${theme}/html/**/*.html`
				])
					.pipe($.changedInPlace())
					.pipe(ftp.upload(`../themes/${theme}`));
				
				gulp.src([
					`../public/theme/html/**/*.html`
				])
					.pipe($.changedInPlace())
					.pipe(ftp.upload(`../public/theme/${theme}`));
				
				// Remove the themes from the FTP server (if connected).	
				global.ftpConn.rmdir(global.ftpConn.config.dest + '/src/cache/smarty', function() {
					fs.src(['./src/cache/smarty'], {buffer: false})
						.pipe(global.ftpConn.dest(global.ftpConn.config.dest + '/src'));
					$.util.log('Refresh Template Cache: Removed theme cache on FTP server.')
				});
			}
		} catch (exception) {
			$.util.log($.util.colors.red('Refresh Template Cache Error: ', exception));
		}
	}
	
	task.__description = 'Will remove the cache files in "cache/smarty" and upload the changed html files '
		+ '(ftp-config.json is required).';
	
	return task;
};
