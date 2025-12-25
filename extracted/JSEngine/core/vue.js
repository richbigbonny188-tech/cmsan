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
    const instances = {};

    /**
     * Holds grouped components.
     *
     * Grouped components are being used for assignments between a parent and child Vue components.
     *
     * @type {Object}
     */
    const components = {};

    /**
     * Holds grouped child components.
     *
     * Grouped components are being used for assignments between a parent and child Vue components.
     *
     * @type {Object}
     */
    const childComponents = {};

    /**
     * Initialize the Vue application once the JS Engine has finished loading all the modules.
     */
    document.addEventListener('JSENGINE_INIT_FINISHED', () => {
        const globalConfig = jse.core.config.get('vue');

        // Check for Vue JSE configuration. 
        if (!globalConfig) {
            return;
        }

        const roots = document.querySelectorAll(globalConfig.el);

        Array.from(roots).forEach((root, index) => {
            const name = root.getAttribute('data-vue-instance-name') || `vue-instance-${index}`;
            const config = Object.assign({}, globalConfig, {el: root});
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
            throw new Error(`Instance with name ${name} already exists, please use a different name.`);
        }

        for (let name in components) {
            components[name].components = childComponents[name] || {};

            Vue.component(name, components[name]);
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
            throw new Error(`Instance with name ${name} does not exist, make sure the instance was initialized correctly.`);
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