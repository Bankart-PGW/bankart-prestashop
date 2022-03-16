<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit775825a16e04c3172d1c782ae1d43bce161ab814310b3f198ceaa8c39f4aa87b
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'BankartPaymentGateway\\Prestashop\\PaymentMethod\\' => 45,
            'BankartPaymentGateway\\Prestashop\\' => 31,
            'BankartPaymentGateway\\Client\\' => 27,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'BankartPaymentGateway\\Prestashop\\PaymentMethod\\' => 
        array (
            0 => __DIR__ . '/../..' . '/payment_method',
        ),
        'BankartPaymentGateway\\Prestashop\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
        'BankartPaymentGateway\\Client\\' => 
        array (
            0 => __DIR__ . '/../..' . '/client',
        ),
    );

    public static $classMap = array (
        'BankartPaymentGateway\\Client\\Callback\\ChargebackData' => __DIR__ . '/../..' . '/client/Callback/ChargebackData.php',
        'BankartPaymentGateway\\Client\\Callback\\ChargebackReversalData' => __DIR__ . '/../..' . '/client/Callback/ChargebackReversalData.php',
        'BankartPaymentGateway\\Client\\Callback\\Result' => __DIR__ . '/../..' . '/client/Callback/Result.php',
        'BankartPaymentGateway\\Client\\Client' => __DIR__ . '/../..' . '/client/Client.php',
        'BankartPaymentGateway\\Client\\CustomerProfile\\CustomerData' => __DIR__ . '/../..' . '/client/CustomerProfile/CustomerData.php',
        'BankartPaymentGateway\\Client\\CustomerProfile\\DeleteProfileResponse' => __DIR__ . '/../..' . '/client/CustomerProfile/DeleteProfileResponse.php',
        'BankartPaymentGateway\\Client\\CustomerProfile\\GetProfileResponse' => __DIR__ . '/../..' . '/client/CustomerProfile/GetProfileResponse.php',
        'BankartPaymentGateway\\Client\\CustomerProfile\\PaymentData\\CardData' => __DIR__ . '/../..' . '/client/CustomerProfile/PaymentData/CardData.php',
        'BankartPaymentGateway\\Client\\CustomerProfile\\PaymentData\\IbanData' => __DIR__ . '/../..' . '/client/CustomerProfile/PaymentData/IbanData.php',
        'BankartPaymentGateway\\Client\\CustomerProfile\\PaymentData\\PaymentData' => __DIR__ . '/../..' . '/client/CustomerProfile/PaymentData/PaymentData.php',
        'BankartPaymentGateway\\Client\\CustomerProfile\\PaymentData\\WalletData' => __DIR__ . '/../..' . '/client/CustomerProfile/PaymentData/WalletData.php',
        'BankartPaymentGateway\\Client\\CustomerProfile\\PaymentInstrument' => __DIR__ . '/../..' . '/client/CustomerProfile/PaymentInstrument.php',
        'BankartPaymentGateway\\Client\\CustomerProfile\\UpdateProfileResponse' => __DIR__ . '/../..' . '/client/CustomerProfile/UpdateProfileResponse.php',
        'BankartPaymentGateway\\Client\\Data\\CreditCardCustomer' => __DIR__ . '/../..' . '/client/Data/CreditCardCustomer.php',
        'BankartPaymentGateway\\Client\\Data\\Customer' => __DIR__ . '/../..' . '/client/Data/Customer.php',
        'BankartPaymentGateway\\Client\\Data\\Data' => __DIR__ . '/../..' . '/client/Data/Data.php',
        'BankartPaymentGateway\\Client\\Data\\IbanCustomer' => __DIR__ . '/../..' . '/client/Data/IbanCustomer.php',
        'BankartPaymentGateway\\Client\\Data\\Item' => __DIR__ . '/../..' . '/client/Data/Item.php',
        'BankartPaymentGateway\\Client\\Data\\Request' => __DIR__ . '/../..' . '/client/Data/Request.php',
        'BankartPaymentGateway\\Client\\Data\\Result\\CreditcardData' => __DIR__ . '/../..' . '/client/Data/Result/CreditcardData.php',
        'BankartPaymentGateway\\Client\\Data\\Result\\IbanData' => __DIR__ . '/../..' . '/client/Data/Result/IbanData.php',
        'BankartPaymentGateway\\Client\\Data\\Result\\PhoneData' => __DIR__ . '/../..' . '/client/Data/Result/PhoneData.php',
        'BankartPaymentGateway\\Client\\Data\\Result\\ResultData' => __DIR__ . '/../..' . '/client/Data/Result/ResultData.php',
        'BankartPaymentGateway\\Client\\Data\\Result\\WalletData' => __DIR__ . '/../..' . '/client/Data/Result/WalletData.php',
        'BankartPaymentGateway\\Client\\Exception\\ClientException' => __DIR__ . '/../..' . '/client/Exception/ClientException.php',
        'BankartPaymentGateway\\Client\\Exception\\InvalidValueException' => __DIR__ . '/../..' . '/client/Exception/InvalidValueException.php',
        'BankartPaymentGateway\\Client\\Exception\\RateLimitException' => __DIR__ . '/../..' . '/client/Exception/RateLimitException.php',
        'BankartPaymentGateway\\Client\\Exception\\TimeoutException' => __DIR__ . '/../..' . '/client/Exception/TimeoutException.php',
        'BankartPaymentGateway\\Client\\Exception\\TypeException' => __DIR__ . '/../..' . '/client/Exception/TypeException.php',
        'BankartPaymentGateway\\Client\\Http\\ClientInterface' => __DIR__ . '/../..' . '/client/Http/ClientInterface.php',
        'BankartPaymentGateway\\Client\\Http\\CurlClient' => __DIR__ . '/../..' . '/client/Http/CurlClient.php',
        'BankartPaymentGateway\\Client\\Http\\CurlExec' => __DIR__ . '/../..' . '/client/Http/CurlExec.php',
        'BankartPaymentGateway\\Client\\Http\\Exception\\ClientException' => __DIR__ . '/../..' . '/client/Http/Exception/ClientException.php',
        'BankartPaymentGateway\\Client\\Http\\Exception\\ResponseException' => __DIR__ . '/../..' . '/client/Http/Exception/ResponseException.php',
        'BankartPaymentGateway\\Client\\Http\\Response' => __DIR__ . '/../..' . '/client/Http/Response.php',
        'BankartPaymentGateway\\Client\\Http\\ResponseInterface' => __DIR__ . '/../..' . '/client/Http/ResponseInterface.php',
        'BankartPaymentGateway\\Client\\Json\\DataObject' => __DIR__ . '/../..' . '/client/Json/DataObject.php',
        'BankartPaymentGateway\\Client\\Json\\ErrorResponse' => __DIR__ . '/../..' . '/client/Json/ErrorResponse.php',
        'BankartPaymentGateway\\Client\\Json\\ResponseObject' => __DIR__ . '/../..' . '/client/Json/ResponseObject.php',
        'BankartPaymentGateway\\Client\\Schedule\\ScheduleData' => __DIR__ . '/../..' . '/client/Schedule/ScheduleData.php',
        'BankartPaymentGateway\\Client\\Schedule\\ScheduleError' => __DIR__ . '/../..' . '/client/Schedule/ScheduleError.php',
        'BankartPaymentGateway\\Client\\Schedule\\ScheduleResult' => __DIR__ . '/../..' . '/client/Schedule/ScheduleResult.php',
        'BankartPaymentGateway\\Client\\StatusApi\\StatusRequestData' => __DIR__ . '/../..' . '/client/StatusApi/StatusRequestData.php',
        'BankartPaymentGateway\\Client\\StatusApi\\StatusResult' => __DIR__ . '/../..' . '/client/StatusApi/StatusResult.php',
        'BankartPaymentGateway\\Client\\Transaction\\Base\\AbstractTransaction' => __DIR__ . '/../..' . '/client/Transaction/Base/AbstractTransaction.php',
        'BankartPaymentGateway\\Client\\Transaction\\Base\\AbstractTransactionWithReference' => __DIR__ . '/../..' . '/client/Transaction/Base/AbstractTransactionWithReference.php',
        'BankartPaymentGateway\\Client\\Transaction\\Base\\AddToCustomerProfileInterface' => __DIR__ . '/../..' . '/client/Transaction/Base/AddToCustomerProfileInterface.php',
        'BankartPaymentGateway\\Client\\Transaction\\Base\\AddToCustomerProfileTrait' => __DIR__ . '/../..' . '/client/Transaction/Base/AddToCustomerProfileTrait.php',
        'BankartPaymentGateway\\Client\\Transaction\\Base\\AmountableInterface' => __DIR__ . '/../..' . '/client/Transaction/Base/AmountableInterface.php',
        'BankartPaymentGateway\\Client\\Transaction\\Base\\AmountableTrait' => __DIR__ . '/../..' . '/client/Transaction/Base/AmountableTrait.php',
        'BankartPaymentGateway\\Client\\Transaction\\Base\\ItemsInterface' => __DIR__ . '/../..' . '/client/Transaction/Base/ItemsInterface.php',
        'BankartPaymentGateway\\Client\\Transaction\\Base\\ItemsTrait' => __DIR__ . '/../..' . '/client/Transaction/Base/ItemsTrait.php',
        'BankartPaymentGateway\\Client\\Transaction\\Base\\OffsiteInterface' => __DIR__ . '/../..' . '/client/Transaction/Base/OffsiteInterface.php',
        'BankartPaymentGateway\\Client\\Transaction\\Base\\OffsiteTrait' => __DIR__ . '/../..' . '/client/Transaction/Base/OffsiteTrait.php',
        'BankartPaymentGateway\\Client\\Transaction\\Base\\ScheduleInterface' => __DIR__ . '/../..' . '/client/Transaction/Base/ScheduleInterface.php',
        'BankartPaymentGateway\\Client\\Transaction\\Base\\ScheduleTrait' => __DIR__ . '/../..' . '/client/Transaction/Base/ScheduleTrait.php',
        'BankartPaymentGateway\\Client\\Transaction\\Capture' => __DIR__ . '/../..' . '/client/Transaction/Capture.php',
        'BankartPaymentGateway\\Client\\Transaction\\Debit' => __DIR__ . '/../..' . '/client/Transaction/Debit.php',
        'BankartPaymentGateway\\Client\\Transaction\\Deregister' => __DIR__ . '/../..' . '/client/Transaction/Deregister.php',
        'BankartPaymentGateway\\Client\\Transaction\\Error' => __DIR__ . '/../..' . '/client/Transaction/Error.php',
        'BankartPaymentGateway\\Client\\Transaction\\Payout' => __DIR__ . '/../..' . '/client/Transaction/Payout.php',
        'BankartPaymentGateway\\Client\\Transaction\\Preauthorize' => __DIR__ . '/../..' . '/client/Transaction/Preauthorize.php',
        'BankartPaymentGateway\\Client\\Transaction\\Refund' => __DIR__ . '/../..' . '/client/Transaction/Refund.php',
        'BankartPaymentGateway\\Client\\Transaction\\Register' => __DIR__ . '/../..' . '/client/Transaction/Register.php',
        'BankartPaymentGateway\\Client\\Transaction\\Result' => __DIR__ . '/../..' . '/client/Transaction/Result.php',
        'BankartPaymentGateway\\Client\\Transaction\\VoidTransaction' => __DIR__ . '/../..' . '/client/Transaction/VoidTransaction.php',
        'BankartPaymentGateway\\Client\\Xml\\Generator' => __DIR__ . '/../..' . '/client/Xml/Generator.php',
        'BankartPaymentGateway\\Client\\Xml\\Parser' => __DIR__ . '/../..' . '/client/Xml/Parser.php',
        'BankartPaymentGateway\\Prestashop\\PaymentMethod\\CreditCard' => __DIR__ . '/../..' . '/payment_method/CreditCard.php',
        'BankartPaymentGateway\\Prestashop\\PaymentMethod\\PaymentMethodInterface' => __DIR__ . '/../..' . '/payment_method/PaymentMethodInterface.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit775825a16e04c3172d1c782ae1d43bce161ab814310b3f198ceaa8c39f4aa87b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit775825a16e04c3172d1c782ae1d43bce161ab814310b3f198ceaa8c39f4aa87b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit775825a16e04c3172d1c782ae1d43bce161ab814310b3f198ceaa8c39f4aa87b::$classMap;

        }, null, ClassLoader::class);
    }
}
