<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Block\Frontend;

use Magento\Framework\View\Element\Template;

class Bar extends Template
{
    /**
     * @var \Amasty\ShippingBar\Model\BarManagement
     */
    private $barManagement;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    public function __construct(
        Template\Context $context,
        \Amasty\ShippingBar\Model\BarManagement $barManagement,
        \Magento\Customer\Model\Session\Proxy $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->barManagement = $barManagement;
        $this->customerSession = $customerSession;
    }

    public function getJsLayout()
    {
        $customerGroup = $this->customerSession->getCustomerGroupId();
        $page = $this->barManagement->getPage($this->getRequest());
        $data = [];

        $data['currencySymbol'] = $this->_storeManager->getStore()->getCurrentCurrency()->getCurrencySymbol()
            ?: $this->_storeManager->getStore()->getCurrentCurrency()->getCurrencyCode();

        $profile = $this->barManagement->getFilledData(
            $customerGroup,
            $page,
            $this->barManagement->getPosition($this->getPosition())
        );

        if ($profile) {
            $data['actionClickable'] = $profile->getActionClickable();
            $data['closeable'] = $profile->getCloseable();
            $data['isCarVisibleValue'] = $profile->getCarIconVisible();
            $data['textSize'] = $profile->getTextSize();
            $data['fontFamily'] = $profile->getTextFont();
            $data['barBackground'] = $profile->getBackgroundColor();
            $data['extraColor'] = $profile->getExtraColor();
            $data['textColor'] = $profile->getTextColor();
            $data['actionLink'] = $profile->getActionLink();
            $data['goal'] = $profile->getGoal();
            $data['customStyle'] = $profile->getCustomStyle();
            $data['position'] = $profile->getPosition();
            $data['labels'] = $profile->getLabels();
        }

        $this->jsLayout['components']['amasty-shipbar-' . $this->getPosition()] += $data;

        return parent::getJsLayout();
    }
}
