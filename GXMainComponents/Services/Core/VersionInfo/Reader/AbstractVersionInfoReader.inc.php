<?php
/* --------------------------------------------------------------
  AbstractVersionInfoReader.inc.php 2022-08-10
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class AbstractVersionInfoReader
 *
 * @category    System
 * @package     VersionInfo
 * @subpackage  Reader
 */
abstract class AbstractVersionInfoReader
{
    /**
     * Determines the name of the version info items while considering known edge cases.
     *
     * @param NonEmptyStringType $versionInfoItemName
     *
     * @return string $versionInfoItemName
     */
    protected function _determineName(NonEmptyStringType $versionInfoItemName)
    {
        $versionInfoItemName = $versionInfoItemName->asString();
        
        $versionInfoItemName = str_replace('_', '.', $versionInfoItemName);
        $versionInfoItemName = strtolower($versionInfoItemName);
        
        switch ($versionInfoItemName) {
            case strpos($versionInfoItemName, 'paypal') !== false:
                break;
            case strpos($versionInfoItemName, 'update-') !== false:
                $versionInfoItemName = 'GX ' . ucwords(substr($versionInfoItemName,
                                                              0,
                                                              strpos($versionInfoItemName,
                                                                     '-',
                                                                     strpos($versionInfoItemName,
                                                                            '-') + 1)));
                break;
            case strpos($versionInfoItemName, 'sp-') !== false:
                $versionInfoItemName = 'GX Service Pack';
                break;
            case strpos($versionInfoItemName, 'mu-') !== false:
                $versionInfoItemName = 'GX Master Update';
                break;
            case strpos($versionInfoItemName, 'ku-') !== false:
                $versionInfoItemName = 'GX Kumulatives Update';
                break;
            case strpos($versionInfoItemName, 'mobile.template') !== false:
                $versionInfoItemName = 'Mobile Template';
                break;
            case strpos($versionInfoItemName, 'gambio.hub') !== false:
                $versionInfoItemName = 'Gambio HUB';
                break;
            case strpos($versionInfoItemName, 'beta') !== false:
                $versionInfoItemName = 'GX Beta';
                break;
            case is_numeric(str_replace('.', '', $versionInfoItemName)) !== false:
                $versionInfoItemName = 'GX ' . $versionInfoItemName;
                break;
            case strpos($versionInfoItemName, 'mobilecandy') !== false:
                $versionInfoItemName = 'MobileCandy';
                break;
            case strpos($versionInfoItemName, 'sec.update') !== false:
                $versionInfoItemName = 'Security Update';
                break;
        }
        
        return $versionInfoItemName;
    }
    
    
    /**
     * Determines the changelog url of the version info items while considering known edge cases.
     *
     * @param NonEmptyStringType $versionInfoItemVersion
     * @param NonEmptyStringType $versionInfoItemVendor
     * @param NonEmptyStringType $versionInfoItemName
     *
     * @return string $changelogVersionURL
     */
    protected function _determineChangelogURL(
        NonEmptyStringType $versionInfoItemVersion,
        NonEmptyStringType $versionInfoItemVendor,
        NonEmptyStringType $versionInfoItemName
    ) {
        $changelogBaseURL = 'https://developers.gambio.de/changelog/?show=';
        
        $versionInfoItemVersionString = strtolower($versionInfoItemVersion->asString());
        $versionInfoItemVendorString  = $versionInfoItemVendor->asString();
        $versionInfoItemNameString    = $versionInfoItemName->asString();
        
        $changelogVersionURL = $versionInfoItemVersionString;
        
        $changelogVersionURL = $this->_determineChangelogVersionURLSpecialCases($changelogVersionURL,
                                                                                $versionInfoItemVendorString,
                                                                                $versionInfoItemNameString);
        
        $changelogBaseURL = $this->_determineChangelogBaseURLSpecialCase($changelogVersionURL,
                                                                         $versionInfoItemVendorString,
                                                                         $versionInfoItemNameString,
                                                                         $changelogBaseURL);
        
        if ($changelogVersionURL !== '') {
            $changelogBaseURL = $this->_determineChangelogSpecialVersionsBaseURL($changelogVersionURL,
                                                                                 $changelogBaseURL);
            
            $changelogVersionURL = $this->_determineChangelogSpecialVersionsURL($changelogVersionURL);
        }
        
        return $changelogBaseURL . $changelogVersionURL;
    }
    
    
    /**
     * Determine the changelog version URL with special cases.
     *
     * @param string $changelogVersionURL
     * @param string $versionInfoItemVendorString
     * @param string $versionInfoItemNameString
     *
     * @return string $changelogVersionURL
     */
    protected function _determineChangelogVersionURLSpecialCases(
        $changelogVersionURL,
        $versionInfoItemVendorString,
        $versionInfoItemNameString
    ) {
        if ($this->_isSpecialCase($versionInfoItemVendorString, $versionInfoItemNameString, $changelogVersionURL)) {
            $changelogVersionURL = '';
        } elseif (strpos($changelogVersionURL, '(') !== false
                  || strpos($changelogVersionURL, 'master update') !== false) {
            $changelogVersionURL = str_replace(['(', ')', 'master update'], '', $changelogVersionURL);
            
            if (strpos($changelogVersionURL, ' r') !== false) {
                $changelogVersionURL = substr($changelogVersionURL, 0, strpos($changelogVersionURL, 'r'));
            }
        } elseif (strpos($changelogVersionURL, ' r') !== false) {
            $changelogVersionURL = substr($changelogVersionURL, 0, strpos($changelogVersionURL, 'r'));
        }
        
        return (string)$changelogVersionURL;
    }
    
    
    /**
     * Determine the changelog base URl with special case to '/'.
     *
     * @param string $changelogVersionURL
     * @param string $versionInfoItemVendorString
     * @param string $versionInfoItemNameString
     * @param string $changelogBaseURL
     *
     * @return string $changelogBaseURL
     */
    protected function _determineChangelogBaseURLSpecialCase(
        $changelogVersionURL,
        $versionInfoItemVendorString,
        $versionInfoItemNameString,
        $changelogBaseURL
    ) {
        if ($this->_isSpecialCase($versionInfoItemVendorString, $versionInfoItemNameString, $changelogVersionURL)) {
            return '/';
        }
        
        return (string)$changelogBaseURL;
    }
    
    
    /**
     * Determine the changelog version URL with special cases.
     *
     * @param string $changelogVersionURL
     *
     * @return string $changelogVersionURL
     */
    protected function _determineChangelogSpecialVersionsURL($changelogVersionURL)
    {
        $versionCompare = explode('.', $changelogVersionURL);
        
        // if the version is under 2.0.11.0
        if ($this->_isVersionUnderMinVersion($versionCompare)) {
            $changelogVersionURL = '';
        } // if the Version ends with an 0
        elseif (isset($versionCompare[3]) && $versionCompare[3] < 1) {
            $changelogStartByVersion = $this->_checkForBetaVersions($changelogVersionURL);
            
            $changelogVersionURL = '?start=' . $changelogStartByVersion . 'beta1' . '&stop=' . $changelogVersionURL;
        }
        
        $changelogVersionURL = str_replace(' ', '', $changelogVersionURL);
        
        return (string)$changelogVersionURL;
    }
    
    
    /**
     * Determine the changelog base URL with special cases.
     *
     * @param string $changelogVersionURL
     * @param string $changelogBaseURL
     *
     * @return string $changelogBaseURL
     */
    protected function _determineChangelogSpecialVersionsBaseURL($changelogVersionURL, $changelogBaseURL)
    {
        $versionCompare = explode('.', $changelogVersionURL);
        
        // if the version is under 2.0.11.0
        if ($this->_isVersionUnderMinVersion($versionCompare)) {
            $changelogBaseURL = '/';
        } // if the Version ends with an 0
        elseif (isset($versionCompare[3]) && $versionCompare[3] < 1) {
            $changelogBaseURL = 'https://developers.gambio.de/changelog/';
        }
        
        return (string)$changelogBaseURL;
    }
    
    
    /**
     * Returns true if the Version is under 2.0.11.0 .
     *
     * @param array $versionCompare
     *
     * @return bool
     */
    protected function _isVersionUnderMinVersion(array $versionCompare)
    {
        if (!is_numeric($versionCompare[0])
            || ($versionCompare[0] < 2
                || ($versionCompare[0] == 2 && $versionCompare[1] == 0
                    && $versionCompare[2] < 11))) {
            return true;
        }
        
        return false;
    }
    
    
    /**
     * Checks for an beta version to set the changelog start version.
     *
     * @param string $changelogVersionURL
     *
     * @return string $changelogStartByVersion
     */
    protected function _checkForBetaVersions($changelogVersionURL)
    {
        if (strpos($changelogVersionURL, 'beta') !== false) {
            return (string)$changelogStartByVersion = substr($changelogVersionURL,
                                                             0,
                                                             strpos($changelogVersionURL, 'b'));
        } else {
            return (string)$changelogStartByVersion = $changelogVersionURL;
        }
    }
    
    
    /**
     * Returns true if on special case true is.
     *
     * @param string $versionInfoItemVendorString
     * @param string $versionInfoItemNameString
     * @param string $changelogVersionURL
     *
     * @return bool
     */
    protected function _isSpecialCase(
        $versionInfoItemVendorString,
        $versionInfoItemNameString,
        $changelogVersionURL
    ) {
        if ($versionInfoItemVendorString !== 'Gambio' || strpos($versionInfoItemNameString, 'Mobile') !== false
            || strpos($versionInfoItemNameString, 'Security Update') !== false
            || strpos($changelogVersionURL, 'unknown_version') !== false) {
            return true;
        } else {
            return false;
        }
    }
}