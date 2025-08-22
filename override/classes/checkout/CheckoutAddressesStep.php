<?php
use Symfony\Component\Translation\TranslatorInterface;
class CheckoutAddressesStep extends CheckoutAddressesStepCore
{
    /*
    * module: quantitydiscountpro
    * date: 2024-09-03 17:48:41
    * version: 2.1.36
    */
    public function handleRequest(array $requestParams = array())
    {
        parent::handleRequest($requestParams);
        if (Module::isEnabled('quantitydiscountpro')) {
            include_once(_PS_MODULE_DIR_.'quantitydiscountpro/quantitydiscountpro.php');
            $quantityDiscount = new QuantityDiscountRule();
            $quantityDiscount->createAndRemoveRules();
        }
        return $this;
    }
}
