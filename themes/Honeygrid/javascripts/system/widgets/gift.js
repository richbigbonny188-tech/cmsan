'use strict';

/* --------------------------------------------------------------
 gift.js 2019-03-27
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2019 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gambio.widgets.module('gift', ['xhr', 'form'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        url = null,
        defaults = {},
        selectorMapping = {
        giftContent: '.gift-cart-content-wrapper'
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    var _submitHandler = function _submitHandler(e) {
        e.preventDefault();
        e.stopPropagation();

        var dataset = jse.libs.form.getData($this);

        jse.libs.xhr.ajax({ url: url, data: dataset }, true).done(function (result) {
            jse.libs.theme.helpers.fill(result.content, $this, selectorMapping);

            var $detailsLink = $this.find('.gift-cart-show-details');
            if ($detailsLink.length) {
                $detailsLink.on('click', _showDetails);
            }
        });
    };

    var _showDetails = function _showDetails(e) {
        e.preventDefault();
        e.stopPropagation();

        var detailsUrl = $(this).data('url');

        $.ajax({
            url: detailsUrl
        }).success(function (result) {
            $('div.redeem-code-details-wrapper').html(result).show();
            $('div.redeem-code-wrapper').hide();
        });
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        url = $this.attr('action');

        $this.on('submit', _submitHandler);

        $('.gift-cart-modal').on('hidden.bs.modal', function () {
            location.reload();
        });

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvZ2lmdC5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsInVybCIsImRlZmF1bHRzIiwic2VsZWN0b3JNYXBwaW5nIiwiZ2lmdENvbnRlbnQiLCJvcHRpb25zIiwiZXh0ZW5kIiwiX3N1Ym1pdEhhbmRsZXIiLCJlIiwicHJldmVudERlZmF1bHQiLCJzdG9wUHJvcGFnYXRpb24iLCJkYXRhc2V0IiwianNlIiwibGlicyIsImZvcm0iLCJnZXREYXRhIiwieGhyIiwiYWpheCIsImRvbmUiLCJyZXN1bHQiLCJ0aGVtZSIsImhlbHBlcnMiLCJmaWxsIiwiY29udGVudCIsIiRkZXRhaWxzTGluayIsImZpbmQiLCJsZW5ndGgiLCJvbiIsIl9zaG93RGV0YWlscyIsImRldGFpbHNVcmwiLCJzdWNjZXNzIiwiaHRtbCIsInNob3ciLCJoaWRlIiwiaW5pdCIsImF0dHIiLCJsb2NhdGlvbiIsInJlbG9hZCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FDSSxNQURKLEVBR0ksQ0FBQyxLQUFELEVBQVEsTUFBUixDQUhKLEVBS0ksVUFBVUMsSUFBVixFQUFnQjs7QUFFWjs7QUFFUjs7QUFFUSxRQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLFFBQ0lDLE1BQU0sSUFEVjtBQUFBLFFBRUlDLFdBQVcsRUFGZjtBQUFBLFFBR0lDLGtCQUFrQjtBQUNkQyxxQkFBYTtBQURDLEtBSHRCO0FBQUEsUUFNSUMsVUFBVUwsRUFBRU0sTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CSixRQUFuQixFQUE2QkosSUFBN0IsQ0FOZDtBQUFBLFFBT0lELFNBQVMsRUFQYjs7QUFVQSxRQUFJVSxpQkFBaUIsU0FBakJBLGNBQWlCLENBQVVDLENBQVYsRUFBYTtBQUM5QkEsVUFBRUMsY0FBRjtBQUNBRCxVQUFFRSxlQUFGOztBQUVBLFlBQUlDLFVBQVVDLElBQUlDLElBQUosQ0FBU0MsSUFBVCxDQUFjQyxPQUFkLENBQXNCaEIsS0FBdEIsQ0FBZDs7QUFFQWEsWUFBSUMsSUFBSixDQUFTRyxHQUFULENBQWFDLElBQWIsQ0FBa0IsRUFBQ2hCLEtBQUtBLEdBQU4sRUFBV0gsTUFBTWEsT0FBakIsRUFBbEIsRUFBNkMsSUFBN0MsRUFBbURPLElBQW5ELENBQXdELFVBQVVDLE1BQVYsRUFBa0I7QUFDdEVQLGdCQUFJQyxJQUFKLENBQVNPLEtBQVQsQ0FBZUMsT0FBZixDQUF1QkMsSUFBdkIsQ0FBNEJILE9BQU9JLE9BQW5DLEVBQTRDeEIsS0FBNUMsRUFBbURJLGVBQW5EOztBQUVBLGdCQUFNcUIsZUFBZXpCLE1BQU0wQixJQUFOLENBQVcseUJBQVgsQ0FBckI7QUFDQSxnQkFBSUQsYUFBYUUsTUFBakIsRUFBeUI7QUFDckJGLDZCQUFhRyxFQUFiLENBQWdCLE9BQWhCLEVBQXlCQyxZQUF6QjtBQUNIO0FBQ0osU0FQRDtBQVNILEtBZkQ7O0FBa0JBLFFBQU1BLGVBQWUsU0FBZkEsWUFBZSxDQUFVcEIsQ0FBVixFQUFhO0FBQzlCQSxVQUFFQyxjQUFGO0FBQ0FELFVBQUVFLGVBQUY7O0FBRUEsWUFBTW1CLGFBQWE3QixFQUFFLElBQUYsRUFBUUYsSUFBUixDQUFhLEtBQWIsQ0FBbkI7O0FBRUFFLFVBQUVpQixJQUFGLENBQU87QUFDSGhCLGlCQUFLNEI7QUFERixTQUFQLEVBRUdDLE9BRkgsQ0FFVyxVQUFVWCxNQUFWLEVBQWtCO0FBQ3pCbkIsY0FBRSxpQ0FBRixFQUFxQytCLElBQXJDLENBQTBDWixNQUExQyxFQUFrRGEsSUFBbEQ7QUFDQWhDLGNBQUUseUJBQUYsRUFBNkJpQyxJQUE3QjtBQUNILFNBTEQ7QUFNSCxLQVpEOztBQWVSOztBQUVROzs7O0FBSUFwQyxXQUFPcUMsSUFBUCxHQUFjLFVBQVVoQixJQUFWLEVBQWdCOztBQUUxQmpCLGNBQU1GLE1BQU1vQyxJQUFOLENBQVcsUUFBWCxDQUFOOztBQUVBcEMsY0FBTTRCLEVBQU4sQ0FBUyxRQUFULEVBQW1CcEIsY0FBbkI7O0FBRVRQLFVBQUUsa0JBQUYsRUFBc0IyQixFQUF0QixDQUF5QixpQkFBekIsRUFBNEMsWUFBVztBQUN0RFMscUJBQVNDLE1BQVQ7QUFDQSxTQUZEOztBQUlTbkI7QUFDSCxLQVhEOztBQWFBO0FBQ0EsV0FBT3JCLE1BQVA7QUFDSCxDQTNFTCIsImZpbGUiOiJ3aWRnZXRzL2dpZnQuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGdpZnQuanMgMjAxOS0wMy0yN1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTkgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbmdhbWJpby53aWRnZXRzLm1vZHVsZShcbiAgICAnZ2lmdCcsXG5cbiAgICBbJ3hocicsICdmb3JtJ10sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKSxcbiAgICAgICAgICAgIHVybCA9IG51bGwsXG4gICAgICAgICAgICBkZWZhdWx0cyA9IHt9LFxuICAgICAgICAgICAgc2VsZWN0b3JNYXBwaW5nID0ge1xuICAgICAgICAgICAgICAgIGdpZnRDb250ZW50OiAnLmdpZnQtY2FydC1jb250ZW50LXdyYXBwZXInXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG4gICAgICAgICAgICBtb2R1bGUgPSB7fTtcblxuXG4gICAgICAgIHZhciBfc3VibWl0SGFuZGxlciA9IGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICBlLnN0b3BQcm9wYWdhdGlvbigpO1xuXG4gICAgICAgICAgICB2YXIgZGF0YXNldCA9IGpzZS5saWJzLmZvcm0uZ2V0RGF0YSgkdGhpcyk7XG5cbiAgICAgICAgICAgIGpzZS5saWJzLnhoci5hamF4KHt1cmw6IHVybCwgZGF0YTogZGF0YXNldH0sIHRydWUpLmRvbmUoZnVuY3Rpb24gKHJlc3VsdCkge1xuICAgICAgICAgICAgICAgIGpzZS5saWJzLnRoZW1lLmhlbHBlcnMuZmlsbChyZXN1bHQuY29udGVudCwgJHRoaXMsIHNlbGVjdG9yTWFwcGluZyk7XG5cbiAgICAgICAgICAgICAgICBjb25zdCAkZGV0YWlsc0xpbmsgPSAkdGhpcy5maW5kKCcuZ2lmdC1jYXJ0LXNob3ctZGV0YWlscycpO1xuICAgICAgICAgICAgICAgIGlmICgkZGV0YWlsc0xpbmsubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgICAgICRkZXRhaWxzTGluay5vbignY2xpY2snLCBfc2hvd0RldGFpbHMpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH07XG5cblxuICAgICAgICBjb25zdCBfc2hvd0RldGFpbHMgPSBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcblxuICAgICAgICAgICAgY29uc3QgZGV0YWlsc1VybCA9ICQodGhpcykuZGF0YSgndXJsJyk7XG5cbiAgICAgICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICAgICAgdXJsOiBkZXRhaWxzVXJsXG4gICAgICAgICAgICB9KS5zdWNjZXNzKGZ1bmN0aW9uIChyZXN1bHQpIHtcbiAgICAgICAgICAgICAgICAkKCdkaXYucmVkZWVtLWNvZGUtZGV0YWlscy13cmFwcGVyJykuaHRtbChyZXN1bHQpLnNob3coKTtcbiAgICAgICAgICAgICAgICAkKCdkaXYucmVkZWVtLWNvZGUtd3JhcHBlcicpLmhpZGUoKTtcbiAgICAgICAgICAgIH0pXG4gICAgICAgIH07XG5cblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuICAgICAgICAgKiBAY29uc3RydWN0b3JcbiAgICAgICAgICovXG4gICAgICAgIG1vZHVsZS5pbml0ID0gZnVuY3Rpb24gKGRvbmUpIHtcblxuICAgICAgICAgICAgdXJsID0gJHRoaXMuYXR0cignYWN0aW9uJyk7XG5cbiAgICAgICAgICAgICR0aGlzLm9uKCdzdWJtaXQnLCBfc3VibWl0SGFuZGxlcik7XG5cblx0XHRcdCQoJy5naWZ0LWNhcnQtbW9kYWwnKS5vbignaGlkZGVuLmJzLm1vZGFsJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdGxvY2F0aW9uLnJlbG9hZCgpO1xuXHRcdFx0fSk7XG5cdFx0XHRcbiAgICAgICAgICAgIGRvbmUoKTtcbiAgICAgICAgfTtcblxuICAgICAgICAvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG4gICAgICAgIHJldHVybiBtb2R1bGU7XG4gICAgfSk7Il19
