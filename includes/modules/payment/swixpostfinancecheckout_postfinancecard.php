<?php

$languageTextManager = MainFactory::create_object('LanguageTextManager', [], true);
$languageTextManager->init_from_lang_file('swixpostfinancecheckout_postfinancecard', $_SESSION['languages_id']);

class swixpostfinancecheckout_postfinancecard extends SwixPostfinanceCheckoutBase
{
    public $code = 'swixpostfinancecheckout_postfinancecard';
    protected $alias = 'PF';
    protected $allowedCountries = 'CH,LI';
    protected $paymentMethodId = 1457546097597;
    protected $paymentMethodBrandIds = [1461144402291];
    public $sort_order = 30;
}