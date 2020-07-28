<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Pgrid
 */


namespace Amasty\Pgrid\Model\Config;

class Date extends \Magento\Framework\Data\Form\Element\Date
{
    /**
     * Set Date Format to use parent Date element on config side in admin
     *
     * @return string
     */
    public function getElementHtml()
    {
        $this->setDateFormat($this->localeDate->getDateFormat());
        return parent::getElementHtml();
    }
}
