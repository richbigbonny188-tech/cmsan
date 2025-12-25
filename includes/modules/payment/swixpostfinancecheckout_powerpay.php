<?php

$languageTextManager = MainFactory::create_object('LanguageTextManager', [], true);
$languageTextManager->init_from_lang_file('swixpostfinancecheckout_powerpay', $_SESSION['languages_id']);

class swixpostfinancecheckout_powerpay extends SwixPostfinanceCheckoutBase
{
    public $code = 'swixpostfinancecheckout_powerpay';
    protected $alias = 'PWR';
    protected $allowedCountries = 'CH,LI';
    protected $paymentMethodId = 1457546097598;
    public $sort_order = 0;
}