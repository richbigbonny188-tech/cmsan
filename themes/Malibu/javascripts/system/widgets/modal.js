'use strict';

/* --------------------------------------------------------------
 modal.js 2019-03-27
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2019 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Component that opens a modal layer with the URL given by
 * an a-tag that has the class "js-open-modal". For backwards
 * compatibility the class "lightbox_iframe" is possible, also.
 */
gambio.widgets.module('modal', [gambio.source + '/libs/modal.ext-magnific', gambio.source + '/libs/modal'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        defaults = {
        add: '&lightbox_mode=1' // Add this parameter to each URL
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## EVENT HANDLER ##########

    /**
     * Event handler to open the modal
     * window with the link data
     * @param       {object}    e       jQuery event object
     * @private
     */
    var _openModal = function _openModal(e) {
        e.preventDefault();

        var $self = $(this),
            url = $self.attr('href'),
            dataset = $self.parseModuleData('modal'),
            type = dataset.type || e.data.type,
            settings = $.extend({}, dataset.settings || {});

        url += url[0] === '#' || url[0] === '.' ? '' : options.add;
        settings.theme = url;
        if (settings.refreshOnClose) {
            settings.callbacks = {
                "close": function close() {
                    location.reload();
                }
            };
        }

        jse.libs.theme.modal[type](settings);
        if (dataset.finishEvent) {
            $('body').trigger(dataset.finishEvent);
        }
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        $this.on('click', '.js-open-modal', _openModal).on('click', '.lightbox_iframe', { type: 'iframe' }, _openModal);

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvbW9kYWwuanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsInNvdXJjZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsImFkZCIsIm9wdGlvbnMiLCJleHRlbmQiLCJfb3Blbk1vZGFsIiwiZSIsInByZXZlbnREZWZhdWx0IiwiJHNlbGYiLCJ1cmwiLCJhdHRyIiwiZGF0YXNldCIsInBhcnNlTW9kdWxlRGF0YSIsInR5cGUiLCJzZXR0aW5ncyIsInRoZW1lIiwicmVmcmVzaE9uQ2xvc2UiLCJjYWxsYmFja3MiLCJsb2NhdGlvbiIsInJlbG9hZCIsImpzZSIsImxpYnMiLCJtb2RhbCIsImZpbmlzaEV2ZW50IiwidHJpZ2dlciIsImluaXQiLCJkb25lIiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7QUFLQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0ksT0FESixFQUdJLENBQ0lGLE9BQU9HLE1BQVAsR0FBZ0IsMEJBRHBCLEVBRUlILE9BQU9HLE1BQVAsR0FBZ0IsYUFGcEIsQ0FISixFQVFJLFVBQVVDLElBQVYsRUFBZ0I7O0FBRVo7O0FBRVI7O0FBRVEsUUFBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxRQUNJQyxXQUFXO0FBQ1BDLGFBQUssa0JBREUsQ0FDb0I7QUFEcEIsS0FEZjtBQUFBLFFBSUlDLFVBQVVILEVBQUVJLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkgsUUFBbkIsRUFBNkJILElBQTdCLENBSmQ7QUFBQSxRQUtJRixTQUFTLEVBTGI7O0FBT1I7O0FBRVE7Ozs7OztBQU1BLFFBQUlTLGFBQWEsU0FBYkEsVUFBYSxDQUFVQyxDQUFWLEVBQWE7QUFDMUJBLFVBQUVDLGNBQUY7O0FBRUEsWUFBSUMsUUFBUVIsRUFBRSxJQUFGLENBQVo7QUFBQSxZQUNJUyxNQUFNRCxNQUFNRSxJQUFOLENBQVcsTUFBWCxDQURWO0FBQUEsWUFFSUMsVUFBVUgsTUFBTUksZUFBTixDQUFzQixPQUF0QixDQUZkO0FBQUEsWUFHSUMsT0FBT0YsUUFBUUUsSUFBUixJQUFnQlAsRUFBRVIsSUFBRixDQUFPZSxJQUhsQztBQUFBLFlBSUlDLFdBQVdkLEVBQUVJLE1BQUYsQ0FBUyxFQUFULEVBQWFPLFFBQVFHLFFBQVIsSUFBb0IsRUFBakMsQ0FKZjs7QUFNQUwsZUFBUUEsSUFBSSxDQUFKLE1BQVcsR0FBWCxJQUFrQkEsSUFBSSxDQUFKLE1BQVcsR0FBOUIsR0FBcUMsRUFBckMsR0FBMENOLFFBQVFELEdBQXpEO0FBQ0FZLGlCQUFTQyxLQUFULEdBQWlCTixHQUFqQjtBQUNULFlBQUdLLFNBQVNFLGNBQVosRUFBNEI7QUFDM0JGLHFCQUFTRyxTQUFULEdBQXFCO0FBQ3BCLHlCQUFTLGlCQUFXO0FBQ25CQyw2QkFBU0MsTUFBVDtBQUNBO0FBSG1CLGFBQXJCO0FBS0E7O0FBRVFDLFlBQUlDLElBQUosQ0FBU04sS0FBVCxDQUFlTyxLQUFmLENBQXFCVCxJQUFyQixFQUEyQkMsUUFBM0I7QUFDQSxZQUFJSCxRQUFRWSxXQUFaLEVBQXlCO0FBQ3JCdkIsY0FBRSxNQUFGLEVBQVV3QixPQUFWLENBQWtCYixRQUFRWSxXQUExQjtBQUNIO0FBQ0osS0F2QkQ7O0FBeUJSOztBQUVROzs7O0FBSUEzQixXQUFPNkIsSUFBUCxHQUFjLFVBQVVDLElBQVYsRUFBZ0I7O0FBRTFCM0IsY0FDSzRCLEVBREwsQ0FDUSxPQURSLEVBQ2lCLGdCQURqQixFQUNtQ3RCLFVBRG5DLEVBRUtzQixFQUZMLENBRVEsT0FGUixFQUVpQixrQkFGakIsRUFFcUMsRUFBQ2QsTUFBTSxRQUFQLEVBRnJDLEVBRXVEUixVQUZ2RDs7QUFJQXFCO0FBQ0gsS0FQRDs7QUFTQTtBQUNBLFdBQU85QixNQUFQO0FBQ0gsQ0F2RUwiLCJmaWxlIjoid2lkZ2V0cy9tb2RhbC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gbW9kYWwuanMgMjAxOS0wMy0yN1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTkgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogQ29tcG9uZW50IHRoYXQgb3BlbnMgYSBtb2RhbCBsYXllciB3aXRoIHRoZSBVUkwgZ2l2ZW4gYnlcbiAqIGFuIGEtdGFnIHRoYXQgaGFzIHRoZSBjbGFzcyBcImpzLW9wZW4tbW9kYWxcIi4gRm9yIGJhY2t3YXJkc1xuICogY29tcGF0aWJpbGl0eSB0aGUgY2xhc3MgXCJsaWdodGJveF9pZnJhbWVcIiBpcyBwb3NzaWJsZSwgYWxzby5cbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuICAgICdtb2RhbCcsXG5cbiAgICBbXG4gICAgICAgIGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvbW9kYWwuZXh0LW1hZ25pZmljJyxcbiAgICAgICAgZ2FtYmlvLnNvdXJjZSArICcvbGlicy9tb2RhbCdcbiAgICBdLFxuXG4gICAgZnVuY3Rpb24gKGRhdGEpIHtcblxuICAgICAgICAndXNlIHN0cmljdCc7XG5cbi8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgICAgIHZhciAkdGhpcyA9ICQodGhpcyksXG4gICAgICAgICAgICBkZWZhdWx0cyA9IHtcbiAgICAgICAgICAgICAgICBhZGQ6ICcmbGlnaHRib3hfbW9kZT0xJywgICAvLyBBZGQgdGhpcyBwYXJhbWV0ZXIgdG8gZWFjaCBVUkxcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcbiAgICAgICAgICAgIG1vZHVsZSA9IHt9O1xuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBFdmVudCBoYW5kbGVyIHRvIG9wZW4gdGhlIG1vZGFsXG4gICAgICAgICAqIHdpbmRvdyB3aXRoIHRoZSBsaW5rIGRhdGFcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9vcGVuTW9kYWwgPSBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgICAgICB2YXIgJHNlbGYgPSAkKHRoaXMpLFxuICAgICAgICAgICAgICAgIHVybCA9ICRzZWxmLmF0dHIoJ2hyZWYnKSxcbiAgICAgICAgICAgICAgICBkYXRhc2V0ID0gJHNlbGYucGFyc2VNb2R1bGVEYXRhKCdtb2RhbCcpLFxuICAgICAgICAgICAgICAgIHR5cGUgPSBkYXRhc2V0LnR5cGUgfHwgZS5kYXRhLnR5cGUsXG4gICAgICAgICAgICAgICAgc2V0dGluZ3MgPSAkLmV4dGVuZCh7fSwgZGF0YXNldC5zZXR0aW5ncyB8fCB7fSk7XG5cbiAgICAgICAgICAgIHVybCArPSAodXJsWzBdID09PSAnIycgfHwgdXJsWzBdID09PSAnLicpID8gJycgOiBvcHRpb25zLmFkZDtcbiAgICAgICAgICAgIHNldHRpbmdzLnRoZW1lID0gdXJsO1xuXHRcdFx0aWYoc2V0dGluZ3MucmVmcmVzaE9uQ2xvc2UpIHtcblx0XHRcdFx0c2V0dGluZ3MuY2FsbGJhY2tzID0ge1xuXHRcdFx0XHRcdFwiY2xvc2VcIjogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRsb2NhdGlvbi5yZWxvYWQoKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH07XG5cdFx0XHR9XG5cbiAgICAgICAgICAgIGpzZS5saWJzLnRoZW1lLm1vZGFsW3R5cGVdKHNldHRpbmdzKTtcbiAgICAgICAgICAgIGlmIChkYXRhc2V0LmZpbmlzaEV2ZW50KSB7XG4gICAgICAgICAgICAgICAgJCgnYm9keScpLnRyaWdnZXIoZGF0YXNldC5maW5pc2hFdmVudCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbi8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcbiAgICAgICAgICogQGNvbnN0cnVjdG9yXG4gICAgICAgICAqL1xuICAgICAgICBtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7XG5cbiAgICAgICAgICAgICR0aGlzXG4gICAgICAgICAgICAgICAgLm9uKCdjbGljaycsICcuanMtb3Blbi1tb2RhbCcsIF9vcGVuTW9kYWwpXG4gICAgICAgICAgICAgICAgLm9uKCdjbGljaycsICcubGlnaHRib3hfaWZyYW1lJywge3R5cGU6ICdpZnJhbWUnfSwgX29wZW5Nb2RhbCk7XG5cbiAgICAgICAgICAgIGRvbmUoKTtcbiAgICAgICAgfTtcblxuICAgICAgICAvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG4gICAgICAgIHJldHVybiBtb2R1bGU7XG4gICAgfSk7XG4iXX0=
