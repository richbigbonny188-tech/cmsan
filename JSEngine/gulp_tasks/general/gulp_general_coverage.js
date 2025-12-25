/* --------------------------------------------------------------
 gulp_general_coverage.js 2016-09-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp General PHPUnit Coverage Task (Unix Systems Only)
 *
 * This task will generate the "system" test folder coverage information by executing the run_tests.sh script with the
 * correct parameters. In addition to that, a testdox.txt file will be generated in the same directory.
 *
 * Output Directory: developers.gambio.de/tests
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const task = $.shell.task([
		'php phpunit.phar --configuration ../tests/phpunit.xml --testsuite coverage '
		+ '--coverage-html ../developers.gambio.de/tests/coverage '
		+ '--testdox-text ../developers.gambio.de/tests/testdox.txt'
	], {cwd: 'tests'});
	
	task.__description = 'Will execute the PHPUnit tests and produce code coverage and testdox documentation '
		+ 'files in the "developers.gambio.de/tests" directory.';
	
	return task;
};
