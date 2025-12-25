/* --------------------------------------------------------------
 configuration.js 2016-12-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.configuration = jse.libs.configuration || {};

/**
 * ## Configurations Library
 *
 * This library makes it possible to receive shop configuration values.
 *
 * @module JSE/Libs/datatable
 * @exports jse.libs.datatable
 */
(function (exports) {
    'use strict';

    /**
     * @type {String}
     */
    const pageToken = jse.core.config.get('pageToken');

    /**
     * @type {String}
     */
    const baseUrl = `${jse.core.config.get('appUrl')}/shop.php?do=JsConfiguration`;

    /**
     * Get the configuration value by the provided key.
     *
     * @param key Configuration key.
     *
     * @returns {Promise} The promise will be resolve with the configuration value.
     */
    exports.get = key => {
        return new Promise((resolve, reject) => {
            const url = `${baseUrl}/Get`;
            $.ajax({url, data: {key, pageToken}})
                .done(response => resolve(response))
                .fail(error => reject(error));
        });
    };

})(jse.libs.configuration);
