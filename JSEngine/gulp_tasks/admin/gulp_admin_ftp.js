/* --------------------------------------------------------------
 gulp_admin_ftp.js 2016-09-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp Admin FTP Task
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const ftp = require('../ftp');
	const vinylFtp = require('vinyl-ftp');
	
	function upload(async) {
		global.ftpConnecting = true; // Determines that a task is trying to connect to FTP server.
		
		ftp
			.init()
			.then(config => {
				if (!global.ftpConn) {
					// If there is no connection try to create a new one. 
					if (config === undefined) {
						throw new Error('No "ftp-config.json" file was found.');
					}
					
					global.ftpConn = vinylFtp.create(config);
				}
				
				global.ftpConnecting = false;
				
				gulp.src('../admin/html/assets/**', {base: '.', buffer: false})
					.pipe(global.ftpConn.dest(global.ftpConn.config.dest));
				
				async();
			})
			.catch(exception => {
				console.log('FTP init error:', exception);
				async();
			});
	}
	
	function task(async) {
		if (global.ftpConnecting === true) {
			// Another task is connecting so wait until it is ready.
			const interval = setInterval(() => {
				if (global.ftpConnecting === false && global.ftpConn !== undefined) {
					clearInterval(interval);
					upload(async);
				}
			}, 100);
		} else {
			// Connect and upload the files. 
			upload(async);
		}
	}
	
	task.__description = 'Will manually upload the admin assets to the server (ftp-config.json is required).';
	
	return task;
};
