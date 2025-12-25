<?php
/* --------------------------------------------------------------
   api-it-recht-kanzlei.php 2023-01-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

    based on:
            Example-Interface-Software for the transmission of legal texts between IT-Recht Kanzlei München and your system
            Script version: Draft V0.2 sw2 - 26. April 2012
            Author: Jaromedia, IT-Recht Kanzlei
            Contact: Max-Lion Keller LL.M., IT-Recht Kanzlei, Alter Messeplatz 2, 80339 Munich, Germany - Phone: +49(0)89/ 130 1433-0, Email: m.keller@it-recht-kanzlei.de

    Released under the GNU General Public License
---------------------------------------------------------------------------------------*/

use Psr\Log\LoggerInterface;
use Gambio\Core\Logging\LoggerBuilder;

$_POST_unfiltered = $_POST;
$_POST            = array();
require_once 'includes/application_top_callback.php';
require_once DIR_FS_CATALOG . 'gm/inc/gm_set_content.inc.php';
session_write_close();

class ITRechtReceiver
{
	public $module_version = '1.27';
	
	protected const LOG_FILE = 'it-recht-kanzlei-api';
	
	//	----- Settings -----
	protected $local_api_version                    = '1.0';
	protected $local_file_prefix                    = 'itrk_';
	protected $local_supported_rechtstext_types     = ['agb', 'impressum', 'datenschutz', 'widerruf'];
	protected $local_supported_rechtstext_languages = ['de', 'be', 'fr', 'nl', 'es', 'en', 'sv', 'da', 'it', 'pl'];
	protected $local_supported_actions = ['push'];
	
	// true or false (set to true for each rechtstext type where you require a pdf-file)
	protected $local_rechtstext_pdf_required = array(
		'agb'         => true,
		'impressum'   => false,
		'datenschutz' => true,
		'widerruf'    => true,
	);
	protected $local_dir_for_pdf_storage;
	// only change when told to do so, this will limit pdf downloads to a specific source host
	//protected $local_limit_download_from_host = 'www.it-recht-kanzlei.de';
	protected $local_limit_download_from_host = '';
	
	// true or false (only set to true if your system is a multishop-system, this means that under one user/password login a user manages more than one shop)
	// Gambio GX2 does not directy support multishop szenarios, so this has to remain false!
	protected $local_flag_multishop_system = false;
	
	// true or false (only set to true for testing, requires 'beispiel.xml' in DIR_FS_CATALOG.'cache')
	protected $test_with_local_xml_file = false;
	
	/** @var LoggerInterface */
    protected $logger;
    
    
    public function __construct()
	{
		$this->local_dir_for_pdf_storage = DIR_FS_CATALOG . '/media/content/';
		
		// ----- begin automatic dependant settings (do not change) -----
		// if your system is a multishop system, action 'getaccountlist' should be supported
		if($this->local_flag_multishop_system === true)
		{
			$this->local_supported_actions[] = 'getaccountlist';
		}
		// no host limit for downloading pdf when testing
		if($this->test_with_local_xml_file === true)
		{
			$this->local_limit_download_from_host = '';
		}
		// ----- end automatic dependant settings (do not change) -----
	}
	
	
	public function itrecht_log($message)
	{
		$this->getLogger()->notice($message);
	}

