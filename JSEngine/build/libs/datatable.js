'use strict';

/* --------------------------------------------------------------
 datatable.js 2016-07-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.datatable = jse.libs.datatable || {};

/**
 * ## DataTable Library
 *
 * This is a wrapper library for the manipulation of jQuery DataTables. Use the "create" method with DataTable
 * configuration to initialize a table on your page. All you need when using this library is an empty `<table>`
 * element. Visit the official website of DataTables to check examples and other information about the plugin.
 *
 * {@link http://www.datatables.net Official DataTables Website}
 *
 * Notice: Make sure that you load the DataTables vendor files before using this module.
 *
 * ### Examples
 *
 * **Example - Create A New Instance**
 * ```javascript
 * var tableApi = jse.libs.datatable.create($('#my-table'), {
 *      ajax: 'https://example.org/table-data.php',
 *      columns: [
 *          { title: 'Name', data: 'name' defaultContent: '...' },
 *          { title: 'Email', data: 'email' },
 *          { title: 'Actions', data: null, orderable: false, defaultContent: 'Add | Edit | Delete' },
 *      ]
 * });
 * ```
 *
 * **Example - Add Error Handler**
 * ```javascript
 * jse.libs.datatable.error($('#my-table'), function(event, settings, techNote, message) {
 *      // Log error in the JavaScript console.
 *      console.log('DataTable Error:', message);
 * });
 * ```
 *
 * @module JSE/Libs/datatable
 * @exports jse.libs.datatable
 * @requires jQuery-DataTables-Plugin
 */
