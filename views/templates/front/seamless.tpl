<form class="payment-form-seamless" data-id="{$id}" data-integration-key="{$integrationKey}" method="POST" action="{$action}">

    <div class="row">
        <div class="form-group col-md-6">
            <label class="form-control-label">{l s='Cardholder' mod='bankartpaymentgateway'}</label>
            <input type="text" class="form-control" name="ccCardHolder" id="bankart-payment-gateway-ccCardHolder-{$id}"/>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 alert alert-warning alert-bankart alert-card_holder-{$id}" style="display: none;">
            <div id="error_blank-card_holder-{$id}" class="bankart-alert" style="display: none;">{l s='Please enter the cardholder name' mod='bankartpaymentgateway'}</div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-6">
            <label class="form-control-label">{l s='Card Number' mod='bankartpaymentgateway'}</label>
            <div class="form-control" id="bankart-payment-gateway-ccCardNumber-{$id}" style="padding: 0; overflow: hidden"></div>
        </div>
        <div class="form-group col-md-3">
            <label class="form-control-label">{l s='CVV2/CVC2' mod='bankartpaymentgateway'}</label>
            <div class="form-control" id="bankart-payment-gateway-ccCvv-{$id}" style="padding: 0; overflow: hidden"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 alert alert-warning alert-bankart alert-number-{$id} alert-cvv-{$id}" style="display: none;">
            <div id="error_blank-number-{$id}" class="alert-bankart" style="display: none;">{l s='Please enter the card number' mod='bankartpaymentgateway'}</div>
            <div id="error_invalid-number-{$id}" class="alert-bankart" style="display: none;">{l s='Invalid card number' mod='bankartpaymentgateway'}</div>
            <div id="error_blank-cvv-{$id}" class="alert-bankart" style="display: none;">{l s='CVV code must not be empty' mod='bankartpaymentgateway'}</div>
            <div id="error_invalid-cvv-{$id}" class="alert-bankart" style="display: none;">{l s='Invalid CVV code' mod='bankartpaymentgateway'}</div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-3">
            <label class="form-control-label">{l s='Month' mod='bankartpaymentgateway'}</label>
            <select class="form-control" name="ccExpiryMonth" id="bankart-payment-gateway-ccExpiryMonth-{$id}">
                <option value="" selected>-</option>
                {foreach from=$months item=month}
                    <option value="{$month}">{$month}</option>
                {/foreach}
            </select>
        </div>
        <div class="form-group col-md-4">
            <label class="form-control-label">{l s='Year' mod='bankartpaymentgateway'}</label>
            <select class="form-control" name="ccExpiryYear" id="bankart-payment-gateway-ccExpiryYear-{$id}">
                <option value="" selected>----</option>
                {foreach from=$years item=year}
                    <option value="{$year}">{$year}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 alert alert-warning alert-bankart alert-month-{$id} alert-year-{$id}" style="display: none;">
            <div id="error_blank-month-{$id}" class="alert-bankart" style="display: none;">{l s='Please set the expiration date' mod='bankartpaymentgateway'}</div>
            <div id="error_blank-year-{$id}" class="alert-bankart" style="display: none;">{l s='Please set the expiration date' mod='bankartpaymentgateway'}</div>
            <div id="error_expired-year-{$id}" class="alert-bankart" style="display: none;">{l s='Card has expired' mod='bankartpaymentgateway'}</div>
        </div>
    </div>

</form>
