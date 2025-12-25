'use strict';

/* --------------------------------------------------------------
 modal.js 2016-07-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.theme.modal = jse.libs.theme.modal || {};

/**
 * ## Honeygrid Modal Dialogs Library
 *
 * Library-function to open default modal layer.  This function depends on jQuery & jQuery UI.
 *
 * @module Honeygrid/Libs/modal
 * @exports jse.libs.theme.modal
 * @ignore
 */
(function (exports) {
    'use strict';

    var $body = $('body'),
        tplStore = [],
        extension = null,

    // Object for default buttons
    buttons = {
        yes: {
            name: jse.core.lang.translate('yes', 'buttons'),
            type: 'success',
            class: 'btn-success'
        },
        no: {
            name: jse.core.lang.translate('no', 'buttons'),
            type: 'fail',
            class: 'btn-default'
        },
        abort: {
            name: jse.core.lang.translate('abort', 'buttons'),
            type: 'fail',
            class: 'btn-default'
        },
        ok: {
            name: jse.core.lang.translate('ok', 'buttons'),
            type: 'success',
            class: 'btn-success'
        },
        close: {
            name: jse.core.lang.translate('close', 'buttons'),
            type: 'fail',
            class: 'btn-default'
        }
    };

    /**
     *    Function to get all form data stored inside
     *    the layer
     *
     *    @param        {object}    $self        jQuery selection of the layer
     *    @return    {json}                    Returns a JSON with all form data
     */
    var _getFormData = function _getFormData($self, checkform) {
        var $forms = $self.filter('form').add($self.find('form')),
            formdata = {},
            valid = true,
            promises = [];

        if ($forms.length) {
            $forms.each(function () {
                var $form = $(this);

                if (checkform) {
                    var localDeferred = $.Deferred();
                    promises.push(localDeferred);
                    $form.trigger('validator.validate', { deferred: localDeferred });
                }

                formdata[$form.attr('name') || $form.attr('id') || 'form_' + new Date().getTime() * Math.random()] = jse.libs.form.getData($form);
            });
        }

        return $.when.apply(undefined, promises).then(function () {
            return formdata;
        }, function () {
            return formdata;
        }).promise();
    };

    /**
     *    Function to transform the custom buttons object (which is
     *    incompatible with jQuery UI) to a jQuery UI compatible format
     *
     *    @param        {object}    dataset        Custom buttons object for the dialog
     *    @param        {promise}    deferred    deferred-object to resolve / reject on close
     *    @return    {array}                    Returns a jQuery UI dialog compatible buttons array
     */
    var _genButtons = function _genButtons(options, extensionDeferred) {

        // Check if buttons are available
        if (options.buttons) {

            var rejectHandler = extension.getRejectHandler,
                resolveHandler = extension.getResolveHandler;

            $.each(options.buttons, function (k, v) {

                // Setup click handler
                options.buttons[k].event = function () {
                    var $self = $(this);

                    // If a callback is given, execute it with
                    // the current scope
                    if (typeof v.callback === 'function') {
                        if (!v.callback.apply($self, [])) {
                            return false;
                        }
                    }

                    // Add the default behaviour
                    // for the close  functionality
                    // On fail, reject the deferred
                    // object, else resolve it
                    switch (v.type) {
                        case 'fail':
                            rejectHandler($self, extensionDeferred, _getFormData);
                            break;
                        case 'success':
                            resolveHandler($self, extensionDeferred, _getFormData);
                            break;
                        case 'link':
                            location.href = v.value;
                            break;
                        default:
                            break;
                    }
                };
            });
        }
    };

    var _finalizeLayer = function _finalizeLayer($container, options) {
        // Prevent submit on enter in inner forms
        var $forms = $container.find('form');
        if ($forms.length) {
            $forms.on('submit', function (e) {
                e.preventDefault();
            });
        }

        if (window.gambio && window.gambio.widgets && window.gambio.widgets.init) {
            window.gambio.widgets.init($container);
        }
    };

    var _setLayer = function _setLayer(name) {
        if (jse.libs.theme.modal[name]) {
            extension = jse.libs.theme.modal[name];
        } else {
            jse.core.debug.error('[MODAL] Can\'t set modal: "' + name + '". Extension doesn\'t exist');
        }
    };

    var _transferOptions = function _transferOptions(options) {
        var mapper = extension.getMapper(),
            result = {};

        $.each(options, function (k, v) {

            if (mapper[k] === false) {
                return true;
            } else if (mapper[k] === undefined) {
                result[k] = v;
            } else if (typeof mapper[k] === 'function') {
                var mapperResult = mapper[k](k, v);
                result[mapperResult[0]] = mapperResult[1];
            } else {
                result[mapper[k]] = v;
            }
        });

        return result;
    };

    var _getTheme = function _getTheme(options, iframe) {

        var $selection = [],
            deferred = $.Deferred();

        if (options.noTheme) {
            deferred.resolve('');
        } else if (iframe) {
            deferred.resolve('<iframe width="100%" height="100%" frameborder="0" src="' + options.theme + '" />');
        } else {
            if (options.storeTheme && tplStore[options.theme]) {
                deferred.resolve(tplStore[options.theme]);
            } else {

                try {
                    $selection = $(options.theme);
                } catch (err) {}

                if ($selection.length) {
                    deferred.resolve($selection.html());
                } else {
                    jse.libs.xhr.ajax({ url: options.theme, dataType: 'html' }).done(function (result) {
                        if (options.sectionSelector) {
                            result = $(result).find(options.sectionSelector).html();
                        }

                        if (options.storeTheme) {
                            tplStore[options.theme] = result;
                        }
                        deferred.resolve(result);
                    }).fail(function () {
                        deferred.reject();
                    });
                }
            }
        }

        return deferred;
    };

    var _createLayer = function _createLayer(options, title, className, defbuttons, theme) {
        // Setup defaults & deferred objects
        var deferred = $.Deferred(),
            promise = deferred.promise(),
            iframe = theme === 'iframe',
            defaults = {
            title: title,
            dialogClass: className,
            modal: true,
            buttons: defbuttons || [],
            closeOnEscape: true,
            theme: theme || null,
            storeTheme: false,
            closeX: true,
            closeOnOuter: true
        },
            instance = null,
            $forms = null,
            extensionDeferred = $.Deferred();

        // Merge custom settings with default settings
        options = options || {};
        options = $.extend({}, defaults, options);

        var tplRequest = _getTheme(options, iframe).done(function (result) {

            extensionDeferred.done(function (result) {
                deferred.resolve(result);
            }).fail(function (result) {
                deferred.reject(result);
            });

            // Generate theme
            options.theme = $(Mustache.render(result, options));
            jse.libs.theme.helpers.setupWidgetAttr(options.theme);
            options.theme = $('<div>').append(options.theme.clone()).html();

            // Generate default button object
            _genButtons(options, extensionDeferred);

            // Transfer options object to extension option object
            var originalOptions = $.extend({}, options);
            options = _transferOptions(options);

            // Call extension
            extension.openLayer(options, extensionDeferred, _getFormData, originalOptions);

            // Passthrough of the close method of the layer
            // to the layer caller
            promise.close = function (success) {
                extensionDeferred.close(success);
            };
        }).fail(function () {
            deferred.reject({ error: 'Theme not found' });
        });

        // Temporary close handler if the upper
        // deferred isn't finished now. It will be
        // overwritten after the layer opens
        if (!promise.close) {
            promise.close = function () {
                tplRequest.reject('Closed after opening');
            };
        }

        return promise;
    };

    /**
     *    Shortcut function for an alert-layer
     *    @param        {object}    options Options that are passed to the modal layer
     *    @return    {promise}            Returns a promise
     */
    var _alert = function _alert(options) {
        return _createLayer(options, jse.core.lang.translate('hint', 'labels'), '', [buttons.close], '#modal_alert');
    };

    /**
     *    Shortcut function for an confirm-layer
     *    @param        {object}    options Options that are passed to the modal layer
     *    @return    {promise}            Returns a promise
     */
    var _confirm = function _confirm(options) {
        return _createLayer(options, jse.core.lang.translate('confirm', 'labels'), 'confirm_dialog', [buttons.yes, buttons.no], '#modal_alert');
    };

    /**
     *    Shortcut function for a prompt-layer
     *    @param        {object}    options Options that are passed to the modal layer
     *    @return    {promise}            Returns a promise
     */
    var _prompt = function _prompt(options) {
        return _createLayer(options, jse.core.lang.translate('prompt', 'labels'), 'prompt_dialog', [buttons.ok, buttons.abort], '#modal_prompt');
    };

    /**
     *    Shortcut function for an success-layer
     *    @param        {object}    options Options that are passed to the modal layer
     *    @return    {promise}            Returns a promise
     */
    var _success = function _success(options) {
        return _createLayer(options, jse.core.lang.translate('success', 'labels'), 'success_dialog', [], '#modal_alert');
    };

    /**
     *    Shortcut function for an error-layer
     *    @param        {object}    options Options that are passed to the modal layer
     *    @return    {promise}            Returns a promise
     */
    var _error = function _error(options) {
        return _createLayer(options, jse.core.lang.translate('errors', 'labels'), 'error_dialog', [], '#modal_alert');
    };

    /**
     *    Shortcut function for a warning-layer
     *    @param        {object}    options Options that are passed to the modal layer
     *    @return    {promise}            Returns a promise
     */
    var _warn = function _warn(options) {
        return _createLayer(options, jse.core.lang.translate('warning', 'labels'), 'warn_dialog', [], '#modal_alert');
    };

    /**
     *    Shortcut function for an info-layer
     *    @param        {object}    options Options that are passed to the modal layer
     *    @return    {promise}            Returns a promise
     */
    var _info = function _info(options) {
        return _createLayer(options, jse.core.lang.translate('info', 'labels'), 'info_dialog', [], '#modal_alert');
    };

    /**
     *    Shortcut function for an iframe-layer
     *    @param        {object}    options Options that are passed to the modal layer
     *    @return    {promise}            Returns a promise
     */
    var _iframe = function _iframe(options) {
        if (options.convertModal) {
            jse.libs.theme.modal[options.convertModal](options, jse.core.lang.translate('info', 'labels'), options.convertModal + '_dialog', [], '#modal_alert');
            return;
        }

        return _createLayer(options, jse.core.lang.translate('info', 'labels'), 'iframe_layer', [], 'iframe');
    };

    // ########## VARIABLE EXPORT ##########

    exports.error = _error;
    exports.warn = _warn;
    exports.info = _info;
    exports.success = _success;
    exports.alert = _alert;
    exports.prompt = _prompt;
    exports.confirm = _confirm;
    exports.iframe = _iframe;
    exports.custom = _createLayer;
    exports.setLayer = _setLayer;
    exports.finalizeLayer = _finalizeLayer;

    // Set default layer.
    var currentTimestamp = Date.now,
        lifetime = 10000; // 10 sec

    extension = jse.core.registry.get('mainModalLayer');

    var intv = setInterval(function () {
        if (jse.libs.theme.modal[extension] !== undefined) {
            _setLayer(extension);
            clearInterval(intv);
        }

        if (Date.now - currentTimestamp > lifetime) {
            throw new Error('Modal extension was not loaded: ' + extension);
        }
    }, 300);
})(jse.libs.theme.modal);

