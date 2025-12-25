/* --------------------------------------------------------------
 domains.js 2018-11-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Domain Handler
 *
 * This module enables the automatic task registration by checking the available files
 * inside certain directories. Include a "__description" property to the returned function
 * of each domain task and it will be displayed within the task listing.
 *
 * @param {Gulp} gulp Gulp Instance
 * @param {Object} $ Contains the automatically loaded gulp plugins.
 */
module.exports = function(gulp, $) {
	return {
		/**
		 * Registered Domain Definitions
		 *
		 * Stores info about: name, path, tasks, info.
		 *
		 * @type {Object[]}
		 */
		domains: [],
		
		/**
		 * Register a task domain with gulp.
		 *
		 * @param {String} domainName The domain name to be registered.
		 * @param {String} domainPath The directory path that contains the task files.
		 */
		register: function(domainName, domainPath) {
			// Load all the files available in the current directory.
			const fs = require('fs');
			const files = fs.readdirSync(domainPath);
			const domain = {
				name: domainName,
				path: domainPath,
				tasks: [],
				info: []
			};
			
			for (let index in files) {
				const regexp = new RegExp('gulp_' + domainName + '_(.*).js');
				const name = files[index].replace(regexp, '$1');
				const definition = require(domainPath + '/' + files[index].replace('.js', ''))(gulp, $);
				
				gulp.task(domainName + ':' + name, definition);
				
				domain.tasks.push(name);
				domain.info.push(name + (definition && definition.__description ? ': '
					+ definition.__description : ''));
			}
			
			// Register an extra gulp task for each domain task which will list all the available tasks.
			gulp.task(domainName, () => $.util.log(`\n\nDomain: "${domain.name}"\n------------------------------\n\n `
				+ ` ${domain.info.join('\n  ')}\n`));
			
			this.domains.push(domain);
		}
	}
};
