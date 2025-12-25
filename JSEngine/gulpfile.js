/* --------------------------------------------------------------
 gulpfile.js.js 2021-07-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Gulp Configuration File
 *
 * Since the project codebase is large we will need to support multiple configuration
 * schemes each of which will serve different domains of development. Every task defined
 * within a domain must have a specific suffix as shown in the examples bellow:
 *
 * > user@pc:~$ gulp general:doc
 *
 * > user@pc:~$ gulp admin:dev
 *
 * > user@pc:~$ gulp theme:build
 *
 * It is also important that each task domain uses more or less the same task names
 * for common operations such as JavaScript concatenation, SASS compilation etc.
 *
 * Recommended Task Names:
 *
 *   "scripts" - Manipulate JavaScript Files
 *   "styles" - Manipulate CSS/SCSS Files
 *   "legacy" - Manipulate Old Files
 *   "doc" - Generate Documentation
 *   "test" - Trigger Test Execution
 *   "clean" - File & Directory Removal
 *   "build" - Final State Preparation
 *   "dev" - Initiate File Watchers + FTP for Development
 *   "watch" - Watch directories and files.
 *   "vendor" - Import external dependencies into the project (mostly bower components).
 *   "ftp" - Upload assets files to the FTP server.
 *   "templates" - Manipulate Smarty templates (might also clear the cache/smarty contents).
 *   "coverage" - Produce code coverage documents for unit tests.
 *
 * This file will use the "domain_handler" module in order to automatically register
 * the available tasks of each domain. You just have to create a new task file inside
 * a domain's directory with the "gulp_{domain}_{task}.js" naming convention.
 */

'use strict';

// ----------------------------------------------------------------------------
// INITIALIZE GULP + MODULES
// ----------------------------------------------------------------------------

/**
 * Require Gulp
 *
 * @type {Gulp}
 */
const gulp = require('gulp');

/**
 * Load all gulp modules under the "$" variable. Custom modules that do not have the "gulp-" prefix
 * will need to be loaded manually, wherever required.
 *
 * @type {Function}
 */
const $ = require('gulp-load-plugins')();

/**
 * Banner with Gulp workflow information.
 *
 * @type {String}
 */
const banner = `
\n\n
Gulp workflow is brought to you by ...

  ____                 _     _          ____           _     _   _
 / ___| __ _ _ __ ___ | |__ (_) ___    / ___|_ __ ___ | |__ | | | |
| |  _ / _\` | '_ \` _ \\| '_ \\| |/ _ \\  | |  _| '_ \` _ \\| '_ \\| |_| |
| |_| | (_| | | | | | | |_) | | (_) | | |_| | | | | | | |_) |  _  |
 \\____|\\__,_|_| |_| |_|_.__/|_|\\___/   \\____|_| |_| |_|_.__/|_| |_|
 
                                                   Copyright © ${new Date().getFullYear()}


* This gulp configuration has a minimum requirement of NodeJS v8.
                                                   
* Execute 'gulp' to build all the dynamic resources of the project.

* Execute 'gulp help' for more information about the workflow and the available tasks.

* Execute 'gulp dev' build the dynamic resources and start the file watchers.

* Execute 'gulp ftp' after a 'gulp' task to upload all the dynamic assets to the server.

* Execute 'npm install', 'bower install' or 'composer install' if gulp crashes due to a missing
  module dependency.
  
* Folder structure changes might break the 'dev' tasks due to watcher caching. Run the dev tasks again.
  
* Remember to restart the 'dev' tasks every 3 ~ 4 hours.

* Babel.js source-maps are not always precise. Use the 'debugger' command or deactivate temporarily 
  the JavaScript source-maps from the dev-tools settings of your browser.
  
* Gulp watch tasks are running very slow within a Vagrant VM because of the file syncing latency.

* The template domain accepts a '--template' cli argument that enables the task execution for a 
  custom template directory (e.g. gulp template:dev --template CustomTemplateDirectory)
\n\n\n`;

$.util.log($.util.colors.dim(banner));

// ----------------------------------------------------------------------------
// GULP ERROR HANDLING
// ----------------------------------------------------------------------------

const gulpSrc = gulp.src;

gulp.src = function() {
	return gulpSrc.apply(gulp, arguments)
		.pipe($.plumber({
			errorHandler: $.notify.onError("Error: <%= error.message %>")
		}));
};

// ----------------------------------------------------------------------------
// REQUIRE TASK DOMAINS AND REGISTER TASKS
// ----------------------------------------------------------------------------

const domains = require('./gulp_tasks/domains')(gulp, $);
const basePath = __dirname + '/gulp_tasks/';
const domainNames = [
	'general',
	'admin',
    'theme',
	'themes',
	'jsengine',
	'gxmodules'
];

domainNames.forEach(function(name) {
	domains.register(name, basePath + name);
});

// ----------------------------------------------------------------------------
// DEFINE GENERAL TASKS
// ----------------------------------------------------------------------------

gulp.task('default', ['general:build']);

gulp.task('ftp', ['general:ftp']);

gulp.task('dev', ['general:dev']);

gulp.task('test', ['general:test']);

gulp.task('help', function() {
	let information = `
		\n\n
		Gulp Workflow
		------------------------------
		
		  The GX3 Gulp workflow supports various sections of development and the tasks are organized in task
		  domains each one of whom is responsible for a single development section. The task files are located
		  in the "tools/gulp_tasks" directory and they are separated into domain folders where each
		  filename follows the "gulp_{domain}_{task}.js" naming convention.
		  
		  Most of the times you will need to execute the "build" or "dev" tasks in order to re-generate the final
		  asset files or during development where file watchers are required. For example if you need to develop
		  Admin JS files, execute the "gulp admin:dev" task which will re-generate all the assets once more and
		  then watch for changes. Once a new change is detected it will copy, minify and optionally upload the
		  file to the server (ftp-config.json required).
		  
		  You can execute any domain task by running the "gulp domain:task" command. Below there is a list of
		  the available tasks and their descriptions: \n\n\n`.replace(/\t/g, '');
	
	domains.domains.forEach(function(domain) {
		information += `Domain: "${domain.name}"\n------------------------------\n`;
		
		domain.info.forEach(function(entry) {
			information += (`  ${entry}\n` );
		});
		
		information += ('\n\n');
	});
	
	$.util.log(information);
});