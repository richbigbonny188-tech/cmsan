/* --------------------------------------------------------------
 inetmarke_config_controller.js 2018-04-04
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gxmodules.controllers.module(
	'inetmarke_config_controller',

	[],

	function (data) {
		'use strict';
		
		/**
		 * Module Selector
		 *
		 * @type {jQuery}
		 */
		const $this = $(this);
		
		/**
		 * Default Options
		 *
		 * @type {object}
		 */
		const defaults = {};
		
		/**
		 * Final Options
		 *
		 * @type {object}
		 */
		const options = $.extend(true, {}, defaults, data);
		
		/**
		 * Module Object
		 *
		 * @type {object}
		 */
		const module = {};
		
		module.init = function(done) {
			$('head').append($('<link rel="stylesheet" href="'+jse.core.config.get('appUrl')+'/admin/html/assets/styles/modules/internetmarke.min.css">'));

			$('#inetmarke_config div.imgcat').on('click', function() {
				var catid = $(this).data('catid');
				$('#inetmarke_config div.imgcat').removeClass('open');
				$(this).addClass('open');
				$('#inetmarke_config div.imglist').hide('fast');
				$('div.imglist#imglist_'+catid).show('fast');
			});
			var selected_catid = $('#inetmarke_config input[name="configuration[prefs_imageid]"]:checked').data('catid');
			$('#inetmarke_config div.imgcat[data-catid="'+selected_catid+'"]').trigger('click');

			done();
		};

		return module;
	}
);
