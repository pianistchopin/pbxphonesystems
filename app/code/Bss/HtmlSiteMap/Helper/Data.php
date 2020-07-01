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
namespace Bss\HtmlSiteMap\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public $scopeStore = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

    /**
     * @return mixed|string
     */
    public function getAdditionUrl()
    {
        $additionUrl = $this->scopeConfig->getValue('bss_htmlsitemap/addition/addition_link', $this->scopeStore);

        $additionUrl = ($additionUrl == '') ? '' : $additionUrl;
        return $additionUrl;
    }

    /**
     * @return mixed|string
     */
    public function getCmsLink()
    {
        $cmsLink = $this->scopeConfig->getValue('bss_htmlsitemap/cms/do_something', $this->scopeStore);

        $cmsLink = ($cmsLink == '') ? '' : $cmsLink;
        return $cmsLink;
    }

    /**
     * @return mixed|string
     */
    public function getEnable()
    {
        $getEnable = $this->scopeConfig->getValue('bss_htmlsitemap/general/enable', $this->scopeStore);

        $getEnable = ($getEnable == '') ? '' : $getEnable;
        return $getEnable;
    }

    /**
     * @return mixed
     */
    public function getTitleSiteMap()
    {
        return $this->scopeConfig->getValue(
            "bss_htmlsitemap/general/title",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getDescriptionSitemap()
    {
        return $this->scopeConfig->getValue(
            "bss_htmlsitemap/for_search/description",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getKeywordsSitemap()
    {
        return $this->scopeConfig->getValue(
            "bss_htmlsitemap/for_search/keywords",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getMetaTitleSitemap()
    {
        return $this->scopeConfig->getValue(
            "bss_htmlsitemap/for_search/meta_title",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed|string
     */
    public function getCategoryDisable()
    {
        $categoryDisable = $this->scopeConfig->getValue('bss_htmlsitemap/category/id_category', $this->scopeStore);

        $categoryDisable = ($categoryDisable == '') ? '' : $categoryDisable;
        return $categoryDisable;
    }

    /**
     * @return mixed|string
     */
    public function enableCategory()
    {
        $enableCategory = $this->scopeConfig->getValue('bss_htmlsitemap/category/enable_category', $this->scopeStore);

        $enableCategory = ($enableCategory == '') ? '' : $enableCategory;
        return $enableCategory;
    }

    /**
     * @return mixed|string
     */
    public function enableProduct()
    {
        $enableProduct = $this->scopeConfig->getValue('bss_htmlsitemap/product/enable_product', $this->scopeStore);

        $enableProduct = ($enableProduct == '') ? '' : $enableProduct;
        return $enableProduct;
    }

    /**
     * @return mixed|string
     */
    public function enableCms()
    {
        $enableCms = $this->scopeConfig->getValue('bss_htmlsitemap/cms/enable_cms', $this->scopeStore);

        $enableCms = ($enableCms == '') ? '' : $enableCms;
        return $enableCms;
    }

    /**
     * @return mixed|string
     */
    public function enableStoreView()
    {
        $enableStoreView = $this->scopeConfig->getValue('bss_htmlsitemap/store/enable_store', $this->scopeStore);

        $enableStoreView = ($enableStoreView == '') ? '' : $enableStoreView;
        return $enableStoreView;
    }

    /**
     * @return mixed|string
     */
    public function orderTemplates()
    {
        $orderTemplates = $this->scopeConfig->getValue('bss_htmlsitemap/general/order_templates', $this->scopeStore);

        $orderTemplates = ($orderTemplates == '') ? '' : $orderTemplates;
        return $orderTemplates;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        $baseUrl = $this->_urlBuilder->getUrl();
        return $baseUrl;
    }

    /**
     * @return mixed|string
     */
    public function titleCategory()
    {
        $titleCategory = $this->scopeConfig->getValue('bss_htmlsitemap/category/title_category', $this->scopeStore);

        $titleCategory = ($titleCategory == '') ? '' : $titleCategory;
        return $titleCategory;
    }

    /**
     * @return mixed|string
     */
    public function titleCms()
    {
        $titleCms = $this->scopeConfig->getValue('bss_htmlsitemap/cms/title_cms', $this->scopeStore);

        $titleCms = ($titleCms == '') ? '' : $titleCms;
        return $titleCms;
    }

    /**
     * @return mixed|string
     */
    public function titleProduct()
    {
        $titleProduct = $this->scopeConfig->getValue('bss_htmlsitemap/product/title_product', $this->scopeStore);

        $titleProduct = ($titleProduct == '') ? '' : $titleProduct;
        return $titleProduct;
    }

    /**
     * @return mixed|string
     */
    public function titleStore()
    {
        $titleStore = $this->scopeConfig->getValue('bss_htmlsitemap/store/title_store', $this->scopeStore);

        $titleStore = ($titleStore == '') ? '' : $titleStore;
        return $titleStore;
    }

    /**
     * @return mixed|string
     */
    public function titleAddition()
    {
        $titleAddition = $this->scopeConfig->getValue('bss_htmlsitemap/addition/title_addition', $this->scopeStore);

        $titleAddition = ($titleAddition == '') ? '' : $titleAddition;
        return $titleAddition;
    }

    /**
     * @return mixed|string
     */
    public function openNewTab()
    {
        $openNewTab = $this->scopeConfig->getValue('bss_htmlsitemap/addition/open_new_tab', $this->scopeStore);

        $openNewTab = ($openNewTab == '') ? '' : $openNewTab;
        return $openNewTab;
    }
}
