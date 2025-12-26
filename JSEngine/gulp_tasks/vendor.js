/* --------------------------------------------------------------
 vendor.js 2017-12-29
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Get vendor files from the node_modules directory.
 *
 * {@link https://www.npmjs.com/package/npm-main-files}
 *
 * @param {String} ext File extension to fetch.
 * @param {String[]} packages Array with the package names to be fetched.
 * @param {Object} [overrides] Optional stub, add npm "override" values.
 */
module.exports = function(ext, packages, overrides) {
	/* TODO: Sadly, npm-main-files has a problem with main files that precede a "./".
		In order to bypass it, we're currently using overrides. A better module(I couldnt find one)
		or another solution would be amazing!
	 */
	const npm = require('npm-main-files')('**/*.' + ext); // glob pattern finding all npm module files matching the given extension.
                                                          // used instead of bower.ext(ext)
	
	// this is the equivalent to bower.match(match):
	let finalPackages = npm.filter(file => packages.some(	// npm-files are already matching the required extension and consist of an array of file paths.
		pkg => (                                // if one package matches to be contained in the specified directory (package),
			file.split('/')[2] === pkg && overrides[pkg] === undefined  	 	// it is to be included in the vendor build unless it exists in overrides
		)
	));
	
	// Let us check if the override exists!
	packages.forEach((pkg) => {
		// If the override isnt undefined, it exists!
		if (overrides[pkg] !== undefined) {
			// Are there multiple files in the override?
			if (Array.isArray(overrides[pkg].main)) {
				// If so, let us handle each one!
				overrides[pkg].main.forEach((file) => {
					// Let us make sure the extension matches with the one we're searching for in the overrides!
					let extension = file.substr(file.length - ext.length);
					if (extension === ext) {
						// If the extension matches, push it into the packages to include!
						finalPackages.push('./node_modules/' + pkg + '/' + file);
					}
				});
			} else { // Looks like it's a single file!
				// Let us check the extension to ensure it matches what we're looking for
				let extension = overrides[pkg].main.substr(overrides[pkg].main.length - ext.length);
				if (extension === ext) {
					// and if it does, let us include this file into the packages!
					finalPackages.push('./node_modules/' + pkg + '/' + overrides[pkg].main);
				}
			}
		}
	});
	
	return finalPackages;
};



