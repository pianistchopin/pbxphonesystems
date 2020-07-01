<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    BSS_HtmlSiteMap
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\HtmlSiteMap\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddSiteMap implements ObserverInterface
{
    /**
     * @var \Bss\HtmlSiteMap\Block\ItemsCollection
     */
    private $siteMapBlock;

    /**
     * @var \Bss\GeoIPAutoSwitchStore\Helper\Data
     */
    private $dataHelper;

    /**
     * AddSiteMap constructor.
     * @param \Bss\HtmlSiteMap\Helper\Data $dataHelper
     * @param \Bss\HtmlSiteMap\Block\ItemsCollection $siteMapBlock
     */
    public function __construct(
        \Bss\HtmlSiteMap\Helper\Data $dataHelper,
        \Bss\HtmlSiteMap\Block\ItemsCollection $siteMapBlock
    ) {
        $this->siteMapBlock = $siteMapBlock;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Add New Layout handle
     *
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $layout = $observer->getData('layout');
        $helper = $this->dataHelper;
        $orderTemplates = $helper->orderTemplates();

        $block = $this->siteMapBlock;

        if ($orderTemplates == '' || $orderTemplates == null) {
            $orderTemplates = $block::PRODUCT_LIST_NUMBER.','.$block::STORE_VIEW_LIST_NUMBER.','.$block::ADDITIONAL_LIST_NUMBER.','.$block::CATE_AND_CMS_NUMBER;
        }

        $orderTemplates = ",".$orderTemplates.",";
        $orderTemplates = explode(',', $orderTemplates);

        $fullActionName = $observer->getData('full_action_name');
        
        if ($fullActionName != 'sitemap_index_index') {
            return $this;
        }

        foreach ($orderTemplates as $key) {
            if ($key == $block::PRODUCT_LIST_NUMBER) {
                $layout->getUpdate()->addHandle('sitemap_product_list');
            }

            if ($key == $block::STORE_VIEW_LIST_NUMBER) {
                $layout->getUpdate()->addHandle('sitemap_store_list');
            }

            if ($key == $block::ADDITIONAL_LIST_NUMBER) {
                $layout->getUpdate()->addHandle('sitemap_additional_list');
            }

            if ($key == $block::CATE_AND_CMS_NUMBER) {
                $layout->getUpdate()->addHandle('sitemap_category_cms_list');
            }
        }

        return $this;
    }
}
