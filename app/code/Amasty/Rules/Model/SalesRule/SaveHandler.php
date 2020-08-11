<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */


namespace Amasty\Rules\Model\SalesRule;

use Amasty\Rules\Api\Data\RuleInterface;
use Amasty\Rules\Api\Data\RuleInterfaceFactory;
use Amasty\Rules\Model\ResourceModel\Rule;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\SalesRule\Api\Data\RuleInterface as SalesRuleInterface;

class SaveHandler implements ExtensionInterface
{
    /**
     * @var Rule
     */
    private $ruleResource;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var RuleInterfaceFactory
     */
    private $ruleFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        Rule $ruleResource,
        MetadataPool $metadataPool,
        RuleInterfaceFactory $ruleFactory,
        RequestInterface $request
    ) {
        $this->ruleResource = $ruleResource;
        $this->metadataPool = $metadataPool;
        $this->ruleFactory = $ruleFactory;
        $this->request = $request;
    }

    /**
     * Stores Special Promotions Rule value from Sales Rule extension attributes
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param \Magento\SalesRule\Model\Rule|\Magento\SalesRule\Model\Data\Rule $entity
     * @param array $arguments
     *
     * @return \Magento\SalesRule\Model\Rule|\Magento\SalesRule\Model\Data\Rule
     *
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function execute($entity, $arguments = [])
    {
        $linkField = $this->metadataPool->getMetadata(SalesRuleInterface::class)->getLinkField();
        $attributes = $entity->getExtensionAttributes() ?: [];

        if (isset($attributes[RuleInterface::EXTENSION_CODE])) {
            $ruleLinkId = $entity->getDataByKey($linkField);
            $inputData = $attributes[RuleInterface::EXTENSION_CODE];
            /** @var \Amasty\Rules\Model\Rule $amRule */
            $amRule = $this->ruleFactory->create();
            $this->ruleResource->load($amRule, $ruleLinkId, RuleInterface::KEY_SALESRULE_ID);

            if ($inputData instanceof RuleInterface) {
                $amRule->addData($inputData->getData());
            } else {
                $amRule->addData($inputData);
            }

            if ($amRule->getSalesruleId() != $ruleLinkId) {
                $amRule->setId(null);
                $amRule->setSalesruleId($ruleLinkId);
            }

            $this->validateRequiredFields($entity, $amRule);

            $this->ruleResource->save($amRule);
        }

        return $entity;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $entity
     * @param \Amasty\Rules\Model\Rule $amRule
     * @throws LocalizedException
     */
    private function validateRequiredFields($entity, $amRule)
    {
        if (stripos($entity->getSimpleAction(), 'buyxgetn') !== false) {
            if (!$amRule->getPromoSkus() && !$amRule->getPromoCats()) {
                throw new LocalizedException(__('Please specify Promo SKU or Promo Categories.'));
            }

            if (!$amRule->getNqty()) {
                throw new LocalizedException(__('Please specify Number of Y product(s).'));
            }

            $ruleParams = $this->request->getParam('rule');

            if ($ruleParams
                && (empty($ruleParams['actions'])
                    || count($ruleParams['actions']) <= 1)
            ) {
                throw new LocalizedException(
                    __("Please scroll down to the 'Actions' section and add at least one condition.")
                );
            }
        }
    }
}
