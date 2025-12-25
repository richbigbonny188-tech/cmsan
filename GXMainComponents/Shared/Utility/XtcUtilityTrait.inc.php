<?php
/* --------------------------------------------------------------
   XtcUtilityTrait.inc.php 2018-02-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once __DIR__ . '/StringUtilityTrait.inc.php';

trait XtcUtilityTrait
{
    use StringUtilityTrait;
    
    
    /**
     * IMPORTANT NOTE:
     * The algorithm and PHPDoc block is copied from the xtc_date_raw() function.
     * The function file can be found here: src/inc/xtc_date_raw.inc.php
     *
     *
     * Return date in raw format.
     *
     * Provided $p_date parameter should be in DD.MM.YYYY or MM.DD.YYYY format depending the current
     * language. The result (raw date) will be formatted in YYYYMMDD. If the provided $p_date parameter
     * is invalid the result will be an empty string.
     *
     * @param string $date
     * @param bool   $reverse (optional)
     *
     * @return string
     */
    public function xtcDateRaw($date, $reverse = false)
    {
        $delimiter = preg_replace('/[0-9]/', '', (string)$date);
        
        if (strlen($delimiter) === 0) {
            return ''; // empty string stands for invalid provided date
        }
        
        $delimiter  = substr($delimiter, 0, 1);
        $dateFormat = preg_replace('/[\.\/\|-]/', $delimiter, DATE_FORMAT);
        
        // parse and recreate date string so that it can be manipulated properly
        $parsedDate = date_parse_from_format($dateFormat, $date);
        
        if (count($parsedDate['errors']) > 0 || count($parsedDate['warnings']) > 0) {
            return ''; // empty string stands for invalid provided date
        }
        
        $parsedDate = strtotime($parsedDate['day'] . '.' . $parsedDate['month'] . '.' . $parsedDate['year']);
        $format     = ($reverse) ? 'dmY' : 'Ymd';
        
        return date($format, $parsedDate);
    }
    
    
    public function xtcValidateEmail($email, $emailAddressCheck)
    {
        $valid_address = true;
        
        // sql injection fix 16.02.2011
        if (strpos($email, "\0") !== false) {
            return false;
        }
        if (strpos($email, "\x00") !== false) {
            return false;
        }
        if (strpos($email, "\u0000") !== false) {
            return false;
        }
        if (strpos($email, "\000") !== false) {
            return false;
        }
        
        $mail_pat      = '/^(.+)@(.+)$/i';
        $valid_chars   = "[^] \(\)<>@,;:\.\\\"\[]";
        $atom          = "$valid_chars+";
        $quoted_user   = '(\"[^\"]*\")';
        $word          = "($atom|$quoted_user)";
        $user_pat      = "/^$word(\.$word)*$/i";
        $ip_domain_pat = '/^\[([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\]$/i';
        $domain_pat    = "/^$atom(\.$atom)*$/i";
        
        if (preg_match($mail_pat, $email, $components)) {
            $user   = $components[1];
            $domain = $components[2];
            // validate user
            if (preg_match($user_pat, $user)) {
                // validate domain
                if (preg_match($ip_domain_pat, $domain, $ip_components)) {
                    // this is an IP address
                    for ($i = 1; $i <= 4; $i++) {
                        if ($ip_components[$i] > 255) {
                            $valid_address = false;
                            break;
                        }
                    }
                } else {
                    // Domain is a name, not an IP
                    if (preg_match($domain_pat, $domain)) {
                        /* domain name seems valid, but now make sure that it ends in a valid TLD or ccTLD
                           and that there's a hostname preceding the domain or country. */
                        $domain_components = explode(".", $domain);
                        // Make sure there's a host name preceding the domain.
                        if (sizeof($domain_components) < 2) {
                            $valid_address = false;
                        } else {
                            $top_level_domain = strtolower($domain_components[sizeof($domain_components) - 1]);
                            if ($this->strlenWrapper($top_level_domain) < 2) {
                                $valid_address = false;
                            }
                        }
                    } else {
                        $valid_address = false;
                    }
                }
            } else {
                $valid_address = false;
            }
        } else {
            $valid_address = false;
        }
        if ($valid_address && $emailAddressCheck == 'true') {
            if (!checkdnsrr($domain, "MX") && !checkdnsrr($domain, "A")) {
                $valid_address = false;
            }
        }
        
        return $valid_address;
    }
}