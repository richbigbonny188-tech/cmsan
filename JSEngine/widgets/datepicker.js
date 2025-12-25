/* --------------------------------------------------------------
 datepicker.js 2018-08-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Datepicker Widget
 *
 * Creates a customizable date(range)picker.
 *
 * jQueryUI Datepicker API: {@link http://api.jqueryui.com/datepicker}
 *
 * You can add the `data-datepicker-gx-container` attribute and it will style the datepicker with
 * the new CSS styles located at the gx-admin.css file. This might be useful when the .gx-container
 * class is not set directly on the <body> tag but in an inner div element of the page. The datepicker
 * will create a new div element which might be outside the .gx-container and therefore will not have
 * its style. This widget is already styled in Honeygrid.
 *
 * ### Example
 *
 * When the page loads, an input field as a date picker will be added.
 *
 * ```html
 * <input type="text" data-jse-widget="datepicker" data-datepicker-show-On="focus"
 *      data-datepicker-gx-container placeholder="##.##.####" />
 * ```
 *
 * For custom date format, use the 'data-datepicker-format' attribute.
 *
 * @module JSE/Widgets/datepicker
 * @requires jQueryUI-Library
 */
jse.widgets.module(
    'datepicker',

    [
        `${jse.source}/vendor/jquery-ui-dist/jquery-ui.min.css`,
        `${jse.source}/vendor/jquery-ui-dist/jquery-ui.min.js`
    ],

    /** @lends module:Widgets/datepicker */

    function (data) {

        'use strict';

        // ------------------------------------------------------------------------
        // VARIABLE DEFINITION
        // ------------------------------------------------------------------------

        var
            /**
             * Widget Reference
             *
             * @type {object}
             */
            $this = $(this),

            /**
             * Default Options for Widget
             *
             * @type {object}
             */
            defaults = {},

            /**
             * Final Widget Options
             *
             * @type {object}
             */
            options = $.extend(true, {}, defaults, data),

            /**
             * Module Object
             *
             * @type {object}
             */
            module = {};

        // ------------------------------------------------------------------------
        // FUNCTIONALITY
        // ------------------------------------------------------------------------

        /**
         * Update Timestamp Field
         *
         * Function that updates the timestamp field belonging to this datepicker. If no
         * one exists, it gets generated.
         *
         * @param {object} inst jQuery datepicker instance object.
         */
        var _updateTimestampField = function (inst) {
            var name = $this.attr('name'),
                $ts = $this.siblings('[name="ts_' + name + '"]'),
                value = new Date([inst.selectedYear, inst.selectedMonth + 1, inst.selectedDay].join(', ')).valueOf();
            if (!$ts.length) {
                $this.after('<input type="hidden" name="ts_' + name + '" value="' + value + '"/>');
            } else {
                $ts.val(value);
            }
        };

        /**
         * Get Configuration
         *
         * Function to create the datepicker configuration object.
         *
         * @returns {object} JSON-configuration object.
         */
        var _getConfiguration = function () {
            // Set default min / max values.
            options.max = options.max ? new Date(options.max) : null;
            options.min = options.min ? new Date(options.min) : null;

            // Base Configuration
            var configuration = {
                constrainInput: true,
                showOn: 'focus',
                showWeek: true,
                changeMonth: true,
                changeYear: true,
                minDate: options.min,
                maxDate: options.max,
                dayNamesMin: [
                    jse.core.lang.translate('_SUNDAY_SHORT', 'general'),
                    jse.core.lang.translate('_MONDAY_SHORT', 'general'),
                    jse.core.lang.translate('_TUESDAY_SHORT', 'general'),
                    jse.core.lang.translate('_WEDNESDAY_SHORT', 'general'),
                    jse.core.lang.translate('_THURSDAY_SHORT', 'general'),
                    jse.core.lang.translate('_FRIDAY_SHORT', 'general'),
                    jse.core.lang.translate('_SATURDAY_SHORT', 'general')
                ],
                dayNames: [
                    jse.core.lang.translate('_SUNDAY', 'general'),
                    jse.core.lang.translate('_MONDAY', 'general'),
                    jse.core.lang.translate('_TUESDAY', 'general'),
                    jse.core.lang.translate('_WEDNESDAY', 'general'),
                    jse.core.lang.translate('_THURSDAY', 'general'),
                    jse.core.lang.translate('_FRIDAY', 'general'),
                    jse.core.lang.translate('_SATURDAY', 'general')
                ],
                monthNamesShort: [
                    jse.core.lang.translate('_JANUARY_SHORT', 'general'),
                    jse.core.lang.translate('_FEBRUARY_SHORT', 'general'),
                    jse.core.lang.translate('_MARCH_SHORT', 'general'),
                    jse.core.lang.translate('_APRIL_SHORT', 'general'),
                    jse.core.lang.translate('_MAY_SHORT', 'general'),
                    jse.core.lang.translate('_JUNE_SHORT', 'general'),
                    jse.core.lang.translate('_JULY_SHORT', 'general'),
                    jse.core.lang.translate('_AUGUST_SHORT', 'general'),
                    jse.core.lang.translate('_SEPTEMBER_SHORT', 'general'),
                    jse.core.lang.translate('_OCTOBER_SHORT', 'general'),
                    jse.core.lang.translate('_NOVEMBER_SHORT', 'general'),
                    jse.core.lang.translate('_DECEMBER_SHORT', 'general')
                ],
                monthNames: [
                    jse.core.lang.translate('_JANUARY', 'general'),
                    jse.core.lang.translate('_FEBRUARY', 'general'),
                    jse.core.lang.translate('_MARCH', 'general'),
                    jse.core.lang.translate('_APRIL', 'general'),
                    jse.core.lang.translate('_MAY', 'general'),
                    jse.core.lang.translate('_JUNE', 'general'),
                    jse.core.lang.translate('_JULY', 'general'),
                    jse.core.lang.translate('_AUGUST', 'general'),
                    jse.core.lang.translate('_SEPTEMBER', 'general'),
                    jse.core.lang.translate('_OCTOBER', 'general'),
                    jse.core.lang.translate('_NOVEMBER', 'general'),
                    jse.core.lang.translate('_DECEMBER', 'general')
                ],
                prevText: jse.core.lang.translate('_PREV', 'general'),
                nextText: jse.core.lang.translate('_NEXT', 'general'),
                onSelect: function (date, inst) {
                    _updateTimestampField(inst);
                }
            };

            // Set "showOn" options.
            if (options.showOn) {
                configuration.showOn = options.showOn;
            }

            // Sets the alternative field with an other date format (for backend).
            if (options.alt) {
                configuration.altField = options.alt;
                configuration.altFormat = '@';
            }

            // Trigger an event onSelect to inform dependencies and set the min / max value at the
            // current value of the dependency.
            if (options.depends && options.type) {
                var $depends = $(options.depends),
                    value = $depends.val(),
                    type = (options.type === 'max') ? 'min' : 'max';

                // Add callback to the onSelect-Event.
                configuration.onSelect = function (date, inst) {
                    _updateTimestampField(inst);
                    var payload = {
                        'type': options.type,
                        'date': [inst.selectedYear, inst.selectedMonth + 1, inst.selectedDay].join(', ')
                    };
                    $depends.trigger('datepicker.selected', [payload]);
                };

                // Get and set the current value of the dependency.
                if (value) {
                    var date = $.datepicker.parseDate($.datepicker._defaults.dateFormat, value);
                    configuration[type + 'Date'] = date;
                }
            }

            // Override date format with data attribute value
            configuration.dateFormat = data.format || jse.core.config.get('languageCode') === 'de' ? 'dd.mm.yy' : 'mm/dd/yy';

            // Merge the data array with the datepicker array for enabling the original widget API options.
            configuration = $.extend(true, {}, configuration, data);

            return configuration;
        };

        // ------------------------------------------------------------------------
        // INITIALIZATION
        // ------------------------------------------------------------------------

        /**
         * Initialize method of the widget, called by the engine.
         */
        module.init = function (done) {
            // Enable the datepicker widget.
            var configuration = _getConfiguration();
            $this.datepicker(configuration);

            // Get the gx-container style (newer style).
            if (typeof options.gxContainer !== 'undefined') {
                $(document).find('.ui-datepicker').not('.gx-container').addClass('gx-container');
            }

            // Add event listener for other datepickers to set the min / maxDate (for daterange).
            $this.on('datepicker.selected', function (e, d) {
                $this.datepicker('option', d.type + 'Date', new Date(d.date));
            });

            done();
        };

        // Return data to module engine.
        return module;
    });
