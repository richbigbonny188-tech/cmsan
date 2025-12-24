/* --------------------------------------------------------------
 gulp_theme_dev.js 2018-11-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp Theme Dev Task
 *
 * This task will initialize the required sub-tasks for the theme development. It will also
 * execute the jsengine:watch task in order to perform the requested operations when a core
 * JSEngine file is changed.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const vinylFtp = require('vinyl-ftp');
	const gulpsync = require('gulp-sync')(gulp);
	const notifier = require('node-notifier');
	const ftp = require('../ftp');
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
				
				gulp.start(gulpsync.sync([
					'theme:clean',
					'theme:vendor',
					'theme:scripts',
					'jsengine:watch',
					'theme:watch'
				], 'theme:dev'));
				
				async();
			})
			.catch(exception => {
				console.log('FTP init error:', exception);
				async();
			});
	}
	
	task.__description = 'Will re-generate the assets and activate the file watchers for development.';
	
	return task;
};
