/* --------------------------------------------------------------
 gulp_gxmodules_styles.js 2023-07-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp GXModules Styles Task
 *
 * This task will handle the compilation of GXModules CSS files.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const fs = require('fs');
	const path = require('path');
	const ftp = require('../ftp');
	const notifier = require('node-notifier');
	
	function isDir(file) {
		return fs.lstatSync(file.path).isDirectory();
	}
	
	function isHtml(file) {
		return (path.extname(file.path) === '.html');
	}
	
	function task() {
		const vendorNames = fs.readdirSync('../GXModules')
			.filter(file => fs.statSync(path.join('../GXModules', file)).isDirectory());
		
		for (let vendorName of vendorNames) {
			const moduleNames = fs.readdirSync('../GXModules/' + vendorName)
				.filter(file => fs.statSync(path.join('../GXModules', vendorName, file)).isDirectory());
			
			for (let moduleName of moduleNames) {
				let glob = [
					`../GXModules/${vendorName}/${moduleName}/**/*.css`,
					`!../GXModules/${vendorName}/${moduleName}/**/*.min.css`,
					`!../GXModules/${vendorName}/${moduleName}/Build/**`,
					`!../GXModules/${vendorName}/${moduleName}/**/Themes/**`,
					`!../GXModules/${vendorName}/${moduleName}/**/Templates/**`
				];
				
				if (moduleName === 'Hub' || moduleName === 'KlarnaOSM') {
					glob = [
						`../GXModules/${vendorName}/${moduleName}/**/*.css`,
						`!../GXModules/${vendorName}/${moduleName}/**/*.min.css`,
						`!../GXModules/${vendorName}/${moduleName}/Build/**`,
						`!../GXModules/${vendorName}/${moduleName}/**/Templates/**`
					];
				} else if (moduleName === 'StyleEdit') {
					glob.push(`!../GXModules/${vendorName}/${moduleName}/App/**`);
				}
				
				gulp.src(glob)
					.pipe($.changed(`../GXModules/${vendorName}/${moduleName}/Build`))
					.pipe(gulp.dest(`../GXModules/${vendorName}/${moduleName}/Build`))
					.pipe(ftp.upload(`../GXModules/${vendorName}/${moduleName}/Build`))
					.pipe($.ignore.exclude(isDir))
					.pipe($.ignore.exclude(isHtml))
					.pipe($.cleanCss())
					.pipe($.rename({suffix: '.min'}))
					.pipe(gulp.dest(`../GXModules/${vendorName}/${moduleName}/Build`))
					.pipe(ftp.upload(`../GXModules/${vendorName}/${moduleName}/Build`));
			}
		}
	}
	
	task.__description = 'Will only build the GXModules style files.';
	
	return task;
};
