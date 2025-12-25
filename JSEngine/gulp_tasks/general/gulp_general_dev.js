/* --------------------------------------------------------------
 gulp_general_dev.js 2021-05-04
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp General Dev Task
 *
 * This task will connect to the FTP server and execute all the other dev-tasks.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const ftp = require('../ftp');
	const vinylFtp = require('vinyl-ftp');
	const notifier = require('node-notifier');
	const environment = require('../environment');
	
	environment.createDevEnvironmentFile();
	
	function notify(config) {
		const title = config ? environment.getMessage('title', 'ftpMode') : environment.getMessage('title', 'localMode');
		const message = config ? environment.getMessage('message', 'ftpMode', config)
			: environment.getMessage('message', 'localMode');
		
		notifier.notify({
			title,
			message
		});
		
		$.util.log($.util.colors.yellow(title + ' ' + message));
	}
	
	function task(async) {
		ftp
			.init()
			.then(config => {
				if (config) {
					global.ftpConn = vinylFtp.create(config);
				}
				
				if (!global.devEnvironment) {
					notify(config);
				}
				
				global.devEnvironment = true;
				
				gulp.start(['jsengine:dev', 'admin:dev', 'theme:dev', 'gxmodules:dev']);
				async();
			})
			.catch(exception => {
				console.log('FTP init error:', exception);
				async();
			});
	}
	
	task.__description = 'Will execute all the dev tasks of each domain.';
	
	return task;
};