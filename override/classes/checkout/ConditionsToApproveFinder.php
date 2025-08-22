<?php
class ConditionsToApproveFinder extends ConditionsToApproveFinderCore
{
    protected function getDefaultTermsAndConditions()
    {
        $cms = new CMS(Configuration::get('PS_CONDITIONS_CMS_ID'), $this->context->language->id);
        $link = $this->context->link->getCMSLink($cms, $cms->link_rewrite, (bool) Configuration::get('PS_SSL_ENABLED'));

        $termsAndConditions = new TermsAndConditions();
        $termsAndConditions
            ->setText(
                $this->translator->trans('I agree to the [terms of service].', [], 'Shop.Theme.Checkout'),
                $link
            )
            ->setIdentifier('terms-and-conditions');

        return $termsAndConditions;
    }
}
