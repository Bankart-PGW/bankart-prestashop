<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;



/**
 * Class BankartPaymentGateway
 *
 * @extends PaymentModule
 */
class BankartPaymentGateway extends PaymentModule
{
    const BANKART_PAYMENT_GATEWAY_OS_STARTING = 'BANKART_PAYMENT_GATEWAY_OS_STARTING';
    const BANKART_PAYMENT_GATEWAY_OS_AWAITING = 'BANKART_PAYMENT_GATEWAY_OS_AWAITING';

    protected $config_form = false;
    //for instalments
    /**
     * @return array
     */
    public function toOptionArray()
    {
    return [
        ['value' => '1', 'label' => ('No instalments')],
        ['value' => '6', 'label' => ('6 instalments')],
        ['value' => '12', 'label' => ('12 instalments')],
        ['value' => '24', 'label' => ('24 instalments')],
        ['value' => '36', 'label' => ('36 instalments')],
        ['value' => '60', 'label' => ('60 instalments')],
        ];
    }
    
    
    //end new

    public function __construct()
    {
        require_once(_PS_MODULE_DIR_ . 'bankartpaymentgateway' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

        $this->name = 'bankartpaymentgateway';
        $this->tab = 'payments_gateways';
        $this->version = '3.1.1.1';
        $this->author = 'Bankart Payment Gateway';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->bootstrap = true;
        $this->controllers = [
            'payment',
            'callback',
            'return',
        ];

        parent::__construct();

        $this->displayName = $this->l('Bankart Payment Gateway');
        $this->description = $this->l('Bankart Payment Gateway Payment');
        $this->confirmUninstall = $this->l('confirm_uninstall');
    }

    public function install()
    {
        if (extension_loaded('curl') == false) {
            $this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');
            return false;
        }

        if (!parent::install()
            || !$this->registerHook('paymentOptions')
            || !$this->registerHook('displayPaymentReturn')

            || !$this->registerHook('payment')
            || !$this->registerHook('displayAfterBodyOpeningTag')
            || !$this->registerHook('displayHeader') // 'header' obsolete. 
        ) {
            return false;
        }

        $this->createOrderState(static::BANKART_PAYMENT_GATEWAY_OS_STARTING);
        $this->createOrderState(static::BANKART_PAYMENT_GATEWAY_OS_AWAITING);

        // set default configuration
        Configuration::updateValue('BANKART_PAYMENT_GATEWAY_HOST', 'https://gateway.bankart.si/');

        return true;
    }

    public function uninstall()
    {
        Configuration::deleteByName('BANKART_PAYMENT_GATEWAY_ENABLED');
        Configuration::deleteByName('BANKART_PAYMENT_GATEWAY_HOST');
        Configuration::deleteByName('BANKART_PAYMENT_GATEWAY_CALLBACK_VALIDATION');

        foreach ($this->getCreditCards() as $creditCard) {
            $prefix = strtoupper($creditCard);
            Configuration::deleteByName('BANKART_PAYMENT_GATEWAY_' . $prefix . '_TITLE');
            Configuration::deleteByName('BANKART_PAYMENT_GATEWAY_' . $prefix . '_TRANSACTION_TYPE');
            Configuration::deleteByName('BANKART_PAYMENT_GATEWAY_' . $prefix . '_ACCOUNT_USER');
            Configuration::deleteByName('BANKART_PAYMENT_GATEWAY_' . $prefix . '_ACCOUNT_PASSWORD');
            Configuration::deleteByName('BANKART_PAYMENT_GATEWAY_' . $prefix . '_API_KEY');
            Configuration::deleteByName('BANKART_PAYMENT_GATEWAY_' . $prefix . '_SHARED_SECRET');
            Configuration::deleteByName('BANKART_PAYMENT_GATEWAY_' . $prefix . '_INTEGRATION_KEY');
            Configuration::deleteByName('BANKART_PAYMENT_GATEWAY_' . $prefix . '_INSTALLMENTS');
            Configuration::deleteByName('BANKART_PAYMENT_GATEWAY_' . $prefix . '_INSTALMENT_AMOUNT');
            Configuration::deleteByName('BANKART_PAYMENT_GATEWAY_' . $prefix . '_SEAMLESS');
        }

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        if (((bool)Tools::isSubmit('submitBankartPaymentGatewayModule')) == true) {
            foreach ($this->getCreditCards() as $creditCard => $cardTitle) {
                $prefix = strtoupper($creditCard);
                
                $installmentAmount = Tools::getValue('BANKART_PAYMENT_GATEWAY_' . $prefix . '_INSTALMENT_AMOUNT');
                error_log(json_encode('Method: '. $prefix . ',    InstallmentAmount: ' . $installmentAmount));
                if ($prefix == 'FLIK') {
                    $installmentAmount = 1;
                }

                if ((!is_numeric((int)$installmentAmount) && ($installmentAmount != '')) || ((int)$installmentAmount <= 0) && ($installmentAmount != '')) {

                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name);         
                    return;
                    
                }  

            }

            $form_values = $this->getConfigFormValues();

            foreach (array_keys($form_values) as $key) {
                
                $key = str_replace(['[', ']'], '', $key);
                $val = Tools::getValue($key);
                if (is_array($val)) {
                    $val = \json_encode($val);
                }
                if ($key == 'BANKART_PAYMENT_GATEWAY_HOST') {
                    $val = trim($val);
                    $val = rtrim($val, '/') . '/';
                }
                if ($this->is_credential($key)) {
                    $val = trim($val);
                }

                Configuration::updateValue($key, $val);
            }
        }
        return $this->renderForm();
    }

