<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Pgrid
 */


namespace Amasty\Pgrid\Plugin\Ui\Model;

class Reader extends AbstractReader
{
    /**
     * @var \Magento\Framework\Webapi\Rest\Request
     */
    protected $request;

    public function __construct(
        \Magento\Framework\Webapi\Rest\Request $request
    ) {
        $this->request = $request;
    }

    /**
     * Added settings for product grid on magento 2.2.x
     *
     * @param \Magento\Ui\Config\Reader $subject
     * @param array                     $result
     *
     * @return array
     */
    public function afterRead(
        \Magento\Ui\Config\Reader $subject,
        $result
    ) {
        // Check namespace for configurable, group or bundle product listings
        if (isset($result['children']['product_columns'])
            && $this->request->getParam('namespace') != 'configurable_associated_product_listing'
            && $this->request->getParam('namespace') != 'bundle_product_listing'
            && $this->request->getParam('namespace') != 'grouped_product_listing') {
            $result['children'] = $this->addAmastySettings($result['children']);
        }

        return $result;
    }
}
