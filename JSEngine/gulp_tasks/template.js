/* --------------------------------------------------------------
 template.js 2016-09-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * Underscore JS template method.
 *
 * This function was extracted from UnderscoreJS in order to avoid its' inclusion as a package dependency.
 *
 * {@link https://github.com/jashkenas/underscore}
 *
 * Example:
 *   const template = require('./template');
 *   const message = template('My name is <%= name %>');
 *   console.log(message({name: 'John'})); // Logs "My name is John"
 *
 * @param {String} text Template string to be converted.
 *
 * @return {template} Returns a function which accepts the data of the string and returns the parsed string.
 */
module.exports = function(text) {
	// Define template settings. 
	const settings = {
		evaluate: /<%([\s\S]+?)%>/g,
		interpolate: /<%=([\s\S]+?)%>/g,
		escape: /<%-([\s\S]+?)%>/g
	};
	
	// Combine delimiters into one regular expression via alternation.
	const matcher = RegExp([
		(settings.escape || noMatch).source,
		(settings.interpolate || noMatch).source,
		(settings.evaluate || noMatch).source
	].join('|') + '|$', 'g');
	
	// Certain characters need to be escaped so that they can be put into a string literal.
	var escapes = {
		"'": "'",
		'\\': '\\',
		'\r': 'r',
		'\n': 'n',
		'\u2028': 'u2028',
		'\u2029': 'u2029'
	};
	
	// Define helper variables and methods.
	const escapeRegExp = /\\|'|\r|\n|\u2028|\u2029/g;
	const escapeChar = function(match) {
		return '\\' + escapes[match];
	};
	
	// Compile the template source, escaping string literals appropriately.
	let index = 0;
	let source = "__p+='";
	text.replace(matcher, function(match, escape, interpolate, evaluate, offset) {
		source += text.slice(index, offset).replace(escapeRegExp, escapeChar);
		index = offset + match.length;
		
		if (escape) {
			source += "'+\n((__t=(" + escape + "))==null?'':__t)+\n'";
		} else if (interpolate) {
			source += "'+\n((__t=(" + interpolate + "))==null?'':__t)+\n'";
		} else if (evaluate) {
			source += "';\n" + evaluate + "\n__p+='";
		}
		
		// Adobe VMs need the match returned to produce the correct offset.
		return match;
	});
	source += "';\n";
	
	// If a variable is not specified, place data values in local scope.
	if (!settings.variable) {
		source = 'with(obj||{}){\n' + source + '}\n';
	}
	
	source = "var __t,__p='',__j=Array.prototype.join," +
		"print=function(){__p+=__j.call(arguments,'');};\n" +
		source + 'return __p;\n';
	
	let render;
	try {
		render = new Function(settings.variable || 'obj', source);
	} catch (exception) {
		exception.source = source;
		throw exception;
	}
	
	const template = function(data) {
		return render.call(this, data);
	};
	
	// Provide the compiled source as a convenience for pre-compilation.
	var argument = settings.variable || 'obj';
	template.source = 'function(' + argument + '){\n' + source + '}';
	
	return template;
};
