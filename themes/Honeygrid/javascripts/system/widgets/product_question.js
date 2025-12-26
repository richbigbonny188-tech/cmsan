'use strict';

/* --------------------------------------------------------------
 product_question.js 2023-02-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that updates that opens a lightbox for asking product questions. Sends an e-mail to the shop administrator
 * with the asked question
 */
gambio.widgets.module('product_question', ['xhr', gambio.source + '/libs/modal.ext-magnific', gambio.source + '/libs/modal'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        $body = $('body'),
        defaults = {
        btnOpen: '.btn-product-question',
        btnClose: '.btn-close-question-window',
        btnSend: '.btn-send-question',
        url: 'shop.php?do=ProductQuestion',
        sendUrl: 'shop.php?do=ProductQuestion/Send',
        productId: 0,
        formSelector: '#product-question-form',
        productFormSelector: '.js-product-form'
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## EVENT HANDLER ##########

    var _validateForm = function _validateForm() {
        try {
            var $privacyCheckbox = $('#privacy_accepted'),
                error = false;

            $this.find('.form-group.mandatory, .checkbox-inline').removeClass('has-error');

            // Validate required fields. 
            $this.find('.form-group.mandatory').each(function () {
                var $formControl = $(this).find('.form-control');

                if ($formControl.val() === '') {
                    $(this).addClass('has-error');
                    error = true;
                }
            });

            if ($privacyCheckbox.length && !$privacyCheckbox.prop('checked')) {
                $privacyCheckbox.closest('.checkbox-inline').addClass('has-error');
                error = true;
            }

            if (error) {
                throw new Error();
            }

            return true;
        } catch (exception) {
            return false;
        }
    };

    var _openModal = function _openModal(e) {
        e.preventDefault();
        var formData = $(options.productFormSelector).serialize();

        jse.libs.xhr.get({ url: options.url + '&' + formData + '&productId=' + options.productId }, true).done(function (response) {
            _closeModal();
            $body.append(response.content);
            gambio.widgets.init($('.mfp-wrap'));
            _activateGoogleRecaptcha();
            document.dispatchEvent(new Event('question-about-product-form-loaded'));
        });
    };

    var _closeModal = function _closeModal() {
        $('.mfp-bg, .mfp-wrap').remove();
        $(options.btnSend).off('click', _sendForm);
        $(options.btnClose).off('click', _closeModal);
    };

    var _sendForm = function _sendForm() {
        if (!_validateForm()) {
            return;
        }

        var formData = $(options.productFormSelector).serialize();
        var url = options.sendUrl + '&' + formData + '&productId=' + options.productId,
            data = $(options.formSelector).serialize() + '&productLink=' + location.href;

        $.ajax({
            url: url,
            data: data,
            type: 'POST',
            dataType: 'json'
        }).done(function (response) {
            _closeModal();
            $body.append(response.content);
            gambio.widgets.init($('.mfp-wrap'));

            if (!response.success) {
                _activateGoogleRecaptcha();
            }
        });
    };

    var _activateGoogleRecaptcha = function _activateGoogleRecaptcha() {
        if (typeof window.showRecaptcha === 'function') {
            setTimeout(function () {
                window.showRecaptcha('captcha_wrapper');
            }, 500);
        }
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     */
    module.init = function (done) {
        if (options.modalMode === undefined) {
            $(options.btnOpen).on('click', _openModal);
        }
        $(options.btnSend).on('click', _sendForm);
        $(options.btnClose).on('click', _closeModal);

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcHJvZHVjdF9xdWVzdGlvbi5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiRib2R5IiwiZGVmYXVsdHMiLCJidG5PcGVuIiwiYnRuQ2xvc2UiLCJidG5TZW5kIiwidXJsIiwic2VuZFVybCIsInByb2R1Y3RJZCIsImZvcm1TZWxlY3RvciIsInByb2R1Y3RGb3JtU2VsZWN0b3IiLCJvcHRpb25zIiwiZXh0ZW5kIiwiX3ZhbGlkYXRlRm9ybSIsIiRwcml2YWN5Q2hlY2tib3giLCJlcnJvciIsImZpbmQiLCJyZW1vdmVDbGFzcyIsImVhY2giLCIkZm9ybUNvbnRyb2wiLCJ2YWwiLCJhZGRDbGFzcyIsImxlbmd0aCIsInByb3AiLCJjbG9zZXN0IiwiRXJyb3IiLCJleGNlcHRpb24iLCJfb3Blbk1vZGFsIiwiZSIsInByZXZlbnREZWZhdWx0IiwiZm9ybURhdGEiLCJzZXJpYWxpemUiLCJqc2UiLCJsaWJzIiwieGhyIiwiZ2V0IiwiZG9uZSIsInJlc3BvbnNlIiwiX2Nsb3NlTW9kYWwiLCJhcHBlbmQiLCJjb250ZW50IiwiaW5pdCIsIl9hY3RpdmF0ZUdvb2dsZVJlY2FwdGNoYSIsImRvY3VtZW50IiwiZGlzcGF0Y2hFdmVudCIsIkV2ZW50IiwicmVtb3ZlIiwib2ZmIiwiX3NlbmRGb3JtIiwibG9jYXRpb24iLCJocmVmIiwiYWpheCIsInR5cGUiLCJkYXRhVHlwZSIsInN1Y2Nlc3MiLCJ3aW5kb3ciLCJzaG93UmVjYXB0Y2hhIiwic2V0VGltZW91dCIsIm1vZGFsTW9kZSIsInVuZGVmaW5lZCIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7QUFJQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0ksa0JBREosRUFHSSxDQUFDLEtBQUQsRUFBUUYsT0FBT0csTUFBUCxHQUFnQiwwQkFBeEIsRUFBb0RILE9BQU9HLE1BQVAsR0FBZ0IsYUFB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LFVBQUVXLFFBQVFOLE9BQVYsRUFBbUJ1RCxFQUFuQixDQUFzQixPQUF0QixFQUErQlosU0FBL0I7QUFDQWhELFVBQUVXLFFBQVFQLFFBQVYsRUFBb0J3RCxFQUFwQixDQUF1QixPQUF2QixFQUFnQ3RCLFdBQWhDOztBQUVBRjtBQUNILEtBUkQ7O0FBVUE7QUFDQSxXQUFPeEMsTUFBUDtBQUNILENBbklMIiwiZmlsZSI6IndpZGdldHMvcHJvZHVjdF9xdWVzdGlvbi5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gcHJvZHVjdF9xdWVzdGlvbi5qcyAyMDIzLTAyLTA2XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAyMyBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBXaWRnZXQgdGhhdCB1cGRhdGVzIHRoYXQgb3BlbnMgYSBsaWdodGJveCBmb3IgYXNraW5nIHByb2R1Y3QgcXVlc3Rpb25zLiBTZW5kcyBhbiBlLW1haWwgdG8gdGhlIHNob3AgYWRtaW5pc3RyYXRvclxuICogd2l0aCB0aGUgYXNrZWQgcXVlc3Rpb25cbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuICAgICdwcm9kdWN0X3F1ZXN0aW9uJyxcblxuICAgIFsneGhyJywgZ2FtYmlvLnNvdXJjZSArICcvbGlicy9tb2RhbC5leHQtbWFnbmlmaWMnLCBnYW1iaW8uc291cmNlICsgJy9saWJzL21vZGFsJ10sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuICAgICAgICAvLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICAgICAgJGJvZHkgPSAkKCdib2R5JyksXG4gICAgICAgICAgICBkZWZhdWx0cyA9IHtcbiAgICAgICAgICAgICAgICBidG5PcGVuOiAnLmJ0bi1wcm9kdWN0LXF1ZXN0aW9uJyxcbiAgICAgICAgICAgICAgICBidG5DbG9zZTogJy5idG4tY2xvc2UtcXVlc3Rpb24td2luZG93JyxcbiAgICAgICAgICAgICAgICBidG5TZW5kOiAnLmJ0bi1zZW5kLXF1ZXN0aW9uJyxcbiAgICAgICAgICAgICAgICB1cmw6ICdzaG9wLnBocD9kbz1Qcm9kdWN0UXVlc3Rpb24nLFxuICAgICAgICAgICAgICAgIHNlbmRVcmw6ICdzaG9wLnBocD9kbz1Qcm9kdWN0UXVlc3Rpb24vU2VuZCcsXG4gICAgICAgICAgICAgICAgcHJvZHVjdElkOiAwLFxuICAgICAgICAgICAgICAgIGZvcm1TZWxlY3RvcjogJyNwcm9kdWN0LXF1ZXN0aW9uLWZvcm0nLFxuICAgICAgICAgICAgICAgIHByb2R1Y3RGb3JtU2VsZWN0b3I6ICcuanMtcHJvZHVjdC1mb3JtJ1xuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuICAgICAgICAgICAgbW9kdWxlID0ge307XG5cblxuICAgICAgICAvLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG4gICAgICAgIHZhciBfdmFsaWRhdGVGb3JtID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgdHJ5IHtcbiAgICAgICAgICAgICAgICB2YXIgJHByaXZhY3lDaGVja2JveCA9ICQoJyNwcml2YWN5X2FjY2VwdGVkJyksXG4gICAgICAgICAgICAgICAgICAgIGVycm9yID0gZmFsc2U7XG5cbiAgICAgICAgICAgICAgICAkdGhpcy5maW5kKCcuZm9ybS1ncm91cC5tYW5kYXRvcnksIC5jaGVja2JveC1pbmxpbmUnKS5yZW1vdmVDbGFzcygnaGFzLWVycm9yJyk7XG5cbiAgICAgICAgICAgICAgICAvLyBWYWxpZGF0ZSByZXF1aXJlZCBmaWVsZHMuIFxuICAgICAgICAgICAgICAgICR0aGlzLmZpbmQoJy5mb3JtLWdyb3VwLm1hbmRhdG9yeScpLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICB2YXIgJGZvcm1Db250cm9sID0gJCh0aGlzKS5maW5kKCcuZm9ybS1jb250cm9sJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKCRmb3JtQ29udHJvbC52YWwoKSA9PT0gJycpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuYWRkQ2xhc3MoJ2hhcy1lcnJvcicpO1xuICAgICAgICAgICAgICAgICAgICAgICAgZXJyb3IgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICBpZiAoJHByaXZhY3lDaGVja2JveC5sZW5ndGggJiYgISRwcml2YWN5Q2hlY2tib3gucHJvcCgnY2hlY2tlZCcpKSB7XG4gICAgICAgICAgICAgICAgICAgICRwcml2YWN5Q2hlY2tib3guY2xvc2VzdCgnLmNoZWNrYm94LWlubGluZScpLmFkZENsYXNzKCdoYXMtZXJyb3InKTtcbiAgICAgICAgICAgICAgICAgICAgZXJyb3IgPSB0cnVlO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGlmIChlcnJvcikge1xuICAgICAgICAgICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgICAgIH0gY2F0Y2ggKGV4Y2VwdGlvbikge1xuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgICAgICB2YXIgX29wZW5Nb2RhbCA9IGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICB2YXIgZm9ybURhdGEgPSAkKG9wdGlvbnMucHJvZHVjdEZvcm1TZWxlY3Rvcikuc2VyaWFsaXplKCk7XG5cbiAgICAgICAgICAgIGpzZS5saWJzLnhoci5nZXQoe3VybDogb3B0aW9ucy51cmwgKyAnJicgKyBmb3JtRGF0YSArICcmcHJvZHVjdElkPScgKyBvcHRpb25zLnByb2R1Y3RJZH0sIHRydWUpXG4gICAgICAgICAgICAgICAgLmRvbmUoZnVuY3Rpb24gKHJlc3BvbnNlKSB7XG4gICAgICAgICAgICAgICAgICAgIF9jbG9zZU1vZGFsKCk7XG4gICAgICAgICAgICAgICAgICAgICRib2R5LmFwcGVuZChyZXNwb25zZS5jb250ZW50KTtcbiAgICAgICAgICAgICAgICAgICAgZ2FtYmlvLndpZGdldHMuaW5pdCgkKCcubWZwLXdyYXAnKSk7XG4gICAgICAgICAgICAgICAgICAgIF9hY3RpdmF0ZUdvb2dsZVJlY2FwdGNoYSgpO1xuICAgICAgICAgICAgICAgICAgICBkb2N1bWVudC5kaXNwYXRjaEV2ZW50KG5ldyBFdmVudCgncXVlc3Rpb24tYWJvdXQtcHJvZHVjdC1mb3JtLWxvYWRlZCcpKTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgfTtcblxuICAgICAgICB2YXIgX2Nsb3NlTW9kYWwgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAkKCcubWZwLWJnLCAubWZwLXdyYXAnKS5yZW1vdmUoKTtcbiAgICAgICAgICAgICQob3B0aW9ucy5idG5TZW5kKS5vZmYoJ2NsaWNrJywgX3NlbmRGb3JtKTtcbiAgICAgICAgICAgICQob3B0aW9ucy5idG5DbG9zZSkub2ZmKCdjbGljaycsIF9jbG9zZU1vZGFsKTtcbiAgICAgICAgfTtcblxuICAgICAgICB2YXIgX3NlbmRGb3JtID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgaWYgKCFfdmFsaWRhdGVGb3JtKCkpIHtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHZhciBmb3JtRGF0YSA9ICQob3B0aW9ucy5wcm9kdWN0Rm9ybVNlbGVjdG9yKS5zZXJpYWxpemUoKTtcbiAgICAgICAgICAgIHZhciB1cmwgPSBvcHRpb25zLnNlbmRVcmwgKyAnJicgKyBmb3JtRGF0YSArICcmcHJvZHVjdElkPScgKyBvcHRpb25zLnByb2R1Y3RJZCxcbiAgICAgICAgICAgICAgICBkYXRhID0gJChvcHRpb25zLmZvcm1TZWxlY3Rvcikuc2VyaWFsaXplKCkgKyAnJnByb2R1Y3RMaW5rPScgKyBsb2NhdGlvbi5ocmVmO1xuXG4gICAgICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgICAgICAgIHVybDogdXJsLFxuICAgICAgICAgICAgICAgIGRhdGE6IGRhdGEsXG4gICAgICAgICAgICAgICAgdHlwZTogJ1BPU1QnLFxuICAgICAgICAgICAgICAgIGRhdGFUeXBlOiAnanNvbidcbiAgICAgICAgICAgIH0pLmRvbmUoZnVuY3Rpb24gKHJlc3BvbnNlKSB7XG4gICAgICAgICAgICAgICAgX2Nsb3NlTW9kYWwoKTtcbiAgICAgICAgICAgICAgICAkYm9keS5hcHBlbmQocmVzcG9uc2UuY29udGVudCk7XG4gICAgICAgICAgICAgICAgZ2FtYmlvLndpZGdldHMuaW5pdCgkKCcubWZwLXdyYXAnKSk7XG5cbiAgICAgICAgICAgICAgICBpZiAoIXJlc3BvbnNlLnN1Y2Nlc3MpIHtcbiAgICAgICAgICAgICAgICAgICAgX2FjdGl2YXRlR29vZ2xlUmVjYXB0Y2hhKCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH07XG5cbiAgICAgICAgdmFyIF9hY3RpdmF0ZUdvb2dsZVJlY2FwdGNoYSA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIGlmICh0eXBlb2YgKHdpbmRvdy5zaG93UmVjYXB0Y2hhKSA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICB3aW5kb3cuc2hvd1JlY2FwdGNoYSgnY2FwdGNoYV93cmFwcGVyJyk7XG4gICAgICAgICAgICAgICAgfSwgNTAwKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgICAgICAvLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG4gICAgICAgICAqL1xuICAgICAgICBtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7XG4gICAgICAgICAgICBpZiAob3B0aW9ucy5tb2RhbE1vZGUgPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgICQob3B0aW9ucy5idG5PcGVuKS5vbignY2xpY2snLCBfb3Blbk1vZGFsKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgICQob3B0aW9ucy5idG5TZW5kKS5vbignY2xpY2snLCBfc2VuZEZvcm0pO1xuICAgICAgICAgICAgJChvcHRpb25zLmJ0bkNsb3NlKS5vbignY2xpY2snLCBfY2xvc2VNb2RhbCk7XG5cbiAgICAgICAgICAgIGRvbmUoKTtcbiAgICAgICAgfTtcblxuICAgICAgICAvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG4gICAgICAgIHJldHVybiBtb2R1bGU7XG4gICAgfSk7Il19
