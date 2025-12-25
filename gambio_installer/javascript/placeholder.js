/* --------------------------------------------------------------
  initiate_jquery_placeholder.js 2019-10-10
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

$(document).ready(function() {
	
	$('input[type="text"], input[type="password"]').each(function() {
		
		const POSITION_ADJUSTMENTS = {
			'FTP_HOST': 11, 'FTP_PASSWORD': 11, 'FTP_PORT': 11, 'FTP_USER' : 11
		};
		
		let $this = $(this),
			name = $this.attr('name'),
			placeholder = $this.attr('placeholder');
		
		if (placeholder !== undefined) {
			
			if ($this.attr('id') === undefined) {
				
				$this.attr('id', name);
			}
			
			$this.after('<label for="' + $this.attr('id') + '" class="placeholder">' + placeholder + '</label>');
			
			let $label = $('label[for="' + $this.attr('id') + '"]');
			
			if ($this.val() === '') {
				
				$label.addClass('hidden');
			}
			
			if (POSITION_ADJUSTMENTS[$this.attr('id')] !== undefined) {
				$label.css('top', ($this.position().top + POSITION_ADJUSTMENTS[$this.attr('id')] ) + 'px');
			}
			
			$this.on('keyup', function() {
				if ($this.val() === '') {
					
					$label.addClass('hidden');
				} else {
					
					$label.removeClass('hidden');
				}
			});
		}
	});
});