	public function getLogger(): LoggerInterface
    {
        if ($this->logger === null) {
            /** @var LoggerBuilder $loggerBuilder */
            $loggerBuilder       = LegacyDependencyContainer::getInstance()->get(LoggerBuilder::class);
            $this->logger        = $loggerBuilder->omitRequestData()->changeNamespace(static::LOG_FILE)->build();
        }
        return $this->logger;
    }
	
	
	protected function get_languages()
	{
		$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$query = $db->select('languages_id, code')
		            ->get('languages')
		            ->result_array();
		$languages = [];
		/** @var array $query */
		foreach($query as $row)
		{
			$languages[$row['code']] = $row['languages_id'];
		}
		return $languages;
	}
	
	
	// validate URL
	protected function url_valid($url, $limit_to_host = '')
	{
		$array_url = @parse_url($url);
		// check host limit
		if($limit_to_host !== '')
		{
			if(strtolower($array_url['host']) !== strtolower($limit_to_host))
			{
				return false;
			}
		}
		
		// check HTTP protocol
		if(in_array(strtolower($array_url['scheme']), array('http', 'https'), true) !== true)
		{
			return false;
		}
		
		// idn (funktion ggf. nicht gegeben)
		// http://de2.php.net/manual/en/function.filter-var.php#104160
		// ODER idn_to_ascii (PHP 5 >= 5.3.0, PECL intl >= 1.0.2, PECL idn >= 0.1)
		$res = filter_var($url, FILTER_VALIDATE_URL);
		if($res !== false)
		{
			return true;
		}
		
		// Check if it has unicode chars.
		$l = mb_strlen($url);
		if($l !== strlen($url))
		{
			// Replace wide chars by “X”.
			$s = str_repeat(' ', $l);
			for($i = 0; $i < $l; ++$i)
			{
				$ch     = mb_substr($url, $i, 1);
				$s [$i] = strlen($ch) > 1 ? 'X' : $ch;
			}
			// Re-check now
			$res = filter_var($s, FILTER_VALIDATE_URL);
			//if ($res) {    $url = $res; return 1;    }
			if($res !== false)
			{
				return true;
			}
		}
		else
		{
			return true;
		}
		
		return false;
	}
	
	
	// check if a file is a pdf
	protected function check_if_pdf_file($filename)
	{
		$handle   = @fopen($filename, "r");
		$contents = @fread($handle, 4);
		@fclose($handle);
		$isPdfFile = $contents === '%PDF';
		
		return $isPdfFile;
	}
	
	
	protected function itrk_retrieve_from_url($p_url)
	{
		$t_allow_url_fopen = (bool)ini_get('allow_url_fopen') === true;
		$t_data            = false;
		if($t_allow_url_fopen == true)
		{
			$t_data = @file_get_contents($p_url);
		}
		else
		{
			$t_curl_opts = array(
				CURLOPT_URL            => $p_url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT        => 15,
			);
			$t_ch        = curl_init();
			curl_setopt_array($t_ch, $t_curl_opts);
			$t_data       = curl_exec($t_ch);
			$t_curl_errno = curl_errno($t_ch);
			curl_close($t_ch);
			if($t_curl_errno > 0)
			{
				$t_data = false;
			}
		}
		
		return $t_data;
	}
	
	
	protected function itrk_convert_html($p_raw_html)
	{
		if(function_exists('iconv'))
		{
			$t_processed_html = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $p_raw_html);
			if($t_processed_html === false)
			{
				$t_processed_html = mb_convert_encoding($p_raw_html, 'ISO-8859-1', 'UTF-8');
			}
		}
		else
		{
			$t_processed_html = mb_convert_encoding($p_raw_html, 'ISO-8859-1', 'UTF-8');
		}
		
