<?php

namespace ProVu\UploadOrder\Plugin\Checkout;

class PONumber
{
	public function afterProcess(\Magento\Checkout\Block\Checkout\LayoutProcessor $subject, $jsLayout)
    { 
        $ponumber = 'po_number';
        $ponumberField = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                // customScope is used to group elements within a single form (e.g. they can be validated separately)
                'customScope' => 'shippingAddress.custom_attributes',
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
                'tooltip' => [
                    'description' => 'Purchase Order Number',
                ],
            ],
            'dataScope' => 'shippingAddress.custom_attributes' . '.' . $ponumber,
            'label' => 'Purchase Order Number',
            'provider' => 'checkoutProvider',
            'sortOrder' => 0,
            'validation' => [
               'required-entry' => true
            ],
            'options' => [],
            'filterBy' => null,
            'customEntry' => null,
            'visible' => true,
            'value' => '' // value field is used to set a default value of the attribute
        ];
        
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['before-form']['children'][$ponumber] = $ponumberField;
        
        return $jsLayout;    
    }
}