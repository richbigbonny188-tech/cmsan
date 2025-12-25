<?php

$languageTextManager = MainFactory::create_object('LanguageTextManager', [], true);
$languageTextManager->init_from_lang_file('swixpostfinancecheckout_twint', $_SESSION['languages_id']);

class swixpostfinancecheckout_twint extends SwixPostfinanceCheckoutBase
{
    public $code = 'swixpostfinancecheckout_twint';
    protected $alias = 'TWINT';
    protected $allowedCountries = 'CH,LI';
    protected $paymentMethodId = 1457546097639;
    public $sort_order = 20;
}