		return $t_processed_html;
	}
	
	public function fixContents()
	{
		$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$cmScriptFiles = ['itrk_agb.php', 'itrk_datenschutz.php', 'itrk_impressum.php', 'itrk_widerruf.php'];
		foreach($cmScriptFiles as $cmScriptFile)
		{
			$scriptFileContents = $db->select('content_group')
			                         ->where('content_file', $cmScriptFile)
			                         ->limit(1)
			                         ->get('content_manager')
			                         ->result_array();
			foreach($scriptFileContents as $scriptFileContent)
			{
				$contentGroup = $scriptFileContent['content_group'];
				$db->where('content_group', $contentGroup)
				   ->set('content_file', $cmScriptFile)
				   ->set('content_type', 'file')
				   ->update('content_manager');
			}
		}
	}
	
	/**
	 * @param $p_post
	 *
	 * @return bool
	 * @throws \ITRKException
	 */
	public function proceed($p_post)
	{
		// additional checks and logging
		$this->itrecht_log('INFO: API contacted by ' . $_SERVER['REMOTE_ADDR']);
		if($this->test_with_local_xml_file !== true)
		{
			if($_SERVER['REQUEST_METHOD'] !== 'POST')
			{
				throw new ITRKException('request type is not POST, aborting', '99');
			}
			if(empty($p_post['xml']))
			{
				throw new ITRKException('no XML content', '12');
			}
			
			// read POST-XML and remove form slashes
			$post_xml = $p_post['xml'];
			
			if(trim($post_xml) === '')
			{
				throw new ITRKException('XML parameter is empty', '12');
			}
			
			if(file_exists(DIR_FS_CATALOG . '/cache/itrecht-tmp.xml'))
			{
				unlink(DIR_FS_CATALOG . '/cache/itrecht-tmp.xml');
			}
			// file_put_contents(DIR_FS_CATALOG.'/cache/itrecht-tmp.xml', $post_xml);
			$xml = @simplexml_load_string($post_xml);
		}
		else
		{
			if(file_exists(DIR_FS_CATALOG . 'cache/beispiel.xml'))
			{
				$this->itrecht_log('INFO: test mode, using XML from local file');
				$xml = @simplexml_load_string(file_get_contents(DIR_FS_CATALOG . 'cache/beispiel.xml'));
			}
			else
			{
				throw new ITRKException('Cannot use local mode, XML file missing', 999);
			}
		}
		
		// Catch errors - error creating xml object
		if($xml === false)
		{
			throw new ITRKException('ERROR: XML invalid', '12');
		}
		
		// Catch errors - action not supported
		if(((string)$xml->action === '') || (in_array((string)$xml->action, $this->local_supported_actions, true) === false))
		{
			$incomingAction = isset($xml->action) ? (string)$xml->action : '-- no action --';
			throw new ITRKException('action ' . $incomingAction . 'not supported', '10');
		}
		
		// Check api-version
		if((string)$xml->api_version !== $this->local_api_version)
		{
			throw new ITRKException('API version mismatch', '1');
		}
		
		if(!empty($xml->user_auth_token))
		{
			$t_user_auth_token = (string)gm_get_conf('ITRECHT_TOKEN');
			if((string)$xml->user_auth_token !== $t_user_auth_token)
			{
				throw new ITRKException('wrong token', '3');
			}
		}
		else
		{
			throw new ITRKException('token empty', '3');
		}
		
		// ---------- begin action 'push' ----------
		if((string)$xml->action === 'push')
		{
			$this->itrecht_log('INFO: received PUSH action');
			// Catch errors - rechtstext_type
			if(((string)$xml->rechtstext_type === '')
			   || (in_array((string)$xml->rechtstext_type, $this->local_supported_rechtstext_types, true) === false))
			{
				throw new ITRKException('document type not given or unsupported ' . (string)$xml->rechtstext_type, '4');
			}
			// Catch errors - rechtstext_text
			if(strlen((string)$xml->rechtstext_text) < 50)
			{
				throw new ITRKException('text too short', '5');
			}
			// Catch errors - rechtstext_html
			if(strlen((string)$xml->rechtstext_html) < 50)
			{
				throw new ITRKException('html too short', '6');
			}
			// Catch errors - rechtstext_language
			if(((string)$xml->rechtstext_language === '')
			   || (in_array((string)$xml->rechtstext_language, $this->local_supported_rechtstext_languages, true) === false))
			{
				throw new ITRKException('language not given or unsupported', '9');
			}
			
			// check if 'user_account_id' is valid and belongs to this user or return error - for multishop systems
			if($this->local_flag_multishop_system === true)
			{
				throw new ITRKException('multishop not supported', '99');
				// Catch errors - no user_account_id transmitted
				/*
				if(trim($xml->user_account_id) === '')
				{
					throw new ITRKException('user id missing', '11');
				}
				*/
			}
			
			$rechtstext_type     = (string)$xml->rechtstext_type;
			$rechtstext_language = (string)$xml->rechtstext_language;
			$this->itrecht_log('INFO: document type: ' . $rechtstext_type);
			
			// download pdf-file and verify md5_hash. pdf-files will be stored in directory $this->local_dir_for_pdf_storage
			if($this->local_rechtstext_pdf_required[$rechtstext_type] === true)
			{
				// Catch errors - element 'rechtstext_pdf_url' empty or URL invalid
				if(((string)$xml->rechtstext_pdf_url === '')
				   || ($this->url_valid((string)$xml->rechtstext_pdf_url, $this->local_limit_download_from_host) !== true))
				{
					throw new ITRKException('URL for PDF not given or invalid - ' . (string)$xml->rechtstext_pdf_url, '7');
				}
				
				// Download pdf file
				//$file_pdf_targetfilename = md5(uniqid("")).'.pdf'; // #### adapt the created filename to your needs, if required
				$file_pdf_targetfilename = $this->local_file_prefix . $rechtstext_type . '_' . $rechtstext_language
				                           . '.pdf';
				$file_pdf_target         = $this->local_dir_for_pdf_storage . $file_pdf_targetfilename;
				$file_pdf                = @fopen($file_pdf_target, "w+");
				if($file_pdf === false)
				{
					throw new ITRKException('error writing output file ' . $file_pdf_target, '7');
				}
				
				$this->itrecht_log('INFO: retrieving PDF file ' . (string)$xml->rechtstext_pdf_url . ' as ' . $file_pdf_target);
				$t_pdf_data = $this->itrk_retrieve_from_url((string)$xml->rechtstext_pdf_url);
				$retval     = @fwrite($file_pdf, $t_pdf_data);
				if($retval === false)
				{
					throw new ITRKException('error writing output file', '7');
				}
				$retval = @fclose($file_pdf);
				if($retval === false)
				{
					throw new ITRKException('error writing output file', '7');
				}
				
				// Catch errors - downloaded file was not properly saved
				if(file_exists($file_pdf_target) !== true)
				{
					throw new ITRKException('ERROR: error writing output file', '7');
				}
				
				// verify that file is a pdf
				if($this->check_if_pdf_file($file_pdf_target) !== true)
				{
					unlink($file_pdf_target);
					throw new ITRKException('file is not a PDF document', '7');
				}
				
				// verify md5-hash, delete file if hash is not equal
				if(md5_file($file_pdf_target) !== (string)$xml->rechtstext_pdf_md5hash)
				{
					unlink($file_pdf_target);
					throw new ITRKException('md5 hash mismatch', '8');
				}
				
				$this->itrecht_log('INFO: PDF written');
			}
			
			// store legal text (rechtstext) in database/file, create your own PDF from it, ... and log this 'push'-call if needed
			$rechtstext_text = (string)$xml->rechtstext_text;
			$textfile        = DIR_FS_CATALOG . 'media/content/' . $this->local_file_prefix . $rechtstext_type . '_'
			                   . $rechtstext_language . '.txt';
			file_put_contents($textfile, $rechtstext_text);
			if(file_exists($textfile) !== true)
			{
				throw new ITRKException('ERROR: error writing text file', '7');
			}
			$this->itrecht_log('INFO: text written to ' . $textfile);
			
			$languages    = $this->get_languages();
			$languages_id = $languages[(string)$xml->rechtstext_language];
			if($rechtstext_type === 'agb' && (bool)gm_get_conf('ITRECHT_USE_AGB_IN_PDF') === true)
			{
				gm_set_content('GM_PDF_CONDITIONS', $rechtstext_text, $languages_id);
				$this->itrecht_log('INFO: Conditions (AGB) written to database for use in PDF invoices');
			}
			if($rechtstext_type === 'widerruf' && (bool)gm_get_conf('ITRECHT_USE_WITHDRAWAL_IN_PDF') === true)
			{
				gm_set_content('GM_PDF_WITHDRAWAL', $rechtstext_text, $languages_id);
				$this->itrecht_log('INFO: Withdrawal written to database for use in PDF invoices');
			}
			
			$htmlfile = DIR_FS_CATALOG . 'media/content/' . $this->local_file_prefix . $rechtstext_type . '_'
			            . $rechtstext_language . '.html';
			file_put_contents($htmlfile, (string)$xml->rechtstext_html);
			if(file_exists($htmlfile) !== true)
			{
				throw new ITRKException('error writing html file', '7');
			}
			$this->itrecht_log('INFO: html written to ' . $htmlfile);
			
			return true;
		}
	}
}

