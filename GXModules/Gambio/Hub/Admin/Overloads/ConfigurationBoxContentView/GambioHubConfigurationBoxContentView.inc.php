<?php
/* --------------------------------------------------------------
   GambioHubConfigurationBoxContentView.inc.php 2018-04-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubConfigurationBoxContentView
 */
class GambioHubConfigurationBoxContentView extends GambioHubConfigurationBoxContentView_parent
{
	/**
	 * Appends JavaScript to the Gambio Hub configuration page.
	 *
	 * The appended JavaScript will create a button that will allow users to manually reset the Hub Client Key.
	 *
	 * @return string
	 */
	public function get_html()
	{
		$html = parent::get_html();

		if(isset($_GET['set'], $_GET['module'], $_GET['action'])
		   && $_GET['set'] === 'payment'
		   && $_GET['module'] === 'gambio_hub'
		   && $_GET['action'] === 'edit')
		{
			$html .= $this->generateResetButtonScript();
			$html .= $this->generateVersionInfoScript();
		}

		return $html;
	}


    /**
     * Checks the version_info folder for the latest connector version and returns it
     *
     * @return string
     */
	private function determineConnectorVersion() {
        $files = glob(DIR_FS_CATALOG . 'version_info/gambio_hub*.php');

        $gambioHubKeys = array_filter($files, function ($file) {
            return strpos($file, 'gambio_hub') !== false;
        });

        $gambioHubKeys = array_map(function ($gambioHubKey) {
            return str_replace(['gambio_hub-', '_', '.php'], ['', '.', ''], $gambioHubKey);
        }, $gambioHubKeys);

        usort($gambioHubKeys, function ($a, $b) {
            return version_compare($a, $b);
        });

        $lastGambioHubKey = basename(end($gambioHubKeys));

        return $lastGambioHubKey;
    }


    /**
     * Generates the script to display the connector version in the secret configuration
     *
     * @return string
     */
    private function generateVersionInfoScript() {

	    $connectorVersion = $this->determineConnectorVersion();

        return '<script>
                    var title = document.createElement("span");
                    title.className = "options-title";
                    title.innerHTML = "Connector Version";
                    title.style.margin = "0 0 24px 0";
                    var version = document.createElement("span");
                    version.innerHTML = "'.$connectorVersion.'";
                    var p = document.createElement("p");
                    p.appendChild(title);
                    p.appendChild(version);
					document.getElementsByClassName(\'configuration-box-form-content\')[0].prepend(p);
				</script>
			';
    }


    /**
     * Generates the reset button so we can reset hub client keys in the secret hub configuration page
     *
     * @return string
     */
	private function generateResetButtonScript() {
	    return '<script>
					var button = document.createElement("button");
					button.className = "btn btn-danger";
					button.innerHTML = "Reset Hub Client Key";
					button.style.margin = "0 0 24px 0";
					button.onclick = function() {
						if (!window.confirm("Are you sure you want to proceed?")) {
							return;
						}

					    $.post("admin.php?do=HubConfigurationAjax/deleteClientKey")
					        .done(function() { window.location.reload(); });
					};

					document.getElementsByClassName(\'configuration-box-content\')[0].appendChild(button);
				</script>
			';
    }
}
