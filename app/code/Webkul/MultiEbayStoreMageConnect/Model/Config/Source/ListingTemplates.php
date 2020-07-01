<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2018 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model\Config\Source;

class ListingTemplates
{
        /**
         * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
         */
    public function __construct(
        \Webkul\MultiEbayStoreMageConnect\Model\ListingTemplateFactory $listingTemplate
    ) {
        $this->listingTemplate = $listingTemplate;
    }
    /**
     * Options getter.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $listingTemplates = ['' => 'None'];
        $templates = $this->listingTemplate->create()->getCollection();
        foreach ($templates as $temp) {
            $listingTemplates[$temp->getEntityId()] = $temp->getTemplateTitle();
        }
        return $listingTemplates;
    }
}
