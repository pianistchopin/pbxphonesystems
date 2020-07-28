<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */


namespace Amasty\Stockstatus\Block\Adminhtml\System\Config\Form\Field;

class OutOfStock extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($this->getModuleManager() && $this->getModuleManager()->isEnabled('Amasty_Xnotif')) {
            $element->setValue( __('Yes, module is installed'));
            $element->setHtmlId('amasty_is_instaled');
            $element->setComment('');
        } else {
            $element->setValue(__('No, module is not installed'));
            $element->setHtmlId('amasty_not_instaled');
        }
        return parent::render($element);
    }
}
