(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/* --------------------------------------------------------------
 collection.js 2016-06-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

(function () {

    'use strict';

    /**
     * Class Collection
     *
     * This class is used to handle multiple modules of the same type (controllers, extensions ...).
     *
     * @class JSE/Constructors/Collection
     */

    var Collection = function () {
        /**
         * Class Constructor
         *
         * @param {String} name The collection name - must be unique.
         * @param {String} attribute The attribute that will trigger collection's modules.
         * @param {Object} namespace Optional, the namespace instance where the collection belongs.
         */
        function Collection(name, attribute, namespace) {
            _classCallCheck(this, Collection);

            this.name = name;
            this.attribute = attribute;
            this.namespace = namespace;
            this.cache = {
                modules: {},
                data: {}
            };
        }

        /**
         * Define a new engine module.
         *
         * This function will define a new module into the engine. Each module will be stored in the
         * collection's cache to prevent unnecessary file transfers. The same happens with the default
         * configuration that append to the module definition.
         *
         * @param {String} name Name of the module (same as the filename).
         * @param {Array} dependencies Array of libraries that this module depends on (will be loaded asynchronously).
         * Apply only filenames without extension e.g. ["emails"].
         * @param {Object} code Contains the module code (function).
         */


        _createClass(Collection, [{
            key: 'module',
            value: function module(name, dependencies, code) {
                // Check if required values are available and of correct type.
                if (!name || typeof name !== 'string' || typeof code !== 'function') {
                    jse.core.debug.warn('Registration of the module failed, due to bad function call', arguments);
                    return false;
                }

                // Check if the module is already defined.
                if (this.cache.modules[name]) {
                    jse.core.debug.warn('Registration of module "' + name + '" skipped, because it already exists.');
                    return false;
                }

                // Store the module to cache so that it can be used later.
                this.cache.modules[name] = {
                    code: code,
                    dependencies: dependencies
                };
            }

            /**
             * Initialize Module Collection
             *
             * This method will trigger the page modules initialization. It will search all
             * the DOM for the "data-gx-extension", "data-gx-controller" or
             * "data-gx-widget" attributes and load the relevant scripts through RequireJS.
             *
             * @param {jQuery} $parent Optional (null), parent element will be used to search for the required modules.
             *
             * @return {jQuery.Deferred} namespaceDeferred Deferred object that gets processed after the
             * module initialization is finished.
             */

        }, {
            key: 'init',
            value: function init() {
                var _this = this;

                var $parent = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

                // Store the namespaces reference of the collection.
                if (!this.namespace) {
                    throw new Error('Collection cannot be initialized without its parent namespace instance.');
                }

                // Set the default parent-object if none was given.
                if ($parent === undefined || $parent === null) {
                    $parent = $('html');
                }

                var attribute = 'data-' + this.namespace.name + '-' + this.attribute;
                var namespaceDeferred = $.Deferred();
                var deferredCollection = [];

                $parent.filter('[' + attribute + ']').add($parent.find('[' + attribute + ']')).each(function (index, element) {
                    var $element = $(element);
                    var modules = $element.attr(attribute);

                    $element.removeAttr(attribute);

                    $.each(modules.replace(/(\r\n|\n|\r|\s\s+)/gm, ' ').trim().split(' '), function (index, name) {
                        if (name === '') {
                            return true;
                        }

                        var deferred = $.Deferred();
                        deferredCollection.push(deferred);

                        jse.core.module_loader.load($element, name, _this).done(function (module) {
                            return module.init(deferred);
                        }).fail(function (error) {
                            deferred.reject();
                            // Log the error in the console but do not stop the engine execution.
                            jse.core.debug.error('Could not load module: ' + name, error);
                        });
                    });
                });

                // Always resolve the namespace, even if there are module errors.
                $.when.apply(undefined, deferredCollection).always(function () {
                    return namespaceDeferred.resolve();
                });

                return deferredCollection.length ? namespaceDeferred.promise() : namespaceDeferred.resolve();
            }
        }]);

        return Collection;
    }();

    jse.constructors.Collection = Collection;
})();

},{}],2:[function(require,module,exports){
'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/* --------------------------------------------------------------
 data_binding.js 2016-05-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

(function () {

    'use strict';

    /**
     * Data Binding Class
     *
     * Handles two-way data binding with UI elements.
     *
     * @class JSE/Constructors/DataBinding
     */

    var DataBinding = function () {
        /**
         * Class Constructor
         *
         * @param {String} name The name of the binding.
         * @param {Object} $element Target element to be bond.
         */
        function DataBinding(name, $element) {
            _classCallCheck(this, DataBinding);

            this.name = name;
            this.$element = $element;
            this.value = null;
            this.isMutable = $element.is('input, textarea, select');
            this.init();
        }

        /**
         * Initialize the binding.
         */


        _createClass(DataBinding, [{
            key: 'init',
            value: function init() {
                var _this = this;

                this.$element.on('change', function () {
                    _this.get();
                });
            }

            /**
             * Get binding value.
             *
             * @returns {*}
             */

        }, {
            key: 'get',
            value: function get() {
                this.value = this.isMutable ? this.$element.val() : this.$element.html();

                if (this.$element.is(':checkbox') || this.$element.is(':radio')) {
                    this.value = this.$element.prop('checked');
                }

                return this.value;
            }

            /**
             * Set binding value.
             *
             * @param {String} value
             */

        }, {
            key: 'set',
            value: function set(value) {
                this.value = value;

                if (this.isMutable) {
                    this.$element.val(value);
                } else {
                    this.$element.html(value);
                }
            }
        }]);

        return DataBinding;
    }();

    jse.constructors.DataBinding = DataBinding;
})();

},{}],3:[function(require,module,exports){
'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/* --------------------------------------------------------------
 module.js 2016-05-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 ----------------------------------------------------------------
 */

(function () {

    'use strict';

    /**
     * Class Module
     *
     * This class is used for representing a module instance within the JSE ecosystem.
     *
     * @class JSE/Constructors/Module
     */

    var Module = function () {
        /**
         * Class Constructor
         *
         * @param {Object} $element Module element selector object.
         * @param {String} name The module name (might contain the path)
         * @param {Object} collection The collection instance of the module.
         */
        function Module($element, name, collection) {
            _classCallCheck(this, Module);

            this.$element = $element;
            this.name = name;
            this.collection = collection;
        }

        /**
         * Initialize the module execution.
         *
         * This function will execute the "init" method of each module.
         *
         * @param {Object} collectionDeferred Deferred object that gets processed after the module
         * initialization is finished.
         */


        _createClass(Module, [{
            key: 'init',
            value: function init(collectionDeferred) {
                var _this = this;

                // Store module instance alias.
                var cached = this.collection.cache.modules[this.name];
                var timeout = null;

                try {
                    if (!cached) {
                        throw new Error('Module "' + this.name + '" could not be found in the collection cache.');
                    }

                    var data = this._getModuleData();
                    var instance = cached.code.call(this.$element, data);

                    // Provide a done function that needs to be called from the module, in order to inform 
                    // that the module "init" function was completed successfully.
                    var done = function done() {
                        _this.$element.trigger('jse:module:initialized', [{ module: _this.name }]);
                        jse.core.debug.info('Module "' + _this.name + '" initialized successfully.');
                        collectionDeferred.resolve();
                        clearTimeout(timeout);
                    };

                    // Load the module data before the module is loaded.
                    this._loadModuleData(instance).done(function () {
                        // Reject the collectionDeferred if the module isn't initialized after 10 seconds.
                        timeout = setTimeout(function () {
                            jse.core.debug.warn('Module was not initialized after 10 seconds! -- ' + _this.name);
                            collectionDeferred.reject();
                        }, 10000);

                        jse.core.vue.registerModule(instance);
                        instance.init(done);
                    }).fail(function (error) {
                        collectionDeferred.reject();
                        jse.core.debug.error('Could not load module\'s meta data.', error);
                    });
                } catch (exception) {
                    collectionDeferred.reject();
                    jse.core.debug.error('Cannot initialize module "' + this.name + '".', exception);
                    $(window).trigger('error', [exception]); // Inform the engine about the exception.
                }

                return collectionDeferred.promise();
            }

            /**
             * Parse the module data attributes.
             *
             * @return {Object} Returns an object that contains the data of the module.
             *
             * @private
             */

        }, {
            key: '_getModuleData',
            value: function _getModuleData() {
                var _this2 = this;

                var data = {};

                $.each(this.$element.data(), function (name, value) {
                    if (name.indexOf(_this2.name) === 0 || name.indexOf(_this2.name.toLowerCase()) === 0) {
                        var key = name.substr(_this2.name.length);
                        key = key.substr(0, 1).toLowerCase() + key.substr(1);
                        data[key] = value;
                        // Remove data attribute from element (sanitise camel case first).
                        var sanitisedKey = key.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
                        _this2.$element.removeAttr('data-' + _this2.name + '-' + sanitisedKey);
                    }
                });

                return data;
            }

            /**
             * Modules return objects which might contain requirements.
             *
             * @param {Object} instance Module instance object.
             *
             * @return {Object} Returns a promise object that will be resolved when the data are fetched.
             *
             * @private
             */

        }, {
            key: '_loadModuleData',
            value: function _loadModuleData(instance) {
                var deferred = $.Deferred();
                var deferredCollection = [];

                try {
                    if (instance.model) {
                        $.each(instance.model, function (index, url) {
                            var modelDeferred = $.Deferred();
                            deferredCollection.push(modelDeferred);
                            $.getJSON(url).done(function (response) {
                                instance.model[index] = response;
                                modelDeferred.resolve(response);
                            }).fail(function (error) {
                                modelDeferred.reject(error);
                            });
                        });
                    }

                    if (instance.view) {
                        $.each(instance.view, function (index, url) {
                            var viewDeferred = $.Deferred();
                            deferredCollection.push(viewDeferred);
                            $.get(url).done(function (response) {
                                instance.view[index] = response;
                                viewDeferred.resolve(response);
                            }).fail(function (error) {
                                viewDeferred.reject(error);
                            });
                        });
                    }

                    if (instance.bindings) {
                        for (var name in instance.bindings) {
                            var $element = instance.bindings[name];
                            instance.bindings[name] = new jse.constructors.DataBinding(name, $element);
                        }
                    }

                    $.when.apply(undefined, deferredCollection).done(deferred.resolve).fail(function (error) {
                        deferred.reject(new Error('Cannot load data for module "' + instance.name + '".', error));
                    });
                } catch (exception) {
                    deferred.reject(exception);
                    jse.core.debug.error('Cannot preload module data for "' + this.name + '".', exception);
                    $(window).trigger('error', [exception]); // Inform the engine about the exception.
                }

                return deferred.promise();
            }
        }]);

        return Module;
    }();

    jse.constructors.Module = Module;
})();

},{}],4:[function(require,module,exports){
'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/* --------------------------------------------------------------
 namespace.js 2016-05-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

(function () {

    'use strict';

    /**
     * Class Namespace
     *
     * This class is used to handle multiple collections of modules. Every namespace has its own source URL
     * for loading the data. That means that JSE can load modules from multiple places at the same time.
     *
     * @class JSE/Constructors/Namespace
     */

    var Namespace = function () {
        /**
         * Class Constructor
         *
         * @param {String} name The namespace name must be unique within the app.
         * @param {String} source Complete URL to the namespace modules directory (without trailing slash).
         * @param {Array} collections Contains collection instances to be included in the namespace.
         */
        function Namespace(name, source, collections) {
            _classCallCheck(this, Namespace);

            this.name = name;
            this.source = source;
            this.collections = collections; // contains the default instances   		
        }

        /**
         * Initialize the namespace collections.
         *
         * This method will create new collection instances based in the original ones.
         *
         * @return {jQuery.Promise} Returns a promise that will be resolved once every namespace collection
         * is resolved.
         */


        _createClass(Namespace, [{
            key: 'init',
            value: function init() {
                var deferredCollection = [];

                var _iteratorNormalCompletion = true;
                var _didIteratorError = false;
                var _iteratorError = undefined;

                try {
                    for (var _iterator = this.collections[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                        var collection = _step.value;

                        this[collection.name] = new jse.constructors.Collection(collection.name, collection.attribute, this);
                        var deferred = this[collection.name].init();
                        deferredCollection.push(deferred);
                    }
                } catch (err) {
                    _didIteratorError = true;
                    _iteratorError = err;
                } finally {
                    try {
                        if (!_iteratorNormalCompletion && _iterator.return) {
                            _iterator.return();
                        }
                    } finally {
                        if (_didIteratorError) {
                            throw _iteratorError;
                        }
                    }
                }

                return deferredCollection.length ? $.when.apply(undefined, deferredCollection).promise() : $.Deferred().resolve();
            }
        }]);

        return Namespace;
    }();

    jse.constructors.Namespace = Namespace;
})();

},{}],5:[function(require,module,exports){
'use strict';

/* --------------------------------------------------------------
 about.js 2016-09-08
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * JSE Information Module
 *
 * Execute the `jse.about()` command and you will get a new log entry in the
 * console with info about the engine. The "about" method is only available in
 * the "development" environment of the engine.
 *
 * @module JSE/Core/about
 */
document.addEventListener('DOMContentLoaded', function () {

    'use strict';

    if (jse.core.config.get('environment') === 'production') {
        return;
    }

    jse.about = function () {
        var info = '\n\t\t\tJS ENGINE v' + jse.core.config.get('version') + ' \xA9 GAMBIO GMBH\n\t\t\t----------------------------------------------------------------\n\t\t\tThe JS Engine enables developers to load automatically small pieces of javascript code by\n\t\t\tplacing specific data attributes to the HTML markup of a page. It was built with modularity\n\t\t\tin mind so that modules can be reused into multiple places without extra effort. The engine\n\t\t\tcontains namespaces which contain collections of modules, each one of whom serve a different\n\t\t\tgeneric purpose.\n\t\t\tVisit http://developers.gambio.de for complete reference of the JS Engine.\n\t\t\t\n\t\t\tFALLBACK INFORMATION\n\t\t\t----------------------------------------------------------------\n\t\t\tSince the engine code becomes bigger there are sections that need to be refactored in order\n\t\t\tto become more flexible. In most cases a warning log will be displayed at the browser\'s console\n\t\t\twhenever there is a use of a deprecated function. Below there is a quick list of fallback support\n\t\t\tthat will be removed in the future versions of the engine.\n\t\t\t\n\t\t\t1. The main engine object was renamed from "gx" to "jse" which stands for the JavaScript Engine.\n\t\t\t2. The "gx.lib" object is removed after a long deprecation period. You should update the modules \n\t\t\t   that contained calls to the functions of this object.\n\t\t\t3. The gx.<collection-name>.register function is deprecated by v1.2, use the \n\t\t\t   <namespace>.<collection>.module() instead.\n\t\t';

        jse.core.debug.info(info);
    };
});

},{}],6:[function(require,module,exports){
'use strict';

/* --------------------------------------------------------------
 config.js 2018-09-12
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.core.config = jse.core.config || {};

/**
 * JSE Configuration Module
 *
 * Once the config object is initialized you cannot change its values. This is done in order to
 * prevent unpleasant situations where one code section changes a core config setting that affects
 * another code section in a way that is hard to discover.
 *
 * ```javascript
 * const appUrl = jse.core.config.get('appUrl');
 * ```
 *
 * @module JSE/Core/config
 */
(function (exports) {

    'use strict';

    // ------------------------------------------------------------------------
    // CONFIGURATION VALUES
    // ------------------------------------------------------------------------

    var config = {
        /**
         * Engine Version
         *
         * @type {String}
         */
        version: '1.6',

        /**
         * App URL
         *
         * e.g. 'http://app.com'
         *
         * @type {String}
         */
        appUrl: null,

        /**
         * Shop URL
         *
         * e.g. 'http://example.org'
         *
         * @deprecated Since v1.4, use appUrl instead.
         *
         * @type {String}
         */
        shopUrl: null,

        /**
         * App Version
         *
         * e.g. '2.7.3.0'
         *
         * @type {String}
         */
        appVersion: null,

        /**
         * Shop Version
         *
         * e.g. '2.7.3.0'
         *
         * @deprecated Since 1.4, use appVersion instead.
         *
         * @type {String}
         */
        shopVersion: null,

        /**
         * URL to JSEngine Directory.
         *
         * e.g. 'http://app.com/JSEngine
         *
         * @type {String}
         */
        engineUrl: null,

        /**
         * Engine Environment
         *
         * Defines the functionality of the engine in many sections.
         *
         * Values: 'development', 'production'
         *
         * @type {String}
         */
        environment: 'production',

        /**
         * Translations Object
         *
         * Contains the loaded translations to be used within JSEngine.
         *
         * @see jse.core.lang object
         *
         * @type {Object}
         */
        translations: {},

        /**
         * Module Collections
         *
         * Provide array with { name: '', attribute: ''} objects that define the collections to be used within
         * the application.
         *
         * @type {Array}
         */
        collections: [],

        /**
         * Current Language Code
         *
         * @type {String}
         */
        languageCode: 'de',

        /**
         * Set the debug level to one of the following: 'DEBUG', 'INFO', 'LOG', 'WARN', 'ERROR',
         * 'ALERT', 'SILENT'.
         *
         * @type {String}
         */
        debug: 'SILENT',

        /**
         * Use cache busting technique when loading modules.
         *
         * @deprecated Since v1.4
         *
         * @see jse.core.module_loader object
         *
         * @type {Boolean}
         */
        cacheBust: true,

        /**
         * Use cache busting token as part of file name.
         *
         * @type {Boolean}
         */
        bustFiles: false,

        /**
         * Whether the client has a mobile interface.
         *
         * @type {Boolean}
         */
        mobile: /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent),

        /**
         * Whether the client supports touch events.
         *
         * @type {Boolean}
         */
        touch: 'ontouchstart' in window || window.ontouchstart || window.onmsgesturechange ? true : false,

        /**
         * Specify the path for the file manager.
         *
         * @deprecated Since v1.4
         *
         * @type {String}
         */
        filemanager: 'includes/ckeditor/filemanager/index.html',

        /**
         * Page token to include in every AJAX request.
         *
         * The page token is used to avoid CSRF attacks. It must be provided by the backend and it will
         * be validated there.
         *
         * @type {String}
         */
        pageToken: '',

        /**
         * Cache Token String
         *
         * This configuration value will be used in production environment for cache busting. It must
         * be provided with the window.JSEngineConfiguration object.
         *
         * @type {String}
         */
        cacheToken: '',

        /**
         * Defines whether the history object is available.
         *
         * @type {Boolean}
         */
        history: history && history.replaceState && history.pushState,

        /**
         * Vue Configuration
         *
         * This configuration wwill be used for adding Vue support in the current page.
         *
         * @type {Object}
         */
        vue: null
    };

    /**
     * Blacklist config values in production environment.
     *
     * @type {String[]}
     */
    var blacklist = ['version', 'appVersion', 'shopVersion'];

    // ------------------------------------------------------------------------
    // PUBLIC METHODS
    // ------------------------------------------------------------------------

    /**
     * Get a configuration value.
     *
     * @param {String} name The configuration value name to be retrieved.
     *
     * @return {*} Returns the config value.
     */
    exports.get = function (name) {
        if (config.environment === 'production' && blacklist.includes(name)) {
            return null;
        }

        return config[name];
    };

    /**
     * Initialize the JS Engine config object.
     *
     * This method will parse the global "JSEngineConfiguration" object and then remove
     * it from the global scope so that it becomes the only config source for javascript.
     *
     * Notice: The only required JSEngineConfiguration values are the "environment" and the "appUrl".
     *
     * @param {Object} jsEngineConfiguration Must contain information that define core operations
     * of the engine. Check the "libs/initialize" entry of the engine documentation.
     */
    exports.init = function (jsEngineConfiguration) {
        config.environment = jsEngineConfiguration.environment;
        config.appUrl = jsEngineConfiguration.appUrl.replace(/\/+$/, ''); // Remove trailing slash from appUrl.

        if (config.environment === 'development') {
            config.cacheBust = false;
            config.minified = false;
            config.debug = 'DEBUG';
        }

        if (jsEngineConfiguration.engineUrl !== undefined) {
            config.engineUrl = jsEngineConfiguration.engineUrl.replace(/\/+$/, '');
        } else {
            config.engineUrl = config.appUrl + '/JSEngine/build';
        }

        if (jsEngineConfiguration.translations !== undefined) {
            config.translations = jsEngineConfiguration.translations;

            for (var sectionName in config.translations) {
                jse.core.lang.addSection(sectionName, config.translations[sectionName]);
            }
        }

        if (jsEngineConfiguration.collections !== undefined) {
            config.collections = jsEngineConfiguration.collections;
        } else {
            config.collections = [{ name: 'controllers', attribute: 'controller' }, { name: 'extensions', attribute: 'extension' }, { name: 'widgets', attribute: 'widget' }];
        }

        if (jsEngineConfiguration.appVersion !== undefined) {
            config.appVersion = jsEngineConfiguration.appVersion;
        }

        if (jsEngineConfiguration.shopUrl !== undefined) {
            jse.core.debug.warn('JS Engine: "shopUrl" is deprecated and will be removed in JS Engine v1.5, please ' + 'use the "appUrl" instead.');
            config.shopUrl = jsEngineConfiguration.shopUrl.replace(/\/+$/, '');
            config.appUrl = config.appUrl || config.shopUrl; // Make sure the "appUrl" value is not empty.
        }

        if (jsEngineConfiguration.shopVersion !== undefined) {
            jse.core.debug.warn('JS Engine: "shopVersion" is deprecated and will be removed in JS Engine v1.5, please ' + 'use the "appVersion" instead.');
            config.shopVersion = jsEngineConfiguration.shopVersion;
        }

        if (jsEngineConfiguration.prefix !== undefined) {
            config.prefix = jsEngineConfiguration.prefix;
        }

        if (jsEngineConfiguration.languageCode !== undefined) {
            config.languageCode = jsEngineConfiguration.languageCode;
        }

        if (document.getElementById('init-js') !== null && document.getElementById('init-js').hasAttribute('data-page-token')) {
            jsEngineConfiguration.pageToken = document.getElementById('init-js').getAttribute('data-page-token');
        }

        if (jsEngineConfiguration.pageToken !== undefined) {
            config.pageToken = jsEngineConfiguration.pageToken;
        }

        if (jsEngineConfiguration.cacheToken !== undefined) {
            config.cacheToken = jsEngineConfiguration.cacheToken;
        }

        if (jsEngineConfiguration.bustFiles !== undefined) {
            config.bustFiles = jsEngineConfiguration.bustFiles;
        }

        if (jsEngineConfiguration.vue !== undefined) {
            config.vue = jsEngineConfiguration.vue;
        }

        // Add the "touchEvents" entry so that modules can bind various touch events depending the browser.
        var generalTouchEvents = {
            start: 'touchstart',
            end: 'trouchend',
            move: 'touchmove'
        };

        var microsoftTouchEvents = {
            start: 'pointerdown',
            end: 'pointerup',
            move: 'pointermove'
        };

        config.touchEvents = window.onmsgesturechange ? microsoftTouchEvents : generalTouchEvents;

        // Set initial registry values. 
        for (var entry in jsEngineConfiguration.registry) {
            jse.core.registry.set(entry, jsEngineConfiguration.registry[entry]);
        }

        // Initialize the module loader object.
        jse.core.module_loader.init();

        // Destroy global EngineConfiguration object.
        delete window.JSEngineConfiguration;
    };
})(jse.core.config);

},{}],7:[function(require,module,exports){
'use strict';

/* --------------------------------------------------------------
 debug.js 2016-09-08
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.core.debug = jse.core.debug || {};

/**
 * JSE Debug Module
 *
 * This object provides an wrapper to the console.log function and enables easy use
 * of the different log types like "info", "warning", "error" etc.
 *
 * @module JSE/Core/debug
 */
(function (exports) {
    'use strict';

    // ------------------------------------------------------------------------
    // VARIABLES
    // ------------------------------------------------------------------------

    /**
     * @type {String}
     */

    var TYPE_DEBUG = 'DEBUG';

    /**
     * @type {String}
     */
    var TYPE_INFO = 'INFO';

    /**
     * @type {String}
     */
    var TYPE_LOG = 'LOG';

    /**
     * @type {String}
     */
    var TYPE_WARN = 'WARN';

    /**
     * @type {String}
     */
    var TYPE_ERROR = 'ERROR';

    /**
     * @type {String}
     */
    var TYPE_ALERT = 'ALERT';

    /**
     * @type {String}
     */
    var TYPE_MOBILE = 'MOBILE';

    /**
     * @type {String}
     */
    var TYPE_SILENT = 'SILENT';

    /**
     * All possible debug levels in the order of importance.
     *
     * @type {String[]}
     */
    var levels = [TYPE_DEBUG, TYPE_INFO, TYPE_LOG, TYPE_WARN, TYPE_ERROR, TYPE_ALERT, TYPE_MOBILE, TYPE_SILENT];

    // ------------------------------------------------------------------------
    // FUNCTIONS
    // ------------------------------------------------------------------------

    /**
     * Set Favicon to Error State.
     *
     * This method will only work if <canvas> is supported from the browser.
     *
     * @private
     */
    function _setFaviconToErrorState() {
        var canvas = document.createElement('canvas');
        var favicon = document.querySelector('[rel="shortcut icon"]');

        if (canvas.getContext && !favicon.className.includes('error-state')) {
            var img = document.createElement('img');
            canvas.height = canvas.width = 16;
            var ctx = canvas.getContext('2d');
            img.onload = function () {
                // Continue once the image has been loaded. 
                ctx.drawImage(this, 0, 0);
                ctx.globalAlpha = 0.65;
                ctx.fillStyle = '#FF0000';
                ctx.rect(0, 0, 16, 16);
                ctx.fill();
                favicon.href = canvas.toDataURL('image/png');
                favicon.className += 'error-state';
            };
            img.src = favicon.href;
        }
    }

    /**
     * Error handler that fetches all exceptions thrown by the javascript.
     *
     * @private
     */
    function _globalErrorHandler() {
        if (jse.core.config.get('environment') !== 'production') {
            // Log the error in the browser's console. 
            if (jse.core.debug !== undefined) {
                jse.core.debug.error('JS Engine Error Handler', arguments);
            } else {
                console.log('JS Engine Error Handler', arguments);
            }

            // Update the page title with an error count.
            var regex = /.\ \[(.+)\]\ /;
            var title = window.document.title;
            var errorCount = 1;

            // Gets the current error count and recreates the default title of the page.
            if (title.match(regex) !== null) {
                errorCount = parseInt(title.match(/\d+/)[0], 10) + 1;
                title = title.replace(regex, '');
            }

            // Re-creates the error flag at the title with the new error count.
            title = '✖ [' + errorCount + '] ' + title;
            window.document.title = title;

            // Set Favicon to Error State.
            _setFaviconToErrorState();
        }

        return true;
    }

    /**
     * Executes the correct console/alert statement.
     *
     * @param {Object} caller (optional) Contains the caller information to be displayed.
     * @param {Object} data (optional) Contains any additional data to be included in the debug output.
     *
     * @private
     */
    function _execute(caller, data) {
        var currentLogIndex = levels.indexOf(caller);
        var allowedLogIndex = levels.indexOf(jse.core.config.get('debug'));
        var consoleMethod = null;

        if (currentLogIndex >= allowedLogIndex) {
            consoleMethod = caller.toLowerCase();

            switch (consoleMethod) {
                case 'alert':
                    alert(JSON.stringify(data));
                    break;

                case 'mobile':
                    var $mobileDebugModal = $('.mobile-debug-modal');

                    if (!$mobileDebugModal.length) {
                        $('<div />').addClass('mobile-debug-modal').css({
                            position: 'fixed',
                            top: 0,
                            left: 0,
                            maxHeight: '50%',
                            minWidth: '200px',
                            maxWidth: '300px',
                            backgroundColor: 'crimson',
                            zIndex: 100000,
                            overflow: 'scroll'
                        }).appendTo($('body'));
                    }

                    $mobileDebugModal.append('<p>' + JSON.stringify(data) + '</p>');
                    break;

                default:
                    if (console === undefined) {
                        return; // There is no console support so do not proceed.
                    }

                    if (typeof console[consoleMethod].apply === 'function' || typeof console.log.apply === 'function') {
                        if (console[consoleMethod] !== undefined) {
                            console[consoleMethod].apply(console, data);
                        } else {
                            console.log.apply(console, data);
                        }
                    } else {
                        console.log(data);
                    }
            }
        }
    }

    /**
     * Bind Global Error Handler
     */
    exports.bindGlobalErrorHandler = function () {
        window.onerror = _globalErrorHandler;
    };

    /**
     * Replaces console.debug
     *
     * @params {*} arguments Any data that should be shown in the console statement.
     */
    exports.debug = function () {
        _execute(TYPE_DEBUG, arguments);
    };

    /**
     * Replaces console.info
     *
     * @params {*} arguments Any data that should be shown in the console statement.
     */
    exports.info = function () {
        _execute(TYPE_INFO, arguments);
    };

    /**
     * Replaces console.log
     *
     * @params {*} arguments Any data that should be shown in the console statement.
     */
    exports.log = function () {
        _execute(TYPE_LOG, arguments);
    };

    /**
     * Replaces console.warn
     *
     * @params {*} arguments Any data that should be shown in the console statement.
     */
    exports.warn = function () {
        _execute(TYPE_WARN, arguments);
    };

    /**
     * Replaces console.error
     *
     * @param {*} arguments Any data that should be shown in the console statement.
     */
    exports.error = function () {
        _execute(TYPE_ERROR, arguments);
    };

    /**
     * Replaces alert
     *
     * @param {*} arguments Any data that should be shown in the console statement.
     */
    exports.alert = function () {
        _execute(TYPE_ALERT, arguments);
    };

    /**
     * Debug info for mobile devices.
     *
     * @param {*} arguments Any data that should be shown in the console statement.
     */
    exports.mobile = function () {
        _execute(TYPE_MOBILE, arguments);
    };
})(jse.core.debug);

},{}],8:[function(require,module,exports){
'use strict';

/* --------------------------------------------------------------
 engine.js 2016-09-08
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.core.engine = jse.core.engine || {};

/**
 * JSE Core Module
 *
 * This object will initialize the page namespaces and collections.
 *
 * @module JSE/Core/engine
 */
(function (exports) {

    'use strict';

    // ------------------------------------------------------------------------
    // PRIVATE FUNCTIONS
    // ------------------------------------------------------------------------

    /**
     * Initialize the page namespaces.
     *
     * This method will search the page HTML for available namespaces.
     *
     * @param {Array} collections Contains the module collection instances to be included in the namespaces.
     *
     * @return {Array} Returns an array with the page namespace names.
     *
     * @private
     */

    function _initNamespaces(collections) {
        var pageNamespaceNames = [];

        // Use the custom pseudo selector defined at extend.js in order to fetch the available namespaces.
        var nodes = Array.from(document.getElementsByTagName('*')),
            regex = /data-(.*)-namespace/;

        var _iteratorNormalCompletion = true;
        var _didIteratorError = false;
        var _iteratorError = undefined;

        try {
            for (var _iterator = nodes[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                var node = _step.value;
                var _iteratorNormalCompletion3 = true;
                var _didIteratorError3 = false;
                var _iteratorError3 = undefined;

                try {
                    for (var _iterator3 = Array.from(node.attributes)[Symbol.iterator](), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true) {
                        var attribute = _step3.value;

                        if (attribute.name.search(regex) !== -1) {
                            // Parse the namespace name and source URL.
                            var name = attribute.name.replace(regex, '$1'),
                                source = attribute.value;

                            // Check if the namespace is already defined.
                            if (pageNamespaceNames.indexOf(name) > -1) {
                                if (window[name].source !== source) {
                                    jse.core.debug.error('Element with the duplicate namespace name: ' + node);
                                    throw new Error('The namespace "' + name + '" is already defined. Please select another ' + 'name for your namespace.');
                                }
                                continue; // The namespace is already defined, continue loop.
                            }

                            if (source === '') {
                                throw new SyntaxError('Namespace source is empty: ' + name);
                            }

                            // Create a new namespaces instance in the global scope (the global scope is used for 
                            // fallback support of old module definitions).
                            if (name === 'jse') {
                                // Modify the engine object with Namespace attributes.
                                _convertEngineToNamespace(source, collections);
                            } else {
                                window[name] = new jse.constructors.Namespace(name, source, collections);
                            }

                            pageNamespaceNames.push(name);
                            node.removeAttribute(attribute.name);
                        }
                    }
                } catch (err) {
                    _didIteratorError3 = true;
                    _iteratorError3 = err;
                } finally {
                    try {
                        if (!_iteratorNormalCompletion3 && _iterator3.return) {
                            _iterator3.return();
                        }
                    } finally {
                        if (_didIteratorError3) {
                            throw _iteratorError3;
                        }
                    }
                }
            }

            // Throw an error if no namespaces were found.
        } catch (err) {
            _didIteratorError = true;
            _iteratorError = err;
        } finally {
            try {
                if (!_iteratorNormalCompletion && _iterator.return) {
                    _iterator.return();
                }
            } finally {
                if (_didIteratorError) {
                    throw _iteratorError;
                }
            }
        }

        if (pageNamespaceNames.length === 0) {
            throw new Error('No module namespaces were found, without namespaces it is not possible to ' + 'load any modules.');
        }

        // Initialize the namespace instances.
        var deferredCollection = [];

        var _iteratorNormalCompletion2 = true;
        var _didIteratorError2 = false;
        var _iteratorError2 = undefined;

        try {
            var _loop = function _loop() {
                var name = _step2.value;

                var deferred = $.Deferred();

                deferredCollection.push(deferred);

                window[name].init().done(deferred.resolve).fail(deferred.reject).always(function () {
                    return jse.core.debug.info('Namespace promises were resolved: ', name);
                });
            };

            for (var _iterator2 = pageNamespaceNames[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
                _loop();
            }

            // Trigger an event after the engine has initialized all new modules.
        } catch (err) {
            _didIteratorError2 = true;
            _iteratorError2 = err;
        } finally {
            try {
                if (!_iteratorNormalCompletion2 && _iterator2.return) {
                    _iterator2.return();
                }
            } finally {
                if (_didIteratorError2) {
                    throw _iteratorError2;
                }
            }
        }

        $.when.apply(undefined, deferredCollection).always(function () {
            var event = document.createEvent('Event');
            event.initEvent('JSENGINE_INIT_FINISHED', true, true);
            document.querySelector('body').dispatchEvent(event);
            jse.core.registry.set('jseEndTime', new Date().getTime());
            jse.core.debug.info('JS Engine Loading Time: ', jse.core.registry.get('jseEndTime') - jse.core.registry.get('jseStartTime'), 'ms');
            if (window.Cypress) {
                window.jseReady = true;
            }
        });

        return pageNamespaceNames;
    }

    /**
     * Convert the "jse" object to a Namespace compatible object.
     *
     * In order to support the "jse" namespace name for the core modules placed in the "JSEngine"
     * directory, we will need to modify the already existing "jse" object so that it can operate
     * as a namespace without losing its initial attributes.
     *
     * @param {String} source Namespace source path for the module files.
     * @param {Array} collections Contain instances to the prototype collection instances.
     *
     * @private
     */
    function _convertEngineToNamespace(source, collections) {
        var tmpNamespace = new jse.constructors.Namespace('jse', source, collections);
        jse.name = tmpNamespace.name;
        jse.source = tmpNamespace.source;
        jse.collections = tmpNamespace.collections;
        jse.init = jse.constructors.Namespace.prototype.init;
    }

    // ------------------------------------------------------------------------
    // PUBLIC FUNCTIONS
    // ------------------------------------------------------------------------

    /**
     * Initialize the engine.
     *
     * @param {Array} collections Contains the supported module collection data.
     */
    exports.init = function (collections) {
        // Global error handler that executes if an uncaught JS error occurs on page.
        jse.core.debug.bindGlobalErrorHandler();

        // Initialize the page namespaces.
        var pageNamespaceNames = _initNamespaces(collections);

        // Log the page namespaces (for debugging only).
        jse.core.debug.info('Page Namespaces: ' + pageNamespaceNames.join());

        // Update the engine registry.
        jse.core.registry.set('namespaces', pageNamespaceNames);
    };
})(jse.core.engine);

},{}],9:[function(require,module,exports){
'use strict';

/* --------------------------------------------------------------
 extensions.js 2017-03-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * JSE Extensions
 *
 * Extend the default behaviour of engine components or external plugins before they are loaded.
 *
 * @module JSE/Core/extend
 */
(function () {

    'use strict';

    // ------------------------------------------------------------------------
    // PARSE MODULE DATA JQUERY EXTENSION
    // ------------------------------------------------------------------------

    $.fn.extend({
        parseModuleData: function parseModuleData(moduleName) {
            if (!moduleName || moduleName === '') {
                throw new Error('Module name was not provided as an argument.');
            }

            var initialData = $(this).data();
            var filteredData = {};

            // Searches for module relevant data inside the main-data-object. Data for other widgets will not get 
            // passed to this widget.
            $.each(initialData, function (key, value) {
                if (key.indexOf(moduleName) === 0 || key.indexOf(moduleName.toLowerCase()) === 0) {
                    var newKey = key.substr(moduleName.length);
                    newKey = newKey.substr(0, 1).toLowerCase() + newKey.substr(1);
                    filteredData[newKey] = value;
                }
            });

            return filteredData;
        }
    });

    // ------------------------------------------------------------------------
    // DATEPICKER REGIONAL INFO
    // ------------------------------------------------------------------------

    if ($.datepicker !== undefined) {
        $.datepicker.regional.de = {
            dateFormat: 'dd.mm.yy',
            firstDay: 1,
            isRTL: false
        };
        $.datepicker.setDefaults($.datepicker.regional.de);
    }
})();

},{}],10:[function(require,module,exports){
/* --------------------------------------------------------------
 initialize.js 2016-09-08
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

'use strict';

/**
 * JSE Initialization Module
 *
 * The document-ready event of the page will trigger the JavaScript Engine initialization. The
 * engine requires a global configuration object "window.JSEngineConfiguration" to be pre-defined
 * in order to retrieve the basic configuration info. After a successful initialization this object
 * is removed from the window object.
 *
 * ### Configuration Sample
 *
 * ```js
 * window.JSEngineConfiguration = {
 *   environment: 'production',
 *   appUrl: 'http://app.com',
 *   collections: [
 *     {name: 'controllers', attribute: 'controller'}
 *   ],  
 *   translations: {
 *     'sectionName': { 'translationKey': 'translationValue' },
 *     'anotherSection': { ... }
 *   },
 *   languageCode: 'en',
 *   pageToken: '9asd7f9879sd8f79s98s7d98f'
 * };
 * ```
 *
 * @module JSE/Core/initialize
 */

// Initialize base engine object. Every other part of the engine will refer to this
// central object for the core operations.

window.jse = {
    core: {},
    libs: {},
    constructors: {}
};

// Initialize the engine on window load. 
document.addEventListener('DOMContentLoaded', function () {
    try {
        // Check if global JSEngineConfiguration object is defined.
        if (window.JSEngineConfiguration === undefined) {
            throw new Error('The "window.JSEngineConfiguration" object is not defined in the global scope. ' + 'This object is required by the engine upon its initialization.');
        }

        // Parse JSEngineConfiguration object.
        jse.core.config.init(window.JSEngineConfiguration);

        // Store the JSE start time in registry (profiling). 
        jse.core.registry.set('jseStartTime', Date.now());

        // Initialize the module collections.
        jse.core.engine.init(jse.core.config.get('collections'));
    } catch (exception) {
        jse.core.debug.error('Unexpected error during JS Engine initialization!', exception);
        // Inform the engine about the exception.
        var event = document.createEvent('CustomEvent');
        event.initCustomEvent('error', true, true, exception);
        window.dispatchEvent(event);
    }

    SVGInject.setOptions({
        onFail: function onFail(img, svg) {
            img.classList.remove('svg--inject'); // if injection fails show the img element
        }
    });
    // inject images with an .svg file ending
    SVGInject(document.querySelectorAll('img.svg--inject'), {
        onAllFinish: function onAllFinish() {
            // the SVG injection has finished for all three images

        }
    });
});

},{}],11:[function(require,module,exports){
'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/* --------------------------------------------------------------
 lang.js 2016-08-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.core.lang = jse.core.lang || {};

/**
 * JSE Localization Library
 *
 * The global Lang object contains language information that can be easily used in your
 * JavaScript code. The object contains constance translations and dynamic sections that
 * can be loaded and used in different page.
 *
 * #### Important
 * The engine will automatically load translation sections that are present in the
 * `window.JSEngineConfiguration.translations` property upon initialization. For more
 * information look at the "core/initialize" page of documentation reference.
 *
 * ```javascript
 * jse.core.lang.addSection('sectionName', { translationKey: 'translationValue' }); // Add translation section.
 * jse.core.translate('translationKey', 'sectionName'); // Get the translated string.
 * jse.core.getSections(); // returns array with sections e.g. ['admin_buttons', 'general']
 * ```
 *
 * @module JSE/Core/lang
 */
(function (exports) {

    'use strict';

    // ------------------------------------------------------------------------
    // VARIABLES
    // ------------------------------------------------------------------------

    /**
     * Contains various translation sections.
     *
     * @type {Object}
     */

    var sections = {};

    // ------------------------------------------------------------------------
    // PUBLIC METHODS
    // ------------------------------------------------------------------------

    /**
     * Add a translation section.
     *
     * @param {String} name Name of the section, used later for accessing translation strings.
     * @param {Object} translations Key - value object containing the translations.
     *
     * @throws {Error} If "name" or "translations" arguments are invalid.
     */
    exports.addSection = function (name, translations) {
        if (typeof name !== 'string' || (typeof translations === 'undefined' ? 'undefined' : _typeof(translations)) !== 'object' || translations === null) {
            throw new Error('window.gx.core.lang.addSection: Invalid arguments provided (name: ' + (typeof name === 'undefined' ? 'undefined' : _typeof(name)) + ', ' + ('translations: ' + (typeof translations === 'undefined' ? 'undefined' : _typeof(translations)) + ').'));
        }
        sections[name] = translations;
    };

    /**
     * Get loaded translation sections.
     *
     * Useful for asserting present translation sections.
     *
     * @return {Array} Returns array with the existing sections.
     */
    exports.getSections = function () {
        var result = [];

        for (var section in sections) {
            result.push(section);
        }

        return result;
    };

    /**
     * Returns an entire section.
     *
     * @param section
     * @returns {*}
     */
    exports.getSection = function (section) {
        if (typeof section !== 'string') {
            throw new Error('Invalid argument provided to getSection (section: ' + (typeof section === 'undefined' ? 'undefined' : _typeof(section)) + ').');
        }

        if (sections[section] === undefined) {
            throw new Error('Section ' + section + ' is unavailable.');
        }

        return sections[section];
    };

    /**
     * Translate string in Javascript code.
     *
     * @param {String} phrase Name of the phrase containing the translation.
     * @param {String} section Section name containing the translation string.
     *
     * @return {String} Returns the translated string.
     *
     * @throws {Error} If provided arguments are invalid.
     * @throws {Error} If required section does not exist or translation could not be found.
     */
    exports.translate = function (phrase, section) {
        // Validate provided arguments.
        if (typeof phrase !== 'string' || typeof section !== 'string') {
            throw new Error('Invalid arguments provided in translate method (phrase: ' + (typeof phrase === 'undefined' ? 'undefined' : _typeof(phrase)) + ', ' + ('section: ' + (typeof section === 'undefined' ? 'undefined' : _typeof(section)) + ').'));
        }

        // Check if translation exists.
        if (sections[section] === undefined || sections[section][phrase] === undefined) {
            jse.core.debug.warn('Could not found requested translation (phrase: ' + phrase + ', section: ' + section + ').');
            return '{' + section + '.' + phrase + '}';
        }

        return sections[section][phrase];
    };
})(jse.core.lang);

},{}],12:[function(require,module,exports){
'use strict';

require('./initialize');

require('../constructors/collection');

require('../constructors/data_binding');

require('../constructors/module');

require('../constructors/namespace');

require('./about');

require('./config');

require('./debug');

require('./engine');

require('./extend');

require('./lang');

require('./require');

require('./module_loader');

require('./polyfills');

require('./registry');

require('./vue');

},{"../constructors/collection":1,"../constructors/data_binding":2,"../constructors/module":3,"../constructors/namespace":4,"./about":5,"./config":6,"./debug":7,"./engine":8,"./extend":9,"./initialize":10,"./lang":11,"./module_loader":13,"./polyfills":14,"./registry":15,"./require":16,"./vue":17}],13:[function(require,module,exports){
'use strict';

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
        var link = document.createElement('link');
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
        var cacheBust = '';

        if (jse.core.config.get('environment') === 'production' && jse.core.config.get('bustFiles') === false && jse.core.config.get('cacheToken')) {
            cacheBust = 'bust=' + jse.core.config.get('cacheToken');
        }

        var config = {
            baseUrl: jse.core.config.get('appUrl'),
            urlArgs: cacheBust,
            onError: function onError(error) {
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
        var _iteratorNormalCompletion = true;
        var _didIteratorError = false;
        var _iteratorError = undefined;

        try {
            for (var _iterator = dependencies[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                var dependency = _step.value;

                if (dependency.includes('.css')) {
                    _loadCss(dependency);
                    var index = dependencies.indexOf(dependency);
                    dependencies.splice(index, 1);
                }
            }
        } catch (err) {
            _didIteratorError = true;
            _iteratorError = err;
        } finally {
            try {
                if (!_iteratorNormalCompletion && _iterator.return) {
                    _iterator.return();
                }
            } finally {
                if (_didIteratorError) {
                    throw _iteratorError;
                }
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
        var deferred = $.Deferred();

        try {
            if (name === '') {
                deferred.reject(new Error('Module name cannot be empty.'));
            }

            var baseModuleName = name.replace(/.*\/(.*)$/, '$1'); // Name without the parent directories.

            // Try to load the cached instance of the module.
            var cached = collection.cache.modules[baseModuleName];
            if (cached && cached.code === 'function') {
                deferred.resolve(new jse.constructors.Module($element, baseModuleName, collection));
                return true; // continue loop
            }

            var bustSuffix = '';
            if (jse.core.config.get('environment') === 'production' && jse.core.config.get('bustFiles') && jse.core.config.get('cacheToken')) {
                bustSuffix = '-bust_' + jse.core.config.get('cacheToken');
            }

            // Try to load the module file from the server.
            var fileExtension = jse.core.config.get('debug') !== 'DEBUG' ? '.min.js' : '.js';
            var url = collection.namespace.source + '/' + collection.name + '/' + name + bustSuffix + fileExtension;

            window.require([url], function () {
                if (collection.cache.modules[baseModuleName] === undefined) {
                    throw new Error('Module "' + name + '" wasn\'t defined correctly. Check the module code for ' + 'further troubleshooting.');
                }

                // Use the slice method for copying the array. 
                var dependencies = collection.cache.modules[baseModuleName].dependencies.slice();

                if (dependencies.length === 0) {
                    // no dependencies
                    deferred.resolve(new jse.constructors.Module($element, baseModuleName, collection));
                    return true; // continue loop
                }

                // Load the dependencies first.
                for (var index in dependencies) {
                    var dependency = dependencies[index];

                    if (dependency.indexOf('.css') !== -1) {
                        _loadCss(dependency);
                        dependencies.splice(index, 1);
                        continue;
                    }

                    // Then convert the relative path to JSEngine/libs directory.
                    if (dependency.indexOf('http') === -1) {
                        dependencies[index] = jse.core.config.get('engineUrl') + '/libs/' + dependency + bustSuffix + fileExtension;
                    } else if (dependency.substr(-3) !== '.js') {
                        // Then add the dynamic file extension to the URL.
                        dependencies[index] += bustSuffix + fileExtension;
                    }
                }

                window.require(dependencies, function () {
                    deferred.resolve(new jse.constructors.Module($element, baseModuleName, collection));
                });
            });
        } catch (exception) {
            deferred.reject(exception);
        }

        return deferred.promise();
    };
})(jse.core.module_loader);

},{}],14:[function(require,module,exports){
'use strict';

/* --------------------------------------------------------------
 polyfills.js 2016-05-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * JSE Polyfills
 *
 * Required polyfills for compatibility among old browsers.
 *
 * @module JSE/Core/polyfills
 */
(function () {

    'use strict';

    // Internet Explorer does not support the origin property of the window.location object.
    // {@link http://tosbourn.com/a-fix-for-window-location-origin-in-internet-explorer}

    if (!window.location.origin) {
        window.location.origin = window.location.protocol + '//' + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
    }

    // Date.now method polyfill
    // {@link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/now}
    if (!Date.now) {
        Date.now = function now() {
            return new Date().getTime();
        };
    }
})();

},{}],15:[function(require,module,exports){
'use strict';

/* --------------------------------------------------------------
 registry.js 2016-09-08
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.core.registry = jse.core.registry || {};

/**
 * JS Engine Registry
 *
 * This object contains string data that other sections of the engine need in order to operate correctly.
 *
 * @module JSE/Core/registry
 */
(function (exports) {

    'use strict';

    /**
     * Contains the registry values.
     *
     * @type {Object[]}
     */

    var registry = [];

    /**
     * Set a value in the registry.
     *
     * @param {String} name Contains the name of the entry to be added.
     * @param {*} value The value to be written in the registry.
     */
    exports.set = function (name, value) {
        // If a registry entry with the same name exists already the following console warning will
        // inform developers that they are overwriting an existing value, something useful when debugging.
        if (registry[name] !== undefined) {
            jse.core.debug.warn('The registry value with the name "' + name + '" will be overwritten.');
        }

        registry[name] = value;
    };

    /**
     * Get a value from the registry.
     *
     * @param {String} name The name of the entry value to be returned.
     *
     * @returns {*} Returns the value that matches the name.
     */
    exports.get = function (name) {
        return registry[name];
    };

    /**
     * Check the current content of the registry object.
     *
     * This method is only available when the engine environment is turned into development.
     */
    exports.debug = function () {
        if (jse.core.config.get('environment') === 'development') {
            jse.core.debug.log('Registry Object:', registry);
        } else {
            throw new Error('This function is not allowed in a production environment.');
        }
    };
})(jse.core.registry);

},{}],16:[function(require,module,exports){
(function (global){
'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/* --------------------------------------------------------------
 require.js 2017-03-28
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Asynchronous Module Loading
 *
 * This module is a fork of RequireJS without the AMD functionality. The global "define" method is removed as
 * it's not necessary by JS Engine.
 *
 * {@link https://github.com/requirejs/requirejs}
 *
 * Not using strict: uneven strict support in browsers, #392, and causes problems with requirejs.exec()/transpiler
 * plugins that may not be strict.
 */
(function () {

    // ------------------------------------------------------------------------
    // VARIABLES
    // ------------------------------------------------------------------------ 

    window.requirejs = undefined;
    window.require = undefined;

    var req = void 0;
    var s = void 0;
    var head = void 0;
    var baseElement = void 0;
    var dataMain = void 0;
    var src = void 0;
    var interactiveScript = void 0;
    var mainScript = void 0;
    var subPath = void 0;
    var version = '2.1.22';
    var jsSuffixRegExp = /\.js$/;
    var currDirRegExp = /^\.\//;
    var op = Object.prototype;
    var ostring = op.toString;
    var hasOwn = op.hasOwnProperty;
    var isBrowser = !!(typeof window !== 'undefined' && typeof navigator !== 'undefined' && window.document);
    var isWebWorker = !isBrowser && typeof importScripts !== 'undefined';
    // PS3 indicates loaded and complete, but need to wait for complet specifically. Sequence is 'loading', 'loaded', 
    // execution then 'complete'. The UA check is unfortunate, but not sure how to feature test w/o causing perf issues.
    var readyRegExp = isBrowser && navigator.platform === 'PLAYSTATION 3' ? /^complete$/ : /^(complete|loaded)$/;
    var defContextName = '_';
    // Oh the tragedy, detecting opera. See the usage of isOpera for reason.
    var isOpera = typeof opera !== 'undefined' && opera.toString() === '[object Opera]';
    var contexts = {};
    var cfg = {};
    var globalDefQueue = [];
    var useInteractive = false;

    // ------------------------------------------------------------------------
    // FUNCTIONS
    // ------------------------------------------------------------------------ 

    /**
     * Check whether value is a function.
     *
     * @param {*} it Value to be checked.
     *
     * @return {boolean} Returns the validation result.
     */
    function isFunction(it) {
        return ostring.call(it) === '[object Function]';
    }

    /**
     * Check whether value is an array.
     *
     * @param {*} it Value to be checked.
     *
     * @return {boolean} Returns the validation result.
     */
    function isArray(it) {
        return ostring.call(it) === '[object Array]';
    }

    /**
     * Helper function for iterating over an array.
     *
     * If the func returns a true value, it will break out of the loop.
     */
    function each(ary, func) {
        if (ary) {
            for (var i = 0; i < ary.length; i += 1) {
                if (ary[i] && func(ary[i], i, ary)) {
                    break;
                }
            }
        }
    }

    /**
     * Helper function for iterating over an array backwards.
     *
     * If the func returns a true value, it will break out of the loop.
     */
    function eachReverse(ary, func) {
        if (ary) {
            var i = void 0;
            for (i = ary.length - 1; i > -1; i -= 1) {
                if (ary[i] && func(ary[i], i, ary)) {
                    break;
                }
            }
        }
    }

    /**
     * Check whether an object has a specific property.
     *
     * @param {Object} obj Object to be checked.
     * @param {String} prop Property name to be checked.
     *
     * @return {Boolean} Returns the validation result.
     */
    function hasProp(obj, prop) {
        return hasOwn.call(obj, prop);
    }

    /**
     * Check if an object has a property and if that property contains a truthy value.
     *
     * @param {Object} obj Object to be checked.
     * @param {String} prop Property name to be checked.
     *
     * @return {Boolean} Returns the validation result.
     */
    function getOwn(obj, prop) {
        return hasProp(obj, prop) && obj[prop];
    }

    /**
     * Cycles over properties in an object and calls a function for each property value.
     *
     * If the function returns a truthy value, then the iteration is stopped.
     */
    function eachProp(obj, func) {
        var prop = void 0;
        for (prop in obj) {
            if (hasProp(obj, prop)) {
                if (func(obj[prop], prop)) {
                    break;
                }
            }
        }
    }

    /**
     * Simple function to mix in properties from source into target, but only if target does not already have a
     * property of the same name.
     */
    function mixin(target, source, force, deepStringMixin) {
        if (source) {
            eachProp(source, function (value, prop) {
                if (force || !hasProp(target, prop)) {
                    if (deepStringMixin && (typeof value === 'undefined' ? 'undefined' : _typeof(value)) === 'object' && value && !isArray(value) && !isFunction(value) && !(value instanceof RegExp)) {

                        if (!target[prop]) {
                            target[prop] = {};
                        }
                        mixin(target[prop], value, force, deepStringMixin);
                    } else {
                        target[prop] = value;
                    }
                }
            });
        }
        return target;
    }

    // Similar to Function.prototype.bind, but the 'this' object is specified first, since it is easier to read/figure 
    // out what 'this' will be.
    function bind(obj, fn) {
        return function () {
            return fn.apply(obj, arguments);
        };
    }

    function scripts() {
        return document.getElementsByTagName('script');
    }

    function defaultOnError(err) {
        throw err;
    }

    // Allow getting a global that is expressed in dot notation, like 'a.b.c'.
    function getGlobal(value) {
        if (!value) {
            return value;
        }
        var g = global;
        each(value.split('.'), function (part) {
            g = g[part];
        });
        return g;
    }

    /**
     * Constructs an error with a pointer to an URL with more information.
     *
     * @param {String} id The error ID that maps to an ID on a web page.
     * @param {String} msg Human readable error.
     * @param {Error} [err] The original error, if there is one.
     *
     * @return {Error}
     */
    function makeError(id, msg, err, requireModules) {
        var error = new Error(msg + '\nhttp://requirejs.org/docs/errors.html#' + id);

        error.requireType = id;
        error.requireModules = requireModules;

        if (err) {
            error.originalError = err;
        }

        return error;
    }

    if (typeof window.requirejs !== 'undefined') {
        if (isFunction(window.requirejs)) {
            // Do not overwrite an existing requirejs instance.
            return;
        }
        cfg = window.requirejs;
        window.requirejs = undefined;
    }

    // Allow for a require config object
    if (typeof window.require !== 'undefined' && !isFunction(window.require)) {
        // assume it is a config object.
        cfg = window.require;
        window.require = undefined;
    }

    function newContext(contextName) {
        var inCheckLoaded = void 0,
            Module = void 0,
            context = void 0,
            handlers = void 0,
            checkLoadedTimeoutId = void 0,
            _config = {
            // Defaults. Do not set a default for map
            // config to speed up normalize(), which
            // will run faster if there is no default.
            waitSeconds: 7,
            baseUrl: './',
            paths: {},
            bundles: {},
            pkgs: {},
            shim: {},
            config: {}
        },
            registry = {},

        // registry of just enabled modules, to speed
        // cycle breaking code when lots of modules
        // are registered, but not activated.
        enabledRegistry = {},
            undefEvents = {},
            defQueue = [],
            _defined = {},
            urlFetched = {},
            bundlesMap = {},
            requireCounter = 1,
            unnormalizedCounter = 1;

        /**
         * Trims the . and .. from an array of path segments.
         *
         * It will keep a leading path segment if a .. will become the first path segment, to help with module name
         * lookups, which act like paths, but can be remapped. But the end result, all paths that use this function
         * should look normalized.
         *
         * NOTE: this method MODIFIES the input array.
         *
         * @param {Array} ary the array of path segments.
         */
        function trimDots(ary) {
            var i = void 0,
                part = void 0;
            for (i = 0; i < ary.length; i++) {
                part = ary[i];
                if (part === '.') {
                    ary.splice(i, 1);
                    i -= 1;
                } else if (part === '..') {
                    // If at the start, or previous value is still ..,  keep them so that when converted to a path it 
                    // may still work when converted to a path, even though as an ID it is less than ideal. In larger 
                    // point releases, may be better to just kick out an error.
                    if (i === 0 || i === 1 && ary[2] === '..' || ary[i - 1] === '..') {
                        continue;
                    } else if (i > 0) {
                        ary.splice(i - 1, 2);
                        i -= 2;
                    }
                }
            }
        }

        /**
         * Given a relative module name, like ./something, normalize it to a real name that can be mapped to a path.
         *
         * @param {String} name the relative name
         * @param {String} baseName a real name that the name arg is relative to.
         * @param {Boolean} applyMap apply the map config to the value. Should only be done if this normalization is
         * for a dependency ID.
         *
         * @return {String} normalized name
         */
        function normalize(name, baseName, applyMap) {
            var pkgMain = void 0,
                mapValue = void 0,
                nameParts = void 0,
                i = void 0,
                j = void 0,
                nameSegment = void 0,
                lastIndex = void 0,
                foundMap = void 0,
                foundI = void 0,
                foundStarMap = void 0,
                starI = void 0,
                normalizedBaseParts = void 0,
                baseParts = baseName && baseName.split('/'),
                map = _config.map,
                starMap = map && map['*'];

            // Adjust any relative paths.
            if (name) {
                name = name.split('/');
                lastIndex = name.length - 1;

                // If wanting node ID compatibility, strip .js from end of IDs. Have to do this here, and not in 
                // nameToUrl because node allows either .js or non .js to map to same file.
                if (_config.nodeIdCompat && jsSuffixRegExp.test(name[lastIndex])) {
                    name[lastIndex] = name[lastIndex].replace(jsSuffixRegExp, '');
                }

                // Starts with a '.' so need the baseName
                if (name[0].charAt(0) === '.' && baseParts) {
                    // Convert baseName to array, and lop off the last part, so that . matches that 'directory' and not 
                    // name of the baseName's module. For instance, baseName of 'one/two/three', maps to 
                    // 'one/two/three.js', but we want the directory, 'one/two' for this normalization.
                    normalizedBaseParts = baseParts.slice(0, baseParts.length - 1);
                    name = normalizedBaseParts.concat(name);
                }

                trimDots(name);
                name = name.join('/');
            }

            // Apply map config if available.
            if (applyMap && map && (baseParts || starMap)) {
                nameParts = name.split('/');

                outerLoop: for (i = nameParts.length; i > 0; i -= 1) {
                    nameSegment = nameParts.slice(0, i).join('/');

                    if (baseParts) {
                        // Find the longest baseName segment match in the config. So, do joins on the biggest to 
                        // smallest lengths of baseParts.
                        for (j = baseParts.length; j > 0; j -= 1) {
                            mapValue = getOwn(map, baseParts.slice(0, j).join('/'));

                            // baseName segment has config, find if it has one for this name.
                            if (mapValue) {
                                mapValue = getOwn(mapValue, nameSegment);
                                if (mapValue) {
                                    // Match, update name to the new value.
                                    foundMap = mapValue;
                                    foundI = i;
                                    break outerLoop;
                                }
                            }
                        }
                    }

                    // Check for a star map match, but just hold on to it, if there is a shorter segment match later in 
                    // a matching config, then favor over this star map.
                    if (!foundStarMap && starMap && getOwn(starMap, nameSegment)) {
                        foundStarMap = getOwn(starMap, nameSegment);
                        starI = i;
                    }
                }

                if (!foundMap && foundStarMap) {
                    foundMap = foundStarMap;
                    foundI = starI;
                }

                if (foundMap) {
                    nameParts.splice(0, foundI, foundMap);
                    name = nameParts.join('/');
                }
            }

            // If the name points to a package's name, use the package main instead.
            pkgMain = getOwn(_config.pkgs, name);

            return pkgMain ? pkgMain : name;
        }

        function removeScript(name) {
            if (isBrowser) {
                each(scripts(), function (scriptNode) {
                    if (scriptNode.getAttribute('data-requiremodule') === name && scriptNode.getAttribute('data-requirecontext') === context.contextName) {
                        scriptNode.parentNode.removeChild(scriptNode);
                        return true;
                    }
                });
            }
        }

        function hasPathFallback(id) {
            var pathConfig = getOwn(_config.paths, id);
            if (pathConfig && isArray(pathConfig) && pathConfig.length > 1) {
                // Pop off the first array value, since it failed, and retry.
                pathConfig.shift();
                context.require.undef(id);

                // Custom require that does not do map translation, since ID is "absolute", already mapped/resolved.
                context.makeRequire(null, {
                    skipMap: true
                })([id]);

                return true;
            }
        }

        // Turns a plugin!resource to [plugin, resource] with the plugin being undefined if the name did not have a 
        // plugin prefix.
        function splitPrefix(name) {
            var prefix = void 0,
                index = name ? name.indexOf('!') : -1;
            if (index > -1) {
                prefix = name.substring(0, index);
                name = name.substring(index + 1, name.length);
            }
            return [prefix, name];
        }

        /**
         * Creates a module mapping that includes plugin prefix, module name, and path. If parentModuleMap is provided
         * it will also normalize the name via require.normalize()
         *
         * @param {String} name The module name.
         * @param {String} [parentModuleMap] Parent module map for the module name, used to resolve relative names.
         * @param {Boolean} isNormalized Is the ID already normalized? This is true if this call is done for a define()
         * module ID.
         * @param {Boolean} applyMap: apply the map config to the ID. Should only be true if this map is for a dependency.
         *
         * @return {Object}
         */
        function makeModuleMap(name, parentModuleMap, isNormalized, applyMap) {
            var url = void 0,
                pluginModule = void 0,
                suffix = void 0,
                nameParts = void 0,
                prefix = null,
                parentName = parentModuleMap ? parentModuleMap.name : null,
                originalName = name,
                isDefine = true,
                normalizedName = '';

            // If no name, then it means it is a require call, generate an
            // internal name.
            if (!name) {
                isDefine = false;
                name = '_@r' + (requireCounter += 1);
            }

            nameParts = splitPrefix(name);
            prefix = nameParts[0];
            name = nameParts[1];

            if (prefix) {
                prefix = normalize(prefix, parentName, applyMap);
                pluginModule = getOwn(_defined, prefix);
            }

            // Account for relative paths if there is a base name.
            if (name) {
                if (prefix) {
                    if (pluginModule && pluginModule.normalize) {
                        // Plugin is loaded, use its normalize method.
                        normalizedName = pluginModule.normalize(name, function (name) {
                            return normalize(name, parentName, applyMap);
                        });
                    } else {
                        // If nested plugin references, then do not try to
                        // normalize, as it will not normalize correctly. This
                        // places a restriction on resourceIds, and the longer
                        // term solution is not to normalize until plugins are
                        // loaded and all normalizations to allow for async
                        // loading of a loader plugin. But for now, fixes the
                        // common uses. Details in #1131
                        normalizedName = name.indexOf('!') === -1 ? normalize(name, parentName, applyMap) : name;
                    }
                } else {
                    // A regular module.
                    normalizedName = normalize(name, parentName, applyMap);

                    // Normalized name may be a plugin ID due to map config
                    // application in normalize. The map config values must
                    // already be normalized, so do not need to redo that part.
                    nameParts = splitPrefix(normalizedName);
                    prefix = nameParts[0];
                    normalizedName = nameParts[1];
                    isNormalized = true;

                    url = context.nameToUrl(normalizedName);
                }
            }

            // If the id is a plugin id that cannot be determined if it needs
            // normalization, stamp it with a unique ID so two matching relative
            // ids that may conflict can be separate.
            suffix = prefix && !pluginModule && !isNormalized ? '_unnormalized' + (unnormalizedCounter += 1) : '';

            return {
                prefix: prefix,
                name: normalizedName,
                parentMap: parentModuleMap,
                unnormalized: !!suffix,
                url: url,
                originalName: originalName,
                isDefine: isDefine,
                id: (prefix ? prefix + '!' + normalizedName : normalizedName) + suffix
            };
        }

        function getModule(depMap) {
            var id = depMap.id,
                mod = getOwn(registry, id);

            if (!mod) {
                mod = registry[id] = new context.Module(depMap);
            }

            return mod;
        }

        function on(depMap, name, fn) {
            var id = depMap.id,
                mod = getOwn(registry, id);

            if (hasProp(_defined, id) && (!mod || mod.defineEmitComplete)) {
                if (name === 'defined') {
                    fn(_defined[id]);
                }
            } else {
                mod = getModule(depMap);
                if (mod.error && name === 'error') {
                    fn(mod.error);
                } else {
                    mod.on(name, fn);
                }
            }
        }

        function onError(err, errback) {
            var ids = err.requireModules,
                notified = false;

            if (errback) {
                errback(err);
            } else {
                each(ids, function (id) {
                    var mod = getOwn(registry, id);
                    if (mod) {
                        // Set error on module, so it skips timeout checks.
                        mod.error = err;
                        if (mod.events.error) {
                            notified = true;
                            mod.emit('error', err);
                        }
                    }
                });

                if (!notified) {
                    req.onError(err);
                }
            }
        }

        /**
         * Internal method to transfer globalQueue items to this context's
         * defQueue.
         */
        function takeGlobalQueue() {
            // Push all the globalDefQueue items into the context's defQueue
            if (globalDefQueue.length) {
                each(globalDefQueue, function (queueItem) {
                    var id = queueItem[0];
                    if (typeof id === 'string') {
                        context.defQueueMap[id] = true;
                    }
                    defQueue.push(queueItem);
                });
                globalDefQueue = [];
            }
        }

        handlers = {
            'require': function require(mod) {
                if (mod.require) {
                    return mod.require;
                } else {
                    return mod.require = context.makeRequire(mod.map);
                }
            },
            'exports': function exports(mod) {
                mod.usingExports = true;
                if (mod.map.isDefine) {
                    if (mod.exports) {
                        return _defined[mod.map.id] = mod.exports;
                    } else {
                        return mod.exports = _defined[mod.map.id] = {};
                    }
                }
            },
            'module': function module(mod) {
                if (mod.module) {
                    return mod.module;
                } else {
                    return mod.module = {
                        id: mod.map.id,
                        uri: mod.map.url,
                        config: function config() {
                            return getOwn(_config.config, mod.map.id) || {};
                        },
                        exports: mod.exports || (mod.exports = {})
                    };
                }
            }
        };

        function cleanRegistry(id) {
            // Clean up machinery used for waiting modules.
            delete registry[id];
            delete enabledRegistry[id];
        }

        function breakCycle(mod, traced, processed) {
            var id = mod.map.id;

            if (mod.error) {
                mod.emit('error', mod.error);
            } else {
                traced[id] = true;
                each(mod.depMaps, function (depMap, i) {
                    var depId = depMap.id,
                        dep = getOwn(registry, depId);

                    // Only force things that have not completed
                    // being defined, so still in the registry,
                    // and only if it has not been matched up
                    // in the module already.
                    if (dep && !mod.depMatched[i] && !processed[depId]) {
                        if (getOwn(traced, depId)) {
                            mod.defineDep(i, _defined[depId]);
                            mod.check(); // pass false?
                        } else {
                            breakCycle(dep, traced, processed);
                        }
                    }
                });
                processed[id] = true;
            }
        }

        function checkLoaded() {
            var err = void 0,
                usingPathFallback = void 0,
                waitInterval = _config.waitSeconds * 1000,

            // It is possible to disable the wait interval by using waitSeconds of 0.
            expired = waitInterval && context.startTime + waitInterval < new Date().getTime(),
                noLoads = [],
                reqCalls = [],
                stillLoading = false,
                needCycleCheck = true;

            // Do not bother if this call was a result of a cycle break.
            if (inCheckLoaded) {
                return;
            }

            inCheckLoaded = true;

            // Figure out the state of all the modules.
            eachProp(enabledRegistry, function (mod) {
                var map = mod.map,
                    modId = map.id;

                // Skip things that are not enabled or in error state.
                if (!mod.enabled) {
                    return;
                }

                if (!map.isDefine) {
                    reqCalls.push(mod);
                }

                if (!mod.error) {
                    // If the module should be executed, and it has not
                    // been inited and time is up, remember it.
                    if (!mod.inited && expired) {
                        if (hasPathFallback(modId)) {
                            usingPathFallback = true;
                            stillLoading = true;
                        } else {
                            noLoads.push(modId);
                            removeScript(modId);
                        }
                    } else if (!mod.inited && mod.fetched && map.isDefine) {
                        stillLoading = true;
                        if (!map.prefix) {
                            // No reason to keep looking for unfinished
                            // loading. If the only stillLoading is a
                            // plugin resource though, keep going,
                            // because it may be that a plugin resource
                            // is waiting on a non-plugin cycle.
                            return needCycleCheck = false;
                        }
                    }
                }
            });

            if (expired && noLoads.length) {
                // If wait time expired, throw error of unloaded modules.
                err = makeError('timeout', 'Load timeout for modules: ' + noLoads, null, noLoads);
                err.contextName = context.contextName;
                return onError(err);
            }

            // Not expired, check for a cycle.
            if (needCycleCheck) {
                each(reqCalls, function (mod) {
                    breakCycle(mod, {}, {});
                });
            }

            // If still waiting on loads, and the waiting load is something
            // other than a plugin resource, or there are still outstanding
            // scripts, then just try back later.
            if ((!expired || usingPathFallback) && stillLoading) {
                // Something is still waiting to load. Wait for it, but only
                // if a timeout is not already in effect.
                if ((isBrowser || isWebWorker) && !checkLoadedTimeoutId) {
                    checkLoadedTimeoutId = setTimeout(function () {
                        checkLoadedTimeoutId = 0;
                        checkLoaded();
                    }, 50);
                }
            }

            inCheckLoaded = false;
        }

        Module = function Module(map) {
            this.events = getOwn(undefEvents, map.id) || {};
            this.map = map;
            this.shim = getOwn(_config.shim, map.id);
            this.depExports = [];
            this.depMaps = [];
            this.depMatched = [];
            this.pluginMaps = {};
            this.depCount = 0;

            /* this.exports this.factory
             this.depMaps = [],
             this.enabled, this.fetched
             */
        };

        Module.prototype = {
            init: function init(depMaps, factory, errback, options) {
                options = options || {};

                // Do not do more inits if already done. Can happen if there
                // are multiple define calls for the same module. That is not
                // a normal, common case, but it is also not unexpected.
                if (this.inited) {
                    return;
                }

                this.factory = factory;

                if (errback) {
                    // Register for errors on this module.
                    this.on('error', errback);
                } else if (this.events.error) {
                    // If no errback already, but there are error listeners
                    // on this module, set up an errback to pass to the deps.
                    errback = bind(this, function (err) {
                        this.emit('error', err);
                    });
                }

                // Do a copy of the dependency array, so that
                // source inputs are not modified. For example
                // "shim" deps are passed in here directly, and
                // doing a direct modification of the depMaps array
                // would affect that config.
                this.depMaps = depMaps && depMaps.slice(0);

                this.errback = errback;

                // Indicate this module has be initialized
                this.inited = true;

                this.ignore = options.ignore;

                // Could have option to init this module in enabled mode,
                // or could have been previously marked as enabled. However,
                // the dependencies are not known until init is called. So
                // if enabled previously, now trigger dependencies as enabled.
                if (options.enabled || this.enabled) {
                    // Enable this module and dependencies.
                    // Will call this.check()
                    this.enable();
                } else {
                    this.check();
                }
            },

            defineDep: function defineDep(i, depExports) {
                // Because of cycles, defined callback for a given
                // export can be called more than once.
                if (!this.depMatched[i]) {
                    this.depMatched[i] = true;
                    this.depCount -= 1;
                    this.depExports[i] = depExports;
                }
            },

            fetch: function fetch() {
                if (this.fetched) {
                    return;
                }
                this.fetched = true;

                context.startTime = new Date().getTime();

                var map = this.map;

                // If the manager is for a plugin managed resource,
                // ask the plugin to load it now.
                if (this.shim) {
                    context.makeRequire(this.map, {
                        enableBuildCallback: true
                    })(this.shim.deps || [], bind(this, function () {
                        return map.prefix ? this.callPlugin() : this.load();
                    }));
                } else {
                    // Regular dependency.
                    return map.prefix ? this.callPlugin() : this.load();
                }
            },

            load: function load() {
                var url = this.map.url;

                // Regular dependency.
                if (!urlFetched[url]) {
                    urlFetched[url] = true;
                    context.load(this.map.id, url);
                }
            },

            /**
             * Checks if the module is ready to define itself, and if so,
             * define it.
             */
            check: function check() {
                if (!this.enabled || this.enabling) {
                    return;
                }

                var err = void 0,
                    cjsModule = void 0,
                    id = this.map.id,
                    depExports = this.depExports,
                    exports = this.exports,
                    factory = this.factory;

                if (!this.inited) {
                    // Only fetch if not already in the defQueue.
                    if (!hasProp(context.defQueueMap, id)) {
                        this.fetch();
                    }
                } else if (this.error) {
                    this.emit('error', this.error);
                } else if (!this.defining) {
                    // The factory could trigger another require call
                    // that would result in checking this module to
                    // define itself again. If already in the process
                    // of doing that, skip this work.
                    this.defining = true;

                    if (this.depCount < 1 && !this.defined) {
                        if (isFunction(factory)) {
                            try {
                                exports = context.execCb(id, factory, depExports, exports);
                            } catch (e) {
                                err = e;
                            }

                            // Favor return value over exports. If node/cjs in play,
                            // then will not have a return value anyway. Favor
                            // module.exports assignment over exports object.
                            if (this.map.isDefine && exports === undefined) {
                                cjsModule = this.module;
                                if (cjsModule) {
                                    exports = cjsModule.exports;
                                } else if (this.usingExports) {
                                    // exports already set the defined value.
                                    exports = this.exports;
                                }
                            }

                            if (err) {
                                // If there is an error listener, favor passing
                                // to that instead of throwing an error. However,
                                // only do it for define()'d  modules. require
                                // errbacks should not be called for failures in
                                // their callbacks (#699). However if a global
                                // onError is set, use that.
                                if (this.events.error && this.map.isDefine || req.onError !== defaultOnError) {
                                    err.requireMap = this.map;
                                    err.requireModules = this.map.isDefine ? [this.map.id] : null;
                                    err.requireType = this.map.isDefine ? 'define' : 'require';
                                    return onError(this.error = err);
                                } else if (typeof console !== 'undefined' && console.error) {
                                    // Log the error for debugging. If promises could be
                                    // used, this would be different, but making do.
                                    console.error(err);
                                } else {
                                    // Do not want to completely lose the error. While this
                                    // will mess up processing and lead to similar results
                                    // as bug 1440, it at least surfaces the error.
                                    req.onError(err);
                                }
                            }
                        } else {
                            // Just a literal value
                            exports = factory;
                        }

                        this.exports = exports;

                        if (this.map.isDefine && !this.ignore) {
                            _defined[id] = exports;

                            if (req.onResourceLoad) {
                                var resLoadMaps = [];
                                each(this.depMaps, function (depMap) {
                                    resLoadMaps.push(depMap.normalizedMap || depMap);
                                });
                                req.onResourceLoad(context, this.map, resLoadMaps);
                            }
                        }

                        // Clean up
                        cleanRegistry(id);

                        this.defined = true;
                    }

                    // Finished the define stage. Allow calling check again
                    // to allow define notifications below in the case of a
                    // cycle.
                    this.defining = false;

                    if (this.defined && !this.defineEmitted) {
                        this.defineEmitted = true;
                        this.emit('defined', this.exports);
                        this.defineEmitComplete = true;
                    }
                }
            },

            callPlugin: function callPlugin() {
                var map = this.map;
                var id = map.id;
                // Map already normalized the prefix.
                var pluginMap = makeModuleMap(map.prefix);

                // Mark this as a dependency for this plugin, so it
                // can be traced for cycles.
                this.depMaps.push(pluginMap);

                on(pluginMap, 'defined', bind(this, function (plugin) {
                    var load = void 0,
                        normalizedMap = void 0,
                        normalizedMod = void 0,
                        bundleId = getOwn(bundlesMap, this.map.id),
                        name = this.map.name,
                        parentName = this.map.parentMap ? this.map.parentMap.name : null,
                        localRequire = context.makeRequire(map.parentMap, {
                        enableBuildCallback: true
                    });

                    // If current map is not normalized, wait for that
                    // normalized name to load instead of continuing.
                    if (this.map.unnormalized) {
                        // Normalize the ID if the plugin allows it.
                        if (plugin.normalize) {
                            name = plugin.normalize(name, function (name) {
                                return normalize(name, parentName, true);
                            }) || '';
                        }

                        // prefix and name should already be normalized, no need
                        // for applying map config again either.
                        normalizedMap = makeModuleMap(map.prefix + '!' + name, this.map.parentMap);
                        on(normalizedMap, 'defined', bind(this, function (value) {
                            this.map.normalizedMap = normalizedMap;
                            this.init([], function () {
                                return value;
                            }, null, {
                                enabled: true,
                                ignore: true
                            });
                        }));

                        normalizedMod = getOwn(registry, normalizedMap.id);
                        if (normalizedMod) {
                            // Mark this as a dependency for this plugin, so it
                            // can be traced for cycles.
                            this.depMaps.push(normalizedMap);

                            if (this.events.error) {
                                normalizedMod.on('error', bind(this, function (err) {
                                    this.emit('error', err);
                                }));
                            }
                            normalizedMod.enable();
                        }

                        return;
                    }

                    // If a paths config, then just load that file instead to
                    // resolve the plugin, as it is built into that paths layer.
                    if (bundleId) {
                        this.map.url = context.nameToUrl(bundleId);
                        this.load();
                        return;
                    }

                    load = bind(this, function (value) {
                        this.init([], function () {
                            return value;
                        }, null, {
                            enabled: true
                        });
                    });

                    load.error = bind(this, function (err) {
                        this.inited = true;
                        this.error = err;
                        err.requireModules = [id];

                        // Remove temp unnormalized modules for this module, since they will never be resolved otherwise 
                        // now.
                        eachProp(registry, function (mod) {
                            if (mod.map.id.indexOf(id + '_unnormalized') === 0) {
                                cleanRegistry(mod.map.id);
                            }
                        });

                        onError(err);
                    });

                    // Allow plugins to load other code without having to know the context or how to 'complete' the 
                    // load.
                    load.fromText = bind(this, function (text, textAlt) {
                        /*jslint evil: true */
                        var moduleName = map.name,
                            moduleMap = makeModuleMap(moduleName),
                            hasInteractive = useInteractive;

                        // As of 2.1.0, support just passing the text, to reinforce fromText only being called once per 
                        // resource. Still support old style of passing moduleName but discard that moduleName in favor 
                        // of the internal ref.
                        if (textAlt) {
                            text = textAlt;
                        }

                        // Turn off interactive script matching for IE for any define calls in the text, then turn it 
                        // back on at the end.
                        if (hasInteractive) {
                            useInteractive = false;
                        }

                        // Prime the system by creating a module instance for it.
                        getModule(moduleMap);

                        // Transfer any config to this other module.
                        if (hasProp(_config.config, id)) {
                            _config.config[moduleName] = _config.config[id];
                        }

                        try {
                            req.exec(text);
                        } catch (e) {
                            return onError(makeError('fromtexteval', 'fromText eval for ' + id + ' failed: ' + e, e, [id]));
                        }

                        if (hasInteractive) {
                            useInteractive = true;
                        }

                        // Mark this as a dependency for the plugin resource.
                        this.depMaps.push(moduleMap);

                        // Support anonymous modules.
                        context.completeLoad(moduleName);

                        // Bind the value of that module to the value for this resource ID.
                        localRequire([moduleName], load);
                    });

                    // Use parentName here since the plugin's name is not reliable, could be some weird string with no 
                    // path that actually wants to reference the parentName's path.
                    plugin.load(map.name, localRequire, load, _config);
                }));

                context.enable(pluginMap, this);
                this.pluginMaps[pluginMap.id] = pluginMap;
            },

            enable: function enable() {
                enabledRegistry[this.map.id] = this;
                this.enabled = true;

                // Set flag mentioning that the module is enabling, so that immediate calls to the defined callbacks for 
                // dependencies do not trigger inadvertent load with the depCount still being zero.
                this.enabling = true;

                // Enable each dependency.
                each(this.depMaps, bind(this, function (depMap, i) {
                    var id = void 0;
                    var mod = void 0;
                    var handler = void 0;

                    if (typeof depMap === 'string') {
                        // Dependency needs to be converted to a depMap and wired up to this module.
                        depMap = makeModuleMap(depMap, this.map.isDefine ? this.map : this.map.parentMap, false, !this.skipMap);
                        this.depMaps[i] = depMap;

                        handler = getOwn(handlers, depMap.id);

                        if (handler) {
                            this.depExports[i] = handler(this);
                            return;
                        }

                        this.depCount += 1;

                        on(depMap, 'defined', bind(this, function (depExports) {
                            if (this.undefed) {
                                return;
                            }
                            this.defineDep(i, depExports);
                            this.check();
                        }));

                        if (this.errback) {
                            on(depMap, 'error', bind(this, this.errback));
                        } else if (this.events.error) {
                            // No direct errback on this module, but something else is listening for errors, so be sure 
                            // to propagate the error correctly.
                            on(depMap, 'error', bind(this, function (err) {
                                this.emit('error', err);
                            }));
                        }
                    }

                    id = depMap.id;
                    mod = registry[id];

                    // Skip special modules like 'require', 'exports', 'module'. Also, don't call enable if it is 
                    // already enabled, important in circular dependency cases.
                    if (!hasProp(handlers, id) && mod && !mod.enabled) {
                        context.enable(depMap, this);
                    }
                }));

                // Enable each plugin that is used in  a dependency.
                eachProp(this.pluginMaps, bind(this, function (pluginMap) {
                    var mod = getOwn(registry, pluginMap.id);
                    if (mod && !mod.enabled) {
                        context.enable(pluginMap, this);
                    }
                }));

                this.enabling = false;

                this.check();
            },

            on: function on(name, cb) {
                var cbs = this.events[name];
                if (!cbs) {
                    cbs = this.events[name] = [];
                }
                cbs.push(cb);
            },

            emit: function emit(name, evt) {
                each(this.events[name], function (cb) {
                    cb(evt);
                });
                if (name === 'error') {
                    // Now that the error handler was triggered, remove the listeners, since this broken Module instance
                    // can stay around for a while in the registry.
                    delete this.events[name];
                }
            }
        };

        function callGetModule(args) {
            // Skip modules already defined.
            if (!hasProp(_defined, args[0])) {
                getModule(makeModuleMap(args[0], null, true)).init(args[1], args[2]);
            }
        }

        function removeListener(node, func, name, ieName) {
            // Favor detachEvent because of IE9 issue, see attachEvent/addEventListener comment elsewhere in this file.
            if (node.detachEvent && !isOpera) {
                // Probably IE. If not it will throw an error, which will be useful to know.
                if (ieName) {
                    node.detachEvent(ieName, func);
                }
            } else {
                node.removeEventListener(name, func, false);
            }
        }

        /**
         * Given an event from a script node, get the requirejs info from it, and then removes the event listeners on
         * the node.
         *
         * @param {Event} evt
         *
         * @return {Object}
         */
        function getScriptData(evt) {
            // Using currentTarget instead of target for Firefox 2.0's sake. Not all old browsers will be supported, but 
            // this one was easy enough to support and still makes sense.
            var node = evt.currentTarget || evt.srcElement;

            // Remove the listeners once here.
            removeListener(node, context.onScriptLoad, 'load', 'onreadystatechange');
            removeListener(node, context.onScriptError, 'error');

            return {
                node: node,
                id: node && node.getAttribute('data-requiremodule')
            };
        }

        function intakeDefines() {
            var args = void 0;

            // Any defined modules in the global queue, intake them now.
            takeGlobalQueue();

            // Make sure any remaining defQueue items get properly processed.
            while (defQueue.length) {
                args = defQueue.shift();
                if (args[0] === null) {
                    return onError(makeError('mismatch', 'Mismatched anonymous define() module: ' + args[args.length - 1]));
                } else {
                    // args are id, deps, factory. Should be normalized by the
                    // define() function.
                    callGetModule(args);
                }
            }
            context.defQueueMap = {};
        }

        context = {
            config: _config,
            contextName: contextName,
            registry: registry,
            defined: _defined,
            urlFetched: urlFetched,
            defQueue: defQueue,
            defQueueMap: {},
            Module: Module,
            makeModuleMap: makeModuleMap,
            nextTick: req.nextTick,
            onError: onError,

            /**
             * Set a configuration for the context.
             *
             * @param {Object} cfg config object to integrate.
             */
            configure: function configure(cfg) {
                // Make sure the baseUrl ends in a slash.
                if (cfg.baseUrl) {
                    if (cfg.baseUrl.charAt(cfg.baseUrl.length - 1) !== '/') {
                        cfg.baseUrl += '/';
                    }
                }

                // Save off the paths since they require special processing, they are additive.
                var shim = _config.shim,
                    objs = {
                    paths: true,
                    bundles: true,
                    config: true,
                    map: true
                };

                eachProp(cfg, function (value, prop) {
                    if (objs[prop]) {
                        if (!_config[prop]) {
                            _config[prop] = {};
                        }
                        mixin(_config[prop], value, true, true);
                    } else {
                        _config[prop] = value;
                    }
                });

                // Reverse map the bundles
                if (cfg.bundles) {
                    eachProp(cfg.bundles, function (value, prop) {
                        each(value, function (v) {
                            if (v !== prop) {
                                bundlesMap[v] = prop;
                            }
                        });
                    });
                }

                // Merge shim
                if (cfg.shim) {
                    eachProp(cfg.shim, function (value, id) {
                        // Normalize the structure
                        if (isArray(value)) {
                            value = {
                                deps: value
                            };
                        }
                        if ((value.exports || value.init) && !value.exportsFn) {
                            value.exportsFn = context.makeShimExports(value);
                        }
                        shim[id] = value;
                    });
                    _config.shim = shim;
                }

                // Adjust packages if necessary.
                if (cfg.packages) {
                    each(cfg.packages, function (pkgObj) {
                        var location = void 0,
                            name = void 0;

                        pkgObj = typeof pkgObj === 'string' ? { name: pkgObj } : pkgObj;

                        name = pkgObj.name;
                        location = pkgObj.location;
                        if (location) {
                            _config.paths[name] = pkgObj.location;
                        }

                        // Save pointer to main module ID for pkg name. Remove leading dot in main, so main paths are 
                        // normalized, and remove any trailing .js, since different package envs have different 
                        // conventions: some use a module name, some use a file name.
                        _config.pkgs[name] = pkgObj.name + '/' + (pkgObj.main || 'main').replace(currDirRegExp, '').replace(jsSuffixRegExp, '');
                    });
                }

                // If there are any "waiting to execute" modules in the registry, update the maps for them, since their 
                // info, like URLs to load, may have changed.
                eachProp(registry, function (mod, id) {
                    // If module already has init called, since it is too late to modify them, and ignore unnormalized 
                    // ones since they are transient.
                    if (!mod.inited && !mod.map.unnormalized) {
                        mod.map = makeModuleMap(id, null, true);
                    }
                });

                // If a deps array or a config callback is specified, then call require with those args. This is useful 
                // when require is defined as a config object before require.js is loaded.
                if (cfg.deps || cfg.callback) {
                    context.require(cfg.deps || [], cfg.callback);
                }
            },

            makeShimExports: function makeShimExports(value) {
                function fn() {
                    var ret = void 0;
                    if (value.init) {
                        ret = value.init.apply(global, arguments);
                    }
                    return ret || value.exports && getGlobal(value.exports);
                }

                return fn;
            },

            makeRequire: function makeRequire(relMap, options) {
                options = options || {};

                function localRequire(deps, callback, errback) {
                    var id = void 0,
                        map = void 0,
                        requireMod = void 0;

                    if (options.enableBuildCallback && callback && isFunction(callback)) {
                        callback.__requireJsBuild = true;
                    }

                    if (typeof deps === 'string') {
                        if (isFunction(callback)) {
                            // Invalid call
                            return onError(makeError('requireargs', 'Invalid require call'), errback);
                        }

                        // If require|exports|module are requested, get the value for them from the special handlers. 
                        // Caveat: this only works while module is being defined.
                        if (relMap && hasProp(handlers, deps)) {
                            return handlers[deps](registry[relMap.id]);
                        }

                        // Synchronous access to one module. If require.get is available (as in the Node adapter), 
                        // prefer that.
                        if (req.get) {
                            return req.get(context, deps, relMap, localRequire);
                        }

                        // Normalize module name, if it contains . or ..
                        map = makeModuleMap(deps, relMap, false, true);
                        id = map.id;

                        if (!hasProp(_defined, id)) {
                            return onError(makeError('notloaded', 'Module name "' + id + '" has not been loaded yet for context: ' + contextName + (relMap ? '' : '. Use require([])')));
                        }
                        return _defined[id];
                    }

                    // Grab defines waiting in the global queue.
                    intakeDefines();

                    // Mark all the dependencies as needing to be loaded.
                    context.nextTick(function () {
                        // Some defines could have been added since the
                        // require call, collect them.
                        intakeDefines();

                        requireMod = getModule(makeModuleMap(null, relMap));

                        // Store if map config should be applied to this require
                        // call for dependencies.
                        requireMod.skipMap = options.skipMap;

                        requireMod.init(deps, callback, errback, {
                            enabled: true
                        });

                        checkLoaded();
                    });

                    return localRequire;
                }

                mixin(localRequire, {
                    isBrowser: isBrowser,

                    /**
                     * Converts a module name + .extension into an URL path.
                     *
                     * *Requires* the use of a module name. It does not support using plain URLs like nameToUrl.
                     */
                    toUrl: function toUrl(moduleNamePlusExt) {
                        var ext = void 0,
                            index = moduleNamePlusExt.lastIndexOf('.'),
                            segment = moduleNamePlusExt.split('/')[0],
                            isRelative = segment === '.' || segment === '..';

                        // Have a file extension alias, and it is not the
                        // dots from a relative path.
                        if (index !== -1 && (!isRelative || index > 1)) {
                            ext = moduleNamePlusExt.substring(index, moduleNamePlusExt.length);
                            moduleNamePlusExt = moduleNamePlusExt.substring(0, index);
                        }

                        return context.nameToUrl(normalize(moduleNamePlusExt, relMap && relMap.id, true), ext, true);
                    },

                    defined: function defined(id) {
                        return hasProp(_defined, makeModuleMap(id, relMap, false, true).id);
                    },

                    specified: function specified(id) {
                        id = makeModuleMap(id, relMap, false, true).id;
                        return hasProp(_defined, id) || hasProp(registry, id);
                    }
                });

                // Only allow undef on top level require calls.
                if (!relMap) {
                    localRequire.undef = function (id) {
                        // Bind any waiting define() calls to this context, fix for #408.
                        takeGlobalQueue();

                        var map = makeModuleMap(id, relMap, true);
                        var mod = getOwn(registry, id);

                        mod.undefed = true;
                        removeScript(id);

                        delete _defined[id];
                        delete urlFetched[map.url];
                        delete undefEvents[id];

                        // Clean queued defines too. Go backwards in array so that the splices do not mess up the 
                        // iteration.
                        eachReverse(defQueue, function (args, i) {
                            if (args[0] === id) {
                                defQueue.splice(i, 1);
                            }
                        });
                        delete context.defQueueMap[id];

                        if (mod) {
                            // Hold on to listeners in case the module will be attempted to be reloaded using a 
                            // different config.
                            if (mod.events.defined) {
                                undefEvents[id] = mod.events;
                            }

                            cleanRegistry(id);
                        }
                    };
                }

                return localRequire;
            },

            /**
             * Called to enable a module if it is still in the registry awaiting enablement. A second arg, parent, the
             * parent module, is passed in for context, when this method is overridden by the optimizer. Not shown here
             * to keep code compact.
             */
            enable: function enable(depMap) {
                var mod = getOwn(registry, depMap.id);
                if (mod) {
                    getModule(depMap).enable();
                }
            },

            /**
             * Internal method used by environment adapters to complete a load event.
             *
             * A load event could be a script load or just a load pass from a synchronous load call.
             *
             * @param {String} moduleName The name of the module to potentially complete.
             */
            completeLoad: function completeLoad(moduleName) {
                var found = void 0;
                var args = void 0;
                var mod = void 0;
                var shim = getOwn(_config.shim, moduleName) || {};
                var shExports = shim.exports;

                takeGlobalQueue();

                while (defQueue.length) {
                    args = defQueue.shift();
                    if (args[0] === null) {
                        args[0] = moduleName;
                        // If already found an anonymous module and bound it to this name, then this is some other anon 
                        // module waiting for its completeLoad to fire.
                        if (found) {
                            break;
                        }
                        found = true;
                    } else if (args[0] === moduleName) {
                        // Found matching define call for this script!
                        found = true;
                    }

                    callGetModule(args);
                }
                context.defQueueMap = {};

                // Do this after the cycle of callGetModule in case the result
                // of those calls/init calls changes the registry.
                mod = getOwn(registry, moduleName);

                if (!found && !hasProp(_defined, moduleName) && mod && !mod.inited) {
                    if (_config.enforceDefine && (!shExports || !getGlobal(shExports))) {
                        if (hasPathFallback(moduleName)) {
                            return;
                        } else {
                            return onError(makeError('nodefine', 'No define call for ' + moduleName, null, [moduleName]));
                        }
                    } else {
                        // A script that does not call define(), so just simulate the call for it.
                        callGetModule([moduleName, shim.deps || [], shim.exportsFn]);
                    }
                }

                checkLoaded();
            },

            /**
             * Converts a module name to a file path.
             *
             * Supports cases where moduleName may actually be just an URL.
             *
             * Note that it **does not** call normalize on the moduleName, it is assumed to have already been
             * normalized. This is an internal API, not a public one. Use toUrl for the public API.
             */
            nameToUrl: function nameToUrl(moduleName, ext, skipExt) {
                var paths = void 0;
                var syms = void 0;
                var i = void 0;
                var parentModule = void 0;
                var url = void 0;
                var parentPath = void 0,
                    bundleId = void 0;
                var pkgMain = getOwn(_config.pkgs, moduleName);

                if (pkgMain) {
                    moduleName = pkgMain;
                }

                bundleId = getOwn(bundlesMap, moduleName);

                if (bundleId) {
                    return context.nameToUrl(bundleId, ext, skipExt);
                }

                // If a colon is in the URL, it indicates a protocol is used and it is just an URL to a file, or if it 
                // starts with a slash, contains a query arg (i.e. ?) or ends with .js, then assume the user meant to 
                // use an url and not a module id. The slash is important for protocol-less URLs as well as full paths.
                if (req.jsExtRegExp.test(moduleName)) {
                    // Just a plain path, not module name lookup, so just return it. Add extension if it is included. 
                    // This is a bit wonky, only non-.js things pass an extension, this method probably needs to be 
                    // reworked.
                    url = moduleName + (ext || '');
                } else {
                    // A module that needs to be converted to a path.
                    paths = _config.paths;

                    syms = moduleName.split('/');
                    // For each module name segment, see if there is a path registered for it. Start with most specific 
                    // name and work up from it.
                    for (i = syms.length; i > 0; i -= 1) {
                        parentModule = syms.slice(0, i).join('/');

                        parentPath = getOwn(paths, parentModule);
                        if (parentPath) {
                            // If an array, it means there are a few choices, Choose the one that is desired.
                            if (isArray(parentPath)) {
                                parentPath = parentPath[0];
                            }
                            syms.splice(0, i, parentPath);
                            break;
                        }
                    }

                    // Join the path parts together, then figure out if baseUrl is needed.
                    url = syms.join('/');
                    url += ext || (/^data\:|\?/.test(url) || skipExt ? '' : '.js');
                    url = (url.charAt(0) === '/' || url.match(/^[\w\+\.\-]+:/) ? '' : _config.baseUrl) + url;
                }

                return _config.urlArgs ? url + ((url.indexOf('?') === -1 ? '?' : '&') + _config.urlArgs) : url;
            },

            // Delegates to req.load. Broken out as a separate function to allow overriding in the optimizer.
            load: function load(id, url) {
                req.load(context, id, url);
            },

            /**
             * Executes a module callback function.
             *
             * Broken out as a separate function solely to allow the build system to sequence the files in the built
             * layer in the right sequence.
             *
             * @private
             */
            execCb: function execCb(name, callback, args, exports) {
                return callback.apply(exports, args);
            },

            /**
             * Callback for script loads, used to check status of loading.
             *
             * @param {Event} evt the event from the browser for the script that was loaded.
             */
            onScriptLoad: function onScriptLoad(evt) {
                // Using currentTarget instead of target for Firefox 2.0's sake. Not
                // all old browsers will be supported, but this one was easy enough
                // to support and still makes sense.
                if (evt.type === 'load' || readyRegExp.test((evt.currentTarget || evt.srcElement).readyState)) {
                    // Reset interactive script so a script node is not held onto for
                    // to long.
                    interactiveScript = null;

                    // Pull out the name of the module and the context.
                    var data = getScriptData(evt);
                    context.completeLoad(data.id);
                }
            },

            /**
             * Callback for script errors.
             */
            onScriptError: function onScriptError(evt) {
                var data = getScriptData(evt);
                if (!hasPathFallback(data.id)) {
                    var parents = [];
                    eachProp(registry, function (value, key) {
                        if (key.indexOf('_@r') !== 0) {
                            each(value.depMaps, function (depMap) {
                                if (depMap.id === data.id) {
                                    parents.push(key);
                                }
                                return true;
                            });
                        }
                    });
                    return onError(makeError('scripterror', 'Script error for "' + data.id + (parents.length ? '", needed by: ' + parents.join(', ') : '"'), evt, [data.id]));
                }
            }
        };

        context.require = context.makeRequire();
        return context;
    }

    /**
     * Main entry point.
     *
     * If the only argument to require is a string, then the module that is represented by that string is fetched for
     * the appropriate context.
     *
     * If the first argument is an array, then it will be treated as an array of dependency string names to fetch. An
     * optional function callback can be specified to execute when all of those dependencies are available.
     *
     * Make a local req variable to help Caja compliance (it assumes things on a require that are not standardized),
     * and to give a short name for minification/local scope use.
     */
    req = window.requirejs = function (deps, callback, errback, optional) {
        // Find the right context, use default
        var context = void 0;
        var config = void 0;
        var contextName = defContextName;

        // Determine if have config object in the call.
        if (!isArray(deps) && typeof deps !== 'string') {
            // deps is a config object
            config = deps;
            if (isArray(callback)) {
                // Adjust args if there are dependencies
                deps = callback;
                callback = errback;
                errback = optional;
            } else {
                deps = [];
            }
        }

        if (config && config.context) {
            contextName = config.context;
        }

        context = getOwn(contexts, contextName);
        if (!context) {
            context = contexts[contextName] = req.s.newContext(contextName);
        }

        if (config) {
            context.configure(config);
        }

        return context.require(deps, callback, errback);
    };

    /**
     * Support require.config() to make it easier to cooperate with other AMD loaders on globally agreed names.
     */
    req.config = function (config) {
        return req(config);
    };

    /**
     * Execute something after the current tick of the event loop.
     *
     * Override for other envs that have a better solution than setTimeout.
     *
     * @param  {Function} fn function to execute later.
     */
    req.nextTick = typeof setTimeout !== 'undefined' ? function (fn) {
        setTimeout(fn, 4);
    } : function (fn) {
        fn();
    };

    /**
     * Export require as a global, but only if it does not already exist.
     */
    if (!window.require) {
        window.require = req;
    }

    req.version = version;

    // Used to filter out dependencies that are already paths.
    req.jsExtRegExp = /^\/|:|\?|\.js$/;
    req.isBrowser = isBrowser;
    s = req.s = {
        contexts: contexts,
        newContext: newContext
    };

    // Create default context.
    req({});

    // Exports some context-sensitive methods on global require.
    each(['toUrl', 'undef', 'defined', 'specified'], function (prop) {
        //  Reference from contexts instead of early binding to default context,
        // so that during builds, the latest instance of the default context
        // with its config gets used.
        req[prop] = function () {
            var ctx = contexts[defContextName];
            return ctx.require[prop].apply(ctx, arguments);
        };
    });

    if (isBrowser) {
        head = s.head = document.getElementsByTagName('head')[0];
        // If BASE tag is in play, using appendChild is a problem for IE6.
        // When that browser dies, this can be removed. Details in this jQuery bug:
        // http://dev.jquery.com/ticket/2709
        baseElement = document.getElementsByTagName('base')[0];
        if (baseElement) {
            head = s.head = baseElement.parentNode;
        }
    }

    /**
     * Any errors that require explicitly generates will be passed to this function.
     *
     * Intercept/override it if you want custom error handling.
     *
     * @param {Error} err the error object.
     */
    req.onError = defaultOnError;

    /**
     * Creates the node for the load command. Only used in browser envs.
     */
    req.createNode = function (config, moduleName, url) {
        var node = config.xhtml ? document.createElementNS('http://www.w3.org/1999/xhtml', 'html:script') : document.createElement('script');
        node.type = config.scriptType || 'text/javascript';
        node.charset = 'utf-8';
        node.async = true;
        return node;
    };

    /**
     * Does the request to load a module for the browser case.
     *
     * Make this a separate function to allow other environments to override it.
     *
     * @param {Object} context the require context to find state.
     * @param {String} moduleName the name of the module.
     * @param {Object} url the URL to the module.
     */
    req.load = function (context, moduleName, url) {
        var config = context && context.config || {};
        var node = void 0;

        if (isBrowser) {
            // In the browser so use a script tag
            node = req.createNode(config, moduleName, url);
            if (config.onNodeCreated) {
                config.onNodeCreated(node, config, moduleName, url);
            }

            node.setAttribute('data-requirecontext', context.contextName);
            node.setAttribute('data-requiremodule', moduleName);

            // Set up load listener. Test attachEvent first because IE9 has a subtle issue in its addEventListener and 
            // script onload firings that do not match the behavior of all other browsers with addEventListener support, 
            // which fire the onload event for a script right after the script execution. See:
            // https://connect.microsoft.com/IE/feedback/details/648057/script-onload-event-is-not-fired-immediately-after-script-execution
            // UNFORTUNATELY Opera implements attachEvent but does not follow the script script execution mode.
            if (node.attachEvent &&
            // Check if node.attachEvent is artificially added by custom script or natively supported by browser
            // read https://github.com/jrburke/requirejs/issues/187
            // if we can NOT find [native code] then it must NOT natively supported. in IE8, node.attachEvent does 
            // not have toString(). Note the test for "[native code" with no closing brace, see:
            // https://github.com/jrburke/requirejs/issues/273
            !(node.attachEvent.toString && node.attachEvent.toString().indexOf('[native code') < 0) && !isOpera) {
                // Probably IE. IE (at least 6-8) do not fire script onload right after executing the script, so
                // we cannot tie the anonymous define call to a name. However, IE reports the script as being in 
                // 'interactive' readyState at the time of the define call.
                useInteractive = true;

                node.attachEvent('onreadystatechange', context.onScriptLoad);
                // It would be great to add an error handler here to catch 404s in IE9+. However, onreadystatechange 
                // will fire before the error handler, so that does not help. If addEventListener is used, then IE will 
                // fire error before load, but we cannot use that pathway given the connect.microsoft.com issue 
                // mentioned above about not doing the 'script execute, then fire the script load event listener before 
                // execute next script' that other browsers do. Best hope: IE10 fixes the issues, and then destroys all 
                // installs of IE 6-9.
                // node.attachEvent('onerror', context.onScriptError);
            } else {
                node.addEventListener('load', context.onScriptLoad, false);
                node.addEventListener('error', context.onScriptError, false);
            }
            node.src = url;

            // For some cache cases in IE 6-8, the script executes before the end of the appendChild execution, so to 
            // tie an anonymous define call to the module name (which is stored on the node), hold on to a reference to 
            // this node, but clear after the DOM insertion.
            if (baseElement) {
                head.insertBefore(node, baseElement);
            } else {
                head.appendChild(node);
            }

            return node;
        } else if (isWebWorker) {
            try {
                // In a web worker, use importScripts. This is not a very
                // efficient use of importScripts, importScripts will block until
                // its script is downloaded and evaluated. However, if web workers
                // are in play, the expectation is that a build has been done so
                // that only one script needs to be loaded anyway. This may need
                // to be reevaluated if other use cases become common.
                importScripts(url);

                // Account for anonymous modules
                context.completeLoad(moduleName);
            } catch (e) {
                context.onError(makeError('importscripts', 'importScripts failed for ' + moduleName + ' at ' + url, e, [moduleName]));
            }
        }
    };

    // Look for a data-main script attribute, which could also adjust the baseUrl.
    if (isBrowser && !cfg.skipDataMain) {
        // Figure out baseUrl. Get it from the script tag with require.js in it.
        eachReverse(scripts(), function (script) {
            // Set the 'head' where we can append children by
            // using the script's parent.
            if (!head) {
                head = script.parentNode;
            }

            // Look for a data-main attribute to set main script for the page
            // to load. If it is there, the path to data main becomes the
            // baseUrl, if it is not already set.
            dataMain = script.getAttribute('data-main');
            if (dataMain) {
                // Preserve dataMain in case it is a path (i.e. contains '?')
                mainScript = dataMain;

                // Set final baseUrl if there is not already an explicit one.
                if (!cfg.baseUrl) {
                    // Pull off the directory of data-main for use as the
                    // baseUrl.
                    src = mainScript.split('/');
                    mainScript = src.pop();
                    subPath = src.length ? src.join('/') + '/' : './';

                    cfg.baseUrl = subPath;
                }

                // Strip off any trailing .js since mainScript is now
                // like a module name.
                mainScript = mainScript.replace(jsSuffixRegExp, '');

                // If mainScript is still a path, fall back to dataMain
                if (req.jsExtRegExp.test(mainScript)) {
                    mainScript = dataMain;
                }

                // Put the data-main script in the files to load.
                cfg.deps = cfg.deps ? cfg.deps.concat(mainScript) : [mainScript];

                return true;
            }
        });
    }

    /**
     * Executes the text. Normally just uses eval, but can be modified to use a better, environment-specific call.
     * Only used for transpiling loader plugins, not for plain JS modules.
     *
     * @param {String} text The text to execute/evaluate.
     */
    req.exec = function (text) {
        /*jslint evil: true */
        return eval(text);
    };

    // Set up with config info.
    req(cfg);
})();

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{}],17:[function(require,module,exports){
'use strict';

/* --------------------------------------------------------------
 vue.js 2018-06-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.core.vue = jse.core.vue || {};

/**
 * JSE Vue Integration
 *
 * Initializes the Vue application for the current page.
 *
 * Vue support is activated if the global JSEngineConfiguration object contains sets the requested values for
 * the integration.
 *
 * ```
 * window.JSEngineConfiguration = {
 *	 environment: 'development',
 *	 appUrl: 'http://example.org',
 *	 appVersion: 'v1.0.0',
 *	 translations: {},
 *	 languageCode: 'en',
 *	 pageToken: 'csrf-protection-token',
 *	 cacheToken: 'cache-busting-token' ,
 *	 vue: {
 *	 	el: '.vue-instance'
 *	 }, 
 *	 collections: [
 *	 	{name: 'controllers', attribute: 'controller'},
 *	 	{name: 'extensions', attribute: 'extension'},
 *	 	{name: 'widgets', attribute: 'widget'},
 *	 	{name: 'compatibility', attribute: 'compatibility'}
 *	 ],
 *	 registry: {} 
 * };
 * ```
 *
 * @module JSE/Core/polyfills
 */
(function (exports) {

    'use strict';

    /**
     * Holds vue instances
     *
     * @type {Object}
     */

    var instances = {};

    /**
     * Holds grouped components.
     *
     * Grouped components are being used for assignments between a parent and child Vue components.
     *
     * @type {Object}
     */
    var components = {};

    /**
     * Holds grouped child components.
     *
     * Grouped components are being used for assignments between a parent and child Vue components.
     *
     * @type {Object}
     */
    var childComponents = {};

    /**
     * Initialize the Vue application once the JS Engine has finished loading all the modules.
     */
    document.addEventListener('JSENGINE_INIT_FINISHED', function () {
        var globalConfig = jse.core.config.get('vue');

        // Check for Vue JSE configuration. 
        if (!globalConfig) {
            return;
        }

        var roots = document.querySelectorAll(globalConfig.el);

        Array.from(roots).forEach(function (root, index) {
            var name = root.getAttribute('data-vue-instance-name') || 'vue-instance-' + index;
            var config = Object.assign({}, globalConfig, { el: root });
            jse.core.vue.create(name, config);
        });
    });

    /**
     * Register a JS Engine vue module.
     *
     * @param {Object} module
     */
    exports.registerModule = function (module) {
        if (!module || !module.vue) {
            return;
        }

        if (!module.parent) {
            jse.core.vue.registerComponent(module.name, module);
        } else {
            jse.core.vue.registerChildComponent(module.parent, module.name, module);
        }
    };

    /**
     * Register a child component.
     *
     * @param {String} parent Parent component reference.
     * @param {String} name Name component reference.
     * @param {Object} childComponent Child component definition.
     */
    exports.registerChildComponent = function (parent, name, childComponent) {
        childComponents[parent] = childComponents[parent] || {};

        if (childComponents[parent][name]) {
            return;
        }

        childComponents[parent][name] = childComponent;
    };

    /**
     * Register a component.
     *
     * @param {String} name Name component reference.
     * @param {Object} component Component definition.
     */
    exports.registerComponent = function (name, component) {
        if (components[name]) {
            return;
        }

        components[name] = component;
    };

    /**
     * Create a new Vue instance.
     *
     * @param {String} name Unique name for instance.
     * @param {Object} config Vue configuration for the new instance.
     */
    exports.create = function (name, config) {
        if (instances[name]) {
            throw new Error('Instance with name ' + name + ' already exists, please use a different name.');
        }

        for (var _name in components) {
            components[_name].components = childComponents[_name] || {};

            Vue.component(_name, components[_name]);
        }

        instances[name] = new Vue(config);
    };

    /**
     * Destroy a vue instance.
     *
     * @param {String} name Instance name to be destroyed.
     */
    exports.destroy = function (name) {
        if (!instances[name]) {
            throw new Error('Instance with name ' + name + ' does not exist, make sure the instance was initialized correctly.');
        }

        instances[name].$destroy();

        delete instances[name];
    };

    /**
     * Get all active instances.
     *
     * @return {Object} Name - instance pairs.
     */
    exports.instances = function () {
        return instances;
    };
})(jse.core.vue);

},{}]},{},[12])

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJjb25zdHJ1Y3RvcnMvY29sbGVjdGlvbi5qcyIsImNvbnN0cnVjdG9ycy9kYXRhX2JpbmRpbmcuanMiLCJjb25zdHJ1Y3RvcnMvbW9kdWxlLmpzIiwiY29uc3RydWN0b3JzL25hbWVzcGFjZS5qcyIsImNvcmUvYWJvdXQuanMiLCJjb3JlL2NvbmZpZy5qcyIsImNvcmUvZGVidWcuanMiLCJjb3JlL2VuZ2luZS5qcyIsImNvcmUvZXh0ZW5kLmpzIiwiY29yZS9pbml0aWFsaXplLmpzIiwiY29yZS9sYW5nLmpzIiwiY29yZS9tYWluLmpzIiwiY29yZS9tb2R1bGVfbG9hZGVyLmpzIiwiY29yZS9wb2x5ZmlsbHMuanMiLCJjb3JlL3JlZ2lzdHJ5LmpzIiwiY29yZS9yZXF1aXJlLmpzIiwiY29yZS92dWUuanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7Ozs7Ozs7QUNBQTs7Ozs7Ozs7OztBQVVBLENBQUMsWUFBWTs7QUFFVDs7QUFFQTs7Ozs7Ozs7QUFKUyxRQVdILFVBWEc7QUFZTDs7Ozs7OztBQU9BLDRCQUFZLElBQVosRUFBa0IsU0FBbEIsRUFBNkIsU0FBN0IsRUFBd0M7QUFBQTs7QUFDcEMsaUJBQUssSUFBTCxHQUFZLElBQVo7QUFDQSxpQkFBSyxTQUFMLEdBQWlCLFNBQWpCO0FBQ0EsaUJBQUssU0FBTCxHQUFpQixTQUFqQjtBQUNBLGlCQUFLLEtBQUwsR0FBYTtBQUNULHlCQUFTLEVBREE7QUFFVCxzQkFBTTtBQUZHLGFBQWI7QUFJSDs7QUFFRDs7Ozs7Ozs7Ozs7Ozs7QUE3Qks7QUFBQTtBQUFBLG1DQXlDRSxJQXpDRixFQXlDUSxZQXpDUixFQXlDc0IsSUF6Q3RCLEVBeUM0QjtBQUM3QjtBQUNBLG9CQUFJLENBQUMsSUFBRCxJQUFTLE9BQU8sSUFBUCxLQUFnQixRQUF6QixJQUFxQyxPQUFPLElBQVAsS0FBZ0IsVUFBekQsRUFBcUU7QUFDakUsd0JBQUksSUFBSixD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QUFDSCxLQUZEO0FBR0gsQ0E1SUQsRUE0SUcsSUFBSSxJQUFKLENBQVMsR0E1SVoiLCJmaWxlIjoianNlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uKCl7ZnVuY3Rpb24gcihlLG4sdCl7ZnVuY3Rpb24gbyhpLGYpe2lmKCFuW2ldKXtpZighZVtpXSl7dmFyIGM9XCJmdW5jdGlvblwiPT10eXBlb2YgcmVxdWlyZSYmcmVxdWlyZTtpZighZiYmYylyZXR1cm4gYyhpLCEwKTtpZih1KXJldHVybiB1KGksITApO3ZhciBhPW5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnXCIraStcIidcIik7dGhyb3cgYS5jb2RlPVwiTU9EVUxFX05PVF9GT1VORFwiLGF9dmFyIHA9bltpXT17ZXhwb3J0czp7fX07ZVtpXVswXS5jYWxsKHAuZXhwb3J0cyxmdW5jdGlvbihyKXt2YXIgbj1lW2ldWzFdW3JdO3JldHVybiBvKG58fHIpfSxwLHAuZXhwb3J0cyxyLGUsbix0KX1yZXR1cm4gbltpXS5leHBvcnRzfWZvcih2YXIgdT1cImZ1bmN0aW9uXCI9PXR5cGVvZiByZXF1aXJlJiZyZXF1aXJlLGk9MDtpPHQubGVuZ3RoO2krKylvKHRbaV0pO3JldHVybiBvfXJldHVybiByfSkoKSIsIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gY29sbGVjdGlvbi5qcyAyMDE2LTA2LTIyXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuKGZ1bmN0aW9uICgpIHtcblxuICAgICd1c2Ugc3RyaWN0JztcblxuICAgIC8qKlxuICAgICAqIENsYXNzIENvbGxlY3Rpb25cbiAgICAgKlxuICAgICAqIFRoaXMgY2xhc3MgaXMgdXNlZCB0byBoYW5kbGUgbXVsdGlwbGUgbW9kdWxlcyBvZiB0aGUgc2FtZSB0eXBlIChjb250cm9sbGVycywgZXh0ZW5zaW9ucyAuLi4pLlxuICAgICAqXG4gICAgICogQGNsYXNzIEpTRS9Db25zdHJ1Y3RvcnMvQ29sbGVjdGlvblxuICAgICAqL1xuICAgIGNsYXNzIENvbGxlY3Rpb24ge1xuICAgICAgICAvKipcbiAgICAgICAgICogQ2xhc3MgQ29uc3RydWN0b3JcbiAgICAgICAgICpcbiAgICAgICAgICogQHBhcmFtIHtTdHJpbmd9IG5hbWUgVGhlIGNvbGxlY3Rpb24gbmFtZSAtIG11c3QgYmUgdW5pcXVlLlxuICAgICAgICAgKiBAcGFyYW0ge1N0cmluZ30gYXR0cmlidXRlIFRoZSBhdHRyaWJ1dGUgdGhhdCB3aWxsIHRyaWdnZXIgY29sbGVjdGlvbidzIG1vZHVsZXMuXG4gICAgICAgICAqIEBwYXJhbSB7T2JqZWN0fSBuYW1lc3BhY2UgT3B0aW9uYWwsIHRoZSBuYW1lc3BhY2UgaW5zdGFuY2Ugd2hlcmUgdGhlIGNvbGxlY3Rpb24gYmVsb25ncy5cbiAgICAgICAgICovXG4gICAgICAgIGNvbnN0cnVjdG9yKG5hbWUsIGF0dHJpYnV0ZSwgbmFtZXNwYWNlKSB7XG4gICAgICAgICAgICB0aGlzLm5hbWUgPSBuYW1lO1xuICAgICAgICAgICAgdGhpcy5hdHRyaWJ1dGUgPSBhdHRyaWJ1dGU7XG4gICAgICAgICAgICB0aGlzLm5hbWVzcGFjZSA9IG5hbWVzcGFjZTtcbiAgICAgICAgICAgIHRoaXMuY2FjaGUgPSB7XG4gICAgICAgICAgICAgICAgbW9kdWxlczoge30sXG4gICAgICAgICAgICAgICAgZGF0YToge31cbiAgICAgICAgICAgIH07XG4gICAgICAgIH1cblxuICAgICAgICAvKipcbiAgICAgICAgICogRGVmaW5lIGEgbmV3IGVuZ2luZSBtb2R1bGUuXG4gICAgICAgICAqXG4gICAgICAgICAqIFRoaXMgZnVuY3Rpb24gd2lsbCBkZWZpbmUgYSBuZXcgbW9kdWxlIGludG8gdGhlIGVuZ2luZS4gRWFjaCBtb2R1bGUgd2lsbCBiZSBzdG9yZWQgaW4gdGhlXG4gICAgICAgICAqIGNvbGxlY3Rpb24ncyBjYWNoZSB0byBwcmV2ZW50IHVubmVjZXNzYXJ5IGZpbGUgdHJhbnNmZXJzLiBUaGUgc2FtZSBoYXBwZW5zIHdpdGggdGhlIGRlZmF1bHRcbiAgICAgICAgICogY29uZmlndXJhdGlvbiB0aGF0IGFwcGVuZCB0byB0aGUgbW9kdWxlIGRlZmluaXRpb24uXG4gICAgICAgICAqXG4gICAgICAgICAqIEBwYXJhbSB7U3RyaW5nfSBuYW1lIE5hbWUgb2YgdGhlIG1vZHVsZSAoc2FtZSBhcyB0aGUgZmlsZW5hbWUpLlxuICAgICAgICAgKiBAcGFyYW0ge0FycmF5fSBkZXBlbmRlbmNpZXMgQXJyYXkgb2YgbGlicmFyaWVzIHRoYXQgdGhpcyBtb2R1bGUgZGVwZW5kcyBvbiAod2lsbCBiZSBsb2FkZWQgYXN5bmNocm9ub3VzbHkpLlxuICAgICAgICAgKiBBcHBseSBvbmx5IGZpbGVuYW1lcyB3aXRob3V0IGV4dGVuc2lvbiBlLmcuIFtcImVtYWlsc1wiXS5cbiAgICAgICAgICogQHBhcmFtIHtPYmplY3R9IGNvZGUgQ29udGFpbnMgdGhlIG1vZHVsZSBjb2RlIChmdW5jdGlvbikuXG4gICAgICAgICAqL1xuICAgICAgICBtb2R1bGUobmFtZSwgZGVwZW5kZW5jaWVzLCBjb2RlKSB7XG4gICAgICAgICAgICAvLyBDaGVjayBpZiByZXF1aXJlZCB2YWx1ZXMgYXJlIGF2YWlsYWJsZSBhbmQgb2YgY29ycmVjdCB0eXBlLlxuICAgICAgICAgICAgaWYgKCFuYW1lIHx8IHR5cGVvZiBuYW1lICE9PSAnc3RyaW5nJyB8fCB0eXBlb2YgY29kZSAhPT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICAgICAgICAgIGpzZS5jb3JlLmRlYnVnLndhcm4oJ1JlZ2lzdHJhdGlvbiBvZiB0aGUgbW9kdWxlIGZhaWxlZCwgZHVlIHRvIGJhZCBmdW5jdGlvbiBjYWxsJywgYXJndW1lbnRzKTtcbiAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vIENoZWNrIGlmIHRoZSBtb2R1bGUgaXMgYWxyZWFkeSBkZWZpbmVkLlxuICAgICAgICAgICAgaWYgKHRoaXMuY2FjaGUubW9kdWxlc1tuYW1lXSkge1xuICAgICAgICAgICAgICAgIGpzZS5jb3JlLmRlYnVnLndhcm4oJ1JlZ2lzdHJhdGlvbiBvZiBtb2R1bGUgXCInICsgbmFtZSArICdcIiBza2lwcGVkLCBiZWNhdXNlIGl0IGFscmVhZHkgZXhpc3RzLicpO1xuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy8gU3RvcmUgdGhlIG1vZHVsZSB0byBjYWNoZSBzbyB0aGF0IGl0IGNhbiBiZSB1c2VkIGxhdGVyLlxuICAgICAgICAgICAgdGhpcy5jYWNoZS5tb2R1bGVzW25hbWVdID0ge1xuICAgICAgICAgICAgICAgIGNvZGU6IGNvZGUsXG4gICAgICAgICAgICAgICAgZGVwZW5kZW5jaWVzOiBkZXBlbmRlbmNpZXNcbiAgICAgICAgICAgIH07XG4gICAgICAgIH1cblxuICAgICAgICAvKipcbiAgICAgICAgICogSW5pdGlhbGl6ZSBNb2R1bGUgQ29sbGVjdGlvblxuICAgICAgICAgKlxuICAgICAgICAgKiBUaGlzIG1ldGhvZCB3aWxsIHRyaWdnZXIgdGhlIHBhZ2UgbW9kdWxlcyBpbml0aWFsaXphdGlvbi4gSXQgd2lsbCBzZWFyY2ggYWxsXG4gICAgICAgICAqIHRoZSBET00gZm9yIHRoZSBcImRhdGEtZ3gtZXh0ZW5zaW9uXCIsIFwiZGF0YS1neC1jb250cm9sbGVyXCIgb3JcbiAgICAgICAgICogXCJkYXRhLWd4LXdpZGdldFwiIGF0dHJpYnV0ZXMgYW5kIGxvYWQgdGhlIHJlbGV2YW50IHNjcmlwdHMgdGhyb3VnaCBSZXF1aXJlSlMuXG4gICAgICAgICAqXG4gICAgICAgICAqIEBwYXJhbSB7alF1ZXJ5fSAkcGFyZW50IE9wdGlvbmFsIChudWxsKSwgcGFyZW50IGVsZW1lbnQgd2lsbCBiZSB1c2VkIHRvIHNlYXJjaCBmb3IgdGhlIHJlcXVpcmVkIG1vZHVsZXMuXG4gICAgICAgICAqXG4gICAgICAgICAqIEByZXR1cm4ge2pRdWVyeS5EZWZlcnJlZH0gbmFtZXNwYWNlRGVmZXJyZWQgRGVmZXJyZWQgb2JqZWN0IHRoYXQgZ2V0cyBwcm9jZXNzZWQgYWZ0ZXIgdGhlXG4gICAgICAgICAqIG1vZHVsZSBpbml0aWFsaXphdGlvbiBpcyBmaW5pc2hlZC5cbiAgICAgICAgICovXG4gICAgICAgIGluaXQoJHBhcmVudCA9IG51bGwpIHtcbiAgICAgICAgICAgIC8vIFN0b3JlIHRoZSBuYW1lc3BhY2VzIHJlZmVyZW5jZSBvZiB0aGUgY29sbGVjdGlvbi5cbiAgICAgICAgICAgIGlmICghdGhpcy5uYW1lc3BhY2UpIHtcbiAgICAgICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoJ0NvbGxlY3Rpb24gY2Fubm90IGJlIGluaXRpYWxpemVkIHdpdGhvdXQgaXRzIHBhcmVudCBuYW1lc3BhY2UgaW5zdGFuY2UuJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vIFNldCB0aGUgZGVmYXVsdCBwYXJlbnQtb2JqZWN0IGlmIG5vbmUgd2FzIGdpdmVuLlxuICAgICAgICAgICAgaWYgKCRwYXJlbnQgPT09IHVuZGVmaW5lZCB8fCAkcGFyZW50ID09PSBudWxsKSB7XG4gICAgICAgICAgICAgICAgJHBhcmVudCA9ICQoJ2h0bWwnKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgY29uc3QgYXR0cmlidXRlID0gJ2RhdGEtJyArIHRoaXMubmFtZXNwYWNlLm5hbWUgKyAnLScgKyB0aGlzLmF0dHJpYnV0ZTtcbiAgICAgICAgICAgIGNvbnN0IG5hbWVzcGFjZURlZmVycmVkID0gJC5EZWZlcnJlZCgpO1xuICAgICAgICAgICAgY29uc3QgZGVmZXJyZWRDb2xsZWN0aW9uID0gW107XG5cbiAgICAgICAgICAgICRwYXJlbnRcbiAgICAgICAgICAgICAgICAuZmlsdGVyKCdbJyArIGF0dHJpYnV0ZSArICddJylcbiAgICAgICAgICAgICAgICAuYWRkKCRwYXJlbnQuZmluZCgnWycgKyBhdHRyaWJ1dGUgKyAnXScpKVxuICAgICAgICAgICAgICAgIC5lYWNoKChpbmRleCwgZWxlbWVudCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICBjb25zdCAkZWxlbWVudCA9ICQoZWxlbWVudCk7XG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IG1vZHVsZXMgPSAkZWxlbWVudC5hdHRyKGF0dHJpYnV0ZSk7XG5cbiAgICAgICAgICAgICAgICAgICAgJGVsZW1lbnQucmVtb3ZlQXR0cihhdHRyaWJ1dGUpO1xuXG4gICAgICAgICAgICAgICAgICAgICQuZWFjaChtb2R1bGVzLnJlcGxhY2UoLyhcXHJcXG58XFxufFxccnxcXHNcXHMrKS9nbSwgJyAnKS50cmltKCkuc3BsaXQoJyAnKSwgKGluZGV4LCBuYW1lKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAobmFtZSA9PT0gJycpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgY29uc3QgZGVmZXJyZWQgPSAkLkRlZmVycmVkKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICBkZWZlcnJlZENvbGxlY3Rpb24ucHVzaChkZWZlcnJlZCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGpzZS5jb3JlLm1vZHVsZV9sb2FkZXJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAubG9hZCgkZWxlbWVudCwgbmFtZSwgdGhpcylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuZG9uZSgobW9kdWxlKSA9PiBtb2R1bGUuaW5pdChkZWZlcnJlZCkpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmZhaWwoKGVycm9yKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRlZmVycmVkLnJlamVjdCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBMb2cgdGhlIGVycm9yIGluIHRoZSBjb25zb2xlIGJ1dCBkbyBub3Qgc3RvcCB0aGUgZW5naW5lIGV4ZWN1dGlvbi5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAganNlLmNvcmUuZGVidWcuZXJyb3IoJ0NvdWxkIG5vdCBsb2FkIG1vZHVsZTogJyArIG5hbWUsIGVycm9yKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIC8vIEFsd2F5cyByZXNvbHZlIHRoZSBuYW1lc3BhY2UsIGV2ZW4gaWYgdGhlcmUgYXJlIG1vZHVsZSBlcnJvcnMuXG4gICAgICAgICAgICAkLndoZW4uYXBwbHkodW5kZWZpbmVkLCBkZWZlcnJlZENvbGxlY3Rpb24pLmFsd2F5cygoKSA9PiBuYW1lc3BhY2VEZWZlcnJlZC5yZXNvbHZlKCkpO1xuXG4gICAgICAgICAgICByZXR1cm4gZGVmZXJyZWRDb2xsZWN0aW9uLmxlbmd0aCA/IG5hbWVzcGFjZURlZmVycmVkLnByb21pc2UoKSA6IG5hbWVzcGFjZURlZmVycmVkLnJlc29sdmUoKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIGpzZS5jb25zdHJ1Y3RvcnMuQ29sbGVjdGlvbiA9IENvbGxlY3Rpb247XG59KSgpO1xuIiwiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBkYXRhX2JpbmRpbmcuanMgMjAxNi0wNS0xN1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbihmdW5jdGlvbiAoKSB7XG5cbiAgICAndXNlIHN0cmljdCc7XG5cbiAgICAvKipcbiAgICAgKiBEYXRhIEJpbmRpbmcgQ2xhc3NcbiAgICAgKlxuICAgICAqIEhhbmRsZXMgdHdvLXdheSBkYXRhIGJpbmRpbmcgd2l0aCBVSSBlbGVtZW50cy5cbiAgICAgKlxuICAgICAqIEBjbGFzcyBKU0UvQ29uc3RydWN0b3JzL0RhdGFCaW5kaW5nXG4gICAgICovXG4gICAgY2xhc3MgRGF0YUJpbmRpbmcge1xuICAgICAgICAvKipcbiAgICAgICAgICogQ2xhc3MgQ29uc3RydWN0b3JcbiAgICAgICAgICpcbiAgICAgICAgICogQHBhcmFtIHtTdHJpbmd9IG5hbWUgVGhlIG5hbWUgb2YgdGhlIGJpbmRpbmcuXG4gICAgICAgICAqIEBwYXJhbSB7T2JqZWN0fSAkZWxlbWVudCBUYXJnZXQgZWxlbWVudCB0byBiZSBib25kLlxuICAgICAgICAgKi9cbiAgICAgICAgY29uc3RydWN0b3IobmFtZSwgJGVsZW1lbnQpIHtcbiAgICAgICAgICAgIHRoaXMubmFtZSA9IG5hbWU7XG4gICAgICAgICAgICB0aGlzLiRlbGVtZW50ID0gJGVsZW1lbnQ7XG4gICAgICAgICAgICB0aGlzLnZhbHVlID0gbnVsbDtcbiAgICAgICAgICAgIHRoaXMuaXNNdXRhYmxlID0gJGVsZW1lbnQuaXMoJ2lucHV0LCB0ZXh0YXJlYSwgc2VsZWN0Jyk7XG4gICAgICAgICAgICB0aGlzLmluaXQoKTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBJbml0aWFsaXplIHRoZSBiaW5kaW5nLlxuICAgICAgICAgKi9cbiAgICAgICAgaW5pdCgpIHtcbiAgICAgICAgICAgIHRoaXMuJGVsZW1lbnQub24oJ2NoYW5nZScsICgpID0+IHtcbiAgICAgICAgICAgICAgICB0aGlzLmdldCgpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cblxuICAgICAgICAvKipcbiAgICAgICAgICogR2V0IGJpbmRpbmcgdmFsdWUuXG4gICAgICAgICAqXG4gICAgICAgICAqIEByZXR1cm5zIHsqfVxuICAgICAgICAgKi9cbiAgICAgICAgZ2V0KCkge1xuICAgICAgICAgICAgdGhpcy52YWx1ZSA9IHRoaXMuaXNNdXRhYmxlID8gdGhpcy4kZWxlbWVudC52YWwoKSA6IHRoaXMuJGVsZW1lbnQuaHRtbCgpO1xuXG4gICAgICAgICAgICBpZiAodGhpcy4kZWxlbWVudC5pcygnOmNoZWNrYm94JykgfHwgdGhpcy4kZWxlbWVudC5pcygnOnJhZGlvJykpIHtcbiAgICAgICAgICAgICAgICB0aGlzLnZhbHVlID0gdGhpcy4kZWxlbWVudC5wcm9wKCdjaGVja2VkJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJldHVybiB0aGlzLnZhbHVlO1xuICAgICAgICB9XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFNldCBiaW5kaW5nIHZhbHVlLlxuICAgICAgICAgKlxuICAgICAgICAgKiBAcGFyYW0ge1N0cmluZ30gdmFsdWVcbiAgICAgICAgICovXG4gICAgICAgIHNldCh2YWx1ZSkge1xuICAgICAgICAgICAgdGhpcy52YWx1ZSA9IHZhbHVlO1xuXG4gICAgICAgICAgICBpZiAodGhpcy5pc011dGFibGUpIHtcbiAgICAgICAgICAgICAgICB0aGlzLiRlbGVtZW50LnZhbCh2YWx1ZSk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIHRoaXMuJGVsZW1lbnQuaHRtbCh2YWx1ZSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBqc2UuY29uc3RydWN0b3JzLkRhdGFCaW5kaW5nID0gRGF0YUJpbmRpbmc7XG59KSgpO1xuIiwiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBtb2R1bGUuanMgMjAxNi0wNS0xN1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuKGZ1bmN0aW9uICgpIHtcblxuICAgICd1c2Ugc3RyaWN0JztcblxuICAgIC8qKlxuICAgICAqIENsYXNzIE1vZHVsZVxuICAgICAqXG4gICAgICogVGhpcyBjbGFzcyBpcyB1c2VkIGZvciByZXByZXNlbnRpbmcgYSBtb2R1bGUgaW5zdGFuY2Ugd2l0aGluIHRoZSBKU0UgZWNvc3lzdGVtLlxuICAgICAqXG4gICAgICogQGNsYXNzIEpTRS9Db25zdHJ1Y3RvcnMvTW9kdWxlXG4gICAgICovXG4gICAgY2xhc3MgTW9kdWxlIHtcbiAgICAgICAgLyoqXG4gICAgICAgICAqIENsYXNzIENvbnN0cnVjdG9yXG4gICAgICAgICAqXG4gICAgICAgICAqIEBwYXJhbSB7T2JqZWN0fSAkZWxlbWVudCBNb2R1bGUgZWxlbWVudCBzZWxlY3RvciBvYmplY3QuXG4gICAgICAgICAqIEBwYXJhbSB7U3RyaW5nfSBuYW1lIFRoZSBtb2R1bGUgbmFtZSAobWlnaHQgY29udGFpbiB0aGUgcGF0aClcbiAgICAgICAgICogQHBhcmFtIHtPYmplY3R9IGNvbGxlY3Rpb24gVGhlIGNvbGxlY3Rpb24gaW5zdGFuY2Ugb2YgdGhlIG1vZHVsZS5cbiAgICAgICAgICovXG4gICAgICAgIGNvbnN0cnVjdG9yKCRlbGVtZW50LCBuYW1lLCBjb2xsZWN0aW9uKSB7XG4gICAgICAgICAgICB0aGlzLiRlbGVtZW50ID0gJGVsZW1lbnQ7XG4gICAgICAgICAgICB0aGlzLm5hbWUgPSBuYW1lO1xuICAgICAgICAgICAgdGhpcy5jb2xsZWN0aW9uID0gY29sbGVjdGlvbjtcbiAgICAgICAgfVxuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBJbml0aWFsaXplIHRoZSBtb2R1bGUgZXhlY3V0aW9uLlxuICAgICAgICAgKlxuICAgICAgICAgKiBUaGlzIGZ1bmN0aW9uIHdpbGwgZXhlY3V0ZSB0aGUgXCJpbml0XCIgbWV0aG9kIG9mIGVhY2ggbW9kdWxlLlxuICAgICAgICAgKlxuICAgICAgICAgKiBAcGFyYW0ge09iamVjdH0gY29sbGVjdGlvbkRlZmVycmVkIERlZmVycmVkIG9iamVjdCB0aGF0IGdldHMgcHJvY2Vzc2VkIGFmdGVyIHRoZSBtb2R1bGVcbiAgICAgICAgICogaW5pdGlhbGl6YXRpb24gaXMgZmluaXNoZWQuXG4gICAgICAgICAqL1xuICAgICAgICBpbml0KGNvbGxlY3Rpb25EZWZlcnJlZCkge1xuICAgICAgICAgICAgLy8gU3RvcmUgbW9kdWxlIGluc3RhbmNlIGFsaWFzLlxuICAgICAgICAgICAgY29uc3QgY2FjaGVkID0gdGhpcy5jb2xsZWN0aW9uLmNhY2hlLm1vZHVsZXNbdGhpcy5uYW1lXTtcbiAgICAgICAgICAgIGxldCB0aW1lb3V0ID0gbnVsbDtcblxuICAgICAgICAgICAgdHJ5IHtcbiAgICAgICAgICAgICAgICBpZiAoIWNhY2hlZCkge1xuICAgICAgICAgICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoYE1vZHVsZSBcIiR7dGhpcy5uYW1lfVwiIGNvdWxkIG5vdCBiZSBmb3VuZCBpbiB0aGUgY29sbGVjdGlvbiBjYWNoZS5gKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBjb25zdCBkYXRhID0gdGhpcy5fZ2V0TW9kdWxlRGF0YSgpO1xuICAgICAgICAgICAgICAgIGNvbnN0IGluc3RhbmNlID0gY2FjaGVkLmNvZGUuY2FsbCh0aGlzLiRlbGVtZW50LCBkYXRhKTtcblxuICAgICAgICAgICAgICAgIC8vIFByb3ZpZGUgYSBkb25lIGZ1bmN0aW9uIHRoYXQgbmVlZHMgdG8gYmUgY2FsbGVkIGZyb20gdGhlIG1vZHVsZSwgaW4gb3JkZXIgdG8gaW5mb3JtIFxuICAgICAgICAgICAgICAgIC8vIHRoYXQgdGhlIG1vZHVsZSBcImluaXRcIiBmdW5jdGlvbiB3YXMgY29tcGxldGVkIHN1Y2Nlc3NmdWxseS5cbiAgICAgICAgICAgICAgICBjb25zdCBkb25lID0gKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLiRlbGVtZW50LnRyaWdnZXIoJ2pzZTptb2R1bGU6aW5pdGlhbGl6ZWQnLCBbe21vZHVsZTogdGhpcy5uYW1lfV0pO1xuICAgICAgICAgICAgICAgICAgICBqc2UuY29yZS5kZWJ1Zy5pbmZvKGBNb2R1bGUgXCIke3RoaXMubmFtZX1cIiBpbml0aWFsaXplZCBzdWNjZXNzZnVsbHkuYCk7XG4gICAgICAgICAgICAgICAgICAgIGNvbGxlY3Rpb25EZWZlcnJlZC5yZXNvbHZlKCk7XG4gICAgICAgICAgICAgICAgICAgIGNsZWFyVGltZW91dCh0aW1lb3V0KTtcbiAgICAgICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAgICAgLy8gTG9hZCB0aGUgbW9kdWxlIGRhdGEgYmVmb3JlIHRoZSBtb2R1bGUgaXMgbG9hZGVkLlxuICAgICAgICAgICAgICAgIHRoaXMuX2xvYWRNb2R1bGVEYXRhKGluc3RhbmNlKVxuICAgICAgICAgICAgICAgICAgICAuZG9uZSgoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBSZWplY3QgdGhlIGNvbGxlY3Rpb25EZWZlcnJlZCBpZiB0aGUgbW9kdWxlIGlzbid0IGluaXRpYWxpemVkIGFmdGVyIDEwIHNlY29uZHMuXG4gICAgICAgICAgICAgICAgICAgICAgICB0aW1lb3V0ID0gc2V0VGltZW91dCgoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAganNlLmNvcmUuZGVidWcud2FybignTW9kdWxlIHdhcyBub3QgaW5pdGlhbGl6ZWQgYWZ0ZXIgMTAgc2Vjb25kcyEgLS0gJyArIHRoaXMubmFtZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY29sbGVjdGlvbkRlZmVycmVkLnJlamVjdCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSwgMTAwMDApO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBqc2UuY29yZS52dWUucmVnaXN0ZXJNb2R1bGUoaW5zdGFuY2UpO1xuICAgICAgICAgICAgICAgICAgICAgICAgaW5zdGFuY2UuaW5pdChkb25lKTtcbiAgICAgICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICAgICAgLmZhaWwoKGVycm9yKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjb2xsZWN0aW9uRGVmZXJyZWQucmVqZWN0KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICBqc2UuY29yZS5kZWJ1Zy5lcnJvcignQ291bGQgbm90IGxvYWQgbW9kdWxlXFwncyBtZXRhIGRhdGEuJywgZXJyb3IpO1xuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0gY2F0Y2ggKGV4Y2VwdGlvbikge1xuICAgICAgICAgICAgICAgIGNvbGxlY3Rpb25EZWZlcnJlZC5yZWplY3QoKTtcbiAgICAgICAgICAgICAgICBqc2UuY29yZS5kZWJ1Zy5lcnJvcihgQ2Fubm90IGluaXRpYWxpemUgbW9kdWxlIFwiJHt0aGlzLm5hbWV9XCIuYCwgZXhjZXB0aW9uKTtcbiAgICAgICAgICAgICAgICAkKHdpbmRvdykudHJpZ2dlcignZXJyb3InLCBbZXhjZXB0aW9uXSk7IC8vIEluZm9ybSB0aGUgZW5naW5lIGFib3V0IHRoZSBleGNlcHRpb24uXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJldHVybiBjb2xsZWN0aW9uRGVmZXJyZWQucHJvbWlzZSgpO1xuICAgICAgICB9XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFBhcnNlIHRoZSBtb2R1bGUgZGF0YSBhdHRyaWJ1dGVzLlxuICAgICAgICAgKlxuICAgICAgICAgKiBAcmV0dXJuIHtPYmplY3R9IFJldHVybnMgYW4gb2JqZWN0IHRoYXQgY29udGFpbnMgdGhlIGRhdGEgb2YgdGhlIG1vZHVsZS5cbiAgICAgICAgICpcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIF9nZXRNb2R1bGVEYXRhKCkge1xuICAgICAgICAgICAgY29uc3QgZGF0YSA9IHt9O1xuXG4gICAgICAgICAgICAkLmVhY2godGhpcy4kZWxlbWVudC5kYXRhKCksIChuYW1lLCB2YWx1ZSkgPT4ge1xuICAgICAgICAgICAgICAgIGlmIChuYW1lLmluZGV4T2YodGhpcy5uYW1lKSA9PT0gMCB8fCBuYW1lLmluZGV4T2YodGhpcy5uYW1lLnRvTG93ZXJDYXNlKCkpID09PSAwKSB7XG4gICAgICAgICAgICAgICAgICAgIGxldCBrZXkgPSBuYW1lLnN1YnN0cih0aGlzLm5hbWUubGVuZ3RoKTtcbiAgICAgICAgICAgICAgICAgICAga2V5ID0ga2V5LnN1YnN0cigwLCAxKS50b0xvd2VyQ2FzZSgpICsga2V5LnN1YnN0cigxKTtcbiAgICAgICAgICAgICAgICAgICAgZGF0YVtrZXldID0gdmFsdWU7XG4gICAgICAgICAgICAgICAgICAgIC8vIFJlbW92ZSBkYXRhIGF0dHJpYnV0ZSBmcm9tIGVsZW1lbnQgKHNhbml0aXNlIGNhbWVsIGNhc2UgZmlyc3QpLlxuICAgICAgICAgICAgICAgICAgICBjb25zdCBzYW5pdGlzZWRLZXkgPSBrZXkucmVwbGFjZSgvKFthLXpdKShbQS1aXSkvZywgJyQxLSQyJykudG9Mb3dlckNhc2UoKTtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy4kZWxlbWVudC5yZW1vdmVBdHRyKGBkYXRhLSR7dGhpcy5uYW1lfS0ke3Nhbml0aXNlZEtleX1gKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgcmV0dXJuIGRhdGE7XG4gICAgICAgIH1cblxuICAgICAgICAvKipcbiAgICAgICAgICogTW9kdWxlcyByZXR1cm4gb2JqZWN0cyB3aGljaCBtaWdodCBjb250YWluIHJlcXVpcmVtZW50cy5cbiAgICAgICAgICpcbiAgICAgICAgICogQHBhcmFtIHtPYmplY3R9IGluc3RhbmNlIE1vZHVsZSBpbnN0YW5jZSBvYmplY3QuXG4gICAgICAgICAqXG4gICAgICAgICAqIEByZXR1cm4ge09iamVjdH0gUmV0dXJucyBhIHByb21pc2Ugb2JqZWN0IHRoYXQgd2lsbCBiZSByZXNvbHZlZCB3aGVuIHRoZSBkYXRhIGFyZSBmZXRjaGVkLlxuICAgICAgICAgKlxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgX2xvYWRNb2R1bGVEYXRhKGluc3RhbmNlKSB7XG4gICAgICAgICAgICBjb25zdCBkZWZlcnJlZCA9ICQuRGVmZXJyZWQoKTtcbiAgICAgICAgICAgIGNvbnN0IGRlZmVycmVkQ29sbGVjdGlvbiA9IFtdO1xuXG4gICAgICAgICAgICB0cnkge1xuICAgICAgICAgICAgICAgIGlmIChpbnN0YW5jZS5tb2RlbCkge1xuICAgICAgICAgICAgICAgICAgICAkLmVhY2goaW5zdGFuY2UubW9kZWwsIGZ1bmN0aW9uIChpbmRleCwgdXJsKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zdCBtb2RlbERlZmVycmVkID0gJC5EZWZlcnJlZCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgZGVmZXJyZWRDb2xsZWN0aW9uLnB1c2gobW9kZWxEZWZlcnJlZCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkLmdldEpTT04odXJsKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5kb25lKChyZXNwb25zZSkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpbnN0YW5jZS5tb2RlbFtpbmRleF0gPSByZXNwb25zZTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbW9kZWxEZWZlcnJlZC5yZXNvbHZlKHJlc3BvbnNlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5mYWlsKChlcnJvcikgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBtb2RlbERlZmVycmVkLnJlamVjdChlcnJvcik7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGlmIChpbnN0YW5jZS52aWV3KSB7XG4gICAgICAgICAgICAgICAgICAgICQuZWFjaChpbnN0YW5jZS52aWV3LCBmdW5jdGlvbiAoaW5kZXgsIHVybCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgY29uc3Qgdmlld0RlZmVycmVkID0gJC5EZWZlcnJlZCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgZGVmZXJyZWRDb2xsZWN0aW9uLnB1c2godmlld0RlZmVycmVkKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQuZ2V0KHVybClcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuZG9uZSgocmVzcG9uc2UpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaW5zdGFuY2Uudmlld1tpbmRleF0gPSByZXNwb25zZTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdmlld0RlZmVycmVkLnJlc29sdmUocmVzcG9uc2UpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmZhaWwoKGVycm9yKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZpZXdEZWZlcnJlZC5yZWplY3QoZXJyb3IpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBpZiAoaW5zdGFuY2UuYmluZGluZ3MpIHtcbiAgICAgICAgICAgICAgICAgICAgZm9yIChsZXQgbmFtZSBpbiBpbnN0YW5jZS5iaW5kaW5ncykge1xuICAgICAgICAgICAgICAgICAgICAgICAgY29uc3QgJGVsZW1lbnQgPSBpbnN0YW5jZS5iaW5kaW5nc1tuYW1lXTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGluc3RhbmNlLmJpbmRpbmdzW25hbWVdID0gbmV3IGpzZS5jb25zdHJ1Y3RvcnMuRGF0YUJpbmRpbmcobmFtZSwgJGVsZW1lbnQpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgJC53aGVuLmFwcGx5KHVuZGVmaW5lZCwgZGVmZXJyZWRDb2xsZWN0aW9uKVxuICAgICAgICAgICAgICAgICAgICAuZG9uZShkZWZlcnJlZC5yZXNvbHZlKVxuICAgICAgICAgICAgICAgICAgICAuZmFpbCgoZXJyb3IpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGRlZmVycmVkLnJlamVjdChuZXcgRXJyb3IoYENhbm5vdCBsb2FkIGRhdGEgZm9yIG1vZHVsZSBcIiR7aW5zdGFuY2UubmFtZX1cIi5gLCBlcnJvcikpO1xuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0gY2F0Y2ggKGV4Y2VwdGlvbikge1xuICAgICAgICAgICAgICAgIGRlZmVycmVkLnJlamVjdChleGNlcHRpb24pO1xuICAgICAgICAgICAgICAgIGpzZS5jb3JlLmRlYnVnLmVycm9yKGBDYW5ub3QgcHJlbG9hZCBtb2R1bGUgZGF0YSBmb3IgXCIke3RoaXMubmFtZX1cIi5gLCBleGNlcHRpb24pO1xuICAgICAgICAgICAgICAgICQod2luZG93KS50cmlnZ2VyKCdlcnJvcicsIFtleGNlcHRpb25dKTsgLy8gSW5mb3JtIHRoZSBlbmdpbmUgYWJvdXQgdGhlIGV4Y2VwdGlvbi5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIGRlZmVycmVkLnByb21pc2UoKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIGpzZS5jb25zdHJ1Y3RvcnMuTW9kdWxlID0gTW9kdWxlO1xufSkoKTtcbiIsIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gbmFtZXNwYWNlLmpzIDIwMTYtMDUtMTdcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4oZnVuY3Rpb24gKCkge1xuXG4gICAgJ3VzZSBzdHJpY3QnO1xuXG4gICAgLyoqXG4gICAgICogQ2xhc3MgTmFtZXNwYWNlXG4gICAgICpcbiAgICAgKiBUaGlzIGNsYXNzIGlzIHVzZWQgdG8gaGFuZGxlIG11bHRpcGxlIGNvbGxlY3Rpb25zIG9mIG1vZHVsZXMuIEV2ZXJ5IG5hbWVzcGFjZSBoYXMgaXRzIG93biBzb3VyY2UgVVJMXG4gICAgICogZm9yIGxvYWRpbmcgdGhlIGRhdGEuIFRoYXQgbWVhbnMgdGhhdCBKU0UgY2FuIGxvYWQgbW9kdWxlcyBmcm9tIG11bHRpcGxlIHBsYWNlcyBhdCB0aGUgc2FtZSB0aW1lLlxuICAgICAqXG4gICAgICogQGNsYXNzIEpTRS9Db25zdHJ1Y3RvcnMvTmFtZXNwYWNlXG4gICAgICovXG4gICAgY2xhc3MgTmFtZXNwYWNlIHtcbiAgICAgICAgLyoqXG4gICAgICAgICAqIENsYXNzIENvbnN0cnVjdG9yXG4gICAgICAgICAqXG4gICAgICAgICAqIEBwYXJhbSB7U3RyaW5nfSBuYW1lIFRoZSBuYW1lc3BhY2UgbmFtZSBtdXN0IGJlIHVuaXF1ZSB3aXRoaW4gdGhlIGFwcC5cbiAgICAgICAgICogQHBhcmFtIHtTdHJpbmd9IHNvdXJjZSBDb21wbGV0ZSBVUkwgdG8gdGhlIG5hbWVzcGFjZSBtb2R1bGVzIGRpcmVjdG9yeSAod2l0aG91dCB0cmFpbGluZyBzbGFzaCkuXG4gICAgICAgICAqIEBwYXJhbSB7QXJyYXl9IGNvbGxlY3Rpb25zIENvbnRhaW5zIGNvbGxlY3Rpb24gaW5zdGFuY2VzIHRvIGJlIGluY2x1ZGVkIGluIHRoZSBuYW1lc3BhY2UuXG4gICAgICAgICAqL1xuICAgICAgICBjb25zdHJ1Y3RvcihuYW1lLCBzb3VyY2UsIGNvbGxlY3Rpb25zKSB7XG4gICAgICAgICAgICB0aGlzLm5hbWUgPSBuYW1lO1xuICAgICAgICAgICAgdGhpcy5zb3VyY2UgPSBzb3VyY2U7XG4gICAgICAgICAgICB0aGlzLmNvbGxlY3Rpb25zID0gY29sbGVjdGlvbnM7IC8vIGNvbnRhaW5zIHRoZSBkZWZhdWx0IGluc3RhbmNlcyAgIFx0XHRcbiAgICAgICAgfVxuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBJbml0aWFsaXplIHRoZSBuYW1lc3BhY2UgY29sbGVjdGlvbnMuXG4gICAgICAgICAqXG4gICAgICAgICAqIFRoaXMgbWV0aG9kIHdpbGwgY3JlYXRlIG5ldyBjb2xsZWN0aW9uIGluc3RhbmNlcyBiYXNlZCBpbiB0aGUgb3JpZ2luYWwgb25lcy5cbiAgICAgICAgICpcbiAgICAgICAgICogQHJldHVybiB7alF1ZXJ5LlByb21pc2V9IFJldHVybnMgYSBwcm9taXNlIHRoYXQgd2lsbCBiZSByZXNvbHZlZCBvbmNlIGV2ZXJ5IG5hbWVzcGFjZSBjb2xsZWN0aW9uXG4gICAgICAgICAqIGlzIHJlc29sdmVkLlxuICAgICAgICAgKi9cbiAgICAgICAgaW5pdCgpIHtcbiAgICAgICAgICAgIGNvbnN0IGRlZmVycmVkQ29sbGVjdGlvbiA9IFtdO1xuXG4gICAgICAgICAgICBmb3IgKGxldCBjb2xsZWN0aW9uIG9mIHRoaXMuY29sbGVjdGlvbnMpIHtcbiAgICAgICAgICAgICAgICB0aGlzW2NvbGxlY3Rpb24ubmFtZV0gPSBuZXcganNlLmNvbnN0cnVjdG9ycy5Db2xsZWN0aW9uKGNvbGxlY3Rpb24ubmFtZSwgY29sbGVjdGlvbi5hdHRyaWJ1dGUsIHRoaXMpO1xuICAgICAgICAgICAgICAgIGNvbnN0IGRlZmVycmVkID0gdGhpc1tjb2xsZWN0aW9uLm5hbWVdLmluaXQoKTtcbiAgICAgICAgICAgICAgICBkZWZlcnJlZENvbGxlY3Rpb24ucHVzaChkZWZlcnJlZCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJldHVybiBkZWZlcnJlZENvbGxlY3Rpb24ubGVuZ3RoID8gJC53aGVuLmFwcGx5KHVuZGVmaW5lZCwgZGVmZXJyZWRDb2xsZWN0aW9uKS5wcm9taXNlKCkgOiAkLkRlZmVycmVkKCkucmVzb2x2ZSgpO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAganNlLmNvbnN0cnVjdG9ycy5OYW1lc3BhY2UgPSBOYW1lc3BhY2U7XG59KSgpO1xuIiwiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBhYm91dC5qcyAyMDE2LTA5LTA4XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBKU0UgSW5mb3JtYXRpb24gTW9kdWxlXG4gKlxuICogRXhlY3V0ZSB0aGUgYGpzZS5hYm91dCgpYCBjb21tYW5kIGFuZCB5b3Ugd2lsbCBnZXQgYSBuZXcgbG9nIGVudHJ5IGluIHRoZVxuICogY29uc29sZSB3aXRoIGluZm8gYWJvdXQgdGhlIGVuZ2luZS4gVGhlIFwiYWJvdXRcIiBtZXRob2QgaXMgb25seSBhdmFpbGFibGUgaW5cbiAqIHRoZSBcImRldmVsb3BtZW50XCIgZW52aXJvbm1lbnQgb2YgdGhlIGVuZ2luZS5cbiAqXG4gKiBAbW9kdWxlIEpTRS9Db3JlL2Fib3V0XG4gKi9cbmRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ0RPTUNvbnRlbnRMb2FkZWQnLCBmdW5jdGlvbiAoKSB7XG5cbiAgICAndXNlIHN0cmljdCc7XG5cbiAgICBpZiAoanNlLmNvcmUuY29uZmlnLmdldCgnZW52aXJvbm1lbnQnKSA9PT0gJ3Byb2R1Y3Rpb24nKSB7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBqc2UuYWJvdXQgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgIGNvbnN0IGluZm8gPSBgXG5cdFx0XHRKUyBFTkdJTkUgdiR7anNlLmNvcmUuY29uZmlnLmdldCgndmVyc2lvbicpfSDCqSBHQU1CSU8gR01CSFxuXHRcdFx0LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFx0VGhlIEpTIEVuZ2luZSBlbmFibGVzIGRldmVsb3BlcnMgdG8gbG9hZCBhdXRvbWF0aWNhbGx5IHNtYWxsIHBpZWNlcyBvZiBqYXZhc2NyaXB0IGNvZGUgYnlcblx0XHRcdHBsYWNpbmcgc3BlY2lmaWMgZGF0YSBhdHRyaWJ1dGVzIHRvIHRoZSBIVE1MIG1hcmt1cCBvZiBhIHBhZ2UuIEl0IHdhcyBidWlsdCB3aXRoIG1vZHVsYXJpdHlcblx0XHRcdGluIG1pbmQgc28gdGhhdCBtb2R1bGVzIGNhbiBiZSByZXVzZWQgaW50byBtdWx0aXBsZSBwbGFjZXMgd2l0aG91dCBleHRyYSBlZmZvcnQuIFRoZSBlbmdpbmVcblx0XHRcdGNvbnRhaW5zIG5hbWVzcGFjZXMgd2hpY2ggY29udGFpbiBjb2xsZWN0aW9ucyBvZiBtb2R1bGVzLCBlYWNoIG9uZSBvZiB3aG9tIHNlcnZlIGEgZGlmZmVyZW50XG5cdFx0XHRnZW5lcmljIHB1cnBvc2UuXG5cdFx0XHRWaXNpdCBodHRwOi8vZGV2ZWxvcGVycy5nYW1iaW8uZGUgZm9yIGNvbXBsZXRlIHJlZmVyZW5jZSBvZiB0aGUgSlMgRW5naW5lLlxuXHRcdFx0XG5cdFx0XHRGQUxMQkFDSyBJTkZPUk1BVElPTlxuXHRcdFx0LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFx0U2luY2UgdGhlIGVuZ2luZSBjb2RlIGJlY29tZXMgYmlnZ2VyIHRoZXJlIGFyZSBzZWN0aW9ucyB0aGF0IG5lZWQgdG8gYmUgcmVmYWN0b3JlZCBpbiBvcmRlclxuXHRcdFx0dG8gYmVjb21lIG1vcmUgZmxleGlibGUuIEluIG1vc3QgY2FzZXMgYSB3YXJuaW5nIGxvZyB3aWxsIGJlIGRpc3BsYXllZCBhdCB0aGUgYnJvd3NlclxcJ3MgY29uc29sZVxuXHRcdFx0d2hlbmV2ZXIgdGhlcmUgaXMgYSB1c2Ugb2YgYSBkZXByZWNhdGVkIGZ1bmN0aW9uLiBCZWxvdyB0aGVyZSBpcyBhIHF1aWNrIGxpc3Qgb2YgZmFsbGJhY2sgc3VwcG9ydFxuXHRcdFx0dGhhdCB3aWxsIGJlIHJlbW92ZWQgaW4gdGhlIGZ1dHVyZSB2ZXJzaW9ucyBvZiB0aGUgZW5naW5lLlxuXHRcdFx0XG5cdFx0XHQxLiBUaGUgbWFpbiBlbmdpbmUgb2JqZWN0IHdhcyByZW5hbWVkIGZyb20gXCJneFwiIHRvIFwianNlXCIgd2hpY2ggc3RhbmRzIGZvciB0aGUgSmF2YVNjcmlwdCBFbmdpbmUuXG5cdFx0XHQyLiBUaGUgXCJneC5saWJcIiBvYmplY3QgaXMgcmVtb3ZlZCBhZnRlciBhIGxvbmcgZGVwcmVjYXRpb24gcGVyaW9kLiBZb3Ugc2hvdWxkIHVwZGF0ZSB0aGUgbW9kdWxlcyBcblx0XHRcdCAgIHRoYXQgY29udGFpbmVkIGNhbGxzIHRvIHRoZSBmdW5jdGlvbnMgb2YgdGhpcyBvYmplY3QuXG5cdFx0XHQzLiBUaGUgZ3guPGNvbGxlY3Rpb24tbmFtZT4ucmVnaXN0ZXIgZnVuY3Rpb24gaXMgZGVwcmVjYXRlZCBieSB2MS4yLCB1c2UgdGhlIFxuXHRcdFx0ICAgPG5hbWVzcGFjZT4uPGNvbGxlY3Rpb24+Lm1vZHVsZSgpIGluc3RlYWQuXG5cdFx0YDtcblxuICAgICAgICBqc2UuY29yZS5kZWJ1Zy5pbmZvKGluZm8pO1xuICAgIH07XG59KTtcbiIsIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gY29uZmlnLmpzIDIwMTgtMDktMTJcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE3IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG5qc2UuY29yZS5jb25maWcgPSBqc2UuY29yZS5jb25maWcgfHwge307XG5cbi8qKlxuICogSlNFIENvbmZpZ3VyYXRpb24gTW9kdWxlXG4gKlxuICogT25jZSB0aGUgY29uZmlnIG9iamVjdCBpcyBpbml0aWFsaXplZCB5b3UgY2Fubm90IGNoYW5nZSBpdHMgdmFsdWVzLiBUaGlzIGlzIGRvbmUgaW4gb3JkZXIgdG9cbiAqIHByZXZlbnQgdW5wbGVhc2FudCBzaXR1YXRpb25zIHdoZXJlIG9uZSBjb2RlIHNlY3Rpb24gY2hhbmdlcyBhIGNvcmUgY29uZmlnIHNldHRpbmcgdGhhdCBhZmZlY3RzXG4gKiBhbm90aGVyIGNvZGUgc2VjdGlvbiBpbiBhIHdheSB0aGF0IGlzIGhhcmQgdG8gZGlzY292ZXIuXG4gKlxuICogYGBgamF2YXNjcmlwdFxuICogY29uc3QgYXBwVXJsID0ganNlLmNvcmUuY29uZmlnLmdldCgnYXBwVXJsJyk7XG4gKiBgYGBcbiAqXG4gKiBAbW9kdWxlIEpTRS9Db3JlL2NvbmZpZ1xuICovXG4oZnVuY3Rpb24gKGV4cG9ydHMpIHtcblxuICAgICd1c2Ugc3RyaWN0JztcblxuICAgIC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICAgIC8vIENPTkZJR1VSQVRJT04gVkFMVUVTXG4gICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cbiAgICBjb25zdCBjb25maWcgPSB7XG4gICAgICAgIC8qKlxuICAgICAgICAgKiBFbmdpbmUgVmVyc2lvblxuICAgICAgICAgKlxuICAgICAgICAgKiBAdHlwZSB7U3RyaW5nfVxuICAgICAgICAgKi9cbiAgICAgICAgdmVyc2lvbjogJzEuNicsXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEFwcCBVUkxcbiAgICAgICAgICpcbiAgICAgICAgICogZS5nLiAnaHR0cDovL2FwcC5jb20nXG4gICAgICAgICAqXG4gICAgICAgICAqIEB0eXBlIHtTdHJpbmd9XG4gICAgICAgICAqL1xuICAgICAgICBhcHBVcmw6IG51bGwsXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFNob3AgVVJMXG4gICAgICAgICAqXG4gICAgICAgICAqIGUuZy4gJ2h0dHA6Ly9leGFtcGxlLm9yZydcbiAgICAgICAgICpcbiAgICAgICAgICogQGRlcHJlY2F0ZWQgU2luY2UgdjEuNCwgdXNlIGFwcFVybCBpbnN0ZWFkLlxuICAgICAgICAgKlxuICAgICAgICAgKiBAdHlwZSB7U3RyaW5nfVxuICAgICAgICAgKi9cbiAgICAgICAgc2hvcFVybDogbnVsbCxcblxuICAgICAgICAvKipcbiAgICAgICAgICogQXBwIFZlcnNpb25cbiAgICAgICAgICpcbiAgICAgICAgICogZS5nLiAnMi43LjMuMCdcbiAgICAgICAgICpcbiAgICAgICAgICogQHR5cGUge1N0cmluZ31cbiAgICAgICAgICovXG4gICAgICAgIGFwcFZlcnNpb246IG51bGwsXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFNob3AgVmVyc2lvblxuICAgICAgICAgKlxuICAgICAgICAgKiBlLmcuICcyLjcuMy4wJ1xuICAgICAgICAgKlxuICAgICAgICAgKiBAZGVwcmVjYXRlZCBTaW5jZSAxLjQsIHVzZSBhcHBWZXJzaW9uIGluc3RlYWQuXG4gICAgICAgICAqXG4gICAgICAgICAqIEB0eXBlIHtTdHJpbmd9XG4gICAgICAgICAqL1xuICAgICAgICBzaG9wVmVyc2lvbjogbnVsbCxcblxuICAgICAgICAvKipcbiAgICAgICAgICogVVJMIHRvIEpTRW5naW5lIERpcmVjdG9yeS5cbiAgICAgICAgICpcbiAgICAgICAgICogZS5nLiAnaHR0cDovL2FwcC5jb20vSlNFbmdpbmVcbiAgICAgICAgICpcbiAgICAgICAgICogQHR5cGUge1N0cmluZ31cbiAgICAgICAgICovXG4gICAgICAgIGVuZ2luZVVybDogbnVsbCxcblxuICAgICAgICAvKipcbiAgICAgICAgICogRW5naW5lIEVudmlyb25tZW50XG4gICAgICAgICAqXG4gICAgICAgICAqIERlZmluZXMgdGhlIGZ1bmN0aW9uYWxpdHkgb2YgdGhlIGVuZ2luZSBpbiBtYW55IHNlY3Rpb25zLlxuICAgICAgICAgKlxuICAgICAgICAgKiBWYWx1ZXM6ICdkZXZlbG9wbWVudCcsICdwcm9kdWN0aW9uJ1xuICAgICAgICAgKlxuICAgICAgICAgKiBAdHlwZSB7U3RyaW5nfVxuICAgICAgICAgKi9cbiAgICAgICAgZW52aXJvbm1lbnQ6ICdwcm9kdWN0aW9uJyxcblxuICAgICAgICAvKipcbiAgICAgICAgICogVHJhbnNsYXRpb25zIE9iamVjdFxuICAgICAgICAgKlxuICAgICAgICAgKiBDb250YWlucyB0aGUgbG9hZGVkIHRyYW5zbGF0aW9ucyB0byBiZSB1c2VkIHdpdGhpbiBKU0VuZ2luZS5cbiAgICAgICAgICpcbiAgICAgICAgICogQHNlZSBqc2UuY29yZS5sYW5nIG9iamVjdFxuICAgICAgICAgKlxuICAgICAgICAgKiBAdHlwZSB7T2JqZWN0fVxuICAgICAgICAgKi9cbiAgICAgICAgdHJhbnNsYXRpb25zOiB7fSxcblxuICAgICAgICAvKipcbiAgICAgICAgICogTW9kdWxlIENvbGxlY3Rpb25zXG4gICAgICAgICAqXG4gICAgICAgICAqIFByb3ZpZGUgYXJyYXkgd2l0aCB7IG5hbWU6ICcnLCBhdHRyaWJ1dGU6ICcnfSBvYmplY3RzIHRoYXQgZGVmaW5lIHRoZSBjb2xsZWN0aW9ucyB0byBiZSB1c2VkIHdpdGhpblxuICAgICAgICAgKiB0aGUgYXBwbGljYXRpb24uXG4gICAgICAgICAqXG4gICAgICAgICAqIEB0eXBlIHtBcnJheX1cbiAgICAgICAgICovXG4gICAgICAgIGNvbGxlY3Rpb25zOiBbXSxcblxuICAgICAgICAvKipcbiAgICAgICAgICogQ3VycmVudCBMYW5ndWFnZSBDb2RlXG4gICAgICAgICAqXG4gICAgICAgICAqIEB0eXBlIHtTdHJpbmd9XG4gICAgICAgICAqL1xuICAgICAgICBsYW5ndWFnZUNvZGU6ICdkZScsXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFNldCB0aGUgZGVidWcgbGV2ZWwgdG8gb25lIG9mIHRoZSBmb2xsb3dpbmc6ICdERUJVRycsICdJTkZPJywgJ0xPRycsICdXQVJOJywgJ0VSUk9SJyxcbiAgICAgICAgICogJ0FMRVJUJywgJ1NJTEVOVCcuXG4gICAgICAgICAqXG4gICAgICAgICAqIEB0eXBlIHtTdHJpbmd9XG4gICAgICAgICAqL1xuICAgICAgICBkZWJ1ZzogJ1NJTEVOVCcsXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFVzZSBjYWNoZSBidXN0aW5nIHRlY2huaXF1ZSB3aGVuIGxvYWRpbmcgbW9kdWxlcy5cbiAgICAgICAgICpcbiAgICAgICAgICogQGRlcHJlY2F0ZWQgU2luY2UgdjEuNFxuICAgICAgICAgKlxuICAgICAgICAgKiBAc2VlIGpzZS5jb3JlLm1vZHVsZV9sb2FkZXIgb2JqZWN0XG4gICAgICAgICAqXG4gICAgICAgICAqIEB0eXBlIHtCb29sZWFufVxuICAgICAgICAgKi9cbiAgICAgICAgY2FjaGVCdXN0OiB0cnVlLFxuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBVc2UgY2FjaGUgYnVzdGluZyB0b2tlbiBhcyBwYXJ0IG9mIGZpbGUgbmFtZS5cbiAgICAgICAgICpcbiAgICAgICAgICogQHR5cGUge0Jvb2xlYW59XG4gICAgICAgICAqL1xuICAgICAgICBidXN0RmlsZXM6IGZhbHNlLFxuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBXaGV0aGVyIHRoZSBjbGllbnQgaGFzIGEgbW9iaWxlIGludGVyZmFjZS5cbiAgICAgICAgICpcbiAgICAgICAgICogQHR5cGUge0Jvb2xlYW59XG4gICAgICAgICAqL1xuICAgICAgICBtb2JpbGU6ICgvQW5kcm9pZHx3ZWJPU3xpUGhvbmV8aVBhZHxpUG9kfEJsYWNrQmVycnl8SUVNb2JpbGV8T3BlcmEgTWluaS9pLnRlc3QobmF2aWdhdG9yLnVzZXJBZ2VudCkpLFxuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBXaGV0aGVyIHRoZSBjbGllbnQgc3VwcG9ydHMgdG91Y2ggZXZlbnRzLlxuICAgICAgICAgKlxuICAgICAgICAgKiBAdHlwZSB7Qm9vbGVhbn1cbiAgICAgICAgICovXG4gICAgICAgIHRvdWNoOiAoKCdvbnRvdWNoc3RhcnQnIGluIHdpbmRvdykgfHwgd2luZG93Lm9udG91Y2hzdGFydCB8fCB3aW5kb3cub25tc2dlc3R1cmVjaGFuZ2UpID8gdHJ1ZSA6IGZhbHNlLFxuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBTcGVjaWZ5IHRoZSBwYXRoIGZvciB0aGUgZmlsZSBtYW5hZ2VyLlxuICAgICAgICAgKlxuICAgICAgICAgKiBAZGVwcmVjYXRlZCBTaW5jZSB2MS40XG4gICAgICAgICAqXG4gICAgICAgICAqIEB0eXBlIHtTdHJpbmd9XG4gICAgICAgICAqL1xuICAgICAgICBmaWxlbWFuYWdlcjogJ2luY2x1ZGVzL2NrZWRpdG9yL2ZpbGVtYW5hZ2VyL2luZGV4Lmh0bWwnLFxuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBQYWdlIHRva2VuIHRvIGluY2x1ZGUgaW4gZXZlcnkgQUpBWCByZXF1ZXN0LlxuICAgICAgICAgKlxuICAgICAgICAgKiBUaGUgcGFnZSB0b2tlbiBpcyB1c2VkIHRvIGF2b2lkIENTUkYgYXR0YWNrcy4gSXQgbXVzdCBiZSBwcm92aWRlZCBieSB0aGUgYmFja2VuZCBhbmQgaXQgd2lsbFxuICAgICAgICAgKiBiZSB2YWxpZGF0ZWQgdGhlcmUuXG4gICAgICAgICAqXG4gICAgICAgICAqIEB0eXBlIHtTdHJpbmd9XG4gICAgICAgICAqL1xuICAgICAgICBwYWdlVG9rZW46ICcnLFxuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBDYWNoZSBUb2tlbiBTdHJpbmdcbiAgICAgICAgICpcbiAgICAgICAgICogVGhpcyBjb25maWd1cmF0aW9uIHZhbHVlIHdpbGwgYmUgdXNlZCBpbiBwcm9kdWN0aW9uIGVudmlyb25tZW50IGZvciBjYWNoZSBidXN0aW5nLiBJdCBtdXN0XG4gICAgICAgICAqIGJlIHByb3ZpZGVkIHdpdGggdGhlIHdpbmRvdy5KU0VuZ2luZUNvbmZpZ3VyYXRpb24gb2JqZWN0LlxuICAgICAgICAgKlxuICAgICAgICAgKiBAdHlwZSB7U3RyaW5nfVxuICAgICAgICAgKi9cbiAgICAgICAgY2FjaGVUb2tlbjogJycsXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIERlZmluZXMgd2hldGhlciB0aGUgaGlzdG9yeSBvYmplY3QgaXMgYXZhaWxhYmxlLlxuICAgICAgICAgKlxuICAgICAgICAgKiBAdHlwZSB7Qm9vbGVhbn1cbiAgICAgICAgICovXG4gICAgICAgIGhpc3Rvcnk6IGhpc3RvcnkgJiYgaGlzdG9yeS5yZXBsYWNlU3RhdGUgJiYgaGlzdG9yeS5wdXNoU3RhdGUsXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFZ1ZSBDb25maWd1cmF0aW9uXG4gICAgICAgICAqXG4gICAgICAgICAqIFRoaXMgY29uZmlndXJhdGlvbiB3d2lsbCBiZSB1c2VkIGZvciBhZGRpbmcgVnVlIHN1cHBvcnQgaW4gdGhlIGN1cnJlbnQgcGFnZS5cbiAgICAgICAgICpcbiAgICAgICAgICogQHR5cGUge09iamVjdH1cbiAgICAgICAgICovXG4gICAgICAgIHZ1ZTogbnVsbCxcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogQmxhY2tsaXN0IGNvbmZpZyB2YWx1ZXMgaW4gcHJvZHVjdGlvbiBlbnZpcm9ubWVudC5cbiAgICAgKlxuICAgICAqIEB0eXBlIHtTdHJpbmdbXX1cbiAgICAgKi9cbiAgICBjb25zdCBibGFja2xpc3QgPSBbXG4gICAgICAgICd2ZXJzaW9uJyxcbiAgICAgICAgJ2FwcFZlcnNpb24nLFxuICAgICAgICAnc2hvcFZlcnNpb24nXG4gICAgXTtcblxuICAgIC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICAgIC8vIFBVQkxJQyBNRVRIT0RTXG4gICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cbiAgICAvKipcbiAgICAgKiBHZXQgYSBjb25maWd1cmF0aW9uIHZhbHVlLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtTdHJpbmd9IG5hbWUgVGhlIGNvbmZpZ3VyYXRpb24gdmFsdWUgbmFtZSB0byBiZSByZXRyaWV2ZWQuXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIHsqfSBSZXR1cm5zIHRoZSBjb25maWcgdmFsdWUuXG4gICAgICovXG4gICAgZXhwb3J0cy5nZXQgPSBmdW5jdGlvbiAobmFtZSkge1xuICAgICAgICBpZiAoY29uZmlnLmVudmlyb25tZW50ID09PSAncHJvZHVjdGlvbicgJiYgYmxhY2tsaXN0LmluY2x1ZGVzKG5hbWUpKSB7XG4gICAgICAgICAgICByZXR1cm4gbnVsbDtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBjb25maWdbbmFtZV07XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIEpTIEVuZ2luZSBjb25maWcgb2JqZWN0LlxuICAgICAqXG4gICAgICogVGhpcyBtZXRob2Qgd2lsbCBwYXJzZSB0aGUgZ2xvYmFsIFwiSlNFbmdpbmVDb25maWd1cmF0aW9uXCIgb2JqZWN0IGFuZCB0aGVuIHJlbW92ZVxuICAgICAqIGl0IGZyb20gdGhlIGdsb2JhbCBzY29wZSBzbyB0aGF0IGl0IGJlY29tZXMgdGhlIG9ubHkgY29uZmlnIHNvdXJjZSBmb3IgamF2YXNjcmlwdC5cbiAgICAgKlxuICAgICAqIE5vdGljZTogVGhlIG9ubHkgcmVxdWlyZWQgSlNFbmdpbmVDb25maWd1cmF0aW9uIHZhbHVlcyBhcmUgdGhlIFwiZW52aXJvbm1lbnRcIiBhbmQgdGhlIFwiYXBwVXJsXCIuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge09iamVjdH0ganNFbmdpbmVDb25maWd1cmF0aW9uIE11c3QgY29udGFpbiBpbmZvcm1hdGlvbiB0aGF0IGRlZmluZSBjb3JlIG9wZXJhdGlvbnNcbiAgICAgKiBvZiB0aGUgZW5naW5lLiBDaGVjayB0aGUgXCJsaWJzL2luaXRpYWxpemVcIiBlbnRyeSBvZiB0aGUgZW5naW5lIGRvY3VtZW50YXRpb24uXG4gICAgICovXG4gICAgZXhwb3J0cy5pbml0ID0gZnVuY3Rpb24gKGpzRW5naW5lQ29uZmlndXJhdGlvbikge1xuICAgICAgICBjb25maWcuZW52aXJvbm1lbnQgPSBqc0VuZ2luZUNvbmZpZ3VyYXRpb24uZW52aXJvbm1lbnQ7XG4gICAgICAgIGNvbmZpZy5hcHBVcmwgPSBqc0VuZ2luZUNvbmZpZ3VyYXRpb24uYXBwVXJsLnJlcGxhY2UoL1xcLyskLywgJycpOyAvLyBSZW1vdmUgdHJhaWxpbmcgc2xhc2ggZnJvbSBhcHBVcmwuXG5cbiAgICAgICAgaWYgKGNvbmZpZy5lbnZpcm9ubWVudCA9PT0gJ2RldmVsb3BtZW50Jykge1xuICAgICAgICAgICAgY29uZmlnLmNhY2hlQnVzdCA9IGZhbHNlO1xuICAgICAgICAgICAgY29uZmlnLm1pbmlmaWVkID0gZmFsc2U7XG4gICAgICAgICAgICBjb25maWcuZGVidWcgPSAnREVCVUcnO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKGpzRW5naW5lQ29uZmlndXJhdGlvbi5lbmdpbmVVcmwgIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgY29uZmlnLmVuZ2luZVVybCA9IGpzRW5naW5lQ29uZmlndXJhdGlvbi5lbmdpbmVVcmwucmVwbGFjZSgvXFwvKyQvLCAnJyk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICBjb25maWcuZW5naW5lVXJsID0gY29uZmlnLmFwcFVybCArICcvSlNFbmdpbmUvYnVpbGQnO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKGpzRW5naW5lQ29uZmlndXJhdGlvbi50cmFuc2xhdGlvbnMgIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgY29uZmlnLnRyYW5zbGF0aW9ucyA9IGpzRW5naW5lQ29uZmlndXJhdGlvbi50cmFuc2xhdGlvbnM7XG5cbiAgICAgICAgICAgIGZvciAobGV0IHNlY3Rpb25OYW1lIGluIGNvbmZpZy50cmFuc2xhdGlvbnMpIHtcbiAgICAgICAgICAgICAgICBqc2UuY29yZS5sYW5nLmFkZFNlY3Rpb24oc2VjdGlvbk5hbWUsIGNvbmZpZy50cmFuc2xhdGlvbnNbc2VjdGlvbk5hbWVdKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIGlmIChqc0VuZ2luZUNvbmZpZ3VyYXRpb24uY29sbGVjdGlvbnMgIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgY29uZmlnLmNvbGxlY3Rpb25zID0ganNFbmdpbmVDb25maWd1cmF0aW9uLmNvbGxlY3Rpb25zO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgY29uZmlnLmNvbGxlY3Rpb25zID0gW1xuICAgICAgICAgICAgICAgIHtuYW1lOiAnY29udHJvbGxlcnMnLCBhdHRyaWJ1dGU6ICdjb250cm9sbGVyJ30sXG4gICAgICAgICAgICAgICAge25hbWU6ICdleHRlbnNpb25zJywgYXR0cmlidXRlOiAnZXh0ZW5zaW9uJ30sXG4gICAgICAgICAgICAgICAge25hbWU6ICd3aWRnZXRzJywgYXR0cmlidXRlOiAnd2lkZ2V0J31cbiAgICAgICAgICAgIF1cbiAgICAgICAgfVxuXG4gICAgICAgIGlmIChqc0VuZ2luZUNvbmZpZ3VyYXRpb24uYXBwVmVyc2lvbiAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICAgICBjb25maWcuYXBwVmVyc2lvbiA9IGpzRW5naW5lQ29uZmlndXJhdGlvbi5hcHBWZXJzaW9uO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKGpzRW5naW5lQ29uZmlndXJhdGlvbi5zaG9wVXJsICE9PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgIGpzZS5jb3JlLmRlYnVnLndhcm4oJ0pTIEVuZ2luZTogXCJzaG9wVXJsXCIgaXMgZGVwcmVjYXRlZCBhbmQgd2lsbCBiZSByZW1vdmVkIGluIEpTIEVuZ2luZSB2MS41LCBwbGVhc2UgJ1xuICAgICAgICAgICAgICAgICsgJ3VzZSB0aGUgXCJhcHBVcmxcIiBpbnN0ZWFkLicpO1xuICAgICAgICAgICAgY29uZmlnLnNob3BVcmwgPSBqc0VuZ2luZUNvbmZpZ3VyYXRpb24uc2hvcFVybC5yZXBsYWNlKC9cXC8rJC8sICcnKTtcbiAgICAgICAgICAgIGNvbmZpZy5hcHBVcmwgPSBjb25maWcuYXBwVXJsIHx8IGNvbmZpZy5zaG9wVXJsOyAvLyBNYWtlIHN1cmUgdGhlIFwiYXBwVXJsXCIgdmFsdWUgaXMgbm90IGVtcHR5LlxuICAgICAgICB9XG5cbiAgICAgICAgaWYgKGpzRW5naW5lQ29uZmlndXJhdGlvbi5zaG9wVmVyc2lvbiAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICAgICBqc2UuY29yZS5kZWJ1Zy53YXJuKCdKUyBFbmdpbmU6IFwic2hvcFZlcnNpb25cIiBpcyBkZXByZWNhdGVkIGFuZCB3aWxsIGJlIHJlbW92ZWQgaW4gSlMgRW5naW5lIHYxLjUsIHBsZWFzZSAnXG4gICAgICAgICAgICAgICAgKyAndXNlIHRoZSBcImFwcFZlcnNpb25cIiBpbnN0ZWFkLicpO1xuICAgICAgICAgICAgY29uZmlnLnNob3BWZXJzaW9uID0ganNFbmdpbmVDb25maWd1cmF0aW9uLnNob3BWZXJzaW9uO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKGpzRW5naW5lQ29uZmlndXJhdGlvbi5wcmVmaXggIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgY29uZmlnLnByZWZpeCA9IGpzRW5naW5lQ29uZmlndXJhdGlvbi5wcmVmaXg7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAoanNFbmdpbmVDb25maWd1cmF0aW9uLmxhbmd1YWdlQ29kZSAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICAgICBjb25maWcubGFuZ3VhZ2VDb2RlID0ganNFbmdpbmVDb25maWd1cmF0aW9uLmxhbmd1YWdlQ29kZTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmIChkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnaW5pdC1qcycpICE9PSBudWxsXG4gICAgICAgICAgICAmJiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnaW5pdC1qcycpLmhhc0F0dHJpYnV0ZSgnZGF0YS1wYWdlLXRva2VuJykpIHtcbiAgICAgICAgICAgIGpzRW5naW5lQ29uZmlndXJhdGlvbi5wYWdlVG9rZW4gPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnaW5pdC1qcycpLmdldEF0dHJpYnV0ZSgnZGF0YS1wYWdlLXRva2VuJyk7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAoanNFbmdpbmVDb25maWd1cmF0aW9uLnBhZ2VUb2tlbiAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICAgICBjb25maWcucGFnZVRva2VuID0ganNFbmdpbmVDb25maWd1cmF0aW9uLnBhZ2VUb2tlbjtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmIChqc0VuZ2luZUNvbmZpZ3VyYXRpb24uY2FjaGVUb2tlbiAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICAgICBjb25maWcuY2FjaGVUb2tlbiA9IGpzRW5naW5lQ29uZmlndXJhdGlvbi5jYWNoZVRva2VuO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKGpzRW5naW5lQ29uZmlndXJhdGlvbi5idXN0RmlsZXMgIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgY29uZmlnLmJ1c3RGaWxlcyA9IGpzRW5naW5lQ29uZmlndXJhdGlvbi5idXN0RmlsZXM7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAoanNFbmdpbmVDb25maWd1cmF0aW9uLnZ1ZSAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICAgICBjb25maWcudnVlID0ganNFbmdpbmVDb25maWd1cmF0aW9uLnZ1ZTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIEFkZCB0aGUgXCJ0b3VjaEV2ZW50c1wiIGVudHJ5IHNvIHRoYXQgbW9kdWxlcyBjYW4gYmluZCB2YXJpb3VzIHRvdWNoIGV2ZW50cyBkZXBlbmRpbmcgdGhlIGJyb3dzZXIuXG4gICAgICAgIGNvbnN0IGdlbmVyYWxUb3VjaEV2ZW50cyA9IHtcbiAgICAgICAgICAgIHN0YXJ0OiAndG91Y2hzdGFydCcsXG4gICAgICAgICAgICBlbmQ6ICd0cm91Y2hlbmQnLFxuICAgICAgICAgICAgbW92ZTogJ3RvdWNobW92ZSdcbiAgICAgICAgfTtcblxuICAgICAgICBjb25zdCBtaWNyb3NvZnRUb3VjaEV2ZW50cyA9IHtcbiAgICAgICAgICAgIHN0YXJ0OiAncG9pbnRlcmRvd24nLFxuICAgICAgICAgICAgZW5kOiAncG9pbnRlcnVwJyxcbiAgICAgICAgICAgIG1vdmU6ICdwb2ludGVybW92ZSdcbiAgICAgICAgfTtcblxuICAgICAgICBjb25maWcudG91Y2hFdmVudHMgPSAod2luZG93Lm9ubXNnZXN0dXJlY2hhbmdlKSA/IG1pY3Jvc29mdFRvdWNoRXZlbnRzIDogZ2VuZXJhbFRvdWNoRXZlbnRzO1xuXG4gICAgICAgIC8vIFNldCBpbml0aWFsIHJlZ2lzdHJ5IHZhbHVlcy4gXG4gICAgICAgIGZvciAobGV0IGVudHJ5IGluIGpzRW5naW5lQ29uZmlndXJhdGlvbi5yZWdpc3RyeSkge1xuICAgICAgICAgICAganNlLmNvcmUucmVnaXN0cnkuc2V0KGVudHJ5LCBqc0VuZ2luZUNvbmZpZ3VyYXRpb24ucmVnaXN0cnlbZW50cnldKTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIEluaXRpYWxpemUgdGhlIG1vZHVsZSBsb2FkZXIgb2JqZWN0LlxuICAgICAgICBqc2UuY29yZS5tb2R1bGVfbG9hZGVyLmluaXQoKTtcblxuICAgICAgICAvLyBEZXN0cm95IGdsb2JhbCBFbmdpbmVDb25maWd1cmF0aW9uIG9iamVjdC5cbiAgICAgICAgZGVsZXRlIHdpbmRvdy5KU0VuZ2luZUNvbmZpZ3VyYXRpb247XG4gICAgfTtcblxufShqc2UuY29yZS5jb25maWcpKTtcbiIsIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gZGVidWcuanMgMjAxNi0wOS0wOFxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbmpzZS5jb3JlLmRlYnVnID0ganNlLmNvcmUuZGVidWcgfHwge307XG5cbi8qKlxuICogSlNFIERlYnVnIE1vZHVsZVxuICpcbiAqIFRoaXMgb2JqZWN0IHByb3ZpZGVzIGFuIHdyYXBwZXIgdG8gdGhlIGNvbnNvbGUubG9nIGZ1bmN0aW9uIGFuZCBlbmFibGVzIGVhc3kgdXNlXG4gKiBvZiB0aGUgZGlmZmVyZW50IGxvZyB0eXBlcyBsaWtlIFwiaW5mb1wiLCBcIndhcm5pbmdcIiwgXCJlcnJvclwiIGV0Yy5cbiAqXG4gKiBAbW9kdWxlIEpTRS9Db3JlL2RlYnVnXG4gKi9cbihmdW5jdGlvbiAoZXhwb3J0cykge1xuICAgICd1c2Ugc3RyaWN0JztcblxuICAgIC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICAgIC8vIFZBUklBQkxFU1xuICAgIC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXG4gICAgLyoqXG4gICAgICogQHR5cGUge1N0cmluZ31cbiAgICAgKi9cbiAgICBjb25zdCBUWVBFX0RFQlVHID0gJ0RFQlVHJztcblxuICAgIC8qKlxuICAgICAqIEB0eXBlIHtTdHJpbmd9XG4gICAgICovXG4gICAgY29uc3QgVFlQRV9JTkZPID0gJ0lORk8nO1xuXG4gICAgLyoqXG4gICAgICogQHR5cGUge1N0cmluZ31cbiAgICAgKi9cbiAgICBjb25zdCBUWVBFX0xPRyA9ICdMT0cnO1xuXG4gICAgLyoqXG4gICAgICogQHR5cGUge1N0cmluZ31cbiAgICAgKi9cbiAgICBjb25zdCBUWVBFX1dBUk4gPSAnV0FSTic7XG5cbiAgICAvKipcbiAgICAgKiBAdHlwZSB7U3RyaW5nfVxuICAgICAqL1xuICAgIGNvbnN0IFRZUEVfRVJST1IgPSAnRVJST1InO1xuXG4gICAgLyoqXG4gICAgICogQHR5cGUge1N0cmluZ31cbiAgICAgKi9cbiAgICBjb25zdCBUWVBFX0FMRVJUID0gJ0FMRVJUJztcblxuICAgIC8qKlxuICAgICAqIEB0eXBlIHtTdHJpbmd9XG4gICAgICovXG4gICAgY29uc3QgVFlQRV9NT0JJTEUgPSAnTU9CSUxFJztcblxuICAgIC8qKlxuICAgICAqIEB0eXBlIHtTdHJpbmd9XG4gICAgICovXG4gICAgY29uc3QgVFlQRV9TSUxFTlQgPSAnU0lMRU5UJztcblxuICAgIC8qKlxuICAgICAqIEFsbCBwb3NzaWJsZSBkZWJ1ZyBsZXZlbHMgaW4gdGhlIG9yZGVyIG9mIGltcG9ydGFuY2UuXG4gICAgICpcbiAgICAgKiBAdHlwZSB7U3RyaW5nW119XG4gICAgICovXG4gICAgY29uc3QgbGV2ZWxzID0gW1xuICAgICAgICBUWVBFX0RFQlVHLFxuICAgICAgICBUWVBFX0lORk8sXG4gICAgICAgIFRZUEVfTE9HLFxuICAgICAgICBUWVBFX1dBUk4sXG4gICAgICAgIFRZUEVfRVJST1IsXG4gICAgICAgIFRZUEVfQUxFUlQsXG4gICAgICAgIFRZUEVfTU9CSUxFLFxuICAgICAgICBUWVBFX1NJTEVOVFxuICAgIF07XG5cbiAgICAvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAgICAvLyBGVU5DVElPTlNcbiAgICAvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblxuICAgIC8qKlxuICAgICAqIFNldCBGYXZpY29uIHRvIEVycm9yIFN0YXRlLlxuICAgICAqXG4gICAgICogVGhpcyBtZXRob2Qgd2lsbCBvbmx5IHdvcmsgaWYgPGNhbnZhcz4gaXMgc3VwcG9ydGVkIGZyb20gdGhlIGJyb3dzZXIuXG4gICAgICpcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIGZ1bmN0aW9uIF9zZXRGYXZpY29uVG9FcnJvclN0YXRlKCkge1xuICAgICAgICBjb25zdCBjYW52YXMgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdjYW52YXMnKTtcbiAgICAgICAgY29uc3QgZmF2aWNvbiA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJ1tyZWw9XCJzaG9ydGN1dCBpY29uXCJdJyk7XG5cbiAgICAgICAgaWYgKGNhbnZhcy5nZXRDb250ZXh0ICYmICFmYXZpY29uLmNsYXNzTmFtZS5pbmNsdWRlcygnZXJyb3Itc3RhdGUnKSkge1xuICAgICAgICAgICAgY29uc3QgaW1nID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnaW1nJyk7XG4gICAgICAgICAgICBjYW52YXMuaGVpZ2h0ID0gY2FudmFzLndpZHRoID0gMTY7XG4gICAgICAgICAgICBjb25zdCBjdHggPSBjYW52YXMuZ2V0Q29udGV4dCgnMmQnKTtcbiAgICAgICAgICAgIGltZy5vbmxvYWQgPSBmdW5jdGlvbiAoKSB7IC8vIENvbnRpbnVlIG9uY2UgdGhlIGltYWdlIGhhcyBiZWVuIGxvYWRlZC4gXG4gICAgICAgICAgICAgICAgY3R4LmRyYXdJbWFnZSh0aGlzLCAwLCAwKTtcbiAgICAgICAgICAgICAgICBjdHguZ2xvYmFsQWxwaGEgPSAwLjY1O1xuICAgICAgICAgICAgICAgIGN0eC5maWxsU3R5bGUgPSAnI0ZGMDAwMCc7XG4gICAgICAgICAgICAgICAgY3R4LnJlY3QoMCwgMCwgMTYsIDE2KTtcbiAgICAgICAgICAgICAgICBjdHguZmlsbCgpO1xuICAgICAgICAgICAgICAgIGZhdmljb24uaHJlZiA9IGNhbnZhcy50b0RhdGFVUkwoJ2ltYWdlL3BuZycpO1xuICAgICAgICAgICAgICAgIGZhdmljb24uY2xhc3NOYW1lICs9ICdlcnJvci1zdGF0ZSc7XG4gICAgICAgICAgICB9O1xuICAgICAgICAgICAgaW1nLnNyYyA9IGZhdmljb24uaHJlZjtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEVycm9yIGhhbmRsZXIgdGhhdCBmZXRjaGVzIGFsbCBleGNlcHRpb25zIHRocm93biBieSB0aGUgamF2YXNjcmlwdC5cbiAgICAgKlxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgZnVuY3Rpb24gX2dsb2JhbEVycm9ySGFuZGxlcigpIHtcbiAgICAgICAgaWYgKGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2Vudmlyb25tZW50JykgIT09ICdwcm9kdWN0aW9uJykge1xuICAgICAgICAgICAgLy8gTG9nIHRoZSBlcnJvciBpbiB0aGUgYnJvd3NlcidzIGNvbnNvbGUuIFxuICAgICAgICAgICAgaWYgKGpzZS5jb3JlLmRlYnVnICE9PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgICAgICBqc2UuY29yZS5kZWJ1Zy5lcnJvcignSlMgRW5naW5lIEVycm9yIEhhbmRsZXInLCBhcmd1bWVudHMpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnSlMgRW5naW5lIEVycm9yIEhhbmRsZXInLCBhcmd1bWVudHMpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBVcGRhdGUgdGhlIHBhZ2UgdGl0bGUgd2l0aCBhbiBlcnJvciBjb3VudC5cbiAgICAgICAgICAgIGNvbnN0IHJlZ2V4ID0gLy5cXCBcXFsoLispXFxdXFwgLztcbiAgICAgICAgICAgIGxldCB0aXRsZSA9IHdpbmRvdy5kb2N1bWVudC50aXRsZTtcbiAgICAgICAgICAgIGxldCBlcnJvckNvdW50ID0gMTtcblxuICAgICAgICAgICAgLy8gR2V0cyB0aGUgY3VycmVudCBlcnJvciBjb3VudCBhbmQgcmVjcmVhdGVzIHRoZSBkZWZhdWx0IHRpdGxlIG9mIHRoZSBwYWdlLlxuICAgICAgICAgICAgaWYgKHRpdGxlLm1hdGNoKHJlZ2V4KSAhPT0gbnVsbCkge1xuICAgICAgICAgICAgICAgIGVycm9yQ291bnQgPSBwYXJzZUludCh0aXRsZS5tYXRjaCgvXFxkKy8pWzBdLCAxMCkgKyAxO1xuICAgICAgICAgICAgICAgIHRpdGxlID0gdGl0bGUucmVwbGFjZShyZWdleCwgJycpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBSZS1jcmVhdGVzIHRoZSBlcnJvciBmbGFnIGF0IHRoZSB0aXRsZSB3aXRoIHRoZSBuZXcgZXJyb3IgY291bnQuXG4gICAgICAgICAgICB0aXRsZSA9ICfinJYgWycgKyBlcnJvckNvdW50ICsgJ10gJyArIHRpdGxlO1xuICAgICAgICAgICAgd2luZG93LmRvY3VtZW50LnRpdGxlID0gdGl0bGU7XG5cbiAgICAgICAgICAgIC8vIFNldCBGYXZpY29uIHRvIEVycm9yIFN0YXRlLlxuICAgICAgICAgICAgX3NldEZhdmljb25Ub0Vycm9yU3RhdGUoKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiB0cnVlO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEV4ZWN1dGVzIHRoZSBjb3JyZWN0IGNvbnNvbGUvYWxlcnQgc3RhdGVtZW50LlxuICAgICAqXG4gICAgICogQHBhcmFtIHtPYmplY3R9IGNhbGxlciAob3B0aW9uYWwpIENvbnRhaW5zIHRoZSBjYWxsZXIgaW5mb3JtYXRpb24gdG8gYmUgZGlzcGxheWVkLlxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBkYXRhIChvcHRpb25hbCkgQ29udGFpbnMgYW55IGFkZGl0aW9uYWwgZGF0YSB0byBiZSBpbmNsdWRlZCBpbiB0aGUgZGVidWcgb3V0cHV0LlxuICAgICAqXG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBmdW5jdGlvbiBfZXhlY3V0ZShjYWxsZXIsIGRhdGEpIHtcbiAgICAgICAgY29uc3QgY3VycmVudExvZ0luZGV4ID0gbGV2ZWxzLmluZGV4T2YoY2FsbGVyKTtcbiAgICAgICAgY29uc3QgYWxsb3dlZExvZ0luZGV4ID0gbGV2ZWxzLmluZGV4T2YoanNlLmNvcmUuY29uZmlnLmdldCgnZGVidWcnKSk7XG4gICAgICAgIGxldCBjb25zb2xlTWV0aG9kID0gbnVsbDtcblxuICAgICAgICBpZiAoY3VycmVudExvZ0luZGV4ID49IGFsbG93ZWRMb2dJbmRleCkge1xuICAgICAgICAgICAgY29uc29sZU1ldGhvZCA9IGNhbGxlci50b0xvd2VyQ2FzZSgpO1xuXG4gICAgICAgICAgICBzd2l0Y2ggKGNvbnNvbGVNZXRob2QpIHtcbiAgICAgICAgICAgICAgICBjYXNlICdhbGVydCc6XG4gICAgICAgICAgICAgICAgICAgIGFsZXJ0KEpTT04uc3RyaW5naWZ5KGRhdGEpKTtcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG5cbiAgICAgICAgICAgICAgICBjYXNlICdtb2JpbGUnOlxuICAgICAgICAgICAgICAgICAgICBjb25zdCAkbW9iaWxlRGVidWdNb2RhbCA9ICQoJy5tb2JpbGUtZGVidWctbW9kYWwnKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiAoISRtb2JpbGVEZWJ1Z01vZGFsLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJCgnPGRpdiAvPicpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmFkZENsYXNzKCdtb2JpbGUtZGVidWctbW9kYWwnKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5jc3Moe1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBwb3NpdGlvbjogJ2ZpeGVkJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdG9wOiAwLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsZWZ0OiAwLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBtYXhIZWlnaHQ6ICc1MCUnLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBtaW5XaWR0aDogJzIwMHB4JyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbWF4V2lkdGg6ICczMDBweCcsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJhY2tncm91bmRDb2xvcjogJ2NyaW1zb24nLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB6SW5kZXg6IDEwMDAwMCxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgb3ZlcmZsb3c6ICdzY3JvbGwnXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuYXBwZW5kVG8oJCgnYm9keScpKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICRtb2JpbGVEZWJ1Z01vZGFsLmFwcGVuZCgnPHA+JyArIEpTT04uc3RyaW5naWZ5KGRhdGEpICsgJzwvcD4nKTtcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG5cbiAgICAgICAgICAgICAgICBkZWZhdWx0OlxuICAgICAgICAgICAgICAgICAgICBpZiAoY29uc29sZSA9PT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm47IC8vIFRoZXJlIGlzIG5vIGNvbnNvbGUgc3VwcG9ydCBzbyBkbyBub3QgcHJvY2VlZC5cbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIGlmICh0eXBlb2YgY29uc29sZVtjb25zb2xlTWV0aG9kXS5hcHBseSA9PT0gJ2Z1bmN0aW9uJyB8fCB0eXBlb2YgY29uc29sZS5sb2cuYXBwbHkgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChjb25zb2xlW2NvbnNvbGVNZXRob2RdICE9PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlW2NvbnNvbGVNZXRob2RdLmFwcGx5KGNvbnNvbGUsIGRhdGEpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZy5hcHBseShjb25zb2xlLCBkYXRhKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKGRhdGEpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBCaW5kIEdsb2JhbCBFcnJvciBIYW5kbGVyXG4gICAgICovXG4gICAgZXhwb3J0cy5iaW5kR2xvYmFsRXJyb3JIYW5kbGVyID0gZnVuY3Rpb24gKCkge1xuICAgICAgICB3aW5kb3cub25lcnJvciA9IF9nbG9iYWxFcnJvckhhbmRsZXI7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIFJlcGxhY2VzIGNvbnNvbGUuZGVidWdcbiAgICAgKlxuICAgICAqIEBwYXJhbXMgeyp9IGFyZ3VtZW50cyBBbnkgZGF0YSB0aGF0IHNob3VsZCBiZSBzaG93biBpbiB0aGUgY29uc29sZSBzdGF0ZW1lbnQuXG4gICAgICovXG4gICAgZXhwb3J0cy5kZWJ1ZyA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgX2V4ZWN1dGUoVFlQRV9ERUJVRywgYXJndW1lbnRzKTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogUmVwbGFjZXMgY29uc29sZS5pbmZvXG4gICAgICpcbiAgICAgKiBAcGFyYW1zIHsqfSBhcmd1bWVudHMgQW55IGRhdGEgdGhhdCBzaG91bGQgYmUgc2hvd24gaW4gdGhlIGNvbnNvbGUgc3RhdGVtZW50LlxuICAgICAqL1xuICAgIGV4cG9ydHMuaW5mbyA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgX2V4ZWN1dGUoVFlQRV9JTkZPLCBhcmd1bWVudHMpO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBSZXBsYWNlcyBjb25zb2xlLmxvZ1xuICAgICAqXG4gICAgICogQHBhcmFtcyB7Kn0gYXJndW1lbnRzIEFueSBkYXRhIHRoYXQgc2hvdWxkIGJlIHNob3duIGluIHRoZSBjb25zb2xlIHN0YXRlbWVudC5cbiAgICAgKi9cbiAgICBleHBvcnRzLmxvZyA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgX2V4ZWN1dGUoVFlQRV9MT0csIGFyZ3VtZW50cyk7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIFJlcGxhY2VzIGNvbnNvbGUud2FyblxuICAgICAqXG4gICAgICogQHBhcmFtcyB7Kn0gYXJndW1lbnRzIEFueSBkYXRhIHRoYXQgc2hvdWxkIGJlIHNob3duIGluIHRoZSBjb25zb2xlIHN0YXRlbWVudC5cbiAgICAgKi9cbiAgICBleHBvcnRzLndhcm4gPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgIF9leGVjdXRlKFRZUEVfV0FSTiwgYXJndW1lbnRzKTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogUmVwbGFjZXMgY29uc29sZS5lcnJvclxuICAgICAqXG4gICAgICogQHBhcmFtIHsqfSBhcmd1bWVudHMgQW55IGRhdGEgdGhhdCBzaG91bGQgYmUgc2hvd24gaW4gdGhlIGNvbnNvbGUgc3RhdGVtZW50LlxuICAgICAqL1xuICAgIGV4cG9ydHMuZXJyb3IgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgIF9leGVjdXRlKFRZUEVfRVJST1IsIGFyZ3VtZW50cyk7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIFJlcGxhY2VzIGFsZXJ0XG4gICAgICpcbiAgICAgKiBAcGFyYW0geyp9IGFyZ3VtZW50cyBBbnkgZGF0YSB0aGF0IHNob3VsZCBiZSBzaG93biBpbiB0aGUgY29uc29sZSBzdGF0ZW1lbnQuXG4gICAgICovXG4gICAgZXhwb3J0cy5hbGVydCA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgX2V4ZWN1dGUoVFlQRV9BTEVSVCwgYXJndW1lbnRzKTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogRGVidWcgaW5mbyBmb3IgbW9iaWxlIGRldmljZXMuXG4gICAgICpcbiAgICAgKiBAcGFyYW0geyp9IGFyZ3VtZW50cyBBbnkgZGF0YSB0aGF0IHNob3VsZCBiZSBzaG93biBpbiB0aGUgY29uc29sZSBzdGF0ZW1lbnQuXG4gICAgICovXG4gICAgZXhwb3J0cy5tb2JpbGUgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgIF9leGVjdXRlKFRZUEVfTU9CSUxFLCBhcmd1bWVudHMpO1xuICAgIH07XG5cbn0oanNlLmNvcmUuZGVidWcpKTtcbiIsIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gZW5naW5lLmpzIDIwMTYtMDktMDhcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG5qc2UuY29yZS5lbmdpbmUgPSBqc2UuY29yZS5lbmdpbmUgfHwge307XG5cbi8qKlxuICogSlNFIENvcmUgTW9kdWxlXG4gKlxuICogVGhpcyBvYmplY3Qgd2lsbCBpbml0aWFsaXplIHRoZSBwYWdlIG5hbWVzcGFjZXMgYW5kIGNvbGxlY3Rpb25zLlxuICpcbiAqIEBtb2R1bGUgSlNFL0NvcmUvZW5naW5lXG4gKi9cbihmdW5jdGlvbiAoZXhwb3J0cykge1xuXG4gICAgJ3VzZSBzdHJpY3QnO1xuXG4gICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gICAgLy8gUFJJVkFURSBGVU5DVElPTlNcbiAgICAvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHBhZ2UgbmFtZXNwYWNlcy5cbiAgICAgKlxuICAgICAqIFRoaXMgbWV0aG9kIHdpbGwgc2VhcmNoIHRoZSBwYWdlIEhUTUwgZm9yIGF2YWlsYWJsZSBuYW1lc3BhY2VzLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtBcnJheX0gY29sbGVjdGlvbnMgQ29udGFpbnMgdGhlIG1vZHVsZSBjb2xsZWN0aW9uIGluc3RhbmNlcyB0byBiZSBpbmNsdWRlZCBpbiB0aGUgbmFtZXNwYWNlcy5cbiAgICAgKlxuICAgICAqIEByZXR1cm4ge0FycmF5fSBSZXR1cm5zIGFuIGFycmF5IHdpdGggdGhlIHBhZ2UgbmFtZXNwYWNlIG5hbWVzLlxuICAgICAqXG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBmdW5jdGlvbiBfaW5pdE5hbWVzcGFjZXMoY29sbGVjdGlvbnMpIHtcbiAgICAgICAgbGV0IHBhZ2VOYW1lc3BhY2VOYW1lcyA9IFtdO1xuXG4gICAgICAgIC8vIFVzZSB0aGUgY3VzdG9tIHBzZXVkbyBzZWxlY3RvciBkZWZpbmVkIGF0IGV4dGVuZC5qcyBpbiBvcmRlciB0byBmZXRjaCB0aGUgYXZhaWxhYmxlIG5hbWVzcGFjZXMuXG4gICAgICAgIGxldCBub2RlcyA9IEFycmF5LmZyb20oZG9jdW1lbnQuZ2V0RWxlbWVudHNCeVRhZ05hbWUoJyonKSksXG4gICAgICAgICAgICByZWdleCA9IC9kYXRhLSguKiktbmFtZXNwYWNlLztcblxuICAgICAgICBmb3IgKGxldCBub2RlIG9mIG5vZGVzKSB7XG4gICAgICAgICAgICBmb3IgKGxldCBhdHRyaWJ1dGUgb2YgQXJyYXkuZnJvbShub2RlLmF0dHJpYnV0ZXMpKSB7XG4gICAgICAgICAgICAgICAgaWYgKGF0dHJpYnV0ZS5uYW1lLnNlYXJjaChyZWdleCkgIT09IC0xKSB7XG4gICAgICAgICAgICAgICAgICAgIC8vIFBhcnNlIHRoZSBuYW1lc3BhY2UgbmFtZSBhbmQgc291cmNlIFVSTC5cbiAgICAgICAgICAgICAgICAgICAgbGV0IG5hbWUgPSBhdHRyaWJ1dGUubmFtZS5yZXBsYWNlKHJlZ2V4LCAnJDEnKSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHNvdXJjZSA9IGF0dHJpYnV0ZS52YWx1ZTtcblxuICAgICAgICAgICAgICAgICAgICAvLyBDaGVjayBpZiB0aGUgbmFtZXNwYWNlIGlzIGFscmVhZHkgZGVmaW5lZC5cbiAgICAgICAgICAgICAgICAgICAgaWYgKHBhZ2VOYW1lc3BhY2VOYW1lcy5pbmRleE9mKG5hbWUpID4gLTEpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmICh3aW5kb3dbbmFtZV0uc291cmNlICE9PSBzb3VyY2UpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBqc2UuY29yZS5kZWJ1Zy5lcnJvcihgRWxlbWVudCB3aXRoIHRoZSBkdXBsaWNhdGUgbmFtZXNwYWNlIG5hbWU6ICR7bm9kZX1gKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoYFRoZSBuYW1lc3BhY2UgXCIke25hbWV9XCIgaXMgYWxyZWFkeSBkZWZpbmVkLiBQbGVhc2Ugc2VsZWN0IGFub3RoZXIgYCArXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGBuYW1lIGZvciB5b3VyIG5hbWVzcGFjZS5gKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnRpbnVlOyAvLyBUaGUgbmFtZXNwYWNlIGlzIGFscmVhZHkgZGVmaW5lZCwgY29udGludWUgbG9vcC5cbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIGlmIChzb3VyY2UgPT09ICcnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB0aHJvdyBuZXcgU3ludGF4RXJyb3IoYE5hbWVzcGFjZSBzb3VyY2UgaXMgZW1wdHk6ICR7bmFtZX1gKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIC8vIENyZWF0ZSBhIG5ldyBuYW1lc3BhY2VzIGluc3RhbmNlIGluIHRoZSBnbG9iYWwgc2NvcGUgKHRoZSBnbG9iYWwgc2NvcGUgaXMgdXNlZCBmb3IgXG4gICAgICAgICAgICAgICAgICAgIC8vIGZhbGxiYWNrIHN1cHBvcnQgb2Ygb2xkIG1vZHVsZSBkZWZpbml0aW9ucykuXG4gICAgICAgICAgICAgICAgICAgIGlmIChuYW1lID09PSAnanNlJykgeyAvLyBNb2RpZnkgdGhlIGVuZ2luZSBvYmplY3Qgd2l0aCBOYW1lc3BhY2UgYXR0cmlidXRlcy5cbiAgICAgICAgICAgICAgICAgICAgICAgIF9jb252ZXJ0RW5naW5lVG9OYW1lc3BhY2Uoc291cmNlLCBjb2xsZWN0aW9ucyk7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB3aW5kb3dbbmFtZV0gPSBuZXcganNlLmNvbnN0cnVjdG9ycy5OYW1lc3BhY2UobmFtZSwgc291cmNlLCBjb2xsZWN0aW9ucyk7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICBwYWdlTmFtZXNwYWNlTmFtZXMucHVzaChuYW1lKTtcbiAgICAgICAgICAgICAgICAgICAgbm9kZS5yZW1vdmVBdHRyaWJ1dGUoYXR0cmlidXRlLm5hbWUpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIC8vIFRocm93IGFuIGVycm9yIGlmIG5vIG5hbWVzcGFjZXMgd2VyZSBmb3VuZC5cbiAgICAgICAgaWYgKHBhZ2VOYW1lc3BhY2VOYW1lcy5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcignTm8gbW9kdWxlIG5hbWVzcGFjZXMgd2VyZSBmb3VuZCwgd2l0aG91dCBuYW1lc3BhY2VzIGl0IGlzIG5vdCBwb3NzaWJsZSB0byAnICtcbiAgICAgICAgICAgICAgICAnbG9hZCBhbnkgbW9kdWxlcy4nKTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIEluaXRpYWxpemUgdGhlIG5hbWVzcGFjZSBpbnN0YW5jZXMuXG4gICAgICAgIGxldCBkZWZlcnJlZENvbGxlY3Rpb24gPSBbXTtcblxuICAgICAgICBmb3IgKGxldCBuYW1lIG9mIHBhZ2VOYW1lc3BhY2VOYW1lcykge1xuICAgICAgICAgICAgbGV0IGRlZmVycmVkID0gJC5EZWZlcnJlZCgpO1xuXG4gICAgICAgICAgICBkZWZlcnJlZENvbGxlY3Rpb24ucHVzaChkZWZlcnJlZCk7XG5cbiAgICAgICAgICAgIHdpbmRvd1tuYW1lXVxuICAgICAgICAgICAgICAgIC5pbml0KClcbiAgICAgICAgICAgICAgICAuZG9uZShkZWZlcnJlZC5yZXNvbHZlKVxuICAgICAgICAgICAgICAgIC5mYWlsKGRlZmVycmVkLnJlamVjdClcbiAgICAgICAgICAgICAgICAuYWx3YXlzKCgpID0+IGpzZS5jb3JlLmRlYnVnLmluZm8oJ05hbWVzcGFjZSBwcm9taXNlcyB3ZXJlIHJlc29sdmVkOiAnLCBuYW1lKSk7XG4gICAgICAgIH1cblxuICAgICAgICAvLyBUcmlnZ2VyIGFuIGV2ZW50IGFmdGVyIHRoZSBlbmdpbmUgaGFzIGluaXRpYWxpemVkIGFsbCBuZXcgbW9kdWxlcy5cbiAgICAgICAgJC53aGVuLmFwcGx5KHVuZGVmaW5lZCwgZGVmZXJyZWRDb2xsZWN0aW9uKS5hbHdheXMoZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgbGV0IGV2ZW50ID0gZG9jdW1lbnQuY3JlYXRlRXZlbnQoJ0V2ZW50Jyk7XG4gICAgICAgICAgICBldmVudC5pbml0RXZlbnQoJ0pTRU5HSU5FX0lOSVRfRklOSVNIRUQnLCB0cnVlLCB0cnVlKTtcbiAgICAgICAgICAgIGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJ2JvZHknKS5kaXNwYXRjaEV2ZW50KGV2ZW50KTtcbiAgICAgICAgICAgIGpzZS5jb3JlLnJlZ2lzdHJ5LnNldCgnanNlRW5kVGltZScsIG5ldyBEYXRlKCkuZ2V0VGltZSgpKTtcbiAgICAgICAgICAgIGpzZS5jb3JlLmRlYnVnLmluZm8oJ0pTIEVuZ2luZSBMb2FkaW5nIFRpbWU6ICcsIGpzZS5jb3JlLnJlZ2lzdHJ5LmdldCgnanNlRW5kVGltZScpXG4gICAgICAgICAgICAgICAgLSBqc2UuY29yZS5yZWdpc3RyeS5nZXQoJ2pzZVN0YXJ0VGltZScpLCAnbXMnKTtcbiAgICAgICAgICAgIGlmICh3aW5kb3cuQ3lwcmVzcykge1xuICAgICAgICAgICAgICAgIHdpbmRvdy5qc2VSZWFkeSA9IHRydWU7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIHJldHVybiBwYWdlTmFtZXNwYWNlTmFtZXM7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQ29udmVydCB0aGUgXCJqc2VcIiBvYmplY3QgdG8gYSBOYW1lc3BhY2UgY29tcGF0aWJsZSBvYmplY3QuXG4gICAgICpcbiAgICAgKiBJbiBvcmRlciB0byBzdXBwb3J0IHRoZSBcImpzZVwiIG5hbWVzcGFjZSBuYW1lIGZvciB0aGUgY29yZSBtb2R1bGVzIHBsYWNlZCBpbiB0aGUgXCJKU0VuZ2luZVwiXG4gICAgICogZGlyZWN0b3J5LCB3ZSB3aWxsIG5lZWQgdG8gbW9kaWZ5IHRoZSBhbHJlYWR5IGV4aXN0aW5nIFwianNlXCIgb2JqZWN0IHNvIHRoYXQgaXQgY2FuIG9wZXJhdGVcbiAgICAgKiBhcyBhIG5hbWVzcGFjZSB3aXRob3V0IGxvc2luZyBpdHMgaW5pdGlhbCBhdHRyaWJ1dGVzLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtTdHJpbmd9IHNvdXJjZSBOYW1lc3BhY2Ugc291cmNlIHBhdGggZm9yIHRoZSBtb2R1bGUgZmlsZXMuXG4gICAgICogQHBhcmFtIHtBcnJheX0gY29sbGVjdGlvbnMgQ29udGFpbiBpbnN0YW5jZXMgdG8gdGhlIHByb3RvdHlwZSBjb2xsZWN0aW9uIGluc3RhbmNlcy5cbiAgICAgKlxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgZnVuY3Rpb24gX2NvbnZlcnRFbmdpbmVUb05hbWVzcGFjZShzb3VyY2UsIGNvbGxlY3Rpb25zKSB7XG4gICAgICAgIGxldCB0bXBOYW1lc3BhY2UgPSBuZXcganNlLmNvbnN0cnVjdG9ycy5OYW1lc3BhY2UoJ2pzZScsIHNvdXJjZSwgY29sbGVjdGlvbnMpO1xuICAgICAgICBqc2UubmFtZSA9IHRtcE5hbWVzcGFjZS5uYW1lO1xuICAgICAgICBqc2Uuc291cmNlID0gdG1wTmFtZXNwYWNlLnNvdXJjZTtcbiAgICAgICAganNlLmNvbGxlY3Rpb25zID0gdG1wTmFtZXNwYWNlLmNvbGxlY3Rpb25zO1xuICAgICAgICBqc2UuaW5pdCA9IGpzZS5jb25zdHJ1Y3RvcnMuTmFtZXNwYWNlLnByb3RvdHlwZS5pbml0O1xuICAgIH1cblxuICAgIC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICAgIC8vIFBVQkxJQyBGVU5DVElPTlNcbiAgICAvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIGVuZ2luZS5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7QXJyYXl9IGNvbGxlY3Rpb25zIENvbnRhaW5zIHRoZSBzdXBwb3J0ZWQgbW9kdWxlIGNvbGxlY3Rpb24gZGF0YS5cbiAgICAgKi9cbiAgICBleHBvcnRzLmluaXQgPSBmdW5jdGlvbiAoY29sbGVjdGlvbnMpIHtcbiAgICAgICAgLy8gR2xvYmFsIGVycm9yIGhhbmRsZXIgdGhhdCBleGVjdXRlcyBpZiBhbiB1bmNhdWdodCBKUyBlcnJvciBvY2N1cnMgb24gcGFnZS5cbiAgICAgICAganNlLmNvcmUuZGVidWcuYmluZEdsb2JhbEVycm9ySGFuZGxlcigpO1xuXG4gICAgICAgIC8vIEluaXRpYWxpemUgdGhlIHBhZ2UgbmFtZXNwYWNlcy5cbiAgICAgICAgbGV0IHBhZ2VOYW1lc3BhY2VOYW1lcyA9IF9pbml0TmFtZXNwYWNlcyhjb2xsZWN0aW9ucyk7XG5cbiAgICAgICAgLy8gTG9nIHRoZSBwYWdlIG5hbWVzcGFjZXMgKGZvciBkZWJ1Z2dpbmcgb25seSkuXG4gICAgICAgIGpzZS5jb3JlLmRlYnVnLmluZm8oJ1BhZ2UgTmFtZXNwYWNlczogJyArIHBhZ2VOYW1lc3BhY2VOYW1lcy5qb2luKCkpO1xuXG4gICAgICAgIC8vIFVwZGF0ZSB0aGUgZW5naW5lIHJlZ2lzdHJ5LlxuICAgICAgICBqc2UuY29yZS5yZWdpc3RyeS5zZXQoJ25hbWVzcGFjZXMnLCBwYWdlTmFtZXNwYWNlTmFtZXMpO1xuICAgIH07XG5cbn0pKGpzZS5jb3JlLmVuZ2luZSk7XG4iLCIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGV4dGVuc2lvbnMuanMgMjAxNy0wMy0wM1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTcgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogSlNFIEV4dGVuc2lvbnNcbiAqXG4gKiBFeHRlbmQgdGhlIGRlZmF1bHQgYmVoYXZpb3VyIG9mIGVuZ2luZSBjb21wb25lbnRzIG9yIGV4dGVybmFsIHBsdWdpbnMgYmVmb3JlIHRoZXkgYXJlIGxvYWRlZC5cbiAqXG4gKiBAbW9kdWxlIEpTRS9Db3JlL2V4dGVuZFxuICovXG4oZnVuY3Rpb24gKCkge1xuXG4gICAgJ3VzZSBzdHJpY3QnO1xuXG4gICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gICAgLy8gUEFSU0UgTU9EVUxFIERBVEEgSlFVRVJZIEVYVEVOU0lPTlxuICAgIC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXG4gICAgJC5mbi5leHRlbmQoe1xuICAgICAgICBwYXJzZU1vZHVsZURhdGE6IGZ1bmN0aW9uIChtb2R1bGVOYW1lKSB7XG4gICAgICAgICAgICBpZiAoIW1vZHVsZU5hbWUgfHwgbW9kdWxlTmFtZSA9PT0gJycpIHtcbiAgICAgICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoJ01vZHVsZSBuYW1lIHdhcyBub3QgcHJvdmlkZWQgYXMgYW4gYXJndW1lbnQuJylcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgY29uc3QgaW5pdGlhbERhdGEgPSAkKHRoaXMpLmRhdGEoKTtcbiAgICAgICAgICAgIGNvbnN0IGZpbHRlcmVkRGF0YSA9IHt9O1xuXG4gICAgICAgICAgICAvLyBTZWFyY2hlcyBmb3IgbW9kdWxlIHJlbGV2YW50IGRhdGEgaW5zaWRlIHRoZSBtYWluLWRhdGEtb2JqZWN0LiBEYXRhIGZvciBvdGhlciB3aWRnZXRzIHdpbGwgbm90IGdldCBcbiAgICAgICAgICAgIC8vIHBhc3NlZCB0byB0aGlzIHdpZGdldC5cbiAgICAgICAgICAgICQuZWFjaChpbml0aWFsRGF0YSwgZnVuY3Rpb24gKGtleSwgdmFsdWUpIHtcbiAgICAgICAgICAgICAgICBpZiAoa2V5LmluZGV4T2YobW9kdWxlTmFtZSkgPT09IDAgfHwga2V5LmluZGV4T2YobW9kdWxlTmFtZS50b0xvd2VyQ2FzZSgpKSA9PT0gMCkge1xuICAgICAgICAgICAgICAgICAgICBsZXQgbmV3S2V5ID0ga2V5LnN1YnN0cihtb2R1bGVOYW1lLmxlbmd0aCk7XG4gICAgICAgICAgICAgICAgICAgIG5ld0tleSA9IG5ld0tleS5zdWJzdHIoMCwgMSkudG9Mb3dlckNhc2UoKSArIG5ld0tleS5zdWJzdHIoMSk7XG4gICAgICAgICAgICAgICAgICAgIGZpbHRlcmVkRGF0YVtuZXdLZXldID0gdmFsdWU7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHJldHVybiBmaWx0ZXJlZERhdGE7XG4gICAgICAgIH1cbiAgICB9KTtcblxuICAgIC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICAgIC8vIERBVEVQSUNLRVIgUkVHSU9OQUwgSU5GT1xuICAgIC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXG4gICAgaWYgKCQuZGF0ZXBpY2tlciAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICQuZGF0ZXBpY2tlci5yZWdpb25hbC5kZSA9IHtcbiAgICAgICAgICAgIGRhdGVGb3JtYXQ6ICdkZC5tbS55eScsXG4gICAgICAgICAgICBmaXJzdERheTogMSxcbiAgICAgICAgICAgIGlzUlRMOiBmYWxzZVxuICAgICAgICB9O1xuICAgICAgICAkLmRhdGVwaWNrZXIuc2V0RGVmYXVsdHMoJC5kYXRlcGlja2VyLnJlZ2lvbmFsLmRlKTtcbiAgICB9XG59KCkpO1xuIiwiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBpbml0aWFsaXplLmpzIDIwMTYtMDktMDhcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4ndXNlIHN0cmljdCc7XG5cbi8qKlxuICogSlNFIEluaXRpYWxpemF0aW9uIE1vZHVsZVxuICpcbiAqIFRoZSBkb2N1bWVudC1yZWFkeSBldmVudCBvZiB0aGUgcGFnZSB3aWxsIHRyaWdnZXIgdGhlIEphdmFTY3JpcHQgRW5naW5lIGluaXRpYWxpemF0aW9uLiBUaGVcbiAqIGVuZ2luZSByZXF1aXJlcyBhIGdsb2JhbCBjb25maWd1cmF0aW9uIG9iamVjdCBcIndpbmRvdy5KU0VuZ2luZUNvbmZpZ3VyYXRpb25cIiB0byBiZSBwcmUtZGVmaW5lZFxuICogaW4gb3JkZXIgdG8gcmV0cmlldmUgdGhlIGJhc2ljIGNvbmZpZ3VyYXRpb24gaW5mby4gQWZ0ZXIgYSBzdWNjZXNzZnVsIGluaXRpYWxpemF0aW9uIHRoaXMgb2JqZWN0XG4gKiBpcyByZW1vdmVkIGZyb20gdGhlIHdpbmRvdyBvYmplY3QuXG4gKlxuICogIyMjIENvbmZpZ3VyYXRpb24gU2FtcGxlXG4gKlxuICogYGBganNcbiAqIHdpbmRvdy5KU0VuZ2luZUNvbmZpZ3VyYXRpb24gPSB7XG4gKiAgIGVudmlyb25tZW50OiAncHJvZHVjdGlvbicsXG4gKiAgIGFwcFVybDogJ2h0dHA6Ly9hcHAuY29tJyxcbiAqICAgY29sbGVjdGlvbnM6IFtcbiAqICAgICB7bmFtZTogJ2NvbnRyb2xsZXJzJywgYXR0cmlidXRlOiAnY29udHJvbGxlcid9XG4gKiAgIF0sICBcbiAqICAgdHJhbnNsYXRpb25zOiB7XG4gKiAgICAgJ3NlY3Rpb25OYW1lJzogeyAndHJhbnNsYXRpb25LZXknOiAndHJhbnNsYXRpb25WYWx1ZScgfSxcbiAqICAgICAnYW5vdGhlclNlY3Rpb24nOiB7IC4uLiB9XG4gKiAgIH0sXG4gKiAgIGxhbmd1YWdlQ29kZTogJ2VuJyxcbiAqICAgcGFnZVRva2VuOiAnOWFzZDdmOTg3OXNkOGY3OXM5OHM3ZDk4ZidcbiAqIH07XG4gKiBgYGBcbiAqXG4gKiBAbW9kdWxlIEpTRS9Db3JlL2luaXRpYWxpemVcbiAqL1xuXG4vLyBJbml0aWFsaXplIGJhc2UgZW5naW5lIG9iamVjdC4gRXZlcnkgb3RoZXIgcGFydCBvZiB0aGUgZW5naW5lIHdpbGwgcmVmZXIgdG8gdGhpc1xuLy8gY2VudHJhbCBvYmplY3QgZm9yIHRoZSBjb3JlIG9wZXJhdGlvbnMuXG53aW5kb3cuanNlID0ge1xuICAgIGNvcmU6IHt9LFxuICAgIGxpYnM6IHt9LFxuICAgIGNvbnN0cnVjdG9yczoge31cbn07XG5cbi8vIEluaXRpYWxpemUgdGhlIGVuZ2luZSBvbiB3aW5kb3cgbG9hZC4gXG5kb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCdET01Db250ZW50TG9hZGVkJywgZnVuY3Rpb24gKCkge1xuICAgIHRyeSB7XG4gICAgICAgIC8vIENoZWNrIGlmIGdsb2JhbCBKU0VuZ2luZUNvbmZpZ3VyYXRpb24gb2JqZWN0IGlzIGRlZmluZWQuXG4gICAgICAgIGlmICh3aW5kb3cuSlNFbmdpbmVDb25maWd1cmF0aW9uID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcignVGhlIFwid2luZG93LkpTRW5naW5lQ29uZmlndXJhdGlvblwiIG9iamVjdCBpcyBub3QgZGVmaW5lZCBpbiB0aGUgZ2xvYmFsIHNjb3BlLiAnICtcbiAgICAgICAgICAgICAgICAnVGhpcyBvYmplY3QgaXMgcmVxdWlyZWQgYnkgdGhlIGVuZ2luZSB1cG9uIGl0cyBpbml0aWFsaXphdGlvbi4nKTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIFBhcnNlIEpTRW5naW5lQ29uZmlndXJhdGlvbiBvYmplY3QuXG4gICAgICAgIGpzZS5jb3JlLmNvbmZpZy5pbml0KHdpbmRvdy5KU0VuZ2luZUNvbmZpZ3VyYXRpb24pO1xuXG4gICAgICAgIC8vIFN0b3JlIHRoZSBKU0Ugc3RhcnQgdGltZSBpbiByZWdpc3RyeSAocHJvZmlsaW5nKS4gXG4gICAgICAgIGpzZS5jb3JlLnJlZ2lzdHJ5LnNldCgnanNlU3RhcnRUaW1lJywgRGF0ZS5ub3coKSk7XG5cbiAgICAgICAgLy8gSW5pdGlhbGl6ZSB0aGUgbW9kdWxlIGNvbGxlY3Rpb25zLlxuICAgICAgICBqc2UuY29yZS5lbmdpbmUuaW5pdChqc2UuY29yZS5jb25maWcuZ2V0KCdjb2xsZWN0aW9ucycpKTtcbiAgICB9IGNhdGNoIChleGNlcHRpb24pIHtcbiAgICAgICAganNlLmNvcmUuZGVidWcuZXJyb3IoJ1VuZXhwZWN0ZWQgZXJyb3IgZHVyaW5nIEpTIEVuZ2luZSBpbml0aWFsaXphdGlvbiEnLCBleGNlcHRpb24pO1xuICAgICAgICAvLyBJbmZvcm0gdGhlIGVuZ2luZSBhYm91dCB0aGUgZXhjZXB0aW9uLlxuICAgICAgICBjb25zdCBldmVudCA9IGRvY3VtZW50LmNyZWF0ZUV2ZW50KCdDdXN0b21FdmVudCcpO1xuICAgICAgICBldmVudC5pbml0Q3VzdG9tRXZlbnQoJ2Vycm9yJywgdHJ1ZSwgdHJ1ZSwgZXhjZXB0aW9uKTtcbiAgICAgICAgd2luZG93LmRpc3BhdGNoRXZlbnQoZXZlbnQpO1xuICAgIH1cblxuICAgIFNWR0luamVjdC5zZXRPcHRpb25zKHtcbiAgICAgICAgb25GYWlsOiBmdW5jdGlvbiAoaW1nLCBzdmcpIHtcbiAgICAgICAgICAgIGltZy5jbGFzc0xpc3QucmVtb3ZlKCdzdmctLWluamVjdCcpOyAvLyBpZiBpbmplY3Rpb24gZmFpbHMgc2hvdyB0aGUgaW1nIGVsZW1lbnRcbiAgICAgICAgfVxuICAgIH0pO1xuICAgIC8vIGluamVjdCBpbWFnZXMgd2l0aCBhbiAuc3ZnIGZpbGUgZW5kaW5nXG4gICAgU1ZHSW5qZWN0KGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJ2ltZy5zdmctLWluamVjdCcpLCB7XG4gICAgICAgIG9uQWxsRmluaXNoOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAvLyB0aGUgU1ZHIGluamVjdGlvbiBoYXMgZmluaXNoZWQgZm9yIGFsbCB0aHJlZSBpbWFnZXNcblxuICAgICAgICB9XG4gICAgfSk7XG59KTsgXG4iLCIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGxhbmcuanMgMjAxNi0wOC0yM1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbmpzZS5jb3JlLmxhbmcgPSBqc2UuY29yZS5sYW5nIHx8IHt9O1xuXG4vKipcbiAqIEpTRSBMb2NhbGl6YXRpb24gTGlicmFyeVxuICpcbiAqIFRoZSBnbG9iYWwgTGFuZyBvYmplY3QgY29udGFpbnMgbGFuZ3VhZ2UgaW5mb3JtYXRpb24gdGhhdCBjYW4gYmUgZWFzaWx5IHVzZWQgaW4geW91clxuICogSmF2YVNjcmlwdCBjb2RlLiBUaGUgb2JqZWN0IGNvbnRhaW5zIGNvbnN0YW5jZSB0cmFuc2xhdGlvbnMgYW5kIGR5bmFtaWMgc2VjdGlvbnMgdGhhdFxuICogY2FuIGJlIGxvYWRlZCBhbmQgdXNlZCBpbiBkaWZmZXJlbnQgcGFnZS5cbiAqXG4gKiAjIyMjIEltcG9ydGFudFxuICogVGhlIGVuZ2luZSB3aWxsIGF1dG9tYXRpY2FsbHkgbG9hZCB0cmFuc2xhdGlvbiBzZWN0aW9ucyB0aGF0IGFyZSBwcmVzZW50IGluIHRoZVxuICogYHdpbmRvdy5KU0VuZ2luZUNvbmZpZ3VyYXRpb24udHJhbnNsYXRpb25zYCBwcm9wZXJ0eSB1cG9uIGluaXRpYWxpemF0aW9uLiBGb3IgbW9yZVxuICogaW5mb3JtYXRpb24gbG9vayBhdCB0aGUgXCJjb3JlL2luaXRpYWxpemVcIiBwYWdlIG9mIGRvY3VtZW50YXRpb24gcmVmZXJlbmNlLlxuICpcbiAqIGBgYGphdmFzY3JpcHRcbiAqIGpzZS5jb3JlLmxhbmcuYWRkU2VjdGlvbignc2VjdGlvbk5hbWUnLCB7IHRyYW5zbGF0aW9uS2V5OiAndHJhbnNsYXRpb25WYWx1ZScgfSk7IC8vIEFkZCB0cmFuc2xhdGlvbiBzZWN0aW9uLlxuICoganNlLmNvcmUudHJhbnNsYXRlKCd0cmFuc2xhdGlvbktleScsICdzZWN0aW9uTmFtZScpOyAvLyBHZXQgdGhlIHRyYW5zbGF0ZWQgc3RyaW5nLlxuICoganNlLmNvcmUuZ2V0U2VjdGlvbnMoKTsgLy8gcmV0dXJucyBhcnJheSB3aXRoIHNlY3Rpb25zIGUuZy4gWydhZG1pbl9idXR0b25zJywgJ2dlbmVyYWwnXVxuICogYGBgXG4gKlxuICogQG1vZHVsZSBKU0UvQ29yZS9sYW5nXG4gKi9cbihmdW5jdGlvbiAoZXhwb3J0cykge1xuXG4gICAgJ3VzZSBzdHJpY3QnO1xuXG4gICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gICAgLy8gVkFSSUFCTEVTXG4gICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cbiAgICAvKipcbiAgICAgKiBDb250YWlucyB2YXJpb3VzIHRyYW5zbGF0aW9uIHNlY3Rpb25zLlxuICAgICAqXG4gICAgICogQHR5cGUge09iamVjdH1cbiAgICAgKi9cbiAgICBjb25zdCBzZWN0aW9ucyA9IHt9O1xuXG4gICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gICAgLy8gUFVCTElDIE1FVEhPRFNcbiAgICAvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblxuICAgIC8qKlxuICAgICAqIEFkZCBhIHRyYW5zbGF0aW9uIHNlY3Rpb24uXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gbmFtZSBOYW1lIG9mIHRoZSBzZWN0aW9uLCB1c2VkIGxhdGVyIGZvciBhY2Nlc3NpbmcgdHJhbnNsYXRpb24gc3RyaW5ncy5cbiAgICAgKiBAcGFyYW0ge09iamVjdH0gdHJhbnNsYXRpb25zIEtleSAtIHZhbHVlIG9iamVjdCBjb250YWluaW5nIHRoZSB0cmFuc2xhdGlvbnMuXG4gICAgICpcbiAgICAgKiBAdGhyb3dzIHtFcnJvcn0gSWYgXCJuYW1lXCIgb3IgXCJ0cmFuc2xhdGlvbnNcIiBhcmd1bWVudHMgYXJlIGludmFsaWQuXG4gICAgICovXG4gICAgZXhwb3J0cy5hZGRTZWN0aW9uID0gZnVuY3Rpb24gKG5hbWUsIHRyYW5zbGF0aW9ucykge1xuICAgICAgICBpZiAodHlwZW9mIG5hbWUgIT09ICdzdHJpbmcnIHx8IHR5cGVvZiB0cmFuc2xhdGlvbnMgIT09ICdvYmplY3QnIHx8IHRyYW5zbGF0aW9ucyA9PT0gbnVsbCkge1xuICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKGB3aW5kb3cuZ3guY29yZS5sYW5nLmFkZFNlY3Rpb246IEludmFsaWQgYXJndW1lbnRzIHByb3ZpZGVkIChuYW1lOiAke3R5cGVvZiBuYW1lfSwgYFxuICAgICAgICAgICAgICAgICsgYHRyYW5zbGF0aW9uczogJHt0eXBlb2YgdHJhbnNsYXRpb25zfSkuYCk7XG4gICAgICAgIH1cbiAgICAgICAgc2VjdGlvbnNbbmFtZV0gPSB0cmFuc2xhdGlvbnM7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIEdldCBsb2FkZWQgdHJhbnNsYXRpb24gc2VjdGlvbnMuXG4gICAgICpcbiAgICAgKiBVc2VmdWwgZm9yIGFzc2VydGluZyBwcmVzZW50IHRyYW5zbGF0aW9uIHNlY3Rpb25zLlxuICAgICAqXG4gICAgICogQHJldHVybiB7QXJyYXl9IFJldHVybnMgYXJyYXkgd2l0aCB0aGUgZXhpc3Rpbmcgc2VjdGlvbnMuXG4gICAgICovXG4gICAgZXhwb3J0cy5nZXRTZWN0aW9ucyA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgY29uc3QgcmVzdWx0ID0gW107XG5cbiAgICAgICAgZm9yIChsZXQgc2VjdGlvbiBpbiBzZWN0aW9ucykge1xuICAgICAgICAgICAgcmVzdWx0LnB1c2goc2VjdGlvbik7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gcmVzdWx0O1xuICAgIH07XG4gICAgXG4gICAgXG4gICAgLyoqXG4gICAgICogUmV0dXJucyBhbiBlbnRpcmUgc2VjdGlvbi5cbiAgICAgKlxuICAgICAqIEBwYXJhbSBzZWN0aW9uXG4gICAgICogQHJldHVybnMgeyp9XG4gICAgICovXG4gICAgZXhwb3J0cy5nZXRTZWN0aW9uID0gZnVuY3Rpb24oc2VjdGlvbikge1xuICAgICAgICBpZiAodHlwZW9mIHNlY3Rpb24gIT09ICdzdHJpbmcnKSB7XG4gICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoYEludmFsaWQgYXJndW1lbnQgcHJvdmlkZWQgdG8gZ2V0U2VjdGlvbiAoc2VjdGlvbjogJHt0eXBlb2Ygc2VjdGlvbn0pLmApO1xuICAgICAgICB9XG4gICAgICAgIFxuICAgICAgICBpZiAoc2VjdGlvbnNbc2VjdGlvbl0gPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKGBTZWN0aW9uICR7c2VjdGlvbn0gaXMgdW5hdmFpbGFibGUuYCk7XG4gICAgICAgIH1cbiAgICAgICAgXG4gICAgICAgIHJldHVybiBzZWN0aW9uc1tzZWN0aW9uXTtcbiAgICB9XG4gICAgXG5cbiAgICAvKipcbiAgICAgKiBUcmFuc2xhdGUgc3RyaW5nIGluIEphdmFzY3JpcHQgY29kZS5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSBwaHJhc2UgTmFtZSBvZiB0aGUgcGhyYXNlIGNvbnRhaW5pbmcgdGhlIHRyYW5zbGF0aW9uLlxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSBzZWN0aW9uIFNlY3Rpb24gbmFtZSBjb250YWluaW5nIHRoZSB0cmFuc2xhdGlvbiBzdHJpbmcuXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIHtTdHJpbmd9IFJldHVybnMgdGhlIHRyYW5zbGF0ZWQgc3RyaW5nLlxuICAgICAqXG4gICAgICogQHRocm93cyB7RXJyb3J9IElmIHByb3ZpZGVkIGFyZ3VtZW50cyBhcmUgaW52YWxpZC5cbiAgICAgKiBAdGhyb3dzIHtFcnJvcn0gSWYgcmVxdWlyZWQgc2VjdGlvbiBkb2VzIG5vdCBleGlzdCBvciB0cmFuc2xhdGlvbiBjb3VsZCBub3QgYmUgZm91bmQuXG4gICAgICovXG4gICAgZXhwb3J0cy50cmFuc2xhdGUgPSBmdW5jdGlvbiAocGhyYXNlLCBzZWN0aW9uKSB7XG4gICAgICAgIC8vIFZhbGlkYXRlIHByb3ZpZGVkIGFyZ3VtZW50cy5cbiAgICAgICAgaWYgKHR5cGVvZiBwaHJhc2UgIT09ICdzdHJpbmcnIHx8IHR5cGVvZiBzZWN0aW9uICE9PSAnc3RyaW5nJykge1xuICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKGBJbnZhbGlkIGFyZ3VtZW50cyBwcm92aWRlZCBpbiB0cmFuc2xhdGUgbWV0aG9kIChwaHJhc2U6ICR7dHlwZW9mIHBocmFzZX0sIGBcbiAgICAgICAgICAgICAgICArIGBzZWN0aW9uOiAke3R5cGVvZiBzZWN0aW9ufSkuYCk7XG4gICAgICAgIH1cblxuICAgICAgICAvLyBDaGVjayBpZiB0cmFuc2xhdGlvbiBleGlzdHMuXG4gICAgICAgIGlmIChzZWN0aW9uc1tzZWN0aW9uXSA9PT0gdW5kZWZpbmVkIHx8IHNlY3Rpb25zW3NlY3Rpb25dW3BocmFzZV0gPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAganNlLmNvcmUuZGVidWcud2FybihgQ291bGQgbm90IGZvdW5kIHJlcXVlc3RlZCB0cmFuc2xhdGlvbiAocGhyYXNlOiAke3BocmFzZX0sIHNlY3Rpb246ICR7c2VjdGlvbn0pLmApO1xuICAgICAgICAgICAgcmV0dXJuICd7JyArIHNlY3Rpb24gKyAnLicgKyBwaHJhc2UgKyAnfSc7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gc2VjdGlvbnNbc2VjdGlvbl1bcGhyYXNlXTtcbiAgICB9O1xuXG59KGpzZS5jb3JlLmxhbmcpKTtcbiIsIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gbWFpbi5qcyAyMDE4LTA2LTA3XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxOCBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLy8gSW1wb3J0IGluaXRpYWxpemF0aW9uIHNjcmlwdC4gXG5pbXBvcnQgJy4vaW5pdGlhbGl6ZSc7XG5cbi8vIEltcG9ydCB0aGUgY29uc3RydWN0b3IgZmlsZXMuIFxuaW1wb3J0ICcuLi9jb25zdHJ1Y3RvcnMvY29sbGVjdGlvbic7XG5pbXBvcnQgJy4uL2NvbnN0cnVjdG9ycy9kYXRhX2JpbmRpbmcnO1xuaW1wb3J0ICcuLi9jb25zdHJ1Y3RvcnMvbW9kdWxlJztcbmltcG9ydCAnLi4vY29uc3RydWN0b3JzL25hbWVzcGFjZSc7XG5cbi8vIEltcG9ydCB0aGUgY29yZSBmaWxlcy4gXG5pbXBvcnQgJy4vYWJvdXQnO1xuaW1wb3J0ICcuL2NvbmZpZyc7XG5pbXBvcnQgJy4vZGVidWcnO1xuaW1wb3J0ICcuL2VuZ2luZSc7XG5pbXBvcnQgJy4vZXh0ZW5kJztcbmltcG9ydCAnLi9sYW5nJztcbmltcG9ydCAnLi9yZXF1aXJlJztcbmltcG9ydCAnLi9tb2R1bGVfbG9hZGVyJztcbmltcG9ydCAnLi9wb2x5ZmlsbHMnO1xuaW1wb3J0ICcuL3JlZ2lzdHJ5JztcbmltcG9ydCAnLi92dWUnOyIsIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gbW9kdWxlX2xvYWRlci5qcyAyMDE4LTA5LTEyXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuanNlLmNvcmUubW9kdWxlX2xvYWRlciA9IGpzZS5jb3JlLm1vZHVsZV9sb2FkZXIgfHwge307XG5cbi8qKlxuICogSlNFIE1vZHVsZSBMb2FkZXJcbiAqXG4gKiBUaGlzIG9iamVjdCBpcyBhbiBhZGFwdGVyIGJldHdlZW4gdGhlIGVuZ2luZSBhbmQgUmVxdWlyZUpTIHdoaWNoIGlzIHVzZWQgdG8gbG9hZCB0aGUgcmVxdWlyZWQgZmlsZXNcbiAqIGludG8gdGhlIGNsaWVudC5cbiAqXG4gKiBAbW9kdWxlIEpTRS9Db3JlL21vZHVsZV9sb2FkZXJcbiAqL1xuKGZ1bmN0aW9uIChleHBvcnRzKSB7XG5cbiAgICAndXNlIHN0cmljdCc7XG5cbiAgICAvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAgICAvLyBQUklWQVRFIE1FVEhPRFNcbiAgICAvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblxuICAgIC8qKlxuICAgICAqIExvYWQgQ1NTIGZpbGUuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gdXJsIEFic29sdXRlIFVSTCBvZiB0aGUgQ1NTIGZpbGUgdG8gYmUgbG9hZGVkLlxuICAgICAqXG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBmdW5jdGlvbiBfbG9hZENzcyh1cmwpIHtcbiAgICAgICAgY29uc3QgbGluayA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2xpbmsnKTtcbiAgICAgICAgbGluay50eXBlID0gJ3RleHQvY3NzJztcbiAgICAgICAgbGluay5yZWwgPSAnc3R5bGVzaGVldCc7XG4gICAgICAgIGxpbmsuaHJlZiA9IHVybDtcbiAgICAgICAgZG9jdW1lbnQuZ2V0RWxlbWVudHNCeVRhZ05hbWUoJ2hlYWQnKVswXS5hcHBlbmRDaGlsZChsaW5rKTtcbiAgICB9XG5cbiAgICAvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAgICAvLyBQVUJMSUMgTUVUSE9EU1xuICAgIC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgbW9kdWxlIGxvYWRlci5cbiAgICAgKlxuICAgICAqIEV4ZWN1dGUgdGhpcyBtZXRob2QgYWZ0ZXIgdGhlIGVuZ2luZSBjb25maWcgaXMgaW5pdGlhbGl6ZWQuIEl0IHdpbGwgY29uZmlndXJlIHJlcXVpcmUuanNcbiAgICAgKiBzbyB0aGF0IGl0IHdpbGwgYmUgYWJsZSB0byBmaW5kIHRoZSBwcm9qZWN0IGZpbGVzLlxuICAgICAqXG4gICAgICogVGhlIGNhY2hlIGJ1c3RpbmcgbWV0aG9kIHdpbGwgdHJ5IHRvIGNyZWF0ZSBhIG51bWJlciBiYXNlZCBvbiB0aGUgY3VycmVudCBzaG9wIHZlcnNpb24uXG4gICAgICovXG4gICAgZXhwb3J0cy5pbml0ID0gZnVuY3Rpb24gKCkge1xuICAgICAgICBsZXQgY2FjaGVCdXN0ID0gJyc7XG5cbiAgICAgICAgaWYgKGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2Vudmlyb25tZW50JykgPT09ICdwcm9kdWN0aW9uJyAmJlxuICAgICAgICAgICAganNlLmNvcmUuY29uZmlnLmdldCgnYnVzdEZpbGVzJykgPT09IGZhbHNlICYmXG4gICAgICAgICAgICBqc2UuY29yZS5jb25maWcuZ2V0KCdjYWNoZVRva2VuJykpIHtcbiAgICAgICAgICAgIGNhY2hlQnVzdCA9IGBidXN0PSR7anNlLmNvcmUuY29uZmlnLmdldCgnY2FjaGVUb2tlbicpfWA7XG4gICAgICAgIH1cblxuICAgICAgICBjb25zdCBjb25maWcgPSB7XG4gICAgICAgICAgICBiYXNlVXJsOiBqc2UuY29yZS5jb25maWcuZ2V0KCdhcHBVcmwnKSxcbiAgICAgICAgICAgIHVybEFyZ3M6IGNhY2hlQnVzdCxcbiAgICAgICAgICAgIG9uRXJyb3I6IGZ1bmN0aW9uIChlcnJvcikge1xuICAgICAgICAgICAgICAgIGpzZS5jb3JlLmRlYnVnLmVycm9yKCdSZXF1aXJlSlMgRXJyb3I6JywgZXJyb3IpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuXG4gICAgICAgIHdpbmRvdy5yZXF1aXJlLmNvbmZpZyhjb25maWcpO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBSZXF1aXJlIEpTIGFuZCBDU1MgZmlsZXMgLlxuICAgICAqXG4gICAgICogTm90aWNlOiBUaGVyZSdzIG5vIGNvbmNyZXRlIHdheSB0byBkZXRlcm1pbmUgd2hlbiBDU1MgZGVwZW5kZW5jaWVzIGFyZSBsb2FkZWQuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1N0cmluZ1tdfSBkZXBlbmRlbmNpZXMgRGVwZW5kZW5jeSBVUkxzLlxuICAgICAqIEBwYXJhbSB7RnVuY3Rpb259IGNhbGxiYWNrIENhbGxiYWNrIG1ldGhvZCB0byBiZSBjYWxsZWQgb25jZSB0aGUgZGVwZW5kZW5jaWVzIGFyZSBsb2FkZWQuXG4gICAgICovXG4gICAgZXhwb3J0cy5yZXF1aXJlID0gZnVuY3Rpb24gKGRlcGVuZGVuY2llcywgY2FsbGJhY2spIHtcbiAgICAgICAgZm9yIChsZXQgZGVwZW5kZW5jeSBvZiBkZXBlbmRlbmNpZXMpIHtcbiAgICAgICAgICAgIGlmIChkZXBlbmRlbmN5LmluY2x1ZGVzKCcuY3NzJykpIHtcbiAgICAgICAgICAgICAgICBfbG9hZENzcyhkZXBlbmRlbmN5KTtcbiAgICAgICAgICAgICAgICBjb25zdCBpbmRleCA9IGRlcGVuZGVuY2llcy5pbmRleE9mKGRlcGVuZGVuY3kpO1xuICAgICAgICAgICAgICAgIGRlcGVuZGVuY2llcy5zcGxpY2UoaW5kZXgsIDEpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgaWYgKGRlcGVuZGVuY2llcy5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICAgIGNhbGxiYWNrKCk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICB3aW5kb3cucmVxdWlyZShkZXBlbmRlbmNpZXMsIGNhbGxiYWNrKTtcbiAgICAgICAgfVxuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBMb2FkIGEgbW9kdWxlIGZpbGUgd2l0aCB0aGUgdXNlIG9mIHJlcXVpcmVqcy5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSAkZWxlbWVudCBTZWxlY3RvciBvZiB0aGUgZWxlbWVudCB3aGljaCBoYXMgdGhlIG1vZHVsZSBkZWZpbml0aW9uLlxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSBuYW1lIE1vZHVsZSBuYW1lIHRvIGJlIGxvYWRlZC4gTW9kdWxlcyBoYXZlIHRoZSBzYW1lIG5hbWVzIGFzIHRoZWlyIGZpbGVzLlxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBjb2xsZWN0aW9uIEN1cnJlbnQgY29sbGVjdGlvbiBpbnN0YW5jZS5cbiAgICAgKlxuICAgICAqIEByZXR1cm4ge09iamVjdH0gUmV0dXJucyBhIHByb21pc2Ugb2JqZWN0IHRvIGJlIHJlc29sdmVkIHdpdGggdGhlIG1vZHVsZSBpbnN0YW5jZSBhcyBhIHBhcmFtZXRlci5cbiAgICAgKi9cbiAgICBleHBvcnRzLmxvYWQgPSBmdW5jdGlvbiAoJGVsZW1lbnQsIG5hbWUsIGNvbGxlY3Rpb24pIHtcbiAgICAgICAgY29uc3QgZGVmZXJyZWQgPSAkLkRlZmVycmVkKCk7XG5cbiAgICAgICAgdHJ5IHtcbiAgICAgICAgICAgIGlmIChuYW1lID09PSAnJykge1xuICAgICAgICAgICAgICAgIGRlZmVycmVkLnJlamVjdChuZXcgRXJyb3IoJ01vZHVsZSBuYW1lIGNhbm5vdCBiZSBlbXB0eS4nKSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGNvbnN0IGJhc2VNb2R1bGVOYW1lID0gbmFtZS5yZXBsYWNlKC8uKlxcLyguKikkLywgJyQxJyk7IC8vIE5hbWUgd2l0aG91dCB0aGUgcGFyZW50IGRpcmVjdG9yaWVzLlxuXG4gICAgICAgICAgICAvLyBUcnkgdG8gbG9hZCB0aGUgY2FjaGVkIGluc3RhbmNlIG9mIHRoZSBtb2R1bGUuXG4gICAgICAgICAgICBjb25zdCBjYWNoZWQgPSBjb2xsZWN0aW9uLmNhY2hlLm1vZHVsZXNbYmFzZU1vZHVsZU5hbWVdO1xuICAgICAgICAgICAgaWYgKGNhY2hlZCAmJiBjYWNoZWQuY29kZSA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICAgICAgICAgIGRlZmVycmVkLnJlc29sdmUobmV3IGpzZS5jb25zdHJ1Y3RvcnMuTW9kdWxlKCRlbGVtZW50LCBiYXNlTW9kdWxlTmFtZSwgY29sbGVjdGlvbikpO1xuICAgICAgICAgICAgICAgIHJldHVybiB0cnVlOyAvLyBjb250aW51ZSBsb29wXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGxldCBidXN0U3VmZml4ID0gJyc7XG4gICAgICAgICAgICBpZiAoanNlLmNvcmUuY29uZmlnLmdldCgnZW52aXJvbm1lbnQnKSA9PT0gJ3Byb2R1Y3Rpb24nICYmXG4gICAgICAgICAgICAgICAganNlLmNvcmUuY29uZmlnLmdldCgnYnVzdEZpbGVzJykgJiZcbiAgICAgICAgICAgICAgICBqc2UuY29yZS5jb25maWcuZ2V0KCdjYWNoZVRva2VuJylcbiAgICAgICAgICAgICkge1xuICAgICAgICAgICAgICAgIGJ1c3RTdWZmaXggPSAnLWJ1c3RfJyArIGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2NhY2hlVG9rZW4nKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy8gVHJ5IHRvIGxvYWQgdGhlIG1vZHVsZSBmaWxlIGZyb20gdGhlIHNlcnZlci5cbiAgICAgICAgICAgIGNvbnN0IGZpbGVFeHRlbnNpb24gPSBqc2UuY29yZS5jb25maWcuZ2V0KCdkZWJ1ZycpICE9PSAnREVCVUcnID8gJy5taW4uanMnIDogJy5qcyc7XG4gICAgICAgICAgICBjb25zdCB1cmwgPSBjb2xsZWN0aW9uLm5hbWVzcGFjZS5zb3VyY2UgKyAnLycgKyBjb2xsZWN0aW9uLm5hbWUgKyAnLycgKyBuYW1lICsgYnVzdFN1ZmZpeCArIGZpbGVFeHRlbnNpb247XG5cbiAgICAgICAgICAgIHdpbmRvdy5yZXF1aXJlKFt1cmxdLCAoKSA9PiB7XG4gICAgICAgICAgICAgICAgaWYgKGNvbGxlY3Rpb24uY2FjaGUubW9kdWxlc1tiYXNlTW9kdWxlTmFtZV0gPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoJ01vZHVsZSBcIicgKyBuYW1lICsgJ1wiIHdhc25cXCd0IGRlZmluZWQgY29ycmVjdGx5LiBDaGVjayB0aGUgbW9kdWxlIGNvZGUgZm9yICdcbiAgICAgICAgICAgICAgICAgICAgICAgICsgJ2Z1cnRoZXIgdHJvdWJsZXNob290aW5nLicpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIC8vIFVzZSB0aGUgc2xpY2UgbWV0aG9kIGZvciBjb3B5aW5nIHRoZSBhcnJheS4gXG4gICAgICAgICAgICAgICAgY29uc3QgZGVwZW5kZW5jaWVzID0gY29sbGVjdGlvbi5jYWNoZS5tb2R1bGVzW2Jhc2VNb2R1bGVOYW1lXS5kZXBlbmRlbmNpZXMuc2xpY2UoKTtcblxuICAgICAgICAgICAgICAgIGlmIChkZXBlbmRlbmNpZXMubGVuZ3RoID09PSAwKSB7IC8vIG5vIGRlcGVuZGVuY2llc1xuICAgICAgICAgICAgICAgICAgICBkZWZlcnJlZC5yZXNvbHZlKG5ldyBqc2UuY29uc3RydWN0b3JzLk1vZHVsZSgkZWxlbWVudCwgYmFzZU1vZHVsZU5hbWUsIGNvbGxlY3Rpb24pKTtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHRydWU7IC8vIGNvbnRpbnVlIGxvb3BcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAvLyBMb2FkIHRoZSBkZXBlbmRlbmNpZXMgZmlyc3QuXG4gICAgICAgICAgICAgICAgZm9yIChsZXQgaW5kZXggaW4gZGVwZW5kZW5jaWVzKSB7XG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IGRlcGVuZGVuY3kgPSBkZXBlbmRlbmNpZXNbaW5kZXhdO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmIChkZXBlbmRlbmN5LmluZGV4T2YoJy5jc3MnKSAhPT0gLTEpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIF9sb2FkQ3NzKGRlcGVuZGVuY3kpO1xuICAgICAgICAgICAgICAgICAgICAgICAgZGVwZW5kZW5jaWVzLnNwbGljZShpbmRleCwgMSk7XG4gICAgICAgICAgICAgICAgICAgICAgICBjb250aW51ZTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIC8vIFRoZW4gY29udmVydCB0aGUgcmVsYXRpdmUgcGF0aCB0byBKU0VuZ2luZS9saWJzIGRpcmVjdG9yeS5cbiAgICAgICAgICAgICAgICAgICAgaWYgKGRlcGVuZGVuY3kuaW5kZXhPZignaHR0cCcpID09PSAtMSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgZGVwZW5kZW5jaWVzW2luZGV4XSA9IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2VuZ2luZVVybCcpICsgJy9saWJzLycgKyBkZXBlbmRlbmN5ICsgYnVzdFN1ZmZpeCArIGZpbGVFeHRlbnNpb247XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAoZGVwZW5kZW5jeS5zdWJzdHIoLTMpICE9PSAnLmpzJykgeyAvLyBUaGVuIGFkZCB0aGUgZHluYW1pYyBmaWxlIGV4dGVuc2lvbiB0byB0aGUgVVJMLlxuICAgICAgICAgICAgICAgICAgICAgICAgZGVwZW5kZW5jaWVzW2luZGV4XSArPSBidXN0U3VmZml4ICsgZmlsZUV4dGVuc2lvbjtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHdpbmRvdy5yZXF1aXJlKGRlcGVuZGVuY2llcywgKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICBkZWZlcnJlZC5yZXNvbHZlKG5ldyBqc2UuY29uc3RydWN0b3JzLk1vZHVsZSgkZWxlbWVudCwgYmFzZU1vZHVsZU5hbWUsIGNvbGxlY3Rpb24pKTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9IGNhdGNoIChleGNlcHRpb24pIHtcbiAgICAgICAgICAgIGRlZmVycmVkLnJlamVjdChleGNlcHRpb24pO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIGRlZmVycmVkLnByb21pc2UoKTtcbiAgICB9O1xuXG59KShqc2UuY29yZS5tb2R1bGVfbG9hZGVyKTtcbiIsIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gcG9seWZpbGxzLmpzIDIwMTYtMDUtMTdcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIEpTRSBQb2x5ZmlsbHNcbiAqXG4gKiBSZXF1aXJlZCBwb2x5ZmlsbHMgZm9yIGNvbXBhdGliaWxpdHkgYW1vbmcgb2xkIGJyb3dzZXJzLlxuICpcbiAqIEBtb2R1bGUgSlNFL0NvcmUvcG9seWZpbGxzXG4gKi9cbihmdW5jdGlvbiAoKSB7XG5cbiAgICAndXNlIHN0cmljdCc7XG5cbiAgICAvLyBJbnRlcm5ldCBFeHBsb3JlciBkb2VzIG5vdCBzdXBwb3J0IHRoZSBvcmlnaW4gcHJvcGVydHkgb2YgdGhlIHdpbmRvdy5sb2NhdGlvbiBvYmplY3QuXG4gICAgLy8ge0BsaW5rIGh0dHA6Ly90b3Nib3Vybi5jb20vYS1maXgtZm9yLXdpbmRvdy1sb2NhdGlvbi1vcmlnaW4taW4taW50ZXJuZXQtZXhwbG9yZXJ9XG4gICAgaWYgKCF3aW5kb3cubG9jYXRpb24ub3JpZ2luKSB7XG4gICAgICAgIHdpbmRvdy5sb2NhdGlvbi5vcmlnaW4gPSB3aW5kb3cubG9jYXRpb24ucHJvdG9jb2wgKyAnLy8nICtcbiAgICAgICAgICAgIHdpbmRvdy5sb2NhdGlvbi5ob3N0bmFtZSArICh3aW5kb3cubG9jYXRpb24ucG9ydCA/ICc6JyArIHdpbmRvdy5sb2NhdGlvbi5wb3J0IDogJycpO1xuICAgIH1cblxuICAgIC8vIERhdGUubm93IG1ldGhvZCBwb2x5ZmlsbFxuICAgIC8vIHtAbGluayBodHRwczovL2RldmVsb3Blci5tb3ppbGxhLm9yZy9lbi1VUy9kb2NzL1dlYi9KYXZhU2NyaXB0L1JlZmVyZW5jZS9HbG9iYWxfT2JqZWN0cy9EYXRlL25vd31cbiAgICBpZiAoIURhdGUubm93KSB7XG4gICAgICAgIERhdGUubm93ID0gZnVuY3Rpb24gbm93KCkge1xuICAgICAgICAgICAgcmV0dXJuIG5ldyBEYXRlKCkuZ2V0VGltZSgpO1xuICAgICAgICB9O1xuICAgIH1cblxufSkoKTtcblxuXG4iLCIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHJlZ2lzdHJ5LmpzIDIwMTYtMDktMDhcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG5qc2UuY29yZS5yZWdpc3RyeSA9IGpzZS5jb3JlLnJlZ2lzdHJ5IHx8IHt9O1xuXG4vKipcbiAqIEpTIEVuZ2luZSBSZWdpc3RyeVxuICpcbiAqIFRoaXMgb2JqZWN0IGNvbnRhaW5zIHN0cmluZyBkYXRhIHRoYXQgb3RoZXIgc2VjdGlvbnMgb2YgdGhlIGVuZ2luZSBuZWVkIGluIG9yZGVyIHRvIG9wZXJhdGUgY29ycmVjdGx5LlxuICpcbiAqIEBtb2R1bGUgSlNFL0NvcmUvcmVnaXN0cnlcbiAqL1xuKGZ1bmN0aW9uIChleHBvcnRzKSB7XG5cbiAgICAndXNlIHN0cmljdCc7XG5cbiAgICAvKipcbiAgICAgKiBDb250YWlucyB0aGUgcmVnaXN0cnkgdmFsdWVzLlxuICAgICAqXG4gICAgICogQHR5cGUge09iamVjdFtdfVxuICAgICAqL1xuICAgIGNvbnN0IHJlZ2lzdHJ5ID0gW107XG5cbiAgICAvKipcbiAgICAgKiBTZXQgYSB2YWx1ZSBpbiB0aGUgcmVnaXN0cnkuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gbmFtZSBDb250YWlucyB0aGUgbmFtZSBvZiB0aGUgZW50cnkgdG8gYmUgYWRkZWQuXG4gICAgICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gYmUgd3JpdHRlbiBpbiB0aGUgcmVnaXN0cnkuXG4gICAgICovXG4gICAgZXhwb3J0cy5zZXQgPSBmdW5jdGlvbiAobmFtZSwgdmFsdWUpIHtcbiAgICAgICAgLy8gSWYgYSByZWdpc3RyeSBlbnRyeSB3aXRoIHRoZSBzYW1lIG5hbWUgZXhpc3RzIGFscmVhZHkgdGhlIGZvbGxvd2luZyBjb25zb2xlIHdhcm5pbmcgd2lsbFxuICAgICAgICAvLyBpbmZvcm0gZGV2ZWxvcGVycyB0aGF0IHRoZXkgYXJlIG92ZXJ3cml0aW5nIGFuIGV4aXN0aW5nIHZhbHVlLCBzb21ldGhpbmcgdXNlZnVsIHdoZW4gZGVidWdnaW5nLlxuICAgICAgICBpZiAocmVnaXN0cnlbbmFtZV0gIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAganNlLmNvcmUuZGVidWcud2FybignVGhlIHJlZ2lzdHJ5IHZhbHVlIHdpdGggdGhlIG5hbWUgXCInICsgbmFtZSArICdcIiB3aWxsIGJlIG92ZXJ3cml0dGVuLicpO1xuICAgICAgICB9XG5cbiAgICAgICAgcmVnaXN0cnlbbmFtZV0gPSB2YWx1ZTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogR2V0IGEgdmFsdWUgZnJvbSB0aGUgcmVnaXN0cnkuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gbmFtZSBUaGUgbmFtZSBvZiB0aGUgZW50cnkgdmFsdWUgdG8gYmUgcmV0dXJuZWQuXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7Kn0gUmV0dXJucyB0aGUgdmFsdWUgdGhhdCBtYXRjaGVzIHRoZSBuYW1lLlxuICAgICAqL1xuICAgIGV4cG9ydHMuZ2V0ID0gZnVuY3Rpb24gKG5hbWUpIHtcbiAgICAgICAgcmV0dXJuIHJlZ2lzdHJ5W25hbWVdO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBDaGVjayB0aGUgY3VycmVudCBjb250ZW50IG9mIHRoZSByZWdpc3RyeSBvYmplY3QuXG4gICAgICpcbiAgICAgKiBUaGlzIG1ldGhvZCBpcyBvbmx5IGF2YWlsYWJsZSB3aGVuIHRoZSBlbmdpbmUgZW52aXJvbm1lbnQgaXMgdHVybmVkIGludG8gZGV2ZWxvcG1lbnQuXG4gICAgICovXG4gICAgZXhwb3J0cy5kZWJ1ZyA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgaWYgKGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2Vudmlyb25tZW50JykgPT09ICdkZXZlbG9wbWVudCcpIHtcbiAgICAgICAgICAgIGpzZS5jb3JlLmRlYnVnLmxvZygnUmVnaXN0cnkgT2JqZWN0OicsIHJlZ2lzdHJ5KTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcignVGhpcyBmdW5jdGlvbiBpcyBub3QgYWxsb3dlZCBpbiBhIHByb2R1Y3Rpb24gZW52aXJvbm1lbnQuJyk7XG4gICAgICAgIH1cbiAgICB9O1xuXG59KShqc2UuY29yZS5yZWdpc3RyeSk7XG4iLCIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHJlcXVpcmUuanMgMjAxNy0wMy0yOFxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTcgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogQXN5bmNocm9ub3VzIE1vZHVsZSBMb2FkaW5nXG4gKlxuICogVGhpcyBtb2R1bGUgaXMgYSBmb3JrIG9mIFJlcXVpcmVKUyB3aXRob3V0IHRoZSBBTUQgZnVuY3Rpb25hbGl0eS4gVGhlIGdsb2JhbCBcImRlZmluZVwiIG1ldGhvZCBpcyByZW1vdmVkIGFzXG4gKiBpdCdzIG5vdCBuZWNlc3NhcnkgYnkgSlMgRW5naW5lLlxuICpcbiAqIHtAbGluayBodHRwczovL2dpdGh1Yi5jb20vcmVxdWlyZWpzL3JlcXVpcmVqc31cbiAqXG4gKiBOb3QgdXNpbmcgc3RyaWN0OiB1bmV2ZW4gc3RyaWN0IHN1cHBvcnQgaW4gYnJvd3NlcnMsICMzOTIsIGFuZCBjYXVzZXMgcHJvYmxlbXMgd2l0aCByZXF1aXJlanMuZXhlYygpL3RyYW5zcGlsZXJcbiAqIHBsdWdpbnMgdGhhdCBtYXkgbm90IGJlIHN0cmljdC5cbiAqL1xuKGZ1bmN0aW9uICgpIHtcblxuICAgIC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICAgIC8vIFZBUklBQkxFU1xuICAgIC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLSBcblxuICAgIHdpbmRvdy5yZXF1aXJlanMgPSB1bmRlZmluZWQ7XG4gICAgd2luZG93LnJlcXVpcmUgPSB1bmRlZmluZWQ7XG5cbiAgICBsZXQgcmVxO1xuICAgIGxldCBzO1xuICAgIGxldCBoZWFkO1xuICAgIGxldCBiYXNlRWxlbWVudDtcbiAgICBsZXQgZGF0YU1haW47XG4gICAgbGV0IHNyYztcbiAgICBsZXQgaW50ZXJhY3RpdmVTY3JpcHQ7XG4gICAgbGV0IG1haW5TY3JpcHQ7XG4gICAgbGV0IHN1YlBhdGg7XG4gICAgbGV0IHZlcnNpb24gPSAnMi4xLjIyJztcbiAgICBsZXQganNTdWZmaXhSZWdFeHAgPSAvXFwuanMkLztcbiAgICBsZXQgY3VyckRpclJlZ0V4cCA9IC9eXFwuXFwvLztcbiAgICBsZXQgb3AgPSBPYmplY3QucHJvdG90eXBlO1xuICAgIGxldCBvc3RyaW5nID0gb3AudG9TdHJpbmc7XG4gICAgbGV0IGhhc093biA9IG9wLmhhc093blByb3BlcnR5O1xuICAgIGxldCBpc0Jyb3dzZXIgPSAhISh0eXBlb2Ygd2luZG93ICE9PSAndW5kZWZpbmVkJyAmJiB0eXBlb2YgbmF2aWdhdG9yICE9PSAndW5kZWZpbmVkJyAmJiB3aW5kb3cuZG9jdW1lbnQpO1xuICAgIGxldCBpc1dlYldvcmtlciA9ICFpc0Jyb3dzZXIgJiYgdHlwZW9mIGltcG9ydFNjcmlwdHMgIT09ICd1bmRlZmluZWQnO1xuICAgIC8vIFBTMyBpbmRpY2F0ZXMgbG9hZGVkIGFuZCBjb21wbGV0ZSwgYnV0IG5lZWQgdG8gd2FpdCBmb3IgY29tcGxldCBzcGVjaWZpY2FsbHkuIFNlcXVlbmNlIGlzICdsb2FkaW5nJywgJ2xvYWRlZCcsIFxuICAgIC8vIGV4ZWN1dGlvbiB0aGVuICdjb21wbGV0ZScuIFRoZSBVQSBjaGVjayBpcyB1bmZvcnR1bmF0ZSwgYnV0IG5vdCBzdXJlIGhvdyB0byBmZWF0dXJlIHRlc3Qgdy9vIGNhdXNpbmcgcGVyZiBpc3N1ZXMuXG4gICAgbGV0IHJlYWR5UmVnRXhwID0gaXNCcm93c2VyICYmIG5hdmlnYXRvci5wbGF0Zm9ybSA9PT0gJ1BMQVlTVEFUSU9OIDMnID8gL15jb21wbGV0ZSQvIDogL14oY29tcGxldGV8bG9hZGVkKSQvO1xuICAgIGxldCBkZWZDb250ZXh0TmFtZSA9ICdfJztcbiAgICAvLyBPaCB0aGUgdHJhZ2VkeSwgZGV0ZWN0aW5nIG9wZXJhLiBTZWUgdGhlIHVzYWdlIG9mIGlzT3BlcmEgZm9yIHJlYXNvbi5cbiAgICBsZXQgaXNPcGVyYSA9IHR5cGVvZiBvcGVyYSAhPT0gJ3VuZGVmaW5lZCcgJiYgb3BlcmEudG9TdHJpbmcoKSA9PT0gJ1tvYmplY3QgT3BlcmFdJztcbiAgICBsZXQgY29udGV4dHMgPSB7fTtcbiAgICBsZXQgY2ZnID0ge307XG4gICAgbGV0IGdsb2JhbERlZlF1ZXVlID0gW107XG4gICAgbGV0IHVzZUludGVyYWN0aXZlID0gZmFsc2U7XG5cbiAgICAvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAgICAvLyBGVU5DVElPTlNcbiAgICAvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0gXG5cbiAgICAvKipcbiAgICAgKiBDaGVjayB3aGV0aGVyIHZhbHVlIGlzIGEgZnVuY3Rpb24uXG4gICAgICpcbiAgICAgKiBAcGFyYW0geyp9IGl0IFZhbHVlIHRvIGJlIGNoZWNrZWQuXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIHtib29sZWFufSBSZXR1cm5zIHRoZSB2YWxpZGF0aW9uIHJlc3VsdC5cbiAgICAgKi9cbiAgICBmdW5jdGlvbiBpc0Z1bmN0aW9uKGl0KSB7XG4gICAgICAgIHJldHVybiBvc3RyaW5nLmNhbGwoaXQpID09PSAnW29iamVjdCBGdW5jdGlvbl0nO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIENoZWNrIHdoZXRoZXIgdmFsdWUgaXMgYW4gYXJyYXkuXG4gICAgICpcbiAgICAgKiBAcGFyYW0geyp9IGl0IFZhbHVlIHRvIGJlIGNoZWNrZWQuXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIHtib29sZWFufSBSZXR1cm5zIHRoZSB2YWxpZGF0aW9uIHJlc3VsdC5cbiAgICAgKi9cbiAgICBmdW5jdGlvbiBpc0FycmF5KGl0KSB7XG4gICAgICAgIHJldHVybiBvc3RyaW5nLmNhbGwoaXQpID09PSAnW29iamVjdCBBcnJheV0nO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEhlbHBlciBmdW5jdGlvbiBmb3IgaXRlcmF0aW5nIG92ZXIgYW4gYXJyYXkuXG4gICAgICpcbiAgICAgKiBJZiB0aGUgZnVuYyByZXR1cm5zIGEgdHJ1ZSB2YWx1ZSwgaXQgd2lsbCBicmVhayBvdXQgb2YgdGhlIGxvb3AuXG4gICAgICovXG4gICAgZnVuY3Rpb24gZWFjaChhcnksIGZ1bmMpIHtcbiAgICAgICAgaWYgKGFyeSkge1xuICAgICAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCBhcnkubGVuZ3RoOyBpICs9IDEpIHtcbiAgICAgICAgICAgICAgICBpZiAoYXJ5W2ldICYmIGZ1bmMoYXJ5W2ldLCBpLCBhcnkpKSB7XG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEhlbHBlciBmdW5jdGlvbiBmb3IgaXRlcmF0aW5nIG92ZXIgYW4gYXJyYXkgYmFja3dhcmRzLlxuICAgICAqXG4gICAgICogSWYgdGhlIGZ1bmMgcmV0dXJucyBhIHRydWUgdmFsdWUsIGl0IHdpbGwgYnJlYWsgb3V0IG9mIHRoZSBsb29wLlxuICAgICAqL1xuICAgIGZ1bmN0aW9uIGVhY2hSZXZlcnNlKGFyeSwgZnVuYykge1xuICAgICAgICBpZiAoYXJ5KSB7XG4gICAgICAgICAgICBsZXQgaTtcbiAgICAgICAgICAgIGZvciAoaSA9IGFyeS5sZW5ndGggLSAxOyBpID4gLTE7IGkgLT0gMSkge1xuICAgICAgICAgICAgICAgIGlmIChhcnlbaV0gJiYgZnVuYyhhcnlbaV0sIGksIGFyeSkpIHtcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQ2hlY2sgd2hldGhlciBhbiBvYmplY3QgaGFzIGEgc3BlY2lmaWMgcHJvcGVydHkuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge09iamVjdH0gb2JqIE9iamVjdCB0byBiZSBjaGVja2VkLlxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSBwcm9wIFByb3BlcnR5IG5hbWUgdG8gYmUgY2hlY2tlZC5cbiAgICAgKlxuICAgICAqIEByZXR1cm4ge0Jvb2xlYW59IFJldHVybnMgdGhlIHZhbGlkYXRpb24gcmVzdWx0LlxuICAgICAqL1xuICAgIGZ1bmN0aW9uIGhhc1Byb3Aob2JqLCBwcm9wKSB7XG4gICAgICAgIHJldHVybiBoYXNPd24uY2FsbChvYmosIHByb3ApO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIENoZWNrIGlmIGFuIG9iamVjdCBoYXMgYSBwcm9wZXJ0eSBhbmQgaWYgdGhhdCBwcm9wZXJ0eSBjb250YWlucyBhIHRydXRoeSB2YWx1ZS5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBvYmogT2JqZWN0IHRvIGJlIGNoZWNrZWQuXG4gICAgICogQHBhcmFtIHtTdHJpbmd9IHByb3AgUHJvcGVydHkgbmFtZSB0byBiZSBjaGVja2VkLlxuICAgICAqXG4gICAgICogQHJldHVybiB7Qm9vbGVhbn0gUmV0dXJucyB0aGUgdmFsaWRhdGlvbiByZXN1bHQuXG4gICAgICovXG4gICAgZnVuY3Rpb24gZ2V0T3duKG9iaiwgcHJvcCkge1xuICAgICAgICByZXR1cm4gaGFzUHJvcChvYmosIHByb3ApICYmIG9ialtwcm9wXTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBDeWNsZXMgb3ZlciBwcm9wZXJ0aWVzIGluIGFuIG9iamVjdCBhbmQgY2FsbHMgYSBmdW5jdGlvbiBmb3IgZWFjaCBwcm9wZXJ0eSB2YWx1ZS5cbiAgICAgKlxuICAgICAqIElmIHRoZSBmdW5jdGlvbiByZXR1cm5zIGEgdHJ1dGh5IHZhbHVlLCB0aGVuIHRoZSBpdGVyYXRpb24gaXMgc3RvcHBlZC5cbiAgICAgKi9cbiAgICBmdW5jdGlvbiBlYWNoUHJvcChvYmosIGZ1bmMpIHtcbiAgICAgICAgbGV0IHByb3A7XG4gICAgICAgIGZvciAocHJvcCBpbiBvYmopIHtcbiAgICAgICAgICAgIGlmIChoYXNQcm9wKG9iaiwgcHJvcCkpIHtcbiAgICAgICAgICAgICAgICBpZiAoZnVuYyhvYmpbcHJvcF0sIHByb3ApKSB7XG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFNpbXBsZSBmdW5jdGlvbiB0byBtaXggaW4gcHJvcGVydGllcyBmcm9tIHNvdXJjZSBpbnRvIHRhcmdldCwgYnV0IG9ubHkgaWYgdGFyZ2V0IGRvZXMgbm90IGFscmVhZHkgaGF2ZSBhXG4gICAgICogcHJvcGVydHkgb2YgdGhlIHNhbWUgbmFtZS5cbiAgICAgKi9cbiAgICBmdW5jdGlvbiBtaXhpbih0YXJnZXQsIHNvdXJjZSwgZm9yY2UsIGRlZXBTdHJpbmdNaXhpbikge1xuICAgICAgICBpZiAoc291cmNlKSB7XG4gICAgICAgICAgICBlYWNoUHJvcChzb3VyY2UsIGZ1bmN0aW9uICh2YWx1ZSwgcHJvcCkge1xuICAgICAgICAgICAgICAgIGlmIChmb3JjZSB8fCAhaGFzUHJvcCh0YXJnZXQsIHByb3ApKSB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChkZWVwU3RyaW5nTWl4aW4gJiYgdHlwZW9mIHZhbHVlID09PSAnb2JqZWN0JyAmJiB2YWx1ZSAmJlxuICAgICAgICAgICAgICAgICAgICAgICAgIWlzQXJyYXkodmFsdWUpICYmICFpc0Z1bmN0aW9uKHZhbHVlKSAmJlxuICAgICAgICAgICAgICAgICAgICAgICAgISh2YWx1ZSBpbnN0YW5jZW9mIFJlZ0V4cCkpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCF0YXJnZXRbcHJvcF0pIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0YXJnZXRbcHJvcF0gPSB7fTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIG1peGluKHRhcmdldFtwcm9wXSwgdmFsdWUsIGZvcmNlLCBkZWVwU3RyaW5nTWl4aW4pO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgdGFyZ2V0W3Byb3BdID0gdmFsdWU7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgICAgICByZXR1cm4gdGFyZ2V0O1xuICAgIH1cblxuICAgIC8vIFNpbWlsYXIgdG8gRnVuY3Rpb24ucHJvdG90eXBlLmJpbmQsIGJ1dCB0aGUgJ3RoaXMnIG9iamVjdCBpcyBzcGVjaWZpZWQgZmlyc3QsIHNpbmNlIGl0IGlzIGVhc2llciB0byByZWFkL2ZpZ3VyZSBcbiAgICAvLyBvdXQgd2hhdCAndGhpcycgd2lsbCBiZS5cbiAgICBmdW5jdGlvbiBiaW5kKG9iaiwgZm4pIHtcbiAgICAgICAgcmV0dXJuIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHJldHVybiBmbi5hcHBseShvYmosIGFyZ3VtZW50cyk7XG4gICAgICAgIH07XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gc2NyaXB0cygpIHtcbiAgICAgICAgcmV0dXJuIGRvY3VtZW50LmdldEVsZW1lbnRzQnlUYWdOYW1lKCdzY3JpcHQnKTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBkZWZhdWx0T25FcnJvcihlcnIpIHtcbiAgICAgICAgdGhyb3cgZXJyO1xuICAgIH1cblxuICAgIC8vIEFsbG93IGdldHRpbmcgYSBnbG9iYWwgdGhhdCBpcyBleHByZXNzZWQgaW4gZG90IG5vdGF0aW9uLCBsaWtlICdhLmIuYycuXG4gICAgZnVuY3Rpb24gZ2V0R2xvYmFsKHZhbHVlKSB7XG4gICAgICAgIGlmICghdmFsdWUpIHtcbiAgICAgICAgICAgIHJldHVybiB2YWx1ZTtcbiAgICAgICAgfVxuICAgICAgICBsZXQgZyA9IGdsb2JhbDtcbiAgICAgICAgZWFjaCh2YWx1ZS5zcGxpdCgnLicpLCBmdW5jdGlvbiAocGFydCkge1xuICAgICAgICAgICAgZyA9IGdbcGFydF07XG4gICAgICAgIH0pO1xuICAgICAgICByZXR1cm4gZztcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBDb25zdHJ1Y3RzIGFuIGVycm9yIHdpdGggYSBwb2ludGVyIHRvIGFuIFVSTCB3aXRoIG1vcmUgaW5mb3JtYXRpb24uXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gaWQgVGhlIGVycm9yIElEIHRoYXQgbWFwcyB0byBhbiBJRCBvbiBhIHdlYiBwYWdlLlxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSBtc2cgSHVtYW4gcmVhZGFibGUgZXJyb3IuXG4gICAgICogQHBhcmFtIHtFcnJvcn0gW2Vycl0gVGhlIG9yaWdpbmFsIGVycm9yLCBpZiB0aGVyZSBpcyBvbmUuXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIHtFcnJvcn1cbiAgICAgKi9cbiAgICBmdW5jdGlvbiBtYWtlRXJyb3IoaWQsIG1zZywgZXJyLCByZXF1aXJlTW9kdWxlcykge1xuICAgICAgICBjb25zdCBlcnJvciA9IG5ldyBFcnJvcihtc2cgKyAnXFxuaHR0cDovL3JlcXVpcmVqcy5vcmcvZG9jcy9lcnJvcnMuaHRtbCMnICsgaWQpO1xuXG4gICAgICAgIGVycm9yLnJlcXVpcmVUeXBlID0gaWQ7XG4gICAgICAgIGVycm9yLnJlcXVpcmVNb2R1bGVzID0gcmVxdWlyZU1vZHVsZXM7XG5cbiAgICAgICAgaWYgKGVycikge1xuICAgICAgICAgICAgZXJyb3Iub3JpZ2luYWxFcnJvciA9IGVycjtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBlcnJvcjtcbiAgICB9XG5cbiAgICBpZiAodHlwZW9mIHdpbmRvdy5yZXF1aXJlanMgIT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgIGlmIChpc0Z1bmN0aW9uKHdpbmRvdy5yZXF1aXJlanMpKSB7XG4gICAgICAgICAgICAvLyBEbyBub3Qgb3ZlcndyaXRlIGFuIGV4aXN0aW5nIHJlcXVpcmVqcyBpbnN0YW5jZS5cbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuICAgICAgICBjZmcgPSB3aW5kb3cucmVxdWlyZWpzO1xuICAgICAgICB3aW5kb3cucmVxdWlyZWpzID0gdW5kZWZpbmVkO1xuICAgIH1cblxuICAgIC8vIEFsbG93IGZvciBhIHJlcXVpcmUgY29uZmlnIG9iamVjdFxuICAgIGlmICh0eXBlb2Ygd2luZG93LnJlcXVpcmUgIT09ICd1bmRlZmluZWQnICYmICFpc0Z1bmN0aW9uKHdpbmRvdy5yZXF1aXJlKSkge1xuICAgICAgICAvLyBhc3N1bWUgaXQgaXMgYSBjb25maWcgb2JqZWN0LlxuICAgICAgICBjZmcgPSB3aW5kb3cucmVxdWlyZTtcbiAgICAgICAgd2luZG93LnJlcXVpcmUgPSB1bmRlZmluZWQ7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gbmV3Q29udGV4dChjb250ZXh0TmFtZSkge1xuICAgICAgICBsZXQgaW5DaGVja0xvYWRlZCwgTW9kdWxlLCBjb250ZXh0LCBoYW5kbGVycyxcbiAgICAgICAgICAgIGNoZWNrTG9hZGVkVGltZW91dElkLFxuICAgICAgICAgICAgY29uZmlnID0ge1xuICAgICAgICAgICAgICAgIC8vIERlZmF1bHRzLiBEbyBub3Qgc2V0IGEgZGVmYXVsdCBmb3IgbWFwXG4gICAgICAgICAgICAgICAgLy8gY29uZmlnIHRvIHNwZWVkIHVwIG5vcm1hbGl6ZSgpLCB3aGljaFxuICAgICAgICAgICAgICAgIC8vIHdpbGwgcnVuIGZhc3RlciBpZiB0aGVyZSBpcyBubyBkZWZhdWx0LlxuICAgICAgICAgICAgICAgIHdhaXRTZWNvbmRzOiA3LFxuICAgICAgICAgICAgICAgIGJhc2VVcmw6ICcuLycsXG4gICAgICAgICAgICAgICAgcGF0aHM6IHt9LFxuICAgICAgICAgICAgICAgIGJ1bmRsZXM6IHt9LFxuICAgICAgICAgICAgICAgIHBrZ3M6IHt9LFxuICAgICAgICAgICAgICAgIHNoaW06IHt9LFxuICAgICAgICAgICAgICAgIGNvbmZpZzoge31cbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICByZWdpc3RyeSA9IHt9LFxuICAgICAgICAgICAgLy8gcmVnaXN0cnkgb2YganVzdCBlbmFibGVkIG1vZHVsZXMsIHRvIHNwZWVkXG4gICAgICAgICAgICAvLyBjeWNsZSBicmVha2luZyBjb2RlIHdoZW4gbG90cyBvZiBtb2R1bGVzXG4gICAgICAgICAgICAvLyBhcmUgcmVnaXN0ZXJlZCwgYnV0IG5vdCBhY3RpdmF0ZWQuXG4gICAgICAgICAgICBlbmFibGVkUmVnaXN0cnkgPSB7fSxcbiAgICAgICAgICAgIHVuZGVmRXZlbnRzID0ge30sXG4gICAgICAgICAgICBkZWZRdWV1ZSA9IFtdLFxuICAgICAgICAgICAgZGVmaW5lZCA9IHt9LFxuICAgICAgICAgICAgdXJsRmV0Y2hlZCA9IHt9LFxuICAgICAgICAgICAgYnVuZGxlc01hcCA9IHt9LFxuICAgICAgICAgICAgcmVxdWlyZUNvdW50ZXIgPSAxLFxuICAgICAgICAgICAgdW5ub3JtYWxpemVkQ291bnRlciA9IDE7XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFRyaW1zIHRoZSAuIGFuZCAuLiBmcm9tIGFuIGFycmF5IG9mIHBhdGggc2VnbWVudHMuXG4gICAgICAgICAqXG4gICAgICAgICAqIEl0IHdpbGwga2VlcCBhIGxlYWRpbmcgcGF0aCBzZWdtZW50IGlmIGEgLi4gd2lsbCBiZWNvbWUgdGhlIGZpcnN0IHBhdGggc2VnbWVudCwgdG8gaGVscCB3aXRoIG1vZHVsZSBuYW1lXG4gICAgICAgICAqIGxvb2t1cHMsIHdoaWNoIGFjdCBsaWtlIHBhdGhzLCBidXQgY2FuIGJlIHJlbWFwcGVkLiBCdXQgdGhlIGVuZCByZXN1bHQsIGFsbCBwYXRocyB0aGF0IHVzZSB0aGlzIGZ1bmN0aW9uXG4gICAgICAgICAqIHNob3VsZCBsb29rIG5vcm1hbGl6ZWQuXG4gICAgICAgICAqXG4gICAgICAgICAqIE5PVEU6IHRoaXMgbWV0aG9kIE1PRElGSUVTIHRoZSBpbnB1dCBhcnJheS5cbiAgICAgICAgICpcbiAgICAgICAgICogQHBhcmFtIHtBcnJheX0gYXJ5IHRoZSBhcnJheSBvZiBwYXRoIHNlZ21lbnRzLlxuICAgICAgICAgKi9cbiAgICAgICAgZnVuY3Rpb24gdHJpbURvdHMoYXJ5KSB7XG4gICAgICAgICAgICBsZXQgaSwgcGFydDtcbiAgICAgICAgICAgIGZvciAoaSA9IDA7IGkgPCBhcnkubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgICAgICAgICBwYXJ0ID0gYXJ5W2ldO1xuICAgICAgICAgICAgICAgIGlmIChwYXJ0ID09PSAnLicpIHtcbiAgICAgICAgICAgICAgICAgICAgYXJ5LnNwbGljZShpLCAxKTtcbiAgICAgICAgICAgICAgICAgICAgaSAtPSAxO1xuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAocGFydCA9PT0gJy4uJykge1xuICAgICAgICAgICAgICAgICAgICAvLyBJZiBhdCB0aGUgc3RhcnQsIG9yIHByZXZpb3VzIHZhbHVlIGlzIHN0aWxsIC4uLCAga2VlcCB0aGVtIHNvIHRoYXQgd2hlbiBjb252ZXJ0ZWQgdG8gYSBwYXRoIGl0IFxuICAgICAgICAgICAgICAgICAgICAvLyBtYXkgc3RpbGwgd29yayB3aGVuIGNvbnZlcnRlZCB0byBhIHBhdGgsIGV2ZW4gdGhvdWdoIGFzIGFuIElEIGl0IGlzIGxlc3MgdGhhbiBpZGVhbC4gSW4gbGFyZ2VyIFxuICAgICAgICAgICAgICAgICAgICAvLyBwb2ludCByZWxlYXNlcywgbWF5IGJlIGJldHRlciB0byBqdXN0IGtpY2sgb3V0IGFuIGVycm9yLlxuICAgICAgICAgICAgICAgICAgICBpZiAoaSA9PT0gMCB8fCAoaSA9PT0gMSAmJiBhcnlbMl0gPT09ICcuLicpIHx8IGFyeVtpIC0gMV0gPT09ICcuLicpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnRpbnVlO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKGkgPiAwKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBhcnkuc3BsaWNlKGkgLSAxLCAyKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGkgLT0gMjtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBHaXZlbiBhIHJlbGF0aXZlIG1vZHVsZSBuYW1lLCBsaWtlIC4vc29tZXRoaW5nLCBub3JtYWxpemUgaXQgdG8gYSByZWFsIG5hbWUgdGhhdCBjYW4gYmUgbWFwcGVkIHRvIGEgcGF0aC5cbiAgICAgICAgICpcbiAgICAgICAgICogQHBhcmFtIHtTdHJpbmd9IG5hbWUgdGhlIHJlbGF0aXZlIG5hbWVcbiAgICAgICAgICogQHBhcmFtIHtTdHJpbmd9IGJhc2VOYW1lIGEgcmVhbCBuYW1lIHRoYXQgdGhlIG5hbWUgYXJnIGlzIHJlbGF0aXZlIHRvLlxuICAgICAgICAgKiBAcGFyYW0ge0Jvb2xlYW59IGFwcGx5TWFwIGFwcGx5IHRoZSBtYXAgY29uZmlnIHRvIHRoZSB2YWx1ZS4gU2hvdWxkIG9ubHkgYmUgZG9uZSBpZiB0aGlzIG5vcm1hbGl6YXRpb24gaXNcbiAgICAgICAgICogZm9yIGEgZGVwZW5kZW5jeSBJRC5cbiAgICAgICAgICpcbiAgICAgICAgICogQHJldHVybiB7U3RyaW5nfSBub3JtYWxpemVkIG5hbWVcbiAgICAgICAgICovXG4gICAgICAgIGZ1bmN0aW9uIG5vcm1hbGl6ZShuYW1lLCBiYXNlTmFtZSwgYXBwbHlNYXApIHtcbiAgICAgICAgICAgIGxldCBwa2dNYWluLCBtYXBWYWx1ZSwgbmFtZVBhcnRzLCBpLCBqLCBuYW1lU2VnbWVudCwgbGFzdEluZGV4LFxuICAgICAgICAgICAgICAgIGZvdW5kTWFwLCBmb3VuZEksIGZvdW5kU3Rhck1hcCwgc3RhckksIG5vcm1hbGl6ZWRCYXNlUGFydHMsXG4gICAgICAgICAgICAgICAgYmFzZVBhcnRzID0gKGJhc2VOYW1lICYmIGJhc2VOYW1lLnNwbGl0KCcvJykpLFxuICAgICAgICAgICAgICAgIG1hcCA9IGNvbmZpZy5tYXAsXG4gICAgICAgICAgICAgICAgc3Rhck1hcCA9IG1hcCAmJiBtYXBbJyonXTtcblxuICAgICAgICAgICAgLy8gQWRqdXN0IGFueSByZWxhdGl2ZSBwYXRocy5cbiAgICAgICAgICAgIGlmIChuYW1lKSB7XG4gICAgICAgICAgICAgICAgbmFtZSA9IG5hbWUuc3BsaXQoJy8nKTtcbiAgICAgICAgICAgICAgICBsYXN0SW5kZXggPSBuYW1lLmxlbmd0aCAtIDE7XG5cbiAgICAgICAgICAgICAgICAvLyBJZiB3YW50aW5nIG5vZGUgSUQgY29tcGF0aWJpbGl0eSwgc3RyaXAgLmpzIGZyb20gZW5kIG9mIElEcy4gSGF2ZSB0byBkbyB0aGlzIGhlcmUsIGFuZCBub3QgaW4gXG4gICAgICAgICAgICAgICAgLy8gbmFtZVRvVXJsIGJlY2F1c2Ugbm9kZSBhbGxvd3MgZWl0aGVyIC5qcyBvciBub24gLmpzIHRvIG1hcCB0byBzYW1lIGZpbGUuXG4gICAgICAgICAgICAgICAgaWYgKGNvbmZpZy5ub2RlSWRDb21wYXQgJiYganNTdWZmaXhSZWdFeHAudGVzdChuYW1lW2xhc3RJbmRleF0pKSB7XG4gICAgICAgICAgICAgICAgICAgIG5hbWVbbGFzdEluZGV4XSA9IG5hbWVbbGFzdEluZGV4XS5yZXBsYWNlKGpzU3VmZml4UmVnRXhwLCAnJyk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgLy8gU3RhcnRzIHdpdGggYSAnLicgc28gbmVlZCB0aGUgYmFzZU5hbWVcbiAgICAgICAgICAgICAgICBpZiAobmFtZVswXS5jaGFyQXQoMCkgPT09ICcuJyAmJiBiYXNlUGFydHMpIHtcbiAgICAgICAgICAgICAgICAgICAgLy8gQ29udmVydCBiYXNlTmFtZSB0byBhcnJheSwgYW5kIGxvcCBvZmYgdGhlIGxhc3QgcGFydCwgc28gdGhhdCAuIG1hdGNoZXMgdGhhdCAnZGlyZWN0b3J5JyBhbmQgbm90IFxuICAgICAgICAgICAgICAgICAgICAvLyBuYW1lIG9mIHRoZSBiYXNlTmFtZSdzIG1vZHVsZS4gRm9yIGluc3RhbmNlLCBiYXNlTmFtZSBvZiAnb25lL3R3by90aHJlZScsIG1hcHMgdG8gXG4gICAgICAgICAgICAgICAgICAgIC8vICdvbmUvdHdvL3RocmVlLmpzJywgYnV0IHdlIHdhbnQgdGhlIGRpcmVjdG9yeSwgJ29uZS90d28nIGZvciB0aGlzIG5vcm1hbGl6YXRpb24uXG4gICAgICAgICAgICAgICAgICAgIG5vcm1hbGl6ZWRCYXNlUGFydHMgPSBiYXNlUGFydHMuc2xpY2UoMCwgYmFzZVBhcnRzLmxlbmd0aCAtIDEpO1xuICAgICAgICAgICAgICAgICAgICBuYW1lID0gbm9ybWFsaXplZEJhc2VQYXJ0cy5jb25jYXQobmFtZSk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgdHJpbURvdHMobmFtZSk7XG4gICAgICAgICAgICAgICAgbmFtZSA9IG5hbWUuam9pbignLycpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBBcHBseSBtYXAgY29uZmlnIGlmIGF2YWlsYWJsZS5cbiAgICAgICAgICAgIGlmIChhcHBseU1hcCAmJiBtYXAgJiYgKGJhc2VQYXJ0cyB8fCBzdGFyTWFwKSkge1xuICAgICAgICAgICAgICAgIG5hbWVQYXJ0cyA9IG5hbWUuc3BsaXQoJy8nKTtcblxuICAgICAgICAgICAgICAgIG91dGVyTG9vcDogZm9yIChpID0gbmFtZVBhcnRzLmxlbmd0aDsgaSA+IDA7IGkgLT0gMSkge1xuICAgICAgICAgICAgICAgICAgICBuYW1lU2VnbWVudCA9IG5hbWVQYXJ0cy5zbGljZSgwLCBpKS5qb2luKCcvJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKGJhc2VQYXJ0cykge1xuICAgICAgICAgICAgICAgICAgICAgICAgLy8gRmluZCB0aGUgbG9uZ2VzdCBiYXNlTmFtZSBzZWdtZW50IG1hdGNoIGluIHRoZSBjb25maWcuIFNvLCBkbyBqb2lucyBvbiB0aGUgYmlnZ2VzdCB0byBcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIHNtYWxsZXN0IGxlbmd0aHMgb2YgYmFzZVBhcnRzLlxuICAgICAgICAgICAgICAgICAgICAgICAgZm9yIChqID0gYmFzZVBhcnRzLmxlbmd0aDsgaiA+IDA7IGogLT0gMSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG1hcFZhbHVlID0gZ2V0T3duKG1hcCwgYmFzZVBhcnRzLnNsaWNlKDAsIGopLmpvaW4oJy8nKSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBiYXNlTmFtZSBzZWdtZW50IGhhcyBjb25maWcsIGZpbmQgaWYgaXQgaGFzIG9uZSBmb3IgdGhpcyBuYW1lLlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmIChtYXBWYWx1ZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBtYXBWYWx1ZSA9IGdldE93bihtYXBWYWx1ZSwgbmFtZVNlZ21lbnQpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAobWFwVmFsdWUpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIE1hdGNoLCB1cGRhdGUgbmFtZSB0byB0aGUgbmV3IHZhbHVlLlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZm91bmRNYXAgPSBtYXBWYWx1ZTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGZvdW5kSSA9IGk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBicmVhayBvdXRlckxvb3A7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAvLyBDaGVjayBmb3IgYSBzdGFyIG1hcCBtYXRjaCwgYnV0IGp1c3QgaG9sZCBvbiB0byBpdCwgaWYgdGhlcmUgaXMgYSBzaG9ydGVyIHNlZ21lbnQgbWF0Y2ggbGF0ZXIgaW4gXG4gICAgICAgICAgICAgICAgICAgIC8vIGEgbWF0Y2hpbmcgY29uZmlnLCB0aGVuIGZhdm9yIG92ZXIgdGhpcyBzdGFyIG1hcC5cbiAgICAgICAgICAgICAgICAgICAgaWYgKCFmb3VuZFN0YXJNYXAgJiYgc3Rhck1hcCAmJiBnZXRPd24oc3Rhck1hcCwgbmFtZVNlZ21lbnQpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBmb3VuZFN0YXJNYXAgPSBnZXRPd24oc3Rhck1hcCwgbmFtZVNlZ21lbnQpO1xuICAgICAgICAgICAgICAgICAgICAgICAgc3RhckkgPSBpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYgKCFmb3VuZE1hcCAmJiBmb3VuZFN0YXJNYXApIHtcbiAgICAgICAgICAgICAgICAgICAgZm91bmRNYXAgPSBmb3VuZFN0YXJNYXA7XG4gICAgICAgICAgICAgICAgICAgIGZvdW5kSSA9IHN0YXJJO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGlmIChmb3VuZE1hcCkge1xuICAgICAgICAgICAgICAgICAgICBuYW1lUGFydHMuc3BsaWNlKDAsIGZvdW5kSSwgZm91bmRNYXApO1xuICAgICAgICAgICAgICAgICAgICBuYW1lID0gbmFtZVBhcnRzLmpvaW4oJy8nKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vIElmIHRoZSBuYW1lIHBvaW50cyB0byBhIHBhY2thZ2UncyBuYW1lLCB1c2UgdGhlIHBhY2thZ2UgbWFpbiBpbnN0ZWFkLlxuICAgICAgICAgICAgcGtnTWFpbiA9IGdldE93bihjb25maWcucGtncywgbmFtZSk7XG5cbiAgICAgICAgICAgIHJldHVybiBwa2dNYWluID8gcGtnTWFpbiA6IG5hbWU7XG4gICAgICAgIH1cblxuICAgICAgICBmdW5jdGlvbiByZW1vdmVTY3JpcHQobmFtZSkge1xuICAgICAgICAgICAgaWYgKGlzQnJvd3Nlcikge1xuICAgICAgICAgICAgICAgIGVhY2goc2NyaXB0cygpLCBmdW5jdGlvbiAoc2NyaXB0Tm9kZSkge1xuICAgICAgICAgICAgICAgICAgICBpZiAoc2NyaXB0Tm9kZS5nZXRBdHRyaWJ1dGUoJ2RhdGEtcmVxdWlyZW1vZHVsZScpID09PSBuYW1lICYmXG4gICAgICAgICAgICAgICAgICAgICAgICBzY3JpcHROb2RlLmdldEF0dHJpYnV0ZSgnZGF0YS1yZXF1aXJlY29udGV4dCcpID09PSBjb250ZXh0LmNvbnRleHROYW1lKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBzY3JpcHROb2RlLnBhcmVudE5vZGUucmVtb3ZlQ2hpbGQoc2NyaXB0Tm9kZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgZnVuY3Rpb24gaGFzUGF0aEZhbGxiYWNrKGlkKSB7XG4gICAgICAgICAgICBsZXQgcGF0aENvbmZpZyA9IGdldE93bihjb25maWcucGF0aHMsIGlkKTtcbiAgICAgICAgICAgIGlmIChwYXRoQ29uZmlnICYmIGlzQXJyYXkocGF0aENvbmZpZykgJiYgcGF0aENvbmZpZy5sZW5ndGggPiAxKSB7XG4gICAgICAgICAgICAgICAgLy8gUG9wIG9mZiB0aGUgZmlyc3QgYXJyYXkgdmFsdWUsIHNpbmNlIGl0IGZhaWxlZCwgYW5kIHJldHJ5LlxuICAgICAgICAgICAgICAgIHBhdGhDb25maWcuc2hpZnQoKTtcbiAgICAgICAgICAgICAgICBjb250ZXh0LnJlcXVpcmUudW5kZWYoaWQpO1xuXG4gICAgICAgICAgICAgICAgLy8gQ3VzdG9tIHJlcXVpcmUgdGhhdCBkb2VzIG5vdCBkbyBtYXAgdHJhbnNsYXRpb24sIHNpbmNlIElEIGlzIFwiYWJzb2x1dGVcIiwgYWxyZWFkeSBtYXBwZWQvcmVzb2x2ZWQuXG4gICAgICAgICAgICAgICAgY29udGV4dC5tYWtlUmVxdWlyZShudWxsLCB7XG4gICAgICAgICAgICAgICAgICAgIHNraXBNYXA6IHRydWVcbiAgICAgICAgICAgICAgICB9KShbaWRdKTtcblxuICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgLy8gVHVybnMgYSBwbHVnaW4hcmVzb3VyY2UgdG8gW3BsdWdpbiwgcmVzb3VyY2VdIHdpdGggdGhlIHBsdWdpbiBiZWluZyB1bmRlZmluZWQgaWYgdGhlIG5hbWUgZGlkIG5vdCBoYXZlIGEgXG4gICAgICAgIC8vIHBsdWdpbiBwcmVmaXguXG4gICAgICAgIGZ1bmN0aW9uIHNwbGl0UHJlZml4KG5hbWUpIHtcbiAgICAgICAgICAgIGxldCBwcmVmaXgsXG4gICAgICAgICAgICAgICAgaW5kZXggPSBuYW1lID8gbmFtZS5pbmRleE9mKCchJykgOiAtMTtcbiAgICAgICAgICAgIGlmIChpbmRleCA+IC0xKSB7XG4gICAgICAgICAgICAgICAgcHJlZml4ID0gbmFtZS5zdWJzdHJpbmcoMCwgaW5kZXgpO1xuICAgICAgICAgICAgICAgIG5hbWUgPSBuYW1lLnN1YnN0cmluZyhpbmRleCArIDEsIG5hbWUubGVuZ3RoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHJldHVybiBbcHJlZml4LCBuYW1lXTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBDcmVhdGVzIGEgbW9kdWxlIG1hcHBpbmcgdGhhdCBpbmNsdWRlcyBwbHVnaW4gcHJlZml4LCBtb2R1bGUgbmFtZSwgYW5kIHBhdGguIElmIHBhcmVudE1vZHVsZU1hcCBpcyBwcm92aWRlZFxuICAgICAgICAgKiBpdCB3aWxsIGFsc28gbm9ybWFsaXplIHRoZSBuYW1lIHZpYSByZXF1aXJlLm5vcm1hbGl6ZSgpXG4gICAgICAgICAqXG4gICAgICAgICAqIEBwYXJhbSB7U3RyaW5nfSBuYW1lIFRoZSBtb2R1bGUgbmFtZS5cbiAgICAgICAgICogQHBhcmFtIHtTdHJpbmd9IFtwYXJlbnRNb2R1bGVNYXBdIFBhcmVudCBtb2R1bGUgbWFwIGZvciB0aGUgbW9kdWxlIG5hbWUsIHVzZWQgdG8gcmVzb2x2ZSByZWxhdGl2ZSBuYW1lcy5cbiAgICAgICAgICogQHBhcmFtIHtCb29sZWFufSBpc05vcm1hbGl6ZWQgSXMgdGhlIElEIGFscmVhZHkgbm9ybWFsaXplZD8gVGhpcyBpcyB0cnVlIGlmIHRoaXMgY2FsbCBpcyBkb25lIGZvciBhIGRlZmluZSgpXG4gICAgICAgICAqIG1vZHVsZSBJRC5cbiAgICAgICAgICogQHBhcmFtIHtCb29sZWFufSBhcHBseU1hcDogYXBwbHkgdGhlIG1hcCBjb25maWcgdG8gdGhlIElELiBTaG91bGQgb25seSBiZSB0cnVlIGlmIHRoaXMgbWFwIGlzIGZvciBhIGRlcGVuZGVuY3kuXG4gICAgICAgICAqXG4gICAgICAgICAqIEByZXR1cm4ge09iamVjdH1cbiAgICAgICAgICovXG4gICAgICAgIGZ1bmN0aW9uIG1ha2VNb2R1bGVNYXAobmFtZSwgcGFyZW50TW9kdWxlTWFwLCBpc05vcm1hbGl6ZWQsIGFwcGx5TWFwKSB7XG4gICAgICAgICAgICBsZXQgdXJsLCBwbHVnaW5Nb2R1bGUsIHN1ZmZpeCwgbmFtZVBhcnRzLFxuICAgICAgICAgICAgICAgIHByZWZpeCA9IG51bGwsXG4gICAgICAgICAgICAgICAgcGFyZW50TmFtZSA9IHBhcmVudE1vZHVsZU1hcCA/IHBhcmVudE1vZHVsZU1hcC5uYW1lIDogbnVsbCxcbiAgICAgICAgICAgICAgICBvcmlnaW5hbE5hbWUgPSBuYW1lLFxuICAgICAgICAgICAgICAgIGlzRGVmaW5lID0gdHJ1ZSxcbiAgICAgICAgICAgICAgICBub3JtYWxpemVkTmFtZSA9ICcnO1xuXG4gICAgICAgICAgICAvLyBJZiBubyBuYW1lLCB0aGVuIGl0IG1lYW5zIGl0IGlzIGEgcmVxdWlyZSBjYWxsLCBnZW5lcmF0ZSBhblxuICAgICAgICAgICAgLy8gaW50ZXJuYWwgbmFtZS5cbiAgICAgICAgICAgIGlmICghbmFtZSkge1xuICAgICAgICAgICAgICAgIGlzRGVmaW5lID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgbmFtZSA9ICdfQHInICsgKHJlcXVpcmVDb3VudGVyICs9IDEpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBuYW1lUGFydHMgPSBzcGxpdFByZWZpeChuYW1lKTtcbiAgICAgICAgICAgIHByZWZpeCA9IG5hbWVQYXJ0c1swXTtcbiAgICAgICAgICAgIG5hbWUgPSBuYW1lUGFydHNbMV07XG5cbiAgICAgICAgICAgIGlmIChwcmVmaXgpIHtcbiAgICAgICAgICAgICAgICBwcmVmaXggPSBub3JtYWxpemUocHJlZml4LCBwYXJlbnROYW1lLCBhcHBseU1hcCk7XG4gICAgICAgICAgICAgICAgcGx1Z2luTW9kdWxlID0gZ2V0T3duKGRlZmluZWQsIHByZWZpeCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vIEFjY291bnQgZm9yIHJlbGF0aXZlIHBhdGhzIGlmIHRoZXJlIGlzIGEgYmFzZSBuYW1lLlxuICAgICAgICAgICAgaWYgKG5hbWUpIHtcbiAgICAgICAgICAgICAgICBpZiAocHJlZml4KSB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChwbHVnaW5Nb2R1bGUgJiYgcGx1Z2luTW9kdWxlLm5vcm1hbGl6ZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgLy8gUGx1Z2luIGlzIGxvYWRlZCwgdXNlIGl0cyBub3JtYWxpemUgbWV0aG9kLlxuICAgICAgICAgICAgICAgICAgICAgICAgbm9ybWFsaXplZE5hbWUgPSBwbHVnaW5Nb2R1bGUubm9ybWFsaXplKG5hbWUsIGZ1bmN0aW9uIChuYW1lKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIG5vcm1hbGl6ZShuYW1lLCBwYXJlbnROYW1lLCBhcHBseU1hcCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIElmIG5lc3RlZCBwbHVnaW4gcmVmZXJlbmNlcywgdGhlbiBkbyBub3QgdHJ5IHRvXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBub3JtYWxpemUsIGFzIGl0IHdpbGwgbm90IG5vcm1hbGl6ZSBjb3JyZWN0bHkuIFRoaXNcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIHBsYWNlcyBhIHJlc3RyaWN0aW9uIG9uIHJlc291cmNlSWRzLCBhbmQgdGhlIGxvbmdlclxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gdGVybSBzb2x1dGlvbiBpcyBub3QgdG8gbm9ybWFsaXplIHVudGlsIHBsdWdpbnMgYXJlXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBsb2FkZWQgYW5kIGFsbCBub3JtYWxpemF0aW9ucyB0byBhbGxvdyBmb3IgYXN5bmNcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIGxvYWRpbmcgb2YgYSBsb2FkZXIgcGx1Z2luLiBCdXQgZm9yIG5vdywgZml4ZXMgdGhlXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBjb21tb24gdXNlcy4gRGV0YWlscyBpbiAjMTEzMVxuICAgICAgICAgICAgICAgICAgICAgICAgbm9ybWFsaXplZE5hbWUgPSBuYW1lLmluZGV4T2YoJyEnKSA9PT0gLTEgP1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG5vcm1hbGl6ZShuYW1lLCBwYXJlbnROYW1lLCBhcHBseU1hcCkgOlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG5hbWU7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAvLyBBIHJlZ3VsYXIgbW9kdWxlLlxuICAgICAgICAgICAgICAgICAgICBub3JtYWxpemVkTmFtZSA9IG5vcm1hbGl6ZShuYW1lLCBwYXJlbnROYW1lLCBhcHBseU1hcCk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy8gTm9ybWFsaXplZCBuYW1lIG1heSBiZSBhIHBsdWdpbiBJRCBkdWUgdG8gbWFwIGNvbmZpZ1xuICAgICAgICAgICAgICAgICAgICAvLyBhcHBsaWNhdGlvbiBpbiBub3JtYWxpemUuIFRoZSBtYXAgY29uZmlnIHZhbHVlcyBtdXN0XG4gICAgICAgICAgICAgICAgICAgIC8vIGFscmVhZHkgYmUgbm9ybWFsaXplZCwgc28gZG8gbm90IG5lZWQgdG8gcmVkbyB0aGF0IHBhcnQuXG4gICAgICAgICAgICAgICAgICAgIG5hbWVQYXJ0cyA9IHNwbGl0UHJlZml4KG5vcm1hbGl6ZWROYW1lKTtcbiAgICAgICAgICAgICAgICAgICAgcHJlZml4ID0gbmFtZVBhcnRzWzBdO1xuICAgICAgICAgICAgICAgICAgICBub3JtYWxpemVkTmFtZSA9IG5hbWVQYXJ0c1sxXTtcbiAgICAgICAgICAgICAgICAgICAgaXNOb3JtYWxpemVkID0gdHJ1ZTtcblxuICAgICAgICAgICAgICAgICAgICB1cmwgPSBjb250ZXh0Lm5hbWVUb1VybChub3JtYWxpemVkTmFtZSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBJZiB0aGUgaWQgaXMgYSBwbHVnaW4gaWQgdGhhdCBjYW5ub3QgYmUgZGV0ZXJtaW5lZCBpZiBpdCBuZWVkc1xuICAgICAgICAgICAgLy8gbm9ybWFsaXphdGlvbiwgc3RhbXAgaXQgd2l0aCBhIHVuaXF1ZSBJRCBzbyB0d28gbWF0Y2hpbmcgcmVsYXRpdmVcbiAgICAgICAgICAgIC8vIGlkcyB0aGF0IG1heSBjb25mbGljdCBjYW4gYmUgc2VwYXJhdGUuXG4gICAgICAgICAgICBzdWZmaXggPSBwcmVmaXggJiYgIXBsdWdpbk1vZHVsZSAmJiAhaXNOb3JtYWxpemVkID9cbiAgICAgICAgICAgICAgICAnX3Vubm9ybWFsaXplZCcgKyAodW5ub3JtYWxpemVkQ291bnRlciArPSAxKSA6XG4gICAgICAgICAgICAgICAgJyc7XG5cbiAgICAgICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICAgICAgcHJlZml4OiBwcmVmaXgsXG4gICAgICAgICAgICAgICAgbmFtZTogbm9ybWFsaXplZE5hbWUsXG4gICAgICAgICAgICAgICAgcGFyZW50TWFwOiBwYXJlbnRNb2R1bGVNYXAsXG4gICAgICAgICAgICAgICAgdW5ub3JtYWxpemVkOiAhIXN1ZmZpeCxcbiAgICAgICAgICAgICAgICB1cmw6IHVybCxcbiAgICAgICAgICAgICAgICBvcmlnaW5hbE5hbWU6IG9yaWdpbmFsTmFtZSxcbiAgICAgICAgICAgICAgICBpc0RlZmluZTogaXNEZWZpbmUsXG4gICAgICAgICAgICAgICAgaWQ6IChwcmVmaXggP1xuICAgICAgICAgICAgICAgICAgICBwcmVmaXggKyAnIScgKyBub3JtYWxpemVkTmFtZSA6XG4gICAgICAgICAgICAgICAgICAgIG5vcm1hbGl6ZWROYW1lKSArIHN1ZmZpeFxuICAgICAgICAgICAgfTtcbiAgICAgICAgfVxuXG4gICAgICAgIGZ1bmN0aW9uIGdldE1vZHVsZShkZXBNYXApIHtcbiAgICAgICAgICAgIGxldCBpZCA9IGRlcE1hcC5pZCxcbiAgICAgICAgICAgICAgICBtb2QgPSBnZXRPd24ocmVnaXN0cnksIGlkKTtcblxuICAgICAgICAgICAgaWYgKCFtb2QpIHtcbiAgICAgICAgICAgICAgICBtb2QgPSByZWdpc3RyeVtpZF0gPSBuZXcgY29udGV4dC5Nb2R1bGUoZGVwTWFwKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIG1vZDtcbiAgICAgICAgfVxuXG4gICAgICAgIGZ1bmN0aW9uIG9uKGRlcE1hcCwgbmFtZSwgZm4pIHtcbiAgICAgICAgICAgIGxldCBpZCA9IGRlcE1hcC5pZCxcbiAgICAgICAgICAgICAgICBtb2QgPSBnZXRPd24ocmVnaXN0cnksIGlkKTtcblxuICAgICAgICAgICAgaWYgKGhhc1Byb3AoZGVmaW5lZCwgaWQpICYmXG4gICAgICAgICAgICAgICAgKCFtb2QgfHwgbW9kLmRlZmluZUVtaXRDb21wbGV0ZSkpIHtcbiAgICAgICAgICAgICAgICBpZiAobmFtZSA9PT0gJ2RlZmluZWQnKSB7XG4gICAgICAgICAgICAgICAgICAgIGZuKGRlZmluZWRbaWRdKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIG1vZCA9IGdldE1vZHVsZShkZXBNYXApO1xuICAgICAgICAgICAgICAgIGlmIChtb2QuZXJyb3IgJiYgbmFtZSA9PT0gJ2Vycm9yJykge1xuICAgICAgICAgICAgICAgICAgICBmbihtb2QuZXJyb3IpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIG1vZC5vbihuYW1lLCBmbik7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgZnVuY3Rpb24gb25FcnJvcihlcnIsIGVycmJhY2spIHtcbiAgICAgICAgICAgIGxldCBpZHMgPSBlcnIucmVxdWlyZU1vZHVsZXMsXG4gICAgICAgICAgICAgICAgbm90aWZpZWQgPSBmYWxzZTtcblxuICAgICAgICAgICAgaWYgKGVycmJhY2spIHtcbiAgICAgICAgICAgICAgICBlcnJiYWNrKGVycik7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIGVhY2goaWRzLCBmdW5jdGlvbiAoaWQpIHtcbiAgICAgICAgICAgICAgICAgICAgbGV0IG1vZCA9IGdldE93bihyZWdpc3RyeSwgaWQpO1xuICAgICAgICAgICAgICAgICAgICBpZiAobW9kKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBTZXQgZXJyb3Igb24gbW9kdWxlLCBzbyBpdCBza2lwcyB0aW1lb3V0IGNoZWNrcy5cbiAgICAgICAgICAgICAgICAgICAgICAgIG1vZC5lcnJvciA9IGVycjtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChtb2QuZXZlbnRzLmVycm9yKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbm90aWZpZWQgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG1vZC5lbWl0KCdlcnJvcicsIGVycik7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIGlmICghbm90aWZpZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgcmVxLm9uRXJyb3IoZXJyKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICAvKipcbiAgICAgICAgICogSW50ZXJuYWwgbWV0aG9kIHRvIHRyYW5zZmVyIGdsb2JhbFF1ZXVlIGl0ZW1zIHRvIHRoaXMgY29udGV4dCdzXG4gICAgICAgICAqIGRlZlF1ZXVlLlxuICAgICAgICAgKi9cbiAgICAgICAgZnVuY3Rpb24gdGFrZUdsb2JhbFF1ZXVlKCkge1xuICAgICAgICAgICAgLy8gUHVzaCBhbGwgdGhlIGdsb2JhbERlZlF1ZXVlIGl0ZW1zIGludG8gdGhlIGNvbnRleHQncyBkZWZRdWV1ZVxuICAgICAgICAgICAgaWYgKGdsb2JhbERlZlF1ZXVlLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgIGVhY2goZ2xvYmFsRGVmUXVldWUsIGZ1bmN0aW9uIChxdWV1ZUl0ZW0pIHtcbiAgICAgICAgICAgICAgICAgICAgbGV0IGlkID0gcXVldWVJdGVtWzBdO1xuICAgICAgICAgICAgICAgICAgICBpZiAodHlwZW9mIGlkID09PSAnc3RyaW5nJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgY29udGV4dC5kZWZRdWV1ZU1hcFtpZF0gPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIGRlZlF1ZXVlLnB1c2gocXVldWVJdGVtKTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICBnbG9iYWxEZWZRdWV1ZSA9IFtdO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgaGFuZGxlcnMgPSB7XG4gICAgICAgICAgICAncmVxdWlyZSc6IGZ1bmN0aW9uIChtb2QpIHtcbiAgICAgICAgICAgICAgICBpZiAobW9kLnJlcXVpcmUpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIG1vZC5yZXF1aXJlO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiAobW9kLnJlcXVpcmUgPSBjb250ZXh0Lm1ha2VSZXF1aXJlKG1vZC5tYXApKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgJ2V4cG9ydHMnOiBmdW5jdGlvbiAobW9kKSB7XG4gICAgICAgICAgICAgICAgbW9kLnVzaW5nRXhwb3J0cyA9IHRydWU7XG4gICAgICAgICAgICAgICAgaWYgKG1vZC5tYXAuaXNEZWZpbmUpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKG1vZC5leHBvcnRzKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gKGRlZmluZWRbbW9kLm1hcC5pZF0gPSBtb2QuZXhwb3J0cyk7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gKG1vZC5leHBvcnRzID0gZGVmaW5lZFttb2QubWFwLmlkXSA9IHt9KTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAnbW9kdWxlJzogZnVuY3Rpb24gKG1vZCkge1xuICAgICAgICAgICAgICAgIGlmIChtb2QubW9kdWxlKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBtb2QubW9kdWxlO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiAobW9kLm1vZHVsZSA9IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlkOiBtb2QubWFwLmlkLFxuICAgICAgICAgICAgICAgICAgICAgICAgdXJpOiBtb2QubWFwLnVybCxcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbmZpZzogZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBnZXRPd24oY29uZmlnLmNvbmZpZywgbW9kLm1hcC5pZCkgfHwge307XG4gICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgZXhwb3J0czogbW9kLmV4cG9ydHMgfHwgKG1vZC5leHBvcnRzID0ge30pXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgICAgICBmdW5jdGlvbiBjbGVhblJlZ2lzdHJ5KGlkKSB7XG4gICAgICAgICAgICAvLyBDbGVhbiB1cCBtYWNoaW5lcnkgdXNlZCBmb3Igd2FpdGluZyBtb2R1bGVzLlxuICAgICAgICAgICAgZGVsZXRlIHJlZ2lzdHJ5W2lkXTtcbiAgICAgICAgICAgIGRlbGV0ZSBlbmFibGVkUmVnaXN0cnlbaWRdO1xuICAgICAgICB9XG5cbiAgICAgICAgZnVuY3Rpb24gYnJlYWtDeWNsZShtb2QsIHRyYWNlZCwgcHJvY2Vzc2VkKSB7XG4gICAgICAgICAgICBsZXQgaWQgPSBtb2QubWFwLmlkO1xuXG4gICAgICAgICAgICBpZiAobW9kLmVycm9yKSB7XG4gICAgICAgICAgICAgICAgbW9kLmVtaXQoJ2Vycm9yJywgbW9kLmVycm9yKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgdHJhY2VkW2lkXSA9IHRydWU7XG4gICAgICAgICAgICAgICAgZWFjaChtb2QuZGVwTWFwcywgZnVuY3Rpb24gKGRlcE1hcCwgaSkge1xuICAgICAgICAgICAgICAgICAgICBsZXQgZGVwSWQgPSBkZXBNYXAuaWQsXG4gICAgICAgICAgICAgICAgICAgICAgICBkZXAgPSBnZXRPd24ocmVnaXN0cnksIGRlcElkKTtcblxuICAgICAgICAgICAgICAgICAgICAvLyBPbmx5IGZvcmNlIHRoaW5ncyB0aGF0IGhhdmUgbm90IGNvbXBsZXRlZFxuICAgICAgICAgICAgICAgICAgICAvLyBiZWluZyBkZWZpbmVkLCBzbyBzdGlsbCBpbiB0aGUgcmVnaXN0cnksXG4gICAgICAgICAgICAgICAgICAgIC8vIGFuZCBvbmx5IGlmIGl0IGhhcyBub3QgYmVlbiBtYXRjaGVkIHVwXG4gICAgICAgICAgICAgICAgICAgIC8vIGluIHRoZSBtb2R1bGUgYWxyZWFkeS5cbiAgICAgICAgICAgICAgICAgICAgaWYgKGRlcCAmJiAhbW9kLmRlcE1hdGNoZWRbaV0gJiYgIXByb2Nlc3NlZFtkZXBJZF0pIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChnZXRPd24odHJhY2VkLCBkZXBJZCkpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBtb2QuZGVmaW5lRGVwKGksIGRlZmluZWRbZGVwSWRdKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBtb2QuY2hlY2soKTsgLy8gcGFzcyBmYWxzZT9cbiAgICAgICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWtDeWNsZShkZXAsIHRyYWNlZCwgcHJvY2Vzc2VkKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIHByb2Nlc3NlZFtpZF0gPSB0cnVlO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgZnVuY3Rpb24gY2hlY2tMb2FkZWQoKSB7XG4gICAgICAgICAgICBsZXQgZXJyLCB1c2luZ1BhdGhGYWxsYmFjayxcbiAgICAgICAgICAgICAgICB3YWl0SW50ZXJ2YWwgPSBjb25maWcud2FpdFNlY29uZHMgKiAxMDAwLFxuICAgICAgICAgICAgICAgIC8vIEl0IGlzIHBvc3NpYmxlIHRvIGRpc2FibGUgdGhlIHdhaXQgaW50ZXJ2YWwgYnkgdXNpbmcgd2FpdFNlY29uZHMgb2YgMC5cbiAgICAgICAgICAgICAgICBleHBpcmVkID0gd2FpdEludGVydmFsICYmIChjb250ZXh0LnN0YXJ0VGltZSArIHdhaXRJbnRlcnZhbCkgPCBuZXcgRGF0ZSgpLmdldFRpbWUoKSxcbiAgICAgICAgICAgICAgICBub0xvYWRzID0gW10sXG4gICAgICAgICAgICAgICAgcmVxQ2FsbHMgPSBbXSxcbiAgICAgICAgICAgICAgICBzdGlsbExvYWRpbmcgPSBmYWxzZSxcbiAgICAgICAgICAgICAgICBuZWVkQ3ljbGVDaGVjayA9IHRydWU7XG5cbiAgICAgICAgICAgIC8vIERvIG5vdCBib3RoZXIgaWYgdGhpcyBjYWxsIHdhcyBhIHJlc3VsdCBvZiBhIGN5Y2xlIGJyZWFrLlxuICAgICAgICAgICAgaWYgKGluQ2hlY2tMb2FkZWQpIHtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGluQ2hlY2tMb2FkZWQgPSB0cnVlO1xuXG4gICAgICAgICAgICAvLyBGaWd1cmUgb3V0IHRoZSBzdGF0ZSBvZiBhbGwgdGhlIG1vZHVsZXMuXG4gICAgICAgICAgICBlYWNoUHJvcChlbmFibGVkUmVnaXN0cnksIGZ1bmN0aW9uIChtb2QpIHtcbiAgICAgICAgICAgICAgICBsZXQgbWFwID0gbW9kLm1hcCxcbiAgICAgICAgICAgICAgICAgICAgbW9kSWQgPSBtYXAuaWQ7XG5cbiAgICAgICAgICAgICAgICAvLyBTa2lwIHRoaW5ncyB0aGF0IGFyZSBub3QgZW5hYmxlZCBvciBpbiBlcnJvciBzdGF0ZS5cbiAgICAgICAgICAgICAgICBpZiAoIW1vZC5lbmFibGVkKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBpZiAoIW1hcC5pc0RlZmluZSkge1xuICAgICAgICAgICAgICAgICAgICByZXFDYWxscy5wdXNoKG1vZCk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYgKCFtb2QuZXJyb3IpIHtcbiAgICAgICAgICAgICAgICAgICAgLy8gSWYgdGhlIG1vZHVsZSBzaG91bGQgYmUgZXhlY3V0ZWQsIGFuZCBpdCBoYXMgbm90XG4gICAgICAgICAgICAgICAgICAgIC8vIGJlZW4gaW5pdGVkIGFuZCB0aW1lIGlzIHVwLCByZW1lbWJlciBpdC5cbiAgICAgICAgICAgICAgICAgICAgaWYgKCFtb2QuaW5pdGVkICYmIGV4cGlyZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChoYXNQYXRoRmFsbGJhY2sobW9kSWQpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdXNpbmdQYXRoRmFsbGJhY2sgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHN0aWxsTG9hZGluZyA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG5vTG9hZHMucHVzaChtb2RJZCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVtb3ZlU2NyaXB0KG1vZElkKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIGlmICghbW9kLmluaXRlZCAmJiBtb2QuZmV0Y2hlZCAmJiBtYXAuaXNEZWZpbmUpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHN0aWxsTG9hZGluZyA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoIW1hcC5wcmVmaXgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBObyByZWFzb24gdG8ga2VlcCBsb29raW5nIGZvciB1bmZpbmlzaGVkXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gbG9hZGluZy4gSWYgdGhlIG9ubHkgc3RpbGxMb2FkaW5nIGlzIGFcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBwbHVnaW4gcmVzb3VyY2UgdGhvdWdoLCBrZWVwIGdvaW5nLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIGJlY2F1c2UgaXQgbWF5IGJlIHRoYXQgYSBwbHVnaW4gcmVzb3VyY2VcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBpcyB3YWl0aW5nIG9uIGEgbm9uLXBsdWdpbiBjeWNsZS5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gKG5lZWRDeWNsZUNoZWNrID0gZmFsc2UpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIGlmIChleHBpcmVkICYmIG5vTG9hZHMubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgLy8gSWYgd2FpdCB0aW1lIGV4cGlyZWQsIHRocm93IGVycm9yIG9mIHVubG9hZGVkIG1vZHVsZXMuXG4gICAgICAgICAgICAgICAgZXJyID0gbWFrZUVycm9yKCd0aW1lb3V0JywgJ0xvYWQgdGltZW91dCBmb3IgbW9kdWxlczogJyArIG5vTG9hZHMsIG51bGwsIG5vTG9hZHMpO1xuICAgICAgICAgICAgICAgIGVyci5jb250ZXh0TmFtZSA9IGNvbnRleHQuY29udGV4dE5hbWU7XG4gICAgICAgICAgICAgICAgcmV0dXJuIG9uRXJyb3IoZXJyKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy8gTm90IGV4cGlyZWQsIGNoZWNrIGZvciBhIGN5Y2xlLlxuICAgICAgICAgICAgaWYgKG5lZWRDeWNsZUNoZWNrKSB7XG4gICAgICAgICAgICAgICAgZWFjaChyZXFDYWxscywgZnVuY3Rpb24gKG1vZCkge1xuICAgICAgICAgICAgICAgICAgICBicmVha0N5Y2xlKG1vZCwge30sIHt9KTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy8gSWYgc3RpbGwgd2FpdGluZyBvbiBsb2FkcywgYW5kIHRoZSB3YWl0aW5nIGxvYWQgaXMgc29tZXRoaW5nXG4gICAgICAgICAgICAvLyBvdGhlciB0aGFuIGEgcGx1Z2luIHJlc291cmNlLCBvciB0aGVyZSBhcmUgc3RpbGwgb3V0c3RhbmRpbmdcbiAgICAgICAgICAgIC8vIHNjcmlwdHMsIHRoZW4ganVzdCB0cnkgYmFjayBsYXRlci5cbiAgICAgICAgICAgIGlmICgoIWV4cGlyZWQgfHwgdXNpbmdQYXRoRmFsbGJhY2spICYmIHN0aWxsTG9hZGluZykge1xuICAgICAgICAgICAgICAgIC8vIFNvbWV0aGluZyBpcyBzdGlsbCB3YWl0aW5nIHRvIGxvYWQuIFdhaXQgZm9yIGl0LCBidXQgb25seVxuICAgICAgICAgICAgICAgIC8vIGlmIGEgdGltZW91dCBpcyBub3QgYWxyZWFkeSBpbiBlZmZlY3QuXG4gICAgICAgICAgICAgICAgaWYgKChpc0Jyb3dzZXIgfHwgaXNXZWJXb3JrZXIpICYmICFjaGVja0xvYWRlZFRpbWVvdXRJZCkge1xuICAgICAgICAgICAgICAgICAgICBjaGVja0xvYWRlZFRpbWVvdXRJZCA9IHNldFRpbWVvdXQoZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgY2hlY2tMb2FkZWRUaW1lb3V0SWQgPSAwO1xuICAgICAgICAgICAgICAgICAgICAgICAgY2hlY2tMb2FkZWQoKTtcbiAgICAgICAgICAgICAgICAgICAgfSwgNTApO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaW5DaGVja0xvYWRlZCA9IGZhbHNlO1xuICAgICAgICB9XG5cbiAgICAgICAgTW9kdWxlID0gZnVuY3Rpb24gKG1hcCkge1xuICAgICAgICAgICAgdGhpcy5ldmVudHMgPSBnZXRPd24odW5kZWZFdmVudHMsIG1hcC5pZCkgfHwge307XG4gICAgICAgICAgICB0aGlzLm1hcCA9IG1hcDtcbiAgICAgICAgICAgIHRoaXMuc2hpbSA9IGdldE93bihjb25maWcuc2hpbSwgbWFwLmlkKTtcbiAgICAgICAgICAgIHRoaXMuZGVwRXhwb3J0cyA9IFtdO1xuICAgICAgICAgICAgdGhpcy5kZXBNYXBzID0gW107XG4gICAgICAgICAgICB0aGlzLmRlcE1hdGNoZWQgPSBbXTtcbiAgICAgICAgICAgIHRoaXMucGx1Z2luTWFwcyA9IHt9O1xuICAgICAgICAgICAgdGhpcy5kZXBDb3VudCA9IDA7XG5cbiAgICAgICAgICAgIC8qIHRoaXMuZXhwb3J0cyB0aGlzLmZhY3RvcnlcbiAgICAgICAgICAgICB0aGlzLmRlcE1hcHMgPSBbXSxcbiAgICAgICAgICAgICB0aGlzLmVuYWJsZWQsIHRoaXMuZmV0Y2hlZFxuICAgICAgICAgICAgICovXG4gICAgICAgIH07XG5cbiAgICAgICAgTW9kdWxlLnByb3RvdHlwZSA9IHtcbiAgICAgICAgICAgIGluaXQ6IGZ1bmN0aW9uIChkZXBNYXBzLCBmYWN0b3J5LCBlcnJiYWNrLCBvcHRpb25zKSB7XG4gICAgICAgICAgICAgICAgb3B0aW9ucyA9IG9wdGlvbnMgfHwge307XG5cbiAgICAgICAgICAgICAgICAvLyBEbyBub3QgZG8gbW9yZSBpbml0cyBpZiBhbHJlYWR5IGRvbmUuIENhbiBoYXBwZW4gaWYgdGhlcmVcbiAgICAgICAgICAgICAgICAvLyBhcmUgbXVsdGlwbGUgZGVmaW5lIGNhbGxzIGZvciB0aGUgc2FtZSBtb2R1bGUuIFRoYXQgaXMgbm90XG4gICAgICAgICAgICAgICAgLy8gYSBub3JtYWwsIGNvbW1vbiBjYXNlLCBidXQgaXQgaXMgYWxzbyBub3QgdW5leHBlY3RlZC5cbiAgICAgICAgICAgICAgICBpZiAodGhpcy5pbml0ZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHRoaXMuZmFjdG9yeSA9IGZhY3Rvcnk7XG5cbiAgICAgICAgICAgICAgICBpZiAoZXJyYmFjaykge1xuICAgICAgICAgICAgICAgICAgICAvLyBSZWdpc3RlciBmb3IgZXJyb3JzIG9uIHRoaXMgbW9kdWxlLlxuICAgICAgICAgICAgICAgICAgICB0aGlzLm9uKCdlcnJvcicsIGVycmJhY2spO1xuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAodGhpcy5ldmVudHMuZXJyb3IpIHtcbiAgICAgICAgICAgICAgICAgICAgLy8gSWYgbm8gZXJyYmFjayBhbHJlYWR5LCBidXQgdGhlcmUgYXJlIGVycm9yIGxpc3RlbmVyc1xuICAgICAgICAgICAgICAgICAgICAvLyBvbiB0aGlzIG1vZHVsZSwgc2V0IHVwIGFuIGVycmJhY2sgdG8gcGFzcyB0byB0aGUgZGVwcy5cbiAgICAgICAgICAgICAgICAgICAgZXJyYmFjayA9IGJpbmQodGhpcywgZnVuY3Rpb24gKGVycikge1xuICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5lbWl0KCdlcnJvcicsIGVycik7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIC8vIERvIGEgY29weSBvZiB0aGUgZGVwZW5kZW5jeSBhcnJheSwgc28gdGhhdFxuICAgICAgICAgICAgICAgIC8vIHNvdXJjZSBpbnB1dHMgYXJlIG5vdCBtb2RpZmllZC4gRm9yIGV4YW1wbGVcbiAgICAgICAgICAgICAgICAvLyBcInNoaW1cIiBkZXBzIGFyZSBwYXNzZWQgaW4gaGVyZSBkaXJlY3RseSwgYW5kXG4gICAgICAgICAgICAgICAgLy8gZG9pbmcgYSBkaXJlY3QgbW9kaWZpY2F0aW9uIG9mIHRoZSBkZXBNYXBzIGFycmF5XG4gICAgICAgICAgICAgICAgLy8gd291bGQgYWZmZWN0IHRoYXQgY29uZmlnLlxuICAgICAgICAgICAgICAgIHRoaXMuZGVwTWFwcyA9IGRlcE1hcHMgJiYgZGVwTWFwcy5zbGljZSgwKTtcblxuICAgICAgICAgICAgICAgIHRoaXMuZXJyYmFjayA9IGVycmJhY2s7XG5cbiAgICAgICAgICAgICAgICAvLyBJbmRpY2F0ZSB0aGlzIG1vZHVsZSBoYXMgYmUgaW5pdGlhbGl6ZWRcbiAgICAgICAgICAgICAgICB0aGlzLmluaXRlZCA9IHRydWU7XG5cbiAgICAgICAgICAgICAgICB0aGlzLmlnbm9yZSA9IG9wdGlvbnMuaWdub3JlO1xuXG4gICAgICAgICAgICAgICAgLy8gQ291bGQgaGF2ZSBvcHRpb24gdG8gaW5pdCB0aGlzIG1vZHVsZSBpbiBlbmFibGVkIG1vZGUsXG4gICAgICAgICAgICAgICAgLy8gb3IgY291bGQgaGF2ZSBiZWVuIHByZXZpb3VzbHkgbWFya2VkIGFzIGVuYWJsZWQuIEhvd2V2ZXIsXG4gICAgICAgICAgICAgICAgLy8gdGhlIGRlcGVuZGVuY2llcyBhcmUgbm90IGtub3duIHVudGlsIGluaXQgaXMgY2FsbGVkLiBTb1xuICAgICAgICAgICAgICAgIC8vIGlmIGVuYWJsZWQgcHJldmlvdXNseSwgbm93IHRyaWdnZXIgZGVwZW5kZW5jaWVzIGFzIGVuYWJsZWQuXG4gICAgICAgICAgICAgICAgaWYgKG9wdGlvbnMuZW5hYmxlZCB8fCB0aGlzLmVuYWJsZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgLy8gRW5hYmxlIHRoaXMgbW9kdWxlIGFuZCBkZXBlbmRlbmNpZXMuXG4gICAgICAgICAgICAgICAgICAgIC8vIFdpbGwgY2FsbCB0aGlzLmNoZWNrKClcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5lbmFibGUoKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLmNoZWNrKCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgZGVmaW5lRGVwOiBmdW5jdGlvbiAoaSwgZGVwRXhwb3J0cykge1xuICAgICAgICAgICAgICAgIC8vIEJlY2F1c2Ugb2YgY3ljbGVzLCBkZWZpbmVkIGNhbGxiYWNrIGZvciBhIGdpdmVuXG4gICAgICAgICAgICAgICAgLy8gZXhwb3J0IGNhbiBiZSBjYWxsZWQgbW9yZSB0aGFuIG9uY2UuXG4gICAgICAgICAgICAgICAgaWYgKCF0aGlzLmRlcE1hdGNoZWRbaV0pIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5kZXBNYXRjaGVkW2ldID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5kZXBDb3VudCAtPSAxO1xuICAgICAgICAgICAgICAgICAgICB0aGlzLmRlcEV4cG9ydHNbaV0gPSBkZXBFeHBvcnRzO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIGZldGNoOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgaWYgKHRoaXMuZmV0Y2hlZCkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIHRoaXMuZmV0Y2hlZCA9IHRydWU7XG5cbiAgICAgICAgICAgICAgICBjb250ZXh0LnN0YXJ0VGltZSA9IChuZXcgRGF0ZSgpKS5nZXRUaW1lKCk7XG5cbiAgICAgICAgICAgICAgICBsZXQgbWFwID0gdGhpcy5tYXA7XG5cbiAgICAgICAgICAgICAgICAvLyBJZiB0aGUgbWFuYWdlciBpcyBmb3IgYSBwbHVnaW4gbWFuYWdlZCByZXNvdXJjZSxcbiAgICAgICAgICAgICAgICAvLyBhc2sgdGhlIHBsdWdpbiB0byBsb2FkIGl0IG5vdy5cbiAgICAgICAgICAgICAgICBpZiAodGhpcy5zaGltKSB7XG4gICAgICAgICAgICAgICAgICAgIGNvbnRleHQubWFrZVJlcXVpcmUodGhpcy5tYXAsIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGVuYWJsZUJ1aWxkQ2FsbGJhY2s6IHRydWVcbiAgICAgICAgICAgICAgICAgICAgfSkodGhpcy5zaGltLmRlcHMgfHwgW10sIGJpbmQodGhpcywgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIG1hcC5wcmVmaXggPyB0aGlzLmNhbGxQbHVnaW4oKSA6IHRoaXMubG9hZCgpO1xuICAgICAgICAgICAgICAgICAgICB9KSk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgLy8gUmVndWxhciBkZXBlbmRlbmN5LlxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gbWFwLnByZWZpeCA/IHRoaXMuY2FsbFBsdWdpbigpIDogdGhpcy5sb2FkKCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgbG9hZDogZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIGxldCB1cmwgPSB0aGlzLm1hcC51cmw7XG5cbiAgICAgICAgICAgICAgICAvLyBSZWd1bGFyIGRlcGVuZGVuY3kuXG4gICAgICAgICAgICAgICAgaWYgKCF1cmxGZXRjaGVkW3VybF0pIHtcbiAgICAgICAgICAgICAgICAgICAgdXJsRmV0Y2hlZFt1cmxdID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgY29udGV4dC5sb2FkKHRoaXMubWFwLmlkLCB1cmwpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIC8qKlxuICAgICAgICAgICAgICogQ2hlY2tzIGlmIHRoZSBtb2R1bGUgaXMgcmVhZHkgdG8gZGVmaW5lIGl0c2VsZiwgYW5kIGlmIHNvLFxuICAgICAgICAgICAgICogZGVmaW5lIGl0LlxuICAgICAgICAgICAgICovXG4gICAgICAgICAgICBjaGVjazogZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIGlmICghdGhpcy5lbmFibGVkIHx8IHRoaXMuZW5hYmxpbmcpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGxldCBlcnIsIGNqc01vZHVsZSxcbiAgICAgICAgICAgICAgICAgICAgaWQgPSB0aGlzLm1hcC5pZCxcbiAgICAgICAgICAgICAgICAgICAgZGVwRXhwb3J0cyA9IHRoaXMuZGVwRXhwb3J0cyxcbiAgICAgICAgICAgICAgICAgICAgZXhwb3J0cyA9IHRoaXMuZXhwb3J0cyxcbiAgICAgICAgICAgICAgICAgICAgZmFjdG9yeSA9IHRoaXMuZmFjdG9yeTtcblxuICAgICAgICAgICAgICAgIGlmICghdGhpcy5pbml0ZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgLy8gT25seSBmZXRjaCBpZiBub3QgYWxyZWFkeSBpbiB0aGUgZGVmUXVldWUuXG4gICAgICAgICAgICAgICAgICAgIGlmICghaGFzUHJvcChjb250ZXh0LmRlZlF1ZXVlTWFwLCBpZCkpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuZmV0Y2goKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAodGhpcy5lcnJvcikge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLmVtaXQoJ2Vycm9yJywgdGhpcy5lcnJvcik7XG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmICghdGhpcy5kZWZpbmluZykge1xuICAgICAgICAgICAgICAgICAgICAvLyBUaGUgZmFjdG9yeSBjb3VsZCB0cmlnZ2VyIGFub3RoZXIgcmVxdWlyZSBjYWxsXG4gICAgICAgICAgICAgICAgICAgIC8vIHRoYXQgd291bGQgcmVzdWx0IGluIGNoZWNraW5nIHRoaXMgbW9kdWxlIHRvXG4gICAgICAgICAgICAgICAgICAgIC8vIGRlZmluZSBpdHNlbGYgYWdhaW4uIElmIGFscmVhZHkgaW4gdGhlIHByb2Nlc3NcbiAgICAgICAgICAgICAgICAgICAgLy8gb2YgZG9pbmcgdGhhdCwgc2tpcCB0aGlzIHdvcmsuXG4gICAgICAgICAgICAgICAgICAgIHRoaXMuZGVmaW5pbmcgPSB0cnVlO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmICh0aGlzLmRlcENvdW50IDwgMSAmJiAhdGhpcy5kZWZpbmVkKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoaXNGdW5jdGlvbihmYWN0b3J5KSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRyeSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGV4cG9ydHMgPSBjb250ZXh0LmV4ZWNDYihpZCwgZmFjdG9yeSwgZGVwRXhwb3J0cywgZXhwb3J0cyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSBjYXRjaCAoZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBlcnIgPSBlO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIEZhdm9yIHJldHVybiB2YWx1ZSBvdmVyIGV4cG9ydHMuIElmIG5vZGUvY2pzIGluIHBsYXksXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gdGhlbiB3aWxsIG5vdCBoYXZlIGEgcmV0dXJuIHZhbHVlIGFueXdheS4gRmF2b3JcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBtb2R1bGUuZXhwb3J0cyBhc3NpZ25tZW50IG92ZXIgZXhwb3J0cyBvYmplY3QuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHRoaXMubWFwLmlzRGVmaW5lICYmIGV4cG9ydHMgPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBjanNNb2R1bGUgPSB0aGlzLm1vZHVsZTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGNqc01vZHVsZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZXhwb3J0cyA9IGNqc01vZHVsZS5leHBvcnRzO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKHRoaXMudXNpbmdFeHBvcnRzKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBleHBvcnRzIGFscmVhZHkgc2V0IHRoZSBkZWZpbmVkIHZhbHVlLlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZXhwb3J0cyA9IHRoaXMuZXhwb3J0cztcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmIChlcnIpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gSWYgdGhlcmUgaXMgYW4gZXJyb3IgbGlzdGVuZXIsIGZhdm9yIHBhc3NpbmdcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gdG8gdGhhdCBpbnN0ZWFkIG9mIHRocm93aW5nIGFuIGVycm9yLiBIb3dldmVyLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBvbmx5IGRvIGl0IGZvciBkZWZpbmUoKSdkICBtb2R1bGVzLiByZXF1aXJlXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIGVycmJhY2tzIHNob3VsZCBub3QgYmUgY2FsbGVkIGZvciBmYWlsdXJlcyBpblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyB0aGVpciBjYWxsYmFja3MgKCM2OTkpLiBIb3dldmVyIGlmIGEgZ2xvYmFsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIG9uRXJyb3IgaXMgc2V0LCB1c2UgdGhhdC5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCh0aGlzLmV2ZW50cy5lcnJvciAmJiB0aGlzLm1hcC5pc0RlZmluZSkgfHxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJlcS5vbkVycm9yICE9PSBkZWZhdWx0T25FcnJvcikge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZXJyLnJlcXVpcmVNYXAgPSB0aGlzLm1hcDtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGVyci5yZXF1aXJlTW9kdWxlcyA9IHRoaXMubWFwLmlzRGVmaW5lID8gW3RoaXMubWFwLmlkXSA6IG51bGw7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBlcnIucmVxdWlyZVR5cGUgPSB0aGlzLm1hcC5pc0RlZmluZSA/ICdkZWZpbmUnIDogJ3JlcXVpcmUnO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIG9uRXJyb3IoKHRoaXMuZXJyb3IgPSBlcnIpKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIGlmICh0eXBlb2YgY29uc29sZSAhPT0gJ3VuZGVmaW5lZCcgJiZcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIExvZyB0aGUgZXJyb3IgZm9yIGRlYnVnZ2luZy4gSWYgcHJvbWlzZXMgY291bGQgYmVcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIHVzZWQsIHRoaXMgd291bGQgYmUgZGlmZmVyZW50LCBidXQgbWFraW5nIGRvLlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZS5lcnJvcihlcnIpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gRG8gbm90IHdhbnQgdG8gY29tcGxldGVseSBsb3NlIHRoZSBlcnJvci4gV2hpbGUgdGhpc1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gd2lsbCBtZXNzIHVwIHByb2Nlc3NpbmcgYW5kIGxlYWQgdG8gc2ltaWxhciByZXN1bHRzXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBhcyBidWcgMTQ0MCwgaXQgYXQgbGVhc3Qgc3VyZmFjZXMgdGhlIGVycm9yLlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVxLm9uRXJyb3IoZXJyKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gSnVzdCBhIGxpdGVyYWwgdmFsdWVcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBleHBvcnRzID0gZmFjdG9yeTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5leHBvcnRzID0gZXhwb3J0cztcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHRoaXMubWFwLmlzRGVmaW5lICYmICF0aGlzLmlnbm9yZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRlZmluZWRbaWRdID0gZXhwb3J0cztcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmIChyZXEub25SZXNvdXJjZUxvYWQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGV0IHJlc0xvYWRNYXBzID0gW107XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGVhY2godGhpcy5kZXBNYXBzLCBmdW5jdGlvbiAoZGVwTWFwKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXNMb2FkTWFwcy5wdXNoKGRlcE1hcC5ub3JtYWxpemVkTWFwIHx8IGRlcE1hcCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXEub25SZXNvdXJjZUxvYWQoY29udGV4dCwgdGhpcy5tYXAsIHJlc0xvYWRNYXBzKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIENsZWFuIHVwXG4gICAgICAgICAgICAgICAgICAgICAgICBjbGVhblJlZ2lzdHJ5KGlkKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5kZWZpbmVkID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIC8vIEZpbmlzaGVkIHRoZSBkZWZpbmUgc3RhZ2UuIEFsbG93IGNhbGxpbmcgY2hlY2sgYWdhaW5cbiAgICAgICAgICAgICAgICAgICAgLy8gdG8gYWxsb3cgZGVmaW5lIG5vdGlmaWNhdGlvbnMgYmVsb3cgaW4gdGhlIGNhc2Ugb2YgYVxuICAgICAgICAgICAgICAgICAgICAvLyBjeWNsZS5cbiAgICAgICAgICAgICAgICAgICAgdGhpcy5kZWZpbmluZyA9IGZhbHNlO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmICh0aGlzLmRlZmluZWQgJiYgIXRoaXMuZGVmaW5lRW1pdHRlZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5kZWZpbmVFbWl0dGVkID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuZW1pdCgnZGVmaW5lZCcsIHRoaXMuZXhwb3J0cyk7XG4gICAgICAgICAgICAgICAgICAgICAgICB0aGlzLmRlZmluZUVtaXRDb21wbGV0ZSA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIGNhbGxQbHVnaW46IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICBsZXQgbWFwID0gdGhpcy5tYXA7XG4gICAgICAgICAgICAgICAgbGV0IGlkID0gbWFwLmlkO1xuICAgICAgICAgICAgICAgIC8vIE1hcCBhbHJlYWR5IG5vcm1hbGl6ZWQgdGhlIHByZWZpeC5cbiAgICAgICAgICAgICAgICBsZXQgcGx1Z2luTWFwID0gbWFrZU1vZHVsZU1hcChtYXAucHJlZml4KTtcblxuICAgICAgICAgICAgICAgIC8vIE1hcmsgdGhpcyBhcyBhIGRlcGVuZGVuY3kgZm9yIHRoaXMgcGx1Z2luLCBzbyBpdFxuICAgICAgICAgICAgICAgIC8vIGNhbiBiZSB0cmFjZWQgZm9yIGN5Y2xlcy5cbiAgICAgICAgICAgICAgICB0aGlzLmRlcE1hcHMucHVzaChwbHVnaW5NYXApO1xuXG4gICAgICAgICAgICAgICAgb24ocGx1Z2luTWFwLCAnZGVmaW5lZCcsIGJpbmQodGhpcywgZnVuY3Rpb24gKHBsdWdpbikge1xuICAgICAgICAgICAgICAgICAgICBsZXQgbG9hZCwgbm9ybWFsaXplZE1hcCwgbm9ybWFsaXplZE1vZCxcbiAgICAgICAgICAgICAgICAgICAgICAgIGJ1bmRsZUlkID0gZ2V0T3duKGJ1bmRsZXNNYXAsIHRoaXMubWFwLmlkKSxcbiAgICAgICAgICAgICAgICAgICAgICAgIG5hbWUgPSB0aGlzLm1hcC5uYW1lLFxuICAgICAgICAgICAgICAgICAgICAgICAgcGFyZW50TmFtZSA9IHRoaXMubWFwLnBhcmVudE1hcCA/IHRoaXMubWFwLnBhcmVudE1hcC5uYW1lIDogbnVsbCxcbiAgICAgICAgICAgICAgICAgICAgICAgIGxvY2FsUmVxdWlyZSA9IGNvbnRleHQubWFrZVJlcXVpcmUobWFwLnBhcmVudE1hcCwge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGVuYWJsZUJ1aWxkQ2FsbGJhY2s6IHRydWVcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vIElmIGN1cnJlbnQgbWFwIGlzIG5vdCBub3JtYWxpemVkLCB3YWl0IGZvciB0aGF0XG4gICAgICAgICAgICAgICAgICAgIC8vIG5vcm1hbGl6ZWQgbmFtZSB0byBsb2FkIGluc3RlYWQgb2YgY29udGludWluZy5cbiAgICAgICAgICAgICAgICAgICAgaWYgKHRoaXMubWFwLnVubm9ybWFsaXplZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgLy8gTm9ybWFsaXplIHRoZSBJRCBpZiB0aGUgcGx1Z2luIGFsbG93cyBpdC5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChwbHVnaW4ubm9ybWFsaXplKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbmFtZSA9IHBsdWdpbi5ub3JtYWxpemUobmFtZSwgZnVuY3Rpb24gKG5hbWUpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIG5vcm1hbGl6ZShuYW1lLCBwYXJlbnROYW1lLCB0cnVlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KSB8fCAnJztcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gcHJlZml4IGFuZCBuYW1lIHNob3VsZCBhbHJlYWR5IGJlIG5vcm1hbGl6ZWQsIG5vIG5lZWRcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIGZvciBhcHBseWluZyBtYXAgY29uZmlnIGFnYWluIGVpdGhlci5cbiAgICAgICAgICAgICAgICAgICAgICAgIG5vcm1hbGl6ZWRNYXAgPSBtYWtlTW9kdWxlTWFwKG1hcC5wcmVmaXggKyAnIScgKyBuYW1lLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMubWFwLnBhcmVudE1hcCk7XG4gICAgICAgICAgICAgICAgICAgICAgICBvbihub3JtYWxpemVkTWFwLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICdkZWZpbmVkJywgYmluZCh0aGlzLCBmdW5jdGlvbiAodmFsdWUpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5tYXAubm9ybWFsaXplZE1hcCA9IG5vcm1hbGl6ZWRNYXA7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuaW5pdChbXSwgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHZhbHVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9LCBudWxsLCB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBlbmFibGVkOiB0cnVlLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWdub3JlOiB0cnVlXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgbm9ybWFsaXplZE1vZCA9IGdldE93bihyZWdpc3RyeSwgbm9ybWFsaXplZE1hcC5pZCk7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAobm9ybWFsaXplZE1vZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIE1hcmsgdGhpcyBhcyBhIGRlcGVuZGVuY3kgZm9yIHRoaXMgcGx1Z2luLCBzbyBpdFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIGNhbiBiZSB0cmFjZWQgZm9yIGN5Y2xlcy5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGlzLmRlcE1hcHMucHVzaChub3JtYWxpemVkTWFwKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmICh0aGlzLmV2ZW50cy5lcnJvcikge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBub3JtYWxpemVkTW9kLm9uKCdlcnJvcicsIGJpbmQodGhpcywgZnVuY3Rpb24gKGVycikge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5lbWl0KCdlcnJvcicsIGVycik7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbm9ybWFsaXplZE1vZC5lbmFibGUoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgLy8gSWYgYSBwYXRocyBjb25maWcsIHRoZW4ganVzdCBsb2FkIHRoYXQgZmlsZSBpbnN0ZWFkIHRvXG4gICAgICAgICAgICAgICAgICAgIC8vIHJlc29sdmUgdGhlIHBsdWdpbiwgYXMgaXQgaXMgYnVpbHQgaW50byB0aGF0IHBhdGhzIGxheWVyLlxuICAgICAgICAgICAgICAgICAgICBpZiAoYnVuZGxlSWQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMubWFwLnVybCA9IGNvbnRleHQubmFtZVRvVXJsKGJ1bmRsZUlkKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMubG9hZCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgbG9hZCA9IGJpbmQodGhpcywgZnVuY3Rpb24gKHZhbHVlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB0aGlzLmluaXQoW10sIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gdmFsdWU7XG4gICAgICAgICAgICAgICAgICAgICAgICB9LCBudWxsLCB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZW5hYmxlZDogdHJ1ZVxuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgIGxvYWQuZXJyb3IgPSBiaW5kKHRoaXMsIGZ1bmN0aW9uIChlcnIpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuaW5pdGVkID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuZXJyb3IgPSBlcnI7XG4gICAgICAgICAgICAgICAgICAgICAgICBlcnIucmVxdWlyZU1vZHVsZXMgPSBbaWRdO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBSZW1vdmUgdGVtcCB1bm5vcm1hbGl6ZWQgbW9kdWxlcyBmb3IgdGhpcyBtb2R1bGUsIHNpbmNlIHRoZXkgd2lsbCBuZXZlciBiZSByZXNvbHZlZCBvdGhlcndpc2UgXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBub3cuXG4gICAgICAgICAgICAgICAgICAgICAgICBlYWNoUHJvcChyZWdpc3RyeSwgZnVuY3Rpb24gKG1vZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmIChtb2QubWFwLmlkLmluZGV4T2YoaWQgKyAnX3Vubm9ybWFsaXplZCcpID09PSAwKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNsZWFuUmVnaXN0cnkobW9kLm1hcC5pZCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIG9uRXJyb3IoZXJyKTtcbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy8gQWxsb3cgcGx1Z2lucyB0byBsb2FkIG90aGVyIGNvZGUgd2l0aG91dCBoYXZpbmcgdG8ga25vdyB0aGUgY29udGV4dCBvciBob3cgdG8gJ2NvbXBsZXRlJyB0aGUgXG4gICAgICAgICAgICAgICAgICAgIC8vIGxvYWQuXG4gICAgICAgICAgICAgICAgICAgIGxvYWQuZnJvbVRleHQgPSBiaW5kKHRoaXMsIGZ1bmN0aW9uICh0ZXh0LCB0ZXh0QWx0KSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAvKmpzbGludCBldmlsOiB0cnVlICovXG4gICAgICAgICAgICAgICAgICAgICAgICBsZXQgbW9kdWxlTmFtZSA9IG1hcC5uYW1lLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG1vZHVsZU1hcCA9IG1ha2VNb2R1bGVNYXAobW9kdWxlTmFtZSksXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaGFzSW50ZXJhY3RpdmUgPSB1c2VJbnRlcmFjdGl2ZTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gQXMgb2YgMi4xLjAsIHN1cHBvcnQganVzdCBwYXNzaW5nIHRoZSB0ZXh0LCB0byByZWluZm9yY2UgZnJvbVRleHQgb25seSBiZWluZyBjYWxsZWQgb25jZSBwZXIgXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyByZXNvdXJjZS4gU3RpbGwgc3VwcG9ydCBvbGQgc3R5bGUgb2YgcGFzc2luZyBtb2R1bGVOYW1lIGJ1dCBkaXNjYXJkIHRoYXQgbW9kdWxlTmFtZSBpbiBmYXZvciBcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIG9mIHRoZSBpbnRlcm5hbCByZWYuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAodGV4dEFsdCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRleHQgPSB0ZXh0QWx0O1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBUdXJuIG9mZiBpbnRlcmFjdGl2ZSBzY3JpcHQgbWF0Y2hpbmcgZm9yIElFIGZvciBhbnkgZGVmaW5lIGNhbGxzIGluIHRoZSB0ZXh0LCB0aGVuIHR1cm4gaXQgXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBiYWNrIG9uIGF0IHRoZSBlbmQuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoaGFzSW50ZXJhY3RpdmUpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB1c2VJbnRlcmFjdGl2ZSA9IGZhbHNlO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBQcmltZSB0aGUgc3lzdGVtIGJ5IGNyZWF0aW5nIGEgbW9kdWxlIGluc3RhbmNlIGZvciBpdC5cbiAgICAgICAgICAgICAgICAgICAgICAgIGdldE1vZHVsZShtb2R1bGVNYXApO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBUcmFuc2ZlciBhbnkgY29uZmlnIHRvIHRoaXMgb3RoZXIgbW9kdWxlLlxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGhhc1Byb3AoY29uZmlnLmNvbmZpZywgaWQpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY29uZmlnLmNvbmZpZ1ttb2R1bGVOYW1lXSA9IGNvbmZpZy5jb25maWdbaWRdO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICB0cnkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJlcS5leGVjKHRleHQpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSBjYXRjaCAoZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBvbkVycm9yKG1ha2VFcnJvcignZnJvbXRleHRldmFsJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ2Zyb21UZXh0IGV2YWwgZm9yICcgKyBpZCArXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICcgZmFpbGVkOiAnICsgZSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgW2lkXSkpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoaGFzSW50ZXJhY3RpdmUpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB1c2VJbnRlcmFjdGl2ZSA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIE1hcmsgdGhpcyBhcyBhIGRlcGVuZGVuY3kgZm9yIHRoZSBwbHVnaW4gcmVzb3VyY2UuXG4gICAgICAgICAgICAgICAgICAgICAgICB0aGlzLmRlcE1hcHMucHVzaChtb2R1bGVNYXApO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBTdXBwb3J0IGFub255bW91cyBtb2R1bGVzLlxuICAgICAgICAgICAgICAgICAgICAgICAgY29udGV4dC5jb21wbGV0ZUxvYWQobW9kdWxlTmFtZSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIEJpbmQgdGhlIHZhbHVlIG9mIHRoYXQgbW9kdWxlIHRvIHRoZSB2YWx1ZSBmb3IgdGhpcyByZXNvdXJjZSBJRC5cbiAgICAgICAgICAgICAgICAgICAgICAgIGxvY2FsUmVxdWlyZShbbW9kdWxlTmFtZV0sIGxvYWQpO1xuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICAvLyBVc2UgcGFyZW50TmFtZSBoZXJlIHNpbmNlIHRoZSBwbHVnaW4ncyBuYW1lIGlzIG5vdCByZWxpYWJsZSwgY291bGQgYmUgc29tZSB3ZWlyZCBzdHJpbmcgd2l0aCBubyBcbiAgICAgICAgICAgICAgICAgICAgLy8gcGF0aCB0aGF0IGFjdHVhbGx5IHdhbnRzIHRvIHJlZmVyZW5jZSB0aGUgcGFyZW50TmFtZSdzIHBhdGguXG4gICAgICAgICAgICAgICAgICAgIHBsdWdpbi5sb2FkKG1hcC5uYW1lLCBsb2NhbFJlcXVpcmUsIGxvYWQsIGNvbmZpZyk7XG4gICAgICAgICAgICAgICAgfSkpO1xuXG4gICAgICAgICAgICAgICAgY29udGV4dC5lbmFibGUocGx1Z2luTWFwLCB0aGlzKTtcbiAgICAgICAgICAgICAgICB0aGlzLnBsdWdpbk1hcHNbcGx1Z2luTWFwLmlkXSA9IHBsdWdpbk1hcDtcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIGVuYWJsZTogZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIGVuYWJsZWRSZWdpc3RyeVt0aGlzLm1hcC5pZF0gPSB0aGlzO1xuICAgICAgICAgICAgICAgIHRoaXMuZW5hYmxlZCA9IHRydWU7XG5cbiAgICAgICAgICAgICAgICAvLyBTZXQgZmxhZyBtZW50aW9uaW5nIHRoYXQgdGhlIG1vZHVsZSBpcyBlbmFibGluZywgc28gdGhhdCBpbW1lZGlhdGUgY2FsbHMgdG8gdGhlIGRlZmluZWQgY2FsbGJhY2tzIGZvciBcbiAgICAgICAgICAgICAgICAvLyBkZXBlbmRlbmNpZXMgZG8gbm90IHRyaWdnZXIgaW5hZHZlcnRlbnQgbG9hZCB3aXRoIHRoZSBkZXBDb3VudCBzdGlsbCBiZWluZyB6ZXJvLlxuICAgICAgICAgICAgICAgIHRoaXMuZW5hYmxpbmcgPSB0cnVlO1xuXG4gICAgICAgICAgICAgICAgLy8gRW5hYmxlIGVhY2ggZGVwZW5kZW5jeS5cbiAgICAgICAgICAgICAgICBlYWNoKHRoaXMuZGVwTWFwcywgYmluZCh0aGlzLCBmdW5jdGlvbiAoZGVwTWFwLCBpKSB7XG4gICAgICAgICAgICAgICAgICAgIGxldCBpZDtcbiAgICAgICAgICAgICAgICAgICAgbGV0IG1vZDtcbiAgICAgICAgICAgICAgICAgICAgbGV0IGhhbmRsZXI7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKHR5cGVvZiBkZXBNYXAgPT09ICdzdHJpbmcnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBEZXBlbmRlbmN5IG5lZWRzIHRvIGJlIGNvbnZlcnRlZCB0byBhIGRlcE1hcCBhbmQgd2lyZWQgdXAgdG8gdGhpcyBtb2R1bGUuXG4gICAgICAgICAgICAgICAgICAgICAgICBkZXBNYXAgPSBtYWtlTW9kdWxlTWFwKGRlcE1hcCxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAodGhpcy5tYXAuaXNEZWZpbmUgPyB0aGlzLm1hcCA6IHRoaXMubWFwLnBhcmVudE1hcCksXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZmFsc2UsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgIXRoaXMuc2tpcE1hcCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB0aGlzLmRlcE1hcHNbaV0gPSBkZXBNYXA7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGhhbmRsZXIgPSBnZXRPd24oaGFuZGxlcnMsIGRlcE1hcC5pZCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChoYW5kbGVyKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5kZXBFeHBvcnRzW2ldID0gaGFuZGxlcih0aGlzKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuZGVwQ291bnQgKz0gMTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgb24oZGVwTWFwLCAnZGVmaW5lZCcsIGJpbmQodGhpcywgZnVuY3Rpb24gKGRlcEV4cG9ydHMpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAodGhpcy51bmRlZmVkKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5kZWZpbmVEZXAoaSwgZGVwRXhwb3J0cyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5jaGVjaygpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSkpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAodGhpcy5lcnJiYWNrKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgb24oZGVwTWFwLCAnZXJyb3InLCBiaW5kKHRoaXMsIHRoaXMuZXJyYmFjaykpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIGlmICh0aGlzLmV2ZW50cy5lcnJvcikge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIE5vIGRpcmVjdCBlcnJiYWNrIG9uIHRoaXMgbW9kdWxlLCBidXQgc29tZXRoaW5nIGVsc2UgaXMgbGlzdGVuaW5nIGZvciBlcnJvcnMsIHNvIGJlIHN1cmUgXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gdG8gcHJvcGFnYXRlIHRoZSBlcnJvciBjb3JyZWN0bHkuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgb24oZGVwTWFwLCAnZXJyb3InLCBiaW5kKHRoaXMsIGZ1bmN0aW9uIChlcnIpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5lbWl0KCdlcnJvcicsIGVycik7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSkpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgaWQgPSBkZXBNYXAuaWQ7XG4gICAgICAgICAgICAgICAgICAgIG1vZCA9IHJlZ2lzdHJ5W2lkXTtcblxuICAgICAgICAgICAgICAgICAgICAvLyBTa2lwIHNwZWNpYWwgbW9kdWxlcyBsaWtlICdyZXF1aXJlJywgJ2V4cG9ydHMnLCAnbW9kdWxlJy4gQWxzbywgZG9uJ3QgY2FsbCBlbmFibGUgaWYgaXQgaXMgXG4gICAgICAgICAgICAgICAgICAgIC8vIGFscmVhZHkgZW5hYmxlZCwgaW1wb3J0YW50IGluIGNpcmN1bGFyIGRlcGVuZGVuY3kgY2FzZXMuXG4gICAgICAgICAgICAgICAgICAgIGlmICghaGFzUHJvcChoYW5kbGVycywgaWQpICYmIG1vZCAmJiAhbW9kLmVuYWJsZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnRleHQuZW5hYmxlKGRlcE1hcCwgdGhpcyk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KSk7XG5cbiAgICAgICAgICAgICAgICAvLyBFbmFibGUgZWFjaCBwbHVnaW4gdGhhdCBpcyB1c2VkIGluICBhIGRlcGVuZGVuY3kuXG4gICAgICAgICAgICAgICAgZWFjaFByb3AodGhpcy5wbHVnaW5NYXBzLCBiaW5kKHRoaXMsIGZ1bmN0aW9uIChwbHVnaW5NYXApIHtcbiAgICAgICAgICAgICAgICAgICAgbGV0IG1vZCA9IGdldE93bihyZWdpc3RyeSwgcGx1Z2luTWFwLmlkKTtcbiAgICAgICAgICAgICAgICAgICAgaWYgKG1vZCAmJiAhbW9kLmVuYWJsZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnRleHQuZW5hYmxlKHBsdWdpbk1hcCwgdGhpcyk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KSk7XG5cbiAgICAgICAgICAgICAgICB0aGlzLmVuYWJsaW5nID0gZmFsc2U7XG5cbiAgICAgICAgICAgICAgICB0aGlzLmNoZWNrKCk7XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBvbjogZnVuY3Rpb24gKG5hbWUsIGNiKSB7XG4gICAgICAgICAgICAgICAgbGV0IGNicyA9IHRoaXMuZXZlbnRzW25hbWVdO1xuICAgICAgICAgICAgICAgIGlmICghY2JzKSB7XG4gICAgICAgICAgICAgICAgICAgIGNicyA9IHRoaXMuZXZlbnRzW25hbWVdID0gW107XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGNicy5wdXNoKGNiKTtcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIGVtaXQ6IGZ1bmN0aW9uIChuYW1lLCBldnQpIHtcbiAgICAgICAgICAgICAgICBlYWNoKHRoaXMuZXZlbnRzW25hbWVdLCBmdW5jdGlvbiAoY2IpIHtcbiAgICAgICAgICAgICAgICAgICAgY2IoZXZ0KTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICBpZiAobmFtZSA9PT0gJ2Vycm9yJykge1xuICAgICAgICAgICAgICAgICAgICAvLyBOb3cgdGhhdCB0aGUgZXJyb3IgaGFuZGxlciB3YXMgdHJpZ2dlcmVkLCByZW1vdmUgdGhlIGxpc3RlbmVycywgc2luY2UgdGhpcyBicm9rZW4gTW9kdWxlIGluc3RhbmNlXG4gICAgICAgICAgICAgICAgICAgIC8vIGNhbiBzdGF5IGFyb3VuZCBmb3IgYSB3aGlsZSBpbiB0aGUgcmVnaXN0cnkuXG4gICAgICAgICAgICAgICAgICAgIGRlbGV0ZSB0aGlzLmV2ZW50c1tuYW1lXTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgZnVuY3Rpb24gY2FsbEdldE1vZHVsZShhcmdzKSB7XG4gICAgICAgICAgICAvLyBTa2lwIG1vZHVsZXMgYWxyZWFkeSBkZWZpbmVkLlxuICAgICAgICAgICAgaWYgKCFoYXNQcm9wKGRlZmluZWQsIGFyZ3NbMF0pKSB7XG4gICAgICAgICAgICAgICAgZ2V0TW9kdWxlKG1ha2VNb2R1bGVNYXAoYXJnc1swXSwgbnVsbCwgdHJ1ZSkpLmluaXQoYXJnc1sxXSwgYXJnc1syXSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBmdW5jdGlvbiByZW1vdmVMaXN0ZW5lcihub2RlLCBmdW5jLCBuYW1lLCBpZU5hbWUpIHtcbiAgICAgICAgICAgIC8vIEZhdm9yIGRldGFjaEV2ZW50IGJlY2F1c2Ugb2YgSUU5IGlzc3VlLCBzZWUgYXR0YWNoRXZlbnQvYWRkRXZlbnRMaXN0ZW5lciBjb21tZW50IGVsc2V3aGVyZSBpbiB0aGlzIGZpbGUuXG4gICAgICAgICAgICBpZiAobm9kZS5kZXRhY2hFdmVudCAmJiAhaXNPcGVyYSkge1xuICAgICAgICAgICAgICAgIC8vIFByb2JhYmx5IElFLiBJZiBub3QgaXQgd2lsbCB0aHJvdyBhbiBlcnJvciwgd2hpY2ggd2lsbCBiZSB1c2VmdWwgdG8ga25vdy5cbiAgICAgICAgICAgICAgICBpZiAoaWVOYW1lKSB7XG4gICAgICAgICAgICAgICAgICAgIG5vZGUuZGV0YWNoRXZlbnQoaWVOYW1lLCBmdW5jKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIG5vZGUucmVtb3ZlRXZlbnRMaXN0ZW5lcihuYW1lLCBmdW5jLCBmYWxzZSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICAvKipcbiAgICAgICAgICogR2l2ZW4gYW4gZXZlbnQgZnJvbSBhIHNjcmlwdCBub2RlLCBnZXQgdGhlIHJlcXVpcmVqcyBpbmZvIGZyb20gaXQsIGFuZCB0aGVuIHJlbW92ZXMgdGhlIGV2ZW50IGxpc3RlbmVycyBvblxuICAgICAgICAgKiB0aGUgbm9kZS5cbiAgICAgICAgICpcbiAgICAgICAgICogQHBhcmFtIHtFdmVudH0gZXZ0XG4gICAgICAgICAqXG4gICAgICAgICAqIEByZXR1cm4ge09iamVjdH1cbiAgICAgICAgICovXG4gICAgICAgIGZ1bmN0aW9uIGdldFNjcmlwdERhdGEoZXZ0KSB7XG4gICAgICAgICAgICAvLyBVc2luZyBjdXJyZW50VGFyZ2V0IGluc3RlYWQgb2YgdGFyZ2V0IGZvciBGaXJlZm94IDIuMCdzIHNha2UuIE5vdCBhbGwgb2xkIGJyb3dzZXJzIHdpbGwgYmUgc3VwcG9ydGVkLCBidXQgXG4gICAgICAgICAgICAvLyB0aGlzIG9uZSB3YXMgZWFzeSBlbm91Z2ggdG8gc3VwcG9ydCBhbmQgc3RpbGwgbWFrZXMgc2Vuc2UuXG4gICAgICAgICAgICBsZXQgbm9kZSA9IGV2dC5jdXJyZW50VGFyZ2V0IHx8IGV2dC5zcmNFbGVtZW50O1xuXG4gICAgICAgICAgICAvLyBSZW1vdmUgdGhlIGxpc3RlbmVycyBvbmNlIGhlcmUuXG4gICAgICAgICAgICByZW1vdmVMaXN0ZW5lcihub2RlLCBjb250ZXh0Lm9uU2NyaXB0TG9hZCwgJ2xvYWQnLCAnb25yZWFkeXN0YXRlY2hhbmdlJyk7XG4gICAgICAgICAgICByZW1vdmVMaXN0ZW5lcihub2RlLCBjb250ZXh0Lm9uU2NyaXB0RXJyb3IsICdlcnJvcicpO1xuXG4gICAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgICAgIG5vZGU6IG5vZGUsXG4gICAgICAgICAgICAgICAgaWQ6IG5vZGUgJiYgbm9kZS5nZXRBdHRyaWJ1dGUoJ2RhdGEtcmVxdWlyZW1vZHVsZScpXG4gICAgICAgICAgICB9O1xuICAgICAgICB9XG5cbiAgICAgICAgZnVuY3Rpb24gaW50YWtlRGVmaW5lcygpIHtcbiAgICAgICAgICAgIGxldCBhcmdzO1xuXG4gICAgICAgICAgICAvLyBBbnkgZGVmaW5lZCBtb2R1bGVzIGluIHRoZSBnbG9iYWwgcXVldWUsIGludGFrZSB0aGVtIG5vdy5cbiAgICAgICAgICAgIHRha2VHbG9iYWxRdWV1ZSgpO1xuXG4gICAgICAgICAgICAvLyBNYWtlIHN1cmUgYW55IHJlbWFpbmluZyBkZWZRdWV1ZSBpdGVtcyBnZXQgcHJvcGVybHkgcHJvY2Vzc2VkLlxuICAgICAgICAgICAgd2hpbGUgKGRlZlF1ZXVlLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgIGFyZ3MgPSBkZWZRdWV1ZS5zaGlmdCgpO1xuICAgICAgICAgICAgICAgIGlmIChhcmdzWzBdID09PSBudWxsKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBvbkVycm9yKG1ha2VFcnJvcignbWlzbWF0Y2gnLCAnTWlzbWF0Y2hlZCBhbm9ueW1vdXMgZGVmaW5lKCkgbW9kdWxlOiAnICtcbiAgICAgICAgICAgICAgICAgICAgICAgIGFyZ3NbYXJncy5sZW5ndGggLSAxXSkpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIC8vIGFyZ3MgYXJlIGlkLCBkZXBzLCBmYWN0b3J5LiBTaG91bGQgYmUgbm9ybWFsaXplZCBieSB0aGVcbiAgICAgICAgICAgICAgICAgICAgLy8gZGVmaW5lKCkgZnVuY3Rpb24uXG4gICAgICAgICAgICAgICAgICAgIGNhbGxHZXRNb2R1bGUoYXJncyk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgY29udGV4dC5kZWZRdWV1ZU1hcCA9IHt9O1xuICAgICAgICB9XG5cbiAgICAgICAgY29udGV4dCA9IHtcbiAgICAgICAgICAgIGNvbmZpZzogY29uZmlnLFxuICAgICAgICAgICAgY29udGV4dE5hbWU6IGNvbnRleHROYW1lLFxuICAgICAgICAgICAgcmVnaXN0cnk6IHJlZ2lzdHJ5LFxuICAgICAgICAgICAgZGVmaW5lZDogZGVmaW5lZCxcbiAgICAgICAgICAgIHVybEZldGNoZWQ6IHVybEZldGNoZWQsXG4gICAgICAgICAgICBkZWZRdWV1ZTogZGVmUXVldWUsXG4gICAgICAgICAgICBkZWZRdWV1ZU1hcDoge30sXG4gICAgICAgICAgICBNb2R1bGU6IE1vZHVsZSxcbiAgICAgICAgICAgIG1ha2VNb2R1bGVNYXA6IG1ha2VNb2R1bGVNYXAsXG4gICAgICAgICAgICBuZXh0VGljazogcmVxLm5leHRUaWNrLFxuICAgICAgICAgICAgb25FcnJvcjogb25FcnJvcixcblxuICAgICAgICAgICAgLyoqXG4gICAgICAgICAgICAgKiBTZXQgYSBjb25maWd1cmF0aW9uIGZvciB0aGUgY29udGV4dC5cbiAgICAgICAgICAgICAqXG4gICAgICAgICAgICAgKiBAcGFyYW0ge09iamVjdH0gY2ZnIGNvbmZpZyBvYmplY3QgdG8gaW50ZWdyYXRlLlxuICAgICAgICAgICAgICovXG4gICAgICAgICAgICBjb25maWd1cmU6IGZ1bmN0aW9uIChjZmcpIHtcbiAgICAgICAgICAgICAgICAvLyBNYWtlIHN1cmUgdGhlIGJhc2VVcmwgZW5kcyBpbiBhIHNsYXNoLlxuICAgICAgICAgICAgICAgIGlmIChjZmcuYmFzZVVybCkge1xuICAgICAgICAgICAgICAgICAgICBpZiAoY2ZnLmJhc2VVcmwuY2hhckF0KGNmZy5iYXNlVXJsLmxlbmd0aCAtIDEpICE9PSAnLycpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNmZy5iYXNlVXJsICs9ICcvJztcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIC8vIFNhdmUgb2ZmIHRoZSBwYXRocyBzaW5jZSB0aGV5IHJlcXVpcmUgc3BlY2lhbCBwcm9jZXNzaW5nLCB0aGV5IGFyZSBhZGRpdGl2ZS5cbiAgICAgICAgICAgICAgICBsZXQgc2hpbSA9IGNvbmZpZy5zaGltLFxuICAgICAgICAgICAgICAgICAgICBvYmpzID0ge1xuICAgICAgICAgICAgICAgICAgICAgICAgcGF0aHM6IHRydWUsXG4gICAgICAgICAgICAgICAgICAgICAgICBidW5kbGVzOiB0cnVlLFxuICAgICAgICAgICAgICAgICAgICAgICAgY29uZmlnOiB0cnVlLFxuICAgICAgICAgICAgICAgICAgICAgICAgbWFwOiB0cnVlXG4gICAgICAgICAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgICAgICBlYWNoUHJvcChjZmcsIGZ1bmN0aW9uICh2YWx1ZSwgcHJvcCkge1xuICAgICAgICAgICAgICAgICAgICBpZiAob2Jqc1twcm9wXSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCFjb25maWdbcHJvcF0pIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb25maWdbcHJvcF0gPSB7fTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIG1peGluKGNvbmZpZ1twcm9wXSwgdmFsdWUsIHRydWUsIHRydWUpO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgY29uZmlnW3Byb3BdID0gdmFsdWU7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIC8vIFJldmVyc2UgbWFwIHRoZSBidW5kbGVzXG4gICAgICAgICAgICAgICAgaWYgKGNmZy5idW5kbGVzKSB7XG4gICAgICAgICAgICAgICAgICAgIGVhY2hQcm9wKGNmZy5idW5kbGVzLCBmdW5jdGlvbiAodmFsdWUsIHByb3ApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGVhY2godmFsdWUsIGZ1bmN0aW9uICh2KSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHYgIT09IHByb3ApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgYnVuZGxlc01hcFt2XSA9IHByb3A7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIC8vIE1lcmdlIHNoaW1cbiAgICAgICAgICAgICAgICBpZiAoY2ZnLnNoaW0pIHtcbiAgICAgICAgICAgICAgICAgICAgZWFjaFByb3AoY2ZnLnNoaW0sIGZ1bmN0aW9uICh2YWx1ZSwgaWQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIE5vcm1hbGl6ZSB0aGUgc3RydWN0dXJlXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoaXNBcnJheSh2YWx1ZSkpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YWx1ZSA9IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZGVwczogdmFsdWVcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCh2YWx1ZS5leHBvcnRzIHx8IHZhbHVlLmluaXQpICYmICF2YWx1ZS5leHBvcnRzRm4pIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YWx1ZS5leHBvcnRzRm4gPSBjb250ZXh0Lm1ha2VTaGltRXhwb3J0cyh2YWx1ZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICBzaGltW2lkXSA9IHZhbHVlO1xuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgY29uZmlnLnNoaW0gPSBzaGltO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIC8vIEFkanVzdCBwYWNrYWdlcyBpZiBuZWNlc3NhcnkuXG4gICAgICAgICAgICAgICAgaWYgKGNmZy5wYWNrYWdlcykge1xuICAgICAgICAgICAgICAgICAgICBlYWNoKGNmZy5wYWNrYWdlcywgZnVuY3Rpb24gKHBrZ09iaikge1xuICAgICAgICAgICAgICAgICAgICAgICAgbGV0IGxvY2F0aW9uLCBuYW1lO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBwa2dPYmogPSB0eXBlb2YgcGtnT2JqID09PSAnc3RyaW5nJyA/IHtuYW1lOiBwa2dPYmp9IDogcGtnT2JqO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBuYW1lID0gcGtnT2JqLm5hbWU7XG4gICAgICAgICAgICAgICAgICAgICAgICBsb2NhdGlvbiA9IHBrZ09iai5sb2NhdGlvbjtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChsb2NhdGlvbikge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNvbmZpZy5wYXRoc1tuYW1lXSA9IHBrZ09iai5sb2NhdGlvbjtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gU2F2ZSBwb2ludGVyIHRvIG1haW4gbW9kdWxlIElEIGZvciBwa2cgbmFtZS4gUmVtb3ZlIGxlYWRpbmcgZG90IGluIG1haW4sIHNvIG1haW4gcGF0aHMgYXJlIFxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gbm9ybWFsaXplZCwgYW5kIHJlbW92ZSBhbnkgdHJhaWxpbmcgLmpzLCBzaW5jZSBkaWZmZXJlbnQgcGFja2FnZSBlbnZzIGhhdmUgZGlmZmVyZW50IFxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gY29udmVudGlvbnM6IHNvbWUgdXNlIGEgbW9kdWxlIG5hbWUsIHNvbWUgdXNlIGEgZmlsZSBuYW1lLlxuICAgICAgICAgICAgICAgICAgICAgICAgY29uZmlnLnBrZ3NbbmFtZV0gPSBwa2dPYmoubmFtZSArICcvJyArIChwa2dPYmoubWFpbiB8fCAnbWFpbicpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLnJlcGxhY2UoY3VyckRpclJlZ0V4cCwgJycpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLnJlcGxhY2UoanNTdWZmaXhSZWdFeHAsICcnKTtcbiAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgLy8gSWYgdGhlcmUgYXJlIGFueSBcIndhaXRpbmcgdG8gZXhlY3V0ZVwiIG1vZHVsZXMgaW4gdGhlIHJlZ2lzdHJ5LCB1cGRhdGUgdGhlIG1hcHMgZm9yIHRoZW0sIHNpbmNlIHRoZWlyIFxuICAgICAgICAgICAgICAgIC8vIGluZm8sIGxpa2UgVVJMcyB0byBsb2FkLCBtYXkgaGF2ZSBjaGFuZ2VkLlxuICAgICAgICAgICAgICAgIGVhY2hQcm9wKHJlZ2lzdHJ5LCBmdW5jdGlvbiAobW9kLCBpZCkge1xuICAgICAgICAgICAgICAgICAgICAvLyBJZiBtb2R1bGUgYWxyZWFkeSBoYXMgaW5pdCBjYWxsZWQsIHNpbmNlIGl0IGlzIHRvbyBsYXRlIHRvIG1vZGlmeSB0aGVtLCBhbmQgaWdub3JlIHVubm9ybWFsaXplZCBcbiAgICAgICAgICAgICAgICAgICAgLy8gb25lcyBzaW5jZSB0aGV5IGFyZSB0cmFuc2llbnQuXG4gICAgICAgICAgICAgICAgICAgIGlmICghbW9kLmluaXRlZCAmJiAhbW9kLm1hcC51bm5vcm1hbGl6ZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIG1vZC5tYXAgPSBtYWtlTW9kdWxlTWFwKGlkLCBudWxsLCB0cnVlKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgLy8gSWYgYSBkZXBzIGFycmF5IG9yIGEgY29uZmlnIGNhbGxiYWNrIGlzIHNwZWNpZmllZCwgdGhlbiBjYWxsIHJlcXVpcmUgd2l0aCB0aG9zZSBhcmdzLiBUaGlzIGlzIHVzZWZ1bCBcbiAgICAgICAgICAgICAgICAvLyB3aGVuIHJlcXVpcmUgaXMgZGVmaW5lZCBhcyBhIGNvbmZpZyBvYmplY3QgYmVmb3JlIHJlcXVpcmUuanMgaXMgbG9hZGVkLlxuICAgICAgICAgICAgICAgIGlmIChjZmcuZGVwcyB8fCBjZmcuY2FsbGJhY2spIHtcbiAgICAgICAgICAgICAgICAgICAgY29udGV4dC5yZXF1aXJlKGNmZy5kZXBzIHx8IFtdLCBjZmcuY2FsbGJhY2spO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIG1ha2VTaGltRXhwb3J0czogZnVuY3Rpb24gKHZhbHVlKSB7XG4gICAgICAgICAgICAgICAgZnVuY3Rpb24gZm4oKSB7XG4gICAgICAgICAgICAgICAgICAgIGxldCByZXQ7XG4gICAgICAgICAgICAgICAgICAgIGlmICh2YWx1ZS5pbml0KSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXQgPSB2YWx1ZS5pbml0LmFwcGx5KGdsb2JhbCwgYXJndW1lbnRzKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gcmV0IHx8ICh2YWx1ZS5leHBvcnRzICYmIGdldEdsb2JhbCh2YWx1ZS5leHBvcnRzKSk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgcmV0dXJuIGZuO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgbWFrZVJlcXVpcmU6IGZ1bmN0aW9uIChyZWxNYXAsIG9wdGlvbnMpIHtcbiAgICAgICAgICAgICAgICBvcHRpb25zID0gb3B0aW9ucyB8fCB7fTtcblxuICAgICAgICAgICAgICAgIGZ1bmN0aW9uIGxvY2FsUmVxdWlyZShkZXBzLCBjYWxsYmFjaywgZXJyYmFjaykge1xuICAgICAgICAgICAgICAgICAgICBsZXQgaWQsIG1hcCwgcmVxdWlyZU1vZDtcblxuICAgICAgICAgICAgICAgICAgICBpZiAob3B0aW9ucy5lbmFibGVCdWlsZENhbGxiYWNrICYmIGNhbGxiYWNrICYmIGlzRnVuY3Rpb24oY2FsbGJhY2spKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjYWxsYmFjay5fX3JlcXVpcmVKc0J1aWxkID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIGlmICh0eXBlb2YgZGVwcyA9PT0gJ3N0cmluZycpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChpc0Z1bmN0aW9uKGNhbGxiYWNrKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIEludmFsaWQgY2FsbFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBvbkVycm9yKG1ha2VFcnJvcigncmVxdWlyZWFyZ3MnLCAnSW52YWxpZCByZXF1aXJlIGNhbGwnKSwgZXJyYmFjayk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIElmIHJlcXVpcmV8ZXhwb3J0c3xtb2R1bGUgYXJlIHJlcXVlc3RlZCwgZ2V0IHRoZSB2YWx1ZSBmb3IgdGhlbSBmcm9tIHRoZSBzcGVjaWFsIGhhbmRsZXJzLiBcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIENhdmVhdDogdGhpcyBvbmx5IHdvcmtzIHdoaWxlIG1vZHVsZSBpcyBiZWluZyBkZWZpbmVkLlxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHJlbE1hcCAmJiBoYXNQcm9wKGhhbmRsZXJzLCBkZXBzKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBoYW5kbGVyc1tkZXBzXShyZWdpc3RyeVtyZWxNYXAuaWRdKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gU3luY2hyb25vdXMgYWNjZXNzIHRvIG9uZSBtb2R1bGUuIElmIHJlcXVpcmUuZ2V0IGlzIGF2YWlsYWJsZSAoYXMgaW4gdGhlIE5vZGUgYWRhcHRlciksIFxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gcHJlZmVyIHRoYXQuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAocmVxLmdldCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiByZXEuZ2V0KGNvbnRleHQsIGRlcHMsIHJlbE1hcCwgbG9jYWxSZXF1aXJlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gTm9ybWFsaXplIG1vZHVsZSBuYW1lLCBpZiBpdCBjb250YWlucyAuIG9yIC4uXG4gICAgICAgICAgICAgICAgICAgICAgICBtYXAgPSBtYWtlTW9kdWxlTWFwKGRlcHMsIHJlbE1hcCwgZmFsc2UsIHRydWUpO1xuICAgICAgICAgICAgICAgICAgICAgICAgaWQgPSBtYXAuaWQ7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmICghaGFzUHJvcChkZWZpbmVkLCBpZCkpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gb25FcnJvcihtYWtlRXJyb3IoJ25vdGxvYWRlZCcsICdNb2R1bGUgbmFtZSBcIicgK1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZCArXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdcIiBoYXMgbm90IGJlZW4gbG9hZGVkIHlldCBmb3IgY29udGV4dDogJyArXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNvbnRleHROYW1lICtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgKHJlbE1hcCA/ICcnIDogJy4gVXNlIHJlcXVpcmUoW10pJykpKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBkZWZpbmVkW2lkXTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIC8vIEdyYWIgZGVmaW5lcyB3YWl0aW5nIGluIHRoZSBnbG9iYWwgcXVldWUuXG4gICAgICAgICAgICAgICAgICAgIGludGFrZURlZmluZXMoKTtcblxuICAgICAgICAgICAgICAgICAgICAvLyBNYXJrIGFsbCB0aGUgZGVwZW5kZW5jaWVzIGFzIG5lZWRpbmcgdG8gYmUgbG9hZGVkLlxuICAgICAgICAgICAgICAgICAgICBjb250ZXh0Lm5leHRUaWNrKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIFNvbWUgZGVmaW5lcyBjb3VsZCBoYXZlIGJlZW4gYWRkZWQgc2luY2UgdGhlXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyByZXF1aXJlIGNhbGwsIGNvbGxlY3QgdGhlbS5cbiAgICAgICAgICAgICAgICAgICAgICAgIGludGFrZURlZmluZXMoKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgcmVxdWlyZU1vZCA9IGdldE1vZHVsZShtYWtlTW9kdWxlTWFwKG51bGwsIHJlbE1hcCkpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBTdG9yZSBpZiBtYXAgY29uZmlnIHNob3VsZCBiZSBhcHBsaWVkIHRvIHRoaXMgcmVxdWlyZVxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gY2FsbCBmb3IgZGVwZW5kZW5jaWVzLlxuICAgICAgICAgICAgICAgICAgICAgICAgcmVxdWlyZU1vZC5za2lwTWFwID0gb3B0aW9ucy5za2lwTWFwO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICByZXF1aXJlTW9kLmluaXQoZGVwcywgY2FsbGJhY2ssIGVycmJhY2ssIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBlbmFibGVkOiB0cnVlXG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgY2hlY2tMb2FkZWQoKTtcbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGxvY2FsUmVxdWlyZTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBtaXhpbihsb2NhbFJlcXVpcmUsIHtcbiAgICAgICAgICAgICAgICAgICAgaXNCcm93c2VyOiBpc0Jyb3dzZXIsXG5cbiAgICAgICAgICAgICAgICAgICAgLyoqXG4gICAgICAgICAgICAgICAgICAgICAqIENvbnZlcnRzIGEgbW9kdWxlIG5hbWUgKyAuZXh0ZW5zaW9uIGludG8gYW4gVVJMIHBhdGguXG4gICAgICAgICAgICAgICAgICAgICAqXG4gICAgICAgICAgICAgICAgICAgICAqICpSZXF1aXJlcyogdGhlIHVzZSBvZiBhIG1vZHVsZSBuYW1lLiBJdCBkb2VzIG5vdCBzdXBwb3J0IHVzaW5nIHBsYWluIFVSTHMgbGlrZSBuYW1lVG9VcmwuXG4gICAgICAgICAgICAgICAgICAgICAqL1xuICAgICAgICAgICAgICAgICAgICB0b1VybDogZnVuY3Rpb24gKG1vZHVsZU5hbWVQbHVzRXh0KSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBsZXQgZXh0LFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGluZGV4ID0gbW9kdWxlTmFtZVBsdXNFeHQubGFzdEluZGV4T2YoJy4nKSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzZWdtZW50ID0gbW9kdWxlTmFtZVBsdXNFeHQuc3BsaXQoJy8nKVswXSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpc1JlbGF0aXZlID0gc2VnbWVudCA9PT0gJy4nIHx8IHNlZ21lbnQgPT09ICcuLic7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIEhhdmUgYSBmaWxlIGV4dGVuc2lvbiBhbGlhcywgYW5kIGl0IGlzIG5vdCB0aGVcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIGRvdHMgZnJvbSBhIHJlbGF0aXZlIHBhdGguXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoaW5kZXggIT09IC0xICYmICghaXNSZWxhdGl2ZSB8fCBpbmRleCA+IDEpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZXh0ID0gbW9kdWxlTmFtZVBsdXNFeHQuc3Vic3RyaW5nKGluZGV4LCBtb2R1bGVOYW1lUGx1c0V4dC5sZW5ndGgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG1vZHVsZU5hbWVQbHVzRXh0ID0gbW9kdWxlTmFtZVBsdXNFeHQuc3Vic3RyaW5nKDAsIGluZGV4KTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGNvbnRleHQubmFtZVRvVXJsKG5vcm1hbGl6ZShtb2R1bGVOYW1lUGx1c0V4dCxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZWxNYXAgJiYgcmVsTWFwLmlkLCB0cnVlKSwgZXh0LCB0cnVlKTtcbiAgICAgICAgICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgICAgICAgICBkZWZpbmVkOiBmdW5jdGlvbiAoaWQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBoYXNQcm9wKGRlZmluZWQsIG1ha2VNb2R1bGVNYXAoaWQsIHJlbE1hcCwgZmFsc2UsIHRydWUpLmlkKTtcbiAgICAgICAgICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgICAgICAgICBzcGVjaWZpZWQ6IGZ1bmN0aW9uIChpZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgaWQgPSBtYWtlTW9kdWxlTWFwKGlkLCByZWxNYXAsIGZhbHNlLCB0cnVlKS5pZDtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBoYXNQcm9wKGRlZmluZWQsIGlkKSB8fCBoYXNQcm9wKHJlZ2lzdHJ5LCBpZCk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIC8vIE9ubHkgYWxsb3cgdW5kZWYgb24gdG9wIGxldmVsIHJlcXVpcmUgY2FsbHMuXG4gICAgICAgICAgICAgICAgaWYgKCFyZWxNYXApIHtcbiAgICAgICAgICAgICAgICAgICAgbG9jYWxSZXF1aXJlLnVuZGVmID0gZnVuY3Rpb24gKGlkKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBCaW5kIGFueSB3YWl0aW5nIGRlZmluZSgpIGNhbGxzIHRvIHRoaXMgY29udGV4dCwgZml4IGZvciAjNDA4LlxuICAgICAgICAgICAgICAgICAgICAgICAgdGFrZUdsb2JhbFF1ZXVlKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGxldCBtYXAgPSBtYWtlTW9kdWxlTWFwKGlkLCByZWxNYXAsIHRydWUpO1xuICAgICAgICAgICAgICAgICAgICAgICAgbGV0IG1vZCA9IGdldE93bihyZWdpc3RyeSwgaWQpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBtb2QudW5kZWZlZCA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgICAgICByZW1vdmVTY3JpcHQoaWQpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBkZWxldGUgZGVmaW5lZFtpZF07XG4gICAgICAgICAgICAgICAgICAgICAgICBkZWxldGUgdXJsRmV0Y2hlZFttYXAudXJsXTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGRlbGV0ZSB1bmRlZkV2ZW50c1tpZF07XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIENsZWFuIHF1ZXVlZCBkZWZpbmVzIHRvby4gR28gYmFja3dhcmRzIGluIGFycmF5IHNvIHRoYXQgdGhlIHNwbGljZXMgZG8gbm90IG1lc3MgdXAgdGhlIFxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gaXRlcmF0aW9uLlxuICAgICAgICAgICAgICAgICAgICAgICAgZWFjaFJldmVyc2UoZGVmUXVldWUsIGZ1bmN0aW9uIChhcmdzLCBpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGFyZ3NbMF0gPT09IGlkKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRlZlF1ZXVlLnNwbGljZShpLCAxKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGRlbGV0ZSBjb250ZXh0LmRlZlF1ZXVlTWFwW2lkXTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKG1vZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIEhvbGQgb24gdG8gbGlzdGVuZXJzIGluIGNhc2UgdGhlIG1vZHVsZSB3aWxsIGJlIGF0dGVtcHRlZCB0byBiZSByZWxvYWRlZCB1c2luZyBhIFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIGRpZmZlcmVudCBjb25maWcuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKG1vZC5ldmVudHMuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB1bmRlZkV2ZW50c1tpZF0gPSBtb2QuZXZlbnRzO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNsZWFuUmVnaXN0cnkoaWQpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHJldHVybiBsb2NhbFJlcXVpcmU7XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICAvKipcbiAgICAgICAgICAgICAqIENhbGxlZCB0byBlbmFibGUgYSBtb2R1bGUgaWYgaXQgaXMgc3RpbGwgaW4gdGhlIHJlZ2lzdHJ5IGF3YWl0aW5nIGVuYWJsZW1lbnQuIEEgc2Vjb25kIGFyZywgcGFyZW50LCB0aGVcbiAgICAgICAgICAgICAqIHBhcmVudCBtb2R1bGUsIGlzIHBhc3NlZCBpbiBmb3IgY29udGV4dCwgd2hlbiB0aGlzIG1ldGhvZCBpcyBvdmVycmlkZGVuIGJ5IHRoZSBvcHRpbWl6ZXIuIE5vdCBzaG93biBoZXJlXG4gICAgICAgICAgICAgKiB0byBrZWVwIGNvZGUgY29tcGFjdC5cbiAgICAgICAgICAgICAqL1xuICAgICAgICAgICAgZW5hYmxlOiBmdW5jdGlvbiAoZGVwTWFwKSB7XG4gICAgICAgICAgICAgICAgbGV0IG1vZCA9IGdldE93bihyZWdpc3RyeSwgZGVwTWFwLmlkKTtcbiAgICAgICAgICAgICAgICBpZiAobW9kKSB7XG4gICAgICAgICAgICAgICAgICAgIGdldE1vZHVsZShkZXBNYXApLmVuYWJsZSgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIC8qKlxuICAgICAgICAgICAgICogSW50ZXJuYWwgbWV0aG9kIHVzZWQgYnkgZW52aXJvbm1lbnQgYWRhcHRlcnMgdG8gY29tcGxldGUgYSBsb2FkIGV2ZW50LlxuICAgICAgICAgICAgICpcbiAgICAgICAgICAgICAqIEEgbG9hZCBldmVudCBjb3VsZCBiZSBhIHNjcmlwdCBsb2FkIG9yIGp1c3QgYSBsb2FkIHBhc3MgZnJvbSBhIHN5bmNocm9ub3VzIGxvYWQgY2FsbC5cbiAgICAgICAgICAgICAqXG4gICAgICAgICAgICAgKiBAcGFyYW0ge1N0cmluZ30gbW9kdWxlTmFtZSBUaGUgbmFtZSBvZiB0aGUgbW9kdWxlIHRvIHBvdGVudGlhbGx5IGNvbXBsZXRlLlxuICAgICAgICAgICAgICovXG4gICAgICAgICAgICBjb21wbGV0ZUxvYWQ6IGZ1bmN0aW9uIChtb2R1bGVOYW1lKSB7XG4gICAgICAgICAgICAgICAgbGV0IGZvdW5kO1xuICAgICAgICAgICAgICAgIGxldCBhcmdzO1xuICAgICAgICAgICAgICAgIGxldCBtb2Q7XG4gICAgICAgICAgICAgICAgbGV0IHNoaW0gPSBnZXRPd24oY29uZmlnLnNoaW0sIG1vZHVsZU5hbWUpIHx8IHt9O1xuICAgICAgICAgICAgICAgIGxldCBzaEV4cG9ydHMgPSBzaGltLmV4cG9ydHM7XG5cbiAgICAgICAgICAgICAgICB0YWtlR2xvYmFsUXVldWUoKTtcblxuICAgICAgICAgICAgICAgIHdoaWxlIChkZWZRdWV1ZS5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICAgICAgYXJncyA9IGRlZlF1ZXVlLnNoaWZ0KCk7XG4gICAgICAgICAgICAgICAgICAgIGlmIChhcmdzWzBdID09PSBudWxsKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBhcmdzWzBdID0gbW9kdWxlTmFtZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIElmIGFscmVhZHkgZm91bmQgYW4gYW5vbnltb3VzIG1vZHVsZSBhbmQgYm91bmQgaXQgdG8gdGhpcyBuYW1lLCB0aGVuIHRoaXMgaXMgc29tZSBvdGhlciBhbm9uIFxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gbW9kdWxlIHdhaXRpbmcgZm9yIGl0cyBjb21wbGV0ZUxvYWQgdG8gZmlyZS5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChmb3VuZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgZm91bmQgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKGFyZ3NbMF0gPT09IG1vZHVsZU5hbWUpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIEZvdW5kIG1hdGNoaW5nIGRlZmluZSBjYWxsIGZvciB0aGlzIHNjcmlwdCFcbiAgICAgICAgICAgICAgICAgICAgICAgIGZvdW5kID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIGNhbGxHZXRNb2R1bGUoYXJncyk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGNvbnRleHQuZGVmUXVldWVNYXAgPSB7fTtcblxuICAgICAgICAgICAgICAgIC8vIERvIHRoaXMgYWZ0ZXIgdGhlIGN5Y2xlIG9mIGNhbGxHZXRNb2R1bGUgaW4gY2FzZSB0aGUgcmVzdWx0XG4gICAgICAgICAgICAgICAgLy8gb2YgdGhvc2UgY2FsbHMvaW5pdCBjYWxscyBjaGFuZ2VzIHRoZSByZWdpc3RyeS5cbiAgICAgICAgICAgICAgICBtb2QgPSBnZXRPd24ocmVnaXN0cnksIG1vZHVsZU5hbWUpO1xuXG4gICAgICAgICAgICAgICAgaWYgKCFmb3VuZCAmJiAhaGFzUHJvcChkZWZpbmVkLCBtb2R1bGVOYW1lKSAmJiBtb2QgJiYgIW1vZC5pbml0ZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKGNvbmZpZy5lbmZvcmNlRGVmaW5lICYmICghc2hFeHBvcnRzIHx8ICFnZXRHbG9iYWwoc2hFeHBvcnRzKSkpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChoYXNQYXRoRmFsbGJhY2sobW9kdWxlTmFtZSkpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBvbkVycm9yKG1ha2VFcnJvcignbm9kZWZpbmUnLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnTm8gZGVmaW5lIGNhbGwgZm9yICcgKyBtb2R1bGVOYW1lLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBudWxsLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBbbW9kdWxlTmFtZV0pKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIEEgc2NyaXB0IHRoYXQgZG9lcyBub3QgY2FsbCBkZWZpbmUoKSwgc28ganVzdCBzaW11bGF0ZSB0aGUgY2FsbCBmb3IgaXQuXG4gICAgICAgICAgICAgICAgICAgICAgICBjYWxsR2V0TW9kdWxlKFttb2R1bGVOYW1lLCAoc2hpbS5kZXBzIHx8IFtdKSwgc2hpbS5leHBvcnRzRm5dKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGNoZWNrTG9hZGVkKCk7XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICAvKipcbiAgICAgICAgICAgICAqIENvbnZlcnRzIGEgbW9kdWxlIG5hbWUgdG8gYSBmaWxlIHBhdGguXG4gICAgICAgICAgICAgKlxuICAgICAgICAgICAgICogU3VwcG9ydHMgY2FzZXMgd2hlcmUgbW9kdWxlTmFtZSBtYXkgYWN0dWFsbHkgYmUganVzdCBhbiBVUkwuXG4gICAgICAgICAgICAgKlxuICAgICAgICAgICAgICogTm90ZSB0aGF0IGl0ICoqZG9lcyBub3QqKiBjYWxsIG5vcm1hbGl6ZSBvbiB0aGUgbW9kdWxlTmFtZSwgaXQgaXMgYXNzdW1lZCB0byBoYXZlIGFscmVhZHkgYmVlblxuICAgICAgICAgICAgICogbm9ybWFsaXplZC4gVGhpcyBpcyBhbiBpbnRlcm5hbCBBUEksIG5vdCBhIHB1YmxpYyBvbmUuIFVzZSB0b1VybCBmb3IgdGhlIHB1YmxpYyBBUEkuXG4gICAgICAgICAgICAgKi9cbiAgICAgICAgICAgIG5hbWVUb1VybDogZnVuY3Rpb24gKG1vZHVsZU5hbWUsIGV4dCwgc2tpcEV4dCkge1xuICAgICAgICAgICAgICAgIGxldCBwYXRocztcbiAgICAgICAgICAgICAgICBsZXQgc3ltcztcbiAgICAgICAgICAgICAgICBsZXQgaTtcbiAgICAgICAgICAgICAgICBsZXQgcGFyZW50TW9kdWxlO1xuICAgICAgICAgICAgICAgIGxldCB1cmw7XG4gICAgICAgICAgICAgICAgbGV0IHBhcmVudFBhdGgsIGJ1bmRsZUlkO1xuICAgICAgICAgICAgICAgIGxldCBwa2dNYWluID0gZ2V0T3duKGNvbmZpZy5wa2dzLCBtb2R1bGVOYW1lKTtcblxuICAgICAgICAgICAgICAgIGlmIChwa2dNYWluKSB7XG4gICAgICAgICAgICAgICAgICAgIG1vZHVsZU5hbWUgPSBwa2dNYWluO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGJ1bmRsZUlkID0gZ2V0T3duKGJ1bmRsZXNNYXAsIG1vZHVsZU5hbWUpO1xuXG4gICAgICAgICAgICAgICAgaWYgKGJ1bmRsZUlkKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBjb250ZXh0Lm5hbWVUb1VybChidW5kbGVJZCwgZXh0LCBza2lwRXh0KTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAvLyBJZiBhIGNvbG9uIGlzIGluIHRoZSBVUkwsIGl0IGluZGljYXRlcyBhIHByb3RvY29sIGlzIHVzZWQgYW5kIGl0IGlzIGp1c3QgYW4gVVJMIHRvIGEgZmlsZSwgb3IgaWYgaXQgXG4gICAgICAgICAgICAgICAgLy8gc3RhcnRzIHdpdGggYSBzbGFzaCwgY29udGFpbnMgYSBxdWVyeSBhcmcgKGkuZS4gPykgb3IgZW5kcyB3aXRoIC5qcywgdGhlbiBhc3N1bWUgdGhlIHVzZXIgbWVhbnQgdG8gXG4gICAgICAgICAgICAgICAgLy8gdXNlIGFuIHVybCBhbmQgbm90IGEgbW9kdWxlIGlkLiBUaGUgc2xhc2ggaXMgaW1wb3J0YW50IGZvciBwcm90b2NvbC1sZXNzIFVSTHMgYXMgd2VsbCBhcyBmdWxsIHBhdGhzLlxuICAgICAgICAgICAgICAgIGlmIChyZXEuanNFeHRSZWdFeHAudGVzdChtb2R1bGVOYW1lKSkge1xuICAgICAgICAgICAgICAgICAgICAvLyBKdXN0IGEgcGxhaW4gcGF0aCwgbm90IG1vZHVsZSBuYW1lIGxvb2t1cCwgc28ganVzdCByZXR1cm4gaXQuIEFkZCBleHRlbnNpb24gaWYgaXQgaXMgaW5jbHVkZWQuIFxuICAgICAgICAgICAgICAgICAgICAvLyBUaGlzIGlzIGEgYml0IHdvbmt5LCBvbmx5IG5vbi0uanMgdGhpbmdzIHBhc3MgYW4gZXh0ZW5zaW9uLCB0aGlzIG1ldGhvZCBwcm9iYWJseSBuZWVkcyB0byBiZSBcbiAgICAgICAgICAgICAgICAgICAgLy8gcmV3b3JrZWQuXG4gICAgICAgICAgICAgICAgICAgIHVybCA9IG1vZHVsZU5hbWUgKyAoZXh0IHx8ICcnKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAvLyBBIG1vZHVsZSB0aGF0IG5lZWRzIHRvIGJlIGNvbnZlcnRlZCB0byBhIHBhdGguXG4gICAgICAgICAgICAgICAgICAgIHBhdGhzID0gY29uZmlnLnBhdGhzO1xuXG4gICAgICAgICAgICAgICAgICAgIHN5bXMgPSBtb2R1bGVOYW1lLnNwbGl0KCcvJyk7XG4gICAgICAgICAgICAgICAgICAgIC8vIEZvciBlYWNoIG1vZHVsZSBuYW1lIHNlZ21lbnQsIHNlZSBpZiB0aGVyZSBpcyBhIHBhdGggcmVnaXN0ZXJlZCBmb3IgaXQuIFN0YXJ0IHdpdGggbW9zdCBzcGVjaWZpYyBcbiAgICAgICAgICAgICAgICAgICAgLy8gbmFtZSBhbmQgd29yayB1cCBmcm9tIGl0LlxuICAgICAgICAgICAgICAgICAgICBmb3IgKGkgPSBzeW1zLmxlbmd0aDsgaSA+IDA7IGkgLT0gMSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgcGFyZW50TW9kdWxlID0gc3ltcy5zbGljZSgwLCBpKS5qb2luKCcvJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHBhcmVudFBhdGggPSBnZXRPd24ocGF0aHMsIHBhcmVudE1vZHVsZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAocGFyZW50UGF0aCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIElmIGFuIGFycmF5LCBpdCBtZWFucyB0aGVyZSBhcmUgYSBmZXcgY2hvaWNlcywgQ2hvb3NlIHRoZSBvbmUgdGhhdCBpcyBkZXNpcmVkLlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmIChpc0FycmF5KHBhcmVudFBhdGgpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHBhcmVudFBhdGggPSBwYXJlbnRQYXRoWzBdO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzeW1zLnNwbGljZSgwLCBpLCBwYXJlbnRQYXRoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIC8vIEpvaW4gdGhlIHBhdGggcGFydHMgdG9nZXRoZXIsIHRoZW4gZmlndXJlIG91dCBpZiBiYXNlVXJsIGlzIG5lZWRlZC5cbiAgICAgICAgICAgICAgICAgICAgdXJsID0gc3ltcy5qb2luKCcvJyk7XG4gICAgICAgICAgICAgICAgICAgIHVybCArPSAoZXh0IHx8ICgvXmRhdGFcXDp8XFw/Ly50ZXN0KHVybCkgfHwgc2tpcEV4dCA/ICcnIDogJy5qcycpKTtcbiAgICAgICAgICAgICAgICAgICAgdXJsID0gKHVybC5jaGFyQXQoMCkgPT09ICcvJyB8fCB1cmwubWF0Y2goL15bXFx3XFwrXFwuXFwtXSs6LykgPyAnJyA6IGNvbmZpZy5iYXNlVXJsKSArIHVybDtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICByZXR1cm4gY29uZmlnLnVybEFyZ3MgPyB1cmwgK1xuICAgICAgICAgICAgICAgICAgICAoKHVybC5pbmRleE9mKCc/JykgPT09IC0xID8gJz8nIDogJyYnKSArXG4gICAgICAgICAgICAgICAgICAgICAgICBjb25maWcudXJsQXJncykgOiB1cmw7XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICAvLyBEZWxlZ2F0ZXMgdG8gcmVxLmxvYWQuIEJyb2tlbiBvdXQgYXMgYSBzZXBhcmF0ZSBmdW5jdGlvbiB0byBhbGxvdyBvdmVycmlkaW5nIGluIHRoZSBvcHRpbWl6ZXIuXG4gICAgICAgICAgICBsb2FkOiBmdW5jdGlvbiAoaWQsIHVybCkge1xuICAgICAgICAgICAgICAgIHJlcS5sb2FkKGNvbnRleHQsIGlkLCB1cmwpO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgLyoqXG4gICAgICAgICAgICAgKiBFeGVjdXRlcyBhIG1vZHVsZSBjYWxsYmFjayBmdW5jdGlvbi5cbiAgICAgICAgICAgICAqXG4gICAgICAgICAgICAgKiBCcm9rZW4gb3V0IGFzIGEgc2VwYXJhdGUgZnVuY3Rpb24gc29sZWx5IHRvIGFsbG93IHRoZSBidWlsZCBzeXN0ZW0gdG8gc2VxdWVuY2UgdGhlIGZpbGVzIGluIHRoZSBidWlsdFxuICAgICAgICAgICAgICogbGF5ZXIgaW4gdGhlIHJpZ2h0IHNlcXVlbmNlLlxuICAgICAgICAgICAgICpcbiAgICAgICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAgICAgKi9cbiAgICAgICAgICAgIGV4ZWNDYjogZnVuY3Rpb24gKG5hbWUsIGNhbGxiYWNrLCBhcmdzLCBleHBvcnRzKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGNhbGxiYWNrLmFwcGx5KGV4cG9ydHMsIGFyZ3MpO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgLyoqXG4gICAgICAgICAgICAgKiBDYWxsYmFjayBmb3Igc2NyaXB0IGxvYWRzLCB1c2VkIHRvIGNoZWNrIHN0YXR1cyBvZiBsb2FkaW5nLlxuICAgICAgICAgICAgICpcbiAgICAgICAgICAgICAqIEBwYXJhbSB7RXZlbnR9IGV2dCB0aGUgZXZlbnQgZnJvbSB0aGUgYnJvd3NlciBmb3IgdGhlIHNjcmlwdCB0aGF0IHdhcyBsb2FkZWQuXG4gICAgICAgICAgICAgKi9cbiAgICAgICAgICAgIG9uU2NyaXB0TG9hZDogZnVuY3Rpb24gKGV2dCkge1xuICAgICAgICAgICAgICAgIC8vIFVzaW5nIGN1cnJlbnRUYXJnZXQgaW5zdGVhZCBvZiB0YXJnZXQgZm9yIEZpcmVmb3ggMi4wJ3Mgc2FrZS4gTm90XG4gICAgICAgICAgICAgICAgLy8gYWxsIG9sZCBicm93c2VycyB3aWxsIGJlIHN1cHBvcnRlZCwgYnV0IHRoaXMgb25lIHdhcyBlYXN5IGVub3VnaFxuICAgICAgICAgICAgICAgIC8vIHRvIHN1cHBvcnQgYW5kIHN0aWxsIG1ha2VzIHNlbnNlLlxuICAgICAgICAgICAgICAgIGlmIChldnQudHlwZSA9PT0gJ2xvYWQnIHx8XG4gICAgICAgICAgICAgICAgICAgIChyZWFkeVJlZ0V4cC50ZXN0KChldnQuY3VycmVudFRhcmdldCB8fCBldnQuc3JjRWxlbWVudCkucmVhZHlTdGF0ZSkpKSB7XG4gICAgICAgICAgICAgICAgICAgIC8vIFJlc2V0IGludGVyYWN0aXZlIHNjcmlwdCBzbyBhIHNjcmlwdCBub2RlIGlzIG5vdCBoZWxkIG9udG8gZm9yXG4gICAgICAgICAgICAgICAgICAgIC8vIHRvIGxvbmcuXG4gICAgICAgICAgICAgICAgICAgIGludGVyYWN0aXZlU2NyaXB0ID0gbnVsbDtcblxuICAgICAgICAgICAgICAgICAgICAvLyBQdWxsIG91dCB0aGUgbmFtZSBvZiB0aGUgbW9kdWxlIGFuZCB0aGUgY29udGV4dC5cbiAgICAgICAgICAgICAgICAgICAgbGV0IGRhdGEgPSBnZXRTY3JpcHREYXRhKGV2dCk7XG4gICAgICAgICAgICAgICAgICAgIGNvbnRleHQuY29tcGxldGVMb2FkKGRhdGEuaWQpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIC8qKlxuICAgICAgICAgICAgICogQ2FsbGJhY2sgZm9yIHNjcmlwdCBlcnJvcnMuXG4gICAgICAgICAgICAgKi9cbiAgICAgICAgICAgIG9uU2NyaXB0RXJyb3I6IGZ1bmN0aW9uIChldnQpIHtcbiAgICAgICAgICAgICAgICBsZXQgZGF0YSA9IGdldFNjcmlwdERhdGEoZXZ0KTtcbiAgICAgICAgICAgICAgICBpZiAoIWhhc1BhdGhGYWxsYmFjayhkYXRhLmlkKSkge1xuICAgICAgICAgICAgICAgICAgICBsZXQgcGFyZW50cyA9IFtdO1xuICAgICAgICAgICAgICAgICAgICBlYWNoUHJvcChyZWdpc3RyeSwgZnVuY3Rpb24gKHZhbHVlLCBrZXkpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChrZXkuaW5kZXhPZignX0ByJykgIT09IDApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBlYWNoKHZhbHVlLmRlcE1hcHMsIGZ1bmN0aW9uIChkZXBNYXApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGRlcE1hcC5pZCA9PT0gZGF0YS5pZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcGFyZW50cy5wdXNoKGtleSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gb25FcnJvcihtYWtlRXJyb3IoJ3NjcmlwdGVycm9yJywgJ1NjcmlwdCBlcnJvciBmb3IgXCInICsgZGF0YS5pZCArXG4gICAgICAgICAgICAgICAgICAgICAgICAocGFyZW50cy5sZW5ndGggP1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICdcIiwgbmVlZGVkIGJ5OiAnICsgcGFyZW50cy5qb2luKCcsICcpIDpcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAnXCInKSwgZXZ0LCBbZGF0YS5pZF0pKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgY29udGV4dC5yZXF1aXJlID0gY29udGV4dC5tYWtlUmVxdWlyZSgpO1xuICAgICAgICByZXR1cm4gY29udGV4dDtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBNYWluIGVudHJ5IHBvaW50LlxuICAgICAqXG4gICAgICogSWYgdGhlIG9ubHkgYXJndW1lbnQgdG8gcmVxdWlyZSBpcyBhIHN0cmluZywgdGhlbiB0aGUgbW9kdWxlIHRoYXQgaXMgcmVwcmVzZW50ZWQgYnkgdGhhdCBzdHJpbmcgaXMgZmV0Y2hlZCBmb3JcbiAgICAgKiB0aGUgYXBwcm9wcmlhdGUgY29udGV4dC5cbiAgICAgKlxuICAgICAqIElmIHRoZSBmaXJzdCBhcmd1bWVudCBpcyBhbiBhcnJheSwgdGhlbiBpdCB3aWxsIGJlIHRyZWF0ZWQgYXMgYW4gYXJyYXkgb2YgZGVwZW5kZW5jeSBzdHJpbmcgbmFtZXMgdG8gZmV0Y2guIEFuXG4gICAgICogb3B0aW9uYWwgZnVuY3Rpb24gY2FsbGJhY2sgY2FuIGJlIHNwZWNpZmllZCB0byBleGVjdXRlIHdoZW4gYWxsIG9mIHRob3NlIGRlcGVuZGVuY2llcyBhcmUgYXZhaWxhYmxlLlxuICAgICAqXG4gICAgICogTWFrZSBhIGxvY2FsIHJlcSB2YXJpYWJsZSB0byBoZWxwIENhamEgY29tcGxpYW5jZSAoaXQgYXNzdW1lcyB0aGluZ3Mgb24gYSByZXF1aXJlIHRoYXQgYXJlIG5vdCBzdGFuZGFyZGl6ZWQpLFxuICAgICAqIGFuZCB0byBnaXZlIGEgc2hvcnQgbmFtZSBmb3IgbWluaWZpY2F0aW9uL2xvY2FsIHNjb3BlIHVzZS5cbiAgICAgKi9cbiAgICByZXEgPSB3aW5kb3cucmVxdWlyZWpzID0gZnVuY3Rpb24gKGRlcHMsIGNhbGxiYWNrLCBlcnJiYWNrLCBvcHRpb25hbCkge1xuICAgICAgICAvLyBGaW5kIHRoZSByaWdodCBjb250ZXh0LCB1c2UgZGVmYXVsdFxuICAgICAgICBsZXQgY29udGV4dDtcbiAgICAgICAgbGV0IGNvbmZpZztcbiAgICAgICAgbGV0IGNvbnRleHROYW1lID0gZGVmQ29udGV4dE5hbWU7XG5cbiAgICAgICAgLy8gRGV0ZXJtaW5lIGlmIGhhdmUgY29uZmlnIG9iamVjdCBpbiB0aGUgY2FsbC5cbiAgICAgICAgaWYgKCFpc0FycmF5KGRlcHMpICYmIHR5cGVvZiBkZXBzICE9PSAnc3RyaW5nJykge1xuICAgICAgICAgICAgLy8gZGVwcyBpcyBhIGNvbmZpZyBvYmplY3RcbiAgICAgICAgICAgIGNvbmZpZyA9IGRlcHM7XG4gICAgICAgICAgICBpZiAoaXNBcnJheShjYWxsYmFjaykpIHtcbiAgICAgICAgICAgICAgICAvLyBBZGp1c3QgYXJncyBpZiB0aGVyZSBhcmUgZGVwZW5kZW5jaWVzXG4gICAgICAgICAgICAgICAgZGVwcyA9IGNhbGxiYWNrO1xuICAgICAgICAgICAgICAgIGNhbGxiYWNrID0gZXJyYmFjaztcbiAgICAgICAgICAgICAgICBlcnJiYWNrID0gb3B0aW9uYWw7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIGRlcHMgPSBbXTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIGlmIChjb25maWcgJiYgY29uZmlnLmNvbnRleHQpIHtcbiAgICAgICAgICAgIGNvbnRleHROYW1lID0gY29uZmlnLmNvbnRleHQ7XG4gICAgICAgIH1cblxuICAgICAgICBjb250ZXh0ID0gZ2V0T3duKGNvbnRleHRzLCBjb250ZXh0TmFtZSk7XG4gICAgICAgIGlmICghY29udGV4dCkge1xuICAgICAgICAgICAgY29udGV4dCA9IGNvbnRleHRzW2NvbnRleHROYW1lXSA9IHJlcS5zLm5ld0NvbnRleHQoY29udGV4dE5hbWUpO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKGNvbmZpZykge1xuICAgICAgICAgICAgY29udGV4dC5jb25maWd1cmUoY29uZmlnKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBjb250ZXh0LnJlcXVpcmUoZGVwcywgY2FsbGJhY2ssIGVycmJhY2spO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBTdXBwb3J0IHJlcXVpcmUuY29uZmlnKCkgdG8gbWFrZSBpdCBlYXNpZXIgdG8gY29vcGVyYXRlIHdpdGggb3RoZXIgQU1EIGxvYWRlcnMgb24gZ2xvYmFsbHkgYWdyZWVkIG5hbWVzLlxuICAgICAqL1xuICAgIHJlcS5jb25maWcgPSBmdW5jdGlvbiAoY29uZmlnKSB7XG4gICAgICAgIHJldHVybiByZXEoY29uZmlnKTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogRXhlY3V0ZSBzb21ldGhpbmcgYWZ0ZXIgdGhlIGN1cnJlbnQgdGljayBvZiB0aGUgZXZlbnQgbG9vcC5cbiAgICAgKlxuICAgICAqIE92ZXJyaWRlIGZvciBvdGhlciBlbnZzIHRoYXQgaGF2ZSBhIGJldHRlciBzb2x1dGlvbiB0aGFuIHNldFRpbWVvdXQuXG4gICAgICpcbiAgICAgKiBAcGFyYW0gIHtGdW5jdGlvbn0gZm4gZnVuY3Rpb24gdG8gZXhlY3V0ZSBsYXRlci5cbiAgICAgKi9cbiAgICByZXEubmV4dFRpY2sgPSB0eXBlb2Ygc2V0VGltZW91dCAhPT0gJ3VuZGVmaW5lZCcgPyBmdW5jdGlvbiAoZm4pIHtcbiAgICAgICAgc2V0VGltZW91dChmbiwgNCk7XG4gICAgfSA6IGZ1bmN0aW9uIChmbikge1xuICAgICAgICBmbigpO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBFeHBvcnQgcmVxdWlyZSBhcyBhIGdsb2JhbCwgYnV0IG9ubHkgaWYgaXQgZG9lcyBub3QgYWxyZWFkeSBleGlzdC5cbiAgICAgKi9cbiAgICBpZiAoIXdpbmRvdy5yZXF1aXJlKSB7XG4gICAgICAgIHdpbmRvdy5yZXF1aXJlID0gcmVxO1xuICAgIH1cblxuICAgIHJlcS52ZXJzaW9uID0gdmVyc2lvbjtcblxuICAgIC8vIFVzZWQgdG8gZmlsdGVyIG91dCBkZXBlbmRlbmNpZXMgdGhhdCBhcmUgYWxyZWFkeSBwYXRocy5cbiAgICByZXEuanNFeHRSZWdFeHAgPSAvXlxcL3w6fFxcP3xcXC5qcyQvO1xuICAgIHJlcS5pc0Jyb3dzZXIgPSBpc0Jyb3dzZXI7XG4gICAgcyA9IHJlcS5zID0ge1xuICAgICAgICBjb250ZXh0czogY29udGV4dHMsXG4gICAgICAgIG5ld0NvbnRleHQ6IG5ld0NvbnRleHRcbiAgICB9O1xuXG4gICAgLy8gQ3JlYXRlIGRlZmF1bHQgY29udGV4dC5cbiAgICByZXEoe30pO1xuXG4gICAgLy8gRXhwb3J0cyBzb21lIGNvbnRleHQtc2Vuc2l0aXZlIG1ldGhvZHMgb24gZ2xvYmFsIHJlcXVpcmUuXG4gICAgZWFjaChbXG4gICAgICAgICd0b1VybCcsXG4gICAgICAgICd1bmRlZicsXG4gICAgICAgICdkZWZpbmVkJyxcbiAgICAgICAgJ3NwZWNpZmllZCdcbiAgICBdLCBmdW5jdGlvbiAocHJvcCkge1xuICAgICAgICAvLyAgUmVmZXJlbmNlIGZyb20gY29udGV4dHMgaW5zdGVhZCBvZiBlYXJseSBiaW5kaW5nIHRvIGRlZmF1bHQgY29udGV4dCxcbiAgICAgICAgLy8gc28gdGhhdCBkdXJpbmcgYnVpbGRzLCB0aGUgbGF0ZXN0IGluc3RhbmNlIG9mIHRoZSBkZWZhdWx0IGNvbnRleHRcbiAgICAgICAgLy8gd2l0aCBpdHMgY29uZmlnIGdldHMgdXNlZC5cbiAgICAgICAgcmVxW3Byb3BdID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgbGV0IGN0eCA9IGNvbnRleHRzW2RlZkNvbnRleHROYW1lXTtcbiAgICAgICAgICAgIHJldHVybiBjdHgucmVxdWlyZVtwcm9wXS5hcHBseShjdHgsIGFyZ3VtZW50cyk7XG4gICAgICAgIH07XG4gICAgfSk7XG5cbiAgICBpZiAoaXNCcm93c2VyKSB7XG4gICAgICAgIGhlYWQgPSBzLmhlYWQgPSBkb2N1bWVudC5nZXRFbGVtZW50c0J5VGFnTmFtZSgnaGVhZCcpWzBdO1xuICAgICAgICAvLyBJZiBCQVNFIHRhZyBpcyBpbiBwbGF5LCB1c2luZyBhcHBlbmRDaGlsZCBpcyBhIHByb2JsZW0gZm9yIElFNi5cbiAgICAgICAgLy8gV2hlbiB0aGF0IGJyb3dzZXIgZGllcywgdGhpcyBjYW4gYmUgcmVtb3ZlZC4gRGV0YWlscyBpbiB0aGlzIGpRdWVyeSBidWc6XG4gICAgICAgIC8vIGh0dHA6Ly9kZXYuanF1ZXJ5LmNvbS90aWNrZXQvMjcwOVxuICAgICAgICBiYXNlRWxlbWVudCA9IGRvY3VtZW50LmdldEVsZW1lbnRzQnlUYWdOYW1lKCdiYXNlJylbMF07XG4gICAgICAgIGlmIChiYXNlRWxlbWVudCkge1xuICAgICAgICAgICAgaGVhZCA9IHMuaGVhZCA9IGJhc2VFbGVtZW50LnBhcmVudE5vZGU7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBBbnkgZXJyb3JzIHRoYXQgcmVxdWlyZSBleHBsaWNpdGx5IGdlbmVyYXRlcyB3aWxsIGJlIHBhc3NlZCB0byB0aGlzIGZ1bmN0aW9uLlxuICAgICAqXG4gICAgICogSW50ZXJjZXB0L292ZXJyaWRlIGl0IGlmIHlvdSB3YW50IGN1c3RvbSBlcnJvciBoYW5kbGluZy5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7RXJyb3J9IGVyciB0aGUgZXJyb3Igb2JqZWN0LlxuICAgICAqL1xuICAgIHJlcS5vbkVycm9yID0gZGVmYXVsdE9uRXJyb3I7XG5cbiAgICAvKipcbiAgICAgKiBDcmVhdGVzIHRoZSBub2RlIGZvciB0aGUgbG9hZCBjb21tYW5kLiBPbmx5IHVzZWQgaW4gYnJvd3NlciBlbnZzLlxuICAgICAqL1xuICAgIHJlcS5jcmVhdGVOb2RlID0gZnVuY3Rpb24gKGNvbmZpZywgbW9kdWxlTmFtZSwgdXJsKSB7XG4gICAgICAgIGNvbnN0IG5vZGUgPSBjb25maWcueGh0bWwgP1xuICAgICAgICAgICAgZG9jdW1lbnQuY3JlYXRlRWxlbWVudE5TKCdodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hodG1sJywgJ2h0bWw6c2NyaXB0JykgOlxuICAgICAgICAgICAgZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnc2NyaXB0Jyk7XG4gICAgICAgIG5vZGUudHlwZSA9IGNvbmZpZy5zY3JpcHRUeXBlIHx8ICd0ZXh0L2phdmFzY3JpcHQnO1xuICAgICAgICBub2RlLmNoYXJzZXQgPSAndXRmLTgnO1xuICAgICAgICBub2RlLmFzeW5jID0gdHJ1ZTtcbiAgICAgICAgcmV0dXJuIG5vZGU7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIERvZXMgdGhlIHJlcXVlc3QgdG8gbG9hZCBhIG1vZHVsZSBmb3IgdGhlIGJyb3dzZXIgY2FzZS5cbiAgICAgKlxuICAgICAqIE1ha2UgdGhpcyBhIHNlcGFyYXRlIGZ1bmN0aW9uIHRvIGFsbG93IG90aGVyIGVudmlyb25tZW50cyB0byBvdmVycmlkZSBpdC5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBjb250ZXh0IHRoZSByZXF1aXJlIGNvbnRleHQgdG8gZmluZCBzdGF0ZS5cbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gbW9kdWxlTmFtZSB0aGUgbmFtZSBvZiB0aGUgbW9kdWxlLlxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSB1cmwgdGhlIFVSTCB0byB0aGUgbW9kdWxlLlxuICAgICAqL1xuICAgIHJlcS5sb2FkID0gZnVuY3Rpb24gKGNvbnRleHQsIG1vZHVsZU5hbWUsIHVybCkge1xuICAgICAgICBsZXQgY29uZmlnID0gKGNvbnRleHQgJiYgY29udGV4dC5jb25maWcpIHx8IHt9O1xuICAgICAgICBsZXQgbm9kZTtcblxuICAgICAgICBpZiAoaXNCcm93c2VyKSB7XG4gICAgICAgICAgICAvLyBJbiB0aGUgYnJvd3NlciBzbyB1c2UgYSBzY3JpcHQgdGFnXG4gICAgICAgICAgICBub2RlID0gcmVxLmNyZWF0ZU5vZGUoY29uZmlnLCBtb2R1bGVOYW1lLCB1cmwpO1xuICAgICAgICAgICAgaWYgKGNvbmZpZy5vbk5vZGVDcmVhdGVkKSB7XG4gICAgICAgICAgICAgICAgY29uZmlnLm9uTm9kZUNyZWF0ZWQobm9kZSwgY29uZmlnLCBtb2R1bGVOYW1lLCB1cmwpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBub2RlLnNldEF0dHJpYnV0ZSgnZGF0YS1yZXF1aXJlY29udGV4dCcsIGNvbnRleHQuY29udGV4dE5hbWUpO1xuICAgICAgICAgICAgbm9kZS5zZXRBdHRyaWJ1dGUoJ2RhdGEtcmVxdWlyZW1vZHVsZScsIG1vZHVsZU5hbWUpO1xuXG4gICAgICAgICAgICAvLyBTZXQgdXAgbG9hZCBsaXN0ZW5lci4gVGVzdCBhdHRhY2hFdmVudCBmaXJzdCBiZWNhdXNlIElFOSBoYXMgYSBzdWJ0bGUgaXNzdWUgaW4gaXRzIGFkZEV2ZW50TGlzdGVuZXIgYW5kIFxuICAgICAgICAgICAgLy8gc2NyaXB0IG9ubG9hZCBmaXJpbmdzIHRoYXQgZG8gbm90IG1hdGNoIHRoZSBiZWhhdmlvciBvZiBhbGwgb3RoZXIgYnJvd3NlcnMgd2l0aCBhZGRFdmVudExpc3RlbmVyIHN1cHBvcnQsIFxuICAgICAgICAgICAgLy8gd2hpY2ggZmlyZSB0aGUgb25sb2FkIGV2ZW50IGZvciBhIHNjcmlwdCByaWdodCBhZnRlciB0aGUgc2NyaXB0IGV4ZWN1dGlvbi4gU2VlOlxuICAgICAgICAgICAgLy8gaHR0cHM6Ly9jb25uZWN0Lm1pY3Jvc29mdC5jb20vSUUvZmVlZGJhY2svZGV0YWlscy82NDgwNTcvc2NyaXB0LW9ubG9hZC1ldmVudC1pcy1ub3QtZmlyZWQtaW1tZWRpYXRlbHktYWZ0ZXItc2NyaXB0LWV4ZWN1dGlvblxuICAgICAgICAgICAgLy8gVU5GT1JUVU5BVEVMWSBPcGVyYSBpbXBsZW1lbnRzIGF0dGFjaEV2ZW50IGJ1dCBkb2VzIG5vdCBmb2xsb3cgdGhlIHNjcmlwdCBzY3JpcHQgZXhlY3V0aW9uIG1vZGUuXG4gICAgICAgICAgICBpZiAobm9kZS5hdHRhY2hFdmVudCAmJlxuICAgICAgICAgICAgICAgIC8vIENoZWNrIGlmIG5vZGUuYXR0YWNoRXZlbnQgaXMgYXJ0aWZpY2lhbGx5IGFkZGVkIGJ5IGN1c3RvbSBzY3JpcHQgb3IgbmF0aXZlbHkgc3VwcG9ydGVkIGJ5IGJyb3dzZXJcbiAgICAgICAgICAgICAgICAvLyByZWFkIGh0dHBzOi8vZ2l0aHViLmNvbS9qcmJ1cmtlL3JlcXVpcmVqcy9pc3N1ZXMvMTg3XG4gICAgICAgICAgICAgICAgLy8gaWYgd2UgY2FuIE5PVCBmaW5kIFtuYXRpdmUgY29kZV0gdGhlbiBpdCBtdXN0IE5PVCBuYXRpdmVseSBzdXBwb3J0ZWQuIGluIElFOCwgbm9kZS5hdHRhY2hFdmVudCBkb2VzIFxuICAgICAgICAgICAgICAgIC8vIG5vdCBoYXZlIHRvU3RyaW5nKCkuIE5vdGUgdGhlIHRlc3QgZm9yIFwiW25hdGl2ZSBjb2RlXCIgd2l0aCBubyBjbG9zaW5nIGJyYWNlLCBzZWU6XG4gICAgICAgICAgICAgICAgLy8gaHR0cHM6Ly9naXRodWIuY29tL2pyYnVya2UvcmVxdWlyZWpzL2lzc3Vlcy8yNzNcbiAgICAgICAgICAgICAgICAhKG5vZGUuYXR0YWNoRXZlbnQudG9TdHJpbmcgJiYgbm9kZS5hdHRhY2hFdmVudC50b1N0cmluZygpLmluZGV4T2YoJ1tuYXRpdmUgY29kZScpIDwgMCkgJiZcbiAgICAgICAgICAgICAgICAhaXNPcGVyYSkge1xuICAgICAgICAgICAgICAgIC8vIFByb2JhYmx5IElFLiBJRSAoYXQgbGVhc3QgNi04KSBkbyBub3QgZmlyZSBzY3JpcHQgb25sb2FkIHJpZ2h0IGFmdGVyIGV4ZWN1dGluZyB0aGUgc2NyaXB0LCBzb1xuICAgICAgICAgICAgICAgIC8vIHdlIGNhbm5vdCB0aWUgdGhlIGFub255bW91cyBkZWZpbmUgY2FsbCB0byBhIG5hbWUuIEhvd2V2ZXIsIElFIHJlcG9ydHMgdGhlIHNjcmlwdCBhcyBiZWluZyBpbiBcbiAgICAgICAgICAgICAgICAvLyAnaW50ZXJhY3RpdmUnIHJlYWR5U3RhdGUgYXQgdGhlIHRpbWUgb2YgdGhlIGRlZmluZSBjYWxsLlxuICAgICAgICAgICAgICAgIHVzZUludGVyYWN0aXZlID0gdHJ1ZTtcblxuICAgICAgICAgICAgICAgIG5vZGUuYXR0YWNoRXZlbnQoJ29ucmVhZHlzdGF0ZWNoYW5nZScsIGNvbnRleHQub25TY3JpcHRMb2FkKTtcbiAgICAgICAgICAgICAgICAvLyBJdCB3b3VsZCBiZSBncmVhdCB0byBhZGQgYW4gZXJyb3IgaGFuZGxlciBoZXJlIHRvIGNhdGNoIDQwNHMgaW4gSUU5Ky4gSG93ZXZlciwgb25yZWFkeXN0YXRlY2hhbmdlIFxuICAgICAgICAgICAgICAgIC8vIHdpbGwgZmlyZSBiZWZvcmUgdGhlIGVycm9yIGhhbmRsZXIsIHNvIHRoYXQgZG9lcyBub3QgaGVscC4gSWYgYWRkRXZlbnRMaXN0ZW5lciBpcyB1c2VkLCB0aGVuIElFIHdpbGwgXG4gICAgICAgICAgICAgICAgLy8gZmlyZSBlcnJvciBiZWZvcmUgbG9hZCwgYnV0IHdlIGNhbm5vdCB1c2UgdGhhdCBwYXRod2F5IGdpdmVuIHRoZSBjb25uZWN0Lm1pY3Jvc29mdC5jb20gaXNzdWUgXG4gICAgICAgICAgICAgICAgLy8gbWVudGlvbmVkIGFib3ZlIGFib3V0IG5vdCBkb2luZyB0aGUgJ3NjcmlwdCBleGVjdXRlLCB0aGVuIGZpcmUgdGhlIHNjcmlwdCBsb2FkIGV2ZW50IGxpc3RlbmVyIGJlZm9yZSBcbiAgICAgICAgICAgICAgICAvLyBleGVjdXRlIG5leHQgc2NyaXB0JyB0aGF0IG90aGVyIGJyb3dzZXJzIGRvLiBCZXN0IGhvcGU6IElFMTAgZml4ZXMgdGhlIGlzc3VlcywgYW5kIHRoZW4gZGVzdHJveXMgYWxsIFxuICAgICAgICAgICAgICAgIC8vIGluc3RhbGxzIG9mIElFIDYtOS5cbiAgICAgICAgICAgICAgICAvLyBub2RlLmF0dGFjaEV2ZW50KCdvbmVycm9yJywgY29udGV4dC5vblNjcmlwdEVycm9yKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgbm9kZS5hZGRFdmVudExpc3RlbmVyKCdsb2FkJywgY29udGV4dC5vblNjcmlwdExvYWQsIGZhbHNlKTtcbiAgICAgICAgICAgICAgICBub2RlLmFkZEV2ZW50TGlzdGVuZXIoJ2Vycm9yJywgY29udGV4dC5vblNjcmlwdEVycm9yLCBmYWxzZSk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBub2RlLnNyYyA9IHVybDtcblxuICAgICAgICAgICAgLy8gRm9yIHNvbWUgY2FjaGUgY2FzZXMgaW4gSUUgNi04LCB0aGUgc2NyaXB0IGV4ZWN1dGVzIGJlZm9yZSB0aGUgZW5kIG9mIHRoZSBhcHBlbmRDaGlsZCBleGVjdXRpb24sIHNvIHRvIFxuICAgICAgICAgICAgLy8gdGllIGFuIGFub255bW91cyBkZWZpbmUgY2FsbCB0byB0aGUgbW9kdWxlIG5hbWUgKHdoaWNoIGlzIHN0b3JlZCBvbiB0aGUgbm9kZSksIGhvbGQgb24gdG8gYSByZWZlcmVuY2UgdG8gXG4gICAgICAgICAgICAvLyB0aGlzIG5vZGUsIGJ1dCBjbGVhciBhZnRlciB0aGUgRE9NIGluc2VydGlvbi5cbiAgICAgICAgICAgIGlmIChiYXNlRWxlbWVudCkge1xuICAgICAgICAgICAgICAgIGhlYWQuaW5zZXJ0QmVmb3JlKG5vZGUsIGJhc2VFbGVtZW50KTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgaGVhZC5hcHBlbmRDaGlsZChub2RlKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIG5vZGU7XG4gICAgICAgIH0gZWxzZSBpZiAoaXNXZWJXb3JrZXIpIHtcbiAgICAgICAgICAgIHRyeSB7XG4gICAgICAgICAgICAgICAgLy8gSW4gYSB3ZWIgd29ya2VyLCB1c2UgaW1wb3J0U2NyaXB0cy4gVGhpcyBpcyBub3QgYSB2ZXJ5XG4gICAgICAgICAgICAgICAgLy8gZWZmaWNpZW50IHVzZSBvZiBpbXBvcnRTY3JpcHRzLCBpbXBvcnRTY3JpcHRzIHdpbGwgYmxvY2sgdW50aWxcbiAgICAgICAgICAgICAgICAvLyBpdHMgc2NyaXB0IGlzIGRvd25sb2FkZWQgYW5kIGV2YWx1YXRlZC4gSG93ZXZlciwgaWYgd2ViIHdvcmtlcnNcbiAgICAgICAgICAgICAgICAvLyBhcmUgaW4gcGxheSwgdGhlIGV4cGVjdGF0aW9uIGlzIHRoYXQgYSBidWlsZCBoYXMgYmVlbiBkb25lIHNvXG4gICAgICAgICAgICAgICAgLy8gdGhhdCBvbmx5IG9uZSBzY3JpcHQgbmVlZHMgdG8gYmUgbG9hZGVkIGFueXdheS4gVGhpcyBtYXkgbmVlZFxuICAgICAgICAgICAgICAgIC8vIHRvIGJlIHJlZXZhbHVhdGVkIGlmIG90aGVyIHVzZSBjYXNlcyBiZWNvbWUgY29tbW9uLlxuICAgICAgICAgICAgICAgIGltcG9ydFNjcmlwdHModXJsKTtcblxuICAgICAgICAgICAgICAgIC8vIEFjY291bnQgZm9yIGFub255bW91cyBtb2R1bGVzXG4gICAgICAgICAgICAgICAgY29udGV4dC5jb21wbGV0ZUxvYWQobW9kdWxlTmFtZSk7XG4gICAgICAgICAgICB9IGNhdGNoIChlKSB7XG4gICAgICAgICAgICAgICAgY29udGV4dC5vbkVycm9yKG1ha2VFcnJvcignaW1wb3J0c2NyaXB0cycsXG4gICAgICAgICAgICAgICAgICAgICdpbXBvcnRTY3JpcHRzIGZhaWxlZCBmb3IgJyArXG4gICAgICAgICAgICAgICAgICAgIG1vZHVsZU5hbWUgKyAnIGF0ICcgKyB1cmwsXG4gICAgICAgICAgICAgICAgICAgIGUsXG4gICAgICAgICAgICAgICAgICAgIFttb2R1bGVOYW1lXSkpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfTtcblxuICAgIC8vIExvb2sgZm9yIGEgZGF0YS1tYWluIHNjcmlwdCBhdHRyaWJ1dGUsIHdoaWNoIGNvdWxkIGFsc28gYWRqdXN0IHRoZSBiYXNlVXJsLlxuICAgIGlmIChpc0Jyb3dzZXIgJiYgIWNmZy5za2lwRGF0YU1haW4pIHtcbiAgICAgICAgLy8gRmlndXJlIG91dCBiYXNlVXJsLiBHZXQgaXQgZnJvbSB0aGUgc2NyaXB0IHRhZyB3aXRoIHJlcXVpcmUuanMgaW4gaXQuXG4gICAgICAgIGVhY2hSZXZlcnNlKHNjcmlwdHMoKSwgZnVuY3Rpb24gKHNjcmlwdCkge1xuICAgICAgICAgICAgLy8gU2V0IHRoZSAnaGVhZCcgd2hlcmUgd2UgY2FuIGFwcGVuZCBjaGlsZHJlbiBieVxuICAgICAgICAgICAgLy8gdXNpbmcgdGhlIHNjcmlwdCdzIHBhcmVudC5cbiAgICAgICAgICAgIGlmICghaGVhZCkge1xuICAgICAgICAgICAgICAgIGhlYWQgPSBzY3JpcHQucGFyZW50Tm9kZTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy8gTG9vayBmb3IgYSBkYXRhLW1haW4gYXR0cmlidXRlIHRvIHNldCBtYWluIHNjcmlwdCBmb3IgdGhlIHBhZ2VcbiAgICAgICAgICAgIC8vIHRvIGxvYWQuIElmIGl0IGlzIHRoZXJlLCB0aGUgcGF0aCB0byBkYXRhIG1haW4gYmVjb21lcyB0aGVcbiAgICAgICAgICAgIC8vIGJhc2VVcmwsIGlmIGl0IGlzIG5vdCBhbHJlYWR5IHNldC5cbiAgICAgICAgICAgIGRhdGFNYWluID0gc2NyaXB0LmdldEF0dHJpYnV0ZSgnZGF0YS1tYWluJyk7XG4gICAgICAgICAgICBpZiAoZGF0YU1haW4pIHtcbiAgICAgICAgICAgICAgICAvLyBQcmVzZXJ2ZSBkYXRhTWFpbiBpbiBjYXNlIGl0IGlzIGEgcGF0aCAoaS5lLiBjb250YWlucyAnPycpXG4gICAgICAgICAgICAgICAgbWFpblNjcmlwdCA9IGRhdGFNYWluO1xuXG4gICAgICAgICAgICAgICAgLy8gU2V0IGZpbmFsIGJhc2VVcmwgaWYgdGhlcmUgaXMgbm90IGFscmVhZHkgYW4gZXhwbGljaXQgb25lLlxuICAgICAgICAgICAgICAgIGlmICghY2ZnLmJhc2VVcmwpIHtcbiAgICAgICAgICAgICAgICAgICAgLy8gUHVsbCBvZmYgdGhlIGRpcmVjdG9yeSBvZiBkYXRhLW1haW4gZm9yIHVzZSBhcyB0aGVcbiAgICAgICAgICAgICAgICAgICAgLy8gYmFzZVVybC5cbiAgICAgICAgICAgICAgICAgICAgc3JjID0gbWFpblNjcmlwdC5zcGxpdCgnLycpO1xuICAgICAgICAgICAgICAgICAgICBtYWluU2NyaXB0ID0gc3JjLnBvcCgpO1xuICAgICAgICAgICAgICAgICAgICBzdWJQYXRoID0gc3JjLmxlbmd0aCA/IHNyYy5qb2luKCcvJykgKyAnLycgOiAnLi8nO1xuXG4gICAgICAgICAgICAgICAgICAgIGNmZy5iYXNlVXJsID0gc3ViUGF0aDtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAvLyBTdHJpcCBvZmYgYW55IHRyYWlsaW5nIC5qcyBzaW5jZSBtYWluU2NyaXB0IGlzIG5vd1xuICAgICAgICAgICAgICAgIC8vIGxpa2UgYSBtb2R1bGUgbmFtZS5cbiAgICAgICAgICAgICAgICBtYWluU2NyaXB0ID0gbWFpblNjcmlwdC5yZXBsYWNlKGpzU3VmZml4UmVnRXhwLCAnJyk7XG5cbiAgICAgICAgICAgICAgICAvLyBJZiBtYWluU2NyaXB0IGlzIHN0aWxsIGEgcGF0aCwgZmFsbCBiYWNrIHRvIGRhdGFNYWluXG4gICAgICAgICAgICAgICAgaWYgKHJlcS5qc0V4dFJlZ0V4cC50ZXN0KG1haW5TY3JpcHQpKSB7XG4gICAgICAgICAgICAgICAgICAgIG1haW5TY3JpcHQgPSBkYXRhTWFpbjtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAvLyBQdXQgdGhlIGRhdGEtbWFpbiBzY3JpcHQgaW4gdGhlIGZpbGVzIHRvIGxvYWQuXG4gICAgICAgICAgICAgICAgY2ZnLmRlcHMgPSBjZmcuZGVwcyA/IGNmZy5kZXBzLmNvbmNhdChtYWluU2NyaXB0KSA6IFttYWluU2NyaXB0XTtcblxuICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBFeGVjdXRlcyB0aGUgdGV4dC4gTm9ybWFsbHkganVzdCB1c2VzIGV2YWwsIGJ1dCBjYW4gYmUgbW9kaWZpZWQgdG8gdXNlIGEgYmV0dGVyLCBlbnZpcm9ubWVudC1zcGVjaWZpYyBjYWxsLlxuICAgICAqIE9ubHkgdXNlZCBmb3IgdHJhbnNwaWxpbmcgbG9hZGVyIHBsdWdpbnMsIG5vdCBmb3IgcGxhaW4gSlMgbW9kdWxlcy5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSB0ZXh0IFRoZSB0ZXh0IHRvIGV4ZWN1dGUvZXZhbHVhdGUuXG4gICAgICovXG4gICAgcmVxLmV4ZWMgPSBmdW5jdGlvbiAodGV4dCkge1xuICAgICAgICAvKmpzbGludCBldmlsOiB0cnVlICovXG4gICAgICAgIHJldHVybiBldmFsKHRleHQpO1xuICAgIH07XG5cbiAgICAvLyBTZXQgdXAgd2l0aCBjb25maWcgaW5mby5cbiAgICByZXEoY2ZnKTtcbn0pKCk7XG4iLCIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHZ1ZS5qcyAyMDE4LTA2LTA3XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxOCBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuanNlLmNvcmUudnVlID0ganNlLmNvcmUudnVlIHx8IHt9O1xuXG4vKipcbiAqIEpTRSBWdWUgSW50ZWdyYXRpb25cbiAqXG4gKiBJbml0aWFsaXplcyB0aGUgVnVlIGFwcGxpY2F0aW9uIGZvciB0aGUgY3VycmVudCBwYWdlLlxuICpcbiAqIFZ1ZSBzdXBwb3J0IGlzIGFjdGl2YXRlZCBpZiB0aGUgZ2xvYmFsIEpTRW5naW5lQ29uZmlndXJhdGlvbiBvYmplY3QgY29udGFpbnMgc2V0cyB0aGUgcmVxdWVzdGVkIHZhbHVlcyBmb3JcbiAqIHRoZSBpbnRlZ3JhdGlvbi5cbiAqXG4gKiBgYGBcbiAqIHdpbmRvdy5KU0VuZ2luZUNvbmZpZ3VyYXRpb24gPSB7XG4gKlx0IGVudmlyb25tZW50OiAnZGV2ZWxvcG1lbnQnLFxuICpcdCBhcHBVcmw6ICdodHRwOi8vZXhhbXBsZS5vcmcnLFxuICpcdCBhcHBWZXJzaW9uOiAndjEuMC4wJyxcbiAqXHQgdHJhbnNsYXRpb25zOiB7fSxcbiAqXHQgbGFuZ3VhZ2VDb2RlOiAnZW4nLFxuICpcdCBwYWdlVG9rZW46ICdjc3JmLXByb3RlY3Rpb24tdG9rZW4nLFxuICpcdCBjYWNoZVRva2VuOiAnY2FjaGUtYnVzdGluZy10b2tlbicgLFxuICpcdCB2dWU6IHtcbiAqXHQgXHRlbDogJy52dWUtaW5zdGFuY2UnXG4gKlx0IH0sIFxuICpcdCBjb2xsZWN0aW9uczogW1xuICpcdCBcdHtuYW1lOiAnY29udHJvbGxlcnMnLCBhdHRyaWJ1dGU6ICdjb250cm9sbGVyJ30sXG4gKlx0IFx0e25hbWU6ICdleHRlbnNpb25zJywgYXR0cmlidXRlOiAnZXh0ZW5zaW9uJ30sXG4gKlx0IFx0e25hbWU6ICd3aWRnZXRzJywgYXR0cmlidXRlOiAnd2lkZ2V0J30sXG4gKlx0IFx0e25hbWU6ICdjb21wYXRpYmlsaXR5JywgYXR0cmlidXRlOiAnY29tcGF0aWJpbGl0eSd9XG4gKlx0IF0sXG4gKlx0IHJlZ2lzdHJ5OiB7fSBcbiAqIH07XG4gKiBgYGBcbiAqXG4gKiBAbW9kdWxlIEpTRS9Db3JlL3BvbHlmaWxsc1xuICovXG4oZnVuY3Rpb24gKGV4cG9ydHMpIHtcblxuICAgICd1c2Ugc3RyaWN0JztcblxuICAgIC8qKlxuICAgICAqIEhvbGRzIHZ1ZSBpbnN0YW5jZXNcbiAgICAgKlxuICAgICAqIEB0eXBlIHtPYmplY3R9XG4gICAgICovXG4gICAgY29uc3QgaW5zdGFuY2VzID0ge307XG5cbiAgICAvKipcbiAgICAgKiBIb2xkcyBncm91cGVkIGNvbXBvbmVudHMuXG4gICAgICpcbiAgICAgKiBHcm91cGVkIGNvbXBvbmVudHMgYXJlIGJlaW5nIHVzZWQgZm9yIGFzc2lnbm1lbnRzIGJldHdlZW4gYSBwYXJlbnQgYW5kIGNoaWxkIFZ1ZSBjb21wb25lbnRzLlxuICAgICAqXG4gICAgICogQHR5cGUge09iamVjdH1cbiAgICAgKi9cbiAgICBjb25zdCBjb21wb25lbnRzID0ge307XG5cbiAgICAvKipcbiAgICAgKiBIb2xkcyBncm91cGVkIGNoaWxkIGNvbXBvbmVudHMuXG4gICAgICpcbiAgICAgKiBHcm91cGVkIGNvbXBvbmVudHMgYXJlIGJlaW5nIHVzZWQgZm9yIGFzc2lnbm1lbnRzIGJldHdlZW4gYSBwYXJlbnQgYW5kIGNoaWxkIFZ1ZSBjb21wb25lbnRzLlxuICAgICAqXG4gICAgICogQHR5cGUge09iamVjdH1cbiAgICAgKi9cbiAgICBjb25zdCBjaGlsZENvbXBvbmVudHMgPSB7fTtcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIFZ1ZSBhcHBsaWNhdGlvbiBvbmNlIHRoZSBKUyBFbmdpbmUgaGFzIGZpbmlzaGVkIGxvYWRpbmcgYWxsIHRoZSBtb2R1bGVzLlxuICAgICAqL1xuICAgIGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ0pTRU5HSU5FX0lOSVRfRklOSVNIRUQnLCAoKSA9PiB7XG4gICAgICAgIGNvbnN0IGdsb2JhbENvbmZpZyA9IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ3Z1ZScpO1xuXG4gICAgICAgIC8vIENoZWNrIGZvciBWdWUgSlNFIGNvbmZpZ3VyYXRpb24uIFxuICAgICAgICBpZiAoIWdsb2JhbENvbmZpZykge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgY29uc3Qgcm9vdHMgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKGdsb2JhbENvbmZpZy5lbCk7XG5cbiAgICAgICAgQXJyYXkuZnJvbShyb290cykuZm9yRWFjaCgocm9vdCwgaW5kZXgpID0+IHtcbiAgICAgICAgICAgIGNvbnN0IG5hbWUgPSByb290LmdldEF0dHJpYnV0ZSgnZGF0YS12dWUtaW5zdGFuY2UtbmFtZScpIHx8IGB2dWUtaW5zdGFuY2UtJHtpbmRleH1gO1xuICAgICAgICAgICAgY29uc3QgY29uZmlnID0gT2JqZWN0LmFzc2lnbih7fSwgZ2xvYmFsQ29uZmlnLCB7ZWw6IHJvb3R9KTtcbiAgICAgICAgICAgIGpzZS5jb3JlLnZ1ZS5jcmVhdGUobmFtZSwgY29uZmlnKTtcbiAgICAgICAgfSk7XG4gICAgfSk7XG5cbiAgICAvKipcbiAgICAgKiBSZWdpc3RlciBhIEpTIEVuZ2luZSB2dWUgbW9kdWxlLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtPYmplY3R9IG1vZHVsZVxuICAgICAqL1xuICAgIGV4cG9ydHMucmVnaXN0ZXJNb2R1bGUgPSBmdW5jdGlvbiAobW9kdWxlKSB7XG4gICAgICAgIGlmICghbW9kdWxlIHx8ICFtb2R1bGUudnVlKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBpZiAoIW1vZHVsZS5wYXJlbnQpIHtcbiAgICAgICAgICAgIGpzZS5jb3JlLnZ1ZS5yZWdpc3RlckNvbXBvbmVudChtb2R1bGUubmFtZSwgbW9kdWxlKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIGpzZS5jb3JlLnZ1ZS5yZWdpc3RlckNoaWxkQ29tcG9uZW50KG1vZHVsZS5wYXJlbnQsIG1vZHVsZS5uYW1lLCBtb2R1bGUpO1xuICAgICAgICB9XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIFJlZ2lzdGVyIGEgY2hpbGQgY29tcG9uZW50LlxuICAgICAqXG4gICAgICogQHBhcmFtIHtTdHJpbmd9IHBhcmVudCBQYXJlbnQgY29tcG9uZW50IHJlZmVyZW5jZS5cbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gbmFtZSBOYW1lIGNvbXBvbmVudCByZWZlcmVuY2UuXG4gICAgICogQHBhcmFtIHtPYmplY3R9IGNoaWxkQ29tcG9uZW50IENoaWxkIGNvbXBvbmVudCBkZWZpbml0aW9uLlxuICAgICAqL1xuICAgIGV4cG9ydHMucmVnaXN0ZXJDaGlsZENvbXBvbmVudCA9IGZ1bmN0aW9uIChwYXJlbnQsIG5hbWUsIGNoaWxkQ29tcG9uZW50KSB7XG4gICAgICAgIGNoaWxkQ29tcG9uZW50c1twYXJlbnRdID0gY2hpbGRDb21wb25lbnRzW3BhcmVudF0gfHwge307XG5cbiAgICAgICAgaWYgKGNoaWxkQ29tcG9uZW50c1twYXJlbnRdW25hbWVdKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBjaGlsZENvbXBvbmVudHNbcGFyZW50XVtuYW1lXSA9IGNoaWxkQ29tcG9uZW50O1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBSZWdpc3RlciBhIGNvbXBvbmVudC5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSBuYW1lIE5hbWUgY29tcG9uZW50IHJlZmVyZW5jZS5cbiAgICAgKiBAcGFyYW0ge09iamVjdH0gY29tcG9uZW50IENvbXBvbmVudCBkZWZpbml0aW9uLlxuICAgICAqL1xuICAgIGV4cG9ydHMucmVnaXN0ZXJDb21wb25lbnQgPSBmdW5jdGlvbiAobmFtZSwgY29tcG9uZW50KSB7XG4gICAgICAgIGlmIChjb21wb25lbnRzW25hbWVdKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBjb21wb25lbnRzW25hbWVdID0gY29tcG9uZW50O1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBDcmVhdGUgYSBuZXcgVnVlIGluc3RhbmNlLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtTdHJpbmd9IG5hbWUgVW5pcXVlIG5hbWUgZm9yIGluc3RhbmNlLlxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBjb25maWcgVnVlIGNvbmZpZ3VyYXRpb24gZm9yIHRoZSBuZXcgaW5zdGFuY2UuXG4gICAgICovXG4gICAgZXhwb3J0cy5jcmVhdGUgPSBmdW5jdGlvbiAobmFtZSwgY29uZmlnKSB7XG4gICAgICAgIGlmIChpbnN0YW5jZXNbbmFtZV0pIHtcbiAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcihgSW5zdGFuY2Ugd2l0aCBuYW1lICR7bmFtZX0gYWxyZWFkeSBleGlzdHMsIHBsZWFzZSB1c2UgYSBkaWZmZXJlbnQgbmFtZS5gKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGZvciAobGV0IG5hbWUgaW4gY29tcG9uZW50cykge1xuICAgICAgICAgICAgY29tcG9uZW50c1tuYW1lXS5jb21wb25lbnRzID0gY2hpbGRDb21wb25lbnRzW25hbWVdIHx8IHt9O1xuXG4gICAgICAgICAgICBWdWUuY29tcG9uZW50KG5hbWUsIGNvbXBvbmVudHNbbmFtZV0pO1xuICAgICAgICB9XG5cbiAgICAgICAgaW5zdGFuY2VzW25hbWVdID0gbmV3IFZ1ZShjb25maWcpO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBEZXN0cm95IGEgdnVlIGluc3RhbmNlLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtTdHJpbmd9IG5hbWUgSW5zdGFuY2UgbmFtZSB0byBiZSBkZXN0cm95ZWQuXG4gICAgICovXG4gICAgZXhwb3J0cy5kZXN0cm95ID0gZnVuY3Rpb24gKG5hbWUpIHtcbiAgICAgICAgaWYgKCFpbnN0YW5jZXNbbmFtZV0pIHtcbiAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcihgSW5zdGFuY2Ugd2l0aCBuYW1lICR7bmFtZX0gZG9lcyBub3QgZXhpc3QsIG1ha2Ugc3VyZSB0aGUgaW5zdGFuY2Ugd2FzIGluaXRpYWxpemVkIGNvcnJlY3RseS5gKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGluc3RhbmNlc1tuYW1lXS4kZGVzdHJveSgpO1xuXG4gICAgICAgIGRlbGV0ZSBpbnN0YW5jZXNbbmFtZV07XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIEdldCBhbGwgYWN0aXZlIGluc3RhbmNlcy5cbiAgICAgKlxuICAgICAqIEByZXR1cm4ge09iamVjdH0gTmFtZSAtIGluc3RhbmNlIHBhaXJzLlxuICAgICAqL1xuICAgIGV4cG9ydHMuaW5zdGFuY2VzID0gZnVuY3Rpb24gKCkge1xuICAgICAgICByZXR1cm4gaW5zdGFuY2VzO1xuICAgIH07XG59KShqc2UuY29yZS52dWUpOyJdLCJwcmVFeGlzdGluZ0NvbW1lbnQiOiIvLyMgc291cmNlTWFwcGluZ1VSTD1kYXRhOmFwcGxpY2F0aW9uL2pzb247Y2hhcnNldD11dGYtODtiYXNlNjQsZXlKMlpYSnphVzl1SWpvekxDSnpiM1Z5WTJWeklqcGJJbTV2WkdWZmJXOWtkV3hsY3k5aWNtOTNjMlZ5TFhCaFkyc3ZYM0J5Wld4MVpHVXVhbk1pTENKamIyNXpkSEoxWTNSdmNuTXZZMjlzYkdWamRHbHZiaTVxY3lJc0ltTnZibk4wY25WamRHOXljeTlrWVhSaFgySnBibVJwYm1jdWFuTWlMQ0pqYjI1emRISjFZM1J2Y25NdmJXOWtkV3hsTG1weklpd2lZMjl1YzNSeWRXTjBiM0p6TDI1aGJXVnpjR0ZqWlM1cWN5SXNJbU52Y21VdllXSnZkWFF1YW5NaUxDSmpiM0psTDJOdmJtWnBaeTVxY3lJc0ltTnZjbVV2WkdWaWRXY3Vhbk1pTENKamIzSmxMMlZ1WjJsdVpTNXFjeUlzSW1OdmNtVXZaWGgwWlc1a0xtcHpJaXdpWTI5eVpTOXBibWwwYVdGc2FYcGxMbXB6SWl3aVkyOXlaUzlzWVc1bkxtcHpJaXdpWTI5eVpTOXRZV2x1TG1weklpd2lZMjl5WlM5dGIyUjFiR1ZmYkc5aFpHVnlMbXB6SWl3aVkyOXlaUzl3YjJ4NVptbHNiSE11YW5NaUxDSmpiM0psTDNKbFoybHpkSEo1TG1weklpd2lZMjl5WlM5eVpYRjFhWEpsTG1weklpd2lZMjl5WlM5MmRXVXVhbk1pWFN3aWJtRnRaWE1pT2x0ZExDSnRZWEJ3YVc1bmN5STZJa0ZCUVVFN096czdPenM3UVVOQlFUczdPenM3T3pzN096dEJRVlZCTEVOQlFVTXNXVUZCV1RzN1FVRkZWRHM3UVVGRlFUczdPenM3T3pzN1FVRktVeXhSUVZkSUxGVkJXRWM3UVVGWlREczdPenM3T3p0QlFVOUJMRFJDUVVGWkxFbEJRVm9zUlVGQmEwSXNVMEZCYkVJc1JVRkJOa0lzVTBGQk4wSXNSVUZCZDBNN1FVRkJRVHM3UVVGRGNFTXNhVUpCUVVzc1NVRkJUQ3hIUVVGWkxFbEJRVm83UVVGRFFTeHBRa0ZCU3l4VFFVRk1MRWRCUVdsQ0xGTkJRV3BDTzBGQlEwRXNhVUpCUVVzc1UwRkJUQ3hIUVVGcFFpeFRRVUZxUWp0QlFVTkJMR2xDUVVGTExFdEJRVXdzUjBGQllUdEJRVU5VTEhsQ1FVRlRMRVZCUkVFN1FVRkZWQ3h6UWtGQlRUdEJRVVpITEdGQlFXSTdRVUZKU0RzN1FVRkZSRHM3T3pzN096czdPenM3T3pzN1FVRTNRa3M3UVVGQlFUdEJRVUZCTEcxRFFYbERSU3hKUVhwRFJpeEZRWGxEVVN4WlFYcERVaXhGUVhsRGMwSXNTVUY2UTNSQ0xFVkJlVU0wUWp0QlFVTTNRanRCUVVOQkxHOUNRVUZKTEVOQlFVTXNTVUZCUkN4SlFVRlRMRTlCUVU4c1NVRkJVQ3hMUVVGblFpeFJRVUY2UWl4SlFVRnhReXhQUVVGUExFbEJRVkFzUzBGQlowSXNWVUZCZWtRc1JVRkJjVVU3UVVGRGFrVXNkMEpCUVVrc1NVRkJTaXhEUVVGVExFdEJRVlFzUTBGQlpTeEpRVUZtTEVOQlFXOUNMRFpFUVVGd1FpeEZRVUZ0Uml4VFFVRnVSanRCUVVOQkxESkNRVUZQTEV0QlFWQTdRVUZEU0RzN1FVRkZSRHRCUVVOQkxHOUNRVUZKTEV0QlFVc3NTMEZCVEN4RFFVRlhMRTlCUVZnc1EwRkJiVUlzU1VGQmJrSXNRMEZCU2l4RlFVRTRRanRCUVVNeFFpeDNRa0ZCU1N4SlFVRktMRU5CUVZNc1MwRkJWQ3hEUVVGbExFbEJRV1lzUTBGQmIwSXNOa0pCUVRaQ0xFbEJRVGRDTEVkQlFXOURMSFZEUVVGNFJEdEJRVU5CTERKQ1FVRlBMRXRCUVZBN1FVRkRTRHM3UVVGRlJEdEJRVU5CTEhGQ1FVRkxMRXRCUVV3c1EwRkJWeXhQUVVGWUxFTkJRVzFDTEVsQlFXNUNMRWxCUVRKQ08wRkJRM1pDTERCQ1FVRk5MRWxCUkdsQ08wRkJSWFpDTEd0RFFVRmpPMEZCUmxNc2FVSkJRVE5DTzBGQlNVZzdPMEZCUlVRN096czdPenM3T3pzN096czdRVUUzUkVzN1FVRkJRVHRCUVVGQkxHMURRWGxGWjBJN1FVRkJRVHM3UVVGQlFTeHZRa0ZCYUVJc1QwRkJaMElzZFVWQlFVNHNTVUZCVFRzN1FVRkRha0k3UVVGRFFTeHZRa0ZCU1N4RFFVRkRMRXRCUVVzc1UwRkJWaXhGUVVGeFFqdEJRVU5xUWl3d1FrRkJUU3hKUVVGSkxFdEJRVW9zUTBGQlZTeDVSVUZCVml4RFFVRk9PMEZCUTBnN08wRkJSVVE3UVVGRFFTeHZRa0ZCU1N4WlFVRlpMRk5CUVZvc1NVRkJlVUlzV1VGQldTeEpRVUY2UXl4RlFVRXJRenRCUVVNelF5dzRRa0ZCVlN4RlFVRkZMRTFCUVVZc1EwRkJWanRCUVVOSU96dEJRVVZFTEc5Q1FVRk5MRmxCUVZrc1ZVRkJWU3hMUVVGTExGTkJRVXdzUTBGQlpTeEpRVUY2UWl4SFFVRm5ReXhIUVVGb1F5eEhRVUZ6UXl4TFFVRkxMRk5CUVRkRU8wRkJRMEVzYjBKQlFVMHNiMEpCUVc5Q0xFVkJRVVVzVVVGQlJpeEZRVUV4UWp0QlFVTkJMRzlDUVVGTkxIRkNRVUZ4UWl4RlFVRXpRanM3UVVGRlFTeDNRa0ZEU3l4TlFVUk1MRU5CUTFrc1RVRkJUU3hUUVVGT0xFZEJRV3RDTEVkQlJEbENMRVZCUlVzc1IwRkdUQ3hEUVVWVExGRkJRVkVzU1VGQlVpeERRVUZoTEUxQlFVMHNVMEZCVGl4SFFVRnJRaXhIUVVFdlFpeERRVVpVTEVWQlIwc3NTVUZJVEN4RFFVZFZMRlZCUVVNc1MwRkJSQ3hGUVVGUkxFOUJRVklzUlVGQmIwSTdRVUZEZEVJc2QwSkJRVTBzVjBGQlZ5eEZRVUZGTEU5QlFVWXNRMEZCYWtJN1FVRkRRU3gzUWtGQlRTeFZRVUZWTEZOQlFWTXNTVUZCVkN4RFFVRmpMRk5CUVdRc1EwRkJhRUk3TzBGQlJVRXNOa0pCUVZNc1ZVRkJWQ3hEUVVGdlFpeFRRVUZ3UWpzN1FVRkZRU3h6UWtGQlJTeEpRVUZHTEVOQlFVOHNVVUZCVVN4UFFVRlNMRU5CUVdkQ0xITkNRVUZvUWl4RlFVRjNReXhIUVVGNFF5eEZRVUUyUXl4SlFVRTNReXhIUVVGdlJDeExRVUZ3UkN4RFFVRXdSQ3hIUVVFeFJDeERRVUZRTEVWQlFYVkZMRlZCUVVNc1MwRkJSQ3hGUVVGUkxFbEJRVklzUlVGQmFVSTdRVUZEY0VZc05FSkJRVWtzVTBGQlV5eEZRVUZpTEVWQlFXbENPMEZCUTJJc2JVTkJRVThzU1VGQlVEdEJRVU5JT3p0QlFVVkVMRFJDUVVGTkxGZEJRVmNzUlVGQlJTeFJRVUZHTEVWQlFXcENPMEZCUTBFc01rTkJRVzFDTEVsQlFXNUNMRU5CUVhkQ0xGRkJRWGhDT3p0QlFVVkJMRFJDUVVGSkxFbEJRVW9zUTBGQlV5eGhRVUZVTEVOQlEwc3NTVUZFVEN4RFFVTlZMRkZCUkZZc1JVRkRiMElzU1VGRWNFSXNSVUZETUVJc1MwRkVNVUlzUlVGRlN5eEpRVVpNTEVOQlJWVXNWVUZCUXl4TlFVRkVPMEZCUVVFc2JVTkJRVmtzVDBGQlR5eEpRVUZRTEVOQlFWa3NVVUZCV2l4RFFVRmFPMEZCUVVFc2VVSkJSbFlzUlVGSFN5eEpRVWhNTEVOQlIxVXNWVUZCUXl4TFFVRkVMRVZCUVZjN1FVRkRZaXh4UTBGQlV5eE5RVUZVTzBGQlEwRTdRVUZEUVN4blEwRkJTU3hKUVVGS0xFTkJRVk1zUzBGQlZDeERRVUZsTEV0QlFXWXNRMEZCY1VJc05FSkJRVFJDTEVsQlFXcEVMRVZCUVhWRUxFdEJRWFpFTzBGQlEwZ3NlVUpCVUV3N1FVRlJTQ3h4UWtGb1FrUTdRVUZwUWtnc2FVSkJNVUpNT3p0QlFUUkNRVHRCUVVOQkxHdENRVUZGTEVsQlFVWXNRMEZCVHl4TFFVRlFMRU5CUVdFc1UwRkJZaXhGUVVGM1FpeHJRa0ZCZUVJc1JVRkJORU1zVFVGQk5VTXNRMEZCYlVRN1FVRkJRU3d5UWtGQlRTeHJRa0ZCYTBJc1QwRkJiRUlzUlVGQlRqdEJRVUZCTEdsQ1FVRnVSRHM3UVVGRlFTeDFRa0ZCVHl4dFFrRkJiVUlzVFVGQmJrSXNSMEZCTkVJc2EwSkJRV3RDTEU5QlFXeENMRVZCUVRWQ0xFZEJRVEJFTEd0Q1FVRnJRaXhQUVVGc1FpeEZRVUZxUlR0QlFVTklPMEZCZUVoSk96dEJRVUZCTzBGQlFVRTdPMEZCTWtoVUxGRkJRVWtzV1VGQlNpeERRVUZwUWl4VlFVRnFRaXhIUVVFNFFpeFZRVUU1UWp0QlFVTklMRU5CTlVoRU96czdPenM3T3pzN1FVTldRVHM3T3pzN096czdPenRCUVZWQkxFTkJRVU1zV1VGQldUczdRVUZGVkRzN1FVRkZRVHM3T3pzN096czdRVUZLVXl4UlFWZElMRmRCV0VjN1FVRlpURHM3T3pzN08wRkJUVUVzTmtKQlFWa3NTVUZCV2l4RlFVRnJRaXhSUVVGc1FpeEZRVUUwUWp0QlFVRkJPenRCUVVONFFpeHBRa0ZCU3l4SlFVRk1MRWRCUVZrc1NVRkJXanRCUVVOQkxHbENRVUZMTEZGQlFVd3NSMEZCWjBJc1VVRkJhRUk3UVVGRFFTeHBRa0ZCU3l4TFFVRk1MRWRCUVdFc1NVRkJZanRCUVVOQkxHbENRVUZMTEZOQlFVd3NSMEZCYVVJc1UwRkJVeXhGUVVGVUxFTkJRVmtzZVVKQlFWb3NRMEZCYWtJN1FVRkRRU3hwUWtGQlN5eEpRVUZNTzBGQlEwZzdPMEZCUlVRN096czdPMEZCTVVKTE8wRkJRVUU3UVVGQlFTeHRRMEUyUWtVN1FVRkJRVHM3UVVGRFNDeHhRa0ZCU3l4UlFVRk1MRU5CUVdNc1JVRkJaQ3hEUVVGcFFpeFJRVUZxUWl4RlFVRXlRaXhaUVVGTk8wRkJRemRDTERCQ1FVRkxMRWRCUVV3N1FVRkRTQ3hwUWtGR1JEdEJRVWRJT3p0QlFVVkVPenM3T3pzN1FVRnVRMHM3UVVGQlFUdEJRVUZCTEd0RFFYZERRenRCUVVOR0xIRkNRVUZMTEV0QlFVd3NSMEZCWVN4TFFVRkxMRk5CUVV3c1IwRkJhVUlzUzBGQlN5eFJRVUZNTEVOQlFXTXNSMEZCWkN4RlFVRnFRaXhIUVVGMVF5eExRVUZMTEZGQlFVd3NRMEZCWXl4SlFVRmtMRVZCUVhCRU96dEJRVVZCTEc5Q1FVRkpMRXRCUVVzc1VVRkJUQ3hEUVVGakxFVkJRV1FzUTBGQmFVSXNWMEZCYWtJc1MwRkJhVU1zUzBGQlN5eFJRVUZNTEVOQlFXTXNSVUZCWkN4RFFVRnBRaXhSUVVGcVFpeERRVUZ5UXl4RlFVRnBSVHRCUVVNM1JDeDVRa0ZCU3l4TFFVRk1MRWRCUVdFc1MwRkJTeXhSUVVGTUxFTkJRV01zU1VGQlpDeERRVUZ0UWl4VFFVRnVRaXhEUVVGaU8wRkJRMGc3TzBGQlJVUXNkVUpCUVU4c1MwRkJTeXhMUVVGYU8wRkJRMGc3TzBGQlJVUTdPenM3T3p0QlFXeEVTenRCUVVGQk8wRkJRVUVzWjBOQmRVUkVMRXRCZGtSRExFVkJkVVJOTzBGQlExQXNjVUpCUVVzc1MwRkJUQ3hIUVVGaExFdEJRV0k3TzBGQlJVRXNiMEpCUVVrc1MwRkJTeXhUUVVGVUxFVkJRVzlDTzBGQlEyaENMSGxDUVVGTExGRkJRVXdzUTBGQll5eEhRVUZrTEVOQlFXdENMRXRCUVd4Q08wRkJRMGdzYVVKQlJrUXNUVUZGVHp0QlFVTklMSGxDUVVGTExGRkJRVXdzUTBGQll5eEpRVUZrTEVOQlFXMUNMRXRCUVc1Q08wRkJRMGc3UVVGRFNqdEJRUzlFU1RzN1FVRkJRVHRCUVVGQk96dEJRV3RGVkN4UlFVRkpMRmxCUVVvc1EwRkJhVUlzVjBGQmFrSXNSMEZCSzBJc1YwRkJMMEk3UVVGRFNDeERRVzVGUkRzN096czdPenM3TzBGRFZrRTdPenM3T3pzN096czdRVUZWUVN4RFFVRkRMRmxCUVZrN08wRkJSVlE3TzBGQlJVRTdPenM3T3pzN08wRkJTbE1zVVVGWFNDeE5RVmhITzBGQldVdzdPenM3T3pzN1FVRlBRU3gzUWtGQldTeFJRVUZhTEVWQlFYTkNMRWxCUVhSQ0xFVkJRVFJDTEZWQlFUVkNMRVZCUVhkRE8wRkJRVUU3TzBGQlEzQkRMR2xDUVVGTExGRkJRVXdzUjBGQlowSXNVVUZCYUVJN1FVRkRRU3hwUWtGQlN5eEpRVUZNTEVkQlFWa3NTVUZCV2p0QlFVTkJMR2xDUVVGTExGVkJRVXdzUjBGQmEwSXNWVUZCYkVJN1FVRkRTRHM3UVVGRlJEczdPenM3T3pzN096dEJRWHBDU3p0QlFVRkJPMEZCUVVFc2FVTkJhVU5CTEd0Q1FXcERRU3hGUVdsRGIwSTdRVUZCUVRzN1FVRkRja0k3UVVGRFFTeHZRa0ZCVFN4VFFVRlRMRXRCUVVzc1ZVRkJUQ3hEUVVGblFpeExRVUZvUWl4RFFVRnpRaXhQUVVGMFFpeERRVUU0UWl4TFFVRkxMRWxCUVc1RExFTkJRV1k3UVVGRFFTeHZRa0ZCU1N4VlFVRlZMRWxCUVdRN08wRkJSVUVzYjBKQlFVazdRVUZEUVN4M1FrRkJTU3hEUVVGRExFMUJRVXdzUlVGQllUdEJRVU5VTERoQ1FVRk5MRWxCUVVrc1MwRkJTaXhqUVVGeFFpeExRVUZMTEVsQlFURkNMRzFFUVVGT08wRkJRMGc3TzBGQlJVUXNkMEpCUVUwc1QwRkJUeXhMUVVGTExHTkJRVXdzUlVGQllqdEJRVU5CTEhkQ1FVRk5MRmRCUVZjc1QwRkJUeXhKUVVGUUxFTkJRVmtzU1VGQldpeERRVUZwUWl4TFFVRkxMRkZCUVhSQ0xFVkJRV2RETEVsQlFXaERMRU5CUVdwQ096dEJRVVZCTzBGQlEwRTdRVUZEUVN4M1FrRkJUU3hQUVVGUExGTkJRVkFzU1VGQlR5eEhRVUZOTzBGQlEyWXNPRUpCUVVzc1VVRkJUQ3hEUVVGakxFOUJRV1FzUTBGQmMwSXNkMEpCUVhSQ0xFVkJRV2RFTEVOQlFVTXNSVUZCUXl4UlFVRlJMRTFCUVVzc1NVRkJaQ3hGUVVGRUxFTkJRV2hFTzBGQlEwRXNORUpCUVVrc1NVRkJTaXhEUVVGVExFdEJRVlFzUTBGQlpTeEpRVUZtTEdOQlFTdENMRTFCUVVzc1NVRkJjRU03UVVGRFFTd3lRMEZCYlVJc1QwRkJia0k3UVVGRFFTeHhRMEZCWVN4UFFVRmlPMEZCUTBnc2NVSkJURVE3TzBGQlQwRTdRVUZEUVN4NVFrRkJTeXhsUVVGTUxFTkJRWEZDTEZGQlFYSkNMRVZCUTBzc1NVRkVUQ3hEUVVOVkxGbEJRVTA3UVVGRFVqdEJRVU5CTEd0RFFVRlZMRmRCUVZjc1dVRkJUVHRCUVVOMlFpeG5RMEZCU1N4SlFVRktMRU5CUVZNc1MwRkJWQ3hEUVVGbExFbEJRV1lzUTBGQmIwSXNjVVJCUVhGRUxFMUJRVXNzU1VGQk9VVTdRVUZEUVN3clEwRkJiVUlzVFVGQmJrSTdRVUZEU0N4NVFrRklVeXhGUVVkUUxFdEJTRThzUTBGQlZqczdRVUZMUVN3MFFrRkJTU3hKUVVGS0xFTkJRVk1zUjBGQlZDeERRVUZoTEdOQlFXSXNRMEZCTkVJc1VVRkJOVUk3UVVGRFFTeHBRMEZCVXl4SlFVRlVMRU5CUVdNc1NVRkJaRHRCUVVOSUxIRkNRVlpNTEVWQlYwc3NTVUZZVEN4RFFWZFZMRlZCUVVNc1MwRkJSQ3hGUVVGWE8wRkJRMklzTWtOQlFXMUNMRTFCUVc1Q08wRkJRMEVzTkVKQlFVa3NTVUZCU2l4RFFVRlRMRXRCUVZRc1EwRkJaU3hMUVVGbUxFTkJRWEZDTEhGRFFVRnlRaXhGUVVFMFJDeExRVUUxUkR0QlFVTklMSEZDUVdSTU8wRkJaVWdzYVVKQmFrTkVMRU5CYVVORkxFOUJRVThzVTBGQlVDeEZRVUZyUWp0QlFVTm9RaXgxUTBGQmJVSXNUVUZCYmtJN1FVRkRRU3gzUWtGQlNTeEpRVUZLTEVOQlFWTXNTMEZCVkN4RFFVRmxMRXRCUVdZc1owTkJRV3RFTEV0QlFVc3NTVUZCZGtRc1UwRkJhVVVzVTBGQmFrVTdRVUZEUVN4elFrRkJSU3hOUVVGR0xFVkJRVlVzVDBGQlZpeERRVUZyUWl4UFFVRnNRaXhGUVVFeVFpeERRVUZETEZOQlFVUXNRMEZCTTBJc1JVRklaMElzUTBGSGVVSTdRVUZETlVNN08wRkJSVVFzZFVKQlFVOHNiVUpCUVcxQ0xFOUJRVzVDTEVWQlFWQTdRVUZEU0RzN1FVRkZSRHM3T3pzN096czdRVUZvUmtzN1FVRkJRVHRCUVVGQkxEWkRRWFZHV1R0QlFVRkJPenRCUVVOaUxHOUNRVUZOTEU5QlFVOHNSVUZCWWpzN1FVRkZRU3hyUWtGQlJTeEpRVUZHTEVOQlFVOHNTMEZCU3l4UlFVRk1MRU5CUVdNc1NVRkJaQ3hGUVVGUUxFVkJRVFpDTEZWQlFVTXNTVUZCUkN4RlFVRlBMRXRCUVZBc1JVRkJhVUk3UVVGRE1VTXNkMEpCUVVrc1MwRkJTeXhQUVVGTUxFTkJRV0VzVDBGQlN5eEpRVUZzUWl4TlFVRTBRaXhEUVVFMVFpeEpRVUZwUXl4TFFVRkxMRTlCUVV3c1EwRkJZU3hQUVVGTExFbEJRVXdzUTBGQlZTeFhRVUZXTEVWQlFXSXNUVUZCTUVNc1EwRkJMMFVzUlVGQmEwWTdRVUZET1VVc05FSkJRVWtzVFVGQlRTeExRVUZMTEUxQlFVd3NRMEZCV1N4UFFVRkxMRWxCUVV3c1EwRkJWU3hOUVVGMFFpeERRVUZXTzBGQlEwRXNPRUpCUVUwc1NVRkJTU3hOUVVGS0xFTkJRVmNzUTBGQldDeEZRVUZqTEVOQlFXUXNSVUZCYVVJc1YwRkJha0lzUzBGQmFVTXNTVUZCU1N4TlFVRktMRU5CUVZjc1EwRkJXQ3hEUVVGMlF6dEJRVU5CTERaQ1FVRkxMRWRCUVV3c1NVRkJXU3hMUVVGYU8wRkJRMEU3UVVGRFFTdzBRa0ZCVFN4bFFVRmxMRWxCUVVrc1QwRkJTaXhEUVVGWkxHbENRVUZhTEVWQlFTdENMRTlCUVM5Q0xFVkJRWGRETEZkQlFYaERMRVZCUVhKQ08wRkJRMEVzSzBKQlFVc3NVVUZCVEN4RFFVRmpMRlZCUVdRc1YwRkJhVU1zVDBGQlN5eEpRVUYwUXl4VFFVRTRReXhaUVVFNVF6dEJRVU5JTzBGQlEwb3NhVUpCVkVRN08wRkJWMEVzZFVKQlFVOHNTVUZCVUR0QlFVTklPenRCUVVWRU96czdPenM3T3pzN08wRkJlRWRMTzBGQlFVRTdRVUZCUVN3MFEwRnBTRmNzVVVGcVNGZ3NSVUZwU0hGQ08wRkJRM1JDTEc5Q1FVRk5MRmRCUVZjc1JVRkJSU3hSUVVGR0xFVkJRV3BDTzBGQlEwRXNiMEpCUVUwc2NVSkJRWEZDTEVWQlFUTkNPenRCUVVWQkxHOUNRVUZKTzBGQlEwRXNkMEpCUVVrc1UwRkJVeXhMUVVGaUxFVkJRVzlDTzBGQlEyaENMREJDUVVGRkxFbEJRVVlzUTBGQlR5eFRRVUZUTEV0QlFXaENMRVZCUVhWQ0xGVkJRVlVzUzBGQlZpeEZRVUZwUWl4SFFVRnFRaXhGUVVGelFqdEJRVU42UXl4blEwRkJUU3huUWtGQlowSXNSVUZCUlN4UlFVRkdMRVZCUVhSQ08wRkJRMEVzSzBOQlFXMUNMRWxCUVc1Q0xFTkJRWGRDTEdGQlFYaENPMEZCUTBFc09FSkJRVVVzVDBGQlJpeERRVUZWTEVkQlFWWXNSVUZEU3l4SlFVUk1MRU5CUTFVc1ZVRkJReXhSUVVGRUxFVkJRV003UVVGRGFFSXNlVU5CUVZNc1MwRkJWQ3hEUVVGbExFdEJRV1lzU1VGQmQwSXNVVUZCZUVJN1FVRkRRU3c0UTBGQll5eFBRVUZrTEVOQlFYTkNMRkZCUVhSQ08wRkJRMGdzTmtKQlNrd3NSVUZMU3l4SlFVeE1MRU5CUzFVc1ZVRkJReXhMUVVGRUxFVkJRVmM3UVVGRFlpdzRRMEZCWXl4TlFVRmtMRU5CUVhGQ0xFdEJRWEpDTzBGQlEwZ3NOa0pCVUV3N1FVRlJTQ3g1UWtGWVJEdEJRVmxJT3p0QlFVVkVMSGRDUVVGSkxGTkJRVk1zU1VGQllpeEZRVUZ0UWp0QlFVTm1MREJDUVVGRkxFbEJRVVlzUTBGQlR5eFRRVUZUTEVsQlFXaENMRVZCUVhOQ0xGVkJRVlVzUzBGQlZpeEZRVUZwUWl4SFFVRnFRaXhGUVVGelFqdEJRVU40UXl4blEwRkJUU3hsUVVGbExFVkJRVVVzVVVGQlJpeEZRVUZ5UWp0QlFVTkJMQ3REUVVGdFFpeEpRVUZ1UWl4RFFVRjNRaXhaUVVGNFFqdEJRVU5CTERoQ1FVRkZMRWRCUVVZc1EwRkJUU3hIUVVGT0xFVkJRMHNzU1VGRVRDeERRVU5WTEZWQlFVTXNVVUZCUkN4RlFVRmpPMEZCUTJoQ0xIbERRVUZUTEVsQlFWUXNRMEZCWXl4TFFVRmtMRWxCUVhWQ0xGRkJRWFpDTzBGQlEwRXNOa05CUVdFc1QwRkJZaXhEUVVGeFFpeFJRVUZ5UWp0QlFVTklMRFpDUVVwTUxFVkJTMHNzU1VGTVRDeERRVXRWTEZWQlFVTXNTMEZCUkN4RlFVRlhPMEZCUTJJc05rTkJRV0VzVFVGQllpeERRVUZ2UWl4TFFVRndRanRCUVVOSUxEWkNRVkJNTzBGQlVVZ3NlVUpCV0VRN1FVRlpTRHM3UVVGRlJDeDNRa0ZCU1N4VFFVRlRMRkZCUVdJc1JVRkJkVUk3UVVGRGJrSXNOa0pCUVVzc1NVRkJTU3hKUVVGVUxFbEJRV2xDTEZOQlFWTXNVVUZCTVVJc1JVRkJiME03UVVGRGFFTXNaME5CUVUwc1YwRkJWeXhUUVVGVExGRkJRVlFzUTBGQmEwSXNTVUZCYkVJc1EwRkJha0k3UVVGRFFTeHhRMEZCVXl4UlFVRlVMRU5CUVd0Q0xFbEJRV3hDTEVsQlFUQkNMRWxCUVVrc1NVRkJTU3haUVVGS0xFTkJRV2xDTEZkQlFYSkNMRU5CUVdsRExFbEJRV3BETEVWQlFYVkRMRkZCUVhaRExFTkJRVEZDTzBGQlEwZzdRVUZEU2pzN1FVRkZSQ3h6UWtGQlJTeEpRVUZHTEVOQlFVOHNTMEZCVUN4RFFVRmhMRk5CUVdJc1JVRkJkMElzYTBKQlFYaENMRVZCUTBzc1NVRkVUQ3hEUVVOVkxGTkJRVk1zVDBGRWJrSXNSVUZGU3l4SlFVWk1MRU5CUlZVc1ZVRkJReXhMUVVGRUxFVkJRVmM3UVVGRFlpeHBRMEZCVXl4TlFVRlVMRU5CUVdkQ0xFbEJRVWtzUzBGQlNpeHRRMEZCTUVNc1UwRkJVeXhKUVVGdVJDeFRRVUUyUkN4TFFVRTNSQ3hEUVVGb1FqdEJRVU5JTEhGQ1FVcE1PMEZCUzBnc2FVSkJNME5FTEVOQk1rTkZMRTlCUVU4c1UwRkJVQ3hGUVVGclFqdEJRVU5vUWl3MlFrRkJVeXhOUVVGVUxFTkJRV2RDTEZOQlFXaENPMEZCUTBFc2QwSkJRVWtzU1VGQlNpeERRVUZUTEV0QlFWUXNRMEZCWlN4TFFVRm1MSE5EUVVGM1JDeExRVUZMTEVsQlFUZEVMRk5CUVhWRkxGTkJRWFpGTzBGQlEwRXNjMEpCUVVVc1RVRkJSaXhGUVVGVkxFOUJRVllzUTBGQmEwSXNUMEZCYkVJc1JVRkJNa0lzUTBGQlF5eFRRVUZFTEVOQlFUTkNMRVZCU0dkQ0xFTkJSM2xDTzBGQlF6VkRPenRCUVVWRUxIVkNRVUZQTEZOQlFWTXNUMEZCVkN4RlFVRlFPMEZCUTBnN1FVRjJTMGs3TzBGQlFVRTdRVUZCUVRzN1FVRXdTMVFzVVVGQlNTeFpRVUZLTEVOQlFXbENMRTFCUVdwQ0xFZEJRVEJDTEUxQlFURkNPMEZCUTBnc1EwRXpTMFE3T3pzN096czdPenRCUTFaQk96czdPenM3T3pzN08wRkJWVUVzUTBGQlF5eFpRVUZaT3p0QlFVVlVPenRCUVVWQk96czdPenM3T3pzN1FVRktVeXhSUVZsSUxGTkJXa2M3UVVGaFREczdPenM3T3p0QlFVOUJMREpDUVVGWkxFbEJRVm9zUlVGQmEwSXNUVUZCYkVJc1JVRkJNRUlzVjBGQk1VSXNSVUZCZFVNN1FVRkJRVHM3UVVGRGJrTXNhVUpCUVVzc1NVRkJUQ3hIUVVGWkxFbEJRVm83UVVGRFFTeHBRa0ZCU3l4TlFVRk1MRWRCUVdNc1RVRkJaRHRCUVVOQkxHbENRVUZMTEZkQlFVd3NSMEZCYlVJc1YwRkJia0lzUTBGSWJVTXNRMEZIU0R0QlFVTnVRenM3UVVGRlJEczdPenM3T3pzN096dEJRVEZDU3p0QlFVRkJPMEZCUVVFc2JVTkJhME5GTzBGQlEwZ3NiMEpCUVUwc2NVSkJRWEZDTEVWQlFUTkNPenRCUVVSSE8wRkJRVUU3UVVGQlFUczdRVUZCUVR0QlFVZElMSGxEUVVGMVFpeExRVUZMTEZkQlFUVkNMRGhJUVVGNVF6dEJRVUZCTERSQ1FVRm9ReXhWUVVGblF6czdRVUZEY2tNc05rSkJRVXNzVjBGQlZ5eEpRVUZvUWl4SlFVRjNRaXhKUVVGSkxFbEJRVWtzV1VGQlNpeERRVUZwUWl4VlFVRnlRaXhEUVVGblF5eFhRVUZYTEVsQlFUTkRMRVZCUVdsRUxGZEJRVmNzVTBGQk5VUXNSVUZCZFVVc1NVRkJka1VzUTBGQmVFSTdRVUZEUVN3MFFrRkJUU3hYUVVGWExFdEJRVXNzVjBGQlZ5eEpRVUZvUWl4RlFVRnpRaXhKUVVGMFFpeEZRVUZxUWp0QlFVTkJMREpEUVVGdFFpeEpRVUZ1UWl4RFFVRjNRaXhSUVVGNFFqdEJRVU5JTzBGQlVFVTdRVUZCUVR0QlFVRkJPMEZCUVVFN1FVRkJRVHRCUVVGQk8wRkJRVUU3UVVGQlFUdEJRVUZCTzBGQlFVRTdRVUZCUVR0QlFVRkJPMEZCUVVFN1FVRkJRVHM3UVVGVFNDeDFRa0ZCVHl4dFFrRkJiVUlzVFVGQmJrSXNSMEZCTkVJc1JVRkJSU3hKUVVGR0xFTkJRVThzUzBGQlVDeERRVUZoTEZOQlFXSXNSVUZCZDBJc2EwSkJRWGhDTEVWQlFUUkRMRTlCUVRWRExFVkJRVFZDTEVkQlFXOUdMRVZCUVVVc1VVRkJSaXhIUVVGaExFOUJRV0lzUlVGQk0wWTdRVUZEU0R0QlFUVkRTVHM3UVVGQlFUdEJRVUZCT3p0QlFTdERWQ3hSUVVGSkxGbEJRVW9zUTBGQmFVSXNVMEZCYWtJc1IwRkJOa0lzVTBGQk4wSTdRVUZEU0N4RFFXaEVSRHM3T3pzN1FVTldRVHM3T3pzN096czdPenRCUVZWQk96czdPenM3T3pzN1FVRlRRU3hUUVVGVExHZENRVUZVTEVOQlFUQkNMR3RDUVVFeFFpeEZRVUU0UXl4WlFVRlpPenRCUVVWMFJEczdRVUZGUVN4UlFVRkpMRWxCUVVrc1NVRkJTaXhEUVVGVExFMUJRVlFzUTBGQlowSXNSMEZCYUVJc1EwRkJiMElzWVVGQmNFSXNUVUZCZFVNc1dVRkJNME1zUlVGQmVVUTdRVUZEY2tRN1FVRkRTRHM3UVVGRlJDeFJRVUZKTEV0QlFVb3NSMEZCV1N4WlFVRlpPMEZCUTNCQ0xGbEJRVTBzSzBKQlEwVXNTVUZCU1N4SlFVRktMRU5CUVZNc1RVRkJWQ3hEUVVGblFpeEhRVUZvUWl4RFFVRnZRaXhUUVVGd1FpeERRVVJHTERnNVEwRkJUanM3UVVGM1FrRXNXVUZCU1N4SlFVRktMRU5CUVZNc1MwRkJWQ3hEUVVGbExFbEJRV1lzUTBGQmIwSXNTVUZCY0VJN1FVRkRTQ3hMUVRGQ1JEdEJRVEpDU0N4RFFXNURSRHM3T3pzN1FVTnVRa0U3T3pzN096czdPenM3UVVGVlFTeEpRVUZKTEVsQlFVb3NRMEZCVXl4TlFVRlVMRWRCUVd0Q0xFbEJRVWtzU1VGQlNpeERRVUZUTEUxQlFWUXNTVUZCYlVJc1JVRkJja003TzBGQlJVRTdPenM3T3pzN096czdPenM3UVVGaFF5eFhRVUZWTEU5QlFWWXNSVUZCYlVJN08wRkJSV2hDT3p0QlFVVkJPMEZCUTBFN1FVRkRRVHM3UVVGRlFTeFJRVUZOTEZOQlFWTTdRVUZEV0RzN096czdRVUZMUVN4cFFrRkJVeXhMUVU1Rk96dEJRVkZZT3pzN096czdPMEZCVDBFc1owSkJRVkVzU1VGbVJ6czdRVUZwUWxnN096czdPenM3T3p0QlFWTkJMR2xDUVVGVExFbEJNVUpGT3p0QlFUUkNXRHM3T3pzN096dEJRVTlCTEc5Q1FVRlpMRWxCYmtORU96dEJRWEZEV0RzN096czdPenM3TzBGQlUwRXNjVUpCUVdFc1NVRTVRMFk3TzBGQlowUllPenM3T3pzN08wRkJUMEVzYlVKQlFWY3NTVUYyUkVFN08wRkJlVVJZT3pzN096czdPenM3UVVGVFFTeHhRa0ZCWVN4WlFXeEZSanM3UVVGdlJWZzdPenM3T3pzN096dEJRVk5CTEhOQ1FVRmpMRVZCTjBWSU96dEJRU3RGV0RzN096czdPenM3UVVGUlFTeHhRa0ZCWVN4RlFYWkdSanM3UVVGNVJsZzdPenM3TzBGQlMwRXNjMEpCUVdNc1NVRTVSa2c3TzBGQlowZFlPenM3T3pzN1FVRk5RU3hsUVVGUExGRkJkRWRKT3p0QlFYZEhXRHM3T3pzN096czdPMEZCVTBFc2JVSkJRVmNzU1VGcVNFRTdPMEZCYlVoWU96czdPenRCUVV0QkxHMUNRVUZYTEV0QmVFaEJPenRCUVRCSVdEczdPenM3UVVGTFFTeG5Ra0ZCVXl4cFJVRkJhVVVzU1VGQmFrVXNRMEZCYzBVc1ZVRkJWU3hUUVVGb1JpeERRUzlJUlRzN1FVRnBTVmc3T3pzN08wRkJTMEVzWlVGQlV5eHJRa0ZCYTBJc1RVRkJia0lzU1VGQk9FSXNUMEZCVHl4WlFVRnlReXhKUVVGeFJDeFBRVUZQTEdsQ1FVRTNSQ3hIUVVGclJpeEpRVUZzUml4SFFVRjVSaXhMUVhSSmNrWTdPMEZCZDBsWU96czdPenM3TzBGQlQwRXNjVUpCUVdFc01FTkJMMGxHT3p0QlFXbEtXRHM3T3pzN096czdRVUZSUVN4dFFrRkJWeXhGUVhwS1FUczdRVUV5U2xnN096czdPenM3TzBGQlVVRXNiMEpCUVZrc1JVRnVTMFE3TzBGQmNVdFlPenM3T3p0QlFVdEJMR2xDUVVGVExGZEJRVmNzVVVGQlVTeFpRVUZ1UWl4SlFVRnRReXhSUVVGUkxGTkJNVXQ2UXpzN1FVRTBTMWc3T3pzN096czdRVUZQUVN4aFFVRkxPMEZCYmt4TkxFdEJRV1k3TzBGQmMweEJPenM3T3p0QlFVdEJMRkZCUVUwc1dVRkJXU3hEUVVOa0xGTkJSR01zUlVGRlpDeFpRVVpqTEVWQlIyUXNZVUZJWXl4RFFVRnNRanM3UVVGTlFUdEJRVU5CTzBGQlEwRTdPMEZCUlVFN096czdPenM3UVVGUFFTeFpRVUZSTEVkQlFWSXNSMEZCWXl4VlFVRlZMRWxCUVZZc1JVRkJaMEk3UVVGRE1VSXNXVUZCU1N4UFFVRlBMRmRCUVZBc1MwRkJkVUlzV1VGQmRrSXNTVUZCZFVNc1ZVRkJWU3hSUVVGV0xFTkJRVzFDTEVsQlFXNUNMRU5CUVRORExFVkJRWEZGTzBGQlEycEZMRzFDUVVGUExFbEJRVkE3UVVGRFNEczdRVUZGUkN4bFFVRlBMRTlCUVU4c1NVRkJVQ3hEUVVGUU8wRkJRMGdzUzBGT1JEczdRVUZSUVRzN096czdPenM3T3pzN1FVRlhRU3haUVVGUkxFbEJRVklzUjBGQlpTeFZRVUZWTEhGQ1FVRldMRVZCUVdsRE8wRkJRelZETEdWQlFVOHNWMEZCVUN4SFFVRnhRaXh6UWtGQmMwSXNWMEZCTTBNN1FVRkRRU3hsUVVGUExFMUJRVkFzUjBGQlowSXNjMEpCUVhOQ0xFMUJRWFJDTEVOQlFUWkNMRTlCUVRkQ0xFTkJRWEZETEUxQlFYSkRMRVZCUVRaRExFVkJRVGRETEVOQlFXaENMRU5CUmpSRExFTkJSWE5DT3p0QlFVVnNSU3haUVVGSkxFOUJRVThzVjBGQlVDeExRVUYxUWl4aFFVRXpRaXhGUVVFd1F6dEJRVU4wUXl4dFFrRkJUeXhUUVVGUUxFZEJRVzFDTEV0QlFXNUNPMEZCUTBFc2JVSkJRVThzVVVGQlVDeEhRVUZyUWl4TFFVRnNRanRCUVVOQkxHMUNRVUZQTEV0QlFWQXNSMEZCWlN4UFFVRm1PMEZCUTBnN08wRkJSVVFzV1VGQlNTeHpRa0ZCYzBJc1UwRkJkRUlzUzBGQmIwTXNVMEZCZUVNc1JVRkJiVVE3UVVGREwwTXNiVUpCUVU4c1UwRkJVQ3hIUVVGdFFpeHpRa0ZCYzBJc1UwRkJkRUlzUTBGQlowTXNUMEZCYUVNc1EwRkJkME1zVFVGQmVFTXNSVUZCWjBRc1JVRkJhRVFzUTBGQmJrSTdRVUZEU0N4VFFVWkVMRTFCUlU4N1FVRkRTQ3h0UWtGQlR5eFRRVUZRTEVkQlFXMUNMRTlCUVU4c1RVRkJVQ3hIUVVGblFpeHBRa0ZCYmtNN1FVRkRTRHM3UVVGRlJDeFpRVUZKTEhOQ1FVRnpRaXhaUVVGMFFpeExRVUYxUXl4VFFVRXpReXhGUVVGelJEdEJRVU5zUkN4dFFrRkJUeXhaUVVGUUxFZEJRWE5DTEhOQ1FVRnpRaXhaUVVFMVF6czdRVUZGUVN4cFFrRkJTeXhKUVVGSkxGZEJRVlFzU1VGQmQwSXNUMEZCVHl4WlFVRXZRaXhGUVVFMlF6dEJRVU42UXl4dlFrRkJTU3hKUVVGS0xFTkJRVk1zU1VGQlZDeERRVUZqTEZWQlFXUXNRMEZCZVVJc1YwRkJla0lzUlVGQmMwTXNUMEZCVHl4WlFVRlFMRU5CUVc5Q0xGZEJRWEJDTEVOQlFYUkRPMEZCUTBnN1FVRkRTanM3UVVGRlJDeFpRVUZKTEhOQ1FVRnpRaXhYUVVGMFFpeExRVUZ6UXl4VFFVRXhReXhGUVVGeFJEdEJRVU5xUkN4dFFrRkJUeXhYUVVGUUxFZEJRWEZDTEhOQ1FVRnpRaXhYUVVFelF6dEJRVU5JTEZOQlJrUXNUVUZGVHp0QlFVTklMRzFDUVVGUExGZEJRVkFzUjBGQmNVSXNRMEZEYWtJc1JVRkJReXhOUVVGTkxHRkJRVkFzUlVGQmMwSXNWMEZCVnl4WlFVRnFReXhGUVVScFFpeEZRVVZxUWl4RlFVRkRMRTFCUVUwc1dVRkJVQ3hGUVVGeFFpeFhRVUZYTEZkQlFXaERMRVZCUm1sQ0xFVkJSMnBDTEVWQlFVTXNUVUZCVFN4VFFVRlFMRVZCUVd0Q0xGZEJRVmNzVVVGQk4wSXNSVUZJYVVJc1EwRkJja0k3UVVGTFNEczdRVUZGUkN4WlFVRkpMSE5DUVVGelFpeFZRVUYwUWl4TFFVRnhReXhUUVVGNlF5eEZRVUZ2UkR0QlFVTm9SQ3h0UWtGQlR5eFZRVUZRTEVkQlFXOUNMSE5DUVVGelFpeFZRVUV4UXp0QlFVTklPenRCUVVWRUxGbEJRVWtzYzBKQlFYTkNMRTlCUVhSQ0xFdEJRV3RETEZOQlFYUkRMRVZCUVdsRU8wRkJRemRETEdkQ1FVRkpMRWxCUVVvc1EwRkJVeXhMUVVGVUxFTkJRV1VzU1VGQlppeERRVUZ2UWl4elJrRkRaQ3d5UWtGRVRqdEJRVVZCTEcxQ1FVRlBMRTlCUVZBc1IwRkJhVUlzYzBKQlFYTkNMRTlCUVhSQ0xFTkJRVGhDTEU5QlFUbENMRU5CUVhORExFMUJRWFJETEVWQlFUaERMRVZCUVRsRExFTkJRV3BDTzBGQlEwRXNiVUpCUVU4c1RVRkJVQ3hIUVVGblFpeFBRVUZQTEUxQlFWQXNTVUZCYVVJc1QwRkJUeXhQUVVGNFF5eERRVW8yUXl4RFFVbEpPMEZCUTNCRU96dEJRVVZFTEZsQlFVa3NjMEpCUVhOQ0xGZEJRWFJDTEV0QlFYTkRMRk5CUVRGRExFVkJRWEZFTzBGQlEycEVMR2RDUVVGSkxFbEJRVW9zUTBGQlV5eExRVUZVTEVOQlFXVXNTVUZCWml4RFFVRnZRaXd3UmtGRFpDd3JRa0ZFVGp0QlFVVkJMRzFDUVVGUExGZEJRVkFzUjBGQmNVSXNjMEpCUVhOQ0xGZEJRVE5ETzBGQlEwZzdPMEZCUlVRc1dVRkJTU3h6UWtGQmMwSXNUVUZCZEVJc1MwRkJhVU1zVTBGQmNrTXNSVUZCWjBRN1FVRkROVU1zYlVKQlFVOHNUVUZCVUN4SFFVRm5RaXh6UWtGQmMwSXNUVUZCZEVNN1FVRkRTRHM3UVVGRlJDeFpRVUZKTEhOQ1FVRnpRaXhaUVVGMFFpeExRVUYxUXl4VFFVRXpReXhGUVVGelJEdEJRVU5zUkN4dFFrRkJUeXhaUVVGUUxFZEJRWE5DTEhOQ1FVRnpRaXhaUVVFMVF6dEJRVU5JT3p0QlFVVkVMRmxCUVVrc1UwRkJVeXhqUVVGVUxFTkJRWGRDTEZOQlFYaENMRTFCUVhWRExFbEJRWFpETEVsQlEwY3NVMEZCVXl4alFVRlVMRU5CUVhkQ0xGTkJRWGhDTEVWQlFXMURMRmxCUVc1RExFTkJRV2RFTEdsQ1FVRm9SQ3hEUVVSUUxFVkJRekpGTzBGQlEzWkZMR3REUVVGelFpeFRRVUYwUWl4SFFVRnJReXhUUVVGVExHTkJRVlFzUTBGQmQwSXNVMEZCZUVJc1JVRkJiVU1zV1VGQmJrTXNRMEZCWjBRc2FVSkJRV2hFTEVOQlFXeERPMEZCUTBnN08wRkJSVVFzV1VGQlNTeHpRa0ZCYzBJc1UwRkJkRUlzUzBGQmIwTXNVMEZCZUVNc1JVRkJiVVE3UVVGREwwTXNiVUpCUVU4c1UwRkJVQ3hIUVVGdFFpeHpRa0ZCYzBJc1UwRkJla003UVVGRFNEczdRVUZGUkN4WlFVRkpMSE5DUVVGelFpeFZRVUYwUWl4TFFVRnhReXhUUVVGNlF5eEZRVUZ2UkR0QlFVTm9SQ3h0UWtGQlR5eFZRVUZRTEVkQlFXOUNMSE5DUVVGelFpeFZRVUV4UXp0QlFVTklPenRCUVVWRUxGbEJRVWtzYzBKQlFYTkNMRk5CUVhSQ0xFdEJRVzlETEZOQlFYaERMRVZCUVcxRU8wRkJReTlETEcxQ1FVRlBMRk5CUVZBc1IwRkJiVUlzYzBKQlFYTkNMRk5CUVhwRE8wRkJRMGc3TzBGQlJVUXNXVUZCU1N4elFrRkJjMElzUjBGQmRFSXNTMEZCT0VJc1UwRkJiRU1zUlVGQk5rTTdRVUZEZWtNc2JVSkJRVThzUjBGQlVDeEhRVUZoTEhOQ1FVRnpRaXhIUVVGdVF6dEJRVU5JT3p0QlFVVkVPMEZCUTBFc1dVRkJUU3h4UWtGQmNVSTdRVUZEZGtJc2JVSkJRVThzV1VGRVowSTdRVUZGZGtJc2FVSkJRVXNzVjBGR2EwSTdRVUZIZGtJc2EwSkJRVTA3UVVGSWFVSXNVMEZCTTBJN08wRkJUVUVzV1VGQlRTeDFRa0ZCZFVJN1FVRkRla0lzYlVKQlFVOHNZVUZFYTBJN1FVRkZla0lzYVVKQlFVc3NWMEZHYjBJN1FVRkhla0lzYTBKQlFVMDdRVUZJYlVJc1UwRkJOMEk3TzBGQlRVRXNaVUZCVHl4WFFVRlFMRWRCUVhOQ0xFOUJRVThzYVVKQlFWSXNSMEZCTmtJc2IwSkJRVGRDTEVkQlFXOUVMR3RDUVVGNlJUczdRVUZGUVR0QlFVTkJMR0ZCUVVzc1NVRkJTU3hMUVVGVUxFbEJRV3RDTEhOQ1FVRnpRaXhSUVVGNFF5eEZRVUZyUkR0QlFVTTVReXhuUWtGQlNTeEpRVUZLTEVOQlFWTXNVVUZCVkN4RFFVRnJRaXhIUVVGc1FpeERRVUZ6UWl4TFFVRjBRaXhGUVVFMlFpeHpRa0ZCYzBJc1VVRkJkRUlzUTBGQkswSXNTMEZCTDBJc1EwRkJOMEk3UVVGRFNEczdRVUZGUkR0QlFVTkJMRmxCUVVrc1NVRkJTaXhEUVVGVExHRkJRVlFzUTBGQmRVSXNTVUZCZGtJN08wRkJSVUU3UVVGRFFTeGxRVUZQTEU5QlFVOHNjVUpCUVdRN1FVRkRTQ3hMUVhwSFJEdEJRVEpIU0N4RFFXeFdRU3hGUVd0V1F5eEpRVUZKTEVsQlFVb3NRMEZCVXl4TlFXeFdWaXhEUVVGRU96czdPenRCUTNwQ1FUczdPenM3T3pzN096dEJRVlZCTEVsQlFVa3NTVUZCU2l4RFFVRlRMRXRCUVZRc1IwRkJhVUlzU1VGQlNTeEpRVUZLTEVOQlFWTXNTMEZCVkN4SlFVRnJRaXhGUVVGdVF6czdRVUZGUVRzN096czdPenM3UVVGUlF5eFhRVUZWTEU5QlFWWXNSVUZCYlVJN1FVRkRhRUk3TzBGQlJVRTdRVUZEUVR0QlFVTkJPenRCUVVWQk96czdPMEZCUjBFc1VVRkJUU3hoUVVGaExFOUJRVzVDT3p0QlFVVkJPenM3UVVGSFFTeFJRVUZOTEZsQlFWa3NUVUZCYkVJN08wRkJSVUU3T3p0QlFVZEJMRkZCUVUwc1YwRkJWeXhMUVVGcVFqczdRVUZGUVRzN08wRkJSMEVzVVVGQlRTeFpRVUZaTEUxQlFXeENPenRCUVVWQk96czdRVUZIUVN4UlFVRk5MR0ZCUVdFc1QwRkJia0k3TzBGQlJVRTdPenRCUVVkQkxGRkJRVTBzWVVGQllTeFBRVUZ1UWpzN1FVRkZRVHM3TzBGQlIwRXNVVUZCVFN4alFVRmpMRkZCUVhCQ096dEJRVVZCT3pzN1FVRkhRU3hSUVVGTkxHTkJRV01zVVVGQmNFSTdPMEZCUlVFN096czdPMEZCUzBFc1VVRkJUU3hUUVVGVExFTkJRMWdzVlVGRVZ5eEZRVVZZTEZOQlJsY3NSVUZIV0N4UlFVaFhMRVZCU1Znc1UwRktWeXhGUVV0WUxGVkJURmNzUlVGTldDeFZRVTVYTEVWQlQxZ3NWMEZRVnl4RlFWRllMRmRCVWxjc1EwRkJaanM3UVVGWFFUdEJRVU5CTzBGQlEwRTdPMEZCUlVFN096czdPenM3UVVGUFFTeGhRVUZUTEhWQ1FVRlVMRWRCUVcxRE8wRkJReTlDTEZsQlFVMHNVMEZCVXl4VFFVRlRMR0ZCUVZRc1EwRkJkVUlzVVVGQmRrSXNRMEZCWmp0QlFVTkJMRmxCUVUwc1ZVRkJWU3hUUVVGVExHRkJRVlFzUTBGQmRVSXNkVUpCUVhaQ0xFTkJRV2hDT3p0QlFVVkJMRmxCUVVrc1QwRkJUeXhWUVVGUUxFbEJRWEZDTEVOQlFVTXNVVUZCVVN4VFFVRlNMRU5CUVd0Q0xGRkJRV3hDTEVOQlFUSkNMR0ZCUVROQ0xFTkJRVEZDTEVWQlFYRkZPMEZCUTJwRkxHZENRVUZOTEUxQlFVMHNVMEZCVXl4aFFVRlVMRU5CUVhWQ0xFdEJRWFpDTEVOQlFWbzdRVUZEUVN4dFFrRkJUeXhOUVVGUUxFZEJRV2RDTEU5QlFVOHNTMEZCVUN4SFFVRmxMRVZCUVM5Q08wRkJRMEVzWjBKQlFVMHNUVUZCVFN4UFFVRlBMRlZCUVZBc1EwRkJhMElzU1VGQmJFSXNRMEZCV2p0QlFVTkJMR2RDUVVGSkxFMUJRVW9zUjBGQllTeFpRVUZaTzBGQlFVVTdRVUZEZGtJc2IwSkJRVWtzVTBGQlNpeERRVUZqTEVsQlFXUXNSVUZCYjBJc1EwRkJjRUlzUlVGQmRVSXNRMEZCZGtJN1FVRkRRU3h2UWtGQlNTeFhRVUZLTEVkQlFXdENMRWxCUVd4Q08wRkJRMEVzYjBKQlFVa3NVMEZCU2l4SFFVRm5RaXhUUVVGb1FqdEJRVU5CTEc5Q1FVRkpMRWxCUVVvc1EwRkJVeXhEUVVGVUxFVkJRVmtzUTBGQldpeEZRVUZsTEVWQlFXWXNSVUZCYlVJc1JVRkJia0k3UVVGRFFTeHZRa0ZCU1N4SlFVRktPMEZCUTBFc2QwSkJRVkVzU1VGQlVpeEhRVUZsTEU5QlFVOHNVMEZCVUN4RFFVRnBRaXhYUVVGcVFpeERRVUZtTzBGQlEwRXNkMEpCUVZFc1UwRkJVaXhKUVVGeFFpeGhRVUZ5UWp0QlFVTklMR0ZCVWtRN1FVRlRRU3huUWtGQlNTeEhRVUZLTEVkQlFWVXNVVUZCVVN4SlFVRnNRanRCUVVOSU8wRkJRMG83TzBGQlJVUTdPenM3TzBGQlMwRXNZVUZCVXl4dFFrRkJWQ3hIUVVFclFqdEJRVU16UWl4WlFVRkpMRWxCUVVrc1NVRkJTaXhEUVVGVExFMUJRVlFzUTBGQlowSXNSMEZCYUVJc1EwRkJiMElzWVVGQmNFSXNUVUZCZFVNc1dVRkJNME1zUlVGQmVVUTdRVUZEY2tRN1FVRkRRU3huUWtGQlNTeEpRVUZKTEVsQlFVb3NRMEZCVXl4TFFVRlVMRXRCUVcxQ0xGTkJRWFpDTEVWQlFXdERPMEZCUXpsQ0xHOUNRVUZKTEVsQlFVb3NRMEZCVXl4TFFVRlVMRU5CUVdVc1MwRkJaaXhEUVVGeFFpeDVRa0ZCY2tJc1JVRkJaMFFzVTBGQmFFUTdRVUZEU0N4aFFVWkVMRTFCUlU4N1FVRkRTQ3gzUWtGQlVTeEhRVUZTTEVOQlFWa3NlVUpCUVZvc1JVRkJkVU1zVTBGQmRrTTdRVUZEU0RzN1FVRkZSRHRCUVVOQkxHZENRVUZOTEZGQlFWRXNaVUZCWkR0QlFVTkJMR2RDUVVGSkxGRkJRVkVzVDBGQlR5eFJRVUZRTEVOQlFXZENMRXRCUVRWQ08wRkJRMEVzWjBKQlFVa3NZVUZCWVN4RFFVRnFRanM3UVVGRlFUdEJRVU5CTEdkQ1FVRkpMRTFCUVUwc1MwRkJUaXhEUVVGWkxFdEJRVm9zVFVGQmRVSXNTVUZCTTBJc1JVRkJhVU03UVVGRE4wSXNOa0pCUVdFc1UwRkJVeXhOUVVGTkxFdEJRVTRzUTBGQldTeExRVUZhTEVWQlFXMUNMRU5CUVc1Q0xFTkJRVlFzUlVGQlowTXNSVUZCYUVNc1NVRkJjME1zUTBGQmJrUTdRVUZEUVN4M1FrRkJVU3hOUVVGTkxFOUJRVTRzUTBGQll5eExRVUZrTEVWQlFYRkNMRVZCUVhKQ0xFTkJRVkk3UVVGRFNEczdRVUZGUkR0QlFVTkJMRzlDUVVGUkxGRkJRVkVzVlVGQlVpeEhRVUZ4UWl4SlFVRnlRaXhIUVVFMFFpeExRVUZ3UXp0QlFVTkJMRzFDUVVGUExGRkJRVkFzUTBGQlowSXNTMEZCYUVJc1IwRkJkMElzUzBGQmVFSTdPMEZCUlVFN1FVRkRRVHRCUVVOSU96dEJRVVZFTEdWQlFVOHNTVUZCVUR0QlFVTklPenRCUVVWRU96czdPenM3T3p0QlFWRkJMR0ZCUVZNc1VVRkJWQ3hEUVVGclFpeE5RVUZzUWl4RlFVRXdRaXhKUVVFeFFpeEZRVUZuUXp0QlFVTTFRaXhaUVVGTkxHdENRVUZyUWl4UFFVRlBMRTlCUVZBc1EwRkJaU3hOUVVGbUxFTkJRWGhDTzBGQlEwRXNXVUZCVFN4clFrRkJhMElzVDBGQlR5eFBRVUZRTEVOQlFXVXNTVUZCU1N4SlFVRktMRU5CUVZNc1RVRkJWQ3hEUVVGblFpeEhRVUZvUWl4RFFVRnZRaXhQUVVGd1FpeERRVUZtTEVOQlFYaENPMEZCUTBFc1dVRkJTU3huUWtGQlowSXNTVUZCY0VJN08wRkJSVUVzV1VGQlNTeHRRa0ZCYlVJc1pVRkJka0lzUlVGQmQwTTdRVUZEY0VNc05FSkJRV2RDTEU5QlFVOHNWMEZCVUN4RlFVRm9RanM3UVVGRlFTeHZRa0ZCVVN4aFFVRlNPMEZCUTBrc2NVSkJRVXNzVDBGQlREdEJRVU5KTERCQ1FVRk5MRXRCUVVzc1UwRkJUQ3hEUVVGbExFbEJRV1lzUTBGQlRqdEJRVU5CT3p0QlFVVktMSEZDUVVGTExGRkJRVXc3UVVGRFNTeDNRa0ZCVFN4dlFrRkJiMElzUlVGQlJTeHhRa0ZCUml4RFFVRXhRanM3UVVGRlFTeDNRa0ZCU1N4RFFVRkRMR3RDUVVGclFpeE5RVUYyUWl4RlFVRXJRanRCUVVNelFpd3dRa0ZCUlN4VFFVRkdMRVZCUTBzc1VVRkVUQ3hEUVVOakxHOUNRVVJrTEVWQlJVc3NSMEZHVEN4RFFVVlRPMEZCUTBRc2MwTkJRVlVzVDBGRVZEdEJRVVZFTEdsRFFVRkxMRU5CUmtvN1FVRkhSQ3hyUTBGQlRTeERRVWhNTzBGQlNVUXNkVU5CUVZjc1MwRktWanRCUVV0RUxITkRRVUZWTEU5QlRGUTdRVUZOUkN4elEwRkJWU3hQUVU1VU8wRkJUMFFzTmtOQlFXbENMRk5CVUdoQ08wRkJVVVFzYjBOQlFWRXNUVUZTVUR0QlFWTkVMSE5EUVVGVk8wRkJWRlFzZVVKQlJsUXNSVUZoU3l4UlFXSk1MRU5CWVdNc1JVRkJSU3hOUVVGR0xFTkJZbVE3UVVGalNEczdRVUZGUkN4elEwRkJhMElzVFVGQmJFSXNRMEZCZVVJc1VVRkJVU3hMUVVGTExGTkJRVXdzUTBGQlpTeEpRVUZtTEVOQlFWSXNSMEZCSzBJc1RVRkJlRVE3UVVGRFFUczdRVUZGU2p0QlFVTkpMSGRDUVVGSkxGbEJRVmtzVTBGQmFFSXNSVUZCTWtJN1FVRkRka0lzSzBKQlJIVkNMRU5CUTJZN1FVRkRXRHM3UVVGRlJDeDNRa0ZCU1N4UFFVRlBMRkZCUVZFc1lVRkJVaXhGUVVGMVFpeExRVUU1UWl4TFFVRjNReXhWUVVGNFF5eEpRVUZ6UkN4UFFVRlBMRkZCUVZFc1IwRkJVaXhEUVVGWkxFdEJRVzVDTEV0QlFUWkNMRlZCUVhaR0xFVkJRVzFITzBGQlF5OUdMRFJDUVVGSkxGRkJRVkVzWVVGQlVpeE5RVUV5UWl4VFFVRXZRaXhGUVVFd1F6dEJRVU4wUXl4dlEwRkJVU3hoUVVGU0xFVkJRWFZDTEV0QlFYWkNMRU5CUVRaQ0xFOUJRVGRDTEVWQlFYTkRMRWxCUVhSRE8wRkJRMGdzZVVKQlJrUXNUVUZGVHp0QlFVTklMRzlEUVVGUkxFZEJRVklzUTBGQldTeExRVUZhTEVOQlFXdENMRTlCUVd4Q0xFVkJRVEpDTEVsQlFUTkNPMEZCUTBnN1FVRkRTaXh4UWtGT1JDeE5RVTFQTzBGQlEwZ3NaME5CUVZFc1IwRkJVaXhEUVVGWkxFbEJRVm83UVVGRFNEdEJRWHBEVkR0QlFUSkRTRHRCUVVOS096dEJRVVZFT3pzN1FVRkhRU3haUVVGUkxITkNRVUZTTEVkQlFXbERMRmxCUVZrN1FVRkRla01zWlVGQlR5eFBRVUZRTEVkQlFXbENMRzFDUVVGcVFqdEJRVU5JTEV0QlJrUTdPMEZCU1VFN096czdPMEZCUzBFc1dVRkJVU3hMUVVGU0xFZEJRV2RDTEZsQlFWazdRVUZEZUVJc2FVSkJRVk1zVlVGQlZDeEZRVUZ4UWl4VFFVRnlRanRCUVVOSUxFdEJSa1E3TzBGQlNVRTdPenM3TzBGQlMwRXNXVUZCVVN4SlFVRlNMRWRCUVdVc1dVRkJXVHRCUVVOMlFpeHBRa0ZCVXl4VFFVRlVMRVZCUVc5Q0xGTkJRWEJDTzBGQlEwZ3NTMEZHUkRzN1FVRkpRVHM3T3pzN1FVRkxRU3haUVVGUkxFZEJRVklzUjBGQll5eFpRVUZaTzBGQlEzUkNMR2xDUVVGVExGRkJRVlFzUlVGQmJVSXNVMEZCYmtJN1FVRkRTQ3hMUVVaRU96dEJRVWxCT3pzN096dEJRVXRCTEZsQlFWRXNTVUZCVWl4SFFVRmxMRmxCUVZrN1FVRkRka0lzYVVKQlFWTXNVMEZCVkN4RlFVRnZRaXhUUVVGd1FqdEJRVU5JTEV0QlJrUTdPMEZCU1VFN096czdPMEZCUzBFc1dVRkJVU3hMUVVGU0xFZEJRV2RDTEZsQlFWazdRVUZEZUVJc2FVSkJRVk1zVlVGQlZDeEZRVUZ4UWl4VFFVRnlRanRCUVVOSUxFdEJSa1E3TzBGQlNVRTdPenM3TzBGQlMwRXNXVUZCVVN4TFFVRlNMRWRCUVdkQ0xGbEJRVms3UVVGRGVFSXNhVUpCUVZNc1ZVRkJWQ3hGUVVGeFFpeFRRVUZ5UWp0QlFVTklMRXRCUmtRN08wRkJTVUU3T3pzN08wRkJTMEVzV1VGQlVTeE5RVUZTTEVkQlFXbENMRmxCUVZrN1FVRkRla0lzYVVKQlFWTXNWMEZCVkN4RlFVRnpRaXhUUVVGMFFqdEJRVU5JTEV0QlJrUTdRVUZKU0N4RFFYWlJRU3hGUVhWUlF5eEpRVUZKTEVsQlFVb3NRMEZCVXl4TFFYWlJWaXhEUVVGRU96czdPenRCUTNCQ1FUczdPenM3T3pzN096dEJRVlZCTEVsQlFVa3NTVUZCU2l4RFFVRlRMRTFCUVZRc1IwRkJhMElzU1VGQlNTeEpRVUZLTEVOQlFWTXNUVUZCVkN4SlFVRnRRaXhGUVVGeVF6czdRVUZGUVRzN096czdPenRCUVU5QkxFTkJRVU1zVlVGQlZTeFBRVUZXTEVWQlFXMUNPenRCUVVWb1FqczdRVUZGUVR0QlFVTkJPMEZCUTBFN08wRkJSVUU3T3pzN096czdPenM3T3p0QlFWZEJMR0ZCUVZNc1pVRkJWQ3hEUVVGNVFpeFhRVUY2UWl4RlFVRnpRenRCUVVOc1F5eFpRVUZKTEhGQ1FVRnhRaXhGUVVGNlFqczdRVUZGUVR0QlFVTkJMRmxCUVVrc1VVRkJVU3hOUVVGTkxFbEJRVTRzUTBGQlZ5eFRRVUZUTEc5Q1FVRlVMRU5CUVRoQ0xFZEJRVGxDTEVOQlFWZ3NRMEZCV2p0QlFVRkJMRmxCUTBrc1VVRkJVU3h4UWtGRVdqczdRVUZLYTBNN1FVRkJRVHRCUVVGQk96dEJRVUZCTzBGQlQyeERMR2xEUVVGcFFpeExRVUZxUWl3NFNFRkJkMEk3UVVGQlFTeHZRa0ZCWml4SlFVRmxPMEZCUVVFN1FVRkJRVHRCUVVGQk96dEJRVUZCTzBGQlEzQkNMREJEUVVGelFpeE5RVUZOTEVsQlFVNHNRMEZCVnl4TFFVRkxMRlZCUVdoQ0xFTkJRWFJDTEcxSlFVRnRSRHRCUVVGQkxEUkNRVUV4UXl4VFFVRXdRenM3UVVGREwwTXNORUpCUVVrc1ZVRkJWU3hKUVVGV0xFTkJRV1VzVFVGQlppeERRVUZ6UWl4TFFVRjBRaXhOUVVGcFF5eERRVUZETEVOQlFYUkRMRVZCUVhsRE8wRkJRM0pETzBGQlEwRXNaME5CUVVrc1QwRkJUeXhWUVVGVkxFbEJRVllzUTBGQlpTeFBRVUZtTEVOQlFYVkNMRXRCUVhaQ0xFVkJRVGhDTEVsQlFUbENMRU5CUVZnN1FVRkJRU3huUTBGRFNTeFRRVUZUTEZWQlFWVXNTMEZFZGtJN08wRkJSMEU3UVVGRFFTeG5RMEZCU1N4dFFrRkJiVUlzVDBGQmJrSXNRMEZCTWtJc1NVRkJNMElzU1VGQmJVTXNRMEZCUXl4RFFVRjRReXhGUVVFeVF6dEJRVU4yUXl4dlEwRkJTU3hQUVVGUExFbEJRVkFzUlVGQllTeE5RVUZpTEV0QlFYZENMRTFCUVRWQ0xFVkJRVzlETzBGQlEyaERMSGREUVVGSkxFbEJRVW9zUTBGQlV5eExRVUZVTEVOQlFXVXNTMEZCWml4cFJFRkJiVVVzU1VGQmJrVTdRVUZEUVN3d1EwRkJUU3hKUVVGSkxFdEJRVW9zUTBGQlZTeHZRa0ZCYTBJc1NVRkJiRUlzT0VWQlFWWXNRMEZCVGp0QlFVVklPMEZCUTBRc2VVTkJUblZETEVOQlRUZENPMEZCUTJJN08wRkJSVVFzWjBOQlFVa3NWMEZCVnl4RlFVRm1MRVZCUVcxQ08wRkJRMllzYzBOQlFVMHNTVUZCU1N4WFFVRktMR2xEUVVFNFF5eEpRVUU1UXl4RFFVRk9PMEZCUTBnN08wRkJSVVE3UVVGRFFUdEJRVU5CTEdkRFFVRkpMRk5CUVZNc1MwRkJZaXhGUVVGdlFqdEJRVUZGTzBGQlEyeENMREJFUVVFd1FpeE5RVUV4UWl4RlFVRnJReXhYUVVGc1F6dEJRVU5JTERaQ1FVWkVMRTFCUlU4N1FVRkRTQ3gxUTBGQlR5eEpRVUZRTEVsQlFXVXNTVUZCU1N4SlFVRkpMRmxCUVVvc1EwRkJhVUlzVTBGQmNrSXNRMEZCSzBJc1NVRkJMMElzUlVGQmNVTXNUVUZCY2tNc1JVRkJOa01zVjBGQk4wTXNRMEZCWmp0QlFVTklPenRCUVVWRUxDdERRVUZ0UWl4SlFVRnVRaXhEUVVGM1FpeEpRVUY0UWp0QlFVTkJMR2xEUVVGTExHVkJRVXdzUTBGQmNVSXNWVUZCVlN4SlFVRXZRanRCUVVOSU8wRkJRMG83UVVGb1EyMUNPMEZCUVVFN1FVRkJRVHRCUVVGQk8wRkJRVUU3UVVGQlFUdEJRVUZCTzBGQlFVRTdRVUZCUVR0QlFVRkJPMEZCUVVFN1FVRkJRVHRCUVVGQk8wRkJRVUU3UVVGcFEzWkNPenRCUVVWRU8wRkJNVU5yUXp0QlFVRkJPMEZCUVVFN1FVRkJRVHRCUVVGQk8wRkJRVUU3UVVGQlFUdEJRVUZCTzBGQlFVRTdRVUZCUVR0QlFVRkJPMEZCUVVFN1FVRkJRVHRCUVVGQk96dEJRVEpEYkVNc1dVRkJTU3h0UWtGQmJVSXNUVUZCYmtJc1MwRkJPRUlzUTBGQmJFTXNSVUZCY1VNN1FVRkRha01zYTBKQlFVMHNTVUZCU1N4TFFVRktMRU5CUVZVc0swVkJRMW9zYlVKQlJFVXNRMEZCVGp0QlFVVklPenRCUVVWRU8wRkJRMEVzV1VGQlNTeHhRa0ZCY1VJc1JVRkJla0k3TzBGQmFrUnJRenRCUVVGQk8wRkJRVUU3TzBGQlFVRTdRVUZCUVR0QlFVRkJMRzlDUVcxRWVrSXNTVUZ1UkhsQ096dEJRVzlFT1VJc2IwSkJRVWtzVjBGQlZ5eEZRVUZGTEZGQlFVWXNSVUZCWmpzN1FVRkZRU3h0UTBGQmJVSXNTVUZCYmtJc1EwRkJkMElzVVVGQmVFSTdPMEZCUlVFc2RVSkJRVThzU1VGQlVDeEZRVU5MTEVsQlJFd3NSMEZGU3l4SlFVWk1MRU5CUlZVc1UwRkJVeXhQUVVadVFpeEZRVWRMTEVsQlNFd3NRMEZIVlN4VFFVRlRMRTFCU0c1Q0xFVkJTVXNzVFVGS1RDeERRVWxaTzBGQlFVRXNNa0pCUVUwc1NVRkJTU3hKUVVGS0xFTkJRVk1zUzBGQlZDeERRVUZsTEVsQlFXWXNRMEZCYjBJc2IwTkJRWEJDTEVWQlFUQkVMRWxCUVRGRUxFTkJRVTQ3UVVGQlFTeHBRa0ZLV2p0QlFYaEVPRUk3TzBGQmJVUnNReXhyUTBGQmFVSXNhMEpCUVdwQ0xHMUpRVUZ4UXp0QlFVRkJPMEZCVlhCRE96dEJRVVZFTzBGQkwwUnJRenRCUVVGQk8wRkJRVUU3UVVGQlFUdEJRVUZCTzBGQlFVRTdRVUZCUVR0QlFVRkJPMEZCUVVFN1FVRkJRVHRCUVVGQk8wRkJRVUU3UVVGQlFUdEJRVUZCT3p0QlFXZEZiRU1zVlVGQlJTeEpRVUZHTEVOQlFVOHNTMEZCVUN4RFFVRmhMRk5CUVdJc1JVRkJkMElzYTBKQlFYaENMRVZCUVRSRExFMUJRVFZETEVOQlFXMUVMRmxCUVZrN1FVRkRNMFFzWjBKQlFVa3NVVUZCVVN4VFFVRlRMRmRCUVZRc1EwRkJjVUlzVDBGQmNrSXNRMEZCV2p0QlFVTkJMR3RDUVVGTkxGTkJRVTRzUTBGQlowSXNkMEpCUVdoQ0xFVkJRVEJETEVsQlFURkRMRVZCUVdkRUxFbEJRV2hFTzBGQlEwRXNjVUpCUVZNc1lVRkJWQ3hEUVVGMVFpeE5RVUYyUWl4RlFVRXJRaXhoUVVFdlFpeERRVUUyUXl4TFFVRTNRenRCUVVOQkxHZENRVUZKTEVsQlFVb3NRMEZCVXl4UlFVRlVMRU5CUVd0Q0xFZEJRV3hDTEVOQlFYTkNMRmxCUVhSQ0xFVkJRVzlETEVsQlFVa3NTVUZCU2l4SFFVRlhMRTlCUVZnc1JVRkJjRU03UVVGRFFTeG5Ra0ZCU1N4SlFVRktMRU5CUVZNc1MwRkJWQ3hEUVVGbExFbEJRV1lzUTBGQmIwSXNNRUpCUVhCQ0xFVkJRV2RFTEVsQlFVa3NTVUZCU2l4RFFVRlRMRkZCUVZRc1EwRkJhMElzUjBGQmJFSXNRMEZCYzBJc1dVRkJkRUlzU1VGRE1VTXNTVUZCU1N4SlFVRktMRU5CUVZNc1VVRkJWQ3hEUVVGclFpeEhRVUZzUWl4RFFVRnpRaXhqUVVGMFFpeERRVVJPTEVWQlF6WkRMRWxCUkRkRE8wRkJSVUVzWjBKQlFVa3NUMEZCVHl4UFFVRllMRVZCUVc5Q08wRkJRMmhDTEhWQ1FVRlBMRkZCUVZBc1IwRkJhMElzU1VGQmJFSTdRVUZEU0R0QlFVTktMRk5CVmtRN08wRkJXVUVzWlVGQlR5eHJRa0ZCVUR0QlFVTklPenRCUVVWRU96czdPenM3T3pzN096czdRVUZaUVN4aFFVRlRMSGxDUVVGVUxFTkJRVzFETEUxQlFXNURMRVZCUVRKRExGZEJRVE5ETEVWQlFYZEVPMEZCUTNCRUxGbEJRVWtzWlVGQlpTeEpRVUZKTEVsQlFVa3NXVUZCU2l4RFFVRnBRaXhUUVVGeVFpeERRVUVyUWl4TFFVRXZRaXhGUVVGelF5eE5RVUYwUXl4RlFVRTRReXhYUVVFNVF5eERRVUZ1UWp0QlFVTkJMRmxCUVVrc1NVRkJTaXhIUVVGWExHRkJRV0VzU1VGQmVFSTdRVUZEUVN4WlFVRkpMRTFCUVVvc1IwRkJZU3hoUVVGaExFMUJRVEZDTzBGQlEwRXNXVUZCU1N4WFFVRktMRWRCUVd0Q0xHRkJRV0VzVjBGQkwwSTdRVUZEUVN4WlFVRkpMRWxCUVVvc1IwRkJWeXhKUVVGSkxGbEJRVW9zUTBGQmFVSXNVMEZCYWtJc1EwRkJNa0lzVTBGQk0wSXNRMEZCY1VNc1NVRkJhRVE3UVVGRFNEczdRVUZGUkR0QlFVTkJPMEZCUTBFN08wRkJSVUU3T3pzN08wRkJTMEVzV1VGQlVTeEpRVUZTTEVkQlFXVXNWVUZCVlN4WFFVRldMRVZCUVhWQ08wRkJRMnhETzBGQlEwRXNXVUZCU1N4SlFVRktMRU5CUVZNc1MwRkJWQ3hEUVVGbExITkNRVUZtT3p0QlFVVkJPMEZCUTBFc1dVRkJTU3h4UWtGQmNVSXNaMEpCUVdkQ0xGZEJRV2hDTEVOQlFYcENPenRCUVVWQk8wRkJRMEVzV1VGQlNTeEpRVUZLTEVOQlFWTXNTMEZCVkN4RFFVRmxMRWxCUVdZc1EwRkJiMElzYzBKQlFYTkNMRzFDUVVGdFFpeEpRVUZ1UWl4RlFVRXhRenM3UVVGRlFUdEJRVU5CTEZsQlFVa3NTVUZCU2l4RFFVRlRMRkZCUVZRc1EwRkJhMElzUjBGQmJFSXNRMEZCYzBJc1dVRkJkRUlzUlVGQmIwTXNhMEpCUVhCRE8wRkJRMGdzUzBGYVJEdEJRV05JTEVOQk4wbEVMRVZCTmtsSExFbEJRVWtzU1VGQlNpeERRVUZUTEUxQk4wbGFPenM3T3p0QlEyNUNRVHM3T3pzN096czdPenRCUVZWQk96czdPenM3TzBGQlQwTXNZVUZCV1RzN1FVRkZWRHM3UVVGRlFUdEJRVU5CTzBGQlEwRTdPMEZCUlVFc1RVRkJSU3hGUVVGR0xFTkJRVXNzVFVGQlRDeERRVUZaTzBGQlExSXNlVUpCUVdsQ0xIbENRVUZWTEZWQlFWWXNSVUZCYzBJN1FVRkRia01zWjBKQlFVa3NRMEZCUXl4VlFVRkVMRWxCUVdVc1pVRkJaU3hGUVVGc1F5eEZRVUZ6UXp0QlFVTnNReXh6UWtGQlRTeEpRVUZKTEV0QlFVb3NRMEZCVlN3NFEwRkJWaXhEUVVGT08wRkJRMGc3TzBGQlJVUXNaMEpCUVUwc1kwRkJZeXhGUVVGRkxFbEJRVVlzUlVGQlVTeEpRVUZTTEVWQlFYQkNPMEZCUTBFc1owSkJRVTBzWlVGQlpTeEZRVUZ5UWpzN1FVRkZRVHRCUVVOQk8wRkJRMEVzWTBGQlJTeEpRVUZHTEVOQlFVOHNWMEZCVUN4RlFVRnZRaXhWUVVGVkxFZEJRVllzUlVGQlpTeExRVUZtTEVWQlFYTkNPMEZCUTNSRExHOUNRVUZKTEVsQlFVa3NUMEZCU2l4RFFVRlpMRlZCUVZvc1RVRkJORUlzUTBGQk5VSXNTVUZCYVVNc1NVRkJTU3hQUVVGS0xFTkJRVmtzVjBGQlZ5eFhRVUZZTEVWQlFWb3NUVUZCTUVNc1EwRkJMMFVzUlVGQmEwWTdRVUZET1VVc2QwSkJRVWtzVTBGQlV5eEpRVUZKTEUxQlFVb3NRMEZCVnl4WFFVRlhMRTFCUVhSQ0xFTkJRV0k3UVVGRFFTdzJRa0ZCVXl4UFFVRlBMRTFCUVZBc1EwRkJZeXhEUVVGa0xFVkJRV2xDTEVOQlFXcENMRVZCUVc5Q0xGZEJRWEJDTEV0QlFXOURMRTlCUVU4c1RVRkJVQ3hEUVVGakxFTkJRV1FzUTBGQk4wTTdRVUZEUVN4cFEwRkJZU3hOUVVGaUxFbEJRWFZDTEV0QlFYWkNPMEZCUTBnN1FVRkRTaXhoUVU1RU96dEJRVkZCTEcxQ1FVRlBMRmxCUVZBN1FVRkRTRHRCUVhCQ1R5eExRVUZhT3p0QlFYVkNRVHRCUVVOQk8wRkJRMEU3TzBGQlJVRXNVVUZCU1N4RlFVRkZMRlZCUVVZc1MwRkJhVUlzVTBGQmNrSXNSVUZCWjBNN1FVRkROVUlzVlVGQlJTeFZRVUZHTEVOQlFXRXNVVUZCWWl4RFFVRnpRaXhGUVVGMFFpeEhRVUV5UWp0QlFVTjJRaXgzUWtGQldTeFZRVVJYTzBGQlJYWkNMSE5DUVVGVkxFTkJSbUU3UVVGSGRrSXNiVUpCUVU4N1FVRklaMElzVTBGQk0wSTdRVUZMUVN4VlFVRkZMRlZCUVVZc1EwRkJZU3hYUVVGaUxFTkJRWGxDTEVWQlFVVXNWVUZCUml4RFFVRmhMRkZCUVdJc1EwRkJjMElzUlVGQkwwTTdRVUZEU0R0QlFVTktMRU5CTTBOQkxFZEJRVVE3T3p0QlEycENRVHM3T3pzN096czdPenRCUVZWQk96dEJRVVZCT3pzN096czdPenM3T3pzN096czdPenM3T3pzN096czdPenM3T3p0QlFUWkNRVHRCUVVOQk96dEJRVU5CTEU5QlFVOHNSMEZCVUN4SFFVRmhPMEZCUTFRc1ZVRkJUU3hGUVVSSE8wRkJSVlFzVlVGQlRTeEZRVVpITzBGQlIxUXNhMEpCUVdNN1FVRklUQ3hEUVVGaU96dEJRVTFCTzBGQlEwRXNVMEZCVXl4blFrRkJWQ3hEUVVFd1FpeHJRa0ZCTVVJc1JVRkJPRU1zV1VGQldUdEJRVU4wUkN4UlFVRkpPMEZCUTBFN1FVRkRRU3haUVVGSkxFOUJRVThzY1VKQlFWQXNTMEZCYVVNc1UwRkJja01zUlVGQlowUTdRVUZETlVNc2EwSkJRVTBzU1VGQlNTeExRVUZLTEVOQlFWVXNiVVpCUTFvc1owVkJSRVVzUTBGQlRqdEJRVVZJT3p0QlFVVkVPMEZCUTBFc1dVRkJTU3hKUVVGS0xFTkJRVk1zVFVGQlZDeERRVUZuUWl4SlFVRm9RaXhEUVVGeFFpeFBRVUZQTEhGQ1FVRTFRanM3UVVGRlFUdEJRVU5CTEZsQlFVa3NTVUZCU2l4RFFVRlRMRkZCUVZRc1EwRkJhMElzUjBGQmJFSXNRMEZCYzBJc1kwRkJkRUlzUlVGQmMwTXNTMEZCU3l4SFFVRk1MRVZCUVhSRE96dEJRVVZCTzBGQlEwRXNXVUZCU1N4SlFVRktMRU5CUVZNc1RVRkJWQ3hEUVVGblFpeEpRVUZvUWl4RFFVRnhRaXhKUVVGSkxFbEJRVW9zUTBGQlV5eE5RVUZVTEVOQlFXZENMRWRCUVdoQ0xFTkJRVzlDTEdGQlFYQkNMRU5CUVhKQ08wRkJRMGdzUzBGbVJDeERRV1ZGTEU5QlFVOHNVMEZCVUN4RlFVRnJRanRCUVVOb1FpeFpRVUZKTEVsQlFVb3NRMEZCVXl4TFFVRlVMRU5CUVdVc1MwRkJaaXhEUVVGeFFpeHRSRUZCY2tJc1JVRkJNRVVzVTBGQk1VVTdRVUZEUVR0QlFVTkJMRmxCUVUwc1VVRkJVU3hUUVVGVExGZEJRVlFzUTBGQmNVSXNZVUZCY2tJc1EwRkJaRHRCUVVOQkxHTkJRVTBzWlVGQlRpeERRVUZ6UWl4UFFVRjBRaXhGUVVFclFpeEpRVUV2UWl4RlFVRnhReXhKUVVGeVF5eEZRVUV5UXl4VFFVRXpRenRCUVVOQkxHVkJRVThzWVVGQlVDeERRVUZ4UWl4TFFVRnlRanRCUVVOSU96dEJRVVZFTEdOQlFWVXNWVUZCVml4RFFVRnhRanRCUVVOcVFpeG5Ra0ZCVVN4blFrRkJWU3hIUVVGV0xFVkJRV1VzUjBGQlppeEZRVUZ2UWp0QlFVTjRRaXhuUWtGQlNTeFRRVUZLTEVOQlFXTXNUVUZCWkN4RFFVRnhRaXhoUVVGeVFpeEZRVVIzUWl4RFFVTmhPMEZCUTNoRE8wRkJTR2RDTEV0QlFYSkNPMEZCUzBFN1FVRkRRU3hqUVVGVkxGTkJRVk1zWjBKQlFWUXNRMEZCTUVJc2FVSkJRVEZDTEVOQlFWWXNSVUZCZDBRN1FVRkRjRVFzY1VKQlFXRXNkVUpCUVZrN1FVRkRja0k3TzBGQlJVZzdRVUZLYlVRc1MwRkJlRVE3UVVGTlNDeERRWEJEUkRzN096czdPenRCUTJ4RVFUczdPenM3T3pzN096dEJRVlZCTEVsQlFVa3NTVUZCU2l4RFFVRlRMRWxCUVZRc1IwRkJaMElzU1VGQlNTeEpRVUZLTEVOQlFWTXNTVUZCVkN4SlFVRnBRaXhGUVVGcVF6czdRVUZGUVRzN096czdPenM3T3pzN096czdPenM3T3pzN1FVRnZRa01zVjBGQlZTeFBRVUZXTEVWQlFXMUNPenRCUVVWb1FqczdRVUZGUVR0QlFVTkJPMEZCUTBFN08wRkJSVUU3T3pzN096dEJRVXRCTEZGQlFVMHNWMEZCVnl4RlFVRnFRanM3UVVGRlFUdEJRVU5CTzBGQlEwRTdPMEZCUlVFN096czdPenM3TzBGQlVVRXNXVUZCVVN4VlFVRlNMRWRCUVhGQ0xGVkJRVlVzU1VGQlZpeEZRVUZuUWl4WlFVRm9RaXhGUVVFNFFqdEJRVU12UXl4WlFVRkpMRTlCUVU4c1NVRkJVQ3hMUVVGblFpeFJRVUZvUWl4SlFVRTBRaXhSUVVGUExGbEJRVkFzZVVOQlFVOHNXVUZCVUN4UFFVRjNRaXhSUVVGd1JDeEpRVUZuUlN4cFFrRkJhVUlzU1VGQmNrWXNSVUZCTWtZN1FVRkRka1lzYTBKQlFVMHNTVUZCU1N4TFFVRktMRU5CUVZVc0swVkJRVFJGTEVsQlFUVkZMSGxEUVVFMFJTeEpRVUUxUlN4M1EwRkRZeXhaUVVSa0xIbERRVU5qTEZsQlJHUXNWVUZCVml4RFFVRk9PMEZCUlVnN1FVRkRSQ3hwUWtGQlV5eEpRVUZVTEVsQlFXbENMRmxCUVdwQ08wRkJRMGdzUzBGT1JEczdRVUZSUVRzN096czdPenRCUVU5QkxGbEJRVkVzVjBGQlVpeEhRVUZ6UWl4WlFVRlpPMEZCUXpsQ0xGbEJRVTBzVTBGQlV5eEZRVUZtT3p0QlFVVkJMR0ZCUVVzc1NVRkJTU3hQUVVGVUxFbEJRVzlDTEZGQlFYQkNMRVZCUVRoQ08wRkJRekZDTEcxQ1FVRlBMRWxCUVZBc1EwRkJXU3hQUVVGYU8wRkJRMGc3TzBGQlJVUXNaVUZCVHl4TlFVRlFPMEZCUTBnc1MwRlNSRHM3UVVGWFFUczdPenM3TzBGQlRVRXNXVUZCVVN4VlFVRlNMRWRCUVhGQ0xGVkJRVk1zVDBGQlZDeEZRVUZyUWp0QlFVTnVReXhaUVVGSkxFOUJRVThzVDBGQlVDeExRVUZ0UWl4UlFVRjJRaXhGUVVGcFF6dEJRVU0zUWl4clFrRkJUU3hKUVVGSkxFdEJRVW9zWjBWQlFYTkZMRTlCUVhSRkxIbERRVUZ6UlN4UFFVRjBSU3hWUVVGT08wRkJRMGc3TzBGQlJVUXNXVUZCU1N4VFFVRlRMRTlCUVZRc1RVRkJjMElzVTBGQk1VSXNSVUZCY1VNN1FVRkRha01zYTBKQlFVMHNTVUZCU1N4TFFVRktMR05CUVhGQ0xFOUJRWEpDTEhOQ1FVRk9PMEZCUTBnN08wRkJSVVFzWlVGQlR5eFRRVUZUTEU5QlFWUXNRMEZCVUR0QlFVTklMRXRCVmtRN08wRkJZVUU3T3pzN096czdPenM3TzBGQlYwRXNXVUZCVVN4VFFVRlNMRWRCUVc5Q0xGVkJRVlVzVFVGQlZpeEZRVUZyUWl4UFFVRnNRaXhGUVVFeVFqdEJRVU16UXp0QlFVTkJMRmxCUVVrc1QwRkJUeXhOUVVGUUxFdEJRV3RDTEZGQlFXeENMRWxCUVRoQ0xFOUJRVThzVDBGQlVDeExRVUZ0UWl4UlFVRnlSQ3hGUVVFclJEdEJRVU16UkN4clFrRkJUU3hKUVVGSkxFdEJRVW9zUTBGQlZTeHhSVUZCYTBVc1RVRkJiRVVzZVVOQlFXdEZMRTFCUVd4RkxHMURRVU5UTEU5QlJGUXNlVU5CUTFNc1QwRkVWQ3hWUVVGV0xFTkJRVTQ3UVVGRlNEczdRVUZGUkR0QlFVTkJMRmxCUVVrc1UwRkJVeXhQUVVGVUxFMUJRWE5DTEZOQlFYUkNMRWxCUVcxRExGTkJRVk1zVDBGQlZDeEZRVUZyUWl4TlFVRnNRaXhOUVVFNFFpeFRRVUZ5UlN4RlFVRm5SanRCUVVNMVJTeG5Ra0ZCU1N4SlFVRktMRU5CUVZNc1MwRkJWQ3hEUVVGbExFbEJRV1lzY1VSQlFYTkZMRTFCUVhSRkxHMUNRVUV3Uml4UFFVRXhSanRCUVVOQkxHMUNRVUZQTEUxQlFVMHNUMEZCVGl4SFFVRm5RaXhIUVVGb1FpeEhRVUZ6UWl4TlFVRjBRaXhIUVVFclFpeEhRVUYwUXp0QlFVTklPenRCUVVWRUxHVkJRVThzVTBGQlV5eFBRVUZVTEVWQlFXdENMRTFCUVd4Q0xFTkJRVkE3UVVGRFNDeExRV1JFTzBGQlowSklMRU5CYmtkQkxFVkJiVWRETEVsQlFVa3NTVUZCU2l4RFFVRlRMRWxCYmtkV0xFTkJRVVE3T3pzN08wRkRja0pCT3p0QlFVZEJPenRCUVVOQk96dEJRVU5CT3p0QlFVTkJPenRCUVVkQk96dEJRVU5CT3p0QlFVTkJPenRCUVVOQk96dEJRVU5CT3p0QlFVTkJPenRCUVVOQk96dEJRVU5CT3p0QlFVTkJPenRCUVVOQk96dEJRVU5CT3pzN096dEJRemxDUVRzN096czdPenM3T3p0QlFWVkJMRWxCUVVrc1NVRkJTaXhEUVVGVExHRkJRVlFzUjBGQmVVSXNTVUZCU1N4SlFVRktMRU5CUVZNc1lVRkJWQ3hKUVVFd1FpeEZRVUZ1UkRzN1FVRkZRVHM3T3pzN096czdRVUZSUVN4RFFVRkRMRlZCUVZVc1QwRkJWaXhGUVVGdFFqczdRVUZGYUVJN08wRkJSVUU3UVVGRFFUdEJRVU5CT3p0QlFVVkJPenM3T3pzN096dEJRVTlCTEdGQlFWTXNVVUZCVkN4RFFVRnJRaXhIUVVGc1FpeEZRVUYxUWp0QlFVTnVRaXhaUVVGTkxFOUJRVThzVTBGQlV5eGhRVUZVTEVOQlFYVkNMRTFCUVhaQ0xFTkJRV0k3UVVGRFFTeGhRVUZMTEVsQlFVd3NSMEZCV1N4VlFVRmFPMEZCUTBFc1lVRkJTeXhIUVVGTUxFZEJRVmNzV1VGQldEdEJRVU5CTEdGQlFVc3NTVUZCVEN4SFFVRlpMRWRCUVZvN1FVRkRRU3hwUWtGQlV5eHZRa0ZCVkN4RFFVRTRRaXhOUVVFNVFpeEZRVUZ6UXl4RFFVRjBReXhGUVVGNVF5eFhRVUY2UXl4RFFVRnhSQ3hKUVVGeVJEdEJRVU5JT3p0QlFVVkVPMEZCUTBFN1FVRkRRVHM3UVVGRlFUczdPenM3T3pzN1FVRlJRU3haUVVGUkxFbEJRVklzUjBGQlpTeFpRVUZaTzBGQlEzWkNMRmxCUVVrc1dVRkJXU3hGUVVGb1FqczdRVUZGUVN4WlFVRkpMRWxCUVVrc1NVRkJTaXhEUVVGVExFMUJRVlFzUTBGQlowSXNSMEZCYUVJc1EwRkJiMElzWVVGQmNFSXNUVUZCZFVNc1dVRkJka01zU1VGRFFTeEpRVUZKTEVsQlFVb3NRMEZCVXl4TlFVRlVMRU5CUVdkQ0xFZEJRV2hDTEVOQlFXOUNMRmRCUVhCQ0xFMUJRWEZETEV0QlJISkRMRWxCUlVFc1NVRkJTU3hKUVVGS0xFTkJRVk1zVFVGQlZDeERRVUZuUWl4SFFVRm9RaXhEUVVGdlFpeFpRVUZ3UWl4RFFVWktMRVZCUlhWRE8wRkJRMjVETEd0RFFVRnZRaXhKUVVGSkxFbEJRVW9zUTBGQlV5eE5RVUZVTEVOQlFXZENMRWRCUVdoQ0xFTkJRVzlDTEZsQlFYQkNMRU5CUVhCQ08wRkJRMGc3TzBGQlJVUXNXVUZCVFN4VFFVRlRPMEZCUTFnc2NVSkJRVk1zU1VGQlNTeEpRVUZLTEVOQlFWTXNUVUZCVkN4RFFVRm5RaXhIUVVGb1FpeERRVUZ2UWl4UlFVRndRaXhEUVVSRk8wRkJSVmdzY1VKQlFWTXNVMEZHUlR0QlFVZFlMSEZDUVVGVExHbENRVUZWTEV0QlFWWXNSVUZCYVVJN1FVRkRkRUlzYjBKQlFVa3NTVUZCU2l4RFFVRlRMRXRCUVZRc1EwRkJaU3hMUVVGbUxFTkJRWEZDTEd0Q1FVRnlRaXhGUVVGNVF5eExRVUY2UXp0QlFVTklPMEZCVEZVc1UwRkJaanM3UVVGUlFTeGxRVUZQTEU5QlFWQXNRMEZCWlN4TlFVRm1MRU5CUVhOQ0xFMUJRWFJDTzBGQlEwZ3NTMEZzUWtRN08wRkJiMEpCT3pzN096czdPenRCUVZGQkxGbEJRVkVzVDBGQlVpeEhRVUZyUWl4VlFVRlZMRmxCUVZZc1JVRkJkMElzVVVGQmVFSXNSVUZCYTBNN1FVRkJRVHRCUVVGQk8wRkJRVUU3TzBGQlFVRTdRVUZEYUVRc2FVTkJRWFZDTEZsQlFYWkNMRGhJUVVGeFF6dEJRVUZCTEc5Q1FVRTFRaXhWUVVFMFFqczdRVUZEYWtNc2IwSkJRVWtzVjBGQlZ5eFJRVUZZTEVOQlFXOUNMRTFCUVhCQ0xFTkJRVW9zUlVGQmFVTTdRVUZETjBJc05rSkJRVk1zVlVGQlZEdEJRVU5CTEhkQ1FVRk5MRkZCUVZFc1lVRkJZU3hQUVVGaUxFTkJRWEZDTEZWQlFYSkNMRU5CUVdRN1FVRkRRU3hwUTBGQllTeE5RVUZpTEVOQlFXOUNMRXRCUVhCQ0xFVkJRVEpDTEVOQlFUTkNPMEZCUTBnN1FVRkRTanRCUVZBclF6dEJRVUZCTzBGQlFVRTdRVUZCUVR0QlFVRkJPMEZCUVVFN1FVRkJRVHRCUVVGQk8wRkJRVUU3UVVGQlFUdEJRVUZCTzBGQlFVRTdRVUZCUVR0QlFVRkJPenRCUVZOb1JDeFpRVUZKTEdGQlFXRXNUVUZCWWl4TFFVRjNRaXhEUVVFMVFpeEZRVUVyUWp0QlFVTXpRanRCUVVOSUxGTkJSa1FzVFVGRlR6dEJRVU5JTEcxQ1FVRlBMRTlCUVZBc1EwRkJaU3haUVVGbUxFVkJRVFpDTEZGQlFUZENPMEZCUTBnN1FVRkRTaXhMUVdSRU96dEJRV2RDUVRzN096czdPenM3TzBGQlUwRXNXVUZCVVN4SlFVRlNMRWRCUVdVc1ZVRkJWU3hSUVVGV0xFVkJRVzlDTEVsQlFYQkNMRVZCUVRCQ0xGVkJRVEZDTEVWQlFYTkRPMEZCUTJwRUxGbEJRVTBzVjBGQlZ5eEZRVUZGTEZGQlFVWXNSVUZCYWtJN08wRkJSVUVzV1VGQlNUdEJRVU5CTEdkQ1FVRkpMRk5CUVZNc1JVRkJZaXhGUVVGcFFqdEJRVU5pTEhsQ1FVRlRMRTFCUVZRc1EwRkJaMElzU1VGQlNTeExRVUZLTEVOQlFWVXNPRUpCUVZZc1EwRkJhRUk3UVVGRFNEczdRVUZGUkN4blFrRkJUU3hwUWtGQmFVSXNTMEZCU3l4UFFVRk1MRU5CUVdFc1YwRkJZaXhGUVVFd1FpeEpRVUV4UWl4RFFVRjJRaXhEUVV4QkxFTkJTM2RFT3p0QlFVVjRSRHRCUVVOQkxHZENRVUZOTEZOQlFWTXNWMEZCVnl4TFFVRllMRU5CUVdsQ0xFOUJRV3BDTEVOQlFYbENMR05CUVhwQ0xFTkJRV1k3UVVGRFFTeG5Ra0ZCU1N4VlFVRlZMRTlCUVU4c1NVRkJVQ3hMUVVGblFpeFZRVUU1UWl4RlFVRXdRenRCUVVOMFF5eDVRa0ZCVXl4UFFVRlVMRU5CUVdsQ0xFbEJRVWtzU1VGQlNTeFpRVUZLTEVOQlFXbENMRTFCUVhKQ0xFTkJRVFJDTEZGQlFUVkNMRVZCUVhORExHTkJRWFJETEVWQlFYTkVMRlZCUVhSRUxFTkJRV3BDTzBGQlEwRXNkVUpCUVU4c1NVRkJVQ3hEUVVaelF5eERRVVY2UWp0QlFVTm9RanM3UVVGRlJDeG5Ra0ZCU1N4aFFVRmhMRVZCUVdwQ08wRkJRMEVzWjBKQlFVa3NTVUZCU1N4SlFVRktMRU5CUVZNc1RVRkJWQ3hEUVVGblFpeEhRVUZvUWl4RFFVRnZRaXhoUVVGd1FpeE5RVUYxUXl4WlFVRjJReXhKUVVOQkxFbEJRVWtzU1VGQlNpeERRVUZUTEUxQlFWUXNRMEZCWjBJc1IwRkJhRUlzUTBGQmIwSXNWMEZCY0VJc1EwRkVRU3hKUVVWQkxFbEJRVWtzU1VGQlNpeERRVUZUTEUxQlFWUXNRMEZCWjBJc1IwRkJhRUlzUTBGQmIwSXNXVUZCY0VJc1EwRkdTaXhGUVVkRk8wRkJRMFVzTmtKQlFXRXNWMEZCVnl4SlFVRkpMRWxCUVVvc1EwRkJVeXhOUVVGVUxFTkJRV2RDTEVkQlFXaENMRU5CUVc5Q0xGbEJRWEJDTEVOQlFYaENPMEZCUTBnN08wRkJSVVE3UVVGRFFTeG5Ra0ZCVFN4blFrRkJaMElzU1VGQlNTeEpRVUZLTEVOQlFWTXNUVUZCVkN4RFFVRm5RaXhIUVVGb1FpeERRVUZ2UWl4UFFVRndRaXhOUVVGcFF5eFBRVUZxUXl4SFFVRXlReXhUUVVFelF5eEhRVUYxUkN4TFFVRTNSVHRCUVVOQkxHZENRVUZOTEUxQlFVMHNWMEZCVnl4VFFVRllMRU5CUVhGQ0xFMUJRWEpDTEVkQlFUaENMRWRCUVRsQ0xFZEJRVzlETEZkQlFWY3NTVUZCTDBNc1IwRkJjMFFzUjBGQmRFUXNSMEZCTkVRc1NVRkJOVVFzUjBGQmJVVXNWVUZCYmtVc1IwRkJaMFlzWVVGQk5VWTdPMEZCUlVFc2JVSkJRVThzVDBGQlVDeERRVUZsTEVOQlFVTXNSMEZCUkN4RFFVRm1MRVZCUVhOQ0xGbEJRVTA3UVVGRGVFSXNiMEpCUVVrc1YwRkJWeXhMUVVGWUxFTkJRV2xDTEU5QlFXcENMRU5CUVhsQ0xHTkJRWHBDTEUxQlFUWkRMRk5CUVdwRUxFVkJRVFJFTzBGQlEzaEVMREJDUVVGTkxFbEJRVWtzUzBGQlNpeERRVUZWTEdGQlFXRXNTVUZCWWl4SFFVRnZRaXg1UkVGQmNFSXNSMEZEVml3d1FrRkVRU3hEUVVGT08wRkJSVWc3TzBGQlJVUTdRVUZEUVN4dlFrRkJUU3hsUVVGbExGZEJRVmNzUzBGQldDeERRVUZwUWl4UFFVRnFRaXhEUVVGNVFpeGpRVUY2UWl4RlFVRjVReXhaUVVGNlF5eERRVUZ6UkN4TFFVRjBSQ3hGUVVGeVFqczdRVUZGUVN4dlFrRkJTU3hoUVVGaExFMUJRV0lzUzBGQmQwSXNRMEZCTlVJc1JVRkJLMEk3UVVGQlJUdEJRVU0zUWl3MlFrRkJVeXhQUVVGVUxFTkJRV2xDTEVsQlFVa3NTVUZCU1N4WlFVRktMRU5CUVdsQ0xFMUJRWEpDTEVOQlFUUkNMRkZCUVRWQ0xFVkJRWE5ETEdOQlFYUkRMRVZCUVhORUxGVkJRWFJFTEVOQlFXcENPMEZCUTBFc01rSkJRVThzU1VGQlVDeERRVVl5UWl4RFFVVmtPMEZCUTJoQ096dEJRVVZFTzBGQlEwRXNjVUpCUVVzc1NVRkJTU3hMUVVGVUxFbEJRV3RDTEZsQlFXeENMRVZCUVdkRE8wRkJRelZDTEhkQ1FVRk5MR0ZCUVdFc1lVRkJZU3hMUVVGaUxFTkJRVzVDT3p0QlFVVkJMSGRDUVVGSkxGZEJRVmNzVDBGQldDeERRVUZ0UWl4TlFVRnVRaXhOUVVFclFpeERRVUZETEVOQlFYQkRMRVZCUVhWRE8wRkJRMjVETEdsRFFVRlRMRlZCUVZRN1FVRkRRU3h4UTBGQllTeE5RVUZpTEVOQlFXOUNMRXRCUVhCQ0xFVkJRVEpDTEVOQlFUTkNPMEZCUTBFN1FVRkRTRHM3UVVGRlJEdEJRVU5CTEhkQ1FVRkpMRmRCUVZjc1QwRkJXQ3hEUVVGdFFpeE5RVUZ1UWl4TlFVRXJRaXhEUVVGRExFTkJRWEJETEVWQlFYVkRPMEZCUTI1RExIRkRRVUZoTEV0QlFXSXNTVUZCYzBJc1NVRkJTU3hKUVVGS0xFTkJRVk1zVFVGQlZDeERRVUZuUWl4SFFVRm9RaXhEUVVGdlFpeFhRVUZ3UWl4SlFVRnRReXhSUVVGdVF5eEhRVUU0UXl4VlFVRTVReXhIUVVFeVJDeFZRVUV6UkN4SFFVRjNSU3hoUVVFNVJqdEJRVU5JTEhGQ1FVWkVMRTFCUlU4c1NVRkJTU3hYUVVGWExFMUJRVmdzUTBGQmEwSXNRMEZCUXl4RFFVRnVRaXhOUVVFd1FpeExRVUU1UWl4RlFVRnhRenRCUVVGRk8wRkJRekZETEhGRFFVRmhMRXRCUVdJc1MwRkJkVUlzWVVGQllTeGhRVUZ3UXp0QlFVTklPMEZCUTBvN08wRkJSVVFzZFVKQlFVOHNUMEZCVUN4RFFVRmxMRmxCUVdZc1JVRkJOa0lzV1VGQlRUdEJRVU12UWl3MlFrRkJVeXhQUVVGVUxFTkJRV2xDTEVsQlFVa3NTVUZCU1N4WlFVRktMRU5CUVdsQ0xFMUJRWEpDTEVOQlFUUkNMRkZCUVRWQ0xFVkJRWE5ETEdOQlFYUkRMRVZCUVhORUxGVkJRWFJFTEVOQlFXcENPMEZCUTBnc2FVSkJSa1E3UVVGSFNDeGhRVzVEUkR0QlFXOURTQ3hUUVRsRVJDeERRVGhFUlN4UFFVRlBMRk5CUVZBc1JVRkJhMEk3UVVGRGFFSXNjVUpCUVZNc1RVRkJWQ3hEUVVGblFpeFRRVUZvUWp0QlFVTklPenRCUVVWRUxHVkJRVThzVTBGQlV5eFBRVUZVTEVWQlFWQTdRVUZEU0N4TFFYUkZSRHRCUVhkRlNDeERRV2hMUkN4RlFXZExSeXhKUVVGSkxFbEJRVW9zUTBGQlV5eGhRV2hMV2pzN096czdRVU53UWtFN096czdPenM3T3pzN1FVRlZRVHM3T3pzN096dEJRVTlCTEVOQlFVTXNXVUZCV1RzN1FVRkZWRHM3UVVGRlFUdEJRVU5CT3p0QlFVTkJMRkZCUVVrc1EwRkJReXhQUVVGUExGRkJRVkFzUTBGQlowSXNUVUZCY2tJc1JVRkJOa0k3UVVGRGVrSXNaVUZCVHl4UlFVRlFMRU5CUVdkQ0xFMUJRV2hDTEVkQlFYbENMRTlCUVU4c1VVRkJVQ3hEUVVGblFpeFJRVUZvUWl4SFFVRXlRaXhKUVVFelFpeEhRVU55UWl4UFFVRlBMRkZCUVZBc1EwRkJaMElzVVVGRVN5eEpRVU5QTEU5QlFVOHNVVUZCVUN4RFFVRm5RaXhKUVVGb1FpeEhRVUYxUWl4TlFVRk5MRTlCUVU4c1VVRkJVQ3hEUVVGblFpeEpRVUUzUXl4SFFVRnZSQ3hGUVVRelJDeERRVUY2UWp0QlFVVklPenRCUVVWRU8wRkJRMEU3UVVGRFFTeFJRVUZKTEVOQlFVTXNTMEZCU3l4SFFVRldMRVZCUVdVN1FVRkRXQ3hoUVVGTExFZEJRVXdzUjBGQlZ5eFRRVUZUTEVkQlFWUXNSMEZCWlR0QlFVTjBRaXh0UWtGQlR5eEpRVUZKTEVsQlFVb3NSMEZCVnl4UFFVRllMRVZCUVZBN1FVRkRTQ3hUUVVaRU8wRkJSMGc3UVVGRlNpeERRVzVDUkRzN096czdRVU5xUWtFN096czdPenM3T3pzN1FVRlZRU3hKUVVGSkxFbEJRVW9zUTBGQlV5eFJRVUZVTEVkQlFXOUNMRWxCUVVrc1NVRkJTaXhEUVVGVExGRkJRVlFzU1VGQmNVSXNSVUZCZWtNN08wRkJSVUU3T3pzN096czdRVUZQUVN4RFFVRkRMRlZCUVZVc1QwRkJWaXhGUVVGdFFqczdRVUZGYUVJN08wRkJSVUU3T3pzN096dEJRVXRCTEZGQlFVMHNWMEZCVnl4RlFVRnFRanM3UVVGRlFUczdPenM3TzBGQlRVRXNXVUZCVVN4SFFVRlNMRWRCUVdNc1ZVRkJWU3hKUVVGV0xFVkJRV2RDTEV0QlFXaENMRVZCUVhWQ08wRkJRMnBETzBGQlEwRTdRVUZEUVN4WlFVRkpMRk5CUVZNc1NVRkJWQ3hOUVVGdFFpeFRRVUYyUWl4RlFVRnJRenRCUVVNNVFpeG5Ra0ZCU1N4SlFVRktMRU5CUVZNc1MwRkJWQ3hEUVVGbExFbEJRV1lzUTBGQmIwSXNkVU5CUVhWRExFbEJRWFpETEVkQlFUaERMSGRDUVVGc1JUdEJRVU5JT3p0QlFVVkVMR2xDUVVGVExFbEJRVlFzU1VGQmFVSXNTMEZCYWtJN1FVRkRTQ3hMUVZKRU96dEJRVlZCT3pzN096czdPMEZCVDBFc1dVRkJVU3hIUVVGU0xFZEJRV01zVlVGQlZTeEpRVUZXTEVWQlFXZENPMEZCUXpGQ0xHVkJRVThzVTBGQlV5eEpRVUZVTEVOQlFWQTdRVUZEU0N4TFFVWkVPenRCUVVsQk96czdPenRCUVV0QkxGbEJRVkVzUzBGQlVpeEhRVUZuUWl4WlFVRlpPMEZCUTNoQ0xGbEJRVWtzU1VGQlNTeEpRVUZLTEVOQlFWTXNUVUZCVkN4RFFVRm5RaXhIUVVGb1FpeERRVUZ2UWl4aFFVRndRaXhOUVVGMVF5eGhRVUV6UXl4RlFVRXdSRHRCUVVOMFJDeG5Ra0ZCU1N4SlFVRktMRU5CUVZNc1MwRkJWQ3hEUVVGbExFZEJRV1lzUTBGQmJVSXNhMEpCUVc1Q0xFVkJRWFZETEZGQlFYWkRPMEZCUTBnc1UwRkdSQ3hOUVVWUE8wRkJRMGdzYTBKQlFVMHNTVUZCU1N4TFFVRktMRU5CUVZVc01rUkJRVllzUTBGQlRqdEJRVU5JTzBGQlEwb3NTMEZPUkR0QlFWRklMRU5CYmtSRUxFVkJiVVJITEVsQlFVa3NTVUZCU2l4RFFVRlRMRkZCYmtSYU96czdPenM3T3p0QlEyNUNRVHM3T3pzN096czdPenRCUVZWQk96czdPenM3T3pzN096dEJRVmRCTEVOQlFVTXNXVUZCV1RzN1FVRkZWRHRCUVVOQk8wRkJRMEU3TzBGQlJVRXNWMEZCVHl4VFFVRlFMRWRCUVcxQ0xGTkJRVzVDTzBGQlEwRXNWMEZCVHl4UFFVRlFMRWRCUVdsQ0xGTkJRV3BDT3p0QlFVVkJMRkZCUVVrc1dVRkJTanRCUVVOQkxGRkJRVWtzVlVGQlNqdEJRVU5CTEZGQlFVa3NZVUZCU2p0QlFVTkJMRkZCUVVrc2IwSkJRVW83UVVGRFFTeFJRVUZKTEdsQ1FVRktPMEZCUTBFc1VVRkJTU3haUVVGS08wRkJRMEVzVVVGQlNTd3dRa0ZCU2p0QlFVTkJMRkZCUVVrc2JVSkJRVW83UVVGRFFTeFJRVUZKTEdkQ1FVRktPMEZCUTBFc1VVRkJTU3hWUVVGVkxGRkJRV1E3UVVGRFFTeFJRVUZKTEdsQ1FVRnBRaXhQUVVGeVFqdEJRVU5CTEZGQlFVa3NaMEpCUVdkQ0xFOUJRWEJDTzBGQlEwRXNVVUZCU1N4TFFVRkxMRTlCUVU4c1UwRkJhRUk3UVVGRFFTeFJRVUZKTEZWQlFWVXNSMEZCUnl4UlFVRnFRanRCUVVOQkxGRkJRVWtzVTBGQlV5eEhRVUZITEdOQlFXaENPMEZCUTBFc1VVRkJTU3haUVVGWkxFTkJRVU1zUlVGQlJTeFBRVUZQTEUxQlFWQXNTMEZCYTBJc1YwRkJiRUlzU1VGQmFVTXNUMEZCVHl4VFFVRlFMRXRCUVhGQ0xGZEJRWFJFTEVsQlFYRkZMRTlCUVU4c1VVRkJPVVVzUTBGQmFrSTdRVUZEUVN4UlFVRkpMR05CUVdNc1EwRkJReXhUUVVGRUxFbEJRV01zVDBGQlR5eGhRVUZRTEV0QlFYbENMRmRCUVhwRU8wRkJRMEU3UVVGRFFUdEJRVU5CTEZGQlFVa3NZMEZCWXl4aFFVRmhMRlZCUVZVc1VVRkJWaXhMUVVGMVFpeGxRVUZ3UXl4SFFVRnpSQ3haUVVGMFJDeEhRVUZ4UlN4eFFrRkJka1k3UVVGRFFTeFJRVUZKTEdsQ1FVRnBRaXhIUVVGeVFqdEJRVU5CTzBGQlEwRXNVVUZCU1N4VlFVRlZMRTlCUVU4c1MwRkJVQ3hMUVVGcFFpeFhRVUZxUWl4SlFVRm5ReXhOUVVGTkxGRkJRVTRzVDBGQmNVSXNaMEpCUVc1Rk8wRkJRMEVzVVVGQlNTeFhRVUZYTEVWQlFXWTdRVUZEUVN4UlFVRkpMRTFCUVUwc1JVRkJWanRCUVVOQkxGRkJRVWtzYVVKQlFXbENMRVZCUVhKQ08wRkJRMEVzVVVGQlNTeHBRa0ZCYVVJc1MwRkJja0k3TzBGQlJVRTdRVUZEUVR0QlFVTkJPenRCUVVWQk96czdPenM3TzBGQlQwRXNZVUZCVXl4VlFVRlVMRU5CUVc5Q0xFVkJRWEJDTEVWQlFYZENPMEZCUTNCQ0xHVkJRVThzVVVGQlVTeEpRVUZTTEVOQlFXRXNSVUZCWWl4TlFVRnhRaXh0UWtGQk5VSTdRVUZEU0RzN1FVRkZSRHM3T3pzN096dEJRVTlCTEdGQlFWTXNUMEZCVkN4RFFVRnBRaXhGUVVGcVFpeEZRVUZ4UWp0QlFVTnFRaXhsUVVGUExGRkJRVkVzU1VGQlVpeERRVUZoTEVWQlFXSXNUVUZCY1VJc1owSkJRVFZDTzBGQlEwZzdPMEZCUlVRN096czdPMEZCUzBFc1lVRkJVeXhKUVVGVUxFTkJRV01zUjBGQlpDeEZRVUZ0UWl4SlFVRnVRaXhGUVVGNVFqdEJRVU55UWl4WlFVRkpMRWRCUVVvc1JVRkJVenRCUVVOTUxHbENRVUZMTEVsQlFVa3NTVUZCU1N4RFFVRmlMRVZCUVdkQ0xFbEJRVWtzU1VGQlNTeE5RVUY0UWl4RlFVRm5ReXhMUVVGTExFTkJRWEpETEVWQlFYZERPMEZCUTNCRExHOUNRVUZKTEVsQlFVa3NRMEZCU2l4TFFVRlZMRXRCUVVzc1NVRkJTU3hEUVVGS0xFTkJRVXdzUlVGQllTeERRVUZpTEVWQlFXZENMRWRCUVdoQ0xFTkJRV1FzUlVGQmIwTTdRVUZEYUVNN1FVRkRTRHRCUVVOS08wRkJRMG83UVVGRFNqczdRVUZGUkRzN096czdRVUZMUVN4aFFVRlRMRmRCUVZRc1EwRkJjVUlzUjBGQmNrSXNSVUZCTUVJc1NVRkJNVUlzUlVGQlowTTdRVUZETlVJc1dVRkJTU3hIUVVGS0xFVkJRVk03UVVGRFRDeG5Ra0ZCU1N4VlFVRktPMEZCUTBFc2FVSkJRVXNzU1VGQlNTeEpRVUZKTEUxQlFVb3NSMEZCWVN4RFFVRjBRaXhGUVVGNVFpeEpRVUZKTEVOQlFVTXNRMEZCT1VJc1JVRkJhVU1zUzBGQlN5eERRVUYwUXl4RlFVRjVRenRCUVVOeVF5eHZRa0ZCU1N4SlFVRkpMRU5CUVVvc1MwRkJWU3hMUVVGTExFbEJRVWtzUTBGQlNpeERRVUZNTEVWQlFXRXNRMEZCWWl4RlFVRm5RaXhIUVVGb1FpeERRVUZrTEVWQlFXOURPMEZCUTJoRE8wRkJRMGc3UVVGRFNqdEJRVU5LTzBGQlEwbzdPMEZCUlVRN096czdPenM3TzBGQlVVRXNZVUZCVXl4UFFVRlVMRU5CUVdsQ0xFZEJRV3BDTEVWQlFYTkNMRWxCUVhSQ0xFVkJRVFJDTzBGQlEzaENMR1ZCUVU4c1QwRkJUeXhKUVVGUUxFTkJRVmtzUjBGQldpeEZRVUZwUWl4SlFVRnFRaXhEUVVGUU8wRkJRMGc3TzBGQlJVUTdPenM3T3pzN08wRkJVVUVzWVVGQlV5eE5RVUZVTEVOQlFXZENMRWRCUVdoQ0xFVkJRWEZDTEVsQlFYSkNMRVZCUVRKQ08wRkJRM1pDTEdWQlFVOHNVVUZCVVN4SFFVRlNMRVZCUVdFc1NVRkJZaXhMUVVGelFpeEpRVUZKTEVsQlFVb3NRMEZCTjBJN1FVRkRTRHM3UVVGRlJEczdPenM3UVVGTFFTeGhRVUZUTEZGQlFWUXNRMEZCYTBJc1IwRkJiRUlzUlVGQmRVSXNTVUZCZGtJc1JVRkJOa0k3UVVGRGVrSXNXVUZCU1N4aFFVRktPMEZCUTBFc1lVRkJTeXhKUVVGTUxFbEJRV0VzUjBGQllpeEZRVUZyUWp0QlFVTmtMR2RDUVVGSkxGRkJRVkVzUjBGQlVpeEZRVUZoTEVsQlFXSXNRMEZCU2l4RlFVRjNRanRCUVVOd1FpeHZRa0ZCU1N4TFFVRkxMRWxCUVVrc1NVRkJTaXhEUVVGTUxFVkJRV2RDTEVsQlFXaENMRU5CUVVvc1JVRkJNa0k3UVVGRGRrSTdRVUZEU0R0QlFVTktPMEZCUTBvN1FVRkRTanM3UVVGRlJEczdPenRCUVVsQkxHRkJRVk1zUzBGQlZDeERRVUZsTEUxQlFXWXNSVUZCZFVJc1RVRkJka0lzUlVGQkswSXNTMEZCTDBJc1JVRkJjME1zWlVGQmRFTXNSVUZCZFVRN1FVRkRia1FzV1VGQlNTeE5RVUZLTEVWQlFWazdRVUZEVWl4eFFrRkJVeXhOUVVGVUxFVkJRV2xDTEZWQlFWVXNTMEZCVml4RlFVRnBRaXhKUVVGcVFpeEZRVUYxUWp0QlFVTndReXh2UWtGQlNTeFRRVUZUTEVOQlFVTXNVVUZCVVN4TlFVRlNMRVZCUVdkQ0xFbEJRV2hDTEVOQlFXUXNSVUZCY1VNN1FVRkRha01zZDBKQlFVa3NiVUpCUVcxQ0xGRkJRVThzUzBGQlVDeDVRMEZCVHl4TFFVRlFMRTlCUVdsQ0xGRkJRWEJETEVsQlFXZEVMRXRCUVdoRUxFbEJRMEVzUTBGQlF5eFJRVUZSTEV0QlFWSXNRMEZFUkN4SlFVTnRRaXhEUVVGRExGZEJRVmNzUzBGQldDeERRVVJ3UWl4SlFVVkJMRVZCUVVVc2FVSkJRV2xDTEUxQlFXNUNMRU5CUmtvc1JVRkZaME03TzBGQlJUVkNMRFJDUVVGSkxFTkJRVU1zVDBGQlR5eEpRVUZRTEVOQlFVd3NSVUZCYlVJN1FVRkRaaXh0UTBGQlR5eEpRVUZRTEVsQlFXVXNSVUZCWmp0QlFVTklPMEZCUTBRc09FSkJRVTBzVDBGQlR5eEpRVUZRTEVOQlFVNHNSVUZCYjBJc1MwRkJjRUlzUlVGQk1rSXNTMEZCTTBJc1JVRkJhME1zWlVGQmJFTTdRVUZEU0N4eFFrRlNSQ3hOUVZGUE8wRkJRMGdzSzBKQlFVOHNTVUZCVUN4SlFVRmxMRXRCUVdZN1FVRkRTRHRCUVVOS08wRkJRMG9zWVVGa1JEdEJRV1ZJTzBGQlEwUXNaVUZCVHl4TlFVRlFPMEZCUTBnN08wRkJSVVE3UVVGRFFUdEJRVU5CTEdGQlFWTXNTVUZCVkN4RFFVRmpMRWRCUVdRc1JVRkJiVUlzUlVGQmJrSXNSVUZCZFVJN1FVRkRia0lzWlVGQlR5eFpRVUZaTzBGQlEyWXNiVUpCUVU4c1IwRkJSeXhMUVVGSUxFTkJRVk1zUjBGQlZDeEZRVUZqTEZOQlFXUXNRMEZCVUR0QlFVTklMRk5CUmtRN1FVRkhTRHM3UVVGRlJDeGhRVUZUTEU5QlFWUXNSMEZCYlVJN1FVRkRaaXhsUVVGUExGTkJRVk1zYjBKQlFWUXNRMEZCT0VJc1VVRkJPVUlzUTBGQlVEdEJRVU5JT3p0QlFVVkVMR0ZCUVZNc1kwRkJWQ3hEUVVGM1FpeEhRVUY0UWl4RlFVRTJRanRCUVVONlFpeGpRVUZOTEVkQlFVNDdRVUZEU0RzN1FVRkZSRHRCUVVOQkxHRkJRVk1zVTBGQlZDeERRVUZ0UWl4TFFVRnVRaXhGUVVFd1FqdEJRVU4wUWl4WlFVRkpMRU5CUVVNc1MwRkJUQ3hGUVVGWk8wRkJRMUlzYlVKQlFVOHNTMEZCVUR0QlFVTklPMEZCUTBRc1dVRkJTU3hKUVVGSkxFMUJRVkk3UVVGRFFTeGhRVUZMTEUxQlFVMHNTMEZCVGl4RFFVRlpMRWRCUVZvc1EwRkJUQ3hGUVVGMVFpeFZRVUZWTEVsQlFWWXNSVUZCWjBJN1FVRkRia01zWjBKQlFVa3NSVUZCUlN4SlFVRkdMRU5CUVVvN1FVRkRTQ3hUUVVaRU8wRkJSMEVzWlVGQlR5eERRVUZRTzBGQlEwZzdPMEZCUlVRN096czdPenM3T3p0QlFWTkJMR0ZCUVZNc1UwRkJWQ3hEUVVGdFFpeEZRVUZ1UWl4RlFVRjFRaXhIUVVGMlFpeEZRVUUwUWl4SFFVRTFRaXhGUVVGcFF5eGpRVUZxUXl4RlFVRnBSRHRCUVVNM1F5eFpRVUZOTEZGQlFWRXNTVUZCU1N4TFFVRktMRU5CUVZVc1RVRkJUU3d3UTBGQlRpeEhRVUZ0UkN4RlFVRTNSQ3hEUVVGa096dEJRVVZCTEdOQlFVMHNWMEZCVGl4SFFVRnZRaXhGUVVGd1FqdEJRVU5CTEdOQlFVMHNZMEZCVGl4SFFVRjFRaXhqUVVGMlFqczdRVUZGUVN4WlFVRkpMRWRCUVVvc1JVRkJVenRCUVVOTUxHdENRVUZOTEdGQlFVNHNSMEZCYzBJc1IwRkJkRUk3UVVGRFNEczdRVUZGUkN4bFFVRlBMRXRCUVZBN1FVRkRTRHM3UVVGRlJDeFJRVUZKTEU5QlFVOHNUMEZCVHl4VFFVRmtMRXRCUVRSQ0xGZEJRV2hETEVWQlFUWkRPMEZCUTNwRExGbEJRVWtzVjBGQlZ5eFBRVUZQTEZOQlFXeENMRU5CUVVvc1JVRkJhME03UVVGRE9VSTdRVUZEUVR0QlFVTklPMEZCUTBRc1kwRkJUU3hQUVVGUExGTkJRV0k3UVVGRFFTeGxRVUZQTEZOQlFWQXNSMEZCYlVJc1UwRkJia0k3UVVGRFNEczdRVUZGUkR0QlFVTkJMRkZCUVVrc1QwRkJUeXhQUVVGUExFOUJRV1FzUzBGQk1FSXNWMEZCTVVJc1NVRkJlVU1zUTBGQlF5eFhRVUZYTEU5QlFVOHNUMEZCYkVJc1EwRkJPVU1zUlVGQk1FVTdRVUZEZEVVN1FVRkRRU3hqUVVGTkxFOUJRVThzVDBGQllqdEJRVU5CTEdWQlFVOHNUMEZCVUN4SFFVRnBRaXhUUVVGcVFqdEJRVU5JT3p0QlFVVkVMR0ZCUVZNc1ZVRkJWQ3hEUVVGdlFpeFhRVUZ3UWl4RlFVRnBRenRCUVVNM1FpeFpRVUZKTEhOQ1FVRktPMEZCUVVFc1dVRkJiVUlzWlVGQmJrSTdRVUZCUVN4WlFVRXlRaXhuUWtGQk0wSTdRVUZCUVN4WlFVRnZReXhwUWtGQmNFTTdRVUZCUVN4WlFVTkpMRFpDUVVSS08wRkJRVUVzV1VGRlNTeFZRVUZUTzBGQlEwdzdRVUZEUVR0QlFVTkJPMEZCUTBFc2VVSkJRV0VzUTBGS1VqdEJRVXRNTEhGQ1FVRlRMRWxCVEVvN1FVRk5UQ3h0UWtGQlR5eEZRVTVHTzBGQlQwd3NjVUpCUVZNc1JVRlFTanRCUVZGTUxHdENRVUZOTEVWQlVrUTdRVUZUVEN4clFrRkJUU3hGUVZSRU8wRkJWVXdzYjBKQlFWRTdRVUZXU0N4VFFVWmlPMEZCUVVFc1dVRmpTU3hYUVVGWExFVkJaR1k3TzBGQlpVazdRVUZEUVR0QlFVTkJPMEZCUTBFc01FSkJRV3RDTEVWQmJFSjBRanRCUVVGQkxGbEJiVUpKTEdOQlFXTXNSVUZ1UW14Q08wRkJRVUVzV1VGdlFra3NWMEZCVnl4RlFYQkNaanRCUVVGQkxGbEJjVUpKTEZkQlFWVXNSVUZ5UW1RN1FVRkJRU3haUVhOQ1NTeGhRVUZoTEVWQmRFSnFRanRCUVVGQkxGbEJkVUpKTEdGQlFXRXNSVUYyUW1wQ08wRkJRVUVzV1VGM1Fra3NhVUpCUVdsQ0xFTkJlRUp5UWp0QlFVRkJMRmxCZVVKSkxITkNRVUZ6UWl4RFFYcENNVUk3TzBGQk1rSkJPenM3T3pzN096czdPenRCUVZkQkxHbENRVUZUTEZGQlFWUXNRMEZCYTBJc1IwRkJiRUlzUlVGQmRVSTdRVUZEYmtJc1owSkJRVWtzVlVGQlNqdEJRVUZCTEdkQ1FVRlBMR0ZCUVZBN1FVRkRRU3hwUWtGQlN5eEpRVUZKTEVOQlFWUXNSVUZCV1N4SlFVRkpMRWxCUVVrc1RVRkJjRUlzUlVGQk5FSXNSMEZCTlVJc1JVRkJhVU03UVVGRE4wSXNkVUpCUVU4c1NVRkJTU3hEUVVGS0xFTkJRVkE3UVVGRFFTeHZRa0ZCU1N4VFFVRlRMRWRCUVdJc1JVRkJhMEk3UVVGRFpDeDNRa0ZCU1N4TlFVRktMRU5CUVZjc1EwRkJXQ3hGUVVGakxFTkJRV1E3UVVGRFFTeDVRa0ZCU3l4RFFVRk1PMEZCUTBnc2FVSkJTRVFzVFVGSFR5eEpRVUZKTEZOQlFWTXNTVUZCWWl4RlFVRnRRanRCUVVOMFFqdEJRVU5CTzBGQlEwRTdRVUZEUVN4M1FrRkJTU3hOUVVGTkxFTkJRVTRzU1VGQldTeE5RVUZOTEVOQlFVNHNTVUZCVnl4SlFVRkpMRU5CUVVvc1RVRkJWeXhKUVVGc1F5eEpRVUV5UXl4SlFVRkpMRWxCUVVrc1EwRkJVaXhOUVVGbExFbEJRVGxFTEVWQlFXOUZPMEZCUTJoRk8wRkJRMGdzY1VKQlJrUXNUVUZGVHl4SlFVRkpMRWxCUVVrc1EwRkJVaXhGUVVGWE8wRkJRMlFzTkVKQlFVa3NUVUZCU2l4RFFVRlhMRWxCUVVrc1EwRkJaaXhGUVVGclFpeERRVUZzUWp0QlFVTkJMRFpDUVVGTExFTkJRVXc3UVVGRFNEdEJRVU5LTzBGQlEwbzdRVUZEU2pzN1FVRkZSRHM3T3pzN096czdPenRCUVZWQkxHbENRVUZUTEZOQlFWUXNRMEZCYlVJc1NVRkJia0lzUlVGQmVVSXNVVUZCZWtJc1JVRkJiVU1zVVVGQmJrTXNSVUZCTmtNN1FVRkRla01zWjBKQlFVa3NaMEpCUVVvN1FVRkJRU3huUWtGQllTeHBRa0ZCWWp0QlFVRkJMR2RDUVVGMVFpeHJRa0ZCZGtJN1FVRkJRU3huUWtGQmEwTXNWVUZCYkVNN1FVRkJRU3huUWtGQmNVTXNWVUZCY2tNN1FVRkJRU3huUWtGQmQwTXNiMEpCUVhoRE8wRkJRVUVzWjBKQlFYRkVMR3RDUVVGeVJEdEJRVUZCTEdkQ1FVTkpMR2xDUVVSS08wRkJRVUVzWjBKQlEyTXNaVUZFWkR0QlFVRkJMR2RDUVVOelFpeHhRa0ZFZEVJN1FVRkJRU3huUWtGRGIwTXNZMEZFY0VNN1FVRkJRU3huUWtGRE1rTXNORUpCUkRORE8wRkJRVUVzWjBKQlJVa3NXVUZCWVN4WlFVRlpMRk5CUVZNc1MwRkJWQ3hEUVVGbExFZEJRV1lzUTBGR04wSTdRVUZCUVN4blFrRkhTU3hOUVVGTkxGRkJRVThzUjBGSWFrSTdRVUZCUVN4blFrRkpTU3hWUVVGVkxFOUJRVThzU1VGQlNTeEhRVUZLTEVOQlNuSkNPenRCUVUxQk8wRkJRMEVzWjBKQlFVa3NTVUZCU2l4RlFVRlZPMEZCUTA0c2RVSkJRVThzUzBGQlN5eExRVUZNTEVOQlFWY3NSMEZCV0N4RFFVRlFPMEZCUTBFc05FSkJRVmtzUzBGQlN5eE5RVUZNTEVkQlFXTXNRMEZCTVVJN08wRkJSVUU3UVVGRFFUdEJRVU5CTEc5Q1FVRkpMRkZCUVU4c1dVRkJVQ3hKUVVGMVFpeGxRVUZsTEVsQlFXWXNRMEZCYjBJc1MwRkJTeXhUUVVGTUxFTkJRWEJDTEVOQlFUTkNMRVZCUVdsRk8wRkJRemRFTEhsQ1FVRkxMRk5CUVV3c1NVRkJhMElzUzBGQlN5eFRRVUZNTEVWQlFXZENMRTlCUVdoQ0xFTkJRWGRDTEdOQlFYaENMRVZCUVhkRExFVkJRWGhETEVOQlFXeENPMEZCUTBnN08wRkJSVVE3UVVGRFFTeHZRa0ZCU1N4TFFVRkxMRU5CUVV3c1JVRkJVU3hOUVVGU0xFTkJRV1VzUTBGQlppeE5RVUZ6UWl4SFFVRjBRaXhKUVVFMlFpeFRRVUZxUXl4RlFVRTBRenRCUVVONFF6dEJRVU5CTzBGQlEwRTdRVUZEUVN3d1EwRkJjMElzVlVGQlZTeExRVUZXTEVOQlFXZENMRU5CUVdoQ0xFVkJRVzFDTEZWQlFWVXNUVUZCVml4SFFVRnRRaXhEUVVGMFF5eERRVUYwUWp0QlFVTkJMREpDUVVGUExHOUNRVUZ2UWl4TlFVRndRaXhEUVVFeVFpeEpRVUV6UWl4RFFVRlFPMEZCUTBnN08wRkJSVVFzZVVKQlFWTXNTVUZCVkR0QlFVTkJMSFZDUVVGUExFdEJRVXNzU1VGQlRDeERRVUZWTEVkQlFWWXNRMEZCVUR0QlFVTklPenRCUVVWRU8wRkJRMEVzWjBKQlFVa3NXVUZCV1N4SFFVRmFMRXRCUVc5Q0xHRkJRV0VzVDBGQmFrTXNRMEZCU2l4RlFVRXJRenRCUVVNelF5dzBRa0ZCV1N4TFFVRkxMRXRCUVV3c1EwRkJWeXhIUVVGWUxFTkJRVm83TzBGQlJVRXNNa0pCUVZjc1MwRkJTeXhKUVVGSkxGVkJRVlVzVFVGQmJrSXNSVUZCTWtJc1NVRkJTU3hEUVVFdlFpeEZRVUZyUXl4TFFVRkxMRU5CUVhaRExFVkJRVEJETzBGQlEycEVMR3REUVVGakxGVkJRVlVzUzBGQlZpeERRVUZuUWl4RFFVRm9RaXhGUVVGdFFpeERRVUZ1UWl4RlFVRnpRaXhKUVVGMFFpeERRVUV5UWl4SFFVRXpRaXhEUVVGa096dEJRVVZCTEhkQ1FVRkpMRk5CUVVvc1JVRkJaVHRCUVVOWU8wRkJRMEU3UVVGRFFTdzJRa0ZCU3l4SlFVRkpMRlZCUVZVc1RVRkJia0lzUlVGQk1rSXNTVUZCU1N4RFFVRXZRaXhGUVVGclF5eExRVUZMTEVOQlFYWkRMRVZCUVRCRE8wRkJRM1JETEhWRFFVRlhMRTlCUVU4c1IwRkJVQ3hGUVVGWkxGVkJRVlVzUzBGQlZpeERRVUZuUWl4RFFVRm9RaXhGUVVGdFFpeERRVUZ1UWl4RlFVRnpRaXhKUVVGMFFpeERRVUV5UWl4SFFVRXpRaXhEUVVGYUxFTkJRVmc3TzBGQlJVRTdRVUZEUVN4blEwRkJTU3hSUVVGS0xFVkJRV003UVVGRFZpd3lRMEZCVnl4UFFVRlBMRkZCUVZBc1JVRkJhVUlzVjBGQmFrSXNRMEZCV0R0QlFVTkJMRzlEUVVGSkxGRkJRVW9zUlVGQll6dEJRVU5XTzBGQlEwRXNLME5CUVZjc1VVRkJXRHRCUVVOQkxEWkRRVUZUTEVOQlFWUTdRVUZEUVN3d1EwRkJUU3hUUVVGT08wRkJRMGc3UVVGRFNqdEJRVU5LTzBGQlEwbzdPMEZCUlVRN1FVRkRRVHRCUVVOQkxIZENRVUZKTEVOQlFVTXNXVUZCUkN4SlFVRnBRaXhQUVVGcVFpeEpRVUUwUWl4UFFVRlBMRTlCUVZBc1JVRkJaMElzVjBGQmFFSXNRMEZCYUVNc1JVRkJPRVE3UVVGRE1VUXNkVU5CUVdVc1QwRkJUeXhQUVVGUUxFVkJRV2RDTEZkQlFXaENMRU5CUVdZN1FVRkRRU3huUTBGQlVTeERRVUZTTzBGQlEwZzdRVUZEU2pzN1FVRkZSQ3h2UWtGQlNTeERRVUZETEZGQlFVUXNTVUZCWVN4WlFVRnFRaXhGUVVFclFqdEJRVU16UWl3clFrRkJWeXhaUVVGWU8wRkJRMEVzTmtKQlFWTXNTMEZCVkR0QlFVTklPenRCUVVWRUxHOUNRVUZKTEZGQlFVb3NSVUZCWXp0QlFVTldMRGhDUVVGVkxFMUJRVllzUTBGQmFVSXNRMEZCYWtJc1JVRkJiMElzVFVGQmNFSXNSVUZCTkVJc1VVRkJOVUk3UVVGRFFTd3lRa0ZCVHl4VlFVRlZMRWxCUVZZc1EwRkJaU3hIUVVGbUxFTkJRVkE3UVVGRFNEdEJRVU5LT3p0QlFVVkVPMEZCUTBFc2MwSkJRVlVzVDBGQlR5eFJRVUZQTEVsQlFXUXNSVUZCYjBJc1NVRkJjRUlzUTBGQlZqczdRVUZGUVN4dFFrRkJUeXhWUVVGVkxFOUJRVllzUjBGQmIwSXNTVUZCTTBJN1FVRkRTRHM3UVVGRlJDeHBRa0ZCVXl4WlFVRlVMRU5CUVhOQ0xFbEJRWFJDTEVWQlFUUkNPMEZCUTNoQ0xHZENRVUZKTEZOQlFVb3NSVUZCWlR0QlFVTllMSEZDUVVGTExGTkJRVXdzUlVGQlowSXNWVUZCVlN4VlFVRldMRVZCUVhOQ08wRkJRMnhETEhkQ1FVRkpMRmRCUVZjc1dVRkJXQ3hEUVVGM1FpeHZRa0ZCZUVJc1RVRkJhMFFzU1VGQmJFUXNTVUZEUVN4WFFVRlhMRmxCUVZnc1EwRkJkMElzY1VKQlFYaENMRTFCUVcxRUxGRkJRVkVzVjBGRUwwUXNSVUZETkVVN1FVRkRlRVVzYlVOQlFWY3NWVUZCV0N4RFFVRnpRaXhYUVVGMFFpeERRVUZyUXl4VlFVRnNRenRCUVVOQkxDdENRVUZQTEVsQlFWQTdRVUZEU0R0QlFVTktMR2xDUVU1RU8wRkJUMGc3UVVGRFNqczdRVUZGUkN4cFFrRkJVeXhsUVVGVUxFTkJRWGxDTEVWQlFYcENMRVZCUVRaQ08wRkJRM3BDTEdkQ1FVRkpMR0ZCUVdFc1QwRkJUeXhSUVVGUExFdEJRV1FzUlVGQmNVSXNSVUZCY2tJc1EwRkJha0k3UVVGRFFTeG5Ra0ZCU1N4alFVRmpMRkZCUVZFc1ZVRkJVaXhEUVVGa0xFbEJRWEZETEZkQlFWY3NUVUZCV0N4SFFVRnZRaXhEUVVFM1JDeEZRVUZuUlR0QlFVTTFSRHRCUVVOQkxESkNRVUZYTEV0QlFWZzdRVUZEUVN4M1FrRkJVU3hQUVVGU0xFTkJRV2RDTEV0QlFXaENMRU5CUVhOQ0xFVkJRWFJDT3p0QlFVVkJPMEZCUTBFc2QwSkJRVkVzVjBGQlVpeERRVUZ2UWl4SlFVRndRaXhGUVVFd1FqdEJRVU4wUWl3MlFrRkJVenRCUVVSaExHbENRVUV4UWl4RlFVVkhMRU5CUVVNc1JVRkJSQ3hEUVVaSU96dEJRVWxCTEhWQ1FVRlBMRWxCUVZBN1FVRkRTRHRCUVVOS096dEJRVVZFTzBGQlEwRTdRVUZEUVN4cFFrRkJVeXhYUVVGVUxFTkJRWEZDTEVsQlFYSkNMRVZCUVRKQ08wRkJRM1pDTEdkQ1FVRkpMR1ZCUVVvN1FVRkJRU3huUWtGRFNTeFJRVUZSTEU5QlFVOHNTMEZCU3l4UFFVRk1MRU5CUVdFc1IwRkJZaXhEUVVGUUxFZEJRVEpDTEVOQlFVTXNRMEZFZUVNN1FVRkZRU3huUWtGQlNTeFJRVUZSTEVOQlFVTXNRMEZCWWl4RlFVRm5RanRCUVVOYUxIbENRVUZUTEV0QlFVc3NVMEZCVEN4RFFVRmxMRU5CUVdZc1JVRkJhMElzUzBGQmJFSXNRMEZCVkR0QlFVTkJMSFZDUVVGUExFdEJRVXNzVTBGQlRDeERRVUZsTEZGQlFWRXNRMEZCZGtJc1JVRkJNRUlzUzBGQlN5eE5RVUV2UWl4RFFVRlFPMEZCUTBnN1FVRkRSQ3h0UWtGQlR5eERRVUZETEUxQlFVUXNSVUZCVXl4SlFVRlVMRU5CUVZBN1FVRkRTRHM3UVVGRlJEczdPenM3T3pzN096czdPMEZCV1VFc2FVSkJRVk1zWVVGQlZDeERRVUYxUWl4SlFVRjJRaXhGUVVFMlFpeGxRVUUzUWl4RlFVRTRReXhaUVVFNVF5eEZRVUUwUkN4UlFVRTFSQ3hGUVVGelJUdEJRVU5zUlN4blFrRkJTU3haUVVGS08wRkJRVUVzWjBKQlFWTXNjVUpCUVZRN1FVRkJRU3huUWtGQmRVSXNaVUZCZGtJN1FVRkJRU3huUWtGQkswSXNhMEpCUVM5Q08wRkJRVUVzWjBKQlEwa3NVMEZCVXl4SlFVUmlPMEZCUVVFc1owSkJSVWtzWVVGQllTeHJRa0ZCYTBJc1owSkJRV2RDTEVsQlFXeERMRWRCUVhsRExFbEJSakZFTzBGQlFVRXNaMEpCUjBrc1pVRkJaU3hKUVVodVFqdEJRVUZCTEdkQ1FVbEpMRmRCUVZjc1NVRktaanRCUVVGQkxHZENRVXRKTEdsQ1FVRnBRaXhGUVV4eVFqczdRVUZQUVR0QlFVTkJPMEZCUTBFc1owSkJRVWtzUTBGQlF5eEpRVUZNTEVWQlFWYzdRVUZEVUN3eVFrRkJWeXhMUVVGWU8wRkJRMEVzZFVKQlFVOHNVMEZCVXl4clFrRkJhMElzUTBGQk0wSXNRMEZCVUR0QlFVTklPenRCUVVWRUxIZENRVUZaTEZsQlFWa3NTVUZCV2l4RFFVRmFPMEZCUTBFc2NVSkJRVk1zVlVGQlZTeERRVUZXTEVOQlFWUTdRVUZEUVN4dFFrRkJUeXhWUVVGVkxFTkJRVllzUTBGQlVEczdRVUZGUVN4blFrRkJTU3hOUVVGS0xFVkJRVms3UVVGRFVpeDVRa0ZCVXl4VlFVRlZMRTFCUVZZc1JVRkJhMElzVlVGQmJFSXNSVUZCT0VJc1VVRkJPVUlzUTBGQlZEdEJRVU5CTEN0Q1FVRmxMRTlCUVU4c1VVRkJVQ3hGUVVGblFpeE5RVUZvUWl4RFFVRm1PMEZCUTBnN08wRkJSVVE3UVVGRFFTeG5Ra0ZCU1N4SlFVRktMRVZCUVZVN1FVRkRUaXh2UWtGQlNTeE5RVUZLTEVWQlFWazdRVUZEVWl4M1FrRkJTU3huUWtGQlowSXNZVUZCWVN4VFFVRnFReXhGUVVFMFF6dEJRVU40UXp0QlFVTkJMSGxEUVVGcFFpeGhRVUZoTEZOQlFXSXNRMEZCZFVJc1NVRkJka0lzUlVGQk5rSXNWVUZCVlN4SlFVRldMRVZCUVdkQ08wRkJRekZFTEcxRFFVRlBMRlZCUVZVc1NVRkJWaXhGUVVGblFpeFZRVUZvUWl4RlFVRTBRaXhSUVVFMVFpeERRVUZRTzBGQlEwZ3NlVUpCUm1kQ0xFTkJRV3BDTzBGQlIwZ3NjVUpCVEVRc1RVRkxUenRCUVVOSU8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFc2VVTkJRV2xDTEV0QlFVc3NUMEZCVEN4RFFVRmhMRWRCUVdJc1RVRkJjMElzUTBGQlF5eERRVUYyUWl4SFFVTmlMRlZCUVZVc1NVRkJWaXhGUVVGblFpeFZRVUZvUWl4RlFVRTBRaXhSUVVFMVFpeERRVVJoTEVkQlJXSXNTVUZHU2p0QlFVZElPMEZCUTBvc2FVSkJiRUpFTEUxQmEwSlBPMEZCUTBnN1FVRkRRU3h4UTBGQmFVSXNWVUZCVlN4SlFVRldMRVZCUVdkQ0xGVkJRV2hDTEVWQlFUUkNMRkZCUVRWQ0xFTkJRV3BDT3p0QlFVVkJPMEZCUTBFN1FVRkRRVHRCUVVOQkxHZERRVUZaTEZsQlFWa3NZMEZCV2l4RFFVRmFPMEZCUTBFc05rSkJRVk1zVlVGQlZTeERRVUZXTEVOQlFWUTdRVUZEUVN4eFEwRkJhVUlzVlVGQlZTeERRVUZXTEVOQlFXcENPMEZCUTBFc2JVTkJRV1VzU1VGQlpqczdRVUZGUVN3d1FrRkJUU3hSUVVGUkxGTkJRVklzUTBGQmEwSXNZMEZCYkVJc1EwRkJUanRCUVVOSU8wRkJRMG83TzBGQlJVUTdRVUZEUVR0QlFVTkJPMEZCUTBFc2NVSkJRVk1zVlVGQlZTeERRVUZETEZsQlFWZ3NTVUZCTWtJc1EwRkJReXhaUVVFMVFpeEhRVU5NTEcxQ1FVRnRRaXgxUWtGQmRVSXNRMEZCTVVNc1EwRkVTeXhIUVVWTUxFVkJSa283TzBGQlNVRXNiVUpCUVU4N1FVRkRTQ3gzUWtGQlVTeE5RVVJNTzBGQlJVZ3NjMEpCUVUwc1kwRkdTRHRCUVVkSUxESkNRVUZYTEdWQlNGSTdRVUZKU0N3NFFrRkJZeXhEUVVGRExFTkJRVU1zVFVGS1lqdEJRVXRJTEhGQ1FVRkxMRWRCVEVZN1FVRk5TQ3c0UWtGQll5eFpRVTVZTzBGQlQwZ3NNRUpCUVZVc1VVRlFVRHRCUVZGSUxHOUNRVUZKTEVOQlFVTXNVMEZEUkN4VFFVRlRMRWRCUVZRc1IwRkJaU3hqUVVSa0xFZEJSVVFzWTBGR1FTeEpRVVZyUWp0QlFWWnVRaXhoUVVGUU8wRkJXVWc3TzBGQlJVUXNhVUpCUVZNc1UwRkJWQ3hEUVVGdFFpeE5RVUZ1UWl4RlFVRXlRanRCUVVOMlFpeG5Ra0ZCU1N4TFFVRkxMRTlCUVU4c1JVRkJhRUk3UVVGQlFTeG5Ra0ZEU1N4TlFVRk5MRTlCUVU4c1VVRkJVQ3hGUVVGcFFpeEZRVUZxUWl4RFFVUldPenRCUVVkQkxHZENRVUZKTEVOQlFVTXNSMEZCVEN4RlFVRlZPMEZCUTA0c2MwSkJRVTBzVTBGQlV5eEZRVUZVTEVsQlFXVXNTVUZCU1N4UlFVRlJMRTFCUVZvc1EwRkJiVUlzVFVGQmJrSXNRMEZCY2tJN1FVRkRTRHM3UVVGRlJDeHRRa0ZCVHl4SFFVRlFPMEZCUTBnN08wRkJSVVFzYVVKQlFWTXNSVUZCVkN4RFFVRlpMRTFCUVZvc1JVRkJiMElzU1VGQmNFSXNSVUZCTUVJc1JVRkJNVUlzUlVGQk9FSTdRVUZETVVJc1owSkJRVWtzUzBGQlN5eFBRVUZQTEVWQlFXaENPMEZCUVVFc1owSkJRMGtzVFVGQlRTeFBRVUZQTEZGQlFWQXNSVUZCYVVJc1JVRkJha0lzUTBGRVZqczdRVUZIUVN4blFrRkJTU3hSUVVGUkxGRkJRVklzUlVGQmFVSXNSVUZCYWtJc1RVRkRReXhEUVVGRExFZEJRVVFzU1VGQlVTeEpRVUZKTEd0Q1FVUmlMRU5CUVVvc1JVRkRjME03UVVGRGJFTXNiMEpCUVVrc1UwRkJVeXhUUVVGaUxFVkJRWGRDTzBGQlEzQkNMSFZDUVVGSExGTkJRVkVzUlVGQlVpeERRVUZJTzBGQlEwZzdRVUZEU2l4aFFVeEVMRTFCUzA4N1FVRkRTQ3h6UWtGQlRTeFZRVUZWTEUxQlFWWXNRMEZCVGp0QlFVTkJMRzlDUVVGSkxFbEJRVWtzUzBGQlNpeEpRVUZoTEZOQlFWTXNUMEZCTVVJc1JVRkJiVU03UVVGREwwSXNkVUpCUVVjc1NVRkJTU3hMUVVGUU8wRkJRMGdzYVVKQlJrUXNUVUZGVHp0QlFVTklMSGRDUVVGSkxFVkJRVW9zUTBGQlR5eEpRVUZRTEVWQlFXRXNSVUZCWWp0QlFVTklPMEZCUTBvN1FVRkRTanM3UVVGRlJDeHBRa0ZCVXl4UFFVRlVMRU5CUVdsQ0xFZEJRV3BDTEVWQlFYTkNMRTlCUVhSQ0xFVkJRU3RDTzBGQlF6TkNMR2RDUVVGSkxFMUJRVTBzU1VGQlNTeGpRVUZrTzBGQlFVRXNaMEpCUTBrc1YwRkJWeXhMUVVSbU96dEJRVWRCTEdkQ1FVRkpMRTlCUVVvc1JVRkJZVHRCUVVOVUxIZENRVUZSTEVkQlFWSTdRVUZEU0N4aFFVWkVMRTFCUlU4N1FVRkRTQ3h4UWtGQlN5eEhRVUZNTEVWQlFWVXNWVUZCVlN4RlFVRldMRVZCUVdNN1FVRkRjRUlzZDBKQlFVa3NUVUZCVFN4UFFVRlBMRkZCUVZBc1JVRkJhVUlzUlVGQmFrSXNRMEZCVmp0QlFVTkJMSGRDUVVGSkxFZEJRVW9zUlVGQlV6dEJRVU5NTzBGQlEwRXNORUpCUVVrc1MwRkJTaXhIUVVGWkxFZEJRVm83UVVGRFFTdzBRa0ZCU1N4SlFVRkpMRTFCUVVvc1EwRkJWeXhMUVVGbUxFVkJRWE5DTzBGQlEyeENMSFZEUVVGWExFbEJRVmc3UVVGRFFTeG5RMEZCU1N4SlFVRktMRU5CUVZNc1QwRkJWQ3hGUVVGclFpeEhRVUZzUWp0QlFVTklPMEZCUTBvN1FVRkRTaXhwUWtGV1JEczdRVUZaUVN4dlFrRkJTU3hEUVVGRExGRkJRVXdzUlVGQlpUdEJRVU5ZTEhkQ1FVRkpMRTlCUVVvc1EwRkJXU3hIUVVGYU8wRkJRMGc3UVVGRFNqdEJRVU5LT3p0QlFVVkVPenM3TzBGQlNVRXNhVUpCUVZNc1pVRkJWQ3hIUVVFeVFqdEJRVU4yUWp0QlFVTkJMR2RDUVVGSkxHVkJRV1VzVFVGQmJrSXNSVUZCTWtJN1FVRkRka0lzY1VKQlFVc3NZMEZCVEN4RlFVRnhRaXhWUVVGVkxGTkJRVllzUlVGQmNVSTdRVUZEZEVNc2QwSkJRVWtzUzBGQlN5eFZRVUZWTEVOQlFWWXNRMEZCVkR0QlFVTkJMSGRDUVVGSkxFOUJRVThzUlVGQlVDeExRVUZqTEZGQlFXeENMRVZCUVRSQ08wRkJRM2hDTEdkRFFVRlJMRmRCUVZJc1EwRkJiMElzUlVGQmNFSXNTVUZCTUVJc1NVRkJNVUk3UVVGRFNEdEJRVU5FTERaQ1FVRlRMRWxCUVZRc1EwRkJZeXhUUVVGa08wRkJRMGdzYVVKQlRrUTdRVUZQUVN4cFEwRkJhVUlzUlVGQmFrSTdRVUZEU0R0QlFVTktPenRCUVVWRUxHMUNRVUZYTzBGQlExQXNkVUpCUVZjc2FVSkJRVlVzUjBGQlZpeEZRVUZsTzBGQlEzUkNMRzlDUVVGSkxFbEJRVWtzVDBGQlVpeEZRVUZwUWp0QlFVTmlMREpDUVVGUExFbEJRVWtzVDBGQldEdEJRVU5JTEdsQ1FVWkVMRTFCUlU4N1FVRkRTQ3d5UWtGQlVTeEpRVUZKTEU5QlFVb3NSMEZCWXl4UlFVRlJMRmRCUVZJc1EwRkJiMElzU1VGQlNTeEhRVUY0UWl4RFFVRjBRanRCUVVOSU8wRkJRMG9zWVVGUVRUdEJRVkZRTEhWQ1FVRlhMR2xDUVVGVkxFZEJRVllzUlVGQlpUdEJRVU4wUWl4dlFrRkJTU3haUVVGS0xFZEJRVzFDTEVsQlFXNUNPMEZCUTBFc2IwSkJRVWtzU1VGQlNTeEhRVUZLTEVOQlFWRXNVVUZCV2l4RlFVRnpRanRCUVVOc1FpeDNRa0ZCU1N4SlFVRkpMRTlCUVZJc1JVRkJhVUk3UVVGRFlpd3JRa0ZCVVN4VFFVRlJMRWxCUVVrc1IwRkJTaXhEUVVGUkxFVkJRV2hDTEVsQlFYTkNMRWxCUVVrc1QwRkJiRU03UVVGRFNDeHhRa0ZHUkN4TlFVVlBPMEZCUTBnc0swSkJRVkVzU1VGQlNTeFBRVUZLTEVkQlFXTXNVMEZCVVN4SlFVRkpMRWRCUVVvc1EwRkJVU3hGUVVGb1FpeEpRVUZ6UWl4RlFVRTFRenRCUVVOSU8wRkJRMG83UVVGRFNpeGhRV3BDVFR0QlFXdENVQ3h6UWtGQlZTeG5Ra0ZCVlN4SFFVRldMRVZCUVdVN1FVRkRja0lzYjBKQlFVa3NTVUZCU1N4TlFVRlNMRVZCUVdkQ08wRkJRMW9zTWtKQlFVOHNTVUZCU1N4TlFVRllPMEZCUTBnc2FVSkJSa1FzVFVGRlR6dEJRVU5JTERKQ1FVRlJMRWxCUVVrc1RVRkJTaXhIUVVGaE8wRkJRMnBDTERSQ1FVRkpMRWxCUVVrc1IwRkJTaXhEUVVGUkxFVkJSRXM3UVVGRmFrSXNOa0pCUVVzc1NVRkJTU3hIUVVGS0xFTkJRVkVzUjBGR1NUdEJRVWRxUWl4blEwRkJVU3hyUWtGQldUdEJRVU5vUWl4dFEwRkJUeXhQUVVGUExGRkJRVThzVFVGQlpDeEZRVUZ6UWl4SlFVRkpMRWRCUVVvc1EwRkJVU3hGUVVFNVFpeExRVUZ4UXl4RlFVRTFRenRCUVVOSUxIbENRVXhuUWp0QlFVMXFRaXhwUTBGQlV5eEpRVUZKTEU5QlFVb3NTMEZCWjBJc1NVRkJTU3hQUVVGS0xFZEJRV01zUlVGQk9VSTdRVUZPVVN4eFFrRkJja0k3UVVGUlNEdEJRVU5LTzBGQkwwSk5MRk5CUVZnN08wRkJhME5CTEdsQ1FVRlRMR0ZCUVZRc1EwRkJkVUlzUlVGQmRrSXNSVUZCTWtJN1FVRkRka0k3UVVGRFFTeHRRa0ZCVHl4VFFVRlRMRVZCUVZRc1EwRkJVRHRCUVVOQkxHMUNRVUZQTEdkQ1FVRm5RaXhGUVVGb1FpeERRVUZRTzBGQlEwZzdPMEZCUlVRc2FVSkJRVk1zVlVGQlZDeERRVUZ2UWl4SFFVRndRaXhGUVVGNVFpeE5RVUY2UWl4RlFVRnBReXhUUVVGcVF5eEZRVUUwUXp0QlFVTjRReXhuUWtGQlNTeExRVUZMTEVsQlFVa3NSMEZCU2l4RFFVRlJMRVZCUVdwQ096dEJRVVZCTEdkQ1FVRkpMRWxCUVVrc1MwRkJVaXhGUVVGbE8wRkJRMWdzYjBKQlFVa3NTVUZCU2l4RFFVRlRMRTlCUVZRc1JVRkJhMElzU1VGQlNTeExRVUYwUWp0QlFVTklMR0ZCUmtRc1RVRkZUenRCUVVOSUxIVkNRVUZQTEVWQlFWQXNTVUZCWVN4SlFVRmlPMEZCUTBFc2NVSkJRVXNzU1VGQlNTeFBRVUZVTEVWQlFXdENMRlZCUVZVc1RVRkJWaXhGUVVGclFpeERRVUZzUWl4RlFVRnhRanRCUVVOdVF5eDNRa0ZCU1N4UlFVRlJMRTlCUVU4c1JVRkJia0k3UVVGQlFTeDNRa0ZEU1N4TlFVRk5MRTlCUVU4c1VVRkJVQ3hGUVVGcFFpeExRVUZxUWl4RFFVUldPenRCUVVkQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRXNkMEpCUVVrc1QwRkJUeXhEUVVGRExFbEJRVWtzVlVGQlNpeERRVUZsTEVOQlFXWXNRMEZCVWl4SlFVRTJRaXhEUVVGRExGVkJRVlVzUzBGQlZpeERRVUZzUXl4RlFVRnZSRHRCUVVOb1JDdzBRa0ZCU1N4UFFVRlBMRTFCUVZBc1JVRkJaU3hMUVVGbUxFTkJRVW9zUlVGQk1rSTdRVUZEZGtJc1owTkJRVWtzVTBGQlNpeERRVUZqTEVOQlFXUXNSVUZCYVVJc1UwRkJVU3hMUVVGU0xFTkJRV3BDTzBGQlEwRXNaME5CUVVrc1MwRkJTaXhIUVVaMVFpeERRVVZXTzBGQlEyaENMSGxDUVVoRUxFMUJSMDg3UVVGRFNDeDFRMEZCVnl4SFFVRllMRVZCUVdkQ0xFMUJRV2hDTEVWQlFYZENMRk5CUVhoQ08wRkJRMGc3UVVGRFNqdEJRVU5LTEdsQ1FXaENSRHRCUVdsQ1FTd3dRa0ZCVlN4RlFVRldMRWxCUVdkQ0xFbEJRV2hDTzBGQlEwZzdRVUZEU2pzN1FVRkZSQ3hwUWtGQlV5eFhRVUZVTEVkQlFYVkNPMEZCUTI1Q0xHZENRVUZKTEZsQlFVbzdRVUZCUVN4blFrRkJVeXd3UWtGQlZEdEJRVUZCTEdkQ1FVTkpMR1ZCUVdVc1VVRkJUeXhYUVVGUUxFZEJRWEZDTEVsQlJIaERPenRCUVVWSk8wRkJRMEVzYzBKQlFWVXNaMEpCUVdsQ0xGRkJRVkVzVTBGQlVpeEhRVUZ2UWl4WlFVRnlRaXhIUVVGeFF5eEpRVUZKTEVsQlFVb3NSMEZCVnl4UFFVRllMRVZCU0c1Rk8wRkJRVUVzWjBKQlNVa3NWVUZCVlN4RlFVcGtPMEZCUVVFc1owSkJTMGtzVjBGQlZ5eEZRVXhtTzBGQlFVRXNaMEpCVFVrc1pVRkJaU3hMUVU1dVFqdEJRVUZCTEdkQ1FVOUpMR2xDUVVGcFFpeEpRVkJ5UWpzN1FVRlRRVHRCUVVOQkxHZENRVUZKTEdGQlFVb3NSVUZCYlVJN1FVRkRaanRCUVVOSU96dEJRVVZFTERSQ1FVRm5RaXhKUVVGb1FqczdRVUZGUVR0QlFVTkJMSEZDUVVGVExHVkJRVlFzUlVGQk1FSXNWVUZCVlN4SFFVRldMRVZCUVdVN1FVRkRja01zYjBKQlFVa3NUVUZCVFN4SlFVRkpMRWRCUVdRN1FVRkJRU3h2UWtGRFNTeFJRVUZSTEVsQlFVa3NSVUZFYUVJN08wRkJSMEU3UVVGRFFTeHZRa0ZCU1N4RFFVRkRMRWxCUVVrc1QwRkJWQ3hGUVVGclFqdEJRVU5rTzBGQlEwZzdPMEZCUlVRc2IwSkJRVWtzUTBGQlF5eEpRVUZKTEZGQlFWUXNSVUZCYlVJN1FVRkRaaXcyUWtGQlV5eEpRVUZVTEVOQlFXTXNSMEZCWkR0QlFVTklPenRCUVVWRUxHOUNRVUZKTEVOQlFVTXNTVUZCU1N4TFFVRlVMRVZCUVdkQ08wRkJRMW83UVVGRFFUdEJRVU5CTEhkQ1FVRkpMRU5CUVVNc1NVRkJTU3hOUVVGTUxFbEJRV1VzVDBGQmJrSXNSVUZCTkVJN1FVRkRlRUlzTkVKQlFVa3NaMEpCUVdkQ0xFdEJRV2hDTEVOQlFVb3NSVUZCTkVJN1FVRkRlRUlzWjBSQlFXOUNMRWxCUVhCQ08wRkJRMEVzTWtOQlFXVXNTVUZCWmp0QlFVTklMSGxDUVVoRUxFMUJSMDg3UVVGRFNDeHZRMEZCVVN4SlFVRlNMRU5CUVdFc1MwRkJZanRCUVVOQkxIbERRVUZoTEV0QlFXSTdRVUZEU0R0QlFVTktMSEZDUVZKRUxFMUJVVThzU1VGQlNTeERRVUZETEVsQlFVa3NUVUZCVEN4SlFVRmxMRWxCUVVrc1QwRkJia0lzU1VGQk9FSXNTVUZCU1N4UlFVRjBReXhGUVVGblJEdEJRVU51UkN4MVEwRkJaU3hKUVVGbU8wRkJRMEVzTkVKQlFVa3NRMEZCUXl4SlFVRkpMRTFCUVZRc1JVRkJhVUk3UVVGRFlqdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFc2JVTkJRVkVzYVVKQlFXbENMRXRCUVhwQ08wRkJRMGc3UVVGRFNqdEJRVU5LTzBGQlEwb3NZVUZ3UTBRN08wRkJjME5CTEdkQ1FVRkpMRmRCUVZjc1VVRkJVU3hOUVVGMlFpeEZRVUVyUWp0QlFVTXpRanRCUVVOQkxITkNRVUZOTEZWQlFWVXNVMEZCVml4RlFVRnhRaXdyUWtGQkswSXNUMEZCY0VRc1JVRkJOa1FzU1VGQk4wUXNSVUZCYlVVc1QwRkJia1VzUTBGQlRqdEJRVU5CTEc5Q1FVRkpMRmRCUVVvc1IwRkJhMElzVVVGQlVTeFhRVUV4UWp0QlFVTkJMSFZDUVVGUExGRkJRVkVzUjBGQlVpeERRVUZRTzBGQlEwZzdPMEZCUlVRN1FVRkRRU3huUWtGQlNTeGpRVUZLTEVWQlFXOUNPMEZCUTJoQ0xIRkNRVUZMTEZGQlFVd3NSVUZCWlN4VlFVRlZMRWRCUVZZc1JVRkJaVHRCUVVNeFFpd3JRa0ZCVnl4SFFVRllMRVZCUVdkQ0xFVkJRV2hDTEVWQlFXOUNMRVZCUVhCQ08wRkJRMGdzYVVKQlJrUTdRVUZIU0RzN1FVRkZSRHRCUVVOQk8wRkJRMEU3UVVGRFFTeG5Ra0ZCU1N4RFFVRkRMRU5CUVVNc1QwRkJSQ3hKUVVGWkxHbENRVUZpTEV0QlFXMURMRmxCUVhaRExFVkJRWEZFTzBGQlEycEVPMEZCUTBFN1FVRkRRU3h2UWtGQlNTeERRVUZETEdGQlFXRXNWMEZCWkN4TFFVRTRRaXhEUVVGRExHOUNRVUZ1UXl4RlFVRjVSRHRCUVVOeVJDd3lRMEZCZFVJc1YwRkJWeXhaUVVGWk8wRkJRekZETEN0RFFVRjFRaXhEUVVGMlFqdEJRVU5CTzBGQlEwZ3NjVUpCU0hOQ0xFVkJSM0JDTEVWQlNHOUNMRU5CUVhaQ08wRkJTVWc3UVVGRFNqczdRVUZGUkN3MFFrRkJaMElzUzBGQmFFSTdRVUZEU0RzN1FVRkZSQ3hwUWtGQlV5eG5Ra0ZCVlN4SFFVRldMRVZCUVdVN1FVRkRjRUlzYVVKQlFVc3NUVUZCVEN4SFFVRmpMRTlCUVU4c1YwRkJVQ3hGUVVGdlFpeEpRVUZKTEVWQlFYaENMRXRCUVN0Q0xFVkJRVGRETzBGQlEwRXNhVUpCUVVzc1IwRkJUQ3hIUVVGWExFZEJRVmc3UVVGRFFTeHBRa0ZCU3l4SlFVRk1MRWRCUVZrc1QwRkJUeXhSUVVGUExFbEJRV1FzUlVGQmIwSXNTVUZCU1N4RlFVRjRRaXhEUVVGYU8wRkJRMEVzYVVKQlFVc3NWVUZCVEN4SFFVRnJRaXhGUVVGc1FqdEJRVU5CTEdsQ1FVRkxMRTlCUVV3c1IwRkJaU3hGUVVGbU8wRkJRMEVzYVVKQlFVc3NWVUZCVEN4SFFVRnJRaXhGUVVGc1FqdEJRVU5CTEdsQ1FVRkxMRlZCUVV3c1IwRkJhMElzUlVGQmJFSTdRVUZEUVN4cFFrRkJTeXhSUVVGTUxFZEJRV2RDTEVOQlFXaENPenRCUVVWQk96czdPMEZCU1Vnc1UwRmtSRHM3UVVGblFrRXNaVUZCVHl4VFFVRlFMRWRCUVcxQ08wRkJRMllzYTBKQlFVMHNZMEZCVlN4UFFVRldMRVZCUVcxQ0xFOUJRVzVDTEVWQlFUUkNMRTlCUVRWQ0xFVkJRWEZETEU5QlFYSkRMRVZCUVRoRE8wRkJRMmhFTERCQ1FVRlZMRmRCUVZjc1JVRkJja0k3TzBGQlJVRTdRVUZEUVR0QlFVTkJPMEZCUTBFc2IwSkJRVWtzUzBGQlN5eE5RVUZVTEVWQlFXbENPMEZCUTJJN1FVRkRTRHM3UVVGRlJDeHhRa0ZCU3l4UFFVRk1MRWRCUVdVc1QwRkJaanM3UVVGRlFTeHZRa0ZCU1N4UFFVRktMRVZCUVdFN1FVRkRWRHRCUVVOQkxIbENRVUZMTEVWQlFVd3NRMEZCVVN4UFFVRlNMRVZCUVdsQ0xFOUJRV3BDTzBGQlEwZ3NhVUpCU0VRc1RVRkhUeXhKUVVGSkxFdEJRVXNzVFVGQlRDeERRVUZaTEV0QlFXaENMRVZCUVhWQ08wRkJRekZDTzBGQlEwRTdRVUZEUVN3NFFrRkJWU3hMUVVGTExFbEJRVXdzUlVGQlZ5eFZRVUZWTEVkQlFWWXNSVUZCWlR0QlFVTm9ReXcyUWtGQlN5eEpRVUZNTEVOQlFWVXNUMEZCVml4RlFVRnRRaXhIUVVGdVFqdEJRVU5JTEhGQ1FVWlRMRU5CUVZZN1FVRkhTRHM3UVVGRlJEdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFc2NVSkJRVXNzVDBGQlRDeEhRVUZsTEZkQlFWY3NVVUZCVVN4TFFVRlNMRU5CUVdNc1EwRkJaQ3hEUVVFeFFqczdRVUZGUVN4eFFrRkJTeXhQUVVGTUxFZEJRV1VzVDBGQlpqczdRVUZGUVR0QlFVTkJMSEZDUVVGTExFMUJRVXdzUjBGQll5eEpRVUZrT3p0QlFVVkJMSEZDUVVGTExFMUJRVXdzUjBGQll5eFJRVUZSTEUxQlFYUkNPenRCUVVWQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRXNiMEpCUVVrc1VVRkJVU3hQUVVGU0xFbEJRVzFDTEV0QlFVc3NUMEZCTlVJc1JVRkJjVU03UVVGRGFrTTdRVUZEUVR0QlFVTkJMSGxDUVVGTExFMUJRVXc3UVVGRFNDeHBRa0ZLUkN4TlFVbFBPMEZCUTBnc2VVSkJRVXNzUzBGQlREdEJRVU5JTzBGQlEwb3NZVUZxUkdNN08wRkJiVVJtTEhWQ1FVRlhMRzFDUVVGVkxFTkJRVllzUlVGQllTeFZRVUZpTEVWQlFYbENPMEZCUTJoRE8wRkJRMEU3UVVGRFFTeHZRa0ZCU1N4RFFVRkRMRXRCUVVzc1ZVRkJUQ3hEUVVGblFpeERRVUZvUWl4RFFVRk1MRVZCUVhsQ08wRkJRM0pDTEhsQ1FVRkxMRlZCUVV3c1EwRkJaMElzUTBGQmFFSXNTVUZCY1VJc1NVRkJja0k3UVVGRFFTeDVRa0ZCU3l4UlFVRk1MRWxCUVdsQ0xFTkJRV3BDTzBGQlEwRXNlVUpCUVVzc1ZVRkJUQ3hEUVVGblFpeERRVUZvUWl4SlFVRnhRaXhWUVVGeVFqdEJRVU5JTzBGQlEwb3NZVUV6UkdNN08wRkJOa1JtTEcxQ1FVRlBMR2xDUVVGWk8wRkJRMllzYjBKQlFVa3NTMEZCU3l4UFFVRlVMRVZCUVd0Q08wRkJRMlE3UVVGRFNEdEJRVU5FTEhGQ1FVRkxMRTlCUVV3c1IwRkJaU3hKUVVGbU96dEJRVVZCTEhkQ1FVRlJMRk5CUVZJc1IwRkJjVUlzU1VGQlNTeEpRVUZLTEVWQlFVUXNRMEZCWVN4UFFVRmlMRVZCUVhCQ096dEJRVVZCTEc5Q1FVRkpMRTFCUVUwc1MwRkJTeXhIUVVGbU96dEJRVVZCTzBGQlEwRTdRVUZEUVN4dlFrRkJTU3hMUVVGTExFbEJRVlFzUlVGQlpUdEJRVU5ZTERSQ1FVRlJMRmRCUVZJc1EwRkJiMElzUzBGQlN5eEhRVUY2UWl4RlFVRTRRanRCUVVNeFFpdzJRMEZCY1VJN1FVRkVTeXh4UWtGQk9VSXNSVUZGUnl4TFFVRkxMRWxCUVV3c1EwRkJWU3hKUVVGV0xFbEJRV3RDTEVWQlJuSkNMRVZCUlhsQ0xFdEJRVXNzU1VGQlRDeEZRVUZYTEZsQlFWazdRVUZETlVNc0swSkJRVThzU1VGQlNTeE5RVUZLTEVkQlFXRXNTMEZCU3l4VlFVRk1MRVZCUVdJc1IwRkJhVU1zUzBGQlN5eEpRVUZNTEVWQlFYaERPMEZCUTBnc2NVSkJSbmRDTEVOQlJucENPMEZCUzBnc2FVSkJUa1FzVFVGTlR6dEJRVU5JTzBGQlEwRXNNa0pCUVU4c1NVRkJTU3hOUVVGS0xFZEJRV0VzUzBGQlN5eFZRVUZNTEVWQlFXSXNSMEZCYVVNc1MwRkJTeXhKUVVGTUxFVkJRWGhETzBGQlEwZzdRVUZEU2l4aFFXNUdZenM3UVVGeFJtWXNhMEpCUVUwc1owSkJRVms3UVVGRFpDeHZRa0ZCU1N4TlFVRk5MRXRCUVVzc1IwRkJUQ3hEUVVGVExFZEJRVzVDT3p0QlFVVkJPMEZCUTBFc2IwSkJRVWtzUTBGQlF5eFhRVUZYTEVkQlFWZ3NRMEZCVEN4RlFVRnpRanRCUVVOc1Fpd3JRa0ZCVnl4SFFVRllMRWxCUVd0Q0xFbEJRV3hDTzBGQlEwRXNORUpCUVZFc1NVRkJVaXhEUVVGaExFdEJRVXNzUjBGQlRDeERRVUZUTEVWQlFYUkNMRVZCUVRCQ0xFZEJRVEZDTzBGQlEwZzdRVUZEU2l4aFFUZEdZenM3UVVFclJtWTdPenM3UVVGSlFTeHRRa0ZCVHl4cFFrRkJXVHRCUVVObUxHOUNRVUZKTEVOQlFVTXNTMEZCU3l4UFFVRk9MRWxCUVdsQ0xFdEJRVXNzVVVGQk1VSXNSVUZCYjBNN1FVRkRhRU03UVVGRFNEczdRVUZGUkN4dlFrRkJTU3haUVVGS08wRkJRVUVzYjBKQlFWTXNhMEpCUVZRN1FVRkJRU3h2UWtGRFNTeExRVUZMTEV0QlFVc3NSMEZCVEN4RFFVRlRMRVZCUkd4Q08wRkJRVUVzYjBKQlJVa3NZVUZCWVN4TFFVRkxMRlZCUm5SQ08wRkJRVUVzYjBKQlIwa3NWVUZCVlN4TFFVRkxMRTlCU0c1Q08wRkJRVUVzYjBKQlNVa3NWVUZCVlN4TFFVRkxMRTlCU201Q096dEJRVTFCTEc5Q1FVRkpMRU5CUVVNc1MwRkJTeXhOUVVGV0xFVkJRV3RDTzBGQlEyUTdRVUZEUVN4M1FrRkJTU3hEUVVGRExGRkJRVkVzVVVGQlVTeFhRVUZvUWl4RlFVRTJRaXhGUVVFM1FpeERRVUZNTEVWQlFYVkRPMEZCUTI1RExEWkNRVUZMTEV0QlFVdzdRVUZEU0R0QlFVTktMR2xDUVV4RUxFMUJTMDhzU1VGQlNTeExRVUZMTEV0QlFWUXNSVUZCWjBJN1FVRkRia0lzZVVKQlFVc3NTVUZCVEN4RFFVRlZMRTlCUVZZc1JVRkJiVUlzUzBGQlN5eExRVUY0UWp0QlFVTklMR2xDUVVaTkxFMUJSVUVzU1VGQlNTeERRVUZETEV0QlFVc3NVVUZCVml4RlFVRnZRanRCUVVOMlFqdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJMSGxDUVVGTExGRkJRVXdzUjBGQlowSXNTVUZCYUVJN08wRkJSVUVzZDBKQlFVa3NTMEZCU3l4UlFVRk1MRWRCUVdkQ0xFTkJRV2hDTEVsQlFYRkNMRU5CUVVNc1MwRkJTeXhQUVVFdlFpeEZRVUYzUXp0QlFVTndReXcwUWtGQlNTeFhRVUZYTEU5QlFWZ3NRMEZCU2l4RlFVRjVRanRCUVVOeVFpeG5RMEZCU1R0QlFVTkJMREJEUVVGVkxGRkJRVkVzVFVGQlVpeERRVUZsTEVWQlFXWXNSVUZCYlVJc1QwRkJia0lzUlVGQk5FSXNWVUZCTlVJc1JVRkJkME1zVDBGQmVFTXNRMEZCVmp0QlFVTklMRFpDUVVaRUxFTkJSVVVzVDBGQlR5eERRVUZRTEVWQlFWVTdRVUZEVWl4elEwRkJUU3hEUVVGT08wRkJRMGc3TzBGQlJVUTdRVUZEUVR0QlFVTkJPMEZCUTBFc1owTkJRVWtzUzBGQlN5eEhRVUZNTEVOQlFWTXNVVUZCVkN4SlFVRnhRaXhaUVVGWkxGTkJRWEpETEVWQlFXZEVPMEZCUXpWRExEUkRRVUZaTEV0QlFVc3NUVUZCYWtJN1FVRkRRU3h2UTBGQlNTeFRRVUZLTEVWQlFXVTdRVUZEV0N3NFEwRkJWU3hWUVVGVkxFOUJRWEJDTzBGQlEwZ3NhVU5CUmtRc1RVRkZUeXhKUVVGSkxFdEJRVXNzV1VGQlZDeEZRVUYxUWp0QlFVTXhRanRCUVVOQkxEaERRVUZWTEV0QlFVc3NUMEZCWmp0QlFVTklPMEZCUTBvN08wRkJSVVFzWjBOQlFVa3NSMEZCU2l4RlFVRlRPMEZCUTB3N1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRXNiME5CUVVzc1MwRkJTeXhOUVVGTUxFTkJRVmtzUzBGQldpeEpRVUZ4UWl4TFFVRkxMRWRCUVV3c1EwRkJVeXhSUVVFdlFpeEpRVU5CTEVsQlFVa3NUMEZCU2l4TFFVRm5RaXhqUVVSd1FpeEZRVU52UXp0QlFVTm9ReXgzUTBGQlNTeFZRVUZLTEVkQlFXbENMRXRCUVVzc1IwRkJkRUk3UVVGRFFTeDNRMEZCU1N4alFVRktMRWRCUVhGQ0xFdEJRVXNzUjBGQlRDeERRVUZUTEZGQlFWUXNSMEZCYjBJc1EwRkJReXhMUVVGTExFZEJRVXdzUTBGQlV5eEZRVUZXTEVOQlFYQkNMRWRCUVc5RExFbEJRWHBFTzBGQlEwRXNkME5CUVVrc1YwRkJTaXhIUVVGclFpeExRVUZMTEVkQlFVd3NRMEZCVXl4UlFVRlVMRWRCUVc5Q0xGRkJRWEJDTEVkQlFTdENMRk5CUVdwRU8wRkJRMEVzTWtOQlFVOHNVVUZCVXl4TFFVRkxMRXRCUVV3c1IwRkJZU3hIUVVGMFFpeERRVUZRTzBGQlEwZ3NhVU5CVGtRc1RVRk5UeXhKUVVGSkxFOUJRVThzVDBGQlVDeExRVUZ0UWl4WFFVRnVRaXhKUVVOUUxGRkJRVkVzUzBGRVRDeEZRVU5aTzBGQlEyWTdRVUZEUVR0QlFVTkJMRFJEUVVGUkxFdEJRVklzUTBGQll5eEhRVUZrTzBGQlEwZ3NhVU5CVEUwc1RVRkxRVHRCUVVOSU8wRkJRMEU3UVVGRFFUdEJRVU5CTEhkRFFVRkpMRTlCUVVvc1EwRkJXU3hIUVVGYU8wRkJRMGc3UVVGRFNqdEJRVU5LTEhsQ1FUZERSQ3hOUVRaRFR6dEJRVU5JTzBGQlEwRXNjME5CUVZVc1QwRkJWanRCUVVOSU96dEJRVVZFTERaQ1FVRkxMRTlCUVV3c1IwRkJaU3hQUVVGbU96dEJRVVZCTERSQ1FVRkpMRXRCUVVzc1IwRkJUQ3hEUVVGVExGRkJRVlFzU1VGQmNVSXNRMEZCUXl4TFFVRkxMRTFCUVM5Q0xFVkJRWFZETzBGQlEyNURMSEZEUVVGUkxFVkJRVklzU1VGQll5eFBRVUZrT3p0QlFVVkJMR2REUVVGSkxFbEJRVWtzWTBGQlVpeEZRVUYzUWp0QlFVTndRaXh2UTBGQlNTeGpRVUZqTEVWQlFXeENPMEZCUTBFc2NVTkJRVXNzUzBGQlN5eFBRVUZXTEVWQlFXMUNMRlZCUVZVc1RVRkJWaXhGUVVGclFqdEJRVU5xUXl4blJFRkJXU3hKUVVGYUxFTkJRV2xDTEU5QlFVOHNZVUZCVUN4SlFVRjNRaXhOUVVGNlF6dEJRVU5JTEdsRFFVWkVPMEZCUjBFc2IwTkJRVWtzWTBGQlNpeERRVUZ0UWl4UFFVRnVRaXhGUVVFMFFpeExRVUZMTEVkQlFXcERMRVZCUVhORExGZEJRWFJETzBGQlEwZzdRVUZEU2pzN1FVRkZSRHRCUVVOQkxITkRRVUZqTEVWQlFXUTdPMEZCUlVFc05rSkJRVXNzVDBGQlRDeEhRVUZsTEVsQlFXWTdRVUZEU0RzN1FVRkZSRHRCUVVOQk8wRkJRMEU3UVVGRFFTeDVRa0ZCU3l4UlFVRk1MRWRCUVdkQ0xFdEJRV2hDT3p0QlFVVkJMSGRDUVVGSkxFdEJRVXNzVDBGQlRDeEpRVUZuUWl4RFFVRkRMRXRCUVVzc1lVRkJNVUlzUlVGQmVVTTdRVUZEY2tNc05rSkJRVXNzWVVGQlRDeEhRVUZ4UWl4SlFVRnlRanRCUVVOQkxEWkNRVUZMTEVsQlFVd3NRMEZCVlN4VFFVRldMRVZCUVhGQ0xFdEJRVXNzVDBGQk1VSTdRVUZEUVN3MlFrRkJTeXhyUWtGQlRDeEhRVUV3UWl4SlFVRXhRanRCUVVOSU8wRkJSVW83UVVGRFNpeGhRUzlOWXpzN1FVRnBUbVlzZDBKQlFWa3NjMEpCUVZrN1FVRkRjRUlzYjBKQlFVa3NUVUZCVFN4TFFVRkxMRWRCUVdZN1FVRkRRU3h2UWtGQlNTeExRVUZMTEVsQlFVa3NSVUZCWWp0QlFVTkJPMEZCUTBFc2IwSkJRVWtzV1VGQldTeGpRVUZqTEVsQlFVa3NUVUZCYkVJc1EwRkJhRUk3TzBGQlJVRTdRVUZEUVR0QlFVTkJMSEZDUVVGTExFOUJRVXdzUTBGQllTeEpRVUZpTEVOQlFXdENMRk5CUVd4Q096dEJRVVZCTEcxQ1FVRkhMRk5CUVVnc1JVRkJZeXhUUVVGa0xFVkJRWGxDTEV0QlFVc3NTVUZCVEN4RlFVRlhMRlZCUVZVc1RVRkJWaXhGUVVGclFqdEJRVU5zUkN4M1FrRkJTU3hoUVVGS08wRkJRVUVzZDBKQlFWVXNjMEpCUVZZN1FVRkJRU3gzUWtGQmVVSXNjMEpCUVhwQ08wRkJRVUVzZDBKQlEwa3NWMEZCVnl4UFFVRlBMRlZCUVZBc1JVRkJiVUlzUzBGQlN5eEhRVUZNTEVOQlFWTXNSVUZCTlVJc1EwRkVaanRCUVVGQkxIZENRVVZKTEU5QlFVOHNTMEZCU3l4SFFVRk1MRU5CUVZNc1NVRkdjRUk3UVVGQlFTeDNRa0ZIU1N4aFFVRmhMRXRCUVVzc1IwRkJUQ3hEUVVGVExGTkJRVlFzUjBGQmNVSXNTMEZCU3l4SFFVRk1MRU5CUVZNc1UwRkJWQ3hEUVVGdFFpeEpRVUY0UXl4SFFVRXJReXhKUVVob1JUdEJRVUZCTEhkQ1FVbEpMR1ZCUVdVc1VVRkJVU3hYUVVGU0xFTkJRVzlDTEVsQlFVa3NVMEZCZUVJc1JVRkJiVU03UVVGRE9VTXNOa05CUVhGQ08wRkJSSGxDTEhGQ1FVRnVReXhEUVVwdVFqczdRVUZSUVR0QlFVTkJPMEZCUTBFc2QwSkJRVWtzUzBGQlN5eEhRVUZNTEVOQlFWTXNXVUZCWWl4RlFVRXlRanRCUVVOMlFqdEJRVU5CTERSQ1FVRkpMRTlCUVU4c1UwRkJXQ3hGUVVGelFqdEJRVU5zUWl4dFEwRkJUeXhQUVVGUExGTkJRVkFzUTBGQmFVSXNTVUZCYWtJc1JVRkJkVUlzVlVGQlZTeEpRVUZXTEVWQlFXZENPMEZCUXpGRExIVkRRVUZQTEZWQlFWVXNTVUZCVml4RlFVRm5RaXhWUVVGb1FpeEZRVUUwUWl4SlFVRTFRaXhEUVVGUU8wRkJRMGdzTmtKQlJrMHNTMEZGUkN4RlFVWk9PMEZCUjBnN08wRkJSVVE3UVVGRFFUdEJRVU5CTEhkRFFVRm5RaXhqUVVGakxFbEJRVWtzVFVGQlNpeEhRVUZoTEVkQlFXSXNSMEZCYlVJc1NVRkJha01zUlVGRFdpeExRVUZMTEVkQlFVd3NRMEZCVXl4VFFVUkhMRU5CUVdoQ08wRkJSVUVzTWtKQlFVY3NZVUZCU0N4RlFVTkpMRk5CUkVvc1JVRkRaU3hMUVVGTExFbEJRVXdzUlVGQlZ5eFZRVUZWTEV0QlFWWXNSVUZCYVVJN1FVRkRia01zYVVOQlFVc3NSMEZCVEN4RFFVRlRMR0ZCUVZRc1IwRkJlVUlzWVVGQmVrSTdRVUZEUVN4cFEwRkJTeXhKUVVGTUxFTkJRVlVzUlVGQlZpeEZRVUZqTEZsQlFWazdRVUZEZEVJc2RVTkJRVThzUzBGQlVEdEJRVU5JTERaQ1FVWkVMRVZCUlVjc1NVRkdTQ3hGUVVWVE8wRkJRMHdzZVVOQlFWTXNTVUZFU2p0QlFVVk1MSGREUVVGUk8wRkJSa2dzTmtKQlJsUTdRVUZOU0N4NVFrRlNWU3hEUVVSbU96dEJRVmRCTEhkRFFVRm5RaXhQUVVGUExGRkJRVkFzUlVGQmFVSXNZMEZCWXl4RlFVRXZRaXhEUVVGb1FqdEJRVU5CTERSQ1FVRkpMR0ZCUVVvc1JVRkJiVUk3UVVGRFpqdEJRVU5CTzBGQlEwRXNhVU5CUVVzc1QwRkJUQ3hEUVVGaExFbEJRV0lzUTBGQmEwSXNZVUZCYkVJN08wRkJSVUVzWjBOQlFVa3NTMEZCU3l4TlFVRk1MRU5CUVZrc1MwRkJhRUlzUlVGQmRVSTdRVUZEYmtJc09FTkJRV01zUlVGQlpDeERRVUZwUWl4UFFVRnFRaXhGUVVFd1FpeExRVUZMTEVsQlFVd3NSVUZCVnl4VlFVRlZMRWRCUVZZc1JVRkJaVHRCUVVOb1JDeDVRMEZCU3l4SlFVRk1MRU5CUVZVc1QwRkJWaXhGUVVGdFFpeEhRVUZ1UWp0QlFVTklMR2xEUVVaNVFpeERRVUV4UWp0QlFVZElPMEZCUTBRc01FTkJRV01zVFVGQlpEdEJRVU5JT3p0QlFVVkVPMEZCUTBnN08wRkJSVVE3UVVGRFFUdEJRVU5CTEhkQ1FVRkpMRkZCUVVvc1JVRkJZenRCUVVOV0xEWkNRVUZMTEVkQlFVd3NRMEZCVXl4SFFVRlVMRWRCUVdVc1VVRkJVU3hUUVVGU0xFTkJRV3RDTEZGQlFXeENMRU5CUVdZN1FVRkRRU3cyUWtGQlN5eEpRVUZNTzBGQlEwRTdRVUZEU0RzN1FVRkZSQ3d5UWtGQlR5eExRVUZMTEVsQlFVd3NSVUZCVnl4VlFVRlZMRXRCUVZZc1JVRkJhVUk3UVVGREwwSXNOa0pCUVVzc1NVRkJUQ3hEUVVGVkxFVkJRVllzUlVGQll5eFpRVUZaTzBGQlEzUkNMRzFEUVVGUExFdEJRVkE3UVVGRFNDeDVRa0ZHUkN4RlFVVkhMRWxCUmtnc1JVRkZVenRCUVVOTUxIRkRRVUZUTzBGQlJFb3NlVUpCUmxRN1FVRkxTQ3h4UWtGT1RTeERRVUZRT3p0QlFWRkJMSGxDUVVGTExFdEJRVXdzUjBGQllTeExRVUZMTEVsQlFVd3NSVUZCVnl4VlFVRlZMRWRCUVZZc1JVRkJaVHRCUVVOdVF5dzJRa0ZCU3l4TlFVRk1MRWRCUVdNc1NVRkJaRHRCUVVOQkxEWkNRVUZMTEV0QlFVd3NSMEZCWVN4SFFVRmlPMEZCUTBFc05FSkJRVWtzWTBGQlNpeEhRVUZ4UWl4RFFVRkRMRVZCUVVRc1EwRkJja0k3TzBGQlJVRTdRVUZEUVR0QlFVTkJMR2xEUVVGVExGRkJRVlFzUlVGQmJVSXNWVUZCVlN4SFFVRldMRVZCUVdVN1FVRkRPVUlzWjBOQlFVa3NTVUZCU1N4SFFVRktMRU5CUVZFc1JVRkJVaXhEUVVGWExFOUJRVmdzUTBGQmJVSXNTMEZCU3l4bFFVRjRRaXhOUVVFMlF5eERRVUZxUkN4RlFVRnZSRHRCUVVOb1JDdzRRMEZCWXl4SlFVRkpMRWRCUVVvc1EwRkJVU3hGUVVGMFFqdEJRVU5JTzBGQlEwb3NlVUpCU2tRN08wRkJUVUVzWjBOQlFWRXNSMEZCVWp0QlFVTklMSEZDUVdSWkxFTkJRV0k3TzBGQlowSkJPMEZCUTBFN1FVRkRRU3g1UWtGQlN5eFJRVUZNTEVkQlFXZENMRXRCUVVzc1NVRkJUQ3hGUVVGWExGVkJRVlVzU1VGQlZpeEZRVUZuUWl4UFFVRm9RaXhGUVVGNVFqdEJRVU5vUkR0QlFVTkJMRFJDUVVGSkxHRkJRV0VzU1VGQlNTeEpRVUZ5UWp0QlFVRkJMRFJDUVVOSkxGbEJRVmtzWTBGQll5eFZRVUZrTEVOQlJHaENPMEZCUVVFc05FSkJSVWtzYVVKQlFXbENMR05CUm5KQ096dEJRVWxCTzBGQlEwRTdRVUZEUVR0QlFVTkJMRFJDUVVGSkxFOUJRVW9zUlVGQllUdEJRVU5VTEcxRFFVRlBMRTlCUVZBN1FVRkRTRHM3UVVGRlJEdEJRVU5CTzBGQlEwRXNORUpCUVVrc1kwRkJTaXhGUVVGdlFqdEJRVU5vUWl3MlEwRkJhVUlzUzBGQmFrSTdRVUZEU0RzN1FVRkZSRHRCUVVOQkxHdERRVUZWTEZOQlFWWTdPMEZCUlVFN1FVRkRRU3cwUWtGQlNTeFJRVUZSTEZGQlFVOHNUVUZCWml4RlFVRjFRaXhGUVVGMlFpeERRVUZLTEVWQlFXZERPMEZCUXpWQ0xHOURRVUZQTEUxQlFWQXNRMEZCWXl4VlFVRmtMRWxCUVRSQ0xGRkJRVThzVFVGQlVDeERRVUZqTEVWQlFXUXNRMEZCTlVJN1FVRkRTRHM3UVVGRlJDdzBRa0ZCU1R0QlFVTkJMR2REUVVGSkxFbEJRVW9zUTBGQlV5eEpRVUZVTzBGQlEwZ3NlVUpCUmtRc1EwRkZSU3hQUVVGUExFTkJRVkFzUlVGQlZUdEJRVU5TTEcxRFFVRlBMRkZCUVZFc1ZVRkJWU3hqUVVGV0xFVkJRMWdzZFVKQlFYVkNMRVZCUVhaQ0xFZEJRMEVzVjBGRVFTeEhRVU5qTEVOQlJrZ3NSVUZIV0N4RFFVaFhMRVZCU1Znc1EwRkJReXhGUVVGRUxFTkJTbGNzUTBGQlVpeERRVUZRTzBGQlMwZzdPMEZCUlVRc05FSkJRVWtzWTBGQlNpeEZRVUZ2UWp0QlFVTm9RaXcyUTBGQmFVSXNTVUZCYWtJN1FVRkRTRHM3UVVGRlJEdEJRVU5CTERaQ1FVRkxMRTlCUVV3c1EwRkJZU3hKUVVGaUxFTkJRV3RDTEZOQlFXeENPenRCUVVWQk8wRkJRMEVzWjBOQlFWRXNXVUZCVWl4RFFVRnhRaXhWUVVGeVFqczdRVUZGUVR0QlFVTkJMSEZEUVVGaExFTkJRVU1zVlVGQlJDeERRVUZpTEVWQlFUSkNMRWxCUVROQ08wRkJRMGdzY1VKQmFrUmxMRU5CUVdoQ096dEJRVzFFUVR0QlFVTkJPMEZCUTBFc01rSkJRVThzU1VGQlVDeERRVUZaTEVsQlFVa3NTVUZCYUVJc1JVRkJjMElzV1VGQmRFSXNSVUZCYjBNc1NVRkJjRU1zUlVGQk1FTXNUMEZCTVVNN1FVRkRTQ3hwUWtFelNYZENMRU5CUVhwQ096dEJRVFpKUVN4M1FrRkJVU3hOUVVGU0xFTkJRV1VzVTBGQlppeEZRVUV3UWl4SlFVRXhRanRCUVVOQkxIRkNRVUZMTEZWQlFVd3NRMEZCWjBJc1ZVRkJWU3hGUVVFeFFpeEpRVUZuUXl4VFFVRm9RenRCUVVOSUxHRkJNVmRqT3p0QlFUUlhaaXh2UWtGQlVTeHJRa0ZCV1R0QlFVTm9RaXhuUTBGQlowSXNTMEZCU3l4SFFVRk1MRU5CUVZNc1JVRkJla0lzU1VGQkswSXNTVUZCTDBJN1FVRkRRU3h4UWtGQlN5eFBRVUZNTEVkQlFXVXNTVUZCWmpzN1FVRkZRVHRCUVVOQk8wRkJRMEVzY1VKQlFVc3NVVUZCVEN4SFFVRm5RaXhKUVVGb1FqczdRVUZGUVR0QlFVTkJMSEZDUVVGTExFdEJRVXNzVDBGQlZpeEZRVUZ0UWl4TFFVRkxMRWxCUVV3c1JVRkJWeXhWUVVGVkxFMUJRVllzUlVGQmEwSXNRMEZCYkVJc1JVRkJjVUk3UVVGREwwTXNkMEpCUVVrc1YwRkJTanRCUVVOQkxIZENRVUZKTEZsQlFVbzdRVUZEUVN4M1FrRkJTU3huUWtGQlNqczdRVUZGUVN4M1FrRkJTU3hQUVVGUExFMUJRVkFzUzBGQmEwSXNVVUZCZEVJc1JVRkJaME03UVVGRE5VSTdRVUZEUVN4cFEwRkJVeXhqUVVGakxFMUJRV1FzUlVGRFNpeExRVUZMTEVkQlFVd3NRMEZCVXl4UlFVRlVMRWRCUVc5Q0xFdEJRVXNzUjBGQmVrSXNSMEZCSzBJc1MwRkJTeXhIUVVGTUxFTkJRVk1zVTBGRWNFTXNSVUZGVEN4TFFVWkxMRVZCUjB3c1EwRkJReXhMUVVGTExFOUJTRVFzUTBGQlZEdEJRVWxCTERaQ1FVRkxMRTlCUVV3c1EwRkJZU3hEUVVGaUxFbEJRV3RDTEUxQlFXeENPenRCUVVWQkxHdERRVUZWTEU5QlFVOHNVVUZCVUN4RlFVRnBRaXhQUVVGUExFVkJRWGhDTEVOQlFWWTdPMEZCUlVFc05FSkJRVWtzVDBGQlNpeEZRVUZoTzBGQlExUXNhVU5CUVVzc1ZVRkJUQ3hEUVVGblFpeERRVUZvUWl4SlFVRnhRaXhSUVVGUkxFbEJRVklzUTBGQmNrSTdRVUZEUVR0QlFVTklPenRCUVVWRUxEWkNRVUZMTEZGQlFVd3NTVUZCYVVJc1EwRkJha0k3TzBGQlJVRXNNa0pCUVVjc1RVRkJTQ3hGUVVGWExGTkJRVmdzUlVGQmMwSXNTMEZCU3l4SlFVRk1MRVZCUVZjc1ZVRkJWU3hWUVVGV0xFVkJRWE5DTzBGQlEyNUVMR2REUVVGSkxFdEJRVXNzVDBGQlZDeEZRVUZyUWp0QlFVTmtPMEZCUTBnN1FVRkRSQ3hwUTBGQlN5eFRRVUZNTEVOQlFXVXNRMEZCWml4RlFVRnJRaXhWUVVGc1FqdEJRVU5CTEdsRFFVRkxMRXRCUVV3N1FVRkRTQ3g1UWtGT2NVSXNRMEZCZEVJN08wRkJVVUVzTkVKQlFVa3NTMEZCU3l4UFFVRlVMRVZCUVd0Q08wRkJRMlFzSzBKQlFVY3NUVUZCU0N4RlFVRlhMRTlCUVZnc1JVRkJiMElzUzBGQlN5eEpRVUZNTEVWQlFWY3NTMEZCU3l4UFFVRm9RaXhEUVVGd1FqdEJRVU5JTEhsQ1FVWkVMRTFCUlU4c1NVRkJTU3hMUVVGTExFMUJRVXdzUTBGQldTeExRVUZvUWl4RlFVRjFRanRCUVVNeFFqdEJRVU5CTzBGQlEwRXNLMEpCUVVjc1RVRkJTQ3hGUVVGWExFOUJRVmdzUlVGQmIwSXNTMEZCU3l4SlFVRk1MRVZCUVZjc1ZVRkJWU3hIUVVGV0xFVkJRV1U3UVVGRE1VTXNjVU5CUVVzc1NVRkJUQ3hEUVVGVkxFOUJRVllzUlVGQmJVSXNSMEZCYmtJN1FVRkRTQ3cyUWtGR2JVSXNRMEZCY0VJN1FVRkhTRHRCUVVOS096dEJRVVZFTEhsQ1FVRkxMRTlCUVU4c1JVRkJXanRCUVVOQkxEQkNRVUZOTEZOQlFWTXNSVUZCVkN4RFFVRk9PenRCUVVWQk8wRkJRMEU3UVVGRFFTeDNRa0ZCU1N4RFFVRkRMRkZCUVZFc1VVRkJVaXhGUVVGclFpeEZRVUZzUWl4RFFVRkVMRWxCUVRCQ0xFZEJRVEZDTEVsQlFXbERMRU5CUVVNc1NVRkJTU3hQUVVFeFF5eEZRVUZ0UkR0QlFVTXZReXhuUTBGQlVTeE5RVUZTTEVOQlFXVXNUVUZCWml4RlFVRjFRaXhKUVVGMlFqdEJRVU5JTzBGQlEwb3NhVUpCYWtSclFpeERRVUZ1UWpzN1FVRnRSRUU3UVVGRFFTeDVRa0ZCVXl4TFFVRkxMRlZCUVdRc1JVRkJNRUlzUzBGQlN5eEpRVUZNTEVWQlFWY3NWVUZCVlN4VFFVRldMRVZCUVhGQ08wRkJRM1JFTEhkQ1FVRkpMRTFCUVUwc1QwRkJUeXhSUVVGUUxFVkJRV2xDTEZWQlFWVXNSVUZCTTBJc1EwRkJWanRCUVVOQkxIZENRVUZKTEU5QlFVOHNRMEZCUXl4SlFVRkpMRTlCUVdoQ0xFVkJRWGxDTzBGQlEzSkNMR2REUVVGUkxFMUJRVklzUTBGQlpTeFRRVUZtTEVWQlFUQkNMRWxCUVRGQ08wRkJRMGc3UVVGRFNpeHBRa0ZNZVVJc1EwRkJNVUk3TzBGQlQwRXNjVUpCUVVzc1VVRkJUQ3hIUVVGblFpeExRVUZvUWpzN1FVRkZRU3h4UWtGQlN5eExRVUZNTzBGQlEwZ3NZVUZ1WW1NN08wRkJjV0ptTEdkQ1FVRkpMRmxCUVZVc1NVRkJWaXhGUVVGblFpeEZRVUZvUWl4RlFVRnZRanRCUVVOd1FpeHZRa0ZCU1N4TlFVRk5MRXRCUVVzc1RVRkJUQ3hEUVVGWkxFbEJRVm9zUTBGQlZqdEJRVU5CTEc5Q1FVRkpMRU5CUVVNc1IwRkJUQ3hGUVVGVk8wRkJRMDRzTUVKQlFVMHNTMEZCU3l4TlFVRk1MRU5CUVZrc1NVRkJXaXhKUVVGdlFpeEZRVUV4UWp0QlFVTklPMEZCUTBRc2IwSkJRVWtzU1VGQlNpeERRVUZUTEVWQlFWUTdRVUZEU0N4aFFUTmlZenM3UVVFMlltWXNhMEpCUVUwc1kwRkJWU3hKUVVGV0xFVkJRV2RDTEVkQlFXaENMRVZCUVhGQ08wRkJRM1pDTEhGQ1FVRkxMRXRCUVVzc1RVRkJUQ3hEUVVGWkxFbEJRVm9zUTBGQlRDeEZRVUYzUWl4VlFVRlZMRVZCUVZZc1JVRkJZenRCUVVOc1F5eDFRa0ZCUnl4SFFVRklPMEZCUTBnc2FVSkJSa1E3UVVGSFFTeHZRa0ZCU1N4VFFVRlRMRTlCUVdJc1JVRkJjMEk3UVVGRGJFSTdRVUZEUVR0QlFVTkJMREpDUVVGUExFdEJRVXNzVFVGQlRDeERRVUZaTEVsQlFWb3NRMEZCVUR0QlFVTklPMEZCUTBvN1FVRjBZMk1zVTBGQmJrSTdPMEZCZVdOQkxHbENRVUZUTEdGQlFWUXNRMEZCZFVJc1NVRkJka0lzUlVGQk5rSTdRVUZEZWtJN1FVRkRRU3huUWtGQlNTeERRVUZETEZGQlFWRXNVVUZCVWl4RlFVRnBRaXhMUVVGTExFTkJRVXdzUTBGQmFrSXNRMEZCVEN4RlFVRm5RenRCUVVNMVFpd3dRa0ZCVlN4alFVRmpMRXRCUVVzc1EwRkJUQ3hEUVVGa0xFVkJRWFZDTEVsQlFYWkNMRVZCUVRaQ0xFbEJRVGRDTEVOQlFWWXNSVUZCT0VNc1NVRkJPVU1zUTBGQmJVUXNTMEZCU3l4RFFVRk1MRU5CUVc1RUxFVkJRVFJFTEV0QlFVc3NRMEZCVEN4RFFVRTFSRHRCUVVOSU8wRkJRMG83TzBGQlJVUXNhVUpCUVZNc1kwRkJWQ3hEUVVGM1FpeEpRVUY0UWl4RlFVRTRRaXhKUVVFNVFpeEZRVUZ2UXl4SlFVRndReXhGUVVFd1F5eE5RVUV4UXl4RlFVRnJSRHRCUVVNNVF6dEJRVU5CTEdkQ1FVRkpMRXRCUVVzc1YwRkJUQ3hKUVVGdlFpeERRVUZETEU5QlFYcENMRVZCUVd0RE8wRkJRemxDTzBGQlEwRXNiMEpCUVVrc1RVRkJTaXhGUVVGWk8wRkJRMUlzZVVKQlFVc3NWMEZCVEN4RFFVRnBRaXhOUVVGcVFpeEZRVUY1UWl4SlFVRjZRanRCUVVOSU8wRkJRMG9zWVVGTVJDeE5RVXRQTzBGQlEwZ3NjVUpCUVVzc2JVSkJRVXdzUTBGQmVVSXNTVUZCZWtJc1JVRkJLMElzU1VGQkwwSXNSVUZCY1VNc1MwRkJja003UVVGRFNEdEJRVU5LT3p0QlFVVkVPenM3T3pzN096dEJRVkZCTEdsQ1FVRlRMR0ZCUVZRc1EwRkJkVUlzUjBGQmRrSXNSVUZCTkVJN1FVRkRlRUk3UVVGRFFUdEJRVU5CTEdkQ1FVRkpMRTlCUVU4c1NVRkJTU3hoUVVGS0xFbEJRWEZDTEVsQlFVa3NWVUZCY0VNN08wRkJSVUU3UVVGRFFTd3lRa0ZCWlN4SlFVRm1MRVZCUVhGQ0xGRkJRVkVzV1VGQk4wSXNSVUZCTWtNc1RVRkJNME1zUlVGQmJVUXNiMEpCUVc1RU8wRkJRMEVzTWtKQlFXVXNTVUZCWml4RlFVRnhRaXhSUVVGUkxHRkJRVGRDTEVWQlFUUkRMRTlCUVRWRE96dEJRVVZCTEcxQ1FVRlBPMEZCUTBnc2MwSkJRVTBzU1VGRVNEdEJRVVZJTEc5Q1FVRkpMRkZCUVZFc1MwRkJTeXhaUVVGTUxFTkJRV3RDTEc5Q1FVRnNRanRCUVVaVUxHRkJRVkE3UVVGSlNEczdRVUZGUkN4cFFrRkJVeXhoUVVGVUxFZEJRWGxDTzBGQlEzSkNMR2RDUVVGSkxHRkJRVW83TzBGQlJVRTdRVUZEUVRzN1FVRkZRVHRCUVVOQkxHMUNRVUZQTEZOQlFWTXNUVUZCYUVJc1JVRkJkMEk3UVVGRGNFSXNkVUpCUVU4c1UwRkJVeXhMUVVGVUxFVkJRVkE3UVVGRFFTeHZRa0ZCU1N4TFFVRkxMRU5CUVV3c1RVRkJXU3hKUVVGb1FpeEZRVUZ6UWp0QlFVTnNRaXd5UWtGQlR5eFJRVUZSTEZWQlFWVXNWVUZCVml4RlFVRnpRaXd5UTBGRGFrTXNTMEZCU3l4TFFVRkxMRTFCUVV3c1IwRkJZeXhEUVVGdVFpeERRVVJYTEVOQlFWSXNRMEZCVUR0QlFVVklMR2xDUVVoRUxFMUJSMDg3UVVGRFNEdEJRVU5CTzBGQlEwRXNhME5CUVdNc1NVRkJaRHRCUVVOSU8wRkJRMG83UVVGRFJDeHZRa0ZCVVN4WFFVRlNMRWRCUVhOQ0xFVkJRWFJDTzBGQlEwZzdPMEZCUlVRc2EwSkJRVlU3UVVGRFRpeHZRa0ZCVVN4UFFVUkdPMEZCUlU0c2VVSkJRV0VzVjBGR1VEdEJRVWRPTEhOQ1FVRlZMRkZCU0VvN1FVRkpUaXh4UWtGQlV5eFJRVXBJTzBGQlMwNHNkMEpCUVZrc1ZVRk1UanRCUVUxT0xITkNRVUZWTEZGQlRrbzdRVUZQVGl4NVFrRkJZU3hGUVZCUU8wRkJVVTRzYjBKQlFWRXNUVUZTUmp0QlFWTk9MREpDUVVGbExHRkJWRlE3UVVGVlRpeHpRa0ZCVlN4SlFVRkpMRkZCVmxJN1FVRlhUaXh4UWtGQlV5eFBRVmhJT3p0QlFXRk9PenM3T3p0QlFVdEJMSFZDUVVGWExHMUNRVUZWTEVkQlFWWXNSVUZCWlR0QlFVTjBRanRCUVVOQkxHOUNRVUZKTEVsQlFVa3NUMEZCVWl4RlFVRnBRanRCUVVOaUxIZENRVUZKTEVsQlFVa3NUMEZCU2l4RFFVRlpMRTFCUVZvc1EwRkJiVUlzU1VGQlNTeFBRVUZLTEVOQlFWa3NUVUZCV2l4SFFVRnhRaXhEUVVGNFF5eE5RVUVyUXl4SFFVRnVSQ3hGUVVGM1JEdEJRVU53UkN3MFFrRkJTU3hQUVVGS0xFbEJRV1VzUjBGQlpqdEJRVU5JTzBGQlEwbzdPMEZCUlVRN1FVRkRRU3h2UWtGQlNTeFBRVUZQTEZGQlFVOHNTVUZCYkVJN1FVRkJRU3h2UWtGRFNTeFBRVUZQTzBGQlEwZ3NNa0pCUVU4c1NVRkVTanRCUVVWSUxEWkNRVUZUTEVsQlJrNDdRVUZIU0N3MFFrRkJVU3hKUVVoTU8wRkJTVWdzZVVKQlFVczdRVUZLUml4cFFrRkVXRHM3UVVGUlFTeDVRa0ZCVXl4SFFVRlVMRVZCUVdNc1ZVRkJWU3hMUVVGV0xFVkJRV2xDTEVsQlFXcENMRVZCUVhWQ08wRkJRMnBETEhkQ1FVRkpMRXRCUVVzc1NVRkJUQ3hEUVVGS0xFVkJRV2RDTzBGQlExb3NORUpCUVVrc1EwRkJReXhSUVVGUExFbEJRVkFzUTBGQlRDeEZRVUZ0UWp0QlFVTm1MRzlEUVVGUExFbEJRVkFzU1VGQlpTeEZRVUZtTzBGQlEwZzdRVUZEUkN3NFFrRkJUU3hSUVVGUExFbEJRVkFzUTBGQlRpeEZRVUZ2UWl4TFFVRndRaXhGUVVFeVFpeEpRVUV6UWl4RlFVRnBReXhKUVVGcVF6dEJRVU5JTEhGQ1FVeEVMRTFCUzA4N1FVRkRTQ3huUTBGQlR5eEpRVUZRTEVsQlFXVXNTMEZCWmp0QlFVTklPMEZCUTBvc2FVSkJWRVE3TzBGQlYwRTdRVUZEUVN4dlFrRkJTU3hKUVVGSkxFOUJRVklzUlVGQmFVSTdRVUZEWWl3MlFrRkJVeXhKUVVGSkxFOUJRV0lzUlVGQmMwSXNWVUZCVlN4TFFVRldMRVZCUVdsQ0xFbEJRV3BDTEVWQlFYVkNPMEZCUTNwRExEWkNRVUZMTEV0QlFVd3NSVUZCV1N4VlFVRlZMRU5CUVZZc1JVRkJZVHRCUVVOeVFpeG5RMEZCU1N4TlFVRk5MRWxCUVZZc1JVRkJaMEk3UVVGRFdpd3lRMEZCVnl4RFFVRllMRWxCUVdkQ0xFbEJRV2hDTzBGQlEwZzdRVUZEU2l4NVFrRktSRHRCUVV0SUxIRkNRVTVFTzBGQlQwZzdPMEZCUlVRN1FVRkRRU3h2UWtGQlNTeEpRVUZKTEVsQlFWSXNSVUZCWXp0QlFVTldMRFpDUVVGVExFbEJRVWtzU1VGQllpeEZRVUZ0UWl4VlFVRlZMRXRCUVZZc1JVRkJhVUlzUlVGQmFrSXNSVUZCY1VJN1FVRkRjRU03UVVGRFFTdzBRa0ZCU1N4UlFVRlJMRXRCUVZJc1EwRkJTaXhGUVVGdlFqdEJRVU5vUWl4dlEwRkJVVHRCUVVOS0xITkRRVUZOTzBGQlJFWXNOa0pCUVZJN1FVRkhTRHRCUVVORUxEUkNRVUZKTEVOQlFVTXNUVUZCVFN4UFFVRk9MRWxCUVdsQ0xFMUJRVTBzU1VGQmVFSXNTMEZCYVVNc1EwRkJReXhOUVVGTkxGTkJRVFZETEVWQlFYVkVPMEZCUTI1RUxHdERRVUZOTEZOQlFVNHNSMEZCYTBJc1VVRkJVU3hsUVVGU0xFTkJRWGRDTEV0QlFYaENMRU5CUVd4Q08wRkJRMGc3UVVGRFJDdzJRa0ZCU3l4RlFVRk1MRWxCUVZjc1MwRkJXRHRCUVVOSUxIRkNRVmhFTzBGQldVRXNORUpCUVU4c1NVRkJVQ3hIUVVGakxFbEJRV1E3UVVGRFNEczdRVUZGUkR0QlFVTkJMRzlDUVVGSkxFbEJRVWtzVVVGQlVpeEZRVUZyUWp0QlFVTmtMSGxDUVVGTExFbEJRVWtzVVVGQlZDeEZRVUZ0UWl4VlFVRlZMRTFCUVZZc1JVRkJhMEk3UVVGRGFrTXNORUpCUVVrc2FVSkJRVW83UVVGQlFTdzBRa0ZCWXl4aFFVRmtPenRCUVVWQkxHbERRVUZUTEU5QlFVOHNUVUZCVUN4TFFVRnJRaXhSUVVGc1FpeEhRVUUyUWl4RlFVRkRMRTFCUVUwc1RVRkJVQ3hGUVVFM1FpeEhRVUU0UXl4TlFVRjJSRHM3UVVGRlFTd3JRa0ZCVHl4UFFVRlBMRWxCUVdRN1FVRkRRU3h0UTBGQlZ5eFBRVUZQTEZGQlFXeENPMEZCUTBFc05FSkJRVWtzVVVGQlNpeEZRVUZqTzBGQlExWXNiME5CUVU4c1MwRkJVQ3hEUVVGaExFbEJRV0lzU1VGQmNVSXNUMEZCVHl4UlFVRTFRanRCUVVOSU96dEJRVVZFTzBGQlEwRTdRVUZEUVR0QlFVTkJMR2REUVVGUExFbEJRVkFzUTBGQldTeEpRVUZhTEVsQlFXOUNMRTlCUVU4c1NVRkJVQ3hIUVVGakxFZEJRV1FzUjBGQmIwSXNRMEZCUXl4UFFVRlBMRWxCUVZBc1NVRkJaU3hOUVVGb1FpeEZRVU51UXl4UFFVUnRReXhEUVVNelFpeGhRVVF5UWl4RlFVTmFMRVZCUkZrc1JVRkZia01zVDBGR2JVTXNRMEZGTTBJc1kwRkdNa0lzUlVGRldDeEZRVVpYTEVOQlFYaERPMEZCUjBnc2NVSkJha0pFTzBGQmEwSklPenRCUVVWRU8wRkJRMEU3UVVGRFFTeDVRa0ZCVXl4UlFVRlVMRVZCUVcxQ0xGVkJRVlVzUjBGQlZpeEZRVUZsTEVWQlFXWXNSVUZCYlVJN1FVRkRiRU03UVVGRFFUdEJRVU5CTEhkQ1FVRkpMRU5CUVVNc1NVRkJTU3hOUVVGTUxFbEJRV1VzUTBGQlF5eEpRVUZKTEVkQlFVb3NRMEZCVVN4WlFVRTFRaXhGUVVFd1F6dEJRVU4wUXl3MFFrRkJTU3hIUVVGS0xFZEJRVlVzWTBGQll5eEZRVUZrTEVWQlFXdENMRWxCUVd4Q0xFVkJRWGRDTEVsQlFYaENMRU5CUVZZN1FVRkRTRHRCUVVOS0xHbENRVTVFT3p0QlFWRkJPMEZCUTBFN1FVRkRRU3h2UWtGQlNTeEpRVUZKTEVsQlFVb3NTVUZCV1N4SlFVRkpMRkZCUVhCQ0xFVkJRVGhDTzBGQlF6RkNMRFJDUVVGUkxFOUJRVklzUTBGQlowSXNTVUZCU1N4SlFVRktMRWxCUVZrc1JVRkJOVUlzUlVGQlowTXNTVUZCU1N4UlFVRndRenRCUVVOSU8wRkJRMG9zWVVFdlIwczdPMEZCYVVoT0xEWkNRVUZwUWl4NVFrRkJWU3hMUVVGV0xFVkJRV2xDTzBGQlF6bENMSGxDUVVGVExFVkJRVlFzUjBGQll6dEJRVU5XTEhkQ1FVRkpMRmxCUVVvN1FVRkRRU3gzUWtGQlNTeE5RVUZOTEVsQlFWWXNSVUZCWjBJN1FVRkRXaXc0UWtGQlRTeE5RVUZOTEVsQlFVNHNRMEZCVnl4TFFVRllMRU5CUVdsQ0xFMUJRV3BDTEVWQlFYbENMRk5CUVhwQ0xFTkJRVTQ3UVVGRFNEdEJRVU5FTERKQ1FVRlBMRTlCUVZFc1RVRkJUU3hQUVVGT0xFbEJRV2xDTEZWQlFWVXNUVUZCVFN4UFFVRm9RaXhEUVVGb1F6dEJRVU5JT3p0QlFVVkVMSFZDUVVGUExFVkJRVkE3UVVGRFNDeGhRVE5JU3pzN1FVRTJTRTRzZVVKQlFXRXNjVUpCUVZVc1RVRkJWaXhGUVVGclFpeFBRVUZzUWl4RlFVRXlRanRCUVVOd1F5d3dRa0ZCVlN4WFFVRlhMRVZCUVhKQ096dEJRVVZCTEhsQ1FVRlRMRmxCUVZRc1EwRkJjMElzU1VGQmRFSXNSVUZCTkVJc1VVRkJOVUlzUlVGQmMwTXNUMEZCZEVNc1JVRkJLME03UVVGRE0wTXNkMEpCUVVrc1YwRkJTanRCUVVGQkxIZENRVUZSTEZsQlFWSTdRVUZCUVN4M1FrRkJZU3h0UWtGQllqczdRVUZGUVN4M1FrRkJTU3hSUVVGUkxHMUNRVUZTTEVsQlFTdENMRkZCUVM5Q0xFbEJRVEpETEZkQlFWY3NVVUZCV0N4RFFVRXZReXhGUVVGeFJUdEJRVU5xUlN4cFEwRkJVeXhuUWtGQlZDeEhRVUUwUWl4SlFVRTFRanRCUVVOSU96dEJRVVZFTEhkQ1FVRkpMRTlCUVU4c1NVRkJVQ3hMUVVGblFpeFJRVUZ3UWl4RlFVRTRRanRCUVVNeFFpdzBRa0ZCU1N4WFFVRlhMRkZCUVZnc1EwRkJTaXhGUVVFd1FqdEJRVU4wUWp0QlFVTkJMRzFEUVVGUExGRkJRVkVzVlVGQlZTeGhRVUZXTEVWQlFYbENMSE5DUVVGNlFpeERRVUZTTEVWQlFUQkVMRTlCUVRGRUxFTkJRVkE3UVVGRFNEczdRVUZGUkR0QlFVTkJPMEZCUTBFc05FSkJRVWtzVlVGQlZTeFJRVUZSTEZGQlFWSXNSVUZCYTBJc1NVRkJiRUlzUTBGQlpDeEZRVUYxUXp0QlFVTnVReXh0UTBGQlR5eFRRVUZUTEVsQlFWUXNSVUZCWlN4VFFVRlRMRTlCUVU4c1JVRkJhRUlzUTBGQlppeERRVUZRTzBGQlEwZzdPMEZCUlVRN1FVRkRRVHRCUVVOQkxEUkNRVUZKTEVsQlFVa3NSMEZCVWl4RlFVRmhPMEZCUTFRc2JVTkJRVThzU1VGQlNTeEhRVUZLTEVOQlFWRXNUMEZCVWl4RlFVRnBRaXhKUVVGcVFpeEZRVUYxUWl4TlFVRjJRaXhGUVVFclFpeFpRVUV2UWl4RFFVRlFPMEZCUTBnN08wRkJSVVE3UVVGRFFTdzRRa0ZCVFN4alFVRmpMRWxCUVdRc1JVRkJiMElzVFVGQmNFSXNSVUZCTkVJc1MwRkJOVUlzUlVGQmJVTXNTVUZCYmtNc1EwRkJUanRCUVVOQkxEWkNRVUZMTEVsQlFVa3NSVUZCVkRzN1FVRkZRU3cwUWtGQlNTeERRVUZETEZGQlFWRXNVVUZCVWl4RlFVRnBRaXhGUVVGcVFpeERRVUZNTEVWQlFUSkNPMEZCUTNaQ0xHMURRVUZQTEZGQlFWRXNWVUZCVlN4WFFVRldMRVZCUVhWQ0xHdENRVU5zUXl4RlFVUnJReXhIUVVWc1F5eDVRMEZHYTBNc1IwRkhiRU1zVjBGSWEwTXNTVUZKYWtNc1UwRkJVeXhGUVVGVUxFZEJRV01zYlVKQlNtMUNMRU5CUVhaQ0xFTkJRVklzUTBGQlVEdEJRVXRJTzBGQlEwUXNLMEpCUVU4c1UwRkJVU3hGUVVGU0xFTkJRVkE3UVVGRFNEczdRVUZGUkR0QlFVTkJPenRCUVVWQk8wRkJRMEVzTkVKQlFWRXNVVUZCVWl4RFFVRnBRaXhaUVVGWk8wRkJRM3BDTzBGQlEwRTdRVUZEUVRzN1FVRkZRU3h4UTBGQllTeFZRVUZWTEdOQlFXTXNTVUZCWkN4RlFVRnZRaXhOUVVGd1FpeERRVUZXTEVOQlFXSTdPMEZCUlVFN1FVRkRRVHRCUVVOQkxHMURRVUZYTEU5QlFWZ3NSMEZCY1VJc1VVRkJVU3hQUVVFM1FqczdRVUZGUVN4dFEwRkJWeXhKUVVGWUxFTkJRV2RDTEVsQlFXaENMRVZCUVhOQ0xGRkJRWFJDTEVWQlFXZERMRTlCUVdoRExFVkJRWGxETzBGQlEzSkRMSEZEUVVGVE8wRkJSRFJDTEhsQ1FVRjZRenM3UVVGSlFUdEJRVU5JTEhGQ1FXaENSRHM3UVVGclFrRXNNa0pCUVU4c1dVRkJVRHRCUVVOSU96dEJRVVZFTEhOQ1FVRk5MRmxCUVU0c1JVRkJiMEk3UVVGRGFFSXNLMEpCUVZjc1UwRkVTenM3UVVGSGFFSTdPenM3TzBGQlMwRXNNa0pCUVU4c1pVRkJWU3hwUWtGQlZpeEZRVUUyUWp0QlFVTm9ReXcwUWtGQlNTeFpRVUZLTzBGQlFVRXNORUpCUTBrc1VVRkJVU3hyUWtGQmEwSXNWMEZCYkVJc1EwRkJPRUlzUjBGQk9VSXNRMEZFV2p0QlFVRkJMRFJDUVVWSkxGVkJRVlVzYTBKQlFXdENMRXRCUVd4Q0xFTkJRWGRDTEVkQlFYaENMRVZCUVRaQ0xFTkJRVGRDTEVOQlJtUTdRVUZCUVN3MFFrRkhTU3hoUVVGaExGbEJRVmtzUjBGQldpeEpRVUZ0UWl4WlFVRlpMRWxCU0doRU96dEJRVXRCTzBGQlEwRTdRVUZEUVN3MFFrRkJTU3hWUVVGVkxFTkJRVU1zUTBGQldDeExRVUZwUWl4RFFVRkRMRlZCUVVRc1NVRkJaU3hSUVVGUkxFTkJRWGhETEVOQlFVb3NSVUZCWjBRN1FVRkROVU1zYTBOQlFVMHNhMEpCUVd0Q0xGTkJRV3hDTEVOQlFUUkNMRXRCUVRWQ0xFVkJRVzFETEd0Q1FVRnJRaXhOUVVGeVJDeERRVUZPTzBGQlEwRXNaMFJCUVc5Q0xHdENRVUZyUWl4VFFVRnNRaXhEUVVFMFFpeERRVUUxUWl4RlFVRXJRaXhMUVVFdlFpeERRVUZ3UWp0QlFVTklPenRCUVVWRUxDdENRVUZQTEZGQlFWRXNVMEZCVWl4RFFVRnJRaXhWUVVGVkxHbENRVUZXTEVWQlEzSkNMRlZCUVZVc1QwRkJUeXhGUVVSSkxFVkJRMEVzU1VGRVFTeERRVUZzUWl4RlFVTjVRaXhIUVVSNlFpeEZRVU00UWl4SlFVUTVRaXhEUVVGUU8wRkJSVWdzY1VKQmRrSmxPenRCUVhsQ2FFSXNOa0pCUVZNc2FVSkJRVlVzUlVGQlZpeEZRVUZqTzBGQlEyNUNMQ3RDUVVGUExGRkJRVkVzVVVGQlVpeEZRVUZwUWl4alFVRmpMRVZCUVdRc1JVRkJhMElzVFVGQmJFSXNSVUZCTUVJc1MwRkJNVUlzUlVGQmFVTXNTVUZCYWtNc1JVRkJkVU1zUlVGQmVFUXNRMEZCVUR0QlFVTklMSEZDUVROQ1pUczdRVUUyUW1oQ0xDdENRVUZYTEcxQ1FVRlZMRVZCUVZZc1JVRkJZenRCUVVOeVFpdzJRa0ZCU3l4alFVRmpMRVZCUVdRc1JVRkJhMElzVFVGQmJFSXNSVUZCTUVJc1MwRkJNVUlzUlVGQmFVTXNTVUZCYWtNc1JVRkJkVU1zUlVGQk5VTTdRVUZEUVN3clFrRkJUeXhSUVVGUkxGRkJRVklzUlVGQmFVSXNSVUZCYWtJc1MwRkJkMElzVVVGQlVTeFJRVUZTTEVWQlFXdENMRVZCUVd4Q0xFTkJRUzlDTzBGQlEwZzdRVUZvUTJVc2FVSkJRWEJDT3p0QlFXMURRVHRCUVVOQkxHOUNRVUZKTEVOQlFVTXNUVUZCVEN4RlFVRmhPMEZCUTFRc2FVTkJRV0VzUzBGQllpeEhRVUZ4UWl4VlFVRlZMRVZCUVZZc1JVRkJZenRCUVVNdlFqdEJRVU5CT3p0QlFVVkJMRFJDUVVGSkxFMUJRVTBzWTBGQll5eEZRVUZrTEVWQlFXdENMRTFCUVd4Q0xFVkJRVEJDTEVsQlFURkNMRU5CUVZZN1FVRkRRU3cwUWtGQlNTeE5RVUZOTEU5QlFVOHNVVUZCVUN4RlFVRnBRaXhGUVVGcVFpeERRVUZXT3p0QlFVVkJMRFJDUVVGSkxFOUJRVW9zUjBGQll5eEpRVUZrTzBGQlEwRXNjVU5CUVdFc1JVRkJZanM3UVVGRlFTd3JRa0ZCVHl4VFFVRlJMRVZCUVZJc1EwRkJVRHRCUVVOQkxDdENRVUZQTEZkQlFWY3NTVUZCU1N4SFFVRm1MRU5CUVZBN1FVRkRRU3dyUWtGQlR5eFpRVUZaTEVWQlFWb3NRMEZCVURzN1FVRkZRVHRCUVVOQk8wRkJRMEVzYjBOQlFWa3NVVUZCV2l4RlFVRnpRaXhWUVVGVkxFbEJRVllzUlVGQlowSXNRMEZCYUVJc1JVRkJiVUk3UVVGRGNrTXNaME5CUVVrc1MwRkJTeXhEUVVGTUxFMUJRVmtzUlVGQmFFSXNSVUZCYjBJN1FVRkRhRUlzZVVOQlFWTXNUVUZCVkN4RFFVRm5RaXhEUVVGb1FpeEZRVUZ0UWl4RFFVRnVRanRCUVVOSU8wRkJRMG9zZVVKQlNrUTdRVUZMUVN3clFrRkJUeXhSUVVGUkxGZEJRVklzUTBGQmIwSXNSVUZCY0VJc1EwRkJVRHM3UVVGRlFTdzBRa0ZCU1N4SFFVRktMRVZCUVZNN1FVRkRURHRCUVVOQk8wRkJRMEVzWjBOQlFVa3NTVUZCU1N4TlFVRktMRU5CUVZjc1QwRkJaaXhGUVVGM1FqdEJRVU53UWl3MFEwRkJXU3hGUVVGYUxFbEJRV3RDTEVsQlFVa3NUVUZCZEVJN1FVRkRTRHM3UVVGRlJDd3dRMEZCWXl4RlFVRmtPMEZCUTBnN1FVRkRTaXh4UWtGb1EwUTdRVUZwUTBnN08wRkJSVVFzZFVKQlFVOHNXVUZCVUR0QlFVTklMR0ZCZWxGTE96dEJRVEpSVGpzN096czdRVUZMUVN4dlFrRkJVU3huUWtGQlZTeE5RVUZXTEVWQlFXdENPMEZCUTNSQ0xHOUNRVUZKTEUxQlFVMHNUMEZCVHl4UlFVRlFMRVZCUVdsQ0xFOUJRVThzUlVGQmVFSXNRMEZCVmp0QlFVTkJMRzlDUVVGSkxFZEJRVW9zUlVGQlV6dEJRVU5NTERoQ1FVRlZMRTFCUVZZc1JVRkJhMElzVFVGQmJFSTdRVUZEU0R0QlFVTktMR0ZCY2xKTE96dEJRWFZTVGpzN096czdPenRCUVU5QkxEQkNRVUZqTEhOQ1FVRlZMRlZCUVZZc1JVRkJjMEk3UVVGRGFFTXNiMEpCUVVrc1kwRkJTanRCUVVOQkxHOUNRVUZKTEdGQlFVbzdRVUZEUVN4dlFrRkJTU3haUVVGS08wRkJRMEVzYjBKQlFVa3NUMEZCVHl4UFFVRlBMRkZCUVU4c1NVRkJaQ3hGUVVGdlFpeFZRVUZ3UWl4TFFVRnRReXhGUVVFNVF6dEJRVU5CTEc5Q1FVRkpMRmxCUVZrc1MwRkJTeXhQUVVGeVFqczdRVUZGUVRzN1FVRkZRU3gxUWtGQlR5eFRRVUZUTEUxQlFXaENMRVZCUVhkQ08wRkJRM0JDTERKQ1FVRlBMRk5CUVZNc1MwRkJWQ3hGUVVGUU8wRkJRMEVzZDBKQlFVa3NTMEZCU3l4RFFVRk1MRTFCUVZrc1NVRkJhRUlzUlVGQmMwSTdRVUZEYkVJc05rSkJRVXNzUTBGQlRDeEpRVUZWTEZWQlFWWTdRVUZEUVR0QlFVTkJPMEZCUTBFc05FSkJRVWtzUzBGQlNpeEZRVUZYTzBGQlExQTdRVUZEU0R0QlFVTkVMR2REUVVGUkxFbEJRVkk3UVVGRFNDeHhRa0ZTUkN4TlFWRlBMRWxCUVVrc1MwRkJTeXhEUVVGTUxFMUJRVmtzVlVGQmFFSXNSVUZCTkVJN1FVRkRMMEk3UVVGRFFTeG5RMEZCVVN4SlFVRlNPMEZCUTBnN08wRkJSVVFzYTBOQlFXTXNTVUZCWkR0QlFVTklPMEZCUTBRc2QwSkJRVkVzVjBGQlVpeEhRVUZ6UWl4RlFVRjBRanM3UVVGRlFUdEJRVU5CTzBGQlEwRXNjMEpCUVUwc1QwRkJUeXhSUVVGUUxFVkJRV2xDTEZWQlFXcENMRU5CUVU0N08wRkJSVUVzYjBKQlFVa3NRMEZCUXl4TFFVRkVMRWxCUVZVc1EwRkJReXhSUVVGUkxGRkJRVklzUlVGQmFVSXNWVUZCYWtJc1EwRkJXQ3hKUVVFeVF5eEhRVUV6UXl4SlFVRnJSQ3hEUVVGRExFbEJRVWtzVFVGQk0wUXNSVUZCYlVVN1FVRkRMMFFzZDBKQlFVa3NVVUZCVHl4aFFVRlFMRXRCUVhsQ0xFTkJRVU1zVTBGQlJDeEpRVUZqTEVOQlFVTXNWVUZCVlN4VFFVRldMRU5CUVhoRExFTkJRVW9zUlVGQmJVVTdRVUZETDBRc05FSkJRVWtzWjBKQlFXZENMRlZCUVdoQ0xFTkJRVW9zUlVGQmFVTTdRVUZETjBJN1FVRkRTQ3g1UWtGR1JDeE5RVVZQTzBGQlEwZ3NiVU5CUVU4c1VVRkJVU3hWUVVGVkxGVkJRVllzUlVGRFdDeDNRa0ZCZDBJc1ZVRkVZaXhGUVVWWUxFbEJSbGNzUlVGSFdDeERRVUZETEZWQlFVUXNRMEZJVnl4RFFVRlNMRU5CUVZBN1FVRkpTRHRCUVVOS0xIRkNRVlJFTEUxQlUwODdRVUZEU0R0QlFVTkJMSE5EUVVGakxFTkJRVU1zVlVGQlJDeEZRVUZqTEV0QlFVc3NTVUZCVEN4SlFVRmhMRVZCUVROQ0xFVkJRV2RETEV0QlFVc3NVMEZCY2tNc1EwRkJaRHRCUVVOSU8wRkJRMG83TzBGQlJVUTdRVUZEU0N4aFFTOVZTenM3UVVGcFZrNDdPenM3T3pzN08wRkJVVUVzZFVKQlFWY3NiVUpCUVZVc1ZVRkJWaXhGUVVGelFpeEhRVUYwUWl4RlFVRXlRaXhQUVVFelFpeEZRVUZ2UXp0QlFVTXpReXh2UWtGQlNTeGpRVUZLTzBGQlEwRXNiMEpCUVVrc1lVRkJTanRCUVVOQkxHOUNRVUZKTEZWQlFVbzdRVUZEUVN4dlFrRkJTU3h4UWtGQlNqdEJRVU5CTEc5Q1FVRkpMRmxCUVVvN1FVRkRRU3h2UWtGQlNTeHRRa0ZCU2p0QlFVRkJMRzlDUVVGblFpeHBRa0ZCYUVJN1FVRkRRU3h2UWtGQlNTeFZRVUZWTEU5QlFVOHNVVUZCVHl4SlFVRmtMRVZCUVc5Q0xGVkJRWEJDTEVOQlFXUTdPMEZCUlVFc2IwSkJRVWtzVDBGQlNpeEZRVUZoTzBGQlExUXNhVU5CUVdFc1QwRkJZanRCUVVOSU96dEJRVVZFTERKQ1FVRlhMRTlCUVU4c1ZVRkJVQ3hGUVVGdFFpeFZRVUZ1UWl4RFFVRllPenRCUVVWQkxHOUNRVUZKTEZGQlFVb3NSVUZCWXp0QlFVTldMREpDUVVGUExGRkJRVkVzVTBGQlVpeERRVUZyUWl4UlFVRnNRaXhGUVVFMFFpeEhRVUUxUWl4RlFVRnBReXhQUVVGcVF5eERRVUZRTzBGQlEwZzdPMEZCUlVRN1FVRkRRVHRCUVVOQk8wRkJRMEVzYjBKQlFVa3NTVUZCU1N4WFFVRktMRU5CUVdkQ0xFbEJRV2hDTEVOQlFYRkNMRlZCUVhKQ0xFTkJRVW9zUlVGQmMwTTdRVUZEYkVNN1FVRkRRVHRCUVVOQk8wRkJRMEVzTUVKQlFVMHNZMEZCWXl4UFFVRlBMRVZCUVhKQ0xFTkJRVTQ3UVVGRFNDeHBRa0ZNUkN4TlFVdFBPMEZCUTBnN1FVRkRRU3cwUWtGQlVTeFJRVUZQTEV0QlFXWTdPMEZCUlVFc01rSkJRVThzVjBGQlZ5eExRVUZZTEVOQlFXbENMRWRCUVdwQ0xFTkJRVkE3UVVGRFFUdEJRVU5CTzBGQlEwRXNlVUpCUVVzc1NVRkJTU3hMUVVGTExFMUJRV1FzUlVGQmMwSXNTVUZCU1N4RFFVRXhRaXhGUVVFMlFpeExRVUZMTEVOQlFXeERMRVZCUVhGRE8wRkJRMnBETEhWRFFVRmxMRXRCUVVzc1MwRkJUQ3hEUVVGWExFTkJRVmdzUlVGQll5eERRVUZrTEVWQlFXbENMRWxCUVdwQ0xFTkJRWE5DTEVkQlFYUkNMRU5CUVdZN08wRkJSVUVzY1VOQlFXRXNUMEZCVHl4TFFVRlFMRVZCUVdNc1dVRkJaQ3hEUVVGaU8wRkJRMEVzTkVKQlFVa3NWVUZCU2l4RlFVRm5RanRCUVVOYU8wRkJRMEVzWjBOQlFVa3NVVUZCVVN4VlFVRlNMRU5CUVVvc1JVRkJlVUk3UVVGRGNrSXNOa05CUVdFc1YwRkJWeXhEUVVGWUxFTkJRV0k3UVVGRFNEdEJRVU5FTEdsRFFVRkxMRTFCUVV3c1EwRkJXU3hEUVVGYUxFVkJRV1VzUTBGQlppeEZRVUZyUWl4VlFVRnNRanRCUVVOQk8wRkJRMGc3UVVGRFNqczdRVUZGUkR0QlFVTkJMREJDUVVGTkxFdEJRVXNzU1VGQlRDeERRVUZWTEVkQlFWWXNRMEZCVGp0QlFVTkJMREpDUVVGUkxGRkJRVkVzWVVGQllTeEpRVUZpTEVOQlFXdENMRWRCUVd4Q0xFdEJRVEJDTEU5QlFURkNMRWRCUVc5RExFVkJRWEJETEVkQlFYbERMRXRCUVdwRUxFTkJRVkk3UVVGRFFTd3dRa0ZCVFN4RFFVRkRMRWxCUVVrc1RVRkJTaXhEUVVGWExFTkJRVmdzVFVGQmEwSXNSMEZCYkVJc1NVRkJlVUlzU1VGQlNTeExRVUZLTEVOQlFWVXNaVUZCVml4RFFVRjZRaXhIUVVGelJDeEZRVUYwUkN4SFFVRXlSQ3hSUVVGUExFOUJRVzVGTEVsQlFUaEZMRWRCUVhCR08wRkJRMGc3TzBGQlJVUXNkVUpCUVU4c1VVRkJUeXhQUVVGUUxFZEJRV2xDTEU5QlEyNUNMRU5CUVVNc1NVRkJTU3hQUVVGS0xFTkJRVmtzUjBGQldpeE5RVUZ4UWl4RFFVRkRMRU5CUVhSQ0xFZEJRVEJDTEVkQlFURkNMRWRCUVdkRExFZEJRV3BETEVsQlEwY3NVVUZCVHl4UFFVWlRMRU5CUVdwQ0xFZEJSVzFDTEVkQlJqRkNPMEZCUjBnc1lVRnNXa3M3TzBGQmIxcE9PMEZCUTBFc2EwSkJRVTBzWTBGQlZTeEZRVUZXTEVWQlFXTXNSMEZCWkN4RlFVRnRRanRCUVVOeVFpeHZRa0ZCU1N4SlFVRktMRU5CUVZNc1QwRkJWQ3hGUVVGclFpeEZRVUZzUWl4RlFVRnpRaXhIUVVGMFFqdEJRVU5JTEdGQmRscExPenRCUVhsYVRqczdPenM3T3pzN1FVRlJRU3h2UWtGQlVTeG5Ra0ZCVlN4SlFVRldMRVZCUVdkQ0xGRkJRV2hDTEVWQlFUQkNMRWxCUVRGQ0xFVkJRV2RETEU5QlFXaERMRVZCUVhsRE8wRkJRemRETEhWQ1FVRlBMRk5CUVZNc1MwRkJWQ3hEUVVGbExFOUJRV1lzUlVGQmQwSXNTVUZCZUVJc1EwRkJVRHRCUVVOSUxHRkJibUZMT3p0QlFYRmhUanM3T3pzN1FVRkxRU3d3UWtGQll5eHpRa0ZCVlN4SFFVRldMRVZCUVdVN1FVRkRla0k3UVVGRFFUdEJRVU5CTzBGQlEwRXNiMEpCUVVrc1NVRkJTU3hKUVVGS0xFdEJRV0VzVFVGQllpeEpRVU5ETEZsQlFWa3NTVUZCV2l4RFFVRnBRaXhEUVVGRExFbEJRVWtzWVVGQlNpeEpRVUZ4UWl4SlFVRkpMRlZCUVRGQ0xFVkJRWE5ETEZWQlFYWkVMRU5CUkV3c1JVRkRNRVU3UVVGRGRFVTdRVUZEUVR0QlFVTkJMSGREUVVGdlFpeEpRVUZ3UWpzN1FVRkZRVHRCUVVOQkxIZENRVUZKTEU5QlFVOHNZMEZCWXl4SFFVRmtMRU5CUVZnN1FVRkRRU3cwUWtGQlVTeFpRVUZTTEVOQlFYRkNMRXRCUVVzc1JVRkJNVUk3UVVGRFNEdEJRVU5LTEdGQmVHSkxPenRCUVRCaVRqczdPMEZCUjBFc01rSkJRV1VzZFVKQlFWVXNSMEZCVml4RlFVRmxPMEZCUXpGQ0xHOUNRVUZKTEU5QlFVOHNZMEZCWXl4SFFVRmtMRU5CUVZnN1FVRkRRU3h2UWtGQlNTeERRVUZETEdkQ1FVRm5RaXhMUVVGTExFVkJRWEpDTEVOQlFVd3NSVUZCSzBJN1FVRkRNMElzZDBKQlFVa3NWVUZCVlN4RlFVRmtPMEZCUTBFc05rSkJRVk1zVVVGQlZDeEZRVUZ0UWl4VlFVRlZMRXRCUVZZc1JVRkJhVUlzUjBGQmFrSXNSVUZCYzBJN1FVRkRja01zTkVKQlFVa3NTVUZCU1N4UFFVRktMRU5CUVZrc1MwRkJXaXhOUVVGMVFpeERRVUV6UWl4RlFVRTRRanRCUVVNeFFpeHBRMEZCU3l4TlFVRk5MRTlCUVZnc1JVRkJiMElzVlVGQlZTeE5RVUZXTEVWQlFXdENPMEZCUTJ4RExHOURRVUZKTEU5QlFVOHNSVUZCVUN4TFFVRmpMRXRCUVVzc1JVRkJka0lzUlVGQk1rSTdRVUZEZGtJc05FTkJRVkVzU1VGQlVpeERRVUZoTEVkQlFXSTdRVUZEU0R0QlFVTkVMSFZEUVVGUExFbEJRVkE3UVVGRFNDdzJRa0ZNUkR0QlFVMUlPMEZCUTBvc2NVSkJWRVE3UVVGVlFTd3lRa0ZCVHl4UlFVRlJMRlZCUVZVc1lVRkJWaXhGUVVGNVFpeDFRa0ZCZFVJc1MwRkJTeXhGUVVFMVFpeEpRVU51UXl4UlFVRlJMRTFCUVZJc1IwRkRSeXh0UWtGQmJVSXNVVUZCVVN4SlFVRlNMRU5CUVdFc1NVRkJZaXhEUVVSMFFpeEhRVVZITEVkQlNHZERMRU5CUVhwQ0xFVkJSMFFzUjBGSVF5eEZRVWRKTEVOQlFVTXNTMEZCU3l4RlFVRk9MRU5CU0Vvc1EwRkJVaXhEUVVGUU8wRkJTVWc3UVVGRFNqdEJRV2hrU3l4VFFVRldPenRCUVcxa1FTeG5Ra0ZCVVN4UFFVRlNMRWRCUVd0Q0xGRkJRVkVzVjBGQlVpeEZRVUZzUWp0QlFVTkJMR1ZCUVU4c1QwRkJVRHRCUVVOSU96dEJRVVZFT3pzN096czdPenM3T3pzN1FVRlpRU3hWUVVGTkxFOUJRVThzVTBGQlVDeEhRVUZ0UWl4VlFVRlZMRWxCUVZZc1JVRkJaMElzVVVGQmFFSXNSVUZCTUVJc1QwRkJNVUlzUlVGQmJVTXNVVUZCYmtNc1JVRkJOa003UVVGRGJFVTdRVUZEUVN4WlFVRkpMR2RDUVVGS08wRkJRMEVzV1VGQlNTeGxRVUZLTzBGQlEwRXNXVUZCU1N4alFVRmpMR05CUVd4Q096dEJRVVZCTzBGQlEwRXNXVUZCU1N4RFFVRkRMRkZCUVZFc1NVRkJVaXhEUVVGRUxFbEJRV3RDTEU5QlFVOHNTVUZCVUN4TFFVRm5RaXhSUVVGMFF5eEZRVUZuUkR0QlFVTTFRenRCUVVOQkxIRkNRVUZUTEVsQlFWUTdRVUZEUVN4blFrRkJTU3hSUVVGUkxGRkJRVklzUTBGQlNpeEZRVUYxUWp0QlFVTnVRanRCUVVOQkxIVkNRVUZQTEZGQlFWQTdRVUZEUVN3eVFrRkJWeXhQUVVGWU8wRkJRMEVzTUVKQlFWVXNVVUZCVmp0QlFVTklMR0ZCVEVRc1RVRkxUenRCUVVOSUxIVkNRVUZQTEVWQlFWQTdRVUZEU0R0QlFVTktPenRCUVVWRUxGbEJRVWtzVlVGQlZTeFBRVUZQTEU5QlFYSkNMRVZCUVRoQ08wRkJRekZDTERCQ1FVRmpMRTlCUVU4c1QwRkJja0k3UVVGRFNEczdRVUZGUkN4clFrRkJWU3hQUVVGUExGRkJRVkFzUlVGQmFVSXNWMEZCYWtJc1EwRkJWanRCUVVOQkxGbEJRVWtzUTBGQlF5eFBRVUZNTEVWQlFXTTdRVUZEVml4elFrRkJWU3hUUVVGVExGZEJRVlFzU1VGQmQwSXNTVUZCU1N4RFFVRktMRU5CUVUwc1ZVRkJUaXhEUVVGcFFpeFhRVUZxUWl4RFFVRnNRenRCUVVOSU96dEJRVVZFTEZsQlFVa3NUVUZCU2l4RlFVRlpPMEZCUTFJc2IwSkJRVkVzVTBGQlVpeERRVUZyUWl4TlFVRnNRanRCUVVOSU96dEJRVVZFTEdWQlFVOHNVVUZCVVN4UFFVRlNMRU5CUVdkQ0xFbEJRV2hDTEVWQlFYTkNMRkZCUVhSQ0xFVkJRV2RETEU5QlFXaERMRU5CUVZBN1FVRkRTQ3hMUVd4RFJEczdRVUZ2UTBFN096dEJRVWRCTEZGQlFVa3NUVUZCU2l4SFFVRmhMRlZCUVZVc1RVRkJWaXhGUVVGclFqdEJRVU16UWl4bFFVRlBMRWxCUVVrc1RVRkJTaXhEUVVGUU8wRkJRMGdzUzBGR1JEczdRVUZKUVRzN096czdPenRCUVU5QkxGRkJRVWtzVVVGQlNpeEhRVUZsTEU5QlFVOHNWVUZCVUN4TFFVRnpRaXhYUVVGMFFpeEhRVUZ2UXl4VlFVRlZMRVZCUVZZc1JVRkJZenRCUVVNM1JDeHRRa0ZCVnl4RlFVRllMRVZCUVdVc1EwRkJaanRCUVVOSUxFdEJSbU1zUjBGRldDeFZRVUZWTEVWQlFWWXNSVUZCWXp0QlFVTmtPMEZCUTBnc1MwRktSRHM3UVVGTlFUczdPMEZCUjBFc1VVRkJTU3hEUVVGRExFOUJRVThzVDBGQldpeEZRVUZ4UWp0QlFVTnFRaXhsUVVGUExFOUJRVkFzUjBGQmFVSXNSMEZCYWtJN1FVRkRTRHM3UVVGRlJDeFJRVUZKTEU5QlFVb3NSMEZCWXl4UFFVRmtPenRCUVVWQk8wRkJRMEVzVVVGQlNTeFhRVUZLTEVkQlFXdENMR2RDUVVGc1FqdEJRVU5CTEZGQlFVa3NVMEZCU2l4SFFVRm5RaXhUUVVGb1FqdEJRVU5CTEZGQlFVa3NTVUZCU1N4RFFVRktMRWRCUVZFN1FVRkRVaXhyUWtGQlZTeFJRVVJHTzBGQlJWSXNiMEpCUVZrN1FVRkdTaXhMUVVGYU96dEJRVXRCTzBGQlEwRXNVVUZCU1N4RlFVRktPenRCUVVWQk8wRkJRMEVzVTBGQlN5eERRVU5FTEU5QlJFTXNSVUZGUkN4UFFVWkRMRVZCUjBRc1UwRklReXhGUVVsRUxGZEJTa01zUTBGQlRDeEZRVXRITEZWQlFWVXNTVUZCVml4RlFVRm5RanRCUVVObU8wRkJRMEU3UVVGRFFUdEJRVU5CTEZsQlFVa3NTVUZCU2l4SlFVRlpMRmxCUVZrN1FVRkRjRUlzWjBKQlFVa3NUVUZCVFN4VFFVRlRMR05CUVZRc1EwRkJWanRCUVVOQkxHMUNRVUZQTEVsQlFVa3NUMEZCU2l4RFFVRlpMRWxCUVZvc1JVRkJhMElzUzBGQmJFSXNRMEZCZDBJc1IwRkJlRUlzUlVGQk5rSXNVMEZCTjBJc1EwRkJVRHRCUVVOSUxGTkJTRVE3UVVGSlNDeExRV0pFT3p0QlFXVkJMRkZCUVVrc1UwRkJTaXhGUVVGbE8wRkJRMWdzWlVGQlR5eEZRVUZGTEVsQlFVWXNSMEZCVXl4VFFVRlRMRzlDUVVGVUxFTkJRVGhDTEUxQlFUbENMRVZCUVhORExFTkJRWFJETEVOQlFXaENPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEVzYzBKQlFXTXNVMEZCVXl4dlFrRkJWQ3hEUVVFNFFpeE5RVUU1UWl4RlFVRnpReXhEUVVGMFF5eERRVUZrTzBGQlEwRXNXVUZCU1N4WFFVRktMRVZCUVdsQ08wRkJRMklzYlVKQlFVOHNSVUZCUlN4SlFVRkdMRWRCUVZNc1dVRkJXU3hWUVVFMVFqdEJRVU5JTzBGQlEwbzdPMEZCUlVRN096czdPenM3UVVGUFFTeFJRVUZKTEU5QlFVb3NSMEZCWXl4alFVRmtPenRCUVVWQk96czdRVUZIUVN4UlFVRkpMRlZCUVVvc1IwRkJhVUlzVlVGQlZTeE5RVUZXTEVWQlFXdENMRlZCUVd4Q0xFVkJRVGhDTEVkQlFUbENMRVZCUVcxRE8wRkJRMmhFTEZsQlFVMHNUMEZCVHl4UFFVRlBMRXRCUVZBc1IwRkRWQ3hUUVVGVExHVkJRVlFzUTBGQmVVSXNPRUpCUVhwQ0xFVkJRWGxFTEdGQlFYcEVMRU5CUkZNc1IwRkZWQ3hUUVVGVExHRkJRVlFzUTBGQmRVSXNVVUZCZGtJc1EwRkdTanRCUVVkQkxHRkJRVXNzU1VGQlRDeEhRVUZaTEU5QlFVOHNWVUZCVUN4SlFVRnhRaXhwUWtGQmFrTTdRVUZEUVN4aFFVRkxMRTlCUVV3c1IwRkJaU3hQUVVGbU8wRkJRMEVzWVVGQlN5eExRVUZNTEVkQlFXRXNTVUZCWWp0QlFVTkJMR1ZCUVU4c1NVRkJVRHRCUVVOSUxFdEJVa1E3TzBGQlZVRTdPenM3T3pzN096dEJRVk5CTEZGQlFVa3NTVUZCU2l4SFFVRlhMRlZCUVZVc1QwRkJWaXhGUVVGdFFpeFZRVUZ1UWl4RlFVRXJRaXhIUVVFdlFpeEZRVUZ2UXp0QlFVTXpReXhaUVVGSkxGTkJRVlVzVjBGQlZ5eFJRVUZSTEUxQlFYQkNMRWxCUVN0Q0xFVkJRVFZETzBGQlEwRXNXVUZCU1N4aFFVRktPenRCUVVWQkxGbEJRVWtzVTBGQlNpeEZRVUZsTzBGQlExZzdRVUZEUVN4dFFrRkJUeXhKUVVGSkxGVkJRVW9zUTBGQlpTeE5RVUZtTEVWQlFYVkNMRlZCUVhaQ0xFVkJRVzFETEVkQlFXNURMRU5CUVZBN1FVRkRRU3huUWtGQlNTeFBRVUZQTEdGQlFWZ3NSVUZCTUVJN1FVRkRkRUlzZFVKQlFVOHNZVUZCVUN4RFFVRnhRaXhKUVVGeVFpeEZRVUV5UWl4TlFVRXpRaXhGUVVGdFF5eFZRVUZ1UXl4RlFVRXJReXhIUVVFdlF6dEJRVU5JT3p0QlFVVkVMR2xDUVVGTExGbEJRVXdzUTBGQmEwSXNjVUpCUVd4Q0xFVkJRWGxETEZGQlFWRXNWMEZCYWtRN1FVRkRRU3hwUWtGQlN5eFpRVUZNTEVOQlFXdENMRzlDUVVGc1FpeEZRVUYzUXl4VlFVRjRRenM3UVVGRlFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFc1owSkJRVWtzUzBGQlN5eFhRVUZNTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQkxHTkJRVVVzUzBGQlN5eFhRVUZNTEVOQlFXbENMRkZCUVdwQ0xFbEJRVFpDTEV0QlFVc3NWMEZCVEN4RFFVRnBRaXhSUVVGcVFpeEhRVUUwUWl4UFFVRTFRaXhEUVVGdlF5eGpRVUZ3UXl4SlFVRnpSQ3hEUVVGeVJpeERRVTVCTEVsQlQwRXNRMEZCUXl4UFFWQk1MRVZCVDJNN1FVRkRWanRCUVVOQk8wRkJRMEU3UVVGRFFTeHBRMEZCYVVJc1NVRkJha0k3TzBGQlJVRXNjVUpCUVVzc1YwRkJUQ3hEUVVGcFFpeHZRa0ZCYWtJc1JVRkJkVU1zVVVGQlVTeFpRVUV2UXp0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwZ3NZVUZ5UWtRc1RVRnhRazg3UVVGRFNDeHhRa0ZCU3l4blFrRkJUQ3hEUVVGelFpeE5RVUYwUWl4RlFVRTRRaXhSUVVGUkxGbEJRWFJETEVWQlFXOUVMRXRCUVhCRU8wRkJRMEVzY1VKQlFVc3NaMEpCUVV3c1EwRkJjMElzVDBGQmRFSXNSVUZCSzBJc1VVRkJVU3hoUVVGMlF5eEZRVUZ6UkN4TFFVRjBSRHRCUVVOSU8wRkJRMFFzYVVKQlFVc3NSMEZCVEN4SFFVRlhMRWRCUVZnN08wRkJSVUU3UVVGRFFUdEJRVU5CTzBGQlEwRXNaMEpCUVVrc1YwRkJTaXhGUVVGcFFqdEJRVU5pTEhGQ1FVRkxMRmxCUVV3c1EwRkJhMElzU1VGQmJFSXNSVUZCZDBJc1YwRkJlRUk3UVVGRFNDeGhRVVpFTEUxQlJVODdRVUZEU0N4eFFrRkJTeXhYUVVGTUxFTkJRV2xDTEVsQlFXcENPMEZCUTBnN08wRkJSVVFzYlVKQlFVOHNTVUZCVUR0QlFVTklMRk5CY0VSRUxFMUJiMFJQTEVsQlFVa3NWMEZCU2l4RlFVRnBRanRCUVVOd1FpeG5Ra0ZCU1R0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTERoQ1FVRmpMRWRCUVdRN08wRkJSVUU3UVVGRFFTeDNRa0ZCVVN4WlFVRlNMRU5CUVhGQ0xGVkJRWEpDTzBGQlEwZ3NZVUZZUkN4RFFWZEZMRTlCUVU4c1EwRkJVQ3hGUVVGVk8wRkJRMUlzZDBKQlFWRXNUMEZCVWl4RFFVRm5RaXhWUVVGVkxHVkJRVllzUlVGRFdpdzRRa0ZEUVN4VlFVUkJMRWRCUTJFc1RVRkVZaXhIUVVOelFpeEhRVVpXTEVWQlIxb3NRMEZJV1N4RlFVbGFMRU5CUVVNc1ZVRkJSQ3hEUVVwWkxFTkJRV2hDTzBGQlMwZzdRVUZEU2p0QlFVTktMRXRCTlVWRU96dEJRVGhGUVR0QlFVTkJMRkZCUVVrc1lVRkJZU3hEUVVGRExFbEJRVWtzV1VGQmRFSXNSVUZCYjBNN1FVRkRhRU03UVVGRFFTeHZRa0ZCV1N4VFFVRmFMRVZCUVhWQ0xGVkJRVlVzVFVGQlZpeEZRVUZyUWp0QlFVTnlRenRCUVVOQk8wRkJRMEVzWjBKQlFVa3NRMEZCUXl4SlFVRk1MRVZCUVZjN1FVRkRVQ3gxUWtGQlR5eFBRVUZQTEZWQlFXUTdRVUZEU0RzN1FVRkZSRHRCUVVOQk8wRkJRMEU3UVVGRFFTeDFRa0ZCVnl4UFFVRlBMRmxCUVZBc1EwRkJiMElzVjBGQmNFSXNRMEZCV0R0QlFVTkJMR2RDUVVGSkxGRkJRVW9zUlVGQll6dEJRVU5XTzBGQlEwRXNOa0pCUVdFc1VVRkJZanM3UVVGRlFUdEJRVU5CTEc5Q1FVRkpMRU5CUVVNc1NVRkJTU3hQUVVGVUxFVkJRV3RDTzBGQlEyUTdRVUZEUVR0QlFVTkJMREJDUVVGTkxGZEJRVmNzUzBGQldDeERRVUZwUWl4SFFVRnFRaXhEUVVGT08wRkJRMEVzYVVOQlFXRXNTVUZCU1N4SFFVRktMRVZCUVdJN1FVRkRRU3c0UWtGQlZTeEpRVUZKTEUxQlFVb3NSMEZCWVN4SlFVRkpMRWxCUVVvc1EwRkJVeXhIUVVGVUxFbEJRV2RDTEVkQlFUZENMRWRCUVcxRExFbEJRVGRET3p0QlFVVkJMSGRDUVVGSkxFOUJRVW9zUjBGQll5eFBRVUZrTzBGQlEwZzdPMEZCUlVRN1FVRkRRVHRCUVVOQkxEWkNRVUZoTEZkQlFWY3NUMEZCV0N4RFFVRnRRaXhqUVVGdVFpeEZRVUZ0UXl4RlFVRnVReXhEUVVGaU96dEJRVVZCTzBGQlEwRXNiMEpCUVVrc1NVRkJTU3hYUVVGS0xFTkJRV2RDTEVsQlFXaENMRU5CUVhGQ0xGVkJRWEpDTEVOQlFVb3NSVUZCYzBNN1FVRkRiRU1zYVVOQlFXRXNVVUZCWWp0QlFVTklPenRCUVVWRU8wRkJRMEVzYjBKQlFVa3NTVUZCU2l4SFFVRlhMRWxCUVVrc1NVRkJTaXhIUVVGWExFbEJRVWtzU1VGQlNpeERRVUZUTEUxQlFWUXNRMEZCWjBJc1ZVRkJhRUlzUTBGQldDeEhRVUY1UXl4RFFVRkRMRlZCUVVRc1EwRkJjRVE3TzBGQlJVRXNkVUpCUVU4c1NVRkJVRHRCUVVOSU8wRkJRMG9zVTBGNFEwUTdRVUY1UTBnN08wRkJSVVE3T3pzN096dEJRVTFCTEZGQlFVa3NTVUZCU2l4SFFVRlhMRlZCUVZVc1NVRkJWaXhGUVVGblFqdEJRVU4yUWp0QlFVTkJMR1ZCUVU4c1MwRkJTeXhKUVVGTUxFTkJRVkE3UVVGRFNDeExRVWhFT3p0QlFVdEJPMEZCUTBFc1VVRkJTU3hIUVVGS08wRkJRMGdzUTBFMUswUkVPenM3T3pzN08wRkRja0pCT3pzN096czdPenM3TzBGQlZVRXNTVUZCU1N4SlFVRktMRU5CUVZNc1IwRkJWQ3hIUVVGbExFbEJRVWtzU1VGQlNpeERRVUZUTEVkQlFWUXNTVUZCWjBJc1JVRkJMMEk3TzBGQlJVRTdPenM3T3pzN096czdPenM3T3pzN096czdPenM3T3pzN096czdPenM3TzBGQlowTkJMRU5CUVVNc1ZVRkJWU3hQUVVGV0xFVkJRVzFDT3p0QlFVVm9RanM3UVVGRlFUczdPenM3TzBGQlMwRXNVVUZCVFN4WlFVRlpMRVZCUVd4Q096dEJRVVZCT3pzN096czdPMEZCVDBFc1VVRkJUU3hoUVVGaExFVkJRVzVDT3p0QlFVVkJPenM3T3pzN08wRkJUMEVzVVVGQlRTeHJRa0ZCYTBJc1JVRkJlRUk3TzBGQlJVRTdPenRCUVVkQkxHRkJRVk1zWjBKQlFWUXNRMEZCTUVJc2QwSkJRVEZDTEVWQlFXOUVMRmxCUVUwN1FVRkRkRVFzV1VGQlRTeGxRVUZsTEVsQlFVa3NTVUZCU2l4RFFVRlRMRTFCUVZRc1EwRkJaMElzUjBGQmFFSXNRMEZCYjBJc1MwRkJjRUlzUTBGQmNrSTdPMEZCUlVFN1FVRkRRU3haUVVGSkxFTkJRVU1zV1VGQlRDeEZRVUZ0UWp0QlFVTm1PMEZCUTBnN08wRkJSVVFzV1VGQlRTeFJRVUZSTEZOQlFWTXNaMEpCUVZRc1EwRkJNRUlzWVVGQllTeEZRVUYyUXl4RFFVRmtPenRCUVVWQkxHTkJRVTBzU1VGQlRpeERRVUZYTEV0QlFWZ3NSVUZCYTBJc1QwRkJiRUlzUTBGQk1FSXNWVUZCUXl4SlFVRkVMRVZCUVU4c1MwRkJVQ3hGUVVGcFFqdEJRVU4yUXl4blFrRkJUU3hQUVVGUExFdEJRVXNzV1VGQlRDeERRVUZyUWl4M1FrRkJiRUlzZFVKQlFTdEVMRXRCUVRWRk8wRkJRMEVzWjBKQlFVMHNVMEZCVXl4UFFVRlBMRTFCUVZBc1EwRkJZeXhGUVVGa0xFVkJRV3RDTEZsQlFXeENMRVZCUVdkRExFVkJRVU1zU1VGQlNTeEpRVUZNTEVWQlFXaERMRU5CUVdZN1FVRkRRU3huUWtGQlNTeEpRVUZLTEVOQlFWTXNSMEZCVkN4RFFVRmhMRTFCUVdJc1EwRkJiMElzU1VGQmNFSXNSVUZCTUVJc1RVRkJNVUk3UVVGRFNDeFRRVXBFTzBGQlMwZ3NTMEZtUkRzN1FVRnBRa0U3T3pzN08wRkJTMEVzV1VGQlVTeGpRVUZTTEVkQlFYbENMRlZCUVZVc1RVRkJWaXhGUVVGclFqdEJRVU4yUXl4WlFVRkpMRU5CUVVNc1RVRkJSQ3hKUVVGWExFTkJRVU1zVDBGQlR5eEhRVUYyUWl4RlFVRTBRanRCUVVONFFqdEJRVU5JT3p0QlFVVkVMRmxCUVVrc1EwRkJReXhQUVVGUExFMUJRVm9zUlVGQmIwSTdRVUZEYUVJc1owSkJRVWtzU1VGQlNpeERRVUZUTEVkQlFWUXNRMEZCWVN4cFFrRkJZaXhEUVVFclFpeFBRVUZQTEVsQlFYUkRMRVZCUVRSRExFMUJRVFZETzBGQlEwZ3NVMEZHUkN4TlFVVlBPMEZCUTBnc1owSkJRVWtzU1VGQlNpeERRVUZUTEVkQlFWUXNRMEZCWVN4elFrRkJZaXhEUVVGdlF5eFBRVUZQTEUxQlFUTkRMRVZCUVcxRUxFOUJRVThzU1VGQk1VUXNSVUZCWjBVc1RVRkJhRVU3UVVGRFNEdEJRVU5LTEV0QlZrUTdPMEZCV1VFN096czdPenM3UVVGUFFTeFpRVUZSTEhOQ1FVRlNMRWRCUVdsRExGVkJRVlVzVFVGQlZpeEZRVUZyUWl4SlFVRnNRaXhGUVVGM1FpeGpRVUY0UWl4RlFVRjNRenRCUVVOeVJTeDNRa0ZCWjBJc1RVRkJhRUlzU1VGQk1FSXNaMEpCUVdkQ0xFMUJRV2hDTEV0QlFUSkNMRVZCUVhKRU96dEJRVVZCTEZsQlFVa3NaMEpCUVdkQ0xFMUJRV2hDTEVWQlFYZENMRWxCUVhoQ0xFTkJRVW9zUlVGQmJVTTdRVUZETDBJN1FVRkRTRHM3UVVGRlJDeDNRa0ZCWjBJc1RVRkJhRUlzUlVGQmQwSXNTVUZCZUVJc1NVRkJaME1zWTBGQmFFTTdRVUZEU0N4TFFWSkVPenRCUVZWQk96czdPenM3UVVGTlFTeFpRVUZSTEdsQ1FVRlNMRWRCUVRSQ0xGVkJRVlVzU1VGQlZpeEZRVUZuUWl4VFFVRm9RaXhGUVVFeVFqdEJRVU51UkN4WlFVRkpMRmRCUVZjc1NVRkJXQ3hEUVVGS0xFVkJRWE5DTzBGQlEyeENPMEZCUTBnN08wRkJSVVFzYlVKQlFWY3NTVUZCV0N4SlFVRnRRaXhUUVVGdVFqdEJRVU5JTEV0QlRrUTdPMEZCVVVFN096czdPenRCUVUxQkxGbEJRVkVzVFVGQlVpeEhRVUZwUWl4VlFVRlZMRWxCUVZZc1JVRkJaMElzVFVGQmFFSXNSVUZCZDBJN1FVRkRja01zV1VGQlNTeFZRVUZWTEVsQlFWWXNRMEZCU2l4RlFVRnhRanRCUVVOcVFpeHJRa0ZCVFN4SlFVRkpMRXRCUVVvc2VVSkJRV2RETEVsQlFXaERMRzFFUVVGT08wRkJRMGc3TzBGQlJVUXNZVUZCU3l4SlFVRkpMRXRCUVZRc1NVRkJhVUlzVlVGQmFrSXNSVUZCTmtJN1FVRkRla0lzZFVKQlFWY3NTMEZCV0N4RlFVRnBRaXhWUVVGcVFpeEhRVUU0UWl4blFrRkJaMElzUzBGQmFFSXNTMEZCZVVJc1JVRkJka1E3TzBGQlJVRXNaMEpCUVVrc1UwRkJTaXhEUVVGakxFdEJRV1FzUlVGQmIwSXNWMEZCVnl4TFFVRllMRU5CUVhCQ08wRkJRMGc3TzBGQlJVUXNhMEpCUVZVc1NVRkJWaXhKUVVGclFpeEpRVUZKTEVkQlFVb3NRMEZCVVN4TlFVRlNMRU5CUVd4Q08wRkJRMGdzUzBGYVJEczdRVUZqUVRzN096czdRVUZMUVN4WlFVRlJMRTlCUVZJc1IwRkJhMElzVlVGQlZTeEpRVUZXTEVWQlFXZENPMEZCUXpsQ0xGbEJRVWtzUTBGQlF5eFZRVUZWTEVsQlFWWXNRMEZCVEN4RlFVRnpRanRCUVVOc1FpeHJRa0ZCVFN4SlFVRkpMRXRCUVVvc2VVSkJRV2RETEVsQlFXaERMSGRGUVVGT08wRkJRMGc3TzBGQlJVUXNhMEpCUVZVc1NVRkJWaXhGUVVGblFpeFJRVUZvUWpzN1FVRkZRU3hsUVVGUExGVkJRVlVzU1VGQlZpeERRVUZRTzBGQlEwZ3NTMEZTUkRzN1FVRlZRVHM3T3pzN1FVRkxRU3haUVVGUkxGTkJRVklzUjBGQmIwSXNXVUZCV1R0QlFVTTFRaXhsUVVGUExGTkJRVkE3UVVGRFNDeExRVVpFTzBGQlIwZ3NRMEUxU1VRc1JVRTBTVWNzU1VGQlNTeEpRVUZLTEVOQlFWTXNSMEUxU1ZvaUxDSm1hV3hsSWpvaVoyVnVaWEpoZEdWa0xtcHpJaXdpYzI5MWNtTmxVbTl2ZENJNklpSXNJbk52ZFhKalpYTkRiMjUwWlc1MElqcGJJaWhtZFc1amRHbHZiaWdwZTJaMWJtTjBhVzl1SUhJb1pTeHVMSFFwZTJaMWJtTjBhVzl1SUc4b2FTeG1LWHRwWmlnaGJsdHBYU2w3YVdZb0lXVmJhVjBwZTNaaGNpQmpQVndpWm5WdVkzUnBiMjVjSWowOWRIbHdaVzltSUhKbGNYVnBjbVVtSm5KbGNYVnBjbVU3YVdZb0lXWW1KbU1wY21WMGRYSnVJR01vYVN3aE1DazdhV1lvZFNseVpYUjFjbTRnZFNocExDRXdLVHQyWVhJZ1lUMXVaWGNnUlhKeWIzSW9YQ0pEWVc1dWIzUWdabWx1WkNCdGIyUjFiR1VnSjF3aUsya3JYQ0luWENJcE8zUm9jbTkzSUdFdVkyOWtaVDFjSWsxUFJGVk1SVjlPVDFSZlJrOVZUa1JjSWl4aGZYWmhjaUJ3UFc1YmFWMDllMlY0Y0c5eWRITTZlMzE5TzJWYmFWMWJNRjB1WTJGc2JDaHdMbVY0Y0c5eWRITXNablZ1WTNScGIyNG9jaWw3ZG1GeUlHNDlaVnRwWFZzeFhWdHlYVHR5WlhSMWNtNGdieWh1Zkh4eUtYMHNjQ3h3TG1WNGNHOXlkSE1zY2l4bExHNHNkQ2w5Y21WMGRYSnVJRzViYVYwdVpYaHdiM0owYzMxbWIzSW9kbUZ5SUhVOVhDSm1kVzVqZEdsdmJsd2lQVDEwZVhCbGIyWWdjbVZ4ZFdseVpTWW1jbVZ4ZFdseVpTeHBQVEE3YVR4MExteGxibWQwYUR0cEt5c3BieWgwVzJsZEtUdHlaWFIxY200Z2IzMXlaWFIxY200Z2NuMHBLQ2tpTENJdktpQXRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExWeHVJR052Ykd4bFkzUnBiMjR1YW5NZ01qQXhOaTB3TmkweU1seHVJRWRoYldKcGJ5QkhiV0pJWEc0Z2FIUjBjRG92TDNkM2R5NW5ZVzFpYVc4dVpHVmNiaUJEYjNCNWNtbG5hSFFnS0dNcElESXdNVFlnUjJGdFltbHZJRWR0WWtoY2JpQlNaV3hsWVhObFpDQjFibVJsY2lCMGFHVWdSMDVWSUVkbGJtVnlZV3dnVUhWaWJHbGpJRXhwWTJWdWMyVWdLRlpsY25OcGIyNGdNaWxjYmlCYmFIUjBjRG92TDNkM2R5NW5iblV1YjNKbkwyeHBZMlZ1YzJWekwyZHdiQzB5TGpBdWFIUnRiRjFjYmlBdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFZ4dUlDb3ZYRzVjYmlobWRXNWpkR2x2YmlBb0tTQjdYRzVjYmlBZ0lDQW5kWE5sSUhOMGNtbGpkQ2M3WEc1Y2JpQWdJQ0F2S2lwY2JpQWdJQ0FnS2lCRGJHRnpjeUJEYjJ4c1pXTjBhVzl1WEc0Z0lDQWdJQ3BjYmlBZ0lDQWdLaUJVYUdseklHTnNZWE56SUdseklIVnpaV1FnZEc4Z2FHRnVaR3hsSUcxMWJIUnBjR3hsSUcxdlpIVnNaWE1nYjJZZ2RHaGxJSE5oYldVZ2RIbHdaU0FvWTI5dWRISnZiR3hsY25Nc0lHVjRkR1Z1YzJsdmJuTWdMaTR1S1M1Y2JpQWdJQ0FnS2x4dUlDQWdJQ0FxSUVCamJHRnpjeUJLVTBVdlEyOXVjM1J5ZFdOMGIzSnpMME52Ykd4bFkzUnBiMjVjYmlBZ0lDQWdLaTljYmlBZ0lDQmpiR0Z6Y3lCRGIyeHNaV04wYVc5dUlIdGNiaUFnSUNBZ0lDQWdMeW9xWEc0Z0lDQWdJQ0FnSUNBcUlFTnNZWE56SUVOdmJuTjBjblZqZEc5eVhHNGdJQ0FnSUNBZ0lDQXFYRzRnSUNBZ0lDQWdJQ0FxSUVCd1lYSmhiU0I3VTNSeWFXNW5mU0J1WVcxbElGUm9aU0JqYjJ4c1pXTjBhVzl1SUc1aGJXVWdMU0J0ZFhOMElHSmxJSFZ1YVhGMVpTNWNiaUFnSUNBZ0lDQWdJQ29nUUhCaGNtRnRJSHRUZEhKcGJtZDlJR0YwZEhKcFluVjBaU0JVYUdVZ1lYUjBjbWxpZFhSbElIUm9ZWFFnZDJsc2JDQjBjbWxuWjJWeUlHTnZiR3hsWTNScGIyNG5jeUJ0YjJSMWJHVnpMbHh1SUNBZ0lDQWdJQ0FnS2lCQWNHRnlZVzBnZTA5aWFtVmpkSDBnYm1GdFpYTndZV05sSUU5d2RHbHZibUZzTENCMGFHVWdibUZ0WlhOd1lXTmxJR2x1YzNSaGJtTmxJSGRvWlhKbElIUm9aU0JqYjJ4c1pXTjBhVzl1SUdKbGJHOXVaM011WEc0Z0lDQWdJQ0FnSUNBcUwxeHVJQ0FnSUNBZ0lDQmpiMjV6ZEhKMVkzUnZjaWh1WVcxbExDQmhkSFJ5YVdKMWRHVXNJRzVoYldWemNHRmpaU2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdkR2hwY3k1dVlXMWxJRDBnYm1GdFpUdGNiaUFnSUNBZ0lDQWdJQ0FnSUhSb2FYTXVZWFIwY21saWRYUmxJRDBnWVhSMGNtbGlkWFJsTzF4dUlDQWdJQ0FnSUNBZ0lDQWdkR2hwY3k1dVlXMWxjM0JoWTJVZ1BTQnVZVzFsYzNCaFkyVTdYRzRnSUNBZ0lDQWdJQ0FnSUNCMGFHbHpMbU5oWTJobElEMGdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJRzF2WkhWc1pYTTZJSHQ5TEZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdSaGRHRTZJSHQ5WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlPMXh1SUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0x5b3FYRzRnSUNBZ0lDQWdJQ0FxSUVSbFptbHVaU0JoSUc1bGR5QmxibWRwYm1VZ2JXOWtkV3hsTGx4dUlDQWdJQ0FnSUNBZ0tseHVJQ0FnSUNBZ0lDQWdLaUJVYUdseklHWjFibU4wYVc5dUlIZHBiR3dnWkdWbWFXNWxJR0VnYm1WM0lHMXZaSFZzWlNCcGJuUnZJSFJvWlNCbGJtZHBibVV1SUVWaFkyZ2diVzlrZFd4bElIZHBiR3dnWW1VZ2MzUnZjbVZrSUdsdUlIUm9aVnh1SUNBZ0lDQWdJQ0FnS2lCamIyeHNaV04wYVc5dUozTWdZMkZqYUdVZ2RHOGdjSEpsZG1WdWRDQjFibTVsWTJWemMyRnllU0JtYVd4bElIUnlZVzV6Wm1WeWN5NGdWR2hsSUhOaGJXVWdhR0Z3Y0dWdWN5QjNhWFJvSUhSb1pTQmtaV1poZFd4MFhHNGdJQ0FnSUNBZ0lDQXFJR052Ym1acFozVnlZWFJwYjI0Z2RHaGhkQ0JoY0hCbGJtUWdkRzhnZEdobElHMXZaSFZzWlNCa1pXWnBibWwwYVc5dUxseHVJQ0FnSUNBZ0lDQWdLbHh1SUNBZ0lDQWdJQ0FnS2lCQWNHRnlZVzBnZTFOMGNtbHVaMzBnYm1GdFpTQk9ZVzFsSUc5bUlIUm9aU0J0YjJSMWJHVWdLSE5oYldVZ1lYTWdkR2hsSUdacGJHVnVZVzFsS1M1Y2JpQWdJQ0FnSUNBZ0lDb2dRSEJoY21GdElIdEJjbkpoZVgwZ1pHVndaVzVrWlc1amFXVnpJRUZ5Y21GNUlHOW1JR3hwWW5KaGNtbGxjeUIwYUdGMElIUm9hWE1nYlc5a2RXeGxJR1JsY0dWdVpITWdiMjRnS0hkcGJHd2dZbVVnYkc5aFpHVmtJR0Z6ZVc1amFISnZibTkxYzJ4NUtTNWNiaUFnSUNBZ0lDQWdJQ29nUVhCd2JIa2diMjVzZVNCbWFXeGxibUZ0WlhNZ2QybDBhRzkxZENCbGVIUmxibk5wYjI0Z1pTNW5MaUJiWENKbGJXRnBiSE5jSWwwdVhHNGdJQ0FnSUNBZ0lDQXFJRUJ3WVhKaGJTQjdUMkpxWldOMGZTQmpiMlJsSUVOdmJuUmhhVzV6SUhSb1pTQnRiMlIxYkdVZ1kyOWtaU0FvWm5WdVkzUnBiMjRwTGx4dUlDQWdJQ0FnSUNBZ0tpOWNiaUFnSUNBZ0lDQWdiVzlrZFd4bEtHNWhiV1VzSUdSbGNHVnVaR1Z1WTJsbGN5d2dZMjlrWlNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1EyaGxZMnNnYVdZZ2NtVnhkV2x5WldRZ2RtRnNkV1Z6SUdGeVpTQmhkbUZwYkdGaWJHVWdZVzVrSUc5bUlHTnZjbkpsWTNRZ2RIbHdaUzVjYmlBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2doYm1GdFpTQjhmQ0IwZVhCbGIyWWdibUZ0WlNBaFBUMGdKM04wY21sdVp5Y2dmSHdnZEhsd1pXOW1JR052WkdVZ0lUMDlJQ2RtZFc1amRHbHZiaWNwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcWMyVXVZMjl5WlM1a1pXSjFaeTUzWVhKdUtDZFNaV2RwYzNSeVlYUnBiMjRnYjJZZ2RHaGxJRzF2WkhWc1pTQm1ZV2xzWldRc0lHUjFaU0IwYnlCaVlXUWdablZ1WTNScGIyNGdZMkZzYkNjc0lHRnlaM1Z0Wlc1MGN5azdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjbVYwZFhKdUlHWmhiSE5sTzF4dUlDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQXZMeUJEYUdWamF5QnBaaUIwYUdVZ2JXOWtkV3hsSUdseklHRnNjbVZoWkhrZ1pHVm1hVzVsWkM1Y2JpQWdJQ0FnSUNBZ0lDQWdJR2xtSUNoMGFHbHpMbU5oWTJobExtMXZaSFZzWlhOYmJtRnRaVjBwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcWMyVXVZMjl5WlM1a1pXSjFaeTUzWVhKdUtDZFNaV2RwYzNSeVlYUnBiMjRnYjJZZ2JXOWtkV3hsSUZ3aUp5QXJJRzVoYldVZ0t5QW5YQ0lnYzJ0cGNIQmxaQ3dnWW1WallYVnpaU0JwZENCaGJISmxZV1I1SUdWNGFYTjBjeTRuS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCeVpYUjFjbTRnWm1Gc2MyVTdYRzRnSUNBZ0lDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUM4dklGTjBiM0psSUhSb1pTQnRiMlIxYkdVZ2RHOGdZMkZqYUdVZ2MyOGdkR2hoZENCcGRDQmpZVzRnWW1VZ2RYTmxaQ0JzWVhSbGNpNWNiaUFnSUNBZ0lDQWdJQ0FnSUhSb2FYTXVZMkZqYUdVdWJXOWtkV3hsYzF0dVlXMWxYU0E5SUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCamIyUmxPaUJqYjJSbExGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHUmxjR1Z1WkdWdVkybGxjem9nWkdWd1pXNWtaVzVqYVdWelhHNGdJQ0FnSUNBZ0lDQWdJQ0I5TzF4dUlDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdMeW9xWEc0Z0lDQWdJQ0FnSUNBcUlFbHVhWFJwWVd4cGVtVWdUVzlrZFd4bElFTnZiR3hsWTNScGIyNWNiaUFnSUNBZ0lDQWdJQ3BjYmlBZ0lDQWdJQ0FnSUNvZ1ZHaHBjeUJ0WlhSb2IyUWdkMmxzYkNCMGNtbG5aMlZ5SUhSb1pTQndZV2RsSUcxdlpIVnNaWE1nYVc1cGRHbGhiR2w2WVhScGIyNHVJRWwwSUhkcGJHd2djMlZoY21Ob0lHRnNiRnh1SUNBZ0lDQWdJQ0FnS2lCMGFHVWdSRTlOSUdadmNpQjBhR1VnWENKa1lYUmhMV2Q0TFdWNGRHVnVjMmx2Ymx3aUxDQmNJbVJoZEdFdFozZ3RZMjl1ZEhKdmJHeGxjbHdpSUc5eVhHNGdJQ0FnSUNBZ0lDQXFJRndpWkdGMFlTMW5lQzEzYVdSblpYUmNJaUJoZEhSeWFXSjFkR1Z6SUdGdVpDQnNiMkZrSUhSb1pTQnlaV3hsZG1GdWRDQnpZM0pwY0hSeklIUm9jbTkxWjJnZ1VtVnhkV2x5WlVwVExseHVJQ0FnSUNBZ0lDQWdLbHh1SUNBZ0lDQWdJQ0FnS2lCQWNHRnlZVzBnZTJwUmRXVnllWDBnSkhCaGNtVnVkQ0JQY0hScGIyNWhiQ0FvYm5Wc2JDa3NJSEJoY21WdWRDQmxiR1Z0Wlc1MElIZHBiR3dnWW1VZ2RYTmxaQ0IwYnlCelpXRnlZMmdnWm05eUlIUm9aU0J5WlhGMWFYSmxaQ0J0YjJSMWJHVnpMbHh1SUNBZ0lDQWdJQ0FnS2x4dUlDQWdJQ0FnSUNBZ0tpQkFjbVYwZFhKdUlIdHFVWFZsY25rdVJHVm1aWEp5WldSOUlHNWhiV1Z6Y0dGalpVUmxabVZ5Y21Wa0lFUmxabVZ5Y21Wa0lHOWlhbVZqZENCMGFHRjBJR2RsZEhNZ2NISnZZMlZ6YzJWa0lHRm1kR1Z5SUhSb1pWeHVJQ0FnSUNBZ0lDQWdLaUJ0YjJSMWJHVWdhVzVwZEdsaGJHbDZZWFJwYjI0Z2FYTWdabWx1YVhOb1pXUXVYRzRnSUNBZ0lDQWdJQ0FxTDF4dUlDQWdJQ0FnSUNCcGJtbDBLQ1J3WVhKbGJuUWdQU0J1ZFd4c0tTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBdkx5QlRkRzl5WlNCMGFHVWdibUZ0WlhOd1lXTmxjeUJ5WldabGNtVnVZMlVnYjJZZ2RHaGxJR052Ykd4bFkzUnBiMjR1WEc0Z0lDQWdJQ0FnSUNBZ0lDQnBaaUFvSVhSb2FYTXVibUZ0WlhOd1lXTmxLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZEdoeWIzY2dibVYzSUVWeWNtOXlLQ2REYjJ4c1pXTjBhVzl1SUdOaGJtNXZkQ0JpWlNCcGJtbDBhV0ZzYVhwbFpDQjNhWFJvYjNWMElHbDBjeUJ3WVhKbGJuUWdibUZ0WlhOd1lXTmxJR2x1YzNSaGJtTmxMaWNwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQXZMeUJUWlhRZ2RHaGxJR1JsWm1GMWJIUWdjR0Z5Wlc1MExXOWlhbVZqZENCcFppQnViMjVsSUhkaGN5Qm5hWFpsYmk1Y2JpQWdJQ0FnSUNBZ0lDQWdJR2xtSUNna2NHRnlaVzUwSUQwOVBTQjFibVJsWm1sdVpXUWdmSHdnSkhCaGNtVnVkQ0E5UFQwZ2JuVnNiQ2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNSd1lYSmxiblFnUFNBa0tDZG9kRzFzSnlrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJR052Ym5OMElHRjBkSEpwWW5WMFpTQTlJQ2RrWVhSaExTY2dLeUIwYUdsekxtNWhiV1Z6Y0dGalpTNXVZVzFsSUNzZ0p5MG5JQ3NnZEdocGN5NWhkSFJ5YVdKMWRHVTdYRzRnSUNBZ0lDQWdJQ0FnSUNCamIyNXpkQ0J1WVcxbGMzQmhZMlZFWldabGNuSmxaQ0E5SUNRdVJHVm1aWEp5WldRb0tUdGNiaUFnSUNBZ0lDQWdJQ0FnSUdOdmJuTjBJR1JsWm1WeWNtVmtRMjlzYkdWamRHbHZiaUE5SUZ0ZE8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBa2NHRnlaVzUwWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTG1acGJIUmxjaWduV3ljZ0t5QmhkSFJ5YVdKMWRHVWdLeUFuWFNjcFhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0xtRmtaQ2drY0dGeVpXNTBMbVpwYm1Rb0oxc25JQ3NnWVhSMGNtbGlkWFJsSUNzZ0oxMG5LU2xjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F1WldGamFDZ29hVzVrWlhnc0lHVnNaVzFsYm5RcElEMCtJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1kyOXVjM1FnSkdWc1pXMWxiblFnUFNBa0tHVnNaVzFsYm5RcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JqYjI1emRDQnRiMlIxYkdWeklEMGdKR1ZzWlcxbGJuUXVZWFIwY2loaGRIUnlhV0oxZEdVcE8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNSbGJHVnRaVzUwTG5KbGJXOTJaVUYwZEhJb1lYUjBjbWxpZFhSbEtUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBa0xtVmhZMmdvYlc5a2RXeGxjeTV5WlhCc1lXTmxLQzhvWEZ4eVhGeHVmRnhjYm54Y1hISjhYRnh6WEZ4ekt5a3ZaMjBzSUNjZ0p5a3VkSEpwYlNncExuTndiR2wwS0NjZ0p5a3NJQ2hwYm1SbGVDd2dibUZ0WlNrZ1BUNGdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lnS0c1aGJXVWdQVDA5SUNjbktTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnY21WMGRYSnVJSFJ5ZFdVN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHTnZibk4wSUdSbFptVnljbVZrSUQwZ0pDNUVaV1psY25KbFpDZ3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdaR1ZtWlhKeVpXUkRiMnhzWldOMGFXOXVMbkIxYzJnb1pHVm1aWEp5WldRcE8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnFjMlV1WTI5eVpTNXRiMlIxYkdWZmJHOWhaR1Z5WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0xteHZZV1FvSkdWc1pXMWxiblFzSUc1aGJXVXNJSFJvYVhNcFhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMbVJ2Ym1Vb0tHMXZaSFZzWlNrZ1BUNGdiVzlrZFd4bExtbHVhWFFvWkdWbVpYSnlaV1FwS1Z4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDNW1ZV2xzS0NobGNuSnZjaWtnUFQ0Z2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCa1pXWmxjbkpsWkM1eVpXcGxZM1FvS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1RHOW5JSFJvWlNCbGNuSnZjaUJwYmlCMGFHVWdZMjl1YzI5c1pTQmlkWFFnWkc4Z2JtOTBJSE4wYjNBZ2RHaGxJR1Z1WjJsdVpTQmxlR1ZqZFhScGIyNHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHcHpaUzVqYjNKbExtUmxZblZuTG1WeWNtOXlLQ2REYjNWc1pDQnViM1FnYkc5aFpDQnRiMlIxYkdVNklDY2dLeUJ1WVcxbExDQmxjbkp2Y2lrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMHBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDBwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0F2THlCQmJIZGhlWE1nY21WemIyeDJaU0IwYUdVZ2JtRnRaWE53WVdObExDQmxkbVZ1SUdsbUlIUm9aWEpsSUdGeVpTQnRiMlIxYkdVZ1pYSnliM0p6TGx4dUlDQWdJQ0FnSUNBZ0lDQWdKQzUzYUdWdUxtRndjR3g1S0hWdVpHVm1hVzVsWkN3Z1pHVm1aWEp5WldSRGIyeHNaV04wYVc5dUtTNWhiSGRoZVhNb0tDa2dQVDRnYm1GdFpYTndZV05sUkdWbVpYSnlaV1F1Y21WemIyeDJaU2dwS1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnY21WMGRYSnVJR1JsWm1WeWNtVmtRMjlzYkdWamRHbHZiaTVzWlc1bmRHZ2dQeUJ1WVcxbGMzQmhZMlZFWldabGNuSmxaQzV3Y205dGFYTmxLQ2tnT2lCdVlXMWxjM0JoWTJWRVpXWmxjbkpsWkM1eVpYTnZiSFpsS0NrN1hHNGdJQ0FnSUNBZ0lIMWNiaUFnSUNCOVhHNWNiaUFnSUNCcWMyVXVZMjl1YzNSeWRXTjBiM0p6TGtOdmJHeGxZM1JwYjI0Z1BTQkRiMnhzWldOMGFXOXVPMXh1ZlNrb0tUdGNiaUlzSWk4cUlDMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdFhHNGdaR0YwWVY5aWFXNWthVzVuTG1weklESXdNVFl0TURVdE1UZGNiaUJIWVcxaWFXOGdSMjFpU0Z4dUlHaDBkSEE2THk5M2QzY3VaMkZ0WW1sdkxtUmxYRzRnUTI5d2VYSnBaMmgwSUNoaktTQXlNREUySUVkaGJXSnBieUJIYldKSVhHNGdVbVZzWldGelpXUWdkVzVrWlhJZ2RHaGxJRWRPVlNCSFpXNWxjbUZzSUZCMVlteHBZeUJNYVdObGJuTmxJQ2hXWlhKemFXOXVJRElwWEc0Z1cyaDBkSEE2THk5M2QzY3VaMjUxTG05eVp5OXNhV05sYm5ObGN5OW5jR3d0TWk0d0xtaDBiV3hkWEc0Z0xTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMxY2JpQXFMMXh1WEc0b1puVnVZM1JwYjI0Z0tDa2dlMXh1WEc0Z0lDQWdKM1Z6WlNCemRISnBZM1FuTzF4dVhHNGdJQ0FnTHlvcVhHNGdJQ0FnSUNvZ1JHRjBZU0JDYVc1a2FXNW5JRU5zWVhOelhHNGdJQ0FnSUNwY2JpQWdJQ0FnS2lCSVlXNWtiR1Z6SUhSM2J5MTNZWGtnWkdGMFlTQmlhVzVrYVc1bklIZHBkR2dnVlVrZ1pXeGxiV1Z1ZEhNdVhHNGdJQ0FnSUNwY2JpQWdJQ0FnS2lCQVkyeGhjM01nU2xORkwwTnZibk4wY25WamRHOXljeTlFWVhSaFFtbHVaR2x1WjF4dUlDQWdJQ0FxTDF4dUlDQWdJR05zWVhOeklFUmhkR0ZDYVc1a2FXNW5JSHRjYmlBZ0lDQWdJQ0FnTHlvcVhHNGdJQ0FnSUNBZ0lDQXFJRU5zWVhOeklFTnZibk4wY25WamRHOXlYRzRnSUNBZ0lDQWdJQ0FxWEc0Z0lDQWdJQ0FnSUNBcUlFQndZWEpoYlNCN1UzUnlhVzVuZlNCdVlXMWxJRlJvWlNCdVlXMWxJRzltSUhSb1pTQmlhVzVrYVc1bkxseHVJQ0FnSUNBZ0lDQWdLaUJBY0dGeVlXMGdlMDlpYW1WamRIMGdKR1ZzWlcxbGJuUWdWR0Z5WjJWMElHVnNaVzFsYm5RZ2RHOGdZbVVnWW05dVpDNWNiaUFnSUNBZ0lDQWdJQ292WEc0Z0lDQWdJQ0FnSUdOdmJuTjBjblZqZEc5eUtHNWhiV1VzSUNSbGJHVnRaVzUwS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0IwYUdsekxtNWhiV1VnUFNCdVlXMWxPMXh1SUNBZ0lDQWdJQ0FnSUNBZ2RHaHBjeTRrWld4bGJXVnVkQ0E5SUNSbGJHVnRaVzUwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdkR2hwY3k1MllXeDFaU0E5SUc1MWJHdzdYRzRnSUNBZ0lDQWdJQ0FnSUNCMGFHbHpMbWx6VFhWMFlXSnNaU0E5SUNSbGJHVnRaVzUwTG1sektDZHBibkIxZEN3Z2RHVjRkR0Z5WldFc0lITmxiR1ZqZENjcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnZEdocGN5NXBibWwwS0NrN1hHNGdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0F2S2lwY2JpQWdJQ0FnSUNBZ0lDb2dTVzVwZEdsaGJHbDZaU0IwYUdVZ1ltbHVaR2x1Wnk1Y2JpQWdJQ0FnSUNBZ0lDb3ZYRzRnSUNBZ0lDQWdJR2x1YVhRb0tTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCMGFHbHpMaVJsYkdWdFpXNTBMbTl1S0NkamFHRnVaMlVuTENBb0tTQTlQaUI3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZEdocGN5NW5aWFFvS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJSDBwTzF4dUlDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdMeW9xWEc0Z0lDQWdJQ0FnSUNBcUlFZGxkQ0JpYVc1a2FXNW5JSFpoYkhWbExseHVJQ0FnSUNBZ0lDQWdLbHh1SUNBZ0lDQWdJQ0FnS2lCQWNtVjBkWEp1Y3lCN0tuMWNiaUFnSUNBZ0lDQWdJQ292WEc0Z0lDQWdJQ0FnSUdkbGRDZ3BJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lIUm9hWE11ZG1Gc2RXVWdQU0IwYUdsekxtbHpUWFYwWVdKc1pTQS9JSFJvYVhNdUpHVnNaVzFsYm5RdWRtRnNLQ2tnT2lCMGFHbHpMaVJsYkdWdFpXNTBMbWgwYld3b0tUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ2FXWWdLSFJvYVhNdUpHVnNaVzFsYm5RdWFYTW9KenBqYUdWamEySnZlQ2NwSUh4OElIUm9hWE11SkdWc1pXMWxiblF1YVhNb0p6cHlZV1JwYnljcEtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkR2hwY3k1MllXeDFaU0E5SUhSb2FYTXVKR1ZzWlcxbGJuUXVjSEp2Y0NnblkyaGxZMnRsWkNjcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0J5WlhSMWNtNGdkR2hwY3k1MllXeDFaVHRjYmlBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lDOHFLbHh1SUNBZ0lDQWdJQ0FnS2lCVFpYUWdZbWx1WkdsdVp5QjJZV3gxWlM1Y2JpQWdJQ0FnSUNBZ0lDcGNiaUFnSUNBZ0lDQWdJQ29nUUhCaGNtRnRJSHRUZEhKcGJtZDlJSFpoYkhWbFhHNGdJQ0FnSUNBZ0lDQXFMMXh1SUNBZ0lDQWdJQ0J6WlhRb2RtRnNkV1VwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJSFJvYVhNdWRtRnNkV1VnUFNCMllXeDFaVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdhV1lnS0hSb2FYTXVhWE5OZFhSaFlteGxLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZEdocGN5NGtaV3hsYldWdWRDNTJZV3dvZG1Gc2RXVXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ2ZTQmxiSE5sSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCMGFHbHpMaVJsYkdWdFpXNTBMbWgwYld3b2RtRnNkV1VwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0I5WEc0Z0lDQWdmVnh1WEc0Z0lDQWdhbk5sTG1OdmJuTjBjblZqZEc5eWN5NUVZWFJoUW1sdVpHbHVaeUE5SUVSaGRHRkNhVzVrYVc1bk8xeHVmU2tvS1R0Y2JpSXNJaThxSUMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0WEc0Z2JXOWtkV3hsTG1weklESXdNVFl0TURVdE1UZGNiaUJIWVcxaWFXOGdSMjFpU0Z4dUlHaDBkSEE2THk5M2QzY3VaMkZ0WW1sdkxtUmxYRzRnUTI5d2VYSnBaMmgwSUNoaktTQXlNREUySUVkaGJXSnBieUJIYldKSVhHNGdVbVZzWldGelpXUWdkVzVrWlhJZ2RHaGxJRWRPVlNCSFpXNWxjbUZzSUZCMVlteHBZeUJNYVdObGJuTmxJQ2hXWlhKemFXOXVJRElwWEc0Z1cyaDBkSEE2THk5M2QzY3VaMjUxTG05eVp5OXNhV05sYm5ObGN5OW5jR3d0TWk0d0xtaDBiV3hkWEc0Z0xTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExWeHVJQ292WEc1Y2JpaG1kVzVqZEdsdmJpQW9LU0I3WEc1Y2JpQWdJQ0FuZFhObElITjBjbWxqZENjN1hHNWNiaUFnSUNBdktpcGNiaUFnSUNBZ0tpQkRiR0Z6Y3lCTmIyUjFiR1ZjYmlBZ0lDQWdLbHh1SUNBZ0lDQXFJRlJvYVhNZ1kyeGhjM01nYVhNZ2RYTmxaQ0JtYjNJZ2NtVndjbVZ6Wlc1MGFXNW5JR0VnYlc5a2RXeGxJR2x1YzNSaGJtTmxJSGRwZEdocGJpQjBhR1VnU2xORklHVmpiM041YzNSbGJTNWNiaUFnSUNBZ0tseHVJQ0FnSUNBcUlFQmpiR0Z6Y3lCS1UwVXZRMjl1YzNSeWRXTjBiM0p6TDAxdlpIVnNaVnh1SUNBZ0lDQXFMMXh1SUNBZ0lHTnNZWE56SUUxdlpIVnNaU0I3WEc0Z0lDQWdJQ0FnSUM4cUtseHVJQ0FnSUNBZ0lDQWdLaUJEYkdGemN5QkRiMjV6ZEhKMVkzUnZjbHh1SUNBZ0lDQWdJQ0FnS2x4dUlDQWdJQ0FnSUNBZ0tpQkFjR0Z5WVcwZ2UwOWlhbVZqZEgwZ0pHVnNaVzFsYm5RZ1RXOWtkV3hsSUdWc1pXMWxiblFnYzJWc1pXTjBiM0lnYjJKcVpXTjBMbHh1SUNBZ0lDQWdJQ0FnS2lCQWNHRnlZVzBnZTFOMGNtbHVaMzBnYm1GdFpTQlVhR1VnYlc5a2RXeGxJRzVoYldVZ0tHMXBaMmgwSUdOdmJuUmhhVzRnZEdobElIQmhkR2dwWEc0Z0lDQWdJQ0FnSUNBcUlFQndZWEpoYlNCN1QySnFaV04wZlNCamIyeHNaV04wYVc5dUlGUm9aU0JqYjJ4c1pXTjBhVzl1SUdsdWMzUmhibU5sSUc5bUlIUm9aU0J0YjJSMWJHVXVYRzRnSUNBZ0lDQWdJQ0FxTDF4dUlDQWdJQ0FnSUNCamIyNXpkSEoxWTNSdmNpZ2taV3hsYldWdWRDd2dibUZ0WlN3Z1kyOXNiR1ZqZEdsdmJpa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ2RHaHBjeTRrWld4bGJXVnVkQ0E5SUNSbGJHVnRaVzUwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdkR2hwY3k1dVlXMWxJRDBnYm1GdFpUdGNiaUFnSUNBZ0lDQWdJQ0FnSUhSb2FYTXVZMjlzYkdWamRHbHZiaUE5SUdOdmJHeGxZM1JwYjI0N1hHNGdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0F2S2lwY2JpQWdJQ0FnSUNBZ0lDb2dTVzVwZEdsaGJHbDZaU0IwYUdVZ2JXOWtkV3hsSUdWNFpXTjFkR2x2Ymk1Y2JpQWdJQ0FnSUNBZ0lDcGNiaUFnSUNBZ0lDQWdJQ29nVkdocGN5Qm1kVzVqZEdsdmJpQjNhV3hzSUdWNFpXTjFkR1VnZEdobElGd2lhVzVwZEZ3aUlHMWxkR2h2WkNCdlppQmxZV05vSUcxdlpIVnNaUzVjYmlBZ0lDQWdJQ0FnSUNwY2JpQWdJQ0FnSUNBZ0lDb2dRSEJoY21GdElIdFBZbXBsWTNSOUlHTnZiR3hsWTNScGIyNUVaV1psY25KbFpDQkVaV1psY25KbFpDQnZZbXBsWTNRZ2RHaGhkQ0JuWlhSeklIQnliMk5sYzNObFpDQmhablJsY2lCMGFHVWdiVzlrZFd4bFhHNGdJQ0FnSUNBZ0lDQXFJR2x1YVhScFlXeHBlbUYwYVc5dUlHbHpJR1pwYm1semFHVmtMbHh1SUNBZ0lDQWdJQ0FnS2k5Y2JpQWdJQ0FnSUNBZ2FXNXBkQ2hqYjJ4c1pXTjBhVzl1UkdWbVpYSnlaV1FwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQzh2SUZOMGIzSmxJRzF2WkhWc1pTQnBibk4wWVc1alpTQmhiR2xoY3k1Y2JpQWdJQ0FnSUNBZ0lDQWdJR052Ym5OMElHTmhZMmhsWkNBOUlIUm9hWE11WTI5c2JHVmpkR2x2Ymk1allXTm9aUzV0YjJSMWJHVnpXM1JvYVhNdWJtRnRaVjA3WEc0Z0lDQWdJQ0FnSUNBZ0lDQnNaWFFnZEdsdFpXOTFkQ0E5SUc1MWJHdzdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lIUnllU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYVdZZ0tDRmpZV05vWldRcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZEdoeWIzY2dibVYzSUVWeWNtOXlLR0JOYjJSMWJHVWdYQ0lrZTNSb2FYTXVibUZ0WlgxY0lpQmpiM1ZzWkNCdWIzUWdZbVVnWm05MWJtUWdhVzRnZEdobElHTnZiR3hsWTNScGIyNGdZMkZqYUdVdVlDazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWTI5dWMzUWdaR0YwWVNBOUlIUm9hWE11WDJkbGRFMXZaSFZzWlVSaGRHRW9LVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JqYjI1emRDQnBibk4wWVc1alpTQTlJR05oWTJobFpDNWpiMlJsTG1OaGJHd29kR2hwY3k0a1pXeGxiV1Z1ZEN3Z1pHRjBZU2s3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QlFjbTkyYVdSbElHRWdaRzl1WlNCbWRXNWpkR2x2YmlCMGFHRjBJRzVsWldSeklIUnZJR0psSUdOaGJHeGxaQ0JtY205dElIUm9aU0J0YjJSMWJHVXNJR2x1SUc5eVpHVnlJSFJ2SUdsdVptOXliU0JjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCMGFHRjBJSFJvWlNCdGIyUjFiR1VnWENKcGJtbDBYQ0lnWm5WdVkzUnBiMjRnZDJGeklHTnZiWEJzWlhSbFpDQnpkV05qWlhOelpuVnNiSGt1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWTI5dWMzUWdaRzl1WlNBOUlDZ3BJRDArSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkR2hwY3k0a1pXeGxiV1Z1ZEM1MGNtbG5aMlZ5S0NkcWMyVTZiVzlrZFd4bE9tbHVhWFJwWVd4cGVtVmtKeXdnVzN0dGIyUjFiR1U2SUhSb2FYTXVibUZ0WlgxZEtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYW5ObExtTnZjbVV1WkdWaWRXY3VhVzVtYnloZ1RXOWtkV3hsSUZ3aUpIdDBhR2x6TG01aGJXVjlYQ0lnYVc1cGRHbGhiR2w2WldRZ2MzVmpZMlZ6YzJaMWJHeDVMbUFwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmpiMnhzWldOMGFXOXVSR1ZtWlhKeVpXUXVjbVZ6YjJ4MlpTZ3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCamJHVmhjbFJwYldWdmRYUW9kR2x0Wlc5MWRDazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklFeHZZV1FnZEdobElHMXZaSFZzWlNCa1lYUmhJR0psWm05eVpTQjBhR1VnYlc5a2RXeGxJR2x6SUd4dllXUmxaQzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IwYUdsekxsOXNiMkZrVFc5a2RXeGxSR0YwWVNocGJuTjBZVzVqWlNsY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMbVJ2Ym1Vb0tDa2dQVDRnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1VtVnFaV04wSUhSb1pTQmpiMnhzWldOMGFXOXVSR1ZtWlhKeVpXUWdhV1lnZEdobElHMXZaSFZzWlNCcGMyNG5kQ0JwYm1sMGFXRnNhWHBsWkNCaFpuUmxjaUF4TUNCelpXTnZibVJ6TGx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZEdsdFpXOTFkQ0E5SUhObGRGUnBiV1Z2ZFhRb0tDa2dQVDRnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHcHpaUzVqYjNKbExtUmxZblZuTG5kaGNtNG9KMDF2WkhWc1pTQjNZWE1nYm05MElHbHVhWFJwWVd4cGVtVmtJR0ZtZEdWeUlERXdJSE5sWTI5dVpITWhJQzB0SUNjZ0t5QjBhR2x6TG01aGJXVXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdOdmJHeGxZM1JwYjI1RVpXWmxjbkpsWkM1eVpXcGxZM1FvS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgwc0lERXdNREF3S1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FuTmxMbU52Y21VdWRuVmxMbkpsWjJsemRHVnlUVzlrZFd4bEtHbHVjM1JoYm1ObEtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbHVjM1JoYm1ObExtbHVhWFFvWkc5dVpTazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgwcFhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzVtWVdsc0tDaGxjbkp2Y2lrZ1BUNGdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdZMjlzYkdWamRHbHZia1JsWm1WeWNtVmtMbkpsYW1WamRDZ3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhbk5sTG1OdmNtVXVaR1ZpZFdjdVpYSnliM0lvSjBOdmRXeGtJRzV2ZENCc2IyRmtJRzF2WkhWc1pWeGNKM01nYldWMFlTQmtZWFJoTGljc0lHVnljbTl5S1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlJR05oZEdOb0lDaGxlR05sY0hScGIyNHBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JqYjJ4c1pXTjBhVzl1UkdWbVpYSnlaV1F1Y21WcVpXTjBLQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYW5ObExtTnZjbVV1WkdWaWRXY3VaWEp5YjNJb1lFTmhibTV2ZENCcGJtbDBhV0ZzYVhwbElHMXZaSFZzWlNCY0lpUjdkR2hwY3k1dVlXMWxmVndpTG1Bc0lHVjRZMlZ3ZEdsdmJpazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdKQ2gzYVc1a2IzY3BMblJ5YVdkblpYSW9KMlZ5Y205eUp5d2dXMlY0WTJWd2RHbHZibDBwT3lBdkx5QkpibVp2Y20wZ2RHaGxJR1Z1WjJsdVpTQmhZbTkxZENCMGFHVWdaWGhqWlhCMGFXOXVMbHh1SUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJQ0FnSUNCeVpYUjFjbTRnWTI5c2JHVmpkR2x2YmtSbFptVnljbVZrTG5CeWIyMXBjMlVvS1R0Y2JpQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJQzhxS2x4dUlDQWdJQ0FnSUNBZ0tpQlFZWEp6WlNCMGFHVWdiVzlrZFd4bElHUmhkR0VnWVhSMGNtbGlkWFJsY3k1Y2JpQWdJQ0FnSUNBZ0lDcGNiaUFnSUNBZ0lDQWdJQ29nUUhKbGRIVnliaUI3VDJKcVpXTjBmU0JTWlhSMWNtNXpJR0Z1SUc5aWFtVmpkQ0IwYUdGMElHTnZiblJoYVc1eklIUm9aU0JrWVhSaElHOW1JSFJvWlNCdGIyUjFiR1V1WEc0Z0lDQWdJQ0FnSUNBcVhHNGdJQ0FnSUNBZ0lDQXFJRUJ3Y21sMllYUmxYRzRnSUNBZ0lDQWdJQ0FxTDF4dUlDQWdJQ0FnSUNCZloyVjBUVzlrZFd4bFJHRjBZU2dwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJR052Ym5OMElHUmhkR0VnUFNCN2ZUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0pDNWxZV05vS0hSb2FYTXVKR1ZzWlcxbGJuUXVaR0YwWVNncExDQW9ibUZ0WlN3Z2RtRnNkV1VwSUQwK0lIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaUFvYm1GdFpTNXBibVJsZUU5bUtIUm9hWE11Ym1GdFpTa2dQVDA5SURBZ2ZId2dibUZ0WlM1cGJtUmxlRTltS0hSb2FYTXVibUZ0WlM1MGIweHZkMlZ5UTJGelpTZ3BLU0E5UFQwZ01Da2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCc1pYUWdhMlY1SUQwZ2JtRnRaUzV6ZFdKemRISW9kR2hwY3k1dVlXMWxMbXhsYm1kMGFDazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUd0bGVTQTlJR3RsZVM1emRXSnpkSElvTUN3Z01Ta3VkRzlNYjNkbGNrTmhjMlVvS1NBcklHdGxlUzV6ZFdKemRISW9NU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHUmhkR0ZiYTJWNVhTQTlJSFpoYkhWbE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCU1pXMXZkbVVnWkdGMFlTQmhkSFJ5YVdKMWRHVWdabkp2YlNCbGJHVnRaVzUwSUNoellXNXBkR2x6WlNCallXMWxiQ0JqWVhObElHWnBjbk4wS1M1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdZMjl1YzNRZ2MyRnVhWFJwYzJWa1MyVjVJRDBnYTJWNUxuSmxjR3hoWTJVb0x5aGJZUzE2WFNrb1cwRXRXbDBwTDJjc0lDY2tNUzBrTWljcExuUnZURzkzWlhKRFlYTmxLQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIUm9hWE11SkdWc1pXMWxiblF1Y21WdGIzWmxRWFIwY2loZ1pHRjBZUzBrZTNSb2FYTXVibUZ0WlgwdEpIdHpZVzVwZEdselpXUkxaWGw5WUNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQWdJQ0FnZlNrN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUhKbGRIVnliaUJrWVhSaE8xeHVJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnTHlvcVhHNGdJQ0FnSUNBZ0lDQXFJRTF2WkhWc1pYTWdjbVYwZFhKdUlHOWlhbVZqZEhNZ2QyaHBZMmdnYldsbmFIUWdZMjl1ZEdGcGJpQnlaWEYxYVhKbGJXVnVkSE11WEc0Z0lDQWdJQ0FnSUNBcVhHNGdJQ0FnSUNBZ0lDQXFJRUJ3WVhKaGJTQjdUMkpxWldOMGZTQnBibk4wWVc1alpTQk5iMlIxYkdVZ2FXNXpkR0Z1WTJVZ2IySnFaV04wTGx4dUlDQWdJQ0FnSUNBZ0tseHVJQ0FnSUNBZ0lDQWdLaUJBY21WMGRYSnVJSHRQWW1wbFkzUjlJRkpsZEhWeWJuTWdZU0J3Y205dGFYTmxJRzlpYW1WamRDQjBhR0YwSUhkcGJHd2dZbVVnY21WemIyeDJaV1FnZDJobGJpQjBhR1VnWkdGMFlTQmhjbVVnWm1WMFkyaGxaQzVjYmlBZ0lDQWdJQ0FnSUNwY2JpQWdJQ0FnSUNBZ0lDb2dRSEJ5YVhaaGRHVmNiaUFnSUNBZ0lDQWdJQ292WEc0Z0lDQWdJQ0FnSUY5c2IyRmtUVzlrZFd4bFJHRjBZU2hwYm5OMFlXNWpaU2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdZMjl1YzNRZ1pHVm1aWEp5WldRZ1BTQWtMa1JsWm1WeWNtVmtLQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQmpiMjV6ZENCa1pXWmxjbkpsWkVOdmJHeGxZM1JwYjI0Z1BTQmJYVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdkSEo1SUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9hVzV6ZEdGdVkyVXViVzlrWld3cElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSkM1bFlXTm9LR2x1YzNSaGJtTmxMbTF2WkdWc0xDQm1kVzVqZEdsdmJpQW9hVzVrWlhnc0lIVnliQ2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWTI5dWMzUWdiVzlrWld4RVpXWmxjbkpsWkNBOUlDUXVSR1ZtWlhKeVpXUW9LVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1JsWm1WeWNtVmtRMjlzYkdWamRHbHZiaTV3ZFhOb0tHMXZaR1ZzUkdWbVpYSnlaV1FwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSkM1blpYUktVMDlPS0hWeWJDbGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F1Wkc5dVpTZ29jbVZ6Y0c5dWMyVXBJRDArSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYVc1emRHRnVZMlV1Ylc5a1pXeGJhVzVrWlhoZElEMGdjbVZ6Y0c5dWMyVTdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHMXZaR1ZzUkdWbVpYSnlaV1F1Y21WemIyeDJaU2h5WlhOd2IyNXpaU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZTbGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F1Wm1GcGJDZ29aWEp5YjNJcElEMCtJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdiVzlrWld4RVpXWmxjbkpsWkM1eVpXcGxZM1FvWlhKeWIzSXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgwcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5S1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaUFvYVc1emRHRnVZMlV1ZG1sbGR5a2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBa0xtVmhZMmdvYVc1emRHRnVZMlV1ZG1sbGR5d2dablZ1WTNScGIyNGdLR2x1WkdWNExDQjFjbXdwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdOdmJuTjBJSFpwWlhkRVpXWmxjbkpsWkNBOUlDUXVSR1ZtWlhKeVpXUW9LVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1JsWm1WeWNtVmtRMjlzYkdWamRHbHZiaTV3ZFhOb0tIWnBaWGRFWldabGNuSmxaQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FrTG1kbGRDaDFjbXdwWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0xtUnZibVVvS0hKbGMzQnZibk5sS1NBOVBpQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbHVjM1JoYm1ObExuWnBaWGRiYVc1a1pYaGRJRDBnY21WemNHOXVjMlU3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFpwWlhkRVpXWmxjbkpsWkM1eVpYTnZiSFpsS0hKbGMzQnZibk5sS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlLVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM1bVlXbHNLQ2hsY25KdmNpa2dQVDRnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IyYVdWM1JHVm1aWEp5WldRdWNtVnFaV04wS0dWeWNtOXlLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOUtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lnS0dsdWMzUmhibU5sTG1KcGJtUnBibWR6S1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1p2Y2lBb2JHVjBJRzVoYldVZ2FXNGdhVzV6ZEdGdVkyVXVZbWx1WkdsdVozTXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR052Ym5OMElDUmxiR1Z0Wlc1MElEMGdhVzV6ZEdGdVkyVXVZbWx1WkdsdVozTmJibUZ0WlYwN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcGJuTjBZVzVqWlM1aWFXNWthVzVuYzF0dVlXMWxYU0E5SUc1bGR5QnFjMlV1WTI5dWMzUnlkV04wYjNKekxrUmhkR0ZDYVc1a2FXNW5LRzVoYldVc0lDUmxiR1Z0Wlc1MEtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDUXVkMmhsYmk1aGNIQnNlU2gxYm1SbFptbHVaV1FzSUdSbFptVnljbVZrUTI5c2JHVmpkR2x2YmlsY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMbVJ2Ym1Vb1pHVm1aWEp5WldRdWNtVnpiMngyWlNsY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMbVpoYVd3b0tHVnljbTl5S1NBOVBpQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmtaV1psY25KbFpDNXlaV3BsWTNRb2JtVjNJRVZ5Y205eUtHQkRZVzV1YjNRZ2JHOWhaQ0JrWVhSaElHWnZjaUJ0YjJSMWJHVWdYQ0lrZTJsdWMzUmhibU5sTG01aGJXVjlYQ0l1WUN3Z1pYSnliM0lwS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlJR05oZEdOb0lDaGxlR05sY0hScGIyNHBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JrWldabGNuSmxaQzV5WldwbFkzUW9aWGhqWlhCMGFXOXVLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JxYzJVdVkyOXlaUzVrWldKMVp5NWxjbkp2Y2loZ1EyRnVibTkwSUhCeVpXeHZZV1FnYlc5a2RXeGxJR1JoZEdFZ1ptOXlJRndpSkh0MGFHbHpMbTVoYldWOVhDSXVZQ3dnWlhoalpYQjBhVzl1S1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBa0tIZHBibVJ2ZHlrdWRISnBaMmRsY2lnblpYSnliM0luTENCYlpYaGpaWEIwYVc5dVhTazdJQzh2SUVsdVptOXliU0IwYUdVZ1pXNW5hVzVsSUdGaWIzVjBJSFJvWlNCbGVHTmxjSFJwYjI0dVhHNGdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJSEpsZEhWeWJpQmtaV1psY25KbFpDNXdjbTl0YVhObEtDazdYRzRnSUNBZ0lDQWdJSDFjYmlBZ0lDQjlYRzVjYmlBZ0lDQnFjMlV1WTI5dWMzUnlkV04wYjNKekxrMXZaSFZzWlNBOUlFMXZaSFZzWlR0Y2JuMHBLQ2s3WEc0aUxDSXZLaUF0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMVnh1SUc1aGJXVnpjR0ZqWlM1cWN5QXlNREUyTFRBMUxURTNYRzRnUjJGdFltbHZJRWR0WWtoY2JpQm9kSFJ3T2k4dmQzZDNMbWRoYldKcGJ5NWtaVnh1SUVOdmNIbHlhV2RvZENBb1l5a2dNakF4TmlCSFlXMWlhVzhnUjIxaVNGeHVJRkpsYkdWaGMyVmtJSFZ1WkdWeUlIUm9aU0JIVGxVZ1IyVnVaWEpoYkNCUWRXSnNhV01nVEdsalpXNXpaU0FvVm1WeWMybHZiaUF5S1Z4dUlGdG9kSFJ3T2k4dmQzZDNMbWR1ZFM1dmNtY3ZiR2xqWlc1elpYTXZaM0JzTFRJdU1DNW9kRzFzWFZ4dUlDMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdFhHNGdLaTljYmx4dUtHWjFibU4wYVc5dUlDZ3BJSHRjYmx4dUlDQWdJQ2QxYzJVZ2MzUnlhV04wSnp0Y2JseHVJQ0FnSUM4cUtseHVJQ0FnSUNBcUlFTnNZWE56SUU1aGJXVnpjR0ZqWlZ4dUlDQWdJQ0FxWEc0Z0lDQWdJQ29nVkdocGN5QmpiR0Z6Y3lCcGN5QjFjMlZrSUhSdklHaGhibVJzWlNCdGRXeDBhWEJzWlNCamIyeHNaV04wYVc5dWN5QnZaaUJ0YjJSMWJHVnpMaUJGZG1WeWVTQnVZVzFsYzNCaFkyVWdhR0Z6SUdsMGN5QnZkMjRnYzI5MWNtTmxJRlZTVEZ4dUlDQWdJQ0FxSUdadmNpQnNiMkZrYVc1bklIUm9aU0JrWVhSaExpQlVhR0YwSUcxbFlXNXpJSFJvWVhRZ1NsTkZJR05oYmlCc2IyRmtJRzF2WkhWc1pYTWdabkp2YlNCdGRXeDBhWEJzWlNCd2JHRmpaWE1nWVhRZ2RHaGxJSE5oYldVZ2RHbHRaUzVjYmlBZ0lDQWdLbHh1SUNBZ0lDQXFJRUJqYkdGemN5QktVMFV2UTI5dWMzUnlkV04wYjNKekwwNWhiV1Z6Y0dGalpWeHVJQ0FnSUNBcUwxeHVJQ0FnSUdOc1lYTnpJRTVoYldWemNHRmpaU0I3WEc0Z0lDQWdJQ0FnSUM4cUtseHVJQ0FnSUNBZ0lDQWdLaUJEYkdGemN5QkRiMjV6ZEhKMVkzUnZjbHh1SUNBZ0lDQWdJQ0FnS2x4dUlDQWdJQ0FnSUNBZ0tpQkFjR0Z5WVcwZ2UxTjBjbWx1WjMwZ2JtRnRaU0JVYUdVZ2JtRnRaWE53WVdObElHNWhiV1VnYlhWemRDQmlaU0IxYm1seGRXVWdkMmwwYUdsdUlIUm9aU0JoY0hBdVhHNGdJQ0FnSUNBZ0lDQXFJRUJ3WVhKaGJTQjdVM1J5YVc1bmZTQnpiM1Z5WTJVZ1EyOXRjR3hsZEdVZ1ZWSk1JSFJ2SUhSb1pTQnVZVzFsYzNCaFkyVWdiVzlrZFd4bGN5QmthWEpsWTNSdmNua2dLSGRwZEdodmRYUWdkSEpoYVd4cGJtY2djMnhoYzJncExseHVJQ0FnSUNBZ0lDQWdLaUJBY0dGeVlXMGdlMEZ5Y21GNWZTQmpiMnhzWldOMGFXOXVjeUJEYjI1MFlXbHVjeUJqYjJ4c1pXTjBhVzl1SUdsdWMzUmhibU5sY3lCMGJ5QmlaU0JwYm1Oc2RXUmxaQ0JwYmlCMGFHVWdibUZ0WlhOd1lXTmxMbHh1SUNBZ0lDQWdJQ0FnS2k5Y2JpQWdJQ0FnSUNBZ1kyOXVjM1J5ZFdOMGIzSW9ibUZ0WlN3Z2MyOTFjbU5sTENCamIyeHNaV04wYVc5dWN5a2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ2RHaHBjeTV1WVcxbElEMGdibUZ0WlR0Y2JpQWdJQ0FnSUNBZ0lDQWdJSFJvYVhNdWMyOTFjbU5sSUQwZ2MyOTFjbU5sTzF4dUlDQWdJQ0FnSUNBZ0lDQWdkR2hwY3k1amIyeHNaV04wYVc5dWN5QTlJR052Ykd4bFkzUnBiMjV6T3lBdkx5QmpiMjUwWVdsdWN5QjBhR1VnWkdWbVlYVnNkQ0JwYm5OMFlXNWpaWE1nSUNCY2RGeDBYRzRnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNBdktpcGNiaUFnSUNBZ0lDQWdJQ29nU1c1cGRHbGhiR2w2WlNCMGFHVWdibUZ0WlhOd1lXTmxJR052Ykd4bFkzUnBiMjV6TGx4dUlDQWdJQ0FnSUNBZ0tseHVJQ0FnSUNBZ0lDQWdLaUJVYUdseklHMWxkR2h2WkNCM2FXeHNJR055WldGMFpTQnVaWGNnWTI5c2JHVmpkR2x2YmlCcGJuTjBZVzVqWlhNZ1ltRnpaV1FnYVc0Z2RHaGxJRzl5YVdkcGJtRnNJRzl1WlhNdVhHNGdJQ0FnSUNBZ0lDQXFYRzRnSUNBZ0lDQWdJQ0FxSUVCeVpYUjFjbTRnZTJwUmRXVnllUzVRY205dGFYTmxmU0JTWlhSMWNtNXpJR0VnY0hKdmJXbHpaU0IwYUdGMElIZHBiR3dnWW1VZ2NtVnpiMngyWldRZ2IyNWpaU0JsZG1WeWVTQnVZVzFsYzNCaFkyVWdZMjlzYkdWamRHbHZibHh1SUNBZ0lDQWdJQ0FnS2lCcGN5QnlaWE52YkhabFpDNWNiaUFnSUNBZ0lDQWdJQ292WEc0Z0lDQWdJQ0FnSUdsdWFYUW9LU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQmpiMjV6ZENCa1pXWmxjbkpsWkVOdmJHeGxZM1JwYjI0Z1BTQmJYVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdabTl5SUNoc1pYUWdZMjlzYkdWamRHbHZiaUJ2WmlCMGFHbHpMbU52Ykd4bFkzUnBiMjV6S1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RHaHBjMXRqYjJ4c1pXTjBhVzl1TG01aGJXVmRJRDBnYm1WM0lHcHpaUzVqYjI1emRISjFZM1J2Y25NdVEyOXNiR1ZqZEdsdmJpaGpiMnhzWldOMGFXOXVMbTVoYldVc0lHTnZiR3hsWTNScGIyNHVZWFIwY21saWRYUmxMQ0IwYUdsektUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmpiMjV6ZENCa1pXWmxjbkpsWkNBOUlIUm9hWE5iWTI5c2JHVmpkR2x2Ymk1dVlXMWxYUzVwYm1sMEtDazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdaR1ZtWlhKeVpXUkRiMnhzWldOMGFXOXVMbkIxYzJnb1pHVm1aWEp5WldRcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0J5WlhSMWNtNGdaR1ZtWlhKeVpXUkRiMnhzWldOMGFXOXVMbXhsYm1kMGFDQS9JQ1F1ZDJobGJpNWhjSEJzZVNoMWJtUmxabWx1WldRc0lHUmxabVZ5Y21Wa1EyOXNiR1ZqZEdsdmJpa3VjSEp2YldselpTZ3BJRG9nSkM1RVpXWmxjbkpsWkNncExuSmxjMjlzZG1Vb0tUdGNiaUFnSUNBZ0lDQWdmVnh1SUNBZ0lIMWNibHh1SUNBZ0lHcHpaUzVqYjI1emRISjFZM1J2Y25NdVRtRnRaWE53WVdObElEMGdUbUZ0WlhOd1lXTmxPMXh1ZlNrb0tUdGNiaUlzSWk4cUlDMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdFhHNGdZV0p2ZFhRdWFuTWdNakF4Tmkwd09TMHdPRnh1SUVkaGJXSnBieUJIYldKSVhHNGdhSFIwY0RvdkwzZDNkeTVuWVcxaWFXOHVaR1ZjYmlCRGIzQjVjbWxuYUhRZ0tHTXBJREl3TVRZZ1IyRnRZbWx2SUVkdFlraGNiaUJTWld4bFlYTmxaQ0IxYm1SbGNpQjBhR1VnUjA1VklFZGxibVZ5WVd3Z1VIVmliR2xqSUV4cFkyVnVjMlVnS0ZabGNuTnBiMjRnTWlsY2JpQmJhSFIwY0RvdkwzZDNkeTVuYm5VdWIzSm5MMnhwWTJWdWMyVnpMMmR3YkMweUxqQXVhSFJ0YkYxY2JpQXRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExWeHVJQ292WEc1Y2JpOHFLbHh1SUNvZ1NsTkZJRWx1Wm05eWJXRjBhVzl1SUUxdlpIVnNaVnh1SUNwY2JpQXFJRVY0WldOMWRHVWdkR2hsSUdCcWMyVXVZV0p2ZFhRb0tXQWdZMjl0YldGdVpDQmhibVFnZVc5MUlIZHBiR3dnWjJWMElHRWdibVYzSUd4dlp5QmxiblJ5ZVNCcGJpQjBhR1ZjYmlBcUlHTnZibk52YkdVZ2QybDBhQ0JwYm1adklHRmliM1YwSUhSb1pTQmxibWRwYm1VdUlGUm9aU0JjSW1GaWIzVjBYQ0lnYldWMGFHOWtJR2x6SUc5dWJIa2dZWFpoYVd4aFlteGxJR2x1WEc0Z0tpQjBhR1VnWENKa1pYWmxiRzl3YldWdWRGd2lJR1Z1ZG1seWIyNXRaVzUwSUc5bUlIUm9aU0JsYm1kcGJtVXVYRzRnS2x4dUlDb2dRRzF2WkhWc1pTQktVMFV2UTI5eVpTOWhZbTkxZEZ4dUlDb3ZYRzVrYjJOMWJXVnVkQzVoWkdSRmRtVnVkRXhwYzNSbGJtVnlLQ2RFVDAxRGIyNTBaVzUwVEc5aFpHVmtKeXdnWm5WdVkzUnBiMjRnS0NrZ2UxeHVYRzRnSUNBZ0ozVnpaU0J6ZEhKcFkzUW5PMXh1WEc0Z0lDQWdhV1lnS0dwelpTNWpiM0psTG1OdmJtWnBaeTVuWlhRb0oyVnVkbWx5YjI1dFpXNTBKeWtnUFQwOUlDZHdjbTlrZFdOMGFXOXVKeWtnZTF4dUlDQWdJQ0FnSUNCeVpYUjFjbTQ3WEc0Z0lDQWdmVnh1WEc0Z0lDQWdhbk5sTG1GaWIzVjBJRDBnWm5WdVkzUnBiMjRnS0NrZ2UxeHVJQ0FnSUNBZ0lDQmpiMjV6ZENCcGJtWnZJRDBnWUZ4dVhIUmNkRngwU2xNZ1JVNUhTVTVGSUhZa2UycHpaUzVqYjNKbExtTnZibVpwWnk1blpYUW9KM1psY25OcGIyNG5LWDBnd3FrZ1IwRk5Ra2xQSUVkTlFraGNibHgwWEhSY2RDMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMWNibHgwWEhSY2RGUm9aU0JLVXlCRmJtZHBibVVnWlc1aFlteGxjeUJrWlhabGJHOXdaWEp6SUhSdklHeHZZV1FnWVhWMGIyMWhkR2xqWVd4c2VTQnpiV0ZzYkNCd2FXVmpaWE1nYjJZZ2FtRjJZWE5qY21sd2RDQmpiMlJsSUdKNVhHNWNkRngwWEhSd2JHRmphVzVuSUhOd1pXTnBabWxqSUdSaGRHRWdZWFIwY21saWRYUmxjeUIwYnlCMGFHVWdTRlJOVENCdFlYSnJkWEFnYjJZZ1lTQndZV2RsTGlCSmRDQjNZWE1nWW5WcGJIUWdkMmwwYUNCdGIyUjFiR0Z5YVhSNVhHNWNkRngwWEhScGJpQnRhVzVrSUhOdklIUm9ZWFFnYlc5a2RXeGxjeUJqWVc0Z1ltVWdjbVYxYzJWa0lHbHVkRzhnYlhWc2RHbHdiR1VnY0d4aFkyVnpJSGRwZEdodmRYUWdaWGgwY21FZ1pXWm1iM0owTGlCVWFHVWdaVzVuYVc1bFhHNWNkRngwWEhSamIyNTBZV2x1Y3lCdVlXMWxjM0JoWTJWeklIZG9hV05vSUdOdmJuUmhhVzRnWTI5c2JHVmpkR2x2Ym5NZ2IyWWdiVzlrZFd4bGN5d2daV0ZqYUNCdmJtVWdiMllnZDJodmJTQnpaWEoyWlNCaElHUnBabVpsY21WdWRGeHVYSFJjZEZ4MFoyVnVaWEpwWXlCd2RYSndiM05sTGx4dVhIUmNkRngwVm1semFYUWdhSFIwY0RvdkwyUmxkbVZzYjNCbGNuTXVaMkZ0WW1sdkxtUmxJR1p2Y2lCamIyMXdiR1YwWlNCeVpXWmxjbVZ1WTJVZ2IyWWdkR2hsSUVwVElFVnVaMmx1WlM1Y2JseDBYSFJjZEZ4dVhIUmNkRngwUmtGTVRFSkJRMHNnU1U1R1QxSk5RVlJKVDA1Y2JseDBYSFJjZEMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMxY2JseDBYSFJjZEZOcGJtTmxJSFJvWlNCbGJtZHBibVVnWTI5a1pTQmlaV052YldWeklHSnBaMmRsY2lCMGFHVnlaU0JoY21VZ2MyVmpkR2x2Ym5NZ2RHaGhkQ0J1WldWa0lIUnZJR0psSUhKbFptRmpkRzl5WldRZ2FXNGdiM0prWlhKY2JseDBYSFJjZEhSdklHSmxZMjl0WlNCdGIzSmxJR1pzWlhocFlteGxMaUJKYmlCdGIzTjBJR05oYzJWeklHRWdkMkZ5Ym1sdVp5QnNiMmNnZDJsc2JDQmlaU0JrYVhOd2JHRjVaV1FnWVhRZ2RHaGxJR0p5YjNkelpYSmNYQ2R6SUdOdmJuTnZiR1ZjYmx4MFhIUmNkSGRvWlc1bGRtVnlJSFJvWlhKbElHbHpJR0VnZFhObElHOW1JR0VnWkdWd2NtVmpZWFJsWkNCbWRXNWpkR2x2Ymk0Z1FtVnNiM2NnZEdobGNtVWdhWE1nWVNCeGRXbGpheUJzYVhOMElHOW1JR1poYkd4aVlXTnJJSE4xY0hCdmNuUmNibHgwWEhSY2RIUm9ZWFFnZDJsc2JDQmlaU0J5WlcxdmRtVmtJR2x1SUhSb1pTQm1kWFIxY21VZ2RtVnljMmx2Ym5NZ2IyWWdkR2hsSUdWdVoybHVaUzVjYmx4MFhIUmNkRnh1WEhSY2RGeDBNUzRnVkdobElHMWhhVzRnWlc1bmFXNWxJRzlpYW1WamRDQjNZWE1nY21WdVlXMWxaQ0JtY205dElGd2laM2hjSWlCMGJ5QmNJbXB6WlZ3aUlIZG9hV05vSUhOMFlXNWtjeUJtYjNJZ2RHaGxJRXBoZG1GVFkzSnBjSFFnUlc1bmFXNWxMbHh1WEhSY2RGeDBNaTRnVkdobElGd2laM2d1YkdsaVhDSWdiMkpxWldOMElHbHpJSEpsYlc5MlpXUWdZV1owWlhJZ1lTQnNiMjVuSUdSbGNISmxZMkYwYVc5dUlIQmxjbWx2WkM0Z1dXOTFJSE5vYjNWc1pDQjFjR1JoZEdVZ2RHaGxJRzF2WkhWc1pYTWdYRzVjZEZ4MFhIUWdJQ0IwYUdGMElHTnZiblJoYVc1bFpDQmpZV3hzY3lCMGJ5QjBhR1VnWm5WdVkzUnBiMjV6SUc5bUlIUm9hWE1nYjJKcVpXTjBMbHh1WEhSY2RGeDBNeTRnVkdobElHZDRManhqYjJ4c1pXTjBhVzl1TFc1aGJXVStMbkpsWjJsemRHVnlJR1oxYm1OMGFXOXVJR2x6SUdSbGNISmxZMkYwWldRZ1lua2dkakV1TWl3Z2RYTmxJSFJvWlNCY2JseDBYSFJjZENBZ0lEeHVZVzFsYzNCaFkyVStManhqYjJ4c1pXTjBhVzl1UGk1dGIyUjFiR1VvS1NCcGJuTjBaV0ZrTGx4dVhIUmNkR0E3WEc1Y2JpQWdJQ0FnSUNBZ2FuTmxMbU52Y21VdVpHVmlkV2N1YVc1bWJ5aHBibVp2S1R0Y2JpQWdJQ0I5TzF4dWZTazdYRzRpTENJdktpQXRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExWeHVJR052Ym1acFp5NXFjeUF5TURFNExUQTVMVEV5WEc0Z1IyRnRZbWx2SUVkdFlraGNiaUJvZEhSd09pOHZkM2QzTG1kaGJXSnBieTVrWlZ4dUlFTnZjSGx5YVdkb2RDQW9ZeWtnTWpBeE55QkhZVzFpYVc4Z1IyMWlTRnh1SUZKbGJHVmhjMlZrSUhWdVpHVnlJSFJvWlNCSFRsVWdSMlZ1WlhKaGJDQlFkV0pzYVdNZ1RHbGpaVzV6WlNBb1ZtVnljMmx2YmlBeUtWeHVJRnRvZEhSd09pOHZkM2QzTG1kdWRTNXZjbWN2YkdsalpXNXpaWE12WjNCc0xUSXVNQzVvZEcxc1hWeHVJQzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRYRzRnS2k5Y2JseHVhbk5sTG1OdmNtVXVZMjl1Wm1sbklEMGdhbk5sTG1OdmNtVXVZMjl1Wm1sbklIeDhJSHQ5TzF4dVhHNHZLaXBjYmlBcUlFcFRSU0JEYjI1bWFXZDFjbUYwYVc5dUlFMXZaSFZzWlZ4dUlDcGNiaUFxSUU5dVkyVWdkR2hsSUdOdmJtWnBaeUJ2WW1wbFkzUWdhWE1nYVc1cGRHbGhiR2w2WldRZ2VXOTFJR05oYm01dmRDQmphR0Z1WjJVZ2FYUnpJSFpoYkhWbGN5NGdWR2hwY3lCcGN5QmtiMjVsSUdsdUlHOXlaR1Z5SUhSdlhHNGdLaUJ3Y21WMlpXNTBJSFZ1Y0d4bFlYTmhiblFnYzJsMGRXRjBhVzl1Y3lCM2FHVnlaU0J2Ym1VZ1kyOWtaU0J6WldOMGFXOXVJR05vWVc1blpYTWdZU0JqYjNKbElHTnZibVpwWnlCelpYUjBhVzVuSUhSb1lYUWdZV1ptWldOMGMxeHVJQ29nWVc1dmRHaGxjaUJqYjJSbElITmxZM1JwYjI0Z2FXNGdZU0IzWVhrZ2RHaGhkQ0JwY3lCb1lYSmtJSFJ2SUdScGMyTnZkbVZ5TGx4dUlDcGNiaUFxSUdCZ1lHcGhkbUZ6WTNKcGNIUmNiaUFxSUdOdmJuTjBJR0Z3Y0ZWeWJDQTlJR3B6WlM1amIzSmxMbU52Ym1acFp5NW5aWFFvSjJGd2NGVnliQ2NwTzF4dUlDb2dZR0JnWEc0Z0tseHVJQ29nUUcxdlpIVnNaU0JLVTBVdlEyOXlaUzlqYjI1bWFXZGNiaUFxTDF4dUtHWjFibU4wYVc5dUlDaGxlSEJ2Y25SektTQjdYRzVjYmlBZ0lDQW5kWE5sSUhOMGNtbGpkQ2M3WEc1Y2JpQWdJQ0F2THlBdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMxY2JpQWdJQ0F2THlCRFQwNUdTVWRWVWtGVVNVOU9JRlpCVEZWRlUxeHVJQ0FnSUM4dklDMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExWeHVYRzRnSUNBZ1kyOXVjM1FnWTI5dVptbG5JRDBnZTF4dUlDQWdJQ0FnSUNBdktpcGNiaUFnSUNBZ0lDQWdJQ29nUlc1bmFXNWxJRlpsY25OcGIyNWNiaUFnSUNBZ0lDQWdJQ3BjYmlBZ0lDQWdJQ0FnSUNvZ1FIUjVjR1VnZTFOMGNtbHVaMzFjYmlBZ0lDQWdJQ0FnSUNvdlhHNGdJQ0FnSUNBZ0lIWmxjbk5wYjI0NklDY3hMalluTEZ4dVhHNGdJQ0FnSUNBZ0lDOHFLbHh1SUNBZ0lDQWdJQ0FnS2lCQmNIQWdWVkpNWEc0Z0lDQWdJQ0FnSUNBcVhHNGdJQ0FnSUNBZ0lDQXFJR1V1Wnk0Z0oyaDBkSEE2THk5aGNIQXVZMjl0SjF4dUlDQWdJQ0FnSUNBZ0tseHVJQ0FnSUNBZ0lDQWdLaUJBZEhsd1pTQjdVM1J5YVc1bmZWeHVJQ0FnSUNBZ0lDQWdLaTljYmlBZ0lDQWdJQ0FnWVhCd1ZYSnNPaUJ1ZFd4c0xGeHVYRzRnSUNBZ0lDQWdJQzhxS2x4dUlDQWdJQ0FnSUNBZ0tpQlRhRzl3SUZWU1RGeHVJQ0FnSUNBZ0lDQWdLbHh1SUNBZ0lDQWdJQ0FnS2lCbExtY3VJQ2RvZEhSd09pOHZaWGhoYlhCc1pTNXZjbWNuWEc0Z0lDQWdJQ0FnSUNBcVhHNGdJQ0FnSUNBZ0lDQXFJRUJrWlhCeVpXTmhkR1ZrSUZOcGJtTmxJSFl4TGpRc0lIVnpaU0JoY0hCVmNtd2dhVzV6ZEdWaFpDNWNiaUFnSUNBZ0lDQWdJQ3BjYmlBZ0lDQWdJQ0FnSUNvZ1FIUjVjR1VnZTFOMGNtbHVaMzFjYmlBZ0lDQWdJQ0FnSUNvdlhHNGdJQ0FnSUNBZ0lITm9iM0JWY213NklHNTFiR3dzWEc1Y2JpQWdJQ0FnSUNBZ0x5b3FYRzRnSUNBZ0lDQWdJQ0FxSUVGd2NDQldaWEp6YVc5dVhHNGdJQ0FnSUNBZ0lDQXFYRzRnSUNBZ0lDQWdJQ0FxSUdVdVp5NGdKekl1Tnk0ekxqQW5YRzRnSUNBZ0lDQWdJQ0FxWEc0Z0lDQWdJQ0FnSUNBcUlFQjBlWEJsSUh0VGRISnBibWQ5WEc0Z0lDQWdJQ0FnSUNBcUwxeHVJQ0FnSUNBZ0lDQmhjSEJXWlhKemFXOXVPaUJ1ZFd4c0xGeHVYRzRnSUNBZ0lDQWdJQzhxS2x4dUlDQWdJQ0FnSUNBZ0tpQlRhRzl3SUZabGNuTnBiMjVjYmlBZ0lDQWdJQ0FnSUNwY2JpQWdJQ0FnSUNBZ0lDb2daUzVuTGlBbk1pNDNMak11TUNkY2JpQWdJQ0FnSUNBZ0lDcGNiaUFnSUNBZ0lDQWdJQ29nUUdSbGNISmxZMkYwWldRZ1UybHVZMlVnTVM0MExDQjFjMlVnWVhCd1ZtVnljMmx2YmlCcGJuTjBaV0ZrTGx4dUlDQWdJQ0FnSUNBZ0tseHVJQ0FnSUNBZ0lDQWdLaUJBZEhsd1pTQjdVM1J5YVc1bmZWeHVJQ0FnSUNBZ0lDQWdLaTljYmlBZ0lDQWdJQ0FnYzJodmNGWmxjbk5wYjI0NklHNTFiR3dzWEc1Y2JpQWdJQ0FnSUNBZ0x5b3FYRzRnSUNBZ0lDQWdJQ0FxSUZWU1RDQjBieUJLVTBWdVoybHVaU0JFYVhKbFkzUnZjbmt1WEc0Z0lDQWdJQ0FnSUNBcVhHNGdJQ0FnSUNBZ0lDQXFJR1V1Wnk0Z0oyaDBkSEE2THk5aGNIQXVZMjl0TDBwVFJXNW5hVzVsWEc0Z0lDQWdJQ0FnSUNBcVhHNGdJQ0FnSUNBZ0lDQXFJRUIwZVhCbElIdFRkSEpwYm1kOVhHNGdJQ0FnSUNBZ0lDQXFMMXh1SUNBZ0lDQWdJQ0JsYm1kcGJtVlZjbXc2SUc1MWJHd3NYRzVjYmlBZ0lDQWdJQ0FnTHlvcVhHNGdJQ0FnSUNBZ0lDQXFJRVZ1WjJsdVpTQkZiblpwY205dWJXVnVkRnh1SUNBZ0lDQWdJQ0FnS2x4dUlDQWdJQ0FnSUNBZ0tpQkVaV1pwYm1WeklIUm9aU0JtZFc1amRHbHZibUZzYVhSNUlHOW1JSFJvWlNCbGJtZHBibVVnYVc0Z2JXRnVlU0J6WldOMGFXOXVjeTVjYmlBZ0lDQWdJQ0FnSUNwY2JpQWdJQ0FnSUNBZ0lDb2dWbUZzZFdWek9pQW5aR1YyWld4dmNHMWxiblFuTENBbmNISnZaSFZqZEdsdmJpZGNiaUFnSUNBZ0lDQWdJQ3BjYmlBZ0lDQWdJQ0FnSUNvZ1FIUjVjR1VnZTFOMGNtbHVaMzFjYmlBZ0lDQWdJQ0FnSUNvdlhHNGdJQ0FnSUNBZ0lHVnVkbWx5YjI1dFpXNTBPaUFuY0hKdlpIVmpkR2x2Ymljc1hHNWNiaUFnSUNBZ0lDQWdMeW9xWEc0Z0lDQWdJQ0FnSUNBcUlGUnlZVzV6YkdGMGFXOXVjeUJQWW1wbFkzUmNiaUFnSUNBZ0lDQWdJQ3BjYmlBZ0lDQWdJQ0FnSUNvZ1EyOXVkR0ZwYm5NZ2RHaGxJR3h2WVdSbFpDQjBjbUZ1YzJ4aGRHbHZibk1nZEc4Z1ltVWdkWE5sWkNCM2FYUm9hVzRnU2xORmJtZHBibVV1WEc0Z0lDQWdJQ0FnSUNBcVhHNGdJQ0FnSUNBZ0lDQXFJRUJ6WldVZ2FuTmxMbU52Y21VdWJHRnVaeUJ2WW1wbFkzUmNiaUFnSUNBZ0lDQWdJQ3BjYmlBZ0lDQWdJQ0FnSUNvZ1FIUjVjR1VnZTA5aWFtVmpkSDFjYmlBZ0lDQWdJQ0FnSUNvdlhHNGdJQ0FnSUNBZ0lIUnlZVzV6YkdGMGFXOXVjem9nZTMwc1hHNWNiaUFnSUNBZ0lDQWdMeW9xWEc0Z0lDQWdJQ0FnSUNBcUlFMXZaSFZzWlNCRGIyeHNaV04wYVc5dWMxeHVJQ0FnSUNBZ0lDQWdLbHh1SUNBZ0lDQWdJQ0FnS2lCUWNtOTJhV1JsSUdGeWNtRjVJSGRwZEdnZ2V5QnVZVzFsT2lBbkp5d2dZWFIwY21saWRYUmxPaUFuSjMwZ2IySnFaV04wY3lCMGFHRjBJR1JsWm1sdVpTQjBhR1VnWTI5c2JHVmpkR2x2Ym5NZ2RHOGdZbVVnZFhObFpDQjNhWFJvYVc1Y2JpQWdJQ0FnSUNBZ0lDb2dkR2hsSUdGd2NHeHBZMkYwYVc5dUxseHVJQ0FnSUNBZ0lDQWdLbHh1SUNBZ0lDQWdJQ0FnS2lCQWRIbHdaU0I3UVhKeVlYbDlYRzRnSUNBZ0lDQWdJQ0FxTDF4dUlDQWdJQ0FnSUNCamIyeHNaV04wYVc5dWN6b2dXMTBzWEc1Y2JpQWdJQ0FnSUNBZ0x5b3FYRzRnSUNBZ0lDQWdJQ0FxSUVOMWNuSmxiblFnVEdGdVozVmhaMlVnUTI5a1pWeHVJQ0FnSUNBZ0lDQWdLbHh1SUNBZ0lDQWdJQ0FnS2lCQWRIbHdaU0I3VTNSeWFXNW5mVnh1SUNBZ0lDQWdJQ0FnS2k5Y2JpQWdJQ0FnSUNBZ2JHRnVaM1ZoWjJWRGIyUmxPaUFuWkdVbkxGeHVYRzRnSUNBZ0lDQWdJQzhxS2x4dUlDQWdJQ0FnSUNBZ0tpQlRaWFFnZEdobElHUmxZblZuSUd4bGRtVnNJSFJ2SUc5dVpTQnZaaUIwYUdVZ1ptOXNiRzkzYVc1bk9pQW5SRVZDVlVjbkxDQW5TVTVHVHljc0lDZE1UMGNuTENBblYwRlNUaWNzSUNkRlVsSlBVaWNzWEc0Z0lDQWdJQ0FnSUNBcUlDZEJURVZTVkNjc0lDZFRTVXhGVGxRbkxseHVJQ0FnSUNBZ0lDQWdLbHh1SUNBZ0lDQWdJQ0FnS2lCQWRIbHdaU0I3VTNSeWFXNW5mVnh1SUNBZ0lDQWdJQ0FnS2k5Y2JpQWdJQ0FnSUNBZ1pHVmlkV2M2SUNkVFNVeEZUbFFuTEZ4dVhHNGdJQ0FnSUNBZ0lDOHFLbHh1SUNBZ0lDQWdJQ0FnS2lCVmMyVWdZMkZqYUdVZ1luVnpkR2x1WnlCMFpXTm9ibWx4ZFdVZ2QyaGxiaUJzYjJGa2FXNW5JRzF2WkhWc1pYTXVYRzRnSUNBZ0lDQWdJQ0FxWEc0Z0lDQWdJQ0FnSUNBcUlFQmtaWEJ5WldOaGRHVmtJRk5wYm1ObElIWXhMalJjYmlBZ0lDQWdJQ0FnSUNwY2JpQWdJQ0FnSUNBZ0lDb2dRSE5sWlNCcWMyVXVZMjl5WlM1dGIyUjFiR1ZmYkc5aFpHVnlJRzlpYW1WamRGeHVJQ0FnSUNBZ0lDQWdLbHh1SUNBZ0lDQWdJQ0FnS2lCQWRIbHdaU0I3UW05dmJHVmhibjFjYmlBZ0lDQWdJQ0FnSUNvdlhHNGdJQ0FnSUNBZ0lHTmhZMmhsUW5WemREb2dkSEoxWlN4Y2JseHVJQ0FnSUNBZ0lDQXZLaXBjYmlBZ0lDQWdJQ0FnSUNvZ1ZYTmxJR05oWTJobElHSjFjM1JwYm1jZ2RHOXJaVzRnWVhNZ2NHRnlkQ0J2WmlCbWFXeGxJRzVoYldVdVhHNGdJQ0FnSUNBZ0lDQXFYRzRnSUNBZ0lDQWdJQ0FxSUVCMGVYQmxJSHRDYjI5c1pXRnVmVnh1SUNBZ0lDQWdJQ0FnS2k5Y2JpQWdJQ0FnSUNBZ1luVnpkRVpwYkdWek9pQm1ZV3h6WlN4Y2JseHVJQ0FnSUNBZ0lDQXZLaXBjYmlBZ0lDQWdJQ0FnSUNvZ1YyaGxkR2hsY2lCMGFHVWdZMnhwWlc1MElHaGhjeUJoSUcxdlltbHNaU0JwYm5SbGNtWmhZMlV1WEc0Z0lDQWdJQ0FnSUNBcVhHNGdJQ0FnSUNBZ0lDQXFJRUIwZVhCbElIdENiMjlzWldGdWZWeHVJQ0FnSUNBZ0lDQWdLaTljYmlBZ0lDQWdJQ0FnYlc5aWFXeGxPaUFvTDBGdVpISnZhV1I4ZDJWaVQxTjhhVkJvYjI1bGZHbFFZV1I4YVZCdlpIeENiR0ZqYTBKbGNuSjVmRWxGVFc5aWFXeGxmRTl3WlhKaElFMXBibWt2YVM1MFpYTjBLRzVoZG1sbllYUnZjaTUxYzJWeVFXZGxiblFwS1N4Y2JseHVJQ0FnSUNBZ0lDQXZLaXBjYmlBZ0lDQWdJQ0FnSUNvZ1YyaGxkR2hsY2lCMGFHVWdZMnhwWlc1MElITjFjSEJ2Y25SeklIUnZkV05vSUdWMlpXNTBjeTVjYmlBZ0lDQWdJQ0FnSUNwY2JpQWdJQ0FnSUNBZ0lDb2dRSFI1Y0dVZ2UwSnZiMnhsWVc1OVhHNGdJQ0FnSUNBZ0lDQXFMMXh1SUNBZ0lDQWdJQ0IwYjNWamFEb2dLQ2duYjI1MGIzVmphSE4wWVhKMEp5QnBiaUIzYVc1a2IzY3BJSHg4SUhkcGJtUnZkeTV2Ym5SdmRXTm9jM1JoY25RZ2ZId2dkMmx1Wkc5M0xtOXViWE5uWlhOMGRYSmxZMmhoYm1kbEtTQS9JSFJ5ZFdVZ09pQm1ZV3h6WlN4Y2JseHVJQ0FnSUNBZ0lDQXZLaXBjYmlBZ0lDQWdJQ0FnSUNvZ1UzQmxZMmxtZVNCMGFHVWdjR0YwYUNCbWIzSWdkR2hsSUdacGJHVWdiV0Z1WVdkbGNpNWNiaUFnSUNBZ0lDQWdJQ3BjYmlBZ0lDQWdJQ0FnSUNvZ1FHUmxjSEpsWTJGMFpXUWdVMmx1WTJVZ2RqRXVORnh1SUNBZ0lDQWdJQ0FnS2x4dUlDQWdJQ0FnSUNBZ0tpQkFkSGx3WlNCN1UzUnlhVzVuZlZ4dUlDQWdJQ0FnSUNBZ0tpOWNiaUFnSUNBZ0lDQWdabWxzWlcxaGJtRm5aWEk2SUNkcGJtTnNkV1JsY3k5amEyVmthWFJ2Y2k5bWFXeGxiV0Z1WVdkbGNpOXBibVJsZUM1b2RHMXNKeXhjYmx4dUlDQWdJQ0FnSUNBdktpcGNiaUFnSUNBZ0lDQWdJQ29nVUdGblpTQjBiMnRsYmlCMGJ5QnBibU5zZFdSbElHbHVJR1YyWlhKNUlFRktRVmdnY21WeGRXVnpkQzVjYmlBZ0lDQWdJQ0FnSUNwY2JpQWdJQ0FnSUNBZ0lDb2dWR2hsSUhCaFoyVWdkRzlyWlc0Z2FYTWdkWE5sWkNCMGJ5QmhkbTlwWkNCRFUxSkdJR0YwZEdGamEzTXVJRWwwSUcxMWMzUWdZbVVnY0hKdmRtbGtaV1FnWW5rZ2RHaGxJR0poWTJ0bGJtUWdZVzVrSUdsMElIZHBiR3hjYmlBZ0lDQWdJQ0FnSUNvZ1ltVWdkbUZzYVdSaGRHVmtJSFJvWlhKbExseHVJQ0FnSUNBZ0lDQWdLbHh1SUNBZ0lDQWdJQ0FnS2lCQWRIbHdaU0I3VTNSeWFXNW5mVnh1SUNBZ0lDQWdJQ0FnS2k5Y2JpQWdJQ0FnSUNBZ2NHRm5aVlJ2YTJWdU9pQW5KeXhjYmx4dUlDQWdJQ0FnSUNBdktpcGNiaUFnSUNBZ0lDQWdJQ29nUTJGamFHVWdWRzlyWlc0Z1UzUnlhVzVuWEc0Z0lDQWdJQ0FnSUNBcVhHNGdJQ0FnSUNBZ0lDQXFJRlJvYVhNZ1kyOXVabWxuZFhKaGRHbHZiaUIyWVd4MVpTQjNhV3hzSUdKbElIVnpaV1FnYVc0Z2NISnZaSFZqZEdsdmJpQmxiblpwY205dWJXVnVkQ0JtYjNJZ1kyRmphR1VnWW5WemRHbHVaeTRnU1hRZ2JYVnpkRnh1SUNBZ0lDQWdJQ0FnS2lCaVpTQndjbTkyYVdSbFpDQjNhWFJvSUhSb1pTQjNhVzVrYjNjdVNsTkZibWRwYm1WRGIyNW1hV2QxY21GMGFXOXVJRzlpYW1WamRDNWNiaUFnSUNBZ0lDQWdJQ3BjYmlBZ0lDQWdJQ0FnSUNvZ1FIUjVjR1VnZTFOMGNtbHVaMzFjYmlBZ0lDQWdJQ0FnSUNvdlhHNGdJQ0FnSUNBZ0lHTmhZMmhsVkc5clpXNDZJQ2NuTEZ4dVhHNGdJQ0FnSUNBZ0lDOHFLbHh1SUNBZ0lDQWdJQ0FnS2lCRVpXWnBibVZ6SUhkb1pYUm9aWElnZEdobElHaHBjM1J2Y25rZ2IySnFaV04wSUdseklHRjJZV2xzWVdKc1pTNWNiaUFnSUNBZ0lDQWdJQ3BjYmlBZ0lDQWdJQ0FnSUNvZ1FIUjVjR1VnZTBKdmIyeGxZVzU5WEc0Z0lDQWdJQ0FnSUNBcUwxeHVJQ0FnSUNBZ0lDQm9hWE4wYjNKNU9pQm9hWE4wYjNKNUlDWW1JR2hwYzNSdmNua3VjbVZ3YkdGalpWTjBZWFJsSUNZbUlHaHBjM1J2Y25rdWNIVnphRk4wWVhSbExGeHVYRzRnSUNBZ0lDQWdJQzhxS2x4dUlDQWdJQ0FnSUNBZ0tpQldkV1VnUTI5dVptbG5kWEpoZEdsdmJseHVJQ0FnSUNBZ0lDQWdLbHh1SUNBZ0lDQWdJQ0FnS2lCVWFHbHpJR052Ym1acFozVnlZWFJwYjI0Z2QzZHBiR3dnWW1VZ2RYTmxaQ0JtYjNJZ1lXUmthVzVuSUZaMVpTQnpkWEJ3YjNKMElHbHVJSFJvWlNCamRYSnlaVzUwSUhCaFoyVXVYRzRnSUNBZ0lDQWdJQ0FxWEc0Z0lDQWdJQ0FnSUNBcUlFQjBlWEJsSUh0UFltcGxZM1I5WEc0Z0lDQWdJQ0FnSUNBcUwxeHVJQ0FnSUNBZ0lDQjJkV1U2SUc1MWJHd3NYRzRnSUNBZ2ZUdGNibHh1SUNBZ0lDOHFLbHh1SUNBZ0lDQXFJRUpzWVdOcmJHbHpkQ0JqYjI1bWFXY2dkbUZzZFdWeklHbHVJSEJ5YjJSMVkzUnBiMjRnWlc1MmFYSnZibTFsYm5RdVhHNGdJQ0FnSUNwY2JpQWdJQ0FnS2lCQWRIbHdaU0I3VTNSeWFXNW5XMTE5WEc0Z0lDQWdJQ292WEc0Z0lDQWdZMjl1YzNRZ1lteGhZMnRzYVhOMElEMGdXMXh1SUNBZ0lDQWdJQ0FuZG1WeWMybHZiaWNzWEc0Z0lDQWdJQ0FnSUNkaGNIQldaWEp6YVc5dUp5eGNiaUFnSUNBZ0lDQWdKM05vYjNCV1pYSnphVzl1SjF4dUlDQWdJRjA3WEc1Y2JpQWdJQ0F2THlBdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMxY2JpQWdJQ0F2THlCUVZVSk1TVU1nVFVWVVNFOUVVMXh1SUNBZ0lDOHZJQzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMVnh1WEc0Z0lDQWdMeW9xWEc0Z0lDQWdJQ29nUjJWMElHRWdZMjl1Wm1sbmRYSmhkR2x2YmlCMllXeDFaUzVjYmlBZ0lDQWdLbHh1SUNBZ0lDQXFJRUJ3WVhKaGJTQjdVM1J5YVc1bmZTQnVZVzFsSUZSb1pTQmpiMjVtYVdkMWNtRjBhVzl1SUhaaGJIVmxJRzVoYldVZ2RHOGdZbVVnY21WMGNtbGxkbVZrTGx4dUlDQWdJQ0FxWEc0Z0lDQWdJQ29nUUhKbGRIVnliaUI3S24wZ1VtVjBkWEp1Y3lCMGFHVWdZMjl1Wm1sbklIWmhiSFZsTGx4dUlDQWdJQ0FxTDF4dUlDQWdJR1Y0Y0c5eWRITXVaMlYwSUQwZ1puVnVZM1JwYjI0Z0tHNWhiV1VwSUh0Y2JpQWdJQ0FnSUNBZ2FXWWdLR052Ym1acFp5NWxiblpwY205dWJXVnVkQ0E5UFQwZ0ozQnliMlIxWTNScGIyNG5JQ1ltSUdKc1lXTnJiR2x6ZEM1cGJtTnNkV1JsY3lodVlXMWxLU2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdjbVYwZFhKdUlHNTFiR3c3WEc0Z0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQnlaWFIxY200Z1kyOXVabWxuVzI1aGJXVmRPMXh1SUNBZ0lIMDdYRzVjYmlBZ0lDQXZLaXBjYmlBZ0lDQWdLaUJKYm1sMGFXRnNhWHBsSUhSb1pTQktVeUJGYm1kcGJtVWdZMjl1Wm1sbklHOWlhbVZqZEM1Y2JpQWdJQ0FnS2x4dUlDQWdJQ0FxSUZSb2FYTWdiV1YwYUc5a0lIZHBiR3dnY0dGeWMyVWdkR2hsSUdkc2IySmhiQ0JjSWtwVFJXNW5hVzVsUTI5dVptbG5kWEpoZEdsdmJsd2lJRzlpYW1WamRDQmhibVFnZEdobGJpQnlaVzF2ZG1WY2JpQWdJQ0FnS2lCcGRDQm1jbTl0SUhSb1pTQm5iRzlpWVd3Z2MyTnZjR1VnYzI4Z2RHaGhkQ0JwZENCaVpXTnZiV1Z6SUhSb1pTQnZibXg1SUdOdmJtWnBaeUJ6YjNWeVkyVWdabTl5SUdwaGRtRnpZM0pwY0hRdVhHNGdJQ0FnSUNwY2JpQWdJQ0FnS2lCT2IzUnBZMlU2SUZSb1pTQnZibXg1SUhKbGNYVnBjbVZrSUVwVFJXNW5hVzVsUTI5dVptbG5kWEpoZEdsdmJpQjJZV3gxWlhNZ1lYSmxJSFJvWlNCY0ltVnVkbWx5YjI1dFpXNTBYQ0lnWVc1a0lIUm9aU0JjSW1Gd2NGVnliRndpTGx4dUlDQWdJQ0FxWEc0Z0lDQWdJQ29nUUhCaGNtRnRJSHRQWW1wbFkzUjlJR3B6Ulc1bmFXNWxRMjl1Wm1sbmRYSmhkR2x2YmlCTmRYTjBJR052Ym5SaGFXNGdhVzVtYjNKdFlYUnBiMjRnZEdoaGRDQmtaV1pwYm1VZ1kyOXlaU0J2Y0dWeVlYUnBiMjV6WEc0Z0lDQWdJQ29nYjJZZ2RHaGxJR1Z1WjJsdVpTNGdRMmhsWTJzZ2RHaGxJRndpYkdsaWN5OXBibWwwYVdGc2FYcGxYQ0lnWlc1MGNua2diMllnZEdobElHVnVaMmx1WlNCa2IyTjFiV1Z1ZEdGMGFXOXVMbHh1SUNBZ0lDQXFMMXh1SUNBZ0lHVjRjRzl5ZEhNdWFXNXBkQ0E5SUdaMWJtTjBhVzl1SUNocWMwVnVaMmx1WlVOdmJtWnBaM1Z5WVhScGIyNHBJSHRjYmlBZ0lDQWdJQ0FnWTI5dVptbG5MbVZ1ZG1seWIyNXRaVzUwSUQwZ2FuTkZibWRwYm1WRGIyNW1hV2QxY21GMGFXOXVMbVZ1ZG1seWIyNXRaVzUwTzF4dUlDQWdJQ0FnSUNCamIyNW1hV2N1WVhCd1ZYSnNJRDBnYW5ORmJtZHBibVZEYjI1bWFXZDFjbUYwYVc5dUxtRndjRlZ5YkM1eVpYQnNZV05sS0M5Y1hDOHJKQzhzSUNjbktUc2dMeThnVW1WdGIzWmxJSFJ5WVdsc2FXNW5JSE5zWVhOb0lHWnliMjBnWVhCd1ZYSnNMbHh1WEc0Z0lDQWdJQ0FnSUdsbUlDaGpiMjVtYVdjdVpXNTJhWEp2Ym0xbGJuUWdQVDA5SUNka1pYWmxiRzl3YldWdWRDY3BJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lHTnZibVpwWnk1allXTm9aVUoxYzNRZ1BTQm1ZV3h6WlR0Y2JpQWdJQ0FnSUNBZ0lDQWdJR052Ym1acFp5NXRhVzVwWm1sbFpDQTlJR1poYkhObE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnWTI5dVptbG5MbVJsWW5WbklEMGdKMFJGUWxWSEp6dGNiaUFnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUdsbUlDaHFjMFZ1WjJsdVpVTnZibVpwWjNWeVlYUnBiMjR1Wlc1bmFXNWxWWEpzSUNFOVBTQjFibVJsWm1sdVpXUXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lHTnZibVpwWnk1bGJtZHBibVZWY213Z1BTQnFjMFZ1WjJsdVpVTnZibVpwWjNWeVlYUnBiMjR1Wlc1bmFXNWxWWEpzTG5KbGNHeGhZMlVvTDF4Y0x5c2tMeXdnSnljcE8xeHVJQ0FnSUNBZ0lDQjlJR1ZzYzJVZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnWTI5dVptbG5MbVZ1WjJsdVpWVnliQ0E5SUdOdmJtWnBaeTVoY0hCVmNtd2dLeUFuTDBwVFJXNW5hVzVsTDJKMWFXeGtKenRjYmlBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lHbG1JQ2hxYzBWdVoybHVaVU52Ym1acFozVnlZWFJwYjI0dWRISmhibk5zWVhScGIyNXpJQ0U5UFNCMWJtUmxabWx1WldRcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUdOdmJtWnBaeTUwY21GdWMyeGhkR2x2Ym5NZ1BTQnFjMFZ1WjJsdVpVTnZibVpwWjNWeVlYUnBiMjR1ZEhKaGJuTnNZWFJwYjI1ek8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNCbWIzSWdLR3hsZENCelpXTjBhVzl1VG1GdFpTQnBiaUJqYjI1bWFXY3VkSEpoYm5Oc1lYUnBiMjV6S1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FuTmxMbU52Y21VdWJHRnVaeTVoWkdSVFpXTjBhVzl1S0hObFkzUnBiMjVPWVcxbExDQmpiMjVtYVdjdWRISmhibk5zWVhScGIyNXpXM05sWTNScGIyNU9ZVzFsWFNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQnBaaUFvYW5ORmJtZHBibVZEYjI1bWFXZDFjbUYwYVc5dUxtTnZiR3hsWTNScGIyNXpJQ0U5UFNCMWJtUmxabWx1WldRcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUdOdmJtWnBaeTVqYjJ4c1pXTjBhVzl1Y3lBOUlHcHpSVzVuYVc1bFEyOXVabWxuZFhKaGRHbHZiaTVqYjJ4c1pXTjBhVzl1Y3p0Y2JpQWdJQ0FnSUNBZ2ZTQmxiSE5sSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJR052Ym1acFp5NWpiMnhzWldOMGFXOXVjeUE5SUZ0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCN2JtRnRaVG9nSjJOdmJuUnliMnhzWlhKekp5d2dZWFIwY21saWRYUmxPaUFuWTI5dWRISnZiR3hsY2lkOUxGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIdHVZVzFsT2lBblpYaDBaVzV6YVc5dWN5Y3NJR0YwZEhKcFluVjBaVG9nSjJWNGRHVnVjMmx2YmlkOUxGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIdHVZVzFsT2lBbmQybGtaMlYwY3ljc0lHRjBkSEpwWW5WMFpUb2dKM2RwWkdkbGRDZDlYRzRnSUNBZ0lDQWdJQ0FnSUNCZFhHNGdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0JwWmlBb2FuTkZibWRwYm1WRGIyNW1hV2QxY21GMGFXOXVMbUZ3Y0ZabGNuTnBiMjRnSVQwOUlIVnVaR1ZtYVc1bFpDa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ1kyOXVabWxuTG1Gd2NGWmxjbk5wYjI0Z1BTQnFjMFZ1WjJsdVpVTnZibVpwWjNWeVlYUnBiMjR1WVhCd1ZtVnljMmx2Ymp0Y2JpQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJR2xtSUNocWMwVnVaMmx1WlVOdmJtWnBaM1Z5WVhScGIyNHVjMmh2Y0ZWeWJDQWhQVDBnZFc1a1pXWnBibVZrS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0JxYzJVdVkyOXlaUzVrWldKMVp5NTNZWEp1S0NkS1V5QkZibWRwYm1VNklGd2ljMmh2Y0ZWeWJGd2lJR2x6SUdSbGNISmxZMkYwWldRZ1lXNWtJSGRwYkd3Z1ltVWdjbVZ0YjNabFpDQnBiaUJLVXlCRmJtZHBibVVnZGpFdU5Td2djR3hsWVhObElDZGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXJJQ2QxYzJVZ2RHaGxJRndpWVhCd1ZYSnNYQ0lnYVc1emRHVmhaQzRuS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJR052Ym1acFp5NXphRzl3VlhKc0lEMGdhbk5GYm1kcGJtVkRiMjVtYVdkMWNtRjBhVzl1TG5Ob2IzQlZjbXd1Y21Wd2JHRmpaU2d2WEZ3dkt5UXZMQ0FuSnlrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0JqYjI1bWFXY3VZWEJ3VlhKc0lEMGdZMjl1Wm1sbkxtRndjRlZ5YkNCOGZDQmpiMjVtYVdjdWMyaHZjRlZ5YkRzZ0x5OGdUV0ZyWlNCemRYSmxJSFJvWlNCY0ltRndjRlZ5YkZ3aUlIWmhiSFZsSUdseklHNXZkQ0JsYlhCMGVTNWNiaUFnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUdsbUlDaHFjMFZ1WjJsdVpVTnZibVpwWjNWeVlYUnBiMjR1YzJodmNGWmxjbk5wYjI0Z0lUMDlJSFZ1WkdWbWFXNWxaQ2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdhbk5sTG1OdmNtVXVaR1ZpZFdjdWQyRnliaWduU2xNZ1JXNW5hVzVsT2lCY0luTm9iM0JXWlhKemFXOXVYQ0lnYVhNZ1pHVndjbVZqWVhSbFpDQmhibVFnZDJsc2JDQmlaU0J5WlcxdmRtVmtJR2x1SUVwVElFVnVaMmx1WlNCMk1TNDFMQ0J3YkdWaGMyVWdKMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ3NnSjNWelpTQjBhR1VnWENKaGNIQldaWEp6YVc5dVhDSWdhVzV6ZEdWaFpDNG5LVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lHTnZibVpwWnk1emFHOXdWbVZ5YzJsdmJpQTlJR3B6Ulc1bmFXNWxRMjl1Wm1sbmRYSmhkR2x2Ymk1emFHOXdWbVZ5YzJsdmJqdGNiaUFnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUdsbUlDaHFjMFZ1WjJsdVpVTnZibVpwWjNWeVlYUnBiMjR1Y0hKbFptbDRJQ0U5UFNCMWJtUmxabWx1WldRcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUdOdmJtWnBaeTV3Y21WbWFYZ2dQU0JxYzBWdVoybHVaVU52Ym1acFozVnlZWFJwYjI0dWNISmxabWw0TzF4dUlDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdhV1lnS0dwelJXNW5hVzVsUTI5dVptbG5kWEpoZEdsdmJpNXNZVzVuZFdGblpVTnZaR1VnSVQwOUlIVnVaR1ZtYVc1bFpDa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ1kyOXVabWxuTG14aGJtZDFZV2RsUTI5a1pTQTlJR3B6Ulc1bmFXNWxRMjl1Wm1sbmRYSmhkR2x2Ymk1c1lXNW5kV0ZuWlVOdlpHVTdYRzRnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNCcFppQW9aRzlqZFcxbGJuUXVaMlYwUld4bGJXVnVkRUo1U1dRb0oybHVhWFF0YW5NbktTQWhQVDBnYm5Wc2JGeHVJQ0FnSUNBZ0lDQWdJQ0FnSmlZZ1pHOWpkVzFsYm5RdVoyVjBSV3hsYldWdWRFSjVTV1FvSjJsdWFYUXRhbk1uS1M1b1lYTkJkSFJ5YVdKMWRHVW9KMlJoZEdFdGNHRm5aUzEwYjJ0bGJpY3BLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQnFjMFZ1WjJsdVpVTnZibVpwWjNWeVlYUnBiMjR1Y0dGblpWUnZhMlZ1SUQwZ1pHOWpkVzFsYm5RdVoyVjBSV3hsYldWdWRFSjVTV1FvSjJsdWFYUXRhbk1uS1M1blpYUkJkSFJ5YVdKMWRHVW9KMlJoZEdFdGNHRm5aUzEwYjJ0bGJpY3BPMXh1SUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ2FXWWdLR3B6Ulc1bmFXNWxRMjl1Wm1sbmRYSmhkR2x2Ymk1d1lXZGxWRzlyWlc0Z0lUMDlJSFZ1WkdWbWFXNWxaQ2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdZMjl1Wm1sbkxuQmhaMlZVYjJ0bGJpQTlJR3B6Ulc1bmFXNWxRMjl1Wm1sbmRYSmhkR2x2Ymk1d1lXZGxWRzlyWlc0N1hHNGdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0JwWmlBb2FuTkZibWRwYm1WRGIyNW1hV2QxY21GMGFXOXVMbU5oWTJobFZHOXJaVzRnSVQwOUlIVnVaR1ZtYVc1bFpDa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ1kyOXVabWxuTG1OaFkyaGxWRzlyWlc0Z1BTQnFjMFZ1WjJsdVpVTnZibVpwWjNWeVlYUnBiMjR1WTJGamFHVlViMnRsYmp0Y2JpQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJR2xtSUNocWMwVnVaMmx1WlVOdmJtWnBaM1Z5WVhScGIyNHVZblZ6ZEVacGJHVnpJQ0U5UFNCMWJtUmxabWx1WldRcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUdOdmJtWnBaeTVpZFhOMFJtbHNaWE1nUFNCcWMwVnVaMmx1WlVOdmJtWnBaM1Z5WVhScGIyNHVZblZ6ZEVacGJHVnpPMXh1SUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ2FXWWdLR3B6Ulc1bmFXNWxRMjl1Wm1sbmRYSmhkR2x2Ymk1MmRXVWdJVDA5SUhWdVpHVm1hVzVsWkNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnWTI5dVptbG5MbloxWlNBOUlHcHpSVzVuYVc1bFEyOXVabWxuZFhKaGRHbHZiaTUyZFdVN1hHNGdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0F2THlCQlpHUWdkR2hsSUZ3aWRHOTFZMmhGZG1WdWRITmNJaUJsYm5SeWVTQnpieUIwYUdGMElHMXZaSFZzWlhNZ1kyRnVJR0pwYm1RZ2RtRnlhVzkxY3lCMGIzVmphQ0JsZG1WdWRITWdaR1Z3Wlc1a2FXNW5JSFJvWlNCaWNtOTNjMlZ5TGx4dUlDQWdJQ0FnSUNCamIyNXpkQ0JuWlc1bGNtRnNWRzkxWTJoRmRtVnVkSE1nUFNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0J6ZEdGeWREb2dKM1J2ZFdOb2MzUmhjblFuTEZ4dUlDQWdJQ0FnSUNBZ0lDQWdaVzVrT2lBbmRISnZkV05vWlc1a0p5eGNiaUFnSUNBZ0lDQWdJQ0FnSUcxdmRtVTZJQ2QwYjNWamFHMXZkbVVuWEc0Z0lDQWdJQ0FnSUgwN1hHNWNiaUFnSUNBZ0lDQWdZMjl1YzNRZ2JXbGpjbTl6YjJaMFZHOTFZMmhGZG1WdWRITWdQU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQnpkR0Z5ZERvZ0ozQnZhVzUwWlhKa2IzZHVKeXhjYmlBZ0lDQWdJQ0FnSUNBZ0lHVnVaRG9nSjNCdmFXNTBaWEoxY0Njc1hHNGdJQ0FnSUNBZ0lDQWdJQ0J0YjNabE9pQW5jRzlwYm5SbGNtMXZkbVVuWEc0Z0lDQWdJQ0FnSUgwN1hHNWNiaUFnSUNBZ0lDQWdZMjl1Wm1sbkxuUnZkV05vUlhabGJuUnpJRDBnS0hkcGJtUnZkeTV2Ym0xeloyVnpkSFZ5WldOb1lXNW5aU2tnUHlCdGFXTnliM052Wm5SVWIzVmphRVYyWlc1MGN5QTZJR2RsYm1WeVlXeFViM1ZqYUVWMlpXNTBjenRjYmx4dUlDQWdJQ0FnSUNBdkx5QlRaWFFnYVc1cGRHbGhiQ0J5WldkcGMzUnllU0IyWVd4MVpYTXVJRnh1SUNBZ0lDQWdJQ0JtYjNJZ0tHeGxkQ0JsYm5SeWVTQnBiaUJxYzBWdVoybHVaVU52Ym1acFozVnlZWFJwYjI0dWNtVm5hWE4wY25rcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUdwelpTNWpiM0psTG5KbFoybHpkSEo1TG5ObGRDaGxiblJ5ZVN3Z2FuTkZibWRwYm1WRGIyNW1hV2QxY21GMGFXOXVMbkpsWjJsemRISjVXMlZ1ZEhKNVhTazdYRzRnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNBdkx5QkpibWwwYVdGc2FYcGxJSFJvWlNCdGIyUjFiR1VnYkc5aFpHVnlJRzlpYW1WamRDNWNiaUFnSUNBZ0lDQWdhbk5sTG1OdmNtVXViVzlrZFd4bFgyeHZZV1JsY2k1cGJtbDBLQ2s3WEc1Y2JpQWdJQ0FnSUNBZ0x5OGdSR1Z6ZEhKdmVTQm5iRzlpWVd3Z1JXNW5hVzVsUTI5dVptbG5kWEpoZEdsdmJpQnZZbXBsWTNRdVhHNGdJQ0FnSUNBZ0lHUmxiR1YwWlNCM2FXNWtiM2N1U2xORmJtZHBibVZEYjI1bWFXZDFjbUYwYVc5dU8xeHVJQ0FnSUgwN1hHNWNibjBvYW5ObExtTnZjbVV1WTI5dVptbG5LU2s3WEc0aUxDSXZLaUF0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMVnh1SUdSbFluVm5MbXB6SURJd01UWXRNRGt0TURoY2JpQkhZVzFpYVc4Z1IyMWlTRnh1SUdoMGRIQTZMeTkzZDNjdVoyRnRZbWx2TG1SbFhHNGdRMjl3ZVhKcFoyaDBJQ2hqS1NBeU1ERTJJRWRoYldKcGJ5QkhiV0pJWEc0Z1VtVnNaV0Z6WldRZ2RXNWtaWElnZEdobElFZE9WU0JIWlc1bGNtRnNJRkIxWW14cFl5Qk1hV05sYm5ObElDaFdaWEp6YVc5dUlESXBYRzRnVzJoMGRIQTZMeTkzZDNjdVoyNTFMbTl5Wnk5c2FXTmxibk5sY3k5bmNHd3RNaTR3TG1oMGJXeGRYRzRnTFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzFjYmlBcUwxeHVYRzVxYzJVdVkyOXlaUzVrWldKMVp5QTlJR3B6WlM1amIzSmxMbVJsWW5WbklIeDhJSHQ5TzF4dVhHNHZLaXBjYmlBcUlFcFRSU0JFWldKMVp5Qk5iMlIxYkdWY2JpQXFYRzRnS2lCVWFHbHpJRzlpYW1WamRDQndjbTkyYVdSbGN5QmhiaUIzY21Gd2NHVnlJSFJ2SUhSb1pTQmpiMjV6YjJ4bExteHZaeUJtZFc1amRHbHZiaUJoYm1RZ1pXNWhZbXhsY3lCbFlYTjVJSFZ6WlZ4dUlDb2diMllnZEdobElHUnBabVpsY21WdWRDQnNiMmNnZEhsd1pYTWdiR2xyWlNCY0ltbHVabTljSWl3Z1hDSjNZWEp1YVc1blhDSXNJRndpWlhKeWIzSmNJaUJsZEdNdVhHNGdLbHh1SUNvZ1FHMXZaSFZzWlNCS1UwVXZRMjl5WlM5a1pXSjFaMXh1SUNvdlhHNG9ablZ1WTNScGIyNGdLR1Y0Y0c5eWRITXBJSHRjYmlBZ0lDQW5kWE5sSUhOMGNtbGpkQ2M3WEc1Y2JpQWdJQ0F2THlBdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMxY2JpQWdJQ0F2THlCV1FWSkpRVUpNUlZOY2JpQWdJQ0F2THlBdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMxY2JseHVJQ0FnSUM4cUtseHVJQ0FnSUNBcUlFQjBlWEJsSUh0VGRISnBibWQ5WEc0Z0lDQWdJQ292WEc0Z0lDQWdZMjl1YzNRZ1ZGbFFSVjlFUlVKVlJ5QTlJQ2RFUlVKVlJ5YzdYRzVjYmlBZ0lDQXZLaXBjYmlBZ0lDQWdLaUJBZEhsd1pTQjdVM1J5YVc1bmZWeHVJQ0FnSUNBcUwxeHVJQ0FnSUdOdmJuTjBJRlJaVUVWZlNVNUdUeUE5SUNkSlRrWlBKenRjYmx4dUlDQWdJQzhxS2x4dUlDQWdJQ0FxSUVCMGVYQmxJSHRUZEhKcGJtZDlYRzRnSUNBZ0lDb3ZYRzRnSUNBZ1kyOXVjM1FnVkZsUVJWOU1UMGNnUFNBblRFOUhKenRjYmx4dUlDQWdJQzhxS2x4dUlDQWdJQ0FxSUVCMGVYQmxJSHRUZEhKcGJtZDlYRzRnSUNBZ0lDb3ZYRzRnSUNBZ1kyOXVjM1FnVkZsUVJWOVhRVkpPSUQwZ0oxZEJVazRuTzF4dVhHNGdJQ0FnTHlvcVhHNGdJQ0FnSUNvZ1FIUjVjR1VnZTFOMGNtbHVaMzFjYmlBZ0lDQWdLaTljYmlBZ0lDQmpiMjV6ZENCVVdWQkZYMFZTVWs5U0lEMGdKMFZTVWs5U0p6dGNibHh1SUNBZ0lDOHFLbHh1SUNBZ0lDQXFJRUIwZVhCbElIdFRkSEpwYm1kOVhHNGdJQ0FnSUNvdlhHNGdJQ0FnWTI5dWMzUWdWRmxRUlY5QlRFVlNWQ0E5SUNkQlRFVlNWQ2M3WEc1Y2JpQWdJQ0F2S2lwY2JpQWdJQ0FnS2lCQWRIbHdaU0I3VTNSeWFXNW5mVnh1SUNBZ0lDQXFMMXh1SUNBZ0lHTnZibk4wSUZSWlVFVmZUVTlDU1V4RklEMGdKMDFQUWtsTVJTYzdYRzVjYmlBZ0lDQXZLaXBjYmlBZ0lDQWdLaUJBZEhsd1pTQjdVM1J5YVc1bmZWeHVJQ0FnSUNBcUwxeHVJQ0FnSUdOdmJuTjBJRlJaVUVWZlUwbE1SVTVVSUQwZ0oxTkpURVZPVkNjN1hHNWNiaUFnSUNBdktpcGNiaUFnSUNBZ0tpQkJiR3dnY0c5emMybGliR1VnWkdWaWRXY2diR1YyWld4eklHbHVJSFJvWlNCdmNtUmxjaUJ2WmlCcGJYQnZjblJoYm1ObExseHVJQ0FnSUNBcVhHNGdJQ0FnSUNvZ1FIUjVjR1VnZTFOMGNtbHVaMXRkZlZ4dUlDQWdJQ0FxTDF4dUlDQWdJR052Ym5OMElHeGxkbVZzY3lBOUlGdGNiaUFnSUNBZ0lDQWdWRmxRUlY5RVJVSlZSeXhjYmlBZ0lDQWdJQ0FnVkZsUVJWOUpUa1pQTEZ4dUlDQWdJQ0FnSUNCVVdWQkZYMHhQUnl4Y2JpQWdJQ0FnSUNBZ1ZGbFFSVjlYUVZKT0xGeHVJQ0FnSUNBZ0lDQlVXVkJGWDBWU1VrOVNMRnh1SUNBZ0lDQWdJQ0JVV1ZCRlgwRk1SVkpVTEZ4dUlDQWdJQ0FnSUNCVVdWQkZYMDFQUWtsTVJTeGNiaUFnSUNBZ0lDQWdWRmxRUlY5VFNVeEZUbFJjYmlBZ0lDQmRPMXh1WEc0Z0lDQWdMeThnTFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0WEc0Z0lDQWdMeThnUmxWT1ExUkpUMDVUWEc0Z0lDQWdMeThnTFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0WEc1Y2JpQWdJQ0F2S2lwY2JpQWdJQ0FnS2lCVFpYUWdSbUYyYVdOdmJpQjBieUJGY25KdmNpQlRkR0YwWlM1Y2JpQWdJQ0FnS2x4dUlDQWdJQ0FxSUZSb2FYTWdiV1YwYUc5a0lIZHBiR3dnYjI1c2VTQjNiM0pySUdsbUlEeGpZVzUyWVhNK0lHbHpJSE4xY0hCdmNuUmxaQ0JtY205dElIUm9aU0JpY205M2MyVnlMbHh1SUNBZ0lDQXFYRzRnSUNBZ0lDb2dRSEJ5YVhaaGRHVmNiaUFnSUNBZ0tpOWNiaUFnSUNCbWRXNWpkR2x2YmlCZmMyVjBSbUYyYVdOdmJsUnZSWEp5YjNKVGRHRjBaU2dwSUh0Y2JpQWdJQ0FnSUNBZ1kyOXVjM1FnWTJGdWRtRnpJRDBnWkc5amRXMWxiblF1WTNKbFlYUmxSV3hsYldWdWRDZ25ZMkZ1ZG1Gekp5azdYRzRnSUNBZ0lDQWdJR052Ym5OMElHWmhkbWxqYjI0Z1BTQmtiMk4xYldWdWRDNXhkV1Z5ZVZObGJHVmpkRzl5S0NkYmNtVnNQVndpYzJodmNuUmpkWFFnYVdOdmJsd2lYU2NwTzF4dVhHNGdJQ0FnSUNBZ0lHbG1JQ2hqWVc1MllYTXVaMlYwUTI5dWRHVjRkQ0FtSmlBaFptRjJhV052Ymk1amJHRnpjMDVoYldVdWFXNWpiSFZrWlhNb0oyVnljbTl5TFhOMFlYUmxKeWtwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJR052Ym5OMElHbHRaeUE5SUdSdlkzVnRaVzUwTG1OeVpXRjBaVVZzWlcxbGJuUW9KMmx0WnljcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnWTJGdWRtRnpMbWhsYVdkb2RDQTlJR05oYm5aaGN5NTNhV1IwYUNBOUlERTJPMXh1SUNBZ0lDQWdJQ0FnSUNBZ1kyOXVjM1FnWTNSNElEMGdZMkZ1ZG1GekxtZGxkRU52Ym5SbGVIUW9KekprSnlrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0JwYldjdWIyNXNiMkZrSUQwZ1puVnVZM1JwYjI0Z0tDa2dleUF2THlCRGIyNTBhVzUxWlNCdmJtTmxJSFJvWlNCcGJXRm5aU0JvWVhNZ1ltVmxiaUJzYjJGa1pXUXVJRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR04wZUM1a2NtRjNTVzFoWjJVb2RHaHBjeXdnTUN3Z01DazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdZM1I0TG1kc2IySmhiRUZzY0doaElEMGdNQzQyTlR0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCamRIZ3VabWxzYkZOMGVXeGxJRDBnSnlOR1JqQXdNREFuTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdOMGVDNXlaV04wS0RBc0lEQXNJREUyTENBeE5pazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdZM1I0TG1acGJHd29LVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JtWVhacFkyOXVMbWh5WldZZ1BTQmpZVzUyWVhNdWRHOUVZWFJoVlZKTUtDZHBiV0ZuWlM5d2JtY25LVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JtWVhacFkyOXVMbU5zWVhOelRtRnRaU0FyUFNBblpYSnliM0l0YzNSaGRHVW5PMXh1SUNBZ0lDQWdJQ0FnSUNBZ2ZUdGNiaUFnSUNBZ0lDQWdJQ0FnSUdsdFp5NXpjbU1nUFNCbVlYWnBZMjl1TG1oeVpXWTdYRzRnSUNBZ0lDQWdJSDFjYmlBZ0lDQjlYRzVjYmlBZ0lDQXZLaXBjYmlBZ0lDQWdLaUJGY25KdmNpQm9ZVzVrYkdWeUlIUm9ZWFFnWm1WMFkyaGxjeUJoYkd3Z1pYaGpaWEIwYVc5dWN5QjBhSEp2ZDI0Z1lua2dkR2hsSUdwaGRtRnpZM0pwY0hRdVhHNGdJQ0FnSUNwY2JpQWdJQ0FnS2lCQWNISnBkbUYwWlZ4dUlDQWdJQ0FxTDF4dUlDQWdJR1oxYm1OMGFXOXVJRjluYkc5aVlXeEZjbkp2Y2toaGJtUnNaWElvS1NCN1hHNGdJQ0FnSUNBZ0lHbG1JQ2hxYzJVdVkyOXlaUzVqYjI1bWFXY3VaMlYwS0NkbGJuWnBjbTl1YldWdWRDY3BJQ0U5UFNBbmNISnZaSFZqZEdsdmJpY3BJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDOHZJRXh2WnlCMGFHVWdaWEp5YjNJZ2FXNGdkR2hsSUdKeWIzZHpaWEluY3lCamIyNXpiMnhsTGlCY2JpQWdJQ0FnSUNBZ0lDQWdJR2xtSUNocWMyVXVZMjl5WlM1a1pXSjFaeUFoUFQwZ2RXNWtaV1pwYm1Wa0tTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhbk5sTG1OdmNtVXVaR1ZpZFdjdVpYSnliM0lvSjBwVElFVnVaMmx1WlNCRmNuSnZjaUJJWVc1a2JHVnlKeXdnWVhKbmRXMWxiblJ6S1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJSDBnWld4elpTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdZMjl1YzI5c1pTNXNiMmNvSjBwVElFVnVaMmx1WlNCRmNuSnZjaUJJWVc1a2JHVnlKeXdnWVhKbmRXMWxiblJ6S1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdMeThnVlhCa1lYUmxJSFJvWlNCd1lXZGxJSFJwZEd4bElIZHBkR2dnWVc0Z1pYSnliM0lnWTI5MWJuUXVYRzRnSUNBZ0lDQWdJQ0FnSUNCamIyNXpkQ0J5WldkbGVDQTlJQzh1WEZ3Z1hGeGJLQzRyS1Z4Y1hWeGNJQzg3WEc0Z0lDQWdJQ0FnSUNBZ0lDQnNaWFFnZEdsMGJHVWdQU0IzYVc1a2IzY3VaRzlqZFcxbGJuUXVkR2wwYkdVN1hHNGdJQ0FnSUNBZ0lDQWdJQ0JzWlhRZ1pYSnliM0pEYjNWdWRDQTlJREU3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQzh2SUVkbGRITWdkR2hsSUdOMWNuSmxiblFnWlhKeWIzSWdZMjkxYm5RZ1lXNWtJSEpsWTNKbFlYUmxjeUIwYUdVZ1pHVm1ZWFZzZENCMGFYUnNaU0J2WmlCMGFHVWdjR0ZuWlM1Y2JpQWdJQ0FnSUNBZ0lDQWdJR2xtSUNoMGFYUnNaUzV0WVhSamFDaHlaV2RsZUNrZ0lUMDlJRzUxYkd3cElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmxjbkp2Y2tOdmRXNTBJRDBnY0dGeWMyVkpiblFvZEdsMGJHVXViV0YwWTJnb0wxeGNaQ3N2S1Zzd1hTd2dNVEFwSUNzZ01UdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjBhWFJzWlNBOUlIUnBkR3hsTG5KbGNHeGhZMlVvY21WblpYZ3NJQ2NuS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdMeThnVW1VdFkzSmxZWFJsY3lCMGFHVWdaWEp5YjNJZ1pteGhaeUJoZENCMGFHVWdkR2wwYkdVZ2QybDBhQ0IwYUdVZ2JtVjNJR1Z5Y205eUlHTnZkVzUwTGx4dUlDQWdJQ0FnSUNBZ0lDQWdkR2wwYkdVZ1BTQW40cHlXSUZzbklDc2daWEp5YjNKRGIzVnVkQ0FySUNkZElDY2dLeUIwYVhSc1pUdGNiaUFnSUNBZ0lDQWdJQ0FnSUhkcGJtUnZkeTVrYjJOMWJXVnVkQzUwYVhSc1pTQTlJSFJwZEd4bE8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBdkx5QlRaWFFnUm1GMmFXTnZiaUIwYnlCRmNuSnZjaUJUZEdGMFpTNWNiaUFnSUNBZ0lDQWdJQ0FnSUY5elpYUkdZWFpwWTI5dVZHOUZjbkp2Y2xOMFlYUmxLQ2s3WEc0Z0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQnlaWFIxY200Z2RISjFaVHRjYmlBZ0lDQjlYRzVjYmlBZ0lDQXZLaXBjYmlBZ0lDQWdLaUJGZUdWamRYUmxjeUIwYUdVZ1kyOXljbVZqZENCamIyNXpiMnhsTDJGc1pYSjBJSE4wWVhSbGJXVnVkQzVjYmlBZ0lDQWdLbHh1SUNBZ0lDQXFJRUJ3WVhKaGJTQjdUMkpxWldOMGZTQmpZV3hzWlhJZ0tHOXdkR2x2Ym1Gc0tTQkRiMjUwWVdsdWN5QjBhR1VnWTJGc2JHVnlJR2x1Wm05eWJXRjBhVzl1SUhSdklHSmxJR1JwYzNCc1lYbGxaQzVjYmlBZ0lDQWdLaUJBY0dGeVlXMGdlMDlpYW1WamRIMGdaR0YwWVNBb2IzQjBhVzl1WVd3cElFTnZiblJoYVc1eklHRnVlU0JoWkdScGRHbHZibUZzSUdSaGRHRWdkRzhnWW1VZ2FXNWpiSFZrWldRZ2FXNGdkR2hsSUdSbFluVm5JRzkxZEhCMWRDNWNiaUFnSUNBZ0tseHVJQ0FnSUNBcUlFQndjbWwyWVhSbFhHNGdJQ0FnSUNvdlhHNGdJQ0FnWm5WdVkzUnBiMjRnWDJWNFpXTjFkR1VvWTJGc2JHVnlMQ0JrWVhSaEtTQjdYRzRnSUNBZ0lDQWdJR052Ym5OMElHTjFjbkpsYm5STWIyZEpibVJsZUNBOUlHeGxkbVZzY3k1cGJtUmxlRTltS0dOaGJHeGxjaWs3WEc0Z0lDQWdJQ0FnSUdOdmJuTjBJR0ZzYkc5M1pXUk1iMmRKYm1SbGVDQTlJR3hsZG1Wc2N5NXBibVJsZUU5bUtHcHpaUzVqYjNKbExtTnZibVpwWnk1blpYUW9KMlJsWW5Wbkp5a3BPMXh1SUNBZ0lDQWdJQ0JzWlhRZ1kyOXVjMjlzWlUxbGRHaHZaQ0E5SUc1MWJHdzdYRzVjYmlBZ0lDQWdJQ0FnYVdZZ0tHTjFjbkpsYm5STWIyZEpibVJsZUNBK1BTQmhiR3h2ZDJWa1RHOW5TVzVrWlhncElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUdOdmJuTnZiR1ZOWlhSb2IyUWdQU0JqWVd4c1pYSXVkRzlNYjNkbGNrTmhjMlVvS1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnYzNkcGRHTm9JQ2hqYjI1emIyeGxUV1YwYUc5a0tTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdZMkZ6WlNBbllXeGxjblFuT2x4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmhiR1Z5ZENoS1UwOU9Mbk4wY21sdVoybG1lU2hrWVhSaEtTazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdKeVpXRnJPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWTJGelpTQW5iVzlpYVd4bEp6cGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWTI5dWMzUWdKRzF2WW1sc1pVUmxZblZuVFc5a1lXd2dQU0FrS0NjdWJXOWlhV3hsTFdSbFluVm5MVzF2WkdGc0p5azdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWWdLQ0VrYlc5aWFXeGxSR1ZpZFdkTmIyUmhiQzVzWlc1bmRHZ3BJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ1FvSnp4a2FYWWdMejRuS1Z4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDNWhaR1JEYkdGemN5Z25iVzlpYVd4bExXUmxZblZuTFcxdlpHRnNKeWxjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdVkzTnpLSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjRzl6YVhScGIyNDZJQ2RtYVhobFpDY3NYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIUnZjRG9nTUN4Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYkdWbWREb2dNQ3hjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdiV0Y0U0dWcFoyaDBPaUFuTlRBbEp5eGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2JXbHVWMmxrZEdnNklDY3lNREJ3ZUNjc1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUcxaGVGZHBaSFJvT2lBbk16QXdjSGduTEZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JpWVdOclozSnZkVzVrUTI5c2IzSTZJQ2RqY21sdGMyOXVKeXhjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdla2x1WkdWNE9pQXhNREF3TURBc1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUc5MlpYSm1iRzkzT2lBbmMyTnliMnhzSjF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMHBYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTG1Gd2NHVnVaRlJ2S0NRb0oySnZaSGtuS1NrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWtiVzlpYVd4bFJHVmlkV2ROYjJSaGJDNWhjSEJsYm1Rb0p6eHdQaWNnS3lCS1UwOU9Mbk4wY21sdVoybG1lU2hrWVhSaEtTQXJJQ2M4TDNBK0p5azdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdKeVpXRnJPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWkdWbVlYVnNkRHBjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWWdLR052Ym5OdmJHVWdQVDA5SUhWdVpHVm1hVzVsWkNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2NtVjBkWEp1T3lBdkx5QlVhR1Z5WlNCcGN5QnVieUJqYjI1emIyeGxJSE4xY0hCdmNuUWdjMjhnWkc4Z2JtOTBJSEJ5YjJObFpXUXVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb2RIbHdaVzltSUdOdmJuTnZiR1ZiWTI5dWMyOXNaVTFsZEdodlpGMHVZWEJ3YkhrZ1BUMDlJQ2RtZFc1amRHbHZiaWNnZkh3Z2RIbHdaVzltSUdOdmJuTnZiR1V1Ykc5bkxtRndjR3g1SUQwOVBTQW5ablZ1WTNScGIyNG5LU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb1kyOXVjMjlzWlZ0amIyNXpiMnhsVFdWMGFHOWtYU0FoUFQwZ2RXNWtaV1pwYm1Wa0tTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWTI5dWMyOXNaVnRqYjI1emIyeGxUV1YwYUc5a1hTNWhjSEJzZVNoamIyNXpiMnhsTENCa1lYUmhLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDBnWld4elpTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWTI5dWMyOXNaUzVzYjJjdVlYQndiSGtvWTI5dWMyOXNaU3dnWkdGMFlTazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgwZ1pXeHpaU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JqYjI1emIyeGxMbXh2Wnloa1lYUmhLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNCOVhHNGdJQ0FnZlZ4dVhHNGdJQ0FnTHlvcVhHNGdJQ0FnSUNvZ1FtbHVaQ0JIYkc5aVlXd2dSWEp5YjNJZ1NHRnVaR3hsY2x4dUlDQWdJQ0FxTDF4dUlDQWdJR1Y0Y0c5eWRITXVZbWx1WkVkc2IySmhiRVZ5Y205eVNHRnVaR3hsY2lBOUlHWjFibU4wYVc5dUlDZ3BJSHRjYmlBZ0lDQWdJQ0FnZDJsdVpHOTNMbTl1WlhKeWIzSWdQU0JmWjJ4dlltRnNSWEp5YjNKSVlXNWtiR1Z5TzF4dUlDQWdJSDA3WEc1Y2JpQWdJQ0F2S2lwY2JpQWdJQ0FnS2lCU1pYQnNZV05sY3lCamIyNXpiMnhsTG1SbFluVm5YRzRnSUNBZ0lDcGNiaUFnSUNBZ0tpQkFjR0Z5WVcxeklIc3FmU0JoY21kMWJXVnVkSE1nUVc1NUlHUmhkR0VnZEdoaGRDQnphRzkxYkdRZ1ltVWdjMmh2ZDI0Z2FXNGdkR2hsSUdOdmJuTnZiR1VnYzNSaGRHVnRaVzUwTGx4dUlDQWdJQ0FxTDF4dUlDQWdJR1Y0Y0c5eWRITXVaR1ZpZFdjZ1BTQm1kVzVqZEdsdmJpQW9LU0I3WEc0Z0lDQWdJQ0FnSUY5bGVHVmpkWFJsS0ZSWlVFVmZSRVZDVlVjc0lHRnlaM1Z0Wlc1MGN5azdYRzRnSUNBZ2ZUdGNibHh1SUNBZ0lDOHFLbHh1SUNBZ0lDQXFJRkpsY0d4aFkyVnpJR052Ym5OdmJHVXVhVzVtYjF4dUlDQWdJQ0FxWEc0Z0lDQWdJQ29nUUhCaGNtRnRjeUI3S24wZ1lYSm5kVzFsYm5SeklFRnVlU0JrWVhSaElIUm9ZWFFnYzJodmRXeGtJR0psSUhOb2IzZHVJR2x1SUhSb1pTQmpiMjV6YjJ4bElITjBZWFJsYldWdWRDNWNiaUFnSUNBZ0tpOWNiaUFnSUNCbGVIQnZjblJ6TG1sdVptOGdQU0JtZFc1amRHbHZiaUFvS1NCN1hHNGdJQ0FnSUNBZ0lGOWxlR1ZqZFhSbEtGUlpVRVZmU1U1R1R5d2dZWEpuZFcxbGJuUnpLVHRjYmlBZ0lDQjlPMXh1WEc0Z0lDQWdMeW9xWEc0Z0lDQWdJQ29nVW1Wd2JHRmpaWE1nWTI5dWMyOXNaUzVzYjJkY2JpQWdJQ0FnS2x4dUlDQWdJQ0FxSUVCd1lYSmhiWE1nZXlwOUlHRnlaM1Z0Wlc1MGN5QkJibmtnWkdGMFlTQjBhR0YwSUhOb2IzVnNaQ0JpWlNCemFHOTNiaUJwYmlCMGFHVWdZMjl1YzI5c1pTQnpkR0YwWlcxbGJuUXVYRzRnSUNBZ0lDb3ZYRzRnSUNBZ1pYaHdiM0owY3k1c2IyY2dQU0JtZFc1amRHbHZiaUFvS1NCN1hHNGdJQ0FnSUNBZ0lGOWxlR1ZqZFhSbEtGUlpVRVZmVEU5SExDQmhjbWQxYldWdWRITXBPMXh1SUNBZ0lIMDdYRzVjYmlBZ0lDQXZLaXBjYmlBZ0lDQWdLaUJTWlhCc1lXTmxjeUJqYjI1emIyeGxMbmRoY201Y2JpQWdJQ0FnS2x4dUlDQWdJQ0FxSUVCd1lYSmhiWE1nZXlwOUlHRnlaM1Z0Wlc1MGN5QkJibmtnWkdGMFlTQjBhR0YwSUhOb2IzVnNaQ0JpWlNCemFHOTNiaUJwYmlCMGFHVWdZMjl1YzI5c1pTQnpkR0YwWlcxbGJuUXVYRzRnSUNBZ0lDb3ZYRzRnSUNBZ1pYaHdiM0owY3k1M1lYSnVJRDBnWm5WdVkzUnBiMjRnS0NrZ2UxeHVJQ0FnSUNBZ0lDQmZaWGhsWTNWMFpTaFVXVkJGWDFkQlVrNHNJR0Z5WjNWdFpXNTBjeWs3WEc0Z0lDQWdmVHRjYmx4dUlDQWdJQzhxS2x4dUlDQWdJQ0FxSUZKbGNHeGhZMlZ6SUdOdmJuTnZiR1V1WlhKeWIzSmNiaUFnSUNBZ0tseHVJQ0FnSUNBcUlFQndZWEpoYlNCN0tuMGdZWEpuZFcxbGJuUnpJRUZ1ZVNCa1lYUmhJSFJvWVhRZ2MyaHZkV3hrSUdKbElITm9iM2R1SUdsdUlIUm9aU0JqYjI1emIyeGxJSE4wWVhSbGJXVnVkQzVjYmlBZ0lDQWdLaTljYmlBZ0lDQmxlSEJ2Y25SekxtVnljbTl5SUQwZ1puVnVZM1JwYjI0Z0tDa2dlMXh1SUNBZ0lDQWdJQ0JmWlhobFkzVjBaU2hVV1ZCRlgwVlNVazlTTENCaGNtZDFiV1Z1ZEhNcE8xeHVJQ0FnSUgwN1hHNWNiaUFnSUNBdktpcGNiaUFnSUNBZ0tpQlNaWEJzWVdObGN5QmhiR1Z5ZEZ4dUlDQWdJQ0FxWEc0Z0lDQWdJQ29nUUhCaGNtRnRJSHNxZlNCaGNtZDFiV1Z1ZEhNZ1FXNTVJR1JoZEdFZ2RHaGhkQ0J6YUc5MWJHUWdZbVVnYzJodmQyNGdhVzRnZEdobElHTnZibk52YkdVZ2MzUmhkR1Z0Wlc1MExseHVJQ0FnSUNBcUwxeHVJQ0FnSUdWNGNHOXlkSE11WVd4bGNuUWdQU0JtZFc1amRHbHZiaUFvS1NCN1hHNGdJQ0FnSUNBZ0lGOWxlR1ZqZFhSbEtGUlpVRVZmUVV4RlVsUXNJR0Z5WjNWdFpXNTBjeWs3WEc0Z0lDQWdmVHRjYmx4dUlDQWdJQzhxS2x4dUlDQWdJQ0FxSUVSbFluVm5JR2x1Wm04Z1ptOXlJRzF2WW1sc1pTQmtaWFpwWTJWekxseHVJQ0FnSUNBcVhHNGdJQ0FnSUNvZ1FIQmhjbUZ0SUhzcWZTQmhjbWQxYldWdWRITWdRVzU1SUdSaGRHRWdkR2hoZENCemFHOTFiR1FnWW1VZ2MyaHZkMjRnYVc0Z2RHaGxJR052Ym5OdmJHVWdjM1JoZEdWdFpXNTBMbHh1SUNBZ0lDQXFMMXh1SUNBZ0lHVjRjRzl5ZEhNdWJXOWlhV3hsSUQwZ1puVnVZM1JwYjI0Z0tDa2dlMXh1SUNBZ0lDQWdJQ0JmWlhobFkzVjBaU2hVV1ZCRlgwMVBRa2xNUlN3Z1lYSm5kVzFsYm5SektUdGNiaUFnSUNCOU8xeHVYRzU5S0dwelpTNWpiM0psTG1SbFluVm5LU2s3WEc0aUxDSXZLaUF0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMVnh1SUdWdVoybHVaUzVxY3lBeU1ERTJMVEE1TFRBNFhHNGdSMkZ0WW1sdklFZHRZa2hjYmlCb2RIUndPaTh2ZDNkM0xtZGhiV0pwYnk1a1pWeHVJRU52Y0hseWFXZG9kQ0FvWXlrZ01qQXhOaUJIWVcxaWFXOGdSMjFpU0Z4dUlGSmxiR1ZoYzJWa0lIVnVaR1Z5SUhSb1pTQkhUbFVnUjJWdVpYSmhiQ0JRZFdKc2FXTWdUR2xqWlc1elpTQW9WbVZ5YzJsdmJpQXlLVnh1SUZ0b2RIUndPaTh2ZDNkM0xtZHVkUzV2Y21jdmJHbGpaVzV6WlhNdlozQnNMVEl1TUM1b2RHMXNYVnh1SUMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0WEc0Z0tpOWNibHh1YW5ObExtTnZjbVV1Wlc1bmFXNWxJRDBnYW5ObExtTnZjbVV1Wlc1bmFXNWxJSHg4SUh0OU8xeHVYRzR2S2lwY2JpQXFJRXBUUlNCRGIzSmxJRTF2WkhWc1pWeHVJQ3BjYmlBcUlGUm9hWE1nYjJKcVpXTjBJSGRwYkd3Z2FXNXBkR2xoYkdsNlpTQjBhR1VnY0dGblpTQnVZVzFsYzNCaFkyVnpJR0Z1WkNCamIyeHNaV04wYVc5dWN5NWNiaUFxWEc0Z0tpQkFiVzlrZFd4bElFcFRSUzlEYjNKbEwyVnVaMmx1WlZ4dUlDb3ZYRzRvWm5WdVkzUnBiMjRnS0dWNGNHOXlkSE1wSUh0Y2JseHVJQ0FnSUNkMWMyVWdjM1J5YVdOMEp6dGNibHh1SUNBZ0lDOHZJQzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMVnh1SUNBZ0lDOHZJRkJTU1ZaQlZFVWdSbFZPUTFSSlQwNVRYRzRnSUNBZ0x5OGdMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRYRzVjYmlBZ0lDQXZLaXBjYmlBZ0lDQWdLaUJKYm1sMGFXRnNhWHBsSUhSb1pTQndZV2RsSUc1aGJXVnpjR0ZqWlhNdVhHNGdJQ0FnSUNwY2JpQWdJQ0FnS2lCVWFHbHpJRzFsZEdodlpDQjNhV3hzSUhObFlYSmphQ0IwYUdVZ2NHRm5aU0JJVkUxTUlHWnZjaUJoZG1GcGJHRmliR1VnYm1GdFpYTndZV05sY3k1Y2JpQWdJQ0FnS2x4dUlDQWdJQ0FxSUVCd1lYSmhiU0I3UVhKeVlYbDlJR052Ykd4bFkzUnBiMjV6SUVOdmJuUmhhVzV6SUhSb1pTQnRiMlIxYkdVZ1kyOXNiR1ZqZEdsdmJpQnBibk4wWVc1alpYTWdkRzhnWW1VZ2FXNWpiSFZrWldRZ2FXNGdkR2hsSUc1aGJXVnpjR0ZqWlhNdVhHNGdJQ0FnSUNwY2JpQWdJQ0FnS2lCQWNtVjBkWEp1SUh0QmNuSmhlWDBnVW1WMGRYSnVjeUJoYmlCaGNuSmhlU0IzYVhSb0lIUm9aU0J3WVdkbElHNWhiV1Z6Y0dGalpTQnVZVzFsY3k1Y2JpQWdJQ0FnS2x4dUlDQWdJQ0FxSUVCd2NtbDJZWFJsWEc0Z0lDQWdJQ292WEc0Z0lDQWdablZ1WTNScGIyNGdYMmx1YVhST1lXMWxjM0JoWTJWektHTnZiR3hsWTNScGIyNXpLU0I3WEc0Z0lDQWdJQ0FnSUd4bGRDQndZV2RsVG1GdFpYTndZV05sVG1GdFpYTWdQU0JiWFR0Y2JseHVJQ0FnSUNBZ0lDQXZMeUJWYzJVZ2RHaGxJR04xYzNSdmJTQndjMlYxWkc4Z2MyVnNaV04wYjNJZ1pHVm1hVzVsWkNCaGRDQmxlSFJsYm1RdWFuTWdhVzRnYjNKa1pYSWdkRzhnWm1WMFkyZ2dkR2hsSUdGMllXbHNZV0pzWlNCdVlXMWxjM0JoWTJWekxseHVJQ0FnSUNBZ0lDQnNaWFFnYm05a1pYTWdQU0JCY25KaGVTNW1jbTl0S0dSdlkzVnRaVzUwTG1kbGRFVnNaVzFsYm5SelFubFVZV2RPWVcxbEtDY3FKeWtwTEZ4dUlDQWdJQ0FnSUNBZ0lDQWdjbVZuWlhnZ1BTQXZaR0YwWVMwb0xpb3BMVzVoYldWemNHRmpaUzg3WEc1Y2JpQWdJQ0FnSUNBZ1ptOXlJQ2hzWlhRZ2JtOWtaU0J2WmlCdWIyUmxjeWtnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdabTl5SUNoc1pYUWdZWFIwY21saWRYUmxJRzltSUVGeWNtRjVMbVp5YjIwb2JtOWtaUzVoZEhSeWFXSjFkR1Z6S1NrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2hoZEhSeWFXSjFkR1V1Ym1GdFpTNXpaV0Z5WTJnb2NtVm5aWGdwSUNFOVBTQXRNU2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJRWVhKelpTQjBhR1VnYm1GdFpYTndZV05sSUc1aGJXVWdZVzVrSUhOdmRYSmpaU0JWVWt3dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR3hsZENCdVlXMWxJRDBnWVhSMGNtbGlkWFJsTG01aGJXVXVjbVZ3YkdGalpTaHlaV2RsZUN3Z0p5UXhKeWtzWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J6YjNWeVkyVWdQU0JoZEhSeWFXSjFkR1V1ZG1Gc2RXVTdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdRMmhsWTJzZ2FXWWdkR2hsSUc1aGJXVnpjR0ZqWlNCcGN5QmhiSEpsWVdSNUlHUmxabWx1WldRdVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtSUNod1lXZGxUbUZ0WlhOd1lXTmxUbUZ0WlhNdWFXNWtaWGhQWmlodVlXMWxLU0ErSUMweEtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaUFvZDJsdVpHOTNXMjVoYldWZExuTnZkWEpqWlNBaFBUMGdjMjkxY21ObEtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYW5ObExtTnZjbVV1WkdWaWRXY3VaWEp5YjNJb1lFVnNaVzFsYm5RZ2QybDBhQ0IwYUdVZ1pIVndiR2xqWVhSbElHNWhiV1Z6Y0dGalpTQnVZVzFsT2lBa2UyNXZaR1Y5WUNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkR2h5YjNjZ2JtVjNJRVZ5Y205eUtHQlVhR1VnYm1GdFpYTndZV05sSUZ3aUpIdHVZVzFsZlZ3aUlHbHpJR0ZzY21WaFpIa2daR1ZtYVc1bFpDNGdVR3hsWVhObElITmxiR1ZqZENCaGJtOTBhR1Z5SUdBZ0sxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCZ2JtRnRaU0JtYjNJZ2VXOTFjaUJ1WVcxbGMzQmhZMlV1WUNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCamIyNTBhVzUxWlRzZ0x5OGdWR2hsSUc1aGJXVnpjR0ZqWlNCcGN5QmhiSEpsWVdSNUlHUmxabWx1WldRc0lHTnZiblJwYm5WbElHeHZiM0F1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9jMjkxY21ObElEMDlQU0FuSnlrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RHaHliM2NnYm1WM0lGTjViblJoZUVWeWNtOXlLR0JPWVcxbGMzQmhZMlVnYzI5MWNtTmxJR2x6SUdWdGNIUjVPaUFrZTI1aGJXVjlZQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QkRjbVZoZEdVZ1lTQnVaWGNnYm1GdFpYTndZV05sY3lCcGJuTjBZVzVqWlNCcGJpQjBhR1VnWjJ4dlltRnNJSE5qYjNCbElDaDBhR1VnWjJ4dlltRnNJSE5qYjNCbElHbHpJSFZ6WldRZ1ptOXlJRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5Qm1ZV3hzWW1GamF5QnpkWEJ3YjNKMElHOW1JRzlzWkNCdGIyUjFiR1VnWkdWbWFXNXBkR2x2Ym5NcExseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb2JtRnRaU0E5UFQwZ0oycHpaU2NwSUhzZ0x5OGdUVzlrYVdaNUlIUm9aU0JsYm1kcGJtVWdiMkpxWldOMElIZHBkR2dnVG1GdFpYTndZV05sSUdGMGRISnBZblYwWlhNdVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCZlkyOXVkbVZ5ZEVWdVoybHVaVlJ2VG1GdFpYTndZV05sS0hOdmRYSmpaU3dnWTI5c2JHVmpkR2x2Ym5NcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5SUdWc2MyVWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkMmx1Wkc5M1cyNWhiV1ZkSUQwZ2JtVjNJR3B6WlM1amIyNXpkSEoxWTNSdmNuTXVUbUZ0WlhOd1lXTmxLRzVoYldVc0lITnZkWEpqWlN3Z1kyOXNiR1ZqZEdsdmJuTXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnY0dGblpVNWhiV1Z6Y0dGalpVNWhiV1Z6TG5CMWMyZ29ibUZ0WlNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJRzV2WkdVdWNtVnRiM1psUVhSMGNtbGlkWFJsS0dGMGRISnBZblYwWlM1dVlXMWxLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNBdkx5QlVhSEp2ZHlCaGJpQmxjbkp2Y2lCcFppQnVieUJ1WVcxbGMzQmhZMlZ6SUhkbGNtVWdabTkxYm1RdVhHNGdJQ0FnSUNBZ0lHbG1JQ2h3WVdkbFRtRnRaWE53WVdObFRtRnRaWE11YkdWdVozUm9JRDA5UFNBd0tTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCMGFISnZkeUJ1WlhjZ1JYSnliM0lvSjA1dklHMXZaSFZzWlNCdVlXMWxjM0JoWTJWeklIZGxjbVVnWm05MWJtUXNJSGRwZEdodmRYUWdibUZ0WlhOd1lXTmxjeUJwZENCcGN5QnViM1FnY0c5emMybGliR1VnZEc4Z0p5QXJYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdKMnh2WVdRZ1lXNTVJRzF2WkhWc1pYTXVKeWs3WEc0Z0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQXZMeUJKYm1sMGFXRnNhWHBsSUhSb1pTQnVZVzFsYzNCaFkyVWdhVzV6ZEdGdVkyVnpMbHh1SUNBZ0lDQWdJQ0JzWlhRZ1pHVm1aWEp5WldSRGIyeHNaV04wYVc5dUlEMGdXMTA3WEc1Y2JpQWdJQ0FnSUNBZ1ptOXlJQ2hzWlhRZ2JtRnRaU0J2WmlCd1lXZGxUbUZ0WlhOd1lXTmxUbUZ0WlhNcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUd4bGRDQmtaV1psY25KbFpDQTlJQ1F1UkdWbVpYSnlaV1FvS1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnWkdWbVpYSnlaV1JEYjJ4c1pXTjBhVzl1TG5CMWMyZ29aR1ZtWlhKeVpXUXBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQjNhVzVrYjNkYmJtRnRaVjFjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F1YVc1cGRDZ3BYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMbVJ2Ym1Vb1pHVm1aWEp5WldRdWNtVnpiMngyWlNsY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdVptRnBiQ2hrWldabGNuSmxaQzV5WldwbFkzUXBYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMbUZzZDJGNWN5Z29LU0E5UGlCcWMyVXVZMjl5WlM1a1pXSjFaeTVwYm1adktDZE9ZVzFsYzNCaFkyVWdjSEp2YldselpYTWdkMlZ5WlNCeVpYTnZiSFpsWkRvZ0p5d2dibUZ0WlNrcE8xeHVJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnTHk4Z1ZISnBaMmRsY2lCaGJpQmxkbVZ1ZENCaFpuUmxjaUIwYUdVZ1pXNW5hVzVsSUdoaGN5QnBibWwwYVdGc2FYcGxaQ0JoYkd3Z2JtVjNJRzF2WkhWc1pYTXVYRzRnSUNBZ0lDQWdJQ1F1ZDJobGJpNWhjSEJzZVNoMWJtUmxabWx1WldRc0lHUmxabVZ5Y21Wa1EyOXNiR1ZqZEdsdmJpa3VZV3gzWVhsektHWjFibU4wYVc5dUlDZ3BJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lHeGxkQ0JsZG1WdWRDQTlJR1J2WTNWdFpXNTBMbU55WldGMFpVVjJaVzUwS0NkRmRtVnVkQ2NwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdaWFpsYm5RdWFXNXBkRVYyWlc1MEtDZEtVMFZPUjBsT1JWOUpUa2xVWDBaSlRrbFRTRVZFSnl3Z2RISjFaU3dnZEhKMVpTazdYRzRnSUNBZ0lDQWdJQ0FnSUNCa2IyTjFiV1Z1ZEM1eGRXVnllVk5sYkdWamRHOXlLQ2RpYjJSNUp5a3VaR2x6Y0dGMFkyaEZkbVZ1ZENobGRtVnVkQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQnFjMlV1WTI5eVpTNXlaV2RwYzNSeWVTNXpaWFFvSjJwelpVVnVaRlJwYldVbkxDQnVaWGNnUkdGMFpTZ3BMbWRsZEZScGJXVW9LU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQnFjMlV1WTI5eVpTNWtaV0oxWnk1cGJtWnZLQ2RLVXlCRmJtZHBibVVnVEc5aFpHbHVaeUJVYVcxbE9pQW5MQ0JxYzJVdVkyOXlaUzV5WldkcGMzUnllUzVuWlhRb0oycHpaVVZ1WkZScGJXVW5LVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzBnYW5ObExtTnZjbVV1Y21WbmFYTjBjbmt1WjJWMEtDZHFjMlZUZEdGeWRGUnBiV1VuS1N3Z0oyMXpKeWs3WEc0Z0lDQWdJQ0FnSUNBZ0lDQnBaaUFvZDJsdVpHOTNMa041Y0hKbGMzTXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IzYVc1a2IzY3Vhbk5sVW1WaFpIa2dQU0IwY25WbE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNCOUtUdGNibHh1SUNBZ0lDQWdJQ0J5WlhSMWNtNGdjR0ZuWlU1aGJXVnpjR0ZqWlU1aGJXVnpPMXh1SUNBZ0lIMWNibHh1SUNBZ0lDOHFLbHh1SUNBZ0lDQXFJRU52Ym5abGNuUWdkR2hsSUZ3aWFuTmxYQ0lnYjJKcVpXTjBJSFJ2SUdFZ1RtRnRaWE53WVdObElHTnZiWEJoZEdsaWJHVWdiMkpxWldOMExseHVJQ0FnSUNBcVhHNGdJQ0FnSUNvZ1NXNGdiM0prWlhJZ2RHOGdjM1Z3Y0c5eWRDQjBhR1VnWENKcWMyVmNJaUJ1WVcxbGMzQmhZMlVnYm1GdFpTQm1iM0lnZEdobElHTnZjbVVnYlc5a2RXeGxjeUJ3YkdGalpXUWdhVzRnZEdobElGd2lTbE5GYm1kcGJtVmNJbHh1SUNBZ0lDQXFJR1JwY21WamRHOXllU3dnZDJVZ2QybHNiQ0J1WldWa0lIUnZJRzF2WkdsbWVTQjBhR1VnWVd4eVpXRmtlU0JsZUdsemRHbHVaeUJjSW1welpWd2lJRzlpYW1WamRDQnpieUIwYUdGMElHbDBJR05oYmlCdmNHVnlZWFJsWEc0Z0lDQWdJQ29nWVhNZ1lTQnVZVzFsYzNCaFkyVWdkMmwwYUc5MWRDQnNiM05wYm1jZ2FYUnpJR2x1YVhScFlXd2dZWFIwY21saWRYUmxjeTVjYmlBZ0lDQWdLbHh1SUNBZ0lDQXFJRUJ3WVhKaGJTQjdVM1J5YVc1bmZTQnpiM1Z5WTJVZ1RtRnRaWE53WVdObElITnZkWEpqWlNCd1lYUm9JR1p2Y2lCMGFHVWdiVzlrZFd4bElHWnBiR1Z6TGx4dUlDQWdJQ0FxSUVCd1lYSmhiU0I3UVhKeVlYbDlJR052Ykd4bFkzUnBiMjV6SUVOdmJuUmhhVzRnYVc1emRHRnVZMlZ6SUhSdklIUm9aU0J3Y205MGIzUjVjR1VnWTI5c2JHVmpkR2x2YmlCcGJuTjBZVzVqWlhNdVhHNGdJQ0FnSUNwY2JpQWdJQ0FnS2lCQWNISnBkbUYwWlZ4dUlDQWdJQ0FxTDF4dUlDQWdJR1oxYm1OMGFXOXVJRjlqYjI1MlpYSjBSVzVuYVc1bFZHOU9ZVzFsYzNCaFkyVW9jMjkxY21ObExDQmpiMnhzWldOMGFXOXVjeWtnZTF4dUlDQWdJQ0FnSUNCc1pYUWdkRzF3VG1GdFpYTndZV05sSUQwZ2JtVjNJR3B6WlM1amIyNXpkSEoxWTNSdmNuTXVUbUZ0WlhOd1lXTmxLQ2RxYzJVbkxDQnpiM1Z5WTJVc0lHTnZiR3hsWTNScGIyNXpLVHRjYmlBZ0lDQWdJQ0FnYW5ObExtNWhiV1VnUFNCMGJYQk9ZVzFsYzNCaFkyVXVibUZ0WlR0Y2JpQWdJQ0FnSUNBZ2FuTmxMbk52ZFhKalpTQTlJSFJ0Y0U1aGJXVnpjR0ZqWlM1emIzVnlZMlU3WEc0Z0lDQWdJQ0FnSUdwelpTNWpiMnhzWldOMGFXOXVjeUE5SUhSdGNFNWhiV1Z6Y0dGalpTNWpiMnhzWldOMGFXOXVjenRjYmlBZ0lDQWdJQ0FnYW5ObExtbHVhWFFnUFNCcWMyVXVZMjl1YzNSeWRXTjBiM0p6TGs1aGJXVnpjR0ZqWlM1d2NtOTBiM1I1Y0dVdWFXNXBkRHRjYmlBZ0lDQjlYRzVjYmlBZ0lDQXZMeUF0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzFjYmlBZ0lDQXZMeUJRVlVKTVNVTWdSbFZPUTFSSlQwNVRYRzRnSUNBZ0x5OGdMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRYRzVjYmlBZ0lDQXZLaXBjYmlBZ0lDQWdLaUJKYm1sMGFXRnNhWHBsSUhSb1pTQmxibWRwYm1VdVhHNGdJQ0FnSUNwY2JpQWdJQ0FnS2lCQWNHRnlZVzBnZTBGeWNtRjVmU0JqYjJ4c1pXTjBhVzl1Y3lCRGIyNTBZV2x1Y3lCMGFHVWdjM1Z3Y0c5eWRHVmtJRzF2WkhWc1pTQmpiMnhzWldOMGFXOXVJR1JoZEdFdVhHNGdJQ0FnSUNvdlhHNGdJQ0FnWlhod2IzSjBjeTVwYm1sMElEMGdablZ1WTNScGIyNGdLR052Ykd4bFkzUnBiMjV6S1NCN1hHNGdJQ0FnSUNBZ0lDOHZJRWRzYjJKaGJDQmxjbkp2Y2lCb1lXNWtiR1Z5SUhSb1lYUWdaWGhsWTNWMFpYTWdhV1lnWVc0Z2RXNWpZWFZuYUhRZ1NsTWdaWEp5YjNJZ2IyTmpkWEp6SUc5dUlIQmhaMlV1WEc0Z0lDQWdJQ0FnSUdwelpTNWpiM0psTG1SbFluVm5MbUpwYm1SSGJHOWlZV3hGY25KdmNraGhibVJzWlhJb0tUdGNibHh1SUNBZ0lDQWdJQ0F2THlCSmJtbDBhV0ZzYVhwbElIUm9aU0J3WVdkbElHNWhiV1Z6Y0dGalpYTXVYRzRnSUNBZ0lDQWdJR3hsZENCd1lXZGxUbUZ0WlhOd1lXTmxUbUZ0WlhNZ1BTQmZhVzVwZEU1aGJXVnpjR0ZqWlhNb1kyOXNiR1ZqZEdsdmJuTXBPMXh1WEc0Z0lDQWdJQ0FnSUM4dklFeHZaeUIwYUdVZ2NHRm5aU0J1WVcxbGMzQmhZMlZ6SUNobWIzSWdaR1ZpZFdkbmFXNW5JRzl1YkhrcExseHVJQ0FnSUNBZ0lDQnFjMlV1WTI5eVpTNWtaV0oxWnk1cGJtWnZLQ2RRWVdkbElFNWhiV1Z6Y0dGalpYTTZJQ2NnS3lCd1lXZGxUbUZ0WlhOd1lXTmxUbUZ0WlhNdWFtOXBiaWdwS1R0Y2JseHVJQ0FnSUNBZ0lDQXZMeUJWY0dSaGRHVWdkR2hsSUdWdVoybHVaU0J5WldkcGMzUnllUzVjYmlBZ0lDQWdJQ0FnYW5ObExtTnZjbVV1Y21WbmFYTjBjbmt1YzJWMEtDZHVZVzFsYzNCaFkyVnpKeXdnY0dGblpVNWhiV1Z6Y0dGalpVNWhiV1Z6S1R0Y2JpQWdJQ0I5TzF4dVhHNTlLU2hxYzJVdVkyOXlaUzVsYm1kcGJtVXBPMXh1SWl3aUx5b2dMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMWNiaUJsZUhSbGJuTnBiMjV6TG1weklESXdNVGN0TURNdE1ETmNiaUJIWVcxaWFXOGdSMjFpU0Z4dUlHaDBkSEE2THk5M2QzY3VaMkZ0WW1sdkxtUmxYRzRnUTI5d2VYSnBaMmgwSUNoaktTQXlNREUzSUVkaGJXSnBieUJIYldKSVhHNGdVbVZzWldGelpXUWdkVzVrWlhJZ2RHaGxJRWRPVlNCSFpXNWxjbUZzSUZCMVlteHBZeUJNYVdObGJuTmxJQ2hXWlhKemFXOXVJRElwWEc0Z1cyaDBkSEE2THk5M2QzY3VaMjUxTG05eVp5OXNhV05sYm5ObGN5OW5jR3d0TWk0d0xtaDBiV3hkWEc0Z0xTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMxY2JpQXFMMXh1WEc0dktpcGNiaUFxSUVwVFJTQkZlSFJsYm5OcGIyNXpYRzRnS2x4dUlDb2dSWGgwWlc1a0lIUm9aU0JrWldaaGRXeDBJR0psYUdGMmFXOTFjaUJ2WmlCbGJtZHBibVVnWTI5dGNHOXVaVzUwY3lCdmNpQmxlSFJsY201aGJDQndiSFZuYVc1eklHSmxabTl5WlNCMGFHVjVJR0Z5WlNCc2IyRmtaV1F1WEc0Z0tseHVJQ29nUUcxdlpIVnNaU0JLVTBVdlEyOXlaUzlsZUhSbGJtUmNiaUFxTDF4dUtHWjFibU4wYVc5dUlDZ3BJSHRjYmx4dUlDQWdJQ2QxYzJVZ2MzUnlhV04wSnp0Y2JseHVJQ0FnSUM4dklDMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExWeHVJQ0FnSUM4dklGQkJVbE5GSUUxUFJGVk1SU0JFUVZSQklFcFJWVVZTV1NCRldGUkZUbE5KVDA1Y2JpQWdJQ0F2THlBdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMxY2JseHVJQ0FnSUNRdVptNHVaWGgwWlc1a0tIdGNiaUFnSUNBZ0lDQWdjR0Z5YzJWTmIyUjFiR1ZFWVhSaE9pQm1kVzVqZEdsdmJpQW9iVzlrZFd4bFRtRnRaU2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdhV1lnS0NGdGIyUjFiR1ZPWVcxbElIeDhJRzF2WkhWc1pVNWhiV1VnUFQwOUlDY25LU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZEdoeWIzY2dibVYzSUVWeWNtOXlLQ2ROYjJSMWJHVWdibUZ0WlNCM1lYTWdibTkwSUhCeWIzWnBaR1ZrSUdGeklHRnVJR0Z5WjNWdFpXNTBMaWNwWEc0Z0lDQWdJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lHTnZibk4wSUdsdWFYUnBZV3hFWVhSaElEMGdKQ2gwYUdsektTNWtZWFJoS0NrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0JqYjI1emRDQm1hV3gwWlhKbFpFUmhkR0VnUFNCN2ZUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0x5OGdVMlZoY21Ob1pYTWdabTl5SUcxdlpIVnNaU0J5Wld4bGRtRnVkQ0JrWVhSaElHbHVjMmxrWlNCMGFHVWdiV0ZwYmkxa1lYUmhMVzlpYW1WamRDNGdSR0YwWVNCbWIzSWdiM1JvWlhJZ2QybGtaMlYwY3lCM2FXeHNJRzV2ZENCblpYUWdYRzRnSUNBZ0lDQWdJQ0FnSUNBdkx5QndZWE56WldRZ2RHOGdkR2hwY3lCM2FXUm5aWFF1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWtMbVZoWTJnb2FXNXBkR2xoYkVSaGRHRXNJR1oxYm1OMGFXOXVJQ2hyWlhrc0lIWmhiSFZsS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWWdLR3RsZVM1cGJtUmxlRTltS0cxdlpIVnNaVTVoYldVcElEMDlQU0F3SUh4OElHdGxlUzVwYm1SbGVFOW1LRzF2WkhWc1pVNWhiV1V1ZEc5TWIzZGxja05oYzJVb0tTa2dQVDA5SURBcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYkdWMElHNWxkMHRsZVNBOUlHdGxlUzV6ZFdKemRISW9iVzlrZFd4bFRtRnRaUzVzWlc1bmRHZ3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCdVpYZExaWGtnUFNCdVpYZExaWGt1YzNWaWMzUnlLREFzSURFcExuUnZURzkzWlhKRFlYTmxLQ2tnS3lCdVpYZExaWGt1YzNWaWMzUnlLREVwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQm1hV3gwWlhKbFpFUmhkR0ZiYm1WM1MyVjVYU0E5SUhaaGJIVmxPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lIMHBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQnlaWFIxY200Z1ptbHNkR1Z5WldSRVlYUmhPMXh1SUNBZ0lDQWdJQ0I5WEc0Z0lDQWdmU2s3WEc1Y2JpQWdJQ0F2THlBdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMxY2JpQWdJQ0F2THlCRVFWUkZVRWxEUzBWU0lGSkZSMGxQVGtGTUlFbE9SazljYmlBZ0lDQXZMeUF0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzFjYmx4dUlDQWdJR2xtSUNna0xtUmhkR1Z3YVdOclpYSWdJVDA5SUhWdVpHVm1hVzVsWkNrZ2UxeHVJQ0FnSUNBZ0lDQWtMbVJoZEdWd2FXTnJaWEl1Y21WbmFXOXVZV3d1WkdVZ1BTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCa1lYUmxSbTl5YldGME9pQW5aR1F1YlcwdWVYa25MRnh1SUNBZ0lDQWdJQ0FnSUNBZ1ptbHljM1JFWVhrNklERXNYRzRnSUNBZ0lDQWdJQ0FnSUNCcGMxSlVURG9nWm1Gc2MyVmNiaUFnSUNBZ0lDQWdmVHRjYmlBZ0lDQWdJQ0FnSkM1a1lYUmxjR2xqYTJWeUxuTmxkRVJsWm1GMWJIUnpLQ1F1WkdGMFpYQnBZMnRsY2k1eVpXZHBiMjVoYkM1a1pTazdYRzRnSUNBZ2ZWeHVmU2dwS1R0Y2JpSXNJaThxSUMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0WEc0Z2FXNXBkR2xoYkdsNlpTNXFjeUF5TURFMkxUQTVMVEE0WEc0Z1IyRnRZbWx2SUVkdFlraGNiaUJvZEhSd09pOHZkM2QzTG1kaGJXSnBieTVrWlZ4dUlFTnZjSGx5YVdkb2RDQW9ZeWtnTWpBeE5pQkhZVzFpYVc4Z1IyMWlTRnh1SUZKbGJHVmhjMlZrSUhWdVpHVnlJSFJvWlNCSFRsVWdSMlZ1WlhKaGJDQlFkV0pzYVdNZ1RHbGpaVzV6WlNBb1ZtVnljMmx2YmlBeUtWeHVJRnRvZEhSd09pOHZkM2QzTG1kdWRTNXZjbWN2YkdsalpXNXpaWE12WjNCc0xUSXVNQzVvZEcxc1hWeHVJQzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRYRzRnS2k5Y2JseHVKM1Z6WlNCemRISnBZM1FuTzF4dVhHNHZLaXBjYmlBcUlFcFRSU0JKYm1sMGFXRnNhWHBoZEdsdmJpQk5iMlIxYkdWY2JpQXFYRzRnS2lCVWFHVWdaRzlqZFcxbGJuUXRjbVZoWkhrZ1pYWmxiblFnYjJZZ2RHaGxJSEJoWjJVZ2QybHNiQ0IwY21sbloyVnlJSFJvWlNCS1lYWmhVMk55YVhCMElFVnVaMmx1WlNCcGJtbDBhV0ZzYVhwaGRHbHZiaTRnVkdobFhHNGdLaUJsYm1kcGJtVWdjbVZ4ZFdseVpYTWdZU0JuYkc5aVlXd2dZMjl1Wm1sbmRYSmhkR2x2YmlCdlltcGxZM1FnWENKM2FXNWtiM2N1U2xORmJtZHBibVZEYjI1bWFXZDFjbUYwYVc5dVhDSWdkRzhnWW1VZ2NISmxMV1JsWm1sdVpXUmNiaUFxSUdsdUlHOXlaR1Z5SUhSdklISmxkSEpwWlhabElIUm9aU0JpWVhOcFl5QmpiMjVtYVdkMWNtRjBhVzl1SUdsdVptOHVJRUZtZEdWeUlHRWdjM1ZqWTJWemMyWjFiQ0JwYm1sMGFXRnNhWHBoZEdsdmJpQjBhR2x6SUc5aWFtVmpkRnh1SUNvZ2FYTWdjbVZ0YjNabFpDQm1jbTl0SUhSb1pTQjNhVzVrYjNjZ2IySnFaV04wTGx4dUlDcGNiaUFxSUNNakl5QkRiMjVtYVdkMWNtRjBhVzl1SUZOaGJYQnNaVnh1SUNwY2JpQXFJR0JnWUdwelhHNGdLaUIzYVc1a2IzY3VTbE5GYm1kcGJtVkRiMjVtYVdkMWNtRjBhVzl1SUQwZ2UxeHVJQ29nSUNCbGJuWnBjbTl1YldWdWREb2dKM0J5YjJSMVkzUnBiMjRuTEZ4dUlDb2dJQ0JoY0hCVmNtdzZJQ2RvZEhSd09pOHZZWEJ3TG1OdmJTY3NYRzRnS2lBZ0lHTnZiR3hsWTNScGIyNXpPaUJiWEc0Z0tpQWdJQ0FnZTI1aGJXVTZJQ2RqYjI1MGNtOXNiR1Z5Y3ljc0lHRjBkSEpwWW5WMFpUb2dKMk52Ym5SeWIyeHNaWEluZlZ4dUlDb2dJQ0JkTENBZ1hHNGdLaUFnSUhSeVlXNXpiR0YwYVc5dWN6b2dlMXh1SUNvZ0lDQWdJQ2R6WldOMGFXOXVUbUZ0WlNjNklIc2dKM1J5WVc1emJHRjBhVzl1UzJWNUp6b2dKM1J5WVc1emJHRjBhVzl1Vm1Gc2RXVW5JSDBzWEc0Z0tpQWdJQ0FnSjJGdWIzUm9aWEpUWldOMGFXOXVKem9nZXlBdUxpNGdmVnh1SUNvZ0lDQjlMRnh1SUNvZ0lDQnNZVzVuZFdGblpVTnZaR1U2SUNkbGJpY3NYRzRnS2lBZ0lIQmhaMlZVYjJ0bGJqb2dKemxoYzJRM1pqazROemx6WkRobU56bHpPVGh6TjJRNU9HWW5YRzRnS2lCOU8xeHVJQ29nWUdCZ1hHNGdLbHh1SUNvZ1FHMXZaSFZzWlNCS1UwVXZRMjl5WlM5cGJtbDBhV0ZzYVhwbFhHNGdLaTljYmx4dUx5OGdTVzVwZEdsaGJHbDZaU0JpWVhObElHVnVaMmx1WlNCdlltcGxZM1F1SUVWMlpYSjVJRzkwYUdWeUlIQmhjblFnYjJZZ2RHaGxJR1Z1WjJsdVpTQjNhV3hzSUhKbFptVnlJSFJ2SUhSb2FYTmNiaTh2SUdObGJuUnlZV3dnYjJKcVpXTjBJR1p2Y2lCMGFHVWdZMjl5WlNCdmNHVnlZWFJwYjI1ekxseHVkMmx1Wkc5M0xtcHpaU0E5SUh0Y2JpQWdJQ0JqYjNKbE9pQjdmU3hjYmlBZ0lDQnNhV0p6T2lCN2ZTeGNiaUFnSUNCamIyNXpkSEoxWTNSdmNuTTZJSHQ5WEc1OU8xeHVYRzR2THlCSmJtbDBhV0ZzYVhwbElIUm9aU0JsYm1kcGJtVWdiMjRnZDJsdVpHOTNJR3h2WVdRdUlGeHVaRzlqZFcxbGJuUXVZV1JrUlhabGJuUk1hWE4wWlc1bGNpZ25SRTlOUTI5dWRHVnVkRXh2WVdSbFpDY3NJR1oxYm1OMGFXOXVJQ2dwSUh0Y2JpQWdJQ0IwY25rZ2UxeHVJQ0FnSUNBZ0lDQXZMeUJEYUdWamF5QnBaaUJuYkc5aVlXd2dTbE5GYm1kcGJtVkRiMjVtYVdkMWNtRjBhVzl1SUc5aWFtVmpkQ0JwY3lCa1pXWnBibVZrTGx4dUlDQWdJQ0FnSUNCcFppQW9kMmx1Wkc5M0xrcFRSVzVuYVc1bFEyOXVabWxuZFhKaGRHbHZiaUE5UFQwZ2RXNWtaV1pwYm1Wa0tTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCMGFISnZkeUJ1WlhjZ1JYSnliM0lvSjFSb1pTQmNJbmRwYm1SdmR5NUtVMFZ1WjJsdVpVTnZibVpwWjNWeVlYUnBiMjVjSWlCdlltcGxZM1FnYVhNZ2JtOTBJR1JsWm1sdVpXUWdhVzRnZEdobElHZHNiMkpoYkNCelkyOXdaUzRnSnlBclhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0oxUm9hWE1nYjJKcVpXTjBJR2x6SUhKbGNYVnBjbVZrSUdKNUlIUm9aU0JsYm1kcGJtVWdkWEJ2YmlCcGRITWdhVzVwZEdsaGJHbDZZWFJwYjI0dUp5azdYRzRnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNBdkx5QlFZWEp6WlNCS1UwVnVaMmx1WlVOdmJtWnBaM1Z5WVhScGIyNGdiMkpxWldOMExseHVJQ0FnSUNBZ0lDQnFjMlV1WTI5eVpTNWpiMjVtYVdjdWFXNXBkQ2gzYVc1a2IzY3VTbE5GYm1kcGJtVkRiMjVtYVdkMWNtRjBhVzl1S1R0Y2JseHVJQ0FnSUNBZ0lDQXZMeUJUZEc5eVpTQjBhR1VnU2xORklITjBZWEowSUhScGJXVWdhVzRnY21WbmFYTjBjbmtnS0hCeWIyWnBiR2x1WnlrdUlGeHVJQ0FnSUNBZ0lDQnFjMlV1WTI5eVpTNXlaV2RwYzNSeWVTNXpaWFFvSjJwelpWTjBZWEowVkdsdFpTY3NJRVJoZEdVdWJtOTNLQ2twTzF4dVhHNGdJQ0FnSUNBZ0lDOHZJRWx1YVhScFlXeHBlbVVnZEdobElHMXZaSFZzWlNCamIyeHNaV04wYVc5dWN5NWNiaUFnSUNBZ0lDQWdhbk5sTG1OdmNtVXVaVzVuYVc1bExtbHVhWFFvYW5ObExtTnZjbVV1WTI5dVptbG5MbWRsZENnblkyOXNiR1ZqZEdsdmJuTW5LU2s3WEc0Z0lDQWdmU0JqWVhSamFDQW9aWGhqWlhCMGFXOXVLU0I3WEc0Z0lDQWdJQ0FnSUdwelpTNWpiM0psTG1SbFluVm5MbVZ5Y205eUtDZFZibVY0Y0dWamRHVmtJR1Z5Y205eUlHUjFjbWx1WnlCS1V5QkZibWRwYm1VZ2FXNXBkR2xoYkdsNllYUnBiMjRoSnl3Z1pYaGpaWEIwYVc5dUtUdGNiaUFnSUNBZ0lDQWdMeThnU1c1bWIzSnRJSFJvWlNCbGJtZHBibVVnWVdKdmRYUWdkR2hsSUdWNFkyVndkR2x2Ymk1Y2JpQWdJQ0FnSUNBZ1kyOXVjM1FnWlhabGJuUWdQU0JrYjJOMWJXVnVkQzVqY21WaGRHVkZkbVZ1ZENnblEzVnpkRzl0UlhabGJuUW5LVHRjYmlBZ0lDQWdJQ0FnWlhabGJuUXVhVzVwZEVOMWMzUnZiVVYyWlc1MEtDZGxjbkp2Y2ljc0lIUnlkV1VzSUhSeWRXVXNJR1Y0WTJWd2RHbHZiaWs3WEc0Z0lDQWdJQ0FnSUhkcGJtUnZkeTVrYVhOd1lYUmphRVYyWlc1MEtHVjJaVzUwS1R0Y2JpQWdJQ0I5WEc1Y2JpQWdJQ0JUVmtkSmJtcGxZM1F1YzJWMFQzQjBhVzl1Y3loN1hHNGdJQ0FnSUNBZ0lHOXVSbUZwYkRvZ1puVnVZM1JwYjI0Z0tHbHRaeXdnYzNabktTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCcGJXY3VZMnhoYzNOTWFYTjBMbkpsYlc5MlpTZ25jM1puTFMxcGJtcGxZM1FuS1RzZ0x5OGdhV1lnYVc1cVpXTjBhVzl1SUdaaGFXeHpJSE5vYjNjZ2RHaGxJR2x0WnlCbGJHVnRaVzUwWEc0Z0lDQWdJQ0FnSUgxY2JpQWdJQ0I5S1R0Y2JpQWdJQ0F2THlCcGJtcGxZM1FnYVcxaFoyVnpJSGRwZEdnZ1lXNGdMbk4yWnlCbWFXeGxJR1Z1WkdsdVoxeHVJQ0FnSUZOV1IwbHVhbVZqZENoa2IyTjFiV1Z1ZEM1eGRXVnllVk5sYkdWamRHOXlRV3hzS0NkcGJXY3VjM1puTFMxcGJtcGxZM1FuS1N3Z2UxeHVJQ0FnSUNBZ0lDQnZia0ZzYkVacGJtbHphRG9nWm5WdVkzUnBiMjRnS0NrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z2RHaGxJRk5XUnlCcGJtcGxZM1JwYjI0Z2FHRnpJR1pwYm1semFHVmtJR1p2Y2lCaGJHd2dkR2h5WldVZ2FXMWhaMlZ6WEc1Y2JpQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUgwcE8xeHVmU2s3SUZ4dUlpd2lMeW9nTFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzFjYmlCc1lXNW5MbXB6SURJd01UWXRNRGd0TWpOY2JpQkhZVzFpYVc4Z1IyMWlTRnh1SUdoMGRIQTZMeTkzZDNjdVoyRnRZbWx2TG1SbFhHNGdRMjl3ZVhKcFoyaDBJQ2hqS1NBeU1ERTJJRWRoYldKcGJ5QkhiV0pJWEc0Z1VtVnNaV0Z6WldRZ2RXNWtaWElnZEdobElFZE9WU0JIWlc1bGNtRnNJRkIxWW14cFl5Qk1hV05sYm5ObElDaFdaWEp6YVc5dUlESXBYRzRnVzJoMGRIQTZMeTkzZDNjdVoyNTFMbTl5Wnk5c2FXTmxibk5sY3k5bmNHd3RNaTR3TG1oMGJXeGRYRzRnTFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzFjYmlBcUwxeHVYRzVxYzJVdVkyOXlaUzVzWVc1bklEMGdhbk5sTG1OdmNtVXViR0Z1WnlCOGZDQjdmVHRjYmx4dUx5b3FYRzRnS2lCS1UwVWdURzlqWVd4cGVtRjBhVzl1SUV4cFluSmhjbmxjYmlBcVhHNGdLaUJVYUdVZ1oyeHZZbUZzSUV4aGJtY2diMkpxWldOMElHTnZiblJoYVc1eklHeGhibWQxWVdkbElHbHVabTl5YldGMGFXOXVJSFJvWVhRZ1kyRnVJR0psSUdWaGMybHNlU0IxYzJWa0lHbHVJSGx2ZFhKY2JpQXFJRXBoZG1GVFkzSnBjSFFnWTI5a1pTNGdWR2hsSUc5aWFtVmpkQ0JqYjI1MFlXbHVjeUJqYjI1emRHRnVZMlVnZEhKaGJuTnNZWFJwYjI1eklHRnVaQ0JrZVc1aGJXbGpJSE5sWTNScGIyNXpJSFJvWVhSY2JpQXFJR05oYmlCaVpTQnNiMkZrWldRZ1lXNWtJSFZ6WldRZ2FXNGdaR2xtWm1WeVpXNTBJSEJoWjJVdVhHNGdLbHh1SUNvZ0l5TWpJeUJKYlhCdmNuUmhiblJjYmlBcUlGUm9aU0JsYm1kcGJtVWdkMmxzYkNCaGRYUnZiV0YwYVdOaGJHeDVJR3h2WVdRZ2RISmhibk5zWVhScGIyNGdjMlZqZEdsdmJuTWdkR2hoZENCaGNtVWdjSEpsYzJWdWRDQnBiaUIwYUdWY2JpQXFJR0IzYVc1a2IzY3VTbE5GYm1kcGJtVkRiMjVtYVdkMWNtRjBhVzl1TG5SeVlXNXpiR0YwYVc5dWMyQWdjSEp2Y0dWeWRIa2dkWEJ2YmlCcGJtbDBhV0ZzYVhwaGRHbHZiaTRnUm05eUlHMXZjbVZjYmlBcUlHbHVabTl5YldGMGFXOXVJR3h2YjJzZ1lYUWdkR2hsSUZ3aVkyOXlaUzlwYm1sMGFXRnNhWHBsWENJZ2NHRm5aU0J2WmlCa2IyTjFiV1Z1ZEdGMGFXOXVJSEpsWm1WeVpXNWpaUzVjYmlBcVhHNGdLaUJnWUdCcVlYWmhjMk55YVhCMFhHNGdLaUJxYzJVdVkyOXlaUzVzWVc1bkxtRmtaRk5sWTNScGIyNG9KM05sWTNScGIyNU9ZVzFsSnl3Z2V5QjBjbUZ1YzJ4aGRHbHZia3RsZVRvZ0ozUnlZVzV6YkdGMGFXOXVWbUZzZFdVbklIMHBPeUF2THlCQlpHUWdkSEpoYm5Oc1lYUnBiMjRnYzJWamRHbHZiaTVjYmlBcUlHcHpaUzVqYjNKbExuUnlZVzV6YkdGMFpTZ25kSEpoYm5Oc1lYUnBiMjVMWlhrbkxDQW5jMlZqZEdsdmJrNWhiV1VuS1RzZ0x5OGdSMlYwSUhSb1pTQjBjbUZ1YzJ4aGRHVmtJSE4wY21sdVp5NWNiaUFxSUdwelpTNWpiM0psTG1kbGRGTmxZM1JwYjI1ektDazdJQzh2SUhKbGRIVnlibk1nWVhKeVlYa2dkMmwwYUNCelpXTjBhVzl1Y3lCbExtY3VJRnNuWVdSdGFXNWZZblYwZEc5dWN5Y3NJQ2RuWlc1bGNtRnNKMTFjYmlBcUlHQmdZRnh1SUNwY2JpQXFJRUJ0YjJSMWJHVWdTbE5GTDBOdmNtVXZiR0Z1WjF4dUlDb3ZYRzRvWm5WdVkzUnBiMjRnS0dWNGNHOXlkSE1wSUh0Y2JseHVJQ0FnSUNkMWMyVWdjM1J5YVdOMEp6dGNibHh1SUNBZ0lDOHZJQzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMVnh1SUNBZ0lDOHZJRlpCVWtsQlFreEZVMXh1SUNBZ0lDOHZJQzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMVnh1WEc0Z0lDQWdMeW9xWEc0Z0lDQWdJQ29nUTI5dWRHRnBibk1nZG1GeWFXOTFjeUIwY21GdWMyeGhkR2x2YmlCelpXTjBhVzl1Y3k1Y2JpQWdJQ0FnS2x4dUlDQWdJQ0FxSUVCMGVYQmxJSHRQWW1wbFkzUjlYRzRnSUNBZ0lDb3ZYRzRnSUNBZ1kyOXVjM1FnYzJWamRHbHZibk1nUFNCN2ZUdGNibHh1SUNBZ0lDOHZJQzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMVnh1SUNBZ0lDOHZJRkJWUWt4SlF5Qk5SVlJJVDBSVFhHNGdJQ0FnTHk4Z0xTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdFhHNWNiaUFnSUNBdktpcGNiaUFnSUNBZ0tpQkJaR1FnWVNCMGNtRnVjMnhoZEdsdmJpQnpaV04wYVc5dUxseHVJQ0FnSUNBcVhHNGdJQ0FnSUNvZ1FIQmhjbUZ0SUh0VGRISnBibWQ5SUc1aGJXVWdUbUZ0WlNCdlppQjBhR1VnYzJWamRHbHZiaXdnZFhObFpDQnNZWFJsY2lCbWIzSWdZV05qWlhOemFXNW5JSFJ5WVc1emJHRjBhVzl1SUhOMGNtbHVaM011WEc0Z0lDQWdJQ29nUUhCaGNtRnRJSHRQWW1wbFkzUjlJSFJ5WVc1emJHRjBhVzl1Y3lCTFpYa2dMU0IyWVd4MVpTQnZZbXBsWTNRZ1kyOXVkR0ZwYm1sdVp5QjBhR1VnZEhKaGJuTnNZWFJwYjI1ekxseHVJQ0FnSUNBcVhHNGdJQ0FnSUNvZ1FIUm9jbTkzY3lCN1JYSnliM0o5SUVsbUlGd2libUZ0WlZ3aUlHOXlJRndpZEhKaGJuTnNZWFJwYjI1elhDSWdZWEpuZFcxbGJuUnpJR0Z5WlNCcGJuWmhiR2xrTGx4dUlDQWdJQ0FxTDF4dUlDQWdJR1Y0Y0c5eWRITXVZV1JrVTJWamRHbHZiaUE5SUdaMWJtTjBhVzl1SUNodVlXMWxMQ0IwY21GdWMyeGhkR2x2Ym5NcElIdGNiaUFnSUNBZ0lDQWdhV1lnS0hSNWNHVnZaaUJ1WVcxbElDRTlQU0FuYzNSeWFXNW5KeUI4ZkNCMGVYQmxiMllnZEhKaGJuTnNZWFJwYjI1eklDRTlQU0FuYjJKcVpXTjBKeUI4ZkNCMGNtRnVjMnhoZEdsdmJuTWdQVDA5SUc1MWJHd3BJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lIUm9jbTkzSUc1bGR5QkZjbkp2Y2loZ2QybHVaRzkzTG1kNExtTnZjbVV1YkdGdVp5NWhaR1JUWldOMGFXOXVPaUJKYm5aaGJHbGtJR0Z5WjNWdFpXNTBjeUJ3Y205MmFXUmxaQ0FvYm1GdFpUb2dKSHQwZVhCbGIyWWdibUZ0Wlgwc0lHQmNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXJJR0IwY21GdWMyeGhkR2x2Ym5NNklDUjdkSGx3Wlc5bUlIUnlZVzV6YkdGMGFXOXVjMzBwTG1BcE8xeHVJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJSE5sWTNScGIyNXpXMjVoYldWZElEMGdkSEpoYm5Oc1lYUnBiMjV6TzF4dUlDQWdJSDA3WEc1Y2JpQWdJQ0F2S2lwY2JpQWdJQ0FnS2lCSFpYUWdiRzloWkdWa0lIUnlZVzV6YkdGMGFXOXVJSE5sWTNScGIyNXpMbHh1SUNBZ0lDQXFYRzRnSUNBZ0lDb2dWWE5sWm5Wc0lHWnZjaUJoYzNObGNuUnBibWNnY0hKbGMyVnVkQ0IwY21GdWMyeGhkR2x2YmlCelpXTjBhVzl1Y3k1Y2JpQWdJQ0FnS2x4dUlDQWdJQ0FxSUVCeVpYUjFjbTRnZTBGeWNtRjVmU0JTWlhSMWNtNXpJR0Z5Y21GNUlIZHBkR2dnZEdobElHVjRhWE4wYVc1bklITmxZM1JwYjI1ekxseHVJQ0FnSUNBcUwxeHVJQ0FnSUdWNGNHOXlkSE11WjJWMFUyVmpkR2x2Ym5NZ1BTQm1kVzVqZEdsdmJpQW9LU0I3WEc0Z0lDQWdJQ0FnSUdOdmJuTjBJSEpsYzNWc2RDQTlJRnRkTzF4dVhHNGdJQ0FnSUNBZ0lHWnZjaUFvYkdWMElITmxZM1JwYjI0Z2FXNGdjMlZqZEdsdmJuTXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lISmxjM1ZzZEM1d2RYTm9LSE5sWTNScGIyNHBPMXh1SUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ2NtVjBkWEp1SUhKbGMzVnNkRHRjYmlBZ0lDQjlPMXh1SUNBZ0lGeHVJQ0FnSUZ4dUlDQWdJQzhxS2x4dUlDQWdJQ0FxSUZKbGRIVnlibk1nWVc0Z1pXNTBhWEpsSUhObFkzUnBiMjR1WEc0Z0lDQWdJQ3BjYmlBZ0lDQWdLaUJBY0dGeVlXMGdjMlZqZEdsdmJseHVJQ0FnSUNBcUlFQnlaWFIxY201eklIc3FmVnh1SUNBZ0lDQXFMMXh1SUNBZ0lHVjRjRzl5ZEhNdVoyVjBVMlZqZEdsdmJpQTlJR1oxYm1OMGFXOXVLSE5sWTNScGIyNHBJSHRjYmlBZ0lDQWdJQ0FnYVdZZ0tIUjVjR1Z2WmlCelpXTjBhVzl1SUNFOVBTQW5jM1J5YVc1bkp5a2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ2RHaHliM2NnYm1WM0lFVnljbTl5S0dCSmJuWmhiR2xrSUdGeVozVnRaVzUwSUhCeWIzWnBaR1ZrSUhSdklHZGxkRk5sWTNScGIyNGdLSE5sWTNScGIyNDZJQ1I3ZEhsd1pXOW1JSE5sWTNScGIyNTlLUzVnS1R0Y2JpQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQmNiaUFnSUNBZ0lDQWdhV1lnS0hObFkzUnBiMjV6VzNObFkzUnBiMjVkSUQwOVBTQjFibVJsWm1sdVpXUXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lIUm9jbTkzSUc1bGR5QkZjbkp2Y2loZ1UyVmpkR2x2YmlBa2UzTmxZM1JwYjI1OUlHbHpJSFZ1WVhaaGFXeGhZbXhsTG1BcE8xeHVJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJRnh1SUNBZ0lDQWdJQ0J5WlhSMWNtNGdjMlZqZEdsdmJuTmJjMlZqZEdsdmJsMDdYRzRnSUNBZ2ZWeHVJQ0FnSUZ4dVhHNGdJQ0FnTHlvcVhHNGdJQ0FnSUNvZ1ZISmhibk5zWVhSbElITjBjbWx1WnlCcGJpQktZWFpoYzJOeWFYQjBJR052WkdVdVhHNGdJQ0FnSUNwY2JpQWdJQ0FnS2lCQWNHRnlZVzBnZTFOMGNtbHVaMzBnY0doeVlYTmxJRTVoYldVZ2IyWWdkR2hsSUhCb2NtRnpaU0JqYjI1MFlXbHVhVzVuSUhSb1pTQjBjbUZ1YzJ4aGRHbHZiaTVjYmlBZ0lDQWdLaUJBY0dGeVlXMGdlMU4wY21sdVozMGdjMlZqZEdsdmJpQlRaV04wYVc5dUlHNWhiV1VnWTI5dWRHRnBibWx1WnlCMGFHVWdkSEpoYm5Oc1lYUnBiMjRnYzNSeWFXNW5MbHh1SUNBZ0lDQXFYRzRnSUNBZ0lDb2dRSEpsZEhWeWJpQjdVM1J5YVc1bmZTQlNaWFIxY201eklIUm9aU0IwY21GdWMyeGhkR1ZrSUhOMGNtbHVaeTVjYmlBZ0lDQWdLbHh1SUNBZ0lDQXFJRUIwYUhKdmQzTWdlMFZ5Y205eWZTQkpaaUJ3Y205MmFXUmxaQ0JoY21kMWJXVnVkSE1nWVhKbElHbHVkbUZzYVdRdVhHNGdJQ0FnSUNvZ1FIUm9jbTkzY3lCN1JYSnliM0o5SUVsbUlISmxjWFZwY21Wa0lITmxZM1JwYjI0Z1pHOWxjeUJ1YjNRZ1pYaHBjM1FnYjNJZ2RISmhibk5zWVhScGIyNGdZMjkxYkdRZ2JtOTBJR0psSUdadmRXNWtMbHh1SUNBZ0lDQXFMMXh1SUNBZ0lHVjRjRzl5ZEhNdWRISmhibk5zWVhSbElEMGdablZ1WTNScGIyNGdLSEJvY21GelpTd2djMlZqZEdsdmJpa2dlMXh1SUNBZ0lDQWdJQ0F2THlCV1lXeHBaR0YwWlNCd2NtOTJhV1JsWkNCaGNtZDFiV1Z1ZEhNdVhHNGdJQ0FnSUNBZ0lHbG1JQ2gwZVhCbGIyWWdjR2h5WVhObElDRTlQU0FuYzNSeWFXNW5KeUI4ZkNCMGVYQmxiMllnYzJWamRHbHZiaUFoUFQwZ0ozTjBjbWx1WnljcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUhSb2NtOTNJRzVsZHlCRmNuSnZjaWhnU1c1MllXeHBaQ0JoY21kMWJXVnVkSE1nY0hKdmRtbGtaV1FnYVc0Z2RISmhibk5zWVhSbElHMWxkR2h2WkNBb2NHaHlZWE5sT2lBa2UzUjVjR1Z2WmlCd2FISmhjMlY5TENCZ1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0t5QmdjMlZqZEdsdmJqb2dKSHQwZVhCbGIyWWdjMlZqZEdsdmJuMHBMbUFwTzF4dUlDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdMeThnUTJobFkyc2dhV1lnZEhKaGJuTnNZWFJwYjI0Z1pYaHBjM1J6TGx4dUlDQWdJQ0FnSUNCcFppQW9jMlZqZEdsdmJuTmJjMlZqZEdsdmJsMGdQVDA5SUhWdVpHVm1hVzVsWkNCOGZDQnpaV04wYVc5dWMxdHpaV04wYVc5dVhWdHdhSEpoYzJWZElEMDlQU0IxYm1SbFptbHVaV1FwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJR3B6WlM1amIzSmxMbVJsWW5WbkxuZGhjbTRvWUVOdmRXeGtJRzV2ZENCbWIzVnVaQ0J5WlhGMVpYTjBaV1FnZEhKaGJuTnNZWFJwYjI0Z0tIQm9jbUZ6WlRvZ0pIdHdhSEpoYzJWOUxDQnpaV04wYVc5dU9pQWtlM05sWTNScGIyNTlLUzVnS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJSEpsZEhWeWJpQW5leWNnS3lCelpXTjBhVzl1SUNzZ0p5NG5JQ3NnY0doeVlYTmxJQ3NnSjMwbk8xeHVJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnY21WMGRYSnVJSE5sWTNScGIyNXpXM05sWTNScGIyNWRXM0JvY21GelpWMDdYRzRnSUNBZ2ZUdGNibHh1ZlNocWMyVXVZMjl5WlM1c1lXNW5LU2s3WEc0aUxDSXZLaUF0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMVnh1SUcxaGFXNHVhbk1nTWpBeE9DMHdOaTB3TjF4dUlFZGhiV0pwYnlCSGJXSklYRzRnYUhSMGNEb3ZMM2QzZHk1bllXMWlhVzh1WkdWY2JpQkRiM0I1Y21sbmFIUWdLR01wSURJd01UZ2dSMkZ0WW1sdklFZHRZa2hjYmlCU1pXeGxZWE5sWkNCMWJtUmxjaUIwYUdVZ1IwNVZJRWRsYm1WeVlXd2dVSFZpYkdsaklFeHBZMlZ1YzJVZ0tGWmxjbk5wYjI0Z01pbGNiaUJiYUhSMGNEb3ZMM2QzZHk1bmJuVXViM0puTDJ4cFkyVnVjMlZ6TDJkd2JDMHlMakF1YUhSdGJGMWNiaUF0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMVnh1SUNvdlhHNWNiaTh2SUVsdGNHOXlkQ0JwYm1sMGFXRnNhWHBoZEdsdmJpQnpZM0pwY0hRdUlGeHVhVzF3YjNKMElDY3VMMmx1YVhScFlXeHBlbVVuTzF4dVhHNHZMeUJKYlhCdmNuUWdkR2hsSUdOdmJuTjBjblZqZEc5eUlHWnBiR1Z6TGlCY2JtbHRjRzl5ZENBbkxpNHZZMjl1YzNSeWRXTjBiM0p6TDJOdmJHeGxZM1JwYjI0bk8xeHVhVzF3YjNKMElDY3VMaTlqYjI1emRISjFZM1J2Y25NdlpHRjBZVjlpYVc1a2FXNW5KenRjYm1sdGNHOXlkQ0FuTGk0dlkyOXVjM1J5ZFdOMGIzSnpMMjF2WkhWc1pTYzdYRzVwYlhCdmNuUWdKeTR1TDJOdmJuTjBjblZqZEc5eWN5OXVZVzFsYzNCaFkyVW5PMXh1WEc0dkx5QkpiWEJ2Y25RZ2RHaGxJR052Y21VZ1ptbHNaWE11SUZ4dWFXMXdiM0owSUNjdUwyRmliM1YwSnp0Y2JtbHRjRzl5ZENBbkxpOWpiMjVtYVdjbk8xeHVhVzF3YjNKMElDY3VMMlJsWW5Wbkp6dGNibWx0Y0c5eWRDQW5MaTlsYm1kcGJtVW5PMXh1YVcxd2IzSjBJQ2N1TDJWNGRHVnVaQ2M3WEc1cGJYQnZjblFnSnk0dmJHRnVaeWM3WEc1cGJYQnZjblFnSnk0dmNtVnhkV2x5WlNjN1hHNXBiWEJ2Y25RZ0p5NHZiVzlrZFd4bFgyeHZZV1JsY2ljN1hHNXBiWEJ2Y25RZ0p5NHZjRzlzZVdacGJHeHpKenRjYm1sdGNHOXlkQ0FuTGk5eVpXZHBjM1J5ZVNjN1hHNXBiWEJ2Y25RZ0p5NHZkblZsSnpzaUxDSXZLaUF0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMVnh1SUcxdlpIVnNaVjlzYjJGa1pYSXVhbk1nTWpBeE9DMHdPUzB4TWx4dUlFZGhiV0pwYnlCSGJXSklYRzRnYUhSMGNEb3ZMM2QzZHk1bllXMWlhVzh1WkdWY2JpQkRiM0I1Y21sbmFIUWdLR01wSURJd01UWWdSMkZ0WW1sdklFZHRZa2hjYmlCU1pXeGxZWE5sWkNCMWJtUmxjaUIwYUdVZ1IwNVZJRWRsYm1WeVlXd2dVSFZpYkdsaklFeHBZMlZ1YzJVZ0tGWmxjbk5wYjI0Z01pbGNiaUJiYUhSMGNEb3ZMM2QzZHk1bmJuVXViM0puTDJ4cFkyVnVjMlZ6TDJkd2JDMHlMakF1YUhSdGJGMWNiaUF0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMVnh1SUNvdlhHNWNibXB6WlM1amIzSmxMbTF2WkhWc1pWOXNiMkZrWlhJZ1BTQnFjMlV1WTI5eVpTNXRiMlIxYkdWZmJHOWhaR1Z5SUh4OElIdDlPMXh1WEc0dktpcGNiaUFxSUVwVFJTQk5iMlIxYkdVZ1RHOWhaR1Z5WEc0Z0tseHVJQ29nVkdocGN5QnZZbXBsWTNRZ2FYTWdZVzRnWVdSaGNIUmxjaUJpWlhSM1pXVnVJSFJvWlNCbGJtZHBibVVnWVc1a0lGSmxjWFZwY21WS1V5QjNhR2xqYUNCcGN5QjFjMlZrSUhSdklHeHZZV1FnZEdobElISmxjWFZwY21Wa0lHWnBiR1Z6WEc0Z0tpQnBiblJ2SUhSb1pTQmpiR2xsYm5RdVhHNGdLbHh1SUNvZ1FHMXZaSFZzWlNCS1UwVXZRMjl5WlM5dGIyUjFiR1ZmYkc5aFpHVnlYRzRnS2k5Y2JpaG1kVzVqZEdsdmJpQW9aWGh3YjNKMGN5a2dlMXh1WEc0Z0lDQWdKM1Z6WlNCemRISnBZM1FuTzF4dVhHNGdJQ0FnTHk4Z0xTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdFhHNGdJQ0FnTHk4Z1VGSkpWa0ZVUlNCTlJWUklUMFJUWEc0Z0lDQWdMeThnTFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0WEc1Y2JpQWdJQ0F2S2lwY2JpQWdJQ0FnS2lCTWIyRmtJRU5UVXlCbWFXeGxMbHh1SUNBZ0lDQXFYRzRnSUNBZ0lDb2dRSEJoY21GdElIdFRkSEpwYm1kOUlIVnliQ0JCWW5OdmJIVjBaU0JWVWt3Z2IyWWdkR2hsSUVOVFV5Qm1hV3hsSUhSdklHSmxJR3h2WVdSbFpDNWNiaUFnSUNBZ0tseHVJQ0FnSUNBcUlFQndjbWwyWVhSbFhHNGdJQ0FnSUNvdlhHNGdJQ0FnWm5WdVkzUnBiMjRnWDJ4dllXUkRjM01vZFhKc0tTQjdYRzRnSUNBZ0lDQWdJR052Ym5OMElHeHBibXNnUFNCa2IyTjFiV1Z1ZEM1amNtVmhkR1ZGYkdWdFpXNTBLQ2RzYVc1ckp5azdYRzRnSUNBZ0lDQWdJR3hwYm1zdWRIbHdaU0E5SUNkMFpYaDBMMk56Y3ljN1hHNGdJQ0FnSUNBZ0lHeHBibXN1Y21Wc0lEMGdKM04wZVd4bGMyaGxaWFFuTzF4dUlDQWdJQ0FnSUNCc2FXNXJMbWh5WldZZ1BTQjFjbXc3WEc0Z0lDQWdJQ0FnSUdSdlkzVnRaVzUwTG1kbGRFVnNaVzFsYm5SelFubFVZV2RPWVcxbEtDZG9aV0ZrSnlsYk1GMHVZWEJ3Wlc1a1EyaHBiR1FvYkdsdWF5azdYRzRnSUNBZ2ZWeHVYRzRnSUNBZ0x5OGdMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRYRzRnSUNBZ0x5OGdVRlZDVEVsRElFMUZWRWhQUkZOY2JpQWdJQ0F2THlBdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMxY2JseHVJQ0FnSUM4cUtseHVJQ0FnSUNBcUlFbHVhWFJwWVd4cGVtVWdkR2hsSUcxdlpIVnNaU0JzYjJGa1pYSXVYRzRnSUNBZ0lDcGNiaUFnSUNBZ0tpQkZlR1ZqZFhSbElIUm9hWE1nYldWMGFHOWtJR0ZtZEdWeUlIUm9aU0JsYm1kcGJtVWdZMjl1Wm1sbklHbHpJR2x1YVhScFlXeHBlbVZrTGlCSmRDQjNhV3hzSUdOdmJtWnBaM1Z5WlNCeVpYRjFhWEpsTG1welhHNGdJQ0FnSUNvZ2MyOGdkR2hoZENCcGRDQjNhV3hzSUdKbElHRmliR1VnZEc4Z1ptbHVaQ0IwYUdVZ2NISnZhbVZqZENCbWFXeGxjeTVjYmlBZ0lDQWdLbHh1SUNBZ0lDQXFJRlJvWlNCallXTm9aU0JpZFhOMGFXNW5JRzFsZEdodlpDQjNhV3hzSUhSeWVTQjBieUJqY21WaGRHVWdZU0J1ZFcxaVpYSWdZbUZ6WldRZ2IyNGdkR2hsSUdOMWNuSmxiblFnYzJodmNDQjJaWEp6YVc5dUxseHVJQ0FnSUNBcUwxeHVJQ0FnSUdWNGNHOXlkSE11YVc1cGRDQTlJR1oxYm1OMGFXOXVJQ2dwSUh0Y2JpQWdJQ0FnSUNBZ2JHVjBJR05oWTJobFFuVnpkQ0E5SUNjbk8xeHVYRzRnSUNBZ0lDQWdJR2xtSUNocWMyVXVZMjl5WlM1amIyNW1hV2N1WjJWMEtDZGxiblpwY205dWJXVnVkQ2NwSUQwOVBTQW5jSEp2WkhWamRHbHZiaWNnSmlaY2JpQWdJQ0FnSUNBZ0lDQWdJR3B6WlM1amIzSmxMbU52Ym1acFp5NW5aWFFvSjJKMWMzUkdhV3hsY3ljcElEMDlQU0JtWVd4elpTQW1KbHh1SUNBZ0lDQWdJQ0FnSUNBZ2FuTmxMbU52Y21VdVkyOXVabWxuTG1kbGRDZ25ZMkZqYUdWVWIydGxiaWNwS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0JqWVdOb1pVSjFjM1FnUFNCZ1luVnpkRDBrZTJwelpTNWpiM0psTG1OdmJtWnBaeTVuWlhRb0oyTmhZMmhsVkc5clpXNG5LWDFnTzF4dUlDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdZMjl1YzNRZ1kyOXVabWxuSUQwZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnWW1GelpWVnliRG9nYW5ObExtTnZjbVV1WTI5dVptbG5MbWRsZENnbllYQndWWEpzSnlrc1hHNGdJQ0FnSUNBZ0lDQWdJQ0IxY214QmNtZHpPaUJqWVdOb1pVSjFjM1FzWEc0Z0lDQWdJQ0FnSUNBZ0lDQnZia1Z5Y205eU9pQm1kVzVqZEdsdmJpQW9aWEp5YjNJcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnFjMlV1WTI5eVpTNWtaV0oxWnk1bGNuSnZjaWduVW1WeGRXbHlaVXBUSUVWeWNtOXlPaWNzSUdWeWNtOXlLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdmVHRjYmx4dUlDQWdJQ0FnSUNCM2FXNWtiM2N1Y21WeGRXbHlaUzVqYjI1bWFXY29ZMjl1Wm1sbktUdGNiaUFnSUNCOU8xeHVYRzRnSUNBZ0x5b3FYRzRnSUNBZ0lDb2dVbVZ4ZFdseVpTQktVeUJoYm1RZ1ExTlRJR1pwYkdWeklDNWNiaUFnSUNBZ0tseHVJQ0FnSUNBcUlFNXZkR2xqWlRvZ1ZHaGxjbVVuY3lCdWJ5QmpiMjVqY21WMFpTQjNZWGtnZEc4Z1pHVjBaWEp0YVc1bElIZG9aVzRnUTFOVElHUmxjR1Z1WkdWdVkybGxjeUJoY21VZ2JHOWhaR1ZrTGx4dUlDQWdJQ0FxWEc0Z0lDQWdJQ29nUUhCaGNtRnRJSHRUZEhKcGJtZGJYWDBnWkdWd1pXNWtaVzVqYVdWeklFUmxjR1Z1WkdWdVkza2dWVkpNY3k1Y2JpQWdJQ0FnS2lCQWNHRnlZVzBnZTBaMWJtTjBhVzl1ZlNCallXeHNZbUZqYXlCRFlXeHNZbUZqYXlCdFpYUm9iMlFnZEc4Z1ltVWdZMkZzYkdWa0lHOXVZMlVnZEdobElHUmxjR1Z1WkdWdVkybGxjeUJoY21VZ2JHOWhaR1ZrTGx4dUlDQWdJQ0FxTDF4dUlDQWdJR1Y0Y0c5eWRITXVjbVZ4ZFdseVpTQTlJR1oxYm1OMGFXOXVJQ2hrWlhCbGJtUmxibU5wWlhNc0lHTmhiR3hpWVdOcktTQjdYRzRnSUNBZ0lDQWdJR1p2Y2lBb2JHVjBJR1JsY0dWdVpHVnVZM2tnYjJZZ1pHVndaVzVrWlc1amFXVnpLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQnBaaUFvWkdWd1pXNWtaVzVqZVM1cGJtTnNkV1JsY3lnbkxtTnpjeWNwS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1gyeHZZV1JEYzNNb1pHVndaVzVrWlc1amVTazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdZMjl1YzNRZ2FXNWtaWGdnUFNCa1pYQmxibVJsYm1OcFpYTXVhVzVrWlhoUFppaGtaWEJsYm1SbGJtTjVLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JrWlhCbGJtUmxibU5wWlhNdWMzQnNhV05sS0dsdVpHVjRMQ0F4S1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lHbG1JQ2hrWlhCbGJtUmxibU5wWlhNdWJHVnVaM1JvSUQwOVBTQXdLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQmpZV3hzWW1GamF5Z3BPMXh1SUNBZ0lDQWdJQ0I5SUdWc2MyVWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ2QybHVaRzkzTG5KbGNYVnBjbVVvWkdWd1pXNWtaVzVqYVdWekxDQmpZV3hzWW1GamF5azdYRzRnSUNBZ0lDQWdJSDFjYmlBZ0lDQjlPMXh1WEc0Z0lDQWdMeW9xWEc0Z0lDQWdJQ29nVEc5aFpDQmhJRzF2WkhWc1pTQm1hV3hsSUhkcGRHZ2dkR2hsSUhWelpTQnZaaUJ5WlhGMWFYSmxhbk11WEc0Z0lDQWdJQ3BjYmlBZ0lDQWdLaUJBY0dGeVlXMGdlMDlpYW1WamRIMGdKR1ZzWlcxbGJuUWdVMlZzWldOMGIzSWdiMllnZEdobElHVnNaVzFsYm5RZ2QyaHBZMmdnYUdGeklIUm9aU0J0YjJSMWJHVWdaR1ZtYVc1cGRHbHZiaTVjYmlBZ0lDQWdLaUJBY0dGeVlXMGdlMU4wY21sdVozMGdibUZ0WlNCTmIyUjFiR1VnYm1GdFpTQjBieUJpWlNCc2IyRmtaV1F1SUUxdlpIVnNaWE1nYUdGMlpTQjBhR1VnYzJGdFpTQnVZVzFsY3lCaGN5QjBhR1ZwY2lCbWFXeGxjeTVjYmlBZ0lDQWdLaUJBY0dGeVlXMGdlMDlpYW1WamRIMGdZMjlzYkdWamRHbHZiaUJEZFhKeVpXNTBJR052Ykd4bFkzUnBiMjRnYVc1emRHRnVZMlV1WEc0Z0lDQWdJQ3BjYmlBZ0lDQWdLaUJBY21WMGRYSnVJSHRQWW1wbFkzUjlJRkpsZEhWeWJuTWdZU0J3Y205dGFYTmxJRzlpYW1WamRDQjBieUJpWlNCeVpYTnZiSFpsWkNCM2FYUm9JSFJvWlNCdGIyUjFiR1VnYVc1emRHRnVZMlVnWVhNZ1lTQndZWEpoYldWMFpYSXVYRzRnSUNBZ0lDb3ZYRzRnSUNBZ1pYaHdiM0owY3k1c2IyRmtJRDBnWm5WdVkzUnBiMjRnS0NSbGJHVnRaVzUwTENCdVlXMWxMQ0JqYjJ4c1pXTjBhVzl1S1NCN1hHNGdJQ0FnSUNBZ0lHTnZibk4wSUdSbFptVnljbVZrSUQwZ0pDNUVaV1psY25KbFpDZ3BPMXh1WEc0Z0lDQWdJQ0FnSUhSeWVTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCcFppQW9ibUZ0WlNBOVBUMGdKeWNwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCa1pXWmxjbkpsWkM1eVpXcGxZM1FvYm1WM0lFVnljbTl5S0NkTmIyUjFiR1VnYm1GdFpTQmpZVzV1YjNRZ1ltVWdaVzF3ZEhrdUp5a3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJQ0FnSUNCamIyNXpkQ0JpWVhObFRXOWtkV3hsVG1GdFpTQTlJRzVoYldVdWNtVndiR0ZqWlNndkxpcGNYQzhvTGlvcEpDOHNJQ2NrTVNjcE95QXZMeUJPWVcxbElIZHBkR2h2ZFhRZ2RHaGxJSEJoY21WdWRDQmthWEpsWTNSdmNtbGxjeTVjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdMeThnVkhKNUlIUnZJR3h2WVdRZ2RHaGxJR05oWTJobFpDQnBibk4wWVc1alpTQnZaaUIwYUdVZ2JXOWtkV3hsTGx4dUlDQWdJQ0FnSUNBZ0lDQWdZMjl1YzNRZ1kyRmphR1ZrSUQwZ1kyOXNiR1ZqZEdsdmJpNWpZV05vWlM1dGIyUjFiR1Z6VzJKaGMyVk5iMlIxYkdWT1lXMWxYVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2hqWVdOb1pXUWdKaVlnWTJGamFHVmtMbU52WkdVZ1BUMDlJQ2RtZFc1amRHbHZiaWNwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCa1pXWmxjbkpsWkM1eVpYTnZiSFpsS0c1bGR5QnFjMlV1WTI5dWMzUnlkV04wYjNKekxrMXZaSFZzWlNna1pXeGxiV1Z1ZEN3Z1ltRnpaVTF2WkhWc1pVNWhiV1VzSUdOdmJHeGxZM1JwYjI0cEtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnlaWFIxY200Z2RISjFaVHNnTHk4Z1kyOXVkR2x1ZFdVZ2JHOXZjRnh1SUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJQ0FnSUNCc1pYUWdZblZ6ZEZOMVptWnBlQ0E5SUNjbk8xeHVJQ0FnSUNBZ0lDQWdJQ0FnYVdZZ0tHcHpaUzVqYjNKbExtTnZibVpwWnk1blpYUW9KMlZ1ZG1seWIyNXRaVzUwSnlrZ1BUMDlJQ2R3Y205a2RXTjBhVzl1SnlBbUpseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHcHpaUzVqYjNKbExtTnZibVpwWnk1blpYUW9KMkoxYzNSR2FXeGxjeWNwSUNZbVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FuTmxMbU52Y21VdVkyOXVabWxuTG1kbGRDZ25ZMkZqYUdWVWIydGxiaWNwWEc0Z0lDQWdJQ0FnSUNBZ0lDQXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JpZFhOMFUzVm1abWw0SUQwZ0p5MWlkWE4wWHljZ0t5QnFjMlV1WTI5eVpTNWpiMjVtYVdjdVoyVjBLQ2RqWVdOb1pWUnZhMlZ1SnlrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQzh2SUZSeWVTQjBieUJzYjJGa0lIUm9aU0J0YjJSMWJHVWdabWxzWlNCbWNtOXRJSFJvWlNCelpYSjJaWEl1WEc0Z0lDQWdJQ0FnSUNBZ0lDQmpiMjV6ZENCbWFXeGxSWGgwWlc1emFXOXVJRDBnYW5ObExtTnZjbVV1WTI5dVptbG5MbWRsZENnblpHVmlkV2NuS1NBaFBUMGdKMFJGUWxWSEp5QS9JQ2N1YldsdUxtcHpKeUE2SUNjdWFuTW5PMXh1SUNBZ0lDQWdJQ0FnSUNBZ1kyOXVjM1FnZFhKc0lEMGdZMjlzYkdWamRHbHZiaTV1WVcxbGMzQmhZMlV1YzI5MWNtTmxJQ3NnSnk4bklDc2dZMjlzYkdWamRHbHZiaTV1WVcxbElDc2dKeThuSUNzZ2JtRnRaU0FySUdKMWMzUlRkV1ptYVhnZ0t5Qm1hV3hsUlhoMFpXNXphVzl1TzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0IzYVc1a2IzY3VjbVZ4ZFdseVpTaGJkWEpzWFN3Z0tDa2dQVDRnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUlDaGpiMnhzWldOMGFXOXVMbU5oWTJobExtMXZaSFZzWlhOYlltRnpaVTF2WkhWc1pVNWhiV1ZkSUQwOVBTQjFibVJsWm1sdVpXUXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RHaHliM2NnYm1WM0lFVnljbTl5S0NkTmIyUjFiR1VnWENJbklDc2dibUZ0WlNBcklDZGNJaUIzWVhOdVhGd25kQ0JrWldacGJtVmtJR052Y25KbFkzUnNlUzRnUTJobFkyc2dkR2hsSUcxdlpIVnNaU0JqYjJSbElHWnZjaUFuWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FySUNkbWRYSjBhR1Z5SUhSeWIzVmliR1Z6YUc5dmRHbHVaeTRuS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJWYzJVZ2RHaGxJSE5zYVdObElHMWxkR2h2WkNCbWIzSWdZMjl3ZVdsdVp5QjBhR1VnWVhKeVlYa3VJRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR052Ym5OMElHUmxjR1Z1WkdWdVkybGxjeUE5SUdOdmJHeGxZM1JwYjI0dVkyRmphR1V1Ylc5a2RXeGxjMXRpWVhObFRXOWtkV3hsVG1GdFpWMHVaR1Z3Wlc1a1pXNWphV1Z6TG5Oc2FXTmxLQ2s3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9aR1Z3Wlc1a1pXNWphV1Z6TG14bGJtZDBhQ0E5UFQwZ01Da2dleUF2THlCdWJ5QmtaWEJsYm1SbGJtTnBaWE5jYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1pHVm1aWEp5WldRdWNtVnpiMngyWlNodVpYY2dhbk5sTG1OdmJuTjBjblZqZEc5eWN5NU5iMlIxYkdVb0pHVnNaVzFsYm5Rc0lHSmhjMlZOYjJSMWJHVk9ZVzFsTENCamIyeHNaV04wYVc5dUtTazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhKbGRIVnliaUIwY25WbE95QXZMeUJqYjI1MGFXNTFaU0JzYjI5d1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnVEc5aFpDQjBhR1VnWkdWd1pXNWtaVzVqYVdWeklHWnBjbk4wTGx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdadmNpQW9iR1YwSUdsdVpHVjRJR2x1SUdSbGNHVnVaR1Z1WTJsbGN5a2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCamIyNXpkQ0JrWlhCbGJtUmxibU41SUQwZ1pHVndaVzVrWlc1amFXVnpXMmx1WkdWNFhUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9aR1Z3Wlc1a1pXNWplUzVwYm1SbGVFOW1LQ2N1WTNOekp5a2dJVDA5SUMweEtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmZiRzloWkVOemN5aGtaWEJsYm1SbGJtTjVLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1JsY0dWdVpHVnVZMmxsY3k1emNHeHBZMlVvYVc1a1pYZ3NJREVwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWTI5dWRHbHVkV1U3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QlVhR1Z1SUdOdmJuWmxjblFnZEdobElISmxiR0YwYVhabElIQmhkR2dnZEc4Z1NsTkZibWRwYm1VdmJHbGljeUJrYVhKbFkzUnZjbmt1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2hrWlhCbGJtUmxibU41TG1sdVpHVjRUMllvSjJoMGRIQW5LU0E5UFQwZ0xURXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1JsY0dWdVpHVnVZMmxsYzF0cGJtUmxlRjBnUFNCcWMyVXVZMjl5WlM1amIyNW1hV2N1WjJWMEtDZGxibWRwYm1WVmNtd25LU0FySUNjdmJHbGljeThuSUNzZ1pHVndaVzVrWlc1amVTQXJJR0oxYzNSVGRXWm1hWGdnS3lCbWFXeGxSWGgwWlc1emFXOXVPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOUlHVnNjMlVnYVdZZ0tHUmxjR1Z1WkdWdVkza3VjM1ZpYzNSeUtDMHpLU0FoUFQwZ0p5NXFjeWNwSUhzZ0x5OGdWR2hsYmlCaFpHUWdkR2hsSUdSNWJtRnRhV01nWm1sc1pTQmxlSFJsYm5OcGIyNGdkRzhnZEdobElGVlNUQzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1JsY0dWdVpHVnVZMmxsYzF0cGJtUmxlRjBnS3owZ1luVnpkRk4xWm1acGVDQXJJR1pwYkdWRmVIUmxibk5wYjI0N1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCM2FXNWtiM2N1Y21WeGRXbHlaU2hrWlhCbGJtUmxibU5wWlhNc0lDZ3BJRDArSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdaR1ZtWlhKeVpXUXVjbVZ6YjJ4MlpTaHVaWGNnYW5ObExtTnZibk4wY25WamRHOXljeTVOYjJSMWJHVW9KR1ZzWlcxbGJuUXNJR0poYzJWTmIyUjFiR1ZPWVcxbExDQmpiMnhzWldOMGFXOXVLU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0I5S1R0Y2JpQWdJQ0FnSUNBZ2ZTQmpZWFJqYUNBb1pYaGpaWEIwYVc5dUtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCa1pXWmxjbkpsWkM1eVpXcGxZM1FvWlhoalpYQjBhVzl1S1R0Y2JpQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJSEpsZEhWeWJpQmtaV1psY25KbFpDNXdjbTl0YVhObEtDazdYRzRnSUNBZ2ZUdGNibHh1ZlNrb2FuTmxMbU52Y21VdWJXOWtkV3hsWDJ4dllXUmxjaWs3WEc0aUxDSXZLaUF0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMVnh1SUhCdmJIbG1hV3hzY3k1cWN5QXlNREUyTFRBMUxURTNYRzRnUjJGdFltbHZJRWR0WWtoY2JpQm9kSFJ3T2k4dmQzZDNMbWRoYldKcGJ5NWtaVnh1SUVOdmNIbHlhV2RvZENBb1l5a2dNakF4TmlCSFlXMWlhVzhnUjIxaVNGeHVJRkpsYkdWaGMyVmtJSFZ1WkdWeUlIUm9aU0JIVGxVZ1IyVnVaWEpoYkNCUWRXSnNhV01nVEdsalpXNXpaU0FvVm1WeWMybHZiaUF5S1Z4dUlGdG9kSFJ3T2k4dmQzZDNMbWR1ZFM1dmNtY3ZiR2xqWlc1elpYTXZaM0JzTFRJdU1DNW9kRzFzWFZ4dUlDMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdFhHNGdLaTljYmx4dUx5b3FYRzRnS2lCS1UwVWdVRzlzZVdacGJHeHpYRzRnS2x4dUlDb2dVbVZ4ZFdseVpXUWdjRzlzZVdacGJHeHpJR1p2Y2lCamIyMXdZWFJwWW1sc2FYUjVJR0Z0YjI1bklHOXNaQ0JpY205M2MyVnljeTVjYmlBcVhHNGdLaUJBYlc5a2RXeGxJRXBUUlM5RGIzSmxMM0J2YkhsbWFXeHNjMXh1SUNvdlhHNG9ablZ1WTNScGIyNGdLQ2tnZTF4dVhHNGdJQ0FnSjNWelpTQnpkSEpwWTNRbk8xeHVYRzRnSUNBZ0x5OGdTVzUwWlhKdVpYUWdSWGh3Ykc5eVpYSWdaRzlsY3lCdWIzUWdjM1Z3Y0c5eWRDQjBhR1VnYjNKcFoybHVJSEJ5YjNCbGNuUjVJRzltSUhSb1pTQjNhVzVrYjNjdWJHOWpZWFJwYjI0Z2IySnFaV04wTGx4dUlDQWdJQzh2SUh0QWJHbHVheUJvZEhSd09pOHZkRzl6WW05MWNtNHVZMjl0TDJFdFptbDRMV1p2Y2kxM2FXNWtiM2N0Ykc5allYUnBiMjR0YjNKcFoybHVMV2x1TFdsdWRHVnlibVYwTFdWNGNHeHZjbVZ5ZlZ4dUlDQWdJR2xtSUNnaGQybHVaRzkzTG14dlkyRjBhVzl1TG05eWFXZHBiaWtnZTF4dUlDQWdJQ0FnSUNCM2FXNWtiM2N1Ykc5allYUnBiMjR1YjNKcFoybHVJRDBnZDJsdVpHOTNMbXh2WTJGMGFXOXVMbkJ5YjNSdlkyOXNJQ3NnSnk4dkp5QXJYRzRnSUNBZ0lDQWdJQ0FnSUNCM2FXNWtiM2N1Ykc5allYUnBiMjR1YUc5emRHNWhiV1VnS3lBb2QybHVaRzkzTG14dlkyRjBhVzl1TG5CdmNuUWdQeUFuT2ljZ0t5QjNhVzVrYjNjdWJHOWpZWFJwYjI0dWNHOXlkQ0E2SUNjbktUdGNiaUFnSUNCOVhHNWNiaUFnSUNBdkx5QkVZWFJsTG01dmR5QnRaWFJvYjJRZ2NHOXNlV1pwYkd4Y2JpQWdJQ0F2THlCN1FHeHBibXNnYUhSMGNITTZMeTlrWlhabGJHOXdaWEl1Ylc5NmFXeHNZUzV2Y21jdlpXNHRWVk12Wkc5amN5OVhaV0l2U21GMllWTmpjbWx3ZEM5U1pXWmxjbVZ1WTJVdlIyeHZZbUZzWDA5aWFtVmpkSE12UkdGMFpTOXViM2Q5WEc0Z0lDQWdhV1lnS0NGRVlYUmxMbTV2ZHlrZ2UxeHVJQ0FnSUNBZ0lDQkVZWFJsTG01dmR5QTlJR1oxYm1OMGFXOXVJRzV2ZHlncElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUhKbGRIVnliaUJ1WlhjZ1JHRjBaU2dwTG1kbGRGUnBiV1VvS1R0Y2JpQWdJQ0FnSUNBZ2ZUdGNiaUFnSUNCOVhHNWNibjBwS0NrN1hHNWNibHh1SWl3aUx5b2dMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMWNiaUJ5WldkcGMzUnllUzVxY3lBeU1ERTJMVEE1TFRBNFhHNGdSMkZ0WW1sdklFZHRZa2hjYmlCb2RIUndPaTh2ZDNkM0xtZGhiV0pwYnk1a1pWeHVJRU52Y0hseWFXZG9kQ0FvWXlrZ01qQXhOaUJIWVcxaWFXOGdSMjFpU0Z4dUlGSmxiR1ZoYzJWa0lIVnVaR1Z5SUhSb1pTQkhUbFVnUjJWdVpYSmhiQ0JRZFdKc2FXTWdUR2xqWlc1elpTQW9WbVZ5YzJsdmJpQXlLVnh1SUZ0b2RIUndPaTh2ZDNkM0xtZHVkUzV2Y21jdmJHbGpaVzV6WlhNdlozQnNMVEl1TUM1b2RHMXNYVnh1SUMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0WEc0Z0tpOWNibHh1YW5ObExtTnZjbVV1Y21WbmFYTjBjbmtnUFNCcWMyVXVZMjl5WlM1eVpXZHBjM1J5ZVNCOGZDQjdmVHRjYmx4dUx5b3FYRzRnS2lCS1V5QkZibWRwYm1VZ1VtVm5hWE4wY25sY2JpQXFYRzRnS2lCVWFHbHpJRzlpYW1WamRDQmpiMjUwWVdsdWN5QnpkSEpwYm1jZ1pHRjBZU0IwYUdGMElHOTBhR1Z5SUhObFkzUnBiMjV6SUc5bUlIUm9aU0JsYm1kcGJtVWdibVZsWkNCcGJpQnZjbVJsY2lCMGJ5QnZjR1Z5WVhSbElHTnZjbkpsWTNSc2VTNWNiaUFxWEc0Z0tpQkFiVzlrZFd4bElFcFRSUzlEYjNKbEwzSmxaMmx6ZEhKNVhHNGdLaTljYmlobWRXNWpkR2x2YmlBb1pYaHdiM0owY3lrZ2UxeHVYRzRnSUNBZ0ozVnpaU0J6ZEhKcFkzUW5PMXh1WEc0Z0lDQWdMeW9xWEc0Z0lDQWdJQ29nUTI5dWRHRnBibk1nZEdobElISmxaMmx6ZEhKNUlIWmhiSFZsY3k1Y2JpQWdJQ0FnS2x4dUlDQWdJQ0FxSUVCMGVYQmxJSHRQWW1wbFkzUmJYWDFjYmlBZ0lDQWdLaTljYmlBZ0lDQmpiMjV6ZENCeVpXZHBjM1J5ZVNBOUlGdGRPMXh1WEc0Z0lDQWdMeW9xWEc0Z0lDQWdJQ29nVTJWMElHRWdkbUZzZFdVZ2FXNGdkR2hsSUhKbFoybHpkSEo1TGx4dUlDQWdJQ0FxWEc0Z0lDQWdJQ29nUUhCaGNtRnRJSHRUZEhKcGJtZDlJRzVoYldVZ1EyOXVkR0ZwYm5NZ2RHaGxJRzVoYldVZ2IyWWdkR2hsSUdWdWRISjVJSFJ2SUdKbElHRmtaR1ZrTGx4dUlDQWdJQ0FxSUVCd1lYSmhiU0I3S24wZ2RtRnNkV1VnVkdobElIWmhiSFZsSUhSdklHSmxJSGR5YVhSMFpXNGdhVzRnZEdobElISmxaMmx6ZEhKNUxseHVJQ0FnSUNBcUwxeHVJQ0FnSUdWNGNHOXlkSE11YzJWMElEMGdablZ1WTNScGIyNGdLRzVoYldVc0lIWmhiSFZsS1NCN1hHNGdJQ0FnSUNBZ0lDOHZJRWxtSUdFZ2NtVm5hWE4wY25rZ1pXNTBjbmtnZDJsMGFDQjBhR1VnYzJGdFpTQnVZVzFsSUdWNGFYTjBjeUJoYkhKbFlXUjVJSFJvWlNCbWIyeHNiM2RwYm1jZ1kyOXVjMjlzWlNCM1lYSnVhVzVuSUhkcGJHeGNiaUFnSUNBZ0lDQWdMeThnYVc1bWIzSnRJR1JsZG1Wc2IzQmxjbk1nZEdoaGRDQjBhR1Y1SUdGeVpTQnZkbVZ5ZDNKcGRHbHVaeUJoYmlCbGVHbHpkR2x1WnlCMllXeDFaU3dnYzI5dFpYUm9hVzVuSUhWelpXWjFiQ0IzYUdWdUlHUmxZblZuWjJsdVp5NWNiaUFnSUNBZ0lDQWdhV1lnS0hKbFoybHpkSEo1VzI1aGJXVmRJQ0U5UFNCMWJtUmxabWx1WldRcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUdwelpTNWpiM0psTG1SbFluVm5MbmRoY200b0oxUm9aU0J5WldkcGMzUnllU0IyWVd4MVpTQjNhWFJvSUhSb1pTQnVZVzFsSUZ3aUp5QXJJRzVoYldVZ0t5QW5YQ0lnZDJsc2JDQmlaU0J2ZG1WeWQzSnBkSFJsYmk0bktUdGNiaUFnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUhKbFoybHpkSEo1VzI1aGJXVmRJRDBnZG1Gc2RXVTdYRzRnSUNBZ2ZUdGNibHh1SUNBZ0lDOHFLbHh1SUNBZ0lDQXFJRWRsZENCaElIWmhiSFZsSUdaeWIyMGdkR2hsSUhKbFoybHpkSEo1TGx4dUlDQWdJQ0FxWEc0Z0lDQWdJQ29nUUhCaGNtRnRJSHRUZEhKcGJtZDlJRzVoYldVZ1ZHaGxJRzVoYldVZ2IyWWdkR2hsSUdWdWRISjVJSFpoYkhWbElIUnZJR0psSUhKbGRIVnlibVZrTGx4dUlDQWdJQ0FxWEc0Z0lDQWdJQ29nUUhKbGRIVnlibk1nZXlwOUlGSmxkSFZ5Ym5NZ2RHaGxJSFpoYkhWbElIUm9ZWFFnYldGMFkyaGxjeUIwYUdVZ2JtRnRaUzVjYmlBZ0lDQWdLaTljYmlBZ0lDQmxlSEJ2Y25SekxtZGxkQ0E5SUdaMWJtTjBhVzl1SUNodVlXMWxLU0I3WEc0Z0lDQWdJQ0FnSUhKbGRIVnliaUJ5WldkcGMzUnllVnR1WVcxbFhUdGNiaUFnSUNCOU8xeHVYRzRnSUNBZ0x5b3FYRzRnSUNBZ0lDb2dRMmhsWTJzZ2RHaGxJR04xY25KbGJuUWdZMjl1ZEdWdWRDQnZaaUIwYUdVZ2NtVm5hWE4wY25rZ2IySnFaV04wTGx4dUlDQWdJQ0FxWEc0Z0lDQWdJQ29nVkdocGN5QnRaWFJvYjJRZ2FYTWdiMjVzZVNCaGRtRnBiR0ZpYkdVZ2QyaGxiaUIwYUdVZ1pXNW5hVzVsSUdWdWRtbHliMjV0Wlc1MElHbHpJSFIxY201bFpDQnBiblJ2SUdSbGRtVnNiM0J0Wlc1MExseHVJQ0FnSUNBcUwxeHVJQ0FnSUdWNGNHOXlkSE11WkdWaWRXY2dQU0JtZFc1amRHbHZiaUFvS1NCN1hHNGdJQ0FnSUNBZ0lHbG1JQ2hxYzJVdVkyOXlaUzVqYjI1bWFXY3VaMlYwS0NkbGJuWnBjbTl1YldWdWRDY3BJRDA5UFNBblpHVjJaV3h2Y0cxbGJuUW5LU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQnFjMlV1WTI5eVpTNWtaV0oxWnk1c2IyY29KMUpsWjJsemRISjVJRTlpYW1WamREb25MQ0J5WldkcGMzUnllU2s3WEc0Z0lDQWdJQ0FnSUgwZ1pXeHpaU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQjBhSEp2ZHlCdVpYY2dSWEp5YjNJb0oxUm9hWE1nWm5WdVkzUnBiMjRnYVhNZ2JtOTBJR0ZzYkc5M1pXUWdhVzRnWVNCd2NtOWtkV04wYVc5dUlHVnVkbWx5YjI1dFpXNTBMaWNwTzF4dUlDQWdJQ0FnSUNCOVhHNGdJQ0FnZlR0Y2JseHVmU2tvYW5ObExtTnZjbVV1Y21WbmFYTjBjbmtwTzF4dUlpd2lMeW9nTFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzFjYmlCeVpYRjFhWEpsTG1weklESXdNVGN0TURNdE1qaGNiaUJIWVcxaWFXOGdSMjFpU0Z4dUlHaDBkSEE2THk5M2QzY3VaMkZ0WW1sdkxtUmxYRzRnUTI5d2VYSnBaMmgwSUNoaktTQXlNREUzSUVkaGJXSnBieUJIYldKSVhHNGdVbVZzWldGelpXUWdkVzVrWlhJZ2RHaGxJRWRPVlNCSFpXNWxjbUZzSUZCMVlteHBZeUJNYVdObGJuTmxJQ2hXWlhKemFXOXVJRElwWEc0Z1cyaDBkSEE2THk5M2QzY3VaMjUxTG05eVp5OXNhV05sYm5ObGN5OW5jR3d0TWk0d0xtaDBiV3hkWEc0Z0xTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMxY2JpQXFMMXh1WEc0dktpcGNiaUFxSUVGemVXNWphSEp2Ym05MWN5Qk5iMlIxYkdVZ1RHOWhaR2x1WjF4dUlDcGNiaUFxSUZSb2FYTWdiVzlrZFd4bElHbHpJR0VnWm05eWF5QnZaaUJTWlhGMWFYSmxTbE1nZDJsMGFHOTFkQ0IwYUdVZ1FVMUVJR1oxYm1OMGFXOXVZV3hwZEhrdUlGUm9aU0JuYkc5aVlXd2dYQ0prWldacGJtVmNJaUJ0WlhSb2IyUWdhWE1nY21WdGIzWmxaQ0JoYzF4dUlDb2dhWFFuY3lCdWIzUWdibVZqWlhOellYSjVJR0o1SUVwVElFVnVaMmx1WlM1Y2JpQXFYRzRnS2lCN1FHeHBibXNnYUhSMGNITTZMeTluYVhSb2RXSXVZMjl0TDNKbGNYVnBjbVZxY3k5eVpYRjFhWEpsYW5OOVhHNGdLbHh1SUNvZ1RtOTBJSFZ6YVc1bklITjBjbWxqZERvZ2RXNWxkbVZ1SUhOMGNtbGpkQ0J6ZFhCd2IzSjBJR2x1SUdKeWIzZHpaWEp6TENBak16a3lMQ0JoYm1RZ1kyRjFjMlZ6SUhCeWIySnNaVzF6SUhkcGRHZ2djbVZ4ZFdseVpXcHpMbVY0WldNb0tTOTBjbUZ1YzNCcGJHVnlYRzRnS2lCd2JIVm5hVzV6SUhSb1lYUWdiV0Y1SUc1dmRDQmlaU0J6ZEhKcFkzUXVYRzRnS2k5Y2JpaG1kVzVqZEdsdmJpQW9LU0I3WEc1Y2JpQWdJQ0F2THlBdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMxY2JpQWdJQ0F2THlCV1FWSkpRVUpNUlZOY2JpQWdJQ0F2THlBdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwZ1hHNWNiaUFnSUNCM2FXNWtiM2N1Y21WeGRXbHlaV3B6SUQwZ2RXNWtaV1pwYm1Wa08xeHVJQ0FnSUhkcGJtUnZkeTV5WlhGMWFYSmxJRDBnZFc1a1pXWnBibVZrTzF4dVhHNGdJQ0FnYkdWMElISmxjVHRjYmlBZ0lDQnNaWFFnY3p0Y2JpQWdJQ0JzWlhRZ2FHVmhaRHRjYmlBZ0lDQnNaWFFnWW1GelpVVnNaVzFsYm5RN1hHNGdJQ0FnYkdWMElHUmhkR0ZOWVdsdU8xeHVJQ0FnSUd4bGRDQnpjbU03WEc0Z0lDQWdiR1YwSUdsdWRHVnlZV04wYVhabFUyTnlhWEIwTzF4dUlDQWdJR3hsZENCdFlXbHVVMk55YVhCME8xeHVJQ0FnSUd4bGRDQnpkV0pRWVhSb08xeHVJQ0FnSUd4bGRDQjJaWEp6YVc5dUlEMGdKekl1TVM0eU1pYzdYRzRnSUNBZ2JHVjBJR3B6VTNWbVptbDRVbVZuUlhod0lEMGdMMXhjTG1wekpDODdYRzRnSUNBZ2JHVjBJR04xY25KRWFYSlNaV2RGZUhBZ1BTQXZYbHhjTGx4Y0x5ODdYRzRnSUNBZ2JHVjBJRzl3SUQwZ1QySnFaV04wTG5CeWIzUnZkSGx3WlR0Y2JpQWdJQ0JzWlhRZ2IzTjBjbWx1WnlBOUlHOXdMblJ2VTNSeWFXNW5PMXh1SUNBZ0lHeGxkQ0JvWVhOUGQyNGdQU0J2Y0M1b1lYTlBkMjVRY205d1pYSjBlVHRjYmlBZ0lDQnNaWFFnYVhOQ2NtOTNjMlZ5SUQwZ0lTRW9kSGx3Wlc5bUlIZHBibVJ2ZHlBaFBUMGdKM1Z1WkdWbWFXNWxaQ2NnSmlZZ2RIbHdaVzltSUc1aGRtbG5ZWFJ2Y2lBaFBUMGdKM1Z1WkdWbWFXNWxaQ2NnSmlZZ2QybHVaRzkzTG1SdlkzVnRaVzUwS1R0Y2JpQWdJQ0JzWlhRZ2FYTlhaV0pYYjNKclpYSWdQU0FoYVhOQ2NtOTNjMlZ5SUNZbUlIUjVjR1Z2WmlCcGJYQnZjblJUWTNKcGNIUnpJQ0U5UFNBbmRXNWtaV1pwYm1Wa0p6dGNiaUFnSUNBdkx5QlFVek1nYVc1a2FXTmhkR1Z6SUd4dllXUmxaQ0JoYm1RZ1kyOXRjR3hsZEdVc0lHSjFkQ0J1WldWa0lIUnZJSGRoYVhRZ1ptOXlJR052YlhCc1pYUWdjM0JsWTJsbWFXTmhiR3g1TGlCVFpYRjFaVzVqWlNCcGN5QW5iRzloWkdsdVp5Y3NJQ2RzYjJGa1pXUW5MQ0JjYmlBZ0lDQXZMeUJsZUdWamRYUnBiMjRnZEdobGJpQW5ZMjl0Y0d4bGRHVW5MaUJVYUdVZ1ZVRWdZMmhsWTJzZ2FYTWdkVzVtYjNKMGRXNWhkR1VzSUdKMWRDQnViM1FnYzNWeVpTQm9iM2NnZEc4Z1ptVmhkSFZ5WlNCMFpYTjBJSGN2YnlCallYVnphVzVuSUhCbGNtWWdhWE56ZFdWekxseHVJQ0FnSUd4bGRDQnlaV0ZrZVZKbFowVjRjQ0E5SUdselFuSnZkM05sY2lBbUppQnVZWFpwWjJGMGIzSXVjR3hoZEdadmNtMGdQVDA5SUNkUVRFRlpVMVJCVkVsUFRpQXpKeUEvSUM5ZVkyOXRjR3hsZEdVa0x5QTZJQzllS0dOdmJYQnNaWFJsZkd4dllXUmxaQ2trTHp0Y2JpQWdJQ0JzWlhRZ1pHVm1RMjl1ZEdWNGRFNWhiV1VnUFNBblh5YzdYRzRnSUNBZ0x5OGdUMmdnZEdobElIUnlZV2RsWkhrc0lHUmxkR1ZqZEdsdVp5QnZjR1Z5WVM0Z1UyVmxJSFJvWlNCMWMyRm5aU0J2WmlCcGMwOXdaWEpoSUdadmNpQnlaV0Z6YjI0dVhHNGdJQ0FnYkdWMElHbHpUM0JsY21FZ1BTQjBlWEJsYjJZZ2IzQmxjbUVnSVQwOUlDZDFibVJsWm1sdVpXUW5JQ1ltSUc5d1pYSmhMblJ2VTNSeWFXNW5LQ2tnUFQwOUlDZGJiMkpxWldOMElFOXdaWEpoWFNjN1hHNGdJQ0FnYkdWMElHTnZiblJsZUhSeklEMGdlMzA3WEc0Z0lDQWdiR1YwSUdObVp5QTlJSHQ5TzF4dUlDQWdJR3hsZENCbmJHOWlZV3hFWldaUmRXVjFaU0E5SUZ0ZE8xeHVJQ0FnSUd4bGRDQjFjMlZKYm5SbGNtRmpkR2wyWlNBOUlHWmhiSE5sTzF4dVhHNGdJQ0FnTHk4Z0xTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdFhHNGdJQ0FnTHk4Z1JsVk9RMVJKVDA1VFhHNGdJQ0FnTHk4Z0xTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdElGeHVYRzRnSUNBZ0x5b3FYRzRnSUNBZ0lDb2dRMmhsWTJzZ2QyaGxkR2hsY2lCMllXeDFaU0JwY3lCaElHWjFibU4wYVc5dUxseHVJQ0FnSUNBcVhHNGdJQ0FnSUNvZ1FIQmhjbUZ0SUhzcWZTQnBkQ0JXWVd4MVpTQjBieUJpWlNCamFHVmphMlZrTGx4dUlDQWdJQ0FxWEc0Z0lDQWdJQ29nUUhKbGRIVnliaUI3WW05dmJHVmhibjBnVW1WMGRYSnVjeUIwYUdVZ2RtRnNhV1JoZEdsdmJpQnlaWE4xYkhRdVhHNGdJQ0FnSUNvdlhHNGdJQ0FnWm5WdVkzUnBiMjRnYVhOR2RXNWpkR2x2YmlocGRDa2dlMXh1SUNBZ0lDQWdJQ0J5WlhSMWNtNGdiM04wY21sdVp5NWpZV3hzS0dsMEtTQTlQVDBnSjF0dlltcGxZM1FnUm5WdVkzUnBiMjVkSnp0Y2JpQWdJQ0I5WEc1Y2JpQWdJQ0F2S2lwY2JpQWdJQ0FnS2lCRGFHVmpheUIzYUdWMGFHVnlJSFpoYkhWbElHbHpJR0Z1SUdGeWNtRjVMbHh1SUNBZ0lDQXFYRzRnSUNBZ0lDb2dRSEJoY21GdElIc3FmU0JwZENCV1lXeDFaU0IwYnlCaVpTQmphR1ZqYTJWa0xseHVJQ0FnSUNBcVhHNGdJQ0FnSUNvZ1FISmxkSFZ5YmlCN1ltOXZiR1ZoYm4wZ1VtVjBkWEp1Y3lCMGFHVWdkbUZzYVdSaGRHbHZiaUJ5WlhOMWJIUXVYRzRnSUNBZ0lDb3ZYRzRnSUNBZ1puVnVZM1JwYjI0Z2FYTkJjbkpoZVNocGRDa2dlMXh1SUNBZ0lDQWdJQ0J5WlhSMWNtNGdiM04wY21sdVp5NWpZV3hzS0dsMEtTQTlQVDBnSjF0dlltcGxZM1FnUVhKeVlYbGRKenRjYmlBZ0lDQjlYRzVjYmlBZ0lDQXZLaXBjYmlBZ0lDQWdLaUJJWld4d1pYSWdablZ1WTNScGIyNGdabTl5SUdsMFpYSmhkR2x1WnlCdmRtVnlJR0Z1SUdGeWNtRjVMbHh1SUNBZ0lDQXFYRzRnSUNBZ0lDb2dTV1lnZEdobElHWjFibU1nY21WMGRYSnVjeUJoSUhSeWRXVWdkbUZzZFdVc0lHbDBJSGRwYkd3Z1luSmxZV3NnYjNWMElHOW1JSFJvWlNCc2IyOXdMbHh1SUNBZ0lDQXFMMXh1SUNBZ0lHWjFibU4wYVc5dUlHVmhZMmdvWVhKNUxDQm1kVzVqS1NCN1hHNGdJQ0FnSUNBZ0lHbG1JQ2hoY25rcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUdadmNpQW9iR1YwSUdrZ1BTQXdPeUJwSUR3Z1lYSjVMbXhsYm1kMGFEc2dhU0FyUFNBeEtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lnS0dGeWVWdHBYU0FtSmlCbWRXNWpLR0Z5ZVZ0cFhTd2dhU3dnWVhKNUtTa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCaWNtVmhhenRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJSDFjYmlBZ0lDQjlYRzVjYmlBZ0lDQXZLaXBjYmlBZ0lDQWdLaUJJWld4d1pYSWdablZ1WTNScGIyNGdabTl5SUdsMFpYSmhkR2x1WnlCdmRtVnlJR0Z1SUdGeWNtRjVJR0poWTJ0M1lYSmtjeTVjYmlBZ0lDQWdLbHh1SUNBZ0lDQXFJRWxtSUhSb1pTQm1kVzVqSUhKbGRIVnlibk1nWVNCMGNuVmxJSFpoYkhWbExDQnBkQ0IzYVd4c0lHSnlaV0ZySUc5MWRDQnZaaUIwYUdVZ2JHOXZjQzVjYmlBZ0lDQWdLaTljYmlBZ0lDQm1kVzVqZEdsdmJpQmxZV05vVW1WMlpYSnpaU2hoY25rc0lHWjFibU1wSUh0Y2JpQWdJQ0FnSUNBZ2FXWWdLR0Z5ZVNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnYkdWMElHazdYRzRnSUNBZ0lDQWdJQ0FnSUNCbWIzSWdLR2tnUFNCaGNua3ViR1Z1WjNSb0lDMGdNVHNnYVNBK0lDMHhPeUJwSUMwOUlERXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb1lYSjVXMmxkSUNZbUlHWjFibU1vWVhKNVcybGRMQ0JwTENCaGNua3BLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHSnlaV0ZyTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnZlZ4dUlDQWdJSDFjYmx4dUlDQWdJQzhxS2x4dUlDQWdJQ0FxSUVOb1pXTnJJSGRvWlhSb1pYSWdZVzRnYjJKcVpXTjBJR2hoY3lCaElITndaV05wWm1saklIQnliM0JsY25SNUxseHVJQ0FnSUNBcVhHNGdJQ0FnSUNvZ1FIQmhjbUZ0SUh0UFltcGxZM1I5SUc5aWFpQlBZbXBsWTNRZ2RHOGdZbVVnWTJobFkydGxaQzVjYmlBZ0lDQWdLaUJBY0dGeVlXMGdlMU4wY21sdVozMGdjSEp2Y0NCUWNtOXdaWEowZVNCdVlXMWxJSFJ2SUdKbElHTm9aV05yWldRdVhHNGdJQ0FnSUNwY2JpQWdJQ0FnS2lCQWNtVjBkWEp1SUh0Q2IyOXNaV0Z1ZlNCU1pYUjFjbTV6SUhSb1pTQjJZV3hwWkdGMGFXOXVJSEpsYzNWc2RDNWNiaUFnSUNBZ0tpOWNiaUFnSUNCbWRXNWpkR2x2YmlCb1lYTlFjbTl3S0c5aWFpd2djSEp2Y0NrZ2UxeHVJQ0FnSUNBZ0lDQnlaWFIxY200Z2FHRnpUM2R1TG1OaGJHd29iMkpxTENCd2NtOXdLVHRjYmlBZ0lDQjlYRzVjYmlBZ0lDQXZLaXBjYmlBZ0lDQWdLaUJEYUdWamF5QnBaaUJoYmlCdlltcGxZM1FnYUdGeklHRWdjSEp2Y0dWeWRIa2dZVzVrSUdsbUlIUm9ZWFFnY0hKdmNHVnlkSGtnWTI5dWRHRnBibk1nWVNCMGNuVjBhSGtnZG1Gc2RXVXVYRzRnSUNBZ0lDcGNiaUFnSUNBZ0tpQkFjR0Z5WVcwZ2UwOWlhbVZqZEgwZ2IySnFJRTlpYW1WamRDQjBieUJpWlNCamFHVmphMlZrTGx4dUlDQWdJQ0FxSUVCd1lYSmhiU0I3VTNSeWFXNW5mU0J3Y205d0lGQnliM0JsY25SNUlHNWhiV1VnZEc4Z1ltVWdZMmhsWTJ0bFpDNWNiaUFnSUNBZ0tseHVJQ0FnSUNBcUlFQnlaWFIxY200Z2UwSnZiMnhsWVc1OUlGSmxkSFZ5Ym5NZ2RHaGxJSFpoYkdsa1lYUnBiMjRnY21WemRXeDBMbHh1SUNBZ0lDQXFMMXh1SUNBZ0lHWjFibU4wYVc5dUlHZGxkRTkzYmlodlltb3NJSEJ5YjNBcElIdGNiaUFnSUNBZ0lDQWdjbVYwZFhKdUlHaGhjMUJ5YjNBb2IySnFMQ0J3Y205d0tTQW1KaUJ2WW1wYmNISnZjRjA3WEc0Z0lDQWdmVnh1WEc0Z0lDQWdMeW9xWEc0Z0lDQWdJQ29nUTNsamJHVnpJRzkyWlhJZ2NISnZjR1Z5ZEdsbGN5QnBiaUJoYmlCdlltcGxZM1FnWVc1a0lHTmhiR3h6SUdFZ1puVnVZM1JwYjI0Z1ptOXlJR1ZoWTJnZ2NISnZjR1Z5ZEhrZ2RtRnNkV1V1WEc0Z0lDQWdJQ3BjYmlBZ0lDQWdLaUJKWmlCMGFHVWdablZ1WTNScGIyNGdjbVYwZFhKdWN5QmhJSFJ5ZFhSb2VTQjJZV3gxWlN3Z2RHaGxiaUIwYUdVZ2FYUmxjbUYwYVc5dUlHbHpJSE4wYjNCd1pXUXVYRzRnSUNBZ0lDb3ZYRzRnSUNBZ1puVnVZM1JwYjI0Z1pXRmphRkJ5YjNBb2IySnFMQ0JtZFc1aktTQjdYRzRnSUNBZ0lDQWdJR3hsZENCd2NtOXdPMXh1SUNBZ0lDQWdJQ0JtYjNJZ0tIQnliM0FnYVc0Z2IySnFLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQnBaaUFvYUdGelVISnZjQ2h2WW1vc0lIQnliM0FwS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWWdLR1oxYm1Nb2IySnFXM0J5YjNCZExDQndjbTl3S1NrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JpY21WaGF6dGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNCOVhHNGdJQ0FnSUNBZ0lIMWNiaUFnSUNCOVhHNWNiaUFnSUNBdktpcGNiaUFnSUNBZ0tpQlRhVzF3YkdVZ1puVnVZM1JwYjI0Z2RHOGdiV2w0SUdsdUlIQnliM0JsY25ScFpYTWdabkp2YlNCemIzVnlZMlVnYVc1MGJ5QjBZWEpuWlhRc0lHSjFkQ0J2Ym14NUlHbG1JSFJoY21kbGRDQmtiMlZ6SUc1dmRDQmhiSEpsWVdSNUlHaGhkbVVnWVZ4dUlDQWdJQ0FxSUhCeWIzQmxjblI1SUc5bUlIUm9aU0J6WVcxbElHNWhiV1V1WEc0Z0lDQWdJQ292WEc0Z0lDQWdablZ1WTNScGIyNGdiV2w0YVc0b2RHRnlaMlYwTENCemIzVnlZMlVzSUdadmNtTmxMQ0JrWldWd1UzUnlhVzVuVFdsNGFXNHBJSHRjYmlBZ0lDQWdJQ0FnYVdZZ0tITnZkWEpqWlNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnWldGamFGQnliM0FvYzI5MWNtTmxMQ0JtZFc1amRHbHZiaUFvZG1Gc2RXVXNJSEJ5YjNBcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaUFvWm05eVkyVWdmSHdnSVdoaGMxQnliM0FvZEdGeVoyVjBMQ0J3Y205d0tTa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9aR1ZsY0ZOMGNtbHVaMDFwZUdsdUlDWW1JSFI1Y0dWdlppQjJZV3gxWlNBOVBUMGdKMjlpYW1WamRDY2dKaVlnZG1Gc2RXVWdKaVpjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0ZwYzBGeWNtRjVLSFpoYkhWbEtTQW1KaUFoYVhOR2RXNWpkR2x2YmloMllXeDFaU2tnSmlaY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNFb2RtRnNkV1VnYVc1emRHRnVZMlZ2WmlCU1pXZEZlSEFwS1NCN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2doZEdGeVoyVjBXM0J5YjNCZEtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZEdGeVoyVjBXM0J5YjNCZElEMGdlMzA3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J0YVhocGJpaDBZWEpuWlhSYmNISnZjRjBzSUhaaGJIVmxMQ0JtYjNKalpTd2daR1ZsY0ZOMGNtbHVaMDFwZUdsdUtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlNCbGJITmxJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFJoY21kbGRGdHdjbTl3WFNBOUlIWmhiSFZsTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0FnSUNBZ2ZTazdYRzRnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnY21WMGRYSnVJSFJoY21kbGREdGNiaUFnSUNCOVhHNWNiaUFnSUNBdkx5QlRhVzFwYkdGeUlIUnZJRVoxYm1OMGFXOXVMbkJ5YjNSdmRIbHdaUzVpYVc1a0xDQmlkWFFnZEdobElDZDBhR2x6SnlCdlltcGxZM1FnYVhNZ2MzQmxZMmxtYVdWa0lHWnBjbk4wTENCemFXNWpaU0JwZENCcGN5QmxZWE5wWlhJZ2RHOGdjbVZoWkM5bWFXZDFjbVVnWEc0Z0lDQWdMeThnYjNWMElIZG9ZWFFnSjNSb2FYTW5JSGRwYkd3Z1ltVXVYRzRnSUNBZ1puVnVZM1JwYjI0Z1ltbHVaQ2h2WW1vc0lHWnVLU0I3WEc0Z0lDQWdJQ0FnSUhKbGRIVnliaUJtZFc1amRHbHZiaUFvS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0J5WlhSMWNtNGdabTR1WVhCd2JIa29iMkpxTENCaGNtZDFiV1Z1ZEhNcE8xeHVJQ0FnSUNBZ0lDQjlPMXh1SUNBZ0lIMWNibHh1SUNBZ0lHWjFibU4wYVc5dUlITmpjbWx3ZEhNb0tTQjdYRzRnSUNBZ0lDQWdJSEpsZEhWeWJpQmtiMk4xYldWdWRDNW5aWFJGYkdWdFpXNTBjMEo1VkdGblRtRnRaU2duYzJOeWFYQjBKeWs3WEc0Z0lDQWdmVnh1WEc0Z0lDQWdablZ1WTNScGIyNGdaR1ZtWVhWc2RFOXVSWEp5YjNJb1pYSnlLU0I3WEc0Z0lDQWdJQ0FnSUhSb2NtOTNJR1Z5Y2p0Y2JpQWdJQ0I5WEc1Y2JpQWdJQ0F2THlCQmJHeHZkeUJuWlhSMGFXNW5JR0VnWjJ4dlltRnNJSFJvWVhRZ2FYTWdaWGh3Y21WemMyVmtJR2x1SUdSdmRDQnViM1JoZEdsdmJpd2diR2xyWlNBbllTNWlMbU1uTGx4dUlDQWdJR1oxYm1OMGFXOXVJR2RsZEVkc2IySmhiQ2gyWVd4MVpTa2dlMXh1SUNBZ0lDQWdJQ0JwWmlBb0lYWmhiSFZsS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0J5WlhSMWNtNGdkbUZzZFdVN1hHNGdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdiR1YwSUdjZ1BTQm5iRzlpWVd3N1hHNGdJQ0FnSUNBZ0lHVmhZMmdvZG1Gc2RXVXVjM0JzYVhRb0p5NG5LU3dnWm5WdVkzUnBiMjRnS0hCaGNuUXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lHY2dQU0JuVzNCaGNuUmRPMXh1SUNBZ0lDQWdJQ0I5S1R0Y2JpQWdJQ0FnSUNBZ2NtVjBkWEp1SUdjN1hHNGdJQ0FnZlZ4dVhHNGdJQ0FnTHlvcVhHNGdJQ0FnSUNvZ1EyOXVjM1J5ZFdOMGN5QmhiaUJsY25KdmNpQjNhWFJvSUdFZ2NHOXBiblJsY2lCMGJ5QmhiaUJWVWt3Z2QybDBhQ0J0YjNKbElHbHVabTl5YldGMGFXOXVMbHh1SUNBZ0lDQXFYRzRnSUNBZ0lDb2dRSEJoY21GdElIdFRkSEpwYm1kOUlHbGtJRlJvWlNCbGNuSnZjaUJKUkNCMGFHRjBJRzFoY0hNZ2RHOGdZVzRnU1VRZ2IyNGdZU0IzWldJZ2NHRm5aUzVjYmlBZ0lDQWdLaUJBY0dGeVlXMGdlMU4wY21sdVozMGdiWE5uSUVoMWJXRnVJSEpsWVdSaFlteGxJR1Z5Y205eUxseHVJQ0FnSUNBcUlFQndZWEpoYlNCN1JYSnliM0o5SUZ0bGNuSmRJRlJvWlNCdmNtbG5hVzVoYkNCbGNuSnZjaXdnYVdZZ2RHaGxjbVVnYVhNZ2IyNWxMbHh1SUNBZ0lDQXFYRzRnSUNBZ0lDb2dRSEpsZEhWeWJpQjdSWEp5YjNKOVhHNGdJQ0FnSUNvdlhHNGdJQ0FnWm5WdVkzUnBiMjRnYldGclpVVnljbTl5S0dsa0xDQnRjMmNzSUdWeWNpd2djbVZ4ZFdseVpVMXZaSFZzWlhNcElIdGNiaUFnSUNBZ0lDQWdZMjl1YzNRZ1pYSnliM0lnUFNCdVpYY2dSWEp5YjNJb2JYTm5JQ3NnSjF4Y2JtaDBkSEE2THk5eVpYRjFhWEpsYW5NdWIzSm5MMlJ2WTNNdlpYSnliM0p6TG1oMGJXd2pKeUFySUdsa0tUdGNibHh1SUNBZ0lDQWdJQ0JsY25KdmNpNXlaWEYxYVhKbFZIbHdaU0E5SUdsa08xeHVJQ0FnSUNBZ0lDQmxjbkp2Y2k1eVpYRjFhWEpsVFc5a2RXeGxjeUE5SUhKbGNYVnBjbVZOYjJSMWJHVnpPMXh1WEc0Z0lDQWdJQ0FnSUdsbUlDaGxjbklwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJR1Z5Y205eUxtOXlhV2RwYm1Gc1JYSnliM0lnUFNCbGNuSTdYRzRnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNCeVpYUjFjbTRnWlhKeWIzSTdYRzRnSUNBZ2ZWeHVYRzRnSUNBZ2FXWWdLSFI1Y0dWdlppQjNhVzVrYjNjdWNtVnhkV2x5WldweklDRTlQU0FuZFc1a1pXWnBibVZrSnlrZ2UxeHVJQ0FnSUNBZ0lDQnBaaUFvYVhOR2RXNWpkR2x2YmloM2FXNWtiM2N1Y21WeGRXbHlaV3B6S1NrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1JHOGdibTkwSUc5MlpYSjNjbWwwWlNCaGJpQmxlR2x6ZEdsdVp5QnlaWEYxYVhKbGFuTWdhVzV6ZEdGdVkyVXVYRzRnSUNBZ0lDQWdJQ0FnSUNCeVpYUjFjbTQ3WEc0Z0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ1kyWm5JRDBnZDJsdVpHOTNMbkpsY1hWcGNtVnFjenRjYmlBZ0lDQWdJQ0FnZDJsdVpHOTNMbkpsY1hWcGNtVnFjeUE5SUhWdVpHVm1hVzVsWkR0Y2JpQWdJQ0I5WEc1Y2JpQWdJQ0F2THlCQmJHeHZkeUJtYjNJZ1lTQnlaWEYxYVhKbElHTnZibVpwWnlCdlltcGxZM1JjYmlBZ0lDQnBaaUFvZEhsd1pXOW1JSGRwYm1SdmR5NXlaWEYxYVhKbElDRTlQU0FuZFc1a1pXWnBibVZrSnlBbUppQWhhWE5HZFc1amRHbHZiaWgzYVc1a2IzY3VjbVZ4ZFdseVpTa3BJSHRjYmlBZ0lDQWdJQ0FnTHk4Z1lYTnpkVzFsSUdsMElHbHpJR0VnWTI5dVptbG5JRzlpYW1WamRDNWNiaUFnSUNBZ0lDQWdZMlpuSUQwZ2QybHVaRzkzTG5KbGNYVnBjbVU3WEc0Z0lDQWdJQ0FnSUhkcGJtUnZkeTV5WlhGMWFYSmxJRDBnZFc1a1pXWnBibVZrTzF4dUlDQWdJSDFjYmx4dUlDQWdJR1oxYm1OMGFXOXVJRzVsZDBOdmJuUmxlSFFvWTI5dWRHVjRkRTVoYldVcElIdGNiaUFnSUNBZ0lDQWdiR1YwSUdsdVEyaGxZMnRNYjJGa1pXUXNJRTF2WkhWc1pTd2dZMjl1ZEdWNGRDd2dhR0Z1Wkd4bGNuTXNYRzRnSUNBZ0lDQWdJQ0FnSUNCamFHVmphMHh2WVdSbFpGUnBiV1Z2ZFhSSlpDeGNiaUFnSUNBZ0lDQWdJQ0FnSUdOdmJtWnBaeUE5SUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QkVaV1poZFd4MGN5NGdSRzhnYm05MElITmxkQ0JoSUdSbFptRjFiSFFnWm05eUlHMWhjRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUdOdmJtWnBaeUIwYnlCemNHVmxaQ0IxY0NCdWIzSnRZV3hwZW1Vb0tTd2dkMmhwWTJoY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QjNhV3hzSUhKMWJpQm1ZWE4wWlhJZ2FXWWdkR2hsY21VZ2FYTWdibThnWkdWbVlYVnNkQzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IzWVdsMFUyVmpiMjVrY3pvZ055eGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmlZWE5sVlhKc09pQW5MaThuTEZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhCaGRHaHpPaUI3ZlN4Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCaWRXNWtiR1Z6T2lCN2ZTeGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQndhMmR6T2lCN2ZTeGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnphR2x0T2lCN2ZTeGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmpiMjVtYVdjNklIdDlYRzRnSUNBZ0lDQWdJQ0FnSUNCOUxGeHVJQ0FnSUNBZ0lDQWdJQ0FnY21WbmFYTjBjbmtnUFNCN2ZTeGNiaUFnSUNBZ0lDQWdJQ0FnSUM4dklISmxaMmx6ZEhKNUlHOW1JR3AxYzNRZ1pXNWhZbXhsWkNCdGIyUjFiR1Z6TENCMGJ5QnpjR1ZsWkZ4dUlDQWdJQ0FnSUNBZ0lDQWdMeThnWTNsamJHVWdZbkpsWVd0cGJtY2dZMjlrWlNCM2FHVnVJR3h2ZEhNZ2IyWWdiVzlrZFd4bGMxeHVJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1lYSmxJSEpsWjJsemRHVnlaV1FzSUdKMWRDQnViM1FnWVdOMGFYWmhkR1ZrTGx4dUlDQWdJQ0FnSUNBZ0lDQWdaVzVoWW14bFpGSmxaMmx6ZEhKNUlEMGdlMzBzWEc0Z0lDQWdJQ0FnSUNBZ0lDQjFibVJsWmtWMlpXNTBjeUE5SUh0OUxGeHVJQ0FnSUNBZ0lDQWdJQ0FnWkdWbVVYVmxkV1VnUFNCYlhTeGNiaUFnSUNBZ0lDQWdJQ0FnSUdSbFptbHVaV1FnUFNCN2ZTeGNiaUFnSUNBZ0lDQWdJQ0FnSUhWeWJFWmxkR05vWldRZ1BTQjdmU3hjYmlBZ0lDQWdJQ0FnSUNBZ0lHSjFibVJzWlhOTllYQWdQU0I3ZlN4Y2JpQWdJQ0FnSUNBZ0lDQWdJSEpsY1hWcGNtVkRiM1Z1ZEdWeUlEMGdNU3hjYmlBZ0lDQWdJQ0FnSUNBZ0lIVnVibTl5YldGc2FYcGxaRU52ZFc1MFpYSWdQU0F4TzF4dVhHNGdJQ0FnSUNBZ0lDOHFLbHh1SUNBZ0lDQWdJQ0FnS2lCVWNtbHRjeUIwYUdVZ0xpQmhibVFnTGk0Z1puSnZiU0JoYmlCaGNuSmhlU0J2WmlCd1lYUm9JSE5sWjIxbGJuUnpMbHh1SUNBZ0lDQWdJQ0FnS2x4dUlDQWdJQ0FnSUNBZ0tpQkpkQ0IzYVd4c0lHdGxaWEFnWVNCc1pXRmthVzVuSUhCaGRHZ2djMlZuYldWdWRDQnBaaUJoSUM0dUlIZHBiR3dnWW1WamIyMWxJSFJvWlNCbWFYSnpkQ0J3WVhSb0lITmxaMjFsYm5Rc0lIUnZJR2hsYkhBZ2QybDBhQ0J0YjJSMWJHVWdibUZ0WlZ4dUlDQWdJQ0FnSUNBZ0tpQnNiMjlyZFhCekxDQjNhR2xqYUNCaFkzUWdiR2xyWlNCd1lYUm9jeXdnWW5WMElHTmhiaUJpWlNCeVpXMWhjSEJsWkM0Z1FuVjBJSFJvWlNCbGJtUWdjbVZ6ZFd4MExDQmhiR3dnY0dGMGFITWdkR2hoZENCMWMyVWdkR2hwY3lCbWRXNWpkR2x2Ymx4dUlDQWdJQ0FnSUNBZ0tpQnphRzkxYkdRZ2JHOXZheUJ1YjNKdFlXeHBlbVZrTGx4dUlDQWdJQ0FnSUNBZ0tseHVJQ0FnSUNBZ0lDQWdLaUJPVDFSRk9pQjBhR2x6SUcxbGRHaHZaQ0JOVDBSSlJrbEZVeUIwYUdVZ2FXNXdkWFFnWVhKeVlYa3VYRzRnSUNBZ0lDQWdJQ0FxWEc0Z0lDQWdJQ0FnSUNBcUlFQndZWEpoYlNCN1FYSnlZWGw5SUdGeWVTQjBhR1VnWVhKeVlYa2diMllnY0dGMGFDQnpaV2R0Wlc1MGN5NWNiaUFnSUNBZ0lDQWdJQ292WEc0Z0lDQWdJQ0FnSUdaMWJtTjBhVzl1SUhSeWFXMUViM1J6S0dGeWVTa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ2JHVjBJR2tzSUhCaGNuUTdYRzRnSUNBZ0lDQWdJQ0FnSUNCbWIzSWdLR2tnUFNBd095QnBJRHdnWVhKNUxteGxibWQwYURzZ2FTc3JLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnY0dGeWRDQTlJR0Z5ZVZ0cFhUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaUFvY0dGeWRDQTlQVDBnSnk0bktTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdGeWVTNXpjR3hwWTJVb2FTd2dNU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHa2dMVDBnTVR0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOUlHVnNjMlVnYVdZZ0tIQmhjblFnUFQwOUlDY3VMaWNwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnU1dZZ1lYUWdkR2hsSUhOMFlYSjBMQ0J2Y2lCd2NtVjJhVzkxY3lCMllXeDFaU0JwY3lCemRHbHNiQ0F1TGl3Z0lHdGxaWEFnZEdobGJTQnpieUIwYUdGMElIZG9aVzRnWTI5dWRtVnlkR1ZrSUhSdklHRWdjR0YwYUNCcGRDQmNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z2JXRjVJSE4wYVd4c0lIZHZjbXNnZDJobGJpQmpiMjUyWlhKMFpXUWdkRzhnWVNCd1lYUm9MQ0JsZG1WdUlIUm9iM1ZuYUNCaGN5QmhiaUJKUkNCcGRDQnBjeUJzWlhOeklIUm9ZVzRnYVdSbFlXd3VJRWx1SUd4aGNtZGxjaUJjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdjRzlwYm5RZ2NtVnNaV0Z6WlhNc0lHMWhlU0JpWlNCaVpYUjBaWElnZEc4Z2FuVnpkQ0JyYVdOcklHOTFkQ0JoYmlCbGNuSnZjaTVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWWdLR2tnUFQwOUlEQWdmSHdnS0drZ1BUMDlJREVnSmlZZ1lYSjVXekpkSUQwOVBTQW5MaTRuS1NCOGZDQmhjbmxiYVNBdElERmRJRDA5UFNBbkxpNG5LU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JqYjI1MGFXNTFaVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZTQmxiSE5sSUdsbUlDaHBJRDRnTUNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1lYSjVMbk53YkdsalpTaHBJQzBnTVN3Z01pazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBJQzA5SURJN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNBdktpcGNiaUFnSUNBZ0lDQWdJQ29nUjJsMlpXNGdZU0J5Wld4aGRHbDJaU0J0YjJSMWJHVWdibUZ0WlN3Z2JHbHJaU0F1TDNOdmJXVjBhR2x1Wnl3Z2JtOXliV0ZzYVhwbElHbDBJSFJ2SUdFZ2NtVmhiQ0J1WVcxbElIUm9ZWFFnWTJGdUlHSmxJRzFoY0hCbFpDQjBieUJoSUhCaGRHZ3VYRzRnSUNBZ0lDQWdJQ0FxWEc0Z0lDQWdJQ0FnSUNBcUlFQndZWEpoYlNCN1UzUnlhVzVuZlNCdVlXMWxJSFJvWlNCeVpXeGhkR2wyWlNCdVlXMWxYRzRnSUNBZ0lDQWdJQ0FxSUVCd1lYSmhiU0I3VTNSeWFXNW5mU0JpWVhObFRtRnRaU0JoSUhKbFlXd2dibUZ0WlNCMGFHRjBJSFJvWlNCdVlXMWxJR0Z5WnlCcGN5QnlaV3hoZEdsMlpTQjBieTVjYmlBZ0lDQWdJQ0FnSUNvZ1FIQmhjbUZ0SUh0Q2IyOXNaV0Z1ZlNCaGNIQnNlVTFoY0NCaGNIQnNlU0IwYUdVZ2JXRndJR052Ym1acFp5QjBieUIwYUdVZ2RtRnNkV1V1SUZOb2IzVnNaQ0J2Ym14NUlHSmxJR1J2Ym1VZ2FXWWdkR2hwY3lCdWIzSnRZV3hwZW1GMGFXOXVJR2x6WEc0Z0lDQWdJQ0FnSUNBcUlHWnZjaUJoSUdSbGNHVnVaR1Z1WTNrZ1NVUXVYRzRnSUNBZ0lDQWdJQ0FxWEc0Z0lDQWdJQ0FnSUNBcUlFQnlaWFIxY200Z2UxTjBjbWx1WjMwZ2JtOXliV0ZzYVhwbFpDQnVZVzFsWEc0Z0lDQWdJQ0FnSUNBcUwxeHVJQ0FnSUNBZ0lDQm1kVzVqZEdsdmJpQnViM0p0WVd4cGVtVW9ibUZ0WlN3Z1ltRnpaVTVoYldVc0lHRndjR3g1VFdGd0tTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCc1pYUWdjR3RuVFdGcGJpd2diV0Z3Vm1Gc2RXVXNJRzVoYldWUVlYSjBjeXdnYVN3Z2Fpd2dibUZ0WlZObFoyMWxiblFzSUd4aGMzUkpibVJsZUN4Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCbWIzVnVaRTFoY0N3Z1ptOTFibVJKTENCbWIzVnVaRk4wWVhKTllYQXNJSE4wWVhKSkxDQnViM0p0WVd4cGVtVmtRbUZ6WlZCaGNuUnpMRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR0poYzJWUVlYSjBjeUE5SUNoaVlYTmxUbUZ0WlNBbUppQmlZWE5sVG1GdFpTNXpjR3hwZENnbkx5Y3BLU3hjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J0WVhBZ1BTQmpiMjVtYVdjdWJXRndMRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSE4wWVhKTllYQWdQU0J0WVhBZ0ppWWdiV0Z3V3ljcUoxMDdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDOHZJRUZrYW5WemRDQmhibmtnY21Wc1lYUnBkbVVnY0dGMGFITXVYRzRnSUNBZ0lDQWdJQ0FnSUNCcFppQW9ibUZ0WlNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHNWhiV1VnUFNCdVlXMWxMbk53YkdsMEtDY3ZKeWs3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYkdGemRFbHVaR1Y0SUQwZ2JtRnRaUzVzWlc1bmRHZ2dMU0F4TzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdTV1lnZDJGdWRHbHVaeUJ1YjJSbElFbEVJR052YlhCaGRHbGlhV3hwZEhrc0lITjBjbWx3SUM1cWN5Qm1jbTl0SUdWdVpDQnZaaUJKUkhNdUlFaGhkbVVnZEc4Z1pHOGdkR2hwY3lCb1pYSmxMQ0JoYm1RZ2JtOTBJR2x1SUZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklHNWhiV1ZVYjFWeWJDQmlaV05oZFhObElHNXZaR1VnWVd4c2IzZHpJR1ZwZEdobGNpQXVhbk1nYjNJZ2JtOXVJQzVxY3lCMGJ5QnRZWEFnZEc4Z2MyRnRaU0JtYVd4bExseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2hqYjI1bWFXY3VibTlrWlVsa1EyOXRjR0YwSUNZbUlHcHpVM1ZtWm1sNFVtVm5SWGh3TG5SbGMzUW9ibUZ0WlZ0c1lYTjBTVzVrWlhoZEtTa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCdVlXMWxXMnhoYzNSSmJtUmxlRjBnUFNCdVlXMWxXMnhoYzNSSmJtUmxlRjB1Y21Wd2JHRmpaU2hxYzFOMVptWnBlRkpsWjBWNGNDd2dKeWNwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJRk4wWVhKMGN5QjNhWFJvSUdFZ0p5NG5JSE52SUc1bFpXUWdkR2hsSUdKaGMyVk9ZVzFsWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYVdZZ0tHNWhiV1ZiTUYwdVkyaGhja0YwS0RBcElEMDlQU0FuTGljZ0ppWWdZbUZ6WlZCaGNuUnpLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJRU52Ym5abGNuUWdZbUZ6WlU1aGJXVWdkRzhnWVhKeVlYa3NJR0Z1WkNCc2IzQWdiMlptSUhSb1pTQnNZWE4wSUhCaGNuUXNJSE52SUhSb1lYUWdMaUJ0WVhSamFHVnpJSFJvWVhRZ0oyUnBjbVZqZEc5eWVTY2dZVzVrSUc1dmRDQmNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z2JtRnRaU0J2WmlCMGFHVWdZbUZ6WlU1aGJXVW5jeUJ0YjJSMWJHVXVJRVp2Y2lCcGJuTjBZVzVqWlN3Z1ltRnpaVTVoYldVZ2IyWWdKMjl1WlM5MGQyOHZkR2h5WldVbkxDQnRZWEJ6SUhSdklGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlBbmIyNWxMM1IzYnk5MGFISmxaUzVxY3ljc0lHSjFkQ0IzWlNCM1lXNTBJSFJvWlNCa2FYSmxZM1J2Y25rc0lDZHZibVV2ZEhkdkp5Qm1iM0lnZEdocGN5QnViM0p0WVd4cGVtRjBhVzl1TGx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnViM0p0WVd4cGVtVmtRbUZ6WlZCaGNuUnpJRDBnWW1GelpWQmhjblJ6TG5Oc2FXTmxLREFzSUdKaGMyVlFZWEowY3k1c1pXNW5kR2dnTFNBeEtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYm1GdFpTQTlJRzV2Y20xaGJHbDZaV1JDWVhObFVHRnlkSE11WTI5dVkyRjBLRzVoYldVcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFJ5YVcxRWIzUnpLRzVoYldVcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHNWhiV1VnUFNCdVlXMWxMbXB2YVc0b0p5OG5LVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0x5OGdRWEJ3YkhrZ2JXRndJR052Ym1acFp5QnBaaUJoZG1GcGJHRmliR1V1WEc0Z0lDQWdJQ0FnSUNBZ0lDQnBaaUFvWVhCd2JIbE5ZWEFnSmlZZ2JXRndJQ1ltSUNoaVlYTmxVR0Z5ZEhNZ2ZId2djM1JoY2sxaGNDa3BJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J1WVcxbFVHRnlkSE1nUFNCdVlXMWxMbk53YkdsMEtDY3ZKeWs3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCdmRYUmxja3h2YjNBNklHWnZjaUFvYVNBOUlHNWhiV1ZRWVhKMGN5NXNaVzVuZEdnN0lHa2dQaUF3T3lCcElDMDlJREVwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdibUZ0WlZObFoyMWxiblFnUFNCdVlXMWxVR0Z5ZEhNdWMyeHBZMlVvTUN3Z2FTa3VhbTlwYmlnbkx5Y3BPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2hpWVhObFVHRnlkSE1wSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklFWnBibVFnZEdobElHeHZibWRsYzNRZ1ltRnpaVTVoYldVZ2MyVm5iV1Z1ZENCdFlYUmphQ0JwYmlCMGFHVWdZMjl1Wm1sbkxpQlRieXdnWkc4Z2FtOXBibk1nYjI0Z2RHaGxJR0pwWjJkbGMzUWdkRzhnWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCemJXRnNiR1Z6ZENCc1pXNW5kR2h6SUc5bUlHSmhjMlZRWVhKMGN5NWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHWnZjaUFvYWlBOUlHSmhjMlZRWVhKMGN5NXNaVzVuZEdnN0lHb2dQaUF3T3lCcUlDMDlJREVwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnRZWEJXWVd4MVpTQTlJR2RsZEU5M2JpaHRZWEFzSUdKaGMyVlFZWEowY3k1emJHbGpaU2d3TENCcUtTNXFiMmx1S0Njdkp5a3BPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdZbUZ6WlU1aGJXVWdjMlZuYldWdWRDQm9ZWE1nWTI5dVptbG5MQ0JtYVc1a0lHbG1JR2wwSUdoaGN5QnZibVVnWm05eUlIUm9hWE1nYm1GdFpTNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb2JXRndWbUZzZFdVcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2JXRndWbUZzZFdVZ1BTQm5aWFJQZDI0b2JXRndWbUZzZFdVc0lHNWhiV1ZUWldkdFpXNTBLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lnS0cxaGNGWmhiSFZsS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJOWVhSamFDd2dkWEJrWVhSbElHNWhiV1VnZEc4Z2RHaGxJRzVsZHlCMllXeDFaUzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdadmRXNWtUV0Z3SUQwZ2JXRndWbUZzZFdVN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQm1iM1Z1WkVrZ1BTQnBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWW5KbFlXc2diM1YwWlhKTWIyOXdPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdRMmhsWTJzZ1ptOXlJR0VnYzNSaGNpQnRZWEFnYldGMFkyZ3NJR0oxZENCcWRYTjBJR2h2YkdRZ2IyNGdkRzhnYVhRc0lHbG1JSFJvWlhKbElHbHpJR0VnYzJodmNuUmxjaUJ6WldkdFpXNTBJRzFoZEdOb0lHeGhkR1Z5SUdsdUlGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCaElHMWhkR05vYVc1bklHTnZibVpwWnl3Z2RHaGxiaUJtWVhadmNpQnZkbVZ5SUhSb2FYTWdjM1JoY2lCdFlYQXVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUlDZ2habTkxYm1SVGRHRnlUV0Z3SUNZbUlITjBZWEpOWVhBZ0ppWWdaMlYwVDNkdUtITjBZWEpOWVhBc0lHNWhiV1ZUWldkdFpXNTBLU2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWm05MWJtUlRkR0Z5VFdGd0lEMGdaMlYwVDNkdUtITjBZWEpOWVhBc0lHNWhiV1ZUWldkdFpXNTBLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSE4wWVhKSklEMGdhVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtSUNnaFptOTFibVJOWVhBZ0ppWWdabTkxYm1SVGRHRnlUV0Z3S1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1p2ZFc1a1RXRndJRDBnWm05MWJtUlRkR0Z5VFdGd08xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JtYjNWdVpFa2dQU0J6ZEdGeVNUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb1ptOTFibVJOWVhBcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYm1GdFpWQmhjblJ6TG5Od2JHbGpaU2d3TENCbWIzVnVaRWtzSUdadmRXNWtUV0Z3S1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdibUZ0WlNBOUlHNWhiV1ZRWVhKMGN5NXFiMmx1S0Njdkp5azdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBdkx5QkpaaUIwYUdVZ2JtRnRaU0J3YjJsdWRITWdkRzhnWVNCd1lXTnJZV2RsSjNNZ2JtRnRaU3dnZFhObElIUm9aU0J3WVdOcllXZGxJRzFoYVc0Z2FXNXpkR1ZoWkM1Y2JpQWdJQ0FnSUNBZ0lDQWdJSEJyWjAxaGFXNGdQU0JuWlhSUGQyNG9ZMjl1Wm1sbkxuQnJaM01zSUc1aGJXVXBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQnlaWFIxY200Z2NHdG5UV0ZwYmlBL0lIQnJaMDFoYVc0Z09pQnVZVzFsTzF4dUlDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdablZ1WTNScGIyNGdjbVZ0YjNabFUyTnlhWEIwS0c1aGJXVXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2hwYzBKeWIzZHpaWElwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCbFlXTm9LSE5qY21sd2RITW9LU3dnWm5WdVkzUnBiMjRnS0hOamNtbHdkRTV2WkdVcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYVdZZ0tITmpjbWx3ZEU1dlpHVXVaMlYwUVhSMGNtbGlkWFJsS0Nka1lYUmhMWEpsY1hWcGNtVnRiMlIxYkdVbktTQTlQVDBnYm1GdFpTQW1KbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjMk55YVhCMFRtOWtaUzVuWlhSQmRIUnlhV0oxZEdVb0oyUmhkR0V0Y21WeGRXbHlaV052Ym5SbGVIUW5LU0E5UFQwZ1kyOXVkR1Y0ZEM1amIyNTBaWGgwVG1GdFpTa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjMk55YVhCMFRtOWtaUzV3WVhKbGJuUk9iMlJsTG5KbGJXOTJaVU5vYVd4a0tITmpjbWx3ZEU1dlpHVXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjbVYwZFhKdUlIUnlkV1U3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUdaMWJtTjBhVzl1SUdoaGMxQmhkR2hHWVd4c1ltRmpheWhwWkNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnYkdWMElIQmhkR2hEYjI1bWFXY2dQU0JuWlhSUGQyNG9ZMjl1Wm1sbkxuQmhkR2h6TENCcFpDazdYRzRnSUNBZ0lDQWdJQ0FnSUNCcFppQW9jR0YwYUVOdmJtWnBaeUFtSmlCcGMwRnljbUY1S0hCaGRHaERiMjVtYVdjcElDWW1JSEJoZEdoRGIyNW1hV2N1YkdWdVozUm9JRDRnTVNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJRkJ2Y0NCdlptWWdkR2hsSUdacGNuTjBJR0Z5Y21GNUlIWmhiSFZsTENCemFXNWpaU0JwZENCbVlXbHNaV1FzSUdGdVpDQnlaWFJ5ZVM1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCd1lYUm9RMjl1Wm1sbkxuTm9hV1owS0NrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1kyOXVkR1Y0ZEM1eVpYRjFhWEpsTG5WdVpHVm1LR2xrS1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJRU4xYzNSdmJTQnlaWEYxYVhKbElIUm9ZWFFnWkc5bGN5QnViM1FnWkc4Z2JXRndJSFJ5WVc1emJHRjBhVzl1TENCemFXNWpaU0JKUkNCcGN5QmNJbUZpYzI5c2RYUmxYQ0lzSUdGc2NtVmhaSGtnYldGd2NHVmtMM0psYzI5c2RtVmtMbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR052Ym5SbGVIUXViV0ZyWlZKbGNYVnBjbVVvYm5Wc2JDd2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCemEybHdUV0Z3T2lCMGNuVmxYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmU2tvVzJsa1hTazdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J5WlhSMWNtNGdkSEoxWlR0Y2JpQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lDOHZJRlIxY201eklHRWdjR3gxWjJsdUlYSmxjMjkxY21ObElIUnZJRnR3YkhWbmFXNHNJSEpsYzI5MWNtTmxYU0IzYVhSb0lIUm9aU0J3YkhWbmFXNGdZbVZwYm1jZ2RXNWtaV1pwYm1Wa0lHbG1JSFJvWlNCdVlXMWxJR1JwWkNCdWIzUWdhR0YyWlNCaElGeHVJQ0FnSUNBZ0lDQXZMeUJ3YkhWbmFXNGdjSEpsWm1sNExseHVJQ0FnSUNBZ0lDQm1kVzVqZEdsdmJpQnpjR3hwZEZCeVpXWnBlQ2h1WVcxbEtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCc1pYUWdjSEpsWm1sNExGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbHVaR1Y0SUQwZ2JtRnRaU0EvSUc1aGJXVXVhVzVrWlhoUFppZ25JU2NwSURvZ0xURTdYRzRnSUNBZ0lDQWdJQ0FnSUNCcFppQW9hVzVrWlhnZ1BpQXRNU2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhCeVpXWnBlQ0E5SUc1aGJXVXVjM1ZpYzNSeWFXNW5LREFzSUdsdVpHVjRLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J1WVcxbElEMGdibUZ0WlM1emRXSnpkSEpwYm1jb2FXNWtaWGdnS3lBeExDQnVZVzFsTG14bGJtZDBhQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNCeVpYUjFjbTRnVzNCeVpXWnBlQ3dnYm1GdFpWMDdYRzRnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNBdktpcGNiaUFnSUNBZ0lDQWdJQ29nUTNKbFlYUmxjeUJoSUcxdlpIVnNaU0J0WVhCd2FXNW5JSFJvWVhRZ2FXNWpiSFZrWlhNZ2NHeDFaMmx1SUhCeVpXWnBlQ3dnYlc5a2RXeGxJRzVoYldVc0lHRnVaQ0J3WVhSb0xpQkpaaUJ3WVhKbGJuUk5iMlIxYkdWTllYQWdhWE1nY0hKdmRtbGtaV1JjYmlBZ0lDQWdJQ0FnSUNvZ2FYUWdkMmxzYkNCaGJITnZJRzV2Y20xaGJHbDZaU0IwYUdVZ2JtRnRaU0IyYVdFZ2NtVnhkV2x5WlM1dWIzSnRZV3hwZW1Vb0tWeHVJQ0FnSUNBZ0lDQWdLbHh1SUNBZ0lDQWdJQ0FnS2lCQWNHRnlZVzBnZTFOMGNtbHVaMzBnYm1GdFpTQlVhR1VnYlc5a2RXeGxJRzVoYldVdVhHNGdJQ0FnSUNBZ0lDQXFJRUJ3WVhKaGJTQjdVM1J5YVc1bmZTQmJjR0Z5Wlc1MFRXOWtkV3hsVFdGd1hTQlFZWEpsYm5RZ2JXOWtkV3hsSUcxaGNDQm1iM0lnZEdobElHMXZaSFZzWlNCdVlXMWxMQ0IxYzJWa0lIUnZJSEpsYzI5c2RtVWdjbVZzWVhScGRtVWdibUZ0WlhNdVhHNGdJQ0FnSUNBZ0lDQXFJRUJ3WVhKaGJTQjdRbTl2YkdWaGJuMGdhWE5PYjNKdFlXeHBlbVZrSUVseklIUm9aU0JKUkNCaGJISmxZV1I1SUc1dmNtMWhiR2w2WldRL0lGUm9hWE1nYVhNZ2RISjFaU0JwWmlCMGFHbHpJR05oYkd3Z2FYTWdaRzl1WlNCbWIzSWdZU0JrWldacGJtVW9LVnh1SUNBZ0lDQWdJQ0FnS2lCdGIyUjFiR1VnU1VRdVhHNGdJQ0FnSUNBZ0lDQXFJRUJ3WVhKaGJTQjdRbTl2YkdWaGJuMGdZWEJ3YkhsTllYQTZJR0Z3Y0d4NUlIUm9aU0J0WVhBZ1kyOXVabWxuSUhSdklIUm9aU0JKUkM0Z1UyaHZkV3hrSUc5dWJIa2dZbVVnZEhKMVpTQnBaaUIwYUdseklHMWhjQ0JwY3lCbWIzSWdZU0JrWlhCbGJtUmxibU41TGx4dUlDQWdJQ0FnSUNBZ0tseHVJQ0FnSUNBZ0lDQWdLaUJBY21WMGRYSnVJSHRQWW1wbFkzUjlYRzRnSUNBZ0lDQWdJQ0FxTDF4dUlDQWdJQ0FnSUNCbWRXNWpkR2x2YmlCdFlXdGxUVzlrZFd4bFRXRndLRzVoYldVc0lIQmhjbVZ1ZEUxdlpIVnNaVTFoY0N3Z2FYTk9iM0p0WVd4cGVtVmtMQ0JoY0hCc2VVMWhjQ2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdiR1YwSUhWeWJDd2djR3gxWjJsdVRXOWtkV3hsTENCemRXWm1hWGdzSUc1aGJXVlFZWEowY3l4Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCd2NtVm1hWGdnUFNCdWRXeHNMRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSEJoY21WdWRFNWhiV1VnUFNCd1lYSmxiblJOYjJSMWJHVk5ZWEFnUHlCd1lYSmxiblJOYjJSMWJHVk5ZWEF1Ym1GdFpTQTZJRzUxYkd3c1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2IzSnBaMmx1WVd4T1lXMWxJRDBnYm1GdFpTeGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBjMFJsWm1sdVpTQTlJSFJ5ZFdVc1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2JtOXliV0ZzYVhwbFpFNWhiV1VnUFNBbkp6dGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0x5OGdTV1lnYm04Z2JtRnRaU3dnZEdobGJpQnBkQ0J0WldGdWN5QnBkQ0JwY3lCaElISmxjWFZwY21VZ1kyRnNiQ3dnWjJWdVpYSmhkR1VnWVc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQzh2SUdsdWRHVnlibUZzSUc1aGJXVXVYRzRnSUNBZ0lDQWdJQ0FnSUNCcFppQW9JVzVoYldVcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBjMFJsWm1sdVpTQTlJR1poYkhObE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHNWhiV1VnUFNBblgwQnlKeUFySUNoeVpYRjFhWEpsUTI5MWJuUmxjaUFyUFNBeEtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQWdJQ0FnYm1GdFpWQmhjblJ6SUQwZ2MzQnNhWFJRY21WbWFYZ29ibUZ0WlNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0J3Y21WbWFYZ2dQU0J1WVcxbFVHRnlkSE5iTUYwN1hHNGdJQ0FnSUNBZ0lDQWdJQ0J1WVcxbElEMGdibUZ0WlZCaGNuUnpXekZkTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb2NISmxabWw0S1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2NISmxabWw0SUQwZ2JtOXliV0ZzYVhwbEtIQnlaV1pwZUN3Z2NHRnlaVzUwVG1GdFpTd2dZWEJ3YkhsTllYQXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSEJzZFdkcGJrMXZaSFZzWlNBOUlHZGxkRTkzYmloa1pXWnBibVZrTENCd2NtVm1hWGdwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQXZMeUJCWTJOdmRXNTBJR1p2Y2lCeVpXeGhkR2wyWlNCd1lYUm9jeUJwWmlCMGFHVnlaU0JwY3lCaElHSmhjMlVnYm1GdFpTNWNiaUFnSUNBZ0lDQWdJQ0FnSUdsbUlDaHVZVzFsS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWWdLSEJ5WldacGVDa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9jR3gxWjJsdVRXOWtkV3hsSUNZbUlIQnNkV2RwYmsxdlpIVnNaUzV1YjNKdFlXeHBlbVVwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklGQnNkV2RwYmlCcGN5QnNiMkZrWldRc0lIVnpaU0JwZEhNZ2JtOXliV0ZzYVhwbElHMWxkR2h2WkM1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUc1dmNtMWhiR2w2WldST1lXMWxJRDBnY0d4MVoybHVUVzlrZFd4bExtNXZjbTFoYkdsNlpTaHVZVzFsTENCbWRXNWpkR2x2YmlBb2JtRnRaU2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lISmxkSFZ5YmlCdWIzSnRZV3hwZW1Vb2JtRnRaU3dnY0dGeVpXNTBUbUZ0WlN3Z1lYQndiSGxOWVhBcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZTazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgwZ1pXeHpaU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCSlppQnVaWE4wWldRZ2NHeDFaMmx1SUhKbFptVnlaVzVqWlhNc0lIUm9aVzRnWkc4Z2JtOTBJSFJ5ZVNCMGIxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdibTl5YldGc2FYcGxMQ0JoY3lCcGRDQjNhV3hzSUc1dmRDQnViM0p0WVd4cGVtVWdZMjl5Y21WamRHeDVMaUJVYUdselhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QndiR0ZqWlhNZ1lTQnlaWE4wY21samRHbHZiaUJ2YmlCeVpYTnZkWEpqWlVsa2N5d2dZVzVrSUhSb1pTQnNiMjVuWlhKY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklIUmxjbTBnYzI5c2RYUnBiMjRnYVhNZ2JtOTBJSFJ2SUc1dmNtMWhiR2w2WlNCMWJuUnBiQ0J3YkhWbmFXNXpJR0Z5WlZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z2JHOWhaR1ZrSUdGdVpDQmhiR3dnYm05eWJXRnNhWHBoZEdsdmJuTWdkRzhnWVd4c2IzY2dabTl5SUdGemVXNWpYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJzYjJGa2FXNW5JRzltSUdFZ2JHOWhaR1Z5SUhCc2RXZHBiaTRnUW5WMElHWnZjaUJ1YjNjc0lHWnBlR1Z6SUhSb1pWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdZMjl0Ylc5dUlIVnpaWE11SUVSbGRHRnBiSE1nYVc0Z0l6RXhNekZjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJRzV2Y20xaGJHbDZaV1JPWVcxbElEMGdibUZ0WlM1cGJtUmxlRTltS0NjaEp5a2dQVDA5SUMweElEOWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J1YjNKdFlXeHBlbVVvYm1GdFpTd2djR0Z5Wlc1MFRtRnRaU3dnWVhCd2JIbE5ZWEFwSURwY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnVZVzFsTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmU0JsYkhObElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1FTQnlaV2QxYkdGeUlHMXZaSFZzWlM1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdibTl5YldGc2FYcGxaRTVoYldVZ1BTQnViM0p0WVd4cGVtVW9ibUZ0WlN3Z2NHRnlaVzUwVG1GdFpTd2dZWEJ3YkhsTllYQXBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJRTV2Y20xaGJHbDZaV1FnYm1GdFpTQnRZWGtnWW1VZ1lTQndiSFZuYVc0Z1NVUWdaSFZsSUhSdklHMWhjQ0JqYjI1bWFXZGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1lYQndiR2xqWVhScGIyNGdhVzRnYm05eWJXRnNhWHBsTGlCVWFHVWdiV0Z3SUdOdmJtWnBaeUIyWVd4MVpYTWdiWFZ6ZEZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJoYkhKbFlXUjVJR0psSUc1dmNtMWhiR2w2WldRc0lITnZJR1J2SUc1dmRDQnVaV1ZrSUhSdklISmxaRzhnZEdoaGRDQndZWEowTGx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnVZVzFsVUdGeWRITWdQU0J6Y0d4cGRGQnlaV1pwZUNodWIzSnRZV3hwZW1Wa1RtRnRaU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIQnlaV1pwZUNBOUlHNWhiV1ZRWVhKMGMxc3dYVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2JtOXliV0ZzYVhwbFpFNWhiV1VnUFNCdVlXMWxVR0Z5ZEhOYk1WMDdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdselRtOXliV0ZzYVhwbFpDQTlJSFJ5ZFdVN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZFhKc0lEMGdZMjl1ZEdWNGRDNXVZVzFsVkc5VmNtd29ibTl5YldGc2FYcGxaRTVoYldVcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1NXWWdkR2hsSUdsa0lHbHpJR0VnY0d4MVoybHVJR2xrSUhSb1lYUWdZMkZ1Ym05MElHSmxJR1JsZEdWeWJXbHVaV1FnYVdZZ2FYUWdibVZsWkhOY2JpQWdJQ0FnSUNBZ0lDQWdJQzh2SUc1dmNtMWhiR2w2WVhScGIyNHNJSE4wWVcxd0lHbDBJSGRwZEdnZ1lTQjFibWx4ZFdVZ1NVUWdjMjhnZEhkdklHMWhkR05vYVc1bklISmxiR0YwYVhabFhHNGdJQ0FnSUNBZ0lDQWdJQ0F2THlCcFpITWdkR2hoZENCdFlYa2dZMjl1Wm14cFkzUWdZMkZ1SUdKbElITmxjR0Z5WVhSbExseHVJQ0FnSUNBZ0lDQWdJQ0FnYzNWbVptbDRJRDBnY0hKbFptbDRJQ1ltSUNGd2JIVm5hVzVOYjJSMWJHVWdKaVlnSVdselRtOXliV0ZzYVhwbFpDQS9YRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdKMTkxYm01dmNtMWhiR2w2WldRbklDc2dLSFZ1Ym05eWJXRnNhWHBsWkVOdmRXNTBaWElnS3owZ01Ta2dPbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ2NuTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0J5WlhSMWNtNGdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSEJ5WldacGVEb2djSEpsWm1sNExGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHNWhiV1U2SUc1dmNtMWhiR2w2WldST1lXMWxMRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSEJoY21WdWRFMWhjRG9nY0dGeVpXNTBUVzlrZFd4bFRXRndMRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFZ1Ym05eWJXRnNhWHBsWkRvZ0lTRnpkV1ptYVhnc1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RYSnNPaUIxY213c1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2IzSnBaMmx1WVd4T1lXMWxPaUJ2Y21sbmFXNWhiRTVoYldVc1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FYTkVaV1pwYm1VNklHbHpSR1ZtYVc1bExGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbGtPaUFvY0hKbFptbDRJRDljYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2NISmxabWw0SUNzZ0p5RW5JQ3NnYm05eWJXRnNhWHBsWkU1aGJXVWdPbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCdWIzSnRZV3hwZW1Wa1RtRnRaU2tnS3lCemRXWm1hWGhjYmlBZ0lDQWdJQ0FnSUNBZ0lIMDdYRzRnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNCbWRXNWpkR2x2YmlCblpYUk5iMlIxYkdVb1pHVndUV0Z3S1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0JzWlhRZ2FXUWdQU0JrWlhCTllYQXVhV1FzWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYlc5a0lEMGdaMlYwVDNkdUtISmxaMmx6ZEhKNUxDQnBaQ2s3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJR2xtSUNnaGJXOWtLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYlc5a0lEMGdjbVZuYVhOMGNubGJhV1JkSUQwZ2JtVjNJR052Ym5SbGVIUXVUVzlrZFd4bEtHUmxjRTFoY0NrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJSEpsZEhWeWJpQnRiMlE3WEc0Z0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQm1kVzVqZEdsdmJpQnZiaWhrWlhCTllYQXNJRzVoYldVc0lHWnVLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQnNaWFFnYVdRZ1BTQmtaWEJOWVhBdWFXUXNYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdiVzlrSUQwZ1oyVjBUM2R1S0hKbFoybHpkSEo1TENCcFpDazdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2hvWVhOUWNtOXdLR1JsWm1sdVpXUXNJR2xrS1NBbUpseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDZ2hiVzlrSUh4OElHMXZaQzVrWldacGJtVkZiV2wwUTI5dGNHeGxkR1VwS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWWdLRzVoYldVZ1BUMDlJQ2RrWldacGJtVmtKeWtnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQm1iaWhrWldacGJtVmtXMmxrWFNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQWdJQ0FnZlNCbGJITmxJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J0YjJRZ1BTQm5aWFJOYjJSMWJHVW9aR1Z3VFdGd0tUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaUFvYlc5a0xtVnljbTl5SUNZbUlHNWhiV1VnUFQwOUlDZGxjbkp2Y2ljcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWm00b2JXOWtMbVZ5Y205eUtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlJR1ZzYzJVZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J0YjJRdWIyNG9ibUZ0WlN3Z1ptNHBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUdaMWJtTjBhVzl1SUc5dVJYSnliM0lvWlhKeUxDQmxjbkppWVdOcktTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCc1pYUWdhV1J6SUQwZ1pYSnlMbkpsY1hWcGNtVk5iMlIxYkdWekxGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHNXZkR2xtYVdWa0lEMGdabUZzYzJVN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUdsbUlDaGxjbkppWVdOcktTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdaWEp5WW1GamF5aGxjbklwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdmU0JsYkhObElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmxZV05vS0dsa2N5d2dablZ1WTNScGIyNGdLR2xrS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR3hsZENCdGIyUWdQU0JuWlhSUGQyNG9jbVZuYVhOMGNua3NJR2xrS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lnS0cxdlpDa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnVTJWMElHVnljbTl5SUc5dUlHMXZaSFZzWlN3Z2MyOGdhWFFnYzJ0cGNITWdkR2x0Wlc5MWRDQmphR1ZqYTNNdVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCdGIyUXVaWEp5YjNJZ1BTQmxjbkk3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb2JXOWtMbVYyWlc1MGN5NWxjbkp2Y2lrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJRzV2ZEdsbWFXVmtJRDBnZEhKMVpUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J0YjJRdVpXMXBkQ2duWlhKeWIzSW5MQ0JsY25JcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlNrN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaUFvSVc1dmRHbG1hV1ZrS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSEpsY1M1dmJrVnljbTl5S0dWeWNpazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnTHlvcVhHNGdJQ0FnSUNBZ0lDQXFJRWx1ZEdWeWJtRnNJRzFsZEdodlpDQjBieUIwY21GdWMyWmxjaUJuYkc5aVlXeFJkV1YxWlNCcGRHVnRjeUIwYnlCMGFHbHpJR052Ym5SbGVIUW5jMXh1SUNBZ0lDQWdJQ0FnS2lCa1pXWlJkV1YxWlM1Y2JpQWdJQ0FnSUNBZ0lDb3ZYRzRnSUNBZ0lDQWdJR1oxYm1OMGFXOXVJSFJoYTJWSGJHOWlZV3hSZFdWMVpTZ3BJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDOHZJRkIxYzJnZ1lXeHNJSFJvWlNCbmJHOWlZV3hFWldaUmRXVjFaU0JwZEdWdGN5QnBiblJ2SUhSb1pTQmpiMjUwWlhoMEozTWdaR1ZtVVhWbGRXVmNiaUFnSUNBZ0lDQWdJQ0FnSUdsbUlDaG5iRzlpWVd4RVpXWlJkV1YxWlM1c1pXNW5kR2dwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCbFlXTm9LR2RzYjJKaGJFUmxabEYxWlhWbExDQm1kVzVqZEdsdmJpQW9jWFZsZFdWSmRHVnRLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHeGxkQ0JwWkNBOUlIRjFaWFZsU1hSbGJWc3dYVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWWdLSFI1Y0dWdlppQnBaQ0E5UFQwZ0ozTjBjbWx1WnljcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHTnZiblJsZUhRdVpHVm1VWFZsZFdWTllYQmJhV1JkSUQwZ2RISjFaVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JrWldaUmRXVjFaUzV3ZFhOb0tIRjFaWFZsU1hSbGJTazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWjJ4dlltRnNSR1ZtVVhWbGRXVWdQU0JiWFR0Y2JpQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lHaGhibVJzWlhKeklEMGdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0ozSmxjWFZwY21Vbk9pQm1kVzVqZEdsdmJpQW9iVzlrS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWWdLRzF2WkM1eVpYRjFhWEpsS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSEpsZEhWeWJpQnRiMlF1Y21WeGRXbHlaVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5SUdWc2MyVWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCeVpYUjFjbTRnS0cxdlpDNXlaWEYxYVhKbElEMGdZMjl1ZEdWNGRDNXRZV3RsVW1WeGRXbHlaU2h0YjJRdWJXRndLU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdmU3hjYmlBZ0lDQWdJQ0FnSUNBZ0lDZGxlSEJ2Y25Sekp6b2dablZ1WTNScGIyNGdLRzF2WkNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHMXZaQzUxYzJsdVowVjRjRzl5ZEhNZ1BTQjBjblZsTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUlDaHRiMlF1YldGd0xtbHpSR1ZtYVc1bEtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUlDaHRiMlF1Wlhod2IzSjBjeWtnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnY21WMGRYSnVJQ2hrWldacGJtVmtXMjF2WkM1dFlYQXVhV1JkSUQwZ2JXOWtMbVY0Y0c5eWRITXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOUlHVnNjMlVnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnY21WMGRYSnVJQ2h0YjJRdVpYaHdiM0owY3lBOUlHUmxabWx1WldSYmJXOWtMbTFoY0M1cFpGMGdQU0I3ZlNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlMRnh1SUNBZ0lDQWdJQ0FnSUNBZ0oyMXZaSFZzWlNjNklHWjFibU4wYVc5dUlDaHRiMlFwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9iVzlrTG0xdlpIVnNaU2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnlaWFIxY200Z2JXOWtMbTF2WkhWc1pUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlJR1ZzYzJVZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J5WlhSMWNtNGdLRzF2WkM1dGIyUjFiR1VnUFNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFpEb2diVzlrTG0xaGNDNXBaQ3hjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFZ5YVRvZ2JXOWtMbTFoY0M1MWNtd3NYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmpiMjVtYVdjNklHWjFibU4wYVc5dUlDZ3BJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCeVpYUjFjbTRnWjJWMFQzZHVLR052Ym1acFp5NWpiMjVtYVdjc0lHMXZaQzV0WVhBdWFXUXBJSHg4SUh0OU8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZTeGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHVjRjRzl5ZEhNNklHMXZaQzVsZUhCdmNuUnpJSHg4SUNodGIyUXVaWGh3YjNKMGN5QTlJSHQ5S1Z4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJSDA3WEc1Y2JpQWdJQ0FnSUNBZ1puVnVZM1JwYjI0Z1kyeGxZVzVTWldkcGMzUnllU2hwWkNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1EyeGxZVzRnZFhBZ2JXRmphR2x1WlhKNUlIVnpaV1FnWm05eUlIZGhhWFJwYm1jZ2JXOWtkV3hsY3k1Y2JpQWdJQ0FnSUNBZ0lDQWdJR1JsYkdWMFpTQnlaV2RwYzNSeWVWdHBaRjA3WEc0Z0lDQWdJQ0FnSUNBZ0lDQmtaV3hsZEdVZ1pXNWhZbXhsWkZKbFoybHpkSEo1VzJsa1hUdGNiaUFnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUdaMWJtTjBhVzl1SUdKeVpXRnJRM2xqYkdVb2JXOWtMQ0IwY21GalpXUXNJSEJ5YjJObGMzTmxaQ2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdiR1YwSUdsa0lEMGdiVzlrTG0xaGNDNXBaRHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdhV1lnS0cxdlpDNWxjbkp2Y2lrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHMXZaQzVsYldsMEtDZGxjbkp2Y2ljc0lHMXZaQzVsY25KdmNpazdYRzRnSUNBZ0lDQWdJQ0FnSUNCOUlHVnNjMlVnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhSeVlXTmxaRnRwWkYwZ1BTQjBjblZsTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdWaFkyZ29iVzlrTG1SbGNFMWhjSE1zSUdaMWJtTjBhVzl1SUNoa1pYQk5ZWEFzSUdrcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYkdWMElHUmxjRWxrSUQwZ1pHVndUV0Z3TG1sa0xGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1pHVndJRDBnWjJWMFQzZHVLSEpsWjJsemRISjVMQ0JrWlhCSlpDazdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdUMjVzZVNCbWIzSmpaU0IwYUdsdVozTWdkR2hoZENCb1lYWmxJRzV2ZENCamIyMXdiR1YwWldSY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnWW1WcGJtY2daR1ZtYVc1bFpDd2djMjhnYzNScGJHd2dhVzRnZEdobElISmxaMmx6ZEhKNUxGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCaGJtUWdiMjVzZVNCcFppQnBkQ0JvWVhNZ2JtOTBJR0psWlc0Z2JXRjBZMmhsWkNCMWNGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCcGJpQjBhR1VnYlc5a2RXeGxJR0ZzY21WaFpIa3VYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUlDaGtaWEFnSmlZZ0lXMXZaQzVrWlhCTllYUmphR1ZrVzJsZElDWW1JQ0Z3Y205alpYTnpaV1JiWkdWd1NXUmRLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb1oyVjBUM2R1S0hSeVlXTmxaQ3dnWkdWd1NXUXBLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2JXOWtMbVJsWm1sdVpVUmxjQ2hwTENCa1pXWnBibVZrVzJSbGNFbGtYU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2JXOWtMbU5vWldOcktDazdJQzh2SUhCaGMzTWdabUZzYzJVL1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOUlHVnNjMlVnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHSnlaV0ZyUTNsamJHVW9aR1Z3TENCMGNtRmpaV1FzSUhCeWIyTmxjM05sWkNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5S1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCd2NtOWpaWE56WldSYmFXUmRJRDBnZEhKMVpUdGNiaUFnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJR1oxYm1OMGFXOXVJR05vWldOclRHOWhaR1ZrS0NrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnYkdWMElHVnljaXdnZFhOcGJtZFFZWFJvUm1Gc2JHSmhZMnNzWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZDJGcGRFbHVkR1Z5ZG1Gc0lEMGdZMjl1Wm1sbkxuZGhhWFJUWldOdmJtUnpJQ29nTVRBd01DeGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJKZENCcGN5QndiM056YVdKc1pTQjBieUJrYVhOaFlteGxJSFJvWlNCM1lXbDBJR2x1ZEdWeWRtRnNJR0o1SUhWemFXNW5JSGRoYVhSVFpXTnZibVJ6SUc5bUlEQXVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdaWGh3YVhKbFpDQTlJSGRoYVhSSmJuUmxjblpoYkNBbUppQW9ZMjl1ZEdWNGRDNXpkR0Z5ZEZScGJXVWdLeUIzWVdsMFNXNTBaWEoyWVd3cElEd2dibVYzSUVSaGRHVW9LUzVuWlhSVWFXMWxLQ2tzWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYm05TWIyRmtjeUE5SUZ0ZExGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lISmxjVU5oYkd4eklEMGdXMTBzWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYzNScGJHeE1iMkZrYVc1bklEMGdabUZzYzJVc1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2JtVmxaRU41WTJ4bFEyaGxZMnNnUFNCMGNuVmxPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQXZMeUJFYnlCdWIzUWdZbTkwYUdWeUlHbG1JSFJvYVhNZ1kyRnNiQ0IzWVhNZ1lTQnlaWE4xYkhRZ2IyWWdZU0JqZVdOc1pTQmljbVZoYXk1Y2JpQWdJQ0FnSUNBZ0lDQWdJR2xtSUNocGJrTm9aV05yVEc5aFpHVmtLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnY21WMGRYSnVPMXh1SUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJQ0FnSUNCcGJrTm9aV05yVEc5aFpHVmtJRDBnZEhKMVpUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0x5OGdSbWxuZFhKbElHOTFkQ0IwYUdVZ2MzUmhkR1VnYjJZZ1lXeHNJSFJvWlNCdGIyUjFiR1Z6TGx4dUlDQWdJQ0FnSUNBZ0lDQWdaV0ZqYUZCeWIzQW9aVzVoWW14bFpGSmxaMmx6ZEhKNUxDQm1kVzVqZEdsdmJpQW9iVzlrS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2JHVjBJRzFoY0NBOUlHMXZaQzV0WVhBc1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJRzF2WkVsa0lEMGdiV0Z3TG1sa08xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnVTJ0cGNDQjBhR2x1WjNNZ2RHaGhkQ0JoY21VZ2JtOTBJR1Z1WVdKc1pXUWdiM0lnYVc0Z1pYSnliM0lnYzNSaGRHVXVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lnS0NGdGIyUXVaVzVoWW14bFpDa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCeVpYUjFjbTQ3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWWdLQ0Z0WVhBdWFYTkVaV1pwYm1VcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnY21WeFEyRnNiSE11Y0hWemFDaHRiMlFwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2doYlc5a0xtVnljbTl5S1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUVsbUlIUm9aU0J0YjJSMWJHVWdjMmh2ZFd4a0lHSmxJR1Y0WldOMWRHVmtMQ0JoYm1RZ2FYUWdhR0Z6SUc1dmRGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCaVpXVnVJR2x1YVhSbFpDQmhibVFnZEdsdFpTQnBjeUIxY0N3Z2NtVnRaVzFpWlhJZ2FYUXVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUlDZ2hiVzlrTG1sdWFYUmxaQ0FtSmlCbGVIQnBjbVZrS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9hR0Z6VUdGMGFFWmhiR3hpWVdOcktHMXZaRWxrS1NrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFZ6YVc1blVHRjBhRVpoYkd4aVlXTnJJRDBnZEhKMVpUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J6ZEdsc2JFeHZZV1JwYm1jZ1BTQjBjblZsTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlNCbGJITmxJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCdWIweHZZV1J6TG5CMWMyZ29iVzlrU1dRcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSEpsYlc5MlpWTmpjbWx3ZENodGIyUkpaQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMGdaV3h6WlNCcFppQW9JVzF2WkM1cGJtbDBaV1FnSmlZZ2JXOWtMbVpsZEdOb1pXUWdKaVlnYldGd0xtbHpSR1ZtYVc1bEtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnpkR2xzYkV4dllXUnBibWNnUFNCMGNuVmxPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lnS0NGdFlYQXVjSEpsWm1sNEtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1RtOGdjbVZoYzI5dUlIUnZJR3RsWlhBZ2JHOXZhMmx1WnlCbWIzSWdkVzVtYVc1cGMyaGxaRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklHeHZZV1JwYm1jdUlFbG1JSFJvWlNCdmJteDVJSE4wYVd4c1RHOWhaR2x1WnlCcGN5QmhYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z2NHeDFaMmx1SUhKbGMyOTFjbU5sSUhSb2IzVm5hQ3dnYTJWbGNDQm5iMmx1Wnl4Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJpWldOaGRYTmxJR2wwSUcxaGVTQmlaU0IwYUdGMElHRWdjR3gxWjJsdUlISmxjMjkxY21ObFhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnYVhNZ2QyRnBkR2x1WnlCdmJpQmhJRzV2Ymkxd2JIVm5hVzRnWTNsamJHVXVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnY21WMGRYSnVJQ2h1WldWa1EzbGpiR1ZEYUdWamF5QTlJR1poYkhObEtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJSDBwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb1pYaHdhWEpsWkNBbUppQnViMHh2WVdSekxteGxibWQwYUNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJRWxtSUhkaGFYUWdkR2x0WlNCbGVIQnBjbVZrTENCMGFISnZkeUJsY25KdmNpQnZaaUIxYm14dllXUmxaQ0J0YjJSMWJHVnpMbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1Z5Y2lBOUlHMWhhMlZGY25KdmNpZ25kR2x0Wlc5MWRDY3NJQ2RNYjJGa0lIUnBiV1Z2ZFhRZ1ptOXlJRzF2WkhWc1pYTTZJQ2NnS3lCdWIweHZZV1J6TENCdWRXeHNMQ0J1YjB4dllXUnpLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JsY25JdVkyOXVkR1Y0ZEU1aGJXVWdQU0JqYjI1MFpYaDBMbU52Ym5SbGVIUk9ZVzFsTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhKbGRIVnliaUJ2YmtWeWNtOXlLR1Z5Y2lrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQzh2SUU1dmRDQmxlSEJwY21Wa0xDQmphR1ZqYXlCbWIzSWdZU0JqZVdOc1pTNWNiaUFnSUNBZ0lDQWdJQ0FnSUdsbUlDaHVaV1ZrUTNsamJHVkRhR1ZqYXlrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHVmhZMmdvY21WeFEyRnNiSE1zSUdaMWJtTjBhVzl1SUNodGIyUXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1luSmxZV3REZVdOc1pTaHRiMlFzSUh0OUxDQjdmU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQzh2SUVsbUlITjBhV3hzSUhkaGFYUnBibWNnYjI0Z2JHOWhaSE1zSUdGdVpDQjBhR1VnZDJGcGRHbHVaeUJzYjJGa0lHbHpJSE52YldWMGFHbHVaMXh1SUNBZ0lDQWdJQ0FnSUNBZ0x5OGdiM1JvWlhJZ2RHaGhiaUJoSUhCc2RXZHBiaUJ5WlhOdmRYSmpaU3dnYjNJZ2RHaGxjbVVnWVhKbElITjBhV3hzSUc5MWRITjBZVzVrYVc1blhHNGdJQ0FnSUNBZ0lDQWdJQ0F2THlCelkzSnBjSFJ6TENCMGFHVnVJR3AxYzNRZ2RISjVJR0poWTJzZ2JHRjBaWEl1WEc0Z0lDQWdJQ0FnSUNBZ0lDQnBaaUFvS0NGbGVIQnBjbVZrSUh4OElIVnphVzVuVUdGMGFFWmhiR3hpWVdOcktTQW1KaUJ6ZEdsc2JFeHZZV1JwYm1jcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJUYjIxbGRHaHBibWNnYVhNZ2MzUnBiR3dnZDJGcGRHbHVaeUIwYnlCc2IyRmtMaUJYWVdsMElHWnZjaUJwZEN3Z1luVjBJRzl1YkhsY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QnBaaUJoSUhScGJXVnZkWFFnYVhNZ2JtOTBJR0ZzY21WaFpIa2dhVzRnWldabVpXTjBMbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtSUNnb2FYTkNjbTkzYzJWeUlIeDhJR2x6VjJWaVYyOXlhMlZ5S1NBbUppQWhZMmhsWTJ0TWIyRmtaV1JVYVcxbGIzVjBTV1FwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdZMmhsWTJ0TWIyRmtaV1JVYVcxbGIzVjBTV1FnUFNCelpYUlVhVzFsYjNWMEtHWjFibU4wYVc5dUlDZ3BJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR05vWldOclRHOWhaR1ZrVkdsdFpXOTFkRWxrSUQwZ01EdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHTm9aV05yVEc5aFpHVmtLQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMHNJRFV3S1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNGdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJR2x1UTJobFkydE1iMkZrWldRZ1BTQm1ZV3h6WlR0Y2JpQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJRTF2WkhWc1pTQTlJR1oxYm1OMGFXOXVJQ2h0WVhBcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUhSb2FYTXVaWFpsYm5SeklEMGdaMlYwVDNkdUtIVnVaR1ZtUlhabGJuUnpMQ0J0WVhBdWFXUXBJSHg4SUh0OU8xeHVJQ0FnSUNBZ0lDQWdJQ0FnZEdocGN5NXRZWEFnUFNCdFlYQTdYRzRnSUNBZ0lDQWdJQ0FnSUNCMGFHbHpMbk5vYVcwZ1BTQm5aWFJQZDI0b1kyOXVabWxuTG5Ob2FXMHNJRzFoY0M1cFpDazdYRzRnSUNBZ0lDQWdJQ0FnSUNCMGFHbHpMbVJsY0VWNGNHOXlkSE1nUFNCYlhUdGNiaUFnSUNBZ0lDQWdJQ0FnSUhSb2FYTXVaR1Z3VFdGd2N5QTlJRnRkTzF4dUlDQWdJQ0FnSUNBZ0lDQWdkR2hwY3k1a1pYQk5ZWFJqYUdWa0lEMGdXMTA3WEc0Z0lDQWdJQ0FnSUNBZ0lDQjBhR2x6TG5Cc2RXZHBiazFoY0hNZ1BTQjdmVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lIUm9hWE11WkdWd1EyOTFiblFnUFNBd08xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBdktpQjBhR2x6TG1WNGNHOXlkSE1nZEdocGN5NW1ZV04wYjNKNVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnZEdocGN5NWtaWEJOWVhCeklEMGdXMTBzWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdkR2hwY3k1bGJtRmliR1ZrTENCMGFHbHpMbVpsZEdOb1pXUmNiaUFnSUNBZ0lDQWdJQ0FnSUNBcUwxeHVJQ0FnSUNBZ0lDQjlPMXh1WEc0Z0lDQWdJQ0FnSUUxdlpIVnNaUzV3Y205MGIzUjVjR1VnUFNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0JwYm1sME9pQm1kVzVqZEdsdmJpQW9aR1Z3VFdGd2N5d2dabUZqZEc5eWVTd2daWEp5WW1GamF5d2diM0IwYVc5dWN5a2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJRzl3ZEdsdmJuTWdQU0J2Y0hScGIyNXpJSHg4SUh0OU8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnUkc4Z2JtOTBJR1J2SUcxdmNtVWdhVzVwZEhNZ2FXWWdZV3h5WldGa2VTQmtiMjVsTGlCRFlXNGdhR0Z3Y0dWdUlHbG1JSFJvWlhKbFhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdZWEpsSUcxMWJIUnBjR3hsSUdSbFptbHVaU0JqWVd4c2N5Qm1iM0lnZEdobElITmhiV1VnYlc5a2RXeGxMaUJVYUdGMElHbHpJRzV2ZEZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklHRWdibTl5YldGc0xDQmpiMjF0YjI0Z1kyRnpaU3dnWW5WMElHbDBJR2x6SUdGc2MyOGdibTkwSUhWdVpYaHdaV04wWldRdVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWWdLSFJvYVhNdWFXNXBkR1ZrS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSEpsZEhWeWJqdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IwYUdsekxtWmhZM1J2Y25rZ1BTQm1ZV04wYjNKNU8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lnS0dWeWNtSmhZMnNwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnVW1WbmFYTjBaWElnWm05eUlHVnljbTl5Y3lCdmJpQjBhR2x6SUcxdlpIVnNaUzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RHaHBjeTV2YmlnblpYSnliM0luTENCbGNuSmlZV05yS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOUlHVnNjMlVnYVdZZ0tIUm9hWE11WlhabGJuUnpMbVZ5Y205eUtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklFbG1JRzV2SUdWeWNtSmhZMnNnWVd4eVpXRmtlU3dnWW5WMElIUm9aWEpsSUdGeVpTQmxjbkp2Y2lCc2FYTjBaVzVsY25OY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnYjI0Z2RHaHBjeUJ0YjJSMWJHVXNJSE5sZENCMWNDQmhiaUJsY25KaVlXTnJJSFJ2SUhCaGMzTWdkRzhnZEdobElHUmxjSE11WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHVnljbUpoWTJzZ1BTQmlhVzVrS0hSb2FYTXNJR1oxYm1OMGFXOXVJQ2hsY25JcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIUm9hWE11WlcxcGRDZ25aWEp5YjNJbkxDQmxjbklwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QkVieUJoSUdOdmNIa2diMllnZEdobElHUmxjR1Z1WkdWdVkza2dZWEp5WVhrc0lITnZJSFJvWVhSY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QnpiM1Z5WTJVZ2FXNXdkWFJ6SUdGeVpTQnViM1FnYlc5a2FXWnBaV1F1SUVadmNpQmxlR0Z0Y0d4bFhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdYQ0p6YUdsdFhDSWdaR1Z3Y3lCaGNtVWdjR0Z6YzJWa0lHbHVJR2hsY21VZ1pHbHlaV04wYkhrc0lHRnVaRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUdSdmFXNW5JR0VnWkdseVpXTjBJRzF2WkdsbWFXTmhkR2x2YmlCdlppQjBhR1VnWkdWd1RXRndjeUJoY25KaGVWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJSGR2ZFd4a0lHRm1abVZqZENCMGFHRjBJR052Ym1acFp5NWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjBhR2x6TG1SbGNFMWhjSE1nUFNCa1pYQk5ZWEJ6SUNZbUlHUmxjRTFoY0hNdWMyeHBZMlVvTUNrN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjBhR2x6TG1WeWNtSmhZMnNnUFNCbGNuSmlZV05yTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdTVzVrYVdOaGRHVWdkR2hwY3lCdGIyUjFiR1VnYUdGeklHSmxJR2x1YVhScFlXeHBlbVZrWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZEdocGN5NXBibWwwWldRZ1BTQjBjblZsTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RHaHBjeTVwWjI1dmNtVWdQU0J2Y0hScGIyNXpMbWxuYm05eVpUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUVOdmRXeGtJR2hoZG1VZ2IzQjBhVzl1SUhSdklHbHVhWFFnZEdocGN5QnRiMlIxYkdVZ2FXNGdaVzVoWW14bFpDQnRiMlJsTEZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklHOXlJR052ZFd4a0lHaGhkbVVnWW1WbGJpQndjbVYyYVc5MWMyeDVJRzFoY210bFpDQmhjeUJsYm1GaWJHVmtMaUJJYjNkbGRtVnlMRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUhSb1pTQmtaWEJsYm1SbGJtTnBaWE1nWVhKbElHNXZkQ0JyYm05M2JpQjFiblJwYkNCcGJtbDBJR2x6SUdOaGJHeGxaQzRnVTI5Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QnBaaUJsYm1GaWJHVmtJSEJ5WlhacGIzVnpiSGtzSUc1dmR5QjBjbWxuWjJWeUlHUmxjR1Z1WkdWdVkybGxjeUJoY3lCbGJtRmliR1ZrTGx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUlDaHZjSFJwYjI1ekxtVnVZV0pzWldRZ2ZId2dkR2hwY3k1bGJtRmliR1ZrS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUVWdVlXSnNaU0IwYUdseklHMXZaSFZzWlNCaGJtUWdaR1Z3Wlc1a1pXNWphV1Z6TGx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJYYVd4c0lHTmhiR3dnZEdocGN5NWphR1ZqYXlncFhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFJvYVhNdVpXNWhZbXhsS0NrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZTQmxiSE5sSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkR2hwY3k1amFHVmpheWdwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJSDBzWEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJR1JsWm1sdVpVUmxjRG9nWm5WdVkzUnBiMjRnS0drc0lHUmxjRVY0Y0c5eWRITXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCQ1pXTmhkWE5sSUc5bUlHTjVZMnhsY3l3Z1pHVm1hVzVsWkNCallXeHNZbUZqYXlCbWIzSWdZU0JuYVhabGJseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJR1Y0Y0c5eWRDQmpZVzRnWW1VZ1kyRnNiR1ZrSUcxdmNtVWdkR2hoYmlCdmJtTmxMbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtSUNnaGRHaHBjeTVrWlhCTllYUmphR1ZrVzJsZEtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhSb2FYTXVaR1Z3VFdGMFkyaGxaRnRwWFNBOUlIUnlkV1U3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIUm9hWE11WkdWd1EyOTFiblFnTFQwZ01UdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZEdocGN5NWtaWEJGZUhCdmNuUnpXMmxkSUQwZ1pHVndSWGh3YjNKMGN6dGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNCOUxGeHVYRzRnSUNBZ0lDQWdJQ0FnSUNCbVpYUmphRG9nWm5WdVkzUnBiMjRnS0NrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2gwYUdsekxtWmxkR05vWldRcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnY21WMGRYSnVPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IwYUdsekxtWmxkR05vWldRZ1BTQjBjblZsTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1kyOXVkR1Y0ZEM1emRHRnlkRlJwYldVZ1BTQW9ibVYzSUVSaGRHVW9LU2t1WjJWMFZHbHRaU2dwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2JHVjBJRzFoY0NBOUlIUm9hWE11YldGd08xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnU1dZZ2RHaGxJRzFoYm1GblpYSWdhWE1nWm05eUlHRWdjR3gxWjJsdUlHMWhibUZuWldRZ2NtVnpiM1Z5WTJVc1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdZWE5ySUhSb1pTQndiSFZuYVc0Z2RHOGdiRzloWkNCcGRDQnViM2N1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYVdZZ0tIUm9hWE11YzJocGJTa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCamIyNTBaWGgwTG0xaGEyVlNaWEYxYVhKbEtIUm9hWE11YldGd0xDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmxibUZpYkdWQ2RXbHNaRU5oYkd4aVlXTnJPaUIwY25WbFhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDBwS0hSb2FYTXVjMmhwYlM1a1pYQnpJSHg4SUZ0ZExDQmlhVzVrS0hSb2FYTXNJR1oxYm1OMGFXOXVJQ2dwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhKbGRIVnliaUJ0WVhBdWNISmxabWw0SUQ4Z2RHaHBjeTVqWVd4c1VHeDFaMmx1S0NrZ09pQjBhR2x6TG14dllXUW9LVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZTa3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDBnWld4elpTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklGSmxaM1ZzWVhJZ1pHVndaVzVrWlc1amVTNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnY21WMGRYSnVJRzFoY0M1d2NtVm1hWGdnUHlCMGFHbHpMbU5oYkd4UWJIVm5hVzRvS1NBNklIUm9hWE11Ykc5aFpDZ3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lIMHNYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lHeHZZV1E2SUdaMWJtTjBhVzl1SUNncElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnNaWFFnZFhKc0lEMGdkR2hwY3k1dFlYQXVkWEpzTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdVbVZuZFd4aGNpQmtaWEJsYm1SbGJtTjVMbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtSUNnaGRYSnNSbVYwWTJobFpGdDFjbXhkS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFZ5YkVabGRHTm9aV1JiZFhKc1hTQTlJSFJ5ZFdVN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR052Ym5SbGVIUXViRzloWkNoMGFHbHpMbTFoY0M1cFpDd2dkWEpzS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNGdJQ0FnSUNBZ0lDQWdJQ0I5TEZ4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0F2S2lwY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FxSUVOb1pXTnJjeUJwWmlCMGFHVWdiVzlrZFd4bElHbHpJSEpsWVdSNUlIUnZJR1JsWm1sdVpTQnBkSE5sYkdZc0lHRnVaQ0JwWmlCemJ5eGNiaUFnSUNBZ0lDQWdJQ0FnSUNBcUlHUmxabWx1WlNCcGRDNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBcUwxeHVJQ0FnSUNBZ0lDQWdJQ0FnWTJobFkyczZJR1oxYm1OMGFXOXVJQ2dwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9JWFJvYVhNdVpXNWhZbXhsWkNCOGZDQjBhR2x6TG1WdVlXSnNhVzVuS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSEpsZEhWeWJqdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JzWlhRZ1pYSnlMQ0JqYW5OTmIyUjFiR1VzWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbGtJRDBnZEdocGN5NXRZWEF1YVdRc1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1JsY0VWNGNHOXlkSE1nUFNCMGFHbHpMbVJsY0VWNGNHOXlkSE1zWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHVjRjRzl5ZEhNZ1BTQjBhR2x6TG1WNGNHOXlkSE1zWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHWmhZM1J2Y25rZ1BTQjBhR2x6TG1aaFkzUnZjbms3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9JWFJvYVhNdWFXNXBkR1ZrS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUU5dWJIa2dabVYwWTJnZ2FXWWdibTkwSUdGc2NtVmhaSGtnYVc0Z2RHaGxJR1JsWmxGMVpYVmxMbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9JV2hoYzFCeWIzQW9ZMjl1ZEdWNGRDNWtaV1pSZFdWMVpVMWhjQ3dnYVdRcEtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjBhR2x6TG1abGRHTm9LQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlJR1ZzYzJVZ2FXWWdLSFJvYVhNdVpYSnliM0lwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkR2hwY3k1bGJXbDBLQ2RsY25KdmNpY3NJSFJvYVhNdVpYSnliM0lwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgwZ1pXeHpaU0JwWmlBb0lYUm9hWE11WkdWbWFXNXBibWNwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnVkdobElHWmhZM1J2Y25rZ1kyOTFiR1FnZEhKcFoyZGxjaUJoYm05MGFHVnlJSEpsY1hWcGNtVWdZMkZzYkZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUIwYUdGMElIZHZkV3hrSUhKbGMzVnNkQ0JwYmlCamFHVmphMmx1WnlCMGFHbHpJRzF2WkhWc1pTQjBiMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QmtaV1pwYm1VZ2FYUnpaV3htSUdGbllXbHVMaUJKWmlCaGJISmxZV1I1SUdsdUlIUm9aU0J3Y205alpYTnpYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklHOW1JR1J2YVc1bklIUm9ZWFFzSUhOcmFYQWdkR2hwY3lCM2IzSnJMbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCMGFHbHpMbVJsWm1sdWFXNW5JRDBnZEhKMVpUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9kR2hwY3k1a1pYQkRiM1Z1ZENBOElERWdKaVlnSVhSb2FYTXVaR1ZtYVc1bFpDa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lnS0dselJuVnVZM1JwYjI0b1ptRmpkRzl5ZVNrcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IwY25rZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCbGVIQnZjblJ6SUQwZ1kyOXVkR1Y0ZEM1bGVHVmpRMklvYVdRc0lHWmhZM1J2Y25rc0lHUmxjRVY0Y0c5eWRITXNJR1Y0Y0c5eWRITXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgwZ1kyRjBZMmdnS0dVcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1pYSnlJRDBnWlR0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QkdZWFp2Y2lCeVpYUjFjbTRnZG1Gc2RXVWdiM1psY2lCbGVIQnZjblJ6TGlCSlppQnViMlJsTDJOcWN5QnBiaUJ3YkdGNUxGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUhSb1pXNGdkMmxzYkNCdWIzUWdhR0YyWlNCaElISmxkSFZ5YmlCMllXeDFaU0JoYm5sM1lYa3VJRVpoZG05eVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnYlc5a2RXeGxMbVY0Y0c5eWRITWdZWE56YVdkdWJXVnVkQ0J2ZG1WeUlHVjRjRzl5ZEhNZ2IySnFaV04wTGx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2gwYUdsekxtMWhjQzVwYzBSbFptbHVaU0FtSmlCbGVIQnZjblJ6SUQwOVBTQjFibVJsWm1sdVpXUXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdZMnB6VFc5a2RXeGxJRDBnZEdocGN5NXRiMlIxYkdVN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUlDaGphbk5OYjJSMWJHVXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdWNGNHOXlkSE1nUFNCamFuTk5iMlIxYkdVdVpYaHdiM0owY3p0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlNCbGJITmxJR2xtSUNoMGFHbHpMblZ6YVc1blJYaHdiM0owY3lrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnWlhod2IzSjBjeUJoYkhKbFlXUjVJSE5sZENCMGFHVWdaR1ZtYVc1bFpDQjJZV3gxWlM1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHVjRjRzl5ZEhNZ1BTQjBhR2x6TG1WNGNHOXlkSE03WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb1pYSnlLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUVsbUlIUm9aWEpsSUdseklHRnVJR1Z5Y205eUlHeHBjM1JsYm1WeUxDQm1ZWFp2Y2lCd1lYTnphVzVuWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUhSdklIUm9ZWFFnYVc1emRHVmhaQ0J2WmlCMGFISnZkMmx1WnlCaGJpQmxjbkp2Y2k0Z1NHOTNaWFpsY2l4Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z2IyNXNlU0JrYnlCcGRDQm1iM0lnWkdWbWFXNWxLQ2tuWkNBZ2JXOWtkV3hsY3k0Z2NtVnhkV2x5WlZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCbGNuSmlZV05yY3lCemFHOTFiR1FnYm05MElHSmxJR05oYkd4bFpDQm1iM0lnWm1GcGJIVnlaWE1nYVc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z2RHaGxhWElnWTJGc2JHSmhZMnR6SUNnak5qazVLUzRnU0c5M1pYWmxjaUJwWmlCaElHZHNiMkpoYkZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCdmJrVnljbTl5SUdseklITmxkQ3dnZFhObElIUm9ZWFF1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtSUNnb2RHaHBjeTVsZG1WdWRITXVaWEp5YjNJZ0ppWWdkR2hwY3k1dFlYQXVhWE5FWldacGJtVXBJSHg4WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCeVpYRXViMjVGY25KdmNpQWhQVDBnWkdWbVlYVnNkRTl1UlhKeWIzSXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdWeWNpNXlaWEYxYVhKbFRXRndJRDBnZEdocGN5NXRZWEE3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCbGNuSXVjbVZ4ZFdseVpVMXZaSFZzWlhNZ1BTQjBhR2x6TG0xaGNDNXBjMFJsWm1sdVpTQS9JRnQwYUdsekxtMWhjQzVwWkYwZ09pQnVkV3hzTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1pYSnlMbkpsY1hWcGNtVlVlWEJsSUQwZ2RHaHBjeTV0WVhBdWFYTkVaV1pwYm1VZ1B5QW5aR1ZtYVc1bEp5QTZJQ2R5WlhGMWFYSmxKenRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhKbGRIVnliaUJ2YmtWeWNtOXlLQ2gwYUdsekxtVnljbTl5SUQwZ1pYSnlLU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDBnWld4elpTQnBaaUFvZEhsd1pXOW1JR052Ym5OdmJHVWdJVDA5SUNkMWJtUmxabWx1WldRbklDWW1YRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JqYjI1emIyeGxMbVZ5Y205eUtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCTWIyY2dkR2hsSUdWeWNtOXlJR1p2Y2lCa1pXSjFaMmRwYm1jdUlFbG1JSEJ5YjIxcGMyVnpJR052ZFd4a0lHSmxYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCMWMyVmtMQ0IwYUdseklIZHZkV3hrSUdKbElHUnBabVpsY21WdWRDd2dZblYwSUcxaGEybHVaeUJrYnk1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHTnZibk52YkdVdVpYSnliM0lvWlhKeUtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZTQmxiSE5sSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJRVJ2SUc1dmRDQjNZVzUwSUhSdklHTnZiWEJzWlhSbGJIa2diRzl6WlNCMGFHVWdaWEp5YjNJdUlGZG9hV3hsSUhSb2FYTmNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUhkcGJHd2diV1Z6Y3lCMWNDQndjbTlqWlhOemFXNW5JR0Z1WkNCc1pXRmtJSFJ2SUhOcGJXbHNZWElnY21WemRXeDBjMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1lYTWdZblZuSURFME5EQXNJR2wwSUdGMElHeGxZWE4wSUhOMWNtWmhZMlZ6SUhSb1pTQmxjbkp2Y2k1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lISmxjUzV2YmtWeWNtOXlLR1Z5Y2lrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlJR1ZzYzJVZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUVwMWMzUWdZU0JzYVhSbGNtRnNJSFpoYkhWbFhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdaWGh3YjNKMGN5QTlJR1poWTNSdmNuazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFJvYVhNdVpYaHdiM0owY3lBOUlHVjRjRzl5ZEhNN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2gwYUdsekxtMWhjQzVwYzBSbFptbHVaU0FtSmlBaGRHaHBjeTVwWjI1dmNtVXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCa1pXWnBibVZrVzJsa1hTQTlJR1Y0Y0c5eWRITTdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9jbVZ4TG05dVVtVnpiM1Z5WTJWTWIyRmtLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR3hsZENCeVpYTk1iMkZrVFdGd2N5QTlJRnRkTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JsWVdOb0tIUm9hWE11WkdWd1RXRndjeXdnWm5WdVkzUnBiMjRnS0dSbGNFMWhjQ2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2NtVnpURzloWkUxaGNITXVjSFZ6YUNoa1pYQk5ZWEF1Ym05eWJXRnNhWHBsWkUxaGNDQjhmQ0JrWlhCTllYQXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjbVZ4TG05dVVtVnpiM1Z5WTJWTWIyRmtLR052Ym5SbGVIUXNJSFJvYVhNdWJXRndMQ0J5WlhOTWIyRmtUV0Z3Y3lrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCRGJHVmhiaUIxY0Z4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWTJ4bFlXNVNaV2RwYzNSeWVTaHBaQ2s3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhSb2FYTXVaR1ZtYVc1bFpDQTlJSFJ5ZFdVN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJHYVc1cGMyaGxaQ0IwYUdVZ1pHVm1hVzVsSUhOMFlXZGxMaUJCYkd4dmR5QmpZV3hzYVc1bklHTm9aV05ySUdGbllXbHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklIUnZJR0ZzYkc5M0lHUmxabWx1WlNCdWIzUnBabWxqWVhScGIyNXpJR0psYkc5M0lHbHVJSFJvWlNCallYTmxJRzltSUdGY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnWTNsamJHVXVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhSb2FYTXVaR1ZtYVc1cGJtY2dQU0JtWVd4elpUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9kR2hwY3k1a1pXWnBibVZrSUNZbUlDRjBhR2x6TG1SbFptbHVaVVZ0YVhSMFpXUXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFJvYVhNdVpHVm1hVzVsUlcxcGRIUmxaQ0E5SUhSeWRXVTdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjBhR2x6TG1WdGFYUW9KMlJsWm1sdVpXUW5MQ0IwYUdsekxtVjRjRzl5ZEhNcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RHaHBjeTVrWldacGJtVkZiV2wwUTI5dGNHeGxkR1VnUFNCMGNuVmxPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNCOUxGeHVYRzRnSUNBZ0lDQWdJQ0FnSUNCallXeHNVR3gxWjJsdU9pQm1kVzVqZEdsdmJpQW9LU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYkdWMElHMWhjQ0E5SUhSb2FYTXViV0Z3TzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUd4bGRDQnBaQ0E5SUcxaGNDNXBaRHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCTllYQWdZV3h5WldGa2VTQnViM0p0WVd4cGVtVmtJSFJvWlNCd2NtVm1hWGd1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYkdWMElIQnNkV2RwYmsxaGNDQTlJRzFoYTJWTmIyUjFiR1ZOWVhBb2JXRndMbkJ5WldacGVDazdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCTllYSnJJSFJvYVhNZ1lYTWdZU0JrWlhCbGJtUmxibU41SUdadmNpQjBhR2x6SUhCc2RXZHBiaXdnYzI4Z2FYUmNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJqWVc0Z1ltVWdkSEpoWTJWa0lHWnZjaUJqZVdOc1pYTXVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkR2hwY3k1a1pYQk5ZWEJ6TG5CMWMyZ29jR3gxWjJsdVRXRndLVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUc5dUtIQnNkV2RwYmsxaGNDd2dKMlJsWm1sdVpXUW5MQ0JpYVc1a0tIUm9hWE1zSUdaMWJtTjBhVzl1SUNod2JIVm5hVzRwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdiR1YwSUd4dllXUXNJRzV2Y20xaGJHbDZaV1JOWVhBc0lHNXZjbTFoYkdsNlpXUk5iMlFzWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JpZFc1a2JHVkpaQ0E5SUdkbGRFOTNiaWhpZFc1a2JHVnpUV0Z3TENCMGFHbHpMbTFoY0M1cFpDa3NYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnVZVzFsSUQwZ2RHaHBjeTV0WVhBdWJtRnRaU3hjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSEJoY21WdWRFNWhiV1VnUFNCMGFHbHpMbTFoY0M1d1lYSmxiblJOWVhBZ1B5QjBhR2x6TG0xaGNDNXdZWEpsYm5STllYQXVibUZ0WlNBNklHNTFiR3dzWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JzYjJOaGJGSmxjWFZwY21VZ1BTQmpiMjUwWlhoMExtMWhhMlZTWlhGMWFYSmxLRzFoY0M1d1lYSmxiblJOWVhBc0lIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JsYm1GaWJHVkNkV2xzWkVOaGJHeGlZV05yT2lCMGNuVmxYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlLVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJKWmlCamRYSnlaVzUwSUcxaGNDQnBjeUJ1YjNRZ2JtOXliV0ZzYVhwbFpDd2dkMkZwZENCbWIzSWdkR2hoZEZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJ1YjNKdFlXeHBlbVZrSUc1aGJXVWdkRzhnYkc5aFpDQnBibk4wWldGa0lHOW1JR052Ym5ScGJuVnBibWN1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2gwYUdsekxtMWhjQzUxYm01dmNtMWhiR2w2WldRcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJRTV2Y20xaGJHbDZaU0IwYUdVZ1NVUWdhV1lnZEdobElIQnNkV2RwYmlCaGJHeHZkM01nYVhRdVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9jR3gxWjJsdUxtNXZjbTFoYkdsNlpTa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUc1aGJXVWdQU0J3YkhWbmFXNHVibTl5YldGc2FYcGxLRzVoYldVc0lHWjFibU4wYVc5dUlDaHVZVzFsS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhKbGRIVnliaUJ1YjNKdFlXeHBlbVVvYm1GdFpTd2djR0Z5Wlc1MFRtRnRaU3dnZEhKMVpTazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlNrZ2ZId2dKeWM3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklIQnlaV1pwZUNCaGJtUWdibUZ0WlNCemFHOTFiR1FnWVd4eVpXRmtlU0JpWlNCdWIzSnRZV3hwZW1Wa0xDQnVieUJ1WldWa1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5Qm1iM0lnWVhCd2JIbHBibWNnYldGd0lHTnZibVpwWnlCaFoyRnBiaUJsYVhSb1pYSXVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnViM0p0WVd4cGVtVmtUV0Z3SUQwZ2JXRnJaVTF2WkhWc1pVMWhjQ2h0WVhBdWNISmxabWw0SUNzZ0p5RW5JQ3NnYm1GdFpTeGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IwYUdsekxtMWhjQzV3WVhKbGJuUk5ZWEFwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYjI0b2JtOXliV0ZzYVhwbFpFMWhjQ3hjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBblpHVm1hVzVsWkNjc0lHSnBibVFvZEdocGN5d2dablZ1WTNScGIyNGdLSFpoYkhWbEtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIUm9hWE11YldGd0xtNXZjbTFoYkdsNlpXUk5ZWEFnUFNCdWIzSnRZV3hwZW1Wa1RXRndPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjBhR2x6TG1sdWFYUW9XMTBzSUdaMWJtTjBhVzl1SUNncElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSEpsZEhWeWJpQjJZV3gxWlR0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlN3Z2JuVnNiQ3dnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1pXNWhZbXhsWkRvZ2RISjFaU3hjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbmJtOXlaVG9nZEhKMVpWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOUtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5S1NrN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHNXZjbTFoYkdsNlpXUk5iMlFnUFNCblpYUlBkMjRvY21WbmFYTjBjbmtzSUc1dmNtMWhiR2w2WldSTllYQXVhV1FwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYVdZZ0tHNXZjbTFoYkdsNlpXUk5iMlFwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJOWVhKcklIUm9hWE1nWVhNZ1lTQmtaWEJsYm1SbGJtTjVJR1p2Y2lCMGFHbHpJSEJzZFdkcGJpd2djMjhnYVhSY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJqWVc0Z1ltVWdkSEpoWTJWa0lHWnZjaUJqZVdOc1pYTXVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZEdocGN5NWtaWEJOWVhCekxuQjFjMmdvYm05eWJXRnNhWHBsWkUxaGNDazdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9kR2hwY3k1bGRtVnVkSE11WlhKeWIzSXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdibTl5YldGc2FYcGxaRTF2WkM1dmJpZ25aWEp5YjNJbkxDQmlhVzVrS0hSb2FYTXNJR1oxYm1OMGFXOXVJQ2hsY25JcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFJvYVhNdVpXMXBkQ2duWlhKeWIzSW5MQ0JsY25JcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOUtTazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHNXZjbTFoYkdsNlpXUk5iMlF1Wlc1aFlteGxLQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhKbGRIVnlianRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklFbG1JR0VnY0dGMGFITWdZMjl1Wm1sbkxDQjBhR1Z1SUdwMWMzUWdiRzloWkNCMGFHRjBJR1pwYkdVZ2FXNXpkR1ZoWkNCMGIxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCeVpYTnZiSFpsSUhSb1pTQndiSFZuYVc0c0lHRnpJR2wwSUdseklHSjFhV3gwSUdsdWRHOGdkR2hoZENCd1lYUm9jeUJzWVhsbGNpNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYVdZZ0tHSjFibVJzWlVsa0tTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjBhR2x6TG0xaGNDNTFjbXdnUFNCamIyNTBaWGgwTG01aGJXVlViMVZ5YkNoaWRXNWtiR1ZKWkNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCMGFHbHpMbXh2WVdRb0tUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lISmxkSFZ5Ymp0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHeHZZV1FnUFNCaWFXNWtLSFJvYVhNc0lHWjFibU4wYVc5dUlDaDJZV3gxWlNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RHaHBjeTVwYm1sMEtGdGRMQ0JtZFc1amRHbHZiaUFvS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjbVYwZFhKdUlIWmhiSFZsTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlN3Z2JuVnNiQ3dnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHVnVZV0pzWldRNklIUnlkV1ZjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDBwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlLVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnNiMkZrTG1WeWNtOXlJRDBnWW1sdVpDaDBhR2x6TENCbWRXNWpkR2x2YmlBb1pYSnlLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IwYUdsekxtbHVhWFJsWkNBOUlIUnlkV1U3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IwYUdsekxtVnljbTl5SUQwZ1pYSnlPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdaWEp5TG5KbGNYVnBjbVZOYjJSMWJHVnpJRDBnVzJsa1hUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnVW1WdGIzWmxJSFJsYlhBZ2RXNXViM0p0WVd4cGVtVmtJRzF2WkhWc1pYTWdabTl5SUhSb2FYTWdiVzlrZFd4bExDQnphVzVqWlNCMGFHVjVJSGRwYkd3Z2JtVjJaWElnWW1VZ2NtVnpiMngyWldRZ2IzUm9aWEozYVhObElGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdibTkzTGx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWldGamFGQnliM0FvY21WbmFYTjBjbmtzSUdaMWJtTjBhVzl1SUNodGIyUXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9iVzlrTG0xaGNDNXBaQzVwYm1SbGVFOW1LR2xrSUNzZ0oxOTFibTV2Y20xaGJHbDZaV1FuS1NBOVBUMGdNQ2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JqYkdWaGJsSmxaMmx6ZEhKNUtHMXZaQzV0WVhBdWFXUXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgwcE8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnZia1Z5Y205eUtHVnljaWs3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMHBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJRUZzYkc5M0lIQnNkV2RwYm5NZ2RHOGdiRzloWkNCdmRHaGxjaUJqYjJSbElIZHBkR2h2ZFhRZ2FHRjJhVzVuSUhSdklHdHViM2NnZEdobElHTnZiblJsZUhRZ2IzSWdhRzkzSUhSdklDZGpiMjF3YkdWMFpTY2dkR2hsSUZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJzYjJGa0xseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JzYjJGa0xtWnliMjFVWlhoMElEMGdZbWx1WkNoMGFHbHpMQ0JtZFc1amRHbHZiaUFvZEdWNGRDd2dkR1Y0ZEVGc2RDa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeXBxYzJ4cGJuUWdaWFpwYkRvZ2RISjFaU0FxTDF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYkdWMElHMXZaSFZzWlU1aGJXVWdQU0J0WVhBdWJtRnRaU3hjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCdGIyUjFiR1ZOWVhBZ1BTQnRZV3RsVFc5a2RXeGxUV0Z3S0cxdlpIVnNaVTVoYldVcExGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2hoYzBsdWRHVnlZV04wYVhabElEMGdkWE5sU1c1MFpYSmhZM1JwZG1VN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJRUZ6SUc5bUlESXVNUzR3TENCemRYQndiM0owSUdwMWMzUWdjR0Z6YzJsdVp5QjBhR1VnZEdWNGRDd2dkRzhnY21WcGJtWnZjbU5sSUdaeWIyMVVaWGgwSUc5dWJIa2dZbVZwYm1jZ1kyRnNiR1ZrSUc5dVkyVWdjR1Z5SUZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z2NtVnpiM1Z5WTJVdUlGTjBhV3hzSUhOMWNIQnZjblFnYjJ4a0lITjBlV3hsSUc5bUlIQmhjM05wYm1jZ2JXOWtkV3hsVG1GdFpTQmlkWFFnWkdselkyRnlaQ0IwYUdGMElHMXZaSFZzWlU1aGJXVWdhVzRnWm1GMmIzSWdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJ2WmlCMGFHVWdhVzUwWlhKdVlXd2djbVZtTGx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYVdZZ0tIUmxlSFJCYkhRcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IwWlhoMElEMGdkR1Y0ZEVGc2REdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnVkhWeWJpQnZabVlnYVc1MFpYSmhZM1JwZG1VZ2MyTnlhWEIwSUcxaGRHTm9hVzVuSUdadmNpQkpSU0JtYjNJZ1lXNTVJR1JsWm1sdVpTQmpZV3hzY3lCcGJpQjBhR1VnZEdWNGRDd2dkR2hsYmlCMGRYSnVJR2wwSUZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1ltRmpheUJ2YmlCaGRDQjBhR1VnWlc1a0xseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWWdLR2hoYzBsdWRHVnlZV04wYVhabEtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZFhObFNXNTBaWEpoWTNScGRtVWdQU0JtWVd4elpUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnVUhKcGJXVWdkR2hsSUhONWMzUmxiU0JpZVNCamNtVmhkR2x1WnlCaElHMXZaSFZzWlNCcGJuTjBZVzVqWlNCbWIzSWdhWFF1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JuWlhSTmIyUjFiR1VvYlc5a2RXeGxUV0Z3S1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdWSEpoYm5ObVpYSWdZVzU1SUdOdmJtWnBaeUIwYnlCMGFHbHpJRzkwYUdWeUlHMXZaSFZzWlM1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUlDaG9ZWE5RY205d0tHTnZibVpwWnk1amIyNW1hV2NzSUdsa0tTa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdOdmJtWnBaeTVqYjI1bWFXZGJiVzlrZFd4bFRtRnRaVjBnUFNCamIyNW1hV2N1WTI5dVptbG5XMmxrWFR0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RISjVJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCeVpYRXVaWGhsWXloMFpYaDBLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDBnWTJGMFkyZ2dLR1VwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnlaWFIxY200Z2IyNUZjbkp2Y2lodFlXdGxSWEp5YjNJb0oyWnliMjEwWlhoMFpYWmhiQ2NzWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ2RtY205dFZHVjRkQ0JsZG1Gc0lHWnZjaUFuSUNzZ2FXUWdLMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQW5JR1poYVd4bFpEb2dKeUFySUdVc1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdVc1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUZ0cFpGMHBLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYVdZZ0tHaGhjMGx1ZEdWeVlXTjBhWFpsS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkWE5sU1c1MFpYSmhZM1JwZG1VZ1BTQjBjblZsTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5Qk5ZWEpySUhSb2FYTWdZWE1nWVNCa1pYQmxibVJsYm1ONUlHWnZjaUIwYUdVZ2NHeDFaMmx1SUhKbGMyOTFjbU5sTGx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZEdocGN5NWtaWEJOWVhCekxuQjFjMmdvYlc5a2RXeGxUV0Z3S1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdVM1Z3Y0c5eWRDQmhibTl1ZVcxdmRYTWdiVzlrZFd4bGN5NWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHTnZiblJsZUhRdVkyOXRjR3hsZEdWTWIyRmtLRzF2WkhWc1pVNWhiV1VwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QkNhVzVrSUhSb1pTQjJZV3gxWlNCdlppQjBhR0YwSUcxdlpIVnNaU0IwYnlCMGFHVWdkbUZzZFdVZ1ptOXlJSFJvYVhNZ2NtVnpiM1Z5WTJVZ1NVUXVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnNiMk5oYkZKbGNYVnBjbVVvVzIxdlpIVnNaVTVoYldWZExDQnNiMkZrS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmU2s3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnVlhObElIQmhjbVZ1ZEU1aGJXVWdhR1Z5WlNCemFXNWpaU0IwYUdVZ2NHeDFaMmx1SjNNZ2JtRnRaU0JwY3lCdWIzUWdjbVZzYVdGaWJHVXNJR052ZFd4a0lHSmxJSE52YldVZ2QyVnBjbVFnYzNSeWFXNW5JSGRwZEdnZ2JtOGdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklIQmhkR2dnZEdoaGRDQmhZM1IxWVd4c2VTQjNZVzUwY3lCMGJ5QnlaV1psY21WdVkyVWdkR2hsSUhCaGNtVnVkRTVoYldVbmN5QndZWFJvTGx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQndiSFZuYVc0dWJHOWhaQ2h0WVhBdWJtRnRaU3dnYkc5allXeFNaWEYxYVhKbExDQnNiMkZrTENCamIyNW1hV2NwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgwcEtUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR052Ym5SbGVIUXVaVzVoWW14bEtIQnNkV2RwYmsxaGNDd2dkR2hwY3lrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RHaHBjeTV3YkhWbmFXNU5ZWEJ6VzNCc2RXZHBiazFoY0M1cFpGMGdQU0J3YkhWbmFXNU5ZWEE3WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlMRnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQmxibUZpYkdVNklHWjFibU4wYVc5dUlDZ3BJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JsYm1GaWJHVmtVbVZuYVhOMGNubGJkR2hwY3k1dFlYQXVhV1JkSUQwZ2RHaHBjenRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IwYUdsekxtVnVZV0pzWldRZ1BTQjBjblZsTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdVMlYwSUdac1lXY2diV1Z1ZEdsdmJtbHVaeUIwYUdGMElIUm9aU0J0YjJSMWJHVWdhWE1nWlc1aFlteHBibWNzSUhOdklIUm9ZWFFnYVcxdFpXUnBZWFJsSUdOaGJHeHpJSFJ2SUhSb1pTQmtaV1pwYm1Wa0lHTmhiR3hpWVdOcmN5Qm1iM0lnWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1pHVndaVzVrWlc1amFXVnpJR1J2SUc1dmRDQjBjbWxuWjJWeUlHbHVZV1IyWlhKMFpXNTBJR3h2WVdRZ2QybDBhQ0IwYUdVZ1pHVndRMjkxYm5RZ2MzUnBiR3dnWW1WcGJtY2dlbVZ5Ynk1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCMGFHbHpMbVZ1WVdKc2FXNW5JRDBnZEhKMVpUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUVWdVlXSnNaU0JsWVdOb0lHUmxjR1Z1WkdWdVkza3VYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdaV0ZqYUNoMGFHbHpMbVJsY0UxaGNITXNJR0pwYm1Rb2RHaHBjeXdnWm5WdVkzUnBiMjRnS0dSbGNFMWhjQ3dnYVNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JzWlhRZ2FXUTdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUd4bGRDQnRiMlE3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHeGxkQ0JvWVc1a2JHVnlPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2gwZVhCbGIyWWdaR1Z3VFdGd0lEMDlQU0FuYzNSeWFXNW5KeWtnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1JHVndaVzVrWlc1amVTQnVaV1ZrY3lCMGJ5QmlaU0JqYjI1MlpYSjBaV1FnZEc4Z1lTQmtaWEJOWVhBZ1lXNWtJSGRwY21Wa0lIVndJSFJ2SUhSb2FYTWdiVzlrZFd4bExseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1pHVndUV0Z3SUQwZ2JXRnJaVTF2WkhWc1pVMWhjQ2hrWlhCTllYQXNYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnS0hSb2FYTXViV0Z3TG1selJHVm1hVzVsSUQ4Z2RHaHBjeTV0WVhBZ09pQjBhR2x6TG0xaGNDNXdZWEpsYm5STllYQXBMRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdaaGJITmxMRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNGMGFHbHpMbk5yYVhCTllYQXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkR2hwY3k1a1pYQk5ZWEJ6VzJsZElEMGdaR1Z3VFdGd08xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQm9ZVzVrYkdWeUlEMGdaMlYwVDNkdUtHaGhibVJzWlhKekxDQmtaWEJOWVhBdWFXUXBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb2FHRnVaR3hsY2lrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFJvYVhNdVpHVndSWGh3YjNKMGMxdHBYU0E5SUdoaGJtUnNaWElvZEdocGN5azdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnY21WMGRYSnVPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IwYUdsekxtUmxjRU52ZFc1MElDczlJREU3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUc5dUtHUmxjRTFoY0N3Z0oyUmxabWx1WldRbkxDQmlhVzVrS0hSb2FYTXNJR1oxYm1OMGFXOXVJQ2hrWlhCRmVIQnZjblJ6S1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lnS0hSb2FYTXVkVzVrWldabFpDa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnlaWFIxY200N1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhSb2FYTXVaR1ZtYVc1bFJHVndLR2tzSUdSbGNFVjRjRzl5ZEhNcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFJvYVhNdVkyaGxZMnNvS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgwcEtUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lnS0hSb2FYTXVaWEp5WW1GamF5a2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUc5dUtHUmxjRTFoY0N3Z0oyVnljbTl5Snl3Z1ltbHVaQ2gwYUdsekxDQjBhR2x6TG1WeWNtSmhZMnNwS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgwZ1pXeHpaU0JwWmlBb2RHaHBjeTVsZG1WdWRITXVaWEp5YjNJcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCT2J5QmthWEpsWTNRZ1pYSnlZbUZqYXlCdmJpQjBhR2x6SUcxdlpIVnNaU3dnWW5WMElITnZiV1YwYUdsdVp5QmxiSE5sSUdseklHeHBjM1JsYm1sdVp5Qm1iM0lnWlhKeWIzSnpMQ0J6YnlCaVpTQnpkWEpsSUZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJSFJ2SUhCeWIzQmhaMkYwWlNCMGFHVWdaWEp5YjNJZ1kyOXljbVZqZEd4NUxseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJRzl1S0dSbGNFMWhjQ3dnSjJWeWNtOXlKeXdnWW1sdVpDaDBhR2x6TENCbWRXNWpkR2x2YmlBb1pYSnlLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFJvYVhNdVpXMXBkQ2duWlhKeWIzSW5MQ0JsY25JcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDBwS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbGtJRDBnWkdWd1RXRndMbWxrTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnRiMlFnUFNCeVpXZHBjM1J5ZVZ0cFpGMDdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdVMnRwY0NCemNHVmphV0ZzSUcxdlpIVnNaWE1nYkdsclpTQW5jbVZ4ZFdseVpTY3NJQ2RsZUhCdmNuUnpKeXdnSjIxdlpIVnNaU2N1SUVGc2MyOHNJR1J2YmlkMElHTmhiR3dnWlc1aFlteGxJR2xtSUdsMElHbHpJRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QmhiSEpsWVdSNUlHVnVZV0pzWldRc0lHbHRjRzl5ZEdGdWRDQnBiaUJqYVhKamRXeGhjaUJrWlhCbGJtUmxibU41SUdOaGMyVnpMbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9JV2hoYzFCeWIzQW9hR0Z1Wkd4bGNuTXNJR2xrS1NBbUppQnRiMlFnSmlZZ0lXMXZaQzVsYm1GaWJHVmtLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JqYjI1MFpYaDBMbVZ1WVdKc1pTaGtaWEJOWVhBc0lIUm9hWE1wTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmU2twTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdSVzVoWW14bElHVmhZMmdnY0d4MVoybHVJSFJvWVhRZ2FYTWdkWE5sWkNCcGJpQWdZU0JrWlhCbGJtUmxibU41TGx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdWaFkyaFFjbTl3S0hSb2FYTXVjR3gxWjJsdVRXRndjeXdnWW1sdVpDaDBhR2x6TENCbWRXNWpkR2x2YmlBb2NHeDFaMmx1VFdGd0tTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUd4bGRDQnRiMlFnUFNCblpYUlBkMjRvY21WbmFYTjBjbmtzSUhCc2RXZHBiazFoY0M1cFpDazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUlDaHRiMlFnSmlZZ0lXMXZaQzVsYm1GaWJHVmtLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JqYjI1MFpYaDBMbVZ1WVdKc1pTaHdiSFZuYVc1TllYQXNJSFJvYVhNcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlNrcE8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkR2hwY3k1bGJtRmliR2x1WnlBOUlHWmhiSE5sTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RHaHBjeTVqYUdWamF5Z3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ2ZTeGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ2IyNDZJR1oxYm1OMGFXOXVJQ2h1WVcxbExDQmpZaWtnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUd4bGRDQmpZbk1nUFNCMGFHbHpMbVYyWlc1MGMxdHVZVzFsWFR0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9JV05pY3lrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JqWW5NZ1BTQjBhR2x6TG1WMlpXNTBjMXR1WVcxbFhTQTlJRnRkTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCalluTXVjSFZ6YUNoallpazdYRzRnSUNBZ0lDQWdJQ0FnSUNCOUxGeHVYRzRnSUNBZ0lDQWdJQ0FnSUNCbGJXbDBPaUJtZFc1amRHbHZiaUFvYm1GdFpTd2daWFowS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1pXRmphQ2gwYUdsekxtVjJaVzUwYzF0dVlXMWxYU3dnWm5WdVkzUnBiMjRnS0dOaUtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdOaUtHVjJkQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWWdLRzVoYldVZ1BUMDlJQ2RsY25KdmNpY3BJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdUbTkzSUhSb1lYUWdkR2hsSUdWeWNtOXlJR2hoYm1Sc1pYSWdkMkZ6SUhSeWFXZG5aWEpsWkN3Z2NtVnRiM1psSUhSb1pTQnNhWE4wWlc1bGNuTXNJSE5wYm1ObElIUm9hWE1nWW5KdmEyVnVJRTF2WkhWc1pTQnBibk4wWVc1alpWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCallXNGdjM1JoZVNCaGNtOTFibVFnWm05eUlHRWdkMmhwYkdVZ2FXNGdkR2hsSUhKbFoybHpkSEo1TGx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmtaV3hsZEdVZ2RHaHBjeTVsZG1WdWRITmJibUZ0WlYwN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNCOU8xeHVYRzRnSUNBZ0lDQWdJR1oxYm1OMGFXOXVJR05oYkd4SFpYUk5iMlIxYkdVb1lYSm5jeWtnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdMeThnVTJ0cGNDQnRiMlIxYkdWeklHRnNjbVZoWkhrZ1pHVm1hVzVsWkM1Y2JpQWdJQ0FnSUNBZ0lDQWdJR2xtSUNnaGFHRnpVSEp2Y0Noa1pXWnBibVZrTENCaGNtZHpXekJkS1NrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHZGxkRTF2WkhWc1pTaHRZV3RsVFc5a2RXeGxUV0Z3S0dGeVozTmJNRjBzSUc1MWJHd3NJSFJ5ZFdVcEtTNXBibWwwS0dGeVozTmJNVjBzSUdGeVozTmJNbDBwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ1puVnVZM1JwYjI0Z2NtVnRiM1psVEdsemRHVnVaWElvYm05a1pTd2dablZ1WXl3Z2JtRnRaU3dnYVdWT1lXMWxLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQXZMeUJHWVhadmNpQmtaWFJoWTJoRmRtVnVkQ0JpWldOaGRYTmxJRzltSUVsRk9TQnBjM04xWlN3Z2MyVmxJR0YwZEdGamFFVjJaVzUwTDJGa1pFVjJaVzUwVEdsemRHVnVaWElnWTI5dGJXVnVkQ0JsYkhObGQyaGxjbVVnYVc0Z2RHaHBjeUJtYVd4bExseHVJQ0FnSUNBZ0lDQWdJQ0FnYVdZZ0tHNXZaR1V1WkdWMFlXTm9SWFpsYm5RZ0ppWWdJV2x6VDNCbGNtRXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCUWNtOWlZV0pzZVNCSlJTNGdTV1lnYm05MElHbDBJSGRwYkd3Z2RHaHliM2NnWVc0Z1pYSnliM0lzSUhkb2FXTm9JSGRwYkd3Z1ltVWdkWE5sWm5Wc0lIUnZJR3R1YjNjdVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWWdLR2xsVG1GdFpTa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCdWIyUmxMbVJsZEdGamFFVjJaVzUwS0dsbFRtRnRaU3dnWm5WdVl5azdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0FnSUNBZ2ZTQmxiSE5sSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCdWIyUmxMbkpsYlc5MlpVVjJaVzUwVEdsemRHVnVaWElvYm1GdFpTd2dablZ1WXl3Z1ptRnNjMlVwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0x5b3FYRzRnSUNBZ0lDQWdJQ0FxSUVkcGRtVnVJR0Z1SUdWMlpXNTBJR1p5YjIwZ1lTQnpZM0pwY0hRZ2JtOWtaU3dnWjJWMElIUm9aU0J5WlhGMWFYSmxhbk1nYVc1bWJ5Qm1jbTl0SUdsMExDQmhibVFnZEdobGJpQnlaVzF2ZG1WeklIUm9aU0JsZG1WdWRDQnNhWE4wWlc1bGNuTWdiMjVjYmlBZ0lDQWdJQ0FnSUNvZ2RHaGxJRzV2WkdVdVhHNGdJQ0FnSUNBZ0lDQXFYRzRnSUNBZ0lDQWdJQ0FxSUVCd1lYSmhiU0I3UlhabGJuUjlJR1YyZEZ4dUlDQWdJQ0FnSUNBZ0tseHVJQ0FnSUNBZ0lDQWdLaUJBY21WMGRYSnVJSHRQWW1wbFkzUjlYRzRnSUNBZ0lDQWdJQ0FxTDF4dUlDQWdJQ0FnSUNCbWRXNWpkR2x2YmlCblpYUlRZM0pwY0hSRVlYUmhLR1YyZENrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1ZYTnBibWNnWTNWeWNtVnVkRlJoY21kbGRDQnBibk4wWldGa0lHOW1JSFJoY21kbGRDQm1iM0lnUm1seVpXWnZlQ0F5TGpBbmN5QnpZV3RsTGlCT2IzUWdZV3hzSUc5c1pDQmljbTkzYzJWeWN5QjNhV3hzSUdKbElITjFjSEJ2Y25SbFpDd2dZblYwSUZ4dUlDQWdJQ0FnSUNBZ0lDQWdMeThnZEdocGN5QnZibVVnZDJGeklHVmhjM2tnWlc1dmRXZG9JSFJ2SUhOMWNIQnZjblFnWVc1a0lITjBhV3hzSUcxaGEyVnpJSE5sYm5ObExseHVJQ0FnSUNBZ0lDQWdJQ0FnYkdWMElHNXZaR1VnUFNCbGRuUXVZM1Z5Y21WdWRGUmhjbWRsZENCOGZDQmxkblF1YzNKalJXeGxiV1Z1ZER0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1VtVnRiM1psSUhSb1pTQnNhWE4wWlc1bGNuTWdiMjVqWlNCb1pYSmxMbHh1SUNBZ0lDQWdJQ0FnSUNBZ2NtVnRiM1psVEdsemRHVnVaWElvYm05a1pTd2dZMjl1ZEdWNGRDNXZibE5qY21sd2RFeHZZV1FzSUNkc2IyRmtKeXdnSjI5dWNtVmhaSGx6ZEdGMFpXTm9ZVzVuWlNjcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnY21WdGIzWmxUR2x6ZEdWdVpYSW9ibTlrWlN3Z1kyOXVkR1Y0ZEM1dmJsTmpjbWx3ZEVWeWNtOXlMQ0FuWlhKeWIzSW5LVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdjbVYwZFhKdUlIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnViMlJsT2lCdWIyUmxMRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xrT2lCdWIyUmxJQ1ltSUc1dlpHVXVaMlYwUVhSMGNtbGlkWFJsS0Nka1lYUmhMWEpsY1hWcGNtVnRiMlIxYkdVbktWeHVJQ0FnSUNBZ0lDQWdJQ0FnZlR0Y2JpQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJR1oxYm1OMGFXOXVJR2x1ZEdGclpVUmxabWx1WlhNb0tTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCc1pYUWdZWEpuY3p0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1FXNTVJR1JsWm1sdVpXUWdiVzlrZFd4bGN5QnBiaUIwYUdVZ1oyeHZZbUZzSUhGMVpYVmxMQ0JwYm5SaGEyVWdkR2hsYlNCdWIzY3VYRzRnSUNBZ0lDQWdJQ0FnSUNCMFlXdGxSMnh2WW1Gc1VYVmxkV1VvS1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1RXRnJaU0J6ZFhKbElHRnVlU0J5WlcxaGFXNXBibWNnWkdWbVVYVmxkV1VnYVhSbGJYTWdaMlYwSUhCeWIzQmxjbXg1SUhCeWIyTmxjM05sWkM1Y2JpQWdJQ0FnSUNBZ0lDQWdJSGRvYVd4bElDaGtaV1pSZFdWMVpTNXNaVzVuZEdncElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmhjbWR6SUQwZ1pHVm1VWFZsZFdVdWMyaHBablFvS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9ZWEpuYzFzd1hTQTlQVDBnYm5Wc2JDa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCeVpYUjFjbTRnYjI1RmNuSnZjaWh0WVd0bFJYSnliM0lvSjIxcGMyMWhkR05vSnl3Z0owMXBjMjFoZEdOb1pXUWdZVzV2Ym5sdGIzVnpJR1JsWm1sdVpTZ3BJRzF2WkhWc1pUb2dKeUFyWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JoY21kelcyRnlaM011YkdWdVozUm9JQzBnTVYwcEtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlJR1ZzYzJVZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCaGNtZHpJR0Z5WlNCcFpDd2daR1Z3Y3l3Z1ptRmpkRzl5ZVM0Z1UyaHZkV3hrSUdKbElHNXZjbTFoYkdsNlpXUWdZbmtnZEdobFhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUdSbFptbHVaU2dwSUdaMWJtTjBhVzl1TGx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmpZV3hzUjJWMFRXOWtkV3hsS0dGeVozTXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdJQ0FnSUdOdmJuUmxlSFF1WkdWbVVYVmxkV1ZOWVhBZ1BTQjdmVHRjYmlBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lHTnZiblJsZUhRZ1BTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCamIyNW1hV2M2SUdOdmJtWnBaeXhjYmlBZ0lDQWdJQ0FnSUNBZ0lHTnZiblJsZUhST1lXMWxPaUJqYjI1MFpYaDBUbUZ0WlN4Y2JpQWdJQ0FnSUNBZ0lDQWdJSEpsWjJsemRISjVPaUJ5WldkcGMzUnllU3hjYmlBZ0lDQWdJQ0FnSUNBZ0lHUmxabWx1WldRNklHUmxabWx1WldRc1hHNGdJQ0FnSUNBZ0lDQWdJQ0IxY214R1pYUmphR1ZrT2lCMWNteEdaWFJqYUdWa0xGeHVJQ0FnSUNBZ0lDQWdJQ0FnWkdWbVVYVmxkV1U2SUdSbFpsRjFaWFZsTEZ4dUlDQWdJQ0FnSUNBZ0lDQWdaR1ZtVVhWbGRXVk5ZWEE2SUh0OUxGeHVJQ0FnSUNBZ0lDQWdJQ0FnVFc5a2RXeGxPaUJOYjJSMWJHVXNYRzRnSUNBZ0lDQWdJQ0FnSUNCdFlXdGxUVzlrZFd4bFRXRndPaUJ0WVd0bFRXOWtkV3hsVFdGd0xGeHVJQ0FnSUNBZ0lDQWdJQ0FnYm1WNGRGUnBZMnM2SUhKbGNTNXVaWGgwVkdsamF5eGNiaUFnSUNBZ0lDQWdJQ0FnSUc5dVJYSnliM0k2SUc5dVJYSnliM0lzWEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQzhxS2x4dUlDQWdJQ0FnSUNBZ0lDQWdJQ29nVTJWMElHRWdZMjl1Wm1sbmRYSmhkR2x2YmlCbWIzSWdkR2hsSUdOdmJuUmxlSFF1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdLbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDb2dRSEJoY21GdElIdFBZbXBsWTNSOUlHTm1aeUJqYjI1bWFXY2diMkpxWldOMElIUnZJR2x1ZEdWbmNtRjBaUzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQXFMMXh1SUNBZ0lDQWdJQ0FnSUNBZ1kyOXVabWxuZFhKbE9pQm1kVzVqZEdsdmJpQW9ZMlpuS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdUV0ZyWlNCemRYSmxJSFJvWlNCaVlYTmxWWEpzSUdWdVpITWdhVzRnWVNCemJHRnphQzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb1kyWm5MbUpoYzJWVmNtd3BJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWWdLR05tWnk1aVlYTmxWWEpzTG1Ob1lYSkJkQ2hqWm1jdVltRnpaVlZ5YkM1c1pXNW5kR2dnTFNBeEtTQWhQVDBnSnk4bktTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmpabWN1WW1GelpWVnliQ0FyUFNBbkx5YzdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJUWVhabElHOW1aaUIwYUdVZ2NHRjBhSE1nYzJsdVkyVWdkR2hsZVNCeVpYRjFhWEpsSUhOd1pXTnBZV3dnY0hKdlkyVnpjMmx1Wnl3Z2RHaGxlU0JoY21VZ1lXUmthWFJwZG1VdVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2JHVjBJSE5vYVcwZ1BTQmpiMjVtYVdjdWMyaHBiU3hjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2IySnFjeUE5SUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhCaGRHaHpPaUIwY25WbExGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1luVnVaR3hsY3pvZ2RISjFaU3hjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR052Ym1acFp6b2dkSEoxWlN4Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUcxaGNEb2dkSEoxWlZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWldGamFGQnliM0FvWTJabkxDQm1kVzVqZEdsdmJpQW9kbUZzZFdVc0lIQnliM0FwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lnS0c5aWFuTmJjSEp2Y0YwcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2doWTI5dVptbG5XM0J5YjNCZEtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWTI5dVptbG5XM0J5YjNCZElEMGdlMzA3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J0YVhocGJpaGpiMjVtYVdkYmNISnZjRjBzSUhaaGJIVmxMQ0IwY25WbExDQjBjblZsS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmU0JsYkhObElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHTnZibVpwWjF0d2NtOXdYU0E5SUhaaGJIVmxPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZTazdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCU1pYWmxjbk5sSUcxaGNDQjBhR1VnWW5WdVpHeGxjMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtSUNoalptY3VZblZ1Wkd4bGN5a2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCbFlXTm9VSEp2Y0NoalptY3VZblZ1Wkd4bGN5d2dablZ1WTNScGIyNGdLSFpoYkhWbExDQndjbTl3S1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCbFlXTm9LSFpoYkhWbExDQm1kVzVqZEdsdmJpQW9kaWtnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2gySUNFOVBTQndjbTl3S1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdKMWJtUnNaWE5OWVhCYmRsMGdQU0J3Y205d08xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDBwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5Qk5aWEpuWlNCemFHbHRYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lnS0dObVp5NXphR2x0S1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1ZoWTJoUWNtOXdLR05tWnk1emFHbHRMQ0JtZFc1amRHbHZiaUFvZG1Gc2RXVXNJR2xrS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5Qk9iM0p0WVd4cGVtVWdkR2hsSUhOMGNuVmpkSFZ5WlZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYVdZZ0tHbHpRWEp5WVhrb2RtRnNkV1VwS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkbUZzZFdVZ1BTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHUmxjSE02SUhaaGJIVmxYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlR0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUlDZ29kbUZzZFdVdVpYaHdiM0owY3lCOGZDQjJZV3gxWlM1cGJtbDBLU0FtSmlBaGRtRnNkV1V1Wlhod2IzSjBjMFp1S1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkbUZzZFdVdVpYaHdiM0owYzBadUlEMGdZMjl1ZEdWNGRDNXRZV3RsVTJocGJVVjRjRzl5ZEhNb2RtRnNkV1VwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYzJocGJWdHBaRjBnUFNCMllXeDFaVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZTazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdOdmJtWnBaeTV6YUdsdElEMGdjMmhwYlR0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJCWkdwMWMzUWdjR0ZqYTJGblpYTWdhV1lnYm1WalpYTnpZWEo1TGx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUlDaGpabWN1Y0dGamEyRm5aWE1wSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdaV0ZqYUNoalptY3VjR0ZqYTJGblpYTXNJR1oxYm1OMGFXOXVJQ2h3YTJkUFltb3BJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR3hsZENCc2IyTmhkR2x2Yml3Z2JtRnRaVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnY0d0blQySnFJRDBnZEhsd1pXOW1JSEJyWjA5aWFpQTlQVDBnSjNOMGNtbHVaeWNnUHlCN2JtRnRaVG9nY0d0blQySnFmU0E2SUhCclowOWlhanRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYm1GdFpTQTlJSEJyWjA5aWFpNXVZVzFsTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYkc5allYUnBiMjRnUFNCd2EyZFBZbW91Ykc5allYUnBiMjQ3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb2JHOWpZWFJwYjI0cElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JqYjI1bWFXY3VjR0YwYUhOYmJtRnRaVjBnUFNCd2EyZFBZbW91Ykc5allYUnBiMjQ3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklGTmhkbVVnY0c5cGJuUmxjaUIwYnlCdFlXbHVJRzF2WkhWc1pTQkpSQ0JtYjNJZ2NHdG5JRzVoYldVdUlGSmxiVzkyWlNCc1pXRmthVzVuSUdSdmRDQnBiaUJ0WVdsdUxDQnpieUJ0WVdsdUlIQmhkR2h6SUdGeVpTQmNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJRzV2Y20xaGJHbDZaV1FzSUdGdVpDQnlaVzF2ZG1VZ1lXNTVJSFJ5WVdsc2FXNW5JQzVxY3l3Z2MybHVZMlVnWkdsbVptVnlaVzUwSUhCaFkydGhaMlVnWlc1MmN5Qm9ZWFpsSUdScFptWmxjbVZ1ZENCY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklHTnZiblpsYm5ScGIyNXpPaUJ6YjIxbElIVnpaU0JoSUcxdlpIVnNaU0J1WVcxbExDQnpiMjFsSUhWelpTQmhJR1pwYkdVZ2JtRnRaUzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR052Ym1acFp5NXdhMmR6VzI1aGJXVmRJRDBnY0d0blQySnFMbTVoYldVZ0t5QW5MeWNnS3lBb2NHdG5UMkpxTG0xaGFXNGdmSHdnSjIxaGFXNG5LVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM1eVpYQnNZV05sS0dOMWNuSkVhWEpTWldkRmVIQXNJQ2NuS1Z4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDNXlaWEJzWVdObEtHcHpVM1ZtWm1sNFVtVm5SWGh3TENBbkp5azdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgwcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUVsbUlIUm9aWEpsSUdGeVpTQmhibmtnWENKM1lXbDBhVzVuSUhSdklHVjRaV04xZEdWY0lpQnRiMlIxYkdWeklHbHVJSFJvWlNCeVpXZHBjM1J5ZVN3Z2RYQmtZWFJsSUhSb1pTQnRZWEJ6SUdadmNpQjBhR1Z0TENCemFXNWpaU0IwYUdWcGNpQmNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJwYm1adkxDQnNhV3RsSUZWU1RITWdkRzhnYkc5aFpDd2diV0Y1SUdoaGRtVWdZMmhoYm1kbFpDNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmxZV05vVUhKdmNDaHlaV2RwYzNSeWVTd2dablZ1WTNScGIyNGdLRzF2WkN3Z2FXUXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdTV1lnYlc5a2RXeGxJR0ZzY21WaFpIa2dhR0Z6SUdsdWFYUWdZMkZzYkdWa0xDQnphVzVqWlNCcGRDQnBjeUIwYjI4Z2JHRjBaU0IwYnlCdGIyUnBabmtnZEdobGJTd2dZVzVrSUdsbmJtOXlaU0IxYm01dmNtMWhiR2w2WldRZ1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUc5dVpYTWdjMmx1WTJVZ2RHaGxlU0JoY21VZ2RISmhibk5wWlc1MExseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb0lXMXZaQzVwYm1sMFpXUWdKaVlnSVcxdlpDNXRZWEF1ZFc1dWIzSnRZV3hwZW1Wa0tTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnRiMlF1YldGd0lEMGdiV0ZyWlUxdlpIVnNaVTFoY0NocFpDd2diblZzYkN3Z2RISjFaU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlLVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklFbG1JR0VnWkdWd2N5QmhjbkpoZVNCdmNpQmhJR052Ym1acFp5QmpZV3hzWW1GamF5QnBjeUJ6Y0dWamFXWnBaV1FzSUhSb1pXNGdZMkZzYkNCeVpYRjFhWEpsSUhkcGRHZ2dkR2h2YzJVZ1lYSm5jeTRnVkdocGN5QnBjeUIxYzJWbWRXd2dYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnZDJobGJpQnlaWEYxYVhKbElHbHpJR1JsWm1sdVpXUWdZWE1nWVNCamIyNW1hV2NnYjJKcVpXTjBJR0psWm05eVpTQnlaWEYxYVhKbExtcHpJR2x6SUd4dllXUmxaQzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb1kyWm5MbVJsY0hNZ2ZId2dZMlpuTG1OaGJHeGlZV05yS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR052Ym5SbGVIUXVjbVZ4ZFdseVpTaGpabWN1WkdWd2N5QjhmQ0JiWFN3Z1kyWm5MbU5oYkd4aVlXTnJLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlMRnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQnRZV3RsVTJocGJVVjRjRzl5ZEhNNklHWjFibU4wYVc5dUlDaDJZV3gxWlNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHWjFibU4wYVc5dUlHWnVLQ2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnNaWFFnY21WME8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb2RtRnNkV1V1YVc1cGRDa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjbVYwSUQwZ2RtRnNkV1V1YVc1cGRDNWhjSEJzZVNobmJHOWlZV3dzSUdGeVozVnRaVzUwY3lrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2NtVjBkWEp1SUhKbGRDQjhmQ0FvZG1Gc2RXVXVaWGh3YjNKMGN5QW1KaUJuWlhSSGJHOWlZV3dvZG1Gc2RXVXVaWGh3YjNKMGN5a3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhKbGRIVnliaUJtYmp0Y2JpQWdJQ0FnSUNBZ0lDQWdJSDBzWEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJRzFoYTJWU1pYRjFhWEpsT2lCbWRXNWpkR2x2YmlBb2NtVnNUV0Z3TENCdmNIUnBiMjV6S1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2IzQjBhVzl1Y3lBOUlHOXdkR2x2Ym5NZ2ZId2dlMzA3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCbWRXNWpkR2x2YmlCc2IyTmhiRkpsY1hWcGNtVW9aR1Z3Y3l3Z1kyRnNiR0poWTJzc0lHVnljbUpoWTJzcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYkdWMElHbGtMQ0J0WVhBc0lISmxjWFZwY21WTmIyUTdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWWdLRzl3ZEdsdmJuTXVaVzVoWW14bFFuVnBiR1JEWVd4c1ltRmpheUFtSmlCallXeHNZbUZqYXlBbUppQnBjMFoxYm1OMGFXOXVLR05oYkd4aVlXTnJLU2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWTJGc2JHSmhZMnN1WDE5eVpYRjFhWEpsU25OQ2RXbHNaQ0E5SUhSeWRXVTdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb2RIbHdaVzltSUdSbGNITWdQVDA5SUNkemRISnBibWNuS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9hWE5HZFc1amRHbHZiaWhqWVd4c1ltRmpheWtwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJKYm5aaGJHbGtJR05oYkd4Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnlaWFIxY200Z2IyNUZjbkp2Y2lodFlXdGxSWEp5YjNJb0ozSmxjWFZwY21WaGNtZHpKeXdnSjBsdWRtRnNhV1FnY21WeGRXbHlaU0JqWVd4c0p5a3NJR1Z5Y21KaFkyc3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCSlppQnlaWEYxYVhKbGZHVjRjRzl5ZEhOOGJXOWtkV3hsSUdGeVpTQnlaWEYxWlhOMFpXUXNJR2RsZENCMGFHVWdkbUZzZFdVZ1ptOXlJSFJvWlcwZ1puSnZiU0IwYUdVZ2MzQmxZMmxoYkNCb1lXNWtiR1Z5Y3k0Z1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QkRZWFpsWVhRNklIUm9hWE1nYjI1c2VTQjNiM0pyY3lCM2FHbHNaU0J0YjJSMWJHVWdhWE1nWW1WcGJtY2daR1ZtYVc1bFpDNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2h5Wld4TllYQWdKaVlnYUdGelVISnZjQ2hvWVc1a2JHVnljeXdnWkdWd2N5a3BJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCeVpYUjFjbTRnYUdGdVpHeGxjbk5iWkdWd2MxMG9jbVZuYVhOMGNubGJjbVZzVFdGd0xtbGtYU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklGTjVibU5vY205dWIzVnpJR0ZqWTJWemN5QjBieUJ2Ym1VZ2JXOWtkV3hsTGlCSlppQnlaWEYxYVhKbExtZGxkQ0JwY3lCaGRtRnBiR0ZpYkdVZ0tHRnpJR2x1SUhSb1pTQk9iMlJsSUdGa1lYQjBaWElwTENCY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklIQnlaV1psY2lCMGFHRjBMbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lnS0hKbGNTNW5aWFFwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnlaWFIxY200Z2NtVnhMbWRsZENoamIyNTBaWGgwTENCa1pYQnpMQ0J5Wld4TllYQXNJR3h2WTJGc1VtVnhkV2x5WlNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJRTV2Y20xaGJHbDZaU0J0YjJSMWJHVWdibUZ0WlN3Z2FXWWdhWFFnWTI5dWRHRnBibk1nTGlCdmNpQXVMbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdiV0Z3SUQwZ2JXRnJaVTF2WkhWc1pVMWhjQ2hrWlhCekxDQnlaV3hOWVhBc0lHWmhiSE5sTENCMGNuVmxLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xrSUQwZ2JXRndMbWxrTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9JV2hoYzFCeWIzQW9aR1ZtYVc1bFpDd2dhV1FwS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjbVYwZFhKdUlHOXVSWEp5YjNJb2JXRnJaVVZ5Y205eUtDZHViM1JzYjJGa1pXUW5MQ0FuVFc5a2RXeGxJRzVoYldVZ1hDSW5JQ3RjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1FnSzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FuWENJZ2FHRnpJRzV2ZENCaVpXVnVJR3h2WVdSbFpDQjVaWFFnWm05eUlHTnZiblJsZUhRNklDY2dLMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmpiMjUwWlhoMFRtRnRaU0FyWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ2h5Wld4TllYQWdQeUFuSnlBNklDY3VJRlZ6WlNCeVpYRjFhWEpsS0Z0ZEtTY3BLU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J5WlhSMWNtNGdaR1ZtYVc1bFpGdHBaRjA3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QkhjbUZpSUdSbFptbHVaWE1nZDJGcGRHbHVaeUJwYmlCMGFHVWdaMnh2WW1Gc0lIRjFaWFZsTGx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBiblJoYTJWRVpXWnBibVZ6S0NrN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1RXRnlheUJoYkd3Z2RHaGxJR1JsY0dWdVpHVnVZMmxsY3lCaGN5QnVaV1ZrYVc1bklIUnZJR0psSUd4dllXUmxaQzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1kyOXVkR1Y0ZEM1dVpYaDBWR2xqYXlobWRXNWpkR2x2YmlBb0tTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJUYjIxbElHUmxabWx1WlhNZ1kyOTFiR1FnYUdGMlpTQmlaV1Z1SUdGa1pHVmtJSE5wYm1ObElIUm9aVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnY21WeGRXbHlaU0JqWVd4c0xDQmpiMnhzWldOMElIUm9aVzB1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwYm5SaGEyVkVaV1pwYm1WektDazdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSEpsY1hWcGNtVk5iMlFnUFNCblpYUk5iMlIxYkdVb2JXRnJaVTF2WkhWc1pVMWhjQ2h1ZFd4c0xDQnlaV3hOWVhBcEtUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnVTNSdmNtVWdhV1lnYldGd0lHTnZibVpwWnlCemFHOTFiR1FnWW1VZ1lYQndiR2xsWkNCMGJ5QjBhR2x6SUhKbGNYVnBjbVZjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUdOaGJHd2dabTl5SUdSbGNHVnVaR1Z1WTJsbGN5NWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lISmxjWFZwY21WTmIyUXVjMnRwY0UxaGNDQTlJRzl3ZEdsdmJuTXVjMnRwY0UxaGNEdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjbVZ4ZFdseVpVMXZaQzVwYm1sMEtHUmxjSE1zSUdOaGJHeGlZV05yTENCbGNuSmlZV05yTENCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdaVzVoWW14bFpEb2dkSEoxWlZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlNrN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHTm9aV05yVEc5aFpHVmtLQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMHBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lISmxkSFZ5YmlCc2IyTmhiRkpsY1hWcGNtVTdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYldsNGFXNG9iRzlqWVd4U1pYRjFhWEpsTENCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2x6UW5KdmQzTmxjam9nYVhOQ2NtOTNjMlZ5TEZ4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzhxS2x4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdLaUJEYjI1MlpYSjBjeUJoSUcxdlpIVnNaU0J1WVcxbElDc2dMbVY0ZEdWdWMybHZiaUJwYm5SdklHRnVJRlZTVENCd1lYUm9MbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0tseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnS2lBcVVtVnhkV2x5WlhNcUlIUm9aU0IxYzJVZ2IyWWdZU0J0YjJSMWJHVWdibUZ0WlM0Z1NYUWdaRzlsY3lCdWIzUWdjM1Z3Y0c5eWRDQjFjMmx1WnlCd2JHRnBiaUJWVWt4eklHeHBhMlVnYm1GdFpWUnZWWEpzTGx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdLaTljYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RHOVZjbXc2SUdaMWJtTjBhVzl1SUNodGIyUjFiR1ZPWVcxbFVHeDFjMFY0ZENrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2JHVjBJR1Y0ZEN4Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBibVJsZUNBOUlHMXZaSFZzWlU1aGJXVlFiSFZ6UlhoMExteGhjM1JKYm1SbGVFOW1LQ2N1Snlrc1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjMlZuYldWdWRDQTlJRzF2WkhWc1pVNWhiV1ZRYkhWelJYaDBMbk53YkdsMEtDY3ZKeWxiTUYwc1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhWE5TWld4aGRHbDJaU0E5SUhObFoyMWxiblFnUFQwOUlDY3VKeUI4ZkNCelpXZHRaVzUwSUQwOVBTQW5MaTRuTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QklZWFpsSUdFZ1ptbHNaU0JsZUhSbGJuTnBiMjRnWVd4cFlYTXNJR0Z1WkNCcGRDQnBjeUJ1YjNRZ2RHaGxYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJrYjNSeklHWnliMjBnWVNCeVpXeGhkR2wyWlNCd1lYUm9MbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lnS0dsdVpHVjRJQ0U5UFNBdE1TQW1KaUFvSVdselVtVnNZWFJwZG1VZ2ZId2dhVzVrWlhnZ1BpQXhLU2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHVjRkQ0E5SUcxdlpIVnNaVTVoYldWUWJIVnpSWGgwTG5OMVluTjBjbWx1WnlocGJtUmxlQ3dnYlc5a2RXeGxUbUZ0WlZCc2RYTkZlSFF1YkdWdVozUm9LVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCdGIyUjFiR1ZPWVcxbFVHeDFjMFY0ZENBOUlHMXZaSFZzWlU1aGJXVlFiSFZ6UlhoMExuTjFZbk4wY21sdVp5Z3dMQ0JwYm1SbGVDazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSEpsZEhWeWJpQmpiMjUwWlhoMExtNWhiV1ZVYjFWeWJDaHViM0p0WVd4cGVtVW9iVzlrZFd4bFRtRnRaVkJzZFhORmVIUXNYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnY21Wc1RXRndJQ1ltSUhKbGJFMWhjQzVwWkN3Z2RISjFaU2tzSUdWNGRDd2dkSEoxWlNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDBzWEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdaR1ZtYVc1bFpEb2dablZ1WTNScGIyNGdLR2xrS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCeVpYUjFjbTRnYUdGelVISnZjQ2hrWldacGJtVmtMQ0J0WVd0bFRXOWtkV3hsVFdGd0tHbGtMQ0J5Wld4TllYQXNJR1poYkhObExDQjBjblZsS1M1cFpDazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgwc1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYzNCbFkybG1hV1ZrT2lCbWRXNWpkR2x2YmlBb2FXUXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xrSUQwZ2JXRnJaVTF2WkhWc1pVMWhjQ2hwWkN3Z2NtVnNUV0Z3TENCbVlXeHpaU3dnZEhKMVpTa3VhV1E3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J5WlhSMWNtNGdhR0Z6VUhKdmNDaGtaV1pwYm1Wa0xDQnBaQ2tnZkh3Z2FHRnpVSEp2Y0NoeVpXZHBjM1J5ZVN3Z2FXUXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZTazdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCUGJteDVJR0ZzYkc5M0lIVnVaR1ZtSUc5dUlIUnZjQ0JzWlhabGJDQnlaWEYxYVhKbElHTmhiR3h6TGx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUlDZ2hjbVZzVFdGd0tTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUd4dlkyRnNVbVZ4ZFdseVpTNTFibVJsWmlBOUlHWjFibU4wYVc5dUlDaHBaQ2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1FtbHVaQ0JoYm5rZ2QyRnBkR2x1WnlCa1pXWnBibVVvS1NCallXeHNjeUIwYnlCMGFHbHpJR052Ym5SbGVIUXNJR1pwZUNCbWIzSWdJelF3T0M1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhSaGEyVkhiRzlpWVd4UmRXVjFaU2dwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCc1pYUWdiV0Z3SUQwZ2JXRnJaVTF2WkhWc1pVMWhjQ2hwWkN3Z2NtVnNUV0Z3TENCMGNuVmxLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR3hsZENCdGIyUWdQU0JuWlhSUGQyNG9jbVZuYVhOMGNua3NJR2xrS1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2JXOWtMblZ1WkdWbVpXUWdQU0IwY25WbE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2NtVnRiM1psVTJOeWFYQjBLR2xrS1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1pHVnNaWFJsSUdSbFptbHVaV1JiYVdSZE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1pHVnNaWFJsSUhWeWJFWmxkR05vWldSYmJXRndMblZ5YkYwN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCa1pXeGxkR1VnZFc1a1pXWkZkbVZ1ZEhOYmFXUmRPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCRGJHVmhiaUJ4ZFdWMVpXUWdaR1ZtYVc1bGN5QjBiMjh1SUVkdklHSmhZMnQzWVhKa2N5QnBiaUJoY25KaGVTQnpieUIwYUdGMElIUm9aU0J6Y0d4cFkyVnpJR1J2SUc1dmRDQnRaWE56SUhWd0lIUm9aU0JjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUdsMFpYSmhkR2x2Ymk1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdWaFkyaFNaWFpsY25ObEtHUmxabEYxWlhWbExDQm1kVzVqZEdsdmJpQW9ZWEpuY3l3Z2FTa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUlDaGhjbWR6V3pCZElEMDlQU0JwWkNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCa1pXWlJkV1YxWlM1emNHeHBZMlVvYVN3Z01TazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCa1pXeGxkR1VnWTI5dWRHVjRkQzVrWldaUmRXVjFaVTFoY0Z0cFpGMDdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtSUNodGIyUXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QkliMnhrSUc5dUlIUnZJR3hwYzNSbGJtVnljeUJwYmlCallYTmxJSFJvWlNCdGIyUjFiR1VnZDJsc2JDQmlaU0JoZEhSbGJYQjBaV1FnZEc4Z1ltVWdjbVZzYjJGa1pXUWdkWE5wYm1jZ1lTQmNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCa2FXWm1aWEpsYm5RZ1kyOXVabWxuTGx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2h0YjJRdVpYWmxiblJ6TG1SbFptbHVaV1FwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZFc1a1pXWkZkbVZ1ZEhOYmFXUmRJRDBnYlc5a0xtVjJaVzUwY3p0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCamJHVmhibEpsWjJsemRISjVLR2xrS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCeVpYUjFjbTRnYkc5allXeFNaWEYxYVhKbE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnZlN4Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnTHlvcVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnS2lCRFlXeHNaV1FnZEc4Z1pXNWhZbXhsSUdFZ2JXOWtkV3hsSUdsbUlHbDBJR2x6SUhOMGFXeHNJR2x1SUhSb1pTQnlaV2RwYzNSeWVTQmhkMkZwZEdsdVp5QmxibUZpYkdWdFpXNTBMaUJCSUhObFkyOXVaQ0JoY21jc0lIQmhjbVZ1ZEN3Z2RHaGxYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0tpQndZWEpsYm5RZ2JXOWtkV3hsTENCcGN5QndZWE56WldRZ2FXNGdabTl5SUdOdmJuUmxlSFFzSUhkb1pXNGdkR2hwY3lCdFpYUm9iMlFnYVhNZ2IzWmxjbkpwWkdSbGJpQmllU0IwYUdVZ2IzQjBhVzFwZW1WeUxpQk9iM1FnYzJodmQyNGdhR1Z5WlZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ29nZEc4Z2EyVmxjQ0JqYjJSbElHTnZiWEJoWTNRdVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnS2k5Y2JpQWdJQ0FnSUNBZ0lDQWdJR1Z1WVdKc1pUb2dablZ1WTNScGIyNGdLR1JsY0UxaGNDa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR3hsZENCdGIyUWdQU0JuWlhSUGQyNG9jbVZuYVhOMGNua3NJR1JsY0UxaGNDNXBaQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYVdZZ0tHMXZaQ2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQm5aWFJOYjJSMWJHVW9aR1Z3VFdGd0tTNWxibUZpYkdVb0tUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNCOUxGeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBdktpcGNiaUFnSUNBZ0lDQWdJQ0FnSUNBcUlFbHVkR1Z5Ym1Gc0lHMWxkR2h2WkNCMWMyVmtJR0o1SUdWdWRtbHliMjV0Wlc1MElHRmtZWEIwWlhKeklIUnZJR052YlhCc1pYUmxJR0VnYkc5aFpDQmxkbVZ1ZEM1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FxWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdLaUJCSUd4dllXUWdaWFpsYm5RZ1kyOTFiR1FnWW1VZ1lTQnpZM0pwY0hRZ2JHOWhaQ0J2Y2lCcWRYTjBJR0VnYkc5aFpDQndZWE56SUdaeWIyMGdZU0J6ZVc1amFISnZibTkxY3lCc2IyRmtJR05oYkd3dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnS2x4dUlDQWdJQ0FnSUNBZ0lDQWdJQ29nUUhCaGNtRnRJSHRUZEhKcGJtZDlJRzF2WkhWc1pVNWhiV1VnVkdobElHNWhiV1VnYjJZZ2RHaGxJRzF2WkhWc1pTQjBieUJ3YjNSbGJuUnBZV3hzZVNCamIyMXdiR1YwWlM1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FxTDF4dUlDQWdJQ0FnSUNBZ0lDQWdZMjl0Y0d4bGRHVk1iMkZrT2lCbWRXNWpkR2x2YmlBb2JXOWtkV3hsVG1GdFpTa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR3hsZENCbWIzVnVaRHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JzWlhRZ1lYSm5jenRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JzWlhRZ2JXOWtPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR3hsZENCemFHbHRJRDBnWjJWMFQzZHVLR052Ym1acFp5NXphR2x0TENCdGIyUjFiR1ZPWVcxbEtTQjhmQ0I3ZlR0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCc1pYUWdjMmhGZUhCdmNuUnpJRDBnYzJocGJTNWxlSEJ2Y25Sek8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkR0ZyWlVkc2IySmhiRkYxWlhWbEtDazdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IzYUdsc1pTQW9aR1ZtVVhWbGRXVXViR1Z1WjNSb0tTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdGeVozTWdQU0JrWldaUmRXVjFaUzV6YUdsbWRDZ3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9ZWEpuYzFzd1hTQTlQVDBnYm5Wc2JDa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdZWEpuYzFzd1hTQTlJRzF2WkhWc1pVNWhiV1U3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCSlppQmhiSEpsWVdSNUlHWnZkVzVrSUdGdUlHRnViMjU1Ylc5MWN5QnRiMlIxYkdVZ1lXNWtJR0p2ZFc1a0lHbDBJSFJ2SUhSb2FYTWdibUZ0WlN3Z2RHaGxiaUIwYUdseklHbHpJSE52YldVZ2IzUm9aWElnWVc1dmJpQmNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJRzF2WkhWc1pTQjNZV2wwYVc1bklHWnZjaUJwZEhNZ1kyOXRjR3hsZEdWTWIyRmtJSFJ2SUdacGNtVXVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaUFvWm05MWJtUXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCaWNtVmhhenRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1p2ZFc1a0lEMGdkSEoxWlR0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmU0JsYkhObElHbG1JQ2hoY21keld6QmRJRDA5UFNCdGIyUjFiR1ZPWVcxbEtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJHYjNWdVpDQnRZWFJqYUdsdVp5QmtaV1pwYm1VZ1kyRnNiQ0JtYjNJZ2RHaHBjeUJ6WTNKcGNIUWhYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQm1iM1Z1WkNBOUlIUnlkV1U3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCallXeHNSMlYwVFc5a2RXeGxLR0Z5WjNNcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmpiMjUwWlhoMExtUmxabEYxWlhWbFRXRndJRDBnZTMwN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJFYnlCMGFHbHpJR0ZtZEdWeUlIUm9aU0JqZVdOc1pTQnZaaUJqWVd4c1IyVjBUVzlrZFd4bElHbHVJR05oYzJVZ2RHaGxJSEpsYzNWc2RGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJRzltSUhSb2IzTmxJR05oYkd4ekwybHVhWFFnWTJGc2JITWdZMmhoYm1kbGN5QjBhR1VnY21WbmFYTjBjbmt1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYlc5a0lEMGdaMlYwVDNkdUtISmxaMmx6ZEhKNUxDQnRiMlIxYkdWT1lXMWxLVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUlDZ2habTkxYm1RZ0ppWWdJV2hoYzFCeWIzQW9aR1ZtYVc1bFpDd2diVzlrZFd4bFRtRnRaU2tnSmlZZ2JXOWtJQ1ltSUNGdGIyUXVhVzVwZEdWa0tTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUlDaGpiMjVtYVdjdVpXNW1iM0pqWlVSbFptbHVaU0FtSmlBb0lYTm9SWGh3YjNKMGN5QjhmQ0FoWjJWMFIyeHZZbUZzS0hOb1JYaHdiM0owY3lrcEtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaUFvYUdGelVHRjBhRVpoYkd4aVlXTnJLRzF2WkhWc1pVNWhiV1VwS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjbVYwZFhKdU8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZTQmxiSE5sSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnlaWFIxY200Z2IyNUZjbkp2Y2lodFlXdGxSWEp5YjNJb0oyNXZaR1ZtYVc1bEp5eGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0owNXZJR1JsWm1sdVpTQmpZV3hzSUdadmNpQW5JQ3NnYlc5a2RXeGxUbUZ0WlN4Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYm5Wc2JDeGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1cyMXZaSFZzWlU1aGJXVmRLU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMGdaV3h6WlNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QkJJSE5qY21sd2RDQjBhR0YwSUdSdlpYTWdibTkwSUdOaGJHd2daR1ZtYVc1bEtDa3NJSE52SUdwMWMzUWdjMmx0ZFd4aGRHVWdkR2hsSUdOaGJHd2dabTl5SUdsMExseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1kyRnNiRWRsZEUxdlpIVnNaU2hiYlc5a2RXeGxUbUZ0WlN3Z0tITm9hVzB1WkdWd2N5QjhmQ0JiWFNrc0lITm9hVzB1Wlhod2IzSjBjMFp1WFNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCamFHVmphMHh2WVdSbFpDZ3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ2ZTeGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0x5b3FYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0tpQkRiMjUyWlhKMGN5QmhJRzF2WkhWc1pTQnVZVzFsSUhSdklHRWdabWxzWlNCd1lYUm9MbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDcGNiaUFnSUNBZ0lDQWdJQ0FnSUNBcUlGTjFjSEJ2Y25SeklHTmhjMlZ6SUhkb1pYSmxJRzF2WkhWc1pVNWhiV1VnYldGNUlHRmpkSFZoYkd4NUlHSmxJR3AxYzNRZ1lXNGdWVkpNTGx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ3BjYmlBZ0lDQWdJQ0FnSUNBZ0lDQXFJRTV2ZEdVZ2RHaGhkQ0JwZENBcUttUnZaWE1nYm05MEtpb2dZMkZzYkNCdWIzSnRZV3hwZW1VZ2IyNGdkR2hsSUcxdlpIVnNaVTVoYldVc0lHbDBJR2x6SUdGemMzVnRaV1FnZEc4Z2FHRjJaU0JoYkhKbFlXUjVJR0psWlc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FxSUc1dmNtMWhiR2w2WldRdUlGUm9hWE1nYVhNZ1lXNGdhVzUwWlhKdVlXd2dRVkJKTENCdWIzUWdZU0J3ZFdKc2FXTWdiMjVsTGlCVmMyVWdkRzlWY213Z1ptOXlJSFJvWlNCd2RXSnNhV01nUVZCSkxseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNvdlhHNGdJQ0FnSUNBZ0lDQWdJQ0J1WVcxbFZHOVZjbXc2SUdaMWJtTjBhVzl1SUNodGIyUjFiR1ZPWVcxbExDQmxlSFFzSUhOcmFYQkZlSFFwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCc1pYUWdjR0YwYUhNN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2JHVjBJSE41YlhNN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2JHVjBJR2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYkdWMElIQmhjbVZ1ZEUxdlpIVnNaVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JzWlhRZ2RYSnNPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR3hsZENCd1lYSmxiblJRWVhSb0xDQmlkVzVrYkdWSlpEdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnNaWFFnY0d0blRXRnBiaUE5SUdkbGRFOTNiaWhqYjI1bWFXY3VjR3RuY3l3Z2JXOWtkV3hsVG1GdFpTazdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb2NHdG5UV0ZwYmlrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J0YjJSMWJHVk9ZVzFsSUQwZ2NHdG5UV0ZwYmp0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmlkVzVrYkdWSlpDQTlJR2RsZEU5M2JpaGlkVzVrYkdWelRXRndMQ0J0YjJSMWJHVk9ZVzFsS1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2hpZFc1a2JHVkpaQ2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnlaWFIxY200Z1kyOXVkR1Y0ZEM1dVlXMWxWRzlWY213b1luVnVaR3hsU1dRc0lHVjRkQ3dnYzJ0cGNFVjRkQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdTV1lnWVNCamIyeHZiaUJwY3lCcGJpQjBhR1VnVlZKTUxDQnBkQ0JwYm1ScFkyRjBaWE1nWVNCd2NtOTBiMk52YkNCcGN5QjFjMlZrSUdGdVpDQnBkQ0JwY3lCcWRYTjBJR0Z1SUZWU1RDQjBieUJoSUdacGJHVXNJRzl5SUdsbUlHbDBJRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUhOMFlYSjBjeUIzYVhSb0lHRWdjMnhoYzJnc0lHTnZiblJoYVc1eklHRWdjWFZsY25rZ1lYSm5JQ2hwTG1VdUlEOHBJRzl5SUdWdVpITWdkMmwwYUNBdWFuTXNJSFJvWlc0Z1lYTnpkVzFsSUhSb1pTQjFjMlZ5SUcxbFlXNTBJSFJ2SUZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklIVnpaU0JoYmlCMWNtd2dZVzVrSUc1dmRDQmhJRzF2WkhWc1pTQnBaQzRnVkdobElITnNZWE5vSUdseklHbHRjRzl5ZEdGdWRDQm1iM0lnY0hKdmRHOWpiMnd0YkdWemN5QlZVa3h6SUdGeklIZGxiR3dnWVhNZ1puVnNiQ0J3WVhSb2N5NWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaUFvY21WeExtcHpSWGgwVW1WblJYaHdMblJsYzNRb2JXOWtkV3hsVG1GdFpTa3BJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdTblZ6ZENCaElIQnNZV2x1SUhCaGRHZ3NJRzV2ZENCdGIyUjFiR1VnYm1GdFpTQnNiMjlyZFhBc0lITnZJR3AxYzNRZ2NtVjBkWEp1SUdsMExpQkJaR1FnWlhoMFpXNXphVzl1SUdsbUlHbDBJR2x6SUdsdVkyeDFaR1ZrTGlCY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnVkdocGN5QnBjeUJoSUdKcGRDQjNiMjVyZVN3Z2IyNXNlU0J1YjI0dExtcHpJSFJvYVc1bmN5QndZWE56SUdGdUlHVjRkR1Z1YzJsdmJpd2dkR2hwY3lCdFpYUm9iMlFnY0hKdlltRmliSGtnYm1WbFpITWdkRzhnWW1VZ1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUhKbGQyOXlhMlZrTGx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjFjbXdnUFNCdGIyUjFiR1ZPWVcxbElDc2dLR1Y0ZENCOGZDQW5KeWs3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlNCbGJITmxJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdRU0J0YjJSMWJHVWdkR2hoZENCdVpXVmtjeUIwYnlCaVpTQmpiMjUyWlhKMFpXUWdkRzhnWVNCd1lYUm9MbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCd1lYUm9jeUE5SUdOdmJtWnBaeTV3WVhSb2N6dGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCemVXMXpJRDBnYlc5a2RXeGxUbUZ0WlM1emNHeHBkQ2duTHljcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCR2IzSWdaV0ZqYUNCdGIyUjFiR1VnYm1GdFpTQnpaV2R0Wlc1MExDQnpaV1VnYVdZZ2RHaGxjbVVnYVhNZ1lTQndZWFJvSUhKbFoybHpkR1Z5WldRZ1ptOXlJR2wwTGlCVGRHRnlkQ0IzYVhSb0lHMXZjM1FnYzNCbFkybG1hV01nWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJRzVoYldVZ1lXNWtJSGR2Y21zZ2RYQWdabkp2YlNCcGRDNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWm05eUlDaHBJRDBnYzNsdGN5NXNaVzVuZEdnN0lHa2dQaUF3T3lCcElDMDlJREVwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhCaGNtVnVkRTF2WkhWc1pTQTlJSE41YlhNdWMyeHBZMlVvTUN3Z2FTa3VhbTlwYmlnbkx5Y3BPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J3WVhKbGJuUlFZWFJvSUQwZ1oyVjBUM2R1S0hCaGRHaHpMQ0J3WVhKbGJuUk5iMlIxYkdVcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWWdLSEJoY21WdWRGQmhkR2dwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUJKWmlCaGJpQmhjbkpoZVN3Z2FYUWdiV1ZoYm5NZ2RHaGxjbVVnWVhKbElHRWdabVYzSUdOb2IybGpaWE1zSUVOb2IyOXpaU0IwYUdVZ2IyNWxJSFJvWVhRZ2FYTWdaR1Z6YVhKbFpDNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb2FYTkJjbkpoZVNod1lYSmxiblJRWVhSb0tTa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQndZWEpsYm5SUVlYUm9JRDBnY0dGeVpXNTBVR0YwYUZzd1hUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2MzbHRjeTV6Y0d4cFkyVW9NQ3dnYVN3Z2NHRnlaVzUwVUdGMGFDazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWW5KbFlXczdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCS2IybHVJSFJvWlNCd1lYUm9JSEJoY25SeklIUnZaMlYwYUdWeUxDQjBhR1Z1SUdacFozVnlaU0J2ZFhRZ2FXWWdZbUZ6WlZWeWJDQnBjeUJ1WldWa1pXUXVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhWeWJDQTlJSE41YlhNdWFtOXBiaWduTHljcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IxY213Z0t6MGdLR1Y0ZENCOGZDQW9MMTVrWVhSaFhGdzZmRnhjUHk4dWRHVnpkQ2gxY213cElIeDhJSE5yYVhCRmVIUWdQeUFuSnlBNklDY3Vhbk1uS1NrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFZ5YkNBOUlDaDFjbXd1WTJoaGNrRjBLREFwSUQwOVBTQW5MeWNnZkh3Z2RYSnNMbTFoZEdOb0tDOWVXMXhjZDF4Y0sxeGNMbHhjTFYwck9pOHBJRDhnSnljZ09pQmpiMjVtYVdjdVltRnpaVlZ5YkNrZ0t5QjFjbXc3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2NtVjBkWEp1SUdOdmJtWnBaeTUxY214QmNtZHpJRDhnZFhKc0lDdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnS0NoMWNtd3VhVzVrWlhoUFppZ25QeWNwSUQwOVBTQXRNU0EvSUNjL0p5QTZJQ2NtSnlrZ0sxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1kyOXVabWxuTG5WeWJFRnlaM01wSURvZ2RYSnNPMXh1SUNBZ0lDQWdJQ0FnSUNBZ2ZTeGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0x5OGdSR1ZzWldkaGRHVnpJSFJ2SUhKbGNTNXNiMkZrTGlCQ2NtOXJaVzRnYjNWMElHRnpJR0VnYzJWd1lYSmhkR1VnWm5WdVkzUnBiMjRnZEc4Z1lXeHNiM2NnYjNabGNuSnBaR2x1WnlCcGJpQjBhR1VnYjNCMGFXMXBlbVZ5TGx4dUlDQWdJQ0FnSUNBZ0lDQWdiRzloWkRvZ1puVnVZM1JwYjI0Z0tHbGtMQ0IxY213cElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnlaWEV1Ykc5aFpDaGpiMjUwWlhoMExDQnBaQ3dnZFhKc0tUdGNiaUFnSUNBZ0lDQWdJQ0FnSUgwc1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUM4cUtseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNvZ1JYaGxZM1YwWlhNZ1lTQnRiMlIxYkdVZ1kyRnNiR0poWTJzZ1puVnVZM1JwYjI0dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnS2x4dUlDQWdJQ0FnSUNBZ0lDQWdJQ29nUW5KdmEyVnVJRzkxZENCaGN5QmhJSE5sY0dGeVlYUmxJR1oxYm1OMGFXOXVJSE52YkdWc2VTQjBieUJoYkd4dmR5QjBhR1VnWW5WcGJHUWdjM2x6ZEdWdElIUnZJSE5sY1hWbGJtTmxJSFJvWlNCbWFXeGxjeUJwYmlCMGFHVWdZblZwYkhSY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FxSUd4aGVXVnlJR2x1SUhSb1pTQnlhV2RvZENCelpYRjFaVzVqWlM1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FxWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdLaUJBY0hKcGRtRjBaVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDb3ZYRzRnSUNBZ0lDQWdJQ0FnSUNCbGVHVmpRMkk2SUdaMWJtTjBhVzl1SUNodVlXMWxMQ0JqWVd4c1ltRmpheXdnWVhKbmN5d2daWGh3YjNKMGN5a2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSEpsZEhWeWJpQmpZV3hzWW1GamF5NWhjSEJzZVNobGVIQnZjblJ6TENCaGNtZHpLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lIMHNYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDOHFLbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDb2dRMkZzYkdKaFkyc2dabTl5SUhOamNtbHdkQ0JzYjJGa2N5d2dkWE5sWkNCMGJ5QmphR1ZqYXlCemRHRjBkWE1nYjJZZ2JHOWhaR2x1Wnk1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FxWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdLaUJBY0dGeVlXMGdlMFYyWlc1MGZTQmxkblFnZEdobElHVjJaVzUwSUdaeWIyMGdkR2hsSUdKeWIzZHpaWElnWm05eUlIUm9aU0J6WTNKcGNIUWdkR2hoZENCM1lYTWdiRzloWkdWa0xseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNvdlhHNGdJQ0FnSUNBZ0lDQWdJQ0J2YmxOamNtbHdkRXh2WVdRNklHWjFibU4wYVc5dUlDaGxkblFwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QlZjMmx1WnlCamRYSnlaVzUwVkdGeVoyVjBJR2x1YzNSbFlXUWdiMllnZEdGeVoyVjBJR1p2Y2lCR2FYSmxabTk0SURJdU1DZHpJSE5oYTJVdUlFNXZkRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUdGc2JDQnZiR1FnWW5KdmQzTmxjbk1nZDJsc2JDQmlaU0J6ZFhCd2IzSjBaV1FzSUdKMWRDQjBhR2x6SUc5dVpTQjNZWE1nWldGemVTQmxibTkxWjJoY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QjBieUJ6ZFhCd2IzSjBJR0Z1WkNCemRHbHNiQ0J0WVd0bGN5QnpaVzV6WlM1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9aWFowTG5SNWNHVWdQVDA5SUNkc2IyRmtKeUI4ZkZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQW9jbVZoWkhsU1pXZEZlSEF1ZEdWemRDZ29aWFowTG1OMWNuSmxiblJVWVhKblpYUWdmSHdnWlhaMExuTnlZMFZzWlcxbGJuUXBMbkpsWVdSNVUzUmhkR1VwS1NrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCU1pYTmxkQ0JwYm5SbGNtRmpkR2wyWlNCelkzSnBjSFFnYzI4Z1lTQnpZM0pwY0hRZ2JtOWtaU0JwY3lCdWIzUWdhR1ZzWkNCdmJuUnZJR1p2Y2x4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMeUIwYnlCc2IyNW5MbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcGJuUmxjbUZqZEdsMlpWTmpjbWx3ZENBOUlHNTFiR3c3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnVUhWc2JDQnZkWFFnZEdobElHNWhiV1VnYjJZZ2RHaGxJRzF2WkhWc1pTQmhibVFnZEdobElHTnZiblJsZUhRdVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR3hsZENCa1lYUmhJRDBnWjJWMFUyTnlhWEIwUkdGMFlTaGxkblFwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmpiMjUwWlhoMExtTnZiWEJzWlhSbFRHOWhaQ2hrWVhSaExtbGtLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlMRnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQXZLaXBjYmlBZ0lDQWdJQ0FnSUNBZ0lDQXFJRU5oYkd4aVlXTnJJR1p2Y2lCelkzSnBjSFFnWlhKeWIzSnpMbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDb3ZYRzRnSUNBZ0lDQWdJQ0FnSUNCdmJsTmpjbWx3ZEVWeWNtOXlPaUJtZFc1amRHbHZiaUFvWlhaMEtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdiR1YwSUdSaGRHRWdQU0JuWlhSVFkzSnBjSFJFWVhSaEtHVjJkQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYVdZZ0tDRm9ZWE5RWVhSb1JtRnNiR0poWTJzb1pHRjBZUzVwWkNrcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYkdWMElIQmhjbVZ1ZEhNZ1BTQmJYVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1pXRmphRkJ5YjNBb2NtVm5hWE4wY25rc0lHWjFibU4wYVc5dUlDaDJZV3gxWlN3Z2EyVjVLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb2EyVjVMbWx1WkdWNFQyWW9KMTlBY2ljcElDRTlQU0F3S1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdaV0ZqYUNoMllXeDFaUzVrWlhCTllYQnpMQ0JtZFc1amRHbHZiaUFvWkdWd1RXRndLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtSUNoa1pYQk5ZWEF1YVdRZ1BUMDlJR1JoZEdFdWFXUXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhCaGNtVnVkSE11Y0hWemFDaHJaWGtwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSEpsZEhWeWJpQjBjblZsTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMHBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOUtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnY21WMGRYSnVJRzl1UlhKeWIzSW9iV0ZyWlVWeWNtOXlLQ2R6WTNKcGNIUmxjbkp2Y2ljc0lDZFRZM0pwY0hRZ1pYSnliM0lnWm05eUlGd2lKeUFySUdSaGRHRXVhV1FnSzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnS0hCaGNtVnVkSE11YkdWdVozUm9JRDljYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBblhDSXNJRzVsWldSbFpDQmllVG9nSnlBcklIQmhjbVZ1ZEhNdWFtOXBiaWduTENBbktTQTZYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSjF3aUp5a3NJR1YyZEN3Z1cyUmhkR0V1YVdSZEtTazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQjlPMXh1WEc0Z0lDQWdJQ0FnSUdOdmJuUmxlSFF1Y21WeGRXbHlaU0E5SUdOdmJuUmxlSFF1YldGclpWSmxjWFZwY21Vb0tUdGNiaUFnSUNBZ0lDQWdjbVYwZFhKdUlHTnZiblJsZUhRN1hHNGdJQ0FnZlZ4dVhHNGdJQ0FnTHlvcVhHNGdJQ0FnSUNvZ1RXRnBiaUJsYm5SeWVTQndiMmx1ZEM1Y2JpQWdJQ0FnS2x4dUlDQWdJQ0FxSUVsbUlIUm9aU0J2Ym14NUlHRnlaM1Z0Wlc1MElIUnZJSEpsY1hWcGNtVWdhWE1nWVNCemRISnBibWNzSUhSb1pXNGdkR2hsSUcxdlpIVnNaU0IwYUdGMElHbHpJSEpsY0hKbGMyVnVkR1ZrSUdKNUlIUm9ZWFFnYzNSeWFXNW5JR2x6SUdabGRHTm9aV1FnWm05eVhHNGdJQ0FnSUNvZ2RHaGxJR0Z3Y0hKdmNISnBZWFJsSUdOdmJuUmxlSFF1WEc0Z0lDQWdJQ3BjYmlBZ0lDQWdLaUJKWmlCMGFHVWdabWx5YzNRZ1lYSm5kVzFsYm5RZ2FYTWdZVzRnWVhKeVlYa3NJSFJvWlc0Z2FYUWdkMmxzYkNCaVpTQjBjbVZoZEdWa0lHRnpJR0Z1SUdGeWNtRjVJRzltSUdSbGNHVnVaR1Z1WTNrZ2MzUnlhVzVuSUc1aGJXVnpJSFJ2SUdabGRHTm9MaUJCYmx4dUlDQWdJQ0FxSUc5d2RHbHZibUZzSUdaMWJtTjBhVzl1SUdOaGJHeGlZV05ySUdOaGJpQmlaU0J6Y0dWamFXWnBaV1FnZEc4Z1pYaGxZM1YwWlNCM2FHVnVJR0ZzYkNCdlppQjBhRzl6WlNCa1pYQmxibVJsYm1OcFpYTWdZWEpsSUdGMllXbHNZV0pzWlM1Y2JpQWdJQ0FnS2x4dUlDQWdJQ0FxSUUxaGEyVWdZU0JzYjJOaGJDQnlaWEVnZG1GeWFXRmliR1VnZEc4Z2FHVnNjQ0JEWVdwaElHTnZiWEJzYVdGdVkyVWdLR2wwSUdGemMzVnRaWE1nZEdocGJtZHpJRzl1SUdFZ2NtVnhkV2x5WlNCMGFHRjBJR0Z5WlNCdWIzUWdjM1JoYm1SaGNtUnBlbVZrS1N4Y2JpQWdJQ0FnS2lCaGJtUWdkRzhnWjJsMlpTQmhJSE5vYjNKMElHNWhiV1VnWm05eUlHMXBibWxtYVdOaGRHbHZiaTlzYjJOaGJDQnpZMjl3WlNCMWMyVXVYRzRnSUNBZ0lDb3ZYRzRnSUNBZ2NtVnhJRDBnZDJsdVpHOTNMbkpsY1hWcGNtVnFjeUE5SUdaMWJtTjBhVzl1SUNoa1pYQnpMQ0JqWVd4c1ltRmpheXdnWlhKeVltRmpheXdnYjNCMGFXOXVZV3dwSUh0Y2JpQWdJQ0FnSUNBZ0x5OGdSbWx1WkNCMGFHVWdjbWxuYUhRZ1kyOXVkR1Y0ZEN3Z2RYTmxJR1JsWm1GMWJIUmNiaUFnSUNBZ0lDQWdiR1YwSUdOdmJuUmxlSFE3WEc0Z0lDQWdJQ0FnSUd4bGRDQmpiMjVtYVdjN1hHNGdJQ0FnSUNBZ0lHeGxkQ0JqYjI1MFpYaDBUbUZ0WlNBOUlHUmxaa052Ym5SbGVIUk9ZVzFsTzF4dVhHNGdJQ0FnSUNBZ0lDOHZJRVJsZEdWeWJXbHVaU0JwWmlCb1lYWmxJR052Ym1acFp5QnZZbXBsWTNRZ2FXNGdkR2hsSUdOaGJHd3VYRzRnSUNBZ0lDQWdJR2xtSUNnaGFYTkJjbkpoZVNoa1pYQnpLU0FtSmlCMGVYQmxiMllnWkdWd2N5QWhQVDBnSjNOMGNtbHVaeWNwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQzh2SUdSbGNITWdhWE1nWVNCamIyNW1hV2NnYjJKcVpXTjBYRzRnSUNBZ0lDQWdJQ0FnSUNCamIyNW1hV2NnUFNCa1pYQnpPMXh1SUNBZ0lDQWdJQ0FnSUNBZ2FXWWdLR2x6UVhKeVlYa29ZMkZzYkdKaFkyc3BLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1FXUnFkWE4wSUdGeVozTWdhV1lnZEdobGNtVWdZWEpsSUdSbGNHVnVaR1Z1WTJsbGMxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHUmxjSE1nUFNCallXeHNZbUZqYXp0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCallXeHNZbUZqYXlBOUlHVnljbUpoWTJzN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1pYSnlZbUZqYXlBOUlHOXdkR2x2Ym1Gc08xeHVJQ0FnSUNBZ0lDQWdJQ0FnZlNCbGJITmxJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JrWlhCeklEMGdXMTA3WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNCcFppQW9ZMjl1Wm1sbklDWW1JR052Ym1acFp5NWpiMjUwWlhoMEtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCamIyNTBaWGgwVG1GdFpTQTlJR052Ym1acFp5NWpiMjUwWlhoME8xeHVJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnWTI5dWRHVjRkQ0E5SUdkbGRFOTNiaWhqYjI1MFpYaDBjeXdnWTI5dWRHVjRkRTVoYldVcE8xeHVJQ0FnSUNBZ0lDQnBaaUFvSVdOdmJuUmxlSFFwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJR052Ym5SbGVIUWdQU0JqYjI1MFpYaDBjMXRqYjI1MFpYaDBUbUZ0WlYwZ1BTQnlaWEV1Y3k1dVpYZERiMjUwWlhoMEtHTnZiblJsZUhST1lXMWxLVHRjYmlBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lHbG1JQ2hqYjI1bWFXY3BJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lHTnZiblJsZUhRdVkyOXVabWxuZFhKbEtHTnZibVpwWnlrN1hHNGdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0J5WlhSMWNtNGdZMjl1ZEdWNGRDNXlaWEYxYVhKbEtHUmxjSE1zSUdOaGJHeGlZV05yTENCbGNuSmlZV05yS1R0Y2JpQWdJQ0I5TzF4dVhHNGdJQ0FnTHlvcVhHNGdJQ0FnSUNvZ1UzVndjRzl5ZENCeVpYRjFhWEpsTG1OdmJtWnBaeWdwSUhSdklHMWhhMlVnYVhRZ1pXRnphV1Z5SUhSdklHTnZiM0JsY21GMFpTQjNhWFJvSUc5MGFHVnlJRUZOUkNCc2IyRmtaWEp6SUc5dUlHZHNiMkpoYkd4NUlHRm5jbVZsWkNCdVlXMWxjeTVjYmlBZ0lDQWdLaTljYmlBZ0lDQnlaWEV1WTI5dVptbG5JRDBnWm5WdVkzUnBiMjRnS0dOdmJtWnBaeWtnZTF4dUlDQWdJQ0FnSUNCeVpYUjFjbTRnY21WeEtHTnZibVpwWnlrN1hHNGdJQ0FnZlR0Y2JseHVJQ0FnSUM4cUtseHVJQ0FnSUNBcUlFVjRaV04xZEdVZ2MyOXRaWFJvYVc1bklHRm1kR1Z5SUhSb1pTQmpkWEp5Wlc1MElIUnBZMnNnYjJZZ2RHaGxJR1YyWlc1MElHeHZiM0F1WEc0Z0lDQWdJQ3BjYmlBZ0lDQWdLaUJQZG1WeWNtbGtaU0JtYjNJZ2IzUm9aWElnWlc1MmN5QjBhR0YwSUdoaGRtVWdZU0JpWlhSMFpYSWdjMjlzZFhScGIyNGdkR2hoYmlCelpYUlVhVzFsYjNWMExseHVJQ0FnSUNBcVhHNGdJQ0FnSUNvZ1FIQmhjbUZ0SUNCN1JuVnVZM1JwYjI1OUlHWnVJR1oxYm1OMGFXOXVJSFJ2SUdWNFpXTjFkR1VnYkdGMFpYSXVYRzRnSUNBZ0lDb3ZYRzRnSUNBZ2NtVnhMbTVsZUhSVWFXTnJJRDBnZEhsd1pXOW1JSE5sZEZScGJXVnZkWFFnSVQwOUlDZDFibVJsWm1sdVpXUW5JRDhnWm5WdVkzUnBiMjRnS0dadUtTQjdYRzRnSUNBZ0lDQWdJSE5sZEZScGJXVnZkWFFvWm00c0lEUXBPMXh1SUNBZ0lIMGdPaUJtZFc1amRHbHZiaUFvWm00cElIdGNiaUFnSUNBZ0lDQWdabTRvS1R0Y2JpQWdJQ0I5TzF4dVhHNGdJQ0FnTHlvcVhHNGdJQ0FnSUNvZ1JYaHdiM0owSUhKbGNYVnBjbVVnWVhNZ1lTQm5iRzlpWVd3c0lHSjFkQ0J2Ym14NUlHbG1JR2wwSUdSdlpYTWdibTkwSUdGc2NtVmhaSGtnWlhocGMzUXVYRzRnSUNBZ0lDb3ZYRzRnSUNBZ2FXWWdLQ0YzYVc1a2IzY3VjbVZ4ZFdseVpTa2dlMXh1SUNBZ0lDQWdJQ0IzYVc1a2IzY3VjbVZ4ZFdseVpTQTlJSEpsY1R0Y2JpQWdJQ0I5WEc1Y2JpQWdJQ0J5WlhFdWRtVnljMmx2YmlBOUlIWmxjbk5wYjI0N1hHNWNiaUFnSUNBdkx5QlZjMlZrSUhSdklHWnBiSFJsY2lCdmRYUWdaR1Z3Wlc1a1pXNWphV1Z6SUhSb1lYUWdZWEpsSUdGc2NtVmhaSGtnY0dGMGFITXVYRzRnSUNBZ2NtVnhMbXB6UlhoMFVtVm5SWGh3SUQwZ0wxNWNYQzk4T254Y1hEOThYRnd1YW5Na0x6dGNiaUFnSUNCeVpYRXVhWE5DY205M2MyVnlJRDBnYVhOQ2NtOTNjMlZ5TzF4dUlDQWdJSE1nUFNCeVpYRXVjeUE5SUh0Y2JpQWdJQ0FnSUNBZ1kyOXVkR1Y0ZEhNNklHTnZiblJsZUhSekxGeHVJQ0FnSUNBZ0lDQnVaWGREYjI1MFpYaDBPaUJ1WlhkRGIyNTBaWGgwWEc0Z0lDQWdmVHRjYmx4dUlDQWdJQzh2SUVOeVpXRjBaU0JrWldaaGRXeDBJR052Ym5SbGVIUXVYRzRnSUNBZ2NtVnhLSHQ5S1R0Y2JseHVJQ0FnSUM4dklFVjRjRzl5ZEhNZ2MyOXRaU0JqYjI1MFpYaDBMWE5sYm5OcGRHbDJaU0J0WlhSb2IyUnpJRzl1SUdkc2IySmhiQ0J5WlhGMWFYSmxMbHh1SUNBZ0lHVmhZMmdvVzF4dUlDQWdJQ0FnSUNBbmRHOVZjbXduTEZ4dUlDQWdJQ0FnSUNBbmRXNWtaV1luTEZ4dUlDQWdJQ0FnSUNBblpHVm1hVzVsWkNjc1hHNGdJQ0FnSUNBZ0lDZHpjR1ZqYVdacFpXUW5YRzRnSUNBZ1hTd2dablZ1WTNScGIyNGdLSEJ5YjNBcElIdGNiaUFnSUNBZ0lDQWdMeThnSUZKbFptVnlaVzVqWlNCbWNtOXRJR052Ym5SbGVIUnpJR2x1YzNSbFlXUWdiMllnWldGeWJIa2dZbWx1WkdsdVp5QjBieUJrWldaaGRXeDBJR052Ym5SbGVIUXNYRzRnSUNBZ0lDQWdJQzh2SUhOdklIUm9ZWFFnWkhWeWFXNW5JR0oxYVd4a2N5d2dkR2hsSUd4aGRHVnpkQ0JwYm5OMFlXNWpaU0J2WmlCMGFHVWdaR1ZtWVhWc2RDQmpiMjUwWlhoMFhHNGdJQ0FnSUNBZ0lDOHZJSGRwZEdnZ2FYUnpJR052Ym1acFp5Qm5aWFJ6SUhWelpXUXVYRzRnSUNBZ0lDQWdJSEpsY1Z0d2NtOXdYU0E5SUdaMWJtTjBhVzl1SUNncElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUd4bGRDQmpkSGdnUFNCamIyNTBaWGgwYzF0a1pXWkRiMjUwWlhoMFRtRnRaVjA3WEc0Z0lDQWdJQ0FnSUNBZ0lDQnlaWFIxY200Z1kzUjRMbkpsY1hWcGNtVmJjSEp2Y0YwdVlYQndiSGtvWTNSNExDQmhjbWQxYldWdWRITXBPMXh1SUNBZ0lDQWdJQ0I5TzF4dUlDQWdJSDBwTzF4dVhHNGdJQ0FnYVdZZ0tHbHpRbkp2ZDNObGNpa2dlMXh1SUNBZ0lDQWdJQ0JvWldGa0lEMGdjeTVvWldGa0lEMGdaRzlqZFcxbGJuUXVaMlYwUld4bGJXVnVkSE5DZVZSaFowNWhiV1VvSjJobFlXUW5LVnN3WFR0Y2JpQWdJQ0FnSUNBZ0x5OGdTV1lnUWtGVFJTQjBZV2NnYVhNZ2FXNGdjR3hoZVN3Z2RYTnBibWNnWVhCd1pXNWtRMmhwYkdRZ2FYTWdZU0J3Y205aWJHVnRJR1p2Y2lCSlJUWXVYRzRnSUNBZ0lDQWdJQzh2SUZkb1pXNGdkR2hoZENCaWNtOTNjMlZ5SUdScFpYTXNJSFJvYVhNZ1kyRnVJR0psSUhKbGJXOTJaV1F1SUVSbGRHRnBiSE1nYVc0Z2RHaHBjeUJxVVhWbGNua2dZblZuT2x4dUlDQWdJQ0FnSUNBdkx5Qm9kSFJ3T2k4dlpHVjJMbXB4ZFdWeWVTNWpiMjB2ZEdsamEyVjBMekkzTURsY2JpQWdJQ0FnSUNBZ1ltRnpaVVZzWlcxbGJuUWdQU0JrYjJOMWJXVnVkQzVuWlhSRmJHVnRaVzUwYzBKNVZHRm5UbUZ0WlNnblltRnpaU2NwV3pCZE8xeHVJQ0FnSUNBZ0lDQnBaaUFvWW1GelpVVnNaVzFsYm5RcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUdobFlXUWdQU0J6TG1obFlXUWdQU0JpWVhObFJXeGxiV1Z1ZEM1d1lYSmxiblJPYjJSbE8xeHVJQ0FnSUNBZ0lDQjlYRzRnSUNBZ2ZWeHVYRzRnSUNBZ0x5b3FYRzRnSUNBZ0lDb2dRVzU1SUdWeWNtOXljeUIwYUdGMElISmxjWFZwY21VZ1pYaHdiR2xqYVhSc2VTQm5aVzVsY21GMFpYTWdkMmxzYkNCaVpTQndZWE56WldRZ2RHOGdkR2hwY3lCbWRXNWpkR2x2Ymk1Y2JpQWdJQ0FnS2x4dUlDQWdJQ0FxSUVsdWRHVnlZMlZ3ZEM5dmRtVnljbWxrWlNCcGRDQnBaaUI1YjNVZ2QyRnVkQ0JqZFhOMGIyMGdaWEp5YjNJZ2FHRnVaR3hwYm1jdVhHNGdJQ0FnSUNwY2JpQWdJQ0FnS2lCQWNHRnlZVzBnZTBWeWNtOXlmU0JsY25JZ2RHaGxJR1Z5Y205eUlHOWlhbVZqZEM1Y2JpQWdJQ0FnS2k5Y2JpQWdJQ0J5WlhFdWIyNUZjbkp2Y2lBOUlHUmxabUYxYkhSUGJrVnljbTl5TzF4dVhHNGdJQ0FnTHlvcVhHNGdJQ0FnSUNvZ1EzSmxZWFJsY3lCMGFHVWdibTlrWlNCbWIzSWdkR2hsSUd4dllXUWdZMjl0YldGdVpDNGdUMjVzZVNCMWMyVmtJR2x1SUdKeWIzZHpaWElnWlc1MmN5NWNiaUFnSUNBZ0tpOWNiaUFnSUNCeVpYRXVZM0psWVhSbFRtOWtaU0E5SUdaMWJtTjBhVzl1SUNoamIyNW1hV2NzSUcxdlpIVnNaVTVoYldVc0lIVnliQ2tnZTF4dUlDQWdJQ0FnSUNCamIyNXpkQ0J1YjJSbElEMGdZMjl1Wm1sbkxuaG9kRzFzSUQ5Y2JpQWdJQ0FnSUNBZ0lDQWdJR1J2WTNWdFpXNTBMbU55WldGMFpVVnNaVzFsYm5ST1V5Z25hSFIwY0RvdkwzZDNkeTUzTXk1dmNtY3ZNVGs1T1M5NGFIUnRiQ2NzSUNkb2RHMXNPbk5qY21sd2RDY3BJRHBjYmlBZ0lDQWdJQ0FnSUNBZ0lHUnZZM1Z0Wlc1MExtTnlaV0YwWlVWc1pXMWxiblFvSjNOamNtbHdkQ2NwTzF4dUlDQWdJQ0FnSUNCdWIyUmxMblI1Y0dVZ1BTQmpiMjVtYVdjdWMyTnlhWEIwVkhsd1pTQjhmQ0FuZEdWNGRDOXFZWFpoYzJOeWFYQjBKenRjYmlBZ0lDQWdJQ0FnYm05a1pTNWphR0Z5YzJWMElEMGdKM1YwWmkwNEp6dGNiaUFnSUNBZ0lDQWdibTlrWlM1aGMzbHVZeUE5SUhSeWRXVTdYRzRnSUNBZ0lDQWdJSEpsZEhWeWJpQnViMlJsTzF4dUlDQWdJSDA3WEc1Y2JpQWdJQ0F2S2lwY2JpQWdJQ0FnS2lCRWIyVnpJSFJvWlNCeVpYRjFaWE4wSUhSdklHeHZZV1FnWVNCdGIyUjFiR1VnWm05eUlIUm9aU0JpY205M2MyVnlJR05oYzJVdVhHNGdJQ0FnSUNwY2JpQWdJQ0FnS2lCTllXdGxJSFJvYVhNZ1lTQnpaWEJoY21GMFpTQm1kVzVqZEdsdmJpQjBieUJoYkd4dmR5QnZkR2hsY2lCbGJuWnBjbTl1YldWdWRITWdkRzhnYjNabGNuSnBaR1VnYVhRdVhHNGdJQ0FnSUNwY2JpQWdJQ0FnS2lCQWNHRnlZVzBnZTA5aWFtVmpkSDBnWTI5dWRHVjRkQ0IwYUdVZ2NtVnhkV2x5WlNCamIyNTBaWGgwSUhSdklHWnBibVFnYzNSaGRHVXVYRzRnSUNBZ0lDb2dRSEJoY21GdElIdFRkSEpwYm1kOUlHMXZaSFZzWlU1aGJXVWdkR2hsSUc1aGJXVWdiMllnZEdobElHMXZaSFZzWlM1Y2JpQWdJQ0FnS2lCQWNHRnlZVzBnZTA5aWFtVmpkSDBnZFhKc0lIUm9aU0JWVWt3Z2RHOGdkR2hsSUcxdlpIVnNaUzVjYmlBZ0lDQWdLaTljYmlBZ0lDQnlaWEV1Ykc5aFpDQTlJR1oxYm1OMGFXOXVJQ2hqYjI1MFpYaDBMQ0J0YjJSMWJHVk9ZVzFsTENCMWNtd3BJSHRjYmlBZ0lDQWdJQ0FnYkdWMElHTnZibVpwWnlBOUlDaGpiMjUwWlhoMElDWW1JR052Ym5SbGVIUXVZMjl1Wm1sbktTQjhmQ0I3ZlR0Y2JpQWdJQ0FnSUNBZ2JHVjBJRzV2WkdVN1hHNWNiaUFnSUNBZ0lDQWdhV1lnS0dselFuSnZkM05sY2lrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1NXNGdkR2hsSUdKeWIzZHpaWElnYzI4Z2RYTmxJR0VnYzJOeWFYQjBJSFJoWjF4dUlDQWdJQ0FnSUNBZ0lDQWdibTlrWlNBOUlISmxjUzVqY21WaGRHVk9iMlJsS0dOdmJtWnBaeXdnYlc5a2RXeGxUbUZ0WlN3Z2RYSnNLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2hqYjI1bWFXY3ViMjVPYjJSbFEzSmxZWFJsWkNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHTnZibVpwWnk1dmJrNXZaR1ZEY21WaGRHVmtLRzV2WkdVc0lHTnZibVpwWnl3Z2JXOWtkV3hsVG1GdFpTd2dkWEpzS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdibTlrWlM1elpYUkJkSFJ5YVdKMWRHVW9KMlJoZEdFdGNtVnhkV2x5WldOdmJuUmxlSFFuTENCamIyNTBaWGgwTG1OdmJuUmxlSFJPWVcxbEtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUc1dlpHVXVjMlYwUVhSMGNtbGlkWFJsS0Nka1lYUmhMWEpsY1hWcGNtVnRiMlIxYkdVbkxDQnRiMlIxYkdWT1lXMWxLVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdMeThnVTJWMElIVndJR3h2WVdRZ2JHbHpkR1Z1WlhJdUlGUmxjM1FnWVhSMFlXTm9SWFpsYm5RZ1ptbHljM1FnWW1WallYVnpaU0JKUlRrZ2FHRnpJR0VnYzNWaWRHeGxJR2x6YzNWbElHbHVJR2wwY3lCaFpHUkZkbVZ1ZEV4cGMzUmxibVZ5SUdGdVpDQmNiaUFnSUNBZ0lDQWdJQ0FnSUM4dklITmpjbWx3ZENCdmJteHZZV1FnWm1seWFXNW5jeUIwYUdGMElHUnZJRzV2ZENCdFlYUmphQ0IwYUdVZ1ltVm9ZWFpwYjNJZ2IyWWdZV3hzSUc5MGFHVnlJR0p5YjNkelpYSnpJSGRwZEdnZ1lXUmtSWFpsYm5STWFYTjBaVzVsY2lCemRYQndiM0owTENCY2JpQWdJQ0FnSUNBZ0lDQWdJQzh2SUhkb2FXTm9JR1pwY21VZ2RHaGxJRzl1Ykc5aFpDQmxkbVZ1ZENCbWIzSWdZU0J6WTNKcGNIUWdjbWxuYUhRZ1lXWjBaWElnZEdobElITmpjbWx3ZENCbGVHVmpkWFJwYjI0dUlGTmxaVHBjYmlBZ0lDQWdJQ0FnSUNBZ0lDOHZJR2gwZEhCek9pOHZZMjl1Ym1WamRDNXRhV055YjNOdlpuUXVZMjl0TDBsRkwyWmxaV1JpWVdOckwyUmxkR0ZwYkhNdk5qUTRNRFUzTDNOamNtbHdkQzF2Ym14dllXUXRaWFpsYm5RdGFYTXRibTkwTFdacGNtVmtMV2x0YldWa2FXRjBaV3g1TFdGbWRHVnlMWE5qY21sd2RDMWxlR1ZqZFhScGIyNWNiaUFnSUNBZ0lDQWdJQ0FnSUM4dklGVk9SazlTVkZWT1FWUkZURmtnVDNCbGNtRWdhVzF3YkdWdFpXNTBjeUJoZEhSaFkyaEZkbVZ1ZENCaWRYUWdaRzlsY3lCdWIzUWdabTlzYkc5M0lIUm9aU0J6WTNKcGNIUWdjMk55YVhCMElHVjRaV04xZEdsdmJpQnRiMlJsTGx4dUlDQWdJQ0FnSUNBZ0lDQWdhV1lnS0c1dlpHVXVZWFIwWVdOb1JYWmxiblFnSmlaY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QkRhR1ZqYXlCcFppQnViMlJsTG1GMGRHRmphRVYyWlc1MElHbHpJR0Z5ZEdsbWFXTnBZV3hzZVNCaFpHUmxaQ0JpZVNCamRYTjBiMjBnYzJOeWFYQjBJRzl5SUc1aGRHbDJaV3g1SUhOMWNIQnZjblJsWkNCaWVTQmljbTkzYzJWeVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdjbVZoWkNCb2RIUndjem92TDJkcGRHaDFZaTVqYjIwdmFuSmlkWEpyWlM5eVpYRjFhWEpsYW5NdmFYTnpkV1Z6THpFNE4xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJR2xtSUhkbElHTmhiaUJPVDFRZ1ptbHVaQ0JiYm1GMGFYWmxJR052WkdWZElIUm9aVzRnYVhRZ2JYVnpkQ0JPVDFRZ2JtRjBhWFpsYkhrZ2MzVndjRzl5ZEdWa0xpQnBiaUJKUlRnc0lHNXZaR1V1WVhSMFlXTm9SWFpsYm5RZ1pHOWxjeUJjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCdWIzUWdhR0YyWlNCMGIxTjBjbWx1WnlncExpQk9iM1JsSUhSb1pTQjBaWE4wSUdadmNpQmNJbHR1WVhScGRtVWdZMjlrWlZ3aUlIZHBkR2dnYm04Z1kyeHZjMmx1WnlCaWNtRmpaU3dnYzJWbE9seHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJR2gwZEhCek9pOHZaMmwwYUhWaUxtTnZiUzlxY21KMWNtdGxMM0psY1hWcGNtVnFjeTlwYzNOMVpYTXZNamN6WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSVNodWIyUmxMbUYwZEdGamFFVjJaVzUwTG5SdlUzUnlhVzVuSUNZbUlHNXZaR1V1WVhSMFlXTm9SWFpsYm5RdWRHOVRkSEpwYm1jb0tTNXBibVJsZUU5bUtDZGJibUYwYVhabElHTnZaR1VuS1NBOElEQXBJQ1ltWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSVdselQzQmxjbUVwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QlFjbTlpWVdKc2VTQkpSUzRnU1VVZ0tHRjBJR3hsWVhOMElEWXRPQ2tnWkc4Z2JtOTBJR1pwY21VZ2MyTnlhWEIwSUc5dWJHOWhaQ0J5YVdkb2RDQmhablJsY2lCbGVHVmpkWFJwYm1jZ2RHaGxJSE5qY21sd2RDd2djMjljYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCM1pTQmpZVzV1YjNRZ2RHbGxJSFJvWlNCaGJtOXVlVzF2ZFhNZ1pHVm1hVzVsSUdOaGJHd2dkRzhnWVNCdVlXMWxMaUJJYjNkbGRtVnlMQ0JKUlNCeVpYQnZjblJ6SUhSb1pTQnpZM0pwY0hRZ1lYTWdZbVZwYm1jZ2FXNGdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnSjJsdWRHVnlZV04wYVhabEp5QnlaV0ZrZVZOMFlYUmxJR0YwSUhSb1pTQjBhVzFsSUc5bUlIUm9aU0JrWldacGJtVWdZMkZzYkM1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCMWMyVkpiblJsY21GamRHbDJaU0E5SUhSeWRXVTdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J1YjJSbExtRjBkR0ZqYUVWMlpXNTBLQ2R2Ym5KbFlXUjVjM1JoZEdWamFHRnVaMlVuTENCamIyNTBaWGgwTG05dVUyTnlhWEIwVEc5aFpDazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnU1hRZ2QyOTFiR1FnWW1VZ1ozSmxZWFFnZEc4Z1lXUmtJR0Z1SUdWeWNtOXlJR2hoYm1Sc1pYSWdhR1Z5WlNCMGJ5QmpZWFJqYUNBME1EUnpJR2x1SUVsRk9Tc3VJRWh2ZDJWMlpYSXNJRzl1Y21WaFpIbHpkR0YwWldOb1lXNW5aU0JjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCM2FXeHNJR1pwY21VZ1ltVm1iM0psSUhSb1pTQmxjbkp2Y2lCb1lXNWtiR1Z5TENCemJ5QjBhR0YwSUdSdlpYTWdibTkwSUdobGJIQXVJRWxtSUdGa1pFVjJaVzUwVEdsemRHVnVaWElnYVhNZ2RYTmxaQ3dnZEdobGJpQkpSU0IzYVd4c0lGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJR1pwY21VZ1pYSnliM0lnWW1WbWIzSmxJR3h2WVdRc0lHSjFkQ0IzWlNCallXNXViM1FnZFhObElIUm9ZWFFnY0dGMGFIZGhlU0JuYVhabGJpQjBhR1VnWTI5dWJtVmpkQzV0YVdOeWIzTnZablF1WTI5dElHbHpjM1ZsSUZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklHMWxiblJwYjI1bFpDQmhZbTkyWlNCaFltOTFkQ0J1YjNRZ1pHOXBibWNnZEdobElDZHpZM0pwY0hRZ1pYaGxZM1YwWlN3Z2RHaGxiaUJtYVhKbElIUm9aU0J6WTNKcGNIUWdiRzloWkNCbGRtVnVkQ0JzYVhOMFpXNWxjaUJpWldadmNtVWdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnWlhobFkzVjBaU0J1WlhoMElITmpjbWx3ZENjZ2RHaGhkQ0J2ZEdobGNpQmljbTkzYzJWeWN5QmtieTRnUW1WemRDQm9iM0JsT2lCSlJURXdJR1pwZUdWeklIUm9aU0JwYzNOMVpYTXNJR0Z1WkNCMGFHVnVJR1JsYzNSeWIzbHpJR0ZzYkNCY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QnBibk4wWVd4c2N5QnZaaUJKUlNBMkxUa3VYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnYm05a1pTNWhkSFJoWTJoRmRtVnVkQ2duYjI1bGNuSnZjaWNzSUdOdmJuUmxlSFF1YjI1VFkzSnBjSFJGY25KdmNpazdYRzRnSUNBZ0lDQWdJQ0FnSUNCOUlHVnNjMlVnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUc1dlpHVXVZV1JrUlhabGJuUk1hWE4wWlc1bGNpZ25iRzloWkNjc0lHTnZiblJsZUhRdWIyNVRZM0pwY0hSTWIyRmtMQ0JtWVd4elpTazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdibTlrWlM1aFpHUkZkbVZ1ZEV4cGMzUmxibVZ5S0NkbGNuSnZjaWNzSUdOdmJuUmxlSFF1YjI1VFkzSnBjSFJGY25KdmNpd2dabUZzYzJVcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdibTlrWlM1emNtTWdQU0IxY213N1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUM4dklFWnZjaUJ6YjIxbElHTmhZMmhsSUdOaGMyVnpJR2x1SUVsRklEWXRPQ3dnZEdobElITmpjbWx3ZENCbGVHVmpkWFJsY3lCaVpXWnZjbVVnZEdobElHVnVaQ0J2WmlCMGFHVWdZWEJ3Wlc1a1EyaHBiR1FnWlhobFkzVjBhVzl1TENCemJ5QjBieUJjYmlBZ0lDQWdJQ0FnSUNBZ0lDOHZJSFJwWlNCaGJpQmhibTl1ZVcxdmRYTWdaR1ZtYVc1bElHTmhiR3dnZEc4Z2RHaGxJRzF2WkhWc1pTQnVZVzFsSUNoM2FHbGphQ0JwY3lCemRHOXlaV1FnYjI0Z2RHaGxJRzV2WkdVcExDQm9iMnhrSUc5dUlIUnZJR0VnY21WbVpYSmxibU5sSUhSdklGeHVJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z2RHaHBjeUJ1YjJSbExDQmlkWFFnWTJ4bFlYSWdZV1owWlhJZ2RHaGxJRVJQVFNCcGJuTmxjblJwYjI0dVhHNGdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb1ltRnpaVVZzWlcxbGJuUXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JvWldGa0xtbHVjMlZ5ZEVKbFptOXlaU2h1YjJSbExDQmlZWE5sUld4bGJXVnVkQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlJR1ZzYzJVZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHaGxZV1F1WVhCd1pXNWtRMmhwYkdRb2JtOWtaU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lISmxkSFZ5YmlCdWIyUmxPMXh1SUNBZ0lDQWdJQ0I5SUdWc2MyVWdhV1lnS0dselYyVmlWMjl5YTJWeUtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCMGNua2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUVsdUlHRWdkMlZpSUhkdmNtdGxjaXdnZFhObElHbHRjRzl5ZEZOamNtbHdkSE11SUZSb2FYTWdhWE1nYm05MElHRWdkbVZ5ZVZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklHVm1abWxqYVdWdWRDQjFjMlVnYjJZZ2FXMXdiM0owVTJOeWFYQjBjeXdnYVcxd2IzSjBVMk55YVhCMGN5QjNhV3hzSUdKc2IyTnJJSFZ1ZEdsc1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdhWFJ6SUhOamNtbHdkQ0JwY3lCa2IzZHViRzloWkdWa0lHRnVaQ0JsZG1Gc2RXRjBaV1F1SUVodmQyVjJaWElzSUdsbUlIZGxZaUIzYjNKclpYSnpYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnWVhKbElHbHVJSEJzWVhrc0lIUm9aU0JsZUhCbFkzUmhkR2x2YmlCcGN5QjBhR0YwSUdFZ1luVnBiR1FnYUdGeklHSmxaVzRnWkc5dVpTQnpiMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUhSb1lYUWdiMjVzZVNCdmJtVWdjMk55YVhCMElHNWxaV1J6SUhSdklHSmxJR3h2WVdSbFpDQmhibmwzWVhrdUlGUm9hWE1nYldGNUlHNWxaV1JjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCMGJ5QmlaU0J5WldWMllXeDFZWFJsWkNCcFppQnZkR2hsY2lCMWMyVWdZMkZ6WlhNZ1ltVmpiMjFsSUdOdmJXMXZiaTVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwYlhCdmNuUlRZM0pwY0hSektIVnliQ2s3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5QkJZMk52ZFc1MElHWnZjaUJoYm05dWVXMXZkWE1nYlc5a2RXeGxjMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR052Ym5SbGVIUXVZMjl0Y0d4bGRHVk1iMkZrS0cxdlpIVnNaVTVoYldVcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnZlNCallYUmphQ0FvWlNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHTnZiblJsZUhRdWIyNUZjbkp2Y2lodFlXdGxSWEp5YjNJb0oybHRjRzl5ZEhOamNtbHdkSE1uTEZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQW5hVzF3YjNKMFUyTnlhWEIwY3lCbVlXbHNaV1FnWm05eUlDY2dLMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCdGIyUjFiR1ZPWVcxbElDc2dKeUJoZENBbklDc2dkWEpzTEZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmxMRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCYmJXOWtkV3hsVG1GdFpWMHBLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdmVnh1SUNBZ0lIMDdYRzVjYmlBZ0lDQXZMeUJNYjI5cklHWnZjaUJoSUdSaGRHRXRiV0ZwYmlCelkzSnBjSFFnWVhSMGNtbGlkWFJsTENCM2FHbGphQ0JqYjNWc1pDQmhiSE52SUdGa2FuVnpkQ0IwYUdVZ1ltRnpaVlZ5YkM1Y2JpQWdJQ0JwWmlBb2FYTkNjbTkzYzJWeUlDWW1JQ0ZqWm1jdWMydHBjRVJoZEdGTllXbHVLU0I3WEc0Z0lDQWdJQ0FnSUM4dklFWnBaM1Z5WlNCdmRYUWdZbUZ6WlZWeWJDNGdSMlYwSUdsMElHWnliMjBnZEdobElITmpjbWx3ZENCMFlXY2dkMmwwYUNCeVpYRjFhWEpsTG1weklHbHVJR2wwTGx4dUlDQWdJQ0FnSUNCbFlXTm9VbVYyWlhKelpTaHpZM0pwY0hSektDa3NJR1oxYm1OMGFXOXVJQ2h6WTNKcGNIUXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDOHZJRk5sZENCMGFHVWdKMmhsWVdRbklIZG9aWEpsSUhkbElHTmhiaUJoY0hCbGJtUWdZMmhwYkdSeVpXNGdZbmxjYmlBZ0lDQWdJQ0FnSUNBZ0lDOHZJSFZ6YVc1bklIUm9aU0J6WTNKcGNIUW5jeUJ3WVhKbGJuUXVYRzRnSUNBZ0lDQWdJQ0FnSUNCcFppQW9JV2hsWVdRcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQm9aV0ZrSUQwZ2MyTnlhWEIwTG5CaGNtVnVkRTV2WkdVN1hHNGdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQzh2SUV4dmIyc2dabTl5SUdFZ1pHRjBZUzF0WVdsdUlHRjBkSEpwWW5WMFpTQjBieUJ6WlhRZ2JXRnBiaUJ6WTNKcGNIUWdabTl5SUhSb1pTQndZV2RsWEc0Z0lDQWdJQ0FnSUNBZ0lDQXZMeUIwYnlCc2IyRmtMaUJKWmlCcGRDQnBjeUIwYUdWeVpTd2dkR2hsSUhCaGRHZ2dkRzhnWkdGMFlTQnRZV2x1SUdKbFkyOXRaWE1nZEdobFhHNGdJQ0FnSUNBZ0lDQWdJQ0F2THlCaVlYTmxWWEpzTENCcFppQnBkQ0JwY3lCdWIzUWdZV3h5WldGa2VTQnpaWFF1WEc0Z0lDQWdJQ0FnSUNBZ0lDQmtZWFJoVFdGcGJpQTlJSE5qY21sd2RDNW5aWFJCZEhSeWFXSjFkR1VvSjJSaGRHRXRiV0ZwYmljcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnYVdZZ0tHUmhkR0ZOWVdsdUtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnVUhKbGMyVnlkbVVnWkdGMFlVMWhhVzRnYVc0Z1kyRnpaU0JwZENCcGN5QmhJSEJoZEdnZ0tHa3VaUzRnWTI5dWRHRnBibk1nSno4bktWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHMWhhVzVUWTNKcGNIUWdQU0JrWVhSaFRXRnBianRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dklGTmxkQ0JtYVc1aGJDQmlZWE5sVlhKc0lHbG1JSFJvWlhKbElHbHpJRzV2ZENCaGJISmxZV1I1SUdGdUlHVjRjR3hwWTJsMElHOXVaUzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb0lXTm1aeTVpWVhObFZYSnNLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJRkIxYkd3Z2IyWm1JSFJvWlNCa2FYSmxZM1J2Y25rZ2IyWWdaR0YwWVMxdFlXbHVJR1p2Y2lCMWMyVWdZWE1nZEdobFhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUdKaGMyVlZjbXd1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lITnlZeUE5SUcxaGFXNVRZM0pwY0hRdWMzQnNhWFFvSnk4bktUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYldGcGJsTmpjbWx3ZENBOUlITnlZeTV3YjNBb0tUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYzNWaVVHRjBhQ0E5SUhOeVl5NXNaVzVuZEdnZ1B5QnpjbU11YW05cGJpZ25MeWNwSUNzZ0p5OG5JRG9nSnk0dkp6dGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCalptY3VZbUZ6WlZWeWJDQTlJSE4xWWxCaGRHZzdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1UzUnlhWEFnYjJabUlHRnVlU0IwY21GcGJHbHVaeUF1YW5NZ2MybHVZMlVnYldGcGJsTmpjbWx3ZENCcGN5QnViM2RjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlCc2FXdGxJR0VnYlc5a2RXeGxJRzVoYldVdVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2JXRnBibE5qY21sd2RDQTlJRzFoYVc1VFkzSnBjSFF1Y21Wd2JHRmpaU2hxYzFOMVptWnBlRkpsWjBWNGNDd2dKeWNwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdTV1lnYldGcGJsTmpjbWx3ZENCcGN5QnpkR2xzYkNCaElIQmhkR2dzSUdaaGJHd2dZbUZqYXlCMGJ5QmtZWFJoVFdGcGJseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2h5WlhFdWFuTkZlSFJTWldkRmVIQXVkR1Z6ZENodFlXbHVVMk55YVhCMEtTa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCdFlXbHVVMk55YVhCMElEMGdaR0YwWVUxaGFXNDdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1VIVjBJSFJvWlNCa1lYUmhMVzFoYVc0Z2MyTnlhWEIwSUdsdUlIUm9aU0JtYVd4bGN5QjBieUJzYjJGa0xseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHTm1aeTVrWlhCeklEMGdZMlpuTG1SbGNITWdQeUJqWm1jdVpHVndjeTVqYjI1allYUW9iV0ZwYmxOamNtbHdkQ2tnT2lCYmJXRnBibE5qY21sd2RGMDdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J5WlhSMWNtNGdkSEoxWlR0Y2JpQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnZlNrN1hHNGdJQ0FnZlZ4dVhHNGdJQ0FnTHlvcVhHNGdJQ0FnSUNvZ1JYaGxZM1YwWlhNZ2RHaGxJSFJsZUhRdUlFNXZjbTFoYkd4NUlHcDFjM1FnZFhObGN5QmxkbUZzTENCaWRYUWdZMkZ1SUdKbElHMXZaR2xtYVdWa0lIUnZJSFZ6WlNCaElHSmxkSFJsY2l3Z1pXNTJhWEp2Ym0xbGJuUXRjM0JsWTJsbWFXTWdZMkZzYkM1Y2JpQWdJQ0FnS2lCUGJteDVJSFZ6WldRZ1ptOXlJSFJ5WVc1emNHbHNhVzVuSUd4dllXUmxjaUJ3YkhWbmFXNXpMQ0J1YjNRZ1ptOXlJSEJzWVdsdUlFcFRJRzF2WkhWc1pYTXVYRzRnSUNBZ0lDcGNiaUFnSUNBZ0tpQkFjR0Z5WVcwZ2UxTjBjbWx1WjMwZ2RHVjRkQ0JVYUdVZ2RHVjRkQ0IwYnlCbGVHVmpkWFJsTDJWMllXeDFZWFJsTGx4dUlDQWdJQ0FxTDF4dUlDQWdJSEpsY1M1bGVHVmpJRDBnWm5WdVkzUnBiMjRnS0hSbGVIUXBJSHRjYmlBZ0lDQWdJQ0FnTHlwcWMyeHBiblFnWlhacGJEb2dkSEoxWlNBcUwxeHVJQ0FnSUNBZ0lDQnlaWFIxY200Z1pYWmhiQ2gwWlhoMEtUdGNiaUFnSUNCOU8xeHVYRzRnSUNBZ0x5OGdVMlYwSUhWd0lIZHBkR2dnWTI5dVptbG5JR2x1Wm04dVhHNGdJQ0FnY21WeEtHTm1aeWs3WEc1OUtTZ3BPMXh1SWl3aUx5b2dMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMWNiaUIyZFdVdWFuTWdNakF4T0Mwd05pMHdOMXh1SUVkaGJXSnBieUJIYldKSVhHNGdhSFIwY0RvdkwzZDNkeTVuWVcxaWFXOHVaR1ZjYmlCRGIzQjVjbWxuYUhRZ0tHTXBJREl3TVRnZ1IyRnRZbWx2SUVkdFlraGNiaUJTWld4bFlYTmxaQ0IxYm1SbGNpQjBhR1VnUjA1VklFZGxibVZ5WVd3Z1VIVmliR2xqSUV4cFkyVnVjMlVnS0ZabGNuTnBiMjRnTWlsY2JpQmJhSFIwY0RvdkwzZDNkeTVuYm5VdWIzSm5MMnhwWTJWdWMyVnpMMmR3YkMweUxqQXVhSFJ0YkYxY2JpQXRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExTMHRMUzB0TFMwdExWeHVJQ292WEc1Y2JtcHpaUzVqYjNKbExuWjFaU0E5SUdwelpTNWpiM0psTG5aMVpTQjhmQ0I3ZlR0Y2JseHVMeW9xWEc0Z0tpQktVMFVnVm5WbElFbHVkR1ZuY21GMGFXOXVYRzRnS2x4dUlDb2dTVzVwZEdsaGJHbDZaWE1nZEdobElGWjFaU0JoY0hCc2FXTmhkR2x2YmlCbWIzSWdkR2hsSUdOMWNuSmxiblFnY0dGblpTNWNiaUFxWEc0Z0tpQldkV1VnYzNWd2NHOXlkQ0JwY3lCaFkzUnBkbUYwWldRZ2FXWWdkR2hsSUdkc2IySmhiQ0JLVTBWdVoybHVaVU52Ym1acFozVnlZWFJwYjI0Z2IySnFaV04wSUdOdmJuUmhhVzV6SUhObGRITWdkR2hsSUhKbGNYVmxjM1JsWkNCMllXeDFaWE1nWm05eVhHNGdLaUIwYUdVZ2FXNTBaV2R5WVhScGIyNHVYRzRnS2x4dUlDb2dZR0JnWEc0Z0tpQjNhVzVrYjNjdVNsTkZibWRwYm1WRGIyNW1hV2QxY21GMGFXOXVJRDBnZTF4dUlDcGNkQ0JsYm5acGNtOXViV1Z1ZERvZ0oyUmxkbVZzYjNCdFpXNTBKeXhjYmlBcVhIUWdZWEJ3VlhKc09pQW5hSFIwY0RvdkwyVjRZVzF3YkdVdWIzSm5KeXhjYmlBcVhIUWdZWEJ3Vm1WeWMybHZiam9nSjNZeExqQXVNQ2NzWEc0Z0tseDBJSFJ5WVc1emJHRjBhVzl1Y3pvZ2UzMHNYRzRnS2x4MElHeGhibWQxWVdkbFEyOWtaVG9nSjJWdUp5eGNiaUFxWEhRZ2NHRm5aVlJ2YTJWdU9pQW5ZM055Wmkxd2NtOTBaV04wYVc5dUxYUnZhMlZ1Snl4Y2JpQXFYSFFnWTJGamFHVlViMnRsYmpvZ0oyTmhZMmhsTFdKMWMzUnBibWN0ZEc5clpXNG5JQ3hjYmlBcVhIUWdkblZsT2lCN1hHNGdLbHgwSUZ4MFpXdzZJQ2N1ZG5WbExXbHVjM1JoYm1ObEoxeHVJQ3BjZENCOUxDQmNiaUFxWEhRZ1kyOXNiR1ZqZEdsdmJuTTZJRnRjYmlBcVhIUWdYSFI3Ym1GdFpUb2dKMk52Ym5SeWIyeHNaWEp6Snl3Z1lYUjBjbWxpZFhSbE9pQW5ZMjl1ZEhKdmJHeGxjaWQ5TEZ4dUlDcGNkQ0JjZEh0dVlXMWxPaUFuWlhoMFpXNXphVzl1Y3ljc0lHRjBkSEpwWW5WMFpUb2dKMlY0ZEdWdWMybHZiaWQ5TEZ4dUlDcGNkQ0JjZEh0dVlXMWxPaUFuZDJsa1oyVjBjeWNzSUdGMGRISnBZblYwWlRvZ0ozZHBaR2RsZENkOUxGeHVJQ3BjZENCY2RIdHVZVzFsT2lBblkyOXRjR0YwYVdKcGJHbDBlU2NzSUdGMGRISnBZblYwWlRvZ0oyTnZiWEJoZEdsaWFXeHBkSGtuZlZ4dUlDcGNkQ0JkTEZ4dUlDcGNkQ0J5WldkcGMzUnllVG9nZTMwZ1hHNGdLaUI5TzF4dUlDb2dZR0JnWEc0Z0tseHVJQ29nUUcxdlpIVnNaU0JLVTBVdlEyOXlaUzl3YjJ4NVptbHNiSE5jYmlBcUwxeHVLR1oxYm1OMGFXOXVJQ2hsZUhCdmNuUnpLU0I3WEc1Y2JpQWdJQ0FuZFhObElITjBjbWxqZENjN1hHNWNiaUFnSUNBdktpcGNiaUFnSUNBZ0tpQkliMnhrY3lCMmRXVWdhVzV6ZEdGdVkyVnpYRzRnSUNBZ0lDcGNiaUFnSUNBZ0tpQkFkSGx3WlNCN1QySnFaV04wZlZ4dUlDQWdJQ0FxTDF4dUlDQWdJR052Ym5OMElHbHVjM1JoYm1ObGN5QTlJSHQ5TzF4dVhHNGdJQ0FnTHlvcVhHNGdJQ0FnSUNvZ1NHOXNaSE1nWjNKdmRYQmxaQ0JqYjIxd2IyNWxiblJ6TGx4dUlDQWdJQ0FxWEc0Z0lDQWdJQ29nUjNKdmRYQmxaQ0JqYjIxd2IyNWxiblJ6SUdGeVpTQmlaV2x1WnlCMWMyVmtJR1p2Y2lCaGMzTnBaMjV0Wlc1MGN5QmlaWFIzWldWdUlHRWdjR0Z5Wlc1MElHRnVaQ0JqYUdsc1pDQldkV1VnWTI5dGNHOXVaVzUwY3k1Y2JpQWdJQ0FnS2x4dUlDQWdJQ0FxSUVCMGVYQmxJSHRQWW1wbFkzUjlYRzRnSUNBZ0lDb3ZYRzRnSUNBZ1kyOXVjM1FnWTI5dGNHOXVaVzUwY3lBOUlIdDlPMXh1WEc0Z0lDQWdMeW9xWEc0Z0lDQWdJQ29nU0c5c1pITWdaM0p2ZFhCbFpDQmphR2xzWkNCamIyMXdiMjVsYm5SekxseHVJQ0FnSUNBcVhHNGdJQ0FnSUNvZ1IzSnZkWEJsWkNCamIyMXdiMjVsYm5SeklHRnlaU0JpWldsdVp5QjFjMlZrSUdadmNpQmhjM05wWjI1dFpXNTBjeUJpWlhSM1pXVnVJR0VnY0dGeVpXNTBJR0Z1WkNCamFHbHNaQ0JXZFdVZ1kyOXRjRzl1Wlc1MGN5NWNiaUFnSUNBZ0tseHVJQ0FnSUNBcUlFQjBlWEJsSUh0UFltcGxZM1I5WEc0Z0lDQWdJQ292WEc0Z0lDQWdZMjl1YzNRZ1kyaHBiR1JEYjIxd2IyNWxiblJ6SUQwZ2UzMDdYRzVjYmlBZ0lDQXZLaXBjYmlBZ0lDQWdLaUJKYm1sMGFXRnNhWHBsSUhSb1pTQldkV1VnWVhCd2JHbGpZWFJwYjI0Z2IyNWpaU0IwYUdVZ1NsTWdSVzVuYVc1bElHaGhjeUJtYVc1cGMyaGxaQ0JzYjJGa2FXNW5JR0ZzYkNCMGFHVWdiVzlrZFd4bGN5NWNiaUFnSUNBZ0tpOWNiaUFnSUNCa2IyTjFiV1Z1ZEM1aFpHUkZkbVZ1ZEV4cGMzUmxibVZ5S0NkS1UwVk9SMGxPUlY5SlRrbFVYMFpKVGtsVFNFVkVKeXdnS0NrZ1BUNGdlMXh1SUNBZ0lDQWdJQ0JqYjI1emRDQm5iRzlpWVd4RGIyNW1hV2NnUFNCcWMyVXVZMjl5WlM1amIyNW1hV2N1WjJWMEtDZDJkV1VuS1R0Y2JseHVJQ0FnSUNBZ0lDQXZMeUJEYUdWamF5Qm1iM0lnVm5WbElFcFRSU0JqYjI1bWFXZDFjbUYwYVc5dUxpQmNiaUFnSUNBZ0lDQWdhV1lnS0NGbmJHOWlZV3hEYjI1bWFXY3BJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lISmxkSFZ5Ymp0Y2JpQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJR052Ym5OMElISnZiM1J6SUQwZ1pHOWpkVzFsYm5RdWNYVmxjbmxUWld4bFkzUnZja0ZzYkNobmJHOWlZV3hEYjI1bWFXY3VaV3dwTzF4dVhHNGdJQ0FnSUNBZ0lFRnljbUY1TG1aeWIyMG9jbTl2ZEhNcExtWnZja1ZoWTJnb0tISnZiM1FzSUdsdVpHVjRLU0E5UGlCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0JqYjI1emRDQnVZVzFsSUQwZ2NtOXZkQzVuWlhSQmRIUnlhV0oxZEdVb0oyUmhkR0V0ZG5WbExXbHVjM1JoYm1ObExXNWhiV1VuS1NCOGZDQmdkblZsTFdsdWMzUmhibU5sTFNSN2FXNWtaWGg5WUR0Y2JpQWdJQ0FnSUNBZ0lDQWdJR052Ym5OMElHTnZibVpwWnlBOUlFOWlhbVZqZEM1aGMzTnBaMjRvZTMwc0lHZHNiMkpoYkVOdmJtWnBaeXdnZTJWc09pQnliMjkwZlNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0JxYzJVdVkyOXlaUzUyZFdVdVkzSmxZWFJsS0c1aGJXVXNJR052Ym1acFp5azdYRzRnSUNBZ0lDQWdJSDBwTzF4dUlDQWdJSDBwTzF4dVhHNGdJQ0FnTHlvcVhHNGdJQ0FnSUNvZ1VtVm5hWE4wWlhJZ1lTQktVeUJGYm1kcGJtVWdkblZsSUcxdlpIVnNaUzVjYmlBZ0lDQWdLbHh1SUNBZ0lDQXFJRUJ3WVhKaGJTQjdUMkpxWldOMGZTQnRiMlIxYkdWY2JpQWdJQ0FnS2k5Y2JpQWdJQ0JsZUhCdmNuUnpMbkpsWjJsemRHVnlUVzlrZFd4bElEMGdablZ1WTNScGIyNGdLRzF2WkhWc1pTa2dlMXh1SUNBZ0lDQWdJQ0JwWmlBb0lXMXZaSFZzWlNCOGZDQWhiVzlrZFd4bExuWjFaU2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdjbVYwZFhKdU8xeHVJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnYVdZZ0tDRnRiMlIxYkdVdWNHRnlaVzUwS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0JxYzJVdVkyOXlaUzUyZFdVdWNtVm5hWE4wWlhKRGIyMXdiMjVsYm5Rb2JXOWtkV3hsTG01aGJXVXNJRzF2WkhWc1pTazdYRzRnSUNBZ0lDQWdJSDBnWld4elpTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCcWMyVXVZMjl5WlM1MmRXVXVjbVZuYVhOMFpYSkRhR2xzWkVOdmJYQnZibVZ1ZENodGIyUjFiR1V1Y0dGeVpXNTBMQ0J0YjJSMWJHVXVibUZ0WlN3Z2JXOWtkV3hsS1R0Y2JpQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUgwN1hHNWNiaUFnSUNBdktpcGNiaUFnSUNBZ0tpQlNaV2RwYzNSbGNpQmhJR05vYVd4a0lHTnZiWEJ2Ym1WdWRDNWNiaUFnSUNBZ0tseHVJQ0FnSUNBcUlFQndZWEpoYlNCN1UzUnlhVzVuZlNCd1lYSmxiblFnVUdGeVpXNTBJR052YlhCdmJtVnVkQ0J5WldabGNtVnVZMlV1WEc0Z0lDQWdJQ29nUUhCaGNtRnRJSHRUZEhKcGJtZDlJRzVoYldVZ1RtRnRaU0JqYjIxd2IyNWxiblFnY21WbVpYSmxibU5sTGx4dUlDQWdJQ0FxSUVCd1lYSmhiU0I3VDJKcVpXTjBmU0JqYUdsc1pFTnZiWEJ2Ym1WdWRDQkRhR2xzWkNCamIyMXdiMjVsYm5RZ1pHVm1hVzVwZEdsdmJpNWNiaUFnSUNBZ0tpOWNiaUFnSUNCbGVIQnZjblJ6TG5KbFoybHpkR1Z5UTJocGJHUkRiMjF3YjI1bGJuUWdQU0JtZFc1amRHbHZiaUFvY0dGeVpXNTBMQ0J1WVcxbExDQmphR2xzWkVOdmJYQnZibVZ1ZENrZ2UxeHVJQ0FnSUNBZ0lDQmphR2xzWkVOdmJYQnZibVZ1ZEhOYmNHRnlaVzUwWFNBOUlHTm9hV3hrUTI5dGNHOXVaVzUwYzF0d1lYSmxiblJkSUh4OElIdDlPMXh1WEc0Z0lDQWdJQ0FnSUdsbUlDaGphR2xzWkVOdmJYQnZibVZ1ZEhOYmNHRnlaVzUwWFZ0dVlXMWxYU2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdjbVYwZFhKdU8xeHVJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnWTJocGJHUkRiMjF3YjI1bGJuUnpXM0JoY21WdWRGMWJibUZ0WlYwZ1BTQmphR2xzWkVOdmJYQnZibVZ1ZER0Y2JpQWdJQ0I5TzF4dVhHNGdJQ0FnTHlvcVhHNGdJQ0FnSUNvZ1VtVm5hWE4wWlhJZ1lTQmpiMjF3YjI1bGJuUXVYRzRnSUNBZ0lDcGNiaUFnSUNBZ0tpQkFjR0Z5WVcwZ2UxTjBjbWx1WjMwZ2JtRnRaU0JPWVcxbElHTnZiWEJ2Ym1WdWRDQnlaV1psY21WdVkyVXVYRzRnSUNBZ0lDb2dRSEJoY21GdElIdFBZbXBsWTNSOUlHTnZiWEJ2Ym1WdWRDQkRiMjF3YjI1bGJuUWdaR1ZtYVc1cGRHbHZiaTVjYmlBZ0lDQWdLaTljYmlBZ0lDQmxlSEJ2Y25SekxuSmxaMmx6ZEdWeVEyOXRjRzl1Wlc1MElEMGdablZ1WTNScGIyNGdLRzVoYldVc0lHTnZiWEJ2Ym1WdWRDa2dlMXh1SUNBZ0lDQWdJQ0JwWmlBb1kyOXRjRzl1Wlc1MGMxdHVZVzFsWFNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnY21WMGRYSnVPMXh1SUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ1kyOXRjRzl1Wlc1MGMxdHVZVzFsWFNBOUlHTnZiWEJ2Ym1WdWREdGNiaUFnSUNCOU8xeHVYRzRnSUNBZ0x5b3FYRzRnSUNBZ0lDb2dRM0psWVhSbElHRWdibVYzSUZaMVpTQnBibk4wWVc1alpTNWNiaUFnSUNBZ0tseHVJQ0FnSUNBcUlFQndZWEpoYlNCN1UzUnlhVzVuZlNCdVlXMWxJRlZ1YVhGMVpTQnVZVzFsSUdadmNpQnBibk4wWVc1alpTNWNiaUFnSUNBZ0tpQkFjR0Z5WVcwZ2UwOWlhbVZqZEgwZ1kyOXVabWxuSUZaMVpTQmpiMjVtYVdkMWNtRjBhVzl1SUdadmNpQjBhR1VnYm1WM0lHbHVjM1JoYm1ObExseHVJQ0FnSUNBcUwxeHVJQ0FnSUdWNGNHOXlkSE11WTNKbFlYUmxJRDBnWm5WdVkzUnBiMjRnS0c1aGJXVXNJR052Ym1acFp5a2dlMXh1SUNBZ0lDQWdJQ0JwWmlBb2FXNXpkR0Z1WTJWelcyNWhiV1ZkS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0IwYUhKdmR5QnVaWGNnUlhKeWIzSW9ZRWx1YzNSaGJtTmxJSGRwZEdnZ2JtRnRaU0FrZTI1aGJXVjlJR0ZzY21WaFpIa2daWGhwYzNSekxDQndiR1ZoYzJVZ2RYTmxJR0VnWkdsbVptVnlaVzUwSUc1aGJXVXVZQ2s3WEc0Z0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQm1iM0lnS0d4bGRDQnVZVzFsSUdsdUlHTnZiWEJ2Ym1WdWRITXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lHTnZiWEJ2Ym1WdWRITmJibUZ0WlYwdVkyOXRjRzl1Wlc1MGN5QTlJR05vYVd4a1EyOXRjRzl1Wlc1MGMxdHVZVzFsWFNCOGZDQjdmVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdWblZsTG1OdmJYQnZibVZ1ZENodVlXMWxMQ0JqYjIxd2IyNWxiblJ6VzI1aGJXVmRLVHRjYmlBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lHbHVjM1JoYm1ObGMxdHVZVzFsWFNBOUlHNWxkeUJXZFdVb1kyOXVabWxuS1R0Y2JpQWdJQ0I5TzF4dVhHNGdJQ0FnTHlvcVhHNGdJQ0FnSUNvZ1JHVnpkSEp2ZVNCaElIWjFaU0JwYm5OMFlXNWpaUzVjYmlBZ0lDQWdLbHh1SUNBZ0lDQXFJRUJ3WVhKaGJTQjdVM1J5YVc1bmZTQnVZVzFsSUVsdWMzUmhibU5sSUc1aGJXVWdkRzhnWW1VZ1pHVnpkSEp2ZVdWa0xseHVJQ0FnSUNBcUwxeHVJQ0FnSUdWNGNHOXlkSE11WkdWemRISnZlU0E5SUdaMWJtTjBhVzl1SUNodVlXMWxLU0I3WEc0Z0lDQWdJQ0FnSUdsbUlDZ2hhVzV6ZEdGdVkyVnpXMjVoYldWZEtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCMGFISnZkeUJ1WlhjZ1JYSnliM0lvWUVsdWMzUmhibU5sSUhkcGRHZ2dibUZ0WlNBa2UyNWhiV1Y5SUdSdlpYTWdibTkwSUdWNGFYTjBMQ0J0WVd0bElITjFjbVVnZEdobElHbHVjM1JoYm1ObElIZGhjeUJwYm1sMGFXRnNhWHBsWkNCamIzSnlaV04wYkhrdVlDazdYRzRnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNCcGJuTjBZVzVqWlhOYmJtRnRaVjB1SkdSbGMzUnliM2tvS1R0Y2JseHVJQ0FnSUNBZ0lDQmtaV3hsZEdVZ2FXNXpkR0Z1WTJWelcyNWhiV1ZkTzF4dUlDQWdJSDA3WEc1Y2JpQWdJQ0F2S2lwY2JpQWdJQ0FnS2lCSFpYUWdZV3hzSUdGamRHbDJaU0JwYm5OMFlXNWpaWE11WEc0Z0lDQWdJQ3BjYmlBZ0lDQWdLaUJBY21WMGRYSnVJSHRQWW1wbFkzUjlJRTVoYldVZ0xTQnBibk4wWVc1alpTQndZV2x5Y3k1Y2JpQWdJQ0FnS2k5Y2JpQWdJQ0JsZUhCdmNuUnpMbWx1YzNSaGJtTmxjeUE5SUdaMWJtTjBhVzl1SUNncElIdGNiaUFnSUNBZ0lDQWdjbVYwZFhKdUlHbHVjM1JoYm1ObGN6dGNiaUFnSUNCOU8xeHVmU2tvYW5ObExtTnZjbVV1ZG5WbEtUc2lYWDA9In0=
