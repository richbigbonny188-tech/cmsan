<?php
/*--------------------------------------------------------------
   AntiSpamUtilityTrait.php 2023-02-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace GXModules\Gambio\AntiSpam\Shop\classes;

/**
 * Trait AntiSpamUtilityTrait
 *
 * @description provides methods for generating anti spam tokens
 */
trait AntiSpamUtilityTrait
{
    /**
     * Validates if transmitted Secret Anti Spam Token is the stored one
     * and if enough time has passed since originally generating it
     *
     * @param string $value transmitted value of input named "ae6b85682663ab4570bd10c67b83d21fe77cdf97"
     *
     * @return bool
     */
    private function sendAntiSpamInputIsValid(string $value): bool
    {
        [$storedId, $storedTime] = explode('@', base64_decode($this->storedAntiSpamToken));
        [$sendTime, $sendId] = explode('@', base64_decode(str_rot13($value)));
        
        $waitTimeValid          = time() >= $sendTime;
        $sendValueIsStoredValue = $storedId === $sendId && $storedTime === $sendTime;
        
        return $sendValueIsStoredValue && $waitTimeValid;
    }
    
    
    /**
     * will generate random id and timestamp when the form is allowed to be processed
     * value will be stored in session, under key "sast" (Secret Anti Spam Token),
     * so it can be validated by "sendAntiSpamInputIsValid" upon sending
     *
     * @return string
     * @throws Exception
     */
    private function generateSecretAntiSpamToken(): string
    {
        $minWaitTimeInSeconds = 4;
        $time                 = time() + $minWaitTimeInSeconds;
        $id                   = sha1(random_bytes(256));
        $separator            = '@';
        
        return $_SESSION['sast'] = base64_encode($id . $separator . $time);
    }
}