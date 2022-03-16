/**
 * seamless form
 */
$('.payment-options').on('change', '.payment-option', function () {
  setTimeout(function () {
    var $seamlessForm = $('.js-payment-option-form:visible .payment-form-seamless');
    if ($seamlessForm.length) {
      initBankartPaymentGatewaySeamless($seamlessForm[0]);
    }
  }, 10);
});

// One Page Checkout PS module compatibility

$('#onepagecheckoutps_step_three').on('change', '.payment-option', function () {
  setTimeout(function () {
    var $seamlessForm = $('.js-payment-option-form:visible .payment-form-seamless');
    if ($seamlessForm.length) {
      //prevents re-init, if the iframe is already loaded
      var formId = $($seamlessForm[0]).data('id');
      var $seamlessCardNumberInput = $('#bankart-payment-gateway-ccCardNumber-' + formId, $seamlessForm);
      if (!$seamlessCardNumberInput.children('iframe').length) {
        initBankartPaymentGatewaySeamless($seamlessForm[0]);
      }
    }
  }, 10);
});

var initBankartPaymentGatewaySeamless = function (seamlessForm) {
  var validNumber;
  var validCvv;

  var $seamlessForm = $(seamlessForm);
  var integrationKey = $seamlessForm.data('integrationKey');
  var formId = $seamlessForm.data('id');

  var $seamlessCardHolderInput = $('#bankart-payment-gateway-ccCardHolder-' + formId, $seamlessForm);
  var $seamlessCardNumberInput = $('#bankart-payment-gateway-ccCardNumber-' + formId, $seamlessForm);
  var $seamlessCvvInput = $('#bankart-payment-gateway-ccCvv-' + formId, $seamlessForm);
  var $seamlessExpiryMonthInput = $('#bankart-payment-gateway-ccExpiryMonth-' + formId, $seamlessForm);
  var $seamlessExpiryYearInput = $('#bankart-payment-gateway-ccExpiryYear-' + formId, $seamlessForm);

  var $paymentButton = $('#payment-confirmation button');

  /**
   * fixed seamless input heights
   */
  $seamlessCardNumberInput.css('height', $seamlessCardHolderInput.css('height'));
  $seamlessCvvInput.css('height', $seamlessCardHolderInput.css('height'));

  /**
   * copy styles
   */
  var style = {
    'background': $seamlessCardHolderInput.css('background'),
    'border': 'none',
    'height': '100%',
    'padding': $seamlessCardHolderInput.css('padding'),
    'font-size': $seamlessCardHolderInput.css('font-size'),
    'color': $seamlessCardHolderInput.css('color'),
  };

  /**
   * initialize
   */
  var payment = new PaymentJs('1.2');
  payment.init(integrationKey, $seamlessCardNumberInput.prop('id'), $seamlessCvvInput.prop('id'),
    function (payment) {
      payment.setNumberStyle(style);
      payment.setCvvStyle(style);
      payment.numberOn('input', function (data) {
        validNumber = data.validNumber;
      });
      payment.cvvOn('input', function (data) {
        validCvv = data.validCvv;
      });
    });

  /**
   * handler
   */
  $seamlessForm.submit(function (e) {
    e.preventDefault();
    $('.alert-bankart').hide();
    
    payment.tokenize(
      {
        card_holder: $seamlessCardHolderInput.val(),
        month: $seamlessExpiryMonthInput.val(),
        year: $seamlessExpiryYearInput.val(),
      },
      function (token, cardData) {
        $seamlessForm.off('submit');
        $seamlessForm.append('<input type="hidden" name="token" value="' + token + '"/>');
        $seamlessForm.submit();
      },
      function (errors) {
        for (let i = 0; i < errors.length; i++) {
          $('.alert-' + errors[i].attribute + '-' + formId, $seamlessForm).show();
          $('#error_' + errors[i].key.substring(7) + '-' + errors[i].attribute + '-' + formId, $seamlessForm).show();
        }
        $paymentButton.prop('disabled', false);
      }
    );
  });
};
