'use strict';

/* --------------------------------------------------------------
 modal.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/* globals Mustache */

jse.libs.modal = jse.libs.modal || {};

/**
 * ## Modal Dialogs Library
 *
 * This library handles jQuery UI and Bootstrap modals and it is quite useful when it comes to display
 * plain messages. Make sure to use the "showMessage" function only in pages where Bootstrap is loaded.
 *
 * Notice: Some library methods are deprecated and will be removed with JSE v1.5.
 *
 * Notice: Make sure that you load the require vendor files (Bootstrap or jQuery UI) before using this module.
 *
 * ### Examples
 *
 * **Display jQuery UI message.**
 *
 * ```javascript
 * jse.libs.modal.message({
 *      title: 'My Title',      // Required
 *      content: 'My Content'   // Required
 *      buttons: { ... }        // Optional
 *      // Other jQueryUI Dialog Widget Options
 * });
 * ```
 *
 * **Display Bootstrap message.**
 *
 * ```javascript
 * jse.libs.modal.showMessage('Title', 'Content');
 * ```
 *
 * @todo Refactor modal functionality, remove Mustache dependency and split jQuery UI and Bootstrap use.
 *
 * @module JSE/Libs/modal
 * @exports jse.libs.modal
 *
 * @requires jQueryUI
 * @requires Bootstrap
 * @requires Mustache
 */