(function (exports) {

    'use strict';

    // ------------------------------------------------------------------------
    // VARIABLES
    // ------------------------------------------------------------------------

    var languages = {
        de: {
            'sEmptyTable': 'Keine Daten in der Tabelle vorhanden',
            'sInfo': '_START_ bis _END_ (von _TOTAL_)',
            'sInfoEmpty': '0 bis 0 von 0 Einträgen',
            'sInfoFiltered': '(gefiltert von _MAX_ Einträgen)',
            'sInfoPostFix': '',
            'sInfoThousands': '.',
            'sLengthMenu': '_MENU_ Einträge anzeigen',
            'sLoadingRecords': 'Wird geladen...',
            'sProcessing': 'Bitte warten...',
            'sSearch': 'Suchen',
            'sZeroRecords': 'Keine Einträge vorhanden.',
            'oPaginate': {
                'sFirst': 'Erste',
                'sPrevious': 'Zurück',
                'sNext': 'Nächste',
                'sLast': 'Letzte'
            },
            'oAria': {
                'sSortAscending': ': aktivieren, um Spalte aufsteigend zu sortieren',
                'sSortDescending': ': aktivieren, um Spalte absteigend zu sortieren'
            }
        },
        en: {
            'sEmptyTable': 'No data available in table',
            'sInfo': '_START_ to _END_ (of _TOTAL_)',
            'sInfoEmpty': 'Showing 0 to 0 of 0 entries',
            'sInfoFiltered': '(filtered from _MAX_ total entries)',
            'sInfoPostFix': '',
            'sInfoThousands': ',',
            'sLengthMenu': 'Show _MENU_ entries',
            'sLoadingRecords': 'Loading...',
            'sProcessing': 'Processing...',
            'sSearch': 'Search:',
            'sZeroRecords': 'No matching records found',
            'oPaginate': {
                'sFirst': 'First',
                'sLast': 'Last',
                'sNext': 'Next',
                'sPrevious': 'Previous'
            },
            'oAria': {
                'sSortAscending': ': activate to sort column ascending',
                'sSortDescending': ': activate to sort column descending'
            }
        }
    };

    // ------------------------------------------------------------------------
    // FUNCTIONALITY
    // ------------------------------------------------------------------------

    /**
     * Reorder the table columns as defined in the active columns array.
     *
     * @param {jQuery} $target Table jQuery selector object.
     * @param {Object} columnDefinitions Array containing the DataTable column definitions.
     * @param {Array} activeColumnNames Array containing the slug-names of the active columns.
     *
     * @return {Array} Returns array with the active column definitions ready to use in DataTable.columns option.
     *
     * @private
     */
    function _reorderColumns($target, columnDefinitions, activeColumnNames) {
        activeColumnNames.unshift('checkbox');
        activeColumnNames.push('actions');

        // Hide the table header cells that are not active.
        $.each(columnDefinitions, function (index, columnDefinition) {
            $target.find('thead tr').each(function () {
                var $headerCell = $(this).find('[data-column-name="' + columnDefinition.name + '"]');

                if (columnDefinition.data !== null && activeColumnNames.indexOf(columnDefinition.name) === -1) {
                    $headerCell.hide();
                }
            });
        });

        // Prepare the active column definitions.
        var finalColumnDefinitions = [],
            columnIndexes = [];

        $.each(activeColumnNames, function (index, name) {
            $.each(columnDefinitions, function (index, columnDefinition) {
                if (columnDefinition.name === name) {
                    // Add the active column definition in the "finalColumnDefinitions" array.
                    finalColumnDefinitions.push(columnDefinition);
                    var headerCellIndex = $target.find('thead:first tr:first [data-column-name="' + columnDefinition.name + '"]').index();
                    columnIndexes.push(headerCellIndex);
                    return true; // continue
                }
            });
        });

        finalColumnDefinitions.sort(function (a, b) {
            var aIndex = activeColumnNames.indexOf(a.name);
            var bIndex = activeColumnNames.indexOf(b.name);

            if (aIndex < bIndex) {
                return -1;
            } else if (aIndex > bIndex) {
                return 1;
            } else {
                return 0;
            }
        });

        // Reorder the table header elements depending the activeColumnNames order.
        $target.find('thead tr').each(function () {
            var _this = this;

            var activeColumnSelections = [$(this).find('th:first')];

            // Sort the columns in the correct order.
            columnIndexes.forEach(function (index) {
                var $headerCell = $(_this).find('th').eq(index);
                activeColumnSelections.push($headerCell);
            });

            // Move the columns to their final position.
            activeColumnSelections.forEach(function ($headerCell, index) {
                if (index === 0) {
                    return true;
                }

                $headerCell.insertAfter(activeColumnSelections[index - 1]);
            });
        });

        return finalColumnDefinitions;
    }

    /**
     * Creates a DataTable Instance
     *
     * This method will create a new instance of datatable into a `<table>` element. It enables
     * developers to easily pass the configuration needed for different and more special situations.
     *
     * @param {jQuery} $target jQuery object for the target table.
     * @param {Object} configuration DataTables configuration applied on the new instance.
     *
     * @return {DataTable} Returns the DataTable API instance (different from the jQuery object).
     */
    exports.create = function ($target, configuration) {
        return $target.DataTable(configuration);
    };

    /**
     * Sets the error handler for specific DataTable.
     *
     * DataTables provide a useful mechanism that enables developers to control errors during data parsing.
     * If there is an error in the AJAX response or some data are invalid in the JavaScript code you can use
     * this method to control the behavior of the app and show or log the error messages.
     *
     * {@link http://datatables.net/reference/event/error}
     *
     * @param {jQuery} $target jQuery object for the target table.
     * @param {Object} callback Provide a callback method called with the "event", "settings", "techNote",
     * "message" arguments (see provided link).
     */
    exports.error = function ($target, callback) {
        $.fn.dataTable.ext.errMode = 'none';
        $target.on('error.dt', callback).on('xhr.dt', function (event, settings, json, xhr) {
            if (json.exception === true) {
                callback(event, settings, null, json.message);
            }
        });
    };

    /**
     * Sets the callback method when ajax load of data is complete.
     *
     * This method is useful for checking PHP errors or modifying the data before
     * they are displayed to the server.
     *
     * {@link http://datatables.net/reference/event/xhr}
     *
     * @param {jQuery} $target jQuery object for the target table.
     * @param {Function} callback Provide a callback method called with the "event", "settings", "techNote",
     * "message" arguments (see provided link).
     */
    exports.ajaxComplete = function ($target, callback) {
        $target.on('xhr.dt', callback);
    };

    /**
     * Sets the table column to be displayed as an index.
     *
     * This method will easily enable you to set a column as an index column, used
     * for numbering the table rows regardless of the search, sorting and row count.
     *
     * {@link http://www.datatables.net/examples/api/counter_columns.html}
     *
     * @param {jQuery} $target jQuery object for the target table.
     * @param {Number} columnIndex Zero based index of the column to be indexed.
     */
    exports.indexColumn = function ($target, columnIndex) {
        $target.on('order.dt search.dt', function () {
            $target.DataTable().column(columnIndex, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function (cell, index) {
                cell.innerHTML = index + 1;
            });
        });
    };

    /**
     * Returns the german translation of the DataTables
     *
     * This method provides a quick way to get the language JSON without having to perform
     * and AJAX request to the server. If you setup your DataTable manually you can set the
     * "language" attribute with this method.
     *
     * @deprecated Since v1.4, use the "getTranslations" method instead.
     *
     * @return {Object} Returns the german translation, must be the same as the "german.lang.json" file.
     */
    exports.getGermanTranslation = function () {
        jse.core.debug.warn('DataTables Library: the getGermanTranslation method is deprecated and will be removed ' + 'in JSE v1.5, please use the "getTranslations" method instead.');
        return languages.de;
    };

    /**
     * Get the DataTables translation depending the language code parameter.
     *
     * @param {String} languageCode Provide 'de' or 'en' (you can also use the jse.core.config.get('languageCode') to
     * get the current language code).
     *
     * @return {Object} Returns the translation strings in an object literal as described by the official DataTables
     * documentation.
     *
     * {@link https://www.datatables.net/plug-ins/i18n}
     */
    exports.getTranslations = function (languageCode) {
        if (languages[languageCode] === undefined) {
            jse.core.debug.warn('DataTables Library: The requested DataTables translation was not found:', languageCode);
            languageCode = 'en';
        }

        return languages[languageCode];
    };

    /**
     * Prepare table columns.
     *
     * This method will convert the column definitions to a DataTable compatible format and also reorder
     * the table header cells of the "thead" element.
     *
     * @param {jQuery} $target Table jQuery selector object.
     * @param {Object} columnDefinitions Array containing the DataTable column definitions.
     * @param {String[]} activeColumnNames Array containing the slug-names of the active columns.
     *
     * @return {Object[]} Returns array with the active column definitions ready to use in DataTable.columns option.
     */
    exports.prepareColumns = function ($target, columnDefinitions, activeColumnNames) {
        var convertedColumnDefinitions = [];

        for (var columnName in columnDefinitions) {
            var columnDefinition = columnDefinitions[columnName];
            columnDefinition.name = columnName;
            convertedColumnDefinitions.push(columnDefinition);
        }

        return _reorderColumns($target, convertedColumnDefinitions, activeColumnNames);
    };
})(jse.libs.datatable);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImRhdGF0YWJsZS5qcyJdLCJuYW1lcyI6WyJqc2UiLCJsaWJzIiwiZGF0YXRhYmxlIiwiZXhwb3J0cyIsImxhbmd1YWdlcyIsImRlIiwiZW4iLCJfcmVvcmRlckNvbHVtbnMiLCIkdGFyZ2V0IiwiY29sdW1uRGVmaW5pdGlvbnMiLCJhY3RpdmVDb2x1bW5OYW1lcyIsInVuc2hpZnQiLCJwdXNoIiwiJCIsImVhY2giLCJpbmRleCIsImNvbHVtbkRlZmluaXRpb24iLCJmaW5kIiwiJGhlYWRlckNlbGwiLCJuYW1lIiwiZGF0YSIsImluZGV4T2YiLCJoaWRlIiwiZmluYWxDb2x1bW5EZWZpbml0aW9ucyIsImNvbHVtbkluZGV4ZXMiLCJoZWFkZXJDZWxsSW5kZXgiLCJzb3J0IiwiYSIsImIiLCJhSW5kZXgiLCJiSW5kZXgiLCJhY3RpdmVDb2x1bW5TZWxlY3Rpb25zIiwiZm9yRWFjaCIsImVxIiwiaW5zZXJ0QWZ0ZXIiLCJjcmVhdGUiLCJjb25maWd1cmF0aW9uIiwiRGF0YVRhYmxlIiwiZXJyb3IiLCJjYWxsYmFjayIsImZuIiwiZGF0YVRhYmxlIiwiZXh0IiwiZXJyTW9kZSIsIm9uIiwiZXZlbnQiLCJzZXR0aW5ncyIsImpzb24iLCJ4aHIiLCJleGNlcHRpb24iLCJtZXNzYWdlIiwiYWpheENvbXBsZXRlIiwiaW5kZXhDb2x1bW4iLCJjb2x1bW5JbmRleCIsImNvbHVtbiIsInNlYXJjaCIsIm9yZGVyIiwibm9kZXMiLCJjZWxsIiwiaW5uZXJIVE1MIiwiZ2V0R2VybWFuVHJhbnNsYXRpb24iLCJjb3JlIiwiZGVidWciLCJ3YXJuIiwiZ2V0VHJhbnNsYXRpb25zIiwibGFuZ3VhZ2VDb2RlIiwidW5kZWZpbmVkIiwicHJlcGFyZUNvbHVtbnMiLCJjb252ZXJ0ZWRDb2x1bW5EZWZpbml0aW9ucyIsImNvbHVtbk5hbWUiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsSUFBSUMsSUFBSixDQUFTQyxTQUFULEdBQXFCRixJQUFJQyxJQUFKLENBQVND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QVosWUFBUXlELG9CQUFSLEdBQStCLFlBQVk7QUFDdkM1RCxZQUFJNkQsSUFBSixDQUFTQyxLQUFULENBQWVDLElBQWYsQ0FBb0IsMkZBQ2QsK0RBRE47QUFFQSxlQUFPM0QsVUFBVUMsRUFBakI7QUFDSCxLQUpEOztBQU1BOzs7Ozs7Ozs7OztBQVdBRixZQUFRNkQsZUFBUixHQUEwQixVQUFVQyxZQUFWLEVBQXdCO0FBQzlDLFlBQUk3RCxVQUFVNkQsWUFBVixNQUE0QkMsU0FBaEMsRUFBMkM7QUFDdkNsRSxnQkFBSTZELElBQUosQ0FBU0MsS0FBVCxDQUFlQyxJQUFmLENBQW9CLHlFQUFwQixFQUErRkUsWUFBL0Y7QUFDQUEsMkJBQWUsSUFBZjtBQUNIOztBQUVELGVBQU83RCxVQUFVNkQsWUFBVixDQUFQO0FBQ0gsS0FQRDs7QUFTQTs7Ozs7Ozs7Ozs7O0FBWUE5RCxZQUFRZ0UsY0FBUixHQUF5QixVQUFVM0QsT0FBVixFQUFtQkMsaUJBQW5CLEVBQXNDQyxpQkFBdEMsRUFBeUQ7QUFDOUUsWUFBSTBELDZCQUE2QixFQUFqQzs7QUFFQSxhQUFLLElBQUlDLFVBQVQsSUFBdUI1RCxpQkFBdkIsRUFBMEM7QUFDdEMsZ0JBQUlPLG1CQUFtQlAsa0JBQWtCNEQsVUFBbEIsQ0FBdkI7QUFDQXJELDZCQUFpQkcsSUFBakIsR0FBd0JrRCxVQUF4QjtBQUNBRCx1Q0FBMkJ4RCxJQUEzQixDQUFnQ0ksZ0JBQWhDO0FBQ0g7O0FBRUQsZUFBT1QsZ0JBQWdCQyxPQUFoQixFQUF5QjRELDBCQUF6QixFQUFxRDFELGlCQUFyRCxDQUFQO0FBQ0gsS0FWRDtBQVlILENBdlJBLEVBdVJDVixJQUFJQyxJQUFKLENBQVNDLFNBdlJWLENBQUQiLCJmaWxlIjoiZGF0YXRhYmxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBkYXRhdGFibGUuanMgMjAxNi0wNy0xMVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbmpzZS5saWJzLmRhdGF0YWJsZSA9IGpzZS5saWJzLmRhdGF0YWJsZSB8fCB7fTtcblxuLyoqXG4gKiAjIyBEYXRhVGFibGUgTGlicmFyeVxuICpcbiAqIFRoaXMgaXMgYSB3cmFwcGVyIGxpYnJhcnkgZm9yIHRoZSBtYW5pcHVsYXRpb24gb2YgalF1ZXJ5IERhdGFUYWJsZXMuIFVzZSB0aGUgXCJjcmVhdGVcIiBtZXRob2Qgd2l0aCBEYXRhVGFibGVcbiAqIGNvbmZpZ3VyYXRpb24gdG8gaW5pdGlhbGl6ZSBhIHRhYmxlIG9uIHlvdXIgcGFnZS4gQWxsIHlvdSBuZWVkIHdoZW4gdXNpbmcgdGhpcyBsaWJyYXJ5IGlzIGFuIGVtcHR5IGA8dGFibGU+YFxuICogZWxlbWVudC4gVmlzaXQgdGhlIG9mZmljaWFsIHdlYnNpdGUgb2YgRGF0YVRhYmxlcyB0byBjaGVjayBleGFtcGxlcyBhbmQgb3RoZXIgaW5mb3JtYXRpb24gYWJvdXQgdGhlIHBsdWdpbi5cbiAqXG4gKiB7QGxpbmsgaHR0cDovL3d3dy5kYXRhdGFibGVzLm5ldCBPZmZpY2lhbCBEYXRhVGFibGVzIFdlYnNpdGV9XG4gKlxuICogTm90aWNlOiBNYWtlIHN1cmUgdGhhdCB5b3UgbG9hZCB0aGUgRGF0YVRhYmxlcyB2ZW5kb3IgZmlsZXMgYmVmb3JlIHVzaW5nIHRoaXMgbW9kdWxlLlxuICpcbiAqICMjIyBFeGFtcGxlc1xuICpcbiAqICoqRXhhbXBsZSAtIENyZWF0ZSBBIE5ldyBJbnN0YW5jZSoqXG4gKiBgYGBqYXZhc2NyaXB0XG4gKiB2YXIgdGFibGVBcGkgPSBqc2UubGlicy5kYXRhdGFibGUuY3JlYXRlKCQoJyNteS10YWJsZScpLCB7XG4gKiAgICAgIGFqYXg6ICdodHRwczovL2V4YW1wbGUub3JnL3RhYmxlLWRhdGEucGhwJyxcbiAqICAgICAgY29sdW1uczogW1xuICogICAgICAgICAgeyB0aXRsZTogJ05hbWUnLCBkYXRhOiAnbmFtZScgZGVmYXVsdENvbnRlbnQ6ICcuLi4nIH0sXG4gKiAgICAgICAgICB7IHRpdGxlOiAnRW1haWwnLCBkYXRhOiAnZW1haWwnIH0sXG4gKiAgICAgICAgICB7IHRpdGxlOiAnQWN0aW9ucycsIGRhdGE6IG51bGwsIG9yZGVyYWJsZTogZmFsc2UsIGRlZmF1bHRDb250ZW50OiAnQWRkIHwgRWRpdCB8IERlbGV0ZScgfSxcbiAqICAgICAgXVxuICogfSk7XG4gKiBgYGBcbiAqXG4gKiAqKkV4YW1wbGUgLSBBZGQgRXJyb3IgSGFuZGxlcioqXG4gKiBgYGBqYXZhc2NyaXB0XG4gKiBqc2UubGlicy5kYXRhdGFibGUuZXJyb3IoJCgnI215LXRhYmxlJyksIGZ1bmN0aW9uKGV2ZW50LCBzZXR0aW5ncywgdGVjaE5vdGUsIG1lc3NhZ2UpIHtcbiAqICAgICAgLy8gTG9nIGVycm9yIGluIHRoZSBKYXZhU2NyaXB0IGNvbnNvbGUuXG4gKiAgICAgIGNvbnNvbGUubG9nKCdEYXRhVGFibGUgRXJyb3I6JywgbWVzc2FnZSk7XG4gKiB9KTtcbiAqIGBgYFxuICpcbiAqIEBtb2R1bGUgSlNFL0xpYnMvZGF0YXRhYmxlXG4gKiBAZXhwb3J0cyBqc2UubGlicy5kYXRhdGFibGVcbiAqIEByZXF1aXJlcyBqUXVlcnktRGF0YVRhYmxlcy1QbHVnaW5cbiAqL1xuKGZ1bmN0aW9uIChleHBvcnRzKSB7XG5cbiAgICAndXNlIHN0cmljdCc7XG5cbiAgICAvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAgICAvLyBWQVJJQUJMRVNcbiAgICAvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblxuICAgIGxldCBsYW5ndWFnZXMgPSB7XG4gICAgICAgIGRlOiB7XG4gICAgICAgICAgICAnc0VtcHR5VGFibGUnOiAnS2VpbmUgRGF0ZW4gaW4gZGVyIFRhYmVsbGUgdm9yaGFuZGVuJyxcbiAgICAgICAgICAgICdzSW5mbyc6ICdfU1RBUlRfIGJpcyBfRU5EXyAodm9uIF9UT1RBTF8pJyxcbiAgICAgICAgICAgICdzSW5mb0VtcHR5JzogJzAgYmlzIDAgdm9uIDAgRWludHLDpGdlbicsXG4gICAgICAgICAgICAnc0luZm9GaWx0ZXJlZCc6ICcoZ2VmaWx0ZXJ0IHZvbiBfTUFYXyBFaW50csOkZ2VuKScsXG4gICAgICAgICAgICAnc0luZm9Qb3N0Rml4JzogJycsXG4gICAgICAgICAgICAnc0luZm9UaG91c2FuZHMnOiAnLicsXG4gICAgICAgICAgICAnc0xlbmd0aE1lbnUnOiAnX01FTlVfIEVpbnRyw6RnZSBhbnplaWdlbicsXG4gICAgICAgICAgICAnc0xvYWRpbmdSZWNvcmRzJzogJ1dpcmQgZ2VsYWRlbi4uLicsXG4gICAgICAgICAgICAnc1Byb2Nlc3NpbmcnOiAnQml0dGUgd2FydGVuLi4uJyxcbiAgICAgICAgICAgICdzU2VhcmNoJzogJ1N1Y2hlbicsXG4gICAgICAgICAgICAnc1plcm9SZWNvcmRzJzogJ0tlaW5lIEVpbnRyw6RnZSB2b3JoYW5kZW4uJyxcbiAgICAgICAgICAgICdvUGFnaW5hdGUnOiB7XG4gICAgICAgICAgICAgICAgJ3NGaXJzdCc6ICdFcnN0ZScsXG4gICAgICAgICAgICAgICAgJ3NQcmV2aW91cyc6ICdadXLDvGNrJyxcbiAgICAgICAgICAgICAgICAnc05leHQnOiAnTsOkY2hzdGUnLFxuICAgICAgICAgICAgICAgICdzTGFzdCc6ICdMZXR6dGUnXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgJ29BcmlhJzoge1xuICAgICAgICAgICAgICAgICdzU29ydEFzY2VuZGluZyc6ICc6IGFrdGl2aWVyZW4sIHVtIFNwYWx0ZSBhdWZzdGVpZ2VuZCB6dSBzb3J0aWVyZW4nLFxuICAgICAgICAgICAgICAgICdzU29ydERlc2NlbmRpbmcnOiAnOiBha3RpdmllcmVuLCB1bSBTcGFsdGUgYWJzdGVpZ2VuZCB6dSBzb3J0aWVyZW4nXG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIGVuOiB7XG4gICAgICAgICAgICAnc0VtcHR5VGFibGUnOiAnTm8gZGF0YSBhdmFpbGFibGUgaW4gdGFibGUnLFxuICAgICAgICAgICAgJ3NJbmZvJzogJ19TVEFSVF8gdG8gX0VORF8gKG9mIF9UT1RBTF8pJyxcbiAgICAgICAgICAgICdzSW5mb0VtcHR5JzogJ1Nob3dpbmcgMCB0byAwIG9mIDAgZW50cmllcycsXG4gICAgICAgICAgICAnc0luZm9GaWx0ZXJlZCc6ICcoZmlsdGVyZWQgZnJvbSBfTUFYXyB0b3RhbCBlbnRyaWVzKScsXG4gICAgICAgICAgICAnc0luZm9Qb3N0Rml4JzogJycsXG4gICAgICAgICAgICAnc0luZm9UaG91c2FuZHMnOiAnLCcsXG4gICAgICAgICAgICAnc0xlbmd0aE1lbnUnOiAnU2hvdyBfTUVOVV8gZW50cmllcycsXG4gICAgICAgICAgICAnc0xvYWRpbmdSZWNvcmRzJzogJ0xvYWRpbmcuLi4nLFxuICAgICAgICAgICAgJ3NQcm9jZXNzaW5nJzogJ1Byb2Nlc3NpbmcuLi4nLFxuICAgICAgICAgICAgJ3NTZWFyY2gnOiAnU2VhcmNoOicsXG4gICAgICAgICAgICAnc1plcm9SZWNvcmRzJzogJ05vIG1hdGNoaW5nIHJlY29yZHMgZm91bmQnLFxuICAgICAgICAgICAgJ29QYWdpbmF0ZSc6IHtcbiAgICAgICAgICAgICAgICAnc0ZpcnN0JzogJ0ZpcnN0JyxcbiAgICAgICAgICAgICAgICAnc0xhc3QnOiAnTGFzdCcsXG4gICAgICAgICAgICAgICAgJ3NOZXh0JzogJ05leHQnLFxuICAgICAgICAgICAgICAgICdzUHJldmlvdXMnOiAnUHJldmlvdXMnXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgJ29BcmlhJzoge1xuICAgICAgICAgICAgICAgICdzU29ydEFzY2VuZGluZyc6ICc6IGFjdGl2YXRlIHRvIHNvcnQgY29sdW1uIGFzY2VuZGluZycsXG4gICAgICAgICAgICAgICAgJ3NTb3J0RGVzY2VuZGluZyc6ICc6IGFjdGl2YXRlIHRvIHNvcnQgY29sdW1uIGRlc2NlbmRpbmcnXG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9O1xuXG4gICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gICAgLy8gRlVOQ1RJT05BTElUWVxuICAgIC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXG4gICAgLyoqXG4gICAgICogUmVvcmRlciB0aGUgdGFibGUgY29sdW1ucyBhcyBkZWZpbmVkIGluIHRoZSBhY3RpdmUgY29sdW1ucyBhcnJheS5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7alF1ZXJ5fSAkdGFyZ2V0IFRhYmxlIGpRdWVyeSBzZWxlY3RvciBvYmplY3QuXG4gICAgICogQHBhcmFtIHtPYmplY3R9IGNvbHVtbkRlZmluaXRpb25zIEFycmF5IGNvbnRhaW5pbmcgdGhlIERhdGFUYWJsZSBjb2x1bW4gZGVmaW5pdGlvbnMuXG4gICAgICogQHBhcmFtIHtBcnJheX0gYWN0aXZlQ29sdW1uTmFtZXMgQXJyYXkgY29udGFpbmluZyB0aGUgc2x1Zy1uYW1lcyBvZiB0aGUgYWN0aXZlIGNvbHVtbnMuXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIHtBcnJheX0gUmV0dXJucyBhcnJheSB3aXRoIHRoZSBhY3RpdmUgY29sdW1uIGRlZmluaXRpb25zIHJlYWR5IHRvIHVzZSBpbiBEYXRhVGFibGUuY29sdW1ucyBvcHRpb24uXG4gICAgICpcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIGZ1bmN0aW9uIF9yZW9yZGVyQ29sdW1ucygkdGFyZ2V0LCBjb2x1bW5EZWZpbml0aW9ucywgYWN0aXZlQ29sdW1uTmFtZXMpIHtcbiAgICAgICAgYWN0aXZlQ29sdW1uTmFtZXMudW5zaGlmdCgnY2hlY2tib3gnKTtcbiAgICAgICAgYWN0aXZlQ29sdW1uTmFtZXMucHVzaCgnYWN0aW9ucycpO1xuXG4gICAgICAgIC8vIEhpZGUgdGhlIHRhYmxlIGhlYWRlciBjZWxscyB0aGF0IGFyZSBub3QgYWN0aXZlLlxuICAgICAgICAkLmVhY2goY29sdW1uRGVmaW5pdGlvbnMsIChpbmRleCwgY29sdW1uRGVmaW5pdGlvbikgPT4ge1xuICAgICAgICAgICAgJHRhcmdldC5maW5kKCd0aGVhZCB0cicpLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIGxldCAkaGVhZGVyQ2VsbCA9ICQodGhpcykuZmluZChgW2RhdGEtY29sdW1uLW5hbWU9XCIke2NvbHVtbkRlZmluaXRpb24ubmFtZX1cIl1gKTtcblxuICAgICAgICAgICAgICAgIGlmIChjb2x1bW5EZWZpbml0aW9uLmRhdGEgIT09IG51bGwgJiYgYWN0aXZlQ29sdW1uTmFtZXMuaW5kZXhPZihjb2x1bW5EZWZpbml0aW9uLm5hbWUpID09PSAtMSkge1xuICAgICAgICAgICAgICAgICAgICAkaGVhZGVyQ2VsbC5oaWRlKCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgIC8vIFByZXBhcmUgdGhlIGFjdGl2ZSBjb2x1bW4gZGVmaW5pdGlvbnMuXG4gICAgICAgIGxldCBmaW5hbENvbHVtbkRlZmluaXRpb25zID0gW10sXG4gICAgICAgICAgICBjb2x1bW5JbmRleGVzID0gW107XG5cbiAgICAgICAgJC5lYWNoKGFjdGl2ZUNvbHVtbk5hbWVzLCAoaW5kZXgsIG5hbWUpID0+IHtcbiAgICAgICAgICAgICQuZWFjaChjb2x1bW5EZWZpbml0aW9ucywgKGluZGV4LCBjb2x1bW5EZWZpbml0aW9uKSA9PiB7XG4gICAgICAgICAgICAgICAgaWYgKGNvbHVtbkRlZmluaXRpb24ubmFtZSA9PT0gbmFtZSkge1xuICAgICAgICAgICAgICAgICAgICAvLyBBZGQgdGhlIGFjdGl2ZSBjb2x1bW4gZGVmaW5pdGlvbiBpbiB0aGUgXCJmaW5hbENvbHVtbkRlZmluaXRpb25zXCIgYXJyYXkuXG4gICAgICAgICAgICAgICAgICAgIGZpbmFsQ29sdW1uRGVmaW5pdGlvbnMucHVzaChjb2x1bW5EZWZpbml0aW9uKTtcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgaGVhZGVyQ2VsbEluZGV4ID0gJHRhcmdldFxuICAgICAgICAgICAgICAgICAgICAgICAgLmZpbmQoYHRoZWFkOmZpcnN0IHRyOmZpcnN0IFtkYXRhLWNvbHVtbi1uYW1lPVwiJHtjb2x1bW5EZWZpbml0aW9uLm5hbWV9XCJdYClcbiAgICAgICAgICAgICAgICAgICAgICAgIC5pbmRleCgpO1xuICAgICAgICAgICAgICAgICAgICBjb2x1bW5JbmRleGVzLnB1c2goaGVhZGVyQ2VsbEluZGV4KTtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHRydWU7IC8vIGNvbnRpbnVlXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgIGZpbmFsQ29sdW1uRGVmaW5pdGlvbnMuc29ydCgoYSwgYikgPT4ge1xuICAgICAgICAgICAgY29uc3QgYUluZGV4ID0gYWN0aXZlQ29sdW1uTmFtZXMuaW5kZXhPZihhLm5hbWUpO1xuICAgICAgICAgICAgY29uc3QgYkluZGV4ID0gYWN0aXZlQ29sdW1uTmFtZXMuaW5kZXhPZihiLm5hbWUpO1xuXG4gICAgICAgICAgICBpZiAoYUluZGV4IDwgYkluZGV4KSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIC0xO1xuICAgICAgICAgICAgfSBlbHNlIGlmIChhSW5kZXggPiBiSW5kZXgpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gMTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIDA7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIC8vIFJlb3JkZXIgdGhlIHRhYmxlIGhlYWRlciBlbGVtZW50cyBkZXBlbmRpbmcgdGhlIGFjdGl2ZUNvbHVtbk5hbWVzIG9yZGVyLlxuICAgICAgICAkdGFyZ2V0LmZpbmQoJ3RoZWFkIHRyJykuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICBsZXQgYWN0aXZlQ29sdW1uU2VsZWN0aW9ucyA9IFskKHRoaXMpLmZpbmQoJ3RoOmZpcnN0JyldO1xuXG4gICAgICAgICAgICAvLyBTb3J0IHRoZSBjb2x1bW5zIGluIHRoZSBjb3JyZWN0IG9yZGVyLlxuICAgICAgICAgICAgY29sdW1uSW5kZXhlcy5mb3JFYWNoKChpbmRleCkgPT4ge1xuICAgICAgICAgICAgICAgIGxldCAkaGVhZGVyQ2VsbCA9ICQodGhpcykuZmluZCgndGgnKS5lcShpbmRleCk7XG4gICAgICAgICAgICAgICAgYWN0aXZlQ29sdW1uU2VsZWN0aW9ucy5wdXNoKCRoZWFkZXJDZWxsKTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAvLyBNb3ZlIHRoZSBjb2x1bW5zIHRvIHRoZWlyIGZpbmFsIHBvc2l0aW9uLlxuICAgICAgICAgICAgYWN0aXZlQ29sdW1uU2VsZWN0aW9ucy5mb3JFYWNoKGZ1bmN0aW9uICgkaGVhZGVyQ2VsbCwgaW5kZXgpIHtcbiAgICAgICAgICAgICAgICBpZiAoaW5kZXggPT09IDApIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgJGhlYWRlckNlbGwuaW5zZXJ0QWZ0ZXIoYWN0aXZlQ29sdW1uU2VsZWN0aW9uc1tpbmRleCAtIDFdKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9KTtcblxuICAgICAgICByZXR1cm4gZmluYWxDb2x1bW5EZWZpbml0aW9ucztcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBDcmVhdGVzIGEgRGF0YVRhYmxlIEluc3RhbmNlXG4gICAgICpcbiAgICAgKiBUaGlzIG1ldGhvZCB3aWxsIGNyZWF0ZSBhIG5ldyBpbnN0YW5jZSBvZiBkYXRhdGFibGUgaW50byBhIGA8dGFibGU+YCBlbGVtZW50LiBJdCBlbmFibGVzXG4gICAgICogZGV2ZWxvcGVycyB0byBlYXNpbHkgcGFzcyB0aGUgY29uZmlndXJhdGlvbiBuZWVkZWQgZm9yIGRpZmZlcmVudCBhbmQgbW9yZSBzcGVjaWFsIHNpdHVhdGlvbnMuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge2pRdWVyeX0gJHRhcmdldCBqUXVlcnkgb2JqZWN0IGZvciB0aGUgdGFyZ2V0IHRhYmxlLlxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBjb25maWd1cmF0aW9uIERhdGFUYWJsZXMgY29uZmlndXJhdGlvbiBhcHBsaWVkIG9uIHRoZSBuZXcgaW5zdGFuY2UuXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIHtEYXRhVGFibGV9IFJldHVybnMgdGhlIERhdGFUYWJsZSBBUEkgaW5zdGFuY2UgKGRpZmZlcmVudCBmcm9tIHRoZSBqUXVlcnkgb2JqZWN0KS5cbiAgICAgKi9cbiAgICBleHBvcnRzLmNyZWF0ZSA9IGZ1bmN0aW9uICgkdGFyZ2V0LCBjb25maWd1cmF0aW9uKSB7XG4gICAgICAgIHJldHVybiAkdGFyZ2V0LkRhdGFUYWJsZShjb25maWd1cmF0aW9uKTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogU2V0cyB0aGUgZXJyb3IgaGFuZGxlciBmb3Igc3BlY2lmaWMgRGF0YVRhYmxlLlxuICAgICAqXG4gICAgICogRGF0YVRhYmxlcyBwcm92aWRlIGEgdXNlZnVsIG1lY2hhbmlzbSB0aGF0IGVuYWJsZXMgZGV2ZWxvcGVycyB0byBjb250cm9sIGVycm9ycyBkdXJpbmcgZGF0YSBwYXJzaW5nLlxuICAgICAqIElmIHRoZXJlIGlzIGFuIGVycm9yIGluIHRoZSBBSkFYIHJlc3BvbnNlIG9yIHNvbWUgZGF0YSBhcmUgaW52YWxpZCBpbiB0aGUgSmF2YVNjcmlwdCBjb2RlIHlvdSBjYW4gdXNlXG4gICAgICogdGhpcyBtZXRob2QgdG8gY29udHJvbCB0aGUgYmVoYXZpb3Igb2YgdGhlIGFwcCBhbmQgc2hvdyBvciBsb2cgdGhlIGVycm9yIG1lc3NhZ2VzLlxuICAgICAqXG4gICAgICoge0BsaW5rIGh0dHA6Ly9kYXRhdGFibGVzLm5ldC9yZWZlcmVuY2UvZXZlbnQvZXJyb3J9XG4gICAgICpcbiAgICAgKiBAcGFyYW0ge2pRdWVyeX0gJHRhcmdldCBqUXVlcnkgb2JqZWN0IGZvciB0aGUgdGFyZ2V0IHRhYmxlLlxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBjYWxsYmFjayBQcm92aWRlIGEgY2FsbGJhY2sgbWV0aG9kIGNhbGxlZCB3aXRoIHRoZSBcImV2ZW50XCIsIFwic2V0dGluZ3NcIiwgXCJ0ZWNoTm90ZVwiLFxuICAgICAqIFwibWVzc2FnZVwiIGFyZ3VtZW50cyAoc2VlIHByb3ZpZGVkIGxpbmspLlxuICAgICAqL1xuICAgIGV4cG9ydHMuZXJyb3IgPSBmdW5jdGlvbiAoJHRhcmdldCwgY2FsbGJhY2spIHtcbiAgICAgICAgJC5mbi5kYXRhVGFibGUuZXh0LmVyck1vZGUgPSAnbm9uZSc7XG4gICAgICAgICR0YXJnZXRcbiAgICAgICAgICAgIC5vbignZXJyb3IuZHQnLCBjYWxsYmFjaylcbiAgICAgICAgICAgIC5vbigneGhyLmR0JywgKGV2ZW50LCBzZXR0aW5ncywganNvbiwgeGhyKSA9PiB7XG4gICAgICAgICAgICAgICAgaWYgKGpzb24uZXhjZXB0aW9uID09PSB0cnVlKSB7XG4gICAgICAgICAgICAgICAgICAgIGNhbGxiYWNrKGV2ZW50LCBzZXR0aW5ncywgbnVsbCwganNvbi5tZXNzYWdlKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogU2V0cyB0aGUgY2FsbGJhY2sgbWV0aG9kIHdoZW4gYWpheCBsb2FkIG9mIGRhdGEgaXMgY29tcGxldGUuXG4gICAgICpcbiAgICAgKiBUaGlzIG1ldGhvZCBpcyB1c2VmdWwgZm9yIGNoZWNraW5nIFBIUCBlcnJvcnMgb3IgbW9kaWZ5aW5nIHRoZSBkYXRhIGJlZm9yZVxuICAgICAqIHRoZXkgYXJlIGRpc3BsYXllZCB0byB0aGUgc2VydmVyLlxuICAgICAqXG4gICAgICoge0BsaW5rIGh0dHA6Ly9kYXRhdGFibGVzLm5ldC9yZWZlcmVuY2UvZXZlbnQveGhyfVxuICAgICAqXG4gICAgICogQHBhcmFtIHtqUXVlcnl9ICR0YXJnZXQgalF1ZXJ5IG9iamVjdCBmb3IgdGhlIHRhcmdldCB0YWJsZS5cbiAgICAgKiBAcGFyYW0ge0Z1bmN0aW9ufSBjYWxsYmFjayBQcm92aWRlIGEgY2FsbGJhY2sgbWV0aG9kIGNhbGxlZCB3aXRoIHRoZSBcImV2ZW50XCIsIFwic2V0dGluZ3NcIiwgXCJ0ZWNoTm90ZVwiLFxuICAgICAqIFwibWVzc2FnZVwiIGFyZ3VtZW50cyAoc2VlIHByb3ZpZGVkIGxpbmspLlxuICAgICAqL1xuICAgIGV4cG9ydHMuYWpheENvbXBsZXRlID0gZnVuY3Rpb24gKCR0YXJnZXQsIGNhbGxiYWNrKSB7XG4gICAgICAgICR0YXJnZXQub24oJ3hoci5kdCcsIGNhbGxiYWNrKTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogU2V0cyB0aGUgdGFibGUgY29sdW1uIHRvIGJlIGRpc3BsYXllZCBhcyBhbiBpbmRleC5cbiAgICAgKlxuICAgICAqIFRoaXMgbWV0aG9kIHdpbGwgZWFzaWx5IGVuYWJsZSB5b3UgdG8gc2V0IGEgY29sdW1uIGFzIGFuIGluZGV4IGNvbHVtbiwgdXNlZFxuICAgICAqIGZvciBudW1iZXJpbmcgdGhlIHRhYmxlIHJvd3MgcmVnYXJkbGVzcyBvZiB0aGUgc2VhcmNoLCBzb3J0aW5nIGFuZCByb3cgY291bnQuXG4gICAgICpcbiAgICAgKiB7QGxpbmsgaHR0cDovL3d3dy5kYXRhdGFibGVzLm5ldC9leGFtcGxlcy9hcGkvY291bnRlcl9jb2x1bW5zLmh0bWx9XG4gICAgICpcbiAgICAgKiBAcGFyYW0ge2pRdWVyeX0gJHRhcmdldCBqUXVlcnkgb2JqZWN0IGZvciB0aGUgdGFyZ2V0IHRhYmxlLlxuICAgICAqIEBwYXJhbSB7TnVtYmVyfSBjb2x1bW5JbmRleCBaZXJvIGJhc2VkIGluZGV4IG9mIHRoZSBjb2x1bW4gdG8gYmUgaW5kZXhlZC5cbiAgICAgKi9cbiAgICBleHBvcnRzLmluZGV4Q29sdW1uID0gZnVuY3Rpb24gKCR0YXJnZXQsIGNvbHVtbkluZGV4KSB7XG4gICAgICAgICR0YXJnZXQub24oJ29yZGVyLmR0IHNlYXJjaC5kdCcsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICR0YXJnZXQuRGF0YVRhYmxlKCkuY29sdW1uKGNvbHVtbkluZGV4LCB7XG4gICAgICAgICAgICAgICAgc2VhcmNoOiAnYXBwbGllZCcsXG4gICAgICAgICAgICAgICAgb3JkZXI6ICdhcHBsaWVkJ1xuICAgICAgICAgICAgfSkubm9kZXMoKS5lYWNoKGZ1bmN0aW9uIChjZWxsLCBpbmRleCkge1xuICAgICAgICAgICAgICAgIGNlbGwuaW5uZXJIVE1MID0gaW5kZXggKyAxO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0pO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBSZXR1cm5zIHRoZSBnZXJtYW4gdHJhbnNsYXRpb24gb2YgdGhlIERhdGFUYWJsZXNcbiAgICAgKlxuICAgICAqIFRoaXMgbWV0aG9kIHByb3ZpZGVzIGEgcXVpY2sgd2F5IHRvIGdldCB0aGUgbGFuZ3VhZ2UgSlNPTiB3aXRob3V0IGhhdmluZyB0byBwZXJmb3JtXG4gICAgICogYW5kIEFKQVggcmVxdWVzdCB0byB0aGUgc2VydmVyLiBJZiB5b3Ugc2V0dXAgeW91ciBEYXRhVGFibGUgbWFudWFsbHkgeW91IGNhbiBzZXQgdGhlXG4gICAgICogXCJsYW5ndWFnZVwiIGF0dHJpYnV0ZSB3aXRoIHRoaXMgbWV0aG9kLlxuICAgICAqXG4gICAgICogQGRlcHJlY2F0ZWQgU2luY2UgdjEuNCwgdXNlIHRoZSBcImdldFRyYW5zbGF0aW9uc1wiIG1ldGhvZCBpbnN0ZWFkLlxuICAgICAqXG4gICAgICogQHJldHVybiB7T2JqZWN0fSBSZXR1cm5zIHRoZSBnZXJtYW4gdHJhbnNsYXRpb24sIG11c3QgYmUgdGhlIHNhbWUgYXMgdGhlIFwiZ2VybWFuLmxhbmcuanNvblwiIGZpbGUuXG4gICAgICovXG4gICAgZXhwb3J0cy5nZXRHZXJtYW5UcmFuc2xhdGlvbiA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAganNlLmNvcmUuZGVidWcud2FybignRGF0YVRhYmxlcyBMaWJyYXJ5OiB0aGUgZ2V0R2VybWFuVHJhbnNsYXRpb24gbWV0aG9kIGlzIGRlcHJlY2F0ZWQgYW5kIHdpbGwgYmUgcmVtb3ZlZCAnXG4gICAgICAgICAgICArICdpbiBKU0UgdjEuNSwgcGxlYXNlIHVzZSB0aGUgXCJnZXRUcmFuc2xhdGlvbnNcIiBtZXRob2QgaW5zdGVhZC4nKTtcbiAgICAgICAgcmV0dXJuIGxhbmd1YWdlcy5kZTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogR2V0IHRoZSBEYXRhVGFibGVzIHRyYW5zbGF0aW9uIGRlcGVuZGluZyB0aGUgbGFuZ3VhZ2UgY29kZSBwYXJhbWV0ZXIuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gbGFuZ3VhZ2VDb2RlIFByb3ZpZGUgJ2RlJyBvciAnZW4nICh5b3UgY2FuIGFsc28gdXNlIHRoZSBqc2UuY29yZS5jb25maWcuZ2V0KCdsYW5ndWFnZUNvZGUnKSB0b1xuICAgICAqIGdldCB0aGUgY3VycmVudCBsYW5ndWFnZSBjb2RlKS5cbiAgICAgKlxuICAgICAqIEByZXR1cm4ge09iamVjdH0gUmV0dXJucyB0aGUgdHJhbnNsYXRpb24gc3RyaW5ncyBpbiBhbiBvYmplY3QgbGl0ZXJhbCBhcyBkZXNjcmliZWQgYnkgdGhlIG9mZmljaWFsIERhdGFUYWJsZXNcbiAgICAgKiBkb2N1bWVudGF0aW9uLlxuICAgICAqXG4gICAgICoge0BsaW5rIGh0dHBzOi8vd3d3LmRhdGF0YWJsZXMubmV0L3BsdWctaW5zL2kxOG59XG4gICAgICovXG4gICAgZXhwb3J0cy5nZXRUcmFuc2xhdGlvbnMgPSBmdW5jdGlvbiAobGFuZ3VhZ2VDb2RlKSB7XG4gICAgICAgIGlmIChsYW5ndWFnZXNbbGFuZ3VhZ2VDb2RlXSA9PT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICAgICBqc2UuY29yZS5kZWJ1Zy53YXJuKCdEYXRhVGFibGVzIExpYnJhcnk6IFRoZSByZXF1ZXN0ZWQgRGF0YVRhYmxlcyB0cmFuc2xhdGlvbiB3YXMgbm90IGZvdW5kOicsIGxhbmd1YWdlQ29kZSk7XG4gICAgICAgICAgICBsYW5ndWFnZUNvZGUgPSAnZW4nO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIGxhbmd1YWdlc1tsYW5ndWFnZUNvZGVdO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBQcmVwYXJlIHRhYmxlIGNvbHVtbnMuXG4gICAgICpcbiAgICAgKiBUaGlzIG1ldGhvZCB3aWxsIGNvbnZlcnQgdGhlIGNvbHVtbiBkZWZpbml0aW9ucyB0byBhIERhdGFUYWJsZSBjb21wYXRpYmxlIGZvcm1hdCBhbmQgYWxzbyByZW9yZGVyXG4gICAgICogdGhlIHRhYmxlIGhlYWRlciBjZWxscyBvZiB0aGUgXCJ0aGVhZFwiIGVsZW1lbnQuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge2pRdWVyeX0gJHRhcmdldCBUYWJsZSBqUXVlcnkgc2VsZWN0b3Igb2JqZWN0LlxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBjb2x1bW5EZWZpbml0aW9ucyBBcnJheSBjb250YWluaW5nIHRoZSBEYXRhVGFibGUgY29sdW1uIGRlZmluaXRpb25zLlxuICAgICAqIEBwYXJhbSB7U3RyaW5nW119IGFjdGl2ZUNvbHVtbk5hbWVzIEFycmF5IGNvbnRhaW5pbmcgdGhlIHNsdWctbmFtZXMgb2YgdGhlIGFjdGl2ZSBjb2x1bW5zLlxuICAgICAqXG4gICAgICogQHJldHVybiB7T2JqZWN0W119IFJldHVybnMgYXJyYXkgd2l0aCB0aGUgYWN0aXZlIGNvbHVtbiBkZWZpbml0aW9ucyByZWFkeSB0byB1c2UgaW4gRGF0YVRhYmxlLmNvbHVtbnMgb3B0aW9uLlxuICAgICAqL1xuICAgIGV4cG9ydHMucHJlcGFyZUNvbHVtbnMgPSBmdW5jdGlvbiAoJHRhcmdldCwgY29sdW1uRGVmaW5pdGlvbnMsIGFjdGl2ZUNvbHVtbk5hbWVzKSB7XG4gICAgICAgIGxldCBjb252ZXJ0ZWRDb2x1bW5EZWZpbml0aW9ucyA9IFtdO1xuXG4gICAgICAgIGZvciAobGV0IGNvbHVtbk5hbWUgaW4gY29sdW1uRGVmaW5pdGlvbnMpIHtcbiAgICAgICAgICAgIGxldCBjb2x1bW5EZWZpbml0aW9uID0gY29sdW1uRGVmaW5pdGlvbnNbY29sdW1uTmFtZV07XG4gICAgICAgICAgICBjb2x1bW5EZWZpbml0aW9uLm5hbWUgPSBjb2x1bW5OYW1lO1xuICAgICAgICAgICAgY29udmVydGVkQ29sdW1uRGVmaW5pdGlvbnMucHVzaChjb2x1bW5EZWZpbml0aW9uKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBfcmVvcmRlckNvbHVtbnMoJHRhcmdldCwgY29udmVydGVkQ29sdW1uRGVmaW5pdGlvbnMsIGFjdGl2ZUNvbHVtbk5hbWVzKTtcbiAgICB9O1xuXG59KGpzZS5saWJzLmRhdGF0YWJsZSkpO1xuIl19
