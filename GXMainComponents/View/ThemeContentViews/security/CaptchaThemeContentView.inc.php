<?php
/* --------------------------------------------------------------
   CaptchaThemeContentView.inc.php 2019-06-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CaptchaThemeContentView
 */
class CaptchaThemeContentView extends ThemeContentView
{
    /**
     * @var string $captchaName
     */
    protected $captchaName = '';
    
    /**
     * @var string $captchaTheme
     */
    protected $captchaTheme = '';
    
    /**
     * @var string $captchaUrl
     */
    protected $captchaUrl = '';
    
    /**
     * @var bool $isRecaptcha
     */
    protected $isRecaptcha = false;
    
    /**
     * @var bool $isRecaptchaV2
     */
    protected $isRecaptchaV2 = false;
    
    /**
     * @var string $publicKey
     */
    protected $publicKey = '';
    
    /**
     * @var string $recaptchaHtml
     */
    protected $recaptchaHtml = '';
    
    
    public function prepare_data()
    {
        if ($this->isRecaptcha) {
            $this->set_content_template('captcha_recaptcha.html');
            
            $this->set_content_data('SCRIPT', $this->recaptchaHtml);
            $this->set_content_data('PUBLIC_KEY', $this->publicKey);
            $this->set_content_data('THEME', $this->captchaTheme);
        } elseif ($this->isRecaptchaV2) {
            $this->set_content_template('captcha_recaptcha_v2.html');
            $this->set_content_data('PUBLIC_KEY', $this->publicKey);
        } else {
            $this->set_content_template('captcha_captcha.html');
            $this->set_content_data('NAME', $this->captchaName);
            $this->set_content_data('URL', $this->captchaUrl);
        }
    }
    
    
    /**
     * @param string $captchaName
     */
    public function setCaptchaName($captchaName)
    {
        $this->captchaName = (string)$captchaName;
    }
    
    
    /**
     * @param string $captchaTheme
     */
    public function setCaptchaTheme($captchaTheme)
    {
        $this->captchaTheme = (string)$captchaTheme;
    }
    
    
    /**
     * @param string $captchaUrl
     */
    public function setCaptchaUrl($captchaUrl)
    {
        $this->captchaUrl = (string)$captchaUrl;
    }
    
    
    /**
     * @param string $isRecaptcha
     */
    public function setIsRecaptcha($isRecaptcha)
    {
        $this->isRecaptcha = (bool)$isRecaptcha;
    }
    
    
    /**
     * @param string $isRecaptchaV2
     */
    public function setIsRecaptchaV2($isRecaptchaV2)
    {
        $this->isRecaptchaV2 = (bool)$isRecaptchaV2;
    }
    
    
    /**
     * @param string $publicKey
     */
    public function setPublicKey($publicKey)
    {
        $this->publicKey = (string)$publicKey;
    }
    
    
    /**
     * @param string $recaptchaHtml
     */
    public function setRecaptchaHtml($recaptchaHtml)
    {
        $this->recaptchaHtml = (string)$recaptchaHtml;
    }
}
