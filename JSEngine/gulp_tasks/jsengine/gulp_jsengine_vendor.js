/* --------------------------------------------------------------
 gulp_general_vendor.js 2018-11-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp JS Engine Vendor Task
 *
 * This task handles JavaScript vendor dependencies. There are two kinds of dependencies: the explicit and the
 * optional. Explicit dependencies are loaded in every page and hence it's better to be placed in a single file
 * whereas optional dependencies are only needed in certain cases and they can be lazy-loaded.
 *
 * Important #1: Execute this task whenever there is a change in the bower components.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const vendor = require('../vendor');
	const ftp = require('../ftp');
	
	function task() {
		// Create JSEngine vendor bundle for explicit dependencies. 
		const coreDepdencies = [
			'node_modules/jquery/dist/jquery.js',
			'node_modules/vue/dist/vue.js',
			'node_modules/babel-polyfill/dist/polyfill.js',
			'node_modules/@iconfu/svg-inject/dist/svg-inject.js'
		];
		
		gulp.src(coreDepdencies)
			.pipe($.sourcemaps.init())
			.pipe($.concat('vendor.js'))
			.pipe($.sourcemaps.write())
			.pipe(gulp.dest('../JSEngine/build'))
			.pipe(ftp.upload('../JSEngine/build'));
		
		const minifiedCoreDependencies = [
			'node_modules/jquery/dist/jquery.js',
			'node_modules/vue/dist/vue.min.js', // Need to use Vue in production mode.
			// Important: do not use the pre-compiled version as it makes problems in production.
			'node_modules/babel-polyfill/dist/polyfill.js',
			'node_modules/@iconfu/svg-inject/dist/svg-inject.min.js'
		];
		
		gulp.src(minifiedCoreDependencies)
			.pipe($.concat('vendor.min.js'))
			.pipe($.terser())
			.pipe(gulp.dest('../JSEngine/build'))
			.pipe(ftp.upload('../JSEngine/build'));
		
		// Create vendor directories for optional dependencies.
		const packages = [
			'blueimp-canvas-to-blob',
			'blueimp-file-upload',
			'blueimp-load-image',
			'blueimp-tmpl',
			'bootstrap-sass',
			'codemirror',
			'datatables',
			'Datejs',
			'honeygrid-modernizr',
			'html5shiv',
			'jquery-canvas-area-draw',
			'jquery-colpick',
			'jquery-datetimepicker',
			'jquery-deparam',
			'jquery-migrate',
			'jQuery-rwdImageMaps',
			'jquery-ui-dist',
			'jquery-ui-daterangepicker',
			'jquery-ui-touch-punch',
			'magnific-popup',
			'moment',
			'morris.js',
			'mustache',
			'pace-js',
			'qtip2',
			'raphael',
			'respond.js',
			'shariff',
			'srcdoc-polyfill',
			'sumoselect',
			'swiper',
		];
		
		const overrides = {
			'morris.js': {
				'main': [
					'morris.js',
					'morris.css'
				]
			},
			'bootstrap': {
				'main': [
					'dist/js/bootstrap.js'
				]
			},
			'codemirror': {
				'main': [
					'lib/codemirror.js',
					'lib/codemirror.css',
					'mode/htmlmixed/htmlmixed.js',
					'mode/xml/xml.js',
					'mode/javascript/javascript.js',
					'mode/css/css.js'
				]
			},
			'jquery-datetimepicker': {
				'main': [
					'build/jquery.datetimepicker.full.js',
					'jquery.datetimepicker.css'
				]
			},
			'jquery-ui-dist': {
				'main': [
					'jquery-ui.js',
					'jquery-ui.css',
					'jquery-ui.min.css'
				]
			},
			'jquery-ui-daterangepicker': {
				'main': [
					'jquery.comiseo.daterangepicker.js',
					'jquery.comiseo.daterangepicker.css'
				]
			},
			'jQuery-rwdImageMaps': {
				'main': 'jquery.rwdImageMaps.js'
			},
			'qtip2': {
				'main': [
					'dist/jquery.qtip.js',
					'dist/jquery.qtip.css'
				]
			},
			'swiper': {
				'main': 'dist/js/swiper.js'
			},
			'sumoselect': {
				'main': [
					'jquery.sumoselect.js',
					'sumoselect.css'
				]
			},
			'moment': {
				'main': 'min/moment.min.js'
			},
			'blueimp-canvas-to-blob': {
				'main': 'js/canvas-to-blob.min.js'
			},
			'datejs': {
				'main': 'index.js'
			},
			'respond.js': {
				'main': 'dest/respond.min.js'
			},
			'mustache': {
				'main': 'mustache.min.js'
			},
			'shariff': {
				'main': [
					'dist/shariff.complete.js',
					'dist/shariff.min.css'
				]
			}
		};
		
		const exportOverrides = {
			'Datejs': {
				'folder': 'DateJS',
				'file': 'date'
			},
			'moment': {
				'folder': 'momentjs',
				'file': 'moment'
			},
			'mustache': {
				'folder': 'mustache.js',
				'file': 'mustache'
			},
			'jquery-datetimepicker': {
				'folder': 'datetimepicker',
				'file': 'jquery.datetimepicker.full.js'
			},
			'shariff': {
				'folder': 'shariff',
				'file': 'shariff'
			}
		};
		for (let packageName of packages) {
			const scripts = vendor('js', [packageName], overrides);
			
			// TODO: Saving old package name for dirty hack below
			let originalPackageName = packageName;
			
			// TODO: Dirty fix to adjust package names to reflect frontend names
			if (exportOverrides[packageName] !== undefined) {
				packageName = exportOverrides[packageName].folder;
			}
			
			gulp.src(scripts)
				.pipe($.rename(function(path) {
					path.basename = path.basename.replace('.min', '');
					// TODO: Dirty fix to adjust file names to reflect frontend names
					if (exportOverrides[packageName] !== undefined) {
						let fixedName = path.basename.split('/');
						let extension = fixedName[fixedName.length - 1].split('.')[1];
						fixedName[fixedName.length - 1] = exportOverrides[packageName].file;
						path.basename = fixedName.join('/');
						
					}
					return path;
				}))
				.pipe(gulp.dest('../JSEngine/build/vendor/' + packageName))
				.pipe(ftp.upload('../JSEngine/build/vendor/' + packageName))
				.pipe($.terser())
				.pipe($.rename({suffix: '.min'}))
				.pipe(gulp.dest('../JSEngine/build/vendor/' + packageName))
				.pipe(ftp.upload('../JSEngine/build/vendor/' + packageName));
			
			// TODO: Saving old package name for dirty hack below
			packageName = originalPackageName;
			
			const styles = vendor('css', [packageName], overrides);
			
			// TODO: Dirty fix to adjust package names to reflect frontend names
			if (exportOverrides[packageName] !== undefined) {
				packageName = exportOverrides[packageName].folder;
			}
			
			gulp.src(styles)
				.pipe($.rename(function(path) {
					path.basename = path.basename.replace('.min', '');
					// TODO: Dirty fix to adjust file names to reflect frontend names
					if (exportOverrides[packageName] !== undefined) {
						let fixedName = path.basename.split('/');
						let extension = fixedName[fixedName.length - 1].split('.')[1];
						fixedName[fixedName.length - 1] = exportOverrides[packageName].file;
						path.basename = fixedName.join('/');
						
					}
					return path;
				}))
				.pipe(gulp.dest('../JSEngine/build/vendor/' + packageName))
				.pipe(ftp.upload('../JSEngine/build/vendor/' + packageName))
				.pipe($.cleanCss())
				.pipe($.rename({suffix: '.min'}))
				.pipe(gulp.dest('../JSEngine/build/vendor/' + packageName))
				.pipe(ftp.upload('../JSEngine/build/vendor/' + packageName));
		}
		
		// Copy required vendor images.
		gulp.src('node_modules/datatables/media/images/**')
			.pipe(gulp.dest('../JSEngine/build/vendor/datatables/images'))
			.pipe(ftp.upload('../JSEngine/build/vendor/datatables/images'));
		
		gulp.src('node_modules/jquery-ui-dist/images/**')
			.pipe(gulp.dest('../JSEngine/build/vendor/jquery-ui-dist/images'))
			.pipe(ftp.upload('../JSEngine/build/vendor/jquery-ui-dist/images'));
		
		// Copy needed files for automatic hyphenation
		gulp.src([
			'node_modules/hyphenopoly/min/**',
			'!**/testsuite/**'
		])
			.pipe(gulp.dest('../JSEngine/build/vendor/hyphenopoly'))
			.pipe(ftp.upload('../JSEngine/build/vendor/hyphenopoly'));

		gulp.src('node_modules/jquery/dist/**')
			.pipe(gulp.dest('../JSEngine/build/vendor/jquery'))
			.pipe(ftp.upload('../JSEngine/build/vendor/jquery'));
	}
	
	task.__description = 'Will copy, transpile and minify all the required JS & CSS vendor files.';
	
	return task;
};
