<?php

$languageTextManager = MainFactory::create_object('LanguageTextManager', [], true);
$languageTextManager->init_from_lang_file('swixpostfinancecheckout_creditdebitcard', $_SESSION['languages_id']);

class swixpostfinancecheckout_creditdebitcard extends SwixPostfinanceCheckoutBase
{
    public $code = 'swixpostfinancecheckout_creditdebitcard';
    protected $alias = 'CC';
    protected $paymentMethodId = 1457546097597;
    protected $paymentMethodBrandIds = [
        1461143265574, // Mastercard Maestro
        1461144365052, // Visa
        1461144371207, // Mastercard
        1461144377346, // American Express
        1461144382208, // Diners Club
        1461144387911, // Discover
        1461144392674, // JCB
        1461144397166, // China Union Pay
        1461144406894, // Mastercard Bancontact
        1474550875665, // Dankort
        1474607954004, // Visa Electron
        1523538488600, // Lunch-Check
        1532520658422, // Elo (Visa)
        1532531230948, // Elo (Mastercard)
        1552559294409, // Boncard
        1580295169595, // Visa V PAY
    ];
    public $sort_order = 10;
}