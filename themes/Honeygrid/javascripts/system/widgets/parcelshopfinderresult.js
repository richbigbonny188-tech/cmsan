'use strict';

/* --------------------------------------------------------------
	parcelshopfinderresult.js 2017-03-21
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

gambio.widgets.module('parcelshopfinderresult', [], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        defaults = {},
        options = $.extend(true, {}, defaults, data),
        module = {};

    var initMap = function initMap() {
        var map;
        var geocoder;
        var clickMarker;
        var markers = [];
        map = new google.maps.Map(document.getElementById('map'), {
            center: markerData[0].position,
            zoom: 14
        });
        map.addListener('click', function (e) {
            console.log(e);
            geocoder.geocode({ 'location': e.latLng }, function (results, status) {
                if (status === 'OK') {
                    console.log(results);
                    if (results[0].types[0] === 'street_address') {
                        var markerAddress = results[0].formatted_address;

                        if (!clickMarker) {
                            clickMarker = new google.maps.Marker({
                                map: map,
                                position: results[0].geometry.location,
                                title: markerAddress
                            });
                        } else {
                            clickMarker.setPosition(results[0].geometry.location);
                            clickMarker.setTitle(markerAddress);
                        }

                        var street = void 0,
                            houseNo = void 0,
                            postCode = void 0,
                            city = void 0,
                            country = void 0,
                            country_iso = void 0;
                        results[0].address_components.forEach(function (component) {
                            if (component.types.indexOf('street_number') >= 0) {
                                houseNo = component.long_name;
                                $('input[name="house"]').val(houseNo);
                            }
                            if (component.types.indexOf('route') >= 0) {
                                street = component.long_name;
                                $('input[name="street"]').val(street);
                            }
                            if (component.types.indexOf('postal_code') >= 0) {
                                postCode = component.long_name;
                                $('input[name="zip"]').val(postCode);
                            }
                            if (component.types.indexOf('locality') >= 0) {
                                city = component.long_name;
                                $('input[name="city"]').val(city);
                            }
                            if (component.types.indexOf('country') >= 0) {
                                country = component.long_name;
                                country_iso = component.short_name;
                                if ($('select[name="country"] option[value="' + country_iso + '"]').length > 0) {
                                    $('select[name="country"]').val(country_iso);
                                }
                            }
                        });
                        /* $('#psf-form').show();
                        $('#psfnewsearch').hide(); */
                        $('#psf-form').trigger('submit');
                    }
                } else {
                    console.log('geocoder failed - ' + status);
                }
            });
        });
        markerData.forEach(function (markerParams) {
            markerParams.map = map;
            var newMarker = new google.maps.Marker(markerParams);
            newMarker.addListener('click', function (e) {
                var markerLabel = markerParams.label,
                    $markerRow = $('tr#marker_' + markerLabel),
                    markerRowOffset = $markerRow.offset();
                $('html, body').animate({ 'scrollTop': markerRowOffset.top - $('body').height() / 2 }, 500);
                $markerRow.addClass('marker_clicked');
                $('tr.parcelshop').not($markerRow).removeClass('marker_clicked');
                markers.forEach(function (marker, index) {
                    marker.setAnimation(null);
                });
                newMarker.setAnimation(google.maps.Animation.BOUNCE);
            });
            markers.push(newMarker);
            $('#marker_' + markerParams.label + ' div.mapmarkerlabel_icon').on('click', function (e) {
                var $markerRow = $(this).closest('tr');
                map.setCenter(newMarker.getPosition());
                markers.forEach(function (marker, index) {
                    marker.setAnimation(null);
                });
                newMarker.setAnimation(google.maps.Animation.BOUNCE);
                $markerRow.addClass('marker_clicked');
                $('tr.parcelshop').not($markerRow).removeClass('marker_clicked');
                $('#map').get(0).scrollIntoView({ behavior: "smooth", block: "end" });
            });
        });
        geocoder = new google.maps.Geocoder();
        geocoder.geocode({ 'address': searchAddress }, function (results, status) {
            if (status == 'OK') {
                map.setCenter(results[0].geometry.location);
                var marker = new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location
                });
            }
        });
    };

    // ########## INITIALIZATION ##########

    /**
     * Initialize Module
     * @constructor
     */
    module.init = function (done) {
        var toggleButton = function toggleButton(enabled) {
            if (enabled == true) {
                $('#psf_make_new_ab_entry').removeAttr('disabled');
            } else {
                $('#psf_make_new_ab_entry').attr('disabled', 'disabled');
            }
        };

        var validateInput = function validateInput() {
            toggleButton($('#psf_new_ab input').hasClass('invalid') === false);
        };

        $('button.prepare_ab_entry').on('click', function (e) {
            e.preventDefault();
            var $row = $(this).closest('tr'),
                parcelshop_heading = $('strong.parcelshop_heading', $row).text(),
                street_address = $('span.street_address', $row).text(),
                house_number = $('span.house_number', $row).text(),
                additional_info = $('span.additional_info', $row).text(),
                country_iso = $('span.country_iso', $row).text(),
                postcode = $('span.postcode', $row).text(),
                city = $('span.city', $row).text(),
                shop_name = $('span.shop_name', $row).text(),
                psf_name;
            if (country_iso !== 'DE') {
                additional_info = additional_info + ' (' + shop_name + ')';
            }
            psf_name = parcelshop_heading;
            if (shop_name.length > 0) {
                psf_name += ', ' + shop_name;
            }
            psf_name += ', ' + postcode + ' ' + city;
            $('#psf_name').val(psf_name);
            $('#psf_new_ab input[name="street_address"]').val(street_address);
            $('#psf_new_ab input[name="house_number"]').val(house_number);
            $('#psf_new_ab input[name="additional_info"]').val(additional_info);
            $('#psf_new_ab input[name="country"]').val(country_iso);
            $('#psf_new_ab input[name="postcode"]').val(postcode);
            $('#psf_new_ab input[name="city"]').val(city);
            $('#psf_name').removeClass('invalid');
            $('#psf_new_ab').show('fast', function () {
                $('#psf_new_ab').get(0).scrollIntoView({ behavior: "smooth", block: "end" });
            });
            validateInput();
        });

        $('#psf_new_ab input[name="firstname"], #psf_new_ab input[name="lastname"]').on('keyup', function (e) {
            $(e.target).toggleClass('invalid', $(e.target).val().length <= 2);
            validateInput();
        });

        $('#psf_new_ab input[name="postnumber"]').on('keyup', function (e) {
            if ($(this).val().length > 5) {
                $.ajax({
                    url: jse.core.config.get('appUrl') + '/shop.php?do=Parcelshopfinder/ValidatePostnumber&postnumber=' + $(this).val(),
                    dataType: 'json'
                }).done(function (data) {
                    $('#psf_new_ab input[name="postnumber"]').toggleClass('invalid', data.postnumberIsValid !== true);
                    validateInput();
                });
            } else {
                $('#psf_new_ab input[name="postnumber"]').toggleClass('invalid', true);
                validateInput();
            }
        });
        $('#psf_new_ab input[name="postnumber"]').on('change', function (e) {
            $('#psf_new_ab input[name="postnumber"]').trigger('keyup');
        });
        $('#psf_new_ab input').trigger('keyup');

        if (typeof psfDynamic !== 'undefined' && psfDynamic === true) {
            initMap();
        }

        done();
    };

    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcGFyY2Vsc2hvcGZpbmRlcnJlc3VsdC5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsImluaXRNYXAiLCJtYXAiLCJnZW9jb2RlciIsImNsaWNrTWFya2VyIiwibWFya2VycyIsImdvb2dsZSIsIm1hcHMiLCJNYXAiLCJkb2N1bWVudCIsImdldEVsZW1lbnRCeUlkIiwiY2VudGVyIiwibWFya2VyRGF0YSIsInBvc2l0aW9uIiwiem9vbSIsImFkZExpc3RlbmVyIiwiZSIsImNvbnNvbGUiLCJsb2ciLCJnZW9jb2RlIiwibGF0TG5nIiwicmVzdWx0cyIsInN0YXR1cyIsInR5cGVzIiwibWFya2VyQWRkcmVzcyIsImZvcm1hdHRlZF9hZGRyZXNzIiwiTWFya2VyIiwiZ2VvbWV0cnkiLCJsb2NhdGlvbiIsInRpdGxlIiwic2V0UG9zaXRpb24iLCJzZXRUaXRsZSIsInN0cmVldCIsImhvdXNlTm8iLCJwb3N0Q29kZSIsImNpdHkiLCJjb3VudHJ5IiwiY291bnRyeV9pc28iLCJhZGRyZXNzX2NvbXBvbmVudHMiLCJmb3JFYWNoIiwiY29tcG9uZW50IiwiaW5kZXhPZiIsImxvbmdfbmFtZSIsInZhbCIsInNob3J0X25hbWUiLCJsZW5ndGgiLCJ0cmlnZ2VyIiwibWFya2VyUGFyYW1zIiwibmV3TWFya2VyIiwibWFya2VyTGFiZWwiLCJsYWJlbCIsIiRtYXJrZXJSb3ciLCJtYXJrZXJSb3dPZmZzZXQiLCJvZmZzZXQiLCJhbmltYXRlIiwidG9wIiwiaGVpZ2h0IiwiYWRkQ2xhc3MiLCJub3QiLCJyZW1vdmVDbGFzcyIsIm1hcmtlciIsImluZGV4Iiwic2V0QW5pbWF0aW9uIiwiQW5pbWF0aW9uIiwiQk9VTkNFIiwicHVzaCIsIm9uIiwiY2xvc2VzdCIsInNldENlbnRlciIsImdldFBvc2l0aW9uIiwiZ2V0Iiwic2Nyb2xsSW50b1ZpZXciLCJiZWhhdmlvciIsImJsb2NrIiwiR2VvY29kZXIiLCJzZWFyY2hBZGRyZXNzIiwiaW5pdCIsImRvbmUiLCJ0b2dnbGVCdXR0b24iLCJlbmFibGVkIiwicmVtb3ZlQXR0ciIsImF0dHIiLCJ2YWxpZGF0ZUlucHV0IiwiaGFzQ2xhc3MiLCJwcmV2ZW50RGVmYXVsdCIsIiRyb3ciLCJwYXJjZWxzaG9wX2hlYWRpbmciLCJ0ZXh0Iiwic3RyZWV0X2FkZHJlc3MiLCJob3VzZV9udW1iZXIiLCJhZGRpdGlvbmFsX2luZm8iLCJwb3N0Y29kZSIsInNob3BfbmFtZSIsInBzZl9uYW1lIiwic2hvdyIsInRhcmdldCIsInRvZ2dsZUNsYXNzIiwiYWpheCIsInVybCIsImpzZSIsImNvcmUiLCJjb25maWciLCJkYXRhVHlwZSIsInBvc3RudW1iZXJJc1ZhbGlkIiwicHNmRHluYW1pYyJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FDSSx3QkFESixFQUdJLEVBSEosRUFLSSxVQUFVQyxJQUFWLEVBQWdCOztBQUVaOztBQUVBOztBQUVBLFFBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsUUFDSUMsV0FBVyxFQURmO0FBQUEsUUFFSUMsVUFBVUYsRUFBRUcsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QkgsSUFBN0IsQ0FGZDtBQUFBLFFBR0lELFNBQVMsRUFIYjs7QUFLQSxRQUFJTyxVQUFVLFNBQVZBLE9BQVUsR0FBWTtBQUN0QixZQUFJQyxHQUFKO0FBQ0EsWUFBSUMsUUFBSjtBQUNBLFlBQUlDLFdBQUo7QUFDQSxZQUFJQyxVQUFVLEVBQWQ7QUFDQUgsY0FBTSxJQUFJSSxPQUFPQyxJQUFQLENBQVlDLEdBQWhCLENBQW9CQyxTQUFTQyxjQUFULENBQXdCLEtBQXhCLENBQXBCLEVBQW9EO0FBQ3REQyxvQkFBUUMsV0FBVyxDQUFYLEVBQWNDLFFBRGdDO0FBRXREQyxrQkFBTTtBQUZnRCxTQUFwRCxDQUFOO0FBSUFa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bkYsTUFBUDtBQUNILENBL01MIiwiZmlsZSI6IndpZGdldHMvcGFyY2Vsc2hvcGZpbmRlcnJlc3VsdC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdHBhcmNlbHNob3BmaW5kZXJyZXN1bHQuanMgMjAxNy0wMy0yMVxuXHRHYW1iaW8gR21iSFxuXHRodHRwOi8vd3d3LmdhbWJpby5kZVxuXHRDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcblx0UmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG5cdFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuXHQtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuKi9cblxuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuICAgICdwYXJjZWxzaG9wZmluZGVycmVzdWx0JyxcblxuICAgIFtdLFxuXG4gICAgZnVuY3Rpb24gKGRhdGEpIHtcblxuICAgICAgICAndXNlIHN0cmljdCc7XG5cbiAgICAgICAgLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKSxcbiAgICAgICAgICAgIGRlZmF1bHRzID0ge30sXG4gICAgICAgICAgICBvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcbiAgICAgICAgICAgIG1vZHVsZSA9IHt9O1xuXG4gICAgICAgIHZhciBpbml0TWFwID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgdmFyIG1hcDtcbiAgICAgICAgICAgIHZhciBnZW9jb2RlcjtcbiAgICAgICAgICAgIHZhciBjbGlja01hcmtlcjtcbiAgICAgICAgICAgIHZhciBtYXJrZXJzID0gW107XG4gICAgICAgICAgICBtYXAgPSBuZXcgZ29vZ2xlLm1hcHMuTWFwKGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdtYXAnKSwge1xuICAgICAgICAgICAgICAgIGNlbnRlcjogbWFya2VyRGF0YVswXS5wb3NpdGlvbixcbiAgICAgICAgICAgICAgICB6b29tOiAxNFxuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICBtYXAuYWRkTGlzdGVuZXIoJ2NsaWNrJywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZyhlKTtcbiAgICAgICAgICAgICAgICBnZW9jb2Rlci5nZW9jb2RlKHsnbG9jYXRpb24nOiBlLmxhdExuZ30sIGZ1bmN0aW9uIChyZXN1bHRzLCBzdGF0dXMpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKHN0YXR1cyA9PT0gJ09LJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2cocmVzdWx0cyk7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAocmVzdWx0c1swXS50eXBlc1swXSA9PT0gJ3N0cmVldF9hZGRyZXNzJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciBtYXJrZXJBZGRyZXNzID0gcmVzdWx0c1swXS5mb3JtYXR0ZWRfYWRkcmVzcztcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmICghY2xpY2tNYXJrZXIpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgY2xpY2tNYXJrZXIgPSBuZXcgZ29vZ2xlLm1hcHMuTWFya2VyKHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIG1hcDogbWFwLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcG9zaXRpb246IHJlc3VsdHNbMF0uZ2VvbWV0cnkubG9jYXRpb24sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aXRsZTogbWFya2VyQWRkcmVzc1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBjbGlja01hcmtlci5zZXRQb3NpdGlvbihyZXN1bHRzWzBdLmdlb21ldHJ5LmxvY2F0aW9uKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgY2xpY2tNYXJrZXIuc2V0VGl0bGUobWFya2VyQWRkcmVzcyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbGV0IHN0cmVldCwgaG91c2VObywgcG9zdENvZGUsIGNpdHksIGNvdW50cnksIGNvdW50cnlfaXNvO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdHNbMF0uYWRkcmVzc19jb21wb25lbnRzLmZvckVhY2goZnVuY3Rpb24gKGNvbXBvbmVudCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAoY29tcG9uZW50LnR5cGVzLmluZGV4T2YoJ3N0cmVldF9udW1iZXInKSA+PSAwKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBob3VzZU5vID0gY29tcG9uZW50LmxvbmdfbmFtZTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICQoJ2lucHV0W25hbWU9XCJob3VzZVwiXScpLnZhbChob3VzZU5vKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAoY29tcG9uZW50LnR5cGVzLmluZGV4T2YoJ3JvdXRlJykgPj0gMCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgc3RyZWV0ID0gY29tcG9uZW50LmxvbmdfbmFtZTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICQoJ2lucHV0W25hbWU9XCJzdHJlZXRcIl0nKS52YWwoc3RyZWV0KTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAoY29tcG9uZW50LnR5cGVzLmluZGV4T2YoJ3Bvc3RhbF9jb2RlJykgPj0gMCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcG9zdENvZGUgPSBjb21wb25lbnQubG9uZ19uYW1lO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJCgnaW5wdXRbbmFtZT1cInppcFwiXScpLnZhbChwb3N0Q29kZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGNvbXBvbmVudC50eXBlcy5pbmRleE9mKCdsb2NhbGl0eScpID49IDApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNpdHkgPSBjb21wb25lbnQubG9uZ19uYW1lO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJCgnaW5wdXRbbmFtZT1cImNpdHlcIl0nKS52YWwoY2l0eSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGNvbXBvbmVudC50eXBlcy5pbmRleE9mKCdjb3VudHJ5JykgPj0gMCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgY291bnRyeSA9IGNvbXBvbmVudC5sb25nX25hbWU7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb3VudHJ5X2lzbyA9IGNvbXBvbmVudC5zaG9ydF9uYW1lO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCQoJ3NlbGVjdFtuYW1lPVwiY291bnRyeVwiXSBvcHRpb25bdmFsdWU9XCInICsgY291bnRyeV9pc28gKyAnXCJdJykubGVuZ3RoID4gMCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICQoJ3NlbGVjdFtuYW1lPVwiY291bnRyeVwiXScpLnZhbChjb3VudHJ5X2lzbyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAvKiAkKCcjcHNmLWZvcm0nKS5zaG93KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJCgnI3BzZm5ld3NlYXJjaCcpLmhpZGUoKTsgKi9cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKCcjcHNmLWZvcm0nKS50cmlnZ2VyKCdzdWJtaXQnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdnZW9jb2RlciBmYWlsZWQgLSAnICsgc3RhdHVzKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICBtYXJrZXJEYXRhLmZvckVhY2goZnVuY3Rpb24gKG1hcmtlclBhcmFtcykge1xuICAgICAgICAgICAgICAgIG1hcmtlclBhcmFtcy5tYXAgPSBtYXA7XG4gICAgICAgICAgICAgICAgdmFyIG5ld01hcmtlciA9IG5ldyBnb29nbGUubWFwcy5NYXJrZXIobWFya2VyUGFyYW1zKTtcbiAgICAgICAgICAgICAgICBuZXdNYXJrZXIuYWRkTGlzdGVuZXIoJ2NsaWNrJywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyIG1hcmtlckxhYmVsID0gbWFya2VyUGFyYW1zLmxhYmVsLFxuICAgICAgICAgICAgICAgICAgICAgICAgJG1hcmtlclJvdyA9ICQoJ3RyI21hcmtlcl8nICsgbWFya2VyTGFiZWwpLFxuICAgICAgICAgICAgICAgICAgICAgICAgbWFya2VyUm93T2Zmc2V0ID0gJG1hcmtlclJvdy5vZmZzZXQoKTtcbiAgICAgICAgICAgICAgICAgICAgJCgnaHRtbCwgYm9keScpLmFuaW1hdGUoeydzY3JvbGxUb3AnOiBtYXJrZXJSb3dPZmZzZXQudG9wIC0gKCQoJ2JvZHknKS5oZWlnaHQoKSAvIDIpfSwgNTAwKTtcbiAgICAgICAgICAgICAgICAgICAgJG1hcmtlclJvdy5hZGRDbGFzcygnbWFya2VyX2NsaWNrZWQnKTtcbiAgICAgICAgICAgICAgICAgICAgJCgndHIucGFyY2Vsc2hvcCcpLm5vdCgkbWFya2VyUm93KS5yZW1vdmVDbGFzcygnbWFya2VyX2NsaWNrZWQnKTtcbiAgICAgICAgICAgICAgICAgICAgbWFya2Vycy5mb3JFYWNoKGZ1bmN0aW9uIChtYXJrZXIsIGluZGV4KSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBtYXJrZXIuc2V0QW5pbWF0aW9uKG51bGwpO1xuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgbmV3TWFya2VyLnNldEFuaW1hdGlvbihnb29nbGUubWFwcy5BbmltYXRpb24uQk9VTkNFKTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICBtYXJrZXJzLnB1c2gobmV3TWFya2VyKTtcbiAgICAgICAgICAgICAgICAkKCcjbWFya2VyXycgKyBtYXJrZXJQYXJhbXMubGFiZWwgKyAnIGRpdi5tYXBtYXJrZXJsYWJlbF9pY29uJykub24oJ2NsaWNrJywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyICRtYXJrZXJSb3cgPSAkKHRoaXMpLmNsb3Nlc3QoJ3RyJyk7XG4gICAgICAgICAgICAgICAgICAgIG1hcC5zZXRDZW50ZXIobmV3TWFya2VyLmdldFBvc2l0aW9uKCkpO1xuICAgICAgICAgICAgICAgICAgICBtYXJrZXJzLmZvckVhY2goZnVuY3Rpb24gKG1hcmtlciwgaW5kZXgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIG1hcmtlci5zZXRBbmltYXRpb24obnVsbCk7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICBuZXdNYXJrZXIuc2V0QW5pbWF0aW9uKGdvb2dsZS5tYXBzLkFuaW1hdGlvbi5CT1VOQ0UpO1xuICAgICAgICAgICAgICAgICAgICAkbWFya2VyUm93LmFkZENsYXNzKCdtYXJrZXJfY2xpY2tlZCcpO1xuICAgICAgICAgICAgICAgICAgICAkKCd0ci5wYXJjZWxzaG9wJykubm90KCRtYXJrZXJSb3cpLnJlbW92ZUNsYXNzKCdtYXJrZXJfY2xpY2tlZCcpO1xuICAgICAgICAgICAgICAgICAgICAkKCcjbWFwJykuZ2V0KDApLnNjcm9sbEludG9WaWV3KHtiZWhhdmlvcjogXCJzbW9vdGhcIiwgYmxvY2s6IFwiZW5kXCJ9KTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgZ2VvY29kZXIgPSBuZXcgZ29vZ2xlLm1hcHMuR2VvY29kZXIoKTtcbiAgICAgICAgICAgIGdlb2NvZGVyLmdlb2NvZGUoeydhZGRyZXNzJzogc2VhcmNoQWRkcmVzc30sIGZ1bmN0aW9uIChyZXN1bHRzLCBzdGF0dXMpIHtcbiAgICAgICAgICAgICAgICBpZiAoc3RhdHVzID09ICdPSycpIHtcbiAgICAgICAgICAgICAgICAgICAgbWFwLnNldENlbnRlcihyZXN1bHRzWzBdLmdlb21ldHJ5LmxvY2F0aW9uKTtcbiAgICAgICAgICAgICAgICAgICAgdmFyIG1hcmtlciA9IG5ldyBnb29nbGUubWFwcy5NYXJrZXIoe1xuICAgICAgICAgICAgICAgICAgICAgICAgbWFwOiBtYXAsXG4gICAgICAgICAgICAgICAgICAgICAgICBwb3NpdGlvbjogcmVzdWx0c1swXS5nZW9tZXRyeS5sb2NhdGlvblxuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBJbml0aWFsaXplIE1vZHVsZVxuICAgICAgICAgKiBAY29uc3RydWN0b3JcbiAgICAgICAgICovXG4gICAgICAgIG1vZHVsZS5pbml0ID0gZnVuY3Rpb24gKGRvbmUpIHtcbiAgICAgICAgICAgIHZhciB0b2dnbGVCdXR0b24gPSBmdW5jdGlvbiAoZW5hYmxlZCkge1xuICAgICAgICAgICAgICAgIGlmIChlbmFibGVkID09IHRydWUpIHtcbiAgICAgICAgICAgICAgICAgICAgJCgnI3BzZl9tYWtlX25ld19hYl9lbnRyeScpLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgJCgnI3BzZl9tYWtlX25ld19hYl9lbnRyeScpLmF0dHIoJ2Rpc2FibGVkJywgJ2Rpc2FibGVkJyk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB2YXIgdmFsaWRhdGVJbnB1dCA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICB0b2dnbGVCdXR0b24oJCgnI3BzZl9uZXdfYWIgaW5wdXQnKS5oYXNDbGFzcygnaW52YWxpZCcpID09PSBmYWxzZSk7XG4gICAgICAgICAgICB9XG5cblxuICAgICAgICAgICAgJCgnYnV0dG9uLnByZXBhcmVfYWJfZW50cnknKS5vbignY2xpY2snLCBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICB2YXIgJHJvdyA9ICQodGhpcykuY2xvc2VzdCgndHInKSxcbiAgICAgICAgICAgICAgICAgICAgcGFyY2Vsc2hvcF9oZWFkaW5nID0gJCgnc3Ryb25nLnBhcmNlbHNob3BfaGVhZGluZycsICRyb3cpLnRleHQoKSxcbiAgICAgICAgICAgICAgICAgICAgc3RyZWV0X2FkZHJlc3MgPSAkKCdzcGFuLnN0cmVldF9hZGRyZXNzJywgJHJvdykudGV4dCgpLFxuICAgICAgICAgICAgICAgICAgICBob3VzZV9udW1iZXIgPSAkKCdzcGFuLmhvdXNlX251bWJlcicsICRyb3cpLnRleHQoKSxcbiAgICAgICAgICAgICAgICAgICAgYWRkaXRpb25hbF9pbmZvID0gJCgnc3Bhbi5hZGRpdGlvbmFsX2luZm8nLCAkcm93KS50ZXh0KCksXG4gICAgICAgICAgICAgICAgICAgIGNvdW50cnlfaXNvID0gJCgnc3Bhbi5jb3VudHJ5X2lzbycsICRyb3cpLnRleHQoKSxcbiAgICAgICAgICAgICAgICAgICAgcG9zdGNvZGUgPSAkKCdzcGFuLnBvc3Rjb2RlJywgJHJvdykudGV4dCgpLFxuICAgICAgICAgICAgICAgICAgICBjaXR5ID0gJCgnc3Bhbi5jaXR5JywgJHJvdykudGV4dCgpLFxuICAgICAgICAgICAgICAgICAgICBzaG9wX25hbWUgPSAkKCdzcGFuLnNob3BfbmFtZScsICRyb3cpLnRleHQoKSxcbiAgICAgICAgICAgICAgICAgICAgcHNmX25hbWU7XG4gICAgICAgICAgICAgICAgaWYgKGNvdW50cnlfaXNvICE9PSAnREUnKSB7XG4gICAgICAgICAgICAgICAgICAgIGFkZGl0aW9uYWxfaW5mbyA9IGFkZGl0aW9uYWxfaW5mbyArICcgKCcgKyBzaG9wX25hbWUgKyAnKSc7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIHBzZl9uYW1lID0gcGFyY2Vsc2hvcF9oZWFkaW5nO1xuICAgICAgICAgICAgICAgIGlmIChzaG9wX25hbWUubGVuZ3RoID4gMCkge1xuICAgICAgICAgICAgICAgICAgICBwc2ZfbmFtZSArPSAnLCAnICsgc2hvcF9uYW1lO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBwc2ZfbmFtZSArPSAnLCAnICsgcG9zdGNvZGUgKyAnICcgKyBjaXR5O1xuICAgICAgICAgICAgICAgICQoJyNwc2ZfbmFtZScpLnZhbChwc2ZfbmFtZSk7XG4gICAgICAgICAgICAgICAgJCgnI3BzZl9uZXdfYWIgaW5wdXRbbmFtZT1cInN0cmVldF9hZGRyZXNzXCJdJykudmFsKHN0cmVldF9hZGRyZXNzKTtcbiAgICAgICAgICAgICAgICAkKCcjcHNmX25ld19hYiBpbnB1dFtuYW1lPVwiaG91c2VfbnVtYmVyXCJdJykudmFsKGhvdXNlX251bWJlcik7XG4gICAgICAgICAgICAgICAgJCgnI3BzZl9uZXdfYWIgaW5wdXRbbmFtZT1cImFkZGl0aW9uYWxfaW5mb1wiXScpLnZhbChhZGRpdGlvbmFsX2luZm8pO1xuICAgICAgICAgICAgICAgICQoJyNwc2ZfbmV3X2FiIGlucHV0W25hbWU9XCJjb3VudHJ5XCJdJykudmFsKGNvdW50cnlfaXNvKTtcbiAgICAgICAgICAgICAgICAkKCcjcHNmX25ld19hYiBpbnB1dFtuYW1lPVwicG9zdGNvZGVcIl0nKS52YWwocG9zdGNvZGUpO1xuICAgICAgICAgICAgICAgICQoJyNwc2ZfbmV3X2FiIGlucHV0W25hbWU9XCJjaXR5XCJdJykudmFsKGNpdHkpO1xuICAgICAgICAgICAgICAgICQoJyNwc2ZfbmFtZScpLnJlbW92ZUNsYXNzKCdpbnZhbGlkJyk7XG4gICAgICAgICAgICAgICAgJCgnI3BzZl9uZXdfYWInKS5zaG93KCdmYXN0JywgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAkKCcjcHNmX25ld19hYicpLmdldCgwKS5zY3JvbGxJbnRvVmlldyh7YmVoYXZpb3I6IFwic21vb3RoXCIsIGJsb2NrOiBcImVuZFwifSk7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgdmFsaWRhdGVJbnB1dCgpO1xuICAgICAgICAgICAgfSk7XG5cblxuICAgICAgICAgICAgJCgnI3BzZl9uZXdfYWIgaW5wdXRbbmFtZT1cImZpcnN0bmFtZVwiXSwgI3BzZl9uZXdfYWIgaW5wdXRbbmFtZT1cImxhc3RuYW1lXCJdJykub24oJ2tleXVwJywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgICAgICAkKGUudGFyZ2V0KS50b2dnbGVDbGFzcygnaW52YWxpZCcsICQoZS50YXJnZXQpLnZhbCgpLmxlbmd0aCA8PSAyKTtcbiAgICAgICAgICAgICAgICB2YWxpZGF0ZUlucHV0KCk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgJCgnI3BzZl9uZXdfYWIgaW5wdXRbbmFtZT1cInBvc3RudW1iZXJcIl0nKS5vbigna2V5dXAnLCBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgICAgIGlmICgkKHRoaXMpLnZhbCgpLmxlbmd0aCA+IDUpIHtcbiAgICAgICAgICAgICAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHVybDoganNlLmNvcmUuY29uZmlnLmdldCgnYXBwVXJsJykgKyAnL3Nob3AucGhwP2RvPVBhcmNlbHNob3BmaW5kZXIvVmFsaWRhdGVQb3N0bnVtYmVyJnBvc3RudW1iZXI9JyArICQodGhpcykudmFsKCksXG4gICAgICAgICAgICAgICAgICAgICAgICBkYXRhVHlwZTogJ2pzb24nLFxuICAgICAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgICAgICAgICAgLmRvbmUoZnVuY3Rpb24gKGRhdGEpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKCcjcHNmX25ld19hYiBpbnB1dFtuYW1lPVwicG9zdG51bWJlclwiXScpLnRvZ2dsZUNsYXNzKCdpbnZhbGlkJywgKGRhdGEucG9zdG51bWJlcklzVmFsaWQgIT09IHRydWUpKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YWxpZGF0ZUlucHV0KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAkKCcjcHNmX25ld19hYiBpbnB1dFtuYW1lPVwicG9zdG51bWJlclwiXScpLnRvZ2dsZUNsYXNzKCdpbnZhbGlkJywgdHJ1ZSk7XG4gICAgICAgICAgICAgICAgICAgIHZhbGlkYXRlSW5wdXQoKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICQoJyNwc2ZfbmV3X2FiIGlucHV0W25hbWU9XCJwb3N0bnVtYmVyXCJdJykub24oJ2NoYW5nZScsIGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICAgICAgJCgnI3BzZl9uZXdfYWIgaW5wdXRbbmFtZT1cInBvc3RudW1iZXJcIl0nKS50cmlnZ2VyKCdrZXl1cCcpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAkKCcjcHNmX25ld19hYiBpbnB1dCcpLnRyaWdnZXIoJ2tleXVwJyk7XG5cbiAgICAgICAgICAgIGlmICh0eXBlb2YgKHBzZkR5bmFtaWMpICE9PSAndW5kZWZpbmVkJyAmJiBwc2ZEeW5hbWljID09PSB0cnVlKSB7XG4gICAgICAgICAgICAgICAgaW5pdE1hcCgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBkb25lKCk7XG4gICAgICAgIH07XG5cbiAgICAgICAgcmV0dXJuIG1vZHVsZTtcbiAgICB9XG4pO1xuIl19
