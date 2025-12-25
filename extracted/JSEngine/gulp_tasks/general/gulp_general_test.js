/* --------------------------------------------------------------
 gulp_general_test.js 2016-09-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp General Test Task
 *
 * This task will execute all the PHPUnit and QUnit tests in the console.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const task = $.shell.task([
		'php phpunit.phar --configuration ../tests/phpunit.xml',
		'mocha javascript --recursive'
	], {cwd: 'tests'});
	
	task.__description = 'Will execute the PHP and JavaScript unit tests of the project.';
	
	return task;
};
