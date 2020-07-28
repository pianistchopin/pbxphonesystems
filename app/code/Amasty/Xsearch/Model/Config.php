<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Model;

use Magento\Store\Model\ScopeInterface;
use Amasty\Xsearch\Model\System\Config\Source\RelatedTerms;

class Config
{
    const MODULE_SECTION_NAME = 'amasty_xsearch/';
    const PERMANENT_REDIRECT_CODE = 301;
    const TEMPORARY_REDIRECT_CODE = 302;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    public function getModuleConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::MODULE_SECTION_NAME . $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return int
     */
    public function getRedirectType()
    {
        return $this->getModuleConfig('general/four_zero_four_redirect');
    }

    /**
     * @return bool
     */
    public function hasRedirect()
    {
        return (bool)$this->getRedirectType();
    }

    /**
     * @return bool
     */
    public function isPermanentRedirect()
    {
        return $this->getRedirectType() == self::PERMANENT_REDIRECT_CODE;
    }

    /**
     * @return int
     */
    public function getRedirectCode()
    {
        return $this->isPermanentRedirect() ? self::PERMANENT_REDIRECT_CODE : self::TEMPORARY_REDIRECT_CODE;
    }

    /**
     * @param int $searchResultCount
     * @return bool
     */
    public function canShowRelatedTerms($searchResultCount = 0)
    {
        switch ($this->getModuleConfig('general/show_related_terms')) {
            case RelatedTerms::DISABLED:
                return false;
            case RelatedTerms::SHOW_ALWAYS:
                return true;
            case RelatedTerms::SHOW_ONLY_WITHOUT_RESULTS:
                return !$searchResultCount;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canShowRelatedNumberResults()
    {
        return (bool)$this->getModuleConfig('general/show_related_terms_results');
    }

    /**
     * @return bool
     */
    public function isShowOutOfStockLast()
    {
        return (bool)$this->getModuleConfig('product/out_of_stock_last');
    }
}
