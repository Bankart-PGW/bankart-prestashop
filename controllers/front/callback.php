<?php
/**
 */

class BankartPaymentGatewayCallbackModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $cartId = Tools::getValue('id_cart');
        $prefix = strtoupper(Tools::getValue('type', ''));
        $notification = Tools::file_get_contents('php://input');

        \BankartPaymentGateway\Client\Client::setApiUrl(Configuration::get('BANKART_PAYMENT_GATEWAY_HOST', null));
        $client = new \BankartPaymentGateway\Client\Client(
            Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_ACCOUNT_USER', null),
            Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_ACCOUNT_PASSWORD', null),
            Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_API_KEY', null),
            Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_SHARED_SECRET', null)
        );

        $callbackValidationMode = Configuration::get('BANKART_PAYMENT_GATEWAY_CALLBACK_VALIDATION', null);
        
        if($callbackValidationMode === "ON") {
            if (!$client->validateCallbackWithGlobals()) {
                die("invalid callback");
            }
        }
        else if($callbackValidationMode === "DEBUG") {
            die(print_r(array_merge($_SERVER, ['Body' => $notification])));
        }
        
        $orderId = Tools::getValue('id_order');
        $order = new Order($orderId);

        $callback = $client->readCallback($notification);

        if ($callback->getResult() === 'OK') {
            $this->processSuccess($cartId, $order, $callback);
            die('OK');
        }

        $this->processFailure($cartId, $order, $callback);
        die('OK');
    }

    /**
     * @param string $cartId
     * @param Order $order
     * @param \BankartPaymentGateway\Client\Callback\Result $callback
     * @throws PrestaShopException
     */
    private function processSuccess($cartId, $order, $callback)
    {
        switch ($callback->getTransactionType()) {
            case 'DEBIT':
            case 'CAPTURE':
                $this->updateOrderPayments($order, $callback, _PS_OS_PAYMENT_);
                break;
            case 'PREAUTHORIZE':
                $this->updateOrderPayments($order, $callback, _PS_OS_PREPARATION_);
                break;
            case 'VOID':
                $order->setCurrentState(_PS_OS_CANCELED_);
                $orderPayments = OrderPayment::getByOrderReference($order->reference);
                $orderPayments[0]->amount = 0;
                break;
            case 'CREDIT':
                $order->setCurrentState(_PS_OS_REFUND_);
                $orderPayments = OrderPayment::getByOrderReference($order->reference);
                // manually triggering repeated callbacks would desync this...
                $orderPayments[0]->amount -= $callback->getAmount();
                $orderPayments[0]->save();
                break;
        }
    }

    /**
     * @param string $cartId
     * @param Order $order
     * @param \BankartPaymentGateway\Client\Callback\Result $callback
     */
    private function processFailure($cartId, $order, $callback)
    {
        $orderId = Order::getIdByCartId((int)($cartId));
        $order = new Order($orderId);

        $order->setCurrentState(_PS_OS_ERROR_);

    }

    /**
     * @param Order $order
     * @param \BankartPaymentGateway\Client\Callback\Result $callback
     * @param string $orderState
     * @throws PrestaShopException
     */
    private function updateOrderPayments($order, $callback, $orderState)
    {
        if($order->getCurrentState() != $orderState) 
        {
            $order->setCurrentState($orderState);
        }

        $orderPayments = OrderPayment::getByOrderReference($order->reference);

        if(empty($orderPayments)) 
        {
            $order->addOrderPayment($callback->getAmount(), 'Bankart Payment Gateway', $callback->getReferenceId());
            $orderPayments = OrderPayment::getByOrderReference($order->reference);
        }
        
        $orderPayment = $orderPayments[0];
        $orderPayment->transaction_id = $callback->getReferenceId();

        $returnData = $callback->getReturnData() ;
        if ($returnData instanceof \BankartPaymentGateway\Client\Data\Result\CreditcardData) {
            $orderPayment->payment_method = strtoupper($returnData->getType());
            $orderPayment->card_brand = $returnData->getBinBrand();
            $orderPayment->card_number = $returnData->getFirstSixDigits() . ' ... ' . $returnData->getLastFourDigits();
            $orderPayment->card_expiration = $returnData->getExpiryMonth() . '/' . $returnData->getExpiryYear();
            $orderPayment->card_holder = $returnData->getCardHolder();
        }

        $orderPayment->save();
    }
}