class ITRKException extends Exception
{
	protected $_errorcode;
	
	
	public function __construct($p_message, $p_errorcode)
	{
		$this->_errorcode = $p_errorcode;
		parent::__construct($p_message);
	}
	
	
	public function get_errorcode()
	{
		return $this->_errorcode;
	}
}

/* ============================================================== */

ob_start();
require DIR_FS_CATALOG . 'release_info.php';
ob_end_clean();
$t_shopversion = preg_replace('/v(.*?) .*/', '$1', $gx_version);

$coo_itrk = new ITRechtReceiver();
try
{
	$coo_itrk->proceed($_POST_unfiltered);
	$coo_itrk->fixContents();
	// output success
	header('Content-type: application/xml; charset=utf-8');
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
	echo "<response>\n";
	echo "	<status>success</status>\n";
	echo "  <meta_shopversion>" . $t_shopversion . "</meta_shopversion>\n";
	echo "  <meta_modulversion>" . $coo_itrk->module_version . "</meta_modulversion>\n";
	echo "</response>";
}
catch(ITRKException $e)
{
	$coo_itrk->itrecht_log('ERROR ' . $e->get_errorcode() . ': ' . $e->getMessage());
	// output error
	header('Content-type: application/xml; charset=utf-8');
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
	echo "<response>\n";
	echo "	<status>error</status>\n";
	echo "	<error>" . $e->get_errorcode() . "</error>\n";
	echo "  <meta_shopversion>" . $t_shopversion . "</meta_shopversion>\n";
	echo "  <meta_modulversion>" . $coo_itrk->module_version . "</meta_modulversion>\n";
	echo "</response>";
}

exit();