    private function is_credential($key) 
    {
        if (substr($key, -strlen('_ACCOUNT_USER')) === '_ACCOUNT_USER') return true;
        if (substr($key, -strlen('_ACCOUNT_PASSWORD')) === '_ACCOUNT_PASSWORD') return true;
        if (substr($key, -strlen('_API_KEY')) === '_API_KEY') return true;
        if (substr($key, -strlen('_SHARED_SECRET')) === '_SHARED_SECRET') return true;
        if (substr($key, -strlen('_SEAMLESS')) === '_SEAMLESS') return true;
        return false;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitBankartPaymentGatewayModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    private function getCreditCards()
    {
        /**
         * Comment/disable adapters that are not applicable
         */
        return [
            'paymentcard' => 'Payment Card',
            'mvcisa' => 'Mastercard / VISA',
            'diners' => 'Diners',
            'flik' => 'Flik', // New
        ];
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        $form = [
            'form' => [
                'tabs' => array_merge(['General' => 'General'], $this->getCreditCards()),
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'name' => 'BANKART_PAYMENT_GATEWAY_ENABLED',
                        'label' => $this->l('Enable'),
                        'tab' => 'General',
                        'type' => 'switch',
                        'is_bool' => 1,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => 'Enabled',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => 'Disabled',
                            ],
                        ],
                    ],
                    [
                        'name' => 'BANKART_PAYMENT_GATEWAY_HOST',
                        'label' => $this->l('Host'),
                        'tab' => 'General',
                        'class' => 'fixed-width-xxl',
                        'type' => 'text',
                    ],
                    [
                        'name' => 'BANKART_PAYMENT_GATEWAY_CALLBACK_VALIDATION',
                        'label' => $this->l('Callback validation'),
                        'tab' => 'General',
                        'type' => 'select',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'ON',
                                    'name' => $this->l('On'),
                                ],
                                [
                                    'id' => 'OFF',
                                    'name' => $this->l('Off'),
                                ],
                                [
                                    'id' => 'DEBUG',
                                    'name' => $this->l('Debug'),
                                ],
                            ],
                            'id' => 'id',
                            'name'=> 'name',
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];

        foreach ($this->getCreditCards() as $creditCard => $cardTitle) {

            $prefix = strtoupper($creditCard);

            $form['form']['input'][] = [
                'name' => 'line',
                'type' => 'html',
                'tab' => $creditCard,
                'html_content' => '<h3 style="margin-top: 10px;">' . $cardTitle . '</h3>',
            ];

            $form['form']['input'][] = [
                'name' => 'BANKART_PAYMENT_GATEWAY_' . $prefix . '_ENABLED',
                'label' => $this->l('Enable'),
                'tab' => $creditCard,
                'type' => 'switch',
                'is_bool' => 1,
                'values' => [
                    [
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => 'Enabled',
                    ],
                    [
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => 'Disabled',
                    ],
                ],
            ];
            $form['form']['input'][] = [
                'name' => 'BANKART_PAYMENT_GATEWAY_' . $prefix . '_TITLE',
                'label' => $this->l('Title'),
                'tab' => $creditCard,
                'class' => 'fixed-width-xxl',
                'type' => 'text',
            ];
            //NEW    
            if ($creditCard == 'flik') {
                $form['form']['input'][] = [
                    'name' => 'BANKART_PAYMENT_GATEWAY_' . $prefix . '_TRANSACTION_TYPE',
                    'label' => $this->l('Transaction type'),
                    'desc' => $this->l('Flik only supports debit transactions.'),
                    'tab' => $creditCard,
                    'type' => 'select',
                    'options' => [
                        'query' => [
                            [
                                'id' => 'DEBIT',
                                'name' => $this->l('Debit'),
                            ],
                        ],
                        'id' => 'id',
                        'name'=> 'name',
                    ],
                ];
            }
            //END NEW
            else {
                $form['form']['input'][] = [
                    'name' => 'BANKART_PAYMENT_GATEWAY_' . $prefix . '_TRANSACTION_TYPE',
                    'label' => $this->l('Transaction type'),
                    'desc' => $this->l('Please select the type defined in the contract signed with your bank.'),
                    'tab' => $creditCard,
                    'type' => 'select',
                    'options' => [
                        'query' => [
                            [
                                'id' => 'DEBIT',
                                'name' => $this->l('Debit'),
                            ],
                            [
                                'id' => 'PREAUTHORIZE',
                                'name' => $this->l('Preauthorization'),
                            ],
                        ],
                        'id' => 'id',
                        'name'=> 'name',
                    ],
                ];
            }
            $form['form']['input'][] = [
                'name' => 'BANKART_PAYMENT_GATEWAY_' . $prefix . '_ACCOUNT_USER',
                'label' => $this->l('User'),
                'tab' => $creditCard,
                'class' => 'fixed-width-xxl',
                'type' => 'text',
            ];
            $form['form']['input'][] = [
                'name' => 'BANKART_PAYMENT_GATEWAY_' . $prefix . '_ACCOUNT_PASSWORD',
                'label' => $this->l('Password'),
                'tab' => $creditCard,
                'class' => 'fixed-width-xxl',
                'type' => 'text',
            ];
            $form['form']['input'][] = [
                'name' => 'BANKART_PAYMENT_GATEWAY_' . $prefix . '_API_KEY',
                'label' => $this->l('API Key'),
                'tab' => $creditCard,
                'class' => 'fixed-width-xxl',
                'type' => 'text',
            ];
            $form['form']['input'][] = [
                'name' => 'BANKART_PAYMENT_GATEWAY_' . $prefix . '_SHARED_SECRET',
                'label' => $this->l('Shared Secret'),
                'tab' => $creditCard,
                'class' => 'fixed-width-xxl',
                'type' => 'text',
            ];
            ##NEW
            if ($creditCard !== 'flik'){
                $form['form']['input'][] = [
                    'name' => 'BANKART_PAYMENT_GATEWAY_' . $prefix . '_INTEGRATION_KEY',
                    'label' => $this->l('Integration Key'),
                    'tab' => $creditCard,
                    'class' => 'fixed-width-xxl',
                    'type' => 'text',
                ];
            
            
            //NEW Instalments
            $form['form']['input'][] = [
                'name' => 'BANKART_PAYMENT_GATEWAY_' . $prefix . '_INSTALLMENTS',
                'label' => $this->l('Instalments'),
                'desc' => $this->l('Select the number of installments.'),
                'tab' => $creditCard,
                'type' => 'select',
                'options' => [
                    'query' => $this->toOptionArray(), 
                    'id' => 'value',  // Change 'id' to match the 'value' key in toOptionArray
                    'name' => 'label',  // Change 'name' to match the 'label' key in toOptionArray
                ],
            ];

            $form['form']['input'][] = [
              'name' => 'BANKART_PAYMENT_GATEWAY_' . $prefix . '_INSTALMENT_AMOUNT',
                'label' => $this->l('Minimal instalment amount'),
                'desc' => $this->l('Input a value greater than 0.'),
                'tab' => $creditCard,
                'type' => 'text',
                'class' => 'fixed-width-md',
                'value' => '50',
  /*
                'id' => 'BANKART_PAYMENT_GATEWAY_' . $prefix . '_INSTALMENT_AMOUNT',
                'label' => $this->l('Minimal instalment amount'),
                'desc' => $this->l('Input a value greater than 0.'),
                'tab' => $creditCard,
                'type' => 'html',
                
                'class' => 'fixed-width-md',
                'html_content' => '<input type="number" min="1" value ="50" step = "0.1" name= "BANKART_PAYMENT_GATEWAY_' . $prefix . '_INSTALMENT_AMOUNT" id= "BANKART_PAYMENT_GATEWAY_' . $prefix . '_INSTALMENT_AMOUNT">',

*/
            ];
            }
            if ($creditCard == 'flik') {
            $form['form']['input'][] = [
                'name' => 'BANKART_PAYMENT_GATEWAY_' . $prefix . '_SEAMLESS',
                'label' => $this->l('Seamless Integration'),
                'tab' => $creditCard,
                'type' => 'hidden',
                'is_bool' => 1,
                'values' => [
                    [
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => 'Disabled',
                    ],
                ],
            ];         
            } else {
            $form['form']['input'][] = [
                'name' => 'BANKART_PAYMENT_GATEWAY_' . $prefix . '_SEAMLESS',
                'label' => $this->l('Seamless Integration'),
                'tab' => $creditCard,
                'type' => 'switch',
                'is_bool' => 1,
                'values' => [
                    [
                        'id' => 'active_of',
                        'value' => 1,
                        'label' => 'Enabled',
                    ],
                    [
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => 'Disabled',
                    ],
                ],
            ];
            //END 
            $form['form']['input'][] = [
                'name' => 'BANKART_PAYMENT_GATEWAY_' . $prefix . '_FORCE_CHALLENGE',
                'label' => $this->l('Merchant prefers Challenge'),
                'tab' => $creditCard,
                'type' => 'switch',
                'is_bool' => 1,
                'values' => [
                    [
                        'id' => 'active_of',
                        'value' => 1,
                        'label' => 'Enabled',
                    ],
                    [
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => 'Disabled',
                    ],
                ],
            ];
            }
            //            $form['form']['input'][] = [
            //                'name' => 'line',
            //                'type' => 'html',
            //                'tab' => 'CreditCard',
            //                'html_content' => '<hr>',
            //            ];
        }

        return $form;
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $values = [
            'BANKART_PAYMENT_GATEWAY_ENABLED' => Configuration::get('BANKART_PAYMENT_GATEWAY_ENABLED', null),
            'BANKART_PAYMENT_GATEWAY_HOST' => Configuration::get('BANKART_PAYMENT_GATEWAY_HOST', null),
            'BANKART_PAYMENT_GATEWAY_CALLBACK_VALIDATION' => Configuration::get('BANKART_PAYMENT_GATEWAY_CALLBACK_VALIDATION', null),
        ];

        foreach ($this->getCreditCards() as $creditCard => $cardTitle) {
            $prefix = strtoupper($creditCard);
            $values['BANKART_PAYMENT_GATEWAY_' . $prefix . '_ENABLED'] = Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_ENABLED', null);
            $values['BANKART_PAYMENT_GATEWAY_' . $prefix . '_TITLE'] = Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_TITLE') ?: $cardTitle;
            $values['BANKART_PAYMENT_GATEWAY_' . $prefix . '_TRANSACTION_TYPE'] = Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_TRANSACTION_TYPE', null);
            $values['BANKART_PAYMENT_GATEWAY_' . $prefix . '_ACCOUNT_USER'] = Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_ACCOUNT_USER', null);
            $values['BANKART_PAYMENT_GATEWAY_' . $prefix . '_ACCOUNT_PASSWORD'] = Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_ACCOUNT_PASSWORD', null);
            $values['BANKART_PAYMENT_GATEWAY_' . $prefix . '_API_KEY'] = Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_API_KEY', null);
            $values['BANKART_PAYMENT_GATEWAY_' . $prefix . '_SHARED_SECRET'] = Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_SHARED_SECRET', null);
            $values['BANKART_PAYMENT_GATEWAY_' . $prefix . '_INTEGRATION_KEY'] = Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_INTEGRATION_KEY', null);
            $values['BANKART_PAYMENT_GATEWAY_' . $prefix . '_SEAMLESS'] = Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_SEAMLESS', null);
            ##
            $values['BANKART_PAYMENT_GATEWAY_' . $prefix . '_INSTALLMENTS'] = Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_INSTALLMENTS', null);
            $values['BANKART_PAYMENT_GATEWAY_' . $prefix . '_INSTALMENT_AMOUNT'] = Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_INSTALMENT_AMOUNT', null);
            ##
            $values['BANKART_PAYMENT_GATEWAY_' . $prefix . '_FORCE_CHALLENGE'] = Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_FORCE_CHALLENGE', null);
        }

        return $values;
    }

    ##NEW
    public function max_instalments($cartId, $creditCard) {
        
        $prefix = strtoupper($creditCard);
        $objCart = new Cart($cartId); //
        $total = $objCart->getOrderTotal(true, Cart::BOTH);
        $amount = \number_format($total, 2, '.', ''); 
            
        $instalments = (int)Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_INSTALLMENTS', null);
        if ($instalments != '1' || $instalments != 1) {
            
            $instalmentsAmount = (int)Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_INSTALMENT_AMOUNT', null);
            if ($instalmentsAmount == 0 || $instalmentsAmount == '') {
                $calculatedInstalments = $instalments;
            } else {
                $calculatedInstalments = floor((float)$amount/$instalmentsAmount);
            }
            
            if ($instalments > 1) {
                if ($calculatedInstalments >= $instalments) {
                    return $instalments;
                } else {
                    return $calculatedInstalments;
                }
            }
    
        } else {
            $instalments = 1;
        }
    }
    
        ##END
    
    /**
     * Payment options hook
     *
     * @param $params
     * @throws Exception
     * @return bool|void
     */
    public function hookPaymentOptions($params)
    {
        if (!$this->active || !Configuration::get('BANKART_PAYMENT_GATEWAY_ENABLED', null)) {
            return;
        }

        $result = [];
        $years = [];
        $years[] = date('Y');
        for ($i = 1; $i <= 10; $i++) {
            $years[] = $years[0] + $i;
        }
        $this->context->smarty->assign([
            'months' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
            'years' => $years,
        ]);

        foreach ($this->getCreditCards() as $creditCard => $cardTitle) {

            $prefix = strtoupper($creditCard);
            if (!Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_ENABLED', null)) {
                continue;
            }

            $payment = new PaymentOption();
            $payment
                ->setModuleName($this->name)
                ->setCallToActionText($this->l(Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_TITLE', null)))
                ->setAction($this->context->link->getModuleLink($this->name, 'payment', [
                        'type' => $creditCard,
                    ], true));

            if (Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_SEAMLESS', null)) {
                //NEW WHERE ##
                $cartId = $this->context->cart->id;
                $maxInstalments = $this->max_instalments($cartId, $creditCard);
                ##END
                $this->context->smarty->assign([
                    'paymentType' => $creditCard,
                    'id' => 'p' . bin2hex(random_bytes(10)),
                    'action' => $payment->getAction(),
                    'integrationKey' => Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_INTEGRATION_KEY', null),
                ]);
                ##
                $this->context->smarty->assign('instalments', (int)$maxInstalments);
                ##END
                $payment->setInputs([['type' => 'input', 'name' => 'test', 'value' => 'value']]);

                $payment->setForm($this->fetch('module:bankartpaymentgateway' . DIRECTORY_SEPARATOR . 'views' .
                    DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'front' . DIRECTORY_SEPARATOR . 'seamless.tpl'));
            }
            else if ((int)Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_INSTALLMENTS', null) > 1) { 
                //error_log('#$$#$#$#$#$#$ JE PRAVA IDEJA ....1');
                //error_log('#$$#$#$#$#$#$ JE PRAVA IDEJA ....1 SEAMLESS: '. json_encode(Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_SEAMLESS', null)));
                //error_log('#$$#$#$#$#$#$ JE PRAVA IDEJA ....1 INTEGRATION_KEY: '. json_encode(Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_INTEGRATION_KEY', null)));

                if (Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_SEAMLESS', null) == '0' || Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_INTEGRATION_KEY', null) == '') {
                    //NEW WHERE ##
                    //error_log('#$$#$#$#$#$#$ JE PRAVA IDEJA ....2');
                    $cartId = $this->context->cart->id;
                    $maxInstalments = $this->max_instalments($cartId, $creditCard);
                    ##END
                    $this->context->smarty->assign([
                        'paymentType' => $creditCard,
                        'id' => 'p' . bin2hex(random_bytes(10)),
                        'action' => $payment->getAction(),
                        //'integrationKey' => Configuration::get('BANKART_PAYMENT_GATEWAY_' . $prefix . '_INTEGRATION_KEY', null),
                    ]);
                    ##
                    $this->context->smarty->assign('instalments', (int)$maxInstalments);
                    $this->context->smarty->assign('integrationKey', '');
                    ##END
                    $payment->setInputs([['type' => 'input', 'name' => 'test', 'value' => 'value']]);

                    $payment->setForm($this->fetch('module:bankartpaymentgateway' . DIRECTORY_SEPARATOR . 'views' .
                        DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'front' . DIRECTORY_SEPARATOR . 'installments.tpl'));
                }
            }
            /*$payment->setLogo(
                Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/creditcard/' . $creditCard . '.png')
            );*/

            $result[] = $payment;
        }

        return count($result) ? $result : false;
    }

    public function hookDisplayPaymentReturn($params)
    {
        if (!$this->active || !Configuration::get('BANKART_PAYMENT_GATEWAY_ENABLED', null)) {
            return;
        }

        return $this->display(__FILE__, 'views/templates/hook/payment_return.tpl');
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        if ($this->context->controller instanceof OrderControllerCore && $this->context->controller->page_name == 'checkout') {
            $uri = '/modules/bankartpaymentgateway/views/js/front.js';
            $this->context->controller->registerJavascript(sha1($uri), $uri, ['position' => 'bottom']);
        }
    }

    public function hookDisplayAfterBodyOpeningTag()
    {
        if ($this->context->controller instanceof OrderControllerCore && $this->context->controller->page_name == 'checkout') {
            $host = Configuration::get('BANKART_PAYMENT_GATEWAY_HOST', null);
            return '<script data-main="payment-js" src="' . $host . 'js/integrated/payment.min.js"></script><script>window.bankartPaymentGatewayPayment = {};</script>';
        }

        return null;
    }


    private function createOrderState($stateName)
    {
        if (!\Configuration::get($stateName)) {
            $orderState = new \OrderState();
            $orderState->name = [];

            switch ($stateName) {
                case self::BANKART_PAYMENT_GATEWAY_OS_STARTING:
                    $names = [
                        'de' => 'Bankart Payment Gateway Bezahlung gestartet',
                        'en' => 'Bankart Payment Gateway payment started',
                    ];
                    break;
                case self::BANKART_PAYMENT_GATEWAY_OS_AWAITING:
                default:
                    $names = [
                        'de' => 'Bankart Payment Gateway Bezahlung ausständig',
                        'en' => 'Bankart Payment Gateway payment awaiting',
                    ];
                    break;
            }

            foreach (\Language::getLanguages() as $language) {
                if (\Tools::strtolower($language['iso_code']) == 'de') {
                    $orderState->name[$language['id_lang']] = $names['de'];
                } else {
                    $orderState->name[$language['id_lang']] = $names['en'];
                }
            }
            $orderState->invoice = false;
            $orderState->send_email = false;
            $orderState->module_name = $this->name;
            $orderState->color = '#076dc4';
            $orderState->hidden = false;
            $orderState->logable = false;
            $orderState->delivery = false;
            $orderState->add();

            \Configuration::updateValue(
                $stateName,
                (int)($orderState->id)
            );
        }
    }
}
