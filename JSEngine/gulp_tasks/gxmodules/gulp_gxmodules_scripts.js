/* --------------------------------------------------------------
 gulp_gxmodules_scripts.js 2023-07-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

const es2015 = require('babel-preset-es2015');

/**
 * Gulp GXModules Scripts Task
 *
 * This task will concatenate and minify the GXModules JS files. The final files will be
 * placed in the GXModules/{VendorName}/{ModuleName}/Build directory.
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
	
	function isDir(file) {
		return fs.lstatSync(file.path).isDirectory();
	}
	
	function isHtml(file) {
		return (path.extname(file.path) === '.html');
	}
	
	function compile(src, dest) {
		let glob = [
			`${src}/**/*.js`,
			`!${src}/**/*.min.js`,
			`!${src}/Build/**`,
			`!${src}/App/**`,
			`!${src}/**/Themes/**`,
			`!${src}/**/Templates/**`
		];
		
		if (src.includes('/Gambio/Hub') || src.includes('/Gambio/KlarnaOSM')) {
			glob = [
				`${src}/**/*.js`,
				`!${src}/**/*.min.js`,
				`!${src}/Build/**`,
				`!${src}/App/**`
			];
		}
		
		gulp.src(glob)
			.pipe($.changed(dest))
			.pipe($.sourcemaps.init())
			.pipe($.babel({presets: [es2015]}))
			.pipe($.sourcemaps.write())
			.pipe(gulp.dest(dest))
			.pipe(ftp.upload(dest))
			.pipe($.ignore.exclude(isDir))
			.pipe($.ignore.exclude(isHtml))
			.pipe($.terser().on('error', $.util.log))
			.pipe($.rename({suffix: '.min'}))
			.pipe(gulp.dest(dest))
			.pipe(ftp.upload(dest));
	}
	
	function task() {
		const vendorNames = fs.readdirSync('../GXModules')
			.filter(file => fs.statSync(path.join('../GXModules', file)).isDirectory());
		
		for (let vendorName of vendorNames) {
			const moduleNames = fs.readdirSync('../GXModules/' + vendorName)
				.filter(file => fs.statSync(path.join('../GXModules/', vendorName, file)).isDirectory());
			
			for (let moduleName of moduleNames) {
				compile(path.join('..', 'GXModules', vendorName, moduleName), path.join('..', 'GXModules', vendorName, moduleName, 'Build'));
			}
		}
	}
	
	task.__description = 'Will only build the GXModules javascript files.';
	
	return task;
};
