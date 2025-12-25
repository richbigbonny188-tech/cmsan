/* --------------------------------------------------------------
 module_loader.js 2018-09-12
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.core.module_loader = jse.core.module_loader || {};

/**
 * JSE Module Loader
 *
 * This object is an adapter between the engine and RequireJS which is used to load the required files
 * into the client.
 *
 * @module JSE/Core/module_loader
 */
(function (exports) {

    'use strict';

    // ------------------------------------------------------------------------
    // PRIVATE METHODS
    // ------------------------------------------------------------------------

    /**
     * Load CSS file.
     *
     * @param {String} url Absolute URL of the CSS file to be loaded.
     *
     * @private
     */
    function _loadCss(url) {
        const link = document.createElement('link');
        link.type = 'text/css';
        link.rel = 'stylesheet';
        link.href = url;
        document.getElementsByTagName('head')[0].appendChild(link);
    }

    // ------------------------------------------------------------------------
    // PUBLIC METHODS
    // ------------------------------------------------------------------------

    /**
     * Initialize the module loader.
     *
     * Execute this method after the engine config is initialized. It will configure require.js
     * so that it will be able to find the project files.
     *
     * The cache busting method will try to create a number based on the current shop version.
     */
    exports.init = function () {
        let cacheBust = '';

        if (jse.core.config.get('environment') === 'production' &&
            jse.core.config.get('bustFiles') === false &&
            jse.core.config.get('cacheToken')) {
            cacheBust = `bust=${jse.core.config.get('cacheToken')}`;
        }

        const config = {
            baseUrl: jse.core.config.get('appUrl'),
            urlArgs: cacheBust,
            onError: function (error) {
                jse.core.debug.error('RequireJS Error:', error);
            }
        };

        window.require.config(config);
    };

    /**
     * Require JS and CSS files .
     *
     * Notice: There's no concrete way to determine when CSS dependencies are loaded.
     *
     * @param {String[]} dependencies Dependency URLs.
     * @param {Function} callback Callback method to be called once the dependencies are loaded.
     */
    exports.require = function (dependencies, callback) {
        for (let dependency of dependencies) {
            if (dependency.includes('.css')) {
                _loadCss(dependency);
                const index = dependencies.indexOf(dependency);
                dependencies.splice(index, 1);
            }
        }

        if (dependencies.length === 0) {
            callback();
        } else {
            window.require(dependencies, callback);
        }
    };

    /**
     * Load a module file with the use of requirejs.
     *
     * @param {Object} $element Selector of the element which has the module definition.
     * @param {String} name Module name to be loaded. Modules have the same names as their files.
     * @param {Object} collection Current collection instance.
     *
     * @return {Object} Returns a promise object to be resolved with the module instance as a parameter.
     */
    exports.load = function ($element, name, collection) {
        const deferred = $.Deferred();

        try {
            if (name === '') {
                deferred.reject(new Error('Module name cannot be empty.'));
            }

            const baseModuleName = name.replace(/.*\/(.*)$/, '$1'); // Name without the parent directories.

            // Try to load the cached instance of the module.
            const cached = collection.cache.modules[baseModuleName];
            if (cached && cached.code === 'function') {
                deferred.resolve(new jse.constructors.Module($element, baseModuleName, collection));
                return true; // continue loop
            }

            let bustSuffix = '';
            if (jse.core.config.get('environment') === 'production' &&
                jse.core.config.get('bustFiles') &&
                jse.core.config.get('cacheToken')
            ) {
                bustSuffix = '-bust_' + jse.core.config.get('cacheToken');
            }

            // Try to load the module file from the server.
            const fileExtension = jse.core.config.get('debug') !== 'DEBUG' ? '.min.js' : '.js';
            const url = collection.namespace.source + '/' + collection.name + '/' + name + bustSuffix + fileExtension;

            window.require([url], () => {
                if (collection.cache.modules[baseModuleName] === undefined) {
                    throw new Error('Module "' + name + '" wasn\'t defined correctly. Check the module code for '
                        + 'further troubleshooting.');
                }

                // Use the slice method for copying the array. 
                const dependencies = collection.cache.modules[baseModuleName].dependencies.slice();

                if (dependencies.length === 0) { // no dependencies
                    deferred.resolve(new jse.constructors.Module($element, baseModuleName, collection));
                    return true; // continue loop
                }

                // Load the dependencies first.
                for (let index in dependencies) {
                    const dependency = dependencies[index];

                    if (dependency.indexOf('.css') !== -1) {
                        _loadCss(dependency);
                        dependencies.splice(index, 1);
                        continue;
                    }

                    // Then convert the relative path to JSEngine/libs directory.
                    if (dependency.indexOf('http') === -1) {
                        dependencies[index] = jse.core.config.get('engineUrl') + '/libs/' + dependency + bustSuffix + fileExtension;
                    } else if (dependency.substr(-3) !== '.js') { // Then add the dynamic file extension to the URL.
                        dependencies[index] += bustSuffix + fileExtension;
                    }
                }

                window.require(dependencies, () => {
                    deferred.resolve(new jse.constructors.Module($element, baseModuleName, collection));
                });
            });
        } catch (exception) {
            deferred.reject(exception);
        }

        return deferred.promise();
    };

})(jse.core.module_loader);
