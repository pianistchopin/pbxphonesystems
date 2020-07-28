<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Plugin\Search\Block\Adminhtml\Term\Edit;

use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;

class FormPlugin
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Json
     */
    private $jsonSerializer;

    public function __construct(Registry $registry, Json $jsonSerializer)
    {
        $this->registry = $registry;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @param $subject
     * @param $form
     * @return array
     */
    public function beforeSetForm($subject, $form)
    {
        $term = $this->registry->registry('current_catalog_search');
        $form->addField(
            'related_terms',
            'hidden',
            [
                'name' => 'related_terms',
                'value' => $this->jsonSerializer->serialize($term->getRelatedTerms())
            ]
        );

        return [$form];
    }
}