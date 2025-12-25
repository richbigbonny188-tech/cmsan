/* --------------------------------------------------------------
 ftp.js 2016-09-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

const fs = require('fs');
const prompt = require('prompt');
const gulpUtil = require('gulp-util');
const gulpIf = require('gulp-if');
const gulpTerser = require('gulp-terser');

/**
 * Custom FTP Handler
 *
 * This module handles the ftp config initialization by reading the "ftp-config.js" file and
 * by getting the FTP credentials from the terminal. It is better that your execute this module
 * in the beginning of each task collection so that terminal logs from other tasks do not interfere
 * with the credentials prompt.
 */
module.exports = {
	/**
	 * Initialize FTP connection.
	 *
	 * This function must be called at the beginning of each "dev" task in order to
	 * get the user's credentials before other tasks start outputting logs in the
	 * terminal.
	 *
	 * @return {Promise} Returns a project object that will be resolved with the FTP
	 * connection configuration settings.
	 */
	init() {
		return new Promise((resolve, reject) => {
			try {
				// Check if the FTP configuration file is present.
				fs.lstatSync('ftp-config.json');
				
				// If there is already a connection don't prompt the user for a new one because the FTP tasks 
				// will use the existing connection. 
				if (global.ftpConn) {
					resolve();
					return;
				}
				
				// Get FTP username and password from the terminal.
				gulpUtil.log(gulpUtil.colors.yellow('An "ftp-config.json" file was detected, please provide your '
					+ 'FTP credentials in order to connect with your server:'));
				prompt.start();
				prompt.get([
					{
						name: 'username',
						required: true
					},
					{
						name: 'password',
						hidden: true
					}
				], (error, credentials) => {
					try {
						if (error) {
							throw(error);
						}
						
						const file = JSON.parse(fs.readFileSync('ftp-config.json', 'utf8'));
						
						const config = {
							host: file.host,
							port: file.port,
							dest: file.dest,
							user: credentials.username,
							password: credentials.password,
							parallel: 10,
							log: (status, path) => {
								// Log the files that were uploaded.
								if (status.indexOf('PUT') !== -1) {
									gulpUtil.log('FTP Upload: ' + path.replace(/.*(src.*)/, '$1'));
								} else if (status.indexOf('RMDIR') !== -1 || status.indexOf('DEL') !== -1) {
									gulpUtil.log('FTP Removal: ' + path);
								}
							},
							reload: true
						};
						
						resolve(config);
					} catch (exception) {
						reject(exception);
					}
				});
			} catch (exception) {
				// No config file was found, continue without an FTP connection ...
				resolve();
			}
		});
	},
	
	/**
	 * Upload Stream Files
	 *
	 * This method must be used inside the tasks that need to upload file through FTP. It must
	 * be integrated as the true-child of the gulp-if pipe.
	 *
	 * Notice: If the global.ftpConn object is not defined the method returns the $.uglify() as
	 * a workaround to the gulp-if execution, which requires a such a command even if the condition is
	 * false. It will not actually be executed.
	 *
	 * @param {String} path Contains the destination directory to be used for the upload.
	 */
	upload(path) {
		if (path.substr(0, 1) === '/') {
			path = path.substr(1);
		}
		
		if (path.substr(-1) === '/') {
			path = path.substr(0, path.length - 1);
		}
		
		return gulpIf(global.ftpConn,
			global.ftpConn ? global.ftpConn.dest(global.ftpConn.config.dest + '/' + path) : gulpTerser());
	}
};
