<?php
/* --------------------------------------------------------------
   JsonWebTokenRepository.php 2019-12-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Auth\Repositories;

use Gambio\Core\Auth\Model\JsonWebToken;

class JsonWebTokenRepository
{
    /**
     * @var JsonWebTokenSecretProvider
     */
    private $secretProvider;
    
    
    /**
     * JsonWebTokenRepository constructor.
     *
     * @param JsonWebTokenSecretProvider $secretProvider
     */
    public function __construct(JsonWebTokenSecretProvider $secretProvider)
    {
        $this->secretProvider = $secretProvider;
    }
    
    
    /**
     * @param string $token
     *
     * @return JsonWebToken
     *
     * @deprecated Should only be used for compatibility use cases. Primary use `getJsonWebToken` method.
     */
    public function getCompatibilityWebToken(string $token): JsonWebToken
    {
        $parts   = explode('.', $token);
        $secret  = $this->secretProvider->getSecret();
        $header  = json_decode($this->base64UrlDecode($parts[0]), true);
        $payload = json_decode($this->base64UrlDecode($parts[1]), true);
        
        return JsonWebToken::createCompatibilityToken($secret, $header, $payload);
    }
    
    
    /**
     * @param string $token
     *
     * @return JsonWebToken
     */
    public function getJsonWebToken(string $token): JsonWebToken
    {
        $parts   = explode('.', $token);
        $secret  = $this->secretProvider->getSecret();
        $header  = json_decode($this->base64UrlDecode($parts[0]), true);
        $payload = json_decode($this->base64UrlDecode($parts[1]), true);
        
        return JsonWebToken::createToken($secret, $header, $payload);
    }
    
    
    /**
     * @param string $input
     *
     * @return string
     */
    private function base64UrlDecode(string $input): string
    {
        return base64_decode(strtr($input, ['-' => '+', '_' => '/']));
    }
}