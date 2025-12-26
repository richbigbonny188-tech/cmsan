$(document).ready(function(){
    $('.page-checkout-confirmation form').submit(function() {
        $('.checkout-confirmation-submit').prop('disabled', true);
    });
});