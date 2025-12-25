/* --------------------------------------------------------------
 DebugBar.js 2018-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Debug Bar JavaScript Enhancements
 *
 * This module will perform some adjustments to the default PHP Debug Bar instance.
 */
$(function () {
    'use strict';

    var $debugBar = $('.phpdebugbar');

    if (!$debugBar.length) {
        return; // The Debug Bar is not loaded on this page or could not be detected.
    }

    // Remove the message counter from "Help" section.
    var $help = $debugBar.find('.phpdebugbar-text').filter(function () {
        return $(this).text().indexOf('Help') !== -1;
    });

    if ($help.length) {
        $help.next().remove(); // Remove the counter element.		
    }

    // Remove unnecessary line break element added by the Debug Bar when minimized.
    $debugBar.on('click', '.phpdebugbar-close-btn', function () {
        $debugBar.next('br').remove();
    });

    $debugBar.on('click', '.phpdebugbar-restore-btn', function () {
        $('<br/>').insertAfter($debugBar);
    });

    if ($debugBar.hasClass('phpdebugbar-closed')) {
        $debugBar.next('br').remove();
    }

    // Correct initial display of Debug Bar in admin layout pages.
    if ($('aside#main-menu').length) {
        setTimeout(function () {
            phpdebugbar.resize();
        }, 2000);
    }

    // Set default initial Debug Bar state to minimized.
    phpdebugbar.minimize();
});