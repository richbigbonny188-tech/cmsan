/* --------------------------------------------------------------
  CreatePurposeValidation.js 2020-01-29
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

$(document).ready(function() {

	const errorMessageSelector = '#purpose-missing-title-msg';
	const newPurposeTitleInputsAreValid = function() {

		let everyTitleHasAValue = true;

		$('.new-purpose-title').each(function() {

			if ($(this).val().length === 0) {

				everyTitleHasAValue = false;
			}
		});

		return everyTitleHasAValue;
	};

	let checkIfTitlesAreNotEmptyInterval = function() {
		
		let $errorMsg = $(errorMessageSelector);
		
		if ($errorMsg.is(":visible") && newPurposeTitleInputsAreValid()) {

			$errorMsg.slideUp();
			clearInterval(checkInterval);
		}
	};

	let checkInterval;

	$('.create-purpose-submit').click(function(event) {

		if (!newPurposeTitleInputsAreValid()) {

			$(errorMessageSelector).slideDown();

			checkInterval = setInterval(checkIfTitlesAreNotEmptyInterval, 1000);

			event.stopPropagation();
			event.preventDefault();

			return false;
		}

		return true;
	});
});
