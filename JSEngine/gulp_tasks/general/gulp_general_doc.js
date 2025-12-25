/* --------------------------------------------------------------
 gulp_general_doc.js 2016-09-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Gulp General Doc Task
 *
 * This task will general the "developers.gambio.de" documentation output by executing
 * the doc/developers-doc.sh script.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 *
 * @return {Function} Returns the gulp task definition.
 */
module.exports = function(gulp, $) {
	const task = $.shell.task(['bash developers-doc.sh'], {cwd: 'docs'});
	
	task.__description = 'Will generate the project documentation by executing the developers-doc.sh (unix only).';
	
	return task;
};
