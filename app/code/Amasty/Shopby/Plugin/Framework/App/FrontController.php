<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin\Framework\App;

use Magento\Framework\App\FrontController as DefaultFronController;
use Magento\Framework\App\RequestInterface;
use Amasty\Shopby\Helper\Category as CategoryHelper;

/**
 * Class FrontController
 * @package Amasty\Shopby\Plugin\Framework\App
 */
class FrontController
{
    const SHOPBY_EXTRA_PARAM = 'amshopby';

    /**
     * @var bool
     */
    private $isCategorySingleSelect;

    public function __construct(CategoryHelper $categoryHelper)
    {
        /**
         * @TODO remove this in 2.11.0
         * quick fix for CAT-3388
         */
        $this->isCategorySingleSelect = !$categoryHelper->getSetting()->isMultiselect();
    }

    /**
     * @param DefaultFronController $subject
     * @param RequestInterface $request
     * @return array
     */
    public function beforeDispatch(DefaultFronController $subject, RequestInterface $request)
    {
        $this->parseAmshopbyParams($request);
        return [$request];
    }

    /**
     * @param RequestInterface $request
     * @return $this
     */
    private function parseAmshopbyParams(RequestInterface $request)
    {
        if ($amShopbyParams = $request->getParam(self::SHOPBY_EXTRA_PARAM, [])) {
            foreach ($amShopbyParams as $key => $value) {
                if ($key == CategoryHelper::CATEGORY_FILTER_PARAM
                    && $this->isCategorySingleSelect
                ) {
                    continue;
                }
                $request->setQueryValue($key, implode(",", $value));
            }
        }

        return $this;
    }
}
