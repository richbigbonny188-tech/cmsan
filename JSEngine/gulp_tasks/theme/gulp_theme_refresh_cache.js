/* --------------------------------------------------------------
 gulp_theme_refresh_cache.js 2018-11-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Remove the required files from the server's cache directory via FTP.
 *
 * This task is useful because it will automatically clear the required cache files so that
 * we always get the new changes while developing Theme.
 *
 * Important: This task can only be executed as part of the "dev" task.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function}
 */
module.exports = function(gulp, $) {
	const del = require('del');
	
	function removeRemoteFile(path) {
		try {
			if (typeof global.ftpConn !== 'undefined') {
				global.ftpConn.delete(global.ftpConn.config.dest + '/src/' + path, () => {
				});
				$.util.log(`Refresh Cache: Removed ${path} on FTP server.`);
			}
		} catch (exception) {
			$.util.log($.util.colors.red('Refresh Cache Error: ' + exception));
		}
	}
	
	function removeLocalFile(glob) {
		try {
			del.sync('./src/' + glob);
			$.util.log(`Refresh Cache: Removed ${glob} locally.`);
		} catch (exception) {
			$.util.log($.util.colors.red('Refresh Cache Error: ' + exception));
		}
	}
	
	function task() {
		// FTP removals (no glob support). 
		removeRemoteFile('cache/__dynamics.css');
		removeRemoteFile('public/theme/styles/system/main.css');
		
		// Local removals (glob support). 
		removeLocalFile('cache/__dynamics*');
		removeLocalFile('public/theme/styles/system/main.css');
	}
	
	task.__description = 'Will remove the CSS cache (either on FTP server or locally).';
	
	return task;
};
