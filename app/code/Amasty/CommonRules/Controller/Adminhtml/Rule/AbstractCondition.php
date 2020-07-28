<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Controller\Adminhtml\Rule;

/**
 * @codingStandardsIgnoreFile
 */
abstract class AbstractCondition extends \Magento\Backend\App\Action
{
    /**
     * @param string $prefix
     */
    public function newConditions($prefix)
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = $this->_objectManager->create($type)
            ->setId($id)
            ->setType($type)
            ->setRule($this->_objectManager->create(\Magento\CatalogRule\Model\Rule::class))
            ->setPrefix($prefix);

        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof \Magento\Rule\Model\Condition\AbstractCondition) {
            $model->setFormName($this->getRequest()->getParam('form'));
            $model->setJsFormObject($this->getRequest()->getParam('form_namespace'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }
}