jse.libs.template = jse.libs.template || {};
jse.libs.template.modal = jse.libs.theme.modal;
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxpYnMvbW9kYWwuanMiXSwibmFtZXMiOlsianNlIiwibGlicyIsInRoZW1lIiwibW9kYWwiLCJleHBvcnRzIiwiJGJvZHkiLCIkIiwidHBsU3RvcmUiLCJleHRlbnNpb24iLCJidXR0b25zIiwieWVzIiwibmFtZSIsImNvcmUiLCJsYW5nIiwidHJhbnNsYXRlIiwidHlwZSIsImNsYXNzIiwibm8iLCJhYm9ydCIsIm9rIiwiY2xvc2UiLCJfZ2V0Rm9ybURhdGEiLCIkc2VsZiIsImNoZWNrZm9ybSIsIiRmb3JtcyIsImZpbHRlciIsImFkZCIsImZpbmQiLCJmb3JtZGF0YSIsInZhbGlkIiwicHJvbWlzZXMiLCJsZW5ndGgiLCJlYWNoIiwiJGZvcm0iLCJsb2NhbERlZmVycmVkIiwiRGVmZXJyZWQiLCJwdXNoIiwidHJpZ2dlciIsImRlZmVycmVkIiwiYXR0ciIsIkRhdGUiLCJnZXRUaW1lIiwiTWF0aCIsInJhbmRvbSIsImZvcm0iLCJnZXREYXRhIiwid2hlbiIsImFwcGx5IiwidW5kZWZpbmVkIiwidGhlbiIsInByb21pc2UiLCJfZ2VuQnV0dG9ucyIsIm9wdGlvbnMiLCJleHRlbnNpb25EZWZlcnJlZCIsInJlamVjdEhhbmRsZXIiLCJnZXRSZWplY3RIYW5kbGVyIiwicmVzb2x2ZUhhbmRsZXIiLCJnZXRSZXNvbHZlSGFuZGxlciIsImsiLCJ2IiwiZXZlbnQiLCJjYWxsYmFjayIsImxvY2F0aW9uIiwiaHJlZiIsInZhbHVlIiwiX2ZpbmFsaXplTGF5ZXIiLCIkY29udGFpbmVyIiwib24iLCJlIiwicHJldmVudERlZmF1bHQiLCJ3aW5kb3ciLCJnYW1iaW8iLCJ3aWRnZXRzIiwiaW5pdCIsIl9zZXRMYXllciIsImRlYnVnIiwiZXJyb3IiLCJfdHJhbnNmZXJPcHRpb25zIiwibWFwcGVyIiwiZ2V0TWFwcGVyIiwicmVzdWx0IiwibWFwcGVyUmVzdWx0IiwiX2dldFRoZW1lIiwiaWZyYW1lIiwiJHNlbGVjdGlvbiIsIm5vVGhlbWUiLCJyZXNvbHZlIiwic3RvcmVUaGVtZSIsImVyciIsImh0bWwiLCJ4aHIiLCJhamF4IiwidXJsIiwiZGF0YVR5cGUiLCJkb25lIiwic2VjdGlvblNlbGVjdG9yIiwiZmFpbCIsInJlamVjdCIsIl9jcmVhdGVMYXllciIsInRpdGxlIiwiY2xhc3NOYW1lIiwiZGVmYnV0dG9ucyIsImRlZmF1bHRzIiwiZGlhbG9nQ2xhc3MiLCJjbG9zZU9uRXNjYXBlIiwiY2xvc2VYIiwiY2xvc2VPbk91dGVyIiwiaW5zdGFuY2UiLCJleHRlbmQiLCJ0cGxSZXF1ZXN0IiwiTXVzdGFjaGUiLCJyZW5kZXIiLCJoZWxwZXJzIiwic2V0dXBXaWRnZXRBdHRyIiwiYXBwZW5kIiwiY2xvbmUiLCJvcmlnaW5hbE9wdGlvbnMiLCJvcGVuTGF5ZXIiLCJzdWNjZXNzIiwiX2FsZXJ0IiwiX2NvbmZpcm0iLCJfcHJvbXB0IiwiX3N1Y2Nlc3MiLCJfZXJyb3IiLCJfd2FybiIsIl9pbmZvIiwiX2lmcmFtZSIsImNvbnZlcnRNb2RhbCIsIndhcm4iLCJpbmZvIiwiYWxlcnQiLCJwcm9tcHQiLCJjb25maXJtIiwiY3VzdG9tIiwic2V0TGF5ZXIiLCJmaW5hbGl6ZUxheWVyIiwiY3VycmVudFRpbWVzdGFtcCIsIm5vdyIsImxpZmV0aW1lIiwicmVnaXN0cnkiLCJnZXQiLCJpbnR2Iiwic2V0SW50ZXJ2YWwiLCJjbGVhckludGVydmFsIiwiRXJyb3IiLCJ0ZW1wbGF0ZSJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBQSxJQUFJQyxJQUFKLENBQVNDLEtBQVQsQ0FBZUMsS0FBZixHQUF1QkgsSUFBSUMsSUFBSixDQUFTQyxLQUFULENBQWVDLEtBQWYsSUFBd0IsRUFBL0M7O0FBRUE7Ozs7Ozs7OztBQVNDLFdBQVVDLE9BQVYsRUFBbUI7QUFDaEI7O0FBRUEsUUFBSUMsUUFBUUMsRUFBRSxNQUFGLENBQVo7QUFBQSxRQUNJQyxXQUFXLEVBRGY7QUFBQSxRQUVJQyxZQUFZLElBRmhCOztBQUdJO0FBQ0FDLGNBQVU7QUFDTkMsYUFBSztBQUNEQyxrQkFBTVgs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O0FBQ3hDLGtCQUFNLElBQUlNLEtBQUosQ0FBVSxxQ0FBcUN4SSxTQUEvQyxDQUFOO0FBQ0g7QUFDSixLQVRVLEVBU1IsR0FUUSxDQUFYO0FBWUgsQ0FuWkEsRUFtWkNSLElBQUlDLElBQUosQ0FBU0MsS0FBVCxDQUFlQyxLQW5aaEIsQ0FBRDs7QUFxWkFILElBQUlDLElBQUosQ0FBU2dKLFFBQVQsR0FBb0JqSixJQUFJQyxJQUFKLENBQVNnSixRQUFULElBQXFCLEVBQXpDO0FBQ0FqSixJQUFJQyxJQUFKLENBQVNnSixRQUFULENBQWtCOUksS0FBbEIsR0FBMEJILElBQUlDLElBQUosQ0FBU0MsS0FBVCxDQUFlQyxLQUF6QyIsImZpbGUiOiJsaWJzL21vZGFsLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBtb2RhbC5qcyAyMDE2LTA3LTA3XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuanNlLmxpYnMudGhlbWUubW9kYWwgPSBqc2UubGlicy50aGVtZS5tb2RhbCB8fCB7fTtcblxuLyoqXG4gKiAjIyBIb25leWdyaWQgTW9kYWwgRGlhbG9ncyBMaWJyYXJ5XG4gKlxuICogTGlicmFyeS1mdW5jdGlvbiB0byBvcGVuIGRlZmF1bHQgbW9kYWwgbGF5ZXIuICBUaGlzIGZ1bmN0aW9uIGRlcGVuZHMgb24galF1ZXJ5ICYgalF1ZXJ5IFVJLlxuICpcbiAqIEBtb2R1bGUgSG9uZXlncmlkL0xpYnMvbW9kYWxcbiAqIEBleHBvcnRzIGpzZS5saWJzLnRoZW1lLm1vZGFsXG4gKiBAaWdub3JlXG4gKi9cbihmdW5jdGlvbiAoZXhwb3J0cykge1xuICAgICd1c2Ugc3RyaWN0JztcblxuICAgIHZhciAkYm9keSA9ICQoJ2JvZHknKSxcbiAgICAgICAgdHBsU3RvcmUgPSBbXSxcbiAgICAgICAgZXh0ZW5zaW9uID0gbnVsbCxcbiAgICAgICAgLy8gT2JqZWN0IGZvciBkZWZhdWx0IGJ1dHRvbnNcbiAgICAgICAgYnV0dG9ucyA9IHtcbiAgICAgICAgICAgIHllczoge1xuICAgICAgICAgICAgICAgIG5hbWU6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCd5ZXMnLCAnYnV0dG9ucycpLFxuICAgICAgICAgICAgICAgIHR5cGU6ICdzdWNjZXNzJyxcbiAgICAgICAgICAgICAgICBjbGFzczogJ2J0bi1zdWNjZXNzJ1xuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIG5vOiB7XG4gICAgICAgICAgICAgICAgbmFtZToganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ25vJywgJ2J1dHRvbnMnKSxcbiAgICAgICAgICAgICAgICB0eXBlOiAnZmFpbCcsXG4gICAgICAgICAgICAgICAgY2xhc3M6ICdidG4tZGVmYXVsdCdcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBhYm9ydDoge1xuICAgICAgICAgICAgICAgIG5hbWU6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdhYm9ydCcsICdidXR0b25zJyksXG4gICAgICAgICAgICAgICAgdHlwZTogJ2ZhaWwnLFxuICAgICAgICAgICAgICAgIGNsYXNzOiAnYnRuLWRlZmF1bHQnXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgb2s6IHtcbiAgICAgICAgICAgICAgICBuYW1lOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnb2snLCAnYnV0dG9ucycpLFxuICAgICAgICAgICAgICAgIHR5cGU6ICdzdWNjZXNzJyxcbiAgICAgICAgICAgICAgICBjbGFzczogJ2J0bi1zdWNjZXNzJ1xuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGNsb3NlOiB7XG4gICAgICAgICAgICAgICAgbmFtZToganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Nsb3NlJywgJ2J1dHRvbnMnKSxcbiAgICAgICAgICAgICAgICB0eXBlOiAnZmFpbCcsXG4gICAgICAgICAgICAgICAgY2xhc3M6ICdidG4tZGVmYXVsdCdcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgIC8qKlxuICAgICAqICAgIEZ1bmN0aW9uIHRvIGdldCBhbGwgZm9ybSBkYXRhIHN0b3JlZCBpbnNpZGVcbiAgICAgKiAgICB0aGUgbGF5ZXJcbiAgICAgKlxuICAgICAqICAgIEBwYXJhbSAgICAgICAge29iamVjdH0gICAgJHNlbGYgICAgICAgIGpRdWVyeSBzZWxlY3Rpb24gb2YgdGhlIGxheWVyXG4gICAgICogICAgQHJldHVybiAgICB7anNvbn0gICAgICAgICAgICAgICAgICAgIFJldHVybnMgYSBKU09OIHdpdGggYWxsIGZvcm0gZGF0YVxuICAgICAqL1xuICAgIHZhciBfZ2V0Rm9ybURhdGEgPSBmdW5jdGlvbiAoJHNlbGYsIGNoZWNrZm9ybSkge1xuICAgICAgICB2YXIgJGZvcm1zID0gJHNlbGZcbiAgICAgICAgICAgICAgICAuZmlsdGVyKCdmb3JtJylcbiAgICAgICAgICAgICAgICAuYWRkKCRzZWxmLmZpbmQoJ2Zvcm0nKSksXG4gICAgICAgICAgICBmb3JtZGF0YSA9IHt9LFxuICAgICAgICAgICAgdmFsaWQgPSB0cnVlLFxuICAgICAgICAgICAgcHJvbWlzZXMgPSBbXTtcblxuICAgICAgICBpZiAoJGZvcm1zLmxlbmd0aCkge1xuICAgICAgICAgICAgJGZvcm1zLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIHZhciAkZm9ybSA9ICQodGhpcyk7XG5cbiAgICAgICAgICAgICAgICBpZiAoY2hlY2tmb3JtKSB7XG4gICAgICAgICAgICAgICAgICAgIHZhciBsb2NhbERlZmVycmVkID0gJC5EZWZlcnJlZCgpO1xuICAgICAgICAgICAgICAgICAgICBwcm9taXNlcy5wdXNoKGxvY2FsRGVmZXJyZWQpO1xuICAgICAgICAgICAgICAgICAgICAkZm9ybS50cmlnZ2VyKCd2YWxpZGF0b3IudmFsaWRhdGUnLCB7ZGVmZXJyZWQ6IGxvY2FsRGVmZXJyZWR9KTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBmb3JtZGF0YVskZm9ybS5hdHRyKCduYW1lJykgfHwgJGZvcm0uYXR0cignaWQnKSB8fCAoJ2Zvcm1fJyArIG5ldyBEYXRlKCkuZ2V0VGltZSgpICogTWF0aC5yYW5kb20oKSldXG4gICAgICAgICAgICAgICAgICAgID0ganNlLmxpYnMuZm9ybS5nZXREYXRhKCRmb3JtKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuICQud2hlblxuICAgICAgICAgICAgLmFwcGx5KHVuZGVmaW5lZCwgcHJvbWlzZXMpXG4gICAgICAgICAgICAudGhlbihmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZvcm1kYXRhO1xuICAgICAgICAgICAgfSwgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIHJldHVybiBmb3JtZGF0YTtcbiAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAucHJvbWlzZSgpO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiAgICBGdW5jdGlvbiB0byB0cmFuc2Zvcm0gdGhlIGN1c3RvbSBidXR0b25zIG9iamVjdCAod2hpY2ggaXNcbiAgICAgKiAgICBpbmNvbXBhdGlibGUgd2l0aCBqUXVlcnkgVUkpIHRvIGEgalF1ZXJ5IFVJIGNvbXBhdGlibGUgZm9ybWF0XG4gICAgICpcbiAgICAgKiAgICBAcGFyYW0gICAgICAgIHtvYmplY3R9ICAgIGRhdGFzZXQgICAgICAgIEN1c3RvbSBidXR0b25zIG9iamVjdCBmb3IgdGhlIGRpYWxvZ1xuICAgICAqICAgIEBwYXJhbSAgICAgICAge3Byb21pc2V9ICAgIGRlZmVycmVkICAgIGRlZmVycmVkLW9iamVjdCB0byByZXNvbHZlIC8gcmVqZWN0IG9uIGNsb3NlXG4gICAgICogICAgQHJldHVybiAgICB7YXJyYXl9ICAgICAgICAgICAgICAgICAgICBSZXR1cm5zIGEgalF1ZXJ5IFVJIGRpYWxvZyBjb21wYXRpYmxlIGJ1dHRvbnMgYXJyYXlcbiAgICAgKi9cbiAgICB2YXIgX2dlbkJ1dHRvbnMgPSBmdW5jdGlvbiAob3B0aW9ucywgZXh0ZW5zaW9uRGVmZXJyZWQpIHtcblxuICAgICAgICAvLyBDaGVjayBpZiBidXR0b25zIGFyZSBhdmFpbGFibGVcbiAgICAgICAgaWYgKG9wdGlvbnMuYnV0dG9ucykge1xuXG4gICAgICAgICAgICB2YXIgcmVqZWN0SGFuZGxlciA9IGV4dGVuc2lvbi5nZXRSZWplY3RIYW5kbGVyLFxuICAgICAgICAgICAgICAgIHJlc29sdmVIYW5kbGVyID0gZXh0ZW5zaW9uLmdldFJlc29sdmVIYW5kbGVyO1xuXG4gICAgICAgICAgICAkLmVhY2gob3B0aW9ucy5idXR0b25zLCBmdW5jdGlvbiAoaywgdikge1xuXG4gICAgICAgICAgICAgICAgLy8gU2V0dXAgY2xpY2sgaGFuZGxlclxuICAgICAgICAgICAgICAgIG9wdGlvbnMuYnV0dG9uc1trXS5ldmVudCA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyICRzZWxmID0gJCh0aGlzKTtcblxuICAgICAgICAgICAgICAgICAgICAvLyBJZiBhIGNhbGxiYWNrIGlzIGdpdmVuLCBleGVjdXRlIGl0IHdpdGhcbiAgICAgICAgICAgICAgICAgICAgLy8gdGhlIGN1cnJlbnQgc2NvcGVcbiAgICAgICAgICAgICAgICAgICAgaWYgKHR5cGVvZiB2LmNhbGxiYWNrID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoIXYuY2FsbGJhY2suYXBwbHkoJHNlbGYsIFtdKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIC8vIEFkZCB0aGUgZGVmYXVsdCBiZWhhdmlvdXJcbiAgICAgICAgICAgICAgICAgICAgLy8gZm9yIHRoZSBjbG9zZSAgZnVuY3Rpb25hbGl0eVxuICAgICAgICAgICAgICAgICAgICAvLyBPbiBmYWlsLCByZWplY3QgdGhlIGRlZmVycmVkXG4gICAgICAgICAgICAgICAgICAgIC8vIG9iamVjdCwgZWxzZSByZXNvbHZlIGl0XG4gICAgICAgICAgICAgICAgICAgIHN3aXRjaCAodi50eXBlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjYXNlICdmYWlsJzpcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZWplY3RIYW5kbGVyKCRzZWxmLCBleHRlbnNpb25EZWZlcnJlZCwgX2dldEZvcm1EYXRhKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICAgICAgICAgIGNhc2UgJ3N1Y2Nlc3MnOlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJlc29sdmVIYW5kbGVyKCRzZWxmLCBleHRlbnNpb25EZWZlcnJlZCwgX2dldEZvcm1EYXRhKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICAgICAgICAgIGNhc2UgJ2xpbmsnOlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxvY2F0aW9uLmhyZWYgPSB2LnZhbHVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgICAgICAgICAgZGVmYXVsdDpcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH1cblxuICAgIH07XG5cblxuICAgIHZhciBfZmluYWxpemVMYXllciA9IGZ1bmN0aW9uICgkY29udGFpbmVyLCBvcHRpb25zKSB7XG4gICAgICAgIC8vIFByZXZlbnQgc3VibWl0IG9uIGVudGVyIGluIGlubmVyIGZvcm1zXG4gICAgICAgIHZhciAkZm9ybXMgPSAkY29udGFpbmVyLmZpbmQoJ2Zvcm0nKTtcbiAgICAgICAgaWYgKCRmb3Jtcy5sZW5ndGgpIHtcbiAgICAgICAgICAgICRmb3Jtcy5vbignc3VibWl0JywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmICh3aW5kb3cuZ2FtYmlvICYmIHdpbmRvdy5nYW1iaW8ud2lkZ2V0cyAmJiB3aW5kb3cuZ2FtYmlvLndpZGdldHMuaW5pdCkge1xuICAgICAgICAgICAgd2luZG93LmdhbWJpby53aWRnZXRzLmluaXQoJGNvbnRhaW5lcik7XG4gICAgICAgIH1cbiAgICB9O1xuXG4gICAgdmFyIF9zZXRMYXllciA9IGZ1bmN0aW9uIChuYW1lKSB7XG4gICAgICAgIGlmIChqc2UubGlicy50aGVtZS5tb2RhbFtuYW1lXSkge1xuICAgICAgICAgICAgZXh0ZW5zaW9uID0ganNlLmxpYnMudGhlbWUubW9kYWxbbmFtZV07XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICBqc2UuY29yZS5kZWJ1Zy5lcnJvcignW01PREFMXSBDYW5cXCd0IHNldCBtb2RhbDogXCInICsgbmFtZSArICdcIi4gRXh0ZW5zaW9uIGRvZXNuXFwndCBleGlzdCcpO1xuICAgICAgICB9XG4gICAgfTtcblxuICAgIHZhciBfdHJhbnNmZXJPcHRpb25zID0gZnVuY3Rpb24gKG9wdGlvbnMpIHtcbiAgICAgICAgdmFyIG1hcHBlciA9IGV4dGVuc2lvbi5nZXRNYXBwZXIoKSxcbiAgICAgICAgICAgIHJlc3VsdCA9IHt9O1xuXG4gICAgICAgICQuZWFjaChvcHRpb25zLCBmdW5jdGlvbiAoaywgdikge1xuXG4gICAgICAgICAgICBpZiAobWFwcGVyW2tdID09PSBmYWxzZSkge1xuICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICAgICAgfSBlbHNlIGlmIChtYXBwZXJba10gPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgIHJlc3VsdFtrXSA9IHY7XG4gICAgICAgICAgICB9IGVsc2UgaWYgKHR5cGVvZiBtYXBwZXJba10gPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgICAgICAgICB2YXIgbWFwcGVyUmVzdWx0ID0gbWFwcGVyW2tdKGssIHYpO1xuICAgICAgICAgICAgICAgIHJlc3VsdFttYXBwZXJSZXN1bHRbMF1dID0gbWFwcGVyUmVzdWx0WzFdO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICByZXN1bHRbbWFwcGVyW2tdXSA9IHY7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSk7XG5cbiAgICAgICAgcmV0dXJuIHJlc3VsdDtcblxuICAgIH07XG5cbiAgICB2YXIgX2dldFRoZW1lID0gZnVuY3Rpb24gKG9wdGlvbnMsIGlmcmFtZSkge1xuXG4gICAgICAgIHZhciAkc2VsZWN0aW9uID0gW10sXG4gICAgICAgICAgICBkZWZlcnJlZCA9ICQuRGVmZXJyZWQoKTtcblxuICAgICAgICBpZiAob3B0aW9ucy5ub1RoZW1lKSB7XG4gICAgICAgICAgICBkZWZlcnJlZC5yZXNvbHZlKCcnKTtcbiAgICAgICAgfSBlbHNlIGlmIChpZnJhbWUpIHtcbiAgICAgICAgICAgIGRlZmVycmVkLnJlc29sdmUoJzxpZnJhbWUgd2lkdGg9XCIxMDAlXCIgaGVpZ2h0PVwiMTAwJVwiIGZyYW1lYm9yZGVyPVwiMFwiIHNyYz1cIicgKyBvcHRpb25zLnRoZW1lICsgJ1wiIC8+Jyk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICBpZiAob3B0aW9ucy5zdG9yZVRoZW1lICYmIHRwbFN0b3JlW29wdGlvbnMudGhlbWVdKSB7XG4gICAgICAgICAgICAgICAgZGVmZXJyZWQucmVzb2x2ZSh0cGxTdG9yZVtvcHRpb25zLnRoZW1lXSk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgdHJ5IHtcbiAgICAgICAgICAgICAgICAgICAgJHNlbGVjdGlvbiA9ICQob3B0aW9ucy50aGVtZSk7XG4gICAgICAgICAgICAgICAgfSBjYXRjaCAoZXJyKSB7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYgKCRzZWxlY3Rpb24ubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgICAgIGRlZmVycmVkLnJlc29sdmUoJHNlbGVjdGlvbi5odG1sKCkpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIGpzZS5saWJzLnhoci5hamF4KHt1cmw6IG9wdGlvbnMudGhlbWUsIGRhdGFUeXBlOiAnaHRtbCd9KS5kb25lKGZ1bmN0aW9uIChyZXN1bHQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChvcHRpb25zLnNlY3Rpb25TZWxlY3Rvcikge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdCA9ICQocmVzdWx0KS5maW5kKG9wdGlvbnMuc2VjdGlvblNlbGVjdG9yKS5odG1sKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChvcHRpb25zLnN0b3JlVGhlbWUpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0cGxTdG9yZVtvcHRpb25zLnRoZW1lXSA9IHJlc3VsdDtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGRlZmVycmVkLnJlc29sdmUocmVzdWx0KTtcbiAgICAgICAgICAgICAgICAgICAgfSkuZmFpbChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBkZWZlcnJlZC5yZWplY3QoKTtcbiAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIGRlZmVycmVkO1xuICAgIH07XG5cbiAgICB2YXIgX2NyZWF0ZUxheWVyID0gZnVuY3Rpb24gKG9wdGlvbnMsIHRpdGxlLCBjbGFzc05hbWUsIGRlZmJ1dHRvbnMsIHRoZW1lKSB7XG4gICAgICAgIC8vIFNldHVwIGRlZmF1bHRzICYgZGVmZXJyZWQgb2JqZWN0c1xuICAgICAgICB2YXIgZGVmZXJyZWQgPSAkLkRlZmVycmVkKCksXG4gICAgICAgICAgICBwcm9taXNlID0gZGVmZXJyZWQucHJvbWlzZSgpLFxuICAgICAgICAgICAgaWZyYW1lID0gKHRoZW1lID09PSAnaWZyYW1lJyksXG4gICAgICAgICAgICBkZWZhdWx0cyA9IHtcbiAgICAgICAgICAgICAgICB0aXRsZTogdGl0bGUsXG4gICAgICAgICAgICAgICAgZGlhbG9nQ2xhc3M6IGNsYXNzTmFtZSxcbiAgICAgICAgICAgICAgICBtb2RhbDogdHJ1ZSxcbiAgICAgICAgICAgICAgICBidXR0b25zOiBkZWZidXR0b25zIHx8IFtdLFxuICAgICAgICAgICAgICAgIGNsb3NlT25Fc2NhcGU6IHRydWUsXG4gICAgICAgICAgICAgICAgdGhlbWU6IHRoZW1lIHx8IG51bGwsXG4gICAgICAgICAgICAgICAgc3RvcmVUaGVtZTogZmFsc2UsXG4gICAgICAgICAgICAgICAgY2xvc2VYOiB0cnVlLFxuICAgICAgICAgICAgICAgIGNsb3NlT25PdXRlcjogdHJ1ZVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGluc3RhbmNlID0gbnVsbCxcbiAgICAgICAgICAgICRmb3JtcyA9IG51bGwsXG4gICAgICAgICAgICBleHRlbnNpb25EZWZlcnJlZCA9ICQuRGVmZXJyZWQoKTtcblxuICAgICAgICAvLyBNZXJnZSBjdXN0b20gc2V0dGluZ3Mgd2l0aCBkZWZhdWx0IHNldHRpbmdzXG4gICAgICAgIG9wdGlvbnMgPSBvcHRpb25zIHx8IHt9O1xuICAgICAgICBvcHRpb25zID0gJC5leHRlbmQoe30sIGRlZmF1bHRzLCBvcHRpb25zKTtcblxuICAgICAgICB2YXIgdHBsUmVxdWVzdCA9IF9nZXRUaGVtZShvcHRpb25zLCBpZnJhbWUpLmRvbmUoZnVuY3Rpb24gKHJlc3VsdCkge1xuXG4gICAgICAgICAgICBleHRlbnNpb25EZWZlcnJlZC5kb25lKGZ1bmN0aW9uIChyZXN1bHQpIHtcbiAgICAgICAgICAgICAgICBkZWZlcnJlZC5yZXNvbHZlKHJlc3VsdCk7XG4gICAgICAgICAgICB9KS5mYWlsKGZ1bmN0aW9uIChyZXN1bHQpIHtcbiAgICAgICAgICAgICAgICBkZWZlcnJlZC5yZWplY3QocmVzdWx0KTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAvLyBHZW5lcmF0ZSB0aGVtZVxuICAgICAgICAgICAgb3B0aW9ucy50aGVtZSA9ICQoTXVzdGFjaGUucmVuZGVyKHJlc3VsdCwgb3B0aW9ucykpO1xuICAgICAgICAgICAganNlLmxpYnMudGhlbWUuaGVscGVycy5zZXR1cFdpZGdldEF0dHIob3B0aW9ucy50aGVtZSk7XG4gICAgICAgICAgICBvcHRpb25zLnRoZW1lID0gJCgnPGRpdj4nKS5hcHBlbmQob3B0aW9ucy50aGVtZS5jbG9uZSgpKS5odG1sKCk7XG5cbiAgICAgICAgICAgIC8vIEdlbmVyYXRlIGRlZmF1bHQgYnV0dG9uIG9iamVjdFxuICAgICAgICAgICAgX2dlbkJ1dHRvbnMob3B0aW9ucywgZXh0ZW5zaW9uRGVmZXJyZWQpO1xuXG4gICAgICAgICAgICAvLyBUcmFuc2ZlciBvcHRpb25zIG9iamVjdCB0byBleHRlbnNpb24gb3B0aW9uIG9iamVjdFxuICAgICAgICAgICAgdmFyIG9yaWdpbmFsT3B0aW9ucyA9ICQuZXh0ZW5kKHt9LCBvcHRpb25zKTtcbiAgICAgICAgICAgIG9wdGlvbnMgPSBfdHJhbnNmZXJPcHRpb25zKG9wdGlvbnMpO1xuXG4gICAgICAgICAgICAvLyBDYWxsIGV4dGVuc2lvblxuICAgICAgICAgICAgZXh0ZW5zaW9uLm9wZW5MYXllcihvcHRpb25zLCBleHRlbnNpb25EZWZlcnJlZCwgX2dldEZvcm1EYXRhLCBvcmlnaW5hbE9wdGlvbnMpO1xuXG4gICAgICAgICAgICAvLyBQYXNzdGhyb3VnaCBvZiB0aGUgY2xvc2UgbWV0aG9kIG9mIHRoZSBsYXllclxuICAgICAgICAgICAgLy8gdG8gdGhlIGxheWVyIGNhbGxlclxuICAgICAgICAgICAgcHJvbWlzZS5jbG9zZSA9IGZ1bmN0aW9uIChzdWNjZXNzKSB7XG4gICAgICAgICAgICAgICAgZXh0ZW5zaW9uRGVmZXJyZWQuY2xvc2Uoc3VjY2Vzcyk7XG4gICAgICAgICAgICB9O1xuXG4gICAgICAgIH0pLmZhaWwoZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgZGVmZXJyZWQucmVqZWN0KHtlcnJvcjogJ1RoZW1lIG5vdCBmb3VuZCd9KTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgLy8gVGVtcG9yYXJ5IGNsb3NlIGhhbmRsZXIgaWYgdGhlIHVwcGVyXG4gICAgICAgIC8vIGRlZmVycmVkIGlzbid0IGZpbmlzaGVkIG5vdy4gSXQgd2lsbCBiZVxuICAgICAgICAvLyBvdmVyd3JpdHRlbiBhZnRlciB0aGUgbGF5ZXIgb3BlbnNcbiAgICAgICAgaWYgKCFwcm9taXNlLmNsb3NlKSB7XG4gICAgICAgICAgICBwcm9taXNlLmNsb3NlID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIHRwbFJlcXVlc3QucmVqZWN0KCdDbG9zZWQgYWZ0ZXIgb3BlbmluZycpO1xuICAgICAgICAgICAgfTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBwcm9taXNlO1xuICAgIH07XG5cblxuICAgIC8qKlxuICAgICAqICAgIFNob3J0Y3V0IGZ1bmN0aW9uIGZvciBhbiBhbGVydC1sYXllclxuICAgICAqICAgIEBwYXJhbSAgICAgICAge29iamVjdH0gICAgb3B0aW9ucyBPcHRpb25zIHRoYXQgYXJlIHBhc3NlZCB0byB0aGUgbW9kYWwgbGF5ZXJcbiAgICAgKiAgICBAcmV0dXJuICAgIHtwcm9taXNlfSAgICAgICAgICAgIFJldHVybnMgYSBwcm9taXNlXG4gICAgICovXG4gICAgdmFyIF9hbGVydCA9IGZ1bmN0aW9uIChvcHRpb25zKSB7XG4gICAgICAgIHJldHVybiBfY3JlYXRlTGF5ZXIob3B0aW9ucywganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2hpbnQnLCAnbGFiZWxzJyksICcnLCBbYnV0dG9ucy5jbG9zZV0sICcjbW9kYWxfYWxlcnQnKTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogICAgU2hvcnRjdXQgZnVuY3Rpb24gZm9yIGFuIGNvbmZpcm0tbGF5ZXJcbiAgICAgKiAgICBAcGFyYW0gICAgICAgIHtvYmplY3R9ICAgIG9wdGlvbnMgT3B0aW9ucyB0aGF0IGFyZSBwYXNzZWQgdG8gdGhlIG1vZGFsIGxheWVyXG4gICAgICogICAgQHJldHVybiAgICB7cHJvbWlzZX0gICAgICAgICAgICBSZXR1cm5zIGEgcHJvbWlzZVxuICAgICAqL1xuICAgIHZhciBfY29uZmlybSA9IGZ1bmN0aW9uIChvcHRpb25zKSB7XG4gICAgICAgIHJldHVybiBfY3JlYXRlTGF5ZXIob3B0aW9ucywganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2NvbmZpcm0nLCAnbGFiZWxzJyksICdjb25maXJtX2RpYWxvZycsIFtcbiAgICAgICAgICAgIGJ1dHRvbnMueWVzLFxuICAgICAgICAgICAgYnV0dG9ucy5ub1xuICAgICAgICBdLCAnI21vZGFsX2FsZXJ0Jyk7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqICAgIFNob3J0Y3V0IGZ1bmN0aW9uIGZvciBhIHByb21wdC1sYXllclxuICAgICAqICAgIEBwYXJhbSAgICAgICAge29iamVjdH0gICAgb3B0aW9ucyBPcHRpb25zIHRoYXQgYXJlIHBhc3NlZCB0byB0aGUgbW9kYWwgbGF5ZXJcbiAgICAgKiAgICBAcmV0dXJuICAgIHtwcm9taXNlfSAgICAgICAgICAgIFJldHVybnMgYSBwcm9taXNlXG4gICAgICovXG4gICAgdmFyIF9wcm9tcHQgPSBmdW5jdGlvbiAob3B0aW9ucykge1xuICAgICAgICByZXR1cm4gX2NyZWF0ZUxheWVyKG9wdGlvbnMsIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdwcm9tcHQnLCAnbGFiZWxzJyksICdwcm9tcHRfZGlhbG9nJywgW1xuICAgICAgICAgICAgYnV0dG9ucy5vayxcbiAgICAgICAgICAgIGJ1dHRvbnMuYWJvcnRcbiAgICAgICAgXSwgJyNtb2RhbF9wcm9tcHQnKTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogICAgU2hvcnRjdXQgZnVuY3Rpb24gZm9yIGFuIHN1Y2Nlc3MtbGF5ZXJcbiAgICAgKiAgICBAcGFyYW0gICAgICAgIHtvYmplY3R9ICAgIG9wdGlvbnMgT3B0aW9ucyB0aGF0IGFyZSBwYXNzZWQgdG8gdGhlIG1vZGFsIGxheWVyXG4gICAgICogICAgQHJldHVybiAgICB7cHJvbWlzZX0gICAgICAgICAgICBSZXR1cm5zIGEgcHJvbWlzZVxuICAgICAqL1xuICAgIHZhciBfc3VjY2VzcyA9IGZ1bmN0aW9uIChvcHRpb25zKSB7XG4gICAgICAgIHJldHVybiBfY3JlYXRlTGF5ZXIob3B0aW9ucywganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3N1Y2Nlc3MnLCAnbGFiZWxzJyksICdzdWNjZXNzX2RpYWxvZycsIFtdLCAnI21vZGFsX2FsZXJ0Jyk7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqICAgIFNob3J0Y3V0IGZ1bmN0aW9uIGZvciBhbiBlcnJvci1sYXllclxuICAgICAqICAgIEBwYXJhbSAgICAgICAge29iamVjdH0gICAgb3B0aW9ucyBPcHRpb25zIHRoYXQgYXJlIHBhc3NlZCB0byB0aGUgbW9kYWwgbGF5ZXJcbiAgICAgKiAgICBAcmV0dXJuICAgIHtwcm9taXNlfSAgICAgICAgICAgIFJldHVybnMgYSBwcm9taXNlXG4gICAgICovXG4gICAgdmFyIF9lcnJvciA9IGZ1bmN0aW9uIChvcHRpb25zKSB7XG4gICAgICAgIHJldHVybiBfY3JlYXRlTGF5ZXIob3B0aW9ucywganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Vycm9ycycsICdsYWJlbHMnKSwgJ2Vycm9yX2RpYWxvZycsIFtdLCAnI21vZGFsX2FsZXJ0Jyk7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqICAgIFNob3J0Y3V0IGZ1bmN0aW9uIGZvciBhIHdhcm5pbmctbGF5ZXJcbiAgICAgKiAgICBAcGFyYW0gICAgICAgIHtvYmplY3R9ICAgIG9wdGlvbnMgT3B0aW9ucyB0aGF0IGFyZSBwYXNzZWQgdG8gdGhlIG1vZGFsIGxheWVyXG4gICAgICogICAgQHJldHVybiAgICB7cHJvbWlzZX0gICAgICAgICAgICBSZXR1cm5zIGEgcHJvbWlzZVxuICAgICAqL1xuICAgIHZhciBfd2FybiA9IGZ1bmN0aW9uIChvcHRpb25zKSB7XG4gICAgICAgIHJldHVybiBfY3JlYXRlTGF5ZXIob3B0aW9ucywganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3dhcm5pbmcnLCAnbGFiZWxzJyksICd3YXJuX2RpYWxvZycsIFtdLCAnI21vZGFsX2FsZXJ0Jyk7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqICAgIFNob3J0Y3V0IGZ1bmN0aW9uIGZvciBhbiBpbmZvLWxheWVyXG4gICAgICogICAgQHBhcmFtICAgICAgICB7b2JqZWN0fSAgICBvcHRpb25zIE9wdGlvbnMgdGhhdCBhcmUgcGFzc2VkIHRvIHRoZSBtb2RhbCBsYXllclxuICAgICAqICAgIEByZXR1cm4gICAge3Byb21pc2V9ICAgICAgICAgICAgUmV0dXJucyBhIHByb21pc2VcbiAgICAgKi9cbiAgICB2YXIgX2luZm8gPSBmdW5jdGlvbiAob3B0aW9ucykge1xuICAgICAgICByZXR1cm4gX2NyZWF0ZUxheWVyKG9wdGlvbnMsIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdpbmZvJywgJ2xhYmVscycpLCAnaW5mb19kaWFsb2cnLCBbXSwgJyNtb2RhbF9hbGVydCcpO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiAgICBTaG9ydGN1dCBmdW5jdGlvbiBmb3IgYW4gaWZyYW1lLWxheWVyXG4gICAgICogICAgQHBhcmFtICAgICAgICB7b2JqZWN0fSAgICBvcHRpb25zIE9wdGlvbnMgdGhhdCBhcmUgcGFzc2VkIHRvIHRoZSBtb2RhbCBsYXllclxuICAgICAqICAgIEByZXR1cm4gICAge3Byb21pc2V9ICAgICAgICAgICAgUmV0dXJucyBhIHByb21pc2VcbiAgICAgKi9cbiAgICB2YXIgX2lmcmFtZSA9IGZ1bmN0aW9uIChvcHRpb25zKSB7XG4gICAgICAgIGlmIChvcHRpb25zLmNvbnZlcnRNb2RhbCkge1xuICAgICAgICAgICAganNlLmxpYnMudGhlbWUubW9kYWxbb3B0aW9ucy5jb252ZXJ0TW9kYWxdKG9wdGlvbnMsIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdpbmZvJywgJ2xhYmVscycpLFxuICAgICAgICAgICAgICAgIG9wdGlvbnMuY29udmVydE1vZGFsICsgJ19kaWFsb2cnLCBbXSwgJyNtb2RhbF9hbGVydCcpO1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIF9jcmVhdGVMYXllcihvcHRpb25zLCBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnaW5mbycsICdsYWJlbHMnKSwgJ2lmcmFtZV9sYXllcicsIFtdLCAnaWZyYW1lJyk7XG4gICAgfTtcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBFWFBPUlQgIyMjIyMjIyMjI1xuXG4gICAgZXhwb3J0cy5lcnJvciA9IF9lcnJvcjtcbiAgICBleHBvcnRzLndhcm4gPSBfd2FybjtcbiAgICBleHBvcnRzLmluZm8gPSBfaW5mbztcbiAgICBleHBvcnRzLnN1Y2Nlc3MgPSBfc3VjY2VzcztcbiAgICBleHBvcnRzLmFsZXJ0ID0gX2FsZXJ0O1xuICAgIGV4cG9ydHMucHJvbXB0ID0gX3Byb21wdDtcbiAgICBleHBvcnRzLmNvbmZpcm0gPSBfY29uZmlybTtcbiAgICBleHBvcnRzLmlmcmFtZSA9IF9pZnJhbWU7XG4gICAgZXhwb3J0cy5jdXN0b20gPSBfY3JlYXRlTGF5ZXI7XG4gICAgZXhwb3J0cy5zZXRMYXllciA9IF9zZXRMYXllcjtcbiAgICBleHBvcnRzLmZpbmFsaXplTGF5ZXIgPSBfZmluYWxpemVMYXllcjtcblxuICAgIC8vIFNldCBkZWZhdWx0IGxheWVyLlxuICAgIHZhciBjdXJyZW50VGltZXN0YW1wID0gRGF0ZS5ub3csXG4gICAgICAgIGxpZmV0aW1lID0gMTAwMDA7IC8vIDEwIHNlY1xuXG4gICAgZXh0ZW5zaW9uID0ganNlLmNvcmUucmVnaXN0cnkuZ2V0KCdtYWluTW9kYWxMYXllcicpO1xuXG4gICAgdmFyIGludHYgPSBzZXRJbnRlcnZhbChmdW5jdGlvbiAoKSB7XG4gICAgICAgIGlmIChqc2UubGlicy50aGVtZS5tb2RhbFtleHRlbnNpb25dICE9PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgIF9zZXRMYXllcihleHRlbnNpb24pO1xuICAgICAgICAgICAgY2xlYXJJbnRlcnZhbChpbnR2KTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmIChEYXRlLm5vdyAtIGN1cnJlbnRUaW1lc3RhbXAgPiBsaWZldGltZSkge1xuICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKCdNb2RhbCBleHRlbnNpb24gd2FzIG5vdCBsb2FkZWQ6ICcgKyBleHRlbnNpb24pO1xuICAgICAgICB9XG4gICAgfSwgMzAwKTtcblxuXG59KGpzZS5saWJzLnRoZW1lLm1vZGFsKSk7XG5cbmpzZS5saWJzLnRlbXBsYXRlID0ganNlLmxpYnMudGVtcGxhdGUgfHwge307XG5qc2UubGlicy50ZW1wbGF0ZS5tb2RhbCA9IGpzZS5saWJzLnRoZW1lLm1vZGFsO1xuIl19
