<?php
/* --------------------------------------------------------------
   EntropyProvider.inc.php 2019-03-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class EntropyProvider
 *
 * This class provides a general source for cryptography-safe random data.
 *
 */
class EntropyProvider
{
    /**
     * Provides $numBytes of random bytes, optionally limited to strong entropy sources
     *
     * @param \IntType  $numBytes
     * @param \BoolType $strongCryptoRequired
     *
     * @return bool|string
     */
    public function randomBytes(IntType $numBytes, BoolType $strongCryptoRequired)
    {
        $bytes         = false;
        $numberOfBytes = $numBytes->asInt();
        
        if (function_exists('random_bytes')) {
            try {
                $bytes = random_bytes($numberOfBytes);
            } catch (Exception $e) {
                // pass, handled below
            }
        }
        if ($bytes === false && function_exists('openssl_random_pseudo_bytes')) {
            $strongCrypto = false;
            $bytes        = openssl_random_pseudo_bytes($numberOfBytes, $strongCrypto);
            if ($strongCrypto === false && $strongCryptoRequired->asBool() === true) {
                $bytes = false;
            }
        }
        if ($bytes === false && function_exists('mt_rand') && $strongCryptoRequired->asBool() !== true) {
            $bytes = str_repeat("\0", $numberOfBytes);
            for ($i = 0; $i < $numberOfBytes; $i++) {
                $bytes[$i] = chr(mt_rand(0, 254));
            }
        }
        
        if ($bytes === false) {
            throw new NoSuitableEntropySourceAvailableException('no entropy source found');
        }
        
        return $bytes;
    }
}
