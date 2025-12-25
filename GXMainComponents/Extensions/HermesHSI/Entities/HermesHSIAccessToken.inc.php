<?php
/* --------------------------------------------------------------
   HermesHSIAccessToken.inc.php 2022-08-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class HermesHSIAccessToken implements Serializable
{
    /**
     * @var array
     */
    protected $accessTokenResponse;
    
    /**
     * @var int
     */
    protected $validUntil;
    
    
    public function __construct(array $accessTokenResponse, ?int $timeOfRetrieval = null)
    {
        $timeOfRetrieval           = $timeOfRetrieval ?? time();
        $this->accessTokenResponse = $accessTokenResponse;
        $this->validUntil          = $timeOfRetrieval + (int)$accessTokenResponse['expires_in'];
    }
    
    
    public function getAccessToken(): string
    {
        return $this->accessTokenResponse['access_token'];
    }
    
    
    public function getRefreshToken(): string
    {
        return $this->accessTokenResponse['refresh_token'];
    }
    
    public function isValid(): bool
    {
        return $this->validUntil > time();
    }
    
    public function getValidUntil(): \DateTime
    {
        return new DateTime('@' . (string)$this->validUntil);
    }
    
    /**
     * String representation of object
     * @link  https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize(): string
    {
        return serialize([
                             'valid_until'           => $this->validUntil,
                             'access_token_response' => $this->accessTokenResponse,
                         ]);
    }
    
    
    /**
     * Constructs the object
     * @link  https://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     *
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized): void
    {
        $dataArray                 = unserialize($serialized, ['allowed_classes' => false]);
        $this->accessTokenResponse = $dataArray['access_token_response'];
        $this->validUntil          = $dataArray['valid_until'];
    }
    
    
    /**
     * @return array
     */
    public function __serialize(): array
    {
        return [
            'valid_until'           => $this->validUntil,
            'access_token_response' => $this->accessTokenResponse,
        ];
    }
    
    
    /**
     * @param $serialized
     *
     * @return void
     */
    public function __unserialize(array $data): void
    {
        $this->accessTokenResponse = $data['access_token_response'];
        $this->validUntil          = $data['valid_until'];
    }
}
