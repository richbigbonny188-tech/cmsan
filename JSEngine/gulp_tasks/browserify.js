/* --------------------------------------------------------------
 browserify.js 2016-09-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

const es2015 = require('babel-preset-es2015');

/**
 * Browserify Handler
 *
 * This file will setup up browserify build with watch functionality so that we get fast
 * bundles for js code. It will also use babelify to transpile ES2015 code into ES5.
 *
 * Example - Single Bundle:
 *   const bundle = require('./browserify')(gulp, $, false, 'path/to/entry.js', 'path/to/output.js');
 *   bundle(); // Run the method whenever required.
 *
 * Example - Enable Watchify:
 *   require('./browserify')(gulp, $, false, 'path/to/entry.js', 'path/to/output.js'); // will watch for changes
 *
 * {@link https://github.com/gulpjs/gulp/blob/master/docs/recipes/fast-browserify-builds-with-watchify.md}
 *
 * @param {Gulp} gulp Gulp instance
 * @param {Object} $ Loaded Gulp plugins
 * @param {Boolean} watch Determines whether we are using this module inside a watch task.
 *
 * @return {Object} Returns an object with the "run" and "watch" methods.
 */
module.exports = function(gulp, $, watch, entry, output) {
	const watchify = require('watchify');
	const browserify = require('browserify');
	const babelify = require('babelify');
	const source = require('vinyl-source-stream');
	const buffer = require('vinyl-buffer');
	const fs = require('fs');
	const path = require('path');
	const ftp = require('./ftp');
	
	function isDir(file) {
		return fs.lstatSync(file.path).isDirectory();
	}
	
	function isHtml(file) {
		return (path.extname(file.path) === '.html');
	}
	
	function bundle() {
		const outputDirectory = path.dirname(output);
		const outputFilename = path.basename(output);
		
		return instance
			.transform('babelify', {presets: [es2015]})
			.bundle()
			.on('error', $.util.log.bind($.util, 'Browserify Error'))
			.pipe(source(outputFilename))
			.pipe(buffer())
			.pipe($.sourcemaps.init({loadMaps: true}))
			.pipe($.sourcemaps.write())
			.pipe(gulp.dest(outputDirectory))
			.pipe(ftp.upload(outputDirectory))
			.pipe($.ignore.exclude(isDir))
			.pipe($.ignore.exclude(isHtml))
			.pipe($.terser().on('error', $.util.log))
			.pipe($.rename({suffix: '.min'}))
			.pipe(gulp.dest(outputDirectory))
			.pipe(ftp.upload(outputDirectory));
	}
	
	// Setup Babelify & Browserify
	const defaults = {
		entries: [entry],
		debug: true
	};
	
	let instance;
	
	if (watch) {
		const options = Object.assign({}, watchify.args, defaults);
		instance = watchify(browserify(options));
		instance.on('update', () => bundle(output));
	} else {
		instance = browserify(defaults)
	}
	
	instance.on('log', $.util.log);
	
	return bundle;
};