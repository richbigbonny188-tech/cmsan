/* --------------------------------------------------------------
 environment.js 2019-01-16
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2019 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

const messages = require('./messages.json');
const template = require('./template');
const fs = require('fs');

/**
 * Noop function
 */
const noop = () => {};

/**
 * Environment
 *
 * Contains re-usable environment methods.
 *
 * @type {Object}
 */
module.exports = {
	/**
	 * Exports a function that returns the values of command line arguments.
	 *
	 * Example:
	 *   // $ node index.js --custom-arg custom-value
	 *   const environment = require('environment');
	 *   const value = environment.getArgument('custom-arg'); // Returns 'custom-value'.
	 *
	 * @param {String} name The argument name without the initial '--' characters.
	 *
	 * @return {String} Returns the value of the requested parameter.
	 */
	getArgument(name) {
		const index = process.argv.indexOf('--' + name);
		return index > -1 ? process.argv[index + 1] : undefined;
	},
	
	/**
	 * Get a specific message from the messages.json.
	 *
	 * The messages in the messages.json file can be reused from various tasks without having to write them
	 * every single time. Use the correct key and section to fetch the requested message.
	 *
	 * @param {String} key Provide the message key (third messages.json property e.g. "title").
	 * @param {String} section Provide the message section (second messages.json property e.g. "ftpMode").
	 * @param {Object} [data] If provided then the message will be parsed as a template string with
	 * template.js and the data will be used for the placeholder replacements.
	 *
	 * @return {String} Returns the message string from messages.json file.
	 */
	getMessage(key, section, data) {
		let message = messages[section] && messages[section][key] ? messages[section][key] : '';
		
		if (message === '') {
			console.log('Requested message was not found in messages.json:', section, key);
		}
		
		if (data) {
			message = template(message)(data);
		}
		
		return message;
	},
	
	/**
	 * Create .dev-environment file.
	 *
	 * This task must be executed in the beginning of every development task. The .dev-environment file will enable
	 * debugging and logging output in the shop and it must be present in any development environment.
	 */
	createDevEnvironmentFile() {
		const path = '../.dev-environment';
		
		try {
			fs.lstatSync(path);
		} catch (e) {
			fs.writeFileSync(path, '', 'utf8', noop); // The file does not exist so create it.
		}
	}
};
