'use strict';

/* --------------------------------------------------------------
 link_crypter.js 2016-02-02 
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that replaces the href-attributes of links with the given
 * data if the element gets in focus / hover state. Additionally
 * it is possible to remove every X sign for decryption
 */
gambio.widgets.module('link_crypter', [], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        defaults = {
        decrypt: true, // If true, it uses the period option to decrypt the links
        period: 3 // Remove every X sign of the data given for the url
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## EVENT HANDLER ##########


    /**
     * Function to replace the href value
     * with the URL or a # (depending on
     * the focus / hover state). Additionally
     * it does some decrypting optionally.
     * @param       {object}    e   jQuery-event-object which contains as data the focus state
     * @private
     */
    var _switchUrl = function _switchUrl(e) {
        var $self = $(this),
            url = $(this).parseModuleData('link_crypter').url;

        if (url) {
            if (e.data.in) {
                // Simple decryption functionality. It removes every x. sign inside the URL. 
                // x is given by options.period
                if (options.decrypt) {
                    var decryptedUrl = '';
                    for (var i = 0; i < url.length; i++) {
                        if (i % options.period) {
                            decryptedUrl += url.charAt(i);
                        }
                    }
                    url = decryptedUrl;
                }
                $self.attr('href', url);
            } else {
                $self.attr('href', '#');
            }
            $self.attr('target', '_top');
        }
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {
        $this.on('mouseenter focus', 'a', { in: true }, _switchUrl).on('mouseleave blur', 'a', { in: false }, _switchUrl);

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvbGlua19jcnlwdGVyLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJkZWNyeXB0IiwicGVyaW9kIiwib3B0aW9ucyIsImV4dGVuZCIsIl9zd2l0Y2hVcmwiLCJlIiwiJHNlbGYiLCJ1cmwiLCJwYXJzZU1vZHVsZURhdGEiLCJpbiIsImRlY3J5cHRlZFVybCIsImkiLCJsZW5ndGgiLCJjaGFyQXQiLCJhdHRyIiwiaW5pdCIsImRvbmUiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7OztBQUtBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FBc0IsY0FBdEIsRUFBc0MsRUFBdEMsRUFBMEMsVUFBVUMsSUFBVixFQUFnQjs7QUFFdEQ7O0FBRUo7O0FBRUksUUFBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxRQUNJQyxXQUFXO0FBQ1BDLGlCQUFTLElBREYsRUFDUTtBQUNmQyxnQkFBUSxDQUZELENBRUc7QUFGSCxLQURmO0FBQUEsUUFLSUMsVUFBVUosRUFBRUssTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CSixRQUFuQixFQUE2QkgsSUFBN0IsQ0FMZDtBQUFBLFFBTUlELFNBQVMsRUFOYjs7QUFTSjs7O0FBR0k7Ozs7Ozs7O0FBUUEsUUFBSVMsYUFBYSxTQUFiQSxVQUFhLENBQVVDLENBQVYsRUFBYTtBQUMxQixZQUFJQyxRQUFRUixFQUFFLElBQUYsQ0FBWjtBQUFBLFlBQ0lTLE1BQU1ULEVBQUUsSUFBRixFQUFRVSxlQUFSLENBQXdCLGNBQXhCLEVBQXdDRCxHQURsRDs7QUFHQSxZQUFJQSxHQUFKLEVBQVM7QUFDTCxnQkFBSUYsRUFBRVQsSUFBRixDQUFPYSxFQUFYLEVBQWU7QUFDWDtBQUNBO0FBQ0Esb0JBQUlQLFFBQVFGLE9BQVosRUFBcUI7QUFDakIsd0JBQUlVLGVBQWUsRUFBbkI7QUFDQSx5QkFBSyxJQUFJQyxJQUFJLENBQWIsRUFBZ0JBLElBQUlKLElBQUlLLE1BQXhCLEVBQWdDRCxHQUFoQyxFQUFxQztBQUNqQyw0QkFBSUEsSUFBSVQsUUFBUUQsTUFBaEIsRUFBd0I7QUFDcEJTLDRDQUFnQkgsSUFBSU0sTUFBSixDQUFXRixDQUFYLENBQWhCO0FBQ0g7QUFDSjtBQUNESiwwQkFBTUcsWUFBTjtBQUNIO0FBQ0RKLHNCQUFNUSxJQUFOLENBQVcsTUFBWCxFQUFtQlAsR0FBbkI7QUFDSCxhQWJELE1BYU87QUFDSEQsc0JBQU1RLElBQU4sQ0FBVyxNQUFYLEVBQW1CLEdBQW5CO0FBQ0g7QUFDRFIsa0JBQU1RLElBQU4sQ0FBVyxRQUFYLEVBQXFCLE1BQXJCO0FBQ0g7QUFDSixLQXZCRDs7QUF5Qko7O0FBRUk7Ozs7QUFJQW5CLFdBQU9vQixJQUFQLEdBQWMsVUFBVUMsSUFBVixFQUFnQjtBQUMxQm5CLGNBQ0tvQixFQURMLENBQ1Esa0JBRFIsRUFDNEIsR0FENUIsRUFDaUMsRUFBQ1IsSUFBSSxJQUFMLEVBRGpDLEVBQzZDTCxVQUQ3QyxFQUVLYSxFQUZMLENBRVEsaUJBRlIsRUFFMkIsR0FGM0IsRUFFZ0MsRUFBQ1IsSUFBSSxLQUFMLEVBRmhDLEVBRTZDTCxVQUY3Qzs7QUFJQVk7QUFDSCxLQU5EOztBQVFBO0FBQ0EsV0FBT3JCLE1BQVA7QUFDSCxDQW5FRCIsImZpbGUiOiJ3aWRnZXRzL2xpbmtfY3J5cHRlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gbGlua19jcnlwdGVyLmpzIDIwMTYtMDItMDIgXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBXaWRnZXQgdGhhdCByZXBsYWNlcyB0aGUgaHJlZi1hdHRyaWJ1dGVzIG9mIGxpbmtzIHdpdGggdGhlIGdpdmVuXG4gKiBkYXRhIGlmIHRoZSBlbGVtZW50IGdldHMgaW4gZm9jdXMgLyBob3ZlciBzdGF0ZS4gQWRkaXRpb25hbGx5XG4gKiBpdCBpcyBwb3NzaWJsZSB0byByZW1vdmUgZXZlcnkgWCBzaWduIGZvciBkZWNyeXB0aW9uXG4gKi9cbmdhbWJpby53aWRnZXRzLm1vZHVsZSgnbGlua19jcnlwdGVyJywgW10sIGZ1bmN0aW9uIChkYXRhKSB7XG5cbiAgICAndXNlIHN0cmljdCc7XG5cbi8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgdmFyICR0aGlzID0gJCh0aGlzKSxcbiAgICAgICAgZGVmYXVsdHMgPSB7XG4gICAgICAgICAgICBkZWNyeXB0OiB0cnVlLCAvLyBJZiB0cnVlLCBpdCB1c2VzIHRoZSBwZXJpb2Qgb3B0aW9uIHRvIGRlY3J5cHQgdGhlIGxpbmtzXG4gICAgICAgICAgICBwZXJpb2Q6IDMgLy8gUmVtb3ZlIGV2ZXJ5IFggc2lnbiBvZiB0aGUgZGF0YSBnaXZlbiBmb3IgdGhlIHVybFxuICAgICAgICB9LFxuICAgICAgICBvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcbiAgICAgICAgbW9kdWxlID0ge307XG5cblxuLy8gIyMjIyMjIyMjIyBFVkVOVCBIQU5ETEVSICMjIyMjIyMjIyNcblxuXG4gICAgLyoqXG4gICAgICogRnVuY3Rpb24gdG8gcmVwbGFjZSB0aGUgaHJlZiB2YWx1ZVxuICAgICAqIHdpdGggdGhlIFVSTCBvciBhICMgKGRlcGVuZGluZyBvblxuICAgICAqIHRoZSBmb2N1cyAvIGhvdmVyIHN0YXRlKS4gQWRkaXRpb25hbGx5XG4gICAgICogaXQgZG9lcyBzb21lIGRlY3J5cHRpbmcgb3B0aW9uYWxseS5cbiAgICAgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgZSAgIGpRdWVyeS1ldmVudC1vYmplY3Qgd2hpY2ggY29udGFpbnMgYXMgZGF0YSB0aGUgZm9jdXMgc3RhdGVcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIHZhciBfc3dpdGNoVXJsID0gZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgdmFyICRzZWxmID0gJCh0aGlzKSxcbiAgICAgICAgICAgIHVybCA9ICQodGhpcykucGFyc2VNb2R1bGVEYXRhKCdsaW5rX2NyeXB0ZXInKS51cmw7XG5cbiAgICAgICAgaWYgKHVybCkge1xuICAgICAgICAgICAgaWYgKGUuZGF0YS5pbikge1xuICAgICAgICAgICAgICAgIC8vIFNpbXBsZSBkZWNyeXB0aW9uIGZ1bmN0aW9uYWxpdHkuIEl0IHJlbW92ZXMgZXZlcnkgeC4gc2lnbiBpbnNpZGUgdGhlIFVSTC4gXG4gICAgICAgICAgICAgICAgLy8geCBpcyBnaXZlbiBieSBvcHRpb25zLnBlcmlvZFxuICAgICAgICAgICAgICAgIGlmIChvcHRpb25zLmRlY3J5cHQpIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyIGRlY3J5cHRlZFVybCA9ICcnO1xuICAgICAgICAgICAgICAgICAgICBmb3IgKHZhciBpID0gMDsgaSA8IHVybC5sZW5ndGg7IGkrKykge1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGkgJSBvcHRpb25zLnBlcmlvZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRlY3J5cHRlZFVybCArPSB1cmwuY2hhckF0KGkpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIHVybCA9IGRlY3J5cHRlZFVybDtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgJHNlbGYuYXR0cignaHJlZicsIHVybCk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICRzZWxmLmF0dHIoJ2hyZWYnLCAnIycpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgJHNlbGYuYXR0cigndGFyZ2V0JywgJ190b3AnKTtcbiAgICAgICAgfVxuICAgIH07XG5cbi8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgLyoqXG4gICAgICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG4gICAgICogQGNvbnN0cnVjdG9yXG4gICAgICovXG4gICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuICAgICAgICAkdGhpc1xuICAgICAgICAgICAgLm9uKCdtb3VzZWVudGVyIGZvY3VzJywgJ2EnLCB7aW46IHRydWV9LCBfc3dpdGNoVXJsKVxuICAgICAgICAgICAgLm9uKCdtb3VzZWxlYXZlIGJsdXInLCAnYScsIHtpbjogZmFsc2V9LCBfc3dpdGNoVXJsKTtcblxuICAgICAgICBkb25lKCk7XG4gICAgfTtcblxuICAgIC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcbiAgICByZXR1cm4gbW9kdWxlO1xufSk7XG4iXX0=