(function (exports) {

    'use strict';

    // ------------------------------------------------------------------------
    // VARIABLES
    // ------------------------------------------------------------------------

    /**
     * Contains Default Modal Buttons
     *
     * @type {Object}
     */

    var buttons = {
        'yes': {
            'name': jse.core.lang.translate('yes', 'buttons'),
            'type': 'success'
        },
        'no': {
            'name': jse.core.lang.translate('no', 'buttons'),
            'type': 'fail'
        },
        'abort': {
            'name': jse.core.lang.translate('abort', 'buttons'),
            'type': 'fail'
        },
        'ok': {
            'name': jse.core.lang.translate('ok', 'buttons'),
            'type': 'success'
        },
        'close': {
            'name': jse.core.lang.translate('close', 'buttons'),
            'type': 'fail'
        }
    };

    // ------------------------------------------------------------------------
    // PRIVATE FUNCTIONS
    // ------------------------------------------------------------------------

    /**
     * Get Form Data
     *
     * Returns all form data, which is stored inside the layer.
     *
     * @param {object} $self jQuery selector of the layer.
     * @param {bool} validateForm Flag that determines whether the form must be validated
     * before we get the data.
     *
     * @return {json} Returns a JSON with all form data.
     *
     * @private
     */
    var _getFormData = function _getFormData($self, validateForm) {
        var $forms = $self.filter('form').add($self.find('form')),
            formData = {},
            promises = [];

        if ($forms.length) {
            $forms.each(function () {
                var $form = $(this);

                if (validateForm) {
                    var localDeferred = $.Deferred();
                    promises.push(localDeferred);
                    $form.trigger('validator.validate', {
                        'deferred': localDeferred
                    });
                }

                var key = $form.attr('name') || $form.attr('id') || 'form_' + new Date().getTime() * Math.random();
                formData[key] = window.jse.lib.form.getData($form);
            });
        }

        return $.when.apply(undefined, promises).then(function () {
            return formData;
        }, function () {
            return formData;
        }).promise();
    };

    /**
     * Reject Handler
     *
     * @param {object} $element Selector element.
     * @param {object} deferred Deferred object.
     *
     * @private
     */
    var _rejectHandler = function _rejectHandler($element, deferred) {
        _getFormData($element).always(function (result) {
            deferred.reject(result);
            $element.dialog('close').remove();
        });
    };

    /**
     * Resolve Handler
     *
     * @param {object} $element Selector element.
     * @param {object} deferred Deferred object.
     *
     * @private
     */
    var _resolveHandler = function _resolveHandler($element, deferred) {
        _getFormData($element, true).done(function (result) {
            deferred.resolve(result);
            $element.dialog('close').remove();
        });
    };

    /**
     * Generate Buttons
     *
     * Transforms the custom buttons object (which is incompatible with jQuery UI)
     * to a jQuery UI compatible format and returns it.
     *
     * @param {object} dataset Custom buttons object for the dialog.
     * @param {object} deferred Deferred-object to resolve/reject on close.
     *
     * @return {array} Returns a jQuery UI dialog compatible buttons array.
     *
     * @private
     */
    var _generateButtons = function _generateButtons(dataset, deferred) {
        var newButtons = [],
            tmpButton = null;

        // Check if buttons are available.
        if (dataset) {
            $.each(dataset, function (k, v) {

                // Setup a new button.
                tmpButton = {};
                tmpButton.text = v.name || 'BUTTON';

                // Setup click handler.
                tmpButton.click = function () {
                    var $self = $(this);

                    // If a callback is given, execute it with the current scope.
                    if (typeof v.callback === 'function') {
                        v.callback.apply($self, []);
                    }

                    // Add the default behaviour for the close  functionality. On fail,
                    // reject the deferred object, else resolve it.
                    switch (v.type) {
                        case 'fail':
                            _rejectHandler($self, deferred);
                            break;
                        case 'success':
                            _resolveHandler($self, deferred);
                            break;
                        default:
                            break;
                    }
                };

                // Add to the new buttons array.
                newButtons.push(tmpButton);
            });
        }

        return newButtons;
    };

    /**
     * Get Template
     *
     * This method will return a promise object that can be used to execute code,
     * once the template HTML of the modal is found.
     *
     * @param {object} options Options to be applied to the template.
     *
     * @return {object} Returns a deferred object.
     *
     * @private
     */
    var _getTemplate = function _getTemplate(options) {
        var $selection = [],
            deferred = $.Deferred();

        try {
            $selection = $(options.template);
        } catch (exception) {
            jse.core.debug(jse.core.lang.templateNotFound(options.template));
        }

        if ($selection.length) {
            deferred.resolve($selection.html());
        } else {
            window.jse.lib.ajax({
                'url': options.template,
                'dataType': 'html'
            }).done(function (result) {
                if (options.storeTemplate) {
                    var $append = $('<div />').attr('id', options.template).html(result);
                    $('body').append($append);
                }
                deferred.resolve(result);
            }).fail(function () {
                deferred.reject();
            });
        }

        return deferred;
    };

    /**
     * Create Modal Layer
     *
     * @param {object} options Extra modal options to be applied.
     * @param {string} title Modal title
     * @param {string} className Class name to be added to the modal element.
     * @param {object} defaultButtons Modal buttons for the layer.
     * @param {string} template Template name to be used for the modal.
     *
     * @return {object} Returns a modal promise object.
     *
     * @private
     */
    var _createLayer = function _createLayer(options, title, className, defaultButtons, template) {
        // Setup defaults & deferred objects.
        var deferred = $.Deferred(),
            promise = deferred.promise(),
            $template = '',
            defaults = {
            'title': title || '',
            'dialogClass': className || '',
            'modal': true,
            'resizable': false,
            'buttons': defaultButtons || [buttons.close],
            'draggable': false,
            'closeOnEscape': false,
            'autoOpen': false,
            'template': template || '#modal_alert',
            'storeTemplate': false,
            'closeX': true,
            'modalClose': false
        },
            instance = null,
            $forms = null;

        // Merge custom settings with default settings
        options = options || {};
        options = $.extend({}, defaults, options);
        options.buttons = _generateButtons(options.buttons, deferred);

        _getTemplate(options).done(function (html) {
            // Generate template
            $template = $(Mustache.render(html, options));

            if (options.validator) {
                $template.find('form').attr('data-gx-widget', 'validator').find('input').attr({
                    'data-validator-validate': options.validator.validate,
                    'data-validator-regex': options.validator.regex || ''
                }).addClass('validate');
            }

            // Setup dialog
            $template.dialog(options);
            try {
                instance = $template.dialog('instance');
            } catch (exception) {
                instance = $template.data('ui-dialog');
            }

            // Add bootstrap button classes to buttonSet.
            instance.uiButtonSet.children().addClass('btn btn-default');

            // If the closeX-option is set to false, remove the button from the layout
            // else bind an event listener to reject the deferred object.
            if (options.closeX === false) {
                instance.uiDialogTitlebarClose.remove();
            } else {
                instance.uiDialogTitlebarClose.html('&times;').one('click', function () {
                    _rejectHandler(instance.element, deferred);
                });
            }

            // Add an event listener to the modal overlay if the option is set.
            if (options.modalClose) {
                $('body').find('.ui-widget-overlay').last().one('click', function () {
                    _rejectHandler(instance.element, deferred);
                });
            }

            // Prevent submit on enter in inner forms
            $forms = instance.element.find('form');
            if ($forms.length) {
                $forms.on('submit', function (event) {
                    event.preventDefault();
                });
            }

            if (options.executeCode && typeof options.executeCode === 'function') {
                options.executeCode.call($(instance.element));
            }

            // Add a close layer method to the promise.
            promise.close = function (fail) {
                if (fail) {
                    _rejectHandler(instance.element, deferred);
                } else {
                    _resolveHandler(instance.element, deferred);
                }
            };

            $template.dialog('open');
            if (window.gx && window.jse.widgets && window.jse.widgets.init) {
                window.jse.widgets.init($template);
                window.jse.controllers.init($template);
                window.jse.extensions.init($template);
            }
        }).fail(function () {
            deferred.reject({
                'error': 'Template not found'
            });
        });

        return promise;
    };

    /**
     * Create a warning log for the deprecated method.
     *
     * @param {String} method The method name to be included in the log.
     *
     * @private
     */
    function _logDeprecatedMethod(method) {
        jse.core.debug.warn('Used deprecated modal method ' + method + ' which will be removed in JSE v1.5.');
    }

    // ------------------------------------------------------------------------
    // PUBLIC FUNCTIONS
    // ------------------------------------------------------------------------

    /**
     * Generates the default alert layer.
     *
     * @param {object} options Mix of jQuery UI dialog options and custom options
     * @param {string} title Default title for the type of alert layer
     * @param {string} className Default class for the type of alert layer
     * @param {array} defbuttons Array wih the default buttons for the array type
     * @param {string} template Selector for the jQuery-object used as template
     *
     * @return {object} Returns a promise object.
     *
     * @deprecated This method will be removed with JSE v1.5.
     */
    exports.alert = function (options) {
        _logDeprecatedMethod('jse.libs.modal.alert()');

        var data = $.extend({}, {
            'draggable': true
        }, options);

        return _createLayer(data, jse.core.lang.translate('hint', 'labels'), '', [buttons.ok]);
    };

    /**
     * Returns a confirm layer.
     *
     * @param {object} options Mix of jQuery UI dialog options and custom options.
     *
     * @return {promise} Returns a promise
     *
     * @deprecated This method will be removed with JSE v1.5.
     */
    exports.confirm = function (options) {
        _logDeprecatedMethod('jse.libs.modal.confirm()');

        var data = $.extend({}, {
            'draggable': true
        }, options);

        return _createLayer(data, jse.core.lang.translate('confirm', 'labels'), 'confirm_dialog', [buttons.no, buttons.yes]);
    };

    /**
     * Returns a prompt layer.
     *
     * @param {object} options Mix of jQuery UI dialog options and custom options.
     *
     * @return {promise} Returns a promise object.
     *
     * @deprecated This method will be removed with JSE v1.5.
     */
    exports.prompt = function (options) {
        _logDeprecatedMethod('jse.libs.modal.prompt()');

        var data = $.extend({}, {
            'draggable': true
        }, options);

        return _createLayer(data, jse.core.lang.translate('prompt', 'labels'), 'prompt_dialog', [buttons.abort, buttons.ok], '#modal_prompt');
    };

    /**
     * Returns a success layer.
     *
     * @param {object} options Mix of jQuery UI dialog options and custom options.
     *
     * @return {object} Returns a promise object.
     *
     * @deprecated This method will be removed with JSE v1.5.
     */
    exports.success = function (options) {
        _logDeprecatedMethod('jse.libs.modal.success()');

        var data = $.extend({}, {
            'draggable': true
        }, options);

        return _createLayer(data, jse.core.lang.translate('success', 'labels'), 'success_dialog');
    };

    /**
     * Returns an error layer.
     *
     * @param {object} options Mix of jQuery UI dialog options and custom options.
     *
     * @return {object} Returns a promise object.
     *
     * @deprecated This method will be removed with JSE v1.5.
     */
    exports.error = function (options) {
        _logDeprecatedMethod('jse.libs.modal.error()');

        var data = $.extend({}, {
            'draggable': true
        }, options);

        return _createLayer(data, jse.core.lang.translate('error', 'labels'), 'error_dialog');
    };

    /**
     * Returns a warning layer.
     *
     * @param {object} options Mix of jQuery UI dialog options and custom options.
     *
     * @return {object} Returns a promise object.
     *
     * @deprecated This method will be removed with JSE v1.5.
     */
    exports.warn = function (options) {
        _logDeprecatedMethod('jse.libs.modal.warn()');

        var data = $.extend({}, {
            'draggable': true
        }, options);

        return _createLayer(data, jse.core.lang.translate('warning', 'labels'), 'warn_dialog');
    };

    /**
     * Returns an info layer.
     *
     * @param {object} options Mix of jQuery UI dialog options and custom options.
     *
     * @return {promise} Returns a promise object.
     *
     * @deprecated This method will be removed with JSE v1.5.
     */
    exports.info = function (options) {
        _logDeprecatedMethod('jse.libs.modal.info()');

        var data = $.extend({}, {
            'draggable': true
        }, options);

        return _createLayer(data, jse.core.lang.translate('info', 'labels'), 'info_dialog');
    };

    /**
     * Display jQuery UI message.
     *
     * This method provides an easy way to display a message to the user by using jQuery UI dialog widget.
     *
     * @param {Object} options Modal options are the same as the jQuery dialog widget.
     */
    exports.message = function (options) {
        // Create div element for modal dialog.
        $('body').append('<div class="modal-layer">' + options.content + '</div>');

        // Append options object with extra dialog options.
        options.modal = true;
        options.dialogClass = 'gx-container';

        // Set default buttons, if option wasn't provided.
        if (options.buttons === undefined) {
            options.buttons = [{
                text: buttons.close.name,
                click: function click() {
                    $(this).dialog('close');
                    $(this).remove();
                }
            }];
        }

        // Display message to the user.
        $('.modal-layer:last').dialog(options);
    };

    /**
     * Display Bootstrap modal message.
     *
     * {@link http://getbootstrap.com/javascript/#modals}
     *
     * Example:
     *
     * jse.libs.modal.showMessage('Title', 'Message', [
     *   {
     *     title: 'Send', // Button title 
     *     class: 'btn btn-primary send', // (optional) Add a custom button class. 
     *     callback: function(event) { ... } // (optional) Provide a click callback
     *   },
     *   {
     *     title: 'Close',
     *     closeModal: true // (optional)  Modal will be closed upon click.
     *   }
     * ]);
     *
     * You can close the modal by using the Bootstrap API: $modal.modal('hide');
     *
     * @param {String} title The message title.
     * @param {String} content The message content.
     * @param {Object[]} [buttons=null] Provide an array with objects which define the modal buttons.
     *
     * @return {jQuery} Returns the modal selector.
     */
    exports.showMessage = function (title, content) {
        var buttons = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;

        // Generate the default close button definition. 
        if (!buttons) {
            buttons = [{
                title: jse.core.lang.translate('CLOSE', 'general'),
                class: 'btn btn-default',
                callback: function callback(event) {
                    return $(event.currentTarget).parents('.modal').modal('hide');
                }
            }];
        }

        // Prepare the Bootstrap HTML markup. 
        var html = '<div class="modal fade" tabindex="-1" role="dialog">\n\t\t\t\t\t\t<div class="modal-dialog">\n\t\t\t\t\t\t\t<div class="modal-content">\n\t\t\t\t\t\t\t\t<div class="modal-header">\n\t\t\t\t\t\t\t\t\t<button type="button" class="close" data-dismiss="modal" aria-label="Close">\n\t\t\t\t\t\t\t\t\t\t<span aria-hidden="true">&times;</span>\n\t\t\t\t\t\t\t\t\t</button>\n\t\t\t\t\t\t\t\t\t<h4 class="modal-title">' + title + '</h4>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class="modal-body">\n\t\t\t\t\t                ' + content + '\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class="modal-footer"></div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>';

        var $modal = $(html).appendTo('body');

        // Add the buttons to the modal. 
        buttons.forEach(function (button) {
            var $button = $('<button/>');
            $button.text(button.title).attr('class', button.class || 'btn btn-default');

            if (button.callback) {
                $button.on('click', button.callback);
            }

            $button.appendTo($modal.find('.modal-footer'));
        });

        // Remove the modal element when its hidden. 
        $modal.on('hidden.bs.modal', function () {
            return $modal.remove();
        });

        // Display the modal to the user.
        $modal.modal('show');

        return $modal;
    };
})(jse.libs.modal);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm1vZGFsLmpzIl0sIm5hbWVzIjpbImpzZSIsImxpYnMiLCJtb2RhbCIsImV4cG9ydHMiLCJidXR0b25zIiwiY29yZSIsImxhbmciLCJ0cmFuc2xhdGUiLCJfZ2V0Rm9ybURhdGEiLCIkc2VsZiIsInZhbGlkYXRlRm9ybSIsIiRmb3JtcyIsImZpbHRlciIsImFkZCIsImZpbmQiLCJmb3JtRGF0YSIsInByb21pc2VzIiwibGVuZ3RoIiwiZWFjaCIsIiRmb3JtIiwiJCIsImxvY2FsRGVmZXJyZWQiLCJEZWZlcnJlZCIsInB1c2giLCJ0cmlnZ2VyIiwia2V5IiwiYXR0ciIsIkRhdGUiLCJnZXRUaW1lIiwiTWF0aCIsInJhbmRvbSIsIndpbmRvdyIsImxpYiIsImZvcm0iLCJnZXREYXRhIiwid2hlbiIsImFwcGx5IiwidW5kZWZpbmVkIiwidGhlbiIsInByb21pc2UiLCJfcmVqZWN0SGFuZGxlciIsIiRlbGVtZW50IiwiZGVmZXJyZWQiLCJhbHdheXMiLCJyZXN1bHQiLCJyZWplY3QiLCJkaWFsb2ciLCJyZW1vdmUiLCJfcmVzb2x2ZUhhbmRsZXIiLCJkb25lIiwicmVzb2x2ZSIsIl9nZW5lcmF0ZUJ1dHRvbnMiLCJkYXRhc2V0IiwibmV3QnV0dG9ucyIsInRtcEJ1dHRvbiIsImsiLCJ2IiwidGV4dCIsIm5hbWUiLCJjbGljayIsImNhbGxiYWNrIiwidHlwZSIsIl9nZXRUZW1wbGF0ZSIsIm9wdGlvbnMiLCIkc2VsZWN0aW9uIiwidGVtcGxhdGUiLCJleGNlcHRpb24iLCJkZWJ1ZyIsInRlbXBsYXRlTm90Rm91bmQiLCJodG1sIiwiYWpheCIsInN0b3JlVGVtcGxhdGUiLCIkYXBwZW5kIiwiYXBwZW5kIiwiZmFpbCIsIl9jcmVhdGVMYXllciIsInRpdGxlIiwiY2xhc3NOYW1lIiwiZGVmYXVsdEJ1dHRvbnMiLCIkdGVtcGxhdGUiLCJkZWZhdWx0cyIsImNsb3NlIiwiaW5zdGFuY2UiLCJleHRlbmQiLCJNdXN0YWNoZSIsInJlbmRlciIsInZhbGlkYXRvciIsInZhbGlkYXRlIiwicmVnZXgiLCJhZGRDbGFzcyIsImRhdGEiLCJ1aUJ1dHRvblNldCIsImNoaWxkcmVuIiwiY2xvc2VYIiwidWlEaWFsb2dUaXRsZWJhckNsb3NlIiwib25lIiwiZWxlbWVudCIsIm1vZGFsQ2xvc2UiLCJsYXN0Iiwib24iLCJldmVudCIsInByZXZlbnREZWZhdWx0IiwiZXhlY3V0ZUNvZGUiLCJjYWxsIiwiZ3giLCJ3aWRnZXRzIiwiaW5pdCIsImNvbnRyb2xsZXJzIiwiZXh0ZW5zaW9ucyIsIl9sb2dEZXByZWNhdGVkTWV0aG9kIiwibWV0aG9kIiwid2FybiIsImFsZXJ0Iiwib2siLCJjb25maXJtIiwibm8iLCJ5ZXMiLCJwcm9tcHQiLCJhYm9ydCIsInN1Y2Nlc3MiLCJlcnJvciIsImluZm8iLCJtZXNzYWdlIiwiY29udGVudCIsImRpYWxvZ0NsYXNzIiwic2hvd01lc3NhZ2UiLCJjbGFzcyIsImN1cnJlbnRUYXJnZXQiLCJwYXJlbnRzIiwiJG1vZGFsIiwiYXBwZW5kVG8iLCJmb3JFYWNoIiwiJGJ1dHRvbiIsImJ1dHRvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOztBQUVBQSxJQUFJQyxJQUFKLENBQVNDLEtBQVQsR0FBaUJGLElBQUlDLElBQUosQ0FBU0MsS0FBVCxJQUFrQixFQUFuQzs7QUFFQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUFzQ0MsV0FBVUMsT0FBVixFQUFtQjs7QUFFaEI7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7QUFLQSxRQUFNQyxVQUFVO0FBQ1osZUFBTztBQUNILG9CQUFRSixJQUFJSyxJQUFKLENBQVNDLElBQVQsQ0FBY0MsU0FBZCxDQUF3QixLQUF4QixFQUErQixTQUEvQixDQURMO0FBRUgsb0JBQVE7QUFGTCxTQURLO0FBS1osY0FBTTtBQUNGLG9CQUFRUCxJQUFJSyxJQUFKLENBQVNDLElBQVQsQ0FBY0MsU0FBZCxDQUF3QixJQUF4QixFQUE4QixTQUE5QixDQUROO0FBRUYsb0JBQVE7QUFGTixT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QzlILEtBQXpDLENBQStDLE1BQS9DLENBQVQ7QUFBQTtBQUhkLGFBRE0sQ0FBVjtBQU9IOztBQUVEO0FBQ0EsWUFBTW1FLHFhQU9xQk8sS0FQckIsMkdBVVMrQyxPQVZULDJJQUFOOztBQWlCQSxZQUFNTSxTQUFTN0csRUFBRWlELElBQUYsRUFBUTZELFFBQVIsQ0FBaUIsTUFBakIsQ0FBZjs7QUFFQTtBQUNBOUgsZ0JBQVErSCxPQUFSLENBQWdCLGtCQUFVO0FBQ3RCLGdCQUFNQyxVQUFVaEgsRUFBRSxXQUFGLENBQWhCO0FBQ0FnSCxvQkFDSzNFLElBREwsQ0FDVTRFLE9BQU96RCxLQURqQixFQUVLbEQsSUFGTCxDQUVVLE9BRlYsRUFFbUIyRyxPQUFPUCxLQUFQLElBQWdCLGlCQUZuQzs7QUFJQSxnQkFBSU8sT0FBT3pFLFFBQVgsRUFBcUI7QUFDakJ3RSx3QkFBUWpDLEVBQVIsQ0FBVyxPQUFYLEVBQW9Ca0MsT0FBT3pFLFFBQTNCO0FBQ0g7O0FBRUR3RSxvQkFBUUYsUUFBUixDQUFpQkQsT0FBT25ILElBQVAsQ0FBWSxlQUFaLENBQWpCO0FBQ0gsU0FYRDs7QUFhQTtBQUNBbUgsZUFBTzlCLEVBQVAsQ0FBVSxpQkFBVixFQUE2QjtBQUFBLG1CQUFNOEIsT0FBT2xGLE1BQVAsRUFBTjtBQUFBLFNBQTdCOztBQUVBO0FBQ0FrRixlQUFPL0gsS0FBUCxDQUFhLE1BQWI7O0FBRUEsZUFBTytILE1BQVA7QUFDSCxLQXJERDtBQXVESCxDQTltQkEsRUE4bUJDakksSUFBSUMsSUFBSixDQUFTQyxLQTltQlYsQ0FBRCIsImZpbGUiOiJtb2RhbC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gbW9kYWwuanMgMjAxNi0wMi0yM1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qIGdsb2JhbHMgTXVzdGFjaGUgKi9cblxuanNlLmxpYnMubW9kYWwgPSBqc2UubGlicy5tb2RhbCB8fCB7fTtcblxuLyoqXG4gKiAjIyBNb2RhbCBEaWFsb2dzIExpYnJhcnlcbiAqXG4gKiBUaGlzIGxpYnJhcnkgaGFuZGxlcyBqUXVlcnkgVUkgYW5kIEJvb3RzdHJhcCBtb2RhbHMgYW5kIGl0IGlzIHF1aXRlIHVzZWZ1bCB3aGVuIGl0IGNvbWVzIHRvIGRpc3BsYXlcbiAqIHBsYWluIG1lc3NhZ2VzLiBNYWtlIHN1cmUgdG8gdXNlIHRoZSBcInNob3dNZXNzYWdlXCIgZnVuY3Rpb24gb25seSBpbiBwYWdlcyB3aGVyZSBCb290c3RyYXAgaXMgbG9hZGVkLlxuICpcbiAqIE5vdGljZTogU29tZSBsaWJyYXJ5IG1ldGhvZHMgYXJlIGRlcHJlY2F0ZWQgYW5kIHdpbGwgYmUgcmVtb3ZlZCB3aXRoIEpTRSB2MS41LlxuICpcbiAqIE5vdGljZTogTWFrZSBzdXJlIHRoYXQgeW91IGxvYWQgdGhlIHJlcXVpcmUgdmVuZG9yIGZpbGVzIChCb290c3RyYXAgb3IgalF1ZXJ5IFVJKSBiZWZvcmUgdXNpbmcgdGhpcyBtb2R1bGUuXG4gKlxuICogIyMjIEV4YW1wbGVzXG4gKlxuICogKipEaXNwbGF5IGpRdWVyeSBVSSBtZXNzYWdlLioqXG4gKlxuICogYGBgamF2YXNjcmlwdFxuICoganNlLmxpYnMubW9kYWwubWVzc2FnZSh7XG4gKiAgICAgIHRpdGxlOiAnTXkgVGl0bGUnLCAgICAgIC8vIFJlcXVpcmVkXG4gKiAgICAgIGNvbnRlbnQ6ICdNeSBDb250ZW50JyAgIC8vIFJlcXVpcmVkXG4gKiAgICAgIGJ1dHRvbnM6IHsgLi4uIH0gICAgICAgIC8vIE9wdGlvbmFsXG4gKiAgICAgIC8vIE90aGVyIGpRdWVyeVVJIERpYWxvZyBXaWRnZXQgT3B0aW9uc1xuICogfSk7XG4gKiBgYGBcbiAqXG4gKiAqKkRpc3BsYXkgQm9vdHN0cmFwIG1lc3NhZ2UuKipcbiAqXG4gKiBgYGBqYXZhc2NyaXB0XG4gKiBqc2UubGlicy5tb2RhbC5zaG93TWVzc2FnZSgnVGl0bGUnLCAnQ29udGVudCcpO1xuICogYGBgXG4gKlxuICogQHRvZG8gUmVmYWN0b3IgbW9kYWwgZnVuY3Rpb25hbGl0eSwgcmVtb3ZlIE11c3RhY2hlIGRlcGVuZGVuY3kgYW5kIHNwbGl0IGpRdWVyeSBVSSBhbmQgQm9vdHN0cmFwIHVzZS5cbiAqXG4gKiBAbW9kdWxlIEpTRS9MaWJzL21vZGFsXG4gKiBAZXhwb3J0cyBqc2UubGlicy5tb2RhbFxuICpcbiAqIEByZXF1aXJlcyBqUXVlcnlVSVxuICogQHJlcXVpcmVzIEJvb3RzdHJhcFxuICogQHJlcXVpcmVzIE11c3RhY2hlXG4gKi9cbihmdW5jdGlvbiAoZXhwb3J0cykge1xuXG4gICAgJ3VzZSBzdHJpY3QnO1xuXG4gICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gICAgLy8gVkFSSUFCTEVTXG4gICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cbiAgICAvKipcbiAgICAgKiBDb250YWlucyBEZWZhdWx0IE1vZGFsIEJ1dHRvbnNcbiAgICAgKlxuICAgICAqIEB0eXBlIHtPYmplY3R9XG4gICAgICovXG4gICAgY29uc3QgYnV0dG9ucyA9IHtcbiAgICAgICAgJ3llcyc6IHtcbiAgICAgICAgICAgICduYW1lJzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3llcycsICdidXR0b25zJyksXG4gICAgICAgICAgICAndHlwZSc6ICdzdWNjZXNzJ1xuICAgICAgICB9LFxuICAgICAgICAnbm8nOiB7XG4gICAgICAgICAgICAnbmFtZSc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdubycsICdidXR0b25zJyksXG4gICAgICAgICAgICAndHlwZSc6ICdmYWlsJ1xuICAgICAgICB9LFxuICAgICAgICAnYWJvcnQnOiB7XG4gICAgICAgICAgICAnbmFtZSc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdhYm9ydCcsICdidXR0b25zJyksXG4gICAgICAgICAgICAndHlwZSc6ICdmYWlsJ1xuICAgICAgICB9LFxuICAgICAgICAnb2snOiB7XG4gICAgICAgICAgICAnbmFtZSc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdvaycsICdidXR0b25zJyksXG4gICAgICAgICAgICAndHlwZSc6ICdzdWNjZXNzJ1xuICAgICAgICB9LFxuICAgICAgICAnY2xvc2UnOiB7XG4gICAgICAgICAgICAnbmFtZSc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdjbG9zZScsICdidXR0b25zJyksXG4gICAgICAgICAgICAndHlwZSc6ICdmYWlsJ1xuICAgICAgICB9XG4gICAgfTtcblxuICAgIC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICAgIC8vIFBSSVZBVEUgRlVOQ1RJT05TXG4gICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cbiAgICAvKipcbiAgICAgKiBHZXQgRm9ybSBEYXRhXG4gICAgICpcbiAgICAgKiBSZXR1cm5zIGFsbCBmb3JtIGRhdGEsIHdoaWNoIGlzIHN0b3JlZCBpbnNpZGUgdGhlIGxheWVyLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtvYmplY3R9ICRzZWxmIGpRdWVyeSBzZWxlY3RvciBvZiB0aGUgbGF5ZXIuXG4gICAgICogQHBhcmFtIHtib29sfSB2YWxpZGF0ZUZvcm0gRmxhZyB0aGF0IGRldGVybWluZXMgd2hldGhlciB0aGUgZm9ybSBtdXN0IGJlIHZhbGlkYXRlZFxuICAgICAqIGJlZm9yZSB3ZSBnZXQgdGhlIGRhdGEuXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIHtqc29ufSBSZXR1cm5zIGEgSlNPTiB3aXRoIGFsbCBmb3JtIGRhdGEuXG4gICAgICpcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIHZhciBfZ2V0Rm9ybURhdGEgPSBmdW5jdGlvbiAoJHNlbGYsIHZhbGlkYXRlRm9ybSkge1xuICAgICAgICB2YXIgJGZvcm1zID0gJHNlbGZcbiAgICAgICAgICAgICAgICAuZmlsdGVyKCdmb3JtJylcbiAgICAgICAgICAgICAgICAuYWRkKCRzZWxmLmZpbmQoJ2Zvcm0nKSksXG4gICAgICAgICAgICBmb3JtRGF0YSA9IHt9LFxuICAgICAgICAgICAgcHJvbWlzZXMgPSBbXTtcblxuICAgICAgICBpZiAoJGZvcm1zLmxlbmd0aCkge1xuICAgICAgICAgICAgJGZvcm1zLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIHZhciAkZm9ybSA9ICQodGhpcyk7XG5cbiAgICAgICAgICAgICAgICBpZiAodmFsaWRhdGVGb3JtKSB7XG4gICAgICAgICAgICAgICAgICAgIHZhciBsb2NhbERlZmVycmVkID0gJC5EZWZlcnJlZCgpO1xuICAgICAgICAgICAgICAgICAgICBwcm9taXNlcy5wdXNoKGxvY2FsRGVmZXJyZWQpO1xuICAgICAgICAgICAgICAgICAgICAkZm9ybS50cmlnZ2VyKCd2YWxpZGF0b3IudmFsaWRhdGUnLCB7XG4gICAgICAgICAgICAgICAgICAgICAgICAnZGVmZXJyZWQnOiBsb2NhbERlZmVycmVkXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHZhciBrZXkgPSAkZm9ybS5hdHRyKCduYW1lJykgfHwgJGZvcm0uYXR0cignaWQnKSB8fCAoJ2Zvcm1fJyArIG5ldyBEYXRlKCkuZ2V0VGltZSgpICogTWF0aC5yYW5kb20oKSk7XG4gICAgICAgICAgICAgICAgZm9ybURhdGFba2V5XSA9IHdpbmRvdy5qc2UubGliLmZvcm0uZ2V0RGF0YSgkZm9ybSk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiAkLndoZW5cbiAgICAgICAgICAgIC5hcHBseSh1bmRlZmluZWQsIHByb21pc2VzKVxuICAgICAgICAgICAgLnRoZW4oZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gZm9ybURhdGE7XG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBmb3JtRGF0YTtcbiAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgLnByb21pc2UoKTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogUmVqZWN0IEhhbmRsZXJcbiAgICAgKlxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSAkZWxlbWVudCBTZWxlY3RvciBlbGVtZW50LlxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBkZWZlcnJlZCBEZWZlcnJlZCBvYmplY3QuXG4gICAgICpcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIHZhciBfcmVqZWN0SGFuZGxlciA9IGZ1bmN0aW9uICgkZWxlbWVudCwgZGVmZXJyZWQpIHtcbiAgICAgICAgX2dldEZvcm1EYXRhKCRlbGVtZW50KS5hbHdheXMoZnVuY3Rpb24gKHJlc3VsdCkge1xuICAgICAgICAgICAgZGVmZXJyZWQucmVqZWN0KHJlc3VsdCk7XG4gICAgICAgICAgICAkZWxlbWVudFxuICAgICAgICAgICAgICAgIC5kaWFsb2coJ2Nsb3NlJylcbiAgICAgICAgICAgICAgICAucmVtb3ZlKCk7XG4gICAgICAgIH0pO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBSZXNvbHZlIEhhbmRsZXJcbiAgICAgKlxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSAkZWxlbWVudCBTZWxlY3RvciBlbGVtZW50LlxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBkZWZlcnJlZCBEZWZlcnJlZCBvYmplY3QuXG4gICAgICpcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIHZhciBfcmVzb2x2ZUhhbmRsZXIgPSBmdW5jdGlvbiAoJGVsZW1lbnQsIGRlZmVycmVkKSB7XG4gICAgICAgIF9nZXRGb3JtRGF0YSgkZWxlbWVudCwgdHJ1ZSkuZG9uZShmdW5jdGlvbiAocmVzdWx0KSB7XG4gICAgICAgICAgICBkZWZlcnJlZC5yZXNvbHZlKHJlc3VsdCk7XG4gICAgICAgICAgICAkZWxlbWVudFxuICAgICAgICAgICAgICAgIC5kaWFsb2coJ2Nsb3NlJylcbiAgICAgICAgICAgICAgICAucmVtb3ZlKCk7XG4gICAgICAgIH0pO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBHZW5lcmF0ZSBCdXR0b25zXG4gICAgICpcbiAgICAgKiBUcmFuc2Zvcm1zIHRoZSBjdXN0b20gYnV0dG9ucyBvYmplY3QgKHdoaWNoIGlzIGluY29tcGF0aWJsZSB3aXRoIGpRdWVyeSBVSSlcbiAgICAgKiB0byBhIGpRdWVyeSBVSSBjb21wYXRpYmxlIGZvcm1hdCBhbmQgcmV0dXJucyBpdC5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBkYXRhc2V0IEN1c3RvbSBidXR0b25zIG9iamVjdCBmb3IgdGhlIGRpYWxvZy5cbiAgICAgKiBAcGFyYW0ge29iamVjdH0gZGVmZXJyZWQgRGVmZXJyZWQtb2JqZWN0IHRvIHJlc29sdmUvcmVqZWN0IG9uIGNsb3NlLlxuICAgICAqXG4gICAgICogQHJldHVybiB7YXJyYXl9IFJldHVybnMgYSBqUXVlcnkgVUkgZGlhbG9nIGNvbXBhdGlibGUgYnV0dG9ucyBhcnJheS5cbiAgICAgKlxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgdmFyIF9nZW5lcmF0ZUJ1dHRvbnMgPSBmdW5jdGlvbiAoZGF0YXNldCwgZGVmZXJyZWQpIHtcbiAgICAgICAgdmFyIG5ld0J1dHRvbnMgPSBbXSxcbiAgICAgICAgICAgIHRtcEJ1dHRvbiA9IG51bGw7XG5cbiAgICAgICAgLy8gQ2hlY2sgaWYgYnV0dG9ucyBhcmUgYXZhaWxhYmxlLlxuICAgICAgICBpZiAoZGF0YXNldCkge1xuICAgICAgICAgICAgJC5lYWNoKGRhdGFzZXQsIGZ1bmN0aW9uIChrLCB2KSB7XG5cbiAgICAgICAgICAgICAgICAvLyBTZXR1cCBhIG5ldyBidXR0b24uXG4gICAgICAgICAgICAgICAgdG1wQnV0dG9uID0ge307XG4gICAgICAgICAgICAgICAgdG1wQnV0dG9uLnRleHQgPSB2Lm5hbWUgfHwgJ0JVVFRPTic7XG5cbiAgICAgICAgICAgICAgICAvLyBTZXR1cCBjbGljayBoYW5kbGVyLlxuICAgICAgICAgICAgICAgIHRtcEJ1dHRvbi5jbGljayA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyICRzZWxmID0gJCh0aGlzKTtcblxuICAgICAgICAgICAgICAgICAgICAvLyBJZiBhIGNhbGxiYWNrIGlzIGdpdmVuLCBleGVjdXRlIGl0IHdpdGggdGhlIGN1cnJlbnQgc2NvcGUuXG4gICAgICAgICAgICAgICAgICAgIGlmICh0eXBlb2Ygdi5jYWxsYmFjayA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgdi5jYWxsYmFjay5hcHBseSgkc2VsZiwgW10pO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgLy8gQWRkIHRoZSBkZWZhdWx0IGJlaGF2aW91ciBmb3IgdGhlIGNsb3NlICBmdW5jdGlvbmFsaXR5LiBPbiBmYWlsLFxuICAgICAgICAgICAgICAgICAgICAvLyByZWplY3QgdGhlIGRlZmVycmVkIG9iamVjdCwgZWxzZSByZXNvbHZlIGl0LlxuICAgICAgICAgICAgICAgICAgICBzd2l0Y2ggKHYudHlwZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgY2FzZSAnZmFpbCc6XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgX3JlamVjdEhhbmRsZXIoJHNlbGYsIGRlZmVycmVkKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICAgICAgICAgIGNhc2UgJ3N1Y2Nlc3MnOlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIF9yZXNvbHZlSGFuZGxlcigkc2VsZiwgZGVmZXJyZWQpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgICAgICAgICAgZGVmYXVsdDpcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgICAgICAvLyBBZGQgdG8gdGhlIG5ldyBidXR0b25zIGFycmF5LlxuICAgICAgICAgICAgICAgIG5ld0J1dHRvbnMucHVzaCh0bXBCdXR0b24pO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBuZXdCdXR0b25zO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBHZXQgVGVtcGxhdGVcbiAgICAgKlxuICAgICAqIFRoaXMgbWV0aG9kIHdpbGwgcmV0dXJuIGEgcHJvbWlzZSBvYmplY3QgdGhhdCBjYW4gYmUgdXNlZCB0byBleGVjdXRlIGNvZGUsXG4gICAgICogb25jZSB0aGUgdGVtcGxhdGUgSFRNTCBvZiB0aGUgbW9kYWwgaXMgZm91bmQuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge29iamVjdH0gb3B0aW9ucyBPcHRpb25zIHRvIGJlIGFwcGxpZWQgdG8gdGhlIHRlbXBsYXRlLlxuICAgICAqXG4gICAgICogQHJldHVybiB7b2JqZWN0fSBSZXR1cm5zIGEgZGVmZXJyZWQgb2JqZWN0LlxuICAgICAqXG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICB2YXIgX2dldFRlbXBsYXRlID0gZnVuY3Rpb24gKG9wdGlvbnMpIHtcbiAgICAgICAgdmFyICRzZWxlY3Rpb24gPSBbXSxcbiAgICAgICAgICAgIGRlZmVycmVkID0gJC5EZWZlcnJlZCgpO1xuXG4gICAgICAgIHRyeSB7XG4gICAgICAgICAgICAkc2VsZWN0aW9uID0gJChvcHRpb25zLnRlbXBsYXRlKTtcbiAgICAgICAgfSBjYXRjaCAoZXhjZXB0aW9uKSB7XG4gICAgICAgICAgICBqc2UuY29yZS5kZWJ1Zyhqc2UuY29yZS5sYW5nLnRlbXBsYXRlTm90Rm91bmQob3B0aW9ucy50ZW1wbGF0ZSkpO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKCRzZWxlY3Rpb24ubGVuZ3RoKSB7XG4gICAgICAgICAgICBkZWZlcnJlZC5yZXNvbHZlKCRzZWxlY3Rpb24uaHRtbCgpKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIHdpbmRvdy5qc2UubGliLmFqYXgoe1xuICAgICAgICAgICAgICAgICd1cmwnOiBvcHRpb25zLnRlbXBsYXRlLFxuICAgICAgICAgICAgICAgICdkYXRhVHlwZSc6ICdodG1sJ1xuICAgICAgICAgICAgfSkuZG9uZShmdW5jdGlvbiAocmVzdWx0KSB7XG4gICAgICAgICAgICAgICAgaWYgKG9wdGlvbnMuc3RvcmVUZW1wbGF0ZSkge1xuICAgICAgICAgICAgICAgICAgICB2YXIgJGFwcGVuZCA9ICQoJzxkaXYgLz4nKVxuICAgICAgICAgICAgICAgICAgICAgICAgLmF0dHIoJ2lkJywgb3B0aW9ucy50ZW1wbGF0ZSlcbiAgICAgICAgICAgICAgICAgICAgICAgIC5odG1sKHJlc3VsdCk7XG4gICAgICAgICAgICAgICAgICAgICQoJ2JvZHknKS5hcHBlbmQoJGFwcGVuZCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGRlZmVycmVkLnJlc29sdmUocmVzdWx0KTtcbiAgICAgICAgICAgIH0pLmZhaWwoZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIGRlZmVycmVkLnJlamVjdCgpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gZGVmZXJyZWQ7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIENyZWF0ZSBNb2RhbCBMYXllclxuICAgICAqXG4gICAgICogQHBhcmFtIHtvYmplY3R9IG9wdGlvbnMgRXh0cmEgbW9kYWwgb3B0aW9ucyB0byBiZSBhcHBsaWVkLlxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSB0aXRsZSBNb2RhbCB0aXRsZVxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSBjbGFzc05hbWUgQ2xhc3MgbmFtZSB0byBiZSBhZGRlZCB0byB0aGUgbW9kYWwgZWxlbWVudC5cbiAgICAgKiBAcGFyYW0ge29iamVjdH0gZGVmYXVsdEJ1dHRvbnMgTW9kYWwgYnV0dG9ucyBmb3IgdGhlIGxheWVyLlxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSB0ZW1wbGF0ZSBUZW1wbGF0ZSBuYW1lIHRvIGJlIHVzZWQgZm9yIHRoZSBtb2RhbC5cbiAgICAgKlxuICAgICAqIEByZXR1cm4ge29iamVjdH0gUmV0dXJucyBhIG1vZGFsIHByb21pc2Ugb2JqZWN0LlxuICAgICAqXG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICB2YXIgX2NyZWF0ZUxheWVyID0gZnVuY3Rpb24gKG9wdGlvbnMsIHRpdGxlLCBjbGFzc05hbWUsIGRlZmF1bHRCdXR0b25zLCB0ZW1wbGF0ZSkge1xuICAgICAgICAvLyBTZXR1cCBkZWZhdWx0cyAmIGRlZmVycmVkIG9iamVjdHMuXG4gICAgICAgIHZhciBkZWZlcnJlZCA9ICQuRGVmZXJyZWQoKSxcbiAgICAgICAgICAgIHByb21pc2UgPSBkZWZlcnJlZC5wcm9taXNlKCksXG4gICAgICAgICAgICAkdGVtcGxhdGUgPSAnJyxcbiAgICAgICAgICAgIGRlZmF1bHRzID0ge1xuICAgICAgICAgICAgICAgICd0aXRsZSc6IHRpdGxlIHx8ICcnLFxuICAgICAgICAgICAgICAgICdkaWFsb2dDbGFzcyc6IGNsYXNzTmFtZSB8fCAnJyxcbiAgICAgICAgICAgICAgICAnbW9kYWwnOiB0cnVlLFxuICAgICAgICAgICAgICAgICdyZXNpemFibGUnOiBmYWxzZSxcbiAgICAgICAgICAgICAgICAnYnV0dG9ucyc6IGRlZmF1bHRCdXR0b25zIHx8IFtidXR0b25zLmNsb3NlXSxcbiAgICAgICAgICAgICAgICAnZHJhZ2dhYmxlJzogZmFsc2UsXG4gICAgICAgICAgICAgICAgJ2Nsb3NlT25Fc2NhcGUnOiBmYWxzZSxcbiAgICAgICAgICAgICAgICAnYXV0b09wZW4nOiBmYWxzZSxcbiAgICAgICAgICAgICAgICAndGVtcGxhdGUnOiB0ZW1wbGF0ZSB8fCAnI21vZGFsX2FsZXJ0JyxcbiAgICAgICAgICAgICAgICAnc3RvcmVUZW1wbGF0ZSc6IGZhbHNlLFxuICAgICAgICAgICAgICAgICdjbG9zZVgnOiB0cnVlLFxuICAgICAgICAgICAgICAgICdtb2RhbENsb3NlJzogZmFsc2VcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBpbnN0YW5jZSA9IG51bGwsXG4gICAgICAgICAgICAkZm9ybXMgPSBudWxsO1xuXG4gICAgICAgIC8vIE1lcmdlIGN1c3RvbSBzZXR0aW5ncyB3aXRoIGRlZmF1bHQgc2V0dGluZ3NcbiAgICAgICAgb3B0aW9ucyA9IG9wdGlvbnMgfHwge307XG4gICAgICAgIG9wdGlvbnMgPSAkLmV4dGVuZCh7fSwgZGVmYXVsdHMsIG9wdGlvbnMpO1xuICAgICAgICBvcHRpb25zLmJ1dHRvbnMgPSBfZ2VuZXJhdGVCdXR0b25zKG9wdGlvbnMuYnV0dG9ucywgZGVmZXJyZWQpO1xuXG4gICAgICAgIF9nZXRUZW1wbGF0ZShvcHRpb25zKS5kb25lKGZ1bmN0aW9uIChodG1sKSB7XG4gICAgICAgICAgICAvLyBHZW5lcmF0ZSB0ZW1wbGF0ZVxuICAgICAgICAgICAgJHRlbXBsYXRlID0gJChNdXN0YWNoZS5yZW5kZXIoaHRtbCwgb3B0aW9ucykpO1xuXG4gICAgICAgICAgICBpZiAob3B0aW9ucy52YWxpZGF0b3IpIHtcbiAgICAgICAgICAgICAgICAkdGVtcGxhdGVcbiAgICAgICAgICAgICAgICAgICAgLmZpbmQoJ2Zvcm0nKVxuICAgICAgICAgICAgICAgICAgICAuYXR0cignZGF0YS1neC13aWRnZXQnLCAndmFsaWRhdG9yJylcbiAgICAgICAgICAgICAgICAgICAgLmZpbmQoJ2lucHV0JylcbiAgICAgICAgICAgICAgICAgICAgLmF0dHIoe1xuICAgICAgICAgICAgICAgICAgICAgICAgJ2RhdGEtdmFsaWRhdG9yLXZhbGlkYXRlJzogb3B0aW9ucy52YWxpZGF0b3IudmFsaWRhdGUsXG4gICAgICAgICAgICAgICAgICAgICAgICAnZGF0YS12YWxpZGF0b3ItcmVnZXgnOiBvcHRpb25zLnZhbGlkYXRvci5yZWdleCB8fCAnJ1xuICAgICAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoJ3ZhbGlkYXRlJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vIFNldHVwIGRpYWxvZ1xuICAgICAgICAgICAgJHRlbXBsYXRlLmRpYWxvZyhvcHRpb25zKTtcbiAgICAgICAgICAgIHRyeSB7XG4gICAgICAgICAgICAgICAgaW5zdGFuY2UgPSAkdGVtcGxhdGUuZGlhbG9nKCdpbnN0YW5jZScpO1xuICAgICAgICAgICAgfSBjYXRjaCAoZXhjZXB0aW9uKSB7XG4gICAgICAgICAgICAgICAgaW5zdGFuY2UgPSAkdGVtcGxhdGUuZGF0YSgndWktZGlhbG9nJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vIEFkZCBib290c3RyYXAgYnV0dG9uIGNsYXNzZXMgdG8gYnV0dG9uU2V0LlxuICAgICAgICAgICAgaW5zdGFuY2VcbiAgICAgICAgICAgICAgICAudWlCdXR0b25TZXRcbiAgICAgICAgICAgICAgICAuY2hpbGRyZW4oKVxuICAgICAgICAgICAgICAgIC5hZGRDbGFzcygnYnRuIGJ0bi1kZWZhdWx0Jyk7XG5cbiAgICAgICAgICAgIC8vIElmIHRoZSBjbG9zZVgtb3B0aW9uIGlzIHNldCB0byBmYWxzZSwgcmVtb3ZlIHRoZSBidXR0b24gZnJvbSB0aGUgbGF5b3V0XG4gICAgICAgICAgICAvLyBlbHNlIGJpbmQgYW4gZXZlbnQgbGlzdGVuZXIgdG8gcmVqZWN0IHRoZSBkZWZlcnJlZCBvYmplY3QuXG4gICAgICAgICAgICBpZiAob3B0aW9ucy5jbG9zZVggPT09IGZhbHNlKSB7XG4gICAgICAgICAgICAgICAgaW5zdGFuY2VcbiAgICAgICAgICAgICAgICAgICAgLnVpRGlhbG9nVGl0bGViYXJDbG9zZVxuICAgICAgICAgICAgICAgICAgICAucmVtb3ZlKCk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIGluc3RhbmNlXG4gICAgICAgICAgICAgICAgICAgIC51aURpYWxvZ1RpdGxlYmFyQ2xvc2VcbiAgICAgICAgICAgICAgICAgICAgLmh0bWwoJyZ0aW1lczsnKVxuICAgICAgICAgICAgICAgICAgICAub25lKCdjbGljaycsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIF9yZWplY3RIYW5kbGVyKGluc3RhbmNlLmVsZW1lbnQsIGRlZmVycmVkKTtcbiAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vIEFkZCBhbiBldmVudCBsaXN0ZW5lciB0byB0aGUgbW9kYWwgb3ZlcmxheSBpZiB0aGUgb3B0aW9uIGlzIHNldC5cbiAgICAgICAgICAgIGlmIChvcHRpb25zLm1vZGFsQ2xvc2UpIHtcbiAgICAgICAgICAgICAgICAkKCdib2R5JylcbiAgICAgICAgICAgICAgICAgICAgLmZpbmQoJy51aS13aWRnZXQtb3ZlcmxheScpXG4gICAgICAgICAgICAgICAgICAgIC5sYXN0KClcbiAgICAgICAgICAgICAgICAgICAgLm9uZSgnY2xpY2snLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBfcmVqZWN0SGFuZGxlcihpbnN0YW5jZS5lbGVtZW50LCBkZWZlcnJlZCk7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBQcmV2ZW50IHN1Ym1pdCBvbiBlbnRlciBpbiBpbm5lciBmb3Jtc1xuICAgICAgICAgICAgJGZvcm1zID0gaW5zdGFuY2UuZWxlbWVudC5maW5kKCdmb3JtJyk7XG4gICAgICAgICAgICBpZiAoJGZvcm1zLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgICRmb3Jtcy5vbignc3VibWl0JywgZnVuY3Rpb24gKGV2ZW50KSB7XG4gICAgICAgICAgICAgICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmIChvcHRpb25zLmV4ZWN1dGVDb2RlICYmIHR5cGVvZiBvcHRpb25zLmV4ZWN1dGVDb2RlID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgICAgICAgICAgb3B0aW9ucy5leGVjdXRlQ29kZS5jYWxsKCQoaW5zdGFuY2UuZWxlbWVudCkpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBBZGQgYSBjbG9zZSBsYXllciBtZXRob2QgdG8gdGhlIHByb21pc2UuXG4gICAgICAgICAgICBwcm9taXNlLmNsb3NlID0gZnVuY3Rpb24gKGZhaWwpIHtcbiAgICAgICAgICAgICAgICBpZiAoZmFpbCkge1xuICAgICAgICAgICAgICAgICAgICBfcmVqZWN0SGFuZGxlcihpbnN0YW5jZS5lbGVtZW50LCBkZWZlcnJlZCk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgX3Jlc29sdmVIYW5kbGVyKGluc3RhbmNlLmVsZW1lbnQsIGRlZmVycmVkKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAkdGVtcGxhdGUuZGlhbG9nKCdvcGVuJyk7XG4gICAgICAgICAgICBpZiAod2luZG93Lmd4ICYmIHdpbmRvdy5qc2Uud2lkZ2V0cyAmJiB3aW5kb3cuanNlLndpZGdldHMuaW5pdCkge1xuICAgICAgICAgICAgICAgIHdpbmRvdy5qc2Uud2lkZ2V0cy5pbml0KCR0ZW1wbGF0ZSk7XG4gICAgICAgICAgICAgICAgd2luZG93LmpzZS5jb250cm9sbGVycy5pbml0KCR0ZW1wbGF0ZSk7XG4gICAgICAgICAgICAgICAgd2luZG93LmpzZS5leHRlbnNpb25zLmluaXQoJHRlbXBsYXRlKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSkuZmFpbChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICBkZWZlcnJlZC5yZWplY3Qoe1xuICAgICAgICAgICAgICAgICdlcnJvcic6ICdUZW1wbGF0ZSBub3QgZm91bmQnXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgcmV0dXJuIHByb21pc2U7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIENyZWF0ZSBhIHdhcm5pbmcgbG9nIGZvciB0aGUgZGVwcmVjYXRlZCBtZXRob2QuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gbWV0aG9kIFRoZSBtZXRob2QgbmFtZSB0byBiZSBpbmNsdWRlZCBpbiB0aGUgbG9nLlxuICAgICAqXG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBmdW5jdGlvbiBfbG9nRGVwcmVjYXRlZE1ldGhvZChtZXRob2QpIHtcbiAgICAgICAganNlLmNvcmUuZGVidWcud2FybihgVXNlZCBkZXByZWNhdGVkIG1vZGFsIG1ldGhvZCAke21ldGhvZH0gd2hpY2ggd2lsbCBiZSByZW1vdmVkIGluIEpTRSB2MS41LmApO1xuICAgIH1cblxuICAgIC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICAgIC8vIFBVQkxJQyBGVU5DVElPTlNcbiAgICAvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblxuICAgIC8qKlxuICAgICAqIEdlbmVyYXRlcyB0aGUgZGVmYXVsdCBhbGVydCBsYXllci5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBvcHRpb25zIE1peCBvZiBqUXVlcnkgVUkgZGlhbG9nIG9wdGlvbnMgYW5kIGN1c3RvbSBvcHRpb25zXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IHRpdGxlIERlZmF1bHQgdGl0bGUgZm9yIHRoZSB0eXBlIG9mIGFsZXJ0IGxheWVyXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IGNsYXNzTmFtZSBEZWZhdWx0IGNsYXNzIGZvciB0aGUgdHlwZSBvZiBhbGVydCBsYXllclxuICAgICAqIEBwYXJhbSB7YXJyYXl9IGRlZmJ1dHRvbnMgQXJyYXkgd2loIHRoZSBkZWZhdWx0IGJ1dHRvbnMgZm9yIHRoZSBhcnJheSB0eXBlXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IHRlbXBsYXRlIFNlbGVjdG9yIGZvciB0aGUgalF1ZXJ5LW9iamVjdCB1c2VkIGFzIHRlbXBsYXRlXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIHtvYmplY3R9IFJldHVybnMgYSBwcm9taXNlIG9iamVjdC5cbiAgICAgKlxuICAgICAqIEBkZXByZWNhdGVkIFRoaXMgbWV0aG9kIHdpbGwgYmUgcmVtb3ZlZCB3aXRoIEpTRSB2MS41LlxuICAgICAqL1xuICAgIGV4cG9ydHMuYWxlcnQgPSBmdW5jdGlvbiAob3B0aW9ucykge1xuICAgICAgICBfbG9nRGVwcmVjYXRlZE1ldGhvZCgnanNlLmxpYnMubW9kYWwuYWxlcnQoKScpO1xuXG4gICAgICAgIHZhciBkYXRhID0gJC5leHRlbmQoe30sIHtcbiAgICAgICAgICAgICdkcmFnZ2FibGUnOiB0cnVlXG4gICAgICAgIH0sIG9wdGlvbnMpO1xuXG4gICAgICAgIHJldHVybiBfY3JlYXRlTGF5ZXIoZGF0YSwganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2hpbnQnLCAnbGFiZWxzJyksICcnLCBbYnV0dG9ucy5va10pO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBSZXR1cm5zIGEgY29uZmlybSBsYXllci5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBvcHRpb25zIE1peCBvZiBqUXVlcnkgVUkgZGlhbG9nIG9wdGlvbnMgYW5kIGN1c3RvbSBvcHRpb25zLlxuICAgICAqXG4gICAgICogQHJldHVybiB7cHJvbWlzZX0gUmV0dXJucyBhIHByb21pc2VcbiAgICAgKlxuICAgICAqIEBkZXByZWNhdGVkIFRoaXMgbWV0aG9kIHdpbGwgYmUgcmVtb3ZlZCB3aXRoIEpTRSB2MS41LlxuICAgICAqL1xuICAgIGV4cG9ydHMuY29uZmlybSA9IGZ1bmN0aW9uIChvcHRpb25zKSB7XG4gICAgICAgIF9sb2dEZXByZWNhdGVkTWV0aG9kKCdqc2UubGlicy5tb2RhbC5jb25maXJtKCknKTtcblxuICAgICAgICB2YXIgZGF0YSA9ICQuZXh0ZW5kKHt9LCB7XG4gICAgICAgICAgICAnZHJhZ2dhYmxlJzogdHJ1ZVxuICAgICAgICB9LCBvcHRpb25zKTtcblxuICAgICAgICByZXR1cm4gX2NyZWF0ZUxheWVyKGRhdGEsIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdjb25maXJtJywgJ2xhYmVscycpLCAnY29uZmlybV9kaWFsb2cnLFxuICAgICAgICAgICAgW2J1dHRvbnMubm8sIGJ1dHRvbnMueWVzXSk7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIFJldHVybnMgYSBwcm9tcHQgbGF5ZXIuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge29iamVjdH0gb3B0aW9ucyBNaXggb2YgalF1ZXJ5IFVJIGRpYWxvZyBvcHRpb25zIGFuZCBjdXN0b20gb3B0aW9ucy5cbiAgICAgKlxuICAgICAqIEByZXR1cm4ge3Byb21pc2V9IFJldHVybnMgYSBwcm9taXNlIG9iamVjdC5cbiAgICAgKlxuICAgICAqIEBkZXByZWNhdGVkIFRoaXMgbWV0aG9kIHdpbGwgYmUgcmVtb3ZlZCB3aXRoIEpTRSB2MS41LlxuICAgICAqL1xuICAgIGV4cG9ydHMucHJvbXB0ID0gZnVuY3Rpb24gKG9wdGlvbnMpIHtcbiAgICAgICAgX2xvZ0RlcHJlY2F0ZWRNZXRob2QoJ2pzZS5saWJzLm1vZGFsLnByb21wdCgpJyk7XG5cbiAgICAgICAgdmFyIGRhdGEgPSAkLmV4dGVuZCh7fSwge1xuICAgICAgICAgICAgJ2RyYWdnYWJsZSc6IHRydWVcbiAgICAgICAgfSwgb3B0aW9ucyk7XG5cbiAgICAgICAgcmV0dXJuIF9jcmVhdGVMYXllcihkYXRhLCBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgncHJvbXB0JywgJ2xhYmVscycpLCAncHJvbXB0X2RpYWxvZycsXG4gICAgICAgICAgICBbYnV0dG9ucy5hYm9ydCwgYnV0dG9ucy5va10sICcjbW9kYWxfcHJvbXB0Jyk7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIFJldHVybnMgYSBzdWNjZXNzIGxheWVyLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtvYmplY3R9IG9wdGlvbnMgTWl4IG9mIGpRdWVyeSBVSSBkaWFsb2cgb3B0aW9ucyBhbmQgY3VzdG9tIG9wdGlvbnMuXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIHtvYmplY3R9IFJldHVybnMgYSBwcm9taXNlIG9iamVjdC5cbiAgICAgKlxuICAgICAqIEBkZXByZWNhdGVkIFRoaXMgbWV0aG9kIHdpbGwgYmUgcmVtb3ZlZCB3aXRoIEpTRSB2MS41LlxuICAgICAqL1xuICAgIGV4cG9ydHMuc3VjY2VzcyA9IGZ1bmN0aW9uIChvcHRpb25zKSB7XG4gICAgICAgIF9sb2dEZXByZWNhdGVkTWV0aG9kKCdqc2UubGlicy5tb2RhbC5zdWNjZXNzKCknKTtcblxuICAgICAgICB2YXIgZGF0YSA9ICQuZXh0ZW5kKHt9LCB7XG4gICAgICAgICAgICAnZHJhZ2dhYmxlJzogdHJ1ZVxuICAgICAgICB9LCBvcHRpb25zKTtcblxuICAgICAgICByZXR1cm4gX2NyZWF0ZUxheWVyKGRhdGEsIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdzdWNjZXNzJywgJ2xhYmVscycpLCAnc3VjY2Vzc19kaWFsb2cnKTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogUmV0dXJucyBhbiBlcnJvciBsYXllci5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBvcHRpb25zIE1peCBvZiBqUXVlcnkgVUkgZGlhbG9nIG9wdGlvbnMgYW5kIGN1c3RvbSBvcHRpb25zLlxuICAgICAqXG4gICAgICogQHJldHVybiB7b2JqZWN0fSBSZXR1cm5zIGEgcHJvbWlzZSBvYmplY3QuXG4gICAgICpcbiAgICAgKiBAZGVwcmVjYXRlZCBUaGlzIG1ldGhvZCB3aWxsIGJlIHJlbW92ZWQgd2l0aCBKU0UgdjEuNS5cbiAgICAgKi9cbiAgICBleHBvcnRzLmVycm9yID0gZnVuY3Rpb24gKG9wdGlvbnMpIHtcbiAgICAgICAgX2xvZ0RlcHJlY2F0ZWRNZXRob2QoJ2pzZS5saWJzLm1vZGFsLmVycm9yKCknKTtcblxuICAgICAgICB2YXIgZGF0YSA9ICQuZXh0ZW5kKHt9LCB7XG4gICAgICAgICAgICAnZHJhZ2dhYmxlJzogdHJ1ZVxuICAgICAgICB9LCBvcHRpb25zKTtcblxuICAgICAgICByZXR1cm4gX2NyZWF0ZUxheWVyKGRhdGEsIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdlcnJvcicsICdsYWJlbHMnKSwgJ2Vycm9yX2RpYWxvZycpO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBSZXR1cm5zIGEgd2FybmluZyBsYXllci5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBvcHRpb25zIE1peCBvZiBqUXVlcnkgVUkgZGlhbG9nIG9wdGlvbnMgYW5kIGN1c3RvbSBvcHRpb25zLlxuICAgICAqXG4gICAgICogQHJldHVybiB7b2JqZWN0fSBSZXR1cm5zIGEgcHJvbWlzZSBvYmplY3QuXG4gICAgICpcbiAgICAgKiBAZGVwcmVjYXRlZCBUaGlzIG1ldGhvZCB3aWxsIGJlIHJlbW92ZWQgd2l0aCBKU0UgdjEuNS5cbiAgICAgKi9cbiAgICBleHBvcnRzLndhcm4gPSBmdW5jdGlvbiAob3B0aW9ucykge1xuICAgICAgICBfbG9nRGVwcmVjYXRlZE1ldGhvZCgnanNlLmxpYnMubW9kYWwud2FybigpJyk7XG5cbiAgICAgICAgdmFyIGRhdGEgPSAkLmV4dGVuZCh7fSwge1xuICAgICAgICAgICAgJ2RyYWdnYWJsZSc6IHRydWVcbiAgICAgICAgfSwgb3B0aW9ucyk7XG5cbiAgICAgICAgcmV0dXJuIF9jcmVhdGVMYXllcihkYXRhLCBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnd2FybmluZycsICdsYWJlbHMnKSwgJ3dhcm5fZGlhbG9nJyk7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIFJldHVybnMgYW4gaW5mbyBsYXllci5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBvcHRpb25zIE1peCBvZiBqUXVlcnkgVUkgZGlhbG9nIG9wdGlvbnMgYW5kIGN1c3RvbSBvcHRpb25zLlxuICAgICAqXG4gICAgICogQHJldHVybiB7cHJvbWlzZX0gUmV0dXJucyBhIHByb21pc2Ugb2JqZWN0LlxuICAgICAqXG4gICAgICogQGRlcHJlY2F0ZWQgVGhpcyBtZXRob2Qgd2lsbCBiZSByZW1vdmVkIHdpdGggSlNFIHYxLjUuXG4gICAgICovXG4gICAgZXhwb3J0cy5pbmZvID0gZnVuY3Rpb24gKG9wdGlvbnMpIHtcbiAgICAgICAgX2xvZ0RlcHJlY2F0ZWRNZXRob2QoJ2pzZS5saWJzLm1vZGFsLmluZm8oKScpO1xuXG4gICAgICAgIHZhciBkYXRhID0gJC5leHRlbmQoe30sIHtcbiAgICAgICAgICAgICdkcmFnZ2FibGUnOiB0cnVlXG4gICAgICAgIH0sIG9wdGlvbnMpO1xuXG4gICAgICAgIHJldHVybiBfY3JlYXRlTGF5ZXIoZGF0YSwganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2luZm8nLCAnbGFiZWxzJyksICdpbmZvX2RpYWxvZycpO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBEaXNwbGF5IGpRdWVyeSBVSSBtZXNzYWdlLlxuICAgICAqXG4gICAgICogVGhpcyBtZXRob2QgcHJvdmlkZXMgYW4gZWFzeSB3YXkgdG8gZGlzcGxheSBhIG1lc3NhZ2UgdG8gdGhlIHVzZXIgYnkgdXNpbmcgalF1ZXJ5IFVJIGRpYWxvZyB3aWRnZXQuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge09iamVjdH0gb3B0aW9ucyBNb2RhbCBvcHRpb25zIGFyZSB0aGUgc2FtZSBhcyB0aGUgalF1ZXJ5IGRpYWxvZyB3aWRnZXQuXG4gICAgICovXG4gICAgZXhwb3J0cy5tZXNzYWdlID0gZnVuY3Rpb24gKG9wdGlvbnMpIHtcbiAgICAgICAgLy8gQ3JlYXRlIGRpdiBlbGVtZW50IGZvciBtb2RhbCBkaWFsb2cuXG4gICAgICAgICQoJ2JvZHknKS5hcHBlbmQoJzxkaXYgY2xhc3M9XCJtb2RhbC1sYXllclwiPicgKyBvcHRpb25zLmNvbnRlbnQgKyAnPC9kaXY+Jyk7XG5cbiAgICAgICAgLy8gQXBwZW5kIG9wdGlvbnMgb2JqZWN0IHdpdGggZXh0cmEgZGlhbG9nIG9wdGlvbnMuXG4gICAgICAgIG9wdGlvbnMubW9kYWwgPSB0cnVlO1xuICAgICAgICBvcHRpb25zLmRpYWxvZ0NsYXNzID0gJ2d4LWNvbnRhaW5lcic7XG5cbiAgICAgICAgLy8gU2V0IGRlZmF1bHQgYnV0dG9ucywgaWYgb3B0aW9uIHdhc24ndCBwcm92aWRlZC5cbiAgICAgICAgaWYgKG9wdGlvbnMuYnV0dG9ucyA9PT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICAgICBvcHRpb25zLmJ1dHRvbnMgPSBbXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICB0ZXh0OiBidXR0b25zLmNsb3NlLm5hbWUsXG4gICAgICAgICAgICAgICAgICAgIGNsaWNrOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmRpYWxvZygnY2xvc2UnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlKCk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICBdO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gRGlzcGxheSBtZXNzYWdlIHRvIHRoZSB1c2VyLlxuICAgICAgICAkKCcubW9kYWwtbGF5ZXI6bGFzdCcpLmRpYWxvZyhvcHRpb25zKTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogRGlzcGxheSBCb290c3RyYXAgbW9kYWwgbWVzc2FnZS5cbiAgICAgKlxuICAgICAqIHtAbGluayBodHRwOi8vZ2V0Ym9vdHN0cmFwLmNvbS9qYXZhc2NyaXB0LyNtb2RhbHN9XG4gICAgICpcbiAgICAgKiBFeGFtcGxlOlxuICAgICAqXG4gICAgICoganNlLmxpYnMubW9kYWwuc2hvd01lc3NhZ2UoJ1RpdGxlJywgJ01lc3NhZ2UnLCBbXG4gICAgICogICB7XG4gICAgICogICAgIHRpdGxlOiAnU2VuZCcsIC8vIEJ1dHRvbiB0aXRsZSBcbiAgICAgKiAgICAgY2xhc3M6ICdidG4gYnRuLXByaW1hcnkgc2VuZCcsIC8vIChvcHRpb25hbCkgQWRkIGEgY3VzdG9tIGJ1dHRvbiBjbGFzcy4gXG4gICAgICogICAgIGNhbGxiYWNrOiBmdW5jdGlvbihldmVudCkgeyAuLi4gfSAvLyAob3B0aW9uYWwpIFByb3ZpZGUgYSBjbGljayBjYWxsYmFja1xuICAgICAqICAgfSxcbiAgICAgKiAgIHtcbiAgICAgKiAgICAgdGl0bGU6ICdDbG9zZScsXG4gICAgICogICAgIGNsb3NlTW9kYWw6IHRydWUgLy8gKG9wdGlvbmFsKSAgTW9kYWwgd2lsbCBiZSBjbG9zZWQgdXBvbiBjbGljay5cbiAgICAgKiAgIH1cbiAgICAgKiBdKTtcbiAgICAgKlxuICAgICAqIFlvdSBjYW4gY2xvc2UgdGhlIG1vZGFsIGJ5IHVzaW5nIHRoZSBCb290c3RyYXAgQVBJOiAkbW9kYWwubW9kYWwoJ2hpZGUnKTtcbiAgICAgKlxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSB0aXRsZSBUaGUgbWVzc2FnZSB0aXRsZS5cbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gY29udGVudCBUaGUgbWVzc2FnZSBjb250ZW50LlxuICAgICAqIEBwYXJhbSB7T2JqZWN0W119IFtidXR0b25zPW51bGxdIFByb3ZpZGUgYW4gYXJyYXkgd2l0aCBvYmplY3RzIHdoaWNoIGRlZmluZSB0aGUgbW9kYWwgYnV0dG9ucy5cbiAgICAgKlxuICAgICAqIEByZXR1cm4ge2pRdWVyeX0gUmV0dXJucyB0aGUgbW9kYWwgc2VsZWN0b3IuXG4gICAgICovXG4gICAgZXhwb3J0cy5zaG93TWVzc2FnZSA9IGZ1bmN0aW9uICh0aXRsZSwgY29udGVudCwgYnV0dG9ucyA9IG51bGwpIHtcbiAgICAgICAgLy8gR2VuZXJhdGUgdGhlIGRlZmF1bHQgY2xvc2UgYnV0dG9uIGRlZmluaXRpb24uIFxuICAgICAgICBpZiAoIWJ1dHRvbnMpIHtcbiAgICAgICAgICAgIGJ1dHRvbnMgPSBbXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICB0aXRsZToganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ0NMT1NFJywgJ2dlbmVyYWwnKSxcbiAgICAgICAgICAgICAgICAgICAgY2xhc3M6ICdidG4gYnRuLWRlZmF1bHQnLFxuICAgICAgICAgICAgICAgICAgICBjYWxsYmFjazogZXZlbnQgPT4gJChldmVudC5jdXJyZW50VGFyZ2V0KS5wYXJlbnRzKCcubW9kYWwnKS5tb2RhbCgnaGlkZScpXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgXTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIFByZXBhcmUgdGhlIEJvb3RzdHJhcCBIVE1MIG1hcmt1cC4gXG4gICAgICAgIGNvbnN0IGh0bWwgPSBgPGRpdiBjbGFzcz1cIm1vZGFsIGZhZGVcIiB0YWJpbmRleD1cIi0xXCIgcm9sZT1cImRpYWxvZ1wiPlxuXHRcdFx0XHRcdFx0PGRpdiBjbGFzcz1cIm1vZGFsLWRpYWxvZ1wiPlxuXHRcdFx0XHRcdFx0XHQ8ZGl2IGNsYXNzPVwibW9kYWwtY29udGVudFwiPlxuXHRcdFx0XHRcdFx0XHRcdDxkaXYgY2xhc3M9XCJtb2RhbC1oZWFkZXJcIj5cblx0XHRcdFx0XHRcdFx0XHRcdDxidXR0b24gdHlwZT1cImJ1dHRvblwiIGNsYXNzPVwiY2xvc2VcIiBkYXRhLWRpc21pc3M9XCJtb2RhbFwiIGFyaWEtbGFiZWw9XCJDbG9zZVwiPlxuXHRcdFx0XHRcdFx0XHRcdFx0XHQ8c3BhbiBhcmlhLWhpZGRlbj1cInRydWVcIj4mdGltZXM7PC9zcGFuPlxuXHRcdFx0XHRcdFx0XHRcdFx0PC9idXR0b24+XG5cdFx0XHRcdFx0XHRcdFx0XHQ8aDQgY2xhc3M9XCJtb2RhbC10aXRsZVwiPiR7dGl0bGV9PC9oND5cblx0XHRcdFx0XHRcdFx0XHQ8L2Rpdj5cblx0XHRcdFx0XHRcdFx0XHQ8ZGl2IGNsYXNzPVwibW9kYWwtYm9keVwiPlxuXHRcdFx0XHRcdCAgICAgICAgICAgICAgICAke2NvbnRlbnR9XG5cdFx0XHRcdFx0XHRcdFx0PC9kaXY+XG5cdFx0XHRcdFx0XHRcdFx0PGRpdiBjbGFzcz1cIm1vZGFsLWZvb3RlclwiPjwvZGl2PlxuXHRcdFx0XHRcdFx0XHQ8L2Rpdj5cblx0XHRcdFx0XHRcdDwvZGl2PlxuXHRcdFx0XHRcdDwvZGl2PmA7XG5cbiAgICAgICAgY29uc3QgJG1vZGFsID0gJChodG1sKS5hcHBlbmRUbygnYm9keScpO1xuXG4gICAgICAgIC8vIEFkZCB0aGUgYnV0dG9ucyB0byB0aGUgbW9kYWwuIFxuICAgICAgICBidXR0b25zLmZvckVhY2goYnV0dG9uID0+IHtcbiAgICAgICAgICAgIGNvbnN0ICRidXR0b24gPSAkKCc8YnV0dG9uLz4nKTtcbiAgICAgICAgICAgICRidXR0b25cbiAgICAgICAgICAgICAgICAudGV4dChidXR0b24udGl0bGUpXG4gICAgICAgICAgICAgICAgLmF0dHIoJ2NsYXNzJywgYnV0dG9uLmNsYXNzIHx8ICdidG4gYnRuLWRlZmF1bHQnKTtcblxuICAgICAgICAgICAgaWYgKGJ1dHRvbi5jYWxsYmFjaykge1xuICAgICAgICAgICAgICAgICRidXR0b24ub24oJ2NsaWNrJywgYnV0dG9uLmNhbGxiYWNrKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgJGJ1dHRvbi5hcHBlbmRUbygkbW9kYWwuZmluZCgnLm1vZGFsLWZvb3RlcicpKTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgLy8gUmVtb3ZlIHRoZSBtb2RhbCBlbGVtZW50IHdoZW4gaXRzIGhpZGRlbi4gXG4gICAgICAgICRtb2RhbC5vbignaGlkZGVuLmJzLm1vZGFsJywgKCkgPT4gJG1vZGFsLnJlbW92ZSgpKTtcblxuICAgICAgICAvLyBEaXNwbGF5IHRoZSBtb2RhbCB0byB0aGUgdXNlci5cbiAgICAgICAgJG1vZGFsLm1vZGFsKCdzaG93Jyk7XG5cbiAgICAgICAgcmV0dXJuICRtb2RhbDtcbiAgICB9O1xuXG59KGpzZS5saWJzLm1vZGFsKSk7XG4iXX0=
